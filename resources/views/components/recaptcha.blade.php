@php
    $recaptchaEnabled = \App\Services\RecaptchaService::isEnabled();
    $recaptchaSiteKey = \App\Services\RecaptchaService::siteKey();
@endphp
@if($recaptchaEnabled && $recaptchaSiteKey)
<div class="mb-3">
    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
    @error('g-recaptcha-response')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

@once
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
(function () {
    // Blocks submission of any form containing a .g-recaptcha widget until it
    // is checked, and re-hooks itself on every page load — so a fresh
    // (unsolved) widget is always required after a server-side validation
    // failure sends the user back to the form.
    function bindRecaptchaForms() {
        document.querySelectorAll('.g-recaptcha').forEach(function (widget) {
            var form = widget.closest('form');
            if (!form || form.dataset.recaptchaBound === '1') return;
            form.dataset.recaptchaBound = '1';

            form.addEventListener('submit', function (e) {
                var solved = window.grecaptcha && typeof grecaptcha.getResponse === 'function'
                    && grecaptcha.getResponse().length > 0;

                if (!solved) {
                    e.preventDefault();
                    e.stopPropagation();

                    var msg = widget.parentElement.querySelector('.recaptcha-client-error');
                    if (!msg) {
                        msg = document.createElement('div');
                        msg.className = 'text-danger small mt-1 recaptcha-client-error';
                        msg.textContent = 'Please verify that you are not a robot.';
                        widget.insertAdjacentElement('afterend', msg);
                    }
                    widget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        });
    }

    if (document.readyState !== 'loading') {
        bindRecaptchaForms();
    } else {
        document.addEventListener('DOMContentLoaded', bindRecaptchaForms);
    }
})();
</script>
@endonce
@endif
