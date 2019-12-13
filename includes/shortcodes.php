<?php
if ( ! defined('ABSPATH')) exit;  // if direct access 


add_shortcode( 'bekarcombd_jobs_search', 'bekarcombd_jobs_search_display' );

function bekarcombd_jobs_search_display($atts, $content = null ) {

    $atts = shortcode_atts(
        array(
            'api_key' => '',
        ),
        $atts);

    $bekar_jobs_api_key = get_option('bekar_jobs_api_key');


    $api_key = isset($atts['api_key']) ? sanitize_text_field($atts['api_key']) : '';
    $api_key = !empty($api_key) ?  $api_key : $bekar_jobs_api_key;


    $api_url = bekar_jobs_api_url;

    $bekar_keyword = '';
    $bekar_location = '';

    if ( get_query_var('paged') ) {$paged = get_query_var('paged');}
    elseif ( get_query_var('page') ) {$paged = get_query_var('page');}
    else {$paged = 1;}

    $per_page = isset($_GET['per_page']) ? sanitize_text_field($_GET['per_page']) : 10;

    //echo '<pre>'.var_export($api_url, true).'</pre>';


    if(isset( $_GET['bekarcombd_nonce'] ) && wp_verify_nonce( $_GET['bekarcombd_nonce'], 'bekarcombd_nonce' ) ){

        $bekar_keyword = isset($_GET['bekar_keyword']) ? sanitize_text_field($_GET['bekar_keyword']) : '';
        $bekar_location = isset($_GET['bekar_location']) ? sanitize_text_field($_GET['bekar_location']) : '';

        $api_params['action'] = 'job_search';

        $api_params['per_page'] = $per_page;
        $api_params['page_number'] = $paged;
        $api_params['api_key'] = $api_key;
        $api_params['keywords'] = urlencode($bekar_keyword);
        $api_params['locations'] = urlencode($bekar_location);



    }else{
        $api_params['action'] = 'job_search';
        $api_params['per_page'] = 10;
        $api_params['page_number'] = 1;
        $api_params['api_key'] =$api_key;


    }

    $response = wp_remote_get(add_query_arg($api_params, bekar_jobs_api_url), array('timeout' => 20, 'sslverify' => false));

    //$response = wp_remote_get( $api_url );
    $body = wp_remote_retrieve_body( $response );
    $response_data =  json_decode($body);

    $error_messages = isset($response_data->error_messages) ? $response_data->error_messages : array();
    $jobs = isset($response_data->jobs) ? $response_data->jobs : array();
    $found_posts = isset($response_data->found_posts) ? $response_data->found_posts : 0;

    //echo '<pre>'.var_export($error_messages, true).'</pre>';


    ob_start();
    ?>

    <div class="bekarcombd-jobs">

    <?php if(!empty($error_messages)): ?>
        <div class="error">
            <?php

            foreach ($error_messages as $message){
                ?>
                <div class="error-message"><?php echo $message;?></div>
                <?php

            }

            ?>
        </div>
    <?php

        else:

        ?>
            <form method="get" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <input placeholder="Keyword" type="search" value="<?php echo $bekar_keyword; ?>" name="bekar_keyword">
                <input placeholder="Location" type="search" value="<?php echo $bekar_location; ?>" name="bekar_location">

                <?php wp_nonce_field( 'bekarcombd_nonce','bekarcombd_nonce' ); ?>
                <input type="submit" value="Submit">
            </form>

            <ul class="job-list">
                <?php

                if(!empty($jobs)):
                    foreach ($jobs as $job){

                        $title = isset($job->title) ? $job->title : '';
                        $url = isset($job->url) ? $job->url : '';
                        $publish_date = isset($job->publish_date) ? $job->publish_date : '';
                        $expire_date = isset($job->expire_date) ? $job->expire_date : '';
                        $company_name = isset($job->company_name) ? $job->company_name : '';
                        $import_source = isset($job->import_source) ? $job->import_source : '';


                        //$publish_date = strtotime($publish_date);

                        //echo '<pre>'.var_export($publish_date, true).'</pre>';


                        ?>
                        <li class="job">
                            <div class="job-title"><a href="<?php echo $url.'?pkey='.$api_key; ?>"><?php echo $title; ?></a></div>
                            <div class="job-meta">
                                <?php if(!empty($publish_date)): ?>
                                    <div class="meta-item"><span class="meta-title">Published:</span> <span class="meta-value"><?php echo $publish_date; ?></span></div>
                                <?php endif; ?>

                                <?php if(!empty($expire_date)): ?>
                                    <div class="meta-item"><span class="meta-title">Expire date:</span> <span class="meta-value"><?php echo $expire_date; ?></span></div>
                                <?php endif; ?>

                                <?php if(!empty($company_name)): ?>
                                    <div class="meta-item"><span class="meta-title">Company:</span> <span class="meta-value"><?php echo $company_name; ?></span></div>
                                <?php endif; ?>

                                <?php if(!empty($import_source)): ?>
                                    <div class="meta-item"><span class="meta-title">Source:</span> <span class="meta-value"><?php echo $import_source; ?></span></div>
                                <?php endif; ?>

                            </div>
                        </li>
                        <?php
                    }
                else:
                    ?>
                    <li class="job">
                        <div class="job-title">Sorry! there is a server error, please try again.</div>
                    </li>
                <?php
                endif;




                ?>
            </ul>


            <?php if(!empty($found_posts)): ?>
            <div class="pagination">
                <?php



                $big = 999999999;
                $total = $found_posts;
                $num_of_pages = ceil( $found_posts / $per_page );
                $page_links = paginate_links( array(
                    //'base' => add_query_arg( 'pagenum', '%#%' ),
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '?paged=%#%',
                    'prev_text' => __( '&laquo;', 'aag' ),
                    'next_text' => __( '&raquo;', 'aag' ),
                    'total' => $num_of_pages,
                    'current' => $paged
                ) );

                echo $page_links;
                ?>
            </div>
        <?php endif; ?>


        <?php

        endif; ?>









    </div>




    <style type="text/css">
        .bekarcombd-jobs{}
        .bekarcombd-jobs form{
            margin-bottom: 20px;
        }
        .bekarcombd-jobs form input{
            width: 30%;
            margin: 10px 7px;
        }

        .bekarcombd-jobs .job-list{}
        .bekarcombd-jobs .job-list .job{
            list-style: none;
            margin: 10px 0;
            padding: 10px 0;
            border-bottom: 1px solid #dddddd78;
        }
        .bekarcombd-jobs .job-title{}
        .bekarcombd-jobs .job-title a{
            font-size: 16px;
        }

        .bekarcombd-jobs .job-meta{

        }

        .bekarcombd-jobs .job-meta .meta-item{
            font-size: 12px;
            display: inline-block;
            margin: 0 10px 0 0;
            padding: 0 10px 0 0;
        }
        .bekarcombd-jobs .job-meta .meta-title{

        }
        .bekarcombd-jobs .job-meta .meta-value{
            font-weight: bold;
        }


        .bekarcombd-jobs .pagination{}
        .bekarcombd-jobs .pagination .page-numbers{
            padding: 5px 15px;
            background: #ddd;
            margin: 0 4px;
        }
        .bekarcombd-jobs .pagination .page-numbers.current{
            background: #b5b5b5;
        }

        .bekarcombd-jobs .error-message{
            color: #f00;
        }

    </style>


    <?php

    return ob_get_clean();

}



