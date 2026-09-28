{{--
    Shared 3-step builder wizard body (create + edit).
    Params: $form (nullable), $users, $formElement, $action (route URL)
--}}
@php $isEdit = isset($form) && $form; @endphp

<style>
    .builder-layout { min-height: 620px; }

    /* Left palette */
    .builder-palette { width: 250px; flex-shrink: 0; max-height: 78vh; overflow-y: auto; }
    .palette-section { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em;
                       color: #8c98a4; font-weight: 600; margin: 12px 0 6px; }
    .palette-list { display: flex; flex-wrap: wrap; gap: 6px; }
    .palette-item { flex: 1 1 45%; min-width: 100px; text-align: left; border: 1px solid #e3e6ea;
                    background: #fff; border-radius: 6px; padding: 7px 9px; font-size: 0.8rem;
                    color: #444; cursor: grab; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .palette-item i { color: #7a8794; margin-right: 3px; }
    .palette-item:hover { border-color: #556ee6; color: #556ee6; background: #f3f6ff; }
    .palette-item:hover i { color: #556ee6; }

    /* Center stage */
    .builder-stage { background: #f0f2f5; padding: 28px 16px; display: flex; justify-content: center;
                     align-items: flex-start; max-height: 78vh; overflow-y: auto; }
    .phone-frame { width: 375px; background: #fff; border-radius: 18px; border: 1px solid #e3e6ea;
                   display: flex; flex-direction: column; min-height: 540px; }
    .phone-header { padding: 16px; font-weight: 600; text-align: center; border-bottom: 1px solid #eef0f3;
                    border-radius: 18px 18px 0 0; background: #fff; }
    .phone-body { flex: 1 1 auto; padding: 6px 0 0; min-height: 320px; }
    .phone-footer { padding: 14px; text-align: center; color: #98a2ad; font-size: 0.8rem;
                    border-top: 1px dashed #e3e6ea; }

    /* Preview rows */
    .pv-row { position: relative; padding: 12px 16px; border-bottom: 1px solid #f0f2f5; cursor: pointer; background: #fff; }
    .pv-row:hover { background: #f8faff; }
    .pv-row .pv-flex { display: flex; justify-content: space-between; align-items: baseline; gap: 10px; }
    .pv-label { font-weight: 500; font-size: 0.88rem; color: #212529; word-break: break-word; }
    .pv-label .req { color: #dc3545; }
    .pv-label .untitled { color: #b6bec7; font-weight: 400; }
    .pv-placeholder { color: #b6bec7; font-size: 0.85rem; flex-shrink: 0; }
    .pv-desc-text { color: #74788d; font-size: 0.82rem; white-space: pre-wrap; }
    .pv-badge { font-size: 0.62rem; }
    .pv-selected { outline: 2px solid #556ee6; outline-offset: -2px; background: #f3f6ff !important; }
    .pv-actions { position: absolute; top: -12px; right: 8px; display: none; z-index: 5; }
    .pv-selected .pv-actions { display: flex; gap: 0; }
    .pv-actions button { border: none; background: #556ee6; color: #fff; width: 28px; height: 24px;
                         font-size: 13px; display: inline-flex; align-items: center; justify-content: center; }
    .pv-actions button:first-child { border-radius: 4px 0 0 4px; border-right: 1px solid rgba(255,255,255,.35); }
    .pv-actions button:last-child { border-radius: 0 4px 4px 0; }
    .pv-cond-flag { position: absolute; bottom: 4px; right: 10px; font-size: 0.62rem; color: #556ee6; }
    .pv-row.pv-invalid { outline: 2px solid #dc3545; outline-offset: -2px; }

    /* Group sections in the phone */
    .pv-group { position: relative; border-top: 6px solid #f0f2f5; border-bottom: 6px solid #f0f2f5; cursor: pointer; }
    .pv-group-header { padding: 10px 16px 4px; font-size: 0.78rem; font-weight: 700; color: #b06f24;
                       text-transform: uppercase; letter-spacing: 0.04em; }
    .pv-group-header .untitled { color: #b6bec7; font-weight: 400; text-transform: none; }
    .pv-group .group-drop-zone { min-height: 44px; }
    .pv-group .group-drop-zone:empty::after { content: 'Drag fields into this group'; display: block;
                       padding: 12px 16px; color: #b6bec7; font-size: 0.78rem; font-style: italic; }
    .pv-group.pv-selected { outline: 2px solid #DD974D; background: #fffaf3 !important; }
    .pv-group.pv-invalid { outline: 2px solid #dc3545; }

    /* Right settings panel */
    .builder-settings { width: 340px; flex-shrink: 0; max-height: 78vh; overflow-y: auto; }
    .sortable-ghost { opacity: 0.4; }

    @media (max-width: 1199.98px) {
        .builder-layout { flex-wrap: wrap; }
        .builder-palette { width: 100%; max-height: none; border-right: 0 !important; border-bottom: 1px solid #e3e6ea; }
        .builder-settings { width: 100%; max-height: none; border-left: 0 !important; border-top: 1px solid #e3e6ea; }
    }

    /* ===== Step 3: Lark-style process canvas (pannable + zoomable) ===== */
    #process-viewport { position: relative; overflow: hidden; background: #eef0f4; border-radius: 8px;
                        height: 640px; cursor: grab; user-select: none; }
    #process-viewport.lk-panning { cursor: grabbing; }
    #process-chain { align-items: center; padding: 30px 40px; width: max-content; min-width: 100%;
                     transform-origin: 0 0; will-change: transform; }
    .lk-zoom-controls { position: absolute; bottom: 14px; left: 14px; z-index: 20; background: #fff;
                        border-radius: 8px; box-shadow: 0 1px 6px rgba(31,35,41,0.18); display: flex;
                        align-items: center; padding: 3px; gap: 2px; }
    .lk-zoom-controls button { border: 0; background: transparent; width: 30px; height: 28px; border-radius: 6px;
                        color: #374151; font-size: 15px; display: inline-flex; align-items: center; justify-content: center; }
    .lk-zoom-controls button:hover { background: #eef0f4; }
    .lk-zoom-controls span { font-size: 0.76rem; color: #374151; min-width: 42px; text-align: center; font-weight: 600; }
    .lk-canvas-hint { position: absolute; bottom: 18px; right: 16px; z-index: 20; color: #98a2ad; font-size: 0.72rem; }

    .lk-node { width: 232px; background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(31,35,41,0.12);
               cursor: pointer; overflow: visible; flex-shrink: 0; position: relative; }
    .lk-node:hover { box-shadow: 0 0 0 2px #3370ff, 0 1px 4px rgba(31,35,41,0.12); }
    .lk-node-submit { cursor: default; }
    .lk-node-submit:hover { box-shadow: 0 1px 4px rgba(31,35,41,0.12); }

    /* Validation banner under the canvas. It used to be plain small red text
       and was easy to miss against a busy diagram, so it reads as a proper
       error strip: larger type, its own background, and an icon. */
    #process-error { display: flex; align-items: flex-start; gap: 9px;
                     font-size: 0.95rem; line-height: 1.45; font-weight: 600; color: #a5301f;
                     background: #fdeeee; border: 1px solid #f2c9c4; border-radius: 8px;
                     padding: 11px 15px; text-align: left; }
    #process-error .mdi { font-size: 1.15rem; line-height: 1.25; flex-shrink: 0; }

    /* A step that failed validation. Drawn as a ring rather than a border: a
       border would change the card's size and shift the whole column. Hover
       still darkens it, so the card does not look dead. */
    .lk-node--invalid { box-shadow: 0 0 0 2px #c0392b, 0 1px 4px rgba(31,35,41,0.12); }
    .lk-node--invalid:hover { box-shadow: 0 0 0 2px #a5301f, 0 1px 4px rgba(31,35,41,0.12); }
    /* A branch block spans every column beneath it, so boxing the whole thing
       would outline half the canvas. Mark its pill instead. */
    .lk-branch.lk-node--invalid { box-shadow: none; }
    .lk-branch.lk-node--invalid > .lk-branch-top .lk-branch-pill {
        border-color: #c0392b; color: #c0392b; background: #fdeeee; }
    .lk-node-head { color: #fff; font-weight: 600; font-size: 0.84rem; padding: 7px 12px;
                    border-radius: 8px 8px 0 0; display: flex; align-items: center; justify-content: space-between; }
    .lk-head-title { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lk-head-actions { display: none; gap: 2px; flex-shrink: 0; }
    .lk-node:hover .lk-head-actions, .lk-branch:hover .lk-branch-actions { display: inline-flex; }
    .lk-mini { border: 0; background: rgba(255,255,255,0.25); color: #fff; width: 20px; height: 20px;
               border-radius: 4px; font-size: 12px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    .lk-mini:hover { background: rgba(255,255,255,0.45); }
    .lk-mini-dark, .lk-mini-grey { border: 0; background: #e4e7ec; color: #5f6672; width: 20px; height: 20px;
               border-radius: 4px; font-size: 12px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    .lk-mini-dark:hover, .lk-mini-grey:hover { background: #d3d8e0; }
    .lk-node-body { padding: 10px 12px; font-size: 0.8rem; color: #374151; border-radius: 0 0 8px 8px;
                    position: relative; padding-right: 24px; min-height: 38px; }
    .lk-body-label { color: #8a919f; }
    .lk-missing { color: #b6bec7; font-style: italic; }
    .lk-chevron { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: #b6bec7; }

    .lk-end span { display: inline-block; background: #d7dbe2; color: #5f6672; border-radius: 14px;
                   padding: 4px 18px; font-size: 0.78rem; font-weight: 600; }

    .lk-connector { display: flex; flex-direction: column; align-items: center; }
    .lk-line { width: 1.5px; height: 12px; background: #b9c0cc; }
    .lk-line-arrow { position: relative; height: 16px; }
    .lk-line-arrow::after { content: ''; position: absolute; bottom: -1px; left: 50%; transform: translateX(-50%);
                            border: 5px solid transparent; border-top-color: #b9c0cc; }
    .lk-add-btn { width: 22px; height: 22px; padding: 0; border-radius: 50%; border: 0;
                  background: #3370ff; color: #fff; font-size: 13px; line-height: 1;
                  display: inline-flex; align-items: center; justify-content: center;
                  box-shadow: 0 1px 3px rgba(51,112,255,0.4); }
    .lk-add-btn:hover { background: #2456cc; }
    .lk-add-menu { min-width: 290px; }
    .lk-add-menu .dropdown-item { white-space: normal; padding: 7px 14px; font-weight: 500; }
    .lk-menu-dot { display: inline-block; width: 9px; height: 9px; border-radius: 50%; margin-right: 8px; }
    .lk-menu-hint { display: block; margin-left: 17px; font-size: 0.72rem; color: #8a919f; font-weight: 400; }

    /* Branch block: split/merge rails drawn per column so lines always connect */
    .lk-branch { position: relative; }
    .lk-branch-top { text-align: center; position: relative; margin-bottom: 0; }
    .lk-branch-pill { border: 1px solid #d0d5dd; background: #fff; color: #22a06b; font-weight: 600;
                      font-size: 0.78rem; border-radius: 16px; padding: 5px 14px; box-shadow: 0 1px 3px rgba(31,35,41,0.08); }
    {{-- Hover matches .lk-node:hover rather than tinting green: one canvas,
         one hover colour. Green stays reserved for status, e.g. Approved. --}}
    .lk-branch-pill:hover { border-color: #3370ff; background: #fff; }
    .lk-branch-actions { position: absolute; right: -8px; top: 4px; }
    .lk-branch-center-drop { width: 1.5px; height: 12px; background: #b9c0cc; margin: 0 auto; }
    .lk-branch-columns { display: flex; gap: 0; justify-content: center; align-items: stretch; }
    .lk-branch-column { display: flex; flex-direction: column; align-items: center; min-width: 260px;
                        padding: 0 14px; position: relative; }
    /* rail piece: horizontal segment spanning the FULL column width (negative
       margins bridge the column padding so adjacent segments connect) plus a
       centered vertical drop */
    .lk-rail { position: relative; align-self: stretch; margin: 0 -14px; height: 14px; flex-shrink: 0; }
    .lk-rail::before { content: ''; position: absolute; left: 0; right: 0; height: 1.5px; background: #b9c0cc; }
    .lk-rail-top::before { top: 0; }
    .lk-rail-bottom::before { bottom: 0; }
    .lk-rail-first::before { left: 50%; }
    .lk-rail-last::before { right: 50%; }
    .lk-rail::after { content: ''; position: absolute; left: 50%; top: 0; bottom: 0; width: 1.5px; background: #b9c0cc; }
    /* filler line from the end of a branch's chain down to the merge rail */
    .lk-col-tail { flex: 1 1 auto; min-height: 10px; width: 1.5px; background: #b9c0cc; }

    .lk-cond-card { width: 232px; background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(31,35,41,0.12);
                    cursor: pointer; }
    .lk-cond-card:hover { box-shadow: 0 0 0 2px #3370ff, 0 1px 4px rgba(31,35,41,0.12); }
    .lk-cond-default { cursor: default; }
    .lk-cond-default:hover { box-shadow: 0 1px 4px rgba(31,35,41,0.12); }
    .lk-cond-head { display: flex; align-items: center; gap: 6px; padding: 7px 10px; font-size: 0.78rem;
                    font-weight: 600; color: #22a06b; border-bottom: 1px solid #f0f2f5; }
    .lk-cond-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lk-priority { margin-left: auto; color: #22a06b; font-weight: 500; font-size: 0.72rem; flex-shrink: 0; }
    .lk-cond-head .lk-mini-grey { flex-shrink: 0; }
    .lk-cond-body { padding: 9px 24px 9px 10px; font-size: 0.78rem; color: #374151; position: relative; min-height: 34px; }

    /* Repeat control on conditional branch arms */
    .lk-cond-loop { display: flex; align-items: center; gap: 2px; border-top: 1px dashed #e5e7eb;
                    padding: 5px 8px; font-size: 0.72rem; color: #98a2b3; border-radius: 0 0 8px 8px; cursor: default; }
    .lk-cond-loop.lk-loop-on { color: #4f46e5; background: #eef2ff; }
    .lk-loop-select { flex: 1 1 auto; min-width: 0; border: 0; background: transparent; color: inherit;
                      font-size: 0.72rem; font-weight: 600; padding: 0; cursor: pointer; outline: none; }
    .lk-loop-select:focus { box-shadow: none; }
    /* ===== Group picker (Step 1) ===== */
    .lk-group-picker { display: flex; align-items: stretch; gap: 8px; }
    /* select2 replaces the control, so the flex child is its container. */
    .lk-group-picker > .select2-container { flex: 1 1 auto; min-width: 0; }
    .lk-group-picker > select { flex: 1 1 auto; min-width: 0; }
    .lk-group-new { flex: 0 0 auto; white-space: nowrap; font-weight: 600; font-size: 0.82rem; }

    .lk-group-modal { border-radius: 16px; overflow: hidden; border: 0; }
    .lk-group-modal .modal-body { padding: 26px 28px 8px; }
    .lk-group-modal .modal-footer { padding: 16px 28px 22px; border-top: 0; }
    .lk-group-modal__icon { width: 44px; height: 44px; border-radius: 12px; background: #eef2ff; color: #4f46e5;
                            display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 14px; }
    .lk-group-modal__title { font-size: 17px; font-weight: 800; color: #16203a; }
    .lk-group-modal__desc { font-size: 13.5px; color: #6b7690; margin-top: 6px; line-height: 1.55; }
    .lk-group-modal__input { border-radius: 9px; padding: 10px 13px; }
    .lk-group-modal__input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.14); }
    .lk-group-modal__existing { margin-top: 18px; padding-top: 14px; border-top: 1px solid #eceff5; }
    .lk-group-modal__existing-label { font-size: 11px; font-weight: 800; letter-spacing: 0.08em;
                                      text-transform: uppercase; color: #8a93a8; margin-bottom: 8px; }
    .lk-group-chips { display: flex; flex-wrap: wrap; gap: 6px; max-height: 96px; overflow-y: auto; }
    .lk-group-chip { font-size: 12px; font-weight: 600; color: #3a445c; background: #f1f4f9;
                     border: 1px solid #e3e8f0; border-radius: 999px; padding: 3px 11px; }

    .lk-col-tail { position: relative; overflow: visible; }
    .lk-col-tail:has(.lk-loop-tail) { min-height: 26px; }
    .lk-loop-tail { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                    white-space: nowrap; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;
                    border-radius: 10px; padding: 1px 8px; font-size: 0.68rem; font-weight: 600; }

    /* Runtime-assignee handler */
    .lk-runtime-tag { color: #7a56d1; font-weight: 600; }
    .lk-runtime-note { background: #f5f2fc; border: 1px solid #e2d9f5; color: #5b3fa8; border-radius: 8px;
                       padding: 10px 12px; font-size: 0.78rem; }
    /* Same note, but for a setting that will refuse to save. */
    .lk-runtime-note--warn { background: #fdeeee; border-color: #f2c9c4; color: #a5301f; }
    /* Person fields that are not Required cannot take a handler — show them
       greyed rather than hiding them, so the reason is visible. */
    #drawer-assignee-field option:disabled,
    .select2-results__option[aria-disabled="true"] { color: #a2aab8; }

    .lk-branch-chain { display: flex; flex-direction: column; align-items: center; }

    /* Settings drawer */
    #node-drawer { width: 460px; max-width: 92vw; }
    .lk-drawer-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px; }
    .lk-drawer-tabs .nav-link { font-size: 0.85rem; padding: 7px 14px; color: #5f6672; }
    .lk-drawer-tabs .nav-link.active { font-weight: 600; color: #3370ff; }
    .lk-perm-table { border: 1px solid #dde1e7; }
    .lk-perm-table th, .lk-perm-table td { vertical-align: middle; border: 1px solid #e6e9ee !important; }
    .lk-perm-table thead th { background: #f7f8fa; font-weight: 600; }
    .lk-perm-table .lk-perm-group { width: 130px; font-size: 0.78rem; font-weight: 600; color: #374151;
                                    background: #fafbfc; }
    .lk-perm-table .lk-perm-field { font-size: 0.8rem; }

    /* Who can submit — pills + Lark-style picker modal */
    #submit-users-pills .approver-pill, #submit-types-pills .approver-pill { font-size: 0.78rem; }
    #submit-types-pills .approver-pill { background: #6f42c1 !important; }

    .ap-modal { border-radius: 14px; }
    .ap-panes { display: flex; border: 1px solid #e3e8f0; border-radius: 10px; overflow: hidden;
                height: 420px; }
    .ap-left { flex: 1.25; border-right: 1px solid #eceff5; display: flex; flex-direction: column; min-width: 0; }
    .ap-tabs { display: flex; gap: 2px; border-bottom: 1px solid #eceff5; padding: 0 14px; flex-shrink: 0; }
    .ap-tab { padding: 11px 14px; font-weight: 600; font-size: 0.86rem; color: #5f6672;
              border-bottom: 2px solid transparent; margin-bottom: -1px; text-decoration: none; }
    .ap-tab:hover { color: #3370ff; }
    .ap-tab.active { color: #3370ff; border-bottom-color: #3370ff; }
    .ap-search { margin: 12px 14px 8px; display: flex; align-items: center; gap: 7px;
                 border: 1px solid #d5dbe7; border-radius: 8px; padding: 7px 11px; flex-shrink: 0; }
    .ap-search:focus-within { border-color: #3370ff; box-shadow: 0 0 0 3px rgba(51,112,255,0.12); }
    .ap-search input { border: 0; outline: none; flex: 1; font-size: 0.85rem; min-width: 0; background: transparent; }
    .ap-list { flex: 1; overflow-y: auto; padding: 2px 8px 10px; }
    .ap-row { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 6px;
              cursor: pointer; font-size: 0.88rem; color: #16203a; }
    .ap-row:hover { background: #f4f6fa; }
    .ap-row .form-check-input { margin: 0; flex-shrink: 0; cursor: pointer; }
    .ap-row-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ap-list-empty { padding: 24px 14px; text-align: center; color: #b6bec7; font-size: 0.85rem; }
    .ap-right { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .ap-right-head { display: flex; justify-content: space-between; align-items: center;
                     padding: 12px 16px 8px; font-size: 0.86rem; font-weight: 600; color: #374151; flex-shrink: 0; }
    .ap-right-head a { font-size: 0.83rem; font-weight: 600; }
    .ap-selected { overflow-y: auto; display: flex; flex-direction: column; }
    .ap-selected-row { display: flex; justify-content: space-between; align-items: center; gap: 8px;
                       padding: 7px 16px; font-size: 0.88rem; }
    .ap-selected-row:hover { background: #f8f9fc; }
    .ap-selected-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ap-selected-tag { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;
                       border-radius: 4px; padding: 2px 6px; flex-shrink: 0; }
    .ap-selected-tag--user { background: #eef1fd; color: #3446b8; }
    .ap-selected-tag--type { background: #f1ecfb; color: #6f42c1; }
    .ap-selected-remove { color: #b6bec7; cursor: pointer; flex-shrink: 0; }
    .ap-selected-remove:hover { color: #c0392b; }
    .ap-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
                color: #b6bec7; gap: 4px; min-height: 200px; }
    .ap-empty i { font-size: 44px; opacity: 0.6; }

    /* Options list collapse (Selection inputs with > 5 options).
       !important: option rows carry Bootstrap's .d-flex (display:flex !important),
       which would otherwise always win over this rule. */
    .ps-options-collapsed .ps-option:nth-child(n+6) { display: none !important; }
</style>

{{-- Step pills --}}
<div class="card">
    <div class="card-body py-3">
        <ul class="nav nav-pills nav-justified">
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link wizard-pill" data-step="1">
                    <span class="badge rounded-circle bg-light text-dark me-1">1</span> Basic Info
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link wizard-pill" data-step="2">
                    <span class="badge rounded-circle bg-light text-dark me-1">2</span> Form Design
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link wizard-pill" data-step="3">
                    <span class="badge rounded-circle bg-light text-dark me-1">3</span> Process Design
                </a>
            </li>
        </ul>
    </div>
</div>

<form id="form-metadata" action="{{ $action }}" method="POST">
    @csrf
    <input type="hidden" name="form_elements" id="form_elements_json">
    <input type="hidden" name="settings" id="settings_json">
    <input type="hidden" name="process_definition" id="process_definition_json">

    {{-- ============ STEP 1: BASIC INFO ============ --}}
    <div class="wizard-pane" data-step="1">
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title mb-4">Basic Info</h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="form_name" name="name" required
                                   placeholder="Enter form name" value="{{ old('name', $isEdit ? $form->name : '') }}">
                            <div class="invalid-feedback">Please enter a name.</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select js-select2" id="is_enabled" name="is_enabled" data-no-search="1" required>
                                <option value="1" {{ $isEdit && $form->is_enabled ? 'selected' : '' }}>Enable</option>
                                <option value="0" {{ !$isEdit || !$form->is_enabled ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="2" maxlength="255"
                                      placeholder="Max 255 characters" required>{{ old('description', $isEdit ? $form->description : '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Who can submit --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Who can submit this form?</label>
                            <select class="form-select js-select2" id="submit-scope-select" data-no-search="1">
                                <option value="everyone">Everyone</option>
                                <option value="selected">Selected Members Only</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="access-summary">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <div class="d-flex flex-wrap gap-2" id="submit-users-pills"></div>
                                <div class="d-flex flex-wrap gap-2" id="submit-types-pills"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="access-edit-btn">
                                    <i class="mdi mdi-pencil-outline me-1"></i>Edit
                                </button>
                            </div>
                            <div class="form-text small mt-2">
                                A user may submit if they are selected individually <strong>or</strong> are one of the selected user types.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Group: where this form is filed. Sits after "who can submit"
                     because both answer "where does this form show up, and for
                     whom" — the two decisions are read together. --}}
                @php $currentGroup = old('form_group_id', $isEdit ? $form->form_group_id : null); @endphp
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Group <span class="text-danger">*</span></label>
                            <div class="lk-group-picker">
                                <select class="form-select js-select2" id="form_group_id" name="form_group_id" required
                                        data-placeholder="Select a group…">
                                    <option value=""></option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ (int) $currentGroup === (int) $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary lk-group-new" id="btn-new-group">
                                    <i class="mdi mdi-folder-plus-outline me-1"></i>New group
                                </button>
                            </div>
                            <div class="invalid-feedback d-block d-none" id="form_group_error">Choose a group for this form.</div>
                            <div class="form-text small mt-2">
                                Groups organise the form list. They do not affect who can submit.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create a group without leaving the builder: being forced to abandon a
         half-written form to make a group would be a reason to pick the wrong
         one. Lives outside the wizard panes so it is never hidden with a step. --}}
    <div class="modal fade" id="newGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content lk-group-modal">
                <div class="modal-body">
                    <div class="lk-group-modal__icon"><i class="mdi mdi-folder-plus-outline"></i></div>
                    <div class="lk-group-modal__title">Create a group</div>
                    <div class="lk-group-modal__desc">
                        Groups keep the form list navigable. This form will be filed into the new group straight away.
                    </div>

                    <label class="form-label small fw-semibold mt-3">Group name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control lk-group-modal__input" id="newGroupName" maxlength="120"
                           placeholder="e.g. Reports, Sales, Claims" autocomplete="off">
                    <div class="invalid-feedback" id="newGroupError">A group name is required.</div>

                    @if($groups->isNotEmpty())
                        {{-- Showing what already exists is the cheapest way to
                             prevent a near-duplicate like "Report" vs "Reports". --}}
                        <div class="lk-group-modal__existing">
                            <div class="lk-group-modal__existing-label">Already in use</div>
                            <div class="lk-group-chips">
                                @foreach($groups as $group)
                                    <span class="lk-group-chip">{{ $group->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btn-save-new-group">
                        <span class="lk-group-save-label"><i class="mdi mdi-check me-1"></i>Create group</span>
                        <span class="lk-group-save-busy d-none"><i class="mdi mdi-loading mdi-spin me-1"></i>Creating…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ STEP 2: FORM DESIGN ============ --}}
    <div class="wizard-pane d-none" data-step="2">
        <div class="card mt-3">
            <div class="card-body p-0">
                <div class="d-flex builder-layout">

                    {{-- Left: widget palette --}}
                    <div class="builder-palette border-end">
                        <div class="p-3 pb-2 fw-bold">Widgets</div>

                        <div class="px-3 pb-3">
                            <div class="palette-section">Layout</div>
                            <div class="palette-list">
                                <button type="button" class="palette-item" data-palette="description" data-label="Description">
                                    <i class="mdi mdi-text"></i> Description
                                </button>
                                <button type="button" class="palette-item" data-palette="group" data-label="Group">
                                    <i class="mdi mdi-group"></i> Group
                                </button>
                            </div>

                            <div class="palette-section">Text</div>
                            <div class="palette-list">
                                <button type="button" class="palette-item" data-palette="field" data-type="text" data-label="Single-line Text">
                                    <i class="mdi mdi-form-textbox"></i> Single line
                                </button>
                                <button type="button" class="palette-item" data-palette="field" data-type="textarea" data-label="Multi-line Text">
                                    <i class="mdi mdi-text-long"></i> Multi line
                                </button>
                                <button type="button" class="palette-item" data-palette="field" data-type="email" data-label="Email">
                                    <i class="mdi mdi-email-outline"></i> Email
                                </button>
                                <button type="button" class="palette-item" data-palette="field" data-type="tel" data-label="Telephone">
                                    <i class="mdi mdi-phone-outline"></i> Telephone
                                </button>
                            </div>

                            <div class="palette-section">Numerical</div>
                            <div class="palette-list">
                                <button type="button" class="palette-item" data-palette="field" data-type="number" data-label="Number">
                                    <i class="mdi mdi-numeric"></i> Number
                                </button>
                            </div>

                            <div class="palette-section">Selection</div>
                            <div class="palette-list">
                                <button type="button" class="palette-item" data-palette="field" data-type="select" data-label="Single Select">
                                    <i class="mdi mdi-arrow-down-drop-circle-outline"></i> Single select
                                </button>
                                <button type="button" class="palette-item" data-palette="field" data-type="multi-choice" data-label="Multiple Select">
                                    <i class="mdi mdi-format-list-checks"></i> Multiple select
                                </button>
                                <button type="button" class="palette-item" data-palette="field" data-type="multi-select" data-label="Categorized Multi-select">
                                    <i class="mdi mdi-file-tree"></i> Categorized
                                </button>
                                <button type="button" class="palette-item" data-palette="field" data-type="checkbox" data-label="Checkbox">
                                    <i class="mdi mdi-checkbox-marked-outline"></i> Checkbox
                                </button>
                            </div>

                            <div class="palette-section">Date &amp; Time</div>
                            <div class="palette-list">
                                <button type="button" class="palette-item" data-palette="field" data-type="date" data-label="Date">
                                    <i class="mdi mdi-calendar-outline"></i> Date
                                </button>
                                <button type="button" class="palette-item" data-palette="field" data-type="time" data-label="Time">
                                    <i class="mdi mdi-clock-outline"></i> Time
                                </button>
                            </div>

                            <div class="palette-section">Other</div>
                            <div class="palette-list">
                                <button type="button" class="palette-item" data-palette="field" data-type="file" data-label="Attachment">
                                    <i class="mdi mdi-paperclip"></i> Attachment
                                </button>
                                {{-- A Handler step can be assigned to whoever is chosen here. --}}
                                <button type="button" class="palette-item" data-palette="field" data-type="user" data-label="Person">
                                    <i class="mdi mdi-account-outline"></i> Person
                                </button>
                                {{-- Device location, captured on open or on tap; never typed. --}}
                                <button type="button" class="palette-item" data-palette="field" data-type="gps" data-label="Location Stamp">
                                    <i class="mdi mdi-crosshairs-gps"></i> Location Stamp
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Center: phone preview canvas --}}
                    <div class="builder-stage flex-grow-1">
                        <div class="phone-frame shadow-sm">
                            <div class="phone-header" id="phone-form-name">New Form</div>
                            <div id="builder-canvas" class="phone-body"></div>
                            <div class="phone-footer" id="canvas-empty-state">
                                <i class="mdi mdi-cursor-move me-1"></i>Click or drag widgets from the left
                            </div>
                        </div>
                    </div>

                    {{-- Right: settings panel --}}
                    <div class="builder-settings border-start">
                        <div id="settings-empty" class="text-muted text-center py-5 px-3">
                            <i class="mdi mdi-gesture-tap display-6 d-block mb-2"></i>
                            Select a widget in the preview to configure it.
                        </div>
                        <div id="settings-panel" class="d-none p-3">
                            <h5 class="mb-3" id="settings-type-title"></h5>
                            <ul class="nav nav-pills nav-fill mb-3" id="settings-tabs">
                                <li class="nav-item">
                                    <a href="javascript:void(0);" class="nav-link active py-1" data-panel-tab="basic">Basic Settings</a>
                                </li>
                                <li class="nav-item" id="settings-visibility-tab-item">
                                    <a href="javascript:void(0);" class="nav-link py-1" data-panel-tab="visibility">Visibility Settings</a>
                                </li>
                            </ul>
                            <div id="panel-basic"></div>
                            <div id="panel-visibility" class="d-none">
                                <p class="text-muted small mb-2">
                                    Show this widget only when the conditions below match.
                                    Conditions in a block must <strong>all</strong> match (AND); any block matching is enough (OR).
                                    Leave empty to always show.
                                </p>
                                <div class="condition-groups-list" id="panel-condition-groups"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="panel-add-condition-group">
                                    <i class="mdi mdi-plus"></i> Or condition block
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div id="form-elements-error" class="text-danger text-center mt-2 font-size-14 d-none">
            Please add at least one form input.
        </div>

        {{-- No record settings any more: every submission is one case, and
             following up on an earlier case is chosen per submission on the
             fill page, not configured per form. --}}
    </div>

    {{-- ============ STEP 3: PROCESS DESIGN ============ --}}
    <div class="wizard-pane d-none" data-step="3">
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title mb-1">Process Design</h4>
                <p class="text-muted small mb-3">
                    Define what happens after submission: <strong>Handler</strong> steps (other users fill their
                    part of the form — no approval), <strong>Approval</strong> steps, <strong>CC</strong>, and
                    <strong>Conditional Branches</strong>. Fields assigned to a Handler are hidden from the
                    original submitter. Click a step to set its people and Form Permissions.
                    A form with no steps is auto-approved on submission.
                </p>
                <div id="process-viewport">
                    <div id="process-chain" class="d-flex flex-column"></div>
                    <div class="lk-zoom-controls">
                        <button type="button" id="canvas-zoom-out" title="Zoom out"><i class="mdi mdi-minus"></i></button>
                        <span id="canvas-zoom-level">100%</span>
                        <button type="button" id="canvas-zoom-in" title="Zoom in"><i class="mdi mdi-plus"></i></button>
                        <button type="button" id="canvas-zoom-fit" title="Reset view"><i class="mdi mdi-fit-to-screen-outline"></i></button>
                    </div>
                    <div class="lk-canvas-hint"><i class="mdi mdi-cursor-move me-1"></i>Drag empty space to pan</div>
                </div>
                {{-- The icon is markup, the message is set as TEXT on the span,
                     so a form/field name in the message can never inject HTML. --}}
                <div id="process-error" class="mt-3 d-none">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <span id="process-error-text"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Wizard footer --}}
    <div class="text-center my-4">
        <button type="button" class="btn btn-warning me-2" onclick="window.history.back()">Cancel</button>
        <button type="button" class="btn btn-secondary me-2 wizard-back d-none" id="wizard-back-btn"><i class="mdi mdi-arrow-left me-1"></i>Back</button>
        <button type="button" class="btn btn-primary me-2 wizard-next" id="wizard-next-btn">Next<i class="mdi mdi-arrow-right ms-1"></i></button>
        <button type="button" class="btn btn-success d-none" id="submit-form-btn">{{ $isEdit ? 'Save Changes' : 'Create Form' }}</button>
    </div>
</form>

@include('page.form.partials._condition-editor')

{{-- Lark-style access picker ("Who can submit" → Selected Members Only).
     List rows are rendered by JS from FormBuilderBoot.users / .types. --}}
<div class="modal fade" id="accessPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content ap-modal">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold">Please select</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="ap-panes">
                    <div class="ap-left">
                        <div class="ap-tabs">
                            <a href="javascript:void(0);" class="ap-tab active" data-ap-tab="users">Specific Users</a>
                            <a href="javascript:void(0);" class="ap-tab" data-ap-tab="types">User Types</a>
                        </div>
                        <div class="ap-search">
                            <i class="mdi mdi-magnify text-muted"></i>
                            <input type="text" id="ap-search-input" placeholder="Search for the name of a member" autocomplete="off">
                        </div>
                        <div class="ap-list" id="ap-list"></div>
                    </div>
                    <div class="ap-right">
                        <div class="ap-right-head">
                            <span>Selected: <span id="ap-count"></span></span>
                            <a href="javascript:void(0);" id="ap-clear">Clear</a>
                        </div>
                        <div class="ap-selected flex-grow-1" id="ap-selected">
                            <div class="ap-empty" id="ap-empty">
                                <i class="mdi mdi-inbox-outline"></i>
                                <div>No Data</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="ap-ok">OK</button>
            </div>
        </div>
    </div>
</div>

{{-- Process node settings drawer (Lark-style) --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="node-drawer" data-bs-scroll="false">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title d-flex align-items-center" id="drawer-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
        <div id="drawer-content" class="flex-grow-1 overflow-auto p-3"></div>
        <div class="border-top p-3 text-end bg-white">
            <button type="button" class="btn btn-light" id="drawer-cancel">Cancel</button>
            <button type="button" class="btn btn-primary ms-2" id="drawer-save">Save</button>
        </div>
    </div>
</div>
