<?php
include "Inbenta/Authorization.php";
include "Inbenta/Conversation.php";
include "Swapi/Swapi.php";
session_start();

/**
* Send messages to Chatbot API
**/
class Chatbot {
	function sendMessage($message) {
		// Client has mention the word 'force'?
		if (stripos($message, "force") !== false) {
			$list = Swapi::getFilmList();
			$formattedList = 'The force is in this movies: ';
		  	foreach ($list as $value) {
    			$formattedList .= htmlspecialchars($value).', ';
  			}
  			$formattedList = rtrim($formattedList, ', ') . '.';
			return $formattedList;
		}

		// We have a valid access token?
		if (!isset($_SESSION['access_token'], $_SESSION['access_token_expiration']) 
			|| time() > $_SESSION['access_token_expiration']) {
			// We need a new access token
			$result = Authorization::getAccessToken();
			$_SESSION['access_token'] = $result['accessToken'];
			$_SESSION['access_token_expiration'] = $result['expiration'];
		}

		// We have a conversation token?
		if (!isset($_SESSION['session_token'])) {
			// Get a new session token
			$_SESSION['session_token'] = Conversation::getSessionToken($_SESSION['access_token']);
		}

		// Send message
		$res = Conversation::sendMessage($_SESSION['access_token'], $_SESSION['session_token'], $message);

		// Two consecutive "no results found"?
		if (Conversation::getConsecutiveUnanswered($_SESSION['access_token'], $_SESSION['session_token']) > 1) {
			$list = Swapi::getCharsList();
			$randKeys = array_rand($list, 5);
			$formattedList = 'I haven\'t any results, but here is a list of some Star Wars characters: '.
				$list[$randKeys[0]].', '.
				$list[$randKeys[1]].', '.
				$list[$randKeys[2]].', '.
				$list[$randKeys[3]].', '.
				$list[$randKeys[4]].'.';
			return $formattedList;
		}
		
		return $res['message'];
	}
}		
?>
