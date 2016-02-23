<?php 
function menu_youpzt_message_update(){
  global $menu,$submenu;
  	 $check_obj=check_youpzt_plugins_messages();
	 $check_version=$check_obj->version;

  if (current_user_can( 'manage_options')&&$check_version!=YOUPZT_MESSAGES_VERSION) {
    $submenu['youpzt_messages_inbox'][0][0].= ' <span class="update-plugins update-youpzt-messages"><span class="update-count">新</span></span>';
  }
  
}
add_action( 'admin_head','menu_youpzt_message_update');
//提示信息
function youpzt_messages_showMessage($message, $errormsg = false)
{
	if ($errormsg) {
		echo '<div id="message" class="error">';
	}else{
		echo '<div id="message" class="updated fade">';
	}
	echo "$message</div>";
}
//检查更新
function check_youpzt_plugins_messages(){
	$check_url="http://www.youpzt.com/wp-content/update_check_youpzt_json/wp-youpzt-messages.json";
	//$check_url=base64_decode($check_url);
	$check_content=geturl_content($check_url);
	$check_obj=json_decode($check_content);
	return $check_obj;
}
//check update version 
function ypzt_messages_showAdminMessages()
{
	 $check_obj=check_youpzt_plugins_messages();
	 $check_version=$check_obj->version;
		if(!$check_version){
			youpzt_messages_showMessage('<p>网络连接失败，不能检查插件更新！【wp-youpzt-messages】</p>', false);
		}elseif($check_version!=YOUPZT_MESSAGES_VERSION){
			
			youpzt_messages_showMessage('<p>wp-youpzt-messages插件最新版本'.$check_version.'，请进入<a class="color-red" href="'.$check_obj->homepage.'" target="_blank" title="更新插件"><strong>详情页面</strong></a>更新版本</p>', false);
		}else{
			if(!function_exists('file_get_contents')){

				echo '如果您看到这句话，证明你的file_get_contents函数被禁用了，请开启此函数！';
			}
		}

}
add_action('admin_notices', 'ypzt_messages_showAdminMessages');//后台显示更新信息	

//获取远程内容
if ( ! function_exists( 'geturl_content' ) ) :
function geturl_content($url){
		$url = trim($url);
		$content = '';
		if (extension_loaded('curl')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$content = curl_exec($ch);
			curl_close($ch);
		} else {
			$content =@file_get_contents($url);
		}
		return trim($content);
}
endif;

//激活插件后显示自定义提示信息youpzt.com
add_action('admin_notices', 'admin_subscribe');
if (!function_exists('admin_subscribe')) {
	function admin_subscribe() {
		global $current_user;
	    $user_id = $current_user->ID;
		if(!empty($_COOKIE["subscribe_start"])){
			$subscribe_start=$_COOKIE["subscribe_start"];
		}

		if (!get_user_meta($user_id,'youpzt-subscribe')&&'off'!=$subscribe_start&&$current_user->user_level>7) {
			
	        echo '<div class="updated subscribe-main"><p>加入邮件订阅列表，获取我们最新内容推送。——<span class="text-ruo">[<a href="http://www.youpzt.com/" target="_blank">优品主题</a>]</span><i class="fr fb f20 youpzt-close">&#215;</i></p><p>
			<input type="text" name="email_subscribe" id="email_subscribe" class="youpzt-text" value="'.$current_user->user_email.'" placeholder="填写E-mail地址" /><span class="youpzt-submit-subscribe button-primary" id="subscribe-submit" site-url="'.get_option('home').'">订阅</span> <span id="subscribe_msg" class="f12 color-success"></span>
			'; 
	        echo "</p></div>";
			wp_enqueue_style('youpztsubscribe-style',YPM_CSS_URL.'youpztsubscribe.css', array(), YOUPZT_MESSAGES_VERSION);
			wp_enqueue_script( 'cookies-jquery',YPM_JS_URL.'jquery.cookie.js', array(), YOUPZT_MESSAGES_VERSION);
			wp_enqueue_script( 'youpztajax-subscribe',YPM_JS_URL.'ajax-subscribe.js', array(), YOUPZT_MESSAGES_VERSION);		
		}
	}
}

//注册站内信所需要的页面
function youpzt_messages_create_pages(){
    /*$config_store_pages=array(
            'cart'=>'购物车',
            'checkout'=>'结算',
            'my-account'=>'我的账户',
            'my-address'=>'我的地址',
            'shop'=>'商店'
        );*/
    $config_store_pages=array(
            'youpzt-messages'=>'站内信',
        );
    foreach ($config_store_pages as $key => $store_page_val) {
        $register_page = array(
             'post_title' => $store_page_val,
             'post_name'=>$key,
             'post_type' => 'page',
             'post_status' => 'publish',
             'post_author' => 1
          );
        wp_insert_post($register_page);
    }
}

?>