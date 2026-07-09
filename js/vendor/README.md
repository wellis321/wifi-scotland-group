# Vendored dependencies

- `libsodium.js` — libsodium compiled to WASM/asm.js. From npm `libsodium@0.8.4`.
- `libsodium-wrappers.js` — JavaScript API wrapper over `libsodium.js`. From npm `libsodium-wrappers@0.8.4`.

Both from https://github.com/jedisct1/libsodium.js — self-hosted here (not loaded from a CDN)
so the confidential-tip form on /contact has no third-party network dependency. Used to encrypt
tip submissions in the browser before they are sent to the server — see js/tip-form.js.

License: ISC, see LICENSE-libsodium.

To update: `npm pack libsodium@latest` / `npm pack libsodium-wrappers@latest`, extract, and
replace dist/modules/*.js here. Keep both packages on the same version.
