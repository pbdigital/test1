$ = jQuery;
let Safar = {}
jQuery(document).ready( $ => {
    Safar = {
        userInfo: {},
        user: {},
        users: [],
        rewardsNotifIds: [],
        
        getUserInfo: async (data) => {

            if(typeof data == "string"){
                data = {userid: data, disabledachievements: false}
            }

            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/user/${data.userid}`,
                    data: {
                        disabledachievements: data.disabledachievements,
                        classroomid: safarObject.classroomId
                    },
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    beforeSend: () => {

                    },
                    success: (d) => {
                    
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /user/${data.userid}`, data, d);
                    }
                });
            });

        },

        leaderboard: {
            points: async (data) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/leaderboard/points/${data.gid}`,
                        data,
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: () => {
        
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /user/${userId}`);
                        }
                    });
                });
            },

            practiceTracker: async (data) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/leaderboard/practice_tracker/${data.gid}`,
                        data,
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: () => {
        
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error leaderboard/practice_tracker/${userId}`);
                        }
                    });
                });
            },

            quiz: async ( quizid) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/quiz/leaderboard/${quizid}`,
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: () => {
        
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /quiz/leaderboard/${quizid}`, );
                        }
                    });
                });
            }
        },

        dashboard: {
            courses: async  () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/subjects`,
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: () => {
        
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error api subjects `);
                        }
                    });
                });
            },

            goals: async () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/dashboard/goals`,
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: () => {
        
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /dashboard/goals`);
                        }
                    });
                });
            },

            recentActivities: async () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/dashboard/recent_activities`,
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: () => {
        
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /dashboard/recent_activities`);
                        }
                    });
                });
            }

        },

        practiceTracker : {
            saveLog: async (date, minutes) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/practice_tracker/log`,
                        type: "put",
                        data: {
                            date,
                            minutes,
                            offset: moment().tz(moment.tz.guess()).format("Z"),
                            tz: moment.tz.guess(),
                            
                        },
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: () => {

                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /practice_tracker/log`);
                        }
                    });
                });
            },

            logs: async () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/practice_tracker/logs`,
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: () => {

                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /practice_tracker/logs`);
                        }
                    });
                });
            },

            quranicSpin: {
                type: "", // this can have 25, 50, 75, or admin => admin manual award spin 
                showModal: () => {
                    console.log("show modal", Safar.practiceTracker.quranicSpin.type );

                    $("#quranic-animal-spin").show().css({"display":"flex"});
                    $("#logPractice").css({"display":"none"});
                },

                showAwardedAnimal : () => {
                    Safar.practiceTracker.quranicSpin.award({type: Safar.practiceTracker.quranicSpin.type})
                        .then( d => {
                            $("#quranic-animal-spin").hide();
                            $("#awarded-quranic-animal").show().css({"display":"flex"});

                            let awardedQuranicAnimalTpl = ``;


                            awardedQuranicAnimalTpl = `
                                
                                <div class="animal-image">
                                    <div class="num">${d.animal_position}</div>
                                    <img src="${d.image}" />
                                    <div class="title">${d.post_title}</div>
                                    <div class="excerpt">${d.post_excerpt}</div>
                                </div>

                                <h2>Congratulations!</h2>
                                <p>You have unlocked the ${d.post_title}</p>

                            `;

                            $(".quranic-animal-content").html(awardedQuranicAnimalTpl);

                            confetti.start();

                            setTimeout(() => {
                                confetti.stop();
                            }, 5000);
                            
                        })
                        .catch( e => {
                            console.log("error showAwardedAnimal", e)
                        })
                },
                award: async ( data ) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: `${safarObject.apiBaseurl}/quranic_animal/award`,
                            data: {
                                type: data.type
                            },
                            type: "put",
                            headers: {
                                "X-WP-Nonce": safarObject.wpnonce
                            },
                            beforeSend: () => {
        
                            },
                            success: (d) => {
                                resolve(d);
                            },
                            error: (d) => {
                                reject(`error /quranic_animal/award`);
                            }
                        });
                    });
                },

                pendingAwards: async ( data ) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: `${safarObject.apiBaseurl}/user/quranic_animal/award`,
                            headers: {
                                "X-WP-Nonce": safarObject.wpnonce
                            },
                            beforeSend: () => {
        
                            },
                            success: (d) => {
                                resolve(d);
                            },
                            error: (d) => {
                                reject(`error /user/quranic_animal/award`);
                            }
                        });
                    });
                }
            }
        },


        shareQuizResult: async ( data ) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/quiz/share_result`,
                    type: "post",
                    data,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    beforeSend: () => {

                    },
                    success: (d) => {
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /quiz/share_result`);
                    }
                });
            });
        },


        profilePopup: (userId) => {
            // skeleton loader for the popup
            $(`.bb-action-popup .modal-container`).html(`
                <header class="bb-model-header">
                    <h4><div class="skeleton-loader" style="width:200px; height:40px"></div></h4>
                    <a class="bb-close-view-profile bb-model-close-button" href="#">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 1 1 17M1 1l16 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </header>
                <div class="bb-view-profile-content bb-action-popup-content">
                    <div class="profile-dashboard">
                        <div class="profile-dashboard__item">
                        <?xml version="1.0" encoding="utf-8"?>
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                        <g transform="rotate(0 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(30 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(60 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(90 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(120 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(150 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(180 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(210 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(240 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(270 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(300 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
                        </rect>
                        </g><g transform="rotate(330 50 50)">
                        <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#58b8ea">
                            <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate>
                        </rect>
                        </g>
                        <!-- [ldio] generated by https://loading.io/ --></svg>
                        </div>
                    </div>
                    <div class="badges-earned">
                        <h5><div class="skeleton-loader" style="width:100px; height:25px"></div></h5>
                        <div class="badges-earned__list">
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>							
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>
                        </div>
                    </div>
                    <div class="badges-earned">
                        <h5><div class="skeleton-loader" style="width:100px; height:25px"></div></h5>
                        <div class="badges-earned__list">
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>							
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>
                            <div class="skeleton-loader" style="width:80px; height:80px"></div>
                        </div>
                    </div>
                    <div class="profile-stats">
                        <div class="profile-stats__inner">
                            <div>
                                <h5><div class="skeleton-loader" style="width:100px; height:25px"></div> </h5>
                                <div class="skeleton-loader" style="width:30px; height:30px"></div>
                            </div>
                        </div>
                        <div class="profile-stats__inner">
                            <div>
                                <h5><div class="skeleton-loader" style="width:100px; height:25px"></div></h5>
                                <div class="skeleton-loader" style="width:30px; height:30px"></div>
                            </div>
                        </div>
                        <div class="profile-stats__inner">
                            <div>
                                <h5><div class="skeleton-loader" style="width:100px; height:25px"></div></h5>
                                <div class="skeleton-loader" style="width:30px; height:30px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            Safar.getUserInfo(userId).then( (user) => {
                let badgesEarned = ``;
                let totalBadges = 0;
                let earnedBadges = 0;

                if (!Safar.users.includes(user)) {
                    Safar.users.push(user);
                }

                user.all_badges.map( badge => {
                    if(badge.earned){
                        badgesEarned += `<div class="item">
                                            <img src="${badge.image}" alt="" class="with-tooltip">

                                            <div class="qa-description-popup-content" data-class="badges">

                                                <div class="qa-image"><img src="${badge.image}" /></div>
                                                <div class="qa-details">

                                                    <div class="qa-title">${badge.badge}</div>
                                                    <div class="qa-description">${badge.badge_description}</div>

                                                </div>
                                            </div>
                                            
                                        </div>    
                                        `
                        earnedBadges++;
                    }
                    totalBadges++;
                })

                let petAccessoriesEearned = ``;
                user.earned_pet_accessories.map( achievement => {
                    petAccessoriesEearned += `<div class="profile-accessories__item">
                        <img src="${achievement.image}" alt="">									
                    </div>`
                })

                let accessories = ``;
                user.earned_accessories.map( achievement => {
                    accessories += `<div class="profile-accessories__item">
                        <img src="${achievement.image}" alt="">									
                    </div>`
                })

                let quranicAnimals = ``;
                let totalQa = 0;
                let earnedQa = 0;
                user.all_quranic_animals.map( qa => {
                    if(qa.earned){
                        quranicAnimals += `
                            <div class="item ">
                                <img src="${qa.image}" alt="" class="with-tooltip">


                                <div class="qa-description-popup-content" data-class="quranic-animal">

                                    <div class="qa-image"><img src="${qa.image}" /></div>
                                    <div class="qa-details">

                                        <div class="qa-title">${qa.title}</div>
                                        <div class="qa-description">${qa.description}</div>

                                    </div>
                                </div>
                                        
                            </div>

                        `
                        earnedQa++;
                    }
                    totalQa++
                })



                let userInfoTemplate = `
                <header class="bb-model-header">
                        <h4>${user.first_name} ${user.last_name}</h4>
                        <a class="bb-close-view-profile bb-model-close-button" href="#">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 1 1 17M1 1l16 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </header>
                    <div class="bb-view-profile-content bb-action-popup-content">
                        <div class="profile-dashboard">
                            
                            <div class="profile-dashboard__item shelf1"><img src="${safarObject.stylesheet_directory}/assets/img/members/shelf.png" alt=""></div>
                            <div class="profile-dashboard__item shelf2"><img src="${safarObject.stylesheet_directory}/assets/img/members/shelf2.png" alt=""></div>
                            <div class="profile-dashboard__item mat"><img src="${safarObject.stylesheet_directory}/assets/img/members/mat.png" alt=""></div>
                            
                            ${
                                (user.gender == "male") ? 
                                `<div class="profile-dashboard__item student male">
                                    ${
                                        (user.avatar_full != "") ? `<img src="${user.avatar_full}?${user.uniqid}" alt="">`:`<img src="${safarObject.stylesheet_directory}/assets/img/members/boy.png" alt="">`
                                    }
                                </div>`
                                :
                                `<div class="profile-dashboard__item student female">
                                    
                                    ${
                                        (user.avatar_full != "") ? `<img src="${user.avatar_full}?${user.uniqid}" alt="">`:`<img src="${safarObject.stylesheet_directory}/assets/img/members/girl.png" alt="">`
                                    }
                                </div>`
                            }
                            
                            
                        </div>
                        <div class="profile-accessories qa-earned-${earnedQa}">
                            <div class="profile-accessories__items">
                                <div class="achievment-heading"> 
                                    <h5>Quranic Animals</h5>
                                    <div class="quranic-animal-count achievement-count"></div>
                                </div>
                                <div class="profile-quranic_animal_list">
                                    ${quranicAnimals}
                                </div>
                            </div>
                        
                        </div>

                        
                        
                        <div class="badges-earned badges-earned-${earnedBadges}">
                            <div class="achievment-heading">
                                <h5>Badges Earned</h5>
                                <div class="badges-count achievement-count"></div>
                            </div>
                            <div class="badges-earned__list">
                                ${badgesEarned}
                            </div>
                        </div>
                        <div class="profile-stats">
                            <div class="profile-stats__inner">
                                <div>
                                    <h5>Points Earned </h5>
                                    <div class="profile-stats__pts"><img src="${safarObject.site_url}/wp-content/uploads/2022/07/coin.png" alt=""> ${user.points}</div>
                                </div>
                            </div>
                            <div class="profile-stats__inner">
                                <div>
                                    <h5>Practices</h5>
                                    <div class="profile-stats__pts"><img src="${safarObject.site_url}/wp-content/uploads/2022/07/book.png" alt=""> ${user.practice_logs_count}</div>
                                </div>
                            </div>
                            <div class="profile-stats__inner">
                                <div>
                                    <h5>Practice Streak</h5>
                                    <div class="profile-stats__pts"><img src="${safarObject.site_url}/wp-content/uploads/2022/07/streak.png" alt=""> ${user.practice_streak}</div>
                                </div>
                            </div>
                        </div>
                        
                        ${ (user.is_institute_student_user) 
                            ? `
                            <div class="reward-points">
                                <div class="left">
                                    <div class="image">
                                        <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.1722 32.5517L15.7797 21.4356L9.79152 18.9053L5.18506 30.0213L9.05842 29.1644L11.1722 32.5517Z" fill="#EF746F"/>
                                        <path d="M19.2276 32.7461L15.1079 21.4199L21.1998 19.1797L25.3182 30.5049L21.4862 29.4627L19.2276 32.7461Z" fill="#EF746F"/>
                                        <path d="M25.7092 14.0809L27.2764 12.781L25.7734 11.4001L26.9519 9.7345L25.1472 8.7925L25.8571 6.87471L23.872 6.43556L24.0659 4.39736L22.0364 4.49033L21.7005 2.47012L19.7646 3.08925L18.9216 1.2242L17.2119 2.32708L15.9184 0.746094L14.5521 2.25719L12.8974 1.06613L11.9653 2.88445L10.0622 2.16413L9.62931 4.16423L7.6068 3.96488L7.70295 6.01068L5.69943 6.34644L6.31728 8.29786L4.46972 9.14488L5.56707 10.8692L4 12.1701L5.50298 13.5511L4.3245 15.2167L6.12912 16.1587L5.41918 18.0754L7.40435 18.5156L7.21047 20.5538L9.23997 20.4608L9.57591 22.481L11.5118 21.8619L12.3548 23.727L14.0644 22.6241L15.357 24.2052L16.7243 22.694L18.379 23.885L19.311 22.0667L21.2142 22.787L21.6471 20.7869L23.6694 20.9852L23.5734 18.9405L25.5769 18.6047L24.9591 16.6533L26.8067 15.8063L25.7092 14.0809Z" fill="#F0AA00"/>
                                        <path d="M22.7526 13.0428C23.0566 9.08247 20.1173 5.61741 16.1876 5.30338C12.2579 4.98935 8.82584 7.94526 8.52185 11.9056C8.21786 15.8659 11.1571 19.331 15.0868 19.645C19.0165 19.959 22.4486 17.0031 22.7526 13.0428Z" fill="white"/>
                                        <path d="M15.8746 7.5682L17.2585 10.4188C17.281 10.4659 17.3259 10.4986 17.3762 10.5064L20.4813 10.9744C20.6098 10.994 20.6606 11.1543 20.5674 11.2449L18.3162 13.4515C18.2796 13.4877 18.2628 13.5406 18.271 13.5916L18.7958 16.7211C18.8176 16.851 18.6832 16.9487 18.5676 16.8874L15.7924 15.4004C15.7464 15.3762 15.6916 15.3754 15.6462 15.3999L12.8656 16.8655C12.7499 16.926 12.616 16.8266 12.6384 16.6978L13.1749 13.5723C13.1834 13.5204 13.1667 13.4683 13.1297 13.4315L10.8863 11.2076C10.7933 11.116 10.8446 10.9561 10.9738 10.9374L14.0797 10.4927C14.1304 10.4856 14.1748 10.4527 14.1976 10.4065L15.5916 7.56612C15.649 7.449 15.8151 7.4492 15.8724 7.5674L15.8746 7.5682Z" fill="#F0AA00"/>
                                        </svg>
                                    </div>
                                    <div class="text">
                                        Reward Points: <span>${user.rewards.totalpoints}</span> 
                                    </div>
                                </div>

                                <div class="right">
                                
                                        ${
                                            (safarObject.isUserTeacher || user.ID == safarObject.user_id || safarObject.isUserAdmin) ? `
                                            <button type="button" class="btn-view-rewards-history" data-userid="${user.ID}">View Rewards History</button>
                                            `:``
                                        }
                                    
                                </div>
                            </div>
                        `:
                        `` 
                        }
                    </div>
                `;



                $(`.bb-action-popup .modal-container`).html(userInfoTemplate);
                $(".quranic-animal-count").html(`${earnedQa}/${totalQa}`);
                $(".badges-count").html(`${earnedBadges}/${totalBadges}`);

                $(".profile-quranic_animal_list, .badges-earned__list").slick({
                    dots: false,
                    infinite: false,
                    speed: 300,
                    slidesToShow: 5,
                    slidesToScroll: 1,
                    prevArrow: `<button class="slick-prev"><svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle r="18.5" transform="matrix(-1 0 0 1 20 20)" fill="#F2A952" stroke="#EBCE99" stroke-width="3"/>
                    <path d="M22.5 25L17.5 20L22.5 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    
                                </button>`,
                    nextArrow: `<button class="slick-next"><svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="20" cy="20" r="18.5" fill="#F2A952" stroke="#EBCE99" stroke-width="3"/>
                    <path d="M17.5 25L22.5 20L17.5 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    
                    </button>`,
                    variableWidth: true,
                    responsive: [
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                            }
                        },
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: 4,
                            }
                        }
                    ]
                });


                new jBox('Tooltip', {
                    attach: '.with-tooltip',
                    trigger: 'mouseenter',
                    onOpen: function () {
                        //this.source.data('clicked', (this.source.data('clicked') || 0) + 1);
                        var popContent = this.source.parent().find('.qa-description-popup-content').html();
                        var popClass = this.source.parent().find('.qa-description-popup-content').attr("data-class")
                        this.setContent(`<div class="qa-description-popup active ${popClass}">${popContent}</div>`);
                    },
                    position: {
                        x: 'center',
                        y: 'bottom'
                    },
                    fixed: true
                });

            }).catch( e => {
                console.log("error getUserInfo ", e )
            });
        }, // profilePopup

        subscription : {

            /* 
            // Check first if user is a parent and has subscription

            Modal  = Day 4 , 5, 6 (Displayed Once only)
            Option 4 = Day 7 (Triggered via automation)
            Option 3 = Day 10 - 14 (See fathom RE: turning more red as they approach day 14)
            */

            notifications: {
                header: (d) => { // aka option 3
                    let tpl = ``;
                    let daysLeft = d.days_left;
                    
                    let borderColor = "#E7AC61";
                    let bgColor = "#FDF7EF";
                    switch(daysLeft){
                        case 0:
                            borderColor = "#E46156";
                            bgColor = "#FDDAD7";
                            break;
                        case 1:
                            borderColor = "#E57A59";
                            bgColor = "#FDE4DF";
                            break;
                        case 2:
                            borderColor = "#E7935D";
                            bgColor = "#FDEDE7";
                            break;
                    }

                    tpl += `
                        <div class="add-payment-header" style="border-color:${borderColor}; background-color:${bgColor}">
                            <span class="close">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 1L1 11M1 1L11 11" stroke="${borderColor}" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div class="main-content">
                                <div class="icon">
                                    <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M25.1393 19.5059L15.3993 1.56585C15.1414 1.09207 14.7606 0.696569 14.2968 0.421004C13.8331 0.145438 13.3037 0 12.7643 0C12.2249 0 11.6954 0.145438 11.2317 0.421004C10.768 0.696569 10.3871 1.09207 10.1293 1.56585L0.379271 19.5059C0.124076 19.9639 -0.00660397 20.4809 0.000256878 21.0052C0.00711772 21.5295 0.15128 22.0428 0.418373 22.4941C0.685467 22.9453 1.06616 23.3186 1.52251 23.5769C1.97886 23.8351 2.49493 23.9692 3.01927 23.9659H22.4993C23.0193 23.9663 23.5306 23.8316 23.9828 23.5749C24.4351 23.3182 24.8129 22.9484 25.0791 22.5016C25.3453 22.0549 25.4909 21.5466 25.5014 21.0267C25.5119 20.5068 25.3872 19.993 25.1393 19.5359V19.5059ZM11.2693 6.70585C11.2693 6.30803 11.4273 5.9265 11.7086 5.64519C11.9899 5.36389 12.3714 5.20585 12.7693 5.20585C13.1671 5.20585 13.5486 5.36389 13.8299 5.64519C14.1112 5.9265 14.2693 6.30803 14.2693 6.70585V13.5959C14.2693 13.9937 14.1112 14.3752 13.8299 14.6565C13.5486 14.9378 13.1671 15.0959 12.7693 15.0959C12.3714 15.0959 11.9899 14.9378 11.7086 14.6565C11.4273 14.3752 11.2693 13.9937 11.2693 13.5959V6.70585ZM12.8093 20.2159C12.4691 20.2159 12.1365 20.115 11.8537 19.926C11.5708 19.737 11.3504 19.4684 11.2202 19.1541C11.09 18.8398 11.056 18.4939 11.1223 18.1603C11.1887 17.8267 11.3525 17.5202 11.593 17.2796C11.8336 17.0391 12.1401 16.8753 12.4737 16.8089C12.8074 16.7425 13.1532 16.7766 13.4675 16.9068C13.7818 17.037 14.0504 17.2574 14.2394 17.5403C14.4284 17.8231 14.5293 18.1557 14.5293 18.4959C14.5293 18.952 14.3481 19.3895 14.0255 19.7121C13.7029 20.0346 13.2654 20.2159 12.8093 20.2159Z" 
                                        fill="${borderColor}"/>
                                    </svg>
                                </div>
                                <div class="details">
                                    <h5>Add payment details</h5>
                                    <div>You still have ${daysLeft} days left on your 14-day trial. To continue your seamless learning experience after the trial
                                        <br/>period, you must add your payment details.</div>
                                </div>
                                <div class="button-container">
                                    <a target="_blank" href="/?action=safarpublications_sso_login" type="button">Add Payment Details</a>
                                </div>
                            </div>
                        </div>
                    `

                    $("#main.site-main").prepend(tpl)
                },

                popUp: ( d ) => { // aka option 2 Day 4 , 5, 6 (Displayed Once only)
                    $("#add-payment-details-popup .description").html(`
                    Enjoy your free 14-day trial until ${d.trialEndDate}.
                    To continue your seamless learning experience after the trial period, we kindly ask you to add your payment details. By doing so, you ensure uninterrupted access to our comprehensive Islamic curriculum.
                    `);
                    $("#add-payment-details-popup").show().css({"display":"flex"})
                },

                buddyboss: (notifications) => {
                    let notificationsTpl = ``;
                    notifications.map( notif => {
                        notificationsTpl += `
                            <li>
                                <a href="${notif.url}" target="_blank">
                                    <div class="top">
                                        <h5>${notif.title}</h5>
                                        <svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.33123 7.33123C9.00835 5.65411 9.00835 2.93496 7.33123 1.25784C5.65411 -0.41928 2.93496 -0.41928 1.25784 1.25784C-0.41928 2.93496 -0.41928 5.65411 1.25784 7.33123C2.93496 9.00835 5.65411 9.00835 7.33123 7.33123Z" fill="#EF746F"/>
                                        </svg>
                                    </div>
                                    <div class="content">${notif.content}</div>
                                </a>
                            </li>
                        `                      
                    })
                    let headerTpl = `
                        <div class="overlay-buddyboss-notif"></div>
                        <div class="buddyboss-custom-notifications">
                            <div class="title">NOTIFICATIONS</div>
                            <div class="content">
                                <ul>
                                    ${notificationsTpl}
                                </ul>
                            </div>
                        </div>
                    `;

                    $("#header-aside").prepend(headerTpl);
                },

                paymentDetailsUpdate: () => {
                    $("#payment-details-updated").show().css({"display":"flex"})
                }
            },

            getStatus: () => {
                // call ajax here, show notification based on subscription status

                if( Safar.userInfo.is_parent ){

                    if(Safar.userInfo.has_added_payment_method){
                        Safar.subscription.notifications.paymentDetailsUpdate();
                    }else{

                        if( Safar.userInfo.add_payment_notification ){
                            switch(Safar.userInfo.notification_type){
                                case "modal":
                                    Safar.subscription.notifications.popUp({trialEndDate: moment(Safar.userInfo.trial_end_date).format('MMMM D, YYYY') });
                                    break;
                                case "header":
                                    Safar.subscription.notifications.header({"days_left": Safar.userInfo.days_left});
                                    break;
                                case "buddyboss":
                                    Safar.subscription.notifications.buddyboss(Safar.userInfo.buddyboss_notifications);
                                    break;
                            }
                        }
                    }
                }
                
                //Safar.subscription.notifications.popUp();
                //Safar.subscription.notifications.bottomHeader({"days_left":5});
                
                console.log("Safar.userInfo", Safar.userInfo.is_parent, Safar.userInfo.add_payment_notification)
            }
        },

        rewardsNotification: () => {

            console.log("SafarUser userInfo", Safar.userInfo.rewards.rewards_ungrouped)
            let countUnread = 0;
            let rewardNotfTpl = ``;
            let negativeNotifTpl = ``;
            let totalPositivePoints = 0;
            let totalNegativePoints = 0;
            Safar.userInfo.rewards.rewards_ungrouped.map( reward => {
                if(reward.read_notification == 0){
                    console.log("reward", reward)
                    countUnread++;
                    if(reward.type=="needs-work"){
                        totalNegativePoints -= parseInt(reward.points);
                        negativeNotifTpl += `<div>${reward.points} points for ${reward.achievement_details.title}</div>`;
                    }else{
                        totalPositivePoints += parseInt(reward.points);
                        rewardNotfTpl += `<div>${reward.points} points for ${reward.achievement_details.title}</div>`;
                    }
                    Safar.rewardsNotifIds.push(reward.id)
                }
            })
            

            let notificatonTpl = `
            <div class="rewards-notification-container">
                <div class="rewards-notification positive" style="display:none">
                    <div class="close">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="#5D53C0"/>
                            <rect width="19.2" height="19.2" transform="translate(10.3999 10.4004)" fill="#5D53C0"/>
                            <path d="M24.7997 15.2004L15.1997 24.8004" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.1997 15.2004L24.7997 24.8004" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
            
                    <div class="content">
                        <div class="points-total">+${totalPositivePoints}</div>
                        <div class="text">
                            <h2>Great Work!</h2>
                            ${rewardNotfTpl}
                            <div>Let these points fuel your motivation to keep aiming high.</div>
                        </div>
            
                        <a href="/achievements/?tab=class-points" class="view-points">View Points</a>
                    </div>
                </div>

                <div class="rewards-notification needs-work" style="display:none">
                    <div class="close">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="#5D53C0"/>
                            <rect width="19.2" height="19.2" transform="translate(10.3999 10.4004)" fill="#5D53C0"/>
                            <path d="M24.7997 15.2004L15.1997 24.8004" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.1997 15.2004L24.7997 24.8004" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
            
                    <div class="content">
                        <div class="points-total">-${totalNegativePoints}</div>
                        <div class="text">
                            <h2>Oh no! Points Deducted</h2>
                            ${negativeNotifTpl}
                            <div> No worries, setbacks happen. Use this as fuel to rise higher!</div>
                        </div>
            
                        <a href="/achievements/?tab=class-points" class="view-points">View Points</a>
                    </div>
                </div>
            </div>
            `

            if(countUnread > 0){
                if($(".rewards-notification").length <= 0){
                    $("#masthead > .site-header-container").append(notificatonTpl);
                    $("#masthead > .site-header-container .rewards-notification").fadeIn();
                }
            }
        },

        clearRewardsNotifications: () => {
            $.ajax({
                url: `${safarObject.apiBaseurl}/rewards/clear`,
                type: "PUT",
                data:{
                    ids: Safar.rewardsNotifIds
                },
                headers: {
                    "X-WP-Nonce": safarObject.wpnonce
                },
                dataType: "json",
                beforeSend: (xhr) => {
                    
                },
                success: (d) => {
                    
                },
                error: (d) => {
                }
            });
        }
    }

    Safar.getUserInfo({disabledachievements:true,userid:safarObject.user_id}).then( d => {
        // update page header 
        Safar.userInfo = d;
        console.log("user/userId", d)

        $(".header-right .header-right__book .practice-log-count").html(d.practice_logs_count);
        $(".header-right .header-right__book .sessions").html(`Only ${d.practice_sessions_left} sessions left until you can unlock your next Quranic Animal!`);

        //console.log(d.latest_quranic_animal, d.latest_quranic_animal.image);
        //if(d.latest_quranic_animal !== false){
        //console.log("test", d.latest_quranic_animal.image)
        //$(".quranic-content img").attr({"src":d.latest_quranic_animal.image, "data-test":"test"})
        //}

        /* Triggers and scripts for "Add Payment Details" SafarPub/J2J subscription */
        Safar.subscription.getStatus();

        /* Trigger Rewards Notification*/
        Safar.rewardsNotification();
        setTimeout( e => {
            Safar.rewardsNotification();
        }, 20000)
   
   }).catch( e => {
       console.log("ERROR Safar.getUserInfo")
   });
   
   
})



$(document).on("click", "#quranic-animal-spin svg", e => {
   $("#quranic-animal-spin svg").toggleClass('spin-animation');
   Safar.practiceTracker.quranicSpin.showAwardedAnimal();
});

$(document).on("click",".rewards-notification .close", e => {
    $(e.currentTarget).parent().fadeOut();

    Safar.clearRewardsNotifications();
})

jQuery(document).ready( $ => {
      

    setTimeout( () => {
        Safar.practiceTracker.quranicSpin.pendingAwards()
            .then( data => {
                console.log("pendingAwards", data )
                if(data.show_spin){
                    Safar.practiceTracker.quranicSpin.type = data.type;
                    Safar.practiceTracker.quranicSpin.showModal();
                }
            })
            .catch( e => {
                console.log("pending awards error ", e)
            })
    }, 3000)

    
    $(document).on("click",".add-payment-header .close", e => {
        $(".add-payment-header").fadeOut();
    })

    $(document).on("click","#add-payment-details-popup .close", e => {
        $.ajax({
            url: `${safarObject.ajaxurl}`,
            data: {
                action: "hide_payment_details_popup",
            },
            dataType: "json",
            beforeSend: () => {
            },
            success: (d) => {
                
            },
        });
    })  

    $(document).on("click","#payment-details-updated .close, #payment-details-updated a ", e => {
        $.ajax({
            url: `${safarObject.ajaxurl}`,
            data: {
                action: "hide_payment_details_updated",
            },
            dataType: "json",
            beforeSend: () => {
            },
            success: (d) => {
                
            },
        });
    })

    //console.log("test123test312")  
})



// event listener for activity avatar profile popup
$(document).on("click",".activity-avatar.item-avatar a img.avatar", e => {
    e.preventDefault();
    $('.bb-view-profile.bb-action-popup').show();
    let userId = $(e.currentTarget).attr("data-userid");
    Safar.profilePopup(userId); 
})

$(document).on("click",".custom-profile-popup", e => {
    e.preventDefault();
    let userId = $(e.currentTarget).attr("class").replace("custom-profile-popup","").replace("userid-","").replace(" ","");
    $('.bb-view-profile.bb-action-popup').show();
    Safar.profilePopup(userId); 
})
$(document).on("click",".global-profile-popup .bb-model-header", e => {
    $('.bb-view-profile.bb-action-popup').fadeOut();
});

$(document).on("click","#wp-admin-bar-my-account-xprofile a, .bb-profile-grid .item-body .entry-header a.push-right", e => {
    let profileText = $(e.currentTarget).text();
    //console.log("profileText.toLowerCase()", profileText.toLowerCase().replace(/\s+/g, ''))

    if(profileText.toLowerCase().replace(/\s+/g, '') == "profile" || profileText.toLowerCase().replace(/\s+/g, '') == "viewmyprofile"){
        e.preventDefault();
        $('.bb-view-profile.bb-action-popup').show();
        Safar.profilePopup(safarObject.user_id); 
    }
    
})



$(document).on("click",".bs-group-member a, .activity-post-avatar, .user-list ul li > a, .sub-menu-inner li > a.user-link", e => {
    
    isEllipsis = $(e.currentTarget).find('.bb-icon-ellipsis-h').length;
    if(isEllipsis <= 0){
        e.preventDefault();
        href = $(e.currentTarget).attr("href").replace(safarObject.bbrootdomain,"");
        let username = href.replace("/","").replace("/","");
        let elementContainer = $(e.currentTarget);
        $.ajax({
            url: `${safarObject.ajaxurl}`,
            data: {
                action: "get_user_by_username",
                username
            },
            dataType: "json",
            beforeSend: () => {
                elementContainer.fadeTo("fast",.3);
            },
            success: (d) => {
                elementContainer.fadeTo("fast",1);
                if(d.exists){
                    $('.bb-view-profile.bb-action-popup').show();
                    Safar.profilePopup(d.user_id); 
                }
            },
            
        });
    }
});


$(document).on("click",".overlay-buddyboss-notif", e => {
    $(".overlay-buddyboss-notif, .buddyboss-custom-notifications").remove();
})

function pbdCheckDate(dateStr) {
    const currentDate = moment().startOf('day');
    const inputDate = moment(dateStr, 'YYYY-MM-DD');
  
    if (inputDate.isSame(currentDate, 'day')) {
      return 'Today';
    } else if (inputDate.isSame(currentDate.clone().subtract(1, 'day'), 'day')) {
      return 'Yesterday';
    } else {
      return inputDate.format('ddd Do MMM');
    }
}
    
rewardsTpl = (reward) => {
    return ` <div class="item ${reward.type}"> 
        <div class="image"><img src="${reward.achievement_details.image}"/></div>
        <div class="details">
            <div class="title">
                ${reward.achievement_details.title}
                <div class="comment">${reward.comment}</div>
            </div>
            <div class="teacher-classroom">
                <img src="${reward.teacher_details.avatar}"/>
                <span>${reward.teacher_details.firstName} ${reward.teacher_details.lastName}</span>
                <svg width="6" height="6" viewBox="0 0 6 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="3" cy="3" r="3" fill="#37394A"/>
                </svg>
                <span>${reward.teacher_details.classroom}</span>
            </div>
        </div>
        <div class="points ">
            ${(reward.type=="positive") ? `+${reward.points}`:`${reward.points}`}
        </div>
    </div> `
}

$(document).on("click",".btn-view-rewards-history", e => {
    userid = $(e.currentTarget).attr("data-userid");
    $(".bb-close-view-profile").trigger("click");
    studentDetails = {};
    Safar.users.map( user => {
        if(user.ID == userid) studentDetails = user;
    })
    $("#rewards-history").fadeIn().css("display","flex")

    let rewardsListTpl = ``;


    // Get today's date without time
    const today = moment().startOf('day');
    // Get yesterday's date without time
    const yesterday = moment().subtract(1, 'day').startOf('day');

    // Group the objects based on the date
    const localGroupedRewards = studentDetails.rewards.rewards_ungrouped.reduce((result, object) => {
        const objectDate = moment.utc(object.datetime).local();
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

    console.log("localGroupedRewards", localGroupedRewards)


    rewardsListTpl += `<div class="reward-group">`
        if(localGroupedRewards.today.length > 0 ){
            rewardsListTpl += `<div class="date">Today</div>`
            rewardsListTpl += `<div class="items">`;
                localGroupedRewards.today.map( reward => {
                    rewardsListTpl += rewardsTpl(reward);
                })
            rewardsListTpl += `</div>`;
        }

        if(localGroupedRewards.yesterday.length > 0 ){
            rewardsListTpl += `<div class="date">Yesterday</div>`
            rewardsListTpl += `<div class="items">`;
                localGroupedRewards.yesterday.map( reward => {
                    rewardsListTpl += rewardsTpl(reward);
                })
            rewardsListTpl += `</div>`;
        }

        if(localGroupedRewards.prior.length > 0 ){

            localGroupedRewards.prior.map( rewards => {
                var dateText = pbdCheckDate(rewards.date);
                rewardsListTpl += `<div class="date">${dateText}</div>`
                rewardsListTpl += `<div class="items">`;
                rewards.objects.map( reward => {
                    rewardsListTpl += rewardsTpl(reward);
                })
                rewardsListTpl += `</div>`;
            } )

        }

    rewardsListTpl += `</div>`

    let historyTpl = `

    <div class="top">
        <div class="text">Rewards History </div>
        <div class="buttons">
            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.37901 24.6635L11.8346 16.3264L7.34352 14.4287L3.88867 22.7657L6.7937 22.123L8.37901 24.6635Z" fill="#EF746F"/>
            <path d="M14.4208 24.8095L11.3311 16.3149L15.8999 14.6348L18.9888 23.1287L16.1148 22.3471L14.4208 24.8095Z" fill="#EF746F"/>
            <path d="M19.2819 10.8107L20.4573 9.83579L19.33 8.80006L20.2139 7.55088L18.8604 6.84437L19.3928 5.40603L17.904 5.07667L18.0494 3.54802L16.5273 3.61775L16.2753 2.10259L14.8234 2.56694L14.1912 1.16815L12.909 1.99531L11.9388 0.80957L10.9141 1.94289L9.67302 1.0496L8.97401 2.41334L7.54666 1.8731L7.22198 3.37317L5.7051 3.22366L5.77721 4.75801L4.27457 5.00983L4.73796 6.47339L3.35229 7.10866L4.1753 8.40188L3 9.37759L4.12724 10.4133L3.24337 11.6625L4.59684 12.369L4.06438 13.8066L5.55326 14.1367L5.40785 15.6654L6.92998 15.5956L7.18193 17.1108L8.63384 16.6464L9.26609 18.0452L10.5483 17.2181L11.5177 18.4039L12.5432 17.2705L13.7843 18.1638L14.4833 16.8L15.9106 17.3403L16.2353 15.8402L17.7521 15.9889L17.6801 14.4554L19.1827 14.2035L18.7193 12.74L20.105 12.1047L19.2819 10.8107Z" fill="#F0AA00"/>
            <path d="M17.0644 10.0321C17.2924 7.06185 15.088 4.46306 12.1407 4.22754C9.19344 3.99201 6.61938 6.20894 6.39139 9.17919C6.1634 12.1494 8.36781 14.7482 11.3151 14.9837C14.2624 15.2193 16.8364 13.0023 17.0644 10.0321Z" fill="white"/>
            <path d="M11.9062 5.92566L12.9441 8.06358C12.961 8.09892 12.9947 8.12349 13.0324 8.1293L15.3612 8.48031C15.4576 8.49498 15.4957 8.61522 15.4258 8.68322L13.7374 10.3382C13.7099 10.3653 13.6973 10.405 13.7035 10.4432L14.0971 12.7904C14.1135 12.8878 14.0127 12.9611 13.9259 12.915L11.8445 11.7998C11.81 11.7817 11.7689 11.7811 11.7349 11.7995L9.64948 12.8987C9.56267 12.944 9.46225 12.8694 9.47905 12.7728L9.88145 10.4287C9.88782 10.3898 9.87526 10.3508 9.84749 10.3231L8.165 8.65522C8.09525 8.58648 8.1337 8.4666 8.23058 8.45259L10.56 8.11907C10.598 8.11371 10.6313 8.08905 10.6485 8.0544L11.6939 5.9241C11.737 5.83626 11.8616 5.83641 11.9046 5.92506L11.9062 5.92566Z" fill="#F0AA00"/>
            </svg>
            <span>Reward Points: <span class="points">${studentDetails.rewards.totalpoints}</span></span> 
        </div>
    </div>
    <div class="main">
        
        <div class="left">
            <div class="student-details">
                <h5>${studentDetails.first_name} ${studentDetails.last_name}</h5>
                <img src="${studentDetails.avatar_full}"/>
            </div>
        </div>
        <div class="right">
            ${rewardsListTpl}
        </div>
    </div>
    
    `;


    $("#rewards-history .modal-content .content").html( historyTpl );

})