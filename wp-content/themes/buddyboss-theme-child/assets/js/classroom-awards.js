$ = jQuery;
let classroomAwards = {
    students: [],
    selectedStudent: {},
    selectedClassroom: {},
    schoolDetails:"",
    achievements: [],
    isRewardsPage: false,
    rewards: {
        "achievement": {},
        "points": 0,
        "comment": "",
    },
    rewardsMultiple: false,
    rewardsSelectedStudents: [],
    feedbackType: "positive",
    api: {
        school: {
            getuserSchoolDetails: async ( data ) => {
                return new Promise((resolve, reject) => {

                    if(classroomAwards.schoolDetails != "" ){
                        resolve(classroomAwards.schoolDetails);
                    }else{
                        
                        $.ajax({
                            url: `${safarObject.apiBaseurl}/manage-classrooms/school/${classroomAwardsObj.classroomId}`,
                            type: "get",
                            data: {
                                bypasstransient : true
                            },
                            headers: {
                                "X-WP-Nonce": safarObject.wpnonce
                            },
                            dataType: "json",
                            beforeSend: (xhr) => {
                                
                            },
                            success: (d) => {
                                schoolbypasstransitent = false;
                                classroomAwards.schoolDetails = d;

                                resolve(d);
                            },
                            error: (d) => {
                                reject(`error /manage-classrooms/school}`);
                            }
                        });
                    }
                })
            },
        },

        getAchievements: async (data ) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/rewards/achievements`,
                    type: "get",
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {
                        
                    },
                    success: (d) => {
                        classroomAwards.achievements = d;

                        classroomAwards.achievements.map( achievement => {
                            classroomAwards.preloadImage(achievement.image);
                        })

                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /rewards/achievements}`);
                    }
                });
            });
        },

        saveReward: async (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/rewards/${data.classroomId}`,
                    type: "POST",
                    data,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {
                        
                    },
                    success: (d) => {
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /rewards/${data.classroomId}/}`);
                    }
                });
            });
        },

        saveAttendance: async (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/attendance/${data.classroomId}`,
                    type: "POST",
                    data: data.formData,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {
                        
                    },
                    success: (d) => {
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /attendance/}`);
                    }
                });
            });
        }
    },

    getStudentById: (studentId) => {
        classroomAwards.selectedStudent = {};
        classroomAwards.students.map( student => {
            if(student.ID == studentId){
                classroomAwards.selectedStudent = student;
            }
        })
        return classroomAwards.selectedStudent;
    },

    templates: {
        studentFeedback: (studentDetails) => {
            //console.log("studentFeedback", studentDetails, classroomAwards.feedbackType )
            var achievements = ``;
            classroomAwards.achievements.map( achievement => {

                var match = false;
                achievement.categories.map( cat => {
                    if(cat.slug == classroomAwards.feedbackType) match = true;
                })

                
                if(match){
                    achievements += `
                    <div class="item" data-id="${achievement.ID}">
                        <span class="points-count"></span>
                        <img src="${achievement.image}"/>
                        <div class="title">${achievement.title}</div>
                    </div>
                    `
                }
            })

            var points = [1,2,3,4,5,6,7,8,9,10,15,20];
            var selectText = "Select Points To Award";
            if( classroomAwards.feedbackType=="needs-work"){
                points = [-1, -2, -3, -4, -5, -6, -7, -8, -9, -10, -15, -20];
                selectText = "Select Points To Deduct";
            }

            var pointsTpl = ``;
            points.map(p => {
                pointsTpl += `<option value="${p}">${p}</option>`
            })

            let leftTpl = ``;
            let topTpl = ``;
            if(!classroomAwards.rewardsMultiple){
                topTpl = studentDetails.data.first_name; 
                leftTpl = `
                <div class="student-details">
                    <h5>${studentDetails.data.first_name} ${studentDetails.data.last_name}</h5>
                    <img src="${studentDetails.data.avatar.fullImage}"/>
                </div>
                `
            }else{
                leftTpl = `<div class="list">`;
                
                classroomAwards.rewardsSelectedStudents.map( student => {
                    leftTpl += `
                        <div class="item">
                            <div class="avatar-container">
                                <div class="avatar">
                                    <img src="${student.data.avatar_url}"/>
                                </div>
                            </div>
                            <div class="name">${student.data.first_name} ${student.data.last_name}</div>
                        </div>
                    `
                })

                leftTpl += `</div>`;

                topTpl = `${classroomAwards.rewardsSelectedStudents.length} students`
            }

            var tpl = `
                
                <div class="top">
                    <div class="text">Give feedback to ${topTpl}</div>
                    <div class="buttons">
                        <button type="button" class="positive ${(classroomAwards.feedbackType=="positive") ? `active`:``}">Positive</button>
                        <button type="button" class="needs-work ${(classroomAwards.feedbackType=="needs-work") ? `active`:``}">Needs Work</button>
                    </div>
                </div>
                <div class="main">
                    
                    <div class="left">
                        ${leftTpl}
                    </div>
                    <div class="right">
                        
                        <div class="middle">
                            <div class="achievements">
                                ${achievements}
                            </div>
                            <div class="points">
                                <div class="text">${selectText}</div>
                                <div>
                                    <select class="sel-rewards-points">
                                        ${pointsTpl}
                                    </select>
                                </div>
                            </div>
                            <div class="comment">
                                <textarea placeholder="Add comment" class="reward-comment"></textarea>
                            </div>
                        </div>
                        <div class="bottom">
                            <button type="button" class="cancel cancel-reward">Cancel</button>
                            <button type="button" class="save save-reward" disabled>Save</button>
                        </div>
                    </div>
                </div>
            `

            return tpl;
        }
    },

    preloadImage : (url) => {
        return new Promise((resolve, reject) => {
            if(url.length > 0 ){
                const image = new Image();
                image.onload = resolve;
                image.onerror = reject;
                image.src = url;
            }
        });
    },

    loadStudents: () => {
        let studentsTpl = ``

        for(i=0; i < 16; i++){
            studentsTpl += `
                <div class="student item skeleton-loader" style="background:#f9f9f9">
                    <div class="skeleton-loader avatar" style="background:#eee; width:80px; height:80px; border-radius:80px;"> </div>
                    <div class="name" style="background:#eee; width:80px; height:20px; margin-top:10px;"></div>
                </div>
            `
        }

        $(".attendance-students").html(studentsTpl)

        classroomAwards.api.school.getuserSchoolDetails({}).then( e => {
            
            classroomAwards.students = [];    
            classroomAwards.selectedClassroom = classroomAwards.schoolDetails;
            studentsTpl = ``;
            classroomAwards.schoolDetails.students.map( student => {
                classroomAwards.students.push(student); // store to browser
                
                studentsTpl += `
                    <div class="student item ${student.data.attendance} 
                            student-${student.data.ID} 
                            ${(classroomAwards.isRewardsPage) ? `rewards`:`attendance` }" 
                            data-attendance="${student.data.attendance}" 
                            data-studentid="${student.data.ID}"
                            data-points="${student.data.rewards.totalPoints}"
                            >
                        <input type="hidden" name="attendance_status[${student.data.ID}]" class="attendance-status" value="${student.data.attendance}" />
                        <div class="avatar">
                            <img src="${student.data.avatar_url}" />
                            ${(classroomAwards.isRewardsPage) ? `<span class="points">${student.data.rewards.totalPoints}</span>`:`<span></span>`}
                        </div>
                        <div class="name">${student.data.first_name} ${student.data.last_name}</div>
                    </div>
                `
                /* 
                preload images on the browser for later use
                */
                classroomAwards.preloadImage(student.data.avatar_url)
                classroomAwards.preloadImage(student.data.avatar.fullImage)
                classroomAwards.preloadImage(student.data.headImage)

                .then(() => { })
                .catch((error) => { });
            })
            
            $(".attendance-students").html(studentsTpl);

            if(classroomAwards.selectedClassroom.has_attendance){
                $(".attendance-students").addClass("has-attendance")
            }

            classroomAwards.attendanceBottomBar();

        }).catch( e => {
            console.log("error", e )
        });
    },

    attendanceBottomBar: () => {
        // update tab title for attendance tab
        if( $(".group-attendance-tab  #item-body .wrapper .title").length > 0 ){
            //Attendance for class XXX on Tue 31st Dec
            console.log("classroomAwards.selectedClassroom", classroomAwards.selectedClassroom )
            if(classroomAwards.selectedClassroom.has_attendance){
                var today = moment();
                var formattedDate = today.format("ddd Do MMM");

                $(".group-attendance-tab  #item-body .wrapper .title").text( `Attendance for class ${classroomAwards.selectedClassroom.post.post_title} on ${formattedDate}`);
                $(".cancel-attendance").show();

                $(".bottom-bar .update-attendance").addClass("active");
                $(".bottom-bar .new-attendance").removeClass("active");

                // statistics counter
                var present = 0;
                var late = 0;
                var absent = 0;
                
                $(".group-attendance-tab .student.item").each( function(){
                    studentCard = $(this);
                    if( studentCard.hasClass("present") ){
                        present++;
                    }else if( studentCard.hasClass("late") ){
                        late++;
                    }else if( studentCard.hasClass("absent") ){
                        absent++
                    }
                });

                $(".bottom-bar .update-attendance .num-present button").html(`${present} Present`);
                $(".bottom-bar .update-attendance .num-late button").html(`${late} Late`);
                $(".bottom-bar .update-attendance .num-absent button").html(`${absent} Absent`);
                $(".list.attendance-students").addClass("has-attendance")
            }else{
                $(".cancel-attendance").hide(); /// hide cancel attendance if no attendance saved for the day
                $(".bottom-bar .new-attendance").addClass("active");
                $(".bottom-bar .update-attendance").removeClass("active");
                $(".list.attendance-students").removeClass("has-attendance")
            }
        }
    },
    updateAchievementsItem: () => {
        let points = $(".sel-rewards-points").val();
        $("#rewards-feedback .achievements .item .points-count").html(points);
    },

    showReward: () => {
        
        let rewardIcon = `<svg width="100" height="101" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M34.9129 99.6001L49.3113 64.8622L30.5983 56.9551L16.2031 91.6925L28.3074 89.0148L34.9129 99.6001Z" fill="#EF746F"/>
            <path d="M60.0858 100.207L47.2119 64.8131L66.2489 57.8125L79.1192 93.2039L67.144 89.9471L60.0858 100.207Z" fill="#EF746F"/>
            <path d="M80.3412 41.8784L85.2386 37.8163L80.5418 33.5007L84.2246 28.2958L78.5852 25.352L80.8033 19.3589L74.6 17.9866L75.2059 11.6172L68.8637 11.9078L67.8139 5.59461L61.7643 7.52939L59.1299 1.70112L53.7873 5.1476L49.7449 0.207031L45.4753 4.92921L40.3043 1.20716L37.3917 6.88939L31.4444 4.63839L30.0916 10.8887L23.7712 10.2657L24.0717 16.6589L17.8107 17.7081L19.7415 23.8063L13.9679 26.4532L17.3971 31.8417L12.5 35.9071L17.1968 40.2226L13.5141 45.4275L19.1535 48.3713L16.9349 54.3611L23.1386 55.7367L22.5327 62.1061L28.8749 61.8156L29.9247 68.1287L35.9743 66.194L38.6087 72.0222L43.9513 68.5757L47.9905 73.5167L52.2634 68.7941L57.4344 72.5162L60.3469 66.834L66.2942 69.085L67.647 62.8347L73.967 63.4543L73.6669 57.0645L79.9279 56.0152L77.9971 49.9171L83.7708 47.2701L80.3412 41.8784Z" fill="#F0AA00"/>
            <path d="M71.1018 38.6342C72.0518 26.2582 62.8667 15.4299 50.5863 14.4486C38.306 13.4672 27.5807 22.7044 26.6308 35.0804C25.6808 47.4564 34.8659 58.2847 47.1462 59.2661C59.4266 60.2474 70.1518 51.0102 71.1018 38.6342Z" fill="white"/>
            <path d="M49.6088 21.5244L53.9334 30.4324C54.0036 30.5796 54.1441 30.682 54.3014 30.7062L64.0046 32.1688C64.4062 32.2299 64.5649 32.7309 64.2736 33.0142L57.2388 39.9098C57.1243 40.0229 57.0718 40.1882 57.0975 40.3474L58.7373 50.1273C58.8057 50.5332 58.3857 50.8386 58.0243 50.6468L49.3518 46C49.208 45.9245 49.0369 45.9219 48.8949 45.9985L40.2058 50.5785C39.8441 50.7677 39.4256 50.4568 39.4956 50.0543L41.1723 40.2872C41.1989 40.125 41.1465 39.9624 41.0308 39.8471L34.0204 32.8976C33.7298 32.6112 33.89 32.1116 34.2937 32.0533L43.9997 30.6636C44.158 30.6413 44.2968 30.5385 44.3682 30.3941L48.7243 21.5179C48.9038 21.1519 49.4229 21.1525 49.602 21.5219L49.6088 21.5244Z" fill="#F0AA00"/>
        </svg>`

        if( classroomAwards.feedbackType == "needs-work"){
            rewardIcon = `
                <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1673_28342)">
                    <path d="M85.3553 85.3553C104.882 65.8291 104.882 34.1709 85.3553 14.6447C65.8291 -4.88155 34.1709 -4.88155 14.6447 14.6447C-4.88155 34.1709 -4.88155 65.8291 14.6447 85.3553C34.1709 104.882 65.8291 104.882 85.3553 85.3553Z" fill="#FFD54D"/>
                    <path d="M6.79706 55.275C6.79706 29.6368 27.5807 8.85321 53.2188 8.85321C68.2059 8.85321 81.5306 15.9549 90.018 26.9777C82.0002 13.0595 66.9757 3.69043 49.7645 3.69043C24.1264 3.69043 3.33984 24.4769 3.33984 50.1151C3.33984 60.7662 6.92959 70.5789 12.9624 78.4124C9.03849 71.6017 6.79418 63.6991 6.79418 55.275H6.79706Z" fill="#FFDD6F"/>
                    <path d="M76.1607 7.32324C81.139 15.0962 84.0258 24.3385 84.0258 34.255C84.0258 61.8695 61.6403 84.255 34.0258 84.255C24.4349 84.255 15.4779 81.5526 7.86621 76.8709C16.7513 90.743 32.303 99.9392 50.001 99.9392C77.6156 99.9392 100.001 77.5537 100.001 49.9392C100.001 31.9156 90.462 16.1219 76.1607 7.32324Z" fill="#FDB037"/>
                    <path d="M29.5084 72.221C28.2206 73.4714 26.1578 72.0885 26.8233 70.4204C30.5168 61.1896 39.543 54.667 50.0933 54.667C60.6436 54.667 69.3673 60.9735 73.1731 69.9594C73.8674 71.5987 71.7873 73.019 70.5082 71.7773C63.1904 64.6814 47.6559 54.5863 29.5084 72.2181V72.221Z" fill="#002D51"/>
                    <path d="M31.5793 43.7111C33.6144 43.7111 35.2642 40.9314 35.2642 37.5025C35.2642 34.0736 33.6144 31.2939 31.5793 31.2939C29.5443 31.2939 27.8945 34.0736 27.8945 37.5025C27.8945 40.9314 29.5443 43.7111 31.5793 43.7111Z" fill="#002D51"/>
                    <path d="M72.1069 37.5025C72.1069 40.9309 70.4561 43.7111 68.4221 43.7111C66.3881 43.7111 64.7373 40.9309 64.7373 37.5025C64.7373 34.0741 66.3881 31.2939 68.4221 31.2939C70.4561 31.2939 72.1069 34.0741 72.1069 37.5025Z" fill="#002D51"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_1673_28342">
                    <rect width="100" height="100" fill="white"/>
                    </clipPath>
                    </defs>
                </svg>
            `
        }

        let tpl = `
            <div class="avatar ${(classroomAwards.rewardsMultiple) ? `multiple`:``}">
                ${(classroomAwards.rewardsMultiple) ? 
                    `${rewardIcon}`
                    :
                    `<img src="${classroomAwards.selectedStudent.data.avatar_url}"/>`}
            </div>
            <div class="info">
                <div class="name">
                    ${(classroomAwards.rewardsMultiple) ? `${classroomAwards.rewardsSelectedStudents.length} Students`:`${classroomAwards.selectedStudent.data.first_name}`}
                </div>
                <div class="achievement-name">${(classroomAwards.feedbackType=="positive") ? `+`:``}${classroomAwards.rewards.points} for ${classroomAwards.rewards.achievement.title}</div>
            </div>
            <div class="image">
                <div class="point-count ${classroomAwards.feedbackType}">${classroomAwards.rewards.points}</div>
                <img src="${classroomAwards.rewards.achievement.image}"/>
            </div>
        `
        $(".current-reward-container").html(tpl).addClass("active");

        var rewardContainer = $(".current-reward-container");
        // Animate the element to the center of the page
        rewardContainer.animate({
            bottom: `50px`
        }, 200); // Adjust the duration as needed
        // animate using javascript

        setTimeout( () => {
            $(".current-reward-container").removeClass('active')
            var rewardContainer = $(".current-reward-container");
            // Animate the element to the center of the page
            rewardContainer.animate({
                bottom: `-100px`
            }, 200); // Adjust the duration as needed
            // animate using javascript
        }, 5000)
    },

    rewardsBottomBar: (view) => {
        
        switch(view){
            case 1:
                $(".group-rewards-tab .bottom-bar .select-multiple-all").removeClass("active");
                $(".group-rewards-tab .bottom-bar .show-selected").addClass("active");
                break;
            case 0: default:
                $(".group-rewards-tab .bottom-bar .select-multiple-all").addClass("active");
                $(".group-rewards-tab .bottom-bar .show-selected").removeClass("active");
                break;
        }

        
    },

    rewardsCountSelected: () => {
        let selectedCount = $(".group-rewards-tab .student.item.active").length;
        let expectedCount = $(".group-rewards-tab .student.item:not(.absent)").length;
        $(".show-selected .middle").html(`${selectedCount} selected`)

        if(selectedCount>=expectedCount){
            $(".buttons-select-deselect").html(`
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="14" cy="14" r="14" fill="#EF746F"/>
                <path d="M18 10L10 18M10 10L18 18" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <button type="button" class="select-none" >Select None</button>
            `)
        }else{
            $(".buttons-select-deselect").html(`
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="14" cy="14" r="14" fill="#98C03D"/>
                <path d="M9 15.3846L11.3077 17.6923L19 10" fill="#98C03D"/>
                <path d="M9 15.3846L11.3077 17.6923L19 10" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="select-all" >Select All</button>
            `)
        }

        classroomAwards.rewardsSelectedStudents = [];
        $(".group-rewards-tab .student.item.active").each( function(){
            let studentid = $(this).attr("data-studentid");

            classroomAwards.students.map( student => {
                if(student.ID == studentid ){
                    classroomAwards.rewardsSelectedStudents.push(student)
                }
            })
            
        });      


        console.log("selectedCount",classroomAwards.rewardsSelectedStudents)
        if( selectedCount <= 0 ){
            $(".show-selected .send-feedback").attr("disabled","disabled")
        }else{
            $(".show-selected .send-feedback").removeAttr("disabled")
        }
    },

    updateStudentPoints: () => {
        console.log("classroomAwards.rewards", classroomAwards.rewards)
        let absPoints = Math.abs(classroomAwards.rewards.points);
        
        if(classroomAwards.rewardsMultiple){
            classroomAwards.rewardsSelectedStudents.map( student => {
                let studentCard = $(`.student.item.student-${student.data.ID}`);
                let currentPoints = parseInt(studentCard.attr("data-points"));
                
                if( classroomAwards.rewards.points < 0 ){
                    currentPoints = currentPoints - absPoints;
                }else{
                    currentPoints = currentPoints + absPoints;
                }
                studentCard.attr("data-points",currentPoints);
                studentCard.find(".points").text(currentPoints);
            })
        }else{
            let studentCard = $(`.student.item.student-${classroomAwards.selectedStudent.data.ID}`);
            let currentPoints = parseInt(studentCard.attr("data-points"));
            
            if( classroomAwards.rewards.points < 0 ){
                currentPoints = currentPoints - absPoints;
            }else{
                currentPoints = currentPoints + absPoints;
            }
            studentCard.attr("data-points",currentPoints);
            studentCard.find(".points").text(currentPoints);
        }
    },

    init: () => {
        classroomAwards.api.getAchievements();
        classroomAwards.loadStudents();

        if( $("body").hasClass("group-rewards-tab")) classroomAwards.isRewardsPage = true;

        $(".bottom-bar").show().css("display","flex")

    }
}

