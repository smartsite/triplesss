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
		$settings['dbname'] = 'members9';
		// Username
		$settings['dbusername'] = 'members9';
		// Password
		$settings['dbpassword'] = 'pw';
		
		return $settings;
	}
}

?>