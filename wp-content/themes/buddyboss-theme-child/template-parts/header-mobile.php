<?php


$show_search        = buddyboss_theme_get_option( 'mobile_component_opt_multi_checkbox', 'mobile_header_search' );
$show_messages      = buddyboss_theme_get_option( 'mobile_component_opt_multi_checkbox', 'mobile_messages' ) && is_user_logged_in();
$show_notifications = buddyboss_theme_get_option( 'mobile_component_opt_multi_checkbox', 'mobile_notifications' ) && is_user_logged_in();
$show_shopping_cart = buddyboss_theme_get_option( 'mobile_component_opt_multi_checkbox', 'mobile_shopping_cart' );
$logo_align         = count( array_filter( array( $show_search, $show_messages, $show_notifications, $show_shopping_cart ) ) );
$logo_class         = ( $logo_align <= 1 && ( ! buddyboss_is_learndash_inner() && ! buddyboss_is_lifterlms_inner() ) ) ? 'bb-single-icon' : '';

?>

<div class="bb-mobile-header-wrapper <?php echo esc_attr( $logo_class ); ?>">
	<div class="bb-mobile-header flex align-items-center">
		<div class="bb-left-panel-icon-wrap">
			<a href="#" class="push-left bb-left-panel-mobile"><i class="bb-icon-l bb-icon-bars"></i></a>
		</div>

		
		<div class="header-aside">
			<?= do_shortcode('[user_points]') ?>
			<?php
			if (
				(
					class_exists( 'SFWD_LMS' ) &&
					buddyboss_is_learndash_inner()
				) ||
				(
					class_exists( 'LifterLMS' ) &&
					buddyboss_is_lifterlms_inner()
				)
			) {
				?>
				<?php if ( is_user_logged_in() ) { ?>
					<a href="#" id="bb-toggle-theme">
						<span class="sfwd-dark-mode" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Dark Mode', 'buddyboss-theme' ); ?>"><i class="bb-icon-rl bb-icon-moon"></i></span>
						<span class="sfwd-light-mode" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Light Mode', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-sun"></i></span>
					</a>
					<a href="#" class="header-maximize-link course-toggle-view" data-balloon-pos="left" data-balloon="<?php esc_html_e( 'Hide Sidepanel', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-expand"></i></a>
					<a href="#" class="header-minimize-link course-toggle-view" data-balloon-pos="left" data-balloon="<?php esc_html_e( 'Show Sidepanel', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-expand"></i></a>
				<?php } else {
					if ( $show_search ) : ?>
						<a data-balloon-pos="left" data-balloon="<?php esc_html_e( 'Search', 'buddyboss-theme' ); ?>" href="#" class="push-right header-search-link"><i class="bb-icon-l bb-icon-search"></i></a>
						<span class="search-separator bb-separator"></span>
					<?php endif;
					if ( $show_shopping_cart && class_exists( 'WooCommerce' ) ) : ?>
						<?php get_template_part( 'template-parts/cart-dropdown' ); ?>
					<?php endif; ?>

					<a href="#" class="header-maximize-link course-toggle-view" data-balloon-pos="left" data-balloon="<?php esc_html_e( 'Hide Sidepanel', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-expand"></i></a>
					<a href="#" class="header-minimize-link course-toggle-view" data-balloon-pos="left" data-balloon="<?php esc_html_e( 'Show Sidepanel', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-expand"></i></a>
					<?php
				}
			} else {
				if ( $show_search ) : ?>
					<a data-balloon-pos="left" data-balloon="<?php esc_html_e( 'Search', 'buddyboss-theme' ); ?>" href="#" class="push-right header-search-link"><i class="bb-icon-l bb-icon-search"></i></a>
					<?php if ( $show_messages && function_exists( 'bp_is_active' ) && bp_is_active( 'messages' ) || $show_notifications && function_exists( 'bp_is_active' ) && bp_is_active( 'notifications' ) || $show_shopping_cart && class_exists( 'WooCommerce' ) ) : ?>
						<span class="search-separator bb-separator"></span>
					<?php endif;
				endif;

				if ( $show_messages && function_exists( 'bp_is_active' ) && bp_is_active( 'messages' ) ) :
					get_template_part( 'template-parts/messages-dropdown' );
				endif;

				if ( $show_notifications && function_exists( 'bp_is_active' ) && bp_is_active( 'notifications' ) ) :
					get_template_part( 'template-parts/notification-dropdown' );
				endif;

				if ( $show_shopping_cart && class_exists( 'WooCommerce' ) ) :
					get_template_part( 'template-parts/cart-dropdown' );
				endif;
			}
			?>
		</div>
	</div>

	<div class="header-search-wrap">
		<div class="container" test123 >
			<form role="search" method="get" class="search-form" action="<?=site_url("search")?>">
				<label>
					<span class="screen-reader-text">Search for:</span>
					<input type="text" name="search" id="search" class="search" autocomplete="off" placeholder="Search..." required="" maxlength="200" value="">
				</label>
			</form>
			<a data-balloon-pos="left" data-balloon="<?php esc_html_e( 'Close', 'buddyboss-theme' ); ?>" href="#" class="close-search"><i class="bb-icon-l bb-icon-times"></i></a>
		</div>
	</div>
