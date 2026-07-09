(function () {
    'use strict';

    var section = document.querySelector('[data-tip-form]');
    if (!section) return;

    var toggle    = section.querySelector('[data-tip-toggle]');
    var panel     = section.querySelector('[data-tip-panel]');
    var form      = section.querySelector('[data-tip-form-el]');
    var textarea  = section.querySelector('[data-tip-message]');
    var hidden    = section.querySelector('[data-tip-ciphertext]');
    var status    = section.querySelector('[data-tip-status]');
    var submitBtn = section.querySelector('[data-tip-submit]');
    var pubKeyHex = section.getAttribute('data-tip-pubkey') || '';

    var sodiumReady = null; /* null = not started, Promise once loading begins */

    function setStatus(text, isError) {
        if (!status) return;
        status.textContent = text;
        status.classList.toggle('tip-status--err', !!isError);
    }

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = function () { reject(new Error('Failed to load ' + src)); };
            document.head.appendChild(s);
        });
    }

    function ensureSodium() {
        if (sodiumReady) return sodiumReady;
        setStatus('Loading encryption…');
        sodiumReady = loadScript('/js/vendor/libsodium.js')
            .then(function () { return loadScript('/js/vendor/libsodium-wrappers.js'); })
            .then(function () { return window.sodium.ready; })
            .then(function () {
                setStatus('Encryption ready. Your message is encrypted in your browser before it is sent.');
                if (submitBtn) submitBtn.disabled = false;
            })
            .catch(function (err) {
                setStatus('Encryption could not be loaded, so this form is disabled. Please reload the page and try again, or use the regular contact form above for anything not sensitive.', true);
                if (submitBtn) submitBtn.disabled = true;
                throw err;
            });
        return sodiumReady;
    }

    if (toggle && panel) {
        toggle.addEventListener('click', function () {
            var isOpen = panel.hidden === false;
            panel.hidden = isOpen;
            toggle.setAttribute('aria-expanded', String(!isOpen));
            if (!isOpen) {
                ensureSodium().catch(function () {});
            }
        });
    }

    if (!form) return;

    if (submitBtn) submitBtn.disabled = true;

    /* Panel can render already-open server-side (e.g. redisplaying after a
       validation error) — load encryption immediately in that case too. */
    if (panel && panel.hidden === false) {
        ensureSodium().catch(function () {});
    }

    form.addEventListener('submit', function (evt) {
        evt.preventDefault();

        if (!pubKeyHex) {
            setStatus('This form is not configured yet — nothing was sent.', true);
            return;
        }

        var message = (textarea && textarea.value ? textarea.value : '').trim();
        if (!message) {
            setStatus('Write a message first.', true);
            return;
        }

        ensureSodium().then(function () {
            var sodium = window.sodium;
            var pubKeyBytes = sodium.from_hex(pubKeyHex);
            var plainBytes = sodium.from_string(message);
            var sealed = sodium.crypto_box_seal(plainBytes, pubKeyBytes);
            var b64 = sodium.to_base64(sealed, sodium.base64_variants.ORIGINAL);

            hidden.value = b64;
            /* The visible textarea has no name attribute (see contact.php) so it
               can never be submitted in plaintext even if this script has a bug.
               Clearing it here too, belt and braces. */
            textarea.value = '';

            setStatus('Encrypting and sending…');
            form.submit();
        }).catch(function () {
            /* setStatus already updated by ensureSodium's catch handler. */
        });
    });
})();
