<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Onm\Settings as s;

// tell PHP to log errors to ipn_errors.log in this directory
ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');

// intantiate the IPN listener
include(SITE_VENDOR_PATH.'/Paypal/ipnlistener.php');
$listener = new IpnListener();

// tell the IPN listener to use the PayPal test sandbox
$listener->use_sandbox = true;

// try to process the IPN POST
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}

if ($verified) {

    $errmsg = '';   // stores errors from fraud checks

    // 1. Make sure the payment status is "Completed"
    if ($_POST['payment_status'] != 'Completed') {
        // simply ignore any IPN that is not completed
        exit(0);
    }

    // 2. Make sure seller email matches your primary account email.
    $paypalMail = s::get("paypal_settings");
    if ($_POST['receiver_email'] != $paypalMail['email']) {
        $errmsg .= "'receiver_email' does not match: ";
        $errmsg .= $_POST['receiver_email']."\n";
    }

    // 3. Make sure the amount(s) paid match
    if ($_POST['mc_gross'] != '3.50') {
        $errmsg .= "'mc_gross' does not match: ";
        $errmsg .= $_POST['mc_gross']."\n";
    }

    // 4. Make sure the currency code matches
    if ($_POST['mc_currency'] != 'EUR') {
        $errmsg .= "'mc_currency' does not match: ";
        $errmsg .= $_POST['mc_currency']."\n";
    }

    // 5. Ensure the transaction is not a duplicate.
    mysql_connect(BD_HOST, BD_USER, BD_PASS) or exit(0);
    mysql_select_db(BD_DATABASE) or exit(0);

    $txn_id = mysql_real_escape_string($_POST['txn_id']);
    $sql = "SELECT COUNT(*) FROM orders WHERE payment_id = '$txn_id'";
    $r = mysql_query($sql);

    if (!$r) {
        error_log(mysql_error());
        exit(0);
    }

    $exists = mysql_result($r, 0);
    mysql_free_result($r);

    if ($exists) {
        $errmsg .= "'This transaction_id' has already been processed: ".$_POST['txn_id']."\n";
    }

    if (!empty($errmsg)) {
        // manually investigate errors from the fraud checking
        $body = "IPN failed fraud checks: \n$errmsg\n\n";
        $body .= $listener->getTextReport();
        mail($paypalMail['email'], 'IPN Fraud Warning', $body);
    } else {
        // assign posted variables to local variables
        $data['item_name']          = $_POST['item_name'];
        $data['item_number']        = $_POST['item_number'];
        $data['payment_status']     = $_POST['payment_status'];
        $data['payment_amount']     = $_POST['mc_gross'];
        $data['payment_currency']   = $_POST['mc_currency'];
        $data['txn_id']             = $_POST['txn_id'];
        $data['receiver_email']     = $_POST['receiver_email'];
        $data['payer_email']        = $_POST['payer_email'];


        // add this order to a table of completed orders
        $payer_email = mysql_real_escape_string($_POST['payer_email']);
        $mc_gross = mysql_real_escape_string($_POST['mc_gross']);

        $sql = "INSERT INTO orders
                (`user_id`, `content_id`, `created`, `state`, `payment_id`,
                 `payment_amount`, `payment_method`, `params`)
                VALUES ($, '$txn_id', '$payer_email', $mc_gross)";

        if (!mysql_query($sql)) {
            error_log(mysql_error());
            exit(0);
        }

        // send user an email with a link to their digital download
        $to = filter_var($_POST['payer_email'], FILTER_SANITIZE_EMAIL);
        $subject = "Your digital download is ready";
        mail($to, "Thank you for your order", "Download URL: ...");
    }

} else {
    // manually investigate the invalid IPN
    mail('YOUR EMAIL ADDRESS', 'Invalid IPN', $listener->getTextReport());
}

