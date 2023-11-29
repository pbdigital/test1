<?php
exit;
if($_GET["delete_get_started"]){
    delete_user_meta(get_current_user_id(), "done_gets_started");
    wp_redirect("dashboard-dev");
}

/** Template Name: Dashboard */
$is_user_teacher = \Safar\SafarUser::is_user_teacher( );
if(!empty($is_user_teacher)){
    wp_redirect("/groups/?scope=personal");
}

$is_institute_admin = \Safar\SafarUser::is_user_institute_admin();
if(!empty($is_institute_admin)){
    wp_redirect("/manage-classroom");
}

if(\Safar\SafarFamily::is_user_institute_parent()){
    wp_redirect("/family-parent-dashboard");
}

$force_avatar_update = get_user_meta(get_current_user_id(),"force_avatar_update",true);
if(!empty($force_avatar_update)){
    delete_user_meta(get_current_user_id(),"force_avatar_update",true);
    wp_redirect("avatar-store");
}

wp_enqueue_style('pbd-sa-style');
wp_enqueue_style('pbd-sa-fullcalendar-css');
wp_enqueue_script('pbd-sa-fullcalendar-js');
wp_enqueue_script('pbd-sa-circleprogress-js');
wp_enqueue_script('pbd-sa-scripts', PBD_SA_URL . '/assets/js/scripts.js', array(), ENQUEUE_VERSION , true);
wp_enqueue_script("dashboard-js");

get_header();
$user = wp_get_current_user();
 

