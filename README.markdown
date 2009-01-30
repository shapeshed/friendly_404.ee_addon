Friendly 404 - Suggests relevant weblog entries on a 404 page
===========================================================================

**Author**: [George Ornbo]
**Github Repository**: <http://github.com/shapeshed/friendly_404.ee_addon/>

Installation
-----

This file pi.human_filesize.php must be placed in the /system/plugins/ folder in your ExpressionEngine installation.

Name
------------------

Friendly 404

Synopsis
-------

Returns suggestions of weblog entries on a 404 page.

Description
-------

The plugin attempts to match entries to the last segment of the 404 URL helping users to find pages that match what they were looking for.

Add the following to your 404 template

	{exp:friendly_404}
		{if count == 1}<ul>{/if}
			<li><a href="{auto_path}">{title}</a></li>
		{if count == total_results}</ul>{/if}
	{/exp:friendly_404}

If no match is found nothing will be shown

Parameters
-------

The following parameters are available:

limit - limits the number of entries returned (default: 5)

	{exp:friendly_404 limit="10"} 
	
weblog - limits entries to weblogs defined by their short name (default: show all weblogs)

	{exp:friendly_404 weblog="news|jobs"} 
	
Single Variables
-------

	{title}
	{auto_path}
	{url_title}
	{count}
	{total_results}
	{weblog_id}
	{search_results_url}
	
Examples
-------

	{exp:friendly_404 limit="10"}
	
Only 10 results will be returned

	{exp:friendly_404 weblog="news|services"}
	
Only results from the news and services weblogs will be returned	
	
Compatibility
-------

ExpressionEngine Version 1.6.x

See also
-------

http://expressionengine.com/forums/viewthread/92908/
	
License
-------

SS Friendly 404 is licensed under a [Open Source Initiative - BSD License][] license.

---

This file is written using the MarkDown syntax. It may or may not have been parsed. If you are having trouble reading it try running the contents through http://daringfireball.net/projects/markdown/dingus.

[Shape Shed]: http://shapeshed.com/
[ExpressionEngine]:http://www.expressionengine.com/index.php?affiliate=shapeshed
[Open Source Initiative - BSD License]: http://opensource.org/licenses/bsd-license.php