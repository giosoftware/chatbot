<?php
require_once __DIR__.'/../CurlHelper.php';

/**
* Manage Swapi API requests
**/
class Swapi {
	const APIURL = 'https://swapi.co/api/';

  public static function getFilmList() {
		$result = CurlHelper::get(
  		self::APIURL.'films/',
  		array(),
  		array()
  	);

  	$res = json_decode($result, true);
  	return array_column($res['results'], 'title');
  }

  public static function getCharsList() {
		$result = CurlHelper::get(
  		self::APIURL.'people/',
  		array(),
  		array()
  	);
  	
  	$res = json_decode($result, true);
  	return array_column($res['results'], 'name');
  }
}

?>
