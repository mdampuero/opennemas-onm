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
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

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
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'    => 'article_inner',
        'show'    => 'article_inner',
        'form'    => 'article_inner',
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
        'form'     => 'letter/letter_form.tpl',
    ];

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

        $now  = new \DateTime();

        $response = $this->get($this->service)->getList(sprintf(
            'content_type_name="letter" and content_status=1 and in_litter=0 '
            . ' and (starttime <= "%s" or starttime is null)'
            . ' and (endtime > "%s" or endtime is null)'
            . 'order by created desc limit %d offset %d',
            $now->format('Y-m-d H:i:s'),
            $now->format('Y-m-d H:i:s'),
            $epp,
            $epp * ($params['page'] - 1),
        ));

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $params = array_merge($params, [
            'recaptcha' => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
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

        $isValid = $this->get('core.recaptcha')
            ->configureFromSettings()
            ->isValid($response, $request->getClientIp());

        if (!$isValid) {
            return new JsonResponse([
                'type'    => 'danger',
                'message' => _('Please fill the captcha code.'),
            ], 400);
        }

        $security_code = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);

        if (!empty($security_code)) {
            return new JsonResponse([
                'type'    => 'danger',
                'message' => _('Unable to save the letter.'),
            ], 400);
        }

        $name    = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
        $subject = $request->request->filter('subject', '', FILTER_SANITIZE_STRING);
        $email   = $request->request->filter('mail', '', FILTER_SANITIZE_STRING);
        $text    = $request->request->filter('lettertext', '', FILTER_SANITIZE_STRING);

        $validate = [
            'email'      => $email,
            'lettertext' => $text,
            'name'       => $name,
            'subject'    => $subject,
        ];

        $errors = $this->get('core.validator')->validate($validate, 'letter');

        if (!empty($errors)) {
            return new JsonResponse([
                'type'    => 'danger',
                'message' =>
                    _('Unable to save the letter.')
                    . '<br>'
                    . implode('<br>', $errors['errors'])
                ,
            ], 400);
        }

        $now  = new DateTime();
        $data = [
            'title'             => $subject,
            'body'              => iconv(mb_detect_encoding($text), "UTF-8", $text),
            'content_status'    => 2, // pending status
            'content_type_name' => 'letter',
            'fk_content_type'   => 17,
            'author'            => $name,
            'email'             => $email,
            'created'           => $now->format('Y-m-d H:i:s'),
            'starttime'         => $now->format('Y-m-d H:i:s'),
            'ip'                => getUserRealIP() ,
            'slug'              => getService('data.manager.filter')
                ->set($subject)
                ->filter('slug')
                ->get()
        ];

        try {
            $this->get($this->service)->createItem($data);
        } catch (\Exception $e) {
            $this->get('application.log')->notice(
                "Letter NOT saved: "
                . $e->getMessage()
            );

            return new JsonResponse([
                'type'    => 'danger',
                'message' => _('Unable to save the letter.'),
            ], 400);
        }

        $moreData = _("Name") . ": {$name} \n " . _("Email") . ": {$email} \n ";

        $this->sendEmail(nl2br($moreData . $data['body']), $subject, $email);

        return new JsonResponse([
            'type'    => 'success',
            'message' => _("Your letter has been saved and is awaiting publication.")
        ], 200);
    }

    /**
     * Send verification email
     */
    protected function sendEmail($body, $subject, $email)
    {
        try {
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
                    ->setBody($body, 'text/html')
                    ->setTo([ $recipient => $recipient ])
                    ->setFrom([ $mailSender => $settings['site_name'] ])
                    ->setSender([ $mailSender => $settings['site_name'] ]);

                $headers = $text->getHeaders();

                $headers->addParameterizedHeader(
                    'ACUMBAMAIL-SMTPAPI',
                    $this->get('core.instance')->internal_name . ' - Letter'
                );

                $this->get('mailer')->send($text);

                $this->get('application.log')->notice(
                    "Email sent. Frontend letter (From:" . $email . ", to: " . $recipient . ")"
                );
            }
        } catch (\Exception $e) {
            $this->get('application.log')->notice(
                "Email NOT sent. Frontend letter (From:" . $email . ", to: " . $recipient . "):"
                . $e->getMessage()
            );
        }
    }
}
