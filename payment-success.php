<?php
// callback.php

// आप GET या POST parameter पढ़ेंगे — documentation देखें कि SMEPay कैसे redirect करता है
// उदाहरण मान लेते हैं GET parameter में status और order_id होंगे

if (isset($_GET['status']) && isset($_GET['order_id'])) {
    $status = $_GET['status'];
    $order_id = $_GET['order_id'];
} else {
    die("Invalid callback data");
}

// आप चाहें तो SMEPay API से verify कर सकते हैं कि transaction सच है
// (Documentation में “verify payment” endpoint हो सकता है)

// उदाहरण (pseudo):
// $verify_payload = [ "order_id" => $order_id ];
// call SMEPay verify API with same Api-Key, Signature etc. और देखें कि transaction successful है या नहीं.

// यहाँ हम सरलता से दिखा देते हैं:
if ($status === "success") {
    echo "<h1>Payment Success ✅</h1>";
    echo "<p>Order ID: " . htmlspecialchars($order_id) . "</p>";
} else {
    echo "<h1>Payment Failed ❌</h1>";
    echo "<p>Status: " . htmlspecialchars($status) . "</p>";
}
?>