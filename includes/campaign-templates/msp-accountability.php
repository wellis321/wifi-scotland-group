<?php

declare(strict_types=1);

namespace Wires\MspTemplate;

/**
 * Email template for the MSP accountability campaign — shared between
 * bin/send-msp-campaign.php (the real sender) and any public page that renders this
 * same template with placeholder values, so the public can read exactly what was sent.
 *
 * Distinct from the council-level letters (includes/campaign-templates/council-ceo-accountability.php):
 * MSPs sit in the devolved Scottish Parliament, not local government, so the ask is
 * about parliamentary levers (questions, committees, public statements) rather than
 * council service design or budget.
 */

/** "MSP for Paisley (Constituency)" -> "As MSP for Paisley"; "... (Region)" -> "As a regional MSP for ..." */
function render_msp_role_phrase(array $row): string
{
    $role = $row['role'];
    if (preg_match('/^MSP for (.+?)\s*\(Region\)$/i', $role, $m)) {
        return "As a regional MSP for {$m[1]}";
    }
    if (preg_match('/^MSP for (.+?)\s*\(Constituency\)$/i', $role, $m)) {
        return "As MSP for {$m[1]}";
    }
    return 'As an MSP';
}

function render_subject(array $row): string
{
    return "Will you raise Scotland's missing digital exclusion plan at Holyrood?";
}

function render_text_body(array $row): string
{
    $name = $row['name'];
    $rolePhrase = render_msp_role_phrase($row);
    $replyBy = reply_by_date();

    return <<<TXT
Dear {$name},

We treat roads and water as essential infrastructure — something no one is
quietly left to go without. Connectivity deserves the same status, and Audit
Scotland's August 2024 review found no clear delivery plan for tackling
digital exclusion in Scotland, and no one named as accountable for producing
one.

The numbers are moving the wrong way: Citizens Advice Scotland recorded a 59%
rise in digitally excluded clients in two years (1,932 to 3,065), and one in
six Scottish adults lack basic digital skills.

We're WIRES. We've already written to every council, Chief Executive, and
council Leader in Scotland — but the gap Audit Scotland identified is a
devolved, national one, and closing it needs action at Holyrood, not just in
council chambers. {$rolePhrase}, we're asking you three things:

1. Public backing — will you say publicly that digital exclusion deserves
   the same seriousness as any other essential-infrastructure gap?

2. A parliamentary question — will you lodge a written question to the
   Scottish Government asking when the digital exclusion delivery plan
   Audit Scotland called for will be published, and who will be named
   accountable for it?

3. Committee scrutiny — if you sit on a committee with any bearing on this,
   will you raise it there?

Could you reply by {$replyBy} — either with a short statement we can quote
publicly, or telling us what action you've taken?

See what we've found so far: https://wires.org.uk/accountability

Thank you for your time,
William Ellis
WIRES — wires.org.uk

If you'd rather not hear from us again, just reply and say so.
TXT;
}

function render_html_body(array $row): string
{
    $name = e($row['name']);
    $rolePhrase = e(render_msp_role_phrase($row));
    $replyBy = e(reply_by_date());

    return <<<HTML
<div style="font-family:-apple-system,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;max-width:600px">
<p>Dear {$name},</p>
<p>We treat roads and water as essential infrastructure — something no one is quietly left to go without. Connectivity deserves the same status, and Audit Scotland's August 2024 review found no clear delivery plan for tackling digital exclusion in Scotland, and no one named as accountable for producing one.</p>
<p>The numbers are moving the wrong way: Citizens Advice Scotland recorded a 59% rise in digitally excluded clients in two years (1,932 to 3,065), and one in six Scottish adults lack basic digital skills.</p>
<p>We're WIRES. We've already written to every council, Chief Executive, and council Leader in Scotland — but the gap Audit Scotland identified is a devolved, national one, and closing it needs action at Holyrood, not just in council chambers. {$rolePhrase}, we're asking you three things:</p>
<ol>
<li><strong>Public backing</strong> — will you say publicly that digital exclusion deserves the same seriousness as any other essential-infrastructure gap?</li>
<li><strong>A parliamentary question</strong> — will you lodge a written question to the Scottish Government asking when the digital exclusion delivery plan Audit Scotland called for will be published, and who will be named accountable for it?</li>
<li><strong>Committee scrutiny</strong> — if you sit on a committee with any bearing on this, will you raise it there?</li>
</ol>
<p><strong>Could you reply by {$replyBy}</strong> — either with a short statement we can quote publicly, or telling us what action you've taken?</p>
<p><a href="https://wires.org.uk/accountability">See what we've found so far &rarr;</a></p>
<p>Thank you for your time,<br>William Ellis<br>WIRES — <a href="https://wires.org.uk">wires.org.uk</a></p>
<p style="font-size:13px;color:#666">If you'd rather not hear from us again, just reply and say so.</p>
</div>
HTML;
}
