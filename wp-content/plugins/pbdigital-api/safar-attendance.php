<?php 
namespace Safar;
use Safar\SafarSchool;
use Safar\SafarUser;
use DateTime;

class SafarAttendance extends Safar{

    static function get_student_attendance_in_classroom($user_id, $classroom_id, $date){
        if(empty($date)){
            $date = date("Y-m-d"); // Server date, default today
        }else{
            $date = date("Y-m-d",strtotime($date));   
        }

        $wpdb = parent::wpdb();
        $query = 
            "SELECT * FROM {$wpdb->prefix}attendance_details AS d
                INNER JOIN {$wpdb->prefix}attendance AS a ON d.attendance_id = a.id
            WHERE d.user_id = '".esc_sql($user_id)."'
                AND DATE_FORMAT(a.date_time, '%Y-%m-%d') = '".esc_sql($date)."'
                AND a.classroom_id = '".esc_sql($classroom_id)."'";
       
        $results = $wpdb->get_results($query);


        if(empty($results)){
            return "present"; // default is present
        }else{
            return $results[0]->status;
        }
    }

    static function classroom_has_attendance($classroom_id, $date){
        if(empty($date)) $date = date("Y-m-d"); // default toda

        $query = "SELECT * FROM `".parent::wpdb()->prefix."attendance` 
                            WHERE classroom_id='".esc_sql($classroom_id)."'
                                AND DATE_FORMAT(date_time, '%Y-%m-%d') = '".esc_sql(date("Y-m-d",strtotime($date)))."'";
        $results = parent::wpdb()->get_results($query);

        if(empty($results)) return false;
        else return $results[0];
    }

