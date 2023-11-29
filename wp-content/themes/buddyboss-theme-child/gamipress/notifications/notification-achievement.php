<?php
/**
 * Achievement Notification template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/notifications/notification-achievement.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/notifications/notification-achievement-{achievement-type}.php
 */
global $gamipress_notifications_template_args;

// Shorthand
$a = $gamipress_notifications_template_args; 

$slug = get_post_field( 'post_name', get_post() );
?>

<div id="gamipress-achievement-<?php the_ID(); ?>-<?php echo $a['earning']->user_earning_id; ?>" class="gamipress-notification-achievement gamipress-notification-achievement-type-<?php echo gamipress_get_post_type( get_the_ID() ); ?> gamigress-achievement-<?=$slug?>">

    <?php
    /**
     * Before render the achievement notification
     *
     * @since 1.0.0
     *
     * @param integer $achievement_id   The Achievement ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_notifications_before_render_achievement_notification', get_the_ID(), $a ); ?>

    <?php /*
    <div class="gamipress-notification-description gamipress-notification-achievement-description">
        <img src="<?=get_the_post_thumbnail_url(get_the_id())?>"/>
        
        <h2>Congratulations!</h2>
        <p><?php 
                echo get_post_meta(get_the_id(),"_gamipress_congratulations_text",true) ;
        ?></p>

    </div>
    */

    $required_achievements = gamipress_get_required_achievements_for_achievement( get_the_ID() , 'publish' );
    $required_title = "";
    if(!empty($required_achievements)){
        $required_title = $required_achievements[0]->post_title;
    }
    $default_message = " You have unlocked the ".get_the_title().".<br/>".$required_title.". Keep it up! ";
    $congrats_text= get_post_meta(get_the_id(),"_gamipress_congratulations_text",true) ? get_post_meta(get_the_id(),"_gamipress_congratulations_text",true) : $default_message;
    ?>

    <div id="achievement-modal" class="modal success-entry-box">  
        <div class="modal-content">
            <div class="modal-body">
                <a style="position:absolute;z-index:999999" type="button" class="close" >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="24" fill="#B0D178"></rect>
                        <path d="M18 6L6 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
                <div class="w-info-box popup_box">
                    <div id="" class="">				
                        <div class="col-width-12 text-center">
                            <div class="dv-image-casc" style="
                                display: flex;
                                justify-content: center;
                            ">
                        
                                <img style="position:relative;top:auto; left:auto" class="bgcircle" src="<?=get_the_post_thumbnail_url(get_the_id())?>">
                        
                            </div>										
                            <div class="dv-heading" style="padding-bottom:0px;">
                                <h3>Congratulations!</h3>
                            </div>
                            <div class="dv-txt">
                                <p><?php echo $congrats_text ; ?></p>
                            </div>					
                        </div>
                    </div>
                </div> 
            </div>  
        </div>
        </div>
    <?php
    /**
     * After render the achievement notification
     *
     * @since 1.0.0
     *
     * @param integer $achievement_id   The Achievement ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_notifications_after_render_achievement_notification', get_the_ID(), $a ); ?>

</div>

<script type="text/javascript">
    jQuery(document).ready( () => {
        $ = jQuery;
        var confetti = {
        maxCount: 150,
        speed: 2,
        frameInterval: 15,
        alpha: 1,
        gradient: !1,
        start: null,
        stop: null,
        toggle: null,
        pause: null,
        resume: null,
        togglePause: null,
        remove: null,
        isPaused: null,
        isRunning: null
        };
        !(function () {
        (confetti.start = s),
            (confetti.stop = w),
            (confetti.toggle = function () {
            e ? w() : s();
            }),
            (confetti.pause = u),
            (confetti.resume = m),
            (confetti.togglePause = function () {
            i ? m() : u();
            }),
            (confetti.isPaused = function () {
            return i;
            }),
            (confetti.remove = function () {
            stop(), (i = !1), (a = []);
            }),
            (confetti.isRunning = function () {
            return e;
            });
        var t =
            window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame,
            n = [
            "rgba(213, 103, 96,",
            "rgba(159, 203, 111, ",
            "rgba(97, 150, 238,",
            "rgba(170, 125, 225,",
            "rgba(238, 199, 119,",
            // "rgba(152,251,152,",
            // "rgba(70,130,180,",
            // "rgba(244,164,96,",
            // "rgba(210,105,30,",
            // "rgba(220,20,60,"
            ],
            e = !1,
            i = !1,
            o = Date.now(),
            a = [],
            r = 0,
            l = null;
        function d(t, e, i) {
            return (
            (t.color = n[(Math.random() * n.length) | 0] + (confetti.alpha + ")")),
            (t.color2 = n[(Math.random() * n.length) | 0] + (confetti.alpha + ")")),
            (t.x = Math.random() * e),
            (t.y = Math.random() * i - i),
            (t.diameter = 10 * Math.random() + 5),
            (t.tilt = 10 * Math.random() - 10),
            (t.tiltAngleIncrement = 0.07 * Math.random() + 0.05),
            (t.tiltAngle = Math.random() * Math.PI),
            t
            );
        }
        function u() {
            i = !0;
        }
        function m() {
            (i = !1), c();
        }
        function c() {
            if (!i)
            if (0 === a.length)
                l.clearRect(0, 0, window.innerWidth, window.innerHeight), null;
            else {
                var n = Date.now(),
                u = n - o;
                (!t || u > confetti.frameInterval) &&
                (l.clearRect(0, 0, window.innerWidth, window.innerHeight),
                (function () {
                    var t,
                    n = window.innerWidth,
                    i = window.innerHeight;
                    r += 0.01;
                    for (var o = 0; o < a.length; o++)
                    (t = a[o]),
                        !e && t.y < -15
                        ? (t.y = i + 100)
                        : ((t.tiltAngle += t.tiltAngleIncrement),
                            (t.x += Math.sin(r) - 0.5),
                            (t.y += 0.5 * (Math.cos(r) + t.diameter + confetti.speed)),
                            (t.tilt = 15 * Math.sin(t.tiltAngle))),
                        (t.x > n + 20 || t.x < -20 || t.y > i) &&
                        (e && a.length <= confetti.maxCount
                            ? d(t, n, i)
                            : (a.splice(o, 1), o--));
                })(),
                (function (t) {
                    for (var n, e, i, o, r = 0; r < a.length; r++) {
                    if (
                        ((n = a[r]),
                        t.beginPath(),
                        (t.lineWidth = n.diameter),
                        (i = n.x + n.tilt),
                        (e = i + n.diameter / 2),
                        (o = n.y + n.tilt + n.diameter / 2),
                        confetti.gradient)
                    ) {
                        var l = t.createLinearGradient(e, n.y, i, o);
                        l.addColorStop("0", n.color),
                        l.addColorStop("1.0", n.color2),
                        (t.strokeStyle = l);
                    } else t.strokeStyle = n.color;
                    t.moveTo(e, n.y), t.lineTo(i, o), t.stroke();
                    }
                })(l),
                (o = n - (u % confetti.frameInterval))),
                requestAnimationFrame(c);
            }
        }
        function s(t, n, o) {
            var r = window.innerWidth,
            u = window.innerHeight;
            window.requestAnimationFrame =
            window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            function (t) {
                return window.setTimeout(t, confetti.frameInterval);
            };
            var m = document.getElementById("confetti-canvas");
            null === m
            ? ((m = document.createElement("canvas")).setAttribute(
                "id",
                "confetti-canvas"
                ),
                m.setAttribute(
                "style",
                "display:block;z-index:999999;pointer-events:none;position:fixed;top:0"
                ),
                document.body.prepend(m),
                (m.width = r),
                (m.height = u),
                window.addEventListener(
                "resize",
                function () {
                    (m.width = window.innerWidth), (m.height = window.innerHeight);
                },
                !0
                ),
                (l = m.getContext("2d")))
            : null === l && (l = m.getContext("2d"));
            var s = confetti.maxCount;
            if (n)
            if (o)
                if (n == o) s = a.length + o;
                else {
                if (n > o) {
                    var f = n;
                    (n = o), (o = f);
                }
                s = a.length + ((Math.random() * (o - n) + n) | 0);
                }
            else s = a.length + n;
            else o && (s = a.length + o);
            for (; a.length < s; ) a.push(d({}, r, u));
            (e = !0), (i = !1), c(), t && window.setTimeout(w, t);
        }
        function w() {
            e = !1;
        }
        })();


        if( !$("body").hasClass("page-template-choose-avatar") ){

            let isDemo = "<?=\Safar\SafarUser::is_demo_user()?>";
            show = true;

            if(isDemo && $slug == "welcome"){
                show = false;
            }

            if(show){

                confetti.start();
                
                setTimeout( () => {
                    confetti.stop();
                }, 3000)


                $("#achievement-modal").css({"display":"flex"});
                $('body').addClass('modal-open');
            }
        }
        
    });
</script>