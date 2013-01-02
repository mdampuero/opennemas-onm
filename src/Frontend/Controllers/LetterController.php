<?php
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
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
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
     * Description of the action
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

        require_once APP_PATH.'/../public/controllers/letter_advertisement.php';

        return $this->render(
            'letter/letter_frontpage.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Description of the action
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
                    'num_comments' => count($comments),
                    'otherLetters' => $otherLetters,
                )
            );

        }

        // require_once APP_PATH.'/../public/controllers/letter_inner_advertisement.php';
        $this->getAds();


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
     * @return Response the response object
     **/
    public function saveAction(Request $request)
    {
        $recaptcha_challenge_field =
            $request->request->filter('recaptcha_challenge_field', '', FILTER_SANITIZE_STRING);
        $recaptcha_response_field =
            $request->request->filter('recaptcha_response_field', '', FILTER_SANITIZE_STRING);

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
            $msg = "reCAPTCHA no fue introducido correctamente. Intentelo de nuevo.";
        } else {
            $lettertext    = $request->request->filter('lettertext', '', FILTER_SANITIZE_STRING);
            $security_code = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);

            if (!empty($lettertext) && empty($security_code) ) {
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
                $msg = $letter->saveLetter($data);

            } else {
                $msg = _('Su Carta al Director <strong>no</strong> ha sido guardada.');
            }
        }

        return new Response($msg);
    }

    /**
     * Gets the advertisement
     *
     * @return void
     **/
    public function getAds()
    {
        $this->ccm = \ContentCategoryManager::get_instance();

        $category = (!isset($category) || ($category=='home'))? 0: $category;
        $advertisement = \Advertisement::getInstance();

        // Load internal banners, principal banners (1,2,3,11,13) and use cache to performance
        /* $banners = $advertisement->cache->getAdvertisements(array(1, 2, 3, 10, 12, 11, 13), $category); */
        $banners = $advertisement->getAdvertisements(array(101, 102, 103, 105, 109, 110), $category);
        $cm = new \ContentManager();
        $banners = $cm->getInTime($banners);
        //$advertisement->renderMultiple($banners, &$tpl);
        $advertisement->renderMultiple($banners, $advertisement);

        $intersticial = $advertisement->getIntersticial(150, "$category");
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement);
        }
    }
}
