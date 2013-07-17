<?php

/** Autoloads PHP last.fm API classes
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
function lastfm_autoload($name){
	if(stripos($name, 'Cache') !== false){
		$filename = realpath(sprintf("%s/cache/%s.php", dirname(__FILE__), $name));
	}
	else if(stripos($name, 'Caller') !== false){
		$filename = realpath(sprintf("%s/caller/%s.php", dirname(__FILE__), $name));
	}
	else{
		$filename = realpath(sprintf("%s/%s.php", dirname(__FILE__), $name));
	}

	if(file_exists($filename) && is_file($filename)){
		require_once $filename;
	}
	else{
		throw new Exception("File '".$filename."' not found!");
	}

	if(!class_exists($name, false) && !interface_exists($name, false)){
		throw new Exception("Class '".$name."' not found!");
	}
}

spl_autoload_register('lastfm_autoload');
