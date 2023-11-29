<?php $form_id = "add_seats"; ?>
<!-- The Modal for adding student classroom-->
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <div class="classroom-modal-header text-center">
            <?php esc_html_e('ADD SEATS','lt-learndash-classroom'); ?><span
                class="classroom-close">&times;</span></div>
        <div class="classroom-modal-container">
            <form action="" method="GET" id="<?php echo $form_id; ?>"
                data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-add-student' );?>">
                <div class="form_message"></div>
                <input type="hidden" name="add-ldc-group-seat" value="true"/>
                <input type="hidden" name="group-id" value="<?php echo  general_encrypt_decrypt('decrypt',  ( get_query_var( 'group_id' , false) != '' ? get_query_var( 'group_id', false ) : $_GET['group'] ) ) ; ?>"/>
                <input type="hidden" name="qty" value="1"/>
                <div class="form-group section">
                    <?php 
                        $products = [];
                        if ( \ld_classroom\SharedFunctions::is_woocommerce_active() ) {
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
                            $products = wc_get_products( $query_args );
                        }
                        
                        if ( count($products) ) { 
                    ?>
                    <div class="col grid_4_of_4">
                        <input type="radio" id="add-non-recurring-cost-seat" name="add-seats-type" value="add-non-recurring-cost-seat" checked>
                        <label
                            for="add-non-recurring-cost-seat"><?php esc_html_e('Add non-recurring cost seat','lt-learndash-classroom'); ?>
                        </label>
                    </div>
                    <?php } ?>
                    <div class="col grid_4_of_4" style="margin-left:0px;">
                        <input type="radio" id="add-recurring-cost-seat" name="add-seats-type" value="add-recurring-cost-seat" checked>
                        <label
                            for="add-recurring-cost-seat"><?php esc_html_e('Add recurring cost seat','lt-learndash-classroom'); ?>
                        </label>
                    </div>
                </div>
                
                 
                <div class="form-action section text-right pull-left action-buttons">
                    <div class="col grid_1_of_4">
                        <button type="submit" class="btn_classroom"
                            id="<?php echo $form_id; ?>_btn"><?php esc_html_e('Add','lt-learndash-classroom'); ?> <i
                                class="fa fa-angle-right"></i></button>
                    </div>
                </div>
                <div class="form-action section text-right"></div>
            </form>
        </div>
    </div>
</div>