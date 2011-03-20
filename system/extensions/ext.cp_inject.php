<?php
if (!defined('EXT')){ exit('Invalid file request'); }


class Cp_inject
{
	var $settings		= array();
	
	var $name			= 'Control Panel Inject JS/CSS';
	var $version		= '1.0';
	var $description	= 'Inject JS and/or CSS into the control panel';
	var $settings_exist	= 'y';
	var $docs_url		= 'http://ninefour.co.uk/labs/';
	
	/**
	 * Constructor
	 *
	 **/
	function Cp_inject($settings='')
	{
		$this->settings = $settings;
	}
	// END
	
	/**
	 * Add javascript to head
	 *
	 **/
	function add_header()
	{
		global $EXT;
		// Play nice with others
		$inject = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';
		
        // Build the javascript
		if ($this->settings['url_match']!="") {
			if(strpos($_SERVER['REQUEST_URI'], $this->settings['url_match'])) {
	    		$inject .= '<!-- Start injection -->';
	    		$inject .= $this->settings['needle_contents'];
	    		$inject .= '<!-- End injection -->';
	    	}
	   	}
        
		return $inject;
    }
    // END add header
  
    
    /**
	 * Activate extension
	 *
	 **/
    function activate_extension()
    {
    	global $DB, $PREFS;
    	
    	$default_settings = serialize( $this->default_settings() );
    	
    	$DB->query($DB->insert_string('exp_extensions',
    								  array('extension_id'	=> '',
    										'class'			=> get_class($this),
    										'method'		=> "add_header",
    										'hook'			=> "show_full_control_panel_end",
    										'settings'		=> $default_settings,
    										'priority'		=> 10,
    										'version'		=> $this->version,
    										'enabled'		=> "y"
    										)
    								 )
    			   );

    }
    // END activate
    
    
    /**
	 * Update extension
	 *
	 **/
    function update_extension($current='')
    {
    	global $DB, $PREFS;
    	
    	if ($current == '' OR $current == $this->version)
    	{
    		return FALSE;
    	}
    	
    	// Update version    	
    	$sql[] = "UPDATE exp_extensions SET version = '".$DB->escape_str($this->version)."' WHERE class = '" . get_class($this) . "'";
    	
    	// Run update queries
    	foreach ($sql as $query)
		{
			$DB->query($query);
		}		
    }
    // END update
    
    
    /**
	 * Disable extension
	 *
	 **/
    function disable_extension()
	{
		global $DB;
		
		$DB->query("DELETE FROM exp_extensions WHERE class = '" . get_class($this) . "'");
	}
	
	/**
	 * Extension settings
	 *
	 **/
    function settings()
    {
    	global $PREFS;
    	
    	$settings = array();
		$settings['url_match'] = '';
		$settings['needle_contents'] = array('t', '', '');
    	   	
    	return $settings;
    }
    // END settings
    
    /**
	 * Default Extension settings
	 *
	 **/
    function default_settings()
    {
    	global $PREFS;
    	
    	$default_settings = array(
    	    'url_match' => 'C=admin&M=blog_admin&P=edit_category',
    		'needle_contents' => 'Add your custom CSS & JS here'
    	);
    	   	
    	return $default_settings;
    }
    // END Default settings
	
}


/* End of file ext.cp_inject.php */
/* Location: ./system/extensions/ext.cp_inject.php */