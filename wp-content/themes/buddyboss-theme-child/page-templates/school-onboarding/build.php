<?php
wp_register_script('school-onboarding-js', get_stylesheet_directory_uri() . '/assets/js/school-onboarding.js', '', ENQUEUE_VERSION, true);
wp_register_style('school-onboarding-css', get_stylesheet_directory_uri() . '/assets/css/school-onboarding.css', '', ENQUEUE_VERSION );
wp_register_script('dropzone-js', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js', '', ENQUEUE_VERSION );
wp_register_style('dropzone-css','https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/basic.css', '', ENQUEUE_VERSION );

add_action( 'gform_previous_button', 'custom_footer', 10 );
function custom_footer( $button ) {

    $prev = '
    <button class="btn-custom btn-prev">
        <svg width="36" height="32" viewBox="0 0 36 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 4C0 1.79086 1.79086 0 4 0H36V32H4C1.79086 32 0 30.2091 0 28V4Z" fill="#F2A952"/>
            <path d="M21 10L15 16L21 22" fill="#F2A952"/>
            <path d="M21 10L15 16L21 22" stroke="white" stroke-width="2" stroke-linejoin="round"/>
        </svg>
    </button>
    ';
    $next = '
    <button class="btn-custom btn-next">
        <svg width="36" height="32" viewBox="0 0 36 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M36 4C36 1.79086 34.2091 0 32 0H0V32H32C34.2091 32 36 30.2091 36 28V4Z" fill="#F2A952"/>
            <path d="M15 22L21 16L15 10" stroke="white" stroke-width="2" stroke-linejoin="round"/>
        </svg>
    </button>

    ';
    return $button."<div class='gf-custom-navigation'>".$prev.$next."</div> ";
}

add_shortcode('custom_gf_logo_upload', function(){
    ob_start();
    require_once("logo_upload.php");
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}); 

add_shortcode('gf_facial_features', function(){
    ob_start();
    require_once("facial_features.php");
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}); 


add_shortcode("custom_gf_welcome_email", function(){
    ob_start();
    require_once("welcome_email.php");
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
});

add_shortcode("custom_gf_add_teacher", function(){
    ob_start();
    require_once("add_teacher.php");
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
});

add_action( 'school_onboarding_test_email', function(){
    ?>
    <h2>Send Test Email</h2>
    <p>Enter the email address where you want to send the test welcome email</p>

    <div>
        <label>Email</label>
        <form class="frm-send-test-email"><input required class="input-test-email" type="email" /></form>
    </div>

    <button type="button" class="btn-send-email">Send Now</button>
    <?php
}, 10 );


// 1 = GF School Onboarding form
add_action( 'gform_after_submission_1', function($entry, $form){

	/* 
	[8] => PBD Institute Name
	[40] => 221726
	[12.1] => address line1
	[12.2] => addressline
	[12.3] => city
	[12.4] => state
	[12.5] => 12312
	[12.6] => Australia
	[16] => Contact name
	[31] => emailaddress@test.com
	[18] => 1122312
	[34] => With facial features
	[41] => Welcome to Journey2Jannah
	[43] => Welcome to Journey2Jannah
	[42] => teacher body
	[44] => student body

    [46] => members  // Activity Feeds(Required) activity_feed_status
    [47] => mods  //Group Photos(Required) media_status
    [48] => mods // Group Documents(Required) document_status
    [49] => mods // //Group Videos(Required) video_status
	*/
  
	$params = [];
	$params["school_name"] = $entry[8];
	$params["logo_attachment_id"] = $entry[40];

	$params["address_line1"] = $entry["12.1"];
	$params["address_line2"] = $entry["12.2"];
	$params["city"] = $entry["12.3"];
	$params["state"] = $entry["12.4"];
	$params["zip_postal"] = $entry["12.5"];
	$params["country"] = $entry["12.6"];

	$params["contact_name"] = $entry[16];
	$params["contact_email_address"] = $entry[31];
	$params["contact_phone"] = $entry[18];

	$params["with_facial_features"] = $entry[34];

	$params["teacher_welcome_subject"] = $entry[41];
	$params["student_welcome_subject"] = $entry[43];
	$params["teacher_welcome_body"] = $entry[42];
	$params["student_welcome_body"] = $entry[44];


    $params["activity_feed_status"] = $entry[46]; 
	$params["media_status"] = $entry[47];
	$params["document_status"] = $entry[48];
	$params["video_status"] = $entry[49];

    #$params["password"] = $entry[68];

	$request   = new \WP_REST_Request( 'POST', );
	$request->set_query_params($params);
	$response = \Safar\SafarSchool::update_school($request);

    // update catch all group and teachers group after onboarding
    $school_resp = \Safar\SafarSchool::get_user_school_id(get_current_user_id());
    $learndash_parent_group_id = $school_resp["learndash_parent_group_id"];
    $catch_all_group_id = $school_resp["catch_all_group_id"];
    $teacher_group_id = $school_resp["teacher_group_id"];


    $post_data = array(
        'ID'         => $teacher_group_id,
        'post_title' => $params["school_name"]." Teachers",
    );
    $updated_post_id = wp_update_post($post_data);

    $post_data = array(
        'ID'         => $catch_all_group_id,
        'post_title' => $params["school_name"]." Entire School",
    );
    $updated_post_id = wp_update_post($post_data);


}, 10, 2 );
?>