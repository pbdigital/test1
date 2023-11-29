<?php 
wp_enqueue_style("classroom-awards-css");
wp_enqueue_script("classroom-awards-js");
?>

<div class="wrapper" id="attendance-wrapper">
    <div class="title ">Attendance</div>
    <form id="frm-students" class="list attendance-students">
        
    </form>
</div>

<div class="bottom-bar " style="display:none">
    <div class="container new-attendance">
        <div class="left">
            <div class="mark-present">
               
                <svg width="26" height="26" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="17.9999" cy="18" r="18" fill="#98C03D"/>
                    <path d="M25.5454 13L15.5454 23L10.9999 18.4545" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <button type="button" class="mark-all-present" >Mark All Present</button>
            </div>

            <div class="mark-absent">
                <svg width="26" height="26" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="17.9999" cy="18" r="18" fill="#EF746F"></circle>
                    <path d="M23.9999 12L11.9999 24M11.9999 12L23.9999 24" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>

                <button type="button" class="mark-all-absent" >Mark All Absent</button>
            </div>
        </div>

        <div class="right">
            <button class="cancel cancel-attendance" type="button">Cancel</button>
            <button class="save save-attendance" type="button" >Save Attendance</button>
        </div>
    </div>

    <div class="container update-attendance">
        <div class="left">
            <div class="num-present">
                <svg width="26" height="26" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="17.9999" cy="18" r="18" fill="#98C03D"/>
                    <path d="M25.5454 13L15.5454 23L10.9999 18.4545" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <button type="button"   >0 Present</button>
            </div>

            <div class="num-late">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="14" cy="14" r="14" fill="#F2A952"/>
                <path d="M11.0117 13.2402C11.0352 9.68945 11.0762 7.65039 11.1348 7.12305C11.1699 7.07617 11.5684 7.04102 12.3301 7.01758C12.6113 7.01172 12.8428 7.00586 13.0244 7C13.335 7 13.4961 7.01172 13.5078 7.03516C13.5195 7.12891 13.5254 7.50977 13.5254 8.17773C13.5254 9.00977 13.5166 10.293 13.499 12.0273C13.4697 15.1562 13.4434 17.0137 13.4199 17.5996C14.293 17.582 15.3271 17.5674 16.5225 17.5557C17.0029 17.5498 17.4072 17.5469 17.7354 17.5469C18.2334 17.5469 18.5703 17.5527 18.7461 17.5645C18.8164 17.752 18.8574 18.1328 18.8691 18.707C18.875 18.8594 18.875 18.9941 18.8691 19.1113C18.8691 19.4395 18.8545 19.6387 18.8252 19.709C18.8193 19.7324 18.1953 19.75 16.9531 19.7617C16.1797 19.7734 15.3975 19.7793 14.6064 19.7793C14.1318 19.7793 13.6484 19.7793 13.1562 19.7793C11.8672 19.7676 11.1582 19.7441 11.0293 19.709C11.0117 19.2754 11.0029 18.3848 11.0029 17.0371C10.9971 16.0293 11 14.7637 11.0117 13.2402Z" fill="white"/>
                </svg>
                <button type="button"   >0 Late</button>
            </div>


            <div class="num-absent">
                <svg width="28" height="28" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="17.9999" cy="18" r="18" fill="#EF746F"></circle>
                    <path d="M23.9999 12L11.9999 24M11.9999 12L23.9999 24" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <button type="button"  >0 Absent</button>
            </div>
        </div>

        <div class="right">
            <button class="save update-attendance" type="button" >Update  Attendance</button>
        </div>
    </div>
    

</div>


