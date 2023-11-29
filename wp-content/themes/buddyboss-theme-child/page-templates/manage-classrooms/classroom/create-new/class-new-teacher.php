<form class="teacher-exists frm-add-teacher" onsubmit=" return false;">
    <input type="hidden" name="tab" value="teacher" />
    <input type="hidden" name="classroom_id" class="new-group-id" value="" />

    <div class="field-group">
        <div class="field-heading">
            <label class="field-label">Teacher Name</label>
            <label class="field-label-checkbox">
                <span class="checkbox">
                    <input type="checkbox" class="class-new-teacher-exists" checked/> <span></span>
                </span>
                Teacher already exists? </label>
        </div>

        <div class="field-input field-teacher-exists search-teacher-container">
            <input type="text" name="search_teacher"  placeholder="Type/Select Teacher" autocomplete="off">
            <input type="hidden" name="search_teacher_id" class="search_teacher_id" value="0"/>
            <div class="search-teacher-result">
        
            </div>

        </div>

        <div class="field-input fullname teacher-add">
            <div><input type="text" name="first_name" placeholder="First Name"></div>
            <div><input type="text" name="last_name" placeholder="Last Name"></div>
        </div>
    </div>

    <div class="field-group teacher-add teacher-email">
        <div class="field-heading">
            <label class="field-label">Teacher Email</label>
        </div>
        <div class="field-input ">
            <input type="email" name="email"  placeholder="Email">
        </div>
    </div>

    <div class="button-container add">
        <button type="button" class="btn-save btn-add btn-add-teacher">ADD</button>
    </div>


    <div class="teachers-list-container">
        
    </div>

    <div class="button-container">
        <button type="button" class="btn-back btn-back-step">BACK</button>
        <button type="button" onclick="JourneyManageClassrooms.classrooms.createNewNavigate(this);" class="btn-save btn-next">NEXT</button>
    </div>

</form>

