<?php
/**
 * Adding widget
 */
add_action( 'widgets_init', create_function( '', 'return register_widget("youpzt_Widget");' ) );

/**
 * youpzt Widget Class
 */
class youpzt_Widget extends WP_Widget
{

	/**
	 * Constructor
	 */
	function youpzt_Widget()
	{
		$widget_options = array( 'description' => __( '在侧边栏（sidebar）上显示新信息提示', 'youpzt' ) );
		$control_options = array();
		parent::WP_Widget( 'youpzt-widget', __( '站内信Widget', 'youpzt' ), $widget_options, $control_options );
	}

	/**
	 * Display widget
	 */
	function widget( $args, $instance )
	{
		global $wpdb, $current_user;

		if ( !is_user_logged_in() )
		{
			return;
		}

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;

		if ( $title )
		{
			echo $before_title . $title . $after_title;
		}

		// get number of PM
		$num_pm = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->youpzt_messages.' WHERE `recipient` = "' . $current_user->user_login . '" AND `deleted` != "2"' );
		$num_unread = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->youpzt_messages.' WHERE `recipient` = "' . $current_user->user_login . '" AND `read` = 0 AND `deleted` != "2"' );

		if ( empty( $num_pm ) )
		{
			$num_pm = 0;
		}
		if ( empty( $num_unread ) )
		{
			$num_unread = 0;
		}

		echo '<p><b>', sprintf( __ngettext( '您有 %d 条站内信。其中 %d 条未读。', '您有 %d 条站内信。其中 %d 条未读。', $num_pm, 'youpzt' ), $num_pm, $num_unread ), '</b></p>';

		if ( $instance['num_pm'] )
		{
			$msgs = $wpdb->get_results( 'SELECT `id`, `sender`, `subject`, `read`, `date` FROM ' . $wpdb->youpzt_messages.' WHERE `recipient` = "' . $current_user->user_login . '" AND `deleted` != "2" ORDER BY `date` DESC LIMIT ' . $instance['num_pm'] );
			if ( count( $msgs ) )
			{
				echo '<ol>';
				foreach ( $msgs as $msg )
				{
					$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->sender'" );
					echo '<li>';
					if ( !$msg->read )
					{
						echo '<b>';
					}
					echo $msg->subject;
					if ( !$msg->read )
					{
						echo '</b>';
					}
					printf( __( '<br />by <b>%s</b><br />at %s', 'youpzt' ), $msg->sender, $msg->date );
					echo '</li>';
				}
				echo '</ol>';
			}
		}

		echo '<p><a href="', get_bloginfo( 'wpurl' ), '/wp-admin/admin.php?page=youpzt_messages_inbox">', __( 'Click here to go to inbox', 'youpzt' ), ' &raquo;</a></p>';

		echo $after_widget;
	}

	/**
	 * Update widget
	 */
	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['num_pm'] = intval( $new_instance['num_pm'] );
		return $instance;
	}

	function form( $instance )
	{

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( '站内信', 'youpzt' ), 'num_pm' => 5 );
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'num_pm' ); ?>"><?php _e( '消息数量:', 'youpzt' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'num_pm' ); ?>" name="<?php echo $this->get_field_name( 'num_pm' ); ?>" value="<?php echo $instance['num_pm']; ?>" style="width:100%;" />
	</p>
	<?php

	}
}
