<?php 
namespace Safar;
use Safar\SafarUser;

class SafarCourses extends Safar{

    static $ld_groupid = 0;
    static $ld_group_category = 0;

    static function get_categories($request){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $terms = get_terms( 'ld_group_category', ['hide_empty' => false]);

            $response = [];
            foreach($terms as $term){
                $response[] = [
                    "term_id" => $term->term_id,
                    "term_name" => $term->name,
                    "background_image" => get_field("background_image", "category_".$term->term_id),
                    "background_color" => get_field("background_color", "category_".$term->term_id)
                ];
            }

            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }
    
    static function get_subjects( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $terms = get_terms( 'ld_group_category');
            
            $searchkey = "";
            $filter_status = "";
            $filter_subject = "";
            
            $bypasstransient = true;

            if(!empty($request)){
                $searchkey = $request->get_param("searchkey");
                $filter_status = $request->get_param("status");
                $filter_subject = $request->get_param("subject");
                $bypasstransient = filter_var( $request->get_param("bypasstransient") , FILTER_VALIDATE_BOOLEAN);
            }
            
            $transient_key = "subjects_transient_".md5($searchkey)."_".md5($searchkey)."_".md5($searchkey)."_".$user_id;
            $bypasstransient = true; // remove after debug
            if($bypasstransient){
                delete_transient($transient_key);
            }
            $cached_response = get_transient($transient_key);


            // if user is and administror or teacher of the LD Group
            // allow user to access the courses under that LD Group
            $admin_groups = learndash_get_administrators_group_ids($user_id); // if user is a teacher or admin of the group
            $admin_allowed_courses = [];
            if(!empty($admin_groups)){
                foreach($admin_groups as $g){
                    $courses_in_group = learndash_group_enrolled_courses($g);
                    foreach($courses_in_group as $cg){
                        $admin_allowed_courses[] = $cg;
                    }
                }
            }
            if(SafarUser::is_user_institute_admin()){
                $institutes = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );
                foreach($institutes as $ins){
                    if(!empty($ins->school_data["courses"])){
                        foreach($ins->school_data["courses"] as $admin_course){
                            $admin_allowed_courses[] = $admin_course->ID;
                        }
                    }
                }
            }
            /* 
            End: // if user is and administror or teacher of the LD Group
            // allow user to access the courses under that LD Group
            */

