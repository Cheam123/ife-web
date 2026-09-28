<?php

namespace Tests\Unit;

use App\Services\FormSchemaService;
use PHPUnit\Framework\TestCase;

class FormSchemaServiceTest extends TestCase
{
    private FormSchemaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FormSchemaService();
    }

    private function v2Schema(): array
    {
        return [
            'schema_version' => 2,
            'groups' => [
                ['id' => 'grp_1', 'label' => 'Customer Details', 'order' => 20, 'visible_when' => null],
            ],
            'elements' => [
                ['id' => 'el_type', 'kind' => 'field', 'type' => 'select', 'label' => 'Customer Type',
                 'mandatory' => true, 'values' => ['Walk-in', 'VIP'], 'group_id' => null, 'order' => 10, 'visible_when' => null],
                ['id' => 'el_desc', 'kind' => 'description', 'text' => 'VIP only section', 'group_id' => 'grp_1', 'order' => 10,
                 'visible_when' => null],
                ['id' => 'el_name', 'kind' => 'field', 'type' => 'text', 'label' => 'Customer Name',
                 'mandatory' => true, 'values' => [], 'group_id' => 'grp_1', 'order' => 20,
                 'visible_when' => [
                     'logic' => 'or',
                     'groups' => [['logic' => 'and', 'conditions' => [
                         ['field' => 'el_type', 'operator' => 'equals', 'value' => 'VIP'],
                     ]]],
                 ]],
                ['id' => 'el_extra', 'kind' => 'field', 'type' => 'text', 'label' => 'Chained Field',
                 'mandatory' => false, 'values' => [], 'group_id' => null, 'order' => 30,
                 'visible_when' => [
                     'logic' => 'or',
                     'groups' => [['logic' => 'and', 'conditions' => [
                         ['field' => 'el_name', 'operator' => 'is_not_empty', 'value' => null],
                     ]]],
                 ]],
            ],
        ];
    }

    public function test_normalize_upgrades_legacy_v1_arrays()
    {
        $legacy = [
            ['type' => 'text', 'label' => 'Name', 'mandatory' => true, 'values' => []],
            ['type' => 'select', 'label' => 'Branch', 'mandatory' => false, 'values' => ['A', 'B']],
        ];

        $schema = $this->service->normalize($legacy);

        $this->assertSame(2, $schema['schema_version']);
        $this->assertSame([], $schema['groups']);
        $this->assertCount(2, $schema['elements']);
        $this->assertSame('el_legacy_0', $schema['elements'][0]['id']);
        $this->assertSame('el_legacy_1', $schema['elements'][1]['id']);
        $this->assertSame(0, $schema['elements'][0]['order']);
        $this->assertSame(10, $schema['elements'][1]['order']);
        $this->assertSame('field', $schema['elements'][0]['kind']);
        $this->assertNull($schema['elements'][0]['visible_when']);
    }

    public function test_normalize_passes_v2_through()
    {
        $schema = $this->service->normalize($this->v2Schema());
        $this->assertSame(2, $schema['schema_version']);
        $this->assertCount(1, $schema['groups']);
        $this->assertCount(4, $schema['elements']);
    }

    public function test_normalize_handles_empty()
    {
        $schema = $this->service->normalize(null);
        $this->assertSame(['schema_version' => 2, 'groups' => [], 'elements' => []], $schema);
    }

    public function test_render_tree_interleaves_groups_and_elements_by_order()
    {
        $tree = $this->service->renderTree($this->v2Schema());

        $this->assertCount(3, $tree); // el_type (10), grp_1 (20), el_extra (30)
        $this->assertSame('element', $tree[0]['kind']);
        $this->assertSame('el_type', $tree[0]['element']['id']);
        $this->assertSame('group', $tree[1]['kind']);
        $this->assertSame('grp_1', $tree[1]['group']['id']);
        $this->assertSame(['el_desc', 'el_name'], array_column($tree[1]['elements'], 'id'));
        $this->assertSame('el_extra', $tree[2]['element']['id']);
    }

    public function test_answers_by_id_reads_v2_snapshots()
    {
        $snapshot = [
            ['id' => 'el_type', 'type' => 'select', 'label' => 'Customer Type', 'value' => 'VIP', 'hidden' => false],
            ['id' => 'el_name', 'type' => 'text', 'label' => 'Customer Name', 'value' => 'Ali', 'hidden' => false],
        ];

        $answers = $this->service->answersById($snapshot, $this->v2Schema());

        $this->assertSame('VIP', $answers['el_type']);
        $this->assertSame('Ali', $answers['el_name']);
    }

    public function test_answers_by_id_maps_legacy_snapshots_positionally_and_by_label()
    {
        $legacySnapshot = [
            ['type' => 'select', 'label' => 'Customer Type', 'value' => 'VIP'],
            ['type' => 'text', 'label' => 'Customer Name', 'value' => 'Ali'],
        ];

        // Positional mapping against a legacy-normalized form.
        $legacyForm = $this->service->normalize([
            ['type' => 'select', 'label' => 'Customer Type', 'values' => ['Walk-in', 'VIP']],
            ['type' => 'text', 'label' => 'Customer Name'],
        ]);
        $answers = $this->service->answersById($legacySnapshot, $legacyForm);
        $this->assertSame('VIP', $answers['el_legacy_0']);
        $this->assertSame('Ali', $answers['el_legacy_1']);

        // Label fallback against a re-saved v2 form.
        $answers = $this->service->answersById($legacySnapshot, $this->v2Schema());
        $this->assertSame('VIP', $answers['el_type']);
        $this->assertSame('Ali', $answers['el_name']);
    }

    public function test_resolve_visibility_applies_element_conditions()
    {
        $schema = $this->v2Schema();

        $visible = $this->service->resolveVisibility($schema, ['el_type' => 'Walk-in']);
        $this->assertFalse($visible['el_name']);

        $visible = $this->service->resolveVisibility($schema, ['el_type' => 'VIP']);
        $this->assertTrue($visible['el_name']);
    }

    public function test_resolve_visibility_chains_through_hidden_fields()
    {
        // el_extra depends on el_name being non-empty; el_name is hidden for
        // Walk-in, so its value must be treated as empty and el_extra hidden too.
        $schema = $this->v2Schema();

        $visible = $this->service->resolveVisibility($schema, ['el_type' => 'Walk-in', 'el_name' => 'Ali']);
        $this->assertFalse($visible['el_name']);
        $this->assertFalse($visible['el_extra']);

        $visible = $this->service->resolveVisibility($schema, ['el_type' => 'VIP', 'el_name' => 'Ali']);
        $this->assertTrue($visible['el_name']);
        $this->assertTrue($visible['el_extra']);
    }

    public function test_resolve_visibility_group_condition_cascades_to_members()
    {
        $schema = $this->v2Schema();
        $schema['groups'][0]['visible_when'] = [
            'logic' => 'or',
            'groups' => [['logic' => 'and', 'conditions' => [
                ['field' => 'el_type', 'operator' => 'equals', 'value' => 'VIP'],
            ]]],
        ];
        // Remove the element-level condition so only the group condition applies.
        $schema['elements'][2]['visible_when'] = null;

        $visible = $this->service->resolveVisibility($schema, ['el_type' => 'Walk-in']);
        $this->assertFalse($visible['el_name']);
        $this->assertFalse($visible['el_desc']);

        $visible = $this->service->resolveVisibility($schema, ['el_type' => 'VIP']);
        $this->assertTrue($visible['el_name']);
    }

    public function test_sanitize_answers_discards_hidden_values_and_skips_their_mandatory()
    {
        $schema = $this->v2Schema();

        // el_name is mandatory but hidden for Walk-in: smuggled value must be
        // nulled and no error raised for it.
        $result = $this->service->sanitizeAnswers($schema, [
            ['id' => 'el_type', 'value' => 'Walk-in'],
            ['id' => 'el_name', 'value' => 'smuggled'],
        ]);

        $this->assertSame([], $result['errors']);
        $byId = collect($result['snapshot'])->keyBy('id');
        $this->assertNull($byId['el_name']['value']);
        $this->assertTrue($byId['el_name']['hidden']);
        $this->assertFalse($byId['el_type']['hidden']);
    }

    public function test_sanitize_answers_enforces_mandatory_on_visible_fields()
    {
        $schema = $this->v2Schema();

        $result = $this->service->sanitizeAnswers($schema, [
            ['id' => 'el_type', 'value' => 'VIP'],
            ['id' => 'el_name', 'value' => ''],
        ]);

        $this->assertArrayHasKey('el_name', $result['errors']);
    }

    public function test_sanitize_answers_defers_fields_owned_by_fill_phases()
    {
        $schema = $this->v2Schema();

        // el_name is mandatory + visible (VIP), but deferred to a later fill
        // phase: no error, no value collected from the submitter.
        $result = $this->service->sanitizeAnswers($schema, [
            ['id' => 'el_type', 'value' => 'VIP'],
            ['id' => 'el_name', 'value' => 'smuggled by submitter'],
        ], ['el_name']);

        $this->assertSame([], $result['errors']);
        $byId = collect($result['snapshot'])->keyBy('id');
        $this->assertNull($byId['el_name']['value']);
        $this->assertFalse($byId['el_name']['hidden']); // visible to the later fill stage
    }

    public function test_validate_definition_catches_structural_errors()
    {
        $schema = $this->v2Schema();

        $this->assertSame([], $this->service->validateDefinition($schema));

        // Duplicate element id.
        $bad = $schema;
        $bad['elements'][3]['id'] = 'el_type';
        $this->assertNotEmpty($this->service->validateDefinition($bad));

        // Element referencing a missing group.
        $bad = $schema;
        $bad['elements'][0]['group_id'] = 'grp_missing';
        $this->assertNotEmpty($this->service->validateDefinition($bad));

        // Condition referencing a missing field.
        $bad = $schema;
        $bad['elements'][3]['visible_when']['groups'][0]['conditions'][0]['field'] = 'el_missing';
        $this->assertNotEmpty($this->service->validateDefinition($bad));

        // No input fields at all.
        $this->assertNotEmpty($this->service->validateDefinition(['schema_version' => 2, 'groups' => [], 'elements' => []]));
    }

    private function gpsSchema(bool $mandatory = true): array
    {
        return [
            'schema_version' => 2,
            'groups'         => [],
            'elements'       => [
                ['id' => 'el_visit', 'kind' => 'field', 'type' => 'select', 'label' => 'Visit type',
                 'mandatory' => false, 'values' => ['On site', 'Remote'], 'group_id' => null, 'order' => 10,
                 'visible_when' => null],
                ['id' => 'el_site', 'kind' => 'field', 'type' => 'gps', 'label' => 'Site location',
                 'mandatory' => $mandatory, 'values' => [], 'group_id' => null, 'order' => 20,
                 'capture_mode' => 'auto',
                 'visible_when' => [
                     'logic'  => 'or',
                     'groups' => [['logic' => 'and', 'conditions' => [
                         ['field' => 'el_visit', 'operator' => 'equals', 'value' => 'On site'],
                     ]]],
                 ]],
            ],
        ];
    }

    public function test_normalize_gps_accepts_and_rounds_a_valid_stamp()
    {
        $element = ['label' => 'Site location'];

        $result = $this->service->normalizeGps($element, [
            'lat'         => 3.139012345678,
            'lng'         => 101.686855312345,
            'accuracy'    => 12.46,
            'captured_at' => '2026-09-28T09:41:07+08:00',
        ]);

        $this->assertNull($result['error']);
        $this->assertSame(3.1390123, $result['value']['lat']);
        $this->assertSame(101.6868553, $result['value']['lng']);
        $this->assertSame(12.5, $result['value']['accuracy']);
        $this->assertSame('2026-09-28T09:41:07+08:00', $result['value']['captured_at']);

        // JSON string input, null accuracy, missing captured_at, boundary coords.
        $result = $this->service->normalizeGps($element, '{"lat":-90,"lng":180,"accuracy":null}');
        $this->assertNull($result['error']);
        $this->assertSame(-90.0, $result['value']['lat']);
        $this->assertSame(180.0, $result['value']['lng']);
        $this->assertNull($result['value']['accuracy']);
        $this->assertNotEmpty($result['value']['captured_at']);

        // expo-location reports its timestamp in epoch milliseconds.
        $result = $this->service->normalizeGps($element, ['lat' => 1, 'lng' => 2, 'captured_at' => 1790000000000]);
        $this->assertNull($result['error']);
        $this->assertSame(1790000000, strtotime($result['value']['captured_at']));

        // Empty stays empty with no error: mandatory is the caller's call.
        $this->assertSame(['value' => null, 'error' => null], $this->service->normalizeGps($element, null));
        $this->assertSame(['value' => null, 'error' => null], $this->service->normalizeGps($element, '{}'));
    }

    public function test_normalize_gps_rejects_out_of_range_and_garbage()
    {
        $element = ['label' => 'Site location'];
        $bad     = [
            ['lat' => 200, 'lng' => 101.68],
            ['lat' => 3.13, 'lng' => -181],
            ['lat' => 'north', 'lng' => 101.68],
            ['lat' => 3.13],
            ['lat' => 3.13, 'lng' => 101.68, 'accuracy' => -5],
            ['lat' => 3.13, 'lng' => 101.68, 'accuracy' => 'close'],
            ['lat' => 3.13, 'lng' => 101.68, 'captured_at' => 'not a date'],
            ['lat' => 3.13, 'lng' => 101.68, 'captured_at' => ['2026']],
            'Kuala Lumpur',
            '3.13,101.68',
            42,
        ];

        foreach ($bad as $value) {
            $result = $this->service->normalizeGps($element, $value);
            $this->assertNull($result['value'], 'Accepted: ' . json_encode($value));
            $this->assertSame('Tap to stamp your current location.', $result['error']);
        }
    }

    public function test_sanitize_answers_validates_gps_fields()
    {
        $schema = $this->gpsSchema();

        // Valid stamp is normalised into the snapshot.
        $result = $this->service->sanitizeAnswers($schema, [
            ['id' => 'el_visit', 'value' => 'On site'],
            ['id' => 'el_site', 'value' => ['lat' => 3.13901234567, 'lng' => 101.68685531, 'accuracy' => 8]],
        ]);
        $this->assertSame([], $result['errors']);
        $site = collect($result['snapshot'])->keyBy('id')['el_site'];
        $this->assertSame(3.1390123, $site['value']['lat']);
        $this->assertSame(8.0, $site['value']['accuracy']);

        // Mandatory, visible and empty: required. An empty JSON object counts as empty.
        foreach ([null, '', '{}'] as $empty) {
            $result = $this->service->sanitizeAnswers($schema, [
                ['id' => 'el_visit', 'value' => 'On site'],
                ['id' => 'el_site', 'value' => $empty],
            ]);
            $this->assertSame('Site location is required.', $result['errors']['el_site'] ?? null);
        }

        // Out of range: the specific message wins over "required".
        $result = $this->service->sanitizeAnswers($schema, [
            ['id' => 'el_visit', 'value' => 'On site'],
            ['id' => 'el_site', 'value' => ['lat' => 200, 'lng' => 101.68]],
        ]);
        $this->assertSame('Tap to stamp your current location.', $result['errors']['el_site']);

        // Hidden: the value is discarded, even a bad one, and nothing is required.
        $result = $this->service->sanitizeAnswers($schema, [
            ['id' => 'el_visit', 'value' => 'Remote'],
            ['id' => 'el_site', 'value' => ['lat' => 200, 'lng' => 101.68]],
        ]);
        $this->assertSame([], $result['errors']);
        $site = collect($result['snapshot'])->keyBy('id')['el_site'];
        $this->assertNull($site['value']);
        $this->assertTrue($site['hidden']);
    }

    public function test_validate_definition_checks_gps_capture_mode()
    {
        $schema = $this->gpsSchema();
        $this->assertSame([], $this->service->validateDefinition($schema));

        // Missing capture_mode falls back to auto.
        unset($schema['elements'][1]['capture_mode']);
        $this->assertSame([], $this->service->validateDefinition($schema));

        $schema['elements'][1]['capture_mode'] = 'manual';
        $this->assertSame([], $this->service->validateDefinition($schema));

        $schema['elements'][1]['capture_mode'] = 'on_submit';
        $this->assertNotEmpty($this->service->validateDefinition($schema));
    }

    public function test_resolve_field_permissions_cascades_group_overrides()
    {
        $schema = $this->v2Schema();
        $permissions = [
            'default'   => 'read',
            'overrides' => ['grp_1' => 'hidden', 'el_name' => 'edit'],
        ];

        $resolved = $this->service->resolveFieldPermissions($schema, $permissions);

        $this->assertSame('read', $resolved['el_type']);      // default
        $this->assertSame('hidden', $resolved['el_desc']);    // group cascade
        $this->assertSame('edit', $resolved['el_name']);      // field override beats group
    }
}
