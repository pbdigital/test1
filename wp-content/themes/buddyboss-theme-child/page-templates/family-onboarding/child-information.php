<?php 
$family_groups = \Safar\SafarFamily::get_family_details($group_id);
$remaining_seats = $family_groups["remaining_seats"];
$total_seats = $family_groups["total_seats"];
$seats_taken = $family_groups["seats_taken"];

?>

<div class="child-information-container" data-seatstaken="<?=$seats_taken?>">
    <div class="top">
        <h3><?=$family_groups["post"]->post_title?></h3>
        <div class="remaining-seats"><?=$seats_taken?>/<?=$total_seats?></div>
    </div>
    <div class="main">
        <?php 
        if(!empty($remaining_seats)){
            ?>
            <div class="child-add-cards">
            <?php
            for($i=0; $i < $remaining_seats; $i++){
                do_action("family-child-add", $family_groups);
            }
            ?>
            </div>
            <?php
        }
        ?>

        

    </div>
</div>

<div class="child-add-container">
    <div class="create-account-message"></div>
    <div class="steps">
        <div class="step child-name active count-progress">
            <div id="field_2_69" class="gfield gfield--width-full gfield_contains_required field_sublabel_below field_description_below gfield_visibility_visible" data-js-reload="field_2_69">
                <label class="gfield_label" for="input_2_69">
                    What is the name of your first child?
                </label>
                <div class="ginput_container ginput_container_text">
                    <input name="child_name" type="text" value="" class="large" placeholder="Name" aria-required="true" aria-invalid="false"> 
                </div>
            </div>

            <button type="button" class="btn-submit-new-child submit" disabled>Submit</button>
        </div>

        <div class="step gender count-progress">
            <h3></h3>
            <div class="select-gender">
                <label>
                    <input type="radio" name="gender" value="female" checked> <span class="ck"></span>
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/family-onboarding/female.png">
                    <div class="text">Female</div>
                </label>
            
                <label>
                    <input type="radio" name="gender" value="male"> <span class="ck"></span>
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/family-onboarding/male.png">
                    <div class="text">Male</div>
                </label>
            </div>
            <button type="button" class="btn-submit-new-child " >Submit</button>
        </div>
        
        <div class="step birthday count-progress">
            <div id="field_2_69" class="gfield gfield--width-full gfield_contains_required field_sublabel_below field_description_below gfield_visibility_visible" data-js-reload="field_2_69">
                <label class="gfield_label birthday-label-container" for="input_2_69">
                </label>
                <div class="ginput_container ginput_container_text">
                    <input name="birthday" id="child-add-birthday"  type="text" value="" class="large" placeholder="DD-MMM-YYYY" aria-required="true" aria-invalid="false" autocomplete="off"> 

                    <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 1V5M6 1V5M1 9H19M3 3H17C18.1046 3 19 3.89543 19 5V19C19 20.1046 18.1046 21 17 21H3C1.89543 21 1 20.1046 1 19V5C1 3.89543 1.89543 3 3 3Z" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                </div>
            </div>

            <button type="button" class="btn-submit-new-child submit" disabled>Submit</button>
        </div>

        <div class="step account count-progress">
            <div id="field_2_69" class="gfield gfield--width-full gfield_contains_required field_sublabel_below field_description_below gfield_visibility_visible" data-js-reload="field_2_69">
                <label class="gfield_label account-label-container" for="input_2_69" class="account-label-container">
                    
                </label>
                <div class="ginput_container ginput_container_text">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6M22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6M22 6L12 13L2 6" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <input name="email" type="text" value="" class="large" placeholder="Email (Optional)" aria-required="true" aria-invalid="false"> 
                </div>

                <div class="ginput_container ginput_container_text">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <input name="username" type="text" value="" class="large" placeholder="Username" aria-required="true" aria-invalid="false"> 
                </div>
                <div class="readonly"></div>

                <div class="ginput_container ginput_container_text">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.63636 10.1818V6.54545C7.63636 5.33993 8.11526 4.18377 8.9677 3.33133C9.82013 2.47889 10.9763 2 12.1818 2C13.3873 2 14.5435 2.47889 15.3959 3.33133C16.2484 4.18377 16.7273 5.33993 16.7273 6.54545V10.1818M5.81818 10.1818H18.5455C19.5496 10.1818 20.3636 10.9958 20.3636 12V20.1818C20.3636 21.186 19.5496 22 18.5455 22H5.81818C4.81403 22 4 21.186 4 20.1818V12C4 10.9958 4.81403 10.1818 5.81818 10.1818Z" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <input name="password" type="text" value="" class="large" placeholder="Password" aria-required="true" aria-invalid="false"> 
                </div>


            </div>

            <button type="button" class="btn-submit-new-child submit" disabled>Submit</button>
        </div>

        <div class="step last">
            <div class="spinner">
                <svg width="81" height="80" viewBox="0 0 81 80" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <rect x="0.5" width="80" height="80" fill="url(#pattern0)"/>
                    <defs>
                    <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                    <use xlink:href="#image0_1941_20524" transform="scale(0.00833333)"/>
                    </pattern>
                    <image id="image0_1941_20524" width="120" height="120" xlink:href="data:image/png;base64,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"/>
                    </defs>
                </svg>
            </div>

            <p class="please-wait">Saving your child’s information...</p>
        </div>
    </div>
</div>