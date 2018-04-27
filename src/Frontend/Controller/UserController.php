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

use Api\Exception\CreateItemException;
use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Entity\User;
use Onm\Settings as s;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Handles the actions for the user profile.
 */
class UserController extends Controller
{
    /**
     * Shows the user information.
     *
     * @return Response The response object.
     */
    public function showAction()
    {
        $photo = null;

        if (!empty($this->get('core.user')->avatar_img_id)) {
            $photo = $this->get('entity_repository')
                ->find('Photo', $this->get('core.user')->avatar_img_id);
        }

        return $this->render('user/show.tpl', [
            'countries'     => $this->get('core.geo')->getCountries(),
            'photo'         => $photo,
            'settings'      => $this->getSettings(),
            'subscriptions' => $this->getSubscriptions(),
            'user'          => $this->get('core.user'),
        ]);
    }

    /**
     * Handles the registration of a new user in frontend.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function registerAction(Request $request)
    {
        if ('POST' === $request->getMethod()
            && $this->checkRecaptcha($request)
        ) {
            $data = $request->request->all();
            unset($data['g-recaptcha-response']);

            if (array_key_exists('user_groups', $data)) {
                $data['user_groups'] =
                    $this->parseSubscriptions($data['user_groups']);
            }

            $data['type']          = 1;
            $data['token']         = md5(uniqid(mt_rand(), true));
            $data['register_date'] = new \DateTime();

            try {
                $this->get('api.service.subscriber')->createItem($data);
                $this->get('application.log')
                    ->info('subscriber.create.success');

                $this->sendCreateEmail($data);
                $this->get('application.log')
                    ->info('subscriber.create.email.success');
                $this->view->assign([
                    'mailSent' => true,
                    'email' => $data['email']
                ]);
            } catch (CreateItemException $e) {
                $this->get('application.log')->error(
                    'subscriber.create.failure: ' . $e->getMessage(),
                    $e->getTrace()
                );
            } catch (\Exception $e) {
                $this->get('application.log')->error(
                    'subscriber.create.email.failure: ' . $e->getMessage(),
                    $e->getTrace()
                );

                $request->getSession()->getFlashBag()->add(
                    'error',
                    _('Unable to send your registration email. Please try it later.')
                );
            }

            $this->view->assign('success', true);
        }

        return $this->render('authentication/register.tpl', [
            'countries'     => $this->get('core.geo')->getCountries(),
            'recaptcha'     => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'settings'      => $this->getSettings(),
            'subscriptions' => $this->getSubscriptions(),
        ]);
    }

    /**
     * Updates the user data.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function updateAction(Request $request)
    {
        if (empty($this->get('core.user'))) {
            throw new AccessDeniedException();
        }

        $data = $request->request->all();

        if (array_key_exists('user_groups', $data)) {
            $data['user_groups'] =
                $this->parseSubscriptions($data['user_groups']);
        }

        // Remove to prevent password changes when empty
        if (empty($data['password'])) {
            unset($data['password']);
        }


        unset($data['password-verify']);

        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('User');

        try {
            $user = $em->getRepository('User')->find($this->getUser()->id);

            if (!empty($data['password'])) {
                $encoder = $this->get('security.password_encoder');

                $data['password'] = $encoder->encodePassword($user, $data['password']);
            }

            $user->merge($converter->objectify($data));
            $em->persist($user);

            $this->get('session')->getFlashBag()->add('success', _('Data updated successfully'));
            $this->get('core.dispatcher')->dispatch('author.update', [ 'id' => $user->id ]);
        } catch (EntityNotFoundException $e) {
            $this->get('session')->getFlashBag()->add('error', _('The user does not exists.'));
        } catch (\Exception $e) {
            $this->get('error.log')
                ->error('frontend.subscriber.update: ' . $e->getMessage());
            $this->get('session')->getFlashBag()->add('error', _('Unable to update the user.'));
        }

        return $this->redirect($this->generateUrl('frontend_user_show'));
    }

    /**
     * Activates an user account given an token.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function activateAction(Request $request)
    {
        // When user confirms registration from email
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);
        $em    = $this->get('orm.manager');
        $oql   = sprintf('token = "%s"', $token);

        try {
            $user = $em->getRepository('User')->findOneBy($oql);

            $user->activated  = true;
            $user->last_login = new \DateTime('now');
            $user->token      = null;

            $em->persist($user);

            $request->getSession()->migrate();

            $token   = new UsernamePasswordToken($user, null, 'frontend', $user->getRoles());
            $session = $request->getSession();

            $securityContext = $this->get('security.token_storage');
            $securityContext->setToken($token);
            $session->set('user', $user);
            $session->set('_security_frontend', serialize($token));

            $session->getFlashBag()->add('success', _('Log in succesful.'));

            // Send welcome mail with link to subscribe action
            $url = $this->generateUrl('frontend_paywall_showcase', [], true);

            $mailSubject = sprintf(_('Welcome to %s'), $this->get('setting_repository')->get('site_name'));
            $mailBody    = $this->renderView(
                'user/emails/welcome.tpl',
                [
                    'name' => $user->name,
                    'url'  => $url,
                ]
            );

            // Build the message
            $message = \Swift_Message::newInstance();
            $message
                ->setSubject($mailSubject)
                ->setBody($mailBody, 'text/html')
                // And optionally an alternative body
                ->addPart(strip_tags($mailBody), 'text/plain')
                ->setTo($user->email)
                ->setFrom(['no-reply@postman.opennemas.com' => $this->get('setting_repository')->get('site_name')]);

            $headers = $message->getHeaders();
            $headers->addParameterizedHeader(
                'ACUMBAMAIL-SMTPAPI',
                $this->get('core.instance')->internal_name . ' - User activation'
            );

            try {
                $mailer = $this->get('mailer');
                $mailer->send($message);

                $this->get('application.log')->notice(
                    "Email sent. Frontend activate user (to: " . $user->email . ")"
                );

                $this->view->assign('mailSent', true);
            } catch (\Exception $e) {
                // Log this error
                $this->get('application.log')->notice(
                    "Unable to send the user welcome email for the "
                    . "user {$user->id}: " . $e->getMessage()
                );

                $this->get('session')->getFlashBag()->add('error', _('Unable to send your welcome email.'));
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while creating your user account.')
            );

            return $this->redirect($this->generateUrl('frontend_user_register'));
        }

        return $this->redirect($this->generateUrl('frontend_frontpage'));
    }

    /**
     * Generates the HTML for the user menu by ajax.
     *
     * @return Response The response object.
     */
    public function getUserMenuAction()
    {
        $photo = null;

        if (!empty($this->get('core.user')->avatar_img_id)) {
            $photo = $this->get('entity_repository')
                ->find('Photo', $this->get('core.user')->avatar_img_id);
        }

        return $this->render('user/menu.tpl', [ 'photo' => $photo ]);
    }

