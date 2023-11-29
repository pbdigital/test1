<form onsubmit="JourneyManageClassrooms.classrooms.createNewNavigate(this); return false;">
    <input type="hidden" name="tab" value="courses" />
    <input type="hidden" name="classroom_id" class="new-group-id" value=""/>

    <div class="field-group cover-photos">
        <label class="field-label">Choose Cover Photo</label>
        <div class="field-input">
            <?php 
            for($i=1; $i <= 12; $i++){
                ?>
                <label>
                    <input type="radio" name="cover_photo_selected" data-tab="cover_photo" class="create-new-radio" value="<?=get_template_directory()?>-child/assets/img/manage-classroom/cover-photo/cover-photo-<?=$i?>.png"/>
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/manage-classroom/cover-photo/cover-photo-<?=$i?>.png"/>
                </label>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="divider"><span>OR</span></div>

    <div class="field-group upload-cover-photo">
        <label class="field-label">Upload Cover Photo</label>
        <div class="field-input">
            <p>Please use a minimum of 1400x550 px for better quality</p>
            <div id="import-cover-photo-dropzone" class="dropzone" action="?" ></div>
        </div>
    </div>

    <div class="button-container">
        <button type="button" class="btn-back btn-back-step">BACK</button>
        <button type="submit" class="btn-save btn-next">NEXT</button>
    </div>
</form>