/*
Dev Note: store data to browser for faster and responsive page load
*/

$(document).ready( e => {
    classroomAwards.init();
})

// event listeners for attendance tab

$(document).on("click", ".group-attendance-tab .student.item", e => {
    if( !$("#frm-students.list").hasClass("has-attendance")){
        let studentCard = $(e.currentTarget);
        // rotate card state from present -> late -> absent
        if( studentCard.hasClass("present") ){
            studentCard.removeClass("present").addClass("late");
            studentCard.find(".attendance-status").val("late");
        }else if( studentCard.hasClass("late") ){
            studentCard.removeClass("late").addClass("absent");
            studentCard.find(".attendance-status").val("absent");
        }else if( studentCard.hasClass("absent") ){
            studentCard.removeClass("absent").addClass("present");
            studentCard.find(".attendance-status").val("present");
        }
    }
})

$(document).on("click","button.mark-all-present", e => {
    $(".group-attendance-tab .attendance-students .student.item").removeClass("absent").removeClass("late").addClass("present");
    $(".group-attendance-tab .attendance-students .student.item").find(".attendance-status").val("present");
})

$(document).on("click","button.mark-all-absent", e => {
    $(".group-attendance-tab .attendance-students .student.item").removeClass("present").removeClass("late").addClass("absent");
    $(".group-attendance-tab .attendance-students .student.item").find(".attendance-status").val("absent");
})

