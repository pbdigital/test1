$ = jQuery;
Dropzone.autoDiscover = false;
let schoolbypasstransient = true;
let JourneyManageClassrooms = {}
JourneyManageClassrooms = {
    importTeacherDropzone: "",
    importStudentsDropzone: "",
    importAvatarDropzone: "",
    importCoverPhotoDropzone: "",
    importFamiliesDropzone: "",
    schoolDetails: "",
    slickSlider: "",
    password: "",

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

    api: {
        school: {
            getuserSchoolDetails: async ( data ) => {
                return new Promise((resolve, reject) => {

                    if(JourneyManageClassrooms.schoolDetails != "" ){
                        resolve(JourneyManageClassrooms.schoolDetails);
                    }else{
                        
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
                                schoolbypasstransitent = false;
                                JourneyManageClassrooms.schoolDetails = d;

                                //console.log("typeof d.parent_school.meta._ulgm_total_seats[0]", typeof d.parent_school.meta._ulgm_total_seats)
                                JourneyManageClassrooms.classrooms.statistics.totalSeats = (typeof d.total_seats == "undefined") ? 0:d.total_seats;
                                JourneyManageClassrooms.classrooms.statistics.totalStudents = 0;
                                
                                let uniqueStudents = [];
                                let uniqueTeachers = [];
                                /*d.classrooms.map( classroom => {
                                    //JourneyManageClassrooms.classrooms.statistics.totalStudents += classroom.school_data.students.length
                                    classroom.school_data.students.map( student => {
                                        if(!uniqueStudents.includes(student.ID)){
                                            uniqueStudents.push(student.ID)
                                        }
                                    })
                                })*/


                                d.students.map( student => {
                                    if(!uniqueStudents.includes(student.ID)){
                                        uniqueStudents.push(student.ID)
                                    }
                                })

                                // include teacers in counting seats but only for institute schools do not include family groups
                                let isFamily = false;
                                JourneyManageClassrooms.schoolDetails.parent_school.tag.map( tag => {
                                    if(tag.slug == "family-group"){
                                        isFamily = true;
                                    }
                                })

                                d.teachers.map( teacher => {
                                    if(!uniqueTeachers.includes(teacher.ID)){
                                        uniqueTeachers.push(teacher.ID)
                                    }
                                })

                                
                                if(!isFamily){
                                    JourneyManageClassrooms.classrooms.statistics.totalStudents = uniqueStudents.length + uniqueTeachers.length;
                                }else{
                                    JourneyManageClassrooms.classrooms.statistics.totalStudents = uniqueStudents.length;
                                } 
                                //console.log("uniqueStudents",uniqueStudents, JourneyManageClassrooms.classrooms.statistics.totalSeats)

                                JourneyManageClassrooms.classrooms.statistics.remainingSeats = JourneyManageClassrooms.classrooms.statistics.totalSeats - JourneyManageClassrooms.classrooms.statistics.totalStudents;

                                if( JourneyManageClassrooms.classrooms.statistics.remainingSeats < 0 ) JourneyManageClassrooms.classrooms.statistics.remainingSeats = 0;
                                
                                resolve(d);
                            },
                            error: (d) => {
                                reject(`error /manage-classrooms/school}`);
                            }
                        });
                    }
                })
            },
            updateClassroomDetails: async ( data ) => {
                return new Promise((resolve, reject) => {

                    //console.log("data", data)
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/manage-classrooms/${data.id}`,
                        type: "PUT",
                        data: data.formData,
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
                            reject(`error /manage-classrooms/school}`);
                        }
                    });
                    
                })
            },

            createNewClassroom: async ( data ) => {
                return new Promise((resolve, reject) => {

                    $.ajax({
                        url: `${safarObject.apiBaseurl}/manage-classrooms`,
                        type: "POST",
                        data: data.formData,
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
                            reject(`error /manage-classrooms/school}`);
                        }
                    });
                    
                })
            },

            getClassrooms:  async (  ) => {
                
                return new Promise((resolve, reject) => {
                    console.log("test", `${safarObject.ajaxurl}?action=manage_classrooms_grid`)
                    $.ajax({
                        url: `${safarObject.ajaxurl}?action=manage_classrooms_grid`,
                        type: "get",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /manage-classrooms/school}`);
                        }
                    });
                    
                })
            },

            classroom: {
                removeTeacher: async (data) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: `${safarObject.apiBaseurl}/manage-classrooms/${data.id}/teacher`,
                            type: "DELETE",
                            data: {
                                teacher_id: data.teacher_id
                            },
                            headers: {
                                "X-WP-Nonce": safarObject.wpnonce
                            },
                            beforeSend: (xhr) => {
                                
                            },
                            success: (d) => {
                                resolve(d);
                            },
                            error: (d) => {
                                reject(`error /manage-classrooms/${data.id}/teacher}`);
                            }
                        });
                    })
                },
                removeAdmin: async (data) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: `${safarObject.apiBaseurl}/manage-classrooms/${data.id}/admin`,
                            type: "DELETE",
                            data: {
                                teacher_id: data.teacher_id
                            },
                            headers: {
                                "X-WP-Nonce": safarObject.wpnonce
                            },
                            beforeSend: (xhr) => {
                                
                            },
                            success: (d) => {
                                resolve(d);
                            },
                            error: (d) => {
                                reject(`error /manage-classrooms/${data.id}/admin}`);
                            }
                        });
                    })
                },

                removeStudent: async (data) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: `${safarObject.apiBaseurl}/manage-classrooms/${data.id}/student`,
                            type: "DELETE",
                            data: {
                                student_id: data.student_id
                            },
                            headers: {
                                "X-WP-Nonce": safarObject.wpnonce
                            },
                            beforeSend: (xhr) => {
                                
                            },
                            success: (d) => {
                                resolve(d);
                            },
                            error: (d) => {
                                reject(`error /manage-classrooms/${data.id}/removeStudent}`);
                            }
                        });
                    })
                },

                archiveClassroom: async (classroomid) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: `${safarObject.apiBaseurl}/manage-classrooms/${classroomid}`,
                            type: "DELETE",
                            headers: {
                                "X-WP-Nonce": safarObject.wpnonce
                            },
                            beforeSend: (xhr) => {
                                
                            },
                            success: (d) => {
                                resolve(d);
                            },
                            error: (d) => {
                                reject(`error /manage-classrooms/${classroomid}`);
                            }
                        });
                    })
                }
            },

            
        },

        user: {
            updateUserDetails: async ( data ) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/manage-classrooms/user/${data.userid}`,
                        type: "PUT",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        data: data.formData,
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /manage-classrooms/user/${data.userid}`);
                        }
                    });
                    
                })
            },
            saveSelectedInstitute: async ( data ) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/manage-classrooms/user/select_institute`,
                        type: "PUT",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        data: {instituteid: data.id},
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /manage-classrooms/user/${data.userid}`);
                        }
                    });
                    
                })
            },

            savePassword: async ( data ) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/user/password`,
                        type: "PUT",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        data: {
                            user_id: data.user_id,
                            password: data.password
                        },
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /user/password`);
                        }
                    });
                    
                })
            },
        },

        admin: {
            updateInstituteAdmins: async ( data ) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/manage-classrooms/institute/${data.id}/admins`,
                        type: "PUT",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        data: {adminids: data.adminids},
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /manage-classrooms/institute/${data.id}/admins`);
                        }
                    });
                    
                })
            }
        },

        families: {
            addFamily: async (data) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/school/family`,
                        type: "POST",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        data,
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /school/family`);
                        }
                    });
                    
                })
            },
            updateFamily: async (d) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/school/family/${d.id}`,
                        type: "PUT",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        data: d.form,
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /school/family`);
                        }
                    });
                    
                })
            },
            addChildSave: async (d) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/school/family/${d.id}/child`,
                        type: "PUT",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        data: d.form,
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /school/family/child`);
                        }
                    });
                    
                })
            },
            deleteFamily: async (data) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/school/family/${data.id}`,
                        type: "DELETE",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /school/family/${data.id}`);
                        }
                    });
                    
                })
            },
            saveImportedFamilies: async (data) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/school/families/import`,
                        type: "POST",
                        data: {families:data, "institute_id":manageClassroomsJs.selectedInstituteId},
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`error /school/families/import`);
                        }
                    });
                    
                })
            },
            removeChild: async (data) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/school/families/${data.familygroupid}/remove_child`,
                        type: "DELETE",
                        data: {childid:data.childid},
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        beforeSend: (xhr) => {
                            
                        },
                        success: (d) => {
                            resolve(d);
                        },
                        error: (d) => {
                            reject(`${safarObject.apiBaseurl}/school/families/${data.family_group_id}/remove_child`);
                        }
                    });
                    
                })
            },
            
        },
        
    },

    isValidEmail : (email) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
      

    users: {
        
        saveEdits: (form) => {
            $(form).find(".btn-save").fadeTo("fast",.3);
            JourneyManageClassrooms.api.user.updateUserDetails({
                formData: $(form).serialize(),
                userid: $(form).find("input[name=userid]").val()
            })
            .then( e => {
                $(form).find(".btn-save").fadeTo("fast",1).html("Updates Saved!");
                JourneyManageClassrooms.classrooms.reloadReloadables();
                setTimeout( () => {
                    $(form).find(".btn-save").html("SAVE")
                },5000)
            })
            .catch( e => {
                console.log("error", e )
            })
        }
    }, 

    institutes: {
        searchKey: "",
        search: () => { 
            let instituteFound = false;
            $(".classrooms-grid .classroom:not('.new')").each( function() {
                if( $(this).text().toLowerCase().includes(JourneyManageClassrooms.institutes.searchKey.toLowerCase()) ){
                    $(this).show();
                    instituteFound = true;
                }else{
                    $(this).hide();
                }
            });
            
            if(!instituteFound){
                $(".institutes-has-no-record").addClass("active")
            }else{
                $(".institutes-has-no-record").removeClass("active");
            }
        },
    },

    classrooms: {
        searchKey: "",
        current: "",

        statistics: {
            totalSeats: 0,
            totalStudents: 0,
            remainingSeats: 0,
        },

        getById: schoolId => {
            //console.log("JourneyManageClassrooms.schoolDetails", JourneyManageClassrooms.schoolDetails)
            JourneyManageClassrooms.schoolDetails.classrooms.map( classroom => {
                if(classroom.ID == schoolId){
                    JourneyManageClassrooms.classrooms.current = classroom;
                }
            })
            return false;
        },

        search: () => { 
            $(".classrooms-grid .classroom:not('.new')").each( function() {
                if( $(this).find(".class-name").text().toLowerCase().includes(JourneyManageClassrooms.classrooms.searchKey.toLowerCase()) ){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            });
        },

        saveUpdates: (form) => {
            $(form).find(".btn-save, .btn-add").fadeTo("fast",.3)

            if($(form).find(`input[name=tab]`).val() == "courses"){
                $(".hidden-active-courses").html("")
                $(form).find(`#select-active-courses option`).each( function(){
                    $(".hidden-active-courses").append(`<input type="hidden" name="active_courses[]" value="${$(this).val()}" />`)
                })
            }

            if($(form).find(`input[name=tab]`).val() == "teacher-add" 
                || $(form).find(`input[name=tab]`).val() == "student-add"
                || $(form).find(`input[name=tab]`).val() == "student-add-single"  ){
                $(".btn-add-teacher").fadeTo("fast",.3)
            }

            if(JourneyManageClassrooms.classrooms.current == ""){
                classroomID = JourneyManageClassrooms.schoolDetails.parent_school.school_id;
            }else{
                classroomID = JourneyManageClassrooms.classrooms.current.ID;
            }

            //learndash_parent_group_id
            if( manageClassroomsJs.post_slug == "manage-family" ){
                classroomID = JourneyManageClassrooms.schoolDetails.school.learndash_parent_group_id;
            }

            let isSubjectSelected = true;
            if($(form).find(`input[name=tab]`).val() == "classroom"){
                isSubjectSelected = false;
                $(form).find(".subjects input[type=checkbox]").each( function(){
                    if($(this).is(":checked")) isSubjectSelected = true;
                });
            }

            if(isSubjectSelected){
                $(".create-new-update-classroom-message").html(``)
                $(`.div-student-add-error-message`).html(``);

                JourneyManageClassrooms.api.school.updateClassroomDetails({
                        id: classroomID,
                        formData: $(form).serialize()
                })
                .then( e => {
                    $(form).find(".btn-save, .btn-add").fadeTo("fast",1)
                    
                    if($(form).find(`input[name=tab]`).val() == "email-classroom"){
                        $(form).find(".btn-save").html("Email Sent")
                        $(form).find("input[name=subject]").val("");
                        $(form).find("textarea").val("")
                    }else{
                        $(form).find(".btn-save").html("Updates Saved")
                    }

                    if($(form).find(`input[name=tab]`).val() == "teacher-add" 
                        || $(form).find(`input[name=tab]`).val() == "student-add"
                        || $(form).find(`input[name=tab]`).val() == "student-add-single" ){
                        $(".btn-add-teacher").fadeTo("fast",1)
                    }

                    setTimeout( () => {
                        
                        if($(form).find(`input[name=tab]`).val() == "email-classroom"){
                            $(form).find(".btn-save").html("Send")
                        }else{
                            $(form).find(".btn-save").html("Save")
                            $(".btn-add-teacher").html("ADD")
                        }
                    }, 3000 )

                    if($(form).find(`input[name=tab]`).val() == "student-add"){
                        if(!e.success){
                            $(`.div-student-add-error-message`).html(`
                                <div class="error-message">${e.error_message}</div>
                            `);
                        }else{

                        }
                    }

                    // reload classrooms grid and schools details
                    JourneyManageClassrooms.classrooms.reloadReloadables();

                    if( manageClassroomsJs.post_slug == "manage-classroom-teachers" 
                        || manageClassroomsJs.post_slug == "manage-classroom-students" 
                    ){
                        if($(form).find(`input[name=tab]`).val() == "student-add"){
                            if(e.success){
                                $("#manage-classroom-modal").hide();
                                modalState();
                            }
                        }else{
                            $("#manage-classroom-modal").hide();
                            modalState();
                        }

                    }

                    if( manageClassroomsJs.post_slug == "manage-family"){
                        if(e.success){
                            $("#manage-classroom-modal").hide();
                            modalState();
                        }
                    }


                    // if save update is coming from add new classroom modal
                    // navigate to next step
                    if( jQuery("#manage-classroom-add-new").is(":visible") ){
                        JourneyManageClassrooms.classrooms.createNewNavigate(true); // isNext == true
                    }
                })
                .catch( e => {

                });
            }else{
                $(form).find(".btn-save, .btn-add").fadeTo("fast",1)
                $(".create-new-update-classroom-message").html(`<div class="error-message">Please select at least one subject</div>`)
            }
            
            return false;
        },

        createNew : (form) => {
            let isSubjectSelected = false;
            $(form).find(".subjects input[type=checkbox]").each( function(){
                if($(this).is(":checked")) isSubjectSelected = true;
            });
            
            if(isSubjectSelected){
                $(form).find("button[type=submit]").fadeTo("fast",.3)

                $("#manage-classroom-add-new .btn-save").attr("disabled","disabled");
                JourneyManageClassrooms.api.school.createNewClassroom( { formData:$(form).serialize() } ).then( e => {
                    $(form).find("button[type=submit]").fadeTo("fast",1);
                    JourneyManageClassrooms.classrooms.createNewNavigate(true);
                    
                    JourneyManageClassrooms.schoolDetails = "";
                    schoolbypasstransient = true;

                    let newGroupId = e.id;
                    JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                        JourneyManageClassrooms.classrooms.getById(newGroupId);
                        $(".new-group-id").val(newGroupId);
                        $(".btn-create-new-button").html("UPDATE")
                        console.log(" create new classroom ", newGroupId, JourneyManageClassrooms.classrooms.current.ID );

                        JourneyManageClassrooms.classrooms.reloadReloadables();
                        $("#manage-classroom-add-new .btn-save").removeAttr("disabled");
                    });

                }).catch( e => {
                    console.log("error createNewClassroom ", e)
                })
                $(".create-new-update-classroom-message").html(``)
            }else{
                $(".create-new-update-classroom-message").html(`<div class="error-message">Please select at least one subject</div>`)
            }
            return false;
        },

        createNewNavigate : (next) => {
            let targetElement = "";
            if(next){
                targetElement = $(".steps-nav a:not(.completed):first");
                targetElement.attr("data-target");
                targetElement.addClass("completed")

                nextElement = targetElement.next();
                nextElement.addClass("active");
                nextElement.trigger("click")
            }else{
                targetElement = $(".steps-nav a.active:first");
                targetElement.attr("data-target");
                targetElement.removeClass("active")

                nextElement = targetElement.prev();
                nextElement.addClass("active");
                nextElement.removeClass("completed");
                nextElement.trigger("click")

            }
            console.log("target", targetElement )
        },

        refreshTopStatistics: () => {
            $(".total-seats .num").html(JourneyManageClassrooms.classrooms.statistics.totalSeats);
            $(".total-seats-used .num").html(JourneyManageClassrooms.classrooms.statistics.totalStudents);
            $(".total-seats-remaining .num").html(JourneyManageClassrooms.classrooms.statistics.remainingSeats);

            if( JourneyManageClassrooms.classrooms.statistics.remainingSeats <= 0){
                //console.log("localStorage.getItem( `noseatsavailable_${safarObject.user_id}` )", localStorage.getItem( `noseatsavailable_${safarObject.user_id}` ), `noseatsavailable_${safarObject.user_id}`)
                if( localStorage.getItem( `noseatsavailable_${safarObject.user_id}` ) != 1 ){
                    $("#manage-classroom-modal").show().css({"display":"flex"});
                    modalContent = `
                    <div class="no-more-seats-message">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="80" height="80" rx="12" fill="#FDF2E5"/>
                        <path d="M28.78 28.2752H49.6456C50.3232 28.2752 50.872 27.7264 50.872 27.0488V17.2264C50.872 16.5488 50.3232 16 49.6456 16H28.78C28.1024 16 27.5536 16.5488 27.5536 17.2264V27.0432C27.5536 27.7264 28.1024 28.2752 28.78 28.2752Z" fill="#F2A952"/>
                        <path d="M50.872 59.8816C50.872 60.3912 51.2864 60.8 51.7904 60.8C52.3 60.8 52.7088 60.3856 52.7088 59.8816V48.5248H50.8664V59.8816H50.872Z" fill="#F2A952"/>
                        <path d="M25.7112 59.8816C25.7112 60.3912 26.1256 60.8 26.6296 60.8C27.1392 60.8 27.548 60.3856 27.548 59.8816V48.5248H25.7056V59.8816H25.7112Z" fill="#F2A952"/>
                        <path d="M50.256 53.744H28.164V55.5864H50.256V53.744Z" fill="#F2A952"/>
                        <path d="M56.0912 31.9544H22.3344C21.4888 31.9544 20.8 32.6432 20.8 33.4888C20.8 34.3344 21.4888 35.0232 22.3344 35.0232H56.0856C56.9312 35.0232 57.62 34.3344 57.62 33.4888C57.6256 32.6432 56.9368 31.9544 56.0912 31.9544Z" fill="#F2A952"/>
                        <path d="M27.5536 38.708L23.8688 46.072V47.9144H54.5569V46.072L50.872 38.708H27.5536Z" fill="#F2A952"/>
                        <path d="M49.0352 35.6392H47.1928V38.092H49.0352V35.6392Z" fill="#F2A952"/>
                        <path d="M49.0352 28.8856H47.1928V31.3384H49.0352V28.8856Z" fill="#F2A952"/>
                        <path d="M31.2384 28.8856H29.396V31.3384H31.2384V28.8856Z" fill="#F2A952"/>
                        <path d="M31.2384 35.6392H29.396V38.092H31.2384V35.6392Z" fill="#F2A952"/>
                        <path d="M22.9504 35.6392V39.3016C22.9504 40.7464 23.7176 42.012 24.86 42.7176L25.6888 41.06C25.1456 40.668 24.7872 40.024 24.7872 39.3016V35.6392H22.9504Z" fill="#F2A952"/>
                        </svg>
                        <h2>No seats available</h2>
                        <p>All of your seats has been taken. Add more<br/>seats in order to add teacher/student.</p>
                        <a href="${manageClassroomsJs.instituteProdUrl}" target="_blank" class="add-more-seats">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Add More Seats</span>
                        </a>
                    </div>
                    `;
                    $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
                    modalState();

                    localStorage.setItem(`noseatsavailable_${safarObject.user_id}`, 1);
                }

            }
        },

        reloadReloadables: () => {
            JourneyManageClassrooms.schoolDetails = "";
            schoolbypasstransient = true;

            switch(manageClassroomsJs.post_slug){
                case "manage-classroom":
                    
                    JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {

                        // statistics
                        JourneyManageClassrooms.classrooms.refreshTopStatistics();

                        JourneyManageClassrooms.classrooms.getById( JourneyManageClassrooms.classrooms.current.ID )
                        
                        $(`#tab-teachers`).html(JourneyManageClassrooms.classrooms.templates.manageClassroom.tabTeachers())
                        $(`#tab-students`).html(JourneyManageClassrooms.classrooms.templates.manageClassroom.tabStudents())
                        
                    })

                    // classroom grid
                    let skeletonLoaders = ``;
                    for(i = 0; i < 8; i++){
                        skeletonLoaders += `<div class="skeleton-loader classrooms-grid" style="height:255px; width:100%"></div>`
                    }
                    $(".classrooms-grid").html(`${skeletonLoaders}`);
                    JourneyManageClassrooms.api.school.getClassrooms()
                        .then( e => {
                            $(".classrooms-grid").html(e);


                        })
                        .catch( e => {
                            console.log("error getClassrooms", e)
                        });

                    break;
                    
                case "manage-classroom-teachers":
                    JourneyManageClassrooms.teachers.list();
                    break;
                case "manage-classroom-students": case "manage-family":
                    JourneyManageClassrooms.students.list();
                    break;
                case "manage-classroom-admins":
                    JourneyManageClassrooms.admins.list();
                    break;
            }

            return false;

        },

        templates : {
            
            manageClassroom: {

                settings: () => {
                    let optionsBb =  [{ 
                                            "text":"Activity Feeds",
                                            "key" : "activity_feed",
                                            "description": "Which members of this group are allowed to post into the activity feed?",
                                            "tooltip": "Short description about the about activity feeds goes here. Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore. "
                                        },
                                        {
                                            "text":"Photos",
                                            "key" : "photos",
                                            "description": "Which members of the classroom are allowed to upload photos?",
                                            "tooltip": "Short description about the about activity feeds goes here. Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore. "
                                        },
                                        {
                                            "text":"Documents",
                                            "key" : "documents",
                                            "description": "Which members of the classroom are allowed to upload documents?",
                                            "tooltip": "Short description about the about activity feeds goes here. Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore. "
                                        },
                                        {
                                            "text":"Videos",
                                            "key" : "videos",
                                            "description": "Which members of the classroom are allowed to upload videos?",
                                            "tooltip": "Short description about the about activity feeds goes here. Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore. "
                                        }
                                ];
                    let radioSelects = [
                        { "text":"All classroom members",
                          "value":"members",
                          "description": "All classroom members can lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. "
                        },
                        {   "text":"Teachers and Admin only",
                            "value":"mods",
                            "description": "Teachers and Admin onlycan lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. "
                        },
                        {   "text":"Admin only",
                            "value":"admins",
                            "description": "Admin only can lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. "
                        },

                    ];

                    tpl = `
                    <div class="manage-classroom-container">
                        <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                            <input type="hidden" name="tab" value="settings" />
                            <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                    `;

                    optionsBb.map( e => {
                        let selections = ``
                        let selectedItem = "";
                        radioSelects.map( r => {
                            let radioChecked = "";
                            
                            if( JourneyManageClassrooms.classrooms.current.school_data.settings[e.key] == r.value ){
                                radioChecked = "checked"
                                selectedItem = `<div class="text">${r.description}</div>`;
                            }
                            selections += `
                                <div class="select-item">
                                    <label>
                                        <div class="radio-container">
                                            <input type="radio" ${radioChecked} name="${e.key}" value="${r.value}" data-description="${r.description}" required/>
                                            <span></span>
                                        </div>
                                        <div class="radio-text">${r.text}</div>
                                    </label>
                                </div>
                            `
                        })
                        tpl += `
                            <div class="classroom-settings-row">
                                <div class="top">
                                    <h2>${e.text}</h2>
                                    <span>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_2537_20711)">
                                        <path d="M7.57508 7.5C7.771 6.94306 8.15771 6.47342 8.66671 6.17428C9.17571 5.87513 9.77416 5.76578 10.3561 5.86559C10.938 5.96541 11.4658 6.26794 11.846 6.71961C12.2262 7.17128 12.4343 7.74294 12.4334 8.33333C12.4334 10 9.93342 10.8333 9.93342 10.8333M10.0001 14.1667H10.0084M18.3334 10C18.3334 14.6024 14.6025 18.3333 10.0001 18.3333C5.39771 18.3333 1.66675 14.6024 1.66675 10C1.66675 5.39763 5.39771 1.66667 10.0001 1.66667C14.6025 1.66667 18.3334 5.39763 18.3334 10Z" stroke="#5D53C0" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_2537_20711">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                        </defs>
                                        </svg>
                                        What is this?
                                        <span class="tooltip">${e.tooltip}</span>
                                    </span>
                                </div>
                                <div class="description">${e.description}</div>
                                <div class="selections">
                                    ${selections}
                                </div>
                                <div class="description-selected-item">${selectedItem}</div>
                            </div>
                        `
                    })


                    tpl += `
                            <div class="button-container">
                                <button type="button" class="btn-back">Cancel</button>
                                <button type="submit" class="btn-save">Save</button>
                            </div>

                        </form>
                    </div>
                    `
                    
                    return tpl;
                },

                importStudents: () => {
                    tpl = `
                        ${JourneyManageClassrooms.classrooms.studentsSeatsAvailable()}

                        <div class="download">
                            <span>Import students using our template.</span>
                            <a href="/wp-content/themes/buddyboss-theme-child/page-templates/manage-classrooms/UsersDemoStudents.csv" download>Download Template</a>
                        </div>

                        <div id="import-students-dropzone-manage-classroom" class="dropzone" action="?" ></div>
                        <div class="imported-students-container-manage-classroom"></div>

                        <div class="button-container">
                            <button type="button" class="btn-back btn-back-create-new-class-student">BACK</button>
                            <button type="submit" class="btn-save btn-import btn-student-submit">SUBMIT</button>
                        </div>
                    `
                    return tpl;
                    //$("#add-student-import-list").html(tpl);
                    //JourneyManageClassrooms.students.renderDropzone();
                },

                renderImportStudentsDropzone: () => {
             
            
                    if(JourneyManageClassrooms.importStudentsDropzone != "") JourneyManageClassrooms.importStudentsDropzone.destroy();
                    JourneyManageClassrooms.importStudentsDropzone = new Dropzone("div#import-students-dropzone-manage-classroom", 
                        { 
                            url: `${safarObject.apiBaseurl}/manage-classrooms/upload_csv`,
                            maxFilesize: 10485760,
                            acceptedFiles: ".csv",
                            headers: {
                                'X-WP-Nonce': safarObject.wpnonce,
                            },
                        }
                    );
                    $(".btn-import").fadeTo("fast",.3)
                    JourneyManageClassrooms.importStudentsDropzone.on("addedfile", file => {
                        if(file.size >= 10485760){
                            console.log("file is greater than 10mb", file.zize)
                            JourneyManageClassrooms.importStudentsDropzone.removeFile(file);
                            return false;
                        }else{
                            
                            return file;
                        }
                    });
                    
            
            
                    JourneyManageClassrooms.importStudentsDropzone.on("complete", file => {
                        console.log("complete", $.parseJSON(file.xhr.response) )
                
                        let xhrResponse = $.parseJSON(file.xhr.response);
                        var classroomID = JourneyManageClassrooms.classrooms.current.ID
                       
                        $("#import-students-dropzone-manage-classroom .dz-default").remove();
                        
                        JourneyManageClassrooms.api.school.updateClassroomDetails({
                            id: classroomID,
                            formData: {
                                "tab": "student-import",
                                "classroom_id": classroomID,
                                "students": xhrResponse
                            }
                        })
                        .then( e => {
                            //imported-students-container
                            let studentImportResult = e;
                            $("#import-students-dropzone-manage-classroom").hide();

                            JourneyManageClassrooms.schoolDetails = "";
                            schoolbypasstransient = true;


                            JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                                JourneyManageClassrooms.classrooms.getById(classroomID);
                                
                                $(".seats-available .num").html(JourneyManageClassrooms.classrooms.statistics.remainingSeats)

                                let successfullImports = "";
                                if( studentImportResult.successfull_imports.length > 0 ){
                                    successfullImports = `
                                        <div class="successfull-imports">
                                            <span class="icon">
                                                <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21 11.08V12C20.9988 14.1564 20.3005 16.2547 19.0093 17.9818C17.7182 19.709 15.9033 20.9725 13.8354 21.5839C11.7674 22.1953 9.55726 22.1219 7.53447 21.3746C5.51168 20.6273 3.78465 19.2461 2.61096 17.4371C1.43727 15.628 0.879791 13.4881 1.02168 11.3363C1.16356 9.18455 1.99721 7.13631 3.39828 5.49706C4.79935 3.85781 6.69279 2.71537 8.79619 2.24013C10.8996 1.7649 13.1003 1.98232 15.07 2.85999M21 3.99999L11 14.01L8.00001 11.01" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            <span class="text">You have successfully imported ${studentImportResult.successfull_imports.length} out of ${xhrResponse.length} students</span>
                                        </div>
                                    `
                                }

                                if( studentImportResult.error_imports.length > 0){
                                    let errorStudentsImport = ``;
                                    studentImportResult.error_imports.map( errorStudent => {
                                        errorStudentsImport += `<div>- ${errorStudent.first_name} ${errorStudent.last_name}, ${errorStudent.error_message}</div>`
                                    })
                                    successfullImports += ` <div class="error-imports" createnewclassroom >
                                            <span class="icon">
                                                <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21 13V21M21 29H21.02M41 21C41 32.0457 32.0457 41 21 41C9.9543 41 1 32.0457 1 21C1 9.9543 9.9543 1 21 1C32.0457 1 41 9.9543 41 21Z" stroke="#EF746F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            
                                            </span>
                                            <span class="text">
                                                Unable to add ${studentImportResult.error_imports.length} students ${studentImportResult.error_message}:
                                                ${errorStudentsImport}
                                            </span>
                                        </div>
                                    `
                                }

                                let importMore = `
                                    <button class="button-import-more">Import More Students</button>
                                `

                                /** Show Imported Students */
                                studentItems = ``;
                                studentImportResult.successfull_imports.map( student => {
                                    studentItems += `
                                        <div class="list-item">
                                            <div class="student-name">${student.first_name} ${student.last_name}</div>
                                            <div class="username">${student.username}</div>
                                            
                                        </div>
                                    `
                                })
                                    
                                if(studentItems == ""){
                                    studentItems = `<div class="no-users-added">No students added yet</div>`;
                                }

                                studentsTpl = `
                                <div class="list"  > 
                                    <div class="header">
                                        <div class="student-name">Student Name</div>
                                        <div class="email">Username</div>
                                    </div>
                                    <div class="body items" test123>
                                        ${studentItems}
                                    </div>
                                </div>`

                                $(".imported-students-container-manage-classroom").html(  successfullImports + studentsTpl + importMore );
                                
                                JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                                    JourneyManageClassrooms.classrooms.refreshTopStatistics();
                                })
            
                                JourneyManageClassrooms.api.school.getClassrooms()
                                    .then( e => {
                                        $(".classrooms-grid").html(e);
            
                                    })
                                    .catch( e => {
                                        console.log("error getClassrooms", e)
                                    });

                                $(".btn-import").fadeTo("fast",1)

                            });
                            
                        })
                        .catch( e => {
                            console.log("error", e)
                        })
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

                tabClassroom: () => {

                    let subjects = ``;
                    //console.log("JourneyManageClassrooms.classrooms.current.school_data.categories", typeof JourneyManageClassrooms.classrooms.current.school_data.categories)
                    className = "";
                    if(JourneyManageClassrooms.classrooms.current != ""){
                        className = JourneyManageClassrooms.classrooms.current.post_title;
                    }
                    manageClassroomsJs.subjects.map( subject => {

                        let checked = "";
                        if(JourneyManageClassrooms.classrooms.current != ""){
                            JourneyManageClassrooms.classrooms.current.school_data.categories.map( cat => {
                                if(cat.term_id == subject.term_id ){
                                    checked = " checked='checked' ";
                                }
                            })
                        }

                        subjects += `
                            <label class="subject">
                                <span class="checkbox">
                                    <input type="checkbox" ${checked} name="subjects[]" value="${subject.term_id}"/> <span></span>
                                </span>
                                <span>${subject.name}</span>
                            </label>
                        `
                    })

                    if( jQuery("#manage-classroom-add-new").is(":visible") ){
                        formHeader = `
                        <form class="" onsubmit="JourneyManageClassrooms.classrooms.createNew(this); return false;">
                            <input type="hidden" name="tab" value="classroom-new" />
                            <input type="hidden" name="parent_group_id" value="${JourneyManageClassrooms.schoolDetails.school.learndash_parent_group_id}"/>
                            <input type="hidden" name="group_id" value="" class="new-group-id" value="0"/>
                        `
                    }else{
                        formHeader = `
                        <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                            <input type="hidden" name="tab" value="classroom" />
                            <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                        `
                    }


                    return `
                            ${formHeader}
                            <div class="field-group">
                                <label class="field-label">Class Name</label>
                                <div class="field-input">
                                    <input type="text" required name="class_name" required placeholder="Class Name" 
                                    value="${className}"
                                    />
                                </div>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Subject</label>
                                <div class="field-input subjects">
                                    ${subjects}
                                </div>

                                <div class="create-new-update-classroom-message"></div>

                                <div class="button-container">
                                    ${
                                        (JourneyManageClassrooms.classrooms.current == "")
                                        ? 
                                        `
                                            <button type="submit" class="btn-save btn-create-new-button">CREATE</button>
                                        `
                                        :
                                        `
                                        <button type="button" class="btn-back">Cancel</button>
                                        <button type="submit" class="btn-save">Save</button>
                                        `
                                    }
                                    
                                </div>
                                <div class="classroom-update-message"></div>
                            </div>
                        </form>
                    `;
                },
                tabStudents: () => {

                    //console.log("JourneyManageClassrooms.schoolDetails.students", JourneyManageClassrooms.schoolDetails.students)
                    let studentsList = ``;
                    if(JourneyManageClassrooms.classrooms.current != ""){
                        JourneyManageClassrooms.classrooms.current.school_data.students.map( student => {
                            studentsList += `
                                <div class="list-item">
                                    <div class="student-name">${student.data.first_name} ${student.data.last_name}</div>
                                    <div class="username">${student.data.user_login}</div>
                                    <div class="action">
                                        <input type="hidden" name="students[]" value="${student.data.ID}" />
                                        <button type="button" class="btn-remove-list-item">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18 6L6 18" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 6L18 18" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            `
                        })
                    }

                    if(studentsList == "") studentsList = `<div class="no-users-added">No students added yet</div>`

                    let manageButtons = ``
                    let gridTemplateCss = ``
                    if(JourneyManageClassrooms.classrooms.current.school_data.is_teachers_classroom){
                        manageButtons = `<a href="#tab-students-manage" data-target="#tab-students-manage" class="active" id="a-manage-student">Manage Students</a>`
                        gridTemplateCss = `style="grid-template-columns:1fr" `
                    }else{
                        manageButtons = `<a href="#tab-students-manage" data-target="#tab-students-manage" class="active" id="a-manage-student">Manage Students</a>
                        <a href="#tab-students-add-student" data-target="#tab-students-add-student" class="">Add Student</a>`
                    }

                    return `
                    <div class="tab-students-nav nav-tabs" ${gridTemplateCss}>
                        ${manageButtons}
                    </div>

                    <div class="student-tabs tabs">

                        <div class="tab active" id="tab-students-manage">
                            <h3>Manage Students</h3>

                            <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                                <input type="hidden" name="tab" value="student" />
                                <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                                <div class="list">
                                    <div class="header">
                                        <div class="">STUDENT NAME</div>
                                        <div class="">USERNAME</div>
                                        <div class="">ACTION</div>
                                    </div>
                                    <div class="body">
                                        ${
                                            studentsList
                                        }
                                    </div>
                                </div>

                                <div class="button-container">
                                    <button type="button" class="btn-back">Cancel</button>
                                    <button type="submit" class="btn-save">Save</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab" id="tab-students-add-student">
                            <h3>Add Student</h3>
                            <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                                <input type="hidden" name="tab" value="student-add" />
                                <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                                <input type="hidden" name="remaining_seat" value="${JourneyManageClassrooms.classrooms.statistics.remainingSeats}" />
                                ${JourneyManageClassrooms.students.templates.addForm(JourneyManageClassrooms.classrooms.current.school_data.ID)}
                                <div class="div-student-add-error-message"></div>

                                <button type="submit" class="btn-add-teacher" ${(JourneyManageClassrooms.classrooms.statistics.remainingSeats < 1) ? `disabled=disabled`:``} >ADD</button>

                                <div class="button-container">
                                    <button type="button" class="btn-back">Cancel</button>
                                    <button type="submit" class="btn-save" ${(JourneyManageClassrooms.classrooms.statistics.remainingSeats < 1) ? `disabled=disabled`:``}>Save</button>
                                </div>

                            </form>
                        </div>

                        <div class="tab" id="tab-students-add-import">
                            
                        </div>

                    </div>
                    `
                },

                tabTeachers: () => {
                    //console.log("JourneyManageClassrooms.schoolDetails.teachers", JourneyManageClassrooms.schoolDetails.teachers)
                    let teachersList = `<div class="no-users-added">No teachers added yet</div>`;

                    //console.log("typeof JourneyManageClassrooms.classrooms.current.school_data.teachers ,", typeof JourneyManageClassrooms.classrooms.current.school_data.teachers , typeof JourneyManageClassrooms.classrooms.current.school_data.teachers.length )
                    if(JourneyManageClassrooms.classrooms.current != ""){
                        if( typeof JourneyManageClassrooms.classrooms.current.school_data.teachers == "object"){
                            teachersList = ``;
                            JourneyManageClassrooms.classrooms.current.school_data.teachers.map( teacher => {
                                
                                teachersList += `
                                    <div class="list-item">
                                        <div class="teacher-name">${teacher.data.first_name} ${teacher.data.last_name}</div>
                                        <div class="username">${teacher.data.user_email}</div>
                                        <div class="action">
                                            <input type="hidden" name="teachers[]" value="${teacher.data.ID}" />
                                            <button type="button" class="btn-remove-list-item">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M18 6L6 18" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6 6L18 18" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                `
                            })
                        }
                    }

                    let manageButtons = ``
                    let gridTemplateCss = ``
                    if(JourneyManageClassrooms.classrooms.current.school_data.is_teachers_classroom){
                        manageButtons = `<a href="#tab-teacher-manage" data-target="#tab-teacher-manage" class="active">Manage Teachers</a>`
                        gridTemplateCss = `style="grid-template-columns:1fr" `
                    }else{
                        manageButtons = `<a href="#tab-teacher-manage" data-target="#tab-teacher-manage" class="active">Manage Teachers</a>
                        <a href="#tab-teacher-add-teacher" data-target="#tab-teacher-add-teacher" class="">Add Teacher</a>`
                    }

                    return `
                    <div class="tab-teacher-nav nav-tabs" ${gridTemplateCss}>
                        ${manageButtons}
                    </div>

                    <div class="teacher-tabs tabs">

                        <div class="tab active" id="tab-teacher-manage">
                            <h3>Manage Teachers</h3>

                            <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                                <input type="hidden" name="tab" value="teacher" />
                                <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                                <div class="list">
                                    <div class="header">
                                        <div class="">TEACHER NAME</div>
                                        <div class="">EMAIL</div>
                                        <div class="">ACTION</div>
                                    </div>
                                    <div class="body">
                                        ${
                                            teachersList
                                        }
                                    </div>
                                </div>

                                <div class="button-container">
                                    <button type="button" class="btn-back">Cancel</button>
                                    <button type="submit" class="btn-save">Save</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab" id="tab-teacher-add-teacher">
                            <h3>Add Teacher</h3>

                            <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                                <input type="hidden" name="tab" value="teacher-add" />
                                <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                                <div class="field-group">
                                    
                                    <div class="field-heading">
                                        <label class="field-label">Teacher Name</label>
                                        <label class="field-label-checkbox">
                                            <span class="checkbox">
                                                <input type="checkbox" class="class-new-teacher-exists" /> <span></span>
                                            </span>
                                            Teacher already exists?
                                        </label>
                                    </div>

                                    <div class="field-input field-teacher-exists search-teacher-container">
                                        <input type="text" name="search_teacher"  placeholder="Type/Select Teacher" autocomplete="off">
                                        <input type="hidden" name="search_teacher_id" class="search_teacher_id" value="0"/>
                                        <div class="search-teacher-result">
                                        </div>
                                    </div>

                                    <div class="field-input fullname teacher-add">
                                        <input type="text" name="first_name" required="" placeholder="First Name">
                                        <input type="text" name="last_name" required="" placeholder="Last Name">
                                    </div>
                                </div>

                                <div class="field-group teacher-add teacher-email">
                                    <label class="field-label">Teacher Email</label>
                                    <div class="field-input ">
                                        <input type="email" name="email" required="" placeholder="Email Addresss">
                                    </div>
                                </div>

                                <button type="submit" class="btn-add-teacher">ADD</button>

                                <div class="button-container">
                                    <button type="button" class="btn-back">Cancel</button>
                                    <button type="submit" class="btn-save">Save</button>
                                </div>
                            
                            </form>
                        </div>
                        
                    </div>
                    `
                },

                tabCourses: () => {
                    //JourneyManageClassrooms.classrooms.current
                    let currentCourseIds = [];
                    if(JourneyManageClassrooms.classrooms.current != ""){
                        JourneyManageClassrooms.classrooms.current.school_data.courses.map( course => {
                            currentCourseIds.push(course.ID);
                        })
                    }

                    let availableCourses = "<select multiple id='select-available-courses'>";
                    manageClassroomsJs.availableGroupCourses.map( categoryGroup => {
                        console.log("categoryGroup", categoryGroup)

                        if(categoryGroup.courses.length > 0 ){


                            var availableCoursesRow = `<optgroup label="${categoryGroup.category}">`
                            var foundGroupCourse = false;
                            categoryGroup.courses.map( course => {                            
                                
                                var foundCurrentGroupCourse = false;

                                if(JourneyManageClassrooms.classrooms.current != ""){
                                    JourneyManageClassrooms.classrooms.current.school_data.group_courses.map( currentGroupCourse => {
                                        if(currentGroupCourse.ID == course.ID) foundCurrentGroupCourse = true;
                                    });
                                }

                                if(!foundCurrentGroupCourse){
                                    foundGroupCourse = true;
                                    availableCoursesRow += `<option value="${course.ID}">${course.post_title}</option>`
                                }

                            })
                            
                            availableCoursesRow += `</optgroup>`

                            if(!foundGroupCourse){
                                availableCoursesRow = ""
                            }
                            availableCourses += availableCoursesRow;
                        }
                        
                    })
                    availableCourses += "</select>";


                    let activeCourses = "<select multiple id='select-active-courses'>";
                    if(JourneyManageClassrooms.classrooms.current != ""){
                        
                        /*JourneyManageClassrooms.classrooms.current.school_data.courses.map( course => {
                            activeCourses += `<option value="${course.ID}">${course.post_title}</option>`
                        })*/

                        manageClassroomsJs.availableGroupCourses.map( categoryGroup => {
                            console.log("categoryGroup", categoryGroup)
    
                            if(categoryGroup.courses.length > 0 ){

                                var activeCoursesRow = `<optgroup label="${categoryGroup.category}">`
                                var foundGroupCourse = false;
                                categoryGroup.courses.map( course => {   
                                    JourneyManageClassrooms.classrooms.current.school_data.group_courses.map( currentGroupCourse => {
                                        if(currentGroupCourse.ID == course.ID){
                                            foundGroupCourse = true;
                                            activeCoursesRow += `<option value="${course.ID}">${course.post_title}</option>`
                                        }
                                    })
                                    
                                    
                                })
                                activeCoursesRow += `</optgroup>`;

                                if(!foundGroupCourse){
                                    activeCoursesRow = ""
                                }

                                activeCourses += activeCoursesRow;
                            }
                            
                        })

                    }
                    activeCourses += "</select>";


                    // console.log("current classroom courses" , JourneyManageClassrooms.classrooms.current.school_data.courses)
                    //createNew

                    if( jQuery("#manage-classroom-add-new").is(":visible") ){
                        formHeader = `
                            <form onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                            <input type="hidden" name="tab" value="courses" />
                            <input type="hidden" name="classroom_id" class="new-group-id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                        `
                        buttons = `
                            <button type="button" class="btn-back btn-back-step">Back</button>
                            <button type="submit" class="btn-save">Next</button>
                        `;
                    }else{
                        formHeader = `
                        <form onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                        <input type="hidden" name="tab" value="courses" />
                        <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                        `
                        buttons = `
                            <button type="button" class="btn-back">Cancel</button>
                            <button type="submit" class="btn-save">Save</button>
                        `;
                    }

                    return `
                        ${formHeader}
                        <h3>Manage Courses</h3>

                        <div class="manage-courses">
                            <div>
                                <h4>Available Courses</h4>
                                <div class="courses">
                                    ${availableCourses}
                                </div>
                            </div>
                            <div>
                                <button type="button" class="btn-add-courses-to-active">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="20" cy="20" r="20" fill="#37394A"/>
                                        <path d="M28 20.3846L19.3846 13V19.1538H12V21.6154H19.3846V27.7692L28 20.3846Z" fill="white"/>
                                    </svg>
                                </button>
                                <button type="button" class="btn-remove-courses-from-active">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle r="20" transform="matrix(-1 0 0 1 20 20)" fill="#37394A"/>
                                        <path d="M12 20.3846L20.6154 13V19.1538H28V21.6154H20.6154V27.7692L12 20.3846Z" fill="white"/>
                                    </svg>
                                </button>
                            </div>
                            <div>
                                <h4>Active Courses</h4>
                                <div class="courses">
                                    ${activeCourses}
                                </div>
                                <div class="hidden-active-courses"></div>
                            </div>
                        </div>

                        <div class="button-container">
                            ${buttons}
                        </div>
                        
                    </form>
                    `;
                }
            }
        },

        createNewFields: () => {
            
            $("#manage-classroom-add-new #class-new-name").html( JourneyManageClassrooms.classrooms.templates.manageClassroom.tabClassroom() );
            $("#manage-classroom-add-new #class-new-manage-courses").html( JourneyManageClassrooms.classrooms.templates.manageClassroom.tabCourses() )
            
        },

        avatarImportDropzone: () => {             
            
            if(JourneyManageClassrooms.importAvatarDropzone != "") JourneyManageClassrooms.importAvatarDropzone.destroy();
            
            JourneyManageClassrooms.importAvatarDropzone = new Dropzone("div#import-avatar-dropzone", 
                { 
                    url: `${safarObject.apiBaseurl}/manage-classrooms/upload`,
                    maxFilesize: 10485760,
                    acceptedFiles: ".png,.jpg,.jpeg",
                    headers: {
                        'X-WP-Nonce': safarObject.wpnonce,
                    },
                    
                }
            );
            $(".btn-import").fadeTo("fast",.3)
            JourneyManageClassrooms.importAvatarDropzone.on("addedfile", file => {
                if(file.size >= 10485760){
                    console.log("file is greater than 10mb", file.zize)
                    JourneyManageClassrooms.importAvatarDropzone.removeFile(file);
                    return false;
                }else{
                    $(".btn-import").fadeTo("fast",1)
                    return file;
                }
            });
            
    
            JourneyManageClassrooms.importAvatarDropzone.on("complete", file => {
                console.log("complete", $.parseJSON(file.xhr.response) )
                let xhrResponse = $.parseJSON(file.xhr.response);
                let classroomID = $(".create-new-avatar .new-group-id").val();
                $("#import-avatar-dropzone .dz-default").remove();

                JourneyManageClassrooms.api.school.updateClassroomDetails({
                    id: classroomID,
                    formData: {
                        "tab": "avatar",
                        "image": "dropzone",
                        "classroom_id": classroomID,
                        "attachment_id": xhrResponse.attachment_id
                    }
                })
                .then( e => {
        
                })
                .catch( e => {
                    console.log("error", e)
                })
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
                    <div class="size-limit">Size limit: 10mb, support .jpg and .png file</div>
                </div>
            `)
            console.log("render dropzone")
            
        },
        importCoverPhotoDropzone: () => {             
            
            if(JourneyManageClassrooms.importCoverPhotoDropzone != "") JourneyManageClassrooms.importCoverPhotoDropzone.destroy();
            JourneyManageClassrooms.importCoverPhotoDropzone = new Dropzone("div#import-cover-photo-dropzone", 
                { 
                    url: `${safarObject.apiBaseurl}/manage-classrooms/upload`,
                    maxFilesize: 10485760,
                    acceptedFiles: ".png,.jpg,.jpeg",
                    headers: {
                        'X-WP-Nonce': safarObject.wpnonce,
                    },
                }
            );
            $(".btn-import").fadeTo("fast",.3)
            JourneyManageClassrooms.importCoverPhotoDropzone.on("addedfile", file => {
                if(file.size >= 10485760){
                    console.log("file is greater than 10mb", file.zize)
                    JourneyManageClassrooms.importCoverPhotoDropzone.removeFile(file);
                    return false;
                }else{
                    $(".btn-import").fadeTo("fast",1)
                    return file;
                }
            });
    
            JourneyManageClassrooms.importCoverPhotoDropzone.on("complete", file => {
                console.log("complete", $.parseJSON(file.xhr.response) )
                let xhrResponse = $.parseJSON(file.xhr.response);

                let classroomID = $(".create-new-avatar .new-group-id").val();
                $("#import-cover-photo-dropzone .dz-default").remove();

                JourneyManageClassrooms.api.school.updateClassroomDetails({
                    id: classroomID,
                    formData: {
                        "tab": "cover_photo",
                        "image": "dropzone",
                        "classroom_id": classroomID,
                        "attachment_id": xhrResponse.attachment_id
                    }
                })
                .then( e => {
        
                })
                .catch( e => {
                    console.log("error", e)
                })
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
                    <div class="size-limit">Size limit: 10mb, support .jpg and .png file</div>
                </div>
            `)
            console.log("render dropzone")
        },

        createNewTeacherTab: () => {
            let teachersList = `<div class="no-users-added">No teachers added yet</div>`;
            if(JourneyManageClassrooms.classrooms.current != ""){
                teachersList = "";
                JourneyManageClassrooms.classrooms.current.school_data.teachers.map( teacher => {
                    
                    teachersList += `
                        <div class="list-item">
                            <div class="teacher-name">${teacher.data.first_name} ${teacher.data.last_name}</div>
                            <div class="username">${teacher.data.user_email}</div>
                            <div class="action">
                                <input type="hidden" name="teachers[]" value="${teacher.data.ID}" />
                                <button type="button" class="btn-remove-list-item">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 6L6 18" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6 6L18 18" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `
                })
            }
            
            $(".teachers-list-container").html(`
                <div class="list">
                    <div class="header">
                        <div class="">TEACHER NAME</div>
                        <div class="">EMAIL</div>
                        <div class="">ACTION</div>
                    </div>
                    <div class="body">
                        ${
                            teachersList
                        }
                    </div>
                </div>
            `)
        },

        createNewStudentImport: () => {
            tpl = `
                ${JourneyManageClassrooms.classrooms.studentsSeatsAvailable()}

                <div class="download">
                    <span>Import students using our template.</span>
                    <a href="/wp-content/themes/buddyboss-theme-child/page-templates/manage-classrooms/UsersDemoStudents.csv" download>Download Template</a>
                </div>

                <div id="import-students-dropzone" class="dropzone" action="?" ></div>
                <div class="imported-students-container"></div>

                <div class="button-container">
                    <button type="button" class="btn-back btn-back-create-new-class-student">BACK</button>
                    <button type="submit" class="btn-save btn-import btn-student-submit">SUBMIT</button>
                </div>
            `
            $("#add-student-import-list").html(tpl);
            JourneyManageClassrooms.students.renderDropzone();
        },

        createNewStudentExisting: () => {
            
            tpl = `
                ${JourneyManageClassrooms.classrooms.studentsSeatsAvailable()}

                <div class="field-group">
                    <label class="field-label">Search Student</label>
                    <div class="field-input search-student-container">
                        <input type="text" name="search_student" placeholder="Type/Select Student" autocomplete="off"/>
                        <input type="hidden" name="search_student_id" class="search_student_id" />
                        <div class="search-student-result"></div>
                    </div>
                </div>

                <form class="frm-createn-new-student-existing">
                    <input type="hidden" name="tab" value="student"/>
                    <input type="hidden" name="classroom_id" class="new-group-id" />
                    
                    ${JourneyManageClassrooms.classrooms.studentsList()}

                </form>
                <div class="button-container">
                    <button type="button" class="btn-back btn-back-create-new-class-student">BACK</button>
                    <button type="submit" class="btn-save btn-import btn-student-submit">SUBMIT</button>
                </div>
            `
            $("#add-student-search-existing").html(tpl);
        },

        createNewStudentEmail: () => {
            
            tpl = `
                ${JourneyManageClassrooms.classrooms.studentsSeatsAvailable()}

                <form onsubmit="JourneyManageClassrooms.classrooms.studentAdd(this); return false;">
                    <input type="hidden" name="tab" value="student-add"/>
                    <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                    <input type="hidden" name="remaining_seat" value="${JourneyManageClassrooms.classrooms.statistics.remainingSeats}" />
                    <div class="field-group">
                        <label class="field-label">Student Name</label>
                        <div class="field-input fullname">
                            <input type="text" name="first_name" required placeholder="First Name" value=""/>
                            <input type="text" name="last_name" required placeholder="Last Name" value=""/>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Gender</label>
                        <div class="field-input gender">
                            <label>
                                <input type="radio" name="gender" required value="male"/> Male
                            </label>
                            <label>
                                <input type="radio" name="gender" required value="female"/> Female
                            </label>
                        </div>
                    </div>

                    <!--
                    <div class="field-group student-add">
                        <label class="field-label">Date of Birth</label>
                        <div class="field-input">
                            <input name="date_of_birth" id="date_of_birth_1" type="text" placeholder="Date of Birth"/>
                        </div>
                    </div>
                    <div class="field-group student-add">
                        <label class="field-label">Family ID</label>
                        <div class="field-input">
                            <input  name="family_id" type="text" placeholder="Family ID"/>
                        </div>
                    </div>
                    -->

                    <div class="field-group">
                        <label class="field-label">Email Address</label>
                        <div class="field-input">
                            <input type="email" name="email" required placeholder="Email Address" value=""/>
                        </div>
                    </div>

                    <div class="button-container add">
                        <button type="submit" class="btn-add ">ADD</button>
                    </div>
                </form>


                ${JourneyManageClassrooms.classrooms.studentsList()}
                
                <div class="button-container">
                    <button type="button" class="btn-back btn-back-create-new-class-student">BACK</button>
                    <button type="submit" class="btn-save btn-import btn-student-submit">SUBMIT</button>
                </div>
            `
            $("#add-student-by-email").html(tpl);
        },

        createNewStudentUsername: () => {
            
            tpl = `
                ${JourneyManageClassrooms.classrooms.studentsSeatsAvailable()}

                <form onsubmit="JourneyManageClassrooms.classrooms.studentAdd(this); return false;">
                    <input type="hidden" name="tab" value="student-add"/>
                    <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                    <input type="hidden" name="remaining_seat" value="${JourneyManageClassrooms.classrooms.statistics.remainingSeats}" />

                    <div class="field-group">
                        <label class="field-label">Student Name</label>
                        <div class="field-input fullname">
                            <input type="text" name="first_name" required placeholder="First Name" value=""/>
                            <input type="text" name="last_name" required placeholder="Last Name" value=""/>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Gender</label>
                        <div class="field-input gender">
                            <label>
                                <input type="radio" name="gender" required value="male"/> Male
                            </label>
                            <label>
                                <input type="radio" name="gender" required value="female"/> Female
                            </label>
                        </div>
                    </div>

                    <!--
                    <div class="field-group student-add">
                        <label class="field-label">Date of Birth</label>
                        <div class="field-input">
                            <input name="date_of_birth" id="date_of_birth_2" type="text" placeholder="Date of Birth"/>
                        </div>
                    </div>
                    <div class="field-group student-add">
                        <label class="field-label">Family ID</label>
                        <div class="field-input">
                            <input  name="family_id" type="text" placeholder="Family ID"/>
                        </div>
                    </div>
                    -->

                    <div class="field-group">
                        <label class="field-label">Username</label>
                        <div class="field-input">
                            <input type="text" name="username" required placeholder="Username" value=""/>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Password</label>
                        <div class="field-input">
                            <input type="password" name="email" required placeholder="First Name" value=""/>
                        </div>
                    </div>

                    <div class="button-container add">
                        <button type="submit" class="btn-add ">ADD</button>
                    </div>
                </form>
                ${JourneyManageClassrooms.classrooms.studentsList()}

                <div class="button-container">
                    <button type="button" class="btn-back btn-back-create-new-class-student">BACK</button>
                    <button type="submit" class="btn-save btn-student-submit">SUBMIT</button>
                </div>
            `
            $("#add-student-by-username").html(tpl);
        },

        studentsList: () => {
            studentItems = ``;

            if(JourneyManageClassrooms.classrooms.current != ""){
                JourneyManageClassrooms.classrooms.current.school_data.students.map( student => {
                    studentItems += `
                        <div class="list-item">
                            <div class="student-name">${student.data.first_name} ${student.data.last_name}</div>
                            <div class="username">${student.data.user_login}</div>
                            <div class="action">
                                <input type="hidden" name="students[]" value="${student.data.ID}" />
                                <button type="button" class="btn-remove-list-item" studentslist data-studentid="${student.data.ID}" data-classroomid="${JourneyManageClassrooms.classrooms.current.school_data.school_id}">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 6L6 18" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6 6L18 18" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `
                })
            }


            if(studentItems == ""){
                studentItems = `<div class="no-users-added">No students added yet</div>`;
            }

            //console.log("studentItems", studentItems)

            return `
            <div class="list"> 
                <div class="header">
                    <div class="student-name">Student Name</div>
                    <div class="email">Username</div>
                    <div class="action">Action</div>
                </div>
                <div class="body items" test123>
                    ${studentItems}
                </div>
            </div>
            `
        },

        studentsSeatsAvailable: () => {
            return `
            <div class="top seats-available">
                <h2>Students List</h2>
                <span>Available Seats:  <span class="num">${JourneyManageClassrooms.classrooms.statistics.remainingSeats}</span><span>
            </div>
            `
        },

        studentAdd: (form) => {
            $("#add-student-by-email .btn-add, #add-student-by-username .btn-add").fadeTo("fast",.3)
            JourneyManageClassrooms.api.school.updateClassroomDetails({
                id: JourneyManageClassrooms.classrooms.current.ID,
                formData: $(form).serialize()
            })
            .then( e => {

                JourneyManageClassrooms.schoolDetails = "";
                schoolbypasstransient = true;

                JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                    console.log("JourneyManageClassrooms.classrooms.statistics.remainingSeats", JourneyManageClassrooms.classrooms.statistics.remainingSeats)
                    $(".seats-available .num").html(JourneyManageClassrooms.classrooms.statistics.remainingSeats)
                    JourneyManageClassrooms.classrooms.getById(JourneyManageClassrooms.classrooms.current.ID);
                    JourneyManageClassrooms.classrooms.createNewStudentEmail();
                    JourneyManageClassrooms.classrooms.createNewStudentUsername();
                    $("#add-student-by-email .btn-add, #add-student-by-username .btn-add").fadeTo("fast",1)
                });
            })
            .catch( e => {
                
            })
        },

        settings: {
            radioButtonsListen: () => {
                $(".tab-classroom-settings .selections .select-item").each( function() {
                    let selectItemContainer = this;
                    let checkedRadio = $(selectItemContainer).find("input[type=radio]:checked");

                    if(checkedRadio.is(":checked")){
                        ///console.log("test radioButtonsListen", checkedRadio)
                        console.log( checkedRadio.val() );

                        $(selectItemContainer).parent().parent().find(".description-selected-item").html(`
                            <div class="text">${$(checkedRadio).attr("data-description")}</div>
                        `);
                    }
                })
            }
        }
    },

    teachers: {
        searchKey: "",
        dataTable: "",
        getTeacherById : teacherid => {
            if(JourneyManageClassrooms.schoolDetails.teachers.length > 0){
                teacherDetails = {};
                JourneyManageClassrooms.schoolDetails.teachers.map( teacher => {
                    
                    if(teacher.data.ID == teacherid){
                        teacherDetails = teacher;
                    }
                })
                return teacherDetails;
            }else{
                return false;
            }
        },
        listTpl: teacher => {

            let classRooms = ``;
            teacher.data.classrooms.map( e => {
                let bgColor = '';
                e.category.map( cat => {
                    //console.log("cat", cat)
                    bgColor = cat.bg_color;
                })

                if(bgColor !='') classRooms += `<span style="background-color:${bgColor}" data-id="${e.ID}">${e.post_title}</span>`
            })

            if(teacher.data.ID == 11917){
                console.log("teacher.data.classrooms",teacher.data)
            }
            
            /* <a href="reporting/?tab=login-report&uid=${teacher.data.ID}">${teacher.data.last_login}</a>*/
            return `
                <tr class="list-item">
                    <td class="name">
                        <img src="${teacher.data.avatar_url}" />
                        ${teacher.data.first_name} ${teacher.data.last_name}
                    </td>
                    <td class="email">${teacher.data.user_email}</td>
                    <td class="classrooms">${classRooms}</td>
                    <td class="last-login" data-numeric="${teacher.data.last_login_numeric}">
                        <a href="reporting/?tab=login-report&uid=${teacher.data.ID}"><span class="hidden" style="position:absolute;width:0px;height:0px;opacity:0;">${teacher.data.last_login_numeric}</span>${teacher.data.last_login}</a> 
                    </td>
                    <td class="action">
                        <button type="button" class="list-item-action-button">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12C18 12.5523 18.4477 13 19 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12C4 12.5523 4.44772 13 5 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        
                        <div class="submenu">
                            <div class="submenu-overlay"></div>
                            <ul>
                                <li>
                                    <button type="button" class="button-teacher-action" data-teacherid="${teacher.data.ID}" data-action="edit_teacher" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.0833 3.66671H3.66659C3.18036 3.66671 2.71404 3.85987 2.37022 4.20368C2.02641 4.5475 1.83325 5.01381 1.83325 5.50005V18.3334C1.83325 18.8196 2.02641 19.2859 2.37022 19.6297C2.71404 19.9736 3.18036 20.1667 3.66659 20.1667H16.4999C16.9861 20.1667 17.4525 19.9736 17.7963 19.6297C18.1401 19.2859 18.3333 18.8196 18.3333 18.3334V11.9167M16.9583 2.29171C17.3229 1.92704 17.8175 1.72217 18.3333 1.72217C18.849 1.72217 19.3436 1.92704 19.7083 2.29171C20.0729 2.65638 20.2778 3.15099 20.2778 3.66671C20.2778 4.18244 20.0729 4.67704 19.7083 5.04171L10.9999 13.75L7.33325 14.6667L8.24992 11L16.9583 2.29171Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="button-teacher-action"  data-broadcasttype="single_user" data-userid="${teacher.data.ID}"  data-action="broadcast_email">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.1666 5.49984C20.1666 4.4915 19.3416 3.6665 18.3333 3.6665H3.66659C2.65825 3.6665 1.83325 4.4915 1.83325 5.49984M20.1666 5.49984V16.4998C20.1666 17.5082 19.3416 18.3332 18.3333 18.3332H3.66659C2.65825 18.3332 1.83325 17.5082 1.83325 16.4998V5.49984M20.1666 5.49984L10.9999 11.9165L1.83325 5.49984" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    
                                        <span>Send Email</span>
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="button-teacher-action" data-teacherid="${teacher.data.ID}" data-action="add_to_classroom" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.9999 7.3335V14.6668M7.33325 11.0002H14.6666M20.1666 11.0002C20.1666 16.0628 16.0625 20.1668 10.9999 20.1668C5.93731 20.1668 1.83325 16.0628 1.83325 11.0002C1.83325 5.93755 5.93731 1.8335 10.9999 1.8335C16.0625 1.8335 20.1666 5.93755 20.1666 11.0002Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    
                                        <span>Add to Classroom</span>
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="button-teacher-action" data-teacherid="${teacher.data.ID}" data-action="remove_teacher" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.75 5.50016H4.58333M4.58333 5.50016H19.25M4.58333 5.50016V18.3335C4.58333 18.8197 4.77649 19.286 5.1203 19.6299C5.46412 19.9737 5.93044 20.1668 6.41667 20.1668H15.5833C16.0696 20.1668 16.5359 19.9737 16.8797 19.6299C17.2235 19.286 17.4167 18.8197 17.4167 18.3335V5.50016M7.33333 5.50016V3.66683C7.33333 3.1806 7.52649 2.71428 7.8703 2.37047C8.21412 2.02665 8.68044 1.8335 9.16667 1.8335H12.8333C13.3196 1.8335 13.7859 2.02665 14.1297 2.37047C14.4735 2.71428 14.6667 3.1806 14.6667 3.66683V5.50016M9.16667 10.0835V15.5835M12.8333 10.0835V15.5835" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    
                                        <span>Remove </span>
                                    </button>
                                </li>

                            </ul>
                            
                        </div>
                    </td>
                </tr>
            `;
        },
        list: () => {
            
            skeletonLoader = ``;
            for(i = 0; i < 3; i++){
                skeletonLoader += `<tr class="list-item">
                    <td class="name">
                        <div class="skeleton-loader" style="width:36px; height:36px; border-radius:50px; margin-right:20px"></div>
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="classrooms">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="action">
                        <div class="skeleton-loader" style="width:24px; height:5px;"></div>
                    </td>
                </tr>`;
            }
            $(".teachers-list .body").html(`${skeletonLoader}`)

            JourneyManageClassrooms.api.school.getuserSchoolDetails({})
                .then( e => {
                    console.log("teachers list", e.teachers, e.teachers.length )
                    let teachersList = ``

                    if( e.teachers.length > 0 ){
                        
                        e.teachers.map( teacher => {

                            show = true;

                            if(JourneyManageClassrooms.teachers.searchKey != ""){
                                haystack = teacher.data.first_name+teacher.data.last_name+teacher.data.user_email+teacher.data.user_login

                                let classRoomsText = ``;
                                teacher.data.classrooms.map( e => {
                                    let bgColor = '';
                                    e.category.map( cat => {
                                        bgColor = cat.bg_color;
                                    })
                                    if(bgColor !='') classRoomsText += e.post_title;
                                })
                                haystack += classRoomsText;
                                haystack = haystack.toLowerCase();

                                show = false;
                                if (haystack.includes(JourneyManageClassrooms.teachers.searchKey)) {
                                    show = true;
                                }
                            }

                            if(show) teachersList += JourneyManageClassrooms.teachers.listTpl(teacher); 
                        })
                    }

                    if(teachersList == "") teachersList = `<tr class="no-records-found"><td>No Teachers Found</td></tr>`;

                    if ($.fn.DataTable.isDataTable( $(".datatable-teachers-list") )) {
                        $(".datatable-teachers-list").DataTable().destroy(); // destroy existing DataTable instance
                    }

                    $(".teachers-list .body").html(teachersList);
                    
                    JourneyManageClassrooms.teachers.dataTable = $(".datatable-teachers-list").DataTable({
                        lengthChange: false, // disable length paging dropdown
                        searching: true,
                        "oSearch": { "bSmart": false, "bRegex": true },
                        columnDefs: [
                            { targets: [0,3], orderable: true }, // enable sorting for columns 1 and 4
                            { targets: '_all', orderable: false }, // disable sorting for all other columns
                            { 
                                targets: 3,
                                orderData: [3],
                                type: 'num',
                                render: function(data, type, row) {
                                    if (type === 'sort') {
                                        return $(row[3]).find(".hidden").text();
                                    }
                                    return data;
                                }
                            }
                        ],
                        dom: '<"top"f>rt<"bottom"ip>',
                        language: {
                          searchPlaceholder: "Enter search term here..."
                        },
                        lengthMenu: [ -1 ] // show all rows
                    });

                    
                })
                .catch( e => {
                    console.log("error on JourneyManageClassrooms.api.teachers.list", e)
                })
        },

        renderDropzone: () => {
             
            
            if(JourneyManageClassrooms.importTeacherDropzone != "") JourneyManageClassrooms.importTeacherDropzone.destroy();
            JourneyManageClassrooms.importTeacherDropzone = new Dropzone("div#import-teachers-dropzone", 
                { 
                    url: `${safarObject.apiBaseurl}/school/admin/teachers/import_csv`,
                    maxFilesize: 10485760,
                    acceptedFiles: ".csv",
                    headers: {
                        'X-WP-Nonce': safarObject.wpnonce,
                    },
                    autoProcessQueue: false, // Disable auto-upload
                }
            );
            $(".btn-import").fadeTo("fast",.3).attr("disabled","disabled")
            JourneyManageClassrooms.importTeacherDropzone.on("addedfile", file => {
                if(file.size >= 10485760){
                    console.log("file is greater than 10mb", file.zize)
                    JourneyManageClassrooms.importTeacherDropzone.removeFile(file);
                    return false;
                }else{
                    console.log("import teachers dropzone")
                    $(".btn-import").fadeTo("fast",1).removeAttr("disabled","disabled")
                    $(document).on("click", ".btn-import", () => {
                        // Manually trigger the file upload when the button is clicked
                        JourneyManageClassrooms.importTeacherDropzone.processQueue();
                    });
                    return file;
                }
            });
            
    
    
            JourneyManageClassrooms.importTeacherDropzone.on("complete", file => {
                console.log("complete", $.parseJSON(file.xhr.response) )
    
                $(".import-teachers-container .upload-form, #import_teachers .upload-form").hide();
                $(".import-teachers-container .imported-teachers, #import_teachers .imported-teachers").show();
    
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
                let errorMessageTpl = ""
                if(xhrResponse.error_message.length > 0 ){
                    let errorMessages = ``;
                    xhrResponse.error_message.map( msg => {
                        errorMessages = `<div>${msg}</div>`;
                    })
                    errorMessageTpl = `
                    <div class="error-imports" importteachers="" error="">
                        <span class="icon">
                            <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 13V21M21 29H21.02M41 21C41 32.0457 32.0457 41 21 41C9.9543 41 1 32.0457 1 21C1 9.9543 9.9543 1 21 1C32.0457 1 41 9.9543 41 21Z" stroke="#EF746F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        
                        </span>
                        <span class="text">
                        ${errorMessages}
                        </span>
                    </div>
                    `
                }
    
                let importedTeachersTpl = `
                ${errorMessageTpl}

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

                <div class="button-container"><button type="button" class="btn-continue btn-close-import">Close</button></div>
                `;            
    
    
                $(".import-teachers-container .imported-teachers, #import_teachers .imported-teachers").html(importedTeachersTpl);
                JourneyManageClassrooms.schoolDetails = ""; // clear school details to reload teachers
                JourneyManageClassrooms.teachers.list();
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
    },

    admins: {
        searchKey: "",
        dataTable: "",
        getAdminById : adminid => {
            if(JourneyManageClassrooms.schoolDetails.admins.length > 0){
                adminDetails = {};
                JourneyManageClassrooms.schoolDetails.admins.map( admin => {
                    
                    if(admin.data.ID == adminid){
                        adminDetails = admin;
                    }
                })
                return adminDetails;
            }else{
                return false;
            }
        },
        selectedTeachers: [],
        displaySelectedTeachers: () => {
            let teacherAdminTpl = ``
            console.log("JourneyManageClassrooms.schoolDetails.teachers length", JourneyManageClassrooms.schoolDetails.teachers.length )
            JourneyManageClassrooms.schoolDetails.teachers.map( teacher => {
                //console.log("typeof teacher", typeof teacher)
                if( JourneyManageClassrooms.admins.selectedTeachers.includes(teacher.data.ID) ){
                    //console.log(teacher.data);
                    teacherAdminTpl += `<div class="selected-for-admin" data-id="${teacher.data.ID}">
                        <img src="${teacher.data.avatar_url}"/> <span>${teacher.data.first_name} ${teacher.data.last_name}</span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L4 12" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 4L12 12" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>`
                }
            });

            $(".selected-teachers-for-admin").html(teacherAdminTpl)
        },
        saveAdmin: e => {

            $(".btn-add-admin").fadeTo("fast",.3)
            JourneyManageClassrooms.api.admin.updateInstituteAdmins({id:JourneyManageClassrooms.schoolDetails.parent_school.school_id, adminids: JourneyManageClassrooms.admins.selectedTeachers})
                .then( e => {
                    
                    JourneyManageClassrooms.schoolDetails = "";
                    schoolbypasstransient = true;
                    JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                        JourneyManageClassrooms.admins.list();
                    });

                    $(".btn-add-admin").fadeTo("fast",1).html("ADMIN ADDED")

                    JourneyManageClassrooms.admins.selectedTeachers = [];
                    setTimeout( () => {
                        //JourneyManageClassrooms.admins.displaySelectedTeachers();
                        JourneyManageClassrooms.schoolDetails != "";
                        JourneyManageClassrooms.classrooms.reloadReloadables();
                    }, 100);

                    setTimeout( () => {
                        $(".btn-add-admin").fadeTo("fast",1).html("ADD AS ADMIN")
                    }, 3000 )
                        
                })
                .catch( e => {
                    console.log("error update institute admins", e )
                })
        },
        listTpl: admin => {
            return `
                <tr class="list-item">
                    <td class="name">
                        <img src="${admin.data.avatar_url}" />
                        ${admin.data.first_name} ${admin.data.last_name}
                    </td>
                    <td class="email">${admin.data.user_email}</td>
                    <td class="action">
                        <button type="button" class="button-admin-action" data-adminid="${admin.data.ID}" data-action="remove_admin">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H5M5 6H21M5 6V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M10 11V17M14 11V17" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        },
        list: () => {
            
            skeletonLoader = ``;
            for(i = 0; i < 3; i++){
                skeletonLoader += `<tr class="list-item">
                    <td class="name">
                        <div class="skeleton-loader" style="width:36px; height:36px; border-radius:50px; margin-right:20px"></div>
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="action">
                        <div class="skeleton-loader" style="width:24px; height:5px;"></div>
                    </td>
                </tr>`;
            }
            $(".admins-list .body").html(`${skeletonLoader}`)

            JourneyManageClassrooms.api.school.getuserSchoolDetails({})
                .then( e => {
                    //console.log("teachers list", e.teachers, e.teachers.length )
                    let adminsList = ``

                    if( e.admins.length > 0 ){
                        
                        e.admins.map( admin => {

                            show = true;

                            if(JourneyManageClassrooms.admins.searchKey != ""){
                                haystack = admin.data.first_name+admin.data.last_name+admin.data.user_email+admin.data.user_login
                                haystack = haystack.toLowerCase();

                                show = false;
                                if (haystack.includes(JourneyManageClassrooms.admins.searchKey)) {
                                    show = true;
                                }
                            }

                            if(show) adminsList += JourneyManageClassrooms.admins.listTpl(admin); 
                        })
                    }

                    if(adminsList == "") adminsList = `<tr class="no-records-found"><td>No Admins Found</td></tr>`;

                    if ($.fn.DataTable.isDataTable( $(".datatable-admins-list") )) {
                        $(".datatable-admins-list").DataTable().destroy(); // destroy existing DataTable instance
                    }

                    $(".admins-list .body").html(adminsList);

                    JourneyManageClassrooms.admins.dataTable = $(".datatable-admins-list").DataTable({
                        lengthChange: false, // disable length paging dropdown
                        searching: true,
                        "oSearch": { "bSmart": false, "bRegex": true },
                        columnDefs: [
                            { targets: [0], orderable: true }, // enable sorting for columns 1 and 4
                            { targets: '_all', orderable: false } // disable sorting for all other columns
                        ],
                        dom: '<"top"f>rt<"bottom"ip>',
                        language: {
                          searchPlaceholder: "Enter search term here..."
                        },
                        lengthMenu: [ -1 ] // show all rows
                    });

                })
                .catch( e => {
                    console.log("error on JourneyManageClassrooms.api.admins.list", e)
                })
        },
    },

    students: {
        searchKey: "",
        dataTable:"",
        studentSelectedToFamily: {},
        templates: {
            addForm: classroomid => {
                let topSection = ``
                if( $(".manage-classroom-modal").hasClass("add_student")){
                    topSection = `<div class="field-group">
                            <div class="available-seats">
                                <span class="text">Available Seats:</span>
                                <span class="num">${JourneyManageClassrooms.classrooms.statistics.remainingSeats}</span>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">${(manageClassroomsJs.post_slug == "manage-family") ? `Child Name`:`Student Name`}</label>
                            <div class="field-input fullname">
                                <input type="text" name="first_name" required placeholder="First Name"/>
                                <input type="text" name="last_name" required placeholder="Last Name"/>
                            </div>
                        </div>    
                    `
                }else{
                    topSection = `<div class="download">
                        <div class="available-seats import-students">
                            <span class="text">Available Seats:</span>
                            <span class="num">${JourneyManageClassrooms.classrooms.statistics.remainingSeats}</span>
                        </div>
                        <button type="button" class="import-students">IMPORT LIST</a>
                    </div>
                    
                    <div class="field-group">
                        <div class="field-heading">
                            <label class="field-label">Student Name</label>
                            <label class="field-label-checkbox student">
                                <span class="checkbox">
                                    <input type="checkbox"/> <span></span>
                                </span>
                                Student already exists?
                            </label>
                        </div>
                        <div class="field-input fullname student-add">
                            <input type="text" name="first_name" required placeholder="First Name"/>
                            <input type="text" name="last_name" required placeholder="Last Name"/>
                        </div>

                        <div class="field-input search-student-container">
                            <input type="text" name="search_student" placeholder="Type/Select Student" autocomplete="off">
                            <input type="hidden" name="student_id" class="search_student_id">
                            <div class="search-student-result"></div>
                        </div>
                    </div>

                    `
                }

                return `
                
                    ${topSection}
                    
                    <div class="field-group student-add">
                        <label class="field-label">Gender</label>
                        <div class="field-input gender">
                            <label>
                                <input type="radio" name="gender" value="male"/> Male
                            </label>
                            <label>
                                <input type="radio" name="gender" value="female"/> Female
                            </label>
                        </div>
                    </div>

                    <div class="field-group student-add">
                        <label class="field-label">${(manageClassroomsJs.post_slug == "manage-family") ? `Child Email (Optional)`:`Student Email (Optional)`}</label>
                        <div class="field-input">
                            <input type="email" name="email" type="email" placeholder="Email Address"/>
                        </div>
                    </div>

                    <!--
                    <div class="field-group student-add">
                        <label class="field-label">Date of Birth</label>
                        <div class="field-input">
                            <input name="date_of_birth" id="date_of_birth" type="text" placeholder="Date of Birth"/>
                        </div>
                    </div>
                    <div class="field-group student-add">
                        <label class="field-label">Family ID</label>
                        <div class="field-input">
                            <input  name="family_id" type="text" placeholder="Family ID"/>
                        </div>
                    </div>
                    -->
                    
                    <div class="field-group student-add">
                        <label class="field-label">Username</label>
                        <div class="field-input">
                            <input type="text" name="username" type="text" required placeholder="Username"/>
                        </div>
                    </div>

                    <div class="field-group student-add">
                        <label class="field-label">Password</label>
                        <div class="field-input password">
                            <input type="password" name="password" type="password" required placeholder="Password" class="input-password"/>
                            <button type="button" class="btn-view-password"></button>
                        </div>
                    </div>
                    
                    
                
                `
            }
        },

        renderDropzone: () => {
             
            
            if(JourneyManageClassrooms.importStudentsDropzone != "") JourneyManageClassrooms.importStudentsDropzone.destroy();
            JourneyManageClassrooms.importStudentsDropzone = new Dropzone("div#import-students-dropzone", 
                { 
                    url: `${safarObject.apiBaseurl}/manage-classrooms/upload_csv`,
                    maxFilesize: 10485760,
                    acceptedFiles: ".csv",
                    headers: {
                        'X-WP-Nonce': safarObject.wpnonce,
                    },
                }
            );
            $(".btn-import").fadeTo("fast",.3)
            JourneyManageClassrooms.importStudentsDropzone.on("addedfile", file => {
                if(file.size >= 10485760){
                    console.log("file is greater than 10mb", file.zize)
                    JourneyManageClassrooms.importStudentsDropzone.removeFile(file);
                    return false;
                }else{
                    $(".btn-import").fadeTo("fast",1)
                    return file;
                }
            });
            
    
    
            JourneyManageClassrooms.importStudentsDropzone.on("complete", file => {
                console.log("complete", $.parseJSON(file.xhr.response) )
        
                let xhrResponse = $.parseJSON(file.xhr.response);
                if( jQuery("#manage-classroom-add-new").is(":visible") ){
                    var classroomID = $(".create-new-avatar .new-group-id").val();
                }else{
                    var classroomID = JourneyManageClassrooms.schoolDetails.catch_all_school_data.school_id;
                }
                $("#import-students-dropzone .dz-default").remove();
                
                
                JourneyManageClassrooms.api.school.updateClassroomDetails({
                    id: classroomID,
                    formData: {
                        "tab": "student-import",
                        "classroom_id": classroomID,
                        "students": xhrResponse
                    }
                })
                .then( e => {
                    //imported-students-container
                    let studentImportResult = e;
                    $("#import-students-dropzone").hide();
                    
                    JourneyManageClassrooms.schoolDetails = "";
                    schoolbypasstransient = true;

                    JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                        JourneyManageClassrooms.classrooms.getById(classroomID);

                        $(".seats-available .num").html(JourneyManageClassrooms.classrooms.statistics.remainingSeats)
                        let successfullImports = ``;

                        if( studentImportResult.successfull_imports.length > 0 ){
                            successfullImports += ` <div class="successfull-imports" createnewclassroom >
                                    <span class="icon">
                                        <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 11.08V12C20.9988 14.1564 20.3005 16.2547 19.0093 17.9818C17.7182 19.709 15.9033 20.9725 13.8354 21.5839C11.7674 22.1953 9.55726 22.1219 7.53447 21.3746C5.51168 20.6273 3.78465 19.2461 2.61096 17.4371C1.43727 15.628 0.879791 13.4881 1.02168 11.3363C1.16356 9.18455 1.99721 7.13631 3.39828 5.49706C4.79935 3.85781 6.69279 2.71537 8.79619 2.24013C10.8996 1.7649 13.1003 1.98232 15.07 2.85999M21 3.99999L11 14.01L8.00001 11.01" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span class="text">You have successfully imported ${studentImportResult.successfull_imports.length} out of ${xhrResponse.length} students</span>
                                </div>
                            `
                        }

                        if( studentImportResult.error_imports.length > 0){
                            let errorStudentsImport = ``;
                            studentImportResult.error_imports.map( errorStudent => {
                                errorStudentsImport += `<div>- ${errorStudent.first_name} ${errorStudent.last_name}, ${errorStudent.error_message}</div>`
                            })
                            successfullImports += ` <div class="error-imports" createnewclassroom >
                                    <span class="icon">
                                        <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 13V21M21 29H21.02M41 21C41 32.0457 32.0457 41 21 41C9.9543 41 1 32.0457 1 21C1 9.9543 9.9543 1 21 1C32.0457 1 41 9.9543 41 21Z" stroke="#EF746F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    
                                    </span>
                                    <span class="text">
                                        Unable to add ${studentImportResult.error_imports.length} students ${studentImportResult.error_message}:
                                        ${errorStudentsImport}
                                    </span>
                                </div>
                            `
                        }

                        let importMore = `
                            <button class="button-import-more">Import More Students</button>
                        `

                        /** Show Imported Students */

                        
                        studentItems = ``;

                        studentImportResult.successfull_imports.map( student => {
                            studentItems += `
                                <div class="list-item">
                                    <div class="student-name">${student.first_name} ${student.last_name}</div>
                                    <div class="username">${student.username}</div>
                                    
                                </div>
                            `
                        })
                            


                        if(studentItems == ""){
                            studentItems = `<div class="no-users-added">No students added yet</div>`;
                        }

                        console.log("studentItems", studentItems)

                        studentsTpl = `
                        <div class="list" allstudentslist > 
                            <div class="header">
                                <div class="student-name">Student Name</div>
                                <div class="email">Username</div>
                            </div>
                            <div class="body items" test123>
                                ${studentItems}
                            </div>
                        </div>
                        `

                        $("#import_students .btn-import").remove();
                        $(".imported-students-container").html(  successfullImports + studentsTpl  );
                        JourneyManageClassrooms.students.list();
                        
                    });
                    
                })
                .catch( e => {
                    console.log("error", e)
                })
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
        
        getStudentById : studentid => {
            if(JourneyManageClassrooms.schoolDetails.students.length > 0){
                studentDetails = {};
                JourneyManageClassrooms.schoolDetails.students.map( student => {
                    
                    if(student.data.ID == studentid){
                        studentDetails = student;
                    }
                })
                return studentDetails;
            }else{
                return false;
            }
        },
        listTpl: student => {
            let classRooms = ``;
            if (student.data && student.data.hasOwnProperty('classrooms')) {
                student.data.classrooms.map( e => {
                    let bgColor = '';
                    e.category.map( cat => {
                        bgColor = cat.bg_color;
                    })
                    if(bgColor !='') classRooms += `<span style="background-color:${bgColor}" data-id="${e.ID}">${e.post_title}</span>`
                })
            }

            let studentButtons = `
                <li >
                    <button type="button" class="button-student-action" data-broadcasttype="single_user" data-userid="${student.data.ID}" data-action="broadcast_email">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.1666 5.49984C20.1666 4.4915 19.3416 3.6665 18.3333 3.6665H3.66659C2.65825 3.6665 1.83325 4.4915 1.83325 5.49984M20.1666 5.49984V16.4998C20.1666 17.5082 19.3416 18.3332 18.3333 18.3332H3.66659C2.65825 18.3332 1.83325 17.5082 1.83325 16.4998V5.49984M20.1666 5.49984L10.9999 11.9165L1.83325 5.49984" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    
                        <span>Send Email</span>
                    </button>
                </li>

                <li>
                    <button type="button" class="button-student-action" data-studentid="${student.data.ID}" data-action="add_to_classroom_student" >
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.9999 7.3335V14.6668M7.33325 11.0002H14.6666M20.1666 11.0002C20.1666 16.0628 16.0625 20.1668 10.9999 20.1668C5.93731 20.1668 1.83325 16.0628 1.83325 11.0002C1.83325 5.93755 5.93731 1.8335 10.9999 1.8335C16.0625 1.8335 20.1666 5.93755 20.1666 11.0002Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    
                        <span>Add to Classroom</span>
                    </button>
                </li>

                <li>
                    <button type="button" class="button-student-action" data-studentid="${student.data.ID}" data-action="add_to_family" >
                        <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.2482 9.33586C16.5284 9.29576 16.8151 9.27409 17.1074 9.27409C20.363 9.27409 23 11.848 23 15.032V20.4724C23 20.5786 22.9548 20.6751 22.8842 20.7444C22.8136 20.8138 22.7166 20.8571 22.6085 20.8571H17.9786M6.06768 20.8571H1.39042C1.17536 20.8561 1 20.6848 1 20.4724V15.032C1 13.44 1.65841 12.0008 2.72488 10.9593C3.79136 9.91783 5.26479 9.27409 6.89262 9.27409C7.18378 9.27409 7.46942 9.29576 7.74845 9.33586M12.284 20.8561H19.4653C19.6826 20.8561 19.8579 20.6816 19.8568 20.4681V20.0834C19.8568 17.5246 17.7492 15.4503 15.1443 15.4503C14.2201 15.4503 13.3609 15.7159 12.633 16.1667M17.1063 2C18.8367 2 20.2494 3.38502 20.2494 5.08759C20.2494 6.79015 18.8378 8.17734 17.1063 8.17734C15.3748 8.17734 13.9631 6.78906 13.9631 5.08759C13.9631 3.38611 15.3759 2 17.1063 2ZM6.89262 2C8.62302 2 10.0358 3.38502 10.0358 5.08759C10.0358 6.79015 8.62412 8.17734 6.89262 8.17734C5.16112 8.17734 3.74945 6.78906 3.74945 5.08759C3.74945 3.38611 5.16222 2 6.89262 2ZM8.68919 9.15271C7.32384 9.15271 6.20774 10.2505 6.20774 11.5922C6.20774 12.9339 7.32384 14.0296 8.68919 14.0296C10.0545 14.0296 11.1706 12.935 11.1706 11.5922C11.1706 10.2495 10.0545 9.15271 8.68919 9.15271ZM8.85573 15.4514C6.25075 15.4514 4.14317 17.5246 4.14317 20.0844V20.4692C4.14317 20.5721 4.18398 20.6697 4.25677 20.7423C4.32956 20.8149 4.42992 20.8561 4.53469 20.8561H13.1779C13.2826 20.8561 13.383 20.816 13.4569 20.7433C13.5308 20.6707 13.5727 20.5721 13.5716 20.4692V20.0844C13.5716 17.5257 11.4607 15.4514 8.85573 15.4514ZM15.3097 9.15271C13.9433 9.15271 12.8272 10.2505 12.8272 11.5922C12.8272 12.9339 13.9444 14.0296 15.3097 14.0296C16.6751 14.0296 17.7901 12.935 17.7901 11.5922C17.7901 10.2495 16.6762 9.15271 15.3097 9.15271Z" stroke="#6B6F72" stroke-width="1.4" stroke-miterlimit="10"/>
                        </svg>
                        <span>Add to Family</span>
                    </button>
                </li>

                <li>
                    <button type="button" class="button-student-action" data-studentid="${student.data.ID}" data-action="remove_student" >
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.75 5.50016H4.58333M4.58333 5.50016H19.25M4.58333 5.50016V18.3335C4.58333 18.8197 4.77649 19.286 5.1203 19.6299C5.46412 19.9737 5.93044 20.1668 6.41667 20.1668H15.5833C16.0696 20.1668 16.5359 19.9737 16.8797 19.6299C17.2235 19.286 17.4167 18.8197 17.4167 18.3335V5.50016M7.33333 5.50016V3.66683C7.33333 3.1806 7.52649 2.71428 7.8703 2.37047C8.21412 2.02665 8.68044 1.8335 9.16667 1.8335H12.8333C13.3196 1.8335 13.7859 2.02665 14.1297 2.37047C14.4735 2.71428 14.6667 3.1806 14.6667 3.66683V5.50016M9.16667 10.0835V15.5835M12.8333 10.0835V15.5835" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    
                        <span>Remove </span>
                    </button>
                </li>
            `
            if( manageClassroomsJs.post_slug=="manage-family" ){
                studentButtons = `
                <li>
                    <button type="button" class="button-student-action" data-studentid="${student.data.ID}" data-action="remove_student" >
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.75 5.50016H4.58333M4.58333 5.50016H19.25M4.58333 5.50016V18.3335C4.58333 18.8197 4.77649 19.286 5.1203 19.6299C5.46412 19.9737 5.93044 20.1668 6.41667 20.1668H15.5833C16.0696 20.1668 16.5359 19.9737 16.8797 19.6299C17.2235 19.286 17.4167 18.8197 17.4167 18.3335V5.50016M7.33333 5.50016V3.66683C7.33333 3.1806 7.52649 2.71428 7.8703 2.37047C8.21412 2.02665 8.68044 1.8335 9.16667 1.8335H12.8333C13.3196 1.8335 13.7859 2.02665 14.1297 2.37047C14.4735 2.71428 14.6667 3.1806 14.6667 3.66683V5.50016M9.16667 10.0835V15.5835M12.8333 10.0835V15.5835" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    
                        <span>Remove </span>
                    </button>
                </li>
                `;
                classRooms = `
                    <a href='?action=login_as_child&key=${student.data.login_key}'><span style="background:#5d53c0">Login as ${student.data.first_name}</span></a>
                `
            }

            //console.log("student institute family", student)
            let familyName = [];
            let familyId = [];

            student.data.institute_families.map( family => {
                familyName.push(family.group_post.post_title);
                familyId.push(family.family_id);
            })

            return `
                <tr class="list-item">
                    <td class="name" style="width:auto">
                        <img src="${student.data.avatar_url}" />
                        ${student.data.first_name} ${student.data.last_name}
                    </td>
                    <td class="email">${student.data.user_login}</td>
                    <td class="classrooms" style="vertical-align:middle">
                        ${classRooms}
                    </td>
                    <td>${familyName.join(', ')}</td>
                    <td>${familyId.join(', ')}</td>
                    <td class="last-login"><a href="reporting/?tab=login-report&uid=${student.data.ID}"><span class="hidden" style="position:absolute;width:0px;height:0px;opacity:0;">${student.data.last_login_numeric}</span>${student.data.last_login}</a></td>
                    <td class="action">
                        <button type="button" class="list-item-action-button">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12C18 12.5523 18.4477 13 19 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12C4 12.5523 4.44772 13 5 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        
                        <div class="submenu">
                            <div class="submenu-overlay"></div>
                            <ul>
                                <li>
                                    <button type="button" class="button-student-action" data-studentid="${student.data.ID}" data-action="edit_student" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.0833 3.66671H3.66659C3.18036 3.66671 2.71404 3.85987 2.37022 4.20368C2.02641 4.5475 1.83325 5.01381 1.83325 5.50005V18.3334C1.83325 18.8196 2.02641 19.2859 2.37022 19.6297C2.71404 19.9736 3.18036 20.1667 3.66659 20.1667H16.4999C16.9861 20.1667 17.4525 19.9736 17.7963 19.6297C18.1401 19.2859 18.3333 18.8196 18.3333 18.3334V11.9167M16.9583 2.29171C17.3229 1.92704 17.8175 1.72217 18.3333 1.72217C18.849 1.72217 19.3436 1.92704 19.7083 2.29171C20.0729 2.65638 20.2778 3.15099 20.2778 3.66671C20.2778 4.18244 20.0729 4.67704 19.7083 5.04171L10.9999 13.75L7.33325 14.6667L8.24992 11L16.9583 2.29171Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="button-student-action" data-studentid="${student.data.ID}" data-action="edit_student">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.41667 10.0835V6.41683C6.41667 5.20125 6.89955 4.03547 7.75909 3.17592C8.61864 2.31638 9.78442 1.8335 11 1.8335C12.2156 1.8335 13.3814 2.31638 14.2409 3.17592C15.1004 4.03547 15.5833 5.20125 15.5833 6.41683V10.0835M4.58333 10.0835H17.4167C18.4292 10.0835 19.25 10.9043 19.25 11.9168V18.3335C19.25 19.346 18.4292 20.1668 17.4167 20.1668H4.58333C3.57081 20.1668 2.75 19.346 2.75 18.3335V11.9168C2.75 10.9043 3.57081 10.0835 4.58333 10.0835Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    
                                        <span>Change Password</span>
                                    </button>
                                </li>

                                ${studentButtons}

                            </ul>
                            
                        </div>
                    </td>
                </tr>
            `;
        },
        list: () => {
            skeletonLoader = ``;
            for(i = 0; i < 3; i++){
                skeletonLoader += `<tr class="list-item">
                    <td class="name">
                        <div class="skeleton-loader" style="width:36px; height:36px; border-radius:50px; margin-right:20px"></div>
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="action">
                        <div class="skeleton-loader" style="width:24px; height:5px;"></div>
                    </td>
                </tr>`;
            }
            $(".students-list .body").html(`${skeletonLoader}`)

            JourneyManageClassrooms.api.school.getuserSchoolDetails({})
                .then( e => {
                    console.log("students list", e.students, e.students.length )
                    let studentsList = ``

                    // update topbar counter
                    $(".top-students-count").html(`${e.students.length} Students`);

                    if( e.students.length > 0 ){
                        e.students.map( student => {

                            show = true;

                            if(JourneyManageClassrooms.students.searchKey != ""){
                                haystack = student.data.first_name+student.data.last_name+student.data.user_email+student.data.user_login


                                let classRoomsText = ``;
                                
                                if (student.data && student.data.hasOwnProperty('classrooms')) {
                                    student.data.classrooms.map( e => {
                                        let bgColor = '';
                                        e.category.map( cat => {
                                            bgColor = cat.bg_color;
                                        })
                                        if(bgColor !='') classRoomsText += e.post_title;
                                    })
                                    haystack += classRoomsText;
                                }
                                
                                haystack = haystack.toLowerCase();
                                show = false;
                                if (haystack.includes(JourneyManageClassrooms.students.searchKey)) {
                                    show = true;
                                }
                            }

                            if(show) studentsList += JourneyManageClassrooms.students.listTpl(student); 
                        })
                    }

                    let hasStudents = true;
                    if(studentsList == ""){
                        

                        if( manageClassroomsJs.post_slug=="manage-family" ){
                            studentsList = `<tr class="no-records-found"><td colspan=7>No Children Found</td></tr>`;
                        }else{
                            studentsList = `<tr class="no-records-found"><td colspan=5>No Students Found</td></tr>`;
                        }
                        hasStudents = false;
                    }


                    if ($.fn.DataTable.isDataTable( $(".datatable-students-list") )) {
                        $(".datatable-students-list").DataTable().destroy(); // destroy existing DataTable instance
                    }

                    $(".students-list .body").html(studentsList);

                    if(hasStudents){
                        JourneyManageClassrooms.students.dataTable = $(".datatable-students-list").DataTable({
                            lengthChange: false, // disable length paging dropdown
                            searching: true,
                            "oSearch": { "bSmart": false, "bRegex": true },
                            columnDefs: [
                                { targets: [0,1,2,3], orderable: true }, // enable sorting for columns 1 and 4
                                { targets: '_all', orderable: false }, // disable sorting for all other columns

                                { 
                                    targets: 3,
                                    orderData: [3],
                                    type: 'num',
                                    render: function(data, type, row) {
                                        if (type === 'sort') {
                                            return $(row[3]).find(".hidden").text();
                                        }
                                        return data;
                                    }
                                }
                            ],
                            dom: '<"top"f>rt<"bottom"ip>',
                            language: {
                            searchPlaceholder: "Enter search term here..."
                            },
                            lengthMenu: [ -1 ] // show all rows
                        });
                    }
                })
                .catch( e => {
                    console.log("error on JourneyManageClassrooms.api.teachers.list", e)
                })
        },

        listTemplateModal : () => {
            $("#manage-classroom-add-new").show().css({"display":"flex"}).attr("class","modal manage-classroom-modal class-student-list-template");
            $("#manage-classroom-add-new #class-student-list-template").addClass("active");
            $("#manage-classroom-add-new #class-new-students").removeClass("active");
            modalState();
        }
    },
    families: {
        searchKey: "",
        dataTable:"",
        relationships: ["Father","Mother","Uncle","Aunt"],
        emergencyRelationships: ["Aunty","Uncle","Family Friend","Grandmother","Grandfather","Relative","Sibling","Other"],
        selectedStudents: [],
        uploadedFamilies: {},
        selectedFamily: {},
        getFamilyById : familyid => {
            if(JourneyManageClassrooms.schoolDetails.families.length > 0){
                familyDetails = {};
                JourneyManageClassrooms.schoolDetails.families.map( family => {
                    
                    if(family.id == familyid){
                        familyDetails = family;
                    }
                })
                return familyDetails;
            }else{
                return false;
            }
        },
        viewFamily: familyId => {
            let familyDetails = JourneyManageClassrooms.families.getFamilyById(familyId);

            $("#manage-classroom-modal").show().css({"display":"flex"});
            $("#manage-classroom-modal").attr("class","modal manage-classroom-modal view_family");
            let parentOne = {ID: 0, first_name: '', phone: '', relationship: '', email: ''};
            let parentTwo = {ID: 0, first_name: '', phone: '', relationship: '', email: ''};
            let childRow = "";
            if(familyDetails.parents.length > 0 ){
                i = 0;
                familyDetails.parents.map( parent => {
                    if(i==0){
                        parentOne = parent;
                    }else{
                        parentTwo = parent;
                    }
                    i++;
                })
            }

            let childCount = 0;
            familyDetails.children.map( child => {
                childCount++;
                childRow += `
                <div class="row">
                    <div class="row-label">Child ${childCount}</div>
                    <div class="row-value">${child.first_name} ${child.last_name}</div>
                </div>
                `
            })

            modalContent = `
                <div class="manage-families-container view-family-details">
                    <h2>${familyDetails.group_post.post_title} Family</h2>
                    <div class="group-label">Family</div>
                    <div class="group-content">
                        <div class="row">
                            <div class="row-label">Family</div>
                            <div class="row-value">${familyDetails.group_post.post_title}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Family ID</div>
                            <div class="row-value">${familyDetails.family_id}</div>
                        </div>
                    </div>

                    <div class="group-label">Parent 1</div>
                    <div class="group-content">
                        <div class="row">
                            <div class="row-label">Name</div>
                            <div class="row-value">${parentOne.first_name}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Relationship</div>
                            <div class="row-value">${parentOne.relationship}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Phone</div>
                            <div class="row-value">${parentOne.phone}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Email</div>
                            <div class="row-value">${parentOne.email}</div>
                        </div>
                    </div>

                    <div class="group-label">Parent 2</div>
                    <div class="group-content">
                        <div class="row">
                            <div class="row-label">Name</div>
                            <div class="row-value">${parentTwo.first_name}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Relationship</div>
                            <div class="row-value">${parentTwo.relationship}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Phone</div>
                            <div class="row-value">${parentTwo.phone}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Email</div>
                            <div class="row-value">${parentTwo.email}</div>
                        </div>
                    </div>

                    <div class="group-label">Emergency Contact</div>
                    <div class="group-content">
                        <div class="row">
                            <div class="row-label">Name</div>
                            <div class="row-value">${familyDetails.emergency_contact_name}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Relationship</div>
                            <div class="row-value">${familyDetails.emergency_contact_relationship}</div>
                        </div>
                        <div class="row">
                            <div class="row-label">Phone</div>
                            <div class="row-value">${familyDetails.emergency_contact_phone}</div>
                        </div>
                    </div>


                    <div class="group-label">Child</div>
                    <div class="group-content">
                        ${childRow}
                    </div>
                </div>
            `
            $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
            modalState();
        },
        manageFamily: familyId => {
            let familyDetails = {};
            let title = "";

            JourneyManageClassrooms.families.selectedStudents = [];
            if(familyId !=0 ){
                familyDetails = JourneyManageClassrooms.families.getFamilyById(familyId);
                title = familyDetails.group_post.post_title;
            }
            $("#manage-classroom-modal").show().css({"display":"flex"});
            $("#manage-classroom-modal").attr("class","modal manage-classroom-modal add_family");
            console.log("JourneyManageClassrooms.families.selectedStudents", JourneyManageClassrooms.families.selectedStudents)
            modalContent = `
                <div class="manage-families-container ">
                    <h2>${title} Family</h2>

                    <form class="form-add-family" onsubmit="JourneyManageClassrooms.families.updateFamily(${familyId},this); return false;">
                        <input type="hidden" name="institute_id" value="${manageClassroomsJs.selectedInstituteId}" />
                        <input type="hidden" name="id" value="${familyId}"/>
                        ${JourneyManageClassrooms.families.templates.form(familyId)}
                        <div class="button-container">
                            <button type="submit" class="btn-add btn-add-family">Save</button>
                        </div>
                    </form>
                </div>
            `
            $("#manage-classroom-modal .modal-content .main-content").html(modalContent);

            //JourneyManageClassrooms.families.displayAddStudents( $(".selected-students") );
            if(familyId !=0 ){
                let tplStudents = "<div class='student-items'>";
                familyDetails.children.map( child => {
                     
                    tplStudents += JourneyManageClassrooms.families.templates.studentItem({
                        id: child.ID,
                        avatar: child.avatar_url,
                        name: child.first_name +" "+child.last_name
                    })

                    JourneyManageClassrooms.families.selectedStudents.push(child.ID);
                });
               
                tplStudents += "</div>";
                $(".selected-students").html(tplStudents);
            }
            
            modalState();
        },

        addChild: familyId => {
            let familyDetails = {};
            let title = "";

            JourneyManageClassrooms.families.selectedStudents = [];
            if(familyId !=0 ){
                familyDetails = JourneyManageClassrooms.families.getFamilyById(familyId);
            }
            $("#manage-classroom-modal").show().css({"display":"flex"});
            $("#manage-classroom-modal").attr("class","modal manage-classroom-modal add_child");
            modalContent = `
                <div class="manage-families-container ">
                    <h2>Add Child to Family</h2>

                    <form class="form-add-family" onsubmit="JourneyManageClassrooms.families.addChildSave(${familyId},this); return false;">
                        <input type="hidden" name="institute_id" value="${manageClassroomsJs.selectedInstituteId}" />
                        <input type="hidden" name="id" value="${familyId}"/>
                        ${JourneyManageClassrooms.families.templates.addChild(familyId)}
                        <div class="button-container">
                            <button type="submit" class="btn-add btn-add-child" disabled>Add Child</button>
                        </div>
                    </form>
                </div>
            `
            $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
 
            
            modalState();
        },
        displayAddStudents: ( target ) => {
            let tplStudents = "<div class='student-items'>";
            JourneyManageClassrooms.schoolDetails.students.map( student => {
                //student.data.ID
                if (JourneyManageClassrooms.families.selectedStudents.includes(parseInt(student.data.ID))) {

                    tplStudents += JourneyManageClassrooms.families.templates.studentItem({
                        id: student.data.ID,
                        avatar: student.data.avatar_url,
                        name: student.data.first_name +" "+student.data.last_name
                    })
                }
            })
            tplStudents += "</div>";
            target.html(tplStudents);
        },
        templates: {
            addChild: familyId => {
                let parentOne = {ID: 0, first_name: '', phone: '', relationship: '', email: ''};
                let parentTwo = {ID: 0, first_name: '', phone: '', relationship: '', email: ''};
                let familyName=""; let familyIdNo=""; let emName=""; let emPhone=""; let emRel="";

                let children = "";
                if(familyId !=0 ){
                    familyDetails = JourneyManageClassrooms.families.getFamilyById(familyId);
                    if(familyDetails.parents.length > 0 ){
                        i = 0;
                        
                        emName = familyDetails.emergency_contact_name;
                        emPhone = familyDetails.emergency_contact_phone;
                        emRel = familyDetails.emergency_contact_relationship;
                        familyDetails.parents.map( parent => {
                            if(i==0){
                                parentOne = parent;
                            }else{
                                parentTwo = parent;
                            }
                            i++;
                        })
                    }
                    familyName = familyDetails.group_post.post_title;
                    familyIdNo = familyDetails.family_id;

                    if(familyDetails.children.length > 0 ){
                        familyDetails.children.map( child => {
                            children += `
                                <div class="child"><img src="${child.avatar_url}" /><span>${child.first_name} ${child.last_name}</span></div>
                            `
                        })
                    }
                }
                return `
                    <div class="child-to-family">
                        <div class="row">
                            <div class="row-label">Family</div>
                            <div class="row-value">${familyDetails.group_post.post_title}</div>
                        </div>

                        <div class="row">
                            <div class="row-label">Family ID</div>
                            <div class="row-value">${familyDetails.family_id}</div>
                        </div>

                        <div class="row">
                            <div class="row-label">Parent 1</div>
                            <div class="row-value">${parentOne.first_name}</div>
                        </div>

                        <div class="row">
                            <div class="row-label">Parent 2</div>
                            <div class="row-value">${parentTwo.first_name}</div>
                        </div>

                        <div class="row">
                            <div class="row-label">Child</div>
                            <div class="row-value">${children}</div>
                        </div>
                    </div>

                    <div class="field-group">
                        <div class="field-input ">
                            <div class="add-child-container">
                                <label class="field-label">Add Child</label>
                                <input type="text" class="search-child" placeholder="Type / Search Student"/>
                                <div class="search-result"></div>
                                <div class="selected-students"></div>
                            </div>
                        </div>
                    </div>
                `
            },
            studentItem: student => {
                return `
                <div class="item">
                        <img src="${student.avatar}"/> 
                        <span class="name">${student.name}</span>
                        <button type="button" class="btn-remove-student" data-studentid="${student.id}">
                            <input type="hidden" name="students[]" value="${student.id}"/>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="#5F94F7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="#5F94F7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                `
            },
            relationship: (field, v) => {
                if(v == "") cl = "novalue";
                else cl = "";

                required = "";
                if(field == "parent_relationship_1") required = "required";

                let parentRelationship = `
                <select name="${field}" class="${cl}" placeholder="Select Relationship" ${required}>
                    <option class='placeholder' value=''>Select Relationship</option>`;
                JourneyManageClassrooms.families.relationships.map( r => {
                    selected = "";
                    if(v.toLowerCase() == r.toLowerCase()) selected = "selected";
                    parentRelationship += `<option ${selected} value="${r}">${r}</option>`
                })
                parentRelationship += `</select>`
                return parentRelationship;
            },
            emergencyRelationship: ( field, v) => {
                //emergencyRelationships
                if(v == "") cl = "novalue";
                else cl = "";

                let parentRelationship = `
                <select name="${field}" class="${cl}" placeholder="Select Relationship" required>
                    <option class='placeholder' value=''>Select Relationship</option>`;
                JourneyManageClassrooms.families.emergencyRelationships.map( r => {
                    selected = "";
                    if(v.toLowerCase() == r.toLowerCase()) selected = "selected";
                    parentRelationship += `<option ${selected} value="${r}">${r}</option>`
                })
                parentRelationship += `</select>`
                return parentRelationship;
            },
            form: familyId => {
                let familyDetails = {};
                let parentOne = {ID: 0, first_name: '', phone: '', relationship: '', email: ''};
                let parentTwo = {ID: 0, first_name: '', phone: '', relationship: '', email: ''};
                let familyName=""; let familyIdNo=""; let emName=""; let emPhone=""; let emRel="";
                let editFields = "";

                if(familyId !=0 ){
                    familyDetails = JourneyManageClassrooms.families.getFamilyById(familyId);
                    if(familyDetails.parents.length > 0 ){
                        i = 0;
                        
                        emName = familyDetails.emergency_contact_name;
                        emPhone = familyDetails.emergency_contact_phone;
                        emRel = familyDetails.emergency_contact_relationship;
                        familyDetails.parents.map( parent => {
                            if(i==0){
                                parentOne = parent;
                            }else{
                                parentTwo = parent;
                            }
                            i++;
                        })
                    }
                    familyName = familyDetails.group_post.post_title;
                    familyIdNo = familyDetails.family_id;

                    editFields = `
                        <input type="hidden" name="parent_id_1" value="${parentOne.ID}"/>
                        <input type="hidden" name="parent_id_2" value="${parentTwo.ID}"/>
                    `
                }

                return `
                ${editFields}
                <div class="group-label">Family</div>
                <div class="field-group">
                    <div class="field-input fullname">
                        <div class="">
                            <label class="field-label">Family Name</label>
                            <input type="text" name="family_name" placeholder="e.g Doe" value="${familyName}" required/>
                        </div>
                        <div class="">
                            <label class="field-label">Family ID</label>
                            <input type="text" name="family_id" placeholder="e.g doe1" value="${familyIdNo}" required/>
                        </div>
                    </div>
                </div>

                <div class="group-label">Parent 1</div>
                <div class="field-group">
                    <div class="field-input fullname">
                        <div class="">
                            <label class="field-label">Name</label>
                            <input type="text" name="parent_name_1" placeholder="Name" value="${parentOne.first_name}" required/>
                        </div>
                        <div class="">
                            <label class="field-label">Relationship</label>
                            ${JourneyManageClassrooms.families.templates.relationship("parent_relationship_1",parentOne.relationship)}
                        </div>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-input fullname">
                        <div class="">
                            <label class="field-label">Phone</label>
                            <input type="text" name="parent_phone_1" placeholder="Phone Number" required value="${parentOne.phone}"/>
                        </div>
                        <div class="">
                            <label class="field-label">Email</label>
                            <input type="email" name="parent_email_1" placeholder="Email Address" required value="${parentOne.email}"/>
                        </div>
                    </div>
                </div>

                <div class="group-label">Parent 2</div>
                <div class="field-group">
                    <div class="field-input fullname">
                        <div class="">
                            <label class="field-label">Name</label>
                            <input type="text" name="parent_name_2" placeholder="Name"  value="${parentTwo.first_name}"/>
                        </div>
                        <div class="">
                            <label class="field-label">Relationship</label>
                            ${JourneyManageClassrooms.families.templates.relationship("parent_relationship_2", parentTwo.relationship)}
                        </div>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-input fullname">
                        <div class="">
                            <label class="field-label">Phone</label>
                            <input type="text" name="parent_phone_2" placeholder="Phone Number"  value="${parentTwo.phone}"/>
                        </div>
                        <div class="">
                            <label class="field-label">Email</label>
                            <input type="email" name="parent_email_2" placeholder="Email Address"  value="${parentTwo.email}"/>
                        </div>
                    </div>
                </div>


                <div class="group-label">Emergency Contact</div>
                <div class="field-group">
                    <div class="field-input fullname">
                        <div class="">
                            <label class="field-label">Name</label>
                            <input type="text" name="emergency_contact_name" placeholder="Name" required value="${emName}"/>
                        </div>
                        <div class="">
                            <label class="field-label">Relationship</label>
                            ${JourneyManageClassrooms.families.templates.emergencyRelationship("emergency_contact_relationship", emRel)}
                        </div>
                    </div>
                </div>
                <div class="field-group">
                    <div class="field-input ">
                        <div class="">
                            <label class="field-label">Phone</label>
                            <input type="text" name="emergency_contact_phone" placeholder="Phone Number" required value="${emPhone}"/>
                        </div>
                    </div>
                </div>

                <div class="group-label">Child</div>
                <div class="field-group">
                    <div class="field-input ">
                        <div class="add-child-container">
                            <label class="field-label">Add Child</label>
                            <input type="text" class="search-child" placeholder="Type / Search Student"/>
                            <div class="search-result"></div>
                            <div class="selected-students"></div>
                        </div>
                    </div>
                </div>
                `
            },

            addTabs: familyid => {
                let familyDetails = {};
                if(familyid !=0 ) familyDetails = JourneyManageClassrooms.families.getFamilyById(familyId);

                console.log("familyid", familyid, familyDetails)

                tpl = `
                <div class="teacher-add-container manage-classroom-families">

                    <div class="nav-tabs">
                        <button type="button" class="active" data-target="#add_family">
                        Add Family
                        </button>
                        <button type="button" data-target="#import_family">Import Family</button>
                    </div>

                    <div class="tabs">
                        <div class="tab active" id="add_family">
                            ${JourneyManageClassrooms.families.templates.form(familyid)}
                            <div class="button-container">
                                <button type="submit" class="btn-add btn-add-family">Save</button>
                            </div>
                        </div> <!-- .active-->
                        <div class="tab" id="import_family">
                            <div class="top">
                                <h2>Import Family</h2>
                            </div>

                            <div class="download">
                                <span>Import family using our template.</span>
                                <a href="/wp-content/themes/buddyboss-theme-child/page-templates/manage-classrooms/import-families-template.csv" download>Download Template</a>
                            </div>

                            <div class="error-family-csv"></div>
                            <div class="successfull-imports-container"></div>
                            <div id="import-families-dropzone" class="dropzone" action="?" ></div>
                            <div class="families-csv-container"></div>

                            <button type="button" class="btn-import">IMPORT</button>
                        </div>
                    </div>
                </div>
                `
                return tpl;
            }
        },
        
       
        listTpl: family => {
             
             
            let parentOne = "";
            let parentOnePhone = "";
            let parentTwo = "";
            let parentTwoPhone = "";

            if(family.parents.length > 0 ){
                i = 0;
                family.parents.map( parent => {
                    if(i==0){
                        parentOne = parent.first_name;
                        parentOnePhone = parent.phone;
                    }else{
                        parentTwo = parent.first_name;
                        parentTwoPhone = parent.phone;
                    }
                    i++;
                })
            }

            let children = "<div style='min-height:30px'>&nbsp;</div>";
            let childCount = 0;
            if(family.children.length > 0 ){
                children = "";
                family.children.map( child => {
                    if(childCount < 3){
                        children += `
                            <img src="${child.avatar_url}" />
                        `
                    }
                    childCount++;
                })
            }

            if( ( childCount - 3 ) > 0 ){
                children += `<div class="more">+${( childCount - 3 )}</div>`
            }

            return `
                <tr class="list-item">
                    <td >
                        ${family.group_post.post_title}
                    </td>
                    <td > ${family.family_id} </td>
                    <td  >${parentOne}</td>
                    <td  >${parentOnePhone}</td>
                    <td  >${parentTwo}</td>
                    <td  >${parentTwoPhone}</td>
                    <td class="children">${children}</td>
                    <td class="action">
                        <button type="button" class="list-item-action-button">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12C18 12.5523 18.4477 13 19 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12C4 12.5523 4.44772 13 5 13Z" stroke="#6B6F72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        
                        <div class="submenu">
                            <div class="submenu-overlay"></div>
                            <ul>
                                <li>
                                    <button type="button" class="button-family-action" data-familyid="${family.id}" data-action="view_family" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.916016 11C0.916016 11 4.58268 3.66669 10.9993 3.66669C17.416 3.66669 21.0827 11 21.0827 11C21.0827 11 17.416 18.3334 10.9993 18.3334C4.58268 18.3334 0.916016 11 0.916016 11Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10.9993 13.75C12.5181 13.75 13.7494 12.5188 13.7494 11C13.7494 9.48124 12.5181 8.25002 10.9993 8.25002C9.48057 8.25002 8.24935 9.48124 8.24935 11C8.24935 12.5188 9.48057 13.75 10.9993 13.75Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>View Family</span>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="button-family-action" data-familyid="${family.id}" data-action="manage_family" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.084 3.66665H3.66732C3.18109 3.66665 2.71477 3.8598 2.37096 4.20362C2.02714 4.54744 1.83398 5.01375 1.83398 5.49998V18.3333C1.83398 18.8195 2.02714 19.2859 2.37096 19.6297C2.71477 19.9735 3.18109 20.1667 3.66732 20.1667H16.5007C16.9869 20.1667 17.4532 19.9735 17.797 19.6297C18.1408 19.2859 18.334 18.8195 18.334 18.3333V11.9167M16.959 2.29165C17.3237 1.92698 17.8183 1.72211 18.334 1.72211C18.8497 1.72211 19.3443 1.92698 19.709 2.29165C20.0737 2.65632 20.2785 3.15093 20.2785 3.66665C20.2785 4.18238 20.0737 4.67698 19.709 5.04165L11.0007 13.75L7.33398 14.6667L8.25065 11L16.959 2.29165Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Manage Family</span>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="button-families-action" data-action="broadcast_email" data-broadcasttype="family_single" data-familyid="${family.id}" data-action="email_family" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.1673 5.50002C20.1673 4.49169 19.3423 3.66669 18.334 3.66669H3.66732C2.65898 3.66669 1.83398 4.49169 1.83398 5.50002M20.1673 5.50002V16.5C20.1673 17.5084 19.3423 18.3334 18.334 18.3334H3.66732C2.65898 18.3334 1.83398 17.5084 1.83398 16.5V5.50002M20.1673 5.50002L11.0007 11.9167L1.83398 5.50002" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Email Family</span>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="button-family-action" data-familyid="${family.id}" data-action="add_child" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.666 19.25V17.4167C14.666 16.4442 14.2797 15.5116 13.5921 14.8239C12.9044 14.1363 11.9718 13.75 10.9993 13.75H4.58268C3.61022 13.75 2.67759 14.1363 1.98996 14.8239C1.30232 15.5116 0.916016 16.4442 0.916016 17.4167V19.25M18.3327 7.33333V12.8333M21.0827 10.0833H15.5827M11.4577 6.41667C11.4577 8.44171 9.81606 10.0833 7.79102 10.0833C5.76597 10.0833 4.12435 8.44171 4.12435 6.41667C4.12435 4.39162 5.76597 2.75 7.79102 2.75C9.81606 2.75 11.4577 4.39162 11.4577 6.41667Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Add Child</span>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="button-family-action" data-familyid="${family.id}" data-action="remove" >
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.75 5.49998H4.58333M4.58333 5.49998H19.25M4.58333 5.49998V18.3333C4.58333 18.8195 4.77649 19.2859 5.1203 19.6297C5.46412 19.9735 5.93044 20.1666 6.41667 20.1666H15.5833C16.0696 20.1666 16.5359 19.9735 16.8797 19.6297C17.2235 19.2859 17.4167 18.8195 17.4167 18.3333V5.49998M7.33333 5.49998V3.66665C7.33333 3.18042 7.52649 2.7141 7.8703 2.37028C8.21412 2.02647 8.68044 1.83331 9.16667 1.83331H12.8333C13.3196 1.83331 13.7859 2.02647 14.1297 2.37028C14.4735 2.7141 14.6667 3.18042 14.6667 3.66665V5.49998M9.16667 10.0833V15.5833M12.8333 10.0833V15.5833" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Remove</span>
                                    </button>
                                </li>
                            </ul>
                            
                        </div>
                    </td>
                </tr>
            `;
        },
        list: () => {
            skeletonLoader = ``;
            for(i = 0; i < 3; i++){
                skeletonLoader += `<tr class="list-item">
                    <td class="name">
                        <div class="skeleton-loader" style="width:36px; height:36px; border-radius:50px; margin-right:20px"></div>
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="email">
                        <div class="skeleton-loader" style="width:200px; height:30px;"></div>
                    </td>
                    <td class="action">
                        <div class="skeleton-loader" style="width:24px; height:5px;"></div>
                    </td>
                </tr>`;
            }
            $(".families-list .body").html(`${skeletonLoader}`)

            JourneyManageClassrooms.api.school.getuserSchoolDetails({})
                .then( e => {
                    console.log("families list", e.families, e.families.length )
                    let familiesList = ``

                    // update topbar counter
                    $(".top-families-count").html(`${e.families.length} Families`);
                    
                    if( e.families.length > 0 ){
                        e.families.map( family => {

                            show = true;

                            if(JourneyManageClassrooms.students.searchKey != ""){
                                haystack = student.data.first_name+student.data.last_name+student.data.user_email+student.data.user_login

                                let classRoomsText = ``;
                                
                                if (student.data && student.data.hasOwnProperty('classrooms')) {
                                    student.data.classrooms.map( e => {
                                        let bgColor = '';
                                        e.category.map( cat => {
                                            bgColor = cat.bg_color;
                                        })
                                        if(bgColor !='') classRoomsText += e.post_title;
                                    })
                                    haystack += classRoomsText;
                                }
                                
                                haystack = haystack.toLowerCase();
                                show = false;
                                if (haystack.includes(JourneyManageClassrooms.students.searchKey)) {
                                    show = true;
                                }
                            }

                            if(show) familiesList += JourneyManageClassrooms.families.listTpl(family); 
                        })
                    }

                    let hasStudents = true;
                    if(familiesList == ""){
                        familiesList = `<tr class="no-records-found"><td colspan=8>No Families Found</td></tr>`;
                        hasStudents = false;
                    }


                    if ($.fn.DataTable.isDataTable( $(".datatable-families-list") )) {
                        $(".datatable-families-list").DataTable().destroy(); // destroy existing DataTable instance
                    }

                    $(".families-list .body").html(familiesList);

                    if(hasStudents){
                        JourneyManageClassrooms.families.dataTable = $(".datatable-families-list").DataTable({
                            lengthChange: false, // disable length paging dropdown
                            searching: true,
                            "oSearch": { "bSmart": false, "bRegex": true },
                            /*columnDefs: [
                                { targets: [0,1,2,3], orderable: true }, // enable sorting for columns 1 and 4
                                { targets: '_all', orderable: false }, // disable sorting for all other columns

                                { 
                                    targets: 3,
                                    orderData: [3],
                                    type: 'num',
                                    render: function(data, type, row) {
                                        if (type === 'sort') {
                                            return $(row[3]).find(".hidden").text();
                                        }
                                        return data;
                                    }
                                }
                            ],*/
                            dom: '<"top"f>rt<"bottom"ip>',
                            language: {
                            searchPlaceholder: "Enter search term here..."
                            },
                            lengthMenu: [ -1 ] // show all rows
                        });
                    }
                })
                .catch( e => {
                    console.log("error on JourneyManageClassrooms.api.teachers.list", e)
                })
        },

        listTemplateModal : () => {
            $("#manage-classroom-add-new").show().css({"display":"flex"}).attr("class","modal manage-classroom-modal class-student-list-template");
            $("#manage-classroom-add-new #class-student-list-template").addClass("active");
            $("#manage-classroom-add-new #class-new-students").removeClass("active");
            modalState();
        },
        saveFamily: (data) => {
            $(".btn-add-family").fadeTo("fast",.3)

            let modalContent = `
            <div class="please-wait tab ">
                <div class="please-wait-container" style="display:flex; flex-direction:column;align-items:center; justify-content:center">
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/admin-institute-onboarding/j2j loading.gif" style="max-width:100px"/>
                    <div style="    color: var(--accent-text-dark, #37394A);
                    text-align: center;
                    font-family: Mikado;
                    font-size: 24px;
                    font-style: normal;
                    font-weight: 500;
                    line-height: 36px;
                    margin-top: 25px;">
                        Saving, please wait...
                    </div>
                </div>
            </div>
            `
            $("#manage-classroom-modal .modal-content .main-content").html(modalContent);

            JourneyManageClassrooms.api.families.addFamily($(data).serialize())
                .then( e => {
                    $(".btn-add-family").fadeTo("fast",1);
                    $("#manage-classroom-modal").removeClass("add_student");
                    $("#manage-classroom-modal").addClass("success_add_student");

                    modalContent = `
                        <div class="success-create-family">
                            <img src="/wp-content/themes/buddyboss-theme-child/assets/img/manage-classroom/success-check.svg"/>
                            <h2>Family account has been successfully created</h2>
                            <p>An invitation email has been sent to the<br/>provided email address.</p>
                            <button type="button" class="btn-back-students">Back to Students</button>
                        </div>
                    `;
                    $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
                    JourneyManageClassrooms.schoolDetails = ""; // clear school details to get fresh data from backend
                    JourneyManageClassrooms.families.list();
                })
                .catch( e => {
                    console.log("error add family", e )
                })
            return false;
        },
        updateFamily: (familyid, data) => {
            $(".btn-add-family").fadeTo("fast",.3);
            let modalContent = `
            <div class="please-wait tab ">
                <div class="please-wait-container" style="display:flex; flex-direction:column;align-items:center; justify-content:center">
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/admin-institute-onboarding/j2j loading.gif" style="max-width:100px"/>
                    <div style="    color: var(--accent-text-dark, #37394A);
                    text-align: center;
                    font-family: Mikado;
                    font-size: 24px;
                    font-style: normal;
                    font-weight: 500;
                    line-height: 36px;
                    margin-top: 25px;">
                        Saving, please wait...
                    </div>
                </div>
            </div>
            `
            $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
            JourneyManageClassrooms.api.families.updateFamily( {id:familyid, form: $(data).serialize()})
                .then( e => {
                    $(".btn-add-family").fadeTo("fast",1);
                    $("#manage-classroom-modal").removeClass("add_student");
                    $("#manage-classroom-modal").addClass("success_add_student");

                    let modalContent = `
                        <div class="success-create-family">
                            <img src="/wp-content/themes/buddyboss-theme-child/assets/img/manage-classroom/success-check.svg"/>
                            <h2>Family account has been successfully updated</h2>
                            <p>An email has been sent to the parents<br/>about the update made.</p>
                            <button type="button" class="btn-back-students">Back to Students</button>
                        </div>
                    `;
                    $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
                    JourneyManageClassrooms.schoolDetails = ""; // clear school details to get fresh data from backend
                    JourneyManageClassrooms.families.list();
                })
                .catch( e => {
                    console.log("error add family", e )
                })
            return false;
        },
        addChildSave: (familyid, data) => {
            $(".btn-add-child").fadeTo("fast",.3)
            JourneyManageClassrooms.api.families.addChildSave( {id:familyid, form: $(data).serialize()})
                .then( e => {
                    $(".btn-add-child").fadeTo("fast",1);
                    $("#manage-classroom-modal").addClass("success_add_student");

                    let modalContent = `
                        <div class="success-create-family">
                            <img src="/wp-content/themes/buddyboss-theme-child/assets/img/manage-classroom/success-check.svg"/>
                            <h2>Family account has been successfully updated</h2>
                            <p>An email has been sent to the parents<br/>about the update made.</p>
                            <button type="button" class="btn-back-students">Back to Students</button>
                        </div>
                    `;
                    $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
                    JourneyManageClassrooms.schoolDetails = ""; // clear school details to get fresh data from backend
                    JourneyManageClassrooms.families.list();
                })
                .catch( e => {
                    console.log("error add child", e )
                })
            return false;
        },
        
        renderDropzone: () => {
             
            
            if(JourneyManageClassrooms.importFamiliesDropzone != "") JourneyManageClassrooms.importFamiliesDropzone.destroy();
            JourneyManageClassrooms.importFamiliesDropzone = new Dropzone("div#import-families-dropzone", 
                { 
                    url: `${safarObject.apiBaseurl}/school/families/import_csv`,
                    maxFilesize: 10485760,
                    acceptedFiles: ".csv",
                    headers: {
                        'X-WP-Nonce': safarObject.wpnonce,
                    },
                    autoProcessQueue: true, // Disable auto-upload
                }
            );
            $(".btn-import").fadeTo("fast",.3).attr("disabled","disabled");
            let familyCsvFile = {};
            JourneyManageClassrooms.importFamiliesDropzone.on("addedfile", file => {
                familyCsvFile = file;
                if(file.size >= 10485760){
                    console.log("file is greater than 10mb", file.zize)
                    JourneyManageClassrooms.importFamiliesDropzone.removeFile(file);
                    return false;
                }else{
                    console.log("import families dropzone")
                    $(".btn-import").fadeTo("fast",1).removeAttr("disabled","disabled")
                    $(document).on("click", ".btn-import", () => {
                        // Manually trigger the file upload when the button is clicked
                        JourneyManageClassrooms.importFamiliesDropzone.processQueue();
                    });
                    return file;
                }
            });
            
    
    
            JourneyManageClassrooms.importFamiliesDropzone.on("complete", file => {
                JourneyManageClassrooms.importFamiliesDropzone.removeAllFiles();
                $("#import_family .upload-form").hide();
                $("#import_family .imported-families").show();
                let response = $.parseJSON(file.xhr.response);

                $(".error-family-csv").html(``);
                //if(response.success){
                    //console.log("familyCsvFile", familyCsvFile)
                    JourneyManageClassrooms.families.uploadedFamilies = response.families;
                    let fileSize = Math.ceil(familyCsvFile.size / 1024);
                    $("#import-families-dropzone").hide();
                    $(".families-csv-container").html(`
                        <div class="csv">
                            <div>
                                <svg width="32" height="40" viewBox="0 0 32 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M23.1578 8.4212H27.9851L23.1578 3.59386V8.4212ZM2.10525 37.8947H29.4735V10.5265H22.1052C21.5241 10.5265 21.0525 10.0548 21.0525 9.47383V2.10545H2.10525V37.8947ZM30.5262 40H1.05263C0.471535 40 0 39.5283 0 38.9474V1.05282C0 0.471727 0.471535 2.35867e-05 1.05263 2.35867e-05H22.1052C22.3978 -0.00195009 22.6631 0.12009 22.8504 0.309563L31.2693 8.72847C31.4588 8.91597 31.581 9.1811 31.5788 9.47386V38.9474C31.5788 39.5283 31.1073 40 30.5262 40Z" fill="#98C03D"/>
                                <path d="M6 23.9638C6 23.2315 6.15361 22.5724 6.46082 21.9866C6.76803 21.3972 7.21634 20.9292 7.80576 20.5827C8.39518 20.2326 9.07747 20.0576 9.85265 20.0576C10.3956 20.0576 10.8797 20.129 11.3048 20.2719C11.7298 20.4112 12.0496 20.5738 12.2639 20.7595C12.271 20.8524 12.2014 21.0614 12.0549 21.3865C11.9085 21.7115 11.7852 21.9187 11.6852 22.008C11.453 21.8544 11.1779 21.724 10.86 21.6169C10.5421 21.5061 10.2099 21.4508 9.86336 21.4508C9.20965 21.4508 8.66309 21.6955 8.22371 22.1849C7.7879 22.6742 7.56999 23.2815 7.56999 24.0067C7.56999 24.8354 7.77182 25.482 8.17549 25.9464C8.58272 26.4108 9.10426 26.643 9.74012 26.643C10.401 26.643 11.0279 26.409 11.6209 25.941C11.7138 26.0125 11.8317 26.1679 11.9745 26.4072C12.121 26.6466 12.2371 26.8591 12.3228 27.0449C12.0906 27.3021 11.7209 27.5343 11.2137 27.7414C10.7064 27.9451 10.1724 28.0469 9.61152 28.0469C9.01496 28.0469 8.4827 27.9433 8.01474 27.7361C7.55035 27.5253 7.17348 27.2342 6.88413 26.8627C6.59478 26.4912 6.37508 26.0607 6.22505 25.5713C6.07502 25.0819 6 24.5461 6 23.9638Z" fill="#98C03D"/>
                                <path d="M13.0194 27.0341C13.0194 27.027 13.0194 27.0181 13.0194 27.0074C13.0194 26.9645 13.0301 26.902 13.0516 26.8198C13.0802 26.7198 13.1194 26.6144 13.1695 26.5037C13.2195 26.3894 13.2713 26.2804 13.3248 26.1768C13.3784 26.0732 13.4284 25.9911 13.4749 25.9303C13.5213 25.8696 13.5553 25.8464 13.5767 25.8607C14.234 26.3894 14.8966 26.6537 15.5646 26.6537C15.9933 26.6537 16.3309 26.5751 16.5774 26.4179C16.8238 26.2608 16.9471 26.0125 16.9471 25.6731C16.9471 25.4266 16.8238 25.2177 16.5774 25.0462C16.3344 24.8712 15.9094 24.7158 15.3021 24.58C14.5555 24.4193 14 24.1567 13.6356 23.7924C13.2748 23.428 13.0944 22.9297 13.0944 22.2974C13.0944 21.9973 13.1552 21.7133 13.2766 21.4454C13.3981 21.1775 13.5713 20.9399 13.7964 20.7327C14.0214 20.522 14.3072 20.3559 14.6537 20.2344C15.0038 20.1094 15.3914 20.0469 15.8165 20.0469C16.338 20.0469 16.8131 20.1147 17.2418 20.2505C17.674 20.3827 18.008 20.5452 18.2438 20.7381C18.2581 20.7488 18.2652 20.7685 18.2652 20.797C18.2652 20.8542 18.2402 20.9471 18.1902 21.0757C18.1152 21.2686 18.0259 21.4543 17.9223 21.6329C17.8223 21.8116 17.758 21.8973 17.7294 21.8901C17.14 21.5329 16.5131 21.3543 15.8486 21.3543C15.445 21.3543 15.136 21.44 14.9216 21.6115C14.7073 21.7794 14.6001 22.0009 14.6001 22.2759C14.6001 22.3867 14.6162 22.4831 14.6484 22.5653C14.6841 22.6475 14.7484 22.7314 14.8413 22.8171C14.9377 22.8993 15.0806 22.9779 15.2699 23.0529C15.4628 23.1279 15.7075 23.2011 16.004 23.2726C16.4148 23.3762 16.7667 23.4905 17.0596 23.6155C17.3561 23.737 17.6205 23.8888 17.8526 24.071C18.0848 24.2532 18.2581 24.4729 18.3724 24.7301C18.4903 24.9837 18.5492 25.2766 18.5492 25.6088C18.5492 25.9232 18.4849 26.2179 18.3563 26.493C18.2277 26.7644 18.0438 27.0074 17.8044 27.2217C17.5651 27.4325 17.2561 27.5986 16.8774 27.72C16.5023 27.8415 16.0826 27.9022 15.6182 27.9022C15.0717 27.9022 14.5608 27.8093 14.0857 27.6236C13.6106 27.4378 13.2552 27.2413 13.0194 27.0341Z" fill="#98C03D"/>
                                <path d="M19.0476 20.2666C19.0654 20.2094 19.2012 20.163 19.4548 20.1273C19.712 20.088 19.9478 20.0683 20.1621 20.0683C20.38 20.0683 20.514 20.079 20.564 20.1005C20.6247 20.2505 20.9123 21.1971 21.4267 22.9404C21.9411 24.6801 22.2376 25.7464 22.3161 26.1393C22.3233 26.1393 22.334 26.1375 22.3483 26.1339C22.3662 26.1268 22.3787 26.1232 22.3858 26.1232C22.4787 25.7017 22.7573 24.7033 23.2217 23.1279C23.6861 21.549 23.9915 20.547 24.138 20.1219C24.1523 20.1076 24.2184 20.1005 24.3362 20.1005C24.4648 20.1005 24.6559 20.1094 24.9096 20.1273C25.3918 20.163 25.6401 20.2166 25.6544 20.288C25.5508 20.7452 25.1418 22.1116 24.4273 24.3871C23.7165 26.6626 23.336 27.8165 23.286 27.8486C23.236 27.8808 22.9627 27.9076 22.4662 27.929C22.2376 27.9397 22.0464 27.9451 21.8928 27.9451C21.7142 27.9451 21.5892 27.9379 21.5177 27.9236C21.5035 27.9201 21.3052 27.3574 20.923 26.2358C20.5443 25.1105 20.1514 23.912 19.7441 22.6403C19.3369 21.3686 19.1047 20.5774 19.0476 20.2666Z" fill="#98C03D"/>
                                </svg>
                            </div>
                            <div class="text">
                                <div class="file-name">${familyCsvFile.name}</div>
                                <div class="file-size">${fileSize}kb</div>
                            </div>
                            <div class="">
                                <button type="button" class="btn-cancel">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 1L1 9M1 1L9 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    `);
                //}else{
                    let errorFields = ``;
                    response.errors.map( error => {
                        errorFields += `<div><b>${error}</b></div>`;
                    })
                    $(".error-family-csv").html(`
                        <div class="error-message">
                            <div class="icon">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 8.2V13M13 17.8H13.012M25 13C25 19.6274 19.6274 25 13 25C6.37258 25 1 19.6274 1 13C1 6.37258 6.37258 1 13 1C19.6274 1 25 6.37258 25 13Z" stroke="#EF746F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Import failed</span>
                            </div>
                            <div class="text">
                                Error occurred while importing your data. Please check this field: ${errorFields} Update your csv and try again.
                            </div>
                        </div>
                    `);

                    if(response.families.length > 0 ){
                        $(".successfull-imports-container").html(`
                        <div class="successfull-imports">
                            <span class="icon">
                                <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 11.08V12C20.9988 14.1564 20.3005 16.2547 19.0093 17.9818C17.7182 19.709 15.9033 20.9725 13.8354 21.5839C11.7674 22.1953 9.55726 22.1219 7.53447 21.3746C5.51168 20.6273 3.78465 19.2461 2.61096 17.4371C1.43727 15.628 0.879791 13.4881 1.02168 11.3363C1.16356 9.18455 1.99721 7.13631 3.39828 5.49706C4.79935 3.85781 6.69279 2.71537 8.79619 2.24013C10.8996 1.7649 13.1003 1.98232 15.07 2.85999M21 3.99999L11 14.01L8.00001 11.01" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="text">You are about to import ${response.families.length} out of ${response.total} families</span>
                        </div>
                        `)
                    }
               // }
                
              
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
                        <b class="blue">Choose file</b> or <b>drag here</b>
                    </div>
                    <div class="size-limit">Size limit: 10mb</div>
                </div>
            `)
            console.log("render dropzone")
        },
        saveImportedFamilies: () => {
            console.log("JourneyManageClassrooms.families.uploadedFamilies", JourneyManageClassrooms.families.uploadedFamilies);
            $("#import_family .btn-import").fadeTo("fast",.3)
            JourneyManageClassrooms.api.families.saveImportedFamilies( JourneyManageClassrooms.families.uploadedFamilies )
                .then( e => { 

                    $("#manage-classroom-modal").hide();
                    modalState();

                    JourneyManageClassrooms.schoolDetails = ""; // clear school details to get fresh data from backend
                    JourneyManageClassrooms.families.list();
                    $("#import_family .btn-import").fadeTo("fast",1)
                    $(".families-import-message").html(`
                        <div class="message">
                            <div class="icon">
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 11.1151V12.0351C21.9988 14.1916 21.3005 16.2898 20.0093 18.017C18.7182 19.7441 16.9033 21.0076 14.8354 21.619C12.7674 22.2305 10.5573 22.157 8.53447 21.4097C6.51168 20.6624 4.78465 19.2812 3.61096 17.4722C2.43727 15.6632 1.87979 13.5232 2.02168 11.3715C2.16356 9.21971 2.99721 7.17147 4.39828 5.53222C5.79935 3.89296 7.69279 2.75053 9.79619 2.27529C11.8996 1.80005 14.1003 2.01748 16.07 2.89514M22 4.03514L12 14.0451L9.00001 11.0451" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="text">
                                You have successfully imported ${e.families.length} families
                            </div>
                        </div>
                    `);
                })
                .catch( e => {

                })
        }, 
    }, // end families : {}

    familyDashboard: {
        childUserId: 0,
        templates: {
            child : student => {
                let classRooms = ``;
                if (student.data && student.data.hasOwnProperty('classrooms')) {
                    student.data.classrooms.map( e => {
                        let bgColor = '';
                        e.category.map( cat => {
                            bgColor = cat.bg_color;
                        })
                        if(bgColor !='') classRooms += `<span style="background-color:${bgColor}" data-id="${e.ID}">${e.post_title}</span>`
                    })
                }
                
                let badges = ``;
                let badgeCount = 0;
                let badgeLeft = 0;
                if (student.data && student.data.hasOwnProperty('badges')) {
                    student.data.badges.map( e => {
                        if(badgeCount < 2){
                            badges += `<img src="${e.image}" title="${e.title}" />`
                        }else{
                            badgeLeft++;
                        }
                        badgeCount++;
                    })
                }

                if(badgeLeft > 0 ){
                    badges += `<div class="badges-left">+${badgeLeft}</div>`
                }

                return `
                    <div class="child-${student.ID} child child-item"> 
                        <div class="child-info">
                            <img src="${student.data.avatar_url}" class="avatar button-child-action" data-action="view_profile"  data-studentid="${student.ID}"/>
                            <div class="name-username">
                                <div class="name" t>${student.data.first_name} ${student.data.last_name}</div>
                                <div class="username">${student.data.user_login}</div>
                            </div>
                            <div class="actions">
                                <button type="button">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.00004 10C4.00004 10.3682 3.70156 10.6667 3.33337 10.6667C2.96518 10.6667 2.66671 10.3682 2.66671 10C2.66671 9.63181 2.96518 9.33333 3.33337 9.33333C3.70156 9.33333 4.00004 9.63181 4.00004 10Z" fill="#6B6F72"/>
                                    <path d="M10.6667 10C10.6667 10.3682 10.3682 10.6667 10 10.6667C9.63185 10.6667 9.33337 10.3682 9.33337 10C9.33337 9.63181 9.63185 9.33333 10 9.33333C10.3682 9.33333 10.6667 9.63181 10.6667 10Z" fill="#6B6F72"/>
                                    <path d="M17.3334 10C17.3334 10.3682 17.0349 10.6667 16.6667 10.6667C16.2985 10.6667 16 10.3682 16 10C16 9.63181 16.2985 9.33333 16.6667 9.33333C17.0349 9.33333 17.3334 9.63181 17.3334 10Z" fill="#6B6F72"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.00004 10C4.00004 9.63181 3.70156 9.33333 3.33337 9.33333C2.96518 9.33333 2.66671 9.63181 2.66671 10C2.66671 10.3682 2.96518 10.6667 3.33337 10.6667C3.70156 10.6667 4.00004 10.3682 4.00004 10ZM3.33337 12C2.2288 12 1.33337 11.1046 1.33337 10C1.33337 8.89543 2.2288 8 3.33337 8C4.43794 8 5.33337 8.89543 5.33337 10C5.33337 11.1046 4.43794 12 3.33337 12ZM10.6667 10C10.6667 9.63181 10.3682 9.33333 10 9.33333C9.63185 9.33333 9.33337 9.63181 9.33337 10C9.33337 10.3682 9.63185 10.6667 10 10.6667C10.3682 10.6667 10.6667 10.3682 10.6667 10ZM10 12C8.89547 12 8.00004 11.1046 8.00004 10C8.00004 8.89543 8.89547 8 10 8C11.1046 8 12 8.89543 12 10C12 11.1046 11.1046 12 10 12ZM17.3334 10C17.3334 9.63181 17.0349 9.33333 16.6667 9.33333C16.2985 9.33333 16 9.63181 16 10C16 10.3682 16.2985 10.6667 16.6667 10.6667C17.0349 10.6667 17.3334 10.3682 17.3334 10ZM16.6667 12C15.5621 12 14.6667 11.1046 14.6667 10C14.6667 8.89543 15.5621 8 16.6667 8C17.7713 8 18.6667 8.89543 18.6667 10C18.6667 11.1046 17.7713 12 16.6667 12Z" fill="#6B6F72"/>
                                    </svg>                                
                                </button>

                                <div class="submenu">
                                    <div class="submenu-overlay"></div>
                                    <ul>
                                        <li>
                                            <button type="button" class="button-child-action" data-studentid="${student.ID}" data-action="edit_student">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.0833 3.66671H3.66659C3.18036 3.66671 2.71404 3.85987 2.37022 4.20368C2.02641 4.5475 1.83325 5.01381 1.83325 5.50005V18.3334C1.83325 18.8196 2.02641 19.2859 2.37022 19.6297C2.71404 19.9736 3.18036 20.1667 3.66659 20.1667H16.4999C16.9861 20.1667 17.4525 19.9736 17.7963 19.6297C18.1401 19.2859 18.3333 18.8196 18.3333 18.3334V11.9167M16.9583 2.29171C17.3229 1.92704 17.8175 1.72217 18.3333 1.72217C18.849 1.72217 19.3436 1.92704 19.7083 2.29171C20.0729 2.65638 20.2778 3.15099 20.2778 3.66671C20.2778 4.18244 20.0729 4.67704 19.7083 5.04171L10.9999 13.75L7.33325 14.6667L8.24992 11L16.9583 2.29171Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                                <span>Edit</span>
                                            </button>
                                        </li>

                                        <li>
                                            <button type="button" class="button-child-action" data-studentid="${student.ID}" data-action="login">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.41675 15.5834L15.5834 6.41675M15.5834 6.41675H6.41675M15.5834 6.41675V15.5834" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span>Login As ${student.data.first_name}</span>
                                            </button>
                                        </li>

                                        <li>
                                            <button type="button" class="button-child-action" data-studentid="${student.ID}" data-action="change_password">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.41667 10.0833V6.41659C6.41667 5.20101 6.89955 4.03522 7.75909 3.17568C8.61864 2.31614 9.78442 1.83325 11 1.83325C12.2156 1.83325 13.3814 2.31614 14.2409 3.17568C15.1004 4.03522 15.5833 5.20101 15.5833 6.41659V10.0833M4.58333 10.0833H17.4167C18.4292 10.0833 19.25 10.9041 19.25 11.9166V18.3333C19.25 19.3458 18.4292 20.1666 17.4167 20.1666H4.58333C3.57081 20.1666 2.75 19.3458 2.75 18.3333V11.9166C2.75 10.9041 3.57081 10.0833 4.58333 10.0833Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span>Change Password</span>
                                            </button>
                                        </li>

                                        <li>
                                            <button type="button" class="button-child-action" data-studentid="${student.ID}" data-action="view_reports">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3.66675 17.8749C3.66675 17.2671 3.90819 16.6842 4.33796 16.2545C4.76773 15.8247 5.35063 15.5833 5.95841 15.5833H18.3334M3.66675 17.8749C3.66675 18.4827 3.90819 19.0656 4.33796 19.4954C4.76773 19.9251 5.35063 20.1666 5.95841 20.1666H18.3334V1.83325H5.95841C5.35063 1.83325 4.76773 2.07469 4.33796 2.50447C3.90819 2.93424 3.66675 3.51713 3.66675 4.12492V17.8749Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span>View Reports</span>
                                            </button>
                                        </li>
                                        
                                        <li>
                                            <button type="button" class="button-child-action" data-studentid="${student.ID}" data-action="view_achievement">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7.52575 12.7326L6.41659 21.0834L10.9999 18.3334L15.5833 21.0834L14.4741 12.7234M17.4166 7.33342C17.4166 10.8772 14.5437 13.7501 10.9999 13.7501C7.45609 13.7501 4.58325 10.8772 4.58325 7.33342C4.58325 3.78959 7.45609 0.916748 10.9999 0.916748C14.5437 0.916748 17.4166 3.78959 17.4166 7.33342Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span>View Achievements</span>
                                            </button>
                                        </li>

                                        <li>
                                            <button type="button" class="button-child-action" data-studentid="${student.ID}" data-action="view_quranic_animals">
                                                <img src="/wp-content/themes/buddyboss-theme-child/assets/img/animal.svg"/>
                                                <span>View Quranic Animals</span>
                                            </button>
                                        </li>
                                    </ul>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="classroom">
                            <b>Classrooms:</b> ${classRooms}
                        </div>
                        <div class="last-login">
                            <b>Last Login:</b> <span>${student.data.last_login}</span>
                        </div>
                        <div class="badges badges-${badges.length}">
                            <div class="img">${badges}</div>
                            <div class="button-conntainer">
                                <a href='?action=login_as_child&key=${student.data.login_key}' title="Login as ${student.data.first_name}">
                                    <span >Login as ${student.data.first_name}</span>
                                    
                                    <div class="svg-container">
                                        <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.66669 11.8334L11.3334 5.16675M11.3334 5.16675H4.66669M11.3334 5.16675V11.8334" stroke="#5D53C0" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                `
            }
        },

        children: () => {
            let skeletonLoader = ``;
            for(i = 0; i < 6; i++){
                skeletonLoader += `<div class="skeleton-loader child-item" ></div>`
            }
            $(".childrens-grid").html(`${skeletonLoader}`);
            JourneyManageClassrooms.api.school.getuserSchoolDetails({})
                .then( e => { 
                    console.log("familydashboard", e.students )
                    let childrenTpl = ``;

                    e.students.map( student => {
                        childrenTpl += JourneyManageClassrooms.familyDashboard.templates.child(student)
                    })

                    $(".childrens-grid").html(childrenTpl);
                })
                .catch( e => {
                    console.log("error families dashboard childrens list ")
                })
        },

        savePassword: () => {
            $(".btn-set-password").fadeTo("fast",.3);
            let data = {
                password: JourneyManageClassrooms.password,
                user_id: JourneyManageClassrooms.familyDashboard.childUserId
            };
            let studentInfo = JourneyManageClassrooms.students.getStudentById(JourneyManageClassrooms.familyDashboard.childUserId);

            JourneyManageClassrooms.api.user.savePassword(data)
                .then( e => {
                    $(".btn-set-password").fadeTo("fast",1);
                    let modalContent = `
                        <div class="success-update-password">
                            <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect width="120" height="120" fill="url(#pattern0)"/>
                            <defs>
                            <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_3210_27590" transform="scale(0.00666667)"/>
                            </pattern>
                            <image id="image0_3210_27590" width="150" height="150" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAYAAAA8AXHiAAAAAXNSR0IArs4c6QAACl1JREFUeF7tnU1MFVcUx6+pKSR9pCaQ+FQSXyOKiSIaSKyAra6QhW4qNrEboYtuCnYr7VZYq6smVVY1qbCSVGWlrYbUBCKIJqKY0gQRE0g00kSMDc1/dOL44PHm69zPczevlpn7cc5vzjn3zL131iwtLS0JLiyBlCWwhsFKWaJcnScBBotBIJEAg0UiVq6UwWIGSCTAYJGIlStlsJgBEgkwWCRi5UoZLGaARAIMFolYuVIGixkgkQCDRSJWrpTBYgZIJMBgkYiVK2WwmAESCTBYJGLlShksZoBEAgwWiVi5UgaLGSCRAINFIlaulMFiBkgkwGCRiJUrZbDeMTC/MCleLs4K7/fVU/Hy1ewHdMw8v+P9e+O6PR/8/7LSrCgr3SDKM1WirCTr/XJxdDMFoHn6YlQAlpnnox5IaRaAtnHdbg/CDZ/uFoDPteKMxQJAD59dJQGpGDQ+aNvWt3jAuVCsBguWafxJv5iau5m6VYoLByDLVewXNZuOWm3JrATr4bNr4u50n5hfeBRX/1Lug6uszraIbesPSWlPZiNWgQWghqd6tbFOYRUJK1afa7MKMCvAMhWofPBsAsxosBBD3Zjo8WZ3NhW4yAPVp4yOwYwE6/WbBS8oH566YBNPy8ZSn2v3gvyP12aMG6dxYCGBOXj/R+PiqLhkwD027zhtXOLVKLBgpYYmz8bVkdH3NVR1etbLlGIEWHB91yd6vHyUywX5r4PVp4xwjdqD5ZrrK/bgmOIatQYLUF0e6xSwWFzeSwDB/JHas1rHXdqChXd7g/e7GKoCT5TucGkJ1sTsVS8/xaW4BJDvwmsh3Yp2YMFSDYx16iYnrftztO6Cdm5RK7A4porHr45uURuwGKp4UPl36QaXFmBh1tc/8q0z2fRkCBW+uzyzVRypPaNFnksLsPCKxvXkZ1qwIYmKV0Cqi3Kwxqf7xNDjc6rlYFX7DVs6RE1lq9IxKQULcVX/SLtSAdjauOqZojKwOK6iRRqvfo7WnVcWbykDC0uIR/7ppZWu47XXbW7zljyrKErAYhcoT9XH915SshJVCVgDYyetW04sD5VoLWGZ8+HaM9FuSuFq6WDxe8AUtBaxiuYd3SJX0RTxrmSXSwfr4u2vORFaQGdIcObKm7yAG5Mb7Iucmr+VTMNCeGdLHN/7W+J6olQgFSy2VoVVs29Lh9i1Qu4preVDsldBSAWLrdXKYBVT+tzCI4G4NMmCR9lWSxpYbK3iQeXflUZ6RmasJQ0svGTW/SyFKDFEGtcWs1TBNnDUEix+kiJzhigFLOxYvnj7WBKZWHdvFKj8wf/8xxeJ5SArryUFrKHJc2L8SV9iodhSQRyoMPY0wKrZ1CoaqjrIRSkFLA7a3+sxLlRTc7e8zSVJi6wgnhystASSVKA63B8XKvR9YPSkmHmRzuEnh2vPkp8sSA7W9Qc93hGNrpckUKUxIwzKX4Y7JAeL3aDwjiSKu0VrYvaauDHRnepzKcMdkoLFs0H9oPIJpZ4dkoLlelJUN0sVNHtJ+hbGfJKC5XJ8lURxFO4vHwYcDX5w+6kwjMS6hhQsV+Mr3aECKdRxFilYaST0Yj0uCm8yASpfPN99+SeZpMjAcnH5sUlQgSjKnTxkYLmWGDUNKoBFudqBDKy0k3pkNjuFik2ECsOm3MVDBpYrM0JToQJYlDNDMrAuj3Z6n26zuZgMFfSCT94d2U1zCjWDFZN806FisGIqnvI2G6BisCgJiVG3LVAZCxZlchRZ47rNJ7w1Rfhv7GJBegOrVJPsZCnGmU1QYazYv9jWeKXYsGP9nSzGogKren2L2Ff1vShZW7ZswNhwMHj/J5JNG7ZB5YH1UUa0NRkGFsWsENbpq7pfVoTKp2zxzUsxMPZDqnDZCJWxrpACrAPVXaI6W/wzt2nCZStUDFbA4Z1o/H1VaxX0jWnAZTNUDFaAlqhv45PAZTtUxoJF8UrnROMVURLxa6Nx4HIBKmNf6VC8hA4bY+VPF6PA5QpUxr6Eplg283ZWeD6y1YIQw8DlElSQiZHLZqgW+uFwMhx9GNUlFoPLNaggDyMX+qHjVEnStOFyESroJ+pkKEoKnizzjk78+tcxsbA4G6U/oa9NCy5XocqUZMU3n18KLe+oF5KCRTEzDA4wKVw4hvGziv1RZeZdL2OLVqyOhbyJcpEfukAKlowNq0ngCqmDZZeZDhUGlMRSh5EbKViyttjLhMsGqACG0VvsqeOstNximCfQBvfnj5M6viJ3hWiAOs6SBZctlgrysuIYI4pE6WoWhsIt2gQVZGfFwWsy3aEPXJpw2QaVDDcoxRWiERWH26YBl21QyXKD0sCSNTvMd5FJ4LIRKhmzQV8HpOmGoKL7h9vF/L+TYSdgqV0XBy5boaLcoJqvMGlgyUiWFqIxCly2QgXZUK5mUAaWiiA+airCZqhkBe3SXeHbBONVcWOiJzU3F7UiWK7mHaeXfcp28c2CGJnqtfrrGdSvcJRaLNVWyx889iZmSrPemi5MLPCxSexJtLXItlbSZoVBham2WrbCs9q4ZMZWSlyh3yjFnkMXgQkzZpkzwWB/pM0Kg42qymuFUYRt11CvYigkLyVgoTMUu3hsgyLpeCiPgizWN2Vg4VSYvuF2sqXLxQZu+98RsLfWX/BOlFFRlIGFwVLt5FEhSN3apNyBE2asSsFCB8en+8TQ43Nh+srXhJRAw5YOUVPZGvJqmsuUg4VhDd7r8nJJXJJLIFfeJJp3pvsZuji90gIsjrfiqG75PeWfVHmnIKuKq5SnG1YSI+It5Lde/7eQjpQdqwWn8wGq8kyVFiPXwmL5kmC44jGhG1QYhVZgoUPYRDow1hlPwo7epXoGuJLYtQMLneT3ieGfENmrFsL2TEuwfMuF2SLHXCurUkf3p2XwzgF9WFvw9hhtnQJ1Y1xhsKMI6K/d6+JXP++EgpTCwe1d2sz+Cj0O2rrCYIeR57r+oNv5JCqSn4BKhzxVMftqBFj+IFx+/aPDa5piMBkTYxWKu1xyjVilcGhnt/auL19XRlksv/NwjXen+8TIP71RHiLjrsV6ql2VrUa4PivA8geBlaiIvWz7kiuWEyOWKivNGvcw+B020mLlSxsJVaxIpTrvVJZ24fYaqzpFrqJJVpNk7VgBli8dUwEDUPW5NlGdbSFTtOyKrQIrCBgg091FwuXtqjxmhYWyKsYq9hQiBkOK4u+5m9q4SVgnnNSMFZ4mx1DFZG+lxVpp0Fg1ASs28/yOdMgA08Z1ezxXh88Nu1CcASuoTFgyAAbYKEDzQQJEAMpmy2T0Kx0ZTzjeSQI4fLgcv/lnOeDvKPkrNPHhKIBTkdnq/eqyglOGzFZrw0mLpVroLrTPYLmgZQVjZLAUCN2FJhksF7SsYIwMlgKhu9Akg+WClhWMkcFSIHQXmmSwXNCygjEyWAqE7kKTDJYLWlYwRgZLgdBdaJLBckHLCsbIYCkQugtNMlguaFnBGBksBUJ3oUkGywUtKxgjg6VA6C40yWC5oGUFY2SwFAjdhSYZLBe0rGCMDJYCobvQJIPlgpYVjPF/B2EGqKye+F8AAAAASUVORK5CYII="/>
                            </defs>
                            </svg>
                        
                            <h2>Password has been<br/>successfully updated!</h2>
                            <p>You have successfully updated the<br/>password of ${studentInfo.data.display_name}.</p>
                        </div>
                    `;
                    $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
                })
                .catch( e => {
                    console.log(e)
                } );
        },
    }, // end familyDashboard
}

