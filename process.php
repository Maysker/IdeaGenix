<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Your API keys and URLs
$serpApiKey = "b17f788e6ef31219e826cb71203a4b27d9cd970cac356e5059f4f63fdfd314f8";
$cahereApiKey = "1TxUBXDK0zgOTpRDPh8Ic9mdlsn6qZF5QcWIxgOd";
$serpApiUrl = "https://serpapi.com/search.json?engine=google_trends&q=";
$cahereUrl = "https://api.cohere.ai/v1/generate";

// Path to the certificate file
$certPath = 'C:\\MAMP\\htdocs\\oef-PHP\\IdeaGenix\\IncludedRootsPEM.txt'; // Make sure to update the certificate file path

// Getting data from the POST request
$topic = $_POST['topic'] ?? '';
$keywords = $_POST['keywords'] ?? '';
$contentType = $_POST['contentType'] ?? '';
$audience = $_POST['audience'] ?? '';

// Function to query SerpAPI
function getGoogleTrends($topic, $serpApiKey, $serpApiUrl, $certPath) {
    $curl = curl_init();
    $url = $serpApiUrl . urlencode($topic) . "&api_key=" . $serpApiKey;

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CAINFO => $certPath,
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        return json_decode($response, true);
    }
}

// Function to query Cahere API
function generateContentIdeas($topic, $keywords, $contentType, $audience, $cahereApiKey, $cahereUrl, $certPath) {
    $prompt = "Please, generate {$contentType} ideas on the topic '{$topic}' with keywords '{$keywords}', that would interest the '{$audience}' audience. Make sure each idea is clearly separated. The content should be relevant to both classical and modern aspects of '{$topic}'.";


    $curl = curl_init();
    $postData = json_encode([
        'prompt' => $prompt,
        'max_tokens' => 400,
        'temperature' => 0.5,
    ]);

    curl_setopt_array($curl, [
        CURLOPT_URL => $cahereUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $cahereApiKey",
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CAINFO => $certPath,
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        return json_decode($response, true);
    }
}

// Performing requests
$trendsData = getGoogleTrends($topic, $serpApiKey, $serpApiUrl, $certPath);
$contentIdeas = generateContentIdeas($topic, $keywords, $contentType, $audience, $cahereApiKey, $cahereUrl, $certPath);

// Sending response to the client
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'trendsData' => $trendsData,
    'contentIdeas' => $contentIdeas,
]);
?>
