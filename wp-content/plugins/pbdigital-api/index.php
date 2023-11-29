<?php
/*
Plugin Name: PB Digital - Mobile App API
Plugin URI: https://pbdigital.com.au
Description: Handles API Stuff In Wordpress For Mobile App
Version: 1.00
Author: PB Digital
Author URI: https://pbdigital.com.au
*/
require_once("safar.php");
require_once("safar-user.php");
require_once("safar-leaderboard.php");
require_once("safar-dashboard.php");
require_once("safar-courses.php");
require_once("safar-avatar-store.php");
require_once("callbacks.php");
require_once("safar-school.php");
require_once("safar-family.php");
require_once("safar-publications.php");
require_once("safar-rewards.php");
require_once("safar-attendance.php");
#require_once('gpm-learning-tracks.php');

/*******
PB DIGITAL APP TEMPLATE WP REST API ENDPOINTS
*******/


/*******
Users
*******/

#POST		/users/login
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/users/login', array(
    'methods' => 'POST',
    'callback' => 'app_login_user',
  ) );
} );



#POST		/users/reset
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/users/reset_password', array(
    'methods' => 'POST',
    'callback' => 'app_reset_user',
  ) );
} );

#POST		/users/register
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/users/register', array(
    'methods' => 'POST',
    'callback' => 'app_register_user',
  ) );
} );

/*******
Courses
*******/
 




#GET		/courses/:course_id
/*
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/courses/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_single_course',
  ) );
} );
*/


#GET		/courses/:course_id/lessons/:lesson_id/
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/courses/(?P<id>\d+)/lessons/(?P<lesson_id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_course_lesson',
  ) );
} );


/*******
Lessons
*******/


#POST		/lessons/:lesson_id/markComplete
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/lessons/(?P<id>\d+)/markComplete', array(
    'methods' => 'POST',
    'callback' => 'lesson_markcomplete',
  ) );
} );

#GET		/get_current_user_data
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/get_current_user_data', array(
    'methods' => 'GET',
    'callback' => 'get_current_user_data',
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/stats', array(
    'methods' => 'GET',
    'callback' => 'user_stats',
  ) );
} );

/*******
CUSTOM ENDPOINTS SPECIFIC FOR CLIENT
*******/


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarUser','get_user_info'],
  ) );
} );

#GET 		/users/:id
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarUser','get_user_info'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/leaderboard/points/(?P<gid>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarLeaderboard','get_points_leaderboard'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/leaderboard/practice_tracker/(?P<gid>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarLeaderboard','get_practice_tracker_leaderboard'],
  ) );
} );


// dashboard
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/dashboard', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarDashboard','get_dashboard'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/dashboard/goals', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarDashboard','get_goals'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/dashboard/recent_activities', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarDashboard','get_recent_activities'],
  ) );
} );


//
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/subjects', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarCourses','get_subjects'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/collection/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarCourses','get_collection'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/courses/categories', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarCourses','get_categories'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/search_posts', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarCourses','get_searched_posts'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/practice_tracker/log', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarUser','log_practice'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/practice_tracker/logs', array(
    'methods' => 'get',
    'callback' => ['Safar\SafarUser','practice_logs'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/quiz/share_result', array(
    'methods' => 'post',
    'callback' => ['Safar\SafarUser','share_result'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/award-points', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarUser','app_award_points'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/quranic_animals', array(
    'methods' => 'get',
    'callback' => ['Safar\SafarUser','get_user_quranic_animals'],
  ) );
} );



add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/quiz/leaderboard/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarLeaderboard','quiz_leaderboard'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/course/pathway/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarCourses','get_course_pathway'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/quranic_animal/award', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarUser','award_quranic_animal'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/quranic_animal/award', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarUser','get_pending_awarded_quranic_animal'],
  ) );
} );





