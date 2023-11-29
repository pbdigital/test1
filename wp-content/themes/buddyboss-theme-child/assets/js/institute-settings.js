$ = jQuery;
let schoolbypasstransient = true;
let j2jInstituteSettings = {}
Dropzone.autoDiscover = false;

j2jInstituteSettings = {
    logoDropzone : "",
    logoId: 0,
    schoolDetails: "",
    activeEmailTab: "",
    api: {
        getuserSchoolDetails: async ( data ) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/manage-classrooms/school`,
                    type: "get",
                    data: {
                        bypasstransient : schoolbypasstransient
                    },
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: (xhr) => {
                        
                    },
                    success: (d) => {
                        j2jInstituteSettings.schoolDetails = d;
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /manage-classrooms/school}`);
                    }
                });
            })
        },
        saveInfo: (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/manage-classrooms/school/save_institute_information`,
                    type: "PUT",
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
                        reject(`error /manage-classrooms/school/save_institute_information}`);
                    }
                });
            })
        },
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
        saveWelcomeEmail : async (data) => {
            return new Promise((resolve, reject) => {
                
                $.ajax({
                    url: `${safarObject.apiBaseurl}/school/save_welcome_email`,
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
                        reject(`error /save_welcome_email ${d}`);
                    }
                });

            })
        },

        saveActivityFeed: (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/school/save_activity_feed`,
                    type: "PUT",
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
                        reject(`error /school/save_activity_feed}`);
                    }
                });
            })
        },
        
    },
    
    renderDropzone: () => {
        if(j2jInstituteSettings.logoDropzone != "") j2jInstituteSettings.logoDropzone.destroy();
        j2jInstituteSettings.logoDropzone = new Dropzone("div#logo-upload", 
            { 
                url: `${safarObject.apiBaseurl}/school/upload_logo`,
                maxFilesize: 10,
                maxThumbnailFilesize: 10,
                acceptedFiles: ".png, .jpg, .jpeg",
                autoProcessQueue: true
            }
        );
        j2jInstituteSettings.logoDropzone.on("addedfile", file => {
            console.log("Addedfile", file )
            if(file.size >= 10485760){
                $("#logo-upload .dz-default").css({"background-image":"none"});
                j2jInstituteSettings.logoDropzone.removeFile(file);
                return false;
            }else{
                return file;
            }
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
        
        j2jInstituteSettings.logoDropzone.on("complete", file => {
            console.log("Logo complete", $.parseJSON(file.xhr.response) )
            $("#logo-upload .dz-default").css({"background-image":"none"});
            let logoDetails = $.parseJSON(file.xhr.response);
            j2jInstituteSettings.logoId = logoDetails.attachment_id;
            
        });
    },

    updateLogo: () => {
        $("#upload-insitute-logo-modal .button-submit-logo").fadeTo("fast",.3)
        $.ajax({
            url: `${safarObject.apiBaseurl}/manage-classrooms/school/update_logo`,
            type: "PUT",
            data: {
                attachmentid: j2jInstituteSettings.logoId
            },
            headers: {
                "X-WP-Nonce": safarObject.wpnonce
            },
            dataType: "json",
            beforeSend: (xhr) => {
                
            },
            success: (d) => {
                $("#upload-insitute-logo-modal .button-submit-logo").fadeTo("fast",1)
                $("#upload-insitute-logo-modal").fadeOut();
                j2jInstituteSettings.loadSchoolDetails();
            }
        });
    },

    loadSchoolDetails: () => {
        $(".logo-update .logo .logo-image, .institute-information-container .logo-update .button-remove").fadeTo('fast',.3)
        j2jInstituteSettings.api.getuserSchoolDetails()
            .then( d => {
                //console.log("j2jInstituteSettings.schoolDetails", j2jInstituteSettings.schoolDetails)
                let avatar = j2jInstituteSettings.schoolDetails.parent_school.post.avatar;
                $(".logo-update .logo .logo-image").css({"background-image":`url(${avatar})`});
                $(".logo-update .logo .logo-image, .institute-information-container .logo-update .button-remove").fadeTo('fast',1)
                if(avatar == ""){
                    $(".institute-information-container .logo-update .button-remove").hide();
                }

                let instituteName = j2jInstituteSettings.schoolDetails.parent_school.post.post_title;
                $(".input-institute-name").val(instituteName);

                let facialFeature = j2jInstituteSettings.schoolDetails.parent_school.post.facial_feature;
                $("section.facial-features input[type=radio]").each( function() {
                    let radioVal = $(this).val();
                    if(radioVal.toLowerCase() == facialFeature.toLowerCase()){
                        $(this).parent().trigger("click");
                    }
                })

            })
            .catch( e => {
                console.log("error get user school details", e)
            })
        
    },
    saveInstituteInfo: () => {
        $(".institute-information-container .button-save").fadeTo("fast", .3);
        data = {
            institute_name: $(".input-institute-name").val(),
            facial_feature: $("section.facial-features input[type=radio]:checked").val()
        };
        j2jInstituteSettings.api.saveInfo(data)
            .then( e => {
                $(".institute-information-container .button-save").html("Updates Saved!")
                $(".institute-information-container .button-save").fadeTo("fast",1);
            })
            .catch( e => {

            })
    },
    saveActivityFeed: () => {
        $(".activity-feed-container .button-save").fadeTo("fast",.3);
        data = $("#frm-activity-feeds").serialize();
        j2jInstituteSettings.api.saveActivityFeed(data)
            .then( e => {
                $(".activity-feed-container .button-save").html("Updates Saved!")
                $(".activity-feed-container .button-save").fadeTo("fast",1);
            })
            .catch( e => {

            })
    },
    showEmailPreview: () => {
        // j2jInstituteSettings.activeEmailTab
        $("#preview-email-modal").show().css({"display":"flex"});
        
        let previewEmailTpl = ``;
        let avatar = j2jInstituteSettings.schoolDetails.parent_school.post.avatar;
        if(avatar != ""){
            avatar = `<img src="${avatar}" />`
        }

        console.log("j2jInstituteSettings.activeEmailTab", j2jInstituteSettings.activeEmailTab)

        switch(j2jInstituteSettings.activeEmailTab){
            case "Teacher Welcome Email":
                emailSubject = $(".teacher_welcome_email_subject").val();
                emailGreetings = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("teacher_body_greetings").getContent()+`</h2>`;
                emailBody1 = tinymce.get("teacher_body_copy_1").getContent();
                emailBody2 = $(".teacher_body_copy_2").html();
                emailBody3 = tinymce.get("teacher_body_copy_3").getContent();
                break;
            case "Family Welcome Email":
                emailSubject = $(".institute_family_subject").val();
                emailGreetings = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("institute_family_greetings").getContent()+`</h2>`;
                emailBody1 = tinymce.get("institute_family_welcome_email_body_1").getContent();
                emailBody2 = "";
                emailBody3 = tinymce.get("institute_family_welcome_email_body_2").getContent();
                break;
            default:
                emailSubject = $(".student_welcome_email_subject").val();
                emailGreetings = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("student_body_greetings").getContent()+`</h2>`;
                emailBody1 = tinymce.get("student_body_copy_1").getContent();
                emailBody2 = $(".student_body_copy_2").html();
                emailBody3 = tinymce.get("student_body_copy_3").getContent();
                break;
        }

        let emailCombinedText = emailGreetings + emailBody1 + emailBody2 + emailBody3;

        previewEmailTpl += `
            <div class="head">
                <div class="institute-name">${j2jInstituteSettings.schoolDetails.parent_school.post.post_title}</div>
                <div class="school-logo">${avatar}</div>
            </div>

            <div class="email-body">
                <div class="header-image">
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/email-preview-header.png"/>
                </div>
                <div class="text">
                    ${emailCombinedText}
                </div>
            </div>
        `

        $("#preview-email-modal .main-content").html(previewEmailTpl);
        modalState();
    },
    sendTestEmail: () => {
        
        let targetEmail = $(".emailtestinput").val();

        switch(j2jInstituteSettings.activeEmailTab){
            case "Teacher Welcome Email":
                emailSubject = $(".teacher_welcome_email_subject").val();
                emailGreetings = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("teacher_body_greetings").getContent()+`</h2>`;
                emailBody1 = tinymce.get("teacher_body_copy_1").getContent();
                emailBody2 = $(".teacher_body_copy_2").html();
                emailBody3 = tinymce.get("teacher_body_copy_3").getContent();
                break;
            case "Family Welcome Email":
                emailSubject = $(".institute_family_subject").val();
                emailGreetings = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("institute_family_greetings").getContent()+`</h2>`;
                emailBody1 = tinymce.get("institute_family_welcome_email_body_1").getContent();
                emailBody2 = "";
                emailBody3 = tinymce.get("institute_family_welcome_email_body_2").getContent();
                break;
            default:
                emailSubject = $(".student_welcome_email_subject").val();
                emailGreetings = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("student_body_greetings").getContent()+`</h2>`;
                emailBody1 = tinymce.get("student_body_copy_1").getContent();
                emailBody2 = $(".student_body_copy_2").html();
                emailBody3 = tinymce.get("student_body_copy_3").getContent();
                break;
        }


        emailBody = emailGreetings+emailBody1+emailBody2+emailBody3;
    
        $("#send-test-email-modal .button-send").fadeTo("fast",.3);
        
        j2jInstituteSettings.api.sendTestEmail({email: targetEmail, subject: emailSubject, body: emailBody})
            .then( e => {
                $("#send-test-email-modal .button-send").fadeTo("fast",1);
                $(".emailtestinput").val("")
                $("#send-test-email-modal .button-send").html("Email Sent");

                let successSendTpl = ``;
                successSendTpl += `
                    <div class="success-send-container">
                        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M62 29.2572V32.0172C61.9963 38.4864 59.9015 44.7812 56.028 49.9626C52.1545 55.1441 46.7098 58.9346 40.5061 60.7688C34.3023 62.6031 27.6718 62.3828 21.6034 60.1409C15.535 57.899 10.354 53.7555 6.83288 48.3284C3.3118 42.9013 1.63937 36.4813 2.06503 30.0261C2.49069 23.5709 4.99162 17.4261 9.19484 12.5084C13.3981 7.59063 19.0783 4.16332 25.3886 2.73761C31.6988 1.3119 38.3008 1.96418 44.21 4.59717M62 8.01717L32 38.0472L23 29.0472" stroke="#98C03D" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2>Success</h2>
                        <p>The test email has been successfully<br/>sent to the email address.</p>
                    </div>
                `
                $("#send-test-email-modal .main-content").html(successSendTpl);
                $("#send-test-email-modal").addClass("success");
                setTimeout( () => {
                    $("#send-test-email-modal .button-send").html("Send")
                },  5000)
            })
            .catch( e => {
                //sendTestEmail
                console.log("error", e )
                $("#send-test-email-modal .button-send").fadeTo("fast",1);
            });
        
    },
    sendEmailModal: () => {
        
        $("#send-test-email-modal").show().css({"display":"flex"});
        $("#send-test-email-modal").removeClass("success");
        let sendTestTpl = ``;

        sendTestTpl += `
            <h2>Send Test Email</h2>
            <p>Enter the email address where you want to send the test welcome email</p>

            <div class="div-email">
                <label>Email</label>
                <input type="email" name="emailaddress" class="emailtestinput"/>
                <div class="error-text">Invalid emaill address</div>
            </div>

            <button type="button" class="button-send">SEND</button>
        `

        $("#send-test-email-modal .main-content").html(sendTestTpl);
        modalState();
    },
    saveWelcomeEmailTemplate: () => {
        $(".welcome-email-container .button-save").fadeTo("fast",.3);

        emailSubjectTeacher = $(".teacher_welcome_email_subject").val();
        emailGreetingsTeacher = tinymce.get("teacher_body_greetings").getContent();
        emailBodyTeacher1 = tinymce.get("teacher_body_copy_1").getContent();
        emailBodyTeacher2 = $(".teacher_body_copy_2").html();
        emailBodyTeacher3 = tinymce.get("teacher_body_copy_3").getContent();

        emailSubjectStudent = $(".student_welcome_email_subject").val();
        emailGreetingsStudent = tinymce.get("student_body_greetings").getContent();
        emailBodyStudent1 = tinymce.get("student_body_copy_1").getContent();
        emailBodyStudent2 = $(".student_body_copy_2").html();
        emailBodyStudent3 = tinymce.get("student_body_copy_3").getContent();

        emailSubjectFamily = $(".institute_family_subject").val();
        emailGreetingsFamily = `<h2 style="font-family: 'Mikado'; font-style: normal; font-weight: 500; font-size: 24px; line-height: 150%; color: #37394a;text-align:left">`+tinymce.get("institute_family_greetings").getContent()+`</h2>`;
        emailBodyFamily1 = tinymce.get("institute_family_welcome_email_body_1").getContent();
        emailBodyFamily2 = tinymce.get("institute_family_welcome_email_body_2").getContent();
        
        j2jInstituteSettings.api.saveWelcomeEmail({"teacher_subject":emailSubjectTeacher, 
                                                    "teacher_greetings":emailGreetingsTeacher, 
                                                    "teacher_body1":emailBodyTeacher1, 
                                                    "teacher_body2":emailBodyTeacher2, 
                                                    "teacher_body3":emailBodyTeacher3, 

                                                    "student_subject":emailSubjectStudent, 
                                                    "student_greetings":emailGreetingsStudent, 
                                                    "student_body1":emailBodyStudent1, 
                                                    "student_body2":emailBodyStudent2, 
                                                    "student_body3":emailBodyStudent3, 

                                                    "institute_family_subject": emailSubjectFamily,
                                                    "institute_family_greetings": emailGreetingsFamily,
                                                    "institute_family_body1": emailBodyFamily1,
                                                    "institute_family_body2": emailBodyFamily2
                                                })
            .then( e => {
                 
                $(".welcome-email-container .button-save").html("Updates Saved!")
                $(".welcome-email-container .button-save").fadeTo("fast",1);

            })
            .catch( e => {
                console.log("Error", e)
            });
    },
    init : () => {
        j2jInstituteSettings.loadSchoolDetails();
    }
}

// init
j2jInstituteSettings.init();

// event listeners
$(document).on("click",".settings-tab-navigation a", e => {
    e.preventDefault();
    let target = $(e.currentTarget).attr("data-target");
    $(".settings-tab-navigation a").removeClass("active")
    $(e.currentTarget).addClass("active")

    $(".settings-tabs > .tab").removeClass("active");
    $(target).addClass('active')

    if(target == "#manage-institute-tab"){
        j2jInstituteSettings.renderDropzone();
        j2jInstituteSettings.loadSchoolDetails();
    }
})

$(document).on("click","#manage-institute-tab .bp-settings-container nav ul li a", e => {
    e.preventDefault();
    let target = $(e.currentTarget).attr("data-target");
    $("#manage-institute-tab .bp-settings-container nav ul li").removeClass("current selected");
    $(e.currentTarget).parent().addClass("current selected");

    $("#manage-institute-tab .bb-bp-settings-content > .tab").removeClass("active")
    $(target).addClass('active')

    
})

$(document).on("click", ".welcome-email-container .tab-menu a",e => {
    e.preventDefault()
    $(".welcome-email-container .tab-menu a, .welcome-email-container .tabs .tab-content").removeClass("active");
    $(e.currentTarget).addClass("active");

    let targetTab = $(e.currentTarget).attr("data-target");

    $(targetTab).addClass("active")

});


$(document).on("click",".institute-information-container .logo-update .button-update", e => {
    //$(".institute-information-container #logo-upload").trigger("click")
    $("#upload-insitute-logo-modal").show().css({"display":"flex"});
})
$(document).on("click",".institute-information-container .logo-update .button-remove", e => {
    $(".institute-information-container .logo-update .button-remove").fadeTo('fast',.3)
    j2jInstituteSettings.logoId = 0;
    j2jInstituteSettings.updateLogo();
})

$(document).on("click","#upload-insitute-logo-modal .button-submit-logo", e => {
    j2jInstituteSettings.updateLogo();
})


$(document).on("click",".institute-information-container .button-save", e => {
    j2jInstituteSettings.saveInstituteInfo();
})

$(document).on("click",".activity-feed-container .button-save", e=> {
    j2jInstituteSettings.saveActivityFeed();
})

$(document).on("click",".welcome-email-container .button-preview", e => {
    j2jInstituteSettings.activeEmailTab = $(".welcome-email-container .tab-menu a.active").text();
    j2jInstituteSettings.showEmailPreview();
})

$(document).on("click",".welcome-email-container .button-send-test-email", e => {
    j2jInstituteSettings.activeEmailTab = $(".welcome-email-container .tab-menu a.active").text();
    j2jInstituteSettings.sendEmailModal();
})

$(document).on("click","#send-test-email-modal .button-send", e => {
    let emailtestinput = $(".emailtestinput").val();    
    let pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    console.log(" pattern.test(email)",  pattern.test(emailtestinput))
    if( pattern.test(emailtestinput) ){
        $(".div-email").removeClass("error");

        j2jInstituteSettings.sendTestEmail();
    }else{
        $(".div-email").addClass("error");
    }

})


$(document).on("click",".welcome-email-container .button-save", e => {
    j2jInstituteSettings.saveWelcomeEmailTemplate();
})