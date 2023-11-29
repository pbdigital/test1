<aside class="buddypanel <?php echo esc_attr( buddyboss_theme_get_option( 'buddypanel_toggle' ) ? 'buddypanel--toggle-on' : 'buddypanel--toggle-off' ); ?>">
	<?php
	$menu              = is_user_logged_in() ? 'buddypanel-loggedin' : 'buddypanel-loggedout';
	$header            = (int) buddyboss_theme_get_option( 'buddyboss_header' );
	$buddypanel_logo   = buddyboss_theme_get_option( 'buddypanel_show_logo' );
	$buddypanel_state  = buddyboss_theme_get_option( 'buddypanel_state' );
	$buddypanel_toggle = buddyboss_theme_get_option( 'buddypanel_toggle' );

	

	

	if ( $buddypanel_toggle ) {
		?>
		<header class="panel-head">
			<a href="#" class="bb-toggle-panel"><i class="bb-icon-l bb-icon-sidebar"></i></a>
		</header>
		<?php
	}
	if (
		3 === $header &&
		! buddypanel_is_learndash_inner() &&
		$buddypanel_logo
	) {
		get_template_part( 'template-parts/site-logo' );

	} elseif (
		3 !== $header &&
		$buddypanel_logo
	) {
		get_template_part( 'template-parts/site-logo' );

	} elseif ( 3 === $header && buddypanel_is_learndash_inner() && $buddypanel_logo ) {
		if ( buddyboss_is_learndash_brand_logo() && buddyboss_theme_ld_focus_mode() ) {
			?>
			<div class="site-branding ld-brand-logo">
				<img src="<?php echo esc_url( wp_get_attachment_url( buddyboss_is_learndash_brand_logo() ) ); ?>" alt="<?php echo esc_attr( get_post_meta( buddyboss_is_learndash_brand_logo(), '_wp_attachment_image_alt', true ) ); ?>">
			</div>
			<?php
		} else {
			get_template_part( 'template-parts/site-logo' );
		}
	}


	// institute logo 
	$site_icon_class = $buddypanel_logo ? ' buddypanel_on_' . $buddypanel_state . '_site_icon' : 'buddypanel_off_' . $buddypanel_state . '_site_icon';
	$site_icon_url   = get_site_icon_url( 38 );


	if ( ! empty( $site_icon_url ) ) {

		$buddypanel      = buddyboss_theme_get_option( 'buddypanel' );
		$show            = buddyboss_theme_get_option( 'logo_switch' );
		$show_dark       = buddyboss_theme_get_option( 'logo_dark_switch' );
		$logo_id         = buddyboss_theme_get_option( 'logo', 'id' );
		$logo_dark_id    = buddyboss_theme_get_option( 'logo_dark', 'id' );
		$buddypanel_logo = buddyboss_theme_get_option( 'buddypanel_show_logo' );
		$logo            = ( $show && $logo_id ) ? wp_get_attachment_image( $logo_id, 'full', '', array( 'class' => 'bb-logo' ) ) : get_bloginfo( 'name' );
		$logo_dark       = ( $show && $show_dark && $logo_dark_id ) ? wp_get_attachment_image( $logo_dark_id, 'full', '', array( 'class' => 'bb-logo bb-logo-dark' ) ) : '';

		 
		?>
		<div class="buddypanel-site-icon test123 <?php echo esc_attr( $site_icon_class ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="buddypanel-site-icon-link">
				<?=$logo?>
			</a>
		</div>
		<?php
	}
	?>
	<div class="side-panel-inner">
		<div class="side-panel-menu-container">
			<?php
			$user = wp_get_current_user();
			$roles = $user->roles;
			
			if(strpos( get_page_template_slug() , "manage-classrooms.php") !== false ){
				$institutes = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );

				
				if(!empty($institutes)){
					$school = \Safar\SafarSchool::get_user_school_id();
					if( sizeof($institutes) > 1 ){
						/*
						?>
						<select class="select-institute">
							<option value="">Select Institute</option>
							<?php 
							foreach($institutes as $institute){
								$selected = "";
								if($institute->ID == $school["learndash_parent_group_id"]) $selected = "selected";
								?>
								<option value="<?=$institute->ID?>" <?=$selected?> ><?=$institute->post_title?></option>
								<?php
							}
							?>
						</select>

						*/?>
						<div class="sidebar-select-institute-container">
							<ul class="buddypanel-menu side-panel-menu select-institute-menu">
								<li>
									<span>Switch Institute</span>
									<svg width="8" height="15" viewBox="0 0 8 15" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M1 13.5L7 7.5L1 1.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>

								</li>
							</ul>

							<div class="institutes-list">
								<div class="overlay"></div>
								<div class="items">
									<?php 
									foreach($institutes as $institute){
										$selected = "";
										if($institute->ID == $school["learndash_parent_group_id"]) $selected = "active";
										?>
										<a href="" data-id="<?=$institute->ID?>" class="item <?=$selected?>" >
											<div class="avatar-container" style="<?=( empty($institute->cover_photo)) ? "background-image:url('".$institute->cover_photo."')":""?>"></div>
											<span>
												<?=$institute->post_title?>
											</span>
										</a>
										<?php
									}
									?>
								</div>
							</div>
						</div>
						<?php
					}
				}
			}

			ob_start();

			$is_parent = \Safar\SafarFamily::is_user_parent( );
			$is_user_teacher = \Safar\SafarUser::is_user_teacher( );
			$is_user_institute_parent = \Safar\SafarFamily::is_user_institute_parent();
			
			$menu_args = [
				'theme_location' => $menu,
				//'menu' 			=> "Parents Menu",
				'menu_id'        => 'buddypanel-menu',
				'container'      => false,
				'fallback_cb'    => '',
				'walker'         => new BuddyBoss_BuddyPanel_Menu_Walker(),
				'menu_class'     => 'buddypanel-menu side-panel-menu',
			];

			if($is_parent){
				$menu_args["menu"] = "Parents Menu";
			}	
			if($is_user_teacher){
				$menu_args["menu"] = "Teachers Menu";
			}	
			if($is_user_institute_parent){
				$menu_args["menu"] = "Institute Parent Menu";
			}
			
			$institutes = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );

			$is_institute_admin = \Safar\SafarUser::is_user_institute_admin();
			if( $is_institute_admin ){
				$menu_args["menu"] = "School Admin";
				
			}
			
			wp_nav_menu($menu_args);

			$buddypanel_menu = ob_get_clean();

			if ( str_contains( $buddypanel_menu, 'bb-menu-section' ) ) {
				$buddypanel_menu = str_replace( 'buddypanel-menu side-panel-menu', 'buddypanel-menu side-panel-menu has-section-menu', $buddypanel_menu );
			}

			if(isset($_GET["test"])){
				print_r($menu_args);
			}

			echo $buddypanel_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>
