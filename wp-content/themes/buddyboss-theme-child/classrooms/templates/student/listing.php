<table class="groups_user_table classroom_table student_list_table"
    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-student-list-table' );?>">
    <thead>
        <tr>
            <th><?php esc_html_e('Student Name','lt-learndash-classroom'); ?></th>
            <th><?php esc_html_e('Username','lt-learndash-classroom'); ?></th>
            <th><span class="email_icon"></span></th>
            <th><?php esc_html_e('Edit Student Information','lt-learndash-classroom'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if( empty ($user_group_users) ){
            ?>
        <tr>
            <td colspan="4">
                <?php _e('No record found.','lt-learndash-classroom'); ?>
            </td>
        </tr>
        <?php
        }
        
        foreach($user_group_users as $user){ 
			$user_key = user_encrypt_decrypt('encrypt', $user->ID);
			?>
        <tr class="<?php esc_attr_e($user->style_classes) ?>">
            <td><?php echo $user->first_name.' '.$user->last_name; ?></td>
            <td align="center"><span class="classroom_info"><?php echo $user->user_login; ?></span></td>
            <td><?php if (!empty($user->user_email)) {?><a href="mailto:<?php esc_attr_e($user->user_email); ?>"><span
                        class="email_icon"></span></a><?php }; ?></td>
            <td align="center" class="ldc-edit-student">
                <?php do_action('ldc_before_student_action_button'); ?>
                <?php 
                    $ldc_hide_classroom_edit_student_button =  get_site_option('ldc_hide_classroom_edit_student_button', 'no'); 
                    if ( $ldc_hide_classroom_edit_student_button === 'no' ) {
                ?>             
                <a href="#" data-user="<?php echo $user_key; ?>" data-firstname="<?php echo $user->first_name; ?>"
                    data-lastname="<?php echo $user->last_name; ?>" data-username="<?php echo $user->user_login; ?>"
                    data-email="<?php echo $user->user_email; ?>"
                    class="btn_classroom btn_classroom_black edit_classroom_student">
                    <?php esc_html_e('Edit','lt-learndash-classroom'); ?>
                    <i class="fa fa-angle-right"></i></a>
                <?php } ?>    
                <a href="#" data-user="<?php echo $user_key; ?>" class="btn_classroom classroom-change-password">
                    <?php esc_html_e('Change Password','lt-learndash-classroom'); ?> <i
                        class="fa fa-angle-right"></i></a>
                <?php do_action('ldc_after_student_action_button'); ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
    <?php 
        $ldc_hide_export_csv_button =  get_site_option('ldc_hide_export_csv_button', 'no'); 

        if ( $ldc_hide_export_csv_button === 'no') {
    ?>
    <tfoot>
        <tr>
            <td>
                <a href="" class="btn_classroom"
                    id="btn_download_student_report_csv"><?php esc_html_e('Export CSV','lt-learndash-classroom'); ?>
                    <i class="fa fa-angle-right"></i></a>
            </td>
        </tr>
    </tfoot>
    <?php } ?>
</table>