<?php

declare(strict_types=1);

namespace Wires\MpTemplate;

/**
 * Email template for the MP accountability campaign — shared between
 * bin/send-mp-campaign.php (the real sender) and any public preview of the same
 * template.
 *
 * Distinct from both the council-level letters and the MSP letter: MPs sit at
 * Westminster, where telecoms regulation (Ofcom), the Universal Service Obligation,
 * and UK-wide funding programmes (Project Gigabit, Shared Rural Network) are reserved
 * matters — not something Holyrood or a council can act on.
 */

function render_subject(array $row): string
{
    return "Will you raise Scotland's connectivity gaps at Westminster?";
}

function render_text_body(array $row): string
{
    $name = $row['name'];
    $constituency = $row['constituency'];
    $replyBy = reply_by_date();

    return <<<TXT
Dear {$name},

We treat roads and water as essential infrastructure — something no one is
quietly left to go without. Connectivity deserves the same status, and Audit
Scotland's August 2024 review found no clear delivery plan for tackling
digital exclusion in Scotland, and no one named as accountable for producing
one.

Much of that gap sits with the Scottish Government and local councils — we've
already written to all of them. But some of it is squarely reserved to
Westminster: Ofcom's enforcement of the Universal Service Obligation, the
pace of Project Gigabit and the Shared Rural Network in {$constituency}, and
whether UK-wide telecoms affordability policy actually reaches the people
who need it.

As MP for {$constituency}, we're asking you three things:

1. Public backing — will you say publicly that digital exclusion deserves
   the same seriousness as any other essential-infrastructure gap?

2. A parliamentary question — will you press the relevant UK minister on
   Project Gigabit and Shared Rural Network delivery timelines for
   {$constituency}, and on whether Ofcom's Universal Service Obligation is
   actually being enforced for constituents who fall short of it?

3. Constituency casework — if constituents raise connectivity or
   affordability problems with you, will you flag the pattern to us, so we
   can track where reserved and devolved systems are both falling short?

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
    $constituency = e($row['constituency']);
    $replyBy = e(reply_by_date());

    return <<<HTML
<div style="font-family:-apple-system,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;max-width:600px">
<p>Dear {$name},</p>
<p>We treat roads and water as essential infrastructure — something no one is quietly left to go without. Connectivity deserves the same status, and Audit Scotland's August 2024 review found no clear delivery plan for tackling digital exclusion in Scotland, and no one named as accountable for producing one.</p>
<p>Much of that gap sits with the Scottish Government and local councils — we've already written to all of them. But some of it is squarely reserved to Westminster: Ofcom's enforcement of the Universal Service Obligation, the pace of Project Gigabit and the Shared Rural Network in {$constituency}, and whether UK-wide telecoms affordability policy actually reaches the people who need it.</p>
<p>As MP for {$constituency}, we're asking you three things:</p>
<ol>
<li><strong>Public backing</strong> — will you say publicly that digital exclusion deserves the same seriousness as any other essential-infrastructure gap?</li>
<li><strong>A parliamentary question</strong> — will you press the relevant UK minister on Project Gigabit and Shared Rural Network delivery timelines for {$constituency}, and on whether Ofcom's Universal Service Obligation is actually being enforced for constituents who fall short of it?</li>
<li><strong>Constituency casework</strong> — if constituents raise connectivity or affordability problems with you, will you flag the pattern to us, so we can track where reserved and devolved systems are both falling short?</li>
</ol>
<p><strong>Could you reply by {$replyBy}</strong> — either with a short statement we can quote publicly, or telling us what action you've taken?</p>
<p><a href="https://wires.org.uk/accountability">See what we've found so far &rarr;</a></p>
<p>Thank you for your time,<br>William Ellis<br>WIRES — <a href="https://wires.org.uk">wires.org.uk</a></p>
<p style="font-size:13px;color:#666">If you'd rather not hear from us again, just reply and say so.</p>
</div>
HTML;
}
