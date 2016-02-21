<?php 
/* 注册激活插件时要调用的函数 */ 
register_activation_hook( __FILE__, 'youpzt_messages_activate' );
/**
 * Create table and register an option when activate
 *
 * @return void
 */
function youpzt_messages_activate()
{
	global $wpdb;
	$wpdb->youpzt_messages = $wpdb->prefix . 'youpzt_messages';
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');		
	if( $wpdb->get_var("SHOW TABLES LIKE '$wpdb->youpzt_messages'") != $wpdb->youpzt_messages){
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

		dbDelta($query);
		//$wpdb->query( $query );
	 }
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
global $pagenow;
$admin_page_GET=isset($_GET['page'])?$_GET['page']:false;
if (is_admin()&&$admin_page_GET=='youpzt_messages_option'&& isset( $_GET['active'])){

	youpzt_messages_activate();//创建数据库
}
/**
 * define youpzt_messages table in wpdb
 */
if (!function_exists('youpztMessages_define_table')) {
		function youpztMessages_define_table() {
			global $wpdb;
			$wpdb->youpzt_messages = $wpdb->prefix . 'youpzt_messages';
		}
}
add_action( 'init', 'youpztMessages_define_table' );
?>