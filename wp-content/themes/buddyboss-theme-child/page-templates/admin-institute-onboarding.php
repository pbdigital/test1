<?php
/** Template Name: Admin Institute Onboarding  */

wp_enqueue_script('admin-institute-onboarding-js');
wp_enqueue_style('admin-institute-onboarding-css');
wp_enqueue_script('html2canvas');
wp_enqueue_script('canvg');

$ld_group_ids = learndash_get_users_group_ids( get_current_user_id() );

$is_user_institute_parent = \Safar\SafarUser::is_user_institute_parent();
$has_facial_features = true;

if($is_user_institute_parent){
    $ld_group_ids = \Safar\SafarFamily::get_institute_by_parent_id( get_current_user_id() );
    //print_r(["is_user_institute_parent"=>$is_user_institute_parent, "ld_group_ids"=>$ld_group_ids["meta"]["school_onboarding_facial_features"][0]]);
    $facial_features = $ld_group_ids["meta"]["school_onboarding_facial_features"][0];
    if($facial_features == "Without facial features") $has_facial_features = false;
}else{
    
    foreach($ld_group_ids as $gid){
        $facial_features = get_post_meta($gid,"facial_features",true);
        if($facial_features == "Without facial features") $has_facial_features = false;
    }
}

if(!$has_facial_features){
    function add_custom_body_class($classes) {
        $classes[] = 'group-no-facial-feature';
        return $classes;
    }
    add_filter('body_class', 'add_custom_body_class');
}


get_header();
    ?>
    <main id="main" class="site-main">

        <?php 
        do_action( "admin-institute-onboarding-main", get_the_id() );
        ?>

    </main><!-- #main -->
    <?php
get_footer(); 
?>
