<?php
function smarty_function_gmail_mailbox($params, &$smarty) {
    // Check gmail inbox
    $mailbox = null;
    if (!isset($_SESSION['authGmail']) && !empty($_SESSION['authGmail'])) {
        $authGmail = $_SESSION['authGmail'];
    }

    $return = '';

    if(isset($authGmail) && !empty($authGmail)) {
        $user = new User();
        $mailbox = $user->cache->parseGmailInbox(base64_decode($smarty.session.authGmail));

        if (isset($params['prepend_html'])) {
            $return = $params['prepend_html'];
        }
        
        $return .= '<a href="https://www.google.com/accounts/ServiceLoginAuth?service=mail&Email='
                    .$_SESSION['email'].'&continue=https%3A%2f%2fmail.google.com%2fmail"'
                    .' title="'.sprintf(_("Your have %d emails in Gmail.\nClick here to go to your GMail mailbox\n "),$mailbox['total']).'" target="_blank"><span>'.$mailbox['total'].'&nbsp;</span>'
                    .'<img style="vertical-align:middle" src="'.SITE_URL_ADMIN.'/themes/default/images/indicator-messages.png" border="0" align="absmiddle" /></a>';
        
        if (isset($params['append_html'])) {
            $return = $params['append_html'];
        }
    }

    return($return);
}
?>
