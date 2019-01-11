<?php

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

$this->item = intval($_GET["cal"]);
$records_per_page = 50;   

global $wpdb;

if ($this->item < 0) $this->setId(0);
$message = "";

if (isset($_GET['delmark']) && $_GET['delmark'] != '')
{
    for ($i=0; $i<=$records_per_page; $i++)
    if (isset($_GET['c'.$i]) && $_GET['c'.$i] != '')   
        $wpdb->query('DELETE FROM `'.$wpdb->prefix.$this->table_messages.'` WHERE id='.intval($_GET['c'.$i]));       
    $message = "Marked items deleted";
}
else if (isset($_GET['del']) && $_GET['del'] == 'all')
{    
    if (CP_CALENDAR_ID == '' || CP_CALENDAR_ID == '0')
        $wpdb->query('DELETE FROM `'.$wpdb->prefix.$this->table_messages.'`');           
    else
        $wpdb->query('DELETE FROM `'.$wpdb->prefix.$this->table_messages.'` WHERE formid='.intval($this->item));           
    $message = "All items deleted";
} 
else if (isset($_GET['ld']) && $_GET['ld'] != '')
{
    $verify_nonce = wp_verify_nonce( $_GET['rsave'], 'cfte_message_actions_plist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
        return;
    }     
    $wpdb->query( $wpdb->prepare( 'DELETE FROM `'.$wpdb->prefix.$this->table_messages.'` WHERE id=%d', intval($_GET['ld']) ) );       
    $message = "Item deleted";
}
else if (isset($_GET['import']) && $_GET['import'] == '1')
{    
    $verify_nonce = wp_verify_nonce( $_GET['rsave'], 'cfte_message_actions_plist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
        return;
    }     
    $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure)));
    $form = $form[0];    
    
    if (($handle = fopen($_FILES['importfile']['tmp_name'], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $rowdata = array(); 
            $formatted_data = '';
            $num = count($data);
            $row++;
            
            $time  = strip_tags($data[0]);
            $ip    = strip_tags($data[1]);
            $email = strip_tags($data[2]);
            
            for ($c=3; $c < $num; $c++)
                if (isset($form[$c-3]))
                {
                    $rowdata[$form[$c-3]->name] = $data[$c]; 
                    $formatted_data .= $form[$c-3]->title. ": ". $data[$c] . "\n\n";
                }                    
            $wpdb->insert($wpdb->prefix.$this->table_messages, array( 
                                   'formid' => $this->item,
                                   'time' => $time,
                                   'ipaddr' => $ip,
                                   'notifyto' => $email,
                                   'data' => $formatted_data,
                                   'posted_data' => serialize($rowdata),
                             ));            
        }
        fclose($handle);
    }    
    $message = "CSV File Imported.";
}

if ($this->item != 0)
    $myform = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.$this->table_items .' WHERE id=%d', intval($this->item) ) );

$current_page = intval($_GET["p"]);
if (!$current_page) $current_page = 1;
                                                                               

$cond = '';
if (@$_GET["search"] != '') $cond .= " AND (data like '%".esc_sql($_GET["search"])."%' OR posted_data LIKE '%".esc_sql($_GET["search"])."%')";
if (@$_GET["dfrom"] != '') $cond .= " AND (`time` >= '".esc_sql($_GET["dfrom"].(@$_GET["tfrom"]?' '.$_GET["tfrom"]:''))."')";
if (@$_GET["dto"] != '') $cond .= " AND (`time` <= '".esc_sql($_GET["dto"].(@$_GET["tto"]?' '.$_GET["tto"]:' 23:59:59'))."')";
if ($this->item != 0) $cond .= " AND formid=".intval($this->item);

$events = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );
$total_pages = ceil(count($events) / $records_per_page);

if ($message) echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>".$message."</strong></p></div>";

$nonce = wp_create_nonce( 'cfte_message_actions_plist' );

?>
<script type="text/javascript">
 function cp_deleteMessageItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {        
        document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&rsave=<?php echo $nonce; ?>&cal=<?php echo $this->item; ?>&list=1&ld='+id+'&r='+Math.random();
    }
 }
 function cp_deletemarked()
 {
    if (confirm('Are you sure that you want to delete the marked items?')) 
        document.dex_table_form.submit();
 }  
 function cp_deleteall()
 {
    if (confirm('Are you sure that you want to delete ALL bookings for this form?'))
    {        
        document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&cal=<?php echo intval($_GET["cal"]); ?>&list=1&del=all&r='+Math.random();
    }    
 }
 function cp_markall()
 {
     var ischecked = document.getElementById("cpcontrolck").checked;
     <?php for ($i=($current_page-1)*$records_per_page; $i<$current_page*$records_per_page; $i++) if (isset($events[$i])) { ?>
     document.forms.dex_table_form.c<?php echo $i-($current_page-1)*$records_per_page; ?>.checked = ischecked;
     <?php } ?>
 }  
