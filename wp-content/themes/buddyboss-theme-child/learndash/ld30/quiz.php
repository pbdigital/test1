<?php
/**
 * Displays a quiz.
 *
 * Available Variables:
 *
 * $course_id       : (int) ID of the course
 * $course      : (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 * $course_status   : Course Status
 * $has_access  : User has access to course or is enrolled.
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $lessons_options : Options/Settings as configured on Lessons Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id         : (object) Current User ID
 * $logged_in       : (true/false) User is logged in
 * $current_user    : (object) Currently logged in user object
 * $post            : (object) The quiz post object
 * $lesson_progression_enabled  : (true/false)
 * $show_content    : (true/false) true if user is logged in and lesson progression is disabled or if previous lesson and topic is completed.
 * $attempts_left   : (true/false)
 * $attempts_count : (integer) No of attempts already made
 * $quiz_settings   : (array)
 *
 * Note:
 *
 * To get lesson/topic post object under which the quiz is added:
 * $lesson_post = !empty($quiz_settings["lesson"])? get_post($quiz_settings["lesson"]):null;
 *
 * @since 2.1.0
 *
 * @package LearnDash\Quiz
 */
global $post;
if ( empty( $course_id ) ) {
	$course_id = buddyboss_theme()->learndash_helper()->ld_30_get_course_id( $post->ID );
}



//$content             = $post->post_content;
$lession_list        = learndash_get_lesson_list( $course_id, array('num' => -1 ) );
$course_quizzes_list = learndash_get_course_quiz_list( $course_id, $user_id );
$content_urls        = buddyboss_theme()->learndash_helper()->buddyboss_theme_ld_custom_pagination( $course_id, $lession_list, $course_quizzes_list );
$quiz_urls           = buddyboss_theme()->learndash_helper()->buddyboss_theme_ld_custom_quiz_count( $course_id, $lession_list, $course_quizzes_list );
$pagination_urls     = buddyboss_theme()->learndash_helper()->buddyboss_theme_custom_next_prev_url( $content_urls );
$current_quiz_ke     = buddyboss_theme()->learndash_helper()->buddyboss_theme_ld_custom_quiz_key( $quiz_urls );
$topics              = array();
$course              = get_post( $course_id );
$course_settings     = learndash_get_setting( $course );
if ( empty( $course ) ) {
	$course = get_post( $course_id );
}
$theme = get_field('background');


global $wpdb;

//$post = get_post_meta( get_the_id() );
$course_id = learndash_get_course_id(get_the_id());//get_post_meta( get_the_id(), "course_id", true );
$post_meta = get_post_meta($course_id);

$sql = "SELECT DISTINCT post_id, menu_order, meta_key FROM ".$wpdb->prefix."postmeta  as pm
                    INNER JOIN ".$wpdb->prefix."posts as p ON pm.post_id = p.ID
                    WHERE meta_key like 'learndash_group_enrolled_%'
                    AND post_id=".esc_sql($course_id);
$course_meta = $wpdb->get_results($sql);

if( isset($_GET["dashboard"]) ){
    $back_link = "/dashboard";
}else{
    $course_details = get_post($course_id);
    if($course_details->post_status=="publish") $back_link = get_permalink($course_id);
    else $back_link = site_url();
}


?>
<a href="<?=$back_link?><?=(isset($_GET["ldgid"])) ? "?ldgid=".$_GET["ldgid"]:""?>" class="btn-back" ><img src="<?=get_stylesheet_directory_uri();?>/assets/img/back.png" alt="Back to Course"></a>

<div id="learndash-content" class="container-full">


