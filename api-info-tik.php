<?php

function getTikTokUserInfo($username) {
    $url = "https://www.tiktok.com/@$username";

    // Initialize a cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects

    // Execute the cURL request
    $response = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    // Check if the response is empty or the user doesn't exist
    if (!$response || strpos($response, 'Page Not Found') !== false) {
        return [
            'error' => true,
            'message' => 'User not found or profile is private.'
        ];
    }

    // Parse response to extract data (you can use DOMDocument or regular expressions)
    $userInfo = [];

    // Example using regular expressions to extract details
    preg_match('/"id":"(\d+)"/', $response, $idMatch);
    preg_match('/"nickname":"(.*?)"/', $response, $nameMatch);
    preg_match('/"uniqueId":"(.*?)"/', $response, $usernameMatch);
    preg_match('/"followerCount":(\d+)/', $response, $followersMatch);
    preg_match('/"followingCount":(\d+)/', $response, $followingMatch);
    preg_match('/"heartCount":(\d+)/', $response, $likesMatch);
    preg_match('/"signature":"(.*?)"/', $response, $bioMatch);
    preg_match('/"verified":(true|false)/', $response, $verifiedMatch);
    preg_match('/"videoCount":(\d+)/', $response, $videoCountMatch);
    
    // Extract profile picture URL
    preg_match('/"avatarLarger":"(.*?)"/', $response, $avatarMatch);

    // Extracted data or default values
    $userInfo['id'] = $idMatch[1] ?? 'Không rõ';
    $userInfo['name'] = $nameMatch[1] ?? 'Không rõ';
    $userInfo['username'] = $usernameMatch[1] ?? 'Không rõ';
    $userInfo['followers'] = number_format($followersMatch[1] ?? 0);
    $userInfo['following'] = number_format($followingMatch[1] ?? 0);
    $userInfo['likes'] = number_format($likesMatch[1] ?? 0);
    $userInfo['bio'] = $bioMatch[1] ?? 'Không có';
    $userInfo['verified'] = ($verifiedMatch[1] == 'true') ? 'Đã xác minh' : 'Chưa xác minh';
    $userInfo['videos'] = $videoCountMatch[1] ?? 0;
    $userInfo['avatar'] = $avatarMatch[1] ?? 'Không rõ';

    if (empty($userInfo)) {
        return [
            'error' => true,
            'message' => 'Failed to retrieve user information.'
        ];
    }

    return [
        'success' => true,
        'data' => $userInfo
    ];
}

// Usage example
$username = $_GET["username"];
$result = getTikTokUserInfo($username);
header('Content-Type: application/json');
echo json_encode($result);
?>