$(document).on("click","button.save-attendance",e => {
    //frm-students
    let saveButton = $(e.currentTarget);
    saveButton.fadeTo("fast",.3);

    classroomAwards.selectedClassroom.has_attendance = true;
    classroomAwards.attendanceBottomBar();
    

    classroomAwards.api.saveAttendance({classroomId: classroomAwardsObj.classroomId, formData: $("#frm-students").serialize()})
        .then( e => {
            saveButton.fadeTo("fast",1);
            // submit attendance to googlesheet
            $.ajax({
                url: `${safarObject.ajaxurl}?action=generate_attendance_google_sheet`,
                type: "get",
                headers: {
                    "X-WP-Nonce": safarObject.wpnonce
                },
                beforeSend: (xhr) => {
                    
                },
                success: (d) => {
                },
                error: (d) => {
                }
            });
        })
        .catch( e => {
            console.log("error save attendance")
        })
})

$(document).on("click","button.update-attendance", e => {
    $(".bottom-bar .new-attendance").addClass("active");
    $(".bottom-bar .update-attendance").removeClass("active");
    $(".list.attendance-students").removeClass("has-attendance")
})

$(document).on("click","button.cancel-attendance", e => {
    $(".bottom-bar .new-attendance").removeClass("active");
    $(".bottom-bar .update-attendance").addClass("active");
    $(".list.attendance-students").addClass("has-attendance")
})


