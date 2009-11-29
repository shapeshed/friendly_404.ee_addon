# Friendly 404 - Suggests relevant weblog entries on a 404 page

**Author**: [George Ornbo][]
**Source**: [Github][]

## Compatibility

* ExpressionEngine Version 1.6.x (1.x.x releases), ExpressionEngine Version 2.0.x (2.x.x releases)
* PHP 5.x

## License

Friendly 404 is free for personal and commercial use. 

If you use it commercially use a donation of $10 is suggested. You can send [donations here](http://pledgie.org/campaigns/5743). 

Friendly 404 is licensed under a [Open Source Initiative - BSD License][] license.

## Installation

This file pi.friendly_404.php must be placed in the /system/plugins/ folder in your [ExpressionEngine][] installation.

For EE 1.6.x the file pi.friendly\_404.php must be placed in the /system/plugins/ folder in your [ExpressionEngine][] installation.

For EE 2.0.0 the friendly\_404 folder must be placed in the /system/expressionengine/third\_party/ folder in your [ExpressionEngine][] installation.

## Name

Friendly 404

## Synopsis

Returns suggestions of weblog entries on a 404 page.

## Description

The plugin attempts to match entries to the last segment of the 404 URL helping users to find pages that match what they were looking for.

Add the following to your 404 template

	{exp:friendly_404}
		{if count == 1}<ul>{/if}
			<li><a href="{auto_path}">{title}</a></li>
		{if count == total_results}</ul>{/if}
	{/exp:friendly_404}

If no match is found nothing will be shown

## Parameters

The following parameters are available:

limit - limits the number of entries returned (default: 5)

	{exp:friendly_404 limit="10"} 
	
weblog (ExpressionEngine 1.6.x) - limits entries to weblogs defined by their short name (default: show all weblogs)

	{exp:friendly_404 weblog="news|jobs"} 
	
channel (ExpressionEngine 2.x) - limits entries to weblogs defined by their short name (default: show all channels)

  {exp:friendly_404 channel="news|jobs"}
	
## Single Variables

	{title}
	{auto_path}
	{url_title}
	{count}
	{total_results}
	{weblog_id} (EE 1.6.x)
	{channel_id} (EE 2.x)
	{search_results_url}
	
## Examples

	{exp:friendly_404 limit="10"}
	
Only 10 results will be returned

	{exp:friendly_404 weblog="news|services"}
	
Only results from the news and services weblogs will be returned	
	
## License

Friendly 404 is licensed under a [Open Source Initiative - BSD License][] license.

[George Ornbo]: http://shapeshed.com/
[Github]: http://github.com/shapeshed/friendly_404.ee_addon/
[ExpressionEngine]:http://www.expressionengine.com/index.php?affiliate=shapeshed
[Open Source Initiative - BSD License]: http://o