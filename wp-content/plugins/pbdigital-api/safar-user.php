<?php 
namespace Safar;

use DOMDocument;
use Buddyboss\LearndashIntegration\Library\SyncGenerator;
use Safar\SafarSchool;
use Safar\SafarRewards;

class SafarUser extends Safar{
    static $btc_prefix = "btc_user_";
    static $current_user_data = [];
    static $max_practice_log = 25;
    static $user_tz = "";

    static function get_max_practice_log(){
        return self::$max_practice_log;
    }

	static function get_user_info( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $user_info = get_user_by("id", $user_id);
            $classroomid = 0;
            $disabledachievements = false;
            if(!empty($request)){
                $id = $request->get_param("id");
                $disabledachievements = filter_var($request->get_param("disabledachievements"),FILTER_VALIDATE_BOOLEAN);
                $classroomid = $request->get_param("classroomid");
            }

        
            if(!empty($id)) $user_id = $id;
        
            $response = get_user_by("id", $user_id)->data;
            $response->first_name = get_user_meta($user_id, "first_name", true);
            $response->last_name = get_user_meta($user_id, "last_name", true);
            

            $avatar_url = get_avatar_url($user_id);
            $custom_avatar = get_user_meta($user_id, "custom_avatar_url", true );
            if(!empty($custom_avatar)){
                $avatar_url = $custom_avatar."?".uniqid();
            }

            $response->avatar_full = get_user_meta($user_id, "custom_avatar_full", true );
            $response->uniqid = uniqid();
            $response->avatar = $avatar_url;
            $response->user_profile_edit = bp_core_get_user_domain($user_id)."profile/edit";
            $response->watched_welcome_video = get_user_meta($user_id, "watched_welcome_video", true);
            $response->username = $user_info->data->user_login;
            $response->city = xprofile_get_field_data(44, $user_id);
            $response->state = xprofile_get_field_data(46, $user_id);
            $response->country = xprofile_get_field_data(45, $user_id );

            $response->done_gets_started = ( !empty(get_user_meta($user_id, "done_gets_started", true)) ) ? true:false;


            $bp_profile_completion_widgets = get_user_meta($user_id, "bp_profile_completion_widgets", true );
         
            if(empty($bp_profile_completion_widgets)){
                $total_steps = 0;
                $steps_completed = 0;
            }else{
                $total_steps = $bp_profile_completion_widgets["total_fields"];
                $steps_completed = $bp_profile_completion_widgets["completed_fields"];
            }
            $response->profile_completion_progress = ["completed"=> ($steps_completed >= $total_steps) ? true:false , "steps_completed"=> $steps_completed, "total_steps" => $total_steps];

            
            if($disabledachievements){
                $response->all_badges = [];
            }else{

                $achievements = [ "earned_pet_accessories"=>"pet-accessory", "earned_accessories"=>"accessory"];

                foreach($achievements as $key=>$achievement){
                    $earned_badges = gamipress_get_user_achievements( array(
                        'user_id'           => $user_id,
                        'achievement_type'  => $achievement,
                        'display'           => true
                    ) );

                    $badges = [];
                    if(!empty($earned_badges)){
                        foreach($earned_badges as $badge){
                            $badges[] = [ 
                                "id" => $badge->ID,
                                "title" => $badge->title,
                                "date_earned" => date("Y-m-d H:i:s", $badge->date_earned),
                                "image" => get_the_post_thumbnail_url($badge->ID)
                            ];
                        }
                    }
                    $response->$key = $badges;
                }
                
                $rs_badges = get_posts(["post_type"=>"badges", "numberposts"=>-1, "post_status"=>"publish"]);
                /*$all_badges = [];

                $earned_badges = gamipress_get_user_earned_achievement_ids( $user_id, 'badges' );
                
                foreach($rs_badges as $badge){
                    $earned = false;
                    if(in_array($badge->ID, $earned_badges)) $earned = true;
                    $all_badges[] = [ "image" => get_the_post_thumbnail_url($badge->ID), 
                                    "badge_description" => get_post_meta($badge->ID, "badge_description", true),
                                    "badge" => $badge->post_title,
                                    "inactive_image" => wp_get_attachment_image_url(get_post_meta($badge->ID, "inactive_image", true)),
                                    "earned" => $earned
                                    ];
                }*/
                $response->all_badges = self::get_user_badges($request)->data;//$all_badges;
            }

            //
            

            $response->points = gamipress_get_user_points( $user_id, "points");
            $response->coins = gamipress_get_user_points( $user_id, "coin");
            $response->practice_stats = 0;

            $response->groups = [];

            $groups = groups_get_user_groups($user_id);
            //groups_get_group
            $group_details = [];
            if(!empty($groups)){
                foreach($groups["groups"] as $group){
                    $group_object = groups_get_group($group);
                    $group_object->url = bp_get_group_permalink($group_object);
                    if(!empty($group_object->id)) $group_details[] = $group_object;
                }
            }
            $response->groups = $group_details;



            $notifications = bp_notifications_get_grouped_notifications_for_user( $user_id );
            
            $notification_count = 0;
            foreach($notifications as $enotif){
                $notification_count += $enotif->total_count;
            }

            $response->notifications["count"] = $notification_count;
            $response->notifications["all"] = $notifications;

            $response->practice_streak = 0;
            $practice_logs = self::practice_logs( $request );
            $response->practice_logs_count = count($practice_logs->data);
            $response->practice_logs = $practice_logs;

            /*
            $streak = 0;
            $prev_date = false;

            $practice_logs->data = array_reverse($practice_logs->data);

            foreach($practice_logs->data as $elog){
                $date = $elog["date"];

                if(!empty($prev_date)){
                    $H = date("H",strtotime($prev_date) );
                    $i = date("i",strtotime($prev_date) );
                    $s = date("s",strtotime($prev_date) );
                    $n = date("n",strtotime($prev_date) );
                    $j = date("j",strtotime($prev_date) );
                    $Y = date("Y",strtotime($prev_date) );
                    $prev_date = mktime($H, $i, $s, $n, $j + 1, $Y);
                    if(date("Y-m-d",$prev_date) == date("Y-m-d",strtotime($date)) ){
                        $streak++;
                    }else{
                        $streak = 0;
                    }
                }

                $prev_date = $date;
            }
            $response->practice_streak = $streak;*/
            $response->practice_streak = self::calculate_streak(array_reverse($practice_logs->data));

            $current_count = $response->practice_logs_count;
            $max_count = self::get_max_practice_log();

            $response->practice_count_div = 0;
            if($current_count >= $max_count ){
                $div = floor(  $current_count / $max_count );
                $current_count = $current_count - ( $div * $max_count );

                $response->practice_count_div = $div;

                $multiplier = $max_count * $div;
                $current_count = $current_count + $multiplier;
                $max_count = ( $div + 1 ) * $max_count;
            }

            $response->practice_sessions_left = $max_count-$current_count;

            $achieved_quranic_animal = gamipress_get_user_earned_achievement_ids($user_id, "quranic-animal");

            $response->latest_quranic_animal = false;
            if(!empty($achieved_quranic_animal)){
                $quranic_animal = get_post($achieved_quranic_animal[0]);
                $response->latest_quranic_animal = ["animal"=>$quranic_animal->post_title, 
                                                    "image" => get_field("unlocked_image", $achieved_quranic_animal[0])["url"],
                                                    "description" => $quranic_animal->post_excerpt  ];
            }

            // quranic animal
            $rs_qa = get_posts(["post_type"=>"quranic-animal", "numberposts"=>-1, "post_status"=>"publish"]);
            $all_quranic_animals = [];            
            $num = 0;
            foreach($rs_qa as $qa){
                $earned = false;
                $num++;
                if(in_array($qa->ID, $achieved_quranic_animal)) $earned = true;
                $all_quranic_animals[] = [ "image" => get_field("unlocked_image", $qa->ID)["url"], 
                                  "description" => $qa->post_excerpt,
                                  "title" => $qa->post_title,
                                  "inactive_image" =>get_post_meta($qa->ID, "inactive_image", true),
                                  "earned" => $earned,
                                  "num" => $num
                                ];
            }

            $response->all_quranic_animals = $all_quranic_animals;
            
            $response->gender = get_user_meta($user_id, "gender", true);

            $user_avatar = get_user_meta($user_id, "user_avatar", true);
            
            if(!empty($user_avatar["group"])) $response->age_group = $user_avatar["group"];
            else $response->age_group = false;

            $response->avatar_selected = get_user_meta($user_id, "avatar_selected", true);
            $response->user_avatar = get_user_meta($user_id, "user_avatar", true);

            $response->is_parent = \Safar\SafarFamily::is_user_parent( );
            if($response->is_parent){
                $institutes = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );

                $trial_end_date = "";
                $is_trial_subscription = false;
                $response->has_added_payment_method =  false;

                foreach($institutes as $ins){
                    if( get_user_meta( $user_id, "selected_institute", true ) == $ins->ID ){
                        $order_id = get_post_meta($ins->ID, "order_id", true);
                        $subscription_status = get_post_meta($ins->ID,"subscription_status", true);

                        $trial_end_date = get_post_meta($ins->ID,"trial_end_date",true);
                        $is_trial_subscription = get_post_meta($ins->ID,"is_trial_subscription",true);

                        $arr_payment = get_post_meta($ins->ID,"has_added_payment_method",true);
                    
                        $response->has_added_payment_method = ( ($arr_payment[0] == "yes") ? true:false);

                        $response->subscriptions = [
                            "group_id" => $ins->ID,
                            "is_trial_subscription" => get_post_meta($ins->ID,"is_trial_subscription",true),
                            "trial_start_date" => get_post_meta($ins->ID,"trial_start_date",true),
                            "trial_end_date" => $trial_end_date,
                            "next_payment_date" => get_post_meta($ins->ID,"next_payment_date",true),
                            "has_added_payment_method" => $response->has_added_payment_method,
                        ];
                    }
                }

                /* 
                // Check first if user is a parent and has subscription

                Modal  = Day 4 , 5, 6 (Displayed Once only)
                Option 4 = Day 7 (Triggered via automation)
                Option 3 = Day 10 - 14 (See fathom RE: turning more red as they approach day 14)
                */

                if( !empty($is_trial_subscription )){
                    
                    #$trial_end_date = '2023-07-03'; // Replace with your trial end date
                    $current_date = date('Y-m-d'); // Get the current date

                    $trial_end_timestamp = strtotime($trial_end_date); // Convert trial end date to a timestamp
                    $current_timestamp = strtotime($current_date); // Convert current date to a timestamp

                    $seconds_left = $trial_end_timestamp - $current_timestamp; // Calculate the difference in seconds

                    $days_left = floor($seconds_left / (60 * 60 * 24)); // Convert seconds to days

                    if($days_left < 0 ) $days_left = 0;

                    $response->days_count = 14 - $days_left;
                    $response->days_left = $days_left;
                
                    $response->add_payment_notification = false;
                    $response->notification_type = "";

                    $response->trial_end_date = $trial_end_date;

                    $response->has_bb_payment_notification = false;

                    if( $response->days_count >= 4 ){
                        $response->add_payment_notification = true;
                        if( $response->days_count >= 4 && $response->days_count <=6 ){
                            $response->notification_type = "modal";
                        }

                        if( $response->days_count >= 7 && $response->days_count <= 9 ){
                            $response->notification_type = "buddyboss"; // option 4
                            
                            $notifications = bp_notifications_get_notifications_for_user( get_current_user_id(), "object" );
                            
                            if(!empty($notifications)){
                                if( $notifications->component_name == "payment_notification"){
                                    $has_payment_notification = true;
                                } 
                            }
                            $notifications = bp_notifications_get_grouped_notifications_for_user( get_current_user_id() );
                            if(!empty($notifications)){
                                foreach($notifications as $notification){
                                    if( $notification->component_name == "payment_notification"){
                                        $response->has_bb_payment_notification = true;
                                    } 
                                }
                            }
                            if(!$response->has_bb_payment_notification ){
                            
                                $post_data = array(
                                    'post_title'    => 'Add payment details',
                                    'post_content'  => 'To continue your seamless learning experience after the trial period, you must add your payment details.',
                                    'post_status'   => 'publish',
                                    'post_type'     => 'payment_notification', // Replace with your custom post type slug
                                );
                                
                                // Insert the post
                                $item_id = wp_insert_post( $post_data );
                                update_post_meta($item_id,"target_user_id", $user_id);
                                update_post_meta($item_id,"url", site_url("action=safarpublications_sso_login"));

                                bp_notifications_add_notification( array(
                                    'user_id'           => $user_id,
                                    'item_id'           => $item_id,
                                    'component_name'    => 'payment_notification',
                                    'component_action'  => 'payment_notification_action',
                                    'date_notified'     => bp_core_current_time(),
                                    'is_new'            => 1,
                                ) );

                            }
                        }

                        if( $response->days_count >= 10 ){
                            $response->notification_type = "header"; // option 3
                        }

                        if( $response->notification_type == "buddyboss"){
                            $notifications = bp_notifications_get_grouped_notifications_for_user( get_current_user_id() );
                            $response->buddyboss_notifications = [];
                            if(!empty($notifications)){
                                foreach($notifications as $notification){
                                    if( $notification->component_name == "payment_notification"){

                                        $post = get_post( $notification->item_id );
                                        $response->buddyboss_notifications[] = [
                                            "title" => $post->post_title,
                                            "content" => $post->post_content,
                                            "url" => get_post_meta($post->ID, "url", true),
                                            "id" => $post->ID
                                        ];

                                    } 
                                }
                            }
                        }
                    }

                }
            }