?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
<link rel="stylesheet" href="<?= get_stylesheet_directory_uri(); ?>/assets/css/dashboard.css?<?=uniqid()?>">
<main id="main" class="site-main">

    <section class="dashboard-header dashboard-wrapper">
        <div class="dashboard-user">
            <div class="dashboard-user__profile">
                <div class="skeleton-loader" style="border-radius:10px"></div>
                <div class="dashboard-user__welcome">
                    <h1><div class="skeleton-loader" style="min-width:200px; height:35px"></div></h1>
                    <div class="skeleton-loader" style="min-width:100px; height:28px"></div>
                    <div class="skeleton-loader" style="min-width:100px; height:28px"></div>
                    <div class="skeleton-loader" style="min-width:100px; height:28px"></div>
                </div>
            </div>
        </div>
        <aside class="dashboard-sidebar">
            <div class="practice-log practice-log-skeleton skeleton-loader" style="height:103px; background:#efefef !important"></div>
            <div class="practice-log" style="display:none">
                <img src="<?=get_stylesheet_directory_uri();?>/assets/img/dashboard/book.svg" alt="Practice Tracker">

                <h3>Have you practiced reading today?</h3>
                <button class="btn-check" data-add="1">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="21" cy="21" r="20" stroke="#fff" stroke-width="2"/>
                    <path d="M29.052 15.286 17.624 26.714l-5.195-5.195" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </aside>
    </section>

    <section class="dashboard-wrapper">
        <section class="dashboard">
            
       
            <section class="dashboard-pickup">
                <div class="dashboard-pickup__heading">
                    <h2><img src="<?=get_stylesheet_directory_uri();?>/assets/img/dashboard/wave.svg" alt="Wave"> Pickup where you left off...</h2>
                </div>
                <div class="dashboard-pickup__slider">
                    
                    <div style="width:100%; display:grid; grid-template-columns:1fr 1fr;grid-column-gap:20px">
                        <div class="dashboard-pickup__slider-item skeleton-loader" style="width: 100%; height:172px"></div>
                        <div class="dashboard-pickup__slider-item skeleton-loader" style="width: 100%; height:172px"></div>
                    </div>

                </div>
            </section>
            

            <section class="dashboard-lessons">
                <div class="dashboard-heading">
                    <h2><svg width="35" height="32" viewBox="0 0 35 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 3.218h7.398l7.686 24.615c-.045.024-.147.06-.303.11-.158.052-.345.11-.559.178-.215.067-.44.14-.678.221l-.66.22c-.203.069-.372.12-.508.162-.135.039-.208.058-.22.058H0V3.218Zm27.104 0h6.958v25.564H21.891l-2.42-1.22 7.633-24.344Z" fill="#3A96DD"/><path d="M19.469 25.125h2.42v3.655h-2.42a.721.721 0 0 1-.28.585 2.343 2.343 0 0 1-.676.373 4.286 4.286 0 0 1-.813.204c-.277.039-.5.058-.669.058-.18 0-.41-.017-.685-.05a3.567 3.567 0 0 1-.804-.195 2.22 2.22 0 0 1-.67-.382.748.748 0 0 1-.279-.594h-2.437v-3.655h2.437v-1.422c.022.226.13.412.321.56.193.144.416.264.67.355.253.088.513.151.777.186.266.032.49.05.67.05.18 0 .402-.017.669-.05.264-.035.524-.098.777-.186.254-.091.477-.21.67-.356a.78.78 0 0 0 .32-.559v1.422h.002Z" fill="#0063B1"/><path d="M17.031 13.9a89.733 89.733 0 0 0 1.803-1.852 30.345 30.345 0 0 1 1.852-1.803c.531-.474 1.07-.936 1.62-1.38.545-.447 1.091-.902 1.633-1.364L30.422 2l2.421 2.438v23.125H18.249a1.19 1.19 0 0 0-.836.32c-.232.216-.36.48-.382.796a1.157 1.157 0 0 0-.382-.795 1.187 1.187 0 0 0-.836-.32H1.22V4.437L3.64 2l6.484 5.502c.542.461 1.088.916 1.634 1.363.549.444 1.088.906 1.619 1.38a30.372 30.372 0 0 1 1.852 1.803 91.584 91.584 0 0 0 1.803 1.853" fill="#CCC"/><path d="M17.031 5.656c0-.507.096-.982.288-1.422a3.753 3.753 0 0 1 1.946-1.948c.44-.19.914-.285 1.421-.285h9.736v23.123c-1.477-.01-2.947-.02-4.402-.024a942.699 942.699 0 0 0-4.401-.009c-.553 0-1.095.056-1.625.169a4.362 4.362 0 0 0-1.43.568c-.422.264-.774.61-1.049 1.04-.277.429-.438.957-.483 1.59v-.541c0-.092-.03-.228-.094-.414a18.139 18.139 0 0 0-.523-1.355 34.525 34.525 0 0 1-.298-.728 16.949 16.949 0 0 1-.227-.618 1.37 1.37 0 0 1-.093-.408V5.656h1.235Z" fill="#F2F2F2"/><path d="M17.031 5.656v22.922c-.022-.644-.167-1.187-.431-1.633a3.34 3.34 0 0 0-1.025-1.084 4.215 4.215 0 0 0-1.439-.591 7.837 7.837 0 0 0-1.692-.178c-1.478 0-2.947.002-4.4.009-1.457.004-2.926.013-4.404.024V2h9.736c.507 0 .981.095 1.421.286a3.755 3.755 0 0 1 1.946 1.948c.193.44.289.914.289 1.422" fill="#E5E5E5"/></svg> What will you learn today?</h2>
                </div>
                <div class="dashboard-lessons__list">
                    <div class="dashboard-lessons__item skeleton-loader" style="background:#efefef !important; height:178px" ></div>
                    <div class="dashboard-lessons__item skeleton-loader" style="background:#efefef !important; height:178px" ></div>
                    <div class="dashboard-lessons__item skeleton-loader" style="background:#efefef !important; height:178px" ></div>
                </div>
            </section>
    
            <section class="dashboard-goals">
                <div class="dashboard-heading">
                    <h2><svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 1c1.381 0 2.711.179 3.989.537 1.277.357 2.472.862 3.585 1.513a14.955 14.955 0 0 1 5.376 5.376 15.205 15.205 0 0 1 1.513 3.585C30.821 13.29 31 14.618 31 16c0 1.382-.179 2.711-.537 3.989a15.165 15.165 0 0 1-1.513 3.585 14.953 14.953 0 0 1-5.375 5.376 15.205 15.205 0 0 1-3.586 1.513A14.711 14.711 0 0 1 16 31c-1.382 0-2.711-.179-3.989-.537a15.165 15.165 0 0 1-3.585-1.513 14.954 14.954 0 0 1-5.376-5.375 15.178 15.178 0 0 1-1.513-3.586A14.71 14.71 0 0 1 1 16c0-1.382.179-2.711.537-3.989A15.178 15.178 0 0 1 3.05 8.426 14.955 14.955 0 0 1 8.425 3.05a15.205 15.205 0 0 1 3.586-1.514A14.717 14.717 0 0 1 16 1Z" fill="#EF746F"/><path d="M16 5.28a10.68 10.68 0 0 1 7.582 3.139A10.664 10.664 0 0 1 26.721 16a10.664 10.664 0 0 1-3.139 7.582 10.761 10.761 0 0 1-2.17 1.677A10.646 10.646 0 0 1 16 26.722a10.67 10.67 0 0 1-7.582-3.139 10.743 10.743 0 0 1-1.678-2.17 10.634 10.634 0 0 1-1.461-5.412 10.68 10.68 0 0 1 3.139-7.582A10.656 10.656 0 0 1 16 5.28Zm0 18.22a7.272 7.272 0 0 0 2.915-.588 7.599 7.599 0 0 0 2.386-1.61 7.6 7.6 0 0 0 1.61-2.386c.393-.91.589-1.882.589-2.915a7.284 7.284 0 0 0-.59-2.915A7.599 7.599 0 0 0 21.3 10.7a7.6 7.6 0 0 0-2.385-1.61A7.273 7.273 0 0 0 16 8.5a7.284 7.284 0 0 0-2.915.589c-.91.393-1.705.93-2.386 1.61a7.598 7.598 0 0 0-1.61 2.386A7.273 7.273 0 0 0 8.5 16c0 1.033.196 2.008.59 2.922.392.915.926 1.71 1.602 2.386a7.537 7.537 0 0 0 2.385 1.602 7.325 7.325 0 0 0 2.923.59Zm0-10.72c.448 0 .865.084 1.252.253.388.169.726.4 1.014.693.289.293.517.634.686 1.022A3.1 3.1 0 0 1 19.206 16a3.1 3.1 0 0 1-.254 1.252 3.36 3.36 0 0 1-.686 1.022 3.195 3.195 0 0 1-1.014.693 3.1 3.1 0 0 1-1.252.254 3.1 3.1 0 0 1-1.252-.254 3.289 3.289 0 0 1-1.715-1.715A3.1 3.1 0 0 1 12.779 16a3.1 3.1 0 0 1 .254-1.252 3.289 3.289 0 0 1 1.715-1.715A3.1 3.1 0 0 1 16 12.779Z" fill="#fff"/><path d="M23.5 13.853h-5.352V8.5c0-1.033.196-2.005.588-2.915.393-.909.93-1.704 1.61-2.385a7.6 7.6 0 0 1 2.386-1.61A7.273 7.273 0 0 1 25.647 1h2.132v3.206H31v2.147a7.284 7.284 0 0 1-.589 2.915 7.593 7.593 0 0 1-1.61 2.385 7.6 7.6 0 0 1-2.386 1.61 7.272 7.272 0 0 1-2.915.59Z" fill="#76B9E3"/><path d="M24.887 5.592c.209-.209.462-.313.76-.313s.54.107.754.32a1.044 1.044 0 0 1-.008 1.514L16.76 16.76a1.036 1.036 0 0 1-.76.314c-.298 0-.551-.105-.76-.314a1.036 1.036 0 0 1-.314-.76c0-.298.105-.551.314-.76l9.647-9.648Z" fill="#5F94F7"/></svg> Goals</h2>
                    <span class="dashboard-goals__total"></span>
                </div>
                <div class="dashboard-goals__list">
                    <h3>Complete your goals and earn coins.</h3>
                    <div class="dashboard-goals__checklist">
                         
                    </div>
                </div>
            </section>
        </section>
        <aside class="dashboard-sidebar">
            
            <div class="practice-tracker">
                <div class="dashboard-sidebar__heading"><span><img src="<?=get_stylesheet_directory_uri();?>/assets/img/dashboard/book-small.svg" alt="Practice Tracker Icon">  Practice Tracker</span></div>

                <div class="practice-tracker__progress-skeleton-loader skeleton-loader" style="
                    height: 700px;
                    width: 100%;
                    border-radius: 20px;
                    margin-top: 25px;
                "></div>
                <div class="practice-tracker__progress" style="display:none"></div>
                <div class="practice-tracker__inner" style="display:none">
                    <img src="<?=get_stylesheet_directory_uri();?>/assets/img/dashboard/book.svg" alt="Practice Tracker">
                    <p>Unlock a Quranic Animal by completing 25 practice reading sessions . Learn how the practice tracker works</p>
                    <a href="#" class="btn btn-learnmore">Learn More</a>
                </div>
            
            
                
            </div>
    
            <div class="achievements">
                <div class="dashboard-sidebar__heading">
                    <span><img src="<?=get_stylesheet_directory_uri();?>/assets/img/dashboard/trophy.svg" alt="Practice Tracker Icon">  Achievements</span> 
                    <a href="<?=get_field("achievements_page_url", $page_id)?>" class="count-achievements"> </a>
                </div>
                <div class="achievements-list">
                    <div class="achievements-list__item skeleton-loader" style="height:90px"></div>
                    <div class="achievements-list__item skeleton-loader" style="height:90px"></div>
                    <div class="achievements-list__item skeleton-loader" style="height:90px"></div>
                    <div class="achievements-list__item skeleton-loader" style="height:90px"></div>
                    <div class="achievements-list__item skeleton-loader" style="height:90px"></div>
                    <div class="achievements-list__item skeleton-loader" style="height:90px"></div>
                    <div class="achievements-list__item skeleton-loader" style="height:90px"></div>
                </div>
                <div class="bottom-view-all"><a href="<?=get_field("achievements_page_url", $page_id)?>">View All</a></div>
            </div>
        </aside>
    </section>
     
    <?php 
    $groups = groups_get_user_groups( get_current_user_id() );

    if(!empty($groups["total"])){
  
        $group_ids = [];
        
        $activity = "";
        $activities = [];

        foreach($groups["groups"] as $gid){
            ob_start();
            echo do_shortcode("[activity-stream object='groups' primary_id='".$gid."' display_comments=0 load_more=0]");
            $activity = ob_get_contents();
            ob_end_clean();

            
            $dom = new DOMDocument;
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $activity);
            $li_all = $dom->getElementsByTagName('li');

            foreach($li_all as $element){
                //class="groups activity_update activity-item" id="activity-1925" data-bp-activity-id="1925" data-bp-timestamp="1660997559"
                $li = "";
                $children  = $element->childNodes;
                $li_class = $element->getAttribute("class");
                $li_id = $element->getAttribute("id");
                $activityid = $element->getAttribute("data-bp-activity-id");
                $timestamp = $element->getAttribute("data-bp-timestamp");

                foreach ($children as $child){ 
                    //$li .= $element->ownerDocument->saveHTML($child);
                     

                    
                    if ($child->nodeName === 'div' && strpos( $child->getAttribute("class"), "activity-content")  !== false){
                        #$li .= $element->ownerDocument->saveHTML($child);  
                        $li .= "<div class='activity-content'><div class='activity-inner'>";
                        $activity = bp_activity_get_specific(array(
                            'activity_ids' => $activityid
                        ));
                        
                        // Check if the activity was found
                        if (!empty($activity['activities'])) {
                            // Get the content of the activity
                            $li .= "<div class='hidden full-content'>".stripslashes(apply_filters('the_content',$activity['activities'][0]->content))."</div>";
                        }  
                        
                        // Define the regex pattern
                        $pattern = '/<div class="activity-(?:content|inner)\s*">|<\/div>/i';

                        // Strip the tags and trim whitespace
                        $content = $element->ownerDocument->saveHTML($child);
                        $content = trim(preg_replace($pattern, '', $content));

                        $li .= "<div class='shortend-content'>".$content."</div></div>";

                        $li .= "</div></div>";

                    }else{
                        $li .= $element->ownerDocument->saveHTML($child);
                    }
                    
                }
                
                if(in_array("activity-item", explode(" ",$li_class) )){
                    // ?ac=1859/#ac-form-1859 
                    $activity_url = bp_activity_get_permalink($activityid);
                    $li = str_replace("?ac=".$activityid."/#ac-form-".$activityid."", $activity_url, $li); // replace comment link

                    $is_align_center = strpos($li,'style="align-items:center"');
                    $is_45 = strpos($li,'width="45px"');
                    $isbadge = false;
                    if( $is_align_center && $is_45 ){
                        $isbadge = true;
                    }

                    if(!$isbadge){
                        $activities[$activityid] = "<div data-isbadge='".$isbadge."' class='".$li_class."  ' id='".$li_id."' data-bp-activity-id='".$activityid."' data-bp-timestamp='".$timestamp."'>".$li."</div>";
                        //$activities .= "<div class='recent__slider-item'>test123</div> \n";
                    }
                    
                }
            }
        }

        if(!empty($activities)){
        ?>
        
        <section class="recent-wrapper">
            <div class="recent-wrapper__inner">
                <div class="recent__heading">
                    <h2><img src="<?=get_stylesheet_directory_uri();?>/assets/img/dashboard/class.svg" alt="Recent Activities From Your Classroom"> Recent Activities From Your Classroom</h2>
                </div>

            
                <div class="">
                    
                    
                    <div id="buddypress">
                        <h3 class="activity-shortcode-title">Latest Activity</h3>
                        <div class="bpas-shortcode-activities activity  shortcode-activity-stream">

                            <div id="activity-stream" class="activity-list recent__slider item-list  bp-list">
                                <?php 
                                
                                    ksort($activities);
                                    krsort($activities);
                                    foreach($activities as $activityid=>$activity){
                                        echo $activity;
                                    }
                                
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                                

                    ?>
                </div>
            </div>
        </section>
        <?php 
        }
    }
    ?>
    
