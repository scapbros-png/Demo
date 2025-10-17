<?php
// Enable error display (testing). Turn off in production.
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';

// Use config values
$baseUrl = rtrim($SMEPAY_BASE_URL, '/');
$api_key = $SMEPAY_API_KEY;
$secret_key = $SMEPAY_SECRET_KEY;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Invalid access");
}

// Basic sanitization and validation
$order_id = isset($_POST['order_id']) ? preg_replace('/[^a-zA-Z0-9-_]/', '', $_POST['order_id']) : null;
$amount = isset($_POST['amount']) ? (int) $_POST['amount'] : 0;
$customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? filter_var($_POST['customer_email'], FILTER_VALIDATE_EMAIL) : false;

if (!$order_id || $amount <= 0 || !$customer_email) {
    die("Missing or invalid input data");
}

// Build the redirect URL back to our site after payment completes
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
$redirect_url = $scheme . '://' . $host . $basePath . '/payment-success.php';

// Prepare payload as per API documentation (adjust fields to match your provider)
$payload = [
    "order_id" => $order_id,
    "amount" => $amount,
    "currency" => "INR",
    "customer_name" => $customer_name,
    "customer_email" => $customer_email,
    "redirect_url" => $redirect_url
];

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
// NOTE: This path is an assumption; update to match SMEPay's API docs if different.
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/api/v1/payment/create");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err = curl_error($ch);
curl_close($ch);

if ($response === false) {
    die("Error connecting to SMEPay API: " . htmlspecialchars($curl_err));
}

$result = json_decode($response, true);

// Debugging aid (uncomment during development)
// file_put_contents(__DIR__ . '/last_response.json', $response);

if (isset($result['data']['payment_url'])) {
    // Redirect user to payment page
    header("Location: " . $result['data']['payment_url']);
    exit;
} else {
    echo "Could not initiate payment.<br>";
    echo "HTTP code: " . htmlspecialchars($http_code) . "<br>";
    echo "Response: " . htmlspecialchars($response);
}
?>