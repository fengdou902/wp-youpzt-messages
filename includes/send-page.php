<?php
/**
 * Send form page
 */
function youpzt_messages_send()
{
	global $wpdb, $current_user;
	?>
<div class="wrap">
    <h2>发送站内信</h2>
	<?php
	$option = get_option( 'youpzt_messages_option' );
	if ( $_REQUEST['page'] == 'youpzt_messages_send' && isset( $_POST['submit'] ) )
	{
		$error = false;
		$status = array();

		// Check if total pm of current user exceed limit
		$role = $current_user->roles[0];
		$sender = $current_user->ID;
		$total = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->youpzt_messages.' WHERE `from_user` = "' . $sender . '" OR `to_user` = "' . $sender . '"' );
		if (($option[$role] != 0 ) && ( $total >= $option[$role]))
		{
			$error = true;
			$status[] = __( '你已经超过了信箱的限制，请删除一些后再发送。', 'youpzt' );
		}

		// Get input fields with no html tags and all are escaped
		$subject = strip_tags( $_POST['subject'] );
		$content = $_POST['content'] ;
		$recipient = $option['type'] == 'autosuggest' ? explode( ',', $_POST['recipient'] ) : $_POST['recipient'];
		$recipient = array_map( 'strip_tags', $recipient );

		// Allow to filter content
		$content = apply_filters( 'youpzt_messages_content_send', $content );
		
		// Remove slash automatically in wp
		$subject = stripslashes($subject);
		$content = stripslashes($content);
		$recipient = array_map( 'stripslashes', $recipient );

		// Escape sql
		$subject = esc_sql( $subject );
		$content = esc_sql( $content );
		$recipient = array_map( 'esc_sql', $recipient );

		// Remove duplicate and empty recipient
		$recipient = array_unique( $recipient );
		$recipient = array_filter( $recipient );
		
		// Check input fields
		if ( empty( $recipient)){
			$error = true;
			$status[] = __( '请输入收件人用户名。', 'youpzt' );
		}
		if ( empty( $subject)){
			$error = true;
			$status[] = __( '请输入信息的标题。', 'youpzt' );
		}
		if ( empty( $content)){
			$error = true;
			$status[] = __( '请输入信息内容。', 'youpzt' );
		}

		if ( !$error){
			$numOK = $numError = 0;
			foreach ( $recipient as $rec_id){
				
				$new_message = array(
							'id'        => NULL,
							'msg_type'   =>1,
							'from_user'    => $sender,
							'to_user' => $rec_id,
							'subject'   => $subject,
							'content'   => $content,
							'date'      => current_time('mysql'),
							'read'      => 0,
							'deleted'   => 0
				);
				// insert into database
				if ( $wpdb->insert( $wpdb->youpzt_messages, $new_message, array( '%d','%d','%d', '%d', '%s', '%s', '%s', '%d', '%d' ) ) ){
					$numOK++;
					unset( $_REQUEST['recipient'], $_REQUEST['subject'], $_REQUEST['content'] );

					// send email to user
					if ( $option['email_enable'] )
					{
						$sender_name=get_userdata($sender)->display_name;
						// replace tags with values
						$tags = array( '%BLOG_NAME%', '%BLOG_ADDRESS%', '%SENDER%', '%INBOX_URL%' );
						$replacement = array( get_bloginfo( 'name' ), get_bloginfo( 'admin_email' ), $sender_name, admin_url( 'admin.php?page=youpzt_messages_inbox' ) );

						$email_name = str_replace( $tags, $replacement, $option['email_name'] );
						$email_address = str_replace( $tags, $replacement, $option['email_address'] );
						$email_subject = str_replace( $tags, $replacement, $option['email_subject'] );
						$email_body = str_replace( $tags, $replacement, $option['email_body'] );

						// set default email from name and address if missed
						if ( empty( $email_name ) )
							$email_name = get_bloginfo( 'name' );

						if ( empty( $email_address ) )
							$email_address = get_bloginfo( 'admin_email' );

						$email_subject = strip_tags( $email_subject );
						if ( get_magic_quotes_gpc() )
						{
							$email_subject = stripslashes( $email_subject );
							$email_body = stripslashes( $email_body );
						}
						$email_body = nl2br( $email_body );

						$recipient_email = $wpdb->get_var( "SELECT user_email from $wpdb->users WHERE ID='$rec_id' LIMIT 1" );
						$mailtext = "<html><head><title>$email_subject</title></head><body>$email_body</body></html>";
						if ($recipient_email) {
								// set headers to send html email
								$headers = "发送到: $recipient_email\r\n";
								$headers .= "来者: $email_name <$email_address>\r\n";
								$headers .= "MIME-Version: 1.0\r\n";
								$headers .= 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) . "\r\n";
								wp_mail( $recipient_email, $email_subject, $mailtext, $headers );
						}

					}
				}else{
					$numError++;
				}
			}

			$status[] = sprintf( _n( '%d 条信息已成功发送。', '%d 条信息已成功发送。', $numOK, 'youpzt' ), $numOK ) . ' ' . sprintf( _n( '%d 个出错.', '%d 个出错.', $numError, 'youpzt' ), $numError );
		}

		echo '<div id="message" class="updated fade"><p>', implode( '</p><p>', $status ), '</p></div>';
	}
	?>
	<?php do_action( 'youpzt_messages_before_form_send' ); ?>
    <form method="post" action="" id="send-form" enctype="multipart/form-data">
	    <input type="hidden" name="page" value="youpzt_messages_send" />
        <table class="form-table">
            <tr>
                <th><?php _e( '接收者', 'youpzt' ); ?></th>
                <td>
					<?php
					// if message is not sent (by errors) or in case of replying, all input are saved

					$recipient = !empty( $_POST['recipient'] ) ? $_POST['recipient'] : ( !empty( $_GET['recipient'] )
						? $_GET['recipient'] : '' );

					// strip slashes if needed
					$subject = isset( $_REQUEST['subject'] ) ? ( get_magic_quotes_gpc() ? stripcslashes( $_REQUEST['subject'] )
						: $_REQUEST['subject'] ) : '';
					$subject = urldecode( $subject );  // for some chars like '?' when reply

					if ( empty( $_GET['id'] ) )
					{
						$content = isset( $_REQUEST['content'] ) ?  $_REQUEST['content']  : '';
					}
					else
					{
						$id = $_GET['id'];
						$msg = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->youpzt_messages.' WHERE `id` = "' . $id . '" LIMIT 1' );

						$content = '<p>&nbsp;</p>';
						$content .= '<p>---</p>';
						$content .= '<p><em>' . __( '时间: ', 'youpzt' ) . $msg->date . "\t" . $msg->sender . __( ' Wrote:', 'youpzt' ) . '</em></p>';
						$content .= wpautop( $msg->content );
						$content  = stripslashes( $content );
					}
					// if auto suggest feature is turned on
					if ( $option['type'] == 'autosuggest' )
					{
						?>
               <input id="recipient" type="text" name="recipient" class="large-text" />
						<?php

					}
					else // classic way: select recipient from dropdown list
					{
						// Get all users of blog
						$args = array(
							'order'   => 'ASC',
							'orderby' => 'display_name' );
						$values = get_users( $args );
						$values = apply_filters( 'youpzt_messages_recipients', $values );
						?>
						<select name="recipient[]" multiple="multiple" size="5">
							<?php
							foreach ( $values as $value )
							{
								$selected = ( $value->ID == $recipient ) ? ' selected="selected"' : '';
								echo "<option value='$value->ID'$selected>$value->display_name</option>";
							}
							?>
						</select>
						<?php
					}
					?>
                </td>
            </tr>
            <tr>
                <th><?php _e( '主题', 'youpzt' ); ?></th>
                <td><input type="text" name="subject" value="<?php echo $subject; ?>" class="large-text" /></td>
            </tr>
            <tr>
                <th><?php _e( '内容', 'youpzt' ); ?></th>
                <th><?php  wp_editor( $content, 'rw-text-editor', $settings = array( 'textarea_name' => 'content' ) );?></th>
            </tr>
	        <?php do_action( 'youpzt_messages_form_send' ); ?>
        </table>
	    <p class="submit"><input type="submit" value="发送" class="button-primary" id="submit" name="submit"></p>
    </form>
	<?php do_action( 'youpzt_messages_after_form_send' ); ?>
</div>
<?php

}