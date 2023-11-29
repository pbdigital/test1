<?php 
$gender = get_user_meta(get_current_user_id(), "gender", true);
if(empty($gender)) $gender = "male";

$relationship = strtolower(get_user_meta(get_current_user_id(),"relationship",true));
if($relationship == "father" || $relationship == "uncle") $gender = "male";
else $gender = "female";
?>
<div class="gender-select tab <?=(($_GET["tab"] == "gender") ? "active":"")?>" data-relationship="<?=$relationship?>">
    <div class="choose-avatar-title">
        <div class="choose-avatar-title-label">Select Your Gender</div>
    </div>
    <div class="select-group">
        <label class=" <?=($gender=="male") ? "active":""?>" >
            <span class="checkmark">
                <input class="select-gender" type="radio" name="gender" value="male" <?=($gender=="male") ? "checked":""?> />
                <span></span>
            </span>
            <img src="/wp-content/themes/buddyboss-theme-child/assets/img/admin-institute-onboarding/male.svg" style="height:180px" />
            <div class="group-name">Male</div>
        </label>
        <label class=" <?=($gender=="female") ? "active":""?>">
            <span class="checkmark">
                <input class="select-gender" type="radio" name="gender" value="female" <?=($gender=="female") ? "checked":""?>/>
                <span></span>
            </span>
            <img src="/wp-content/themes/buddyboss-theme-child/assets/img/admin-institute-onboarding/female.svg"  style="height:180px"  />
            <div class="group-name">Female</div>
        </label>
    </div>

    <button type="button" class="btn btn-select-gender">Continue</button>
</div>