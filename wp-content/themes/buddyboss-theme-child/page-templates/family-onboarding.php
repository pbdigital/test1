<?php 
/** Template Name: Family Onboarding */

wp_enqueue_script('family-onboarding-js');
wp_enqueue_style('family-onboarding-css');
wp_enqueue_script('pikaday-js');
wp_enqueue_style('pikaday-css');

$order_meta = get_post_meta($_GET["gid"],"order_meta", true);
if(!empty($order_meta)){
    $order_meta = json_decode($order_meta);
}
$args = [
    "gid" => $_GET["gid"],
    "order_meta" => $order_meta
];

wp_localize_script('family-onboarding-js', 'j2jFamilyOnboarding', $args);

get_header();
$user = wp_get_current_user();?>
<main id="main" class="site-main">
    
    <div class="school-onboarding-container">
        
        <?=do_shortcode('[gravityform id="2" title="false" ajax="true"]')?>
     
    </div>
   
</main>

<div id="confirm-continue-setup" class="modal">

    <!-- Modal content -->
    <div class="modal-content">            
        <span class="close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6 6L18 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>

        <div class="confirm-content">

        </div>

    </div>

</div>

<?php 
get_footer();

?>