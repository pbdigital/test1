<?php 
/** Template Name: Achievements */

wp_enqueue_script('achievements-js');
wp_enqueue_style('achievements-css');

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

wp_localize_script('achievements-js', 'achievementsObject', $args);

get_header();
$user = wp_get_current_user();?>
<main id="main" class="site-main">
    
    <div class="nav-tabs">
        <?php if(\Safar\SafarUser::is_institute_student_user() || \Safar\SafarFamily::is_user_institute_parent() ){ ?>
            <a href="" class="<?=(!isset($_GET["tab"])) ? "active":""?> achievements" target=".achievements">Achievements</a>
            <a href="" class="class-points <?=(isset($_GET["tab"]) && $_GET["tab"]=="class-points") ? "active":""?>" target=".class-points">Class Points</a>
        <?php } ?>
    </div>
    
  

    <div class="achievements-container <?=(!isset($_GET["tab"])) ? "active":""?>  achievements">
        <div class="page-heading">
            <div class="select-student">
            <?php 
            if(\Safar\SafarFamily::is_user_institute_parent()){
                $institute = \Safar\SafarSchool::get_school_details([]);

         
                ?>
                <div class="text">Achievements of</div> 
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
                <?php
            }
            ?>
            </div>
        
            <div class="achievement-count"></div>
        </div>

        <div class="tab-container">
            <div class="tab-heading">
                <a href="" class="active" data-type="inprogress">In-Progress</a>
                <a href="" data-type="completed">Completed</a>
            </div>
            <div class="tab-content">
                <div class="items"></div>
            </div>
        </div>
    </div>

    <div class="achievements-container class-points <?=(isset($_GET["tab"]) && $_GET["tab"]=="class-points") ? "active":""?> ">

    </div>

</main>
<?php 


get_footer();

?>