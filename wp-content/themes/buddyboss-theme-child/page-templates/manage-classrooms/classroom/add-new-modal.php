<div id="manage-classroom-add-new" class="modal manage-classroom-modal" >

    <!-- Modal content -->
    <div class="modal-content">            
        <span class="close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 6L18 18" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <div class="main-content">
            <div class="create-new-class">
                <h2>Create New Class</h2>
                <div class="nav-tabs steps-nav">
                    <a href="#class-new-name" class="active" data-target="#class-new-name">1</a>
                    <a href="#class-new-manage-courses" data-target="#class-new-manage-courses">2</a>
                    <a href="#class-new-avatar" data-target="#class-new-avatar">3</a>
                    <a href="#class-new-cover-photo" data-target="#class-new-cover-photo">4</a>
                    <a href="#class-new-teacher" data-target="#class-new-teacher">5</a>
                    <a href="#class-new-students" data-target="#class-new-students">6</a>
                </div>

                <div class="tabs">
                    <div id="class-new-name" class="tab active">
                    </div>
                    <div id="class-new-manage-courses" class="tab">
                    </div>
                    <div id="class-new-avatar" class="tab"><?php do_action("class-new-avatar",$args);?></div>
                    <div id="class-new-cover-photo" class="tab"><?php do_action("class-new-cover-photo",$args);?></div>
                    <div id="class-new-teacher" class="tab"><?php do_action("class-new-teacher",$args);?></div>
                    <div id="class-new-students" class="tab"><?php do_action("class-new-students",$args);?></div>

                    <div id="class-student-list-template" class="tab"><?php do_action("class-student-list-template",$args);?></div>

                </div>
            </div>

            <div class="">
                
            </div>
        </div>
    </div>

</div>