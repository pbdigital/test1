<?php 
namespace ld_classroom;
// $all_primary_groups = Group::get_all_primary_groups();
if(count($all_primary_groups) > 1 ){
    if(isset($_GET['parent-group-id'])){
        $_get_parent_group_id = $_GET['parent-group-id'];
    }
    
    if( isset($shortcode_name) && in_array($shortcode_name,['ld_classroom_primary_report', 'ld_classroom_report']) && isset($_GET['group']) && !empty($_GET['group'])){
        $group_id = general_encrypt_decrypt('decrypt', $_GET['group']);
        $_get_parent_group_id = Group::get_parent_group_id($group_id);
    }
   
?>
<div class="classroom_admin_header">

    <div class="form-group section">
        <div class="col grid_4_of_4">
            <label class="title"><?php _e("Select School","lt-learndash-classroom"); ?>:</label>
            <select name="primary_group_id">
                <?php foreach($all_primary_groups as $single_primary_group) :?>
                <option value="<?php _e($single_primary_group->ID); ?>"
                    <?php _e(isset($_get_parent_group_id) && $_get_parent_group_id == $single_primary_group->ID ? "selected":"") ?>>
                    <?php _e($single_primary_group->post_title); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('select[name="primary_group_id"]').on('change', function() {
            var parent_group_id = $(this).val(); // get selected value
            if (parent_group_id) { // require a URL
                window.location = "?parent-group-id=" + parent_group_id; // redirect
            }
            return false;
        });
    });
    </script>
</div>
<?php } ?>