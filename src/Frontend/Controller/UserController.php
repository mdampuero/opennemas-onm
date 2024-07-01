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

use Api\Exception\CreateExistingItemException;
use Api\Exception\CreateItemException;
use Api\Exception\GetItemException;
use Api\Exception\UpdateItemException;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Handles the actions for the user profile.
 */
class UserController extends Controller
{
    /**
     * Activates an user account given an token.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function activateAction(Request $request)
    {
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);

        try {
            $ss   = $this->get('api.service.subscriber');
            $user = $ss->getItemBy(sprintf('token = "%s" limit 1', $token));

            $ss->patchItem($user->id, [ 'activated' => true, 'token' => null ]);

            $this->get('core.security.authentication')->authenticate($user);
            $this->get('application.log')->info('subscriber.activate.success');

            $this->sendActivateEmail($user);
            $this->get('application.log')->info('subscriber.activate.email.success');

            $this->get('session')->getFlashBag()
                ->add('success', _('Your account is now activated'));

            return $this->redirect($this->generatePrefixedUrl('frontend_user_show'));
        } catch (GetItemException $e) {
            $this->get('application.log')->error(
                'subscriber.activate.failure: ' . $token
            );

            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to find your account'));

            return $this->redirect($this->generatePrefixedUrl('core_authentication_logout'));
        } catch (UpdateItemException $e) {
            $this->get('application.log')->error(
                'subscriber.activate.failure: ' . $token
            );

            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to activate your account'));

            return $this->redirect($this->generatePrefixedUrl('core_authentication_logout'));
        } catch (\Exception $e) {
            $this->get('application.log')->error(
                'subscriber.activate.email.failure: ' . $token
            );

            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to activate your account'));

            return $this->redirect($this->generatePrefixedUrl('core_authentication_logout'));
        }
    }

    /**
     * Shows the user information.
     *
     * @return Response The response object.
     */
    public function showAction()
    {
        if (empty($this->get('core.user'))) {
            return new RedirectResponse($this->get('router')->generate('frontend_authentication_login'));
        }

        $countries = array_merge(
            [ '' => _('Select a country') . '...' ],
            $this->get('core.geo')->getCountries()
        );

        $userGroups = array_filter(
            $this->get('core.user')->user_groups,
            function ($el) {
                return $el['status'] != 0;
            }
        );

        return $this->render('user/show.tpl', [
            'countries'     => $countries,
            'settings'      => $this->getSettings(),
            'subscriptions' => $this->getSubscriptions(),
            'user'          => $this->get('core.user'),
            'user_groups'   => array_map(function ($el) {
                return $el['user_group_id'];
            }, $userGroups),
        ]);
    }

    /**
     * Recover the password for one user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function recoverAction(Request $request)
    {
        $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);
        $em    = $this->get('orm.manager');

        try {
            $user = $em->getRepository('User')->findOneBy("email = '$email'");
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()
                ->add('error', _('Unable to find an user with that email.'));

            return $this->redirect($this->generatePrefixedUrl('frontend_user_reset'));
        }

        if (!$user->activated) {
            $this->get('session')->getFlashBag()
                ->add('error', sprintf(_(
                    '<strong>This account has not been verified.</strong>' .
                    '<ul>' .
                    '<li>To verify this account click on the link sent to your email.</li>' .
                    '<li>If you have not received any message, check your spam box.</li>' .
                    '<li>If you want a new link, click <a href="%s">here</a>.</li>' .
                    '</ul>'
                ), $this->generatePrefixedUrl('frontend_user_verify')));
            return $this->redirect($this->generatePrefixedUrl('frontend_user_reset'));
        }

        $this->view->setCaching(0);

        // Generate and update user with new token
        $token = md5(uniqid(mt_rand(), true));
        $user->merge([ 'token' => $token ]);
        $em->persist($user);

        $mailSubject = sprintf(
            _('Password reminder for %s'),
            $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('site_title')
        );

        $url = $this->get('router')->generate(
            'frontend_password_change',
            [ 'token' => $token ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $mailBody = $this->get('core.template.frontend')
            ->render('user/emails/recoverpassword.tpl', [
                'user' => $user,
                'url'  => $this->get('core.decorator.url')->prefixUrl($url),
            ]);

        //  Build the message
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($mailSubject)
            ->setBody($mailBody, 'text/plain')
            ->setTo($user->email)
            ->setFrom([
                'no-reply@postman.opennemas.com' =>
                    $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('site_name')
            ]);

        $headers = $message->getHeaders();
        $headers->addParameterizedHeader(
            'ACUMBAMAIL-SMTPAPI',
            $this->get('core.instance')->internal_name . ' - Backend Recover Password'
        );

        try {
            $mailer = $this->get('mailer');
            $mailer->send($message);

            $this->get('application.log')
                ->info('password.request.email.success: ' . $user->email);

            $this->get('session')->getFlashBag()->add('success', _('Password recovery email was sent.'));

            $this->view->assign([ 'user' => $user ]);
        } catch (\Exception $e) {
            $this->get('application.log')->error(
                'password.request.email.failure: ' . $user->email . '('
                    . $e->getMessage() . ')',
                $e->getTrace()
            );

            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to send your recover password email. Please try it later.')
            );
        }

        return $this->redirect($this->generatePrefixedUrl('frontend_authentication_login'));
    }

    /**
     * Displays a form to create a new user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function registerAction()
    {
        $cs = $this->get('core.security');

        if (!$cs->hasExtension('NEWSLETTER_MANAGER') && !$cs->hasExtension('CONTENT_SUBSCRIPTIONS')) {
            throw new ResourceNotFoundException();
        }

        if (!empty($this->get('core.user'))) {
            return new RedirectResponse($this->get('router')->generate('frontend_user_show'));
        }

        $countries = array_merge(
            [ '' => _('Select a country') . '...' ],
            $this->get('core.geo')->getCountries()
        );

        return $this->render('user/register.tpl', [
            'countries'     => $countries,
            'recaptcha'     => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'settings'      => $this->getSettings(),
            'subscriptions' => $this->getSubscriptions(),
        ]);
    }

    /**
     * Shows the form to recover the password.
     *
     * @return Response The response object.
     */
    public function resetAction()
    {
        return $this->render('user/reset.tpl');
    }

