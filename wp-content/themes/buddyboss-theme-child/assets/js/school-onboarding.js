let JourneySchoolOnboarding = {}
$ = jQuery;

Dropzone.autoDiscover = false;
JourneySchoolOnboarding = {
    slickSlider : "",
    logoDropzone : "",
    importTeacherDropzone: "",
    stepsProgress: {
        total: 0,
        completed: 0,
        left: 0
    },

    formData: {
        instituteName: "",
    },

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
        previewEmail: async ( data ) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/school/preview_email`,
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
                        reject(`error /preview_template ${d}`);
                    }
                });

            })
        }
    },
    renderDropzone: () => {
        if(JourneySchoolOnboarding.logoDropzone != "") JourneySchoolOnboarding.logoDropzone.destroy();
        JourneySchoolOnboarding.logoDropzone = new Dropzone("div#logo-upload", 
            { 
                url: `${safarObject.apiBaseurl}/school/upload_logo`,
                maxFilesize: 10,
                maxThumbnailFilesize: 10,
                acceptedFiles: ".png, .jpg, .jpeg"
            }
        );
        JourneySchoolOnboarding.logoDropzone.on("addedfile", file => {
            if(file.size >= 10485760){
                JourneySchoolOnboarding.logoDropzone.removeFile(file);
                return false;
            }else{
                return file;
            }
        })

        JourneySchoolOnboarding.logoDropzone.on("complete", file => {
            console.log("Logo complete", $.parseJSON(file.xhr.response) )
            let logoDetails = $.parseJSON(file.xhr.response);
            $(".logo-id-container input[type=text]").val(logoDetails.attachment_id);
        });


        
        if(JourneySchoolOnboarding.importTeacherDropzone != "") JourneySchoolOnboarding.importTeacherDropzone.destroy();
        JourneySchoolOnboarding.importTeacherDropzone = new Dropzone("div#import-teachers", 
            { 
                url: `${safarObject.apiBaseurl}/school/admin/teachers/import_csv`,
                maxFilesize: 10485760,
                acceptedFiles: ".csv",
                autoProcessQueue: false,
                headers: {
                    'X-WP-Nonce': safarObject.wpnonce,
                },
            }
        );
        $(".btn-import").fadeTo("fast",.3)
        JourneySchoolOnboarding.importTeacherDropzone.on("addedfile", file => {
            if(file.size >= 10485760){
                console.log("file is greater than 10mb", file.zize)
                JourneySchoolOnboarding.importTeacherDropzone.removeFile(file);
                return false;
            }else{
                $(".btn-import").fadeTo("fast",1)
                return file;
            }
        });
        


        JourneySchoolOnboarding.importTeacherDropzone.on("complete", file => {
            console.log("complete", $.parseJSON(file.xhr.response) )

            $(".import-teachers-container .upload-form").hide();
            $(".import-teachers-container .imported-teachers").show();

            let xhrResponse = $.parseJSON(file.xhr.response);

            let teacherItems = ``;
            xhrResponse.teachers.map( teacher => {
                teacherItems += `
                <div class="item">
                    <div class="teacher-name">${teacher.first_name+" "+teacher.last_name}</div>
                    <div class="email">${teacher.email}</div>
                </div>
                `
            });

            let importedTeachersTpl = `
            <div class="message">
                <div class="icon">
                    <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 10.1152V11.0352C20.9988 13.1916 20.3005 15.2898 19.0093 17.017C17.7182 18.7441 15.9033 20.0076 13.8354 20.6191C11.7674 21.2305 9.55726 21.1571 7.53447 20.4097C5.51168 19.6624 3.78465 18.2813 2.61096 16.4722C1.43727 14.6632 0.879791 12.5232 1.02168 10.3715C1.16356 8.21972 1.99721 6.17148 3.39828 4.53223C4.79935 2.89298 6.69279 1.75054 8.79619 1.2753C10.8996 0.800068 13.1003 1.01749 15.07 1.89516M21 3.03516L11 13.0452L8.00001 10.0452" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="text">
                    You have successfully imported ${xhrResponse.teachers.length} teachers
                </div>
            </div>
            <div class="list"> 
                <div class="heading">
                    <div class="teacher-name">Teacher Name</div>
                    <div class="email">Email</div>
                </div>
                <div class="items">
                    ${teacherItems}
                </div>
            </div>
            <button type="button" class="btn-submit">Continue</button>
            `;            


            $(".import-teachers-container .imported-teachers").html(importedTeachersTpl);
        });


        $(".dz-button").html(`
        <div class="custom-button-uploader">
            <div class="svg">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M27.5192 22.3955L20.0033 14.8795L12.4873 22.3955L14.3728 24.2811L18.6699 19.984V40H21.3366V19.984L25.6337 24.2811L27.5192 22.3955Z" fill="#E6E4F6"/>
                    <path d="M32 12.1211V12C32 5.38333 26.6167 0 20 0C13.3833 0 8 5.38333 8 12V12.2076C6.0251 12.5803 4.20449 13.5286 2.76717 14.9333C1.89248 15.7823 1.19691 16.798 0.721617 17.9205C0.246321 19.0429 0.00094433 20.2493 0 21.4683C0 24.0615 1.06567 26.5318 3.00083 28.424C4.89167 30.2729 7.44483 31.3333 10.0058 31.3333H15.3333V28.6667H10.0058C6.09617 28.6667 2.66667 25.3029 2.66667 21.4683C2.66667 17.9459 5.63667 14.9753 9.42808 14.7053L10.6667 14.6171V12C10.6667 9.52465 11.65 7.15068 13.4003 5.40034C15.1507 3.65 17.5246 2.66667 20 2.66667C22.4754 2.66667 24.8493 3.65 26.5997 5.40034C28.35 7.15068 29.3333 9.52465 29.3333 12V14.67L30.65 14.6867C34.5227 14.7357 37.3333 17.5877 37.3333 21.4683C37.3333 25.5047 34.3622 28.6667 30.5692 28.6667H24.6667V31.3333H30.5692C31.8396 31.3375 33.097 31.077 34.2612 30.5683C35.4254 30.0596 36.4708 29.3139 37.3308 28.3788C39.0521 26.5282 40 24.0741 40 21.4683C40 16.5985 36.643 12.7827 32 12.1211Z" fill="#E6E4F6"/>
                </svg>
            </div>
            <div class="text">
                <b>Choose file</b> or <b>drag here</b>
            </div>
            <div class="size-limit">Size limit: 10mb</div>
        </div>
        
        `)
        console.log("render dropzone")
    },
    calculateStepsProgress: () => {
        JourneySchoolOnboarding.stepsProgress.total = $(".gf_page_steps .gf_step:visible").length - 1;
        JourneySchoolOnboarding.stepsProgress.completed = $(".gf_page_steps .gf_step.gf_step_completed").length - 1;
        JourneySchoolOnboarding.stepsProgress.left = JourneySchoolOnboarding.stepsProgress.total - JourneySchoolOnboarding.stepsProgress.completed;

        console.log(JourneySchoolOnboarding.stepsProgress);

        if( $(".gform_page:visible").find(".top-progress-tracker").length <= 0 ){

            let progressPercent = ( JourneySchoolOnboarding.stepsProgress.completed / JourneySchoolOnboarding.stepsProgress.total ) * 100
            $(".gform_page:visible").prepend(`<div class="top-progress-tracker" 
            data-completed="${JourneySchoolOnboarding.stepsProgress.completed}"
            data-left="${JourneySchoolOnboarding.stepsProgress.left}"
            data-total="${JourneySchoolOnboarding.stepsProgress.total}">
                <div class="progress" style="width:${progressPercent}%"></div>
            </div>`)
        }

        if($(".gform_page#gform_page_1_10:visible")){
            setTimeout( () => {
                $(".gform_page#gform_page_1_10:visible #gform_submit_button_1").trigger("click")
            }, 4000);
        }
    },

    storeFormData: () => {
        // this function also serves element arrangement and replacing texts
        JourneySchoolOnboarding.formData.instituteName = $(".institute_name input[type=text]").val();

        //let h3 = $(".gform_page:visible").find("h3").text().replace("[Institute Name]", JourneySchoolOnboarding.formData.instituteName);
        //$(".gform_page:visible").find("h3").html(h3);
        $(".gform_page:visible").find("h3").each( function(){
            let h3 = this;
            $(h3).html( $(h3).text().replace("[Institute Name]", JourneySchoolOnboarding.formData.instituteName) );

        })

        if($("#gform_page_1_5:visible")){
            $(".ginput_address_zip").detach().insertAfter(".ginput_address_country");
        }

        if($("#gform_page_1_6:visible")){
            
            if( $("#gform_page_1_6 .phonenumber").find(".flag-dp").length <=0 ){
                $("#gform_page_1_6 .phonenumber .ginput_container").prepend(`<div class="phone-flag">
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/phone-flag.png"/>
                    <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L7 7L13 1" stroke="#BFBFBF" stroke-width="2" stroke-linejoin="round"/>
                    </svg>

                </div>`);
            }
        }

        
        if($("#gform_page_1_7:visible")){
            $("#gform_page_1_7 label").removeClass("active")
            let facialRadio = $("#gform_page_1_7").find("input[type=radio]:checked");
            facialRadio.parent().addClass("active");

            let selectedFacial = facialRadio.val();
        
            if( typeof selectedFacial != "undefined" ){
                $(".facial-features-container .ginput_container_text input").val(selectedFacial);
            }

            let gfHiddenFieldValue = $(".facial-features-container .ginput_container_text input").val();
            if(gfHiddenFieldValue != ""){
                facialRadio = $("#gform_page_1_7").find(`input[value="${gfHiddenFieldValue}"]`);
                facialRadio.attr("checked","checked");
                facialRadio.parent().addClass("active")
            }
        }
        
    },

    stepsSlickSlider : () => {
        if(JourneySchoolOnboarding.slickSlider != "") JourneySchoolOnboarding.slickSlider.slick("destroy")
        JourneySchoolOnboarding.slickSlider = $(".gf_page_steps").slick({
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
                }
            ]
        });
    },  

    init : () => {
        JourneySchoolOnboarding.renderDropzone();
        JourneySchoolOnboarding.calculateStepsProgress();
        JourneySchoolOnboarding.storeFormData();
        JourneySchoolOnboarding.stepsSlickSlider();
    }
}


$(document).ready( () => {
    //initialize
    JourneySchoolOnboarding.init();


    // event listeners

    window.addEventListener("resize", () => {
        JourneySchoolOnboarding.stepsSlickSlider();
    });

    
    $(document).on("click",".btn-submit, .btn-skip", e => {
        var nextButton = $(".gform_page:visible").find(".gform_next_button");
        $(nextButton).trigger("click");
    })

    $(document).on("click",".btn-next", e => {
        var nextButton = $(".gform_page:visible").find(".gform_next_button");
        $(nextButton).trigger("click");
    })

    $(document).on("click",".btn-prev", e => {
        var prevButton = $(".gform_page:visible").find(".gform_previous_button");
        $(prevButton).trigger("click");
    })

    $(document).on('gform_page_loaded', function(event, form_id, current_page){
        console.log("loaded",event, form_id, current_page)
        switch(parseInt(current_page)){
            case 4: case 9:
                // logo upload form, initialize dropzone
                JourneySchoolOnboarding.renderDropzone();

                if(current_page==9){
                    // copy the email and body fields to hidden Subject and Body fields for teachers and students
                    let teacherEmailSubject = $(".teacher_welcome_email_subject").val();
                    let teacherEmailBody = tinymce.get("teacher_welcome_email_body").getContent();

                    let studentEmailSubject = $(".student_welcome_email_subject").val();
                    let studentEmailBody = tinymce.get("student_welcome_email_body").getContent();
                    
                    console.log(teacherEmailSubject, teacherEmailBody)
                    $(".email-subject-teacher-container input[type=text]").val(teacherEmailSubject);
                    $(".email-body-teacher-container textarea").val(teacherEmailBody);

                    $(".email-subject-student-container input[type=text]").val(studentEmailSubject);
                    $(".email-body-student-container textarea").val(studentEmailBody);
                }

                if(current_page==4){
                    // save parent password 
                    institutepassword = localStorage.getItem('institutepassword');
                    //console.log("saving family password")
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/groups/institute/update_admin_password`,
                        data: {
                            password: institutepassword
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
                }

                break;
            
            case 8:
                // need to reload the page when it comes to the welcome email
                // theres an issue with WP not loading the TinyMCE editor when Gravity Form loads the page via ajax
                window.location.href = window.location.href; 
            break;

            case 3:
                if($("#input_1_67").val().length <= 0 ){
                    $("#input_1_67").val(j2jSchoolOnboarding.order_meta.email).attr("readonly","readonly");;
                }
            break;
        }
        JourneySchoolOnboarding.calculateStepsProgress();
        JourneySchoolOnboarding.storeFormData();

    });

    $(document).on("change", "#gform_page_1_7 label input[type=radio]",e => {
        JourneySchoolOnboarding.storeFormData();
    });

    $(document).on("click", ".welcome-email-container .tab-menu a",e => {
        e.preventDefault()
        $(".welcome-email-container .tab-menu a, .welcome-email-container .tabs .tab-content").removeClass("active");
        $(e.currentTarget).addClass("active");

        let targetTab = $(e.currentTarget).attr("data-target");

        $(targetTab).addClass("active")
    
    });

    $(document).on("click",".btn-test-email", e => {
        $("#send-test-email-modal").show().css("display","flex");
        modalState();
    });

    $(document).on("click",".btn-continue", e => {
        $("#gform_page_1_9 .gform_page_footer #gform_submit_button_1").trigger("click");
    })

    $(document).on("click",".btn-import", e => {
        JourneySchoolOnboarding.importTeacherDropzone.processQueue()
    })


    $(document).on("click", ".btn-send-email", e => {
        let emailSubject = "";
        let emailBody1 = "";
        let emailGreetings = "";
        let emailBody2 = "";
        let emailBody = "";

        let targetEmail = $(".input-test-email").val();

        if( $("#gform_page_1_8 .tab-menu a.active").attr("data-target") == "#teacher-welcome-email"){
            emailSubject = $(".teacher_welcome_email_subject").val();
            emailGreetings = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("teacher_body_greetings").getContent()+`</h2>`;
            emailBody1 = tinymce.get("teacher_body_copy_1").getContent();
            emailBody2 = $(".teacher_body_copy_2").html();
            emailBody3 = tinymce.get("teacher_body_copy_3").getContent();
        }else{
            emailSubject = $(".student_welcome_email_subject").val();
            emailGreetings = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("student_body_greetings").getContent()+`</h2>`;
            emailBody1 = tinymce.get("student_body_copy_1").getContent();
            emailBody2 = $(".student_body_copy_2").html();
            emailBody3 = tinymce.get("student_body_copy_3").getContent();
        }

        if( $(".frm-send-test-email").valid() ){

            emailBody = emailGreetings+emailBody1+emailBody2+emailBody3;
        
            $(".btn-send-email").fadeTo("fast",.3);
            JourneySchoolOnboarding.api.sendTestEmail({email: targetEmail, subject: emailSubject, body: emailBody})
                .then( e => {
                    $(".btn-send-email").fadeTo("fast",1);
                    $(".input-test-email").val("")
                    $(".btn-send-email").html("Email Sent");

                    setTimeout( () => {
                        $(".btn-send-email").html("Send Now")
                    },  5000)
                })
                .catch( e => {
                    //sendTestEmail
                    console.log("error", e )
                    $(".btn-send-email").fadeTo("fast",1);
                });
        }
    })


    $(document).on("click",".btn-preview-email", e => {
        let emailSubject = "";
        let emailBody1 = "";
        let emailGreetings = "";
        let emailBody2 = "";
        let emailBody = "";

        if( $("#gform_page_1_8 .tab-menu a.active").attr("data-target") == "#teacher-welcome-email"){
            emailSubject = $(".teacher_welcome_email_subject").val();
            emailGreetings = tinymce.get("teacher_body_greetings").getContent();
            emailBody1 = tinymce.get("teacher_body_copy_1").getContent();
            emailBody2 = tinymce.get("teacher_body_copy_2").getContent();
        }else{
            emailSubject = $(".student_welcome_email_subject").val();
            emailGreetings = tinymce.get("student_body_greetings").getContent();
            emailBody1 = tinymce.get("student_body_copy_1").getContent();
            emailBody2 = tinymce.get("student_body_copy_2").getContent();
        }

        $(".btn-preview-email").fadeTo("fast",.3)
        
        //$("#preview-email-modal .email-template").html()
        JourneySchoolOnboarding.api.previewEmail({
            "subject":emailSubject,
            "greetings":emailGreetings,
            "body1":emailBody1,
            "body2":emailBody2,
        })
            .then( e => {
                $(".btn-preview-email").fadeTo("fast",1)
                $("#preview-email-modal").show().css("display","flex");
                $("#preview-email-modal .email-template").html(e.template)
            })
            .catch( e => {

            })
        modalState();
    });

    $(document).on("click",".modal", e => {
        if( $(e.target).hasClass("modal") ){
            $(".modal").fadeOut();
            modalState();
        }
    });

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

    // check browser local storage for the password
    var institutepassword = localStorage.getItem('institutepassword');
    if (institutepassword) {
        $("#gform_1 .hidden-password").remove();
        $("#gform_1 #field_2_93").append(`<input type="hidden" class="hidden-password" name="hidden_password" value="${institutepassword}"/>`);
    }

});


$(document).on("keyup",".input-password", e => {
    let pwd1 = $(".input-password[name=password]").val();
    let pwd2 = $(".input-password[name=confirm_password]").val();

    if( (pwd1 == pwd2) && pwd1.length > 0 && pwd2.length > 0 ){
        $("#gform_page_1_3").find(".btn-submit, .btn-next").removeAttr("disabled")
        $("#gform_1 .hidden-password").remove();
        $("#gform_1 .gf_page_steps").append(`<input type="hidden" class="hidden-password" name="hidden_password" value="${pwd1}"/>`);
        localStorage.setItem('institutepassword', pwd1);

        checkPasswordStrength(pwd1, document.getElementById("pwd-indicator"));

    }else{
        
        if(pwd1.length > 0 || pwd2.length > 0 &&  (pwd1 != pwd2)){
            document.getElementById("pwd-indicator").innerHTML = 'Password mismatch';
            document.getElementById("pwd-indicator").className = 'mismatch';
        }

        $("#gform_page_1_3").find(".btn-submit, .btn-next").attr("disabled","disabled")
    }

   
})