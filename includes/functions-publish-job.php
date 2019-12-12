<?php
if ( ! defined('ABSPATH')) exit;  // if direct access 
	
add_filter('job_bm_metabox_job_navs', 'job_bm_metabox_job_navs_bekar');

function job_bm_metabox_job_navs_bekar($job_bm_settings_tab){

    $job_bm_settings_tab[] = array(
        'id' => 'bekar',
        'title' => sprintf(__('%s Sync','job-board-manager'),'<i class="fas fa-upload"></i>'),
        'priority' => 9,
        'active' => false,
    );


    return $job_bm_settings_tab;

}




add_action('job_bm_metabox_job_content_bekar','job_bm_metabox_job_content_bekar');


function job_bm_metabox_job_content_bekar($job_id){


    ?>
    <div class="section">
        <div class="section-title"><?php echo __('Job Sync','job-board-manager'); ?></div>
        <p class="section-description"><?php echo __('Job sync to bekar.com.bd','job-board-manager'); ?></p>



        <?php

        $settings_tabs_field = new settings_tabs_field();

        $job_bm_enable_sync = get_post_meta($job_id, 'job_bm_enable_sync', true);

        $args = array(
            'id'		=> 'job_bm_enable_sync',
            //'parent'		=> '',
            'title'		=> __('Enable sync','job-board-manager'),
            'details'	=> __('Send job to bekar.com.bd when published the job.','job-board-manager'),
            'type'		=> 'select',
            'value'		=> $job_bm_enable_sync,
            'default'		=> 'no',
            'args'		=> array('no'=>'No','yes'=>'Yes'),
        );

        $settings_tabs_field->generate_field($args);


        ?>
    </div>
    <?php
}




add_action('job_bm_meta_box_save_job','job_bm_meta_box_save_job_bekar');

function job_bm_meta_box_save_job_bekar($job_id){

    $job_bm_enable_sync = isset($_POST['job_bm_enable_sync']) ? sanitize_text_field($_POST['job_bm_enable_sync']) : '';
    update_post_meta($job_id, 'job_bm_enable_sync', $job_bm_enable_sync);

}



function job_bm_bekar_publish_job($job_id, $post ) {

    $job_bm_enable_sync = get_post_meta($job_id, 'job_bm_enable_sync', true);

    //if($job_bm_enable_sync == 'yes'){
        bekarcombd_sync_job_by_id($job_id );
    //}




}
add_action( 'publish_job', 'job_bm_bekar_publish_job', 10, 2 );



function job_bm_bekar_post_updated_messages($messages ) {

    $messages['job'] = array(
        0  => '', // Unused. Messages start at index 1.
        1  => __( 'Job updated & synced to bekar.com.bd' ) ,

    );

return $messages;

}
add_filter( 'post_updated_messages', 'job_bm_bekar_post_updated_messages', 10);