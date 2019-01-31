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

		// No results found?
		if ($res['isNoResult']) {
			if (!isset($_SESSION['noresult_count'])) {
				$_SESSION['noresult_count'] = 0;
			}
			$_SESSION['noresult_count'] += 1;
		} else {
			$_SESSION['noresult_count'] = 0;
		}

		// 2 consecutive no_results?
		if ($_SESSION['noresult_count'] >= 2) {
			$_SESSION['noresult_count'] = 0;
			$list = Swapi::getCharsList();
			$randKeys = array_rand($list, 3);
			$formattedList = 'I haven\'t any results, but here is a list of some Star Wars characters: '.
				$list[$randKeys[0]].', '.
				$list[$randKeys[1]].', '.
				$list[$randKeys[2]].'.';
			return $formattedList;
		}

		return $res['message'];
	}
}		
?>
