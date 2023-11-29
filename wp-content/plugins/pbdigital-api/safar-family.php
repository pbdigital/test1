<?php 
namespace Safar;
use Buddyboss\LearndashIntegration\Library\SyncGenerator;
use uncanny_learndash_groups\MigrateLearndashGroups;
use uncanny_learndash_groups\LearndashGroupsPostEditAdditions;
use DateTime;
use Safar\SafarSchool;
use Safar\SafarCourses;
use Safar\SafarUser;

class SafarFamily extends Safar{

    /* API for creating family group, this is used for integration woocommerce 
    successful purchase from Safar Publications to J2J
    */

    static function submit_family_details( $request ){

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
        $order_data["is_gifted"] = $request->get_param("is_gifted");
        $order_data["gifter_user_id"] = $request->get_param("gifter_user_id");
        $order_data["product_name"] = $request->get_param("product_name");
        $order_data["subscription_period"] = $request->get_param("subscription_period");
        $order_data["subscription_seats_count"] = $request->get_param("subscription_seats_count");
        $order_data["order_notes"] = $request->get_param("order_notes");
        $order_data["gifter_user_email"] = $request->get_param("gifter_user_email");
        $order_data["order_id"] = $order_id;
        $order_data["gifter_first_name"] = $request->get_param("gifter_first_name");

        #$response["order_data"] = $order_data;
        $response["order_id"] = $order_id;

        $email = $order_data["email"];
        $user_id = email_exists($email);
        $send_welcome_email = false;

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
        $user_id = $user->ID;
	    $user->set_role( "group_leader" );

        if($user_exists){
            $safar_publications_user_id = get_user_meta($user_id,"safar_publications_user_id",true);
            if(empty($safar_publications_user_id)) $send_welcome_email = true;
        }

        update_user_meta($user_id,"safar_publications_user_id", $order_data["user_id"]);

        // create / update group 
        
        // check first if the user has an existing family group
        // if yes, just use and update that group
        $family_groups = self::get_family_groups_by_user_id( $user_id );
        $response["family_groups"] = $family_groups;
        
        if(empty($family_groups)){
            $group_name = $order_data["last_name"]." Family";
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
            // set completed family onboarding to false
            update_post_meta($post_id,"completed_family_onboarding",0);
            update_post_meta($post_id, "order_id", $order_id);
        }else{
            $post_id = $family_groups[0]->ID;
        }

        $response["group_id"] = $post_id;

        update_post_meta($post_id, "order_meta", json_encode($order_data));
        update_post_meta($post_id,"safar_publications_user_id", $order_data["user_id"]);
        update_post_meta($post_id,"subscription_status", $order_data["subscription_status"]);
        wp_set_object_terms($post_id, 'family-group', 'ld_group_tag', false);

        update_post_meta($post_id,"is_gifted", ( ( !empty($order_data["is_gifted"]) ) ? "yes":"" ));
        update_post_meta($post_id,"gifter_user_id", $order_data["gifter_user_id"]);

        update_post_meta($post_id,"product_name", $order_data["product_name"]);
        update_post_meta($post_id,"subscription_period", $order_data["subscription_period"]);
        update_post_meta($post_id,"subscription_seats_count", $order_data["subscription_seats_count"]);
        update_post_meta($post_id,"order_notes", $order_data["order_notes"]);
        update_post_meta($post_id,"gifter_user_email", $order_data["gifter_user_email"]);
        update_post_meta($post_id,"gifter_first_name", $order_data["gifter_first_name"]);
        
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
            \Safar\SafarSchool::send_parent_welcome_mail($user_id, $post_id);
            $response["welcome_email_sent"] = true;
        }
        