// autoloaders
$(document).ready(e => {

    switch(manageClassroomsJs.post_slug){
        case "manage-classroom-teachers":
            JourneyManageClassrooms.teachers.list();
            break;
        case "manage-classroom-admins":
            JourneyManageClassrooms.admins.list();
            break;
        case "manage-classroom-students": case "manage-family":
            JourneyManageClassrooms.students.list();
            break;
        case "manage-institute-families":
            JourneyManageClassrooms.families.list();
            break;
        case "family-parent-dashboard":
            JourneyManageClassrooms.familyDashboard.children();
            break;
        default: 
            //JourneyManageClassrooms.api.school.getuserSchoolDetails({})
            JourneyManageClassrooms.classrooms.reloadReloadables();
            break;
        
    }
});


// event listeners
$(document).on("click",".classroom .classroom-options .overlay, .institute-details .classroom-options .overlay", e => {
    // console.log()
    $(".classroom-options.active").removeClass("active");
});

$(document).on("click",".classroom .cog, .institute-details .item.settings svg", e => {
    $(e.currentTarget).parent().find(".classroom-options").addClass("active");
});

$(document).on("click",".list-item .list-item-action-button", e => {
    $(e.currentTarget).parent().find(".submenu").addClass("active");
});

$(document).on("click",".submenu-overlay", e => {
    // console.log()
    $(".submenu.active").removeClass("active");
});