// events listener for rewards
$(document).on("click", ".group-rewards-tab .student.item:not(.absent)", e => {
    if(!classroomAwards.rewardsMultiple){
        let studentId = $(e.currentTarget).attr("data-studentid")
        let studentDetails = classroomAwards.getStudentById(studentId);
        classroomAwards.feedbackType = "positive";
        $("#rewards-feedback").show().css({"display":"flex"})
        $("#rewards-feedback .modal-content .content").html( classroomAwards.templates.studentFeedback(studentDetails) );
    }else{
        // add active class to student item
        let card = $(e.currentTarget);
        card.toggleClass("active");
        classroomAwards.rewardsCountSelected();
    }
})


$(document).on("click", ".group-rewards-tab .achievements .item", e => {
    $(".group-rewards-tab .achievements .item").removeClass("active");
    $(".save-reward").removeAttr("disabled");
    $(e.currentTarget).addClass("active")
})

$(document).on("mouseover", ".group-rewards-tab .achievements .item", e => {
    classroomAwards.updateAchievementsItem();
})
$(document).on("change", ".sel-rewards-points", e => {
    classroomAwards.updateAchievementsItem();
})

$(document).on("click",".cancel-reward", e => {
    $("#rewards-feedback").hide();
})
$(document).on("click",".save-reward", e => {
    let achievementId = $("#rewards-feedback .achievements .item.active").attr("data-id");
    achievementDetails = {};
    classroomAwards.achievements.map( achievement => {
        if( achievement.ID == achievementId ) achievementDetails = achievement;
    });
    classroomAwards.rewards = {
        "achievement": achievementDetails,
        "points": $(".sel-rewards-points").val(),
        "comment": $(".reward-comment").val()
    }
    classroomAwards.showReward();
    $("#rewards-feedback").hide();

    // call ajax here
    let studentIds = [];
    classroomAwards.rewardsSelectedStudents.map( student => {
        studentIds.push(student.data.ID)
    })
    let data = {
        "is_multiple": classroomAwards.rewardsMultiple,
        "single_student": ( (!classroomAwards.rewardsMultiple) ? classroomAwards.selectedStudent.data.ID:0),
        "student_ids": studentIds,
        "type": classroomAwards.feedbackType,
        "achievement": classroomAwards.rewards.achievement.ID,
        "points": classroomAwards.rewards.points,
        "classroomId": classroomAwardsObj.classroomId,
        "comment": classroomAwards.rewards.comment
    }
    classroomAwards.api.saveReward(data)
        .then( e => { })
        .catch( e => { console.log(e)})

    // adjust student points on view
    classroomAwards.updateStudentPoints();

    // set rewards tab to default mode
    if(classroomAwards.rewardsMultiple){
        $(".cancel-feedback").trigger("click")
    }
})

