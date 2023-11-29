<?php
function get_user() {
	$user_id = pb_auth_user($request->get_header('Authorization'));;
	return 'test';
}

function app_login_user(WP_REST_Request $request) {
	$username = $request->get_param('username');
	$password = $request->get_param('password');
	$rememberme = $request->get_param('rememberme') ? true : false;
	$user = wp_authenticate( $username, $password );

	if(is_wp_error($user)) {
        return new WP_REST_Response('Username or Password Incorrect', 403);
    } else {
		$credentials = [
			'user_login' => $username,
			'user_password' => $password,
			'rememberme' => true,
		];
		wp_signon($credentials, true);
	    $response['token'] = bin2hex(random_bytes(64));
		$response['user_id'] = $user->data->ID ;
	    $response['user_email'] = $user->data->user_email ;
	    $response['user_nicename'] = $user->data->user_nicename ;
	    $response['user_display_name'] = $user->data->display_name ;
	    $response['avatar'] = get_avatar_url($user->data->ID );
		$response['rememberme'] = $rememberme;
	    update_user_meta( $user->data->ID , 'mobile_app_token', $response['token'] );
		 
    }
	$response = new WP_REST_Response($response);
	return $response;
}

function reset_user() {

}





function test(WP_REST_Request $request) {
	return $request->get_param('course_id');
}


function pb_auth_user ($token) {
	global $wpdb;
	$token = preg_replace('/Bearer /', '', $token);
	if (empty($token)){
		$user_cookie = (wp_parse_auth_cookie( '', 'logged_in' ));
		$user = get_user_by( 'email', $user_cookie['username'] );
		if (isset( $user->data->ID)){
			
			return $user->data->ID;
		} else {
			return false;
		}
	}
	$user = $wpdb->get_row( "SELECT * FROM $wpdb->usermeta WHERE meta_value = '$token'" );
	if (isset($user)){
		wp_set_current_user( $user->user_id );
		return $user->user_id;
	} else {
		return false;
	}
}




function get_courses(WP_REST_Request $request) {
	global $wpdb;
	$user_id = pb_auth_user($request->get_header('Authorization'));;
	if ($user_id == false) {
		return new WP_REST_Response('Unauthorized', 401);
	}
	//Get all courses
	$all_courses = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'sfwd-courses' AND post_status = 'publish' Order BY menu_order asc", OBJECT);

	$all_courses_count = count($all_courses);

	//Get Course Progress
	$user_meta = get_user_meta($user_id);
	$course_progress = unserialize($user_meta["_sfwd-course_progress"][0]);

	$response = array();
	foreach ($all_courses as $course) {
		$course_id = $course->ID;
		$completed = 0;
		$total = 0;
		$percentage = 0;
		if (isset($course_progress[$course_id])) {
			$completed = $course_progress[$course_id]['completed'];
			$total = $course_progress[$course_id]['total'];
			$percentage = round(($completed / $total) * 100, 0);
		}
		#$show_in_app = get_field('show_in_app',$course->ID);
		//Does the user have access to it?
		#if (!memb_hasPostAccess( $course_id )){
		#	$show_in_app = false;
		#}
	
		$url = get_the_post_thumbnail_url($course->ID);
		#if (!empty($url) && $show_in_app) {
		$response[] = array(
			'title' => $course->post_title,
			'post_title' => $course->post_title,
			'id' => $course->ID,
			'percent_complete' => $percentage,
			'thumbnail' => get_the_post_thumbnail_url($course->ID,'full')
		);
		#} else {

		#}
	}
	$response = new WP_REST_Response($response);
	return $response;

}
function get_single_course(WP_REST_Request $request) {
	global $wpdb;
	$user_id = pb_auth_user($request->get_header('Authorization'));;
	if ($user_id == false) {
		return new WP_REST_Response('Unauthorized', 401);
	}
	$course_id = $request->get_param('id');

	$user_meta = get_user_meta($user_id);
	$course_progress = unserialize($user_meta["_sfwd-course_progress"][0]);

	$lessons = learndash_get_lesson_list($course_id);
	
	$i = 0;
	foreach ($lessons as $lesson)
	{
		
		if (memb_hasPostAccess( $lesson->ID,705996 )){	
			
		
			$percent_completed=0;
			$lesson_topics[$i]['id'] = $lesson->ID;
			$lesson_topics[$i]['title'] = $lesson->post_title;
			$lesson_topics[$i]['subtitle'] = ' ';
			$lesson_topics[$i]['video_url'] = get_field('video_url', $lesson->ID);
			$lesson_topics[$i]['thumbnail'] = get_the_post_thumbnail_url($lesson->ID,'full');
			$lesson_topics[$i]['progress'] = $percent_completed;
			$lesson_topics[$i]['duration'] = get_field('video_duration', $lesson->ID);
	
			
			$post = get_post($lesson->ID);
			$lesson_topics[$i]['text_html'] = $post->post_content;
			$lesson_topics[$i]['text_plain'] = nl2br(strip_tags($post->post_content));
	
			$multi_tiered = false;
			if ($course_progress[$course_id]['lessons'][$lesson->ID]) {
				$lesson_topics[$i]['completed'] = true;
			} else {
				$lesson_topics[$i]['completed'] = false;
			}
	
			$topics = learndash_topic_dots($lesson->ID, false, 'array');
			if (!empty($topics)) {
				$topic_count = 0;
				$topic_completed_count = 0;
				
				foreach ($topics as $topic) {
					$multi_tiered = true;
					$completed = false;
					if ($course_progress[$course_id]['topics'][$lesson->ID][$topic->ID]) {
						$completed = true;
						$topic_completed_count++;
					}
	
					$post = get_post($topic->ID);
					$lesson_topics[$i]['topics'][] = array(
						'id' => $topic->ID, 
						'title' => $topic->post_title, 
						'completed' => $completed,
						'text_html' => $post->post_content,
						'text_plain' => nl2br(strip_tags($post->post_content)),
						'subtitle' => null,
						'thumbnail' => get_the_post_thumbnail_url($topic->ID,'full'),
						'video_url' => get_field('video_url', $topic->ID),
						'progress' => null,
						'duration' => get_field('video_duration', $topic->ID)
					);
					$topic_count++;
	
				}
				//remove this after making progress actually dynamic
				$lesson_topics[$i]['duration'] = null;
				$percent_completed = round($topic_completed_count / $topic_count * 100);
				$lesson_topics[$i]['progress'] = $percent_completed;
				
			}
		
			$i++;
		}
	}
	
	
	$course  = get_post($course_id);
	$percentage   = 0;
	
	if (isset($course_progress[$course_id])) {
		$completed = $course_progress[$course_id]['completed'];
		$total = $course_progress[$course_id]['total'];
		
		$percentage = round(($completed / $total) * 100, 0);
	}
	
	$return = array();
	$return[] = array(
		'image' => get_the_post_thumbnail_url($course_id,'full'),
		'title' => $course->post_title,
		'subtitle' => ' ', 
		'progress' => $percentage , //make dynamic
		'description' => 'Course description for '.$course->post_title,
		'multi_tiered' => $multi_tiered,
		'lessons' => $lesson_topics,
		
	);

	$response = new WP_REST_Response($return);
	return $response;

}


