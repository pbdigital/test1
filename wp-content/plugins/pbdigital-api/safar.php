<?php 
namespace Safar;

use \WP_REST_Request;

class Safar{
    static $requires_auth = true;
    static $user_id = 0;
    static $request;
  
    static function wpdb(){
        global $wpdb;
        return $wpdb;
    }
    

    static function pb_auth_user ( $request ) {
        global $wpdb, $user;
  
        #$wpdb->insert('mobile_app_log', array('token_used' => $token));
        if(!empty(get_current_user_id())){
            $user_id = get_current_user_id();
            self::$user_id = $user_id;
            return $user_id;
        }
        
        $token = $request->get_header('Authorization');
        $token = preg_replace('/Bearer /', '', $token);
        if (empty($token)){
            $user_cookie = (wp_parse_auth_cookie( '', 'logged_in' ));
            $user = get_user_by( 'email', $user_cookie['username'] );
            if (isset( $user->data->ID)){
                self::$user_id = $user->data->ID;
                return $user->data->ID;
            } else {
                return false;
            }
        }
        $user = $wpdb->get_row( "SELECT * FROM $wpdb->usermeta WHERE meta_value = '$token'" );
        if (isset($user)){
            self::$user_id = $user->user_id;
            wp_set_current_user( $user->user_id );
            return $user->user_id;
        } else {
            return false;
        }
    }

    static function pbd_helper_timezone_abbrev_from_min($min){
        $min = 0 - $min;// flip the sign because JavaScript gets the offset backwards
        $seconds = $min * 60;
        // PHP bug https://bugs.php.net/bug.php?id=73988 and https://bugs.php.net/bug.php?id=44780
        if(660 == $min) $location = 'Asia/Magadan';
        // NOTE: change 1 to 0 to use no DST
        else $location = timezone_name_from_abbr('', $seconds, 1);
     
        try {
            $z = new \DateTimeZone($location);
        } catch(Exception $e) {
                 // error_log(sprintf('ERROR (incoming min = %s, seconds = %s, $location = %s): %s<br>', $min, $seconds, $location, $e->getMessage()));
                 $z = new \DateTimeZone('America/New_York');
        }
        $date = new \DateTime(null, $z);
        return $date->format('e T');
    }

    static function debug($d){
        echo "<Pre>";
            print_r($d);
        echo "</pre>";
    }

}