<?php
/**
 * LearnDash LD30 Displays a topic.
 *
 * Available Variables:
 *
 * $course_id                 : (int) ID of the course
 * $course                    : (object) Post object of the course
 * $course_settings           : (array) Settings specific to current course
 * $course_status             : Course Status
 * $has_access                : User has access to course or is enrolled.
 *
 * $courses_options            : Options/Settings as configured on Course Options page
 * $lessons_options            : Options/Settings as configured on Lessons Options page
 * $quizzes_options            : Options/Settings as configured on Quiz Options page
 *
 * $user_id                    : (object) Current User ID
 * $logged_in                  : (true/false) User is logged in
 * $current_user               : (object) Currently logged in user object
 * $quizzes                    : (array) Quizzes Array
 * $post                       : (object) The topic post object
 * $lesson_post                : (object) Lesson post object in which the topic exists
 * $topics                     : (array) Array of Topics in the current lesson
 * $all_quizzes_completed      : (true/false) User has completed all quizzes on the lesson Or, there are no quizzes.
 * $lesson_progression_enabled : (true/false)
 * $show_content               : (true/false) true if lesson progression is disabled or if previous lesson and topic is completed.
 * $previous_lesson_completed  : (true/false) true if previous lesson is completed
 * $previous_topic_completed   : (true/false) true if previous topic is completed
 *
 * @since 3.0.0
 *
 * @package LearnDash\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$lesson_data = $post;



$parent_course_data = learndash_get_setting( $post, 'course' );
if ( 0 === $parent_course_data ) {
	$parent_course_data = $course_id;
	if ( 0 === $parent_course_data ) {
		$course_id = buddyboss_theme()->learndash_helper()->ld_30_get_course_id( $post->ID );
	}
	$parent_course_data = learndash_get_setting( $course_id, 'course' );
}

$parent_course       = get_post( $parent_course_data );
$parent_course_link  = $parent_course->guid;
$parent_course_title = $parent_course->post_title;
if ( empty( $course_id ) ) {
	$course_id = learndash_get_course_id( $lesson_data->ID );

	if ( empty( $course_id ) ) {
		$course_id = buddyboss_theme()->learndash_helper()->ld_30_get_course_id( $lesson_data->ID );
	}
}

$lession_list    = learndash_get_lesson_list( $course_id, array( 'num' => - 1 ) );
$content_urls    = buddyboss_theme()->learndash_helper()->buddyboss_theme_ld_custom_pagination( $course_id, $lession_list );
$pagination_urls = buddyboss_theme()->learndash_helper()->buddyboss_theme_custom_next_prev_url( $content_urls );

if ( empty( $course ) ) {
	if ( empty( $course_id ) ) {
		$course = learndash_get_course_id( $lesson_data->ID );
	} else {
		$course = get_post( $course_id );
	}
}
$lesson_id = learndash_get_lesson_id( $lesson_data->ID );
$topics    = learndash_get_topic_list( $lesson_id, $course_id );

$fullscreen_mode = true;

?>
<?php if (!$fullscreen_mode): ?>
<div id="learndash-content" class="container-full">
	
	<div class="bb-grid grid">
		<?php
		if ( ! empty( $course ) ) :
			include locate_template( '/learndash/ld30/learndash-sidebar.php' );
		endif;
		?>

		<div id="learndash-page-content">
			<div class="learndash-content-body">
				<?php
				$buddyboss_content = apply_filters( 'buddyboss_learndash_content', '', $post );
				if ( ! empty( $buddyboss_content ) ) {
					echo $buddyboss_content;
				} else {

					$lesson_no = 1;
					foreach ( $lession_list as $les ) {
						if ( $les->ID == $lesson_id ) {
							break;
						}
						$lesson_no ++;
					}

					$topic_no = 1;
					foreach ( $topics as $topic ) {
						if ( $topic->ID == $post->ID ) {
							break;
						}
						$topic_no ++;
					}
					?>

					<div class="<?php echo esc_attr( learndash_the_wrapper_class() ); ?>">
						<?php
						/**
						 * Fires before the topic
						 *
						 * @since 3.0.0
						 * @param int $course_id Course ID.
						 * @param int $user_id   User ID.
						 */
						 do_action( 'learndash-topic-before', get_the_ID(), $course_id, $user_id );
						?>
						 <div id="learndash-course-header" class="bb-lms-header">
							<div class="bb-ld-info-bar">
								<?php
								learndash_get_template_part(
									'modules/infobar.php',
									array(
										'context'   => 'topic',
										'course_id' => $course_id,
										'user_id'   => $user_id,
									),
									true
								);
								?>
							</div>
							<div class="flex bb-position">
								<div class="sfwd-course-position">
									<span class="bb-pages"><?php echo LearnDash_Custom_Label::get_label( 'lesson' ); ?> <?php echo $lesson_no; ?>, <?php echo LearnDash_Custom_Label::get_label( 'topic' ); ?> <?php echo $topic_no; ?></span>
								</div>
								<div class="sfwd-course-nav">
									<div class="bb-ld-status">
									<?php
										$status = ( learndash_is_item_complete() ? 'complete' : 'incomplete' );
										learndash_status_bubble( $status );
									?>
									</div>
									<?php
									$expire_date_calc    = ld_course_access_expires_on( $course_id, $user_id );
									$courses_access_from = ld_course_access_from( $course_id, $user_id );
									$expire_access_days  = learndash_get_setting( $course_id, 'expire_access_days' );
									$date_format         = get_option( 'date_format' );
									$expire_date         = date_i18n( $date_format, $expire_date_calc );
									$current             = time();
									$expire_string       = ( $expire_date_calc > $current ) ? __( 'Expires at', 'buddyboss-theme' ) : __( 'Expired at', 'buddyboss-theme' );

									if ( $expire_date_calc > 0 && abs( intval( $expire_access_days ) ) > 0 && ( ! empty( $user_id ) ) ) {
										?>
									<div class="sfwd-course-expire">
										<span data-balloon-pos="up" data-balloon="<?php echo esc_attr( $expire_string ); ?>"><i class="bb-icon-l bb-icon-alarm"></i><?php echo wp_kses_post( $expire_date ); ?></span>
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
										<?php
										if ( ( apply_filters( 'learndash_show_next_link', learndash_is_topic_complete( $user_id, $post->ID ), $user_id, $post->ID ) && $pagination_urls['next'] != '' ) || ( $course_settings['course_disable_lesson_progression'] === 'on' && $pagination_urls['next'] != '' ) ) {
											echo $pagination_urls['next'];
										} else {
											echo '<span class="next-link empty-post"></span>';
										}
										?>
									</div>
								</div>
							</div>
							<div class="lms-header-title">
								<h1><?php the_title(); ?></h1>
							</div>
							<?php
							global $post;
							$course_post = learndash_get_setting( $post, 'course' );
							$course_data = get_post( $course_post );
							$author_id   = $course_data->post_author;
							learndash_get_template_part(
								'template-course-author.php',
								array(
									'user_id' => $author_id,
								),
								true
							);
							?>
						</div>

					<div class="learndash_content_wrap">

						<?php
						learndash_get_template_part(
							'modules/progress.php',
							array(
								'context'   => 'topic',
								'user_id'   => $user_id,
								'course_id' => $course_id,
							),
							true
						);



						/**
						 * If the user needs to complete the previous lesson display an alert
						 */
						if ( ( $lesson_progression_enabled ) && ( ! empty( $sub_context ) || ! $previous_topic_completed || ! $previous_lesson_completed ) ) :


							if ( 'video_progression' === $sub_context ) {
								$previous_item = $lesson_post;
							} else {
								$previous_item_id = learndash_user_progress_get_previous_incomplete_step( $user_id, $course_id, $post->ID );
								if ( ! empty( $previous_item_id ) ) {
									$previous_item = get_post( $previous_item_id );
								}
							}

							if ( ( isset( $previous_item ) ) && ( ! empty( $previous_item ) ) ) {
								$show_content = false;
								learndash_get_template_part(
									'modules/messages/lesson-progression.php',
									array(
										'previous_item' => $previous_item,
										'course_id'     => $course_id,
										'context'       => 'topic',
										'sub_context'   => $sub_context,
									),
									true
								);
							}
						endif;

						if ( $show_content ) :

							learndash_get_template_part(
								'modules/tabs.php',
								array(
									'course_id' => $course_id,
									'post_id'   => get_the_ID(),
									'user_id'   => $user_id,
									'content'   => $content,
									'materials' => $materials,
									'context'   => 'topic',
								),
								true
							);

							if ( ! empty( $quizzes ) ) :

								learndash_get_template_part(
									'quiz/listing.php',
									array(
										'user_id'   => $user_id,
										'course_id' => $course_id,
										'quizzes'   => $quizzes,
										'context'   => 'topic',
									),
									true
								);
							endif;

							if ( learndash_lesson_hasassignments( $post ) && ! empty( $user_id ) ) :

								learndash_get_template_part(
									'assignment/listing.php',
									array(
										'user_id'          => $user_id,
										'course_step_post' => $post,
										'course_id'        => $course_id,
										'context'          => 'topic',
									),
									true
								);
							endif;
						endif; // $show_content

						$can_complete = false;

						if ( $all_quizzes_completed && $logged_in && ! empty( $course_id ) ) :
							/** This filter is documented in themes/ld30/templates/lesson.php */
							$can_complete = apply_filters( 'learndash-lesson-can-complete', true, get_the_ID(), $course_id, $user_id );
						endif;

						learndash_get_template_part(
							'modules/course-steps.php',
							array(
								'course_id'             => $course_id,
								'course_step_post'      => $post,
								'all_quizzes_completed' => $all_quizzes_completed,
								'user_id'               => $user_id,
								'course_settings'       => isset( $course_settings ) ? $course_settings : array(),
								'context'               => 'topic',
								'can_complete'          => $can_complete,
							),
							true
						);

						/**
						 * Fires after the topic.
						 *
						 * @since 3.0.0
						 *
						 * @param int $post_id   Current Post ID.
						 * @param int $course_id Course ID.
						 * @param int $user_id   User ID.
						 */
							do_action( 'learndash-topic-after', get_the_ID(), $course_id, $user_id );

						$focus_mode         = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'focus_mode_enabled' );
						$post_type          = get_post_type( $post->ID );
						$post_type_comments = learndash_post_type_supports_comments( $post_type );

						if ( is_user_logged_in() && 'yes' === $focus_mode && comments_open() ) {

							learndash_get_template_part(
								'focus/comments.php',
								array(
									'course_id' => $course_id,
									'user_id'   => $user_id,
									'context'   => 'focus',
								),
								true
							);

						} elseif ( true === $post_type_comments ) {
							if ( comments_open() ) :
								comments_template();
							endif;
						}

						?>

						</div><?php /* .learndash_content_wrap */ ?>

					</div> <!--/.learndash-wrapper-->
				<?php } ?>
			</div><?php /* .learndash-content-body */ ?>
		</div><?php /* #learndash-page-content */ ?>
	</div>

