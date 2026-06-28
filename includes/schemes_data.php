<?php

declare(strict_types=1);

/**
 * Active schemes and programmes helping people in Scotland get online or pay less.
 * Ordered by updated descending (most recently verified first).
 *
 * Fields:
 *   slug         – unique identifier
 *   name         – scheme name
 *   summary      – one or two sentences for listing view
 *   who          – who qualifies
 *   what         – what you actually get
 *   how          – how to find out / apply
 *   url          – official primary source
 *   source_label – link text for the url
 *   updated      – YYYY-MM when we last verified this information
 *   status       – 'active' | 'check' | 'ended'
 *   scope        – 'scotland' | 'uk'
 *   note         – any caveat to show (empty string = none)
 */
function load_schemes(): array
{
    return [
        [
            'slug'         => 'social-tariffs',
            'name'         => 'Social tariffs — cheaper broadband for people on benefits',
            'summary'      => 'Major broadband providers offer significantly cheaper deals for people receiving Universal Credit and other qualifying benefits. Around 532,000 of the 6.2 million qualifying UK households use one — roughly 1 in 12. More than half of people on benefits have never heard that social tariffs exist.',
            'who'          => 'People claiming Universal Credit, Pension Credit, Employment and Support Allowance, Jobseeker\'s Allowance, or Income Support. Each provider has its own qualifying list — check directly with them.',
            'what'         => 'Broadband packages from around £12.50–£20 per month with no mid-contract price rises and no exit fees. BT Home Essentials, Virgin Media Essential, Sky Broadband Basics, and Community Fibre all offer versions.',
            'how'          => 'Check which providers cover your address on the Ofcom page below, confirm your benefit, and contact the provider. You do not need to wait for your current contract to end.',
            'url'          => 'https://www.ofcom.org.uk/phones-and-broadband/saving-money/social-tariffs',
            'source_label' => 'Ofcom: social tariffs guide',
            'updated'      => '2026-02',
            'status'       => 'active',
            'scope'        => 'uk',
            'note'         => '',
        ],
        [
            'slug'         => 'r100',
            'name'         => 'R100 — Reaching 100% broadband',
            'summary'      => 'The Scottish Government\'s £697m programme to bring superfast broadband to every premises in Scotland, including rural and remote areas that commercial providers have not reached. Over 96,000 premises already connected.',
            'who'          => 'Households and businesses in areas without superfast broadband (30 Mbps+), particularly rural Scotland. Build is ongoing — your area may not be connected yet.',
            'what'         => 'Superfast broadband infrastructure delivered via Openreach fibre. Build completion expected 2028.',
            'how'          => 'Check the Scottish Government\'s broadband pages to see whether your area is included in the programme and what the expected delivery date is.',
            'url'          => 'https://www.gov.scot/policies/digital/broadband-roll-out/',
            'source_label' => 'Scottish Government: R100 broadband roll-out',
            'updated'      => '2026-01',
            'status'       => 'active',
            'scope'        => 'scotland',
            'note'         => 'Some areas are being built now; others are scheduled for later. Check your postcode on the official page.',
        ],
        [
            'slug'         => 'uk-gigabit-voucher',
            'name'         => 'UK Gigabit Broadband Voucher Scheme',
            'summary'      => 'Vouchers of up to £4,500 to help homes and businesses in rural areas pay for gigabit-capable broadband where commercial providers have not invested. Active Scottish contracts include the Borders, East Lothian, and North East Scotland.',
            'who'          => 'Homes and businesses in rural areas not scheduled for a commercial gigabit upgrade and currently receiving less than 1 Gbps. Groups of premises can combine vouchers for larger installations.',
            'what'         => 'Vouchers up to £4,500 for businesses and up to £1,500 for residential premises toward the cost of installation. Verify current amounts on the official page — they change.',
            'how'          => 'Check eligibility and apply through Building Digital UK. A broadband supplier must be part of the application. Groups of neighbours can apply together.',
            'url'          => 'https://www.gov.uk/government/publications/gigabit-broadband-voucher-scheme-information/gigabit-broadband-voucher-scheme-information',
            'source_label' => 'Building Digital UK: Gigabit Voucher Scheme',
            'updated'      => '2025-12',
            'status'       => 'active',
            'scope'        => 'uk',
            'note'         => 'Some Scottish areas are excluded where public-funded programmes (R100) are already delivering coverage.',
        ],
        [
            'slug'         => 'connecting-scotland',
            'name'         => 'Connecting Scotland',
            'summary'      => 'A Scottish Government programme that provided devices and internet connections to digitally excluded people during and after the COVID-19 pandemic. The programme is currently being redesigned — check the official page for what is currently available.',
            'who'          => 'People on low incomes who lack devices or connectivity. The original programme targeted specific groups; the redesigned version may have different eligibility.',
            'what'         => 'Devices and subsidised or free internet connections. Exact offer depends on the current phase of the programme.',
            'how'          => 'Visit the Connecting Scotland website to check what is currently available and how to apply or be referred.',
            'url'          => 'https://connecting.scot/',
            'source_label' => 'Connecting Scotland',
            'updated'      => '2025-01',
            'status'       => 'check',
            'scope'        => 'scotland',
            'note'         => 'New applications were paused as the Scottish Government redesigns the programme. Check the official page for current status.',
        ],
        [
            'slug'         => 'national-databank',
            'name'         => 'National Databank — free SIM cards with data',
            'summary'      => 'The Good Things Foundation distributes free SIM cards with data, calls, and texts to people who cannot afford to get online. Cards are available through over 1,600 community organisations including libraries, foodbanks, and charities.',
            'who'          => 'People experiencing data poverty or on a low income who cannot afford a mobile data plan. Available through local partner organisations — no direct application to the Foundation.',
            'what'         => 'Free SIM cards from Vodafone (40GB/month), O2 (25GB + rollover), and Three (24GB). No contract.',
            'how'          => 'Ask at your local library, foodbank, or community centre whether they are a National Databank partner. You can also search for partner organisations on the Good Things Foundation website.',
            'url'          => 'https://www.goodthingsfoundation.org/our-services/national-databank/',
            'source_label' => 'Good Things Foundation: National Databank',
            'updated'      => '2024-12',
            'status'       => 'check',
            'scope'        => 'uk',
            'note'         => 'New partner organisations were not being accepting at the time we last checked. Existing network partners still distribute SIMs.',
        ],
        [
            'slug'         => 'talktalk-dwp',
            'name'         => 'TalkTalk free broadband — for DWP benefit claimants',
            'summary'      => 'TalkTalk in partnership with DWP offers free superfast broadband for six months to eligible customers receiving DWP benefits. After six months it moves to a paid plan — check current terms before signing up.',
            'who'          => 'People currently receiving DWP benefits such as Universal Credit. Eligibility is verified directly through DWP, so you do not need to provide paper proof of your benefit.',
            'what'         => 'Free 39 Mbps superfast broadband with no usage cap for six months. After the free period ends it converts to a standard TalkTalk plan — make sure you know what the ongoing cost will be before signing up.',
            'how'          => 'Check the official DWP guidance page for the current link and sign-up process. DWP can verify your eligibility automatically with TalkTalk, removing the need to provide documents.',
            'url'          => 'https://www.gov.uk/government/news/low-cost-broadband-and-mobile-phone-tariffs',
            'source_label' => 'GOV.UK: low-cost broadband and mobile tariffs for benefit claimants',
            'updated'      => '2025-01',
            'status'       => 'check',
            'scope'        => 'uk',
            'note'         => 'Offers like this can change — verify current terms on the official GOV.UK page before applying. The six-month free period converts to a paid plan, so check the ongoing cost.',
        ],
        [
            'slug'         => 'jobcentre-plus-computers',
            'name'         => 'Jobcentre Plus — free computers and internet access',
            'summary'      => 'Every Jobcentre Plus branch in Scotland has free computers, Wi-Fi, and in-person digital support. Open to anyone for job searching, Universal Credit management, and general internet access — no appointment needed for device use.',
            'who'          => 'Anyone who needs free internet access, not only benefit claimants. Particularly useful for people without a home connection, in temporary accommodation, or who need help with online forms.',
            'what'         => 'Free use of computers and Wi-Fi. Digital mentors available Monday to Friday, 10am–3pm, to help with job searching, CV writing, Universal Credit accounts, and basic digital skills.',
            'how'          => 'Walk into your nearest Jobcentre Plus during opening hours. No appointment needed to use the computers. Find your nearest branch on GOV.UK.',
            'url'          => 'https://www.gov.uk/contact-jobcentre-plus',
            'source_label' => 'GOV.UK: find your nearest Jobcentre Plus',
            'updated'      => '2025-01',
            'status'       => 'active',
            'scope'        => 'uk',
            'note'         => '',
        ],
    ];
}
