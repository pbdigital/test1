$ = jQuery;
let SafarSearch = {

    searchKey: "",
    page: 0,
    length: 9,
    xhrLoading: false,
    loadedAll: false,

    api : {
        searchPosts : async ( data ) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/search_posts`,
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
                        reject(`error /search_posts/${userId}`);
                    }
                });
            });
        }
    },

    loadSearchResult: () => {
        SafarSearch.xhrLoading = true;
        $(".search .noresult").hide();
        let courseSkeletonLoader = ``;
        for(i = 0; i < 6; i++){
            courseSkeletonLoader += `
            <div class="search-item loader">
                <div class="skeleton-loader" style="width:100%; height:207px;"></div>
            </div>
            `
        }

        $(".search-items").append(courseSkeletonLoader);

        let searchResult = ``
        SafarSearch.api.searchPosts({ page:SafarSearch.page, length:SafarSearch.length, search: SafarSearch.searchKey })
            .then( data => {
                console.log("search result", data )
                $(".search-item.loader").remove();

                SafarSearch.loadedAll = ( data.has_next ) ? true:false;

                if(data.result.length > 0 ){

                    data.result.map( post => {
                        searchResult = `
                        <div class="search-item">
                            <a href="${post.link}">
                                <span class="search-item__title">${ (post.course) ? post.course:"&nbsp;"}</span>
                                ${
                                    (!post.thumbnail) ? `<div class="no-image-title">${post.title}</div>`: `<img src="${post.thumbnail}" alt="">`
                                }
                            </a>
                        </div>
                        `
                        $(".search-items").append(searchResult);
                    })
                }else{
                    $(".search .noresult").show();
                }
                SafarSearch.xhrLoading = false;
                SafarSearch.page++;
              
            })
            .catch( e => {

            })
    },

    init : () => {
        SafarSearch.loadSearchResult();
    }

}

