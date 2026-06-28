<?php
/**
 * Share strip component.
 *
 * Set before including:
 *   $shareUrl   — full absolute URL to share
 *   $shareTitle — plain text title
 *   $shareCompact — bool, true for inline compact version (e.g. per-scheme)
 */
declare(strict_types=1);

$shareUrl     = $shareUrl     ?? page_url(ltrim((string) ($_SERVER['REQUEST_URI'] ?? '/'), '/'));
$shareTitle   = $shareTitle   ?? SITE_BRAND;
$shareCompact = $shareCompact ?? false;

$waUrl    = 'https://wa.me/?text=' . rawurlencode($shareTitle . ' ' . $shareUrl);
$emailUrl = 'mailto:?subject=' . rawurlencode($shareTitle) . '&body=' . rawurlencode($shareUrl);
$uid      = 'share-' . substr(md5($shareUrl), 0, 6);
?>
<div class="share-strip<?= $shareCompact ? ' share-strip--compact' : '' ?>" data-share-url="<?= e($shareUrl) ?>" data-share-title="<?= e($shareTitle) ?>">
    <?php if (!$shareCompact): ?>
        <span class="share-label">Share</span>
    <?php endif; ?>
    <button class="share-btn share-btn--native" id="<?= e($uid) ?>" hidden aria-label="Share via device">
        <?php if (!$shareCompact): ?>Share<?php else: ?>Share<?php endif; ?>
    </button>
    <a class="share-btn share-btn--wa" href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
    <a class="share-btn share-btn--email" href="<?= e($emailUrl) ?>">Email</a>
    <button class="share-btn share-btn--copy" data-copy-url="<?= e($shareUrl) ?>">Copy link</button>
</div>
<script>
(function () {
    var strip = document.getElementById('<?= e($uid) ?>');
    if (!strip) return;
    var url   = <?= json_encode($shareUrl) ?>;
    var title = <?= json_encode($shareTitle) ?>;
    if (navigator.share) {
        strip.removeAttribute('hidden');
        strip.addEventListener('click', function () {
            navigator.share({ title: title, url: url }).catch(function () {});
        });
    }
    var parent = strip.closest('.share-strip');
    var copy   = parent ? parent.querySelector('.share-btn--copy') : null;
    if (copy) {
        copy.addEventListener('click', function () {
            var btn = this;
            navigator.clipboard.writeText(url).then(function () {
                btn.textContent = 'Copied!';
                btn.classList.add('share-btn--copied');
                setTimeout(function () {
                    btn.textContent = 'Copy link';
                    btn.classList.remove('share-btn--copied');
                }, 2200);
            });
        });
    }
})();
</script>
