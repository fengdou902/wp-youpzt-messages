<?php
/**
 * Outbox page
 */
function youpzt_messages_outbox()
{
    global $wpdb, $current_user;

    // if view message
    if (isset($_GET['action']) && 'view' == $_GET['action'] && !empty($_GET['id'])) {
        $id = $_GET['id'];

        check_admin_referer("ypm-view_outbox_msg_$id");

        // select message information
        $msg = $wpdb->get_row('SELECT * FROM ' . $wpdb->youpzt_messages.' WHERE `id` = "' . $id . '" LIMIT 1');
       //$msg->recipient = $wpdb->get_var("SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->recipient'");
        $recipient_name=get_userdata($msg->to_user)->display_name;
        ?>
    <div class="wrap">
        <h2><?php _e('已发信息', 'youpzt'); ?></h2>

        <p><a href="?page=youpzt_messages_outbox"><?php _e('返回已发信息', 'youpzt'); ?></a></p>
        <table class="widefat fixed" cellspacing="0">
            <thead>
            <tr>
                <th class="manage-column" width="20%"><?php _e('资讯', 'youpzt'); ?></th>
                <th class="manage-column"><?php _e('信息', 'youpzt'); ?></th>
                <th class="manage-column" width="15%"><?php _e('动作', 'youpzt'); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php printf(__('<b>接收者</b>: %s<br /><b>Date</b>: %s', 'youpzt'),$recipient_name, $msg->date); ?></td>
                <td><?php printf(__('<p><b>主题</b>: %s</p><p>%s</p>', 'youpzt'), stripcslashes($msg->subject), nl2br(stripcslashes($msg->content))); ?></td>
                <td>
						<span class="delete">
							<a class="delete"
                               href="<?php echo wp_nonce_url("?page=youpzt_messages_outbox&action=delete&id=$msg->id", 'ypm-delete_outbox_msg_' . $msg->id); ?>"><?php _e('删除', 'youpzt'); ?></a>
						</span>
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <th class="manage-column" width="20%"><?php _e('资讯', 'youpzt'); ?></th>
                <th class="manage-column"><?php _e('信息', 'youpzt'); ?></th>
                <th class="manage-column" width="15%"><?php _e('动作', 'youpzt'); ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php
        // don't need to do more!
        return;
    }

    // if delete message
    if (isset($_GET['action']) && 'delete' == $_GET['action'] && !empty($_GET['id'])) {
        $id = $_GET['id'];

        if (!is_array($id)) {
            check_admin_referer("ypm-delete_outbox_msg_$id");
            $id = array($id);
        } else {
            check_admin_referer("ypm-bulk-action_outbox");
        }
        $error = false;
        foreach ($id as $msg_id) {
            // check if the recipient has deleted this message
            $recipient_deleted = $wpdb->get_var('SELECT `deleted` FROM ' . $wpdb->youpzt_messages.' WHERE `id` = "' . $msg_id . '" LIMIT 1');
            // create corresponding query for deleting message
            if ($recipient_deleted == 2) {
                $query = 'DELETE from ' . $wpdb->youpzt_messages.' WHERE `id` = "' . $msg_id . '"';
            } else {
                $query = 'UPDATE ' . $wpdb->youpzt_messages.' SET `deleted` = "1" WHERE `id` = "' . $msg_id . '"';
            }

            if (!$wpdb->query($query)) {
                $error = true;
            }
        }
        if ($error) {
            $status = __('错误，请再次尝试', 'youpzt');
        } else {
            $status = _n('消息已删除', '消息已删除', count($id), 'youpzt');
        }
    }

    // show all messages
    $msgs = $wpdb->get_results('SELECT * FROM ' . $wpdb->youpzt_messages.' WHERE `from_user` = "' . $current_user->ID . '" AND `deleted` != 1 ORDER BY `date` DESC');
    ?>
<div class="wrap">
    <h2><?php _e('已发信息', 'youpzt'); ?><a href="<?php echo admin_url().'admin.php?page=youpzt_messages_send';?>" class="page-title-action">发送</a></h2>
    <?php
    if (!empty($status)) {
        echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
    }
    if (empty($msgs)) {
        echo '<p>', __('没有已发信息。', 'youpzt'), '</p>';
    } else {
        $n = count($msgs);
        echo '<p>', sprintf(_n('您发送了 %d 条站内信.', '您发送了 %d 条站内信.', $n, 'youpzt'), $n), '</p>';
        ?>
        <form action="" method="get">
            <?php wp_nonce_field('ypm-bulk-action_outbox'); ?>
            <input type="hidden" name="action" value="delete"/> <input type="hidden" name="page" value="youpzt_messages_outbox"/>

            <div class="tablenav">
                <input type="submit" class="button-secondary" value="<?php _e('删除选中的项', 'youpzt'); ?>"/>
            </div>

            <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr>
                    <td class="manage-column check-column"><input type="checkbox"/></td>
                    <th class="manage-column" width="10%"><?php _e('接收者', 'youpzt'); ?></th>
                    <th class="manage-column"><?php _e('主题', 'youpzt'); ?></th>
                    <th class="manage-column"><?php _e('对方已阅读', 'youpzt'); ?></th>
                    <th class="manage-column" width="20%"><?php _e('时间', 'youpzt'); ?></th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($msgs as $msg) {
                        $recipient_name=get_userdata($msg->to_user)->display_name;
                        ?>
                    <tr>
                        <th class="check-column"><input type="checkbox" name="id[]" value="<?php echo $msg->id; ?>"/>
                        </th>
                        <td><a href=""><?php echo get_avatar($msg->to_user,32);echo $recipient_name; ?></a></td>
                        <td class="yzpt-content-td">
                            <?php
                            echo '<a class="yzpt-content-td-title" href="', wp_nonce_url("?page=youpzt_messages_outbox&action=view&id=$msg->id", 'ypm-view_outbox_msg_' . $msg->id), '">', stripcslashes($msg->subject), '</a>';
                            ?>
                            <div class="row-actions">
							<span>
								<a href="<?php echo wp_nonce_url("?page=youpzt_messages_outbox&action=view&id=$msg->id", 'ypm-view_outbox_msg_' . $msg->id); ?>"><?php _e('查看', 'youpzt'); ?></a>
							</span>
							<span class="delete">
								| <a class="delete"
                                     href="<?php echo wp_nonce_url("?page=youpzt_messages_outbox&action=delete&id=$msg->id", 'ypm-delete_outbox_msg_' . $msg->id); ?>"><?php _e('删除', 'youpzt'); ?></a>
							</span>
                            </div>
                        </td>
                        <td><?php if ($msg->read==1) {echo '是';}elseif($msg->read==0){echo '<span class="noread" style="color:#10b68c;">否</span>';}else{echo '未知';};?></td>
                        <td><?php echo $msg->date; ?></td>
                    </tr>
                        <?php

                    }
                    ?>
                </tbody>
                <tfoot>
                <tr>
                    <th class="manage-column check-column"><input type="checkbox"/></th>
                    <th class="manage-column"><?php _e('接收者', 'youpzt'); ?></th>
                    <th class="manage-column"><?php _e('主题', 'youpzt'); ?></th>
                    <th class="manage-column"><?php _e('对方已阅读', 'youpzt'); ?></th>
                    <th class="manage-column"><?php _e('时间', 'youpzt'); ?></th>
                </tr>
                </tfoot>
            </table>
        </form>
        <?php

    }
    ?>
</div>
<?php
}
?>
