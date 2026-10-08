-- MySQL / MariaDB schema for WIRES (wires.scot)
-- Charset: utf8mb4 for full Unicode support

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS member_signups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(160) NOT NULL,
  email VARCHAR(255) NOT NULL,
  locality VARCHAR(120) DEFAULT NULL COMMENT 'Town, council area, or region',
  interests TEXT DEFAULT NULL COMMENT 'Optional notes from member',
  consent TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Privacy / contact consent',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_member_signups_email (email),
  KEY idx_member_signups_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(160) NOT NULL,
  email VARCHAR(255) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contact_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Confidential tips. `ciphertext` is a libsodium sealed-box, base64-encoded,
-- produced client-side in the browser before submission — the server and this
-- table never see plaintext. There is deliberately no name/email column: the
-- whole point is not to force identity on someone reporting something sensitive.
-- Decrypt only offline, with the private key, using bin/decrypt-tip.php — see
-- includes/tip_crypto.php for the public key this is encrypted against.
CREATE TABLE IF NOT EXISTS secure_tips (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ciphertext TEXT NOT NULL COMMENT 'libsodium crypto_box_seal output, base64',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_secure_tips_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS news_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(220) NOT NULL,
  slug VARCHAR(220) NOT NULL,
  summary VARCHAR(500) DEFAULT NULL,
  body MEDIUMTEXT NOT NULL,
  published_at DATE NOT NULL,
  group_id INT UNSIGNED DEFAULT NULL COMMENT 'Optional: link to local_groups.id to tag as local news',
  image_filename VARCHAR(160) DEFAULT NULL COMMENT 'Filename from /images/ — e.g. card-fibre.jpg. NULL = default banner.',
  image_alt VARCHAR(300) DEFAULT NULL COMMENT 'Alt text for image_filename — should be set whenever image_filename is set',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_news_items_slug (slug),
  KEY idx_news_published (published_at),
  KEY idx_news_group_id (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Starter editorial items (factual framing; verify figures on official pages before quoting in press)
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'Why we are campaigning for connectivity as everyday infrastructure',
  'why-connectivity-infrastructure',
  'Access to affordable, reliable internet underpins work, learning, health information, and participation in public life.',
  '<p>Reliable connectivity is no longer a niche luxury: it shapes whether people can apply for jobs, attend medical appointments remotely where offered, follow coursework, and stay in touch with family and friends.</p><p>This campaign argues that Scotland should treat <strong>affordable, dignified access</strong> as part of the baseline expectations we have for housing, transport, and utilities—while celebrating community-led models that show what is possible when people organise around shared infrastructure.</p>',
  '2026-01-15'
),
(
  'Reading official strategy alongside lived experience',
  'strategy-and-lived-experience',
  'Policy documents matter—but they need to be tested against what people experience in homes, tenements, and rural communities.',
  '<p>Scottish Government digital and connectivity materials set direction for public investment and partnership with industry. Local authorities and community organisations often see the gaps first: patchy mobile coverage, complex voucher processes, or costs that remain out of reach.</p><p>Our role as a campaign is to <strong>amplify community questions</strong>, signpost official programmes, and push for outcomes that are easy to understand and fair to access.</p>',
  '2026-02-02'
),
(
  'Global spotlight: learning from community networks',
  'global-community-networks',
  'From Catalonia to New York City, volunteer and cooperative models demonstrate that connectivity can be built and governed differently.',
  '<p>Large-scale commercial roll-out will remain part of the picture—but it is not the only model. Community networks and non-profit infrastructure projects show that governance, transparency, and local ownership can be designed in from the start.</p><p>See our <a href="/global-spotlight.php">Global spotlight</a> page for examples and primary links.</p>',
  '2026-03-20'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-07 news sweep — verify each figure/date on the linked source before publishing to production
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'R100 passes 100,000 connections — but delivery in the north may slip again',
  'r100-100000-connections-milestone',
  'Scotland''s £697m Reaching 100% programme has now connected more than 100,000 premises, though some north-of-Scotland contract areas are reportedly at risk of running past their original dates.',
  '<p>The Scottish Government''s R100 programme has passed <strong>100,000 premises connected</strong>, a milestone worth marking: for most of those households and businesses, this is the first time superfast broadband has reached their door.</p><p>The picture is not uniform. Reporting on the north Scotland contract has raised the possibility of parts of the build slipping to 2027, later than residents were originally told. Remaining areas across the three R100 contracts are scheduled for completion by March 2028.</p><p>Progress is real and worth acknowledging — so is holding delivery dates to account when they move. See current scheme status on <a href="/get-help.php#r100">Get help</a>.</p>',
  '2026-06-15'
),
(
  'Ullapool''s community broadband network closes as national schemes fail small providers',
  'highland-community-broadband-closure',
  'Highland Community Broadband, a volunteer-founded wireless network serving remote Wester Ross since 2017, shut down at the end of April 2026 after larger-scale voucher schemes proved unworkable for a small operator.',
  '<p>Highland Community Broadband began in 2017 to bring a working internet connection to part of Wester Ross that commercial providers were not going to reach. It closed on 30 April 2026, citing rising backhaul, legal, and maintenance costs it could no longer sustain.</p><p>The detail that should concern anyone designing connectivity policy: the operator says it tried to use the R100 voucher scheme and the UK Gigabit voucher scheme to fund network upgrades, and found both built around the scale and paperwork of much larger companies, not a community-run network.</p><p>This is the flip side of the community-network model we celebrate on our <a href="/global-spotlight.php">Global spotlight</a> page. Volunteer and cooperative networks can do things commercial rollout won''t — but only if public schemes are actually built to support them, not just the incumbents.</p>',
  '2026-05-04'
),
(
  'MPs open inquiry into rural and island connectivity — here''s how to respond',
  'scottish-affairs-committee-connectivity-inquiry',
  'The House of Commons Scottish Affairs Committee is taking public evidence on broadband and mobile coverage in rural and island Scotland. Submissions are a direct way to get lived experience in front of policymakers.',
  '<p>The Scottish Affairs Committee at Westminster has launched an inquiry into digital connectivity across rural and remote Scotland, with a public survey open for residents, community groups, and businesses to submit their experience.</p><p>This is exactly the kind of moment our campaign exists for: a live, official channel where a account of a bad signal day, a missed voucher application, or a rollout date that keeps moving actually reaches the people who write policy.</p><p>Visit our <a href="/get-involved.php">Get involved</a> page for guidance on taking part, or go directly to the committee''s inquiry page to check the submission deadline before it closes.</p>',
  '2026-07-08'
),
(
  'Ofcom: 7 in 10 eligible households still don''t know social tariffs exist',
  'ofcom-social-tariff-awareness-gap-2026',
  'Take-up of discounted broadband for people on benefits remains below 1 in 10 eligible households, Ofcom''s 2026 pricing report found — even as more providers than ever offer one.',
  '<p>Ofcom''s latest Pricing Trends report puts social tariff take-up at 532,000 households out of an estimated 4–8 million eligible — still under 10%. The regulator''s own figure for the reason is blunt: roughly seven in ten eligible households say they have never heard that a cheaper tariff exists for them.</p><p>The supply side has improved: broadband social tariffs have grown from three providers in 2020 to more than 30 by the end of 2025, and most major providers now offer one. The gap is now almost entirely about awareness, not availability.</p><p>Check who qualifies and how to switch on our <a href="/get-help.php#social-tariffs">Get help</a> page — it takes a few minutes and does not require waiting for a current contract to end.</p>',
  '2026-02-20'
),
(
  'Audit Scotland: government still has no clear plan to tackle digital exclusion',
  'audit-scotland-digital-exclusion-no-plan',
  'Independent auditors say the Scottish Government and COSLA have yet to set out a coherent action plan or clear ownership for tackling digital exclusion, despite it affecting more than a million people in Scotland.',
  '<p>Audit Scotland''s review of digital exclusion work found no single, clearly owned action plan across the Scottish Government and COSLA, despite estimates that more than a million people in Scotland lack meaningful digital access and around one in six adults lack the digital skills for everyday life.</p><p>This is not a story about a single missed target. It is a story about accountability: infrastructure spending like R100 addresses one part of the problem, but skills, devices, and affordable connections need the same clarity of ownership that roads and water do.</p><p>Read the background on why we treat this as an infrastructure issue, not a charity appeal, on <a href="/why-it-matters.php">Why it matters</a>.</p>',
  '2026-06-01'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Articles previously authored directly in the live database via /admin, backfilled
-- here so schema.sql stays a complete, accurate mirror of production. Figures below
-- were corrected during a July 2026 fact-check pass — see figures.php for sourcing.
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'R100 hits 100,000 homes — but hundreds of thousands in Scotland still can''t get online',
  'r100-hits-100000-homes-but-700000-in-scotland-still-cant-get-online',
  'The Scottish Government''s R100 programme has now delivered faster broadband to over 100,000 homes and businesses. Progress worth noting — and an estimated 9% of Scottish households still without any internet connection worth noting more.',
  '<p>The R100 Reaching 100% programme has passed a milestone: more than 100,000 homes and businesses in Scotland now have faster broadband connections as a result of the £697 million programme. An independent evaluation found that 76% of connected businesses reported increased productivity, and around two-thirds of residents said they felt more connected as a result.</p><p>This is real progress, and it should be acknowledged. R100 is doing something that commercial providers were not willing to do.</p><p>But the same period tells a more uncomfortable story: <strong>an estimated 9% of Scottish households — around 490,000 people — still have no internet connection at all</strong>. The programme aims to reach another 113,000 premises by March 2028. That still leaves a substantial portion of Scotland behind.</p><p>And connectivity is not only about whether a wire reaches a building. Even where R100 delivers infrastructure, the questions of affordability, device access, digital skills, and language barriers remain unanswered by any current programme. Coverage is necessary. It is not sufficient.</p><p>The UK Government has separately announced a £157 million Project Gigabit contract for remote Scottish communities in the Highlands, Outer Hebrides, and islands including Skye, Islay, and Tiree. Again, investment worth welcoming — and investment that should be accompanied by clear, public reporting on who benefits and when.</p><p class="meta">Sources: <a href="https://www.ispreview.co.uk/index.php/2026/01/r100-gigabit-broadband-rollout-reaches-96347-premises-in-scotland.html">ISPreview (January 2026)</a> · <a href="https://www.gov.scot/publications/r100-interim-evaluation/">Scottish Government, R100 Interim Evaluation (November 2025)</a> · <a href="https://www.gov.uk/government/news/scotlands-most-remote-towns-and-villages-get-huge-broadband-upgrade-as-uk-government-vows-to-end-digital-exclusion-plight">UK Government announcement</a> · <a href="https://www.gov.scot/publications/scottish-household-survey-2023-results-internet/">Scottish Household Survey 2023</a></p>',
  '2026-01-20'
),
(
  '70% of people on benefits have never heard of the cheaper broadband they qualify for',
  '70-of-people-on-benefits-have-never-heard-of-the-cheaper-broadband-they-qualify-for',
  'Ofcom''s latest figures show 532,000 UK households using a social tariff. There are now 30+ options. And 70% of eligible people have never heard of any of them. That is not a communications problem. That is a policy failure.',
  '<p>Ofcom''s latest data shows 532,000 UK households have taken up a social broadband or mobile tariff — up from 506,000 the previous year. There are now more than 30 social tariff options available, costing between £12 and £24 a month, compared with just three options in 2020.</p><p>And yet: <strong>70% of households who qualify for a social tariff have never heard that they exist.</strong></p><p>Ofcom''s current estimate for the eligible population is 6.2 million UK households on Universal Credit. At current take-up, fewer than one in twelve is actually using one. The gap between entitlement and awareness represents millions of households overpaying for broadband — or going without it entirely — while qualifying for a cheaper deal they simply do not know about.</p><p>April 2026 also marks the end of inflation-linked in-contract price rises under industry commitments. That is a positive step. But it does not solve the awareness problem.</p><p>Ofcom has the regulatory power to require ISPs to actively promote social tariffs, not merely offer them. It has not used that power. WIRES believes it should.</p><p>If you are on Universal Credit, Pension Credit, or certain other benefits, ask your broadband provider today about their social tariff. You do not need to provide paper proof — they can verify your eligibility automatically through the DWP system. Our <a href="/get-help">Help getting online</a> page lists what is available.</p><p class="meta">Source: <a href="https://www.ispreview.co.uk/index.php/2026/02/ofcom-find-532000-uk-homes-taking-social-broadband-and-mobile-tariffs.html">ISPreview / Ofcom, February 2026</a></p>',
  '2026-02-24'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-07-21 news sweep — verify each figure/date on the linked source before publishing to production
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'Shared Rural Network passes 140 masts UK-wide — Islay gets a second not-spot fixed',
  'srn-140-masts-islay-second-not-spot',
  'The government-backed Shared Rural Network has switched on its 140th mobile mast, with 49 now live in Scotland — including a second site on Islay that restores signal from all four networks to an area that previously had none.',
  '<p>The Shared Rural Network — the joint UK Government and mobile operator scheme to close 4G mobile not-spots — has activated 140 masts across the UK by the end of June 2026, with 49 of those in Scotland.</p><p>The latest addition is a second "total not-spot" site on Islay, bringing coverage from EE, O2, Three, and Vodafone to part of the island that previously had no signal from any network at all. Other recent Scottish sites include Arran, Jura, Dukes Pass in the Trossachs, and Tarbert on Kintyre.</p><p>Scotland''s 11% total not-spot rate is still more than double the UK average of 5%, so this is real but partial progress — worth tracking against how many sites remain scheduled.</p><p class="meta">Source: <a href="https://srn.org.uk/news/">Shared Rural Network news</a> &middot; <a href="https://www.thinkbroadband.com/news/shared-rural-network-reaches-50-live-masts-in-scotland">thinkbroadband</a></p>',
  '2026-07-21'
),
(
  'Vodafone drops its £12 social tariff — Virgin Media''s £12.50 deal is now the cheapest',
  'vodafone-drops-cheapest-social-tariff-2026',
  'Vodafone withdrew its £12/month Essentials tariff, raising its entry price to £20. Virgin Media''s Essential Broadband at £12.50 is now the lowest verified social tariff on the market — a reminder that these deals and their prices shift, so it''s worth checking before you apply.',
  '<p>Social tariffs — discounted broadband for people on Universal Credit and other qualifying benefits — keep changing, and not always for the better. Vodafone has withdrawn its long-running £12/month entry-level tariff; its Essentials plan is now £20/month.</p><p>Virgin Media''s Essential Broadband, at £12.50/month, is currently the cheapest verified social tariff on the market. BT, Sky, and Community Fibre all still offer their own versions in the same £12.50&ndash;£20 range.</p><p>The prices move; the eligibility and the point don''t: if you''re on Universal Credit or another qualifying benefit, you''re likely paying more than you need to. Providers can verify your benefit automatically through the DWP &mdash; no paperwork required.</p><p>Check who''s currently cheapest and how to switch on our <a href="/get-help.php#social-tariffs">Get help</a> page.</p><p class="meta">Source: <a href="https://broadbandswitch.uk/social-tariffs-uk.html">BroadbandSwitch.uk, verified social tariffs (July 2026)</a></p>',
  '2026-07-21'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-07-31 news sweep — verify each figure/date on the linked source before publishing to production
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'Shared Rural Network''s 50th mast goes live in Scotland — coverage bigger than Edinburgh and Glasgow combined',
  'srn-50th-scotland-mast',
  'The government-backed Shared Rural Network switched on its 50th mobile mast in Scotland on the remote Ardnamurchan peninsula, extending all-four-operator 4G coverage across more than 1,900 square kilometres of rural Scotland — an area larger than Edinburgh and Glasgow combined.',
  '<p>The Shared Rural Network''s 50th Scottish mast went live near Branault, on the remote Ardnamurchan peninsula, bringing 4G coverage from EE, O2, Three, and Vodafone to a part of the Highlands that previously had patchy or no signal.</p><p>Fifty masts is a milestone worth marking on its own — but the scale is the real story: UK Government-funded upgrades now cover more than 1,900 square kilometres of rural Scotland, an area larger than Edinburgh and Glasgow combined. That is coverage that would not exist without the public subsidy behind the scheme; commercial operators had left these areas unserved for years.</p><p>It follows our report on the programme passing 140 masts UK-wide. Progress is real — the remaining gap is what happens for communities not yet on the list.</p><p class="meta">Source: <a href="https://www.ispreview.co.uk/index.php/2026/07/50-uk-gov-funded-4g-mobile-rural-mast-upgrades-now-live-in-scotland.html">ISPreview (July 2026)</a> &middot; <a href="https://srn.org.uk/ring-it-on-rural-scots-get-mobile-signal-boost-as-50th-uk-government-funded-mast-goes-live/">Shared Rural Network</a></p>',
  '2026-07-09'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-09-13 news sweep — verify each figure/date on the linked source before publishing to production
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'Shared Rural Network hits 150 masts and 95% UK coverage — a year ahead of schedule',
  'srn-150-masts-95-percent-uk-coverage',
  'The UK-wide Shared Rural Network has switched on its 150th mobile mast and reached 95% 4G coverage of the UK landmass a year early. More than 50 of those masts are in Scotland, with up to 44 more publicly funded masts now building — the first already live in the Western Isles.',
  '<p>The Shared Rural Network — the joint UK Government and mobile operator scheme to close 4G not-spots — has activated its 150th mast, and the programme says it has now reached 95% 4G coverage of the UK landmass, a year ahead of its original target.</p><p>More than 50 of those masts are in Scotland, and the government says up to 44 more publicly funded masts are now building across the country, with the first of that new tranche already live in the Western Isles. Recent activations include a site in Loch Lomond and The Trossachs National Park.</p><p>This is real, measurable progress on mobile not-spots — the kind of infrastructure spending WIRES wants held to the same account as any other public investment. Scotland''s total not-spot rate has been running at more than double the UK average, so the test now is whether the remaining Scottish sites keep building on schedule rather than slipping once the easier coverage gains are banked.</p><p class="meta">Source: <a href="https://www.gov.uk/government/news/150-rural-4g-masts-now-live-so-holidaymakers-can-switch-off-without-being-cut-off">UK Government press release (14 August 2026)</a> &middot; <a href="https://srn.org.uk/news/">Shared Rural Network news</a></p>',
  '2026-08-14'
),
(
  'Scottish Government''s first Digital Strategy delivery plan lands — but not for digital exclusion',
  'digital-strategy-scotland-vision-statement-no-delivery-plan',
  'In November 2025, the Scottish Government published its Digital Strategy vision statement alongside the first in a promised series of delivery plans — but that plan covers digital public services, not digital exclusion, which Audit Scotland flagged as lacking a plan or a named owner over a year earlier.',
  '<p>In August 2024, Audit Scotland told the Scottish Government and COSLA to publish a refreshed national digital strategy and a detailed, measurable delivery plan by the end of 2024/25. Both deadlines passed. In September 2025, Third Force News summarised the position as "no leadership, no momentum."</p><p>On 18 November 2025, the Scottish Government published a <em>Digital Strategy for Scotland: vision statement</em>, developed jointly with COSLA — real movement, and worth acknowledging as such. Alongside it came the first in a promised series of delivery plans: <em>Digital strategy for Scotland: sustainable digital public services - delivery plan 2025-2028</em>.</p><p>That plan is real, but its scope is digital public services — not digital exclusion, the specific gap Audit Scotland''s review was about. The vision statement''s own Performance Framework, meant to connect outcomes to accountability, is described as still being refreshed. Almost a year on, no plan or named accountable lead for digital exclusion itself has followed.</p><p>We''ve updated our <a href="/accountability">accountability tracker</a> to reflect this more precisely — crediting the real delivery plan that has landed, while keeping the substantive question open for the specific gap this campaign exists to close.</p><p class="meta">Source: <a href="https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/">Scottish Government: Digital Strategy for Scotland: vision statement (18 Nov 2025)</a> &middot; <a href="https://tfn.scot/news/no-leadership-no-momentun-scottish-government-has-failed-to-act-on-digital-exclusion">Third Force News</a></p>',
  '2026-09-13'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-09-27 news sweep — verify each figure/date on the linked source before publishing to production
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'GoFibre reaches six more Aberdeenshire villages — about 1,000 homes gain full fibre',
  'gofibre-north-east-scotland-six-more-villages',
  'GoFibre has started construction in six more Aberdeenshire communities — St Fergus, Inchmarlo, Kincardine O''Neil, Collieston, Newburgh, and Kingswells — bringing full fibre broadband to around 1,000 homes and businesses that have never had it before, part of its £105m Project Gigabit contract for North East Scotland.',
  '<p>GoFibre, the Project Gigabit contractor delivering full fibre broadband across North East Scotland, has started construction in six more Aberdeenshire communities: St Fergus, Inchmarlo, Kincardine O''Neil, Collieston, Newburgh, and Kingswells. Around 1,000 homes and businesses across these areas will get full fibre broadband for the first time.</p><p>The build is part of a £105m contract — funded by the UK Government and delivered through the Scottish Government''s Project Gigabit programme — targeting 63,000 premises across Aberdeenshire, Aberdeen City, Angus, Dundee, Highland, Moray, and Perth &amp; Kinross that commercial rollouts were expected to miss. The first connections went live earlier this year in Ezdell, Inverbervie, and Longforgan, with Montrose, Dunkeld, Bankfoot, Newtonhill, Portlethen, Alyth, and Coupar Angus following through the summer.</p><p>It''s real progress for the people in these six villages — until now, none of them had a full fibre option at all. But it''s worth keeping the scale in view: this is around 1,000 of the 63,000 premises this contract covers, awarded back in July 2025. And infrastructure alone doesn''t close the exclusion gap — GoFibre''s entry-level package starts at £22.50 a month, well above the £10&ndash;£15 social tariffs some providers offer to households on qualifying benefits. Once a full fibre line reaches a street, it''s still worth checking for a cheaper option — see our <a href="/get-help">get help page</a>.</p><p class="meta">Source: <a href="https://www.ispreview.co.uk/index.php/2026/09/gofibres-sept-2026-update-on-north-east-scotland-gigabit-broadband-build.html">ISPreview UK (17 September 2026)</a> &middot; <a href="https://www.gov.scot/news/boosting-broadband-in-the-north-east/">Scottish Government: Boosting broadband in the North East</a></p>',
  '2026-09-27'
),
(
  'Ofcom sets the rural repair target at what Openreach already achieves, not the 83% Scotland''s consumer body asked for',
  'ofcom-telecoms-access-review-rural-repair-target',
  'In March 2026, Ofcom published its five-year Telecoms Access Review, setting fibre and repair rules through 2031. Consumer Scotland had asked for 83% of full-fibre faults in the hardest-to-reach areas to be fixed within one working day. Ofcom set the standard at 81%, the level Openreach was already achieving.',
  '<p>Ofcom''s Telecoms Access Review sets the rules Openreach and other network operators must follow for the next five years — including how quickly they must fix a fault in areas with no realistic prospect of a rival network ever being built, what Ofcom calls "Area 3". That category covers 14% of the UK and a large share of rural Scotland.</p><p>Ofcom had proposed that 79% of full-fibre faults in Area 3 be fixed within one working day. Consumer Scotland, the statutory body representing Scottish consumers, asked for 83%, and for 98% rather than 96% to be fixed within 11 working days. Its case: around 16,000 premises in Scotland still don''t have a decent broadband connection, full fibre reaches only 44% of rural Scottish premises against 65% for Scotland overall, and more than 30% of Scottish postcodes fall into Area 3, against 18% across the UK — so a standard set for the whole UK matters more here than anywhere else.</p><p>Ofcom''s final statement, published 17 March 2026, set the standard at 81% — the level Openreach was already achieving in these areas — and left the 11-day standard at 96%. It said it had "decided not to introduce higher standards as suggested" by Sky and Consumer Scotland, because a standard above Openreach''s existing performance would not fit its aim of "a backstop against any future deterioration in performance." That is a floor to stop things getting worse, not a target to make them better. One real gain: the standard applies from 2026/27, a year earlier than Ofcom first proposed.</p><p>It''s exactly the kind of decision our letters to Scotland''s 57 MPs ask them to scrutinise: enforcement of telecoms standards is a reserved matter, decided at a UK regulator, not Holyrood. A repair target isn''t an abstract number — it''s how many extra days a household or business in a Scottish glen or island goes without a working line. We''ll be tracking whether Ofcom revisits this before the framework runs its course in 2031.</p><p class="meta">Source: <a href="https://www.ofcom.org.uk/siteassets/resources/documents/consultations/statement-promoting-competition-and-investment-in-fibre-networks-telecoms-access-review-2026-31/main-documents/volume-5-quality-of-service.pdf">Ofcom: Telecoms Access Review 2026-31 statement, Volume 5: Quality of service (17 March 2026)</a> &middot; <a href="https://consumer.scot/publications/ofcom-telecoms-access-review-2026-2031-html/">Consumer Scotland: response to the Telecoms Access Review</a></p><p class="meta"><strong>Correction, 5 October 2026:</strong> An earlier version of this article said Ofcom''s final rules kept the target at 79%, and that Scotland accounts for more than 30% of the UK''s Area 3 postcodes. The 79% figure was Ofcom''s original proposal; its final decision was 81%. The 30% figure is the share of Scottish postcodes that fall within Area 3. The headline and text have been corrected. See our <a href="/corrections">corrections log</a>.</p>',
  '2026-09-27'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-10-01 — Scottish Government written answers on a Digital Inclusion Delivery Plan
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'Scottish Government promises a digital inclusion delivery plan ''later this year''',
  'scottish-government-promises-digital-inclusion-delivery-plan',
  'Answering written questions from Carol Mochan MSP, the Scottish Government says it is developing a Digital Inclusion Delivery Plan, to be published later this year — the plan Audit Scotland called for in August 2024. It will set out actions and how progress will be measured. It doesn''t yet say who will be accountable.',
  '<p>On 8 September 2026, Carol Mochan, Scottish Labour MSP for South Scotland, lodged a set of written questions drawing on Family Fund research: 56% of surveyed families raising disabled children experienced digital exclusion, and only 7% met the criteria for digital inclusion. The Scottish Government''s answers, given by Tom Arthur on 18 September, contain the line this campaign has been waiting two years to read: "we are developing a Digital Inclusion Delivery Plan, which will be published later this year."</p><p>The plan "will set out clear actions to address the wider barriers that prevent people from participating digitally and how we will work with partners, including local government," and, in a separate answer, "how progress will be measured and reported" — the measurable outcomes Audit Scotland found missing. The same answers say the Government has committed more than £870,000 since 2024 to over 50 local digital inclusion projects, and has no current plans for a statutory duty on public services to identify and respond to digital exclusion.</p><p>Two things to watch. First, timing: "later this year" means by the end of December — after a 2024/25 deadline Audit Scotland set that has already passed. Second, ownership: Audit Scotland''s finding wasn''t only that there was no plan, but that it was unclear who was responsible. None of the answers says who will be accountable for delivering this one.</p><p>Our letters to all 129 MSPs, sent less than two weeks after these questions were lodged, asked them to press for exactly this — when the plan would be published. Now there''s a date to hold the Government to. We''ve updated our <a href="/accountability">accountability tracker</a>, and we''ll report back when the plan is published, or when "later this year" runs out.</p><p class="meta">Source: <a href="https://www.parliament.scot/chamber-and-committees/questions-and-answers/question?ref=S7W-03032">Scottish Parliament written answer S7W-03032 (18 September 2026)</a> &middot; <a href="https://www.parliament.scot/chamber-and-committees/questions-and-answers/question?ref=S7W-03034">S7W-03034</a> &middot; <a href="https://www.parliament.scot/chamber-and-committees/questions-and-answers/question?ref=S7W-03033">S7W-03033</a> &middot; <a href="https://audit.scot/publications/tackling-digital-exclusion">Audit Scotland: Tackling digital exclusion (August 2024)</a></p>',
  '2026-10-01'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-10-05 — BT acquires TalkTalk; CMA invitation to comment closes 9 October
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'BT has bought TalkTalk. The regulator wants your view by Friday 9 October',
  'bt-buys-talktalk-cma-comment-deadline',
  'BT has acquired TalkTalk''s broadband and wholesale businesses out of administration, keeping 2.5 million customers connected. It also leaves one company owning BT, EE, Plusnet, TalkTalk and Openreach. The competition regulator is taking comments until Friday 9 October.',
  '<p>On 5 October 2026, BT acquired TalkTalk Telecommunications and its wholesale arm, PlatformX, out of administration, at an estimated cost of around £400m. BT says the deal protects 1.5 million retail and 1 million wholesale customers, including vulnerable households and connections used by health and emergency services, and calls it "the only viable option to keep millions of customers connected and supported."</p><p>A rescue is better than a collapse. But look at what it leaves. BT already owns BT, EE and Plusnet, as well as Openreach, the network most other providers rent their lines from. TalkTalk has long sold itself on price. Folding it into the largest group in the market removes one of the cheaper alternatives for households watching every pound — the people this campaign exists for.</p><p>The UK Government has intervened. The Culture Secretary issued a public interest intervention notice the same day, citing among its grounds the "supply to customers who are or may be vulnerable." The Competition and Markets Authority must report by 19 October. BT says the two companies will operate separately and continue to compete until the review concludes.</p><p>The CMA is inviting comments from any interested party until <strong>Friday 9 October</strong>, by email to <a href="mailto:bt.talktalk@cma.gov.uk">bt.talktalk@cma.gov.uk</a>. WIRES has <a href="/news-item?slug=wires-cma-submission-bt-talktalk">sent a submission</a> asking that any approval protects low-cost tariffs, makes sure TalkTalk customers on qualifying benefits are offered a <a href="/get-help#social-tariffs">social tariff</a>, and rules out forced price rises for vulnerable households. If you are a TalkTalk customer in Scotland, your experience is exactly the evidence the regulator should hear. A few lines is enough.</p><p class="meta">Source: <a href="https://newsroom.bt.com/bt-group-has-acquired-talktalk-and-platformx-ensuring-continuity-of-service-for-millions-of-customers/">BT Group announcement (5 October 2026)</a> &middot; <a href="https://www.gov.uk/cma-cases/bt-slash-talktalk-merger-inquiry">CMA: BT / TalkTalk merger inquiry</a></p>',
  '2026-10-05'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-10-06 — Findings from checking all 32 councils' published digital inclusion plans
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'Only one of 24 Scottish councils we checked has a standalone plan to get residents online',
  'council-digital-inclusion-plans-checked',
  'We read the published digital strategies of Scotland''s 32 councils. Of the 24 we could verify, only Glasgow has a standalone strategy on digital exclusion. In 22, it is a theme inside a plan about the council''s own services. Eight we couldn''t confirm.',
  '<p>Audit Scotland found in 2024 that Scotland had no clear plan for reducing digital exclusion and no one clearly responsible. We wanted to know whether that holds at council level, so we read what each of Scotland''s 32 councils has published. Not summaries of it: the strategies, delivery plans and committee papers themselves.</p><p>We could verify 24. Only one, Glasgow, has a standalone strategy: its Digital Housing Strategy 2022–2028. In 22 others, digital inclusion or connectivity appears inside a wider plan, usually a "digital strategy" that is mostly about the council''s own systems and online services. In Clackmannanshire, a council report covering 2019–21 said a Digital Inclusion Strategy was "being developed"; we could not find one in the council''s list of published strategies.</p><p>The gap between words and delivery is clearest in Dumfries and Galloway. Its new strategy names "Digital Inclusion: bridging the digital divide" as a core theme, but its delivery plan lists only internal actions: staff skills, AI guidance, automation and cyber security. Aberdeenshire''s strategy promised "no one is left behind" and ran out in 2025, with no successor published that we could find. In Orkney and the Western Isles, the plans we found are about cables and masts, not whether people can afford a connection or use it.</p><p>Some councils are doing real work. Falkirk offers free SIM cards with up to six months of data and lends devices. Perth and Kinross has a Local Digital Inclusion Fund. East Ayrshire and Renfrewshire back local networks of organisations working on digital inclusion. What almost none has is a plan of its own. The Scottish Government says its promised Digital Inclusion Delivery Plan will set out how it will work with local government. That will need to reach all 32.</p><p>Every finding is on our <a href="/accountability#councils-verified">accountability tracker</a>, with a link to the council''s own document. Eight councils we could not confirm: Aberdeen City, East Lothian, East Renfrewshire, Fife, Moray, Shetland, South Ayrshire and West Lothian. If you work for or live in one of them, or think we have a council wrong, <a href="/contact">tell us</a> and we will correct it.</p><p class="meta">Source: <a href="/accountability#councils-verified">WIRES accountability tracker</a> (each entry links to the council''s published document, checked October 2026) &middot; <a href="https://audit.scot/publications/tackling-digital-exclusion">Audit Scotland: Tackling digital exclusion (August 2024)</a></p>',
  '2026-10-06'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- 2026-10-06 — WIRES submission to the CMA's BT / TalkTalk inquiry (full text)
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES
(
  'What we told the competition regulator about BT and TalkTalk',
  'wires-cma-submission-bt-talktalk',
  'WIRES has sent its submission to the Competition and Markets Authority''s inquiry into BT''s purchase of TalkTalk, asking that any approval protects prices, guarantees access to a social tariff and recognises the greater effect in rural Scotland. The full text is here. Comments close on Friday 9 October.',
  '<p>On 6 October 2026, WIRES sent a submission to the Competition and Markets Authority''s inquiry into BT''s purchase of TalkTalk. The CMA is taking comments until Friday 9 October and must report to the Culture Secretary by 19 October. The CMA confirmed receipt the same day and said it will consider our comments as part of its investigation.</p><p>We said that keeping 2.5 million customers connected was better than letting TalkTalk fail, and asked that any clearance comes with conditions. Our five points:</p><ol><li><strong>Make the price promise binding.</strong> Ofcom has told TalkTalk customers "There will be no change to your price, contract or how you access billing", but adds that BT will give notice "if anything changes in future". We asked for that protection to be a condition of clearance, for a defined period.</li><li><strong>Guarantee access to a social tariff.</strong> TalkTalk does not offer one. BT does. TalkTalk customers on qualifying benefits should be told about it directly and able to move to it without penalty.</li><li><strong>Weigh the loss of a low-cost competitor</strong>, and the position of the smaller providers supplied through TalkTalk''s wholesale arm.</li><li><strong>Recognise that the effect is greater in rural Scotland.</strong> More than 30% of Scottish postcodes are in areas where a rival network is unlikely to be viable, against 18% across the UK.</li><li><strong>Protect vulnerable customers through any migration.</strong> Nobody who relies on a landline, telecare alarm or text relay should be moved without direct contact and support.</li></ol><details class="letter-preview"><summary>Read the submission we sent</summary><div class="letter-preview__body"><pre class="letter-preview__text">Dear CMA BT / TalkTalk inquiry team,

I am writing on behalf of WIRES (Web Infrastructure Rights for Everyone in Scotland), a volunteer-led campaign for affordable, reliable connectivity in Scotland. This representation covers both the competition question and the public interest ground concerning customers who are or may be vulnerable.

We accept that keeping 2.5 million customers connected was better than letting TalkTalk fail. Our concern is what the transaction does to prices and choice for low-income households afterwards. We ask the CMA to reflect the following points in its report and advice to the Secretary of State, and that any clearance is subject to conditions addressing them.

1. Protect existing TalkTalk customers on price
TalkTalk has competed largely on price. Its customers include many households for whom a few pounds a month decides whether they stay online. Ofcom&#039;s advice to TalkTalk customers on 5 October says: &quot;There will be no change to your price, contract or how you access billing.&quot; It goes on: &quot;If anything changes in future, BT will contact affected customers directly and provide clear notice and support.&quot; That is an assurance with no end date, and it leaves future changes open. We ask that the protection on price and contract terms is made a condition of any clearance, for a defined period. We also ask that customers are not moved to dearer BT Group tariffs without a clear choice, and that any customer who is moved can leave without exit fees.

2. Guarantee access to a social tariff
TalkTalk does not offer a social tariff: it is absent from Ofcom&#039;s published list. Its low-income customers can only get one by switching provider, which can mean paying to leave their contract. Ofcom&#039;s own research finds that fewer than one in ten eligible households take a social tariff, and around seven in ten are unaware they exist. BT offers one. TalkTalk customers on qualifying benefits should be told about it directly and able to move to it without penalty.

3. Weigh the loss of a low-cost competitor
BT Group already owns BT, EE and Plusnet, and Openreach, the network most other retailers depend on. We ask the CMA to examine the effect on the budget end of the market specifically, and on the smaller providers supplied through PlatformX, whose wholesale terms now sit with the group they compete against.

4. Recognise that the effect is greater in rural Scotland
In the areas Ofcom classes as &quot;Area 3&quot;, competing networks are unlikely to be viable, so the main competition consumers have is between retailers on Openreach&#039;s lines. Consumer Scotland&#039;s analysis is that more than 30% of Scottish postcodes fall into Area 3, against 18% across the UK. Removing an independent retailer matters most where there is no other network to turn to.

5. Protect vulnerable customers through any migration
This goes to the heart of the public interest ground on supply to vulnerable customers. No customer who relies on a landline, telecare alarm or text relay should be migrated without direct contact and support.

We would be glad to provide anything further that would help.

Yours faithfully,

William Ellis
WIRES — Web Infrastructure Rights for Everyone in Scotland
hello@wires.org.uk
https://wires.org.uk</pre><p class="meta">Sent by email to the CMA''s BT / TalkTalk inquiry team on 6 October 2026. This is the full text.</p></div></details><p>You can still have your say. Email <a href="mailto:bt.talktalk@cma.gov.uk">bt.talktalk@cma.gov.uk</a> by Friday 9 October. A few lines about your own experience as a customer is enough. Our <a href="/news-item?slug=bt-buys-talktalk-cma-comment-deadline">earlier article</a> explains the deal.</p><p class="meta">Source: <a href="https://www.gov.uk/cma-cases/bt-slash-talktalk-merger-inquiry">CMA: BT / TalkTalk merger inquiry</a> &middot; <a href="https://www.ofcom.org.uk/phones-and-broadband/telecoms-infrastructure/advice-for-talktalk-customers-following-acquisition-announcement">Ofcom: Advice for TalkTalk customers (5 October 2026)</a> &middot; <a href="https://www.ofcom.org.uk/phones-and-broadband/saving-money/social-tariffs">Ofcom: social tariffs list</a> &middot; <a href="https://consumer.scot/publications/ofcom-telecoms-access-review-2026-2031-html/">Consumer Scotland: response to the Telecoms Access Review</a></p>',
  '2026-10-06'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Scottish Broadband Voucher Scheme closing to new applications (8 Oct 2026)
INSERT INTO news_items (title, slug, summary, body, published_at) VALUES (
  'Scotland''s rural broadband voucher closes to new applications on 20 November',
  'scottish-broadband-voucher-scheme-closes-20-november',
  'The Scottish Broadband Voucher Scheme, worth up to £5,000 towards a faster connection, closes to new applications on 20 November 2026. In Aberdeenshire alone, 2,089 premises are still eligible and have not used it.',
  '<p>The Scottish Government is closing the Scottish Broadband Voucher Scheme. The official page now says: "The Scottish Broadband Voucher Scheme (SBVS) will close to new applications on 20 November 2026." That leaves six weeks.</p><p>The voucher pays up to £5,000 towards installing a faster connection, and covers all direct installation costs carried out by a registered supplier. It is for homes and businesses where the current connection is slower than 30 Mbps and there are no plans for a superfast connection through the R100 contracts or commercial build. These are the properties that R100 and commercial rollout are not going to reach.</p><h2>If you might be eligible, apply now</h2><p>The Government says requests submitted before the closing date "will continue to be processed against the SBVS eligibility criteria", and that "existing connections and approved applications are unaffected". So the date that matters is when your request goes in, not when the work is done.</p><p>Check your address on the Scottish Government''s broadband address checker, linked from the <a href="https://digitalconnectivity.campaign.gov.scot/sbvs">official scheme page</a>. If you are eligible, the next step is to contact one of the roughly 30 registered suppliers. Our <a href="/get-help#scottish-broadband-voucher">help page</a> has the details.</p><h2>In one council area, more than 2,000 eligible premises have not used it</h2><p>Aberdeenshire Council''s officer for digital connectivity told us this week that the area has had the highest take-up of the scheme in Scotland for some time. As of October 2026, residents there had used 1,189 vouchers. Another 2,089 premises in Aberdeenshire are still eligible and have not.</p><p>That is one council area, and by the council''s account the one where the scheme has worked best. We do not have figures for the rest of Scotland.</p><h2>What we do not know</h2><p>Neither official page gives a reason for the closure, or says what an eligible household should do after 20 November. For a property with no superfast connection planned, the voucher has been the main route to a subsidised one. We will report what replaces it, if anything does.</p><p class="meta">Sources: <a href="https://digitalconnectivity.campaign.gov.scot/sbvs">Scottish Government, Scottish Broadband Voucher Scheme</a> and <a href="https://www.mygov.scot/scottish-broadband-voucher-scheme">mygov.scot</a>, both checked 8 October 2026. Aberdeenshire figures from Aberdeenshire Council, in correspondence with WIRES, October 2026.</p>',
  '2026-10-08'
)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- ─── Local groups ────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS local_groups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(100) NOT NULL,
  council_area VARCHAR(120) NOT NULL COMMENT 'e.g. Dundee City, Highland, Glasgow City',
  council_code VARCHAR(20) DEFAULT NULL COMMENT 'Scottish council code e.g. S12000042 — matches GeoJSON on wifi-map.php',
  tagline VARCHAR(255) DEFAULT NULL COMMENT 'One-line description shown on the directory listing',
  description MEDIUMTEXT DEFAULT NULL COMMENT 'Longer description — trusted HTML, admin-authored only',
  contact_name VARCHAR(160) DEFAULT NULL,
  contact_email VARCHAR(255) DEFAULT NULL,
  social_url VARCHAR(500) DEFAULT NULL COMMENT 'Facebook group, Mastodon, etc.',
  status ENUM('active','forming','seeking_organiser') NOT NULL DEFAULT 'forming',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_local_groups_slug (slug),
  KEY idx_local_groups_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS group_events (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  group_id INT UNSIGNED NOT NULL,
  title VARCHAR(220) NOT NULL,
  description TEXT DEFAULT NULL,
  event_date DATE NOT NULL,
  event_time VARCHAR(50) DEFAULT NULL COMMENT 'Plain text e.g. "7:00pm" — flexible for partial times',
  location_text VARCHAR(300) DEFAULT NULL COMMENT 'e.g. Dundee Central Library, 1 The Waterfront',
  online_url VARCHAR(500) DEFAULT NULL COMMENT 'Video call link if virtual or hybrid',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_group_events_group (group_id),
  KEY idx_group_events_date (event_date),
  CONSTRAINT fk_group_events_group FOREIGN KEY (group_id) REFERENCES local_groups (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Existing installs: run these lines to add columns added after initial setup:
-- ALTER TABLE news_items ADD COLUMN group_id INT UNSIGNED DEFAULT NULL AFTER published_at;
-- ALTER TABLE news_items ADD KEY idx_news_group_id (group_id);
-- ALTER TABLE news_items ADD COLUMN image_filename VARCHAR(160) DEFAULT NULL AFTER group_id;
-- ALTER TABLE news_items ADD COLUMN image_alt VARCHAR(300) DEFAULT NULL AFTER image_filename;

-- ─── Help getting online: schemes & programmes ───────────────────────────────

CREATE TABLE IF NOT EXISTS schemes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(100) NOT NULL,
  name VARCHAR(255) NOT NULL,
  summary TEXT NOT NULL,
  who_for TEXT NOT NULL,
  what_you_get TEXT NOT NULL,
  how_to_apply TEXT NOT NULL,
  url VARCHAR(500) NOT NULL,
  source_label VARCHAR(200) NOT NULL,
  updated_month CHAR(7) NOT NULL DEFAULT '' COMMENT 'YYYY-MM — when we last verified this',
  status ENUM('active','check','ended') NOT NULL DEFAULT 'active',
  scope ENUM('uk','scotland') NOT NULL DEFAULT 'uk',
  note TEXT DEFAULT NULL,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_schemes_slug (slug),
  KEY idx_schemes_updated (updated_month),
  KEY idx_schemes_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schemes (slug, name, summary, who_for, what_you_get, how_to_apply, url, source_label, updated_month, status, scope, note, sort_order) VALUES
(
  'social-tariffs',
  'Social tariffs — cheaper broadband for people on benefits',
  'Major broadband providers offer significantly cheaper deals for people receiving Universal Credit and other qualifying benefits. Around 532,000 of the 6.2 million qualifying UK households use one — roughly 1 in 12. Around 7 in 10 people on benefits have never heard that social tariffs exist.',
  'People claiming Universal Credit, Pension Credit, Employment and Support Allowance, Jobseeker\'s Allowance, or Income Support. Each provider has its own qualifying list — check directly with them.',
  'Broadband packages from around £12.50–£20 per month with no mid-contract price rises and no exit fees. BT Home Essentials, Virgin Media Essential, Sky Broadband Basics, and Community Fibre all offer versions.',
  'Check which providers cover your address on the Ofcom page below, confirm your benefit, and contact the provider. You do not need to wait for your current contract to end.',
  'https://www.ofcom.org.uk/phones-and-broadband/saving-money/social-tariffs',
  'Ofcom: social tariffs guide',
  '2026-02', 'active', 'uk', '', 10
),
(
  'r100',
  'R100 — Reaching 100% broadband',
  'The Scottish Government\'s £697m programme to bring superfast broadband to every premises in Scotland, including rural and remote areas that commercial providers have not reached. Over 100,000 premises already connected.',
  'Households and businesses in areas without superfast broadband (30 Mbps+), particularly rural Scotland. Build is ongoing — your area may not be connected yet.',
  'Superfast broadband infrastructure delivered via Openreach fibre. Build completion expected 2028.',
  'Check the Scottish Government\'s broadband pages to see whether your area is included in the programme and what the expected delivery date is.',
  'https://www.gov.scot/policies/digital/broadband-roll-out/',
  'Scottish Government: R100 broadband roll-out',
  '2026-06', 'active', 'scotland', 'Some areas are being built now; others are scheduled for later. Check your postcode on the official page.', 20
),
(
  'uk-gigabit-voucher',
  'UK Gigabit Broadband Voucher Scheme',
  'Vouchers of up to £3,500 to help homes and businesses in rural areas pay for gigabit-capable broadband where commercial providers have not invested. Active Scottish contracts include the Borders, East Lothian, and North East Scotland.',
  'Homes and businesses in rural areas not scheduled for a commercial gigabit upgrade and currently receiving less than 1 Gbps. Groups of premises can combine vouchers for larger installations.',
  'Vouchers up to £3,500 for businesses and up to £1,500 for residential premises toward the cost of installation (combined group projects can be worth more). Verify current amounts on the official page — they change.',
  'Check eligibility and apply through Building Digital UK. A broadband supplier must be part of the application. Groups of neighbours can apply together.',
  'https://www.gov.uk/government/publications/gigabit-broadband-voucher-scheme-information/gigabit-broadband-voucher-scheme-information',
  'Building Digital UK: Gigabit Voucher Scheme',
  '2025-12', 'active', 'uk', 'Some Scottish areas are excluded where public-funded programmes (R100) are already delivering coverage.', 30
),
(
  'connecting-scotland',
  'Connecting Scotland',
  'A Scottish Government programme that provided devices and internet connections to digitally excluded people during and after the COVID-19 pandemic. The programme is currently being redesigned — check the official page for what is currently available.',
  'People on low incomes who lack devices or connectivity. The original programme targeted specific groups; the redesigned version may have different eligibility.',
  'Devices and subsidised or free internet connections. Exact offer depends on the current phase of the programme.',
  'Visit the Connecting Scotland website to check what is currently available and how to apply or be referred.',
  'https://connecting.scot/',
  'Connecting Scotland',
  '2025-01', 'check', 'scotland', 'New applications were paused as the Scottish Government redesigns the programme. Check the official page for current status.', 40
),
(
  'national-databank',
  'National Databank — free SIM cards with data',
  'The Good Things Foundation distributes free SIM cards with data, calls, and texts to people who cannot afford to get online. Cards are available through over 4,000 Digital Inclusion Hubs, including libraries, foodbanks, and charities.',
  'People experiencing data poverty or on a low income who cannot afford a mobile data plan. Available through local partner organisations — no direct application to the Foundation.',
  'Free SIM cards from Vodafone (40GB/month), O2 (25GB with rollover), and Three (24GB). No contract.',
  'Ask at your local library, foodbank, or community centre whether they are a National Databank partner. You can also search for partner organisations on the Good Things Foundation website.',
  'https://www.goodthingsfoundation.org/our-services/national-databank/',
  'Good Things Foundation: National Databank',
  '2026-07', 'check', 'uk', 'New partner applications were closed at the time we last checked, reopening later in the year — existing network partners still distribute SIMs regardless. Check the official page for current status.', 50
),
(
  'scottish-broadband-voucher',
  'Scottish Broadband Voucher Scheme — up to £5,000 to get connected (closes 20 November 2026)',
  'A Scottish Government subsidy of up to £5,000 that covers the direct installation cost of a faster connection for homes and businesses the R100 contracts and commercial rollouts aren''t going to reach. Around 30 registered suppliers offer full fibre, fixed wireless, 4G/5G or satellite, depending on where you live.',
  'Homes and businesses where both are true: your current connection is slower than 30 Mbps, and there are no plans for a superfast connection through the R100 contracts or commercial build.',
  'A subsidy of up to £5,000 per property, covering all direct installation costs carried out by a registered supplier. The type of connection depends on what suppliers can offer at your location.',
  'Check your address on the Scottish Government''s broadband address checker, linked from the official page. If you''re eligible, the official guidance explains how to engage a registered supplier, and what to do if a supplier contacts you first.',
  'https://digitalconnectivity.campaign.gov.scot/sbvs',
  'Scottish Government: Scottish Broadband Voucher Scheme',
  '2026-10', 'active', 'scotland', 'Closes to new applications on 20 November 2026. Requests submitted before that date will still be processed, so apply now if you are eligible. Open for individual properties. Group applications for several properties with a gigabit-capable connection have been paused since 19 August 2024 while Project Gigabit contracts roll out.', 25
)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ─── Organisational supporters ───────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS org_supporters (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  org_name VARCHAR(220) NOT NULL,
  org_type VARCHAR(100) DEFAULT NULL COMMENT 'e.g. Housing association, Trade union, Charity',
  org_url VARCHAR(500) DEFAULT NULL,
  location VARCHAR(120) DEFAULT NULL COMMENT 'Town, council area, or region',
  contact_name VARCHAR(160) NOT NULL,
  contact_email VARCHAR(255) NOT NULL,
  why_joining TEXT DEFAULT NULL COMMENT 'Optional statement from the organisation',
  consent_public TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Consent to appear in public directory',
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_org_status (status),
  KEY idx_org_name (org_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Council councillor contacts (outreach tracking) ─────────────────────────

CREATE TABLE IF NOT EXISTS council_contacts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  council_area VARCHAR(120) NOT NULL COMMENT 'Matches Scottish council-area GeoJSON used on wifi-map.php',
  directory_url VARCHAR(500) DEFAULT NULL COMMENT 'Official page listing all councillors for this council',
  contact_method VARCHAR(300) DEFAULT NULL COMMENT 'Email pattern, bulk mailbox, or contact route',
  councillor_count VARCHAR(20) DEFAULT NULL COMMENT 'Approximate seat count, e.g. "~45" or "32"',
  status ENUM('confirmed','check','blocked') NOT NULL DEFAULT 'check' COMMENT 'confirmed = working email pattern verified; check = directory found, contact route unverified; blocked = no direct email found',
  notes TEXT DEFAULT NULL,
  outreach_sent_at DATE DEFAULT NULL COMMENT 'Date the accountability letter was sent to this council',
  outreach_replied_at DATE DEFAULT NULL COMMENT 'Date a reply was received, if any',
  reply_summary TEXT DEFAULT NULL COMMENT 'Public-facing one/two sentence summary of what the council said — shown on /council-replies',
  ceo_name VARCHAR(150) DEFAULT NULL COMMENT 'Chief Executive this council''s accountability letter was addressed to',
  ceo_email VARCHAR(255) DEFAULT NULL COMMENT 'Address the accountability letter was actually sent to',
  subject VARCHAR(255) DEFAULT NULL COMMENT 'Exact rendered subject line sent — content changes over time, this is what that council actually received',
  body_text MEDIUMTEXT DEFAULT NULL COMMENT 'Exact rendered plain-text body sent',
  leader_name VARCHAR(150) DEFAULT NULL COMMENT 'Council Leader (political head) this council''s accountability letter was addressed to — distinct from the Chief Executive above',
  leader_email VARCHAR(255) DEFAULT NULL COMMENT 'Address the Leader accountability letter was actually sent to',
  leader_sent_at DATE DEFAULT NULL COMMENT 'Date the accountability letter was sent to the Leader — tracked separately from outreach_sent_at (the CEO send)',
  leader_subject VARCHAR(255) DEFAULT NULL,
  leader_body_text MEDIUMTEXT DEFAULT NULL,
  leader_replied_at DATE DEFAULT NULL COMMENT 'Date a reply was received from the Leader, if any — symmetric with outreach_replied_at (the CEO reply)',
  leader_reply_summary TEXT DEFAULT NULL COMMENT 'Public-facing one/two sentence summary of what the Leader said — shown on /council-replies',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_council_contacts_area (council_area),
  KEY idx_council_contacts_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Existing installs that already ran the council_contacts CREATE TABLE above before
-- these columns were added: run these lines to add them.
-- ALTER TABLE council_contacts ADD COLUMN outreach_sent_at DATE DEFAULT NULL AFTER notes;
-- ALTER TABLE council_contacts ADD COLUMN outreach_replied_at DATE DEFAULT NULL AFTER outreach_sent_at;
-- ALTER TABLE council_contacts ADD COLUMN reply_summary TEXT DEFAULT NULL AFTER outreach_replied_at;
-- ALTER TABLE council_contacts ADD COLUMN ceo_name VARCHAR(150) DEFAULT NULL AFTER reply_summary;
-- ALTER TABLE council_contacts ADD COLUMN ceo_email VARCHAR(255) DEFAULT NULL AFTER ceo_name;
-- ALTER TABLE council_contacts ADD COLUMN subject VARCHAR(255) DEFAULT NULL AFTER ceo_email;
-- ALTER TABLE council_contacts ADD COLUMN body_text MEDIUMTEXT DEFAULT NULL AFTER subject;
-- ALTER TABLE council_contacts ADD COLUMN leader_name VARCHAR(150) DEFAULT NULL AFTER body_text;
-- ALTER TABLE council_contacts ADD COLUMN leader_email VARCHAR(255) DEFAULT NULL AFTER leader_name;
-- ALTER TABLE council_contacts ADD COLUMN leader_sent_at DATE DEFAULT NULL AFTER leader_email;
-- ALTER TABLE council_contacts ADD COLUMN leader_subject VARCHAR(255) DEFAULT NULL AFTER leader_sent_at;
-- ALTER TABLE council_contacts ADD COLUMN leader_body_text MEDIUMTEXT DEFAULT NULL AFTER leader_subject;
-- ALTER TABLE council_contacts ADD COLUMN leader_replied_at DATE DEFAULT NULL AFTER leader_body_text;
-- ALTER TABLE council_contacts ADD COLUMN leader_reply_summary TEXT DEFAULT NULL AFTER leader_replied_at;

INSERT INTO council_contacts (council_area, directory_url, contact_method, councillor_count, status, notes) VALUES
('Angus', 'https://www.angus.gov.uk/councillors', 'CllrSurname@angus.gov.uk', '~28', 'confirmed', 'Confirmed working pattern (verified on Cllr Chris Beattie''s profile page).'),
('Argyll and Bute', 'https://www.argyll-bute.gov.uk/my-council/councillors-directory', 'firstname.lastname@argyll-bute.gov.uk', '~36', 'confirmed', 'Confirmed working pattern (verified on John Armour''s page). Directory paginated, 3 pages.'),
('Clackmannanshire', 'https://www.clacks.gov.uk/council/wards/', '[initial][surname]@clacks.gov.uk', '18', 'confirmed', 'Confirmed working pattern, e.g. cholden@clacks.gov.uk.'),
('Dundee City', 'https://www.dundeecity.gov.uk/service-area/councillors', 'firstname.lastname@dundeecity.gov.uk', '~29', 'confirmed', 'Confirmed working pattern, e.g. nadia.el-nakla@dundeecity.gov.uk.'),
('East Lothian', 'https://www.eastlothian.gov.uk/councillors/name', '[initial][surname]@eastlothian.gov.uk', '~22', 'confirmed', 'Confirmed working pattern, e.g. cyorkston@eastlothian.gov.uk.'),
('Eilean Siar', 'https://www.cne-siar.gov.uk/council-and-committees/wards-and-councillors/councillors', 'firstname.lastname@cne-siar.gov.uk', '~29', 'confirmed', 'Direct emails shown on the roster page itself — cleanest case found.'),
('Orkney Islands', 'https://www.orkney.gov.uk/your-council/councillors-and-meetings/councillors/', 'firstname.lastname@orkney.gov.uk', '~21', 'confirmed', 'Direct emails (mailto links) shown on the roster page.'),
('Perth and Kinross', 'https://perth-and-kinross.cmis.uk.com/perth-and-kinross/Councillors.aspx', 'councillorenquiries@pkc.gov.uk (bulk mailbox)', '~40', 'confirmed', 'Genuine bulk enquiry mailbox found on the page — one send may reach all.'),
('Renfrewshire', 'https://www.renfrewshire.gov.uk/council-and-elections/councillors-and-council-boards/councillors', 'cllr.firstname.lastname@renfrewshire.gov.uk', '43', 'confirmed', 'Confirmed working pattern across all 43 profile cards.'),
('Scottish Borders', 'https://www.scotborders.gov.uk/councillors', 'firstname.lastname@scotborders.gov.uk', '~33', 'confirmed', 'Confirmed on the main gov.uk domain (ModernGov subdomain separately blocks automated checks).'),
('Shetland Islands', 'https://www.shetland.gov.uk/councillors', 'firstname.lastname@shetland.gov.uk', '23', 'confirmed', 'Confirmed working pattern across the full list.'),
('Stirling', 'https://www.stirling.gov.uk/council-and-committees/councillors/your-councillors/', '[initial][lastname]@stirling.gov.uk', '23', 'confirmed', 'Confirmed via browser user-agent request, e.g. macphersona@stirling.gov.uk.'),
('Aberdeen City', 'https://committees.aberdeencity.gov.uk/mgMemberIndex.aspx', 'ModernGov roster — email format unconfirmed', '~45', 'check', 'Site blocks automated checks (bot protection); confirmed live via search index, open in an ordinary browser.'),
('Aberdeenshire', 'https://aberdeenshire.moderngov.co.uk/mgMemberIndex.aspx?bcr=1', 'ModernGov roster — email format unconfirmed', '~69', 'check', 'Site blocks automated checks; confirmed live via search index ("Your Councillors - Aberdeenshire Council").'),
('City of Edinburgh', 'https://www.edinburgh.gov.uk/councillors-committees', 'Hub page only — roster page not yet located', '~63', 'check', 'Only the navigation hub was reached in research — click through to the actual roster/search tool and confirm.'),
('Dumfries and Galloway', 'https://dumfriesgalloway.moderngov.co.uk/mgMemberIndex.aspx?bcr=1', 'ModernGov roster — email format unconfirmed', '~43', 'check', 'Site blocks automated checks; confirmed live via search index. Older dumgal.gov.uk article URL is now dead — do not use.'),
('East Dunbartonshire', 'https://eastdunbarton.moderngov.co.uk/mgMemberIndex.aspx', 'ModernGov roster — email format unconfirmed', '~22', 'check', 'Site blocks automated checks; only a shared customerservices@eastdunbarton.gov.uk address found otherwise.'),
('East Renfrewshire', 'https://www.eastrenfrewshire.gov.uk/article/5573/Full-Council', 'Unconfirmed — site blocks automated checks', '~18', 'check', 'Cloudflare-protected; confirm manually whether individual emails are shown.'),
('Glasgow City', 'https://www.glasgow.gov.uk/article/1687/Councillors-Listed-by-A-Z', 'Unconfirmed — site blocks automated checks', '~85', 'check', 'Cloudflare-protected; check the linked Councillor Information System for per-member contact details.'),
('Midlothian', 'https://midlothian.cmis.uk.com/live/councillors.aspx', 'Click-through needed — no email on list page', '18', 'check', 'Roster loads fine; open an individual profile to confirm email format.'),
('Moray', 'https://moray.cmis.uk.com/moray/CouncilandGovernance/Councillors.aspx', 'Click-through needed — emails reportedly on CMIS profiles', '26', 'check', 'A Moray FOI response confirms councillor emails are published on this portal; exact format not yet verified.'),
('North Ayrshire', 'https://www.north-ayrshire.gov.uk/council-voting-elections/councillors', 'Unconfirmed — JS app, no server-rendered content', '33', 'check', 'Page returns 200 OK but is a client-rendered app; open directly in a browser to check.'),
('North Lanarkshire', 'https://www.northlanarkshire.gov.uk/your-council/councillors-and-committees/official-council-roles/councillors', 'membersservices@northlan.gov.uk (bulk mailbox, reach unconfirmed)', '77', 'check', 'No individual emails found; confirm the bulk mailbox actually reaches all councillors before relying on it.'),
('West Dunbartonshire', 'https://www.west-dunbarton.gov.uk/council/councillors-and-committees/councillor/councillors-by-ward/', 'Click-through needed — no email on listing page', '22', 'check', 'Full list of 22 across 6 wards confirmed; click into each profile for an email.'),
('West Lothian', 'https://coins.westlothian.gov.uk/coins/allMembers.asp?sort=0', 'Click-through needed; general customer.services@westlothian.gov.uk', '~33', 'check', 'Paginated CoINS member list confirmed working; per-profile email presence not yet confirmed.'),
('East Ayrshire', 'https://www.east-ayrshire.gov.uk/CouncilAndGovernment/About-the-Council/Councillors-and-Provost/Councillors.aspx', 'admin@east-ayrshire.gov.uk (shared inbox only)', '~32', 'blocked', 'No individual councillor emails found anywhere on the site.'),
('Falkirk', 'https://council.falkirk.gov.uk/councillors', 'No email shown — likely a web form', '32', 'blocked', 'Roster confirmed; profile links have no visible email address.'),
('Fife', 'https://www.fife.gov.uk/kb/docs/articles/about-your-council2/politicians-and-committees/your-local-councillors/councillor/councillors', 'Phone only: 03451 55 55 55 ext 442320', '75', 'blocked', 'Full A-Z roster confirmed (Adams-Young); no emails listed on any page checked.'),
('Highland', 'https://highland.gov.uk/councillors', 'No email shown — likely a web form', '~74', 'blocked', 'Names, ward and party only — no emails or bulk contact found.'),
('Inverclyde', 'https://www2.inverclyde.gov.uk/memberscontact/', 'No email shown — likely a web form', '22', 'blocked', 'Ward-lookup dropdown of all 22 names, no direct emails shown.'),
('South Ayrshire', 'https://www.south-ayrshire.gov.uk/article/24777/Find-my-councillor', 'Unconfirmed — site fully blocks automated access', '28', 'blocked', 'Cloudflare bot challenge blocks all automated checks; needs a full manual browser visit.'),
('South Lanarkshire', 'https://www.southlanarkshire.gov.uk/councillors', 'No email shown — postcode/name search only', '64', 'blocked', 'Sampled profile page showed no email address; CMIS subdomain may hold fuller records, not yet checked.')
ON DUPLICATE KEY UPDATE directory_url = VALUES(directory_url);

-- ─── Individual councillor mail-merge campaigns (bin/send-councillor-campaign.php) ──
-- Tracks per-person sends so a re-run of the script is safe (already-sent rows are
-- skipped; only 'failed' rows are retried). One row per (campaign_slug, email).
-- Distinct from council_contacts above, which tracks one accountability-letter send
-- per council area, not per individual councillor.

CREATE TABLE IF NOT EXISTS councillor_campaign_sends (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  campaign_slug VARCHAR(100) NOT NULL COMMENT 'Identifies which mail-merge send this belongs to, e.g. councillor-public-statement-2026-09',
  full_name VARCHAR(160) NOT NULL,
  council_area VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL,
  status ENUM('sent','failed') NOT NULL DEFAULT 'sent' COMMENT 'sent = accepted by Resend for delivery, not a confirmed open/click',
  resend_id VARCHAR(100) DEFAULT NULL COMMENT 'Email id returned by Resend, for tracing a specific delivery',
  error_message TEXT DEFAULT NULL,
  subject VARCHAR(255) DEFAULT NULL COMMENT 'Exact rendered subject line this person was sent — template wording changes over time, this is what they actually got',
  body_text MEDIUMTEXT DEFAULT NULL COMMENT 'Exact rendered plain-text body this person was sent',
  sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  replied_at DATE DEFAULT NULL COMMENT 'Auto-set by bin/check-campaign-replies.php when a reply is detected, or manually via /admin',
  reply_is_auto TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = only an out-of-office / automatic acknowledgement so far — shown publicly as "Acknowledged", not "Replied", and still watched for a real reply',
  reply_notes TEXT DEFAULT NULL COMMENT 'What the councillor said — auto-filled with a body snippet on detection, or manually via /admin. Not shown publicly',
  public_quote TEXT DEFAULT NULL COMMENT 'A short line curated FROM reply_notes for public display on /councillor-statements — deliberately separate from reply_notes so a raw auto-captured email snippet is never shown publicly without a human choosing to feature it',
  PRIMARY KEY (id),
  UNIQUE KEY uq_councillor_campaign_sends (campaign_slug, email),
  KEY idx_councillor_campaign_sends_council (council_area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Existing installs: this table was originally created without the columns below —
-- run these lines to add them if you already had councillor_campaign_sends.
-- ALTER TABLE councillor_campaign_sends ADD COLUMN replied_at DATE DEFAULT NULL AFTER sent_at;
-- ALTER TABLE councillor_campaign_sends ADD COLUMN reply_notes TEXT DEFAULT NULL AFTER replied_at;
-- ALTER TABLE councillor_campaign_sends ADD COLUMN subject VARCHAR(255) DEFAULT NULL AFTER error_message;
-- ALTER TABLE councillor_campaign_sends ADD COLUMN body_text MEDIUMTEXT DEFAULT NULL AFTER subject;
-- ALTER TABLE councillor_campaign_sends ADD COLUMN public_quote TEXT DEFAULT NULL AFTER reply_notes;

-- ─── MSP accountability campaign (bin/send-msp-campaign.php) ────────────────────────
-- Tracks per-person sends, same shape and purpose as councillor_campaign_sends above,
-- for the 129 MSPs at Holyrood — a separate national/devolved-legislature audience
-- from the council-level campaigns.

CREATE TABLE IF NOT EXISTS msp_campaign_sends (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  campaign_slug VARCHAR(100) NOT NULL COMMENT 'Identifies which mail-merge send this belongs to, e.g. msp-accountability-2026-09',
  full_name VARCHAR(160) NOT NULL,
  party VARCHAR(120) DEFAULT NULL,
  role VARCHAR(160) DEFAULT NULL COMMENT 'e.g. "MSP for Paisley (Constituency)" or "MSP for North East Scotland (Region)"',
  email VARCHAR(255) NOT NULL,
  status ENUM('sent','failed') NOT NULL DEFAULT 'sent' COMMENT 'sent = accepted by Resend for delivery, not a confirmed open/click',
  resend_id VARCHAR(100) DEFAULT NULL COMMENT 'Email id returned by Resend, for tracing a specific delivery',
  error_message TEXT DEFAULT NULL,
  subject VARCHAR(255) DEFAULT NULL COMMENT 'Exact rendered subject line this person was sent',
  body_text MEDIUMTEXT DEFAULT NULL COMMENT 'Exact rendered plain-text body this person was sent',
  sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  replied_at DATE DEFAULT NULL COMMENT 'Auto-set by bin/check-campaign-replies.php when a reply is detected, or manually via /admin',
  reply_is_auto TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = only an out-of-office / automatic acknowledgement so far — shown publicly as "Acknowledged", not "Replied", and still watched for a real reply',
  reply_notes TEXT DEFAULT NULL COMMENT 'What the MSP said — auto-filled with a body snippet on detection, or manually via /admin. Not shown publicly',
  public_quote TEXT DEFAULT NULL COMMENT 'A short line curated FROM reply_notes for public display — deliberately separate from reply_notes so a raw auto-captured email snippet is never shown publicly without a human choosing to feature it',
  PRIMARY KEY (id),
  UNIQUE KEY uq_msp_campaign_sends (campaign_slug, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── MP accountability campaign (bin/send-mp-campaign.php) ──────────────────────────
-- Tracks per-person sends, same shape as msp_campaign_sends above — for Scotland's 57
-- Westminster MPs, a separate reserved-matters audience from the MSPs at Holyrood.

CREATE TABLE IF NOT EXISTS mp_campaign_sends (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  campaign_slug VARCHAR(100) NOT NULL COMMENT 'Identifies which mail-merge send this belongs to, e.g. mp-accountability-2026-09',
  full_name VARCHAR(160) NOT NULL,
  party VARCHAR(120) DEFAULT NULL,
  constituency VARCHAR(160) DEFAULT NULL,
  email VARCHAR(255) NOT NULL,
  status ENUM('sent','failed') NOT NULL DEFAULT 'sent' COMMENT 'sent = accepted by Resend for delivery, not a confirmed open/click',
  resend_id VARCHAR(100) DEFAULT NULL COMMENT 'Email id returned by Resend, for tracing a specific delivery',
  error_message TEXT DEFAULT NULL,
  subject VARCHAR(255) DEFAULT NULL COMMENT 'Exact rendered subject line this person was sent',
  body_text MEDIUMTEXT DEFAULT NULL COMMENT 'Exact rendered plain-text body this person was sent',
  sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  replied_at DATE DEFAULT NULL COMMENT 'Auto-set by bin/check-campaign-replies.php when a reply is detected, or manually via /admin',
  reply_is_auto TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = only an out-of-office / automatic acknowledgement so far — shown publicly as "Acknowledged", not "Replied", and still watched for a real reply',
  reply_notes TEXT DEFAULT NULL COMMENT 'What the MP said — auto-filled with a body snippet on detection, or manually via /admin. Not shown publicly',
  public_quote TEXT DEFAULT NULL COMMENT 'A short line curated FROM reply_notes for public display — deliberately separate from reply_notes so a raw auto-captured email snippet is never shown publicly without a human choosing to feature it',
  PRIMARY KEY (id),
  UNIQUE KEY uq_mp_campaign_sends (campaign_slug, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- People who have asked not to be contacted again, checked before every campaign send
-- (bin/send-councillor-campaign.php, bin/send-council-ceo-campaign.php) — independent of
-- any single campaign_slug, so an opt-out is honoured across every future campaign, not
-- just retries of the one they replied to.
CREATE TABLE IF NOT EXISTS do_not_contact (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  reason TEXT DEFAULT NULL COMMENT 'Context on why, e.g. a quoted opt-out line from a reply',
  added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_do_not_contact_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── One-off stakeholder notification (bin/send-stakeholder-notifications.php) ───────
-- A courtesy heads-up to sympathetic sector organisations about the accountability
-- campaign — not an ongoing mail-merge campaign like the tables above, just a small,
-- one-time send with the same audit-trail discipline.

CREATE TABLE IF NOT EXISTS stakeholder_notifications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  organisation VARCHAR(160) NOT NULL,
  contact_type VARCHAR(120) DEFAULT NULL COMMENT 'e.g. "media enquiries", "general info" — what kind of inbox this is',
  email VARCHAR(255) NOT NULL,
  status ENUM('sent','failed') NOT NULL DEFAULT 'sent',
  resend_id VARCHAR(100) DEFAULT NULL,
  error_message TEXT DEFAULT NULL,
  subject VARCHAR(255) DEFAULT NULL,
  body_text MEDIUMTEXT DEFAULT NULL,
  sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  replied_at DATE DEFAULT NULL,
  reply_is_auto TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = only an out-of-office / automatic acknowledgement so far — shown publicly as "Acknowledged", not "Replied", and still watched for a real reply',
  reply_notes TEXT DEFAULT NULL COMMENT 'Not shown publicly',
  PRIMARY KEY (id),
  UNIQUE KEY uq_stakeholder_notifications_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Existing installs (Oct 2026): sent_at previously had ON UPDATE CURRENT_TIMESTAMP, so
-- logging a reply overwrote the real send time. Drop that, and add reply_is_auto so
-- out-of-office/acknowledgement emails aren't counted as replies. Run these four lines:
-- ALTER TABLE councillor_campaign_sends MODIFY sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ADD COLUMN reply_is_auto TINYINT(1) NOT NULL DEFAULT 0 AFTER replied_at;
-- ALTER TABLE msp_campaign_sends MODIFY sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ADD COLUMN reply_is_auto TINYINT(1) NOT NULL DEFAULT 0 AFTER replied_at;
-- ALTER TABLE mp_campaign_sends MODIFY sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ADD COLUMN reply_is_auto TINYINT(1) NOT NULL DEFAULT 0 AFTER replied_at;
-- ALTER TABLE stakeholder_notifications MODIFY sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ADD COLUMN reply_is_auto TINYINT(1) NOT NULL DEFAULT 0 AFTER replied_at;

-- ─── Correspondence log (/admin/correspondence.php) ─────────────────────────────────
-- One row per meaningful email, in or out: who it was, what it said, what we did about
-- it, and whether anything is still outstanding. Automatic replies are never logged.
-- Rows are imported from the hello@wires.org.uk Inbox and Sent folders by
-- import_correspondence() (includes/correspondence.php) with status 'review', then
-- summarised by a person in /admin. Entries can also be added by hand (calls, meetings).

CREATE TABLE IF NOT EXISTS correspondence_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  occurred_on DATE NOT NULL,
  direction ENUM('in','out') NOT NULL COMMENT 'in = they wrote to us, out = we wrote to them',
  person VARCHAR(160) NOT NULL,
  organisation VARCHAR(200) DEFAULT NULL COMMENT 'Role or organisation, e.g. "Councillor, North Lanarkshire"',
  category ENUM('councillor','council','msp','mp','stakeholder','regulator','public','other') NOT NULL DEFAULT 'other',
  email VARCHAR(255) DEFAULT NULL COMMENT 'The other party''s address — also what groups entries into one conversation',
  subject VARCHAR(255) DEFAULT NULL,
  summary TEXT DEFAULT NULL COMMENT 'One or two lines, written by a person',
  body_text MEDIUMTEXT DEFAULT NULL COMMENT 'Full text of the email',
  action_taken TEXT DEFAULT NULL COMMENT 'What we did about it',
  status ENUM('review','open','done') NOT NULL DEFAULT 'review' COMMENT 'review = imported, not yet looked at; open = something still to do; done = nothing outstanding',
  message_id VARCHAR(255) DEFAULT NULL COMMENT 'Email Message-ID, so the importer never logs the same email twice',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_correspondence_message_id (message_id),
  KEY idx_correspondence_occurred (occurred_on),
  KEY idx_correspondence_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
