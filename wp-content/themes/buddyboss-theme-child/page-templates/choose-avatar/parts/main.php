<?php 
$buddypanel      = buddyboss_theme_get_option( 'buddypanel' );
$show            = buddyboss_theme_get_option( 'logo_switch' );
$show_dark       = buddyboss_theme_get_option( 'logo_dark_switch' );
$logo_id         = buddyboss_theme_get_option( 'logo', 'id' );
$logo_dark_id    = buddyboss_theme_get_option( 'logo_dark', 'id' );
$buddypanel_logo = buddyboss_theme_get_option( 'buddypanel_show_logo' );
$logo            = ( $show && $logo_id ) ? wp_get_attachment_image( $logo_id, 'full', '', array( 'class' => 'bb-logo' ) ) : get_bloginfo( 'name' );
$logo_dark       = ( $show && $show_dark && $logo_dark_id ) ? wp_get_attachment_image( $logo_dark_id, 'full', '', array( 'class' => 'bb-logo bb-logo-dark' ) ) : '';

?>
<div class="choose-avatar-logo">
    <?=$logo?>
</div>
<div class="choose-avatar">
    <div class="choose-avatar-title">
        <div class="choose-avatar-title-label">Choose Your Avatar</div>
    </div>
    <div class="choose-avatar-inner">
        <?=do_action("choose-avatar-gender-select-tabs")?>
        <?=do_action("choose-avatar-color-tabs")?>
    </div>
</div>