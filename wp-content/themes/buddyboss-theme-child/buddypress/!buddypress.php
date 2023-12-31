<?php
/**
 * The template for displaying BuddyPress pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */

get_header();

$admin_custom_login              = buddyboss_theme_get_option( 'boss_custom_login' );
$admin_login_background_text     = buddyboss_theme_get_option( 'admin_login_background_text' );
$admin_login_background_textarea = buddyboss_theme_get_option( 'admin_login_background_textarea' );
$admin_login_background_switch   = buddyboss_theme_get_option( 'admin_login_background_switch' );

if ( ( function_exists( 'bp_is_register_page' ) && bp_is_register_page() ) || ( function_exists( 'bp_is_activation_page' ) && bp_is_activation_page() ) ) {
	$class_bp_register = 'bs-bp-container-reg';

	if ( $admin_custom_login && $admin_login_background_switch ) {
		echo '<div class="login-split"><div class="login-split__entry">';
		if ( $admin_login_background_text ) {
			echo '<h1>';
			echo wp_kses_post( sprintf( esc_html__( '%s', 'buddyboss-theme' ), $admin_login_background_text ) );
			echo '</h1>';
		}
		if ( $admin_login_background_textarea ) {
			echo '<div>';
			echo ( $admin_login_background_textarea );
			echo '</div>';
		}
		echo '</div><div class="split-overlay"></div></div>';
	}
} else {
	$class_bp_register = 'bs-bp-container';
}
?>

<div id="primary" class="content-area <?php echo $class_bp_register; ?>">
	<main id="main" class="site-main">
		<?php
		if ( function_exists( 'bp_is_register_page' ) && bp_is_register_page() ) {
			$logo_id                = buddyboss_theme_get_option( 'admin_logo_media', 'id' );
			$logo                   = ( $logo_id ) ? wp_get_attachment_image( $logo_id, 'full', '', array( 'class' => 'bb-logo' ) ) : get_bloginfo( 'name' );
			$enable_private_network = (int) bp_get_option( 'bp-enable-private-network' );
			if ( 0 === $enable_private_network ) {
				?>
				<div class="register-section-logo private-on-div">
					<?php echo wp_kses_post( $logo ); ?>
				</div>
				<?php
			} else {
				?>
				<div class="register-section-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<?php echo wp_kses_post( $logo ); ?>
					</a>
				</div>
				<?php
			}
		} elseif ( function_exists( 'bp_is_activation_page' ) && bp_is_activation_page() ) {
			$logo_id                = buddyboss_theme_get_option( 'admin_logo_media', 'id' );
			$logo                   = ( $logo_id ) ? wp_get_attachment_image( $logo_id, 'full', '', array( 'class' => 'bb-logo' ) ) : get_bloginfo( 'name' );
			$enable_private_network = (int) bp_get_option( 'bp-enable-private-network' );
			if ( 0 === $enable_private_network ) {
				?>
				<div class="activate-section-logo">
					<?php echo wp_kses_post( $logo ); ?>
				</div>
				<?php
			} else {
				?>
				<div class="activate-section-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<?php echo wp_kses_post( $logo ); ?>
					</a>
				</div>
				<?php
			}
		}

		if ( have_posts() ) :

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'buddypress' );

			endwhile;
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
if ( ! bp_is_group_create() && ! bp_is_user_profile_edit() && ! bp_is_user_change_avatar() && ! bp_is_user_change_cover_image() ) {
	get_sidebar( 'buddypress' );
}

get_footer();
