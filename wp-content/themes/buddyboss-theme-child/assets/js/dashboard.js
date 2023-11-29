$ = jQuery;
let SafarDashboard = {}
SafarDashboard = {
    goalsLoaded: false,
    achievementsLoaded: false,

    displayUser: () => {
        
        let userProfile = ``;

        $(".dashboard-user__profile").html(`
            <div class="skeleton-loader" style="border-radius:10px"></div>
            <div class="dashboard-user__welcome">
                <h1><div class="skeleton-loader" style="min-width:200px; height:35px"></div></h1>
                <div class="skeleton-loader" style="min-width:100px; height:28px"></div>
                <div class="skeleton-loader" style="min-width:100px; height:28px"></div>
                <div class="skeleton-loader" style="min-width:100px; height:28px"></div>
            </div>
        `)

        Safar.getUserInfo({disabledachievements:true,userid:safarObject.user_id})
            .then( (user) => {
                ////console.log("response",  user )
                // user section 
                let classrooms = ``;
                if(user.groups.length > 0 ){

                    //console.log("group count", user.groups.length )


                    classrooms += `<div class="dashboard-user__classrooms">`
                    user.groups.map( group => {
                        classrooms += `<a class="dashboard-user__classroom" href="${group.url}">${group.name}</a>`
                    })
                    classrooms += `</div>`;
                }else{
                    classrooms = `<p>Start your learning journey today</p>`
                }


                userProfile = `
                    <img src="${user.avatar}" />

                    <div class="dashboard-user__welcome">
                        <h1>Assalāmu ʿalaykum ${user.first_name}!</h1>
                        ${classrooms}
                    </div>
                `

                $(".dashboard-user__profile").html(userProfile)

                console.log("height", $(".dashboard-user__classrooms").css("height"))
                if( parseInt($(".dashboard-user__classrooms").css("height")) > 68 ){
                    $(`<a class="dashboard-user__classroom show-more"  >show more ... </a>`).insertAfter(".dashboard-user__classrooms");
                    $(".dashboard-user__classrooms").addClass("collapsed")
                }else{

                }
                

                // show hide practice tracker after clicking lets get started button

                if(user.done_gets_started > 0 ){
                    $(".practice-tracker__inner").hide();
                    $(".practice-tracker__progress").show();
                    $(".practice-log").show();
                    $(".practice-tracker").removeAttr("style")
                }else{
                    $(".practice-tracker__progress").hide();
                    $(".practice-tracker__inner").show();
                    $(".practice-log").hide();
                    $(".practice-tracker").attr("style","margin-top:0px")
                }

                $(".practice-log-skeleton, .practice-tracker__progress-skeleton-loader").hide();

            })
            .catch( (e) => {
                //console.log("error on getuserINfo", e )
        });

        // Dashboard Pickp Where You Left Off...
        // skeleton loader
        //$(".dashboard-pickup__slider").html(`<div class="dashboard-pickup__slider-item skeleton-loader" style="width:100%; height:172px"></div><div class="dashboard-pickup__slider-item skeleton-loader" style="width:100%; height:172px"></div>`)


        Safar.dashboard.courses()
            .then( courses => {
                let dashboardPickup = ``
                let dashboardPickupArr = [];
                let counterPickup = 0;
                if(courses.lesson_in_progress_found){
                    courses.subjects.map( subject => {

                        if(subject.collections.length > 0){
                            subject.collections.map( collection => { 
                                
                                collection.courses.reverse().map( course => {

                                    if(course.status == "in_progress"){

                                        

                                        let courseImage = course.course_image;

                                        //console.log("in progress", typeof course.lesson_in_progress, course.lesson_in_progress[0].topic[0].topic_url, course.course_url)
                                        if(!courseImage) courseImage = "/wp-content/uploads/2022/10/bg-red.png";

                                        let continueCourseUrl = course.course_url
                                        let continueTitle = course.course_name;
                                        if(typeof course.lesson_in_progress == "object"){
                                            
                                            if(course.lesson_in_progress.length > 0 ){
                                                //console.log("course in progress", typeof course.lesson_in_progress,  course.lesson_in_progress.length, course.lesson_in_progress[0].topic[0] )
                                                continueCourseUrl = course.lesson_in_progress[0].topic[0].topic_url+"?dashboard=1"
                                                continueTitle = course.lesson_in_progress[0].topic[0].topic_name;
                                            }
                                        } 

                                        counterPickup++;
                                        dashboardPickupArr.push( `
                                            <div class="dashboard-pickup__slider-item">
                                                <div class="dashboard-pickup__slider-tag">
                                                    <span style="color: ${subject.background_color};">${subject.name}</span>
                                                </div>
                                                <a href="${continueCourseUrl}"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                                                    <img src="${courseImage}" alt="">
                                                    ${
                                                        ( !course.course_image ) ? `<div class="no-image-title">${continueTitle}</div>`:``
                                                    }
                                                </a>
                                            </div>
                                        `
                                        )
                                    }

                                })
                            })
                        }
                    })

                    // add custom dashboard pick up where you left off
                    if(safarObject.is_demo_user){
                        if( counterPickup <= 1 ){
                            dashboardPickupArr.push( `
                                <div class="dashboard-pickup__slider-item">
                                    <div class="dashboard-pickup__slider-tag">
                                        <span style="color: #ef746f;">Islamic Studies Demo</span>
                                    </div>
                                    <a href="/courses/islamic-studies-demo/lessons/textbook-1-ḥajj-fiqh/topic/ḥajj-4/?ldgid=226473"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                                        <img src="https://d39ju4w2l5x7vm.cloudfront.net/wp-content/uploads/2021/07/12105147/Path-4-1.png" alt="">
                                      
                                    </a>
                                </div>
                            `
                            )
                        }
                    }

                    dashboardPickupArr.reverse().map( tpl => {
                        dashboardPickup += tpl;
                    })

                    if(dashboardPickupArr.length > 0){

                        $(".dashboard-pickup").show();
                        $(".dashboard-pickup__slider").html(dashboardPickup);

                        $('.dashboard-pickup__slider').slick({
                            dots: false,
                            infinite: false,
                            speed: 300,
                            slidesToShow: 2,
                            slidesToScroll: 2,
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
                    }else{
                        $(".dashboard-pickup").hide();
                    }
                }else{
                    ////console.log("courses", courses )
                    $(".dashboard-pickup").hide();
                }


                // Dashboard Grouped Lessons
                ////console.log("Grouped Courses", courses.grouped_courses)

                let groupedLessonList = ``;
                
                for(i =0; i < 3; i++){
                    groupedLessonList += `<div class="dashboard-lessons__item skeleton-loader" style="background:#efefef !important; height:178px" ></div>`
                }
                $(".dashboard-lessons__list").html(groupedLessonList)
                groupedLessonList = ``;

                ////console.log("courses subjects", courses.subjects )

                i = 0;
                courses.subjects.map( subject => {
                    let disableLearnHeart = "Start";
                    let disableButton = "";
                    if( subject.name == "Learn By Heart"){
                        disableLearnHeart = "Coming Soon"
                        disableButton = "disabled"
                    }
                    
                    groupedLessonList += `
                        <div class="dashboard-lessons__item item-${i}" style="background-image:url(${subject.background_image})" data-progress="${subject.progress_percent}">
                            <div class="dashboard-lessons__circleprogress">
                                <strong>${subject.progress_percent}%</strong>
                              
                            </div>
                            <div class="dashboard-lessons__title">${subject.name}</div>
                            
                            <div class="dashboard-lessons__action">
                                <a href="/course-library/?subject=${subject.name}" class="btn-start ${disableButton}">${disableLearnHeart}</a>
                            </div>
                        </div>
                    `;
                    i++
                })
                $(".dashboard-lessons__list").html(groupedLessonList).addClass(`subjects-count-${courses.subjects.length}`)

                $('.dashboard-lessons__circleprogress').each( function(){
                    let progress = $(this).attr("data-progress")
                    $(this).circleProgress({
                        value: progress,
                        emptyFill: "#FFFFFF",
                        size: 28,
                        fill: { color: "#5D53C0" }
                    })
                });
                 

                if($(".dashboard-lessons__list").html().length <= 0){
                    $(".dashboard-lessons").hide();
                }

                //console.log("subects", courses)



                if(dashboardPickupArr.length <= 0 && $(".dashboard-lessons__list").html().length <= 0 ){
                    $(".dashboard-goals").css({"margin-top":"0px"});
                    SafarDashboard.goals();
                    SafarDashboard.achievements();
                }
            })
            .catch( e => {
                console.log("error on subjects ", e )
        });  
        
    },

    goals: () => {
        if(!SafarDashboard.goalsLoaded){
            // Goals
            let goalsTpl = ``
            for(i = 0; i < 5; i++){
                goalsTpl += `
                <div class="dashboard-goals__goal">
                    <a href="#"><div class="skeleton-loader" style="min-width:200px; height:28px"></div></a>
                    <div class="dashboard-goals__coins">
                        <div class="skeleton-loader" style="min-width:50px; height:28px"></div>
                    </div>
                </div>
                `
            }
            $(".dashboard-goals__checklist").html(goalsTpl);
            goalsTpl = "";

            Safar.dashboard.goals()
                .then( goals => {
                    SafarDashboard.goalsLoaded = true;
                    ////console.log("goals", goals)
                    
                    let earnedCount = 0;
                    let totalGoals = 0
                    goals.map( goal => {
                        goalsTpl += `
                        <div class="dashboard-goals__goal">
                            <a href="${goal.url}">
                                ${
                                ( (goal.earned) ? 
                                    `<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="15" cy="15" r="14" fill="#B0D178" stroke="#B0D178" stroke-width="2"/>
                                        <path d="m20.636 11-8 8L9 15.364" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg> `
                                    :
                                    `<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="15" cy="15" r="14" stroke="#E7E9EE" stroke-width="2"/></svg>`)   
                                }
                                ${goal.goal_name}
                            </a>
                            <div class="dashboard-goals__coins">
                                <img src="${safarObject.stylesheet_directory}/assets/img/dashboard/coin.png" alt="Coins Required"> <span>${goal.points}</span>
                            </div>
                        </div>
                        `
                        if(goal.earned) earnedCount++;
                        totalGoals++;
                    })
                    $(".dashboard-goals__total").html(`${earnedCount}/${totalGoals}`);
                    $(".dashboard-goals__checklist").html(goalsTpl);
                })
                .catch( e => {

            }); 
        }
    },

    achievements: () => {
        if(!SafarDashboard.achievementsLoaded){
            Safar.getUserInfo({userid:safarObject.user_id})
            .then( (user) => {
                SafarDashboard.achievementsLoaded = true;
                // achievements
                // earned achievements first
                ////console.log("badges", user.all_badges)
                let earnedAchievements = ``;
                let unEarnedAchievements = ``;
                let badgeTpl = ``

                i = 0;
                earned = 0;
                user.all_badges.map( badge => {
                    if(i < 9){
                        if(badge.earned){
                            earnedAchievements += `
                                <div class="achievements-list__item active">
                                    <img src="${badge.image}" alt="${badge.badge}">
                                    <div class="achievements-description">
                                        <svg width="16" height="19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m8 0 7.071 11.071L8 18.142l-7.071-7.07L8 0Z" fill="#37394A"/></svg>
                                        ${badge.badge_description}</div>
                                </div>
                            `
                            
                        }else{
                            unEarnedAchievements += `
                                <div class="achievements-list__item ">
                                    <img src="${badge.inactive_image}" alt="${badge.inactive_image}">
                                    <div class="achievements-description">
                                        <svg width="16" height="19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m8 0 7.071 11.071L8 18.142l-7.071-7.07L8 0Z" fill="#37394A"/></svg>
                                        ${badge.badge_description}</div>
                                </div>
                            `
                        }
                    }

                    if(badge.earned){
                        earned++;
                    }

                    i++;
                });

                
                 
                $(".achievements-list").html(earnedAchievements+unEarnedAchievements);
                $(".count-achievements").html(`${earned}/${i}`)

            })
            .catch( (e) => {
                    //console.log("error on getuserINfo", e )
            });
        }
    },

    practiceTrackerCalendar : () => {
        $.ajax({
            url: safarObject.ajaxurl,
            data: {
                action: "practice_tracker_calendar",
                page_id: safarObject.page_id,
                tz: moment.tz.guess()
            },
            beforeSend: () => {

            },
            success: (d) => {
                // sidebar weekly calendar
                $(".practice-tracker__progress").html(d)

                console.log("practice_now", safarObject.practice_now)
                if( safarObject.practice_now ){
                    setTimeout( () => {
                        $(".btn-log").trigger("click");
                    }, 1000)
                }

            }
        });

        $.ajax({
            url: safarObject.ajaxurl,
            data: {
                action: "practice_tracker_calendar_popup"
            },
            beforeSend: () => {

            },
            success: (d) => {
                // popup full calendar
                $(".practiceLogs-content__calendar").html(d)

                setTimeout( () => {
                    $('.view-full-calendar').trigger('click');
                    console.log("calendar popup")
                }, 100)

            }
        });

        
    },
    saveLog: () => {
        let logDate = $(".practice_date").val() ;
        let mins = $(".mins-options input[type=radio]:checked").val();
        if(mins.length   ){

            console.log("logDate", moment(logDate, "Do MMMM YYYY ").format("YYYY-MM-DD"),  moment().format("HH:mm:ss"), logDate )
            logDate = moment( moment(logDate, "Do MMMM YYYY").format("YYYY-MM-DD")+" "+moment().format("HH:mm:ss") ).utc().format();
            console.log("ConvertedToUTC",logDate, mins)

            Safar.practiceTracker.saveLog(logDate, mins)
                .then( e => {
                    
                    SafarDashboard.practiceTrackerCalendar();

                    if(e.log_type != "update"){
                        
                        if(!e.award_spin){
                            modalState();
                            $('#greatWork').css("display", "flex")
                                        .hide()
                                        .fadeIn();
                            confetti.start();

                            setTimeout(() => {
                                confetti.stop();
                            }, 5000);
                        }else{
                            Safar.practiceTracker.quranicSpin.type = e.award_type;
                            Safar.practiceTracker.quranicSpin.showModal();
                        }
                    }else{
                        modalState();
                        $('#logPractice').hide();
                        $('.btn-save').fadeTo("fast",1)
                    }

                    SafarDashboard.practiceLogs();

                    //console.log("save log e", e)
                    
                    $(".save-log-count-left").html(e.count_left);
                    
                })
                .catch( e => {
                    //console.log("error ", e )
                })
                 
        }else{
            $(".save-log-message").html(`<div class="error">Please select minutes</div>`);
        }
    },

    practiceLogs: () => {
        Safar.practiceTracker.logs()
            .then( logs => {
                //console.log("logs", logs )
                let practiceLogsTpl = ``;
                let hasToday = false;
                logs.map( log => {

                    // date and time from server is utc
                    // convert to browsers timezone
                    let convertedDateTime = moment.tz(log.date,"UTC").tz(moment.tz.guess()).format();

                    practiceLogsTpl += `
                        <div class="practiceLogs-content__item" data-date="${moment(convertedDateTime).format("L")}" data-mins="${log.minutes}">
                            <div class="practiceLogs-content__icon">
                                <img src="${safarObject.stylesheet_directory}/assets/img/dashboard/book-small.svg" alt="Practice Tracker Icon">
                            </div>
                            <div class="practiceLogs-content__details">
                                <p data-time="Wed 15th June 2022 at 3:00 pm"> ${moment(convertedDateTime).format("MMMM DD, YYYY") + " at " + moment(convertedDateTime).format("h:mm a")}</p>
                                <div class="practiceLogs-content__time">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 3.8V8l2.8 1.4M15 8A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" stroke="#B0D178" stroke-linecap="round" stroke-linejoin="round"/></svg> ${log.minutes}m
                                </div>
                            </div>
                        </div>
                    `

                    // check if has logged today
                    //console.log("hasTOday", moment(convertedDateTime).format("L"), moment().format("L"))

                    if( moment(convertedDateTime).format("L") == moment().format("L") ){
                        hasToday = true;
                        $(".practice-log .btn-check").attr("data-date", moment(log.date).format("L")).attr("data-mins", log.minutes).attr( "data-add","0" );

                        $(".btn-logpractice").attr("data-date", moment(log.date).format("L")).attr("data-mins", log.minutes).attr( "data-add","0" ).html("Update your Practice Today");
                        $(".btn-log").attr("data-date", moment(log.date).format("L")).attr("data-mins", log.minutes).attr( "data-add","0" );
                    }
                });

                $(".practice-log-count").html(logs.length)
                
                console.log("hasToday", hasToday)
                $(".practiceLogs-content__list").html(practiceLogsTpl)
                if(hasToday){
                    $(".practice-log h3").html(`Good job! You have practiced today!`);
                    $(".practice-log .btn-check").html(`
                        <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="21" cy="21" r="20" fill="#B0D178" stroke="#B0D178" stroke-width="2"/>
                            <path d="M29.052 15.2857L17.6234 26.7143L12.4286 21.5195" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    `);
                    $(".btn-log").html("Update your Practice");
                    
                }else{
                    $(".practice-log .btn-check").attr({"data-date": "", "data-mins": 0, "data-add":1 });
                    $(".btn-logpractice").attr({"data-date": "", "data-mins": 0, "data-add":1 }).html("Log your Practice Today");
                    $(".btn-log").attr({"data-date": "", "data-mins": 0, "data-add":1 }).html("Log your Practice");
                }

                Safar.getUserInfo({disabledachievements:true,userid:safarObject.user_id});
                
            })
            .catch( e => {
                //console.log("error ", e)
            })
    }
}

$(document).on("click",".activity-inner .activity-read-more a", e => {
    console.log("test test")
    e.preventDefault();
    $(e.currentTarget).closest(".activity-inner").find(".full-content").toggleClass("hidden");
    $(e.currentTarget).closest(".activity-inner").find(".shortend-content").toggleClass("hidden");
})
