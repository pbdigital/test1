<!--- Modal of add studnet email -->
<?php $form_id = "add_student_email_classroom"?>
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <?php 	
            $current_user_id = get_current_user_id();
            $ldc_student_email_data = get_user_meta( $current_user_id, 'ldc_student_email_data',true ); 
            if(empty($ldc_student_email_data)){ 
                $ldc_student_email_data = array();
            }
            extract($ldc_student_email_data);?>
        <ul class="tabs">
            <?php 
                $ldc_hide_classroom_broadcast_email_tab =  get_site_option('ldc_hide_classroom_broadcast_email_tab', 'no'); 
                if ( $ldc_hide_classroom_broadcast_email_tab === 'no' ) {
            ?> 
            <li class="tab-link current" data-tab="tab-2">
                <?php esc_html_e('Broadcast Email','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>

            <?php 
                $ldc_hide_classroom_student_welcome_email_tab =  get_site_option('ldc_hide_classroom_student_welcome_email_tab', 'no'); 
                if ( $ldc_hide_classroom_student_welcome_email_tab === 'no' ) {
            ?> 
            <li class="tab-link" data-tab="tab-1">
                <?php esc_html_e('Student Welcome Email','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>
            <?php 
                $ldc_enable_classrom_message_board =  get_site_option('ldc_enable_classrom_message_board'); 
                if ( $ldc_enable_classrom_message_board == 'yes' ){
            ?>
            <li class="tab-link" data-tab="tab-3">
                <?php esc_html_e('Broadcast Messages','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>
            <li class="tab-link classroom-close">&times;</li>
        </ul>

        <div class="classroom-modal-container with-tab">
            <div id="tab-1" class="tab-content ">
                <h5><?php esc_html_e('Configure Student welcome email','lt-learndash-classroom'); ?></h5>
                <p class="para">
                    <?php esc_html_e('This email template will be sent when you add new students','lt-learndash-classroom'); ?>
                </p>
                <br />
                <h6><?php _e('Available merge codes', 'lt-learndash-classroom'); ?>:</h6>
                <table class="modal-table">
                    <tr>
                        <td class="para italic">
                            <?php _e('Insert name of Parent Group', 'lt-learndash-classroom'); ?>:<code>{group_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert name of child-group', 'lt-learndash-classroom'); ?>:<code>{childgroup_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert name of Student', 'lt-learndash-classroom'); ?>:<code>{student_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert username of Student', 'lt-learndash-classroom'); ?>:<code>{user_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert password of Student', 'lt-learndash-classroom'); ?>:<code>{password}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Autologin link of Student', 'lt-learndash-classroom'); ?>:<code>{autologin}</code>
                        </td>
                    </tr>
                </table>
                <br />
                <h5><?php _e('Student Invitation Email', 'lt-learndash-classroom'); ?>:</h5>
                <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-add-student-email-classroom' );?>">
                    <div class="form_message"></div>
                    <div class="form-group">
                        <label class=""><?php esc_html_e('Subject','lt-learndash-classroom'); ?>:</label>
                        <input type="text" name="student_email_subject"
                            value="<?php echo isset($student_email_subject) ? esc_attr_e($student_email_subject) : ''; ?>"
                            placeholder="<?php esc_html_e('Subject','lt-learndash-classroom'); ?>" autocomplete="off" />

                        <label class=""><?php esc_html_e('Body','lt-learndash-classroom'); ?>:</label>
                        <?php
                        $content   = isset($student_email_body) ? $student_email_body : '';
                        $editor_id = 'student_email_body';
                        $settings  = array( 'media_buttons' => false,'wpautop' => false );
                        wp_editor( $content, $editor_id,$settings );
                    ?>
                    </div>
                    <div class="form-action text-right">
                        <button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom'); ?>
                            <i class="fa fa-angle-right"></i></button>
                        <!-- button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn_save_and_send"><?php esc_html_e('Save & Send','lt-learndash-classroom'); ?>
                            <i class="fa fa-angle-right"></i></button -->
                    </div>
                </form>
            </div>
            <div id="tab-2" class="tab-content current">
                <?php $form_id = "send_broadcast_email_to_students"?>
                <h5><?php esc_html_e('Configure Classroom broadcast email','lt-learndash-classroom'); ?></h5>
                <p class="para">
                    <?php esc_html_e('This email will be sent immediately to all students','lt-learndash-classroom'); ?>
                </p>
                <br />
                <h6><?php _e('Available merge codes', 'lt-learndash-classroom'); ?>:</h6>
                <table class="modal-table">
                    <tr>
                        <td class="para italic">
                            <?php _e('Insert name of Parent Group', 'lt-learndash-classroom'); ?>:<code>{group_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert name of child-group', 'lt-learndash-classroom'); ?>:<code>{childgroup_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert name of Student', 'lt-learndash-classroom'); ?>:<code>{student_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert username of Student', 'lt-learndash-classroom'); ?>:<code>{user_name}</code>
                        </td>
                    </tr>
                </table>
                <br />
                <h5><?php _e('Classroom Broadcast Email', 'lt-learndash-classroom'); ?>:</h5>
                <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-send-broadcast-email-to-students' );?>">
                    <div class="form_message"></div>
                    <div class="form-group">
                        <label class=""><?php esc_html_e('Subject','lt-learndash-classroom'); ?>:</label>
                        <input type="text" name="student_b_email_subject" value=""
                            placeholder="<?php esc_html_e('Subject','lt-learndash-classroom'); ?>" autocomplete="off" />

                        <label class=""><?php esc_html_e('Body','lt-learndash-classroom'); ?>:</label>
                        <?php
                        $content   = '';
                        $editor_id = 'student_b_email_body';
                        $settings  = array( 'media_buttons' => false,'wpautop' => false );
                        wp_editor( $content, $editor_id,$settings );
                    ?>
                    </div>
                    <div class="form-action text-right">
                        <button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Send','lt-learndash-classroom'); ?>
                            <i class="fa fa-angle-right"></i></button>
                    </div>
                </form>
            </div>
            <?php if ( $ldc_enable_classrom_message_board == 'yes' ){ ?>
            <div id="tab-3" class="tab-content ">
                <?php $form_id = "send_broadcast_message_to_students"?>
                <h5><?php _e('Classroom Broadcast Message', 'lt-learndash-classroom'); ?>:</h5>
                <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-send-broadcast-message-to-students' );?>">
                    <div class="form_message"></div>
                    <div class="form-group">
                        <!-- label class=""><?php esc_html_e('Message','lt-learndash-classroom'); ?>:</label -->
                        <?php
                        $content   = '';
                        $editor_id = 'student_b_message_body';
                        $settings  = array( 'media_buttons' => false,'wpautop' => false );
                        wp_editor( $content, $editor_id,$settings );
                    ?>
                    </div>
                    <div class="form-action text-right">
                        <button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Send','lt-learndash-classroom'); ?>
                            <i class="fa fa-angle-right"></i></button>
                    </div>
                </form>

                <h6><?php _e('Message List:','lt-learndash-classroom')?></h6>
                <table id="table-broadcast-messages-list"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-manage-broadcast-messages' );?>">
                    <thead>
                        <tr>
                            <th width="70%"><?php _e("Message",'lt-learndash-classroom'); ?></th>
                            <th width="20%"><?php _e("Date",'lt-learndash-classroom'); ?></th>
                            <th width="10%"><?php _e("Action",'lt-learndash-classroom'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="tbody-broadcast-messages-list">
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>

    </div>
</div>