            $response->rewards = SafarRewards::get_rewards_history_by("student", $user_id, $classroomid); 
            $response->is_institute_student_user = self::is_institute_student_user($user_id);
            
            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function calculate_streak($practice_logs, $user_id=0){
        $streak = 0;
        $prev_date = false;
        $has_yesterday = false;
        foreach($practice_logs as $elog){
            $date = $elog["date"];

            if(!empty($prev_date)){
                $H = date("H",strtotime($prev_date) );
                $i = date("i",strtotime($prev_date) );
                $s = date("s",strtotime($prev_date) );
                $n = date("n",strtotime($prev_date) );
                $j = date("j",strtotime($prev_date) );
                $Y = date("Y",strtotime($prev_date) );
                $prev_date = mktime($H, $i, $s, $n, $j + 1, $Y);

                if(date("Y-m-d",$prev_date) == date("Y-m-d",strtotime($date)) ){
                    if($streak==0) $streak = 1; // this is to include first date into the streak
                    $streak++;
                }else{
                    $streak = 0;
                }
                
            }

            $prev_date = $date;

            if(date("Y-m-d", strtotime("-1 day")) == date("Y-m-d",strtotime($date)) ){ // check if user has practiced yesterday
                $has_yesterday = true;
            }
        }

        if(!$has_yesterday) $streak = 0; // if user don't have log yesterday, streak will be 0
        
        return $streak;
    }

    static function calculate_highest_streak($practice_logs){
        $streak = 0;
        $prev_date = false;
        $highest_streak = 0;
        
        $test = [];
        $dates = [];
   

        foreach($practice_logs as $elog){
            $date = $elog["date"];

            if(!empty($prev_date)){
                $H = date("H",strtotime($prev_date) );
                $i = date("i",strtotime($prev_date) );
                $s = date("s",strtotime($prev_date) );
                $n = date("n",strtotime($prev_date) );
                $j = date("j",strtotime($prev_date) );
                $Y = date("Y",strtotime($prev_date) );
                $prev_date = mktime($H, $i, $s, $n, $j + 1, $Y);

                if(date("Y-m-d",$prev_date) == date("Y-m-d",strtotime($date)) ){

                    if($streak==0) $streak = 1; // this is to include first date into the streak

                    $streak++;

                    if($streak > $highest_streak) $highest_streak = $streak;

                    $dates[] = ["prev"=>$prev_date, "date"=>$date];

                }else{
                    $streak = 0;
                }
            }

            $prev_date = $date;
        }

       # print_r(["streak"=>$streak, "dates"=>$dates, "pl"=>$practice_logs]);

       if(!empty($practice_logs) && empty($highest_streak)){
            // default highest streak to 1 if there is a log
            $highest_streak = 1;
        }

        return $highest_streak;
    }

    static function calculate_highest_streak_login($practice_logs, $user_id){
        $streak = 0;
        $prev_date = false;
        $highest_streak = 0;
        
        $test = [];
        $dates = [];

        
        foreach($practice_logs as $elog){
            $date = $elog["date"];
            
            if(!empty($prev_date)){
                $H = date("H",strtotime($prev_date) );
                $i = date("i",strtotime($prev_date) );
                $s = date("s",strtotime($prev_date) );
                $n = date("n",strtotime($prev_date) );
                $j = date("j",strtotime($prev_date) );
                $Y = date("Y",strtotime($prev_date) );
                $prev_date = mktime($H, $i, $s, $n, $j + 1, $Y);

                
                if(date("Y-m-d",$prev_date) == date("Y-m-d",strtotime($date)) ){

                    if($streak==0) $streak = 1; // this is to include first date into the streak

                    $streak++;
                    
                    if($streak > $highest_streak) $highest_streak = $streak;

                    $dates[] = ["prev"=> date("Y-m-d",$prev_date) , "date"=> date("Y-m-d",strtotime($date)), $streak, $highest_streak ];
                    
                }else{
                    $streak = 0;
                    
                }
            }

            $prev_date = $date;
        }

        if(!empty($practice_logs) && empty($highest_streak)){
            // default highest streak to 1 if there is a log
            $highest_streak = 1;
        }
        #print_r(["test"=>$practice_logs]);

        return $highest_streak;
    }


    static function convert_date($to_tz, $date, $from_tz = ""){
        $orig_tz = date_default_timezone_get();

        if(empty($from_tz)) $from_tz = date_default_timezone_get();
        date_default_timezone_set($from_tz);

        $datetime = new \DateTime( $date );
        $la_time = new \DateTimeZone( $to_tz );
        $datetime->setTimezone($la_time);

        date_default_timezone_set($orig_tz);

        return $datetime->format('Y-m-d H:i:s');
    }

    static function log_practice( $request ){
        
        $user_id = parent::pb_auth_user($request);
        
       
        if(!empty($user_id)){
            $minutes = $request->get_param("minutes");
            $date = date("Y-m-d H:i:s",strtotime($request->get_param("date"))); // store date based on server's tz

            $tz = $request->get_param("tz");

            $converted_date_time = self::convert_date($tz, $date);  

            $converted_tz_from_date = date("Y-m-d 00:00:00",strtotime($converted_date_time));
            $converted_tz_to_date = date("Y-m-d 23:59:59",strtotime($converted_date_time));

            // convert based tz dates to utc so we can pass it to the SQL
            $server_tz = date_default_timezone_get();
            $converted_utc_from_date = self::convert_date($server_tz, $converted_tz_from_date, $tz);  
            $converted_utc_to_date = self::convert_date($server_tz, $converted_tz_to_date, $tz);  
 
            // update meta when exists
            $sql = "SELECT * FROM `".parent::wpdb()->prefix."gamipress_user_earnings` as e
                LEFT OUTER JOIN `".parent::wpdb()->prefix."gamipress_user_earnings_meta` as em ON e.user_earning_id = em.user_earning_id
                
                WHERE post_id = ".GAMIPRESS_EARNING_POST_ID." 
                    AND ( 
                            e.date >= '$converted_utc_from_date' AND e.date <= '$converted_utc_to_date '
                        )
                AND user_id = ".$user_id;
           
            $rs_exists = parent::wpdb()->get_results($sql);

            $response = [];
            
            if(!empty($rs_exists)){
                $response["update_result"] = parent::wpdb()->query("UPDATE `".parent::wpdb()->prefix."gamipress_user_earnings_meta` SET meta_value='".esc_sql($minutes)."' WHERE meta_id=".$rs_exists[0]->meta_id." LIMIT 1 ");
                $response["log_type"] = "update";
            }else{
                gamipress_insert_user_earning( get_current_user_id(), 
                            ["post_id"=>GAMIPRESS_EARNING_POST_ID, "date"=>$date],
                            ["minutes"=>$minutes]
                        );
                $response["log_type"] = "add";

                gamipress_trigger_event( array(
                    'event' => 'logged_practice_session',
                    'user_id' => get_current_user_id()
                ) );
                
            }
            $rs_logs = self::practice_logs([]);
            $logs = $rs_logs->data;
            $max_count = self::get_max_practice_log();
            $current_count = count($logs);

            if($current_count >= $max_count ){
                $div = floor(  $current_count / $max_count );
                $current_count = $current_count - ( $div * $max_count );
            }

            
            $response["current_count"] = $current_count;
            $response["max_count"] = $max_count;
            $response["count_left"] = $max_count - $current_count;
            $response["all_logs"] = count( self::practice_logs([])->data );

            $response["award_spin"] = false;
            if( $response["all_logs"] > 0 ){
                //echo $response["current_count"] % $response["max_count"];
            
                if( $response["all_logs"] % $response["max_count"] == 0 ){
                    //delete_user_meta($user_id, "award_quranic_spin_".$response["all_logs"] ); // delete after testing 
                    $award_spin_meta = get_user_meta($user_id, "award_quranic_spin_".$response["all_logs"], true );
                    if(empty($award_spin_meta)){
                        $response["award_spin"] = true;
                        $response["award_type"] = $response["all_logs"];
                        update_user_meta($user_id, "award_quranic_spin_".$response["all_logs"], ["ts"=>date("Y-m-d H:i:s"), "type" => $response["all_logs"], "current_count"=>$current_count, "count_left"=>$response["count_left"] ]);
                        $response["award_quranic_spin_meta_key"] = $award_spin_meta;
                    }
                }
            }
            
            $response = new \WP_REST_Response($response);
            return $response;
        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }

    }

    static function practice_logs( $request ){
        $user_id = parent::pb_auth_user($request);
        if(!empty($request)) $id = $request->get_param("id");
        if(!empty($id)) $user_id = $id;

      
        if(!empty($user_id)){
            $last_3_months = date("Y-m-d 00:00:00",strtotime("-3 months"));
            $sql = "SELECT * FROM `".parent::wpdb()->prefix."gamipress_user_earnings` as e
            LEFT OUTER JOIN `".parent::wpdb()->prefix."gamipress_user_earnings_meta` as em ON e.user_earning_id = em.user_earning_id
            
            WHERE post_id = ".PRACTICE_STREAK_POST_ID." 
                AND user_id = ".$user_id." 
                /*AND e.date >= '".$last_3_months."'*/
                GROUP BY date_format(e.date, '%Y-%m-%d')
                ORDER BY e.date DESC 
            
            ";

            $currentIP = $_SERVER['REMOTE_ADDR'];
            $desiredIP = '49.149.108.137';
             
            $rs_logs = parent::wpdb()->get_results($sql);

            $response = [];
            foreach($rs_logs as $log){
                $response[] = [
                    "date" => $log->date,
                    "minutes" => $log->meta_value,
                    "earning_id" => $log->user_earning_id
                ];
            }
            
            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }

    }

