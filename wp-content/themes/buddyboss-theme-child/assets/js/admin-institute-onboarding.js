$ = jQuery;
let AdminInstitute = {
    typeSelected: "avatar",
    taxonomySelected: "avatar_category",
    subTypeSelected: "",
    avatarItems: "",
    apiResponseAvatarItems: "",
    password: "",

    selectedAvatars: {
        group: "infant",
        skinColor: "",
        hairStyle: "",
        hairColor: "",
        gender: (safarObject.user_gender=="") ? "male":safarObject.user_gender,
    },

    api: {
        avatarItems: async (e) => {
            return new Promise((resolve, reject) => {

                if(AdminInstitute.apiResponseAvatarItems ==""){
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/gears`,
                        data: {
                            type: e.type,
                            taxonomy: e.taxonomy,
                            adminonboarding: true
                        },
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        dataType: "json",
                        beforeSend: (xhr) => {

                        },
                        success: d => {
                            AdminInstitute.apiResponseAvatarItems = d;
                            resolve(AdminInstitute.apiResponseAvatarItems);
                        },
                        error: (d) => {
                            reject(`error /user/avatar}`);
                        }
                    });
                }else{
                    resolve(AdminInstitute.apiResponseAvatarItems);
                }
            });
        },
        savePassword: async (e) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/groups/institute/update_admin_password`,
                    data: {
                        password: AdminInstitute.password
                    },
                    type: "post",
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {

                    },
                    success: d => {
                        window.location.href = window.location.href + "?tab=gender";
                        console.log("pwd", d)
                    },
                    error: (d) => {
                        reject(`error pwd ${d}`);
                    }
                });
            });
        }
    },

    getAvatarItems: (e) => {
        type = e.type;

        $(".gears-list").removeClass("avatar-active").addClass("sub-category-skin-color").removeClass("sub-category-hair-color");
        tpl = ``;
        for (i = 0; i < 8; i++) {
        tpl += `
                    <div class="avatars-categories__list-item " style="background:#efefef; width:100%;min-height:80px; display:flex;">
                        <div class="avatars-categories__img">
                                
                        </div>
                    </div>
                `;
        }

        $(".gears-list").html(tpl);
        
        AdminInstitute.api.avatarItems(e)
            .then( d => { 
                AdminInstitute.avatarItems = d.items;
                tpl = ``;

                //console.log("d.sub_categories", d.sub_categories, d.sub_categories.length )

                d.items.map((e) => {

                    equipped = "";
                    button = "";

                    let show = false;
                    let subTypeText = "";
                    if(d.sub_categories.length <=0){
                        show = true;
                    }else{
                        if(AdminInstitute.subTypeSelected == ""){
                            // get the first subcategory
                            AdminInstitute.subTypeSelected = d.sub_categories[0].term_id;
                        }
                        
                        if(e.terms.length > 0){
                            e.terms.map( term => {
                                //console.log("term.term_id == AdminInstitute.subTypeSelected", term.term_id , AdminInstitute.subTypeSelected)
                                // check if the SubCategory selected matches with the item, IF yes show
                                if(term.term_id == AdminInstitute.subTypeSelected  ){
                                    show = true;
                                    subTypeText = term.slug;
                                }
                            })
                        }
                    }

                    if(show){
                        tpl += `
                            <div class="avatars-categories__list-item ${equipped} item-${type}" data-type="${type}" 
                                                data-id="${e.ID}" 
                                                data-subtype="${subTypeText}"  
                                                data-colorhex="${e.color_hex}"
                                                data-secondarycolor="${e.secondary_color}"
                                                data-eyecolor="${e.eye_color}"
                                                data-eyebrowcolor="${e.eyebrow_color}"
                                                data-eyelashcolor="${e.eyelash_color}"
                                            >
                                <div class="avatars-categories__img">
                                    <img src="${e.image}" alt="">
                                </div>
                                <div class="avatars-categories__info">
                                    ${button}
                                </div>
                            </div>
                        `;
                    }
                    
                });

                $(".gears-list").html(tpl);
                if (type == "avatars") {
                    $(".gears-list").addClass("avatar-active");
                } else {
                    $(".gears-list").removeClass("avatar-active");
                }

                // display Subcategories
                let subCategoriesTpl = ``;

                console.log("display subcategories", d.sub_categories)

                if(d.sub_categories.length <=0){
                    subCategoriesTpl = "";
                    $(".avatars-categories__list-sub-categories").hide()
                }else{
                    let isActive = "";
                    d.sub_categories.map( subCat => {

                        if(subCat.term_id == AdminInstitute.subTypeSelected){
                            isActive = "active";
                            $(".gears-list").addClass(`sub-category-${subCat.slug}`)
                        }else{
                            isActive = "";
                            $(".gears-list").removeClass(`sub-category-${subCat.slug}`)
                        }

                        subCategoriesTpl += `
                            <div>
                                <button type="button" class="${isActive}" data-termid="${subCat.term_id}">${subCat.name}</button>
                            </div>
                        `
                    })
                    $(".avatars-categories__list-sub-categories").show().html(subCategoriesTpl);
                    
                }

                

                $(".avatars-categories .avatars-categories__item").each( function(){
                    var itemType = $(this).attr("data-type");
                    if(itemType == type){
                        $(".avatars-categories__list").addClass(`list-${itemType}`)
                    }else{
                        $(".avatars-categories__list").removeClass(`list-${itemType}`)
                    }
                });
            })
            .catch (e => {
                console.log("error displaying avatar items", e )
            })
         
    },

    getUserEquippedGears: () => {
        $.ajax({
            url: `${safarObject.apiBaseurl}/user/gears`,
            data: {
                type,
                adminonboarding: true
            },
            dataType: "json",
            headers: {
                "X-WP-Nonce": safarObject.wpnonce
            },
            beforeSend: (xhr) => {

                $(".avatars-hero").html(`
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
                        `);

                $(".avatars-gears__item-equip").hide();
                $(".age-group .select-group").html(`
                    <label class="skeleton-loader" style="padding:0px" ><div class="skeleton-loader" style="
                        width: 100%;
                        height: 100%;
                    "></div></label>
                        <label class="skeleton-loader" style="padding:0px" ><div class="skeleton-loader" style="
                        width: 100%;
                        height: 100%;
                    "></div></label>
                `);
            },
            success: (d) => {
                
                let heroFemale = `
                 
                    <div class="hijabs hijabs-preview" style="position: absolute;z-index: 2;top: 37px; justify-content: center; display: flex; width:100%;" id="hijabs-2"><img src="/wp-content/themes/buddyboss-theme-child/assets/img/defaul-hijab.png" style="display: block; width: 117px; margin-left: 0px; margin-top: 0px;"></div>
                    <svg width="126" class="head" height="131" viewBox="0 0 126 131" fill="none" xmlns="http://www.w3.org/2000/svg" style="top:45px">
                        <path d="M50.6582 103.394V122.611C50.6582 126.741 56.3154 130.095 63.2898 130.095C70.2642 130.095 75.9214 126.741 75.9214 122.611V103.394H50.6582Z" fill="#EBAE8C" class="skin-color-shaded"/>
                        <path d="M108.663 31.0201C111.413 37.5485 107.157 95.3327 100.639 103.225C93.7274 111.601 77.0812 119.105 63.1744 119.111C50.3958 119.111 29.6091 111.213 24.4767 101.835C18.4049 90.7408 15.3611 39.5374 17.4288 33.5024C24.4137 13.1197 41.6792 12.6631 64.2502 12.9675C85.3676 13.2509 101.526 14.0748 108.663 31.0201Z" fill="#FFC19F" class="skin-color" "/>
                        <path class="for-no-face" d="M80.2877 94.1416C80.3139 101.2 72.5943 106.952 63.0432 106.988C53.4973 107.025 45.7357 101.326 45.7095 94.2676C45.6833 87.2092 53.8017 88.5054 63.3528 88.0016C72.8882 87.4978 80.2667 87.0833 80.2877 94.1469V94.1416Z" fill="#630900"/>
                        <path class="for-no-face" d="M64.0455 100.303C58.8869 100.319 54.3632 101.861 51.7708 104.17C54.8303 106.044 58.7557 107.167 63.0432 107.151C67.8712 107.136 72.2322 105.677 75.4019 103.341C72.715 101.473 68.6269 100.287 64.0455 100.303Z" fill="#D36553"/>
                        <path class="for-no-face" d="M75.4019 88.0437C72.3267 87.3667 68.1074 87.5871 63.3581 87.839C57.9948 88.1224 53.0828 87.8495 49.7609 88.7574C51.1358 91.5912 56.3102 93.6904 62.4869 93.6694C69.1097 93.6484 74.557 91.1924 75.4072 88.0384L75.4019 88.0437Z" fill="white"/>
                        <path class="for-no-face" d="M67.1995 84.5537C66.9528 84.5537 66.7324 84.3701 66.6957 84.1129C66.4228 82.3077 65.0164 81.1269 63.1114 81.1006C61.196 81.0744 59.7265 82.2394 59.4484 84.0027C59.4012 84.2861 59.1388 84.4803 58.8554 84.433C58.572 84.3911 58.3778 84.1234 58.4251 83.84C58.7819 81.5835 60.7131 80.0301 63.1272 80.0616C65.5254 80.0931 67.3727 81.6569 67.7243 83.9555C67.7663 84.2389 67.5721 84.5013 67.2887 84.5485H67.2048L67.1995 84.5537Z" fill="#C4735B"/>
                        <path class="for-no-face" d="M99.117 48.8682C98.7706 48.8472 98.4295 48.7003 98.1671 48.4326C94.8085 45.0268 88.107 43.7043 82.56 45.3731C81.7886 45.6093 80.9699 45.1685 80.7337 44.3918C80.5028 43.6151 80.9384 42.8017 81.7151 42.5655C88.3326 40.5766 96.1362 42.1824 100.261 46.3807C100.828 46.958 100.823 47.8869 100.24 48.4536C99.9304 48.758 99.5211 48.8997 99.117 48.8735V48.8682Z" fill="#772400" class="eyebrow"/>
                        <path class="for-no-face" d="M28.0032 48.2646C27.5991 48.2699 27.1951 48.1124 26.9012 47.7923C26.3501 47.1993 26.3869 46.2704 26.9851 45.7194C31.3094 41.7258 39.1812 40.503 45.6938 42.8069C46.4599 43.0797 46.8588 43.9194 46.5859 44.6803C46.313 45.4465 45.4734 45.8454 44.7124 45.5725C39.2546 43.636 32.4901 44.6226 28.9741 47.871C28.7012 48.1282 28.3496 48.2541 28.0032 48.2594V48.2646Z" fill="#772400" class="eyebrow"/>
                        <path class="for-no-face" d="M50.8 64.2339C54.253 69.4293 53.8122 75.1232 50.779 77.2644C46.2081 80.4918 35.408 81.3367 31.3881 76.5979C27.4942 72.0008 29.3992 63.6986 35.7701 60.1668C41.3433 57.0758 47.4623 59.2117 50.8 64.2392V64.2339Z" fill="#F9FEFF"/>
                        <path class="for-no-face" d="M30.6901 62.6334C35.6599 54.6567 47.5673 57.4643 50.9154 64.4072C47.4675 59.2118 41.0757 57.5902 35.9223 60.445C32.7368 62.2136 30.4697 65.3833 29.8557 68.9256C29.3677 71.7489 29.9974 74.9291 31.6138 76.8394C29.7508 75.5904 27.0376 68.4953 30.6954 62.6334H30.6901Z" fill="#772400" class="eyelash"/>
                        <path class="for-no-face" d="M49.5195 67.6712C50.9311 71.2608 49.2466 75.3279 45.7568 76.75C42.2669 78.1722 38.2943 76.4142 36.8826 72.8246C35.4709 69.2351 37.1555 65.168 40.6453 63.7458C44.1352 62.3237 48.1078 64.0817 49.5195 67.6712Z" fill="#772400" class="eyes"/>
                        <path class="for-no-face" d="M48.9632 67.0469C49.3568 68.044 48.8897 69.1723 47.9189 69.5659C46.948 69.9595 45.846 69.4714 45.4576 68.4743C45.064 67.4772 45.5311 66.349 46.502 65.9554C47.4728 65.5618 48.5749 66.0498 48.9632 67.0469Z" fill="#FEFFFE"/>
                        <path class="for-no-face" d="M75.2602 64.276C71.8648 69.4609 72.3686 75.1548 75.4229 77.3065C80.0305 80.5496 90.8411 81.4313 94.8085 76.703C98.6499 72.1216 96.6557 63.809 90.2481 60.2562C84.6381 57.1442 78.5454 59.2591 75.2602 64.276Z" fill="#F9FEFF"/>
                        <path class="for-no-face" d="M95.3543 62.7437C90.2953 54.7512 78.4194 57.5169 75.15 64.4493C78.5454 59.2696 84.9163 57.669 90.1011 60.5396C93.3076 62.3186 95.6061 65.4988 96.2621 69.0412C96.7817 71.8698 96.1887 75.0447 94.5933 76.9497C96.4406 75.7059 99.075 68.6213 95.3595 62.7437H95.3543Z" fill="#772400" class="eyelash"/>
                        <path class="for-no-face" d="M89.2457 67.7398C90.6574 71.3293 88.9729 75.3964 85.483 76.8186C81.9932 78.2408 78.0206 76.4827 76.6089 72.8932C75.1972 69.3036 76.8818 65.2366 80.3716 63.8144C83.8614 62.3922 87.8341 64.1502 89.2457 67.7398Z" fill="#772400" class="eyes"/>
                        <path class="for-no-face" d="M88.6895 67.1152C89.0831 68.1123 88.616 69.2406 87.6451 69.6342C86.6743 70.0278 85.5722 69.5397 85.1839 68.5426C84.7903 67.5455 85.2574 66.4172 86.2282 66.0237C87.1991 65.6301 88.3011 66.1181 88.6895 67.1152Z" fill="#FEFFFE"/>
                    </svg>


                    <svg class="body" width="183" height="237" viewBox="0 0 183 237" fill="none" xmlns="http://www.w3.org/2000/svg" style="top:180px;">
                        <path d="M30.9893 99.712C28.8782 99.0015 20.6537 94.7487 19.8679 93.6803C30.3712 72.1218 37.5 38 50 16.5C52.064 16.3053 63.81 23.0833 66.3193 23.4096C65.6697 31.9046 42.2731 82.4536 30.9893 99.7173L30.9893 99.712Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M12.8606 121.036C14.9403 117.241 17.4443 111.257 23.6992 100.619C24.4116 99.4035 30.3626 99.9773 29.472 101.104C24.3016 107.646 18.5549 122.746 12.8606 121.036Z" fill="#E0956C" class="skin-color-shaded"  />
                        <path d="M6.53769 120.941C8.61739 117.146 11.8234 107.151 20.3203 98.7825C21.368 97.7509 26.9575 98.9246 26.067 100.051C20.8965 106.593 11.2629 122.241 6.53769 120.941Z" fill="#E0956C" class="skin-color-shaded"  />
                        <path d="M-2.74383e-06 117.646C2.0797 113.851 16.559 94.7455 25.0559 86.3768C26.1036 85.3452 33.9091 91.4454 33.6995 93.7297C35.9731 98.5193 30.263 105.641 30.2473 116.82C30.2473 118.751 27.5704 117.525 26.9942 116.862C25.5117 115.157 25.8103 114.267 23.783 109.035C22.5467 105.846 18.6021 106.114 16.5066 107.241C8.28737 113.404 3.76127 119.02 -2.74383e-06 117.646H-2.74383e-06Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M47.5 21C54.5358 5.62453 70.6307 1.98647 78.4817 0.350281H91.005L90.9932 87.1215H58.9391L60.5577 40.5699L47.5 21Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M151.016 99.712C153.127 99.0015 161.351 94.7487 162.137 93.6803C151.634 72.1218 144.505 38 132.005 16.5C129.941 16.3053 118.195 23.0833 115.686 23.4096C116.335 31.9046 139.732 82.4536 151.016 99.7173L151.016 99.712Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M169.144 121.036C167.065 117.241 164.561 111.257 158.306 100.619C157.593 99.4035 151.642 99.9773 152.533 101.104C157.703 107.646 163.45 122.746 169.144 121.036Z" fill="#E0956C" class="skin-color-shaded"  />
                        <path d="M175.467 120.941C173.388 117.146 170.182 107.151 161.685 98.7825C160.637 97.7509 155.047 98.9246 155.938 100.051C161.108 106.593 170.742 122.241 175.467 120.941Z" fill="#E0956C" class="skin-color-shaded"  />
                        <path d="M182.005 117.646C179.925 113.851 165.446 94.7455 156.949 86.3768C155.901 85.3452 148.096 91.4454 148.305 93.7297C146.032 98.5193 151.742 105.641 151.758 116.82C151.758 118.751 154.435 117.525 155.011 116.862C156.493 115.157 156.195 114.267 158.222 109.035C159.458 105.846 163.403 106.114 165.498 107.241C173.718 113.404 178.244 119.02 182.005 117.646V117.646Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M134.505 21C127.469 5.62453 111.374 1.98647 103.523 0.350281H91L91.0118 87.1215H123.066L121.447 40.5699L134.505 21Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M47.5 21C54.5358 5.62453 70.6307 1.98647 78.4817 0.350281H91.005L90.9932 87.1215H58.9391L60.5577 40.5699L47.5 21Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M134.505 21C127.469 5.62453 111.374 1.98647 103.523 0.350281H91L91.0118 87.1215H123.066L121.447 40.5699L134.505 21Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M90.969 113.5C96.569 114.7 100.302 134 101.469 143.5L114.219 220.5C114.719 225 114.719 224.5 115.719 233.5C116.117 237.083 155.719 239 152.219 233.5C148.395 227.492 137.719 222.5 130.719 220.5L126.469 143.5L120.969 39.5H90.719V113.461C90.8017 113.469 90.8851 113.482 90.969 113.5Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M90.469 113.5C84.869 114.7 81.1357 134 79.969 143.5L67.219 220.5C66.719 225 66.719 224.5 65.719 233.5C65.3208 237.083 25.719 239 29.219 233.5C33.0425 227.492 43.719 222.5 50.719 220.5L54.969 143.5L60.469 39.5H90.719V113.461C90.6362 113.469 90.5529 113.482 90.469 113.5Z" fill="#FFC19F" class="skin-color" "/>
                        <path d="M126 134H99.5C96.7 120.8 92.5 115.833 90.5 115V86H123.5L126 134Z" fill="#17AADD"/>
                        <path d="M55 134H81.5C84.3 120.8 88.5 115.833 90.5 115V86H57.5L55 134Z" fill="#17AADD"/>
                    </svg>

                    <div class="tops adminonboarding" style="
                        position: absolute;
                        left: 200px;
                        z-index: 1;
                        top: 166px;
                        left: 41px;
                    ">

                        <svg width="117" height="105" viewBox="0 0 117 105" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M96.2798 97.9965C96.2799 97.9964 96.2801 97.9963 96.2802 97.9963V98.0015C96.2802 98.0015 96.2801 97.9998 96.2798 97.9965C71.1634 109.09 23.23 104.141 18.9845 99.9852C18.9845 99.9852 24.0224 22.8679 25.665 3.15693C31.1141 1.75909 38.036 0.918969 45.5 0.538513C47 2.69235 51.6 7.00003 58 7.00003C64.4 7.00003 69.3333 2.88708 71 0.830605C77.0735 1.24749 82.8295 1.89318 87.7787 2.7161C89.4082 25.0168 96.2144 97.3021 96.2798 97.9965Z" fill="#F46483"/>
                        <path d="M91.7933 48.2729C98.0908 45.7119 111.893 37.5987 116.878 33.836C110.859 25.9957 98.4634 9.82182 92.2394 3.0101C87.9991 2.48006 86.2568 2.33312 82.5256 1.8818C85.8632 15.5735 85.7583 32.2669 87.4586 35.9772C89.4633 40.3591 89.9723 45.0297 91.7933 48.2729Z" fill="#F46483"/>
                        <path d="M23.4107 48.3859C17.5856 46.9847 5.07468 38.1525 0 34.7309C6.42338 24.209 15.6229 11.3307 21.2066 3.99421C25.3839 3.08633 27.3265 2.78196 31 2.00003C28.9061 15.9383 31.7128 31.7764 30.3116 35.6126C28.5011 40.577 24.7752 46.0925 23.4055 48.3911L23.4107 48.3859Z" fill="#F46483"/>
                        <g opacity="0.5">
                        <path d="M49.0352 31.5432C42.1675 38.0223 54.5514 46.4647 58.5903 48.0489C63.6193 45.7173 73.604 34.638 66.1548 30.5432C63.2474 28.9431 59.4914 31.0853 57.9722 34.2643C55.7511 31.48 51.4764 29.2379 49.0352 31.5379V31.5432Z" fill="white"/>
                        </g>
                        </svg>


                    </div>

                    <div class="trousers" style="
                        position: absolute;
                        z-index: 1;
                        top: 259px;
                        left: 189px;
                        z-index: 0;
                        ">
                        <svg width="83" height="131" viewBox="0 0 83 131" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M37.1733 2.5786C29.8189 2.68039 5.17557 0.21001 5.17557 0.21001C2.98955 49.9183 1.34618 105.481 0.451157 127.438L25.9185 128.403C27.6444 113.081 31.583 64.9036 36.0409 38.1205C36.9425 34.4488 44.2709 34.3309 44.9783 37.7889C50.7464 65.9117 55.622 109.814 57.591 130.006L82.9041 128.497C81.104 101.107 77.3077 44.9592 75.0232 1.92204C75.0232 1.92204 52.3862 2.35583 37.1734 2.5681L37.1733 2.5786Z" fill="#40748E"/>
                        </svg>
                    </div>`

                let heroMale = `

                    
                        <svg width="124" height="85" viewBox="0 0 124 85" fill="none" xmlns="http://www.w3.org/2000/svg" class="hair default">
                            <path d="M114.279 42.4839C118.276 41.5302 120.155 40.308 124 38.9268C106.884 37.4085 110.565 13.895 90.7154 5.36113C58.2313 -6.08319 29.0909 3.57981 20.7479 8.71002C5.37995 18.1593 1.72583 22.27 0.0594182 42.2811C-0.986172 54.86 12.0783 78.072 14.8066 85C17.78 82.0786 14.2675 39.6228 15.5581 36.1643C16.3096 34.1638 23.204 22.5495 26.2101 22.3906C32.7613 22.0453 40.4944 44.6928 63.688 43.772C54.2068 38.9432 48.9244 31.9166 43.5277 21.848C45.3738 22.8948 47.2308 24.1664 49.0715 25.7285C68.5075 42.177 86.1736 46.0027 102.5 44.205C103.557 60.9549 103.045 73.1227 103.045 79.3984C110.925 75.0575 114.944 53.8405 114.274 42.4839H114.279Z" fill="#772400"/>
                        </svg>
                    
                        <svg width="126" class="head" height="131" viewBox="0 0 126 131" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M50.6582 103.394V122.611C50.6582 126.741 56.3154 130.095 63.2898 130.095C70.2642 130.095 75.9214 126.741 75.9214 122.611V103.394H50.6582Z" fill="#EBAE8C" class="skin-color-shaded"/>
                            <path d="M0.76157 80.9432C3.64789 92.4045 14.7104 99.678 23.0807 98.6232C31.4511 97.5736 33.1304 88.1957 31.7449 77.1647C30.3595 66.1337 22.4457 58.0153 14.0754 59.0911C2.6403 60.5657 -1.95157 70.164 0.756322 80.9379L0.76157 80.9432Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M10.8007 83.4988C11.4095 88.3373 15.3611 91.8219 19.6276 91.2866C23.8941 90.7513 26.8592 86.3956 26.2504 81.5571C25.6417 76.7185 21.69 73.234 17.4288 73.7692C13.1623 74.3045 10.1972 78.6602 10.806 83.4988H10.8007Z" fill="#E0956C" class="skin-color-shaded"/>
                            <path d="M124.742 82.6225C121.552 93.9998 110.29 100.969 101.951 99.6938C93.612 98.4133 92.1846 88.9986 93.8691 78.0096C95.5537 67.0206 103.683 59.1173 112.021 60.424C123.415 62.2083 127.744 71.9273 124.742 82.6225Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M114.021 84.9001C113.281 89.7176 109.24 93.0973 104.989 92.4465C100.739 91.7958 97.8942 87.3613 98.6342 82.5386C99.3741 77.721 103.415 74.3414 107.666 74.9921C111.917 75.6429 114.761 80.0773 114.026 84.8949L114.021 84.9001Z" fill="#E0956C" class="skin-color-shaded"/>
                            <path d="M118.177 38.2045C121.845 75.5273 94.3572 99.8617 62.996 99.4366C31.6348 99.0115 6.16689 71.5127 9.84039 35.271C11.8766 15.1402 33.2144 -0.382984 63.1744 0.215272C94.5356 0.845016 115.569 11.6609 118.177 38.2045Z" fill="#772400" class="hair-color"/>
                            <path d="M108.663 31.0201C111.413 37.5485 107.157 95.3327 100.639 103.225C93.7274 111.601 77.0812 119.105 63.1744 119.111C50.3958 119.111 29.6091 111.213 24.4767 101.835C18.4049 90.7408 15.3611 39.5374 17.4288 33.5024C24.4137 13.1197 41.6792 12.6631 64.2502 12.9675C85.3676 13.2509 101.526 14.0748 108.663 31.0201Z" fill="#FFC19F" class="skin-color" "/>
                            
                            <path class="for-no-face" d="M80.2877 94.1416C80.3139 101.2 72.5943 106.952 63.0432 106.988C53.4973 107.025 45.7357 101.326 45.7095 94.2676C45.6833 87.2092 53.8017 88.5054 63.3528 88.0016C72.8882 87.4978 80.2667 87.0833 80.2877 94.1469V94.1416Z" fill="#630900"/>
                            <path class="for-no-face" d="M64.0455 100.303C58.8869 100.319 54.3632 101.861 51.7708 104.17C54.8303 106.044 58.7557 107.167 63.0432 107.151C67.8712 107.136 72.2322 105.677 75.4019 103.341C72.715 101.473 68.6269 100.287 64.0455 100.303Z" fill="#D36553"/>
                            <path class="for-no-face" d="M75.4019 88.0437C72.3267 87.3667 68.1074 87.5871 63.3581 87.839C57.9948 88.1224 53.0828 87.8495 49.7609 88.7574C51.1358 91.5912 56.3102 93.6904 62.4869 93.6694C69.1097 93.6484 74.557 91.1924 75.4072 88.0384L75.4019 88.0437Z" fill="white"/>
                            <path class="for-no-face" d="M67.1995 84.5537C66.9528 84.5537 66.7324 84.3701 66.6957 84.1129C66.4228 82.3077 65.0164 81.1269 63.1114 81.1006C61.196 81.0744 59.7265 82.2394 59.4484 84.0027C59.4012 84.2861 59.1388 84.4803 58.8554 84.433C58.572 84.3911 58.3778 84.1234 58.4251 83.84C58.7819 81.5835 60.7131 80.0301 63.1272 80.0616C65.5254 80.0931 67.3727 81.6569 67.7243 83.9555C67.7663 84.2389 67.5721 84.5013 67.2887 84.5485H67.2048L67.1995 84.5537Z" fill="#C4735B"/>
                            <path class="for-no-face" d="M99.117 48.8682C98.7706 48.8472 98.4295 48.7003 98.1671 48.4326C94.8085 45.0268 88.107 43.7043 82.56 45.3731C81.7886 45.6093 80.9699 45.1685 80.7337 44.3918C80.5028 43.6151 80.9384 42.8017 81.7151 42.5655C88.3326 40.5766 96.1362 42.1824 100.261 46.3807C100.828 46.958 100.823 47.8869 100.24 48.4536C99.9304 48.758 99.5211 48.8997 99.117 48.8735V48.8682Z" fill="#772400" class="eyebrow"/>
                            <path class="for-no-face" d="M28.0032 48.2646C27.5991 48.2699 27.1951 48.1124 26.9012 47.7923C26.3501 47.1993 26.3869 46.2704 26.9851 45.7194C31.3094 41.7258 39.1812 40.503 45.6938 42.8069C46.4599 43.0797 46.8588 43.9194 46.5859 44.6803C46.313 45.4465 45.4734 45.8454 44.7124 45.5725C39.2546 43.636 32.4901 44.6226 28.9741 47.871C28.7012 48.1282 28.3496 48.2541 28.0032 48.2594V48.2646Z" fill="#772400" class="eyebrow"/>
                            <path class="for-no-face" d="M50.8 64.2339C54.253 69.4293 53.8122 75.1232 50.779 77.2644C46.2081 80.4918 35.408 81.3367 31.3881 76.5979C27.4942 72.0008 29.3992 63.6986 35.7701 60.1668C41.3433 57.0758 47.4623 59.2117 50.8 64.2392V64.2339Z" fill="#F9FEFF"/>
                            <path class="for-no-face" d="M30.6901 62.6334C35.6599 54.6567 47.5673 57.4643 50.9154 64.4072C47.4675 59.2118 41.0757 57.5902 35.9223 60.445C32.7368 62.2136 30.4697 65.3833 29.8557 68.9256C29.3677 71.7489 29.9974 74.9291 31.6138 76.8394C29.7508 75.5904 27.0376 68.4953 30.6954 62.6334H30.6901Z" fill="#772400" class="eyelash"/>
                            <path class="for-no-face" d="M49.5195 67.6712C50.9311 71.2608 49.2466 75.3279 45.7568 76.75C42.2669 78.1722 38.2943 76.4142 36.8826 72.8246C35.4709 69.2351 37.1555 65.168 40.6453 63.7458C44.1352 62.3237 48.1078 64.0817 49.5195 67.6712Z" fill="#772400" class="eyes"/>
                            <path class="for-no-face" d="M48.9632 67.0469C49.3568 68.044 48.8897 69.1723 47.9189 69.5659C46.948 69.9595 45.846 69.4714 45.4576 68.4743C45.064 67.4772 45.5311 66.349 46.502 65.9554C47.4728 65.5618 48.5749 66.0498 48.9632 67.0469Z" fill="#FEFFFE"/>
                            <path class="for-no-face" d="M75.2602 64.276C71.8648 69.4609 72.3686 75.1548 75.4229 77.3065C80.0305 80.5496 90.8411 81.4313 94.8085 76.703C98.6499 72.1216 96.6557 63.809 90.2481 60.2562C84.6381 57.1442 78.5454 59.2591 75.2602 64.276Z" fill="#F9FEFF"/>
                            <path class="for-no-face" d="M95.3543 62.7437C90.2953 54.7512 78.4194 57.5169 75.15 64.4493C78.5454 59.2696 84.9163 57.669 90.1011 60.5396C93.3076 62.3186 95.6061 65.4988 96.2621 69.0412C96.7817 71.8698 96.1887 75.0447 94.5933 76.9497C96.4406 75.7059 99.075 68.6213 95.3595 62.7437H95.3543Z" fill="#772400" class="eyelash"/>
                            <path class="for-no-face" d="M89.2457 67.7398C90.6574 71.3293 88.9729 75.3964 85.483 76.8186C81.9932 78.2408 78.0206 76.4827 76.6089 72.8932C75.1972 69.3036 76.8818 65.2366 80.3716 63.8144C83.8614 62.3922 87.8341 64.1502 89.2457 67.7398Z" fill="#772400" class="eyes"/>
                            <path class="for-no-face" d="M88.6895 67.1152C89.0831 68.1123 88.616 69.2406 87.6451 69.6342C86.6743 70.0278 85.5722 69.5397 85.1839 68.5426C84.7903 67.5455 85.2574 66.4172 86.2282 66.0237C87.1991 65.6301 88.3011 66.1181 88.6895 67.1152Z" fill="#FEFFFE"/>
                        </svg>


                        <svg class="body" width="183" height="237" viewBox="0 0 183 237" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M30.9893 99.712C28.8782 99.0015 20.6537 94.7487 19.8679 93.6803C30.3712 72.1218 37.5 38 50 16.5C52.064 16.3053 63.81 23.0833 66.3193 23.4096C65.6697 31.9046 42.2731 82.4536 30.9893 99.7173L30.9893 99.712Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M12.8606 121.036C14.9403 117.241 17.4443 111.257 23.6992 100.619C24.4116 99.4035 30.3626 99.9773 29.472 101.104C24.3016 107.646 18.5549 122.746 12.8606 121.036Z" fill="#E0956C" class="skin-color-shaded"  />
                            <path d="M6.53769 120.941C8.61739 117.146 11.8234 107.151 20.3203 98.7825C21.368 97.7509 26.9575 98.9246 26.067 100.051C20.8965 106.593 11.2629 122.241 6.53769 120.941Z" fill="#E0956C" class="skin-color-shaded"  />
                            <path d="M-2.74383e-06 117.646C2.0797 113.851 16.559 94.7455 25.0559 86.3768C26.1036 85.3452 33.9091 91.4454 33.6995 93.7297C35.9731 98.5193 30.263 105.641 30.2473 116.82C30.2473 118.751 27.5704 117.525 26.9942 116.862C25.5117 115.157 25.8103 114.267 23.783 109.035C22.5467 105.846 18.6021 106.114 16.5066 107.241C8.28737 113.404 3.76127 119.02 -2.74383e-06 117.646H-2.74383e-06Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M47.5 21C54.5358 5.62453 70.6307 1.98647 78.4817 0.350281H91.005L90.9932 87.1215H58.9391L60.5577 40.5699L47.5 21Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M151.016 99.712C153.127 99.0015 161.351 94.7487 162.137 93.6803C151.634 72.1218 144.505 38 132.005 16.5C129.941 16.3053 118.195 23.0833 115.686 23.4096C116.335 31.9046 139.732 82.4536 151.016 99.7173L151.016 99.712Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M169.144 121.036C167.065 117.241 164.561 111.257 158.306 100.619C157.593 99.4035 151.642 99.9773 152.533 101.104C157.703 107.646 163.45 122.746 169.144 121.036Z" fill="#E0956C" class="skin-color-shaded"  />
                            <path d="M175.467 120.941C173.388 117.146 170.182 107.151 161.685 98.7825C160.637 97.7509 155.047 98.9246 155.938 100.051C161.108 106.593 170.742 122.241 175.467 120.941Z" fill="#E0956C" class="skin-color-shaded"  />
                            <path d="M182.005 117.646C179.925 113.851 165.446 94.7455 156.949 86.3768C155.901 85.3452 148.096 91.4454 148.305 93.7297C146.032 98.5193 151.742 105.641 151.758 116.82C151.758 118.751 154.435 117.525 155.011 116.862C156.493 115.157 156.195 114.267 158.222 109.035C159.458 105.846 163.403 106.114 165.498 107.241C173.718 113.404 178.244 119.02 182.005 117.646V117.646Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M134.505 21C127.469 5.62453 111.374 1.98647 103.523 0.350281H91L91.0118 87.1215H123.066L121.447 40.5699L134.505 21Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M47.5 21C54.5358 5.62453 70.6307 1.98647 78.4817 0.350281H91.005L90.9932 87.1215H58.9391L60.5577 40.5699L47.5 21Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M134.505 21C127.469 5.62453 111.374 1.98647 103.523 0.350281H91L91.0118 87.1215H123.066L121.447 40.5699L134.505 21Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M90.969 113.5C96.569 114.7 100.302 134 101.469 143.5L114.219 220.5C114.719 225 114.719 224.5 115.719 233.5C116.117 237.083 155.719 239 152.219 233.5C148.395 227.492 137.719 222.5 130.719 220.5L126.469 143.5L120.969 39.5H90.719V113.461C90.8017 113.469 90.8851 113.482 90.969 113.5Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M90.469 113.5C84.869 114.7 81.1357 134 79.969 143.5L67.219 220.5C66.719 225 66.719 224.5 65.719 233.5C65.3208 237.083 25.719 239 29.219 233.5C33.0425 227.492 43.719 222.5 50.719 220.5L54.969 143.5L60.469 39.5H90.719V113.461C90.6362 113.469 90.5529 113.482 90.469 113.5Z" fill="#FFC19F" class="skin-color" "/>
                            <path d="M126 134H99.5C96.7 120.8 92.5 115.833 90.5 115V86H123.5L126 134Z" fill="#17AADD"/>
                            <path d="M55 134H81.5C84.3 120.8 88.5 115.833 90.5 115V86H57.5L55 134Z" fill="#17AADD"/>
                        </svg>

                        <div class="tops adminonboarding" style="
                            position: absolute;
                            left: 200px;
                            z-index: 1;
                            top: 147px;
                            left: 41px;
                        ">

                            <svg width="117" height="105" viewBox="0 0 117 105" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M96.2798 97.9965C96.2799 97.9964 96.2801 97.9963 96.2802 97.9963V98.0015C96.2802 98.0015 96.2801 97.9998 96.2798 97.9965C71.1634 109.09 23.23 104.141 18.9845 99.9852C18.9845 99.9852 24.0224 22.8679 25.665 3.15693C31.1141 1.75909 38.036 0.918969 45.5 0.538513C47 2.69235 51.6 7.00003 58 7.00003C64.4 7.00003 69.3333 2.88708 71 0.830605C77.0735 1.24749 82.8295 1.89318 87.7787 2.7161C89.4082 25.0168 96.2144 97.3021 96.2798 97.9965Z" fill="#17AADD"/>
                            <path d="M91.7934 48.2729C98.0908 45.7119 111.893 37.5987 116.878 33.836C110.859 25.9957 98.4634 9.82182 92.2394 3.0101C87.9992 2.48006 86.2569 2.33312 82.5256 1.8818C85.8633 15.5735 85.7583 32.2669 87.4586 35.9772C89.4633 40.3591 89.9724 45.0297 91.7934 48.2729Z" fill="#17AADD"/>
                            <path d="M23.4107 48.3859C17.5856 46.9847 5.07468 38.1525 0 34.7309C6.42338 24.209 15.6229 11.3307 21.2066 3.99421C25.3839 3.08633 27.3265 2.78196 31 2.00003C28.9061 15.9383 31.7128 31.7764 30.3116 35.6126C28.5011 40.577 24.7752 46.0925 23.4055 48.3911L23.4107 48.3859Z" fill="#17AADD"/>
                            </svg>
                        
                        </div>

                        <div class="trousers" style="
                            position: absolute;
                            z-index: 1;
                            top: 260px;
                            left: 187px;
                            z-index: 0;
                        ">
                        <svg width="89" height="132" viewBox="0 0 89 132" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M39.1733 2.57862C31.8189 2.68041 7.17557 0.210029 7.17557 0.210029C4.98955 49.9183 3.34618 105.481 2.45116 127.438L27.9185 128.403C29.6444 113.081 33.583 64.9036 38.0409 38.1205C38.9425 34.4488 46.2709 34.3309 46.9783 37.789C52.7464 65.9117 57.622 109.814 59.591 130.006L84.9041 128.497C83.104 101.107 79.3077 44.9592 77.0232 1.92205C77.0232 1.92205 54.3862 2.35585 39.1734 2.56812L39.1733 2.57862Z" fill="#40748E"/>
                        <path d="M77.006 1.28163C76.7019 1.25638 76.4143 1.18414 76.1047 1.1798C69.3251 1.08495 63.7542 6.50205 63.6594 13.2816C63.5645 20.0612 68.9816 25.632 75.7612 25.7269C76.6585 25.7394 77.5311 25.6414 78.3732 25.4748C77.8858 17.1703 77.4223 9.0393 77.006 1.28163Z" fill="#305868"/>
                        <path d="M8.40012 0.321747C8.70476 0.305015 8.99432 0.240847 9.30391 0.245178C16.0835 0.340033 21.5006 5.91086 21.4057 12.6904C21.3109 19.47 15.74 24.8871 8.96046 24.7923C8.06317 24.7797 7.19365 24.6573 6.35657 24.4672C7.07609 16.1796 7.76695 8.06474 8.40012 0.321747Z" fill="#305868"/>
                        <path d="M57.0701 130.879L86.7501 130.227L86.5285 120.138L56.8485 120.79L57.0701 130.879Z" fill="#2E5B72"/>
                        <path d="M30.1689 120.414L0.518799 118.932L0.0149909 129.011L29.6651 130.493L30.1689 120.414Z" fill="#2E5B72"/>
                        </svg>
                        


                    </div>`

                if(d.gender != "male"){
                    hero = heroFemale;
                }else{
                    hero = heroMale;
                }
                
                
                $(".avatars-hero").html(hero);

                // remove face parts if group is set to no facial features
                if( $("body").hasClass("group-no-facial-feature")){
                    $(".for-no-face").remove();
                }

                emptyGear = ``;
                if (!d.hats) hat = emptyGear;
                else
                hat = `<img class="avatar-${d.achievement_name.avatars} hat-${d.achievement_name.hats}" src="${d.hats}" />`;

                if (!d.backpacks) backpack = emptyGear;
                else
                backpack = `<img class="avatar-${d.achievement_name.avatars} backpack-${d.achievement_name.backpacks}" src="${d.backpacks}" />`;

                if (!d.headphones) headphone = emptyGear;
                else
                headphone = `<img class="avatar-${d.achievement_name.avatars} headphones-${d.achievement_name.headphones}" src="${d.headphones}" />`;

                $(".avatars-gears").html(`

                    <div class="avatars-gears__item">
                        
                        <div class="avatars-gears__item-equip">
                            ${hat}
                        </div>
                    </div>
                    <div class="avatars-gears__item">
                        <div class="avatars-gears__item-equip">
                            ${backpack}
                        </div>
                    </div>
                    <div class="avatars-gears__item">
                        <div class="avatars-gears__item-equip">
                            ${headphone}
                        </div>
                    </div>
                
                `);
                $(".avatars-gears__item-equip").fadeIn();

                // select Age group section

                let selectAgeGroupTpl = `
                <label>
                    <span class="checkmark">
                        <input type="radio" name="age_group" value="infant" checked />
                        <span></span>
                    </span>
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/choose-avatar/${d.gender}.png" style="height:150px" />
                    <div class="group-name">Infant</div>
                </label>
                <label>
                    <span class="checkmark">
                        <input type="radio" name="age_group" value="junior" />
                        <span></span>
                    </span>
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/choose-avatar/${d.gender}.png"  style="height:180px"  />
                    <div class="group-name">Junior</div>
                </label>
                `
                $(".age-group .select-group").html(selectAgeGroupTpl);
            },
        });

        // check if user has already selected gender
        // if yes, they will be shown select Age group instead
        /*
        if(safarObject.user_gender != ""){
            setTimeout( () => {
                if( $(".gender-select").is(":visible") ){
                    $(".btn-select-gender").trigger("click");
                }
            }, 500)
        }
        */
    },

    saveAvatar: () => {
        console.log(AdminInstitute.selectedAvatars)
        $.ajax({
            url: `${safarObject.apiBaseurl}/user/avatar`,
            data: {
                group : AdminInstitute.selectedAvatars.group,
                skin_color : AdminInstitute.selectedAvatars.skinColor,
                hair_style : AdminInstitute.selectedAvatars.hairStyle,
                hair_color : AdminInstitute.selectedAvatars.hairColor,
                avatar_html: $(".avatars-hero").html(),
                gender: AdminInstitute.selectedAvatars.gender,
                adminonboarding: 1
            },
            type: "post",
            headers: {
                "X-WP-Nonce": safarObject.wpnonce
            },
            dataType: "json",
            beforeSend: (xhr) => {
                $(".btn-select-avatar").fadeTo("fast",.3)
            },
            success: (d) => {
                // show 
                
                $(".choose-avatar-inner .tab").removeClass("active");
                $(".choose-avatar-inner .tab.please-wait").addClass("active")
                $(".choose-avatar-inner").removeClass("wide");

                setTimeout( e => {
                   window.location.href = "/"
                }, 5000)
                
            }
        });

    },

    saveGender : async () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${safarObject.apiBaseurl}/user/avatar`,
                data: {
                    group : AdminInstitute.selectedAvatars.group,
                    skin_color : AdminInstitute.selectedAvatars.skinColor,
                    hair_style : AdminInstitute.selectedAvatars.hairStyle,
                    hair_color : AdminInstitute.selectedAvatars.hairColor,
                    gender: AdminInstitute.selectedAvatars.gender
                },
                type: "post",
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
                    reject(`error /user/avatar}`);
                }
            });
        })
    },

    checkPasswordStrength : (password, indicatorElement) => {
        console.log("checking password strength", password )
        // Check the length of the password
        if (password.length < 8) {
          indicatorElement.innerHTML = '<span class="label">Password Strength:</span> Weak';
          indicatorElement.className = 'weak';
          return;
        }
    
        // Check if the password contains both uppercase and lowercase characters
        if (!/[a-z]/.test(password) || !/[A-Z]/.test(password)) {
        indicatorElement.innerHTML = '<span class="label">Password Strength:</span> Medium';
          indicatorElement.className = 'medium';
          return;
        }
    
        // Check if the password contains at least one digit
        if (!/\d/.test(password)) {
          indicatorElement.innerHTML = '<span class="label">Password Strength:</span> Medium';
          indicatorElement.className = 'medium';
          return;
        }
    
        // Check if the password contains at least one special character
        if (!/[!@#$%^&*]/.test(password)) {
          indicatorElement.innerHTML = '<span class="label">Password Strength:</span> Medium';
          indicatorElement.className = 'medium';
          return;
        }
    
        // If all checks pass, the password is considered strong
        indicatorElement.innerHTML = '<span class="label">Password Strength:</span> Strong';
        indicatorElement.className = 'strong';
        
    },

    init : () => {
        
        AdminInstitute.getAvatarItems({"type": AdminInstitute.typeSelected, "taxonomy": AdminInstitute.taxonomySelected });
        AdminInstitute.getUserEquippedGears();
        
        // if active tab is gender select and if gender is already selected trigger click continue
        if( $(".gender-select.tab").hasClass("active") ){
            if($(".gender-select.tab.active").attr("data-relationship").length > 0 ){
                setTimeout( () => {
                    $(".gender-select.tab.active .btn-select-gender").trigger("click");
                }, 200);
            }
        }

    }
}

function getURLParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

  
AdminInstitute.init();

// event listeners

$(document).on("click", ".select-group-group label", e => {
    $(".select-group-group label").removeClass("active")
    $(e.currentTarget).addClass("active")
    
    AdminInstitute.selectedAvatars.group =  $(e.currentTarget).find("input").val();
    
});

$(document).on("change", ".select-group .select-gender", e => {
    $(".select-group label").removeClass("active")
    $(".select-group .select-gender:checked").parent().parent().addClass("active")
});

$(document).on("click",".btn-select-group", e => {
    $(".choose-avatar-inner .tab").removeClass("active");
    $(".choose-avatar-inner .tab.color-select").addClass("active")

})

$(document).on("click",".avatars-categories__list-item.item-avatar", e => {
    let colorHex = $(e.currentTarget).attr("data-colorhex");
    let subType = $(e.currentTarget).attr("data-subtype");
    let secondaryColor = $(e.currentTarget).attr("data-secondarycolor");
    let itemId = $(e.currentTarget).attr("data-id")
    let eyeColor = $(e.currentTarget).attr("data-eyecolor")
    let eyeBrowColor = $(e.currentTarget).attr("data-eyebrowcolor")
    let eyeLashColor = $(e.currentTarget).attr("data-eyelashcolor")
    //console.log("colorHex", colorHex)

    $(e.currentTarget).parent().find(".item-avatar").removeClass("active")
    $(e.currentTarget).addClass("active");
    
    console.log("selectAvatarItem", subType)

    switch(subType){
        case "hijabs":
            $(".hair, .hair-back, .ears").remove();
            $(".removed-on-hijabs").remove();
            AdminInstitute.avatarItems.map( e => {
                
                if( e.ID == itemId){
                    item = e;
                    console.log("test e.ID",e.ID, itemId, item)
                    let target = $(".avatars-hero .hijabs-preview")
                    target.find("img").attr("src", item.image);
                    target.find("img").css({"display":"block","width":"117px","margin-left":"0px","margin-top":"0px"})
                    target.css({"top":`37px`,});
                    target.attr("id",item.slug)

                    switch(item.slug){
                        case "hijabs-10": case "hijabs-11":
                            target.find("img").css({"display":"block","margin-top":"12px"})
                            break;
                        case "hijabs-6":
                            target.css({"top":`49px`});
                            target.find("img").css({"display":"block","width":"151px","margin-left":"-11px"})
                        break;
                        case "hijabs-5":
                            target.css({"top":`49px`});
                            break;
                        case "hijabs-1":
                            target.css({"top":`46px`});
                            break;
                    }
                }
            })

           
            
            break;

        case "skin-color":
            $("svg.head path.skin-color, svg.body path.skin-color").attr("fill",colorHex)
            $("svg.head path.skin-color-shaded, svg.body path.skin-color-shaded").attr("fill",secondaryColor)
            AdminInstitute.selectedAvatars.skinColor = itemId
        break;

        case "hair-color":
            $("svg.hair path, svg.hair-back path, svg.head path.hair-color").attr("fill",colorHex)
            $("svg.head path.eyebrow").attr("fill", eyeBrowColor)
            $("svg.head path.eyes").attr("fill", eyeColor)
            $("svg.head path.eyelash").attr("fill", eyeLashColor)

            AdminInstitute.selectedAvatars.hairColor = itemId
        break;

        case "hairstyle":
            AdminInstitute.selectedAvatars.hairStyle = itemId
            AdminInstitute.avatarItems.map( e => {
                if(e.ID == itemId){
                    //console.log("hairstyle", e, $(e.hair_style_front).attr("height"), $(e.hair_style_front).attr("width"), $(e.hair_style_front).attr("viewBox"), e.hair_style_front)
                    
                    $(".hair")
                        .attr("width",$(e.hair_style_front).attr("width"))
                        .attr("height",$(e.hair_style_front).attr("height"))
                        .attr("viewBox",$(e.hair_style_front).attr("viewBox"))
                        .html(e.hair_style_front).attr("data-hairstyle", e.slug).removeClass("default");

                    $(".hair-back")
                        .attr("width",$(e.hair_style_back).attr("width"))
                        .attr("height",$(e.hair_style_back).attr("height"))
                        .attr("viewBox",$(e.hair_style_back).attr("viewBox"))
                        .html(e.hair_style_back).attr("data-hairstyle", e.slug);
                    
                    if(AdminInstitute.selectedAvatars.hairColor != ""){
                        AdminInstitute.avatarItems.map( eachHaircolor => {
                            if(eachHaircolor.ID == AdminInstitute.selectedAvatars.hairColor){
                                $("svg.hair path, svg.hair-back path, svg.head path.hair-color").attr("fill",eachHaircolor.color_hex)
                            }
                        });
                    } else {
                        
                        $("svg.hair path, svg.hair-back path, svg.head path.hair-color").attr("fill",'#772400')
                    }
                }
            })
        break;
    }


    // move to the next category after selecting curent category
    let nextCategoryButton = $(".avatars-categories__list-sub-categories button.active").parent().next().find("button");
    if($(nextCategoryButton).length > 0){
        $(nextCategoryButton).trigger("click")
    }
    
});

$(document).on("click",".avatars-categories__list-sub-categories button", e => {
    AdminInstitute.subTypeSelected = $(e.currentTarget).attr("data-termid");
    AdminInstitute.getAvatarItems({"type": AdminInstitute.typeSelected, "taxonomy": AdminInstitute.taxonomySelected });
});

$(document).on("click",".btn-select-avatar", e => {
    AdminInstitute.saveAvatar();
});

$(document).on("click", ".btn-select-gender", e => {
    $(e.currentTarget).fadeTo("fast",.3);
    AdminInstitute.selectedAvatars.gender = $(".select-gender:checked").val();

    AdminInstitute.saveGender()
        .then( e => {
            AdminInstitute.apiResponseAvatarItems = "";
            $(".choose-avatar-inner .tab").removeClass("active");
            $(".choose-avatar-inner .tab.color-select").addClass("active");
            $(".choose-avatar-inner").addClass("wide");

            AdminInstitute.init();

        })
        .catch (e => {
            console.log("error selecting gender", e )
        })
});

$(document).on("click",".btn-set-password", e => {
    //$(".choose-avatar-inner .tab").removeClass("active");
    //$(".choose-avatar-inner .tab.gender-select").addClass("active");
    $(".btn-set-password").fadeTo("fast",.3)
    AdminInstitute.api.savePassword()
        .then( e => { })
        .catch( e => { console.log("error", error )})
})

$(document).on('touchstart', ".btn-select-gender", function(e) {
    $(".btn-select-gender").trigger("click");
});

$(document).on('touchstart', ".btn-select-avatar", function(e) {
    $(".btn-select-avatar").trigger("click");
});


$(document).on("keyup",".input-password", e => {
    let pwd1 = $(".input-password[name=password]").val();
    let pwd2 = $(".input-password[name=confirm_password]").val();

    if( (pwd1 == pwd2) && pwd1.length > 0 && pwd2.length > 0 ){
        $(".set-password .btn-set-password").removeAttr("disabled")
        AdminInstitute.checkPasswordStrength(pwd1, document.getElementById("pwd-indicator"));

        if(pwd1.length > 0 || pwd2.length > 0 &&  (pwd1 != pwd2)){
            document.getElementById("pwd-indicator").innerHTML = 'Password match!';
            $("#pwd-indicator").removeClass("mismatch").addClass("match");
            $(".input.password2").removeClass("error")
            AdminInstitute.password = pwd1;
        }

    }else{
        
        if(pwd1.length > 0 || pwd2.length > 0 &&  (pwd1 != pwd2)){
            document.getElementById("pwd-indicator").innerHTML = 'Password did not match!';
            $("#pwd-indicator").removeClass("match").addClass("mismatch");
            $(".input.password2").addClass("error")
        }
        $(".set-password .btn-set-password").attr("disabled","disabled")
    }
})

$(document).on("click",".btn-view-password", e => {
    $(e.currentTarget).toggleClass("hide-password")
    
    if( $(e.currentTarget).hasClass("hide-password")){
        $(e.currentTarget).parent().find(".input-password").attr("type","text");
    }else{
        $(e.currentTarget).parent().find(".input-password").attr("type","password");
    }
})


