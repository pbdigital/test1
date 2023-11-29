let JourneyFamilyOnboarding = {}
$ = jQuery;

JourneyFamilyOnboarding = {
    slickSlider : "",
    stepsProgress: {
        total: 0,
        completed: 0,
        left: 0
    },

    formData: {
        instituteName: "",
    },

    familyDetails: "",

    api : {
        sendTestEmail : async (data) => {
            return new Promise((resolve, reject) => {
                
                $.ajax({
                    url: `${safarObject.apiBaseurl}/school/send_test_email`,
                    data,
                    type: "post",
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {

                    },
                    success: d => {
                        
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /sendTestEmail ${d}`);
                    }
                });

            })
        },
        
        createChildAccount: async (data) => {
            return new Promise((resolve, reject) => {
                
                $.ajax({
                    url: `${safarObject.apiBaseurl}/groups/family/child`,
                    data,
                    type: "post",
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {

                    },
                    success: d => {
                        
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /createChildAccount ${d}`);
                    }
                });

            })
        },
        getFamily: async (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/groups/family/${data.gid}`,
                    data,
                    type: "get",
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {

                    },
                    success: d => {
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /getFamily ${d}`);
                    }
                });

            })
        },
        deleteChildAccount: async ( data ) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/groups/family/${data.gid}/child/${data.child_id}`,
                    data,
                    type: "delete",
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {

                    },
                    success: d => {
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /deleteChildAccount ${d}`);
                    }
                });

            })
        }
    },
    
    calculateStepsProgress: () => {
        JourneyFamilyOnboarding.stepsProgress.total = $(".gf_page_steps .gf_step:visible").length - 1 ;
        JourneyFamilyOnboarding.stepsProgress.completed = $(".gf_page_steps .gf_step.gf_step_completed").length ;
        JourneyFamilyOnboarding.stepsProgress.left = JourneyFamilyOnboarding.stepsProgress.total - JourneyFamilyOnboarding.stepsProgress.completed;

        console.log(JourneyFamilyOnboarding.stepsProgress);
        let progressPercent = ( JourneyFamilyOnboarding.stepsProgress.completed / JourneyFamilyOnboarding.stepsProgress.total ) * 100
        if( $(".gform_page:visible").find(".top-progress-tracker").length <= 0 ){

            
            $(".gform_page:visible").prepend(`<div class="top-progress-tracker" 
            data-completed="${JourneyFamilyOnboarding.stepsProgress.completed}"
            data-left="${JourneyFamilyOnboarding.stepsProgress.left}"
            data-total="${JourneyFamilyOnboarding.stepsProgress.total}">
                <div class="progress" style="width:${progressPercent}%"></div>
            </div>`)
        }else{
            $(".top-progress-tracker .progress").css({"width":`${progressPercent}%`});
        }

        if($(".gform_page#gform_page_1_9:visible")){
            setTimeout( () => {
                $(".gform_page#gform_page_1_9:visible #gform_submit_button_1").trigger("click")
            }, 4000);
        }
    },

    storeFormData: () => {
        // this function also serves element arrangement and replacing texts
        JourneyFamilyOnboarding.formData.instituteName = $(".institute_name input[type=text]").val();

        //let h3 = $(".gform_page:visible").find("h3").text().replace("[Institute Name]", JourneyFamilyOnboarding.formData.instituteName);
        //$(".gform_page:visible").find("h3").html(h3);
        $(".gform_page:visible").find("h3").each( function(){
            let h3 = this;
            $(h3).html( $(h3).text().replace("[Institute Name]", JourneyFamilyOnboarding.formData.instituteName) );

        })

        if($("#gform_page_1_4:visible")){
            $(".ginput_address_zip").detach().insertAfter(".ginput_address_country");
        }

        if($("#gform_fields_2_2:visible")){
            
            if( $("#gform_fields_2_2 .phonenumber").find(".flag-dp").length <=0 ){
                $("#gform_fields_2_2 .phonenumber .ginput_container").prepend(`<div class="phone-flag">
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/phone-flag.png"/>
                    <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L7 7L13 1" stroke="#BFBFBF" stroke-width="2" stroke-linejoin="round"/>
                    </svg>

                </div>`);
            }
        }

        
        if($("#gform_page_2_5:visible")){
            $("#gform_page_2_5 label").removeClass("active")
            let facialRadio = $("#gform_page_2_5").find("input[type=radio]:checked");
            facialRadio.parent().addClass("active");

            let selectedFacial = facialRadio.val();
        
            if( typeof selectedFacial != "undefined" ){
                $(".facial-features-container .ginput_container_text input").val(selectedFacial);
            }

            let gfHiddenFieldValue = $(".facial-features-container .ginput_container_text input").val();
            if(gfHiddenFieldValue != ""){
                facialRadio = $("#gform_page_2_5").find(`input[value="${gfHiddenFieldValue}"]`);
                facialRadio.attr("checked","checked");
                facialRadio.parent().addClass("active")

                $("#gform_page_2_5 .btn-submit").removeAttr("disabled")
            }
        }

        $(".hidden-gid-container input[type=text]").val(j2jFamilyOnboarding.gid);
        

        new Pikaday({
            field: document.getElementById("child-add-birthday"),
            format: 'DD-MMM-YYYY',
            maxDate: new Date(), // set minimum date to today
            position: "bottom",
            yearRange: 20,
        });

        $("#input_2_90").val($("#input_2_72").val()).attr("readonly","readonly");

        
        // check browser local storage for the password
        var familypassword = localStorage.getItem('familypassword');
        if (familypassword) {
            $("#gform_2 .hidden-password").remove();
            $("#gform_2 #field_2_93").append(`<input type="hidden" class="hidden-password" name="hidden_password" value="${familypassword}"/>`);
        }
    },

    stepsSlickSlider : () => {
        if(JourneyFamilyOnboarding.slickSlider != "") JourneyFamilyOnboarding.slickSlider.slick("destroy")
        JourneyFamilyOnboarding.slickSlider = $(".gf_page_steps").slick({
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
                    breakpoint: 1200,
                    settings: "unslick"
                },
                {
                    breakpoint: 425,
                    settings: {
                      slidesToShow: 3
                    }
                }
            ]
        });
    },  

    child: {
        formData: {
            childname: "",
            gender: "",
            birthday: "",
            gid: j2jFamilyOnboarding.gid,
            userid: 0,
        },
        templates: {
            
        },
        moveStep: (isNext) => {
            var currentActiveStep = $(".child-add-container .steps .step.active");
            var nextStep = $(".child-add-container .steps .step.active").next();
            var prevStep = $(".child-add-container .steps .step.active").prev();
            

            if(isNext){

                currentActiveStep.removeClass('active').addClass("completed")
                nextStep.addClass('active');

                let activeStep = $(".child-add-container .steps .step.active");
                
                if( activeStep.hasClass("gender") ){
                    $(activeStep).find("h3").html(`Choose ${JourneyFamilyOnboarding.child.formData.childname}’s gender`)
                }

                if( activeStep.hasClass("birthday") ){
                    $(activeStep).find(".birthday-label-container").html(`When is ${JourneyFamilyOnboarding.child.formData.childname}'s Birthday?`)
                }

                if( activeStep.hasClass("account") ){
                    $(activeStep).find(".account-label-container").html(`${JourneyFamilyOnboarding.child.formData.childname}’s Account`)
                }

               
            }else{
                currentActiveStep.removeClass('active');
                prevStep.addClass('active');
            }

            // calculate percentage
            let totalChildAddSteps = $(".child-add-container .steps .step.count-progress").length;
            let totalChildAddCompleted = $(".child-add-container .steps .step.count-progress.completed").length;
            
            let perc = (totalChildAddCompleted / totalChildAddSteps) * 100;
            $(".top-progress-tracker.child-information .progress").css({"width":`${perc}%`});

            if(!isNext){
                if(prevStep.length <= 0 ){
                    JourneyFamilyOnboarding.calculateStepsProgress();
                    $(".child-add-container .steps .step, .child-add-container").removeClass("active");
                    $(".child-add-container .steps .step.child-name").addClass("active");
                    $(".child-information-container").show();
                    $(".top-progress-tracker").removeClass("child-information");
                    JourneyFamilyOnboarding.loadChildInformation();
                }
            }
        },
        newSubmit: () => {
            var currentActiveStep = $(".child-add-container .steps .step.active");

            JourneyFamilyOnboarding.child.formData.childname = $(".child-add-container .steps .step.child-name input[name=child_name]").val();
            JourneyFamilyOnboarding.child.formData.gender = $(".child-add-container .steps .step input[name=gender]:checked").val();
            JourneyFamilyOnboarding.child.formData.birthday = $(".child-add-container .steps .step input[name=birthday]").val();
            JourneyFamilyOnboarding.child.formData.email = $(".child-add-container .steps .step input[name=email]").val();
            JourneyFamilyOnboarding.child.formData.username = $(".child-add-container .steps .step input[name=username]").val();
            JourneyFamilyOnboarding.child.formData.password = $(".child-add-container .steps .step input[name=password]").val();
            // if current active step is account
            // send child data to backend in order to create account
            if( currentActiveStep.hasClass("account") ){

                $(".btn-submit-new-child").fadeTo("fast",.1);
                $(".create-account-message").html("");

                console.log("JourneyFamilyOnboarding.child.formData", JourneyFamilyOnboarding.child.formData)
                
                //moveToNextStep = false;
                JourneyFamilyOnboarding.api.createChildAccount( JourneyFamilyOnboarding.child.formData )
                    .then( d => {
                        if(d.success){
                            JourneyFamilyOnboarding.child.moveStep(true);
                            setTimeout( () => {
                                $(".child-add-container .steps .step, .child-add-container").removeClass("active");
                                $(".child-add-container .steps .step.child-name").addClass("active");
                                $(".child-information-container").show();
                
                                $(".top-progress-tracker").removeClass("child-information");

                                $(".child-add-container .steps .step.child-name input[name=child_name]").val("");
                                $(".child-add-container .steps .step input[name=gender]:checked").val("");
                                $(".child-add-container .steps .step input[name=birthday]").val("");
                                $(".child-add-container .steps .step input[name=email]").val("");
                                $(".child-add-container .steps .step input[name=username]").val("").removeAttr("readonly");
                                $(".child-add-container .steps .step input[name=password]").val("");
                                $(".child-add-container .steps .step.account .readonly").html("");

                                $(".step.birthday .btn-submit-new-child, .step.child-name .btn-submit-new-child, .step.account .btn-submit-new-child").attr("disabled","disabled")

                                JourneyFamilyOnboarding.calculateStepsProgress();
                                JourneyFamilyOnboarding.loadChildInformation();
                            }, 3000)
                        }else{
                            $(".create-account-message").html(`<div class="error">${d.error_message}</div>`);
                            $(".child-add-container .steps .step.active .btn-submit-new-child").attr("disabled","disabled");
                        }

                        $(".btn-submit-new-child").fadeTo("fast",1);
                    })
                    .catch( e => {
                        console.log("error create child account", e)
                    })

            }else{
                JourneyFamilyOnboarding.child.moveStep(true);
            }

            
        }
    },

    getChildById: ( childid ) => {
        let selectedChild = "";
        JourneyFamilyOnboarding.familyDetails.children.map( child => {
            if(child.id == childid){
                selectedChild = child;
            }
        })
        return selectedChild;
    },

    loadChildInformation: () => {
        // 1. Get family members from api and store to js 
        // 2. wait 3 seconds to go back to add child step
        // 3. clear all form fields and set the steps to step 1
        $(".child-add-cards").fadeTo("fast",.3)
        JourneyFamilyOnboarding.api.getFamily( {gid:j2jFamilyOnboarding.gid} )
            .then( d => {
                console.log("family details", d)

                JourneyFamilyOnboarding.familyDetails = d;

                $(".child-information-container .remaining-seats").html(`${d.seats_taken}/${d.total_seats}`);
                $(".child-information-container").attr("data-seatstaken",d.seats_taken);

               
                setTimeout( () => {
                    var tpl = ``
                    // display children
                    if(d.seats_taken > 0 && d.remaining_seats > 0 ){
                        tpl += `
                            <div class="remaining-seats-message">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 13H11V7H13M13 17H11V15H13M12 2C10.6868 2 9.38642 2.25866 8.17317 2.7612C6.95991 3.26375 5.85752 4.00035 4.92893 4.92893C3.05357 6.8043 2 9.34784 2 12C2 14.6522 3.05357 17.1957 4.92893 19.0711C5.85752 19.9997 6.95991 20.7362 8.17317 21.2388C9.38642 21.7413 10.6868 22 12 22C14.6522 22 17.1957 20.9464 19.0711 19.0711C20.9464 17.1957 22 14.6522 22 12C22 10.6868 21.7413 9.38642 21.2388 8.17317C20.7362 6.95991 19.9997 5.85752 19.0711 4.92893C18.1425 4.00035 17.0401 3.26375 15.8268 2.7612C14.6136 2.25866 13.3132 2 12 2Z" fill="white"/>
                                </svg>
                                You still have ${d.remaining_seats} available slots to create another child account.
                            </div>
                        `
                    }

                    tpl += `<div class="child-add-cards">`;

                        if(d.seats_taken > 0){

                            d.children.map( child => {
                                
                                if(child.gender=="female"){
                                    avatarUrl = "/wp-content/themes/buddyboss-theme-child/assets/img/family-onboarding/female-avatar.png";
                                }else{
                                    avatarUrl = "/wp-content/themes/buddyboss-theme-child/assets/img/family-onboarding/male-avatar.png";
                                }

                                tpl += `
                                    <div class="child-add-card child-info">
                                        <div class="avatar"><img src="${avatarUrl}"/></div>
                                        <div class="details">
                                            <div class="name">${child.name}</div>
                                            <div class="username">Username: ${child.username}</div>
                                            <div class="gender-age">
                                                <div class="gender">Gender: ${child.gender}</div>
                                                <div class="age">Age: ${child.age}</div>
                                            </div>
                                        </div>
                                        <div class="actions">
                                            <button type="button" data-action="edit" class="child-actions" data-id="${child.id}">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 4.00001H4C3.46957 4.00001 2.96086 4.21073 2.58579 4.5858C2.21071 4.96087 2 5.46958 2 6.00001V20C2 20.5304 2.21071 21.0392 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0392 20 20.5304 20 20V13M18.5 2.50001C18.8978 2.10219 19.4374 1.87869 20 1.87869C20.5626 1.87869 21.1022 2.10219 21.5 2.50001C21.8978 2.89784 22.1213 3.4374 22.1213 4.00001C22.1213 4.56262 21.8978 5.10219 21.5 5.50001L12 15L8 16L9 12L18.5 2.50001Z" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                            <button type="button" data-action="delete" class="child-actions" data-id="${child.id}">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 6H5M5 6H21M5 6V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                `
                            })
                        }

                        if(d.remaining_seats > 0 ){
                            for(i = 0; i < d.remaining_seats; i++){
                                tpl += `
                                <div class="child-add-card">
                                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M24.9998 16.6667V33.3333M16.6665 25H33.3332M45.8332 25C45.8332 36.5059 36.5058 45.8333 24.9998 45.8333C13.4939 45.8333 4.1665 36.5059 4.1665 25C4.1665 13.4941 13.4939 4.16666 24.9998 4.16666C36.5058 4.16666 45.8332 13.4941 45.8332 25Z" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="text">ADD CHILD</div>
                                </div>
                                `
                            }
                        }

                    tpl += "</div>";

                    tpl += `<button type="button" class="btn-submit-children">Submit</buton>`

                    $(".child-information-container .main").html(tpl);
                    $(".child-add-cards").fadeTo("fast",1)
                    
                    // disabled submit buttons if no seats taken

                    console.log(d.seats_taken,)
                     
                    if(d.seats_taken <= 0){ // when parent added at least 1 child
                    //if(d.remaining_seats > 0 ){ // force user to add all children
                        $("#gform_page_2_5 .btn-submit-children, #gform_page_2_5 .gf-custom-navigation .btn-next").attr("disabled","disabled");
                    }else{
                        $("#gform_page_2_5 .btn-submit-children, #gform_page_2_5 .gf-custom-navigation .btn-next").removeAttr("disabled","disabled");
                    }

                    
                },0);
            })
            .catch( e => {

            })
    },

    init : () => {
        JourneyFamilyOnboarding.calculateStepsProgress();
        JourneyFamilyOnboarding.storeFormData();
        JourneyFamilyOnboarding.stepsSlickSlider();
        JourneyFamilyOnboarding.loadChildInformation();
    }
}


