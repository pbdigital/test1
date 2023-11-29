$ = jQuery;
let SafarCourses = {
    loadedSubjects : false,

    filters: {
        searchkey: $(".input-search").val(),
        status: $("a.filter-menu.status span").attr("data-val"),
        subject: $("a.filter-menu.subject span").attr("data-val"),
        bypasstransient: true
    },

    api : {
        getCourses: async(pars) => {
            return new Promise((resolve, reject) => {
                
                $.ajax({
                    url: `${safarObject.apiBaseurl}/subjects`,
                    data: pars,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    success: (d) => {
                        //biddyEvents.events = d
                        
                        resolve(d);
                        
                    },
                    error: (d) => {
                        reject(`error /subjects`);
                    }
                });
            });
        },

        getCategories: async() => {
            return new Promise((resolve, reject) => {
                
                $.ajax({
                    url: `${safarObject.apiBaseurl}/courses/categories`,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    success: (d) => {
                        //biddyEvents.events = d
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /courses/categories`);
                    }
                });
            })
        }
    },

    displayCourses: () => {
        

        console.log("current filters", SafarCourses.filters)

        let coursesTpl = ``
        let hasCollection = false;

        /* skeleton loader */
        coursesTpl = `
            <div class="courses-wrapper">
                <div class="courses-list">
                    <div class="skeleton-loader" style="width:100px; height:32px;"></div>

                    <div class="courses__slider" style="opacity:1">
                        <div class="courses__slider-item skeleton-loader" style="width:33%; height:190px"></div>
                        <div class="courses__slider-item skeleton-loader" style="width:33%; height:190px"></div>
                        <div class="courses__slider-item skeleton-loader" style="width:33%; height:190px"></div>
                    </div>
                </div>
                <div class="courses-list">
                    <div class="skeleton-loader" style="width:100px; height:32px;"></div>

                    <div class="courses__slider" style="opacity:1">
                        <div class="courses__slider-item skeleton-loader" style="width:33%; height:190px"></div>
                        <div class="courses__slider-item skeleton-loader" style="width:33%; height:190px"></div>
                        <div class="courses__slider-item skeleton-loader" style="width:33%; height:190px"></div>
                    </div>
                </div>
            </div>
        `
        $(".courses-container").html(coursesTpl);
        

        SafarCourses.api.getCourses(SafarCourses.filters)
            .then( e => {
                SafarCourses.filters.bypasstransient = false;
                console.log("bypassTransient", SafarCourses.filters.bypasstransient )
                //console.log("subjects", e )
                /* 
                  "background_image": "https://learn.safaracademy.org/wp-content/uploads/2022/07/islamic-studies.png",
                "background_color": "#ef746f",
                "completed_steps": 25,
                "total_steps": 304,
                "progress_percent": "8"
                */
                //console.log("filters", SafarCourses.filters)
                // show results group by subjects
                if( SafarCourses.filters.searchkey.length <= 0 && 
                    SafarCourses.filters.status.length <= 0 && 
                    SafarCourses.filters.subject.length <= 0 ){
                    
                    console.log("group by subject")
                    coursesTpl = `<div class="courses-wrapper">`

                        e.subjects.map( subject => {{
                            coursesTpl += `
                                <div class="courses-list">
                                    <h2>${subject.name}</h2>
                                    <div class="courses__slider">
                                   `;

                                    subject.collections.map( collection => {
                                        // .courses
                                        hasCollection = true;
                                        if(collection.progress >= 100 ){
                                            progressTag = `<span class="complete">Complete</span>`
                                        }else if(collection.progress > 0 && collection.progress < 100 ){
                                            progressTag = `<span class="inprogress">In Progress</span>`
                                        }else{
                                            progressTag = `<span class="start">Start Course</span>`
                                        }

                                        if(!collection.image){
                                            collection.image = "/wp-content/uploads/2022/10/bg-red.png";
                                        }

                                        let playIcon = `
                                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/>
                                        </svg>
                                        `;
                                        let isLocked = false;

                                        if(safarObject.is_demo_user){
                                            if(!collection.demo_user_access){
                                                playIcon = `
                                                <svg width="70" height="90" viewBox="0 0 70 90" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:60px; height:60px">
                                                <path d="M56.1031 70.2314C57.8115 70.2314 59.2089 68.834 59.2089 67.1256V35.6232C59.2089 24.5123 57.9579 17.0776 55.1522 11.5168C51.297 3.87418 44.5167 0 34.9994 0C25.4821 0 18.7018 3.87418 14.8466 11.5168C12.0418 17.0776 10.7908 24.5114 10.7908 35.6232V67.1256C10.7908 68.834 12.1882 70.2314 13.8957 70.2314H16.1248C17.8323 70.2314 19.2306 68.834 19.2306 67.1256V35.6232C19.2306 29.5896 19.5578 20.9184 22.3826 15.3186C24.7825 10.5613 28.6738 8.43985 35.0003 8.43985C41.3268 8.43985 45.2172 10.5613 47.618 15.3186C50.4428 20.9175 50.77 29.5887 50.77 35.6232V67.1256C50.77 68.834 52.1674 70.2314 53.8758 70.2314H56.1049H56.1031Z" fill="#AAAAAA"/>
                                                <path d="M69.1801 85.7797C69.1801 88.1009 67.2809 90.0001 64.9597 90.0001H5.03922C2.71797 90.0001 0.818848 88.1009 0.818848 85.7797V39.1504C0.818848 36.8291 2.71797 34.9309 5.03922 34.9309H64.9606C67.2818 34.9309 69.181 36.8291 69.181 39.1504V85.7797H69.1801Z" fill="#FFAB15"/>
                                                <path d="M5.0392 34.9309C3.17714 34.9309 1.66309 36.4459 1.66309 38.307V84.9354C1.66309 86.7975 3.17714 88.3115 5.0392 88.3115H64.9606C66.8226 88.3115 68.3367 86.7966 68.3367 84.9354V38.307C68.3367 36.4459 66.8217 34.9309 64.9606 34.9309H5.0392Z" fill="#FFC50B"/>
                                                <path d="M48.531 34.9309H5.0392C3.17714 34.9309 1.66309 36.4459 1.66309 38.307V81.7998L48.531 34.9309Z" fill="#FFD91F"/>
                                                <path d="M43.8605 57.2431C43.8605 52.3493 39.8932 48.382 34.9994 48.382C30.1056 48.382 26.1375 52.3493 26.1375 57.2431C26.1375 62.1369 29.8806 64.2538 29.8806 64.2538C30.2078 64.5829 30.4039 65.2256 30.3171 65.682L28.4054 75.72C28.3186 76.1765 28.6277 76.548 29.0914 76.548H40.9074C41.3711 76.548 41.6803 76.1756 41.5935 75.72L39.6817 65.682C39.594 65.2256 39.7911 64.5829 40.1183 64.2538C40.1183 64.2538 43.8605 62.2842 43.8605 57.2431Z" fill="#FFAB15"/>
                                                <path d="M26.9818 58.0873C26.9818 61.8702 30.3181 64.3668 30.3181 64.3668C30.6417 64.6985 31.0205 65.0393 31.1606 65.1234C31.2998 65.2065 31.3432 65.6476 31.2564 66.1023L29.4242 75.719C29.3374 76.1755 29.6465 76.547 30.1102 76.547H39.8861C40.3507 76.547 40.6589 76.1746 40.5721 75.719L38.7399 66.1023C38.6531 65.6467 38.6965 65.2056 38.8357 65.1234C38.9758 65.0393 39.3537 64.6985 39.6782 64.3668C39.6782 64.3668 43.0154 62.0772 43.0154 58.0873C43.0154 53.6663 39.4187 50.0696 34.9977 50.0696C30.5767 50.0696 26.98 53.6663 26.98 58.0873H26.9818Z" fill="#B76732"/>
                                                </svg>                                                
                                                `
                                                isLocked = true;
                                            }
                                        }
                                        coursesTpl += `
                                            <div class="courses__slider-item">
                                                
                                                <div class="courses__slider-tag">${progressTag}</div>
                                                ${
                                                    (isLocked) ? `
                                                    <svg style="width:60px;height:60px;position: absolute;right: 10px;top: 25px;width: 20px;height: 20px;" width="70" height="90" viewBox="0 0 70 90" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:60px; height:60px">
                                                        <path d="M56.1031 70.2314C57.8115 70.2314 59.2089 68.834 59.2089 67.1256V35.6232C59.2089 24.5123 57.9579 17.0776 55.1522 11.5168C51.297 3.87418 44.5167 0 34.9994 0C25.4821 0 18.7018 3.87418 14.8466 11.5168C12.0418 17.0776 10.7908 24.5114 10.7908 35.6232V67.1256C10.7908 68.834 12.1882 70.2314 13.8957 70.2314H16.1248C17.8323 70.2314 19.2306 68.834 19.2306 67.1256V35.6232C19.2306 29.5896 19.5578 20.9184 22.3826 15.3186C24.7825 10.5613 28.6738 8.43985 35.0003 8.43985C41.3268 8.43985 45.2172 10.5613 47.618 15.3186C50.4428 20.9175 50.77 29.5887 50.77 35.6232V67.1256C50.77 68.834 52.1674 70.2314 53.8758 70.2314H56.1049H56.1031Z" fill="#AAAAAA"/>
                                                        <path d="M69.1801 85.7797C69.1801 88.1009 67.2809 90.0001 64.9597 90.0001H5.03922C2.71797 90.0001 0.818848 88.1009 0.818848 85.7797V39.1504C0.818848 36.8291 2.71797 34.9309 5.03922 34.9309H64.9606C67.2818 34.9309 69.181 36.8291 69.181 39.1504V85.7797H69.1801Z" fill="#FFAB15"/>
                                                        <path d="M5.0392 34.9309C3.17714 34.9309 1.66309 36.4459 1.66309 38.307V84.9354C1.66309 86.7975 3.17714 88.3115 5.0392 88.3115H64.9606C66.8226 88.3115 68.3367 86.7966 68.3367 84.9354V38.307C68.3367 36.4459 66.8217 34.9309 64.9606 34.9309H5.0392Z" fill="#FFC50B"/>
                                                        <path d="M48.531 34.9309H5.0392C3.17714 34.9309 1.66309 36.4459 1.66309 38.307V81.7998L48.531 34.9309Z" fill="#FFD91F"/>
                                                        <path d="M43.8605 57.2431C43.8605 52.3493 39.8932 48.382 34.9994 48.382C30.1056 48.382 26.1375 52.3493 26.1375 57.2431C26.1375 62.1369 29.8806 64.2538 29.8806 64.2538C30.2078 64.5829 30.4039 65.2256 30.3171 65.682L28.4054 75.72C28.3186 76.1765 28.6277 76.548 29.0914 76.548H40.9074C41.3711 76.548 41.6803 76.1756 41.5935 75.72L39.6817 65.682C39.594 65.2256 39.7911 64.5829 40.1183 64.2538C40.1183 64.2538 43.8605 62.2842 43.8605 57.2431Z" fill="#FFAB15"/>
                                                        <path d="M26.9818 58.0873C26.9818 61.8702 30.3181 64.3668 30.3181 64.3668C30.6417 64.6985 31.0205 65.0393 31.1606 65.1234C31.2998 65.2065 31.3432 65.6476 31.2564 66.1023L29.4242 75.719C29.3374 76.1755 29.6465 76.547 30.1102 76.547H39.8861C40.3507 76.547 40.6589 76.1746 40.5721 75.719L38.7399 66.1023C38.6531 65.6467 38.6965 65.2056 38.8357 65.1234C38.9758 65.0393 39.3537 64.6985 39.6782 64.3668C39.6782 64.3668 43.0154 62.0772 43.0154 58.0873C43.0154 53.6663 39.4187 50.0696 34.9977 50.0696C30.5767 50.0696 26.98 53.6663 26.98 58.0873H26.9818Z" fill="#B76732"/>
                                                    </svg> 
                                                    `:``
                                                }
                                                <a href="${collection.collection_url}">
                                                    ${playIcon}    
                                                    <img src="${collection.image}" alt="">
                                                </a>
                                            </div> `;
                                       
                                    })

                            coursesTpl += `
                                    </div>
                                </div>
                            `
                        }})

                    coursesTpl += `</div>`

                }else{

                    // non grouped
                    coursesTpl = ``;
                    if(SafarCourses.filters.searchkey != ""){
                        coursesTpl += `
                        <div class="courses-query">
                            Showing results for “<span class="courses-query__string">${SafarCourses.filters.searchkey}</span>”
                        </div>`;
                    }
                    
                    coursesTpl += `
                        
                        <div class="courses-result">
                            <div class="courses-result__items"> `;

                                e.subjects.map( subject => {

                                    subject.collections.map( collection => {

                                        hasCollection = true;

                                        if(collection.progress >= 100 ){
                                            progressTag = `<span class="complete">Complete</span>`
                                        }else if(collection.progress > 0 && collection.progress < 100 ){
                                            progressTag = `<span class="inprogress">In Progress</span>`
                                        }else{
                                            progressTag = `<span class="start">Start Course</span>`
                                        }

                                        if(!collection.image){
                                            collection.image = "/wp-content/uploads/2022/10/bg-red.png";
                                        }

                                        coursesTpl += `
                                        <div class="courses-result__item">
                                            <div class="courses-result__tag">${progressTag}</div>
                                            <a href="${collection.collection_url}"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                                                
                                                <img src="${collection.image}" alt="">

                                            </a>
                                        </div>`;
                                    });
                                });

                    coursesTpl += `
                            </div>
                        </div>
                    `;
                    //console.log("coursesTpl", coursesTpl )
                }

                if(!hasCollection){

                    if(safarObject.course_library_no_result === null ){
                        safarObject.course_library_no_result = `
                            <img src="${safarObject.stylesheet_directory}/assets/img/no-result.png" alt="No result">
                            <h3>No results found</h3>
                            <p>Try adjusting your search or filter to find what you’re looking for.</p>
                        `;
                    }
                    coursesTpl += `
                    <div class="noresult">
                        ${safarObject.course_library_no_result}
                    </div>
                    `;
                }

                
                $(".courses-container").html(coursesTpl);
                coursesSliderSlick();

            })
            .catch( e => {
                console.log("error ", e )
            })

            
    },

    loadCategories : () => {
        SafarCourses.api.getCategories()
            .then( categories => {
                console.log("categories", categories)

                /* 
                <li class="disabled optsubject" data-val="">Subject</li>
                            <li class="optsubject">Islamic Studies</li>
                            <li class="optsubject">Learn To Read</li>
                            <li class="optsubject">Learn By Heart</li> 
                */
                let categoriesTpl = `<li class="disabled optsubject" data-val="">Subject</li>`
                categories.map(category => {
                    categoriesTpl += `
                    <li class="optsubject" data-val="${category.term_id}">${category.term_name}</li>
                    `
                })
                SafarCourses.loadedSubjects = true;
                $(".categories-dp").html(categoriesTpl);
            })
            .catch( e => {
                console.log("erorr categories", e)
            })
    }
}