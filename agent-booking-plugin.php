<?php
/*
 * Plugin Name:       Hotel Plugin
 * Plugin URI:        https://github.com/sumegizoltan73/hotel-plugin
 * Description:       Hotel booking with WordPress plugin.
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Zoltan Peter Sumegi & ChatGPT
 * Author URI:        https://www.sumegizoltanpeter.hu/
 * License:           MIT
 * License URI:       https://mit-license.org
 * Update URI:        https://programozo.info.hu/hotel-plugin/
 * Text Domain:       hotel-plugin
 * Domain Path:       languages
 * Requires Plugins:  
 */

if ( ! class_exists( 'WPOrg_Hotel_Plugin' ) ) {
    class WPOrg_Hotel_Plugin {
        
				public function __construct(){
						define('HOTEL_PLUGIN_PATH', plugin_dir_path(__FILE__));
						define('HOTEL_PLUGIN_URL', plugin_dir_url( __FILE__ ));
						
				}
				public function initialize() {
            //include_once HOTEL_PLUGIN_PATH . 'includes/hotel-form.php';
        }

        public function get_items() {
            return get_option( 'wporg_items' );
        }

        
    }

		$hotelPlugin = new WPOrg_Hotel_Plugin;
		$hotelPlugin->initialize();

require_once __DIR__ . '/includes/render.php';

add_action( 'init', function () {
    register_block_type(
        __DIR__, // ← automatikusan beolvassa a block.json-t
        [
            'render_callback' => 'hotel_plugin_render'
        ]
    );
} );

require_once __DIR__ . '/includes/block.php';
require_once __DIR__ . '/includes/widget.php';
require_once __DIR__ . '/includes/block-styles.php';
require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/booking.php';

/**
 * custom option and settings
 */
function wporg_settings_init() {
	// Register a new setting for "wporg" page.
	//register_setting( 'wporg', 'wporg_options' );
	//register_setting( 'wporg', 'wporg_setting_name' );
	register_setting( 'wporg', 'wporg_items' );


	// Register a new section in the "wporg" page.
	add_settings_section(
		'wporg_section_developers',
		__( 'The Matrix has you.', 'hotel-plugin' ), 'wporg_section_developers_callback',
		'wporg'
	);

	add_settings_field(
    'wporg_items',
    __( 'Rooms', 'hotel-plugin' ),
    'wporg_items_cb',
    'wporg',
    'wporg_section_developers'
	);

	//new Hotel_Plugin_Widget();
}

/**
 * Register our wporg_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'wporg_settings_init' );


add_action( 'plugins_loaded', 'hotel_plugin_load_textdomain' );

function hotel_plugin_load_textdomain() {
    load_plugin_textdomain(
        'hotel-plugin',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}


/**
 * Custom option and settings:
 *  - callback functions
 */
function wporg_items_cb() {
    $items = get_option( 'wporg_items', [] );
    ?>
    <div id="wporg-repeater">
        <?php foreach ( $items as $i => $item ) : ?>
            <div class="wporg-item">
                <input type="text"
                       name="wporg_items[<?php echo $i; ?>][number]"
                       value="<?php echo esc_attr( $item['number'] ?? '' ); ?>"
                       placeholder="<?php _e('Number', 'hotel-plugin'); ?>" />

								<input type="text"
                       name="wporg_items[<?php echo $i; ?>][personcnt]"
                       value="<?php echo esc_attr( $item['personcnt'] ?? '' ); ?>"
                       placeholder="<?php _e('Person Count', 'hotel-plugin'); ?>" />

                <input type="text"
                       name="wporg_items[<?php echo $i; ?>][title]"
                       value="<?php echo esc_attr( $item['title'] ?? '' ); ?>"
                       placeholder="<?php _e('Title', 'hotel-plugin'); ?>" />

                <input type="text"
                       name="wporg_items[<?php echo $i; ?>][subtitle]"
                       value="<?php echo esc_attr( $item['subtitle'] ?? '' ); ?>"
                       placeholder="<?php _e('Subtitle', 'hotel-plugin'); ?>" />
								<input type="text"
                       		name="wporg_items[<?php echo $i; ?>][price]"
                       		value="<?php echo esc_attr( $item['price'] ?? '' ); ?>"
                       		placeholder="<?php _e('Price', 'hotel-plugin'); ?>" />
								<select
										data-language="<?php echo esc_attr( $item['lang'] ?? '' ); ?>"
										name="wporg_items[<?php echo $i; ?>][lang]"
										placeholder="<?php _e('Language', 'hotel-plugin'); ?>"
								>
									<option value="hu_HU" <?php echo isset( $item['lang'] ) ? ( selected( $item['lang'], 'hu_HU', false ) ) : ( '' ); ?>>
										<?php _e( 'Hungarian', 'hotel-plugin' ); ?>
									</option>
									<option value="en_US" <?php echo isset( $item['lang'] ) ? ( selected( $item['lang'], 'en_US', false ) ) : ( '' ); ?>>
										<?php _e( 'English', 'hotel-plugin' ); ?>
									</option>
									<option value="de_DE" <?php echo isset( $item['lang'] ) ? ( selected( $item['lang'], 'de_DE', false ) ) : ( '' ); ?>>
										<?php _e( 'German', 'hotel-plugin' ); ?>
									</option>
								</select>

                <textarea
                    name="wporg_items[<?php echo $i; ?>][desc]"
                    placeholder="<?php _e('Description', 'hotel-plugin'); ?>"><?php echo esc_textarea( $item['desc'] ?? '' ); ?></textarea>
                <textarea
                    name="wporg_items[<?php echo $i; ?>][details]"
                    placeholder="<?php _e('Details', 'hotel-plugin'); ?>"><?php echo esc_textarea( $item['details'] ?? '' ); ?></textarea>

								<div>
										<input type="text" 
														name="wporg_items[<?php echo $i; ?>][image_id]" 
														value="<?php echo esc_attr( $item['image_id'] ?? '' ); ?>" 
														class="hotel_media_image_id" 
														placeholder="<?php _e('Image ID\'s', 'hotel-plugin'); ?>" />
 										<input type='button' 
														class="button-primary hotel_media_manager" 
														value="<?php esc_attr_e( 'Select a image', 'hotel-plugin' ); ?>" 
														onclick="Browse_OnClick(event)"/>
								</div>

                <button type="button" class="button remove-item">–</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" class="button" id="add-item">+ <?php _e('New Item', 'hotel-plugin'); ?></button>
    <?php
}

add_action('admin_enqueue_scripts', function () {
		wp_enqueue_media();
    wp_enqueue_script('hotel-plugin-admin-media', plugin_dir_url(__FILE__) . '/admin/js/admin_media.js', array('jquery'), false, true);
		
		wp_register_script(
			'hotel-plugin-admin',
			plugin_dir_url(__FILE__) . '/admin/js/admin.js',
			array( 'wp-i18n', 'jquery' ),
			'0.0.1'
		);
		//wp_set_script_translations('hotel-plugin-admin', 'hotel-plugin', '');
		
    wp_enqueue_script('hotel-plugin-admin', plugin_dir_url(__FILE__) . '/admin/js/admin.js', array('jquery'), false, true);
		wp_enqueue_style(
        'hotel-admin-style',
        plugin_dir_url(__FILE__) . '/admin/css/admin.css'
    );

});

/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function wporg_section_developers_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'hotel-plugin' ); ?></p>
	<?php
}