    static function share_result( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){

            $course_id = $request->get_param("course_id");
            $question_count = $request->get_param("question_count");
            $correct_answers = $request->get_param("correct_answers");
            $total_points = $request->get_param("total_points");
            $quiz_id = $request->get_param("quiz_id");

            $user_ld_group_ids = learndash_get_users_group_ids($user_id);
            $user_bp_group_ids = [];
            // if order id is present, meaning this group is a family group therefore should be included on the share result
            foreach($user_ld_group_ids as $ugid){
                $ld_group_order_id = get_post_meta($ugid,"order_id",true);
                if(!empty($ld_group_order_id)){
                    
                    $generator = new SyncGenerator( "", $ugid );
                    $bpGroupId = $generator->getBpGroupId();

                    $user_bp_group_ids[] = $bpGroupId;
                }
            }

            // 1. Get the LD Group of the course
            // 2. Get the ld_group_category of the course
            // 3. Get buddyboss group ids who have subject the same as the LD group

            $ld_group = learndash_get_course_groups($course_id); // #1

            gamipress_trigger_event( array(
                // Mandatory data, the event triggered and the user ID to be awarded
                'event' => 'share_quiz_results_custom_event',
                'user_id' => get_current_user_id()
                // Also, you can add any extra parameters you want
                // They will be passed too on any hook inside the GamiPress awards engine
                // 'date' => date( 'Y-m-d H:i:s' ),
                // 'custom_param' => 'custom_value',
            ) );

            $shared_to_groups = [];
            $shared_to_group_ids = [];

            if(!empty($ld_group)){

                foreach($ld_group as $e_ld_group){

                    $ld_group_id = $e_ld_group;
                    $ld_group_details = get_post($ld_group_id);
                    
                    $ld_course_term = wp_get_post_terms( $ld_group_id, "ld_group_category");

                    $ld_course_term_id = [];
                    if(!empty($ld_course_term)){
                        foreach($ld_course_term as $eterm){
                            $ld_course_term_id[] = $eterm->term_id;
                        }

                    } 
                    if(!empty($ld_course_term_id)){

                        $rs_bb_groups_ids = parent::wpdb()->get_results("SELECT * FROM `".parent::wpdb()->prefix."bp_groups_groupmeta` 
                                                        WHERE meta_key='group_subject_id' 
                                                            AND meta_value in (".implode(",",$ld_course_term_id).") ");
                        $bb_group_ids_subjects = [];
                        if(!empty($rs_bb_groups_ids)){
                            foreach($rs_bb_groups_ids as $r){
                                $bb_group_ids_subjects[] = $r->group_id;
                            }
                        }
                    

                        $groups = groups_get_user_groups($user_id);
                                                
                        //groups_get_group
                        $group_details = [];
                        if(!empty($groups)){
                            foreach($groups["groups"] as $group){
                                $group_object = groups_get_group($group);
                                
                                $allow_share = false;
                                if(in_array($group_object->id, $user_bp_group_ids)){ // family buddyboss group ids
                                    $allow_share = true;
                                    
                                }
                                if(in_array($group, $bb_group_ids_subjects)){
                                    $allow_share = true;
                                }
                                
             
                                if($allow_share){
                                   
                                    if(!in_array($group_object->id,$shared_to_group_ids)){
                                        $content = get_field("share_quiz_result_message", "option");
                                        $content = str_replace("{question_count}", $question_count, $content );
                                        $content = str_replace("{correct_answers_count}", $correct_answers, $content );
                                        $content = str_replace("{total_points}", $total_points, $content );

                                        $post_quiz = get_post($quiz_id);
                                        $quiz_name = "<a href='".get_permalink($post_quiz)."'>".$post_quiz->post_title."</a>";

                                        $content = str_replace("{quiz_name}", $quiz_name, $content );

                                        $args = [
                                            "content" => $content,
                                            "user_id" => $user_id, 
                                            "group_id" => $group_object->id
                                        ];
                                        $result = groups_post_update($args);

                                        $shared_to_groups[] = [
                                            "group_name" => $group_object->name,
                                            "group_link" => bp_get_group_permalink($group_object)
                                        ];
                                        $shared_to_group_ids[] = $group_object->id;
                                    }
                                }
                                
                                
                            }
                        }
                    }
                }
            }



            /* 
            $sql = "SELECT DISTINCT post_id, menu_order FROM ".parent::wpdb()->prefix."postmeta  as pm
                        INNER JOIN ".parent::wpdb()->prefix."posts as p ON pm.post_id = p.ID
                        WHERE meta_key='learndash_group_enrolled_".$ld_group->ID."'
                        ORDER BY menu_order ASC 
                ";
            */

            $response["success"] = (!empty($shared_to_groups) ) ? true:false;
            $response["shared_to_groups"] = $shared_to_groups;
            $response["user_groups"] = $groups;
            $response["bb_group_ids_subjects"] = $bb_group_ids_subjects;
            $response["user_bp_group_ids"] = $user_bp_group_ids;
            $response["allow_share"] = $allow_share;
            $response["ld_group"] = $ld_group;
            $response["groups"] = $groups["groups"];
            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function app_award_points ( $request ) {
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $type = $request->get_param('type');
        $amount = $request->get_param('amount');
        $quiz_id = $request->get_param("quiz_id");
        $highest_streak = $request->get_param("streak");
        $response = array();
        $response["streak"] = parent::wpdb()->insert(parent::wpdb()->prefix."quiz_streaks", ["user_id"=>$user_id,"quiz_id"=>$quiz_id,"streak"=>$highest_streak] );

        $args = [];
        if(!empty($quiz_id)){
            $quiz = get_post($quiz_id);
            $quiz_title = $quiz->post_title;

            $args["title"] = "completed quiz - ".$quiz_title;
            $args["points_type"] = $type;
            $args["points"] = $amount;
            $args["post_type"] = "points-award";
            $args["post_id"] = 218561; // WP-admin/Achievements/ Quiz Point

            $meta["quiz_id"] = $quiz_id;
            $meta["reason"] = "completed quiz";
            $meta["completed_quiz_key"] = "completed-quiz-".$quiz_id;
            
            gamipress_insert_user_earning($user_id, $args, $meta); // use this function to add points and record user
        }

        gamipress_award_points_to_user ($user_id, $amount, $type, $args);

        $balance = gamipress_get_user_points($user_id, $type);

        
        //$response = pbd_get_gamification_stats($user_id);
        $response["success"] = true;
        $response["balance"] = $balance;
    
        $response = new \WP_REST_Response($response);
        return $response;
    }


    static function award_quranic_animal($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $type = $request->get_param("type");
        $awarded = false;

        $award_spin_meta = get_user_meta($user_id, "award_quranic_spin_".$type, true );

        if(!empty($award_spin_meta)){ // check if user has unclaimed spin for the type
            $allow_award = false;
            
            if($type == "admin"){ // admin awarded spins can be done multiple types, delete user meta after awarding
                $allow_award = true;
                
            }else{
                // regular spins for 25, 50, 75, etc can only be done once, so do not delete user_meta, update width achievement_awarded and the achievement_id instead

                if(empty($award_spin_meta["achievement_awarded"])){ // do not award the achievement again, only once
                  $allow_award = true;
                }
            }

            if($allow_award){
                
                $award_spin_meta["achievement_awarded"] = true;
                // update user meta with achievemnt_awarded and the achievement id
                $award_spin_meta["achievement_id"] = $achievement_id;


                // do the random awarding of gamipress achievement
                // do not include the achievements already achieved by the user
                // post_type=quranic-animal

                $achieved_quranic_animal = gamipress_get_user_earned_achievement_ids($user_id, "quranic-animal");
                if(empty($achieved_quranic_animal)) $achieved_quranic_animal = [];

                $quranic_animals = gamipress_get_achievements(["post_type"=>"quranic-animal", "exclude"=>$achieved_quranic_animal]);

                $max = sizeof($quranic_animals) - 1;
                $min = 0;
                $rand_key = rand($min, $max);

                if($rand_key !== false){
                    $quranic_animal_to_award = $quranic_animals[$rand_key];

                    if(!empty($quranic_animals[$rand_key])){

                        $quranic_animal_id = $quranic_animals[$rand_key]->ID;
                        gamipress_award_achievement_to_user($quranic_animal_id, $user_id);
                        $awarded = true;
                        $response["post_excerpt"] = $quranic_animals[$rand_key]->post_excerpt;
                        $response["post_title"] = $quranic_animals[$rand_key]->post_title;
                        //$response["post_content"] = strip_tags($quranic_animals[$rand_key]->post_content);
                        $response["post_content"] =  get_post_meta($quranic_animals[$rand_key]->ID,"_gamipress_congratulations_text",true);
                        $response["image"] = get_field("unlocked_image", $quranic_animal_id)["url"];
                        $response["ID"] = $quranic_animals[$rand_key]->ID;


                        $quranic_animals = gamipress_get_achievements(["post_type"=>"quranic-animal", "orderby"=>"menu_order, post_title", "order" => "asc"]);

                        $i = 0;
                        foreach($quranic_animals as $q){
                            $i++;
                            if($quranic_animal_id == $q->ID){
                                $response["animal_position"] = $i;
                            }
                        }
                
                    }

                }


                if($type != "admin"){
                    update_user_meta($user_id, "award_quranic_spin_".$type, $award_spin_meta);

                    $user_ld_group_ids = learndash_get_users_group_ids($user_id);
                    $user_bp_group_ids = [];                
                    $fullname = ucwords(get_user_meta($user_id, "first_name", true)." ".get_user_meta($user_id,"last_name", true));

                    foreach($user_ld_group_ids as $ugid){
                        
                        $generator = new SyncGenerator( "", $ugid );
                        $bpGroupId = $generator->getBpGroupId();

                        $user_bp_group_ids[] = $bpGroupId;

                        $content = "<span  class='activity-quranc-animal'>
                                    ".$fullname." has completed ".$type." reads, and has unlocked a new Quranic Animal - a ".$response["post_title"]." 
                                        <img style='width:70px' src='".$response["image"]."'/> 
                                    </span>";

                        $args = [
                            "content" => $content,
                            "user_id" => $user_id, 
                            "group_id" => $bpGroupId
                        ];
                        $result = groups_post_update($args);
                        
                    }
                    

                }else{
                    $award_spin_meta = get_user_meta($user_id, "award_quranic_spin_admin", true );
                    $user_earning_id = $award_spin_meta["user_earning_id"];
                    
                    gamipress_update_user_earning_meta( $user_earning_id, "awarded", date("Y-m-d H:i:s"));
                    gamipress_update_user_earning_meta( $user_earning_id, "awarded_quranic_animal_title", $response["post_title"]  );
                    gamipress_update_user_earning_meta( $user_earning_id, "awarded_quranic_animal_id", $quranic_animal_id  );
                    
                    
                    
                    delete_user_meta($user_id, "award_quranic_spin_".$type);
                }
            }
        }


        

        $response["awarded"] = $awarded;
        $response["type"] = $type;
        $response["user_meta"] = get_user_meta($user_id, "award_quranic_spin_".$type, true );
        $response = new \WP_REST_Response($response);
        return $response;
    }
    
    static function get_user_quranic_animals( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $instituteparent = $request->get_param("instituteparent");
        if(!empty($instituteparent)){
            $childid = $request->get_param("childid");
            update_user_meta($user_id,"institute_parent_selected_child", $childid);

            $user_id = $childid;
        }

        $achieved_quranic_animal = gamipress_get_user_earned_achievement_ids($user_id, "quranic-animal");
        $quranic_animals = gamipress_get_achievements(["post_type"=>"quranic-animal", "orderby"=>"menu_order, post_title", "order" => "asc"]);

        foreach($quranic_animals as $k=>$animal){
            if(in_array($animal->ID, $achieved_quranic_animal)){
                $quranic_animals[$k]->unlocked = true;
            }else{
                $quranic_animals[$k]->unlocked = false;
            }

            $quranic_animals[$k]->unlocked_image = get_field("unlocked_image", $animal->ID)["url"];
            $quranic_animals[$k]->locked_image = get_field("locked_image", $animal->ID)["url"];

        }

        $response["quranic_animals"] = $quranic_animals;
        $response = new \WP_REST_Response($response);
        return $response;
    }
    

    static function admin_awarded_quranic_animal_spins( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        // Mystery Box – Manually Awarded -> 219340
        $sql = " 
            SELECT ue.*, 
                ( SELECT meta_value FROM ".parent::wpdb()->prefix."gamipress_user_earnings_meta as uem 
                    WHERE ue.user_earning_id = uem.user_earning_id 
                        AND uem.meta_key = 'awarded'
                ) as awarded
            FROM ".parent::wpdb()->prefix."gamipress_user_earnings as ue

            
            WHERE post_id = 219340
            
            AND user_id = ".$user_id."
            
            HAVING awarded IS NULL
        ";

        $admin_awarded_earnings = parent::wpdb()->get_results($sql);

        //$award_spin_meta = get_user_meta($user_id, "award_quranic_spin_".$type, true );
        foreach($admin_awarded_earnings as $earning){
            $award_spin_meta = get_user_meta($user_id, "award_quranic_spin_admin", true );
            if(empty($award_spin_meta)){ // only award 1 spin at a time
                add_user_meta($user_id, "award_quranic_spin_admin", ["user_earning_id"=>$earning->user_earning_id]);
            }
        }

        $response["success"] = true;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function clear_quranic_animal_awards_log( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $practice_logs = self::practice_logs([]);
        $total_practice_count = count( $practice_logs->data);

        $x = floor( $total_practice_count / self::$max_practice_log ) ; 

        for($i=0; $i < $x; $i++){
            $type = ($i + 1) * self::$max_practice_log;
            $award_meta = delete_user_meta($user_id, "award_quranic_spin_".$type );
        }

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function get_pending_awarded_quranic_animal( $request ){

        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        // check admin award
        $award_spin_meta = get_user_meta($user_id, "award_quranic_spin_admin", true );

        $response["show_spin"] = false;
        $response["type"] = "";
        if($award_spin_meta){
            $response["show_spin"] = true;
            $response["type"] = "admin";
        }else{
            // check 25, 50, 75, but get total user logs 
            
            $practice_logs = self::practice_logs([]);
            $total_practice_count = count( $practice_logs->data);
            $response["total_practice_logs"] = $total_practice_count;

            $x = floor( $total_practice_count / self::$max_practice_log ) ; 

            for($i=0; $i < $x; $i++){
                /* 
                $award_spin_meta["achievement_awarded"] = true;
                // update user meta with achievemnt_awarded and the achievement id
                $award_spin_meta["achievement_id"] = $achievement_id;

                */
                $type = ($i + 1) * self::$max_practice_log;
                $award_meta = get_user_meta($user_id, "award_quranic_spin_".$type, true );

                if(!empty($award_meta)){

                    if(empty( $award_meta["achievement_awarded"] )){ // check if this is already awarded, do not show spin if already is
                        $response["show_spin"] = true;
                        $response["type"] = $type;
                    }
                }

            }

        }
        
        $response = new \WP_REST_Response($response);
        return $response;
    }


    static function get_user_badges( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        if(!empty($request)){
            $id = $request->get_param("id");
            
            $instituteparent = $request->get_param("instituteparent");
            $childid = $request->get_param("childid");

            if(!empty($childid)){
                $id = $childid;
                update_user_meta($user_id,"institute_parent_selected_child", $childid);
            }
        }

        if(!empty($id)) $user_id = $id;



        $rs_badges = get_posts(["post_type"=>"badges", "numberposts"=>-1, "post_status"=>"publish", "order"=>"asc"]);
        $all_badges = [];

        $earned_badges = gamipress_get_user_earned_achievement_ids( $user_id, 'badges' );

        $practice_logs = self::practice_logs( $request );
        $practice_streak = self::calculate_streak(array_reverse($practice_logs->data));
      
        foreach($rs_badges as $badge){
            $earned = false;
            if(in_array($badge->ID, $earned_badges)) $earned = true;

            $required_achievements = gamipress_get_required_achievements_for_achievement( $badge->ID, 'publish' );
            $num_requirements_earned = 0;
            $num_requirements_left = 0;

            $total_requirements = 0;

            $requirement_rules = [];
            
            $progress = [];
            $progress["current"] = 0;
            $progress["total"] = 1;

            if ( is_array( $required_achievements ) && ! empty( $required_achievements ) ) {
                
                $current = 0;
                $total = 0;
                foreach ( $required_achievements as $requirement ) {
    
                    $requirement_earned = gamipress_get_user_achievements( array(
                        'user_id' => $user_id,
                        'achievement_id' => $requirement->ID,
                        'since' => gamipress_achievement_last_user_activity( $badge->ID, $user_id ) - 1
                    ) );

                    $requirement_rules[] = $requirement_earned;
    
                    // Has the user already earned the requirements?
                    if ( empty( $requirement_earned ) ) {
                        $num_requirements_left++;
                    }else{
                        $num_requirements_earned++;
                    }

                    $total_requirements++;

                    $req_prog = gamipress_progress_get_requirement_progress($requirement->ID, $user_id);

                    $current += $req_prog["current"];
                    $total += $req_prog["total"];
                }

                $progress["current"] = $current;
                $progress["total"] = ($total) ? $total:1;
    
            }


            if($earned){
                $progress["current"] = $progress["total"];
            }

            $show = true;
            // only show badges if it meets prerequisite achievement
            // or if it has no required achievement to complete first
            $required_achievement = get_field("required_achievement", $badge->ID);
            if($required_achievement){
                $show = false;
                if(in_array($required_achievement->ID, $earned_badges)){
                    $show = true;
                }
            }            

            // Badges with custom award, these are badges that can't be done on gamipress triggers
            // therfore needs to be done manually if the target streak has achieved
            $custom_award = get_field("custom_award", $badge->ID);
            $custom_requirement = [];
            if($custom_award){
                $custom_requirement["target_streak"] = get_field("target_streak",$badge->ID);
                $custom_requirement["current_streak"] = 0;

                $progress["total"] = $custom_requirement["target_streak"];
                $progress["current"] = $custom_requirement["current_streak"];

                switch(strtolower($badge->post_title)){
                    case "practise streak": case "perfect month":
                        $progress["current"] = $practice_streak;
                        $custom_requirement["current_streak"] = $practice_streak;

                        if($practice_streak >= $custom_requirement["target_streak"]){
                            if(!$earned) gamipress_award_achievement_to_user( $badge->ID, $user_id);
                            $earned = true;
                        }
                            
                        break;

                    case "on a roll":

                            $last_7_days = date("Y-m-d H:i:s",strtotime("-6 days"));
                            $login_streak = 0;
                            for($x=0; $x < 7; $x++){
                                
                                $H = date("H",strtotime($last_7_days));
                                $i = date("i",strtotime($last_7_days));
                                $s = date("s",strtotime($last_7_days));
                                $n = date("n",strtotime($last_7_days));
                                $j = date("j",strtotime($last_7_days));
                                $Y = date("Y",strtotime($last_7_days));
                                $date_compare = date("Y-m-d",mktime($H, $i, $s, $n, $j+$x, $Y) );
                                
                                $logs = parent::wpdb()->get_results("SELECT * FROM ".parent::wpdb()->prefix."gamipress_logs 
                                    WHERE user_id=".$user_id." AND trigger_type='gamipress_login'
                                        AND date_format(`date`, '%Y-%m-%d') = '".$date_compare."'
                                    ORDER BY `date` DESC  
                                    LIMIT 1
                                ");
                                if(empty($logs)) $login_streak = 0;
                                else $login_streak++;
                            }

                            $progress["current"] = $login_streak;
                            $custom_requirement["current_streak"] = $login_streak;

                            if($login_streak >= $custom_requirement["target_streak"]){
                                if(!$earned) gamipress_award_achievement_to_user( $badge->ID, $user_id);
                                $earned = true;
                            }

                        break;

                }
            }

            
            $all_badges[] = [   
                    "badge" => $badge->post_title,
                            "badge_description" => get_post_meta($badge->ID, "badge_description", true),
                            "inactive_image" => wp_get_attachment_image_url(get_post_meta($badge->ID, "inactive_image", true)),
                            "earned" => $earned,
                            "image" => get_the_post_thumbnail_url($badge->ID), 
                            // "required_achievements" => $required_achievements,
                            "progress" => $progress,
                            "progess_percent" => ( empty($total) ) ? ($earned) ? 100:0: ( ($progress["current"] / $progress["total"]) * 100 ),
                            "required_achievement" => $required_achievement,
                            "custom_award" => ($custom_award[0]=="yes") ? true:false,
                            "custom_requirement" => $custom_requirement,
                            "badge_number_color" => get_field("badge_number_color", $badge->ID),
                            "show" => $show,
                            "user_id" => $user_id
                                
            ];
            
        }
        
        $response = new \WP_REST_Response($all_badges);
        return $response;
        
    }

    static function create_sandbox( $request ){
        // Define the user data.
        $user_data = array(
            'user_login' => 'demo_' . wp_generate_password( 4, false ),
            'user_pass'  => wp_generate_password( 12, true ),
            'user_email' => 'demo_' . wp_generate_password( 4, false ) . '@journey2jannah.com',
            'role'       => 'subscriber'
        );

        $timezone = $request->get_param("tz");
        $admin_users = get_users( array( 'role' => 'administrator' ) );
        $admin_id = $admin_users[0]->data->ID;
        $redirect = $request->get_param("redirect");

        // Create the user.
        $user_id = wp_insert_user( $user_data );

        // Check if the user was created successfully.
        if ( ! is_wp_error( $user_id ) ) {

            // Get the LearnDash group.
            $group = get_post( DEMO_USER_GROUP_ID );

            // Check if the group exists.
            if ( $group ) {

                // Add the user to the group.
                $demousers = learndash_get_groups_user_ids( DEMO_USER_GROUP_ID , true );
                $demousers[] = $user_id;
                learndash_set_groups_users(DEMO_USER_GROUP_ID, $demousers );
        
            }
        }


        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id );

        //Create a function to assign 500 coins to new users at the time of account creation. This can be done using Game of Press trigger or code.
        $points = 5000;
        $points_type = "points";
        $points_meta = "_gamipress_{$points_type}_points";
        $new_points_meta = "_gamipress_{$points_type}_new_points";
        
        // Update user's points total
        gamipress_update_user_meta( $user_id, $points_meta, $points );
        // Update a meta as flag to meet how many points has been awarded or deducted
        gamipress_update_user_meta( $user_id, $new_points_meta, $points );


        // Write a DB query to populate the Game of Press table with 74 practice reads consecutively, starting 75 days ago.
        $dates = [];
        for ($i = 2; $i <= 75; $i++) { // inserts 74 records excluding today
            $date = new \DateTime("-$i days"); // create a DateTime object for the current day
            $date_server = $date->format('Y-m-d H:i:s');
            gamipress_insert_user_earning( $user_id, 
                            ["post_id"=>GAMIPRESS_EARNING_POST_ID, "date"=>$date_server],
                            ["minutes"=>$minutes]
                        );
        }

        //Select a few kranic animals to unlock in the demo accounts and add the relevant data to the database.
        $quranic_animals = [ "Wolf", "Lion"];
        foreach($quranic_animals as $animal){
            $args = [
                'post_type'      => 'quranic-animal',
                'numberposts'    => -1,
                'post_status'    => 'publish',
                'title'     => $animal
            ];

            $quranic_animal_posts = get_posts($args);
            if(!empty($quranic_animal_posts)){
                gamipress_award_achievement_to_user( $quranic_animal_posts[0]->ID, $user_id);
            }
        }

        //Create a function to unlock badges using the data populated in the demo accounts.
        $badges = ["Welcome", "First Step", "Brainiac In The Making", "On a Roll"];
        foreach($badges as $badge){
            $badge_posts = parent::wpdb()->get_results("SELECT * FROM ".parent::wpdb()->prefix."posts WHERE post_type='badges' AND post_title='".esc_sql($badge)."' LIMIT 1");
            if(!empty($badge_posts)){
                gamipress_award_achievement_to_user( $badge_posts[0]->ID, $user_id, $admin_id);
            }
        }

        //get_user_meta($user_id, "done_gets_started", true)
        update_user_meta($user_id, "done_gets_started", true);

        // if redirect is not empty setup default avatar and gender
        if(!empty($redirect)){
            update_user_meta($user_id, "custom_avatar_url", site_url("/wp-content/themes/buddyboss-theme-child/assets/img/default_avatar/avatar.png"));
            update_user_meta($user_id,"custom_avatar_full", site_url("/wp-content/themes/buddyboss-theme-child/assets/img/default_avatar/full.png"));
            update_user_meta($user_id, "gender", "female");
            update_user_meta($user_id,"avatar_selected", true);
            update_user_meta($user_id, "first_name", "Demo");
        }
        
        // mark lesson complete
        // this is for continue where you left of
        learndash_process_mark_complete( $user_id,  227302); 
        //learndash_process_mark_complete( $user_id,  215420);

        $response["success"] = true;
        $response["user_id"] = $user_id;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function is_user_teacher($request=[]){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $sub_role = get_field('user_role', 'user_'.$user_id);

        if($sub_role != "teacher") return false;
        else return true;
    }

    static function is_user_institute_admin(){

        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        if(current_user_can('administrator')){
            return false;
        }


        $is_parent = \Safar\SafarFamily::is_user_parent( );
		$is_user_teacher = \Safar\SafarUser::is_user_teacher( );

        if($is_parent || $is_user_teacher) return false;

        $ld_groups = learndash_get_administrators_group_ids( $user_id, true );

        if(!empty($ld_groups)) return true;
        else return false;

    }

    static function is_user_student(){
        $is_parent = \Safar\SafarFamily::is_user_parent( );
		$is_user_teacher = \Safar\SafarUser::is_user_teacher( );
        $is_admin = self::is_user_institute_admin();

        if( $is_parent || $is_user_teacher || $is_admin ){
            return false;
        }else{
            return true;
        }

    }

    static function is_institute_student_user( $user_id = false ){
        if(empty($user_id)) $user_id = get_current_user_id();

        $groups = learndash_get_users_group_ids($user_id);
        $is_institute_group = true;
        foreach($groups as $gid){
            $taxonomy = 'ld_group_tag';
            $terms = wp_get_post_terms($gid, $taxonomy);

            foreach($terms as $term){
                if( $term->slug == "family-group"){
                    $is_institute_group = false;
                }
            }
        }
        // should use array merge
        $groups = learndash_get_administrators_group_ids($user_id);
        foreach($groups as $gid){
            $taxonomy = 'ld_group_tag';
            $terms = wp_get_post_terms($gid, $taxonomy);

            foreach($terms as $term){
                if( $term->slug == "family-group"){
                    $is_institute_group = false;
                }
            }
        }

        return $is_institute_group;
    }

    static function is_demo_user( $request=[] ){
        $user_id = parent::pb_auth_user($request);
        if(!empty($user_id)){
            $groups = learndash_get_users_group_ids($user_id);
            $is_demo = false;
            foreach($groups as $group_id){
                if($group_id==DEMO_USER_GROUP_ID){
                    $is_demo = true;
                }
            }
            return $is_demo;
        }
        return false;
    }

    static function demo_user_has_access($post_id){
        
        $allowed_pages = get_field("demo_user_allowed_pages", DEMO_USER_GROUP_ID);    
        $allowed_page_ids = [];
        foreach($allowed_pages as $e){
            $allowed_page_ids[] = $e["allowed_pages"]->ID;
        }
        $is_demo_user = self::is_demo_user();

        if(!$is_demo_user){
            return true;
        }else{
            if( in_array($post_id, $allowed_page_ids)){
                return true;
            }else{
                return false;
            }
        }
     
    }


    static function login_logs( $request ){

        $draw = $request->get_param("draw");
        $limit = $request->get_param("length");
        $offset = $request->get_param("start");
        $search_value = $request->get_param("search");//search[value]: 
        $order_arr = $request->get_param("order");
        $order_col = $order_arr[0]["column"];
        $csv = $request->get_param("csv");
        $period = $request->get_param("period");
        $tz = $request->get_param("tz");
        $search_class = $request->get_param("search_class");

        self::$user_tz = $tz;
        
        
        $order_by = "";
        switch($order_col){
            case 0: $order_by = "first_name"; break;
            case 1: $order_by = "last_name"; break;
            case 2: $order_by = "login_count"; break;
            case 3: $order_by = "login_datetime"; break;
            case 4: $order_by = "current_login_streak"; break;
            case 5: $order_by = "highest_login_streak"; break;
        }

        if(!empty($csv)){
            $is_limit = "";
        }else{
            $is_limit = " LIMIT ".$limit." OFFSET ".$offset." ;";
        }

        $order_dir = $order_arr[0]["dir"];

        $where = " WHERE 1 ";
        if (!empty($search_value)) {
            $where .= " AND ( umeta_fname.meta_value LIKE '".esc_sql($search_value["value"])."%' OR umeta_lname.meta_value LIKE '".esc_sql($search_value["value"])."%' )";
        }

        $period = esc_sql($period); // Assuming you have a database connection named $conn
        switch($period) {
            case "Last Week":
                $where .= "AND ( DATE(login_datetime) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) )";
                $start_date = date('Y-m-d', strtotime('-1 week'));
                $end_date = date("Y-m-d");

                break;
            case "Last 30 Days":
                $where .= "AND ( DATE(login_datetime) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) )";
                $start_date = date('Y-m-d', strtotime('-30 days'));
                $end_date = date("Y-m-d");

                break;
            case "Last 90 Days":
                $where .= "AND ( DATE(login_datetime) >= DATE_SUB(CURDATE(), INTERVAL 90 DAY) )";
                $start_date = date('Y-m-d', strtotime('-90 days'));
                $end_date = date("Y-m-d");

                break;
            case "This Academic Year":
                // Academic year starts on 1st September and ends on 31st August next year
                $where .= " AND ( DATE(login_datetime) >= CONCAT(YEAR(CURDATE()) - IF(MONTH(CURDATE()) < 9, 1, 0), '-09-01') 
                                AND DATE(login_datetime) <= CONCAT(YEAR(CURDATE()) + IF(MONTH(CURDATE()) >= 9, 1, 0), '-08-31') )";

                $currentYear = date('Y');
                $start_date = ($currentYear - (date('n') < 9 ? 1 : 0)) . '-09-01';
                $end_date = ($currentYear + (date('n') >= 9 ? 1 : 0)) . '-08-31';

                break;
        }

      


        // check if user is an admin
        // if not get only users who is under the parent
        if (!current_user_can('administrator')) {
            // User is not an admin     
            $user_id = get_current_user_id();   

            if(SafarUser::is_user_teacher()){
                $children_user_ids = [];
                $ld_groups = learndash_get_administrators_group_ids( $user_id, true );
                foreach($ld_groups as $gid){
                    $students = learndash_get_groups_user_ids(  $gid , true);
                    foreach($students as $studid){
                        if(get_field('user_role', 'user_'.$studid) != "teacher") $children_user_ids[] = $studid;
                    }
                }
                //print_r([SafarUser::is_user_institute_parent()]);
            }else{

                $parent_school_details = SafarSchool::get_school_details($request);
                $children_user_ids = [];
                foreach($parent_school_details->data["students"] as $student){
                    $children_user_ids[] = $student->data->ID;
                }
                foreach($parent_school_details->data["teachers"] as $student){
                    $children_user_ids[] = $student->data->ID;
                }

            }
            if(!SafarUser::is_user_institute_parent()) $children_user_ids[] = $user_id;

            if(!empty($children_user_ids)){
                $where .= " AND ( users.ID in (".implode(",",$children_user_ids).")) ";
            }
        }

        // search by group name
        if(!empty($search_class)){
            $searchQuery = $search_class; // Replace with the desired search query
            $searchQuery = esc_sql($searchQuery);

            $query = " 
                SELECT ".parent::wpdb()->prefix."bp_groups.id, 
                    ".parent::wpdb()->prefix."bp_groups.name, 
                    ".parent::wpdb()->prefix."bp_groups.description, 
                    ".parent::wpdb()->prefix."bp_groups_members.user_id, 
                    ".parent::wpdb()->prefix."users.display_name  
                FROM ".parent::wpdb()->prefix."bp_groups 
                    LEFT JOIN ".parent::wpdb()->prefix."bp_groups_members ON ( ".parent::wpdb()->prefix."bp_groups_members.group_id = ".parent::wpdb()->prefix."bp_groups.id AND ".parent::wpdb()->prefix."bp_groups_members.is_admin = 1 )
                    LEFT JOIN ".parent::wpdb()->prefix."users ON ".parent::wpdb()->prefix."users.ID = ".parent::wpdb()->prefix."bp_groups_members.user_id
                
                WHERE ".parent::wpdb()->prefix."bp_groups.name LIKE '%".$searchQuery."%' OR ".parent::wpdb()->prefix."users.display_name like '%".$searchQuery."%';
            ";
           
            $groups = parent::wpdb()->get_results($query);
            // Loop through the groups and display the results
            
            $member_users = [];

            if (!empty($groups)) {
                foreach ($groups as $group) {
                    // Access the group details as needed
                    $groupId = $group->id;
                    $groupName = $group->name;
                    $groupDescription = $group->description;

                    $params = array(
                        'group_id' => $groupId,
                        'per_page' => -1, // Retrieve all members
                    );                    
                    $groupMembers = groups_get_group_members($params);

                    foreach ($groupMembers['members'] as $member) {
                        $member_users[] = $member->user_id;
                    }

                }
            }
            
            if(!empty($member_users)){
                $where .= " AND ( users.ID in (".implode(",",$member_users).")) ";
            }else{
                // force no result, because $member_users is blank, means no members found with Search CLass parameter
                $where .= " AND ( users.ID = 0 )";
            }
        }

        // if $period is provided, only count logins within that period
        $where_login_logs = "";
        if(!empty($period)){
            if(!empty($start_date) && !empty($end_date)){
                $where_login_logs .= " AND  ( DATE_FORMAT(login_datetime, '%Y-%m-%d') >= '$start_date' AND   DATE_FORMAT(login_datetime, '%Y-%m-%d') <= '$end_date' ) ";
            }
        }
        
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS users.ID as user_id, users.user_login as user_login,
                ( SELECT login_datetime FROM `".parent::wpdb()->prefix."login_logs`as login_logs_count WHERE login_logs_count.user_id = login_logs.user_id ORDER BY login_datetime DESC LIMIT 1 ) as login_datetime, 
                ( SELECT count(*) 
                    FROM `".parent::wpdb()->prefix."login_logs`as login_logs_count 
                    WHERE login_logs_count.user_id = login_logs.user_id 
                        ". $where_login_logs ."
                    LIMIT 1 
                ) login_count,
                umeta_fname.meta_value as first_name,
                umeta_lname.meta_value as last_name,
                tbl_cs.current_streak,
                tbl_cs.highest_streak,
                tbl_cs.current_login_streak,
                tbl_cs.highest_login_streak
            
            FROM `".parent::wpdb()->prefix."users` as users
                INNER JOIN `".parent::wpdb()->prefix."usermeta`as umeta_fname ON(users.ID=umeta_fname.user_id AND umeta_fname.meta_key='first_name')
                INNER JOIN `".parent::wpdb()->prefix."usermeta`as umeta_lname ON(users.ID=umeta_lname.user_id AND umeta_lname.meta_key='last_name')
                LEFT JOIN `".parent::wpdb()->prefix."login_logs` as login_logs ON login_logs.user_id = users.ID
                LEFT JOIN `".parent::wpdb()->prefix."practice_log_report`as tbl_cs ON tbl_cs.user_id=users.ID
            " . $where . "
            GROUP BY users.ID
            ORDER BY ".$order_by." ".$order_dir."
            ".$is_limit;

        #echo $sql;

        // trigger update practice log report
        // pass the base SQL in order to update the data of each user for the report
        // for more optimized reporting
        self::update_practice_log_report($request, $sql);
        
        $subjects_background_color = get_field("subjects_background_color","option");

        $rs = parent::wpdb()->get_results($sql);
        $total_rows = parent::wpdb()->get_var("SELECT FOUND_ROWS() AS total_rows");
        $data = [];
        foreach($rs as $log){

            if(empty($log->login_datetime) || $log->login_datetime == "0000-00-00 00:00:00"){
                $log->login_datetime = "Never Logged In";
                $orig_datetime = $log->login_datetime;
            }else{
                $log->login_datetime = date('Y-m-d H:i:s', strtotime($log->login_datetime));
                $orig_datetime = $log->login_datetime;
                $log->login_datetime = date('D, jS F Y', strtotime(self::convert_date($tz, $log->login_datetime ) ) );
            }

            if(!$log->current_streak) $log->current_streak = 0;
            if(!$log->highest_streak) $log->highest_streak = 0;


            // get student classrooms and bg color
            $classrooms = [];
            $ld_groups = [];
            $ld_groups = learndash_get_users_group_ids( $log->user_id, true );

            $ld_gids = [];
            foreach($ld_groups as $ld_gid){
                $group = get_post($ld_gid);
                $bg_color = "";
                $category = wp_get_post_terms( $group->ID, "ld_group_category");

                $user_ids = [];
                $students = learndash_get_groups_user_ids(  $group->ID , true);
                #$adminstrators = learndash_get_groups_administrator_ids( $group->ID, true);
                
                foreach($students as $student_id) $user_ids[] = $student_id;
                #foreach($adminstrators as $admin_id) $user_ids[] = $admin_id;
               
                if( in_array($log->user_id, $user_ids) ){
                    
                    // parent id should be > 0, meaning this is a classroom not an institute LD group
                    if(!empty($group->post_parent)){
                        $ld_gids[] = $group->ID;
                        if(!empty($category)){
                            foreach($category as $ckey=>$cat){
                                foreach($subjects_background_color as $bg){
                                    if($bg["subject"] == $cat->term_id) $bg_color = $bg["background_color"];
                                }
                            }
                        }
                        $classrooms[] = [
                            "name" => $group->post_title,
                            "bg_color" => $bg_color
                        ];
                    }
                }
                
            }

            if(empty($log->current_login_streak)) $log->current_login_streak = 0;
            if(empty($log->highest_login_streak)) $log->highest_login_streak = 0;

            if($csv){
                $new_classrooms = [];
                foreach($classrooms as $cl){
                    $new_classrooms[] = $cl["name"];
                }
                $classrooms = implode(" ", $new_classrooms);
            }
            
            $data[] = [
                "first_name"=>$log->first_name,
                "last_name"=>$log->last_name,
                "login_count"=>$log->login_count,
                "login_datetime"=>$log->login_datetime,
                "current_streak"=>$log->current_streak,
                "highest_streak"=>$log->highest_streak,
                "current_login_streak"=>$log->current_login_streak,
                "highest_login_streak"=>$log->highest_login_streak,
                "user_id" => $log->user_login,
                "orig_datetime" => $orig_datetime,
                "tz" => $tz,
                "classrooms" => $classrooms,
            ];
        }

        $response["draw"] = $draw;
        $response["recordsTotal"] = $total_rows;
        $response["recordsFiltered"] = $total_rows;
        $response["data"] = $data;
        $response["tz"] = $tz;

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function login_logs_details( $request ){
        $user_id = $request->get_param("id");
        
        // trigger update practice log report
        self::update_practice_log_report($request);

        $user_data = get_userdata($user_id);
        $profile["first_name"] = get_user_meta($user_id,"first_name",true);
        $profile["last_name"] = get_user_meta($user_id,"last_name",true);
        $profile["avatar"] = get_avatar_url($user_id);
        $profile["email"] = $user_data->user_email;
        $tz = $request->get_param("tz");
        $response["profile"] = $profile;

        
        $base_sql = "
            SELECT users.ID as user_id, 
                ( SELECT login_datetime FROM `".parent::wpdb()->prefix."login_logs`as login_logs_count WHERE login_logs_count.user_id = login_logs.user_id ORDER BY login_datetime DESC LIMIT 1 ) as login_datetime,
                ( SELECT count(*) FROM `".parent::wpdb()->prefix."login_logs`as login_logs_count WHERE login_logs_count.user_id = login_logs.user_id LIMIT 1 ) login_count,
                umeta_fname.meta_value as first_name,
                umeta_lname.meta_value as last_name,
                tbl_cs.current_streak,
                tbl_cs.highest_streak,
                tbl_cs.current_login_streak,
                tbl_cs.highest_login_streak
            FROM `".parent::wpdb()->prefix."users` as users
                INNER JOIN `".parent::wpdb()->prefix."usermeta`as umeta_fname ON(users.ID=umeta_fname.user_id AND umeta_fname.meta_key='first_name')
                INNER JOIN `".parent::wpdb()->prefix."usermeta`as umeta_lname ON(users.ID=umeta_lname.user_id AND umeta_lname.meta_key='last_name')
                LEFT JOIN `".parent::wpdb()->prefix."login_logs` as login_logs ON login_logs.user_id = users.ID
                LEFT JOIN `".parent::wpdb()->prefix."practice_log_report`as tbl_cs ON tbl_cs.user_id=users.ID
            WHERE users.ID = ".$user_id."
            GROUP BY users.ID ";

        $rs = parent::wpdb()->get_results($base_sql);
        $summary = $rs[0];
        
        if(empty($summary->current_streak)){
            $summary->current_streak = 0;
        }
        if(empty($summary->highest_streak)){
            $summary->highest_streak = 0;
        }

        if(empty($summary->current_login_streak)){
            $summary->current_login_streak = 0;
        }
        if(empty($summary->highest_login_streak)){
            $summary->highest_login_streak = 0;
        }

        //$log->login_datetime = date('D, jS F Y', strtotime(self::convert_date($tz, $log->login_datetime ) ) );
        if(!empty($summary->login_datetime)){
            $summary->orig_datetime = $summary->login_datetime;
            $summary->login_datetime = date('Y-m-d H:i:s', strtotime(self::convert_date($tz, $summary->login_datetime ) ) );
        }

        $response["summary"] = $summary;
        

        $logs = "
                SELECT 
                    login_logs.id, 
                    login_logs.user_id, 
                    login_datetime
                FROM `".parent::wpdb()->prefix."login_logs` as login_logs
                    INNER JOIN `".parent::wpdb()->prefix."usermeta` as umeta_fname ON ( login_logs.user_id = umeta_fname.user_id AND umeta_fname.meta_key='first_name' )
                    INNER JOIN `".parent::wpdb()->prefix."usermeta` as umeta_lname ON ( login_logs.user_id = umeta_lname.user_id AND umeta_lname.meta_key='last_name' )
                WHERE login_logs.user_id = ".$user_id."
                ORDER BY login_datetime DESC 
        ";
        $logs = parent::wpdb()->get_results($logs);

        if(!empty($logs)){
            foreach($logs as $key=>$log){
                $logs[$key]->orig_datetime = $log->login_datetime;
                $logs[$key]->login_datetime = date('Y-m-d H:i:s', strtotime(self::convert_date($tz, $log->login_datetime ) ) );
            }
        }

        $response["logs"] = $logs;
        $response["practice_logs"] = self::practice_logs( $request );

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function practice_logs_report( $request ){

        $draw = $request->get_param("draw");
        $limit = $request->get_param("length");
        $offset = $request->get_param("start");
        $search_value = $request->get_param("search");//search[value]: 
        $order_arr = $request->get_param("order");
        $order_col = $order_arr[0]["column"];
        $csv = $request->get_param("csv");
        $period = $request->get_param("period");
        $tz = $request->get_param("tz");
        $search_class = $request->get_param("search_class");
        $user_id = get_current_user_id();  

        

        $draw = $request->get_param("draw");
        $limit = $request->get_param("length");
        $offset = $request->get_param("start");
        $search_value = $request->get_param("search");//search[value]: 
        $order_arr = $request->get_param("order");
        $order_col = $order_arr[0]["column"];
        $csv = $request->get_param("csv");
        $period = $request->get_param("period");
        $tz = $request->get_param("tz");
        $search_class = $request->get_param("search_class");

        $order_by = "";
        switch($order_col){
            case 0: $order_by = "first_name"; break;
            case 1: $order_by = "last_name"; break;
            case 2: $order_by = "number_practice_reads"; break;
            case 3: $order_by = "avg_practice_length"; break;
            case 4: $order_by = "date_time_last_practice"; break;
            case 5: $order_by = "current_streak"; break;
            case 6: $order_by = "highest_streak"; break;
        }

        if(!empty($csv)){
            $is_limit = "";
        }else{
            $is_limit = " LIMIT ".$limit." OFFSET ".$offset." ;";
        }

        $where = " WHERE 1 ";
        if (!empty($search_value)) {
            $where .= " AND ( umeta_fname.meta_value LIKE '".esc_sql($search_value["value"])."%' OR umeta_lname.meta_value LIKE '".esc_sql($search_value["value"])."%' )";
        }

        // check if user is an admin
        // if not get only users who is under the parent
        if (!current_user_can('administrator')) {
            // User is not an admin     
            $user_id = get_current_user_id();   
            $parent_school_details = SafarSchool::get_school_details($request);
            $children_user_ids = [];
            foreach($parent_school_details->data["students"] as $student){
                $children_user_ids[] = $student->data->ID;
            }
            foreach($parent_school_details->data["teachers"] as $student){
                //if(!SafarUser::is_user_institute_parent())  
                $children_user_ids[] = $student->data->ID;
            }
            if(!SafarUser::is_user_institute_parent()) $children_user_ids[] = $user_id;

            if(!empty($children_user_ids)){
                $where .= " AND ( users.ID in (".implode(",",$children_user_ids).")) ";
            }

        }

        // search by group name
        if(!empty($search_class)){
            $searchQuery = $search_class; // Replace with the desired search query
            $searchQuery = esc_sql($searchQuery);

            $query = " 
                SELECT ".parent::wpdb()->prefix."bp_groups.id, 
                    ".parent::wpdb()->prefix."bp_groups.name, 
                    ".parent::wpdb()->prefix."bp_groups.description, 
                    ".parent::wpdb()->prefix."bp_groups_members.user_id, 
                    ".parent::wpdb()->prefix."users.display_name  
                FROM ".parent::wpdb()->prefix."bp_groups 
                    LEFT JOIN ".parent::wpdb()->prefix."bp_groups_members ON ( ".parent::wpdb()->prefix."bp_groups_members.group_id = ".parent::wpdb()->prefix."bp_groups.id AND ".parent::wpdb()->prefix."bp_groups_members.is_admin = 1 )
                    LEFT JOIN ".parent::wpdb()->prefix."users ON ".parent::wpdb()->prefix."users.ID = ".parent::wpdb()->prefix."bp_groups_members.user_id
                
                WHERE ".parent::wpdb()->prefix."bp_groups.name LIKE '%".$searchQuery."%' OR ".parent::wpdb()->prefix."users.display_name like '%".$searchQuery."%';
            ";

            $groups = parent::wpdb()->get_results($query);
            // Loop through the groups and display the results
            
            $member_users = [];

            if (!empty($groups)) {
                foreach ($groups as $group) {
                    // Access the group details as needed
                    $groupId = $group->id;
                    $groupName = $group->name;
                    $groupDescription = $group->description;

                    $params = array(
                        'group_id' => $groupId,
                        'per_page' => -1, // Retrieve all members
                    );                    
                    $groupMembers = groups_get_group_members($params);

                    foreach ($groupMembers['members'] as $member) {
                        $member_users[] = $member->user_id;
                    }

                }
            }
            
            if(!empty($member_users)){
                $where .= " AND ( users.ID in (".implode(",",$member_users).")) ";
            }else{
                // force no result, because $member_users is blank, means no members found with Search CLass parameter
                $where .= " AND ( users.ID = 0 )";
            }
        }

        $period = esc_sql($period); // Assuming you have a database connection named $conn
        switch($period) {
            case "Last Week":
                $where .= "AND ( DATE(date_time_last_practice) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) )";
                break;
            case "Last 30 Days":
                $where .= "AND ( DATE(date_time_last_practice) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) )";
                break;
            case "Last 90 Days":
                $where .= "AND ( DATE(date_time_last_practice) >= DATE_SUB(CURDATE(), INTERVAL 90 DAY) )";
                break;
            case "This Academic Year":
                // Academic year starts on 1st September and ends on 31st August next year
                $where .= " AND ( DATE(date_time_last_practice) >= CONCAT(YEAR(CURDATE()) - IF(MONTH(CURDATE()) < 9, 1, 0), '-09-01') 
                                AND DATE(date_time_last_practice) <= CONCAT(YEAR(CURDATE()) + IF(MONTH(CURDATE()) >= 9, 1, 0), '-08-31') )";
                break;
                
        }
        
        $order_dir = $order_arr[0]["dir"];

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *, 
                umeta_fname.meta_value as first_name,
                umeta_lname.meta_value as last_name,
                users.ID as user_id,
                users.user_login as user_login
            FROM `".parent::wpdb()->prefix."users` as users
                LEFT JOIN `".parent::wpdb()->prefix."practice_log_report`as practice_log_report ON users.ID = practice_log_report.user_id
                LEFT JOIN `".parent::wpdb()->prefix."usermeta`as umeta_fname ON(users.ID=umeta_fname.user_id AND umeta_fname.meta_key='first_name')
                LEFT JOIN `".parent::wpdb()->prefix."usermeta`as umeta_lname ON(users.ID=umeta_lname.user_id AND umeta_lname.meta_key='last_name')
            " . $where . "
            GROUP BY users.ID 
            ORDER BY ".$order_by." ".$order_dir."
            ".$is_limit;
        
        
        // trigger update practice log report
        // pass the base SQL in order to update the data of each user for the report
        // for more optimized reporting
        self::update_practice_log_report($request, $sql);
        
        $rs = parent::wpdb()->get_results($sql);
        $total_rows = parent::wpdb()->get_var("SELECT FOUND_ROWS() AS total_rows");

        $subjects_background_color = get_field("subjects_background_color","option");
        $data = [];

        foreach($rs as $log){

            if(empty($log->date_time_last_practice)  || $log->date_time_last_practice === "0000-00-00 00:00:00" ){
                $log->date_time_last_practice = "Never Practiced";
                $orig_datetime = $log->date_time_last_practice;
            }else{
                $log->date_time_last_practice = date('D, jS F Y', strtotime($log->date_time_last_practice));
                $orig_datetime = $log->date_time_last_practice;
                $log->date_time_last_practice = $log->date_time_last_practice;
            }

            if(empty($log->avg_practice_length)) $log->avg_practice_length = 0;
            if(empty($log->number_practice_reads)) $log->number_practice_reads = 0;
            if(empty($log->current_streak)) $log->current_streak = 0;
            if(empty($log->highest_streak)) $log->highest_streak = 0;


            // get student classrooms and bg color
            $classrooms = [];
            $ld_groups = [];
            $ld_groups = learndash_get_users_group_ids( $log->user_id, true );

            $ld_gids = [];
            foreach($ld_groups as $ld_gid){
                $group = get_post($ld_gid);
                $bg_color = "";
                $category = wp_get_post_terms( $group->ID, "ld_group_category");

                $user_ids = [];
                $students = learndash_get_groups_user_ids(  $group->ID , true);
                #$adminstrators = learndash_get_groups_administrator_ids( $group->ID, true);
                
                foreach($students as $student_id) $user_ids[] = $student_id;
                #foreach($adminstrators as $admin_id) $user_ids[] = $admin_id;
               
                if( in_array($log->user_id, $user_ids) ){
                    
                    // parent id should be > 0, meaning this is a classroom not an institute LD group
                    if(!empty($group->post_parent)){
                        $ld_gids[] = $group->ID;
                        if(!empty($category)){
                            foreach($category as $ckey=>$cat){
                                foreach($subjects_background_color as $bg){
                                    if($bg["subject"] == $cat->term_id) $bg_color = $bg["background_color"];
                                }
                            }
                        }
                        $classrooms[] = [
                            "name" => $group->post_title,
                            "bg_color" => $bg_color
                        ];
                    }
                }
                
            }

            if($csv){
                $new_classrooms = [];
                foreach($classrooms as $cl){
                    $new_classrooms[] = $cl["name"];
                }
                $classrooms = implode(" ", $new_classrooms);
            }


            $data[] = [
                "first_name"=>$log->first_name,
                "last_name"=>$log->last_name,
                "avg_practice_length"=>$log->avg_practice_length." Min",
                "date_time_last_practice"=>$log->date_time_last_practice,
                "current_streak"=>$log->current_streak,
                "highest_streak"=>$log->highest_streak,
                "user_id" => $log->user_login,
                "orig_datetime" => $orig_datetime,
                "number_practice_reads" => $log->number_practice_reads,
                "classrooms" => $classrooms
            ];
        }
        
        $response["draw"] = $draw;
        $response["recordsTotal"] = $total_rows;
        $response["recordsFiltered"] = $total_rows;
        $response["data"] = $data;
        $response["tz"] = $tz;

        $response = new \WP_REST_Response($response);
        return $response;
    }


    static function practice_logs_report_details( $request ){
        $user_id = $request->get_param("id");

        // trigger update practice log report
        self::update_practice_log_report($request);

        $user_data = get_userdata($user_id);
        $profile["first_name"] = get_user_meta($user_id,"first_name",true);
        $profile["last_name"] = get_user_meta($user_id,"last_name",true);
        $profile["avatar"] = get_avatar_url($user_id);
        $profile["email"] = $user_data->user_email;
        $tz = $request->get_param("tz");
        $response["profile"] = $profile;

        
        $base_sql = "
            SELECT SQL_CALC_FOUND_ROWS *, 
                umeta_fname.meta_value as first_name,
                umeta_lname.meta_value as last_name
            FROM `".parent::wpdb()->prefix."practice_log_report` as practice_log_report
                INNER JOIN `".parent::wpdb()->prefix."usermeta` as umeta_fname ON ( practice_log_report.user_id = umeta_fname.user_id AND umeta_fname.meta_key='first_name' )
                INNER JOIN `".parent::wpdb()->prefix."usermeta` as umeta_lname ON ( practice_log_report.user_id = umeta_lname.user_id AND umeta_lname.meta_key='last_name' )
                WHERE practice_log_report.user_id = ".$user_id;

        $rs = parent::wpdb()->get_results($base_sql);
        $summary = $rs[0];
        
        if(empty($summary->current_streak)){
            $summary->current_streak = 0;
        }
        if(empty($summary->highest_streak)){
            $summary->highest_streak = 0;
        }

        //$log->login_datetime = date('D, jS F Y', strtotime(self::convert_date($tz, $log->login_datetime ) ) );
        if(!empty($summary->date_time_last_practice)){
            $summary->orig_datetime = $summary->date_time_last_practice;
            $summary->date_time_last_practice =$summary->date_time_last_practice;
        }

        if(empty($summary->number_practice_reads)){
            $summary->number_practice_reads = 0;
        }
        if(empty($summary->avg_practice_length)){
            $summary->avg_practice_length = 0;
        }
        

        $response["summary"] = $summary;
        

        $request->set_param("id", $user_id);
        $logs = self::practice_logs( $request )->data;

        if(!empty($logs)){
            foreach($logs as $key=>$log){
                $logs[$key]["orig_datetime"] = $log["date"];
                $logs[$key]["date"] = $log["date"];
            }
        }

        $response["logs"] = $logs;

        $response = new \WP_REST_Response($response);
        return $response;
    }


    // function to refresh practice_log_report
    static function update_practice_log_report( $request, $sql="" ){
        // check if user is an admin
        // if not get only users who is under the parent
        $reports = [];
        $tz = $request->get_param("tz");
        if (!current_user_can('administrator') || !empty($sql) ) {
            $user_id = get_current_user_id();
            // only run this once or when transient key is not available
            $transient_key = "practice_report_".$user_id;
            $id = $request->get_param("id");
            delete_transient($transient_key);

            // NOTE:: if $Period is provided, only get the data/logs 
            // BOTH $practice_logs and $login_logs 
            $period = $request->get_param("period");
            
            switch ($period) {
                case "Last Week":
                    $start_date = date('Y-m-d', strtotime('-1 week'));
                    $end_date = date("Y-m-d");
                    break;
                case "Last 30 Days":
                    $start_date = date('Y-m-d', strtotime('-30 days'));
                    $end_date = date("Y-m-d");
                    break;
                case "Last 90 Days":
                    $start_date = date('Y-m-d', strtotime('-90 days'));
                    $end_date = date("Y-m-d");
                    break;
                case "This Academic Year": default:
                    $currentYear = date('Y');
                    $start_date = ($currentYear - (date('n') < 9 ? 1 : 0)) . '-09-01';
                    $end_date = ($currentYear + (date('n') >= 9 ? 1 : 0)) . '-08-31';
                    break;
            }

            $reports = get_transient($transient_key);
    
            if(empty($reports)){
                // User is not an admin     
                
                $children_user_ids = [];

                // SQL is provided, get the user ids of that query,, no need to get it from school details
                if($sql){
                    $rs_user_ids = parent::wpdb()->get_results($sql);
                    foreach($rs_user_ids as $rs_user){
                        $children_user_ids[] = $rs_user->user_id;
                    }
                }else{
                    $parent_school_details = SafarSchool::get_school_details($request);
                    foreach($parent_school_details->data["students"] as $student){
                        $children_user_ids[] = $student->data->ID;
                    }
                    foreach($parent_school_details->data["teachers"] as $student){
                        $children_user_ids[] = $student->data->ID;
                    }
                    
                }

                # when $id is provided, meaning only update the logs on that specific user, $Id == is a user id
                if(!empty($id)){
                    $children_user_ids = [];
                    $children_user_ids[] = $id;
                    
                }

                $reports = [];
                foreach($children_user_ids as $user_id){
                    $request->set_param("id", $user_id);

                    // NOTE:: if $Period is provided, only get the data/logs 
                    // BOTH $practice_logs and $login_logs 
                    $practice_logs = self::practice_logs( $request );

                    

                    if(!empty($period)){
                        $filteredData = array_map(function($item) use ($start_date, $end_date) {
                            $itemDate = strtotime($item['date']);
                            $startDate = strtotime($start_date);
                            $endDate = strtotime($end_date);
                        
                            if ($itemDate >= $startDate && $itemDate <= $endDate) {
                                return $item;
                            }
                        }, $practice_logs->data);
                        
                        $practice_logs->data = array_filter($filteredData);

                       
                    }

                    #print_r($practice_logs->data);
                    $practice_streak = self::calculate_streak(array_reverse($practice_logs->data));
                    $highest_streak = self::calculate_highest_streak(array_reverse($practice_logs->data));
                    
                
                    // login logs calculate streak
                    // current_login_streak highest_login_streak
                    // if $period is provided, filter login logs within the start_date and end date of that period
                    $where = "";
                    if(!empty($period)){
                        $where .= " AND  ( DATE_FORMAT(login_datetime, '%Y-%m-%d') >= '$start_date' AND   DATE_FORMAT(login_datetime, '%Y-%m-%d') <= '$end_date' ) ";
                    }
                    $login_logs = parent::wpdb()->get_results(" SELECT id, DATE_FORMAT(login_datetime, '%Y-%m-%d %H:%i:%s') AS date , user_id 
                                                                    FROM `".parent::wpdb()->prefix."login_logs` 
                                                                    WHERE user_id = '".esc_sql($user_id)."' ".$where."
                                                                GROUP BY date
                                                                ORDER BY date ASC ", ARRAY_A);
                                                               
                    $current_login_streak = self::calculate_streak($login_logs, $user_id);
                    $login_logs_new = [];
                    // convert login_logs to current timezone
                    if(!empty($tz)){
                        $log_dates = [];
                        foreach($login_logs as $key=>$log){
                            $date =  date("Y-m-d", strtotime(self::convert_date($tz, $log["date"], date_default_timezone_get()) ) ); //date("Y-m-d", strtotime($log["date"]));

                            if(!in_array($date, $log_dates)){
                                $log["date"] = $date;
                                $login_logs_new[] = $log;
                            }
                            $log_dates[] = $date;
                        }
                         
                    }else{
                        $login_logs_new = $login_logs;
                    }

                    $highest_login_streak = self::calculate_highest_streak_login($login_logs_new, $user_id);

                   

                    if(empty($practice_streak)) $practice_streak = 0;
                    if(empty($highest_streak)) $highest_streak = 0;
                    if(empty($current_login_streak)) $current_login_streak = 0;
                    if(empty($highest_login_streak)) $highest_login_streak = 0;
                    
                    $reports[$user_id] = ["logs"=>$practice_logs->data, 
                                            "current_streak"=>$practice_streak, 
                                            "highest_streak"=>$highest_streak,
                                            "current_login_streak" => $current_login_streak,
                                            "highest_login_streak" => $highest_login_streak
                                        ];
                }
                // store this to ".parent::wpdb()->prefix."practice_log_report table
                set_transient( $transient_key, $reports, 1800 ); // 3600 = 1 hour
            }

        }
        #print_r($reports);
    
        if(!empty($reports)){
            foreach($reports as $user_id => $report){
                // user_id, number_practice_reads, avg_practice_length, date_time_last_practice, current_streak, highest_streak
                #if(!empty($report["logs"])){

                    $avg_practice_length = 0;
                    foreach($report["logs"] as $log){
                        $avg_practice_length+=$log["minutes"];
                    }

                    $avg_practice_length = ceil($avg_practice_length / count($report["logs"]));

                    $number_practice_reads = count($report["logs"]);
                    $avg_practice_length = empty($avg_practice_length) ? 0:$avg_practice_length;
                    $date_time_last_practice = empty($report["logs"][0]["date"]) ? "t":$report["logs"][0]["date"];
                    $current_streak = $report["current_streak"];
                    $highest_streak = $report["highest_streak"];
                    $current_login_streak = $report["current_login_streak"];
                    $highest_login_streak = $report["highest_login_streak"];
                    
                    
                    // Check if user_id exists
                    $user_exists = parent::wpdb()->get_var(
                        parent::wpdb()->prepare("SELECT COUNT(*) FROM ".parent::wpdb()->prefix."practice_log_report WHERE user_id = %s", $user_id)
                    );
                    if ($user_exists) {
                        // User exists, perform an update
                        parent::wpdb()->update(
                            parent::wpdb()->prefix.'practice_log_report',
                            array(
                                'number_practice_reads' => $number_practice_reads,
                                'avg_practice_length' => $avg_practice_length,
                                'date_time_last_practice' => $date_time_last_practice,
                                'current_streak' => $current_streak,
                                'highest_streak' => $highest_streak,
                                'current_login_streak' => $current_login_streak,
                                'highest_login_streak' => $highest_login_streak,
                            ),
                            array('user_id' => $user_id)
                        );
                    } else {
                        // User does not exist, perform an insert
                        $error = parent::wpdb()->insert(
                            parent::wpdb()->prefix.'practice_log_report',
                            array(
                                'user_id' => $user_id,
                                'number_practice_reads' => $number_practice_reads,
                                'avg_practice_length' => $avg_practice_length,
                                'date_time_last_practice' => $date_time_last_practice,
                                'current_streak' => $current_streak,
                                'highest_streak' => $highest_streak,
                                'current_login_streak' => $current_login_streak,
                                'highest_login_streak' => $highest_login_streak,
                            )
                        );

                        
                    }

                #}
            }
        }

        $response["reports"] = $reports;
        $response["transient"] = $transient_key;

        $response = new \WP_REST_Response($response);
        return $response; 
    }

    static function send_gifter_receiver_emails( $group_id ){
        $administrator = learndash_get_groups_administrator_ids($group_id);

        $receiver_id = $administrator[0];
        $sender_id = get_post_meta($group_id,"gifter_user_id", true);

        $subscription_frequency = "1 ".ucwords(get_post_meta($group_id,"subscription_period",true));
        $number_children = get_post_meta($group_id,"subscription_seats_count",true)." Child";
        $subscription_note = get_post_meta($group_id,"order_notes",true);
        $sender_first_name = get_post_meta($group_id,"gifter_first_name", true);

        $receiver_first_name = get_user_meta( $receiver_id, "first_name", true);
        $user_data = get_userdata($receiver_id);
        $receiver_email =  $user_data->user_email;
        $sender_email =  get_post_meta($group_id,"gifter_user_email", true);


        ob_start();
        require_once("email_templates/gift_subs_receiver.php");
        $email_body_receiver = ob_get_contents();
        ob_end_clean();

        ob_start();
        require_once("email_templates/gift_subs_sender.php");
        $email_body_sender = ob_get_contents();
        ob_end_clean();

        header('Content-Type: text/html; charset=utf-8');
        

        $headers = array('Content-Type: text/html; charset=UTF-8','Bcc: joe@pbdigital.com.au');
        
        // receiver send email
        $to = $receiver_email;
        $subject = "Your Gift Subscription Awaits!";
        wp_mail( $to, $subject, $email_body_receiver, $headers );

        // sender send email
        $to = $sender_email;
        $subject = "A Gift of Islamic Knowledge & Faith!";
        wp_mail( $to, $subject, $email_body_sender, $headers );

        return true;
    }

    static function test_gift_subscription_emails($request){
        $group_id = 231253;
        self::send_gifter_receiver_emails($group_id);
    }

    static function create_user_account($data){
        $email = $data["email"];
        $user_id = email_exists($email);
        $send_welcome_email = false;

        $user_login = $email;
        if(!empty($data["username"])) $user_login = $data["username"];

        $user_exists = false;
        if (!$user_id) {
            // User doesn't exist, so create a new user

            $user_data = [
                'user_email' => $email,
                'first_name' => $data["first_name"],
                'last_name'  => $data["last_name"],
                'user_pass' => wp_generate_password(),
                'user_login' => $user_login
            ];

            $user_id = wp_insert_user($user_data);
            $send_welcome_email = true;
            
        }else{
            $user_exists = true;
        }

        $user = get_user_by('email', $email);
        $user_id = $user->ID;
	    if(!empty($data["role"])) $user->set_role( $data["role"] );

        
        update_user_meta($user_id,"first_name", $data["first_name"]);
        update_user_meta($user_id,"last_name", $data["last_name"]);
        update_user_meta($user_id,"phone", $data["phone"]);
        update_user_meta($user_id,"relationship", $data["relationship"]);
    

        if(!empty($data["is_parent"])){
            \Safar\SafarFamily::send_institute_parent_welcome_mail($user_id, $data["institute_id"]);
            update_field( 'additional_roles', ["instituteparent"], 'user_'.$user_id );
        }
        
        return $user_id;
    }

    static function update_user_account($data){
        if(!empty($data["user_id"])){
            $user_id = $data["user_id"];
            $new_email = $data["email"];
            $user_data = get_userdata($user_id);

            if ($user_data) {
                $user_data->user_email = $new_email;
                wp_update_user($user_data);
                update_user_meta($user_id,"first_name", $data["first_name"]);
                update_user_meta($user_id,"last_name", $data["last_name"]);
                update_user_meta($user_id,"phone", $data["phone"]);
                update_user_meta($user_id,"relationship", $data["relationship"]);
            }
            
            return $data["user_id"];
        }
    }

    static function update_user_password($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $password = $request->get_param("password");
        $user_id = $request->get_param("user_id");
        $set_password = wp_set_password($password, $user_id);

        $response["user_id"] = $user_id;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function is_user_institute_parent(){

        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        if(current_user_can('administrator')){
            return false;
        }

        $roles = get_field("additional_roles","user_".$user_id);
        // if(in_array("instituteparent", $roles)) return true;
        // else false;
        if (is_array($roles) && in_array("instituteparent", $roles)) {
            return true;
        } else {
            return false;
        }

    }
}