            if( $cached_response ){
                $response = $cached_response;
                $response["from_cache"] = true;
            }else{

                $response = [];
                $lesson_in_progress_found = false;
                $subjects = [];

                // search groups by title
                $group_post_in = [];
                if(!empty($searchkey)){
                    $rs_groups = parent::wpdb()->get_results("SELECT * FROM ".parent::wpdb()->prefix."posts WHERE post_type='groups' AND post_title like '%".esc_sql($searchkey)."%' ");
                    $group_post_in[] = 0;
                    foreach($rs_groups as $g) $group_post_in[] = $g->ID;
                }

                if(!empty(self::$ld_group_category)){ // used for get_collection function
                    $terms = self::$ld_group_category;
                }

                foreach($terms as $term){
                    
                    $show_term = true;

                    if(!empty($filter_subject)){
                        if($filter_subject == $term->term_id) $show_term = true;
                        else $show_term = false;
                    }

                    if($show_term){

                        $args = [
                            'numberposts' => -1,
                            'orderby'   => 'menu_order post_title',
                            "order" => 'asc',
                            'post_type' => 'groups',
                            "suppress_filters" => true,
                            'tax_query' => [
                                    [
                                    'taxonomy' => 'ld_group_category',
                                        'field' => 'term_id',
                                        'terms' => $term->term_id
                                    ],
                                    [
                                        'taxonomy' => 'ld_group_tag',
                                        'field' => 'slug',
                                        'terms' => ['child-classroom'], // Exclude 'child-classroom' term
                                        'operator' => 'NOT IN' // Set the operator to exclude terms
                                    ]
                                ]
                        ];

                        if(!empty($group_post_in)) $args["include"] = $group_post_in;

                        // used for get_collection function
                        if(!empty(self::$ld_groupid)){
                            $args["include"] = self::$ld_groupid;
                        }

                        if( !SafarUser::is_user_institute_admin() && !SafarUser::is_user_teacher()){ // students role only
                            // for students only show courses under their group excluding the "entire school"
                            $user_learndash_groups = learndash_get_users_group_ids($user_id);
                            $student_courses = [];
                            foreach($user_learndash_groups as $user_ld_group_id){
                                $ld_group_tag = wp_get_post_terms($user_ld_group_id,"ld_group_tag");

                                $rs_entire_school = parent::wpdb()->get_results("SELECT * FROM ".parent::wpdb()->prefix."postmeta 
                                        WHERE meta_key='entire_school_container' 
                                        AND meta_value='".esc_sql($user_ld_group_id)."' ");
                                if(empty($rs_entire_school)){ // exclude courses under the entire group container
                                    $group_courses = self::get_ld_group_courses($user_ld_group_id);
                                    foreach($group_courses as $egid){
                                        $student_courses[] = $egid->post_id;
                                    }
                                }
                            }
                        }

                        //if(!empty($searchkey)) $search_par = " AND ( p.post_title like '".esc_sql($searchkey)."%'  ) ";
                        //print_r(["args"=>$args]);
                        $rs_learndash_groups = get_posts( $args );

                        $collections = [];
                        $subject_completed_steps = 0;
                        $subject_total_steps = 0;


                        foreach($rs_learndash_groups as $ld_group){

                            $buddyboss_group_id = get_post_meta($ld_group->ID,"_sync_group_id", true);
                            if(empty($buddyboss_group_id)){ // if buddyboss group id is not blank, meaning this LD group is a classroom not a Group Course
                                $learndash_groups = [];
                                
                                $rs_group_courses = self::get_ld_group_courses($ld_group->ID);

                                $collection_completed_steps = 0;
                                $collection_total_steps = 0;
                                if(!empty($rs_group_courses)){

                                    $courses = [];
                                    $collection_url = "";
                                    foreach($rs_group_courses as $course){
                                        $last_course_id = $course->post_id;
                                        $rc = get_post($course->post_id);
                                        $rs_lessons = learndash_get_lesson_list($rc->ID);
                                        $course_progress = learndash_user_get_course_progress($user_id, $rc->ID);

                                    
                                        $lessons = [];
                                        $found_uncompleted_topic = false;
                                        
                                        $in_progress_course = [];

                                        foreach($rs_lessons as $rl){

                                            $rs_topics = learndash_get_topic_list($rl->ID, $rc->ID);
                                            $topics = [];
                                            $resume_topic = [];
                                            foreach($rs_topics as $rt){
                                                $topic_details = [
                                                    "topic_name" => $rt->post_title,
                                                    "topic_id" => $rt->ID,
                                                    "topic_url" => get_permalink($rt->ID),
                                                    "topic_image" => get_the_post_thumbnail_url($rt->ID),
                                                    "completed" => ( $course_progress["topics"][$rl->ID][$rt->ID] ) ? true: false
                                                ];
                                                $topics[] = $topic_details;

                                                // get last uncompleted topic
                                                if(!empty($course_progress)){
                                                    
                                                    if (empty($course_progress["topics"][$rl->ID][$rt->ID])) {

                                                        if (!$found_uncompleted_topic) {

                                                            $found_uncompleted_topic = true;
                                                            
                                                            $resume_topic[] = $topic_details;
                                                        }
                                                    }
                                                }

                                            }

                                            $lessons[] = [
                                                "lesson_name" => $rl->post_title,
                                                "lesson_id" => $rl->ID,
                                                "lesson_url" => get_permalink($rl->ID),
                                                "topics" => $topics,
                                                "completed" => ( $course_progress["lessons"][$rl->ID] ) ? true: false
                                            ];

                                            if( $course_progress["status"] == "in_progress"){
                                                $lesson_in_progress_found = true;
                                                if(!empty($resume_topic)){
                                                    $in_progress_course[] = [
                                                        "course_id" => $rc->ID,
                                                        "lesson_id" => $rl->ID,
                                                        "category" => $term->name,
                                                        "background_image" => get_field("background_image", "category_".$term->term_id),
                                                        "background_color" => get_field("background_color", "category_".$term->term_id),
                                                        "topic" => $resume_topic
                                                    ];
                                                }
                                            }

                                        }

                                        $completed_steps_count = ( empty($course_progress["completed"])) ? 0:$course_progress["completed"];
                                        $total_steps = ( empty($course_progress["total"])) ? 0:$course_progress["total"];

                                        if($total_steps > 0 ){
                                            $percent_completed = number_format( ($completed_steps_count / $total_steps ) * 100, 0 );
                                        }else{
                                            $percent_completed = 0;
                                        }
                                        
                                        $course_status = ( empty($course_progress["status"])) ? "not_started":$course_progress["status"];

                                        // get the last uncompleted topic of the course
                                        // this is used on the dashboard Pickup where you left of section
                                        
                                        $is_enrolled = in_array($rc->ID, learndash_user_get_enrolled_courses($user_id)) ;

                                        if(!$is_enrolled){
                                            // check if course is one of admin/teacher allowed course to access
                                            $is_enrolled = in_array($rc->ID, $admin_allowed_courses);
                                        }
                                        
                                        if(!current_user_can('administrator')){
                                            if( !SafarUser::is_user_institute_admin() && !SafarUser::is_user_teacher()){ // students role only{
                                                $is_enrolled = false;
                                                if(in_array($rc->ID, $student_courses)) $is_enrolled = true;
                                            }
                                        }
                                        if($is_enrolled){
                                            $courses[] = [
                                                "course_name" => $rc->post_title,
                                                "course_id" => $rc->ID,
                                                "course_url" => get_permalink($rc->ID),
                                                "course_image" => get_the_post_thumbnail_url($rc->ID, "full"),
                                                "lessons" => $lessons,
                                                //"user_progress" => $course_progress,
                                                "lesson_in_progress" => $in_progress_course,
                                                "status" => $course_status,
                                                "completed_steps" => $completed_steps_count,
                                                "total_steps" => $total_steps,
                                                "percent_completed" => $percent_completed,
                                                "is_enrolled" => $is_enrolled,
                                            ];


                                            
                                        }

                                        $collection_completed_steps += $completed_steps_count;
                                        $collection_total_steps += $total_steps;

                                        if(empty($collection_url)){
                                            if($course_status != "completed"){
                                                $collection_url = get_permalink($rc->ID);
                                            }
                                        }
                                    }

                                }
                                
                                if(empty($collection_total_steps)){
                                    $progress_percent_collection = 0;
                                }else{
                                    $progress_percent_collection = number_format( ( $collection_completed_steps / $collection_total_steps ) * 100 , 0);            
                                }

                                $status = "not_started";
                                if($progress_percent_collection > 0 && $progress_percent_collection < 100 ){
                                    $status = "in_progress";
                                }elseif ( $progress_percent_collection >= 100 ){
                                    $status = "completed";
                                }else{
                                    $status = "not_started";
                                }

                                
                                
                                if(empty($collection_url)) $collection_url = get_permalink($last_course_id);

                                $show = true;

                                if(!empty($filter_status)){
                                    if( strtolower($filter_status) == strtolower($status) ){
                                        $show = true;
                                    }else{
                                        $show = false;
                                    }
                                }

                                
                                if($show){

                                    if(!empty($courses)){

                                        // is_demo_user
                                         
                                        $collections[] = [
                                            "courses" => $courses,
                                            "name" => $ld_group->post_title,
                                            "group_id" => $ld_group->ID,
                                            "image" => get_the_post_thumbnail_url($ld_group->ID),
                                            "collection_url" => get_permalink($ld_group->ID)."?".mt_rand(), //$collection_url, LD group url
                                            "completed_steps" => $collection_completed_steps,
                                            "total_steps" => $collection_total_steps,
                                            "progress" => $progress_percent_collection,
                                            "status" => $status,
                                            "post_type" => $ld_group->post_type,
                                            "demo_user_access" => SafarUser::demo_user_has_access($ld_group->ID),
                                        ];
                                    }
                                }

                                $subject_completed_steps += $collection_completed_steps;
                                $subject_total_steps += $collection_total_steps;
                            }
                        }

                        if(empty($subject_total_steps)){
                            $progress_percent = 0;
                        }else{
                            $progress_percent = number_format( ( $subject_completed_steps / $subject_total_steps ) * 100 , 0);
                        }

                        if(!empty($collections)){
                            $subjects[] = [
                                "term_id" => $term->term_id,
                                "name" => $term->name,
                                "collections" => $collections,
                                "background_image" => get_field("background_image", "category_".$term->term_id),
                                "background_color" => get_field("background_color", "category_".$term->term_id),
                                "completed_steps" => $subject_completed_steps,
                                "total_steps" => $subject_total_steps,
                                "progress_percent" => $progress_percent
                            ];
                        }
                    
                    }

                }

                // for teacher training check if user is enrolled to the course
                // if not do not show teacher training
                $has_enrolled_teacher_training = false;
                $teacher_training_key = false;
                foreach($subjects as $key => $subject){
                    if($subject["name"] == "Teacher Training"){
                        $teacher_training_key = $key;
                        foreach($subject["collections"] as $collection){
                            foreach($collection["courses"] as $course){
                                if($course["is_enrolled"]){
                                    $has_enrolled_teacher_training = true;
                                }
                            }
                        }
                    }
                }

                if(!$has_enrolled_teacher_training){
                    if($teacher_training_key !== false){
                        unset($subjects[$teacher_training_key]);
                    }
                }

                $response["subjects"] = $subjects;
                $response["lesson_in_progress_found"] = $lesson_in_progress_found;
                $response["from_cache"] = false;

                $transient = set_transient( $transient_key, $response, 3600 );
            }

            $response["transient_key"] = $transient_key;

            $response = new \WP_REST_Response($response);
            return $response;
            
        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function get_group_course_details($group_id, $course_id){
        self::$ld_groupid = $group_id;
        $courses = self::get_collection([]);
        $course_details = [];

        if(!empty($courses->data["courses"])){
            foreach($courses->data["courses"] as $course){
                if($course["course_id"] == $course_id){
                    $course_details = $course;
                }
            }
        }

        return $course_details;
    }   

    static function get_group_details($group_id){
        self::$ld_groupid = $group_id;
        $collection = self::get_collection([]);
        return $collection->data;
    }   



    static function get_collection( $request ){

        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            if(!empty($request)) self::$ld_groupid = $request->get_param("id");

            if(empty(self::$ld_groupid)){
                return new \WP_REST_Response('Invalid Group ID', 401);
            }
            //ld_group_category
            self::$ld_group_category = get_the_terms(self::$ld_groupid, "ld_group_category");

            $subjects = self::get_subjects( $request )->data;

            $collections = [];
            if(!empty($subjects->subjects->collections)) $collections = $subjects->subjects->collections;
            foreach($subjects["subjects"] as $subject){
                $collections = $subject["collections"][0];
            }
            $response = new \WP_REST_Response($collections);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
        
    }

    static function get_searched_posts( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $search = $request->get_param("search");
            $page = $request->get_param("page");
            $length = $request->get_param("length");

            $start = 0;
            if($page > 0){
                $start = $page * $length;
            }

            $posts = [];
            $base_sql = " SELECT * FROM ".parent::wpdb()->prefix."posts 
                            WHERE post_status='publish' 
                                AND post_type='sfwd-lessons'
                                AND ( post_title like '%".esc_sql($search)."%' || post_content like '%".esc_sql($search)."%' )
                                AND post_status = 'publish'
                            ORDER BY menu_order ASC  
                            ";
            $sql = $base_sql." LIMIT $start, $length  ";
             
            $rs_posts = parent::wpdb()->get_results($sql);

            $start = ( $page + 1) * $length;

            $sql = $base_sql." LIMIT $start, $length  ";
            $next_posts = parent::wpdb()->get_results($sql);

            foreach($rs_posts as $post){
                $course_id = get_post_meta($post->ID, "course_id", true);
                $course = get_post($course_id);
                $is_enrolled = in_array($course_id, learndash_user_get_enrolled_courses($user_id)) ;
                if($is_enrolled){
                    $posts[] = [
                        "ID" => $post->ID,
                        "title" => $post->post_title,
                        "link" => get_permalink($post->ID),
                        "thumbnail" => get_the_post_thumbnail_url($post->ID),
                        "course_id" => $course_id,
                        "course" => $course->post_title,
                        "enrolled" => $is_enrolled,
                        "meta" => get_post_meta($post->ID),
                        "course_id2"=> learndash_get_course_id($post->ID)
                    ];
                }
            }

            $response["result"] = $posts;
            $response["has_next"] = ( !empty($next_posts) ) ? true: false;
            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }
    

    static function get_course_pathway( $request ){
        $user_id = parent::pb_auth_user($request);
    
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        } 

        $return = [];
        $pathPercent = 0;
        $steps = 0;
        $currentStep = 0;
        $streak = 0;
        $course_id = $request->get_param( 'id' );
        $previous_completed = true;
        $active_step = false;
        $found_active_step = false;
        $completed_business_learning = false;
        
        $course_progress = learndash_user_get_course_progress( $user_id, $course_id);

        $all_completed = true;
        // Check rows exists.
        if( have_rows('steps',$course_id) ):
            // Loop through rows.
            $steps = count(get_field('steps', $course_id));
           
    
            while( have_rows('steps',$course_id) ) : the_row();
                
                
                // Set Initial Values
                $currentStep++;
                $youAreHere = false;
                $complete = false;
                $url = "javascript:void 0;";
                
                
                //Code for figuring out if step has been completed or not
                /*
                 *******
                  CODE FOR FIGURING OUT IF STEP COMPLETE GOES HERE
                  a Helpful function is 
                  learndash_user_progress_is_step_complete( integer $user_id,  integer $course_id,  integer $step_id )
                  https://developers.learndash.com/function/learndash_user_progress_is_step_complete/
                 *******
                */
                
                
                //Override for testing
                $lesson_object = get_sub_field("linked_object");
                $lesson_id = $lesson_object->ID;
                /* 
                $total_course_steps = count(learndash_get_course_steps($course_id)); 
                $completed_steps  = learndash_course_get_completed_steps($user_id, $course_id);
                */

                $topics = learndash_get_topic_list($lesson_id, $course_id);
                $quiz = learndash_get_lesson_quiz_list($lesson_id, $user_id, $course_id);
                if(!empty($quiz)){
                    foreach($quiz as $equiz){
                        array_push($topics, $equiz["post"]);
                    }
                }	
 
                $total_course_steps = 0;
                $completed_steps = 0;

                foreach($topics as $topic){
                    $topic_completed = false;

                    if($topic->post_type == "sfwd-quiz"){
                        $topic_completed = learndash_is_quiz_complete($user_id, $topic->ID, $course_id);
                    }else{
                        $topic_completed = $course_progress["topics"][$lesson_id][$topic->ID];
                    }

                    
                    $total_course_steps++;

                    if($topic_completed){
                        $completed_steps++;
                    }
                }

                $incomplete_image = get_sub_field('incomplete_image');
                $title = get_sub_field('title');
                $step_progress = 0;
                if($total_course_steps > 0){
                    $step_progress = $completed_steps / $total_course_steps;
                }
    
                if (get_sub_field('completed')){
                  $completed = get_sub_field('completed');
                }
    
                
                
                //Handle Final/Finish Step
               
                if ($currentStep == $steps)
                {
                  if ($streak + 1 == $steps)
                  {
                    $image = get_sub_field('complete_image');
                    $image = $image['url'];
                    $pathPercent = get_sub_field('path_percent');
                    $streak++;
                  } else {
                    $image = get_sub_field('incomplete_image');
                    $image = $image['url'];
                  }
                }
                else {
                  if ($completed){
                    
                    $image = get_sub_field('complete_image');
                    $image = $image['url'];
                    $streak++;
                    if ($streak  == $currentStep) {
                      $pathPercent = get_sub_field('path_percent');
                      $youAreHere = true;
                    }
                  }else {
      
                    $image = get_sub_field('incomplete_image');
                    $image = $image['url'];
                    $streak = 0;
                  }
                }
                
                $url = get_permalink($lesson_id);
                if(!empty(get_sub_field('url'))) $url = get_sub_field('url');
                
            
                if($step_progress >= 1) $completed = true;
                else $completed = false;

                if($lesson_object->post_type == "sfwd-quiz"){
                    $completed = learndash_is_quiz_complete($user_id, $lesson_object->ID, $course_id);

                    if($completed){
                        $step_progress = 1;
                        $completed_steps = 1;
                    }
                }
 
                
                if($previous_completed){
                  
                    if(empty($found_active_step)){
                        if(!$active_step && !$completed) {
                            $active_step = true;
                            $pathPercent = get_sub_field('path_percent');
                            $found_active_step = true;
                        }else{
                            $active_step = false;
                        }
                    }else{
                        $active_step = false;
                    }
                    
                }else{
                    $active_step = false;
                }

                if( strtolower(get_sub_field('title')) == "finish"){
                    if($previous_completed){
                        $completed = true;
                       
                        $completed_business_learning = true;

                        $step_progress = 1;

                    }
                }

                if(!$completed) $all_completed = false;

                if( strtolower($title) == "finish" ){
                    if($all_completed){
                        $step_progress = 1;
                        $completed = 1;
                    }else{
                        $step_progress = 0;
                        $completed = 0;
                    }
                }

                $return['steps'][] = array(
                  'title' => get_sub_field('title'),
                  'completed' => $completed,
                  'position_x' => get_sub_field('postion_x'),
                  'position_y' => get_sub_field('position_y'),
                  'url' => $url,
                  'path_percent' => $pathPercent,
                  'youAreHere' => $youAreHere,
                  'image' => $incomplete_image["url"],//$image,
                  'image_complete' => get_sub_field('complete_image')["url"],
                  'step' => $currentStep,
                  'streak' => $streak,
                  'previous_completed' => $previous_completed,
                  'active_step' => $active_step,
                  "total_steps" => $total_course_steps,
                  "completed_steps" => $completed_steps,
                  "step_progress" => floor($step_progress * 100) / 100,
                  "step_progress_percent" => number_format( floor($step_progress * 100),0),
                  "not_started" => (empty($step_progress)) ? true:false,
                  "found_active_step" => $found_active_step,
                  "last_step" => ( strtolower($title) == "finish") ? true:false,
                  "background_color" => get_sub_field('background_color'),
                  "linked_object" => get_sub_field("linked_object"),
                  "all_completed" => $all_completed,
                  "text_color" => get_sub_field("text_color"),
                  "progress_border_color" => get_sub_field("progress_border_color")
                );
                
                if($completed) $previous_completed = true;
                else $previous_completed = false;
                

                /*
                if( strtolower($title) == "finish"){
                    if($pathPercent >= 100 ){
                        gamipress_trigger_event( array(
                            'event' => 'completed_business_learning_path',
                            'user_id' => $user_id
                        ) );
                    }
                }*/

    
            // End loop.
            endwhile;
        
        // No value.
        else :
            // Do something...
        endif;
        
        if($all_completed){
            // mark the course complete here
            $return["course_mark_complete"] = learndash_process_mark_complete($user_id ,  $course_id); 
        }

        //set the spot for the path
        $return['pathPercent'] = $pathPercent;
        $return["course_complete"] = learndash_course_completed( $user_id,  $course_id );
        $return["course"] = get_post($course_id);
        $return["certificate_link"] = learndash_get_course_certificate_link($course_id, $user_id);
        $return["all_completed"] = $all_completed;
        $return["course_id"] = $course_id;

        if(!empty($return["certificate_link"])){

            $usermeta = get_user_meta( $user_id, '_sfwd-quizzes', true );
            $course_completions = [];
            foreach($usermeta as $emeta){
                if($emeta["course"] == $course_id){
                    $course_completions[$emeta["completed"]] = $emeta;
                }
            }
            krsort($course_completions);
            $latest_completed = array_shift($course_completions);

            ob_start();
            $name = get_user_meta($user_id,"first_name",true)." ".get_user_meta($user_id,"last_name",true);
            $course_name = get_post($course_id)->post_title;
            $date = date("F d, Y", $latest_completed["completed"]);
            require_once("certificate.php");
            $certificate_html = ob_get_contents();
            ob_end_clean();

            $certificate_name = $user_id."-".$course_id;
            $return["certiticate_html"] = file_put_contents(plugin_dir_path(__FILE__).'certificate_images/'.$certificate_name.'.html',$certificate_html);
            $return["certificate_raw_url"] = site_url("wp-content/plugins/pbdigital-api/certificate_images/".$certificate_name.".html");
            
            $certificate_name = plugin_dir_path(__FILE__).'certificate_images/'.$certificate_name.'.png';
            $command = "/usr/local/bin/wkhtmltoimage --transparent ".$return["certificate_raw_url"]." ".$certificate_name;


            $return["pdf_to_image"] = exec($command);
            $return["certificate_image"] = site_url("/wp-content/plugins/pbdigital-api/certificate_images/".$user_id.'-'.$course_id.'.png'."?".uniqid());
        }

        //delete_user_meta($user_id, "seen_course_certificate_".$course_id);

        if(empty($return["course_complete"])){
            $return["seen_course_certificate"] = false;
            delete_user_meta($user_id, "seen_course_certificate_".$course_id);
        }else{
            $return["seen_course_certificate"] = get_user_meta($user_id, "seen_course_certificate_".$course_id, true);
            update_user_meta($user_id, "seen_course_certificate_".$course_id , date("Y-m-d H:i:s"));
        }
        //$return["completed_business_learning"] = $completed_business_learning;
    
       
        $response = new \WP_REST_Response($return);
        
        return $response;

    }

