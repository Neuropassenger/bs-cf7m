<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://neuropassenger.ru
 * @since      1.0.0
 *
 * @package    Bs_Cf7m
 * @subpackage Bs_Cf7m/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bs_Cf7m
 * @subpackage Bs_Cf7m/includes
 * @author     Oleg Sokolov <turgenoid@gmail.com>
 */
class Bs_Cf7m {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bs_Cf7m_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The loader that's responsible for maintaining updates of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bs_Cf7m_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $updater;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BS_CF7M_VERSION' ) ) {
			$this->version = BS_CF7M_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'bs-cf7m';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bs_Cf7m_Loader. Orchestrates the hooks of the plugin.
	 * - Bs_Cf7m_i18n. Defines internationalization functionality.
	 * - Bs_Cf7m_Admin. Defines all hooks for the admin area.
	 * - Bs_Cf7m_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bs-cf7m-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bs-cf7m-i18n.php';

		/**
		 * The class responsible for defining all shared features & utilities.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bs-cf7m-shared-features.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bs-cf7m-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bs-cf7m-public.php';

		/**
		 * The class responsible for plugin updates.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'libs/plugin-update-checker/plugin-update-checker.php';

		$this->loader = new Bs_Cf7m_Loader();
		$this->updater = Puc_v4_Factory::buildUpdateChecker(
			'https://dev.neuropassenger.ru/rep/bs-cf7m-update-manifest.json',
			plugin_dir_path( dirname( __FILE__ ) ) . 'bs-cf7m.php', // Full path to the main plugin file or functions.php.
			$this->plugin_name
		);

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bs_Cf7m_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Bs_Cf7m_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Bs_Cf7m_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_settings_page' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'add_plugin_settings' );

		$this->loader->add_action( 'wpcf7_mail_sent', $plugin_admin, 'add_new_request' );

        $this->loader->add_action( 'update_option_bs_cf7m_interval', $plugin_admin, 'after_interval_update', 10, 3 );
        $this->loader->add_filter( 'cron_schedules', $plugin_admin, 'cron_interval' );
        $this->loader->add_action( 'bs_cf7m_check_forms', $plugin_admin, 'check_forms' );
		$this->loader->add_action( 'bs_cf7m_zero_requests', $plugin_admin, 'send_requests_alert' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Bs_Cf7m_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Bs_Cf7m_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
