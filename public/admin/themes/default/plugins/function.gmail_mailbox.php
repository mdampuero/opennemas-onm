<?php
function smarty_function_gmail_mailbox($params, &$smarty) {
    // Check gmail inbox
    $mailbox = null;
    $authGmail = $_SESSION['authGmail'];

    $return = '';

    if(isset($authGmail)) {
        $user = new User();
        $mailbox = $user->cache->parseGmailInbox(base64_decode($_SESSION['authGmail']));

        $return = '<div id="user_mailbox">
                    <a href="https://www.google.com/accounts/ServiceLoginAuth?service=mail&Email='
                    .$_SESSION['email'].'&continue=https%3A%2f%2fmail.google.com%2fmail"'
                    .' title="'.sprintf(_("Your have %d emails in Gmail.\nClick here to go to your GMail mailbox\n "),$mailbox['total']).'" target="_blank"><span>'.$mailbox['total'].'&nbsp;</span>'
                    .'<img style="vertical-align:middle" src="'.SITE_URL_ADMIN.'/themes/default/images/indicator-messages.png" border="0" align="absmiddle" /></a>'
                .'</div>';
    }

    return($return);
}
?>
