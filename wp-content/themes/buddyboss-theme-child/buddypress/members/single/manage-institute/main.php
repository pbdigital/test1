<div class="bp-settings-container">
    <nav class="bp-navs bp-subnavs no-ajax user-subnav bb-subnav-plain" id="subnav" role="navigation" aria-label="Sub Menu">
        <ul class="subnav">
            
            <li id="institute-information-li" class="bp-personal-sub-tab current selected" data-bp-user-scope="institute-information">
                <a href="" id="institute-information" class="" data-target="#institite-information-tab"> Institute Information </a>
            </li> 
            <li id="activity-feed-li" class="bp-personal-sub-tab  " data-bp-user-scope="activity-feed">
                <a href="" id="activity-feed" class="" data-target="#activity-feed-tab"> Activity Feed </a>
            </li> 
            <li id="welcome-email-template-li" class="bp-personal-sub-tab  " data-bp-user-scope="welcome-email-template">
                <a href="" id="welcome-email-template" class="" data-target="#welcome-email-template-tab"> Welcome Email Template </a>
            </li> 

        </ul>
    </nav>

    <div class="bb-bp-settings-content">
        <div class="tab active" id="institite-information-tab"><?php do_action("manage_institute_information")?></div>
        <div class="tab" id="activity-feed-tab"><?php do_action("manage_institute_activity_feed")?></div>
        <div class="tab" id="welcome-email-template-tab"><?php do_action("manage_institute_welcome_email_template")?></div>
    </div>
</div>