$(document).on("keyup", ".search-box .search",e => {
    let searchKey = $(e.currentTarget).val().toLowerCase();
    let searchType = $(e.currentTarget).attr("data-type")
    switch(searchType){
        case "teachers":
            JourneyManageClassrooms.teachers.searchKey = searchKey;
            JourneyManageClassrooms.teachers.dataTable.search(searchKey).draw();
        break;
        case "students":
            JourneyManageClassrooms.students.searchKey = searchKey;
            JourneyManageClassrooms.students.dataTable.search(searchKey).draw();
        break;
        case "admins":
            JourneyManageClassrooms.admins.searchKey = searchKey;
            JourneyManageClassrooms.admins.dataTable.search(searchKey).draw();
        break;
        case "families":
            JourneyManageClassrooms.families.searchKey = searchKey;
            JourneyManageClassrooms.families.dataTable.search(searchKey).draw();
        break;
        default: return false; break;
    }
});

$(document).on("click",".button-teacher-action, .button-student-action, .button-admin-action, .button-families-action", e => {
    let actiontype = $(e.currentTarget).attr("data-action")
    let modalUserId = 0;
    let modalUserDetails = "";

    switch(manageClassroomsJs.post_slug){
        case "manage-classroom-teachers": 
            modalUserId = $(e.currentTarget).attr("data-teacherid");
            modalUserDetails = JourneyManageClassrooms.teachers.getTeacherById(modalUserId);
            break;
        case "manage-classroom-admins":
            modalUserId = $(e.currentTarget).attr("data-adminid");
            modalUserDetails = JourneyManageClassrooms.admins.getAdminById(modalUserId);
            break;
        case "manage-classroom-students": case "manage-family":
            modalUserId = $(e.currentTarget).attr("data-studentId");
            modalUserDetails = JourneyManageClassrooms.students.getStudentById(modalUserId); 
                break;
        
    }
    $("#manage-classroom-modal").attr("class",`modal manage-classroom-modal ${actiontype}`);

    switch(actiontype){
        case "add_to_classroom": case "add_to_classroom_student":

            $("#manage-classroom-modal").show().css({"display":"flex"});

            classrooms = ``;
            JourneyManageClassrooms.schoolDetails.classrooms.map( classroom => {
                classrooms += `
                    <li>${classroom.post_title}</li>
                `
            })

            modalContent = `
            <div class="teacher-add-class-container">
                <h2>Add to Classroom</h2>
                <div class="teacher-details">
                    <img src="${modalUserDetails.data.avatar_url}" class="avatar"/>
                    <span>${modalUserDetails.data.first_name} ${modalUserDetails.data.last_name}</span>
                </div>

                <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">

                    ${
                        ( actiontype=="add_to_classroom_student" )
                        ? 
                        `
                            <input type="hidden" name="tab" value="student-add-single" />
                            <input type="hidden" name="student_id" value="${modalUserDetails.data.ID}" />
                        `
                        :
                            `
                            <input type="hidden" name="tab" value="teacher-add-single" />
                            <input type="hidden" name="teacher_id" value="${modalUserDetails.data.ID}" />
                        `
                    }

                    
                    <div class="classroom-container">
                        <label>Classroom</label>
                        <input type="text" class="search-classroom" placeholder="Type/Select Classroom" />
                        <input type="hidden" name="classroom_id" value="" />

                        <div class="classrooms-list-container">
                            <div class="overlay"></div>
                            <div class="">
                                <ul>
                                    ${classrooms}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="button-container">
                        <button type="button" class="btn-back">BACK</button>
                        <button type="submit" class="btn-save btn-add">ADD</button>
                    </div>
                </form>
            </div>
            `;

            

        break; // add_to_classroom

        case "add_to_family":
            $("#manage-classroom-modal").show().css({"display":"flex"});
            JourneyManageClassrooms.students.studentSelectedToFamily = JourneyManageClassrooms.students.getStudentById(modalUserId); 

            modalContent = `
            <div class="teacher-add-class-container add-to-family">
                <form id="frm-add-to-family">
                    <input type="hidden" name="students[]" value="${modalUserDetails.data.ID}"/>
                    <h2>Add to Family</h2>
                    <div class="teacher-details student-details">
                        <img src="${modalUserDetails.data.avatar_url}" class="avatar"/>
                        <span>${modalUserDetails.data.first_name} ${modalUserDetails.data.last_name}</span>
                    </div>
                    <div class="family-details">
                        <label>Family Name</label>
                        <input type="text" class="search-family" placeholder="e.g Doe"/>
                        <div class="search-result"></div>
                        <div class="family-details-table"></div>
                    </div>

                    <button type="button" class="btn-add-to-family" disabled>Add to Family</button>
                </form>
            </div>
            `;
        break; // add_to_family

        case "remove_teacher": case "remove_student":
            $("#manage-classroom-modal").show().css({"display":"flex"});

            modalContent = `
            <div class="teacher-remove">
                <h2>Confirmation</h2>
                <div class="teacher-details">
                    <img src="${modalUserDetails.data.avatar_url}" class="avatar"/>
                    <span>${modalUserDetails.data.first_name} ${modalUserDetails.data.last_name}</span>
                </div>
                <div class="text">
                    Are you sure you want remove<br/>this ${(actiontype=="remove_student") ? `student`:`teacher`}?
                </div>
                <div class="button-container">
                    <button type="button" class="btn-remove" 
                        data-userid="${modalUserDetails.data.ID}" 
                        data-classroomid="${JourneyManageClassrooms.schoolDetails.parent_school.school_id}" 
                        data-type="${(actiontype=="remove_student") ? `student`:`teacher`}">Remove</button>
                    <button type="button" class="btn-back">BACK</button>
                </div>
            </div>
            `;

        break;

        case "remove_admin":
            $("#manage-classroom-modal").show().css({"display":"flex"});

            modalContent = `
            <div class="teacher-remove">
                <h2>Confirmation</h2>
                <div class="teacher-details">
                    <img src="${modalUserDetails.data.avatar_url}" class="avatar"/>
                    <span>${modalUserDetails.data.first_name} ${modalUserDetails.data.last_name}</span>
                </div>
                <div class="text">
                    Are you sure you want remove<br/>this admin?
                </div>
                <div class="button-container">
                    <button type="button" class="btn-remove" 
                        data-userid="${modalUserDetails.data.ID}" 
                        data-classroomid="${JourneyManageClassrooms.schoolDetails.parent_school.school_id}" 
                        data-type="admin">Remove</button>
                    <button type="button" class="btn-back">BACK</button>
                </div>
            </div>
            `;

        break;

        case "edit_teacher": case "edit_student":
            $("#manage-classroom-modal").show().css({"display":"flex"});

            modalContent = `
            <div class="teacher-edit-container">
                <h2>${(actiontype == "edit_student") ?  (manageClassroomsJs.post_slug=="manage-family") ? `Update Child`:`Update Student` :`Update Teacher`} </h2>

                <form onsubmit="JourneyManageClassrooms.users.saveEdits(this); return false;">
                    <input type="hidden" name="userid" value="${modalUserDetails.data.ID}" />
                    <input type="hidden" name="type" value="${actiontype}" />
                    <div class="field-group">
                        <label class="field-label">Name</label>
                        <div class="field-input fullname">
                            <input type="text" name="first_name" required placeholder="First Name" value="${modalUserDetails.data.first_name}"/>
                            <input type="text" name="last_name" required placeholder="Last Name" value="${modalUserDetails.data.last_name}"/>
                        </div>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Email</label>
                        <div class="field-input">
                            <input type="email" name="email" type="email" ${(actiontype == "edit_student") ? ``:`required`}  placeholder="Email Address" value="${modalUserDetails.data.user_email}"/>
                        </div>
                    </div>

                    ${
                        (actiontype == "edit_student") ? 

                        `
                        <!--
                        <div class="field-group student-add">
                            <label class="field-label">Date of Birth</label>
                            <div class="field-input">
                                <input name="date_of_birth" id="date_of_birth" type="text" placeholder="Date of Birth" value="${modalUserDetails.data.date_of_birth}"/>
                            </div>
                        </div>
                        <div class="field-group student-add">
                            <label class="field-label">Family ID</label>
                            <div class="field-input">
                                <input  name="family_id" type="text" placeholder="Family ID" value="${modalUserDetails.data.family_id}"/>
                            </div>
                        </div>

                        -->
                        
                        <div class="field-group">
                            <label class="field-label">Gender</label>
                            <div class="field-input gender">
                                <label>
                                    <input type="radio" name="gender" required value="male" ${(modalUserDetails.data.gender=="male") ? `checked`:``} /> Male
                                </label>
                                <label>
                                    <input type="radio" name="gender" required value="female" ${(modalUserDetails.data.gender=="female") ? `checked`:``}/> Female
                                </label>
                            </div>
                        </div>
                        
                        <div class="field-group">
                            <label class="field-label">Username</label>
                            <div class="field-input">
                                <input type="text"   value="${modalUserDetails.data.user_login}" readonly disabled/>
                                <input type="hidden" name="username"  value="${modalUserDetails.data.user_login}"/>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Password</label>
                            <div class="field-input password">
                                <input name="password" type="password"  placeholder="Password" value="" class="input-password"/>
                                <button type="button" class="btn-view-password"></button>
                            </div>
                        </div>
                        `
                        :
                        ``
                    }

                    <div class="button-container">
                        <button type="button" class="btn-back">CANCEL</button>
                        <button type="submit" class="btn-save">SAVE</button>
                    </div>
                    
                </form>
            </div>
            `;
            
        break;

        case "add_teacher": case "add_student": 
            $("#manage-classroom-modal").show().css({"display":"flex"});

            classroomID = JourneyManageClassrooms.schoolDetails.catch_all_school_data.school_id;
            if( manageClassroomsJs.post_slug == "manage-family" ){
                classroomID = JourneyManageClassrooms.schoolDetails.school.learndash_parent_group_id;
            }

            

            if (
                JourneyManageClassrooms &&
                JourneyManageClassrooms.schoolDetails &&
                JourneyManageClassrooms.schoolDetails.catch_all_school_data &&
                JourneyManageClassrooms.schoolDetails.catch_all_school_data.school_id !== undefined
              ) {
                teacherClassroomId = JourneyManageClassrooms.schoolDetails.catch_all_school_data.school_id;
            } else {
                JourneyManageClassrooms.schoolDetails.classrooms.map( classroom => {
                    if (classroom && classroom.school_data && classroom.school_data.meta && classroom.school_data.meta.is_teachers_classroom && classroom.school_data.meta.is_teachers_classroom[0] !== undefined) {
                        if( classroom.school_data.meta.is_teachers_classroom[0] == "yes"){
                            teacherClassroomId = classroom.ID;
                        }
                    }
                })
            }

            modalContent = `
            <div class="teacher-add-container ${manageClassroomsJs.post_slug}">
                
                ${
                    (actiontype == "add_student") ? 
                    `
                        <div class="nav-tabs">
                            <button type="button" class="active" data-target="#add_student">
                                ${(manageClassroomsJs.post_slug == "manage-family") ? `Add Child`:`Add Student`}
                            </button>
                            <button type="button" data-target="#import_students">Import Students</button>
                        </div>

                        <div class="tabs">
                            <div class="tab active" id="add_student">
                                <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                                    <input type="hidden" name="tab" value="student-add" parentchild/>
                                    <input type="hidden" name="classroom_id" value="${classroomID}" data-catchall=""/>
                                    <input type="hidden" name="remaining_seat" value="${JourneyManageClassrooms.classrooms.statistics.remainingSeats}" />
                                    ${JourneyManageClassrooms.students.templates.addForm()}
                                    <div class="div-student-add-error-message"></div>
                                    <div class="button-container">
                                        <button type="submit" class="btn-add btn-add-teacher" ${(JourneyManageClassrooms.classrooms.statistics.remainingSeats <= 0) ? `disabled`:``}>Add</button>
                                    </div>
                                </form>

                                
                            </div>

                            

                            <div class="tab" id="import_students">
                                
                                <div class="top">
                                    <h2>Import Students</h2>
                                    <span class="seats-available">Available Seats:  <span class="num">${JourneyManageClassrooms.classrooms.statistics.remainingSeats}</span><span>
                                </div>

                                <div class="download">
                                    <span>Import students using our template.</span>
                                    <a href="/wp-content/themes/buddyboss-theme-child/page-templates/manage-classrooms/UsersDemoStudents.csv" download>Download Template</a>
                                </div>

                                <div id="import-students-dropzone" class="dropzone" action="?" ></div>
                                <div class="imported-students-container"></div>
                                <button type="button" class="btn-import">IMPORT</button>
                                
                            </div>
                        </div>
                    `
                    :
                    `
                        <div class="nav-tabs">
                            <button type="button" class="active" data-target="#add_teacher">Add Teacher</button>
                            <button type="button" data-target="#import_teachers">Import Teachers</button>
                        </div>

                        <div class="tabs">
                            <div class="tab active" id="add_teacher">
                                <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                                    <input type="hidden" name="tab" value="teacher-add" />
                                    <input type="hidden" name="classroom_id" value="${teacherClassroomId}" />
                                    <div class="field-group">
                                        <label class="field-label">Teacher Name</label>
                                        <div class="field-input fullname">
                                            <input type="text" name="first_name" required placeholder="First Name"/>
                                            <input type="text" name="last_name" required placeholder="Last Name"/>
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <label class="field-label">Teacher Email</label>
                                        <div class="field-input">
                                            <input type="email" name="email" type="email" required placeholder="Email Address"/>
                                        </div>
                                    </div>
                                    
                                    <div class="button-container">
                                        <button type="submit" class="btn-add">Add</button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab" id="import_teachers">
                                <div class="top"><h2>Import Teachers</h2></div>

                                <div class="upload-form">
                                    <div class="download">
                                        <span>Import teachers using our template.</span>
                                        <a href="/wp-content/plugins/learndash-classroom/data/UsersDemo.csv" download>Download Template</a>
                                    </div>

                                    <div id="import-teachers-dropzone" class="dropzone" action="?" ></div>

                                    <button type="button" class="btn-import">IMPORT</button>
                                </div>

                                <div class="imported-teachers">   </div>

                            </div>
                        </div>
                    `
                }
            
            </div>
            `;
        break;

        case "add_admin":
            $("#manage-classroom-modal").show().css({"display":"flex"});

            modalContent = `
                <div class="manage-classroom-container ">
                    <h2>Add Admin</h2>

                    <form class="form-add-admin" onsubmit="JourneyManageClassrooms.admins.saveAdmin(this); return false;">
                        <div class="field-group">
                            <div class="field-heading">
                                <label class="field-label">Select Teacher</label>
                            </div>

                            <div class="field-input search-teacher-container">
                                <input type="text" name="search_teacher"  placeholder="Type/Select Teacher" autocomplete="off">
                                <input type="hidden" name="search_teacher_id" class="search_teacher_id" value="0"/>
                                <div class="search-teacher-result">
                                </div>
                            </div>

                        </div>
                        <div class="selected-teachers-for-admin"></div>

                        <button type="submit" class="btn-add-admin">ADD AS ADMIN</button>
                    </form>
                </div>
            `
        break;

        case "add_family":
            $("#manage-classroom-modal").show().css({"display":"flex"});
            $("#manage-classroom-modal").removeClass("success_add_student");
            JourneyManageClassrooms.families.selectedStudents = [];
            modalContent = `
                <div class="manage-families-container ">
                    <h2>Add Family</h2>

                    <form class="form-add-family" onsubmit="JourneyManageClassrooms.families.saveFamily(this); return false;">
                        <input type="hidden" name="institute_id" value="${manageClassroomsJs.selectedInstituteId}" />
                        ${JourneyManageClassrooms.families.templates.addTabs(0)}
                    </form>
                </div>
            `
            break;

        case "broadcast_email":
            $("#manage-classroom-modal").show().css({"display":"flex"});

            let broadcastType = ( typeof $(e.currentTarget).attr("data-broadcasttype") != "string" ) ? "":$(e.currentTarget).attr("data-broadcasttype");
            let broadcastUserId = ( typeof $(e.currentTarget).attr("data-userid") != "string" ) ? "":$(e.currentTarget).attr("data-userid");
                
            let buttonBroadcastAttr = "";

            let familyId = 0;

            switch(broadcastType){
                case "teachers":
                    broadcastText = "This email will be sent to all teachers of the classroom";
                    break;
                case "students":
                    broadcastText = "This email will be sent to all students of the classroom";
                    break;
                case "single_user":
                    let targetUser = "";
                    JourneyManageClassrooms.schoolDetails.students.map( e => {
                        if(e.ID == broadcastUserId){
                            targetUser = e;
                        }
                    })
                    JourneyManageClassrooms.schoolDetails.teachers.map( e => {
                        if(e.ID == broadcastUserId){
                            targetUser = e;
                        }
                    })
                    

                    if( JourneyManageClassrooms.isValidEmail( targetUser.data.user_email ) ){
                        broadcastText = "This email will be sent to " +  targetUser.data.user_email;
                    }else{
                        broadcastText = "An email address was not found for this user";
                        buttonBroadcastAttr = "disabled";
                    }

                    break;
                case "families": case "family_single":
                    broadcastText = "This email will be sent to all parents of the family";
                    familyId = $(e.currentTarget).attr('data-familyid')
                    break;
                default:
                    broadcastText = "This email will be sent to all teachers and students of the school";
                    break;
            }

            modalContent = `
            <div class="broadcast-email-container">
                <h2>Broadcast Email</h2>
                <p>${broadcastText}</p>
                
                <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                    <input type="hidden" name="tab" value="email-classroom" />
                    <input type="hidden" name="classroom_type" value="parent" />
                    <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.schoolDetails.parent_school.school_id}" />
                    <input type="hidden" name="type" value="${actiontype}"/>
                    <input type="hidden" name="broadcast_type" value="${broadcastType}"/>
                    <input type="hidden" name="to_user_id" value="${broadcastUserId}"/>
                    <input type="hidden" name="family_id" value="${familyId}"/>

                    <div class="field-group">
                        <label class="field-label">Subject</label>
                        <div class="field-input">
                            <input type="text" name="subject" required placeholder="Subject"/>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Body</label>
                        <div class="field-input">
                            <textarea required name="body"></textarea>
                        </div>
                    </div>
                    
                    <div class="button-container">
                        <button type="button" class="btn-back">Cancel</button>
                        <button type="submit" class="btn-save" ${buttonBroadcastAttr} >Send</button>
                    </div>
                </form>
            </div>
            `;
        break;
    }

    $("#manage-classroom-modal .modal-content .main-content").html(modalContent);


    if( actiontype == "add_teacher"){
        JourneyManageClassrooms.teachers.renderDropzone();
    }
    if( actiontype == "add_student"){
        JourneyManageClassrooms.students.renderDropzone();
    }
    if( actiontype == "add_family"){
        JourneyManageClassrooms.families.renderDropzone();
    }

    modalState();

    // initialize dob datepicker
    let dobPicker = new Pikaday({
        field: document.getElementById("date_of_birth"),
        format: 'YYYY-MM-DD'
    });

});


