<?php 
namespace Safar;
use DateTime;
use Safar\SafarSchool;
use Safar\SafarCourses;
use \WP_REST_Request;

class SafarPublications extends Safar{
    static function api_request($args){
        //?field=value&field2=fasdf'
        $query_string = "";
        if(!empty($args["query_string"])) $query_string = "?".http_build_query($args["query_string"]);

        $method = strtoupper( ( empty($args["method"])) ? "GET":$args["method"] );
        
        $curl = curl_init();
        $curl_args = [
            CURLOPT_URL => SAFARPUB_API_URL.$args["endpoint"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ];


        if($method == "POST"){
            if(!empty($args["post_data"])) $curl_args[CURLOPT_POSTFIELDS] = http_build_query($args["post_data"]);
        }

        #\SafarJ2j\SafarJ2j::debug($curl_args);

        curl_setopt_array($curl, $curl_args);
        $response = json_decode( curl_exec($curl) );
        curl_close($curl);
        
        return $response;
    }

    
}