{{-- Ronda HQ dashboard styles, shared by the team dashboard (.dash), the
     admin Overview and the app errors page (.adm).
     Brand: teal for actions, ink for text, a warm paper ground. Red, amber
     and green are for status only, always with an icon and a word. --}}
<style>
    .dash, .adm {
        --teal         : #0D729E;
        --teal-dark    : #0A5A7D;
        --teal-tint    : #E3F1F7;
        --ink          : #0E2A36;
        --ink-2        : #3D525C;
        --muted        : #5B6B72;
        --paper        : #F6F4EF;
        --track        : #EAE6DC;
        --surface      : #FFFFFF;
        --hair         : #E6E1D6;
        --hair-soft    : #EFEBE3;
        --field        : #7C8E96;
        --ok           : #067647;
        --ok-tint      : #E3F5EA;
        --warn         : #93370D;
        --warn-icon    : #B54708;
        --warn-tint    : #FEF0C7;
        --crit         : #B42318;
        --crit-tint    : #FDE7E5;
        --neutral-tint : #EFEBE3;
        --series-1     : #0D729E;
        --series-2     : #D9822B;
        --critical     : var(--crit);
        --serious      : var(--warn-icon);
        --good         : var(--ok);
        font-family    : "IBM Plex Sans", system-ui, sans-serif;
        color          : var(--ink);
    }

    /* ---- Team dashboard ---- */
    .dash-tiles { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .dash-tile { background: var(--surface); border: 1px solid var(--hair); border-radius: 12px; padding: 12px 14px; }
    .dash-tile-label { font-size: 0.8rem; color: var(--ink-2); display: flex; align-items: center; gap: 6px; }
    .dash-tile-value { font-size: 1.6rem; font-weight: 600; line-height: 1.2; margin-top: 4px; font-variant-numeric: tabular-nums; }
    .dash-tile-note { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
    .dash-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .dash-card { background: var(--surface); border: 1px solid var(--hair); border-radius: 12px; padding: 14px 16px; height: 100%; }
    .dash-card h6 { font-size: 0.95rem; font-weight: 600; margin: 0 0 10px; color: var(--ink); }
    .dash-sub { font-size: 0.78rem; color: var(--muted); }
    .dash-table { width: 100%; font-size: 0.8rem; border-collapse: collapse; }
    .dash-table th { color: var(--ink-2); font-weight: 600; border-bottom: 1px solid var(--hair); padding: 6px 8px; white-space: nowrap; }
    .dash-table td { border-bottom: 1px solid var(--hair-soft); padding: 6px 8px; vertical-align: top; }
    .dash-table .num { text-align: right; font-variant-numeric: tabular-nums; }
    .dash-level { display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; white-space: nowrap; color: var(--ink); }
    .dash-level i { font-size: 1rem; }
    .dash-digest { font-size: 0.85rem; line-height: 1.5; }
    .dash-digest .label { font-weight: 600; margin-top: 8px; }
    .dash-digest ul { padding-left: 18px; margin: 2px 0 0; }
    .dash-chart-wrap { position: relative; height: 260px; }

    /* ---- Overview | Team switch (admins) ---- */
    .adm-tabs { display: inline-flex; padding: 4px; gap: 4px; background: var(--track); border-radius: 10px; }
    .adm-tab { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 0 18px; border-radius: 8px; color: var(--ink-2); font-size: 14px; font-weight: 500; text-decoration: none; }
    .adm-tab:hover { color: var(--ink); }
    .adm-tab[aria-current="page"] { background: var(--surface); color: var(--ink); font-weight: 600; box-shadow: 0 1px 2px rgba(14, 42, 54, 0.14); }
    .dash :focus-visible, .adm :focus-visible { outline: 3px solid var(--teal); outline-offset: 2px; }

    /* ---- Admin pages ---- */
    .adm { background: var(--paper); border-radius: 16px; padding: 24px; margin: 8px 0 24px; font-size: 14px; line-height: 1.45; }
    .adm [hidden] { display: none !important; }
    .adm a:not([class]), .adm .adm-link { color: var(--teal); }
    .adm a:not([class]):hover, .adm .adm-link:hover { color: var(--teal-dark); }
    .adm-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px 24px; margin-bottom: 24px; }
    .adm-title { margin: 0; font-size: 26px; line-height: 34px; font-weight: 600; color: var(--ink); }
    .adm-meta { margin: 4px 0 0; font-size: 14px; color: var(--muted); }
    .adm-head-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; }
    .adm-muted { color: var(--muted); }
    .adm-note { font-size: 12px; line-height: 18px; color: var(--muted); }

    .adm-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 16px; border: 1px solid var(--teal); border-radius: 8px; background: var(--surface); color: var(--teal); font-size: 14px; font-weight: 600; text-decoration: none; white-space: nowrap; flex-shrink: 0; }
    .adm .adm-btn:hover { background: var(--teal-tint); color: var(--teal-dark); }
    .adm-link { display: inline-flex; align-items: center; gap: 6px; min-height: 44px; font-weight: 600; text-decoration: none; }
    .adm-link--tight { min-height: 32px; }

    .adm-stack { display: flex; flex-direction: column; gap: 24px; }
    .adm-row-2 { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 24px; }
    .adm-row-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
    .adm-row-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 24px; }

    .adm-card { background: var(--surface); border: 1px solid var(--hair); border-radius: 12px; padding: 24px; min-width: 0; }
    .adm-card-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .adm-card-title { margin: 0; font-size: 17px; line-height: 24px; font-weight: 600; color: var(--ink); display: flex; align-items: center; gap: 10px; }
    .adm-card-sub { margin: 4px 0 0; font-size: 13px; line-height: 18px; color: var(--muted); }
    .adm-count { font-size: 12px; line-height: 18px; font-weight: 600; background: var(--ink); color: #FFFFFF; padding: 1px 8px; border-radius: 999px; }

    /* Lists of rows: attention items, system status, setup */
    .adm-list { list-style: none; margin: 16px 0 0; padding: 0; }
    .adm-item { display: flex; align-items: center; gap: 16px; padding: 14px 0; border-top: 1px solid var(--hair-soft); }
    .adm-item-body { flex: 1; min-width: 0; }
    .adm-item-title { font-size: 15px; line-height: 22px; font-weight: 600; }
    .adm-item-detail { font-size: 13px; line-height: 18px; color: var(--muted); margin-top: 2px; }
    .adm-item .adm-btn { min-width: 148px; }
    .adm-badge { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .adm-badge--critical { background: var(--crit-tint); color: var(--crit); }
    .adm-badge--warning { background: var(--warn-tint); color: var(--warn-icon); }
    .adm-badge--info { background: var(--teal-tint); color: var(--teal); }
    .adm-empty { text-align: center; padding: 32px 16px 8px; margin-top: 16px; border-top: 1px solid var(--hair-soft); }
    .adm-empty-icon { width: 56px; height: 56px; border-radius: 50%; margin: 0 auto; background: var(--ok-tint); color: var(--ok); display: flex; align-items: center; justify-content: center; }
    .adm-empty-title { margin: 16px 0 0; font-size: 16px; line-height: 24px; font-weight: 600; }

    .adm-status { display: flex; flex-direction: column; }
    .adm-status .adm-list { flex: 1; }
    .adm-status .adm-item { align-items: flex-start; gap: 12px; }
    .adm-status-icon { display: flex; flex-shrink: 0; margin-top: 1px; }
    .adm-status-icon--ok { color: var(--ok); }
    .adm-status-icon--warning { color: var(--warn-icon); }
    .adm-status-icon--critical { color: var(--crit); }
    .adm-status-icon--neutral, .adm-status-icon--info { color: var(--muted); }
    .adm-status-foot { margin: 8px 0 0; padding-top: 12px; border-top: 1px solid var(--hair-soft); }

    .adm-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; font-size: 13px; font-weight: 600; white-space: nowrap; }
    .adm-chip--sm { padding: 2px 8px; font-size: 12px; }
    .adm-chip--ok, .adm-chip--good, .adm-chip--done { background: var(--ok-tint); color: var(--ok); }
    .adm-chip--warning, .adm-chip--low { background: var(--warn-tint); color: var(--warn); }
    .adm-chip--critical { background: var(--crit-tint); color: var(--crit); }
    .adm-chip--neutral, .adm-chip--fair, .adm-chip--none, .adm-chip--inactive { background: var(--neutral-tint); color: var(--ink-2); }

    /* Headline numbers */
    .adm-kpi { padding: 20px 24px; }
    .adm-kpi-head { display: flex; align-items: center; justify-content: space-between; }
    .adm-kpi-label { margin: 0; font-size: 14px; line-height: 20px; font-weight: 500; color: var(--ink-2); }
    .adm-info { width: 44px; height: 44px; margin: -12px -14px -12px 0; border: 0; border-radius: 8px; background: transparent; color: var(--muted); display: flex; align-items: center; justify-content: center; cursor: help; }
    .adm-kpi-value { margin-top: 6px; font-size: 30px; line-height: 38px; font-weight: 600; font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
    .adm-kpi-value small { font-size: 17px; font-weight: 500; color: var(--muted); }
    .adm-kpi-note { margin-top: 4px; font-size: 13px; line-height: 18px; color: var(--muted); }
    .adm-delta { margin-top: 10px; display: flex; align-items: flex-start; gap: 6px; font-size: 13px; line-height: 18px; font-weight: 600; }
    .adm-delta svg { flex-shrink: 0; margin-top: 1px; }
    .adm-delta--up { color: var(--ok); }
    .adm-delta--down { color: var(--warn); }
    .adm-delta--flat { color: var(--ink-2); }
    .adm-focus-pair { margin-top: 6px; display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 20px; }
    .adm-focus-pair > div { display: flex; align-items: baseline; gap: 6px; }
    .adm-focus-num { font-size: 30px; line-height: 38px; font-weight: 600; font-variant-numeric: tabular-nums; }
    .adm-focus-label { display: inline-flex; align-items: center; gap: 4px; font-size: 14px; font-weight: 600; }

    .adm-bar { height: 8px; background: var(--track); border-radius: 4px; overflow: hidden; }
    .adm-bar > span { display: block; height: 100%; background: var(--teal); border-radius: 4px; }
    .adm-bar--thin { height: 6px; }
    .adm-bar--thick { height: 12px; border-radius: 6px; }

    /* People */
    .adm-tools { display: flex; flex-wrap: wrap; align-items: center; gap: 12px 16px; margin-top: 16px; }
    .adm-search { position: relative; width: 280px; max-width: 100%; }
    .adm-search svg { position: absolute; left: 12px; top: 12px; color: var(--muted); pointer-events: none; }
    .adm-input, .adm-select { height: 44px; border: 1px solid var(--field); border-radius: 8px; background: var(--surface); color: var(--ink); font-size: 14px; }
    .adm-input { width: 100%; padding: 0 12px 0 40px; }
    .adm-select { padding: 0 12px; }
    .adm-filters { display: flex; flex-wrap: wrap; gap: 8px; }
    .adm-filter { display: inline-flex; align-items: center; gap: 6px; min-height: 40px; padding: 0 14px; border: 1px solid var(--field); border-radius: 999px; background: var(--surface); color: var(--ink); font-size: 14px; cursor: pointer; }
    .adm-filter span { color: var(--muted); }
    .adm-filter[aria-pressed="true"] { background: var(--ink); border-color: var(--ink); color: #FFFFFF; font-weight: 600; }
    .adm-filter[aria-pressed="true"] span { color: #FFFFFF; }
    .adm-sort { margin-left: auto; display: flex; align-items: center; gap: 8px; }
    .adm-sort label { margin: 0; color: var(--ink-2); }
    .adm-table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
    .adm-table th { padding: 10px 12px; border-bottom: 1px solid var(--hair); font-size: 13px; font-weight: 600; color: var(--ink-2); text-align: left; white-space: nowrap; }
    .adm-table td { padding: 12px; border-bottom: 1px solid var(--hair-soft); vertical-align: middle; }
    .adm-table .num { text-align: right; font-variant-numeric: tabular-nums; }
    .adm-person { display: flex; align-items: center; gap: 12px; }
    .adm-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--teal-tint); color: var(--teal-dark); font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .adm .adm-person a { color: var(--ink); font-weight: 600; text-decoration: none; }
    .adm .adm-person a:hover { color: var(--ink); text-decoration: underline; }
    .adm-quiet { display: inline-flex; align-items: center; gap: 6px; color: var(--warn); font-weight: 600; }
    .adm-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-top: 16px; }

    /* Data health */
    .adm-health-item { padding: 14px 0 4px; border-top: 1px solid var(--hair-soft); }
    .adm-health-head { display: flex; align-items: center; gap: 8px; }
    .adm-health-label { flex: 1; font-size: 14px; line-height: 20px; font-weight: 600; }
    .adm-health-share { width: 48px; text-align: right; font-size: 18px; font-weight: 600; font-variant-numeric: tabular-nums; }
    .adm-health-item .adm-bar { margin-top: 8px; }
    .adm-health-note { margin-top: 8px; font-size: 13px; line-height: 18px; color: var(--ink-2); }
    .adm-health-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; min-height: 40px; font-size: 13px; }
    .adm-health-foot a { display: inline-flex; align-items: center; min-height: 40px; font-weight: 600; }

    /* Coverage */
    .adm-toggle { display: inline-flex; padding: 4px; gap: 4px; background: var(--track); border-radius: 10px; flex-shrink: 0; }
    .adm-toggle button { min-height: 36px; padding: 0 12px; border: 0; border-radius: 8px; background: transparent; color: var(--ink-2); font-size: 13px; font-weight: 500; cursor: pointer; }
    .adm-toggle button[aria-pressed="true"] { background: var(--surface); color: var(--ink); font-weight: 600; box-shadow: 0 1px 2px rgba(14, 42, 54, 0.14); }
    .adm-cov { list-style: none; margin: 20px 0 0; padding: 0; display: flex; flex-direction: column; gap: 14px; }
    .adm-cov li { display: flex; align-items: center; gap: 12px; }
    .adm-cov-name { width: 120px; flex-shrink: 0; }
    .adm-cov-name strong { display: block; font-size: 14px; line-height: 18px; font-weight: 600; }
    .adm-cov-name span { display: block; font-size: 12px; line-height: 16px; color: var(--muted); }
    .adm-cov .adm-bar { flex: 1; }
    .adm-cov-share { width: 44px; flex-shrink: 0; text-align: right; font-weight: 600; font-variant-numeric: tabular-nums; }
    .adm-cov-scale { margin: 10px 56px 0 132px; display: flex; justify-content: space-between; font-size: 12px; color: var(--muted); }

    /* Setup */
    .adm-setup-row { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-top: 1px solid var(--hair-soft); }
    .adm-setup-icon { width: 36px; height: 36px; border-radius: 8px; background: var(--teal-tint); color: var(--teal); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .adm-setup-name { font-size: 14px; line-height: 20px; font-weight: 600; }
    .adm-setup-row a { display: inline-flex; align-items: center; min-height: 44px; padding: 0 4px; font-weight: 600; }
    .adm-quick-title { margin: 16px 0 10px; font-size: 13px; line-height: 18px; font-weight: 600; color: var(--ink-2); }
    .adm-quick { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .adm .adm-quick a { display: flex; align-items: center; justify-content: center; gap: 8px; min-height: 48px; border-radius: 8px; background: var(--teal-tint); color: var(--teal-dark); font-weight: 600; text-decoration: none; }
    .adm .adm-quick a:hover { background: #D2E8F1; }

    .adm-message { white-space: pre-wrap; overflow-wrap: anywhere; margin-top: 6px; font-size: 13px; color: var(--ink-2); }

    /* ---- Narrower screens: same order, stacked ---- */
    @media (max-width: 1199.98px) {
        .adm-row-3, .adm-row-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 991.98px) {
        .adm-row-2, .adm-row-3 { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width: 767.98px) {
        .adm-sort { margin-left: 0; }
        /* The people table becomes a list of cards */
        .adm-table--stack thead { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); }
        .adm-table--stack, .adm-table--stack tbody, .adm-table--stack tr, .adm-table--stack td { display: block; width: 100%; }
        .adm-table--stack tr { padding: 12px 0; border-bottom: 1px solid var(--hair-soft); }
        .adm-table--stack td { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 4px 0; border: 0; }
        .adm-table--stack td[data-label]::before { content: attr(data-label); color: var(--muted); font-size: 13px; }
    }
    @media (max-width: 575.98px) {
        .adm { padding: 16px; border-radius: 12px; }
        .adm-stack { gap: 16px; }
        .adm-row-4 { gap: 12px; }
        .adm-card { padding: 16px; }
        .adm-kpi { padding: 14px 16px; }
        .adm-kpi-value, .adm-focus-num { font-size: 24px; line-height: 32px; }
        .adm-kpi-value--money { font-size: 20px; line-height: 30px; }
        .adm-head-actions, .adm-tabs { width: 100%; }
        .adm-tab { flex: 1; }
        /* Attention rows: the button drops below the text instead of squeezing it */
        .adm-item { flex-wrap: wrap; align-items: flex-start; }
        .adm-item .adm-item-body { flex-basis: calc(100% - 56px); }
        .adm-item .adm-btn { margin-left: 56px; }
        /* System status keeps its chip on the right */
        .adm-status .adm-item { flex-wrap: nowrap; }
        .adm-status .adm-item .adm-item-body { flex-basis: auto; }
        .adm-search { width: 100%; }
    }
</style>
