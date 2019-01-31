<?php
require_once __DIR__.'/../CurlHelper.php';
require_once __DIR__.'/Authorization.php';

/**
* Manage API Conversation calls
**/
class Conversation {
	const CONVURL = 'https://api-gce3.inbenta.io/prod/chatbot/v1/conversation';

  public static function getSessionToken($accessToken) {
		$result = CurlHelper::post(
  		self::CONVURL,
  		array(
				'x-inbenta-key: '.Authorization::AUTHKEY,
				'Authorization: Bearer '.$accessToken
  		),
  		""
  	);

  	$res = json_decode($result);

  	return $res->sessionToken;
  }

  public static function sendMessage($accessToken, $sessionToken, $message) {
		$result = CurlHelper::post(
  		self::CONVURL."/message",
  		array(
				'x-inbenta-key: '.Authorization::AUTHKEY,
				'Authorization: Bearer '.$accessToken,
				'x-inbenta-session: Bearer '.$sessionToken,
				'Content-Type: application/json'
  		),
  		array(
  			'message' => $message
  		)
  	);

  	$res = json_decode($result);

    // ON error get a new session token 
    if ( isset($res->errors) ) {
      $sessionToken = Conversation::getSessionToken($accessToken);
      $_SESSION['session_token'] = $sessionToken;
      return Conversation::sendMessage($accessToken, $sessionToken, $message);
		}
		
		// Message is deprecated. Use messageList instead
		foreach ($res->answers[0]->messageList as $value) {
			$botMessage .= htmlspecialchars($value).'\n';
		}
		$botMessage = rtrim($botMessage, '\n');

  	return array(
  		'message' => $botMessage,
  		'isNoResult' => in_array('no-results', $res->answers[0]->flags, true)
  	);
	}
	
	public static function getHistory($accessToken, $sessionToken, $message) {
		$result = CurlHelper::get(
  		self::CONVURL."/history",
  		array(
				'x-inbenta-key: '.Authorization::AUTHKEY,
				'Authorization: Bearer '.$accessToken,
				'x-inbenta-session: Bearer '.$sessionToken,
				'Content-Type: application/json'
  		),
  		array()
  	);

		$res = json_decode($result);
		return $res;
	}

}

?>
