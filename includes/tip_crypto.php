<?php

declare(strict_types=1);

/**
 * Public key for the confidential-tip form (js/tip-form.js encrypts client-side
 * against this). Hex-encoded libsodium crypto_box public key, 32 bytes / 64 hex chars.
 *
 * The matching private key is NOT stored anywhere in this codebase or on the
 * server — it must be kept offline and used only with bin/decrypt-tip.php,
 * run on a machine that never uploads it anywhere. If TIP_PUBLIC_KEY is unset,
 * the confidential-tip form is hidden rather than falling back to anything else.
 */
function tip_public_key_hex(): ?string
{
    $key = env_raw('TIP_PUBLIC_KEY');
    if ($key === null || $key === '') {
        return null;
    }
    if (!preg_match('/^[0-9a-f]{64}$/i', $key)) {
        return null;
    }
    return strtolower($key);
}

function tip_form_enabled(): bool
{
    return tip_public_key_hex() !== null;
}
