{{--
    Row hover for the form screens' tables.

    The theme sets --bs-table-hover-bg globally, and Bootstrap's own
    .table-success / .table-active variants redefine it per row, so a row can
    pick up a tinted (green) highlight that has nothing to do with this app's
    palette. Pin it to a neutral grey for form tables only, and pin the text
    colour too — otherwise the variant's --bs-table-hover-color still applies
    and the row's text shifts on hover.

    Scoped to .form-table so no other module's tables are touched.
--}}
<style>
    .form-table.table-hover > tbody > tr:hover > * {
        --bs-table-hover-bg: #f1f4f9;
        --bs-table-hover-color: inherit;
        --bs-table-bg-state: #f1f4f9;
        --bs-table-color-state: inherit;
    }
</style>
