<?php 
$user_id = get_current_user_id();
?>
<div class="top">
    <h1>Institutes</h1>
    <div class="search-box">
        <input type="text" class="search search-institute" placeholder="Search institute">
    </div>
</div>

<?php 
$institutes = \Safar\SafarSchool::get_user_institutes($user_id);
?>
<div class="classrooms-grid">
    <?php
    if(!empty($institutes)){
        foreach($institutes as $institute){
            //\Safar\Safar::debug($school->school_data["courses"]);
            do_action("institute_card", $institute);
        }
    }
    //do_action("classroom_new");
    ?>
    
</div>
<div class="institutes-has-no-record <?=(!empty($institutes)) ? "":"active"?>"><div class="no-records-found">No Institutes Found</div></div>

<?php