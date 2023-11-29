<form action="" method="POST" id="<?php _e(self::$form_id); ?>"
    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-edit-student' );?>">
    <div class="form_message"></div>
    <div class="form-group section">
        <?php $ldc_lock_teacher_and_student_names = get_site_option('ldc_lock_teacher_and_student_names'); ?>
        <div class="col grid_1_of_4">
            <input type="hidden" name="user" value="<?php _e($user_key);?>" />
            <input type="hidden" name="group" value="<?php _e($group_key);?>" />
            <input type="text" name="firstname" value="<?php _e($user_info->first_name ); ?>"
                placeholder="<?php esc_html_e('First Name *','lt-learndash-classroom'); ?>" 
                <?php echo $ldc_lock_teacher_and_student_names == 'yes' ? "readonly":""; ?> />
        </div>
        <div class="col grid_1_of_4">
            <input type="text" name="lastname" value="<?php _e($user_info->last_name ); ?>"
                placeholder="<?php esc_html_e('Last Name','lt-learndash-classroom'); ?>" 
                <?php echo $ldc_lock_teacher_and_student_names == 'yes' ? "readonly":""; ?>/>
        </div>
        <div class="col grid_1_of_4">
            <input type="text" name="username" value="<?php _e($user_info->user_login ); ?>"
                placeholder="<?php esc_html_e('Username *','lt-learndash-classroom'); ?>" />
        </div>
        <div class="col grid_1_of_4">
            <input type="email" name="email" value="<?php _e($user_info->user_email ); ?>"
                placeholder="<?php esc_html_e('Email','lt-learndash-classroom'); ?>" />
        </div>
    </div>
    <div class="form-action section text-right action-buttons">
        <div class="col grid_1_of_4 pull-left text-left">
            <button type="submit" class="btn_classroom"
                id="<?php _e(self::$form_id); ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom');?> <i
                    class="fa fa-angle-right"></i></button>
        </div>
        <div class="col grid_3_of_4 pull-right text-right">
            <?php do_action('ldc_before_edit_student_button', $user_id, $group_id); ?>
            
            <?php 
                $ldc_hide_classroom_edit_student_remove_icon_button =  get_site_option('ldc_hide_classroom_edit_student_remove_icon_button', 'no'); 
                if ( $ldc_hide_classroom_edit_student_remove_icon_button === 'no' ) {
            ?> 
            <button type="button" class="btn_classroom" title="Remove student from classroom"
                id="<?php _e(self::$form_id); ?>_btn_delete"><?php esc_html_e('Remove','lt-learndash-classroom');?>
                <i class="fas fa-trash"></i><span></button>
            <?php } ?>

            <?php if($is_ldc_primary_teacher){ ?>
                <?php 
                    $ldc_hide_classroom_edit_student_delete_icon_button =  get_site_option('ldc_hide_classroom_edit_student_delete_icon_button', 'no'); 
                    if ( $ldc_hide_classroom_edit_student_delete_icon_button === 'no' ) {
                ?> 
            <button type="button" class="btn_classroom" title="Delete student from classroom permanently"
                id="<?php _e(self::$form_id) ; ?>_btn_delete_permanently"><?php esc_html_e('Delete','lt-learndash-classroom');?>
                <i class="fas fa-user-times"></i><span></button>
                <?php } ?>
            <?php } ?>
            <?php do_action('ldc_after_edit_student_button', $user_id, $group_id); ?>
        </div>
    </div>
    <div class="form-action section text-right">
    </div>
</form>