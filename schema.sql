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

CREATE TABLE IF NOT EXISTS news_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(220) NOT NULL,
  slug VARCHAR(220) NOT NULL,
  summary VARCHAR(500) DEFAULT NULL,
  body MEDIUMTEXT NOT NULL,
  published_at DATE NOT NULL,
  group_id INT UNSIGNED DEFAULT NULL COMMENT 'Optional: link to local_groups.id to tag as local news',
  image_filename VARCHAR(160) DEFAULT NULL COMMENT 'Filename from /images/ — e.g. card-fibre.jpg. NULL = default banner.',
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
  'The Good Things Foundation distributes free SIM cards with data, calls, and texts to people who cannot afford to get online. Cards are available through over 1,600 community organisations including libraries, foodbanks, and charities.',
  'People experiencing data poverty or on a low income who cannot afford a mobile data plan. Available through local partner organisations — no direct application to the Foundation.',
  'Free SIM cards from Vodafone (40GB/month), O2 (25GB + rollover), and Three (24GB). No contract.',
  'Ask at your local library, foodbank, or community centre whether they are a National Databank partner. You can also search for partner organisations on the Good Things Foundation website.',
  'https://www.goodthingsfoundation.org/our-services/national-databank/',
  'Good Things Foundation: National Databank',
  '2024-12', 'check', 'uk', 'New partner organisations were not being accepted at the time we last checked. Existing network partners still distribute SIMs.', 50
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
