<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	

class bekarcombdLatestJob extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'bekarcombd_latest_job',
			__('bekar.com.bd - Latest Job', 'bekar-com-bd-jobs-widgets'),
			array( 'description' => __( 'Show latest jobs.', 'bekar-com-bd-jobs-widgets' ), )
		);
	}

	public function widget( $args, $instance ) {
        $bekar_jobs_api_key = get_option('bekar_jobs_api_key');


        $title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$count = isset($instance['count']) ? $instance['count'] : 5;

        $api_key = isset($instance['api_key']) ? $instance['api_key'] : '';
        $api_key = !empty($api_key) ?  $api_key : $bekar_jobs_api_key;


        $api_url = bekar_jobs_api_url;


        $api_url = $api_url.'?api_key='.$api_key.'&per_page='.$count;

		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];

        $response = wp_remote_get( $api_url );
        $body = wp_remote_retrieve_body( $response );
        $response_data =  json_decode($body);

        $jobs = isset($response_data->jobs) ? $response_data->jobs : array();



        ?>
        <ul class="bekar-latest-jobs">
            <?php
            foreach ($jobs as $job){
                $title = isset($job->title) ? $job->title : '';
                $url = isset($job->url) ? $job->url : '';
                $publish_date = isset($job->publish_date) ? $job->publish_date : '';

                ?>
                <li class="bekar-job">
                    <a href="<?php echo $url; ?>?pkey=<?php echo $api_key; ?>"><?php echo $title; ?></a>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
		
		echo $args['after_widget'];
	}
	
	public function form( $instance ) {

        $title = isset($instance['title']) ? $instance['title'] : __( 'Latest Job', 'bekar-com-bd-jobs-widgets' );

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
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Job count:', 'bekar-com-bd-jobs-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
		</p>

        <p>
            <label for="<?php echo $this->get_field_id( 'api_key' ); ?>"><?php _e( 'API key:', 'bekar-com-bd-jobs-widgets' ); ?></label>
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