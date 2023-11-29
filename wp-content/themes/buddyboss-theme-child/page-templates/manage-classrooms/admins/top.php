<div class="top">
    <h1>Admins at <?=(!empty($school_details["post"]->post_title) ? $school_details["post"]->post_title:"")?></h1>
    <div class="search-box">
        <input type="text" class="search" placeholder="Search" data-type="admins" placeholder="Search admin"/>
    </div>
    <div class="buttons-container">
        <button type="button" class="button-admin-action" data-action="add_admin" data-teacherid="0">Add Admin</button>
    </div>
</div>