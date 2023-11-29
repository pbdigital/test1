<?php 
namespace Safar;
use Buddyboss\LearndashIntegration\Library\SyncGenerator;
use uncanny_learndash_groups\MigrateLearndashGroups;
use uncanny_learndash_groups\LearndashGroupsPostEditAdditions;
use Safar\SafarCourses;
use Safar\SafarAttendance;
use Safar\SafarRewards;
use DateTime;

class SafarSchool extends Safar{

    static $bypass_transient = false;

    static function import_csv($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $response = [];
        $upload_dir = wp_upload_dir();
        $base_upload_dir = $upload_dir["basedir"];
        $school_onboarding_csv_folder = "school_onboarding_csv";
        $csv_file_name = uniqid()."_".time()."_".$user_id.".csv";

        if(!is_dir($base_upload_dir."/".$school_onboarding_csv_folder)){
            mkdir($base_upload_dir."/".$school_onboarding_csv_folder, 0775);
        }
        $tempFile = $_FILES['file']['tmp_name']; 
        $targetFile = $base_upload_dir."/".$school_onboarding_csv_folder."/".$csv_file_name;
        move_uploaded_file($tempFile,$targetFile);

        if(is_file($targetFile)){
            $handle = fopen($targetFile, "r");

            $teachers_rows = [];
            $error = false;
            $error_message = [];
            $teacher_ids = [];
            $row = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row > 0 ){
                    if (filter_var($data[4], FILTER_VALIDATE_EMAIL)) {
                        // peform add user here
                        $firstname = $data[0];
                        $lastname = $data[1];
                        $username = $data[2];
                        $password = $data[3];
                        $email = $data[4];

                        if(!empty($username) && !empty($password) && !empty($email) ){
                            $result = wp_create_user( $username, $password, $email );

                            $is_exists = get_user_by("login", $username);
                            if(!$is_exists){
                                $is_exists = get_user_by("email", $email);
                            }
                            
                            if($is_exists){
                                if(!in_array($is_exists->data->ID, $teacher_ids)){
                                    $teachers_rows[] = [
                                        "first_name" => $firstname,
                                        "last_name" => $lastname,
                                        "username" => $password,
                                        "email" => $email,
                                        "user_id" => $is_exists->data->ID,
                                        "user_existing" => true
                                    ];
                                }

                                $teacher_ids[] = $is_exists->data->ID;
                            }else{
                                if(is_wp_error($result)){
                                    $error_message[] = "Error adding ".$username.". ".$result->get_error_message();
                                    $error = true;
                                }else{
                                    if(!in_array($result, $teacher_ids)){
                                        $teachers_rows[] = [
                                            "first_name" => $firstname,
                                            "last_name" => $lastname,
                                            "username" => $password,
                                            "email" => $email,
                                            "user_id" => $result,
                                            "user_existing" => false
                                        ];
                                    }
                                    $teacher_ids[] = $result;
                                }
                            }
                        }
                        
                    }else{
                        $error = true;
                        $error_message[] = "Invalid email address \"".$data[4]."\"";
                    }
                }
                $row++;
            }
        
            
            if(!empty($teachers_rows)){
                
                //$learndash_groups = get_posts(["post_type"=>"groups", "post_parent"=>0]);
      
                $school_resp = self::get_user_school_id($user_id);
                $learndash_parent_group_id = $school_resp["learndash_parent_group_id"];
                $catch_all_group_id = $school_resp["catch_all_group_id"];
                $teacher_group_id = $school_resp["teacher_group_id"];
                
                //print_r(["meta"=>$group_meta, $user_meta, $school_resp]);
               
                foreach($teachers_rows as $teacher){
                    if(!empty($catch_all_group_id) && $teacher["user_id"]){

                        wp_update_user([
                            'ID' => $teacher["user_id"], // this is the ID of the user you want to update.
                            'first_name' => $teacher["first_name"],
                            'last_name' => $teacher["last_name"],
                        ]);

                        // update primary role to Group Leader
                        $user = new \WP_User( $teacher["user_id"]);
	                    $user->set_role( "group_leader" );

                        // update secondary role to teacher
                        get_field("user_role","user_".$teacher["user_id"],"teacher");

                        // add user to learndash Entire School/Catch all group as group leader
                        $resp = ld_update_leader_group_access( $teacher["user_id"], $catch_all_group_id );

                        // also add user to the teacher group container
                        //ld_update_leader_group_access( $teacher["user_id"], $teacher_group_id );
                        
                        $get_teachers_group = self::get_teachers_group($user_id);
                        if(!empty($get_teachers_group)){
                            
                            foreach($get_teachers_group as $group_id){
                                $teachers = learndash_get_groups_user_ids( $group_id, true );
                                $teachers[] = $teacher["user_id"];
                                learndash_set_groups_users($group_id, $teachers );
                            }
                        }

                        update_user_meta($teacher["user_id"],"user_role","teacher");

                        //print_r([$catch_all_group_id, $teacher["user_id"], $resp ]);
                        /*
                        $member = new \BP_Groups_Member($teacher["user_id"], $catch_all_group_id );
                        $resp = $member->promote( "admin" );

                        $promote = groups_promote_member($teacher["user_id"], $catch_all_group_id, "admin");
                        */
                    }
                }
            }


