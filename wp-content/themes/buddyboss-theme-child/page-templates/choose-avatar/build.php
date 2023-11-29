<?php 
wp_register_script('choose-avatar-js', get_stylesheet_directory_uri() . '/assets/js/choose-avatar.js', '', uniqid(), true);
wp_register_style('choose-avatar-css', get_stylesheet_directory_uri() . '/assets/css/choose-avatar.css', '', uniqid() );


add_action("choose-avatar-main", function($page_id){
    require("parts/main.php");
});

add_action("choose-avatar-age-group", function($page_id){
    require("parts/age-group.php");
});

add_action("choose-avatar-color-tabs", function($page_id){
    require("parts/color-tabs.php");
});

add_action("choose-avatar-gender-select-tabs", function($page_id){
    require("parts/gender-select.php");
});

?>