<?php 

function youpztMessages_template_func(){

global $post,$current_user;

?>
<link rel='stylesheet' id='youpztMessages'  href='<?php echo YPM_CSS_URL;?>message-style.css' type='text/css' />
<div class="hfeed content ypzt-message">
	<div class="ypzt-message-border">
	<h2><a href="<?php echo get_permalink($post->ID);?>">站内信</a></h2>
	<div class="ypzt-message-title-img"><?php echo get_avatar($current_user->ID,64);?><span><?php echo $current_user->display_name;?></span></div>
	<div class="ypzt-message-information">
		<span>未读消息：<span class="ypzt-number"><a href="javascript:(0)"><?php echo get_messages_noread_count($current_user->ID);?></a></span>条</span>
		<span>您共收到消息：<span class="ypzt-number"><a href="javascript:(0)" onclick="ypmSwitch('pm-inbox');"><?php echo get_messages_count($current_user->ID,-1);?></a></span>条</span>
	</div>
	<div class="ypzt-message-title">
	<a href="javascript:void(0);" onclick="ypmSwitch('pm-send');"><i class="iconfont">&#xe601;</i>发送</a><a href="javascript:void(0);" onclick="ypmSwitch('pm-inbox');"><i class="iconfont">&#xe603;</i>收件箱</a><a href="javascript:void(0);" onclick="ypmSwitch('pm-outbox');"><i class="iconfont">&#xe604;</i>已发送信息</a>
	</div>
	</div>
	<script type="text/javascript">
		// Switch between send page, inbox and outbox
		function ypmSwitch(page) {
			document.getElementById('pm-send').style.display = 'none';
			document.getElementById('pm-inbox').style.display = 'none';
			document.getElementById('pm-outbox').style.display = 'none';
			document.getElementById(page).style.display = '';
			return false;
		}
	</script>
	<!-- Include scripts and style for autosuggest feature -->
	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/wp-youpzt-messages/js/script.js"></script>

	<div class="post ypzt-mt-10 ypzt-message-content" id="post-<?php echo $post->ID; ?>">
		<?php
		$show = array(true, false, false);
		if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'ypm_inbox') {
			$show = array(false, true, false);
		} elseif (isset($_REQUEST['page']) && $_REQUEST['page'] == 'ypm_outbox') {
			$show = array(false, false, true);
		}
		?>
		<div id="pm-send" <?php if (!$show[0]) echo 'style="display:none"'; ?>><?php youpzt_messages_send($post->ID);?></div>
		<div id="pm-inbox" <?php if (!$show[1]) echo 'style="display:none"'; ?>><?php youpzt_messages_inbox($post->ID);?></div>
		<div id="pm-outbox" <?php if (!$show[2]) echo 'style="display:none"'; ?>><?php youpzt_messages_outbox($post->ID);?></div>
	</div>

<?php };?>