$(document).on("keyup","#manage-classroom-modal .search-classroom", e => {
    let searchClassroomKey = $(e.currentTarget).val();
    console.log("searchClassroomKey", searchClassroomKey)

    $("#manage-classroom-modal .classrooms-list-container").addClass("active")
    classrooms = ``;
    JourneyManageClassrooms.schoolDetails.classrooms.map( classroom => {
        if (classroom.post_title.toLowerCase().includes(searchClassroomKey.toLowerCase())) {
            classrooms += `
                <li class="classroom-name" data-id="${classroom.ID}">${classroom.post_title}</li>
            `
        }
    })
    if(classrooms == ""){
        classrooms = '<li class="no-records-found">No records found</li>';
    }

    $(".classrooms-list-container ul").html(classrooms);
    
})

$(document).on("click","#manage-classroom-modal .classrooms-list-container .overlay", e => {
    $(".classrooms-list-container").removeClass("active")
});

$(document).on("click","#manage-classroom-modal .classrooms-list-container li.classroom-name", e => {
    classRoomTitle = $(e.currentTarget).text();
    $(".classrooms-list-container").removeClass("active")
    $("#manage-classroom-modal .classroom-container .search-classroom").val(classRoomTitle);
    $("#manage-classroom-modal .classroom-container input[name=classroom_id]").val($(e.currentTarget).attr("data-id"));
});


