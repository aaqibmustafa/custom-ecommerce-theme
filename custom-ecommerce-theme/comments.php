<?php
/**
 * The template for displaying comments
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area cet-widget" style="margin-top:2.5rem;">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title cet-widget__title">
			<?php
			$cet_comment_count = get_comments_number();
			printf(
				/* translators: 1: comment count number, 2: post title. */
				esc_html( _nx( '%1$s Response on &ldquo;%2$s&rdquo;', '%1$s Responses on &ldquo;%2$s&rdquo;', $cet_comment_count, 'comments title', 'custom-ecommerce-theme' ) ),
				esc_html( number_format_i18n( $cet_comment_count ) ),
				'<span>' . wp_kses_post( get_the_title() ) . '</span>'
			);
			?>
		</h2>

		<?php the_comments_navigation(); ?>

		<ol class="comment-list" style="list-style:none; padding:0; margin:0 0 1.5rem;">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size'=> 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation();

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'custom-ecommerce-theme' ); ?></p>
			<?php
		endif;
	endif;

	comment_form(
		array(
			'class_submit' => 'cet-btn cet-btn--gold',
		)
	);
	?>
</div><!-- #comments -->

