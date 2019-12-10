<?php	
if ( ! defined('ABSPATH')) exit;  // if direct access


$bekar_jobs_tab = array();

$bekar_jobs_tab[] = array(
    'id' => 'general',
    'title' => sprintf(__('%s General','bekar-com-bd-jobs'),'<i class="fas fa-list-ul"></i>'),
    'priority' => 1,
    'active' => false,
);

$bekar_jobs_tab[] = array(
    'id' => 'help',
    'title' => sprintf(__('%s Help','bekar-com-bd-jobs'),'<i class="fas fa-list-ul"></i>'),
    'priority' => 2,
    'active' => true,
);



$bekar_jobs_tab = apply_filters('bekar_jobs_tabs', $bekar_jobs_tab);


wp_enqueue_script('settings-tabs');
wp_enqueue_style('settings-tabs');


$tabs_sorted = array();
foreach ($bekar_jobs_tab as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
array_multisort($tabs_sorted, SORT_ASC, $bekar_jobs_tab);
	
?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div><h2><?php echo sprintf(__('%s Settings', 'bekar-com-bd-jobs'), bekar_jobs_plugin_name)?></h2>
		<form  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	        <input type="hidden" name="bekar_jobs_hidden" value="Y">
            <?php
            if(!empty($_POST['bekar_jobs_hidden'])){

                $nonce = sanitize_text_field($_POST['_wpnonce']);

                if(wp_verify_nonce( $nonce, 'bekar_jobs_nonce' ) && $_POST['bekar_jobs_hidden'] == 'Y') {


                    do_action('bekar_jobs_settings_save');

                    ?>
                    <div class="updated notice  is-dismissible"><p><strong><?php _e('Changes Saved.', 'bekar-com-bd-jobs' ); ?></strong></p></div>

                    <?php
                }
            }
            ?>
            <div class="settings-tabs vertical has-right-panel">

                <div class="settings-tabs-right-panel">
                    <?php
                    foreach ($bekar_jobs_tab as $tab) {
                        $id = $tab['id'];
                        $active = $tab['active'];

                        ?>
                        <div class="right-panel-content <?php if($active) echo 'active';?> right-panel-content-<?php echo $id; ?>">
                            <?php

                            do_action('bekar_jobs_tabs_right_panel_'.$id);
                            ?>

                        </div>
                        <?php

                    }
                    ?>
                </div>

                <ul class="tab-navs">
                    <?php
                    foreach ($bekar_jobs_tab as $tab){
                        $id = $tab['id'];
                        $title = $tab['title'];
                        $active = $tab['active'];
                        $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                        $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                        ?>
                        <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></li>
                        <?php
                    }
                    ?>
                </ul>



                <?php
                foreach ($bekar_jobs_tab as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    ?>

                    <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                        <?php
                        do_action('bekar_jobs_tabs_content_'.$id, $tab);
                        ?>


                    </div>

                    <?php
                }
                ?>

            </div>

            <div class="clear clearfix"></div>
            <p class="submit">
                <?php wp_nonce_field( 'bekar_jobs_nonce' ); ?>
                <input class="button button-primary" type="submit" name="Submit" value="<?php _e('Save Changes','bekar-com-bd-jobs' ); ?>" />
            </p>
		</form>
</div>