<div class="correct-ans" style="display:none;">
        <div class="correct-ans--inner">
            <div class="col">
                <h3><svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="25" cy="25" r="23" stroke="#fff" stroke-width="4"/><path d="M35.875 20.125c0 .359-.125.664-.377.915L23.916 32.623A1.247 1.247 0 0 1 23 33c-.36 0-.664-.126-.916-.377l-6.707-6.707A1.247 1.247 0 0 1 15 25c0-.36.126-.664.377-.916l1.832-1.831c.251-.252.556-.378.916-.378.359 0 .664.126.915.378L23 26.226l8.835-8.849c.251-.251.557-.377.916-.377s.664.126.916.377l1.831 1.832c.252.251.377.556.377.916Z" fill="#fff"/></svg> <span>correct</span></h3>
            </div>
            <div class="col">
                <h3><svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="25" cy="25" r="25" fill="#F2A952"/><path d="m36.692 20.907-7.494-1.09-3.35-6.792a.94.94 0 0 0-.429-.428.948.948 0 0 0-1.266.428l-3.35 6.792-7.495 1.09a.942.942 0 0 0-.522 1.611l5.422 5.287-1.281 7.465a.943.943 0 0 0 1.37.995L25 32.74l6.703 3.525a.943.943 0 0 0 1.37-.995l-1.28-7.465 5.421-5.287a.943.943 0 0 0 .275-.54.941.941 0 0 0-.797-1.071Z" fill="#fff"/></svg>
                <span class="correct-answer-multiplier">+ 0</span>
                </h3>
            </div>
            <!-- <div class="col">
                <button class="btn-correct btn-next">next</button>
            </div> -->
        </div>
    </div>

    <div class="wrong-ans" style="display:none;">
        <div class="wrong-ans--inner">
            <div class="wrong-ans--top">
                <div class="wrong-ans--cols">
                    <div class="col">
                        <h3>
                            <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="25" cy="25" r="23" stroke="#fff" stroke-width="4"/>
                            <path d="M33 18L18 33m0-15l15 15" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg> 
                            <span class="wrong-timesup-text">Oops, You’ve not got it right this time</span>
                        </h3>
                    </div>
                    <div class="col">
                        <h3 class="lose-streak">You lose your streak :(</h3>
                    </div>
                    <!-- <div class="col">
                        <button class="btn-wrong btn-next">next</button>
                    </div> -->
                </div>
            </div>
            <?php /*
            <div class="wrong-ans--bottom">
                <div class="wrong-ans--cols">
                    <div class="col"><p>Nuts is lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p></div>
                </div>
            </div>
            */?>
        </div>
    </div>

    <div class="bb-grid grid">
        
      

        <?php
        // if ( ! empty( $course ) ) :
	    //     include locate_template('/learndash/ld30/learndash-sidebar.php');
        // endif;
        ?>

        <div id="learndash-page-content" class="theme-<?=$theme?>">

            <div class="stars-wrapper">
                <?php if( $theme === "1" ): ?>
                    <img src="<?=get_stylesheet_directory_uri();?>/assets/img/stars1.svg" alt="Theme 1 Stars">
                <?php elseif ($theme === "5"): ?>
                    <img src="<?=get_stylesheet_directory_uri();?>/assets/img/stars5.svg" alt="Theme 5 Stars">
                <?php endif; ?>
                
            </div>
            <svg class="clouds-container"  viewBox="0 0 1237 316" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g class="clouds">
                <path opacity="0.05" d="M1224.77 305.628C1224.02 305.628 1223.29 305.712 1222.59 305.822C1222.64 305.157 1222.7 304.519 1222.7 303.853C1222.7 290.154 1211.26 279.034 1197.15 279.034C1186.02 279.034 1176.59 285.939 1173.09 295.589C1170.51 294.12 1167.51 293.26 1164.28 293.26C1158.2 293.26 1152.87 296.31 1149.75 300.886C1146.22 298.584 1141.99 297.226 1137.42 297.226C1136.04 297.226 1134.72 297.364 1133.43 297.586C1133.52 296.394 1133.63 295.229 1133.63 294.009C1133.63 269.161 1112.88 249 1087.3 249C1067.14 249 1050.03 261.535 1043.66 279.006C1038.98 276.344 1033.51 274.791 1027.71 274.791C1012.39 274.791 999.606 285.329 996.634 299.361C993.717 298.002 990.492 297.226 987.043 297.226C975.741 297.226 966.43 305.406 965 316H1237C1236.19 310.149 1231.05 305.656 1224.83 305.656L1224.77 305.628Z" fill="white"/>
                <path opacity="0.05" d="M159.969 201.97C158.779 193.29 151.114 186.57 141.832 186.57C140.702 186.57 139.634 186.69 138.565 186.871C138.656 185.907 138.718 184.912 138.718 183.948C138.718 163.545 121.649 147 100.611 147C84.0305 147 69.9542 157.307 64.7023 171.652C60.855 169.452 56.3664 168.186 51.5725 168.186C38.9618 168.186 28.458 176.836 26.0153 188.348C23.6336 187.233 20.9466 186.6 18.1374 186.6C8.85496 186.6 1.19084 193.321 0 202H160L159.969 201.97Z" fill="white"/>
                <path opacity="0.15" d="M1087 49C1087 40.5499 1080.16 33.7229 1071.69 33.7229C1068.24 33.7229 1065.07 34.8822 1062.51 36.7886C1060.2 28.5189 1052.6 22.439 1043.57 22.439C1033.43 22.439 1025.09 30.0904 1024.03 39.9317C1021.9 38.2056 1019.24 37.1236 1016.27 37.1236C1016.04 37.1236 1015.84 37.1751 1015.61 37.2008C1016.04 35.1656 1016.27 33.0273 1016.27 30.8633C1016.27 13.8086 1002.43 0 985.368 0C970.614 0 958.293 10.3307 955.226 24.1393C952.792 23.0573 950.08 22.439 947.24 22.439C937.582 22.439 929.571 29.3948 927.898 38.5405C926.199 37.6646 924.323 37.1236 922.27 37.1236C915.603 37.1236 910.228 42.4048 910 49H1086.95H1087Z" fill="white"/>
                </g>
            </svg>

            
            
            <div class="learndash-content-body" style="visibility:hidden;">
                <?php
                $buddyboss_content = apply_filters( 'buddyboss_learndash_content', '', $post );
                if ( ! empty( $buddyboss_content ) ) {
	                echo $buddyboss_content;
                } else {
                ?>
                    <div class="<?php echo esc_attr( learndash_the_wrapper_class() ); ?>">

                 <?php
                 /**
                  * Action to add custom content before the quiz content starts
                  *
                  * @since 3.0
                  */
                 do_action( 'learndash-quiz-before', get_the_ID(), $course_id, $user_id );
                 ?>
                    <div id="learndash-course-header" class="bb-lms-header quiz-fix">
                    <div class="info-bar">
                            <div class="questions">
                                <span>Questions 10 of 10</span>
                            </div>
                            <div class="legend">
                                <div class="legend-item g_scores">
                                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="18" cy="18" r="18" fill="#B0D178"/><path d="M27.266 13.734c0 .314-.11.581-.33.801L16.801 24.67c-.22.22-.487.33-.801.33a1.09 1.09 0 0 1-.801-.33L9.33 18.801A1.09 1.09 0 0 1 9 18c0-.314.11-.581.33-.801l1.603-1.603c.22-.22.487-.33.801-.33.314 0 .581.11.801.33L16 19.072l7.73-7.742c.22-.22.488-.33.802-.33.314 0 .581.11.801.33l1.603 1.603c.22.22.33.487.33.801Z" fill="#fff"/></svg>
                                    <span>Correct Answers: <span>0</span></span>
                                </div>
                                <div class="legend-item g_points">
                                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="18" cy="18" r="18" fill="#F2A952"/><path d="m26.418 15.053-5.396-.785-2.412-4.89a.683.683 0 0 0-1.22 0l-2.412 4.89-5.396.785a.679.679 0 0 0-.376 1.16l3.904 3.806-.923 5.375a.679.679 0 0 0 .987.716L18 23.573l4.826 2.537a.679.679 0 0 0 .986-.716l-.922-5.374 3.904-3.807a.679.679 0 0 0 .198-.389.678.678 0 0 0-.574-.771Z" fill="#fff"/></svg>
                                    <span>Points: <span>0</span></span>
                                </div>
                                <div class="legend-item g_streak">
                                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="18" cy="18" r="18" fill="#EF746F"/><path d="M14.72 28c-4.097-2.31-5.176-4.495-4.559-7.48.455-2.205 1.946-3.999 2.09-6.2.636 1.16.902 1.996.974 3.207 2.025-2.482 3.364-5.918 3.443-9.527 0 0 5.277 3.1 5.623 7.783.454-.965.683-2.498.228-3.492 1.363.994 9.339 9.814-1.08 15.709 1.959-3.814.505-8.96-2.896-11.337.227 1.022-.17 4.834-1.675 6.509.417-2.799-.397-3.982-.397-3.982s-.28 1.567-1.363 3.15c-.989 1.447-1.674 2.982-.387 5.66Z" fill="#fff"/></svg>
                                    <span>Streak: <span class="current-streak">0</span></span>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="flex bb-position">
                            <div class="sfwd-course-position">
                                <span class="bb-pages"><?php echo LearnDash_Custom_Label::get_label( 'quiz' ); ?> <?php echo $current_quiz_ke; ?> <span class="bb-total"><?php _e( 'of', 'buddyboss-theme' ); ?> <?php echo count( $quiz_urls ); ?></span></span>
                            </div>
                            <div class="sfwd-course-nav">
                                <?php
                                $expire_date_calc    = ld_course_access_expires_on( $course_id, $user_id );
                                $courses_access_from = ld_course_access_from( $course_id, $user_id );
                                $expire_access_days  = learndash_get_setting( $course_id, 'expire_access_days' );
                                $date_format         = get_option( 'date_format' );
                                $expire_date         = date_i18n( $date_format, $expire_date_calc );
                                $current             = time();
                                $expire_string       = ( $expire_date_calc > $current ) ? __( 'Expires at', 'buddyboss-theme' ) : __( 'Expired at', 'buddyboss-theme' );
                                if ( $expire_date_calc > 0 && abs( intval( $expire_access_days ) )  > 0 && ( !empty( $user_id ) ) ) { ?>
                                <div class="sfwd-course-expire">
                                    <span data-balloon-pos="up" data-balloon="<?php echo $expire_string; ?>"><i class="bb-icons bb-icon-watch-alarm"></i><?php echo $expire_date; ?></span>
                                </div>
                                <?php } ?>
                                <div class="learndash_next_prev_link">
                    				<?php
                    				if ( $pagination_urls['prev'] != '' ) {
                    					echo $pagination_urls['prev'];
                    				} else {
                    					echo '<span class="prev-link empty-post"></span>';
                    				}
                    				?>
                    				<?php if ( $pagination_urls['next'] != '' || ( isset( $course_settings['course_disable_lesson_progression'] ) && $course_settings['course_disable_lesson_progression'] === 'on' && $pagination_urls['next'] != '') ) {
                    					echo $pagination_urls['next'];
                    				} else {
                    					echo '<span class="next-link empty-post"></span>';
                    				}
                    				?>
                                </div>
                            </div>
                        </div> -->
                        <div class="lms-header-title">
                            <h1><?php echo $post->post_title; ?></h1>
                            <p><?= get_field('quiz_description');?> </p>
                        </div>
                        <?php
                        // global $post;
                        // $course_post = learndash_get_setting( $post, 'course' );
                        // $course_data = get_post( $course_post );
                        // $author_id = $course_data->post_author;
                        // learndash_get_template_part( 'template-course-author.php', array(
                        //     'user_id'   => $author_id
                        // ), true );
                        ?>
                    </div>

                 <div class="learndash_content_wrap">
                    <?php
                    learndash_get_template_part( 'modules/infobar.php', array(
                         'context'   =>  'quiz',
                         'course_id' =>  $course_id,
                         'user_id'   =>  $user_id
                     ), true );
                    if( !empty($lesson_progression_enabled) ):
                    	$last_incomplete_step = is_quiz_accessable( null, $post, true );
                    	if ( 1 !== $last_incomplete_step ):
                            /**
                             * Action to add custom content before the quiz progression
                             *
                             * @since 3.0
                             */
                            do_action( 'learndash-quiz-progression-before', get_the_ID(), $course_id, $user_id );
                    		if ( is_a( $last_incomplete_step, 'WP_Post' ) ) {
                                learndash_get_template_part('modules/messages/lesson-progression.php', array(
                                    'previous_item' => $last_incomplete_step,
                                    'course_id'     => $course_id,
                                    'user_id'       => $user_id,
                                    'context'       => 'quiz'
                                ), true );
                    		}
                            /**
                             * Action to add custom content after the quiz progress
                             *
                             * @since 3.0
                             */
                            do_action( 'learndash-quiz-progression-after', get_the_ID(), $course_id, $user_id );
                    	endif;
                    endif;
                     if( $show_content ):
                         /**
                          * Content and/or tabs
                          */
                         learndash_get_template_part( 'modules/tabs.php', array(
                             'course_id' => $course_id,
                             'post_id'   => get_the_ID(),
                             'user_id'   => $user_id,
                             'content'   => $content,
                             'materials' => $materials,
                             'context'   => 'quiz'
                         ), true );
                        if ( $attempts_left ):
                            /**
                             * Action to add custom content before the actual quiz content (not WP_Editor content)
                             *
                             * @since 3.0
                             */
                            do_action( 'learndash-quiz-actual-content-before', get_the_ID(), $course_id, $user_id );
                            echo $quiz_content;
                            /**
                             * Action to add custom content after the actual quiz content (not WP_Editor content)
                             *
                             * @since 3.0
                             */
                            do_action( 'learndash-quiz-actual-content-after', get_the_ID(), $course_id, $user_id );
                        else:
                            /**
                             * Display an alert
                             */
                             /**
                              * Action to add custom content before the quiz attempts alert
                              *
                              * @since 3.0
                              */
                             do_action( 'learndash-quiz-attempts-alert-before', get_the_ID(), $course_id, $user_id );
                            learndash_get_template_part( 'modules/alert.php', array(
                                'type'      =>  'warning',
                                'icon'      =>  'alert',
                                'message' => sprintf( esc_html_x( 'You have already taken this %1$s %2$d time(s) and may not take it again.', 'placeholders: quiz, attempts count', 'buddyboss-theme' ), learndash_get_custom_label_lower('quiz'), $attempts_count )
                            ), true );
                            /**
                             * Action to add custom content after the quiz attempts alert
                             *
                             * @since 3.0
                             */
                            do_action( 'learndash-quiz-attempts-alert-after', get_the_ID(), $course_id, $user_id );
                        endif;
                    endif;
                    /**
                     * Action to add custom content before the quiz content starts
                     *
                     * @since 3.0
                     */
                    do_action( 'learndash-quiz-after', get_the_ID(), $course_id, $user_id ); ?>

                    <?php
                    $focus_mode         = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'focus_mode_enabled' );
                    $post_type          = get_post_type( $post->ID );
                    $post_type_comments = learndash_post_type_supports_comments( $post_type );
                    if ( is_user_logged_in() && 'yes' === $focus_mode && comments_open() ) {
	                    learndash_get_template_part( 'focus/comments.php',
		                    array(
			                    'course_id' => $course_id,
			                    'user_id'   => $user_id,
			                    'context'   => 'focus'
		                    ),
		                    true );
                    } elseif ( $post_type_comments == true ) {
	                    if ( comments_open() ) :
		                    comments_template();
	                    endif;
                    }
                    ?>

                    </div><?php /* .learndash_content_wrap */ ?>
                    <div class="ctr">3</div>
                </div> <!--/.learndash-wrapper-->
                <?php } ?>
            </div><?php /* .learndash-content-body */ ?>
            <div class="svg-wrapper">
                <?php if( $theme === "1" ): ?>
                    <svg viewBox="0 0 1440 389" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M391.042 93.661C357.19 75.4595 344.356 33.8064 361.865 0C355.171 5.53437 349.394 12.3765 344.987 20.438C333.497 41.4635 332 93.2187 373.438 115.508C407.761 133.968 450.67 121.333 469.28 87.2864C469.535 86.8188 469.758 86.3513 470 85.8838C448.421 103.719 417.232 107.756 391.036 93.6673L391.042 93.661Z" fill="#F9DBC2"/>
                    <path d="M153.642 251.458C262.978 245.338 621.983 191.13 632.283 191.902C632.283 191.902 679.724 245.363 800.428 237.726C921.133 230.088 1096.49 183.493 1155.28 178.916C1155.28 178.916 1186.23 226.257 1241.93 225.511C1297.63 224.74 1199.64 346.987 1000.54 357.634C801.448 368.232 153.642 251.458 153.642 251.458Z" fill="#841D96"/>
                    <path d="M1241.93 225.486C1191.87 226.182 1161.82 188.045 1156.25 180.284C1042.14 177.995 735.125 337.036 663.404 316.686C598.25 298.202 601.907 243.074 632.283 191.876C582.453 194.961 256.386 245.711 153.642 251.433C153.642 251.433 801.473 368.207 1000.57 357.584C1199.66 346.962 1297.65 224.715 1241.95 225.461L1241.93 225.486Z" fill="#581564"/>
                    <path d="M368.11 310.94C372.563 309.571 810.056 194.016 810.056 194.016L1147.47 298.551L708.706 324.672L368.11 310.915V310.94Z" fill="#2C033B"/>
                    <path d="M813.166 194.986C819.012 222.127 796.971 261.26 720.274 324L1147.44 298.551L813.166 194.986Z" fill="#671A74"/>
                    <path d="M719.95 307.855C508.941 327.11 251.634 178.891 0 178.891V428.535C566.855 428.684 922.352 424.156 1440 405.847V106C1268.07 106 990.243 283.176 719.975 307.855H719.95Z" fill="#4B0F55"/>
                    <path d="M722.015 371.466C511.005 388.656 251.634 205.509 0 205.509V428.535C566.855 428.659 922.352 424.629 1440 408.26V106C1268.07 106 992.307 349.425 722.04 371.466H722.015Z" fill="#2C033B"/>
                    </svg>
                <?php elseif ($theme === "2"): ?> 
                    
                    <svg viewBox="0 0 1440 362" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.1" d="M333.6 334.832C427.709 334.832 504 259.878 504 167.416C504 74.9548 427.709 0 333.6 0C239.491 0 163.2 74.9548 163.2 167.416C163.2 259.878 239.491 334.832 333.6 334.832Z" fill="#FFFFD3"/>
                    <path opacity="0.2" d="M333.6 299.407C407.785 299.407 467.924 240.313 467.924 167.417C467.924 94.5201 407.785 35.4258 333.6 35.4258C259.415 35.4258 199.276 94.5201 199.276 167.417C199.276 240.313 259.415 299.407 333.6 299.407Z" fill="#FFFFD3"/>
                    <path d="M333.601 266.424C389.269 266.424 434.397 222.097 434.397 167.417C434.397 112.737 389.269 68.4105 333.601 68.4105C277.932 68.4105 232.804 112.737 232.804 167.417C232.804 222.097 277.932 266.424 333.601 266.424Z" fill="#FFFFD3"/>
                    <path d="M153.642 224.458C262.978 218.338 621.983 164.13 632.283 164.902C632.283 164.902 679.724 218.363 800.428 210.726C921.133 203.088 1096.49 156.493 1155.28 151.916C1155.28 151.916 1186.23 199.257 1241.93 198.511C1297.63 197.74 1199.64 319.987 1000.54 330.634C801.448 341.232 153.642 224.458 153.642 224.458Z" fill="#FFD869"/>
                    <path d="M1241.93 198.486C1191.87 199.182 1161.82 161.045 1156.25 153.284C1042.14 150.995 735.125 310.036 663.404 289.686C598.25 271.202 601.907 216.074 632.283 164.876C582.453 167.961 256.386 218.711 153.642 224.433C153.642 224.433 801.473 341.207 1000.57 330.584C1199.66 319.962 1297.65 197.715 1241.95 198.461L1241.93 198.486Z" fill="#FFEE76"/>
                    <path d="M368.11 283.939C372.563 282.571 810.056 167.016 810.056 167.016L1147.47 271.551L708.706 297.672L368.11 283.915V283.939Z" fill="#FFCB57"/>
                    <path d="M813.166 167.986C819.012 195.128 796.971 234.26 720.274 297L1147.44 271.551L813.166 167.986Z" fill="#FFBD48"/>
                    <path d="M719.95 280.855C508.941 300.11 251.634 151.891 0 151.891V401.535C566.855 401.684 922.352 397.156 1440 378.847V79C1268.07 79 990.243 256.176 719.975 280.855H719.95Z" fill="#FFDC6C"/>
                    <path d="M722.015 344.466C511.005 361.656 251.634 178.509 0 178.509V401.535C566.855 401.659 922.352 397.629 1440 381.26V79C1268.07 79 992.307 322.425 722.04 344.466H722.015Z" fill="#FFCB57"/>
                    </svg>

                <?php elseif ($theme === "3"): ?>
                    
                    <svg viewBox="0 0 1440 520" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.1" d="M495.459 286.416C575.729 286.416 640.8 222.484 640.8 143.62C640.8 64.7557 575.729 0.823608 495.459 0.823608C415.189 0.823608 350.118 64.7557 350.118 143.62C350.118 222.484 415.189 286.416 495.459 286.416Z" fill="#FFFFD3"/>
                    <path opacity="0.2" d="M495.459 256.2C558.734 256.2 610.029 205.796 610.029 143.62C610.029 81.4435 558.734 31.0396 495.459 31.0396C432.183 31.0396 380.888 81.4435 380.888 143.62C380.888 205.796 432.183 256.2 495.459 256.2Z" fill="#FFFFD3"/>
                    <path d="M495.461 228.067C542.943 228.067 581.435 190.259 581.435 143.62C581.435 96.9815 542.943 59.1733 495.461 59.1733C447.979 59.1733 409.487 96.9815 409.487 143.62C409.487 190.259 447.979 228.067 495.461 228.067Z" fill="#FFFFD3"/>
                    <path d="M153.642 382.458C262.978 376.338 621.983 322.13 632.283 322.902C632.283 322.902 679.724 376.363 800.428 368.726C921.133 361.088 1096.49 314.493 1155.28 309.916C1155.28 309.916 1186.23 357.257 1241.93 356.511C1297.63 355.74 1199.64 477.987 1000.54 488.634C801.448 499.232 153.642 382.458 153.642 382.458Z" fill="#FFB950"/>
                    <path d="M1241.93 356.486C1191.87 357.182 1161.82 319.045 1156.25 311.284C1042.14 308.995 735.125 468.036 663.404 447.686C598.25 429.202 601.907 374.074 632.283 322.876C582.453 325.961 256.386 376.711 153.642 382.433C153.642 382.433 801.473 499.207 1000.57 488.584C1199.66 477.962 1297.65 355.715 1241.95 356.461L1241.93 356.486Z" fill="#FFD178"/>
                    <path d="M368.11 441.939C372.563 440.571 810.056 325.016 810.056 325.016L1147.47 429.551L708.706 455.672L368.11 441.915V441.939Z" fill="#FFC148"/>
                    <path d="M813.166 325.986C819.012 353.128 796.971 392.26 720.274 455L1147.44 429.551L813.166 325.986Z" fill="#F79E36"/>
                    <path d="M719.95 438.855C508.941 458.11 251.634 309.891 0 309.891V559.535C566.855 559.684 922.352 555.156 1440 536.847V237C1268.07 237 990.243 414.176 719.975 438.855H719.95Z" fill="#FFB950"/>
                    <path d="M722.015 502.466C511.005 519.656 251.634 336.509 0 336.509V559.535C566.855 559.659 922.352 555.629 1440 539.26V237C1268.07 237 992.307 480.425 722.04 502.466H722.015Z" fill="#F79E36"/>
                    </svg>

                <?php elseif ($theme === "4"): ?>
                    
                    <svg viewBox="0 0 1440 377" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.1" d="M976.457 334.945C1070.6 334.945 1146.91 259.965 1146.91 167.473C1146.91 74.98 1070.6 0 976.457 0C882.316 0 806 74.98 806 167.473C806 259.965 882.316 334.945 976.457 334.945Z" fill="#FEE195"/>
                    <path opacity="0.2" d="M976.457 299.508C1050.67 299.508 1110.83 240.394 1110.83 167.473C1110.83 94.5519 1050.67 35.4377 976.457 35.4377C902.247 35.4377 842.088 94.5519 842.088 167.473C842.088 240.394 902.247 299.508 976.457 299.508Z" fill="#FEE195"/>
                    <path d="M976.458 266.514C1032.15 266.514 1077.29 222.172 1077.29 167.474C1077.29 112.775 1032.15 68.4336 976.458 68.4336C920.77 68.4336 875.627 112.775 875.627 167.474C875.627 222.172 920.77 266.514 976.458 266.514Z" fill="#FEE195"/>
                    <path d="M153.643 239.458C262.978 233.338 621.983 179.13 632.283 179.902C632.283 179.902 679.724 233.363 800.429 225.726C921.133 218.088 1096.49 171.493 1155.28 166.916C1155.28 166.916 1186.23 214.257 1241.93 213.511C1297.63 212.74 1199.64 334.987 1000.54 345.634C801.449 356.232 153.643 239.458 153.643 239.458Z" fill="#FF9432"/>
                    <path d="M1241.93 213.486C1191.87 214.182 1161.82 176.045 1156.25 168.284C1042.14 165.995 735.126 325.036 663.404 304.686C598.25 286.202 601.907 231.074 632.283 179.876C582.453 182.961 256.386 233.711 153.643 239.433C153.643 239.433 801.473 356.207 1000.57 345.584C1199.66 334.962 1297.65 212.715 1241.95 213.461L1241.93 213.486Z" fill="#E97636"/>
                    <path d="M368.11 298.939C372.563 297.571 810.056 182.016 810.056 182.016L1147.47 286.551L708.706 312.672L368.11 298.915V298.939Z" fill="#B34F18"/>
                    <path d="M813.166 182.986C819.012 210.128 796.971 249.26 720.274 312L1147.44 286.551L813.166 182.986Z" fill="#D2692F"/>
                    <path d="M719.95 295.855C508.941 315.11 251.634 166.891 0 166.891V416.535C566.855 416.684 922.352 412.156 1440 393.847V94C1268.07 94 990.243 271.176 719.975 295.855H719.95Z" fill="#952F02"/>
                    <path d="M722.015 359.466C511.005 376.656 251.634 193.509 0 193.509V416.535C566.855 416.659 922.352 412.629 1440 396.26V94C1268.07 94 992.307 337.425 722.04 359.466H722.015Z" fill="#5B1302"/>
                    </svg>

                <?php elseif ($theme === "5"): ?>
                    
                    <svg viewBox="0 0 1440 394" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M708.64 93.661C675.045 75.4595 662.308 33.8064 679.684 0C673.041 5.53437 667.308 12.3765 662.934 20.438C651.531 41.4635 650.046 93.2187 691.169 115.508C725.232 133.968 767.816 121.333 786.286 87.2864C786.539 86.8188 786.76 86.3513 787 85.8838C765.585 103.719 734.632 107.756 708.634 93.6673L708.64 93.661Z" fill="#F9DBC2"/>
                    <path d="M153.643 256.458C262.978 250.338 621.983 196.13 632.283 196.902C632.283 196.902 679.724 250.363 800.429 242.726C921.133 235.088 1096.49 188.493 1155.28 183.916C1155.28 183.916 1186.23 231.257 1241.93 230.511C1297.63 229.74 1199.64 351.987 1000.54 362.634C801.449 373.232 153.643 256.458 153.643 256.458Z" fill="#5574C8"/>
                    <path d="M1241.93 230.486C1191.87 231.182 1161.82 193.045 1156.25 185.284C1042.14 182.995 735.126 342.036 663.404 321.686C598.25 303.202 601.907 248.074 632.283 196.876C582.453 199.961 256.386 250.711 153.643 256.433C153.643 256.433 801.473 373.207 1000.57 362.584C1199.66 351.962 1297.65 229.715 1241.95 230.461L1241.93 230.486Z" fill="#2A4FB4"/>
                    <path d="M368.11 315.939C372.563 314.571 810.056 199.016 810.056 199.016L1147.47 303.551L708.706 329.672L368.11 315.915V315.939Z" fill="#123594"/>
                    <path d="M813.165 199.986C819.012 227.128 796.97 266.26 720.273 329L1147.44 303.551L813.165 199.986Z" fill="#1640B0"/>
                    <path d="M719.95 312.855C508.941 332.11 251.634 183.891 0 183.891V433.535C566.855 433.684 922.352 429.156 1440 410.847V111C1268.07 111 990.243 288.176 719.975 312.855H719.95Z" fill="#0E2174"/>
                    <path d="M722.015 376.466C511.005 393.656 251.634 210.509 0 210.509V433.535C566.855 433.659 922.352 429.629 1440 413.26V111C1268.07 111 992.307 354.425 722.04 376.466H722.015Z" fill="#0B0D55"/>
                    </svg>

                <?php elseif ($theme === "6"): ?>
                    
                    <svg viewBox="0 0 1440 346" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M987.042 94.4163C953.19 76.068 940.356 34.079 957.865 0C951.171 5.579 945.394 12.4763 940.987 20.6028C929.497 41.7979 928 93.9705 969.438 116.439C1003.76 135.049 1046.67 122.311 1065.28 87.9903C1065.54 87.519 1065.76 87.0477 1066 86.5764C1044.42 104.555 1013.23 108.625 987.036 94.4227L987.042 94.4163Z" fill="#F9DBC2"/>
                    <path d="M153.643 208.458C262.978 202.338 621.983 148.13 632.283 148.902C632.283 148.902 679.724 202.363 800.429 194.726C921.133 187.088 1096.49 140.493 1155.28 135.916C1155.28 135.916 1186.23 183.257 1241.93 182.511C1297.63 181.74 1199.64 303.987 1000.54 314.634C801.449 325.232 153.643 208.458 153.643 208.458Z" fill="#6944B7"/>
                    <path d="M1241.93 182.486C1191.87 183.182 1161.82 145.045 1156.25 137.284C1042.14 134.995 735.126 294.036 663.404 273.686C598.25 255.202 601.907 200.074 632.283 148.876C582.453 151.961 256.386 202.711 153.643 208.433C153.643 208.433 801.473 325.207 1000.57 314.584C1199.66 303.962 1297.65 181.715 1241.95 182.461L1241.93 182.486Z" fill="#4E339D"/>
                    <path d="M368.11 267.939C372.563 266.571 810.056 151.016 810.056 151.016L1147.47 255.551L708.706 281.672L368.11 267.915V267.939Z" fill="#32187B"/>
                    <path d="M813.165 151.986C819.012 179.128 796.97 218.26 720.273 281L1147.44 255.551L813.165 151.986Z" fill="#402885"/>
                    <path d="M719.95 264.855C508.941 284.11 251.634 135.891 0 135.891V385.535C566.855 385.684 922.352 381.156 1440 362.847V63C1268.07 63 990.243 240.176 719.975 264.855H719.95Z" fill="#29126B"/>
                    <path d="M722.015 328.466C511.005 345.656 251.634 162.509 0 162.509V385.535C566.855 385.659 922.352 381.629 1440 365.26V63C1268.07 63 992.307 306.425 722.04 328.466H722.015Z" fill="#19064F"/>
                    </svg>


                <?php endif; ?>
                
            </div>

        </div><?php /* #learndash-page-content */ ?>
    </div>

