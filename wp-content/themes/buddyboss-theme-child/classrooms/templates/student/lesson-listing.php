<?php 
$ldc_hide_classroom_lock_column_in_detail_student_report =  get_site_option('ldc_hide_classroom_lock_column_in_detail_student_report', 'no'); 
?>
<style>
    .div-table-container .div-table-col.ld-lock-unlock-header, .ld-lock-unlock{
        display: <?php echo $ldc_hide_classroom_lock_column_in_detail_student_report === 'no' ? 'initial' : 'none'; ?>;
    }
</style>
<?php
$user_courses_lessons_quizzies = array();

// Filter as per query parameter for user id
if(empty($selected_student_id)){
	$user_group_users =  !empty($user_group_users) ? array($user_group_users[0]) : array();
}
else {
	$user_group_users = array(get_userdata($selected_student_id));
}
$user_courses_lessons_quizzies = array( 
								"user_id"=>0,
								"course_id"=>0
								);
$design_setting = get_site_option('ldc_design_setting');
if ( empty($design_setting['color_codes']['not_start']) ){
    $design_setting['color_codes']['not_start'] = '#FF0000';
}
if ( empty($design_setting['color_codes']['in_progress']) ){
    $design_setting['color_codes']['in_progress'] = '#FFA500';
}
if ( empty($design_setting['color_codes']['completed']) ){
    $design_setting['color_codes']['completed'] = '#008000';
}

// check whether show all quiz attempts or just one 
$has_ldc_show_all_quiz_attempts =  get_site_option('ldc_show_all_quiz_attempts');

$is_gravity_form_active = \ld_classroom\SharedFunctions :: is_gravity_form_active();