$(document).on("click",".current-reward-container", e => {
    $(".current-reward-container").removeClass('active')
    var rewardContainer = $(".current-reward-container");
    // Animate the element to the center of the page
    rewardContainer.animate({
        bottom: `-100px`
    }, 200); // Adjust the duration as needed
    // animate using javascript
})

$(document).on("click","#rewards-feedback .top .buttons .needs-work",e => {
    $("#rewards-feedback .top .buttons .positive").removeClass("active")
    $("#rewards-feedback .top .buttons .needs-work").addClass("active")

    classroomAwards.feedbackType = "needs-work";
    classroomAwards.updateAchievementsItem();
    $("#rewards-feedback .modal-content .content").html(classroomAwards.templates.studentFeedback( classroomAwards.selectedStudent ));
    $("#rewards-feedback .achievements").addClass("negative")
})

$(document).on("click","#rewards-feedback .top .buttons .positive",e => {
    $("#rewards-feedback .top .buttons .positive").addClass("active")
    $("#rewards-feedback .top .buttons .needs-work").removeClass("active")
    
    classroomAwards.feedbackType = "positive";
    classroomAwards.updateAchievementsItem();
    $("#rewards-feedback .modal-content .content").html(classroomAwards.templates.studentFeedback( classroomAwards.selectedStudent ));
    $("#rewards-feedback .achievements").removeClass("negative")
})

