<?php
/**
 * LearnDash LD30 Displays the listing of course row content
 *
 * @var int    $group_id            Group ID.
 * @var int    $user_id             User ID.
 * @var int    $course_id           Course ID.
 * @var bool   $has_access          User has access to group or is enrolled.
 *
 * @since 3.1.7
 *
 * @package LearnDash\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course      = get_post( $course_id );
$course_link = get_permalink( $course_id );

$progress = learndash_course_progress(
	array(
		'user_id'   => $user_id,
		'course_id' => $course_id,
		'array'     => true,
	)
);

/**
 * The logic in learndash_course_progress() should
 * return an array of elements. However, when scanning
 * other calls to this function some check if the returned
 * value is an empty string.
 */
if ( ! is_array( $progress ) ) {
	$progress = array();
}

if ( ! isset( $progress['percentage'] ) ) {
	$progress['percentage'] = 0;
}
if ( ! isset( $progress['completed'] ) ) {
	$progress['completed'] = 0;
}

if ( ! isset( $progress['total'] ) ) {
	// We can set the 'total' to zero because the detail is not displayed.
	$progress['total'] = 0;
}

$has_group_access = false;
$status           = '';

if ( $has_access ) {
	if ( learndash_is_user_in_group( $user_id, $group_id ) ) {
		$has_group_access = true;
	}

	$status = ( 100 === $progress['percentage'] ) ? 'completed' : 'notcompleted';

	if ( $progress['percentage'] > 0 && 100 !== $progress['percentage'] ) {
		$status = 'progress';
	}
}

$course_class = apply_filters(
	'learndash-course-row-class',
	'ld-item-list-item ld-item-list-item-course ld-expandable ' . ( 100 === $progress['percentage'] ? 'learndash-complete' : 'learndash-incomplete' ),
	$course,
	$user_id
); ?>

<div class="<?php echo esc_attr( $course_class ); ?>" id="<?php echo esc_attr( 'ld-course-list-item-' . $course_id ); ?>">
	<div class="ld-item-list-item-preview" data-img="<?=get_field('course_background_image', $course_id );?>" testitempreview>

		<?php 
		
		$course_details = \Safar\SafarCourses::get_group_course_details($group_id, $course_id);

		/*echo "<pre>";
			print_r($course_details);
		echo "</pre>";*/
		?>
		<a href="<?php echo esc_url( get_the_permalink( $course_id )."?ldgid=".$group_id ); ?>" class="ld-item-name">
			<?php #learndash_status_icon( $status, get_post_type(), null, true ); 
			$course_abbreviation = get_field("course_abbreviation", $course_id);
			if(empty($course_abbreviation)) $course_abbreviation = esc_html( get_the_title( $course_id ) );
			?>
			<span class="ld-course-title"><?php echo $course_abbreviation; ?></span>
			<img src="<?=($course_details["course_image"]) ? $course_details["course_image"]:"/wp-content/uploads/2022/09/Group-7234.png"?>" alt="">
			
		</a> <!--/.ld-course-name-->

		<?php /*if ( true === $has_group_access ) { ?>
			<div class="ld-item-details">
				<?php echo wp_kses_post( learndash_status_bubble( $status ) ); ?>
				<div class="ld-expand-button ld-primary-background ld-compact ld-not-mobile" data-ld-expands="<?php echo esc_attr( 'ld-course-list-item-' . $course_id ); ?>">
					<span class="ld-icon-arrow-down ld-icon"></span>
				</div> <!--/.ld-expand-button-->

				<div class="ld-expand-button ld-button-alternate ld-mobile-only" data-ld-expands="<?php echo esc_attr( 'ld-course-list-item-' . $course_id ); ?>"  data-ld-expand-text="<?php esc_html_e( 'Expand', 'learndash' ); ?>" data-ld-collapse-text="<?php esc_html_e( 'Collapse', 'learndash' ); ?>">
					<span class="ld-icon-arrow-down ld-icon"></span>
					<span class="ld-text ld-primary-color"><?php esc_html_e( 'Expand', 'learndash' ); ?></span>
				</div> <!--/.ld-expand-button-->

			</div> <!--/.ld-course-details-->
			<?php } */?>
		<div class="course-items">

			<?php 
			foreach($course_details["lessons"] as $lesson){
				?>
				<a href="<?=$lesson["lesson_url"]?>" class="course-items__btn <?=($lesson["completed"]) ? "active":""?>"></a>
				<?php
			}
			/*
			<a href="#" class="course-items__btn active"></a>
			<a href="#" class="course-items__btn active"></a>
			<a href="#" class="course-items__btn active"></a>
			<a href="#" class="course-items__btn"></a>
			<a href="#" class="course-items__btn"></a>
			<a href="#" class="course-items__btn"></a>
			*/?>
		</div>
	</div> <!--/.ld-course-preview-->
	<div class="ld-item-list-item-expanded">

		<?php
		learndash_get_template_part(
			'shortcodes/profile/course-progress.php',
			array(
				'user_id'   => $user_id,
				'course_id' => $course_id,
				'progress'  => $progress,
			),
			true
		);

		?>

	</div> <!--/.ld-course-list-item-expanded-->

	<?php 
	

	if($progress["percentage"] > 0){
		$button_text = "Continue";
	}else{
		$button_text = "Start";
	}
	?>

	<a href="<?php echo esc_url(  get_the_permalink( $course_id )."?ldgid=".$group_id ); ?>" class="btn-start"><?=$button_text?></a>

</div> <!--/.ld-course-list-item-->
