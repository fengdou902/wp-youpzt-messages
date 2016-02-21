=== 插件信息 ===

插件名: 站内信youpzt-messages
URL: http://www.youpzt.com/
标签: 站内信，通信，信息
版本: 1.0

这个WordPress站内信插件允许你的WordPress网站用户之间可进行站内信互相通信

== 描述 ==

[项目主页](http://www.youpzt.com/wp-youpzt-messages) 

*这个WordPress站内信插件允许你的WordPress网站用户之间可进行站内信互相通信*，用户有自己的收件箱和发件箱，超级管理员拥有所有用户组(Administrator, Editor, Author, Constributor and Subscriber)的站内信操作权限。

1，同时可以进行站内信发送，同时让用户接收邮件通知。
2，提供前台站内信模板，让用户自己在前台进行管理。

**优化列表:**
1，去除加载语言包，源码汉化，性能提升更好。
2，可查看对方是否已读信。

== 常见问题 ==

= How can I set the number of private messages for each user? =

You can set number of PM for only user role (group): Administrator, Editor, Author, Contributor, Subscriber. The option is in the plugin option page (`Settings` > `Private Messages`)

= 如何删除我的旧的站内信? =

进入『收件箱』或者『已发送』点击删除按钮可以删除对应的站内信， 你也能点击下拉框进行批量操作删除站内信。

= 我如何给发送者进行回复? =

进入『收件箱』点击消息的回复按钮。

= 如果超过了发件限制，我该如何操作? =

你可以删除旧的站内信(see previous question) or ask admin to increase the mailbox quota.

= 如何进行使用前台站内信模板 =

1. 复制插件下的 `youpztMessages-template.php` 到你的主题目录下
2. 在wordpress后台创建一个页面，然后选择站内信模板
3. 打开页面检测是否成功。

提示: 模板文件是主干，你可以针对当前使用主题进行模板的自定义和美化。

== 更新日志 ==

= 1.0 =
* Bug fix: Send to multiple recipient
* Improvement: Add hooks to pages
* Improvement: Better retrieve user list

