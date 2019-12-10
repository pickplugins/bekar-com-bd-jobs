<?php


if ( ! defined('ABSPATH')) exit;  // if direct access 
	

add_action('bekar_jobs_tabs_content_general', 'bekar_jobs_tabs_content_general');

if(!function_exists('bekar_jobs_tabs_content_general')) {
    function bekar_jobs_tabs_content_general($tab){

        $settings_tabs_field = new settings_tabs_field();

        $bekar_jobs_api_key= get_option('bekar_jobs_api_key');


        ?>
        <div class="section">
            <div class="section-title"><?php echo __('General settings', 'bekar-com-bd-jobs'); ?></div>
            <p class="description section-description"><?php echo __('Choose soma back general options.', 'bekar-com-bd-jobs'); ?></p>

            <?php

            $args = array(
                'id'		=> 'bekar_jobs_api_key',
                //'parent'		=> '',
                'title'		=> __('API key','bekar-com-bd-jobs'),
                'details'	=> sprintf(__('Write API key here, you can find API key from here %s','bekar-com-bd-jobs'), '<a href="https://bekar.com.bd/job-dashboard/?tabs=apikey">https://bekar.com.bd/job-dashboard/?tabs=apikey</a>'),
                'type'		=> 'text',
                'value'		=> $bekar_jobs_api_key,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);




            ?>


        </div>
    <?php


    }
}




add_action('bekar_jobs_tabs_content_help', 'bekar_jobs_tabs_content_help');

if(!function_exists('bekar_jobs_tabs_content_help')) {
    function bekar_jobs_tabs_content_help($tab){

        $settings_tabs_field = new settings_tabs_field();

        $bekar_jobs_api_key= get_option('bekar_jobs_api_key');


        ?>
        <div class="section">
            <div class="section-title"><?php echo __('Help & Support', 'bekar-com-bd-jobs'); ?></div>
            <p class="description section-description"><?php echo __('Get help and support from our expert team.', 'bekar-com-bd-jobs'); ?></p>


            <div class="setting-field">
                <h3>#Step - 1</h3>
                <p>Go and register an account to generate API key</p>
                <p>API key will be used to track visitors click from your site.  </p>
                <a class="button" target="_blank" href="https://bekar.com.bd/job-dashboard/">https://bekar.com.bd</a>
                <p>Click on <a href="https://bekar.com.bd/job-dashboard/?tabs=apikey">API Key</a> tab,it will generate an API key automatically. see the screenshot</p>
                <img src="https://i.imgur.com/zDGPXpw.png">

                <h3>#Step - 2</h3>
                <p>Copy the API key and paste on <b>API key</b> option field on your site under <b>bekar.com.bd Jobs Settings</b> page</p>
                <img width="700" src="https://i.imgur.com/oJAiF26.png">

                <h4>You are almost done!</h4>
                <h3>#Step - 3</h3>
                <p>Use following shortcode anywher under page content or post content to display job search form.</p>
                <p><code>[bekarcombd_jobs_search]</code></p>
                <img width="700" src="https://i.imgur.com/M6zJxM9.png">

                <h3>Display on sidebar</h3>
                <p>You can display latest job list on sidebar, please go widgets page and see there is a widget <b>bekar.com.bd - Latest Job</b></p>
                <img width="700" src="https://i.imgur.com/YnYXXfz.png">


                <h3>View click track</h3>
                <p>You can see the click stats our website, go to <a href="https://bekar.com.bd/job-dashboard/?tabs=apikey">API Key</a> page and click on API ID to go api details page. </p>
                <img width="700" src="https://i.imgur.com/RVVUL0W.png">
                <p>On API details page you will see the click stats.</p>
                <img width="700" src="https://i.imgur.com/f1G9MQp.png">


            </div>




        </div>
        <?php


    }
}


























add_action('bekar_jobs_settings_save', 'bekar_jobs_settings_save');

if(!function_exists('bekar_jobs_settings_save')) {
    function bekar_jobs_settings_save(){

        $bekar_jobs_api_key = isset($_POST['bekar_jobs_api_key']) ?  sanitize_text_field($_POST['bekar_jobs_api_key']) : '';
        update_option('bekar_jobs_api_key', $bekar_jobs_api_key);
    }
}