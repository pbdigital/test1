<?php 
/** Template Name: Create Sandbox */
wp_enqueue_style('family-onboarding-css');

get_header();
$user = wp_get_current_user();
?>
<style type="text/css">
    div#content{
        min-height:100vh;
        display: flex;
        align-items:center;
        justify-content:center;
        width:100%;
    }
    div#content .container{
        width:100%;
    }
    header#masthead{
        display:none;
    }
    .sandbox-container{
        max-width:500px;
        width:100%;
        background: #FFFFFF;
        box-shadow: 0px 25px 50px rgba(93, 83, 192, 0.05);
        padding:70px 0px;
        display: flex;
        align-items:center;
        justify-content:center;
        flex-direction:column;
        margin:auto;
        min-width:100%;
        margin-top:40px;
    }
    .sandbox-container p{
        font-family: 'Mikado';
        font-style: normal;
        font-weight: 500;
        font-size: 20px;
        line-height: 32px;
        text-align: center;
        color: #37394A;
        margin-top:30px;
    }
    .sandbox-container .spinner {
        text-align: center;
        -webkit-animation: spin 2s linear infinite;
                animation: spin 2s linear infinite;
    }
</style>
<main id="main" class="site-main">
    
    <div class="school-onboarding-container">
        <div class="sandbox-container">
            <div class="spinner">
            <img src="/wp-content/themes/buddyboss-theme-child/assets/img/family-onboarding/spinner.png"/>
            </div>

            <p class="please-wait">Please wait while we prepare<br/>your demo account...</p>
        </div>    
    </div>
   
</main>



<?php 
add_action("wp_footer", function(){
    $user_id = get_current_user_id();
    if(empty($user_id)){
        $redirect = false;
        if(isset($_GET["redirect"])){
            $redirect = $_GET["redirect"];
        }
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script type="text/javascript">
        jQuery(document).ready( $ => {
            $.ajax({
                url: `${safarObject.apiBaseurl}/user/sandbox`,
                type: "post",
                data: {
                    tz: moment.tz.guess(),
                    redirect: "<?=$redirect?>",
                },
                headers: {
                    "X-WP-Nonce": safarObject.wpnonce
                },
                beforeSend: (xhr) => {

                },
                success: () => {
                    console.log("sandbox created")

                    const now = moment();
                    localStorage.setItem('currentDateTime', now.format());
                    localStorage.removeItem('submitted_mailchimp_form');

                    setTimeout( e => {
                        window.location.href="<?=(isset($_GET["redirect"])) ? $_GET["redirect"]:site_url()?>"
                    }, 3000)
                },
                error: () => {
                    console.log("error creating sandbox")
                }
            });
        });
    </script>
    <?php
    }else{
        ?>
        <script type="text/javascript"> window.location.href="<?=site_url()?>" </script>
        <?php
    }
});
get_footer();
?>