$(document).on("click","#manage-classroom-modal .btn-back, #manage-classroom-modal .btn-import.btn-student-submit", e => {
    $("#manage-classroom-modal").hide();
    modalState();
})



$(document).on("click",".nav-tabs a, .nav-tabs button", e => {
    e.preventDefault();
    
    if($(e.currentTarget).hasClass("disabled")){
        return false
    }else{

        let target = $(e.currentTarget).attr("data-target");
        let parentNav = $(e.currentTarget).parent();
        let closestTab = $(e.currentTarget).parent().parent().find("> .tabs");

        $(closestTab).find("> .tab").removeClass("active");
        $(closestTab).find(`> .tab${target}`).addClass("active");
    
        $(parentNav).find("a,button").removeClass("active");
        $(e.currentTarget).addClass("active")
    }
});

$(document).on("mouseover",".steps-nav", function(){
    $(".steps-nav > a" ).addClass("disabled","disabled")
})
$(document).on("mouseout",".steps-nav", function(){
    $(".steps-nav > a" ).removeClass("disabled")
})


/* classroom event listeners */
$(document).on("keyup", ".search-classroom", e => {
    JourneyManageClassrooms.classrooms.searchKey = $(e.currentTarget).val();
    JourneyManageClassrooms.classrooms.search();
});

