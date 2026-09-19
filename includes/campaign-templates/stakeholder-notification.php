<?php

declare(strict_types=1);

/**
 * Email template for the one-off stakeholder notification — a courtesy heads-up to
 * sympathetic sector organisations about the accountability campaign, not an ask.
 * Shared between bin/send-stakeholder-notifications.php (the real sender) and any
 * public preview of the same content.
 */

function render_subject(array $row): string
{
    return 'WIRES has written to ~1,300 elected officials across Scotland — thought you\'d want to know';
}

function render_text_body(array $row): string
{
    $org = $row['organisation'];

    return <<<TXT
Hi {$org},

Quick heads-up rather than an ask. WIRES has spent the last few weeks writing
directly to everyone in Scotland with a lever to pull on digital exclusion:
around 1,200 councillors, every council Chief Executive, every council
Leader, and all 129 MSPs at Holyrood. Each was asked to address the gap
Audit Scotland identified in August 2024 — no delivery plan, no one named
accountable — and to say publicly where they stand.

Replies are starting to come in, and we're tracking all of it publicly and
in real time: https://wires.org.uk/accountability-campaign

Given your own work in this space, thought it was worth flagging directly
rather than you finding out secondhand. Happy to share more detail, data, or
sourcing if useful to you.

Best,
William Ellis
WIRES — wires.org.uk
TXT;
}

function render_html_body(array $row): string
{
    $org = e($row['organisation']);

    return <<<HTML
<div style="font-family:-apple-system,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;max-width:600px">
<p>Hi {$org},</p>
<p>Quick heads-up rather than an ask. WIRES has spent the last few weeks writing directly to everyone in Scotland with a lever to pull on digital exclusion: around 1,200 councillors, every council Chief Executive, every council Leader, and all 129 MSPs at Holyrood. Each was asked to address the gap Audit Scotland identified in August 2024 — no delivery plan, no one named accountable — and to say publicly where they stand.</p>
<p>Replies are starting to come in, and we're tracking all of it publicly and in real time: <a href="https://wires.org.uk/accountability-campaign">wires.org.uk/accountability-campaign</a></p>
<p>Given your own work in this space, thought it was worth flagging directly rather than you finding out secondhand. Happy to share more detail, data, or sourcing if useful to you.</p>
<p>Best,<br>William Ellis<br>WIRES — <a href="https://wires.org.uk">wires.org.uk</a></p>
</div>
HTML;
}
