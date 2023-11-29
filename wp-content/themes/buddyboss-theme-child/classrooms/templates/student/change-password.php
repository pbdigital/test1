<!-- The Modal for changing password -->
<?php 
$form_id = "save_classroom_student_password";
?>
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <div class="classroom-modal-header text-center"><?php esc_html_e('Change Password','lt-learndash-classroom'); ?>
            <span class="classroom-close">&times;</span>
        </div>
        <div class="classroom-modal-container">
            <form action="" method="POST" id="<?php echo $form_id; ?>"
                data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-change-student-password' );?>">
                <div class="form_message"></div>
                <div class="form-group section">
                    <div class="col grid_1_of_4">
                        <input type="hidden" name="user" />
                        <input type="password" name="new_password"
                            placeholder="<?php esc_html_e('New password','lt-learndash-classroom'); ?>">
                    </div>
                    <div class="col grid_1_of_4" style="margin-left:50px;">
                        <input type="password" name="confirm_password"
                            placeholder="<?php esc_html_e('Confirm password','lt-learndash-classroom'); ?>">
                    </div>
                </div>
                <div class="form-action section text-right pull-left action-buttons">
                    <div class="col grid_1_of_4">
                        <button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom'); ?><i
                                class="fa fa-angle-right"></i></button>
                    </div>
                </div>
                <div class="form-action section text-right"></div>
            </form>
        </div>
    </div>
</div>