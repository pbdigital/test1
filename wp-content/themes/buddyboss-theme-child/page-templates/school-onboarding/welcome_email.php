<?php 
$learndash_global_email["teacher_subject"] = get_field("teacher_welcome_email_subject","option");
$learndash_global_email["teacher_body_greetings"] = get_field("teacher_welcome_email_greetings","option");
$learndash_global_email["teacher_body_copy_1"] = get_field("teacher_welcome_email_body_1","option");
$learndash_global_email["teacher_body_copy_2"] = get_field("teacher_welcome_email_body_2","option");
$learndash_global_email["teacher_body_copy_3"] = get_field("teacher_welcome_email_body_3","option");

$learndash_global_email["student_subject"] = get_field("student_welcome_email_subject","option");
$learndash_global_email["student_body_greetings"] = get_field("student_welcome_email_greetings","option");
$learndash_global_email["student_body_copy_1"] = get_field("student_welcome_email_body_1","option");
$learndash_global_email["student_body_copy_2"] = get_field("student_welcome_email_body_2","option");
$learndash_global_email["student_body_copy_3"] = get_field("student_welcome_email_body_3","option");


$learndash_global_email["institute_family_subject"] = get_field("institute_family_welcome_email_subject","option");
$learndash_global_email["institute_family_greetings"] = get_field("institute_family_welcome_email_greetings","option");
$learndash_global_email["institute_family_welcome_email_body_1"] = get_field("institute_family_welcome_email_body_1","option");
$learndash_global_email["institute_family_welcome_email_body_2"] = get_field("institute_family_welcome_email_body_2","option");

$school_id = get_user_meta( get_current_user_id() ,"selected_institute", true);
if(!empty($school_id)){
   
    $teacher_subject = get_post_meta( $school_id, "school_onboarding_teacher_welcome_email_subject",true );
    $teacher_greetings = get_post_meta( $school_id, "school_onboarding_teacher_welcome_email_greetings",true );
    $teacher_body_1 = get_post_meta( $school_id, "school_onboarding_teacher_welcome_email_body_1",true );
    $teacher_body_2 = get_post_meta( $school_id, "school_onboarding_teacher_welcome_email_body_2",true );
    $teacher_body_3 = get_post_meta( $school_id, "school_onboarding_teacher_welcome_email_body_3",true );
    
    $student_subject = get_post_meta( $school_id, "school_onboarding_student_welcome_email_subject",true );
    $student_greetings = get_post_meta( $school_id, "school_onboarding_student_welcome_email_greetings",true );
    $student_body_1 = get_post_meta( $school_id, "school_onboarding_student_welcome_email_body_1",true );
    $student_body_2 = get_post_meta( $school_id, "school_onboarding_student_welcome_email_body_2",true );
    $student_body_3 = get_post_meta( $school_id, "school_onboarding_student_welcome_email_body_3",true );

    $institute_family_subject = get_post_meta( $school_id, "school_onboarding_institute_family_welcome_email_subject",true );
    $institute_family_greetings = get_post_meta( $school_id, "school_onboarding_institute_family_welcome_email_greetings",true );
    $institute_family_welcome_email_body_1 = get_post_meta( $school_id, "school_onboarding_institute_family_welcome_email_body_1",true );
    $institute_family_welcome_email_body_2 = get_post_meta( $school_id, "school_onboarding_institute_family_welcome_email_body_2",true );

    if(!empty($teacher_subject)) $learndash_global_email["teacher_subject"] = $teacher_subject;
    if(!empty($teacher_greetings)) $learndash_global_email["teacher_body_greetings"] = $teacher_greetings;
    if(!empty($teacher_body_1)) $learndash_global_email["teacher_body_copy_1"] = $teacher_body_1;
    if(!empty($teacher_body_2)) $learndash_global_email["teacher_body_copy_2"] = $teacher_body_2;
    if(!empty($teacher_body_3)) $learndash_global_email["teacher_body_copy_3"] = $teacher_body_3;

    if(!empty($student_subject)) $learndash_global_email["student_subject"] = $student_subject;
    if(!empty($student_greetings)) $learndash_global_email["student_body_greetings"] = $student_greetings;
    if(!empty($student_body_1)) $learndash_global_email["student_body_copy_1"] = $student_body_1;
    if(!empty($student_body_2)) $learndash_global_email["student_body_copy_2"] = $student_body_2;
    if(!empty($student_body_3)) $learndash_global_email["student_body_copy_3"] = $student_body_3;

    if(!empty($institute_family_subject)) $learndash_global_email["institute_family_subject"] = $institute_family_subject;
    if(!empty($institute_family_greetings)) $learndash_global_email["institute_family_greetings"] = $institute_family_greetings;
    if(!empty($institute_family_welcome_email_body_1)) $learndash_global_email["institute_family_welcome_email_body_1"] = $institute_family_welcome_email_body_1;
    if(!empty($institute_family_welcome_email_body_2)) $learndash_global_email["institute_family_welcome_email_body_2"] = $institute_family_welcome_email_body_2;
}

$tinymce = ['content_css' => get_stylesheet_directory_uri() . '/assets/css/school-onboarding.css?'.ENQUEUE_VERSION];

