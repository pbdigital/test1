<?php
/** Template Name: Choose Avatar  */

wp_enqueue_script('choose-avatar-js');
wp_enqueue_style('choose-avatar-css');
wp_enqueue_script('html2canvas');
wp_enqueue_script('canvg');

$ld_group_ids = learndash_get_users_group_ids( get_current_user_id() );

$has_facial_features = true;
foreach($ld_group_ids as $gid){
    $facial_features = get_post_meta($gid,"facial_features",true);
    if($facial_features == "Without facial features") $has_facial_features = false;
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
        do_action( "choose-avatar-main", get_the_id() );
        ?>

    </main><!-- #main -->
    <?php
get_footer(); 
?>
