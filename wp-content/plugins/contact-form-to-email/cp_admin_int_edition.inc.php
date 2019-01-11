<?php

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

global $wpdb;

$cpid = 'CP_CFTE';
$plugslug = $this->menu_parameter;

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST[$cpid.'_post_edition'] ) )
    echo "<div id='setting-error-settings_updated' class='updated settings-error'> <p><strong>Settings saved.</strong></p></div>";

if ($_GET["item"] == 'js')
    $saved_contents = base64_decode(get_option($cpid.'_JS', ''));
else if ($_GET["item"] == 'css')
    $saved_contents = base64_decode(get_option($cpid.'_CSS', ''));

$nonce = wp_create_nonce( 'cfte_update_actions_custom' );

?>
<script>
// Move to an external file
jQuery(function(){
	var $ = jQuery;
    <?php 
            if(function_exists('wp_enqueue_code_editor'))
			{
				$settings_js = wp_enqueue_code_editor(array('type' => 'application/javascript'));
				$settings_css = wp_enqueue_code_editor(array('type' => 'text/css'));

				// Bail if user disabled CodeMirror.
				if(!(false === $settings_js && false === $settings_css))
				{
                    if ($_GET["item"] == 'js')
                        print sprintf('{wp.codeEditor.initialize( "editionarea", %s );}',wp_json_encode( $settings_js ));
                    else
					    print sprintf('{wp.codeEditor.initialize( "editionarea", %s );}',wp_json_encode( $settings_css ));
				}
			}      
              
    ?>    
});
</script>
<style>
.ahb-tab{display:none;}
.ahb-tab label{font-weight:600;}
.tab-active{display:block;}
.ahb-code-editor-container{border:1px solid #DDDDDD;margin-bottom:20px;}
  
.ahb-csssample { margin-top: 15px; margin-left:20px;  margin-right:20px;}
.ahb-csssampleheader { 
  font-weight: bold; 
  background: #dddddd;
	padding:10px 20px;-webkit-box-shadow: 0px 2px 2px 0px rgba(100, 100, 100, 0.1);-moz-box-shadow:    0px 2px 2px 0px rgba(100, 100, 100, 0.1);box-shadow:         0px 2px 2px 0px rgba(100, 100, 100, 0.1);
  text-align:left;
}
.ahb-csssamplecode {     background: #f4f4f4;
    border: 1px solid #ddd;
    border-left: 3px solid #f36d33;
    color: #666;
    page-break-inside: avoid;
    font-family: monospace;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 1.6em;
    max-width: 100%;
    overflow: auto;
    padding: 1em 1.5em;
    display: block;
    word-wrap: break-word; 
    text-align:left;
}   
</style>
<div class="wrap">
<h1>Customization / Edit Page</h1>  



<input type="button" name="backbtn" value="Back to items list..." onclick="document.location='admin.php?page=<?php echo $plugslug; ?>';">
<br /><br />

<form method="post" action="" name="cpformconf"> 
<input name="<?php echo $cpid; ?>_post_edition" type="hidden" value="1" />
<input name="cfwpp_edit" type="hidden" value="<?php echo esc_attr($_GET["item"]); ?>" />
<input name="rsave" type="hidden" value="<?php echo $nonce; ?>" />  
<div id="normal-sortables" class="meta-box-sortables">

<?php if ($_GET["item"] == 'css') { ?>
    You can add custom CSS rules into this area (NO need to place the &lt;style&gt;&lt;/style&gt; tags). For ideas about useful styles check the <a href="https://form2email.dwbooster.com/faq#design">design section of the FAQ</a>.<br /><br />
<?php } else if ($_GET["item"] == 'js') { ?>
     You can add custom scripts into this area (NO need to place the &lt;script&gt;&lt;/script&gt; tags).<br /><br />
<?php }  ?>

<textarea name="editionarea" id="editionarea" style="width:100%" rows="20"><?php echo $saved_contents; ?></textarea> 
  
</div> 


<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  /></p>


</form>

<?php if ($_GET["item"] == 'css') { ?>
<hr />
   
   <div class="ahb-statssection-container" style="background:#f6f6f6;">
	<div class="ahb-statssection-header" style="background:white;
	padding:10px 20px;-webkit-box-shadow: 0px 2px 2px 0px rgba(100, 100, 100, 0.1);-moz-box-shadow:    0px 2px 2px 0px rgba(100, 100, 100, 0.1);box-shadow:         0px 2px 2px 0px rgba(100, 100, 100, 0.1);">
    <h3>Sample Styles:</h3>
	</div>
	<div class="ahb-statssection">
      
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           Make the send button in a hover format:
         </div>
         <div class="ahb-csssamplecode">
           .pbSubmit:hover {
               background-color: #4CAF50;
               color: white;
           }         
         </div>
        </div> 
        
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           Change the color of all form field labels:
         </div>
         <div class="ahb-csssamplecode">
           #fbuilder, #fbuilder label, #fbuilder span { color: #00f; }     
         </div>
        </div> 

        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           Change color of fonts into all fields:
         </div>
         <div class="ahb-csssamplecode">
           #fbuilder input[type=text], 
           #fbuilder textarea, 
           #fbuilder select { 
             color: #00f; 
           }     
         </div>
        </div> 
        
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
            Replace submit button text to icon/image:
         </div>
         <div class="ahb-csssamplecode">
           .pbSubmit{
                 background-image:url(<span style="color:#0000bb">https://form2email.dwbooster.com/images/logo.png</span>);
                color:transparent;
           }     
         </div>
        </div> 
                
        
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           Other styles:
         </div>
         <div class="ahb-csssamplecode">
           For other styles check the design section in the FAQ: <a href="https://form2email.dwbooster.com/faq?page=faq#design">https://form2email.dwbooster.com/faq?page=faq#design</a>     
         </div>
        </div>         
       
    </div>
   </div>
   
<?php } ?>
   
</div>













