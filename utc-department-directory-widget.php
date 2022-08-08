<?php
/*
Plugin Name: UTC Department Directory Widget
Description: Queries a JSON endpoint for its department information
Plugin URI:  https://github.com/UTCWeb/departmentdirectory
Author:      Bernardo Martinez
Version:     1.3
*/


// disable direct file access
if (! defined('ABSPATH')) {
    exit;
}
/**
 * Adds twig integration and needed files
 *
 */
// auto load not needed in main plugin file?
// require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// example: GET request
function http_get_request($url)
{
    $url = esc_url_raw($url);

    $args = array( 'user-agent' => 'UTC Department info Plugin: HTTP API; '. home_url() );

    return wp_safe_remote_get($url, $args);
}
/**
 * Admin function, gets all UTC departments(Organizatioanl sections)
 *
 */
function admin_http_get_response()
{
    $url = 'https://www.utc.edu/json/departmentdirectory';

    $response = http_get_request($url);

    // response data
    $body    = wp_remote_retrieve_body($response);
    /* Will result in $api_response being an array of data,
    parsed from the JSON response of the API listed above */
    $api_response = json_decode($body, true);
    $organization_array = array();
    $organization_output= [];
    if (count($api_response) == 0) {
        $output .= '<div> The plugin can not fetch the data at the moment. Make sure https://www.utc.edu/json/departmentdirectory is reachable </div>';
        $organizational_output[0] = $output;
        $organizational_output[1] = null;

        return $organizational_output;
    }
    $output .= '<div>'. count($api_response) .' organizational sections are listed</div>';
    
    foreach ($api_response as $index => $organization) {
        $organization_array[$index]['name'] = $api_response[$index]['info'][0]['value'];
        $organization_array[$index]['OrganizationalSectionID'] = $api_response[$index]['field_utc_organizational_section'][0]['target_id'];
    }
    ob_start();
    $output .= ob_get_clean();
    $organizational_output[0] = $output;
    $organizational_output[1] = $organization_array;
    return $organizational_output;
}

/**
 * Public function, gets one UTC department(Organizatioanl sections)
 *
 */
// TODO Refactor this class and the admin one.
function public_http_get_response($organizationalSectionID)
{
    $url = 'https://www.utc.edu/json/departmentdirectory'. '/' . $organizationalSectionID ;

    $response = http_get_request($url);

    // response data
    $body    = wp_remote_retrieve_body($response);
    
    /* Will result in $api_response being an array of data,
    parsed from the JSON response of the API listed above */
    $api_response = json_decode($body, true);

    // Adds a twig template and pipes the $api_response variables to it
    $loader = new FilesystemLoader(__DIR__ . '/src/templates');
    $twig = new Environment($loader);
    echo $twig->render(
        'departmentinfo.html.twig',
        [
        'name' => isset($api_response[0]['info'][0]['value'])? $api_response[0]['info'][0]['value']: "empty",
        'taxonomyid' => isset($api_response[0]['field_utc_organizational_section'][0]['target_id'])? $api_response[0]['field_utc_organizational_section'][0]['target_id']: "empty",
        'mailcode' => isset($api_response[0]['field_utc_department_mail_code'][0]['value'])? $api_response[0]['field_utc_department_mail_code'][0]['value']: "empty",
        'phonenumber' => isset($api_response[0]['field_utc_department_phone'][0]['value'])? $api_response[0]['field_utc_department_phone'][0]['value']: "empty",
        'faxnumber' => isset($api_response[0]['field_utc_department_fax_number'][0]['value'])? $api_response[0]['field_utc_department_fax_number'][0]['value']: "empty",
        'email' => isset($api_response[0]['field_utc_department_email'][0]['value'])? $api_response[0]['field_utc_department_email'][0]['value']: "empty",
        'buildingtitle' => isset($api_response[0]['field_utc_department_building'][0]['title'])? $api_response[0]['field_utc_department_building'][0]['title']: "empty",
        'buildinguri' => isset($api_response[0]['field_utc_department_building'][0]['uri'])? $api_response[0]['field_utc_department_building'][0]['uri']: "empty",
        'address' => isset($api_response[0]['field_utc_department_street_addr'][0]['title'])? $api_response[0]['field_utc_department_street_addr'][0]['title']: "empty",
        'facebook' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['facebook']['value'])?$api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['facebook']['value']: "empty",
        'instagram' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['instagram']['value'])? $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['instagram']['value']: "empty",
        'twitter' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['twitter']['value'])? $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['twitter']['value']: "empty",
        'youtube' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['youtube']['value'])? $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['youtube']['value']: "empty",
        'linkedin' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['linkedin']['value'])? $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['linkedin']['value']: "empty",
        'homepage' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['departamental_homepage']['value'])? $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['departamental_homepage']['value']: "empty",
        'vimeo' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['vimeo']['value'])?$api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['vimeo']['value']: "empty",
        'podcast' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['podcast']['value'])?$api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['podcast']['value']: "empty",
        'blog' => !empty($api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['blog']['value'])?$api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['blog']['value']: "empty",
        'officehours' => !empty($api_response[0]['field_utc_department_hours'])? $api_response[0]['field_utc_department_hours']: "empty"
        ]
    );
    return;
}

