<?php 
/** Template Name: Manage Classrooms */
// hot fix if user is a parent, automatically select the first group
if(isset($_GET["clearselectedins"])){
    delete_user_meta(get_current_user_id(), "selected_institute");
    wp_redirect("/manage-classroom/");
}

$is_parent = \Safar\SafarFamily::is_user_parent( );

if($is_parent){
    $family_groups = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );
    $auto_select_family_group_id = 0;
    $selected_family_group = get_user_meta( get_current_user_id() ,"selected_institute", true);
    if(empty($selected_family_group)){
        foreach($family_groups as $egroup){
            if(!empty(count($egroup->school_data["students"]))){
                update_user_meta( get_current_user_id() ,"selected_institute", $egroup->ID);
                wp_redirect(site_url("manage-family")); // reload the page
            }
        }
    }
}

// login hook where it checks if the logged in user is a parent
// check for any active subscriptions
// and update the parent associated groups

$institutes = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );
foreach($institutes as $ins){
    $order_id = get_post_meta($ins->ID, "order_id", true);
    $subscription_status = get_post_meta($ins->ID,"subscription_status", true);
    if(!empty($order_id)){
        $subscriptions = \Safar\SafarPublications::api_request(["endpoint"=>"/family/order/".$order_id."/subscriptions?".mt_rand()]);
        $has_active_subscription = false;
        
        foreach($subscriptions as $subscription){
            if($subscription->status == "active" || $subscription->status == "pending-cancel"){
                $has_active_subscription = true;

                if($subscription->is_trial_period == "day"){
                    update_post_meta($ins->ID, "is_trial_subscription", "yes");
                }else{
                    delete_post_meta($ins->ID, "is_trial_subscription");
                }
                
                if(!empty($subscription->trial_start_date)){
                    update_post_meta($ins->ID,"trial_start_date", date("m/d/Y",strtotime($subscription->trial_start_date) ) );
                }else{
                    delete_post_meta($ins->ID,"trial_start_date");
                }

                if(!empty($subscription->trial_end_date)){
                    update_post_meta($ins->ID,"trial_end_date", date("m/d/Y",strtotime($subscription->trial_end_date) ) );
                }else{
                    delete_post_meta($ins->ID,"trial_end_date");
                }

                if(!empty($subscription->schedule_next_payment)){
                    update_post_meta($ins->ID,"next_payment_date", date("m/d/Y",strtotime($subscription->schedule_next_payment) ) );
                }else{
                    delete_post_meta($ins->ID,"next_payment_date");
                }

                if(!empty($subscription->payment_method)){
                    update_post_meta($ins->ID, "has_added_payment_method", "yes");
                }else{
                    delete_post_meta($ins->ID, "has_added_payment_method");
                }
            }

        }

        if($has_active_subscription){
            update_post_meta($ins->ID, "subscription_status", "active");
        }else{
            update_post_meta($ins->ID, "subscription_status", "cancelled");
        }
    }
}


$is_user_institute_admin = \Safar\SafarUser::is_user_institute_admin();
if(!empty($is_user_institute_admin)){
    if(!empty($institutes)){
        $user_id = get_current_user_id();
        $completed_school_onboarding = get_user_meta($user_id, "completed_user_onboarding", true);
        //https://my.journey2jannah.com/school-onboarding/?gid=232164
        $sub_role = get_field("user_role","user_".$user_id);
        if($sub_role == "school admin"){
            if(empty($completed_school_onboarding)){

                $completed_onboarding = get_post_meta($institutes[0]->ID, "completed_onboarding", true);
                $redirect = true;

                if(is_array($completed_onboarding)){
                    if($completed_onboarding[0] == "yes") $redirect = false;
                }

                $contact_email_address = get_post_meta($institutes[0]->ID, "school_onboarding_contact_email_address",true);
                if(!empty($contact_email_address)){
                    $redirect = false;
                }

                if($redirect){
                    wp_redirect("school-onboarding/?gid=".$institutes[0]->ID);
                    exit();
                }
            }
        }
    }
}

add_action("buddyboss_theme_after_header", function(){
    require("manage-classrooms/institutes/top-bar.php");
});

global $post;
$post_slug = $post->post_name;
$user = wp_get_current_user();


add_filter( 'body_class', function($classes){
    global $post_slug;
    // Add your custom class(es) here
    $classes[] = 'page-template-'.$post_slug;
    return $classes;
} );

get_header();

$school = \Safar\SafarSchool::get_user_school_id();
$school_details = [];
$child_schools = [];

if(!empty($school)){
    $learndash_parent_group_id = $school["learndash_parent_group_id"];
    $school_details = \Safar\SafarSchool::get_school_data($learndash_parent_group_id);
    $child_schools = \Safar\SafarSchool::get_classrooms($learndash_parent_group_id);
}

if($post_slug == "family-parent-dashboard"){
    $is_institute_family = \Safar\SafarUser::is_user_institute_parent();
    if($is_institute_family){
        $user_info = \Safar\SafarUser::get_user_info([]);
        if(empty($user_info->data->gender)){
            wp_redirect("admin-onboarding");
        }
    }
}
?>
<main id="main" class="site-main <?=$post_slug?>">
    <?php 
    if(!empty($school)){

        switch($post_slug){

            case "manage-classroom-students": do_action("manage-classroom-students", ["school_details"=>$school_details ]); break;
            case "manage-family": do_action("manage-classroom-students", ["school_details"=>$school_details ]); break;
            case "manage-classroom-teachers": do_action("manage-classroom-teachers", ["school_details"=>$school_details ]); break;
            case "manage-classroom-admins": do_action("manage-classroom-admins", ["school_details"=>$school_details, "child_schools"=>$child_schools ]); break;
            case "manage-classroom-institutes": do_action("manage-classroom-institutes", ["school_details"=>$school_details, "child_schools"=>$child_schools ]); break;
            case "manage-institute-families": do_action("manage-institute-families", ["school_details"=>$school_details, "child_schools"=>$child_schools ]); break;
            case "family-parent-dashboard"; do_action("family-parent-dashboard", ["school_details"=>$school_details, "child_schools"=>$child_schools ]); break;

            default: do_action("manage-classroom-classrooms", ["school_details"=>$school_details, "child_schools"=>$child_schools]); break; 
        }   

    }else{
        ?><div class="no-records" style="padding:30px; text-align:center; background: #FFFFFF;
        border-radius: 12px; margin-top:30px;"><h2 style="margin:0px">Select Institute</h2></div><?php
    }
    ?>
    
    <div id="manage-classroom-modal" class="modal manage-classroom-modal" >

        <!-- Modal content -->
        <div class="modal-content">            
            <span class="close" style="position: absolute;
            top: -20px;
            right: -20px;
            opacity: 1;">
                <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="25" cy="25" r="25" fill="#98C03D"/>
                    <rect width="24" height="24" transform="translate(13 13)" fill="#98C03D"/>
                    <path d="M31 19L19 31" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M19 19L31 31" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div class="main-content">
                    
            </div>
        </div>

    </div>

    <?php do_action("manage-classroom-add-new", ["school_details"=>$school_details, "child_schools"=>$child_schools]);?>

</main>


<?php 
add_action("wp_footer", function(){
    
}, 999);

get_footer();

?>