$(document).on("keyup", ".search-institute", e => {
    JourneyManageClassrooms.institutes.searchKey = $(e.currentTarget).val();
    JourneyManageClassrooms.institutes.search();
});

$(document).on("click",".classroom-action", e => {
    let schoolid = $(e.currentTarget).attr("data-schoolid");
    let action = $(e.currentTarget).attr("data-action");
    $("#manage-classroom-modal").attr("class",`modal manage-classroom-modal ${action}`);
    JourneyManageClassrooms.classrooms.getById(schoolid)

    console.log("action", action)

    switch(action){
        case "archive-classroom":
            $(e.currentTarget).fadeTo("fast",.3)
            JourneyManageClassrooms.api.school.classroom.archiveClassroom( JourneyManageClassrooms.classrooms.current.ID )
                    .then( e => {
                        $(".classroom-options.active").removeClass("active");
                        JourneyManageClassrooms.classrooms.reloadReloadables();
                    })
                    .catch( e => {

                    })
            return false;
        break;

        case "broadcast_email":
          
            modalContent = `
                <div class="broadcast-email-container">
                    <h2>Email Classroom</h2>
                    <p>This email will be sent too all teachers and students of the classroom</p>
                    
                    <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                        <input type="hidden" name="tab" value="email-classroom" />
                        <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.classrooms.current.ID}" />
                        
                        <div class="field-group">
                            <label class="field-label">Subject</label>
                            <div class="field-input">
                                <input type="text" name="subject" required placeholder="Subject"/>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Body</label>
                            <div class="field-input">
                                <textarea required name="body" ></textarea>
                            </div>
                        </div>
                        
                        <div class="button-container">
                            <button type="button" class="btn-back">Cancel</button>
                            <button type="submit" class="btn-save">Send</button>
                        </div>
                    </form>
                </div>
                `;
                
            break;

        case "tab-classroom-settings":
                modalContent = JourneyManageClassrooms.classrooms.templates.manageClassroom.settings();
            break;

        default:

            defaultTab = $(e.currentTarget).attr("data-tab")

            modalContent = `
                <div class="manage-classroom-container">
                    <h2>Manage Classroom</h2>
                    <div class="nav-tabs">
                        <a href="" data-target="#tab-classroom" class="${(defaultTab=="tab-classroom") ? `active`:``}" >Classroom</a>
                        <a href="" data-target="#tab-courses" class="${(defaultTab=="tab-courses") ? `active`:``}">Courses</a>
                        <a href="" data-target="#tab-teachers" class="${(defaultTab=="tab-teachers") ? `active`:``}">Teachers</a>
                        <a href="" data-target="#tab-students" class="${(defaultTab=="tab-students") ? `active`:``}">Students</a>
                    </div>

                    <div class="tabs">
                        <div class="tab ${(defaultTab=="tab-classroom") ? `active`:``} " id="tab-classroom">
                            ${JourneyManageClassrooms.classrooms.templates.manageClassroom.tabClassroom()}
                        </div> <!-- #tab-classroom -->

                        <div class="tab ${(defaultTab=="tab-students") ? `active`:``} " id="tab-students">
                            ${JourneyManageClassrooms.classrooms.templates.manageClassroom.tabStudents()}
                        </div> <!-- #tab-students -->

                        <div class="tab ${(defaultTab=="tab-teachers") ? `active`:``} " id="tab-teachers">
                            ${JourneyManageClassrooms.classrooms.templates.manageClassroom.tabTeachers()}
                        </div> <!-- #tab-teachers -->

                        <div class="tab ${(defaultTab=="tab-courses") ? `active`:``} " id="tab-courses">
                            ${JourneyManageClassrooms.classrooms.templates.manageClassroom.tabCourses()}
                        </div> <!-- #tab-courses -->

                    </div>
                </div>
            `
            
        break;
    }

    $("#manage-classroom-modal").show().css({"display":"flex"});
    $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
    modalState();

    // initialize dob datepicker
    let dobPicker = new Pikaday({
        field: document.getElementById("date_of_birth"),
        format: 'YYYY-MM-DD'
    });

})

