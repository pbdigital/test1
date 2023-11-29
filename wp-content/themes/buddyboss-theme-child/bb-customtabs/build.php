<?php 
add_action('wp_enqueue_scripts', function(){
	global $bp;
	$is_member = groups_is_user_member( get_current_user_id(), $bp->groups->current_group->id);
	$member_role = bp_get_user_group_role_title( get_current_user_id(), $bp->groups->current_group->id);

	$is_group_admin = false;
	if($is_member && strtolower($member_role) != "member"){
		$is_group_admin = true;
	}

	$args = [
        "bpGroupId" => bp_get_current_group_id(),
		"classroomId" => groups_get_groupmeta( bp_get_current_group_id() ,"_sync_group_id"),
		"isGroupAdmin"=>$is_group_admin
    ];

	wp_register_script('classroom-awards-js', get_stylesheet_directory_uri() . '/assets/js/classroom-awards.js', '', ENQUEUE_VERSION);
    wp_register_style('classroom-awards-css', get_stylesheet_directory_uri() . '/assets/css/classroom-awards.css', '', ENQUEUE_VERSION);
	wp_localize_script('classroom-awards-js', 'classroomAwardsObj', $args);

}, 9999);



function buddypress_custom_webinar_tab()
{

	// Avoid fatal errors when plugin is not available.
	if (
		!function_exists('bp_core_new_subnav_item') ||
		!function_exists('bp_is_single_item') ||
		!function_exists('bp_is_groups_component') ||
		!function_exists('bp_get_group_permalink') ||
		empty(get_current_user_id())
	) {

		return;
	}


	// Check if we are on group page.
	if (bp_is_groups_component() && bp_is_single_item()) {

		global $bp;

		// Get current group page link.
		$group_link = bp_get_group_permalink($bp->groups->current_group);

		// Tab args.
   
		$tab_args = array(
			'name'                => esc_html__('Leaderboard', 'default'),
			'slug'                => 'group-leaderboard-tab',
			'screen_function'     => 'group_custom_leaderboard_tab_screen',
			'position'            => 20,
			'parent_url'          => $group_link,
			'parent_slug'         => $bp->groups->current_group->slug,
			'default_subnav_slug' => 'group-leaderboard-tab',
			'item_css_id'         => 'group-leaderboard-tab',
		);

		// Add sub-tab leaderboard.
		bp_core_new_subnav_item($tab_args, 'groups');


		/* Show attendance and rewards tab if current logged in user is a Teacher*/
		$is_member = groups_is_user_member( get_current_user_id(), $bp->groups->current_group->id);
		$member_role = bp_get_user_group_role_title( get_current_user_id(), $bp->groups->current_group->id);

		if($is_member && strtolower($member_role) != "member"){
			$tab_args = array(
				'name'                => esc_html__('Attendance', 'default'),
				'slug'                => 'group-attendance-tab',
				'screen_function'     => 'group_custom_attendance_tab_screen',
				'position'            => 21,
				'parent_url'          => $group_link,
				'parent_slug'         => $bp->groups->current_group->slug,
				'default_subnav_slug' => 'group-attendance-tab',
				'item_css_id'         => 'group-attendance-tab',
			);

			// Add sub-tab attendance.
			bp_core_new_subnav_item($tab_args, 'groups');

			// Add sub-tab rewards.
			#bp_core_new_subnav_item($tab_args, 'groups');

			$tab_args = array(
				'name'                => esc_html__('Rewards', 'default'),
				'slug'                => 'group-rewards-tab',
				'screen_function'     => 'group_custom_rewards_tab_screen',
				'position'            => 22,
				'parent_url'          => $group_link,
				'parent_slug'         => $bp->groups->current_group->slug,
				'default_subnav_slug' => 'group-rewards-tab',
				'item_css_id'         => 'group-rewards-tab',
			);

			// Add sub-tab attendance.
			bp_core_new_subnav_item($tab_args, 'groups');
		}

	}
}

add_action('bp_setup_nav', 'buddypress_custom_webinar_tab');

function group_custom_leaderboard_tab_screen() {

	// Add title and content here - last is to call the members plugin.php template.
	add_action( 'bp_template_title', 'custom_leaderboard_tab_title' );
	add_action( 'bp_template_content', 'custom_leaderboard_tab_content' );
	bp_core_load_template( 'buddypress/members/single/plugins' );
}

function group_custom_attendance_tab_screen(){
	add_action( 'bp_template_title', 'custom_attendance_tab_title' );
	add_action( 'bp_template_content', 'custom_attendance_tab_content' );
	bp_core_load_template( 'buddypress/members/single/plugins' );

}

function group_custom_rewards_tab_screen(){
	add_action( 'bp_template_title', 'custom_rewards_tab_title' );
	add_action( 'bp_template_content', 'custom_rewards_tab_content' );
	bp_core_load_template( 'buddypress/members/single/plugins' );
}
 
function custom_leaderboard_tab_title() {
	echo esc_html__( 'Leaderboard', 'default_content' );
}

function custom_attendance_tab_title(){
	echo esc_html__( 'Attendance', 'default_content' );
}

function custom_rewards_tab_title(){
	echo esc_html__( 'Rewards', 'default_content' );
}
 
function custom_leaderboard_tab_content() {
	require_once("leaderboard.php");
}

function custom_attendance_tab_content(){
	require_once("attendance.php");
}

function custom_rewards_tab_content(){
	require_once("reward.php");
}


/* custom user settings sub nav*/
// Add a custom subnav item under Account Settings
add_action( 'wp_ajax_save_profile_gender', function(){
	$user_id = get_current_user_id();
	$gender = $_POST["gender"];
	$current_gender = get_user_meta($user_id,"gender",true);
	update_user_meta($user_id,"gender",$gender);
	
	if($current_gender != $gender) delete_user_meta($user_id, "user_avatar");
	
	echo json_encode(["success"=>true,"uid"=>$user_id]);
	wp_die();
});
function avatar_gender_settings() {
    $page_title = 'Gender';
    $page_slug = 'gender';

    // Check if BuddyPress is active
    if (function_exists('bp_core_new_subnav_item')) {
        bp_core_new_subnav_item(array(
            'name'            => $page_title,
            'slug'            => $page_slug,
            'parent_url'      => bp_loggedin_user_domain() . 'settings/',
            'parent_slug'     => 'settings',
            'position'        => 30, // Adjust the position as needed
            'secondary' => 1,
            'screen_function' => 'custom_page_screen',
        ));
    }
}

// Callback function to display content on the custom page
function custom_page_screen() {
    add_action('bp_template_title', 'custom_page_gender_title');
    add_action('bp_template_content', 'custom_page_gender_content');
    bp_core_load_template('buddypress/members/single/plugins');
}

function custom_page_gender_title() {
    //echo 'Custom Page Title';
}

function custom_page_gender_content() {
    require_once("profile-gender.php");
}

// Hook into BuddyPress initialization to add the custom page
add_action('bp_init', 'avatar_gender_settings');
/* end custom user settings sub nav*/
?>