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
		$settings['dbname'] = 'members';
		// Username
		$settings['dbusername'] = 'mem';
		// Password
		$settings['dbpassword'] = 'password';
		
		if($this->getEnvironment() == 'dev') {
			$settings['dbhost'] = 'localhost';
			// Database name
			$settings['dbname'] = 'members';
			// Username
			$settings['dbusername'] = 'members';
			// Password
			$settings['dbpassword'] = 'password';
		}

        /**
         *    User session  settings
         */  
        
		$settings['session_time'] = 1 * 30 * 60 * 60; // 30 mins for testing!
		
		$settings['hostname'] = 'https://hostymchostface.com';

		if($this->getEnvironment() == 'dev') {
			$settings['hostname'] = 'http://dev.hostymchostface.com';
		}
		
		return $settings;
	}

	public function getEnvironment() {
		$host = $_SERVER['HTTP_HOST']; 
		
		$settings['environment'] = 'dev';
		
		if(strpos($host, 'hosty') > -1) {
			$settings['environment'] = 'prod';
		}

		if(strpos($host, 'uat.hosty') > -1) {
			$settings['environment'] = 'uat';
		}

		return $settings['environment'];
	}
}

?>
