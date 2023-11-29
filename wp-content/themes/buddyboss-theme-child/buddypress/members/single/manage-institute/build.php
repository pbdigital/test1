<?php 
add_action("manage_institute_main", function(){
    require("main.php");
});

add_action("manage_institute_information", function(){
    require("institute-information.php");
});
add_action("manage_institute_activity_feed", function(){
    require("activity-feed.php");
});

add_action("manage_institute_welcome_email_template", function(){
    require("welcome-email-template.php");
});
?>