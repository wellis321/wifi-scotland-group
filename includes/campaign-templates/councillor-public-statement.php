<?php

declare(strict_types=1);

/**
 * Email template for the councillor public-statement campaign, shared between
 * bin/send-councillor-campaign.php (the real sender) and councillor-statements.php
 * (which renders this same template, with placeholder values, so the public can read
 * exactly what was sent — this file is the single source of truth for both).
 */

function render_subject(array $row): string
{
    return "Will you back reliable connectivity for {$row['council']}?";
}

function render_text_body(array $row): string
{
    $council = $row['council'];
    $name    = $row['full_name'];
    $replyBy = reply_by_date();

    return <<<TXT
Dear Councillor {$name},

We treat roads and water as essential infrastructure — something no one is
quietly left to go without. Connectivity deserves the same status, and right
now Scotland doesn't give it that.

Audit Scotland's August 2024 review found no clear delivery plan for tackling
digital exclusion, and no one named as accountable for producing one. Since
then, the Scottish Government has published a Digital Strategy vision
statement and an initial delivery plan (November 2025) — but that plan covers
digital public services, not digital exclusion, and the vision statement's
own Performance Framework is still described as "being refreshed." As SCVO
noted last year, Scotland is still waiting for "strategy, delivery plan and
visible leadership" on digital exclusion.

Meanwhile the numbers are moving the wrong way:
- Citizens Advice Scotland recorded a 59% rise in digitally excluded clients
  in two years (1,932 to 3,065)
- One in six Scottish adults lack basic digital skills
- Only around one in twelve eligible households use the discounted broadband
  social tariff — most have never heard it exists

We're WIRES, campaigning for connectivity to be treated as essential
infrastructure across all 32 council areas, including {$council}. We're
asking councillors to say publicly that everyone in their ward deserves
reliable, affordable connectivity — and that someone needs to be named
responsible for closing this gap.

Could you reply by {$replyBy} with either:
1. A short line we can quote, publicly attributed to you, or
2. Where {$council} already stands, so we record it accurately

See what we've found so far, council-by-council: https://wires.org.uk/accountability

Thank you for your time,
William Ellis
WIRES — wires.org.uk

If you'd rather not hear from us again, just reply and say so.
TXT;
}

function render_html_body(array $row): string
{
    $council = e($row['council']);
    $name    = e($row['full_name']);
    $replyBy = e(reply_by_date());

    return <<<HTML
<div style="font-family:-apple-system,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;max-width:600px">
<p>Dear Councillor {$name},</p>
<p>We treat roads and water as essential infrastructure — something no one is quietly left to go without. Connectivity deserves the same status, and right now Scotland doesn't give it that.</p>
<p>Audit Scotland's August 2024 review found no clear delivery plan for tackling digital exclusion, and no one named as accountable for producing one. Since then, the Scottish Government has published a Digital Strategy vision statement and an initial delivery plan (November 2025) — but that plan covers digital public services, not digital exclusion, and the vision statement's own Performance Framework is still described as "being refreshed." As SCVO noted last year, Scotland is still waiting for "strategy, delivery plan and visible leadership" on digital exclusion.</p>
<p>Meanwhile the numbers are moving the wrong way:</p>
<ul>
<li>Citizens Advice Scotland recorded a 59% rise in digitally excluded clients in two years (1,932 to 3,065)</li>
<li>One in six Scottish adults lack basic digital skills</li>
<li>Only around one in twelve eligible households use the discounted broadband social tariff — most have never heard it exists</li>
</ul>
<p>We're WIRES, campaigning for connectivity to be treated as essential infrastructure across all 32 council areas, including {$council}. We're asking councillors to say publicly that everyone in their ward deserves reliable, affordable connectivity — and that someone needs to be named responsible for closing this gap.</p>
<p><strong>Could you reply by {$replyBy} with either:</strong></p>
<ol>
<li>A short line we can quote, publicly attributed to you, or</li>
<li>Where {$council} already stands, so we record it accurately</li>
</ol>
<p><a href="https://wires.org.uk/accountability">See what we've found so far, council-by-council &rarr;</a></p>
<p>Thank you for your time,<br>William Ellis<br>WIRES — <a href="https://wires.org.uk">wires.org.uk</a></p>
<p style="font-size:13px;color:#666">If you'd rather not hear from us again, just reply and say so.</p>
</div>
HTML;
}