function get_course_lesson( WP_REST_Request $request) {
	$user_id = pb_auth_user($request->get_header('Authorization'));;
	if ($user_id == false) {
		return new WP_REST_Response('Unauthorized', 401);
	}
	$lesson_id = $request->get_param('lesson_id');
	$post = get_post($lesson_id);
	
	$video_url = "https://raw.githubusercontent.com/mediaelement/mediaelement-files/master/big_buck_bunny.mp4";
	$return = array();
	$return['title'] = $post->post_title;
	$return['completed'] = learndash_is_lesson_complete($user_id,$lesson_id);
	$return['video_url'] = $video_url;
	$return['text_html'] = $post->post_content;
	$return['text_plain'] = nl2br(strip_tags($post->post_content));


	$course = learndash_get_course_progress($user_id,$lesson_id);
	$return['prev_lesson_id'] = $course['prev']->ID;
	$return['next_lesson_id'] = $course['next']->ID;
	
	$response = new WP_REST_Response($return);
	return $response;
}

function lesson_markcomplete( WP_REST_Request $request ) {
	$user_id = pb_auth_user($request->get_header('Authorization'));;
	if ($user_id == false) {
		return new WP_REST_Response('Unauthorized', 401);
	}
	$lesson_id = $request->get_param('id');
	$result = learndash_process_mark_complete($user_id, $lesson_id);

	$response = new WP_REST_Response($result);
	
	return $response;
}


function get_current_user_data(){
	global $wpdb;
	$user_id      		   = pb_auth_user($request->get_header('Authorization'));;
	$current_user		   = wp_get_current_user(); 
	$response["user_meta"] = get_user_meta($user_id);
	$response["avatar"]    = get_avatar_url( $current_user->user_email, ["size"=>150]);

	if(strpos($response["avatar"], "gravatar.com") > 0 ) $response["avatar"] = "http:".$response["avatar"];

	return $response;
}

function app_reset_user(WP_REST_Request $request) {
	
	$user_login = $request->get_param('email');
	$process = retrieve_password( $user_login );
	if(!isset($process->errors)){
		$response = array('success' => true, 'message' => 'Please check your email to reset your password');
	}
	else{
		$response = $process;
	}
	

	$response = new WP_REST_Response($response);
	return $response;

}

function app_register_user(WP_REST_Request $request) {
	
	$user_fname = $request->get_param('user_fname');
	$user_lname = $request->get_param('user_lname');
	$user_email = $request->get_param('user_email');
	$user_dob = $request->get_param('user_dob');
	$user_password = $request->get_param('user_password');

	$error = false;
    if ( !username_exists( $user_email ) && email_exists($user_email) == false ) {
        $user_id = wp_create_user( $user_email, $user_password, $user_email );
        if( !is_wp_error($user_id) ) {
            $user = get_user_by( 'id', $user_id );
            $user->set_role( 'subscriber' );
			wp_update_user([
				'ID' => $user_id, // this is the ID of the user you want to update.
				'first_name' => $user_fname,
				'last_name' => $user_lname,
			]);
			add_user_meta($user_id, "birthday", $user_dob );

			$response = get_user_meta( $user_id );
        }
		else{
			$error = true;
		}
    }
	else{
		$error = true;
	}

	if($error){
		$response = array('success' => false);
	}
	

	$response = new WP_REST_Response($response);
	return $response;

}
