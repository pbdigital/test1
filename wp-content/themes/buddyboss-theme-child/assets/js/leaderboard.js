let SafarLeaderBoard = {}

SafarLeaderBoard = {
    quizid : 0,
    ranking: {},
    
    loadLeaderBoard: () => {
        //skeleton loader top

        $(".leaderboard-top").html(`
            <div class="leaderboard-top__rank">
                <div class="skeleton-loader" style="width:140px; height:140px; border-radius:50%"></div>
                <div class="leaderboard-top__name"> <div style="width:100px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-top__pts"> <div style="width:80px; height:24px" class="skeleton-loader" ></div> </div>
            </div>
            <div class="leaderboard-top__rank">
                <div class="skeleton-loader" style="width:140px; height:140px; border-radius:50%"></div>
                <div class="leaderboard-top__name"> <div style="width:100px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-top__pts"> <div style="width:80px; height:24px" class="skeleton-loader" ></div> </div>
            </div>
            <div class="leaderboard-top__rank">
                <div class="skeleton-loader" style="width:140px; height:140px; border-radius:50%"></div>
                <div class="leaderboard-top__name"> <div style="width:100px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-top__pts"> <div style="width:80px; height:24px" class="skeleton-loader" ></div> </div>
            </div>
        `);

        $(".leaderboard-ranking").html(`
            <div class="leaderboard-ranking__row">
                <div class="leaderboard-ranking__rank"> <div style="width:24px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-ranking__user"> <div class="skeleton-loader" style="width:40px; height:40px; border-radius:50%"></div>  <div style="width:100px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-ranking__pts">  <div style="width:100px; height:24px" class="skeleton-loader" ></div> </div>
            </div>
             
            <div class="leaderboard-ranking__row">
                <div class="leaderboard-ranking__rank"> <div style="width:24px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-ranking__user"> <div class="skeleton-loader" style="width:40px; height:40px; border-radius:50%"></div>  <div style="width:100px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-ranking__pts">  <div style="width:100px; height:24px" class="skeleton-loader" ></div> </div>
            </div>

            <div class="leaderboard-ranking__row">
                <div class="leaderboard-ranking__rank"> <div style="width:24px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-ranking__user"> <div class="skeleton-loader" style="width:40px; height:40px; border-radius:50%"></div>  <div style="width:100px; height:24px" class="skeleton-loader" ></div> </div>
                <div class="leaderboard-ranking__pts">  <div style="width:50px; height:24px" class="skeleton-loader" ></div> </div>
            </div>
        `)

        Safar.leaderboard.quiz(SafarLeaderBoard.quizid)
            .then( rankings => {
                
                // .leaderboard-top
                let top = 0;
                let topLeaderBoard = ``
                let nextLeaderBoard = ``
                rankings.map( rank => {

                    let isYouClass = "";
                    if(rank.is_you){
                        rank.display_name = "You";
                        isYouClass = "leaderboard-ranking__you";
                    }

                    if(top < 3){
                        topLeaderBoard += `
                            <div class="leaderboard-top__rank">
                                
                                ${(rank.rank==1) ? `<img class="crown" src="${safarObject.stylesheet_directory}/assets/img/crown.svg" alt="">` : ``} 
                                <img src="${rank.avatar}" alt="">
                                <div class="leaderboard-top__num">${rank.rank}</div>
                                <div class="leaderboard-top__name">${rank.display_name}</div>
                                <div class="leaderboard-top__pts">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="16" cy="16" r="16" fill="#F2A952"/>
                                    <path d="m23.483 13.38-4.797-.697-2.144-4.347a.607.607 0 0 0-1.084 0l-2.144 4.347-4.797.697a.604.604 0 0 0-.334 1.032l3.47 3.383-.82 4.778a.604.604 0 0 0 .877.636L16 20.954l4.29 2.255a.604.604 0 0 0 .877-.637l-.82-4.777 3.47-3.383a.602.602 0 0 0-.334-1.031Z" fill="#fff"/></svg> 
                                    <span>${rank.points}</span>
                                </div>
                            </div>
                        `
                    }else{
                        nextLeaderBoard += `
                            <div class="leaderboard-ranking__row ${isYouClass}">
                                <div class="leaderboard-ranking__rank">${rank.rank}</div>
                                <div class="leaderboard-ranking__user"><img src="${rank.avatar}" alt=""> <span>${rank.display_name}</span></div>
                                <div class="leaderboard-ranking__pts">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="16" cy="16" r="16" fill="#F2A952"/><path d="m23.483 13.38-4.797-.697-2.144-4.347a.607.607 0 0 0-1.084 0l-2.144 4.347-4.797.697a.603.603 0 0 0-.334 1.032l3.47 3.383-.82 4.778a.604.604 0 0 0 .877.636L16 20.954l4.29 2.255a.604.604 0 0 0 .877-.637l-.82-4.777 3.47-3.383a.602.602 0 0 0-.334-1.031Z" fill="#fff"/></svg> 
                                    ${rank.points}
                                </div>
                            </div>
                        `
                    }
                    top++;
                });
               
                if (top === 1) {
                    topLeaderBoard += `
                        <div class="leaderboard-top__rank">
                        </div>
                        <div class="leaderboard-top__rank">
                        </div>
                    `
                }
                if (top === 2) {
                    topLeaderBoard += `
                        <div class="leaderboard-top__rank">
                        </div>
                    `
                }

                $(".leaderboard-top").html(topLeaderBoard);
                $(".leaderboard-ranking").html(nextLeaderBoard);

            })
            .catch( e => {

            });
    },

    init : () => {
        SafarLeaderBoard.loadLeaderBoard();
    }

}