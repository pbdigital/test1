<?php $form_id = "import_classroom_student"; ?>
<!-- The Modal for adding student classroom-->
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <div class="classroom-modal-header text-center">
            <?php esc_html_e('IMPORT NEW STUDENTS - MULTIPLE ENTRIES','lt-learndash-classroom'); ?><span
                class="classroom-close import-close">&times;</span></div>
        <div class="classroom-modal-container">
            <form action="" method="POST" enctype="multipart/form-data" id="<?php echo $form_id; ?>"
                data-nonce="<?php echo wp_create_nonce( 'lt-learndash-classroom-import-students' );?>">
                <input type="hidden" name="group"
                    value="<?php echo get_query_var( 'group_id' , false) != '' ? get_query_var( 'group_id',false ) : $_GET['group'] ; ?>" />
                <div class="form_message"></div>
                <div class="import_classrrom_description">
                    <?php esc_html_e('Set up your CSV by following instructions in links below:','lt-learndash-classroom'); ?>
                </div>
                <div class="import_classrrom_template">
                    <a href="<?php echo LT_LD_CLASSROOM_URL . 'data/UsersDemo.csv' ; ?>">
                        <img src="<?php echo LT_LD_CLASSROOM_URL .'img/csv_icon.png'; ?>" /><br />
                        <?php esc_html_e('Download CSV template','lt-learndash-classroom'); ?>
                    </a>
                </div>
                <div class="import_classrrom_upload_file form-group">
                    <div class="import_classrrom_upload_label">
                        <?php esc_html_e('Upload File:','lt-learndash-classroom'); ?> </div>
                    <div class="import_classrrom_upload_csv">
                        <div class="upload-import-wrapper">
                            <button class="btn_classroom"><?php esc_html_e('BROWSE','lt-learndash-classroom'); ?> <i
                                    class="fa fa-angle-right"></i></button>
                            <input type="file" name="classroom_csv" id="classroom_file_import" />
                        </div>
                    </div>
                    <div class="import_classrrom_upload_filename">
                        <?php esc_html_e('File name will appear here','lt-learndash-classroom'); ?></div>
                </div>
                <div class="form-action" style="margin-bottom:-20px;">
                    <button type="submit" class="btn_classroom"
                        id="<?php echo $form_id; ?>_btn"><?php esc_html_e('SAVE','lt-learndash-classroom'); ?><i
                            class="fa fa-angle-right"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>