$(document).on("click",".select-multiple-all .select-multiple", e => {
    classroomAwards.rewardsMultiple = true;
    classroomAwards.rewardsBottomBar(1);
    classroomAwards.rewardsCountSelected();
});
$(document).on("click",".select-multiple-all .select-all, .show-selected .select-all", e => {
    classroomAwards.rewardsMultiple = true;
    classroomAwards.rewardsBottomBar(1);

    $(".group-rewards-tab .student.item:not(.absent)").each( function(){
        $(this).addClass("active")
    })
    classroomAwards.rewardsCountSelected();
});

$(document).on("click",".show-selected .select-none", e => {
    classroomAwards.rewardsMultiple = true;
    classroomAwards.rewardsBottomBar(1);

    $(".group-rewards-tab .student.item:not(.absent)").each( function(){
        $(this).removeClass("active")
    })
    classroomAwards.rewardsCountSelected();
});

$(document).on("click",".show-selected .cancel-feedback", e => {
    classroomAwards.rewardsMultiple = false;
    classroomAwards.rewardsBottomBar(0);
    $(".group-rewards-tab .student.item:not(.absent)").each( function(){
        $(this).removeClass("active")
    })
    classroomAwards.rewardsCountSelected();
});

$(document).on("click",".show-selected .send-feedback", e => {
    let studentDetails = {}; // not available because multiple students are selected
    classroomAwards.feedbackType = "positive";
    $("#rewards-feedback").show().css({"display":"flex"})
    $("#rewards-feedback .modal-content .content").html( classroomAwards.templates.studentFeedback(studentDetails) );
})