global $post;
$current_page_id = $post->ID;
$page_slug = get_post_field('post_name', $current_page_id);
?>
<div class="welcome-email-container">
    <div class="tab-menu">
        <a href="" class="active" data-target="#teacher-welcome-email">Teacher Welcome Email</a>
        <a href="" data-target="#student-welcome-email" >Student Welcome Email</a>
        <?php 
        if($page_slug != "school-onboarding"){
            ?>
            <a href="" data-target="#family-welcome-email" >Family Welcome Email</a>
            <?php 
        }
        ?>
    </div>
    <div class="tabs">
        <div class="tab-content active" id="teacher-welcome-email">
            <h3>Configure Teacher Welcome Email</h3>
            <p>This email template will be sent when you add new teachers</p>

            <div class="email-template">
                <div class="subject" >
                    <label >Subject</label>
                    <input type="text" name="teacher_welcome_email[subject]" class="teacher_welcome_email_subject" value="<?=$learndash_global_email["teacher_subject"]?>">
                </div>
                <?php /*
                <div class="body" >
                    <label >Body</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'teacher_welcome_email[body]',"tinymce"=>$tinymce];
                    $editor_id = "teacher_welcome_email_body";
                    wp_editor( $learndash_global_email["teacher_body"], $editor_id, $settings );
                    ?>
                </div>
                */ ?>
                <div class="body" >
                    <label >Greetings</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'teacher_welcome_email[teacher_body_greetings]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "teacher_body_greetings";
                    wp_editor( strip_tags($learndash_global_email["teacher_body_greetings"]), $editor_id, $settings );
                    ?>
                </div>

                <div class="body" >
                    <label >Body Copy #1</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'teacher_welcome_email[teacher_body_copy_1]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "teacher_body_copy_1";
                    wp_editor( $learndash_global_email["teacher_body_copy_1"], $editor_id, $settings );
                    ?>
                </div>

                <div class="body" >
                    <label >Body Copy #2</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'teacher_welcome_email[teacher_body_copy_2]',
                                    "tinymce"=>$tinymce, 
                                    "textarea_rows" => 2,
                                    'readonly' => true, // Set the readonly attribute to true
                                ];
                    $editor_id = "teacher_body_copy_2";
                    #wp_editor( $learndash_global_email["teacher_body_copy_2"], $editor_id, $settings );
                    ?>
                    <div class="body2-disabled teacher_body_copy_2"><?=$learndash_global_email["teacher_body_copy_2"]?></div>
                </div>

                <div class="body" >
                    <label >Body Copy #3</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'teacher_welcome_email[teacher_body_copy_3]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "teacher_body_copy_3";
                    wp_editor( $learndash_global_email["teacher_body_copy_3"], $editor_id, $settings );
                    ?>
                </div>

            </div>
        </div>

        <div class="tab-content" id="student-welcome-email">
            <h3>Configure Student Welcome Email</h3>
            <p>This email template will be sent when you add new student</p>

            <div class="email-template">
                <div class="subject" >
                    <label >Subject</label>
                    <input type="text" name="student_welcome_email[subject]" class="student_welcome_email_subject" value="<?=$learndash_global_email["student_subject"]?>">
                </div>

                <?php /*
                <div class="body" >
                    <label >Body</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'student_welcome_email[body]',"tinymce"=>$tinymce];
                    $editor_id = "student_welcome_email_body";
                    wp_editor( $learndash_global_email["student_body"], $editor_id, $settings );
                    ?>
                </div> */?>
                <div class="body" >
                    <label >Greetings</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'student_welcome_email[student_body_greetings]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "student_body_greetings";
                    wp_editor( strip_tags($learndash_global_email["student_body_greetings"]), $editor_id, $settings );
                    ?>
                </div>

                <div class="body" >
                    <label >Body Copy #1</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'student_welcome_email[student_body_copy_1]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "student_body_copy_1";
                    wp_editor( $learndash_global_email["student_body_copy_1"], $editor_id, $settings );
                    ?>
                </div>
                
                

                <div class="body" >
                    <label >Body Copy #2</label>
                    <div class="body2-disabled student_body_copy_2"><?=$learndash_global_email["student_body_copy_2"]?></div>
                </div>

                <div class="body" >
                    <label >Body Copy #3</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'student_welcome_email[student_body_copy_3]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "student_body_copy_3";
                    wp_editor( $learndash_global_email["student_body_copy_3"], $editor_id, $settings );
                    ?>
                </div>

            </div>
        </div>


        <!-- Institute Family Welcome Email-->
        <div class="tab-content" id="family-welcome-email">
            <h3>Configure Family Welcome Email</h3>
            <p>This email template will be sent when you add new parent</p>

            <div class="email-template">
                <div class="subject" >
                    <label >Subject</label>
                    <input type="text" name="institute_family[subject]" class="institute_family_subject" value="<?=$learndash_global_email["institute_family_subject"]?>">
                </div>

                <div class="body" >
                    <label >Greetings</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'institute_family[institute_family_greetings]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "institute_family_greetings";
                    wp_editor( strip_tags($learndash_global_email["institute_family_greetings"]), $editor_id, $settings );
                    ?>
                </div>

                <div class="body" >
                    <label >Body Copy #1</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'institute_family[institute_family_welcome_email_body_1]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "institute_family_welcome_email_body_1";
                    wp_editor( $learndash_global_email["institute_family_welcome_email_body_1"], $editor_id, $settings );
                    ?>
                </div>
                <div class="body" >
                    <label >Body Copy #2</label>
                    <?php 
                    $settings = [ 'textarea_name' => 'institute_family[institute_family_welcome_email_body_2]',"tinymce"=>$tinymce, "textarea_rows" => 5];
                    $editor_id = "institute_family_welcome_email_body_2";
                    wp_editor( $learndash_global_email["institute_family_welcome_email_body_2"], $editor_id, $settings );
                    ?>
                </div>

            </div>
        </div>
        <!-- : Institute Family Welcome Email-->

    </div>
</div>