##WordPress站内信插件wp-youpzt-messages
* 作者:youpzt  
* 官网: http://www.youpzt.com  
* WordPress最低版本: 3.0.1  
* WordPress最高版本: 4.4  
* License: GPLv2 or later  
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

这个WordPress站内信插件允许你的WordPress网站用户之间可进行站内信互相通信
***
### 描述
[项目主页](http://www.youpzt.com/wp-youpzt-messages) 
*这个WordPress站内信插件允许你的WordPress网站用户之间可进行站内信互相通信*，用户有自己的收件箱和发件箱，超级管理员拥有所有用户组(Administrator, Editor, Author, Constributor and Subscriber)的站内信操作权限。

* 同时可以进行站内信发送，同时让用户接收邮件通知。
* 提供前台站内信模板，让用户自己在前台进行管理。
* 超级管理员可进行所有站内信管理和查看（但谨慎操作）。

### 相关
* 如果你希望保持最新的更新，可以通过github获取当前最新的版本（完整版）。
* 官网提供了稳定版本的下载：http://www.youpzt.com/wp-youpzt-messages
* wp-youpzt-messages插件为了做到更好更方便使用，感谢这些免费项目（排名不分先后）  
	1， Private Messages For WordPress
	2， Front End PM

### 常见问题

* 如何设置允许指定的用户发送站内信?
你可以设置指定的用户角色和成员: Administrator（管理员）, Editor（编辑）, Author（作者）, Contributor（投稿者）, Subscriber（订阅者）。选项配置在插件的选项页面(`站内信` > `设置`)

* 如何删除我的旧的站内信?

  进入『收件箱』或者『已发送』点击删除按钮可以删除对应的站内信， 你也能点击下拉框进行批量操作删除站内信。

* 我如何给发送者进行回复?

  进入『收件箱』点击消息的回复按钮。

* 如果超过了发件限制，我该如何操作?

  你可以删除旧的站内信(see previous question) or ask admin to increase the mailbox quota.

* 如何进行使用前台站内信模板？

1. 复制插件下的 `youpztMessages-template.php` 到你的主题目录下
2. 在wordpress后台创建一个页面，然后选择站内信模板
3. 打开页面检测是否成功。

提示: 模板文件是主干，你可以针对当前使用主题进行模板的自定义和美化。

### 常用API
* 如果插件提供的如消息模板不能满足你的前台排版需求，我们内置了如下的API方便二次开发页面模板

1, 获取用户收件箱结果集  

	@param int $user_id 用户id
	return array/obj 用户收件箱结果集
	get_inbox_messages($user_id)

2, 获取用户发件箱结果集  

	@param int $user_id 用户id
	return array/obj 用户收件箱结果集
	get_outbox_messages($user_id)

3, 获取用户发件箱结果集  

	@param int $user_id 用户id
	return array/obj 用户收件箱结果集
	get_youpzt_messages($user_id,$type='inbox')

4, 获取用户未读消息数  

	@param int $user_id 用户id
	return int 未读消息数量
	get_messages_noread_count($user_id)

5, 获取用户已读消息数  

	@param int $user_id 用户id
	return int 已读消息数量
	get_messages_read_count($user_id)

6, 获取用户总消息数量  

	@param int $user_id 用户id
	return int 总消息数量
	get_messages_all_count($user_id)

7, 向某个用户发送站内信  

	@param int $user_id 接收者用户id
	@param int $subject 主题
	@param int $content 内容
	@param datetime $date 时间(可选)
	@param int $from_user 发送者用户id(默认为当前用户，也可指定)
	return bool true/false
	to_user_message($user_id,$subject,$content,$date='',$from_user='')


### 更新日志

**1.0【2015/11/19】:**
* 高级数据库设计。
* 可查看对方是否已读信。
* 封装完善的API。