            $response["teachers"] = $teachers_rows;
            $response["has_errors"] = $error;
            $response["error_message"] = $error_message;
        }

        $response = new \WP_REST_Response($response);
        return $response;
    }


    static function update_school($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $school_resp = self::get_user_school_id($user_id);
        $learndash_parent_group_id = $school_resp["learndash_parent_group_id"];
        $catch_all_group_id = $school_resp["catch_all_group_id"];

        // update school name and logo
        $school_name = $request->get_param("school_name");
        $logo_id = $request->get_param("logo_attachment_id");
        wp_update_post(["ID"=>$learndash_parent_group_id, "post_title"=>$school_name]);
        set_post_thumbnail($learndash_parent_group_id, $logo_id);

        // UPdate address , ACF
        $address_line1 = $request->get_param("address_line1");
        $address_line2 = $request->get_param("address_line2");
        $city = $request->get_param("city");
        $state = $request->get_param("state");
        $zip_postal = $request->get_param("zip_postal");
        $country = $request->get_param("country");

        $activity_feed_status = $request->get_param("activity_feed_status");
        $media_status = $request->get_param("media_status");
        $document_status = $request->get_param("document_status");
        $video_status = $request->get_param("video_status");
        $password = $request->get_param("password");

        
     
        update_post_meta( $learndash_parent_group_id, "school_onboarding_address_line_1", $address_line1);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_address_line_2", $address_line2);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_city", $city);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_state", $state);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_country", $zip_postal);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_zipcode", $country);

        // update contact
        $contact_name = $request->get_param("contact_name");
        $contact_email_address = $request->get_param("contact_email_address");
        $contact_phone = $request->get_param("contact_phone");
        update_post_meta( $learndash_parent_group_id, "school_onboarding_contact_name", $contact_name);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_contact_email_address", $contact_email_address);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_contact_phone", $contact_phone);

        // update facial features
        $with_facial_features = $request->get_param("with_facial_features");
        update_post_meta( $learndash_parent_group_id, "school_onboarding_facial_features", $with_facial_features);

        // update welcome email
        $teacher_welcome_subject = $request->get_param("teacher_welcome_subject");
        $student_welcome_subject = $request->get_param("student_welcome_subject");
        $teacher_welcome_body = $request->get_param("teacher_welcome_body");
        $student_welcome_body = $request->get_param("student_welcome_body");
        update_post_meta( $learndash_parent_group_id, "school_onboarding_teacher_welcome_email_subject", $teacher_welcome_subject);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_teacher_welcome_email_body", $teacher_welcome_body);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_student_welcome_email_subject", $student_welcome_subject);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_student_welcome_email_body", $student_welcome_body);

        update_user_meta($user_id, "completed_user_onboarding", true);

        $groups = groups_get_user_groups( $user_id );
        #print_r([$entry, $groups]);

        if(!empty($groups)){
            foreach($groups["groups"] as $gid){
                $group = groups_get_group($gid);
    
                if(empty($group->parent_id)){
                   
                    groups_update_groupmeta( $gid, "activity_feed_status", $activity_feed_status);
                    groups_update_groupmeta( $gid, "media_status", $media_status);
                    groups_update_groupmeta( $gid, "document_status", $document_status);
                    groups_update_groupmeta( $gid, "video_status", $video_status);
                }
            }
        }
        
        $resonse["success"] = true;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function user_select_institute($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $instituteid = $request->get_param("instituteid");
        update_user_meta($user_id,"selected_institute",$instituteid);

        $response["user_id"] = $user_id;
        $response["instituteid"] = $instituteid;
        $response["success"] = true;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function get_user_school_id($request = []){
        $user_id = parent::pb_auth_user($request);

        $learndash_parent_group_id = get_user_meta($user_id,"selected_institute", true);

        // if user has only one institute, automatically set it as default institute
        //delete_user_meta($user_id,"selected_institute");
        if(empty($learndash_parent_group_id)){
            $institutes = self::get_user_institutes( $user_id );

            if(!empty($institutes)){
                if( sizeof($institutes) == 1 ){
                    update_user_meta($user_id,"selected_institute", $institutes[0]->ID );
                    $learndash_parent_group_id = $institutes[0]->ID;
                    //\Safar\Safar::debug($institutes);
                }
            }else{
                $is_user_teacher = \Safar\SafarUser::is_user_teacher( );
                if($is_user_teacher){
                    $classrooms = learndash_get_administrators_group_ids( get_current_user_id(), true );
                    foreach($classrooms as $class_id){
                        $classroom = get_post($class_id);
                        update_user_meta($user_id,"selected_institute", $classroom->post_parent );
                    }
                    
                }
            }

        }

        if(empty($learndash_parent_group_id)) return false;

        $user_meta = get_user_meta($user_id);

        $catch_all_group_id = 0;

        /*foreach($user_meta as $k=>$v){
            if( strpos($k, "learndash_group_leaders_") !== false){
                $group_post = get_post($v[0]);
                if(empty($group_post->post_parent)){
                    $learndash_parent_group_id = $group_post->ID;
                    $catch_all_group = get_field("entire_school_container", $learndash_parent_group_id);
                    $catch_all_group_id = $catch_all_group->ID;
                    
                }
            }
        }*/
        $catch_all_group = get_field("entire_school_container", $learndash_parent_group_id);
        $catch_all_group_id = $catch_all_group->ID;

        // teachers container
        $child_groups = array(
            'post_type'      => 'groups',   // Replace 'page' with the desired post type, if different
            'post_parent'    => $learndash_parent_group_id,   // Replace 231043 with the parent ID you want to query
            'posts_per_page' => -1,       // Set the number of posts to retrieve. Use -1 for all.
        );
        
        $child_groups =  get_posts($child_groups);
        $teacher_group_id = 0;
        foreach($child_groups as $cg){
            $is_teacher_group = get_post_meta($cg->ID,"is_teachers_classroom",true);
            if( $is_teacher_group == "yes" || $is_teacher_group[0] == "yes"){
                $teacher_group_id = $cg->ID;
            }
        }

        $response = ["learndash_parent_group_id"=>$learndash_parent_group_id, "catch_all_group_id"=>$catch_all_group_id, "teacher_group_id"=>$teacher_group_id];

        return $response;
    }

    static function get_user_institutes( $request ){
        $user_id = parent::pb_auth_user($request);
        $user_meta = get_user_meta($user_id);
        $institutes = [];
        $institute_ids = [];
        foreach($user_meta as $k=>$v){
            if( strpos($k, "learndash_group_leaders_") !== false){
                $group_post = get_post($v[0]);
                if(empty($group_post->post_parent)){
                    $institute_ids[] = $group_post->ID;
                }
            }
        }

        // if user is a parent add group_tag family group
        $is_parent = \Safar\SafarFamily::is_user_parent( );
        $is_user_teacher = \Safar\SafarUser::is_user_teacher( );
        
        if(!empty($institute_ids)){
            $user_id = parent::$user_id;

            $transient_key = "user_institutes_".$user_id;
            if( self::$bypass_transient ){
                delete_transient($transient_key);
            }

            $institutes = get_transient($transient_key);
            if(empty($institutes)){
                $institutes = get_posts(["post_type"=>"groups", 
                                            "post_status" => "publish",
                                            "post__in"=> $institute_ids, 
                                            "numberposts" => -1,
                                            "orderby" => "ID",
                                            "order" => "asc"]);

                if(!empty($institutes)){
                    foreach($institutes as $key=>$group){
                        $institutes[$key]->avatar = get_field("class_avatar", $group->ID);
                        $institutes[$key]->cover_photo = get_field("class_cover_photo", $group->ID);
                        $institutes[$key]->school_data = self::get_school_data($group->ID);

                        if($is_parent){
                            wp_set_object_terms($group->ID, 'family-group', 'ld_group_tag', true);
                        }else{
                            if(!$is_user_teacher){
                                wp_set_object_terms($group->ID, 'overall-school', 'ld_group_tag', true);
                            }
                        }
                    }
                }
                set_transient( $transient_key, $response, 1800 ); // 3600 = 1 hour
            }
            return $institutes;
        }else{
            return false;
        }
    }
    
    

    static function get_school_data($school_id){
        if(!empty($school_id)){
            $user_id = parent::$user_id;
            $rand = uniqid();
            $transient_key = "school_data_".$user_id."_".$school_id;
            //if( self::$bypass_transient ){
                delete_transient($transient_key);
            //}

            $school_data = get_transient($transient_key);
            $response = [];
            if(empty($school_data)){
                $school_post = get_post($school_id);
                //$school_post->avatar = get_field("class_avatar", $school_id);

                if (filter_var( get_post_meta("class_avatar", $school_id) , FILTER_VALIDATE_URL)) {
                    $school_post->avatar = get_post_meta($school_id, "class_avatar", true);
                } else {
                    $school_post->avatar = get_field("class_avatar", $school_id);
                }

                if (filter_var( get_post_meta($school_id, "class_cover_photo", true) , FILTER_VALIDATE_URL)) {
                    $school_post->cover_photo = get_post_meta($school_id, "class_cover_photo", true);
                } else {
                    $school_post->cover_photo = get_field("class_cover_photo", $school_id);
                }

                $school_category = wp_get_post_terms($school_id,'ld_group_tag',);
                foreach($school_category as $school_cat){
                    if($school_cat->slug == "overall-school"){
                        $school_post->avatar = "";
                        $school_logo  = get_the_post_thumbnail_url($school_id);
                        if(!empty($school_logo)) $school_post->avatar = $school_logo;
                        $school_post->facial_feature = get_post_meta($school_id,"school_onboarding_facial_features", true);
                    }
                }

                $school_meta = get_post_meta($school_id);
                $rsstudents = learndash_get_groups_user_ids($school_id);
                $rsteachers = learndash_get_groups_administrator_ids($school_id);
                /*if($school_id == 231263){
                    echo "<pre>";
                        print_r(["teachers"=>$rsteachers]);
                    echo "</pre>";
                }*/
                $i = 0;
                $teachers = [];
                foreach($rsteachers as $teacher){
                    $teachers[$i] = get_user_by("id", $teacher);

                    $teachers[$i]->data->first_name = get_user_meta($teachers[$i]->data->ID, "first_name", true );
                    $teachers[$i]->data->last_name = get_user_meta($teachers[$i]->data->ID, "last_name", true ); 
                    $teachers[$i]->data->avatar_url = get_avatar_url($teachers[$i]->data->ID); 
                    $teachers[$i]->data->last_login = wpb_lastlogin($teachers[$i]->data->ID); 

        
                    $base_date = strtotime($teachers[$i]->data->last_login);
                    $currentDate = time();
                    $daysDifference = ($currentDate - $base_date) / (60 * 60 * 24);

                    $teachers[$i]->data->last_login_numeric = $daysDifference;
                    $i++;
                }

                $students = [];
                $i = 0;
                
                foreach($rsstudents as $student){
                    $students[$i] = get_user_by("id", $student);

                    $students[$i]->data->first_name = get_user_meta($students[$i]->data->ID, "first_name", true );
                    $students[$i]->data->last_name = get_user_meta($students[$i]->data->ID, "last_name", true ); 
                    $students[$i]->data->avatar_url = get_avatar_url($students[$i]->data->ID);
                    $students[$i]->data->last_login = wpb_lastlogin($students[$i]->data->ID); 
                    $students[$i]->data->gender = get_user_meta($students[$i]->data->ID, "gender", true );
                    $students[$i]->data->date_of_birth = get_user_meta($students[$i]->data->ID, "date_of_birth", true );
                    $students[$i]->data->family_id = get_user_meta($students[$i]->data->ID, "family_id", true );


                    // get student badges
                    $earned_badges = gamipress_get_user_achievements( array(
                        'user_id'           => $students[$i]->data->ID,
                        'achievement_type'  => "badges",
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
                    $students[$i]->data->badges = $badges;
                    // end student badges


                    $base_date = strtotime($students[$i]->data->last_login);
                    $currentDate = time();
                    $daysDifference = ($currentDate - $base_date) / (60 * 60 * 24);

                    $students[$i]->data->last_login_numeric = $daysDifference;

                    /* 
                    Additional fields for Attendance and Award System
                    */
 
                    $students[$i]->data->avatar = [
                        "headImage" => $students[$i]->data->avatar_url,
                        "fullImage" => get_user_meta( $students[$i]->data->ID , "custom_avatar_full", true ) 
                    ];
                    
                    $students[$i]->data->attendance = SafarAttendance::get_student_attendance_in_classroom($students[$i]->data->ID, $school_id, date("Y-m-d"));

                    $rewards = SafarRewards::get_rewards_history_by("student", $students[$i]->data->ID, $school_id); 
                     
                    $students[$i]->data->rewards["totalPoints"] = ( empty($rewards["totalpoints"])) ? 0:$rewards["totalpoints"];
                    $students[$i]->data->rewards["instances"] = [];
                    foreach($rewards["rewards"] as $rewards){
                        foreach($rewards as $date=>$reward){
                            $teacher_id = $reward->teacher_id;
                            $students[$i]->data->rewards["instances"][] = $reward;
                        }
                    }
                

                    $login_key = bin2hex(random_bytes(16));
                    update_user_meta($students[$i]->data->ID,"student_login_key",$login_key);
                    $students[$i]->data->login_key = md5($students[$i]->data->ID);//$login_key;
                    $i++;
                }
                

                $course_ids = learndash_group_enrolled_courses($school_id);
        
                $courses = [];
                if(!empty($course_ids)){
                    $courses = get_posts(["post__in"=>$course_ids, 
                                            "orderby" => "menu_order", 
                                            "order" => "asc", 
                                            "post_type" => "sfwd-courses",
                                            "numberposts" => -1 , 
                                            "suppress_filters"=> true]);
                }
                
                $terms = get_terms( [
                    'taxonomy' => 'ld_group_category',
                ] );
                $available_courses = [];
                $group_courses  = [];
                foreach($terms as $eterm){
                    $groups = get_posts([
                        'post_type' => 'groups',
                        "orderby" => "menu_order", 
                        "order" => "asc", 
                        "numberposts"=>-1,
                        'tax_query' => [
                            [
                                'taxonomy' => 'ld_group_tag',
                                'field'    => 'name',
                                'terms'    => 'Course content groups',
                            ],
                            [
                                'taxonomy' => 'ld_group_category',
                                'field'    => 'slug',
                                'terms'    => $eterm->slug,
                            ],
                        ],
                    ]);
                    if(!empty($groups)){
                        foreach($groups as $key=>$group){
                            $groups[$key]->courses = learndash_group_enrolled_courses($group->ID);

                             // check if all courses within the group matches with course_ids or the school course
                            // if yes then thats the group_courses set for the school
                            $found_all = true;
                            foreach( $groups[$key]->courses  as $eg_course){

                                
                                if(!in_array($eg_course, $course_ids)){
                                    $found_all = false;
                                }
                            }

                            if($found_all){
                                $groups[$key]->is_group_course =true;
                            }else{
                                $groups[$key]->is_group_course =false;
                            }

                            if($groups[$key]->is_group_course){
                                $group_courses[] = $groups[$key];
                            }
                            
                        }
                       

                        $available_courses[] = [   
                            "category_id" => $eterm->term_id,
                            "category" => $eterm->name,
                            "groups" => $groups 
                        ];
                    }
                }

                $generator = new SyncGenerator( "", $school_id );
                $bpGroupId = $generator->getBpGroupId();

                $bp_group_settings["activity_feed"] = groups_get_groupmeta( $bpGroupId, "activity_feed_status");
                $bp_group_settings["photos"] = groups_get_groupmeta( $bpGroupId, "media_status");
                $bp_group_settings["documents"] = groups_get_groupmeta( $bpGroupId, "document_status");
                $bp_group_settings["videos"] = groups_get_groupmeta( $bpGroupId, "video_status");

                // check if classroom has attendance for the day
                $has_attendance = ( SafarAttendance::classroom_has_attendance($school_id, date("Y-m-d")) ) ? true:false;
                $attendance_details["date"] = date("Y-m-d");
                $attendance_details["statistics"] = [];

                $response = [   "post"=>$school_post, 
                                "meta" => $school_meta, 
                                "transient" => false, 
                                "teachers" => $teachers,
                                "students" =>$students, 
                                "courses" => $courses,
                                "group_courses" => $group_courses,
                                "school_id" => $school_id,
                                "categories" =>  wp_get_post_terms( $school_id, "ld_group_category"),
                                "settings" => $bp_group_settings,
                                "bpGroupId" => $bpGroupId,
                                "has_attendance" => $has_attendance,
                                "attendance_details" => $attendance_details,
                                "tag" => wp_get_post_terms($school_id, "ld_group_tag"),
                                "is_teachers_classroom" => (!empty(get_post_meta($school_id, "is_teachers_classroom", true)) ) ? true:false,
                            ];
                set_transient("school_data_".$user_id."_".$school_id, $response, 1800 ); // 3600 = 1 hour
            }else{
                $response = $school_data;
                $response["transient"] = true;
            }

            return $response;

        }else{
            return false;
        }
    }

    static function get_single_classroom($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }   
        $school_id = $request->get_param("id");
        self::$bypass_transient = $request->get_param("bypasstransient");

        $response =  self::get_school_data($school_id);
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function get_student_classroom_ids($all_classrooms, $student_id){
        $classroom_ids = [];
        foreach( $all_classrooms as $child_school){
            if(!empty($child_school->school_data)){
                if(!empty($child_school->school_data["students"])){
                    foreach($child_school->school_data["students"] as $student){
                        if($student_id == $student->data->ID) $classroom_ids[] = $child_school->ID;
                    }
                }
            }
        }

        return $classroom_ids;
    }

    static function get_teacher_classroom_ids($all_classrooms, $teacher_id){
        $classroom_ids = [];
        foreach( $all_classrooms as $child_school){
            if(!empty($child_school->school_data)){
                if(!empty($child_school->school_data["teachers"])){
                    foreach($child_school->school_data["teachers"] as $teacher){
                        if($teacher_id == $teacher->data->ID) $classroom_ids[] = $child_school->ID;
                    }
                }
            }
        }

        return $classroom_ids;
    }

    static function update_buddyboss_group_permalink($group_id, $new_permalink) {
        global $wpdb;
    
        // Update the group slug in the database
        $wpdb->update(
            $wpdb->prefix . 'bp_groups',
            array('slug' => $new_permalink),
            array('id' => $group_id),
            array('%s'),
            array('%d')
        );
    
        // Update any links or references to the old permalink (optional)
        // This step depends on your specific needs and the customizations you've made.
    
        // Flush rewrite rules to ensure the new permalink takes effect
        flush_rewrite_rules();
    }

    static function get_classrooms($parent_school_id){
        if(!empty($parent_school_id)){
            $user_id = parent::$user_id;

            $transient_key = "school_data_".$parent_school_id."_".$user_id;
            if( self::$bypass_transient ){
                delete_transient($transient_key);
            }

            $child_groups = get_transient($transient_key);
            if(empty($child_groups)){
                $child_groups = get_posts(["post_type"=>"groups", 
                                            "post_status" => "publish",
                                            "post_parent"=>$parent_school_id, 
                                            "numberposts" => -1,
                                            "orderby" => "post_title",
                                            "order" => "ASC"
                                        ]);

                if(!empty($child_groups)){
                    foreach($child_groups as $key=>$group){
                        $child_groups[$key]->avatar = get_field("class_avatar", $group->ID);
                        
                        if (filter_var( get_post_meta($group->ID, "class_cover_photo", true) , FILTER_VALIDATE_URL)) {
                            $child_groups[$key]->cover_photo = get_post_meta($group->ID, "class_cover_photo", true);
                        } else {
                            $child_groups[$key]->cover_photo = get_field("class_cover_photo", $group->ID);
                        }

                        $child_groups[$key]->school_data = self::get_school_data($group->ID);
                        $generator = new SyncGenerator( "", $group->ID);
                        $bpGroupId = $generator->getBpGroupId();
                        $child_groups[$key]->bp_group_id = $bpGroupId;

                        // bug fix for https://app.clickup.com/t/865d36jz9
                        // Sep 16, 2023
                        if(empty($bpGroupId)){ // create buddyboss group if no group is created
                            $newGroup  = groups_create_group(["name"=>$group->post_title]);
                            $ldGroupId = $group->ID;
                            $generator = new SyncGenerator( $newGroup, $ldGroupId );
                            $generator->updateBuddypressGroup( $ldGroupId, $newGroup );
                            $generator->fullSyncToBuddypress();
                            $bpGroupId = $newGroup;
                        }
                        
                        if(!empty($bpGroupId)){
                            $group = groups_get_group($bpGroupId);

                            /* start code to fix bug on Buddyboss where it creates groups with the same slug*/
                            $group_slug = $group->slug;
                            // Create a query to retrieve the group by slug.
                            $args = array(
                                'name' => $group_slug, // Use 'name' to query by slug.
                                'post_type' => 'groups', // Replace with your custom post type name.
                                'post_status' => 'publish', // You may adjust the post status as needed.
                                'posts_per_page' => 1, // Limit the result to 1 post.
                            );
                            $groups_with_slug = get_posts($args);
                            if(sizeof($groups_with_slug) > 1 ){
                                // Update the group slug
                                if ($group) {
                                    #$new_slug = str_replace($bpGroupId."-","",$group->slug);
                                    $new_slug = $bpGroupId."-".$group->slug;
                                    $group->slug = $new_slug;
                                    do_action( 'groups_update_group', $bpGroupId, $group );
                                    self::update_buddyboss_group_permalink($bpGroupId, $new_slug);
                                }
                            }
                            /*end  code to fix bug on Buddyboss where it creates groups with the same slug*/

                            /* add school admins to the group as organizer 
                                https://app.clickup.com/t/865d66k2f
                            */
                            if(get_user_meta($user_id,"user_role",true) == "school admin"){
                                $is_group_admin = groups_is_user_admin($user_id, $bpGroupId);
                                if(!$is_group_admin){
                                    groups_join_group( $bpGroupId, $user_id );
                                    $member = new \BP_Groups_Member( $user_id, $bpGroupId );
                                    $success =  $member->promote( "admin" );
                                }     
                            }
                            /* end add school admins to the group as organizer*/

                            $child_groups[$key]->bp_url = bp_get_group_permalink($group);
                        }else{
                            $child_groups[$key]->bp_url = "";
                        }
                        wp_set_object_terms($group->ID, 'child-classroom', 'ld_group_tag', true);
                    }
                }
                set_transient($transient_key, $response, 1800 ); // 3600 = 1 hour
            }
            return $child_groups;
        }else{
            return false;
        }
    }

    static function get_school_details( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        if(!empty($request)){
            self::$bypass_transient =  filter_var( $request->get_param("bypasstransient") , FILTER_VALIDATE_BOOLEAN);
        }

        $user_schools = self::get_user_school_id($user_id);
        $response["school"] = $user_schools;
        $response["catch_all_school_data"] = self::get_school_data($user_schools["catch_all_group_id"]);
        $response["classrooms"] = self::get_classrooms($user_schools["learndash_parent_group_id"]);
        $response["parent_school"] = self::get_school_data($user_schools["learndash_parent_group_id"]);

        

        $teachers = [];
        $teacher_ids = [];
        $subjects_background_color = get_field("subjects_background_color","option");
        $all_classrooms = []; // including parent group/school
        $all_classrooms = $response["classrooms"]; 
        $parent_school = get_post($user_schools["learndash_parent_group_id"]);
        $parent_school->school_data = $response["parent_school"];
        $all_classrooms[] = $parent_school;

        // start fix https://app.clickup.com/t/865d3w6ae
        // for "teachers" classrooms make add teachers as students          

        // get all teacher ids 
        $all_teacher_ids = [];
        foreach( $all_classrooms as $child_school){
            if(!empty($child_school->school_data)){
                if(!empty($child_school->school_data["teachers"])){
                    foreach($child_school->school_data["teachers"] as $teacher){
                        if(get_user_meta($teacher->data->ID,"user_role",true) != "school admin"){
                            $all_teacher_ids[] = $teacher->data->ID;
                        }
                    }
                }
            }
        }
        if(!empty($response["classrooms"])){
            foreach($response["classrooms"] as $classroom){
                $is_teachers_classroom = get_post_meta($classroom->ID, "is_teachers_classroom", true);
                if(!empty($is_teachers_classroom)){
                    $teacher_to_student = [];
                    $teacher_ids = learndash_get_groups_administrator_ids( $classroom->ID );
                    foreach($teacher_ids as $teacher_id){
                        $teacher_role = get_user_meta($teacher_id,"user_role",true);
                        if( $teacher_role == "teacher" ){
                            $teacher_to_student[] = $teacher_id;
                        }
                    }
                    if(!empty($teacher_to_student)){
                        //learndash_set_groups_users($classroom->ID, $teacher_to_student); // convert teachers into students
                    }
                    // make sure all teachers are added to Teachers Classroom a a student
                    // get values from $all_teacher_ids above
                    $current_student_teachers = learndash_get_groups_user_ids($classroom->ID);
                    //15234
                    $current_student_teachers = array_merge($current_student_teachers, $all_teacher_ids);
                    learndash_set_groups_users($classroom->ID, $current_student_teachers); // convert teachers into students

                    // set parent school admin as teachers
                    $admin_new_ids = [];
                    foreach($response["parent_school"]["teachers"] as $admin){
                        $admin_new_ids[] = $admin->ID;
                    }
                    
                    learndash_set_groups_administrators($classroom->ID, $admin_new_ids);


                    //print_r(["admin_ids"=>learndash_get_groups_administrator_ids($classroom->ID), "ewadmin_ids"=>$admin_new_ids, "classroomid"=>$classroom->ID]); die();

                }
            }
        }
        // end fix https://app.clickup.com/t/865d3w6ae
        /*
        echo "<Pre>";
            foreach($response["classrooms"] as $testcl){
                if($testcl->ID == 231263){
                    print_r(["classroombefore"=>$testcl->school_data["teachers"]]);
                }
            }
        echo "</pre>";
        */
        
        foreach( $all_classrooms as $child_school){
            if(!empty($child_school->school_data)){
                if(!empty($child_school->school_data["teachers"])){

                    // start https://app.clickup.com/t/865d3w6ae
                    $is_teacher_group = get_post_meta($child_school->ID,"is_teachers_classroom",true);
                    $classroom_teachers = $child_school->school_data["teachers"];

                    if($is_teacher_group){
                        // if its a teachers group add all students from that group to the $teachers array
                        $student_teacher_ids = learndash_get_groups_user_ids($child_school->ID);
                        $classroom_teachers = array_merge(
                            $child_school->school_data["teachers"],
                            $child_school->school_data["students"]
                        );
                        //$child_school->school_data["teachers"] = array_unique($child_school->school_data["teachers"]);
                    }
                    // end https://app.clickup.com/t/865d3w6ae
                    
                    foreach($classroom_teachers as $teacher){
                        if(get_user_meta($teacher->data->ID,"user_role",true) != "school admin"){
                            if(!in_array($teacher->data->ID,$teacher_ids)){
                                // get all groups for the teacher
                                $teacher_group_ids = self::get_teacher_classroom_ids($all_classrooms,$teacher->data->ID);
                                if(!empty($teacher_group_ids)){
                                    $teacher_group_posts = get_posts(['post_type' => 'groups', 'post__in'=>$teacher_group_ids, 'post_status'=>'publish']);

                                    foreach($teacher_group_posts as $key=>$group){
                                        $teacher_group_posts[$key]->category = wp_get_post_terms( $group->ID, "ld_group_category");
                                        if(!empty($teacher_group_posts[$key]->category)){
                                            foreach($teacher_group_posts[$key]->category as $ckey=>$cat){
                                                $bg_color = "";
                                                foreach($subjects_background_color as $bg){
                                                    if($bg["subject"] == $cat->term_id) $bg_color = $bg["background_color"];
                                                }
                                                $teacher_group_posts[$key]->category[$ckey]->bg_color = $bg_color;
                                            }
                                        }
                                    }
                                }else{
                                    $teacher_group_posts = [];
                                }

                                $teacher->data->classrooms = $teacher_group_posts;
                                $teachers[] = $teacher;
                                $teacher_ids[] = $teacher->data->ID;
                            }
                        }
                    }
                }
            }
        }
        /*
        echo "<Pre>";
            foreach($response["classrooms"] as $testcl){
                if($testcl->ID == 231263){
                    print_r(["classroomsafter"=>$testcl->school_data["teachers"]]);
                }
            }
        echo "</pre>";
        */
        
        $response["teachers"] = $teachers;

        
        $students = [];
        $student_ids = [];

        $is_institute_parent = \Safar\SafarFamily::is_user_institute_parent();
        $institute = \Safar\SafarFamily::get_institute_by_parent_id($user_id);
        
        foreach( $all_classrooms as $child_school){
            if(!empty($child_school->school_data)){
                if(!empty($child_school->school_data["students"])){
                    foreach($child_school->school_data["students"] as $student){

                        if(get_user_meta($student->data->ID,"user_role",true) != "teacher"){ // filtered hardcode because on "techers classroom" teachers are set as students
                            if(!in_array($student->data->ID,$student_ids)){

                                // get all groups for the student
                                $filter_only_institute = "";
                                if( $is_institute_parent ){
                                    $student_group_ids = learndash_get_users_group_ids($student->data->ID);
                                }else{
                                    $student_group_ids = self::get_student_classroom_ids($all_classrooms,$student->data->ID);
                                }
                                if(!empty($student_group_ids)){

                                    $where_post_parent = "";
                                    if($is_institute_parent){
                                        if(!empty($institute)) $where_post_parent = " AND post_parent=".$institute["post"]->ID;
                                    }
                                    
                                    $post_type = 'groups';
                                    $post_status = 'publish';
                                    $in_ids = implode(',', array_map('intval', $student_group_ids));
                                    $query = "SELECT *
                                            FROM ".parent::wpdb()->posts." 
                                            WHERE post_type = %s
                                            AND ID IN ($in_ids)
                                                $where_post_parent
                                            AND post_status = %s";
                                    
                                    $prepared_query = parent::wpdb()->prepare($query, $post_type, $post_status);
                                    $student_group_posts = parent::wpdb()->get_results($prepared_query);
                                
                                    foreach($student_group_posts as $key=>$group){
                                        $student_group_posts[$key]->category = wp_get_post_terms( $group->ID, "ld_group_category");
                                        if(!empty($student_group_posts[$key]->category)){
                                            foreach($student_group_posts[$key]->category as $ckey=>$cat){
                                                $bg_color = "";
                                                foreach($subjects_background_color as $bg){
                                                    if($bg["subject"] == $cat->term_id) $bg_color = $bg["background_color"];
                                                }
                                                $student_group_posts[$key]->category[$ckey]->bg_color = $bg_color;
                                            }
                                        }
                                    }

                                }else{
                                    $student_group_posts = [];
                                }
                                $student->data->classrooms = $student_group_posts;
                                
                                $students[] = $student;
                                $student_ids[] = $student->data->ID;
                            }
                        }
                    }
                }
            }
        }

        // add students who where added to the catch all group
        if(!empty($response["catch_all_school_data"]["students"])){
            foreach($response["catch_all_school_data"]["students"] as $catch_all_student){
                if(!in_array($catch_all_student->data->ID, $student_ids)){
                    if(get_user_meta($catch_all_student->data->ID,"user_role",true) != "school admin"){
                        $students[] = $catch_all_student;
                    }
                }
            }
        }        
       
        // get families
        $families = [];
        $institute_id = $user_schools["learndash_parent_group_id"];
        $table_name = parent::wpdb()->prefix . 'families'; 
        $families = parent::wpdb()->get_results("SELECT * FROM ".$table_name." WHERE institute_id=".$institute_id." ORDER BY id DESC ");

        $institute_family_students = [];
        foreach($families as $key=>$family){
            $parents = [];

            // get students from family
            $family_group = self::get_school_data($family->family_group_id);
            
            

            //$admin_ids = learndash_get_groups_administrator_ids( $family->family_group_id );
            if(!empty($family_group["teachers"])){
               
                foreach($family_group["teachers"] as $user_obj){
                    $aid = $user_obj->ID;
                    $user_data = get_user_by("id", $aid);
                    $parents[] = [  "ID" => $aid, 
                                    "first_name" => $user_obj->data->first_name,
                                    "phone"=> get_user_meta($aid,"phone",true),
                                    "relationship" => get_user_meta($aid, "relationship", true),
                                    "email" => $user_data->data->user_email
                                ];
                }
            }

            $children = [];
            foreach($family_group["students"] as $user_obj){
                $gid = $user_obj->ID;
                $user_data = get_user_by("id", $gid);
                
                $children[] = [  "ID" => $gid, 
                                "first_name" => $user_obj->data->first_name,
                                "last_name" => $user_obj->data->last_name,
                                "email" => $user_obj->data->user_email,
                                "avatar_url" => $user_obj->data->avatar_url
                            ];
                // Add institute family to students list
            
                if(!in_array($gid, $student_ids)){
                    $students[] = $user_obj;
                }
                $student_ids[] = $gid;

                $institute_family_students[$gid][] = $family;
            }

            $families[$key]->children = $children;
            $families[$key]->parents = $parents;
            $families[$key]->group_post = get_post($family->family_group_id);

            
        }

        // clean students make sure there are no duplicates
        // also add to students object their "Institute Family" where they belong
        $students_id = [];
        $new_students = [];
        foreach($students as $student){
            if(!in_array($student->ID, $students_id)){

                $student->institute_families = [];
                if(!empty($institute_family_students[$student->ID])){
                    $student->institute_families = $institute_family_students[$student->ID];
                }

                // remove if role = teacher
                $teacher_role = get_user_meta($student->ID,"user_role",true);
                if($teacher_role != "teacher" && $teacher_role != "school admin"){
                    $new_students[] = $student;
                    $students_id[] = $student->ID;
                }
            }
        }

        $response["students"] = $new_students;
        // fix Sep 12, add students to entire group container
        // bug: remove students to classroom ends up losing access to institute
        if(!empty($user_schools["catch_all_group_id"])){
            $entire_institute_students = learndash_get_groups_user_ids( $user_schools["catch_all_group_id"], true );
            $need_update = false;
            foreach($students as $student){
                if(!in_array($student->data->ID, $entire_institute_students)){
                    $entire_institute_students[] = $student->data->ID;
                    $need_update = true;
                }
            }

            if($need_update){
                learndash_set_groups_users($user_schools["catch_all_group_id"], $entire_institute_students);
            }

        }
        // end fix Sep 12, add students to entire group container


        // cleanup teachers array to remove school admin
        $new_teachers = [];
        foreach($teachers as $teacher){
            $teacher_role = get_user_meta($teacher->ID,"user_role",true);
            if($teacher_role == "teacher" ){
                $new_teachers[] = $teacher;
            }
        }
        // end cleanup teachers
        
        $response["teachers"] = $new_teachers;
        $response["families"] = $families;
        $response["admins"] = $response["parent_school"]["teachers"];
        
        $total_seats     = ulgm()->group_management->seat->total_seats( $user_schools["learndash_parent_group_id"]);
        $children = learndash_get_groups_user_ids($user_schools["learndash_parent_group_id"]);
        $seats_taken = count($children);
        $remaining_seats = $total_seats - $seats_taken;
        $response["total_seats"] = $total_seats;
        $response["remaining_seats"] = $remaining_seats;
        $response["seats_taken"] = $seats_taken;

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function upload_logo( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $file = $_FILES["file"];
        $sideload = wp_handle_sideload(
            $file,
            array(
                'test_form'   => false // no needs to check 'action' parameter
            )
        );
    
        if( ! empty( $sideload[ 'error' ] ) ) {
            // you may return error message if you want
            return false;
        }
    
        // it is time to add our uploaded image into WordPress media library
        $attachment_id = wp_insert_attachment(
            array(
                'guid'           => $sideload[ 'url' ],
                'post_mime_type' => $sideload[ 'type' ],
                'post_title'     => basename( $sideload[ 'file' ] ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ),
            $sideload[ 'file' ]
        );

        $response["attachment_id"] = $attachment_id;
        $response["url"] = wp_get_attachment_image_url($attachment_id);
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function send_test_email( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            //return new \WP_REST_Response('Unauthorized', 401);
        }
        
        $to = $request->get_param("email");
        $subject = $request->get_param("subject");
        $message = $request->get_param("body");

        $message = str_replace(["{teacher_name}","{student_name}"],"John Smith", $message);
        $message = str_replace(["{teacher_username}","{user_name}"],"johnsmith", $message);
        $message = str_replace("{password}","J2JPWD", $message);

        $institutes = self::get_user_institutes( $user_id );
        $single_institute = false;
        if(!empty($institutes)) $single_institute = $institutes[0];
   
        if(!empty($single_institute)) $message = self::get_welcome_email_body(["body"=>$message,"school_id"=>$single_institute->ID]);
        

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $sent = wp_mail($to, $subject, $message, $headers);

        $response["sent"] = $sent;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function preview_email($request){
        
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $institutes = self::get_user_institutes( $user_id );
        $single_institute = false;
        if(!empty($institutes)) $single_institute = $institutes[0];

        $subject = $request->get_param("subject");
        $greetings = $request->get_param("greetings");
        $body1 = $request->get_param("body1");
        $body2 = $request->get_param("body2");

        $out = "";

        $body = $greetings.$body1.$body2;
        if(!empty($single_institute)){
            $out = self::get_welcome_email_body(["body"=>$body,"school_id"=>$single_institute->ID]);
        }
        
        $response["template"] = $out;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function get_welcome_email_body($args){
        $body = $args["body"];
        $school = self::get_school_data($args["school_id"]);
        if(empty($body) && !empty($args["email_type"])){ // email type for student or teacher
            if($args["email_type"] == "teacher"){
                $greetings = get_field("teacher_welcome_email_greetings","option");
                $body = get_field("teacher_welcome_email_body_1","option");
                $body .= get_field("teacher_welcome_email_body_2","option");
                $body .= get_field("teacher_welcome_email_body_3","option");
            }

            if($args["email_type"] == "student"){
                $body = get_field("student_welcome_email_greetings","option");
                $body .= get_field("student_welcome_email_body_1","option");
                $body .= get_field("student_welcome_email_body_2","option");
                $body .= get_field("student_welcome_email_body_3","option");
            }

            if($args["email_type"] == "parent"){
                $body = get_field("family_welcome_email_greetings","option");
                $body = get_field("family_welcome_email_body_1","option");
                $body .= get_field("family_welcome_email_body_2","option");
            }

            if($args["email_type"] == "institute"){
                $body = get_field("institute_welcome_email_greetings","option");
                $body .= get_field("institute_welcome_email_body_1","option");
                $body .= get_field("institute_welcome_email_body_2","option");
            }

            if($args["email_type"] == "institute_parent"){

                
                $institute_family_greetings = get_post_meta( $args["school_id"], "school_onboarding_institute_family_welcome_email_greetings",true );
                $institute_family_welcome_email_body_1 = get_post_meta( $args["school_id"], "school_onboarding_institute_family_welcome_email_body_1",true );
                $institute_family_welcome_email_body_2 = get_post_meta( $args["school_id"], "school_onboarding_institute_family_welcome_email_body_2",true );
                
                $body = (!empty($institute_family_greetings)) ? $institute_family_greetings:get_field("institute_family_welcome_email_greetings","option");
                $body = (!empty($institute_family_welcome_email_body_1)) ? $institute_family_welcome_email_body_1:get_field("institute_family_welcome_email_body_1","option");
                $body .= (!empty($institute_family_welcome_email_body_2)) ? $institute_family_welcome_email_body_2:get_field("institute_family_welcome_email_body_2","option");
            }
        }

        ob_start();
        require("welcome_email_template.php");
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    static function archive_classroom($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $classroom_id = $request->get_param("id");

        wp_update_post(
            [
                'ID'          => $classroom_id,
                'post_status' => 'trash',
            ]
        );

        $response["success"] = true;
        $response["id"] = $classroom_id;
        

        $generator = new SyncGenerator( "", $classroom_id );
        $bpGroupId = $generator->getBpGroupId();
        $response["bpGroupId"] = $bpGroupId;
        $response["delete_bp_group"] = $generator->deleteBpGroup( $bpGroupId );

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function create_new_classroom( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $class_name = $request->get_param("class_name");
        $subjects = $request->get_param("subjects");
        $post_parent = $request->get_param("parent_group_id");
        $post_id = $request->get_param("group_id");
        $teachers_classroom = $request->get_param("teachers_classroom");

        if(!empty($post_id)){
            wp_update_post( ["ID"=>$post_id, "post_title"=>$class_name] );
            wp_set_post_terms($post_id, $subjects, "ld_group_category", false);
            $response["new"] = false;
        }else{
        
            $new_post = array(
                'post_title'    => $class_name,
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_author'   => $user_id,
                'post_type'     => 'groups',
                'post_parent'   => $post_parent
            );
            $parent_bp_id = get_post_meta($post_parent,"_sync_group_id", true);
            $post_id = wp_insert_post( $new_post );

            if(!empty($subjects)){
                foreach($subjects as $sub){
                    wp_set_object_terms( $post_id, intval($sub), 'ld_group_category', true );
                }
            }

            if(!empty($teachers_classroom)){
                update_post_meta($post_id,"is_teachers_classroom","yes");
            }

            $response["new"] = true;

            /* 
            Create Buddyboss Sync
            */
            $newGroup  = groups_create_group(["name"=>$class_name, "parent_id" => $parent_bp_id]);
            $ldGroupId = $post_id;
            $generator = new SyncGenerator( $newGroup, $ldGroupId );
            $generator->updateBuddypressGroup( $ldGroupId, $newGroup );
            $generator->fullSyncToBuddypress();

            $response["bpgroupid"] = $newGroup;
            
        }
        
        $response["success"] = true;
        $response["id"] = $post_id;

        $response["fullysync"] = self::fully_sync_buddyboss_learndash_group($post_id);

        $response = new \WP_REST_Response($response);
        return $response;

    }

    static function update_user( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $first_name = $request->get_param("first_name");
        $last_name = $request->get_param("last_name");
        $email = $request->get_param("email");
        $user_id = $request->get_param("id");
        $type = $request->get_param("type");
        $username = $request->get_param("username");
        $password = $request->get_param("password");
        $gender = $request->get_param("gender");
        $date_of_birth = $request->get_param("date_of_birth");
        $family_id = $request->get_param("family_id");


        if($type == "edit_student"){
            $userdata = [
                'ID'            => $user_id,
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'user_email'    => $email,
                'user_login'    => $username,
                
            ];

            $current_gender = get_user_meta($user_id,"gender",true);
            if($current_gender != $gender){
                update_user_meta($user_id,"force_avatar_update",true);
                delete_user_meta($user_id, "user_avatar");
            }
            if(!empty($password)){
                $userdata["user_pass"] = $password;
            }
            update_user_meta($user_id, "gender", $gender);
            update_user_meta($user_id, "date_of_birth", $date_of_birth);
            update_user_meta($user_id, "family_id", $family_id);
            update_user_meta($user_id,"user_role","student");
        }else{
            $userdata = [
                'ID'            => $user_id,
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'user_email'    => $email
            ];
            if(get_user_meta($user_id,"user_role",true) != "school admin"){
                update_user_meta($user_id,"user_role","teacher");
            }
        }
        
        $response["success"] = wp_update_user( $userdata );
        $response["user_meta"] = get_user_meta($user_id);

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function remove_teacher( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $school_id = $request->get_param("id"); //
        $teacher_id = $request->get_param("teacher_id");
        // ld_update_group_access( $user_id = 0, $group_id = 0, $remove = false )
        // remove access to all classroom/groups including child classrooms

        $classrooms = self::get_classrooms($school_id);
        $classroom_ids[] = $school_id;

        foreach($classrooms as $classroom){
            $classroom_ids[] = $classroom->ID;
        }

        if(!empty($classroom_ids)){
            foreach($classroom_ids as $classroom_id){
                $teachers = learndash_get_groups_administrator_ids( $classroom_id, true );

                $updated_teachers = [];
                foreach($teachers as $eteach){
                    if($eteach != $teacher_id){
                        $updated_teachers[] = $eteach;
                    }
                }
                learndash_set_groups_administrators($classroom_id, $updated_teachers);

                $is_teachers_classroom = get_post_meta($classroom_id, "is_teachers_classroom", true);
                if(!empty($is_teachers_classroom)){
                    $teacher_id_to_remove = $teacher_id;
                    $current_student_teachers = learndash_get_groups_user_ids($classroom_id);
                    $current_student_teachers = array_diff($current_student_teachers, [$teacher_id_to_remove]);
                    learndash_set_groups_users($classroom_id, $current_student_teachers);
                }
            }
        }

       
      

        $response["success"] = true;
        $response["teacher_id"] = $teacher_id;
        $response["new_teachers"] = $new_teachers;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function remove_admin( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $school_id = $request->get_param("id"); //
        $teacher_id = $request->get_param("teacher_id");
        
        $teachers = learndash_get_groups_administrator_ids( $school_id, true );
        $updated_teachers = [];
        foreach($teachers as $eteach){
            if($eteach != $teacher_id){
                $updated_teachers[] = $eteach;
            }
        }
        learndash_set_groups_administrators($school_id, $updated_teachers);

        $response["success"] = true;
        $response["admin_id"] = $teacher_id;
        $response["new_admins"] = $new_teachers;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function remove_student( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $school_id = $request->get_param("id"); //
        $student_id = $request->get_param("student_id");
        // ld_update_group_access( $user_id = 0, $group_id = 0, $remove = false )
        // remove access to all classroom/groups including child classrooms

        $classrooms = self::get_classrooms($school_id);
        $classroom_ids[] = $school_id;

        foreach($classrooms as $classroom){
            $classroom_ids[] = $classroom->ID;
        }
        
        if(!empty($classroom_ids)){
            foreach($classroom_ids as $classroom_id){
                $students = learndash_get_groups_user_ids( $classroom_id, true );

                $updated_students = [];
                foreach($students as $eteach){
                    if($eteach != $student_id){
                        $updated_students[] = $eteach;
                    }
                }
                learndash_set_groups_users($classroom_id, $updated_students);
            }
        }

        // remove student from catchall
        $school_resp = self::get_user_school_id($user_id);
        $classroom_id = $school_resp["catch_all_group_id"];
        $students = learndash_get_groups_user_ids( $classroom_id, true );
        $updated_students = [];
        foreach($students as $eteach){
            if($eteach != $student_id){
                $updated_students[] = $eteach;
            }
        }
        learndash_set_groups_users($classroom_id, $updated_students);
        require_once( ABSPATH.'wp-admin/includes/user.php' );
        wp_delete_user($student_id);

        $response["success"] = true;
        $response["teacher_id"] = $student_id;
        $response["new_students"] = $new_students;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function upload($request){
        $file = $_FILES["file"];
        $upload = wp_handle_upload( $file, array( 'test_form' => false ) );
        $attachment_id = 0;
        // Check if the upload was successful
        if ( ! $upload['error'] ) {

            // Define the attachment post data
            $attachment = array(
                'post_title'   => $file_name,
                'post_content' => '',
                'post_status'  => 'inherit',
                'guid'         => $upload['url'],
            );

            // Insert the attachment post into the database
            $attachment_id = wp_insert_attachment( $attachment, $upload['file'], 0 );

            // Check if the attachment was inserted
            if ( $attachment_id ) {
            }
        }
        
        $response["attachment_id"] = $attachment_id;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function upload_csv($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        
        $upload_dir = wp_upload_dir();
        $base_upload_dir = $upload_dir["basedir"];
        $school_onboarding_csv_folder = "school_onboarding_csv";
        $csv_file_name = uniqid()."_".time()."_".$user_id.".csv";

        if(!is_dir($base_upload_dir."/".$school_onboarding_csv_folder)){
            mkdir($base_upload_dir."/".$school_onboarding_csv_folder, 0775);
        }
        $tempFile = $_FILES['file']['tmp_name']; 
        $targetFile = $base_upload_dir."/".$school_onboarding_csv_folder."/".$csv_file_name;
        move_uploaded_file($tempFile,$targetFile);

        if(is_file($targetFile)){
            $handle = fopen($targetFile, "r");

            $teachers_rows = [];
            $error = false;
            $error_message = [];
            $row = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row > 0){
                //if (filter_var($data[4], FILTER_VALIDATE_EMAIL)) {
                    // peform add user here
                    $firstname = $data[0];
                    $lastname = $data[1];
                    $username = $data[2];
                    $password = $data[3];
                    $email = $data[4];
                    $gender = $data[5];

                    $response[] = [
                        "first_name" => $firstname,
                        "last_name" => $lastname,
                        "username" => $username,
                        "password" => $password,
                        "email" => $email,
                        "gender" => $gender
                    ];
                //}   
                }
                $row++;
            }
        }

        $response = new \WP_REST_Response($response);
        return $response;

    }

    static function get_teachers_group($user_id){
        $institutes = \Safar\SafarSchool::get_user_institutes( $user_id );
        $school_id = get_user_meta($user_id,"selected_institute", true);
        $entire_school_container = get_post_meta($school_id, "entire_school_container", true);

        $return_group_ids = [];
        if(!empty($school_id)){
            //  get teacher container group
            $args = array(
                'post_parent' => $school_id,
                'post_type' => 'groups',
                'numberposts' => -1,
            );
            $child_groups = get_posts($args);
            foreach($child_groups as $cg){
                $is_teacher_group = get_post_meta($cg->ID,"is_teachers_classroom",true);
                if($is_teacher_group){
                    $return_group_ids[] = $cg->ID;
                }
            }
        }

        return $return_group_ids;
    }

    static function get_catchall_group($user_id){
        $institutes = \Safar\SafarSchool::get_user_institutes( $user_id );
        $school_id = get_user_meta($user_id,"selected_institute", true);
        $entire_school_container = get_post_meta($school_id, "entire_school_container", true);

        $return_group_ids = [];
        if(!empty($entire_school_container)) $return_group_ids[] = $entire_school_container;

        return $return_group_ids;
    }

    static function update_classroom_details( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $tab = $request->get_param("tab");
        $classroom_id = $request->get_param("classroom_id");

        switch($tab){

            case "settings":

                $generator = new SyncGenerator( "", $classroom_id );
                $bpGroupId = $generator->getBpGroupId();

                $activity_feed_status = $request->get_param("activity_feed");
                $media_status = $request->get_param("photos");
                $document_status = $request->get_param("documents");
                $video_status = $request->get_param("videos");
                
                groups_update_groupmeta( $bpGroupId, "activity_feed_status", $activity_feed_status);
                groups_update_groupmeta( $bpGroupId, "media_status", $media_status);
                groups_update_groupmeta( $bpGroupId, "document_status", $document_status);
                groups_update_groupmeta( $bpGroupId, "video_status", $video_status);
                
                $response["bp_group_id"] = $bpGroupId;
                break;

            case "classroom":
                $class_name = $request->get_param("class_name");
                $subjects = $request->get_param("subjects");
                $response["class_name"] = $class_name;
                $response["subjects"] = $subjects;
                wp_update_post( ["ID"=>$classroom_id, "post_title"=>$class_name] );
                wp_set_post_terms($classroom_id, $subjects, "ld_group_category", false);

                break;

            case "courses":
                $active_courses = $request->get_param("active_courses");
                $courses = [];
                foreach($active_courses as $e_group_course){
                    $group_courses = learndash_group_enrolled_courses($e_group_course );
                    foreach($group_courses as $course_id) $courses[] = $course_id;
                }
                                
                learndash_set_group_enrolled_courses($classroom_id, $courses);
                $response["success"] = $result;
                $response["courses"] = $courses;
                //\Safar\Safar::debug($active_courses);
                break;
            case "teacher":
                $teachers = $request->get_param("teachers");
                if(empty($teachers)) $teachers = [];
                $response["success"] = learndash_set_groups_administrators($classroom_id, $teachers);
                //\Safar\Safar::debug($teachers);
                break;
            case "teacher-add":
                $first_name = $request->get_param("first_name");
                $last_name = $request->get_param("last_name");
                $email = $request->get_param("email");

                $user_exists = get_user_by("email", $email);
             
                if(!empty($user_exists->ID)){
                    $teacher_id = $user_exists->ID;

                    update_user_meta($teacher_id, "first_name", $first_name);
                    update_user_meta($teacher_id, "last_name", $last_name);
                    //learndash_set_groups_administrators($classroom_id, [$teacher_id]);

                    $response["teacher_id"] = $teacher_id;
                    $response["exists"] = true;
                    //update_user_meta($teacher_id,"user_role","teacher");
                    $response["rolea"] = get_user_meta($teacher_id,"user_role",true);
                    if(get_user_meta($teacher_id,"user_role",true) != "school admin"){
                        update_user_meta($teacher_id,"user_role","teacher");
                    }

                    $response["role"] = get_user_meta($teacher_id,"user_role",true);
                }else{
                    $response["exists"] = false;
                    $userdata = [
                        'user_email' => $email,
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'user_pass' => wp_generate_password(),
                        'user_login' => $email
                    ];
                    $teacher_id = wp_insert_user( $userdata );
                    wp_set_object_terms( $teacher_id, 'group_leader', 'role' );
                    $teacher = new \WP_User( $teacher_id );
                    $teacher->set_role( 'group_leader' );
                    
                    $response["teacher_id"] = $teacher_id;
                    if(get_user_meta($teacher_id,"user_role",true) != "school admin"){
                        update_user_meta($teacher_id,"user_role","teacher");
                    }
                    // send teacher email email
                    
                }

                self::send_teacher_welcome_mail($teacher_id, $classroom_id,$userdata = array());
                $teachers = learndash_get_groups_administrator_ids( $classroom_id, true );
                $teachers[] = $teacher_id;
                learndash_set_groups_administrators($classroom_id, $teachers );

                // Add teacher to catch all and teachers container group
                //get_teachers_group
                //get_catchall_group
                $get_teachers_group = self::get_teachers_group($user_id);
                if(!empty($get_teachers_group)){
                    
                    foreach($get_teachers_group as $group_id){
                        $teachers = learndash_get_groups_user_ids( $group_id, true );
                        $teachers[] = $teacher_id;
                        learndash_set_groups_users($group_id, $teachers );
                    }
                }
                $get_catchall_group = self::get_catchall_group($user_id);
                if(!empty($get_catchall_group)){
                    foreach($get_catchall_group as $group_id){
                        $teachers = learndash_get_groups_administrator_ids( $group_id, true );
                        $teachers[] = $teacher_id;
                        learndash_set_groups_administrators($group_id, $teachers );
                    }
                }

                break;
            case "teacher-add-single":
                $teacher_id = $request->get_param("teacher_id");
                $teachers = learndash_get_groups_administrator_ids( $classroom_id, true );
                $teachers[] = $teacher_id;
                learndash_set_groups_administrators($classroom_id, $teachers );
            
                self::send_teacher_welcome_mail($teacher_id, $classroom_id,$userdata = array());
                $response["classroomid"] = $classroom_id;
                $response["success"] = true;
                break;

            case "student":
                $students = $request->get_param("students");
                if(empty($students)) $students = [];
                $response["success"] = learndash_set_groups_users($classroom_id, $students);
                break;
            
            case "student-add-single":
                $student_id = $request->get_param("student_id");
                $students = learndash_get_groups_user_ids( $classroom_id, true );
                $students[] = $student_id;
                learndash_set_groups_users($classroom_id, $students );

                $response["email_sent"] = self::send_student_welcome_mail($student_id, $classroom_id,$userdata = array());
                $response["success"] = true;
                break;
            case "student-add":
                $first_name = $request->get_param("first_name");
                $last_name = $request->get_param("last_name");
                $email = $request->get_param("email");
                $username = $request->get_param("username");
                if(empty($username)) $username = $email;
                $password = $request->get_param("password");
                $gender = $request->get_param("gender");
                $remaining_seat = $request->get_param("remaining_seat");
                $date_of_birth = $request->get_param("date_of_birth");
                $family_id = $request->get_param("family_id");

                if($remaining_seat > 0){
                    $user_exists = get_user_by("login", $username);
                    
                
                    if(!empty($user_exists->ID)){
                        #$student_id = $user_exists->ID;
                        #$response["success"] = true;
                        #$response["student_id"] = $student_id;

                        $student_id = 0;
                        $group_parent = learndash_get_groups_administrator_ids( $classroom_id, true );
                       
                        if(in_array($student_id, $group_parent)){
                            $response["success"] = false;
                            $response["error_message"] = "Sorry, you cannot use the parent's username or email to create a student account. Please choose a unique username and email for the student account.";
                        }else{
                            $response["success"] = false;
                            $response["error_message"] = "User already exists";
                        }

                    }else{
                        $student_id = wp_create_user( $username, $password, $email);
                        if (is_wp_error($student_id)) {
                            // there was an error creating the user
                            $error_message = $student_id->get_error_message();
                            $response["success"] = false;
                            $response["student_id"] = $student_id;
                            $response["error_message"] = $error_message;
                        } else {
                            // user was created successfully
                            $response["success"] = true;
                            $response["student_id"] = $student_id;
                        }

                        
                    }

                    if(!empty($student_id)){
                        update_user_meta($student_id, "first_name", $first_name);
                        update_user_meta($student_id, "last_name", $last_name);
                        update_user_meta($student_id, "gender", $gender);
                        update_user_meta($student_id, "date_of_birth", $date_of_birth);
                        update_user_meta($student_id, "family_id", $family_id);
                    
                        $response["email_sent"] = self::send_student_welcome_mail($student_id, $classroom_id, ["user_pass"=>$password] );
                        $response["user_meta"] = get_user_meta($student_id);
                        $students = learndash_get_groups_user_ids( $classroom_id, true );
                        $students[] = $student_id;
                        learndash_set_groups_users($classroom_id, $students );
                    }
                }else{
                    $response["success"] = false;
                    $response["student_id"] = $student_id;
                    $response["error_message"] = "You have no available seats left";
                }

                break;

            case "student-import":
                $students = $request->get_param("students");

                $response["successfull_imports"] = [];
                $response["unsuccessfull_imports"] = [];
                $response["error_imports"] = [];
                $response["error_message"] = "";

                foreach($students as $student){
                    $first_name = $student["first_name"];
                    $last_name = $student["last_name"];
                    $username = $student["username"];
                    $password = $student["password"];
                    $email = $student["email"];
                    $gender = $student["gender"];

                    $error = false;
                              

                    if(!$error){
                        $user_exists = get_user_by("login", $username);
                         

                        if(empty($gender)){
                            $student["error_message"] = " no gender found";
                            $response["error_imports"][] = $student;
                        }else{
                            $student_id = 0;
                            if(!empty($user_exists->ID)){
                                $student_found = false;
                                // check if student belongs to the same intitute
                                // allow add if student is a member
                                $user_schools = self::get_school_details( $request );
                                // get all students
                                if(!empty($user_schools->data["students"])){ 
                                    foreach($user_schools->data["students"] as $student_details){
                                        if($student_details->data->ID == $user_exists->ID ){
                                            $student_id = $student_details->data->ID; // allow add student
                                            $student_found = true;
                                        }
                                    }
                                }
                                    
                                if(!$student_found){
                                    $student["error_message"] = "user already exists";
                                    $response["error_imports"][] = $student;
                                    $student_id = 0;
                                }

                            }else{

                                $student_id = wp_create_user( $username, $password, $email);
                                if (is_wp_error($student_id)) {
                                    // there was an error creating the user
                                    $error_message = $student_id->get_error_message();
                                    $response["success"] = false;
                                    $response["student_id"] = $student_id;
                                    $response["error_message"] = $error_message;

                                    $student["error_message"] = $error_message;
                                    $response["error_imports"][] = $student;
                                    $student_id = 0;
                                } else {
                                    // allow add student
                                    // user was created successfully
                                    update_user_meta($student_id, "first_name", $first_name);
                                    update_user_meta($student_id, "last_name", $last_name);
                                    update_user_meta($student_id, "gender", $gender);
                                }

                            }

                            if(!empty($student_id)){

                                $student["classroom_id"] = $classroom_id;
                                $response["success"] = true;
                                $response["student_id"] = $student_id;
                                $response["successfull_imports"][] = $student;
                                
                                $response["email_sent"] = self::send_student_welcome_mail($student_id, $classroom_id,$userdata = array());
                                $students = learndash_get_groups_user_ids( $classroom_id, true );
                                $students[] = $student_id;
                                learndash_set_groups_users($classroom_id, $students );
                            }

                        }
                    }
                }
          
                break;
            case "email-classroom":
                
                $subject = $request->get_param("subject");
                $body = $request->get_param("body");
                $classroom_type = $request->get_param("classroom_type");

                $email_address = [];
                if($classroom_type == "parent"){
                    $parent_school = self::get_classrooms($classroom_id);
                    $broadcast_type = $request->get_param("broadcast_type");
                    $to_user_id = $request->get_param("to_user_id");
                    

                    foreach($parent_school as $classroom){
                        //\Safar\Safar::debug(["id"=>$classroom->ID, "teacher"=>$classroom->school_data["teachers"], "students"=> $classroom->school_data["students"]]);

                        switch($broadcast_type){
                            case "families": case "family_single":
                                $institute = self::get_school_details([])->data;
                                $email_address = [];
                                $family_id = $request->get_param("family_id");
                                $get_all_emails = true;

                                if(!empty($family_id)) $get_all_emails = false;

                                foreach($institute["families"] as $family){

                                    if($get_all_emails){
                                        $allow = true;
                                    }else{
                                        if($family->id == $family_id){
                                            $allow = true;
                                        }else{
                                            $allow = false;
                                        }
                                    }

                                    if($allow){

                                        if(!empty($family->parents)){
                                            foreach($family->parents as $parent){
                                                if(!empty($parent["email"])){
                                                    if(!in_array($parent["email"], $email_address)){
                                                        $email_address[] = $parent["email"];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            
                                break;
                            case "students":
                                foreach($classroom->school_data["students"] as $user){
                                    if(!empty($user->data->user_email)){
                                        if(!in_array($user->data->user_email, $email_address)) $email_address[] = $user->data->user_email;
                                    }
                                }
                                break;

                            case "teachers":
                                foreach($classroom->school_data["teachers"] as $user){
                                    if(!empty($user->data->user_email)){
                                        if(!in_array($user->data->user_email, $email_address)) $email_address[] = $user->data->user_email;
                                    }
                                }
                                break;

                            case "single_user":
                                foreach($classroom->school_data["teachers"] as $user){
                                    if(!empty($user->data->user_email)){
                                        if(!in_array($user->data->user_email, $email_address)){
                                            if( $user->data->ID == $to_user_id) $email_address[] = $user->data->user_email;
                                        }
                                    }
                                }
                                foreach($classroom->school_data["students"] as $user){
                                    if(!empty($user->data->user_email)){
                                        
                                        if(!in_array($user->data->user_email, $email_address)){
                                            if( $user->data->ID == $to_user_id) $email_address[] = $user->data->user_email;
                                        }
                                    }
                                }

                                break;

                            case "institute":
                                $institute = self::get_school_details([])->data;

                                foreach($institute["teachers"] as $user){
                                    if(!empty($user->data->user_email)){
                                        if(!in_array($user->data->user_email, $email_address)){
                                            $email_address[] = $user->data->user_email;
                                        }
                                    }
                                }
                                foreach($institute["students"] as $user){
                                    \Safar\Safar::debug($user->data->user_email);
                                    if(!empty($user->data->user_email)){
                                        
                                        if(!in_array($user->data->user_email, $email_address)){
                                            $email_address[] = $user->data->user_email;
                                        }
                                    }
                                }
                                
                                break;

                            default:
                                foreach($classroom->school_data["teachers"] as $user){
                                    if(!empty($user->data->user_email)){
                                        if(!in_array($user->data->user_email, $email_address)) $email_address[] = $user->data->user_email;
                                    }
                                }
                                foreach($classroom->school_data["students"] as $user){
                                    if(!empty($user->data->user_email)){
                                        if(!in_array($user->data->user_email, $email_address)) $email_address[] = $user->data->user_email;
                                    }
                                }
                                break;
                        }
                        
                    }

                    $email_address = array_unique($email_address);
                
                }else{
                    $school_data = self::get_school_data($classroom_id);
                    foreach($school_data["teachers"] as $user){
                        if(!empty($user->data->user_email)) $email_address[] = $user->data->user_email;
                    }

                    foreach($school_data["students"] as $user){
                        if(!empty($user->data->user_email)) $email_address[] = $user->data->user_email;
                    }
                }

                if(!empty($email_address)){
                    #\Safar\Safar::debug(["email"=>$email_address,"subject"=>$subject,"body"=>$body]);

                    foreach($email_address as $email){
                        $to = $email;
                        $headers = ['Content-Type: text/html; charset=UTF-8','Bcc: joe@pbdigital.com.au'];

                        wp_mail( $to, $subject, $body, $headers );
                    }
                }
                $response["success"] = true;
                break;

            case "avatar":
                $avatar = $request->get_param("image");
                

                if($avatar=="dropzone"){
                    $attachment_id = $request->get_param("attachment_id");
                    $response["success"] = update_field("class_avatar",$attachment_id, $classroom_id);

                    

                }else{

                    $file_path = $avatar;
                    // Get the file data
                    $file_data = file_get_contents( $file_path );
                    // Get the file name
                    $file_name = basename( $file_path );
                    // Define the parameters for the function
                    $upload = wp_upload_bits( $file_name, null, $file_data );

                    // Check if the upload was successful
                    if ( ! $upload['error'] ) {

                        // Define the attachment post data
                        $attachment = array(
                            'post_title'   => $file_name,
                            'post_content' => '',
                            'post_status'  => 'inherit',
                            'guid'         => $upload['url'],
                        );

                        // Insert the attachment post into the database
                        $attachment_id = wp_insert_attachment( $attachment, $upload['file'], 0 );

                        // Check if the attachment was inserted
                        if ( $attachment_id ) {
                            $response["success"] = update_field("class_avatar",$attachment_id, $classroom_id);

                            
                        }
                    }
                }

                $response["attachment_id"] = $attachment_id;
                $response["avatar"] = $avatar;
                $response["classroom_id"] = $classroom_id;
                break;

            case "cover_photo":
                $cover_photo = $request->get_param("image");

                if($cover_photo=="dropzone"){
                    $attachment_id = $request->get_param("attachment_id");
                    $response["success"] = update_field("class_cover_photo",$attachment_id, $classroom_id);

                    

                }else{

                    $file_path = $cover_photo;
                    // Get the file data
                    $file_data = file_get_contents( $file_path );
                    // Get the file name
                    $file_name = basename( $file_path );
                    // Define the parameters for the function
                    $upload = wp_upload_bits( $file_name, null, $file_data );

                    // Check if the upload was successful
                    if ( ! $upload['error'] ) {

                        // Define the attachment post data
                        $attachment = array(
                            'post_title'   => $file_name,
                            'post_content' => '',
                            'post_status'  => 'inherit',
                            'guid'         => $upload['url'],
                        );

                        // Insert the attachment post into the database
                        $attachment_id = wp_insert_attachment( $attachment, $upload['file'], 0 );

                        // Check if the attachment was inserted
                        if ( $attachment_id ) {
                            $response["success"] = update_field("class_cover_photo",$attachment_id, $classroom_id);

                        }
                    }
                }
                $response["attachment_id"] = $attachment_id;
                $response["cover_photo"] = $cover_photo;
                $response["classroom_id"] = $classroom_id;
                break;
        }

        $response["sync"] = self::fully_sync_buddyboss_learndash_group($classroom_id);
        delete_transient("school_data_".$user_id."_".$classroom_id);
        
        $response = new \WP_REST_Response($response);
        return $response;
    }


    static function fully_sync_buddyboss_learndash_group($classroom_id){

        $generator = new SyncGenerator( "", $classroom_id );
        $response["admins_and_users"] = $generator->syncLdAdmins()->syncLdUsers();
        $bpGroupId = $generator->getBpGroupId();

        $terms = wp_get_post_terms( $classroom_id, "ld_group_category");
        foreach($terms as $eterm){
            $response["subject"][] = groups_update_groupmeta($bpGroupId, "group_subject_id", $eterm->term_id);
        }

        // sync buddyboss group title
        $ld_group_post = get_post($classroom_id);
        $group_params = [ 'group_id' => $bpGroupId, 'name' => $ld_group_post->post_title ];
        // Update the group name using the BuddyBoss function
        $result = groups_create_group( $group_params );
        $response["buddyboss_group_id"] = $bpGroupId;
        $response["result"] = $result;

        return $response;
    }

    static function get_learndash_group_id_from_bp_group_id($group_id){
        $result = parent::wpdb()->get_results("SELECT * FROm ".parent::wpdb()->prefix."postmeta
            WHERE meta_key='_sync_group_id' AND meta_value='".esc_sql($group_id)."' 

            LIMIT 1
        ");
        if(!empty($result)){
            return $result[0]->post_id;
        }else{
            return false;
        }
    }

    // welcome emails
    static function send_teacher_welcome_mail($user_id, $group_id,$userdata = array()){
		$teacher_data = get_user_by('ID', $user_id);
		$teacher_first_name = get_user_meta( $user_id, 'first_name', true );
		$teacher_last_name = get_user_meta( $user_id, 'last_name', true );
		$teacher_username = $teacher_data->user_login;

		$current_user_id = get_current_user_id();
		$ldc_teacher_email_data = get_user_meta( $current_user_id, 'ldc_teacher_email_data', true );
		$ldc_email_tab = get_site_option('ldc_email_tab');
		
		
		$find = array('{group_name}','{childgroup_name}','{teacher_name}','{teacher_username}','{password}','{autologin}');
		
		$group_name = get_the_title($group_id);
		$parent_group_id = wp_get_post_parent_id($group_id);
		$parent_group_name = get_the_title($parent_group_id);

        if(empty($parent_group_id)){
            $parent_group_id = get_user_meta( $current_user_id ,"selected_institute", true);
        }

        /* 
        update_post_meta( $learndash_parent_group_id, "school_onboarding_teacher_welcome_email_subject", $teacher_welcome_subject);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_teacher_welcome_email_body", $teacher_welcome_body);
        $ldc_email_tab['teacher_subject']
        $ldc_email_tab['teacher_body']
        */
        $custom_email_subject = get_post_meta($parent_group_id, "school_onboarding_teacher_welcome_email_subject", true);
        $custom_email_body = self::get_welcome_email_body(["email_type"=>"teacher", "school_id" => $parent_group_id]);//get_post_meta($parent_group_id, "school_onboarding_teacher_welcome_email_body", true);

        if(!empty($custom_email_subject)) $ldc_email_tab['teacher_subject'] = $custom_email_subject;
        if(!empty($custom_email_body)) $ldc_email_tab['teacher_body'] = $custom_email_body;
        
        $reset_password_link = site_url("/login/?action=lostpassword");
		$teacher_password = !empty($userdata) ? $userdata['user_pass'] : "<a href='".$reset_password_link."'>Create your password</a>"; 
		
		$unique_id = uniqid();
		update_user_meta($user_id, "ldc_unique_id", $unique_id);
		$autologin_link = site_url() . '?j2j_autologin=true&username=' . rawurlencode($teacher_username) . "&unique_id=" . $unique_id."&redirect=admin-onboarding";
        

		$replace = array($parent_group_name,$group_name, trim($teacher_first_name .' ' . $teacher_last_name), $teacher_username, $teacher_password, $autologin_link);
	
		$headers = array('Content-Type: text/html; charset=UTF-8','Bcc: joe@pbdigital.com.au');
		$to = $teacher_data->user_email;
			
        $teacher_email_body = str_replace($find, $replace, $ldc_email_tab['teacher_body']);
        $subject = $ldc_email_tab['teacher_subject'];
        $body = $teacher_email_body;
        
        wp_mail( $to, $subject, $body, $headers );
	}

    static function get_institute_by_teacher_id($teacher_id){
        $admin_groups = learndash_get_administrators_group_ids( $teacher_id ); // if user is a teacher or admin of the group
        $institute = [];
		foreach($admin_groups as $gid){
            $is_teacher_classroom = get_post_meta($gid,"is_teachers_classroom",true);
            if($is_teacher_classroom){
                $teacher_classroom = get_post($gid);
                $post_parent = $teacher_classroom->post_parent;
                if(!empty($post_parent)){
                    $institute = \Safar\SafarSchool::get_school_data($post_parent);
                }
            }
        }
        return $institute;
    }

    static function test_send_teacher_email(){
        self::send_teacher_welcome_mail( 11835, 0, [] );
    }

    static function send_parent_welcome_mail($user_id, $group_id,$userdata = array()){
		$teacher_data = get_user_by('ID', $user_id);
		$teacher_first_name = get_user_meta( $user_id, 'first_name', true );
		$teacher_last_name = get_user_meta( $user_id, 'last_name', true );
		$teacher_username = $teacher_data->user_login;

		$current_user_id = get_current_user_id();
		$ldc_teacher_email_data = get_user_meta( $current_user_id, 'ldc_teacher_email_data', true );
		$ldc_email_tab = get_site_option('ldc_email_tab');
		
		
		$find = array('{group_name}','{childgroup_name}','{parent_name}','{parent_username}','{password}','{autologin}');
		
		$group_name = get_the_title($group_id);
		$parent_group_id = wp_get_post_parent_id($group_id);
		$parent_group_name = get_the_title($parent_group_id);

        /* 
        update_post_meta( $learndash_parent_group_id, "school_onboarding_teacher_welcome_email_subject", $teacher_welcome_subject);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_teacher_welcome_email_body", $teacher_welcome_body);
        $ldc_email_tab['teacher_subject']
        $ldc_email_tab['teacher_body']
        */
        $email_body_args = [];
        $email_body_args["email_type"] = "parent";
        if(!empty($group_id)){
            //$group_id = get_user_meta( $current_user_id ,"selected_institute", true);
            $terms = wp_get_object_terms($group_id, "ld_group_tag");
            if(!empty($terms)){
                if($terms[0]->slug == "overall-school"){
                    $email_body_args["school_id"] = $group_id;
                }
            }
        }
        
        $custom_email_subject = get_field("family_welcome_email_subject","option");
        $custom_email_body = self::get_welcome_email_body($email_body_args); //


        if(!empty($custom_email_subject)) $ldc_email_tab['teacher_subject'] = $custom_email_subject;
        if(!empty($custom_email_body)) $ldc_email_tab['teacher_body'] = $custom_email_body;
        
        $reset_password_link = site_url("/login/?action=lostpassword");
		$teacher_password = !empty($userdata) ? $userdata['user_pass'] : "<a href='".$reset_password_link."'>Create your password</a>"; 
		
		$unique_id = uniqid();
		update_user_meta($user_id, "ldc_unique_id", $unique_id);
		$autologin_link = site_url() . '?action_autologin=true&username=' . rawurlencode($teacher_username) . "&unique_id=" . $unique_id;

		$replace = array($parent_group_name,$group_name, trim($teacher_first_name .' ' . $teacher_last_name), $teacher_username, $teacher_password, $autologin_link);
	
		$headers = array('Content-Type: text/html; charset=UTF-8','Bcc: joe@pbdigital.com.au');
		$to = $teacher_data->user_email;
			
        $teacher_email_body = str_replace($find, $replace, $custom_email_body);
        $subject = $ldc_email_tab['teacher_subject'];
        $body = $teacher_email_body;
        

        #print_r(["em"=>$email_body_args, "group_id"=>$group_id, "custom_email_body"=>$custom_email_body, "to"=>$to]);
        
        wp_mail( $to, $subject, $body, $headers );
		
	}

    static function send_institute_welcome_mail($user_id, $group_id,$userdata = array()){
		$teacher_data = get_user_by('ID', $user_id);
		$teacher_first_name = get_user_meta( $user_id, 'first_name', true );
		$teacher_last_name = get_user_meta( $user_id, 'last_name', true );
		$teacher_username = $teacher_data->user_login;

		$current_user_id = get_current_user_id();
		$ldc_teacher_email_data = get_user_meta( $current_user_id, 'ldc_teacher_email_data', true );
		$ldc_email_tab = get_site_option('ldc_email_tab');
		
		
		$find = array('{group_name}','{childgroup_name}','{parent_name}','{parent_username}','{password}','{autologin}');
		
		$group_name = get_the_title($group_id);
		$parent_group_id = wp_get_post_parent_id($group_id);
		$parent_group_name = get_the_title($parent_group_id);

        /* 
        update_post_meta( $learndash_parent_group_id, "school_onboarding_teacher_welcome_email_subject", $teacher_welcome_subject);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_teacher_welcome_email_body", $teacher_welcome_body);
        $ldc_email_tab['teacher_subject']
        $ldc_email_tab['teacher_body']
        */
        $custom_email_subject = get_field("institute_welcome_email_subject","option");
        $custom_email_body = self::get_welcome_email_body(["email_type"=>"institute"]); //

        if(!empty($custom_email_subject)) $ldc_email_tab['teacher_subject'] = $custom_email_subject;
        if(!empty($custom_email_body)) $ldc_email_tab['teacher_body'] = $custom_email_body;
        
        $reset_password_link = site_url("/login/?action=lostpassword");
		$teacher_password = !empty($userdata) ? $userdata['user_pass'] : "<a href='".$reset_password_link."'>Create your password</a>"; 
		
		$unique_id = uniqid();
		update_user_meta($user_id, "ldc_unique_id", $unique_id);
		$autologin_link = site_url() . '?action_autologin=true&username=' . rawurlencode($teacher_username) . "&unique_id=" . $unique_id;

		$replace = array($parent_group_name,$group_name, trim($teacher_first_name .' ' . $teacher_last_name), $teacher_username, $teacher_password, $autologin_link);
	
		$headers = array('Content-Type: text/html; charset=UTF-8','Bcc: joe@pbdigital.com.au');
		$to = $teacher_data->user_email;
			
        $teacher_email_body = str_replace($find, $replace, $ldc_email_tab['teacher_body']);
        $subject = $ldc_email_tab['teacher_subject'];
        $body = $teacher_email_body;
        
        wp_mail( $to, $subject, $body, $headers );
		
	}

	static function send_student_welcome_mail($user_id, $group_id, $userdata = array()){
		$student_data = get_user_by('ID', $user_id);
		
		$current_user_id = get_current_user_id();
		
		$student_first_name = get_user_meta( $user_id, 'first_name', true );
		$student_last_name = get_user_meta( $user_id, 'last_name', true );

		$ldc_student_email_data = get_user_meta( $current_user_id, 'ldc_student_email_data', true );
		$ldc_email_tab = get_site_option('ldc_email_tab');

		$teacher_first_name = $teacher_last_name = "";
		
		$find = array('{group_name}','{childgroup_name}','{student_name}','{user_name}','{password}','{autologin}');
		
		$group_name = get_the_title($group_id);
		$parent_group_id = wp_get_post_parent_id($group_id);
		$parent_group_name = get_the_title($parent_group_id);

        /* 
        update_post_meta( $learndash_parent_group_id, "school_onboarding_student_welcome_email_subject", $student_welcome_subject);
        update_post_meta( $learndash_parent_group_id, "school_onboarding_student_welcome_email_body", $student_welcome_body);
        */

        $custom_email_subject = get_post_meta($parent_group_id, "school_onboarding_student_welcome_email_subject", true);
        $custom_email_body = self::get_welcome_email_body(["email_type"=>"student", "school_id" => $parent_group_id]);//get_post_meta($parent_group_id, "school_onboarding_student_welcome_email_body", true);


        

        if(!empty($custom_email_subject)) $ldc_email_tab['student_subject'] = $custom_email_subject;
        if(!empty($custom_email_body)) $ldc_email_tab['student_body'] = $custom_email_body;
        
        $reset_password_link = site_url("/login/?action=lostpassword");
		#$student_password = !empty($userdata) ? $userdata['user_pass'] : "{" . __('use your current password') . "}"; 
        $student_password = !empty($userdata['user_pass']) ? $userdata['user_pass'] : "<a href='".$reset_password_link."'>Create your password</a>"; 
		
		$unique_id = uniqid();
		update_user_meta($user_id, "ldc_unique_id", $unique_id);
		$autologin_link = site_url() . '?action_autologin=true&username=' . rawurlencode($student_data->user_login) . "&unique_id=" . $unique_id ;

		$replace = array($parent_group_name,$group_name, trim($student_first_name .' ' . $student_last_name),$student_data->user_login, $student_password, $autologin_link);

		$headers = array('Content-Type: text/html; charset=UTF-8','Bcc: joe@pbdigital.com.au');
		$to = $student_data->user_email;

        $student_email_body = str_replace($find, $replace, $ldc_email_tab['student_body']);
        $subject = $ldc_email_tab['student_subject'];
        $body = $student_email_body;
        
        wp_mail( $to, $subject, $body, $headers );
		
	}
    // end welcome emails

    static function update_school_admins( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $adminids = $request->get_param("adminids");
        $school_id = $request->get_param("id");

        $teachers = learndash_get_groups_administrator_ids( $school_id, true );
        foreach($adminids as $teacher_id){
            $teachers[] = $teacher_id;
        }
        learndash_set_groups_administrators($school_id, $teachers );

        $response["success"] = true;
        $response = new \WP_REST_Response($response);
        return $response;
    }


    /* API for creating Institute group, this is used for integration woocommerce 
    successful purchase from Safar Publications to J2J
    */

    static function submit_institute_details( $request ){
        
        $order_id = $request->get_param("order_id");
        $order_data["first_name"] = $request->get_param("first_name");
        $order_data["last_name"] =  $request->get_param("last_name");
        $order_data["company"] =  $request->get_param("company");
        $order_data["address_1"] =  $request->get_param("address_1");
        $order_data["address_2"] =  $request->get_param("address_2");
        $order_data["city"] =  $request->get_param("city");
        $order_data["state"] =  $request->get_param("state");
        $order_data["postcode"] =  $request->get_param("postcode");
        $order_data["country"] =  $request->get_param("country");
        $order_data["email"] =  $request->get_param("email");
        $order_data["phone"] =  $request->get_param("phone");
        $order_data["seats_count"] =  $request->get_param("seats_count");
        $order_data["product_ids"] =  $request->get_param("product_ids");
        $order_data["ld_group_categories"] =  $request->get_param("ld_group_categories");
        $order_data["user_id"] = $request->get_param("user_id");
        $order_data["subscription_status"] = $request->get_param("subscription_status");

        #$response["order_data"] = $order_data;
        $response["order_id"] = $order_id;

        $email = $order_data["email"];
        $user_id = email_exists($email);
        $send_welcome_email = false;
        
        if(empty($user_id)){ // check if user exists using SafarPubUserId
            if(!empty($order_data["user_id"])) $user_id =  \Safar\SafarFamily::get_login_info_by_safarpub_user_id( $order_data["user_id"] );
        }
        
        $user_exists = false;
        if (!$user_id) {
            // User doesn't exist, so create a new user

            $user_data = [
                'user_email' => $email,
                'first_name' => $order_data["first_name"],
                'last_name'  => $order_data["last_name"],
                'user_pass' => wp_generate_password(),
                'user_login' => $email
            ];

            $user_id = wp_insert_user($user_data);

            $send_welcome_email = true;
        }else{
            $user_exists = true;
        }
        // Retrieve the user ID
        $user = get_user_by('email', $email);

        if(empty($user)){
            if(!empty($user_id)){
                $user = get_user_by("id",$user_id);
            }
        }

        $user_id = $user->ID;
	    $user->set_role( "group_leader" );
        update_field("user_role", "school admin", "user_" . $user_id);
        
        
        if($user_exists){
            $safar_publications_user_id = get_user_meta($user_id,"safar_publications_institute_user_id",true);
            if(empty($safar_publications_user_id)) $send_welcome_email = true;
        }

        update_user_meta($user_id,"safar_publications_institute_user_id", $order_data["user_id"]);
        delete_user_meta($user_id,"safar_publications_user_id"); // identifier user id for family groups

       
        // create / update group 
        
        // check first if the user has an existing Institute group
        // if yes, just use and update that group
        $institute_groups = self::get_institute_groups_by_user_id( $user_id ); // reuse this 

        //$response["test_mode"] = true;
        $response["institute_groups"] = $institute_groups;
        if(empty($institute_groups)){
            $group_name = $order_data["last_name"]." Institute";
            $new_post = array(
                'post_title'    => $group_name,
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_author'   => $user_id,
                'post_type'     => 'groups',
                'post_parent'   => 0
            );
            $post_id = wp_insert_post( $new_post );
            $response["new"] = true;
            // set completed Institute onboarding to false
            update_post_meta($post_id, "order_id", $order_id);
        }else{
            $post_id = $institute_groups[0]->ID;

            // start
            // if exists meaning its and currently have an active subscription
            // add current seats to current number of seats
            $subscriptions = \Safar\SafarPublications::api_request(["endpoint"=>"/family/order/".$order_id."/subscriptions?".mt_rand()]);
            $has_active_subscription = false;
            foreach($subscriptions as $subscription){
                if($subscription->status == "active" || $subscription->status == "pending-cancel"){
                    $has_active_subscription = true;
                }
            }
            if($has_active_subscription){
                $current_number_seats = get_post_meta($post_id, "_ulgm_total_seats", true);
                update_post_meta($post_id, "show_upgrade_notification", true );
                update_post_meta($post_id,"upgraded_seats_count", $order_data["seats_count"]);
            }
            $response["has_active_subscription"] = $has_active_subscription;
            $response["upgraded_seats_count"] = $order_data["seats_count"];
            // end if exists meaning its and currently have an active subscription
        }

        $response["group_id"] = $post_id;

        update_post_meta($post_id, "order_meta", json_encode($order_data));
        update_post_meta($post_id,"safar_publications_institute_user_id", $order_data["user_id"]);
        update_post_meta($post_id,"subscription_status", $order_data["subscription_status"]);
        wp_set_object_terms($post_id, 'overall-school', 'ld_group_tag', false);

        // Add user as administrator
        $admin_result = learndash_set_groups_administrators($post_id, [$user_id]);

        /* 
        Create Buddyboss Sync
        */
        $newGroup  = groups_create_group(["name"=>$group_name]);
        $ldGroupId = $post_id;
        $generator = new SyncGenerator( $newGroup, $ldGroupId );
        $generator->updateBuddypressGroup( $ldGroupId, $newGroup );
        $generator->fullSyncToBuddypress();
        
        // activate Uncanny Group Management
        $group_post = get_post($post_id);
        $MigrateLearndashGroups = new MigrateLearndashGroups;
        $MigrateLearndashGroups->process_upgrade($group_post);

        // update seat count
        update_post_meta( $post_id, '_ulgm_total_seats',  $order_data["seats_count"]);
        $code_group_id = ulgm()->group_management->seat->get_code_group_id( $post_id );
        LearndashGroupsPostEditAdditions::update_seat_count( $post_id, $code_group_id, $order_data["seats_count"] );

    

        $response["seats_count"] = $order_data["seats_count"];
        $response["user_id"] = $user_id;
        //$response["post_meta"] = get_post_meta($post_id);

        // update group avatar and cover photo
        $family_cover_photo = get_field("family_cover_photo","option");
        $family_avatar = get_field("family_avatar","option");
        update_field("class_avatar",$family_avatar, $post_id);
        update_field("class_cover_photo",$family_cover_photo, $post_id);

        // add courses from ld_group_category
        $args = [
            'numberposts' => -1,
            'orderby'   => 'menu_order post_title',
            "order" => 'asc',
            'post_type' => 'groups',
            "suppress_filters" => true,
            'tax_query' => [
                    [
                        'taxonomy' => 'ld_group_category',
                        'field' => 'slug',
                        'terms' => $order_data["ld_group_categories"]
                    ],
                    [
                        'taxonomy' => 'ld_group_tag',
                        'field' => 'slug',
                        'terms' => 'course-content-groups'
                    ]
                ]
        ];
        $ld_groups = get_posts($args);
        $group_courses = [];
        foreach($ld_groups as $egroup){
            $group_details = SafarCourses::get_ld_group_courses($egroup->ID);
            if(!empty($group_details)){
                foreach($group_details as $g){
                    $group_courses[] = $g->post_id;
                }
            }
        }
        learndash_set_group_enrolled_courses( $post_id, $group_courses );

        $response["welcome_email_sent"] = false;
        if($send_welcome_email){
            self::send_institute_welcome_mail($user_id, $post_id);
            $response["welcome_email_sent"] = true;
        }
        

        // create initial catch all and teachers group
        // the group names will be updated after onboarding
        wp_set_auth_cookie($user_id);
        wp_set_current_user($user_id);
        $params = [];
        $params["class_name"] = $group_name." Teachers";
        $params["subjects"] ="";
        $params["parent_group_id"] = $response["group_id"];
        $params["post_id"] = "";
        $params["teachers_classroom"] = 1;
        $request   = new \WP_REST_Request( 'POST', );
        $request->set_query_params($params);
        $teachers_group = self::create_new_classroom($request);
        $response["params"] = $params;

        // add current user_id as teacher, user_id = is the institute admin
        learndash_set_groups_administrators($teachers_group->data["id"], [$user_id]);

        $params = [];
        $params["class_name"] = $group_name." Entire School";
        $params["subjects"] ="";
        $params["post_id"] = "";
        $request   = new \WP_REST_Request( 'POST', );
        $request->set_query_params($params);
        $entire_school = self::create_new_classroom($request);

        update_field("entire_school_container", $entire_school->data["id"], $response["group_id"] );
        
        $response["teachers_container_id"] = $teachers_group->data["id"];
        $response["entire_school_id"] = $entire_school->data["id"];
        $response["rand"] = mt_rand();


        // Check if user has an existing Family Learndash Group
        // 1. Convert that family group into a classroom
        $group_ids = learndash_get_administrators_group_ids( $user_id, true );
        $args = [
            'numberposts' => -1,
            'orderby'   => 'menu_order post_title',
            "order" => 'asc',
            'post_type' => 'groups',
            "post__in" => $group_ids,
            "suppress_filters" => true,
            'tax_query' => [
                    [
                        'taxonomy' => 'ld_group_tag',
                        'field' => 'slug',
                        'terms' => 'family-group'
                    ]
                ]
        ];
        $ld_groups = get_posts($args);
        if(!empty($ld_groups)){
            foreach($ld_groups as $group){
                wp_remove_object_terms($group->ID, 'family-group', 'ld_group_tag');
                wp_set_object_terms($group->ID, 'child-classroom', 'ld_group_tag', true);
                $child_group = get_post($group->ID);
                // Update the parent post ID for the child post
                if ($child_group) {
                    $child_group->post_title = $order_data["last_name"]." Classroom";
                    $child_group->post_parent = $post_id;
                    wp_update_post($child_group);
                }
            }
        }
        // end convert family group into classroom
        // END upgrade family to institute

        // add courses to the selected category
        // add courses from ld_group_category
        $group_courses = [];
        $args = [
            'numberposts' => -1,
            'orderby'   => 'menu_order post_title',
            "order" => 'asc',
            'post_type' => 'groups',
            "suppress_filters" => true,
            'tax_query' => [
                    [
                        'taxonomy' => 'ld_group_category',
                        'field' => 'slug',
                        'terms' => $order_data["ld_group_categories"]
                    ],
                    [
                        'taxonomy' => 'ld_group_tag',
                        'field' => 'slug',
                        'terms' => 'course-content-groups'
                    ]
                ]
        ];
        $ld_groups = get_posts($args);
        $response["ld_groups"] = $ld_groups;
        foreach($ld_groups as $egroup){
            $group_details = SafarCourses::get_ld_group_courses($egroup->ID);
            if(!empty($group_details)){
                foreach($group_details as $g){
                    $group_courses[] = $g->post_id;
                }
            }
        }
        
        learndash_set_group_enrolled_courses( $post_id, $group_courses ); // add courses to Institute Group 
        learndash_set_group_enrolled_courses( $entire_school->data["id"],$group_courses); // add courses to Catch All Group
        
        $students = learndash_get_groups_user_ids( $entire_school->data["id"] , true ); 
        $students[] = $user_id; // Add User/ or Institute Admin to the users so admin can see courses on the course library page
        learndash_set_groups_users($entire_school->data["id"], $students );
        
        $response["group_courses"] = $group_courses;
        $response["course_user_ids"] = $students;
        $response["ld_group_categories"] = $order_data["ld_group_categories"];
        // end add courses to the selected category

        $response = new \WP_REST_Response($response);
        return $response;

    }

    static function update_admin_password($request){

        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $password = $request->get_param("password");
        $email = $request->get_param("email");
        // update user password
        $user = get_user_by('id', $user_id);
        $set_password = false;
        if ($user) {
            $set_password = wp_set_password($password, $user->ID);
            wp_set_auth_cookie($user->ID);
        }

        $response["password"] = $password;
        $response["email"] = $email;
        $response["user_id"] = $user_id;
        $response["set_password"] = $set_password;
        $response["user"] = $user;
        $response["nonce"] = wp_create_nonce('wp_rest');
        $response = new \WP_REST_Response($response);
        return $response;
    }
    static function get_institute_groups_by_user_id( $user_id ){
       
        $group_ids = learndash_get_administrators_group_ids( $user_id, true );
        if(empty($group_ids)) return false;
        
        $args = [
            'numberposts' => -1,
            'orderby'   => 'menu_order post_title',
            "order" => 'asc',
            'post_type' => 'groups',
            "post__in" => $group_ids,
            "suppress_filters" => true,
            'tax_query' => [
                    [
                        'taxonomy' => 'ld_group_tag',
                        'field' => 'slug',
                        'terms' => 'overall-school'
                    ]
                ]
        ];
        $institutes = get_posts($args);

        if(!empty($institutes)){
            foreach($institutes as $key=>$group){
                $institutes[$key]->avatar = get_field("class_avatar", $group->ID);
                $institutes[$key]->cover_photo = get_field("class_cover_photo", $group->ID);
                $institutes[$key]->school_data = \Safar\SafarSchool::get_school_data($group->ID);
            }
        }
        //set_transient( $transient_key, $response, 1800 ); // 3600 = 1 hour
        return $institutes;
    }

    static function update_logo($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $attachment_id = $request->get_param("attachmentid");
        $school_id = get_user_meta($user_id,"selected_institute", true);
        
        if(!empty($attachment_id)) set_post_thumbnail($school_id, $attachment_id);
        else delete_post_thumbnail($school_id);

        $response["attachment_id"] = $attachment_id;
        $response["school_id"] = $school_id;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function save_institute_information($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $institute_name = $request->get_param("institute_name");
        $facial_feature = $request->get_param("facial_feature");
        $school_id = get_user_meta($user_id,"selected_institute", true);

        update_post_meta( $school_id, "school_onboarding_facial_features", $facial_feature);
        wp_update_post(["ID"=>$school_id, "post_title"=>$institute_name]);

        $response["institute_name"] = $institute_name;
        $response["facial_feature"] = $facial_feature;
        $response["school_id"] = $school_id;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function save_activity_feed( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $school_id = get_user_meta($user_id,"selected_institute", true);
        $generator = new SyncGenerator( "", $school_id );
        $bpGroupId = $generator->getBpGroupId();

        $activity_feed_status = $request->get_param("activity_feed_status");
        $media_status = $request->get_param("media_status");
        $document_status = $request->get_param("document_status");
        $video_status = $request->get_param("video_status");

        if(empty($bpGroupId)){
            $group = get_post($school_id);
            $newGroup  = groups_create_group(["name"=>$group->post_title]);
            $ldGroupId = $school_id;
            $generator = new SyncGenerator( $newGroup, $ldGroupId );
            $generator->updateBuddypressGroup( $ldGroupId, $newGroup );
            $generator->fullSyncToBuddypress();

            $generator = new SyncGenerator( "", $school_id );
            $bpGroupId = $generator->getBpGroupId();
        }
        
        groups_update_groupmeta( $bpGroupId, "activity_feed_status", $activity_feed_status);
        groups_update_groupmeta( $bpGroupId, "media_status", $media_status);
        groups_update_groupmeta( $bpGroupId, "document_status", $document_status);
        groups_update_groupmeta( $bpGroupId, "video_status", $video_status);
    

        $response["school_id"] = $school_id;
        $response["bpGroupId"] = $bpGroupId;
        $response["activity_feed_status"] = $activity_feed_status;
        $response["media_status"] = $media_status;
        $response["document_status"] = $document_status;
        $response["video_status"] = $video_status;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function save_institute_welcome_email($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        // update welcome email
        $school_id = get_user_meta($user_id,"selected_institute", true);
        
        $teacher_subject = $request->get_param("teacher_subject");
        $teacher_greetings = $request->get_param("teacher_greetings");
        $teacher_body1 = $request->get_param("teacher_body1");
        $teacher_body2 = $request->get_param("teacher_body2");
        $teacher_body3 = $request->get_param("teacher_body3");

        $student_subject = $request->get_param("student_subject");
        $student_greetings = $request->get_param("student_greetings");
        $student_body1 = $request->get_param("student_body1");
        $student_body2 = $request->get_param("student_body2");
        $student_body3 = $request->get_param("student_body3");


        $institute_family_subject = $request->get_param("institute_family_subject");
        $institute_family_greetings = $request->get_param("institute_family_greetings");
        $institute_family_body1 = $request->get_param("institute_family_body1");
        $institute_family_body2 = $request->get_param("institute_family_body2");

        update_post_meta( $school_id, "school_onboarding_teacher_welcome_email_subject", $teacher_subject);
        update_post_meta( $school_id, "school_onboarding_teacher_welcome_email_greetings", strip_tags($teacher_greetings) );
        update_post_meta( $school_id, "school_onboarding_teacher_welcome_email_body_1", $teacher_body1);
        update_post_meta( $school_id, "school_onboarding_teacher_welcome_email_body_2", $teacher_body2);
        update_post_meta( $school_id, "school_onboarding_teacher_welcome_email_body_3", $teacher_body3);
        
        update_post_meta( $school_id, "school_onboarding_student_welcome_email_subject", $student_subject);
        update_post_meta( $school_id, "school_onboarding_student_welcome_email_greetings", strip_tags($student_greetings) );
        update_post_meta( $school_id, "school_onboarding_student_welcome_email_body_1", $student_body1);
        update_post_meta( $school_id, "school_onboarding_student_welcome_email_body_2", $student_body2);
        update_post_meta( $school_id, "school_onboarding_student_welcome_email_body_3", $student_body3);


        update_post_meta( $school_id, "school_onboarding_institute_family_welcome_email_subject", $institute_family_subject );
        update_post_meta( $school_id, "school_onboarding_institute_family_welcome_email_greetings", strip_tags($institute_family_greetings) );
        update_post_meta( $school_id, "school_onboarding_institute_family_welcome_email_body_1", $institute_family_body1 );
        update_post_meta( $school_id, "school_onboarding_institute_family_welcome_email_body_2", $institute_family_body2 );
    

        $response["school_id"] = $school_id;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function is_entire_school_group($group_id){
        if(empty($group_id)) return false;

        $meta_key = 'entire_school_container';
        $meta_value = $group_id;
        
        $query = parent::wpdb()->prepare(
            "SELECT post_id FROM ".parent::wpdb()->postmeta."
            WHERE meta_key = %s
            AND meta_value = %s",
            $meta_key,
            $meta_value
        );
        
        $results = parent::wpdb()->get_results($query);
        
        if(!empty($results)) return true;
        else return false;
    }
}