        // send custom emails to Gifter and Receiver
        // if subscription is gifted
        if(!empty($order_data["is_gifted"])){
            SafarUser::send_gifter_receiver_emails($post_id);
        }

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function get_family_groups_by_user_id( $user_id ){
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
        
        if(!empty($institute_ids)){
            $user_id = parent::$user_id;

            $transient_key = "user_institutes_".$user_id;
            

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
                        $institutes[$key]->school_data = \Safar\SafarSchool::get_school_data($group->ID);
                    }
                }
                //set_transient( $transient_key, $response, 1800 ); // 3600 = 1 hour
            }
            return $institutes;
        }else{
            return false;
        }
    }
    

    static function get_login_info_by_order_id($id){

        $response["id"] = $id;

        $result = parent::wpdb()->get_results("SELECT * FROm ".parent::wpdb()->prefix."postmeta
            WHERE meta_key='order_id' AND meta_value='".esc_sql($id)."' 

            LIMIT 1 ");

        
        if(!empty($result)){
            //$response["meta"] = $result[0];
            $group_id = $result[0]->post_id;
            $group_leaders = learndash_get_groups_administrator_ids($group_id);

            $response["group_id"] = $group_id;
            $response["group_leaders"] = $group_leaders;

            $response["user_id"] = $group_leaders[0];
        }

        return $response;
    }

    static function get_login_info_by_safarpub_user_id($safar_user_id){
        $result = parent::wpdb()->get_results("SELECT * FROm ".parent::wpdb()->prefix."usermeta
            WHERE ( meta_key = 'safar_publications_user_id' || meta_key='safar_publications_institute_user_id' ) AND meta_value='".esc_sql($safar_user_id)."' 

            LIMIT 1 ");
        
        if(!empty($result)){
            $user_id = $result[0]->user_id;
            return $user_id;
        }else{
            return false;
        }
    }

    static function get_family_details($group_id){
        $family_post = get_post($group_id);
        #$family_meta = get_post_meta($group_id);
        $total_seats     = ulgm()->group_management->seat->total_seats( $group_id );
        $children = learndash_get_groups_user_ids($group_id);
        $seats_taken = count($children);
        $remaining_seats = $total_seats - $seats_taken;

        $children_details = [];
        if(!empty($children)){
            foreach($children as $child_id){
                $user_data = get_userdata( $child_id );
                $username = $user_data->user_login;
                
                $age = 0;
                $birthday = get_user_meta($child_id,"date_of_birth",true);
                if(!empty($birthday)){
                    $birthdate = DateTime::createFromFormat('D M d Y', $birthday);
                    $today = new DateTime('now');
                    $age = $birthdate->diff($today)->y;
                }
                
                $gender = get_user_meta($child_id,"gender",true);
                if(empty($gender)) $gender = "male";

                $children_details[] = [
                    "gender" => $gender,
                    "username" => $username,
                    "user_email" => $user_data->data->user_email,
                    "name" => get_user_meta($child_id,"first_name",true)." ".get_user_meta($child_id,"last_name",true),
                    "date_of_birth" => $birthday,
                    "age" => $age,
                    "id" => $child_id
                ];
            }
        }

        return ["post" => $family_post, 
                "total_seats" => $total_seats, 
                "remaining_seats" => $remaining_seats,
                "seats_taken" => $seats_taken,
                "children" => $children_details
            ];
    }

    static function api_family_details( $request ){ // function used for api calls
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $gid = $request->get_param("gid");

        $response = self::get_family_details($gid);
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function delete_child_account( $request ){ // function used for api calls
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $gid = $request->get_param("gid");
        $childid = $request->get_param("childid");

        $children = learndash_get_groups_user_ids($gid);
        $updated_children = [];
        foreach($children as $echildid){
            if($echildid != $childid){
                $updated_children[] = $echildid;
            }
        }

        learndash_set_groups_users($gid, $updated_children );

        // delete wp user account
        require_once(ABSPATH.'wp-admin/includes/user.php');
        $deleted = wp_delete_user($childid,0);
        $response["deleted"] = $deleted;
        $response["updated_children"] = $updated_children;

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function update_family_subscription_status( $request ){

        $order_id = $request->get_param("order_id");
        $subscription_status = $request->get_param("subscription_status");

        $response["order_id"] = $order_id;
        $response["order_status"] = $order_status;

        // get group id by order_id
        if(!empty($order_id)){
            $groups = parent::wpdb()->get_results("SELECT * FROM ".parent::wpdb()->prefix."postmeta WHERE meta_value='".$order_id."' ");
            $group_id = $groups[0]->post_id;
            if(!empty($group_id)){
                update_post_meta($group_id,"subscription_status",$subscription_status);
            }
        }
        
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function update_family( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $family_name = $request->get_param("family_name");
        $parent_name = $request->get_param("parent_name");
        $parent_email = $request->get_param("parent_email");
        $parent_phone = $request->get_param("parent_phone");
        $facial_features = $request->get_param("facial_features");
        $gid = $request->get_param("gid");
        $parent_email = $request->get_param("parent_email");
        $password = $request->get_param("password");

        wp_update_post( ["ID"=>$gid,"post_title"=>$family_name] );

        update_post_meta($gid, "parent_name", $parent_name);
        update_post_meta($gid, "parent_email", $parent_email);
        update_post_meta($gid, "parent_phone", $parent_phone);
        update_post_meta($gid, "facial_features", $facial_features);
        update_post_meta($gid, "completed_family_onboarding", true);

        $group_params = [ 'name' => $family_name ];
        // Update the group name using the BuddyBoss function
        $bpgroup_id = groups_create_group( $group_params );
        update_post_meta($gid,"_sync_group_id", $bpgroup_id);

        // API call to SafarPublications Order ID, Family_setup = true
        $order_id = get_post_meta($gid, "order_id", true);
        \Safar\SafarPublications::api_request(["endpoint"=>"/family/order/".$order_id,"method"=>"PUT"]);

        $response = SafarSchool::fully_sync_buddyboss_learndash_group($gid);

    

        $response = new \WP_REST_Response($response);
        return $response;

    }

    static function family_update_parent_password($request){

        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $password = $request->get_param("password");
        $email = $request->get_param("email");
        // update user password
        $user = get_user_by('email', $email);
        $set_password = false;
        if ($user) {
            $set_password = wp_set_password($password, $user->ID);
            wp_set_auth_cookie($user->ID);
        }

        $response["password"] = $password;
        $response["email"] = $email;
        $response["user_id"] = $user_id;
        $response["set_password"] = $set_password;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function is_user_parent($request=[]){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $safar_publications_user_id = get_user_meta($user_id,"safar_publications_user_id",true);
        // if Relationship meta is not blank, meaning this user is created using the Manage Institute Families page
        $relationship = get_user_meta($user_id,"relationship",true); 
        if(empty($safar_publications_user_id) && empty($relationship) ) return false;
        else return true;
    }

    static function is_user_institute_parent($request=[]){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        if(current_user_can('administrator')){
            return false;
        }
        
        $institute = self::get_institute_by_parent_id($user_id);
    
        if(empty($institute)) return false;
        else return true;
    }

    static function create_child_account( $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $childname = $request->get_param("childname");
        $gender = $request->get_param("gender");
        $birthday = $request->get_param("birthday");
        $email = $request->get_param("email");
        $username = $request->get_param("username");
        $password = $request->get_param("password");
        $gid = $request->get_param("gid");
        $userid = $request->get_param("userid"); // if userid is provide, update the user


        $error = false;
        if(!empty($email)){
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response["success"] = false;
                $response["error_message"] = "<b>".$email."</b> email is invalid.";
                $error = true;
            }
        }

        if(empty($userid)){
            $exists = username_exists( $username );
            if ( $exists ) {
                $response["success"] = false;
                $response["exists"] = $exists;
                $response["error_message"] = "Username <b>".$username."</b> already exists.";
                $error = true;
            }
        }

        if(!$error){
            if(empty($gid)){
                $response["success"] = false;
                $response["error_message"] = "Family group id is missing";
            }else{

                if(!empty($userid)){

                    $user_data = array(
                        'ID' => $userid,
                        'user_login' => $username,
                        'user_email' => $email,
                        'user_pass' => wp_hash_password( $password )
                    );
                    
                    $updated = wp_update_user( $user_data );

                    update_user_meta($userid, "first_name", $childname);
                    update_user_meta($userid, "date_of_birth", $birthday);
                    update_user_meta($userid, "gender", $gender);
                    
                    wp_set_password($password, $userid);

                    $response["success"] = true;
                    $response["edit"] = true;
                    $response["updated"] = $updated;
                    $response["user_data"] = $user_data;
                    $response["meta"] = [$childname, $birthday, $gender];
                    $response["password"] = $password;
                    $response["userid"] = $userid;
                }else{

                    $user_id = wp_create_user( $username, $password, $email );
                    if ( is_wp_error( $user_id ) ) {
                        $error_message = $user_id->get_error_message();
                        $response["success"] = false;
                        $response["error_message"] = $error_message;
                    } else {
                        update_user_meta($user_id, "first_name", $childname);
                        update_user_meta($user_id, "date_of_birth", $birthday);
                        update_user_meta($user_id, "gender", $gender);
                        
                        // add child to family group
                        $children = learndash_get_groups_user_ids( $gid, true );
                        $children[] = $user_id;
                        learndash_set_groups_users($gid, $children );


                        
                        $response["success"] = true;
                    }
                }
            }
        }
        
        $response = new \WP_REST_Response($response);
        return $response;
    }

    // family - institute api endpoints
    static function create_institute_family($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $institute_id = $request->get_param("institute_id");
        $family_name = $request->get_param("family_name");
        $family_id = $request->get_param("family_id");

        $parent_name_1 = $request->get_param("parent_name_1");
        $parent_relationship_1 = $request->get_param("parent_relationship_1");
        $parent_phone_1 = $request->get_param("parent_phone_1");
        $parent_email_1 = $request->get_param("parent_email_1");

        $parent_name_2 = $request->get_param("parent_name_2");
        $parent_relationship_2 = $request->get_param("parent_relationship_2");
        $parent_phone_2 = $request->get_param("parent_phone_2");
        $parent_email_2 = $request->get_param("parent_email_2");

        $emergency_contact_name = $request->get_param("emergency_contact_name");
        $emergency_contact_relationship = $request->get_param("emergency_contact_relationship");
        $emergency_contact_phone = $request->get_param("emergency_contact_phone");

        $students = $request->get_param("students");

        // from import csv
        $arr_fields = ["student_1","student_2","student_3","student_4","student_5"];
        $school_details = \Safar\SafarSchool::get_school_details([]);
        $all_student_ids = [];
        if(!empty($school_details->data["students"])){
            foreach($school_details->data["students"] as $ea_student){
                $all_student_ids[] = $ea_student->data->ID;
            }
        }
        
        foreach($arr_fields as $field){
            $has_username = $request->get_param($field);
            if(!empty($has_username)){
                $student_user = get_user_by('login', $has_username);
                if(!empty($student_user->ID)){
                    if(in_array($student_user->ID, $all_student_ids)) $students[] = $student_user->ID;
                }
            }
        }
        // end from import csv

        // add parent 1 account check
        $data = [];
        $data["email"] = $parent_email_1;
        $data["first_name"] = $parent_name_1;
        $data["phone"] = $parent_phone_1;
        $data["relationship"] = $parent_relationship_1;
        $data["role"] = "group_leader";
        $data["is_parent"] = true;
        $data["institute_id"] = $institute_id;
        $data["username"] = str_replace(' ', '', $data["first_name"]) . mt_rand();
        $parent_1_user_id = SafarUser::create_user_account($data);

        $data = [];
        $data["email"] = $parent_email_2;
        $data["first_name"] = $parent_name_2;
        $data["phone"] = $parent_phone_2;
        $data["relationship"] = $parent_relationship_2;
        $data["role"] = "group_leader";
        $data["institute_id"] = $institute_id;
        $data["is_parent"] = true;
        $data["username"] = str_replace(' ', '', $data["first_name"]) . mt_rand();

        if(!empty($data["email"])) $parent_2_user_id = SafarUser::create_user_account($data);
        else $parent_2_user_id = 0;

       
        // create learndash group / family
        $group_name = $family_name;
        $new_post = array(
            'post_title'    => $group_name,
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_author'   => $user_id,
            'post_type'     => 'groups',
            'post_parent'   => 0
        );
        $family_group_id = wp_insert_post( $new_post );

        // add both parents to the family group
        $admin_result = learndash_set_groups_administrators($family_group_id, [$parent_1_user_id, $parent_2_user_id]);

        // add students to family group
        learndash_set_groups_users($family_group_id, $students );

        /* 
        Create Buddyboss Sync
        */
        $newGroup  = groups_create_group(["name"=>$group_name]);
        $ldGroupId = $family_group_id;
        $generator = new SyncGenerator( $newGroup, $ldGroupId );
        $generator->updateBuddypressGroup( $ldGroupId, $newGroup );
        $generator->fullSyncToBuddypress();
        
        // activate Uncanny Group Management
        $group_post = get_post($family_group_id);
        $MigrateLearndashGroups = new MigrateLearndashGroups;
        $MigrateLearndashGroups->process_upgrade($group_post);
        
        wp_set_object_terms($family_group_id, ['family-group','institute-family-group'], 'ld_group_tag', false);

        $data = [];
        $data["family_id"] = $family_id;
        $data["institute_id"] = $institute_id;
        $data["family_group_id"] = $family_group_id;
        $data["emergency_contact_name"] = $emergency_contact_name;
        $data["emergency_contact_relationship"] = $emergency_contact_relationship;
        $data["emergency_contact_phone"] = $emergency_contact_phone;
        
        $family_institute_id = self::create_update_family_institute($data);

        $response["success"] = true;
        $response["family_institute_id"] = $family_institute_id;
        $response["data"] = $data;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function send_institute_parent_welcome_mail($user_id, $group_id,$userdata = array()){
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

        $email_body_args = [];
        $email_body_args["email_type"] = "institute_parent";
        $email_body_args["school_id"] = $group_id;

        $custom_email_subject = get_field("institute_family_welcome_email_subject","option");
        $institute_family_subject = get_post_meta( $group_id, "school_onboarding_institute_family_welcome_email_subject",true );
        if(!empty($institute_family_subject)) $custom_email_subject = $institute_family_subject;
        
        $custom_email_body = SafarSchool::get_welcome_email_body($email_body_args); //


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

    static function delete_institute_family($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $id = $request->get_param("id");
        $table_name = parent::wpdb()->prefix . 'families';
        $rs = parent::wpdb()->get_results( parent::wpdb()->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );

        $family_group_id = 0;
        if(!empty($rs)){
            $family_group_id = $rs[0]->family_group_id;
            wp_delete_post($family_group_id, true); 
        }

        $success = parent::wpdb()->delete($table_name, array('id' => $id ));
        $response["id"] = $id;
        $response["success"] = $success;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function add_child_institute_family($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $id = $request->get_param("id");
        $table_name = parent::wpdb()->prefix . 'families';
        $rs = parent::wpdb()->get_results( parent::wpdb()->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
        $family = $rs[0];
        $family_group_id = $family->family_group_id;
        $students = $request->get_param("students");

        $children = learndash_get_groups_user_ids($family_group_id);
        foreach($students as $student_id){
            array_push($children, $student_id);
        }
        learndash_set_groups_users($family_group_id, $children );     
        
        $response["id"] = $id;
        $response["family_group_id"] = $family_group_id;
        $response["success"] = $success;
        $response["children"] = $children;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function update_institute_family($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $id = $request->get_param("id");
        $table_name = parent::wpdb()->prefix . 'families';
        $rs = parent::wpdb()->get_results( parent::wpdb()->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
        $family = $rs[0];
        $family_group_id = $family->family_group_id;
        $parents = learndash_get_groups_administrator_ids($family_group_id);

        $institute_id = $request->get_param("institute_id");
        $family_name = $request->get_param("family_name");
        $family_id = $request->get_param("family_id");

        $parent_name_1 = $request->get_param("parent_name_1");
        $parent_relationship_1 = $request->get_param("parent_relationship_1");
        $parent_phone_1 = $request->get_param("parent_phone_1");
        $parent_email_1 = $request->get_param("parent_email_1");
        $parent_id_1 = $request->get_param("parent_id_1");

        $parent_name_2 = $request->get_param("parent_name_2");
        $parent_relationship_2 = $request->get_param("parent_relationship_2");
        $parent_phone_2 = $request->get_param("parent_phone_2");
        $parent_email_2 = $request->get_param("parent_email_2");
        $parent_id_2 = $request->get_param("parent_id_2");

        $emergency_contact_name = $request->get_param("emergency_contact_name");
        $emergency_contact_relationship = $request->get_param("emergency_contact_relationship");
        $emergency_contact_phone = $request->get_param("emergency_contact_phone");

        $students = $request->get_param("students");

        $parent_one_user_id = $parents[0];
        $data = [];
        $data["email"] = $parent_email_1;
        $data["first_name"] = $parent_name_1;
        $data["phone"] = $parent_phone_1;
        $data["relationship"] = $parent_relationship_1;
        $data["role"] = "group_leader";
        $data["is_parent"] = true;
        $data["institute_id"] = $institute_id;
        if(empty($parent_id_1)){
            $parent_1_user_id = SafarUser::create_user_account($data);
        }else{
            $data["user_id"] = $parent_id_1;
            $parent_1_user_id = SafarUser::update_user_account($data);
        }

        $data = [];
        $data["email"] = $parent_email_2;
        $data["first_name"] = $parent_name_2;
        $data["phone"] = $parent_phone_2;
        $data["relationship"] = $parent_relationship_2;
        $data["role"] = "group_leader";
        $data["is_parent"] = true;
        $data["institute_id"] = $institute_id;

        if(empty($parent_id_2)){
            if(!empty($data["email"])) $parent_2_user_id = SafarUser::create_user_account($data);
        }else{
            $data["user_id"] = $parent_id_2;
            $parent_2_user_id = SafarUser::update_user_account($data);
        }
        
        // add both parents to the family group
        #$admin_result = learndash_set_groups_administrators($family_group_id, [$parent_1_user_id, $parent_2_user_id]);
        // add students to family group
        learndash_set_groups_users($family_group_id, $students );        
        $data = [];
        $data["family_id"] = $family_id;
        //$data["family_institute_id"] = $id;
        $data["institute_id"] = $institute_id;
        $data["family_group_id"] = $family_group_id;
        $data["emergency_contact_name"] = $emergency_contact_name;
        $data["emergency_contact_relationship"] = $emergency_contact_relationship;
        $data["emergency_contact_phone"] = $emergency_contact_phone;
        $data["id"] = $id;
        
        $family_institute_id = self::create_update_family_institute($data);

        $updated_post_data = array(
            'ID' => $family_group_id,
            'post_title' => $family_name,
        );
        
        // Update the family group
        wp_update_post($updated_post_data);

        $response["id"] = $id;
        $response["family"] = $family;
        $response["success"] = $success;
        $response["parents"] = $parents;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function create_update_family_institute($data){
        //family_name
        $family_institute_id = 0;
        if(empty($data["id"])){
            $table_name = parent::wpdb()->prefix . 'families'; // Replace 'your_table_name' with the actual table name

            $row_data = array(
                'family_id' => $data["family_id"],
                'institute_id' => $data["institute_id"],
                'family_group_id' => $data["family_group_id"],
                'emergency_contact_name' => $data["emergency_contact_name"],
                'emergency_contact_relationship' =>$data["emergency_contact_relationship"],
                'emergency_contact_phone' => $data["emergency_contact_phone"],
            );

            
            parent::wpdb()->insert($table_name, $row_data);
            $family_institute_id = parent::wpdb()->insert_id;
            #print_r([parent::wpdb()->last_error,$table_name,$row_data]);
        }else{
            $table_name = parent::wpdb()->prefix . 'families'; // Replace with your table name

            $updated_data = array(
                'family_id' => $data["family_id"],
                'institute_id' => $data["institute_id"],
                'family_group_id' => $data["family_group_id"],
                'emergency_contact_name' => $data["emergency_contact_name"],
                'emergency_contact_relationship' => $data["emergency_contact_relationship"],
                'emergency_contact_phone' => $data["emergency_contact_phone"],
            );

            $where_condition = array('id' => $data["id"]);

            parent::wpdb()->update($table_name, $updated_data, $where_condition);

            $family_institute_id = $data["id"];
        }

        return $family_institute_id;
    }

    static function import_institute_family_csv($request){
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

        $families = [];
        $success_upload = true;
        $errors = [];
        if(is_file($targetFile)){
            $handle = fopen($targetFile, "r");

            $teachers_rows = [];
            $error = false;
            $error_message = [];
            $row = 0;

            $fields = [
                "Family Name", // no numbers
                "Family ID", // if family id blank, then system should generate one.
                "Parent 1 Name", // no numbers
                "Parent 1 Relationship",  // Mother | Father
                "Parent 1 Phone", // number only
                "Parent 1 Email", // have an @ symbol
                "Parent 2 Name", // no numbers
                "Parent 2 Relationship", // Mother | Father
                "Parent 2 Phone",  // number only
                "Parent 2 Email", // have an @ symbol
                "Emergency Contact Name",  // no numbers
                "Emergency Contact Relationship", // Aunty Uncle Family Friend Grandmother Grandfather Relative Sibling Other
                "Emergency Contact Phone",  // number only
                "Student 1",
                "Student 2",
                "Student 3",
                "Student 4",
                "Student 5"
            ];  

            $mandatory_fields = ["Family Name",
                                    "Parent 1 Name",
                                    "Parent 1 Relationship",
                                    "Parent 1 Phone",
                                    "Parent 1 Email",
                                    "Emergency Contact Name",
                                    "Emergency Contact Relationship",
                                    "Emergency Contact Phone"];


            $school_details = \Safar\SafarSchool::get_school_details([]);
            $all_student_ids = [];
            if(!empty($school_details->data["students"])){
                foreach($school_details->data["students"] as $ea_student){
                    $all_student_ids[] = $ea_student->data->ID;
                }
            }

            $institute_name = $school_details->data["parent_school"]["post"]->post_title;
            $csv_i = 0;
            $total = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($csv_i > 0 ){

                    $error_family = false;

                    $family_name = $data[0]; 
                    $family_id	= $data[1]; 
                    if(empty($family_id)) $family_id = mt_rand();

                    $parent_1_name	= $data[2]; 
                    $parent_1_relationship	= $data[3];
                    $parent_1_phone	= $data[4];
                    $parent_1_email	= $data[5];
                    
                    $parent_2_name	= $data[6]; 
                    $parent_2_relationship	= $data[7]; 
                    $parent_2_phone	= $data[8];
                    $parent_2_email	= $data[9]; 
                    
                    $emergency_contact_name = $data[10];
                    $emergency_contact_relationship	= $data[11]; 
                    $emergency_contact_phone = $data[12];

                    $student_1 = $data[13];
                    $student_2 = $data[14];
                    $student_3 = $data[15];
                    $student_4 = $data[16];
                    $student_5 = $data[17];	
                    
                    foreach($fields as $key=>$field){

                        if(in_array($field, $mandatory_fields)){
                            if(empty($data[$key])){
                                $success_upload = false;
                                $errors[] = $field;
                                $error_family = true;
                            }
                        }

                        // have an @ symbol valid email
                        if(in_array($field, ["Parent 1 Email", "Parent 2 Email"])){
                            if (!filter_var($data[$key], FILTER_VALIDATE_EMAIL) ) {
                                if(!empty($data[$key])){
                                    $errors[] = $data[$key]." invalid " .strtolower($field).".";
                                    $error_family = true;
                                }
                            } 
                        }

                        // no numbers
                        if(in_array($field, ["Family Name", "Parent 1 Name","Parent 2 Name","Emergency Contact Name"])){
                            if (preg_match('/\d/', $data[$key])) {
                                $errors[] = $data[$key]." invalid " .strtolower($field).", it should not contain number.";
                                $error_family = true;
                            }
                        }

                        // number only
                        if(in_array($field, ["Parent 1 Phone", "Parent 1 Phone","Emergency Contact Phone"])){
                            if (!preg_match('/^\d+$/', $data[$key])) {
                                $errors[] = $field." should be numbers only.";
                                $error_family = true;
                            }
                            
                        }

                        // // Mother | Father
                        if(in_array($field, ["Parent 1 Relationship","Parent 2 Relationship"])){
                            if (!in_array(strtolower($data[$key]),["mother","father"])) {
                                if(!empty($data[$key])){
                                    $errors[] = $field." should only be mother or father.";
                                    $error_family = true;
                                }
                            }
                        }

                        /// check if students belong to the institute, if not throw error that this student can't be added because 
                        // belongs to another institute
                        if(in_array($field,["Student 1",
                                            "Student 2",
                                            "Student 3",
                                            "Student 4",
                                            "Student 5"])){
                            if(!empty($data[$key])){
                                $student_user = get_user_by('login', $data[$key]);
                                if(!empty($student_user->ID)){
                                    if(!in_array($student_user->ID, $all_student_ids)){
                                        $errors[] = $data[$key]." student does not belong to ".$institute_name;
                                        $error_family = true;
                                    }
                                }else{
                                    
                                    $errors[] = $data[$key]." student username does not exists";
                                    $error_family = true;
                                }
                            }
                        }
                    }

                    if(!$error_family){
                        $families[] = [
                            "family_name" => $family_name, 
                            "family_id" => $family_id,

                            "parent_name_1" => $parent_1_name,
                            "parent_relationship_1" => $parent_1_relationship,
                            "parent_phone_1" => $parent_1_phone,
                            "parent_email_1" => $parent_1_email,

                            "parent_name_2" => $parent_2_name,
                            "parent_relationship_2" => $parent_2_relationship,
                            "parent_phone_2" => $parent_2_phone,
                            "parent_email_2" => $parent_2_email,

                            "emergency_contact_name" => $emergency_contact_name,
                            "emergency_contact_relationship" => $emergency_contact_relationship,
                            "emergency_contact_phone" => $emergency_contact_phone,

                            "student_1" => $student_1,
                            "student_2" => $student_2,
                            "student_3" => $student_3,
                            "student_4" => $student_4,
                            "student_5" => $student_5,
                            
                        ];
                    }
                    $total++;
                }
                $csv_i++;
            }
        }

        if(!empty($errors)) $success_upload = false;

        $response["success"] = $success_upload;
        $response["errors"] = $errors;
        $response["families"] = $families;
        $response["total"] = $total;

        $response = new \WP_REST_Response($response);
        return $response;

    } // import_institute_family

    static function import_institute_family($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $families = $request->get_param("families");
        $institute_id = $request->get_param("institute_id");

        //create_institute_family
        $fields = [ "family_name", "family_id", 
                    "parent_name_1", 
                    "parent_relationship_1", 
                    "parent_phone_1", 
                    "parent_email_1", 
                    
                    "parent_name_2",
                    "parent_relationship_2",
                    "parent_phone_2",
                    "parent_email_2",

                    "emergency_contact_name",
                    "emergency_contact_relationship",
                    "emergency_contact_phone",
                
                    "student_1",
                    "student_2",
                    "student_3",
                    "student_4",
                    "student_5",
                ];
        $successful_imports = [];
        foreach($families as $family){
            
            $params = [];
            $params["institute_id"] = $institute_id;
            foreach($fields as $field){
                $params[$field] = $family[$field];
            }
            
            $request   = new \WP_REST_Request( 'POST', );
            $request->set_query_params($params);
            $response = \Safar\SafarFamily::create_institute_family($request);

            if($response->data->success){
                $successful_imports[] = $family;
            }
        }
        $response = [];

        $response["families"] = $families;
        $response["successful_imports"] = $successful_imports;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function get_institute_by_parent_id($parent_id){
        $admin_groups = learndash_get_administrators_group_ids( $parent_id ); // if user is a teacher or admin of the group
        $institute = [];
		foreach($admin_groups as $gid){
            $table_name = parent::wpdb()->prefix . 'families';
            #echo parent::wpdb()->prepare("SELECT * FROM $table_name WHERE family_group_id = %d", $gid);
            $rs = parent::wpdb()->get_results( parent::wpdb()->prepare("SELECT * FROM $table_name WHERE family_group_id = %d", $gid) );
            foreach($rs as $family){
                $institute_id = $family->institute_id;
                $institute = \Safar\SafarSchool::get_school_data($institute_id);
            }
        }
        return $institute;
    }

    static function delete_institute_family_child($request){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $classroom_id = $request->get_param("id");
        $childid = $request->get_param("childid");

        $students = learndash_get_groups_user_ids( $classroom_id, true );
        // Find the index of $childid in the $students array
        $index = array_search($childid, $students);
        if ($index !== false) {
            unset($students[$index]);
        }

        learndash_set_groups_users($classroom_id, $students );

        $response["children"] = $students;
        $response = new \WP_REST_Response($response);
        return $response;
                
    }
}