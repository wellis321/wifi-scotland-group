-- MySQL / MariaDB schema for The WIFI Scotland Group campaign site
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
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_news_items_slug (slug),
  KEY idx_news_published (published_at)
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
