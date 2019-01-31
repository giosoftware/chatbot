<?php
include __DIR__.'/../server/Chatbot.php';

// Get JSON message variable
$message = json_decode(file_get_contents('php://input'))->message;

// Pass message to Chatbot and get response
$bot = new ChatBot();
echo $bot->sendMessage($message);

?>