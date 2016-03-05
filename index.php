<?php 

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
					`from_user` int(11) NOT NULL,
  				`to_user` int(11) NOT NULL,
					`subject` text NOT NULL,
					`content` text NOT NULL,
					`date` datetime NOT NULL,
					`msg_read` tinyint(1) NOT NULL,
					`deleted` tinyint(1) NOT NULL,
					PRIMARY KEY (`id`)
				)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
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
//发送订阅请求
add_action('parse_request', 'go_subscribe', 4);
if ( ! function_exists( 'go_subscribe' )) :
	function go_subscribe($wp){
		$data_token=isset($_GET["token"])?$_GET["token"]:false;//绑定token的安全码

		if($data_token=='open_subscribe'){
			$subscribe_email=isset($_GET['email'])? $_GET['email']:false;
			$from_url=$_SERVER['HTTP_HOST'];
			$subscribe_code = array(
				"from_url"=>$from_url,
				"email"=>$subscribe_email,
				"_form_"=>"subscriptionFront"
			);
			
			echo https_post("http://www.youpzt.com?token=get_subscribe",$subscribe_code);
			exit;
		}elseif($data_token=='cancel_subscribe'){
			global $current_user;
	        $user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */

			add_user_meta($user_id, 'youpzt-subscribe', 'true', true);
			
		}	
	}
endif;
	 //通过链接post获取数据
if ( ! function_exists( 'https_post' ) ) :
function https_post($url, $data = null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
	   curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	 }
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	 $output = curl_exec($curl);
	 curl_close($curl);
	 return $output;
 }
 endif;

 /* Load author template */
function youpzt_load_message_template($template_path){
    if (is_page('youpzt-messages')){
    	$template = locate_template( array( "youpztMessages-template.php", get_stylesheet_directory(). "/youpztMessages-template.php" ) );
    	if ($template) {
    		return $template;
    	}else{
        return $template_path = YPM_DIR.'/youpztMessages-template.php';
    	}
    }else{
        return $template_path;  
    }
}
add_filter( 'template_include', 'youpzt_load_message_template', 1 );
?>