</script>
<div class="wrap">
<h1><?php if ($this->item != 0) echo strip_tags($myform[0]->form_name); else echo 'All forms'; ?> - <?php echo $this->plugin_name; ?> Message List</h1>

<div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;Return to the forms list</a>
	<div class="clear"></div>
</div>

<div class="ahb-section-container">
	<div class="ahb-section">
<form action="admin.php" method="get">
 <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>" />
 <input type="hidden" name="cal" value="<?php echo $this->item; ?>" />
 <input type="hidden" name="list" value="1" />
 <nobr>Search for: <input type="text" name="search" value="<?php echo esc_attr($_GET["search"]); ?>" /> &nbsp; &nbsp; &nbsp;</nobr> 
 <nobr>From: <input type="text" id="dfrom" name="dfrom" style="width:100px;" value="<?php echo esc_attr($_GET["dfrom"]); ?>" /><?php cfte_get_time_field('tfrom');?>
 &nbsp; &nbsp; &nbsp; </nobr>
 <nobr>To: <input type="text" id="dto" name="dto" value="<?php echo esc_attr($_GET["dto"]); ?>" /><?php cfte_get_time_field('tto'); ?>
 &nbsp; &nbsp; &nbsp; </nobr>
 <nobr>Item: <select id="cal" name="cal" style="vertical-align:baseline;height:auto;"> 
          <option value="-1">[All Items]</option>
   <?php
    $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );                                                                     
    foreach ($myrows as $item)  
         echo '<option value="'.$item->id.'"'.(intval($item->id)==intval($this->item)?" selected":"").'>'.strip_tags($item->form_name).'</option>'; 
   ?>
    </select></nobr>
 <nobr> 
    <input class="button" type="submit" name="<?php echo $this->prefix; ?>_csv" value="Export to CSV"  style="margin-left:10px;float:right;"/>
    <input class="button-primary button" type="submit" name="ds" value="Filter"  style="float:right;" />
 </nobr>
</form>
 <div class="clearer"></div>
	</div>
</div>

<br />
                             
<?php


echo paginate_links(  array(
    'base'         => 'admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&list=1%_%&dfrom='.urlencode($_GET["dfrom"]).'&dto='.urlencode($_GET["dto"]).'&search='.urlencode($_GET["search"]),
    'format'       => '&p=%#%',
    'total'        => $total_pages,
    'current'      => $current_page,
    'show_all'     => False,
    'end_size'     => 1,
    'mid_size'     => 2,
    'prev_next'    => True,
    'prev_text'    => __('&laquo; Previous','contact-form-to-email'),
    'next_text'    => __('Next &raquo;','contact-form-to-email'),
    'type'         => 'plain',
    'add_args'     => False
    ) );

?>

<div id="dex_printable_contents">
<form name="dex_table_form" id="dex_table_form" action="admin.php" method="get">
 <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>" />
 <input type="hidden" name="cal" value="<?php echo intval($_GET["cal"]); ?>" />
 <input type="hidden" name="list" value="1" />
 <input type="hidden" name="delmark" value="1" />
