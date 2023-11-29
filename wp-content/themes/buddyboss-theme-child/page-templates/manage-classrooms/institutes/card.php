<div class="classroom group-<?=$school->ID?>" data-gid="<?=$school->ID?>">
    <?php 
    
    //\Safar\Safar::debug($school->school_data["students"]);
    
    if(!empty($school->cover_photo)){
        ?>
        <img src="<?=$school->cover_photo?>" class="cover-photo"/>
        <?php
    }else{
        ?><div class="no-cover-photo"></div><?php
    }
    ?>
    <div class="avatar-container classroom-action" data-action="manage_classroom" data-tab="tab-classroom" data-schoolid="<?=$school->ID?>">
    <?php 
    if(!empty($school->avatar)){
        ?>
        <img src="<?=$school->avatar?>" class="avatar-photo"/>
        <?php
    }else{
        ?>
        <div class="no-avatar"></div>
        <?php
    }
    ?>
    </div>

    <button type="button" class="cog">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.0117 9.15474C11.3109 9.15474 10.6547 9.42661 10.1578 9.92349C9.66329 10.4204 9.38907 11.0766 9.38907 11.7774C9.38907 12.4782 9.66329 13.1344 10.1578 13.6313C10.6547 14.1258 11.3109 14.4001 12.0117 14.4001C12.7125 14.4001 13.3688 14.1258 13.8656 13.6313C14.3602 13.1344 14.6344 12.4782 14.6344 11.7774C14.6344 11.0766 14.3602 10.4204 13.8656 9.92349C13.6229 9.67893 13.3341 9.48504 13.0158 9.35307C12.6976 9.2211 12.3563 9.15368 12.0117 9.15474V9.15474ZM21.675 14.6743L20.1422 13.3641C20.2149 12.9188 20.2524 12.4641 20.2524 12.0118C20.2524 11.5594 20.2149 11.1024 20.1422 10.6594L21.675 9.34927C21.7908 9.25014 21.8737 9.11812 21.9126 8.97075C21.9515 8.82338 21.9447 8.66765 21.893 8.52427L21.8719 8.46333C21.45 7.2837 20.818 6.19026 20.0063 5.23599L19.9641 5.18677C19.8655 5.07087 19.7341 4.98756 19.5873 4.94782C19.4404 4.90807 19.285 4.91375 19.1414 4.96411L17.2383 5.64146C16.5352 5.06489 15.7524 4.61021 14.9039 4.2938L14.536 2.30396C14.5082 2.15405 14.4355 2.01615 14.3275 1.90856C14.2195 1.80097 14.0813 1.72879 13.9313 1.70161L13.868 1.68989C12.6492 1.46958 11.3649 1.46958 10.1461 1.68989L10.0828 1.70161C9.93282 1.72879 9.79463 1.80097 9.68662 1.90856C9.57861 2.01615 9.5059 2.15405 9.47814 2.30396L9.10782 4.30317C8.26742 4.6221 7.48453 5.07569 6.78986 5.64614L4.87267 4.96411C4.72915 4.91335 4.57358 4.90746 4.42664 4.94724C4.2797 4.98701 4.14834 5.07055 4.05001 5.18677L4.00782 5.23599C3.19757 6.19128 2.56565 7.28444 2.1422 8.46333L2.12111 8.52427C2.01564 8.81724 2.10236 9.14536 2.33907 9.34927L3.89064 10.6735C3.81798 11.1141 3.78282 11.5641 3.78282 12.0094C3.78282 12.4594 3.81798 12.9094 3.89064 13.3454L2.34376 14.6696C2.22797 14.7687 2.1451 14.9007 2.10616 15.0481C2.06723 15.1955 2.07407 15.3512 2.12579 15.4946L2.14689 15.5555C2.57111 16.7344 3.19689 17.8243 4.01251 18.7829L4.0547 18.8321C4.15326 18.948 4.28463 19.0313 4.43149 19.071C4.57834 19.1108 4.73379 19.1051 4.87736 19.0547L6.79454 18.3727C7.49298 18.9469 8.27111 19.4016 9.11251 19.7157L9.48282 21.7149C9.51059 21.8648 9.5833 22.0027 9.69131 22.1103C9.79932 22.2179 9.9375 22.2901 10.0875 22.3172L10.1508 22.329C11.3815 22.5505 12.6419 22.5505 13.8727 22.329L13.9359 22.3172C14.086 22.2901 14.2241 22.2179 14.3322 22.1103C14.4402 22.0027 14.5129 21.8648 14.5406 21.7149L14.9086 19.725C15.757 19.4063 16.5399 18.954 17.243 18.3774L19.1461 19.0547C19.2896 19.1055 19.4452 19.1114 19.5921 19.0716C19.7391 19.0318 19.8704 18.9483 19.9688 18.8321L20.011 18.7829C20.8266 17.8196 21.4524 16.7344 21.8766 15.5555L21.8977 15.4946C21.9985 15.204 21.9117 14.8782 21.675 14.6743ZM12.0117 15.8977C9.73595 15.8977 7.89142 14.0532 7.89142 11.7774C7.89142 9.50161 9.73595 7.65708 12.0117 7.65708C14.2875 7.65708 16.132 9.50161 16.132 11.7774C16.132 14.0532 14.2875 15.8977 12.0117 15.8977Z" fill="white"/>
        </svg>
    </button>
    
    <div class="classroom-options">
        <div class="overlay"></div>
        <ul>
            <li class="classroom-action" data-action="manage_classroom" data-tab="tab-classroom" data-schoolid="<?=$school->ID?>" >
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.0833 3.66671H3.66659C3.18036 3.66671 2.71404 3.85987 2.37022 4.20368C2.02641 4.5475 1.83325 5.01381 1.83325 5.50005V18.3334C1.83325 18.8196 2.02641 19.2859 2.37022 19.6297C2.71404 19.9736 3.18036 20.1667 3.66659 20.1667H16.4999C16.9861 20.1667 17.4525 19.9736 17.7963 19.6297C18.1401 19.2859 18.3333 18.8196 18.3333 18.3334V11.9167M16.9583 2.29171C17.3229 1.92704 17.8175 1.72217 18.3333 1.72217C18.849 1.72217 19.3436 1.92704 19.7083 2.29171C20.0729 2.65638 20.2778 3.15099 20.2778 3.66671C20.2778 4.18244 20.0729 4.67704 19.7083 5.04171L10.9999 13.75L7.33325 14.6667L8.24992 11L16.9583 2.29171Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <span>Manage Institute</span>
            </li>

            <li class="classroom-action" data-action="broadcast_email" data-schoolid="<?=$school->ID?>" >
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.1666 5.50008C20.1666 4.49175 19.3416 3.66675 18.3333 3.66675H3.66659C2.65825 3.66675 1.83325 4.49175 1.83325 5.50008M20.1666 5.50008V16.5001C20.1666 17.5084 19.3416 18.3334 18.3333 18.3334H3.66659C2.65825 18.3334 1.83325 17.5084 1.83325 16.5001V5.50008M20.1666 5.50008L10.9999 11.9167L1.83325 5.50008" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Email Institute</span>
            </li>

            <li class="classroom-action" data-action="manage_classroom" data-tab="tab-students" data-schoolid="<?=$school->ID?>" >
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.9999 6.41667C10.9999 5.44421 10.6136 4.51158 9.92598 3.82394C9.23834 3.13631 8.30571 2.75 7.33325 2.75H1.83325V16.5H8.24992C8.97926 16.5 9.67874 16.7897 10.1945 17.3055C10.7102 17.8212 10.9999 18.5207 10.9999 19.25M10.9999 6.41667V19.25M10.9999 6.41667C10.9999 5.44421 11.3862 4.51158 12.0739 3.82394C12.7615 3.13631 13.6941 2.75 14.6666 2.75H20.1666V16.5H13.7499C13.0206 16.5 12.3211 16.7897 11.8054 17.3055C11.2896 17.8212 10.9999 18.5207 10.9999 19.25" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Manage Classrooms</span>
            </li>

            <li class="classroom-action" data-action="manage_classroom" data-tab="tab-students" data-schoolid="<?=$school->ID?>" >
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.5834 19.25V17.4167C15.5834 16.4442 15.1971 15.5116 14.5095 14.8239C13.8218 14.1363 12.8892 13.75 11.9167 13.75H4.58342C3.61095 13.75 2.67832 14.1363 1.99069 14.8239C1.30306 15.5116 0.916748 16.4442 0.916748 17.4167V19.25M21.0834 19.25V17.4167C21.0828 16.6043 20.8124 15.815 20.3147 15.173C19.8169 14.5309 19.12 14.0723 18.3334 13.8692M14.6667 2.86917C15.4555 3.07111 16.1545 3.52981 16.6538 4.17295C17.153 4.81609 17.4239 5.60709 17.4239 6.42125C17.4239 7.23541 17.153 8.02641 16.6538 8.66955C16.1545 9.31269 15.4555 9.77139 14.6667 9.97333M11.9167 6.41667C11.9167 8.44171 10.2751 10.0833 8.25008 10.0833C6.22504 10.0833 4.58342 8.44171 4.58342 6.41667C4.58342 4.39162 6.22504 2.75 8.25008 2.75C10.2751 2.75 11.9167 4.39162 11.9167 6.41667Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Manage Students</span>
            </li>

            <li class="classroom-action" data-action="manage_classroom" data-tab="tab-teachers" data-schoolid="<?=$school->ID?>" >
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.1869 8.02542V12.2526M5.29658 10.0031V15.7057C5.29658 15.7057 7.50116 17.875 10.5514 17.875C13.6025 17.875 15.8071 15.7057 15.8071 15.7057V10.0031M0.916748 7.975L10.5518 4.125L20.1869 7.975L10.5518 11.825L0.916748 7.975Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Manage Teachers</span>
            </li>

            
            
        </ul>
    </div>

    <div class="details">

        <div class="class-name"><?=$school->post_title?></div>
        <div class="teachers-name">
            <?php 
            $teachers_name = [];
            foreach($school->school_data["teachers"] as $teacher) $teachers_name[] = $teacher->data->display_name;
            echo implode(", ",$teachers_name);
            ?>
        </div>

        <div class="meta">
            <div class="student-count">
                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2202_18585)">
                <path d="M11.8333 14V12.6667C11.8333 11.9594 11.5524 11.2811 11.0523 10.781C10.5522 10.281 9.8739 10 9.16666 10H3.83332C3.12608 10 2.4478 10.281 1.94771 10.781C1.44761 11.2811 1.16666 11.9594 1.16666 12.6667V14M15.8333 14V12.6667C15.8329 12.0758 15.6362 11.5018 15.2742 11.0349C14.9122 10.5679 14.4054 10.2344 13.8333 10.0867M11.1667 2.08667C11.7403 2.23353 12.2487 2.56713 12.6117 3.03487C12.9748 3.50261 13.1719 4.07789 13.1719 4.67C13.1719 5.26211 12.9748 5.83739 12.6117 6.30513C12.2487 6.77287 11.7403 7.10647 11.1667 7.25333M9.16666 4.66667C9.16666 6.13943 7.97275 7.33333 6.49999 7.33333C5.02723 7.33333 3.83332 6.13943 3.83332 4.66667C3.83332 3.19391 5.02723 2 6.49999 2C7.97275 2 9.16666 3.19391 9.16666 4.66667Z" stroke="#BFBFBF" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                <clipPath id="clip0_2202_18585">
                <rect width="16" height="16" fill="white" transform="translate(0.5)"/>
                </clipPath>
                </defs>
                </svg> <?=sizeof($school->school_data["students"])?> Students

            </div>
            <div class="subject-count">
                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.50001 4.66667C8.50001 3.95942 8.21906 3.28115 7.71896 2.78105C7.21886 2.28095 6.54059 2 5.83334 2H1.83334V12H6.50001C7.03044 12 7.53915 12.2107 7.91422 12.5858C8.2893 12.9609 8.50001 13.4696 8.50001 14M8.50001 4.66667V14M8.50001 4.66667C8.50001 3.95942 8.78096 3.28115 9.28106 2.78105C9.78116 2.28095 10.4594 2 11.1667 2H15.1667V12H10.5C9.96958 12 9.46087 12.2107 9.0858 12.5858C8.71072 12.9609 8.50001 13.4696 8.50001 14" stroke="#BFBFBF" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?=sizeof($school->school_data["categories"])?> subjects
            </div>
        </div>
    </div>
</div>