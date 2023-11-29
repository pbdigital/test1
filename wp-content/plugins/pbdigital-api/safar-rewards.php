<?php 
namespace Safar;
use Safar\SafarSchool;
use DateTime;

class SafarRewards extends Safar{

    static function reward_student( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $achievement_id = $request->get_param("achievement_id");
            $points = $request->get_param("points");
            $teacher_id = $request->get_param("teacher_id");
            $student_id = $request->get_param("student_id");
            $ld_classroom_id = $request->get_param("ld_classroom_id");

            gamipress_award_achievement_to_user($achievement_id, $student_id);
            gamipress_award_points_to_user( $student_id, $points, '');
            
            $args = [
                "user_id" => $user_id,
                "teacher_id" => $teacher_id,
                "achievement_id" => $achievement_id,
                "points" => $points,
                "ld_classroom_id" => $ld_classroom_id
            ];
            $log_id = parent::wpdb()->insert( parent::wpdb()->prefix."rewards", $args );

            $response = new \WP_REST_Response($response);
            $response["log_id"] = $log_id;

            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function get_rewards_achievemets( $request ){
        $rs = get_posts(["post_type"=>"rewards", "post_status"=>"publish", "numberposts" => -1, "orderby"=>"ID", "order"=>"ASC"]);
        $response = [];

        foreach($rs as $r){
            $response[] = self::get_reward_details($r->ID);
        }

        $response = new \WP_REST_Response($response);
        return $response;
    }

    static function get_reward_details($id){
        $r = get_post($id);
        //$owned = gamipress_get_earnings_count(["post_id"=>$r->ID, "user_id"=>$user_id]);

        $categories = wp_get_post_terms($id, "rewards_category");
        return [
            "ID" => $r->ID,
            "title" => $r->post_title,
            "image" => get_the_post_thumbnail_url($r->ID),
            "slug" => $r->post_name,
            "categories" => $categories
        ];
    }

    static function get_rewards( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $ids = $request->get_param("ids");
            $type = $request->get_param("type");
            $length = $request->get_param("length");
            $page = $request->get_param("page");
            $filter_type = $request->get_param("filter");

            $instituteparent = $request->get_param("instituteparent");
            $childid = $request->get_param("childid");
            if(!empty($instituteparent)){
                unset($ids1);
                $ids = $childid;


            }

           
            $response = self::get_rewards_history_by($type, $ids, 0, $page, $length,$filter_type);
        
            $response = new \WP_REST_Response($response);

            return $response;
        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function get_rewards_history_by($type, $id, $classroom_id, $page=false, $perPage=false, $filter_type=false){

        $where = "WHERE 1 ";
        if(empty($id)) $id[] = 0;
        if(!is_array($id)){
            $arr = [];
            $arr[] = $id;
            $id = $arr;
        }
        

        switch($type){
            case "student":
                $where .= " AND user_id in (".implode(",",$id).") ";
                if(!empty($classroom_id)){
                   $where .= " AND ld_classroom_id='".esc_sql($classroom_id)."' "; 
                }
                break;
            case "teacher":
                $where .= " AND teacher_id in (".implode(",",$id).") ";
                break;
            case "classroom":
                $where .= " AND ld_classroom_id in (".implode(",",$id).") ";
                break;
        }

        if(!empty($filter_type)){
            $where .= " AND `type` = '".esc_sql($filter_type)."' ";
        }

        $limit = "";
        if(!empty($perPage)){
            $limit = "  LIMIT " . ( ($page - 1)* $perPage) . ", " . $perPage;
        }

        $result = parent::wpdb()->get_results("SELECT  * FROM ".parent::wpdb()->prefix."rewards 
        
                                                    ".$where." ORDER BY datetime DESC ".$limit);

        $totalRows = parent::wpdb()->get_var("SELECT COUNT(*) FROM " . parent::wpdb()->prefix . "rewards " . $where);

        $totalpoints = parent::wpdb()->get_results("SElECT sum(`points`) as total FROM ".parent::wpdb()->prefix."rewards 
        
                                                    ".$where);

        $grouped_history = [];
        $rewards = [];
        foreach($result as $rs){
            $rs->datetime = date("Y-m-d H:i:s", strtotime($rs->datetime));
            $rs->achievement_details = self::get_reward_details($rs->achievement_id);
            $school_post = get_post($rs->ld_classroom_id);
            $teacher_id = $rs->teacher_id;
            $rs->teacher_details = [
                "avatar" => get_avatar_url($teacher_id),
                "firstName" => get_user_meta($teacher_id, "first_name", true), // string - teacher's first name
                "lastName" => get_user_meta($teacher_id, "last_name", true), // string - teacher's last name
                "classroom" => $school_post->post_title // string - classroom associated with the teacher
            ];
            $grouped_history[date("Y-m-d",strtotime($rs->datetime))][] = $rs;
            $rewards[] = $rs;
        }

        $response["totalpoints"] = (empty($totalpoints) || empty($totalpoints[0]->total)) ? 0 :$totalpoints[0]->total;
        $response["rewards"] = $grouped_history;
        $response["rewards_ungrouped"] = $rewards;
        $response["total"] = $totalRows;
        return $response;
    }

    static function clear_rewards( $request ){
        $user_id = parent::pb_auth_user($request);
        
        if(!empty($user_id)){
            $ids = $request->get_param("ids");

            $success = false;
            if(!empty($ids)){
                foreach($ids as $id){
                    $success = parent::wpdb()->update(parent::wpdb()->prefix."rewards", ["read_notification"=>1], ["id"=>$id]);
                    
                }
            }

            $response["success"] = $success;
            $response = new \WP_REST_Response($response);
            return $response;
        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function save_rewards($request){
        $teacher_id = parent::pb_auth_user($request); 
        if(!empty($teacher_id)){
 
            $is_multiple = filter_var( $request->get_param("is_multiple"), FILTER_VALIDATE_BOOLEAN);
            $single_student = $request->get_param("single_student");
            $student_ids = $request->get_param("student_ids");
            $type = $request->get_param("type");
            $achievement = $request->get_param("achievement");
            $points = $request->get_param("points");
            $classroom_id = $request->get_param("classroom_id");
            $comment = $request->get_param("comment");

            #print_r([$is_multiple, $single_student, $student_ids, $type, $achievement, $points, $classroom_id, $comment]);
            
            if($is_multiple){
               
                foreach($student_ids as $student_id){
                    $args = [
                        "user_id" => $student_id,
                        "teacher_id" => $teacher_id,
                        "achievement_id" => $achievement,
                        "points" => $points,
                        "ld_classroom_id" => $classroom_id,
                        "type" => $type,
                        "comment" => $comment
                    ];
                    parent::wpdb()->insert( parent::wpdb()->prefix."rewards", $args );
                }
            }else{
                $args = [
                    "user_id" => $single_student,
                    "teacher_id" => $teacher_id,
                    "achievement_id" => $achievement,
                    "points" => $points,
                    "ld_classroom_id" => $classroom_id,
                    "type" => $type,
                    "comment" => $comment
                ];
               
                $resp = parent::wpdb()->insert( parent::wpdb()->prefix."rewards", $args );
            }


            $response["success"] = true;
            $response["is_multiple"] = $is_multiple;
            $response = new \WP_REST_Response($response);

            return $response;
        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }
}