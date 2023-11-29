<?php 
$g_id = get_query_var( 'group_id' , false) != '' ? get_query_var( 'group_id' , false) : $_GET['group']; 
$decrypt_group_id = general_encrypt_decrypt('decrypt', $g_id);
$parent_g_id = wp_get_post_parent_id($decrypt_group_id);
?>
<div class="classroom_header_box" data-group-id="<?php echo $g_id; ?>">
    <div class="classroom_title">
        <h5><?php esc_html_e($group_info->post_title); ?></h5>
    </div>
    <div class="text-right">
        <?php 
        
            do_action('ldc_before_classroom_header_button', $decrypt_group_id); 
        
            if ( \ld_classroom\SharedFunctions::is_woocommerce_active() && 'yes' === get_option( 'ldc_enable_wc', 'no' ) && '1' === get_option( 'ldc_enable_add_seats', '0' ) ) {
                $query_args = array(
					'post_type' => 'product',
                    'post_status' => 'publish',
					'tax_query' => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => array('subscription','variable-subscription' ),
						),
					),
					'meta_query' => array( 
                        array(
						    'key' => \ld_classroom\SharedFunctions::$is_individual_seat_purchase_enable,
                            'value' => 'on',
					    )
                    )
				);
                $subscription_products = new WP_Query( $query_args );

                $query_args = array(
                    'post_type' => 'product',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_type',
                            'field'    => 'slug',
                            'terms'    => 'classroom_seats', 
                        ),
                    ),
                );
                $no_subscription_products = wc_get_products( $query_args );

                //echo $query_sql = $products->request;
                // print_r($products);
                
                if ( 'yes' === get_option( 'ldc_allow_teachers_to_add_subscription_seats', 'no' ) 
                    && count($subscription_products->posts) 
                    && count($no_subscription_products) 
                    ) {
                    ?>
                    <a href="" class="btn_classroom" id="btn_add_seats">
                        <?php esc_html_e('ADD SEATS','lt-learndash-classroom'); ?> 
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <?php
                }
                else{
                    
            
                    // Check if the classroom_seats products are there or not, if have any single then only shows the button to add seats.
                    if ( count($subscription_products->posts)  || count($no_subscription_products) ) {                
                        $site = site_url();
                        $add_seat_link = "$site?add-ldc-group-seat=true&group-id=$decrypt_group_id&qty=1"; 
                    ?>
                        <a onclick="location.href='<?php echo $add_seat_link; ?>'" class="btn_classroom">
                            <?php esc_html_e('ADD SEATS','lt-learndash-classroom'); ?> 
                            <i class="fa fa-angle-right"></i>
                        </a>
                    <?php 
                    } //  if ( count($products) ) : 
                }
            }  // if ( \ld_classroom\SharedFunctions::is_woocommerce_active() && 'yes' === get_option( 'ldc_enable_wc', 'no' ) ) 
        ?>

        <?php 
            $ldc_hide_classroom_add_student_button =  get_site_option('ldc_hide_classroom_add_student_button', 'no');
            if ( $ldc_hide_classroom_add_student_button === 'no') {
        ?>
        <a href="" class="btn_classroom"
            id="btn_add_classroom_student"><?php esc_html_e('ADD STUDENT','lt-learndash-classroom'); ?> <i
                class="fa fa-angle-right"></i></a>
        <?php } ?>

        <?php 
            $ldc_hide_classroom_import_list_button =  get_site_option('ldc_hide_classroom_import_list_button', 'no');
            if ( $ldc_hide_classroom_import_list_button === 'no') {
        ?>
        <a href="" class="btn_classroom"
            id="btn_import_classroom_student"><?php esc_html_e('IMPORT LIST','lt-learndash-classroom'); ?> <i
                class="fa fa-angle-right"></i></a>
        <?php } ?>        

        <?php 
            $ldc_hide_classroom_email_classroom_button =  get_site_option('ldc_hide_classroom_email_classroom_button', 'no');
            if ( $ldc_hide_classroom_email_classroom_button === 'no') {
        ?>        
        <a href="#" class="btn_classroom btn_email_classroom init_classroom_student_email"
            data-group="<?php //echo $encrypt_group_id; ?>" data-group-name=""
            data-group-courses=""><?php esc_html_e('EMAIL CLASSROOM','lt-learndash-classroom'); ?> <i
                class="fa fa-angle-right"></i>
        </a>
        <?php } ?>        
        
        <?php do_action('ldc_after_classroom_header_button', $decrypt_group_id); ?>
    </div>
</div>