</div>

<div class="bb-mobile-panel-wrapper left light closed header-wrapper">
	<div class="bb-mobile-panel-inner">
		<div class="bb-mobile-panel-header">
			<?php if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$user_link    = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $current_user->ID ) : get_author_posts_url( $current_user->ID );
				$display_name = function_exists( 'bp_core_get_user_displayname' ) ? bp_core_get_user_displayname( $current_user->ID ) : $current_user->display_name;
				?>
				<div class="user-wrap">
					<a href="<?php echo esc_url( $user_link ); ?>"><?php echo get_avatar( $current_user->ID, 100 ); ?></a>
					<div>
						<a href="<?php echo esc_url( $user_link ); ?>">
							<span class="user-name"><?php echo esc_html( $display_name ); ?></span>
						</a>
						<?php
						if ( function_exists( 'bp_is_active' ) && bp_is_active( 'settings' ) ) {
							$settings_link = trailingslashit( bp_loggedin_user_domain() . bp_get_settings_slug() );
							?>
							<div class="my-account-link"><a class="ab-item" aria-haspopup="true" href="<?php echo esc_url( $settings_link ); ?>"><?php esc_html_e( 'My Account', 'buddyboss-theme' ); ?></a></div>
							<?php
						}
						?>
					</div>
				</div>
			<?php } else { ?>
				<div class="logo-wrap">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<?php echo $logo; ?>
					</a>
				</div>
			<?php } ?>
			<a href="#" class="bb-close-panel"><i class="bb-icon-l bb-icon-times"></i></a>
		</div>
		

		<nav class="main-navigation" data-menu-space="120">
			<?php

			if ( is_user_logged_in() ) {

				if ( has_nav_menu( 'mobile-menu-logged-in' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'mobile-menu-logged-in',
							'menu_id'        => '',
							'container'      => false,
							'fallback_cb'    => false,
							'walker'         => new BuddyBoss_BuddyPanel_Menu_Walker(),
							'menu_class'     => 'bb-primary-menu mobile-menu buddypanel-menu side-panel-menu',
						)
					);

				} else {

					wp_nav_menu(
						array(
							'theme_location' => 'header-menu',
							'menu_id'        => '',
							'container'      => false,
							'fallback_cb'    => false,
							'menu_class'     => 'bb-primary-menu mobile-menu buddypanel-menu side-panel-menu',
						)
					);

					if ( has_nav_menu( 'buddypanel-loggedin') &&  has_nav_menu( 'header-menu' ) ) {
						echo '<hr />';
					}


					$is_parent = \Safar\SafarFamily::is_user_parent( );
					$is_user_teacher = \Safar\SafarUser::is_user_teacher( );
					$menu = 'buddypanel-loggedin';
					$menu_args = [
						'theme_location' => $menu,
						'menu_id'        => '',
						'container'      => false,
						'fallback_cb'    => false,
						'walker'         => new BuddyBoss_BuddyPanel_Menu_Walker(),
						'menu_class'     => 'bb-primary-menu mobile-menu buddypanel-menu side-panel-menu',
						
					];

					if($is_parent){
						$menu_args["menu"] = "Parents Menu";
					}	
					if($is_user_teacher){
						$menu_args["menu"] = "Teachers Menu";
					}	


					wp_nav_menu( $menu_args );

				}

			} else {

				if ( has_nav_menu( 'mobile-menu-logged-out' ) ) {

					wp_nav_menu(
						array(
							'theme_location' => 'mobile-menu-logged-out',
							'menu_id'        => '',
							'container'      => false,
							'fallback_cb'    => false,
							'walker'         => new BuddyBoss_BuddyPanel_Menu_Walker(),
							'menu_class'     => 'bb-primary-menu mobile-menu buddypanel-menu side-panel-menu',
						)
					);

				} else {

					wp_nav_menu(
						array(
							'theme_location' => 'header-menu-logout',
							'menu_id'        => '',
							'container'      => false,
							'fallback_cb'    => false,
							'menu_class'     => 'bb-primary-menu mobile-menu buddypanel-menu side-panel-menu',
						)
					);

					if ( has_nav_menu( 'buddypanel-loggedout') &&  has_nav_menu( 'header-menu-logout' ) ) {
						echo '<hr />';
					}

					wp_nav_menu(
						array(
							'theme_location' => 'buddypanel-loggedout',
							'menu_id'        => '',
							'container'      => false,
							'fallback_cb'    => false,
							'walker'         => new BuddyBoss_BuddyPanel_Menu_Walker(),
							'menu_class'     => 'bb-primary-menu mobile-menu buddypanel-menu side-panel-menu',
						)
					);

				}

			}
			?>
		</nav>

	</div>
</div>