</div>


<!--quiz result -->
<div class="quiz-result" style="display:none">
    <div class="quiz-result--inner">
        <div class="quiz-score">
            <h2>quiz score</h2>
        </div>
        <div class="my-scores">
            <div class="pts">Correct Answers: <span class="correct-answers">0</span></div>
            <div class="multiplier"> 
            
            </div>
            <div class="result">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="20" cy="20" r="20" fill="#F2A952"/><path d="m29.354 16.725-5.996-.871-2.68-5.434a.759.759 0 0 0-1.356 0l-2.68 5.434-5.995.871a.754.754 0 0 0-.418 1.29l4.338 4.229-1.025 5.972a.754.754 0 0 0 1.095.796L20 26.192l5.363 2.82a.755.755 0 0 0 1.096-.796l-1.025-5.972 4.337-4.23a.755.755 0 0 0 .22-.431.753.753 0 0 0-.637-.858Z" fill="#fff"/></svg>
                <span class="points-before-multiplier">0</span>
            </div>
        </div>
        <div class="streak">
            <div class="streak-pts">
                Highest Streak: <span class="result-streak">0</span>
            </div>
            <div class="total-streak">
                x <span class="result-streak"></span>
            </div>
        </div>
        <div class="total-score">
            Total Score:
            <div class="total-score--result">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="20" cy="20" r="20" fill="#F2A952"/><path d="m29.354 16.725-5.996-.871-2.68-5.434a.759.759 0 0 0-1.356 0l-2.68 5.434-5.995.871a.754.754 0 0 0-.418 1.29l4.338 4.229-1.025 5.972a.754.754 0 0 0 1.095.796L20 26.192l5.363 2.82a.755.755 0 0 0 1.096-.796l-1.025-5.972 4.337-4.23a.755.755 0 0 0 .22-.431.753.753 0 0 0-.637-.858Z" fill="#fff"/></svg>
                <span class="total-points-after-mult">0</span>
            </div>
        </div>
        <div class="quiz-result__actions">
            <a href="/leaderboard?quiz=<?=$post->ID?>" class="btn-view"><svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.555 21.522a7.959 7.959 0 0 1-.405.088H9.694a16.142 16.142 0 0 1-.486-.107l.189-.865.187-.86.572-2.62.056-.261.223-1.02h2.891l.222 1.02.179.82.45 2.061.187.86.192.884Z" fill="#FFD31D"/><path d="M13.325 15.878h-2.892l-.279 1.28a7.37 7.37 0 0 0 3.572.559l-.401-1.84Z" fill="#FFAB2E"/><path d="M15.331 20.676a.893.893 0 0 1-.408.752 11.487 11.487 0 0 1-.593.145H9.513a10.126 10.126 0 0 1-.734-.185.895.895 0 0 1-.352-.713v-.037a.893.893 0 0 1 .897-.86h5.11c.483 0 .877.382.897.86v.037ZM23.422 6.678a4.29 4.29 0 0 0-2.269-2.269 4.239 4.239 0 0 0-1.617-.335H4.22A4.239 4.239 0 0 0 1.25 5.322 4.248 4.248 0 0 0 0 8.337a4.24 4.24 0 0 0 1.249 3.015c.391.392.847.7 1.355.913a4.24 4.24 0 0 0 1.66.336h15.23a4.257 4.257 0 0 0 3.928-2.604 4.237 4.237 0 0 0 .335-1.66c0-.574-.112-1.133-.335-1.66Zm-3.928 4.325H4.264a2.669 2.669 0 0 1-2.666-2.666 2.669 2.669 0 0 1 2.666-2.665h15.23a2.669 2.669 0 0 1 2.665 2.665 2.669 2.669 0 0 1-2.665 2.666Z" fill="#FFBF1F"/><path d="M4.845 5.672h14.648c.18 0 .356.018.526.052l.074-1.608a4.293 4.293 0 0 0-.557-.042H4.77l.074 1.598ZM19.728 10.993c-.077.007-.155.01-.235.01H5.141c.091.557.246 1.092.454 1.598h13.677c.21-.51.364-1.048.456-1.608Z" fill="#FFAB2E"/><path d="M3.92 5.694a2.72 2.72 0 0 1 .343-.022h14.861l.074-1.598H4.22c-.126.001-.25.008-.373.02l.073 1.6ZM18.477 12.601a7.346 7.346 0 0 0 .382-1.598H4.186c.068.554.197 1.09.38 1.599h13.912Z" fill="#FFAB2E"/><path d="M11.879 17.087a7.393 7.393 0 0 1-7.39-7.155l-.356-7.788h15.492l-.357 7.788a7.392 7.392 0 0 1-7.39 7.155Z" fill="#FFD31D"/><path d="m19.516 4.507.109-2.364H4.133L4.19 3.38l15.326 1.127Z" fill="#FFAB2E"/><path d="M19.758 2.966H4a.844.844 0 0 1-.825-.668l-.272-1.279A.844.844 0 0 1 3.73 0h16.299c.536 0 .936.494.825 1.019l-.27 1.278a.844.844 0 0 1-.826.669Z" fill="#FFBF1F"/><path d="M12.633 1.085H3.996a.313.313 0 1 1 0-.625h8.637a.313.313 0 1 1 0 .625ZM14.098.97c.256-.335-.123-.714-.459-.459-.255.335.124.714.46.459Z" fill="#fff"/><path opacity=".8" d="M6.959 6.033c.085-1.026.202-1.7.202-1.7l-2.031.03c-.04.588-.064 1.144-.072 1.67h1.9ZM6.881 7.508H5.078c.33 7.454 4.114 7.752 4.114 7.752-2.031-1.648-2.373-5.154-2.312-7.752h.001Z" fill="#FFEF42"/><path d="m12.372 5.04.76 1.539c.091.186.27.315.475.345l1.697.247a.632.632 0 0 1 .35 1.078l-1.228 1.197a.631.631 0 0 0-.181.56l.29 1.69a.632.632 0 0 1-.918.666l-1.518-.798a.632.632 0 0 0-.588 0l-1.518.798a.632.632 0 0 1-.917-.666l.29-1.69a.633.633 0 0 0-.182-.56L7.956 8.25a.632.632 0 0 1 .35-1.078l1.697-.247a.632.632 0 0 0 .476-.345l.76-1.539a.632.632 0 0 1 1.133 0Z" fill="#fff"/><path d="M16.472 20.774H7.286a.875.875 0 0 0-.875.875v1.476c0 .483.392.875.875.875h9.186a.875.875 0 0 0 .875-.875V21.65a.875.875 0 0 0-.875-.875Z" fill="#FFAB2E"/></svg> 
            view leaderboard</a>
            <a href="/leaderboard" class="btn-share" data-courseid="<?=$course_id?>" >
                <svg width="25" height="23" viewBox="0 0 25 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.738 17.963h3.773c.295 0 .577.12.78.334l3.429 3.586a1.08 1.08 0 0 0 1.56 0l3.428-3.586a1.08 1.08 0 0 1 .78-.334h3.774a3.238 3.238 0 0 0 3.238-3.238V3.738A3.238 3.238 0 0 0 21.262.5H3.738A3.238 3.238 0 0 0 .5 3.738v10.987a3.238 3.238 0 0 0 3.238 3.238Z" fill="#EF746F"/><path d="m17.783 9.068-4.386-4.322.02 2.66a8.71 8.71 0 0 0-1.946.72A7.712 7.712 0 0 0 9.424 9.66c-1.146 1.21-1.792 2.64-2.207 4.05.935-1.14 2.052-2.105 3.257-2.606a5.427 5.427 0 0 1 2.97-.367l.02 2.717 4.32-4.387Z" fill="#fff"/>
                </svg> Share to the Class
            </a>
            
        </div>

        <a href="<?=$back_link?>" class="btn-continue-course">CONTINUE TO COURSE</a>
        
    </div>
