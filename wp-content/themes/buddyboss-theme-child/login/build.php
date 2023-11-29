<?php 
add_action( 'login_head', function(){
    $rx_admin_login_background_switch   = buddyboss_theme_get_option( 'admin_login_background_switch' );
    $rx_admin_login_background_text     = buddyboss_theme_get_option( 'admin_login_background_text' );
    $rx_admin_login_background_textarea = buddyboss_theme_get_option( 'admin_login_background_textarea' );
    $rx_admin_login_heading_color       = buddyboss_theme_get_option( 'admin_login_heading_color' );
    $rx_admin_login_overlay_opacity     = buddyboss_theme_get_option( 'admin_login_overlay_opacity' );


    $rx_logoimg                                     = buddyboss_theme_get_option( 'admin_logo_media' );
 

    if ( $rx_admin_login_background_switch ) {
        echo '<div class="login-welcome"><div class="login-split__entry">';

        ?>
        <img src="<?=$rx_logoimg["url"]?>" class="login-logo"/>
        <?php

    
        if ( $rx_admin_login_background_text ) {
            echo '<h1>';
            echo wp_kses_post( sprintf( esc_html__( '%s', 'buddyboss-theme' ), $rx_admin_login_background_text ) );
            echo '</h1>';
        }
        if ( $rx_admin_login_background_textarea ) {
            echo '<p >';
            echo nl2br( $rx_admin_login_background_textarea );
            echo '</p>';
        }
        echo '</div><div class="split-overlay"></div></div>';
    }

    

    $rx_logowidth                                   = buddyboss_theme_get_option( 'admin_logo_width' );
    $rx_login_background_media                      = buddyboss_theme_get_option( 'admin_login_background_media' );
    $rx_success_color                               = buddyboss_theme_get_option( 'success_notice_bg_color' );
    $rx_warning_color                               = buddyboss_theme_get_option( 'warning_notice_bg_color' );
    $buddyboss_custom_font                          = buddyboss_theme_get_option( 'custom_typography' );
    $buddyboss_body_font                            = buddyboss_theme_get_option( 'boss_body_font_family' );
    $buddyboss_h1_font                              = buddyboss_theme_get_option( 'boss_h1_font_options' );
    $buddyboss_h2_font                              = buddyboss_theme_get_option( 'boss_h2_font_options' );
    $primary_color                                  = buddyboss_theme_get_option( 'accent_color' );
    $body_background                                = buddyboss_theme_get_option( 'body_background' );
    $body_blocks                                    = buddyboss_theme_get_option( 'body_blocks' );
    $light_background_blocks                        = buddyboss_theme_get_option( 'light_background_blocks' );
    $body_blocks_border                             = buddyboss_theme_get_option( 'body_blocks_border' );
    $buddyboss_theme_group_cover_bg                 = buddyboss_theme_get_option( 'buddyboss_theme_group_cover_bg' );
    $heading_text_color                             = buddyboss_theme_get_option( 'heading_text_color' );
    $body_text_color                                = buddyboss_theme_get_option( 'body_text_color' );
    $alternate_text_color                           = buddyboss_theme_get_option( 'alternate_text_color' );
    $admin_screen_bgr_color                         = buddyboss_theme_get_option( 'admin_screen_bgr_color' );
    $admin_screen_txt_color                         = buddyboss_theme_get_option( 'admin_screen_txt_color' );

    $primary_button_background_regular              = buddyboss_theme_get_option( 'primary_button_background' )['regular'];
    $primary_button_background_hover                = buddyboss_theme_get_option( 'primary_button_background' )['hover'];
    $primary_button_border_regular                  = buddyboss_theme_get_option( 'primary_button_border' )['regular'];
    $primary_button_border_hover                    = buddyboss_theme_get_option( 'primary_button_border' )['hover'];
    $primary_button_text_color_regular              = buddyboss_theme_get_option( 'primary_button_text_color' )['regular'];
    $primary_button_text_color_hover                = buddyboss_theme_get_option( 'primary_button_text_color' )['hover'];
    $secondary_button_background_regular            = buddyboss_theme_get_option( 'secondary_button_background' )['regular'];
    $secondary_button_background_hover              = buddyboss_theme_get_option( 'secondary_button_background' )['hover'];
    $secondary_button_border_regular                = buddyboss_theme_get_option( 'secondary_button_border' )['regular'];
    $secondary_button_border_hover                  = buddyboss_theme_get_option( 'secondary_button_border' )['hover'];
    $secondary_button_text_color_regular            = buddyboss_theme_get_option( 'secondary_button_text_color' )['regular'];
    $secondary_button_text_color_hover              = buddyboss_theme_get_option( 'secondary_button_text_color' )['hover'];

    $login_register_link_color_regular              = buddyboss_theme_get_option( 'login_register_link_color' )['regular'];
    $login_register_link_color_hover                = buddyboss_theme_get_option( 'login_register_link_color' )['hover'];
    $login_register_button_background_color_regular = buddyboss_theme_get_option( 'login_register_button_background_color' )['regular'];
    $login_register_button_background_color_hover   = buddyboss_theme_get_option( 'login_register_button_background_color' )['hover'];
    $login_register_button_border_color_regular     = buddyboss_theme_get_option( 'login_register_button_border_color' )['regular'];
    $login_register_button_border_color_hover       = buddyboss_theme_get_option( 'login_register_button_border_color' )['hover'];
    $login_register_button_text_color_regular       = buddyboss_theme_get_option( 'login_register_button_text_color' )['regular'];
    $login_register_button_text_color_hover         = buddyboss_theme_get_option( 'login_register_button_text_color' )['hover'];

    $default_notice_color                           = buddyboss_theme_get_option( 'default_notice_bg_color' );
    $success_color                                  = buddyboss_theme_get_option( 'success_notice_bg_color' );
    $warning_color                                  = buddyboss_theme_get_option( 'warning_notice_bg_color' );
    $danger_color                                   = buddyboss_theme_get_option( 'error_notice_bg_color' );

    $button_radius                                  = buddyboss_theme_get_option( 'button_default_radius' );
    $theme_style                                    = buddyboss_theme_get_option( 'theme_template' );

    echo '<style>';
    ?>
    :root{
        --bb-primary-color: <?php echo $primary_color; ?>;
        --bb-primary-color-rgb: <?php echo join( ', ', hex_2_RGB( $primary_color ) ); ?>;
        --bb-body-background-color: <?php echo $body_background; ?>;
        --bb-content-background-color: <?php echo $body_blocks; ?>;
        --bb-content-alternate-background-color: <?php echo $light_background_blocks; ?>;
        --bb-content-border-color: <?php echo $body_blocks_border; ?>;
        --bb-content-border-color-rgb: <?php echo join( ', ', hex_2_RGB( $body_blocks_border ) ); ?>;
        --bb-cover-image-background-color: <?php echo $buddyboss_theme_group_cover_bg; ?>;
        --bb-headings-color: <?php echo $heading_text_color; ?>;
        --bb-body-text-color: <?php echo $body_text_color; ?>;
        --bb-alternate-text-color: <?php echo $alternate_text_color; ?>;
        --bb-alternate-text-color-rgb: <?php echo join( ', ', hex_2_RGB( $alternate_text_color ) ); ?>;

        --bb-primary-button-background-regular: <?php echo $primary_button_background_regular; ?>;
        --bb-primary-button-background-hover: <?php echo $primary_button_background_hover; ?>;
        --bb-primary-button-border-regular: <?php echo $primary_button_border_regular; ?>;
        --bb-primary-button-border-hover: <?php echo $primary_button_border_hover; ?>;
        --bb-primary-button-border-hover-rgb: <?php echo join( ', ', hex_2_RGB( $primary_button_border_hover ) ); ?>;
        --bb-primary-button-text-regular: <?php echo $primary_button_text_color_regular; ?>;
        --bb-primary-button-text-regular-rgb: <?php echo join( ', ', hex_2_RGB( $primary_button_text_color_regular ) ); ?>;
        --bb-primary-button-text-hover: <?php echo $primary_button_text_color_hover; ?>;
        --bb-primary-button-text-hover-rgb: <?php echo join( ', ', hex_2_RGB( $primary_button_text_color_hover ) ); ?>;
        --bb-secondary-button-background-regular: <?php echo $secondary_button_background_regular; ?>;
        --bb-secondary-button-background-hover: <?php echo $secondary_button_background_hover; ?>;
        --bb-secondary-button-border-regular: <?php echo $secondary_button_border_regular; ?>;
        --bb-secondary-button-border-hover: <?php echo $secondary_button_border_hover; ?>;
        --bb-secondary-button-border-hover-rgb:  <?php echo join( ', ', hex_2_RGB( $secondary_button_border_hover ) ); ?>;
        --bb-secondary-button-text-regular: <?php echo $secondary_button_text_color_regular; ?>;
        --bb-secondary-button-text-hover: <?php echo $secondary_button_text_color_hover; ?>;

        --bb-admin-screen-bgr-color: <?php echo $admin_screen_bgr_color; ?>;
        --bb-admin-screen-txt-color: <?php echo $admin_screen_txt_color; ?>;
        --bb-login-register-link-color-regular: <?php echo $login_register_link_color_regular; ?>;
        --bb-login-register-link-color-hover: <?php echo $login_register_link_color_hover; ?>;
        --bb-login-register-button-background-color-regular: <?php echo $login_register_button_background_color_regular; ?>;
        --bb-login-register-button-background-color-hover: <?php echo $login_register_button_background_color_hover; ?>;
        --bb-login-register-button-border-color-regular: <?php echo $login_register_button_border_color_regular; ?>;
        --bb-login-register-button-border-color-hover: <?php echo $login_register_button_border_color_hover; ?>;
        --bb-login-register-button-text-color-regular: <?php echo $login_register_button_text_color_regular; ?>;
        --bb-login-register-button-text-color-hover: <?php echo $login_register_button_text_color_hover; ?>;

        --bb-default-notice-color: <?php echo $default_notice_color; ?>;
        --bb-default-notice-color-rgb: <?php echo join( ', ', hex_2_RGB( $default_notice_color ) ); ?>;
        --bb-success-color: <?php echo $success_color; ?>;
        --bb-success-color-rgb: <?php echo join( ', ', hex_2_RGB( $success_color ) ); ?>;
        --bb-warning-color: <?php echo $warning_color; ?>;
        --bb-warning-color-rgb: <?php echo join( ', ', hex_2_RGB( $warning_color ) ); ?>;
        --bb-danger-color: <?php echo $danger_color; ?>;
        --bb-danger-color-rgb: <?php echo join( ', ', hex_2_RGB( $danger_color ) ); ?>;

        --bb-login-custom-heading-color: <?php echo $rx_admin_login_heading_color; ?>;

        --bb-button-radius: <?php echo $button_radius; ?>px;

        <?php
        if ( ! isset( $theme_style ) ) {
            $theme_style = '1';
        }
        ?>

        <?php if ( '1' === $theme_style ) { ?>
            --bb-block-radius: 4px;
            --bb-block-radius-inner: 4px;
            --bb-input-radius: 4px;
            --bb-checkbox-radius: 2.7px;
            --bb-primary-button-focus-shadow: none;
            --bb-secondary-button-focus-shadow: none;
            --bb-outline-button-focus-shadow: none;
            --bb-input-focus-shadow: none;
            --bb-input-focus-border-color: var(--bb-content-border-color);
        <?php } else { ?>
            --bb-block-radius: 10px;
            --bb-block-radius-inner: 6px;
            --bb-input-radius: 6px;
            --bb-checkbox-radius: 5.4px;
            --bb-primary-button-focus-shadow: 0px 0px 0px 2px rgba(var(--bb-primary-button-border-hover-rgb), 0.1);
            --bb-secondary-button-focus-shadow: 0px 0px 0px 2px rgba(var(--bb-secondary-button-border-hover-rgb), 0.1);
            --bb-outline-button-focus-shadow: 0px 0px 0px 2px rgba(var(--bb-content-border-color-rgb), 0.1);
            --bb-input-focus-shadow: 0px 0px 0px 2px rgba(var(--bb-primary-color-rgb), 0.1);
            --bb-input-focus-border-color: var(--bb-primary-color);
        <?php } ?>
    }
    <?php
    if ( '1' == $buddyboss_custom_font ) {
        if ( ! empty( $buddyboss_body_font['font-family'] ) ) {
            ?>
            body, body.rtl {
            font-family: <?php echo $buddyboss_body_font['font-family']; ?>
            }
            <?php
        }

        if ( ! empty( $buddyboss_h1_font['font-family'] ) ) {
            ?>
            h1, .rtl h1 {
            font-family: <?php echo $buddyboss_h1_font['font-family']; ?>
            }
            <?php
        }

        if ( ! empty( $buddyboss_h2_font['font-family'] ) ) {
            ?>
            h2, .rtl h2 {
            font-family: <?php echo $buddyboss_h2_font['font-family']; ?>
            }
            <?php
        }
    }

    if ( ! empty( $rx_logoimg['url'] ) ) {
        ?>
        .login h1 a {
        background-image: url(<?php echo $rx_logoimg['url']; ?>);
        background-size: contain;
        <?php
        if ( $rx_logowidth ) {
            echo 'width:' . $rx_logowidth . 'px;';
        }
        ?>
        }

        .login #login h1 img.bs-cs-login-logo.private-on {
        <?php
        if ( $rx_logowidth ) {
            echo 'width:' . $rx_logowidth . 'px;';
        }
        ?>
        }
        <?php
    }
    if ( $rx_admin_login_background_switch && $rx_login_background_media ) {
        ?>
        .login-welcome {
        background-image: url(<?php echo $rx_login_background_media['url']; ?>);
        background-size: cover;
        background-position: bottom center;
        background-color: #5E59A5;
        background-repeat:no-repeat;
        }
        <?php
    }
    if ( $danger_color ) {
        ?>
        .login.bb-login #pass-strength-result.short,
        .login.bb-login #pass-strength-result.bad {
        background-color: <?php echo $danger_color; ?>;
        border-color: <?php echo $danger_color; ?>;
        }
        <?php
    }
    if ( $rx_success_color ) {
        ?>
        .login.bb-login #pass-strength-result.strong {
        background-color: <?php echo $rx_success_color; ?>;
        border-color: <?php echo $rx_success_color; ?>;
        }
        <?php
    }
    if ( $rx_warning_color ) {
        ?>
        .login.bb-login #pass-strength-result.good {
        background-color: <?php echo $rx_warning_color; ?>;
        border-color: <?php echo $rx_warning_color; ?>;
        }
        <?php
    }
    if ( $rx_admin_login_overlay_opacity ) {
        ?>
        body.login.login-split-page .login-split .split-overlay {
        opacity: <?php echo $rx_admin_login_overlay_opacity / 100; ?>;
        }
        <?php
    }

    echo '</style>';

 


    ?>
    <script type="text/javascript">
        jQuery(document).ready( $ => {
            $('.login-welcome, #login').wrapAll('<div class="login-wrapper" />');
            // $(".login-action-login #login").append( `<div class="account-sign-up">Don’t have an account? <a href="">Sign up</a></div>` );
            $('.login-welcome .login-split__entry h1,.login-welcome .login-split__entry p').wrapAll('<div class="login-welcome__content" />');
            $( `<div class="account-sign-up">Don’t have an account? <a href="https://journey2jannah.com/pricing/">Sign up</a></div>` ).insertBefore($('.privacy-policy-page-link'))
            $("#user_login").attr("placeholder","Username or email address")

            $(".login-action-checkemail #login .login-heading").prepend(`<svg width="104" height="120" viewBox="0 0 104 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M98.7124 55.569H5.28772C2.91536 55.569 0.992188 57.4921 0.992188 59.8645V115.704C0.992188 118.077 2.91536 120 5.28772 120H98.7124C101.085 120 103.008 118.077 103.008 115.704V59.8645C103.008 57.4921 101.085 55.569 98.7124 55.569Z" fill="#FFC007"/>
                    <path d="M103.008 59.4775V56.6637C102.982 55.291 102.317 54.0088 101.21 53.1965L57.3671 22.0681C54.1733 19.7236 49.8274 19.7236 46.6337 22.0681L2.79023 53.1384C1.68335 53.9507 1.01897 55.233 0.992188 56.6057V59.392L46.5897 91.1848C49.8096 93.6294 54.1912 93.6294 57.4111 91.1848L103.009 59.4775H103.008Z" fill="#E09705"/>
                    <path d="M103.008 56.6051H0.992188V59.3914L45.1914 96.7619L78.0088 95.5588L103.009 59.8243L103.008 56.6051Z" fill="#FFB000"/>
                    <path d="M97.8635 117.832C97.8635 118.827 97.0563 119.634 96.061 119.634H7.93922C6.94392 119.634 6.13672 118.827 6.13672 117.832V1.8025C6.13672 0.807205 6.94392 0 7.93922 0H73.2699L97.8635 24.5936V117.832Z" fill="#F8F7FF"/>
                    <path opacity="0.4" d="M73.2705 0L97.8571 24.5866H75.073C74.0777 24.5866 73.2705 23.7794 73.2705 22.7841V0Z" fill="#9B9B9B"/>
                    <path d="M52 70.7898C63.2684 70.7898 72.4033 61.655 72.4033 50.3865C72.4033 39.1181 63.2684 29.9833 52 29.9833C40.7315 29.9833 31.5967 39.1181 31.5967 50.3865C31.5967 61.655 40.7315 70.7898 52 70.7898Z" fill="#B0D178"/>
                    <path d="M48.0697 62.3805L36.9811 51.4628C36.4456 50.9355 36.4392 50.0748 36.9665 49.5392L39.8293 46.6317C40.3566 46.0961 41.2174 46.0897 41.753 46.617L48.0697 52.8369C48.5989 53.3584 49.4488 53.3584 49.978 52.8369L62.2468 40.7575C62.7824 40.2302 63.6431 40.2372 64.1704 40.7721L67.0333 43.6796C67.5606 44.2152 67.5536 45.076 67.0186 45.6033L49.978 62.3811C49.4488 62.9027 48.5989 62.9027 48.0697 62.3811V62.3805Z" fill="#FEFEFE"/>
                    <path d="M99.4493 57.9262L57.877 86.7682L101.551 118.927C102.444 118.139 103.008 116.988 103.008 115.704V59.8645C103.008 59.8511 103.008 59.8384 103.008 59.825C103.029 57.9791 100.966 56.8748 99.4486 57.9268L99.4493 57.9262Z" fill="#F9C648"/>
                    <path d="M4.52769 58.0136C3.04846 56.9908 1.00813 58.0269 0.992188 59.8256C0.992188 59.8384 0.992188 59.8518 0.992188 59.8645V115.705C0.992188 116.989 1.55647 118.141 2.44911 118.928L46.123 86.7688L4.52769 58.0136Z" fill="#F9C648"/>
                    <path d="M46.6338 86.4997L2.45117 118.929C3.20801 119.595 4.20012 120 5.28723 120H98.7132C99.8003 120 100.793 119.595 101.549 118.929L57.3672 86.4991C54.1735 84.1546 49.8276 84.1546 46.6338 86.4991V86.4997Z" fill="#FCB932"/>
                    </svg>
            `);
            $(`<a href="<?=wp_login_url()?>" class="back-to-signin">Back to sign in</a>`).insertAfter(".login-action-checkemail #login p.message");
            $(".login-action-checkemail #login .login-heading h2").html("Check your email");

            $(`
                <div class="reset-password-error ">
                    <svg width="130" height="120" viewBox="0 0 130 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23.2134 120C15.2009 120 7.99062 115.878 3.92803 108.973C-0.13655 102.068 -0.239312 93.7638 3.65068 86.759L45.4379 11.5133C49.3807 4.41178 56.8764 0 65.0005 0C73.1247 0 80.6184 4.41178 84.5622 11.5133L126.349 86.76C130.239 93.7638 130.137 102.069 126.072 108.974C122.008 115.879 114.8 120.001 106.788 120.001H23.2134V120Z" fill="#DF8781"/>
                    <path d="M14.2636 92.6533L56.0508 17.4077C59.9508 10.3859 70.0484 10.3859 73.9483 17.4077L115.736 92.6543C119.525 99.4775 114.591 107.86 106.786 107.86H23.2119C15.408 107.86 10.4744 99.4765 14.2626 92.6543L14.2636 92.6533Z" fill="white"/>
                    <g style="mix-blend-mode:multiply" opacity="0.21">
                    <path d="M95.9091 56.9501C90.0446 62.8145 84.1792 68.6799 78.3148 74.5443C67.2095 85.6496 56.1053 96.7539 45 107.859H106.788C114.592 107.859 119.525 99.4756 115.737 92.6534L95.9091 56.9501Z" fill="#D3D2D7"/>
                    </g>
                    <path d="M58.3049 77.1094L55.6221 47.4192V46.8594C55.6221 45.4367 56.7754 44.2834 58.1981 44.2834H71.8006C73.2233 44.2834 74.3766 45.4367 74.3766 46.8594V47.4192L71.635 77.1134C71.5182 78.3794 70.4567 79.3472 69.1857 79.3472H60.7552C59.4821 79.3472 58.4196 78.3764 58.3049 77.1084V77.1094ZM57.8798 91.1079V84.7756C57.8798 83.6831 58.7648 82.7982 59.8573 82.7982H70.0786C71.171 82.7982 72.056 83.6831 72.056 84.7756V91.1079C72.056 92.2004 71.171 93.0854 70.0786 93.0854H59.8573C58.7648 93.0854 57.8798 92.2004 57.8798 91.1079Z" fill="#DF8781"/>
                    </svg>
                    <h2>Ooops!</h2>
                    <p>Your reset password link is no longer valid. Please click the button below to obtain a new reset password link.</p>
                    <a href="<?=wp_login_url()."?action=lostpassword"?>" class="back-to-signin">Forgot Password</a>
                </div>
            `).insertAfter("#lostpasswordform");

            if($(".login-action-lostpassword #login_error").is(":visible")){
                $(".login-action-lostpassword").addClass("lost-password-error")
            }else{
                $(".login-action-lostpassword").removeClass("lost-password-error")
            }

            jQuery('#login').children().not('.privacy-policy-page-link').wrapAll("<div class='signin-wrapper'></div>");

            <?php 
            if(isset($_GET["action"])){
                if($_GET["action"] == "rp"){
                    
                    ?>
                    setTimeout( () => {
                        let passOne = $("#pass1").val();
                        $("#bs-pass2").val(passOne);
                    }, 1000)


                    checkPwdmatch = () => {
                        let pwd1 = $("#pass1").val();
                        let pwd2 = $("#bs-pass2").val();

                        if( (pwd1 == pwd2) && pwd1.length > 0 && pwd2.length > 0 ){
                            jQuery(".pwd-indicator").html('Password match');
                            jQuery(".pwd-indicator").removeClass("mismatch").addClass("match");
                        }else{
                            
                            if(pwd1.length > 0 || pwd2.length > 0 &&  (pwd1 != pwd2)){
                                jQuery(".pwd-indicator").html('Password mismatch');
                                jQuery(".pwd-indicator").removeClass("match").addClass("mismatch");
                            }
                        }
                    }
                    jQuery(document).on("keyup","#pass1, #bs-pass2", e => {
                        checkPwdmatch();
                    })
                    jQuery(document).on("change","#pass1, #bs-pass2", e => {
                        checkPwdmatch();
                    })

                    jQuery(document).on("click","button.wp-generate-pw",e => {
                        console.log("wp-generate-pw")
                        setTimeout( () => {
                            passOne = $("#pass1").val();
                            $("#bs-pass2").val(passOne);
                            checkPwdmatch();
                        }, 100)
                    });

                    jQuery("#resetpassform #user_login").attr({"type":"text","readonly":"readonly"}).css({"background-color":"#e7e7e7"})
                    jQuery('<div class="label">Password</div>').insertBefore('#resetpassform #pass1');
                    jQuery('<div class="label">Confirm Password</div>').insertBefore('#resetpassform #bs-pass2');
                    jQuery('#user_login').wrap('<div class="user-email-wrap"></div>');
                    jQuery('<div class="label">Email</div>').insertBefore('#resetpassform #user_login');


                    var imgElement = $('<img class="lock" src="/wp-content/themes/buddyboss-theme-child/assets/img/family-onboarding/lock.svg"/>');
                    jQuery('.user-email-wrap, .user-pass1-wrap, .user-bs-pass2-wrap').append(imgElement);

                    var buttonViewPwd = $('<button type="button" class="btn-view-pwd"></button>');
                    jQuery('.user-pass1-wrap, .user-bs-pass2-wrap').append(buttonViewPwd);

                    jQuery("#resetpassform .wp-pwd .wp-hide-pw, #resetpassform .wp-pwd #pass-strength-result").remove();

                    jQuery(document).on("click",".btn-view-pwd", e => {
                        jQuery(e.currentTarget).toggleClass("hide-password")
                        
                        if( jQuery(e.currentTarget).hasClass("hide-password")){
                            jQuery(e.currentTarget).parent().find("input[type=password]").attr("type","text");
                        }else{
                            jQuery(e.currentTarget).parent().find("input[type=text]").attr("type","password");
                        }
                    })

                    jQuery(".user-bs-pass2-wrap").append("<div class='pwd-indicator'></div>");

                    
                    
                    <?php
                }
            }
            ?>

        })
    </script>
    <?php
}, 9999 );
?>