    static function save_attendance($request){
        $user_id = parent::pb_auth_user($request);
       
        if(!empty($user_id)){
            $classroom_id = $request->get_param("classroom_id");
            $attendance_status = $request->get_param("attendance_status");
            $date = date("Y-m-d H:i:s");
            $has_attendance = self::classroom_has_attendance($classroom_id, $date);

            $present_count = 0;
            $late_count = 0;
            $absent_count = 0;

            foreach($attendance_status as $student_id=>$status){
                if($status=="present") $present_count++;
                if($status=="late") $late_count++;
                if($status=="absent") $absent_count++;
            }
            $table_name = parent::wpdb()->prefix . 'attendance';

            if($has_attendance){
                // update
                parent::wpdb()->update($table_name, ["date_time"=>date("Y-m-d H:i:s")], ["id"=>$has_attendance->id]); // update attendance with current date
                $table_name = parent::wpdb()->prefix . 'attendance_details';
                parent::wpdb()->delete( $table_name, array( 'attendance_id' => $has_attendance->id ) );
                foreach($attendance_status as $student_id=>$status){
                    $data = [
                        'user_id'    => $student_id,
                        'attendance_id' => $has_attendance->id,
                        'status'    => $status
                    ];
                    parent::wpdb()->insert($table_name, $data);
                }

                $response["attendance_id"] = $has_attendance->id;
                $response["save_type"] = "update";
            }else{
                
                $data = [
                    'teacher_id'    => $user_id,
                    'classroom_id'  => $classroom_id,
                    'date_time'     => $date,
                    'present_count' => $present_count,
                    'late_count'    => $late_count,
                    'absent_count'  => $absent_count
                ];
                
                parent::wpdb()->insert($table_name, $data);
                $attendance_id = parent::wpdb()->insert_id;

                $table_name = parent::wpdb()->prefix . 'attendance_details';
                foreach($attendance_status as $student_id=>$status){
                    $data = [
                        'user_id'    => $student_id,
                        'attendance_id' => $attendance_id,
                        'status'    => $status
                    ];
                    parent::wpdb()->insert($table_name, $data);
                }

                $response["attendance_id"] = $attendance_id;
                $response["save_type"] = "add";
            }

            // trigger googlesheet update
            $response["googlesheet_update"] = admin_url('admin-ajax.php')."?action=generate_attendance_google_sheet";
            $ch = curl_init($response["googlesheet_update"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of printing it
            $curl_response = curl_exec($ch);
            curl_close($ch);
            $response["googlesheet_response"] = $curl_response;

            $response = new \WP_REST_Response($response);
            return $response;

        }else{
            return new \WP_REST_Response('Unauthorized', 401);
        }
    }

    static function get_attendance_report($request){
        $user_id = parent::pb_auth_user($request);
       
        if(empty($user_id)) return new \WP_REST_Response('Unauthorized', 401);

        $draw = $request->get_param("draw");
        $limit = $request->get_param("length");
        $offset = $request->get_param("start");
        $search_value = $request->get_param("search");//search[value]: 
        $order_arr = $request->get_param("order");
        $order_col = $order_arr[0]["column"];
        $tz = $request->get_param("tz");
        $subfilter = $request->get_param("subfilter");
        $searchclass = $request->get_param("searchclass");

        
        $where = "";
        $period = $request->get_param("period");

        $key = preg_replace('/[^A-Za-z0-9\-]/', '',$period.$searchclass.$subfilter.date("Y-m-dH")); // include hour to make sure transient is reloaded after an hour
        $transient_key = "attendance_report_".$key."_".$user_id;
        delete_transient($transient_key);
        $transient_data = get_transient( $transient_key );
        if ( false === $transient_data ) {
           
            switch($period) {
            
                case "Last Week":
                    $start_date = date('Y-m-d', strtotime('-1 week'));
                    $end_date = date("Y-m-d");

                    break;
                case "Last 30 Days":
                    $start_date = date('Y-m-d', strtotime('-30 days'));
                    $end_date = date("Y-m-d");

                    break;
                case "Last 90 Days":
                    $start_date = date('Y-m-d', strtotime('-90 days'));
                    $end_date = date("Y-m-d");
                    break;
                case "This Academic Year":
                    $currentYear = date('Y');
                    $start_date = ($currentYear - (date('n') < 9 ? 1 : 0)) . '-09-01';
                    $end_date = ($currentYear + (date('n') >= 9 ? 1 : 0)) . '-08-31';
                    break;

                default: case "Last 7 Days":
                    $start_date = date('Y-m-d', strtotime('-7 days'));
                    $end_date = date("Y-m-d");

                    break;
            }

            $where .= " AND ( DATE(a.date_time) >= '".date("Y-m-d",strtotime($start_date))."' AND DATE(a.date_time) <= '".date("Y-m-d",strtotime($end_date))."' )  ";

            $is_user_institute_admin = SafarUser::is_user_institute_admin();

            $classroom_ids = [];
            $institute_ids = [];
            $student_ids = [];

            if($is_user_institute_admin){

                
                // check if transient cache data is not blank

                $institutes = SafarSchool::get_user_institutes( $user_id );

                    
                foreach($institutes as $institute){
                    $institute_ids[] = $institute->ID;
                    $institute_data = \Safar\SafarSchool::get_school_data($institute->ID);
                    $classrooms = \Safar\SafarSchool::get_classrooms($institute->ID);

                    if(!empty($classrooms)){
                        foreach($classrooms as $cl){
                            $classroom_ids[] = $cl->ID;
                            $rs_student_ids = learndash_get_groups_user_ids($cl->ID);
                            foreach($rs_student_ids as $r_student_id){
                                if(!in_array($r_student_id, $student_ids)) $student_ids[] = $r_student_id;
                            }
                        }
                    }

                    if(!empty($institute_data["students"])){
                        foreach($institute_data["students"] as $student){
                            if(!in_array($student->ID, $student_ids)) $student_ids[] = $student->ID;
                        }
                    }

                }
                $response["institute_ids"] = $institute_ids;
                $response["classroom_ids"] = $classroom_ids;

            }else if(SafarUser::is_user_institute_parent()){
                $response["classroom_ids"] = [];
                $response["is_user_institute_parent"] = SafarUser::is_user_institute_parent();
                $result = learndash_get_administrators_group_ids($user_id);
                foreach($result as $r){
                    $rs_student_ids = learndash_get_groups_user_ids($r);

                    foreach($rs_student_ids as $r_student_id){
                        if(!in_array($r_student_id, $student_ids)) $student_ids[] = $r_student_id;
                    }
                }
            }else{
                $result = learndash_get_administrators_group_ids($user_id);
                foreach($result as $r){
                    $classroom_ids[] = $r;
                    $rs_student_ids = learndash_get_groups_user_ids($r);

                    foreach($rs_student_ids as $r_student_id){
                        if(!in_array($r_student_id, $student_ids)) $student_ids[] = $r_student_id;
                    }
                }
                $response["classroom_ids"] = $classroom_ids;
            }

            // get all attendance from students
            // matching classroomids

            $join_class = "";
            if(!empty($searchclass)){
                $join_class = " INNER JOIN `".parent::wpdb()->prefix."posts` as classroom ON a.classroom_id = classroom.ID ";
                $where .= " AND ( classroom.post_title like '%".esc_sql($searchclass)."%') ";

                $sql = "SELECT * FROM `".parent::wpdb()->prefix."posts` as classroom WHERE  classroom.post_title like '%".esc_sql($searchclass)."%' AND 
                    ID in (".implode(",",$classroom_ids).")";
                $result = parent::wpdb()->get_results($sql);
                // only get the students from the searched class
                $student_ids = [];
                foreach($result as $sg){
                    $rs_student_ids = learndash_get_groups_user_ids($sg->ID);
                    foreach($rs_student_ids as $r_student_id){
                        if(!in_array($r_student_id, $student_ids)) $student_ids[] = $r_student_id;
                    }
                }
            }

            $classroom_filter = " 1 ";
            if(!empty($classroom_ids)){
                $classroom_filter = " a.classroom_id in (".implode(",",$classroom_ids).")  ";
            }
            
            $sql = "
                SELECT a.classroom_id, 
                        a.institute_id,
                        ( select id from `wp7s_attendance_details` WHERE attendance_id=a.id AND user_id=ad.user_id ORDER BY id DESC LIMIT 1 ) as latest_ad_id,
                        ad.user_id,
                        ( select status from `wp7s_attendance_details` WHERE attendance_id=a.id AND user_id=ad.user_id ORDER BY id DESC LIMIT 1 ) as status,
                        a.date_time  
                
                    FROM `".parent::wpdb()->prefix."attendance` as a 
                INNER JOIN `".parent::wpdb()->prefix."attendance_details` as ad ON a.id = ad.attendance_id
                ".$join_class."
                WHERE ".$classroom_filter."
                    AND ad.user_id in (".implode(",",$student_ids).")

                ".$where."

                GROUP BY a.date_time DESC, ad.user_id DESC 

                ORDER BY a.date_time DESC, ad.user_id DESC 
            ";
           
            $result = parent::wpdb()->get_results($sql);

        
            $attendance = [];
            $date_with_attendance = [];
            foreach($result as $r){
                $attendance[$r->user_id][] = $r;
                $date = date("Y-m-d",strtotime($r->date_time));
                if(!in_array($date,$date_with_attendance)) $date_with_attendance[] = $date;
            }


            $all_dates = [];
            $current_date = $start_date;
            while ($current_date < $end_date) {
                $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                if(in_array($current_date,$date_with_attendance)){
                    if(!in_array($current_date,$all_dates)) $all_dates[] = $current_date;
                }
            }
            rsort($all_dates);
        

            $data = [];
            $today = date("Y-m-d");
            foreach($student_ids as $student_id){

                $cellattendance = [];
                $found_dates = [];
                $no_log_dates = [];
                
                foreach($all_dates as $current_date){
                    
                    $found = false;
                    foreach($attendance[$student_id] as $date){
                        if(!in_array($current_date, $found_dates)){
                            
                            if($current_date == date("Y-m-d",strtotime($date->date_time)) ) {
                                if(!$found) $cellattendance[] = $date;
                                $found_dates[] = $f;
                                $found = true;
                            }
                        }
                    }
                    if(!$found){
                        $cellattendance[] = ["date_time"=>$current_date, "status"=>"no attendance"];
                    }
                }

                /* 
                <div class="item">Absent today</div>
                <div class="item">Late today</div>
                <div class="item">3 lates in a row</div>
                <div class="item">More than 5 lates</div>
                <div class="item">More than 10 lates</div>
                <div class="item">3 or more absences in a row</div>
                */

                $absent_today = false;
                $three_lates_row = false;
                $late_today = false;
                $more_five_lates = false;
                $three_late_row_count = 0;
                $more_ten_lates = false;
                $three_more_lates_row = true;
                $absent_row_count = 0;
                $three_more_absent_row = false;
                $late_count = 0;
                $absent_count = 0;
                

                foreach($cellattendance as $key=>$attendance_cell){
                    $attendance_cell = (array) $attendance_cell;
                    if($today==date("Y-m-d",strtotime($attendance_cell["date_time"]))){
                        if($attendance_cell["status"] == "absent") $absent_today = true;
                        if($attendance_cell["status"] == "late") $late_today = true;
                    }

                    if($attendance_cell["status"]=="late") $late_count++;
                    if($attendance_cell["status"]=="absent") $absent_count++;

                    if ($attendance_cell['status'] == 'late') {
                        $three_late_row_count++;
                        if($three_late_row_count == 3) {
                            $three_lates_row = true;
                        }
                        if($three_late_row_count > 3) $three_more_lates_row = true;
                    } else {
                        $three_late_row_count = 0;
                    }
                    
                    if ($attendance_cell['status'] == 'absent') {
                        $absent_row_count++;
                        if($absent_row_count > 3) $three_more_absent_row = true;
                    } else {
                        $absent_row_count = 0;
                    }
                }
            
                $attendance_perc = "100";
                if($absent_count > 0 || $late_count > 0){
                    $attendance_perc = number_format( (  ( sizeof($attendance[$student_id]) - ($absent_count + $late_count) ) / sizeof($attendance[$student_id]) ) * 100,0);
                }


                if($late_count > 5 ) $more_five_lates = true;
                if($more_ten_lates > 10 ) $more_ten_lates = true;
                
                $show_data = false;
                switch($subfilter){
                    case "Absent today":
                        if($absent_today) $show_data = true;
                        break;
                    case "Late today":
                        if($late_today) $show_data = true;
                        break;
                    case "3 lates in a row":
                        if($three_lates_row) $show_data = true;
                        break;
                    case "More than 5 lates":
                        if($more_five_lates) $show_data = true;
                        break;
                    case "More than 10 lates":
                        if($more_ten_lates) $show_data = true;
                        break;
                    case "3 or more absences in a row":
                        if($three_more_absent_row) $show_data = true;
                        break;
                    default: $show_data = true; break;
                }
            
                if($show_data){
                    $data[] = [
                        "user_id" => $student_id,
                        "avatar" => get_avatar_url($student_id),
                        "name" => get_user_meta($student_id,"first_name",true)." ".get_user_meta($student_id,"last_name",true),
                        "attendance" => $attendance[$student_id],
                        "cellattendance" => $cellattendance,
                        "late_count" => $late_count,
                        "absent_count" => $absent_count,
                        "attendance_percent" => $attendance_perc,
                        "absent_today" => $absent_today,
                        "late_today" => $late_today,
                        "three_lates_row" => $three_lates_row,
                        "more_five_lates" => $more_five_lates,
                        "more_ten_lates" => $more_ten_lates,
                        "three_more_lates_row" => $three_more_lates_row,
                        "three_more_absent_row" => $three_more_absent_row
                    ];
                }
            }
    
            usort($data, function($a, $b){ 
                return strnatcmp($a['name'], $b['name']); 
            });
            set_transient( $transient_key, ["data"=>$data,"start_date"=>$start_date,"end_date"=>$end_date,"all_dates"=>$all_dates], 1800 ); // 3600 = 1 hour
            $response["transient"] = false;
        }else{
            $response["transient"] = true;
            $response["key"] = $transient_key;
            $data = $transient_data["data"];
            $start_date = $transient_data["start_date"];
            $end_date = $transient_data["end_date"];
            $all_dates = $transient_data["all_dates"];
        }

        $response["draw"] = 0;
        $response["recordsTotal"] = 0;
        $response["recordsFiltered"] = 0;
        $response["data"] = $data;
        $response["tz"] = $tz;
        $response["start_date"] = $start_date;
        $response["end_date"] = $end_date;
        $response["is_user_institute_admin"] = $is_user_institute_admin;
        $response["all_dates"] = $all_dates;
        $response = new \WP_REST_Response($response);

        return $response;
    }
}