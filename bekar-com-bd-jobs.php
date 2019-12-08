<?php
/*
Plugin Name: bekar com bd Jobs
Plugin URI: http://pickplugins.com
Description: Display jobs from bekar.com.bd for bangladeshi local job news.
Version: 1.0.0
Author: pickplugins
Author URI: http://pickplugins.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class bekarcombdJobs{
	
	public function __construct(){
	
        define('bekar_jobs_plugin_url', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
        define('bekar_jobs_plugin_dir', plugin_dir_path( __FILE__ ) );
        define('bekar_jobs_plugin_name', 'Job Board Manager - API Clients'  );
        define('bekar_jobs_plugin_version', '1.0.0' );

        // Class

        require_once( bekar_jobs_plugin_dir . 'includes/shortcodes.php');
        require_once( bekar_jobs_plugin_dir . 'includes/class-widget-latest-job.php');


        register_activation_hook( __FILE__, array( $this, '_activation' ) );
        register_deactivation_hook(__FILE__, array( $this, '_deactivation' ));
        add_action( 'widgets_init', array( $this, 'load_widget' ) );
	}



    function load_widget(){
        register_widget( 'WidgetLatestJob' );

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


        do_action( 'bekar_jobs_admin_scripts' );
	}
	
	
	
	

}

new bekarcombdJobs();