<?php 
namespace Safar;
use Safar\SafarFamily;
use Safar\SafarUser;

class SafarAvatarStore extends Safar{

    static function set_user_achievement_gear(\WP_REST_Request $request){
        $user_id = parent::pb_auth_user($request);
        
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $achievementid = $request->get_param("achievementid");
        $rs = get_post($achievementid);
        $type = $rs[0]->post_type;

        $allowed = false;
        if($firstlogin) $allowed = true;

        $success = false;
        if($allowed){
            update_user_meta($user_id, $type.'_selected', $achievementid);
            $success = gamipress_award_achievement_to_user($achievementid, $user_id);
        }
        

        $return["achievementid"] = $achievementid;	
        $return["success"] = $success;
        $response = new \WP_REST_Response($return);
        return $response;
    }

    
    static function get_gears_by_type( \WP_REST_Request $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }
        $type = $request->get_param("type");
        $taxonomy = $request->get_param("taxonomy");
        $adminonboarding = $request->get_param("adminonboarding");
        $gender = get_user_meta($user_id, "gender", true);

        $rs = get_posts(["post_type"=>$type, "post_status"=>"publish", "numberposts" => -1, "orderby"=>"ID", "order"=>"ASC"]);

       
        $sub_categories = [];

        if(!empty($taxonomy)){
            $sub_categories = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'orderby' => "term_id",
                'order' => "ASC"
            ]);
        }

        if( !SafarUser::is_user_student() ){
            $adminonboarding = true;
        }

        if(isset($adminonboarding)){
            if($gender  == "female"){
                if( $request->get_param("taxonomy") == "avatar_category" ){
                    $taxonomy = ["avatar_category","clothing_category"];
                    $rs = get_posts(["post_type"=>["clothing","avatar"], "post_status"=>"publish", "numberposts" => -1, "orderby"=>"ID", "order"=>"ASC"]);

                    $sub2 = get_terms([
                        'taxonomy'   => "clothing_category",
                        'hide_empty' => false,
                        'orderby'    => "term_id",
                        'order'      => "ASC"
                    ]);
                
                    // Merge the results
                    $sub_categories = array_merge($sub_categories, get_terms([
                        'taxonomy'   => "clothing_category",
                        'hide_empty' => false,
                        'orderby'    => "term_id",
                        'order'      => "ASC"
                    ]));
                }
            }
        }
        
        $response = [];
        $avatar_items = [];

        $user_info  = \Safar\SafarUser::get_user_info([]);
        $user_gender = $user_info->data->gender;

        $sub_category_result_count = [];

        foreach($rs as $r){
            
            if(!is_array($taxonomy)){
                $terms = get_the_terms($r->ID, $taxonomy);
            }else{
                $terms = [];
                foreach($taxonomy as $t){
                    foreach( get_the_terms($r->ID,$t) as $te){
                        $terms[] = $te;
                    }
                }
            }

            foreach($terms as $eterm){
                $sub_category_result_count[$eterm->term_id][] = $r->ID; 
            }
            
            if ($type == 'clothing' || $type == 'avatar') {
                $gender_suitable = get_field('gender_suitable', $r->ID);
                if ($gender_suitable == 'unisex') {
                    $avatar_items[] = self::get_avatar_details($r->ID, $taxonomy);
                } else if ($gender_suitable == $user_gender) {
                    $avatar_items[] = self::get_avatar_details($r->ID, $taxonomy);
                } 

            } else {
                $avatar_items[] = self::get_avatar_details($r->ID, $taxonomy);
            }
        }

        // do not show sub categories if they don't have items
        $return_sub_cats = [];
        foreach($sub_categories as $cat){
            if(!empty($sub_category_result_count[$cat->term_id])){
                $return_sub_cats[] = $cat;
            }
        }

        $user_info  = \Safar\SafarUser::get_user_info([]);
        $user_gender = $user_info->data->gender;
        if ($type == 'clothing') {
            $new_return_sub_cats = $return_sub_cats;
            $return_sub_cats = [];

            foreach ($new_return_sub_cats as $ct) {
                $gender_suitable = get_field('gender_suitable',  $ct);

                if ($gender_suitable == 'unisex') {
                    $return_sub_cats[] = $ct;
                } else if ($gender_suitable == $user_gender) {
                    $return_sub_cats[] = $ct;
                }
            }

        } 
        
        if(isset($adminonboarding)){
            if( $request->get_param("taxonomy") == "avatar_category" ){
                if($gender  == "female"){
                    $new_return_sub_cats = $return_sub_cats;
                    $return_sub_cats = [];

                    foreach ($new_return_sub_cats as $ct) {
                        if(in_array($ct->slug,["skin-color","hijabs"])){
                            $return_sub_cats[] = $ct;
                        }
                    }
                }
            }
        }
        
        $response["items"] = $avatar_items;
        $response["sub_categories"] = $return_sub_cats;

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function get_avatar_details($id, $taxonomy){
        $r = get_post($id);
        $user_id = parent::$user_id;
        $is_owned = false;
        $owned = gamipress_get_earnings_count(["post_id"=>$r->ID, "user_id"=>$user_id]);
        if($owned){
            $is_owned = true;
        }

        $equiped = false;
        
        $achievementid = get_user_meta($user_id, $r->post_type.'_selected', true);

        if($achievementid == $r->ID) $equiped = true;

        $coins_required = get_post_meta($r->ID, "coins_required", true);

        $gender_taxonomy = get_the_terms($r->ID, "gender");
        if(!empty($gender_taxonomy)){
    
            if($gender_taxonomy[0]->slug != strtolower($user_gender)){
                $show = false;
            }
        }
        
        $terms = get_the_terms($r->ID, $taxonomy);
        foreach($terms as $eterm){
            $sub_category_result_count[$eterm->term_id][] = $r->ID; 
        }

        if ($r->post_type == 'clothing') {
            if (!empty($terms)) {
                $achievementid = get_user_meta($user_id, $r->post_type. '_' . $terms[0]->slug . '_selected', true);
                if($achievementid == $r->ID) $equiped = true;
            }
        }

        return [
            "ID" => $r->ID,
            "title" => $r->post_title,
            "image" => get_the_post_thumbnail_url($r->ID),
            "type" => $r->post_type,
            "coins_required" => get_post_meta($r->ID, "coins_required", true),
            "color_hex" => get_post_meta($r->ID, "color_hex", true),
            "secondary_color" => get_post_meta($r->ID, "secondary_color", true),
            "hair_style_front" => get_post_meta($r->ID, "hair_style_front", true),
            "hair_style_back" => get_post_meta($r->ID, "hair_style_back", true),
            "eyebrow_color" => get_post_meta($r->ID, "eyebrow_color", true),
            "eye_color" => get_post_meta($r->ID, "eye_color", true),
            "eyelash_color" => get_post_meta($r->ID, "eyelash_color", true),
            "svg" => get_post_meta($r->ID, "svg", true),
            "owned" => $is_owned,
            "equipped" => $equiped,
            "taxonomy_test" => $r->post_type. '_' . $taxonomy . '_selected',
            "rs_eq" => $rs_eq,
            "terms" => (!empty($taxonomy)) ? $terms:[],
            "gender" => $gender_taxonomy,
            "slug" => $r->post_name,
            "user_id" => $user_id,
            "position" => [ "top"=> get_post_meta($r->ID,"styling_and_positioning_top",true), 
                            "left" => get_post_meta($r->ID,"styling_and_positioning_left", true),
                            "image_width" => get_post_meta($r->ID,"styling_and_positioning_image_width", true),
                         ]
        ];
    }

    static function get_user_gears( \WP_REST_Request $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $user_info  = \Safar\SafarUser::get_user_info([]);
        $user_gender = $user_info->data->gender;
        $user_age_group = $user_info->data->age_group;

        $gear_post_types = [ "headwears", "glasses", "backpacks", "headphones", "clothing"];
        $response = [];
        foreach($gear_post_types as $type){

            $items = array();
            if ($type == 'clothing') {
                $categories = get_terms(array(
                    'taxonomy' => 'clothing_category',
                    'hide_empty' => true
                ));


                foreach($categories as $category) {
                    $achievementid = get_user_meta($user_id, $type. '_' . $category->slug .   '_selected', true);
                    if ($achievementid) {
                        array_push($items, array(
                            'category' => $category->slug,
                            'id' => $achievementid
                        ));
                    }
                }
                $achievementid = $items;
            } else {
                $achievementid = get_user_meta($user_id, $type.'_selected', true);
            }
            
            $response[$type] = false;
            if(!empty($achievementid)){
                if($type=="avatars"){
                    $response[$type] = wp_get_attachment_url(get_post_meta($achievementid, "full_body", true));
                }else{

                    if ($type == 'clothing') {
                        if (is_array($achievementid)) {
                            $assets = array();
                            foreach($achievementid as $ac_id) {
                                $image = get_the_post_thumbnail_url($ac_id['id']);
                                $positioning = get_field('styling_and_positioning', $ac_id['id']);

                                $assets[$ac_id['category']]['item_id'] = $ac_id['id'];
                                $assets[$ac_id['category']]['image'] = $image;
                                $assets[$ac_id['category']]['positioning']['top'] = $positioning['top'];
                                $assets[$ac_id['category']]['positioning']['left'] = $positioning['left'];
                                $assets[$ac_id['category']]['positioning']['image_width'] = $positioning['image_width'];

                            }

                            $response[$type] = $assets;
                        }
                    } else {
                        $response[$type] = get_the_post_thumbnail_url($achievementid);
                    }
                    
                }
                $response["achievement_name"][$type] = get_post_field( 'post_name', get_post($achievementid) );
            }
        }

        if(!empty($response["glasses"])){
            $avatar_id = get_user_meta($user_id, 'avatars_selected', true);
            $glass_id = get_user_meta($user_id, 'glasses_selected', true);
            while(have_rows("glasses", $avatar_id)){
                the_row();
                $glasses = get_sub_field("glasses");
                $image = get_sub_field("image");
                #print_r(["glass_id" => $glass_id,"glasses"=>$glasses, "image" => wp_get_attachment_url($image->ID) ]);
                if($glass_id == $glasses->ID){
                    $response["avatars"] = $image["url"];
                }
            }
        }

        $user_institute = self::get_user_institute($user_id);
        $facial_features = get_field('school_onboarding_facial_features', $user_institute);
        
        $is_parent = SafarFamily::is_user_parent( $request );
        if( $is_parent ){
            $ld_groups = learndash_get_administrators_group_ids($user_id, true );
            $facial_features = "";
            foreach($ld_groups as $ld_gid){
                $user_institute = $ld_gid;
                $facial_features = get_post_meta($ld_gid,"facial_features", true);
            }
        }
        if(empty($facial_features)) $facial_features = "";
        $response["user_institute"] = $user_institute;
        $response["user_institute_details"] = get_post($user_institute);
        $response["facial_features"] = $facial_features;
        $response["is_parent"] = $is_parent;
        $response["gender"] = $user_gender;
        $response["group"] = $user_age_group;

        $avatars = [];
        $meta_avatars = get_user_meta($user_id, "user_avatar", true);
        if(!empty($meta_avatars)){
            foreach($meta_avatars as $k=>$v){
                $avatars[] = ["type"=>$k, "itemid"=>$v, "details" => self::get_avatar_details($v, "")];
            }
        }
        $response["avatars"] = $avatars;

        $response = new \WP_REST_Response($response);
        return $response;

    }

    static function user_unequip_gear( \WP_REST_Request $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $achievementid = $request->get_param("id");
        $category = $request->get_param("category");
        $rs = get_post($achievementid);
        delete_user_meta($user_id, $rs->post_type .'_'. $category . '_selected', $achievementid);

        $response["success"] = true;
        $response = new \WP_REST_Response($response);
        return $response;
    }
    static function user_equip_gear( \WP_REST_Request $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $achievementid = $request->get_param("id");
        $category = $request->get_param("category");
        $rs = get_post($achievementid);

        // add category on meta
        update_user_meta($user_id, $rs->post_type .'_'. $category . '_selected', $achievementid);

        if ($category == 'tops') {
            delete_user_meta($user_id, $rs->post_type .'_jilbabs_selected');
            delete_user_meta($user_id, $rs->post_type .'_thoubs_selected');
        }

        if ($category == 'jilbabs' || $category == 'thoubs')  {
            delete_user_meta($user_id, $rs->post_type .'_tops_selected');
            delete_user_meta($user_id, $rs->post_type .'_skirts_selected');
        }

        if ($category == 'trousers')  {
            delete_user_meta($user_id, $rs->post_type .'_skirts_selected');
        }

        if ($category == 'skirts')  {
            delete_user_meta($user_id, $rs->post_type .'_trousers_selected');
            delete_user_meta($user_id, $rs->post_type .'_jilbabs_selected');
        }

        // if cateogry jilbabs delete user meta skirts and trousers
        if ($category == 'jilbabs')  {
            delete_user_meta($user_id, $rs->post_type .'_trousers_selected');
            delete_user_meta($user_id, $rs->post_type .'_skirts_selected');
        }

        if ($category == 'headwears')  {
            delete_user_meta($user_id, $rs->post_type .'_hijabs_selected');
        }

        if ($category == 'hijabs')  {
            delete_user_meta($user_id, $rs->post_type .'_headwears_selected');
        }

        $unequip_items = get_field('unequip_items', $achievementid);
        foreach($unequip_items as $unequip_item){
            if ($category == $unequip_item->slug)
                continue;
            
            delete_user_meta($user_id, $rs->post_type .'_'. $unequip_item->slug . '_selected');
        }

        $unequip_item_specific = get_field('unequip_item_specific', $achievementid);
        foreach($unequip_item_specific as $unequip_item_id){
            // get clothing_category taxonomy by achievement id
            $clothing_category = get_the_terms($unequip_item_id, 'clothing_category')[0];
            $id = get_user_meta($user_id, $rs->post_type .'_'. $clothing_category->slug . '_selected', true);
            if ($id == $unequip_item_id) {
                delete_user_meta($user_id, $rs->post_type .'_'. $clothing_category->slug . '_selected');
            }
        }

        // equip gear show achievement to buddyboss group feed
        $user_groups = groups_get_user_groups($user_id);
        
        $response["unequip_items"] = $unequip_items;
        $response["success"] = true;
        $response = new \WP_REST_Response($response);
        return $response;
    }

    // user purchase gear is also required on other redeem or purchase achievements
    // like for venture kits and bonus videos
    static function user_purchase_gear( \WP_REST_Request $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $id = $request->get_param("id");
        $post = get_post($id);

        $coins_required = get_post_meta($post->ID, "coins_required", true);

        //$gamification = pbd_get_gamification_stats($user_id);
        $user_info = \Safar\SafarUser::get_user_info([]);

        $user_coins= $user_info->data->points;

        $response = [];
        if($user_coins >= $coins_required){
            gamipress_award_achievement_to_user($id, $user_id);
            
            if ($coins_required):
                gamipress_deduct_points_to_user( $user_id, $coins_required, "points", array(
                    'log_type' => 'points_expend',
                    'reason' => gamipress_get_option( 'points_expended_log_pattern', __( '{user} expended {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ) )
                ) );
            endif;

            $response["reward_store_purchase"] = gamipress_trigger_event( array(
                'event' => 'pbd_reward_store_purchase',
                'user_id' => $user_id
            ) );

            $response["success"] = true;
            $response["file_url"] = get_field("file_url", $id);
            $response["link"] = get_permalink($id);
            $response["item_details"] = get_post($id);
            $response["categories"] = get_the_terms($id, 'clothing_category');

            $user_info = \Safar\SafarUser::get_user_info([]);
            $user_coins= $user_info->data->points;
            $response["coins_remaining"] = $user_coins;
        }else{
            $response["success"] = false;
            $response["message"] = "You don't have enough coins to buy this item.";
            $response["file_url"] = "";
            $response["link"] = "";
        }
        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function save_avatar( \WP_REST_Request $request ){
        $user_id = parent::pb_auth_user($request);
        if ($user_id == false) {
            return new \WP_REST_Response('Unauthorized', 401);
        }

        $group = $request->get_param("group");
        $skin_color = $request->get_param("skin_color");
        $hair_style = $request->get_param("hair_style");
        $hair_color = $request->get_param("hair_color");
        $gender = $request->get_param("gender");

        #print_r([$group, $skin_color, $hair_style, $hair_color, $gender]);

        if(!empty($request->get_param("avatar_html"))){
            $avatar_html = '
            <html>
            <link rel="stylesheet" href="/wp-content/themes/buddyboss-theme-child/assets/css/choose-avatar.css?ver=6363b4d614f9a"/>
            <body class="page-template-choose-avatar print-avatar">
                    <div id="page" style="
                    background: transparent;
                ">
                        <div class="choose-avatar-inner" style="
                    border: none;
                    padding: 0px;
                    background: transparent;
                    box-shadow: none;
                    border: 1px solid red;
                    width: 400px;
                    overflow: hidden;
                ">
                            <div class="color-select" style="
                    border: none;
                    padding: 0px;
                ">
                                <div class="avatars-hero female" id="my-node" style="
                    width: 430px;
                    height: 470px;
                    border: none;
                    padding: 0px;
                    margin: 0px;
                ">
                '.$request->get_param("avatar_html").'

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ';

            // write the avatar html so it can be processed by wkhtmltoimage
            $response["avatar_html"] = file_put_contents(plugin_dir_path(__FILE__).'avatars_html/'.$user_id.'.html',$avatar_html);
            $response["avatar_raw_url"] = site_url("wp-content/plugins/pbdigital-api/avatars_html/".$user_id.".html");

            $avatar_name = plugin_dir_path(__FILE__).'avatars_raw_img/'.$user_id.'.png';
            $command = "/usr/local/bin/wkhtmltoimage --transparent --quality 60 ".$response["avatar_raw_url"]." ".$avatar_name;

            $response["wkhtmltoimage"] = exec($command);

            // crop the image to used for avatar
            $response["crop_image_avatar"] = self::cropImage( "230", "190",$avatar_name, "png", plugin_dir_path(__FILE__).'avatars_cropped/'.$user_id.'.png' );
            $avatar_url = site_url("/wp-content/plugins/pbdigital-api/avatars_cropped/".$user_id.'.png');
            update_user_meta($user_id,"custom_avatar_url", $avatar_url);


            // crop for full body
            self::cropImage( "230", "410",$avatar_name, "png", plugin_dir_path(__FILE__).'avatars_full/'.$user_id.'.png' );
            $response["avatar_url"] = $avatar_url."?".uniqid();
            
            $response["avatar_full"] = site_url("/wp-content/plugins/pbdigital-api/avatars_full/".$user_id.'.png');
            update_user_meta($user_id,"custom_avatar_full", $response["avatar_full"]);
       
        }


        if(!empty($gender)) update_user_meta($user_id, "gender", $gender);
        update_user_meta($user_id,"avatar_selected", true);

        $updated_avatar_id = 215274;
        if (!gamipress_has_user_earned_achievement($updated_avatar_id, $user_id)) {
            gamipress_award_achievement_to_user($updated_avatar_id, $user_id); // 215274 https://my.journey2jannah.com/wp-admin/post.php?post=215274&action=edit
        }
        

        $response["success"] = update_user_meta($user_id, "user_avatar", ["skin_color"=>$skin_color, 
                                            "group"=>$group, 
                                            "gender" => $gender,
                                            "hair_style" => $hair_style,
                                            "hair_color" => $hair_color]);
        
        $response = new \WP_REST_Response($response);
        return $response;

    }

    static function cropImage($target_width, $target_height, $source, $stype, $dest) {
       
        $size = getimagesize($source);
        $w = $size[0];
        $h = $size[1];

        $source = imagecreatefrompng($source);
        
        $resized_image = imagecreatetruecolor($target_width, $target_height);

        imagealphablending($resized_image, FALSE);
        imagesavealpha($resized_image, TRUE);
        imagecopyresampled($resized_image, $source, 0, 0, 425, 40, $target_width, $target_height, $target_width, $target_height);
        //$resized_image = imagecrop($resized_image, ['x' => 425, 'y' => 50, 'width' => "230", 'height' => "400"]);
        $r = @imagepng($resized_image,$dest);

        return $r;
   }

    static function get_user_institute($user_id) {
        $group_ids = learndash_get_users_group_ids( $user_id ); // get an array of group IDs for the specified user
        // return $group_ids;
        $parent_group_id = 0;
        foreach ( $group_ids as $group_id ) {
            $group = get_post( $group_id ); // get the group post object
            if ( $group->post_parent ) {
                $parent_group_id = $group->post_parent;
                break;
            } 
        }

        return $parent_group_id;
    }

    static function resizeAvatarRawImages() {
        // Set the path to the folder containing avatar HTML 
        $folderPath = plugin_dir_path(__FILE__) . 'avatars_html/';
    
        // Get all HTML files in the folder
        $htmlFiles = glob($folderPath . '*.html');
    
        echo 'RESIZING...<br><br>';
    
        foreach ($htmlFiles as $key => $htmlFile) {
            // Extract filename without extension
            $filename = pathinfo($htmlFile)['filename'];
    
            // Build URLs and file paths
            $avatarRawUrl = site_url("wp-content/plugins/pbdigital-api/avatars_html/{$filename}.html");
            $avatarName = plugin_dir_path(__FILE__) . "avatars_raw_img/{$filename}.png";
    
            // Execute the wkhtmltoimage command
            $command = "/usr/local/bin/wkhtmltoimage --transparent --quality 60 {$avatarRawUrl} {$avatarName}";
            $response['wkhtmltoimage'] = exec($command);
    
            echo "{$avatarName}<br>";
        }
    
        echo '<br>DONE';
    }
}


if(isset($_GET['resize_avatar_raw_images']) && $_GET['resize_avatar_raw_images'] == 1) {
    
    SafarAvatarStore::resizeAvatarRawImages();
    exit;

}    


?>