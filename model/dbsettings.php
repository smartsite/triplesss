<?php

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