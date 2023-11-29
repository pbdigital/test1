<?php 
wp_enqueue_style("classroom-awards-css");
wp_enqueue_script("classroom-awards-js");
?>

<div class="wrapper" id="rewards-wrapper">
    <div class="title">Reward</div>
    <div class="list attendance-students">
        
    </div>

    <div class="current-reward-container">
        
    </div>
</div>


<div id="rewards-feedback" class="modal"  >

    <!-- Modal content -->
    <div class="modal-content">            
        <span class="close">
            <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="25" cy="25" r="25" fill="#B0D178"/>
                <rect width="24" height="24" transform="translate(13 13)" fill="#B0D178"/>
                <path d="M31 19L19 31" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M19 19L31 31" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <div class="content">
                         

        </div>
    </div>

</div>


<div class="bottom-bar rewards-bottom " style="display:none">
    <div class="container select-multiple-all active">
        <div class="left">
            <div class="mark-present">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="14" cy="14" r="14" fill="#F2A952"/>
                    <path d="M12 13.3333L14 15.3333L20.6667 8.66667M20 14V18.6667C20 19.0203 19.8595 19.3594 19.6095 19.6095C19.3594 19.8595 19.0203 20 18.6667 20H9.33333C8.97971 20 8.64057 19.8595 8.39052 19.6095C8.14048 19.3594 8 19.0203 8 18.6667V9.33333C8 8.97971 8.14048 8.64057 8.39052 8.39052C8.64057 8.14048 8.97971 8 9.33333 8H16.6667" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="select-multiple" >Select Multiple</button>
            </div>

            <div class="mark-absent">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="14" cy="14" r="14" fill="#98C03D"/>
                <path d="M9 15.3846L11.3077 17.6923L19 10" fill="#98C03D"/>
                <path d="M9 15.3846L11.3077 17.6923L19 10" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="select-all" >Select All</button>
            </div>
        </div>
 
    </div>

    <div class="container show-selected">

        <div class="left">
            <div class="buttons-select-deselect">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="14" cy="14" r="14" fill="#98C03D"/>
                <path d="M9 15.3846L11.3077 17.6923L19 10" fill="#98C03D"/>
                <path d="M9 15.3846L11.3077 17.6923L19 10" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="select-all" >Select All</button>
            </div>
        </div>
        <div class="middle">
            0 selected
        </div>
        <div class="right">
            <button class="cancel cancel-feedback" type="button">Cancel</button>
            <button class="save send-feedback" type="button" >Send Feedback</button>
        </div>
    </div>

</div>

