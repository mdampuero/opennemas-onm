<?php
/**
 * Handles the actions for letters
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for letters
 *
 * @package Frontend_Controllers
 **/
class LetterController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Renders letters frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        $this->page = $request->query->filter('page', '0', FILTER_SANITIZE_STRING);

        $cm = new \ContentManager();

        $this->view->setConfig('letter-frontpage');

        $cacheID = $this->view->generateCacheId('letter-frontpage', '', $this->page);
        if ($this->view->caching == 0
            || !$this->view->isCached('letter/letter-frontpage.tpl', $cacheID)
        ) {
            $otherLetters = $cm->find_all(
                'Letter',
                'available=1 ',
                'ORDER BY created DESC LIMIT 5'
            );

            $this->view->assign(
                array(
                    'otherLetters'=> $otherLetters
                )
            );
        }

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'letter/letter_frontpage.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Shows a letter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->view->setConfig('letter-inner');
        $dirtyID = $request->query->filter('id', '', FILTER_SANITIZE_STRING);

        $letterId = \Content::resolveID($dirtyID);

        if (empty($letterId)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $cacheID = $this->view->generateCacheId('letter-inner', '', $letterId);
        if ($this->view->caching == 0
            || !$this->view->isCached('letter/letter.tpl', $cacheID)
        ) {
            $letter = new \Letter($letterId);

            if (empty($letter)
                && ($letter->available != 1 || $letter->in_litter != 0)
            ) {
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
            }

            $cm = new \ContentManager();

            $otherLetters = $cm->find(
                'Letter',
                'available=1 ',
                'ORDER BY created DESC LIMIT 5'
            );

            $this->view->assign('contentId', $letterId); // Used on module_comments.tpl
            $this->view->assign(
                array(
                    'letter'       => $letter,
                    'content'      => $letter,
                    'otherLetters' => $otherLetters,
                )
            );
        }

        $ads = $this->getAds('inner');
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'letter/letter.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Saves a letter into database
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function saveAction(Request $request)
    {
        require_once 'recaptchalib.php';

        $recaptcha_challenge_field =
            $request->request->filter('recaptcha_challenge_field', '', FILTER_SANITIZE_STRING);
        $recaptcha_response_field =
            $request->request->filter('recaptcha_response_field', '', FILTER_SANITIZE_STRING);

        //Get config vars
        $configRecaptcha = s::get('recaptcha');

        // Get reCaptcha validate response
        $resp = \recaptcha_check_answer(
            $configRecaptcha['private_key'],
            $_SERVER["REMOTE_ADDR"],
            $recaptcha_challenge_field,
            $recaptcha_response_field
        );

        // What happens when the CAPTCHA was entered incorrectly
        if (!$resp->is_valid) {
            $msg = "reCAPTCHA no fue introducido correctamente. Intentelo de nuevo.";
        } else {
            $lettertext    = $request->request->filter('lettertext', '', FILTER_SANITIZE_STRING);
            $security_code = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);

            if (!empty($lettertext) && empty($security_code)) {
                $data = array();
                $name    = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
                $subject = $request->request->filter('subject', '', FILTER_SANITIZE_STRING);
                $mail    = $request->request->filter('mail', '', FILTER_SANITIZE_STRING);

                $data['body']      = $lettertext;
                $data['author']    = $name;
                $data['title']     = $subject;
                $data['email']     = $mail;
                $data['available'] = 0; //pendding

                $letter = new \Letter();
                $msg = $letter->saveLetter($data);

            } else {
                $msg = _('<strong>Unable</strong> to save the letter.');
            }
        }

        return new Response($msg);
    }

    /**
     * Returns the advertisements for the letters frontpage
     *
     * @return void
     **/
    public function getAds($position = '')
    {
        $category = 0;

        // I have added the element 150 in order to integrate all the code in the same query
        if ($position == 'inner') {
            $positions = array(7, 9, 150, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 191, 192, 193);
        } else {
            $positions = array(50, 1, 2, 103, 105, 5, 6, 7, 9, 91, 92);
        }

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