// Avatar Store
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/avatar', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarAvatarStore','save_avatar'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/gears', array(
    'methods' => 'get',
    'callback' => ['Safar\SafarAvatarStore','get_gears_by_type'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/gears', array(
    'methods' => 'get',
    'callback' => ['Safar\SafarAvatarStore','get_user_gears'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/unequip_gear', array(
    'methods' => 'post',
    'callback' => ['Safar\SafarAvatarStore','user_unequip_gear'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/equip_gear', array(
    'methods' => 'post',
    'callback' => ['Safar\SafarAvatarStore','user_equip_gear'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/purchase/gear', array(
    'methods' => 'post',
    'callback' => ['Safar\SafarAvatarStore','user_purchase_gear'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/achievements/badges', array(
    'methods' => 'get',
    'callback' => ['Safar\SafarUser','get_user_badges'],
  ) );
} );


/* School Admin API endpoints*/
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/admin/teachers/import_csv', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','import_csv'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/upload_logo', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','upload_logo'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/send_test_email', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','send_test_email'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/save_welcome_email', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','save_institute_welcome_email'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/save_activity_feed', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarSchool','save_activity_feed'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/preview_email', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','preview_email'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/family', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarFamily','create_institute_family'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/family/(?P<id>\d+)', array(
    'methods' => 'DELETE',
    'callback' => ['Safar\SafarFamily','delete_institute_family'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/families/(?P<id>\d+)/remove_child', array(
    'methods' => 'DELETE',
    'callback' => ['Safar\SafarFamily','delete_institute_family_child'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/family/(?P<id>\d+)', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarFamily','update_institute_family'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/family/(?P<id>\d+)/child', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarFamily','add_child_institute_family'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/families/import_csv', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarFamily','import_institute_family_csv'],
  ) );
} );
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/school/families/import', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarFamily','import_institute_family'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/school', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarSchool','get_school_details'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/school/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarSchool','get_single_classroom'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/school/update_logo', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarSchool','update_logo'],
  ) );
} );
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/school/save_institute_information', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarSchool','save_institute_information'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/institute/(?P<id>\d+)/admins', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarSchool','update_school_admins'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/(?P<id>\d+)', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarSchool','update_classroom_details'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/(?P<id>\d+)', array(
    'methods' => 'DELETE',
    'callback' => ['Safar\SafarSchool','archive_classroom'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/(?P<id>\d+)/teacher', array(
    'methods' => 'DELETE',
    'callback' => ['Safar\SafarSchool','remove_teacher'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/(?P<id>\d+)/admin', array(
    'methods' => 'DELETE',
    'callback' => ['Safar\SafarSchool','remove_admin'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/(?P<id>\d+)/student', array(
    'methods' => 'DELETE',
    'callback' => ['Safar\SafarSchool','remove_student'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/user/(?P<id>\d+)', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarSchool','update_user'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/user/select_institute', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarSchool','user_select_institute'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','create_new_classroom'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/upload', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','upload'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/manage-classrooms/upload_csv', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','upload_csv'],
  ) );
} );

/* Endpoints For Safar Publications */
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/family', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarFamily','submit_family_details'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/institute', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','submit_institute_details'],
  ) );
} );
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/institute/update_admin_password', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarSchool','update_admin_password'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/family/subscription/status', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarFamily','update_family_subscription_status'],
  ) );
} );



add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/family/order/(?P<id>\d+)/login_link', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarFamily','get_login_link_by_order'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/family/child', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarFamily','create_child_account'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/family/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarFamily','api_family_details'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/family/family_update_parent_password', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarFamily','family_update_parent_password'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/password', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarUser','update_user_password'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/groups/family/(?P<gid>\d+)/child/(?P<childid>\d+)', array(
    'methods' => 'DELETE',
    'callback' => ['Safar\SafarFamily','delete_child_account'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/user/sandbox', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarUser','create_sandbox'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/users/login_logs', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarUser','login_logs'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/users/login_logs/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarUser','login_logs_details'],
  ) );
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/users/practice_logs', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarUser','practice_logs_report'],
  ) );
} );
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/users/practice_logs/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarUser','practice_logs_report_details'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/tests/gift_subscription_emails', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarUser','test_gift_subscription_emails'],
  ) );
} );


/* Rewards Endpoints */
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/rewards/achievements', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarRewards','get_rewards_achievemets'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/rewards/history', array(
    'methods' => 'GET',
    'callback' => ['Safar\SafarRewards','get_rewards'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/rewards/(?P<classroom_id>\d+)', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarRewards','save_rewards'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/attendance/(?P<classroom_id>\d+)', array(
    'methods' => 'POST',
    'callback' => ['Safar\SafarAttendance','save_attendance'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/rewards/clear', array(
    'methods' => 'PUT',
    'callback' => ['Safar\SafarRewards','clear_rewards'],
  ) );
} );

