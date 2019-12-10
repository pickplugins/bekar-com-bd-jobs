<?php



if ( ! defined('ABSPATH')) exit;  // if direct access 


class bekar_jobs_settings{
	

    public function __construct(){

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
    }
	

	
	
	public function admin_menu() {


        add_menu_page(__('BD Local Jobs', 'breadcrumb'), __('BD Local Jobs', 'breadcrumb'), 'manage_options', 'bekar_jobs_settings', array( $this, '_settings' ), 'dashicons-store');

		do_action( 'job_bm_action_admin_menus' );
		
	}


	public function _settings(){
		
		include( 'menu/settings.php' );
		}





}


new bekar_jobs_settings();

