<?php
/**
 * Login & Registration Form inside My Account
 *
 * Styled two-column luxury client portal authentication cards.
 *
 * @package Custom_Ecommerce_Theme
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

<div class="u-columns col2-set cet-grid-cols-2" id="customer_login">

	<div class="u-column1 col-1 cet-widget" style="padding:2.25rem;">

<?php else : ?>

	<div class="cet-widget" style="max-width:520px; margin:0 auto; padding:2.25rem;">

<?php endif; ?>

		<span class="cet-eyebrow"><?php esc_html_e( 'Client Access', 'custom-ecommerce-theme' ); ?></span>
		<h2><?php esc_html_e( 'Sign In to Your Account', 'custom-ecommerce-theme' ); ?></h2>

		<form class="woocommerce-form woocommerce-form-login login" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( 'Username or email address', 'custom-ecommerce-theme' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required /> <?php // @codingStandardsIgnoreLine ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( 'Password', 'custom-ecommerce-theme' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-top:1.25rem;">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme" style="display:inline-flex; align-items:center; gap:0.4rem;">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" style="width:auto;" />
					<span><?php esc_html_e( 'Remember me', 'custom-ecommerce-theme' ); ?></span>
				</label>
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit cet-btn cet-btn--gold" name="login" value="<?php esc_attr_e( 'Log in', 'custom-ecommerce-theme' ); ?>">
					<?php esc_html_e( 'Sign In', 'custom-ecommerce-theme' ); ?>
				</button>
			</p>
			<p class="woocommerce-LostPassword lost_password" style="margin-bottom:0; font-size:0.85rem;">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'custom-ecommerce-theme' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

	</div>

	<div class="u-column2 col-2 cet-widget" style="padding:2.25rem; border-top:3px solid var(--color-accent);">

		<span class="cet-eyebrow"><?php esc_html_e( 'New Collector', 'custom-ecommerce-theme' ); ?></span>
		<h2><?php esc_html_e( 'Create Private Account', 'custom-ecommerce-theme' ); ?></h2>

		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Username', 'custom-ecommerce-theme' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required /> <?php // @codingStandardsIgnoreLine ?>
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Email address', 'custom-ecommerce-theme' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required /> <?php // @codingStandardsIgnoreLine ?>
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'custom-ecommerce-theme' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required />
				</p>

			<?php else : ?>

				<p style="font-size:0.875rem; color:var(--color-text-muted);"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'custom-ecommerce-theme' ); ?></p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="woocommerce-form-row form-row" style="margin-top:1.25rem; margin-bottom:0;">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit cet-btn cet-btn--primary" name="register" value="<?php esc_attr_e( 'Register', 'custom-ecommerce-theme' ); ?>">
					<?php esc_html_e( 'Register Account', 'custom-ecommerce-theme' ); ?>
				</button>
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

</div>
<?php else : ?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

