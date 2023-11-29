<?php 
global $post;
$post_slug = $post->post_name;
?>
<div class="top">
    <h1>
    <?php 
    if($post_slug == "manage-family"){
        echo "Children";
    }else{
        echo "Students";
    }
    ?></h1>

    <?php 
    if($post_slug != "manage-family"){
    ?>
        <div class="search-box">
            <input type="text" class="search" placeholder="Search" data-type="students"/>
        </div>
        
        <div class="buttons-container">
            <button type="button" class="button-student-action" data-action="broadcast_email" data-broadcasttype="students" data-studentid="0">Broadcast Email</button>
            <button type="button" class="button-student-action" data-action="add_student" data-studentid="0">Add Students</button>
        </div>
    <?php 
    }else{
        ?>
            <div class="buttons-container manage-family">
                <button type="button" class="button-student-action" data-action="add_student" data-studentid="0">Add Child</button>
            </div>
        <?php 
    }
    ?>
</div>