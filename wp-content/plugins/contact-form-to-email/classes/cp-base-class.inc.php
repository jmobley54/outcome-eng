<?php

if( !class_exists( 'CP_CFTEMAIL_BaseClass' ) ) {         
    
class CP_CFTEMAIL_BaseClass {       

    protected $item = 1;
    
    /** installation functions */
    public function install($networkwide)  {
    	global $wpdb;
     
    	if (function_exists('is_multisite') && is_multisite()) {
    		// check if it is a network activation - if so, run the activation function for each blog id
    		if ($networkwide) {
    	                $old_blog = $wpdb->blogid;
    			// Get all blog ids
    			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    			foreach ($blogids as $blog_id) {
    				switch_to_blog($blog_id);
    				$this->_install();
    			}
    			switch_to_blog($old_blog);
    			return;
    		}	
    	} 
    	$this->_install();	
    }    
    
    function get_param($key)
    {
        if (isset($_GET[$key]) && $_GET[$key] != '')
            return $_GET[$key];
        else if (isset($_POST[$key]) && $_POST[$key] != '')
            return $_POST[$key];
        else 
            return '';
    }
    
    function is_administrator()
    {
        return current_user_can('manage_options');
    }
    
    function get_site_url($admin = false)
    {
        $blog = get_current_blog_id();
        if( $admin ) 
            $url = get_admin_url( $blog );	
        else 
            $url = get_home_url( $blog );	
        
        $url = parse_url($url);
        return rtrim(@$url["path"],"/");
    }
    
    
    function get_FULL_site_url($admin = false)
    {
        $blog = get_current_blog_id();
        if( $admin ) 
            $url = get_admin_url( $blog );	
        else 
            $url = get_home_url( $blog );	
        
        $url = parse_url($url);
        $url = rtrim($url["path"],"/");
        $pos = strpos($url, "://");
        if ($pos === false)
            $url = 'http://'.$_SERVER["HTTP_HOST"].$url;
        return $url;
    }   
    
    function cleanJSON ($str)
    {
        $str = str_replace('&qquot;','"',$str);
        $str = str_replace('	',' ',$str);
        $str = str_replace("\n",'\n',$str);
        $str = str_replace("\r",'',$str);      
        return $str;        
    }
    
    function add_field_verify ($table, $field, $type = "text") 
    {
        global $wpdb;
        
        $results = $wpdb->get_results( $wpdb->prepare("SHOW columns FROM `".esc_sql($table)."` where field=%s", $field) );    
        if (!count($results))
        {               
            $sql = "ALTER TABLE  `".esc_sql($table)."` ADD `".esc_sql($field)."` ".$type; 
            $wpdb->query($sql);
            return true;
        }
        return false;
    }    
    
    

    private $option_buffered_item = false;
    private $option_buffered_id = -1;

    function get_option ($field, $default_value = '')
    {   
        global $wpdb;        
        if ($this->option_buffered_id == $this->item)
            $value = @$this->option_buffered_item->$field;
        else
        {              
           $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->table_items." WHERE id=%d", $this->item ) );
           if (count($myrows)) 
           {
               $value = @$myrows[0]->$field;           
               $this->option_buffered_item = $myrows[0];
               $this->option_buffered_id  = $this->item;
           }
           else  
               $value = $default_value;
        }
        if ($value == '' && is_object($this->option_buffered_item) && $this->option_buffered_item->form_structure == '')
            $value = $default_value;
        return $value;
    }
    
    
    function getRealUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
    
        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }
    
        return $ip;
    }
       
} // end class

} // end if class exists

?>