foreach($user_group_users as $user){ 
	$user_id = $user->ID;
	
	$user_quiz_meta = get_user_meta( $user_id, '_sfwd-quizzes', true );
	if ( ! is_array( $user_quiz_meta ) ) {
        $user_quiz_meta = array();
    }
	if(empty($filter_course_id)){
		$courses = empty($result_courses) ? array() : array($result_courses[0]->ID) ; //learndash_user_get_enrolled_courses( $user->ID);
	}
	else {
		$courses = array($filter_course_id);
	}
	if ( ( is_array( $courses ) )  && ( ! empty( $courses ) ) ) {
		
		foreach ($courses as $course) {
			$course_id = $course;
			$course_post = get_post( $course_id );
			// Get a list of lessons to loop.
			$lessons        = learndash_get_course_lessons_list( $course_id, null, array( 'num' => 0 ) );

			// Course Status
			$course_status 	=	learndash_course_status($course_id,$user->ID);
			if($course_status == 'Completed'){
				$course_color = $design_setting["color_codes"]["completed"];	
			}	
			else if($course_status == 'In Progress'){
				$course_color = $design_setting["color_codes"]["in_progress"];
			}
			else{
				$course_color = $design_setting["color_codes"]["not_start"];	
			}
			$course_cert = learndash_get_course_certificate_link($course_id, $user_id);
			$progress = learndash_course_progress(
										array(
											'user_id'   => $user_id,
											'course_id' => $course_id,
											'array'     => true,
										)
									);
									
			$user_courses_lessons_quizzies = array("user_id"=>$user->ID, 
													"user_name"=>$user->first_name.' '.$user->last_name,
													"course_id"=>$course_id,
													"course_status" => $course_status,
													"course_name"=> isset($course_post->post_title)?$course_post->post_title : "",
													"course_color"=>$course_color,
													"lessons_data" => array(),
													"course_quizzes_data" => array(),
													"course_certificate" => $course_cert,
													"course_progress_percentage" => $progress['percentage']
			);

			if ( ( is_array( $lessons ) )  && ( ! empty( $lessons ) ) ) {
				// Loop course's lessons.
				foreach ( $lessons as $lesson ) {
					$post          = $lesson['post'];
					$lesson_id = $lesson['post']->ID;

					// Lesson Status
					$lesson_status = "";
					$lesson_completion_time = "";
					if(learndash_is_lesson_complete($user_id, $lesson_id,$course_id)){
						$lesson_status = "Completed";
						$lesson_color = $design_setting["color_codes"]["completed"];
						$lesson_args = array(
							'course_id'        => $course_id,
							'user_id'			=>	$user_id,
							'post_id'			=>	$lesson_id,
							'activity_type'		=>	'lesson',
						);
						$lesson_completion_time = learndash_get_user_activity( $lesson_args );
						// print_r($lesson_completion_time); die;
						if(!empty($lesson_completion_time) && $lesson_completion_time->activity_completed){
							$lesson_completion_time = date("Y-m-d H:i:s", $lesson_completion_time->activity_completed);
						}else{
							$lesson_completion_time = 'Unknown';
						}
					}
					else{
						$lesson_status = "Not Completed";
						$lesson_color = $design_setting["color_codes"]["not_start"];	
					}

					$lessons_data = array("lesson_id"=>$lesson_id,
											"lesson_name"=>$post->post_title,
											"lesson_url"=>get_post_permalink($lesson_id),
											"lesson_status"=>$lesson_status,
											"lesson_completion_time" => $lesson_completion_time,
											"lesson_color"=>$lesson_color,
                                            "time_spent" => ld_classroom\Timer::get_time_spent($post, $user->ID),
											"topics_data" => array(),
											"quizzes_data" => array(),
											"assignments_data" => array(),
										);

					// Get lesson's topics.
					$topics = learndash_get_topic_list($lesson_id,null); //learndash_topic_dots( $lesson_id, false, 'array', null, $course_id );
					// $output_topics = [];


					if ( ( is_array( $topics ) )  && ( ! empty( $topics ) ) ) {
						// Loop Topics.
						foreach ( $topics as $topic ) {
							$topic_id = $topic->ID;							

							// Topic Status
							$topic_completed_date = "";
							if(learndash_is_topic_complete($user_id, $topic_id, $course_id)){
								if($lessons_data['lesson_color'] != $design_setting["color_codes"]["completed"]){
									$lessons_data['lesson_color'] = $design_setting["color_codes"]["in_progress"];
								}
								$topic_color = $design_setting["color_codes"]["completed"];
								$topic_args     = array(
									'course_id'     => $course_id,
									'user_id'       => $user_id,
									'post_id'       => $topic_id,
									'activity_type' => 'topic',
								);
								$topic_activity = learndash_get_user_activity( $topic_args );
								
								if(!empty($topic_activity)){
									$topic_color = $design_setting["color_codes"]["completed"];	
									$topic_completed_date = date('M j, Y', $topic_activity->activity_completed);
								}
								else{
									$topic_color = $design_setting["color_codes"]["not_start"];
									$topic_completed_date = "Not Started";
								}
							}
							else{
								$topic_color = $design_setting["color_codes"]["not_start"];
								$topic_completed_date = "Not Started";
							}

							$topics_data = array("topic_id"=>$topic_id,
																	"topic_name"=>$topic->post_title,
																	"topic_url"=>get_post_permalink($topic_id),
																	"topic_color"=>$topic_color,
																	"topic_completed_date"=>$topic_completed_date,
                                                                    "time_spent" => ld_classroom\Timer::get_time_spent($topic, $user->ID),
																	"quizzes_data" => array(),
																	"assignments_data" => array()
																);


							/**  Get quiz of lesson **/
							//check if lesson has quiz  
							$topic_quiz_args = array(
								'post_type' => array( 'sfwd-quiz'),
								'meta_key'=> 'topic_id',
								'meta_value'=> $topic_id
							);
							$topic_quizzes = new WP_Query( $topic_quiz_args );

							// Get Topic's Quizzes.
							
							$topic_quizzes =  learndash_get_lesson_quiz_list( $topic_id, $user_id, $course_id );
							
							if ( ( is_array( $topic_quizzes ) )  && ( ! empty( $topic_quizzes ) ) ) {
								foreach ( $topic_quizzes as $quiz ) {
									$quiz_id = $quiz['post']->ID;
									$pt = 0;
									if($quiz_id && count(learndash_get_user_quiz_attempts($user_id, $quiz_id))){ 
										//$pt = do_shortcode("[courseinfo show='cumulative_percentage'  user_id='".$user_id."' course_id='".$course_id."']");
										$pt = do_shortcode('[quizinfo show="percentage"  user_id="'.$user_id.'" quiz="'.$quiz_id.'"]');
										$quiz_score = !empty($pt) ? $pt . '%' : '-';	
									}
									else{
										$quiz_score = '-';
									}

									$certificate_link = '';
									$pro_quizid = $statistic_ref_id = $quiz_pro_statistics_on = 0;
									$quiz_attempts_key = array();

									if( $quiz_id ){ 	
										if( $quiz['status'] == 'completed' ) {
											$cert = learndash_certificate_details( $quiz_id, $user_id );
											if ((isset($cert['certificateLink'])) && (!empty($cert['certificateLink'])))
												$certificate_link = $cert['certificateLink']; 
										}	

										if( $pt >= 0 ){
											$quiz_pro_statistics_on = learndash_get_setting( $quiz_id, 'statisticsOn', true );

											$user_quiz_meta_ = array_reverse($user_quiz_meta);
											if( $has_ldc_show_all_quiz_attempts && !empty(array_keys( array_column($user_quiz_meta, 'quiz') ,$quiz_id )) ){
												$quiz_attempts_key = array_keys( array_column($user_quiz_meta, 'quiz') ,$quiz_id );

												$user_quiz_meta_ = $user_quiz_meta;
											}
											else {
												$key = array_search($quiz_id, array_column($user_quiz_meta_, 'quiz'));	
												
												$pro_quizid = isset($user_quiz_meta_[$key]['pro_quizid']) ?? 0;
												$statistic_ref_id = isset($user_quiz_meta_[$key]['statistic_ref_id']) ?? 0;		

												$quiz_attempts_key = array($key);
											}
											
											
										}
									}	
									
									// echo "<pre>";
									// print_r(  $user_quiz_meta );
									// print_r($key_2);
									// echo "</pre>";
									$quizzes_data = array("quiz_id"=>$quiz_id,
															"quiz_name"=>$quiz['post']->post_title,
															"quiz_url"=>get_post_permalink($quiz_id),
															"quiz_score" => $quiz_score,
															"quiz_certificate" => $certificate_link,
															"quiz_statistics_on" => $quiz_pro_statistics_on,
															"pro_quizid" => $pro_quizid,
															"statistic_ref_id" => $statistic_ref_id,
                                                            "time_spent" => ld_classroom\Timer::get_time_spent($quiz['post'], $user->ID)
															);
									//Quiz Attempts 
									$quizzes_data['quiz_attempts'] = array();
									foreach ($quiz_attempts_key as $quiz_attempt_key) {
										$quiz_score = $pro_quizid = $statistic_ref_id = 0;
										if( isset( $user_quiz_meta_[$quiz_attempt_key] ) && $user_quiz_meta_[$quiz_attempt_key]['quiz'] == $quiz_id ){
											$quiz_score = $user_quiz_meta_[$quiz_attempt_key]['percentage'];
											$pro_quizid = $user_quiz_meta_[$quiz_attempt_key]['pro_quizid'];
											$statistic_ref_id = $user_quiz_meta_[$quiz_attempt_key]['statistic_ref_id'];	
										}

										$quiz_score = !empty($quiz_score) ? $quiz_score . '%' : '-';
										
										$certificate_link_ = '';
										if( !empty($certificate_link) ){
											$certificate_link_ = $certificate_link .'&time=' . $user_quiz_meta_[$quiz_attempt_key]['time']; 	
										}
										
										
										// Essays 
										$essays_data = array();
	                                    if($quiz_id /* && $quiz['status'] != 'notcompleted' */ ){ 
	                                        $quiz_questions = learndash_get_quiz_questions($quiz_id);
	                                        foreach($quiz_questions as $quiz_question_id => $quiz_question_pro_id){
	                                            $question_type = get_post_meta( $quiz_question_id, 'question_type', true );
	                                            if($question_type === 'essay'){
	                                                if( isset($user_quiz_meta_[$quiz_attempt_key]['graded']) ){
			                                            $essay_post_id = end($user_quiz_meta_[$quiz_attempt_key]['graded'])['post_id'];
			                                        }
			                                        else{
			                                            continue;
			                                        }
	                                                
	                                                if ( !is_null(get_post($essay_post_id) ) ){
		                                                $question_post_id = (int) get_post_meta( $essay_post_id, 'question_post_id', true );
		                                                $question_post_title = get_the_title($question_post_id);
		                                                $essay_comments_number = get_comments_number($essay_post_id);
		                                                $last_essay_attempted_details = learndash_get_essay_details($essay_post_id);
		                                                $essays_data[] = array("essay_id" 		=> $essay_post_id,
		                                                                                    "essay_url"			=> get_post_permalink($essay_post_id),
		                                                                                    "question_post_id" 	=> $question_post_id,
		                                                                                    "question_post_title" => $question_post_title,
		                                                                                    "points"			=> $last_essay_attempted_details['points'],
		                                                                                    "status" 			=> $last_essay_attempted_details['status'],
		                                                                                    "comments"			=> $essay_comments_number
		                                                                                );
		                                            }
	                                            }
	                                        }
	                                    }

										$quizzes_data['quiz_attempts'][] = array("quiz_id"=>$quiz_id,
																				"quiz_name"=>$quiz['post']->post_title,
																				"quiz_url"=>get_post_permalink($quiz_id),
																				"quiz_score" => $quiz_score,
																				"quiz_certificate" => $certificate_link_ ,
																				"quiz_statistics_on" => $quiz_pro_statistics_on,
																				"pro_quizid" => $pro_quizid,
																				"statistic_ref_id" => $statistic_ref_id,
																				"essays_data" => $essays_data
																				);
									}

									
									
															
									$topics_data['quizzes_data'][] = $quizzes_data;	
								}
							}
							
							/**  Get assignments of topic **/
							$topic_assignments = learndash_get_user_assignments( $topic_id, $user_id );
							if(is_array( $topic_assignments ) && !empty($topic_assignments)){
								$points_enabled = learndash_get_setting( $topic_id, 'lesson_assignment_points_enabled' );	
								if ( 'on' === $points_enabled ) {
									$is_points_enabled = 1;
									$assingment_points = learndash_get_setting( $topic_id, 'lesson_assignment_points_amount' );
								}
								else{
									$is_points_enabled = 0 ;
									$assingment_points = 0 ;
								}

								// Loop Assignments.
								foreach ( $topic_assignments as $topic_assignment ) {
									$assignment_id = $topic_assignment->ID;
									
									if ( learndash_is_assignment_approved_by_meta( $assignment_id )) {
										$is_approved = true;
									}
									else{
										$is_approved = false;
									}
									$assignments_data = array("assignment_id"=>$assignment_id,
															"assignment_name"=>$topic_assignment->post_title,
															"assignment_url"=> get_post_permalink($assignment_id),
                                                            "assignment_media_url" => get_post_meta( $assignment_id, 'file_link', true ),
															"is_approved" => $is_approved,
															"is_points_enabled" => $is_points_enabled,
															"assingment_points" => $assingment_points,
                                                            "comments" => get_comments_number($assignment_id)
															);
									
									$assignments_data['is_gf_enabled'] = $is_gravity_form_active;
									if ( $is_gravity_form_active ) {
										$gf_page_url = get_post_meta($lesson_id, 'gf-page-url', true);
										$gf_form_id = get_post_meta($lesson_id, 'gf-form-id', true);
										$gf_form_field_id = get_post_meta($lesson_id, 'gf-form-field-id', true);
										$gf_form_field_student_id_key = get_post_meta($lesson_id, 'gf-form-field-student-id', true);
										$has_gf_entries = false;
										$gf_chosen_text = "";

										if ( 'on' == learndash_get_setting( $lesson_id, 'lesson_assignment_upload' ) 
											&& !empty($gf_page_url) 
											&& !empty($gf_form_id) 
											&& !empty($gf_form_field_id) 
										){
											$gf_form = \GFAPI::get_form($gf_form_id);
											$gf_hidden_assignment_id_field = 0;
											$gf_hidden_student_id_field = 0;
											// Let's iterate through the "fields" array and find our fields there
											foreach ( $gf_form['fields'] as $field ) {
												if ( isset( $field['inputName'] ) &&  $field['inputName'] === "ldc_assignment_id" ) {
													$gf_hidden_assignment_id_field  =  $field['id'];
												}
												else if ( isset( $field['inputName'] ) &&  $field['inputName'] === "ldc_student_id" ) {
													$gf_hidden_student_id_field  =  $field['id'];
												}
											}
						
											// Check if the hidden ldc_student_id field set to form. if set then use that user id, else use gf-form-field-student-id meta set under the post(lesson/topic) settings
											if ( isset($gf_hidden_student_id_field) && is_int($gf_hidden_student_id_field) ){
												$gf_form_field_student_id_key  = $gf_hidden_student_id_field;
											}									

											$assignments_data['is_gf_enabled_for_post'] = true;
											$assignments_data['gf_assignment_status'] = 'pending';
											$search_criteria = array();
											$search_criteria['field_filters'][] = array( 'key' => $gf_form_field_student_id_key , 'value' => $user_id );

											if ( $gf_hidden_assignment_id_field ){
												$search_criteria['field_filters'][] = array( 'key' => $gf_hidden_assignment_id_field , 'value' => $assignments_data['assignment_id'] );
											}
											
											$entries = \GFAPI::get_entries($gf_form_id, $search_criteria);
											$has_gf_entries = false;
											$choice_text = "";
											if ( is_array($entries) && !empty($entries) ){
												$has_gf_entries = true;
												$assignments_data['has_gf_entries'] = $has_gf_entries;
											
												$selected_value = $entries[0][$gf_form_field_id];
												$field_one = \GFAPI::get_field( $gf_form_id, $gf_form_field_id );
												if ( $selected_value && is_array($field_one->choices) ){
													$choices_key = array_search($selected_value, array_column($field_one->choices, 'value'));
													$choice_text = $field_one->choices[$choices_key]['text'];	
												}
												if ($choice_text === "Competent" || $choice_text === "Satisfactory"){
													$assignments_data['gf_assignment_status'] = 'approved';
												}
												else{
													$assignments_data['gf_assignment_status'] = 'resubmit';
												}
											}
											// Appendning the ldc_assignment_id and ldc_student_id to GF url
											$gf_page_url = $gf_page_url . (parse_url($gf_page_url, PHP_URL_QUERY) ? '&' : '?')
											. 'ldc_assignment_id=' . $assignment_id . '&ldc_student_id=' . $user_id
											. '&ldc_student_email=' . $user->user_email;
											
											$assignments_data['gf_page_url'] = $gf_page_url;		
										}
									}
									$topics_data['assignments_data'][] = $assignments_data;
								}
								 
							}
							
							$lessons_data['topics_data'][] = $topics_data;										
						}
					}

					/**  Get quiz of lesson **/
					//check if lesson has quiz  
					$lesson_quizzes = learndash_get_lesson_quiz_list( $lesson_id, $user_id,$course_id ); 
					 			
					
					if ( ( is_array( $lesson_quizzes ) )  && ( ! empty( $lesson_quizzes ) ) ) {
						
						// Loop Quizzes.
						foreach ( $lesson_quizzes as $quiz ) {
							$quiz_id = $quiz['post']->ID;
							$pt = 0;
							if( $quiz_id && count(learndash_get_user_quiz_attempts($user_id, $quiz_id)) ){ 							
								//$pt = do_shortcode("[courseinfo show='cumulative_percentage'  user_id='".$user_id."' course_id='".$course_id."']");
								$pt = do_shortcode('[quizinfo show="percentage"  user_id="'.$user_id.'" quiz="'.$quiz_id.'"]');
								$quiz_score = $pt ? $pt . '%' : '-';	
							}
							else{
								$quiz_score = '-';
							}
							
							$certificate_link = '';
							$pro_quizid = $statistic_ref_id = $quiz_pro_statistics_on = 0;
							if( $quiz_id ){ 	
								if( $quiz['status'] == 'completed' ) {
									$cert = learndash_certificate_details( $quiz_id, $user_id );
									if ((isset($cert['certificateLink'])) && (!empty($cert['certificateLink'])))
										$certificate_link = $cert['certificateLink']; 
								}	

								if( $pt >= 0 ){
									$quiz_pro_statistics_on = learndash_get_setting( $quiz_id, 'statisticsOn', true );
									
									if( $has_ldc_show_all_quiz_attempts && !empty(array_keys( array_column($user_quiz_meta, 'quiz') ,$quiz_id )) ){
										$quiz_attempts_key = array_keys( array_column($user_quiz_meta, 'quiz') ,$quiz_id );

										$user_quiz_meta_ = $user_quiz_meta;
									}
									else {
										$user_quiz_meta_ = array_reverse($user_quiz_meta);
										$key = array_search($quiz_id, array_column($user_quiz_meta_, 'quiz'));	

										
										$pro_quizid = isset($user_quiz_meta_[$key]['pro_quizid']) ?? 0;
										$statistic_ref_id = isset($user_quiz_meta_[$key]['statistic_ref_id']) ?? 0;		

										$quiz_attempts_key = array($key);
									}
								}
							}	
							
							$quizzes_data = array("quiz_id"=>$quiz_id,
													"quiz_name"=>$quiz['post']->post_title,
													"quiz_url"=>get_post_permalink($quiz_id),
													"quiz_score" => $quiz_score,
													"quiz_certificate" => $certificate_link,
													"quiz_statistics_on" => $quiz_pro_statistics_on,
													"pro_quizid" => $pro_quizid,
													"statistic_ref_id" => $statistic_ref_id,
                                                    "time_spent" => ld_classroom\Timer::get_time_spent($quiz['post'], $user->ID)
													);

							//Quiz Attempts 
							$quizzes_data['quiz_attempts'] = array();
							foreach ($quiz_attempts_key as $quiz_attempt_key) {
								$quiz_score = $pro_quizid = $statistic_ref_id = 0;
								if( isset( $user_quiz_meta_[$quiz_attempt_key] ) && $user_quiz_meta_[$quiz_attempt_key]['quiz'] == $quiz_id ){
									$quiz_score = $user_quiz_meta_[$quiz_attempt_key]['percentage'];
									$pro_quizid = $user_quiz_meta_[$quiz_attempt_key]['pro_quizid'];
									$statistic_ref_id = $user_quiz_meta_[$quiz_attempt_key]['statistic_ref_id'];		
								}
								$quiz_score = !empty($quiz_score) ? $quiz_score . '%' : '-';

								$certificate_link_ = '';
								if( !empty($certificate_link) ){
									$certificate_link_ = $certificate_link .'&time=' . $user_quiz_meta_[$quiz_attempt_key]['time']; 	
								}

								// Essays 
								$essays_data = array();
	                            if($quiz_id /* && $quiz['status'] != 'notcompleted' */ ){ 
	                                $quiz_questions = learndash_get_quiz_questions($quiz_id);
	                                foreach($quiz_questions as $quiz_question_id => $quiz_question_pro_id){
	                                    $question_type = get_post_meta( $quiz_question_id, 'question_type', true );
	                                    if($question_type === 'essay'){
	                                        if( isset($user_quiz_meta_[$quiz_attempt_key]['graded']) ){
	                                            $essay_post_id = end($user_quiz_meta_[$quiz_attempt_key]['graded'])['post_id'];
	                                        }
	                                        else{
	                                            continue;
	                                        }
	                                        
	                                        if ( !is_null(get_post($essay_post_id) ) ){
		                                        $question_post_id = (int) get_post_meta( $essay_post_id, 'question_post_id', true );
		                                        $question_post_title = get_the_title($question_post_id);
		                                        $essay_comments_number = get_comments_number($essay_post_id);
		                                        $last_essay_attempted_details = learndash_get_essay_details($essay_post_id);
		                                        $essays_data[] = array("essay_id" 		=> $essay_post_id,
		                                                                            "essay_url"			=> get_post_permalink($essay_post_id),
		                                                                            "question_post_id" 	=> $question_post_id,
		                                                                            "question_post_title" => $question_post_title,
		                                                                            "points"			=> $last_essay_attempted_details['points'],
		                                                                            "status" 			=> $last_essay_attempted_details['status'],
		                                                                            "comments"			=> $essay_comments_number
		                                                                        );
		                                    }
	                                    }
	                                }
	                            }

								$quizzes_data['quiz_attempts'][] = array("quiz_id"=>$quiz_id,
																		"quiz_name"=>$quiz['post']->post_title,
																		"quiz_url"=>get_post_permalink($quiz_id),
																		"quiz_score" => $quiz_score,
																		"quiz_certificate" => $certificate_link_,
																		"quiz_statistics_on" => $quiz_pro_statistics_on,
																		"pro_quizid" => $pro_quizid,
																		"statistic_ref_id" => $statistic_ref_id,
																		"essays_data" =>  $essays_data
																		);
							}

							// Essays 
							$quizzes_data['essays_data'] = array();
                            // if($quiz_id /* && $quiz['status'] != 'notcompleted' */ ){ 
                            //     $quiz_questions = learndash_get_quiz_questions($quiz_id);
                            //     foreach($quiz_questions as $quiz_question_id => $quiz_question_pro_id){
                            //         $question_type = get_post_meta( $quiz_question_id, 'question_type', true );
                            //         if($question_type === 'essay'){
                            //             if( isset($user_quiz_meta_[$quiz_attempts_key[0]]['graded']) ){
                            //                 $essay_post_id = end($user_quiz_meta_[$quiz_attempts_key[0]]['graded'])['post_id'];
                            //             }
                            //             else{
                            //                 continue;
                            //             }
                                        
                            //             $question_post_id = (int) get_post_meta( $essay_post_id, 'question_post_id', true );
                            //             $question_post_title = get_the_title($question_post_id);
                            //             $essay_comments_number = get_comments_number($essay_post_id);
                            //             $last_essay_attempted_details = learndash_get_essay_details($essay_post_id);
                            //             $quizzes_data['essays_data'][] = array("essay_id" 		=> $essay_post_id,
                            //                                                 "essay_url"			=> get_post_permalink($essay_post_id),
                            //                                                 "question_post_id" 	=> $question_post_id,
                            //                                                 "question_post_title" => $question_post_title,
                            //                                                 "points"			=> $last_essay_attempted_details['points'],
                            //                                                 "status" 			=> $last_essay_attempted_details['status'],
                            //                                                 "comments"			=> $essay_comments_number
                            //                                             );
                            //         }
                            //     }
                            // }
							
							$lessons_data['quizzes_data'][] = $quizzes_data;	
						}
					}

					/**  Get assignments of lesson **/
					$lesson_assignments = learndash_get_user_assignments( $lesson_id, $user_id );
					if(is_array( $lesson_assignments ) && !empty($lesson_assignments)){
						$points_enabled = learndash_get_setting( $lesson_id, 'lesson_assignment_points_enabled' );	
						if ( 'on' === $points_enabled ) {
							$is_points_enabled = 1;
							$assingment_points = learndash_get_setting( $lesson_id, 'lesson_assignment_points_amount' );
						}
						else{
							$is_points_enabled = 0 ;
							$assingment_points = 0 ;
						}
						// Loop Assignments.
						foreach ( $lesson_assignments as $lesson_assignment ) {
							$assignment_id = $lesson_assignment->ID;
							// print_r($lesson_assignment);
							if ( learndash_is_assignment_approved_by_meta( $assignment_id )) {
								$is_approved = true;
							}
							else{
								$is_approved = false;
							}
							$assignments_data = array("assignment_id"=>$assignment_id,
													"assignment_name"=>$lesson_assignment->post_title,
													"assignment_url"=> get_post_permalink($assignment_id),
                                                    "assignment_media_url" => get_post_meta( $assignment_id, 'file_link', true ),
													"is_approved" => $is_approved,
													"is_points_enabled" => $is_points_enabled,
													"assingment_points" => $assingment_points,
                                                    "comments" => get_comments_number($assignment_id)
													);
							
							$assignments_data['is_gf_enabled'] = $is_gravity_form_active;
						    if ( $is_gravity_form_active ) {
								$gf_page_url = get_post_meta($lesson_id, 'gf-page-url', true);
                                $gf_form_id = get_post_meta($lesson_id, 'gf-form-id', true);
                                $gf_form_field_id = get_post_meta($lesson_id, 'gf-form-field-id', true);
                                $gf_form_field_student_id_key = get_post_meta($lesson_id, 'gf-form-field-student-id', true);
                                $has_gf_entries = false;
                                $gf_chosen_text = "";

								if ( 'on' == learndash_get_setting( $lesson_id, 'lesson_assignment_upload' ) 
									&& !empty($gf_page_url) 
									&& !empty($gf_form_id) 
									&& !empty($gf_form_field_id) 
								){
									$gf_form = \GFAPI::get_form($gf_form_id);
									$gf_hidden_assignment_id_field = 0;
									$gf_hidden_student_id_field = 0;
									// Let's iterate through the "fields" array and find our fields there
									foreach ( $gf_form['fields'] as $field ) {
										if ( isset( $field['inputName'] ) &&  $field['inputName'] === "ldc_assignment_id" ) {
											$gf_hidden_assignment_id_field  =  $field['id'];
										}
										else if ( isset( $field['inputName'] ) &&  $field['inputName'] === "ldc_student_id" ) {
											$gf_hidden_student_id_field  =  $field['id'];
										}
									}
				
									// Check if the hidden ldc_student_id field set to form. if set then use that user id, else use gf-form-field-student-id meta set under the post(lesson/topic) settings
									if ( isset($gf_hidden_student_id_field) && is_int($gf_hidden_student_id_field) ){
										$gf_form_field_student_id_key  = $gf_hidden_student_id_field;
									}									

									$assignments_data['is_gf_enabled_for_post'] = true;
									$assignments_data['gf_assignment_status'] = 'pending';
									$search_criteria = array();
                                    $search_criteria['field_filters'][] = array( 'key' => $gf_form_field_student_id_key , 'value' => $user_id );

									
									if ( $gf_hidden_assignment_id_field ){
										$search_criteria['field_filters'][] = array( 'key' => $gf_hidden_assignment_id_field , 'value' => $assignments_data['assignment_id'] );
									}
                                     
                                    $entries = \GFAPI::get_entries($gf_form_id, $search_criteria);
									$has_gf_entries = false;
									$choice_text = "";
									if ( is_array($entries) && !empty($entries) ){
										$has_gf_entries = true;
										$assignments_data['has_gf_entries'] = $has_gf_entries;

										$selected_value = $entries[0][$gf_form_field_id];
										$field_one = \GFAPI::get_field( $gf_form_id, $gf_form_field_id );
										if ( $selected_value && is_array($field_one->choices) ){
											$choices_key = array_search($selected_value, array_column($field_one->choices, 'value'));
											$choice_text = $field_one->choices[$choices_key]['text'];	
										}
										if ($choice_text === "Competent" || $choice_text === "Satisfactory"){
											$assignments_data['gf_assignment_status'] = 'approved';
										}
										else{
											$assignments_data['gf_assignment_status'] = 'resubmit';
										}
									}

									// Appendning the ldc_assignment_id and ldc_student_id to GF url
									$gf_page_url = $gf_page_url . (parse_url($gf_page_url, PHP_URL_QUERY) ? '&' : '?')
									 . 'ldc_assignment_id=' . $assignment_id . '&ldc_student_id=' . $user_id
									 . '&ldc_student_email=' . $user->user_email;
									$assignments_data['gf_page_url'] = $gf_page_url;
								}
							}
												
							$lessons_data['assignments_data'][] = $assignments_data;
						}
						 
					}
					
					$user_courses_lessons_quizzies['lessons_data'][] = $lessons_data;	
				}
			}
			

			/**  Get quiz of course **/
			//check if course has quiz 
			$course_quizzes = learndash_get_course_quiz_list( $course_id, $user_id ); 
			 
			if ( ( is_array( $course_quizzes ) )  && ( ! empty( $course_quizzes ) ) ) {
				
				// Loop Quizzes.
				foreach ( $course_quizzes as $quiz ) {
					$quiz_id = $quiz['post']->ID;
					$pt = 0;
					if( $quiz_id && count(learndash_get_user_quiz_attempts($user_id, $quiz_id)) ){ 
						//$pt = do_shortcode("[courseinfo show='cumulative_percentage'  user_id='".$user_id."' course_id='".$course_id."']");
						$pt = do_shortcode('[quizinfo show="percentage"  user_id="'.$user_id.'" quiz="'.$quiz_id.'"]');
						$quiz_score = $pt ? $pt . '%' : '-';	
					}
					else{
						$quiz_score = '-';
					}
					
					$certificate_link = '';
					$pro_quizid = $statistic_ref_id = $quiz_pro_statistics_on = 0;
					if( $quiz_id ){ 
						if( $quiz['status'] == 'completed' ) {
							$cert = learndash_certificate_details( $quiz_id, $user_id );
							if ((isset($cert['certificateLink'])) && (!empty($cert['certificateLink'])))
								$certificate_link = $cert['certificateLink']; 	
						}
						
						if( $pt >= 0 ){
							$quiz_pro_statistics_on = learndash_get_setting( $quiz_id, 'statisticsOn', true );	
							
							if( $has_ldc_show_all_quiz_attempts && !empty(array_keys( array_column($user_quiz_meta, 'quiz') ,$quiz_id )) ) {
								$quiz_attempts_key = array_keys( array_column($user_quiz_meta, 'quiz') ,$quiz_id );

								$user_quiz_meta_ = $user_quiz_meta;
							}
							else {
								$user_quiz_meta_ = array_reverse($user_quiz_meta);
								$key = array_search($quiz_id, array_column($user_quiz_meta_, 'quiz'));	
								 
								$pro_quizid = isset($user_quiz_meta_[$key]['pro_quizid']) ?? 0;
								$statistic_ref_id = isset($user_quiz_meta_[$key]['statistic_ref_id']) ?? 0;		
								
								$quiz_attempts_key = array($key);
							}
						}
					}
					
					$quizzes_data = array("quiz_id"=>$quiz_id,
											"quiz_name"=>$quiz['post']->post_title,
											"quiz_url"=>get_post_permalink($quiz_id),
											"quiz_score" => $quiz_score,
											"quiz_certificate" => $certificate_link,
											"quiz_statistics_on" => $quiz_pro_statistics_on,
											"pro_quizid" => $pro_quizid,
											"statistic_ref_id" => $statistic_ref_id,
                                            "time_spent" => ld_classroom\Timer::get_time_spent($quiz['post'], $user->ID)
											);
                    
                    //Quiz Attempts 
					$quizzes_data['quiz_attempts'] = array();
					
					foreach ($quiz_attempts_key as $quiz_attempt_key) {
						$quiz_score = $pro_quizid = $statistic_ref_id = 0;
						if( isset( $user_quiz_meta_[$quiz_attempt_key] ) && $user_quiz_meta_[$quiz_attempt_key]['quiz'] == $quiz_id ){
							$quiz_score = $user_quiz_meta_[$quiz_attempt_key]['percentage'];
							$pro_quizid = $user_quiz_meta_[$quiz_attempt_key]['pro_quizid'];
							$statistic_ref_id = $user_quiz_meta_[$quiz_attempt_key]['statistic_ref_id'];		
						}
						$quiz_score = !empty($quiz_score) ? $quiz_score . '%' : '-';

						$certificate_link_ = '';
						if( !empty($certificate_link) ){
							$certificate_link_ = $certificate_link .'&time=' . $user_quiz_meta_[$quiz_attempt_key]['time']; 	
						}
						
						// Essays 
	                    $essays_data = array();
	                    if($quiz_id /* && $quiz['status'] != 'notcompleted' */ ){ 
	                        $quiz_questions = learndash_get_quiz_questions($quiz_id);
	                        foreach($quiz_questions as $quiz_question_id => $quiz_question_pro_id){
	                            $question_type = get_post_meta( $quiz_question_id, 'question_type', true );
	                            if($question_type === 'essay'){
	                                if( isset($user_quiz_meta_[$quiz_attempts_key[0]]['graded']) ){
	                                    $essay_post_id = end($user_quiz_meta_[$quiz_attempts_key[0]]['graded'])['post_id'];
	                                    
	                                    if ( !is_null(get_post($essay_post_id) ) ){ 
		                                    $question_post_id = (int) get_post_meta( $essay_post_id, 'question_post_id', true );
		                                    $question_post_title = get_the_title($question_post_id);
		                                    $essay_comments_number = get_comments_number($essay_post_id);
		                                    $last_essay_attempted_details = learndash_get_essay_details($essay_post_id);
		                                    $essays_data[] = array("essay_id" 		=> $essay_post_id,
		                                                                        "essay_url"			=> get_post_permalink($essay_post_id),
		                                                                        "question_post_id" 	=> $question_post_id,
		                                                                        "question_post_title" => $question_post_title,
		                                                                        "points"			=> $last_essay_attempted_details['points'],
		                                                                        "status" 			=> $last_essay_attempted_details['status'],
		                                                                        "comments"			=> $essay_comments_number
		                                                                    );
		                                }
	                                }
	                            }
	                        }
	                    }

						$quizzes_data['quiz_attempts'][] = array("quiz_id"=>$quiz_id,
																"quiz_name"=>$quiz['post']->post_title,
																"quiz_url"=>get_post_permalink($quiz_id),
																"quiz_score" => $quiz_score,
																"quiz_certificate" => $certificate_link_,
																"quiz_statistics_on" => $quiz_pro_statistics_on,
																"pro_quizid" => $pro_quizid,
																"statistic_ref_id" => $statistic_ref_id,
																"essays_data" => $essays_data
																);
					}

                    

					$user_courses_lessons_quizzies['course_quizzes_data'][] = $quizzes_data;	
				}
			}
		}
	}
}