    /**
     * Handles the registration of a new user in frontend.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function saveAction(Request $request)
    {
        /*
        if ('POST' != $request->getMethod() || !$this->checkRecaptcha($request)) {
            return $this->redirect($this->generatePrefixedUrl('frontend_user_register'));
        }
        */

        $securityInput = $request->request->get('register_control');
        $userGroups    = $request->request->get('user_groups', []);
        $userGroups    = !is_array($userGroups) ? [ $userGroups => '' ] : $userGroups;
        $data          = array_merge(
            $request->request->all(),
            [
                'name'       => $request->request->filter('email', null, FILTER_SANITIZE_EMAIL),
                'email'       => $request->request->filter('email', null, FILTER_SANITIZE_EMAIL),
                'user_groups' => $userGroups,
                'password'    => $request->request->get('password'),
            ]
        );

        if (!empty($securityInput)) {
            $this->get('application.log')->error(
                'subscriber.create.failure | Bot Detected | Email:' . $data['email']
            );
            return $this->redirect($this->generatePrefixedUrl('frontend_user_register'));
        }

        $cs = $this->get('core.security');

        if (!$cs->hasExtension('NEWSLETTER_MANAGER') && !$cs->hasExtension('CONTENT_SUBSCRIPTIONS')) {
            $this->get('application.log')->error(
                'subscriber.create.failure | Module not activated | Email:' . $data['email']
            );
            return $this->redirect($this->generatePrefixedUrl('frontend_user_register'));
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->get('application.log')->error(
                'subscriber.create.failure | Email not valid | Email:' . $data['email']
            );

            $request->getSession()->getFlashBag()->add(
                'error',
                _('Please provide a valid email address.')
            );

            return $this->redirect($this->generatePrefixedUrl('frontend_user_register'));
        }

        unset($data['g-recaptcha-response']);
        unset($data['dog']);

        if (array_key_exists('user_groups', $data)) {
            $data['user_groups'] =
                $this->parseSubscriptions($data['user_groups']);
        }

        $data['type']          = 1;
        $data['token']         = md5(uniqid(mt_rand(), true));
        $data['register_date'] = new \DateTime();

        try {
            $user = $this->get('api.service.subscriber')->createItem($data);
            $this->get('application.log')->info('subscriber.create.success | User ID: '
                . $user->id . ' | ' . $user->getUsername());

            $this->sendCreateEmail($data);
            $this->get('application.log')->info('subscriber.create.email.success | User ID: '
                . $user->id . ' | ' . $user->getUsername());
        } catch (CreateExistingItemException $e) {
            $this->get('application.log')->error(
                'subscriber.create.failure | ' . $e->getMessage() . ' | Email: ' . $data['email']
            );

            $request->getSession()->getFlashBag()
                ->add('error', sprintf(
                    _('<strong>The email address is already in use.</strong>' .
                    '<ul>' .
                    '<li>Sign in <a href="%s">here</a>.</li>' .
                    '<li>Recover password <a href="%s">here</a>.</li>' .
                    '</ul>'),
                    $this->generatePrefixedUrl('frontend_authentication_login'),
                    $this->generatePrefixedUrl('frontend_user_recover')
                ));

            return $this->redirect($this->generatePrefixedUrl('frontend_user_register'));
        } catch (CreateItemException $e) {
            $this->get('application.log')->error(
                'subscriber.create.failure | ' . $e->getMessage() . ' | Email: ' . $data['email']
            );

            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to create your user account. Please try it later.')
            );

