<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	

class WidgetLatestJob extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'job_bm_widget_latest_job', 
			__('bekar.com.bd - Latest Job', 'job-board-manager-widgets'),
			array( 'description' => __( 'Show latest jobs.', 'job-board-manager-widgets' ), )
		);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$count = isset($instance['count']) ? $instance['count'] : 5;
        $api_key = isset($instance['api_key']) ? $instance['api_key'] : '';

        $api_url = 'https://bekar.com.bd/job-search-api/?api_key='.$api_key.'&per_page='.$count;

		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];

        $response = file_get_contents($api_url);
        $response_data =  json_decode($response);

        $jobs = $response_data->jobs;

        ?>
        <ul class="bekar-latest-jobs">
            <?php
            foreach ($jobs as $job){
                $title = isset($job->title) ? $job->title : '';
                $url = isset($job->url) ? $job->url : '';
                $publish_date = isset($job->publish_date) ? $job->publish_date : '';

                ?>
                <li class="bekar-job">
                    <a href="<?php echo $url; ?>"><?php echo $title; ?></a>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {

        $title = isset($instance['title']) ? $instance['title'] : __( 'Latest Job', 'job-board-manager-widgets' );

        $api_key = isset($instance['api_key']) ? $instance['api_key'] : '';
        $count = isset($instance['count']) ? $instance['count'] : 5;


		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
		

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Job count:', 'job-board-manager-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
		</p>

        <p>
            <label for="<?php echo $this->get_field_id( 'api_key' ); ?>"><?php _e( 'API key:', 'job-board-manager-widgets' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" type="text" value="<?php echo esc_attr( $api_key ); ?>" />
            <div>Get your API key from here <a href="https://bekar.com.bd/job-dashboard/">https://bekar.com.bd/job-dashboard/</a> </div>
        </p>



		<?php 
		
		
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
        $instance['api_key'] = ( ! empty( $new_instance['api_key'] ) ) ? strip_tags( $new_instance['api_key'] ) : '';

		return $instance;
	}
}




add_action('job_bm_latest_jobs_loop','job_bm_latest_jobs_loop', 10, 1);

if(!function_exists('job_bm_latest_jobs_loop')){
    function job_bm_latest_jobs_loop($job_id){


        $class_job_bm_functions = new class_job_bm_functions();
        $job_status_list = $class_job_bm_functions->job_status_list();
        $job_type_list = $class_job_bm_functions->job_type_list();

        $job_bm_default_company_logo = get_option('job_bm_default_company_logo');


        $job_bm_company_logo = get_post_meta($job_id,'job_bm_company_logo', true);
        $job_bm_location = get_post_meta($job_id,'job_bm_location', true);
        $job_bm_job_type = get_post_meta($job_id,'job_bm_job_type', true);
        $job_bm_job_status = get_post_meta($job_id,'job_bm_job_status', true);
        $post_date = get_the_time( 'U', $job_id );
        $job_bm_company_name = get_post_meta($job_id,'job_bm_company_name', true);


        if(!empty($job_bm_company_logo)){

            if(is_serialized($job_bm_company_logo)){

                $job_bm_company_logo = unserialize($job_bm_company_logo);
                if(!empty($job_bm_company_logo[0])){
                    $job_bm_company_logo = $job_bm_company_logo[0];
                    $job_bm_company_logo = wp_get_attachment_url($job_bm_company_logo);
                }
                else{
                    $job_bm_company_logo = $job_bm_default_company_logo;

                }
            }

        }
        else{
            $job_bm_company_logo = $job_bm_default_company_logo;

        }

        //var_dump('ggggggg');

        ?>
        <div class="single">
            <div class="company_logo">
                <img src="<?php echo $job_bm_company_logo; ?>">
            </div>
            <div class="title"><a href="<?php echo get_permalink($job_id); ?>"><?php echo get_the_title($job_id); ?></a></div>

            <div class="job-meta">
                <?php if(!empty($job_bm_company_name)):?>
                    <span class="company-name"><?php echo $job_bm_company_name; ?></span>
                <?php endif; ?>

                <?php if(isset($job_type_list[$job_bm_job_type])):?>
                    <span class="meta-item job_type freelance"><i class="fas fa-briefcase"></i>  <?php echo $job_type_list[$job_bm_job_type]; ?></span>
                <?php endif; ?>

                <?php if(isset($job_status_list[$job_bm_job_status])):?>
                    <span class=" meta-item job_status open"><i class="fas fa-traffic-light"></i> <?php echo $job_status_list[$job_bm_job_status]; ?></span>
                <?php endif; ?>
                <?php if(!empty($job_bm_location)):?>
                    <span class="job-location meta-item"><i class="fas fa-map-marker-alt"></i> <?php echo $job_bm_location; ?></span>
                <?php endif; ?>

                <span class="job-post-date meta-item"><i class="far fa-calendar-alt"></i> <?php echo sprintf(__('Posted %s ago','job-board-manager'), human_time_diff( $post_date, current_time( 'timestamp' ) ) )?></span>
            </div>
        </div>
        <?php

    }
}

add_action('job_bm_latest_jobs_after','job_bm_latest_jobs_after', 10);


if(!function_exists('job_bm_latest_jobs_after')){
    function job_bm_latest_jobs_after($job_id){

        ?>

        <style type="text/css">
            .job_bm_latest_job{}
            .job_bm_latest_job li{
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .job_bm_latest_job .single {
                clear: both;
                display: block;
                margin: 15px 0;
                border-bottom: 1px solid #ddd;
                padding-bottom: 15px;
            }
            .job_bm_latest_job .company_logo {
                width: 50px;
                height: 50px;
                overflow: hidden;
                float: left;
                margin-right: 15px;
            }
            .job_bm_latest_job .title {
                font-size: 15px;

            }

            .job_bm_latest_job a {
                text-decoration: none;

            }
            .job_bm_latest_job .company-name {
                display: inline-block;
                margin-right: 10px;
            }


            .job_bm_latest_job .job-meta {
                /*display: inline-block;*/
            }
            .job_bm_latest_job .job-meta span{
                display: inline-block;
                margin-right: 15px;
                font-size: 12px;
            }


        </style>
        <?php

    }
}