</div>
<!--end quiz result-->

<div id="share-result-modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">            
        <span class="close"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 1 1 13M1 1l12 12" stroke="#A5A6A5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>
        <div class="share-result-content">
            <h2>Thanks for Sharing</h2>
            <p>You have shared your results to the following groups</p>

            <div class="group-item"><a href="">Group Name</a></div>
        </div>
    </div>

</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/3.6.0/gsap.min.js"></script>
<script>
    var g = document.querySelector(".clouds");
    var demo = document.querySelector(".clouds-container");
    var cloudCopy = g.cloneNode(true);
    demo.appendChild(cloudCopy);
    TweenMax.set(cloudCopy,{x:"100%"});
    TweenMax.to("g", 100, {x:"-=100%", ease:Linear.easeNone, repeat:-1,});

    jQuery(function ($) {

        
        
            $('.learndash-content-body').css('visibility', 'visible');
            let interval = "";
            
            let quizStepSubmitted = false;
            let mainParent = "";
            let quizStepsCount=0;
            let currentStep=0;
            let totalPoints = 0;
            let totalSeconds = 0;
            let default_seconds = 20;
            let initialSeconds = default_seconds;
            let counter = initialSeconds;
            let points_multiplier = 0.5;
            let highestStreak = 0;
            let ctr = 4;

            setTimeout( () => {
                quizStepsCount = $(".wpProQuiz_listItem").length;
            }, 100);

            displayActiveQuestionType = () => {
                let questionType = $('.wpProQuiz_list .wpProQuiz_listItem:visible').attr("data-type");
                if(typeof questionType != 'undefined'){
                    /* 
                    <" tabindex="-1" aria-hidden="true"><option value="" data-select2-id="2">All Question Types</option>
                    <option value="single" data-select2-id="10">Single choice</option>
                    <option value="multiple" data-select2-id="11">Multiple choice</option>
                    <option value="free_answer" data-select2-id="12">"Free" choice</option>
                    <option value="sort_answer" data-select2-id="13">"Sorting" choice</option>
                    <option value="matrix_sort_answer" data-select2-id="14">"Matrix Sorting" choice</option
                    ><option value="cloze_answer" data-select2-id="15">Fill in the blank</option>
                    <option value="assessment_answer" data-select2-id="16">Assessment</option>
                    <option value="essay" data-select2-id="17">Essay / Open Answer</option><
                    
                    /select>
                    */

                    switch(questionType){
                        case "single": questionType = "Single Choice";  break;
                        case "multiple": questionType = "Multiple Choice";  break;
                        case "free_answer": questionType = "Free Choice";  break;
                        case "sort_answer": questionType = "Sorting Choice";  break;
                        case "matrix_sort_answer": questionType = "Matrix Sorting Choice";  break;
                        case "cloze_answer": questionType = "Fill in the blank";  break;
                        case "assessment_answer": questionType = "Assessment";  break;
                        case "essay": questionType = "Essay / Open Answer";  break;
                    }

                    $('.wpProQuiz_quiz .question-badge').show();
                    $('.wpProQuiz_quiz .question-badge').text(`${questionType}`)
                }else{
                    $('.wpProQuiz_quiz .question-badge').hide();
                }
            }

            quizCounter = () => {
                objTime = {
                    timerPB : function(){
                        
                        if(parseInt($('.countdown').attr('data-val')) > 0){
                            $('.countdown').addClass('stop');
                        }					
                        countdown20();
                    
                    } 
                }
                
                $('<div class="question-badge"></div>').appendTo('.wpProQuiz_question');
                $('.wpProQuiz_quiz').addClass('ctr-mode');
                $('.ctr').insertAfter('.wpProQuiz_quiz .wpProQuiz_list');
                $('.wpProQuiz_quiz').append($('.ctr'));


                jQuery('.wpProQuiz_question').append('<div><div class="timer"><svg width="37" height="40" viewBox="0 0 37 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 21.985C0 12.527 7.317 4.762 16.635 4.038V2.83h-3.348a1.4 1.4 0 01-1.415-1.415A1.4 1.4 0 0113.287 0h9.319a1.4 1.4 0 011.415 1.415 1.4 1.4 0 01-1.415 1.415h-3.14v1.208c3.45.276 6.59 1.484 9.214 3.417l1.553-1.553-.656-.656a1.416 1.416 0 012.002-2.002l3.279 3.279a1.416 1.416 0 01-2.002 2.002l-.656-.656-1.415 1.415a17.95 17.95 0 015.246 12.7c0 9.94-8.076 18.016-18.015 18.016C8.075 40 0 31.924 0 21.985zM18.015 6.799c-8.386 0-15.22 6.799-15.22 15.22 0 8.387 6.834 15.22 15.22 15.22 8.387 0 15.22-6.833 15.22-15.22s-6.833-15.22-15.22-15.22zm0 16.359c-.793 0-1.415-.621-1.415-1.38v-8.732a1.4 1.4 0 011.415-1.415 1.4 1.4 0 011.415 1.415v7.282h4.625a1.4 1.4 0 011.415 1.415 1.4 1.4 0 01-1.415 1.415h-6.04z" fill="#497DC0"/></svg> <span class="countdown"> </span>&nbsp;s</div></div>')
                $('.wpProQuiz_sending').wrapInner('<div class="wpProQuiz_sending__inner" />');
                
                let observer = new MutationObserver(function(mutations) {
                    $('.correct-ans, .wrong-ans').hide();
                });
                let target = document.querySelector('.wpProQuiz_sending');
                observer.observe(target, {
                    attributes: true
                });
            }
            quizCounter();



            $(document).ajaxComplete(function(event, xhr, settings) {
                if (typeof settings !== 'undefined' && typeof settings.data !== 'undefined') {
                    if (settings.data.indexOf('action=wp_pro_quiz_admin_ajax_load_data') !== -1) {
                        $('.ctr').text(3);
                        setTimeout( () => {
                            numQuiz = $('.wpProQuiz_list .wpProQuiz_listItem').length;
                            $('.questions span').text(`QUESTION 1 OF ${numQuiz}`);

                            displayActiveQuestionType();
                        }, 200);
                        setTimeout( () => {
                            quizStepsCount = $(".wpProQuiz_listItem").length;
                        }, 100);
                        quizCounter();
                    }
                }
            });


        
            function countdown20() {
                

                // check if the current question/answer type is sortable
                // show the "check" button
                $(".wpProQuiz_list .wpProQuiz_listItem").each( function(){
                    //console.log("listItem",$(this).is(":visible"), $(this).attr("data-type") )
                    if( $(this).attr("data-type") != "single"  && $(this).is(":visible") 
                    ){
                        $(this).addClass("not_single_select");
                        $(this).addClass(`question-type-${$(this).attr("data-type")}`);
                        $(this).find('input[name="check"]').addClass("btn-check").show().attr({"style":"display: margin: auto;display: block !important;width: 200px;margin: auto;"});
                    }
                })

                counter = 20; // original countdown is 20 seconds, increase for testing
                
               

                // check for the current question if it has "seconds_to_answer" value
                // if not use the default value
                $(".wpProQuiz_list .wpProQuiz_listItem").each( function(){
                    if($(this).is(":visible") ){
                        counter = $(this).attr("data-seconds_to_answer");
                        initialSeconds = counter;
                    }
                })


                // https://app.clickup.com/t/865bxgjxx
                // 40 seconds for Sorting elements, 
                let quizType = $(".wpProQuiz_listItem:visible").attr("data-type");
                if (quizType.includes("sort")) {
                    counter = 40;
                } 

                console.log("counter", counter, quizType)
                quizStepSubmitted = false;


                if(interval !="") clearInterval(interval);

                setTimeout( () => {
                    $('.countdown').text(counter);
                    interval = setInterval(function() {
                        counter--;
                        
                        // Display 'counter' wherever you want to display it.
                        $('.countdown').text(counter);
                        $('.countdown').attr('data-val', counter);
                        if (counter == 0 || $('.countdown').hasClass('stop')) {
                            // Display a login box
                            $('.countdown').removeClass('stop');
                            
                        }
                        
                        if(counter == 0){
                            clearInterval(interval);	
                            $(".wpProQuiz_listItem").each( function(){
                                display = $(this).css("display");
                                if(display == "list-item"){
                                    mainParent = $(this);
                                    $(this).find("input[name='check']").trigger("click");
                                    $(this).find("input[name='check']").attr("data-trigger","1")
                                    
                                    var erMsg = $(this).find('.wpProQuiz_response .wpProQuiz_incorrect .wpProQuiz_AnswerMessage').text();						
                                    $('.wrong-ans--cols p').text(erMsg);
                                    $('.wrong-ans').slideDown();
                                    $(".wrong-timesup-text").html("Times Up");

                                    //$('.g_streak span span').text("0");
                                    $(this).addClass('currentjm');	
                             
                                    /*setTimeout(function() {
                                        $('html, body').animate({
                                            scrollTop:0
                                        }, 1500, 'swing');
                                    }, 500);*/
                                    
                                }else{
                                    $(this).removeClass('currentjm');
                                }
                            })
                        }


                    }, 1000);
                }, 500)


                


            }
            

            // start btn
          
            $('.wpProQuiz_text .wpProQuiz_button').on('click', function () {
                $('#learndash-page-content').addClass('bgcolor');
                $('#learndash-course-header > .info-bar').addClass('active');
                // $('.svg-wrapper,.lms-header-title').hide();
                $('.lms-header-title').hide();
               
                let countdown3 = setInterval(function() {
                    ctr--;
                    
                    $('.ctr').text(ctr);
                    if (ctr == 0) {
                        // Display a login box
                        $('.ctr').fadeOut();
                        $('.wpProQuiz_quiz').removeClass('ctr-mode');
                        $('.timer').css('opacity', 1);
                        $('.wpProQuiz_question_text').css('filter', 'blur(0)');
                        $('.wpProQuiz_questionList').addClass('active');
                        clearInterval(countdown3);
                        countdown20();

                        displayActiveQuestionType();
                    }
                }, 1000);
				
				$('.wpProQuiz_list .wpProQuiz_listItem').each(function(){
					//console.log($(this).attr('style'));
					if(!$(this).attr('style')){
						var qPage = $('.wpProQuiz_question_page',this).text();			
						$('.questions span').text(qPage);	
                        displayActiveQuestionType();
					}
				});	
            });
			
            // initial numbers after clicking start quiz
            setTimeout( () => {
                let numQuiz = $('.wpProQuiz_list .wpProQuiz_listItem').length;
                $('.questions span').text(`QUESTION 1 OF ${numQuiz}`);

                displayActiveQuestionType();
            }, 200);
            
			$(document).on('click','.btn-correct', function(){
				// $('.wpProQuiz_listItem.currentjm input[name="next"]').click();
				$('.wpProQuiz_list .wpProQuiz_listItem').each(function(){
					//console.log($(this).attr('style'));
					if(!$(this).attr('style')){
						var qPage = $('.wpProQuiz_question_page',this).text();			
						$('.questions span').text(qPage);	

                        displayActiveQuestionType();
					}
				});	
				
				//console.log($('.wpProQuiz_listItem.currentjm input[name="next"]').length);
				
				setTimeout(function(){
					$('.wpProQuiz_listItem').each(function(){
						$('.wpProQuiz_listItem').removeClass('currentjm');
					});	
				},150);
				
                
                $('.correct-ans').slideUp("fast");	
				/*
                setTimeout(function() {
					$('html, body').animate({
						scrollTop: $('.wpProQuiz_quiz').offset().top
					}, 1500, 'swing');
				}, 500);
                */
				
				objTime.timerPB()
				
			});
			
			$(document).on('click','.btn-wrong', function(){
				// $('.wpProQuiz_listItem.currentjm input[name="next"]').click();
				$('.wpProQuiz_list .wpProQuiz_listItem').each(function(){
					//console.log($(this).attr('style'));
					if(!$(this).attr('style')){
						var qPage = $('.wpProQuiz_question_page',this).text();			
						$('.questions span').text(qPage);	

                        displayActiveQuestionType();
					}
				});	
				
				//console.log($('.wpProQuiz_listItem.currentjm input[name="next"]').length);
				
				setTimeout(function(){
					$('.wpProQuiz_listItem').each(function(){
						$('.wpProQuiz_listItem').removeClass('currentjm');
					});	
				},150);
				$('.wrong-ans').slideUp("fast");					
				
                /*
				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $('.wpProQuiz_quiz').offset().top
					}, 1500, 'swing');
				}, 500);	
                */			

				objTime.timerPB();

                setTimeout( () => {
                    $(".wrong-timesup-text").html("Oops, You’ve not got it right this time");
                }, 500)
                
				
			});
			
			
            checkStepAnswer = () => {
                if(currentStep < quizStepsCount){
                    //console.log("steps count", quizStepsCount, currentStep)

                    $("body, html").scrollTop(0);

                    if(typeof mainParent =="object") mainParent.addClass('currentjm');
                            
                    console.log("checkstep answer")

                    mainParent = $(".wpProQuiz_quiz .wpProQuiz_list li.wpProQuiz_listItem:visible");

                    console.log("mainParent data-question-meta", $(mainParent).attr("data-question-meta"))

                    if(!$('.wpProQuiz_response .wpProQuiz_incorrect[style="display: none;"]',mainParent).length){
                        
                        var erMsg = $('.wpProQuiz_response .wpProQuiz_incorrect .wpProQuiz_AnswerMessage',mainParent).text();						
                        $('.wrong-ans--cols p').text(erMsg);

                        $('.wrong-ans').slideDown("fast");						
                        
                        $('.g_streak span span').text("0");
                        $('.wpProQuiz_listItem.currentjm input[name="next"], .wpProQuiz_listItem.not_single_select input[name="next"]').attr('style', 'display:block !important').val('Next').addClass('btn-wrong');
                       
                        /*setTimeout(function() {
                            $('html, body').animate({
                                scrollTop:0
                            }, 1500, 'swing');
                        }, 200);
                        */
                        
                        console.log("checkstep answer", "wrong")

                    }else if(!$('.wpProQuiz_response .wpProQuiz_correct[style="display: none;"]',mainParent).length){
                        $('.wrong-ans').slideUp("fast");	
                            
                            
                        $('.correct-ans').slideDown("fast");	
                        
                        //bonusPointsTimeMult = Math.ceil(.5 * counter);

                        /* to calculate points */
                        var seconds_allowed = initialSeconds;
                        var ratio = default_seconds / seconds_allowed;
                        var seconds_left = counter; // TODO: make this dynamic based on time left
                        bonusPointsTimeMult = Math.round(seconds_left * ratio * points_multiplier);
                        /* end to calculate points*/

                        
                        totalPoints += bonusPointsTimeMult;

                        var scores = parseFloat($('.g_scores span span').text()) + 1;
                        var points = totalPoints;
                        var streak = parseFloat($('.g_streak span span').text()) + 1;
                            
                        $('.g_scores span span').text(scores);
                        $('.g_points span span').text(points);
                        $('.g_streak span span').text(streak);

                        if(streak > highestStreak) highestStreak = streak;
                        
                        
                        $(".correct-answer-multiplier").html( `+ ${bonusPointsTimeMult}`);
                        $('.wpProQuiz_listItem.currentjm input[name="next"], .wpProQuiz_listItem.not_single_select input[name="next"]').attr('style', 'display:block !important').val('Next').addClass('btn-correct');
                        
                        console.log("checkstep answer", "right ")

                        totalSeconds += counter;

                        console.log("checkstep totalSeconds", totalSeconds, bonusPointsTimeMult);

                        
                        
                    }else{
                        $('.wrong-ans').slideDown("fast");
                        $('.wpProQuiz_listItem.currentjm input[name="next"], .wpProQuiz_listItem.not_single_select input[name="next"]').attr('style', 'display:block !important').val('Next').addClass('btn-wrong');
                    }

                   
                }
                quizStepSubmitted = true;
                currentStep++;

                
                
                //$(".my-scores .multiplier").html(`${totalSeconds} x .5`);
            }
            
            let i = 0;

           

			$(document).on('click','.wpProQuiz_questionListItem', e => {	

                eClass = $(e.currentTarget).attr("class");
                //console.log("eClass", eClass)
                mainParent = $(e.currentTarget).parent().parent().parent();
                // only allow click function for single select answers.
                // eg. radio buttons

                let dataType = $(mainParent).attr("data-type");
                let isSingleAnswer = true;

                switch(dataType){
                    case "single":
                        isSingleAnswer = true;
                        break;
                    default: 
                        isSingleAnswer = false;
                        break;
                }
                //console.log("dataType", dataType, isSingleAnswer)

                if(isSingleAnswer){

                    quizStepSubmitted = false;			
                    clearInterval(interval); // countdown interval
                    
                    $(".mainParent").fadeTo("fast", .3)
                    $(e.currentTarget).find(".wpProQuiz_questionInput").prop( "checked", true );

                    val = $(e.currentTarget).find(".wpProQuiz_questionInput").val();
                    name = $(e.currentTarget).find(".wpProQuiz_questionInput").attr("name");
    
                    //$(`.wpProQuiz_questionInput[value="${val}"][name="${name}"]`).trigger("click").addClass("selected")

                    setTimeout( () => {
                        // remove the check button to fix bug on double sending the form
                        $(mainParent).find('input[name="check"]').click().remove();
                    
                    }, 50);

                }else{
                    $(mainParent).addClass("not_single_select currentjm");
                    $(mainParent).addClass(`question-type-${$(mainParent).attr("data-type")}`);
                    $(mainParent).find('input[name="check"]').addClass("btn-check").show().attr({"style":"display: margin: auto;display: block !important;width: 200px;margin: auto;"});
                }
                

			});

            $(document).on("click",".btn-check", e => {
                clearInterval(interval); // countdown interval
            })
            

			// the ajax call listner for steps and quiz submissions
           
            $( document ).ajaxSuccess(function( event, request, settings ) {
                const urlParams = new URLSearchParams(settings.data);
                action = urlParams.get('action');
                
                
                switch(action){
                    case "wp_pro_quiz_completed_quiz":
                        $(".quiz-result").show();
                        $("#learndash-content").hide();
                        $('.wrong-ans').remove();
                        $('.correct-ans').remove();

                        result = urlParams.get("results");
                        result = $.parseJSON(result);
                        correctQuestions = result.comp.correctQuestions;

                        $(".correct-answers").html(correctQuestions);
                        //streak = $(".current-streak").html();
                        $(".result-streak").html(highestStreak);
                        pointsBeforeMult = totalPoints;
                        $(".points-before-multiplier").html(pointsBeforeMult);
                        total = pointsBeforeMult * highestStreak;
                        
                        if(highestStreak <= 1) total = pointsBeforeMult;
                        $(".total-points-after-mult").html(total);

                        // award points api endpoint here
                        $.ajax({
                            "url": "<?=site_url("wp-json/api")?>/user/award-points",
                            dataType:"json",
                            beforeSend: xhr => {
                                xhr.setRequestHeader('X-WP-Nonce', "<?=wp_create_nonce( 'wp_rest' );?>"); 
                            },
                            data: {
                                "type" : "points",
                                "amount": total,
                                "streak": highestStreak,
                                "result": correctQuestions,
                                "total_bonus": totalPoints,
                                "quiz_id": "<?=$post->ID?>",
                            },
                            type: "post",
                            "success" : function(data){
                                console.log(data)
                            }
                        });

                        break; 

                    case "ld_adv_quiz_pro_ajax":
                        func = urlParams.get('func');
                        console.log("test submit answers func quizStepSubmitted",action, request.responseJSON, event, settings, func, quizStepSubmitted );
                        if(func=="checkAnswers"){
                            $(".mainParent").fadeTo("fast", 1)
                            if(!quizStepSubmitted) checkStepAnswer();

                        }
                        break;
                }
            
            });
            

            $(document).on("click",".btn-share",e => {
                e.preventDefault();

                let courseId = $(e.currentTarget).attr("data-courseid");
                let numQuiz = $('.wpProQuiz_list .wpProQuiz_listItem').length;
                let correctAnswers = $(".correct-answers").text();
                let totalPoints = $(".total-points-after-mult").text();

                //console.log( "courseid", courseId, numQuiz, correctAnswers, totalPoints )
                $(".btn-share").fadeTo("fast", .3);
                Safar.shareQuizResult({"course_id":courseId, 
                                        "question_count": numQuiz, 
                                        "correct_answers": correctAnswers, 
                                        "total_points": totalPoints, 
                                        "quiz_id":"<?=$post->ID?>"})
                                    .then( e => {
                                        $(".btn-share").fadeTo("fast", 1);

                                        let shareModal = "";
                                        console.log("e.shared_to_groups", e.shared_to_groups)
                                        $("#share-result-modal").show().css({"display":"flex"});
                                        if(e.shared_to_groups.length > 0 ){
                                            $(".btn-share").html(`
                                            <svg width="25" height="23" viewBox="0 0 25 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3.738 17.963h3.773c.295 0 .577.12.78.334l3.429 3.586a1.08 1.08 0 0 0 1.56 0l3.428-3.586a1.08 1.08 0 0 1 .78-.334h3.774a3.238 3.238 0 0 0 3.238-3.238V3.738A3.238 3.238 0 0 0 21.262.5H3.738A3.238 3.238 0 0 0 .5 3.738v10.987a3.238 3.238 0 0 0 3.238 3.238Z" fill="#EF746F"/><path d="m17.783 9.068-4.386-4.322.02 2.66a8.71 8.71 0 0 0-1.946.72A7.712 7.712 0 0 0 9.424 9.66c-1.146 1.21-1.792 2.64-2.207 4.05.935-1.14 2.052-2.105 3.257-2.606a5.427 5.427 0 0 1 2.97-.367l.02 2.717 4.32-4.387Z" fill="#fff"/>
                                            </svg> Result shared to class
                                            `)
                                        
                                            shareModal = `<h2>Thanks for Sharing</h2>
                                                                    <p>You have shared your results to the following groups</p>

                                                                    `;
                                            e.shared_to_groups.map( g => {
                                                shareModal += `<div class="group-item"><a href="${g.group_link}">${g.group_name}</a></div>`
                                            })
                                            
                                            
                                        }else{
                                            shareModal = `<p style="
                                                margin-top: 30px;
                                            ">We're sorry, but we can't share your quiz results with other students in your course subject. Please try again later</p>`

                                        }

                                        $("#share-result-modal .share-result-content").html(shareModal)
                                    })
                                    .catch( e => {
                                        console.log("error share", e)
                                    });
            })
        
    });
</script>
<?php get_footer();