$(document).on("click",".btn-add-courses-to-active", e => {
    // .select-available-courses
    //JourneyManageClassrooms.classrooms.current.school_data.courses
    //manageClassroomsJs.availableGroupCourses
    let targetElement = ""
    if( jQuery("#manage-classroom-add-new").is(":visible") ){
        targetElement = $("#manage-classroom-add-new #select-available-courses option:selected");
    }else{
        targetElement = $("#manage-classroom-modal #select-available-courses option:selected");
    }

    targetElement.each( function(){
        courseid = $(this).val()

        manageClassroomsJs.availableGroupCourses.map( categoryGroup => {
            categoryGroup.courses.map( course => {  
                if(course.ID == courseid){
                    JourneyManageClassrooms.classrooms.current.school_data.group_courses.push(course)
                }
            });
        })
    });
    
    if( jQuery("#manage-classroom-add-new").is(":visible") ){
        $("#manage-classroom-add-new #class-new-manage-courses").html( JourneyManageClassrooms.classrooms.templates.manageClassroom.tabCourses() )
    }else{
        $("#tab-courses").html( JourneyManageClassrooms.classrooms.templates.manageClassroom.tabCourses() )
    }
})
$(document).on("click",".btn-remove-courses-from-active", e => {
    // .select-active-courses
    let newCourses = [];
    $("#select-active-courses option").each(function(){
        courseid = $(this).val()

        if( !$(this).is(":selected")){
            /*JourneyManageClassrooms.classrooms.current.school_data.courses.map( course => {
                if( course.ID == courseid){
                    newCourses.push(course)
                }
            })*/

            manageClassroomsJs.availableGroupCourses.map( categoryGroup => {
                categoryGroup.courses.map( course => {  
                    if(course.ID == courseid){
                        newCourses.push(course)
                    }
                });
            })
            
        }

        
    });
    JourneyManageClassrooms.classrooms.current.school_data.group_courses = newCourses;

    if( jQuery("#manage-classroom-add-new").is(":visible") ){
        $("#manage-classroom-add-new #class-new-manage-courses").html( JourneyManageClassrooms.classrooms.templates.manageClassroom.tabCourses() )
    }else{
        $("#tab-courses").html( JourneyManageClassrooms.classrooms.templates.manageClassroom.tabCourses() )
    }

})

$(document).on("click",".btn-remove-list-item", e => {
    $(e.currentTarget).parent().parent().remove();

    let removeStudentId = $(e.currentTarget).attr("data-studentid");
    if(parseInt(removeStudentId)){
        let removeClassRoomId = $(e.currentTarget).attr("data-classroomid");
        JourneyManageClassrooms.api.school.classroom.removeStudent({id:removeClassRoomId, student_id:removeStudentId})
                .then( e => {
                    $(e.currentTarget).fadeTo('fast',1);
                    JourneyManageClassrooms.schoolDetails = "";
                    schoolbypasstransient = true;
                    JourneyManageClassrooms.classrooms.reloadReloadables();

                    JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                        console.log("JourneyManageClassrooms.classrooms.statistics.remainingSeats", JourneyManageClassrooms.classrooms.statistics.remainingSeats)
                        $(".seats-available .num").html(JourneyManageClassrooms.classrooms.statistics.remainingSeats)
                    });
                    
                })
                .catch( e => {
                    console.log("error ", e)
                })
    }
});

$(document).on("click", ".btn-remove", e => {
    let userid = $(e.currentTarget).attr("data-userid")
    let classroomid = $(e.currentTarget).attr("data-classroomid");
    let type = $(e.currentTarget).attr("data-type");

    switch(type){
        case "admin":
            $(e.currentTarget).fadeTo('fast',.3);
            JourneyManageClassrooms.api.school.classroom.removeAdmin({id:classroomid, teacher_id:userid})
                .then( e => {
                    $(e.currentTarget).fadeTo('fast',1);

                    JourneyManageClassrooms.classrooms.reloadReloadables();
                    $("#manage-classroom-modal").hide();
                    modalState();
                    
                })
                .catch( e => {
                    console.log("error ", e)
                })
        break;
        case "teacher":
                $(e.currentTarget).fadeTo('fast',.3);
                JourneyManageClassrooms.api.school.classroom.removeTeacher({id:classroomid, teacher_id:userid})
                    .then( e => {
                        $(e.currentTarget).fadeTo('fast',1);

                        JourneyManageClassrooms.classrooms.reloadReloadables();

                        if( manageClassroomsJs.post_slug == "manage-classroom-teachers" || manageClassroomsJs.post_slug == "manage-classroom-students" ){
                            $("#manage-classroom-modal").hide();
                            modalState();
                        }
                        
                    })
                    .catch( e => {
                        console.log("error ", e)
                    })
            break;
        case "student":
            $(e.currentTarget).fadeTo('fast',.3);
            JourneyManageClassrooms.api.school.classroom.removeStudent({id:classroomid, student_id:userid})
                .then( e => {
                    $(e.currentTarget).fadeTo('fast',1);

                    JourneyManageClassrooms.classrooms.reloadReloadables();

                    if( manageClassroomsJs.post_slug == "manage-classroom-teachers" || manageClassroomsJs.post_slug == "manage-classroom-students" || manageClassroomsJs.post_slug == "manage-family"){
                        $("#manage-classroom-modal").hide();
                        modalState();
                    }
                    
                })
                .catch( e => {
                    console.log("error ", e)
                })
            break;
        default: break;
    }
})

/* start event listeners for create classroom*/
$(document).on("click",".classroom.new", e => {
    

    $("#manage-classroom-add-new").show().css({"display":"flex"}).attr("class","modal manage-classroom-modal");
    modalState();
    JourneyManageClassrooms.classrooms.current = "";
    JourneyManageClassrooms.classrooms.current = ""
    JourneyManageClassrooms.classrooms.createNewFields();
    JourneyManageClassrooms.classrooms.avatarImportDropzone();
    JourneyManageClassrooms.classrooms.importCoverPhotoDropzone();
    JourneyManageClassrooms.classrooms.createNewTeacherTab();
    $(".add-student-select-option").show();
    $(".tabs.add-student-forms .tab").removeClass("active");
    

    $("#manage-classroom-add-new .steps-nav a:first-child").trigger("click");
    $("#manage-classroom-add-new .steps-nav a").removeClass("completed")
    $("#manage-classroom-add-new .teacher-exists .field-teacher-exists input[name=search_teacher]").val("");
});
$(document).on("click","#manage-classroom-add-new .steps-nav a", e => {
    let activeTab = $("#manage-classroom-add-new .tabs .tab.active").attr("id");
    $("#manage-classroom-add-new").attr("class",`modal manage-classroom-modal ${activeTab}`);
    console.log("test123", activeTab)
});



$(document).on("change",".class-new-teacher-exists",e => {

    if( jQuery("#manage-classroom-add-new").is(":visible") ){

        if($(e.currentTarget).is(":checked")){
            $("#class-new-teacher form").addClass("teacher-exists")
            $("#class-new-teacher .teacher-add input").removeAttr("required")
            $("#class-new-teacher .btn-add-teacher").attr("type","button")
        }else{
            $("#class-new-teacher form").removeClass("teacher-exists");
            $("#class-new-teacher .teacher-add input").attr("required","required")
            $("#class-new-teacher .btn-add-teacher").attr("type","submit")
        }
    }else{
        if($(e.currentTarget).is(":checked")){
            $("#manage-classroom-modal .field-teacher-exists").show();
            $("#manage-classroom-modal #tab-teacher-add-teacher .teacher-add input").removeAttr("required");
            $("#manage-classroom-modal #tab-teacher-add-teacher .teacher-add").hide();
            $("#manage-classroom-modal #tab-teacher-add-teacher .btn-add-teacher").attr("type","button")
        }else{
            $("#manage-classroom-modal .field-teacher-exists").hide();
            $("#manage-classroom-modal #tab-teacher-add-teacher .teacher-add input").attr("required","required")
            $("#manage-classroom-modal #tab-teacher-add-teacher .teacher-add").show();
            $("#manage-classroom-modal #tab-teacher-add-teacher .btn-add-teacher").attr("type","submit")
        }
    }
});

$(document).on("click","#class-new-students .buttons-list button", e => {
    $(".add-student-select-option").hide();
    action = $(e.currentTarget).attr("data-target");

    switch(action){
        case "#add-student-import-list":
            JourneyManageClassrooms.classrooms.createNewStudentImport();
            break;
        case "#add-student-search-existing":
            JourneyManageClassrooms.classrooms.createNewStudentExisting();
            break;
        case "#add-student-by-email":
            JourneyManageClassrooms.classrooms.createNewStudentEmail();
            break;
        case "#add-student-by-username":
            JourneyManageClassrooms.classrooms.createNewStudentUsername();
            break;
    }
    // initialize dob datepicker
    new Pikaday({
        field: document.getElementById("date_of_birth_1"),
        format: 'YYYY-MM-DD'
    });
    new Pikaday({
        field: document.getElementById("date_of_birth_2"),
        format: 'YYYY-MM-DD'
    });
})

$(document).on("click","#class-new-students .btn-back-create-new-class-student", e => {
    $(".add-student-select-option").show();
    $(".tabs.add-student-forms .tab").removeClass("active")
})

$(document).on("click",".btn-back-to-import-student", e => {
    $("#class-student-list-template").removeClass("active");
    $("#class-new-students").addClass("active")
})

$(document).on("click",".btn-back-step", e => {
    console.log("btn-back-step")
    JourneyManageClassrooms.classrooms.createNewNavigate(false);
});

$(document).on("click",".selected-for-admin", e => {
    newSelectedTeachers = [];
    teacherId = $(e.currentTarget).attr("data-id")
    JourneyManageClassrooms.admins.selectedTeachers.map( e => {
        if( e != teacherId) newSelectedTeachers.push(e);
    })
    JourneyManageClassrooms.admins.selectedTeachers = newSelectedTeachers;
    setTimeout( () => {
        JourneyManageClassrooms.admins.displaySelectedTeachers();
    }, 100);

    $(e.currentTarget).remove();
})

$(document).on("change",".create-new-radio", e => {
    $(".create-new-radio").parent().removeClass("active")
    if( $(e.currentTarget).is(":checked")){
        $(e.currentTarget).parent().addClass("active")
        let classroomID = $(".create-new-avatar .new-group-id").val();
        
        JourneyManageClassrooms.api.school.updateClassroomDetails({
            id: classroomID,
            formData: {
                "tab": $(e.currentTarget).attr("data-tab"),
                "image": $(e.currentTarget).val(),
                "classroom_id": classroomID 
            }
        })
        .then( e => {

        })
        .catch( e => {
            console.log("error", e)
        })
    }
});