</div>
<?php else: ?>
	<style>
		[data-elementor-type="header"] {
			display:none;
		} 
	</style>
	<div id="learndash-content" class="container-full custom-topic">
	
		<div class="bb-grid grid">
			
	
			<div id="learndash-page-content">
				<div class="learndash-content-body">
					<?php 
					global $wpdb;
				
					//$post = get_post_meta( get_the_id() );
					$course_id = learndash_get_course_id(get_the_id()); //get_post_meta( get_the_id(), "course_id", true );

					$post_meta = get_post_meta($course_id);
					$group_meta = get_post_meta(216291);

					$sql = "SELECT DISTINCT post_id, menu_order, meta_key FROM ".$wpdb->prefix."postmeta  as pm
                                        INNER JOIN ".$wpdb->prefix."posts as p ON pm.post_id = p.ID
                                        WHERE meta_key like 'learndash_group_enrolled_%'
                                        AND post_id=".esc_sql($course_id);
					$course_meta = $wpdb->get_results($sql);

					if( isset($_GET["dashboard"]) ){
						$back_link = "/dashboard";
						$back_title = "Return to Dashboard";
					}else{
						$back_link = get_permalink($course_id);
						$back_title = "Return to Course Overview";
					}
					
					?>
					<a title="<?=$back_title?>" href="<?=$back_link?><?=(isset($_GET["ldgid"])) ? "?ldgid=".$_GET["ldgid"]:""?>" class="course-entry-link">
						<svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#a)"><circle cx="50" cy="46" r="30" fill="#fff"/></g><path d="m53 55-9-9 9-9" stroke="#5D53C0" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><defs><filter id="a" x="0" y="0" width="100" height="100" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4"/><feGaussianBlur stdDeviation="10"/><feComposite in2="hardAlpha" operator="out"/><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/><feBlend in2="BackgroundImageFix" result="effect1_dropShadow_1027_2"/><feBlend in="SourceGraphic" in2="effect1_dropShadow_1027_2" result="shape"/></filter></defs></svg>
					
					</a>
					<?php
					$buddyboss_content = apply_filters( 'buddyboss_learndash_content', '', $post );
					if ( ! empty( $buddyboss_content ) ) {
						echo $buddyboss_content;
					} else {
	
						$lesson_no = 1;
						foreach ( $lession_list as $les ) {
							if ( $les->ID == $lesson_id ) {
								break;
							}
							$lesson_no ++;
						}
	
						$topic_no = 1;
						foreach ( $topics as $topic ) {
							if ( $topic->ID == $post->ID ) {
								break;
							}
							$topic_no ++;
						}
						?>
	
						<div class="<?php echo esc_attr( learndash_the_wrapper_class() ); ?>">
							<?php
							/**
							 * Fires before the topic
							 *
							 * @since 3.0.0
							 * @param int $course_id Course ID.
							 * @param int $user_id   User ID.
							 */
							 do_action( 'learndash-topic-before', get_the_ID(), $course_id, $user_id );
							?>
							
	
						<div class="learndash_content_wrap" test123>
	
							<?php
							// learndash_get_template_part(
							// 	'modules/progress.php',
							// 	array(
							// 		'context'   => 'topic',
							// 		'user_id'   => $user_id,
							// 		'course_id' => $course_id,
							// 	),
							// 	true
							// );
	
	
	
							/**
							 * If the user needs to complete the previous lesson display an alert
							 */
							if ( ( $lesson_progression_enabled ) && ( ! empty( $sub_context ) || ! $previous_topic_completed || ! $previous_lesson_completed ) ) :
	
	
								if ( 'video_progression' === $sub_context ) {
									$previous_item = $lesson_post;
								} else {
									$previous_item_id = learndash_user_progress_get_previous_incomplete_step( $user_id, $course_id, $post->ID );
									if ( ! empty( $previous_item_id ) ) {
										$previous_item = get_post( $previous_item_id );
									}
								}
	
								if ( ( isset( $previous_item ) ) && ( ! empty( $previous_item ) ) ) {
									$show_content = false;
									learndash_get_template_part(
										'modules/messages/lesson-progression.php',
										array(
											'previous_item' => $previous_item,
											'course_id'     => $course_id,
											'context'       => 'topic',
											'sub_context'   => $sub_context,
										),
										true
									);
								}
							endif;
	
							if ( $show_content ) :
								$s_content = get_the_content();
								if (empty($s_content)):
									echo '<div class="no-content-coming-soon" style="    display: flex;
									color: #fff;
									font-size: 24px;
									font-weight: 700;
									justify-content: center;
									height: 400px;
									align-items: center;">
										<div>Lesson/Activity Coming Soon</div>
									</div>';
								endif;
								
								learndash_get_template_part(
									'modules/tabs.php',
									array(
										'course_id' => $course_id,
										'post_id'   => get_the_ID(),
										'user_id'   => $user_id,
										'content'   => $content,
										'materials' => $materials,
										'context'   => 'topic',
									),
									true
								);

								
	
								if ( ! empty( $quizzes ) ) :
	
									learndash_get_template_part(
										'quiz/listing.php',
										array(
											'user_id'   => $user_id,
											'course_id' => $course_id,
											'quizzes'   => $quizzes,
											'context'   => 'topic',
										),
										true
									);
								endif;
	
								if ( learndash_lesson_hasassignments( $post ) && ! empty( $user_id ) ) :
	
									learndash_get_template_part(
										'assignment/listing.php',
										array(
											'user_id'          => $user_id,
											'course_step_post' => $post,
											'course_id'        => $course_id,
											'context'          => 'topic',
										),
										true
									);
								endif;
							endif; // $show_content
	
							$can_complete = false;
	
							if ( $all_quizzes_completed && $logged_in && ! empty( $course_id ) ) :
								/** This filter is documented in themes/ld30/templates/lesson.php */
								$can_complete = apply_filters( 'learndash-lesson-can-complete', true, get_the_ID(), $course_id, $user_id );
							endif;


							
							
							learndash_get_template_part(
								'modules/course-steps.php',
								array(
									'course_id'             => $course_id,
									'course_step_post'      => $post,
									'all_quizzes_completed' => $all_quizzes_completed,
									'user_id'               => $user_id,
									'course_settings'       => isset( $course_settings ) ? $course_settings : array(),
									'context'               => 'topic',
									'can_complete'          => $can_complete,
								),
								true
							);
	
							/**
							 * Fires after the topic.
							 *
							 * @since 3.0.0
							 *
							 * @param int $post_id   Current Post ID.
							 * @param int $course_id Course ID.
							 * @param int $user_id   User ID.
							 */
								do_action( 'learndash-topic-after', get_the_ID(), $course_id, $user_id );
	
							$focus_mode         = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'focus_mode_enabled' );
							$post_type          = get_post_type( $post->ID );
							$post_type_comments = learndash_post_type_supports_comments( $post_type );
	
							if ( is_user_logged_in() && 'yes' === $focus_mode && comments_open() ) {
	
								learndash_get_template_part(
									'focus/comments.php',
									array(
										'course_id' => $course_id,
										'user_id'   => $user_id,
										'context'   => 'focus',
									),
									true
								);
	
							} elseif ( true === $post_type_comments ) {
								if ( comments_open() ) :
									comments_template();
								endif;
							}
	
							?>
	
							</div><?php /* .learndash_content_wrap */ ?>
	
						</div> <!--/.learndash-wrapper-->
					<?php } ?>
				</div><?php /* .learndash-content-body */ ?>
			</div><?php /* #learndash-page-content */ ?>
			<div class="course-nav active">
				<button class="btn-toggle__topics">
					<svg class="toggle-close" width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" transform="rotate(90 30 30)" fill="#463F88"/><path d="m21 34 9-9 9 9" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<svg class="toggle-open" width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" transform="rotate(90 30 30)" fill="#463F88"/><path d="M37 23 23 37m0-14 14 14" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<div class="course-nav__outline">

					<?php 
					$course_progress = learndash_user_get_course_progress( get_current_user_id(), $course_id);
					$lessons = learndash_get_lesson_list($course_id);

					

					?>
					<div class="course-nav__slider">
					<?php 
					$i = 0;

						
					$topics = learndash_get_topic_list($lesson_id, $course_id);


					$quiz = learndash_get_lesson_quiz_list($lesson_id, $user_id, $course_id);
					if(!empty($quiz)){
						foreach($quiz as $equiz){
							array_push($topics, $equiz["post"]);
						}
					}	
					
					
					foreach($topics as $topic){
						//ld_topic_category
						$topic_category = wp_get_post_terms($topic->ID, "ld_topic_category");

						$completed = false;
						if($topic->post_type == "sfwd-quiz"){
							$completed = learndash_is_quiz_complete($user_id, $topic->ID, $course_id);
						}else{
							$completed = $course_progress["topics"][$lesson_id][$topic->ID];
						}
						?>
						<div class="course-nav__topic <?= ( ( $completed ) ? 'completed' : ''); ?> <?= (( get_the_id() == $topic->ID ) ? 'current active' : '');?>">
							
							<a data-topicid="<?=$topic->ID?>" href="<?=get_permalink($topic->ID)?><?=(isset($_GET["dashboard"])) ? "?dashboard=".$_GET["dashboard"]:""?>">
								
								<?php 
								//f( $course_progress["status"] != "not_started"){
									if(!empty($topic_category)){
										
										?>	
										<span class="topic-type"><?=$topic_category[0]->name?></span>
										<?php 
									}else{
										if($topic->post_type=="sfwd-quiz"){
											?>	
											<span class="topic-type">Quiz</span>
											<?php 
										}else{
											// fallback
											?>	
											<span class="topic-type">Lesson</span>
											<?php 
										}
									}
								
								/*}else{
									?>
									<span  class="course-nav__status <?= ( ( $completed ) ? 'complete' : 'start'); ?>"><?= ( ( $completed ) ? 'Complete' : 'Start Lesson'); ?></span>
									<?php
								}*/

								$thumbnail = get_the_post_thumbnail_url($topic->ID);
								$topic_title = "";
								// if(empty($thumbnail)){
								// 	$thumbnail = "/wp-content/uploads/2022/07/blank-1.png";
									$topic_title = "<div class='topic-title'><span>".$topic->post_title."</span></div>";
								// }
								?>
								<!-- <img class="topic-img" alt="" src="<?=$thumbnail?>" alt=""> -->
								<svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="32" cy="32" fill="#B0D178" stroke="#B0D178" stroke-width="3" r="30"/><path d="m46.09 22-20 20L17 32.91" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
								<?=$topic_title?>
							</a>
						</div>
						<?php
						
						if(get_the_id() == $topic->ID){
							$initial_slide = $i;
						}
						
						$i++;
					}
					
					
					?>
					</div>
				</div>
			</div>
		</div>
	
	</div>

	 
	<div class="rotate-device">Please rotate your device for best browsing experience!</div>
	<script>
		
		jQuery(document).ready(function ($) {
			
			$('.h5p-iframe').on('load', function(){
				if ($(".h5p-iframe").contents().find(".purple").length) {
					$('.learndash_mark_complete_button').addClass('peach')
				}
				
			});
			$(window).load(function () {

				setTimeout(() => {
					if ($('.purple-bg').length) {
						$('.learndash_mark_complete_button').addClass('peach')
					}
				}, 100);
			})
			

			$(document).on("click",".course-nav",e => {
			
				if( $(e.target).attr("class") == "course-nav"){
					
					$('.course-nav').addClass('active')
					 
				}
			});
				
		 
			$('button.btn-toggle__topics').click(function () {
				$('.course-nav').toggleClass('active')
			});

			$('.course-nav__slider').slick({
			infinite: false,
			speed: 300,
			slidesToShow: 4,
			dots:false,
			slidesToScroll: 1,
			prevArrow: '<button class="slick-prev"><svg width="26" height="46" viewBox="0 0 26 46" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23 43 3 23 23 3" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
    		nextArrow: '<button class="slick-next"><svg width="26" height="46" viewBox="0 0 26 46" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m3 43 20-20L3 3" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
			responsive: [
				{
				breakpoint: 1024,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 1,
					arrows:true,
				}
				},
				{
				breakpoint: 901,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
					arrows:true,
				}
				},
				{
				breakpoint: 601,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows:true,
				}
				}
				// You can unslick at a given breakpoint now by adding:
				// settings: "unslick"
				// instead of a settings object
			]
			});
			
			let slideInterval = "";
			slideInterval = setInterval( () => {
				if( $(".course-nav__topic.current").parent().parent().hasClass("slick-active") ) {
					clearInterval(slideInterval);
					console.log("slide found")
				}else{
					$(".slick-next").trigger("click");
				}

			}, 1000)
			
			
			<?php 
			$topic_id = get_the_id();
			$is_global = true;
			$we_meta = get_post_meta($topic_id,"_WE-meta_",true);
			if($we_meta["restrict-mark-complete"] != "Use Global Setting"){
				$is_global = false;
			}
			
			if(!$is_global){
				?>
				$(".ld-content-actions").find(".learndash_mark_complete_button").val("Next Lesson");
				$(".ld-content-actions").appendTo($(".h5p-iframe-wrapper "));
				$("body").addClass("custom-im-finish")
				<?php
			}


			
			?>
			let h5pVideoIsPlaying = false;
			var h5pContainer = document.getElementsByClassName("h5p-iframe-wrapper")[0];
			let h5pInterval = "";
			function toggleFullScreen() {
				if (h5pContainer.requestFullscreen) {
					h5pContainer.requestFullscreen();
				} else if (h5pContainer.mozRequestFullScreen) {
					h5pContainer.mozRequestFullScreen();
				} else if (h5pContainer.webkitRequestFullscreen) {
					h5pContainer.webkitRequestFullscreen();
				} else if (h5pContainer.msRequestFullscreen) {
					h5pContainer.msRequestFullscreen();
				}
			}

			setVideoIsplaying = h5pVideoIsPlaying => {
				if(h5pVideoIsPlaying){
					if (screen.width <= 768) {
						///console.log("Video Is Played", h5pVideoIsPlaying)
						clearInterval(h5pInterval);
						setTimeout( () => toggleFullScreen(), 500);
						$(".h5p-iframe-wrapper").addClass("mobile-video-playing")
					}
				}
			}


			addFullScreenClass = () => {
				jQuery(".h5p-iframe-wrapper").addClass("h5p-fullscreen")
				jQuery("body").addClass("body-h5p-fullscreen")
				console.log("added fullscreen")
			}

			moveToNextTopic = () => {
				let nextItem = $(".course-nav__topic.current.active").parent().parent().next()
				href = nextItem.find("a").attr("href")
				setTimeout( () => {
					if( !jQuery("#greatWork").is(":visible") ){
						window.location.href = href
					}
				}, 1000)
			}

			addClassToIframe = (e) => {
				console.log("classlist", e)
				jQuery(".h5p-iframe-wrapper iframe").addClass(e)
				jQuery(".h5p-iframe-wrapper").addClass(e)
			}

			fixContainerWidth = () => {
				const element = document.querySelector('.h5p-iframe-wrapper.h5p-fullscreen');
				// Calculate the width based on the full height (100vh) of the page
				if (element) {
					const windowHeight = window.innerHeight;
					const rect = element.getBoundingClientRect();
					const elementWidth = rect.width * (windowHeight / rect.height);
					
					
					offset = windowHeight * 0.035;
					console.log('Element width:', elementWidth, windowHeight, offset);
					$('.h5p-iframe-wrapper.h5p-fullscreen').attr(`style`,`width:${(elementWidth - offset)}px !important`)
				}
			}
			//fixContainerWidth();
			let intervalFixWidth = "";
			let counterWidth = 0;
			setTimeout( () => {
				fixContainerWidth()
			}, 2000)

			<?php if(isset($_GET["test"])):?>
			setInterval(() => {
				// Get the .h5p-iframe element
				var iframe = document.querySelector('.h5p-iframe-wrapper');

				// Get the dimensions and position of the iframe
				var rect = iframe.getBoundingClientRect();
				var iframeTop = rect.top;
				var iframeBottom = rect.bottom;

				// Get the dimensions of the visible portion of the screen
				var windowHeight = window.innerHeight || document.documentElement.clientHeight;

				// Check if the iframe is fully visible on the screen
				var isFullyVisible = (iframeTop >= 0) && (iframeBottom <= windowHeight);

				// Output the result
				console.log('Is fully visible:', isFullyVisible);
				alert('Is fully visible:' + isFullyVisible);
				if(!isFullyVisible){
					fixContainerWidth()
				}
			}, 10000);
			<?php endif;?>
			
			//window.addEventListener('resize', fixContainerWidth);


		})


	</script>
<?php endif; ?>