add_action( 'rest_api_init', function () {
  register_rest_route( 'api', '/attendance_report', array(
    'methods' => 'get',
    'callback' => ['Safar\SafarAttendance','get_attendance_report'],
  ) );
} );

/* end endpoints*/

add_action("init", function(){
    if(isset($_GET["demologin"])){
      $orderid = $_GET["orderid"];
      $user_id = DEMO_USER_ID;
      if(!empty($user_id)){
        if(isset($_GET["redirect"])) $url = $_GET["redirect"];
        else $url = site_url();
        wp_set_auth_cookie($user_id);
        wp_redirect($url);
      }
      die();
    }
});



add_action("init", function(){
  if(isset($_GET["safarautologin"])){
    $orderid = $_GET["orderid"];
    $login_info = \Safar\SafarFamily::get_login_info_by_order_id($orderid);
    if(!empty($login_info["user_id"])){

      $safar_publications_institute_user_id = get_post_meta($login_info["group_id"],"safar_publications_institute_user_id",true);
      wp_set_auth_cookie($login_info["user_id"]);

      if(!empty($safar_publications_institute_user_id)) wp_redirect(site_url("school-onboarding?gid=".$login_info["group_id"]));
      else wp_redirect(site_url("family-onboarding?gid=".$login_info["group_id"]));
    }else{
      // check for any active subscriptions and get user details
      if(!empty($orderid)){
        $subscriptions = \Safar\SafarPublications::api_request(["endpoint"=>"/family/order/".$orderid."/subscriptions?"]);
        
        $safar_user_id = 0;
        foreach($subscriptions as $sub){
          if($sub->status == "active"){
            $safar_user_id = $sub->user_id;
            $j2j_user_id = \Safar\SafarFamily::get_login_info_by_safarpub_user_id($safar_user_id);
            if(!empty($j2j_user_id)){
              wp_set_auth_cookie($j2j_user_id);
              
              $safar_publications_institute_user_id = get_user_meta($j2j_user_id,"safar_publications_institute_user_id",true);
              if(!empty($safar_publications_institute_user_id)){
                wp_redirect(site_url("/"));
              }else{
                wp_redirect(site_url("manage-family"));
              }
            }
          }
        }
      }
    }
    die();
  }

  if(isset($_GET["j2j_autologin"])){
    $unique_id = $_GET["unique_id"]; // Replace with the actual unique_id you're searching for
    $meta_key = 'ldc_unique_id';

    // Arguments for querying users based on user meta
    $args = array(
        'meta_key'   => $meta_key,
        'meta_value' => $unique_id,
        'fields'     => 'ID', // Retrieve only user IDs
    );

    // Get user IDs matching the criteria
    $user_ids = get_users($args);

    if(!empty($user_ids)){
      $user_id = $user_ids[0];
      wp_set_auth_cookie($user_id);
      wp_redirect(site_url($_GET["redirect"]));
      exit();
    }
  }
});

