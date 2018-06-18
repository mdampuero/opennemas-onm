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
use Api\Exception\GetListException;
use Api\Exception\UpdateItemException;
use Common\Core\Controller\Controller;
use Onm\Settings as s;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

            return $this->redirect($this->generateUrl('frontend_user_show'));
        } catch (GetItemException $e) {
            $this->get('application.log')->error(
                'subscriber.activate.failure: ' . $token
            );

            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to find your account'));

            return $this->redirect($this->generateUrl('core_authentication_logout'));
        } catch (UpdateItemException $e) {
            $this->get('application.log')->error(
                'subscriber.activate.failure: ' . $token
            );

            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to activate your account'));

            return $this->redirect($this->generateUrl('core_authentication_logout'));
        } catch (\Exception $e) {
            $this->get('application.log')->error(
                'subscriber.activate.email.failure: ' . $token
            );

            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to activate your account'));

            return $this->redirect($this->generateUrl('core_authentication_logout'));
        }
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
     * Shows the user information.
     *
     * @return Response The response object.
     */
    public function showAction()
    {
        $photo     = null;
        $countries = array_merge(
            [ '' => _('Select a country') . '...' ],
            $this->get('core.geo')->getCountries()
        );

        if (!empty($this->get('core.user')->avatar_img_id)) {
            $photo = $this->get('entity_repository')
                ->find('Photo', $this->get('core.user')->avatar_img_id);
        }

        return $this->render('user/show.tpl', [
            'countries'     => $countries,
            'photo'         => $photo,
            'settings'      => $this->getSettings(),
            'subscriptions' => $this->getSubscriptions(),
            'user'          => $this->get('core.user'),
        ]);
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
     * Handles the registration of a new user in frontend.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function saveAction(Request $request)
    {
        if (!$this->checkRecaptcha($request)) {
            return $this->redirect($this->generateUrl('frontend_user_register'));
        }

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
        } catch (CreateExistingItemException $e) {
            $this->get('application.log')->info(
                'subscriber.create.failure: ' . $e->getMessage()
            );

            $request->getSession()->getFlashBag()
                ->add('error', _('The email address is already in use.'));

            return $this->redirect($this->generateUrl('frontend_user_register'));
        } catch (CreateItemException $e) {
            $this->get('application.log')->error(
                'subscriber.create.failure: ' . $e->getMessage()
            );

            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to create your user account. Please try it later.')
            );

            return $this->redirect($this->generateUrl('frontend_user_register'));
        } catch (\Exception $e) {
            $this->get('application.log')->error(
                'subscriber.create.email.failure: ' . $e->getMessage()
            );

            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to send your registration email. Please try it later.')
            );

            return $this->redirect($this->generateUrl('frontend_user_register'));
        }

        return $this->render('user/complete.tpl', [
            'countries'     => $this->get('core.geo')->getCountries(),
            'email'         => $data['email'],
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
        $data = array_merge(
            [ 'fk_user_group' => [], 'user_groups' => [] ],
            $request->request->all()
        );

        if (array_key_exists('user_groups', $data)) {
            $data['fk_user_group'] = array_keys($data['user_groups']);
            $data['user_groups']   =
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

        return $this->redirect($this->generateUrl('frontend_user_show'));
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
        $body    = $this->renderView('user/emails/welcome.tpl', [
            'name' => $user->name,
            'url'  => $this->generateUrl('frontend_paywall_showcase', [], true),
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
}
