<?php
/**
 * LearnDash LD30 Displays a course
 *
 * Available Variables:
 * $course_id                   : (int) ID of the course
 * $course                      : (object) Post object of the course
 * $course_settings             : (array) Settings specific to current course
 *
 * $courses_options             : Options/Settings as configured on Course Options page
 * $lessons_options             : Options/Settings as configured on Lessons Options page
 * $quizzes_options             : Options/Settings as configured on Quiz Options page
 *
 * $user_id                     : Current User ID
 * $logged_in                   : User is logged in
 * $current_user                : (object) Currently logged in user object
 *
 * $course_status               : Course Status
 * $has_access                  : User has access to course or is enrolled.
 * $materials                   : Course Materials
 * $has_course_content          : Course has course content
 * $lessons                     : Lessons Array
 * $quizzes                     : Quizzes Array
 * $lesson_progression_enabled  : (true/false)
 * $has_topics                  : (true/false)
 * $lesson_topics               : (array) lessons topics
 *
 * @since 3.0.0
 *
 * @package LearnDash\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$materials              = ( isset( $materials ) ) ? $materials : '';
$lessons                = ( isset( $lessons ) ) ? $lessons : array();
$quizzes                = ( isset( $quizzes ) ) ? $quizzes : array();
$lesson_topics          = ( isset( $lesson_topics ) ) ? $lesson_topics : array();
$course_certficate_link = ( isset( $course_certficate_link ) ) ? $course_certficate_link : '';

$template_args = array(
	'course_id'                  => $course_id,
	'course'                     => $course,
	'course_settings'            => $course_settings,
	'courses_options'            => $courses_options,
	'lessons_options'            => $lessons_options,
	'quizzes_options'            => $quizzes_options,
	'user_id'                    => $user_id,
	'logged_in'                  => $logged_in,
	'current_user'               => $current_user,
	'course_status'              => $course_status,
	'has_access'                 => $has_access,
	'materials'                  => $materials,
	'has_course_content'         => $has_course_content,
	'lessons'                    => $lessons,
	'quizzes'                    => $quizzes,
	'lesson_progression_enabled' => $lesson_progression_enabled,
	'has_topics'                 => $has_topics,
	'lesson_topics'              => $lesson_topics,
);

$has_lesson_quizzes = learndash_30_has_lesson_quizzes( $course_id, $lessons ); 
$user_info = \Safar\SafarUser::get_user_info([]);
$course_id = get_the_id();

?>
<?php 
$use_custom_path_layout = get_field("use_custom_path_layout");
wp_enqueue_script('html2canvas');

if (!empty($use_custom_path_layout)): 
	wp_enqueue_style( 'singlecourse', get_stylesheet_directory_uri() .'/assets/css/singlecourse.css', array(), time() ,false );
?>
	<style>
		.single-sfwd-courses .course-path {
			max-width:<?=get_field('path_max_width')?>px;
			margin-top:<?=get_field('path_margin_top');?>px;
		}
		@media (max-width:601px) {
			.single-sfwd-courses .course-path {
				margin-top:<?=get_field('mobile_margin_top');?>px;
			}
			
		}
	</style>
	<div class="path-wrapper">

	<?php 
		$course_meta = get_post_meta($course_id);
		$ld_group_id = 0;
		$found_ld_group_cat = false;
		foreach($course_meta as $meta_key=>$meta_value){
			if( strpos($meta_key, "learndash_group_enrolled_") !== false ){
				
				if(!$found_ld_group_cat){
					$ld_group_id = str_replace("learndash_group_enrolled_", "",$meta_key);
					$ld_group_terms = wp_get_post_terms($ld_group_id, "ld_group_category");
					if($ld_group_terms) $found_ld_group_cat = true;
				}
			}
		}

		if(isset($_GET["ldgid"])) $ld_group_id = $_GET["ldgid"];

		$group_courses = \Safar\SafarCourses::get_ld_group_courses($ld_group_id);
		
		$current_page_key = false;

		foreach($group_courses as $key=>$ecourse){
			if($ecourse->post_id == $course_id){
				$current_page_key = $key;
			}
		}

		if( empty($group_courses[$current_page_key-1]) ){
			$prev_page = false;
		}else{
			$prev_course = $group_courses[ $current_page_key - 1];
			$prev_page = get_permalink($prev_course->post_id)."?ldgid=".$ld_group_id;
		}

		if( empty($group_courses[$current_page_key+1]) ){
			$next_page = false;
		}else{
			$next_course = $group_courses[ $current_page_key + 1];
			$next_page = get_permalink($next_course->post_id)."?ldgid=".$ld_group_id;
		}


		$back_url = site_url("course-library");
		
		if(!empty($ld_group_id)) $back_url = get_permalink($ld_group_id);
		?>

		<div class="path-bg">
			<?php 
			$path_background = get_field("path_background");
			if(!empty($path_background)){
			?>
				<img src="<?=$path_background?>" alt="">
			<?php
			}
			?>
			<div class="course-nav">
				<?php 
				if(!empty($prev_page)){
				?>
				<a class="btn-nav btn-prev	" href="<?=$prev_page?>"><img src="<?=get_stylesheet_directory_uri();?>/assets/img/singlecourse/prev-btn.svg" alt="Previous Course"></a>
				<?php 
				}

				if(!empty($next_page)){
				?>
				<a class="btn-nav btn-next	" href="<?=$next_page?>"><img src="<?=get_stylesheet_directory_uri();?>/assets/img/singlecourse/next-btn.svg" alt="Next Course"></a>
				<?php 
				}
				?>
			</div>
		</div>

		
		<a href="<?=$back_url?>" class="btn-back" ><img src="<?=get_stylesheet_directory_uri();?>/assets/img/groups/group-back.png" alt="Back Button"></a>
		<div class="g-actions">
			<img src="<?=get_stylesheet_directory_uri();?>/assets/img/groups/groupcoin.png" alt="Points">
			<div class="gcoins"><svg viewBox="0 0 103 13" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M93.317.63H9.691C4.57.63.424 5.025.424 10.452v2.121c0-5.427 4.147-9.823 9.267-9.823h83.618c5.12 0 9.267 4.396 9.267 9.823v-2.121c0-5.427-4.147-9.824-9.267-9.824h.008Z" fill="#000" fill-opacity=".15" style="mix-blend-mode:multiply"/></svg>
				<span><?=$user_info->data->points?></span>
			</div>
		</div>
		
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title();?></h1>
		</header>
			
		<?php 
		$request = new WP_REST_Request( 'GET' );
		$request->set_param("id", $course_id);
		$course_pathway = \Safar\SafarCourses::get_course_pathway($request);

		if(isset($_GET["testpbd"])){
			echo "<pre>";
					print_r($course_pathway->data);
			echo "</pre>";
		}

		$start_url = "";
		if(!empty($course_pathway->data)){
			foreach($course_pathway->data["steps"] as $step ){
				if ($step["completed_steps"] < $step["total_steps"]){
					if(empty($start_url)) $start_url = $step["url"];
				}
			}

			if(empty($start_url)) $start_url = $course_pathway->data["steps"][0]["url"];
		}

		?>
		<div class="path-content">
			<div class="path-content__inner">
				<p><?=get_field('path_description')?></p>
				<a class="btn-start" href="<?=$start_url?>">START</a>
			</div>
			<div class="course-path">
				<img src="<?=get_field('path_svg')?>" alt="Path">
				<div class="course-path__items">
				<?php
					

					if(!empty($course_pathway->data)){
						foreach($course_pathway->data["steps"] as $step ){
							/* 
							Array
							(
								[title] => Saying Salām
								[completed] => 1
								[position_x] => 2%
								[position_y] => -12px
								[url] => https://journey2jannah.com/courses/textbook-1-term-1/
								[path_percent] => 0
								[youAreHere] => 
								[image] => https://journey2jannah.com/wp-content/uploads/2020/10/chat.png
								[image_complete] => 
								[step] => 1
								[streak] => 0
								[previous_completed] => 1
								[active_step] => 
								[total_steps] => 8
								[completed_steps] => 8
								[step_progress] => 1
								[step_progress_percent] => 100
								[not_started] => 
								[found_active_step] => 
								[last_step] => 
								[background_color] => #73529d
							)
							*/
							
							
							?>
							<a href="<?=$step["url"]?><?="?ldgid=".$ld_group_id?>" 
								class="course-path__item <?=($step["completed"]) ? "completed":""?>" 
								style="left:<?=$step["position_x"]?>; top:<?=$step["position_y"]?>">
							 
								<div>
									<div class="course-path__progress" data-value="<?=$step["step_progress"]?>" data-color="<?=$step["background_color"]?>"></div>
									
									<?php if ($step["completed_steps"] < $step["total_steps"]): ?>
										<div <?=(!empty($step["progress_border_color"])) ? "style='border-color:".$step["progress_border_color"]."'":""?> class="course-path__total" style="background:<?=$step["background_color"]?>"><?=$step["completed_steps"]?>/<?=$step["total_steps"]?></div>
									<?php 
									else:

										
										?>
										<svg class="checkmark " <?=(!empty($step["progress_border_color"])) ? "style='stroke:".$step["progress_border_color"]."'":""?> viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
											<circle class="checkmark__circle" cx="14" cy="14" r="14" fill="<?=$step["background_color"]?>"></circle>
											<path class="checkmark__check" d="M20.817 10l-8 8-3.637-3.636" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
										</svg>
										<?php
										

									endif; ?>

									<div class="course-path__icon" style="background:<?=$step["background_color"]?>">
										<?php 
										$image = $step['image'];
										if( !empty( $image ) ): ?>
											<img src="<?=$image?>" alt="" />
										<?php endif; ?>
									</div>
								</div>
								<div class="course-path__name" <?=(!empty($step["text_color"])) ? "style='color:".$step["text_color"]."'":""?>>
									
									<?php 
									$is_demo_user = \Safar\SafarUser::is_demo_user();
									if($is_demo_user){
										if(!empty($step["linked_object"]->ID)){
											$demo_user_has_access = \Safar\SafarUser::demo_user_has_access($step["linked_object"]->ID);
											if(!$demo_user_has_access){
												?>
												<svg width="70" height="90" viewBox="0 0 70 90" fill="none" xmlns="http://www.w3.org/2000/svg" style="
															width: 20px;
															height: 20px;
															margin-left:10px;
															position: absolute;
															margin-left:-25px;
														">
													<path d="M56.1031 70.2314C57.8115 70.2314 59.2089 68.834 59.2089 67.1256V35.6232C59.2089 24.5123 57.9579 17.0776 55.1522 11.5168C51.297 3.87418 44.5167 0 34.9994 0C25.4821 0 18.7018 3.87418 14.8466 11.5168C12.0418 17.0776 10.7908 24.5114 10.7908 35.6232V67.1256C10.7908 68.834 12.1882 70.2314 13.8957 70.2314H16.1248C17.8323 70.2314 19.2306 68.834 19.2306 67.1256V35.6232C19.2306 29.5896 19.5578 20.9184 22.3826 15.3186C24.7825 10.5613 28.6738 8.43985 35.0003 8.43985C41.3268 8.43985 45.2172 10.5613 47.618 15.3186C50.4428 20.9175 50.77 29.5887 50.77 35.6232V67.1256C50.77 68.834 52.1674 70.2314 53.8758 70.2314H56.1049H56.1031Z" fill="#AAAAAA"/>
													<path d="M69.1801 85.7797C69.1801 88.1009 67.2809 90.0001 64.9597 90.0001H5.03922C2.71797 90.0001 0.818848 88.1009 0.818848 85.7797V39.1504C0.818848 36.8291 2.71797 34.9309 5.03922 34.9309H64.9606C67.2818 34.9309 69.181 36.8291 69.181 39.1504V85.7797H69.1801Z" fill="#FFAB15"/>
													<path d="M5.0392 34.9309C3.17714 34.9309 1.66309 36.4459 1.66309 38.307V84.9354C1.66309 86.7975 3.17714 88.3115 5.0392 88.3115H64.9606C66.8226 88.3115 68.3367 86.7966 68.3367 84.9354V38.307C68.3367 36.4459 66.8217 34.9309 64.9606 34.9309H5.0392Z" fill="#FFC50B"/>
													<path d="M48.531 34.9309H5.0392C3.17714 34.9309 1.66309 36.4459 1.66309 38.307V81.7998L48.531 34.9309Z" fill="#FFD91F"/>
													<path d="M43.8605 57.2431C43.8605 52.3493 39.8932 48.382 34.9994 48.382C30.1056 48.382 26.1375 52.3493 26.1375 57.2431C26.1375 62.1369 29.8806 64.2538 29.8806 64.2538C30.2078 64.5829 30.4039 65.2256 30.3171 65.682L28.4054 75.72C28.3186 76.1765 28.6277 76.548 29.0914 76.548H40.9074C41.3711 76.548 41.6803 76.1756 41.5935 75.72L39.6817 65.682C39.594 65.2256 39.7911 64.5829 40.1183 64.2538C40.1183 64.2538 43.8605 62.2842 43.8605 57.2431Z" fill="#FFAB15"/>
													<path d="M26.9818 58.0873C26.9818 61.8702 30.3181 64.3668 30.3181 64.3668C30.6417 64.6985 31.0205 65.0393 31.1606 65.1234C31.2998 65.2065 31.3432 65.6476 31.2564 66.1023L29.4242 75.719C29.3374 76.1755 29.6465 76.547 30.1102 76.547H39.8861C40.3507 76.547 40.6589 76.1746 40.5721 75.719L38.7399 66.1023C38.6531 65.6467 38.6965 65.2056 38.8357 65.1234C38.9758 65.0393 39.3537 64.6985 39.6782 64.3668C39.6782 64.3668 43.0154 62.0772 43.0154 58.0873C43.0154 53.6663 39.4187 50.0696 34.9977 50.0696C30.5767 50.0696 26.98 53.6663 26.98 58.0873H26.9818Z" fill="#B76732"/>
                                                </svg>
												<?php
											}
										}
									}
									?>
									<?=$step["title"]?>
								</div>

							</a>
							<?php
						}
					}

					// Check rows existexists.
					/*
					if( have_rows('steps') ):
						$all_fields_count = count(get_field('steps'));
						$fields_count = 1;
						// Loop through rows.
						while( have_rows('steps') ) : the_row();
							?>
							<div class="course-path__item" style="left:<?=get_sub_field('postion_x');?>; top:<?=get_sub_field('position_y')?>">
								<div>
									<div class="course-path__progress" data-value="1" data-color="<?=get_sub_field('background_color')?>"></div>
									
								
									<?php if ($fields_count !== $all_fields_count): ?>
										<svg class="checkmark" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><circle class="checkmark__circle" cx="14" cy="14" r="14" fill="<?=get_sub_field('background_color')?>"></circle><path class="checkmark__check" d="M20.817 10l-8 8-3.637-3.636" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
										<div class="course-path__total" style="background:<?=get_sub_field('background_color')?>">0/6</div>
									<?php endif; ?>
									<div class="course-path__icon" style="background:<?=get_sub_field('background_color')?>">
										<?php 
										$image = get_sub_field('incomplete_image');
										if( !empty( $image ) ): ?>
											<img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
										<?php endif; ?>
									</div>
								</div>
								<div class="course-path__name"><?=get_sub_field('title')?></div>
							</div>
							<?php
							$fields_count++;
						// End loop.
						endwhile;
					endif;
					*/
					?>
				</div>
			</div>
		</div>
	</div>
	<!-- The Modal -->
	<div id="certificateModal" class="modal">

		<!-- Modal content -->
		<div class="modal-content">
			<span class="close"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="#B0D178" d="M0 0h24v24H0z"/><path d="M18 6 6 18M6 6l12 12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
			<img src="<?=$course_pathway->data["certificate_image"]?>"/>
			<p>Congratulations! You have <?=$course_pathway->data["course"]->post_title?> path and<br/>earned a Certificate of Completion.</p>
			<a href="<?=$course_pathway->data["certificate_link"]?>" target="_blank" class="btn-download">Download Certificate</a>
		</div>

	</div>
	<script>
		jQuery(document).ready(function ($) {
			$('.btn-start').click(function () {
				$("html, body").animate({ scrollTop: $('.course-path').offset().top - 50 }, 1000);
			});

			
			// When the user clicks anywhere outside of the modal, close it
			window.onclick = function(event) {
				if (event.target == $('#certificateModal')[0]) {
					modalState();
					$('.modal').fadeOut();
				}
			}
			<?php 
			if( $course_pathway->data["course_complete"]){
				#if(empty($course_pathway->data["seen_course_certificate"]) ){
				if(!empty($course_pathway->data["certificate_link"])){
				?>
			
					confetti.start();
					modalState();
					$('#certificateModal').css("display", "flex")
						.hide()
						.fadeIn();

					setTimeout(() => {
						confetti.stop();
					}, 5000);
				<?php
				}
				#}
			}
			?>

			$(window).load(function () {

				$('.course-path__items .course-path__item').each(function () {
					let fillColor = $('.course-path__progress',this).data('color'),
						courseProgress = $('.course-path__progress',this).data('value');
					$('.course-path__progress',this).circleProgress({
						value: courseProgress,
						size: 100,
						startAngle: -1,
						lineCap: 'round',
						emptyFill: '#EFE5FD',
						fill: fillColor,
						thickness:5
					});
					console.log(courseProgress)
					if (courseProgress == 1) {
						$(this).addClass('completed')
					}
				})
			})
		})
	</script>
