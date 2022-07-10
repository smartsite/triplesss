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
		$settings['dbname'] = 'dbname';
		// Username
		$settings['dbusername'] = 'dbusername';
		// Password
        $settings['dbpassword'] = 'dbpassword';

        /**
         *    User session  settings
         */  
        
		$settings['session_time'] = 1 * 30 * 60 * 120; // 2 hours
		
		$settings['hostname'] = 'http://your.hostname';
		
		return $settings;
	}
}

?>