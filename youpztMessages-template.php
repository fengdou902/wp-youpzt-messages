<?php
/**
 * @package wp-youpzt-messages
 *
 * @author: youpzt
 * @url: http://www.youpzt.com
 * @email: 981248356@qq.com
 
 Template Name: 站内信模板
 
 */
 ?>

<?php
if (!is_user_logged_in()) {
	redirect_to_login_url();
}

get_header();
?>
<style type="text/css">
	@font-face {
  font-family: 'iconfont';
  src: url('//at.alicdn.com/t/font_1456106752_3732517.eot'); /* IE9*/
  src: url('//at.alicdn.com/t/font_1456106752_3732517.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
  url('//at.alicdn.com/t/font_1456106752_3732517.woff') format('woff'), /* chrome、firefox */
  url('//at.alicdn.com/t/font_1456106752_3732517.ttf') format('truetype'), /* chrome、firefox、opera、Safari, Android, iOS 4.2+*/
  url('//at.alicdn.com/t/font_1456106752_3732517.svg#iconfont') format('svg'); /* iOS 4.1- */
}
 
.iconfont{font-family:"iconfont";
font-size:20px;font-style:normal;margin-right:2px;}                   
	.ypzt-message{padding:20px;width: 60%;margin:40px auto;border: 1px solid #ccc;border-radius: 5px;box-shadow: 2px 2px 2px #dcdcdc;color: #666;}
	.ypzt-message a{color: #666;}
	.ypzt-message td{border: none;}
	.ypzt-message-title a{margin-right: 20px;padding: 10px;border: 1px solid;border-radius:5px;text-decoration: none;color: #6F6F6F}
	.ypzt-message-title-img{display: inline-block;margin-right: 10px;margin-bottom: 30px;margin-top: 10px;}
	.ypzt-message-title-img img{width:60px;border-radius:50%;}
	.ypzt-message-title-img span{margin-left: 20px;font-size: 25px;}
	.ypzt-mt-10{margin-top: 10px;}
	.ypzt-message-content .form-table select{width: 100%;padding:5px;border-radius: 5px;border: 1px solid #ccc;}
	.ypzt-message-content select{padding:5px;border-radius: 5px;border: 1px solid #ccc;}
	.ypzt-message-content h2{margin:20px 0;font-size: 20px;}
	.ypzt-message-content input{border: 1px solid #ccc;border-radius: 4px;margin-top: 10px;margin-bottom: 13px;}
	
	.ypzt-message-content #insert-media-button{margin-right: 15px;}
	.ypzt-message-border{border-bottom:1px solid #ececec;padding-bottom: 35px;text-align: center;}
	.ypzt-message-information{margin-bottom: 30px;}
	.ypzt-number{font-size: 25px;margin-right: 5px;}
	.ypzt-message-border h2{border-bottom: 1px solid #ECECEC;margin-top: 0px;padding-bottom: 20px;}
	.ypzt-message-subject{width: 100%;}
	.yzpt-content-td{width: 45%;padding-right: 5%}
	.yzpt-content-td .yzpt-content-td-title{font-size: 16px;font-weight:800;}
	@media screen and (max-width: 768px) {
    .ypzt-message{
       width: 100%
    }
}
</style>
<div class="hfeed content ypzt-message">
	<div class="ypzt-message-border">
	<h2><?php the_title(); ?></h2>
	<div class="ypzt-message-title-img"><img src="http://img4q.duitang.com/uploads/item/201410/01/20141001113442_mXNBy.jpeg"><span>admin</span></div>
	<div class="ypzt-message-information">
		<span>未读消息：<span class="ypzt-number"><a href="javascript:(0)">10</a></span>条</span>
		<span>您共收到消息：<span class="ypzt-number"><a href="javascript:(0)" onclick="pmSwitch('pm-inbox');">10</a></span>条</span>
	</div>
	<div class="ypzt-message-title">
	<a href="javascript:void(0);" onclick="pmSwitch('pm-send');"><i class="iconfont">&#xe601;</i>发送</a><a href="javascript:void(0);" onclick="pmSwitch('pm-inbox');"><i class="iconfont">&#xe603;</i>收件箱</a><a href="javascript:void(0);" onclick="pmSwitch('pm-outbox');"><i class="iconfont">&#xe604;</i>已发送信息</a>
	</div>
	</div>
	<script type="text/javascript">
		// Switch between send page, inbox and outbox
		function pmSwitch(page) {
			document.getElementById('pm-send').style.display = 'none';
			document.getElementById('pm-inbox').style.display = 'none';
			document.getElementById('pm-outbox').style.display = 'none';
			document.getElementById(page).style.display = '';
			return false;
		}
	</script>
	<!-- Include scripts and style for autosuggest feature -->
	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/wp-youpzt-messages/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/wp-youpzt-messages/js/jquery.autoSuggest.packed.js"></script>
	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/wp-youpzt-messages/js/script.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wp-youpzt-messages/css/style.css" />
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="post ypzt-mt-10 ypzt-message-content" id="post-<?php the_ID(); ?>">
		<?php
		$show = array(true, false, false);
		if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'ypm_inbox') {
			$show = array(false, true, false);
		} elseif (isset($_REQUEST['page']) && $_REQUEST['page'] == 'ypm_outbox') {
			$show = array(false, false, true);
		}
		?>
		<div id="pm-send" <?php if (!$show[0]) echo 'style="display:none"'; ?>><?php youpzt_messages_send();?></div>
		<div id="pm-inbox" <?php if (!$show[1]) echo 'style="display:none"'; ?>><?php youpzt_messages_inbox();?></div>
		<div id="pm-outbox" <?php if (!$show[2]) echo 'style="display:none"'; ?>><?php youpzt_messages_outbox();?></div>
	</div>
	<?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
