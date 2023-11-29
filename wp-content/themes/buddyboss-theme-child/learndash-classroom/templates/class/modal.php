<?php namespace ld_classroom; ?>
<?php 
    $ldc_hide_school_delete_classroom_icon =  get_site_option('ldc_hide_school_delete_classroom_icon', 'no'); 
    $ldc_hide_school_delete_teacher_trashcan_icon =  get_site_option('ldc_hide_school_delete_teacher_trashcan_icon', 'no');
    $ldc_hide_school_delete_teacher_person_x_icon =  get_site_option('ldc_hide_school_delete_teacher_person_x_icon', 'no');
    
?>
<style>
    #tbody-classrooms-list span.icon-delete-classroom{
        display: <?php echo $ldc_hide_school_delete_classroom_icon === 'no' ? 'initial' : 'none'; ?>;
    }
    #tbody-teachers-list span.icon-delete-teacher{
        display: <?php echo $ldc_hide_school_delete_teacher_trashcan_icon === 'no' ? 'initial' : 'none'; ?>;
    }
    #tbody-teachers-list span.icon-delete-teacher-permanently{
        display: <?php echo $ldc_hide_school_delete_teacher_person_x_icon === 'no' ? 'initial' : 'none'; ?>;
    }

</style>
<div id="edit_classroom_init_modal"></div>
<?php 
    $user_id = get_current_user_id();
    $group_course_ids = array();
    if(Group::$parent_group_id){
        $group_course_ids = learndash_get_groups_courses_ids( $user_id, array( Group::$parent_group_id) );
    }
    // else{
    //     $group_course_ids = learndash_get_groups_courses_ids( $user_id, array( $groups[0]->ID) );
    // }

    $ldc_course_orderby =  get_site_option('ldc_course_orderby')??"title"; 
    $ldc_course_order =  get_site_option('ldc_course_order')??"ASC"; 
    
    $form_id = "add_classroom"; 
    $args = array(
        'numberposts' => -1,
        'post_type'   => 'sfwd-courses',
        'orderby' => $ldc_course_orderby,
        'post__in' => $group_course_ids,
        'order' => $ldc_course_order
    );
    $courses = get_posts($args);
