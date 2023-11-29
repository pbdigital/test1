<?php $form_id = "save_classroom_student"; ?>
<!-- The Modal for adding student classroom-->
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <div class="classroom-modal-header text-center">
            <?php esc_html_e('ADD NEW STUDENT - SINGLE ENTRY','lt-learndash-classroom'); ?><span
                class="classroom-close">&times;</span></div>
        <div class="classroom-modal-container">
            <form action="" method="POST" id="<?php echo $form_id; ?>"
                data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-add-student' );?>">
                <div class="form_message"></div>
                <div class="form-group section">
                    <div class="col grid_2_of_4">
                        <input type="checkbox" id="has_student_exists" name="has_student_exists" value="1">
                        <label
                            for="has_student_exists"><?php esc_html_e('Student already exists?','lt-learndash-classroom'); ?>
                        </label>
                    </div>
                </div>
                <div class="form-group section div-has-student">
                    <div class="col grid_1_of_4">
                        <select name="classroom_student_id[]" class="classroom_student_ids form-control"
                            multiple="multiple">
                            <option value=""><?php _e('-- Choose student --','lt-learndash-classroom'); ?></option>
                            <?php 
                            $ldc_allow_leader_to_see_all_users =  get_site_option('ldc_allow_leader_to_see_all_users');
                            if($ldc_allow_leader_to_see_all_users){
                                $child_groups_users = ldc_get_users();    
                            }
                            else{ 
                                $child_groups_users = ldc_get_child_groups_users($group_info->post_parent, $group_info->ID);
                            }
                            foreach($child_groups_users as $usr_id => $child_groups_user){
                                ?>
                            <option value="<?php _e($usr_id); ?>">
                                <?php _e($child_groups_user['display_name'] . ' (' . $child_groups_user['username'] . ')' ); ?>
                            </option>
                            <?php
                            } 
                        ?>
                        </select>
                    </div>
                </div>
                <div class="div-new-student">
                    <div class="form-group section">
                        <div class="col grid_1_of_4">
                            <input type="hidden" name="classroom_student[group]"
                                value="<?php echo get_query_var( 'group_id' , false) != '' ? get_query_var( 'group_id', false ) : $_GET['group'] ; ?>" />
                            <input type="text" name="classroom_student[firstname]" value=""
                                placeholder="<?php esc_html_e('First Name *','lt-learndash-classroom'); ?>"
                                autocomplete="off" />
                        </div>
                        <div class="col grid_1_of_4">
                            <input type="text" name="classroom_student[lastname]" value=""
                                placeholder="<?php esc_html_e('Last Name','lt-learndash-classroom'); ?>"
                                autocomplete="off" />
                        </div>
                        <div class="col grid_1_of_4">
                            <input type="text" name="classroom_student[username]" value=""
                                placeholder="<?php esc_html_e('Username *','lt-learndash-classroom'); ?>"
                                autocomplete="off" />
                        </div>
                        <div class="col grid_1_of_4">
                            <input type="text" name="classroom_student[email]" value=""
                                placeholder="<?php esc_html_e('Email','lt-learndash-classroom'); ?>"
                                autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group section">
                        <div class="col grid_1_of_4">
                            <label
                                class="classrrom-form-label text-center"><?php esc_html_e('Password','lt-learndash-classroom'); ?></label>
                            <input type="text" name="classroom_student[password]" class="form_password"
                                value="<?php echo esc_attr(generate_student_password()); ?>"
                                placeholder="<?php esc_html_e('Password','lt-learndash-classroom'); ?>" />
                        </div>
                    </div>
                </div>
                <div class="form-action section text-right pull-left action-buttons">
                    <div class="col grid_1_of_4">
                        <button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom'); ?> <i
                                class="fa fa-angle-right"></i></button>
                    </div>
                </div>
                <div class="form-action section text-right"></div>
            </form>
        </div>
    </div>
</div>