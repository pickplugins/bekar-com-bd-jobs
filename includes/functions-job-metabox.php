<?php
if ( ! defined('ABSPATH')) exit;  // if direct access 


// Job Board Manager integration

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
        $server_job_url = get_post_meta($job_id, 'server_job_url', true);
        $job_bm_enable_sync = get_post_meta($job_id, 'job_bm_enable_sync', true);

        if($job_bm_enable_sync == 'yes'){
            $sync_status = bekarcombd_sync_job_by_id($job_id );
        }

        $settings_tabs_field = new settings_tabs_field();

        $job_bm_enable_sync = get_post_meta($job_id, 'job_bm_enable_sync', true);

        $args = array(
            'id'		=> 'job_bm_enable_sync',
            //'parent'		=> '',
            'title'		=> __('Sync this job','job-board-manager'),
            'details'	=> __('Send job to bekar.com.bd when published or updated the job.','job-board-manager'),
            'type'		=> 'select',
            'value'		=> $job_bm_enable_sync,
            'default'		=> 'no',
            'args'		=> array('no'=>'No','yes'=>'Yes'),
        );

        $settings_tabs_field->generate_field($args);

        if($job_bm_enable_sync == 'yes'):
            ob_start();
            ?>
            <p>Job link: <a href="<?php echo $server_job_url; ?>">#job link</a> </p>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'job_bm_sync_status',
                //'parent'		=> '',
                'title'		=> __('Sync details','job-board-manager'),
                'details'	=> __('Sync details for this job, you may access to job link at bekar.com.bd','job-board-manager'),
                'type'		=> 'custom_html',
                'html'		=> $html,
            );

            $settings_tabs_field->generate_field($args);


        endif;

        ?>
    </div>
    <?php
}




add_action('job_bm_meta_box_save_job','job_bm_meta_box_save_job_bekar');

function job_bm_meta_box_save_job_bekar($job_id){

    $job_bm_enable_sync = isset($_POST['job_bm_enable_sync']) ? sanitize_text_field($_POST['job_bm_enable_sync']) : '';
    update_post_meta($job_id, 'job_bm_enable_sync', $job_bm_enable_sync);

}


function bekar_jobs_job_add_shortcode_column( $columns ) {
    return array_merge( $columns,
        array( 'sync_status' => __( 'Sync Status', 'job' ) ) );
}
add_filter( 'manage_job_posts_columns' , 'bekar_jobs_job_add_shortcode_column' );


function bekar_jobs_job_posts_shortcode_display( $column, $post_id ) {
    if ($column == 'sync_status'){
        wp_enqueue_style( 'font-awesome-5' );
        $server_job_id = get_post_meta($post_id, 'server_job_id', true);

        if(!empty($server_job_id)){
            ?>
            <i style="color: #009688;" title="Synced" class="fas fa-upload"></i>
            <?php

        }else{
            ?>
            <i style="color: #d0d0d0;" title="Not Synced" class="fas fa-upload"></i>
            <?php
        }
    }
}

add_action( 'manage_job_posts_custom_column' , 'bekar_jobs_job_posts_shortcode_display', 10, 2 );