add_shortcode( 'bekarcombd_job_post', 'bekarcombd_job_post_display' );

function bekarcombd_job_post_display($atts, $content = null ){

    $atts = shortcode_atts(
        array(
            'api_key' => '',
        ),
        $atts);

    $bekar_jobs_api_key = get_option('bekar_jobs_api_key');
    $api_key = $bekar_jobs_api_key;

    $api_params = array();

    $api_params['api_key'] = $api_key;

    $meta_query[] = array(
        'key' => 'server_job_id',
        'compare' => 'NOT EXISTS',
    );



    $query_args = array (
        'post_type' => 'job',
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => $meta_query,
        'posts_per_page' => 1,
    );

    $wp_query = new WP_Query($query_args);
    if ( $wp_query->have_posts() ) :
        $count = 1;

        while( $wp_query->have_posts() ) : $wp_query->the_post();

            $client_job_id = get_the_ID();

            $api_params['client_job_id'] =  $client_job_id;
            $api_params['post_title'] = get_the_title();
            $api_params['post_content'] = get_the_content();
            $api_params['job_category'] = '';

            $api_params['job_bm_total_vacancies'] = get_post_meta($client_job_id, 'job_bm_total_vacancies', true);
            $api_params['job_bm_job_type'] =  get_post_meta($client_job_id, 'job_bm_job_type', true);
            $api_params['job_bm_job_level'] =  get_post_meta($client_job_id, 'job_bm_job_level', true);
            $api_params['job_bm_years_experience'] =  get_post_meta($client_job_id, 'job_bm_years_experience', true);
            $api_params['job_bm_salary_type'] =  get_post_meta($client_job_id, 'job_bm_salary_type', true);
            $api_params['job_bm_salary_fixed'] =  get_post_meta($client_job_id, 'job_bm_salary_fixed', true);
            $api_params['job_bm_salary_min'] =  get_post_meta($client_job_id, 'job_bm_salary_min', true);
            $api_params['job_bm_salary_max'] =  get_post_meta($client_job_id, 'job_bm_salary_max', true);
            $api_params['job_bm_salary_duration'] =  get_post_meta($client_job_id, 'job_bm_salary_duration', true);
            $api_params['job_bm_salary_currency'] =  get_post_meta($client_job_id, 'job_bm_total_vacancies', true);
            $api_params['job_bm_contact_email'] =  get_post_meta($client_job_id, 'job_bm_contact_email', true);
            $api_params['job_bm_company_name'] =  get_post_meta($client_job_id, 'job_bm_company_name', true);
            $api_params['job_bm_location'] =  get_post_meta($client_job_id, 'job_bm_location', true);
            $api_params['job_bm_address'] =  get_post_meta($client_job_id, 'job_bm_address', true);
            $api_params['job_bm_company_website'] =  get_post_meta($client_job_id, 'job_bm_company_website', true);
            $api_params['job_bm_company_logo'] =  get_post_meta($client_job_id, 'job_bm_company_logo', true);


            $response = wp_remote_get(add_query_arg($api_params, bekar_jobs_api_url), array('timeout' => 20, 'sslverify' => false));

            //$response = wp_remote_get( $api_url );
            $body = wp_remote_retrieve_body( $response );
            $response_data =  json_decode($body);

            $server_job_id = isset($response_data->server_job_id) ? $response_data->server_job_id : 'no jo created';
            $server_error = isset($response_data->error) ? $response_data->error : '';



            if(!$server_error && !empty($server_job_id)){

                echo '<pre>'.var_export(get_the_title(), true).'</pre>';
                echo '<pre>'.var_export($server_job_id, true).'</pre>';

                update_post_meta($client_job_id, 'server_job_id', $server_job_id);
            }else{
                echo '<pre>'.var_export('There is an error', true).'</pre>';
            }



        endwhile;

        wp_reset_query();
    else:


    endif;



}






