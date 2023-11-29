<?php 
/** Template Name: No Access */

wp_enqueue_style('no-access', get_stylesheet_directory_uri() . '/assets/css/no-access.css', '', ENQUEUE_VERSION);
get_header();


?>
<main id="main" class="site-main <?=$post_slug?>">
    
    <h1>Oops!</h1>
    <section class="main-content">
        
    </section>
</main>


<?php 

get_footer();

?>