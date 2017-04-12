<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for letters
 *
 * @package Frontend_Controllers
 **/
class LetterController extends Controller
{
    /**
     * Displays a list of letters.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function frontpageAction(Request $request)
    {
        if (!$this->get('core.security')->hasExtension('LETTER_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        $page = $request->query->getDigits('page', 1);

        $this->view->setConfig('letter-frontpage');

        $cacheID = $this->view->generateCacheId('letter-frontpage', '', $page);
        if ($this->view->getCaching() === 0
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

            // Pagination for block more videos
            $pagination = $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $countLetters,
                'route'       => [
                    'name'   => 'frontend_letter_frontpage',
                ]
            ]);

            $this->view->assign([
                'otherLetters' => $letters,
                'pagination'   => $pagination,
            ]);
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('letter/letter_frontpage.tpl', [
            'cache_id'       => $cacheID,
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'recaptcha'      => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'x-tags'         => 'letter-frontpage',
            'x-cache-for'    => '+1 day',
        ]);
    }

    /**
     * Shows a letter.
     *
     * @param string $slug   The letter slug.
     * @param string $id     The letter id.
     *
     * @return Response The response object.
     */
    public function showAction($slug, $id)
    {
        $letter = $this->get('content_url_matcher')
            ->matchContentUrl('letter', $id, $slug);

        if (empty($letter)) {
            throw new ResourceNotFoundException();
        }

        // Setup view
        $this->view->setConfig('letter-inner');

        $cacheID = $this->view->generateCacheId('letter-inner', '', $letter->id);
        if ($this->view->getCaching() === 0
            || !$this->view->isCached('letter/letter.tpl', $cacheID)
        ) {
            $cm = new \ContentManager();
            $otherLetters = $cm->find(
                'Letter',
                'content_status=1 ',
                'ORDER BY created DESC LIMIT 5'
            );

            $this->view->assign(['otherLetters' => $otherLetters]);
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('letter/letter.tpl', [
            'advertisements' => $advertisements,
            'ads_positions'  => $positions,
            'letter'         => $letter,
            'content'        => $letter,
            'contentId'      => $letter->id, // Used on module_comments.tpl
            'cache_id'       => $cacheID,
            'x-tags'         => 'letter,'.$letter->id,
            'x-cache-for'    => '+1 day',
        ]);
    }

    /**
     * Displays a form to send letters to the newspaper.
     *
     * @return Response The response object.
     */
    public function showFormAction()
    {
        list($positions, $advertisements) = $this->getAds();

        return $this->render('letter/letter_form.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'recaptcha'      => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml()
        ]);
    }

    /**
     * Saves a letter into database.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function saveAction(Request $request)
    {
        $response = $request->request->filter('g-recaptcha-response', '', FILTER_SANITIZE_STRING);
        $isValid  = $this->get('core.recaptcha')
            ->configureFromSettings()
            ->isValid($response, $request->getClientIp());

        // What happens when the CAPTCHA was entered incorrectly
        if (!$isValid) {
            $msg = _("The reCAPTCHA wasn't entered correctly. Go back and try it again.");
            $response = new RedirectResponse($this->generateUrl('frontend_letter_frontpage').'?msg="'.$msg.'"');

            return $response;
        }

        $lettertext    = $request->request->filter('lettertext', '', FILTER_SANITIZE_STRING);
        $security_code = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);
        $msg           = _('Unable to save the letter.');

        if (empty($security_code)) {
            $params  = array();
            $data    = array();
            $name    = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
            $subject = $request->request->filter('subject', '', FILTER_SANITIZE_STRING);
            $mail    = $request->request->filter('mail', '', FILTER_SANITIZE_STRING);
            $url     = $request->request->filter('url', '', FILTER_SANITIZE_STRING);
            $items   = $request->request->get('items');

            $moreData = _("Name")." {$name} \n "._("Email"). "{$mail} \n ";
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    if (!empty($key) && !empty($value)) {
                        $params[$key] = $request->request->filter("items[{$key}]", '', FILTER_SANITIZE_STRING);
                        $moreData .= " {$key}: {$value}\n ";
                    }
                }
            }

            $data['url']            = $url;
            $data['body']           = iconv(mb_detect_encoding($lettertext), "UTF-8", $lettertext);
            $data['author']         = $name;
            $data['title']          = $subject;
            $data['email']          = $mail;
            $data['content_status'] = 0; //pendding
            $data['image']          = $this->saveImage($data);

            $letter = new \Letter();

            $request->getSession()->set(
                'user',
                json_decode(json_encode([ 'id' => 'user', 'username' => $data['author'] ]))
            );

            // Prevent XSS attack
            $data = array_map('strip_tags', $data);
            $data['body'] = nl2br($moreData.$data['body']);

            if ($letter->hasBadWords($data)) {
                $msg = "Su carta fue rechazada debido al uso de palabras malsonantes.";
            } else {
                $ip = getUserRealIP();
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

                            $this->get('application.log')->notice(
                                "Email sent. Frontend letter (sender:".$mail.", to: ".$recipient.")"
                            );
                        } catch (\Swift_SwiftException $e) {
                        }
                    }
                } else {
                    $msg = "Su carta no ha sido guardada.\nAsegúrese de cumplimentar "
                        ."correctamente todos los campos.";
                }
            }
        }

        $response = new RedirectResponse($this->generateUrl('frontend_letter_frontpage').'?msg="'.$msg.'"');

        return $response;
    }

    /**
     * Uploads and creates an image.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
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
     * Returns the advertisements for the letters frontpage.
     *
     * @return array The list of advertisements.
     */
    public function getAds()
    {
        // Get letter positions
        $positionManager = $this->get('core.manager.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7 ]);
        $advertisements  = \Advertisement::findForPositionIdsAndCategory($positions, 0);

        return [ $positions, $advertisements ];
    }
}
