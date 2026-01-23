<?php 
    session_start();

    require_once '../../admin/assets/includes/config/config.php';
    require_once '../../admin/assets/includes/functions/data_fetcher.php';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Terms & Conditions | <?= get_site_option('site_name') ?> - <?= get_site_option('site_tagline') ?></title>

	<!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/site.webmanifest">
    <meta name="theme-color" content="#ffffff">

	<!-- Core Styles & Fonts (same stack as gallery page) -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="../../assets/css/global.css">
	<link rel="stylesheet" href="../../assets/css/style_home.css">
	<link rel="stylesheet" href="../../assets/css/gallery.css">
	<link rel="stylesheet" href="../../assets/css/terms.css">
</head>
<body class="terms-page">
	<div class="main-wrapper">
		<!-- Navigation -->
		<?php require_once '../../admin/assets/elements/user-side/navbar.php'; ?>

		<!-- Breadcrumb Section -->
        <section class="breadcrumb-section">
            <div class="container-lg">
                <nav class="breadcrumb-nav" aria-label="breadcrumb">
                    <ol class="breadcrumb-list">
                        <li class="breadcrumb-item">
                            <a href="<?= get_site_option('site_url') ?>" class="breadcrumb-link">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="breadcrumb-separator">
                            <i class="fas fa-chevron-right"></i>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);" class="breadcrumb-link">
                                <span>Policies</span>
                            </a>
                        </li>
					</ol>
					<div class="gallery-hero-content">
                        <h1 class="gallery-hero-title">Terms & Conditions</h1>
                        <p class="gallery-hero-subtitle">Please read these terms and conditions carefully before participating in any Rang Exhibition event.</p>
                    </div>
                </nav>
            </div>
        </section>

		<div class="terms-wrapper">
			<div class="terms-hero">
				<div class="terms-meta">
					<span><i class="far fa-calendar-check"></i> Last updated: January 13, 2026</span>
					<span><i class="fas fa-shield-alt"></i> Applies to all Rang Exhibitions</span>
				</div>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-circle-info"></i> 1. Acceptance of Terms</h3>
				<p>By booking a stall, confirming participation, submitting an application, or attending any Rang Exhibition event, you agree to be bound by these Terms & Conditions, our Privacy Policy, and any event-specific guidelines communicated to you.</p>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-users"></i> 2. Eligibility & Participation</h3>
				<ul class="terms-list">
					<li>Exhibitors must provide accurate business details, permits, and GST information (where applicable).</li>
					<li>Attendees must comply with venue rules, safety instructions, and security screenings.</li>
					<li>We reserve the right to refuse or revoke participation for non-compliance or misconduct.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-file-signature"></i> 3. Bookings, Payments & Invoices</h3>
				<ul class="terms-list">
					<li>50% advance at the time of booking is necessary and the balance payment must be made at least one week prior to the exhibition. Exhibit booth space will not be processed nor will space assignment be made without full payment received by Rang exhibition organizers.</li>
					<li>Payment must be on UPI Id <strong>rangexhibition.india@oksbi</strong>. Please Send screenshot for payment confirmation on official WhatsApp Number <a href="https://api.whatsapp.com/send?phone=+917688995560">+91 76889 95560</a>.</li>
					<li>Late payments may lead to forfeiture of the stall allocation or reassignment without refund.</li>
					<li>Mentioned best prices help us to make this exhibition big success.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-undo"></i> 4. Cancellations & Refunds</h3>
				<ul class="terms-list">
					<li>Organizer-initiated cancellations: If Rang cancels or postpones an event, we will offer a reschedule.</li>
					<li>Exhibitor cancellations: In case of cancellation, no amount will be refunded. However, it can be transferred to another exhibitor.</li>
					<li>No-shows: Failure to occupy the stall within the allotted setup time may result in forfeiture without refund.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-clipboard-check"></i> 5. Exhibitor Responsibilities</h3>
				<ul class="terms-list">
					<li>An additional setup or requirement must be informed to the organising team at least 7 days prior, and the charges are to be paid on the spot.</li>
					<li>Pasting any posters inside or outside the stall is forbidden.</li>
					<li>Maintain booth decorum, avoid excessive noise, and keep aisles unobstructed.</li>
					<li>Use only approved electrical setups; any special power requirements must be pre-approved.</li>
					<li>Ensure products/services comply with all applicable laws (licenses, safety, labeling, and authenticity).</li>
					<li>Keep the stall area clean; damages to venue property will be chargeable.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-users-slash"></i> 6. Attendee Conduct</h3>
				<ul class="terms-list">
					<li>Harassment, abusive language, or disruptive behavior is not tolerated.</li>
					<li>Follow all safety announcements, emergency protocols, and staff directions.</li>
					<li>Unauthorized resale of tickets/passes is prohibited.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-camera"></i> 7. Photography, Media & Promotions</h3>
				<ul class="terms-list">
					<li>Events may be photographed or recorded; attendance implies consent to appear in marketing collateral.</li>
					<li>Exhibitors using in-stall photography/videography must avoid obstructing walkways and follow venue guidelines.</li>
					<li>Branding or promotional activities must be confined to the allocated stall unless pre-approved.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-lock"></i> 8. Intellectual Property & Branding</h3>
				<p>All trademarks, logos, event creatives, and brand assets of Rang remain our property and may not be reproduced without written consent. Exhibitors are responsible for ensuring that displayed products do not infringe third-party rights.</p>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-user-shield"></i> 9. Privacy & Data</h3>
				<p>Any personal data collected for registrations or bookings will be handled in line with our Privacy Policy. Exhibitors must handle attendee data responsibly and only for legitimate business purposes related to the event.</p>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-hands-helping"></i> 10. Liability & Insurance</h3>
				<ul class="terms-list">
					<li>Security during the night will be provided from our side, but we are not responsible for the stalls during daytime hours when the exhibition is ongoing.</li>
					<li>We are also not responsible for your belongings between the opening hours or while the visitors are still in the hall.</li>
					<li>The organisers will not be held responsible for any loss, theft, damage, or accidents due to fire, rain, mischief, etc.</li>
					<li>No insurance or indemnity coverage is provided by the organisers.</li>
					<li>We aren't responsible for any damages due to unforeseen circumstances.</li>
					<li>We don't take any guarantee of sales or footfall, however, We will take the best measures to ensure a successful event.</li>
					<li>Participation is at your own risk; please follow all safety and security instructions.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-exclamation-triangle"></i> 11. Force Majeure</h3>
				<p>The organizer reserves the right to alter the dates, layout, production arrangements, setup and the venue of the event in case of unforeseen conditions or in case of not getting enough bookings to cover the overall cost of the exhibition by informing 1 week prior notice.</p>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-gavel"></i> 12. Compliance & Governing Law</h3>
				<ul class="terms-list">
					<li>These terms and conditions are effective upon booking (token amount transfer in favor of Rang Exhibition) and shall expire immediately following the final day of the event/ item as described or until all responsibilities set out are fulfilled.</li>
					<li>All participants must comply with applicable laws, venue regulations, and municipal permissions. These terms are governed by the laws of India, and any disputes shall be subject to the jurisdiction of courts in Rajasthan.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-pen-to-square"></i> 13. Updates to These Terms</h3>
				<p>We may update these Terms & Conditions to reflect operational, legal, or regulatory changes. The “Last updated” date will indicate the latest version. Continued participation after updates constitutes acceptance of the revised terms.</p>
			</div>

			<div class="cta-card">
				<p class="cta-text"><strong>Questions?</strong> We're here to help. Reach out for clarifications or exhibition-specific guidelines.</p>
				<a href="mailto:rangexhibition.india@gmail.com" class="u-btn-primary">Contact Support</a>
			</div>

			<p class="footer-note">By booking a stall, you agree to comply with these rules. All rights are reserved with the organisers.</p>
		</div>

		<!-- Footer -->
        <?php require_once '../../admin/assets/elements/user-side/footer.php'; ?>
	</div>

	<script src="../../assets/js/nav.js"></script>

	<!-- WhatsApp balloon -->
	<?php require_once '../../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>