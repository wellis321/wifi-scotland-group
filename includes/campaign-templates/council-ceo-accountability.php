<?php

declare(strict_types=1);

/**
 * Email template for the council Chief Executive accountability campaign, shared
 * between bin/send-council-ceo-campaign.php (the real sender) and council-replies.php
 * (which renders this same template, with placeholder values, so the public can read
 * exactly what was sent — this file is the single source of truth for both).
 */

/**
 * Aberdeen City's CE post is interim, mid-recruitment — address the office, not a name
 * that may change within days. Only applies to the CEO send (default role) — a Leader
 * row (role: 'leader', using 'leader_name' instead of 'ceo_name') skips this, since it's
 * a CEO-specific situation, not a Leader one.
 */
function render_greeting(array $row): string
{
    $role = $row['role'] ?? 'ceo';
    if ($role === 'ceo' && $row['council_area'] === 'Aberdeen City') {
        return "Chief Executive's Office";
    }
    return $row['ceo_name'] ?? $row['leader_name'] ?? '';
}

function render_subject(array $row): string
{
    return "Is {$row['council_area']}'s digital service design leaving people behind?";
}

function render_text_body(array $row): string
{
    $council  = $row['council_area'];
    $greeting = render_greeting($row);
    $replyBy  = reply_by_date();

    return <<<TXT
Dear {$greeting},

We treat roads and water as essential infrastructure — something no one is
quietly left to go without. Connectivity deserves the same status, and Audit
Scotland's August 2024 review found no clear delivery plan for tackling
digital exclusion in Scotland, and no one named as accountable for producing
one.

The numbers are moving the wrong way: Citizens Advice Scotland recorded a 59%
rise in digitally excluded clients in two years (1,932 to 3,065), and one in
six Scottish adults lack basic digital skills — which is exactly why a
non-digital route for essential services isn't a nice-to-have.

We're WIRES, campaigning for connectivity to be treated as essential
infrastructure across all 32 council areas, including {$council}. We're
writing in parallel to all councillors, asking them to speak publicly on
this — we wanted council leadership to have the same visibility into where
things stand operationally. Rather than a general ask of you, we'd like to
know where {$council} stands on four practical questions — about day-to-day
practice, not just policy:

1. Staff and service design — Are staff trained to recognise when someone
   can't easily get online, and does every essential council service still
   have a working non-digital route, so no resident is cut off by skills,
   hardware, or address?

2. Affordability and access — What is {$council} doing to promote the
   discounted broadband social tariff (only around one in twelve eligible
   households currently use it), and what local options — public wifi
   points, community networks, library wifi — exist for residents who can't
   otherwise afford a home connection?

3. Built for unreliable connections — Do council digital services,
   especially anything residents rely on for e-learning, benefits, or
   housing, avoid unnecessary streaming and data costs and use reasonable
   file sizes? And if a connection drops mid-task, is that work saved
   automatically — for residents and staff alike — rather than lost?

4. Ownership — Audit Scotland's own review quoted Accounts Commission member
   Nichola Brown saying councils "must be clearer about how they will reduce
   digital exclusion in their local area." Is there a published digital
   inclusion action plan for {$council}, with someone named as accountable
   for it?

Could you reply by {$replyBy} — either with a short statement we can quote
publicly, or a summary of where {$council} stands, so we record it
accurately?

See what we've found so far, council-by-council:
https://wires.org.uk/council-replies

Thank you for your time,
William Ellis
WIRES — wires.org.uk

If you'd rather not hear from us again, just reply and say so.
TXT;
}

function render_html_body(array $row): string
{
    $council  = e($row['council_area']);
    $greeting = e(render_greeting($row));
    $replyBy  = e(reply_by_date());

    return <<<HTML
<div style="font-family:-apple-system,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;max-width:600px">
<p>Dear {$greeting},</p>
<p>We treat roads and water as essential infrastructure — something no one is quietly left to go without. Connectivity deserves the same status, and Audit Scotland's August 2024 review found no clear delivery plan for tackling digital exclusion in Scotland, and no one named as accountable for producing one.</p>
<p>The numbers are moving the wrong way: Citizens Advice Scotland recorded a 59% rise in digitally excluded clients in two years (1,932 to 3,065), and one in six Scottish adults lack basic digital skills — which is exactly why a non-digital route for essential services isn't a nice-to-have.</p>
<p>We're WIRES, campaigning for connectivity to be treated as essential infrastructure across all 32 council areas, including {$council}. We're writing in parallel to all councillors, asking them to speak publicly on this — we wanted council leadership to have the same visibility into where things stand operationally. Rather than a general ask of you, we'd like to know where {$council} stands on four practical questions — about day-to-day practice, not just policy:</p>
<ol>
<li><strong>Staff and service design</strong> — Are staff trained to recognise when someone can't easily get online, and does every essential council service still have a working non-digital route, so no resident is cut off by skills, hardware, or address?</li>
<li><strong>Affordability and access</strong> — What is {$council} doing to promote the discounted broadband social tariff (only around one in twelve eligible households currently use it), and what local options — public wifi points, community networks, library wifi — exist for residents who can't otherwise afford a home connection?</li>
<li><strong>Built for unreliable connections</strong> — Do council digital services, especially anything residents rely on for e-learning, benefits, or housing, avoid unnecessary streaming and data costs and use reasonable file sizes? And if a connection drops mid-task, is that work saved automatically — for residents and staff alike — rather than lost?</li>
<li><strong>Ownership</strong> — Audit Scotland's own review quoted Accounts Commission member Nichola Brown saying councils "must be clearer about how they will reduce digital exclusion in their local area." Is there a published digital inclusion action plan for {$council}, with someone named as accountable for it?</li>
</ol>
<p><strong>Could you reply by {$replyBy}</strong> — either with a short statement we can quote publicly, or a summary of where {$council} stands, so we record it accurately?</p>
<p><a href="https://wires.org.uk/council-replies">See what we've found so far, council-by-council &rarr;</a></p>
<p>Thank you for your time,<br>William Ellis<br>WIRES — <a href="https://wires.org.uk">wires.org.uk</a></p>
<p style="font-size:13px;color:#666">If you'd rather not hear from us again, just reply and say so.</p>
</div>
HTML;
}
