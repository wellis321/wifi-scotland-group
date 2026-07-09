#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Offline decryption for the confidential-tip form.
 *
 * RUN THIS ONLY ON A MACHINE YOU TRUST — NEVER ON THE WEB SERVER.
 * It needs the private key that must never touch the codebase, git, or Hostinger.
 * The whole point of the tip form is that the server never has this key.
 *
 * Usage:
 *   php bin/decrypt-tip.php
 *     Prompts for the private key (hex) and ciphertext (base64) interactively.
 *
 *   TIP_PRIVATE_KEY=<hex> php bin/decrypt-tip.php <ciphertext-file>
 *     Reads the private key from an env var (so it doesn't linger in shell
 *     history) and the ciphertext from a file (e.g. copy-pasted from
 *     /admin/tips.php into a text file first).
 *
 * Generate a keypair (do this once, offline):
 *   php -r '$k=sodium_crypto_box_keypair();echo "PUBLIC: ".sodium_bin2hex(sodium_crypto_box_publickey($k))."\nPRIVATE: ".sodium_bin2hex(sodium_crypto_box_secretkey($k))."\n";'
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("This script is for command-line use only.\n");
}

function prompt(string $label, bool $hide = false): string
{
    fwrite(STDERR, $label);
    if ($hide && stripos(PHP_OS, 'WIN') !== 0) {
        system('stty -echo');
        $value = trim((string) fgets(STDIN));
        system('stty echo');
        fwrite(STDERR, "\n");
        return $value;
    }
    return trim((string) fgets(STDIN));
}

$privateKeyHex = getenv('TIP_PRIVATE_KEY') ?: prompt('Private key (hex, kept offline): ', true);
$privateKeyHex = strtolower(trim($privateKeyHex));

if (!preg_match('/^[0-9a-f]{64}$/', $privateKeyHex)) {
    fwrite(STDERR, "That doesn't look like a 32-byte hex private key.\n");
    exit(1);
}

$ciphertextFile = $argv[1] ?? null;
if ($ciphertextFile !== null) {
    if (!is_readable($ciphertextFile)) {
        fwrite(STDERR, "Can't read file: $ciphertextFile\n");
        exit(1);
    }
    $ciphertextB64 = trim((string) file_get_contents($ciphertextFile));
} else {
    $ciphertextB64 = prompt('Ciphertext (base64, paste from /admin/tips.php): ');
}

$privateKey = sodium_hex2bin($privateKeyHex);
$publicKey  = sodium_crypto_box_publickey_from_secretkey($privateKey);
$keypair    = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey, $publicKey);

$ciphertext = base64_decode($ciphertextB64, true);
if ($ciphertext === false) {
    fwrite(STDERR, "That doesn't look like valid base64.\n");
    exit(1);
}

$plaintext = sodium_crypto_box_seal_open($ciphertext, $keypair);
sodium_memzero($privateKey);

if ($plaintext === false) {
    fwrite(STDERR, "Could not decrypt — wrong key, or the ciphertext is corrupted/truncated.\n");
    exit(1);
}

fwrite(STDOUT, "\n--- decrypted tip ---\n\n" . $plaintext . "\n\n---------------------\n");
