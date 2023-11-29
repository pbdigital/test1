<?php
/**
 * Group Members Loop template
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/single/members-loop.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 */

$footer_buttons_class = ( bp_is_active( 'friends' ) && bp_is_active( 'messages' ) ) ? 'footer-buttons-on' : '';

$is_follow_active = bp_is_active( 'activity' ) && function_exists( 'bp_is_activity_follow_active' ) && bp_is_activity_follow_active();
$follow_class     = $is_follow_active ? 'follow-active' : '';

// Member directories elements.
$enabled_online_status = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'online-status' );
$enabled_profile_type  = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'profile-type' );
$enabled_followers     = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'followers' );
$enabled_last_active   = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'last-active' );
$enabled_joined_date   = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'joined-date' );

$total_members = groups_get_total_member_count(bp_get_current_group_id());

?>

<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) . '&type=group_role&per_page='.$total_members ) ) : ?>
	<style>
		body.groups #item-body .subnav-filters.filters{display:block!important;margin-bottom:40px;}
		.buddypress-wrap .subnav-filters .bp-search{max-width: 580px;width: 100%;}
		.buddypress-wrap .subnav-filters .bp-search form{width: 100%;border: 1px solid #E7E9EE;border-radius: 8px;overflow:hidden}
		.buddypress-wrap .subnav-filters .bp-search form input{width: 100% !important;height: 44px !important;font-family: 'Mikado';font-style: normal;font-weight: 400;font-size: 16px;line-height: 24px;background: #FFFFFF;padding:0 0 0 43px !important}
		.buddypress-wrap .subnav-filters .bp-search form input::placeholder{color: #BFBFBF;}
		body .buddypress-wrap .bp-invites-search-form:before, body .buddypress-wrap form.bp-dir-search-form:before, body .buddypress-wrap form.bp-messages-search-form:before{content:"";width: 24px;height: 24px;background: url('<?=get_stylesheet_directory_uri();?>/assets/img/members/search.svg') no-repeat center center/contain;opacity:1;left: 14px;top: 9px;}
		.buddypress-wrap .grid-filters{height:44px;}
		.buddypress-wrap .subnav-filters .grid-filters a{width: 44px;place-items:center;place-content:center;color:#37394A}
	</style>
	<?php bp_nouveau_group_hook( 'before', 'members_content' ); ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<?php bp_nouveau_group_hook( 'before', 'members_list' ); ?>

	<ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?> members-list"  >

		<?php
		require_once("members-group-type.php");
		/* Have to do another loop for admins*/
		$admins = members_group_type(["admin"]);
		if(!empty($admins)){
			?><li class="item-entry item-entry-header">Admins</li><?php
			echo $admins;
		}
		
		/* For teachers */
		$teachers = members_group_type(["teacher"]);
		if(!empty($teachers)){
			?><li class="item-entry item-entry-header">Teachers</li><?php
			echo $teachers;
		}

		/* For students */
		$students = members_group_type(["student"]);
		if(!empty($students)){
			?><li class="item-entry item-entry-header">Students</li><?php
			echo $students;
		}
		?>

	</ul>

	<?php bp_nouveau_group_hook( 'after', 'members_list' ); ?>

	<?php bp_nouveau_pagination( 'bottom' ); ?>

	<?php bp_nouveau_group_hook( 'after', 'members_content' ); ?>

	<?php
else :

	bp_nouveau_user_feedback( 'group-members-none' );

endif;
?>

<!-- Remove Connection confirmation popup -->
<div class="bb-remove-connection bb-action-popup" style="display: none">
	<transition name="modal">
		<div class="modal-mask bb-white bbm-model-wrap">
			<div class="modal-wrapper">
				<div class="modal-container">
					<header class="bb-model-header">
						<h4><span class="target_name"><?php echo esc_html__( 'Remove Connection', 'buddyboss' ); ?></span></h4>
						<a class="bb-close-remove-connection bb-model-close-button" href="#">
							<span class="bb-icon-l bb-icon-times"></span>
						</a>
					</header>
					<div class="bb-remove-connection-content bb-action-popup-content">
						<p>
							<?php
							echo sprintf(
								/* translators: %s: The member name with HTML tags */
								esc_html__( 'Are you sure you want to remove %s from your connections?', 'buddyboss' ),
								'<span class="bb-user-name"></span>'
							);
							?>
						</p>
					</div>
					<footer class="bb-model-footer flex align-items-center">
						<a class="bb-close-remove-connection bb-close-action-popup" href="#"><?php echo esc_html__( 'Cancel', 'buddyboss' ); ?></a>
						<a class="button push-right bb-confirm-remove-connection" href="#"><?php echo esc_html__( 'Confirm', 'buddyboss' ); ?></a>
					</footer>
				</div>
			</div>
		</div>
	</transition>
</div> <!-- .bb-remove-connection -->

<!-- Remove Connection confirmation popup -->
<div class="bb-view-profile bb-action-popup" style="display:none">
	<transition name="modal">
		<div class="modal-mask bb-white bbm-model-wrap">
			<div class="modal-wrapper">
				<div class="modal-container">
					 
				</div>
			</div>
		</div>
	</transition>
</div> <!-- .bb-remove-connection -->
