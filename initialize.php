<?php

define('APP_ROOT',realpath(dirname(__FILE__)));

require_once APP_ROOT.'/mfw/mfwServerEnv.php';
require_once APP_ROOT.'/mfw/mfwRequest.php';
require_once APP_ROOT.'/mfw/mfwModules.php';
require_once APP_ROOT.'/mfw/mfwActions.php';
require_once APP_ROOT.'/mfw/mfwTemplate.php';
require_once APP_ROOT.'/mfw/mfwSession.php';
require_once APP_ROOT.'/mfw/mfwMemcache.php';
require_once APP_ROOT.'/mfw/mfwApc.php';
require_once APP_ROOT.'/mfw/mfwDBConnection.php';
require_once APP_ROOT.'/mfw/mfwDBIBase.php';
require_once APP_ROOT.'/mfw/mfwObject.php';
require_once APP_ROOT.'/mfw/mfwObjectSet.php';
require_once APP_ROOT.'/mfw/mfwObjectDb.php';
require_once APP_ROOT.'/mfw/mfwHttp.php';
//require_once APP_ROOT.'/mfw/mfwOAuth.php';

//古いPHPを使っているのでとりあえず
//require_once APP_ROOT."/vendor/autoload.php";

function apache_log($key,$value)
{
	static $log = array();
	if(function_exists('apache_setenv')){
		$log['env'] = mfwServerEnv::getEnv();
		$log[$key] = $value;
		apache_setenv('LOGMSG',json_encode($log));
	}
}

function var_dump_log($var_name, &$var)
{
	ob_start();
	var_dump($var);
	$var_dump = ob_get_contents();
	ob_end_clean();
	error_log("$var_name: " . $var_dump . "\n", 3, "/tmp/var_dump.log");
}

// Compatibility for PHP 5.5
function array_column($target_data, $column_key, $index_key = null)
{
	if ( is_array($target_data) === FALSE || count($target_data) === 0 )
		return FALSE;
	$result = array();
	foreach ( $target_data as $array ) {
		if ( array_key_exists($column_key, $array) === FALSE )
			continue;
		if ( is_null($index_key) === FALSE && array_key_exists($index_key, $array) === TRUE ) {
			$result[$array[$index_key]] = $array[$column_key];
			continue;
		}
		$result[] = $array[$column_key];
	}

	if (count($result) === 0)
		return FALSE;
	return $result;
}