// user meta of unlock/lock lesson of course
$ldc_lock_courses_lessons = get_user_meta($user_courses_lessons_quizzies['user_id'] , 'ldc_lock_courses_lessons', true);
if(!empty($ldc_lock_courses_lessons) && isset( $ldc_lock_courses_lessons[$user_courses_lessons_quizzies['course_id']])){
	$ldc_lock_courses_lessons = $ldc_lock_courses_lessons[$user_courses_lessons_quizzies['course_id']];
}
else{
	$ldc_lock_courses_lessons = array();
}

?>

<div class="div-class-container">
    <h5 class="course-progress-percentage"><?php esc_html_e( 'Course: ' . (isset($user_courses_lessons_quizzies['course_name'])?$user_courses_lessons_quizzies['course_name']:"") . ' ( ' . (isset($user_courses_lessons_quizzies['course_progress_percentage'])? $user_courses_lessons_quizzies['course_progress_percentage'] : "0") . '% )', 'lt-learndash-classroom' ); ?>
    </h5>
    <h5 class="course-time-spent"><?php esc_html_e( 'Time spent: ', 'lt-learndash-classroom' ); echo do_shortcode('[ldc_time user-id="' . $user_courses_lessons_quizzies['user_id'] . '" course-id="' . $user_courses_lessons_quizzies['course_id'] . '"]') ?></h5>
    <?php if(!empty($user_courses_lessons_quizzies['course_certificate'])) : ?>
    <p><?php _e('Certificate', 'lt-learndash-classroom' ); ?>: <a class="btn"
            href="<?php echo esc_url($user_courses_lessons_quizzies['course_certificate']); ?>"
            target="_blank"><?php _e('Print PDF', 'lt-learndash-classroom' ); ?></a>
    </p>
    <?php endif; ?>
