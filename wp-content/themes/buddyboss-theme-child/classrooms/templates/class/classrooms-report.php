<div class="ldc_classrooms_report_list">
    <h3><?php esc_html_e('Classrooms Report', 'lt-learndash-classroom'); ?></h3>
    <div class="div-table-container">
        <div class="div-table classrooms-report-table"
            data-nonce="<?php echo wp_create_nonce( 'ldc-classrooms-report' ); ?>"
            data-parent-group-id="<?php _e(\ld_classroom\Group::$parent_group_id)?>">
            <div class="div-table-row-header">
                <div class="div-table-col" align="left">
                    <?php esc_html_e('Table is loading...', 'lt-learndash-classroom'); ?>
                </div>
                <div class="div-table-col">&nbsp;</div>
                <div class="div-table-col">&nbsp;</div>
            </div>
        </div>
    </div>
</div>
<?php 
    $ldc_hide_school_export_csv_button =  get_site_option('ldc_hide_school_export_csv_button', 'no'); 

    if ( $ldc_hide_school_export_csv_button === 'no') {
?>
<div class="div_export_section">
    <a href="" class="btn_classroom"
        id="btn_download_report_csv"><?php esc_html_e('Export CSV','lt-learndash-classroom'); ?> <i
            class="fa fa-angle-right"></i></a>
</div>
<?php } ?>