<?php
// Success page
if(isset($_GET['status']) && $_GET['status'] == 'success') {
    echo "Payment Successful ✅";
} else {
    echo "Payment Failed ❌";
}
?>