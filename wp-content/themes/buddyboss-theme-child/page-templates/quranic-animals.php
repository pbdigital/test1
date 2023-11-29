<?php 
/** Template Name: Quranic Animals */

wp_enqueue_script('quranic-animals-js');
wp_enqueue_style('quranic-animals-css');

$children = [];
if(\Safar\SafarFamily::is_user_institute_parent()){
    $institute = \Safar\SafarSchool::get_school_details([]);
    foreach($institute->data["students"] as $student){
        $children[] = $student;
    }
}

if(!isset($_GET["userid"])){
    $institute_parent_selected_child = get_user_meta(get_current_user_id(),"institute_parent_selected_child",true);
    $found_child = false;
    foreach($children as $child){
        if($institute_parent_selected_child == $child->data->ID) $found_child = true;
    }

    if(!$found_child){
        wp_redirect("?userid=".$children[0]->data->ID);
    }else{
        wp_redirect("?userid=".$institute_parent_selected_child."&mem_".mt_rand());
    }
    exit();
}


$args = [
    "user_id" => get_current_user_id(),
    "child_id" => ( isset($_GET["userid"]) ) ? $_GET["userid"]:0,
    "is_user_institute_parent" => \Safar\SafarFamily::is_user_institute_parent(),
    "children" => $children
];

wp_localize_script('quranic-animals-js', 'achievementsObject', $args);

get_header();
$user = wp_get_current_user();
 
?>
<main id="main" class="site-main">

    <?php 
    if(\Safar\SafarFamily::is_user_institute_parent()){
        ?>
        <div class="top page-heading">
            <div class="left select-student"> 
                <div class="text">Quranic Animals of </div> 
                <div class="students-dropdown-container">
                    <div class="selected">
                        <div></div>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                    </div>
                    <div class="dropdown">
                        <div class="items"></div>
                        <div class="overlay"></div>
                    </div>
                    
                </div>
            </div>
            <div class="right">
                <b>Progress: </b> <span class="earned-quranic-animals"> </span>
            </div>
        </div>
        <?php
    }else{
    ?>

    <div class="top">
        <div class="left"> 
            <h1>Quranic Animals <span class="earned-quranic-animals">0/25</span> </h1>
            <p>Unlock a Quranic Animal by completing 25 practice reading sessions</p>
        </div>
        <div class="right">
            <a class="button-practice-now" href="<?=site_url("/?practice_now=1")?>">Practice Now</a>
        </div>
    </div>
    <?php 
    }
    ?>
    <div class="quranic-animals-container">
        <div class="items">
            <?php 
            $quranic_animals = gamipress_get_achievements(["post_type"=>"quranic-animal", "orderby"=>"menu_order, post_title", "order" => "asc"]);

            $num = 0;
            foreach($quranic_animals as $animal){
                $num++;
                $locked_image = get_field("locked_image", $animal->ID)["url"];
                ?>
                <div class="item">
                    <span class="num"><?=($num < 10 ) ? "0".$num:$num?></span>
                    <img src="<?=$locked_image?>" class="skeleton-loader quranic-animal-<?=$animal->ID?>"/>
                    
                </div>
                <?php
            }
            ?>  
        </div>
    </div>
</main>
<?php 
add_action("wp_footer", function(){
    
    ?>
    
    <script>
       
    </script>
    <?php
}, 999);

get_footer();

?>