</main>
<?php 
add_action("wp_footer", function(){
    
    ?>
    <!-- howTo  -->
    <div id="howTo" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 1 1 13M1 1l12 12" stroke="#A5A6A5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <div class="modal-title">
                How Practice Tracker Works
            </div>
            <?=get_field("how_practice_tracker_works")?>

            <a href="<?=get_field("lets_get_started_button_url")?>" class="btn-getstarted" type="button"><?=get_field("lets_get_started_button_text")?></a>
        </div>

    </div>
    <!-- practiceLog  -->
    <div id="practiceLogs" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-title">
                <h2>Your Practice Logs</h2>
                <span class="close"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6 6 18M6 6l12 12" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            </div>
            <div class="practiceLogs-content">
                <div class="practiceLogs-content__list"></div>
                <div class="practiceLogs-content__calendar">
                     
                </div>
            </div>
            <div class="practiceLogs-action">
                <button class="btn-logpractice" data-date="" data-mins="0">Log Your Practice</button>
            </div>
        </div>

    </div>
    <!-- logPractice  -->
    <div id="logPractice" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6 6 18M6 6l12 12" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <div class="modal-title">
                Log Your Practice
            </div>
            <form action="#">
                <div class="update-log-practice-message"><?=get_field("update_log_practice_message")?></div>

                <div class="form-row">
                    <label for="dop">Date of practice</label>
                    <input type="text" name="datepicker" id="datepicker" class="practice_date">
                </div>
                
                <div class="form-row">
                    <label for="mins">How many minutes did you practice?</label>
                    <div class="mins-options">
                        <div class="mins-item"><input type="radio" name="radioMins" id="radioMins5" value="5"><label for="radioMins5">5</label></div>
                        <div class="mins-item"><input type="radio" name="radioMins" id="radioMins10" value="10"><label for="radioMins10">10</label></div>
                        <div class="mins-item"><input type="radio" name="radioMins" id="radioMins15" value="15"><label for="radioMins15">15</label></div>
                        <div class="mins-item"><input type="radio" name="radioMins" id="radioMins20" value="20"><label for="radioMins20">20</label></div>
                        <div class="mins-item"><input type="radio" name="radioMins" id="radioMins25" value="25"><label for="radioMins25">25</label></div>
                        <div class="mins-item"><input type="radio" name="radioMins" id="radioMins30" value="30"><label for="radioMins30">30</label></div>
                        <div class="mins-item"><input type="radio" name="radioMins" id="radioMins45" value="45"><label for="radioMins45">45</label></div>
                        <div class="mins-item"><input type="radio" name="radioMins" id="radioMins60" value="60"><label for="radioMins60">60</label></div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="save-log-message"></div>
                    <button class="btn-save" type="submit">save practice</button>
                </div>
            </form>
            <div class="form-quote"><?=get_field("sidebar_quote");?></div>
        </div>

    </div>
    <!-- logPracticeConfirm  -->
    <div id="logPracticeConfirm" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6 6 18M6 6l12 12" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <div class="modal-title">
                Confirmation
            </div>
            <h3>Are you sure you want to log your <span class="mins-of-practice"></span> minutes practice?</h3>
            <div class="modal-confirm">
                <button class="btn-save__confirm">Save Practice</button>
                <button class="btn-cancel">cancel</button>
            </div>
        </div>

    </div>
    <!-- greatWork  -->
    <div id="greatWork" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6 6 18M6 6l12 12" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <img src="<?=get_stylesheet_directory_uri();?>/assets/img/dashboard/book.svg" alt="Great work">
            <h2>Great Work!</h2>
            <p>Earn 25 Reads to unlock a quranic animal. You've earned a Read, <span class="save-log-count-left"></span> more Reads to go! Keep up the good work!</p>
        </div>

    </div>
    

    

    <script src="//cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <script>
        jQuery(document).ready(function () {
            var picker = new Pikaday({
                firstDay: 1,
                field: document.getElementById('datepicker'),
                format: 'Do MMMM YYYY',
                defaultDate: moment().toDate(), //14 Mar 2015
                setDefaultDate: true,
                disableDayFn: function(theDate) {
                    today = new Date();
                    if(today >= theDate) return false;
                    else return true;
                }
            });

            var contentToggle = 0; 

            $('.bb-toggle-panel').on('click', function() { 
                
                // $('.custom-page-template-full #page > .site-content').css('opacity',0)

                // setTimeout(function () {
                //         $('.custom-page-template-full #page > .site-content').css('opacity',1)
                // }, 200)
                if ($('body').hasClass('buddypanel-open')) {

                    $(".buddypanel").animate({
                        width: "280px"
                    }, 200);
                }else {
                    $(".buddypanel").animate({
                        width: "80px"
                    }, 200);
                }
            
            });

            let updateDate = "";
            let updateMins = 0;
            let isAddLog = true;

            showPracticeLog = () => {

                //console.log("date", updateDate, "mins", updateMins, "isAddLog", isAddLog)

                $('#logPractice').css("display", "flex")
                            .hide()
                            .fadeIn();

                if(updateDate != ""){
                    $(".practice_date").val( moment(updateDate).format("Do MMMM YYYY") )
                }else{
                    $(".practice_date").val( moment().format("Do MMMM YYYY") )
                }
                
                $(".mins-item input[type=radio]").removeAttr("checked");
                if(isAddLog == 1){
                    $(".update-log-practice-message").hide();
                    $("#logPractice .modal-title").html("LOG YOUR PRACTICE")
                    $("#logPractice .btn-save").html("save practice")
                
                }else{
                    $(".update-log-practice-message").show();
                    $("#logPractice .modal-title").html("Update Your Practice")
                    $("#logPractice .btn-save").html("update practice")

                    
                    $(".mins-item input[type=radio]").each( function(){                        
                        if( parseInt($(this).val()) == parseInt(updateMins) ){
                            //console.log( $(this).val() +"=="+updateMins, updateDate, "radio box")
                            $(this).attr("checked","checked").prop("checked", true);
                        }
                    })

                }

            }

          
            $(document).on("click", '.btn-logpractice', function () {
                ///$('#practiceLogs').hide();
                updateDate = $(this).attr("data-date")
                updateMins = $(this).attr("data-mins")
                isAddLog = $(this).attr("data-add")

                showPracticeLog();
            })

            $(document).on("click",'.btn-log,.btn-check', function (e) {
                modalState();
                updateDate = $(this).attr("data-date")
                updateMins = $(this).attr("data-mins")
                isAddLog = $(this).attr("data-add")

                showPracticeLog();
            });

            $('.btn-save').click(function (e) {
                e.preventDefault();

                let mins = $(".mins-options input[type=radio]:checked").val();
                console.log("mins ", mins)
                
                if (typeof mins !== 'undefined') {
                    
                    if(mins.length > 0 ){
                        $(".mins-of-practice").html(mins);
                        
                        //$("#practiceLogs").hide();

                        if(isAddLog == 1){
                            $('#logPractice').hide();
                            $('#logPracticeConfirm').css("display", "flex")
                                    .hide()
                                    .fadeIn();
                        }else{
                            $('.btn-save').fadeTo("fast",.3)
                            SafarDashboard.saveLog();
                        }    
                    }
                }
            });

            $(document).on("click", ".btn-save__confirm", e => {
                e.preventDefault();
                SafarDashboard.saveLog();
                $('#logPracticeConfirm').hide();
            });

            $('.btn-cancel').click(function () {
                $('#logPracticeConfirm').hide();
                $('#logPractice').css("display", "flex")
                            .hide()
                            .fadeIn();
            });

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == $('#howTo')[0] || 
                    event.target == $('#practiceLogs')[0] || 
                    event.target == $('#logPracticeConfirm')[0] || 
                    event.target == $('#greatWork')[0] || 
                    event.target == $('#logPractice')[0] ) {
                    modalState();

                    //console.log("event target", event.target)
                    $(event.target).fadeOut();
                }
            }

            $('.btn-learnmore').click(function () {
                modalState();
                $('#howTo').css("display", "flex")
                            .hide()
                            .fadeIn();
            });

            $(document).on("click",'a.btn-viewlogs', function () {
                modalState();
                
                $('#practiceLogs').css("display", "flex")
                            .hide()
                            .fadeIn();
                $('.view-full-calendar').trigger('click')

                let logContentSize = $(".practiceLogs-content__list").html().length;

                if(logContentSize == 0){
                    SafarDashboard.practiceLogs();
                }
            });

            
            $('.month-view').appendTo($('.practiceLogs-content__calendar'))

            
            $('.activity-list').slick({
                dots: false,
                infinite: false,
                speed: 300,
                slidesToShow: 3,
                slidesToScroll: 3,
                prevArrow: '<button class="slick-prev"><svg viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18.1 8H1.9m0 0 6.3-6.3M1.9 8l6.3 6.3" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
                nextArrow: '<button class="slick-next"><svg viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.9 8h16.2m0 0-6.3-6.3M18.1 8l-6.3 6.3" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
                responsive: [
                    {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                    }
                ]
            });

            $(document).on("click",".practiceLogs-content__item",e => {
                updateDate = $(e.currentTarget).attr("data-date")
                updateMins = $(e.currentTarget).attr("data-mins")
                isAddLog = 0
                showPracticeLog();
                
            })

            

            $(document).on("click",".dashboard-user__classroom.show-more", e => {
                $(".dashboard-user__classroom.show-more").toggleClass("collapsed")
                $(".dashboard-user__classrooms").toggleClass("collapsed")

                if( !$(".dashboard-user__classroom.show-more").hasClass("collapsed") ){
                    $(".dashboard-user__classroom.show-more").html("show more ... ")
                }else{
                    $(".dashboard-user__classroom.show-more").html("show less ... ")
                }
            });


            $(document).on("click",".btn-getstarted", e => {
                e.preventDefault();
                let _button = $(e.currentTarget);
                $.ajax({
                    url: `${safarObject.ajaxurl}?action=done_gets_started`,
                    beforeSend: () => {
                        _button.fadeTo("fast",.3)
                    },
                    success: () => {
                        _button.fadeTo("fast",1)
                        SafarDashboard.displayUser();
                        modalState();
                        $('#howTo').fadeOut();

                    }
                });
            })

            $(document).on("click",".btn-check", e => {
                $(".practice_date").val(moment().format("Do MMMM YYYY"))
            })

            $(document).on("click",".fc-day", e => {
                //console.log("click")
                if(!$(e.currentTarget).hasClass("fc-day-future")){
                    let fcDate = $(e.currentTarget).attr("data-date");
                    let allEvents = $.parseJSON($(".view-full-calendar").attr("data-events"))
                    
                    updateDate = fcDate;
                    updateMins = 0;
                    isAddLog = 1;

                    allEvents.map( event => {
                        if( event.converted == fcDate ){
                            isAddLog = 0;
                            updateMins = event.meta_value;
                            
                        }
                    })

                    console.log("allEvents", allEvents)
                    showPracticeLog();
                }

            });

            $(document).on("click",".btn-start.disabled", e => {
                e.preventDefault();
            })

            $(document).on("click",".user-weekdays .day.past, .user-weekdays .day.today", e => {
                modalState();
                updateDate = $(e.currentTarget).attr("data-date")
                isAddLog = $(e.currentTarget).attr("data-add");
                updateMins = $(e.currentTarget).attr("data-mins");
                //console.log("isAddlog", isAddLog)

                showPracticeLog();

            })


            /* 
            SafarDashboard.displayUser();
            SafarDashboard.practiceLogs();
            */

            /* functions to load first */
            SafarDashboard.displayUser();
            SafarDashboard.practiceTrackerCalendar();
            

            /* functions to load when they scroll down, load goals and achievments when its visible scrolling down*/

            window.addEventListener('scroll', function() {
                var element = document.querySelector('.dashboard-goals__list');
                var position = element.getBoundingClientRect();

                // checking for partial visibility
                if(position.top < window.innerHeight && position.bottom >= 0) {

                    SafarDashboard.goals();
                    SafarDashboard.achievements();
                }
            });
            
        });
    </script>
    <?php
}, 999);

get_footer();

?>