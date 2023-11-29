$ = jQuery;
let AvatarStore = {
    typeSelected: "",
    taxonomySelected: "",
    subTypeSelected: "",
    avatarItems: "",
    saveAvatarTimeout: "",
    subCategories: {},

    selectedAvatars: {
        group: "",
        skinColor: "",
        hairStyle: "",
        hairColor: "",
        gender: "",
        hijabs: "",
    },

    getUserEquippedGears: () => {
        $.ajax({
            url: `${safarObject.apiBaseurl}/user/gears`,
            data: {
                type,
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
            },
            success: (d) => {
                // console.log('dd', d)

                let with_facial_features = true;
                if( d.facial_features.toLowerCase() == 'without facial features'){
                    with_facial_features = false;
                }
                hair = `<svg class="hijabs-hair first hair" width="124" height="85" viewBox="0 0 124 85" fill="none" xmlns="http://www.w3.org/2000/svg" >
                            <path d="M114.279 42.4839C118.276 41.5302 120.155 40.308 124 38.9268C106.884 37.4085 110.565 13.895 90.7154 5.36113C58.2313 -6.08319 29.0909 3.57981 20.7479 8.71002C5.37995 18.1593 1.72583 22.27 0.0594182 42.2811C-0.986172 54.86 12.0783 78.072 14.8066 85C17.78 82.0786 14.2675 39.6228 15.5581 36.1643C16.3096 34.1638 23.204 22.5495 26.2101 22.3906C32.7613 22.0453 40.4944 44.6928 63.688 43.772C54.2068 38.9432 48.9244 31.9166 43.5277 21.848C45.3738 22.8948 47.2308 24.1664 49.0715 25.7285C68.5075 42.177 86.1736 46.0027 102.5 44.205C103.557 60.9549 103.045 73.1227 103.045 79.3984C110.925 75.0575 114.944 53.8405 114.274 42.4839H114.279Z" fill="#772400"/>
                        </svg>
                        
                        <svg class="hijabs-hair hair-back" width="135" height="118" viewBox="0 0 135 118" fill="none" xmlns="http://www.w3.org/2000/svg"  >
                            <path d="M127.867 106.314C146.813 64.8107 122.022 0.0971595 122.022 0.0971595C121.816 -0.697005 12.8174 3.63826 12.8174 3.63826C-0.6678 48.2692 -1.66008 78.9534 1.49569 97.7413C0.329901 87.7327 -0.640688 57.821 10.5617 17.4872C0.514257 68.4008 0.704037 97.725 10.7841 106.319C4.37492 95.3915 6.29441 63.5215 7.87771 59.6976C7.88314 70.6038 6.7065 92.4106 13.0397 107.962C38.3184 127.169 100.723 113.505 116.199 110.192C116.871 108.892 117.614 91.4207 118.037 87.1887C118.915 88.6465 119.213 108.12 119.555 109.507C128.898 100.738 135.393 74.8683 126.354 25.9945C136.624 59.2244 132.823 84.7954 127.856 106.314H127.867Z" fill="#772400"/>
                        </svg>`
                        
                if(d.gender != "male"){
                    if(!safarObject.isUserStudent){
                        
                        hair = `<div class="hijabs hijabs-preview" style="position: absolute; z-index: 2; top: 44px; left: 171px;">
                                <img src="/wp-content/themes/buddyboss-theme-child/assets/img/defaul-hijab.png" style="width: 118px; display: block;"/>
                                </div>`
                    
                    }
                }
                

                let heroFemale = `

                    ${
                        (d.clothing.headwears) ? `<div class="headwears" style="
                            position: absolute;
                            top: ${ d.clothing.headwears.positioning.top ? d.clothing.headwears.positioning.top+'px' : `19px`};
                            left: ${ d.clothing.headwears.positioning.left ? d.clothing.headwears.positioning.left+'px' : `171px`};
                            z-index: 3;
                        ">
                            <img src="${d.clothing.headwears.image}" style="width: ${ d.clothing.headwears.positioning.image_width ? d.clothing.headwears.positioning.image_width+'px' : `139px`}" />
                        </div>` : `<div class="headwears" style="position: absolute;z-index: 3;"><img src="" style="display:none"/></div>`
                    }

                    ${
                        (d.clothing.hijabs) ? `<div class="hijabs" style="
                            position: absolute;
                            top: ${ d.clothing.hijabs.positioning.top ? d.clothing.hijabs.positioning.top+'px' : `46px`};
                            left: ${ d.clothing.hijabs.positioning.left ? d.clothing.hijabs.positioning.left+'px' : `163px`};
                            z-index: 2;
                        ">
                            <img src="${d.clothing.hijabs.image}" style="width: ${ d.clothing.hijabs.positioning.image_width ? d.clothing.hijabs.positioning.image_width+'px' : `130px`}" />
                        </div>` : `${hair}`
                    }

                    <svg width="126" class="head" height="131" viewBox="0 0 126 131" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M50.6582 103.394V122.611C50.6582 126.741 56.3154 130.095 63.2898 130.095C70.2642 130.095 75.9214 126.741 75.9214 122.611V103.394H50.6582Z" fill="#EBAE8C" class="skin-color-shaded"/>
                        
                        ${ d.clothing.hijabs ? `` : `<path d="M0.76157 80.9432C3.64789 92.4045 14.7104 99.678 23.0807 98.6232C31.4511 97.5736 33.1304 88.1957 31.7449 77.1647C30.3595 66.1337 22.4457 58.0153 14.0754 59.0911C2.6403 60.5657 -1.95157 70.164 0.756322 80.9379L0.76157 80.9432Z" fill="#FFC19F" class="skin-color removed-on-hijabs" "/>`}
                        
                        ${ d.clothing.hijabs ? `` : `<path d="M10.8007 83.4988C11.4095 88.3373 15.3611 91.8219 19.6276 91.2866C23.8941 90.7513 26.8592 86.3956 26.2504 81.5571C25.6417 76.7185 21.69 73.234 17.4288 73.7692C13.1623 74.3045 10.1972 78.6602 10.806 83.4988H10.8007Z" fill="#E0956C" class="skin-color-shaded removed-on-hijabs"/>`}

                        ${ d.clothing.hijabs ? `` : `<path d="M124.742 82.6225C121.552 93.9998 110.29 100.969 101.951 99.6938C93.612 98.4133 92.1846 88.9986 93.8691 78.0096C95.5537 67.0206 103.683 59.1173 112.021 60.424C123.415 62.2083 127.744 71.9273 124.742 82.6225Z" fill="#FFC19F" class="skin-color removed-on-hijabs" "/>`}

                        ${ d.clothing.hijabs ? `` : `<path d="M114.021 84.9001C113.281 89.7176 109.24 93.0973 104.989 92.4465C100.739 91.7958 97.8942 87.3613 98.6342 82.5386C99.3741 77.721 103.415 74.3414 107.666 74.9921C111.917 75.6429 114.761 80.0773 114.026 84.8949L114.021 84.9001Z" fill="#E0956C removed-on-hijabs" class="skin-color-shaded"/>`}

                        ${ d.clothing.hijabs ? `` : `<path d="M118.177 38.2045C121.845 75.5273 94.3572 99.8617 62.996 99.4366C31.6348 99.0115 6.16689 71.5127 9.84039 35.271C11.8766 15.1402 33.2144 -0.382984 63.1744 0.215272C94.5356 0.845016 115.569 11.6609 118.177 38.2045Z" fill="#772400" class="hair-color removed-on-hijabs"/>`}

                        <path d="M108.663 31.0201C111.413 37.5485 107.157 95.3327 100.639 103.225C93.7274 111.601 77.0812 119.105 63.1744 119.111C50.3958 119.111 29.6091 111.213 24.4767 101.835C18.4049 90.7408 15.3611 39.5374 17.4288 33.5024C24.4137 13.1197 41.6792 12.6631 64.2502 12.9675C85.3676 13.2509 101.526 14.0748 108.663 31.0201Z" fill="#FFC19F" class="skin-color" "/>

                        ${ (with_facial_features) ? `<path d="M80.2877 94.1416C80.3139 101.2 72.5943 106.952 63.0432 106.988C53.4973 107.025 45.7357 101.326 45.7095 94.2676C45.6833 87.2092 53.8017 88.5054 63.3528 88.0016C72.8882 87.4978 80.2667 87.0833 80.2877 94.1469V94.1416Z" fill="#630900"/>
                        <path d="M64.0455 100.303C58.8869 100.319 54.3632 101.861 51.7708 104.17C54.8303 106.044 58.7557 107.167 63.0432 107.151C67.8712 107.136 72.2322 105.677 75.4019 103.341C72.715 101.473 68.6269 100.287 64.0455 100.303Z" fill="#D36553"/>
                        <path d="M75.4019 88.0437C72.3267 87.3667 68.1074 87.5871 63.3581 87.839C57.9948 88.1224 53.0828 87.8495 49.7609 88.7574C51.1358 91.5912 56.3102 93.6904 62.4869 93.6694C69.1097 93.6484 74.557 91.1924 75.4072 88.0384L75.4019 88.0437Z" fill="white"/>
                        <path d="M67.1995 84.5537C66.9528 84.5537 66.7324 84.3701 66.6957 84.1129C66.4228 82.3077 65.0164 81.1269 63.1114 81.1006C61.196 81.0744 59.7265 82.2394 59.4484 84.0027C59.4012 84.2861 59.1388 84.4803 58.8554 84.433C58.572 84.3911 58.3778 84.1234 58.4251 83.84C58.7819 81.5835 60.7131 80.0301 63.1272 80.0616C65.5254 80.0931 67.3727 81.6569 67.7243 83.9555C67.7663 84.2389 67.5721 84.5013 67.2887 84.5485H67.2048L67.1995 84.5537Z" fill="#C4735B"/>
                        <path d="M99.117 48.8682C98.7706 48.8472 98.4295 48.7003 98.1671 48.4326C94.8085 45.0268 88.107 43.7043 82.56 45.3731C81.7886 45.6093 80.9699 45.1685 80.7337 44.3918C80.5028 43.6151 80.9384 42.8017 81.7151 42.5655C88.3326 40.5766 96.1362 42.1824 100.261 46.3807C100.828 46.958 100.823 47.8869 100.24 48.4536C99.9304 48.758 99.5211 48.8997 99.117 48.8735V48.8682Z" fill="#772400" class="eyebrow"/>
                        <path d="M28.0032 48.2646C27.5991 48.2699 27.1951 48.1124 26.9012 47.7923C26.3501 47.1993 26.3869 46.2704 26.9851 45.7194C31.3094 41.7258 39.1812 40.503 45.6938 42.8069C46.4599 43.0797 46.8588 43.9194 46.5859 44.6803C46.313 45.4465 45.4734 45.8454 44.7124 45.5725C39.2546 43.636 32.4901 44.6226 28.9741 47.871C28.7012 48.1282 28.3496 48.2541 28.0032 48.2594V48.2646Z" fill="#772400" class="eyebrow"/>
                        <path d="M50.8 64.2339C54.253 69.4293 53.8122 75.1232 50.779 77.2644C46.2081 80.4918 35.408 81.3367 31.3881 76.5979C27.4942 72.0008 29.3992 63.6986 35.7701 60.1668C41.3433 57.0758 47.4623 59.2117 50.8 64.2392V64.2339Z" fill="#F9FEFF"/>
                        <path d="M30.6901 62.6334C35.6599 54.6567 47.5673 57.4643 50.9154 64.4072C47.4675 59.2118 41.0757 57.5902 35.9223 60.445C32.7368 62.2136 30.4697 65.3833 29.8557 68.9256C29.3677 71.7489 29.9974 74.9291 31.6138 76.8394C29.7508 75.5904 27.0376 68.4953 30.6954 62.6334H30.6901Z" fill="#772400" class="eyelash"/>
                        <path d="M49.5195 67.6712C50.9311 71.2608 49.2466 75.3279 45.7568 76.75C42.2669 78.1722 38.2943 76.4142 36.8826 72.8246C35.4709 69.2351 37.1555 65.168 40.6453 63.7458C44.1352 62.3237 48.1078 64.0817 49.5195 67.6712Z" fill="#772400" class="eyes"/>
                        <path d="M48.9632 67.0469C49.3568 68.044 48.8897 69.1723 47.9189 69.5659C46.948 69.9595 45.846 69.4714 45.4576 68.4743C45.064 67.4772 45.5311 66.349 46.502 65.9554C47.4728 65.5618 48.5749 66.0498 48.9632 67.0469Z" fill="#FEFFFE"/>
                        <path d="M75.2602 64.276C71.8648 69.4609 72.3686 75.1548 75.4229 77.3065C80.0305 80.5496 90.8411 81.4313 94.8085 76.703C98.6499 72.1216 96.6557 63.809 90.2481 60.2562C84.6381 57.1442 78.5454 59.2591 75.2602 64.276Z" fill="#F9FEFF"/>
                        <path d="M95.3543 62.7437C90.2953 54.7512 78.4194 57.5169 75.15 64.4493C78.5454 59.2696 84.9163 57.669 90.1011 60.5396C93.3076 62.3186 95.6061 65.4988 96.2621 69.0412C96.7817 71.8698 96.1887 75.0447 94.5933 76.9497C96.4406 75.7059 99.075 68.6213 95.3595 62.7437H95.3543Z" fill="#772400" class="eyelash"/>
                        <path d="M89.2457 67.7398C90.6574 71.3293 88.9729 75.3964 85.483 76.8186C81.9932 78.2408 78.0206 76.4827 76.6089 72.8932C75.1972 69.3036 76.8818 65.2366 80.3716 63.8144C83.8614 62.3922 87.8341 64.1502 89.2457 67.7398Z" fill="#772400" class="eyes"/>
                        <path d="M88.6895 67.1152C89.0831 68.1123 88.616 69.2406 87.6451 69.6342C86.6743 70.0278 85.5722 69.5397 85.1839 68.5426C84.7903 67.5455 85.2574 66.4172 86.2282 66.0237C87.1991 65.6301 88.3011 66.1181 88.6895 67.1152Z" fill="#FEFFFE"/>` : `` }
                        
                    </svg>

                    <svg xmlns="http://www.w3.org/2000/svg" class="body" width="183" height="237" viewBox="0 0 405 525" fill="none">
                        

                        ${
                            (d.clothing.shoes && d.clothing?.shoes?.item_id != 225858) ? `` : `<path d="M280.799 523.5C267.899 522.2 257.799 519.9 257.499 517.2C256.399 506.9 255.799 502.2 255.399 498.7C258.399 500.3 261.699 502.2 265.399 504.7C275.099 511.5 277.599 517.5 280.799 523.5Z" fill="#FFC19F" class="skin-color"/>`
                        }

                        <path d="M280.8 523.5C277.6 517.5 275.2 511.5 265.4 504.8C261.7 502.3 258.4 500.3 255.4 498.8C255 495.5 254.7 493.2 254.2 488.4L253.4 483.3L225.9 317.2C223.3 296.1 215 253.2 202.6 250.5C202.4 250.5 202.2 250.4 202 250.4V86H269.2L281.4 317.2L290.9 488.4C303.7 492.1 322.1 500.2 332.9 510.5C335.2 512.7 337.2 515 338.7 517.3C343.8 525.4 306.5 526.3 280.8 523.5Z" fill="#FFC19F" class="skin-color"/>
                        
                        ${
                            (d.clothing.shoes && d.clothing?.shoes?.item_id != 225858) ? `` : `<path d="M147.691 498.9C147.291 502.4 146.691 507.1 145.591 517.4C145.291 520.1 135.191 522.3 122.291 523.7C125.491 517.7 127.891 511.7 137.691 505C141.391 502.4 144.691 500.4 147.691 498.9Z" fill="#FFC19F" class="skin-color"/>`
                        }
                        
                        <path d="M201.191 86.1V250.6C200.991 250.6 200.791 250.6 200.591 250.7C188.191 253.4 179.791 296.3 177.291 317.4L149.791 483.4L148.891 488.6C148.391 493.4 148.091 495.7 147.691 499C144.691 500.6 141.391 502.5 137.691 505C127.891 511.6 125.491 517.6 122.291 523.6C96.4915 526.3 59.2915 525.5 64.4915 517.3C65.9915 514.9 67.9915 512.7 70.2915 510.5C81.0915 500.3 99.4915 492.1 112.291 488.4L121.791 317.2L133.991 86H201.191V86.1Z" fill="#FFC19F" class="skin-color"/>
                        <path d="M280.075 296.604H221.17C214.947 267.263 205.611 256.223 201.165 254.37V189.908H274.518L280.075 296.604Z" fill="#F46483" class="skin-color"/>
                        <path d="M122.255 296.604H181.16C187.384 267.263 196.719 256.223 201.165 254.37V189.908H127.812L122.255 296.604Z" fill="#F46483" class="skin-color"/>
                        <path d="M68.8841 220.864C64.1914 219.284 45.9097 209.831 44.1631 207.456C67.51 159.535 83.3563 83.6887 111.142 35.8979C115.729 35.465 141.839 50.5314 147.416 51.2567C145.973 70.1396 93.966 182.501 68.8841 220.875L68.8841 220.864Z" fill="#FFC19F" class="skin-color"/>
                        <path d="M28.5869 268.263C33.2097 259.828 38.7757 246.526 52.6791 222.881C54.2627 220.179 67.4907 221.454 65.5112 223.958C54.0182 238.5 41.2443 272.066 28.5869 268.263V268.263Z" fill="#E0956C" class="skin-color-shaded"/>
                        <path d="M14.5322 268.053C19.155 259.617 26.2814 237.4 45.1686 218.798C47.4974 216.505 59.922 219.114 57.9424 221.618C46.4494 236.16 25.0354 270.942 14.5322 268.053V268.053Z" fill="#E0956C" class="skin-color-shaded"/>
                        <path d="M-4.8637e-06 260.728C4.62281 252.293 36.8078 209.824 55.695 191.222C58.0239 188.929 75.374 202.489 74.9082 207.566C79.9619 218.213 67.2695 234.042 67.2346 258.892C67.2346 263.185 61.2843 260.459 60.0034 258.985C56.7081 255.195 57.3718 253.217 52.8654 241.588C50.1173 234.498 41.3491 235.095 36.6914 237.599C18.4214 251.299 8.36065 263.782 -4.8637e-06 260.728H-4.8637e-06Z" fill="#FFC19F" class="skin-color"/>
                        <path d="M105.584 45.9007C121.223 11.7237 156.999 3.63697 174.451 0H202.288L202.262 192.877H131.011L134.609 89.4011L105.584 45.9007Z" fill="#FFC19F" class="skin-color"/>
                        <path d="M335.682 220.864C340.375 219.284 358.657 209.831 360.403 207.456C337.056 159.535 321.21 83.6887 293.425 35.8979C288.837 35.465 262.728 50.5314 257.15 51.2567C258.594 70.1396 310.6 182.501 335.682 220.875L335.682 220.864Z" fill="#FFC19F" class="skin-color"/>
                        <path d="M375.979 268.263C371.357 259.828 365.791 246.526 351.887 222.881C350.304 220.179 337.076 221.454 339.055 223.958C350.548 238.5 363.322 272.066 375.979 268.263V268.263Z" fill="#E0956C" class="skin-color-shaded"/>
                        <path d="M390.034 268.053C385.411 259.617 378.285 237.4 359.398 218.798C357.069 216.505 344.644 219.114 346.624 221.618C358.117 236.16 379.531 270.942 390.034 268.053V268.053Z" fill="#E0956C" class="skin-color-shaded" />
                        <path d="M404.566 260.728C399.944 252.293 367.759 209.824 348.871 191.222C346.543 188.929 329.192 202.489 329.658 207.566C324.605 218.213 337.297 234.042 337.332 258.892C337.332 263.185 343.282 260.459 344.563 258.985C347.858 255.195 347.195 253.217 351.701 241.588C354.449 234.498 363.217 235.095 367.875 237.599C386.145 251.299 396.206 263.782 404.566 260.728V260.728Z" fill="#FFC19F" class="skin-color"/>
                        <path d="M298.981 45.9007C283.342 11.7237 247.566 3.63697 230.114 0H202.277L202.304 192.877H273.554L269.956 89.4011L298.981 45.9007Z" fill="#FFC19F" class="skin-color"/>
                        <path d="M105.584 45.9007C121.223 11.7237 156.999 3.63697 174.451 0H202.288L202.262 192.877H131.011L134.609 89.4011L105.584 45.9007Z" fill="#FFC19F" class="skin-color"/>
                        <path d="M298.981 45.9007C283.342 11.7237 247.566 3.63697 230.114 0H202.277L202.304 192.877H273.554L269.956 89.4011L298.981 45.9007Z" fill="#FFC19F" class="skin-color"/>
                    </svg>

                    ${
                        (d.clothing.skirts) ? `<div class="skirts" style="
                            position: absolute;
                            z-index: 1;
                            top: ${ d.clothing.skirts.positioning.top ? d.clothing.skirts.positioning.top+'px' : `244px`};
                            left: ${ d.clothing.skirts.positioning.left ? d.clothing.skirts.positioning.left+'px' : `164px`};
                        ">
                            <img src="${d.clothing.skirts.image}" style="width: ${ d.clothing.skirts.positioning.image_width ? d.clothing.skirts.positioning.image_width+'px' : `135px`}" />
                        </div>` : `<div class="skirts" style="position: absolute;z-index: 1;"><img src="" style="display:none"/></div>` }

                    ${
                        (d.clothing.jilbabs) ? `<div class="jilbabs" style="
                            position: absolute;
                            z-index: 1;
                            top: ${ d.clothing.jilbabs.positioning.top ? d.clothing.jilbabs.positioning.top+'px' : `174px`};
                            left: ${ d.clothing.jilbabs.positioning.left ? d.clothing.jilbabs.positioning.left+'px' : `158px`};
                        ">
                            <img src="${d.clothing.jilbabs.image}" style="width: ${ d.clothing.jilbabs.positioning.image_width ? d.clothing.jilbabs.positioning.image_width+'px' : `145px`}" />
                        </div>` :
                        (d.clothing.tops && d.clothing.tops.image) ? `<div class="tops" style="
                            position: absolute;
                            left: 200px;
                            z-index: 1;
                            top: ${ d.clothing.tops.positioning.top ? d.clothing.tops.positioning.top+'px' : `170px`};
                            left: ${ d.clothing.tops.positioning.left ? d.clothing.tops.positioning.left+'px' : `170px`};
                        ">
                            <img src="${d.clothing.tops.image}" style="width: ${ d.clothing.tops.positioning.image_width ? d.clothing.tops.positioning.image_width+'px' : `120px`}" />
                        </div>` : `<div class="tops" style="
                            position: absolute;
                            left: 200px;
                            z-index: 1;
                            top: 116px;
                            left: 162px;
                        "><svg xmlns="http://www.w3.org/2000/svg" width="138" height="250" viewBox="0 0 255 250" fill="none">
                            <path d="M252.187 161.125C244.628 168.586 233.762 177.93 226.187 172.125C218.611 166.319 205.187 130.625 194.687 105.625C180.187 81.6245 191.196 63.642 191.196 63.642C191.196 63.642 186.879 176.485 189.034 196.387C191.19 216.289 200.91 233.055 185.798 242.176C170.686 251.297 126.423 249.625 126.423 249.625C126.423 249.625 65.8044 251.297 50.6865 242.176C35.5686 233.055 52.6459 213.289 54.8071 193.387C56.9684 173.485 61.6458 63.6293 61.6458 63.6293C61.6458 63.6293 72.1866 83.1245 56.1866 111.125C46.6865 136.625 34.2456 167.323 26.6866 173.125C20.2083 178.1 8.24004 167.107 0.686604 159.625C-2.81362 150.625 7.68652 127.125 18.1865 99.6245C33.6865 63.642 41.1864 36.6245 48.6864 25.1245C53.9038 17.1245 94.1367 0.704638 98.7625 0.615714H99.4296V0.425161C102.958 0.149919 103.187 0.124512 103.187 0.124512C103.187 0.124512 105.469 14.1164 125.687 14.6245C150.687 14.1245 149.687 0.387051 149.687 0.387051C149.687 0.387051 148.532 0.149919 152.083 0.387051C152.083 0.492913 152.083 0.598776 152.083 0.708873C152.083 0.708873 177.752 3.35782 197.187 16.6245C209.639 25.1245 211.187 49.1245 236.187 101.625C248.187 135.125 259.745 153.659 252.187 161.125Z" fill="#2F50A3"/>
                            <path d="M185.192 89.5963C184.929 89.592 184.677 89.5159 184.481 89.3816C184.285 89.2473 184.157 89.0637 184.122 88.8637C181.106 69.656 182.832 52.2226 189.398 35.5683C189.519 35.3965 189.714 35.2622 189.949 35.188C190.184 35.1138 190.446 35.1043 190.689 35.1611C190.932 35.2179 191.142 35.3375 191.284 35.4997C191.426 35.6618 191.49 35.8566 191.466 36.051C184.999 52.4766 183.295 69.6729 186.294 88.6689C186.321 88.8858 186.238 89.1023 186.063 89.2735C185.887 89.4446 185.632 89.5572 185.352 89.5878L185.192 89.5963Z" fill="#143790"/>
                            <path d="M63.5761 92.035H63.4493C63.1674 92.0044 62.9118 91.8912 62.7361 91.7192C62.5604 91.5471 62.4781 91.3295 62.5065 91.1119C65.5885 71.4469 64.0613 54.1955 57.8201 38.3754C57.7419 38.2649 57.6968 38.1423 57.6881 38.0167C57.6793 37.891 57.7072 37.7655 57.7695 37.6491C57.8318 37.5328 57.9272 37.4285 58.0485 37.344C58.1699 37.2595 58.3142 37.1968 58.4711 37.1605C58.6279 37.1242 58.7933 37.1152 58.9553 37.1342C59.1172 37.1531 59.2716 37.1995 59.4073 37.2701C59.543 37.3406 59.6565 37.4334 59.7396 37.5419C59.8226 37.6503 59.8732 37.7716 59.8876 37.8969C66.2116 53.9415 67.7719 71.4172 64.6457 91.3152C64.6067 91.5128 64.478 91.6935 64.2822 91.8252C64.0865 91.957 63.8364 92.0313 63.5761 92.035Z" fill="#143790"/>
                            <path d="M155.687 0.38348C154.912 5.93876 150.263 17.1245 126.129 17.1245C102.909 17.1245 97.7663 6.49011 96.6865 0.609033C96.6865 0.563087 96.6865 0.521318 96.6865 0.475372L97.4491 0.421072C101.329 0.149573 103.666 0.124512 103.666 0.124512C103.666 0.124512 104.081 12.2793 126.452 12.7805C149.824 13.3068 149.384 0.124512 149.384 0.124512C149.384 0.124512 151.758 0.149573 155.687 0.38348Z" fill="#8A81AF"/>
                            <path d="M126.152 18.1245C97.397 18.1245 95.6989 0.765888 95.6865 0.589747C95.686 0.478814 95.7445 0.371834 95.8502 0.290194C95.9559 0.208554 96.101 0.158271 96.2567 0.149397C96.4138 0.149123 96.5652 0.191262 96.6802 0.26729C96.7953 0.343317 96.8654 0.447557 96.8764 0.558923C96.9322 1.24147 98.5434 17.2922 126.127 17.2922C153.711 17.2922 155.39 1.22385 155.446 0.541309C155.451 0.483481 155.471 0.426846 155.506 0.374637C155.542 0.322429 155.591 0.275669 155.652 0.237027C155.712 0.198386 155.783 0.16862 155.86 0.149429C155.937 0.130237 156.019 0.121997 156.1 0.125177C156.181 0.128358 156.261 0.142897 156.335 0.167965C156.408 0.193033 156.474 0.228139 156.528 0.271279C156.583 0.314418 156.624 0.364746 156.651 0.419389C156.678 0.474032 156.69 0.53192 156.686 0.589747C156.63 0.765888 154.913 18.1245 126.152 18.1245Z" fill="#293341"/>
                            </svg></div>`
                    }

                    ${
                        (d.clothing.socks) ? `<div class="socks" style="
                            position: absolute;
                            z-index: 1;
                            top: ${ d.clothing.socks.positioning.top ? d.clothing.socks.positioning.top+'px' : `379px`};
                            left: ${ d.clothing.socks.positioning.left ? d.clothing.socks.positioning.left+'px' : `166px`};
                            z-index: 0;
                        ">
                            <img src="${d.clothing.socks.image}" style="width: ${ d.clothing.socks.positioning.image_width ? d.clothing.socks.positioning.image_width+'px' : `127px`}" />
                        </div>` : `<div class="socks empty-placeholder" style="position: absolute;z-index: 1;"><img src="" style="display:none"/></div>`
                    }

                    ${
                        (d.clothing.shoes) ? `<div class="shoes" style="
                            position: absolute;
                            z-index: 1;
                            top: ${ d.clothing.shoes.positioning.top ? d.clothing.shoes.positioning.top+'px' : `383px`};
                            left: ${ d.clothing.shoes.positioning.left ? d.clothing.shoes.positioning.left+'px' : `161px`};
                            z-index: 0;
                        ">
                            <img src="${d.clothing.shoes.image}" style="width: ${ d.clothing.shoes.positioning.image_width ? d.clothing.shoes.positioning.image_width+'px' : `136px`}" />
                        </div>` : `<div class="shoes empty-placeholder" style="position: absolute;z-index: 1;"><img src="" style="display:none"/></div>`
                    }

                    ${
                        (d.clothing.socks || d.clothing.shoes) ? `<style>
                            @media (max-width: 768px) {
                                .socks {
                                    top: ${d.clothing.socks?.positioning?.top - 8}px !important;
                                }

                                .shoes {
                                    top: ${d.clothing.shoes?.positioning?.top - 8}px !important;
                                }
                            }
                        </style>` : ``
                    }

                    ${
                        (d.clothing.trousers) ? `<div class="trousers" style="
                            position: absolute;
                            z-index: 1;
                            top: ${ d.clothing.trousers.positioning.top ? d.clothing.trousers.positioning.top+'px' : `260px`};
                            left: ${ d.clothing.trousers.positioning.left ? d.clothing.trousers.positioning.left+'px' : `189px`};
                            z-index: 0;
                        ">
                            <img src="${d.clothing.trousers.image}" style="width: ${ d.clothing.trousers.positioning.image_width ? d.clothing.trousers.positioning.image_width+'px' : `84px`}" />
                        </div>` : `<div class="trousers empty-placeholder" style="position: absolute;z-index: 0; top: 260px; left:189px"><img src="" style="display:none; width:84px"/></div>`
                    }

                    ${
                        (!d.clothing.trousers && !d.clothing.skirts) ? `<div class="trousers" style="
                            position: absolute;
                            z-index: 1;
                            top: 261px;
                            left: 186px;
                            z-index: 0;
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="90" height="120" viewBox="0 0 159 221" fill="none">
                                <path d="M70.4065 5.0324C57.1932 5.21528 12.9178 0.776854 12.9178 0.776854C8.99024 90.0854 6.03768 175.784 4.42963 215.233L50.1856 216.967C53.2865 189.439 60.3627 108.271 68.3721 60.1509C69.992 53.554 83.1586 53.3422 84.4295 59.5552C94.7928 110.082 103.552 183.57 107.09 219.848L152.569 217.136C149.335 167.926 142.187 79.3365 138.083 2.01367C138.083 2.01367 97.7388 4.63214 70.4068 5.01352L70.4065 5.0324Z" fill="#5A76A8"/>
                                <path d="M12.1843 17.0413C36.3509 23.2079 95.6843 31.8413 139.684 17.0413" stroke="#7FA1D3" stroke-width="2" stroke-linecap="round"/>
                                <path d="M16.1843 18.0413C14.0176 74.5413 9.88428 189.441 10.6843 197.041" stroke="#7FA1D3" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 4"/>
                                <path d="M134.184 19.0413C139.018 75.7079 146.984 191.241 146.184 200.041" stroke="#7FA1D3" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 4"/>
                                <path d="M31.126 6.04126C30.7927 8.70793 30.326 14.7413 31.126 17.5413" stroke="#7FA1D3" stroke-width="7" stroke-linecap="round"/>
                                <path d="M58.126 10.0413C57.7927 12.7079 57.326 18.7413 58.126 21.5413" stroke="#7FA1D3" stroke-width="7" stroke-linecap="round"/>
                                <path d="M120.684 7.04126C121.018 9.70793 121.484 15.7413 120.684 18.5413" stroke="#7FA1D3" stroke-width="7" stroke-linecap="round"/>
                                <path d="M93.6843 10.0413C94.0176 12.7079 94.4843 18.7413 93.6843 21.5413" stroke="#7FA1D3" stroke-width="7" stroke-linecap="round"/>
                                <path d="M82.582 15.6491C82.5607 16.5398 82.2754 17.4042 81.7623 18.1326C81.2492 18.8611 80.5313 19.4208 79.6998 19.7409C78.8682 20.0609 77.9603 20.127 77.0912 19.9306C76.2221 19.7342 75.4308 19.2842 74.8177 18.6377C74.2046 17.9911 73.7972 17.1771 73.6472 16.2989C73.4972 15.4206 73.6113 14.5175 73.975 13.7041C74.3388 12.8908 74.9359 12.2037 75.6905 11.73C76.4452 11.2562 77.3235 11.0172 78.2142 11.0432C79.4016 11.0824 80.5258 11.5877 81.3434 12.4498C82.1609 13.3119 82.6059 14.4613 82.582 15.6491Z" fill="#E2C676"/>
                                <path d="M102.561 219.371L155.886 218.2L155.487 200.073L102.163 201.245L102.561 219.371Z" fill="#7FA1D3"/>
                                <path d="M54.2288 199.143L0.957764 196.48L0.0525954 214.589L53.3236 217.251L54.2288 199.143Z" fill="#7FA1D3"/>
                            </svg>
                        </div>` : ``
                    }
                `

                let heroMale = `

                        ${
                            (d.clothing.headwears) ? `<div class="headwears" style="
                                position: absolute;
                                top: ${ d.clothing.headwears.positioning.top ? d.clothing.headwears.positioning.top+'px' : `20px`};
                                left: ${ d.clothing.headwears.positioning.left ? d.clothing.headwears.positioning.left+'px' : `171px`};
                                z-index: 3;
                            ">
                                <img src="${d.clothing.headwears.image}" style="width: ${ d.clothing.headwears.positioning.image_width ? d.clothing.headwears.positioning.image_width+'px' : `118px`}" />
                                <style>
                                    svg.hair path {
                                        display: none;
                                    }
                                </style>
                            </div>` : ` 
                            <div class="headwears" style="position: absolute;z-index: 3;"><img src="" style="display:none"/></div>
                            `
                        }
                    
                        <svg width="124" height="85" viewBox="0 0 124 85" fill="none" xmlns="http://www.w3.org/2000/svg" class="hair">
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

                            ${ (with_facial_features) ? `<path d="M80.2877 94.1416C80.3139 101.2 72.5943 106.952 63.0432 106.988C53.4973 107.025 45.7357 101.326 45.7095 94.2676C45.6833 87.2092 53.8017 88.5054 63.3528 88.0016C72.8882 87.4978 80.2667 87.0833 80.2877 94.1469V94.1416Z" fill="#630900"/><path d="M64.0455 100.303C58.8869 100.319 54.3632 101.861 51.7708 104.17C54.8303 106.044 58.7557 107.167 63.0432 107.151C67.8712 107.136 72.2322 105.677 75.4019 103.341C72.715 101.473 68.6269 100.287 64.0455 100.303Z" fill="#D36553"/>
                            <path d="M75.4019 88.0437C72.3267 87.3667 68.1074 87.5871 63.3581 87.839C57.9948 88.1224 53.0828 87.8495 49.7609 88.7574C51.1358 91.5912 56.3102 93.6904 62.4869 93.6694C69.1097 93.6484 74.557 91.1924 75.4072 88.0384L75.4019 88.0437Z" fill="white"/><path d="M67.1995 84.5537C66.9528 84.5537 66.7324 84.3701 66.6957 84.1129C66.4228 82.3077 65.0164 81.1269 63.1114 81.1006C61.196 81.0744 59.7265 82.2394 59.4484 84.0027C59.4012 84.2861 59.1388 84.4803 58.8554 84.433C58.572 84.3911 58.3778 84.1234 58.4251 83.84C58.7819 81.5835 60.7131 80.0301 63.1272 80.0616C65.5254 80.0931 67.3727 81.6569 67.7243 83.9555C67.7663 84.2389 67.5721 84.5013 67.2887 84.5485H67.2048L67.1995 84.5537Z" fill="#C4735B"/><path d="M99.117 48.8682C98.7706 48.8472 98.4295 48.7003 98.1671 48.4326C94.8085 45.0268 88.107 43.7043 82.56 45.3731C81.7886 45.6093 80.9699 45.1685 80.7337 44.3918C80.5028 43.6151 80.9384 42.8017 81.7151 42.5655C88.3326 40.5766 96.1362 42.1824 100.261 46.3807C100.828 46.958 100.823 47.8869 100.24 48.4536C99.9304 48.758 99.5211 48.8997 99.117 48.8735V48.8682Z" fill="#772400" class="eyebrow"/>
                            <path d="M28.0032 48.2646C27.5991 48.2699 27.1951 48.1124 26.9012 47.7923C26.3501 47.1993 26.3869 46.2704 26.9851 45.7194C31.3094 41.7258 39.1812 40.503 45.6938 42.8069C46.4599 43.0797 46.8588 43.9194 46.5859 44.6803C46.313 45.4465 45.4734 45.8454 44.7124 45.5725C39.2546 43.636 32.4901 44.6226 28.9741 47.871C28.7012 48.1282 28.3496 48.2541 28.0032 48.2594V48.2646Z" fill="#772400" class="eyebrow"/>
                            <path d="M50.8 64.2339C54.253 69.4293 53.8122 75.1232 50.779 77.2644C46.2081 80.4918 35.408 81.3367 31.3881 76.5979C27.4942 72.0008 29.3992 63.6986 35.7701 60.1668C41.3433 57.0758 47.4623 59.2117 50.8 64.2392V64.2339Z" fill="#F9FEFF"/>
                            <path d="M30.6901 62.6334C35.6599 54.6567 47.5673 57.4643 50.9154 64.4072C47.4675 59.2118 41.0757 57.5902 35.9223 60.445C32.7368 62.2136 30.4697 65.3833 29.8557 68.9256C29.3677 71.7489 29.9974 74.9291 31.6138 76.8394C29.7508 75.5904 27.0376 68.4953 30.6954 62.6334H30.6901Z" fill="#772400" class="eyelash"/>
                            <path d="M49.5195 67.6712C50.9311 71.2608 49.2466 75.3279 45.7568 76.75C42.2669 78.1722 38.2943 76.4142 36.8826 72.8246C35.4709 69.2351 37.1555 65.168 40.6453 63.7458C44.1352 62.3237 48.1078 64.0817 49.5195 67.6712Z" fill="#772400" class="eyes"/>
                            <path d="M48.9632 67.0469C49.3568 68.044 48.8897 69.1723 47.9189 69.5659C46.948 69.9595 45.846 69.4714 45.4576 68.4743C45.064 67.4772 45.5311 66.349 46.502 65.9554C47.4728 65.5618 48.5749 66.0498 48.9632 67.0469Z" fill="#FEFFFE"/>
                            <path d="M75.2602 64.276C71.8648 69.4609 72.3686 75.1548 75.4229 77.3065C80.0305 80.5496 90.8411 81.4313 94.8085 76.703C98.6499 72.1216 96.6557 63.809 90.2481 60.2562C84.6381 57.1442 78.5454 59.2591 75.2602 64.276Z" fill="#F9FEFF"/>
                            <path d="M95.3543 62.7437C90.2953 54.7512 78.4194 57.5169 75.15 64.4493C78.5454 59.2696 84.9163 57.669 90.1011 60.5396C93.3076 62.3186 95.6061 65.4988 96.2621 69.0412C96.7817 71.8698 96.1887 75.0447 94.5933 76.9497C96.4406 75.7059 99.075 68.6213 95.3595 62.7437H95.3543Z" fill="#772400" class="eyelash"/>
                            <path d="M89.2457 67.7398C90.6574 71.3293 88.9729 75.3964 85.483 76.8186C81.9932 78.2408 78.0206 76.4827 76.6089 72.8932C75.1972 69.3036 76.8818 65.2366 80.3716 63.8144C83.8614 62.3922 87.8341 64.1502 89.2457 67.7398Z" fill="#772400" class="eyes"/>
                            <path d="M88.6895 67.1152C89.0831 68.1123 88.616 69.2406 87.6451 69.6342C86.6743 70.0278 85.5722 69.5397 85.1839 68.5426C84.7903 67.5455 85.2574 66.4172 86.2282 66.0237C87.1991 65.6301 88.3011 66.1181 88.6895 67.1152Z" fill="#FEFFFE"/>` : `` }
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

                        ${
                            (d.clothing.thoubs) ? `<div class="tops" style="
                                position: absolute;
                                z-index: 1;
                                top: ${ d.clothing.thoubs.positioning.top ? d.clothing.thoubs.positioning.top+'px' : `170px`};
                                left: ${ d.clothing.thoubs.positioning.left ? d.clothing.thoubs.positioning.left+'px' : `170px`};
                            ">
                                <img src="${d.clothing.thoubs.image}" style="width: ${ d.clothing.thoubs.positioning.image_width ? d.clothing.thoubs.positioning.image_width+'px' : `120px`}" />
                            </div>` :  (d.clothing.tops && d.clothing.tops.image) ? `<div class="tops" style="
                                position: absolute;
                                z-index: 1;
                                top: ${ d.clothing.tops.positioning.top ? d.clothing.tops.positioning.top+'px' : `170px`};
                                left: ${ d.clothing.tops.positioning.left ? d.clothing.tops.positioning.left+'px' : `170px`};
                            ">
                                <img src="${d.clothing.tops.image}" style="width: ${ d.clothing.tops.positioning.image_width ? d.clothing.tops.positioning.image_width+'px' : `120px`}" />
                            </div>` : `<div class="tops" style="
                                position: absolute;
                                z-index: 1;
                                top: 175px;
                                left: 170px;
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="115" height="127" viewBox="0 0 210 236" fill="none">
                                <path d="M176.755 224.143L174.853 66.7522L175.603 105.5L209.5 81.5C209.5 81.5 205.591 24.022 182.57 12.168C159.548 0.314068 130.5 0 130.5 0C130.5 0 128.144 13.3012 106.843 12.7707C86.4535 12.2656 85.5 0 85.5 0C85.5 0 57.575 0.309823 34.5596 12.168C11.5442 24.0262 0 83 0 83L41.6647 101.5L42.5562 66.7522L37.5312 224.143C37.5312 224.143 54.5099 235.5 107.406 235.5C160.303 235.5 176.755 224.143 176.755 224.143Z" fill="#F3803F"/>
                                <path d="M133.481 0.263138C129.903 0.0212208 130.5 0 130.5 0C130.5 0 128.144 13.3012 106.843 12.7707C86.4535 12.2656 85.5 0 85.5 0C85.5 0 83.6591 0.0297092 79.6888 0.35651C80.6184 6.26014 85.2215 17.1549 106.568 17.1549C128.559 17.1549 132.753 5.84846 133.481 0.263138Z" fill="#8A81AF"/>
                                <path d="M0 83L41.5 101.5L42.5 95.5L1 78C0.5 81 0 83 0 83Z" fill="#8A81AF"/>
                                <path d="M209.5 81.5L175.5 105.5L175 99.5L209 76.5C209.5 79 209.5 81.5 209.5 81.5Z" fill="#8A81AF"/>
                                </svg>
                            </div>`
                        }

                        ${
                            (d.clothing.socks) ?
                            ` <div class="socks" style="
                                position: absolute;
                                z-index: 1;
                                top: ${ d.clothing.socks.positioning.top ? d.clothing.socks.positioning.top+'px' : `379px`};
                                left: ${ d.clothing.socks.positioning.left ? d.clothing.socks.positioning.left+'px' : `166px`};
                                z-index: 0;
                                ${(d.clothing.socks) ? ``:`<div class="socks empty-placeholder" style="position: absolute;z-index: 1;"><img src="" style="display:none"/></div>`}
                            ">
                                <img src="${d.clothing.socks.image}" style="width: ${ d.clothing.socks.positioning.image_width ? d.clothing.socks.positioning.image_width+'px' : `127px`}" />
                            
                            </div>` : `<div class="socks empty-placeholder" style="position: absolute;z-index: 1;"><img src="" style="display:none"/></div>`
                        }
                        
                        ${
                            (d.clothing.shoes) ? `<div class="shoes" style="
                                position: absolute;
                                z-index: 1;
                                top: ${ d.clothing.shoes.positioning.top ? d.clothing.shoes.positioning.top+'px' : `381px`};
                                left: ${ d.clothing.shoes.positioning.left ? d.clothing.shoes.positioning.left+'px' : `155px`};
                                z-index: 0;
                            ">
                                <img src="${d.clothing.shoes.image}" style="width: ${ d.clothing.shoes.positioning.image_width ? d.clothing.shoes.positioning.image_width+'px' : `150px`}" />
                            </div>` : `<div class="shoes empty-placeholder" style="position: absolute;z-index: 1;"><img src="" style="display:none"/></div>`
                        }

                        ${
                            (d.clothing.socks || d.clothing.shoes) ? `<style>
                                @media (max-width: 768px) {
                                    .socks {
                                        top: ${d.clothing?.socks?.positioning?.top - 8}px !important;
                                    }
    
                                    .shoes {
                                        top: ${d.clothing?.shoes?.positioning?.top - 8}px !important;
                                    }
                                }
                            </style>` : ``
                        }

                        ${
                            (d.clothing.trousers) ? `<div class="trousers" style="
                                position: absolute;
                                z-index: 1;
                                top: ${ d.clothing.trousers.positioning.top ? d.clothing.trousers.positioning.top+'px' : `260px`};
                                left: ${ d.clothing.trousers.positioning.left ? d.clothing.trousers.positioning.left+'px' : `189px`};
                                z-index: 0;
                            ">
                                <img src="${d.clothing.trousers.image}" style="width: ${ d.clothing.trousers.positioning.image_width ? d.clothing.trousers.positioning.image_width+'px' : `84px`}" />
                            </div>` : `<div class="trousers" style="
                                position: absolute;
                                z-index: 1;
                                top: 261px;
                                left: 186px;
                                z-index: 0;
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="90" height="120" viewBox="0 0 159 221" fill="none">
                                <path d="M70.4065 5.0324C57.1932 5.21528 12.9178 0.776854 12.9178 0.776854C8.99024 90.0854 6.03768 175.784 4.42963 215.233L50.1856 216.967C53.2865 189.439 60.3627 108.271 68.3721 60.1509C69.992 53.554 83.1586 53.3422 84.4295 59.5552C94.7928 110.082 103.552 183.57 107.09 219.848L152.569 217.136C149.335 167.926 142.187 79.3365 138.083 2.01367C138.083 2.01367 97.7388 4.63214 70.4068 5.01352L70.4065 5.0324Z" fill="#5A76A8"/>
                                <path d="M12.1843 17.0413C36.3509 23.2079 95.6843 31.8413 139.684 17.0413" stroke="#7FA1D3" stroke-width="2" stroke-linecap="round"/>
                                <path d="M16.1843 18.0413C14.0176 74.5413 9.88428 189.441 10.6843 197.041" stroke="#7FA1D3" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 4"/>
                                <path d="M134.184 19.0413C139.018 75.7079 146.984 191.241 146.184 200.041" stroke="#7FA1D3" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 4"/>
                                <path d="M31.126 6.04126C30.7927 8.70793 30.326 14.7413 31.126 17.5413" stroke="#7FA1D3" stroke-width="7" stroke-linecap="round"/>
                                <path d="M58.126 10.0413C57.7927 12.7079 57.326 18.7413 58.126 21.5413" stroke="#7FA1D3" stroke-width="7" stroke-linecap="round"/>
                                <path d="M120.684 7.04126C121.018 9.70793 121.484 15.7413 120.684 18.5413" stroke="#7FA1D3" stroke-width="7" stroke-linecap="round"/>
                                <path d="M93.6843 10.0413C94.0176 12.7079 94.4843 18.7413 93.6843 21.5413" stroke="#7FA1D3" stroke-width="7" stroke-linecap="round"/>
                                <path d="M82.582 15.6491C82.5607 16.5398 82.2754 17.4042 81.7623 18.1326C81.2492 18.8611 80.5313 19.4208 79.6998 19.7409C78.8682 20.0609 77.9603 20.127 77.0912 19.9306C76.2221 19.7342 75.4308 19.2842 74.8177 18.6377C74.2046 17.9911 73.7972 17.1771 73.6472 16.2989C73.4972 15.4206 73.6113 14.5175 73.975 13.7041C74.3388 12.8908 74.9359 12.2037 75.6905 11.73C76.4452 11.2562 77.3235 11.0172 78.2142 11.0432C79.4016 11.0824 80.5258 11.5877 81.3434 12.4498C82.1609 13.3119 82.6059 14.4613 82.582 15.6491Z" fill="#E2C676"/>
                                <path d="M102.561 219.371L155.886 218.2L155.487 200.073L102.163 201.245L102.561 219.371Z" fill="#7FA1D3"/>
                                <path d="M54.2288 199.143L0.957764 196.48L0.0525954 214.589L53.3236 217.251L54.2288 199.143Z" fill="#7FA1D3"/>
                                </svg>
                            </div>`
                        }
                        `

                if(d.gender != "male"){
                    hero = heroFemale;
                }else{
                    hero = heroMale;
                }
                
                
                $(".avatars-hero").html(hero);

                if(d.gender != "male"){
                    if(!safarObject.isUserStudent){
                        $(".hijabs-hair").remove();
                        $(".removed-on-hijabs").remove();
                    }
                }

                emptyGear = ``;
                if (!d.headwears) hat = emptyGear;
                else
                hat = `<img class="avatar-${d.achievement_name.avatars} hat-${d.achievement_name.headwears}" src="${d.headwears}" />`;

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


                // Trick, trigger click event if user has selected avatars
               
                if(d.avatars.length > 0 ){

                    AvatarStore.selectedAvatars.gender = d.gender;
                    AvatarStore.selectedAvatars.group = d.group;

                    let details_id = []
                    d.avatars.map( (avatar) => {
                        details_id.push(avatar.details.ID)
                        if(avatar.details.ID != null){
                            
                            switch(avatar.type){
                                case "skin_color":
                                    $("svg.head path.skin-color, svg.body path.skin-color").attr("fill",avatar.details.color_hex)
                                    $("svg.head path.skin-color-shaded, svg.body path.skin-color-shaded").attr("fill",avatar.details.secondary_color)

                                    AvatarStore.selectedAvatars.skinColor = avatar.details.ID
                                    
                                break;

                                case "hair_style":
                                    $(".hair")
                                        .attr("width",$(avatar.details.hair_style_front).attr("width"))
                                        .attr("height",$(avatar.details.hair_style_front).attr("height"))
                                        .attr("viewBox",$(avatar.details.hair_style_front).attr("viewBox"))
                                        .html(avatar.details.hair_style_front).attr("data-hairstyle", avatar.details.slug);
                
                                    $(".hair-back")
                                        .attr("width",$(avatar.details.hair_style_back).attr("width"))
                                        .attr("height",$(avatar.details.hair_style_back).attr("height"))
                                        .attr("viewBox",$(avatar.details.hair_style_back).attr("viewBox"))
                                        .html(avatar.details.hair_style_back).attr("data-hairstyle", avatar.details.slug);

                                    AvatarStore.selectedAvatars.hairStyle = avatar.details.ID
                                break;

                                case "hair_color":
                                    $("svg.hair path, svg.hair-back path, svg.head path.hair-color").attr("fill",avatar.details.color_hex)
                                    $("svg.head path.eyebrow").attr("fill", avatar.details.eyebrow_color)
                                    $("svg.head path.eyes").attr("fill", avatar.details.eye_color)
                                    $("svg.head path.eyelash").attr("fill", avatar.details.eyelash_color)

                                    AvatarStore.selectedAvatars.hairColor = avatar.details.ID
                                break;
                            }

                        }
                        // hair color
                    })

                    // if (avatar.details.ID == 229608) {
                    if (details_id.includes(229608)) {
                        $("svg.hair path").attr("fill", "#FFB800");
                    }
                }

                // lastly save avatar
                AvatarStore.saveAvatar();
            },
        });
    },
    unEquipGear: (e) => {
        id = $(e.currentTarget).attr("data-id");
        category = $(e.currentTarget).attr("data-category");
        btn = $(e.currentTarget);
        $.ajax({
        url: `${safarObject.apiBaseurl}/user/unequip_gear`,
        data: {
            id,
            category
        },
        type: "post",
        dataType: "json",
        headers: {
            "X-WP-Nonce": safarObject.wpnonce
        },
        beforeSend: (xhr) => {
            btn.fadeTo("fast", 0.3);
        },
        success: () => {
            btn.fadeTo("fast", 1);

            AvatarStore.getShopItems({ type: AvatarStore.typeSelected, taxonomy: AvatarStore.taxonomySelected });
            
            AvatarStore.getUserEquippedGears();
            Safar.getUserInfo(safarObject.user_id);
        },
        });
    },
    equipGear: (e) => {
        id = $(e.currentTarget).attr("data-id");
        category = $(e.currentTarget).attr("data-category");
        btn = $(e.currentTarget);
        $.ajax({
        url: `${safarObject.apiBaseurl}/user/equip_gear`,
        data: {
            id,
            category
        },
        type: "post",
        dataType: "json",
        headers: {
            "X-WP-Nonce": safarObject.wpnonce
        },
        beforeSend: (xhr) => {
            btn.fadeTo("fast", 0.3);
        },
        success: () => {
            btn.fadeTo("fast", 1);

            AvatarStore.getShopItems({ type: AvatarStore.typeSelected, taxonomy: AvatarStore.taxonomySelected });
            
            AvatarStore.getUserEquippedGears();
            Safar.getUserInfo(safarObject.user_id);

            // modalState();
            // $("#successModalEquip").css("display", "flex").hide().fadeIn();

            AvatarStore.avatarItems.map((e) => {
                if (e.ID == id) {
                    // $("#successModalEquip .modal-content").html(`
                    //         <span class="close">
                    //         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="#A7AFB9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    //         <h2>Success</h2>
                    //         <div class="gear-item ">
                    //         <img src="${e.image}" alt="">
                    //         </div>
                    //         <p>
                    //         Great! You have successfully<br/>
                    //         equipped ${e.title}.
                    //         </p>
                             
                    //         <button class="btn-goback" >Go Back</button>
                    //     `);
                }
            });
        },
        });
    },
    getShopItems: (e) => {
        type = e.type;
        $.ajax({
            url: `${safarObject.apiBaseurl}/gears`,
            data: {
                type,
                taxonomy: e.taxonomy
            },
            headers: {
                "X-WP-Nonce": safarObject.wpnonce
            },
            dataType: "json",
            beforeSend: (xhr) => {
                tpl = ``;
                for (i = 0; i < 4; i++) {
                tpl += `
                            <div class="avatars-categories__list-item " style="background:#efefef; width:100%;max-width:200px;height:120px">
                                <div class="avatars-categories__img">
                                        
                                </div>
                            </div>
                        `;
                }

                $(".gears-list").html(tpl);
                $(".gears-list").removeClass("avatar-active").removeClass("sub-category-skin-color").removeClass("sub-category-hair-color");

                $(".avatars-categories__list-sub-categories").hide()

                
            },
            success: (d) => {
                AvatarStore.avatarItems = d.items;
                AvatarStore.subCategories = d.sub_categories;
                tpl = ``;

                if( $(".avatars-categories__list-sub-categories").hasClass("slick-initialized")){
                    $(".avatars-categories__list-sub-categories").slick("unslick")
                }

                d.items.map((e) => {

                    equipped = "";
                    button = "";

                    if (e.owned) {
                        button = `
                                    <div class="required-coins"><span>Owned</span></div>
                                    <button class="btn-buy btn-equip-gear" data-id="${e.ID}" data-category="${e.terms[0] ? e.terms[0].slug : ''}">Equip</button>
                                `;
                    } else {
                        button = `
                                    <div class="required-coins">
                                        <img src="/wp-content/uploads/2022/10/avatar-coin.png"/>
                                        <span>${e.coins_required}</span>
                                    </div>
                                    <button class="btn-buy btn-confirm-buy" data-id="${e.ID}">Buy</button>
                                    <button type="button" class="btn-preview" data-id="${e.ID}">Preview</button>
                                `;
                    }

                    if (e.equipped) {
                        equipped = "equipped";
                        button = `
                                    <div class="required-coins current"><span>Current</span></div>
                                    <button class="btn-buy " ata-id="${e.ID}">Equipped</button>
                                `;
                        if ((e.terms[0] && e.terms[0].slug != 'tops' )&& e.terms[0] && (e.terms[0].slug != 'trousers')) {
                            if (e.type != "avatars") {
                            button = `
                                <div class="required-coins current"><span>Current</span></div>
                                <button class="btn-buy btn-unequip" data-id="${e.ID}" data-category="${e.terms[0].slug}">
                                    <span class="equip">Equipped</span>
                                    <span class="unequip">Unequip</span>
                                </button>
                            `;
                            }
                        }
                        
                    }

                    let show = false;
                    let subTypeText = "";
                    if(d.sub_categories.length <=0){
                        show = true;
                    }else{
                        if(AvatarStore.subTypeSelected == ""){
                            // get the first subcategory
                            AvatarStore.subTypeSelected = d.sub_categories[0].term_id;
                        }
                        
                        if(e.terms.length > 0){
                            e.terms.map( term => {
                                // check if the SubCategory selected matches with the item, IF yes show
                                if(term.term_id == AvatarStore.subTypeSelected  ){
                                    show = true;
                                    subTypeText = term.slug;
                                }
                            })
                        }
                    }

                    //console.log("show avatar", show, e.terms)

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

                if(d.sub_categories.length <=0){
                    subCategoriesTpl = "";
                    $(".avatars-categories__list-sub-categories").hide()
                }else{
                    let isActive = "";
                    d.sub_categories.map( subCat => {

                        if(subCat.term_id == AvatarStore.subTypeSelected){
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

                    

                    $(".avatars-categories__list-sub-categories").slick({
                        dots: false,
                        infinite: false,
                        speed: 300,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        mobileFirst: true,
                        prevArrow: `<a class="slick-prev"><svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.1 8.00001H1.90001M1.90001 8.00001L8.20001 1.70001M1.90001 8.00001L8.20001 14.3" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        
                                    </a>`,
                        nextArrow: `<a class="slick-next"><svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.89999 8.00001H18.1M18.1 8.00001L11.8 1.70001M18.1 8.00001L11.8 14.3" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>`,
                        variableWidth: true,
                        responsive: [
                            {
                               breakpoint: 767,
                               settings: "unslick"
                            }
                        ]
                       
                    });
                    
                }


                $(".avatars-categories .avatars-categories__item").each( function(){
                    var itemType = $(this).attr("data-type");
                    if(itemType == type){
                        $(".avatars-categories__list").addClass(`list-${itemType}`)
                    }else{
                        $(".avatars-categories__list").removeClass(`list-${itemType}`)
                    }
                });

            },
        });
    },

    confirmGearPurchase: (e) => {
        id = $(e.currentTarget).attr("data-id");
        tpl = "";
        AvatarStore.avatarItems.map((e) => {
        if (e.ID == id) {
            classActive = "";
            if (e.type == "avatars") classActive = "avatar-active";

            modalState();
            $("#confirmationModal").css("display", "flex").hide().fadeIn();
            $("#confirmationModal .modal-content").html(`
                        <span class="close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="#A7AFB9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <h2>Confirmation</h2>
                        <div class="gear-item ${classActive}">
                        <img src="${e.image}" alt="">
                        </div>
                        <p>
                            Are you sure you want to buy
                        </p>
                        <p class="coin-price">
                            this item for &nbsp; 
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect width="32" height="32" fill="url(#pattern0)"/>
                            <defs>
                            <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_2132_2523" transform="scale(0.00980392)"/>
                            </pattern>
                            <image id="image0_2132_2523" width="102" height="102" xlink:href="data:image/png;base64,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"/>
                            </defs>
                            </svg>
                            
                            <span class="coin-price__main">${e.coins_required}</span>
                        </p>

                        <div class="div-purchase-gear-message"></div>
                        <button class="btn-buynow btn-proceed-to-purchase" data-id="${id}">buy now</button>
                        <button class="btn-goback" >Go Back</button>
                    `);
        }
        });
    },

    proceedGearPurchase: (e) => {
        btn = $(e.currentTarget);
        AvatarStore.avatarItems.map((e) => {
        if (e.ID == id) {
            $.ajax({
            url: `${safarObject.apiBaseurl}/user/purchase/gear`,
            data: {
                id,
            },
            type: "post",
            dataType: "json",
            headers: {
                "X-WP-Nonce": safarObject.wpnonce
            },
            beforeSend: (xhr) => {
                btn.fadeTo("fast", 0.3);
                $(".div-purchase-gear-message").html("");
            },
            success: (d) => {
                btn.fadeTo("fast", 1);
                if (d.success) {
                classActive = "";
                if (e.type == "avatars") classActive = "avatar-active";

                    $("#confirmationModal").hide();
                    $("#successModal").css("display", "flex").hide().fadeIn();
                    $("#successModal .modal-content").html(`
                                        <span class="close"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="#A7AFB9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                        <h2>Success</h2>

                                        <div class="unlocked-item">
                                            <div class="gear-item ${classActive}">
                                                <img src="${e.image}" alt="">
                                            </div>
                                            <div class="gear-item-description">
                                                <div class="title">Awesome! You have successfully bought ${d.item_details.post_title}.</div>
                                                <div class="description">${d.item_details.post_excerpt}</div>
                                                <div class="success-btn">
                                                    <button class="btn-equipnow btn-equip-gear" data-id="${e.ID}" data-category="${ d?.categories[0]?.slug }">equip now</button>
                                                </div>
                                            </div>
                                        </div>
                                `);

                    Safar.getUserInfo(safarObject.user_id);
                    AvatarStore.getShopItems({"type": AvatarStore.typeSelected, "taxonomy": AvatarStore.taxonomySelected });
                    AvatarStore.getUserEquippedGears();

                    $(".current-user-points").html(d.coins_remaining);
                } else {
                    $(".div-purchase-gear-message").html(`
                                <div style="background:#ffffff; padding:20px; border:1px solid #b8132c; color:#b8132c; text-align:center; margin-top:20px">${d.message}</div>
                            `);
                }
            },
            });
        }
        });
    },

    saveAvatar: () => {
        AvatarStore.saveAvatarTimeout = setTimeout( () => { 
            $.ajax({
                url: `${safarObject.apiBaseurl}/user/avatar`,
                data: {
                    group : AvatarStore.selectedAvatars.group,
                    skin_color : AvatarStore.selectedAvatars.skinColor,
                    hair_style : AvatarStore.selectedAvatars.hairStyle,
                    hair_color : AvatarStore.selectedAvatars.hairColor,
                    avatar_html: $(".avatars-hero").html(),
                    gender: AvatarStore.selectedAvatars.gender
                },
                type: "post",
                headers: {
                    "X-WP-Nonce": safarObject.wpnonce
                },
                dataType: "json",
                beforeSend: (xhr) => {

                    console.log("saving avatar", AvatarStore.selectedAvatars)

                },
                success: (d) => {
                    $("#wp-admin-bar-my-account img.avatar, #wp-admin-bar-user-info img.avatar").attr({"src":d.avatar_url})
                }
            });
        }, 1000);

    },
    
    init : () => {
        // avatars-categories__item active
        AvatarStore.typeSelected = $(".avatars-categories__item.active").attr("data-type");
        AvatarStore.taxonomySelected = $(".avatars-categories__item.active").attr("data-taxonomy");

        AvatarStore.getShopItems({"type": AvatarStore.typeSelected, "taxonomy": AvatarStore.taxonomySelected });


        AvatarStore.getUserEquippedGears();
    }
}

AvatarStore.init();


// event listeners
$(document).on('click','.avatars-categories__item', function () {
    $('.avatars-categories__item').removeClass('active')
    $(this).addClass('active');

    AvatarStore.typeSelected = $(".avatars-categories__item.active").attr("data-type");
    AvatarStore.taxonomySelected = $(".avatars-categories__item.active").attr("data-taxonomy");
    AvatarStore.subTypeSelected = ""

    AvatarStore.getShopItems({"type": AvatarStore.typeSelected, "taxonomy": AvatarStore.taxonomySelected });

});


$(document).on("click",".avatars-categories__list-sub-categories button", e => {
    AvatarStore.subTypeSelected = $(e.currentTarget).attr("data-termid");
    AvatarStore.getShopItems({"type": AvatarStore.typeSelected, "taxonomy": AvatarStore.taxonomySelected });
});

$(document).on("click",".avatars-categories__list-item.item-avatar", e => {
    let colorHex = $(e.currentTarget).attr("data-colorhex");
    let subType = $(e.currentTarget).attr("data-subtype");
    let secondaryColor = $(e.currentTarget).attr("data-secondarycolor");
    let itemId = $(e.currentTarget).attr("data-id")
    let eyeColor = $(e.currentTarget).attr("data-eyecolor")
    let eyeBrowColor = $(e.currentTarget).attr("data-eyebrowcolor")
    let eyeLashColor = $(e.currentTarget).attr("data-eyelashcolor")

    $(e.currentTarget).parent().find(".item-avatar").removeClass("active")
    $(e.currentTarget).addClass("active");

    switch(subType){
        case "skin-color":
            $("svg.head path.skin-color, svg.body path.skin-color").attr("fill",colorHex)
            $("svg.head path.skin-color-shaded, svg.body path.skin-color-shaded").attr("fill",secondaryColor)
            AvatarStore.selectedAvatars.skinColor = itemId
        break;

        case "hijabs":
            if(!safarObject.isUserStudent){
                if(safarObject.user_gender == "female"){
                    AvatarStore.selectedAvatars.hijabs = itemId
                    //$(e.currentTarget).find(".btn-preview").trigger("click")
                    AvatarStore.avatarItems.map( item => {
                        if(item.ID == itemId){
                            let target = $(`.avatars-hero .hijabs`);
                            target.find("img").attr("src", item.image);
                            target.find("img").css({"width":`${item.position.image_width}px`,"display":"block"})
                            target.css({"top":`${item.position.top}px`,"left":`${item.position.left}px`});

                            $.ajax({
                                url: `${safarObject.apiBaseurl}/user/equip_gear`,
                                data: {
                                    id: itemId,
                                    category: subType
                                },
                                type: "post",
                                dataType: "json",
                                headers: {
                                    "X-WP-Nonce": safarObject.wpnonce
                                },
                                beforeSend: (xhr) => {
                                },
                                success: () => {

                                }
                            });
                        }
                    });
                }
            }
        break;

        case "hair-color":
            $("svg.hair path, svg.hair-back path, svg.head path.hair-color").attr("fill",colorHex)
            $("svg.head path.eyebrow").attr("fill", eyeBrowColor)
            $("svg.head path.eyes").attr("fill", eyeColor)
            $("svg.head path.eyelash").attr("fill", eyeLashColor)

            AvatarStore.selectedAvatars.hairColor = itemId;

            if(AvatarStore.selectedAvatars.hairColor != ""){
                AvatarStore.avatarItems.map( eachHaircolor => {
                    if(eachHaircolor.ID == AvatarStore.selectedAvatars.hairColor){
                        $("svg.hair path, svg.hair-back path, svg.head path.hair-color").attr("fill",eachHaircolor.color_hex)
                    }
                });
            }

        break;

        case "hairstyle":
            AvatarStore.avatarItems.map( e => {
                if(e.ID == itemId){
                    AvatarStore.selectedAvatars.hairStyle = itemId;

                    $(".hair")
                        .attr("width",$(e.hair_style_front).attr("width"))
                        .attr("height",$(e.hair_style_front).attr("height"))
                        .attr("viewBox",$(e.hair_style_front).attr("viewBox"))
                        .html(e.hair_style_front).attr("data-hairstyle", e.slug);

                    $(".hair-back")
                        .attr("width",$(e.hair_style_back).attr("width"))
                        .attr("height",$(e.hair_style_back).attr("height"))
                        .attr("viewBox",$(e.hair_style_back).attr("viewBox"))
                        .html(e.hair_style_back).attr("data-hairstyle", e.slug);

                    if(AvatarStore.selectedAvatars.hairColor != ""){
                        AvatarStore.avatarItems.map( eachHaircolor => {
                            if(eachHaircolor.ID == AvatarStore.selectedAvatars.hairColor){
                                $("svg.hair path, svg.hair-back path, svg.head path.hair-color").attr("fill",eachHaircolor.color_hex)
                            }
                        });
                    } else {
                        
                        $("svg.hair path, svg.hair-back path, svg.head path.hair-color").attr("fill",'#772400')
                    }

                    if (itemId == 229608) {
                        $("svg.hair path").attr("fill", "#FFB800");
                    }
                    
                }
            })
        break;
    }


    // save avatar here
    if(AvatarStore.saveAvatarTimeout != "") clearTimeout(AvatarStore.saveAvatarTimeout);
    AvatarStore.saveAvatar();
});

$(document).on('click', '.btn-confirm-buy', e => {
    AvatarStore.confirmGearPurchase(e);
});

$(document).on("click", ".btn-equip-gear", e => {
    AvatarStore.equipGear(e)
});

$(document).on("click", ".btn-unequip", e => {
    AvatarStore.unEquipGear(e)
});

$(document).on('click', '.btn-buynow', e => {
    AvatarStore.proceedGearPurchase(e)
});

$(document).on("click", ".close, .btn-close, .btn-equipnow, .btn-goback", () => {
    $('#confirmationModal').hide();
    $('#successModal').hide();
    $('#successModalEquip').hide();
    modalState();
})

$(document).on("click",".btn-preview", e => {
    let selectedId = $(e.currentTarget).attr("data-id")
    AvatarStore.avatarItems.map( item => {
        if(item.ID == selectedId){
            AvatarStore.subCategories.map( cat => {
                if( cat.term_id == AvatarStore.subTypeSelected ){
                    console.log(item, AvatarStore.subTypeSelected, cat)

                    if(cat.slug == "thoubs" || cat.slug == "jilbabs"){
                        cat.slug = "tops";
                    }
                    if(cat.slug == "hijabs"){
                        $(`<div class="hijabs hijabs-preview" style="position:absolute;z-index:2"><img src="" style="display:none"/></div>`).insertBefore(".hijabs-hair.first")
                        $(".hijabs-hair").remove();
                        $(".removed-on-hijabs").remove();
                    }
                    
                    let target = $(`.avatars-hero .${cat.slug}`);

                    if(cat.slug == "headwears"){
                        $("svg.hair path").hide();
                    }

                    if(cat.slug == "trousers"){
                        target.find("svg").remove();
                    }
                    
                    console.log(`target.find("img")`, target.find("img").length)
                    if( target.find("img").length <= 0 ){
                        target.html(`<img src="" style="display:none"/>`);
                        target = $(`.avatars-hero .${cat.slug}`);
                    }

                    target.find("img").attr("src", item.image);
                    target.find("img").css({"width":`${item.position.image_width}px`,"display":"block"})
                    target.css({"top":`${item.position.top}px`,"left":`${item.position.left}px`});
                   
                }
            })
            //$(`.avatars-hero .${}`)
        }
    });
})