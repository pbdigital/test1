<?php 
$gender = get_user_meta(get_current_user_id(), "gender", true);
if(empty($gender)) $gender = "male";
?>
<div class="gender-select tab active">
    <div class="title">
        Select your gender
    </div>
    <div class="select-group">
        <label>
            <span class="checkmark">
                <input class="select-gender" type="radio" name="gender" value="male" <?=($gender=="male") ? "checked":""?> />
                <span></span>
            </span>
            <img src="/wp-content/themes/buddyboss-theme-child/assets/img/choose-avatar/male.png" style="height:180px" />
            <div class="group-name">Boy</div>
        </label>
        <label>
            <span class="checkmark">
                <input class="select-gender" type="radio" name="gender" value="female" <?=($gender=="female") ? "checked":""?>/>
                <span></span>
            </span>
            <img src="/wp-content/themes/buddyboss-theme-child/assets/img/choose-avatar/female.png"  style="height:180px"  />
            <div class="group-name">Girl</div>
        </label>
    </div>

    <button type="button" class="btn btn-select-gender">Select</button>
</div>