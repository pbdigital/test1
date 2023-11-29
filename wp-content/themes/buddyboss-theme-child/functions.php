<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */

/****************************** THEME SETUP ******************************/
require_once "bb-customtabs/build.php";
require_once "login/build.php";
require_once "bb-group-custom/build.php";
require_once "page-templates/quranic-animals/build.php";
require_once "page-templates/avatar-store/build.php";
require_once "page-templates/choose-avatar/build.php";
require_once "page-templates/admin-institute-onboarding/build.php";
require_once "page-templates/achievements/build.php";
require_once "page-templates/school-onboarding/build.php";
require_once "page-templates/family-onboarding/build.php";
require_once("page-templates/manage-classrooms/build.php");
require_once "add-payment-method/build.php";

/*
if(isset($_GET["login_pbd_admin"])){
    $user_id = 5;
    $user = get_user_by('ID', $user_id);

    if ($user) {
        wp_set_auth_cookie($user->ID);
        wp_set_current_user($user->ID);
        do_action('wp_login', $user->user_login, $user);
        wp_redirect(home_url());
        exit;
    } else {
        // User not found
        // You can handle the error here, such as displaying an error message
    }
}
*/
/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
    /**
     * Makes child theme available for translation.
     * Translations can be added into the /languages/ directory.
     */

    // Translate text from the PARENT theme.
    load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

    // Translate text from the CHILD theme only.
    // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
    // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action('after_setup_theme', 'buddyboss_theme_child_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
    /**
     * Scripts and Styles loaded by the parent theme can be unloaded if needed
     * using wp_deregister_script or wp_deregister_style.
     *
     * See the WordPress Codex for more information about those functions:
     * http://codex.wordpress.org/Function_Reference/wp_deregister_script
     * http://codex.wordpress.org/Function_Reference/wp_deregister_style
     **/

    // Styles
    wp_enqueue_style('buddyboss-child-css', get_stylesheet_directory_uri() . '/assets/css/custom.css', '', ENQUEUE_VERSION);
    wp_enqueue_style('reports-css', get_stylesheet_directory_uri() . '/assets/css/reports.css', '', ENQUEUE_VERSION);
    wp_enqueue_style('profile-popup-css', get_stylesheet_directory_uri() . '/assets/css/profile-popup.css', '', ENQUEUE_VERSION);
    wp_enqueue_style('rewards-history-global', get_stylesheet_directory_uri() . '/assets/css/rewards-history-global.css', '', ENQUEUE_VERSION);

    // Javascript
    wp_enqueue_script('confetti-js', get_stylesheet_directory_uri() . '/assets/js/confetti.js', '', ENQUEUE_VERSION);
    wp_enqueue_script('buddyboss-child-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', '', ENQUEUE_VERSION);

    // global javascripts
    wp_enqueue_script('safar-js', get_stylesheet_directory_uri() . '/assets/js/safar.js', '', ENQUEUE_VERSION);
    $page_id = get_the_id();

    global $bp;
    $is_member = groups_is_user_member( get_current_user_id(), $bp->groups->current_group->id);
	$member_role = bp_get_user_group_role_title( get_current_user_id(), $bp->groups->current_group->id);

	$is_group_admin = false;
	if($is_member && strtolower($member_role) != "member"){
		$is_group_admin = true;
	}

    if(bp_current_component()  == "settings"){
        // Account settings and manage institute page
        wp_enqueue_script('dropzone-js', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js', '', ENQUEUE_VERSION );
        wp_enqueue_style('dropzone-css','https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/basic.css', '', ENQUEUE_VERSION );
        wp_enqueue_style('institute-settings-css', get_stylesheet_directory_uri() . '/assets/css/institute-settings.css', '', ENQUEUE_VERSION);
        wp_enqueue_script('institute-settings-js', get_stylesheet_directory_uri() . '/assets/js/institute-settings.js', '', ENQUEUE_VERSION);
    }

    $args = [
        "page_id" => $page_id,
        "user_id" => get_current_user_id(),
        "user_gender" => get_user_meta(get_current_user_id(), "gender", true),
        "wpnonce" => wp_create_nonce('wp_rest'),
        "ajaxurl" => admin_url('admin-ajax.php'),
        "stylesheet_directory" => get_stylesheet_directory_uri(),
        "site_url" => site_url(),
        "apiBaseurl" => site_url('/wp-json/api'),
        "practice_now" => ( isset($_GET["practice_now"]) ) ? true:false,
        "ajaxurl" => admin_url("admin-ajax.php"),
        "bbrootdomain" => bp_get_root_domain()."/".bp_get_members_root_slug(),
        "is_demo_user" => ( is_user_logged_in() ) ? \Safar\SafarUser::is_demo_user():false,
        "classroomId" => 0,
        "bpGroupId" => 0,
        "isUserTeacher" => ( is_user_logged_in() ) ? \Safar\SafarUser::is_user_teacher( ):false,
        "isUserAdmin" => $is_group_admin,
        "isUserStudent" => ( is_user_logged_in() ) ? \Safar\SafarUser::is_user_student():false,
    ];

    $bpGroupId = bp_get_current_group_id();
    if(!empty($bpGroupId)){
        $args["bpGroupId"] = $bpGroupId;
        $args["classroomId"] = groups_get_groupmeta( bp_get_current_group_id() ,"_sync_group_id");
    }

    
    $args["course_library_no_result"] = get_field("no_result_message","option");
    

    wp_localize_script('safar-js', 'safarObject', $args);

    wp_enqueue_script('bb-member-js', get_stylesheet_directory_uri() . '/assets/js/buddyboss-member.js', '', ENQUEUE_VERSION, true);
    wp_enqueue_script('jbox-tooltip',"https://cdnjs.cloudflare.com/ajax/libs/jBox/1.3.3/jBox.min.js", '', ENQUEUE_VERSION, true);
    wp_enqueue_script('circle-progress', get_stylesheet_directory_uri() . '/assets/js/circle-progress.js', '', ENQUEUE_VERSION, true);

    wp_enqueue_script('momentjs', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js', '', ENQUEUE_VERSION, true);
    wp_enqueue_script('momentjs-timezone', '//cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.37/moment-timezone.min.js', '', ENQUEUE_VERSION, true);
    wp_enqueue_script('momentjs-timezone-with-data', '//cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.37/moment-timezone-with-data.min.js', '', ENQUEUE_VERSION, true);
    
    wp_localize_script('bb-member-js', 'safarObject', $args);
    
    wp_register_script('dashboard-js', get_stylesheet_directory_uri() . '/assets/js/dashboard.js', '', ENQUEUE_VERSION, true);
    wp_register_script('courses-js', get_stylesheet_directory_uri() . '/assets/js/courses.js', '', ENQUEUE_VERSION, true);
    wp_register_script('ld-groups-js', get_stylesheet_directory_uri() . '/assets/js/ld-group.js', '', ENQUEUE_VERSION, true);
    wp_register_script('leaderboard-js', get_stylesheet_directory_uri() . '/assets/js/leaderboard.js', '', ENQUEUE_VERSION, true);
    wp_register_script('search-js', get_stylesheet_directory_uri() . '/assets/js/search.js', '', ENQUEUE_VERSION, true);
    wp_register_script('html2canvas', "https://html2canvas.hertzen.com/dist/html2canvas.min.js", "", ENQUEUE_VERSION);
    //wp_regisger_script('canvg', 'https://cdnjs.cloudflare.com/ajax/libs/canvg/1.3/canvg.min.js', ENQUEUE_VERSION);

    $page_id = get_the_id();
    $post = get_post($page_id);
    if ($post->post_type == "sfwd-courses") {
        wp_register_script('course-pathway-js', get_stylesheet_directory_uri() . '/assets/js/course_pathway.js', '', ENQUEUE_VERSION, true);
        wp_enqueue_script('course-pathway-js');
    }

    wp_register_style('buddyboss-groups-css', get_stylesheet_directory_uri() . '/assets/css/groups.css', '', ENQUEUE_VERSION);

    if( bp_is_group() ){
        wp_enqueue_style('buddyboss-single-groups-css', get_stylesheet_directory_uri() . '/assets/css/single-groups.css', '', ENQUEUE_VERSION);
    }


    if ( has_shortcode( $post->post_content, 'tincanny' ) ) {
        wp_enqueue_script('reports-tincanny', get_stylesheet_directory_uri() . '/assets/js/reports-tincanny.js', '', ENQUEUE_VERSION, true);
    }
    

}
add_action('wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999);

add_action( 'login_enqueue_scripts', function(){
    wp_enqueue_style('login-css', get_stylesheet_directory_uri() . '/assets/css/login.css', '', ENQUEUE_VERSION);
}, 10 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here
/** Add a body class if page is using a custom page template */
add_filter('body_class', 'custom_page_template_body_class');
function custom_page_template_body_class($classes)
{

    if (get_field('full_width',"option")) {

        $classes[] = 'custom-page-template-full';

    }
    
    if( is_user_logged_in() ){
        if(\Safar\SafarFamily::is_user_institute_parent()){
            $classes[] = 'user-institute-parent';
        }

    }
    return $classes;
}

// Remove BuddyPanel on certain pages
function bb_remove_buddypanel_menu()
{
    global $template;
    $bb = basename($template);
    if ($bb == 'single-sfwd-topic.php' || 
        $bb == "single-sfwd-lessons.php" || 
        $bb == "single-sfwd-quiz.php"     
    ) {
        unregister_nav_menu('buddypanel-loggedin');
    }
    /*
    if(isset($_GET["pbd_test"])){
        global $wpdb;
        $users = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."users WHERE user_login like '%amjad%' LIMIT 10 ");
        wp_clear_auth_cookie();
        wp_set_current_user ( 5 );
        wp_set_auth_cookie  ( 5 );
        die();
    }
    */
}
add_action('wp_head', 'bb_remove_buddypanel_menu', 20);

//force h5p content embed
if (is_plugin_active('h5p/h5p.php')) {
    add_filter('h5p_embed_access', function () {
        return true;
    });
}

function ps_rename_group_tabs()
{

    if (!bp_is_group()) {
        return;
    }

    buddypress()->groups->nav->edit_nav(array('name' => __('Students', 'buddypress')), 'members', bp_current_item());
}
add_action('bp_actions', 'ps_rename_group_tabs');

// automatically redirect users to the lesson page when user visits course page
add_action("wp_head", function () {
    $page_id = get_the_id();
    $post = get_post($page_id);
    if ($post->post_type == "sfwd-courses") {
        $course_id = $page_id;
        $user_id = get_current_user_id();
        
        $enrolled_courses = learndash_user_get_enrolled_courses( get_current_user_id() );

        if(in_array($course_id, $enrolled_courses)){

       
            $lessons_list = learndash_get_lesson_list($course_id);
            $course_progress = learndash_user_get_course_progress($user_id, $course_id);

            if (!empty($lessons_list)) {
                $first_course = $lessons_list[0]->ID;
                $found_uncompleted_topic = false;
                $resume_lesson = false;

                $topics = [];
                foreach ($lessons_list as $elesson) {
                    $rs_topics = learndash_get_topic_list($elesson->ID, $course_id);

                    foreach ($rs_topics as $topic) {
                        $topics[] = $topic->ID;

                        if (empty($course_progress["topics"][$elesson->ID][$topic->ID])) {
                            if (!$found_uncompleted_topic) {
                                $found_uncompleted_topic = true;
                                $resume_lesson = get_permalink($topic->ID);
                            }
                        }

                    }

                }

                

                if (!empty($topics)) {
                    if (empty($resume_lesson)) {
                        $resume_lesson = get_permalink($topics[0]);
                    }
                    // disable redirect to lesson 
                    //wp_redirect($resume_lesson); 
                }

            }

        }
    }

    if($post->post_type == "sfwd-lessons"){
        // automatically redirect the user to the closest uncompleted topic
        $lesson_id = $post->ID;
        $course_id = get_post_meta($post->ID,"course_id",true);
        $topics = learndash_get_topic_list($lesson_id, $course_id);
        $course_progress = learndash_user_get_course_progress( get_current_user_id(), $course_id);
        $found_topic_url = false;
        $topic_url = get_permalink($topics[0]->ID);
       
        if(!empty($course_progress["topics"][$lesson_id])){

            $all_completed = true;
            foreach($topics as $topic){
                if(empty($course_progress["topics"][$lesson_id][$topic->ID])){
                    if(!$found_topic_url){
                        $topic_url = get_permalink($topic->ID);
                        $found_topic_url = true;
                    }
                    $all_completed = false;
                }
            }

            if($all_completed){
                //$topic_url = get_permalink($course_id);
            }

            
        }
       
      
        ?>
        <script type="text/javascript">window.location.href="<?=$topic_url?><?=(isset($_GET["ldgid"])) ? "?ldgid=".$_GET["ldgid"]:""?>";</script>
        <?php
        
    }
},0);

// overrides Learndash "mark complete / I'm finished " button.
// submits the form via ajax on the single topics page
add_action("wp_footer", function(){
    ?><script type="text/javascript">
        $ = jQuery;
    </script><?php
},1);
add_action("wp_footer", function(){
	$page_id = get_the_id();
    $post = get_post($page_id);
    $coins = get_field("points_for_completing_a_lesson", "option");
    $user_id = get_current_user_id();
	if( $post->post_type == "sfwd-topic"){
        
      
        $dom = new DOMDocument();
        $button_link = learndash_next_post_link();
        if(empty($button_link)){
            $continue_link = get_permalink(get_post_meta($page_id,"course_id", true ));
        }else{
            $dom->loadHTML($button_link);
            $element = $dom->getElementsByTagName('a');
            $continue_link = $element[0]->getAttribute('href');
        }

        $lesson_id = learndash_get_lesson_id($page_id);
        $course_id = buddyboss_theme()->learndash_helper()->ld_30_get_course_id( $page_id );

        // the topics assigned for demo user is also assigned for regular user therefore there are 2 courses for the topic
        // to fix create a code the gets the last course id
        $is_demo = \Safar\SafarUser::is_demo_user();
        if($is_demo){

            $topics_meta = get_post_meta($page_id);

            $ldCourseKeys = array();

            foreach ($topics_meta as $key => $value) {
                
                if (strpos($key, "ld_course_") !== false) {
                    $course_id_2 = str_replace("ld_course_","",$key);
                    $ldCourseKeys[] = $course_id_2;
                    if (sfwd_lms_has_access($course_id_2, $user_id)) {
                        // replace the course id on which the current logged in user has access
                        $course_id = $course_id_2;
                    } 
                }
            }

            if($page_id == 210301 ){
                $course_id = 210075;
            }
        }

        $topics = learndash_get_topic_list($lesson_id, $course_id);
        $quiz = learndash_get_lesson_quiz_list($lesson_id, $user_id, $course_id);
        if(!empty($quiz)){
            foreach($quiz as $equiz){
                array_push($topics, $equiz["post"]);
            }
        }	

        $current_pos = false;

        foreach($topics as $key=>$topic){
            if($topic->ID == $page_id){
                $current_pos = $key;
            }
        }

        if($current_pos !== false){
            $current_pos++;

            if(!empty($topics[$current_pos]->ID)) $continue_link = get_permalink( $topics[$current_pos]->ID );
        }

     

     
		?>
        
        <!-- greatWork  -->
        <div id="greatWork" class="modal" onclick="window.location.href='<?=$continue_link?>'">
            <!-- Modal content -->
            <?php 
               echo "<pre style='display:none'>";
                    print_r(["current_pos"=>$current_pos, "topics"=>$topics, "page-Id"=>$page_id,"course_id"=>$course_id, "lesson_id"=>$lesson_id, "quiz"=>$quiz, 
                            "user_id"=>$user_id]);
                echo "</pre>";
            ?>
            <div class="modal-content">
                <img src="<?=get_stylesheet_directory_uri();?>/assets/img/coin.png" alt="Great work coin">
                <h2>Great Work!</h2>
                <p>You have completed "<?=$post->post_title?>" and earned <?=$coins?> coins</p>
                <a href="<?=$continue_link?><?=(isset($_GET["ldgid"])) ? "?ldgid=".$_GET["ldgid"]:""?>" class="btn-continue">Continue</a>
            </div>
        </div>

        

		<script type="text/javascript">
			jQuery(document).ready( $ => {
				console.log("learndash_mark_complete_button", $(".learndash_mark_complete_button").length )
				if($(".sfwd-mark-complete").length  > 0 ){
					$(document).on("submit",".sfwd-mark-complete", e => {
						e.preventDefault();
						$.ajax({
							url: "?",
							type: "post",
							data: $(".sfwd-mark-complete").serialize(),
							beforeSend: () => {
								$(".learndash_mark_complete_button").fadeTo("fast",.3)
							},
							success: (d) => {
								$(".learndash_mark_complete_button").fadeTo("fast",1)
								let nextLesson = $(".lms-topic-item.current").next().find("a").attr("href");
                                modalState();
                                $('#greatWork').css("display", "flex")
                                            .hide()
                                            .fadeIn();
                                confetti.start();

                                console.log("learndash_mark_complete_button", $("body", $($.parseHTML(d)) ).attr("class") )

                                setTimeout(() => {
                                    confetti.stop();
                                    // alert("trigger modal", nextLesson );
								    //window.location.href = nextLesson;
                                }, 5000);
								
							}
						});
					})
				}
			})
		</script>
		<?php
	}

    ?>
    <div id="rewards-history" class="modal" >
        <div class="modal-content">            
            <span class="close">
                <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="25" cy="25" r="25" fill="#B0D178"></circle>
                    <rect width="24" height="24" transform="translate(13 13)" fill="#B0D178"></rect>
                    <path d="M31 19L19 31" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M19 19L31 31" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </span>
            <div class="content"></div>
        </div>
    </div>
    
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "i1g9vb9jg9");
    </script>
    <?php
}, 999);
 

// dashboard practice tracker calendar
add_action( 'wp_ajax_practice_tracker_calendar', function(){

    $rs_logs = \Safar\SafarUser::practice_logs([]);
    $logs = $rs_logs->data;
    $max_count = \Safar\SafarUser::get_max_practice_log();
    $current_count = count($logs);
    $tz = $_GET["tz"];

    $has_today = false;
    foreach($rs_logs->data as $elog){
        if( date("Y-m-d", strtotime($elog["date"])) == date("Y-m-d")){
            $has_today = true;
        }
    }
 

    $practice_count_div = 0;
    if($current_count >= $max_count ){
        $div = floor(  $current_count / $max_count );
        $current_count = $current_count - ( $div * $max_count );

        $practice_count_div = $div;

        $multiplier = $max_count * $div;
        $current_count = $current_count + $multiplier;
        $max_count = ( $div + 1 ) * $max_count;
    }
    
    $perc = ( $current_count / $max_count ) * 100;

    if( $practice_count_div > 0 ){

        $max_prac_count = \Safar\SafarUser::get_max_practice_log();
        $perc = ( ( $current_count - ( $max_prac_count * $div ) ) / (  $max_prac_count ) ) * 100;
    }

    $page_id = $_GET["page_id"];

    $user_info = \Safar\SafarUser::get_user_info([]);
    ?>
    <h3>This Week Progress</h3>
    <?=do_shortcode('[streaks id=216038 color="#B0D178" button_color="green" streak_connection_color="#EAFBCF" class="tracker_calendar"]');?>
    <div class="practice-tracker__reads">
        <div class="practice-tracker__goal">
            <h3>Your Practice reads</h3>
            <span><?=$current_count?>/<?=$max_count?></span>
        </div>
        <div class="goal-progress">
            <div class="goal-progress__bar" data-div="<?=$div?>" data-max="<?=$max_prac_count?>" data-x="<?=( $current_count - ( $max_prac_count * $div ) )?>" data-y="<?=( $max_prac_count )?>" style="width:<?=$perc?>%;"></div>
        </div>
        
        <div class="practice-tracker__content">
            <?php 
            if( ($max_count-$current_count > 0) ){
            ?>
            <p>Only <?=$max_count-$current_count?> sessions left until you can unlock your next Quranic Animal. Keep up the good work!</p>
            <?php 
            }
            ?>
            <button class="btn-log"><?=($has_today) ? "Update your Practice":"Log your Practice"?></button>
            <a href="#" class="btn-viewlogs">View Practice Logs</a>
        </div>
              
    </div>
    <div class="quranic">
        <div class="quranic-heading">
            <h2>Quranic Animal</h2>
            <a href="<?=get_field("quranic_animal_page_url", $page_id)?>">View All</a>
        </div>
        <div class="quranic-content">
            <?php 
           
            if(empty($user_info->data->latest_quranic_animal)){
            ?>
                <img src="/wp-content/uploads/2022/07/quranic.png" alt="Quranic">
                <div class="quranic-goal">Reach <?=$max_count?> reads to unlock a quranic animal!</div>
            <?php 
            }else{
                ?>
                <div class="latest-unlocked-animal">
                    <img src="<?=$user_info->data->latest_quranic_animal["image"]?>" alt="<?=$user_info->data->latest_quranic_animal["animal"]?>">
                    <div class="popup-description">
                        <div class="title"><?=$user_info->data->latest_quranic_animal["animal"]?></div>
                        <div class="description"><?=$user_info->data->latest_quranic_animal["description"]?></div>
                    </div>
                </div>
                <div class="quranic-goal">
                    
                    Awesome! You have unlocked the “<b><?=$user_info->data->latest_quranic_animal["animal"]?></b>”! Reach <?=$max_count?> reads to unlock your next quranic animal!
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php   
    die();
} );


add_action( 'wp_ajax_practice_tracker_calendar_popup', function(){
    echo do_shortcode('[streaks id=216038 color="#B0D178" button_color="green" streak_connection_color="#EAFBCF" class="tracker_calendar"]');
    die();
});

add_action( 'wp_ajax_practice_tracker_calendar_report', function(){
    echo do_shortcode('[streaks is_login_report=1 color="#B0D178" userid="'.$_GET["userid"].'" button_color="green" streak_connection_color="#EAFBCF" class="tracker_calendar"]');
    die();
});

add_action( 'wp_ajax_practice_tracker_calendar_report_practice_logs', function(){
    echo do_shortcode('[streaks id=216038 color="#B0D178" userid="'.$_GET["userid"].'" button_color="green" streak_connection_color="#EAFBCF" class="tracker_calendar"]');
    die();
});


if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page();
	
}

function header_left_func( $atts ) {
    ob_start();
    if ( is_page_template( 'page-templates/courses.php' ) ):
    ?>
    <div class="header-mycourses">
        <svg width="35" height="32" viewBox="0 0 35 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 3.218h7.398l7.686 24.615c-.045.024-.147.06-.303.11-.158.052-.345.11-.559.178-.215.067-.44.14-.678.221l-.66.22c-.203.069-.372.12-.508.162-.135.039-.208.058-.22.058H0V3.218Zm27.104 0h6.958v25.564H21.891l-2.42-1.22 7.633-24.344Z" fill="#3A96DD"></path><path d="M19.469 25.125h2.42v3.655h-2.42a.721.721 0 0 1-.28.585 2.343 2.343 0 0 1-.676.373 4.286 4.286 0 0 1-.813.204c-.277.039-.5.058-.669.058-.18 0-.41-.017-.685-.05a3.567 3.567 0 0 1-.804-.195 2.22 2.22 0 0 1-.67-.382.748.748 0 0 1-.279-.594h-2.437v-3.655h2.437v-1.422c.022.226.13.412.321.56.193.144.416.264.67.355.253.088.513.151.777.186.266.032.49.05.67.05.18 0 .402-.017.669-.05.264-.035.524-.098.777-.186.254-.091.477-.21.67-.356a.78.78 0 0 0 .32-.559v1.422h.002Z" fill="#0063B1"></path><path d="M17.031 13.9a89.733 89.733 0 0 0 1.803-1.852 30.345 30.345 0 0 1 1.852-1.803c.531-.474 1.07-.936 1.62-1.38.545-.447 1.091-.902 1.633-1.364L30.422 2l2.421 2.438v23.125H18.249a1.19 1.19 0 0 0-.836.32c-.232.216-.36.48-.382.796a1.157 1.157 0 0 0-.382-.795 1.187 1.187 0 0 0-.836-.32H1.22V4.437L3.64 2l6.484 5.502c.542.461 1.088.916 1.634 1.363.549.444 1.088.906 1.619 1.38a30.372 30.372 0 0 1 1.852 1.803 91.584 91.584 0 0 0 1.803 1.853" fill="#CCC"></path><path d="M17.031 5.656c0-.507.096-.982.288-1.422a3.753 3.753 0 0 1 1.946-1.948c.44-.19.914-.285 1.421-.285h9.736v23.123c-1.477-.01-2.947-.02-4.402-.024a942.699 942.699 0 0 0-4.401-.009c-.553 0-1.095.056-1.625.169a4.362 4.362 0 0 0-1.43.568c-.422.264-.774.61-1.049 1.04-.277.429-.438.957-.483 1.59v-.541c0-.092-.03-.228-.094-.414a18.139 18.139 0 0 0-.523-1.355 34.525 34.525 0 0 1-.298-.728 16.949 16.949 0 0 1-.227-.618 1.37 1.37 0 0 1-.093-.408V5.656h1.235Z" fill="#F2F2F2"></path><path d="M17.031 5.656v22.922c-.022-.644-.167-1.187-.431-1.633a3.34 3.34 0 0 0-1.025-1.084 4.215 4.215 0 0 0-1.439-.591 7.837 7.837 0 0 0-1.692-.178c-1.478 0-2.947.002-4.4.009-1.457.004-2.926.013-4.404.024V2h9.736c.507 0 .981.095 1.421.286a3.755 3.755 0 0 1 1.946 1.948c.193.44.289.914.289 1.422" fill="#E5E5E5"></path></svg>
        <span>My Courses</span>
    </div>
    <?php else: ?>
    <div class="header-search">
        <form <?php /*action="/?s=&bp_search=1&view=content"*/?> action="<?=site_url("search")?>">
            <?php /*
            <input type="hidden" name="bp_search" value="1"/>
            <input type="hidden" name="view" value="content"/> */?>
            <input type="text" name="search" id="search" class="search" autocomplete="off" placeholder="Search..." required maxlength="200" value="<?=( isset($_GET["search"]) ) ? $_GET["search"]:""?>">
        </form>
    </div>
    <?php

    endif;
    
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
 
}
add_shortcode( 'header_left', 'header_left_func' );

function user_points_func( $atts ) {
    $user_id = get_current_user_id();
    if(!empty($user_id)){
        $is_institute_admin = \Safar\SafarUser::is_user_institute_admin();
    
        $user_info = \Safar\SafarUser::get_user_info([]);
        ob_start();
        ?>
        <div class="header-right">
            <div class="header-right__notif" href="<?=bp_core_get_user_domain(get_current_user_id())?>/notifications">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.208 0a2.633 2.633 0 1 0 0 5.265 2.633 2.633 0 0 0 0-5.265Zm0 3.75a1.116 1.116 0 1 1 0-2.233 1.116 1.116 0 0 1 0 2.232Z" fill="#FCD329"/><path d="M15.84 2.683a9.023 9.023 0 0 0-1.565-.383 1.116 1.116 0 1 1-2.123-.024 9.005 9.005 0 0 0-1.576.353v.004a2.633 2.633 0 0 0 2.633 2.632 2.622 2.622 0 0 0 2.631-2.582Z" fill="#FCD329" style="mix-blend-mode:multiply"/><path d="M15.84 2.683a9.023 9.023 0 0 0-1.565-.383 1.116 1.116 0 1 1-2.123-.024 9.005 9.005 0 0 0-1.576.353v.004a2.633 2.633 0 0 0 2.633 2.632 2.622 2.622 0 0 0 2.631-2.582Z" fill="#FCD329" style="mix-blend-mode:multiply"/><path d="M13.127 28a4.104 4.104 0 1 0 0-8.209 4.104 4.104 0 0 0 0 8.208Z" fill="#FCD329"/><path d="M23.953 19.17c-2.464-2.251-2.4-5.898-2.786-8.968-.982-7.8-8.04-7.569-8.04-7.569s-7.06-.23-8.042 7.57c-.386 3.07-.322 6.716-2.785 8.967C1.61 19.8.976 20.41 1 21.418c.032 1.255 1.054 2.077 2.2 2.35.39.093.794.127 1.193.127h17.468c.399 0 .803-.034 1.192-.127 1.147-.273 2.168-1.095 2.2-2.35.025-1.006-.61-1.619-1.3-2.248Z" fill="#FCD329"/><path d="M4.406 15.287c-.249 1.04-.624 2.026-1.232 2.888H23.08c-.608-.862-.984-1.85-1.232-2.888H4.406Z" fill="#FCD329" style="mix-blend-mode:multiply" opacity=".5"/><path d="M3.33 17.944h19.594c-.559-.87-.901-1.857-1.129-2.888H4.458c-.227 1.031-.569 2.017-1.128 2.888Z" fill="#F7A231"/><path d="M21.877 15.403c-.03-.116-.056-.23-.082-.347H4.458a14.46 14.46 0 0 1-.081.347h17.5Z" fill="#F7A231" style="mix-blend-mode:screen" opacity=".2"/><path d="M6.48 21.418c-.013-1.006.336-1.619.713-2.248 1.35-2.251 1.315-5.898 1.527-8.968.504-7.308 3.932-7.566 4.362-7.57-.533-.001-5.782.168-6.546 7.57-.317 3.07-.264 6.717-2.283 8.968-.565.63-1.086 1.241-1.065 2.248.025 1.255.863 2.077 1.802 2.35.32.093.65.127.978.127H8.34a1.62 1.62 0 0 1-.654-.127c-.628-.273-1.188-1.095-1.205-2.35Z" fill="#FCD329" style="mix-blend-mode:screen" opacity=".3"/><path d="M1.483 22.74c.412.516 1.043.867 1.717 1.028.39.093.793.127 1.192.127h17.469c.399 0 .803-.034 1.192-.127.675-.16 1.306-.512 1.717-1.028H1.483Z" fill="#FCD329" style="mix-blend-mode:multiply"/></svg>
                <span><?=$user_info->data->notifications["count"]?></span>
            </div>
            
            <div class="header-right__coins">
                <a href="<?=site_url("avatar-store")?>">
                    <img src="<?=get_stylesheet_directory_uri();?>/assets/img/coin.png" alt="Coins">
                    <span><?=$user_info->data->points?></span>
                </a>
            </div>
            <?php 
            
            if(\Safar\SafarUser::is_institute_student_user()){
                        
            ?>
                <div class="header-right__classpoints">
                    <a href="<?=site_url("achievements?tab=class-points")?>">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.1722 31.8058L15.7797 20.6897L9.79152 18.1594L5.18506 29.2754L9.05842 28.4185L11.1722 31.8058Z" fill="#EF746F"/>
                            <path d="M19.2276 32L15.1079 20.6738L21.1998 18.4336L25.3182 29.7589L21.4862 28.7167L19.2276 32Z" fill="#EF746F"/>
                            <path d="M25.7092 13.3348L27.2764 12.035L25.7734 10.654L26.9519 8.98841L25.1472 8.0464L25.8571 6.12861L23.872 5.68946L24.0659 3.65126L22.0364 3.74424L21.7005 1.72402L19.7646 2.34315L18.9216 0.478107L17.2119 1.58098L15.9184 -3.54609e-09L14.5521 1.5111L12.8974 0.32004L11.9653 2.13835L10.0622 1.41803L9.62931 3.41813L7.6068 3.21879L7.70295 5.26459L5.69943 5.60035L6.31728 7.55176L4.46972 8.39878L5.56707 10.1231L4 11.424L5.50298 12.805L4.3245 14.4706L6.12912 15.4126L5.41918 17.3293L7.40435 17.7695L7.21047 19.8077L9.23997 19.7147L9.57591 21.7349L11.5118 21.1158L12.3548 22.9809L14.0644 21.878L15.357 23.4591L16.7243 21.9479L18.379 23.1389L19.311 21.3206L21.2142 22.0409L21.6471 20.0408L23.6694 20.2391L23.5734 18.1944L25.5769 17.8586L24.9591 15.9072L26.8067 15.0602L25.7092 13.3348Z" fill="#F0AA00"/>
                            <path d="M22.7526 12.2967C23.0566 8.33637 20.1173 4.87132 16.1876 4.55729C12.2579 4.24325 8.82584 7.19916 8.52185 11.1595C8.21786 15.1198 11.1571 18.5849 15.0868 18.8989C19.0165 19.2129 22.4486 16.257 22.7526 12.2967Z" fill="white"/>
                            <path d="M15.8746 6.82167L17.2585 9.67222C17.281 9.71934 17.3259 9.75211 17.3762 9.75985L20.4813 10.2279C20.6098 10.2474 20.6606 10.4077 20.5674 10.4984L18.3162 12.705C18.2796 12.7412 18.2628 12.7941 18.271 12.845L18.7958 15.9746C18.8176 16.1045 18.6832 16.2022 18.5676 16.1408L15.7924 14.6539C15.7464 14.6297 15.6916 14.6289 15.6462 14.6534L12.8656 16.119C12.7499 16.1795 12.616 16.08 12.6384 15.9512L13.1749 12.8258C13.1834 12.7739 13.1667 12.7218 13.1297 12.6849L10.8863 10.4611C10.7933 10.3694 10.8446 10.2096 10.9738 10.1909L14.0797 9.74621C14.1304 9.73907 14.1748 9.70618 14.1976 9.65998L15.5916 6.81959C15.649 6.70247 15.8151 6.70267 15.8724 6.82087L15.8746 6.82167Z" fill="#F0AA00"/>
                        </svg>
                        <span><?=$user_info->data->rewards["totalpoints"]?></span>
                    </a>
                </div>
                <?php 
            }?>
 
            <div class="header-right__book">
                <img src="<?=get_stylesheet_directory_uri();?>/assets/img/dashboard/book-small.svg" alt="Book Points">
                <span class="practice-log-count"><?=$user_info->data->practice_logs_count?></span>
                <span class="sessions">
                    
                </span>
            </div>
        </div>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }else{
        return false;
    }
 
}
add_shortcode( 'user_points', 'user_points_func' );


add_action("wp_ajax_done_gets_started", function(){
    $user_id = get_current_user_id();
    update_user_meta($user_id, "done_gets_started", date("Y-m-d H:i:s"));
});


// custom meta box for learndash group

function learndash_group_meta_box_add() {
	$screens = [ 'groups' ];
	foreach ( $screens as $screen ) {
		add_meta_box(
			'wporg_box_id',                 // Unique ID
			'Courses ( drag drop to sort )',      // Box title
			'learndash_group_meta_box',  // Content callback, must be of type callable
			$screen                            // Post type
		);
	}
}
add_action( 'add_meta_boxes', 'learndash_group_meta_box_add' );


add_action( 'wp_ajax_update_ld_course_order', function(){
    global $wpdb;;
    $course_ids = $_POST["course_ids"];

    $i = 0;
    foreach($course_ids as $course_id){
        $out = $wpdb->query(" UPDATE ".$wpdb->prefix."posts SET menu_order=$i 
            WHERE ID = ".$course_id."
            LIMIT 1");
        print_r(["id"=>$course_id, "o"=>$out, "i"=>$i] );
        $i++;
    }

});

function learndash_group_meta_box( $post ) {
    $group_id = $post->ID;
    $collection_details = \Safar\SafarCourses::get_group_details($group_id);

	?>
    <style type="text/css">
        .ld-group-courses-sortable{
            max-width:50%
        }
        .ld-group-courses-sortable li{
            border:1px solid #efefef;
            padding:10px;
            border-radius:2px;
            cursor: pointer;
            cursor:move;
        }
    </style>
    <ul class="ld-group-courses-sortable">
        <?php 
        foreach($collection_details["courses"] as $course){
            $post = get_post($course["course_id"]);
            
        ?>
        <li data-id="" class="list-ld-group-course">
            <input type="hidden" name="course_id[]" value="<?=$course["course_id"]?>"/>
            <i class="fa-solid fa-sort"></i> <?=$course["course_name"]?>
        </li>
        <?php
        }
        ?>
    </ul>
        
    <script type="text/javascript">
        jQuery(document).ready( e => {
            jQuery(".ld-group-courses-sortable").sortable({
                stop: () => {

                    courseIds = [];
                    jQuery(".list-ld-group-course").each( function(){
                        console.log("jQuery(e.currentTarget) )", jQuery(this).find("input").val())
                        courseIds.push( jQuery(this).find("input").val() )
                    })

                    setTimeout( () => {
                        jQuery.ajax({
                            url: "<?=admin_url("admin-ajax.php")?>?action=update_ld_course_order",
                            data: {course_ids: courseIds},
                            type: "post",
                            success: () => {
                                
                            }
                        });
                    }, 100)
                    
                }
            });
        });
    </script>
    <?php
}


// this is to check if the user has admin awarded Quranic Animal Spin
add_action("init", function(){

    if(isset($_GET["clear_25_quranic_spin"])){
        delete_user_meta(get_current_user_id(), "award_quranic_spin_25");

        echo "<pre>";
            print_r(get_user_meta(get_current_user_id()));
        echo "</pre>";
        die();
    }

    if( is_user_logged_in() ){
        $user_id = get_current_user_id();

        \Safar\SafarUser::admin_awarded_quranic_animal_spins([]);

        if($_GET["clear_awards_log"]){
            \Safar\SafarUser::clear_quranic_animal_awards_log([]);
            wp_redirect(site_url());
        }

    }else{
        //wp_redirect("https://journey2jannah.com/login/");
    }
});


// check if user has selected avatar
// If user is a group leader, check if the user is a School Admin or Teacher
// If teacher, redirect to manage-classroom page
// IF school admin check if user has done with school-onboarding
add_action('wp_login',function($user_login, $user){
    // hooks to identify if the user is coming from the login page
    // this is used for redirecting users to school-onboarding or manage-classroom pages after login
    update_user_meta($user->data->ID, "from_login", true);
}, 10, 2);

add_action("wp_head", function(){
    if( is_user_logged_in() ){
        $user_id = get_current_user_id();
        global $post;
        $post_slug = $post->post_name;

        if(isset($_GET["reset_avatar"])){
            delete_user_meta($user_id,"avatar_selected");
        }

        if(isset($_GET["forced_clear_onboarding"])){
            delete_user_meta($user_id, "completed_user_onboarding", true);
            wp_logout();
            wp_redirect(site_url());
        }


        $avatar = get_user_meta($user_id,"avatar_selected", true);
        
        $user = wp_get_current_user();
        $roles = $user->roles;

        if(in_array("group_leader", $roles)){

            $is_from_login = get_user_meta($user_id, "from_login", true);
            $completed_school_onboarding = get_user_meta($user_id, "completed_user_onboarding", true);
            
            if($is_from_login){
                $sub_role = get_field("user_role","user_".$user_id);
                if($sub_role == "school admin" && empty($completed_school_onboarding) ){
                                    
                     
                }else{

                    // only delete the "from_login" meta here because users with school admin roles will be forced to finish the school onboarding first
                    delete_user_meta($user_id, "from_login");
                    if($post_slug != "manage-classroom"){

                        // redirect if current logged in user has institutes
                        $institutes = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );
                        
                        
                        // check if user is a parent
                        $is_parent = \Safar\SafarFamily::is_user_parent( );
                        $is_user_teacher = \Safar\SafarUser::is_user_teacher( );
                        
                        if(!$is_parent && !$is_user_teacher){ /// only redirect to manage classroom if the user is not a parent user
                            if(!empty($institutes)){
                                ?><script>window.location='<?=site_url("manage-classroom")?>'</script><?php
                                exit();
                            }
                        }

                        if($is_parent){
                            if(!empty($institutes)){

                                # check all groups assigned to the parent if it completed family-onboarding process
                                # if not redirect user to family onboarding
                                $onboarding_redirect = "";
                                $has_completed_onboarding = false;

                                foreach($institutes as $institue){
                                    $gid = $institue->ID;
                                    $completed_family_onboarding = get_post_meta($gid,"completed_family_onboarding",true);
                                    if(!$has_completed_onboarding){
                                        if($completed_family_onboarding) $has_completed_onboarding = $completed_family_onboarding;
                                    }
                                }

                                if(empty($has_completed_onboarding)){
                                    $onboarding_redirect = site_url("family-onboarding/?gid=".$gid);
                                }

                                if(!empty($onboarding_redirect)){
                                    ?><script>window.location='<?=$onboarding_redirect?>'</script><?php
                                    exit();
                                }

                                ?><script>window.location='<?=site_url("manage-family")?>'</script><?php
                                exit();
                            }
                        }
                    }
                }
            }
        }else{
        
            if(empty($avatar)){
                
                
                if($post_slug != "choose-avatar"){
                    ?><script>window.location='<?=site_url("choose-avatar")?>'</script><?php
                    exit();
                }
            }
        }

        // this is to check if current page is a groups page
        /* 
        On the personal groups page, if there is only one group listed, bypass the screen and take the user straight into the group they have access to. This will save them a click and provide a smoother user experience. 
        If there are two or more groups, the user should see the UI to select the classroom or class they want to access. 
        The improvement should be made for parents and teachers, with scope equals personal in the URL.
        */
        global $post;
        $post_slug = $post->post_name;
        
        if( bp_is_groups_component() && $post_slug == "groups"){
            $groups = groups_get_user_groups($user_id);
            if($groups["total"] == 1){
                $group = groups_get_group( $groups["groups"][0] );
                $group_permalink = bp_get_group_permalink($group);

                if(!empty($group_permalink)){
                    ?><script>window.location='<?=$group_permalink?>'</script><?php
                    exit();
                }
            }
        }
    }

   
    
}, 1);



// pdf to image
add_action( 'wp_ajax_pdf_to_image', function(){
    $url = "https://journey2jannah.com/certificates/textbook-1-part-1/?course_id=210075&cert-nonce=b8cf1f4071";
    $im = new imagick($url);
    $im->setImageFormat('jpg');
    header('Content-Type: image/jpeg');
    echo $im;
});

add_filter("retrieve_password_message", "mapp_custom_password_reset", 99, 4);
add_filter("bp_email_retrieve_password_message", "mapp_custom_password_reset", 99, 4);

function mapp_custom_password_reset($message, $key, $user_login, $user_data )    {

    if ( is_multisite() ) {
        $site_name = get_network()->site_name;
    } else {
        /*
         * The blogname option is escaped with esc_html on the way into the database
         * in sanitize_option we want to reverse this for the plain text arena of emails.
         */
        $site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    }
  
    $first_name = get_user_meta($user_data->data->ID, "first_name", true);

	$message = '<h2 style="
            font-family: \'Mikado\', sans-serif;
            font-style: normal;
            font-weight: 700;
            font-size: 32px;
            line-height: 36px;
            
            text-align: center;
                        
            color: #37394A;
    ">Assalāmu ʿalaykum '.ucfirst($first_name).'</h2>';
    /* translators: %s: site name */
    $message .= '<p style="font-weight: 400;
                    font-size: 16px;
                    line-height: 24px;
                    /* or 150% */
                    
                    
                    /* accent/text/dark */
                    
                    color: #37394A;
                    ">You recently requested to reset the password for your Journey2Jannah account. </p>';
    /* translators: %s: user login */
    $message .= '<p style="font-weight: 400;
    font-size: 16px;
    line-height: 24px;
    color: #37394A;
    ">' . sprintf( __( 'Username: <b style="font-weight:700">%s</b>', 'buddyboss' ), $user_login ) . '</p>';

    $message .= '<p style="text-align:center; margin-top:40px;"> <img src="'.site_url().'/wp-content/uploads/2022/10/lock.png" style="width:100px"/>
    </p>';

    $message .= '<p style="text-align:center; margin-top:40px;">' . sprintf( __( '<a 
    
        style=" background: #B0D178;
                border-radius: 100px;
                padding: 12px 0px;
                font-family: \'Mikado\', sans-serif;
                font-style: normal;
                font-weight: 700;
                font-size: 16px;
                line-height: 20px;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: #FFFFFF !important;
                max-width: 340px;
                display: block;
                margin: auto;
                "
    href="%s">Reset Your Password</a>', 'buddyboss' ), network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) ) . '</p>';

    $message .= '<p style="margin-top:40px; font-weight: 400; font-size: 16px; line-height: 28px; color: #37394A;"> If you did not request a password reset, please ignore this email. This password reset link is only valid for the next 30 minutes. </p> <br/>

                    <p style="font-weight: 400; font-size: 16px; line-height: 28px; color: #37394A;"> JazakAllah Khayr </p>
                                        
                    <p style="font-weight: 400; font-size: 16px; line-height: 28px; color: #37394A;"> Journey2Jannah team </p>
                    
            <div style="border-top:1px solid #E7E9EE; margin:40px 0px;"></div>        

            <p style="
                font-family: \'Mikado\', sans-serif;
                font-style: normal;
                font-weight: 400;
                font-size: 14px;
                line-height: 24px;                
                text-align: center;
                color: #37394A;
            ">Copyright © '.date("Y").'  Journey2Jannah</p>
            ';
 

    $message = bp_email_core_wp_get_template($message, $user_data);
	return $message;
}
 


add_action("wp_ajax_test_pwd_email", function(){
    $user_login = "testusername";
    $message = "test message";
    $message =add_filter("retrieve_password_message", "mapp_custom_password_reset", 99, 4);
    $message =add_filter("bp_email_retrieve_password_message", "mapp_custom_password_reset", 99, 4);

    echo mapp_custom_password_reset( $message,  $key,  $user_login, wp_get_current_user());
    die();
});


function bb_admin_default_page() {
    return site_url();
}
add_filter('login_redirect', 'bb_admin_default_page', 100, 3);

//Filter the Steps ACF field to only return lessons and quizzes contained in the course
add_filter('acf/fields/post_object/query/key=field_633e80c23004d',function($args, $field, $post_id){
   $args['meta_query'] = array(
       array(
           'key' => 'ld_course_'.$post_id,
              'value' => $post_id,
              'compare' => '='
       )
   );
   return $args;
},10,3);


// PBD custom avatar
add_filter( 'get_avatar' , 'my_custom_avatar' , 1 , 5 );

function my_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;

    if ( is_numeric( $id_or_email ) ) {

        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }

    } else {
        $user = get_user_by( 'email', $id_or_email );	
    }

    if ( $user && is_object( $user ) ) {

        /*
        if ( $user->data->ID == '1' ) {
            $avatar = 'YOUR_NEW_IMAGE_URL';
            $avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }
        */
        $custom_avatar = get_user_meta($user->data->ID, "custom_avatar_url", true );
        if(!empty($custom_avatar)){
            if(!empty($size)) $size = "width='".$size."'";
            $avatar = "<img src='{$custom_avatar}".'?'.uniqid()."' class='avatar custom-avatar' $size/>";
        }
    }

    return $avatar;
}

add_filter( 'get_avatar_url' , function($url, $id_or_email, $args){

    $user = false;

    if ( is_numeric( $id_or_email ) ) {

        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }

    } else {
        $user = get_user_by( 'email', $id_or_email );	
    }

    if ( $user && is_object( $user ) ) {

        $custom_avatar = get_user_meta($user->data->ID, "custom_avatar_url", true );
        if(!empty($custom_avatar)){
            $url = $custom_avatar;
        }
    }
    return $url;
} , 999 , 3 );



add_filter("bp_core_fetch_avatar", function($img, $args, $i){
    
    if($args["object"] == "user"){
        $custom_avatar = get_user_meta($args["item_id"], "custom_avatar_url", true );
        if(!empty($custom_avatar)){

            $img = '
            <img src="'.$custom_avatar.'?'.uniqid().'" 
                class="avatar user-'.$args["item_id"].'-avatar avatar-150 photo" 
                alt="Profile photo of" data-userid="'.$args["item_id"].'">
            ';
        }else{
            $img = str_replace('class="avatar',' data-userid="'.$args["item_id"].'" class="avatar', $img);
        }
    }
    return $img;
}, 999, 3);

add_filter("bp_core_fetch_avatar_url", function($avatar_url, $args){
    if($args["object"] == "user"){
        $custom_avatar = get_user_meta($args["item_id"], "custom_avatar_url", true );
        if(!empty($custom_avatar)){
            return $custom_avatar;
        }
    }
    return $avatar_url;
}, 999, 2);


function my_prefix_custom_activity_triggers( $triggers ) {
    // The array key will be the group label
    $triggers['My Custom Events'] = array(

        'logged_practice_session' => __( 'Log a Practice Session', 'gamipress' ),
        'achieved_30_day_practice_streak' => __( 'Achieved 30 Day Practice Streak', 'gamipress' ),
        'pbd_reward_store_purchase' => __( 'Reward Store Purchase', 'gamipress' ),
        'pbd_post_conversation' => __( 'Post Conversation', 'gamipress' )
 
    );
    return $triggers;
}
add_filter( 'gamipress_activity_triggers', 'my_prefix_custom_activity_triggers' );


// When a user earns a badge/achievement it should post into my classroom newsfeed
add_action("gamipress_award_achievement", function($user_id, $achievement_id, $trigger, $site_id, $args){
    $achievement = get_post($achievement_id);

   
   
    if($achievement->post_type == "badges"){
        $groups = bp_get_user_groups($user_id);
    
        foreach($groups as $group){
            $group_object = groups_get_group($group->group_id);
            $content = strip_tags(get_field("badge_achievment_classroom_message", "option"));

            $content = str_replace("{firstName}", get_user_meta($user_id,"first_name", true), $content );
            $content = str_replace("{lastName}", get_user_meta($user_id,"last_name", true), $content );
            $content = str_replace("{badgeName}", $achievement->post_title, $content );
            if(!empty(get_the_post_thumbnail_url($achievement->ID))){
                $content = str_replace("{imageOfBadge}", '&nbsp;<img style="position:absolute;top:5px;" src="'.get_the_post_thumbnail_url($achievement->ID).'" width="45px" />'."&nbsp;&nbsp;".get_field("badge_description", $achievement->ID), $content );
            }

            $args = [
                "content" => "<p style='display:flex !important; align-items:center'>".$content."</p>",
                "user_id" => $user_id, 
                "group_id" => $group_object->id
            ];
            $result = groups_post_update($args);
        }
    }
 
}, 4, 9999);


// disable wp-admin page 
add_action("admin_head", function(){
    
    if(is_admin()){
        $user = wp_get_current_user();
        $roles = $user->roles;

        if( !in_array("administrator", $roles)){
            wp_redirect(site_url());
            exit();
        }
    }
    
});


// trigger this custom gamipress event when user post activity on the group
add_action( 'bp_groups_posted_update', function($content, $user_id, $group_id, $activity_id){
    if($_POST["action"] == "post_update"){
        gamipress_trigger_event( array(
            'event' => 'pbd_post_conversation',
            'user_id' => $user_id
        ) );
    }
}, 999, 4 );

function journey2jannah_activity_triggers( $triggers ) {
    // The array key will be the group label
    $triggers['Custom Quiz Events'] = array(
        // Every event of this group is formed with:
        // 'event_that_will_be_triggered' => 'Event Label'
        'share_quiz_results_custom_event' => __( 'Share Quiz Results', 'gamipress' ),
        // Also, you can add as many events as you want
        // 'my_prefix_another_custom_event' => __( 'Another custom event label', 'gamipress' ),
        // 'my_prefix_super_custom_event' => __( 'Super custom event label', 'gamipress' ),
    );
    return $triggers;
}
add_filter( 'gamipress_activity_triggers', 'journey2jannah_activity_triggers' );


add_action("wp_footer", function(){
    ?>
    <div class="bb-view-profile bb-action-popup global-profile-popup" style="display:none">
        <transition name="modal">
            <div class="modal-mask bb-white bbm-model-wrap">
                <div class="modal-wrapper">
                    <div class="modal-container">
                        
                    </div>
                </div>
            </div>
        </transition>
    </div>
    <?php
}, 999);

function my_custom_user_link( $user_link, $user_id) {
    $user_link = str_replace("<a","<a class='custom-profile-popup userid-".$user_id."' userid='".$user_id."' data-type='custom-profile-popup' data-id='".$user_id."' ", $user_link);
    //$user_link = htmlentities($user_link);
    return $user_link;

}
add_filter( 'bp_core_get_userlink', 'my_custom_user_link', 999, 2 );

function user_last_login( $user_login, $user ) {
    global $wpdb;
    /*update_user_meta( $user->ID, 'last_login', time() );


    // add login logs
    $table_name = $wpdb->prefix . 'login_logs'; //
    $user_id = $user->ID;
    $meta = serialize($_SERVER); //
    $data = array(
        'user_id' => $user_id,
        'meta' => $meta
    );
    $wpdb->insert($table_name, $data);*/
}
add_action( 'wp_login', 'user_last_login', 10, 2 );

function wpb_lastlogin($user_id) { 
    // $last_login = get_the_author_meta('last_login');
    $last_login = get_user_meta($user_id, 'last_login', true);
    $the_login_date = $last_login ? human_time_diff($last_login) . ' ago' : 'Never Active';
    return $the_login_date; 
}

add_action("wp_ajax_get_user_by_username", function(){
    $username = $_GET["username"];
    $user = get_user_by("slug", $username );
    $exists = false;
    if(!empty($user)) $exists = true;
    echo json_encode(["exists"=>$exists, "user_id"=>$user->data->ID]);
    die();
});

function add_noindex_nofollow() {
    #echo "<pre style='display:none;'>".$_SERVER['REMOTE_ADDR']."</pre>";
    echo '<meta name="robots" content="noindex, nofollow"/>';
}
add_action( 'wp_head', 'add_noindex_nofollow' );
add_action( 'login_head', 'add_noindex_nofollow' );


function auto_login_on_password_reset( $user ) {
    if(!empty($user->ID)){
        $user_id = $user->ID;
        $user_pass = $_POST["pass1"];
        wp_set_password( $user_pass, $user_id);
        wp_set_current_user ( $user_id );
        wp_set_auth_cookie  ( $user_id );

        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'password_reset', 'auto_login_on_password_reset' );


add_action("wp_footer", function($args){
    
    if(is_user_logged_in()){
        $is_demo = \Safar\SafarUser::is_demo_user();
        if($is_demo){

            ?>
            <div id="j2j-mailchimp-register" class="modal" >

                <!-- Modal content -->
                <div class="modal-content">            
                    <span class="close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="">
                        <div class="register-interest-form">
                            <div class="register-interest-form__left">
                                <img src="<?= get_stylesheet_directory_uri() . '/assets/img/register-modal-bg.svg'?>" alt="" class="bg">
                                <img src="<?= get_stylesheet_directory_uri() . '/assets/img/register-modal-bg-fish.svg'?>" alt="" class="bg-fish">
                                <div class="logo">
                                    <img src="<?= get_stylesheet_directory_uri() . '/assets/img/logo.png'?>" alt="">
                                </div>
                            </div>
                            <div class="register-interest-form__right">
                                <h2>Register to find out more</h2>
                                <p class="description text-center">Don't miss out on our latest updates, promotions, and educational resources! Register to stay informed about all the exciting things happening at J2J.</p>
                                <form class="form end" method="post" name="New Form" onsubmit="sendMailChimpForm(this); return false;">
                                    <input type="hidden" name="form_id" value="73157a83" />
                                    <input type="hidden" name="queried_id" value="216601" />
                                    <input type="hidden" name="post_id" value="216601" />
                                    <input type="hidden" name="referer_title" value="homeschool">
                                    <input type="hidden" name="action" value="elementor_pro_forms_send_form"/>
                                    
                                    <div class="form-fields-wrapper labels-above">
                                        <div class="form-group">
                                            <div class="field-type-text field-group column field-group-firstname col-50 field-required">
                                                <label for="form-field-firstname" class="field-label">First Name</label>
                                                <input type="text" name="form_fields[firstname]" id="form-field-firstname" class="field size-md  field-textual"   required="required" aria-required="true">
                                            </div>

                                            <div class="field-type-text field-group column field-group-04fbb9f col-50 field-required">
                                                <label for="form-field-04fbb9f" class="field-label">Last Name							</label>
                                                <input type="text" name="form_fields[04fbb9f]" id="form-field-04fbb9f" class="field size-md  field-textual"   required="required" aria-required="true">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="field-type-email field-group column field-group-email col-100">
                                        <label for="form-field-email" class="field-label">E-mail</label>
                                        <input type="email" name="form_fields[email]" id="form-field-email" class="field size-md  field-textual"  >
                                    </div>

                                    <div class="field-group column field-type-submit col-100 e-form__buttons">
                                        <button type="submit" class="button size-lg">
                                            Register Interest
                                        </button>
                                    </div>
                                    <div class="div-message"></div>
                                </form>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready( $ => {
                    
                    sendMailChimpForm = (e) => {
                        $.ajax({
                            url: "<?=admin_url( 'admin-ajax.php' )?>",
                            data : $(e).serialize(),
                            type: "post",
                            beforeSend: () => {
                                $("#j2j-mailchimp-register button").fadeTo("fast",.3)
                            },
                            success: d => {
                                console.log("success", d)
                                $("#j2j-mailchimp-register button").fadeTo("fast",1);
                                if(d.success){
                                    $("#j2j-mailchimp-register .div-message").html(`<div class="success">${d.data.message}</div>`)
                                    localStorage.setItem('submitted_mailchimp_form', '1');
                                }else{
                                    $("#j2j-mailchimp-register .div-message").html(`<div class="error">${d.data.message}</div>`)
                                }
                            }
                        });
                        return true;
                    }

                    let intervalPopup = "";

                    function updateTimeDiff() {
                        const storedDateTime = localStorage.getItem('currentDateTime');
                        let submittedForm = localStorage.getItem('submitted_mailchimp_form');

                        if( submittedForm != 1){
                            if (storedDateTime) {
                                const formattedDateTime = moment(storedDateTime).format('MMMM Do YYYY, h:mm:ss a');
                                const currentTime = moment();
                                const timeDiff = currentTime.diff(storedDateTime, 'minutes');
                                console.log(timeDiff);
                                if (timeDiff >= 3) {
                                    $("#j2j-mailchimp-register").fadeIn().css("display","flex");
                                    clearInterval(intervalPopup)
                                }
                            }
                        }
                    }

                    $(document).on("click","#j2j-mailchimp-register .close",e => {
                        localStorage.setItem('submitted_mailchimp_form', '1');
                    })

                    intervalPopup = setInterval(updateTimeDiff, 10000); // update every 10 seconds
                        
                    /* 
                    post_id: 216601
                    form_id: 73157a83
                    referer_title: homeschool
                    queried_id: 216601
                    form_fields[firstname]: John Test
                    form_fields[04fbb9f]: Smith
                    form_fields[email]: johnsmith@gmail.com
                    action: elementor_pro_forms_send_form
                    referrer: https://my.journey2jannah.com/homeschool/
                    */
                })
            </script>
            <?php
            
        }
    }
});


add_shortcode( 'j2j_dynamic_display_heading', 'j2j_dynamic_display_heading' );
function j2j_dynamic_display_heading( $atts ) {
	ob_start();
        ?>
        <style type="text/css">
            .title-description{
                display:none;
            }
        </style>
        <?php
        $institutes = \Safar\SafarSchool::get_user_institutes( get_current_user_id() );
        $has_active_subscription = false;
        foreach($institutes as $ins){
            $order_id = get_post_meta($ins->ID, "order_id", true);
            $subscription_status = get_post_meta($ins->ID,"subscription_status", true);
            $active_subscription = [];
            $subscriptions_all = [];
            if(!empty($order_id)){
                $subscriptions = \Safar\SafarPublications::api_request(["endpoint"=>"/family/order/".$order_id."/subscriptions?".mt_rand()]);
                 
                foreach($subscriptions as $subscription){
                    if($subscription->status == "active" || $subscription->status == "pending-cancel"){
                        $active_subscription = $subscription;
                    }
                    $subscriptions_all[$subscription->subscription_id] = $subscription;
                }

                
            }
        }

        $latest_subscription = [];
        if(!empty($subscriptions_all)){
            krsort($subscriptions_all);
            $latest_subscription = reset($subscriptions);
        }
        if(!$active_subscription){
        ?>
        <style type="text/css">
            .title-description{
                display:none;
            }
            <?php 
            $is_parent = \Safar\SafarFamily::is_user_parent( );

            if($is_parent){
                if( $latest_subscription->status == "on-hold"){
                    ?>
                    .title-description.parent-onhold{
                        display:block;
                    }
                    <?php
                }
                if( $latest_subscription->status == "cancelled"){
                    ?>
                    .title-description.parent-cancelled{
                        display:block;
                    }
                    <?php
                }
            }else{
                // child
                ?>
                .title-description.child{
                    display:block;
                }
                <?php
            }
            ?>
            
        </style>
        <?php

        }
    $out = ob_get_contents();
    ob_get_clean();
    return $out;
}

function custom_password_onboarding(){
    ob_start();
    ?>
    <style>
    .gform_page .weak, 
    .gform_page .mismatch {
        border: 2px solid #ef746f;
        border-radius:3px;
        padding:8px 11px;
        color:#ef746f;
        font-size: 13px;
        text-align:center;
    }


    .gform_page .medium {
        border: 2px solid #e6ad76;
        border-radius: 3px;
        padding: 8px 11px;
        color: #e6ad76;
        font-size: 13px;
        text-align:center;
    }

    .gform_page .strong {
        border: 2px solid #8ecfa3;
        border-radius:3px;
        padding:8px 11px;
        color:#8ecfa3;
        font-size: 13px;
        text-align:center;
    }
    .gform_page #pwd-indicator .label{
        color:#6B6F72;
        font-weight:500;
    }
    .ginput_container.password .btn-view-password {
        position: absolute;
        background: none;
        border: none;
        padding: 0px;
        margin: 0px;
        right: 14px;
        bottom: 18px;
        background:none !important;
        border-color:none !important;
        min-width:auto !important;
        min-height:auto !important;
    }
    .ginput_container.password .btn-view-password:before {
        content: " ";
        width: 26px;
        height: 26px;
        background-image: url(/wp-content/themes/buddyboss-theme-child/assets/img/admin-institute-onboarding/eye.svg);
        background-repeat: no-repeat;
        display: block;
        background-position: center;
    }
    .ginput_container.password .btn-view-password.hide-password:before {
        background-image: url(/wp-content/themes/buddyboss-theme-child/assets/img/admin-institute-onboarding/eye-off.svg);
    }
    .input-password:focus{
        box-shadow:none !important;
    }
    </style>

    <div id="field_2_91">
        <div class="ginput_container password">
            <input id="input_2_91" type="password" name="password" class="input-password" placeholder="Choose Your Password"/>
            <button type="button" class="btn-view-password"></button>
        </div>
    </div>

    <div id="field_2_93">
        <div class="ginput_container password"> 
            <input id="input_2_93" type="password" name="confirm_password" class="input-password" placeholder="Confirm Your Password"/>
            <button type="button" class="btn-view-password"></button>
        </div>
    </div>

    <div id="pwd-indicator"></div>

    <script type="text/javascript">
        jQuery(document).ready($ => {
            $(document).on("click",".btn-view-password", e => {
                $(e.currentTarget).toggleClass("hide-password")
                
                if( $(e.currentTarget).hasClass("hide-password")){
                    $(e.currentTarget).parent().find(".input-password").attr("type","text");
                }else{
                    $(e.currentTarget).parent().find(".input-password").attr("type","password");
                }
            })
        })
    </script>
    <?php
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}

add_shortcode("gf_password_form_family_onboarding","custom_password_onboarding");
add_shortcode("gf_password_form_institute_onboarding","custom_password_onboarding");


// check users who completed selected topics
add_action("wp_ajax_check_completed_activities", function(){
    global $wpdb;
    $post_ids = [210701, 210706, 210710];

    foreach($post_ids as $post_id){
        $sql = " SELECT DISTINCT * FROM ".$wpdb->prefix."usermeta WHERE meta_key='_sfwd-course_progress' AND meta_value LIKE '%".$post_id."%' ";
        $result = $wpdb->get_results($sql);

        foreach($result as $progress){
            $sfwd_progress = unserialize($progress->meta_value);
            foreach($sfwd_progress as $course_id=>$ld_progress){
                foreach($ld_progress["topics"] as $lesson_id=>$topics){
                    foreach($topics as $topic_id=>$completed){

                        if( $completed && $topic_id == $post_id){
                            if($progress->user_id == 9401){ // support
                                
                                $success = learndash_process_mark_incomplete( $progress->user_id ,  $course_id, $topic_id );
                                echo "<Pre>";
                                    print_r([$topic_id, $completed, $progress->user_id, $success]);
                                echo "</pre>";
                            }
                        }
                    }
                }
            }
        }
    }
    //SELECT DISTINCT user_id FROM wp_usermeta WHERE meta_key = 'course_completed_123' AND meta_value LIKE '%210693%';

});

add_action("wp_footer", function(){
    $current_user = wp_get_current_user();
    global $wpdb;

    // Check if the user is logged in
    if ($current_user->ID != 0) {

        $cookie_key = "last_update_".$current_user->ID;

        $last_update_cookie = isset($_COOKIE[$cookie_key]) ? $_COOKIE[$cookie_key] : null;
        $should_update_db = false;
        if ($last_update_cookie) {
            // Check if the cookie is older than 2 hours
            $time_since_last_update = time() - $last_update_cookie;
            if ($time_since_last_update > 2 * 3600) {
                $should_update_db = true;
            }
        } else {
            $should_update_db = true;
        }

        if ($should_update_db) {

            $logged_in_cookie = $_COOKIE[LOGGED_IN_COOKIE];
            $user_id = $current_user->ID;
            update_user_meta($user_id, 'last_login', time());
            $table_name = $wpdb->prefix . 'login_logs';

            $meta = serialize($_SERVER);
            $data = [
                'user_id' => $user_id,
                'meta' => $meta,
                'logged_in_cookie' => $logged_in_cookie,
                'login_datetime' => current_time('mysql'),
            ];
            $wpdb->insert($table_name, $data);
    
            // Set a new cookie for 2 hours
            setcookie($cookie_key, time(), time() + 2 * 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
        }

    }
});