<table class=" widefat fixed pages"  cellspacing="0" width="100%">
	<thead >
	<tr>
	  <th width="30" class="cpnopr"><input type="checkbox" name="cpcontrolck" id="cpcontrolck" value="" onclick="cp_markall();"></th>
	  <th style="padding-left:7px;font-weight:bold;width:120px;">Date</th>
	  <th style="padding-left:7px;font-weight:bold;">Email</th>
	  <th style="padding-left:7px;font-weight:bold;">Message</th>
	  <th style="padding-left:7px;font-weight:bold;width:80px;"  nowrap class="cpnopr">Options</th>	
	</tr>
	</thead>
	<tbody id="the-list">
	 <?php for ($i=($current_page-1)*$records_per_page; $i<$current_page*$records_per_page; $i++) if (isset($events[$i])) { ?>
	  <tr class='<?php if (!($i%2)) { ?>alternate <?php } ?>author-self status-draft format-default iedit' valign="top">
		<td width="1%"  class="cpnopr"><input type="checkbox" name="c<?php echo $i-($current_page-1)*$records_per_page; ?>" value="<?php echo $events[$i]->id; ?>" /></td>      
		<td><?php echo substr($events[$i]->time,0,16); ?></td>
		<td style="overflow:hidden"><?php echo htmlentities($events[$i]->notifyto); ?></td>
		<td  style="overflow:hidden"><?php  
		        $data = $events[$i]->data;		        
		        $posted_data = unserialize($events[$i]->posted_data);		        
		        foreach ($posted_data as $item => $value)
		            if (strpos($item,"_url") && $value != '')		         
		            {
		                $data = str_replace ($posted_data[str_replace("_url","",$item)],'<a href="'.$value.'" target="_blank">'.$posted_data[str_replace("_url","",$item)].'</a><br />',$data);  		                
		            }    
		        echo str_replace("\n","<br />",str_replace('<','&lt;',$data)); 
		    ?></td>
		<td class="cpnopr">
		  <input class="button" type="button" name="caldelete_<?php echo $events[$i]->id; ?>" value="Delete" onclick="cp_deleteMessageItem(<?php echo $events[$i]->id; ?>);" />                             
		</td>
      </tr>
     <?php } ?>
	</tbody>
</table>
</form>
</div>
<input class="button-primary button" type="button" name="pbutton" value="Print" onclick="do_dexapp_print();" />
<div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;Return to the forms list</a>
	<div class="clear"></div>
</div>
<div style="clear:both"></div>
<p class="submit" style="float:left;"><input class="button" type="button" name="pbutton" value="Delete marked items" onclick="cp_deletemarked();" /> &nbsp; &nbsp; &nbsp; </p>
<p class="submit" style="float:left;"><input class="button" type="button" name="pbutton" value="Delete All Bookings" onclick="cp_deleteall();" /></p>
<div style="clear:both"></div>

</div>

<?php if ($this->item) { ?>
<div id="normal-sortables" class="meta-box-sortables">

 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Import CSV File</span></h3>
  <div class="inside">
  
   <form name="CPImportForm" action="admin.php?page=cp_contactformtoemail&rsave=<?php echo $nonce; ?>&cal=<?php echo $this->item; ?>&list=1&import=1" method="post" enctype="multipart/form-data">
   <input style="float:left"  type="file" name="importfile" />
   <input class="button" type="submit" name="pbuttonimport" value="Import"/>
   <div style="clear:both"></div>
   <p>Instructions: Comma separated CSV file. One record per line, one field per column. <strong>Don't use a header row with the field names</strong>.</p>
   <p>The first 3 columns into the CSV file are the <strong>time, IP address and email address</strong>, if you don't have this information then leave the first three columns empty. 
      After those initial columns the fields (columns) must appear in the same order than in the form.</p>
   <p>Sample format for the CSV file:</p>
   <pre>
<span style="color:#009900;">2017-03-21 18:50:00, 192.168.1.12, john@sample.com,</span> "john@sample.com", "sample subject", "sample message text"
<span style="color:#009900;">2017-04-16 20:49:00, 192.168.1.24, jane.smith@sample.com,</span> "jane.smith@sample.com", "other subject", "other message"
   </pre>
   </form>
  </div>
</div>
</div>
<?php } ?>

<script type="text/javascript">
 function do_dexapp_print()
 {
      w=window.open();
      w.document.write("<style>.cpnopr{display:none;};table{border:2px solid black;width:100%;}th{border-bottom:2px solid black;text-align:left}td{padding-left:15px;border-bottom:1px solid black;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
      w.print();
      w.close();    
 }
 
 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#dfrom").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 	$j("#dto").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 });
 
</script>
<?php

function cfte_get_time_field($field)
{
  echo '<select style="vertical-align:baseline;height:auto;" name="'.$field.'">  <option value=""></option>';
  for ($i=0; $i<23; $i++)
  {
      echo '<option'.($_GET[$field]==($i<10?'0':'').$i.":00"?' selected':'').'>'.($i<10?'0':'').$i.":00</option>";
      echo '<option'.($_GET[$field]==($i<10?'0':'').$i.":15"?' selected':'').'>'.($i<10?'0':'').$i.":15</option>";
      echo '<option'.($_GET[$field]==($i<10?'0':'').$i.":30"?' selected':'').'>'.($i<10?'0':'').$i.":30</option>";
      echo '<option'.($_GET[$field]==($i<10?'0':'').$i.":45"?' selected':'').'>'.($i<10?'0':'').$i.":45</option>";
  }
  echo '</select>';
}