</aside>


<?php 
$page_tpl_slug = get_page_template_slug();
if($page_tpl_slug == "page-templates/admin-institute-onboarding.php" ){
	$logo      = "/wp-content/themes/buddyboss-theme-child/assets/img/admin-institute-onboarding/onboarding-logo.svg";
	
	$is_user_teacher = \Safar\SafarUser::is_user_teacher( );
	$is_parent = \Safar\SafarFamily::is_user_parent( );
	$user_id = get_current_user_id() ;


	if($is_user_teacher){
		$institute = \Safar\SafarSchool::get_institute_by_teacher_id($user_id);
		if(!empty($institute)){
			$logo_institute = $institute["post"]->avatar;
		}else{
			$logo_institute = "";
		}
	}else if($is_parent){
		$institute = \Safar\SafarFamily::get_institute_by_parent_id($user_id);

		if(!empty($institute)){
			$logo_institute = $institute["post"]->avatar;
		}else{
			$logo_institute = "";
		}
 

	}else{
		$institute = \Safar\SafarSchool::get_school_details([])->data;
		$logo_institute = $institute["parent_school"]["post"]->avatar;
	}
	?>
	<div class="choose-avatar-logo">
		<div class="site-logo"><img src="<?=$logo?>"/></div>
		<div class="institute-logo">
			<?php 
			if(!empty($logo_institute)){
				?><img src="<?=$logo_institute?>" /><?php
			}
			?>
		</div>
	</div>
	<?php 
}
?>
