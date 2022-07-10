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
		$settings['dbname'] = 'smartsit_members9';
		// Username
		$settings['dbusername'] = 'smartsit_mem9';
		// Password
		$settings['dbpassword'] = '$bLUe123!*';
		
		if($this->getEnvironment() == 'dev') {
			$settings['dbhost'] = 'localhost';
			// Database name
			$settings['dbname'] = 'members9';
			// Username
			$settings['dbusername'] = 'members9';
			// Password
			$settings['dbpassword'] = 'pw';
		}

        /**
         *    User session  settings
         */  
        
		$settings['session_time'] = 1 * 30 * 60 * 60; // 30 mins for testing!
		
		$settings['hostname'] = 'https://vip.surfsouthoz.com.au';

		if($this->getEnvironment() == 'dev') {
			$settings['hostname'] = 'http://uat.surfsouthoz';
		}
		
		return $settings;
	}

	public function getEnvironment() {
		$host = $_SERVER['HTTP_HOST']; 
		
		$settings['environment'] = 'dev';
		
		if(strpos($host, 'vip.surfsouthoz') > -1) {
			$settings['environment'] = 'prod';
		}

		if(strpos($host, 'app2.surfsouthoz') > -1) {
			$settings['environment'] = 'uat';
		}

		return $settings['environment'];
	}
}

?>