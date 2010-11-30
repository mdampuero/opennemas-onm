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
                    .' title="Go to my GMail mailbox '.$mailbox['total'].'" target="_blank"><span>'.$mailbox['total'].'</span>'
                    .'<img src="'.$RESOURCES_PATH.'images/gmail_ico.png" border="0" align="absmiddle" /></a>'
                .'</div>';
    }

    return($return);
}
?>
