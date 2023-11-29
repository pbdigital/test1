
<div class="email-content-template" style="background:#F8F7FF">
    <div class="top" style="
        background: #FFFFFF;
        box-shadow: 0px 10px 20px rgba(93, 83, 192, 0.05);
        margin:0px;
        margin-bottom:40px;
        display:flex;
        align-items:center;
    " >
        <div class="container" style="width:544px; margin:auto; display:flex; align-items:center;">
            <table width="100%" style="width:100%">
                <tr><td>
                <h2 style="color: var(--brand-purple, #5D53C0);
                font-family: Arial;
                font-size: 18px;
                font-style: normal;
                font-weight: 700;
                line-height: normal;
                margin-top:30px;
                margin-bottom:30px;"><?=$school["post"]->post_title?></h2>
                </td><td style="vertical-align: middle; text-align:right">
                <div>
                    <?php 
                    if(!empty($school["post"]->avatar)){
                        ?>
                        <img src="<?=$school["post"]->avatar?>" style="max-height:40px"/>
                        <?php
                    }
                    ?>
                    </div> 
                </td></tr>
            
            </table>
        </div>
    </div>


    <div style="width:544px; margin:auto; background:#ffffff;">
        <div class="container" style="width:100%; margin:auto">
            <div class="header" style="margin:0px 0px -6px 0px">
                <?php 
                if( $args["email_type"] == "teacher" || $args["email_type"] == "parent" || $args["email_type"] == "institute_parent"){
                    ?>
                    <div style="margin-top: -360px;">
                       
                        <table style="width:100%;height: 360px;color: #fff;background-image:url('<?=site_url("/wp-content/themes/buddyboss-theme-child/assets/img/email-template-header-teacher-v2.png")?>')">
                            <tr>
                                <td style="
        color: #fff;
        vertical-align: top;
        text-align: center;
        padding-top: 0px;
    ">
                                     <table align="center">
                                            <tr><td style="
                                    color: #fff;
                                    vertical-align: top;
                                    text-align: center;
                                    padding-top: 30px;
                                "><img src="<?=site_url("/wp-content/themes/buddyboss-theme-child/assets/img/logo-email.png")?>"/></td></tr>
                                        </table>
                                    <?php 
                                    if( $args["email_type"] == "teacher" ) $greetings = get_field("teacher_welcome_email_greetings","option");
                                    else $greetings = get_field("family_welcome_email_greetings","option"); 
                                    
                                    echo $greetings;
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                }else{
                    ?>
                    <img src="<?=site_url("/wp-content/themes/buddyboss-theme-child/assets/img/email-template-header.png")?>" style="width:100%"/>
                    <?php
                }
                
                ?>
                
            </div>
        </div>

        <div class="body" style="width:100%; margin:auto;  font-family: 'Mikado';
            font-style: normal;
            font-weight: 400;
            font-size: 16px;
            line-height: 150%;
            letter-spacing: 0.2px;
            color: #37394A;
            background:#ffffff;
            margin-top:-5px;
            ">
            <div style="margin:0px 40px; padding:40px 0px; margin-top:-5px"><?=$body?></div>
        </div>
    </div>

</div>