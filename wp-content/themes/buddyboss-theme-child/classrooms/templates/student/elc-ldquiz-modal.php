<?php $form_id = "elc_ldquiz_notification"; ?>
<!-- The Modal for adding student classroom-->
<div id="<?php echo $form_id; ?>_modal" class="classroom-modal">
    <!-- Modal content -->
    <div class="classroom-modal-content">
        <div class="classroom-modal-header text-center"><?php esc_html_e('Quiz Notification','lt-learndash-classroom');?>
            <span class="classroom-close">&times;</span>
        </div>
        <div class="classroom-modal-container">
            <p><?php _e('Please wait to load...', 'lt-learndash-classroom');?></p>
        </div>
    </div>
</div>