            return $this->redirect($this->generatePrefixedUrl('frontend_user_register'));
        } catch (\Exception $e) {
            $this->get('application.log')->error(
                'subscriber.create.email.failure | ' . $e->getMessage() . ' | Email:' . $data['email']
            );

            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to send your registration email. Please try it later.')
            );

            return $this->redirect($this->generatePrefixedUrl('frontend_user_register'));
        }

        return $this->render('user/complete.tpl', [
            'countries'     => $this->get('core.geo')->getCountries(),
            'email'         => $data['email'],
            'recaptcha'     => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'settings'      => $this->getSettings(),
            'subscriptions' => $this->getSubscriptions(),
            'name'          => $data['email']
        ]);
    }

    /**
     * Show the form to get the verification email.
     *
     * @return Response The response object.
     */
    public function verifyAction()
    {
        return $this->render('user/verification.tpl');
    }

    /**
     * Send the verification email.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function sendVerificationAction(Request $request)
    {
        $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);

        if (empty($email)) {
            return $this->redirect($this->generatePrefixedUrl('frontend_user_verify'));
        }

        try {
            $ss    = $this->get('api.service.subscriber');
            $user  = $ss->getItemBy(sprintf('email = "%s" limit 1', $email));
            $token = $user->token;

            if (empty($token)) {
                $this->get('session')->getFlashBag()
                    ->add('error', _('This account is already verified'));

                return $this->redirect($this->generatePrefixedUrl('frontend_user_verify'));
            }

            $data = [
                'name'  => $user->name,
                'token' => $token,
                'email' => $email
            ];

            $this->sendCreateEmail($data);

            $this->get('session')->getFlashBag()
                ->add('success', _('The verification email was sent.'));

            return $this->redirect($this->generatePrefixedUrl('frontend_authentication_login'));
        } catch (GetItemException $e) {
            $this->get('application.log')->error(
                'user.verify.failure: ' . $email
            );

            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to find your account'));

            return $this->redirect($this->generatePrefixedUrl('frontend_user_register'));
        }
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
        $data = array_merge(
            $request->request->all(),
            [
                'name'        => $request->request->filter('name', null, FILTER_SANITIZE_SPECIAL_CHARS),
                'email'       => $request->request->filter('email', null, FILTER_SANITIZE_EMAIL),
                'password'    => $request->request->get('password'),
                'user_groups' => $request->request->get('user_groups', []),
            ]
        );

        if (array_key_exists('user_groups', $data)) {
            $data['user_groups'] =
                $this->parseSubscriptions($data['user_groups']);
        }

        // Remove to prevent password changes when empty
        if (empty($data['password'])) {
            unset($data['password']);
        }

        unset($data['password-verify']);

        try {
            $this->get('api.service.subscriber')
                ->updateItem($this->getUser()->id, $data);

            $this->get('session')->getFlashBag()
                ->add('success', _('Item updated successfully'));

            $this->get('core.dispatcher')
                ->dispatch('user.update', [ 'id' => $this->getUser()->id ]);
        } catch (\Exception $e) {
            $this->get('error.log')
                ->error('frontend.subscriber.update: ' . $e->getMessage());
            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to update the user.'));
        }

        return $this->redirect($this->generatePrefixedUrl('frontend_user_show'));
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

        if (!is_array($settings['fields'])) {
            $settings['fields'] = [];
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
            ->setCount(false)->getList('enabled = 1 and private = 0');

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
        if (empty($subscriptions)) {
            return [];
        }

        $ids   = array_keys($subscriptions);
        $items = $this->get('api.service.subscription')
            ->setCount(false)
            ->getListByIds($ids);

        $subscriptions = [];
        foreach ($items['items'] as $item) {
            $subscriptions[] = [
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

        $subject = sprintf(_('New user account in %s'), $ds->get('site_name'));

        $this->view->setCaching(0);

        $url = $this->get('router')->generate(
            'frontend_user_activate',
            [ 'token' => $data['token'] ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $body = $this->get('core.template.frontend')
            ->render('user/emails/register.tpl', [
                'name' => $data['name'],
                'url'  => $this->get('core.decorator.url')->prefixUrl($url),
            ]);

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

    /**
     * Sends the email after account is activated.
     *
     * @param array $data The user.
     */
    protected function sendActivateEmail($user)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings');

        $subject = sprintf(_('Welcome to %s'), $ds->get('site_name'));
        $body    = $this->get('core.template.frontend')
            ->render('user/emails/welcome.tpl', [
                'name' => $user->name,
                'url'  => $this->generatePrefixedUrl('frontend_paywall_showcase', [], true),
            ]);

        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($subject)
            ->setBody($body, 'text/html')
            ->addPart(strip_tags($body), 'text/plain')
            ->setTo($user->email)
            ->setFrom([
                'no-reply@postman.opennemas.com' => $ds->get('site_name')
            ]);

        $message->getHeaders()->addParameterizedHeader(
            'ACUMBAMAIL-SMTPAPI',
            $this->get('core.instance')->internal_name . ' - User activation'
        );

        $this->get('mailer')->send($message);
    }

    /**
     * Generates the url with the subdirectory and multilanguage prefix if needed.
     *
     * @param String $route The name of the route to generate the url.
     *
     * @return String The url with the prefix if needed.
     */
    private function generatePrefixedUrl(
        string $route,
        array $parameters = [],
        bool $reference = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        $url = $this->generateUrl($route, $parameters, $reference);

        return $this->get('core.decorator.url')->prefixUrl($url);
    }
}
