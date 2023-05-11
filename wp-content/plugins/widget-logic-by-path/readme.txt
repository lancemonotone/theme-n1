=== Widget Logic by Path ===
Contributors: mchev2
Tags: widget logic, url, path, url path, context, widget, admin, conditional tags, filter 
Requires at least: 2.8
Tested up to: 3.6
Stable tag: 0.2.2

Widget Logic by Path adds URL path pattern based logic to Widget Logic. 

== Description ==
Widget Logic by Path extends [Widget Logic](http://wordpress.org/extend/plugins/widget-logic/) functionality to use URL path pattern based logic. It is similar to Drupal's "Show block on specific pages" feature. "*" is the wildcard character. See examples below.

By enabling this plugin, Widget Logic default PHP based logic (eval) will be [disabled](http://wordpress.org/extend/plugins/widget-logic/other_notes/#The-%27widget_logic_eval_override%27-filter). So if you are concerned about "eval" security, but still need to use Widget Logic, this plugin will be helpful.

* Widget Logic version >= 0.56 is required for this plugin to work.
* Works in Multisite. 


Example URL: http://www.domain.com/--SITE--/about/staff/

= Example 1 =
`about/             <-- Show on about page only
about/*            <-- Show on about and all the pages under about
about/staff/*      <-- Show on about/staff/ and all the pages under it
<home>             <-- Show on homepage
<blog>             <-- Show on blog latest posts front page
<search>           <-- Show on search page
<logged_in_user>   <-- Show only for logged in users`

= Example 2 =
`<all paths except>  <-- All paths except those listed below
about/*             <-- Do not show on about and all the pages under about
contact/            <-- Do not show on the contact page
<home>              <-- Do not show on homepage
<search>            <-- Do not show on search page`

= Example 3 (Advanced) =
`/.*apple.*/        <-- Show on all the pages with URL path matching this regular expression. In this case "/.*apple.*/" will be passed to PHP preg_match function"`

== Installation ==
1. Install [Widget Logic](http://wordpress.org/extend/plugins/widget-logic/) version >= 0.56
2. Install this plugin
3. Activate Widget Logic and this plugin through the 'Plugins' menu in WordPress
4. You can start adding URL path patterns to Widgets from the usual Widgets admin interface

== Changelog ==
= 0.2.2 =
* Fixed PHP warning for implode
= 0.2.1 =
* Added "blog" keyword for blog latest posts front page
* Updated regular expression used to evaluate * wildcard
* Suppress preg_match warnings incase of bad regex in user input
= 0.2 =
* Support for regular expressions
* Re-write
= 0.1.1 =
* Fixed conflict between home and search
= 0.1 =
* Initial wordpress.org release