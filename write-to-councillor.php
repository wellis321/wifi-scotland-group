<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Write to your councillor';
$pageDescription = 'A ready-to-use template letter about broadband access and digital inclusion — personalise it, copy it, and send it to your local councillor via WriteToThem.com.';
$currentNav      = 'involved';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'Person writing at a table — representing constituent contact with elected representatives.';

$sidebarRelated = [
    ['href' => '/get-involved.php',  'label' => 'Get involved'],
    ['href' => '/scotland.php',      'label' => 'Scotland policy'],
    ['href' => '/why-it-matters.php','label' => 'Why it matters'],
    ['href' => '/resources.php',     'label' => 'Resources & references'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <p class="meta"><a href="/get-involved.php">&larr; Get involved</a></p>
        <h1>Write to your councillor</h1>
        <p>Personalise the template below, copy it, then send it through WriteToThem.com — a free service that finds your local councillors and delivers your message. The whole thing takes about five minutes.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

        <div class="prose">

            <div class="info-card" style="margin-bottom:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Why writing works</h2>
                    <p class="info-card__sub">Elected officials respond to constituent contact</p>
                </div>
                <div class="info-card__body">
                    <p>Councillors are accountable to the people who live in their ward. A specific, well-evidenced question — especially one that includes links to official policy — is hard to ignore, and the response becomes public record that you can follow up on.</p>
                    <p>You don't need to be an expert. Asking a simple question, as a constituent, is enough to put broadband access on the record at your council.</p>
                </div>
            </div>

            <h2>1. Personalise your letter</h2>
            <p>Fill in the fields below. The template updates as you type — nothing is sent until you copy and paste it into WriteToThem.com yourself.</p>

            <div class="letter-form">
                <div class="letter-form__row">
                    <label class="letter-form__label" for="your-name">Your name</label>
                    <input class="letter-form__input" id="your-name" type="text" placeholder="e.g. Morag MacDonald" autocomplete="name">
                </div>
                <div class="letter-form__row">
                    <label class="letter-form__label" for="council-area">Your council area</label>
                    <input class="letter-form__input" id="council-area" type="text" placeholder="e.g. Highland, Dundee City, Glasgow City">
                </div>
                <div class="letter-form__row">
                    <label class="letter-form__label" for="postcode">Your postcode</label>
                    <input class="letter-form__input" id="postcode" type="text" placeholder="e.g. IV3 5QA" autocomplete="postal-code">
                    <p class="letter-form__hint">Used in the letter sign-off only — not shared with us.</p>
                </div>
                <div class="letter-form__row">
                    <label class="letter-form__label" for="specific-issue">Specific local issue <span style="font-weight:400;text-transform:none">(optional)</span></label>
                    <textarea class="letter-form__input" id="specific-issue" rows="3"
                        placeholder="e.g. Our street only has one provider and speeds drop below 5 Mbps. The school gate has no mobile signal."></textarea>
                    <p class="letter-form__hint">Leave blank to use the general opening. Adding a local example makes the letter more powerful.</p>
                </div>
            </div>

            <h2>2. Copy your letter</h2>
            <p>The letter below updates as you fill in the form above.</p>

            <div class="letter-output-wrap">
                <textarea class="letter-output" id="letter-output" readonly aria-label="Your personalised letter — copy this text"></textarea>
                <div class="letter-output-actions">
                    <button class="btn btn-primary" id="copy-btn" type="button">Copy to clipboard</button>
                    <span class="letter-output-hint">Then paste into WriteToThem.com</span>
                </div>
            </div>

            <h2>3. Find your councillor and send</h2>

            <div class="councillor-routes">
                <a class="councillor-route" href="https://www.writetothem.com/"<?= external_link_attrs('https://www.writetothem.com/') ?>>
                    <strong>WriteToThem.com</strong>
                    <span>Enter your postcode to find all your local councillors and send your letter — free, delivered directly.</span>
                </a>
                <a class="councillor-route" href="https://www.gov.scot/publications/councillors/"<?= external_link_attrs('https://www.gov.scot/publications/councillors/') ?>>
                    <strong>Scottish councillor directory</strong>
                    <span>Official Scottish Government list of all elected councillors across the 32 local authorities.</span>
                </a>
                <a class="councillor-route" href="https://www.theyworkforyou.com/"<?= external_link_attrs('https://www.theyworkforyou.com/') ?>>
                    <strong>TheyWorkForYou.com</strong>
                    <span>Find your MSPs and MPs by postcode — useful if you want to raise this at Holyrood or Westminster too.</span>
                </a>
            </div>

            <div class="pull-quote" style="margin-top:2rem">
                <p>"The response becomes public record. Ask the question, then follow it up."</p>
            </div>

            <h2>What to do with the response</h2>
            <ul>
                <li>Share it with your community group or WIRES local group.</li>
                <li>If the answer is evasive, submit a follow-up question at the next full council meeting.</li>
                <li>If you find something significant — a scheme not being advertised, a gap in provision — <a href="/contact.php">tell us</a> and we may be able to amplify it.</li>
            </ul>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<script>
(function () {
    var defaultIssue = 'I am concerned that many residents still lack affordable, reliable internet access — making it harder to access services, work, learn, and stay in touch with family.';

    function val(id) {
        var el = document.getElementById(id);
        return el ? el.value.trim() : '';
    }

    function updateTemplate() {
        var name     = val('your-name')      || '[Your name]';
        var area     = val('council-area')   || '[Your council area]';
        var postcode = val('postcode')       || '[Your postcode]';
        var issue    = val('specific-issue') || defaultIssue;

        var letter = [
            'Subject: Connectivity and broadband access in ' + area + ' — constituent question',
            '',
            'Dear Councillor,',
            '',
            'I am a constituent in ' + area + ' writing about broadband access and digital inclusion in our community.',
            '',
            issue,
            '',
            'I would like to ask:',
            '',
            '1. Which parts of ' + area + ' still lack superfast broadband, and what is the current timeline for the R100 programme to reach them?',
            '2. How is the council helping residents find and access social tariffs and other schemes that can reduce the cost of broadband?',
            '3. What digital inclusion support — through libraries, community centres, or partner organisations — is available locally, and is it adequately resourced?',
            '',
            'I believe affordable internet access is as essential as water and heating. I would welcome a response on what ' + area + ' Council is doing to close these gaps.',
            '',
            'Yours sincerely,',
            '',
            name,
            postcode,
        ].join('\n');

        var output = document.getElementById('letter-output');
        if (output) output.value = letter;
    }

    ['your-name', 'council-area', 'postcode', 'specific-issue'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', updateTemplate);
    });

    var copyBtn = document.getElementById('copy-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var output = document.getElementById('letter-output');
            if (!output) return;
            navigator.clipboard.writeText(output.value).then(function () {
                copyBtn.textContent = 'Copied!';
                setTimeout(function () { copyBtn.textContent = 'Copy to clipboard'; }, 2200);
            }).catch(function () {
                output.select();
                document.execCommand('copy');
                copyBtn.textContent = 'Copied!';
                setTimeout(function () { copyBtn.textContent = 'Copy to clipboard'; }, 2200);
            });
        });
    }

    updateTemplate();
})();
</script>

<?php
$ctaHeading = 'Taken action? Tell us what happened.';
$ctaBody    = 'Responses from councillors — even evasive ones — are useful intelligence for the campaign. Join WIRES and share what you find.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
