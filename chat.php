<?php

// Set the content type to JSON
header('Content-Type: application/json');

// Include the Chatbot class and the config file
require_once 'Chatbot.php';
require_once 'config.php';

// Check if the message is received
if (isset($_POST['message'])) {
    // Get the user's message
    $userMessage = $_POST['message'];

    // Create a new Chatbot instance
    $chatbot = new Chatbot(HUGGING_FACE_API_KEY);

    // Get the chatbot's response
    $botResponse = $chatbot->getResponse($userMessage);

    // Send the response back to the frontend
    echo json_encode(['response' => $botResponse]);
} else {
    // Handle the case where no message is provided
    echo json_encode(['error' => 'No message provided.']);
}