add_action("wp_head", function(){
	global $post;

	$has_active_subscription = true;
	$user_id = get_current_user_id();
	$is_user_demo = false;
	
	$group = learndash_get_users_group_ids($user_id);
	if(!empty($group)){
		$group_details = get_post($group[0]);
	
		if( $group_details->post_name == "demo-users"){
			$is_user_demo = true;
			
			// if user belongs to demo user add class to <body>
			add_filter( 'body_class', function( $classes ){
				$classes[] = "demo-user";
				return $classes;
			});

			$allowed_pages = get_field("demo_user_allowed_pages", $group_details->ID);
			$allow_view = false;
	
			$upgrade_page = get_field("upgrade_page","option");

			
			if (strpos($upgrade_page, $post->post_name) === false) {
				foreach($allowed_pages as $eallowed_page){
					//print_r([$eallowed_page["allowed_pages"]->ID, $eallowed_page["allowed_pages"]->post_title]);
					if( $eallowed_page["allowed_pages"]->ID ==  $post->ID ){
					$allow_view = true;
					}
				}

				if(!$allow_view){
				if($post->post_type == "sfwd-topic"){
					$course_id = learndash_get_course_id($post->ID);
					$lesson_id = learndash_course_get_single_parent_step( $course_id, $post->ID );
					$lesson_details = get_post($lesson_id);         
					$allow_view = \Safar\SafarUser::demo_user_has_access($lesson_id);     
				}
				}
			
				if(!$allow_view){
				//echo $post->post_name;
				wp_redirect( $upgrade_page );
				}
			}
		} // end demo users

		$has_active = true;
		$all_status = [];



		// get the parent/admin user_id of the group. Check if there are active subscriptions for the parent
		$parent_user_ids = []; 
		foreach($group as $egroup){
		$subscription_status = get_post_meta($egroup,"subscription_status",true);
		
		$parent_user_ids = learndash_get_groups_administrator_ids($egroup);
		$all_status[] = $subscription_status;
		}
		if(!in_array("active",$all_status)) $has_active = false;
		if(!$has_active){
			$has_active_subscription = false;
		}else{
		$has_active_subscription = true;
		}

		// add to the current users group, groups that the parent is managing.
		// this will be used for checking if the parent still has an active subscription from SafarPub
		if(!empty($parent_user_ids)){
		foreach($parent_user_ids as $parent_user_id){
			$group_as_parent = learndash_get_administrators_group_ids($parent_user_id, true);
			foreach($group_as_parent as $eg_parent){
			if(!in_array($eg_parent, $group)) $group[] = $eg_parent;
			}
		}
		}
		
	}

  

	// check for group ids
	$group_as_admins = learndash_get_administrators_group_ids($user_id, true);
	if($group_as_admins){
		$has_active = true;
		
		$all_status = [];
		foreach($group_as_admins as $group_id){
			$subscription_status = get_post_meta($group_id,"subscription_status",true);
			$all_status[] = $subscription_status;
		}

		if(!in_array("active",$all_status)) $has_active = false;

		if(!$has_active){
			$has_active_subscription = false;
		}else{
			$has_active_subscription = true;
		}

		
	}

  
	if(!empty($user_id)){ // redirects for non demo users

		// allow access if user is a group leader but not a parent
		$is_parent = \Safar\SafarFamily::is_user_parent( );
		if(!$is_parent){
		if(!empty($group_as_admins)) $has_active_subscription = true;
		}
		


		// this is for the children
		if(!$has_active_subscription){

		// check from SafarPub API if subscription has not updated
		foreach($group_as_admins as $eg_admin){
			$group[] = $eg_admin;
		}

    // fix for https://app.clickup.com/t/865d4hzrf
    // if user is a student only include groups where the student belong
    if(!$is_parent && !$is_institute_admin){
      $group = learndash_get_users_group_ids($user_id, true);
    }
    

		if(!empty($group)){
			$order_ids = [];
			foreach($group as $gid){
			$order_id = get_post_meta($gid, "order_id", true);
			if(!empty($order_id)) $order_ids[] = $order_id;
			if(!empty($order_id)){
				$subscriptions = \Safar\SafarPublications::api_request(["endpoint"=>"/family/order/".$order_id."/subscriptions?".mt_rand()]);
				foreach($subscriptions as $subscription){
					if($subscription->status == "active" || $subscription->status == "pending-cancel"){
					$has_active_subscription = true;
					update_post_meta($gid,"subscription_status","active");
					}
				}
			}
			}
		}
		
		// if there are no order ids, this tells that the user/child doesn't belong to any family with subscription
		// so allow access
		if(empty($order_ids)) $has_active_subscription = true;
		
		}

	
		// allow access if user is administrator or teacher
		if ( current_user_can( 'administrator' ) || \Safar\SafarUser::is_user_teacher( ) ) {
			$has_active_subscription = true;
		}

		
		if(!$has_active_subscription){

			$ld_group_ids = learndash_get_users_group_ids( get_current_user_id() );

			$all_status = [];
			$is_institute_admin = false;
			foreach($ld_group_ids as $ld_gid){
				$group_admin = learndash_get_groups_administrator_ids($ld_gid);
				foreach($group_admin as $eg_admin){
					$safar_publications_institute_user_id = get_user_meta( $eg_admin,"safar_publications_institute_user_id", true);
					if(!empty($safar_publications_institute_user_id)) $is_institute_admin = true;
				}
				
			}

			// if the LD group admin is an Institute leader
			// members/students are part of the institute
			if(!empty($is_institute_admin)){
				
				if(!$has_active_subscription){
					if ( $post->post_name != "no-access-institute" && $post->post_name != "choose-avatar" ) {
						wp_redirect( site_url("no-access-institute") );
					}
				}
			}else{

				// for family members

				if ( $post->post_name != "no-access-family" && $post->post_name != "choose-avatar") {
					wp_redirect( site_url("no-access-family") );
				
				} 
			}
		}

		/* 
		Start: instititute members access checks
		This section is for Institute USERS not parent users
		1. Check if user has safar_publications_institute_user_id
		2. check if the LD group has an active subscription
		3. IF not redirect to no access page
		*/
		$safar_publications_institute_user_id = get_user_meta( get_current_user_id(),"safar_publications_institute_user_id", true);

		if(!empty($safar_publications_institute_user_id)){
			$institutes = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );
			
			$all_status = [];
			foreach($institutes as $institute){
				$subscription_status = get_post_meta($institute->ID,"subscription_status",true);
				$all_status[] = $subscription_status;
			}

			if(!in_array("active",$all_status)) $has_active = false;

			if(!$has_active){
				$has_active_subscription = false;
			}else{
				$has_active_subscription = true;
			}

			if(!$has_active_subscription){
				if ( $post->post_name != "no-access-institute") {
					wp_redirect( site_url("no-access-institute") );
				} 
			}

		} 
		/* END instititute members access checks */
	}
},1);

