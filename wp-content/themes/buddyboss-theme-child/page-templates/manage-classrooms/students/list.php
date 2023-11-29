<?php 
global $post;
$post_slug = $post->post_name;
?>
<table class="list-container students-list datatable-students-list" style="width:100%">
    <thead class="head">
        <th class="name">NAME</th>
        <th class="email">USERNAME</th>
        <th class="classrooms"  >
            
            <?=($post_slug=="manage-family") ? "":"CLASSROOMS"?> 
        </th>
        
        
        <th class="last-login" style="">FAMILY</th>
        <th class="last-login" style="">FAMILY ID</th>
        <th class="last-login" style="width:200px">LAST LOGIN</th>
        <th class="action"  style="width:100px">ACTION</th>
    </thead>
    <tbody class="body"></tbody>
</table>