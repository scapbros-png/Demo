<?php
// Enable error display (testing). Production में बंद करें।
ini_set('display_errors', 1);
error_reporting(E_ALL);

// SMEPay API endpoint (as per documentation)
$baseUrl = "https://api.smepay.in/v2";  // यह endpoint documentation के अनुसार हो सकता है

$api_key = "8bd912b6FA1569DE";
$secret_key = "e01e3651278e9f4362b58bb6691c1f19ec3f7496e266c6e42c6651d4266732a6";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access");
}

$order_id = $_POST['order_id'];
$amount = $_POST['amount'];

// Prepare payload as per API documentation
$payload = [
    "order_id" => $order_id,
    "amount" => $amount,
    "currency" => "INR",
    // यहाँ callback/redirect URL डालें जहाँ payment complete होने पर user वापस आएगा
    "redirect_url" => "https://yourdomain.com/callback.php"
];

// Generate signature if required (depends on API spec)
// मान लीजिए documentation कहती है कि आपको signature header में भेजना है:
$payload_json = json_encode($payload);

// Example: signature = HMAC_SHA256(secret_key, payload_json)
$signature = hash_hmac('sha256', $payload_json, $secret_key);

// Prepare headers
$headers = [
    "Content-Type: application/json",
    "Api-Key: $api_key",
    "Signature: $signature"
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/payment/create");  // यहाँ path documentation अनुसार बदल सकती है
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    die("Error connecting to SMEPay API");
}

$result = json_decode($response, true);

// Debug: आप यहाँ response देख सकते हैं
// var_dump($result);

if (isset($result['data']['payment_url'])) {
    // Redirect user to payment page
    header("Location: " . $result['data']['payment_url']);
    exit;
} else {
    echo "Could not initiate payment.<br>";
    echo "Response: " . htmlspecialchars($response);
}
?>