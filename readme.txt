=== Post Archival in the Internet Archive ===
Contributors: geekysoft
Tags: Internet Archive, Wayback Machine
Requires at least: 4.5.2
Tested up to: 5.0.2
Stable tag: 1.3.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Automatically save new blog posts to the Internet Archive.

== Description ==

The Internet Archive saves snapshots of web pages and makes them available for posterity. Now you can have your blog posts included in this internet-wide archive of knowledge!

Install and enable this plugin and it will silently take care of the rest. There is no configuration or anything else required from you than to keep writing great blog posts. New posts will be submitted to the archive 12 hours after you publish them on your website.

External links in your posts will also be submitted to the Internet Archive, helping to assure that the resources you reference will remain available in the future.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/post-archival` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the ‘Plugins’ screen in WordPress

There is no configuration or settings required. Your old and your future blog posts will be archived as you publish them.

_Optionally,_ you may also want to install the [Minor Edits plugin](https://wordpress.org/plugins/minor-edits/) to archive any significant changes you make to your posts after publishing them.

== Frequently Asked Questions ==

= What is the Internet Archive? =

The Internet Archive is a non-profit organization that archives the worlds information online over time. Read more in [their FAQ](https://archive.org/about/faqs.php#The_Wayback_Machine).

= What does it mean to be included in the archive? =

The Internet Archive stores copies of all text, images, stylesheets scripts, and other resources from your website in a “snapshot”. These snapshots can show what your website looked like in the past, but also highlight how information online change over time. Your web pages are preserved as-is with everything included; even the ads! If your website goes down, there will still be a copy of it in the Internet Archive.

The archive is freely available to anyone including academic researchers through the [Internet Archive website](https://web.archive.org/).

= Is my site guaranteed to be included in the archive forever? =

No. That is entirely up the Internet Archive. This plugin will simply inform them when you have new pages that are available for archival.

See [their FAQ](https://archive.org/about/faqs.php#The_Wayback_Machine).

= When are new blog posts archived? =

12 hours after you publish a blog post (or after it’s been scheduled). This is to give you a window were you can mix broken links, correct spelling mistakes, or even unpublish the post without having it included in the archive. After 12 hours, the post will already be included in so many archives and caches around the web that it would be hard to unpublish it. This time window is not configurable.

= How can I update the post in the Wayback Machine once I make changes to my blog posts? =

The old versions will remain in the archive. You may, however submit a new revision to the Internet Archive.

Normally when you make changes to and update an already public blog post, this plugin will not send another request. If you install the [*Minor Edits* plugin](https://wordpress.org/plugins/minor-edits/), however, all non-minor edits to your posts (as defined by that plugin) will queue a new archive submission no more than once per 12-hour assuming no other archive request have already been queued.

= Can the plugin archive my old blog posts? =

When you first install and enable this plugin, it will schedule all your old post for archival. The plugin waits 25 minutes between each archival request, so this can take several hours or even days depending on how many blog posts you’ve got. The long delay between each request is intended to prevent a large WordPress site from flooding the archive with new requests. It’s fully automatic so do not worry about it.

= Can the plugin archive other pages? tag? categories? =

No. Your website’s blog posts are the meat of the matter and are the types of pages that are most interesting to archive. Other pages are not archived by this plugin, but you can request archiving of any page on the web at [https://web.archive.org](https://web.archive.org/).

Note that both your posts’ permalinks and shortlinks (technically only redirects to the permalink) are archived.

= Can I archive my WordPress.com hosted blog? =

Blogs hosted by WordPress.com are already automatically archived. No need to do anything.

= My PHP error log is full of errors! =

The plugin is unlikely to trigger any error logging except when your PHP or server security settings limit use of the `file_get_contents` function in PHP. Refer to your server manual for details on how to best address this problem.

== Changelog ==

= 1.3.1 =

* Bugfix.

= 1.3.0 =

* Request archival of all linked resources in a post (only done on initial publication).

= 1.2.2 =

* Resolved (another) problem leading to some edits incorrectly triggering new archival requests

= 1.2.1 =

* Submit new archival requests on significant post updates (requires the [*Minor Edits* plugin](https://wordpress.org/plugins/minor-edits/))
* Archive the home page as a one-off when activating the plugin
* Shortened timeout when archiving posts
* More [fault-tolerant use of `file_get_contents()`](https://www.ctrl.blog/entry/php-file-contents-dual-stack) (IPv6)
* Resolved a problem leading to all edits incorrectly triggering new archival requests
* Stop send separate archive requests for shortlinks.

= 1.1 =

* User-Agent now includes the site URL

= 1.0 =

* Initial public release