/**
 * Add the top level menu page.
 */
function wporg_options_page() {
	add_menu_page(
		__('Hotel Admin', 'hotel-plugin'),
		__('Hotel Settings', 'hotel-plugin'),
		'manage_options',
		'wporg',
		'wporg_options_page_html'
	);
}


/**
 * Register our wporg_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'wporg_options_page' );

function hotel_add_rewrite_rules() {
    add_rewrite_rule(
        '^({2})',
        'index.php?lang=$matches[1]&room=$matches[2]',
        'top'
    );
}
add_action('init', 'hotel_add_rewrite_rules');

function hotel_query_vars($vars) {
    $vars[] = 'lang';
    $vars[] = 'room';
    return $vars;
}
add_filter('query_vars', 'hotel_query_vars');

function hotel_template_redirect() {
    $room = get_query_var('room');
    $lang = get_query_var('lang');

    if ($room) {
        include plugin_dir_path(__FILE__) . 'includes/templates/room-details.php';
        exit;
    }
}
add_action('template_redirect', 'hotel_template_redirect');

/**
 * Top level menu callback function
 */
function wporg_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'hotel-plugin' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'wporg_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "wporg"
			settings_fields( 'wporg' );
			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections( 'wporg' );
			// output save settings button
			submit_button( __('Save Settings', 'hotel-plugin') );
			?>
		</form>
	</div>
	<?php
}

}