</div>

<div class="div-table-container">
    <div class="div-table lesson-table"
        data-nonce="<?php echo esc_attr( wp_create_nonce( 'ldc-lesson-table-nonce' ) ); ?>"
        data-course-id="<?php esc_attr_e( $user_courses_lessons_quizzies['course_id' ] ); ?>"
        data-user-id="<?php esc_attr_e( $user_courses_lessons_quizzies['user_id' ] ); ?>">
        <div class="div-table-row-header">
            <div class="div-table-col ld-lock-unlock-header" align="center">
                <?php esc_html_e( 'Lock', 'lt-learndash-classroom' ); ?>
            </div>
            <div class="div-table-col" align="center">
                <?php echo sprintf( esc_html_x( '%s', 'Lesson', 'learndash' ), esc_attr( LearnDash_Custom_Label::get_label( 'lesson' ) ) ); ?>
            </div>
            <div class="div-table-col"><?php esc_html_e( 'Completed On', 'lt-learndash-classroom' ); ?></div>
            <div class="div-table-col"><?php esc_html_e( 'Time Spent', 'lt-learndash-classroom' ); ?></div>
            <span class="ldc-icon-arrow main-list"></span>
        </div>
        <?php 
		if(empty($user_courses_lessons_quizzies['lessons_data'])){
			?>
        <div class="div-table-row-parent">
            <div class="div-table-row" align="center">
                <?php _e('No record found.','lt-learndash-classroom'); ?>
            </div>
        </div>
        <?php
		}
		if( !empty($user_courses_lessons_quizzies['lessons_data']))
		foreach($user_courses_lessons_quizzies['lessons_data'] as $lesson_row){  ?>
        <div class="div-table-row-parent">
            <div class="div-table-row" style="color:<?php echo $lesson_row['lesson_color']; ?>"
                data-lesson-id="<?php esc_attr_e($lesson_row['lesson_id']); ?>">
                <span class="ld-lock-unlock"><i
                        class="fas <?php echo (!empty($ldc_lock_courses_lessons) && $ldc_lock_courses_lessons['lesson_id'] == $lesson_row['lesson_id']?"fa-lock": "fa-unlock"); ?>"></i></span>
                <div class="div-table-col"><a href="<?php esc_attr_e($lesson_row['lesson_url']); ?>"
                        target="_blank"><?php esc_html_e( $lesson_row['lesson_name']); ?></a>
                </div>
                <div class="div-table-col"><?php esc_html_e( $lesson_row['lesson_completion_time']); ?></div>
                <div class="div-table-col"><?php esc_html_e( $lesson_row['time_spent']); ?></div>

                <?php if(!empty($lesson_row['topics_data']) || !empty($lesson_row['quizzes_data']) || !empty($lesson_row['assignments_data'])) : ?>
                <span class="ldc-icon-arrow"></span>
                <?php endif;?>

            </div>
            <?php if(!empty($lesson_row['topics_data']) || !empty($lesson_row['quizzes_data']) || !empty($lesson_row['assignments_data'])) : ?>
            <div class="lesson-content">
                <?php if(!empty($lesson_row['topics_data'])) : ?>
                <h6>Topics:</h6>
                <div class="div-table topic-table">
                    <div class="div-table-row-header">
                        <div class="div-table-col" align="center">
                            <?php echo sprintf( esc_html_x( '%s', 'Topic', 'learndash' ), esc_attr( LearnDash_Custom_Label::get_label( 'topic' ) ) ); ?>
                        </div>
                        <div class="div-table-col"><?php esc_html_e( 'Completed On', 'lt-learndash-classroom' ); ?></div>
                        <div class="div-table-col"><?php esc_html_e( 'Time Spent', 'lt-learndash-classroom' ); ?></div>
                    </div>
                    <?php foreach($lesson_row['topics_data'] as $topic_row) { ?>
                    <div class="div-table-row-parent">
                        <div class="div-table-row" style="color:<?php echo $topic_row['topic_color']; ?>">
                            <div class="div-table-col">
                                <a href="<?php esc_attr_e($topic_row['topic_url']); ?>"
                                    target="_blank"><?php esc_html_e( $topic_row['topic_name']); ?></a>
                            </div>
                            <div class="div-table-col"><?php esc_html_e( $topic_row['topic_completed_date']); ?></div>
                            <div class="div-table-col"><?php esc_html_e( $topic_row['time_spent']); ?></div>
                            <?php if(!empty($topic_row['quizzes_data']) || !empty($topic_row['assignments_data'])) : ?>
                            <span class="ldc-icon-arrow"></span>
                            <?php endif; ?>
                        </div>
                        <?php if(!empty($topic_row['quizzes_data']) || !empty($topic_row['assignments_data'])) : ?>
                        <div class="topic-content">
                            <?php if(!empty($topic_row['quizzes_data'])) : ?>
                            <h6><?php _e('Topic Quizzes:','lt-learndash-classroom') ?></h6>
                            <div class="div-table quiz-table">
                                <div class="div-table-row-header">
                                    <div class="div-table-col" align="center">
                                        <?php echo sprintf( esc_html_x( '%s', 'Quiz', 'learndash' ), esc_attr( LearnDash_Custom_Label::get_label( 'quiz' ) ) ); ?>
                                    </div>
                                    <div class="div-table-col">
                                        <?php esc_html_e( 'Score', 'lt-learndash-classroom' ); ?>
                                    </div>
                                    <div class="div-table-col">
                                        <?php esc_html_e( 'Statistics', 'lt-learndash-classroom' ); ?>
                                    </div>
                                    <div class="div-table-col">
                                        <?php esc_html_e( 'Time Spent', 'lt-learndash-classroom' ); ?>
                                    </div>
                                    <div class="div-table-col">
                                        <?php esc_html_e( 'Certificate', 'lt-learndash-classroom' ); ?>
                                    </div>
                                </div>
                                <?php foreach($topic_row['quizzes_data'] as $quiz_row) { ?>
                                <div class="div-table-row-parent">
                                    <?php foreach($quiz_row['quiz_attempts'] as $quiz_attempt) { ?>
                                    <div class="div-table-row">
                                        <div class="div-table-col">
                                            <a href="<?php esc_attr_e($quiz_attempt['quiz_url']); ?>" target="_blank">
                                                <?php esc_html_e( $quiz_attempt['quiz_name']); ?>
                                            </a>
                                        </div>
                                        <div class="div-table-col"><?php esc_html_e( $quiz_attempt['quiz_score']); ?>
                                        </div>
                                        <div class="div-table-col">
                                            <?php if( $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id'] ) { ?>
                                            <a class="user_statistic"
                                                data-statistic-nonce="<?php echo esc_attr( wp_create_nonce( 'statistic_nonce_' . $quiz_attempt['statistic_ref_id'] . '_' . get_current_user_id() . '_' . $user_id ) ); ?>"
                                                data-user-id="<?php esc_attr_e( $user_id); ?>"
                                                data-quiz-id="<?php esc_attr_e( $quiz_attempt['pro_quizid']); ?>"
                                                data-ref-id="<?php esc_attr_e( $quiz_attempt['statistic_ref_id']); ?>"
                                                href="#"><span class="ld-icon ld-icon-assignment"></span></a>
                                            <?php } else { echo "-"; } ?>
                                        </div>
                                        <div class="div-table-col"><?php _e( $quiz_row['time_spent']); ?></div>
                                        <div class="div-table-col">
                                            <?php if (!empty($quiz_attempt['quiz_certificate'])){ ?>
                                            <a class="certificate-icon"
                                                href="<?php esc_attr_e( $quiz_attempt['quiz_certificate']); ?>"
                                                target="_blank">
                                                <img width="50px"
                                                    src="<?php echo LT_LD_CLASSROOM_URL; ?>img/certificate.png"
                                                    alt="<?php _e('Print PDF','lt-learndash-classroom'); ?>"
                                                    srcset="<?php echo LT_LD_CLASSROOM_URL; ?>img/certificate.svg">
                                                <path id="Layer_1" />
                                                </img>
                                            </a>
                                            <?php } else { echo "-"; } ?>
                                        </div>
                                    </div>

                                    	<?php if(!empty($quiz_attempt['essays_data'])) : ?>
	                                    <div class="quiz-content">
	                                        <div class="div-table essay-table">
	                                            <div class="div-table-row-header">
	                                                <div class="div-table-col" align="center">
	                                                    <?php esc_html_e( 'Essays', 'lt-learndash-classroom' ); ?>
	                                                </div>
	                                                <div class="div-table-col">
	                                                    <?php esc_html_e( 'Comments', 'lt-learndash-classroom' ); ?>
	                                                </div>
	                                                <div class="div-table-col">
	                                                    <?php esc_html_e( 'Status', 'lt-learndash-classroom' ); ?>
	                                                </div>
	                                                <div class="div-table-col">
	                                                    <?php esc_html_e( 'Points', 'lt-learndash-classroom' ); ?>
	                                                </div>
	                                            </div>

	                                            <?php foreach($quiz_attempt['essays_data'] as $essay_row) : ?>
	                                            <div class="div-table-row">
	                                                <div class="div-table-col">
	                                                    <a href="<?php echo esc_url($essay_row['essay_url']); ?>"
	                                                        target="_blank">
	                                                        <?php esc_html_e( $essay_row['question_post_title']); ?>
	                                                    </a>
	                                                </div>
	                                                <div class="div-table-col">
	                                                    <a href="<?php echo esc_url($essay_row['essay_url']); ?>"
	                                                        target="_blank">
	                                                        <span class="ld-icon ld-icon-comments"></span>
	                                                        <?php esc_html_e( $essay_row['comments']); ?>
	                                                    </a>
	                                                </div>
	                                                <div class="div-table-col">
	                                                    <?php 
															if($essay_row['status'] === "graded") :
																echo wp_kses_post( learndash_status_bubble( $essay_row['status'], 'essay', false ) );
															else: 
																?>
	                                                    <p class="essay-status">
	                                                        <input type="number"
	                                                            name="essay_points[<?php echo $essay_row['essay_id']; ?>]"
	                                                            min="0" max="<?php _e($essay_row['points']['total']); ?>"
	                                                            step="1" /> /
	                                                        <?php _e($essay_row['points']['total']); ?>
	                                                        <button type="button" class="ldc_apr_essay_btn"
	                                                            data-essay-id="<?php echo esc_attr( $essay_row['essay_id']); ?>"><?php _e( 'Approve','lt-learndash-classroom' ); ?></button>
	                                                    </p>
	                                                    <?php endif; ?>
	                                                </div>
	                                                <div class="div-table-col">
	                                                    <?php esc_html_e( $essay_row['points']['awarded'] . '/' . $essay_row['points']['total'] ); ?>
	                                                </div>
	                                            </div>
	                                            <?php endforeach; // eassys_data  ?>
	                                        </div>
	                                    </div>
	                                    <?php endif; // eassys_data  ?>

                                    <?php } ?>
                                    
                                </div>
                                <?php } ?>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($topic_row['assignments_data'])) : ?>
                            <h6><?php _e('Topic Assignments:','lt-learndash-classroom') ?></h6>
                            <div class="div-table assignment-table">
                                <div class="div-table-row-header">
                                    <div class="div-table-col div-assignment-name" align="center">
                                        <?php esc_html_e( 'File Name', 'lt-learndash-classroom' ); ?>
                                    </div>
                                    <div class="div-table-col div-comments" align="center">
                                        <?php esc_html_e( 'Comments', 'lt-learndash-classroom' ); ?>
                                    </div>
                                    <div class="div-table-col div-approve" align="center">
                                        <?php esc_html_e( 'Action', 'lt-learndash-classroom' ); ?>
                                    </div>
                                </div>
                                <?php foreach($topic_row['assignments_data'] as $assignment_row) { ?>
                                <div class="div-table-row">
                                    <div class="div-table-col div-assignment-name"><a
                                            href="<?php echo esc_url( $assignment_row['assignment_media_url']); ?>"
                                            target="_blank"><?php esc_html_e( $assignment_row['assignment_name']); ?></a>
                                    </div>
                                    <div class="div-table-col div-comments">
                                        <a href="<?php echo esc_url($assignment_row['assignment_url']); ?>"
                                            target="_blank">
                                            <span class="ld-icon ld-icon-comments"></span>
                                            <?php esc_html_e( $assignment_row['comments']); ?>
                                        </a>
                                    </div>
									<div class="div-table-col div-approve <?php  _e($assignment_row['is_approved']?'approved':'')?>" align="center">
									<?php 
									if ( $assignment_row['is_gf_enabled_for_post'] ) {										
										if($assignment_row['is_approved'] || $assignment_row['gf_assignment_status'] === "approved") :
											?>
											<a href="javascript:;" 
												id="assignment-id-<?php echo esc_attr( $assignment_row['assignment_id']); ?>" 
												class="btn-ldc-assignment-approved ldc_apr_btn"
												data-assignment-id="<?php echo esc_attr( $assignment_row['assignment_id']); ?>">
												<?php _e( 'Approved','lt-learndash-classroom' ); ?>
											</a>
											<?php
										elseif($assignment_row['gf_assignment_status'] === "resubmit") : 
											?>
											<a href="<?php echo esc_url( $assignment_row['gf_page_url'] ); ?>" target="_blank" class="btn-ldc-assignment-rejected">
												<?php _e( 'Resubmit','lt-learndash-classroom' ); ?>
											</a>
											<?php
										else:
											?>
											<a href="<?php echo esc_url( $assignment_row['gf_page_url'] ); ?>" target="_blank" class="btn-ldc-assignment-grade">
												<?php _e( 'Grade','lt-learndash-classroom' ); ?>
											</a>
											<?php
										endif;
									}
									else{
									 	if($assignment_row['is_approved']) : 
											 _e( 'Approved','lt-learndash-classroom' ); 
										else: 
											if($assignment_row['is_points_enabled']) : ?>
											<p>
												<input type="number"
													name="assignment[<?php echo $assignment_row['assignment_id']; ?>]"
													min="0" max="<?php _e($assignment_row['assingment_points']); ?>"
													step="1" /> /
												<?php _e($assignment_row['assingment_points']); ?>
											</p>
                                        	<?php endif; ?>

                                        <button type="button" class="ldc_apr_btn"
                                            data-assignment-id="<?php echo esc_attr( $assignment_row['assignment_id']); ?>"><?php _e( 'Approve','lt-learndash-classroom' ); ?></button>
                                        <?php endif; ?>
									<?php } ?>
									</div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php endif; ?>

                        </div>
                        <?php endif; ?>
                    </div>
                    <?php } ?>

                </div>
                <?php endif; ?>

                <?php if(!empty($lesson_row['quizzes_data'])) : ?>
                <h6><?php _e( 'Quizzes:','lt-learndash-classroom' ); ?></h6>
                <div class="div-table quiz-table">
                    <div class="div-table-row-header">
                        <div class="div-table-col" align="center">
                            <?php echo sprintf( esc_html_x( '%s', 'Quiz', 'learndash' ), esc_attr( LearnDash_Custom_Label::get_label( 'quiz' ) ) ); ?>
                        </div>
                        <div class="div-table-col"><?php esc_html_e( 'Score', 'lt-learndash-classroom' ); ?></div>
                        <div class="div-table-col">
                            <?php esc_html_e( 'Statistics', 'lt-learndash-classroom' ); ?>
                        </div>
                        <div class="div-table-col">
                            <?php esc_html_e( 'Time Spent', 'lt-learndash-classroom' ); ?>
                        </div>
                        <div class="div-table-col"><?php esc_html_e( 'Certificate', 'lt-learndash-classroom' ); ?>
                        </div>
                    </div>
                    <?php foreach($lesson_row['quizzes_data'] as $quiz_row) { ?>
                    <div class="div-table-row-parent">
                        <?php foreach($quiz_row['quiz_attempts'] as $quiz_attempt) { ?>
                        <div class="div-table-row">
                            <div class="div-table-col">
                                <a href="<?php esc_attr_e($quiz_attempt['quiz_url']); ?>" target="_blank">
                                    <?php esc_html_e( $quiz_attempt['quiz_name']); ?>
                                </a>
                            </div>
                            <div class="div-table-col"><?php esc_html_e( $quiz_attempt['quiz_score']); ?></div>
                            <div class="div-table-col">
                                <?php 
								if ( \ld_classroom\SharedFunctions :: is_quiz_notification_for_ld_active() ){
									echo '<a class="elc_ldquiz_load" >Hello <span class="ld-icon ld-icon-assignment"></span></a>';
								}
								else if( $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id']) { ?>
                                <a class="user_statistic"
                                    data-statistic-nonce="<?php echo esc_attr( wp_create_nonce( 'statistic_nonce_' . $quiz_attempt['statistic_ref_id'] . '_' . get_current_user_id() . '_' . $user_id ) ); ?>"
                                    data-user-id="<?php esc_attr_e( $user_id); ?>"
                                    data-quiz-id="<?php esc_attr_e( $quiz_attempt['pro_quizid']); ?>"
                                    data-ref-id="<?php esc_attr_e( $quiz_attempt['statistic_ref_id']); ?>"
                                    href="#"><span class="ld-icon ld-icon-assignment"></span></a>
                                <?php } else { echo "-"; } ?>
                            </div>
                            <div class="div-table-col"><?php _e( $quiz_row['time_spent']); ?></div>
                            <div class="div-table-col">
                                <?php if (!empty($quiz_attempt['quiz_certificate'])){
									?>
                                <a class="certificate-icon"
                                    href="<?php esc_attr_e( $quiz_attempt['quiz_certificate']); ?>" target="_blank">
                                    <img width="50px" src="<?php echo LT_LD_CLASSROOM_URL; ?>img/certificate.png"
                                        alt="<?php _e('Print PDF','lt-learndash-classroom'); ?>"
                                        srcset="<?php echo LT_LD_CLASSROOM_URL; ?>img/certificate.svg">
                                    <path id="Layer_1" />
                                    </img>
                                </a>
                                <?php } else { echo "-"; } ?>
                            </div>
                        </div>

	                        <?php if(!empty($quiz_attempt['essays_data'])) : ?>
	                        <div class="quiz-content">
	                            <div class="div-table essay-table">
	                                <div class="div-table-row-header">
	                                    <div class="div-table-col" align="center">
	                                        <?php esc_html_e( 'Essays', 'lt-learndash-classroom' ); ?>
	                                    </div>
	                                    <div class="div-table-col">
	                                        <?php esc_html_e( 'Comments', 'lt-learndash-classroom' ); ?>
	                                    </div>
	                                    <div class="div-table-col">
	                                        <?php esc_html_e( 'Status', 'lt-learndash-classroom' ); ?>
	                                    </div>
	                                    <div class="div-table-col">
	                                        <?php esc_html_e( 'Points', 'lt-learndash-classroom' ); ?>
	                                    </div>
	                                </div>

	                                <?php foreach($quiz_attempt['essays_data'] as $essay_row) : ?>
	                                <div class="div-table-row">
	                                    <div class="div-table-col">
	                                        <a href="<?php echo esc_url($essay_row['essay_url']); ?>" target="_blank">
	                                            <?php esc_html_e( $essay_row['question_post_title']); ?>
	                                        </a>
	                                    </div>
	                                    <div class="div-table-col">
	                                        <a href="<?php echo esc_url($essay_row['essay_url']); ?>" target="_blank">
	                                            <span class="ld-icon ld-icon-comments"></span>
	                                            <?php esc_html_e( $essay_row['comments']); ?>
	                                        </a>
	                                    </div>
	                                    <div class="div-table-col">
	                                        <?php 
											if($essay_row['status'] === "graded") :
												echo wp_kses_post( learndash_status_bubble( $essay_row['status'], 'essay', false ) );
											else: 
												?>
	                                        <p class="essay-status">
	                                            <input type="number"
	                                                name="essay_points[<?php echo $essay_row['essay_id']; ?>]" min="0"
	                                                max="<?php _e($essay_row['points']['total']); ?>" step="1" /> /
	                                            <?php _e($essay_row['points']['total']); ?>
	                                            <button type="button" class="ldc_apr_essay_btn"
	                                                data-essay-id="<?php echo esc_attr( $essay_row['essay_id']); ?>"><?php _e( 'Approve','lt-learndash-classroom' ); ?></button>
	                                        </p>
	                                        <?php endif; ?>
	                                    </div>
	                                    <div class="div-table-col">
	                                        <?php esc_html_e( $essay_row['points']['awarded'] . '/' . $essay_row['points']['total'] ); ?>
	                                    </div>
	                                </div>
	                                <?php endforeach; // eassys_data  ?>
	                            </div>
	                        </div>
	                        <?php endif; // eassys_data ?>

                        <?php } ?>
                        
                    </div>
                    <?php } ?>
                </div>
                <?php endif; ?>

                <?php if(!empty($lesson_row['assignments_data'])) : ?>
                <h6><?php _e( 'Assignments:','lt-learndash-classroom' ); ?></h6>
                <div class="div-table assignment-table">
                    <div class="div-table-row-header">
                        <div class="div-table-col div-assignment-name" align="center">
                            <?php esc_html_e( 'File Name', 'lt-learndash-classroom' ); ?>
                        </div>
                        <div class="div-table-col div-comments" align="center">
                            <?php esc_html_e( 'Comments', 'lt-learndash-classroom' ); ?>
                        </div>
                        <div class="div-table-col div-approve" align="center">
                            <?php esc_html_e( 'Action', 'lt-learndash-classroom' ); ?>
                        </div>
                    </div>
                    <?php 
						foreach($lesson_row['assignments_data'] as $assignment_row) { 
						//print_r($assignment_row); ?>
                    <div class="div-table-row">
                        <div class="div-table-col div-assignment-name"><a
                                href="<?php echo esc_url( $assignment_row['assignment_media_url']); ?>"
                                target="_blank"><?php esc_html_e( $assignment_row['assignment_name']); ?></a>
                        </div>
                        <div class="div-table-col div-comments">
                            <a href="<?php echo esc_url($assignment_row['assignment_url']); ?>" target="_blank">
                                <span class="ld-icon ld-icon-comments"></span>
                                <?php esc_html_e( $assignment_row['comments']); ?>
                            </a>
                        </div>
						<div class="div-table-col div-approve <?php _e($assignment_row['is_approved']?'approved':'')?>"
                            align="center">
						<?php 
						if ( $assignment_row['is_gf_enabled_for_post'] ) {
							if($assignment_row['is_approved'] || $assignment_row['gf_assignment_status'] === "approved") :
								?>
								<a href="javascript:;" 
									id="assignment-id-<?php echo esc_attr( $assignment_row['assignment_id']); ?>" 
									class="btn-ldc-assignment-approved ldc_apr_btn"
                                	data-assignment-id="<?php echo esc_attr( $assignment_row['assignment_id']); ?>">
									<?php _e( 'Approved','lt-learndash-classroom' ); ?>
								</a>
								<?php
							elseif($assignment_row['gf_assignment_status'] === "resubmit") : 
								?>
								<a href="<?php echo esc_url($assignment_row['gf_page_url']); ?>" target="_blank" class="btn-ldc-assignment-rejected">
									<?php _e( 'Resubmit','lt-learndash-classroom' ); ?>
								</a>
								<?php
							else:
								?>
								<a href="<?php echo esc_url($assignment_row['gf_page_url']); ?>" target="_blank" class="btn-ldc-assignment-grade">
									<?php _e( 'Grade','lt-learndash-classroom' ); ?>
								</a>
								<?php
							endif;
						}
						else{
							if($assignment_row['is_approved']) :
								_e( 'Approved','lt-learndash-classroom' );
							else: 
								if($assignment_row['is_points_enabled']) :
								?>
								<p>
									<input type="number" name="assignment[<?php echo $assignment_row['assignment_id']; ?>]"
										min="0" max="<?php _e($assignment_row['assingment_points']); ?>" step="1" /> /
									<?php _e($assignment_row['assingment_points']); ?>
								</p>
                            	<?php endif; ?>
                            <button type="button" class="ldc_apr_btn"
                                data-assignment-id="<?php echo esc_attr( $assignment_row['assignment_id']); ?>"><?php _e( 'Approve','lt-learndash-classroom' ); ?></button>
                            <?php endif; ?>
						<?php }?>
						</div>
                    </div>
                    <?php } ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php } // foreach of lesson ?>


        <?php if(!empty($user_courses_lessons_quizzies['course_quizzes_data'])) : ?>
        <h5><?php _e('Course Quizzes', 'lt-learndash-classroom'); ?></h5>
        <div class="course-quizzes-content">
            <div class="div-table quiz-table">
                <div class="div-table-row-header div-table-row-header-course-quizzes">
                    <div class="div-table-col" align="center">
                        <?php echo sprintf( esc_html_x( '%s', 'Quiz', 'learndash' ), esc_attr( LearnDash_Custom_Label::get_label( 'quiz' ) ) ); ?>
                    </div>
                    <div class="div-table-col"><?php esc_html_e( 'Score', 'lt-learndash-classroom' ); ?></div>
                    <div class="div-table-col">
                        <?php esc_html_e( 'Statistics', 'lt-learndash-classroom' ); ?>
                    </div>
                    <div class="div-table-col">
                        <?php esc_html_e( 'Time Spent', 'lt-learndash-classroom' ); ?>
                    </div>
                    <div class="div-table-col"><?php esc_html_e( 'Certificate', 'lt-learndash-classroom' ); ?></div>
                </div>
                <?php 
            // print_r($user_courses_lessons_quizzies['course_quizzes_data']);
            foreach($user_courses_lessons_quizzies['course_quizzes_data'] as $quiz_row) { ?>
                <div class="div-table-row-parent">
                    <?php foreach($quiz_row['quiz_attempts'] as $quiz_attempt) { ?>
                    <div class="div-table-row">
                        <div class="div-table-col">
                            <a href="<?php esc_attr_e($quiz_attempt['quiz_url']); ?>"
                                target="_blank"><?php esc_html_e( $quiz_attempt['quiz_name']); ?>
                            </a>
                        </div>
                        <div class="div-table-col"><?php esc_html_e( $quiz_attempt['quiz_score']); ?></div>
                        <div class="div-table-col">
                            <?php if( $quiz_attempt['quiz_statistics_on'] && $quiz_attempt['statistic_ref_id'] ) { ?>
                            <a class="user_statistic"
                                data-statistic-nonce="<?php echo esc_attr( wp_create_nonce( 'statistic_nonce_' . $quiz_attempt['statistic_ref_id'] . '_' . get_current_user_id() . '_' . $user_id ) ); ?>"
                                data-user-id="<?php esc_attr_e( $user_id); ?>"
                                data-quiz-id="<?php esc_attr_e( $quiz_attempt['pro_quizid']); ?>"
                                data-ref-id="<?php esc_attr_e( $quiz_attempt['statistic_ref_id']); ?>" href="#"><span
                                    class="ld-icon ld-icon-assignment"></span></a>
                            <?php } else { echo "-"; } ?>
                        </div>
                        <div class="div-table-col"><?php _e( $quiz_row['time_spent']); ?></div>
                        <div class="div-table-col">
                            <?php if (!empty($quiz_attempt['quiz_certificate'])){
									?>
                            <a class="certificate-icon" href="<?php esc_attr_e( $quiz_attempt['quiz_certificate']); ?>"
                                target="_blank">
                                <img width="50px" src="<?php echo LT_LD_CLASSROOM_URL; ?>img/certificate.png"
                                    alt="<?php _e('Print PDF','lt-learndash-classroom'); ?>"
                                    srcset="<?php echo LT_LD_CLASSROOM_URL; ?>img/certificate.svg">
                                <path id="Layer_1" />
                                </img>
                            </a>
                            <?php } else { echo "-"; } ?>
                        </div>
                    </div>
                    	<?php if(!empty($quiz_attempt['essays_data'])) : ?>
	                    <div class="quiz-content">
	                        <div class="div-table essay-table">
	                            <div class="div-table-row-header">
	                                <div class="div-table-col" align="center">
	                                    <?php esc_html_e( 'Essays', 'lt-learndash-classroom' ); ?>
	                                </div>
	                                <div class="div-table-col">
	                                    <?php esc_html_e( 'Comments', 'lt-learndash-classroom' ); ?>
	                                </div>
	                                <div class="div-table-col">
	                                    <?php esc_html_e( 'Status', 'lt-learndash-classroom' ); ?>
	                                </div>
	                                <div class="div-table-col">
	                                    <?php esc_html_e( 'Points', 'lt-learndash-classroom' ); ?>
	                                </div>
	                            </div>

	                            <?php foreach($quiz_attempt['essays_data'] as $essay_row) : ?>
	                            <div class="div-table-row">
	                                <div class="div-table-col">
	                                    <a href="<?php echo esc_url($essay_row['essay_url']); ?>" target="_blank">
	                                        <?php esc_html_e( $essay_row['question_post_title']); ?>
	                                    </a>
	                                </div>
	                                <div class="div-table-col">
	                                    <a href="<?php echo esc_url($essay_row['essay_url']); ?>" target="_blank">
	                                        <span class="ld-icon ld-icon-comments"></span>
	                                        <?php esc_html_e( $essay_row['comments']); ?>
	                                    </a>
	                                </div>
	                                <div class="div-table-col">
	                                    <?php 
											if($essay_row['status'] === "graded") :
												echo wp_kses_post( learndash_status_bubble( $essay_row['status'], 'essay', false ) );
											else: 
												?>
	                                    <p class="essay-status">
	                                        <input type="number" name="essay_points[<?php echo $essay_row['essay_id']; ?>]"
	                                            min="0" max="<?php _e($essay_row['points']['total']); ?>" step="1" /> /
	                                        <?php _e($essay_row['points']['total']); ?>
	                                        <button type="button" class="ldc_apr_essay_btn"
	                                            data-essay-id="<?php echo esc_attr( $essay_row['essay_id']); ?>"><?php _e( 'Approve','lt-learndash-classroom' ); ?></button>
	                                    </p>
	                                    <?php endif; ?>
	                                </div>
	                                <div class="div-table-col">
	                                    <?php esc_html_e( $essay_row['points']['awarded'] . '/' . $essay_row['points']['total'] ); ?>
	                                </div>
	                            </div>
	                            <?php endforeach; // eassys_data  ?>
	                        </div>
	                    </div>
	                    <?php endif; // eassys_data ?>

                    <?php } ?>
                    
                </div>
                <?php } ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Statictics overlay popup -->
<div id="wpProQuiz_user_overlay" style="display: none;">
    <div class="wpProQuiz_modal_window" style="padding: 20px; overflow: scroll;">
        <input type="button" value="Close" class="button-primary"
            style="position: fixed; top: 48px; right: 59px; z-index: 160001;" id="wpProQuiz_overlay_close">
        <div id="wpProQuiz_user_content" style="margin-top: 20px;"></div>
        <div id="wpProQuiz_loadUserData" class="wpProQuiz_blueBox"
            style="background-color: #F8F5A8; display: none; margin: 50px;">
            <img alt="load" src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" />
            <?php esc_html_e('Loading','lt-learndash-classroom'); ?>
        </div>
    </div>
    <div class="wpProQuiz_modal_backdrop"></div>
</div>