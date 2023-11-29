/* This is your custom Javascript */
$ = jQuery;


jQuery(document).ready(function ($) {
    $(document).on('click', '.close', e => {
        modalState();
        
        $(e.currentTarget).closest(".modal").fadeOut()

    });


    $(document).on("click", ".header-right__notif", e=> {
        let notifHref = $(e.currentTarget).attr("href");
        window.location.href = notifHref
    })

    modalState = () => {
        if($("body").hasClass("modal-open")) $('body').removeClass('modal-open')
        else  $('body').addClass('modal-open');
    
        setTimeout( () => {
            let hasOpenModal = false;
            $(".modal").each( function(){
                if($(this).css("display") != "none") hasOpenModal = true;
            })
            setTimeout( () => {
                if(hasOpenModal) $('body').addClass('modal-open')
                else $('body').removeClass('modal-open')
            }, 100)
        }, 500)
    }

    $(document).on("click",".my-account.gender .btn-save-gender", e => {
        $.ajax({
            url: `${safarObject.ajaxurl}`,
            data: {
                action: "save_profile_gender",
                gender: $(".field-input.gender input[type=radio]:checked").val()
            },
            type: "post",
            dataType: "json",
            beforeSend: () => {
                $(".my-account.gender .btn-save-gender").fadeTo("fast",.3);
            },
            success: (d) => {
                $(".my-account.gender .btn-save-gender").fadeTo("fast",1).html("Updates saved!");
                window.location.href = "/avatar-store";
            },
            
        });
    })
})


