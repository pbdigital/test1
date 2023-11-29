<?php 
wp_register_script('family-onboarding-js', get_stylesheet_directory_uri() . '/assets/js/family-onboarding.js', '', ENQUEUE_VERSION, true);
wp_register_style('family-onboarding-css', get_stylesheet_directory_uri() . '/assets/css/family-onboarding.css', '', ENQUEUE_VERSION );
wp_register_script('pikaday-js','//cdn.jsdelivr.net/npm/pikaday/pikaday.js', '', ENQUEUE_VERSION,true  );
wp_register_style('pikaday-css','https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css', '', ENQUEUE_VERSION );

add_shortcode("family_onboarding_child_information", function(){
    ob_start();
    $group_id = $_GET["gid"];
    require("child-information.php");
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
});

add_action("family-child-add", function($data){
    require("child-add.php");
});

// 2 = GF family onboarding form
add_action( 'gform_after_submission_2', function($entry, $form){
    
    /* 
    [id] => 87
    [status] => active
    [form_id] => 2
    [ip] => 180.195.213.183
    [source_url] => https://staging.journey2jannah.com/family-onboarding/?gid=222881
    [currency] => USD
    [post_id] => 
    [date_created] => 2023-03-10 05:25:15
    [date_updated] => 2023-03-10 05:25:15
    [is_starred] => 0
    [is_read] => 0
    [user_agent] => Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36
    [payment_status] => 
    [payment_date] => 
    [payment_amount] => 
    [payment_method] => 
    [transaction_id] => 
    [is_fulfilled] => 
    [created_by] => 8503
    [transaction_type] => 
    [69] => New Smith Family
    [71] => ParentName
    [72] => parentname@pbd.com
    [77] => 12312312312
    */

 
    $params = [];
    $params["family_name"] = $entry[69];
    $params["parent_name"] = $entry[71];
    $params["parent_email"] = $entry[72];
    $params["parent_phone"] = $entry[77];
    $params["facial_features"] = $entry[84];
    //$params["password"] = $entry[91];
    $params["gid"] = $entry[85];
    $params["parent_email"] = $entry[90];


	$request   = new \WP_REST_Request( 'POST', );
	$request->set_query_params($params);
	$response = \Safar\SafarFamily::update_family($request);
 

}, 10, 2 );

?>