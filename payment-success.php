<?php
require_once __DIR__ . '/config.php';

// Accept both GET and POST depending on provider
$data = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;

if (empty($data) || !isset($data['order_id'])) {
    http_response_code(400);
    die("Invalid callback data");
}

$order_id = preg_replace('/[^a-zA-Z0-9-_]/', '', $data['order_id']);
$status = isset($data['status']) ? $data['status'] : 'unknown';

// If provider sends a signature (recommended), verify it using secret key
if (isset($data['signature'])) {
    $received_sig = $data['signature'];
    // Build a simple verification string — exact method depends on provider docs.
    $verify_payload = json_encode(['order_id' => $order_id, 'status' => $status]);
    $expected_sig = hash_hmac('sha256', $verify_payload, $SMEPAY_SECRET_KEY);

    $verified = hash_equals($expected_sig, $received_sig);
} else {
    $verified = false; // unknown unless we call verify API
}

// Simple display
if ($status === 'success') {
    echo "<h1>Payment Success ✅</h1>";
    echo "<p>Order ID: " . htmlspecialchars($order_id) . "</p>";
    if ($verified) {
        echo "<p>Signature verified.</p>";
    } else {
        echo "<p>Signature not present or not verified. Consider calling the provider's verify API to confirm.</p>";
    }
} else {
    echo "<h1>Payment Failed ❌</h1>";
    echo "<p>Status: " . htmlspecialchars($status) . "</p>";
    if ($verified) {
        echo "<p>Signature verified.</p>";
    }
}

?>