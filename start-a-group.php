<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Start a local group';
$pageDescription = 'A step-by-step guide to starting a WIFI Scotland Group in your council area. You do not need to be an expert—you need a concern and a couple of neighbours.';
$currentNav = 'groups';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Start a local group</h1>
        <p>You do not need to be a technology expert. You need a concern, two or three people who share it, and a first conversation.</p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">

        <figure class="page-lede">
            <img src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800" alt="People in a community meeting with a laptop open on the table." decoding="async" loading="lazy">
            <figcaption>Local groups start in community centres, libraries, tenants' meetings, and kitchens. Wherever people already gather works fine.</figcaption>
        </figure>

        <p>National campaigns only land if they are rooted in real places. A local group gives your area a voice: it maps the gaps, raises questions at council meetings, and gives your neighbours somewhere to go when they need help understanding their options.</p>

        <ol class="start-group-steps">
            <li class="start-group-step">
                <div>
                    <h3>Find two or three people who care</h3>
                    <p>You do not need a big turnout to start. Talk to people in your block, at the library, in your tenants' association, or at the school gate. The question is simple: does unreliable or unaffordable internet affect people you know? If yes, you have the basis of a group.</p>
                </div>
            </li>
            <li class="start-group-step">
                <div>
                    <h3>Hold a first informal meeting</h3>
                    <p>A community centre, library café, or someone's living room all work. The goal is not to have all the answers—it is to agree what the problem looks like in your area and what you could realistically do about it. One hour is enough to start.</p>
                </div>
            </li>
            <li class="start-group-step">
                <div>
                    <h3>Tell us you exist</h3>
                    <p>Use the <a href="/contact.php">contact form</a> and mention your council area and a name or email we can use. We will add your group to the <a href="/groups.php">groups page</a>, include you in national campaign updates, and connect you with others doing similar work elsewhere in Scotland.</p>
                </div>
            </li>
            <li class="start-group-step">
                <div>
                    <h3>Map your local picture</h3>
                    <p>Which broadband schemes are available in your area? Does your council have a digital strategy? Is the community centre running any digital skills sessions? Are there mobile signal black spots on your high street? Knowing this puts you on the front foot before any meeting with officials.</p>
                </div>
            </li>
            <li class="start-group-step">
                <div>
                    <h3>Show up in democratic spaces</h3>
                    <p>Ask your community council for a standing agenda item on connectivity. Submit questions to your local councillor when budget lines touch digital infrastructure. Attend full council or scrutiny committees when relevant items come up—your presence changes what officials say and remember.</p>
                </div>
            </li>
            <li class="start-group-step">
                <div>
                    <h3>Feed what you find back</h3>
                    <p>Your on-the-ground experience is what national policy usually misses. Send us local stories, broken information links, confusing leaflets, and anything that made your neighbours frustrated or confused. What you see in your street matters to the campaign as a whole.</p>
                </div>
            </li>
        </ol>

        <div class="what-we-provide">
            <h2>What we provide</h2>
            <ul>
                <li>Your group listed on the <a href="/groups.php">local groups page</a></li>
                <li>Templates for council letters and meeting agenda items</li>
                <li>Updates when national policy, programmes, or funding change in ways that affect your area</li>
                <li>Connection to other groups doing similar work across Scotland</li>
                <li>A way to get local news and events listed on your group's page on this site</li>
            </ul>
        </div>

        <div class="callout">
            <p><strong>Ready to start?</strong> Use the contact form and mention your council area and a contact name. We aim to reply within a week.</p>
            <p style="margin-top: 0.75rem"><a class="btn btn-primary" href="/contact.php">Get in touch</a></p>
        </div>

        <p class="meta" style="margin-top: 2rem">Already have a group? <a href="/contact.php">Tell us about it</a> and we will add it to the directory. You do not need to be formally registered or have an official structure—active and forming groups are both listed.</p>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
