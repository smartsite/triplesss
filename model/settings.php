<?php

namespace Triplesss\settings;

// Todo: move these to environment vars

class Settings
{
	Public $settings;

	function getSettings()
	{
        /**
         *    Database settings
         */     
        
		// Host name
		$settings['dbhost'] = 'localhost';
		// Database name
		$settings['dbname'] = 'members9';
		// Username
		$settings['dbusername'] = 'members9';
		// Password
        $settings['dbpassword'] = 'pw';

        /**
         *    User session  settings
         */  
        
		$settings['session_time'] = 1 * 30 * 60 * 120; // 2 hours
		
		$settings['hostname'] = 'http://dev2020-vbox';
		
		return $settings;
	}
}

?>