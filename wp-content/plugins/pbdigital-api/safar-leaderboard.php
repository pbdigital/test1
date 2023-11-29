<?php 
namespace Safar;


class SafarLeaderboard extends Safar{


	static function get_points_leaderboard( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $gid = $request->get_param("gid");
            $type = $request->get_param("type");

            $date_from = date("Y-m-d 00:00:00");
            $date_to = date("Y-m-d 23:59:59");
            //this month, academic year
            switch($type){
                case "academic year":
                    
                    #$date_from = date("Y-01-01");
                    #$date_to = date("Y-m-d 23:59:59");

                    //Academic year starts on 1st september and ends on 31 august next year
                    // Code needs to by dynamic and take current year into consideration
                    $month_today = date("m");
                    if ($month_today >= 9) {
                        $date_from = date("Y")."-09-01 00:00:00";
                        $date_to = date("Y", strtotime("+1 year"))."-08-31 23:59:59";
                    } else {
                        $date_from = date("Y", strtotime("-1 year"))."-09-01 00:00:00";
                        $date_to = date("Y")."-08-31 23:59:59";
                    }

                    break;
                case "this month": default:
                    $date_from = date("Y-m-01");
                    $date_to = date("Y-m-d 23:59:59");
                    break;
            }

            //".parent::wpdb()->prefix."gamipress_user_earnings

            $members = groups_get_group_members(["group_id"=>$gid, "per_page"=>-1, "group_role"=>["member","mod"]]);
            
            $db_prefix = parent::wpdb()->prefix;
            
            # get points by date 
            $user_ids = [];
            if(!empty($members)){
            foreach($members["members"] as $mem) {
                $user_ids[] = $mem->ID;}
            }
            
            $top_rankings = [];
            $next_rankings = [];
            $belong_to_top = false;
            $top_ranking_user_ids = [];
            $your_ranking_pos = 0;

            if(!empty($user_ids)){
                $sql = "
                    SELECT user_id, sum(CASE 
                            WHEN ( post_type = 'points-award' || points_type = 'points')
                            THEN points 
                            ELSE 0
                        END) as total_points FROM ".parent::wpdb()->prefix."gamipress_user_earnings 
                    WHERE user_id in (".implode(",",$user_ids).")
                    AND ( `date` >= '".$date_from."' AND `date` <= '".$date_to."' )

                    GROUP BY user_id
			        ORDER BY total_points DESC
                " ;
                
           
                $top_points_leaderboard = parent::wpdb()->get_results($sql);

                $user_rank_points = [];
                $ranking_user_ids = [];
                foreach($top_points_leaderboard as $user){
                    $user_rank_points[$user->user_id] = $user->total_points;
                    $ranking_user_ids[] = $user->user_id;
                }

                foreach($user_ids as $euid){
                    if(!in_array($euid, $ranking_user_ids)) $ranking_user_ids[] = $euid;
                }

                $rank = 0;
                foreach($ranking_user_ids as $memb_user_id){
                    $rank++;
                    $user = get_user_by("id", $memb_user_id);
                    $user_data = [
                        "rank" => $rank,
                        "user_id" => $memb_user_id,
                        "name" => $user->data->display_name,
                        "avatar" => get_avatar_url($memb_user_id),
                        "points" => (!empty($user_rank_points[$memb_user_id])) ? $user_rank_points[$memb_user_id]:0,
                        "is_you" => ( $user->data->ID == $user_id ) ? true: false
                    ];
                    
                    if($rank <= 6){
                        if($rank <= 3){
                            $top_rankings[] = $user_data;
                        }else{
                            $next_rankings[] = $user_data;
                        }

                        if($user->data->ID == $user_id ) $belong_to_top = true;
                    }

                    if($user->data->ID == $user_id ){
                        $your_ranking_pos = $rank;
                        $user_data["name"] = "You";
                        $your_ranking = $user_data;
                    }
                }

            }

            if(!$belong_to_top){ // if user does not belong to top replace the #6 with the current logged in user ranking
                if(!empty($your_ranking)) $next_rankings[2] = $your_ranking;
            }

            $response["top_rankings"] = $top_rankings;
            $response["next_rankings"] = $next_rankings;
            $response["belong_to_top"] = $belong_to_top;
            $response["your_ranking"]  = $your_ranking;
            $response["your_ranking_pos"] = $your_ranking_pos;

            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function get_practice_tracker_leaderboard( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $gid = $request->get_param("gid");
            $type = $request->get_param("type");
            $leaderboard = $request->get_param("leaderboard");

            $date_from = date("Y-m-d 00:00:00");
            $date_to = date("Y-m-d 23:59:59");
            //this month, academic year
            switch($type){
                case "academic year":
                    //Academic year starts on 1st september and ends on 31 august next year
                    // Code needs to by dynamic and take current year into consideration
                    $month_today = date("m");
                    if ($month_today >= 9) {
                        $date_from = date("Y")."-09-01 00:00:00";
                        $date_to = date("Y", strtotime("+1 year"))."-08-31 23:59:59";
                    } else {
                        $date_from = date("Y", strtotime("-1 year"))."-09-01 00:00:00";
                        $date_to = date("Y")."-08-31 23:59:59";
                    }
                    
                    break;
                case "this month": default:
                    $date_from = date("Y-m-01");
                    $date_to = date("Y-m-d 23:59:59");
                    break;
            }

            //".parent::wpdb()->prefix."gamipress_user_earnings

            $members = groups_get_group_members(["group_id"=>$gid, "per_page"=>-1, "group_role"=>["member","mod"] ]);
            
            $db_prefix = parent::wpdb()->prefix;
            
            # get points by date 
            $user_ids = [];
            if(!empty($members)){
            foreach($members["members"] as $mem) {
                $user_ids[] = $mem->ID;}
            }
            
            $top_rankings = [];
            $next_rankings = [];
            $belong_to_top = false;
            $top_ranking_user_ids = [];
            $your_ranking_pos = 0;

            if(!empty($user_ids)){

                if($leaderboard == "streak"){
                    
                    // calculate streak per user ids
                    //$response->practice_streak = 0;
                    //$practice_logs = self::practice_logs( $request );
                    //$response->practice_logs_count = count($practice_logs->data);
                    //$response->practice_streak = self::calculate_streak(array_reverse($practice_logs->data));

                    $sql = "SELECT * FROM `".parent::wpdb()->prefix."gamipress_user_earnings` as e
                        LEFT OUTER JOIN `".parent::wpdb()->prefix."gamipress_user_earnings_meta` as em ON e.user_earning_id = em.user_earning_id
                        
                        WHERE post_id = ".PRACTICE_STREAK_POST_ID." 
                            AND ( e.`date` >= '".$date_from."' AND e.`date` <= '".$date_to."' )
                            AND  user_id in (".implode(",",$user_ids).") 
                        GROUP BY user_id, date_format(e.date, '%Y-%m-%d')
                        ORDER BY e.date DESC ";
            
                    $rs_logs = parent::wpdb()->get_results($sql);

                    $practice_logs_all = [];
                    foreach($rs_logs as $log){
                        $practice_logs_all[$log->user_id][] = [
                            "date" => $log->date,
                            "minutes" => $log->meta_value,
                            "earning_id" => $log->user_earning_id,
                            "user_id" => $log->user_id
                        ];
                    }

                    $all_user_streaks = [];
                    foreach($user_ids as $user_id){
                        $logs = (empty($practice_logs_all[$user_id])) ? []:$practice_logs_all[$user_id];
                        $practice_streak = \Safar\SafarUser::calculate_streak(array_reverse($logs));
                        $all_user_streaks[$user_id] = $practice_streak;
                    }

                    arsort($all_user_streaks);

                    foreach($all_user_streaks as $memb_user_id=>$streak){
                        $rank++;
                        $user = get_user_by("id", $memb_user_id);
                        $user_data = [
                            "rank" => $rank,
                            "user_id" => $memb_user_id,
                            "name" => $user->data->display_name,
                            "avatar" => get_avatar_url($memb_user_id),
                            "points" => $streak,
                            "is_you" => ( $user->data->ID == $user_id ) ? true: false
                        ];
                        
                        if($rank <= 6){
                            if($rank <= 3){
                                $top_rankings[] = $user_data;
                            }else{
                                $next_rankings[] = $user_data;
                            }

                            if($user->data->ID == $user_id ) $belong_to_top = true;
                        }

                        if($user->data->ID == $user_id ){
                            $your_ranking_pos = $rank;
                            $user_data["name"] = "You";
                            $your_ranking = $user_data;
                        }
                    }

                    if(!$belong_to_top){ // if user does not belong to top replace the #6 with the current logged in user ranking
                        if(!empty($your_ranking)) $next_rankings[2] = $your_ranking;
                    }
                    
                    // end calculate streak

                }else{
                    $sql = "
                        SELECT user_id, count(*) as total_points FROM ".parent::wpdb()->prefix."gamipress_user_earnings 
                        WHERE user_id in (".implode(",",$user_ids).")
                            AND ( `date` >= '".$date_from."' AND `date` <= '".$date_to."' )
                            AND `title` = 'Daily Lesson Streak'

                        GROUP BY user_id
                        ORDER BY total_points DESC
                    " ;

                    $top_points_leaderboard = parent::wpdb()->get_results($sql);

                    $user_rank_points = [];
                    $ranking_user_ids = [];
                    foreach($top_points_leaderboard as $user){
                        $user_rank_points[$user->user_id] = $user->total_points;
                        $ranking_user_ids[] = $user->user_id;
                    }

                    foreach($user_ids as $euid){
                        if(!in_array($euid, $ranking_user_ids)) $ranking_user_ids[] = $euid;
                    }

                    $rank = 0;

                    foreach($ranking_user_ids as $memb_user_id){
                        $rank++;
                        $user = get_user_by("id", $memb_user_id);
                        $user_data = [
                            "rank" => $rank,
                            "user_id" => $memb_user_id,
                            "name" => $user->data->display_name,
                            "avatar" => get_avatar_url($memb_user_id),
                            "points" => (!empty($user_rank_points[$memb_user_id])) ? $user_rank_points[$memb_user_id]:0,
                            "is_you" => ( $user->data->ID == $user_id ) ? true: false
                        ];
                        
                        if($rank <= 6){
                            if($rank <= 3){
                                $top_rankings[] = $user_data;
                            }else{
                                $next_rankings[] = $user_data;
                            }

                            if($user->data->ID == $user_id ) $belong_to_top = true;
                        }

                        if($user->data->ID == $user_id ){
                            $your_ranking_pos = $rank;
                            $user_data["name"] = "You";
                            $your_ranking = $user_data;
                        }
                    }

                    if(!$belong_to_top){ // if user does not belong to top replace the #6 with the current logged in user ranking
                        if(!empty($your_ranking)) $next_rankings[2] = $your_ranking;
                    }
                    
                    
                } //if($leaderboard == "streak"){

            }

           
            $response["top_rankings"] = $top_rankings;
            $response["next_rankings"] = $next_rankings;
            $response["belong_to_top"] = $belong_to_top;
            $response["your_ranking"]  = $your_ranking;
            $response["your_ranking_pos"] = $your_ranking_pos;
            $response["leaderboard"] = $leaderboard;

            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function quiz_leaderboard(  $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){

            $quiz_id = $request->get_param("id");

        
            $sql = "
                    SELECT DISTINCT e.user_id, (
            
                        SELECT sub_e.points
                    
                        FROM `".parent::wpdb()->prefix."gamipress_user_earnings_meta` as sub_m
                    
                        INNER JOIN `".parent::wpdb()->prefix."gamipress_user_earnings` sub_e ON sub_e.user_earning_id = sub_m.user_earning_id
                    
                        WHERE sub_m.`meta_key` = '_gamipress_completed_quiz_key'  AND sub_m.`meta_value`='completed-quiz-".$quiz_id."'    
                                
                                AND sub_e.user_id = e.user_id

                        ORDER BY sub_e.date DESC 

                        LIMIT 1
                    
                    ) as `points` 
                    
                    FROM `".parent::wpdb()->prefix."gamipress_user_earnings_meta` as m
                    
                    INNER JOIN `".parent::wpdb()->prefix."gamipress_user_earnings` e ON e.user_earning_id = m.user_earning_id

                    LEFT OUTER JOIN `".parent::wpdb()->prefix."usermeta` as umeta ON (umeta.user_id = e.user_id AND umeta.meta_key='is_test_user')
                    
                    WHERE m.`meta_key` = '_gamipress_completed_quiz_key'  AND m.`meta_value`='completed-quiz-".$quiz_id."'

                    AND  ( umeta.meta_value IS NULL or umeta.meta_value = 0 ) 
                    
                    GROUP BY e.user_id 
                    
                    ORDER BY `points` DESC 
            ";
            
            $result = parent::wpdb()->get_results($sql);
            $response = [];
            //Jessy Baker
            $rank = 0;
            foreach($result as $resp){
                $rank = $rank + 1;
                $user = get_user_by("ID", $resp->user_id);
                $response[] = ["rank"=> $rank, 
                                "user_id" => $resp->user_id, 
                                "display_name" => $user->display_name,
                                "points" => $resp->points,
                                "avatar" => get_avatar_url( $resp->user_id ),
                                "is_you" => ( $user_id == $resp->user_id) ? true : false 
                            ];
            }
            
            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

}