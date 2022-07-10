<?php

// TODO: retire this class and change all refs to use the Settings class 

namespace  Triplesss\db;

class DBSettings
{
	Public $settings;

	function getSettings()
	{
		// Database variables
		// Host name
		$settings['dbhost'] = 'localhost';
		// Database name
		$settings['dbname'] = 'dbname';
		// Username
		$settings['dbusername'] = 'dbusername';
		// Password
        $settings['dbpassword'] = 'dbpassword';
		
		return $settings;
	}
}

?>