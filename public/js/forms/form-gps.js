/**
 * Location Stamp fields (type: 'gps') on the web fill pages.
 *
 * Each field renders a hidden .gps-input that holds the answer as JSON
 * {lat, lng, accuracy, captured_at}; the page's readValue() parses it. The
 * value is only ever written from the browser's geolocation, never typed.
 *
 *   data-capture-mode="auto"    capture as soon as the field is on screen
 *   data-capture-mode="manual"  wait for the "Stamp location" button
 *
 * Auto mode waits until a field is actually visible, so a field hidden by a
 * condition (or owned by a later fill stage) never asks for location. A
 * failed auto capture is not retried on its own: the button turns into
 * "Retry" instead of prompting again on every change.
 *
 * The partials render above layouts/vendor-scripts, so this file loads
 * before jQuery and sticks to the plain DOM.
 */
(function () {
    'use strict';

    // Every gps field includes this file; only wire it up once per page.
    if (window.FormGps) return;

    var GEO_OPTIONS = { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 };

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function fieldOf(input) {
        return input.closest('.gps-field');
    }

    function parse(input) {
        try {
            var value = input.value ? JSON.parse(input.value) : null;
            return value && typeof value.lat === 'number' && typeof value.lng === 'number' ? value : null;
        } catch (e) {
            return null;
        }
    }

    /** Redraw the readout, button label and status line for one field. */
    function render(input, status, isError) {
        var field   = fieldOf(input);
        var value   = parse(input);
        var readout = field.querySelector('.gps-readout');
        var label   = field.querySelector('.gps-btn-label');
        var note    = field.querySelector('.gps-status');

        if (value) {
            var details = [];
            if (typeof value.accuracy === 'number') details.push('±' + Math.round(value.accuracy) + ' m');
            if (value.captured_at) details.push(new Date(value.captured_at).toLocaleString());

            readout.classList.remove('text-muted');
            readout.innerHTML =
                '<i class="mdi mdi-map-marker text-success me-1"></i>' +
                '<span class="fw-semibold">' + value.lat.toFixed(6) + ', ' + value.lng.toFixed(6) + '</span>' +
                (details.length ? ' <span class="text-muted">· ' + escapeHtml(details.join(' · ')) + '</span>' : '') +
                ' <a href="https://www.google.com/maps?q=' + value.lat + ',' + value.lng + '"' +
                ' target="_blank" rel="noopener" class="ms-1">Open in Maps</a>';
        } else {
            readout.classList.add('text-muted');
            readout.innerHTML = '<i class="mdi mdi-map-marker-off-outline me-1"></i>Location not stamped yet.';
        }

        if (label) {
            label.textContent = value ? 'Stamp again' : (input.hasAttribute('data-failed') ? 'Retry' : 'Stamp location');
        }

        note.textContent = status || '';
        note.classList.toggle('text-danger', !!isError);
        note.classList.toggle('text-muted', !isError);
    }

    function failureMessage(error) {
        switch (error && error.code) {
            case 1:  return 'We could not check your location. Please check your browser’s permission for this site and retry.';
            case 2:  return 'We could not find your location right now. Please turn on location services and retry.';
            case 3:  return 'Finding your location took too long. Please retry.';
            default: return 'We could not stamp your location. Please retry.';
        }
    }

    function fail(input, message) {
        // A failed recapture keeps the previous stamp; only the status changes.
        input.setAttribute('data-failed', '1');
        render(input, message, true);
    }

    function capture(input) {
        var button = fieldOf(input).querySelector('.gps-capture-btn');

        if (!window.isSecureContext) {
            fail(input, 'We can only stamp your location on a secure (https) page.');
            return;
        }
        if (!navigator.geolocation) {
            fail(input, 'This browser cannot share your location.');
            return;
        }

        if (button) button.disabled = true;
        render(input, 'Stamping your location…');

        navigator.geolocation.getCurrentPosition(function (position) {
            var coords = position.coords;
            input.value = JSON.stringify({
                lat         : coords.latitude,
                lng         : coords.longitude,
                accuracy    : typeof coords.accuracy === 'number' ? coords.accuracy : null,
                captured_at : new Date(position.timestamp || Date.now()).toISOString()
            });
            input.removeAttribute('data-failed');
            if (button) button.disabled = false;
            render(input, '');

            var error = document.getElementById('error_' + input.getAttribute('data-el-id'));
            if (error) error.textContent = '';

            // Let the page re-run its conditions (is_empty / is_not_empty).
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }, function (error) {
            if (button) button.disabled = false;
            fail(input, failureMessage(error));
        }, GEO_OPTIONS);
    }

    function inputs() {
        return Array.prototype.slice.call(document.querySelectorAll('.gps-input'));
    }

    /** Capture every visible, empty auto-mode field not tried yet. */
    function autoCapture() {
        inputs().forEach(function (input) {
            if (input.disabled || input.value || input.hasAttribute('data-auto-tried')) return;
            if (input.getAttribute('data-capture-mode') !== 'auto') return;
            if (input.closest('.d-none')) return; // hidden by a condition: wait until it shows

            input.setAttribute('data-auto-tried', '1');
            capture(input);
        });
    }

    window.FormGps = { capture: capture, render: render, autoCapture: autoCapture };

    document.addEventListener('click', function (e) {
        var button = e.target.closest && e.target.closest('.gps-capture-btn');
        if (!button) return;
        var input = fieldOf(button).querySelector('.gps-input');
        if (input && !input.disabled) capture(input);
    });

    document.addEventListener('DOMContentLoaded', function () {
        inputs().forEach(function (input) { render(input, ''); });

        // Conditions reveal fields as other answers change. select2 fires its
        // change through jQuery, which native listeners never see, so listen
        // through jQuery (loaded by now) and run after the page's own handler.
        if (window.jQuery) {
            window.jQuery(document).on('change input', function () { setTimeout(autoCapture, 0); });
        }
    });

    // The pages hide conditional and deferred fields in their jQuery ready
    // handlers; by "load" those have run, so the first pass sees real visibility.
    window.addEventListener('load', function () { setTimeout(autoCapture, 0); });
})();
