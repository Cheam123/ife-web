/**
 * Process designer — Step 3, Lark-style.
 *
 * Canvas: vertical flow of colored node cards on a grey background
 *   [Submitted by ...] -> [Handler (purple)] -> [Approval (orange)] ->
 *   [CC (blue)] -> conditional branch columns -> [End]
 * Clicking a card opens a right-side settings drawer (Bootstrap offcanvas)
 * with tabs — "Set Handlers/Approvers/Recipients" and "Form Permissions"
 * (Lark-style table: group name cell spans its member rows; Read + Edit/Fill
 * checkbox columns with select-all headers). Drawer edits work on a copy and
 * only apply on Save.
 *
 * Node types (internal type strings are stable — the server engine relies
 * on them): fill = Handler phase (user fills their part, no approval),
 * approval, cc, branch.
 */
(function ($) {
    'use strict';

    var uidCounter = 0;
    function uid(prefix) {
        uidCounter++;
        return prefix + '_' + Date.now().toString(36) + uidCounter;
    }

    function esc(text) {
        return $('<div>').text(text == null ? '' : String(text)).html();
    }

    var NODE_META = {
        fill:     { label: 'Handler',  icon: 'mdi-account-edit-outline',  color: '#7a56d1', role: 'Handler' },
        approval: { label: 'Approval', icon: 'mdi-account-check-outline', color: '#e8871e', role: 'Approver' },
        cc:       { label: 'CC',       icon: 'mdi-email-outline',         color: '#3370ff', role: 'CC' }
    };

    var OPERATOR_TEXT = {
        equals: 'is', not_equals: 'is not', includes: 'includes', not_includes: 'does not include',
        gt: '>', lt: '<', is_empty: 'is empty', is_not_empty: 'is not empty'
    };

    var Designer = {
        nodes: [],
        users: [],
        // Set once the user has tried to leave this step. Until then the canvas
        // stays unmarked — a flow being built should not be red before it has
        // been submitted even once.
        validated: false,

        init: function (boot) {
            this.users = boot.users || [];
            this.nodes = (boot.process && boot.process.nodes) ? JSON.parse(JSON.stringify(boot.process.nodes)) : [];
            this.bindEvents();
            Drawer.init();
            this.render();
            Canvas.init();
        },

        /* ---------- state helpers ---------- */

        findNode: function (id, list) {
            list = list || this.nodes;
            for (var i = 0; i < list.length; i++) {
                if (list[i].id === id) return { list: list, index: i, node: list[i] };
                if (list[i].type === 'branch') {
                    for (var b = 0; b < (list[i].branches || []).length; b++) {
                        var found = this.findNode(id, list[i].branches[b].nodes || []);
                        if (found) return found;
                    }
                }
            }
            return null;
        },

        findBranch: function (branchId) {
            for (var i = 0; i < this.nodes.length; i++) {
                var node = this.nodes[i];
                if (node.type !== 'branch') continue;
                for (var b = 0; b < (node.branches || []).length; b++) {
                    if (node.branches[b].id === branchId) {
                        return { branchNode: node, branch: node.branches[b], index: b };
                    }
                }
            }
            return null;
        },

        newNode: function (type) {
            if (type === 'approval') {
                return { id: uid('nd'), type: 'approval', name: 'Approval', approver_ids: [], approval_mode: 'any',
                         field_permissions: { default: 'read', overrides: {} } };
            }
            if (type === 'fill') {
                return { id: uid('nd'), type: 'fill', name: 'Handler', assignee_ids: [],
                         field_permissions: { default: 'read', overrides: {} } };
            }
            if (type === 'cc') {
                return { id: uid('nd'), type: 'cc', name: 'CC', user_ids: [],
                         field_permissions: { default: 'read', overrides: {} } };
            }
            return { id: uid('nd'), type: 'branch',
                     branches: [
                         { id: uid('br'), name: 'Conditional branch 1', when: { logic: 'or', groups: [] }, nodes: [] },
                         { id: uid('br'), name: 'Else', when: null, nodes: [] }
                     ] };
        },

        userName: function (id) {
            var user = this.users.filter(function (u) { return String(u.id) === String(id); })[0];
            return user ? user.name : ('User #' + id);
        },

        namesLine: function (ids) {
            var self = this;
            ids = ids || [];
            if (ids.length === 0) return '<span class="lk-missing">Click to set</span>';
            var names = ids.map(function (id) { return esc(self.userName(id)); });
            var shown = names.slice(0, 3).join(', ');
            if (names.length > 3) shown += ' +' + (names.length - 3);
            return shown;
        },

        /* Who a step goes to, as shown on its card. */
        assigneeLine: function (node) {
            if (node.type === 'fill' && node.assignee_mode === 'runtime') {
                return '<span class="lk-runtime-tag"><i class="mdi mdi-account-question-outline"></i> Picked when the flow gets here</span>';
            }
            if (node.type === 'fill' && node.assignee_mode === 'field') {
                var elements = window.FormBuilderApi ? window.FormBuilderApi.collectElements() : [];
                var field    = elements.filter(function (el) { return el.id === node.assignee_field; })[0];
                return field
                    ? '<span class="lk-runtime-tag"><i class="mdi mdi-account-arrow-right-outline"></i> From &ldquo;' + esc(field.label || 'Person field') + '&rdquo;</span>'
                    : '<span class="lk-missing">Click to pick a Person field</span>';
            }

            return this.namesLine(this.participantIds(node));
        },

        participantIds: function (node) {
            if (node.type === 'approval') return node.approver_ids || [];
            if (node.type === 'fill') return node.assignee_ids || [];
            return node.user_ids || [];
        },

        /* ---------- rendering ---------- */

        render: function () {
            var $chain = $('#process-chain').empty();

            // Submit node.
            var submitSummary = (window.FormBuilderApi && window.FormBuilderApi.submitScopeSummary)
                ? window.FormBuilderApi.submitScopeSummary() : 'All members';
            $chain.append(
                '<div class="lk-node lk-node-submit">' +
                '  <div class="lk-node-head" style="background:#f3f4f6;color:#374151;">Submit</div>' +
                '  <div class="lk-node-body"><span class="lk-body-label">Submitted by:</span> ' + esc(submitSummary) + '</div>' +
                '</div>'
            );
            $chain.append(this.connector(null, 0, true));

            for (var i = 0; i < this.nodes.length; i++) {
                $chain.append(this.renderNode(this.nodes[i]));
                $chain.append(this.connector(null, i + 1, true));
            }

            $chain.append('<div class="lk-end"><span>End</span></div>');

            // The chain was just rebuilt, so any error marks went with it.
            this.refreshMarks();
        },

        connector: function (parentBranchId, index, allowBranch) {
            var branchAttr = parentBranchId ? ' data-branch-id="' + esc(parentBranchId) + '"' : '';
            return (
                '<div class="lk-connector process-insert-point"' + branchAttr + ' data-index="' + index + '">' +
                '  <div class="lk-line"></div>' +
                '  <div class="dropdown">' +
                // data-bs-display="static": Popper misplaces menus inside the scaled canvas.
                '    <button type="button" class="lk-add-btn" data-bs-toggle="dropdown" data-bs-display="static" title="Add step"><i class="mdi mdi-plus"></i></button>' +
                '    <ul class="dropdown-menu shadow-sm lk-add-menu">' +
                '      <li><a class="dropdown-item process-add" href="#" data-type="fill"><span class="lk-menu-dot" style="background:#7a56d1;"></span>Handler<span class="lk-menu-hint">User fills their part of the form — no approval</span></a></li>' +
                '      <li><a class="dropdown-item process-add" href="#" data-type="approval"><span class="lk-menu-dot" style="background:#e8871e;"></span>Approval<span class="lk-menu-hint">Approve or reject the submission</span></a></li>' +
                '      <li><a class="dropdown-item process-add" href="#" data-type="cc"><span class="lk-menu-dot" style="background:#3370ff;"></span>CC<span class="lk-menu-hint">Give someone read access</span></a></li>' +
                (allowBranch
                ? '      <li><a class="dropdown-item process-add" href="#" data-type="branch"><span class="lk-menu-dot" style="background:#22a06b;"></span>Conditional Branch<span class="lk-menu-hint">Different paths based on answers</span></a></li>'
                : '') +
                '    </ul>' +
                '  </div>' +
                '  <div class="lk-line lk-line-arrow"></div>' +
                '</div>'
            );
        },

        renderNode: function (node) {
            if (node.type === 'branch') return this.renderBranchNode(node);

            var meta = NODE_META[node.type];
            return $(
                '<div class="lk-node process-node" data-node-id="' + esc(node.id) + '">' +
                '  <div class="lk-node-head" style="background:' + meta.color + ';">' +
                '    <span class="lk-head-title">' + esc(node.name || meta.label) + '</span>' +
                '    <span class="lk-head-actions">' +
                '      <button type="button" class="lk-mini process-move-up" data-node-id="' + esc(node.id) + '" title="Move up"><i class="mdi mdi-arrow-up"></i></button>' +
                '      <button type="button" class="lk-mini process-move-down" data-node-id="' + esc(node.id) + '" title="Move down"><i class="mdi mdi-arrow-down"></i></button>' +
                '      <button type="button" class="lk-mini process-remove" data-node-id="' + esc(node.id) + '" title="Delete"><i class="mdi mdi-close"></i></button>' +
                '    </span>' +
                '  </div>' +
                '  <div class="lk-node-body">' +
                '    <span class="lk-body-label">' + meta.role + ':</span> ' + this.assigneeLine(node) +
                '    <i class="mdi mdi-chevron-right lk-chevron"></i>' +
                '  </div>' +
                '</div>'
            );
        },

        conditionSummary: function (branch) {
            var groups = (branch.when && branch.when.groups) || [];
            var conditions = groups.length ? (groups[0].conditions || []) : [];
            if (conditions.length === 0) return '<span class="lk-missing">Click to set condition</span>';

            var elements = window.FormBuilderApi ? window.FormBuilderApi.collectElements() : [];
            var label = function (fieldId) {
                var el = elements.filter(function (e) { return e.id === fieldId; })[0];
                return el ? (el.label || '(untitled)') : fieldId;
            };

            var first = conditions[0];
            var text = 'When ' + label(first.field) + ' ' + (OPERATOR_TEXT[first.operator] || first.operator);
            if (first.value !== null && first.value !== undefined && ['is_empty', 'is_not_empty'].indexOf(first.operator) === -1) {
                text += ' "' + first.value + '"';
            }
            var total = groups.reduce(function (n, g) { return n + (g.conditions || []).length; }, 0);
            if (total > 1) text += ' +' + (total - 1) + ' more';
            return esc(text);
        },

        renderBranchNode: function (node) {
            var $block = $(
                '<div class="lk-branch process-node" data-node-id="' + esc(node.id) + '">' +
                '  <div class="lk-branch-top">' +
                '    <button type="button" class="lk-branch-pill process-add-branch" data-node-id="' + esc(node.id) + '">' +
                '      <i class="mdi mdi-plus me-1"></i>Add Conditional Branch' +
                '    </button>' +
                '    <span class="lk-head-actions lk-branch-actions">' +
                '      <button type="button" class="lk-mini-dark process-move-up" data-node-id="' + esc(node.id) + '" title="Move up"><i class="mdi mdi-arrow-up"></i></button>' +
                '      <button type="button" class="lk-mini-dark process-move-down" data-node-id="' + esc(node.id) + '" title="Move down"><i class="mdi mdi-arrow-down"></i></button>' +
                '      <button type="button" class="lk-mini-dark process-remove" data-node-id="' + esc(node.id) + '" title="Delete branch block"><i class="mdi mdi-close"></i></button>' +
                '    </span>' +
                '  </div>' +
                '  <div class="lk-branch-center-drop"></div>' +
                '  <div class="lk-branch-columns"></div>' +
                '  <div class="lk-branch-center-drop"></div>' +
                '</div>'
            );

            var $columns = $block.find('.lk-branch-columns');
            var branches = node.branches || [];
            for (var b = 0; b < branches.length; b++) {
                $columns.append(this.renderBranchColumn(node, branches[b], b === branches.length - 1, b, branches.length));
            }
            return $block;
        },

        /* Repeat scope for a conditional arm: none, repeat the arm's own
           steps, or rewind to an earlier step in the branch's own flow
           (e.g. back to "Assign Technician" so a new person is picked). */
        loopControlHtml: function (branchNode, branch) {
            var found   = this.findNode(branchNode.id);
            var options = '<option value="">Don\'t repeat</option>' +
                          '<option value="loop"' + (branch.loop ? ' selected' : '') + '>Repeat this path</option>';

            // Only steps ABOVE this branch in the same flow can be rewound to.
            var earlier = 0;
            if (found) {
                for (var i = 0; i < found.index; i++) {
                    var step = found.list[i];
                    if (step.type === 'branch') continue; // cannot rewind into a branch block
                    earlier++;
                    options += '<option value="to:' + esc(step.id) + '"' +
                               (branch.loop_to === step.id ? ' selected' : '') + '>' +
                               'Repeat from: ' + esc(step.name || NODE_META[step.type].label) + '</option>';
                }
            }
            if (earlier === 0) {
                // Say why the option is missing instead of silently omitting it.
                options += '<option value="" disabled>Repeat from… (add a step above this branch first)</option>';
            }

            return '<div class="lk-cond-loop' + (branch.loop || branch.loop_to ? ' lk-loop-on' : '') + '"' +
                   ' title="Repeat while this condition still matches — e.g. keep going while Status is Pending.">' +
                   '<i class="mdi mdi-repeat me-1"></i>' +
                   '<select class="lk-loop-select branch-loop-select" data-branch-id="' + esc(branch.id) + '">' + options + '</select>' +
                   '</div>';
        },

        loopTailHtml: function (branchNode, branch) {
            if (branch.loop) {
                return '<span class="lk-loop-tail"><i class="mdi mdi-repeat"></i> loops back</span>';
            }
            if (!branch.loop_to) return '';

            var target = this.findNode(branch.loop_to);
            var name   = target ? (target.node.name || NODE_META[target.node.type].label) : 'earlier step';
            return '<span class="lk-loop-tail"><i class="mdi mdi-repeat"></i> back to ' + esc(name) + '</span>';
        },

        renderBranchColumn: function (branchNode, branch, isDefault, index, total) {
            var headHtml;
            if (isDefault) {
                headHtml =
                    '<div class="lk-cond-card lk-cond-default">' +
                    '  <div class="lk-cond-head"><span>Else</span><span class="lk-priority">Priority ' + (index + 1) + '</span></div>' +
                    '  <div class="lk-cond-body text-muted">All other cases</div>' +
                    '</div>';
            } else {
                headHtml =
                    '<div class="lk-cond-card branch-condition-btn" data-branch-id="' + esc(branch.id) + '">' +
                    '  <div class="lk-cond-head">' +
                    '    <span class="lk-cond-name" title="Double-click to rename">' + esc(branch.name || ('Conditional branch ' + (index + 1))) + '</span>' +
                    '    <span class="lk-priority">Priority ' + (index + 1) + '</span>' +
                    '    <button type="button" class="lk-mini-grey branch-remove" data-branch-id="' + esc(branch.id) + '" title="Remove branch"><i class="mdi mdi-close"></i></button>' +
                    '  </div>' +
                    '  <div class="lk-cond-body">' + this.conditionSummary(branch) + ' <i class="mdi mdi-chevron-right lk-chevron"></i></div>' +
                    this.loopControlHtml(branchNode, branch) +
                    '</div>';
            }

            var railMods = (index === 0 ? ' lk-rail-first' : '') + (index === total - 1 ? ' lk-rail-last' : '');

            var $column = $(
                '<div class="lk-branch-column" data-branch-id="' + esc(branch.id) + '">' +
                '<div class="lk-rail lk-rail-top' + railMods + '"></div>' +
                headHtml +
                '<div class="lk-branch-chain"></div>' +
                '<div class="lk-col-tail">' + (isDefault ? '' : this.loopTailHtml(branchNode, branch)) + '</div>' +
                '<div class="lk-rail lk-rail-bottom' + railMods + '"></div>' +
                '</div>'
            );

            var $chain = $column.find('.lk-branch-chain');
            $chain.append(this.connector(branch.id, 0, false));
            var nodes = branch.nodes || [];
            for (var i = 0; i < nodes.length; i++) {
                $chain.append(this.renderNode(nodes[i]));
                $chain.append(this.connector(branch.id, i + 1, false));
            }

            return $column;
        },

        /* ---------- events ---------- */

        bindEvents: function () {
            var self = this;

            $(document).on('click', '.process-add', function (e) {
                e.preventDefault();
                var type = $(this).data('type');
                var $point = $(this).closest('.process-insert-point');
                var index = parseInt($point.attr('data-index'), 10) || 0;
                var branchId = $point.attr('data-branch-id');

                var node = self.newNode(type);
                if (branchId) {
                    var found = self.findBranch(branchId);
                    if (found) (found.branch.nodes = found.branch.nodes || []).splice(index, 0, node);
                } else {
                    self.nodes.splice(index, 0, node);
                }
                self.render();
                if (type !== 'branch') Drawer.open(node.id);
            });

            // Open the settings drawer when a step card is clicked.
            $(document).on('click', '.lk-node.process-node', function (e) {
                if ($(e.target).closest('button').length) return;
                Drawer.open($(this).attr('data-node-id'));
            });

            $(document).on('click', '.process-remove', function (e) {
                e.stopPropagation();
                var found = self.findNode($(this).data('node-id'));
                if (found) found.list.splice(found.index, 1);
                self.render();
            });

            $(document).on('click', '.process-move-up, .process-move-down', function (e) {
                e.stopPropagation();
                var up = $(this).hasClass('process-move-up');
                var found = self.findNode($(this).data('node-id'));
                if (!found) return;
                var to = found.index + (up ? -1 : 1);
                if (to < 0 || to >= found.list.length) return;
                found.list.splice(to, 0, found.list.splice(found.index, 1)[0]);
                self.render();
            });

            /* branches */

            $(document).on('click', '.process-add-branch', function (e) {
                e.stopPropagation();
                var found = self.findNode($(this).data('node-id'));
                if (!found || found.node.type !== 'branch') return;
                found.node.branches.splice(found.node.branches.length - 1, 0,
                    { id: uid('br'), name: 'Conditional branch ' + found.node.branches.length, when: { logic: 'or', groups: [] }, nodes: [] });
                self.render();
            });

            $(document).on('click', '.branch-remove', function (e) {
                e.stopPropagation();
                var found = self.findBranch($(this).data('branch-id'));
                if (!found) return;
                found.branchNode.branches.splice(found.index, 1);
                self.render();
            });

            $(document).on('dblclick', '.lk-cond-name', function (e) {
                e.stopPropagation();
                var $card = $(this).closest('.branch-condition-btn');
                var found = self.findBranch($card.data('branch-id'));
                if (!found) return;
                var name = prompt('Branch name', found.branch.name || '');
                if (name !== null) {
                    found.branch.name = name.trim() || found.branch.name;
                    self.render();
                }
            });

            // The repeat selector sits inside the condition card — stop
            // propagation so using it doesn't open the condition editor.
            $(document).on('click', '.lk-cond-loop', function (e) {
                e.stopPropagation();
            });

            $(document).on('change', '.branch-loop-select', function (e) {
                e.stopPropagation();
                var found = self.findBranch($(this).data('branch-id'));
                if (!found) return;

                var value = $(this).val() || '';
                delete found.branch.loop;
                delete found.branch.loop_to;
                if (value === 'loop') {
                    found.branch.loop = true;
                } else if (value.indexOf('to:') === 0) {
                    found.branch.loop_to = value.slice(3);
                }
                self.render();
            });

            $(document).on('click', '.branch-condition-btn', function (e) {
                if ($(e.target).closest('button').length) return;
                var found = self.findBranch($(this).data('branch-id'));
                if (!found) return;
                window.FormBuilderApi.openConditionEditor(found.branch.when, function (schema) {
                    found.branch.when = schema || { logic: 'or', groups: [] };
                    self.render();
                });
            });
        },

        /* ---------- public API ---------- */

        refreshFieldsFromBuilder: function () {
            Drawer.close();
            this.render(); // refresh names/summaries against the live builder state
            Canvas.reset(); // the pane was hidden until now; center the flow
        },

        /* Loop-arm helpers (mirror FormProcessService validation) */

        armHasBlockingNode: function (nodes) {
            var self = this;
            return (nodes || []).some(function (node) {
                if (node.type === 'approval' || node.type === 'fill') return true;
                if (node.type === 'branch') {
                    return (node.branches || []).some(function (b) { return self.armHasBlockingNode(b.nodes); });
                }
                return false;
            });
        },

        indexInList: function (list, nodeId) {
            for (var i = 0; i < list.length; i++) {
                if (list[i].id === nodeId) return i;
            }
            return -1;
        },

        // A loop can only end if some Handler in the repeated range can fill a
        // field the condition checks (group "Fill" overrides expand to members).
        rangeCanChangeCondition: function (range, when) {
            var fieldIds = [];
            ((when || {}).groups || []).forEach(function (g) {
                (g.conditions || []).forEach(function (c) { if (c.field) fieldIds.push(c.field); });
            });
            if (fieldIds.length === 0) return true;

            var elements = window.FormBuilderApi ? window.FormBuilderApi.collectElements() : [];
            var groupOf = {};
            elements.forEach(function (el) { if (el.group_id) groupOf[el.id] = el.group_id; });

            var self = this;
            var check = function (nodes) {
                return (nodes || []).some(function (node) {
                    if (node.type === 'branch') {
                        return (node.branches || []).some(function (b) { return check(b.nodes); });
                    }
                    if (node.type !== 'fill') return false;
                    var overrides = (node.field_permissions || {}).overrides || {};
                    return fieldIds.some(function (id) {
                        return overrides[id] === 'edit' || (groupOf[id] && overrides[groupOf[id]] === 'edit');
                    });
                });
            };
            return check(range);
        },

        /**
         * Walks the flow and returns every problem found, each attributed to the
         * node it came from — so the offending card can be outlined on the canvas
         * instead of leaving the user to match a sentence against a diagram.
         *
         * Pure: it reports, it does not touch the DOM.
         */
        collectProblems: function () {
            var errors = [];        // messages, in the order found
            var badNodes = {};      // node id -> first message for that node
            var self = this;

            // Record a problem against the card it belongs to.
            var fail = function (nodeId, message) {
                errors.push(message);
                if (nodeId && !badNodes[nodeId]) badNodes[nodeId] = message;
            };

            var walk = function (nodes, depth) {
                nodes.forEach(function (node) {
                    if (node.type === 'approval') {
                        if (!(node.name || '').trim()) fail(node.id, 'Every approval step needs a name.');
                        if (!node.approver_ids || node.approver_ids.length === 0) fail(node.id, 'Approval step "' + (node.name || '?') + '" needs at least one approver.');
                    } else if (node.type === 'fill') {
                        if (!(node.name || '').trim()) fail(node.id, 'Every handler step needs a name.');
                        var assigneeMode = node.assignee_mode || 'fixed';
                        if (assigneeMode === 'fixed' && (!node.assignee_ids || node.assignee_ids.length === 0)) fail(node.id, 'Handler step "' + (node.name || '?') + '" needs at least one handler.');
                        if (assigneeMode === 'field' && !node.assignee_field) fail(node.id, 'Handler step "' + (node.name || '?') + '" needs a Person field to take its handler from.');
                        if (assigneeMode === 'field' && node.assignee_field) {
                            // Mirrors FormProcessService::validateDefinition — an
                            // optional person field can reach the step empty.
                            var src = (window.FormBuilderApi ? window.FormBuilderApi.collectElements() : [])
                                        .filter(function (el) { return el.id === node.assignee_field; })[0];
                            if (src && !src.mandatory) {
                                fail(node.id, 'Handler step "' + (node.name || '?') + '" takes its handler from "' +
                                              (src.label || node.assignee_field) +
                                              '". Set that field to Required in Form Design so it must always be answered.');
                            }
                        }
                        var overrides = (node.field_permissions || {}).overrides || {};
                        var hasEdit = Object.keys(overrides).some(function (k) { return overrides[k] === 'edit'; });
                        if (!hasEdit) fail(node.id, 'Handler step "' + (node.name || '?') + '" needs at least one field marked as "Fill" in Form Permissions.');
                    } else if (node.type === 'cc') {
                        if (!node.user_ids || node.user_ids.length === 0) fail(node.id, 'CC step "' + (node.name || '?') + '" needs at least one recipient.');
                    } else if (node.type === 'branch') {
                        (node.branches || []).forEach(function (branch, i) {
                            var isLast = i === node.branches.length - 1;
                            if (!isLast) {
                                var count = branch.when && branch.when.groups ? branch.when.groups.reduce(function (n, g) { return n + (g.conditions || []).length; }, 0) : 0;
                                if (count === 0) fail(node.id, 'Branch "' + (branch.name || '?') + '" needs a condition.');
                            }
                            if (!isLast && branch.loop_to) {
                                // Repeat from an earlier step: the rewound range
                                // must contain something that blocks and something
                                // that can change the condition.
                                var owner  = self.findNode(node.id);
                                var target = owner ? self.indexInList(owner.list, branch.loop_to) : -1;
                                if (!owner || target === -1 || target >= owner.index) {
                                    fail(node.id, 'Branch "' + (branch.name || '?') + '" must repeat from a step that comes before it.');
                                } else {
                                    var range = owner.list.slice(target, owner.index);
                                    if (!self.armHasBlockingNode(range)) {
                                        fail(node.id, 'Branch "' + (branch.name || '?') + '" repeats a range with no Handler or Approval step.');
                                    } else if (!self.rangeCanChangeCondition(range, branch.when)) {
                                        fail(node.id, 'Branch "' + (branch.name || '?') + '" repeats steps that cannot fill the field(s) its condition checks — otherwise the loop can never end.');
                                    }
                                }
                            } else if (!isLast && branch.loop) {
                                if (!self.armHasBlockingNode(branch.nodes)) {
                                    fail(node.id, 'Loop branch "' + (branch.name || '?') + '" needs at least one Handler or Approval step inside it.');
                                } else if (!self.rangeCanChangeCondition(branch.nodes, branch.when)) {
                                    fail(node.id, 'Loop branch "' + (branch.name || '?') + '" needs a Handler step that can fill the field(s) its condition checks — otherwise the loop can never end.');
                                }
                            }
                            walk(branch.nodes || [], depth + 1);
                        });
                    }
                });
            };
            walk(this.nodes, 0);

            return { errors: errors, badNodes: badNodes };
        },

        /** True when the flow is valid. Paints the canvas either way. */
        validate: function () {
            var found = this.collectProblems();

            // From here on the canvas keeps itself marked up as the user edits.
            this.validated = true;
            this.markInvalidNodes(found.badNodes);

            var $error = $('#process-error');
            if (found.errors.length) {
                // Text on the span, never the wrapper: the wrapper holds the icon.
                $('#process-error-text').text(
                    found.errors[0] + (found.errors.length > 1 ? ' (+' + (found.errors.length - 1) + ' more)' : '')
                );
                $error.removeClass('d-none');
                this.scrollToFirstInvalid();
                return false;
            }
            $error.addClass('d-none');
            return true;
        },

        /**
         * Re-applies the marks after a re-render, so fixing one step clears its
         * red immediately instead of waiting for the next Next.
         *
         * Silent until the user has actually tried to move on — a flow being
         * built for the first time should not be covered in red before it has
         * been submitted once.
         */
        refreshMarks: function () {
            if (!this.validated) return;
            this.markInvalidNodes(this.collectProblems().badNodes);
        },

        /**
         * Outlines the cards that failed and clears the ones that did not.
         * The message rides along as a tooltip, so hovering a red card tells you
         * what is wrong with THAT card rather than only the first problem found.
         */
        markInvalidNodes: function (badNodes) {
            badNodes = badNodes || {};
            $('#process-chain .process-node').each(function () {
                var $node   = $(this);
                var message = badNodes[$node.attr('data-node-id')];
                $node.toggleClass('lk-node--invalid', !!message);
                if (message) { $node.attr('title', message); }
                else { $node.removeAttr('title'); }
            });
        },

        /**
         * Brings the first failing card into view — it may be scrolled off.
         * The canvas pans by CSS transform inside an overflow:hidden viewport,
         * so scrollIntoView would move the PAGE and leave the card where it was.
         * Shift the canvas by the on-screen gap instead; that is correct at any
         * zoom and for nodes nested inside a branch column.
         */
        scrollToFirstInvalid: function () {
            var $first   = $('#process-chain .lk-node--invalid').first();
            var viewport = $('#process-viewport')[0];
            if (!$first.length || !viewport) return;

            var vp = viewport.getBoundingClientRect();
            var nd = $first[0].getBoundingClientRect();
            Canvas.y += (vp.top + vp.height / 2) - (nd.top + nd.height / 2);
            Canvas.apply();
        },

        serialize: function () {
            var nodes = JSON.parse(JSON.stringify(this.nodes));
            var clean = function (list) {
                list.forEach(function (node) {
                    if (node.type === 'fill' && node.assignee_mode !== 'fixed' && node.assignee_mode) {
                        node.assignee_ids = []; // resolved at runtime, never persisted on the definition
                    }
                    if (node.type === 'fill' && node.assignee_mode !== 'field') {
                        delete node.assignee_field;
                    }
                    if (node.type !== 'branch') return;
                    (node.branches || []).forEach(function (branch, i) {
                        var isLast = i === node.branches.length - 1;
                        if (isLast) { branch.when = null; delete branch.loop; delete branch.loop_to; }
                        if (branch.loop && branch.loop_to) delete branch.loop_to; // mutually exclusive
                        else if (branch.when && (!branch.when.groups || branch.when.groups.length === 0)) branch.when = { logic: 'or', groups: [] };
                        clean(branch.nodes || []);
                    });
                });
            };
            clean(nodes);
            return { process_version: 1, nodes: nodes };
        }
    };

    /* =================================================================
     * Settings drawer (Bootstrap offcanvas) — edits a working copy,
     * applied on Save.
     * ================================================================= */

    var Drawer = {
        nodeId: null,
        working: null,
        offcanvas: null,

        init: function () {
            var el = document.getElementById('node-drawer');
            if (el && window.bootstrap) {
                this.offcanvas = window.bootstrap.Offcanvas.getOrCreateInstance(el);
            }
            this.bindEvents();
        },

        open: function (nodeId) {
            var found = Designer.findNode(nodeId);
            if (!found) return;
            this.nodeId = nodeId;
            this.working = JSON.parse(JSON.stringify(found.node));
            this.renderShell();
            this.showTab('people');
            if (this.offcanvas) this.offcanvas.show();
        },

        close: function () {
            if (this.offcanvas) this.offcanvas.hide();
        },

        save: function () {
            var found = Designer.findNode(this.nodeId);
            if (found && this.working) {
                // Replace node contents in place so tree references stay valid.
                Object.keys(found.node).forEach(function (key) { delete found.node[key]; });
                $.extend(true, found.node, Drawer.working);
                Designer.render();
            }
            this.close();
        },

        meta: function () {
            return NODE_META[this.working.type] || NODE_META.approval;
        },

        peopleKey: function () {
            if (this.working.type === 'approval') return 'approver_ids';
            if (this.working.type === 'fill') return 'assignee_ids';
            return 'user_ids';
        },

        peopleTabLabel: function () {
            if (this.working.type === 'approval') return 'Set Approvers';
            if (this.working.type === 'fill') return 'Set Handlers';
            return 'Set Recipients';
        },

        renderShell: function () {
            var meta = this.meta();
            $('#drawer-title').html(
                '<span class="lk-drawer-dot" style="background:' + meta.color + ';"></span>' + meta.label + ' Step'
            );

            $('#drawer-content').html(
                '<div class="mb-3">' +
                '  <label class="form-label small fw-semibold mb-1">Step name <span class="text-danger">*</span></label>' +
                '  <input type="text" class="form-control form-control-sm" id="drawer-name" value="' + esc(this.working.name || '') + '">' +
                '</div>' +
                '<ul class="nav nav-tabs lk-drawer-tabs mb-3">' +
                '  <li class="nav-item"><a href="javascript:void(0);" class="nav-link" data-drawer-tab="people">' + this.peopleTabLabel() + '</a></li>' +
                '  <li class="nav-item"><a href="javascript:void(0);" class="nav-link" data-drawer-tab="permissions">Form Permissions</a></li>' +
                '</ul>' +
                '<div id="drawer-tab-people"></div>' +
                '<div id="drawer-tab-permissions" class="d-none"></div>'
            );

            this.renderPeopleTab();
            this.renderPermissionsTab();
        },

        showTab: function (tab) {
            $('.lk-drawer-tabs .nav-link').removeClass('active');
            $('.lk-drawer-tabs .nav-link[data-drawer-tab="' + tab + '"]').addClass('active');
            $('#drawer-tab-people').toggleClass('d-none', tab !== 'people');
            $('#drawer-tab-permissions').toggleClass('d-none', tab !== 'permissions');
        },

        renderPeopleTab: function () {
            var self = this;
            var key = this.peopleKey();
            var selected = this.working[key] || [];

            var chosen  = selected.map(String);
            var options = '';
            Designer.users.forEach(function (u) {
                options += '<option value="' + esc(u.id) + '"' +
                           (chosen.indexOf(String(u.id)) !== -1 ? ' selected' : '') + '>' +
                           esc(u.name) + '</option>';
            });

            var pickerHtml =
                '<select class="form-select form-select-sm js-select2" id="drawer-people" multiple' +
                ' data-placeholder="Search and select people...">' + options + '</select>';

            var html =
                '<label class="form-label small fw-semibold mb-1">' +
                (this.working.type === 'fill' ? 'Handlers <span class="text-danger">*</span>' :
                 this.working.type === 'approval' ? 'Approvers <span class="text-danger">*</span>' : 'Recipients <span class="text-danger">*</span>') +
                '</label>';

            if (this.working.type === 'fill') {
                var mode        = this.working.assignee_mode || 'fixed';
                var userFields  = (window.FormBuilderApi ? window.FormBuilderApi.collectElements() : [])
                                    .filter(function (el) { return el.kind === 'field' && el.type === 'user'; });
                // Only a REQUIRED person field can be trusted to name a handler.
                // An optional one can arrive here empty, and the step then has
                // nobody to go to. Optional fields stay visible but disabled, so
                // the reason is obvious rather than the field just missing.
                var optional = userFields.filter(function (el) { return !el.mandatory; });

                var fieldOptions = '<option value="">Select a Person field…</option>';
                userFields.forEach(function (el) {
                    var picked = self.working.assignee_field === el.id;
                    fieldOptions += '<option value="' + esc(el.id) + '"' +
                                    (picked ? ' selected' : '') +
                                    // Never disable the current pick, or the drawer
                                    // would silently drop a choice already saved.
                                    (!el.mandatory && !picked ? ' disabled' : '') + '>' +
                                    esc(el.label || '(untitled)') +
                                    // State what to DO, not what the field allows:
                                    // "not required" reads as "this person is not
                                    // needed", and "can be left blank" reads as
                                    // permission to leave it blank. Neither is meant.
                                    (el.mandatory ? '' : ' — set Required first') + '</option>';
                });

                html +=
                    '<div class="small text-muted mb-2">Any one handler completes this step by filling their section — no approve/reject.</div>' +
                    '<div class="form-check">' +
                    '  <input class="form-check-input" type="radio" name="drawer_assignee_mode" value="fixed" id="am_fixed"' + (mode === 'fixed' ? ' checked' : '') + '>' +
                    '  <label class="form-check-label small" for="am_fixed">Specific people, chosen now</label>' +
                    '</div>' +
                    '<div class="form-check">' +
                    '  <input class="form-check-input" type="radio" name="drawer_assignee_mode" value="field" id="am_field"' + (mode === 'field' ? ' checked' : '') + '>' +
                    '  <label class="form-check-label small" for="am_field">Whoever is chosen in a Person field</label>' +
                    '</div>' +
                    '<div class="form-check mb-2">' +
                    '  <input class="form-check-input" type="radio" name="drawer_assignee_mode" value="runtime" id="am_runtime"' + (mode === 'runtime' ? ' checked' : '') + '>' +
                    '  <label class="form-check-label small" for="am_runtime">Decided when the flow reaches this step</label>' +
                    '</div>';

                if (mode === 'runtime') {
                    html += '<div class="lk-runtime-note"><i class="mdi mdi-account-question-outline me-1"></i>' +
                            'Whoever completes the previous step will be asked to pick the handler at that moment.</div>';
                } else if (mode === 'field') {
                var picked   = userFields.filter(function (el) { return el.id === self.working.assignee_field; })[0];
                var badPick  = picked && !picked.mandatory;

                html += userFields.length
                    ? '<select class="form-select form-select-sm js-select2" id="drawer-assignee-field"' +
                      ' data-placeholder="Select a Person field...">' + fieldOptions + '</select>' +
                      // Why some options cannot be chosen, stated where they are.
                      (optional.length
                        ? '<div class="lk-runtime-note mt-2"><i class="mdi mdi-information-outline me-1"></i>' +
                          'A Person field must always be answered before it can choose this step’s ' +
                          'handler. To use a greyed-out one, open it in Form Design and tick ' +
                          '<strong>Required</strong>.</div>'
                        : '') +
                      (badPick
                        ? '<div class="lk-runtime-note lk-runtime-note--warn mt-2"><i class="mdi mdi-alert-outline me-1"></i>' +
                          '&ldquo;' + esc(picked.label || 'This field') + '&rdquo; must be set to ' +
                          '<strong>Required</strong> in Form Design before it can choose this step’s ' +
                          'handler. Tick it there, or pick another field here.</div>'
                        : '') +
                      '<div class="lk-runtime-note mt-2"><i class="mdi mdi-account-arrow-right-outline me-1"></i>' +
                      'The step goes to whoever that field names when the flow gets here.</div>'
                    : '<div class="lk-runtime-note"><i class="mdi mdi-alert-outline me-1"></i>' +
                      'Add a <strong>Person</strong> field in Form Design first — that is where the handler is chosen.</div>';
                } else {
                    html += pickerHtml;
                }
            } else {
                html += pickerHtml;
            }

            if (this.working.type === 'approval') {
                html +=
                    '<hr class="my-3">' +
                    '<label class="form-label small fw-semibold mb-1">Approval mode</label>' +
                    '<div class="form-check"><input class="form-check-input" type="radio" name="drawer_mode" value="any" id="dm_any"' + (this.working.approval_mode !== 'all' ? ' checked' : '') + '><label class="form-check-label small" for="dm_any">Any one approver is enough</label></div>' +
                    '<div class="form-check"><input class="form-check-input" type="radio" name="drawer_mode" value="all" id="dm_all"' + (this.working.approval_mode === 'all' ? ' checked' : '') + '><label class="form-check-label small" for="dm_all">Everyone must approve</label></div>';
            }

            // Destroy first: this tab re-renders on every mode switch, and a
            // stale select2 container would be orphaned next to the new markup.
            if (window.FormSelect2) window.FormSelect2.destroy('#drawer-tab-people');
            $('#drawer-tab-people').html(html);
            if (window.FormSelect2) window.FormSelect2.apply('#drawer-tab-people');
        },

        /* Lark-style permissions table: group cell spans member rows,
           Read + Edit(/Fill) checkbox columns with select-all headers. */
        renderPermissionsTab: function () {
            var working = this.working;
            var isFill = working.type === 'fill';
            var isCc = working.type === 'cc';
            var editLabel = isFill ? 'Fill' : 'Edit';

            var groups = window.FormBuilderApi ? window.FormBuilderApi.collectGroups() : [];
            var elements = (window.FormBuilderApi ? window.FormBuilderApi.collectElements() : [])
                .filter(function (el) { return el.kind === 'field'; });

            var permissions = working.field_permissions || { default: 'read', overrides: {} };
            var overrides = permissions.overrides || {};
            var levelOf = function (el) {
                if (overrides[el.id]) return overrides[el.id];
                if (el.group_id && overrides[el.group_id]) return overrides[el.group_id];
                return permissions.default || 'read';
            };

            var checkCells = function (el) {
                var level = levelOf(el);
                var read = level === 'read' || level === 'edit';
                var edit = level === 'edit';
                return (
                    '<td class="text-center"><input type="checkbox" class="form-check-input lk-perm-read" data-el-id="' + esc(el.id) + '"' + (read ? ' checked' : '') + '></td>' +
                    (isCc
                        ? '<td class="text-center text-muted">—</td>'
                        : '<td class="text-center"><input type="checkbox" class="form-check-input lk-perm-edit" data-el-id="' + esc(el.id) + '"' + (edit ? ' checked' : '') + '></td>')
                );
            };

            var body = '';
            var grouped = {};
            elements.forEach(function (el) {
                if (el.group_id) (grouped[el.group_id] = grouped[el.group_id] || []).push(el);
            });

            // Walk the builder's visual order: ungrouped fields span both
            // label columns; a group's name cell spans its member rows.
            var renderedGroups = {};
            elements.forEach(function (el) {
                if (!el.group_id) {
                    body += '<tr><td class="lk-perm-field" colspan="2">' + esc(el.label || '(untitled)') + '</td>' + checkCells(el) + '</tr>';
                    return;
                }
                if (renderedGroups[el.group_id]) return;
                renderedGroups[el.group_id] = true;

                var members = grouped[el.group_id];
                var group = groups.filter(function (g) { return g.id === el.group_id; })[0];
                var groupLabel = group ? (group.label || '(unnamed group)') : '';
                members.forEach(function (member, i) {
                    var groupCell = i === 0
                        ? '<td class="lk-perm-group" rowspan="' + members.length + '">' + esc(groupLabel) + '</td>'
                        : '';
                    body += '<tr>' + groupCell + '<td class="lk-perm-field">' + esc(member.label || '(untitled)') + '</td>' + checkCells(member) + '</tr>';
                });
            });

            var html =
                (isFill
                    ? '<div class="small text-muted mb-2">Tick <strong>Fill</strong> for the fields this handler must complete. Untick <strong>Read</strong> to hide a field from them.</div>'
                    : isCc
                        ? '<div class="small text-muted mb-2">Untick <strong>Read</strong> to hide a field from the CC recipients.</div>'
                        : '<div class="small text-muted mb-2">Untick <strong>Read</strong> to hide a field from the approvers; tick <strong>Edit</strong> to let them change it.</div>') +
                '<div class="table-responsive">' +
                '<table class="table table-sm table-bordered lk-perm-table mb-0">' +
                '<thead><tr>' +
                '  <th colspan="2" class="small">Form fields</th>' +
                '  <th class="text-center small" style="width:70px;"><input type="checkbox" class="form-check-input me-1" id="lk-master-read"> Read</th>' +
                (isCc
                    ? '<th class="text-center small" style="width:70px;"></th>'
                    : '<th class="text-center small" style="width:70px;"><input type="checkbox" class="form-check-input me-1" id="lk-master-edit"> ' + editLabel + '</th>') +
                '</tr></thead>' +
                '<tbody>' + body + '</tbody>' +
                '</table></div>';

            $('#drawer-tab-permissions').html(html);
            this.syncMasterCheckboxes();
        },

        // Rebuild working.field_permissions from the checkbox states:
        // explicit per-field overrides, default kept for fields added later.
        applyPermissionCheckboxes: function () {
            var overrides = {};
            $('#drawer-tab-permissions .lk-perm-read').each(function () {
                var id = $(this).data('el-id');
                var read = $(this).is(':checked');
                var edit = $('#drawer-tab-permissions .lk-perm-edit[data-el-id="' + id + '"]').is(':checked');
                overrides[id] = edit ? 'edit' : (read ? 'read' : 'hidden');
            });
            this.working.field_permissions = {
                default: (this.working.field_permissions || {}).default || 'read',
                overrides: overrides
            };
            this.syncMasterCheckboxes();
        },

        syncMasterCheckboxes: function () {
            var $reads = $('#drawer-tab-permissions .lk-perm-read');
            var $edits = $('#drawer-tab-permissions .lk-perm-edit');
            var setMaster = function ($master, $boxes) {
                if (!$master.length || !$boxes.length) return;
                var checked = $boxes.filter(':checked').length;
                $master.prop('checked', checked === $boxes.length && checked > 0);
                $master.prop('indeterminate', checked > 0 && checked < $boxes.length);
            };
            setMaster($('#lk-master-read'), $reads);
            setMaster($('#lk-master-edit'), $edits);
        },

        bindEvents: function () {
            var self = this;

            $(document).on('click', '.lk-drawer-tabs .nav-link', function () {
                self.showTab($(this).data('drawer-tab'));
            });

            $(document).on('input', '#drawer-name', function () {
                if (self.working) self.working.name = $(this).val();
            });

            $(document).on('change', '#drawer-people', function () {
                if (!self.working) return;
                self.working[self.peopleKey()] = ($(this).val() || []).map(Number);
            });

            $(document).on('change', 'input[name="drawer_mode"]', function () {
                if (self.working) self.working.approval_mode = $(this).val();
            });

            $(document).on('change', 'input[name="drawer_assignee_mode"]', function () {
                if (!self.working) return;
                self.working.assignee_mode = $(this).val();
                self.renderPeopleTab(); // swap between picker / field select / runtime note
            });

            $(document).on('change', '#drawer-assignee-field', function () {
                if (self.working) self.working.assignee_field = $(this).val() || null;
            });

            // Permission checkboxes: Edit implies Read; unchecking Read clears Edit.
            $(document).on('change', '#drawer-tab-permissions .lk-perm-edit', function () {
                if ($(this).is(':checked')) {
                    $('#drawer-tab-permissions .lk-perm-read[data-el-id="' + $(this).data('el-id') + '"]').prop('checked', true);
                }
                self.applyPermissionCheckboxes();
            });
            $(document).on('change', '#drawer-tab-permissions .lk-perm-read', function () {
                if (!$(this).is(':checked')) {
                    $('#drawer-tab-permissions .lk-perm-edit[data-el-id="' + $(this).data('el-id') + '"]').prop('checked', false);
                }
                self.applyPermissionCheckboxes();
            });
            $(document).on('change', '#lk-master-read', function () {
                var on = $(this).is(':checked');
                $('#drawer-tab-permissions .lk-perm-read').prop('checked', on);
                if (!on) $('#drawer-tab-permissions .lk-perm-edit').prop('checked', false);
                self.applyPermissionCheckboxes();
            });
            $(document).on('change', '#lk-master-edit', function () {
                var on = $(this).is(':checked');
                $('#drawer-tab-permissions .lk-perm-edit').prop('checked', on);
                if (on) $('#drawer-tab-permissions .lk-perm-read').prop('checked', true);
                self.applyPermissionCheckboxes();
            });

            $(document).on('click', '#drawer-save', function () { self.save(); });
            $(document).on('click', '#drawer-cancel', function () { self.close(); });
        }
    };

    /* =================================================================
     * Canvas — Figma-like pan (drag empty space) + zoom buttons
     * (bottom-left). The chain element is translated/scaled; nodes stay
     * fully interactive.
     * ================================================================= */

    var Canvas = {
        x: 0, y: 0, z: 1,
        panning: false, startX: 0, startY: 0, originX: 0, originY: 0,

        init: function () {
            var self = this;
            var $viewport = $('#process-viewport');
            if (!$viewport.length) return;

            $viewport.on('mousedown', function (e) {
                if ($(e.target).closest('.lk-node, .lk-cond-card, button, input, a, .dropdown-menu, .lk-zoom-controls').length) return;
                self.panning = true;
                self.startX = e.clientX; self.startY = e.clientY;
                self.originX = self.x;   self.originY = self.y;
                $viewport.addClass('lk-panning');
                e.preventDefault();
            });
            $(document).on('mousemove.lkcanvas', function (e) {
                if (!self.panning) return;
                self.x = self.originX + (e.clientX - self.startX);
                self.y = self.originY + (e.clientY - self.startY);
                self.apply();
            });
            $(document).on('mouseup.lkcanvas', function () {
                if (!self.panning) return;
                self.panning = false;
                $('#process-viewport').removeClass('lk-panning');
            });

            $('#canvas-zoom-in').on('click', function () { self.zoom(0.1); });
            $('#canvas-zoom-out').on('click', function () { self.zoom(-0.1); });
            $('#canvas-zoom-fit').on('click', function () { self.reset(); });

            this.reset();
        },

        zoom: function (delta) {
            var viewport = $('#process-viewport')[0];
            if (!viewport) return;
            var next = Math.min(1.5, Math.max(0.4, Math.round((this.z + delta) * 10) / 10));
            if (next === this.z) return;
            // Zoom about the viewport centre so the flow doesn't drift away.
            var cx = viewport.clientWidth / 2, cy = viewport.clientHeight / 2;
            this.x = cx - (cx - this.x) * (next / this.z);
            this.y = cy - (cy - this.y) * (next / this.z);
            this.z = next;
            this.apply();
        },

        reset: function () {
            var viewport = $('#process-viewport')[0];
            var chain = $('#process-chain')[0];
            this.z = 1;
            this.y = 0;
            this.x = (viewport && chain && viewport.clientWidth > 0)
                ? Math.max(0, (viewport.clientWidth - chain.scrollWidth) / 2)
                : 0;
            this.apply();
        },

        apply: function () {
            $('#process-chain').css('transform', 'translate(' + this.x + 'px,' + this.y + 'px) scale(' + this.z + ')');
            $('#canvas-zoom-level').text(Math.round(this.z * 100) + '%');
        }
    };

    window.FormProcessDesigner = Designer;

})(jQuery);
