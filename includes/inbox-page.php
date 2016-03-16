<?php
/**
 * Inbox page
 */
function youpzt_messages_inbox($page_id=false)
{
	global $wpdb, $current_user;
	if ($page_id) {
		$page_url=get_permalink($page_id);
	}else{
		$page_url='?page=youpzt_messages_inbox';
	}
// if view message
	if ( isset( $_GET['action'] ) && 'view' == $_GET['action'] && !empty( $_GET['id'] ) )
	{
		$id = $_GET['id'];

		check_admin_referer( "ypm-view_inbox_msg_$id" );

		// mark message as msg_msg_read
		$wpdb->update( $wpdb->youpzt_messages, array( 'msg_msg_read' => 1 ), array( 'id' => $id ) );

		// select message information
		$msg = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->youpzt_messages.' WHERE `id` = "' . $id . '" LIMIT 1' );
		$sender_name=get_userdata($msg->from_user)->display_name;
		//$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->from_user'" );
		?>
	<div class="wrap">
		<h2><?php _e( '收件箱', 'youpzt' ); ?></h2>

		<p><a href="?page=youpzt_messages_inbox"><?php _e( '返回收件箱', 'youpzt' ); ?></a></p>
		<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th class="manage-column" width="20%"><?php _e( '资讯', 'youpzt' ); ?></th>
					<th class="manage-column"><?php _e( '信息', 'youpzt' ); ?></th>
					<th class="manage-column" width="15%"><?php _e( '动作', 'youpzt' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td><?php printf( __( '<b>发件人</b>: %s<br /><b>时间</b>: %s', 'youpzt' ), $sender_name, $msg->date ); ?></td>
				<td><?php printf( __( '<p><b>主题</b>: %s</p><p>%s</p>', 'youpzt' ), stripcslashes( $msg->subject ) , nl2br(stripcslashes($msg->content))); ?></td>
				<td>
						<span class="delete">
							<a class="delete"
								href="<?php echo wp_nonce_url( "$page_url&action=delete&id=$msg->id", 'ypm-delete_inbox_msg_' . $msg->id ); ?>"><?php _e( '删除', 'youpzt' ); ?></a>
						</span>
						<span class="reply">
							| <a class="reply"
							href="<?php echo wp_nonce_url( "?page=youpzt_messages_send&recipient=$sender_name&id=$msg->id&subject=Re: " . stripcslashes( $msg->subject ), 'ypm-reply_inbox_msg_' . $msg->id ); ?>"><?php _e( '回复', 'youpzt' ); ?></a>
						</span>
				</td>
			</tr>
			</tbody>
			<tfoot>
			<tr>
				<th class="manage-column" width="20%"><?php _e( '资讯', 'youpzt' ); ?></th>
				<th class="manage-column"><?php _e( '消息', 'youpzt' ); ?></th>
				<th class="manage-column" width="15%"><?php _e( '动作', 'youpzt' ); ?></th>
			</tr>
			</tfoot>
		</table>
	</div>
	<?php
// don't need to do more!
		return;
	}

$status=deal_inbox_request_data();//过滤数据

	// show all messages which have not been deleted by this user (deleted status != 2)
	$msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->youpzt_messages.' WHERE `to_user` = "' . $current_user->ID . '" AND `deleted` != "2" ORDER BY `date` DESC' );
	?>