<?php 
else: 
?>
 <div class="<?php echo esc_attr( learndash_the_wrapper_class() ); ?>">

	<?php
	global $course_pager_results;

	/**
	 * Fires before the topic.
	 *
	 * @since 3.0.0
	 *
	 * @param int $post_id   Post ID.
	 * @param int $course_id Course ID.
	 * @param int $user_id   User ID.
	 */
	do_action( 'learndash-course-before', get_the_ID(), $course_id, $user_id );

	learndash_get_template_part(
		'template-banner.php',
		array(
			'context'   => 'course',
			'course_id' => $course_id,
			'user_id'   => $user_id,
		),
		true
	);
	?>
	
	<div class="bb-grid">

		<div class="bb-learndash-content-wrap">

			<?php
			/**
			 * Fires before the course certificate link.
			 *
			 * @since 3.0.0
			 *
			 * @param int $course_id Course ID.
			 * @param int $user_id   User ID.
			 */
			do_action( 'learndash-course-certificate-link-before', $course_id, $user_id );

			/**
			 * Certificate link
			 */

			if ( isset( $course_certficate_link ) && $course_certficate_link && ! empty( $course_certficate_link ) ) :

				learndash_get_template_part(
					'modules/alert.php',
					array(
						'type'    => 'success ld-alert-certificate',
						'icon'    => 'certificate',
						'message' => __( 'You\'ve earned a certificate!', 'buddyboss-theme' ),
						'button'  => array(
							'url'   => $course_certficate_link,
							'icon'  => 'download',
							'label' => __( 'Download Certificate', 'buddyboss-theme' ),
						),
					),
					true
				);

			endif;

			/**
			 * Fires after the course certificate link.
			 *
			 * @since 3.0.0
			 *
			 * @param int $course_id Course ID.
			 * @param int $user_id   User ID.
			 */
			 do_action( 'learndash-course-certificate-link-after', $course_id, $user_id );


			/**
			 * Course info bar
			 */
			learndash_get_template_part(
				'modules/infobar.php',
				array(
					'context'       => 'course',
					'course_id'     => $course_id,
					'user_id'       => $user_id,
					'has_access'    => $has_access,
					'course_status' => $course_status,
					'post'          => $post,
				),
				true
			);

			/** This filter is documented in themes/legacy/templates/course.php */
			echo apply_filters( 'ld_after_course_status_template_container', '', learndash_course_status_idx( $course_status ), $course_id, $user_id );

			/**
			 * Content tabs
			 */
			echo '<div class="bb-ld-tabs">';
			echo '<div id="learndash-course-content"></div>';
			learndash_get_template_part(
				'modules/tabs.php',
				array(
					'course_id' => $course_id,
					'post_id'   => get_the_ID(),
					'user_id'   => $user_id,
					'content'   => $content,
					'materials' => $materials,
					'context'   => 'course',
				),
				true
			);
			 echo '</div>';

			/**
			 * Identify if we should show the course content listing
			 *
			 * @var $show_course_content [bool]
			 */
			$show_course_content = ( ! $has_access && 'on' === $course_meta['sfwd-courses_course_disable_content_table'] ? false : true );

			if ( $has_course_content && $show_course_content ) :
				?>

				<div class="ld-item-list ld-lesson-list">
					<div class="ld-section-heading">

						<?php
						/**
						 * Fires before the course heading.
						 *
						 * @since 3.0.0
						 *
						 * @param int $course_id Course ID.
						 * @param int $user_id   User ID.
						 */
						do_action( 'learndash-course-heading-before', $course_id, $user_id );
						?>

						<h2>
							<?php
							printf(
								// translators: placeholder: Course.
								esc_html_x( '%s Content', 'placeholder: Course', 'buddyboss-theme' ),
								LearnDash_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
							);
							?>
						</h2>

						<?php
						/**
						 * Fires after the course heading.
						 *
						 * @since 3.0.0
						 *
						 * @param int $course_id Course ID.
						 * @param int $user_id   User ID.
						 */
						do_action( 'learndash-course-heading-after', $course_id, $user_id );
						?>

						<div class="ld-item-list-actions" data-ld-expand-list="true">

							<?php
							/**
							 * Fires before the course expand.
							 *
							 * @since 3.0.0
							 *
							 * @param int $course_id Course ID.
							 * @param int $user_id   User ID.
							 */
							do_action( 'learndash-course-expand-before', $course_id, $user_id );
							?>

							<?php
							// Only display if there is something to expand.
							if ( $has_topics || $has_lesson_quizzes ) :
								?>
								<div class="ld-expand-button ld-primary-background" id="<?php echo esc_attr( 'ld-expand-button-' . $course_id ); ?>" data-ld-expands="<?php echo esc_attr( 'ld-item-list-' . $course_id ); ?>" data-ld-expand-text="<?php echo esc_attr_e( 'Expand All', 'buddyboss-theme' ); ?>" data-ld-collapse-text="<?php echo esc_attr_e( 'Collapse All', 'buddyboss-theme' ); ?>">
									<span class="ld-icon-arrow-down ld-icon"></span>
									<span class="ld-text"><?php echo esc_html_e( 'Expand All', 'buddyboss-theme' ); ?></span>
								</div> <!--/.ld-expand-button-->
								<?php
								// TODO @37designs Need to test this
								/**
								 * Filters whether to expand all course steps by default. Default is false.
								 *
								 * @since 2.5.0
								 *
								 * @param boolean $expand_all Whether to expand all course steps.
								 * @param int     $course_id  Course ID.
								 * @param string  $context    The context where course is expanded.
								 */
								if ( apply_filters( 'learndash_course_steps_expand_all', false, $course_id, 'course_lessons_listing_main' ) ) :
									?>
									<script>
										jQuery(document).ready(function(){
											jQuery("<?php echo '#ld-expand-button-' . $course_id; ?>").click();
										});
									</script>
									<?php
								endif;

							endif;

							/**
							 * Fires after the course content expand button.
							 *
							 * @since 3.0.0
							 *
							 * @param int $course_id Course ID.
							 * @param int $user_id   User ID.
							 */
							do_action( 'learndash-course-expand-after', $course_id, $user_id );
							?>

						</div> <!--/.ld-item-list-actions-->
					</div> <!--/.ld-section-heading-->

					<?php
					/**
					 * Fires before the course content listing
					 *
					 * @since 3.0.0
					 *
					 * @param int $course_id Course ID.
					 * @param int $user_id   User ID.
					 */
					do_action( 'learndash-course-content-list-before', $course_id, $user_id );

					/**
					 * Content listing
					 *
					 * @since 3.0.0
					 *
					 * ('listing.php');
					 */
					learndash_get_template_part(
						'course/listing.php',
						array(
							'course_id'                  => $course_id,
							'user_id'                    => $user_id,
							'lessons'                    => $lessons,
							'lesson_topics'              => @$lesson_topics,
							'quizzes'                    => $quizzes,
							'has_access'                 => $has_access,
							'course_pager_results'       => $course_pager_results,
							'lesson_progression_enabled' => $lesson_progression_enabled,
						),
						true
					);

					/**
					 * Fires before the course content listing.
					 *
					 * @since 3.0.0
					 *
					 * @param int $course_id Course ID.
					 * @param int $user_id   User ID.
					 */
					do_action( 'learndash-course-content-list-after', $course_id, $user_id );
					?>

				</div> <!--/.ld-item-list-->

				<?php
			endif;

			learndash_get_template_part(
				'template-course-author-details.php',
				array(
					'context'   => 'course',
					'course_id' => $course_id,
					'user_id'   => $user_id,
				),
				true
			);

			?>

		</div>

		<?php
		// Single course sidebar.
		learndash_get_template_part( 'template-single-course-sidebar.php', $template_args, true );
		?>
	</div>
	
	<?php

	/**
	 * Fires before the topic.
	 *
	 * @since 3.0.0
	 *
	 * @param int $post_id   Post ID.
	 * @param int $course_id Course ID.
	 * @param int $user_id   User ID.
	 */
	do_action( 'learndash-course-after', get_the_ID(), $course_id, $user_id );
	if ( ! is_user_logged_in() ) {
		global $login_model_load_once;
		$login_model_load_once      = false;
		$learndash_login_model_html = learndash_get_template_part( 'modules/login-modal.php', array(), false );
		echo '<div class="learndash-wrapper learndash-wrapper-login-modal">' . $learndash_login_model_html . '</div>';
	}
	?>

</div>
<?php 
endif; ?>
