<?php 

register_activation_hook( __FILE__, 'youpzt_message_activate' );
/**
 * Create table and register an option when activate
 *
 * @return void
 */
function youpzt_message_activate()
{
	global $wpdb;

	// Create table
	$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->youpzt_messages . ' (
		`id` bigint(20) NOT NULL auto_increment,
		`msg_type` tinyint(1) DEFAULT NULL,
		`subject` text NOT NULL,
		`content` text NOT NULL,
		`sender` varchar(60) NOT NULL,
		`recipient` varchar(60) NOT NULL,
		`date` datetime NOT NULL,
		`read` tinyint(1) NOT NULL,
		`deleted` tinyint(1) NOT NULL,
		PRIMARY KEY (`id`)
	) COLLATE utf8_general_ci;';

	// Note: deleted = 1 if message is deleted by sender, = 2 if it is deleted by recipient

	$wpdb->query( $query );

	// Default numbers of PM for each group
	$default_option = array(
		'administrator' => 0,
		'editor'        => 50,
		'author'        => 20,
		'contributor'   => 10,
		'subscriber'    => 5,
		'type'          => 'dropdown', // How to choose recipient: dropdown list or autocomplete based on user input
		'email_enable'  => 1,
		'email_name'    => '%BLOG_NAME%',
		'email_address' => '%BLOG_ADDRESS%',
		'email_subject' => __( '新的消息 %BLOG_NAME%', 'youpzt' ),
		'email_body'    => __( "你有新的消息，来自 <b>%SENDER%</b> -<b>%BLOG_NAME%</b>.\n\n<a href=\"%INBOX_URL%\">点击这里</a> 去你的消息箱.\n\n这邮件为自动发送，请不要回复.", 'youpzt' )
	);
	add_option( 'youpzt_messages_option', $default_option, '', 'no' );
}

/**
 * define youpzt_messages table in wpdb
 */
if (!function_exists('youpzt_messages_define_table')) {
		function youpzt_messages_define_table() {
			global $wpdb;
			$wpdb->youpzt_messages = $wpdb->prefix . 'youpzt_messages';
		}
}
add_action( 'init', 'youpzt_messages_define_table' );
?>