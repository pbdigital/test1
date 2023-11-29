<?php
namespace ld_classroom;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wp;

if( !isset($shortcode_name) ){
	get_header(); 
}
$all_primary_groups = Group::get_all_primary_groups();
$groups_id = learndash_get_administrators_group_ids(get_current_user_id());

$groups = array();

$groups = Group::get_child_groups();
$page_layout = 'class_admin';

if(!empty($_GET['group'])){
	$page_layout = 'view_class';
}
$license_info = get_licenses_info_user();

$ldc_change_the_used_seats_icon_color =  get_site_option('ldc_change_the_used_seats_icon_color', '#000');
$ldc_change_seats_remaining_icon_color =  get_site_option('ldc_change_seats_remaining_icon_color', '#000');
$ldc_header_background_color =  get_site_option('ldc_header_background_color', '#EAEAEA');
$ldc_header_border_color =  get_site_option('ldc_header_border_color', '#231F20');
?>
<style>
	.ldc-user-licenses::before { color: <?php echo $ldc_change_the_used_seats_icon_color; ?>; }
	.ldc-user-licenses-remaining::before { color: <?php echo $ldc_change_seats_remaining_icon_color; ?>; }
	.classroom_header { background: <?php echo $ldc_header_background_color; ?>; border-color:<?php echo $ldc_header_border_color; ?>;}
</style>
<div class="classroom_container">
    <div class="row-group">
        <?php 
		if(!empty($all_primary_groups) || !empty($groups)  ) :  
			include_once SharedFunctions::get_template('/templates/class/admin-header.php');

			$ldc_hide_classrooms_header =  get_site_option('ldc_hide_classrooms_header', 'no');
			if ( $ldc_hide_classrooms_header === 'no') {
				include_once SharedFunctions::get_template('/templates/class/header.php');
				// include LT_LD_CLASSROOM_DIR . '/templates/class/header.php';
			}
        	
			include_once SharedFunctions::get_template('/templates/class/listing.php');
        	//include LT_LD_CLASSROOM_DIR . '/templates/class/listing.php';

        	//<!-- Classroom report -->
        	include_once SharedFunctions::get_template('/templates/class/classrooms-report.php');

		elseif( current_user_can('group_leader') && empty($groups) ):
			_e("You are not a part of any Groups yet.", 'lt-learndash-classroom');
		else:
			_e("Please add your first Learndash Group to get started.", 'lt-learndash-classroom');
        endif; 
		?>
    </div>
    <?php  include_once SharedFunctions::get_template('/templates/class/modal.php');  ?>
</div>
<?php 
if( !isset($shortcode_name) ){
	get_footer(); 
} ?>