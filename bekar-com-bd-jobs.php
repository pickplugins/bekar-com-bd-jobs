<?php
/*
Plugin Name: BD Local Jobs
Plugin URI: https://bekar.com.bd/
Description: Display jobs from bekar.com.bd for bangladeshi local job news.
Version: 1.0.3
Author: pickplugins
Author URI: http://pickplugins.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class bekarcombdJobs{
	
	public function __construct(){

        define('bekar_jobs_plugin_url', plugins_url('/', __FILE__)  );
        define('bekar_jobs_plugin_dir', plugin_dir_path( __FILE__ ) );
        define('bekar_jobs_plugin_name', 'BD Local Jobs'  );
        define('bekar_jobs_plugin_version', '1.0.3' );
        define('bekar_jobs_api_url', 'https://bekar.com.bd/job-api/' );

        // Class
        require_once( bekar_jobs_plugin_dir . 'includes/class-settings.php');
        require_once( bekar_jobs_plugin_dir . 'includes/class-settings-tabs.php');

        require_once( bekar_jobs_plugin_dir . 'includes/shortcodes.php');
        require_once( bekar_jobs_plugin_dir . 'includes/class-widget-latest-job.php');

        require_once( bekar_jobs_plugin_dir . 'includes/functions-job-metabox.php');
        require_once( bekar_jobs_plugin_dir . 'includes/functions-settings.php');



        add_action( 'wp_enqueue_scripts', array( $this, '_front_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );

        register_activation_hook( __FILE__, array( $this, '_activation' ) );
        register_deactivation_hook(__FILE__, array( $this, '_deactivation' ));
        add_action( 'widgets_init', array( $this, 'load_widget' ) );
	}



    function load_widget(){
        register_widget( 'bekarcombdLatestJob' );

    }

	public function _uninstall(){
		
		do_action( 'bekar_jobs_uninstall' );

	}

    public function _activation(){

        do_action( 'bekar_jobs_activation' );
    }

	public function _deactivation(){

        do_action( 'bekar_jobs_deactivation' );

	}
		
	public function _front_scripts(){

        do_action( 'bekar_jobs_front_scripts' );
	}

	public function _admin_scripts(){

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-sortable');

        wp_register_script('settings-tabs', bekar_jobs_plugin_url.'assets/admin/js/settings-tabs.js' , array( 'jquery' ));
        wp_register_style('settings-tabs', bekar_jobs_plugin_url.'assets/admin/css/settings-tabs.css');

        do_action( 'bekar_jobs_admin_scripts' );
	}
	
	
	
	

}

new bekarcombdJobs();