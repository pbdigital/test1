jQuery(document).ready(function ($) {


    $(document).on("click", '#members-list .item-entry.member-type-student a', e => {
        e.preventDefault();
    });
    $(document).on("click", '#members-list .item-entry', e => {
        e.preventDefault();

        if(!$(e).hasClass("item-entry-header")){

            $('.bb-view-profile.bb-action-popup').show();
            let userId = $(e.currentTarget).attr("data-bp-item-id");
            Safar.profilePopup(userId);
        }
    });


    $(document).on("click", 'a.bb-close-view-profile.bb-model-close-button', (e) => {
        e.preventDefault();
        $('.bb-view-profile.bb-action-popup').fadeOut();
        
    })

    $(document).on("click",".bb-view-profile .modal-mask", e => {
        if( $(e.target).hasClass("modal-mask") ){
            $('.bb-view-profile.bb-action-popup').fadeOut();
        }
    })

    //$("body").append(`<div class="quranic-animal-popup-container"></div>`);
    /*
    $(document).on("mouseenter", ".profile-quranic_animal_list .item", function(){
      
        var PosTop = $(this).offset().top;
        var PosLeft = $(this).offset().left;
        //bb-view-profile-content bb-action-popup-content
        var popupWidth = $(".bb-view-profile.bb-action-popup .bb-view-profile-content.bb-action-popup-content").width();

        quranicAnimalLeft =   PosLeft - popupWidth + 34;

        let lastIndex = $(".profile-quranic_animal_list .slick-active").last().attr("data-slick-index")
        let thisIndex = $(this).closest(".slick-active").attr("data-slick-index")
        let qaContent = $(this).find(".qa-description-popup-content").html()
        //console.log(PosTop, PosLeft, popupWidth, quranicAnimalLeft, lastIndex, thisIndex, qaContent)
        $(".qa-description-popup").addClass("active").css({"left":quranicAnimalLeft}).html(qaContent)

    });

   
    $(document).on("mouseenter", ".badges-earned__list .item", function(){
      
        var PosTop = $(this).offset().top;
        var PosLeft = $(this).offset().left;
        //bb-view-profile-content bb-action-popup-content
        var popupWidth = $(".bb-view-profile.bb-action-popup .bb-view-profile-content.bb-action-popup-content").width();

        quranicAnimalLeft =   PosLeft - popupWidth + 30;

        //let lastIndex = $(".profile-quranic_animal_list .slick-active").last().attr("data-slick-index")
        //let thisIndex = $(this).closest(".slick-active").attr("data-slick-index")
        let qaContent = $(this).find(".badge-description-popup-content").html()
        //console.log(PosTop, PosLeft, popupWidth, quranicAnimalLeft, lastIndex, thisIndex, qaContent)
        $(".badge-description-popup").addClass("active").css({"left":quranicAnimalLeft}).html(qaContent)

    });

    $(document).on("mouseleave", ".profile-quranic_animal_list .item, .badges-earned__list .item", function(){
        $(".qa-description-popup").removeClass("active");
        $(".badge-description-popup").removeClass("active")
    })
    */
})