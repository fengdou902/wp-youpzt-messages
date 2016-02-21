=== Plugin Name ===
Contributors: youpzt
link: http://www.youpzt.com/
Tags: pm, private message, private messages, message, messages
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 2.1.10

The Private Messages For WordPress allows users of WordPress blog send private messages (PM) to each other, just like in a forum.

== Description ==

[Project Page](http://www.deluxeblogtips.com/private-messages-for-wordpress) | [Support](http://www.deluxeblogtips.com/support) | [Donate](http://www.deluxeblogtips.com/donate)

*The Private Messages For WordPress allows users of WordPress blog send private messages (PM) to each other*.  Users will have their own inbox and outbox. Administrators of blog can  control total numbers of items in mailbox of each user group (Administrator, Editor, Author, Constributor and Subscriber).

Also, an email is sent to user when a new PM is received. Email template is full-controlled.

**优化列表:**
1，去除加载语言包，源码汉化，性能提升更好。
2，可查看对方是否已读信。

== Frequently Asked Questions ==

= How can I set the number of private messages for each user? =

You can set number of PM for only user role (group): Administrator, Editor, Author, Contributor, Subscriber. The option is in the plugin option page (`Settings` > `Private Messages`)

= How can I delete my old PM? =

Go to `Inbox` or `Outbox`, and click the link `Delete` after each message to delete it. Or you can check multiple messages and select `Delete` action from the dropdown box.

= How can I reply to sender? =

Just click the `Reply` link below the message in your `Inbox`.

= What can I do if I exceed my limit? =

You can delete your old PM (see previous question) or ask admin to increase the mailbox quota.

= How can use this plugin in the front-end =

1. Copy the file `pm4wp-template.php` into your theme folder
1. Create a page, choose Private Messages as a page template (in the right panel)
1. Check it out in the front-page

Note: the template file is just the backbone, you should modify it to fit your template.

== Screenshots ==

1. Inbox
2. Outbox
3. Send page
4. Option page

== Changelog ==

= 1.0 =
* Bug fix: Send to multiple recipient
* Improvement: Add hooks to pages
* Improvement: Better retrieve user list

