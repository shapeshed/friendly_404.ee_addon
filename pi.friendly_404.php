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
 * @version    2.0.0
 * @since      1.0.0
 * @author     George Ornbo <george@shapeshed.com>
 * @see        {@link http://github.com/shapeshed/friendly_404.ee_addon/} 
 * @license    {@link http://opensource.org/licenses/bsd-license.php} 
 */

$plugin_info = array(
  'pi_name'         => 'Friendly 404',
  'pi_version'      => '2.0.0',
  'pi_author'       => 'George Ornbo',
  'pi_author_url'   => 'http://shapeshed.com/',
  'pi_description'  => 'Returns suggestions for 404 page based on the final segment of the 404 URL',
  'pi_usage'        => Friendly_404::usage()
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

    $this->EE =& get_instance();	

    /*---------------------------------------
    Get variables
    ----------------------------------------*/

    $search_segment = end($this->EE->uri->segment_array());

    $this->limit = ( ! $this->EE->TMPL->fetch_param('limit')) ? 5 :  $this->EE->TMPL->fetch_param('limit');
    $this->channel = ( ! $this->EE->TMPL->fetch_param('channel')) ? '' :  $this->EE->TMPL->fetch_param('channel');
    $this->tagdata =  $this->EE->TMPL->tagdata;

    /*---------------------------------------
    Build channel query
    ----------------------------------------*/

    $channel_str = "";
    if ($this->channel != "") 
    {
      $count = 1; 
      $channels = explode("|", $channels);
      foreach ($channel as $channel_name) 
      {
        if ($count == 1) 
        {
          $channel_str .= " AND ( w.channel_name = '".$DB->escape_str($channel_name)."'";
          $count++;
        }
        else 
        {
          $channel_str .= " OR w.channel_name = '".$DB->escape_str($channel_name)."'";
        }
      } 
      $channel_str .= " )";
    }
    
    /*---------------------------------------
    Query the DB
    ----------------------------------------*/

    $query = $this->EE->db->query("SELECT t.entry_id, t.title, t.url_title, t.channel_id, c.search_results_url, c.channel_url FROM exp_channel_titles AS t
    LEFT JOIN exp_channels AS c ON t.channel_id = c.channel_id 
    WHERE t.entry_date < UNIX_TIMESTAMP()
    $channel_str
    AND (t.expiration_date = 0 || t.expiration_date > UNIX_TIMESTAMP())
    AND (t.title LIKE '%".$this->EE->db->escape_str($search_segment)."%')
    AND t.status = 'Open' AND t.status != 'closed'
    ORDER BY t.sticky desc, t.entry_date desc, t.entry_id desc
    LIMIT 0, ".$this->EE->db->escape_str($this->limit)."");


    if ($query->num_rows() > 0)
    {
      $total_results = $query->num_rows();
      foreach ($query->result_array() as $count => $row)
      {
        $row['count']			= $count+1;
        $row['total_results']	= $total_results;
        
        /*---------------------------------------
        Format the link and add to $row[] array as auto_path
        ----------------------------------------*/
        
        $url = ($row['search_results_url'] != '') ? $row['search_results_url'] : $row['channel_url'];
        $row['auto_path'] = $this->EE->functions->remove_double_slashes($this->EE->functions->prep_query_string($url).'/'.$row['url_title'].'/');

        /*---------------------------------------
        Conditionals
        ----------------------------------------*/
        
        $tagdata =  $this->EE->functions->prep_conditionals($this->tagdata, $row);    

        /*---------------------------------------
        Single variables
        ----------------------------------------*/
        
        foreach ($this->EE->TMPL->var_single as $key => $val)
        {
          if (isset($row->$val))
          {
              $tagdata = $this->EE->TMPL->swap_var_single($val, $row[$val], $tagdata);
          }
        } 
      $this->return_data .= $tagdata;      
      } 
    }	
  }

  /**
  * Plugin usage documentation
  *
  * @return	string Plugin usage instructions
  */
  public function usage()
  {
    return "Documentation is available here http://shapeshed.github.com/expressionengine/plugins/friendly_404/";
  }
	
}

?>