function bekarcombd_sync_job_by_id($client_job_id ){

    global $post;

    $response = array();
    $bekar_jobs_api_key = get_option('bekar_jobs_api_key');
    $api_key = $bekar_jobs_api_key;

    $api_params = array();

    $api_params['api_key'] = $api_key;
    $api_params['action'] = 'job_submit';


    $api_params['client_job_id'] =  $client_job_id;
    $api_params['post_title'] = get_the_title($client_job_id);
    $api_params['post_content'] = $post->post_content;
    $api_params['job_bm_total_vacancies'] = get_post_meta($client_job_id, 'job_bm_total_vacancies', true);
    $api_params['job_bm_job_type'] =  get_post_meta($client_job_id, 'job_bm_job_type', true);
    $api_params['job_bm_job_level'] =  get_post_meta($client_job_id, 'job_bm_job_level', true);
    $api_params['job_bm_years_experience'] =  get_post_meta($client_job_id, 'job_bm_years_experience', true);
    $api_params['job_bm_salary_type'] =  get_post_meta($client_job_id, 'job_bm_salary_type', true);
    $api_params['job_bm_salary_fixed'] =  get_post_meta($client_job_id, 'job_bm_salary_fixed', true);
    $api_params['job_bm_salary_min'] =  get_post_meta($client_job_id, 'job_bm_salary_min', true);
    $api_params['job_bm_salary_max'] =  get_post_meta($client_job_id, 'job_bm_salary_max', true);
    $api_params['job_bm_salary_duration'] =  get_post_meta($client_job_id, 'job_bm_salary_duration', true);
    $api_params['job_bm_salary_currency'] =  get_post_meta($client_job_id, 'job_bm_total_vacancies', true);
    $api_params['job_bm_contact_email'] =  get_post_meta($client_job_id, 'job_bm_contact_email', true);
    $api_params['job_bm_company_name'] =  get_post_meta($client_job_id, 'job_bm_company_name', true);
    $api_params['job_bm_location'] =  get_post_meta($client_job_id, 'job_bm_location', true);
    $api_params['job_bm_address'] =  get_post_meta($client_job_id, 'job_bm_address', true);
    $api_params['job_bm_company_website'] =  get_post_meta($client_job_id, 'job_bm_company_website', true);
    $api_params['job_bm_company_logo'] =  get_post_meta($client_job_id, 'job_bm_company_logo', true);


    $server_response = wp_remote_get(add_query_arg($api_params, bekar_jobs_api_url), array('timeout' => 20, 'sslverify' => false));
    $body = wp_remote_retrieve_body( $server_response );
    $server_response_data =  json_decode($body);

    $server_job_id = isset($server_response_data->server_job_id) ? $server_response_data->server_job_id : '';
    $server_job_url = isset($server_response_data->server_job_url) ? $server_response_data->server_job_url : '';

    $error_messages = isset($server_response_data->error_messages) ? $server_response_data->error_messages : '';

    echo '<pre>'.var_export($server_response_data, true).'</pre>';

    if(!empty($error_messages)){
        $response['error_messages'] = $error_messages;
        $response['status'] = 'failed';
    }else{
        if(!empty($server_job_id)){

            update_post_meta($client_job_id, 'server_job_id', $server_job_id);
            update_post_meta($client_job_id, 'server_job_url', $server_job_url);

            $response['status'] = 'success';

        }



    }


    return $response;


}







