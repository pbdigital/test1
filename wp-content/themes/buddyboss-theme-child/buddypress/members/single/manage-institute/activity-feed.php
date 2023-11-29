<?php 
$activity_settings[] = [
	"key" => "activity_feed_status",
	"name" => "Activity Feeds",
	"description" => "Which members of this group are allowed to post into the activity feed?",
	"tooltip" => "Short description about the about activity feeds goes here. Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore."
];

$activity_settings[] = [
	"key" => "media_status",
	"name" => "Group Photos",
	"description" => "Which members of this group are allowed to upload photos?",
	"tooltip" => "Short description about the about group photos goes here. Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore."
];

$activity_settings[] = [
	"key" => "document_status",
	"name" => "Group Documents",
	"description" => "Which members of this group are allowed to post into the group documents?",
	"tooltip" => "Short description about the about group documents goes here. Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore."
];

$activity_settings[] = [
	"key" => "video_status",
	"name" => "Group Videos",
	"description" => "Which members of this group are allowed to post into the group videos?",
	"tooltip" => "Short description about the about group videos goes here. Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore."
];

$radio_buttons = [
	"members"=>"All group members",
	"mods" => "Organizers and Moderators only",
	"admins" => "Organizers only" 
];

$school_id = get_user_meta( get_current_user_id(),"selected_institute", true);
$generator = new \Buddyboss\LearndashIntegration\Library\SyncGenerator( "", $school_id );
$bpGroupId = $generator->getBpGroupId();
?>

<div class="activity-feed-container">
   <form id="frm-activity-feeds">
      <?php 
      foreach($activity_settings as $setting){
      ?>
      <section class="section_<?=$setting["key"]?>">
         
         <fieldset id="field_1_46" class="gfield gfield--width-full gfield_contains_required field_sublabel_below field_description_above gfield_visibility_visible" data-js-reload="field_1_46">
            <legend class="gfield_label">
               <?=$setting["name"];?><span class="gfield_required"><span class="gfield_required gfield_required_text"></span></span>

               <div class="top-tooltip">
                  <span>
                     <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <g clip-path="url(#clip0_2537_20711)">
                        <path d="M7.57508 7.5C7.771 6.94306 8.15771 6.47342 8.66671 6.17428C9.17571 5.87513 9.77416 5.76578 10.3561 5.86559C10.938 5.96541 11.4658 6.26794 11.846 6.71961C12.2262 7.17128 12.4343 7.74294 12.4334 8.33333C12.4334 10 9.93342 10.8333 9.93342 10.8333M10.0001 14.1667H10.0084M18.3334 10C18.3334 14.6024 14.6025 18.3333 10.0001 18.3333C5.39771 18.3333 1.66675 14.6024 1.66675 10C1.66675 5.39763 5.39771 1.66667 10.0001 1.66667C14.6025 1.66667 18.3334 5.39763 18.3334 10Z" stroke="#5D53C0" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path>
                     </g>
                     <defs>
                        <clipPath id="clip0_2537_20711">
                        <rect width="20" height="20" fill="white"></rect>
                        </clipPath>
                     </defs>
                     </svg>
                     What is this?
                     <span class="tooltip"><?=$setting["tooltip"];?></span>
                  </span>
               </div>
               
            </legend>
            <div class="gfield_description" id="gfield_description_1_46">
            
            <?=$setting["description"]?>
            </div>
            <div class="ginput_container ginput_container_radio">
               <div class="gfield_radio" id="input_1_46">
                  <?php 
                  $selected_value = groups_get_groupmeta( $bpGroupId, $setting["key"], true );
                  if(empty($selected_value)) $selected_value = "members";

                  foreach($radio_buttons as $r_key=>$radio){
                  ?>
                  <div class="gchoice gchoice_<?=$setting["key"].$r_key?>">
                     <input class="gfield-choice-input" name="<?=$setting["key"]?>" 
                        <?=($selected_value==$r_key) ? "checked":""?> 
                        type="radio" 
                        value="<?=$r_key?>" 
                        id="<?=$setting["key"].$r_key?>"   >
                     <label for="<?=$setting["key"].$r_key?>" id="<?=$setting["key"].$r_key?>"><?=$radio?></label>
                  </div>
                  <?php 
                  }
                  ?>	
                  
               </div>
            </div>
         </fieldset>

         <div class="selection-info">
            <div  class="gfield gfield--width-full settings-selected-item gfield_html gfield_html_formatted field_sublabel_below field_description_below gfield_visibility_visible" data-js-reload="field_1_50" style="">
            All classroom members can lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </div>
            <div  class="gfield gfield--width-full settings-selected-item gfield_html gfield_html_formatted gfield_no_follows_desc field_sublabel_below field_description_below gfield_visibility_visible" data-js-reload="field_1_51" style="display: none;">
            All classroom members can lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </div>
            <div class="gfield gfield--width-full settings-selected-item gfield_html gfield_html_formatted gfield_no_follows_desc field_sublabel_below field_description_below gfield_visibility_visible" data-js-reload="field_1_52" style="display: none;">
            All classroom members can lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </div>
         </div>
      </section>
      <?php 
      }
      ?>

      <button type="button" class="button-save">Save</button>
   </form>
</div>