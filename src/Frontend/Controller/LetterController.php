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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Handles the actions for letters
 *
 * @package Frontend_Controllers
 */
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

        // Setup templating cache layer
        $this->view->setConfig('letter-frontpage');
        $cacheID = $this->view->getCacheId('frontpage', 'letter', $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('letter/letter_frontpage.tpl', $cacheID)
        ) {
            $itemsPerPage = 12;

            $order   = [ 'created' => 'DESC' ];
            $filters = [
                'content_type_name' => [[ 'value' => 'letter' ]],
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 0 ]],
                'starttime'       => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ];

            $em           = $this->get('entity_repository');
            $letters      = $em->findBy($filters, $order, $itemsPerPage, $page);
            $countLetters = $em->countBy($filters);

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

        $this->getAds();

        return $this->render('letter/letter_frontpage.tpl', [
            'cache_id'    => $cacheID,
            'recaptcha'   => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'x-tags'      => 'letter-frontpage',
            'x-cacheable' => true,
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
        $cacheID = $this->view->getCacheId('content', $letter->id);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('letter/letter.tpl', $cacheID)
        ) {
            $order   = [ 'created' => 'DESC' ];
            $filters = [
                'content_type_name' => [[ 'value' => 'letter' ]],
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 0 ]],
                'starttime'       => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ],
            ];

            $otherLetters = $this->get('entity_repository')->findBy($filters, $order, 5, 1);

            $this->view->assign(['otherLetters' => $otherLetters]);
        }

        $this->getAds();

        return $this->render('letter/letter.tpl', [
            'cache_id'    => $cacheID,
            'content'     => $letter,
            'contentId'   => $letter->id,
            'letter'      => $letter,
            'o_content'   => $letter,
            'x-tags'      => 'letter,' . $letter->id,
            'x-cacheable' => true,
            'tags'        => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($letter->tags)['items']
        ]);
    }

    /**
     * Displays a form to send letters to the newspaper.
     *
     * @return Response The response object.
     */
    public function showFormAction()
    {
        $this->getAds();

        return $this->render('letter/letter_form.tpl', [
            'recaptcha' => $this->get('core.recaptcha')
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
            $msg      = _("The reCAPTCHA wasn't entered correctly. Go back and try it again.");
            $response = new RedirectResponse($this->generateUrl('frontend_letter_frontpage') . '?msg="' . $msg . '"');

            return $response;
        }

        $lettertext    = $request->request->filter('lettertext', '', FILTER_SANITIZE_STRING);
        $security_code = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);
        $msg           = _('Unable to save the letter.');

        if (!empty($security_code)) {
            return new RedirectResponse($this->generateUrl('frontend_letter_frontpage') . '?msg="' . $msg . '"');
        }

        $params  = [];
        $data    = [];
        $name    = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
        $subject = $request->request->filter('subject', '', FILTER_SANITIZE_STRING);
        $email   = $request->request->filter('mail', '', FILTER_SANITIZE_STRING);
        $url     = $request->request->filter('url', '', FILTER_SANITIZE_STRING);
        $items   = $request->request->get('items');

        $moreData = _("Name") . ": {$name} \n " . _("Email") . ": {$email} \n ";
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                if (!empty($key) && !empty($value)) {
                    $params[$key] = $request->request->filter("items[{$key}]", '', FILTER_SANITIZE_STRING);
                    $moreData    .= " {$key}: {$value}\n ";
                }
            }
        }

        $data = [
            'author'         => $url,
            'body'           => iconv(mb_detect_encoding($lettertext), "UTF-8", $lettertext),
            'content_status' => 0, // pending status
            'email'          => $email,
            'image'          => $this->saveImage($request),
            'title'          => $subject,
            'url'            => $url,
        ];

        // Prevent XSS attack
        $data         = array_map('strip_tags', $data);
        $data['body'] = nl2br($moreData . $data['body']);

        $data['params'] = [
            'ip' => getUserRealIP(),
        ];

        $letter = new \Letter();

        if ($letter->create($data)) {
            $msg = _("Your letter has been saved and is awaiting publication.");

            $settings = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get(['contact_email', 'site_name']);

            $recipient = $settings['contact_email'];
            if (!empty($recipient)) {
                $mailSender = $this->getParameter('mailer_no_reply_address');

                //  Build the message
                $text = \Swift_Message::newInstance();
                $text
                    ->setSubject('[' . _('Letter to the editor') . '] ' . $subject)
                    ->setBody($data['body'], 'text/html')
                    ->setTo([ $recipient => $recipient ])
                    ->setFrom([ $mailSender => $settings['site_name'] ])
                    ->setSender([ $mailSender => $settings['site_name'] ]);

                $headers = $text->getHeaders();
                $headers->addParameterizedHeader(
                    'ACUMBAMAIL-SMTPAPI',
                    $this->get('core.instance')->internal_name . ' - Letter'
                );

                try {
                    $this->get('mailer')->send($text);

                    $this->get('application.log')->notice(
                        "Email sent. Frontend letter (From:" . $email . ", to: " . $recipient . ")"
                    );
                } catch (\Exception $e) {
                    $this->get('application.log')->notice(
                        "Email NOT sent. Frontend letter (From:" . $email . ", to: " . $recipient . "):"
                        . $e->getMessage()
                    );
                }
            }
        } else {
            $msg = _("Your letter has not been saved, make sure you fill in all the fields correctly.");
        }

        return new RedirectResponse($this->generateUrl('frontend_letter_frontpage') . '?msg="' . $msg . '"');
    }

    /**
     * Uploads and creates an image.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function saveImage(Request $request)
    {
        $file = $request->files->get('image');
        $ps   = $this->get('api.service.photo');

        if (empty($file)) {
            return null;
        }

        try {
            return $ps->createItem([], $file)->pk_content;
        } catch (\Exception $e) {
            $this->get('error.log')->error('Unable to save letter image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Loads the list of positions and advertisements on renderer service.
     */
    public function getAds()
    {
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7 ]);
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
    }
}
