<?php
/*
Plugin Name: Clean Markup Widget
Description: Add clean, well-formatted markup to any widgetized area.
Plugin URI:  https://perishablepress.com/clean-markup-widget/
Author:      Jeff Starr
Version:     1.1
*/


// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}


require __DIR__ . '/vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;




 // example: GET request
 function http_get_request( $url ) {

    $url = esc_url_raw( $url );

    $args = array( 'user-agent' => 'Plugin Demo: HTTP API; '. home_url() );

    return wp_safe_remote_get( $url, $args );

}

// example: GET response
function http_get_response() {

    $url = 'https://www.utc.edu/json/departmentdirectory';

    $response = http_get_request( $url );

    // response data

    $code    = wp_remote_retrieve_response_code( $response );
    $message = wp_remote_retrieve_response_message( $response );
    $body    = wp_remote_retrieve_body( $response );
    $headers = wp_remote_retrieve_headers( $response );

    $header_date  = wp_remote_retrieve_header( $response, 'date' );
    $header_type  = wp_remote_retrieve_header( $response, 'content-type' );
    $header_cache = wp_remote_retrieve_header( $response, 'cache-control' );

    // output data

    // $output  = '<h2><code>'. $url .'</code></h2>';

    // $output .= '<h3>Status</h3>';
    // $output .= '<div>Response Code: '    . $code    .'</div>';
    // $output .= '<div>Response Message: ' . $message .'</div>';

    // $output .= '<h3>Body</h3>';
    $output .= '<pre>';
    ob_start();
    // var_dump( $body );
    $output .= ob_get_clean();
    /* Will result in $api_response being an array of data,
    parsed from the JSON response of the API listed above */
    $api_response = json_decode( $body, true );
    // var_dump( $api_response[0]['info'][0]['value'] );
    $output .= '</pre>';
    $output .= '<h3>Department</h3>';
    $output .= '<div>Organizational Section name ' . $api_response[0]['info'][0]['value']  .'</div>';
    $output .= '<div>Organizational Section ID '  . $api_response[0]['field_utc_organizational_section'][0]['target_id']  .'</div>';
    $output .= '<div>Organizational Section mail code ' . $api_response[0]['field_utc_department_mail_code'][0]['value'] .'</div>';

    $api_reponse_size = count($api_response);
    $organization_array = array();
    $organization_output= [];
    foreach($api_response as $index => $organization)
    {
        // organization_array = array($api_response[$index]['info'][0]['value'],;
        $organization_array[$index]['name'] = $api_response[$index]['info'][0]['value'];
        $organization_array[$index]['OrganizationalSectionID'] = $api_response[$index]['field_utc_organizational_section'][0]['target_id'];
    }
    $output .= '<pre>';

    // $output .= '<h3>Headers</h3>';
    // $output .= '<div>Response Date: ' . $header_date  .'</div>';
    // $output .= '<div>Content Type: '  . $header_type  .'</div>';
    // $output .= '<div>Cache Control: ' . $header_cache .'</div>';
    // $output .= '<pre>';
    ob_start();
    // var_dump( $headers );
    $output .= ob_get_clean();
    $output .= '</pre>';
    $organizational_output[0] = $output;
    $organizational_output[1] = $organization_array;
    return $organizational_output;

}

