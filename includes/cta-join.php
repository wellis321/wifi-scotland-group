<?php
/**
 * Reusable join CTA band.
 * Optional: set $ctaHeading and $ctaBody before including.
 */
declare(strict_types=1);

$ctaHeading = $ctaHeading ?? 'Ready to get involved?';
$ctaBody    = $ctaBody    ?? 'Join the mailing list and we\'ll keep you updated on events, consultations, and ways to take action. No spam — this is a volunteer campaign.';
?>
<section class="join-band" aria-labelledby="cta-join-heading">
    <div class="wrap join-band-inner">
        <div>
            <h2 id="cta-join-heading"><?= e($ctaHeading) ?></h2>
            <p><?= e($ctaBody) ?></p>
        </div>
        <a class="btn btn-lg" href="/join.php">Join the campaign &rarr;</a>
    </div>
</section>
