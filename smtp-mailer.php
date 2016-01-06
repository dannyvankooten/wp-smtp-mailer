<?php
/*
Plugin Name: SMTP Mailer
Description: This plugin will configure wp_mail to use SMTP for sending your email.
Author: Danny van Kooten
Version: 1.0
Author URI: https://dannyvankooten.com/
Private: True
*/

namespace SMTP_Mailer;

if( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {

	/**
	 * @var array Necessary configuration constants, these should be defined in your /wp-config.php file
	 */
	private $constants = array(
		'SMTP_HOST',
		'SMTP_PORT',
		'SMTP_USER',
		'SMTP_PASSWORD'
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'phpmailer_init', array( $this, 'setup_phpmailer' ) );
	}

	/**
	 * Alter the PHPMailer object
	 *
	 * @param $phpmailer
	 */
	public function setup_phpmailer( $phpmailer ) {

		// make sure all configuration constants are given
		foreach( $this->constants as $constant ) {
			if( ! defined( $constant ) || '' === constant( $constant ) ) {
				return;
			}
		}

		$phpmailer->Mailer = 'smtp';
		$phpmailer->SMTPSecure = 'ssl';
		$phpmailer->Host = SMTP_HOST;
		$phpmailer->Port = SMTP_PORT;
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = SMTP_USER;
		$phpmailer->Password = SMTP_PASSWORD;
	}

}

new Plugin;

// remove plugin from update check response
add_filter( 'site_transient_update_plugins', function( $value ) {

	if( is_object( $value ) && isset( $value->response ) ) {
		$plugin_slug = plugin_basename( __FILE__ );
		unset( $value->response[ $plugin_slug ] );
	}

	return $value;
} );