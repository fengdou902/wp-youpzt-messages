<?php
/*
Plugin Name: 站内信youpzt-messages
Plugin URI: http://www.youpzt.com/youpzt-messages
Description:允许你的WordPress网站用户之间可进行站内信互相通信
Version: 1.0
Author: youpzt
Author URI: http://www.youpzt.com
License: GNU GPL 2+
*/

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

$plugin_version = '1.0';
define('YOUPZT_MESSAGES_VERSION', $plugin_version);

define( 'YPM_DIR', plugin_dir_path( __FILE__ ) );
define( 'YPM_INC_DIR', trailingslashit( YPM_DIR . 'includes' ) );

define( 'YPM_URL', plugin_dir_url( __FILE__ ) );
define( 'YPM_CSS_URL', trailingslashit( YPM_URL . 'css' ) );
define( 'YPM_JS_URL', trailingslashit( YPM_URL . 'js' ) );
define( 'YPM_IMG_URL', trailingslashit( YPM_URL . 'images' ) );

include_once YPM_DIR . 'index.php';
include_once YPM_INC_DIR . 'widget.php';
include_once YPM_INC_DIR . 'inbox-page.php';
include_once YPM_INC_DIR . 'send-page.php';
include_once YPM_INC_DIR . 'outbox-page.php';

if ( is_admin() )
{
	include_once YPM_INC_DIR . 'options.php';
}

add_action( 'admin_notices', 'youpzt_messages_notify' );
add_action( 'admin_bar_menu', 'youpzt_messages_adminbar', 300 );
add_action( 'wp_ajax_youpzt_messages_get_users', 'youpzt_messages_get_users' );

/**
 * Show notification of new PM
 */
function youpzt_messages_notify()
{
	global $wpdb, $current_user;

	// get number of unread messages
	$num_unread = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->youpzt_messages.' WHERE `recipient` = "' . $current_user->user_login . '" AND `read` = 0 AND `deleted` != "2"' );

	if ( !$num_unread )
		return;

	printf(
		'<div id="message" class="error"><p><b>%s</b> <a href="%s">%s</a></p></div>',
		sprintf( _n( '您有 %d 条新信息!', '您有 %d 条新信息!', $num_unread, 'youpzt' ), $num_unread ),
		admin_url( 'admin.php?page=youpzt_messages_inbox' ),
		__( '点击这里进入收件箱', 'youpzt' )
	);
}

/**
 * Show number of unread messages in admin bar
 */
function youpzt_messages_adminbar()
{
	global $wp_admin_bar;
	global $wpdb, $current_user;

	// get number of unread messages
	$num_unread = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->youpzt_messages.' WHERE `recipient` = "' . $current_user->user_login . '" AND `read` = 0 AND `deleted` != "2"' );

	if ( $num_unread && is_admin_bar_showing() )
	{
		$wp_admin_bar->add_menu( array(
			'id'    => 'ypm',
			'title' => sprintf( _n( '您有 %d 条新信息!', '您有 %d 条新信息!', $num_unread, 'youpzt' ), $num_unread ),
			'href'  => admin_url( 'admin.php?page=youpzt_messages_inbox' ),
			'meta'  => array( 'class' => "youpzt_messages_newmessages" ),
		) );
	}
}

/**
 * Ajax callback function to get list of users
 */
function youpzt_messages_get_users()
{
	$keyword = trim( strip_tags( $_POST['term'] ) );
	$values = array();
	$args = array( 'search' => '*' . $keyword . '*',
	               'fields' => 'all_with_meta' );
	$results_search_users = get_users( $args );
	$results_search_users = apply_filters( 'youpzt_messages_recipients', $results_search_users );
	if ( !empty( $results_search_users ) )
	{
		foreach ( $results_search_users as $result )
		{
			$values[] = $result->display_name;
		}
	}
	die( json_encode( $values ) );
}
