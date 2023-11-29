<?php 
wp_register_script('admin-institute-onboarding-js', get_stylesheet_directory_uri() . '/assets/js/admin-institute-onboarding.js', '', uniqid(), true);
wp_register_style('admin-institute-onboarding-css', get_stylesheet_directory_uri() . '/assets/css/admin-institute-onboarding.css', '', uniqid() );


add_action("admin-institute-onboarding-main", function($page_id){
    require("parts/main.php");
});

add_action("admin-institute-onboarding-color-tabs", function($page_id){
    require("parts/color-tabs.php");
});

add_action("admin-institute-onboarding-gender-select-tabs", function($page_id){
    require("parts/gender-select.php");
});

add_action("admin-institute-onboarding-set-password", function($page_id){
    require("parts/set-password.php");
});

add_action("admin-institute-onboarding-please-wait", function($page_id){
    require("parts/please-wait.php");
});

?>