    static function get_ld_group_courses($ld_group_id){

        $sql = "SELECT DISTINCT post_id, menu_order FROM ".parent::wpdb()->prefix."postmeta  as pm
                        INNER JOIN ".parent::wpdb()->prefix."posts as p ON pm.post_id = p.ID
                        WHERE meta_key='learndash_group_enrolled_".$ld_group_id."'
                        ORDER BY menu_order ASC 
                ";

        $rs_group_courses = parent::wpdb()->get_results($sql);

        return $rs_group_courses;
        
    }
}

function pbd_resize_png_image($pngFile, $sourceDirectory) {

    $newWidth = 600; // Desired width
    $newHeight = 0; // Leave the height as 0 to maintain aspect ratio

    // Load the source image with transparency
    $sourceImage = imagecreatefrompng($pngFile);

    // Get the dimensions of the source image
    $sourceWidth = imagesx($sourceImage);
    $sourceHeight = imagesy($sourceImage);

    // Calculate the new height while maintaining the aspect ratio
    $newHeight = ($newWidth / $sourceWidth) * $sourceHeight;

    // Create a new image with transparency support
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Enable alpha blending and save alpha channel
    imagesavealpha($newImage, true);
    imagealphablending($newImage, false);

    // Fill the new image with transparency
    $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127); // Adjust alpha value as needed
    imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);

    // Copy and resize the source image to the new image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

    // Generate a unique filename for the resized image
    $fileInfo = pathinfo($pngFile);
    $sourceImagePath = $sourceDirectory . $fileInfo['filename'] . '.png';

    // Save the resized image with transparency
    imagepng($newImage, $sourceImagePath);

    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($newImage);
}

/***
 * function to reduce certificate image
 */

 if(isset($_GET['resize_certificate_images']) && $_GET['resize_certificate_images'] == 1) {

    // Set the path to the folder containing PNG images
    $folderPath = plugin_dir_path(__FILE__).'certificate_images/';

    // Get all PNG files in the folder
    $pngFiles = glob($folderPath . '*.png');

    foreach ($pngFiles as $pngFile) {
        echo $pngFile.'<br>';
        pbd_resize_png_image($pngFile, $folderPath);
    }    

    echo 'done...';
    exit;

}