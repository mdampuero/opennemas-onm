<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s;

/**
 * Start up and setup the app
*/
require_once '../bootstrap.php';
require_once 'recaptchalib.php';

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$cm  = new ContentManager();

/**
 * Setting up available categories for menu.
*/
 /******************************  *********************************/

$action = $request->query->filter('action', 'frontpage', FILTER_SANITIZE_STRING);

switch ($action) {
    case 'frontpage':

        $tpl->setConfig('letter-frontpage');

        $page = $request->query->filter('page', '0', FILTER_SANITIZE_STRING);

        $cacheID = $tpl->generateCacheId('letter-frontpage', '', $page);

        /**
         * Don't execute action logic if was cached before
         */
        if (1==1 ||  ($tpl->caching == 0)
           || (!$tpl->isCached('letter/letter-frontpage.tpl', $cacheID))
        ) {

            $otherLetters = $cm->find_all(
                'Letter',
                'available=1 ',
                'ORDER BY created DESC LIMIT 5'
            );

            $tpl->assign(array('otherLetters'=> $otherLetters));
        }

        require_once 'letter_advertisement.php';

        $tpl->display('letter/letter_frontpage.tpl', $cacheID);

        break;
    case 'show':

        $tpl->setConfig('letter-inner');
        $dirtyID = $request->query->filter('id', '', FILTER_SANITIZE_STRING);

        $letterId = Content::resolveID($dirtyID);

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($letterId)) {
            Application::forward301('/cartas-al-director/');
        }

        $letter = new Letter($letterId);

        if (empty($letter)) {
            Application::forward301('/404.html');
        }

        if (($letter->available==1) && ($letter->in_litter==0)) {
            // Increment numviews if it's accesible
            $tpl->assign('contentId', $letterId);

            $cacheID = $tpl->generateCacheId('letter-inner', '', $letterId);

            if (1==1 || ($tpl->caching == 0)
                || !$tpl->isCached('letter/letter.tpl', $cacheID)
            ) {

                $comment  = new Comment();
                $comments = $comment->get_public_comments($letterId);

                $otherLetters = $cm->find(
                    'Letter',
                    'available=1 ',
                    'ORDER BY created DESC LIMIT 5'
                );

                $tpl->assign(
                    array(
                        'letter'       => $letter,
                        'num_comments' => count($comments),
                        'otherLetters' => $otherLetters,
                    )
                );

            } // end if $tpl->is_cached

            require_once 'letter_inner_advertisement.php';

            $tpl->assign('contentId', $letterId); // Used on module_comments.tpl

            $tpl->display('letter/letter.tpl', $cacheID);

        }

        break;
    case 'save_letter':

        $recaptcha_challenge_field = $request->request->
                filter('recaptcha_challenge_field', '', FILTER_SANITIZE_STRING);
        $recaptcha_response_field = $request->request->
                filter('recaptcha_response_field', '', FILTER_SANITIZE_STRING);

        //Get config vars
        $configRecaptcha = s::get('recaptcha');

        // Get reCaptcha validate response
        $resp = recaptcha_check_answer(
            $configRecaptcha['private_key'],
            $_SERVER["REMOTE_ADDR"],
            $recaptcha_challenge_field,
            $recaptcha_response_field
        );

        // What happens when the CAPTCHA was entered incorrectly
        if (!$resp->is_valid) {
            $msg="reCAPTCHA no fue introducido correctamente. Intentelo de nuevo.";
            echo($msg);
        } else {

            $lettertext    = $request->request->filter('lettertext', '', FILTER_SANITIZE_STRING);
            $security_code = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);

            if (!empty($lettertext) && empty($security_code) ) {

                /*  Anonymous comment ************************* */
                $data = array();
                $name    = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
                $subject = $request->request->filter('subject', '', FILTER_SANITIZE_STRING);
                $mail    = $request->request->filter('mail', '', FILTER_SANITIZE_STRING);

                $data['body']      = $lettertext;
                $data['author']    = $name;
                $data['title']     = $subject;
                $data['email']     = $mail;
                $data['available'] = 0; //pendding

                $letter = new Letter();
                $msg =  $letter->saveLetter($data);

            } else {
                $msg = _('Su Carta al Director <strong>no</strong> ha sido guardada.');
            }
            echo $msg;
        }

        break;
    default:
        //  Application::forward301('index.php');
        break;
}