$(document).ready( () => {
    //initialize
    JourneyFamilyOnboarding.init();

    // if last page of the family onboarding is showing
    // force submit the form
    if( $("#gform_page_2_6.gform_page").is(":visible") ){        
        setTimeout( () => {
            $("#gform_submit_button_2").trigger("click");
        },300);
    }
    $("#gform_page_2_3").find(".btn-submit, .btn-next").attr("disabled","disabled")
    let currentURL = window.location.href;
    let containsDomain = currentURL.includes('staging.journey2jannah.com');
    if(containsDomain){
        $("#gform_page_2_3").find(".btn-submit, .btn-next").removeAttr("disabled")
    }


    // event listeners

    window.addEventListener("resize", () => {
        JourneyFamilyOnboarding.stepsSlickSlider();
    });

    
    $(document).on("click",".btn-submit, .btn-skip", e => {
        var nextButton = $(".gform_page:visible").find(".gform_next_button");
        $(nextButton).trigger("click");
    })

    $(document).on("click",".btn-next", e => {
        //gform_page_2_5
        var formPageId = $(".gform_page:visible").attr("id")

        switch(formPageId){
            case "gform_page_2_5":
                e.preventDefault();
                $(".btn-submit-children:visible").trigger("click");
                $(".btn-submit-new-child:visible").trigger("click");
            break;
            case "gform_page_2_5":
                e.preventDefault();
                $("#gform_page_2_5 .btn-submit").trigger("click")
            break;
            default:
                var nextButton = $(".gform_page:visible").find(".gform_next_button");
                $(nextButton).trigger("click");
            break;
            
        }
        
    })

    $(document).on("click",".btn-prev", e => {
        var formPageId = $(".gform_page:visible").attr("id")
        

        console.log("btn-prev", formPageId, !$(".child-information-container").is(":visible") )

        if(formPageId == "gform_page_2_5" && !$(".child-information-container").is(":visible") ){
            
            e.preventDefault();
            JourneyFamilyOnboarding.child.moveStep(false);
        }else{
            var prevButton = $(".gform_page:visible").find(".gform_previous_button");
            $(prevButton).trigger("click");
        }
    })

    $(document).on('gform_page_loaded', function(event, form_id, current_page){
        console.log("loaded",event, form_id, current_page)
        switch(parseInt(current_page)){
            case 2:
                if($("#input_2_71").val().length <= 0 ){
                    $("#input_2_71").val(j2jFamilyOnboarding.order_meta.first_name+` `+j2jFamilyOnboarding.order_meta.last_name);
                    $("#input_2_72").val(j2jFamilyOnboarding.order_meta.email);
                    $("#input_2_77").val(j2jFamilyOnboarding.order_meta.phone);
                }
            break;
            case 3:
                
                $("#input_2_90").val($("#input_2_72").val()).attr("readonly","readonly");
                $("#gform_page_2_3").find(".btn-submit, .btn-next").attr("disabled","disabled")
                let currentURL = window.location.href;
                let containsDomain = currentURL.includes('staging.journey2jannah.com');
                if(containsDomain){
                    $("#gform_page_2_3").find(".btn-submit, .btn-next").removeAttr("disabled")
                }

                break;
            case 4:
                
                // save parent password 
                familypassword = localStorage.getItem('familypassword');
                //console.log("saving family password")
                $.ajax({
                    url: `${safarObject.apiBaseurl}/groups/family/family_update_parent_password`,
                    data: {
                        email: $("#input_2_90").val(),
                        password: familypassword
                    },
                    type: "post",
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {

                    },
                    success: d => {
                        window.location.href = window.location.href;
                        console.log("pwd", d)
                    },
                    error: (d) => {
                        reject(`error pwd ${d}`);
                    }
                });

                JourneyFamilyOnboarding.loadChildInformation();

            break;
            case 6: // last step submit
                    $("#gform_submit_button_2").trigger("click");
                break;
        }
        JourneyFamilyOnboarding.calculateStepsProgress();
        JourneyFamilyOnboarding.storeFormData();
        JourneyFamilyOnboarding.stepsSlickSlider();
       
    });

    $(document).on("change", "#gform_page_2_5 label input[type=radio]",e => {
        JourneyFamilyOnboarding.storeFormData();
    });

 
    $(document).on("click",".btn-test-email", e => {
        $("#send-test-email-modal").show().css("display","flex");
        modalState();
    });

    $(document).on("click",".btn-continue", e => {
        $("#gform_page_1_8 .gform_page_footer #gform_submit_button_1").trigger("click");
    })

    $(document).on("click",".modal", e => {
        if( $(e.target).hasClass("modal") ){
            $(".modal").fadeOut();
            modalState();
        }
    });

    $(document).on("click",".child-add-card:not(.child-info)", e => {
        $(".child-information-container").hide();
        $(".top-progress-tracker").addClass("child-information");
        $(".child-add-container").addClass("active");

        $(".top-progress-tracker.child-information .progress").css({"width":"0%"});

        JourneyFamilyOnboarding.child.formData.childname = "";
        JourneyFamilyOnboarding.child.formData.gender =  "";
        JourneyFamilyOnboarding.child.formData.birthday =  "";
        JourneyFamilyOnboarding.child.formData.email =  "";
        JourneyFamilyOnboarding.child.formData.username =  "";
        JourneyFamilyOnboarding.child.formData.password =  "";
        JourneyFamilyOnboarding.child.formData.password =  0;

        $("#gform_page_2_5 .gf-custom-navigation .btn-next").removeAttr("disabled","disabled");
    });

    $(document).on("click",".btn-submit-new-child", e => {
        JourneyFamilyOnboarding.child.newSubmit();
    })

    $(document).on("keyup, change",".child-add-container .steps .step.active input[name=birthday]",e => {
        let childName = $(e.currentTarget).val();
        if(childName.length > 0 ){
            $(".child-add-container .steps .step.active .btn-submit-new-child").removeAttr("disabled")
        }else{
            $(".child-add-container .steps .step.active .btn-submit-new-child").attr("disabled","disabled")
        }
    });
    $(document).on("keyup",".child-add-container .steps .step.active input[name=child_name]",e => {
        let childName = $(e.currentTarget).val();
        if(childName.length > 0 ){
            $(".child-add-container .steps .step.active .btn-submit-new-child").removeAttr("disabled")
        }else{
            $(".child-add-container .steps .step.active .btn-submit-new-child").attr("disabled","disabled")
        }
    });

    $(document).on("keyup",".child-add-container .steps .step.account input[name=username], .child-add-container .steps .step.account input[name=password]",e => {
        let username = $(".child-add-container .steps .step.account input[name=username]").val();
        let password = $(".child-add-container .steps .step.account input[name=password]").val();


        if(username.length > 0 && password.length > 0 ){
            $(".child-add-container .steps .step.active .btn-submit-new-child").removeAttr("disabled")
        }else{
            $(".child-add-container .steps .step.active .btn-submit-new-child").attr("disabled","disabled")
        }

    });

    $(document).on("change",".child-add-container .steps .step.active .select-gender input[type=radio]",e => {
        $(".child-add-container .steps .step.active .btn-submit-new-child").removeAttr("disabled")
    });

    $(document).on("click",".child-actions", e => {
        let id = $(e.currentTarget).attr("data-id")
        let action = $(e.currentTarget).attr("data-action")

        switch(action){
            case "delete":

                $(e.currentTarget).fadeTo("fast",.3);

                JourneyFamilyOnboarding.api.deleteChildAccount({
                    child_id: id,
                    gid: j2jFamilyOnboarding.gid
                }).then( d => {
                    JourneyFamilyOnboarding.loadChildInformation();
                }).catch( e => {
                    console.log("error", e)
                })
                break;
            case "edit":
                let childInfo = JourneyFamilyOnboarding.getChildById(id);

                JourneyFamilyOnboarding.child.formData.childname = childInfo.name;
                JourneyFamilyOnboarding.child.formData.gender =  childInfo.gender;
                JourneyFamilyOnboarding.child.formData.birthday =  childInfo.date_of_birth;
                JourneyFamilyOnboarding.child.formData.email =  childInfo.user_email;
                JourneyFamilyOnboarding.child.formData.username =  childInfo.username;
                JourneyFamilyOnboarding.child.formData.password =  "";
                JourneyFamilyOnboarding.child.formData.userid =  childInfo.id;


                $(".child-add-container .steps .step.child-name input[name=child_name]").val(childInfo.name);
                $(`.child-add-container .steps .step input[name=gender][value=${childInfo.gender}]`).attr("checked","checked");
                $(".child-add-container .steps .step input[name=birthday]").val(childInfo.date_of_birth);
                $(".child-add-container .steps .step input[name=email]").val(childInfo.user_email);
                $(".child-add-container .steps .step input[name=username]").val(childInfo.username).attr("readonly","readonly");
                $(".child-add-container .steps .step input[name=password]").val("");
                $(".child-add-container .steps .step.account .readonly").html(`<div class="notice">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 13H11V7H13M13 17H11V15H13M12 2C10.6868 2 9.38642 2.25866 8.17317 2.7612C6.95991 3.26375 5.85752 4.00035 4.92893 4.92893C3.05357 6.8043 2 9.34784 2 12C2 14.6522 3.05357 17.1957 4.92893 19.0711C5.85752 19.9997 6.95991 20.7362 8.17317 21.2388C9.38642 21.7413 10.6868 22 12 22C14.6522 22 17.1957 20.9464 19.0711 19.0711C20.9464 17.1957 22 14.6522 22 12C22 10.6868 21.7413 9.38642 21.2388 8.17317C20.7362 6.95991 19.9997 5.85752 19.0711 4.92893C18.1425 4.00035 17.0401 3.26375 15.8268 2.7612C14.6136 2.25866 13.3132 2 12 2Z" fill="white"></path>
                    </svg> 
                    Usernames cannot be changed.
                </div>`);
                
                $(".child-information-container").hide();
                $(".top-progress-tracker").addClass("child-information");
                $(".child-add-container").addClass("active");
        
                $(".top-progress-tracker.child-information .progress").css({"width":"0%"});

                $(".step.birthday .btn-submit-new-child, .step.child-name .btn-submit-new-child").removeAttr("disabled")

                console.log(childInfo)
            break;
        }
    });

    $(document).on("click",".btn-submit-children", e => {

        if(JourneyFamilyOnboarding.familyDetails.remaining_seats > 0){
            $("#confirm-continue-setup").fadeIn().css("display","flex");
            modalState();

            let modalContent = `
            <h3>Continue Setup</h3>
            <div>You have already created ${JourneyFamilyOnboarding.familyDetails.seats_taken} child accounts.<br/>
            You still have ${JourneyFamilyOnboarding.familyDetails.remaining_seats} available slot to create another child account.</div>
            <br/>
            <div>Are you sure you would you like to continue?</div>

            <div class="buttons">
                <button type="button" class="btn-go-back">Go Back</button>
                <button type="button" class="btn-continue">Continue</div>
            </div>
            `
            $("#confirm-continue-setup .confirm-content").html(modalContent)
        }else{
            var nextButton = $(".gform_page:visible").find(".gform_next_button");
            $(nextButton).trigger("click");
        }
    })
    
    $(document).on("click","#confirm-continue-setup .btn-go-back", e => {
        $("#confirm-continue-setup").hide();
        modalState();
    })

    $(document).on("click","#confirm-continue-setup .btn-continue", e => {
        $("#confirm-continue-setup").hide();
        modalState();

        var nextButton = $(".gform_page:visible").find(".gform_next_button");
        $(nextButton).trigger("click");
    })   

    checkPasswordStrength = (password, indicatorElement) => {
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
        
    }
});


$(document).on("keyup",".input-password", e => {
    let pwd1 = $(".input-password[name=password]").val();
    let pwd2 = $(".input-password[name=confirm_password]").val();

    if( (pwd1 == pwd2) && pwd1.length > 0 && pwd2.length > 0 ){
        $("#gform_page_2_3").find(".btn-submit, .btn-next").removeAttr("disabled")
        $("#gform_2 .hidden-password").remove();
        $("#gform_2 .gf_page_steps").append(`<input type="hidden" class="hidden-password" name="hidden_password" value="${pwd1}"/>`);
        localStorage.setItem('familypassword', pwd1);

        checkPasswordStrength(pwd1, document.getElementById("pwd-indicator"));

    }else{
        
        if(pwd1.length > 0 || pwd2.length > 0 &&  (pwd1 != pwd2)){
            document.getElementById("pwd-indicator").innerHTML = 'Password mismatch';
            document.getElementById("pwd-indicator").className = 'mismatch';
        }

        $("#gform_page_2_3").find(".btn-submit, .btn-next").attr("disabled","disabled")
    }

    
   
})