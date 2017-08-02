<?php
/*
  Plugin Name: Tiny Simple AdBlock Detector
  Plugin URI: http://myththrazz.com/
  Author URI: http://myththrazz.com/
  Description: Detects (some) AdBlocking software and allows to display some html based on detection result
  Author: MythThrazz
  Text Domain: tiny-simple-adblock-detector
  Domain Path: /languages
  Version: 1.0.1
 */

class TinySimpleAdBlockDetector {

	private static $instance = null;

	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof TinySimpleAdBlockDetector ) ) {
			self::$instance = new TinySimpleAdBlockDetector;
		}

		self::$instance->setup_constants();
		self::$instance->init();
		return self::$instance;
	}

	private function setup_constants() {
		/**
		 * Define Plugin File Name
		 *
		 * @since 1.0
		 */
		if ( !defined( 'TSAD_PLUGIN_FILE' ) ) {
			define( 'TSAD_PLUGIN_FILE', __FILE__ );
		}
	}

	private function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_script' ) );
		add_action( 'wp_head', array( $this, 'custom_style' ), 100 );
		add_action( 'wp_footer', array( $this, 'debug' ), 0 );

		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 11 );
	}

	public function add_menu() {
		add_menu_page( 'Tiny Simple AdBlock Detector', 'Tiny Simple AdBlock Detector', 'manage_options', 'tsad_menu', array( $this, 'show_about' ) );
	}

	public function show_about() {
		?>
		<div style="display:block">
			<ol>
				<li>Plugin has no configuration and it's already running.</li>
				<li>Use this link to <a target="_blank" href="<?php echo home_url( '?tsad_debug=1#tsad_debug_message' ); ?>">check if it's working</a> - it will open in a new tab. Debug information will show in the bottom of the page.</li>
				<li>Add <code>class="show-only-when-adblock"</code> to display element only when AdBlock is detected (eg. a message asking to disable it)</li>
				<li>Add <code>class="hide-only-when-adblock"</code> to hide element only when AdBlock is detected (eg. hide part of your content from AdBlock users)</li>
			</ol>
		</div>
		<?php
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'tiny-simple-adblock-detector', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	public function load_script() {
		wp_register_script( 'tiny-simple-adblock-detector-ads', plugin_dir_url( __FILE__ ) . 'showads.js' );
		wp_enqueue_script( 'tiny-simple-adblock-detector', plugin_dir_url( __FILE__ ) . 'tiny-simple-adblock-detector.js', array( 'tiny-simple-adblock-detector-ads' ), false, true );
	}

	public function custom_style() {
		?>
		<style>
			.show-only-when-adblock{display:none}
			.hide-only-when-adblock{display:block}
		</style>
		<?php
	}

	public function adblock_detected_style() {
		?>
		<style>
			.show-only-when-adblock{display:initial}
			.hide-only-when-adblock{display:none}
		</style>
		<?php
	}

	public function debug() {
		$debugEnabled = filter_input( INPUT_GET, 'tsad_debug' );
		if ( $debugEnabled ) {
			?>
			<div id="tsad_debug_message" style="text-align: center;font-size: 12pt;font-weight: bold;border: 2px solid red;margin: 10px;color: black;background: white;padding: 5px;z-index: 999999;position: relative;">
				<p>
					<?php _e( 'Tiny Simple AdBlock Detector is running', 'tiny-simple-adblock-detector' ); ?>
				</p>
				<p>
					<?php printf( __( 'You can use %s and %s to control content displayed for AdBlock users.', 'tiny-simple-adblock-detector' ), '<code>class="show-only-when-adblock"</code>', '<code>class="hide-only-when-adblock"</code>' ); ?>
				</p>
				<p>
					<?php _e( 'Message below should show if AdBlock is detected. If there is no message below please contact support.', 'tiny-simple-adblock-detector' ); ?>
				</p>
				<div class="show-only-when-adblock" style="color: red;"><?php _e( 'AdBlock detected!', 'tiny-simple-adblock-detector' ); ?></div>
				<div class="hide-only-when-adblock" style="color: green;"><?php _e( 'AdBlock not detected!', 'tiny-simple-adblock-detector' ); ?></div>
			</div>
			<?php
		}
	}

}

function TinySimpleAdBlockDetectorInit() {
	if ( !class_exists( 'TinySimpleAdBlockDetector' ) ) {
		return;
	}
	return TinySimpleAdBlockDetector::instance();
}

add_action( 'plugins_loaded', 'TinySimpleAdBlockDetectorInit' );
