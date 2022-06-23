// custom login styles
<?php // utc-department-directory - Core Functionality

function utcdepartmentdirectory_enqueue_style()
{
    var_dump(plugin_dir_url(dirname(__FILE__)));
    // wp_enqueue_style('utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'utcdepartmentdirectory/dist/output.css', false);
}
 
function utcdepartmentdirectory_enqueue_script()
{
    // wp_enqueue_script('utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'utcdepartmentdirectory/dist/utcdepartmentdirectory.js', false);
}
 
// add_action('wp_enqueue_scripts', 'utcdepartmentdirectory_enqueue_style');
// add_action('wp_enqueue_scripts', 'utcdepartmentdirectory_enqueue_script');