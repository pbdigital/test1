<?php

if ( is_user_logged_in() ) {


	// Action - Before buddypress profile menu.
	do_action( THEME_HOOK_PREFIX . 'before_bb_profile_menu' );

	if ( bp_is_active( 'xprofile' ) ) {
		// Profile link.
		$profile_link = trailingslashit( bp_loggedin_user_domain() . bp_get_profile_slug() );

		$is_enable_profile_avatar = true;
		if ( function_exists( 'bp_disable_group_avatar_uploads' ) && bp_disable_avatar_uploads() ) {
			$is_enable_profile_avatar = false;
		}
		?>
		<li id="wp-admin-bar-my-account-xprofile" class="menupop parent">
			<a class="ab-item" aria-haspopup="true" href="<?php echo esc_url( $profile_link ); ?>">
				<i class="bb-icon-l bb-icon-user-avatar"></i>
				<span class="wp-admin-bar-arrow" aria-hidden="true"></span><?php esc_html_e( 'Profile', 'buddyboss-theme' ); ?>
			</a>
			
		</li>
		<?php
	}

	// Action - After buddypress xprofile menu.
	do_action( THEME_HOOK_PREFIX . 'after_bb_xprofile_menu' );

	if ( bp_is_active( 'settings' ) ) {
		// Setup the logged in user variables.
		$settings_link = trailingslashit( bp_loggedin_user_domain() . bp_get_settings_slug() );

		?>
		<li id="wp-admin-bar-my-account-settings" class="menupop parent">
			<a class="ab-item" aria-haspopup="true" href="<?php echo esc_url( $settings_link ); ?>">
				<i class="bb-icon-l bb-icon-user"></i>
				<span class="wp-admin-bar-arrow" aria-hidden="true"></span><?php esc_html_e( 'Account', 'buddyboss-theme' ); ?>
			</a>
			 
		</li>
		<?php
	}

	// Action - After buddypress setting menu.
	do_action( THEME_HOOK_PREFIX . 'after_bb_setting_menu' );

	
	if ( bp_is_active( 'notifications' ) ) {
		// Setup the logged in user variables.
		$notifications_link = trailingslashit( bp_loggedin_user_domain() . bp_get_notifications_slug() );

		// Pending notification requests.
		$count = bp_notifications_get_unread_notification_count( bp_loggedin_user_id() );
		if ( ! empty( $count ) ) {
			$title = sprintf(
			/* translators: %s: Unread notification count for the current user */
				__( 'Notifications %s', 'buddyboss-theme' ),
				'<span class="count">' . bp_core_number_format( $count ) . '</span>'
			);
			$unread = sprintf(
			/* translators: %s: Unread notification count for the current user */
				__( 'Unread %s', 'buddyboss-theme' ),
				'<span class="count">' . bp_core_number_format( $count ) . '</span>'
			);
		} else {
			$title  = __( 'Notifications', 'buddyboss-theme' );
			$unread = __( 'Unread', 'buddyboss-theme' );
		}

		?>
		<li id="wp-admin-bar-my-account-notifications" class="menupop parent">
			<a class="ab-item" aria-haspopup="true" href="<?php echo esc_url( $notifications_link ); ?>">
				<i class="bb-icon-l bb-icon-bell"></i>
				<span class="wp-admin-bar-arrow" aria-hidden="true"></span><?php echo wp_kses_post( $title ); ?>
			</a>
			<div class="ab-sub-wrapper wrapper">
				<ul id="wp-admin-bar-my-account-notifications-default" class="ab-submenu">
					<li id="wp-admin-bar-my-account-notifications-unread">
						<a class="ab-item" href="<?php echo esc_url( $notifications_link ); ?>"><?php echo $unread; ?></a>
					</li>
					<li id="wp-admin-bar-my-account-notifications-read">
						<a class="ab-item" href="<?php echo esc_url( trailingslashit( $notifications_link . 'read' ) ); ?>"><?php esc_html_e( 'Read', 'buddyboss-theme' ); ?></a>
					</li>
				</ul>
			</div>
		</li>
		<?php
	}
	

	// Action - After buddypress notifications menu.
	do_action( THEME_HOOK_PREFIX . 'after_bb_notifications_menu' );

	?>
	<?php if (false): ?>
	<li id="wp-admin-bar-my-account-invites" class="menupop parent">
		<a class="ab-item" aria-haspopup="true" href="<?php echo site_url("avatar-store"); ?>" style="
    display: flex;
    align-items: center;
">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right:10px">
			<path d="M1.44 8.04002C1.44266 8.49314 1.58781 8.934 1.85484 9.3001C2.12188 9.6662 2.49734 9.93908 2.92796 10.08V18.888C2.92796 19.5246 3.18086 20.1351 3.63092 20.5851C4.08098 21.0352 4.69146 21.288 5.32796 21.288H18.648C19.2845 21.288 19.895 21.0352 20.3451 20.5851C20.7951 20.135 21.048 19.5246 21.048 18.888V10.104C21.487 9.96597 21.8705 9.69128 22.1425 9.31996C22.4145 8.94872 22.5608 8.50028 22.56 8.04004V6.74402L20.712 3.67202C20.3702 3.08006 19.7395 2.71436 19.056 2.71202H5.28002C4.6501 2.70772 4.05822 3.01264 3.69604 3.52796L1.51204 6.64796V6.71999H1.48798V6.88804L1.44001 6.88796L1.44 8.04002ZM10.248 3.67202L9.31198 6.79202V8.376H9.31206C9.31206 9.00194 8.97808 9.5803 8.43604 9.89326C7.89394 10.2062 7.22612 10.2062 6.684 9.89326C6.14196 9.5803 5.80798 9.00194 5.80798 8.376V7.056L7.84798 3.672L10.248 3.67202ZM12.768 3.67202L13.776 6.98402V8.40004L13.776 8.39996C13.776 9.0259 13.4421 9.60426 12.9 9.91722C12.3579 10.2302 11.6901 10.2302 11.148 9.91722C10.6059 9.60426 10.272 9.0259 10.272 8.39996V7.00792L11.28 3.69592L12.768 3.67202ZM16.56 3.67202L18.24 7.03202V8.39998C18.24 9.02592 17.9061 9.60428 17.364 9.91724C16.8219 10.2302 16.1541 10.2302 15.612 9.91724C15.0699 9.60428 14.736 9.02592 14.736 8.39998V6.83998L13.8 3.71998L16.56 3.67202ZM5.088 9.60002C5.51198 10.4692 6.36676 11.0467 7.3312 11.116C8.29574 11.1853 9.22416 10.7356 9.768 9.93604C10.2743 10.6696 11.1087 11.1075 12 11.1075C12.8913 11.1075 13.7257 10.6696 14.232 9.93604C14.7758 10.7356 15.7042 11.1853 16.6688 11.116C17.6333 11.0467 18.4881 10.4692 18.912 9.60002C19.2349 9.90908 19.6458 10.1103 20.0879 10.176V16.608H3.91194V10.176C4.35404 10.1103 4.76498 9.90908 5.08788 9.60002H5.088ZM18.648 20.328H5.352C4.97004 20.328 4.6038 20.1763 4.33372 19.9063C4.06372 19.6362 3.912 19.27 3.912 18.888V17.568H20.088V18.888C20.088 19.27 19.9363 19.6362 19.6663 19.9063C19.3962 20.1763 19.03 20.328 18.648 20.328ZM19.872 4.15202L21.6 7.05602V8.04C21.6 8.46876 21.3713 8.86484 21 9.07922C20.6288 9.2936 20.1713 9.2936 19.8 9.07922C19.4288 8.86484 19.2 8.46876 19.2 8.04V6.72L17.616 3.672H19.056C19.226 3.67044 19.3933 3.71411 19.5409 3.79841C19.6885 3.88278 19.811 4.00473 19.896 4.15201L19.872 4.15202ZM4.51198 4.08007C4.68815 3.82929 4.97346 3.67765 5.27994 3.67211H6.67198L4.89596 6.64811H4.87198V7.94413C4.87198 8.37289 4.64322 8.76905 4.27198 8.98335C3.90066 9.19773 3.44322 9.19773 3.07198 8.98335C2.70066 8.76905 2.47198 8.37289 2.47198 7.94413V6.98413L4.51198 4.08007Z" fill="#A3A5A9"/>
			</svg>

			<span class="wp-admin-bar-arrow" aria-hidden="true"></span> Avatar Store
		</a> 
	</li>
	<?php endif; ?>
	<?php

	/**
	 * Action - After buddypress video menu.
	 *
	 * @since 1.7.0
	 */
	do_action( THEME_HOOK_PREFIX . 'after_bb_video_menu' );

	if ( bp_is_active( 'invites' ) && true === bp_allow_user_to_send_invites() ) {
		// Setup the logged in user variables.
		$invites_link = trailingslashit( bp_loggedin_user_domain() . bp_get_invites_slug() );

		?>
		<li id="wp-admin-bar-my-account-invites" class="menupop parent">
			<a class="ab-item" aria-haspopup="true" href="<?php echo esc_url( $invites_link ); ?>">
				<i class="bb-icon-l bb-icon-envelope"></i>
				<span class="wp-admin-bar-arrow" aria-hidden="true"></span><?php esc_html_e( 'Email Invites', 'buddyboss-theme' ); ?>
			</a>
			<div class="ab-sub-wrapper wrapper">
				<ul id="wp-admin-bar-my-account-invites-default" class="ab-submenu">
					<li id="wp-admin-bar-my-account-invites-invites">
						<a class="ab-item" href="<?php echo esc_url( $invites_link ); ?>"><?php esc_html_e( 'Send Invites', 'buddyboss-theme' ); ?></a>
					</li>
					<li id="wp-admin-bar-my-account-invites-sent">
						<a class="ab-item" href="<?php echo esc_url( trailingslashit( $invites_link . 'sent-invites' ) ); ?>"><?php esc_html_e( 'Sent Invites', 'buddyboss-theme' ); ?></a>
					</li>
				</ul>
			</div>
		</li>
		<?php
	}

	// Action - After buddypress profile menu.
	do_action( THEME_HOOK_PREFIX . 'after_bb_profile_menu' );

	?>
	<li class="logout-link">
		<a href="<?php echo esc_url( wp_logout_url( bp_get_requested_url() ) ); ?>">
			<i class="bb-icon-l bb-icon-sign-out"></i>
			<?php esc_html_e( 'Log Out', 'buddyboss-theme' ); ?>
		</a>
	</li>
	<?php
}
