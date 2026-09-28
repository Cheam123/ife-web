{{--
    Filter-bar chrome for the Forms list screens.

    Same look as the Task screen (page/tasks/form/form-index): a row of status
    pills over a compact table, with the unselected pills in the pale blue that
    screen gives .nav-link.inactive, and tighter cells so a long list stays
    readable without scrolling.

    @yield('css') is emitted before the theme's stylesheets, so the cell rule
    has to out-specify Bootstrap's own `.table > :not(caption) > * > *` rather
    than rely on coming later.

    Scoped to .form-filter / .form-table-compact so no other module's pills or
    tables are touched.
--}}
<style>
    .form-filter .nav-pills .nav-link.inactive,
    .form-filter .nav-pills .nav-link.active,
    .form-filter .nav-pills .show > .nav-link {
        padding: 10px;
    }
    .form-filter .nav-pills .nav-link.inactive {
        color: #000000;
        background-color: #bedbe8;
    }
    .form-filter .nav-pills .nav-link.inactive:hover {
        background-color: #a9d0e1;
    }
    .form-table-compact.table > :not(caption) > * > * {
        padding: 0.45rem 0.5rem;
    }
</style>
