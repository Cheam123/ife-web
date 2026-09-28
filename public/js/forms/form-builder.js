/**
 * Form builder (create/edit) — Lark-style three-panel Step 2 plus wizard,
 * Step 1 access settings and final serialization.
 *
 *   left   = widget palette (click or drag to add)
 *   center = phone-style live preview (#builder-canvas); click a row to
 *            select it, drag to reorder, drag into/out of group sections
 *   right  = settings panel for the selected widget (Basic / Visibility tabs)
 *
 * Element/group config lives in the Store (id -> object); the preview DOM
 * only carries ids and ordering. Boot data arrives on window.FormBuilderBoot
 * = { schema, process, settings, users }.
 *
 * Step 3 (process designer) lives in form-process-designer.js and reuses the
 * condition editor through window.FormBuilderApi.openConditionEditor (modal).
 */
(function ($) {
    'use strict';

    var TYPES_WITH_VALUES = ['select', 'multi-choice', 'multi-select', 'checkbox'];
    var CONDITION_OPERATORS = {
        'text':         ['equals', 'not_equals', 'is_empty', 'is_not_empty'],
        'textarea':     ['equals', 'not_equals', 'is_empty', 'is_not_empty'],
        'email':        ['equals', 'not_equals', 'is_empty', 'is_not_empty'],
        'tel':          ['equals', 'not_equals', 'is_empty', 'is_not_empty'],
        'select':       ['equals', 'not_equals', 'is_empty', 'is_not_empty'],
        'multi-choice': ['includes', 'not_includes', 'is_empty', 'is_not_empty'],
        'multi-select': ['includes', 'not_includes', 'is_empty', 'is_not_empty'],
        'checkbox':     ['includes', 'not_includes', 'is_empty', 'is_not_empty'],
        'number':       ['equals', 'not_equals', 'gt', 'lt', 'is_empty', 'is_not_empty'],
        'date':         ['equals', 'not_equals', 'gt', 'lt', 'is_empty', 'is_not_empty'],
        'time':         ['equals', 'not_equals', 'gt', 'lt', 'is_empty', 'is_not_empty'],
        'file':         ['is_empty', 'is_not_empty'],
        'user':         ['equals', 'not_equals', 'is_empty', 'is_not_empty']
    };
    var OPERATOR_LABELS = {
        equals: 'is', not_equals: 'is not',
        includes: 'includes', not_includes: 'does not include',
        gt: 'is after / greater than', lt: 'is before / less than',
        is_empty: 'is empty', is_not_empty: 'is not empty'
    };
    var VALUELESS_OPERATORS = ['is_empty', 'is_not_empty'];
    var PLACEHOLDERS = {
        'text': 'Enter', 'textarea': 'Enter', 'email': 'Enter', 'tel': 'Enter', 'number': 'Enter',
        'select': 'Select ›', 'multi-choice': 'Select ›', 'multi-select': 'Select ›',
        'checkbox': 'Select ›', 'date': 'Select ›', 'time': 'Select ›', 'file': 'Upload ›',
        'user': 'Select a person ›'
    };

    var uidCounter = 0;
    function uid(prefix) {
        uidCounter++;
        return prefix + '_' + Date.now().toString(36) + uidCounter;
    }

    function esc(text) {
        return $('<div>').text(text == null ? '' : String(text)).html();
    }

    function conditionCount(schema) {
        if (!schema || !schema.groups) return 0;
        return schema.groups.reduce(function (n, g) { return n + (g.conditions || []).length; }, 0);
    }

    /* =================================================================
     * Store — element/group config keyed by id
     * ================================================================= */

    var Store = {
        elements: {},   // el id -> {kind, type, label, text, placeholder, mandatory, values, min, max, min_days, visible_when}
        groups: {},     // grp id -> {label, visible_when}

        addElement: function (config) {
            var id = config.id || uid('el');
            config.id = id;
            this.elements[id] = config;
            return id;
        },
        addGroup: function (config) {
            var id = config.id || uid('grp');
            config.id = id;
            this.groups[id] = config;
            return id;
        }
    };

    /* =================================================================
     * Wizard navigation
     * ================================================================= */

    var Wizard = {
        current: 1,
        init: function () {
            var self = this;
            $('.wizard-next').on('click', function () {
                if (self.validateStep(self.current)) self.go(self.current + 1);
            });
            $('.wizard-back').on('click', function () { self.go(self.current - 1); });
            $('.wizard-pill').on('click', function () {
                var target = parseInt($(this).data('step'), 10);
                if (target < self.current || self.validateStep(self.current)) self.go(target);
            });
            this.go(1);
        },
        go: function (step) {
            if (step < 1 || step > 3) return;
            this.current = step;
            $('.wizard-pane').addClass('d-none');
            $('.wizard-pane[data-step="' + step + '"]').removeClass('d-none');
            $('.wizard-pill').removeClass('active');
            $('.wizard-pill[data-step="' + step + '"]').addClass('active');
            $('#wizard-back-btn').toggleClass('d-none', step === 1);
            $('#wizard-next-btn').toggleClass('d-none', step === 3);
            $('#submit-form-btn').toggleClass('d-none', step !== 3);
            if (step === 2) {
                $('#phone-form-name').text($('#form_name').val() || 'New Form');
            }
            if (step === 3 && window.FormProcessDesigner) {
                window.FormProcessDesigner.refreshFieldsFromBuilder();
            }
        },
        validateStep: function (step) {
            if (step === 1) return Builder.validateBasicInfo();
            if (step === 2) return Builder.validateDesign();
            return true;
        }
    };

    /* =================================================================
     * Shared condition-editor UI pieces
     *
     * Renders OR'd condition blocks of AND'd rows into any container with
     * class .condition-groups-list. Used by the settings panel (autosave)
     * and by the modal (branch conditions, explicit save).
     * ================================================================= */

    var ConditionUI = {
        // Fields usable as condition sources (every input field except the excluded one).
        sourceFields: function (excludeId) {
            return Builder.collectElements().filter(function (el) {
                return el.kind === 'field' && el.id !== excludeId;
            });
        },

        render: function ($container, schema, excludeId) {
            $container.empty().data('excludeId', excludeId || null);
            var groups = (schema && schema.groups) || [];
            var self = this;
            groups.forEach(function (group) { self.addGroup($container, group); });
        },

        addGroup: function ($container, group) {
            var $group = $(
                '<div class="condition-group border rounded p-2 mb-2 bg-light">' +
                '  <div class="d-flex justify-content-between align-items-center mb-2">' +
                '    <span class="fw-semibold small text-muted">When ALL of these match</span>' +
                '    <button type="button" class="btn btn-sm btn-outline-danger remove-condition-group"><i class="mdi mdi-trash-can"></i></button>' +
                '  </div>' +
                '  <div class="condition-rows"></div>' +
                '  <button type="button" class="btn btn-sm btn-outline-secondary add-condition-row mt-1"><i class="mdi mdi-plus"></i> And condition</button>' +
                '</div>'
            );
            $container.append($group);

            var conditions = (group && group.conditions) || [];
            if (conditions.length === 0) {
                this.addRow($group.find('.condition-rows'));
            } else {
                var self = this;
                conditions.forEach(function (condition) { self.addRow($group.find('.condition-rows'), condition); });
            }
            return $group;
        },

        addRow: function ($rows, condition) {
            var excludeId = $rows.closest('.condition-groups-list').data('excludeId');
            var fields = this.sourceFields(excludeId);
            var fieldOptions = fields.map(function (f) {
                var selected = condition && condition.field === f.id ? ' selected' : '';
                return '<option value="' + esc(f.id) + '"' + selected + '>' + esc(f.label || '(untitled)') + '</option>';
            }).join('');

            var $row = $(
                '<div class="condition-row d-flex flex-wrap gap-1 align-items-center mb-2">' +
                '  <select class="form-select form-select-sm condition-field js-select2" data-placeholder="Select a field..." style="max-width:100%;flex:1 1 100%;">' + fieldOptions + '</select>' +
                '  <select class="form-select form-select-sm condition-operator js-select2" data-no-search="1" style="flex:1 1 45%;"></select>' +
                '  <span class="condition-value-slot d-inline-flex" style="flex:1 1 40%;"></span>' +
                '  <button type="button" class="btn btn-sm btn-outline-danger remove-condition-row"><i class="mdi mdi-minus"></i></button>' +
                '</div>'
            );
            $rows.append($row);
            this.refreshRow($row, condition);
            if (window.FormSelect2) window.FormSelect2.apply($row);
        },

        refreshRow: function ($row, condition) {
            var excludeId = $row.closest('.condition-groups-list').data('excludeId');
            var fields = this.sourceFields(excludeId);
            var fieldId = $row.find('.condition-field').val();
            var field = fields.filter(function (f) { return f.id === fieldId; })[0];
            var operators = field ? (CONDITION_OPERATORS[field.type] || CONDITION_OPERATORS.text) : CONDITION_OPERATORS.text;

            var $operator = $row.find('.condition-operator').empty();
            operators.forEach(function (op) {
                var selected = condition && condition.operator === op ? ' selected' : '';
                $operator.append('<option value="' + op + '"' + selected + '>' + OPERATOR_LABELS[op] + '</option>');
            });
            if ($operator.data('select2')) $operator.trigger('change.select2'); // repaint the new options

            this.refreshValueControl($row, field, condition);
        },

        refreshValueControl: function ($row, field, condition) {
            var operator = $row.find('.condition-operator').val();
            var $slot = $row.find('.condition-value-slot');
            if (window.FormSelect2) window.FormSelect2.destroy($slot);
            $slot.empty();
            if (VALUELESS_OPERATORS.indexOf(operator) !== -1) return;

            var current = condition ? condition.value : null;
            var options = [];
            if (field && TYPES_WITH_VALUES.indexOf(field.type) !== -1) {
                (field.values || []).forEach(function (value) {
                    if (value && typeof value === 'object' && value.options) {
                        (value.options || []).forEach(function (opt) { if (opt) options.push(opt); });
                    } else if (value) {
                        options.push(value);
                    }
                });
            }

            if (options.length > 0) {
                var html = '<select class="form-select form-select-sm condition-value js-select2" data-placeholder="Select a value...">';
                options.forEach(function (opt) {
                    var selected = current !== null && String(current) === String(opt) ? ' selected' : '';
                    html += '<option value="' + esc(opt) + '"' + selected + '>' + esc(opt) + '</option>';
                });
                html += '</select>';
                $slot.append(html);
            } else {
                var inputType = field && field.type === 'date' ? 'date'
                              : (field && field.type === 'time' ? 'time'
                              : (field && field.type === 'number' ? 'number' : 'text'));
                $slot.append('<input type="' + inputType + '" class="form-control form-control-sm condition-value" value="' + esc(current || '') + '">');
            }
            if (window.FormSelect2) window.FormSelect2.apply($slot);
        },

        serialize: function ($container) {
            var groups = [];
            $container.find('.condition-group').each(function () {
                var conditions = [];
                $(this).find('.condition-row').each(function () {
                    var field = $(this).find('.condition-field').val();
                    var operator = $(this).find('.condition-operator').val();
                    if (!field || !operator) return;
                    var value = null;
                    if (VALUELESS_OPERATORS.indexOf(operator) === -1) {
                        value = $(this).find('.condition-value').val() || '';
                    }
                    conditions.push({ field: field, operator: operator, value: value });
                });
                if (conditions.length > 0) {
                    groups.push({ logic: 'and', conditions: conditions });
                }
            });
            return groups.length > 0 ? { logic: 'or', groups: groups } : null;
        },

        init: function () {
            var self = this;

            // Structure edits (both modal and panel).
            $(document).on('click', '.add-condition-row', function () {
                self.addRow($(this).siblings('.condition-rows'));
                Panel.autosaveConditions(this);
            });
            $(document).on('click', '.remove-condition-row', function () {
                var $rows = $(this).closest('.condition-rows');
                $(this).closest('.condition-row').remove();
                if ($rows.children().length === 0) {
                    $rows.closest('.condition-group').remove();
                }
                Panel.autosaveConditions(this);
            });
            $(document).on('click', '.remove-condition-group', function () {
                var container = this;
                $(this).closest('.condition-group').remove();
                Panel.autosaveConditions(container);
            });
            $(document).on('change', '.condition-groups-list .condition-field', function () {
                self.refreshRow($(this).closest('.condition-row'));
                Panel.autosaveConditions(this);
            });
            $(document).on('change', '.condition-groups-list .condition-operator', function () {
                var $row = $(this).closest('.condition-row');
                var excludeId = $row.closest('.condition-groups-list').data('excludeId');
                var fieldId = $row.find('.condition-field').val();
                var field = self.sourceFields(excludeId).filter(function (f) { return f.id === fieldId; })[0];
                self.refreshValueControl($row, field);
                Panel.autosaveConditions(this);
            });
            $(document).on('change input', '.condition-groups-list .condition-value', function () {
                Panel.autosaveConditions(this);
            });
        }
    };

    /* Modal wrapper (used by the process designer for branch conditions). */
    var ConditionModal = {
        callback: null,
        open: function (schema, excludeId, onSave) {
            this.callback = onSave;
            var $list = $('#condition-groups-list');
            ConditionUI.render($list, schema, excludeId);
            if ($list.children().length === 0) ConditionUI.addGroup($list);
            $('#conditionModal').modal('show');
        },
        init: function () {
            var self = this;
            $('#add-condition-group-btn').on('click', function () {
                ConditionUI.addGroup($('#condition-groups-list'));
            });
            $('#condition-save-btn').on('click', function () {
                if (self.callback) self.callback(ConditionUI.serialize($('#condition-groups-list')));
                $('#conditionModal').modal('hide');
            });
            $('#condition-clear-btn').on('click', function () {
                if (self.callback) self.callback(null);
                $('#conditionModal').modal('hide');
            });
        }
    };

    /* =================================================================
     * Settings panel (right column)
     * ================================================================= */

    var Panel = {
        selectedId: null,      // element id or group id
        selectedKind: null,    // 'element' | 'group'

        show: function (kind, id) {
            this.selectedId = id;
            this.selectedKind = kind;

            $('.pv-row, .pv-group').removeClass('pv-selected');
            var $node = kind === 'group'
                ? $('.pv-group[data-group-id="' + id + '"]')
                : $('.pv-row[data-el-id="' + id + '"]');
            $node.addClass('pv-selected');

            $('#settings-empty').addClass('d-none');
            $('#settings-panel').removeClass('d-none');
            this.showTab('basic');

            if (kind === 'group') {
                $('#settings-type-title').text('Group');
                this.renderGroupBasic(Store.groups[id]);
                this.renderVisibility(Store.groups[id].visible_when, null);
            } else {
                var element = Store.elements[id];
                $('#settings-type-title').text(element.kind === 'description' ? 'Description' : Builder.typeLabel(element.type));
                if (element.kind === 'description') {
                    this.renderDescriptionBasic(element);
                } else {
                    this.renderFieldBasic(element);
                }
                this.renderVisibility(element.visible_when, id);
            }
        },

        clear: function () {
            this.selectedId = null;
            this.selectedKind = null;
            $('.pv-row, .pv-group').removeClass('pv-selected');
            $('#settings-panel').addClass('d-none');
            $('#settings-empty').removeClass('d-none');
        },

        showTab: function (tab) {
            $('#settings-tabs .nav-link').removeClass('active');
            $('#settings-tabs .nav-link[data-panel-tab="' + tab + '"]').addClass('active');
            $('#panel-basic').toggleClass('d-none', tab !== 'basic');
            $('#panel-visibility').toggleClass('d-none', tab !== 'visibility');
        },

        /* ---------- basic tab renderers ---------- */

        renderFieldBasic: function (element) {
            var hasValues = TYPES_WITH_VALUES.indexOf(element.type) !== -1;
            var hasMinMax = element.type === 'multi-select' || element.type === 'multi-choice';
            var isDate = element.type === 'date';

            var html =
                '<div class="mb-3">' +
                '  <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>' +
                '  <input type="text" class="form-control form-control-sm" id="ps-label" value="' + esc(element.label || '') + '" placeholder="Field title">' +
                '</div>' +
                '<div class="mb-3">' +
                '  <label class="form-label small fw-semibold">Tooltip / placeholder</label>' +
                '  <input type="text" class="form-control form-control-sm" id="ps-placeholder" value="' + esc(element.placeholder || '') + '" placeholder="Shown inside the empty input">' +
                '</div>' +
                '<div class="form-check form-switch mb-3">' +
                '  <input class="form-check-input" type="checkbox" id="ps-mandatory"' + (element.mandatory ? ' checked' : '') + '>' +
                '  <label class="form-check-label small" for="ps-mandatory">Required</label>' +
                '</div>';

            if (isDate) {
                html +=
                    '<div class="mb-3">' +
                    '  <label class="form-label small fw-semibold">Minimum date of request</label>' +
                    '  <div class="input-group input-group-sm" style="max-width:260px;">' +
                    '    <span class="input-group-text">Today +</span>' +
                    '    <input type="number" class="form-control" id="ps-min-days" min="1" max="365" value="' + esc(element.min_days || '') + '" placeholder="none">' +
                    '    <span class="input-group-text">days</span>' +
                    '  </div>' +
                    '  <div class="form-text small">The earliest date users may pick. Leave empty for no minimum.</div>' +
                    '</div>';
            }

            if (hasMinMax) {
                html +=
                    '<div class="d-flex gap-2 mb-3">' +
                    '  <div class="input-group input-group-sm"><span class="input-group-text">Min</span><input type="number" class="form-control" id="ps-min" min="1" value="' + esc(element.min || 1) + '"></div>' +
                    '  <div class="input-group input-group-sm"><span class="input-group-text">Max</span><input type="number" class="form-control" id="ps-max" min="1" value="' + esc(element.max || '') + '"></div>' +
                    '</div>';
            }

            if (element.type === 'user') {
                html += this.userSourceHtml(element);
            }

            if (hasValues) {
                html += '<label class="form-label small fw-semibold">Options <span class="text-danger">*</span></label>';
                html += '<div id="ps-options">' + this.optionsEditorHtml(element) + '</div>';
            }

            // The panel re-renders whenever the selection changes, so tear the
            // old select2 controls down before replacing their markup.
            if (window.FormSelect2) window.FormSelect2.destroy('#panel-basic');
            $('#panel-basic').html(html);
            if (window.FormSelect2) window.FormSelect2.apply('#panel-basic');
        },

        /* Person field: which people the picker offers. */
        userSourceHtml: function (element) {
            var source  = element.user_source === 'selected' ? 'selected' : 'all';
            var chosen  = (element.user_ids || []).map(String);
            var users   = (Builder.boot && Builder.boot.users) || [];
            var options = '';

            users.forEach(function (user) {
                options += '<option value="' + esc(user.id) + '"' +
                           (chosen.indexOf(String(user.id)) !== -1 ? ' selected' : '') + '>' +
                           esc(user.name) + '</option>';
            });

            return '<div class="mb-3">' +
                   '  <label class="form-label small fw-semibold">People to choose from</label>' +
                   '  <select class="form-select form-select-sm mb-2 js-select2" id="ps-user-source" data-no-search="1">' +
                   '    <option value="all"' + (source === 'all' ? ' selected' : '') + '>Everyone</option>' +
                   '    <option value="selected"' + (source === 'selected' ? ' selected' : '') + '>Selected people only</option>' +
                   '  </select>' +
                   '  <div id="ps-user-ids-wrap" class="' + (source === 'selected' ? '' : 'd-none') + '">' +
                   '    <select class="form-select form-select-sm js-select2" id="ps-user-ids" multiple' +
                   '            data-placeholder="Search and select people...">' + options + '</select>' +
                   '    <div class="form-text small">Only these people appear in the picker on the form.</div>' +
                   '  </div>' +
                   '</div>';
        },


        renderDescriptionBasic: function (element) {
            $('#panel-basic').html(
                '<div class="mb-3">' +
                '  <label class="form-label small fw-semibold">Text <span class="text-danger">*</span></label>' +
                '  <textarea class="form-control form-control-sm" id="ps-desc-text" rows="4" placeholder="Description shown on the form">' + esc(element.text || '') + '</textarea>' +
                '</div>'
            );
        },

        renderGroupBasic: function (group) {
            $('#panel-basic').html(
                '<div class="mb-3">' +
                '  <label class="form-label small fw-semibold">Group name <span class="text-danger">*</span></label>' +
                '  <input type="text" class="form-control form-control-sm" id="ps-group-label" value="' + esc(group.label || '') + '" placeholder="e.g. Customer Details">' +
                '</div>' +
                '<p class="text-muted small mb-0">Drag fields into the group section in the preview. Hiding a group hides all fields inside it.</p>'
            );
        },

        optionsEditorHtml: function (element) {
            var html = '';
            if (element.type === 'multi-select') {
                var groups = element.values && element.values.length ? element.values : [{ group: '', options: [''] }];
                groups.forEach(function (group, gi) {
                    html +=
                        '<div class="border rounded p-2 mb-2 bg-light ps-ms-group" data-gi="' + gi + '">' +
                        '  <div class="d-flex align-items-center gap-1 mb-1">' +
                        '    <input type="text" class="form-control form-control-sm ps-ms-group-name" placeholder="Category name" value="' + esc(group.group || '') + '">' +
                        '    <button type="button" class="btn btn-sm btn-outline-danger ps-ms-remove-group"><i class="mdi mdi-minus"></i></button>' +
                        '  </div>';
                    (group.options && group.options.length ? group.options : ['']).forEach(function (option) {
                        html +=
                            '  <div class="d-flex align-items-center gap-1 mb-1 ps-ms-option">' +
                            '    <i class="mdi mdi-drag-vertical text-muted"></i>' +
                            '    <input type="text" class="form-control form-control-sm ps-ms-option-input" placeholder="Option" value="' + esc(option) + '">' +
                            '    <button type="button" class="btn btn-sm btn-outline-danger ps-ms-remove-option"><i class="mdi mdi-minus"></i></button>' +
                            '  </div>';
                    });
                    html += '  <button type="button" class="btn btn-sm btn-link p-0 ps-ms-add-option"><i class="mdi mdi-plus"></i> Option</button>' +
                        '</div>';
                });
                html += '<button type="button" class="btn btn-sm btn-outline-success" id="ps-ms-add-group"><i class="mdi mdi-plus"></i> Add category</button>';
            } else {
                var values = element.values && element.values.length ? element.values : [''];
                var collapsed = values.length > 5;
                html += '<div id="ps-options-list" class="' + (collapsed ? 'ps-options-collapsed' : '') + '">';
                values.forEach(function (value) {
                    html +=
                        '<div class="d-flex align-items-center gap-1 mb-1 ps-option">' +
                        '  <i class="mdi mdi-drag-vertical text-muted"></i>' +
                        '  <input type="text" class="form-control form-control-sm ps-option-input" placeholder="Option" value="' + esc(value) + '">' +
                        '  <button type="button" class="btn btn-sm btn-outline-danger ps-remove-option"><i class="mdi mdi-minus"></i></button>' +
                        '</div>';
                });
                html += '</div>' +
                    '<div class="d-flex align-items-center gap-3 mt-1">' +
                    '  <button type="button" class="btn btn-sm btn-link p-0" id="ps-add-option"><i class="mdi mdi-plus"></i> Add option</button>' +
                    '  <button type="button" class="btn btn-sm btn-link p-0 text-muted ' + (collapsed ? '' : 'd-none') + '" id="ps-options-toggle">' +
                    '    <i class="mdi mdi-chevron-down"></i> Show all (' + values.length + ')' +
                    '  </button>' +
                    '</div>';
            }
            return html;
        },

        /* ---------- visibility tab ---------- */

        renderVisibility: function (schema, excludeId) {
            var $list = $('#panel-condition-groups');
            ConditionUI.render($list, schema, excludeId);
            var sources = ConditionUI.sourceFields(excludeId);
            $('#panel-add-condition-group').prop('disabled', sources.length === 0)
                .attr('title', sources.length === 0 ? 'Add other input fields first' : '');
        },

        // Called by ConditionUI on any edit inside a .condition-groups-list;
        // saves only when the edit happened inside the settings panel.
        autosaveConditions: function (sourceEl) {
            if (!$(sourceEl).closest('#panel-visibility').length) return;
            if (!this.selectedId) return;
            var schema = ConditionUI.serialize($('#panel-condition-groups'));
            if (this.selectedKind === 'group') {
                Store.groups[this.selectedId].visible_when = schema;
                Builder.refreshGroupNode(this.selectedId);
            } else {
                Store.elements[this.selectedId].visible_when = schema;
                Builder.refreshRowNode(this.selectedId);
            }
        },

        /* ---------- events (inputs write back to Store) ---------- */

        element: function () {
            return this.selectedKind === 'element' ? Store.elements[this.selectedId] : null;
        },

        readOptions: function (element) {
            if (element.type === 'multi-select') {
                var groups = [];
                $('#ps-options .ps-ms-group').each(function () {
                    var name = ($(this).find('.ps-ms-group-name').val() || '').trim();
                    var options = [];
                    $(this).find('.ps-ms-option-input').each(function () { if ($(this).val()) options.push($(this).val()); });
                    if (name || options.length) groups.push({ group: name, options: options });
                });
                element.values = groups;
            } else {
                var values = [];
                $('#ps-options .ps-option-input').each(function () { if ($(this).val()) values.push($(this).val()); });
                element.values = values;
            }
        },

        // Show/hide the "Show all / Show less" toggle for long option lists,
        // and keep its label in sync with the current collapsed state.
        refreshOptionsCollapse: function () {
            var $list = $('#ps-options-list');
            if (!$list.length) return;
            var total = $list.find('.ps-option').length;
            var $toggle = $('#ps-options-toggle');
            if (total > 5) {
                var collapsed = $list.hasClass('ps-options-collapsed');
                $toggle.removeClass('d-none').html(collapsed
                    ? '<i class="mdi mdi-chevron-down"></i> Show all (' + total + ')'
                    : '<i class="mdi mdi-chevron-up"></i> Show less');
            } else {
                $toggle.addClass('d-none');
                $list.removeClass('ps-options-collapsed');
            }
        },

        init: function () {
            var self = this;

            $('#settings-tabs').on('click', '.nav-link', function () {
                self.showTab($(this).data('panel-tab'));
            });

            $('#panel-add-condition-group').on('click', function () {
                ConditionUI.addGroup($('#panel-condition-groups'));
                self.autosaveConditions($('#panel-condition-groups')[0]);
            });

            $(document).on('input', '#ps-label', function () {
                var element = self.element();
                if (!element) return;
                element.label = $(this).val();
                Builder.refreshRowNode(element.id);
                $(document).trigger('builder:changed');
            });
            $(document).on('input', '#ps-placeholder', function () {
                var element = self.element();
                if (element) element.placeholder = $(this).val();
            });
            $(document).on('change', '#ps-mandatory', function () {
                var element = self.element();
                if (!element) return;
                element.mandatory = $(this).is(':checked');
                Builder.refreshRowNode(element.id);
            });
            $(document).on('input', '#ps-min-days', function () {
                var element = self.element();
                if (element) element.min_days = $(this).val() ? parseInt($(this).val(), 10) : null;
            });
            $(document).on('input', '#ps-min', function () {
                var element = self.element();
                if (element) element.min = $(this).val() || null;
            });
            $(document).on('input', '#ps-max', function () {
                var element = self.element();
                if (element) element.max = $(this).val() || null;
            });
            $(document).on('change', '#ps-user-source', function () {
                var element = self.element();
                if (!element) return;
                element.user_source = $(this).val();
                $('#ps-user-ids-wrap').toggleClass('d-none', element.user_source !== 'selected');
                if (element.user_source !== 'selected') element.user_ids = [];
            });

            $(document).on('change', '#ps-user-ids', function () {
                var element = self.element();
                if (element) element.user_ids = ($(this).val() || []).map(Number);
            });

            $(document).on('input', '#ps-desc-text', function () {
                var element = self.element();
                if (!element) return;
                element.text = $(this).val();
                Builder.refreshRowNode(element.id);
            });
            $(document).on('input', '#ps-group-label', function () {
                if (self.selectedKind !== 'group') return;
                Store.groups[self.selectedId].label = $(this).val();
                Builder.refreshGroupNode(self.selectedId);
            });

            // Options editors — rebuild values in Store on any input.
            $(document).on('input', '.ps-option-input, .ps-ms-option-input, .ps-ms-group-name', function () {
                var element = self.element();
                if (element) self.readOptions(element);
            });
            $(document).on('click', '#ps-add-option', function () {
                // Expand first so the newly-added row is always visible.
                var $newRow = $(
                    '<div class="d-flex align-items-center gap-1 mb-1 ps-option">' +
                    '  <i class="mdi mdi-drag-vertical text-muted"></i>' +
                    '  <input type="text" class="form-control form-control-sm ps-option-input" placeholder="Option">' +
                    '  <button type="button" class="btn btn-sm btn-outline-danger ps-remove-option"><i class="mdi mdi-minus"></i></button>' +
                    '</div>');
                $('#ps-options-list').removeClass('ps-options-collapsed').append($newRow);
                self.refreshOptionsCollapse();
                $newRow.find('.ps-option-input').trigger('focus');
            });
            $(document).on('click', '.ps-remove-option', function () {
                var element = self.element();
                $(this).closest('.ps-option').remove();
                if ($('#ps-options-list').children().length === 0) { $('#ps-add-option').trigger('click'); return; }
                self.refreshOptionsCollapse();
                if (element) self.readOptions(element);
            });
            $(document).on('click', '#ps-options-toggle', function () {
                $('#ps-options-list').toggleClass('ps-options-collapsed');
                self.refreshOptionsCollapse();
            });
            $(document).on('click', '#ps-ms-add-group', function () {
                var element = self.element();
                if (!element) return;
                self.readOptions(element);
                element.values.push({ group: '', options: [''] });
                $('#ps-options').html(self.optionsEditorHtml(element));
            });
            $(document).on('click', '.ps-ms-remove-group', function () {
                var element = self.element();
                $(this).closest('.ps-ms-group').remove();
                if (element) self.readOptions(element);
            });
            $(document).on('click', '.ps-ms-add-option', function () {
                $(this).before(
                    '<div class="d-flex align-items-center gap-1 mb-1 ps-ms-option">' +
                    '  <i class="mdi mdi-drag-vertical text-muted"></i>' +
                    '  <input type="text" class="form-control form-control-sm ps-ms-option-input" placeholder="Option">' +
                    '  <button type="button" class="btn btn-sm btn-outline-danger ps-ms-remove-option"><i class="mdi mdi-minus"></i></button>' +
                    '</div>');
            });
            $(document).on('click', '.ps-ms-remove-option', function () {
                var element = self.element();
                $(this).closest('.ps-ms-option').remove();
                if (element) self.readOptions(element);
            });
        }
    };

    /* =================================================================
     * Step 2 — phone-preview canvas
     * ================================================================= */

    var Builder = {
        init: function (boot) {
            this.boot = boot || {};
            this.$canvas = $('#builder-canvas');
            this.bindPalette();
            this.bindCanvas();
            this.initSortable(this.$canvas[0]);
            this.renderBoot();
            Access.init(this.boot);

            // Live phone header from the Step 1 name field.
            $('#form_name').on('input', function () {
                $('#phone-form-name').text($(this).val() || 'New Form');
            });

            this.bindGroupPicker();
        },

        /**
         * Step 1's group picker, including creating a group without leaving
         * the half-written form.
         */
        bindGroupPicker: function () {
            var $select = $('#form_group_id');
            if (!$select.length) return;

            $select.on('change', function () {
                var chosen = !!$(this).val();
                $(this).toggleClass('is-invalid', !chosen);
                $('#form_group_error').toggleClass('d-none', chosen);
            });

            $('#btn-new-group').on('click', function () {
                $('#newGroupName').val('').removeClass('is-invalid');
                new bootstrap.Modal(document.getElementById('newGroupModal')).show();
            });

            $('#newGroupName').on('input', function () { $(this).removeClass('is-invalid'); });

            $('#btn-save-new-group').on('click', function () {
                var $input = $('#newGroupName');
                var name = $input.val().trim();

                if (!name) {
                    $('#newGroupError').text('A group name is required.');
                    $input.addClass('is-invalid').focus();
                    return;
                }

                var $btn = $(this).prop('disabled', true);
                $btn.find('.lk-group-save-label').addClass('d-none');
                $btn.find('.lk-group-save-busy').removeClass('d-none');

                $.ajax({
                    type: 'post',
                    url: window.FORM_GROUP_STORE_URL,
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    data: { _token: window.CSRF_TOKEN, name: name }
                }).done(function (res) {
                    // Select the new group straight away — the admin made it
                    // for the form they are writing right now.
                    $select.append(new Option(res.group.name, res.group.id, true, true))
                           .val(res.group.id).trigger('change');

                    // Keep the "already in use" list honest for the next open.
                    var $chips = $('.lk-group-chips');
                    if ($chips.length) {
                        $chips.append($('<span class="lk-group-chip"></span>').text(res.group.name));
                    }

                    bootstrap.Modal.getInstance(document.getElementById('newGroupModal')).hide();
                    if (window.showToast) showToast(res.message, 'success');
                }).fail(function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not create the group.';
                    $('#newGroupError').text(msg);
                    $input.addClass('is-invalid').focus();
                    if (window.showToast) showToast(msg, 'error');
                }).always(function () {
                    $btn.prop('disabled', false);
                    $btn.find('.lk-group-save-label').removeClass('d-none');
                    $btn.find('.lk-group-save-busy').addClass('d-none');
                });
            });
        },

        typeLabel: function (type) {
            var $item = $('.palette-item[data-type="' + type + '"]');
            return $item.length ? $item.data('label') : type;
        },

        /* ---------- boot ---------- */

        renderBoot: function () {
            var schema = this.boot.schema || { groups: [], elements: [] };
            var self = this;
            var byGroup = {};

            (schema.elements || []).forEach(function (el) {
                (byGroup[el.group_id || ''] = byGroup[el.group_id || ''] || []).push(el);
            });
            Object.keys(byGroup).forEach(function (key) {
                byGroup[key].sort(function (a, b) { return (a.order || 0) - (b.order || 0); });
            });

            var items = [];
            (schema.groups || []).forEach(function (g) { items.push({ order: g.order || 0, group: g }); });
            (byGroup[''] || []).forEach(function (el) { items.push({ order: el.order || 0, element: el }); });
            items.sort(function (a, b) { return a.order - b.order; });

            items.forEach(function (item) {
                if (item.group) {
                    Store.addGroup({ id: item.group.id, label: item.group.label || '', visible_when: item.group.visible_when || null });
                    var $section = self.buildGroupNode(item.group.id);
                    self.$canvas.append($section);
                    self.initSortable($section.find('.group-drop-zone')[0]);
                    (byGroup[item.group.id] || []).forEach(function (el) {
                        Store.addElement(self.bootElementConfig(el));
                        $section.find('.group-drop-zone').append(self.buildRowNode(el.id));
                    });
                } else {
                    Store.addElement(self.bootElementConfig(item.element));
                    self.$canvas.append(self.buildRowNode(item.element.id));
                }
            });

            this.refreshEmptyState();
        },

        bootElementConfig: function (el) {
            return {
                id: el.id, kind: el.kind || 'field', type: el.type, label: el.label || '',
                text: el.text || '', placeholder: el.placeholder || '',
                mandatory: !!el.mandatory, values: el.values || [], min: el.min || null, max: el.max || null,
                min_days: el.min_days || null, visible_when: el.visible_when || null,
                user_source: el.user_source || 'all', user_ids: el.user_ids || []
            };
        },

        /* ---------- palette ---------- */

        newElementConfig: function (paletteKind, type) {
            if (paletteKind === 'description') {
                return { kind: 'description', text: '', visible_when: null };
            }
            var config = { kind: 'field', type: type, label: '', placeholder: '', mandatory: false,
                           values: [], min: null, max: null, min_days: null, visible_when: null,
                           user_source: 'all', user_ids: [] };
            if (TYPES_WITH_VALUES.indexOf(type) !== -1) {
                config.values = type === 'multi-select' ? [{ group: '', options: [''] }] : [''];
            }
            return config;
        },

        bindPalette: function () {
            var self = this;

            // Click to append (after the current selection when possible).
            $(document).on('click', '.palette-item', function () {
                var palette = $(this).data('palette');
                if (palette === 'group') {
                    self.addGroupSection();
                    return;
                }
                var id = Store.addElement(self.newElementConfig(palette, $(this).data('type')));
                var $row = self.buildRowNode(id);
                self.insertNearSelection($row);
                self.refreshEmptyState();
                Panel.show('element', id);
                $(document).trigger('builder:changed');
            });

            // Drag from palette into the canvas.
            $('.palette-list').each(function () {
                if (typeof Sortable === 'undefined') return;
                Sortable.create(this, {
                    group: { name: 'builder-cards', pull: 'clone', put: false },
                    sort: false,
                    animation: 150
                });
            });
        },

        insertNearSelection: function ($node) {
            if (Panel.selectedKind === 'element' && Panel.selectedId) {
                var $selected = $('.pv-row[data-el-id="' + Panel.selectedId + '"]');
                if ($selected.length) { $selected.after($node); return; }
            }
            if (Panel.selectedKind === 'group' && Panel.selectedId) {
                var $group = $('.pv-group[data-group-id="' + Panel.selectedId + '"]');
                if ($group.length) { $group.find('.group-drop-zone').append($node); return; }
            }
            this.$canvas.append($node);
        },

        addGroupSection: function () {
            var id = Store.addGroup({ label: '', visible_when: null });
            var $section = this.buildGroupNode(id);
            this.$canvas.append($section);
            this.initSortable($section.find('.group-drop-zone')[0]);
            this.refreshEmptyState();
            Panel.show('group', id);
        },

        /* ---------- preview nodes ---------- */

        buildRowNode: function (id) {
            var $row = $(
                '<div class="pv-row" data-el-id="' + esc(id) + '">' +
                '  <div class="pv-actions">' +
                '    <button type="button" class="pv-copy" title="Duplicate"><i class="mdi mdi-content-copy"></i></button>' +
                '    <button type="button" class="pv-delete" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>' +
                '  </div>' +
                '  <div class="pv-content"></div>' +
                '  <span class="pv-cond-flag d-none"><i class="mdi mdi-eye-settings-outline"></i></span>' +
                '</div>'
            );
            this.refreshRowNode(id, $row);
            return $row;
        },

        refreshRowNode: function (id, $row) {
            $row = $row || $('.pv-row[data-el-id="' + id + '"]');
            var element = Store.elements[id];
            if (!$row.length || !element) return;

            var html;
            if (element.kind === 'description') {
                html = '<span class="badge bg-info pv-badge mb-1">Description</span>' +
                       '<div class="pv-desc-text">' + (element.text ? esc(element.text) : '<span class="untitled">Description text…</span>') + '</div>';
            } else {
                var label = element.label
                    ? esc(element.label)
                    : '<span class="untitled">' + esc(this.typeLabel(element.type)) + '</span>';
                html = '<div class="pv-flex">' +
                       '  <span class="pv-label">' + label + (element.mandatory ? ' <span class="req">*</span>' : '') + '</span>' +
                       '  <span class="pv-placeholder">' + esc(element.placeholder || PLACEHOLDERS[element.type] || 'Enter') + '</span>' +
                       '</div>';
            }
            $row.find('.pv-content').html(html);
            $row.find('.pv-cond-flag').toggleClass('d-none', conditionCount(element.visible_when) === 0);
        },

        buildGroupNode: function (id) {
            var $section = $(
                '<div class="pv-group" data-group-id="' + esc(id) + '">' +
                '  <div class="pv-actions">' +
                '    <button type="button" class="pv-delete" title="Delete group"><i class="mdi mdi-trash-can-outline"></i></button>' +
                '  </div>' +
                '  <div class="pv-group-header"></div>' +
                '  <div class="group-drop-zone"></div>' +
                '  <span class="pv-cond-flag d-none"><i class="mdi mdi-eye-settings-outline"></i></span>' +
                '</div>'
            );
            this.refreshGroupNode(id, $section);
            return $section;
        },

        refreshGroupNode: function (id, $section) {
            $section = $section || $('.pv-group[data-group-id="' + id + '"]');
            var group = Store.groups[id];
            if (!$section.length || !group) return;
            $section.find('.pv-group-header').html(
                group.label ? esc(group.label) : '<span class="untitled">Group name…</span>'
            );
            $section.find('.pv-cond-flag').toggleClass('d-none', conditionCount(group.visible_when) === 0);
        },

        /* ---------- canvas events ---------- */

        bindCanvas: function () {
            var self = this;

            $(document).on('click', '#builder-canvas .pv-row', function (e) {
                e.stopPropagation();
                Panel.show('element', $(this).attr('data-el-id'));
            });
            $(document).on('click', '#builder-canvas .pv-group', function (e) {
                if ($(e.target).closest('.pv-row').length) return;
                e.stopPropagation();
                Panel.show('group', $(this).attr('data-group-id'));
            });

            $(document).on('click', '#builder-canvas .pv-delete', function (e) {
                e.stopPropagation();
                var $row = $(this).closest('.pv-row');
                if ($row.length) {
                    delete Store.elements[$row.attr('data-el-id')];
                    $row.remove();
                } else {
                    var $section = $(this).closest('.pv-group');
                    var groupId = $section.attr('data-group-id');
                    // Fields inside move back to the top level.
                    $section.find('.group-drop-zone .pv-row').insertBefore($section);
                    delete Store.groups[groupId];
                    $section.remove();
                }
                Panel.clear();
                self.refreshEmptyState();
                $(document).trigger('builder:changed');
            });

            $(document).on('click', '#builder-canvas .pv-copy', function (e) {
                e.stopPropagation();
                var $row = $(this).closest('.pv-row');
                var source = Store.elements[$row.attr('data-el-id')];
                var copy = JSON.parse(JSON.stringify(source));
                delete copy.id;
                var id = Store.addElement(copy);
                $row.after(self.buildRowNode(id));
                Panel.show('element', id);
                $(document).trigger('builder:changed');
            });
        },

        initSortable: function (container) {
            if (!container || typeof Sortable === 'undefined') return;
            var self = this;
            Sortable.create(container, {
                group: 'builder-cards',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onMove: function (evt) {
                    // Group sections stay top-level; palette group items too.
                    var $dragged = $(evt.dragged);
                    var intoGroupZone = $(evt.to).hasClass('group-drop-zone');
                    if (intoGroupZone && ($dragged.hasClass('pv-group') || $dragged.data('palette') === 'group')) return false;
                    return true;
                },
                onAdd: function (evt) {
                    var $item = $(evt.item);
                    if ($item.hasClass('palette-item')) {
                        // Dropped from the palette: convert the clone into a real node.
                        var palette = $item.data('palette');
                        if (palette === 'group') {
                            var groupId = Store.addGroup({ label: '', visible_when: null });
                            var $section = self.buildGroupNode(groupId);
                            $item.replaceWith($section);
                            self.initSortable($section.find('.group-drop-zone')[0]);
                            Panel.show('group', groupId);
                        } else {
                            var id = Store.addElement(self.newElementConfig(palette, $item.data('type')));
                            $item.replaceWith(self.buildRowNode(id));
                            Panel.show('element', id);
                        }
                        self.refreshEmptyState();
                        $(document).trigger('builder:changed');
                    }
                }
            });
        },

        refreshEmptyState: function () {
            var hasNodes = this.$canvas.find('.pv-row, .pv-group').length > 0;
            $('#canvas-empty-state').html(hasNodes
                ? '<i class="mdi mdi-cursor-move me-1"></i>Click or drag widgets from the left'
                : '<i class="mdi mdi-gesture-tap-button me-1"></i>Your form is empty — click or drag widgets from the left to start');
        },

        /* ---------- reads used across the app ---------- */

        // Flat list of element configs in DOM order (conditions, record
        // columns, process permission tables all read this). group_id is
        // derived from the DOM — membership lives in which group section
        // the row currently sits in, not in the Store.
        collectElements: function () {
            var elements = [];
            $('#builder-canvas .pv-row').each(function () {
                var element = Store.elements[$(this).attr('data-el-id')];
                if (!element) return;
                var copy = $.extend({}, element);
                var $zone = $(this).closest('.group-drop-zone');
                copy.group_id = $zone.length ? $zone.closest('.pv-group').attr('data-group-id') : null;
                elements.push(copy);
            });
            return elements;
        },

        collectGroups: function () {
            var groups = [];
            $('#builder-canvas .pv-group').each(function () {
                var group = Store.groups[$(this).attr('data-group-id')];
                if (group) groups.push({ id: group.id, label: group.label });
            });
            return groups;
        },

        /* ---------- serialization ---------- */

        serializeSchema: function () {
            var schema = { schema_version: 2, groups: [], elements: [] };
            var order = 0;

            $('#builder-canvas').children('.pv-row, .pv-group').each(function () {
                order += 10;
                var $node = $(this);

                if ($node.hasClass('pv-group')) {
                    var group = Store.groups[$node.attr('data-group-id')];
                    schema.groups.push({
                        id: group.id, label: (group.label || '').trim(),
                        order: order, visible_when: group.visible_when || null
                    });
                    var memberOrder = 0;
                    $node.find('.group-drop-zone .pv-row').each(function () {
                        memberOrder += 10;
                        var element = $.extend({}, Store.elements[$(this).attr('data-el-id')]);
                        element.group_id = group.id;
                        element.order = memberOrder;
                        schema.elements.push(element);
                    });
                } else {
                    var element = $.extend({}, Store.elements[$node.attr('data-el-id')]);
                    element.group_id = null;
                    element.order = order;
                    schema.elements.push(element);
                }
            });

            schema.elements = schema.elements.map(function (element) {
                return {
                    id: element.id, kind: element.kind, type: element.type,
                    label: (element.label || '').trim(), text: (element.text || '').trim(),
                    placeholder: (element.placeholder || '').trim() || null,
                    mandatory: !!element.mandatory, values: element.values || [],
                    min: element.min || null, max: element.max || null,
                    min_days: element.min_days || null,
                    group_id: element.group_id, order: element.order,
                    visible_when: element.visible_when || null,
                    // Person fields: which people the picker offers.
                    user_source: element.type === 'user' ? (element.user_source || 'all') : null,
                    user_ids: element.type === 'user' && element.user_source === 'selected'
                              ? (element.user_ids || []).map(Number) : []
                };
            });

            return schema;
        },

        /* ---------- validation ---------- */

        validateBasicInfo: function () {
            var $name = $('#form_name'), $description = $('#description'), $group = $('#form_group_id');
            $name.toggleClass('is-invalid', !$name.val().trim());
            $description.toggleClass('is-invalid', !$description.val().trim());

            // Every form must be filed somewhere, or it lands nowhere in the
            // grouped list. select2 hides the native control, so the message
            // is a sibling rather than Bootstrap's own invalid-feedback.
            var groupOk = !!($group.length === 0 || $group.val());
            $group.toggleClass('is-invalid', !groupOk);
            $('#form_group_error').toggleClass('d-none', groupOk);

            var ok = !!($name.val().trim() && $description.val().trim()) && groupOk;

            if (!ok) $('.is-invalid').first().focus();
            return ok;
        },

        validateDesign: function () {
            var ok = true;
            var firstBadId = null, firstBadKind = null;
            var fieldCount = 0;

            $('#builder-canvas .pv-row').removeClass('pv-invalid');
            $('#builder-canvas .pv-group').removeClass('pv-invalid');

            $('#builder-canvas .pv-row').each(function () {
                var id = $(this).attr('data-el-id');
                var element = Store.elements[id];
                if (!element) return;
                var bad = false;

                if (element.kind === 'description') {
                    bad = !(element.text || '').trim();
                } else {
                    fieldCount++;
                    if (!(element.label || '').trim()) bad = true;
                    if (TYPES_WITH_VALUES.indexOf(element.type) !== -1) {
                        var optionCount = 0;
                        (element.values || []).forEach(function (value) {
                            if (value && typeof value === 'object') optionCount += (value.options || []).filter(Boolean).length;
                            else if (value) optionCount++;
                        });
                        if (optionCount === 0) bad = true;
                    }
                    if (element.min && element.max && parseInt(element.max, 10) < parseInt(element.min, 10)) bad = true;
                    // "Selected people only" with nobody selected leaves an unusable picker.
                    if (element.type === 'user' && element.user_source === 'selected'
                        && (element.user_ids || []).length === 0) bad = true;
                }

                if (bad) {
                    ok = false;
                    $(this).addClass('pv-invalid');
                    if (!firstBadId) { firstBadId = id; firstBadKind = 'element'; }
                }
            });

            $('#builder-canvas .pv-group').each(function () {
                var id = $(this).attr('data-group-id');
                var group = Store.groups[id];
                if (group && !(group.label || '').trim()) {
                    ok = false;
                    $(this).addClass('pv-invalid');
                    if (!firstBadId) { firstBadId = id; firstBadKind = 'group'; }
                }
            });

            var $error = $('#form-elements-error');
            if (fieldCount === 0) {
                $error.text('Please add at least one form input.').removeClass('d-none');
                ok = false;
            } else if (!ok) {
                $error.text('Highlighted widgets are missing a title or options.').removeClass('d-none');
            } else {
                $error.addClass('d-none');
            }

            if (firstBadId) Panel.show(firstBadKind, firstBadId);
            return ok;
        }
    };

    /* =================================================================
     * Record settings
     * ================================================================= */

    /* Every form produces RECORDS. This only decides whether a record can
       gather more entries later (e.g. several On Site Respond Call reports
       for one service job) — it is a capability, not a mode: nothing else in
       the app behaves differently because of it. */
    /* =================================================================
     * Step 1 — who can submit
     * ================================================================= */

    /* "Who can submit" — a scope dropdown; choosing "Selected Members Only"
       opens a Lark-style picker modal (Specific Users / User Types tabs, search,
       selected pane). Closing the modal with nothing selected reverts the
       scope to Everyone. Committed selections show as pills + an Edit button. */
    var Access = {
        users: [],
        types: [],
        work: { users: {}, types: {} },   // modal working copy: id -> name
        activeTab: 'users',
        committed: false,

        init: function (boot) {
            var self = this;
            this.users = boot.users || [];
            this.types = boot.types || [];

            var access = (boot.settings && boot.settings.access) || {};
            (access.user_ids || []).forEach(function (id) {
                var user = self.users.filter(function (u) { return String(u.id) === String(id); })[0];
                if (user) self.addPill('#submit-users-pills', user.id, user.name);
            });
            (access.user_types || []).forEach(function (id) {
                var type = self.types.filter(function (t) { return String(t.id) === String(id); })[0];
                if (type) self.addPill('#submit-types-pills', type.id, type.name);
            });

            var scope = access.submit_scope || 'everyone';
            if (scope === 'selected' && this.pillTotal() === 0) scope = 'everyone';
            this.setScope(scope);
            this.toggleSummary();

            $('#submit-scope-select').on('change', function () {
                if ($(this).val() === 'selected') {
                    self.openPicker();
                } else {
                    $('#submit-users-pills, #submit-types-pills').empty();
                    self.toggleSummary();
                }
            });
            $(document).on('click', '#access-edit-btn', function () { self.openPicker(); });

            $(document).on('click', '#submit-users-pills .remove-approver, #submit-types-pills .remove-approver', function () {
                $(this).closest('.approver-pill').remove();
                if (self.pillTotal() === 0) {
                    self.setScope('everyone');
                }
                self.toggleSummary();
            });

            /* ----- picker modal events ----- */

            $(document).on('click', '.ap-tab', function () {
                $('.ap-tab').removeClass('active');
                $(this).addClass('active');
                self.activeTab = $(this).data('ap-tab');
                $('#ap-search-input').val('').attr('placeholder',
                    self.activeTab === 'users' ? 'Search for the name of a member' : 'Search for a user type');
                self.renderList();
            });

            $(document).on('input', '#ap-search-input', function () { self.renderList(); });

            $(document).on('click', '.ap-row', function () {
                var tab = $(this).data('tab');
                var id = String($(this).data('id'));
                var name = $(this).data('name');
                if (self.work[tab][id]) {
                    delete self.work[tab][id];
                } else {
                    self.work[tab][id] = name;
                }
                $(this).find('.form-check-input').prop('checked', !!self.work[tab][id]);
                self.renderSelected();
            });

            $(document).on('click', '.ap-selected-remove', function () {
                var tab = $(this).data('tab');
                delete self.work[tab][String($(this).data('id'))];
                self.renderList();
                self.renderSelected();
            });

            $(document).on('click', '#ap-clear', function () {
                self.work = { users: {}, types: {} };
                self.renderList();
                self.renderSelected();
            });

            $(document).on('click', '#ap-ok', function () {
                self.committed = true;
                self.applyWork();
                self.pickerModal().hide();
            });

            var modalEl = document.getElementById('accessPickerModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    // Cancel / X / backdrop: keep any previously committed pills,
                    // but if nothing is selected at all, fall back to Everyone.
                    if (!self.committed && self.pillTotal() === 0) {
                        self.setScope('everyone');
                        self.toggleSummary();
                    }
                });
            }
        },

        pickerModal: function () {
            return window.bootstrap.Modal.getOrCreateInstance(document.getElementById('accessPickerModal'));
        },

        openPicker: function () {
            var self = this;
            this.committed = false;
            this.work = { users: {}, types: {} };
            $('#submit-users-pills .approver-pill').each(function () {
                self.work.users[String($(this).attr('data-id'))] = $(this).data('pill-name');
            });
            $('#submit-types-pills .approver-pill').each(function () {
                self.work.types[String($(this).attr('data-id'))] = $(this).data('pill-name');
            });

            this.activeTab = 'users';
            $('.ap-tab').removeClass('active');
            $('.ap-tab[data-ap-tab="users"]').addClass('active');
            $('#ap-search-input').val('').attr('placeholder', 'Search for the name of a member');
            this.renderList();
            this.renderSelected();
            this.pickerModal().show();
        },

        renderList: function () {
            var self = this;
            var query = ($('#ap-search-input').val() || '').toLowerCase().trim();
            var items = this.activeTab === 'users' ? this.users : this.types;
            var $list = $('#ap-list').empty();

            var shown = 0;
            items.forEach(function (item) {
                if (query && String(item.name).toLowerCase().indexOf(query) === -1) return;
                shown++;
                var checked = !!self.work[self.activeTab][String(item.id)];
                $list.append(
                    '<div class="ap-row" data-tab="' + self.activeTab + '" data-id="' + esc(item.id) + '" data-name="' + esc(item.name) + '">' +
                    '  <input type="checkbox" class="form-check-input"' + (checked ? ' checked' : '') + '>' +
                    '  <span class="ap-row-name">' + esc(item.name) + '</span>' +
                    '</div>'
                );
            });

            if (shown === 0) {
                $list.append('<div class="ap-list-empty">No matching ' + (this.activeTab === 'users' ? 'members' : 'user types') + '</div>');
            }
        },

        renderSelected: function () {
            var self = this;
            var $pane = $('#ap-selected').empty();
            var total = 0;

            var row = function (tab, id, name) {
                total++;
                var tagClass = tab === 'users' ? 'ap-selected-tag--user' : 'ap-selected-tag--type';
                var tagLabel = tab === 'users' ? 'User' : 'Type';
                $pane.append(
                    '<div class="ap-selected-row">' +
                    '  <span class="ap-selected-name">' + esc(name) + '</span>' +
                    '  <span class="d-flex align-items-center gap-2">' +
                    '    <span class="ap-selected-tag ' + tagClass + '">' + tagLabel + '</span>' +
                    '    <i class="mdi mdi-close ap-selected-remove" data-tab="' + tab + '" data-id="' + esc(id) + '"></i>' +
                    '  </span>' +
                    '</div>'
                );
            };

            Object.keys(this.work.users).forEach(function (id) { row('users', id, self.work.users[id]); });
            Object.keys(this.work.types).forEach(function (id) { row('types', id, self.work.types[id]); });

            if (total === 0) {
                $pane.append(
                    '<div class="ap-empty"><i class="mdi mdi-inbox-outline"></i><div>No Data</div></div>'
                );
            }
            $('#ap-count').text(total > 0 ? total : '');
        },

        applyWork: function () {
            var self = this;
            $('#submit-users-pills, #submit-types-pills').empty();
            Object.keys(this.work.users).forEach(function (id) {
                self.addPill('#submit-users-pills', id, self.work.users[id]);
            });
            Object.keys(this.work.types).forEach(function (id) {
                self.addPill('#submit-types-pills', id, self.work.types[id]);
            });

            this.setScope(this.pillTotal() > 0 ? 'selected' : 'everyone');
            this.toggleSummary();
        },

        /**
         * select2 renders its own box, so a bare .val() changes the value the
         * form submits while the user still reads the old label. The namespaced
         * event redraws select2 WITHOUT re-firing our own change handler, which
         * would reopen the picker.
         */
        setScope: function (value) {
            $('#submit-scope-select').val(value).trigger('change.select2');
        },

        toggleSummary: function () {
            var selected = $('#submit-scope-select').val() === 'selected';
            $('#access-summary').toggleClass('d-none', !selected || this.pillTotal() === 0);
        },

        addPill: function (containerSel, id, name) {
            var $container = $(containerSel);
            if ($container.find('.approver-pill[data-id="' + id + '"]').length) return;
            var $pill = $(
                '<span class="badge bg-primary p-2 approver-pill d-flex align-items-center" data-id="' + esc(id) + '">' + esc(name) +
                '  <i class="mdi mdi-close ms-2 remove-approver" style="cursor:pointer;"></i>' +
                '</span>'
            );
            $pill.data('pill-name', name);
            $container.append($pill);
        },

        pillIds: function (containerSel) {
            return $(containerSel + ' .approver-pill').map(function () { return parseInt($(this).attr('data-id'), 10); }).get();
        },

        pillTotal: function () {
            return $('#submit-users-pills .approver-pill, #submit-types-pills .approver-pill').length;
        },

        serialize: function () {
            var scope = $('#submit-scope-select').val() || 'everyone';
            if (scope === 'selected' && this.pillTotal() === 0) scope = 'everyone';
            return {
                submit_scope: scope,
                user_ids: this.pillIds('#submit-users-pills'),
                user_types: this.pillIds('#submit-types-pills')
            };
        }
    };

    /* =================================================================
     * Final submit
     * ================================================================= */

    function bindSubmit() {
        $('#submit-form-btn').on('click', function () {
            if (!Builder.validateBasicInfo()) { Wizard.go(1); return; }
            if (!Builder.validateDesign()) { Wizard.go(2); return; }
            if (window.FormProcessDesigner && !window.FormProcessDesigner.validate()) { Wizard.go(3); return; }

            $('#form_elements_json').val(JSON.stringify(Builder.serializeSchema()));
            // No `record` key: following up on an earlier case is chosen per
            // submission, not configured per form.
            $('#settings_json').val(JSON.stringify({
                access: Access.serialize()
            }));
            $('#process_definition_json').val(JSON.stringify(
                window.FormProcessDesigner ? window.FormProcessDesigner.serialize() : { process_version: 1, nodes: [] }
            ));

            $('#form-metadata').submit();
        });
    }

    /* =================================================================
     * Boot
     * ================================================================= */

    $(function () {
        var boot = window.FormBuilderBoot || {};

        ConditionUI.init();
        ConditionModal.init();
        Panel.init();
        Builder.init(boot);
        Wizard.init();
        bindSubmit();

        $('#phone-form-name').text($('#form_name').val() || 'New Form');

        if (window.FormProcessDesigner) {
            window.FormProcessDesigner.init(boot);
        }
    });

    // Shared API for the process designer.
    window.FormBuilderApi = {
        collectElements: function () { return Builder.collectElements(); },
        collectGroups: function () { return Builder.collectGroups(); },
        openConditionEditor: function (schema, onSave) { ConditionModal.open(schema, null, onSave); },
        submitScopeSummary: function () {
            if ($('#submit-scope-select').val() === 'selected') {
                var members = $('#submit-users-pills .approver-pill').length;
                var types = $('#submit-types-pills .approver-pill').length;
                var parts = [];
                if (members) parts.push(members + ' member' + (members === 1 ? '' : 's'));
                if (types) parts.push(types + ' user type' + (types === 1 ? '' : 's'));
                return parts.length ? parts.join(', ') : 'No one selected';
            }
            return 'All members';
        }
    };

})(jQuery);
