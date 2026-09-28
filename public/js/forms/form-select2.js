/**
 * Shared select2 wiring for the Forms feature.
 *
 * select2 4.0.13 + the Bootstrap-5 theme are loaded app-wide (layouts/head
 * and layouts/vendor-scripts), so every Forms dropdown uses the same
 * component and options as the Task pages instead of a hand-rolled picker.
 *
 * Mark a <select> with `js-select2` and it is initialised on page load;
 * call FormSelect2.apply($scope) after rendering markup dynamically (the
 * settings panel, the process drawer, condition rows).
 *
 * Options via data attributes:
 *   data-placeholder="..."   placeholder text
 *   data-no-search="1"       hide the search box (short, fixed lists)
 *   data-tags="1"            allow free-text values (condition values)
 */
(function () {
    'use strict';

    // Several partials include this file; only wire it up once per page.
    if (window.FormSelect2) return;

    function ready() {
        return !!(window.jQuery && window.jQuery.fn && window.jQuery.fn.select2);
    }

    window.FormSelect2 = {
        /**
         * Initialise every js-select2 inside $scope (default: whole document).
         * Already-initialised controls are skipped, so it is safe to re-run.
         */
        apply: function (scope) {
            if (!ready()) return;
            var $ = window.jQuery;
            var $scope = scope ? $(scope) : $(document);

            $scope.find('select.js-select2').addBack('select.js-select2').each(function () {
                var $select = $(this);
                if ($select.data('select2')) return; // already wired

                // Keep the dropdown inside its modal/offcanvas, otherwise it
                // renders behind the backdrop and the search box can't focus.
                var $container = $select.closest('.modal, .offcanvas');

                $select.select2({
                    dropdownCssClass:  'small',
                    containerCssClass: 'small',
                    placeholder:       $select.data('placeholder') || 'Select...',
                    allowClear:        !$select.prop('required') && !$select.prop('multiple'),
                    width:             '100%',
                    tags:              $select.data('tags') === 1 || $select.data('tags') === '1',
                    minimumResultsForSearch: ($select.data('no-search') ? Infinity : 0),
                    dropdownParent:    $container.length ? $container : $(document.body)
                });
            });
        },

        /** Tear down before replacing markup, so no orphan containers linger. */
        destroy: function (scope) {
            if (!ready()) return;
            var $ = window.jQuery;
            $(scope).find('select.js-select2').each(function () {
                if (window.jQuery(this).data('select2')) window.jQuery(this).select2('destroy');
            });
        }
    };

    // A select2 initialised inside a hidden container (a wizard step, a
    // closed modal, a conditionally hidden field) measures 0px wide and stays
    // collapsed when revealed — force the container to fill its slot instead.
    var style = document.createElement('style');
    style.textContent =
        '.select2-container { width: 100% !important; }' +
        '.select2-container--bootstrap-5 .select2-selection { min-height: calc(1.5em + 0.75rem + 2px); }';
    document.head.appendChild(style);

    document.addEventListener('DOMContentLoaded', function () {
        window.FormSelect2.apply(document);
    });
})();