$(document).on("click", ".teacher-exists .field-teacher-exists input[name=search_teacher], #tab-teacher-add-teacher input[name=search_teacher]", e=> {
    $(".search-teacher-result").removeClass("active")
});
$(document).on("keyup", `.teacher-exists .field-teacher-exists input[name=search_teacher], 
                        #tab-teacher-add-teacher input[name=search_teacher],
                        .form-add-admin input[name=search_teacher]
                        `, e => {
    var searchKey = $(e.currentTarget).val();

    if( searchKey != ""){

        var teachersList = "";

        JourneyManageClassrooms.schoolDetails.teachers.map( teacher => {
            haystack = teacher.data.first_name+" "+teacher.data.last_name+teacher.data.user_email;
            haystack = haystack.toLowerCase();
            show = false;
            if (haystack.includes(searchKey.toLowerCase())) {
                show = true;
            }

            if(show){

                if($(".form-add-admin").is(":visible")){
                    teacherName = `<div class="with-avatar"><img src="${teacher.data.avatar_url}"/><span>${teacher.data.first_name} ${teacher.data.last_name}</span></div>`;
                }else{
                    teacherName = `${teacher.data.first_name} ${teacher.data.last_name}`;
                }

                teachersList += `
                    <button type="button" class="teachers-list-item-result" data-teacherid="${teacher.data.ID}">${teacherName}</button>
                `
            }
        })
        
        if(teachersList == ""){
            teachersList = `<div style="padding:10px 0px">No records found</div>`;
        }

        $(".search-teacher-result").addClass("active").html(teachersList);
        
    }else{
        $(".search-teacher-result").removeClass("active")
    }
});

$(document).on("click",".teachers-list-item-result", e => {
    teacherId = $(e.currentTarget).attr("data-teacherid");
    $(".search_teacher_id").val( teacherId);
    $(".search-teacher-result").removeClass("active");
    $(".teacher-exists .field-teacher-exists input[name=search_teacher], #tab-teacher-add-teacher input[name=search_teacher]").val( $(e.currentTarget).text() );


    if($(".form-add-admin").is(":visible")){
        if(!JourneyManageClassrooms.admins.selectedTeachers.includes(teacherId)){
            JourneyManageClassrooms.admins.selectedTeachers.push(teacherId);
            setTimeout( () => {
                JourneyManageClassrooms.admins.displaySelectedTeachers();
            }, 100);
        }
    }
})
$(document).on("click","#class-new-teacher .btn-add-teacher", e => {
    if($(".class-new-teacher-exists").is(":checked")){
        $(".teacher-exists .field-teacher-exists input[name=search_teacher]").val("")
        teachersList = "";
        JourneyManageClassrooms.schoolDetails.teachers.map( teacher => {
            if( teacher.data.ID == $(".search_teacher_id").val() ){
                var exists = false;
                JourneyManageClassrooms.classrooms.current.school_data.teachers.map( eTeacher => {
                    if(eTeacher.data.ID == teacher.data.ID ) exists = true;
                })

                if(!exists) JourneyManageClassrooms.classrooms.current.school_data.teachers.push(teacher)
            }
        })

        setTimeout( () => { 
            JourneyManageClassrooms.classrooms.createNewTeacherTab();
            JourneyManageClassrooms.api.school.updateClassroomDetails({
                    id: $(".frm-add-teacher .new-group-id").val(),
                    formData: $(".frm-add-teacher").serialize()
            })
        }, 200);
    }else{
        if($(".frm-add-teacher").valid()){
            var classroomID = $(".frm-add-teacher .new-group-id").val();
            $("#class-new-teacher .btn-add-teacher").fadeTo("fast",.3)
            JourneyManageClassrooms.api.school.updateClassroomDetails({
                id: classroomID,
                formData: {
                    "tab": "teacher-add",
                    "first_name": $(".teacher-add input[name=first_name]").val(),
                    "last_name": $(".teacher-add input[name=last_name]").val(),
                    "email": $(".teacher-add input[name=email]").val(),
                    "classroom_id": classroomID 
                }
            })
            .then( e => {

                $(".teacher-add input[name=first_name]").val("");
                $(".teacher-add input[name=last_name]").val("");
                $(".teacher-add input[name=email]").val("");

                JourneyManageClassrooms.schoolDetails = "";
                schoolbypasstransient = true;

                JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                    JourneyManageClassrooms.classrooms.getById(classroomID);

                    setTimeout( () => {
                        $("#class-new-teacher .btn-add-teacher").fadeTo("fast",1);
                        JourneyManageClassrooms.classrooms.createNewTeacherTab();
                    }, 200)
                });
                

            })
            .catch( e => {
                console.log("error", e)
            })
        }
    }
})


$(document).on("keyup", ".search-student-container input[name=search_student]", e => {
    var searchKey = $(e.currentTarget).val();

    if( searchKey != ""){

        var studentsList = "";

        JourneyManageClassrooms.schoolDetails.students.map( student => {
            haystack = student.data.first_name+" "+student.data.last_name+student.data.user_email;
            haystack = haystack.toLowerCase()
            show = false;
            if (haystack.includes(searchKey.toLowerCase())) {
                show = true;
            }

            if(show){
                studentsList += `
                    <button type="button" class="student-list-item-result" data-teacherid="${student.data.ID}">${student.data.first_name} ${student.data.last_name}</button>
                `
            }
        })
        
        if(studentsList == ""){
            studentsList = `<div style="padding:10px 0px">No records found</div>`;
        }

        $(".search-student-result").addClass("active").html(studentsList);
        
    }else{
        $(".search-student-result").removeClass("active")
    }
});
$(document).on("click",".student-list-item-result", e => {
    $(".search_student_id").val( $(e.currentTarget).attr("data-teacherid"));
    $(".search-student-result").removeClass("active");
    $(".search-student-container input[name=search_student]").val( $(e.currentTarget).text() );

    if( JourneyManageClassrooms.classrooms.statistics.remainingSeats > 0 ){
    
        JourneyManageClassrooms.schoolDetails.students.map( student => {
            if( student.data.ID == $(".search_student_id").val() ){
                var exists = false;
                JourneyManageClassrooms.classrooms.current.school_data.students.map( eStudent => {
                    if(eStudent.data.ID == student.data.ID ) exists = true;
                })

                if(!exists) JourneyManageClassrooms.classrooms.current.school_data.students.push(student)
            }
        })

        if( jQuery("#manage-classroom-add-new").is(":visible") ){
            setTimeout( () => { 
                JourneyManageClassrooms.classrooms.createNewStudentExisting();
                $(".frm-createn-new-student-existing .new-group-id").val(JourneyManageClassrooms.classrooms.current.ID);
                setTimeout( () => {

                    JourneyManageClassrooms.api.school.updateClassroomDetails({
                            id: $(".frm-add-teacher .new-group-id").val(),
                            formData: $(".frm-createn-new-student-existing").serialize()
                    }).then( e => {
                        JourneyManageClassrooms.schoolDetails = "";
                        schoolbypasstransient = true;
                        
                        JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
                            console.log("JourneyManageClassrooms.classrooms.statistics.remainingSeats", JourneyManageClassrooms.classrooms.statistics.remainingSeats)
                            $(".seats-available .num").html(JourneyManageClassrooms.classrooms.statistics.remainingSeats)
                        });

                    }).catch(e => {
                        console.log("error", e)
                    })
                    
                })
                
            }, 200);
        }
    }
});

$(document).on("click","#manage-classroom-add-new .btn-student-submit", e => {
    $(".steps-nav a").removeClass("active").addClass("completed");
    $(".btn-student-submit").fadeTo("fast",.3);
    setTimeout( () => {
        $(".btn-student-submit").fadeTo("fast",1);
        $("#manage-classroom-add-new").hide();
        modalState();
        
        JourneyManageClassrooms.schoolDetails = "";
        schoolbypasstransient = true;

        JourneyManageClassrooms.classrooms.reloadReloadables();
    }, 1000)
    
})

$(document).on("click","#class-new-students .button-import-more", e => {
    JourneyManageClassrooms.classrooms.createNewStudentImport();
});

$(document).on("click", "#tab-teacher-add-teacher button[type=button].btn-add-teacher", e => {
    $("#tab-teacher-add-teacher button[type=button].btn-add-teacher").fadeTo("fast",.3)
    JourneyManageClassrooms.api.school.updateClassroomDetails({
        id: JourneyManageClassrooms.classrooms.current.ID,
        formData: {
            "tab": "teacher-add-single",
            "classroom_id": JourneyManageClassrooms.classrooms.current.ID,
            "teacher_id": $("#tab-teacher-add-teacher .search_teacher_id").val()
        }
    })
    .then( e => {
        
        JourneyManageClassrooms.schoolDetails = "";
        schoolbypasstransient = true;

        JourneyManageClassrooms.api.school.getuserSchoolDetails({}).then( e => {
            JourneyManageClassrooms.classrooms.getById(JourneyManageClassrooms.classrooms.current.ID);
            $(`#tab-teachers`).html(JourneyManageClassrooms.classrooms.templates.manageClassroom.tabTeachers());
            $("#tab-teacher-add-teacher button[type=button].btn-add-teacher").fadeTo("fast",1)
        });
        
    })
    .catch( e => {
        console.log("error", e)
    })
});
/* end event listeners for create classroom*/


$(document).on("click",".modal", e => {
    if( $(e.target).hasClass("modal") ){
        $(".modal").fadeOut();
        modalState();
    }
})

$(document).on("click","#manage-classroom-add-new #add-student-import-list .download a", e => {
    JourneyManageClassrooms.students.listTemplateModal();
})


$(document).on("click", "#tab-students-add-student .import-students", e => {
    $(".student-tabs.tabs .tab").removeClass("active");
    $(".student-tabs.tabs #tab-students-add-import.tab").addClass("active");

    $("#tab-students-add-import").html( JourneyManageClassrooms.classrooms.templates.manageClassroom.importStudents() );

    JourneyManageClassrooms.classrooms.templates.manageClassroom.renderImportStudentsDropzone();
    
    $("#tab-students-add-import .btn-back.btn-back-create-new-class-student").html("CANCEL")
})


$(document).on("click", "#tab-students-add-student .field-label-checkbox.student .checkbox input[type=checkbox]", e => {
    if($(e.currentTarget).is(":checked")){
        $("#tab-students-add-student form .student-add").hide();
        $("#tab-students-add-student form .search-student-container").show();
        $("#tab-students-add-student form input[name=tab]").val("student-add-single");

        $("#tab-students-add-student form input[name=first_name]").removeAttr("required");
        $("#tab-students-add-student form input[name=last_name]").removeAttr("required");
        $("#tab-students-add-student form input[name=username]").removeAttr("required");
        $("#tab-students-add-student form input[name=password]").removeAttr("required");

    }else{
        $("#tab-students-add-student form .student-add").show();
        $("#tab-students-add-student form .search-student-container").hide();
        $("#tab-students-add-student form input[name=tab]").val("student-add");

        $("#tab-students-add-student form input[name=first_name]").attr("required","required");
        $("#tab-students-add-student form input[name=last_name]").attr("required","required");
        $("#tab-students-add-student form input[name=username]").attr("required","required");
        $("#tab-students-add-student form input[name=password]").attr("required","required");
    }
})


$(document).on("click","#tab-students-add-import .button-import-more", e => {
    $("#tab-students-add-import").html( JourneyManageClassrooms.classrooms.templates.manageClassroom.importStudents() );
    JourneyManageClassrooms.classrooms.templates.manageClassroom.renderImportStudentsDropzone();
});

$(document).on("click",".tab-students-nav #a-manage-student", e => {
    $(`#tab-students`).html(JourneyManageClassrooms.classrooms.templates.manageClassroom.tabStudents())
})

$(document).on("change", ".tab-classroom-settings .selections .select-item input[type=radio]", e => {
    JourneyManageClassrooms.classrooms.settings.radioButtonsListen();
});

$(document).on("keyup", ".add-to-family .family-details .search-family", e => {
    let searchKey = $(e.currentTarget).val();
    console.log("JourneyManageClassrooms.schoolDetails.families", JourneyManageClassrooms.schoolDetails.families)
    let families = "";
    let children = "";
    JourneyManageClassrooms.schoolDetails.families.map( family => {
        children = "";
        if( family.family_id.includes(searchKey) || family.group_post.post_title.includes(searchKey) ){
            if(family.children.length > 0 ){
                family.children.map( child => {
                    children += `
                        <img src="${child.avatar_url}" title="${child.first_name} ${child.last_name}"/>
                    `
                })
            }

            families += `
                <div class="row family-result-row" data-familyid="${family.id}">
                    <div class="col">${family.family_id}</div>
                    <div class="col">${family.group_post.post_title}</div>
                    <div class="col children">${children}</div>
                </div>
            `;
        }
    })
    

    let searchResult = ``;
    searchResult += `
        <div class="result-table">
            <div class="table">
                <div class="row head">
                    <div class="col">FAMILY</div>
                    <div class="col">FAMILY ID</div>
                    <div class="col">CHILD</div>
                </div>
                ${families}
            </div>
            <div class="button-container">
                <a href="/manage-institute-families/?new=1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="#5D53C0" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                Add To New Family
                </a>
            </div>
        </div>
    `;
    $(".add-to-family .family-details .search-result").html(searchResult)

})

$(document).on("click",".add-to-family .family-details .search-result .family-result-row", e => {
    $(".add-to-family .family-details .search-result").html("");
    let familyDetailsTable = "";
    let familyId = $(e.currentTarget).attr("data-familyid");
    let familyDetails = JourneyManageClassrooms.families.getFamilyById(familyId);
    familyDetailsTable = JourneyManageClassrooms.families.templates.addChild(familyId)
    JourneyManageClassrooms.families.selectedFamily = familyDetails;

    $(".add-to-family .family-details .family-details-table").html(familyDetailsTable);
    $(".add-to-family .family-details .search-family").val(familyDetails.group_post.post_title);
    $(".add-to-family .btn-add-to-family").removeAttr("disabled");

});

$(document).on("click", ".add-to-family .btn-add-to-family", e => {
    console.log("JourneyManageClassrooms.students.studentSelectedToFamily", JourneyManageClassrooms.students.studentSelectedToFamily)
    $(".add-to-family .btn-add-to-family").fadeTo("fast",.3);
    JourneyManageClassrooms.api.families.addChildSave( {id:JourneyManageClassrooms.families.selectedFamily.id, form: $("#frm-add-to-family").serialize()})
        .then( e => {

            $("#manage-classroom-modal").removeClass("add_student");
            $("#manage-classroom-modal").addClass("success_add_student");

            let modalContent = `
                <div class="success-create-family">
                    <img src="/wp-content/themes/buddyboss-theme-child/assets/img/manage-classroom/success-check.svg"/>
                    <h2>Success</h2>
                    <p>You have successfully added ${JourneyManageClassrooms.students.studentSelectedToFamily.data.first_name} ${JourneyManageClassrooms.students.studentSelectedToFamily.data.last_name}
                        to the ${JourneyManageClassrooms.families.selectedFamily.group_post.post_title} family..</p>
                    <button type="button" class="btn-back-students">Back to Students</button>
                </div>
            `;
            $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
            JourneyManageClassrooms.schoolDetails = ""; // clear school details to get fresh data from backend
            JourneyManageClassrooms.students.list();
        })
        .catch( e => {

        })
    
}) 
// end manage students javascripts


// *start manage families javascripts */
$(document).on("click", ".sidebar-select-institute-container .institutes-list .items a.item", e => {
    e.preventDefault();
    let instituteId = $(e.currentTarget).attr("data-id");
    console.log("instituteId", instituteId)
    if(instituteId != ""){
        $(e.currentTarget).fadeTo("fast",.3)
        JourneyManageClassrooms.api.user.saveSelectedInstitute({id:instituteId})
            .then( e => {
                window.location = window.location;
            })
            .catch( e => {
                console.log("error ", e)
            })
    }
})

$(document).on("click",".select-institute-menu li, .institutes-list .overlay", e => {
    $(".sidebar-select-institute-container").toggleClass("active")
})


$(document).on("click", ".institute-action", e => {
    let action = $(e.currentTarget).attr("data-action");

    switch(action){
        case "redirect":
            let target = $(e.currentTarget).attr("data-target")
            window.location = target;
            break;
        case "broadcast_email":
            $("#manage-classroom-modal").attr("class",`modal manage-classroom-modal broadcast_email`);
            $("#manage-classroom-modal").show().css({"display":"flex"});
            broadcastText = "This email will be sent to all teachers and students of the institute";
                  
            modalContent = `
            <div class="broadcast-email-container">
                <h2>Broadcast Email</h2>
                <p>${broadcastText}</p>
                
                <form class="" onsubmit="JourneyManageClassrooms.classrooms.saveUpdates(this); return false;">
                    <input type="hidden" name="tab" value="email-classroom" />
                    <input type="hidden" name="classroom_type" value="parent" />
                    <input type="hidden" name="classroom_id" value="${JourneyManageClassrooms.schoolDetails.parent_school.school_id}" />
                    <input type="hidden" name="broadcast_type" value="institute"/>

                    <div class="field-group">
                        <label class="field-label">Subject</label>
                        <div class="field-input">
                            <input type="text" name="subject" required placeholder="Subject"/>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Body</label>
                        <div class="field-input">
                            <textarea required name="body"></textarea>
                        </div>
                    </div>
                    
                    <div class="button-container">
                        <button type="button" class="btn-back">Cancel</button>
                        <button type="submit" class="btn-save" >Send</button>
                    </div>
                </form>
            </div>
            `;

            $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
            modalState();
        break;
    }
})

$(document).on("click",".btn-close-import", e => {
    $("#manage-classroom-modal").hide();
    modalState();
})

$(document).on("change",".manage-families-container select", e => {
    if( $(e.currentTarget).val() == ""){
        $(e.currentTarget).addClass("novalue");
    }else{
        $(e.currentTarget).removeClass("novalue")
    }
});

$(document).on("click",".btn-back-students", e => {
    $("#manage-classroom-modal").hide();
    modalState();
})

$(document).on("keyup", ".add-child-container .search-child", e => {
    let childSearchkey = $(e.currentTarget).val();

    let searchResult = "";
    JourneyManageClassrooms.schoolDetails.students.map( student => {
        haystack = student.data.first_name+" "+student.data.last_name+student.data.user_email;
        haystack = haystack.toLowerCase()
        show = false;
        console.log("JourneyManageClassrooms.schoolDetails.students", JourneyManageClassrooms.schoolDetails.students, childSearchkey)

        if (haystack.includes(childSearchkey.toLowerCase())) {
            show = true;
        }

        if(show){
            searchResult += `<div class="list-item" data-studentid="${student.data.ID}">
                <img src="${student.data.avatar_url}"/> <span class="name">${student.data.first_name} ${student.data.last_name}</span>
            </div>`
        }
    })

    $(".add-child-container .search-result").addClass("active").html(searchResult);
});
$(document).on("click",e => {
    if( $(".add-child-container .search-result").hasClass("active")){
        $(".add-child-container .search-result").removeClass("active")
    }
})
$(document).on("click",".add-child-container .search-result .list-item", e => {
    let studentid = parseInt($(e.currentTarget).attr("data-studentid"));
    if (!JourneyManageClassrooms.families.selectedStudents.includes(studentid)) {
        JourneyManageClassrooms.families.selectedStudents.push(studentid);
    }
    JourneyManageClassrooms.families.displayAddStudents( $(".selected-students") );
    $(".add-child-container .search-child").val("")
    $(".btn-add-child").removeAttr("disabled");
    //console.log("JourneyManageClassrooms.families.selectedStudents", JourneyManageClassrooms.families.selectedStudents)
})
$(document).on("click",".add-child-container .selected-students .btn-remove-student", e => {
    let studentid = $(e.currentTarget).attr("data-studentid");
    let newList = [];
   
    JourneyManageClassrooms.families.selectedStudents.map(e => {
        if( e != parseInt(studentid)){
            newList.push(e);
        }
    })
    JourneyManageClassrooms.families.selectedStudents = newList;
    JourneyManageClassrooms.families.displayAddStudents( $(".selected-students") );
    
})

$(document).on("click",".families-list .button-family-action", e => {
    let action = $(e.currentTarget).attr("data-action");
    let familyId = $(e.currentTarget).attr("data-familyid");
    switch(action){
        case "remove":
            $(e.currentTarget).fadeTo("fast",.3)
            JourneyManageClassrooms.api.families.deleteFamily({id:familyId})
            .then( e => {
                JourneyManageClassrooms.schoolDetails = ""; // clear school details to get fresh data from backend
                JourneyManageClassrooms.families.list();
            })
            .catch( e => {
                
            })
        break;
        case "manage_family":
            JourneyManageClassrooms.families.manageFamily(familyId)
        break;
        case "view_family":
            JourneyManageClassrooms.families.viewFamily(familyId)
            break;
        case "add_child":
            JourneyManageClassrooms.families.addChild(familyId)
            break;
    }
})

$(document).on("click","#import_family .btn-import", e => {
    JourneyManageClassrooms.families.saveImportedFamilies()
})
$(document).on("click","#import_family .btn-cancel", e => {
    $("#import_family .families-csv-container, #import_family .successfull-imports-container, #import_family .error-family-csv").html("");
    $("#import-families-dropzone").show();
})

$(document).ready( $ => {
    var currentUrl = window.location.href;
    var pattern = "manage-institute-families/?new=1";
    if (currentUrl.indexOf(pattern) !== -1) {
        $(".button-families-action[data-action='add_family']").trigger("click")
    } 
})
// end manage families javascripts

// start family parent dashboard
$(document).on("click",".childrens-grid .actions button", e => {
    $(e.currentTarget).parent().find(".submenu").addClass("active");
});
$(document).on("click",".childrens-grid .child-item .button-child-action", e => {
    let action = $(e.currentTarget).attr("data-action");
    let studentId = $(e.currentTarget).attr("data-studentid");
    
    switch(action){
        case "edit_student":
            $("#manage-classroom-modal").show().css({"display":"flex"});
            $("#manage-classroom-modal").addClass("edit_student");
            actiontype = action;
            modalUserDetails = JourneyManageClassrooms.students.getStudentById(studentId); 
            let editModalContent = `
                <div class="teacher-edit-container">
                    <h2>Update Child</h2>

                    <form onsubmit="JourneyManageClassrooms.users.saveEdits(this); return false;">
                        <input type="hidden" name="userid" value="${modalUserDetails.data.ID}" />
                        <input type="hidden" name="type" value="${actiontype}" />
                        <div class="field-group">
                            <label class="field-label">Name</label>
                            <div class="field-input fullname">
                                <input type="text" name="first_name" required placeholder="First Name" value="${modalUserDetails.data.first_name}"/>
                                <input type="text" name="last_name" required placeholder="Last Name" value="${modalUserDetails.data.last_name}"/>
                            </div>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Email</label>
                            <div class="field-input">
                                <input type="email" name="email" type="email" ${(actiontype == "edit_student") ? ``:`required`}  placeholder="Email Address" value="${modalUserDetails.data.user_email}"/>
                            </div>
                        </div>

                        ${
                            (actiontype == "edit_student") ? 

                            `
                            <div class="field-group" style="visibility:hidden; opacity:0px;position:absolute;width:0px;height:0px;">
                                <label class="field-label">Gender</label>
                                <div class="field-input gender">
                                    <label>
                                        <input type="radio" name="gender" required value="male" ${(modalUserDetails.data.gender=="male") ? `checked`:``} /> Male
                                    </label>
                                    <label>
                                        <input type="radio" name="gender" required value="female" ${(modalUserDetails.data.gender=="female") ? `checked`:``}/> Female
                                    </label>
                                </div>
                            </div>
                            
                            <div class="field-group">
                                <label class="field-label">Username</label>
                                <div class="field-input">
                                    <input type="text"   value="${modalUserDetails.data.user_login}" readonly disabled/>
                                    <input type="hidden" name="username"  value="${modalUserDetails.data.user_login}"/>
                                </div>
                            </div>

                            <div class="field-group">
                                <label class="field-label">Password</label>
                                <div class="field-input password">
                                    <input name="password" type="password"  placeholder="Password" value="" class="input-password"/>
                                    <button type="button" class="btn-view-password"></button>
                                </div>
                            </div>
                            `
                            :
                            ``
                        }

                        <div class="button-container">
                            <button type="button" class="btn-back">CANCEL</button>
                            <button type="submit" class="btn-save">SAVE</button>
                        </div>
                        
                    </form>
                </div>
                `;
                $("#manage-classroom-modal").show().css({"display":"flex"});
                $("#manage-classroom-modal .modal-content .main-content").html(editModalContent);
                modalState();
            break;
        case "login":
            var studentInfo = JourneyManageClassrooms.students.getStudentById(studentId);
            window.location.href = `?action=login_as_child&key=${studentInfo.data.login_key}`
        break;
        case "remove":
            //JourneyManageClassrooms.schoolDetails.parent_school.post.ID
            //studentId
            $(e.currentTarget).fadeTo("fast",.3)
            JourneyManageClassrooms.api.families.removeChild({childid:studentId, familygroupid:JourneyManageClassrooms.schoolDetails.parent_school.post.ID})
                .then( e => {
                    $(e.currentTarget).fadeTo("fast",1)
                    $(`.child-${studentId}`).fadeOut().remove();
                })
                .catch( e => {
                    console.log(e);
                })
        break;
        case "view_reports":
            window.location.href = "/reporting/?tab=userReportTab&userid="+studentId;
            break;
        case "view_profile":
            $('.bb-view-profile.bb-action-popup').show();
            Safar.profilePopup(studentId); 
        break;
        case "view_achievement":
            window.location.href = "/achievements?userid="+studentId;
        break;
        case "view_quranic_animals":
            window.location.href = "/quranic-animals?userid="+studentId;
        break;
        case "change_password":
            let modalContent = ``;
            var studentInfo = JourneyManageClassrooms.students.getStudentById(studentId);
            console.log("studentInfo", studentInfo)
            JourneyManageClassrooms.familyDashboard.childUserId = studentInfo.data.ID;
            modalContent += `
            <div class="teacher-add-class-container change-password">
                <h2>Change Password</h2>
                <div class="teacher-details">
                    <img src="${studentInfo.data.avatar_url}" class="avatar">
                    <span>${studentInfo.data.display_name}</span>
                </div>

                <form class="" onsubmit="JourneyManageClassrooms.familyDashboard.savePassword(this); return false;">
                    <input type="hidden" name="student_id" value="${studentInfo.data.ID}">
                    <div class="set-password-input">
                        
                        <div class="input">
                            <label>Username</label>
                            <span>${studentInfo.data.user_login}</span>
                        </div>

                        <div class="input password">
                            <label>Password</label>
                            <input type="password" name="password" class="input-password" placeholder="Enter password"/>
                        </div>

                        <div class="input password password2">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="input-password" placeholder="Confirm password"/>
                        </div>
                        <div id="pwd-indicator"></div> 
                        
                        <button type="submit" class="btn btn-set-password" disabled>Submit</button>

                    </div>
                </form>
            </div>
            `

            $("#manage-classroom-modal").show().css({"display":"flex"});
            $("#manage-classroom-modal .modal-content .main-content").html(modalContent);
            modalState();
        break;
    }
});
$(document).on("keyup",".input-password", e => {
    let pwd1 = $(".input-password[name=password]").val();
    let pwd2 = $(".input-password[name=confirm_password]").val();

    if( (pwd1 == pwd2) && pwd1.length > 0 && pwd2.length > 0 ){
        $(".btn-set-password").removeAttr("disabled")
        JourneyManageClassrooms.checkPasswordStrength(pwd1, document.getElementById("pwd-indicator"));

        if(pwd1.length > 0 || pwd2.length > 0 &&  (pwd1 != pwd2)){
            document.getElementById("pwd-indicator").innerHTML = 'Password match!';
            $("#pwd-indicator").removeClass("mismatch").addClass("match");
            $(".input.password2").removeClass("error")
            JourneyManageClassrooms.password = pwd1;
        }

    }else{
        
        if(pwd1.length > 0 || pwd2.length > 0 &&  (pwd1 != pwd2)){
            document.getElementById("pwd-indicator").innerHTML = 'Password did not match!';
            $("#pwd-indicator").removeClass("match").addClass("mismatch");
            $(".input.password2").addClass("error")
        }
        $(".btn-set-password").attr("disabled","disabled")
    }
})
// end family parent dashboard
$(document).on("click",".btn-close-upgrade-seats", e => {
    $.ajax({
        url: `${safarObject.ajaxurl}?action=close_upgrade_seats`,
        type: "get",
        headers: {
            "X-WP-Nonce": safarObject.wpnonce
        },
        beforeSend: (xhr) => {
            $(".institute-upgrade-success").fadeTo("fast",.8)
        },
        success: (d) => {
            $(".institute-upgrade-success").fadeOut();
        },
        error: (d) => {
             
        }
    });
});
/* end classroom event listeners*/

$(document).on("click",".btn-view-password", e => {
    $(e.currentTarget).toggleClass("hide-password")
    
    if( $(e.currentTarget).hasClass("hide-password")){
        $(e.currentTarget).parent().find(".input-password").attr("type","text");
    }else{
        $(e.currentTarget).parent().find(".input-password").attr("type","password");
    }
})
