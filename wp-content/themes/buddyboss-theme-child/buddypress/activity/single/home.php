<?php
/**
 * The template for BuddyBoss - Home
 *
 * This template can be overridden by copying it to yourtheme/buddypress/activity/single/home.php.
 *
 * @since   BuddyBoss 1.0.0
 * @version 1.0.0
 */

$activity_id = bp_current_action();
$activity_meta = get_metadata( 'activity', $activity_id );
$activity_meta_bp = bp_activity_get_meta( $activity_id, "bp_group_id");

$activity_details = bp_activity_get(["in"=>[$activity_id]]);

$activity = bp_activity_get_specific( array( 'activity_ids' => $activity_id ) );
// Check if the activity exists
if ( ! empty( $activity['activities'] ) ) {
    $activity_item = $activity['activities'][0];
    if ( $activity_item->component === 'groups' ) {
		$group_id = $activity['activities'][0]->item_id;
		$group_permalink = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) )."#activity-".$activity_id;

		wp_redirect($group_permalink);
	}
}

?>

<div id="bp-nouveau-single-activity-edit-form-wrap" style="display: none;">
	<div id="bp-nouveau-activity-form" class="activity-update-form<?php if ( !bp_is_active( 'media' ) ){ echo ' media-off'; } ?>"></div>
</div>

<?php bp_nouveau_template_notices(); ?>

<?php bp_nouveau_before_single_activity_content(); ?>

<div class="activity" data-bp-single="<?php echo esc_attr( bp_current_action() ); ?>">



	<?php do_action( 'bp_before_single_activity_content' ); 
	 
	?>

	<ul id="activity-stream" class="activity-list item-list bp-list" data-bp-list="activity">

		<li id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'single-activity-loading' ); ?></li>

	</ul>


	<?php do_action( 'bp_after_single_activity_content' ); ?>

</div>
