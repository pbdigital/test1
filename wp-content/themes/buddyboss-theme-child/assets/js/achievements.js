$ = jQuery;
let JourneyAchievements = {}
JourneyAchievements = {
    selectedClassPointsFilter: "All",
    rewardsPoints: "",
    status: "inprogress",
    badges: {"all":[], "completed":[], "inprogress":[]},
    rewards: {},
    rewardsPaginate: {
        length: 10,
        page: 1
    },
    api: {
        getAchievements: async (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/user/achievements/badges`,
                    type: "get",
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    data: {
                        instituteparent: achievementsObject.is_user_institute_parent,
                        childid: achievementsObject.child_id
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {
                        
                    },
                    success: (d) => {
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /user/achievements/badges}`);
                    }
                });
            })
        },
        getRewards: async (data) => {
            return new Promise((resolve, reject) => {
                let filter = "";
                if( JourneyAchievements.selectedClassPointsFilter.toLowerCase() != "all"){
                    filter = JourneyAchievements.selectedClassPointsFilter.toLowerCase();
                    if( filter == "needs work") filter = "needs-work";
                }
                $.ajax({
                    url: `${safarObject.apiBaseurl}/rewards/history`,
                    type: "get",
                    data: {
                        ids: [safarObject.user_id],
                        type: "student",
                        paginate: true,
                        length: JourneyAchievements.rewardsPaginate.length,
                        page: JourneyAchievements.rewardsPaginate.page,
                        filter,
                        instituteparent: achievementsObject.is_user_institute_parent,
                        childid: achievementsObject.child_id
                    },
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {
                        
                    },
                    success: (d) => {
                        JourneyAchievements.rewards = d;
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /rewards/history`);
                    }
                });
            })
        }
    },

    singleBadgeTpl: ( data ) => {

        if(!data.show) return "";

        let badgeImage = ( data.earned ) ? data.image:data.inactive_image;
        let itemClass = "notstarted";
        if(data.progress.current > 0){
            badgeImage = data.image;
            itemClass = "inprogress";
        }
        if(data.earned) itemClass = "completed"
        return `
            <div class="item ${itemClass} ${(data.progress.total > 1) ? `show-total`:`hide-total`} ">
                <div class="image">
                    <img src="${badgeImage}" />
                    <div class="total" style="background-color:${data.badge_number_color}">${data.progress.total}</div>
                </div>
                <div class="details">
                    <div class="header">
                        <div class="title">${data.badge}</div>
                        <div class="progress">${data.progress.current}/${data.progress.total}</div>
                    </div>

                    ${ (data.custom_award) ? ``:
                    `
                    <div class="progress-bar">
                        <div class="progress-bar-inner" style="width:${data.progess_percent}%"></div>
                    </div>
                    `}

                    <div class="description">${data.badge_description}</div>

                    ${ (!data.custom_award) ? ``:
                    `
                    <div class="custom-award">
                        <div class="current-streak">
                            <span class="text">Current Streak:</span>
                            <span class="num">${data.custom_requirement.current_streak}</span>
                        </div>
                        <div class="target-streak">
                            <span class="text">Target Streak:</span>
                            <span class="num">${data.custom_requirement.target_streak}</span>
                        </div>
                    </div>
                    `}
                    
                </div>
            </div>
        `;
    },

    displayAchievements: () => {
        

        let badgesTpl = ``;
        let completedBadges = ``;
        let inProgressBadges = ``;
        let notStartedBadges = ``;

        JourneyAchievements.badges.completed = [];
        JourneyAchievements.badges.inprogress = [];
        
        JourneyAchievements.badges.all.map( badge => {
            badgesTpl = JourneyAchievements.singleBadgeTpl(badge);

            if(badge.earned){
                JourneyAchievements.badges.completed.push(badge);
            }else{
                JourneyAchievements.badges.inprogress.push(badge);
            }
            
            if(JourneyAchievements.status=="completed"){
                if(badge.earned) completedBadges += badgesTpl;
            }else{
                if(!badge.earned){
                    if(badge.progress.current > 0 ){
                        inProgressBadges += badgesTpl;
                    }else{
                        notStartedBadges += badgesTpl;
                    }
                }
            }
            
        });
        
        $(".achievements-container .tab-content .items").html(completedBadges+inProgressBadges+notStartedBadges);
        
        console.log("JourneyAchievemnts", JourneyAchievements.badges, JourneyAchievements.badges.completed )

        $(".achievement-count").html( `${JourneyAchievements.badges.completed.length}/${JourneyAchievements.badges.all.length}` );
            
    },
    pbdCheckDate: (dateStr) => {
        const currentDate = moment().startOf('day');
        const inputDate = moment(dateStr, 'YYYY-MM-DD');
      
        if (inputDate.isSame(currentDate, 'day')) {
          return 'Today';
        } else if (inputDate.isSame(currentDate.clone().subtract(1, 'day'), 'day')) {
          return 'Yesterday';
        } else {
          return inputDate.format('ddd Do MMM');
        }
    },

    rewardsTpl : (reward) => {
        return ` <div class="item ${reward.type}"> 
            <div class="image"><img src="${reward.achievement_details.image}"/></div>
            <div class="details">
                <div class="title">${reward.achievement_details.title}</div>
                <div class="comment">${reward.comment}</div>
            </div>
            <div class="teacher-classroom">
                <div class="avatar"><img src="${reward.teacher_details.avatar}"/></div>
                <div class="name">
                    <div class="teacher">${reward.teacher_details.firstName} ${reward.teacher_details.lastName}</div>
                    <div class="classroom">${reward.teacher_details.classroom}</div>
                </div>
            </div>
            <div class="points ">
                ${(reward.type=="positive") ? `+${reward.points}`:`${reward.points}`}
            </div>
        </div> `
    },

    rewardsPaginateTpl : (currentPage, totalPages, rewardsTotal) => {

                
        let pagination = "";

        currentPage = JourneyAchievements.rewardsPaginate.page;

        // Generate "Previous" button
        pagination += `<button ${ (currentPage > 1) ? ``:`disabled` } class="prev" onclick="JourneyAchievements.goToPage(${currentPage - 1})">Previous</button>`;
        

        // Generate page numbers
        pagination += "<div class='pages'>";
        for (let page = 1; page <= totalPages; page++) {
            pagination += `<button onclick="JourneyAchievements.goToPage(${page})" ${page === currentPage ? 'class="active"' : ''}>${page}</button>`;
        }
        pagination += `</div>`;

        // Generate "Next" button
        pagination += `<button ${ (currentPage < totalPages) ? ``:`disabled`} class="next" onclick="JourneyAchievements.goToPage(${currentPage + 1})">Next</button>`;

        showFrom = (JourneyAchievements.rewardsPaginate.page-1) * JourneyAchievements.rewardsPaginate.length + 1;
        showTo = JourneyAchievements.rewards.rewards_ungrouped.length + ( ( JourneyAchievements.rewardsPaginate.page-1) * JourneyAchievements.rewardsPaginate.length );

        if(rewardsTotal==0) showFrom = 0;

        let paginationDisplay = `<div class="showing" data-currentpage="${currentPage}">Showing ${showFrom} to ${showTo} of ${rewardsTotal}</div>`;
        paginationDisplay += `<div class="entries">Show <select><option>10</option><option>20</option></select> Entries</div>`

        return `<div class="pagination-container"><div class="left">`+ paginationDisplay + `</div><div class="right">` + pagination + `</div></div>`;   
    },

    goToPage: (page) => {
        JourneyAchievements.rewardsPaginate.page = page;
        JourneyAchievements.loadRewards();
    },

    loadRewards: () => {
        /* Show rewards*/
        // achievements-container class-points
        let rewardsListTpl = "";
        $(".achievements-container.class-points.active").fadeTo("fast",.3)
        
        JourneyAchievements.api.getRewards()
            .then( rewards => {
                if(JourneyAchievements.rewardsPoints=="") JourneyAchievements.rewardsPoints = rewards.totalpoints;

                let classPointsTpl = ``
                if(achievementsObject.is_user_institute_parent){
                    classPointsTpl = `
                    <div class="page-heading">
                        <div class="select-student">
                            <span>Class Points of</span>
                            <div class="students-dropdown-container">
                                <div class="selected">
                                    <div></div>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

                                </div>
                                <div class="dropdown">
                                    <div class="items"></div>
                                    <div class="overlay"></div>
                                </div>
                            </div> 
                        </div>
                    </div>
                    `
                }else{
                    classPointsTpl = `<span>Class Points </span>`;
                }

                rewardsListTpl += `
                <div class="top">
                    <div class="filters ${(achievementsObject.is_user_institute_parent) ? `is-institute-parent`:``}">
                        ${classPointsTpl}
                        <div class="filter-container">
                            <div class="selected">
                                <span>${JourneyAchievements.selectedClassPointsFilter}</span>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <ul class="list">
                                <li>All</li>
                                <li>Positive</li>
                                <li>Needs Work</li>
                            </ul>
                        </div>
                    </div>
                    <div class="points">
                        <svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.17218 31.8058L11.7797 20.6897L5.79152 18.1594L1.18506 29.2754L5.05842 28.4185L7.17218 31.8058Z" fill="#EF746F"/>
                        <path d="M15.2276 32L11.1079 20.6738L17.1998 18.4336L21.3182 29.7589L17.4862 28.7167L15.2276 32Z" fill="#EF746F"/>
                        <path d="M21.7092 13.3348L23.2764 12.035L21.7734 10.654L22.9519 8.98841L21.1472 8.0464L21.8571 6.12861L19.872 5.68946L20.0659 3.65126L18.0364 3.74424L17.7005 1.72402L15.7646 2.34315L14.9216 0.478107L13.2119 1.58098L11.9184 -3.54609e-09L10.5521 1.5111L8.89737 0.32004L7.96535 2.13835L6.06221 1.41803L5.62931 3.41813L3.6068 3.21879L3.70295 5.26459L1.69943 5.60035L2.31728 7.55176L0.469716 8.39878L1.56707 10.1231L3.39901e-09 11.424L1.50298 12.805L0.324499 14.4706L2.12912 15.4126L1.41918 17.3293L3.40435 17.7695L3.21047 19.8077L5.23997 19.7147L5.57591 21.7349L7.51179 21.1158L8.35478 22.9809L10.0644 21.878L11.357 23.4591L12.7243 21.9479L14.379 23.1389L15.311 21.3206L17.2142 22.0409L17.6471 20.0408L19.6694 20.2391L19.5734 18.1944L21.5769 17.8586L20.9591 15.9072L22.8067 15.0602L21.7092 13.3348Z" fill="#F0AA00"/>
                        <path d="M18.7526 12.2967C19.0566 8.33637 16.1173 4.87132 12.1876 4.55729C8.25792 4.24325 4.82584 7.19916 4.52185 11.1595C4.21786 15.1198 7.15709 18.5849 11.0868 18.8989C15.0165 19.2129 18.4486 16.257 18.7526 12.2967Z" fill="white"/>
                        <path d="M11.8746 6.82166L13.2585 9.67222C13.281 9.71934 13.3259 9.75211 13.3762 9.75985L16.4813 10.2279C16.6098 10.2474 16.6606 10.4077 16.5674 10.4984L14.3162 12.705C14.2796 12.7412 14.2628 12.7941 14.271 12.845L14.7958 15.9746C14.8176 16.1045 14.6832 16.2022 14.5676 16.1408L11.7924 14.6539C11.7464 14.6297 11.6916 14.6289 11.6462 14.6534L8.86565 16.119C8.7499 16.1795 8.61601 16.08 8.6384 15.9512L9.17495 12.8258C9.18344 12.7739 9.16668 12.7218 9.12966 12.6849L6.88635 10.4611C6.79335 10.3694 6.84461 10.2096 6.97378 10.1909L10.0797 9.7462C10.1304 9.73907 10.1748 9.70618 10.1976 9.65998L11.5916 6.81959C11.649 6.70246 11.8151 6.70267 11.8724 6.82087L11.8746 6.82166Z" fill="#F0AA00"/>
                        </svg>
                        <b>Total Class Points:</b> <span>${JourneyAchievements.rewardsPoints}</span>
                    </div>
                </div>

                <div class="rewards-container">
                `
                // Get today's date without time
                const today = moment().startOf('day');
                // Get yesterday's date without time
                const yesterday = moment().subtract(1, 'day').startOf('day');

                // Group the objects based on the date
                const localGroupedRewards = rewards.rewards_ungrouped.reduce((result, object) => {
                    const objectDate = moment(object.datetime).local();
                    const formattedDate = objectDate.format('YYYY-MM-DD'); // Get the formatted date (year-month-day)
                   
                    if (objectDate.isSame(today, 'day')) {
                        result.today.push(object);
                    } else if (objectDate.isSame(yesterday, 'day')) {
                        result.yesterday.push(object);
                    } else {
                        // Check if the date is already present in prior grouping
                        const priorGroup = result.prior.find(group => group.date === formattedDate);
                        if (priorGroup) {
                        priorGroup.objects.push(object);
                        } else {
                        result.prior.push({
                            date: formattedDate,
                            objects: [object]
                        });
                        }
                    }
                    
                    return result;
                }, { today: [], yesterday: [], prior: [] });

                    if(localGroupedRewards.today.length > 0 ){
                        rewardsListTpl += `<div class="reward-group">`
                            rewardsListTpl += `<div class="date">Today</div>`
                            rewardsListTpl += `<div class="items">`;
                                localGroupedRewards.today.map( reward => {
                                    rewardsListTpl += JourneyAchievements.rewardsTpl(reward);
                                })
                            rewardsListTpl += `</div>`;
                        rewardsListTpl += `</div>`;
                    }

                    if(localGroupedRewards.yesterday.length > 0 ){
                        rewardsListTpl += `<div class="reward-group">`
                        rewardsListTpl += `<div class="date">Yesterday</div>`
                        rewardsListTpl += `<div class="items">`;
                            localGroupedRewards.yesterday.map( reward => {
                                rewardsListTpl += JourneyAchievements.rewardsTpl(reward);
                            })
                        rewardsListTpl += `</div>`;
                        rewardsListTpl += `</div>`;
                    }

                    if(localGroupedRewards.prior.length > 0 ){

                        localGroupedRewards.prior.map( rewards => {
                            var dateText = JourneyAchievements.pbdCheckDate(rewards.date);
                            
                            rewardsListTpl += `<div class="reward-group">`
                            rewardsListTpl += `<div class="date">${dateText}</div>`
                            rewardsListTpl += `<div class="items">`;
                            rewards.objects.map( reward => {

                                rewardsListTpl += JourneyAchievements.rewardsTpl(reward);
                            })
                            rewardsListTpl += `</div>`;
                            rewardsListTpl += `</div>`;
                        } )
                    }

                rewardsListTpl += `</div>`


                let pageStart = parseInt( JourneyAchievements.rewardsPaginate.page * JourneyAchievements.rewardsPaginate.length ) + 1;
                let totalPages = Math.ceil(rewards.total / JourneyAchievements.rewardsPaginate.length );

                rewardsListTpl += JourneyAchievements.rewardsPaginateTpl(pageStart, totalPages, rewards.total);

                $(".achievements-container.class-points").html(rewardsListTpl);
                $(".achievements-container.class-points.active").fadeTo("fast",1)
                
                $(".achievements-container.class-points .points span").html(rewards.totalpoints);
                
                JourneyAchievements.familyInstitute.studentDropdown();
            })
            .catch( e => {
                console.log("error on getrewards", e)
            })
    },

    familyInstitute: {
        studentDropdown : () => {
            if(achievementsObject.is_user_institute_parent){
                let dropdownItems =  ``;
                let selectedStudent = ``;
                achievementsObject.children.map( student => {

                    //console.log("student",student)

                    dropdownItems += `<div class="item" data-studentid="${student.ID}">
                        <img src="${student.data.avatar_url}" class="avatar"/>
                        <span class="student-name" >${student.data.first_name} ${student.data.last_name}</span> 
                    </div>`
    
                    if(student.ID == achievementsObject.child_id){
                        selectedStudent = `
                            <img src="${student.data.avatar_url}" class="avatar"/>
                            <span class="student-name" style="
                            max-width: 151px;
                            overflow: hidden;
                            display: inline-flex;
                            white-space: nowrap;
                        ">${student.data.first_name} ${student.data.last_name}</span> 
                        `
                    }
                })
    
                $('.students-dropdown-container .selected div').html(selectedStudent);
                $('.students-dropdown-container .dropdown .items').html(dropdownItems);
            }
        }  
    },

    init: () => {

        // skeleton loader for achievements
        let skeletonLoader =``;
        for(i = 0; i < 6; i++){
            skeletonLoader += `
            <div class="item  ">
                <div class="image">
                    <div class="skeleton-loader" style=" height: 120px; width: 120px; border-radius: 100px; "></div>
                </div>
                <div class="details">
                    <div class="header">
                        <div class="title"><div class="skeleton-loader" style="height:25px; width:100px"></div></div>
                        <div class="progress"><div class="skeleton-loader" style="height:25px; width:50px"></div></div>
                    </div>
                    <div class="progress-bar skeleton-loader">
                    </div>
                    <div class="description">
                        <div class="skeleton-loader" style="height:25px; width:50%"></div>
                    </div>
                </div>
            </div>
            `;
        }
        $(".achievements-container .tab-content .items").html(skeletonLoader);

        JourneyAchievements.api.getAchievements({status:JourneyAchievements.status})
            .then( e => {
                JourneyAchievements.badges.all = e;
                JourneyAchievements.displayAchievements();
            })
            .catch( e => {
                console.log("error on displayAchievements", e)
            })
        
        JourneyAchievements.loadRewards();
        
        JourneyAchievements.familyInstitute.studentDropdown();
        
       
        
        
    }

}



// initialize achievements js here
JourneyAchievements.init();


// events listener here
$(document).on("click",".tab-heading a",e => {
    e.preventDefault();
    $(".tab-heading a").removeClass("active");
    $(e.currentTarget).addClass("active")
    JourneyAchievements.status = $(e.currentTarget).attr("data-type")
    JourneyAchievements.displayAchievements();
})

$(document).on("click",".nav-tabs a", e => {
    e.preventDefault();
    $(".nav-tabs a").removeClass('active')
    $(e.currentTarget).addClass("active")

    let target = $(e.currentTarget).attr("target");
    $(".achievements-container").removeClass("active")
    $(`.achievements-container${target}`).addClass("active")
})

$(document).on("click", ".filter-container", e => {
    $(".filter-container .list").addClass("active")
})


$(document).on("click", ".filter-container .list li", e => {
    JourneyAchievements.selectedClassPointsFilter = $(e.currentTarget).text();
    $(".filter-container .selected span").text(JourneyAchievements.selectedClassPointsFilter)
    JourneyAchievements.rewardsPaginate.page = 1;
    JourneyAchievements.loadRewards();
})

$(document).on("click",".students-dropdown-container", e => {
    $(e.currentTarget).find(".dropdown").toggleClass("active")
})
$(document).on("click",".students-dropdown-container .dropdown .overlay", e=>{
    $(".students-dropdown-container .dropdown").removeClass(".active")
});
$(document).on("click",".students-dropdown-container .items .item", e => {
    let studentid = $(e.currentTarget).attr("data-studentid")
    achievementsObject.child_id = studentid;
    JourneyAchievements.init();
})