<?php
namespace ld_classroom;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !isset($shortcode_name) ){
	get_header(); 
}

?>
<div class="classroom_container">
    <?php 

    if(get_query_var( 'group_id' , false) != ''){
        $group_key = get_query_var( 'group_id' );	
    }
    else{
        $group_key = $_GET['group'];	
    }
    
    $group_id = general_encrypt_decrypt('decrypt', $group_key);
    
    // Check group_id is number and have access to group
    if( $group_id && is_numeric($group_id) && Group::has_group_access($group_id) ):
        $user_group_users = Group::get_group_users( $group_id );
        $group_info = get_post($group_id);
        $license_info = get_licenses_info_user($group_id);
    ?>

    <div class="row-group">
        <?php  
            $ldc_hide_classrooms_header =  get_site_option('ldc_hide_classrooms_header', 'no'); 
            if ( $ldc_hide_classrooms_header === 'no'){
                
                include_once SharedFunctions::get_template('/templates/class/header.php');
                do_action('ldc_before_classroom_header_box', $group_id); 

                include_once SharedFunctions::get_template('/templates/student/header.php');
                do_action('ldc_after_classroom_header_box', $group_id);
            }
        
            include_once SharedFunctions::get_template('/templates/student/listing.php');
            include_once SharedFunctions::get_template('/templates/student/filter.php');
            include_once SharedFunctions::get_template('/templates/student/login-history.php');
            include_once SharedFunctions::get_template('/templates/student/lesson-listing.php');
        ?>
    </div>

    <?php
    else:
        _e("You don't have access of this group.", 'lt-learndash-classroom');
    endif; 
    ?>
</div>

<!--- Modals --->
<?php 
if ( Group::has_group_access($group_id) ) :
    include_once SharedFunctions::get_template('/templates/student/add-seats.php');
    include_once SharedFunctions::get_template('/templates/student/add-student.php');
    include_once SharedFunctions::get_template('/templates/student/edit-student.php');
    include_once SharedFunctions::get_template('/templates/student/change-password.php');
    include_once SharedFunctions::get_template('/templates/student/import-student.php');
    include_once SharedFunctions::get_template('/templates/student/email-student.php');
endif;

if( !isset($shortcode_name) ){
    get_footer();
}