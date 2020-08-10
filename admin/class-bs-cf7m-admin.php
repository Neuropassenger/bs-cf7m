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

	/**
	 * Adds plugin settings page
	 */
    public function add_plugin_settings_page() {
        add_options_page( 'Contact Form 7 Monitor', 'CF7 Monitor', 'manage_options', 'bs_cf7m_settings', array( $this, 'show_plugin_settings_page' ) );
    }

	/**
	 * Shows plugin settings page
	 */
    function show_plugin_settings_page() {
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title() ?></h2>

            <form action="options.php" method="POST">
                <?php
                settings_fields( 'bs_cf7m_general' );
                do_settings_sections( 'bs_cf7m_settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

	/**
	 * Adds plugin settings fields
	 */
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
            __( 'What forms should be scanned', 'bs-cf7m' ),
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
            __( 'Scanning Interval (in hours)', 'bs-cf7m' ),
            array( $this, 'show_interval_field' ),
            'bs_cf7m_settings',
            'bs_cf7m_main_setting_section'
        );

	    register_setting(
		    'bs_cf7m_general',
		    'bs_cf7m_auto_increase_interval',
		    array( 'sanitize_callback' => array( $this, 'sanitize_auto_increase_interval_callback' ) )
	    );

	    add_settings_field(
		    'bs_cf7m_auto_increase_interval_field',
		    __( 'Automatically increase the interval by 24 hours after a failed scan', 'bs-cf7m' ),
		    array( $this, 'show_auto_increase_interval_field' ),
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

	/**
     * Sanitizes active_forms checkbox fields
     *
	 * @param $options
	 *
	 * @return mixed
	 */
    function sanitize_active_forms_callback( $options ) {

        foreach( $options as $id => $option ){
            $options[$id] = intval( $option );
        }

        return $options;

    }

	/**
	 * Shows the active_forms field
	 */
	function show_active_forms_field() {

		$forms = get_posts( array(
				'post_type'     =>  'wpcf7_contact_form',
				'post_status'   =>  'publish',
			)
		);

		$active_forms = get_option( 'bs_cf7m_active_forms', array() );

		foreach ( $forms as $form ) {
			?>
            <p><input type='checkbox' name='bs_cf7m_active_forms[]' <?php checked( in_array( $form->ID, $active_forms ), 1 ); ?> value='<?php echo $form->ID; ?>'>
                <label><?php echo $form->post_title; ?></label></p>
			<?php
		}

	}

	/**
     * Sanitizes the interval field
     *
	 * @param $value
	 *
	 * @return int
	 */
	function sanitize_interval_callback( $value ) {
		return intval( $value );
	}

	/**
	 * Shows the interval field
	 */
	function show_interval_field() {

		$interval = get_option( 'bs_cf7m_interval' );

        ?>
        <p><input style="width: 300px;" type='number' name='bs_cf7m_interval' value='<?php echo $interval; ?>'></p>
        <?php

	}

	/**
     * Sanitizes the emails field
     *
	 * @param $value
	 *
	 * @return string
	 */
	function sanitize_emails_callback( $value ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Shows the emails field
	 */
	function show_emails_field() {

		$emails = get_option( 'bs_cf7m_emails' );

		?>
        <p><input placeholder="one@example.com two@example.com" style="width: 300px;" type='text' name='bs_cf7m_emails' value='<?php echo $emails; ?>'></p>
		<?php

	}

	function sanitize_auto_increase_interval_callback( $value ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Shows the increase interval checkbox
	 */
	function show_auto_increase_interval_field() {
		$auto_increase_interval = get_option( 'bs_cf7m_auto_increase_interval' );
        ?>

		<p>
			<input type='checkbox' name='bs_cf7m_auto_increase_interval' <?php checked( $auto_increase_interval, 'yes' ); ?> value='yes'>
		</p>

		<?php
	}

	/**
     * Fires after updating the field with a check interval
     * Reschedules the next CRON event to check
     *
     * It is launched only if the field value has really been changed
     *
	 * @param $old_value
	 * @param $value - interval in hours
	 * @param $option
	 */
	public function after_interval_update( $old_value, $value, $option ) {
	    update_option( 'bs_cf7m_last_time', time() );

		add_filter( 'cron_schedules', function( $schedules ) use ( $value ){
			return self::cron_interval( $schedules, $value );
		} );

		$schedule_check_forms_timestamp = wp_next_scheduled( 'bs_cf7m_check_forms' );
		wp_unschedule_event( $schedule_check_forms_timestamp, 'bs_cf7m_check_forms' );
	    wp_schedule_event( time() + $value * HOUR_IN_SECONDS, 'bs_cf7m_interval', 'bs_cf7m_check_forms' );
    }

	/**
     * Creates the custom CRON interval
     *
	 * @param $schedules
	 * @param string $value
	 *
	 * @return mixed
	 */
	public static function cron_interval( $schedules, $value = '' ) {
		if ( !$value )
			$value = intval( get_option( 'bs_cf7m_interval' ) ?: 24 );

		$schedules['bs_cf7m_interval'] = array(
			'interval'  =>  $value * HOUR_IN_SECONDS,
			'display'   =>  __( "Every {$value} hours", 'bs-cf7m' )
		);

		return $schedules;
	}

    /* CONTACT FORM 7 MONITOR */

	/**
     * Adds a new application to the database
     *
	 * @param $contact_form
	 */
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

	/**
	 * Checks forms activity
     * Starts when the next scheduled CRON check is triggered
	 */
    public function check_forms() {

	    $last_time = get_option( 'bs_cf7m_last_time' );
	    $current_time = time();

    	global $wpdb;
	    $table_name = $wpdb->prefix . 'bs_cf7m_requests';
	    $active_forms = get_option( 'bs_cf7m_active_forms' );
	    $not_used_forms = array();

	    foreach ( $active_forms as $form_id ) {
	    	$requests_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name} WHERE form_id = {$form_id} AND time > {$last_time} AND time < {$current_time}" );

	    	if ( $requests_count < 1 )
	    		$not_used_forms[] = get_post( $form_id );
	    }

	    if ( count( $not_used_forms ) > 0 )
	        do_action( 'bs_cf7m_zero_requests', $not_used_forms );

	    update_option( 'bs_cf7m_last_time', time() );

	    /* Increase the scan interval if necessary */
	    /* TODO: включить проверку каждые 24 часа */
	    $auto_increase_interval = get_option( 'bs_cf7m_auto_increase_interval' );
	    if ( count( $not_used_forms ) == count( $active_forms ) && $auto_increase_interval == 'yes' ) {
		    $current_interval = get_option( 'bs_cf7m_interval' );
		    update_option( 'bs_cf7m_interval', $current_interval + 24 );
	    }

    }

	/**
     * Sends email alerts
     *
	 * @param $not_used_forms
	 */
    public function send_requests_alert( $not_used_forms ) {
	    /* FIX for do_action array with single arg when only one form activated */
	    /* TODO: Нужно как-то отказаться от этого костыля 🤔 */
	    if ( is_object( $not_used_forms ) ) {
		    $good_array = array();
		    $good_array[] = $not_used_forms;
		    $not_used_forms = $good_array;
	    }

    	if ( count( $not_used_forms ) < 1 )
    		return;

    	$emails = get_option( 'bs_cf7m_emails' );
    	$active_forms = get_option( 'bs_cf7m_active_forms' );

    	if ( empty( $emails ) )
    	    $emails = array();
    	else
    	    $emails = explode( ' ', $emails );

    	// Admin email
        $emails[] = get_bloginfo( 'admin_email' );

    	$last_time = get_option( 'bs_cf7m_last_time' );
    	$current_time = time();
	    $next_time = wp_next_scheduled( 'bs_cf7m_check_forms' );
    	$real_interval = floor( ($current_time - $last_time) / 3600 );
    	$domain = str_replace( 'http://', '', str_replace( 'https://', '', site_url() ) );
    	$settings_page_permalink = get_admin_url( null, 'options-general.php?page=bs_cf7m_settings' );
	    $last_date = date( 'F j Y  H:i', $last_time );
	    $current_date = date( 'F j Y  H:i', $current_time );

    	$headers = "From: Contact Form 7 Monitor <informer@{$domain}>";
    	$subject = get_bloginfo( 'name' ) . " |  No new applications were received";
    	$body = "<p>Hi there,️</p>";
    	$body .= "<p>No applications have been received from the forms below in the past {$real_interval} hours:</p>";

    	$body .= "<ul>";

    	foreach ( $not_used_forms as $form ) {
    	    $edit_form_permalink = get_admin_url( null, "admin.php?page=wpcf7&post={$form->ID}&action=edit" );
			$body .= "<li><a href='{$edit_form_permalink}'>{$form->post_title}</a></li>";
	    }
	    $body .= "</ul>";

    	$body .= "<p>Check the work of the specified forms, or increase the scanning interval.</p>";

    	if ( get_option( 'bs_cf7m_auto_increase_interval' ) == 'yes' && count( $active_forms ) == count( $not_used_forms ) ) {
		    $body .= "<p>Since there were no new applications for any of the forms, the scanning interval was automatically increased by 24 hours. It can be changed at any time in the plugin settings.</p>";
	        $next_time += 86400;
    	}

	    $body .= "<p>This email was sent by the Contact Form 7 Monitor plugin from {$domain}. If you do not want to receive these emails anymore, delete your email address on the <a href='{$settings_page_permalink}'>plugin settings page</a>.</p>";

	    $next_date = date( 'F j Y  H:i', $next_time );

    	$body .= "<hr>";

	    $body .= "<p>Previous scan: {$last_date} UTC</p>";
	    $body .= "<p>Current scan: {$current_date} UTC</p>";
	    $body .= "<p>Next scan: {$next_date} UTC</p>";

	    add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
	    $mail_sent = wp_mail(
	    	$emails,
		    $subject,
		    $body,
		    $headers
	    );
	    remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
    }

	/**
     * Enables HTML in emails
     *
	 * @return string
	 */
	function set_html_content_type() {
		return 'text/html';
	}

}
