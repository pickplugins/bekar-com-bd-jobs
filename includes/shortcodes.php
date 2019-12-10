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

        $api_url = $api_url.'?api_key='.$api_key.'&per_page='.$per_page.'&paged='.$paged.'&keywords='.urlencode($bekar_keyword);

    }else{
        $api_url = $api_url.'?per_page=10&paged='.$paged.'&api_key='.$api_key.'';
    }


    //echo '<pre>'.var_export($api_url, true).'</pre>';

    $response = wp_remote_get( $api_url );
    $body = wp_remote_retrieve_body( $response );
    $response_data =  json_decode($body);

    $error = isset($response_data->error) ? $response_data->error : false;
    $error_type = isset($response_data->error_type) ? $response_data->error_type : '';
    $error_message = isset($response_data->error_message) ? $response_data->error_message : '';
    $get_api_url = isset($response_data->get_api_url) ? $response_data->get_api_url : '';



    $jobs = isset($response_data->jobs) ? $response_data->jobs : array();
    $found_posts = isset($response_data->found_posts) ? $response_data->found_posts : 0;

    //echo '<pre>'.var_export($found_posts, true).'</pre>';


    ob_start();
    ?>

    <div class="bekarcombd-jobs">

    <?php if($error): ?>
        <div class="error">
            <p class="error-message"><?php echo $error_message; ?></p>
        </div>
    <?php endif; ?>



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



