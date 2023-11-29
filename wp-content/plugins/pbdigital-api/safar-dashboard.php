<?php 
namespace Safar;

class SafarDashboard extends Safar{
  
    static function get_goals( $request ){

        $user_id = parent::pb_auth_user($request);
        if(!empty($user_id)){
            $rs_goals = get_posts(["post_type"=>"goals","numberposts" => -1]);
            $goals = [];
            $earned_goals = gamipress_get_user_achievements( array(
                'user_id'           => $user_id,
                'achievement_type'  => "goals",
                'display'           => true
            ) );

            $earned_post_ids = [];
            foreach($earned_goals as $goal) $earned_post_ids[] = $goal->post_id;

            foreach($rs_goals as $goal){
                $goal_meta = get_post_meta($goal->ID);
                $earned = false;
                if(in_array($goal->ID, $earned_post_ids)) $earned = true;
                $goals[] = [
                    "goal_id" => $goal->ID,
                    "goal_name" => $goal->post_title,
                    "points" => $goal_meta["_gamipress_points"][0],
                    "url" => do_shortcode($goal_meta["url"][0]),
                    "earned" => $earned,
                    "test" => do_shortcode("[bpps_profile_url]")
                ];
            }

            $user_goals_transient = get_transient("user_goals".$user_id);
            $response = [];
            if(!empty($user_goals_transient)){
                $goals = $user_goals_transient;
            }


            $response = new \WP_REST_Response($goals);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function get_recent_activities( $request ){
        $user_id = parent::pb_auth_user($request);
        if(!empty($user_id)){

            $activities = bp_activity_get([ "display_comments"=>true, "filter"=> ["object"=>"groups"] ]);
 
            foreach($activities["activities"] as $key=>$activity){
                
                $groups = groups_get_user_groups( $activity->user_id );
                $group_details = [];
                if(!empty($groups)){
                    foreach($groups["groups"] as $group){
                        $group_details[] = groups_get_group($group);
                    }
                }
                $activities["activities"][$key]->user_group = $group_details;
            }

            $recent_activities = get_transient("recent_activities_".$user_id);
            $response = [];
            if(!empty($recent_activities)){
                $activities["activities"] = $recent_activities;
            }


            $response = new \WP_REST_Response($activities["activities"]);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }
}