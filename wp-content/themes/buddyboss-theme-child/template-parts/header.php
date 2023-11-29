<?php 
// Site Logo
$show		  = buddyboss_theme_get_option( 'logo_switch' );
$show_dark    = buddyboss_theme_get_option( 'logo_dark_switch' );
$logo_dark_id = buddyboss_theme_get_option( 'logo_dark', 'id' );
$logo_dark    = ( $show && $show_dark && $logo_dark_id ) ? wp_get_attachment_image( $logo_dark_id, 'full', '', array( 'class' => 'bb-logo bb-logo-dark' ) ) : '';
?>
<div class="container site-header-container flex default-header header-wrapper">
    <a href="#" class="bb-toggle-panel"><i class="bb-icon-l bb-icon-sidebar"></i></a>
    <?php

    // show institute logo if logged in user is an Institute Family Parent
    if( is_user_logged_in() ){
        $is_user_institute_parent = \Safar\SafarFamily::is_user_institute_parent();
        if($is_user_institute_parent){
            $institute = \Safar\SafarFamily::get_institute_by_parent_id(get_current_user_id());
            ?>
            <div class="institute-logo">
                <img src="<?=$institute["post"]->avatar?>" />
                <span><?=$institute["post"]->post_title?></span>
            </div>
            <?php
        }
    }

    if ( buddyboss_is_learndash_inner() && !buddyboss_theme_ld_focus_mode() ) {
        get_template_part( 'template-parts/site-logo' );
        get_template_part( 'template-parts/site-navigation' );
        do_shortcode('[header_left]');

    } elseif ( buddyboss_is_learndash_inner() && buddyboss_theme_ld_focus_mode() ) {
        
        if ( buddyboss_is_learndash_brand_logo() ) { ?>
        <div id="site-logo" class="site-branding">
            <div class="ld-brand-logo ld-focus-custom-logo site-title">
 
                <img src="<?=$logo?>" alt="<?php echo esc_attr(get_post_meta(buddyboss_is_learndash_brand_logo() , '_wp_attachment_image_alt', true)); ?>" class="bb-logo">
            
            </div>  
        </div>
        <?php } else {
            get_template_part( 'template-parts/site-logo' );   
        }
    } elseif ( !buddyboss_is_learndash_inner() ) {
        get_template_part( 'template-parts/site-logo' );
        get_template_part( 'template-parts/site-navigation' );
    }
    ?>

	<?php 
    
    get_template_part( 'template-parts/header-aside' ); 
    ?>

    
</div>