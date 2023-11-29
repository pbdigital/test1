
 <form onsubmit="JourneyManageClassrooms.classrooms.createNewNavigate(this); return false;" class="create-new-avatar">
    <input type="hidden" name="tab" value="avatar" />
    <input type="hidden" name="classroom_id" class="new-group-id" value=""/>
    <div class="field-group avatars">
        <label class="field-label">Choose Class Avatar</label>
        <div class="field-input">
            <?php 
            for($i=1; $i <= 10; $i++){
                ?>
                <label>
                    <input type="radio" name="avatar_selected" data-tab="avatar" class="create-new-radio" value="<?=get_template_directory()?>-child/assets/img/manage-classroom/avatars/avatar-<?=$i?>.png"/>
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/manage-classroom/avatars/avatar-<?=$i?>.png"/>
                </label>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="divider"><span>OR</span></div>

    <div class="field-group upload-avatar">
        <label class="field-label">Upload Class Avatar</label>
        <div class="field-input">
            <p>Please use a minimum of 600x600 px for better quality</p>
            <div id="import-avatar-dropzone" class="dropzone" action="?" ></div>

        </div>
    </div>

    <div class="button-container">
        <button type="button" class="btn-back btn-back-step">BACK</button>
        <button type="submit" class="btn-save btn-next">NEXT</button>
    </div>
</form>