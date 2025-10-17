<?php
// config.php
// Small config file for SMEPay integration used by example code.
// For production, prefer storing secrets in environment variables or a protected vault.

// NOTE: These keys were present in the original repository. Keep them secure.
$SMEPAY_API_KEY = '8bd912b6FA1569DE';
$SMEPAY_SECRET_KEY = 'e01e3651278e9f4362b58bb6691c1f19ec3f7496e266c6e42c6651d4266732a6';

// Assumed SMEPay API base URL. The original code referenced a JS URL; adjusted to an API-style base.
// If your provider's docs use a different base, update this value.
$SMEPAY_BASE_URL = 'https://typof.co/smepay';

// In production replace above values with getenv('SMEPAY_API_KEY') etc. and ensure HTTPS.

?>
