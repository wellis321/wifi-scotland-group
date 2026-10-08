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
            'summary'      => 'Major broadband providers offer significantly cheaper deals for people receiving Universal Credit and other qualifying benefits. Around 532,000 of the 6.2 million qualifying UK households use one — roughly 1 in 12. Around 7 in 10 people on benefits have never heard that social tariffs exist.',
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
            'summary'      => 'The Scottish Government\'s £697m programme to bring superfast broadband to every premises in Scotland, including rural and remote areas that commercial providers have not reached. Over 100,000 premises already connected.',
            'who'          => 'Households and businesses in areas without superfast broadband (30 Mbps+), particularly rural Scotland. Build is ongoing — your area may not be connected yet.',
            'what'         => 'Superfast broadband infrastructure delivered via Openreach fibre. Build completion expected 2028.',
            'how'          => 'Check the Scottish Government\'s broadband pages to see whether your area is included in the programme and what the expected delivery date is.',
            'url'          => 'https://digitalconnectivity.campaign.gov.scot/',
            'source_label' => 'Scottish Government: R100 broadband roll-out',
            'updated'      => '2026-01',
            'status'       => 'active',
            'scope'        => 'scotland',
            'note'         => 'Some areas are being built now; others are scheduled for later. Check your postcode on the official page.',
        ],
        [
            'slug'         => 'uk-gigabit-voucher',
            'name'         => 'UK Gigabit Broadband Voucher Scheme',
            'summary'      => 'Vouchers of up to £3,500 to help homes and businesses in rural areas pay for gigabit-capable broadband where commercial providers have not invested. Active Scottish contracts include the Borders, East Lothian, and North East Scotland.',
            'who'          => 'Homes and businesses in rural areas not scheduled for a commercial gigabit upgrade and currently receiving less than 1 Gbps. Groups of premises can combine vouchers for larger installations.',
            'what'         => 'Vouchers up to £3,500 for businesses and up to £1,500 for residential premises toward the cost of installation (combined group projects can be worth more). Verify current amounts on the official page — they change.',
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
            'summary'      => 'The Good Things Foundation distributes free SIM cards with data, calls, and texts to people who cannot afford to get online. Cards are available through over 4,000 Digital Inclusion Hubs, including libraries, foodbanks, and charities.',
            'who'          => 'People experiencing data poverty or on a low income who cannot afford a mobile data plan. Available through local partner organisations — no direct application to the Foundation.',
            'what'         => 'Free SIM cards from Vodafone (40GB/month), O2 (25GB with rollover), and Three (24GB). No contract.',
            'how'          => 'Ask at your local library, foodbank, or community centre whether they are a National Databank partner. You can also search for partner organisations on the Good Things Foundation website.',
            'url'          => 'https://www.goodthingsfoundation.org/our-services/national-databank/',
            'source_label' => 'Good Things Foundation: National Databank',
            'updated'      => '2026-07',
            'status'       => 'check',
            'scope'        => 'uk',
            'note'         => 'New partner applications were closed at the time we last checked, reopening later in the year — existing network partners still distribute SIMs regardless. Check the official page for current status.',
        ],
        [
            'slug'         => 'talktalk-dwp',
            'name'         => 'TalkTalk free broadband — for DWP benefit claimants',
            'summary'      => 'TalkTalk has previously offered free superfast broadband for six months to DWP benefit claimants, referred by a work coach. This scheme has a history of pausing and relaunching — confirm it is currently running before telling someone to rely on it.',
            'who'          => 'People currently receiving DWP benefits such as Universal Credit, referred via their DWP work coach rather than applying directly.',
            'what'         => 'When running: free ~39 Mbps superfast broadband with no usage cap for six months. After the free period ends it converts to a standard TalkTalk plan — make sure you know what the ongoing cost will be before signing up.',
            'how'          => 'Ask your DWP work coach whether this scheme is currently active and, if so, for the referral code — it is not a self-service sign-up. Check the official DWP guidance page first.',
            'url'          => 'https://www.gov.uk/government/news/low-cost-broadband-and-mobile-phone-tariffs',
            'source_label' => 'GOV.UK: low-cost broadband and mobile tariffs for benefit claimants',
            'updated'      => '2026-07',
            'status'       => 'check',
            'scope'        => 'uk',
            'note'         => 'We could not confirm this scheme is still running as of mid-2026 — some sources suggest it paused in 2025. Ask your work coach or check the official GOV.UK page before relying on it.',
        ],
        [
            'slug'         => 'jobcentre-plus-computers',
            'name'         => 'Jobcentre Plus — free computers and internet access',
            'summary'      => 'Every Jobcentre Plus branch in Scotland has free computers, Wi-Fi, and in-person digital support. Open to anyone for job searching, Universal Credit management, and general internet access — no appointment needed for device use.',
            'who'          => 'Anyone who needs free internet access, not only benefit claimants. Particularly useful for people without a home connection, in temporary accommodation, or who need help with online forms.',
            'what'         => 'Free use of computers and Wi-Fi, plus in-person help with job searching, CV writing, Universal Credit accounts, and basic digital skills. Availability of dedicated digital support varies by branch — ask at reception what is on offer and when.',
            'how'          => 'Walk into your nearest Jobcentre Plus during opening hours. No appointment needed to use the computers. Find your nearest branch and its opening hours on GOV.UK.',
            'url'          => 'https://www.gov.uk/contact-jobcentre-plus',
            'source_label' => 'GOV.UK: find your nearest Jobcentre Plus',
            'updated'      => '2026-07',
            'status'       => 'active',
            'scope'        => 'uk',
            'note'         => '',
        ],
        [
            'slug'         => 'scottish-broadband-voucher',
            'name'         => 'Scottish Broadband Voucher Scheme — up to £5,000 to get connected (closes 20 November 2026)',
            'summary'      => 'A Scottish Government subsidy of up to £5,000 that covers the direct installation cost of a faster connection for homes and businesses the R100 contracts and commercial rollouts aren\'t going to reach. Around 30 registered suppliers offer full fibre, fixed wireless, 4G/5G or satellite, depending on where you live.',
            'who'          => 'Homes and businesses where both are true: your current connection is slower than 30 Mbps, and there are no plans for a superfast connection through the R100 contracts or commercial build.',
            'what'         => 'A subsidy of up to £5,000 per property, covering all direct installation costs carried out by a registered supplier. The type of connection depends on what suppliers can offer at your location.',
            'how'          => 'Check your address on the Scottish Government\'s broadband address checker, linked from the official page. If you\'re eligible, the official guidance explains how to engage a registered supplier, and what to do if a supplier contacts you first.',
            'url'          => 'https://digitalconnectivity.campaign.gov.scot/sbvs',
            'source_label' => 'Scottish Government: Scottish Broadband Voucher Scheme',
            'updated'      => '2026-10',
            'status'       => 'active',
            'scope'        => 'scotland',
            'note'         => 'Closes to new applications on 20 November 2026. Requests submitted before that date will still be processed, so apply now if you are eligible. Open for individual properties. Group applications for several properties with a gigabit-capable connection have been paused since 19 August 2024 while Project Gigabit contracts roll out.',
        ],
    ];
}
