<?php 
do_action("classroom_top",$school_details );
$institute_id = $school_details["post"]->ID;
$show_upgrade_notification = get_post_meta($institute_id,"show_upgrade_notification", true);
if($show_upgrade_notification){
    $upgraded_seats_count = get_post_meta($institute_id, "upgraded_seats_count", true);
    ?>
    <div class="institute-upgrade-success">

        <svg width="29" height="32" viewBox="0 0 29 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M25.931 24.2759C27.4546 24.2759 28.6897 23.0408 28.6897 21.5172C28.6897 19.9937 27.4546 18.7586 25.931 18.7586C24.4075 18.7586 23.1724 19.9937 23.1724 21.5172C23.1724 23.0408 24.4075 24.2759 25.931 24.2759Z" fill="#F2A952"/>
        <path d="M28.1379 25.3793H23.7241C23.4194 25.3793 23.1724 25.6263 23.1724 25.931V31.4483C23.1724 31.753 23.4194 32 23.7241 32H28.1379C28.4426 32 28.6897 31.753 28.6897 31.4483V25.931C28.6897 25.6263 28.4426 25.3793 28.1379 25.3793Z" fill="#F2A952"/>
        <path d="M2.75862 24.2759C4.28217 24.2759 5.51724 23.0408 5.51724 21.5172C5.51724 19.9937 4.28217 18.7586 2.75862 18.7586C1.23508 18.7586 0 19.9937 0 21.5172C0 23.0408 1.23508 24.2759 2.75862 24.2759Z" fill="#F2A952"/>
        <path d="M4.96552 25.3793H0.551724C0.247015 25.3793 0 25.6263 0 25.931V31.4483C0 31.753 0.247015 32 0.551724 32H4.96552C5.27023 32 5.51724 31.753 5.51724 31.4483V25.931C5.51724 25.6263 5.27023 25.3793 4.96552 25.3793Z" fill="#F2A952"/>
        <path d="M14.3448 24.2759C15.8684 24.2759 17.1035 23.0408 17.1035 21.5172C17.1035 19.9937 15.8684 18.7586 14.3448 18.7586C12.8213 18.7586 11.5862 19.9937 11.5862 21.5172C11.5862 23.0408 12.8213 24.2759 14.3448 24.2759Z" fill="#F2A952"/>
        <path d="M16.5517 25.3793H12.1379C11.8332 25.3793 11.5862 25.6263 11.5862 25.931V31.4483C11.5862 31.753 11.8332 32 12.1379 32H16.5517C16.8564 32 17.1035 31.753 17.1035 31.4483V25.931C17.1035 25.6263 16.8564 25.3793 16.5517 25.3793Z" fill="#F2A952"/>
        <path d="M8.82759 12.1379H5.51724V16.5517H8.82759V12.1379Z" fill="#F2A952"/>
        <path d="M7.17241 8.82759C8.08654 8.82759 8.82759 8.08654 8.82759 7.17241C8.82759 6.25829 8.08654 5.51724 7.17241 5.51724C6.25829 5.51724 5.51724 6.25829 5.51724 7.17241C5.51724 8.08654 6.25829 8.82759 7.17241 8.82759Z" fill="#F2A952"/>
        <path d="M24.2759 13.2414H22.069V14.3448H24.2759V13.2414Z" fill="#F2A952"/>
        <path d="M20.9655 12.6897C20.9655 12.6172 20.9798 12.5454 21.0075 12.4785C21.0352 12.4115 21.0758 12.3507 21.1271 12.2995C21.1783 12.2482 21.2391 12.2076 21.3061 12.1799C21.373 12.1522 21.4448 12.1379 21.5172 12.1379H24.8276C24.9 12.1379 24.9718 12.1522 25.0387 12.1799C25.1057 12.2076 25.1665 12.2482 25.2178 12.2995C25.269 12.3507 25.3096 12.4115 25.3374 12.4785C25.3651 12.5454 25.3793 12.6172 25.3793 12.6897V14.3448H27.0345V0H1.65517V14.3448H4.41379V11.5862C4.41377 11.5137 4.42803 11.442 4.45575 11.375C4.48347 11.3081 4.52411 11.2473 4.57535 11.196C4.62658 11.1448 4.68741 11.1042 4.75436 11.0764C4.82131 11.0487 4.89306 11.0345 4.96552 11.0345H9.37931C9.46459 11.036 9.5483 11.0576 9.62359 11.0977L9.62608 11.0927L11.3478 11.9537L12.8211 9.12446L13.7996 9.63416L12.0757 12.9445C12.0088 13.0729 11.8942 13.1698 11.7566 13.2145C11.6189 13.2591 11.4692 13.2479 11.3397 13.1832L9.93104 12.479V14.3448H20.9655V12.6897ZM7.17241 9.93103C6.62681 9.93103 6.09346 9.76924 5.63981 9.46612C5.18615 9.163 4.83257 8.73216 4.62378 8.22809C4.41499 7.72402 4.36036 7.16935 4.4668 6.63423C4.57324 6.09911 4.83597 5.60757 5.22178 5.22177C5.60757 4.83597 6.09911 4.57324 6.63423 4.4668C7.16935 4.36036 7.72402 4.41499 8.22809 4.62378C8.73217 4.83257 9.163 5.18615 9.46612 5.63981C9.76925 6.09346 9.93104 6.62681 9.93104 7.17241C9.93018 7.90378 9.63926 8.60495 9.12211 9.1221C8.60495 9.63926 7.90378 9.93018 7.17241 9.93103ZM16.5517 1.10345H24.8276C24.9739 1.10345 25.1142 1.16158 25.2177 1.26504C25.3212 1.36851 25.3793 1.50885 25.3793 1.65517C25.3793 1.8015 25.3212 1.94183 25.2177 2.0453C25.1142 2.14877 24.9739 2.2069 24.8276 2.2069H16.5517C16.4054 2.2069 16.2651 2.14877 16.1616 2.0453C16.0581 1.94183 16 1.8015 16 1.65517C16 1.50885 16.0581 1.36851 16.1616 1.26504C16.2651 1.16158 16.4054 1.10345 16.5517 1.10345ZM16.5517 3.31034H24.8276C24.9739 3.31034 25.1142 3.36847 25.2177 3.47194C25.3212 3.57541 25.3793 3.71574 25.3793 3.86207C25.3793 4.0084 25.3212 4.14873 25.2177 4.2522C25.1142 4.35567 24.9739 4.41379 24.8276 4.41379H16.5517C16.4054 4.41379 16.2651 4.35567 16.1616 4.2522C16.0581 4.14873 16 4.0084 16 3.86207C16 3.71574 16.0581 3.57541 16.1616 3.47194C16.2651 3.36847 16.4054 3.31034 16.5517 3.31034ZM16.5517 5.51724H24.8276C24.9739 5.51724 25.1142 5.57537 25.2177 5.67884C25.3212 5.78231 25.3793 5.92264 25.3793 6.06897C25.3793 6.21529 25.3212 6.35563 25.2177 6.45909C25.1142 6.56256 24.9739 6.62069 24.8276 6.62069H16.5517C16.4054 6.62069 16.2651 6.56256 16.1616 6.45909C16.0581 6.35563 16 6.21529 16 6.06897C16 5.92264 16.0581 5.78231 16.1616 5.67884C16.2651 5.57537 16.4054 5.51724 16.5517 5.51724ZM16.5517 7.72414H24.8276C24.9739 7.72414 25.1142 7.78227 25.2177 7.88573C25.3212 7.9892 25.3793 8.12954 25.3793 8.27586C25.3793 8.42219 25.3212 8.56252 25.2177 8.66599C25.1142 8.76946 24.9739 8.82759 24.8276 8.82759H16.5517C16.4054 8.82759 16.2651 8.76946 16.1616 8.66599C16.0581 8.56252 16 8.42219 16 8.27586C16 8.12954 16.0581 7.9892 16.1616 7.88573C16.2651 7.78227 16.4054 7.72414 16.5517 7.72414ZM16 10.4828C16 10.4103 16.0142 10.3385 16.042 10.2716C16.0697 10.2047 16.1103 10.1438 16.1616 10.0926C16.2128 10.0414 16.2736 10.0007 16.3406 9.97299C16.4075 9.94527 16.4793 9.93102 16.5517 9.93103H24.8276C24.9739 9.93103 25.1142 9.98916 25.2177 10.0926C25.3212 10.1961 25.3793 10.3364 25.3793 10.4828C25.3793 10.6291 25.3212 10.7694 25.2177 10.8729C25.1142 10.9764 24.9739 11.0345 24.8276 11.0345H16.5517C16.4793 11.0345 16.4075 11.0202 16.3406 10.9925C16.2736 10.9648 16.2128 10.9242 16.1616 10.8729C16.1103 10.8217 16.0697 10.7609 16.042 10.6939C16.0142 10.627 16 10.5552 16 10.4828Z" fill="#F2A952"/>
        </svg>
        <div class="text">Congratulations! Your total seats have been updated to <?=$upgraded_seats_count?>.</div>
        <div class="">
            <button type="button" class="btn-close-upgrade-seats">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 1L1 17M1 1L17 17" stroke="#98C03D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>
<?php
}
do_action("classroom_statistics", ["parent_school"=>$school_details, "child_schools"=>$child_schools]);

$is_school_admin = false;
if(!empty($school_details["teachers"])){
    $school_admin_user_ids = [];
    foreach($school_details["teachers"] as $admin){
        $school_admin_user_ids[] = $admin->data->ID;
        if(get_current_user_id() == $admin->data->ID){
            $is_school_admin = true;
        }
    }
}

?>

<div class="classrooms-grid">
    <?php 
    if(!empty($child_schools)){
        foreach($child_schools as $school){
            //\Safar\Safar::debug($school->school_data["courses"]);
            do_action("classroom_card", $school, $is_school_admin);
        }
    }
    
    do_action("classroom_new");
    ?>
    

</div>

<?php