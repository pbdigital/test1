<?php 
/** Template Name: School Onboarding */

wp_enqueue_script('school-onboarding-js');
wp_enqueue_style('school-onboarding-css');
wp_enqueue_script('dropzone-js');
wp_enqueue_style('dropzone-css');

$order_meta = get_post_meta($_GET["gid"],"order_meta", true);
if(!empty($order_meta)){
    $order_meta = json_decode($order_meta);
}
$args = [
    "gid" => $_GET["gid"],
    "order_meta" => $order_meta
];

wp_localize_script('school-onboarding-js', 'j2jSchoolOnboarding', $args);

get_header();
$user = wp_get_current_user();
delete_user_meta(get_current_user_id(),"selected_institute");
?>
<main id="main" class="site-main">
    
    <div class="school-onboarding-container">
        
        <?=do_shortcode('[gravityform id="1" title="false" ajax="true"]')?>
     
    </div>
</main>


<div id="send-test-email-modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">          
        <button class="close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6 6L18 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

        </button>
        <?=do_action("school_onboarding_test_email")?>
    </div>

</div>

<div id="preview-email-modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">          
        <button class="close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6 6L18 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

        </button>

        <div class="email-template"></div>
        
    </div>

</div>

<?php 


get_footer();

?>