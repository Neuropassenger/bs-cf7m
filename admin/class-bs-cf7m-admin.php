<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://neuropassenger.ru
 * @since      1.0.0
 *
 * @package    Bs_Cf7m
 * @subpackage Bs_Cf7m/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bs_Cf7m
 * @subpackage Bs_Cf7m/admin
 * @author     Oleg Sokolov <turgenoid@gmail.com>
 */
class Bs_Cf7m_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bs_Cf7m_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bs_Cf7m_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bs-cf7m-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bs_Cf7m_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bs_Cf7m_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bs-cf7m-admin.js', array( 'jquery' ), $this->version, false );

	}



	/* PLUGIN SETTINGS */

    public function add_plugin_settings_page() {
        add_options_page( 'Contact Form 7 Monitor', 'CF7 Monitor', 'manage_options', 'bs_cf7m_settings', array( $this, 'show_plugin_settings_page' ) );
    }

    function show_plugin_settings_page() {
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title() ?></h2>

            <form action="options.php" method="POST">
                <?php
                // скрытые защитные поля
                settings_fields( 'bs_cf7m_general' );
                // секции с настройками (опциями)
                do_settings_sections( 'bs_cf7m_settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function add_plugin_settings() {

	    add_settings_section(
            'bs_cf7m_main_setting_section',
            __( 'Main Settings', 'bs-cf7m' ),
            '',
            'bs_cf7m_settings'
        );

        register_setting(
            'bs_cf7m_general',
            'bs_cf7m_active_forms',
            array( 'sanitize_callback' => array( $this, 'sanitize_active_forms_callback' ) )
        );

        add_settings_field(
            'bs_cf7m_active_forms_field',
            __( 'What forms should be observed', 'bs-cf7m' ),
            array( $this, 'show_active_forms_field' ),
            'bs_cf7m_settings',
            'bs_cf7m_main_setting_section'
        );

	    register_setting(
		    'bs_cf7m_general',
		    'bs_cf7m_interval',
		    array( 'sanitize_callback' => array( $this, 'sanitize_interval_callback' ) )
	    );

        add_settings_field(
            'bs_cf7m_interval_field',
            __( 'Inspection Interval (in hours)', 'bs-cf7m' ),
            array( $this, 'show_interval_field' ),
            'bs_cf7m_settings',
            'bs_cf7m_main_setting_section'
        );

	    register_setting(
		    'bs_cf7m_general',
		    'bs_cf7m_emails',
		    array( 'sanitize_callback' => array( $this, 'sanitize_emails_callback' ) )
	    );

	    add_settings_field(
		    'bs_cf7m_emails_field',
		    __( 'Email addresses for notifications (separated by a space)', 'bs-cf7m' ),
		    array( $this, 'show_emails_field' ),
		    'bs_cf7m_settings',
		    'bs_cf7m_main_setting_section'
	    );

    }

    function sanitize_active_forms_callback( $options ) {

	foreach( $options as $id => $option ){
		$options[$id] = intval( $option );
	}

	return $options;

}

	function show_active_forms_field() {

		$forms = get_posts( array(
				'post_type'     =>  'wpcf7_contact_form',
				'post_status'   =>  'publish',
			)
		);

		$active_forms = get_option( 'bs_cf7m_active_forms' );

		foreach ( $forms as $form ) {
			?>
            <p><input type='checkbox' name='bs_cf7m_active_forms[]' <?php checked( in_array( $form->ID, $active_forms ), 1 ); ?> value='<?php echo $form->ID; ?>'>
                <label><?php echo $form->post_title; ?></label></p>
			<?php
		}

	}

	function sanitize_interval_callback( $value ) {
		return intval( $value );
	}

	function show_interval_field() {

		$interval = get_option( 'bs_cf7m_interval' );

        ?>
        <p><input style="width: 300px;" type='number' name='bs_cf7m_interval' value='<?php echo $interval; ?>'></p>
        <?php

	}

	function sanitize_emails_callback( $value ) {
		return sanitize_text_field( $value );
	}

	function show_emails_field() {

		$emails = get_option( 'bs_cf7m_emails' );

		?>
        <p><input style="width: 300px;" type='text' name='bs_cf7m_emails' value='<?php echo $emails; ?>'></p>
		<?php

	}

	public function after_interval_update( $old_value, $value, $option ) {
	    update_option( 'bs_cf7m_last_time', time() );

		add_filter( 'cron_schedules', function( $schedules ) use ( $value ){
			return $this->cron_interval( $schedules, $value );
		} );

		$schedule_check_forms_timestamp = wp_next_scheduled( 'bs_cf7m_check_forms' );
		wp_unschedule_event( $schedule_check_forms_timestamp, 'bs_cf7m_check_forms' );
	    wp_schedule_event( time(), 'bs_cf7m_interval', 'bs_cf7m_check_forms' );

	    Bs_Cf7m_Shared_Features::bs_logit( 'Check', 'interval update' );
    }

	public function cron_interval( $schedules, $value = '' ) {
		if ( !$value )
			$value = intval( get_option( 'bs_cf7m_interval' ) ?: 24 );

		$schedules['bs_cf7m_interval'] = array(
			'interval'  =>  $value * HOUR_IN_SECONDS,
			'display'   =>  __( "Every {$value} hours", 'bs-cf7m' )
		);

		return $schedules;
	}

    /* CONTACT FORM 7 MONITOR */

    public function add_new_request( $contact_form ) {
		global $wpdb;

        $wpdb->insert(
        	$wpdb->prefix . 'bs_cf7m_requests',
	        array(
	        	'form_id'   =>  $contact_form->id,
		        'time'      =>  time()
	        ),
	        array( '%d', '%d' )
        );

    }

    public function check_forms() {
    	global $wpdb;
	    $table_name = $wpdb->prefix . 'bs_cf7m_requests';
	    $last_time = get_option( 'bs_cf7m_last_time' );
	    $active_forms = get_option( 'bs_cf7m_active_forms' );
	    $current_time = time();
	    $form_names = array();

	    foreach ( $active_forms as $form_id ) {
	    	$requests_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name} WHERE time > {$last_time} AND time < {$current_time}" );

	    	if ( $requests_count == 0 )
	    		$form_names[] = get_post( $form_id )->post_title;
	    }

	    if ( count( $form_names ) > 0 )
	        do_action( 'bs_cf7m_zero_requests', $form_names );

	    update_option( 'bs_cf7m_last_time', time() );
    }

    public function send_requests_alert( $form_names ) {
    	$emails = get_option( 'bs_cf7m_emails' );
    	$interval = get_option( 'bs_cf7m_interval' );
    	$emails = explode( ' ', $emails );

    	$body = "<p>Hi there,</p>";
    	$body .= "<p>Hi there,</p>";

    	$site_url = home_url();
	    add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	    wp_mail(
	    	$emails,
		    __( "No new applications were received from the forms on site {$site_url} within {$interval} hours", 'bs_cf7m' ),
		    $body
	    );
	    remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
    }

}
