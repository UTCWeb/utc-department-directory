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


require __DIR__ . '/vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// example: GET request
function http_get_request($url)
{
    $url = esc_url_raw($url);

    $args = array( 'user-agent' => 'UTC Department info Plugin: HTTP API; '. home_url() );

    return wp_safe_remote_get($url, $args);
}

// example: GET response
function http_get_response()
{
    $url = 'https://www.utc.edu/json/departmentdirectory';

    $response = http_get_request($url);

    // response data

    $code    = wp_remote_retrieve_response_code($response);
    $message = wp_remote_retrieve_response_message($response);
    $body    = wp_remote_retrieve_body($response);
    $headers = wp_remote_retrieve_headers($response);

    $header_date  = wp_remote_retrieve_header($response, 'date');
    $header_type  = wp_remote_retrieve_header($response, 'content-type');
    $header_cache = wp_remote_retrieve_header($response, 'cache-control');

    /* Will result in $api_response being an array of data,
    parsed from the JSON response of the API listed above */
    $api_response = json_decode($body, true);
    // var_dump( $api_response[0]['info'][0]['value'] );
    $output .= '</pre>';
    // $output .= '<div>Organizational Section name ' . $api_response[0]['info'][0]['value']  .'</div>';
    // $output .= '<div>Organizational Section ID '  . $api_response[0]['field_utc_organizational_section'][0]['target_id']  .'</div>';
    // $output .= '<div>Organizational Section mail code ' . $api_response[0]['field_utc_department_mail_code'][0]['value'] .'</div>';
    $output .= '<div>This list includes ' . count($api_response) .' organizational sections</div>';
    $organization_array = array();
    $organization_output= [];
    foreach ($api_response as $index => $organization) {
        // organization_array = array($api_response[$index]['info'][0]['value'],;
        $organization_array[$index]['name'] = $api_response[$index]['info'][0]['value'];
        $organization_array[$index]['OrganizationalSectionID'] = $api_response[$index]['field_utc_organizational_section'][0]['target_id'];
    }
    $output .= '<pre>';

    ob_start();
    // var_dump( $headers );
    $output .= ob_get_clean();
    $output .= '</pre>';
    $organizational_output[0] = $output;
    $organizational_output[1] = $organization_array;
    return $organizational_output;
}

// example: GET response
function public_http_get_response($organizationalSectionID)
{
    $url = 'https://www.utc.edu/json/departmentdirectory'. '/' . $organizationalSectionID ;

    $response = http_get_request($url);

    // response data
    $body    = wp_remote_retrieve_body($response);
    
    /* Will result in $api_response being an array of data,
    parsed from the JSON response of the API listed above */
    $api_response = json_decode($body, true);

    $loader = new FilesystemLoader(__DIR__ . '/src/templates');
    $twig = new Environment($loader);
    echo $twig->render(
        'departmentinfo.html.twig',
        [
        'name' => $api_response[0]['info'][0]['value'],
        'taxonomyid' => $api_response[0]['field_utc_organizational_section'][0]['target_id'],
        'mailcode' => $api_response[0]['field_utc_department_mail_code'][0]['value'],
        'phonenumber' => $api_response[0]['field_utc_department_phone'][0]['value'],
        'phonenumber' => $api_response[0]['field_utc_department_fax_number'][0]['value'],
        'buildingtitle' => $api_response[0]['field_utc_department_building'][0]['title'],
        'buildinguri' => $api_response[0]['field_utc_department_building'][0]['uri'],
        'address' => $api_response[0]['field_utc_department_street_addr'][0]['title'],
        'facebook' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['facebook']['value'],
        'instagram' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['instagram']['value'],
        'twitter' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['twitter']['value'],
        'youtube' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['youtube']['value'],
        'linkedin' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['linkedin']['value'],
        'homepage' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['departamental_homepage']['value'],
        'vimeo' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['departamental_homepage']['value']
    
        ]
    );
    return;
}

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

        // extract( $args );
        // echo http_get_response();
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
        
        // echo http_get_response();
        $data = [];
        $data = http_get_response();
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


// include plugin dependencies: admin and public
// require_once plugin_dir_url( __FILE__ ) . 'includes/utcdepartmentdirectorycore-functions.php';

function utcdepartmentdirectory_enqueue_style()
{
    wp_enqueue_style('utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'utcdepartmentdirectory/dist/output.css', false);
}
 
function utcdepartmentdirectory_enqueue_script()
{
    wp_enqueue_script('utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'utcdepartmentdirectory/dist/utcdepartmentdirectory.js', false);
}
 
add_action('wp_enqueue_scripts', 'utcdepartmentdirectory_enqueue_style');
add_action('wp_enqueue_scripts', 'utcdepartmentdirectory_enqueue_script');