add_action( 'parse_query', 'reset_demo_user' );
add_action( 'init', function(){
  if ( isset( $_GET['action'] ) && $_GET['action'] === 'reset_demo_user' ) {
      require_once(ABSPATH.'wp-admin/includes/user.php');

      $demousers = learndash_get_groups_user_ids( DEMO_USER_GROUP_ID , true );
      foreach($demousers as $user_id){
        wp_delete_user( $user_id , '');
      }
      
      die();
  }
},999);
function reset_demo_user( $query ) {
 


  if ( isset( $_GET['action'] ) && $_GET['action'] === 'safarpublications_sso_login' ) {
      $user_id = get_current_user_id();
			$safar_publications_user_id = get_user_meta($user_id, "safar_publications_user_id", true);
      if(empty($safar_publications_user_id)){
        $safar_publications_user_id = get_user_meta($user_id, "safar_publications_institute_user_id", true);
      }

      $sso_login = \Safar\SafarPublications::api_request(["endpoint"=>"users/".$safar_publications_user_id."/login_url/?uuid=".rand()]);
     
      if( !empty($sso_login->user_found)){
          $url = $sso_login->sso_login_url;
      }else{
          $url = site_url("404");
      }
      wp_redirect($url);
      exit();

  }

  if ( isset( $_GET['action'] ) && $_GET['action'] === 'login_as_child' ) {
      $key = $_GET["key"];
      $current_user_id = get_current_user_id();
      if(empty($current_user_id)) die();
      global $wpdb;
      $user = $wpdb->get_results("SELECT ID FROM ".$wpdb->prefix."users WHERE md5(ID)='".esc_sql($key)."' ");
      $user_id = $user[0]->ID;
      wp_set_auth_cookie($user_id);
      wp_redirect(home_url());
      exit();

  }

}


if(isset($_GET["testpbd"])){
    error_reporting(E_ALL);
    ini_set("display_errors",1);
    /*$user_id = 9381;
    $family_groups = \Safar\SafarFamily::get_family_groups_by_user_id($user_id);

    echo "<pre>";
      print_r($family_groups);

      foreach($family_groups as $egroup){
        print_r([$egroup->ID]);
      }
    echo "</pre>";*/
  
  
}
/* 
start upload attendance to google sheet
*/
add_action( 'wp_ajax_nopriv_generate_attendance_google_sheet', 'generate_attendance_google_sheet');
add_action( 'wp_ajax_generate_attendance_google_sheet', 'generate_attendance_google_sheet');

