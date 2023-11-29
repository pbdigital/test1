<div class="leaderboard">
    <div class="leaderboard-items">
        <div class="leaderboard-item leaderboard-points">
            <div class="leaderboard-items__header">
                Points Leaderboard
            </div>
            <div class="leaderboard-top__filter">
                <a href="#" class="filter-menu points-filter">
                    <span>
                        <span>This Month</span>
                        <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m1.5 1 6 6 6-6" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <ul>
                        <li data-type="this month" data-rank="points">This Month</li>
                        <li data-type="academic year" data-rank="points">This Academic Year</li>
                    </ul>
                </a>
            </div>
            <div class="leaderboard-top">
                
            </div>
            <div class="leaderboard-ranking">
                <div class="leaderboard-ranking__headings">
                    <div class="leaderboard-ranking__headings-item">
                        Rank
                    </div>
                    <div class="leaderboard-ranking__headings-item">
                        Student
                    </div>
                    <div class="leaderboard-ranking__headings-item">
                        Points
                    </div>
                </div>
                <div class="leaderboard-ranking__list">
                     
                </div>
            </div>
        </div>
        <div class="leaderboard-item leaderboard-practice">
            <div class="leaderboard-items__header">
                Practice Leaderboard
            </div>
            <div class="leaderboard-top__filter">
                <a href="#" class="filter-menu practices-filter">
                    <span>
                        <span>Practices</span>
                        <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m1.5 1 6 6 6-6" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <ul>
                        <li>Practices</li>
                        <li>Streak</li>
                    </ul>
                </a>
                <a href="#" class="filter-menu practice-filter">
                    <span>
                        <span>This Month</span>
                        <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m1.5 1 6 6 6-6" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <ul>
                        <li data-type="this month" data-rank="practice">This Month</li>
                        <li data-type="academic year" data-rank="practice">This Academic Year</li>
                    </ul>
                </a>
            </div>
            <div class="leaderboard-top">
                 
            </div>
            <div class="leaderboard-ranking">
                <div class="leaderboard-ranking__headings practice-leaderboard-heading">
                    <div class="leaderboard-ranking__headings-item">
                        Rank
                    </div>
                    <div class="leaderboard-ranking__headings-item">
                        Student
                    </div>
                    <div class="leaderboard-ranking__headings-item points">
                        Practices
                    </div>
                </div>
                <div class="leaderboard-ranking__list">
                   
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        let practiceTrackerType = "this month";
        let pointsType = "this month";
        let practiceLeaderboard = "practices";


        $(document).on("click", ".filter-menu", e => {
            e.preventDefault();
            e.stopPropagation();
            let $this = $(e.currentTarget);       

            // hide all open dropdowns      
            $('.filter-menu ul').hide();
            $('.filter-menu').not($this).removeClass('active');

            $this.toggleClass('active');
            if($this.hasClass('active')) {    
                
                $this.find('ul').show();

            } else {                     
                $this.find('ul').hide();
            }         
        });   



        $(document).on("click", ".filter-menu li", e => {
        
            let $this = $(e.currentTarget);     
            let option = $this.attr('data-val');
            let option_name = $this.text();

            page = 1;
            
            if($this.hasClass('optsort')) {            
            
                sortby = option;
                $(".filter-menu.sortby span").text(option_name).attr("data-val",$this.attr("data-val"));
            }            
            else if($this.hasClass('opttype')) {            
                $('.opttype').removeClass('disabled');
                $this.addClass('disabled');
                resource_type = option;
                $(".filter-menu.types-menu span").text(option_name).attr("data-val",$this.attr("data-val"));
            }           
            else if($this.hasClass('opttopic')) {           
                $('.opttopic').removeClass('disabled');
                $this.addClass('disabled');
                resource_topic = option;
                $(".filter-menu.topics-menu span").text(option_name).attr("data-val",$this.attr("data-val"));
            }
            else if($this.hasClass("optskill")){
                $('.optskill').removeClass('disabled');
                $this.addClass('disabled');
                resource_topic = option;
                $(".filter-menu.skill-menu span").text(option_name).attr("data-val",$this.attr("data-val"));
            }

            console.log("option name", option_name)
            /*
            <li data-type="this month" data-rank="practice">This Month</li>
            <li data-type="academic year" data-rank="practice">This Academic Year</li>
                */
            if($this.attr("data-rank") == "practice"){
                practiceTrackerType = $this.attr("data-type")
                $(".filter-menu.practice-filter span span").html(option_name)
                loadPracticeTrackerLeaderBoard();
            }

            
            if( $this.attr("data-rank") == "points"){
                //filter-menu points-filter
                pointsType = $this.attr("data-type")
                console.log("pointsType", pointsType)
                $(".filter-menu.points-filter span span").html(option_name)
                loadPointsLeaderBoard();
            }
            
            if( option_name.toLowerCase() == "practices" || option_name.toLowerCase() == "streak" ){
                practiceLeaderboard = option_name.toLowerCase();
                $(".filter-menu.practices-filter span span").html(option_name)
                loadPracticeTrackerLeaderBoard();
            }
        });  

        $(document).on("click", e => {
            // hide all open dropdowns      
            $('.filter-menu').removeClass('active');
            $('.filter-menu ul').hide();        
        });


        // skeleton loader for the rankigns
        

        loadPointsLeaderBoard = function(){
            $(".leaderboard-points .leaderboard-top").html(`
                <div class="leaderboard-top__item">
                    <div class="skeleton-loader" style="width:100px; height:100px; border-radius:140px"></div>
                    <div class="skeleton-loader" style="width:116px; height:24px; margin-top:10px"></div>
                    <div class="pts" style="margin-top:5px"><div class="skeleton-loader" style="width:35px; height:25px;  margin-right:5px"></div> <div class="skeleton-loader" style="width:65px; height:25px"></div></div>
                </div>
                <div class="leaderboard-top__item">
                    <div class="skeleton-loader" style="width:80px; height:80px; border-radius:140px"></div>
                    <div class="skeleton-loader" style="width:116px; height:24px; margin-top:10px"></div>
                    <div class="pts" style="margin-top:5px"><div class="skeleton-loader" style="width:35px; height:25px;  margin-right:5px"></div> <div class="skeleton-loader" style="width:65px; height:25px"></div></div>
                </div>
                <div class="leaderboard-top__item">
                    <div class="skeleton-loader" style="width:80px; height:80px; border-radius:140px"></div>
                    <div class="skeleton-loader" style="width:116px; height:24px; margin-top:10px"></div>
                    <div class="pts" style="margin-top:5px"><div class="skeleton-loader" style="width:35px; height:25px;  margin-right:5px"></div> <div class="skeleton-loader" style="width:65px; height:25px"></div></div>
                </div>
            ` )

            let skeletonLoaders = ``;
            for(i = 0; i < 3; i++){
                skeletonLoaders += `
                <div class="leaderboard-ranking__row">
                    <div class="leaderboard-ranking__rank">
                        <div class="skeleton-loader" style="width:35px; height:23px; "></div>
                    </div>
                    <div class="leaderboard-ranking__student">
                        <div class="skeleton-loader" style="width:106px; height:23px;  margin-right:5px"></div>
                    </div>
                    <div class="leaderboard-ranking__pts">
                        <div class="skeleton-loader" style="width:36px; height:23px;  margin-right:5px"></div>
                    </div>
                </div>
                `
            }

            $(".leaderboard-points .leaderboard-ranking__list").html(skeletonLoaders);

            Safar.leaderboard.points( {"gid":<?=bp_get_current_group_id()?>,"type":pointsType} )
                .then( rankings => {
                    console.log("rankings", rankings)
                    let topRankings = ``;
                    let crown = ``;
                    let nextRankings = ``;
                    
                    rankCount = 0;
                    rankings.top_rankings.map( ranking => {
                        
                    
                        if(ranking.rank == 1) crown = `<div class="crown"><img src="<?=get_stylesheet_directory_uri();?>/assets/img/members/crownleaderboard.svg" alt=""></div>`
                        else crown = ``;

                        if( ranking.user_id == safarObject.user_id ){
                            ranking.name = "You"
                        }

                        topRankings += `
                        <div class="leaderboard-top__item">
                            ${crown}
                            <img src="${ranking.avatar}" alt="">
                            <span>${ranking.rank}</span>
                            <h3>${ranking.name}</h3>
                            <div class="pts"><img src="<?=site_url();?>/wp-content/uploads/2022/07/coin.png" alt=""> ${ranking.points}</div>
                        </div>
                        `
                        $(".leaderboard-points .leaderboard-top").html(topRankings)
                    
                    

                        rankCount++;
                    })

                    
                    let i =0;
                    if( 3 - rankCount ){
                        for( i = 0; i < ( 3 - rankCount  ); i++ ){
                            topRankings += `
                            <div class="leaderboard-top__item">
                               
                            </div>
                            `
                            $(".leaderboard-points .leaderboard-top").html(topRankings)
                        }
                    }
                    
                    i = 0;
                    

                    rankings.next_rankings.map( ranking => {

                        
                        if(rankings.your_ranking != null){
                            
                            if( ranking.user_id == safarObject.user_id ){
                                ranking = rankings.your_ranking
                            }
                        }
                        
                        //console.log(rankings.belong_to_top, i, ranking)

                        nextRankings += `<div class="leaderboard-ranking__row ${(ranking.is_you) ? `current`:``}">
                            <div class="leaderboard-ranking__rank">
                                ${ranking.rank}
                            </div>
                            <div class="leaderboard-ranking__student">
                            <img src="${ranking.avatar}" alt=""> <span>${ranking.name}</span>
                            </div>
                            <div class="leaderboard-ranking__pts">
                                <img src="<?=site_url();?>/wp-content/uploads/2022/07/coin.png" alt=""><span>${ranking.points}</span>
                            </div>
                        </div>`;

                        i++;

                    });

                    $(".leaderboard-points .leaderboard-ranking__list").html(nextRankings)

                })
                .catch( e => {
                    console.log("error Leaderboard points ", e)
                });
        }
        loadPointsLeaderBoard();


        // start practice tracker
        // skeleton loader for the rankigns
        
        loadPracticeTrackerLeaderBoard = () => {
            $(".leaderboard-practice .leaderboard-top").html(`
                <div class="leaderboard-top__item">
                    <div class="skeleton-loader" style="width:100px; height:100px; border-radius:140px"></div>
                    <div class="skeleton-loader" style="width:116px; height:24px; margin-top:10px"></div>
                    <div class="pts" style="margin-top:5px"><div class="skeleton-loader" style="width:35px; height:25px;  margin-right:5px"></div> <div class="skeleton-loader" style="width:65px; height:25px"></div></div>
                </div>
                <div class="leaderboard-top__item">
                    <div class="skeleton-loader" style="width:80px; height:80px; border-radius:140px"></div>
                    <div class="skeleton-loader" style="width:116px; height:24px; margin-top:10px"></div>
                    <div class="pts" style="margin-top:5px"><div class="skeleton-loader" style="width:35px; height:25px;  margin-right:5px"></div> <div class="skeleton-loader" style="width:65px; height:25px"></div></div>
                </div>
                <div class="leaderboard-top__item">
                    <div class="skeleton-loader" style="width:80px; height:80px; border-radius:140px"></div>
                    <div class="skeleton-loader" style="width:116px; height:24px; margin-top:10px"></div>
                    <div class="pts" style="margin-top:5px"><div class="skeleton-loader" style="width:35px; height:25px;  margin-right:5px"></div> <div class="skeleton-loader" style="width:65px; height:25px"></div></div>
                </div>
            ` )

            skeletonLoaders = ``;
            for(i = 0; i < 3; i++){
                skeletonLoaders += `
                <div class="leaderboard-ranking__row">
                    <div class="leaderboard-ranking__rank">
                        <div class="skeleton-loader" style="width:35px; height:23px; "></div>
                    </div>
                    <div class="leaderboard-ranking__student">
                        <div class="skeleton-loader" style="width:106px; height:23px;  margin-right:5px"></div>
                    </div>
                    <div class="leaderboard-ranking__pts">
                        <div class="skeleton-loader" style="width:36px; height:23px;  margin-right:5px"></div>
                    </div>
                </div>
                `
            }

            $(".leaderboard-practice .leaderboard-ranking__list").html(skeletonLoaders);

            
            Safar.leaderboard.practiceTracker({"gid":<?=bp_get_current_group_id()?>,"type":practiceTrackerType,"leaderboard":practiceLeaderboard})
                .then( rankings => {
                    console.log("leaderboard practice tracker", rankings)

                    $(".practice-leaderboard-heading .points").html(practiceLeaderboard)

                    console.log("rankings", rankings)
                    let topRankings = ``;
                    let crown = ``;
                    let nextRankings = ``;

                    /* 
                    <div class="leaderboard-top__item">
                        <div class="crown"><img src="<?=get_stylesheet_directory_uri();?>/assets/img/members/crownleaderboard.svg" alt=""></div>
                        <img src="<?=site_url();?>/wp-content/uploads/2022/07/face3.png" alt="">
                        <span>1</span>
                        <h3>David Walker</h3>
                        <div class="pts"><img src="<?=site_url();?>/wp-content/uploads/2022/07/book.png" alt=""> 600</div>
                    </div>
                    */
                    
                    rankCount = 0;
                    rankings.top_rankings.map( ranking => {
                        
                        if(ranking.rank == 1) crown = `<div class="crown"><img src="<?=get_stylesheet_directory_uri();?>/assets/img/members/crownleaderboard.svg" alt=""></div>`
                        else crown = ``;

                        if( ranking.user_id == safarObject.user_id ){
                            ranking.name = "You"
                        }

                        if( practiceLeaderboard != "streak"){
                            var img = `<?=site_url();?>/wp-content/uploads/2022/07/book.png`;
                        }else{
                            var img = `<?=site_url();?>/wp-content/uploads/2022/11/fire.png`;
                        }

                        topRankings += `
                        <div class="leaderboard-top__item">
                            ${crown}
                            <img src="${ranking.avatar}" alt="">
                            <span>${ranking.rank}</span>
                            <h3>${ranking.name}</h3>
                            <div class="pts"><img src="${img}" alt=""> ${ranking.points}</div>
                        </div>
                        `
                        $(".leaderboard-practice .leaderboard-top").html(topRankings)
                        

                        rankCount++;
                    })

                    if( 3 - rankCount ){
                        for( i = 0; i < ( 3 - rankCount  ); i++ ){
                            topRankings += `
                            <div class="leaderboard-top__item">
                               
                            </div>
                            `
                            $(".leaderboard-practice .leaderboard-top").html(topRankings)
                        }
                    }
                    
                    i = 3;

                    rankings.next_rankings.map( ranking => {

                        
                        console.log("rankings.your_ranking.rank ", rankings.your_ranking.rank  , i, rankings.belong_to_top)

                       
                        if(rankings.your_ranking != null){
                            
                            if( ranking.user_id == safarObject.user_id ){
                                ranking = rankings.your_ranking
                            }
                        }
                        
                        //console.log(rankings.belong_to_top, i, ranking)

                        if( practiceLeaderboard != "streak"){
                            var img = `<?=site_url();?>/wp-content/uploads/2022/07/book.png`;
                        }else{
                            var img = `<?=site_url();?>/wp-content/uploads/2022/11/fire.png`;
                        }

                        nextRankings += `<div class="leaderboard-ranking__row ${(ranking.is_you) ? `current`:``}">
                            <div class="leaderboard-ranking__rank">
                                ${ranking.rank}
                            </div>
                            <div class="leaderboard-ranking__student">
                                <img src="${ranking.avatar}" alt=""> <span>${ranking.name}</span>
                            </div>
                            <div class="leaderboard-ranking__pts">
                                <img src="${img}" alt=""><span>${ranking.points}</span>
                            </div>
                        </div>`;

                        i++;

                    });

                    $(".leaderboard-practice .leaderboard-ranking__list").html(nextRankings)

                })
                .catch( e => {
                    console.log("error practice tracker leaderboard ", e)
                })
        }
        loadPracticeTrackerLeaderBoard();
        
        // end practice tracker
    })
</script>