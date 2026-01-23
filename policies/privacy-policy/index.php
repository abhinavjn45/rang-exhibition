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
	<title>Privacy Policy | <?= get_site_option('site_name') ?> - <?= get_site_option('site_tagline') ?></title>

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
                        <h1 class="gallery-hero-title">Privacy Policy</h1>
                        <p class="gallery-hero-subtitle">We are committed to protecting your personal data and ensuring transparency about how we collect, use, and safeguard your information at Rang Exhibitions.</p>
                    </div>
                </nav>
            </div>
        </section>

		<div class="terms-wrapper">
			<div class="terms-hero">
				<div class="terms-meta">
					<span><i class="far fa-calendar-check"></i> Last updated: January 13, 2026</span>
					<span><i class="fas fa-shield-alt"></i> Applies to all Rang Exhibition Users</span>
				</div>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-circle-info"></i> 1. Information We Collect</h3>
				<p>We collect information you provide directly to us, such as when you book a stall, register for an event, or contact us. This includes:</p>
				<ul class="terms-list">
					<li><strong>Personal Information:</strong> Name, email address, phone number, business name, GST number, address, and payment details.</li>
					<li><strong>Event Information:</strong> Event preferences, stall requirements, dietary restrictions, and special accommodations.</li>
					<li><strong>Communication Data:</strong> Messages, inquiries, feedback, and support tickets.</li>
					<li><strong>Technical Data:</strong> IP address, browser type, device information, and pages visited (collected via cookies and analytics).</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-users"></i> 2. How We Use Your Information</h3>
				<p>We use the information we collect for the following purposes:</p>
				<ul class="terms-list">
					<li>To process bookings, registrations, and payment transactions securely.</li>
					<li>To manage event logistics, send confirmations, reminders, and updates.</li>
					<li>To communicate with you regarding event changes, cancellations, or rescheduling.</li>
					<li>To provide customer support and respond to inquiries.</li>
					<li>To send marketing communications and newsletters (with your consent).</li>
					<li>To improve our website, services, and user experience through analytics.</li>
					<li>To comply with legal obligations and prevent fraud.</li>
					<li>To conduct surveys and gather feedback to enhance our events.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-file-signature"></i> 3. Legal Basis for Processing</h3>
				<p>We process your personal data based on the following legal grounds:</p>
				<ul class="terms-list">
					<li><strong>Contract Performance:</strong> Processing necessary to fulfill our booking and event management services.</li>
					<li><strong>Legitimate Interests:</strong> Marketing, analytics, and improving our services for better user experience.</li>
					<li><strong>Legal Compliance:</strong> Meeting GST, payment, and regulatory requirements under Indian law.</li>
					<li><strong>Consent:</strong> For marketing communications, photography, and optional data collection.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-undo"></i> 4. Data Sharing & Third Parties</h3>
				<p>We do not sell, rent, or trade your personal information. However, we may share data with:</p>
				<ul class="terms-list">
					<li><strong>Payment Processors:</strong> To securely process transactions (e.g., payment gateway partners).</li>
					<li><strong>Venue Partners:</strong> For event logistics, security, and operational purposes.</li>
					<li><strong>Email Service Providers:</strong> To send confirmations, newsletters, and event updates.</li>
					<li><strong>Legal Authorities:</strong> When required by law or to prevent fraud and illegal activities.</li>
					<li><strong>Business Partners:</strong> With your explicit consent for co-branded events or partnerships.</li>
				</ul>
				<p>All third parties are contractually bound to maintain confidentiality and use data only for specified purposes.</p>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-clipboard-check"></i> 5. Cookies & Tracking Technologies</h3>
				<ul class="terms-list">
					<li>We use cookies to remember your preferences, improve user experience, and track site analytics.</li>
					<li><strong>Essential Cookies:</strong> Required for website functionality and security.</li>
					<li><strong>Analytics Cookies:</strong> Track user behavior to improve our services (e.g., Google Analytics).</li>
					<li><strong>Marketing Cookies:</strong> Used to personalize advertisements and marketing campaigns.</li>
					<li>You can disable cookies in your browser settings, but some features may not work properly.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-users-slash"></i> 6. Data Security & Protection</h3>
				<ul class="terms-list">
					<li>We implement industry-standard security measures including SSL encryption for data transmission.</li>
					<li>Sensitive information (passwords, payment details) are encrypted and stored securely.</li>
					<li>Access to personal data is restricted to authorized personnel only.</li>
					<li>We regularly review and update our security practices to prevent unauthorized access.</li>
					<li>However, no online transmission is completely secure; you assume risk by using our services.</li>
					<li>In case of a data breach, we will notify affected users within 30 days as per legal requirements.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-camera"></i> 7. Photography & Media Rights</h3>
				<ul class="terms-list">
					<li>By attending or participating in Rang Exhibitions, you consent to be photographed or recorded for promotional purposes.</li>
					<li>We may use images/videos in marketing materials, social media, advertisements, and press releases.</li>
					<li>If you do not wish to be photographed, please inform our staff at the event.</li>
					<li>Exhibitors retain ownership of their product photography but grant Rang a license to use images for event promotion.</li>
					<li>We respect intellectual property rights and will not reproduce copyrighted material without permission.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-lock"></i> 8. Data Retention</h3>
				<p>We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy:</p>
				<ul class="terms-list">
					<li><strong>Booking/Event Data:</strong> Retained for 3 years for tax, legal, and customer service purposes.</li>
					<li><strong>Marketing Communications:</strong> Retained until you unsubscribe from our mailing list.</li>
					<li><strong>Technical/Analytics Data:</strong> Automatically deleted after 6-12 months.</li>
					<li><strong>Payment Information:</strong> Retained per PCI-DSS compliance and GST requirements.</li>
				</ul>
				<p>You may request deletion of your data at any time, subject to legal and contractual obligations.</p>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-user-shield"></i> 9. Your Rights & Choices</h3>
				<p>Depending on your location, you have certain rights regarding your personal data:</p>
				<ul class="terms-list">
					<li><strong>Right to Access:</strong> Request a copy of the personal data we hold about you.</li>
					<li><strong>Right to Correction:</strong> Update or correct inaccurate information.</li>
					<li><strong>Right to Deletion:</strong> Request deletion of your data (subject to legal obligations).</li>
					<li><strong>Right to Withdraw Consent:</strong> Opt-out of marketing communications anytime.</li>
					<li><strong>Right to Data Portability:</strong> Receive your data in a machine-readable format.</li>
					<li>To exercise these rights, contact us at <strong>rangexhibition.india@gmail.com</strong> with proof of identity.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-hands-helping"></i> 10. Marketing Communications</h3>
				<ul class="terms-list">
					<li>We send promotional emails, event updates, and newsletters based on your preferences.</li>
					<li>You can unsubscribe from marketing communications by clicking the "Unsubscribe" link in any email.</li>
					<li>Transactional emails (bookings, confirmations, receipts) will continue even if you opt-out of marketing.</li>
					<li>SMS notifications are sent only with your prior explicit consent.</li>
					<li>We never share your email with third parties for marketing without your permission.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-exclamation-triangle"></i> 11. Children's Privacy</h3>
				<p>Rang Exhibitions does not knowingly collect personal information from children under 18 years of age. If you are under 18, please do not provide personal information without parental consent. If we discover that we have collected data from a minor without consent, we will delete it immediately. For questions about children's privacy, contact us at <strong>rangexhibition.india@gmail.com</strong>.</p>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-gavel"></i> 12. International Data Transfers</h3>
				<ul class="terms-list">
					<li>Rang Exhibitions is based in India and operates under Indian law (including DPDP Act).</li>
					<li>Your personal data is primarily stored and processed within India.</li>
					<li>If data is transferred internationally, we ensure appropriate safeguards and legal mechanisms are in place.</li>
					<li>By using our services, you consent to processing of your data in India.</li>
				</ul>
			</div>

			<div class="terms-section">
				<h3><i class="fas fa-pen-to-square"></i> 13. Changes to This Privacy Policy</h3>
				<p>We may update this Privacy Policy from time to time to reflect changes in our practices, technology, legal requirements, or other factors. The "Last updated" date at the top indicates the most recent revision. We will notify you of material changes via email or prominent notice on our website. Your continued use of our services after updates indicates your acceptance of the revised policy.</p>
			</div>

			<div class="cta-card">
				<p class="cta-text"><strong>Questions about your privacy?</strong> We're here to help. Contact us for any concerns or to exercise your data rights.</p>
				<a href="mailto:rangexhibition.india@gmail.com" class="u-btn-primary">Contact Privacy Officer</a>
			</div>

			<p class="footer-note">This Privacy Policy is effective as of January 13, 2026. We are committed to protecting your privacy in compliance with the Digital Personal Data Protection Act, 2023 and other applicable laws.</p>
		</div>

		<!-- Footer -->
        <?php require_once '../../admin/assets/elements/user-side/footer.php'; ?>
	</div>

	<script src="../../assets/js/nav.js"></script>

	<!-- WhatsApp balloon -->
	<?php require_once '../../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>