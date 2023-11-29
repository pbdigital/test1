<?php 
add_action( 'bp_groups_admin_meta_boxes', 'bpg_safar_add_admin_metabox' );
function bpg_safar_add_admin_metabox() {	
	add_meta_box( 
		'bp_group_subject', // Meta box ID 
		'Group Subject', // Meta box title
		'bpg_safar_render_admin_metabox', // Meta box callback function
		get_current_screen()->id, // Screen on which the metabox is displayed. In our case, the value is toplevel_page_bp-groups
		'side', // Where the meta box is displayed
		'core' // Meta box priority
	);
}



function bpg_safar_render_admin_metabox() {
	$group_id = intval( $_GET['gid'] );
	$group_subject_id = intval( groups_get_groupmeta( $group_id, 'group_subject_id' ) );
	
    $terms = get_terms( array(
        'taxonomy' => 'ld_group_category',
        'hide_empty' => false,
    ) );

    ?>

	<div class="bp-groups-settings-section" id="bp-groups-settings-section-content-protection">
		<fieldset>
			<legend>Select Subject</legend>
			<label>
				
                <select name="group_subject_id">
                    <option></option>
                    <?php 
                    foreach($terms as $term){
                        ?>
                        <option <?=($group_subject_id==$term->term_id) ? "selected='selected'":""?> value="<?=$term->term_id?>"><?=$term->name?></option>
                        <?php
                    }
                    ?>
                </select>
			</label>
		</fieldset>
	</div>

	<?php
}




/* Start Meta Box for select Catch All Group
add_action( 'bp_groups_admin_meta_boxes', 'buddyboss_group_custom_catchall_metabox' );
function buddyboss_group_custom_catchall_metabox() {	
	add_meta_box( 
		'bp_catch_al_group', // Meta box ID 
		'Catch All Group', // Meta box title
		'buddyboss_render_group_custom_catchall_metabox', // Meta box callback function
		get_current_screen()->id, // Screen on which the metabox is displayed. In our case, the value is toplevel_page_bp-groups
		'side', // Where the meta box is displayed
		'core' // Meta box priority
	);
}

function buddyboss_render_group_custom_catchall_metabox() {
	$group_id = intval( $_GET['gid'] );
	$child_groups = groups_get_groups(["show_hidden"=>true, "parent_id"=>$group_id]);
	$catch_all_group_id = intval( groups_get_groupmeta( $group_id, 'catch_all_group_id' ) );

    ?>
	<div class="bp-groups-settings-section" id="bp-groups-settings-section-content-protection">
		<fieldset>
			<legend>Select Child Group</legend>
			<label>
				<select name="catch_all_group_id">
                    <option></option>
                    <?php 
					if(!empty($child_groups["total"])){
						foreach($child_groups["groups"] as $child_group){
							?>
							<option <?=($catch_all_group_id==$child_group->id) ? "selected='selected'":""?> value="<?=$child_group->id?>"><?=$child_group->name?></option>
							<?php
						}
					}
                    ?>
                </select>
			</label>
		</fieldset>
	</div>

	<?php
}

/* End meta box for select catch all group

*/
add_action( 'bp_group_admin_edit_after', 'bpg_safar_save_metabox_fields' );
function bpg_safar_save_metabox_fields( $group_id ) {
	$group_subject_id = intval( $_POST['group_subject_id'] );
	$catch_all_group_id = intval( $_POST["catch_all_group_id"] );

	groups_update_groupmeta( $group_id, 'group_subject_id', $group_subject_id );
	groups_update_groupmeta( $group_id, 'catch_all_group_id', $catch_all_group_id );
}

?>