<?php 
$gender = get_user_meta(get_current_user_id(), "gender", true);
?>
<div class="field-group profile-gender">
    <label class="field-label">Gender</label>
    <div class="field-input gender">
        <label>
            <input type="radio" name="gender" value="male" <?php if($gender=="male") echo "checked='checked'"; ?> /> Male
        </label>
        <label>
            <input type="radio" name="gender" value="female" <?php if($gender=="female") echo "checked='checked'"; ?> /> Female
        </label>
    </div>
</div>

<div class="button-container">
    <button type="button" class="btn btn-save-gender">Save</button>
</div>
