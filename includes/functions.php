<?php 

/**
 * 向某个用户发送站内信
 *
 * @param int $user_id 用户id
 *return bool true/false
 */
function to_user_message($user_id,$subject,$content,$date='',$from_user=''){

	$user_id=intval($user_id);
	if ($user_id&&$subject&&$content) {
			global $wpdb,$current_user;
			if (empty($from_user)) {
				$from_user=$current_user->ID;
			}
			$date = $date ? $date : current_time('mysql');//时间
			$title = sanitize_text_field($title);
			$content = htmlspecialchars($content);//消息内容
      $insert_msg=$wpdb->insert($wpdb->youpzt_messages,array(
                                    'msg_type'=>1,//消息类型，默认1
                                    'from_user'=>$from_user,
                                    'to_user'=>$user_id,
                                    'subject'=>$subject,//默认是产品名                     
                                    'content'=>$content,//内容
                                    'date'=>$date,
                                    'read' =>0,
                                    'deleted'=>0
                            ),
                            array('%d','%d','%d','%s','%s','%s','%d','%d'));
      return $wpdb->insert_id;
	}else{
		return false;
	}
}

/**
 * 获取用户收件箱结果集
 *
 * @param int $user_id 用户id
 *return array/obj 用户收件箱结果集
 */
function get_inbox_messages($user_id){
	if ($user_id) {
		return get_youpzt_messages($user_id,'inbox');
	}else{
		return false;
	}
}

/**
 * 获取用户发件箱结果集
 *
 * @param int $user_id 用户id
 *return array/obj 用户收件箱结果集
 */
function get_outbox_messages($user_id){
	if ($user_id) {
		return get_youpzt_messages($user_id,'outbox');
	}else{
		return false;
	}
}

/**
 * 获取用户发件箱结果集
 *
 * @param int $user_id 用户id
 *return array/obj 用户收件箱结果集
 */
function get_youpzt_messages($user_id,$type='inbox'){
	if ($user_id) {
		global $wpdb;
		if ($type=='inbox') {
				$msgs = $wpdb->get_results('SELECT * FROM '.$wpdb->youpzt_messages.' WHERE `to_user` = "' .$user_id. '" AND `deleted` != "2" ORDER BY `date` DESC',ARRAY_A);
		}elseif ($type=='outbox') {
				$msgs = $wpdb->get_results('SELECT * FROM '.$wpdb->youpzt_messages.' WHERE `from_user` = "' .$user_id. '" AND `deleted` != "2" ORDER BY `date` DESC',ARRAY_A);
		}
		return $msgs;
	}else{
		return false;
	}
}


/**
 * 获取用户未读消息
 *
 * @param int $user_id 用户id
 *return int 未读消息数量
 */
function get_messages_noread_count($user_id){
	if ($user_id) {
		return get_messages_count($user_id,0);
	}else{
		return false;
	}
}

/**
 * 获取用户已读消息
 *
 * @param int $user_id 用户id
 *return int 已读消息数量
 */
function get_messages_read_count($user_id){
	if ($user_id) {
		return get_messages_count($user_id,1);
	}else{
		return false;
	}
}

/**
 * 获取用户总消息数量
 *
 * @param int $user_id 用户id
 *return int 总消息数量
 */
function get_messages_all_count($user_id){
	if ($user_id) {
		return get_messages_count($user_id,-1);
	}else{
		return false;
	}
}

//获取用户的消息总数量，0未读，1已读，-1不限制
function get_messages_count($user_id,$read=0){
		if ($user_id) {
			global $wpdb;
			if ($read==0) {//未读
				 $count=$wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $wpdb->youpzt_messages where msg_type=%d and to_user=%d and read=%d and deleted<>%d;",1,$user_id,0,2));//查询结果数量
			}elseif ($read==1) {//已读
				$count=$wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $wpdb->youpzt_messages where msg_type=%d and to_user=%d and read=%d and deleted<>%d;",1,$user_id,1,2));//查询结果数量
			}elseif($read==-1){//获取总的数量
				$count=$wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $wpdb->youpzt_messages where msg_type=%d and to_user=%d and deleted<>%d;",1,$user_id,2));//查询结果数量
			}
			return $count;
		}else{
			return false;
		}
}
?>