/**
 * Extendeds the Wordpress widget class
 *
 */
class UTCDepartment_Directory_Widget extends WP_Widget
{
    public function __construct()
    {
        $id = 'utcdepartment_directory_widget';

        $title = esc_html__('UTC Department Directory Widget', 'custom-widget');

        $options = array(
            'classname' => 'utcdepartment_directory_widget',
            'description' => esc_html__('Adds a Department\'s contact information that is fed by a UTC.edu Drupal JSON API', 'custom-widget')
        );

        parent::__construct($id, $title, $options);
    }

    public function widget($args, $instance)
    {
        $organizationalsection = '';
        if (isset($instance['organizationalsection'])) {
            echo public_http_get_response($instance['organizationalsection']);
            // echo wp_kses_post( $instance['organizationalsection'] );
        }
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();

        if (isset($new_instance['organizationalsection']) && ! empty($new_instance['organizationalsection'])) {
            $instance['organizationalsection'] = $new_instance['organizationalsection'];
        }

        return $instance;
    }

    public function form($instance)
    {
        $id = $this->get_field_id('organizationalsection');

        $for = $this->get_field_id('organizationalsection');

        $name = $this->get_field_name('organizationalsection');

        $label = __('UTC Organizational Sections:', 'custom-widget');

        $organizationalsection = '<p>'. __('Contact information for any UTC organizational sections.', 'custom-widget') .'</p>';
        
        $data = [];
        $data = admin_http_get_response();
        echo $data[0];

        if (isset($instance['organizationalsection']) && ! empty($instance['organizationalsection'])) {
            $organizationalsection = $instance['organizationalsection'];
        }
        $valuesselected = []; ?>

        <p>
            <label for="<?php echo esc_attr($for); ?>"><?php echo esc_html($label); ?></label>
            <select class="widefat" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>">
            <?php foreach ($data[1] as $index=>$row) { ?>
            <option value="<?php echo $row["OrganizationalSectionID"] ?>"<?php selected($valuesselected["name"], $row["OrganizationalSectionID"]); ?>><?php echo $row["name"] ?></option> <?php
            } ?>
            </select>
            <?php $organizationalsection = $valuesselected[0]; ?>
            <label for="<?php echo esc_attr($for); ?>"><?php echo $organizationalsection; ?></label>

        </p>

        <?php
    }
}

// register widget
function utcdepartmentdirectory_register_widgets()
{
    register_widget('UTCDepartment_Directory_Widget');
}
add_action('widgets_init', 'utcdepartmentdirectory_register_widgets');

// TODO Add the following functiuons in a separate file
// include plugin dependencies: admin and public
// require_once plugin_dir_url( __FILE__ ) . 'includes/utcdepartmentdirectorycore-functions.php';

/**
 * Adds javascript and CSS to the plugin.
 *
 */
// require get_template_directory() . 'includes/utcdepartmentdirectorycore-functions.php';

function utcdepartmentdirectory_enqueue_style()
{
    wp_enqueue_style('utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'utc-department-directory/dist/output.css', false);
}
 
function utcdepartmentdirectory_enqueue_script()
{
    wp_enqueue_script('utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'utc-department-directory/dist/utcdepartmentdirectory.js', false);
}
 
add_action('wp_enqueue_scripts', 'utcdepartmentdirectory_enqueue_style');
add_action('wp_enqueue_scripts', 'utcdepartmentdirectory_enqueue_script');
