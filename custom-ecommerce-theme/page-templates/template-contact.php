<?php
/**
 * Template Name: Client Concierge & Contact
 * Template Post Type: page
 *
 * Luxury two-column Contact & Private Client Concierge page template with nonce-verified inquiry handler.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_sent  = false;
$form_error = '';

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['cet_contact_nonce'] ) ) {
	if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cet_contact_nonce'] ) ), 'cet_submit_contact' ) ) {
		$sender_name    = isset( $_POST['cet_name'] ) ? sanitize_text_field( wp_unslash( $_POST['cet_name'] ) ) : '';
		$sender_email   = isset( $_POST['cet_email'] ) ? sanitize_email( wp_unslash( $_POST['cet_email'] ) ) : '';
		$sender_subject = isset( $_POST['cet_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['cet_subject'] ) ) : '';
		$sender_message = isset( $_POST['cet_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cet_message'] ) ) : '';

		if ( empty( $sender_name ) || ! is_email( $sender_email ) || empty( $sender_message ) ) {
			$form_error = __( 'Please complete all required fields with a valid email address.', 'custom-ecommerce-theme' );
		} else {
			$admin_email = get_option( 'admin_email' );
			$subject     = sprintf( '[%s Concierge Inquiry] %s', get_bloginfo( 'name' ), $sender_subject ?: __( 'General Inquiry', 'custom-ecommerce-theme' ) );
			$body        = sprintf(
				"Name: %s\nEmail: %s\n\nMessage:\n%s",
				$sender_name,
				$sender_email,
				$sender_message
			);
			$headers     = array( 'Reply-To: ' . $sender_name . ' <' . $sender_email . '>' );

			wp_mail( $admin_email, $subject, $body, $headers );
			$form_sent = true;
		}
	}
}

get_header();

if ( function_exists( 'cet_breadcrumbs' ) ) {
	cet_breadcrumbs();
}

$phone   = (string) cet_get_option( 'footer_phone' );
$email   = (string) cet_get_option( 'footer_email' );
$address = (string) cet_get_option( 'footer_address' );
?>

<main id="primary" class="site-main cet-main">
	<div class="cet-container">
		<header class="cet-section-header">
			<div>
				<span class="cet-eyebrow"><?php esc_html_e( 'At Your Service', 'custom-ecommerce-theme' ); ?></span>
				<h1 class="cet-section-title"><?php the_title(); ?></h1>
			</div>
		</header>

		<div class="cet-grid-cols-2" style="align-items:start;">
			<!-- Left Column: Atelier Details -->
			<div class="cet-promo-card cet-promo-card--dark" style="padding:2.5rem;">
				<span class="cet-eyebrow"><?php esc_html_e( 'Direct Concierge', 'custom-ecommerce-theme' ); ?></span>
				<h3><?php esc_html_e( 'Private Client Assistance', 'custom-ecommerce-theme' ); ?></h3>
				<p style="opacity:0.86; margin-bottom:2rem;">
					<?php esc_html_e( 'Our specialists are available to assist with bespoke sizing, corporate gifting, order tracking, and private appointments.', 'custom-ecommerce-theme' ); ?>
				</p>

				<div style="display:flex; flex-direction:column; gap:1.25rem;">
					<div>
						<strong style="color:var(--color-accent); display:block; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em;"><?php esc_html_e( 'Flagship Atelier', 'custom-ecommerce-theme' ); ?></strong>
						<span><?php echo esc_html( $address ); ?></span>
					</div>
					<div>
						<strong style="color:var(--color-accent); display:block; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em;"><?php esc_html_e( 'Telephone Concierge', 'custom-ecommerce-theme' ); ?></strong>
						<span><?php echo esc_html( $phone ); ?></span>
					</div>
					<div>
						<strong style="color:var(--color-accent); display:block; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em;"><?php esc_html_e( 'Electronic Mail', 'custom-ecommerce-theme' ); ?></strong>
						<span><?php echo esc_html( $email ); ?></span>
					</div>
				</div>
			</div>

			<!-- Right Column: Inquiry Form -->
			<div class="cet-widget" style="padding:2.5rem;">
				<?php if ( $form_sent ) : ?>
					<div class="woocommerce-message" role="alert">
						<?php esc_html_e( 'Thank you. Your inquiry has been received by our Client Concierge team.', 'custom-ecommerce-theme' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $form_error ) ) : ?>
					<div class="woocommerce-error" role="alert">
						<?php echo esc_html( $form_error ); ?>
					</div>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( get_permalink() ); ?>">
					<?php wp_nonce_field( 'cet_submit_contact', 'cet_contact_nonce' ); ?>

					<p>
						<label for="cet_name"><strong><?php esc_html_e( 'Full Name *', 'custom-ecommerce-theme' ); ?></strong></label>
						<input type="text" id="cet_name" name="cet_name" required>
					</p>

					<p>
						<label for="cet_email"><strong><?php esc_html_e( 'Email Address *', 'custom-ecommerce-theme' ); ?></strong></label>
						<input type="email" id="cet_email" name="cet_email" required>
					</p>

					<p>
						<label for="cet_subject"><strong><?php esc_html_e( 'Subject / Order Number', 'custom-ecommerce-theme' ); ?></strong></label>
						<input type="text" id="cet_subject" name="cet_subject">
					</p>

					<p>
						<label for="cet_message"><strong><?php esc_html_e( 'Your Message *', 'custom-ecommerce-theme' ); ?></strong></label>
						<textarea id="cet_message" name="cet_message" rows="5" required></textarea>
					</p>

					<button type="submit" class="cet-btn cet-btn--gold">
						<?php esc_html_e( 'Send Concierge Inquiry', 'custom-ecommerce-theme' ); ?>
					</button>
				</form>
			</div>
		</div>
	</div>
</main><!-- #primary -->

<?php
get_footer();
