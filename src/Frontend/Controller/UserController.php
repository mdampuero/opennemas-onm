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

use Common\ORM\Core\Exception\EntityNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

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
        if (empty($this->getUser())) {
            return $this->redirect($this->generateUrl('frontend_authentication_login'));
        }

        $user = $this->get('orm.manager')->getRepository('User')
            ->find($this->getUser()->id);

        // Get current time
        $currentTime = new \DateTime();

        // Get user orders
        $order      = new \Order();
        $userOrders = $order->find('user_id = ' . $user->id);

        // Fetch paywall settings
        $paywallSettings = $this->get('setting_repository')->get('paywall_settings');

        return $this->render('user/show.tpl', [
            'countries'        => $this->get('core.geo')->getCountries(),
            'current_time'     => $currentTime,
            'paywall_settings' => $paywallSettings,
            'user'             => $user,
            'user_groups'      => $this->getUserGroups(),
            'user_orders'      => $userOrders
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
        $errors = [];
        if ('POST' == $request->getMethod()) {
            // Check reCAPTCHA
            $valid    = false;
            $response = $request->get('g-recaptcha-response');
            $ip       = $request->getClientIp();

            if (!is_null($response)) {
                $valid = $this->get('core.recaptcha')
                    ->configureFromSettings()
                    ->isValid($response, $ip);

                if (!$valid) {
                    $errors[] = _(
                        'The reCAPTCHA wasn\'t entered correctly.'
                        . ' Try to authenticate again.'
                    );
                }
            }

            $data = [
                'activated'     => 0, // Before activation by mail, user is not allowed
                'cpwd'          => $request->request->filter('cpwd', null, FILTER_SANITIZE_STRING),
                'email'         => $request->request->filter('user_email', null, FILTER_SANITIZE_EMAIL),
                'username'      => $request->request->filter('user_name', null, FILTER_SANITIZE_STRING),
                'name'          => $request->request->filter('full_name', null, FILTER_SANITIZE_STRING),
                'password'      => $request->request->filter('pwd', null, FILTER_SANITIZE_STRING),
                'token'         => md5(uniqid(mt_rand(), true)), // Token for activation,
                'type'          => 1, // It is a frontend user registration.
                'id_user_group' => [],
                'bio'           => '',
                'url'           => '',
                'avatar_img_id' => 0,
                'meta'          => $request->request->get('meta'),
            ];

            // Before send mail and create user on DB, do some checks
            $user = new \User();

            // Check if pwd and cpwd are the same
            if (($data['password'] != $data['cpwd'])) {
                $errors[] = _('Password and confirmation must be equal.');
            }

            // Check existing mail
            if ($user->checkIfExistsUserEmail($data['email'])) {
                $errors[] = _('The email address is already in use.');
            }

            // Fill username and name with email if empty
            foreach ([ 'username', 'name' ] as $value) {
                if (empty($data[$value])) {
                    $data[$value] = $data['email'];
                }
            }

            // Check existing user name
            if ($user->checkIfExistsUserName($data['username'])) {
                $errors[] = _('The user name is already in use.');
            }

            // If checks are both false and pass is valid then send mail
            if (count($errors) <= 0) {
                $url = $this->generateUrl('frontend_user_activate', ['token' => $data['token']], true);

                $this->view->setCaching(0);

                $mailSubject = sprintf(_('New user account in %s'), s::get('site_title'));
                $mailBody    = $this->renderView(
                    'user/emails/register.tpl',
                    [
                        'name' => $data['name'],
                        'url'  => $url,
                    ]
                );

                // If user is successfully created, send an email
                if (!$user->create($data)) {
                    $errors[] = _('An error has occurred. Try to complete the form with valid data.');
                } else {
                    $user->setMeta($request->request->get('meta'));

                    // Set registration date
                    $currentTime = new \DateTime();
                    $currentTime->setTimezone(new \DateTimeZone('UTC'));

                    $user->setMeta(['register_date' => $currentTime->format('Y-m-d H:i:s')]);

                    try {
                        // Build the message
                        $message = \Swift_Message::newInstance();
                        $message
                            ->setSubject($mailSubject)
                            ->setBody($mailBody, 'text/plain')
                            // And optionally an alternative body
                            ->addPart($mailBody, 'text/html')
                            ->setTo($data['email'])
                            ->setFrom([
                                'no-reply@postman.opennemas.com' => $this->get('setting_repository')->get('site_name')
                            ]);

                        $mailer = $this->get('mailer');
                        $mailer->send($message);

                        $this->get('application.log')->notice(
                            "Email sent. Frontend register user (to: " . $data['email'] . ")"
                        );

                        $this->view->assign([
                            'mailSent' => true,
                            'email'    => $data['email'],
                        ]);
                    } catch (\Exception $e) {
                        // Log this error
                        $this->get('application.log')->notice(
                            "Unable to send the user activation email for the "
                            . "user {$user->id}: " . $e->getMessage()
                        );

                        $this->get('session')->getFlashBag()->add(
                            'error',
                            _('Unable to send your registration email. Please try it later.')
                        );
                    }

                    $this->view->assign('success', true);
                }
            }
        }

        return $this->render('authentication/register.tpl', [
            'errors'      => $errors,
            'countries'   => $this->get('core.geo')->getCountries(),
            'recaptcha'   => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'settings'    => $this->getSettings(),
            'user_groups' => $this->getUserGroups()
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
        if (empty($this->getUser())) {
            return $this->redirect($this->generateUrl('frontend_authentication_login'));
        }

        $data = $request->request->all();

        if ($data['password'] !== $data['password-verify']) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Password and confirmation must be equal.')
            );

            return $this->redirect($this->generateUrl('frontend_user_show'));
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
                ->setBody($mailBody, 'text/plain')
                // And optionally an alternative body
                ->addPart($mailBody, 'text/html')
                ->setTo($user->email)
                ->setFrom(['no-reply@postman.opennemas.com' => $this->get('setting_repository')->get('site_name')]);

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
     * Shows the form for recovering the pass of a user and send the mail to the
     * user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function recoverPasswordAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('user/recover_pass.tpl');
        }

        $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);

        // Get user by email
        $user = new \User();
        $user->findByEmail($email);

        // If e-mail exists in DB
        if (!is_null($user->id)) {
            // Generate and update user with new token
            $token = md5(uniqid(mt_rand(), true));
            $user->updateUserToken($user->id, $token);

            $url = $this->generateUrl('frontend_user_resetpass', ['token' => $token], true);

            $this->view->setCaching(0);

            $mailSubject = sprintf(_('Password reminder for %s'), $this->get('setting_repository')->get('site_title'));
            $mailBody    = $this->renderView(
                'user/emails/recoverpassword.tpl',
                [
                    'user' => $user,
                    'url'  => $url,
                ]
            );

            //  Build the message
            $message = \Swift_Message::newInstance();
            $message
                ->setSubject($mailSubject)
                ->setBody($mailBody, 'text/plain')
                ->setTo($user->email)
                ->setFrom(['no-reply@postman.opennemas.com' => $this->get('setting_repository')->get('site_name')]);

            try {
                $mailer = $this->get('mailer');
                $mailer->send($message);

                $this->get('application.log')->notice(
                    "Email sent. Frontend recover password (to: " . $user->email . ")"
                );

                $this->view->assign([
                    'mailSent' => true,
                    'user'     => $user
                ]);
            } catch (\Exception $e) {
                // Log this error
                $this->get('application.log')->notice(
                    "Unable to send the recover password email for the "
                    . "user {$user->id}: " . $e->getMessage()
                );

                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to send your recover password email. Please try it later.')
                );
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', _('Unable to find an user with that email.'));
        }

        // Display form
        return $this->render('user/recover_pass.tpl');
    }

    /**
     * Regenerates the pass for a user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function regeneratePasswordAction(Request $request)
    {
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);

        $user = new \User();
        $user = $user->findByToken($token);

        if ('POST' !== $request->getMethod()) {
            if (empty($user->id)) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _(
                        'Unable to find the password reset request. '
                        . 'Please check the url we sent you in the email.'
                    )
                );

                $this->view->assign('userNotValid', true);
            } else {
                $this->view->assign('user', $user);
            }
        } else {
            $password       = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
            $passwordVerify = $request->request->filter('password-verify', null, FILTER_SANITIZE_STRING);

            if ($password == $passwordVerify && !empty($password)) {
                $user->updateUserPassword($user->id, $password);
                $user->updateUserToken($user->id, null);

                $this->view->assign('updated', true);
            } else {
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    _('Unable to find the password reset request. Please check the url we sent you in the email.')
                );
            }
        }

        return $this->render('user/regenerate_pass.tpl', [
            'token' => $token,
            'user'  => $user
        ]);
    }

    /**
     * Generates the HTML for the user menu by ajax.
     *
     * @return Response The response object.
     */
    public function getUserMenuAction()
    {
        return $this->render('user/menu.tpl');
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
    protected function getUserGroups()
    {
        $userGroups = $this->get('orm.manager')
            ->getRepository('UserGroup')->findBy();

        // Show only public groups ()
        $userGroups = array_filter($userGroups, function ($a) {
            return in_array(223, $a->privileges);
        });

        $userGroups = array_map(function ($a) {
            return [ 'id' => $a->pk_user_group, 'name' => $a->name ];
        }, $userGroups);

        return array_values($userGroups);
    }
}
