<?php
// SMEPay credentials
$api_key = "8bd912b6FA1569DE";
$secret_key = "e01e3651278e9f4362b58bb6691c1f19ec3f7496e266c6e42c6651d4266732a6";

$order_id = $_POST['order_id'];
$amount = $_POST['amount'];

// SMEPay API endpoint
$api_url = "https://api.smepay.in/payment/create";

// Create payment request data
$data = [
    "api_key" => $api_key,
    "order_id" => $order_id,
    "amount" => $amount,
    "currency" => "INR",
    "redirect_url" => "https://yourwebsite.com/payment-success.php"
];

// CURL to create payment
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// Redirect to SMEPay payment page
if(isset($result['payment_url'])) {
    header("Location: " . $result['payment_url']);
    exit;
} else {
    echo "Payment initialization failed!";
}
?>