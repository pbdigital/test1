<?php
/** Template Name: Leaderboard */
wp_enqueue_style( 'leaderboard-css', get_stylesheet_directory_uri() .'/assets/css/leaderboard.css',array(), time() );
wp_enqueue_script('leaderboard-js');
get_header();


$course_id = get_post_meta( $_GET["quiz"], "course_id", true );
#$back_link = get_permalink($course_id);
$course_details = get_post($course_id);
if($course_details->post_status=="publish") $back_link = get_permalink($course_id);
else $back_link = site_url();
?>
<main class="leaderboard">
    <a href="<?=$back_link?>" class="btn-back" ><img src="<?=get_stylesheet_directory_uri();?>/assets/img/back.png" alt="Back to Course"></a>
    <div class="stars-wrapper">
        <img src="<?=get_stylesheet_directory_uri();?>/assets/img/stars1.svg" alt="Theme 1 Stars">
    </div>
    <svg class="clouds-container"  viewBox="0 0 1237 316" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g class="clouds">
        <path opacity="0.05" d="M1224.77 305.628C1224.02 305.628 1223.29 305.712 1222.59 305.822C1222.64 305.157 1222.7 304.519 1222.7 303.853C1222.7 290.154 1211.26 279.034 1197.15 279.034C1186.02 279.034 1176.59 285.939 1173.09 295.589C1170.51 294.12 1167.51 293.26 1164.28 293.26C1158.2 293.26 1152.87 296.31 1149.75 300.886C1146.22 298.584 1141.99 297.226 1137.42 297.226C1136.04 297.226 1134.72 297.364 1133.43 297.586C1133.52 296.394 1133.63 295.229 1133.63 294.009C1133.63 269.161 1112.88 249 1087.3 249C1067.14 249 1050.03 261.535 1043.66 279.006C1038.98 276.344 1033.51 274.791 1027.71 274.791C1012.39 274.791 999.606 285.329 996.634 299.361C993.717 298.002 990.492 297.226 987.043 297.226C975.741 297.226 966.43 305.406 965 316H1237C1236.19 310.149 1231.05 305.656 1224.83 305.656L1224.77 305.628Z" fill="white"/>
        <path opacity="0.05" d="M159.969 201.97C158.779 193.29 151.114 186.57 141.832 186.57C140.702 186.57 139.634 186.69 138.565 186.871C138.656 185.907 138.718 184.912 138.718 183.948C138.718 163.545 121.649 147 100.611 147C84.0305 147 69.9542 157.307 64.7023 171.652C60.855 169.452 56.3664 168.186 51.5725 168.186C38.9618 168.186 28.458 176.836 26.0153 188.348C23.6336 187.233 20.9466 186.6 18.1374 186.6C8.85496 186.6 1.19084 193.321 0 202H160L159.969 201.97Z" fill="white"/>
        <path opacity="0.15" d="M1087 49C1087 40.5499 1080.16 33.7229 1071.69 33.7229C1068.24 33.7229 1065.07 34.8822 1062.51 36.7886C1060.2 28.5189 1052.6 22.439 1043.57 22.439C1033.43 22.439 1025.09 30.0904 1024.03 39.9317C1021.9 38.2056 1019.24 37.1236 1016.27 37.1236C1016.04 37.1236 1015.84 37.1751 1015.61 37.2008C1016.04 35.1656 1016.27 33.0273 1016.27 30.8633C1016.27 13.8086 1002.43 0 985.368 0C970.614 0 958.293 10.3307 955.226 24.1393C952.792 23.0573 950.08 22.439 947.24 22.439C937.582 22.439 929.571 29.3948 927.898 38.5405C926.199 37.6646 924.323 37.1236 922.27 37.1236C915.603 37.1236 910.228 42.4048 910 49H1086.95H1087Z" fill="white"/>
        </g>
    </svg>
    <div class="leaderboard-quiz">
        <h1>Quiz Leaderboard</h1>
        <div class="leaderboard-top">
             
        </div>
        <div class="leaderboard-list">
            <div class="leaderboard-list__heading">
                <div class="leaderboard-list__head">Rank</div>
                <div class="leaderboard-list__head">Student</div>
                <div class="leaderboard-list__head">Score</div>
            </div>
            <div class="leaderboard-ranking">
                 

            </div>

            


        </div>

        

    </div>

    <a href="<?=$back_link?>" class="btn-continue-course">CONTINUE TO COURSE</a>
    
    <svg viewBox="0 0 1440 283" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M153.643 145.458C262.978 139.338 621.983 85.1304 632.283 85.9016C632.283 85.9016 679.724 139.363 800.429 131.726C921.133 124.088 1096.49 77.4931 1155.28 72.9156C1155.28 72.9156 1186.23 120.257 1241.93 119.511C1297.63 118.74 1199.64 240.987 1000.54 251.634C801.449 262.232 153.643 145.458 153.643 145.458Z" fill="#841D96"/>
    <path d="M1241.93 119.486C1191.87 120.182 1161.82 82.0453 1156.25 74.2836C1042.14 71.9949 735.126 231.036 663.404 210.686C598.25 192.202 601.907 137.074 632.283 85.8764C582.453 88.9612 256.386 139.711 153.643 145.433C153.643 145.433 801.473 262.207 1000.57 251.584C1199.66 240.962 1297.65 118.715 1241.95 119.461L1241.93 119.486Z" fill="#581564"/>
    <path d="M368.11 204.94C372.563 203.571 810.056 88.0161 810.056 88.0161L1147.47 192.551L708.706 218.672L368.11 204.915V204.94Z" fill="#2C033B"/>
    <path d="M813.165 88.9863C819.012 116.127 796.97 155.259 720.273 218L1147.44 192.551L813.165 88.9863Z" fill="#671A74"/>
    <path d="M719.95 201.855C508.941 221.11 251.634 72.8906 0 72.8906V322.535C566.855 322.684 922.352 318.156 1440 299.847V0C1268.07 0 990.243 177.176 719.975 201.855H719.95Z" fill="#4B0F55"/>
    <path d="M722.015 265.466C511.005 282.656 251.634 99.5094 0 99.5094V322.535C566.855 322.659 922.352 318.629 1440 302.26V0C1268.07 0 992.307 243.425 722.04 265.466H722.015Z" fill="#2C033B"/>
    </svg>

</main>
<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/3.6.0/gsap.min.js"></script>
<script>
    var g = document.querySelector(".clouds");
    var demo = document.querySelector(".clouds-container");
    var cloudCopy = g.cloneNode(true);
    demo.appendChild(cloudCopy);
    TweenMax.set(cloudCopy,{x:"100%"});
    TweenMax.to("g", 100, {x:"-=100%", ease:Linear.easeNone, repeat:-1,});
</script>


<?php 

add_action("wp_footer", function(){
    ?>
    <script type="text/javascript">
        jQuery(document).ready( $ => {

            SafarLeaderBoard.quizid = "<?=$_GET["quiz"];?>";
            SafarLeaderBoard.init();

        });
    </script> 
    <?php
});

get_footer();