    /**
     * Checks if there is a valid recaptcha reponse in the request.
     *
     * @param Request $request The request object.
     *
     * @return boolean True if there is no recaptcha response or the recaptcha
     *                 response is valid. False otherwise.
     */
    protected function checkRecaptcha($request)
    {
        $valid = $this->get('core.recaptcha')->configureFromSettings()
            ->isValid(
                $request->get('g-recaptcha-response'),
                $request->getClientIp()
            );

        if (!$valid) {
            $request->getSession()->getFlashBag()->add(
                'error',
                'The reCAPTCHA wasn\'t entered correctly. Try to authenticate again.'
            );

            return false;
        }

        return true;
    }

    /**
     * Returns the list of user settings.
     *
     * @return array The list of user settings.
     */
    protected function getSettings()
    {
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('user_settings');

        if (empty($settings) || !array_key_exists('fields', $settings)) {
            return [];
        }

        foreach ($settings['fields'] as &$field) {
            if ($field['type'] === 'options') {
                $options = [];
                $values  = explode(',', $field['values']);
                $values  = array_map(function ($a) {
                    return trim($a);
                }, $values);

                foreach ($values as $value) {
                    $value = explode(':', $value);
                    $key   = trim($value[0]);
                    $value = trim($value[1]);

                    $options[] = [ 'key' => $key, 'name' => $value ];
                }

                $field['values'] = $options;
            }
        }

        return $settings;
    }

    /**
     * Returns the list of public user groups.
     *
     * @return array The list of public user groups.
     */
    protected function getSubscriptions()
    {
        $response = $this->get('api.service.subscription')
            ->setCount(false)->getList('enabled = 1');

        return $response['items'];
    }

    /**
     * Parses subscriptions from form.
     *
     * @param array $subscriptions The list of subscriptions.
     *
     * @return array The list of parsed subscriptions.
     */
    protected function parseSubscriptions($subscriptions)
    {
        $ids   = array_keys($subscriptions);
        $items = $this->get('api.service.subscription')
            ->setCount(false)
            ->getListByIds($ids);

        $subscriptions = [];
        foreach ($items['items'] as $item) {
            $subscriptions[$item->pk_user_group] = [
                'user_group_id' => $item->pk_user_group,
                'status'        => $item->request ? 2 : 1
            ];
        }

        return $subscriptions;
    }

    /**
     * Sends the email before account creation.
     *
     * @param array $data The email information.
     */
    protected function sendCreateEmail($data)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings');

        $subject = sprintf(_('New user account in %s'), $ds->get('site_title'));

        $this->view->setCaching(0);
        $body = $this->renderView('user/emails/register.tpl', [
            'name' => $data['name'],
            'url'  => $this->get('router')->generate('frontend_user_activate', [
                'token' => $data['token']
            ], true),
        ]);

        // Build the message
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($subject)
            ->setBody($body, 'text/html')
            ->addPart(strip_tags($body), 'text/plain')
            ->setTo($data['email'])
            ->setFrom([
                'no-reply@postman.opennemas.com' => $ds->get('site_name')
            ]);

        $message->getHeaders()->addParameterizedHeader(
            'ACUMBAMAIL-SMTPAPI',
            $this->get('core.instance')->internal_name . ' - User register'
        );

        $this->get('mailer')->send($message);
    }
}
