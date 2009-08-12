<?php
/** 
 * ExpressionEngine
 *
 * LICENSE
 *
 * ExpressionEngine by EllisLab is copyrighted software
 * The licence agreement is available here http://expressionengine.com/docs/license.html
 * 
 * Friendly 404 Plugin
 * 
 * @category   Plugins
 * @package    friendly_404
 * @version    1.0.0
 * @since      1.0.0
 * @author     George Ornbo <george@shapeshed.com>
 * @see        {@link http://github.com/shapeshed/friendly_404.ee_addon/} 
 * @license    {@link http://opensource.org/licenses/bsd-license.php} 
 */

$plugin_info = array(
						'pi_name'			=> 'Friendly 404',
						'pi_version'		=> '1.1.0',
						'pi_author'			=> 'George Ornbo',
						'pi_author_url'		=> 'http://shapeshed.com/',
						'pi_description'	=> 'Returns suggestions for 404 page based on the final segment of the 404 URL',
						'pi_usage'			=> Friendly_404::usage()
					);

/**
 * Friendly 404 Plugin
 *
 * @category   Plugins
 * @package    friendly_404
 */
class Friendly_404{
	
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
	function Friendly_404() 
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
See http://github.com/shapeshed/friendly_404.ee_addon/

<?php
$buffer = ob_get_contents();

ob_end_clean(); 

return $buffer;
}
/* END */
	
}

?>