// example: GET response
function public_http_get_response($organizationalSectionID) {

    $url = 'https://www.utc.edu/json/departmentdirectory'. '/' . $organizationalSectionID ;

    $response = http_get_request( $url );

    // response data

    $code    = wp_remote_retrieve_response_code( $response );
    $message = wp_remote_retrieve_response_message( $response );
    $body    = wp_remote_retrieve_body( $response );
    $headers = wp_remote_retrieve_headers( $response );

    $header_date  = wp_remote_retrieve_header( $response, 'date' );
    $header_type  = wp_remote_retrieve_header( $response, 'content-type' );
    $header_cache = wp_remote_retrieve_header( $response, 'cache-control' );
    



    // output data

    // $output  = '<h2><code>'. $url .'</code></h2>';

    // $output .= '<h3>Status</h3>';
    // $output .= '<div>Response Code: '    . $code    .'</div>';
    // $output .= '<div>Response Message: ' . $message .'</div>';

    // $output .= '<h3>Body</h3>';
    // $output .= '<pre>';
    ob_start();
    // var_dump( $body );
    $output .= ob_get_clean();
    /* Will result in $api_response being an array of data,
    parsed from the JSON response of the API listed above */
    $api_response = json_decode( $body, true );

    $loader = new FilesystemLoader(__DIR__ . '/templates');
    $twig = new Environment($loader);
    echo $twig->render('departmentinfo.html.twig', 
    [
        'name' => $api_response[0]['info'][0]['value'], 
        'taxonomyid' => $api_response[0]['field_utc_organizational_section'][0]['target_id'],
        'mailcode' => $api_response[0]['field_utc_department_mail_code'][0]['value'],
        'phonenumber' => $api_response[0]['field_utc_department_phone'][0]['value'],
        'phonenumber' => $api_response[0]['field_utc_department_fax_number'][0]['value'],
        'buildingtitle' => $api_response[0]['field_utc_department_building'][0]['title'],
        'buildingurl' => $api_response[0]['field_utc_department_building'][0]['value'],
        'address' => $api_response[0]['field_utc_department_street_addr'][0]['value'],
        'facebook' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['facebook']['value'],
        'instagram' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['instagram']['value'],
        'twitter' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['twitter']['value'],
        'youtube' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['youtube']['value'],
        'linkedin' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['linkedin']['value'],
        'homepage' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['departamental_homepage']['value'],
        'vimeo' => $api_response[0]['field_utc_department_socialmedia'][0]['platform_values']['departamental_homepage']['value']       
    
    ]);


    ob_start();
    // var_dump( $headers );
    $output .= ob_get_clean();
    $output .= '</pre>';
    return $output;

}



// widget example: clean markup
class Clean_Markup_Widget extends WP_Widget {

	public function __construct() {

		$id = 'clean_markup_widget';

		$title = esc_html__('Clean Markup Widget', 'custom-widget');

		$options = array(
			'classname' => 'clean-markup-widget',
			'description' => esc_html__('Adds clean markup that is not modified by WordPress.', 'custom-widget')
		);

		parent::__construct( $id, $title, $options );

	}

	public function widget( $args, $instance ) {

		// extract( $args );
        // echo http_get_response();
        $markup = '';

		if ( isset( $instance['markup'] ) ) {
            
            echo public_http_get_response($instance['markup']);
            echo wp_kses_post( $instance['markup'] );
            

		}

	}

	public function update( $new_instance, $old_instance ) {

		$instance = array();

		if ( isset( $new_instance['markup'] ) && ! empty( $new_instance['markup'] ) ) {

			$instance['markup'] = $new_instance['markup'];

		}

		return $instance;

    }

	public function form( $instance ) {

		$id = $this->get_field_id( 'markup' );

		$for = $this->get_field_id( 'markup' );

		$name = $this->get_field_name( 'markup' );

        $label = __( 'Markup/text:', 'custom-widget' );
        
        $label = __( 'Markup/text:', 'custom-widget' );

        $markup = '<p>'. __( 'Clean markup.', 'custom-widget' ) .'</p>';
        
        // echo http_get_response();
        $data = [];
        $data = http_get_response();
        echo $data[0];

		if ( isset( $instance['markup'] ) && ! empty( $instance['markup'] ) ) {

			$markup = $instance['markup'];

		}
        $valuesselected = [];
		?>

		<p>
			<label for="<?php echo esc_attr( $for ); ?>"><?php echo esc_html( $label ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
            <?php foreach($data[1] as $index=>$row) { ?>
            <option value="<?= $row["OrganizationalSectionID"] ?>"<?php selected( $valuesselected["name"] , $row["OrganizationalSectionID"]); ?>><?= $row["name"] ?></option> <?php } ?>
            </select>
            <?php $markup = $valuesselected[0]; ?>
            <label for="<?php echo esc_attr( $for ); ?>"><?php echo $markup; ?></label>

		</p>

<?php }

}

// register widget
function utcdepartmentdirectory_register_widgets() {

	register_widget( 'Clean_Markup_Widget' );

}
add_action( 'widgets_init', 'utcdepartmentdirectory_register_widgets' );


// include plugin dependencies: admin and public
// require_once plugin_dir_url( __FILE__ ) . 'includes/utcdepartmentdirectorycore-functions.php';

function themeslug_enqueue_style() {
    wp_enqueue_style( 'utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'utcdepartmentdirectory/dist/output.css', false );
}
 
function themeslug_enqueue_script() {
    wp_enqueue_script( 'utcdepartmentdirectory-public', plugin_dir_url(dirname(__FILE__)) . 'utcdepartmentdirectory/public/js/utcdepartmentdirectory.js', false );
}
 
add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_script' );