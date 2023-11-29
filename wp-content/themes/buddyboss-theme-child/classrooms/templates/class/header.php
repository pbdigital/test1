<?php namespace ld_classroom; ?>
<?php 
    $ldc_header_background_color =  get_site_option('ldc_header_background_color', '#EAEAEA');
    $ldc_header_border_color =  get_site_option('ldc_header_border_color', '#231F20');
    $ldc_header_custom_background_color =  get_site_option('ldc_header_custom_background_color', '#ffffff');
    $ldc_header_custom_border_color =  get_site_option('ldc_header_custom_border_color', '#ffffff');
    $ldc_change_the_used_seats_icon_color =  get_site_option('ldc_change_the_used_seats_icon_color', '#000');
    $ldc_change_seats_remaining_icon_color =  get_site_option('ldc_change_seats_remaining_icon_color', '#000');
    $ldc_header_background_color =  get_site_option('ldc_header_background_color', '#EAEAEA');
    $ldc_header_border_color =  get_site_option('ldc_header_border_color', '#231F20');
?>
<style>
	.classroom_header { background: <?php echo $ldc_header_background_color; ?>; border-color:<?php echo $ldc_header_border_color; ?>; }
    .ldc-user-licenses::before { color: #F2A952;background: #FDF2E5;border-radius: 12px; font-size: 28px;padding: 12px;} 
	.ldc-user-licenses-remaining::before { color: #F2A952;background: #FDF2E5;border-radius: 12px;font-size: 28px;padding: 12px; }
	.classroom_header { background: <?php echo $ldc_header_custom_background_color; ?>; border-color:<?php echo $ldc_header_custom_border_color; ?>;border-radius: 12px;padding-bottom: 2em;padding-top: 2em;}
</style>
<div class="classroom_header" data-primary-group-id="<?php echo Group::$parent_group_id; ?>">
    
    <div class="classroom_licenses">
        <?php 
            $ldc_change_the_used_seats_attachment_id =  get_site_option('ldc_change_the_used_seats_attachment_id'); 
             
            if ( isset($ldc_change_the_used_seats_attachment_id) && $used_seats_attachment_id = wp_get_attachment_image_src( $ldc_change_the_used_seats_attachment_id ) ){
                echo '<img src="' . $used_seats_attachment_id[0] . '"  class="custom_header_icon_image" />';
            }
            else{
        ?>
        <span class="ldc-user-licenses"></span>
        <?php } ?>

        
        <span class="classroom-license-text">
            <?php 
            $ldc_hide_seats_used_text =  get_site_option('ldc_hide_seats_used_text', 'no');
            if ( $ldc_hide_seats_used_text === "no") {
                esc_html_e('Total Seats Used:','lt-learndash-classroom');
            }
            ?>
            
            <?php echo $license_info['licenses_used']; ?> 
        </span>
        
    </div>
    

    
    <div class="classroom_remaining_licenses">
        <?php 
            $ldc_change_seats_remaining_attachment_id =  get_site_option('ldc_change_seats_remaining_attachment_id'); 
             
            if ( isset($ldc_change_seats_remaining_attachment_id) && $seats_remaining_attachment_id = wp_get_attachment_image_src( $ldc_change_seats_remaining_attachment_id ) ){
                echo '<img src="' . $seats_remaining_attachment_id[0] . '"  class="custom_header_icon_image" />';
            }
            else{
        ?>
        <span class="ldc-user-licenses-remaining"></span>
        <?php } ?>

        
        <span class="classroom-license-text">
            <?php 
            $ldc_hide_seats_ramaining_text =  get_site_option('ldc_hide_seats_ramaining_text', 'no');
            if ( $ldc_hide_seats_ramaining_text === "no") {
                esc_html_e('Seats Remaining:','lt-learndash-classroom'); 
            } ?>
            <?php echo $license_info['licenses_remaining']; ?>
        </span>
        
    </div>
    

    <?php 
    if( get_query_var( 'group_id' ) == false && !isset($_GET['group']) &&  Group::is_admin_or_primary_group_leader(Group::$parent_group_id)){
        
        $encrypt_group_id = general_encrypt_decrypt('encrypt', 0);
        $classrooms = array();
        foreach($groups as $group){
            $user_query_args = array(
                'orderby' 	=>	'display_name',
                'order'	 	=>	'ASC',
                'meta_query' => array(
                    array(
                        'key'     	=> 	'learndash_group_users_'. intval( $group->ID ),
                        'compare' 	=> 	'EXISTS',
                    )
                )
            );
            $user_query = new \WP_User_Query( $user_query_args );            

            $classrooms[] = array("id" => general_encrypt_decrypt('encrypt', $group->ID) , "title" => $group->post_title, 'total_students' => $user_query->total_users );
         }
        ?>

    <?php 
        $ldc_hide_manage_classroom_button =  get_site_option('ldc_hide_manage_classroom_button', 'no'); 
         if ( $ldc_hide_manage_classroom_button === 'no' ) {
    ?>     
    <div class="classroom_manage">
        <a href="#" class="btn_classroom init_classroom_group" data-group="<?php echo $encrypt_group_id; ?>"
            data-group-name=""
            data-groups="<?php esc_attr_e(json_encode($classrooms)); ?>"><?php esc_html_e('MANAGE CLASSROOM','lt-learndash-classroom'); ?>
            <i class="fa fa-angle-right"></i></a>
    </div>
    <?php } ?>

    <?php 
        $ldc_hide_school_email_button =  get_site_option('ldc_hide_school_email_button', 'no'); 
         if ( $ldc_hide_school_email_button === 'no' ) {
    ?>
    <div class="classroom_email">
        <a href="#" class="btn_classroom init_classroom_teacher_email" data-group="<?php echo $encrypt_group_id; ?>"
            data-group-name="" data-group-courses=""><?php esc_html_e('Email','lt-learndash-classroom'); ?> <i
                class="fa fa-angle-right"></i></a>
    </div>
    <?php } ?>
    
    <?php } else if( get_query_var( 'group_id' , false) != '' || isset($_GET['group']) ) {
        ?>
    <div class="classroom_back_to_classrooms" style="float: right;">
        <a href="<?php echo ( get_query_var( 'group_id' , false) != '' ? home_url('ld-classroom'): esc_url(the_permalink())  ). '?parent-group-id=' . Group::$parent_group_id; ?>"
            class="btn_classroom btn_classroom_black btn_back_to_classroom"><i
                class="fa fa-angle-left"></i><?php esc_html_e('BACK TO CLASSROOMS','lt-learndash-classroom'); ?></a>
    </div>
    <?php } ?>
</div>