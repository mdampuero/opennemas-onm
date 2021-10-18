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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Swift_RfcComplianceException;
use DateTime;
use VARIANT;

/**
 * Displays a letter or a list of letters.
 */
class LetterController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'LETTER_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'       => 'letter',
        'listauthor' => 'letter',
        'show'       => 'letter',
        'showamp'    => 'letter',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'    => 'letters_frontpage',
        'show'    => 'letters_inner',
        'form'    => 'letters_inner',
        'showamp' => 'amp_inner',
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_letter_frontpage'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.letter';

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'     => 'letter/letter_frontpage.tpl',
        'show'     => 'letter/letter.tpl',
        'form' => 'letter/letter_form.tpl',
        'showamp'  => 'amp/content.tpl',
    ];

    /**
     * {@inheritdoc}
     */
    /*public function listAction(Request $request)
    {
        $this->getAds();

        return parent::listAction($request);
    }*/

    /**
     * {@inheritdoc}
     */
    /*public function showAction(Request $request)
    {
        $this->getAds();

        return parent::showAction($request);
    }*/

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $epp = (int) $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $response = $this->get($this->service)->getList(sprintf(
            'content_type_name="letter" and content_status=1 and in_litter=0 '
            . 'order by created desc limit %d offset %d',
            $epp,
            $epp * ($params['page'] - 1)
        ));

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $params = array_merge($params, [
            'otherLetters'    => $response['items'],
            'total'           => $response['total'],
            'pagination'      => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $params['page'],
                'total'       => $response['total'],
                'route'       => [
                    'name'    => 'frontend_letter_frontpage',
                    'params'  => [],
                ]

            ])
        ]);
    }

    /**
     * Displays a form to send letters to the newspaper.
     * The list of routes per action.
     *
     * @return Response The response object.
     * @var array
     */
    public function formAction(Request $request)
    {
        //$this->getAds();
        parent::getAdvertisements();

        $action = $this->get('core.globals')->getAction();
        $params = $this->getParameters($request);
        $params = array_merge($params, [
            'recaptcha' => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml()
        ]);

        return $this->render($this->getTemplate($action), $params);
    }

    /**
     * Saves a content given its information and the content to relate to
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function saveAction(Request $request)
    {
        $response = $request->request->filter('g-recaptcha-response', '', FILTER_SANITIZE_STRING);

        // Check current recaptcha
        $isValid = $this->get('core.recaptcha')
            ->configureFromSettings()
            ->isValid($response, $request->getClientIp());

        // What happens when the CAPTCHA was entered incorrectly
        if (!$isValid) {
            return  new RedirectResponse(
                $this->generateUrl('frontend_letter_form')
                . '?msg="'
                . _("The reCAPTCHA wasn't entered correctly. Go back and try it again.")
                . '"'
            );
        }

        $security_code = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);

        if (!empty($security_code)) {
            return new RedirectResponse(
                $this->generateUrl('frontend_letter_frontpage')
                . '?msg="'
                . _('Unable to save the letter.')
                . '"'
            );
        }

        //$params  = [];
        $data    = [];

        $name    = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
        $subject = $request->request->filter('subject', '', FILTER_SANITIZE_STRING);
        $email   = $request->request->filter('mail', '', FILTER_SANITIZE_STRING);
        //$url     = $request->request->filter('url', '', FILTER_SANITIZE_STRING);
        $text    = $request->request->filter('lettertext', '', FILTER_SANITIZE_STRING);
        //$items   = $request->request->get('items');
        $now     = new DateTime();

        /*$moreData = _("Name") . ": {$name} \n " . _("Email") . ": {$email} \n ";
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                if (!empty($key) && !empty($value)) {
                    $params[$key] = $request->request->filter("items[{$key}]", '', FILTER_SANITIZE_STRING);
                    $moreData    .= " {$key}: {$value}\n ";
                }
            }
        }*/

        $data = [
            'author'         => $name,
            'body'           => iconv(mb_detect_encoding($text), "UTF-8", $text),
            'content_status' => 0, // pending status
            'email'          => $email,
            //'image'          => $this->saveImage($request),
            'title'          => $subject,
            //'url'            => $url,
        ];

        // Prevent XSS attack
        $data = array_map('strip_tags', $data);

        $data['body']              = '<p>' . preg_replace('@\\n@', '</p><p>', $data['body']) . '</p>';
        $data['created']           = $now->format('Y-m-d H:i:s');
        $data['starttime']         = $now->format('Y-m-d H:i:s');
        $data['content_type_name'] = 'letter';
        $data['fk_content_type']   = 17;
        $data['content_status']    = 2;
        $data['params']            = [' ip' => getUserRealIP() ];
        $data['slug']              = getService('data.manager.filter')
            ->set(empty($data['slug']) ? $data['title'] : $data['slug'])
            ->filter('slug')
            ->get();

        try {
            $settings = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get(['contact_email', 'site_name']);

            $recipient = $settings['contact_email'];

            if (!empty($recipient)) {
               $mailSender = $this->getParameter('mailer_no_reply_address');

               //  Build the message
               $swiftMsg = \Swift_Message::newInstance();
               $swiftMsg
                   ->setSubject('[' . _('Letter to the editor') . '] ' . $subject)
                   ->setBody($data['body'], 'text/html')
                   ->setTo([ $recipient => $recipient ])
                   ->setFrom([ $mailSender => $settings['site_name'] ])
                   ->setSender([ $mailSender => $settings['site_name'] ]);

               $headers = $swiftMsg->getHeaders();

               $headers->addParameterizedHeader(
                   'ACUMBAMAIL-SMTPAPI',
                   $this->get('core.instance')->internal_name . ' - Letter'
               );

               $this->get('mailer')->send($text);

               $this->get('application.log')->notice(
                   "Email sent. Frontend letter (From:" . $email . ", to: " . $recipient . ")"
               );
            }

            $this->get($this->service)->createItem($data);
        } catch (Swift_RfcComplianceException $e) {
            $this->get('application.log')->notice(
                "Email NOT sent. Frontend letter (From:" . $email . ", to: " . $recipient . "):"
                . $e->getMessage()
            );

            return new RedirectResponse(
                $this->generateUrl('frontend_letter_frontpage')
                . '?msg="'
                . $e->getMessage()
                . '"'
            );
        } catch (\Exception $e) {
            return new RedirectResponse(
                $this->generateUrl('frontend_letter_frontpage')
                . '?msg="'
                . $e->getMessage()
                . '"'
            );
        }

        return new RedirectResponse(
            $this->generateUrl('frontend_letter_frontpage')
            . '?msg="'
            . _("Your letter has been saved and is awaiting publication.")
            . '"'
        );
    }

    /**
-     * Loads the list of positions and advertisements on renderer service.
-     */
   /*public function getAds()
    {
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7 ]);
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
    }*/
}
