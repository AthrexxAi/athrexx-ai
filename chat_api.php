<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = json_decode(file_get_contents("php://input"), true);
$userMessage = trim($input["message"] ?? "");

if ($userMessage === "") {
    echo json_encode(["reply" => "Please type something."]);
    exit;
}

$apiKey = "AIzaSyDaS6PzPc--7WT2j8lzjpFI9n7XJR9M6hA"; // replace with your key

// Use valid model ID
$model = "gemini-2.5-flash";

$url = "https://generativelanguage.googleapis.com/v1/models/" . $model . ":generateContent?key=" . $apiKey;

$data = [
    "contents" => [
        ["parts" => [["text" => $userMessage]]]
    ]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($curlError) {
    echo json_encode(["reply" => "⚠️ Network error: " . $curlError]);
    exit;
}

if (isset($result["error"])) {
    $errorMsg = $result["error"]["message"] ?? "Unknown API error.";
    echo json_encode(["reply" => "⚠️ API error: " . $errorMsg]);
    exit;
}

$reply = $result["candidates"][0]["content"]["parts"][0]["text"] ?? "Sorry, I didn’t understand that.";
echo json_encode(["reply" => $reply]);
?>
