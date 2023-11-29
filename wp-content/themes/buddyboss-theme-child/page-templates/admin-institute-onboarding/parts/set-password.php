<?php 
$gender = get_user_meta(get_current_user_id(), "gender", true);
if(empty($gender)) $gender = "male";
$current_user = wp_get_current_user();
?>
<div class="set-password tab <?=(!isset($_GET["tab"]) ? "active":"")?>">
    <div class="choose-avatar-title">
        <div class="choose-avatar-title-label">Set Your Password</div>
    </div>
    <div class="set-password-input">
        <div class="input email">
            <label>Email</label>
            <input type="email" name="email" value="<?=$current_user->user_email?>" readonly />
        </div>

        <div class="input password">
            <label>Password</label>
            <input type="password" name="password" class="input-password" placeholder="Enter password"/>
            <button type="button" class="btn-view-password">
                 
            </button>
        </div>

        <div class="input password password2">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="input-password" placeholder="Confirm password"/>
            <button type="button" class="btn-view-password">
                 
            </button>
        </div>
        <div id="pwd-indicator"></div> 
        
        <button type="button" class="btn btn-set-password" disabled>Submit</button>

    </div>
    
</div>