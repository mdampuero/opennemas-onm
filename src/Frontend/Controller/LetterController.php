<?php
/**
 * Defines the frontend controller for the letter content type
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
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * Renders letters frontpage.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function frontpageAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('letter-frontpage');

        $cacheID = $this->view->generateCacheId('letter-frontpage', '', $page);
        if ($this->view->caching == 0
            || !$this->view->isCached('letter/letter_frontpage.tpl', $cacheID)
        ) {
            $itemsPerPage = 12;

            $order   = array('created' => 'DESC');
            $filters = array(
                'content_type_name' => array(array('value' => 'letter')),
                'content_status'    => array(array('value' => 1)),
                'in_litter'         => array(array('value' => 0)),
            );

            $em           = $this->get('entity_repository');
            $letters      = $em->findBy($filters, $order, $itemsPerPage, $page);
            $countLetters = $em->countBy($filters);

            foreach ($letters as &$letter) {
                $letter->loadAllContentProperties();
                if (!empty($letter->image)) {
                    $letter->photo = $letter->photo;
                }
            }

            $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                array(
                    'page'  => $page,
                    'items' => $itemsPerPage,
                    'total' => $countLetters,
                    'url'   => $this->generateUrl(
                        'frontend_letter_frontpage'
                    )
                )
            );

            $this->view->assign(
                array(
                    'otherLetters' => $letters,
                    'pagination'   => $pagination,
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
        $this->view = new \Template(TEMPLATE_USER);
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
            $letter = $this->get('entity_repository')->find('Letter', $letterId);
            $letter->with_comment = 1;

            if (empty($letter)
                && ($letter->content_status != 1 || $letter->in_litter != 0)
            ) {
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
            }

            $cm = new \ContentManager();

            $otherLetters = $cm->find(
                'Letter',
                'content_status=1 ',
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

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'letter/letter.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showFormAction(Request $request)
    {
        $this->view = new \Template(TEMPLATE_USER);

        return $this->render('letter/letter_form.tpl');
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
        $this->view = new \Template(TEMPLATE_USER);

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
            $response = new RedirectResponse($this->generateUrl('frontend_letter_frontpage').'?msg="'.$msg.'"');

            return $response;
        } else {

            $lettertext    = $request->request->filter('lettertext', '', FILTER_SANITIZE_STRING);
            $security_code = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);

            if (empty($security_code)) {
                $params  = array();
                $data    = array();
                $name    = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
                $subject = $request->request->filter('subject', '', FILTER_SANITIZE_STRING);
                $mail    = $request->request->filter('mail', '', FILTER_SANITIZE_STRING);
                $url     = $request->request->filter('url', '', FILTER_SANITIZE_STRING);
                $items   = $request->request->get('items');

                $moreData = _("Name").": {$name} \n "._("Email"). ": {$mail} \n ";
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        if (!empty($key) && !empty($value)) {
                            $params[$key] = $request->request->filter("items[{$key}]", '', FILTER_SANITIZE_STRING);
                            $moreData .= " {$key}: {$value}\n ";
                        }
                    }
                }

                $data['url']        = $url;
                $data['body']       = iconv(mb_detect_encoding($lettertext), "UTF-8", $lettertext);
                $data['author']     = $name;
                $data['title']      = $subject;
                $data['email']      = $mail;
                $_SESSION['userid'] = 0;
                $data['content_status']  = 0; //pendding
                $data['image']      = $this->saveImage($data);

                $letter = new \Letter();
                $_SESSION['username'] = $data['author'];
                $_SESSION['userid'] = 'user';

                // Prevent XSS attack
                $data = array_map('strip_tags', $data);
                $data['body'] = nl2br($moreData.$data['body']);

                if ($letter->hasBadWords($data)) {
                    $msg = "Su carta fue rechazada debido al uso de palabras malsonantes.";
                } else {
                    $ip = getRealIp();
                    $params['ip']   = $ip;
                    $data["params"] = $params;

                    if ($letter->create($data)) {

                        $msg = "Su carta ha sido guardada y está pendiente de publicación.";

                        $recipient = s::get('contact_email');
                        if (!empty($recipient)) {
                            $mailSender = s::get('mail_sender');
                            if (empty($mailSender)) {
                                $mailSender = "no-reply@postman.opennemas.com";
                            }
                            //  Build the message
                            $text = \Swift_Message::newInstance();
                            $text
                                ->setSubject($subject)
                                ->setBody($data['body'], 'text/html')
                                ->setTo(array($recipient => $recipient))
                                ->setFrom(array($mail => $name))
                                ->setSender(array($mailSender => s::get('site_name')));
                            try {
                                $mailer = $this->get('mailer');
                                $mailer->send($text);

                            } catch (\Swift_SwiftException $e) {
                            }
                        }

                    } else {
                        $msg = "Su carta no ha sido guardada.\nAsegúrese de cumplimentar "
                            ."correctamente todos los campos.";
                    }
                }
            } else {
                $msg = _('Unable to save the letter.');
            }
        }

        $response = new RedirectResponse($this->generateUrl('frontend_letter_frontpage').'?msg="'.$msg.'"');

        return $response;
    }



    /**
     * Uploads and creates
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function saveImage($data)
    {
        // check if category, and file sizes are properly set and category_name is valid
        $category      = 1;
        $category_name = 'fotos';

        $upload        = isset($_FILES['image']) ? $_FILES['image'] : null;
        $info          = array();

        if ($upload) {
            $data = array(
                'local_file'        => $upload['tmp_name'],
                'original_filename' => $upload['name'] ,
                'title'             => $data['title'],
                'fk_category'       => $category,
                'category'          => $category,
                'category_name'     => $category_name,
                'description'       => '',
                'metadata'          => '',
            );

            try {
                $photo = new \Photo();
                $photo = $photo->createFromLocalFile($data);

                return $photo->id;
            } catch (Exception $e) {
                $info [] = array(
                    'error'         => $e->getMessage(),
                );
            }
        }

        return null;
    }


    /**
     * Returns the advertisements for the letters frontpage
     *
     * @return void
     **/
    public function getAds()
    {
        $category = 0;

        // Get letter positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions       = $positionManager->getAdsPositionsForGroup('article_inner', array(7, 9));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
