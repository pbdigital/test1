<?php 
function members_group_type( $type = ["teacher","student"]){
    ob_start();
    while ( bp_group_members() ) :
        bp_group_the_member();

        $role = bp_get_user_group_role_title( bp_get_member_user_id(), bp_get_current_group_id() );
        $user_sub_role = get_user_meta(bp_get_member_user_id(),"user_role", true);
        
        if($user_sub_role == "teacher"){ 
            $role = str_replace("Organizer", "Teacher", $role);
        }else{
            $role = str_replace("Organizer", "Admin", $role);
        }
        
        $role = str_replace("Member", "Student", $role);

        if(in_array(strtolower($role), $type)){
            
            ob_start();
            bp_group_member_section_title();
            $out = ob_get_contents();
            ob_end_clean();
            
            $out = str_replace("Members","Students",$out);
            if($user_sub_role == "teacher"){ 
                $out = str_replace("Organizers","Teachers",$out);
            }else{
                $out = str_replace("Organizers","Admin",$out);
            }

            #echo $out;

            // Check if members_list_item has content.
            ob_start();
            bp_nouveau_member_hook( '', 'members_list_item' );
            $members_list_item_content = ob_get_clean();
            $member_loop_has_content   = ! empty( $members_list_item_content );

            // Get member followers element.
            $followers_count = '';
            if ( $enabled_followers && function_exists( 'bb_get_followers_count' ) ) {
                ob_start();
                bb_get_followers_count( bp_get_member_user_id() );
                $followers_count = ob_get_clean();
            }

            // Member joined data.
            $member_joined_date = bp_get_group_member_joined_since();

            // Member last activity.
            $member_last_activity = bp_get_last_activity( bp_get_member_user_id() );

            // Primary and secondary profile action buttons.
            $profile_actions = bb_member_directories_get_profile_actions( bp_get_member_user_id() );

            // Member switch button.
            $member_switch_button = bp_get_add_switch_button( bp_get_member_user_id() );

            // Get Primary action.
            $primary_action_btn = function_exists( 'bb_get_member_directory_primary_action' ) ? bb_get_member_directory_primary_action() : '';
            ?>
            <li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr( bp_get_group_member_id() ); ?>" data-bp-item-component="members" testaaa >
                <div class="list-wrap <?php echo esc_attr( $footer_buttons_class ); ?> <?php echo esc_attr( $follow_class ); ?> <?php echo $member_loop_has_content ? esc_attr( ' has_hook_content' ) : esc_attr( '' ); ?> <?php echo ! empty( $profile_actions['secondary'] ) ? esc_attr( 'secondary-buttons' ) : esc_attr( 'no-secondary-buttons' ); ?> <?php echo ! empty( $primary_action_btn ) ? esc_attr( 'primary-button' ) : esc_attr( 'no-primary-buttons' ); ?>">

                    <div class="list-wrap-inner">
                        <div class="item-avatar">
                            <a href="<?php bp_group_member_domain(); ?>">
                                <?php
                                if ( $enabled_online_status ) {
                                    bb_current_user_status( bp_get_group_member_id() );
                                }
                                bp_group_member_avatar();
                                ?>
                            </a>
                        </div>

                        <div class="item">

                            <div class="item-block">

                                <?php
                                
                                $role = '<span class="bp-member-type bb-current-member-'.strtolower($role).'" role >'.$role.'</span>';
                                echo '<p class="item-meta member-type only-grid-view">' . $role . '</p>';
                                
                                ?>

                                <h2 class="list-title member-name">
                                    <a href="<?php bp_member_permalink(); ?>" >
                                        <?php
                                        $member_user_id = bp_get_member_user_id();
                                        echo get_user_meta($member_user_id,"first_name", true)." ".get_user_meta($member_user_id, "last_name", true); 
                                        #bp_member_name(); 
                                        ?>
                                    </a>
                                </h2>

                                <?php
                                if ( $enabled_profile_type && function_exists( 'bp_member_type_enable_disable' ) && true === bp_member_type_enable_disable() && true === bp_member_type_display_on_profile() ) {
                                    echo '<p class="item-meta member-type only-list-view">' . $role . '</p>';
                                }
                                ?>

                                <?php if ( ( $enabled_last_active && $member_last_activity ) || ( $enabled_joined_date && $member_joined_date ) ) : ?>
                                    <p class="item-meta last-activity">

                                        <?php
                                        if ( $enabled_joined_date ) :
                                            echo wp_kses_post( $member_joined_date );
                                        endif;
                                        ?>

                                        <?php if ( ( $enabled_last_active && $member_last_activity ) && ( $enabled_joined_date && $member_joined_date ) ) : ?>
                                            <span class="separator">&bull;</span>
                                        <?php endif; ?>

                                        <?php
                                        if ( $enabled_last_active ) :
                                            echo wp_kses_post( $member_last_activity );
                                        endif;
                                        ?>

                                    </p>
                                <?php endif; ?>


                                <?php 
                                $earned_badges = gamipress_get_user_achievements( array(
                                    'user_id'           => bp_get_group_member_id(),
                                    'achievement_type'  => "badges",
                                    'display'           => true
                                ) );

                                $earned_badge_ids = gamipress_get_user_earned_achievement_ids( bp_get_group_member_id(), 'badges' );
                                
                                if(!empty($earned_badges)){
                                    ?>
                                    <div class="earned-badges">
                                    <?php
                                    $i = 0;
                                    $has_more = 0;
                                    foreach($earned_badges as $badge){
                                        if($i < 3){
                                        ?>
                                        <img style="max-width:40px" src="<?=get_the_post_thumbnail_url($badge->ID)?>" class="badge" data-userid="<?=bp_get_group_member_id()?>"/>
                                        <?php
                                        }else{
                                            $has_more++;
                                        }

                                        $i++;
                                    }
                                    if(!empty($has_more)){
                                        ?>
                                        <span>+ <?=$has_more?></span>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                    <?php
                                }
                            
                                ?>
                                
                                
                            </div>

                            <!-- <div class="flex align-items-center follow-container justify-center">
                                <?php echo wp_kses_post( $followers_count ); ?>
                            </div> -->

                            <!-- <div class="flex only-grid-view align-items-center primary-action justify-center">
                                <?php echo wp_kses_post( $profile_actions['primary'] ); ?>
                            </div> -->

                        </div><!-- // .item -->

                        <div class="member-buttons-wrap">

                            <?php if ( $profile_actions['secondary'] ) { ?>
                                <div class="flex only-grid-view button-wrap member-button-wrap footer-button-wrap">
                                    <?php echo wp_kses_post( $profile_actions['secondary'] ); ?>
                                </div>
                            <?php } ?>

                            <?php if ( $profile_actions['primary'] ) { ?>
                                <div class="flex only-list-view align-items-center primary-action justify-center">
                                    <?php echo wp_kses_post( $profile_actions['primary'] ); ?>
                                </div>
                            <?php } ?>


                            

                        </div><!-- .member-buttons-wrap -->

                    

                    </div>

                    <div class="bp-members-list-hook">
                        <?php if ( $member_loop_has_content ) { ?>
                            <a class="more-action-button" href="#"><i class="bb-icon-menu-dots-h"></i></a>
                        <?php } ?>
                        <div class="bp-members-list-hook-inner">
                            <?php bp_nouveau_member_hook( '', 'members_list_item' ); ?>
                        </div>
                    </div>

                    <?php if ( ! empty( $member_switch_button ) ) { ?>
                    <div class="bb_more_options member-dropdown">
                        <a href="#" class="bb_more_options_action">
                            <i class="bb-icon-menu-dots-h"></i>
                        </a>
                        <div class="bb_more_options_list">
                            <?php echo wp_kses_post( bp_get_add_switch_button( bp_get_member_user_id() ) ); ?>
                        </div>
                    </div><!-- .bb_more_options -->
                    <?php } ?>

                    
                </div>
            </li>

    <?php 
        }
    endwhile; 

    $members_loop = ob_get_contents();
    ob_end_clean();

    return $members_loop;
}