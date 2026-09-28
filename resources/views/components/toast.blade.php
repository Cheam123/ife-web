{{--
    Reusable toast notifications (bottom-right).

    Include once per page (or globally in the master layout):
        <x-toast />

    Then trigger from anywhere in JS:
        showToast('Activity saved successfully');                 // success (default)
        showToast('Something went wrong', 'error');               // error
        showToast('Heads up — check your input', 'warning');      // warning
        showToast('FYI', 'info', { title: 'Notice', duration: 6000 });
--}}
<div id="app-toast-container" class="app-toast-container" aria-live="polite" aria-atomic="true"></div>

<style>
    .app-toast-container {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
        max-width: 380px;
        width: calc(100% - 48px);
    }
    .app-toast {
        pointer-events: auto;
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(17, 24, 39, 0.12), 0 2px 6px rgba(17, 24, 39, 0.08);
        border: 1px solid rgba(17, 24, 39, 0.06);
        overflow: hidden;
        font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        transform: translateX(120%);
        opacity: 0;
        transition: transform 0.35s cubic-bezier(0.21, 1.02, 0.73, 1), opacity 0.35s ease;
    }
    .app-toast.show { transform: translateX(0); opacity: 1; }
    .app-toast.hide { transform: translateX(120%); opacity: 0; }

    .app-toast__icon {
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #fff;
        line-height: 1;
    }
    .app-toast__body { min-width: 0; flex: 1 1 auto; padding-top: 2px; }
    .app-toast__title {
        font-size: 13.5px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 2px;
        line-height: 1.3;
    }
    .app-toast__msg {
        font-size: 13px;
        color: #4b5563;
        line-height: 1.45;
        word-break: break-word;
    }
    .app-toast__close {
        flex-shrink: 0;
        background: none;
        border: none;
        cursor: pointer;
        color: #9ca3af;
        font-size: 18px;
        line-height: 1;
        padding: 2px 4px;
        margin: -2px -4px 0 0;
        border-radius: 6px;
        transition: color 0.15s ease, background 0.15s ease;
    }
    .app-toast__close:hover { color: #374151; background: rgba(17, 24, 39, 0.05); }

    .app-toast__bar {
        position: absolute;
        left: 0;
        bottom: 0;
        height: 3px;
        width: 100%;
        transform-origin: left center;
        opacity: 0.85;
    }

    /* Variants */
    .app-toast--success .app-toast__icon { background: #16a34a; }
    .app-toast--success .app-toast__bar  { background: #16a34a; }
    .app-toast--error   .app-toast__icon { background: #dc2626; }
    .app-toast--error   .app-toast__bar  { background: #dc2626; }
    .app-toast--warning .app-toast__icon { background: #d97706; }
    .app-toast--warning .app-toast__bar  { background: #d97706; }
    .app-toast--info    .app-toast__icon { background: #2563eb; }
    .app-toast--info    .app-toast__bar  { background: #2563eb; }

    @keyframes appToastBar {
        from { transform: scaleX(1); }
        to   { transform: scaleX(0); }
    }

    @media (max-width: 500px) {
        .app-toast-container { bottom: 16px; right: 16px; width: calc(100% - 32px); }
    }
</style>

<script>
(function () {
    if (window.showToast) return; // guard against double-include

    var ICONS = {
        success: '<i class="mdi mdi-check-bold"></i>',
        error:   '<i class="mdi mdi-close-thick"></i>',
        warning: '<i class="mdi mdi-alert-outline"></i>',
        info:    '<i class="mdi mdi-information-outline"></i>'
    };
    var DEFAULT_TITLES = {
        success: 'Success',
        error:   'Error',
        warning: 'Warning',
        info:    'Notice'
    };

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
        });
    }

    /**
     * Show a toast notification in the bottom-right corner.
     * @param {string} message  The message body.
     * @param {string} [type]   success | error | warning | info  (default: success)
     * @param {object} [options]
     *        - title    {string}  Heading text (defaults per type; pass '' to hide).
     *        - duration {number}  Auto-dismiss ms (default 3500; pass 0 to keep until closed).
     */
    window.showToast = function (message, type, options) {
        type = ICONS[type] ? type : 'success';
        options = options || {};
        var duration = (typeof options.duration === 'number') ? options.duration : 3500;
        var title = (options.title !== undefined) ? options.title : DEFAULT_TITLES[type];

        var container = document.getElementById('app-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'app-toast-container';
            container.className = 'app-toast-container';
            document.body.appendChild(container);
        }

        var toast = document.createElement('div');
        toast.className = 'app-toast app-toast--' + type;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

        var barStyle = duration > 0
            ? ' style="animation: appToastBar ' + duration + 'ms linear forwards;"'
            : ' style="display:none;"';

        toast.innerHTML =
            '<span class="app-toast__icon">' + ICONS[type] + '</span>' +
            '<div class="app-toast__body">' +
                (title ? '<div class="app-toast__title">' + escapeHtml(title) + '</div>' : '') +
                '<div class="app-toast__msg">' + escapeHtml(message || '') + '</div>' +
            '</div>' +
            '<button type="button" class="app-toast__close" aria-label="Dismiss">&times;</button>' +
            '<div class="app-toast__bar"' + barStyle + '></div>';

        container.appendChild(toast);

        // Enter animation on next frame.
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { toast.classList.add('show'); });
        });

        var timer = null;
        function dismiss() {
            if (toast._dismissed) return;
            toast._dismissed = true;
            if (timer) clearTimeout(timer);
            toast.classList.remove('show');
            toast.classList.add('hide');
            toast.addEventListener('transitionend', function () {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, { once: true });
            // Fallback removal in case transitionend doesn't fire.
            setTimeout(function () { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 600);
        }

        toast.querySelector('.app-toast__close').addEventListener('click', dismiss);

        if (duration > 0) {
            var bar = toast.querySelector('.app-toast__bar');
            timer = setTimeout(dismiss, duration);
            // Pause on hover for readability.
            toast.addEventListener('mouseenter', function () {
                if (timer) clearTimeout(timer);
                if (bar) bar.style.animationPlayState = 'paused';
            });
            toast.addEventListener('mouseleave', function () {
                if (bar) bar.style.animationPlayState = 'running';
                timer = setTimeout(dismiss, 1200);
            });
        }

        return { dismiss: dismiss };
    };
})();
</script>