<div class="wrap">
	<h2><?php _e( '收件箱', 'youpzt' ); ?><?php if(is_admin()){?><a href="<?php echo admin_url().'admin.php?page=youpzt_messages_send';?>" class="page-title-action">发送</a><?php };?></h2>
	<?php
	if (!empty( $status)){
		echo '<div id="message" class="updated fade"><p>',$status,'</p></div>';
	}
	if ( empty($msgs)){
		echo '<p>', __( '收件箱中没有信息。', 'youpzt' ), '</p>';
	}else{
		$n = count( $msgs );
		$num_unmsg_read = 0;
		foreach ( $msgs as $msg )
		{
			if ( !( $msg->msg_read ) )
			{
				$num_unmsg_read++;
			}
		}
		echo '<p>', sprintf( _n( '您有 %d 条站内信 （%d 条未读）.', '您有 %d 条站内信 （%d 条未读）.', $n, 'youpzt' ), $n, $num_unmsg_read ), '</p>';
		?>
		<form action="" method="get">
			<?php wp_nonce_field( 'ypm-bulk-action_inbox' ); ?>
			<input type="hidden" name="page" value="youpzt_messages_inbox" />

			<div class="tablenav">
				<select name="action">
					<option value="-1" selected="selected"><?php _e( '批量操作', 'youpzt' ); ?></option>
					<option value="delete"><?php _e( '删除', 'youpzt' ); ?></option>
					<option value="mar"><?php _e( '标记为已读', 'youpzt' ); ?></option>
				</select> <input type="submit" class="button-secondary" value="<?php _e( '确定', 'youpzt' ); ?>" />
			</div>

			<table class="wp-list-table widefat fixed striped messages" cellspacing="0">
				<thead>
					<tr>
						<td class="manage-column check-column"><input type="checkbox" /></td>
						<th class="manage-column" width="10%"><?php _e( '发件人', 'youpzt' );?></th>
						<th class="manage-column"><?php _e( '主题', 'youpzt' ); ?></th>
						<th class="manage-column"><?php _e( '状态', 'youpzt' ); ?></th>
						<th class="manage-column" width="20%"><?php _e( '日期', 'youpzt' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $msgs as $msg )
					{
						//$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->sender'" );
						$sender_name=get_userdata($msg->from_user)->display_name;
						?>
					<tr>
						<th class="check-column"><input type="checkbox" name="id[]" value="<?php echo $msg->id; ?>" />
						</th>
						
						<td><a href="#"><?php echo get_avatar($msg->from_user,32);echo $sender_name; ?></a></td>
						
						<td class="yzpt-content-td">
						
							<?php
							if ( $msg->msg_read ){
								$isStrong_subject=stripcslashes($msg->subject);
							}else{
									$isStrong_subject='<strong>'.stripcslashes( $msg->subject ).'</strong>';
							}
							echo '<a href="', wp_nonce_url( "$page_url&action=view&id=$msg->id", 'ypm-view_inbox_msg_' . $msg->id ), '">'.$isStrong_subject.'</a>';
							?>
							<div class="row-actions">
							<span>
								<a href="<?php echo wp_nonce_url( "$page_url&action=view&id=$msg->id", 'ypm-view_inbox_msg_' . $msg->id ); ?>"><?php _e( '查看', 'youpzt' ); ?></a>
							</span>
								<?php
								if ( !($msg->msg_read))
								{
									?>
									<span>
								| <a href="<?php echo wp_nonce_url( "$page_url&action=mar&id=$msg->id", 'ypm-mar_inbox_msg_' . $msg->id ); ?>"><?php _e( '标记为已读', 'youpzt' ); ?></a>
							</span>
									<?php
								}
								?>
								<span class="delete">
								| <a class="delete"
									href="<?php echo wp_nonce_url( "$page_url&action=delete&id=$msg->id", 'ypm-delete_inbox_msg_' . $msg->id ); ?>"><?php _e( '删除', 'youpzt' ); ?></a>
							</span>
							<span class="reply">
								| <a class="reply"
								href="<?php echo wp_nonce_url( "$page_url&recipient=$sender_name&id=$msg->id&subject=Re: " . stripcslashes( $msg->subject ), 'ypm-reply_inbox_msg_' . $msg->id ); ?>"><?php _e( '回复', 'youpzt' ); ?></a>
							</span>
							</div>
						</td>
						<td><?php if ($msg->msg_read==1) {echo '已读';}elseif($msg->msg_read==0){echo '<span class="nomsg_read" style="color:#10b68c;">未读？</span>';}else{echo '未知';};?></td>
						<td><?php echo $msg->date; ?></td>
					</tr>
						<?php

					}
					?>
				</tbody>
				<tfoot>
				<tr>
					<td class="manage-column check-column"><input type="checkbox" /></td>
					<th class="manage-column"><?php _e( '发件人', 'youpzt' ); ?></th>
					<th class="manage-column"><?php _e( '主题', 'youpzt' ); ?></th>
					<th class="manage-column"><?php _e( '状态', 'youpzt' ); ?></th>
					<th class="manage-column"><?php _e( '日期', 'youpzt' ); ?></th>
				</tr>
				</tfoot>
			</table>
		</form>
		<?php
	}
	?>
</div>
<?php };

function deal_inbox_request_data(){
			global $wpdb, $current_user;
		// if mark messages as msg_read
	if ( isset( $_GET['action'] ) && 'mar' == $_GET['action'] && !empty( $_GET['id'] ) )
	{
		$id = $_GET['id'];

		if ( !is_array( $id ) )
		{
			check_admin_referer( "ypm-mar_inbox_msg_$id" );
			$id = array( $id );
		}
		else
		{
			check_admin_referer( "ypm-bulk-action_inbox" );
		}
		$n = count( $id );
		$id = implode( ',', $id );
		if ( $wpdb->query( 'UPDATE ' . $wpdb->youpzt_messages.' SET `msg_read` = "1" WHERE `id` IN (' . $id . ')' ) )
		{
			$status = _n( '条信息已标记为已读。', '条信息已标记为已读。', $n, 'youpzt' );
		}else{
			$status = __( '错误，请再次尝试', 'youpzt' );
		}
	}

	// if delete message
	if ( isset( $_GET['action'] ) && 'delete' == $_GET['action'] && !empty( $_GET['id'] ) )
	{
		$id = $_GET['id'];

		if ( !is_array( $id ) )
		{
			check_admin_referer( "ypm-delete_inbox_msg_$id" );
			$id = array( $id );
		}else{
			check_admin_referer( "ypm-bulk-action_inbox" );
		}

		$error = false;
		foreach ( $id as $msg_id )
		{
			// check if the sender has deleted this message
			$sender_deleted = $wpdb->get_var( 'SELECT `deleted` FROM ' . $wpdb->youpzt_messages.' WHERE `id` = "' . $msg_id . '" LIMIT 1' );

			// create corresponding query for deleting message
			if ( $sender_deleted == 1 )
			{
				$query = 'DELETE from ' . $wpdb->youpzt_messages.' WHERE `id` = "' . $msg_id . '"';
			}else{
				$query = 'UPDATE ' . $wpdb->youpzt_messages.' SET `deleted` = "2" WHERE `id` = "' . $msg_id . '"';
			}

			if ( !$wpdb->query( $query ) )
			{
				$error = true;
			}
		}
		if ( $error )
		{
			$status = __( '错误，请再次尝试', 'youpzt' );
		}else{
			$status = _n( '消息已删除', '消息已删除', count( $id ), 'youpzt' );
		}
	}

	return $status;
}

?>

