<?php

/**
 * Class Chatbot
 *
 * This class handles the logic for the chatbot, including communication
 * with the Hugging Face API to get responses.
 */
class Chatbot
{
    /**
     * @var string The URL of the Hugging Face API endpoint.
     */
    private $apiUrl;

    /**
     * @var string The Hugging Face API key.
     */
    private $apiKey;

    /**
     * Chatbot constructor.
     *
     * @param string $apiKey The Hugging Face API key.
     * @param string $model The model to use for the chatbot.
     */
    public function __construct($apiKey, $model = 'google/flan-t5-base')
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = "https://api-inference.huggingface.co/models/" . $model;
    }

    /**
     * Get a response from the chatbot.
     *
     * @param string $message The user's message.
     * @return string The chatbot's response.
     */
    public function getResponse($message)
    {
        // Prepare the data for the API request.
        $data = [
            'inputs' => $message,
        ];

        // Use cURL to send the request to the Hugging Face API.
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Handle the API response.
        $response = json_decode($result, true);

        if ($httpCode == 200) {
            // The response for text generation is an array with one element.
            if (isset($response[0]['generated_text'])) {
                return $response[0]['generated_text'];
            }
        } else {
            // If there's an error, the API often returns a JSON with an 'error' key.
            if (isset($response['error'])) {
                return 'API Error: ' . $response['error'];
            }
        }

        // Return a default error message if something goes wrong.
        return 'Sorry, I could not get a response. HTTP Code: ' . $httpCode;
    }
}
