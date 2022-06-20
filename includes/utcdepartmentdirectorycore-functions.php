// custom login styles
<?php // utcdepartmentdirectory - Core Functionality


function themeslug_enqueue_style() {
    wp_enqueue_style( 'utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'public/js/utcdepartmentdirectory.css', false );
}
 
function themeslug_enqueue_script() {
    wp_enqueue_script( 'utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'public/js/utcdepartmentdirectory.js', false );
}
 
add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_script' );