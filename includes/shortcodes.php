<?php
if ( ! defined('ABSPATH')) exit;  // if direct access 


add_shortcode( 'bekarcombd_jobs_search', 'bekarcombd_jobs_search_display' );

function bekarcombd_jobs_search_display($atts, $content = null ) {

    $atts = shortcode_atts(
        array(
            'api_url' => 'https://bekar.com.bd/job-search-api/?per_page=5',

        ),
        $atts);

    $bekar_keyword = '';
    $bekar_location = '';
    if ( get_query_var('paged') ) {$paged = get_query_var('paged');}
    elseif ( get_query_var('page') ) {$paged = get_query_var('page');}
    else {$paged = 1;}

    $per_page = isset($_GET['per_page']) ? sanitize_text_field($_GET['per_page']) : 10;

    //echo '<pre>'.var_export($paged, true).'</pre>';


    if(! isset( $_POST['bekarcombd_nonce'] ) || ! wp_verify_nonce( $_POST['bekarcombd_nonce'], 'bekarcombd_nonce' ) ){

        $bekar_keyword = isset($_GET['bekar_keyword']) ? sanitize_text_field($_GET['bekar_keyword']) : '';
        $bekar_location = isset($_GET['bekar_location']) ? sanitize_text_field($_GET['bekar_location']) : '';

        $api_url = 'https://bekar.com.bd/job-search-api/?per_page='.$per_page.'&paged='.$paged.'&keywords='.urlencode($bekar_keyword);

    }else{
        $api_url = 'https://bekar.com.bd/job-search-api/?per_page=10';
    }




    $response = wp_remote_get( $api_url );
    $body = wp_remote_retrieve_body( $response );
    $response_data =  json_decode($body);

    $jobs = isset($response_data->jobs) ? $response_data->jobs : array();
    $found_posts = isset($response_data->found_posts) ? $response_data->found_posts : '';

    //echo '<pre>'.var_export($_SERVER, true).'</pre>';



    ?>

    <div class="bekarcombd-jobs">

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
                $title = $job->title;
                $url = $job->url;
                $publish_date = $job->publish_date;
                $expire_date = $job->expire_date;
                $company_name = $job->company_name;
                $import_source = $job->import_source;


                $publish_date = strtotime($publish_date);

                //echo '<pre>'.var_export($response_data, true).'</pre>';


                ?>
                <li class="job">
                    <div class="job-title"><a href="<?php echo $url; ?>"><?php echo $title; ?></a></div>
                    <div class="job-meta">
                        <?php if(!empty($publish_date)): ?>
                        <span>Published: <?php echo esc_html( human_time_diff( $publish_date, current_time('timestamp') ) ) . ' ago'; ?></span>
                        <?php endif; ?>

                        <?php if(!empty($expire_date)): ?>
                        <span>Expire date: <?php echo $expire_date; ?></span>
                        <?php endif; ?>

                        <?php if(!empty($company_name)): ?>
                        <span>Company: <?php echo $company_name; ?></span>
                        <?php endif; ?>

                        <?php if(!empty($import_source)): ?>
                            <span>Source: <?php echo $import_source; ?></span>
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

        <div class="pagination">
            <?php

            $big = $found_posts; // need an unlikely integer
            echo paginate_links( array(
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, $paged ),
                'total' => $per_page
            ) );

            ?>
        </div>


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

        .bekarcombd-jobs .job-meta span{
            font-size: 12px;
            display: inline-block;
            margin: 0 10px 0 0;
            padding: 0 10px 0 0;
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



    </style>


    <?php
}



