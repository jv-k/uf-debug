<?php

/*
 * TLP
 *
 * @link      https://github.com/jv-k/annaslittleprince
 * @copyright Copyright (c) 2022 John Valai
 * @license   UNLICENSED
 */

namespace UserFrosting\Sprinkle\UfDebug;

use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Sprinkle\Account\Log\UserActivityProcessor;
use UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager;

use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Account\Authorize\AuthorizationException;
use UserFrosting\Support\FileNotFoundException;

/**
 * Controller class to Generate a Debug page
 * 
 * @author John Valai (https://jvk.to)
 * 
 */
class DebugController extends SimpleController {
	
	/**
	 * pageDebug
	 *
     * @param Request  $request
     * @param Response $response
     * @param string[] $args
	 * 
	 * @throws AuthorizationException If user is not authorized to access page
 	 */
	public function pageDebug($request, $response, $args) {

		$this->Log("accessed /debug/", __FUNCTION__);

		/** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
		$authorizer = $this->ci->authorizer;

		/** @var UserFrosting\Sprinkle\Account\Model\User $currentUser */
		$currentUser = $this->ci->currentUser;

		if (!$authorizer->checkAccess($currentUser, 'maintenance_access')) {
			$e = new AuthorizationException("Debug access privilege required!");
			$e->addUserMessage("You do not have the privilige to access this page! Please contant the Admin.");
			throw $e;
		}

		$cfg = [
			'debugPanes' => [
				'uf-site' => [
					'title' => 'UF Site Config',
					'body' 	=> '<pre>' . json_encode($this->ci->config['site'], JSON_PRETTY_PRINT) . '</pre>', 
					'isRaw'	=> true // true = won't escape body contents
				],
				'phpinfo' => [
					'title' => 'PHP Info',
					'body' => $this->getPhpInfo(), 
					'isRaw'	=> true
				],
			]
		];

		return $this->ci->view->render($response, 'pages/debug.html.twig', $cfg);
	}
	
	/**
	 * Fetches and strips unneeded tags from phpinfo()
	 *
	 */
	private function getPhpInfo() {

		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();
		
		$phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);

		return "
			<style type='text/css'>
				#phpinfo {}
				#phpinfo pre {margin: 0; font-family: monospace;}
				#phpinfo a:link {color: #009; text-decoration: none; background-color: #fff;}
				#phpinfo a:hover {text-decoration: underline;}
				#phpinfo table {border-collapse: collapse; border: 0; width: 100%; box-shadow: 1px 2px 3px #ccc;}
				#phpinfo .center {text-align: center;}
				#phpinfo .center table {margin: 1em auto; text-align: left;}
				#phpinfo .center th {text-align: center !important;}
				#phpinfo td, th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
				#phpinfo h1 {font-size: 150%;}
				#phpinfo h2 {font-size: 125%;}
				#phpinfo .p {text-align: left;}
				#phpinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}
				#phpinfo .h {background-color: #99c; font-weight: bold;}
				#phpinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
				#phpinfo .v i {color: #999;}
				#phpinfo img {float: right; border: 0;}
				#phpinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
			</style>
			<div id='phpinfo'>
				$phpinfo
			</div>
			";
	}

    /**
     * Log function in  table UF_activities    
     *
     * @param  String  $msg 	Text to be recorded in Log
     * @param  String  $type	Category of the log message
	 */
    private function Log($msg, $type) {

        $this->ci->userActivityLogger->info("DEBUG: User {$this->ci->currentUser->user_name}: $msg", [
            'type' => "TLP:$type"
        ]);
        
    }	

}
