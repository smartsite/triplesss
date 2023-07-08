<?php

namespace Triplesss\settings;

// Todo: move these to environment vars

class Settings
{
	Public $settings;

	function getSettings()
	{
        /**
         *   Database settings
         *   ( moving to .env )
		 */  
			
		 
        
		// Host name
		$settings['dbhost'] = 'localhost';
		// Database name
		$settings['dbname'] = 'members';
		// Username
		$settings['dbusername'] = 'members';
		// Password
		$settings['dbpassword'] = 'members';
		
		/**
		 *  JWT settings 
		 */
		
		// secret
		$settings['jwt_secret'] = file_get_contents("/var/www/html/jwt/secret.key");
		//  public key
		$settings['jwt_key'] = "public99";

		
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
			$settings['hostname'] = 'http://dev2022.surfsouthoz';
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