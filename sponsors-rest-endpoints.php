<?php

/*
Plugin Name: Sponsors REST Endpoints
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: mmarcec007
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

add_action( 'rest_api_init', function () {
    require plugin_dir_path( __FILE__ ) . 'includes/SponsorsController.php';
    $controller = new SponsorsController();
    $controller->register_routes();
} );