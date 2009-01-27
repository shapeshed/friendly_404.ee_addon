<?php
/** 
 * ExpressionEngine
 *
 * LICENSE
 *
 * ExpressionEngine by EllisLab is copyrighted software
 * The licence agreement is available here http://expressionengine.com/docs/license.html
 * 
 * SS Friendly 404 Plugin
 * 
 * @category   Plugins
 * @package    ss_friendly_404
 * @version    1.1.0
 * @since      1.0.0
 * @author     George Ornbo <george@shapeshed.com>
 * @see        {@link http://code.google.com/p/shapeshed-ee-addons/wiki/Friendly404Plugin} 
 * @license    {@link http://www.opensource.org/licenses/mit-license.php} 
 */

$plugin_info = array(
						'pi_name'			=> 'SS Friendly 404',
						'pi_version'		=> '1.1.0',
						'pi_author'			=> 'George Ornbo',
						'pi_author_url'		=> 'http://shapeshed.com/',
						'pi_description'	=> 'Returns suggestions for 404 page based on the final segment of the 404 URL',
						'pi_usage'			=> Ss_friendly_404::usage()
					);

/**
 * SS Friendly 404 Plugin
 *
 * @category   Plugins
 * @package    ss_friendly_404
 */
class Ss_friendly_404{
	
	/**
	* Return data
	* @var string
	*/
	var $return_data;
	
	/** 
	* Returns suggestions for 404 page based on the final segment of the 404 URL
	* 
	* @access public 
	* @return string 
	*/
	function Ss_friendly_404() 
    {

		global $TMPL, $DB, $IN, $FNS, $REGX;	

		/*---------------------------------------
		Get variables
		----------------------------------------*/

		$search_segment = end($IN->SEGS);
		$limit = ( ! $TMPL->fetch_param('limit')) ? '5' : $TMPL->fetch_param('limit');
		$weblog = ( ! $TMPL->fetch_param('weblog')) ? '' : $TMPL->fetch_param('weblog');

		/*---------------------------------------
		Build weblog query
		----------------------------------------*/						

		$weblog_str = "";
		if ($weblog != "") 
		{
			$count = 1; 
			$weblogs = explode("|", $weblog);
				foreach ($weblogs as $weblog_name) 
				{
					if ($count == 1) 
					{
						$weblog_str .= " AND ( w.blog_name = '".$DB->escape_str($weblog_name)."'";
						$count++;
					}
					else 
					{
						$weblog_str .= " OR w.blog_name = '".$DB->escape_str($weblog_name)."'";
					}
				} 
			$weblog_str .= " )";
		} 	
			
		/*---------------------------------------
		Query the DB
		----------------------------------------*/			

	    $query = $DB->query("SELECT t.entry_id, t.title, t.url_title, t.weblog_id, w.search_results_url, w.blog_url FROM exp_weblog_titles AS t
					LEFT JOIN exp_weblogs AS w ON t.weblog_id = w.weblog_id 
					WHERE t.entry_date < UNIX_TIMESTAMP()
					$weblog_str
					AND (t.expiration_date = 0 || t.expiration_date > UNIX_TIMESTAMP())
					AND (t.title LIKE '%".$DB->escape_str($search_segment)."%')
					AND t.status = 'Open' AND t.status != 'closed'
					ORDER BY t.sticky desc, t.entry_date desc, t.entry_id desc
					LIMIT 0, ".$DB->escape_str($limit)."");
			
		$total_results = sizeof($query->result);
	
        foreach ($query->result as $count => $row)
        {
			$tagdata = $TMPL->tagdata;

			$row['count']			= $count+1;
			$row['total_results']	= $total_results;

			/*---------------------------------------
			Format the link and add to $row[] array as auto_path
			----------------------------------------*/				
	
			$url = ($row['search_results_url'] != '') ? $row['search_results_url'] : $row['blog_url'];							
			$row['auto_path'] = $FNS->remove_double_slashes($REGX->prep_query_string($url).'/'.$row['url_title'].'/');

			/*---------------------------------------
			Conditionals
			----------------------------------------*/

			$tagdata = $FNS->prep_conditionals($tagdata, $row);
	
			/*---------------------------------------
			Single variables
			----------------------------------------*/
	
		    foreach ($TMPL->var_single as $key => $val)
		    {
		        if (isset($row[$val]))
		        {
		            $tagdata = $TMPL->swap_var_single($val, $row[$val], $tagdata);
		        }
		    }

		    $this->return_data .= $tagdata; 
		}	
					
	}

function usage()
{
ob_start(); 
?>
NAME
=======================
SS Friendly 404 

SYNOPSIS
=======================
Returns suggestions of weblog entries on a 404 page. 	

DESCRIPTION
=======================		
The plugin matches entries to the last segment of the 404 URL helping users to find pages that match what they were looking for.

Add the following to your 404 template

{exp:ss_friendly_404}
	{if count == 1}<h4>Perhaps you were looking for<ul>{/if}
		<li><a href="{auto_path}">{title}</a></li>
	{if count == total_results}</ul>{/if}
{/exp:ss_friendly_404}

If no match is found nothing will be shown

***********************
PARAMETERS
***********************
The following parameters are available:

limit - limits the number of entries returned

e.g {exp:ss_friendly_404 limit="10"}
default: 5

weblog - limits entries to weblogs defined by their short name

e.g {exp:ss_friendly_404 weblog="news|jobs"}	
default: show all weblogs

***********************
SINGLE VARIABLES
***********************
{title}
{auto_path}
{url_title}
{count}
{total_results}
{weblog_id}
{search_results_url}
			
EXAMPLES
=======================
{exp:ss_friendly_404 limit="10"}
10 results will be returned	

{exp:ss_friendly_404 weblog="news|services"}
Only results from the news and services weblogs will be returned

COMPATIBILITY
=======================
ExpressionEngine Version 1.6.x 

SEE ALSO
=======================
http://code.google.com/p/shapeshed-ee-addons/wiki/Friendly404Plugin

BUGS
=======================
http://code.google.com/p/shapeshed-ee-addons/issues/list

<?php
$buffer = ob_get_contents();

ob_end_clean(); 

return $buffer;
}
/* END */
	
}

?>