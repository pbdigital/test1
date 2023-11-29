<?php
/**
 * BuddyBoss - Groups Loop
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/groups-loop.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 *
 * @package BuddyBoss\Core
 */

$_POST["scope"] = "personal";

add_filter( 'bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2 );

bp_nouveau_before_loop(); ?>

<?php if ( bp_get_current_group_directory_type() ) : ?>
	<div class="bp-feedback info">
		<span class="bp-icon" aria-hidden="true"></span>
		<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
	</div>
<?php endif; ?>

<?php

$cover_class        = ! bb_platform_group_element_enable( 'cover-images' ) ? 'bb-cover-disabled' : 'bb-cover-enabled';
$meta_privacy       = ! bb_platform_group_element_enable( 'group-privacy' ) ? 'meta-privacy-hidden' : '';
$meta_group_type    = ! bb_platform_group_element_enable( 'group-type' ) ? 'meta-group-type-hidden' : '';
$group_members      = ! bb_platform_group_element_enable( 'members' ) ? 'group-members-hidden' : '';
$join_button        = ! bb_platform_group_element_enable( 'join-buttons' ) ? 'group-join-button-hidden' : '';
$group_alignment    = bb_platform_group_grid_style( 'left' );
$group_cover_height = function_exists( 'bb_get_group_cover_image_height' ) ? bb_get_group_cover_image_height() : 'small';

?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<ul id="groups-list  " test123 class="
	<?php
	bp_nouveau_loop_classes();
	echo esc_attr( ' ' . $cover_class . ' ' . $group_alignment );
	?>
	 groups-dir-list">

		<?php
		$i = 0;
		$is_user_teacher = \Safar\SafarUser::is_user_teacher( );
		while ( bp_groups() ) :
			bp_the_group();

			$ld_group_id = \Safar\SafarSchool::get_learndash_group_id_from_bp_group_id( bp_get_group_id() );
			$terms = wp_get_post_terms( $ld_group_id, "ld_group_tag" );

			$is_entire_school = \Safar\SafarSchool::is_entire_school_group($ld_group_id);
			$is_overall_school = false;
			foreach($terms as $eterm){
				if($eterm->slug=="overall-school") $is_overall_school = true;
			}

			$child_group = false;
			$group_post = get_post($ld_group_id);
			if(!empty($group_post->post_parent)){
				$ld_group_category = wp_get_post_terms($ld_group_id,"ld_group_category");
				if(!empty($ld_group_category)){
					$child_group = true;
				}
			}
			/*
			echo "<pre style='display:none'>";
				
				print_r([
					"post_title" => $group_post->post_title,
					"post_parent" => $group_post->post_parent,
					"ld_group_id"=>$ld_group_id,"ld_group_category"=>$ld_group_category,"is_user_teacher"=>$is_user_teacher,"is_entire_school"=>$is_entire_school,"is_oeverall_school"=>$is_overall_school]);
			echo "</pre>";
			*/

			if(!empty($terms) || $is_user_teacher || $child_group){

				if(!$is_entire_school && !$is_overall_school){
				?>

				<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups" data-col="<?=$i?>" 
				data-ldgroupid="<?=$ld_group_id?>"
				data-isentireschool="<?=$is_entire_school?>" 
				data-is_overall_school="<?=$is_overall_school?>" 
				>
					<div class="list-wrap">

						<?php
						if ( ! bp_disable_group_cover_image_uploads() && bb_platform_group_element_enable( 'cover-images' ) ) {
							$group_cover_image_url = bp_attachments_get_attachment(
								'url',
								array(
									'object_dir' => 'groups',
									'item_id'    => bp_get_group_id(),
								)
							);
							$has_default_cover     = function_exists( 'bb_attachment_get_cover_image_class' ) ? bb_attachment_get_cover_image_class( bp_get_group_id(), 'group' ) : '';
							
							//class_cover_photo
							
							$class_cover_photo = get_post_meta($ld_group_id,"class_cover_photo",true);
							$group_cover_image_url = wp_get_attachment_url($class_cover_photo);
							
							?>
							<div style="background:url('<?=$group_cover_image_url?>'); background-size:cover; background-position:-7px" class="bs-group-cover only-grid-view <?php echo esc_attr( $has_default_cover . ' cover-' . $group_cover_height ); ?>">
								<a href="<?php bp_group_permalink(); ?>">
									<?php if ( ! empty( $group_cover_image_url ) ) { ?>
										<img src="<?php echo esc_url( $group_cover_image_url ); ?>" test123>
									<?php } ?>
								</a>
							</div>

							<?php
						}

						if ( ! bp_disable_group_avatar_uploads() && bb_platform_group_element_enable( 'avatars' ) ) :
							?>
							<div class="item-avatar"><a href="<?php bp_group_permalink(); ?>" class="group-avatar-wrap"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a></div>
						<?php endif; ?>

						<div class="item <?php echo esc_attr( $group_members . ' ' . $join_button ); ?>">

							<div class="group-item-wrap">

								<div class="item-block">

									<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>

									<div class="item-meta-wrap <?php echo esc_attr( bb_platform_group_element_enable( 'last-activity' ) || empty( $meta_privacy ) || empty( $meta_group_type ) ? 'has-meta' : 'meta-hidden' ); ?> ">

										<?php if ( bp_nouveau_group_has_meta() ) : ?>

											<p class="item-meta group-details <?php echo esc_attr( $meta_privacy . ' ' . $meta_group_type ); ?>">
												<?php
												$meta = bp_nouveau_get_group_meta();
												echo wp_kses_post( $meta['status'] );
												?>
											</p>
											<?php
										endif;

										if ( bb_platform_group_element_enable( 'last-activity' ) ) {
											echo '<p class="last-activity item-meta">' .
											sprintf(
													/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
												esc_attr__( 'Active %s', 'buddyboss' ),
												wp_kses_post( bp_get_group_last_active() )
											) .
											'</p>';
										}
										?>

									</div>

								</div>

								<?php if ( bb_platform_group_element_enable( 'group-descriptions' ) ) { ?>
									<div class="item-desc group-item-desc only-list-view"><?php bp_group_description_excerpt( false, 150 ); ?></div>
								<?php } ?>

							</div>

							<?php bp_nouveau_groups_loop_item(); ?>

							<div class="group-footer-wrap <?php echo esc_attr( $group_members . ' ' . $join_button ); ?>">
								<div class="group-members-wrap">
									<?php bb_groups_loop_members(); ?>
								</div>
								<?php if ( bb_platform_group_element_enable( 'join-buttons' ) ) { ?>
									<div class="groups-loop-buttons footer-button-wrap"><?php bp_nouveau_groups_loop_buttons(); ?></div>
								<?php } ?>
							</div>

						</div>


					</div>
				</li>

				<?php 
				}
				
				$i++;
				if($i == 3 ) $i = 0;
			}

		endwhile; ?>

	</ul>

	<!-- Leave Group confirmation popup -->
	<div id="leave-group-popup" class="bb-leave-group-popup bb-action-popup" style="display: none">
		<transition name="modal">
			<div class="modal-mask bb-white bbm-model-wrap">
				<div class="modal-wrapper">
					<div class="modal-container">
						<header class="bb-model-header">
							<h4><span class="target_name"><?php esc_html_e( 'Leave Group', 'buddyboss' ); ?></span></h4>
							<a class="bb-close-leave-group bb-model-close-button" href="#">
								<span class="bb-icon-l bb-icon-times"></span>
							</a>
						</header>
						<div class="bb-leave-group-content bb-action-popup-content">
							<p><?php esc_html_e( 'Are you sure you want to leave ', 'buddyboss' ); ?><span class="bb-group-name"></span>?</p>
						</div>
						<footer class="bb-model-footer flex align-items-center">
							<a class="bb-close-leave-group bb-close-action-popup" href="#"><?php esc_html_e( 'Cancel', 'buddyboss' ); ?></a>
							<a class="button push-right bb-confirm-leave-group" href="#"><?php esc_html_e( 'Confirm', 'buddyboss' ); ?></a>
						</footer>

					</div>
				</div>
			</div>
		</transition>
	</div> <!-- .bb-leave-group-popup -->

	<?php bp_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php bp_nouveau_user_feedback( 'groups-loop-none' ); ?>

<?php endif; ?>



<script type="text/javascript">
	var observer = new MutationObserver(function(mutations) {
		mutations.forEach(function(mutationRecord) {
			var display = jQuery("#leave-group-popup").css("display");
			if(display == "block"){
				$('body').addClass('modal-open');
			}else{
				$('body').removeClass('modal-open')
			}
		});    
	});

	var target = document.getElementById('leave-group-popup');
	observer.observe(target, { attributes : true, attributeFilter : ['style'] });
</script>

<?php



bp_nouveau_after_loop();

remove_filter( 'bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2 );
