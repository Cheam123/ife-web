<?php 
    use Jenssegers\Agent\Agent;
$agent = new Agent();
?>

<style>
    .select2-selection--multiple {
        border: 1px solid #d3d3d3ff!important;
        box-shadow: #e6e6e6ff 1px 1px!important;
    }

    .overflowText {
        overflow            : hidden;
        text-overflow       : ellipsis;
        display             : -webkit-box;
        -webkit-line-clamp  : 2;
                line-clamp  : 2; 
        -webkit-box-orient  : vertical;
    }
    
    /* The Modal (background) */
    .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;

    }

    .modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
    }

    /* The Close Button */
    .close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    }

    .close:hover,
    .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
    }

    .center {
        margin: auto;
        background-color: white;
        padding: 70px 0;
        border: 3px solid green;
        height: 100%;
        padding-left: 40%;
        text-align: left;
    }
    .nav-pills .nav-link.inactive {
        padding: 10px;
    }
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        padding: 10px;
    }
    .card-body {
        flex: 1 1 auto;
        padding: 0.75rem 0.75rem;
    }
    hr {
        margin: 0.5rem 0;
        color: rgba(0, 0, 0, 0.1);
        background-color: currentColor;
        border: 0;
        opacity: 0.5;
    }
    .nav-pills .nav-link.inactive {
        color: #000000;
        background-color: #bedbe8;
    }
    .swal2-container {
        z-index: 2100; /* above the activity timeline modal (1050) and lightbox (2000) */
    }
    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
        margin: auto;
        width: 90%!important;
    }

    #myImg:hover {
        opacity: 0.7;
    }

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    /* Modal Content (image) */
    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    /* Caption of Modal Image */
    #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        height: 150px;
    }

    /* Add Animation */
    .modal-content, #caption {  
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.6s;
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
        from {-webkit-transform:scale(0)} 
        to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
        from {transform:scale(0)} 
        to {transform:scale(1)}
    }

    /* The Tutup Button */
    .tutup {
        position: absolute;
        top: 100px;
        right: 35px;
        color:rgb(0, 0, 0);
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px){
        .modal-content {
            width: 100%;
        }
    }

    /* ===== Sales Manager triage redesign (Eciatto warm theme) ===== */
    .triage-card {
        background: #ffffff;
        border: 1px solid #e6dfcf;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .triage-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .triage-table thead tr { background: #2a2a2a; color: #fff; }
    .triage-table thead th {
        padding: 9px 10px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 1px solid #555;
    }
    .triage-table td {
        padding: 10px 10px;
        font-size: 12px;
        vertical-align: middle;
        color: #333;
        border-bottom: 1px solid #e6dfcf;
        background: #fff;
    }
    .triage-table tr.alert td { background: #fef6f4; }
    .triage-strip { width: 4px; padding: 0 !important; border-right: none; }
    .triage-strip.danger { background: #c94a40; }
    .triage-strip.warn   { background: #d68a18; }

    .triage-num {
        width: 28px;
        color: #999;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    }

    .lead-name {
        font-weight: 700;
        color: #222;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .lead-mobile {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 11.5px;
        color: #666;
    }
    .lead-link, .task-link {
        background: transparent;
        border: none;
        color: #0d6efd;
        font-size: 10.5px;
        font-weight: 500;
        padding: 0;
        cursor: pointer;
        margin-top: 4px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .lead-link:hover, .task-link:hover { text-decoration: underline; }
    .lead-link i, .task-link i { font-size: 12px; }

    .task-title {
        font-size: 12.5px;
        font-weight: 600;
        color: #222;
        line-height: 1.35;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        white-space: normal;
    }
    .due-row { display: flex; align-items: center; gap: 6px; }
    .due-label { color: #666; font-size: 10.5px; }
    .pill-due {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 25px;
        background: #9e1225;
        color: #fff;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }
    .no-date { color: #999; font-size: 11px; font-style: italic; }

    .aging-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
    .aging-badge {
        padding: 3px 10px;
        border-radius: 99px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        border: 1px solid transparent;
    }
    .aging-badge.good   { background: #dff3e3; color: #076f24; border-color: #a8d5b3; }
    .aging-badge.warn   { background: #fff4e3; color: #9a5b07; border-color: #f1d8a9; }
    .aging-badge.danger { background: #fdeaea; color: #a3322b; border-color: #f1b8b3; }
    .aging-badge.info   { background: #e6f0fb; color: #12339e; border-color: #b5cbed; }
    .aging-count { font-size: 10.5px; color: #999; font-weight: 500; }

    .activity-preview {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        cursor: pointer;
        padding-top: 1px;
    }
    .activity-bullet {
        width: 18px; height: 18px; border-radius: 99px;
        display: inline-flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 1px;
        color: #fff; font-size: 10px;
    }
    .activity-bullet.call     { background: #0d6efd; }
    .activity-bullet.whatsapp { background: #22a45d; }
    .activity-bullet.note     { background: #7a4f2c; }
    .activity-text {
        font-size: 11.5px; color: #333; line-height: 1.35;
        white-space: normal;
    }
    .activity-text .label { font-weight: 600; }
    .activity-meta {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 10.5px;
        color: #888;
        margin-top: 1px;
    }
    .activity-empty {
        color: #888; font-size: 11px; font-style: italic;
    }

    .subscriber-cell { display: flex; align-items: center; gap: 7px; }
    .subscriber-avatar {
        width: 26px; height: 26px; border-radius: 99px; background: #076f24;
        display: inline-flex; align-items: center; justify-content: center;
        color: #fff; font-size: 10.5px; font-weight: 700;
        flex-shrink: 0;
    }
    .subscriber-name {
        font-size: 11.5px; color: #222; font-weight: 600;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 110px;
    }
    .subscriber-sub { font-size: 10px; color: #888; }

    .actions-cell { width: 170px; text-align: right; white-space: nowrap; }
    .actions-row { display: flex; gap: 6px; justify-content: flex-end; margin-bottom: 6px; }
    .qa-btn {
        background: #fff;
        width: 30px; height: 28px; border-radius: 4px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0;
    }
    .qa-btn.nudge  { border: 1px solid #c68607; color: #c68607; }
    .qa-btn.delete { border: 1px solid #c94a40; color: #c94a40; }
    .qa-btn i { font-size: 14px; line-height: 1; }
    .qa-btn.nudge i  { color: #c68607; }
    .qa-btn.delete i { color: #c94a40; }
    .qa-excel {
        background: #28a745; border: 1px solid #28a745; color: #fff;
        padding: 0 10px; border-radius: 4px; font-size: 11px; font-weight: 700;
        height: 28px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center; gap: 4px;
        text-decoration: none;
    }
    .qa-excel:hover { color: #fff; opacity: 0.92; }
    .qa-excel i { font-size: 14px; }
    .open-activity {
        background: #1e7eb8; color: #fff; border: none;
        padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: 600;
        cursor: pointer; width: 100%;
        display: inline-flex; align-items: center; justify-content: center; gap: 4px;
    }
    .open-activity:hover { background: #1a6b9d; color: #fff; }
    .unread-chip-row { display: flex; justify-content: flex-end; margin-bottom: 4px; }
    .unread-chip {
        padding: 2px 7px; border-radius: 99px;
        background: #ffdd00; color: #222; border: 1px solid #d4b300;
        font-size: 10px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 3px;
    }
    .unread-chip i { font-size: 11px; }
    .alert-flag { color: #c94a40; font-size: 14px; }

    /* ===== Triage modals (lead/task details + activity) ===== */
    .triage-modal {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        overflow-y: auto;
        /* padding: 60px 20px; */
    }
    .triage-modal.show { display: flex; align-items: flex-start; justify-content: center; }
    .triage-modal-card {
        background: #fff; border: 1px solid #888;
        width: 100%; border-radius: 4px;
        max-height: 100%;
        display: flex; flex-direction: column; overflow: hidden;
        font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .triage-modal-card.lg { max-width: 1400px; width: 95%; }
    .triage-modal-head {
        padding: 14px 20px; border-bottom: 1px solid #ddd;
        display: flex; justify-content: space-between; align-items: flex-start;
    }
    .triage-modal-head .sub {
        font-size: 11px; color: #888; letter-spacing: 0.5px;
        text-transform: uppercase; font-weight: 600;
    }
    .triage-modal-head .title {
        font-size: 16px; font-weight: 700; color: #222; margin-top: 2px;
    }
    .triage-modal-close {
        background: none; border: none; cursor: pointer;
        font-size: 28px; font-weight: 700; color: #aaa; padding: 0; line-height: 1;
    }
    .triage-modal-body { padding: 20px; overflow-y: auto; }
    .section-header {
        background: #18016c; color: #fff; padding: 6px 10px;
        font-size: 11.5px; font-weight: 600; letter-spacing: 0.3px;
    }
    .detail-table {
        width: 100%; border-collapse: collapse;
        border: 1px solid #ddd; margin-bottom: 16px;
    }
    .detail-table td { padding: 5px 8px; font-size: 12px; vertical-align: top; }
    .detail-table td.k { color: #333; white-space: nowrap; width: 1px; }
    .detail-table td.s { width: 1px; }
    .detail-table td.v { color: #0d6efd; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 720px) { .detail-grid { grid-template-columns: 1fr; } }

    /* Activity modal — larger sizing */
    .am-head {
        background: #2a2a2a; color: #fff; padding: 12px 24px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .am-head .sub {
        font-size: 11px; color: #bbb; letter-spacing: 0.6px;
        text-transform: uppercase; font-weight: 600;
    }
    .am-head .title { font-size: 18px; font-weight: 700; margin-top: 2px; }
    .am-head .lead-name-am { color: #f59e0b; text-transform: uppercase; letter-spacing: 0.4px; }
    .am-head .sep { color: #fff; margin: 0 10px; }
    .am-head .task-title-am { color: #fff; font-weight: 500; font-size: 15px; }
    .am-body { display: grid; grid-template-columns: 1fr 240px; max-height: 90vh; }
    @media (max-width: 820px) { .am-body { grid-template-columns: 1fr; } }
    .am-main { padding: 16px 22px; overflow-y: auto; background: #fff; border-right: 1px solid #eee; }
    .am-aside { padding: 14px 14px; overflow-y: auto; background: #fafafa; }
    .composer {
        border: 1px solid #ddd; border-radius: 6px; padding: 12px 14px;
        background: #fbf8f1; margin-bottom: 14px;
        display: grid; grid-template-columns: auto 1fr auto; gap: 12px; align-items: start;
    }
    .composer-label {
        font-size: 12px; color: #7a4f2c; letter-spacing: 0.5px;
        text-transform: uppercase; font-weight: 700;
        padding-top: 10px; white-space: nowrap;
    }
    .composer textarea {
        width: 100%; border: 1px solid #ccc; border-radius: 4px;
        padding: 8px 10px; font-size: 14px; font-family: inherit; resize: vertical;
        min-height: 42px; outline: none; line-height: 1.45;
    }
    .composer-foot {
        display: flex; flex-direction: column; justify-content: flex-start; align-items: flex-end;
        gap: 6px;
    }
    @media (max-width: 820px) {
        .composer { grid-template-columns: 1fr; }
        .composer-foot { flex-direction: row; justify-content: flex-end; align-items: center; }
    }
    .composer-save {
        background: #1e7eb8; color: #fff; border: none;
        padding: 9px 22px; border-radius: 4px; font-size: 14px; font-weight: 600;
        cursor: pointer;
    }
    .timeline-label {
        font-size: 13px; color: #7a4f2c; letter-spacing: 0.5px;
        text-transform: uppercase; font-weight: 700; margin: 4px 0 10px;
    }
    .timeline-scroll {
        max-height: calc(90vh - 180px);
        min-height: 60vh;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 18px;
        background: #fff;
    }
    .timeline-scroll::-webkit-scrollbar { width: 10px; }
    .timeline-scroll::-webkit-scrollbar-thumb {
        background: #d4cdb8; border-radius: 5px;
    }
    .timeline-scroll::-webkit-scrollbar-track { background: transparent; }
    .tl-row { display: grid; grid-template-columns: 50px 1fr; position: relative; }
    .tl-rail { position: relative; display: flex; justify-content: center; padding-top: 2px; }
    .tl-rail .line {
        position: absolute; top: 38px; bottom: -10px;
        width: 2px; background: #dcd5c6;
    }
    .tl-dot {
        width: 38px; height: 38px; border-radius: 99px;
        display: inline-flex; align-items: center; justify-content: center;
        position: relative; z-index: 1; color: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        background: #7a4f2c;
    }
    .tl-dot i { font-size: 18px; }
    .tl-content { padding-bottom: 20px; padding-left: 8px; }
    .tl-head {
        display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap;
    }
    .tl-head .who { font-size: 16px; font-weight: 700; color: #222; }
    .tl-head .when {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 13.5px; color: #666;
    }

    /* Three-dot actions menu on a timeline message */
    .tl-menu { position: relative; margin-left: auto; }
    .tl-menu-btn {
        background: none; border: none; cursor: pointer;
        color: #8a8270; font-size: 18px; line-height: 1;
        padding: 2px 6px; border-radius: 6px;
        transition: color .15s ease, background .15s ease;
    }
    .tl-menu-btn:hover { color: #444; background: rgba(0,0,0,0.05); }
    .tl-menu-list {
        display: none; position: absolute; top: 100%; right: 0; margin-top: 4px;
        min-width: 130px; background: #fff; border: 1px solid #e6dfcf;
        border-radius: 8px; box-shadow: 0 8px 22px rgba(17,24,39,0.14);
        z-index: 20; overflow: hidden; padding: 4px;
    }
    .tl-menu.open .tl-menu-list { display: block; }
    .tl-menu-item {
        display: flex; align-items: center; gap: 8px; width: 100%;
        background: none; border: none; cursor: pointer; text-align: left;
        font-size: 13.5px; color: #333; padding: 8px 10px; border-radius: 6px;
    }
    .tl-menu-item:hover { background: #f6f2e7; }
    .tl-menu-item.danger { color: #c94a40; }
    .tl-menu-item.danger:hover { background: #fbecea; }
    .tl-menu-item i { font-size: 16px; }

    .tl-msg { font-size: 15px; color: #333; margin-top: 5px; line-height: 1.55; white-space: pre-wrap; }

    /* Inline edit box for a timeline message */
    .tl-edit { margin-top: 6px; }
    .tl-edit textarea {
        width: 100%; box-sizing: border-box; resize: vertical; min-height: 60px;
        font-size: 14px; color: #333; line-height: 1.5;
        border: 1px solid #d8d0bd; border-radius: 8px; padding: 8px 10px;
        font-family: inherit; background: #fffdf8;
    }
    .tl-edit-actions { display: flex; gap: 8px; margin-top: 6px; }
    .tl-edit-actions button {
        border: none; cursor: pointer; font-size: 13px; font-weight: 600;
        padding: 6px 14px; border-radius: 6px;
    }
    .tl-edit-save { background: #7a4f2c; color: #fff; }
    .tl-edit-save:disabled { opacity: .6; cursor: default; }
    .tl-edit-cancel { background: #ece6d8; color: #555; }
    /* Inline images inside a message (e.g. base64 from mobile) — render as clickable thumbnails */
    .tl-msg img {
        max-width: 160px !important; max-height: 160px !important; width: auto !important; height: auto !important;
        border-radius: 6px; border: 1px solid #e6dfcf; cursor: pointer;
        object-fit: cover; display: inline-block; margin-top: 6px; margin-right: 6px;
        vertical-align: top;
    }
    .tl-attachments { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 8px; }
    .tl-attachment img {
        max-width: 160px; max-height: 160px; height: auto; border-radius: 6px;
        border: 1px solid #e6dfcf; cursor: pointer; object-fit: cover;
    }
    .tl-attachment video { max-width: 240px; border-radius: 6px; }
    .tl-attachment a.tl-file {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; color: #0d6efd; text-decoration: none;
        border: 1px solid #e6dfcf; border-radius: 6px; padding: 6px 10px; background: #fbf8f1;
    }
    .tl-attachment a.tl-file:hover { text-decoration: underline; }

    /* WhatsApp-style full-screen image viewer */
    .tl-lightbox {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.92);
        z-index: 2000;
        align-items: center; justify-content: center;
        padding: 40px;
    }
    .tl-lightbox.show { display: flex; }
    .tl-lightbox img {
        max-width: 100%; max-height: 100%;
        object-fit: contain;
        border-radius: 4px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.5);
    }
    .tl-lightbox-close {
        position: absolute; top: 18px; right: 28px;
        color: #fff; font-size: 42px; font-weight: 700;
        line-height: 1; cursor: pointer; background: none; border: none;
        z-index: 2001;
    }
    .tl-lightbox-close:hover { color: #ccc; }
    .timeline-empty {
        padding: 40px; text-align: center; color: #888;
        border: 1px dashed #ccc; border-radius: 6px; font-size: 15px;
    }
    /* Compact sidebar typography in activity modal */
    .am-aside .section-header { font-size: 12px; padding: 6px 10px; }
    .am-aside .detail-table { margin-bottom: 10px; }
    .am-aside .detail-table td { padding: 4px 8px; font-size: 12px; }
    .am-aside .upcoming-item { padding: 6px 10px; }
    .am-aside .upcoming-item .ui-label { font-size: 10.5px; }
    .am-aside .upcoming-item .ui-when { font-size: 12px; }
    .upcoming-item {
        background: #fff; padding: 6px 10px; border-radius: 4px;
        margin-bottom: 6px;
    }
    .upcoming-item .ui-label {
        font-size: 10px; color: #888; text-transform: uppercase;
        letter-spacing: 0.4px; font-weight: 700;
    }
    .upcoming-item .ui-when { font-size: 12px; color: #222; font-weight: 600; margin-top: 2px; }
    .upcoming-item.appt { border: 1px solid rgba(18,51,158,0.2); border-left: 3px solid #12339e; }
    .upcoming-item.rem  { border: 1px solid rgba(198,134,7,0.2);  border-left: 3px solid #c68607; }
    .upcoming-item.due  { border: 1px solid rgba(158,18,37,0.2);  border-left: 3px solid #9e1225; }

    .remark-list { margin: 0; padding-left: 18px; font-size: 11.5px; color: #333; line-height: 1.55; }
    .remark-list li { margin-bottom: 3px; }

    /* ===== Full-screen activity modal ===== */
    .triage-modal-card.fs {
        max-width: 100%;
        width: 100%;
        height: 100vh;
        max-height: 100vh;
        border: none;
        border-radius: 0;
    }
    .triage-modal-card.fs .am-body {
        flex: 1 1 auto;
        min-height: 0;
        max-height: none;
        grid-template-columns: 1fr 300px;
    }
    @media (max-width: 820px) {
        .triage-modal-card.fs .am-body { grid-template-columns: 1fr; }
    }
    .triage-modal-card.fs .am-main,
    .triage-modal-card.fs .am-aside { min-height: 0; }
    .triage-modal-card.fs .timeline-scroll {
        max-height: none;
        min-height: 0;
        flex: 1 1 auto;
    }
    .triage-modal-card.fs .am-main {
        display: flex;
        flex-direction: column;
    }
    /* Compact single-line composer */
    .triage-modal-card.fs .composer {
        display: flex; flex-wrap: wrap; align-items: flex-end;
        column-gap: 10px; row-gap: 0;
        padding: 8px 12px; margin-bottom: 8px;
    }
    .triage-modal-card.fs .composer-label { align-self: center; }
    .triage-modal-card.fs .composer-label { padding-top: 0; font-size: 11px; white-space: nowrap; }
    .triage-modal-card.fs .composer textarea {
        flex: 1 1 auto; width: auto; min-width: 200px;
        height: 38px; min-height: 38px; max-height: 140px; padding: 8px 12px;
        font-size: 13px; resize: none; overflow-y: hidden; line-height: 1.4;
    }
    .triage-modal-card.fs .composer-attach {
        flex: 0 0 auto; width: 44px; height: 38px; padding: 0; justify-content: center;
    }
    .triage-modal-card.fs .composer-attach i { font-size: 18px; }
    .triage-modal-card.fs .composer-save { flex: 0 0 auto; height: 38px; }
    .triage-modal-card.fs .composer-status { flex-basis: 100%; font-size: 12.5px; color: #999; }
    .triage-modal-card.fs .composer-status:not(:empty) { margin-top: 6px; }
    .triage-modal-card.fs .composer-preview { flex-basis: 100%; margin-top: 0; }
    .triage-modal-card.fs .composer-preview:not(:empty) { margin-top: 8px; }
    .triage-modal-card.fs .timeline-label { margin: 0 0 6px; }

    /* ===== Composer attachments ===== */
    .composer-attach {
        background: #fff; border: 1px dashed #c9a96a; color: #7a4f2c;
        padding: 7px 14px; border-radius: 4px; font-size: 13px; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    }
    .composer-attach:hover { background: #fbf3e4; }
    .composer-attach i { font-size: 16px; }
    .composer-hint { font-size: 11.5px; color: #999; margin-left: 10px; }
    .composer-preview {
        display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px;
    }
    .composer-chip {
        display: inline-flex; align-items: center; gap: 8px;
        background: #fff; border: 1px solid #e6dfcf; border-radius: 6px;
        padding: 5px 8px; max-width: 240px;
    }
    .composer-chip .thumb {
        width: 34px; height: 34px; border-radius: 4px; object-fit: cover;
        flex-shrink: 0; background: #f1ece0;
        display: inline-flex; align-items: center; justify-content: center;
        color: #7a4f2c; font-size: 18px;
    }
    .composer-chip .meta { min-width: 0; }
    .composer-chip .fname {
        font-size: 12px; color: #333; font-weight: 600;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;
    }
    .composer-chip .fsize { font-size: 10.5px; color: #999; }
    .composer-chip .rm {
        background: none; border: none; color: #c94a40; cursor: pointer;
        font-size: 18px; line-height: 1; padding: 0 2px; font-weight: 700;
    }
</style>

<div>
    <form class="form" id="filter" action="{{ route('tasks.index2') }}" method="GET">
        <div class="custom-font-small">
            <div id="statusRow1" name="statusRow1" class="mb-2">
                <div class="container-fluid">
                    <div class="row">
                        @if(!$agent->isMobile())
                            <!-- title -->
                            <div class="col-lg-3 col-sm-12">
                                <div class="mb-3">
                                    <label for="filter_title" class="custom-font-xsmall">Task Title:</label><br/>
                                    <input type="text" maxlength="500" class="form-control form-control-sm custom-font-xsmall" name="filter_title" id="filter_title" value="{{ old('filter_title', $request->filter_title) }}" placeholder="">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <!-- reference no -->
                                <div class="mb-3">
                                    <label for="filter_reference_no" class="custom-font-xsmall">@lang('translation.reference_no'):</label><br/>
                                    <input type="text" maxlength="21" class="form-control form-control-sm custom-font-xsmall" name="filter_reference_no" id="filter_reference_no" value="{{ old('filter_reference_no', $request->filter_reference_no) }}" placeholder="">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <!-- subscriber -->
                                <div class="mb-3">
                                    <label for="filter_subscriber" class="custom-font-xsmall">Subscriber:</label><br/>
                                    <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_subscriber" id="filter_subscriber" style="width:100%">
                                        <option value="">-- @lang('translation.select_your_choice') --</option>
                                        @foreach($users as $key => $u)
                                            <option value="{{ $u->id }}" @if (old('filter_subscriber', $request->filter_subscriber) == $u->id) selected @endif>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <!-- sub-subscriber -->
                                <div class="mb-3">
                                    <label for="filter_subsubscriber" class="custom-font-xsmall">Sub-Subscriber:</label><br/>
                                    <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_subsubscriber" id="filter_subsubscriber" style="width:100%">
                                        <option value="">-- @lang('translation.select_your_choice') --</option>
                                        @foreach($users as $key => $u)
                                            <option value="{{ $u->id }}" @if (old('filter_subsubscriber', $request->filter_subsubscriber) == $u->id) selected @endif>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-3 col-sm-12" style="text-align: right;">
                            <a class="btn custom-button-position custom-align-button-sidebyside custom-button-shadow" id="btnExpendRow" name="btnExpendRow" data-bs-toggle="collapse" data-bs-target="#statusRow2" aria-expanded="true" aria-controls="statusRow2"><i class="material-icons">unfold_more</i></a>
                            <button id="btnSearch" class="btn custom-button-position custom-align-button-sidebyside custom-button-shadow" type="submit"><i class="material-icons">search</i></button>
                            <button class="btn custom-button-position custom-align-button-sidebyside custom-button-shadow" type="reset"  id="querystring"><i class="material-icons">delete_sweep</i></button>
                            <button id="btnIfeArea" class="btn btn-sm btn-primary custom-button-shadow" type="button" style="padding-top: 9px; padding-bottom: 9px; margin-right: 4px; width:120px">IFE Area Code</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="statusRow2" name="statusRow2" class="collapse {{ isset($_COOKIE['expendRowTwo']) ? ($_COOKIE['expendRowTwo'] == '1' ? 'show' : '') : '' }}">
                <div class="container-fluid" style="padding-top:10px;">
                    @if($agent->isMobile())
                    <div class="row">
                        <!-- title -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="filter_title" class="custom-font-xsmall">Task Title:</label><br/>
                                <input type="text" maxlength="500" class="form-control form-control-sm custom-font-xsmall" name="filter_title" id="filter_title" value="{{ old('filter_title', $request->filter_title) }}" placeholder="">
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-12">
                            <!-- reference no -->
                            <div class="mb-3">
                                <label for="filter_reference_no" class="custom-font-xsmall">Task Reference:</label><br/>
                                <input type="text" maxlength="20" class="form-control form-control-sm custom-font-xsmall" name="filter_reference_no" id="filter_reference_no" value="{{ old('filter_reference_no', $request->filter_reference_no) }}" placeholder="">
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-12">
                            <!-- subscriber -->
                            <div class="mb-3">
                                <label for="filter_subscriber" class="custom-font-xsmall">Subscriber:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_subscriber" id="filter_subscriber" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_subscriber', $request->filter_subscriber) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-12">
                            <!-- sub-subscriber -->
                            <div class="mb-3">
                                <label for="filter_subsubscriber" class="custom-font-xsmall">Sub-Subscriber:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_subsubscriber" id="filter_subsubscriber" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_subsubscriber', $request->filter_subsubscriber) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <!-- With Sales -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="filter_withsales" class="custom-font-xsmall">With Sales:</label><br/>
                                <select class="js-select2-with-sales form-control form-control-sm custom-font-xsmall" name="filter_withsales" id="filter_withsales" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    <option value="Y" @if (old('filter_withsales', $request->filter_withsales) == 'Y') selected @endif>Yes</option>
                                    <option value="N" @if (old('filter_withsales', $request->filter_withsales) == 'Y') selected @endif>No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- name -->
                            <div class="mb-3">
                                <label for="filter_name" class="custom-font-xsmall">Lead/Customer Name:</label><br/>
                                <select class="form-control form-select custom-font-small js-leadname-multiple" name="filter_name[]" id="filter_name" multiple style="width:100%;">
                                    @if(null !== $request->filter_name)
                                        @foreach($leadNameOptions as $c)
                                        <option value="{{ $c }}" {{ array_search($c, $request->filter_name) === false ? '' : 'selected' }}>{{ $c }}</option>
                                        @endforeach
                                    @else
                                        @foreach($leadNameOptions as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- Appointment date -->
                            <div class="mb-3">
                                <label for="appointment_date_start" class="custom-font-xsmall">Appointment Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker7" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker7">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="appointment_date_start" id="appointment_date_start" placeholder="Start Date" value="{{ old('appointment_date_start', $request->appointment_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="appointment_date_end" id="appointment_date_end" placeholder="End Date" value="{{ old('appointment_date_end', $request->appointment_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- alert -->
                            <div class="mb-3">
                                <label for="filter_alert" class="custom-font-xsmall">Alert Indicator ON:</label><br/>
                                <select class="form-control form-select custom-font-small js-alertind" name="filter_alert" id="filter_alert" style="width:100%;">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    <option value="1" {{ old('filter_alert', $request->filter_alert) == 1 ? 'selected' : '' }}>NO</option>
                                    <option value="2" {{ old('filter_alert', $request->filter_alert) == 2 ? 'selected' : '' }}>YES</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Customer ID -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="filter_cid" class="custom-font-xsmall">Customer ID:</label><br/>
                                <input type="text" maxlength="24" class="form-control form-control-sm custom-font-xsmall" name="filter_cid" id="filter_cid" value="{{ old('filter_cid', $request->filter_cid) }}" placeholder="">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <label for="filter_ifearea" class="custom-font-xsmall">IFE Area Code:</label><br/>
                            <select class="js-select2-area_city form-control custom-font-small" name="filter_ifearea" id="filter_ifearea" style="width:100%;">
                                <option value="">-- Select IFE Area --</option>
                                @foreach($ifeareas as $c)
                                    <option value="{{ $c->id }}" {{ old('filter_ifearea', $request->filter_ifearea) == $c->id ? 'selected' : '' }}>{{ $c->area }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <!-- source -->
                            <div class="mb-3">
                                <label for="filter_source" class="custom-font-xsmall">Source:</label><br/>
                                <select class="js-select2-source form-control form-control-sm custom-font-xsmall" name="filter_source" id="filter_source" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach(\App\Helpers\Helper::getLeadSourceListing() as $key => $leadsource)
                                    <option value="{{ $key }}" {{ old('filter_source', $request->filter_source) == $key ? 'selected' : '' }}>{{ $leadsource }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- creator -->
                            <div class="mb-3">
                                <label for="filter_creator" class="custom-font-xsmall">Creator:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_creator" id="filter_creator" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_creator', $request->filter_creator) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- checker -->
                            <div class="mb-3">
                                <label for="filter_checker" class="custom-font-xsmall">Checker:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_checker" id="filter_checker" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_checker', $request->filter_checker) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- owner -->
                            <div class="mb-3">
                                <label for="filter_owner" class="custom-font-xsmall">Owner:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_owner" id="filter_owner" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_owner', $request->filter_owner) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <!-- viewer -->
                            <div class="mb-3">
                                <label for="filter_viewer" class="custom-font-xsmall">Viewer:</label><br/>
                                <select class="js-select2-users form-control form-control-sm custom-font-xsmall" name="filter_viewer" id="filter_viewer" style="width:100%">
                                    <option value="">-- @lang('translation.select_your_choice') --</option>
                                    @foreach($users as $key => $u)
                                        <option value="{{ $u->id }}" @if (old('filter_viewer', $request->filter_viewer) == $u->id) selected @endif>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- busienss category -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="filter_business_category" class="custom-font-xsmall">Business Category:</label><br/>
                                <select class="js-select2-business-category form-control form-control form-control-sm custom-font-xsmall" name="filter_business_category" id="filter_business_category" style="width:100%">
                                    <option value="">-- @lang('translation.select_company_type') --</option>
                                    <option value="1" {{ old('filter_business_category', $request->filter_business_category) == '1' ? 'selected' : '' }}>Bar</option>
                                    <option value="2" {{ old('filter_business_category', $request->filter_business_category) == '2' ? 'selected' : '' }}>Cafe</option>
                                    <option value="3" {{ old('filter_business_category', $request->filter_business_category) == '3' ? 'selected' : '' }}>Hotel</option>
                                    <option value="4" {{ old('filter_business_category', $request->filter_business_category) == '4' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- last updated date -->
                        <div class="col-lg-3 col-sm-12">
                            <div class="mb-3">
                                <label for="updated_date_start" class="custom-font-xsmall">Last Updated Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="updated_date_start" id="updated_date_start" placeholder="Start Date" value="{{ old('updated_date_start', $request->updated_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="updated_date_end" id="updated_date_end" placeholder="End Date" value="{{ old('updated_date_end', $request->updated_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <!-- submission date -->
                            <div class="mb-3">
                                <label for="start" class="custom-font-xsmall">@lang('translation.submission_date'):</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6" style="z-index:1000;">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="start" id="start" placeholder="Start Date" value="{{ old('start', $request->start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="end" id="end" placeholder="End Date" value="{{ old('end', $request->end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- complete date -->
                            <div class="mb-3">
                                <label for="complete_date_start" class="custom-font-xsmall">@lang('translation.complete_date'):</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="complete_date_start" id="complete_date_start" placeholder="Start Date" value="{{ old('complete_date_start', $request->complete_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="complete_date_end" id="complete_date_end" placeholder="End Date" value="{{ old('complete_date_end', $request->complete_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- inprogress date -->
                            <div class="mb-3">
                                <label for="inprogress_date_start" class="custom-font-xsmall">In Progress Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="inprogress_date_start" id="inprogress_date_start" placeholder="Start Date" value="{{ old('inprogress_date_start', $request->inprogress_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="inprogress_date_end" id="inprogress_date_end" placeholder="End Date" value="{{ old('inprogress_date_end', $request->inprogress_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- done date -->
                            <div class="mb-3">
                                <label for="done_date_start" class="custom-font-xsmall">Done Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="done_date_start" id="done_date_start" placeholder="Start Date" value="{{ old('done_date_start', $request->done_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="done_date_end" id="done_date_end" placeholder="End Date" value="{{ old('done_date_end', $request->done_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- verify date -->
                            <div class="mb-3">
                                <label for="verify_date_start" class="custom-font-xsmall">Verified Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="verify_date_start" id="verify_date_start" placeholder="Start Date" value="{{ old('verify_date_start', $request->verify_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="verify_date_end" id="verify_date_end" placeholder="End Date" value="{{ old('verify_date_end', $request->verify_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- reject date -->
                            <div class="mb-3">
                                <label for="reject_date_start" class="custom-font-xsmall">Rejected Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="reject_date_start" id="reject_date_start" placeholder="Start Date" value="{{ old('reject_date_start', $request->reject_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="reject_date_end" id="reject_date_end" placeholder="End Date" value="{{ old('reject_date_end', $request->reject_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <!-- KIV date -->
                            <div class="mb-3">
                                <label for="kiv_date_start" class="custom-font-xsmall">KIV Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="kiv_date_start" id="kiv_date_start" placeholder="Start Date" value="{{ old('kiv_date_start', $request->kiv_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="kiv_date_end" id="kiv_date_end" placeholder="End Date" value="{{ old('kiv_date_end', $request->kiv_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-lg-3 col-sm-12">
                            <!-- on hold date -->
                            <div class="mb-3">
                                <label for="onhold_date_start" class="custom-font-xsmall">On Hold Date:</label><br/>
                                <div class="mb-3">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="onhold_date_start" id="onhold_date_start" placeholder="Start Date" value="{{ old('onhold_date_start', $request->onhold_date_start) }}">
                                        <input type="text" class="form-control form-control-sm custom-font-xsmall" name="onhold_date_end" id="onhold_date_end" placeholder="End Date" value="{{ old('onhold_date_end', $request->onhold_date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <br />
            </div>
        </div>

        <hr />

        <input type="hidden" name="status" id="status" value="{{ old('status', $request->status) }}">

        <div class="d-flex justify-content-between nav-pills" style="overflow-x:auto; white-space: nowrap;">
            <div>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 1 ? 'active' : 'inactive' }}" onclick="filter_data(1)">
                        <span>New Task{{ ($all->where('status', '1')->first() !== NULL && $all->where('status', '1')->first()->cnt > 0) ? ' ( ' . $all->where('status', '1')->first()->cnt . ' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 2 ? 'active' : 'inactive' }}" onclick="filter_data(2)">
                        <span>Inprogress{{ ($all->where('status', '2')->first() !== NULL && $all->where('status', '2')->first()->cnt > 0) ? ' ( ' . $all->where('status', '2')->first()->cnt . ' )' : '' }}</span>
                    </a>
                </span>
                {{-- <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 8 ? 'active' : 'inactive' }}" onclick="filter_data(8)">
                        <span>On Hold{{ ($all->where('status', '8')->first() !== NULL && $all->where('status', '8')->first()->cnt > 0) ? ' ( ' . $all->where('status', '8')->first()->cnt . ' )' : '' }}</span>
                    </a>
                </span> --}}
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 3 ? 'active' : 'inactive' }}" onclick="filter_data(3)">
                        <span>Done{{ ($all->where('status', '3')->first() !== NULL && $all->where('status', '3')->first()->cnt > 0) ? ' ( ' . $all->where('status', '3')->first()->cnt . ' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 4 ? 'active' : 'inactive' }}" onclick="filter_data(4)">
                        <span>Verified{{ ($all->where('status', '4')->first() !== NULL && $all->where('status', '4')->first()->cnt > 0) ? ' ( ' . $all->where('status', '4')->first()->cnt . ' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 5 ? 'active' : 'inactive' }}" onclick="filter_data(5)">
                        <span>Completed{{ ($all->where('status', '5')->first() !== NULL && $all->where('status', '5')->first()->cnt > 0) ? ' ( ' . $all->where('status', '5')->first()->cnt . ' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 6 ? 'active' : 'inactive' }}" onclick="filter_data(6)">
                        <span>Keep In View{{ ($all->where('status', '6')->first() !== NULL && $all->where('status', '6')->first()->cnt > 0) ? ' ( ' . $all->where('status', '6')->first()->cnt . ' )' : '' }}</span>
                    </a>
                </span>
                <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
                    <a class="btn btn-sm nav-link {{ $request->status == 7 ? 'active' : 'inactive' }}" onclick="filter_data(7)">
                        <span>Rejected{{ ($all->where('status', '7')->first() !== NULL && $all->where('status', '7')->first()->cnt > 0) ? ' ( ' . $all->where('status', '7')->first()->cnt . ' )' : '' }}</span>
                    </a>
                </span>
            </div>
            <div class="d-flex">
                <div style="padding-top: 10px; width: 250px;">
                    <select class="js-sortby form-control form-control-sm custom-font-xsmall" name="sortby" id="sortby" onchange="refreshPage()">
                        <option value="last_follow_up" @if (old('sortby', $request->sortby) == 'last_follow_up') selected @endif>Sort By Last Updated Date</option>
                        <option value="reminder_date" @if (old('sortby', $request->sortby) == 'reminder_date') selected @endif>Sort By Reminder Date</option>
                        <option value="due_date" @if (old('sortby', $request->sortby) == 'due_date') selected @endif>Sort By Due Date</option>
                        <option value="created_at" @if (old('sortby', $request->sortby) == 'created_at') selected @endif>Sort By Created Date</option>
                        <option value="inprogress_date" @if (old('sortby', $request->sortby) == 'inprogress_date') selected @endif>Sort By In Progress Date</option>
                        <option value="complete_date" @if (old('sortby', $request->sortby) == 'complete_date') selected @endif>Sort By In Completed Date</option>
                        <option value="kiv_date" @if (old('sortby', $request->sortby) == 'kiv_date') selected @endif>Sort By KIV Date</option>
                        <option value="reject_date" @if (old('sortby', $request->sortby) == 'reject_date') selected @endif>Sort By Rejected Date</option>
                        {{-- <option value="onhold_date" @if (old('sortby', $request->sortby) == 'onhold_date') selected @endif>Sort By On Hold Date</option> --}}
                    </select>
                </div>
                <div style="padding-top: 10px; width: 120px;">
                    <select class="js-sortmode form-control form-control-sm custom-font-xsmall" name="sortmode" id="sortmode" onchange="refreshPage()">
                        <option value="desc" @if (old('sortmode', $request->sortmode) == 'desc') selected @endif>🔽 Desc</option>
                        <option value="asc" @if (old('sortmode', $request->sortmode) == 'asc') selected @endif>🔼 Asc</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div style="overflow-x:auto;">
        <div class="triage-card">
        <table class="triage-table">
            <thead>
                <tr>
                    <th class="triage-strip" style="width:4px;"></th>
                    <th style="width:32px;">#</th>
                    <th style="min-width:180px;">Lead</th>
                    <th style="min-width:240px;">Task</th>
                    <th style="min-width:300px;">Last Activity</th>
                    <th style="min-width:140px;">Subscriber</th>
                    <th style="width:170px; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $index => $s)
                                    @php
    $rowNum = (($tasks->links()->paginator->currentPage() - 1) * $tasks->links()->paginator->perPage()) + $loop->iteration;
    // Aging based on last_follow_up
    $lastFu = $s->last_follow_up ? strtotime($s->last_follow_up) : null;
    $daysSince = $lastFu ? floor((time() - $lastFu) / 86400) : null;
    if ($daysSince === null) {
        $agingTone = 'warn';
        $agingLabel = 'No contact';
    } elseif ($daysSince <= 1) {
        $agingTone = 'good';
        $agingLabel = $daysSince === 0 ? 'Today' : 'Yesterday';
    } elseif ($daysSince <= 3) {
        $agingTone = 'good';
        $agingLabel = $daysSince . ' days ago';
    } elseif ($daysSince <= 7) {
        $agingTone = 'warn';
        $agingLabel = $daysSince . ' days ago';
    } else {
        $agingTone = 'danger';
        $agingLabel = 'Stale · ' . $daysSince . ' days';
    }
    if ($s->alert == 2) {
        $agingTone = 'danger';
    }

    $stripClass = '';
    if ($s->alert == 2 || $agingTone === 'danger') {
        $stripClass = 'danger';
    } elseif ($agingTone === 'warn') {
        $stripClass = 'warn';
    }

    $latestComment = $s->comments->sortByDesc('created_at')->first();
    $activityCount = $s->comments->count();
    $subscriberUser = optional(optional($s->users->where('role', 2)->first())->user);
    $subInitials = $subscriberUser->name
        ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', explode(' ', $subscriberUser->name)[0] ?? ''), 0, 1)
            . substr(preg_replace('/[^A-Za-z]/', '', explode(' ', $subscriberUser->name)[1] ?? ''), 0, 1))
        : '?';
    $unread = $s->has_unread_notification->count();
    $detailsId = 'details-modal-' . $s->id;
    $activityId = 'activity-modal-' . $s->id;
                                    @endphp
                                    <tr class="{{ $s->alert == 2 ? 'alert' : '' }}">
                                        {{-- Aging strip --}}
                                        <td class="triage-strip {{ $stripClass }}"></td>

                                        {{-- # --}}
                                        <td class="triage-num">{{ str_pad($rowNum, 2, '0', STR_PAD_LEFT) }}</td>
                                        {{-- Lead --}}
                                        <td>
                                            <div class="lead-name">
                                                <span>{{ $s->lead->name }}</span>
                                                @if($s->alert == 2)
                                                    <span class="alert-flag" title="Flagged for attention">⚠</span>
                                                @endif
                                            </div>
                                            <div class="lead-mobile">{{ $s->lead->mobile }}</div>
                                            <button type="button" class="lead-link" onclick="openTriageModal('{{ $detailsId }}')">
                                                <i class="mdi mdi-information-outline"></i> Lead details
                                            </button>
                                        </td>

                                        {{-- Task --}}
                                        <td>
                                            <div class="task-title">{{ $s->title }}</div>
                                            @if($s->due_date)
                                                <div class="due-row">
                                                    <span class="due-label">Due</span>
                                                    <span class="pill-due">{{ date('Y-m-d, h:i A', strtotime($s->due_date . ' ' . $s->due_time)) }}</span>
                                                </div>
                                            @else
                                                <div class="no-date">No due date</div>
                                            @endif
                                            <button type="button" class="task-link" onclick="openTriageModal('{{ $detailsId }}')">
                                                <i class="mdi mdi-information-outline"></i> Task details
                                            </button>
                                        </td>

                                        {{-- Last Activity --}}
                                        <td>
                                            <div class="aging-row">
                                                <span class="aging-badge {{ $agingTone }}">{{ $agingLabel }}</span>
                                                @if($activityCount > 0)
                                                    <span class="aging-count">{{ $activityCount }} activit{{ $activityCount === 1 ? 'y' : 'ies' }}</span>
                                                @endif
                                            </div>
                                            @if($latestComment)
                                                <div class="activity-preview" onclick="openTriageModal('{{ $activityId }}')">
                                                    <span class="activity-bullet note"><i class="mdi mdi-comment-text-outline"></i></span>
                                                    <div style="min-width:0; flex:1;">
                                                        <div class="activity-text">
                                                            <span class="label">{{ optional($latestComment->submitBy)->name ?: 'User' }}</span>
                                                            @php $plain = trim(strip_tags($latestComment->message ?? '')); @endphp
                                                            @if($plain !== '')
                                                                — {{ \Illuminate\Support\Str::limit($plain, 80) }}
                                                            @endif
                                                        </div>
                                                        <div class="activity-meta">
                                                            {{ date('d/m/y · h:i A', strtotime($latestComment->created_at)) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="activity-empty">No follow-up yet — log first activity</div>
                                            @endif
                                        </td>

                                        {{-- Subscriber --}}
                                        <td>
                                            @php $sub = $s->users->where('role', 2)->first(); @endphp
                                            @if($sub)
                                                @php $rate = $s->rating->where('user_id', $sub->user_id)->first(); @endphp
                                                <div class="subscriber-cell" onclick="appointmentList('{{ $sub->user->name }}','{{ $s->id }}','{{ $sub->user_id }}','{{ $sub->role }}')" style="cursor:pointer;">
                                                    <span class="subscriber-avatar">{{ $subInitials }}</span>
                                                    <div style="min-width:0;">
                                                        <div class="subscriber-name">{{ $sub->user->name }}{{ $rate ? ' (' . $rate->rate . ')' : '' }}</div>
                                                        @if($s->users->where('role', 6)->count() > 0)
                                                            <div class="subscriber-sub">+{{ $s->users->where('role', 6)->count() }} sub</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Actions: unread + Nudge / Delete / Excel + Open Activity --}}
                                        <td class="actions-cell">
                                            {{-- @if($unread > 0)
                                                <div class="unread-chip-row">
                                                    <span class="unread-chip"><i class="mdi mdi-email-outline"></i> {{ $unread }} unread</span>
                                                </div>
                                            @endif --}}
                                            <div class="actions-row">
                                                 {{-- @if($s->status == 1 || $s->status == 2 || $s->status == 3 || $s->status == 4 || $s->status == 8)
                                                    @if($s->users->whereIn('role', [1, 3, 4])->where('user_id', Auth::guard('web')->user()->id)->count() > 0 || Auth::guard('web')->user()->type == 0)
                                                        <a href="edit/{{ $s->id }}?{{ $queryString }}" title="Edit" style="color: white; padding: 5px 10px; text-decoration: none;" class="qa-btn nudge"><i class="mdi mdi-square-edit-outline" style="font-size:18px;"></i></a>
                                                    @endif
                                                @endif --}}

                                                 @if($s->status == 1 || $s->status == 2 || $s->status == 3 || $s->status == 4 || $s->status == 8)
                                                    @if($s->users->whereIn('role', [1, 3, 4])->where('user_id', Auth::guard('web')->user()->id)->count() > 0 || Auth::guard('web')->user()->type == 0)
                                                        <a href="edit/{{ $s->id }}?{{ $queryString }}" style="line-height: 1;"><i class="mdi mdi-square-edit-outline" style="font-size: 22px;"></i></a>
                                                    @endif
                                                    <a href="view/{{ $s->id }}?{{ $queryString }}" style="line-height: 1;"><i class="mdi mdi-clipboard-outline" style="font-size: 22px;"></i></a>
                                                @else
                                                    <a href="view/{{ $s->id }}?{{ $queryString }}" style="line-height: 1;"><i class="mdi mdi-clipboard-outline" style="font-size: 22px;"></i></a>
                                                @endif

                                                @php
    $canDelete = ($s->status == 1 && $s->users->whereIn('role', [1, 4])->where('user_id', Auth::guard('web')->user()->id)->count() > 0)
        || ($s->status == 2 && Auth::guard('web')->user()->type == 1);
                                                @endphp
                                                @if($canDelete)
                                                    <button type="button" class="qa-btn delete" title="Delete" onclick="deleteTask({{ $s->id }})">
                                                        <i class="mdi mdi-trash-can"></i>
                                                    </button>
                                                @endif
                                                @if(
        ($s->status == 6 && $s->users->whereIn('role', [4])->where('user_id', Auth::guard('web')->user()->id)->count() > 0) ||
        ($s->status == 6 && Auth::guard('web')->user()->type == 1) ||
        ($s->status == 6 && Auth::guard('web')->user()->type == 2)
    )
                                                    <a href="edit/{{ $s->id }}?{{ $queryString }}" title="Reactivate" style="align-self:center; margin-right:4px;"><i class="mdi mdi-recycle-variant" style="color:#c94a40; font-size:18px;"></i></a>
                                                @endif

                                                @if(
        ($s->status == 8 && $s->users->whereIn('role', [2])->where('user_id', Auth::guard('web')->user()->id)->count() > 0) ||
        ($s->status == 8 && Auth::guard('web')->user()->type == 1) ||
        ($s->status == 8 && Auth::guard('web')->user()->type == 2)
    )
                                                    <button type="button" title="Reactivate" onclick="reactivateTask({{ $s->id }})" style="border:none; background:transparent; align-self:center; margin-right:4px;"><i class="mdi mdi-recycle-variant" style="color:#c94a40; font-size:18px;"></i></button>
                                                @endif

                                                <a download href="{{ route('tasks.export_single_task', ['id' => $s->id]) }}" class="qa-excel" title="Export Excel">
                                                    <i class="mdi mdi-download"></i> Excel
                                                </a>
                                            </div>

                                            <div style="display:flex; gap:6px; justify-content:flex-end;">

                                                <button type="button" class="open-activity" onclick="openTriageModal('{{ $activityId }}')">
                                                    Open Activity <span>→</span>
                                                </button>

                                                {{-- <a href="view/{{ $s->id }}?{{ $queryString }}" title="View" style="color:#555; align-self:center; margin-left:4px;"><i class="mdi mdi-clipboard-outline" style="font-size:18px;"></i></a> --}}
                                            </div>
                                        </td>
                                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    {{ $tasks->withQueryString()->links() }}
</div>

{{-- ================= Triage modals (outside the table to avoid display:none on parent) ================= --}}
@foreach($tasks as $s)
    @php
    $detailsId = 'details-modal-' . $s->id;
    $activityId = 'activity-modal-' . $s->id;
    $activityCount = $s->comments->count();
    @endphp

                                    <div id="{{ $detailsId }}" class="triage-modal" onclick="closeTriageModal('{{ $detailsId }}')">
                                        <div class="triage-modal-card" onclick="event.stopPropagation()">
                                            <div class="triage-modal-head">
                                                <div>
                                                    <div class="sub">{{ $s->task_reference }}</div>
                                                    <div class="title">Lead & Task Details</div>
                                                </div>
                                                <button type="button" class="triage-modal-close" onclick="closeTriageModal('{{ $detailsId }}')">&times;</button>
                                            </div>
                                            <div class="triage-modal-body">
                                                <div class="detail-grid">
                                                    <section>
                                                        <div class="section-header">Lead / Customer</div>
                                                        <table class="detail-table"><tbody>
                                                            <tr><td class="k">Name</td><td class="s">:</td><td class="v">{{ $s->lead->name }}</td></tr>
                                                            <tr><td class="k">Customer ID</td><td class="s">:</td><td class="v">{{ $s->lead->customer_id ?: '—' }}</td></tr>
                                                            <tr><td class="k">Shop Name</td><td class="s">:</td><td class="v">{{ $s->lead->business_name ?: '—' }}</td></tr>
                                                            @if($s->lead->ife_area_id)
                                                            <tr><td class="k">IFE Area</td><td class="s">:</td><td class="v">{{ $s->lead->ifearea->area }}</td></tr>
                                                            @endif
                                                            <tr><td class="k">Mobile</td><td class="s">:</td><td class="v">{{ $s->lead->mobile }}</td></tr>
                                                            <tr><td class="k">Source</td><td class="s">:</td><td class="v">{{ \App\Helpers\Helper::getLeadSource($s->lead->source) }}</td></tr>
                                                            <tr><td class="k">Sales Amount</td><td class="s">:</td><td class="v">{{ $s->sales > 0 ? 'RM ' . number_format($s->sales, 2) : '—' }}</td></tr>
                                                        </tbody></table>
                                                    </section>
                                                    <section>
                                                        <div class="section-header">Task Information</div>
                                                        <table class="detail-table"><tbody>
                                                            <tr><td class="k">Title</td><td class="s">:</td><td class="v">{{ $s->title }}</td></tr>
                                                            <tr><td class="k">@lang('translation.reference_no')</td><td class="s">:</td><td class="v">{{ $s->task_reference }}</td></tr>
                                                            <tr><td class="k">Due Date</td><td class="s">:</td><td class="v">{{ $s->due_date ? date('Y-m-d, h:i A', strtotime($s->due_date . ' ' . $s->due_time)) : '—' }}</td></tr>
                                                            <tr><td class="k">Appointment</td><td class="s">:</td><td class="v">{{ $s->appointment_date ? date('Y-m-d, h:i A', strtotime($s->appointment_date)) : '—' }}</td></tr>
                                                            @php $r = $s->reminder->where('user_id', Auth::guard('web')->user()->id); @endphp
                                                            <tr><td class="k">Reminder</td><td class="s">:</td><td class="v">{{ $r->count() > 0 ? date('Y-m-d, h:i A', strtotime(\Carbon\Carbon::parse($r->first()->reminder_date . ' ' . $r->first()->reminder_time))) : '—' }}</td></tr>
                                                            <tr><td class="k">Status</td><td class="s">:</td><td class="v">{{ $s->getTaskStatus($s->status) }}</td></tr>
                                                            <tr><td class="k">Created</td><td class="s">:</td><td class="v">{{ date('Y-m-d, h:i A', strtotime($s->created_at)) }}</td></tr>
                                                            <tr><td class="k">Last updated</td><td class="s">:</td><td class="v">{{ date('Y-m-d, h:i A', strtotime($s->last_follow_up)) }}</td></tr>
                                                        </tbody></table>
                                                    </section>
                                                </div>

                                                <div class="section-header">Manage By</div>
                                                <table class="detail-table"><tbody>
                                                    @if($sub = $s->users->where('role', 2)->first())
                                                        <tr><td class="k">Subscriber</td><td class="s">:</td><td class="v">{{ $sub->user->name }}</td></tr>
                                                    @endif
                                                    @if($s->users->where('role', 6)->count() > 0)
                                                        <tr><td class="k">Sub-Subscriber(s)</td><td class="s">:</td><td class="v">
                                                            @foreach($s->users->where('role', 6) as $u){{ $u->user->name }}{{ !$loop->last ? ', ' : '' }}@endforeach
                                                        </td></tr>
                                                    @endif
                                                    @php
    $owners = [];
    foreach ($s->users->where('role', 4) as $u) {
        $owners[] = $u->user->name;
    }
                                                    @endphp
                                                    @if(count($owners) > 0)
                                                        <tr><td class="k">Owner(s)</td><td class="s">:</td><td class="v">{{ implode(', ', $owners) }}</td></tr>
                                                    @endif
                                                </tbody></table>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                    {{-- Activity Timeline modal for this row --}}
                                    <div id="{{ $activityId }}" class="triage-modal" onclick="closeTriageModal('{{ $activityId }}')">
                                        <div class="triage-modal-card lg fs" onclick="event.stopPropagation()" style="padding:0;">
                                            <div class="am-head">
                                                <div>

                                                    <div class="title">
                                                        <span class="lead-name-am">{{ $s->lead->name }}</span>
                                                        <span class="sep">·</span>
                                                        <span class="task-title-am">{{ $s->title }}</span>
                                                        <span class="sep"> </span>
                                                        <span class="sep"> </span>
                                                        <span class="sub">Follow-up Activity · {{ $s->task_reference }}</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="triage-modal-close" style="color:#fff;" onclick="closeTriageModal('{{ $activityId }}')">&times;</button>
                                            </div>
                                            <div class="am-body">
                                                <div class="am-main">
                                                    <div class="composer">
                                                        <div class="composer-label">+ Log follow-up</div>
                                                        <textarea id="composer-text-{{ $s->id }}" rows="1" oninput="autoGrowComposer(this)" placeholder="e.g. NPU / Spoke 5 min, interested in Type B / Appt set 8/3 1pm"></textarea>
                                                        <input type="file" id="composer-files-{{ $s->id }}" multiple style="display:none;" onchange="onComposerFiles({{ $s->id }})" />
                                                        <button type="button" class="composer-attach" title="Attach files — images, documents, video" onclick="document.getElementById('composer-files-{{ $s->id }}').click()">
                                                            <i class="mdi mdi-paperclip"></i>
                                                        </button>
                                                        <button type="button" class="composer-save" onclick="saveActivity({{ $s->id }})">Save activity</button>
                                                        <span id="composer-status-{{ $s->id }}" class="composer-status"></span>
                                                        <div id="composer-preview-{{ $s->id }}" class="composer-preview"></div>
                                                    </div>

                                                    <div class="timeline-label">Activity Timeline (<span id="timeline-count-{{ $s->id }}">{{ $activityCount }}</span>)</div>
                                                    <div class="timeline-scroll" id="timeline-scroll-{{ $s->id }}">
                                                        @if($activityCount === 0)
                                                            <div class="timeline-empty" id="timeline-empty-{{ $s->id }}">No activity yet.</div>
                                                        @else
                                                            @foreach($s->comments->sortByDesc('created_at') as $cIdx => $c)
                                                                @php
                                                                    $isLast = $cIdx === ($activityCount - 1);
                                                                    $tlCreatedIso = \Carbon\Carbon::parse($c->created_at)->toIso8601String();
                                                                    $tlCanModify = $c->submit_by == Auth::guard('web')->user()->id
                                                                        && \Carbon\Carbon::parse($c->created_at)->diffInMinutes(\Carbon\Carbon::now()) <= 15;
                                                                @endphp
                                                                <div class="tl-row" data-chatid="{{ $c->id }}" data-created="{{ $tlCreatedIso }}" data-message="{{ e($c->message) }}">
                                                                    <div class="tl-rail">
                                                                        @if(!$isLast)<span class="line"></span>@endif
                                                                        <span class="tl-dot"><i class="mdi mdi-comment-text-outline"></i></span>
                                                                    </div>
                                                                    <div class="tl-content">
                                                                        <div class="tl-head">
                                                                            <span class="who">{{ optional($c->submitBy)->name ?: 'User' }}</span>
                                                                            <span class="when">{{ date('d/m/y · h:i A', strtotime($c->created_at)) }}</span>
                                                                            @if($tlCanModify)
                                                                                <div class="tl-menu">
                                                                                    <button type="button" class="tl-menu-btn" title="Actions" onclick="toggleTlMenu(event, this)"><i class="mdi mdi-dots-vertical"></i></button>
                                                                                    <div class="tl-menu-list">
                                                                                        <button type="button" class="tl-menu-item" onclick="startEditActivity({{ $c->id }})"><i class="mdi mdi-pencil-outline"></i> Edit</button>
                                                                                        <button type="button" class="tl-menu-item danger" onclick="deleteActivity({{ $c->id }})"><i class="mdi mdi-trash-can-outline"></i> Delete</button>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        @php $tlPlain = trim(strip_tags($c->message ?? '')); @endphp
                                                                        @if($tlPlain !== '' || strpos($c->message ?? '', '<img') !== false)
                                                                            <div class="tl-msg">{!! $c->formated_message !!}</div>
                                                                        @endif
                                                                        @if($c->documentUploads->count() > 0)
                                                                            <div class="tl-attachments">
                                                                                @foreach($c->documentUploads as $doc)
                                                                                    @php
                    $tlExtArr = explode('.', $doc->filename);
                    $tlExt = strtolower($tlExtArr[count($tlExtArr) - 1]);
                    $tlImageFormat = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'bmp'];
                    $tlVideoFormat = ['mp4', 'webm', 'ogg', 'mov'];
                                                                                    @endphp
                                                                                    @php $tlDocUrl = route('lead.file.download', ['doc_id' => $doc->id]); @endphp
                                                                                    <div class="tl-attachment">
                                                                                        @if(in_array($tlExt, $tlImageFormat))
                                                                                            <img src="{{ $tlDocUrl }}" alt="{{ $doc->display_name }}" loading="lazy" onclick="openTlLightbox('{{ $tlDocUrl }}')" />
                                                                                        @elseif(in_array($tlExt, $tlVideoFormat))
                                                                                            <video controls>
                                                                                                <source src="{{ $tlDocUrl }}">
                                                                                                Your browser does not support the video tag.
                                                                                            </video>
                                                                                        @else
                                                                                            <a class="tl-file" href="{{ $tlDocUrl }}" target="_blank">
                                                                                                <i class="mdi mdi-paperclip"></i> {{ $doc->display_name }}
                                                                                            </a>
                                                                                        @endif
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>

                                                <aside class="am-aside">
                                                    <div class="section-header">Lead Snapshot</div>
                                                    <table class="detail-table"><tbody>
                                                        <tr><td class="k">Name</td><td class="s">:</td><td class="v">{{ $s->lead->name }}</td></tr>
                                                        <tr><td class="k">Mobile</td><td class="s">:</td><td class="v">{{ $s->lead->mobile }}</td></tr>
                                                        <tr><td class="k">Source</td><td class="s">:</td><td class="v">{{ \App\Helpers\Helper::getLeadSource($s->lead->source) }}</td></tr>
                                                        <tr><td class="k">Customer ID</td><td class="s">:</td><td class="v">{{ $s->lead->customer_id ?: '—' }}</td></tr>
                                                        <tr><td class="k">Status</td><td class="s">:</td><td class="v">{{ $s->getTaskStatus($s->status) }}</td></tr>
                                                    </tbody></table>

                                                    <div class="section-header">Upcoming</div>
                                                    <div style="display:flex; flex-direction:column; gap:6px; margin-top:8px; margin-bottom:14px;">
                                                        @if($s->appointment_date)
                                                            <div class="upcoming-item appt">
                                                                <div class="ui-label">Appointment</div>
                                                                <div class="ui-when">{{ date('Y-m-d, h:i A', strtotime($s->appointment_date)) }}</div>
                                                            </div>
                                                        @endif
                                                        @php $rr = $s->reminder->where('user_id', Auth::guard('web')->user()->id); @endphp
                                                        @if($rr->count() > 0)
                                                            <div class="upcoming-item rem">
                                                                <div class="ui-label">Reminder</div>
                                                                <div class="ui-when">{{ date('Y-m-d, h:i A', strtotime(\Carbon\Carbon::parse($rr->first()->reminder_date . ' ' . $rr->first()->reminder_time))) }}</div>
                                                            </div>
                                                        @endif
                                                        @if($s->due_date)
                                                            <div class="upcoming-item due">
                                                                <div class="ui-label">Due</div>
                                                                <div class="ui-when">{{ date('Y-m-d, h:i A', strtotime($s->due_date . ' ' . $s->due_time)) }}</div>
                                                            </div>
                                                        @endif
                                                        @if(!$s->appointment_date && $rr->count() === 0 && !$s->due_date)
                                                            <div style="color:#999; font-size:11.5px; font-style:italic;">None scheduled</div>
                                                        @endif
                                                    </div>
                                                </aside>
                                            </div>
                                        </div>
                                    </div>
@endforeach
{{-- ================= end triage modals ================= --}}

<!-- The Modal -->
<div id="myifearea" class="modal mt-3" style="z-index:50;">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" style="z-index:50;">&times;</span>
        <p>IFE Area Listing</p>

        <div class="modal-body">
            <table class="table table-sm custom-font-small" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
                <tr>
                    <td style="background-color: #18016cff; color: #ffffff; border: 1px solid;">Area Code</td>
                    <td style="background-color: #18016cff; color: #ffffff; border: 1px solid;">Description</td>
                </tr>
                @foreach($ifeareas as $c)
                <tr>
                    <td style="border: 1px solid;">{{ $c->area }}</td>
                    <td style="border: 1px solid;">{{ $c->description }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

<!-- The Modal -->
<div id="myModal" class="modal mt-3" style="z-index:50;">
    <span class="tutup" style="padding-right:30px;">&times;</span>
    <p id="content"></p>
</div>

<!-- Full-screen image viewer (WhatsApp-style) -->
<div id="tlLightbox" class="tl-lightbox" onclick="closeTlLightbox(event)">
    <button type="button" class="tl-lightbox-close" onclick="closeTlLightbox(event)">&times;</button>
    <img id="tlLightboxImg" src="" alt="" onclick="event.stopPropagation()" />
</div>

@section('script')
<script>
    function openTlLightbox(src) {
        var box = document.getElementById('tlLightbox');
        var img = document.getElementById('tlLightboxImg');
        if (!box || !img) return;
        img.src = src;
        box.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeTlLightbox(e) {
        if (e) e.stopPropagation();
        var box = document.getElementById('tlLightbox');
        var img = document.getElementById('tlLightboxImg');
        if (!box) return;
        box.classList.remove('show');
        if (img) img.src = '';
        // Keep scroll locked if an activity/details modal is still open behind the viewer.
        if (!document.querySelector('.triage-modal.show')) {
            document.body.style.overflow = '';
        }
    }

    // Inline images embedded inside a message body (e.g. base64 from mobile)
    // open the full-screen viewer just like uploaded attachments.
    document.addEventListener('click', function(e) {
        var t = e.target;
        if (t && t.tagName === 'IMG' && t.closest && t.closest('.tl-msg') && t.src) {
            e.preventDefault();
            openTlLightbox(t.src);
        }
    });

    function openTriageModal(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeTriageModal(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('show');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var lb = document.getElementById('tlLightbox');
            if (lb && lb.classList.contains('show')) {
                closeTlLightbox();
                return;
            }
            document.querySelectorAll('.triage-modal.show').forEach(function(m) {
                m.classList.remove('show');
            });
            document.body.style.overflow = '';
        }
    });

    function _escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function(c) {
            return ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' })[c];
        });
    }

    function _fmtDateDMY(iso) {
        var d = iso ? new Date(iso.replace(' ', 'T')) : new Date();
        if (isNaN(d.getTime())) d = new Date();
        var dd = String(d.getDate()).padStart(2,'0');
        var mm = String(d.getMonth()+1).padStart(2,'0');
        var yy = String(d.getFullYear()).slice(2);
        var h  = d.getHours(), m = d.getMinutes();
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return dd + '/' + mm + '/' + yy + ' · ' + String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ' ' + ampm;
    }

    // Per-task staged attachments, keyed by task id.
    var _composerFiles = {};
    var _docDownloadTpl = '{{ route('lead.file.download', ['doc_id' => 'DOC_ID_PLACEHOLDER']) }}';

    function _fmtFileSize(bytes) {
        if (!bytes && bytes !== 0) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    // WhatsApp-style auto-grow: expand with content up to the CSS max-height, then scroll.
    function autoGrowComposer(el) {
        if (!el) return;
        el.style.height = 'auto';
        var max = parseInt(window.getComputedStyle(el).maxHeight, 10) || 140;
        var next = Math.min(el.scrollHeight, max);
        el.style.height = next + 'px';
        el.style.overflowY = el.scrollHeight > max ? 'auto' : 'hidden';
    }

    function onComposerFiles(taskId) {
        var input = document.getElementById('composer-files-' + taskId);
        if (!input) return;
        if (!_composerFiles[taskId]) _composerFiles[taskId] = [];
        Array.prototype.forEach.call(input.files, function(f) {
            _composerFiles[taskId].push(f);
        });
        input.value = ''; // allow re-selecting the same file
        renderComposerPreview(taskId);
    }

    function removeComposerFile(taskId, idx) {
        if (_composerFiles[taskId]) {
            _composerFiles[taskId].splice(idx, 1);
            renderComposerPreview(taskId);
        }
    }

    function renderComposerPreview(taskId) {
        var box = document.getElementById('composer-preview-' + taskId);
        if (!box) return;
        var files = _composerFiles[taskId] || [];
        box.innerHTML = '';
        files.forEach(function(f, idx) {
            var chip = document.createElement('div');
            chip.className = 'composer-chip';
            var thumbHtml;
            if (f.type && f.type.indexOf('image/') === 0) {
                var url = URL.createObjectURL(f);
                thumbHtml = '<img class="thumb" src="' + url + '" alt="">';
            } else {
                thumbHtml = '<span class="thumb"><i class="mdi mdi-file-document-outline"></i></span>';
            }
            chip.innerHTML =
                thumbHtml +
                '<div class="meta">' +
                    '<div class="fname" title="' + _escapeHtml(f.name) + '">' + _escapeHtml(f.name) + '</div>' +
                    '<div class="fsize">' + _fmtFileSize(f.size) + '</div>' +
                '</div>' +
                '<button type="button" class="rm" title="Remove">&times;</button>';
            chip.querySelector('.rm').addEventListener('click', function() {
                removeComposerFile(taskId, idx);
            });
            box.appendChild(chip);
        });
    }

    function _attachmentHtml(doc) {
        var url  = _docDownloadTpl.replace('DOC_ID_PLACEHOLDER', encodeURIComponent(doc.docid));
        var name = doc.filename || 'Attachment';
        if (parseInt(doc.isChatImage, 10) === 1) {
            return '<div class="tl-attachment">' +
                '<img src="' + url + '" alt="' + _escapeHtml(name) + '" loading="lazy" onclick="openTlLightbox(\'' + url + '\')" />' +
                '</div>';
        }
        if (parseInt(doc.isChatVideo, 10) === 1) {
            return '<div class="tl-attachment"><video controls><source src="' + url + '">' +
                'Your browser does not support the video tag.</video></div>';
        }
        return '<div class="tl-attachment">' +
            '<a class="tl-file" href="' + url + '" target="_blank">' +
            '<i class="mdi mdi-paperclip"></i> ' + _escapeHtml(name) + '</a></div>';
    }

    function saveActivity(taskId) {
        var textEl   = document.getElementById('composer-text-' + taskId);
        var statusEl = document.getElementById('composer-status-' + taskId);
        var scrollEl = document.getElementById('timeline-scroll-' + taskId);
        var countEl  = document.getElementById('timeline-count-' + taskId);
        var emptyEl  = document.getElementById('timeline-empty-' + taskId);
        var btn      = textEl ? textEl.closest('.composer').querySelector('.composer-save') : null;
        if (!textEl) return;

        var msg   = (textEl.value || '').trim();
        var files = _composerFiles[taskId] || [];
        if (msg === '' && files.length === 0) {
            statusEl.textContent = 'Please type a follow-up note or attach a file.';
            statusEl.style.color = '#c94a40';
            return;
        }

        var token = document.querySelector('meta[name="csrf-token"]');
        token = token ? token.getAttribute('content') : '';

        var form = new FormData();
        form.append('_token', token);
        form.append('task_id', taskId);
        form.append('chat_message', msg);
        files.forEach(function(f, i) {
            form.append('file-' + i, f, f.name);
        });

        if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
        statusEl.textContent = '';
        statusEl.style.color = '#999';

        fetch('{{ route('chat.store') }}', {
            method: 'POST',
            body: form,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
        }).then(function(r) { return r.json(); }).then(function(resp) {
            if (btn) { btn.disabled = false; btn.textContent = 'Save activity'; }
            if (resp && resp.type === 'success') {
                var data = {};
                try { data = JSON.parse(resp.data || '{}'); } catch (e) { data = {}; }
                var who  = data.name || 'You';
                var when = _fmtDateDMY(data.created_at);

                var msgHtml = '';
                if (msg !== '') {
                    msgHtml = '<div class="tl-msg">' + _escapeHtml(msg).replace(/\n/g, '<br>') + '</div>';
                }

                var attachHtml = '';
                if (data.doc && data.doc.length) {
                    attachHtml = '<div class="tl-attachments">' +
                        data.doc.map(_attachmentHtml).join('') +
                        '</div>';
                }

                var chatId = data.chatid || '';
                var menuHtml = chatId ? (
                    '<div class="tl-menu">' +
                      '<button type="button" class="tl-menu-btn" title="Actions" onclick="toggleTlMenu(event, this)"><i class="mdi mdi-dots-vertical"></i></button>' +
                      '<div class="tl-menu-list">' +
                        '<button type="button" class="tl-menu-item" onclick="startEditActivity(' + chatId + ')"><i class="mdi mdi-pencil-outline"></i> Edit</button>' +
                        '<button type="button" class="tl-menu-item danger" onclick="deleteActivity(' + chatId + ')"><i class="mdi mdi-trash-can-outline"></i> Delete</button>' +
                      '</div>' +
                    '</div>'
                ) : '';

                var html = '' +
                    '<div class="tl-row" data-chatid="' + _escapeHtml(String(chatId)) + '" data-created="' + _escapeHtml(data.created_at || new Date().toISOString()) + '" data-message="' + _escapeHtml(msg) + '">' +
                      '<div class="tl-rail">' +
                        '<span class="line"></span>' +
                        '<span class="tl-dot"><i class="mdi mdi-comment-text-outline"></i></span>' +
                      '</div>' +
                      '<div class="tl-content">' +
                        '<div class="tl-head">' +
                          '<span class="who">' + _escapeHtml(who) + '</span>' +
                          '<span class="when">' + _escapeHtml(when) + '</span>' +
                          menuHtml +
                        '</div>' +
                        msgHtml +
                        attachHtml +
                      '</div>' +
                    '</div>';
                if (emptyEl) { emptyEl.remove(); }
                if (scrollEl) {
                    scrollEl.insertAdjacentHTML('afterbegin', html);
                    scrollEl.scrollTop = 0;
                }
                if (countEl) { countEl.textContent = (parseInt(countEl.textContent || '0', 10) + 1); }
                textEl.value = '';
                autoGrowComposer(textEl);
                _composerFiles[taskId] = [];
                renderComposerPreview(taskId);
                statusEl.textContent = '';
                if (window.showToast) showToast('Follow-up activity saved successfully.', 'success');
            } else {
                statusEl.textContent = '';
                var errMsg = (resp && resp.message) ? resp.message : 'Failed to save activity.';
                if (window.showToast) { showToast(errMsg, 'error'); }
                else { statusEl.textContent = errMsg; statusEl.style.color = '#c94a40'; }
            }
        }).catch(function(err) {
            if (btn) { btn.disabled = false; btn.textContent = 'Save activity'; }
            statusEl.textContent = '';
            if (window.showToast) { showToast('Network error — please retry.', 'error'); }
            else { statusEl.textContent = 'Network error — please retry.'; statusEl.style.color = '#c94a40'; }
        });
    }

    /* ---------- Timeline message actions (edit / delete, 15-min window) ---------- */

    // A message can only be edited/deleted within 15 minutes of being sent.
    function _tlWithin15(row) {
        var iso = row ? row.getAttribute('data-created') : null;
        if (!iso) return false;
        var created = new Date(iso).getTime();
        if (isNaN(created)) return false;
        return (Date.now() - created) <= 15 * 60 * 1000;
    }

    // Remove the actions menu from any row whose 15-minute window has expired.
    function _tlPruneExpiredMenus() {
        document.querySelectorAll('.tl-row .tl-menu').forEach(function (menu) {
            var row = menu.closest('.tl-row');
            if (!_tlWithin15(row)) { menu.remove(); }
        });
    }

    function _tlCloseAllMenus() {
        document.querySelectorAll('.tl-menu.open').forEach(function (m) { m.classList.remove('open'); });
    }

    function toggleTlMenu(event, btn) {
        event.stopPropagation();
        var menu = btn.closest('.tl-menu');
        var row  = btn.closest('.tl-row');
        // Window may have lapsed while the modal stayed open.
        if (!_tlWithin15(row)) {
            menu.remove();
            if (window.showToast) showToast('The 15-minute window to edit or delete this message has passed.', 'warning');
            return;
        }
        var isOpen = menu.classList.contains('open');
        _tlCloseAllMenus();
        if (!isOpen) menu.classList.add('open');
    }

    // Close menus when clicking elsewhere.
    document.addEventListener('click', function () { _tlCloseAllMenus(); });

    function startEditActivity(chatId) {
        _tlCloseAllMenus();
        var row = document.querySelector('.tl-row[data-chatid="' + chatId + '"]');
        if (!row) return;
        if (!_tlWithin15(row)) {
            var menu = row.querySelector('.tl-menu');
            if (menu) menu.remove();
            if (window.showToast) showToast('The 15-minute window to edit this message has passed.', 'warning');
            return;
        }
        var content = row.querySelector('.tl-content');
        if (content.querySelector('.tl-edit')) return; // already editing

        var current = row.getAttribute('data-message') || '';
        var msgEl   = content.querySelector('.tl-msg');

        var box = document.createElement('div');
        box.className = 'tl-edit';
        box.innerHTML =
            '<textarea>' + _escapeHtml(current) + '</textarea>' +
            '<div class="tl-edit-actions">' +
                '<button type="button" class="tl-edit-save">Save</button>' +
                '<button type="button" class="tl-edit-cancel">Cancel</button>' +
            '</div>';

        if (msgEl) { msgEl.style.display = 'none'; content.insertBefore(box, msgEl.nextSibling); }
        else { content.querySelector('.tl-head').insertAdjacentElement('afterend', box); }

        var ta = box.querySelector('textarea');
        ta.focus();
        ta.setSelectionRange(ta.value.length, ta.value.length);

        box.querySelector('.tl-edit-cancel').addEventListener('click', function () { _tlCloseEdit(row); });
        box.querySelector('.tl-edit-save').addEventListener('click', function () { saveEditActivity(chatId); });
    }

    function _tlCloseEdit(row) {
        var box   = row.querySelector('.tl-edit');
        var msgEl = row.querySelector('.tl-msg');
        if (box) box.remove();
        if (msgEl) msgEl.style.display = '';
    }

    function saveEditActivity(chatId) {
        var row = document.querySelector('.tl-row[data-chatid="' + chatId + '"]');
        if (!row) return;
        var box = row.querySelector('.tl-edit');
        if (!box) return;
        var ta  = box.querySelector('textarea');
        var saveBtn = box.querySelector('.tl-edit-save');
        var newMsg = (ta.value || '').trim();

        var token = document.querySelector('meta[name="csrf-token"]');
        token = token ? token.getAttribute('content') : '';

        var form = new FormData();
        form.append('_token', token);
        form.append('id', chatId);
        form.append('chat_message', newMsg);

        saveBtn.disabled = true; saveBtn.textContent = 'Saving...';

        fetch('{{ route('chat.update') }}', {
            method: 'POST',
            body: form,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
        }).then(function (r) { return r.json(); }).then(function (resp) {
            if (resp && resp.type === 'success') {
                var data = {};
                try { data = JSON.parse(resp.data || '{}'); } catch (e) { data = {}; }
                var msgEl = row.querySelector('.tl-msg');
                if (newMsg !== '') {
                    if (!msgEl) {
                        msgEl = document.createElement('div');
                        msgEl.className = 'tl-msg';
                        box.parentNode.insertBefore(msgEl, box);
                    }
                    msgEl.innerHTML = _escapeHtml(newMsg).replace(/\n/g, '<br>');
                } else if (msgEl) {
                    msgEl.remove();
                }
                row.setAttribute('data-message', newMsg);
                _tlCloseEdit(row);
                if (window.showToast) showToast('Follow-up activity updated successfully.', 'success');
            } else {
                saveBtn.disabled = false; saveBtn.textContent = 'Save';
                if (window.showToast) showToast((resp && resp.message) ? resp.message : 'Failed to update activity.', 'error');
            }
        }).catch(function () {
            saveBtn.disabled = false; saveBtn.textContent = 'Save';
            if (window.showToast) showToast('Network error — please retry.', 'error');
        });
    }

    function deleteActivity(chatId) {
        _tlCloseAllMenus();
        var row = document.querySelector('.tl-row[data-chatid="' + chatId + '"]');
        if (!row) return;
        if (!_tlWithin15(row)) {
            var menu = row.querySelector('.tl-menu');
            if (menu) menu.remove();
            if (window.showToast) showToast('The 15-minute window to delete this message has passed.', 'warning');
            return;
        }

        new swal({
            title: 'Please confirm to delete this follow-up log!',
            customClass: {
                container: 'text-class align-middle',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
                zIndex: 2000,
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (!result.isConfirmed) return;

            var token = document.querySelector('meta[name="csrf-token"]');
            token = token ? token.getAttribute('content') : '';

            var form = new FormData();
            form.append('_token', token);
            form.append('id', chatId);

            fetch('{{ route('chat.delete') }}', {
                method: 'POST',
                body: form,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
            }).then(function (r) { return r.json(); }).then(function (resp) {
                if (resp && resp.type === 'success') {
                    var scrollEl = row.closest('.timeline-scroll');
                    var countEl  = scrollEl ? document.getElementById(scrollEl.id.replace('timeline-scroll-', 'timeline-count-')) : null;
                    row.remove();
                    if (countEl) {
                        var n = Math.max(0, parseInt(countEl.textContent || '0', 10) - 1);
                        countEl.textContent = n;
                        if (n === 0 && scrollEl && !scrollEl.querySelector('.timeline-empty')) {
                            scrollEl.innerHTML = '<div class="timeline-empty">No activity yet.</div>';
                        }
                    }
                    if (window.showToast) showToast('Follow-up activity deleted successfully.', 'success');
                } else {
                    if (window.showToast) showToast((resp && resp.message) ? resp.message : 'Failed to delete activity.', 'error');
                }
            }).catch(function () {
                if (window.showToast) showToast('Network error — please retry.', 'error');
            });
        });
    }

    // Keep menus honest as time passes while a modal stays open.
    setInterval(_tlPruneExpiredMenus, 30000);

    $(".js-select2-source").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-select2-users").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-sortby").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-sortmode").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-select2-business-category").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-select2-with-sales").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-leadname-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-leadbusiness-multiple").select2({ dropdownCssClass: "small", containerCssClass: "small", width: '100%' });
    $(".js-alertind").select2({ dropdownCssClass: "small", containerCssClass: "small" });
    $("#filter_ifearea").select2({ dropdownCssClass: "small", containerCssClass: "small" });

    $("#querystring").click(function(){
        window.location.href = window.location.href.split('?')[0];
    });

    $("#start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#complete_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#complete_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#inprogress_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#inprogress_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#reject_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#reject_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#kiv_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#kiv_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#onhold_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#onhold_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#appointment_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#appointment_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#done_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#done_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#verify_date_start").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });
    $("#verify_date_end").datepicker({
        format      : "yyyy-mm-dd",
        autoclose   : true,
        startDate   : '',
    });

    $("#start").change(function(){
        $('#status').val(1);

        // $("#start").val('');
        // $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#end").change(function(){
        $('#status').val(1);

        // $("#start").val('');
        // $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#done_date_start").change(function(){
        $('#status').val(3);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        // $("#done_date_start").val('');
        // $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#done_date_end").change(function(){
        $('#status').val(3);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        // $("#done_date_start").val('');
        // $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#reject_date_start").change(function(){
        $('#status').val(7);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        // $("#reject_date_start").val('');
        // $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#reject_date_end").change(function(){
        $('#status').val(7);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        // $("#reject_date_start").val('');
        // $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#inprogress_date_start").change(function(){
        $('#status').val(2);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        // $("#inprogress_date_start").val('');
        // $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#inprogress_date_end").change(function(){
        $('#status').val(2);
        
        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        // $("#inprogress_date_start").val('');
        // $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#complete_date_start").change(function(){
        $('#status').val(5);

        $("#start").val('');
        $("#end").val('');

        // $("#complete_date_start").val('');
        // $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#complete_date_end").change(function(){
        $('#status').val(5);

        $("#start").val('');
        $("#end").val('');

        // $("#complete_date_start").val('');
        // $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#kiv_date_start").change(function(){
        $('#status').val(6);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        // $("#kiv_date_start").val('');
        // $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#kiv_date_end").change(function(){
        $('#status').val(6);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        // $("#kiv_date_start").val('');
        // $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        $("#verify_date_start").val('');
        $("#verify_date_end").val('');
    });
    $("#verify_date_start").change(function(){
        $('#status').val(4);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        // $("#verify_date_start").val('');
        // $("#verify_date_end").val('');
    });
    $("#verify_date_end").change(function(){
        $('#status').val(4);

        $("#start").val('');
        $("#end").val('');

        $("#complete_date_start").val('');
        $("#complete_date_end").val('');

        $("#inprogress_date_start").val('');
        $("#inprogress_date_end").val('');

        $("#reject_date_start").val('');
        $("#reject_date_end").val('');

        $("#kiv_date_start").val('');
        $("#kiv_date_end").val('');

        $("#done_date_start").val('');
        $("#done_date_end").val('');

        // $("#verify_date_start").val('');
        // $("#verify_date_end").val('');
    });
 
    $('#sortby').change(function() {
        if ($( "#sortby option:selected" ).text() == 'Sort By Due Date') {
            $("select#sortmode").prop('selectedIndex', 1);
        }
    });

    $( document ).ready(function() {
        // setInterval(function() {
        //     location.reload();
        // }, 60 * 1000);
    });

    function deleteTask(id) {
        new swal({
            title: 'Please confirm to proceed on the deletion!',
            customClass: {
                container: 'text-class align-middle',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (result.isConfirmed) {
                submitActionForm("{{ route('tasks.delete') }}", id);
            }
        })
    }

    // Submit a state-changing task action as a normal form POST so the
    // server's flashed SweetAlert (alert()->success()) is shown after the
    // redirect — same pattern as create/update/accept/done/fallback.
    function submitActionForm(action, id) {
        var form = document.createElement('form');
        form.method = 'post';
        form.action = action;
        form.innerHTML =
            '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
            '<input type="hidden" name="id" value="' + id + '">';
        document.body.appendChild(form);
        form.submit();
    }

    function reactivateTask(id) {
        new swal({
            title: 'Please confirm to reactivate this task!',
            customClass: {
                container: 'text-class align-middle',
                header: 'text-class',
                title: 'title-class',
                htmlContainer: 'text-class',
                input: 'text-class width-class',
                inputLabel: 'text-class',
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (result.isConfirmed) {
                submitActionForm("{{ route('tasks.reactivate') }}", id);
            }
        })
    }

    function filter_data(status) {
        $('#status').val(status);
        document.forms['filter'].submit();
    }

    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    const tasks = ({!! json_encode($tasks->toArray()['data']) !!})

    // json_encode($u->user->active_appointment 

    function appointmentList(subscriber, t, u, r) {
        let text = '<div class="center">';
        
        text += "<span><b><u>Appointment List of " + subscriber + "</u></b></span><br><br>";

        let entries = Object.values(tasks);
        let selectedTask = entries.filter(task => task['id'] == t)[0];
        let selectedUser = selectedTask.users.filter(user => user.user_id == u && user.role == r)[0];
        let appointments = selectedUser.user.active_appointment;

        // console.log(selectedTask.users);
        // console.log(selectedUser.user.active_appointment);

        for (let i = 0; i < appointments.length; i++) {
            text += (i+1) + '. <span style="color:red;">' + appointments[i][0] + "</span> (" + appointments[i][1] + ")<br>";
        }
        
        text += "</div>";

        document.getElementById("content").innerHTML = text;

        var modal = document.getElementById("myModal");
        modal.style.display  = "block";
        
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("tutup")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() { 
            modal.style.display = "none";
        }
    }

    function refreshPage() {
        document.getElementById("btnSearch").click();
    }

    
    // Get the modal
    var modal = document.getElementById("myifearea");

    // Get the button that opens the modal
    var btn = document.getElementById("btnIfeArea");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@endsection