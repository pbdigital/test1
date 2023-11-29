<?php 
add_action('wp_enqueue_scripts', function(){

    if( get_page_template_slug() == "page-templates/manage-classrooms.php"){
        wp_enqueue_style('manage-classrooms-css', get_stylesheet_directory_uri() . '/assets/css/manage-classrooms.css', '', ENQUEUE_VERSION);
        wp_enqueue_script('manage-classrooms-js', get_stylesheet_directory_uri() . '/assets/js/manage-classrooms.js', '', ENQUEUE_VERSION, true);
        wp_enqueue_script('dropzone-js', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js', '', ENQUEUE_VERSION );
        wp_enqueue_style('dropzone-css','https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/basic.css', '', ENQUEUE_VERSION );
        wp_enqueue_script('datatable-js', 'https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js', '', ENQUEUE_VERSION );
        wp_enqueue_script('pikaday-js','//cdn.jsdelivr.net/npm/pikaday/pikaday.js', '', ENQUEUE_VERSION,true  );
        wp_enqueue_style('pikaday-css','https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css', '', ENQUEUE_VERSION );

        global $post;
        $post_slug = $post->post_name;

        

        $terms = get_terms( [
            'taxonomy' => 'ld_group_category',
        ] );

        foreach($terms as $eterm){

            $courses = get_posts([
                'post_type' => 'groups',
                "orderby" => "post_title", 
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

            if(!empty($courses)){
                $available_courses[] = [   
                    "category_id" => $eterm->term_id,
                    "category" => $eterm->name,
                    "courses" => $courses 
                ];
            }
        }
       
        $args = [
            "post_slug" => $post_slug,
            "subjects" => get_terms( array(
                'taxonomy' => 'ld_group_category',
                'hide_empty' => false,
            ) ),
            "availableGroupCourses" => $available_courses,
            "selectedInstituteId" => get_user_meta( get_current_user_id(),"selected_institute", true),
            "instituteProdUrl" => "/?action=safarpublications_sso_login"
        ];
        wp_localize_script('manage-classrooms-js', 'manageClassroomsJs', $args);
    }
}, 999);


add_action("wp_ajax_manage_classrooms_grid", function(){

    $user = wp_get_current_user();
    $school = \Safar\SafarSchool::get_user_school_id($user->data->ID);
    $school_details = [];
    $child_schools = [];
    if(!empty($school)){
        $learndash_parent_group_id = $school["learndash_parent_group_id"];
        $parent_school_meta = get_post_meta($learndash_parent_group_id);
        $school_details = \Safar\SafarSchool::get_school_data($learndash_parent_group_id);

        //$school = \Safar\Safar::debug(["school_details"=>$school_details["post"]->post_title]);
        $child_schools = \Safar\SafarSchool::get_classrooms($learndash_parent_group_id);
        //\Safar\Safar::debug($child_schools);
    }

    $is_school_admin = false;
    if(!empty($school_details["teachers"])){
        $school_admin_user_ids = [];
        foreach($school_details["teachers"] as $admin){
            $school_admin_user_ids[] = $admin->data->ID;
            if(get_current_user_id() == $admin->data->ID){
                $is_school_admin = true;
            }
        }
    }

    if(!empty($child_schools)){
        foreach($child_schools as $school){
            //\Safar\Safar::debug($school->school_data["courses"]);
            do_action("classroom_card", $school, $is_school_admin);
        }
    }
    
    do_action("classroom_new");
    
    wp_die();
});

add_action("classroom_statistics", function($data){
    extract($data);
    require("classroom/statistics.php");
});

add_action("classroom_top", function($school_details){
    require("classroom/top.php");
});


add_action("classroom_card", function($school,$is_school_admin=false){
    require("classroom/card.php");
},10,2);
add_action("classroom_new", function(){ // card
    require("classroom/new.php");
});

add_action("manage-classroom-add-new", function($args){
    require("classroom/add-new-modal.php");
});

add_action("create-new-class-name", function($args){
    require("classroom/create-new/class-new-name.php");
});
add_action("class-new-manage-courses", function($args){
    require("classroom/create-new/class-new-manage-courses.php");
});
add_action("class-new-avatar", function($args){
    require("classroom/create-new/class-new-avatar.php");
});
add_action("class-new-cover-photo", function($args){
    require("classroom/create-new/class-new-cover-photo.php");
});
add_action("class-new-teacher", function($args){
    require("classroom/create-new/class-new-teacher.php");
});
add_action("class-new-students", function($args){
    require("classroom/create-new/class-new-students.php");
});
add_action("class-student-list-template", function($args){
    require("classroom/create-new/class-student-list-template.php");
});

add_action("manage-classroom-classrooms", function($args){
    extract($args);
    require("classroom/main.php");
});

add_action("manage-classroom-teachers", function($args){
    extract($args);
    require("teachers/main.php");
});

add_action("manage-classroom-teachers-top", function($school_details){
    require("teachers/top.php");
});

add_action("manage-classroom-teachers-list", function($school_details){
    require("teachers/list.php");
});


add_action("manage-classroom-students", function($args){
    extract($args);
    require("students/main.php");
});

add_action("manage-classroom-students-top", function($school_details){
    require("students/top.php");
});

add_action("manage-classroom-students-list", function($school_details){
    require("students/list.php");
});

add_action("manage-classroom-institutes", function($args){
    extract($args);
    require("institutes/main.php");
});

add_action("institute_card", function($school){
    require("institutes/card.php");
});



// overrides default group avatar from what is set on /manage-classroom page
add_action("bp_get_group_avatar", function($x,$y){
    global $groups_template;
    $group_id = $groups_template->group->id;
    if(!empty($group_id)){
        $ld_group_id = \Safar\SafarSchool::get_learndash_group_id_from_bp_group_id($group_id);
        $class_avatar = get_post_meta($ld_group_id,"class_avatar",true);
        if(!empty($class_avatar)){
            $class_avatar = wp_get_attachment_url($class_avatar);

            $x = '
                <img src="'.$class_avatar.'" 
                    class="avatar group-183-avatar avatar-'.$y["width"].' photo" 
                    width="'.$y["width"].'" 
                    height="'.$y["height"].'" 
                    alt="'.$y["alt"].'">
            ';
            return $x;
        }
        
    }
    return $x;
},999,2);


add_action("manage-classroom-admins", function($args){
    extract($args);
    require("admins/main.php");
});

add_action("manage-classroom-admins-top", function($school_details){
    require("admins/top.php");
});

add_action("manage-classroom-admins-list", function($school_details){
    require("admins/list.php");
});


// families
add_action("manage-institute-families", function($args){
    extract($args);
    require("families/main.php");
});
add_action("manage-classroom-families-top", function($school_details){
    require("families/top.php");
});

add_action("manage-classroom-families-list", function($school_details){
    require("families/list.php");
});


// actions for family parent dashboard
add_action("family-parent-dashboard", function($args){
    extract($args);
    require("family-parent-dashboard/main.php");
});

add_action("wp_ajax_close_upgrade_seats", function(){
    $user = wp_get_current_user();
    $school = \Safar\SafarSchool::get_user_school_id($user->data->ID);
    $learndash_parent_group_id = $school["learndash_parent_group_id"];
    delete_post_meta($learndash_parent_group_id,"show_upgrade_notification");
});
?>