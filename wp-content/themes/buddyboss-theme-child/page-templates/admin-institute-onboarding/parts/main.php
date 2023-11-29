<?php 
$buddypanel      = buddyboss_theme_get_option( 'buddypanel' );
$show            = buddyboss_theme_get_option( 'logo_switch' );
$show_dark       = buddyboss_theme_get_option( 'logo_dark_switch' );
$logo_id         = buddyboss_theme_get_option( 'logo', 'id' );
$logo_dark_id    = buddyboss_theme_get_option( 'logo_dark', 'id' );
$buddypanel_logo = buddyboss_theme_get_option( 'buddypanel_show_logo' );
?>

<div class="choose-avatar">
    
    <div class="choose-avatar-inner <?=(($_GET["tab"] == "color") ? "wide":"")?>">
        <?=do_action("admin-institute-onboarding-set-password")?>
        <?=do_action("admin-institute-onboarding-gender-select-tabs")?>
        <?=do_action("admin-institute-onboarding-color-tabs")?>
        <?=do_action("admin-institute-onboarding-please-wait")?>
    </div>
</div>