=== 404 Not found report ===
Contributors: MrXHellboy
Donate link: http://online-source.net/2011/04/03/404-not-found-report/
Tags: 404, not found, report
Requires at least: 1.5
Tested up to: 3.1
Stable tag: 0.8

== Description ==

<h2>Track your 404 hits!</h2>
Do you keep track of your 404 not found hits ?  If you don't, try this simple plugin.
It will keep track of the hits on your 404 not found page.<br />
<hr />
<h2>Useful for what ?</h2>
Well, probably you removed or relocated some categories, pages, posts etc etc a while ago or maybe just a couple of weeks without knowing anymore.
Right now it sucks search engines already checked your website and took a notice of those URI's or maybe you have some anchors on your own website pointing to moved URI's.
Those visitors arrives on your 404 not found page with a notice that the page they are looking for not exists anymore.
Some visitors will stay on your site and look for the particular page, but the other group leaves immediately. What a shame...<br />
<hr />
<h2>What does this plugin do ?</h2>
This plugin tracks those visits on your 404 - not found page. It stores the time, requested URI and Referer. 
More information is absolutely not needed. The time and referer does already overdo it, but maybe you can get some extra information out of it.
Thos information is stored in a txt file since i found it quite useles to create a new table or use the database in the first place.
With 1 single click on the button you empty the file when you're done with taking the appropriate actions on the results.
You may want to add rewrite conditions @ your .htaccess file or use a seperate plugin for adding redirects.
You can check the report within the admin settings menu - 404 report<br />
<hr />
<h2>How do i get it to work ?</h2>
Simple!
1) Upload this plugin into your plugin directory. Activate the plugin: admin->plugins.
2) Open your 404.php page within your theme and add the following snippet somewhere on that page.
[php]
# 404 reporting
if (class_exists('NotFound_404')){
    $Error404 = new NotFound_404();
        $Error404->Log404Error();
}
[/php]
3) Change the permissions (CHMOD) of the file 404.txt to 600.

Have fun logging!
<hr />
== Development Blog ==
[Online-Source Wordpress plugins](http://online-source.net/category/scripts/wordpress-plugins/ "Online-Source Wordpress plugins")

== Installation ==
Simple! Open your 404.php page within your theme and add the following snippet somewhere on that page.
[php]
# 404 reporting
if (class_exists('NotFound_404')){
    $Error404 = new NotFound_404();
        $Error404->Log404Error();
}
[/php]

Change the permissions (CHMOD) of the file 404.txt to 600.
[Category plugin](http://online-source.net/2011/04/03/404-not-found-report/ "Installation/information")

== Screenshots ==
[Category plugin](http://online-source.net/2011/04/03/404-not-found-report/ "Installation/information")