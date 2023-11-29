$ = jQuery;
let SafarQuranicAnimals = {}
SafarQuranicAnimals = {

    api: {
        getUserQuranicAnimals : async () => {

            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/user/quranic_animals`,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    data: {
                        instituteparent: achievementsObject.is_user_institute_parent,
                        childid: achievementsObject.child_id
                    },
                    beforeSend: () => {

                    },
                    success: (d) => {
                    
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /user/quranic animals`, d);
                    }
                });
            });
        }
    },

    loadQuranicAnimals : () => {
        SafarQuranicAnimals.api.getUserQuranicAnimals()
            .then( d => {
                $(".quranic-animals-container .items .item img").removeClass("skeleton-loader")
                // quranic-animal-ID
                let totalCount = 0;
                let unlockedCount = 0;
                d.quranic_animals.map( animal => {
                    //console.log("Unlocked", animal.unlocked, animal.post_title, animal.ID )
                    if(animal.unlocked){
                        $(`.quranic-animal-${animal.ID}`).attr({"src": animal.unlocked_image })
                        $(`.quranic-animal-${animal.ID}`).parent().addClass("unlocked")
                        $( `<div class="title">${animal.post_title}</div>
                            <div class="excerpt">${animal.post_excerpt}</div>
                        ` ).insertAfter( `.quranic-animal-${animal.ID}` );
                        unlockedCount++;
                    }

                    totalCount++;
                })

                $(".earned-quranic-animals").html(`${unlockedCount}/${totalCount}`);

            })
            .catch( d => {
                console.log(" error GetQUranicAnimals ", d )
            })
    },

    familyInstitute: {
        studentDropdown : () => {
            if(achievementsObject.is_user_institute_parent){
                let dropdownItems =  ``;
                let selectedStudent = ``;
                achievementsObject.children.map( student => {
                    dropdownItems += `<div class="item" data-studentid="${student.ID}">
                        <img src="${student.data.avatar_url}" class="avatar"/>
                        <span class="student-name" >${student.data.first_name} ${student.data.last_name}</span> 
                    </div>`
    
                    if(student.ID == achievementsObject.child_id){
                        selectedStudent = `
                            <img src="${student.data.avatar_url}" class="avatar"/>
                            <span class="student-name" style="
                            max-width: 151px;
                            overflow: hidden;
                            display: inline-flex;
                            white-space: nowrap;
                        ">${student.data.first_name} ${student.data.last_name}</span> 
                        `
                    }
                })
    
                $('.students-dropdown-container .selected div').html(selectedStudent);
                $('.students-dropdown-container .dropdown .items').html(dropdownItems);
            }
        }  
    },

    init: () => {
        SafarQuranicAnimals.loadQuranicAnimals();
        SafarQuranicAnimals.familyInstitute.studentDropdown();
    }
}


$(document).ready( e => {
    SafarQuranicAnimals.init();
})  

$(document).on("click",".students-dropdown-container", e => {
    $(e.currentTarget).find(".dropdown").toggleClass("active")
})
$(document).on("click",".students-dropdown-container .dropdown .overlay", e=>{
    $(".students-dropdown-container .dropdown").removeClass(".active")
});
$(document).on("click",".students-dropdown-container .items .item", e => {
    let studentid = $(e.currentTarget).attr("data-studentid")
    achievementsObject.child_id = studentid;
    window.location.href = "/quranic-animals/?userid="+studentid;
})