?>
<!-- The Modal for adding teacher-->
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <ul class="tabs">
            <?php 
                $ldc_hide_school_manage_classroom_tab =  get_site_option('ldc_hide_school_manage_classroom_tab', 'no');
                if ( $ldc_hide_school_manage_classroom_tab === 'no') {
            ?>
            <li class="tab-link current" data-tab="tab-manage-classroom">
                <?php esc_html_e('Manage Classroom','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>

            <?php 
                $ldc_hide_school_add_classroom_tab =  get_site_option('ldc_hide_school_add_classroom_tab', 'no');
                if ( $ldc_hide_school_add_classroom_tab === 'no') {
            ?>
            <li class="tab-link" data-tab="tab-add-classroom">
                <?php esc_html_e('Add Classroom','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>
            <li class="tab-link classroom-close">&times;</li>
        </ul>
        <div class="classroom-modal-container with-tab">
            <div id="tab-manage-classroom" class="tab-content current">
                <div class="form_message"></div>
                <table id="table-classrooms-list"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-manage-classroom-delete-classroom' );?>">
                    <thead>
                        <tr>
                            <th><?php _e("Class Name",'lt-learndash-classroom'); ?></th>
                            <th><?php _e("Student Total",'lt-learndash-classroom'); ?></th>
                            <th><?php _e("Action",'lt-learndash-classroom'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="tbody-classrooms-list">
                    </tbody>
                </table>
            </div>
            <div id="tab-add-classroom" class="tab-content">
                <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-add-classroom' );?>">
                    <div class="form_message"></div>
                    <div class="form-group section">
                        <div class="col grid_1_of_4">
                            <input type="hidden" name="group" value="0" />
                            <input type="text" name="classroom_group_name" value=""
                                placeholder="<?php esc_html_e('Enter Class Name *','lt-learndash-classroom'); ?>"
                                autocomplete="off" />
                        </div>
                        <div class="col grid_2_of_4 div-has_teacher_exists">
                            <input type="checkbox" id="has_teacher_exists" name="has_teacher_exists" value="1">
                            <label
                                for="has_teacher_exists"><?php esc_html_e('Teacher already exists?','lt-learndash-classroom'); ?></label>
                        </div>
                    </div>
                    <div class="form-group section div-has-teacher">
                        <div class="col grid_1_of_4">
                            <select name="classroom_teacher_ids[]" multiple="multiple"
                                class="classroom_teacher_ids form-control">
                                <option value=""><?php _e('-- Choose teachers --','lt-learndash-classroom'); ?></option>
                                <?php 
                                $ldc_allow_leader_to_see_all_users =  get_site_option('ldc_allow_leader_to_see_all_users');
                                if($ldc_allow_leader_to_see_all_users){
                                    $child_groups_administrators = ldc_get_group_leaders();    
                                }
                                else{ 
                                    $child_groups_administrators = ldc_get_child_groups_administrators( Group::$parent_group_id );
                                }
                            foreach($child_groups_administrators as $admin_id => $child_groups_administrators){
                                ?>
                                <option value="<?php _e($admin_id); ?>">
                                    <?php _e($child_groups_administrators['display_name']); ?></option>
                                <?php
                            } 
                        ?>
                            </select>
                        </div>
                    </div>
                    <div class=" div-new-teacher">
                        <div class="form-group section">
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_firstname" value=""
                                    placeholder="<?php esc_html_e('Teacher Firstname *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_lastname" value=""
                                    placeholder="<?php esc_html_e('Teacher Lastname *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_username" value=""
                                    placeholder="<?php esc_html_e('Teacher Username *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group section">
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_email" value=""
                                    placeholder="<?php esc_html_e('Teacher Email *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                            <div class="col grid_1_of_4 text-right">
                                <label
                                    class="classrrom-form-label text-center"><?php _e('Password','lt-learndash-classroom'); ?>:</label>
                            </div>
                            <div class="col grid_1_of_4">

                                <input type="text" name="classroom_teacher_password" class="form_password"
                                    value="<?php echo esc_attr(generate_student_password()); ?>"
                                    placeholder="<?php esc_html_e('Password *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group section">
                        <div class="col grid_2_of_5 ">
                            
                            <label
                                class="classroom-form-label text-center"><?php esc_html_e('Available Courses','lt-learndash-classroom'); ?></label>
                            <input type="text" class="ldc_courses_search_box" placeholder="<?php _e('Search for names...','lt-learndash-classroom'); ?>" title="Type in a name" />
                            <select class="classroom-courses-select" id="listcoursesSelectBox" multiple>
                                <?php foreach($courses as $course){ ?>
                                <option value="<?php esc_attr_e( $course->ID ); ?>">
                                    <?php esc_html_e( $course->post_title); ?>
                                </option>
                                <?php } ?>
                            </select>

                            <select class="" id="listcoursesHidden" style="display:none;">
                                <?php foreach($courses as $course){ ?>
                                <option value="<?php esc_attr_e( $course->ID ); ?>">
                                    <?php esc_html_e( $course->post_title ); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col grid_1_of_5">
                            <div class="classroom-courses-actions">
                                <a href="#" id="btnAddCourse" class="btnAddCourse"><img
                                        src="<?php echo LT_LD_CLASSROOM_URL . 'img/c_arrow_right.png'; ?>" /></a>
                                <br>
                                <a href="#" id="btnRemoveCourse" class="btnRemoveCourse">
                                    <img src="<?php echo LT_LD_CLASSROOM_URL . 'img/c_arrow_left.png'; ?>" />
                                </a>
                            </div>
                        </div>
                        <div class="col grid_2_of_5">
                            <label
                                class="classroom-form-label text-center"><?php esc_html_e('Active Courses','lt-learndash-classroom'); ?></label>
                            <input type="text" class="ldc_courses_search_box" placeholder="<?php _e('Search for names...','lt-learndash-classroom'); ?>" title="Type in a name" />    
                            <select class="classroom-courses-select" name="courses[]" id="coursesSelectBox" multiple>
                            </select>
                        </div>
                    </div>
                    <div class="form-action section text-right pull-left">
                        <div class="col grid_1_of_4">
                            <button type="submit" class="btn_classroom"
                                id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom'); ?><i
                                    class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                    <div class="form-action section text-right">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--- Modal of add teacher email -->
<?php $form_id = "add_teacher_email_classroom"; ?>
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <?php 	$ldc_teacher_email_data =get_user_meta( $user_id, 'ldc_teacher_email_data',true ); 
        if(empty($ldc_teacher_email_data)){ 
            $ldc_teacher_email_data = array();
        }
        extract($ldc_teacher_email_data);?>
        <ul class="tabs">
            <?php 
                $ldc_hide_school_broadcast_email_tab =  get_site_option('ldc_hide_school_broadcast_email_tab', 'no');
                if ( $ldc_hide_school_broadcast_email_tab === 'no') {
            ?>
            <li class="tab-link current" data-tab="tab-2">
                <?php esc_html_e('Broadcast Email','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>

            <?php 
                $ldc_hide_school_welcome_email_tab =  get_site_option('ldc_hide_school_welcome_email_tab', 'no');
                if ( $ldc_hide_school_welcome_email_tab === 'no') {
            ?>
            <li class="tab-link" data-tab="tab-1">
                <?php esc_html_e('Teacher Welcome Email','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>
            <li class="tab-link classroom-close">&times;</li>
        </ul>

        <div class="classroom-modal-container with-tab">
            <div id="tab-1" class="tab-content">
                <h5 style="margin-bottom:10px;">
                    <?php esc_html_e('Configure Teacher welcome email','lt-learndash-classroom'); ?></h5>
                <p class="para">
                    <?php esc_html_e('This email template will be sent when you add new teachers','lt-learndash-classroom'); ?>
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
                            <?php _e('Insert name of Teacher', 'lt-learndash-classroom'); ?>:<code>{teacher_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert username of Teacher', 'lt-learndash-classroom'); ?>:<code>{teacher_username}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert password of Teacher', 'lt-learndash-classroom'); ?>:<code>{password}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Autologin link of Teacher', 'lt-learndash-classroom'); ?>:<code>{autologin}</code>
                        </td>
                    </tr>
                </table>
                <br />
                <h5><?php _e('Teacher Invitation Email', 'lt-learndash-classroom'); ?>:</h5>
                <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-add-teacher-email-classroom' );?>">
                    <div class="form_message"></div>
                    <div class="form-group">
                        <label class=""><?php esc_html_e('Subject','lt-learndash-classroom'); ?>:</label>
                        <input type="text" name="teacher_email_subject"
                            value="<?php echo isset($teacher_email_subject) ? esc_attr_e($teacher_email_subject) : ''; ?>"
                            placeholder="<?php esc_html_e('Subject','lt-learndash-classroom'); ?>" autocomplete="off" />

                        <label class="modal-editor-label"><?php esc_html_e('Body','lt-learndash-classroom'); ?>:</label>
                        <?php
                        $content   = isset($teacher_email_body) ? $teacher_email_body : '';
                        $editor_id = 'teacher_email_body';
                        $settings  = array( 'media_buttons' => false,'wpautop' => false );
                        wp_editor( $content, $editor_id,$settings );
                    ?>
                    </div>
                    <div class="form-action text-right">
                        <button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom'); ?>
                            <i class="fa fa-angle-right"></i></button>
                    </div>
                </form>
            </div>
            <div id="tab-2" class="tab-content current">
                <?php $form_id = "send_broadcast_email_to_teachers"?>
                <h5 style="margin-bottom:10px;">
                    <?php esc_html_e('Configure Teachers broadcast email','lt-learndash-classroom'); ?></h5>
                <p class="para">
                    <?php esc_html_e('This email will be sent immediately to all teachers','lt-learndash-classroom'); ?>
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
                            <?php _e('Insert name of Teacher', 'lt-learndash-classroom'); ?>:<code>{teacher_name}</code>
                        </td>
                        <td class="para italic">
                            <?php _e('Insert username of Teacher', 'lt-learndash-classroom'); ?>:<code>{teacher_username}</code>
                        </td>
                    </tr>
                </table>
                <br />
                <h5><?php _e('Teachers Broadcast Email', 'lt-learndash-classroom'); ?>:</h5>
                <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-send-broadcast-email-to-teachers' );?>">
                    <div class="form_message"></div>
                    <div class="form-group">
                        <label class=""><?php esc_html_e('Subject','lt-learndash-classroom'); ?>:</label>
                        <input type="text" name="teacher_b_email_subject" value=""
                            placeholder="<?php esc_html_e('Subject','lt-learndash-classroom'); ?>" autocomplete="off" />

                        <label class="modal-editor-label"><?php esc_html_e('Body','lt-learndash-classroom'); ?>:</label>
                        <?php
                        $content   = '';
                        $editor_id = 'teacher_b_email_body';
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
        </div>
    </div>
</div>

<!--- Modal of manage courses -->
<?php $form_id = "manage_classroom_courses"; ?>
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <div class="classroom-modal-header text-center">
            <span class="modal-title"><?php esc_html_e('Manage Courses','lt-learndash-classroom'); ?></span>
            <span class="classroom-close">&times;</span>
        </div>
        <div class="classroom-modal-container">
            <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-manages-courses' );?>">
                <div class="form_message"></div>
                <div class="form-group section">
                    <div class="col grid_2_of_5 ">
                        <input type="hidden" name="group" value="0" />
                        <label
                            class="classrrom-form-label text-center"><?php esc_html_e('Available Courses','lt-learndash-classroom'); ?></label>
                        <input type="text" class="ldc_courses_search_box" placeholder="<?php _e('Search for names...','lt-learndash-classroom'); ?>" title="Type in a name" />
                        <select class="classroom-courses-select" id="listcoursesSelectBox" multiple>
                            <?php foreach($courses as $course){ ?>
                            <option value="<?php esc_attr_e( $course->ID ); ?>">
                                <?php esc_html_e( $course->post_title); ?>
                            </option>
                            <?php } ?>
                        </select>

                        <select class="" id="listcoursesHidden" style="display:none;">
                            <?php foreach($courses as $course){ ?>
                            <option value="<?php esc_attr_e( $course->ID ); ?>">
                                <?php esc_html_e( $course->post_title ); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col grid_1_of_5">
                        <div class="classroom-courses-actions">
                            <a href="#" id="btnAddCourse" class="btnAddCourse"><img
                                    src="<?php echo LT_LD_CLASSROOM_URL . 'img/c_arrow_right.png'; ?>" /></a>
                            <br>
                            <a href="#" id="btnRemoveCourse" class="btnRemoveCourse">
                                <img src="<?php echo LT_LD_CLASSROOM_URL . 'img/c_arrow_left.png'; ?>" />
                            </a>
                        </div>
                    </div>
                    <div class="col grid_2_of_5">
                        <label
                            class="classrrom-form-label text-center"><?php esc_html_e('Active Courses','lt-learndash-classroom'); ?></label>
                        <input type="text" class="ldc_courses_search_box" placeholder="<?php _e('Search for names...','lt-learndash-classroom'); ?>" title="Type in a name" />
                        <select class="classroom-courses-select" name="courses[]" id="coursesSelectBox" multiple>
                        </select>
                    </div>
                </div>
                <div class="form-action section text-right pull-left">
                    <div class="col grid_1_of_4">
                        <button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom'); ?><i
                                class="fa fa-angle-right"></i></button>
                    </div>
                </div>
                <div class="form-action section text-right">
                </div>
            </form>
        </div>
    </div>
</div>



<!--- Modal of manage teachers -->
<?php $form_id = "manage_classroom_add_teachers"; ?>
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <ul class="tabs">
            <?php  
            $ldc_hide_school_manage_teachers_tab =  get_site_option('ldc_hide_school_manage_teachers_tab', 'no'); 
            if ( $ldc_hide_school_manage_teachers_tab === 'no') { ?>
            <li class="tab-link current" data-tab="tab-manage-teachers">
                <?php esc_html_e('Manage Teachers','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>

            <?php  
            $ldc_hide_school_add_teachers_tab =  get_site_option('ldc_hide_school_add_teachers_tab', 'no'); 
            if ( $ldc_hide_school_add_teachers_tab === 'no') { ?>

            <li class="tab-link" data-tab="tab-add-teacher">
                <?php esc_html_e('Add Teachers','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>

            <?php  
            $ldc_hide_school_add_teacher_as_student_tab =  get_site_option('ldc_hide_school_add_teacher_as_student_tab', 'no'); 
            if ( $ldc_hide_school_add_teacher_as_student_tab === 'no') { ?>
            <li class="tab-link" data-tab="tab-be-a-student-as-a-teacher">
                <?php esc_html_e('Add Teacher as Student','lt-learndash-classroom'); ?>
            </li>
            <?php } ?>

            <li class="tab-link classroom-close">&times;</li>
        </ul>

        <!-- <div class="classroom-modal-header text-center">
            <span class="modal-title"><?php esc_html_e('Manage Teachers','lt-learndash-classroom'); ?></span>
            <span class="classroom-close">&times;</span>
        </div> -->
        <div class="classroom-modal-container with-tab">
            <div id="tab-manage-teachers" class="tab-content current">
                <table id="table-teachers-list" data-group=""
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-manage-classroom-delete-teacher' );?>">
                    <thead>
                        <tr>
                            <th><?php _e("Firstname",'lt-learndash-classroom'); ?></th>
                            <th><?php _e("Lastname",'lt-learndash-classroom'); ?></th>
                            <th><?php _e("Username",'lt-learndash-classroom'); ?></th>
                            <th><?php _e("Email",'lt-learndash-classroom'); ?></th>
                            <th><?php _e("Action",'lt-learndash-classroom'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="tbody-teachers-list">
                    </tbody>
                </table>
            </div>
            <div id="tab-add-teacher" class="tab-content">
                <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-manage-classroom-add-teachers' );?>">
                    <div class="form_message"></div>
                    <div class="form-group section">
                        <div class="col grid_1_of_4">
                            <input type="hidden" name="group" value="0" />
                            <input type="hidden" name="classroom_teacher_id" value="0" />
                            <input type="text" name="classroom_group_name" value=""
                                placeholder="<?php esc_html_e('Enter Class Name *','lt-learndash-classroom'); ?>"
                                readonly disabled autocomplete="off" />
                        </div>
                        <div class="col grid_2_of_4 div-has_teacher_exists">
                            <input type="checkbox" id="edit_has_teacher_exists" name="has_teacher_exists" value="1">
                            <label
                                for="edit_has_teacher_exists"><?php esc_html_e('Teacher already exists?','lt-learndash-classroom'); ?></label>
                        </div>
                    </div>
                    <div class="form-group section div-has-teacher">
                        <div class="col grid_1_of_4">
                            <select name="classroom_teacher_ids[]" multiple="multiple"
                                class="classroom_teacher_ids form-control">
                                <!-- <option value=""><?php _e('-- Choose teacher --','lt-learndash-classroom'); ?></option> -->
                                <?php 
                            $ldc_allow_leader_to_see_all_users =  get_site_option('ldc_allow_leader_to_see_all_users');
                            if($ldc_allow_leader_to_see_all_users){
                                $child_groups_administrators = ldc_get_group_leaders();    
                            }
                            else{ 
                                $child_groups_administrators = ldc_get_child_groups_administrators( Group::$parent_group_id );
                            }

                            
                            foreach($child_groups_administrators as $admin_id => $child_groups_administrator){
                                ?>
                                <option value="<?php _e($admin_id); ?>">
                                    <?php _e($child_groups_administrator['display_name']); ?></option>
                                <?php
                            } 
                        ?>
                            </select>
                        </div>
                    </div>
                    <div class="div-new-teacher">
                        <div class="form-group section ">
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_firstname" value=""
                                    placeholder="<?php esc_html_e('Teacher Firstname *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_lastname" value=""
                                    placeholder="<?php esc_html_e('Teacher Lastname *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group section ">
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_username" value=""
                                    placeholder="<?php esc_html_e('Teacher Username *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_email" value=""
                                    placeholder="<?php esc_html_e('Teacher Email *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>

                            <div class="col grid_1_of_6 text-right">
                                <label
                                    class="classrrom-form-label text-center"><?php _e('Password','lt-learndash-classroom'); ?>:</label>
                            </div>
                            <div class="col grid_1_of_4">
                                <input type="text" name="classroom_teacher_password" class="form_password"
                                    value="<?php echo esc_attr(generate_student_password()); ?>"
                                    placeholder="<?php esc_html_e('Password *','lt-learndash-classroom'); ?>"
                                    autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="form-action section text-right pull-left">
                        <div class="col grid_1_of_4">
                            <button type="submit" class="btn_classroom"
                                id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom'); ?><i
                                    class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                    <div class="form-action section">

                    </div>
                </form>
            </div>
            <div id="tab-be-a-student-as-a-teacher" class="tab-content">
                <?php $form_id = "manage_classroom_be_a_student_as_a_teacher"?>
                <form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal"
                    data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-manage-classroom-be-a-student-as-a-teacher' );?>">
                    <div class="form_message"></div>
                    <div class="form-group section">
                        <div class="col grid_4_of_4">
                            <h4><strong><?php _e("Class Name",'lt-learndash-classroom');?>: </strong><span
                                    class="classroom_group_name"></span></h4>
                        </div>
                    </div>
                    <div class="form-group section">
                        <div class="col grid_1_of_4">
                            <input type="hidden" name="group" value="0" />
                            <select name="classroom_teacher_ids[]" multiple="multiple"
                                class="classroom_teacher_ids form-control">
                                <?php 
                            //$child_groups_administrators = ldc_get_child_groups_administrator($parent_group_id);
                            foreach($child_groups_administrators as $admin_id => $child_groups_administrator){
                                ?>
                                <option value="<?php _e($admin_id); ?>">
                                    <?php _e($child_groups_administrator['display_name']); ?></option>
                                <?php
                            } 
                        ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group section">
                        <div class="col grid_4_of_4">
                            <table id="table-teachers-list" data-group=""
                                data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-manage-classroom-delete-teacher-from-student' );?>">
                                <thead>
                                    <tr>
                                        <th><?php _e("Firstname",'lt-learndash-classroom'); ?></th>
                                        <th><?php _e("Lastname",'lt-learndash-classroom'); ?></th>
                                        <th><?php _e("Username",'lt-learndash-classroom'); ?></th>
                                        <th><?php _e("Email",'lt-learndash-classroom'); ?></th>
                                        <th><?php _e("Action",'lt-learndash-classroom'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-teachers-list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-action section text-right pull-left">
                        <div class="col grid_1_of_4">
                            <button type="submit" class="btn_classroom"
                                id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Save','lt-learndash-classroom'); ?><i
                                    class="fa fa-angle-right"></i></button>
                        </div>
                    </div>
                    <div class="form-action section">

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>