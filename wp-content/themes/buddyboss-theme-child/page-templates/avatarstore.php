<?php
/** Template Name: Avatar Store */

wp_enqueue_script('avatar-store-js');
wp_enqueue_style('avatar-store-css');
$user_info = \Safar\SafarUser::get_user_info([]);

/* remove this section after testing*/
#$gender = get_user_meta();
$user_id = $user_info->data->ID;
$gender = $user_info->data->gender;
if(empty($gender)){
    wp_redirect(site_url("choose-avatar?redirect=".site_url("avatar-store")));
}

get_header();
?>

<a href="/" class="btn-back" ><img src="<?=get_stylesheet_directory_uri();?>/assets/img/groups/group-back.png" alt="Back Button"></a>
<div class="g-actions">
	<img src="<?=get_stylesheet_directory_uri();?>/assets/img/groups/groupcoin.png" alt="Points">
	<div class="gcoins">
        <svg viewBox="0 0 103 13" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M93.317.63H9.691C4.57.63.424 5.025.424 10.452v2.121c0-5.427 4.147-9.823 9.267-9.823h83.618c5.12 0 9.267 4.396 9.267 9.823v-2.121c0-5.427-4.147-9.824-9.267-9.824h.008Z" fill="#000" fill-opacity=".15" style="mix-blend-mode:multiply"/>
        </svg>
        <span class="current-user-points"><?=$user_info->data->points?></span>
    </div>
</div>

<main class="avatars">
    <div class="shop"><div class="shop-label">Shop</div></div>
    <div class="avatars-inner">
        <div class="avatars-main">
            <div class="avatars-wrapper">
                <div class="avatars-hero <?=$gender?>">

                    


                </div>
                <div class="avatars-gears">
                    <div class="avatars-gears__item">
                        <div class="avatars-gears__item-equip">
                        </div>
                    </div>
                    <div class="avatars-gears__item">
                        <div class="avatars-gears__item-equip">
                        </div>
                    </div>
                    <div class="avatars-gears__item">
                        <div class="avatars-gears__item-equip">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="avatars-store">

            <div class="avatars-categories">
                <div class="avatars-categories__item active" data-type="avatar" data-taxonomy="avatar_category">
                    AVATAR
                </div>
                <div class="avatars-categories__item" data-type="clothing" data-taxonomy="clothing_category" >
                    CLOTHING
                </div>
                <!-- <div class="avatars-categories__item" data-type="accessory" data-taxonomy="" >
                    ACCESSORIES
                </div> -->
                 
            </div>
            <!-- end avatar categories -->

            <div class="avatars-categories__list">
                <div class="avatars-categories__list-sub-categories">

                </div>
                <div class="avatars-categories__list-scroll">
                    <div class="avatars-categories__list-wrapper gears-list"> 

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<style>
#achievement-modal {
    display: none !important;
}


</style>
<?php get_footer(); ?>
<!-- The Modal -->
<div id="confirmationModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    
  </div>

</div>
<!-- The Modal -->
<div id="successModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    
  </div>

</div>
 

<div id="successModalEquip" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    
  </div>

</div>
 