function generate_attendance_google_sheet(){
  
  require(WP_PLUGIN_DIR."/pbdigital-api/vendor/autoload.php");
  global $wpdb;
  $sql = "
    SELECT d.attendance_id, a.classroom_id, a.teacher_id, d.user_id, d.status, a.date_time 
      FROM `".$wpdb->prefix."attendance_details` as d 
      INNER JOIN `".$wpdb->prefix."attendance` as a ON d.attendance_id = a.id
    ORDER BY a.date_time DESC 
  ";

  
  $client = new Google_Client();
  $client->setAuthConfig(WP_PLUGIN_DIR.'/pbdigital-api/j2j-attendance-sheet-3dcbc8e56216.json');
  $client->addScope(Google_Service_Sheets::SPREADSHEETS);

  $rs = $wpdb->get_results($sql);
  $spreadheet[] = [
    "Institute",
    "Student Username",	
    "family id",	
    "student first name",	
    "student last name",	
    "class",	
    "class subject",	
    "teacher",
    "date", 
    "Attendance"
  ];

  foreach($rs as $r){
    $student = get_user_by("id", $r->user_id);
    $classroom = get_post($r->classroom_id);
    $classroom_category = wp_get_post_terms( $r->classroom_id, "ld_group_category");
    $r->classroom_subject = "";
    if(!empty($classroom_category)){
      $r->classroom_subject = $classroom_category[0]->name;
    }

    $teacher = get_user_by("id",$r->teacher_id);

    $student_username = $student->user_login;
    $student_first_name = get_user_meta($r->user_id,"first_name",true);
    $student_last_name = get_user_meta($r->user_id,"last_name",true);

    $class = $classroom->post_title;
    $teacher = $teacher->data->display_name;
    
    $family_id = "";
    $parent_post_id = wp_get_post_parent_id( $r->classroom_id );
    $institute = get_post($parent_post_id);

    /* 
    Safar Slough
    Safar Finchley
    Safar Enfield
    Safar Edmonton
    Safar Academy Harlow
    */
    $report_institute_ids = [220056, 220050, 219973, 220052, 220054];

    $utcDateTime = new DateTime($r->date_time, new DateTimeZone('UTC'));
    $ukTimeZone = new DateTimeZone('Europe/London');
    $utcDateTime->setTimezone($ukTimeZone);
    $ukDateTime = $utcDateTime->format('Y-m-d H:i:s');

    if(in_array($parent_post_id, $report_institute_ids)){
      $spreadheet[] = [
        ($institute->post_title) ? $institute->post_title:" ",
        ($student_username) ? $student_username:" ",
        ($family_id) ? $family_id:" ",
        ($student_first_name) ? $student_first_name:" ",
        ($student_last_name) ? $student_last_name:" ",
        ($class) ? $class:" ",
        ($r->classroom_subject) ? $r->classroom_subject:" ",
        ($teacher) ? $teacher:" ",
        ($r->date_time) ? $ukDateTime:" ",
        ($r->status) ? $r->status:" "
      ];
    } 

     
  }

   
  $service = new Google_Service_Sheets($client);
  $spreadsheetId = GOOGLESHEET_ATTENDANCE_ID;
  $range = 'Sheet1'; // The name of the sheet, no specific range

  $body = new Google_Service_Sheets_ValueRange([
      'values' => $spreadheet
  ]);

  $params = [
      'valueInputOption' => 'RAW'
  ];

  try {
      $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
      printf("Updated %d cells.\n", $result->getUpdatedCells());
  } catch (Google_Service_Exception $e) {
      print_r($e->getMessage());
  }

}
/* end upload attendance to google sheet*/

function custom_handle_cover_image_upload($bpGroupId, $j, $cover_url, $y) {
  // Your custom code to handle the cover image upload/update goes here
  $classroom_id = \Safar\SafarSchool::get_learndash_group_id_from_bp_group_id($bpGroupId);
  //update_field("class_cover_photo",$cover_url, $classroom_id);
  update_post_meta($classroom_id, "class_cover_photo", $cover_url);
  //print_r([$cover_url,$classroom_id]);
  //print_r(["bpGroupId"=>$bpGroupId, "j"=>$j, "cover_url"=>$cover_url, "y"=>$y, "classroom_id"=>$classroom_id]);
}

add_action('groups_cover_image_uploaded', 'custom_handle_cover_image_upload',10, 4);

function custom_handle_groups_avatar_uploaded($groupId,$y,$avatar){
  $avatar_url = $avatar["avatar"];
  $classroom_id = \Safar\SafarSchool::get_learndash_group_id_from_bp_group_id($groupId);
  update_post_meta($classroom_id,"class_avatar",$avatar_url);
}

add_action('groups_avatar_uploaded','custom_handle_groups_avatar_uploaded',10,3);