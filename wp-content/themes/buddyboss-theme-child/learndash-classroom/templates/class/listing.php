<?php namespace ld_classroom; ?>
<div>
    <table cellspacing="0" class="groups_user_table classroom_table">
        <thead>
            <tr>
                <th><?php esc_html_e('Class Name','lt-learndash-classroom'); ?></th>
                <th class="pull-center"><?php esc_html_e('Student Total','lt-learndash-classroom'); ?></th>
                <th class="pull-center"><?php esc_html_e('Student List','lt-learndash-classroom'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty($groups)) {?>
            <tr>
                <td colspan="3">
                    <?php _e('No record found.','lt-learndash-classroom'); ?>
                </td>
            </tr>
            <?php }?>
            <?php foreach($groups as $group){ 
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
                        $encrypt_group_id = general_encrypt_decrypt('encrypt', $group->ID);

                        $courses = learndash_group_enrolled_courses( $group->ID );	
                        
                        // $ldc_teacher_firstname = get_post_meta($group->ID,'ldc_teacher_firstname',true);
                        // $ldc_teacher_lastname = get_post_meta($group->ID,'ldc_teacher_lastname',true);
                        // $ldc_teacher_email = get_post_meta($group->ID,'ldc_teacher_email',true);
                        $group_leaders = learndash_get_groups_administrators( $group->ID );
                        $group_leader = array();
                        $teacher_first_name = $teacher_last_name = "";
                        $list_groups_leaders = [];
                        if(!empty($group_leaders)){
                            foreach($group_leaders as $group_leader){
                                $group_leader_meta = get_userdata($group_leader->ID);
                                $list_groups_leaders[] = array(
                                    "id"            => $group_leader->ID,
                                    "email"         => $group_leader->user_email,
                                    "username"      => $group_leader->user_login,
                                    "first_name"    => get_user_meta( $group_leader->ID, 'first_name', true ),
                                    "last_name"     => get_user_meta( $group_leader->ID, 'last_name', true ),
                                    "is_admin"      => (in_array("administrator",$group_leader_meta->roles)?true:false)
                                );
                            }
                            $group_leader = $group_leaders[0];
                            $teacher_first_name = get_user_meta( $group_leader->ID, 'first_name', true );
                            $teacher_last_name = get_user_meta( $group_leader->ID, 'last_name', true );
                        }

                        
                        $group_student_ids = learndash_get_groups_user_ids( $group->ID );
                        $list_student_groups_leaders = [];
                        foreach($group_student_ids as $group_student_id){
                            $student = get_user_by("ID", $group_student_id);
                            $student_meta = get_userdata($group_student_id);
                            if(in_array("group_leader",$student_meta->roles)){
                                $list_student_groups_leaders[] = array(
                                    "id"            => $group_student_id,
                                    "email"         => $student->user_email,
                                    "username"      => $student->user_login,
                                    "first_name"    => get_user_meta( $group_student_id, 'first_name', true ),
                                    "last_name"     => get_user_meta( $group_student_id, 'last_name', true )
                                );
                            }
                            
                        }
                    ?>
            <tr>
                <td>
                    <h5><?php esc_html_e( $group->post_title);  ?></h5>
                    <div class="manage-buttons">
                        <?php
                    if(\ld_classroom\Group::is_admin_or_primary_group_leader(\ld_classroom\Group::$parent_group_id)){
                    ?>
                        <?php  
                            $ldc_hide_school_manage_course_button =  get_site_option('ldc_hide_school_manage_course_button', 'no'); 

                        if ( $ldc_hide_school_manage_course_button === 'no') { ?>
                        <a href="#" data-group="<?php esc_attr_e( $encrypt_group_id); ?>"
                            data-group-name="<?php esc_attr_e($group->post_title); ?>"
                            data-group-courses="<?php esc_attr_e( '['.implode(',',$courses).']'); ?>"
                            class="btn_classroom btn_classroom_black pull-right manage_classroom_courses"><?php esc_html_e('Manage
                        Courses','lt-learndash-classroom'); ?> <i class="fa fa-angle-right"></i></a>
                        <?php } ?>

                        <?php  
                            $ldc_hide_school_manage_teachers_button =  get_site_option('ldc_hide_school_manage_teachers_button', 'no'); 

                        if ( $ldc_hide_school_manage_teachers_button === 'no') { ?>
                        
                        <a href="#" data-group="<?php esc_attr_e( $encrypt_group_id); ?>"
                            data-group-name="<?php esc_attr_e($group->post_title); ?>"
                            data-teacher-id="<?php isset($group_leader->ID) ? esc_attr_e($group_leader->ID): "0"; ?>"
                            data-teacher-firstname="<?php isset($teacher_first_name)?esc_attr_e($teacher_first_name):""; ?>"
                            data-teacher-lastname="<?php isset($teacher_last_name)?esc_attr_e($teacher_last_name):""; ?>"
                            data-teacher-username="<?php isset($group_leader->user_login)?esc_attr_e($group_leader->user_login):""; ?>"
                            data-teacher-email="<?php isset($group_leader->user_email)?esc_attr_e($group_leader->user_email):""; ?>"
                            data-group-leaders="<?php esc_attr_e(json_encode($list_groups_leaders)); ?>"
                            data-student-group-leaders="<?php esc_attr_e(json_encode($list_student_groups_leaders)); ?>"
                            class="btn_classroom btn_classroom_black pull-right manage_classroom_teachers"><?php esc_html_e('Manage Teachers','lt-learndash-classroom'); ?>
                            <i class="fa fa-angle-right"></i></a>
                        <?php } ?>
                    <?php } ?>
                    </div>
                </td>
                <td align="center" class="classroom_total_student"><?php esc_html_e( $user_query->total_users ); ?></td>
                <td align="center">
                    <a href="<?php echo ( isset($wp->query_vars['ld-classroom'])? home_url( '/ld-classroom/group/' ) : '?group=' ) . $encrypt_group_id; ?>"
                        class="btn_classroom"><?php esc_html_e('Manage Students','lt-learndash-classroom'); ?> <i
                            class="fa fa-angle-right"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>