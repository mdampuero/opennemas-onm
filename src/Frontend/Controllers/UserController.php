<?php
/**
 * Handles the actions for the user profile
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
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the user profile
 *
 * @package Frontend_Controllers
 **/
class UserController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);

        session_name('_onm_sess');
        $this->session = $this->get('session');
        $this->session->start();

        require_once 'recaptchalib.php';
    }

    /**
     * Shows the user information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        if (array_key_exists('userid', $_SESSION) && !empty($_SESSION['userid'])) {
            $user = new \User($_SESSION['userid']);
            $user->getMeta();

            // Get current time
            $currentTime = new \DateTime();

            return $this->render(
                'user/show.tpl',
                array(
                    'user'         => $user,
                    'current_time' => $currentTime
                )
            );
        }

        return $this->redirect($this->generateUrl('frontend_auth_login'));
    }



    /**
     * Handles the registration of a new user in frontend
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function registerAction(Request $request)
    {
        //Get config vars
        $configSiteName = s::get('site_name');

        $errors = array();
        // What happens when the CAPTCHA was entered incorrectly
        if ('POST' != $request->getMethod()) {
            // Do nothing
        } else {
            $data = array(
                'activated'     => 0, // Before activation by mail, user is not allowed
                'cpwd'          => $request->request->filter('cpwd', null, FILTER_SANITIZE_STRING),
                'email'         => $request->request->filter('user_email', null, FILTER_SANITIZE_EMAIL),
                'username'      => $request->request->filter('user_name', null, FILTER_SANITIZE_STRING),
                'name'          => $request->request->filter('full_name', null, FILTER_SANITIZE_STRING),
                'password'      => $request->request->filter('pwd', null, FILTER_SANITIZE_STRING),
                'sessionexpire' => 15,
                'token'         => md5(uniqid(mt_rand(), true)), // Token for activation,
                'type'          => 1, // It is a frontend user registration.
                'id_user_group' => array(),
                'bio'           => '',
                'url'           => '',
                'avatar_img_id' => 0,
            );

            // Before send mail and create user on DB, do some checks
            $user = new \User();

            // Check if pwd and cpwd are the same
            if (($data['password'] != $data['cpwd'])) {
                $errors []= _('Password and confirmation must be equal.');
            }

            // Check existing mail
            if ($user->checkIfExistsUserEmail($data['email'])) {
                $errors []= _('The email address is already in use.');
            }

            // Check existing user name
            if ($user->checkIfExistsUserName($data['username'])) {
                $errors []= _('The user name is already in use.');
            }

            // If checks are both false and pass is valid then send mail
            if (count($errors) <= 0) {

                $url = $this->generateUrl('frontend_user_activate', array('token' => $data['token']), true);

                $tplMail = new \Template(TEMPLATE_USER);
                $tplMail->caching = 0;
                $mailSubject = sprintf(_('New user account in %s'), s::get('site_title'));
                $mailBody = $tplMail->fetch(
                    'user/emails/register.tpl',
                    array(
                        'name' => $data['name'],
                        'url'  => $url,
                    )
                );

                // Build the message
                $message = \Swift_Message::newInstance();
                $message
                    ->setSubject($mailSubject)
                    ->setBody($mailBody, 'text/plain')
                    ->setTo($data['email'])
                    ->setFrom(array('no-reply@postman.opennemas.com' => s::get('site_name')));

                // If user is successfully created, send an email
                if (!$user->create($data)) {
                    $errors []=_('An error has occurred. Try to complete the form with valid data.');
                } else {
                    try {
                        $mailer = $this->get('mailer');
                        $mailer->send($message);

                        $this->view->assign(
                            array(
                                'mailSent' => true,
                                'email'    => $data['email'],
                            )
                        );
                    } catch (\Exception $e) {
                        // Log this error
                        $this->get('logger')->notice(
                            "Unable to send the user activation email for the "
                            ."user {$user->id}: ".$e->getMessage()
                        );

                        m::add(_('Unable to send your registration email. Please try it later.'), m::ERROR);
                    }
                    // Set registration date
                    $user->addRegisterDate();
                    $this->view->assign('success', true);
                }
            }
        }

        return $this->render(
            'authentication/register.tpl',
            array(
                'errors' => $errors,
            )
        );
    }

    /**
     * Updates the user data
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        if (!isset($_SESSION['userid'])) {
            return $this->redirect($this->generateUrl('frontend_auth_login'));
        }

        // Get variables from the user FORM an set some manually
        $data['id']              = $_SESSION['userid'];
        $data['username']        = $request->request->filter('username', null, FILTER_SANITIZE_STRING);
        $data['name']            = $request->request->filter('name', null, FILTER_SANITIZE_STRING);
        $data['email']           = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);
        $data['password']        = $request->request->filter('password', '', FILTER_SANITIZE_STRING);
        $data['passwordconfirm'] = $request->request->filter('password-verify', '', FILTER_SANITIZE_STRING);
        $data['sessionexpire']   = 15;
        $data['type']            = 1;
        $data['bio']             = '';
        $data['url']             = '';
        $data['avatar_img_id']   = 0;

        if ($data['password'] != $data['passwordconfirm']) {
            m::add(_('Password and confirmation must be equal.'), m::ERROR);
            return $this->redirect($this->generateUrl('frontend_user_show'));
        }

        // Fetch user data and update
        $user = new \User($_SESSION['userid']);

        if ($user->id > 0) {
            if ($user->update($data)) {
                m::add(_('Data updated successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while updating the user data.'), m::ERROR);
            }
        } else {
            m::add(_('The user does not exists.'), m::ERROR);
        }

        return $this->redirect($this->generateUrl('frontend_user_show'));
    }

    /**
     * Shows the user box
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function userBoxAction(Request $request)
    {
        return $this->render('login/user_box.tpl');
    }

    /**
     * Activates an user account given an token
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function activateAction(Request $request)
    {
        // When user confirms registration from email
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);
        $captcha = '';
        $user = new \User();
        $userData = $user->findByToken($token);

        if ($userData) {
            $user->activateUser($userData->id);

            if ($user->login($userData->username, $userData->password, $userData->token, $captcha)) {
                // Increase security by regenerating the id
                $request->getSession()->migrate();

                $maxSessionLifeTime = (int) s::get('max_session_lifetime', 60);

                // Set last login date
                $user->setLastLoginDate();

                // Set token to null
                $user->updateUserToken($user->id, null);

                $group = \UserGroup::getGroupName($user->fk_user_group);

                $_SESSION = array(
                    'userid'           => $user->id,
                    'realname'         => $user->name,
                    'username'         => $user->username,
                    'email'            => $user->email,
                    'deposit'          => $user->deposit,
                    'authMethod'       => $user->authMethod,
                    'default_expire'   => $user->sessionexpire,
                    'session_lifetime' => $maxSessionLifeTime * 60,
                    'csrf'             => md5(uniqid(mt_rand(), true)),
                    'meta'             => $user->getMeta(),
                );

                // Store default expire time
                setCookieSecure('default_expire', $user->sessionexpire, 0);
            }

            m::add(_('Log in succesful.'), m::SUCCESS);

            // Send welcome mail with link to subscribe action
            $url = $this->generateUrl('frontend_paywall_showcase', array(), true);

            $tplMail = new \Template(TEMPLATE_USER);
            $tplMail->caching = 0;
            $mailSubject = sprintf(_('Welcome to %s'), s::get('site_name'));
            $mailBody = $tplMail->fetch(
                'user/emails/welcome.tpl',
                array(
                    'name' => $user->name,
                    'url'  => $url,
                )
            );

            // Build the message
            $message = \Swift_Message::newInstance();
            $message
                ->setSubject($mailSubject)
                ->setBody($mailBody, 'text/plain')
                ->setTo($user->email)
                ->setFrom(array('no-reply@postman.opennemas.com' => s::get('site_name')));


            try {
                $mailer = $this->get('mailer');
                $mailer->send($message);

                $this->view->assign('mailSent', true);
            } catch (\Exception $e) {
                // Log this error
                $this->get('logger')->notice(
                    "Unable to send the user welcome email for the "
                    ."user {$user->id}: ".$e->getMessage()
                );

                m::add(_('Unable to send your welcome email. Please try it later.'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('frontend_user_show'));
        } else {
            m::add(_('There was an error while creating your user account. Please try again'), m::ERROR);

            return $this->redirect($this->generateUrl('frontend_user_register'));
        }
    }

    /**
     * Shows the form for recovering the pass of a user and
     * sends the mail to the user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function recoverPasswordAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('user/recover_pass.tpl');
        } else {
            $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);

            // Get user by email
            $user = new \User();
            $user->findByEmail($email);

            // If e-mail exists in DB
            if (!is_null($user->id)) {
                // Generate and update user with new token
                $token = md5(uniqid(mt_rand(), true));
                $user->updateUserToken($user->id, $token);

                $url = $this->generateUrl('frontend_user_resetpass', array('token' => $token), true);

                $tplMail = new \Template(TEMPLATE_USER);
                $tplMail->caching = 0;

                $mailSubject = sprintf(_('Password reminder for %s'), s::get('site_title'));
                $mailBody = $tplMail->fetch(
                    'user/emails/recoverpassword.tpl',
                    array(
                        'user' => $user,
                        'url'  => $url,
                    )
                );

                //  Build the message
                $message = \Swift_Message::newInstance();
                $message
                    ->setSubject($mailSubject)
                    ->setBody($mailBody, 'text/plain')
                    ->setTo($user->email)
                    ->setFrom(array('no-reply@postman.opennemas.com' => s::get('site_name')));

                try {
                    $mailer = $this->get('mailer');
                    $mailer->send($message);

                    $this->view->assign(
                        array(
                            'mailSent' => true,
                            'user' => $user
                        )
                    );
                } catch (\Exception $e) {
                    // Log this error
                    $this->get('logger')->notice(
                        "Unable to send the recover password email for the "
                        ."user {$user->id}: ".$e->getMessage()
                    );

                    m::add(_('Unable to send your recover password email. Please try it later.'), m::ERROR);
                }

            } else {
                m::add(_('Unable to find an user with that email.'), m::ERROR);
            }

            // Display form
            return $this->render('user/recover_pass.tpl');
        }
    }

    /**
     * Shows the form for recovering the username of a user and
     * sends the mail to the user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function recoverUsernameAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('user/recover_username.tpl');
        } else {
            $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);

            // Get user by email
            $user = new \User();
            $user->findByEmail($email);

            // If e-mail exists in DB
            if (!is_null($user->id)) {
                // Generate and update user with new token
                $token = md5(uniqid(mt_rand(), true));
                $user->updateUserToken($user->id, $token);

                $tplMail = new \Template(TEMPLATE_USER);
                $tplMail->caching = 0;

                $mailSubject = sprintf(_('Username reminder for %s'), s::get('site_title'));
                $mailBody = $tplMail->fetch(
                    'user/emails/recoverusername.tpl',
                    array(
                        'user' => $user,
                    )
                );

                //  Build the message
                $message = \Swift_Message::newInstance();
                $message
                    ->setSubject($mailSubject)
                    ->setBody($mailBody, 'text/plain')
                    ->setTo($user->email)
                    ->setFrom(array('no-reply@postman.opennemas.com' => s::get('site_name')));

                try {
                    $mailer = $this->get('mailer');
                    $mailer->send($message);

                    $url = $this->generateUrl('frontend_auth_login', array(), true);

                    $this->view->assign(
                        array(
                            'mailSent' => true,
                            'user' => $user,
                            'url' => $url
                        )
                    );
                } catch (\Exception $e) {
                    // Log this error
                    $this->get('logger')->notice(
                        "Unable to send the recover password email for the "
                        ."user {$user->id}: ".$e->getMessage()
                    );

                    m::add(_('Unable to send your recover password email. Please try it later.'), m::ERROR);
                }

            } else {
                m::add(_('Unable to find an user with that email.'), m::ERROR);
            }

            // Display form
            return $this->render('user/recover_username.tpl');
        }
    }
    /**
     * Regenerates the pass for a user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function regeneratePasswordAction(Request $request)
    {
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);

        $user = new \User();
        $user = $user->findByToken($token);

        if ('POST' !== $request->getMethod()) {
            if (empty($user->id)) {
                m::add(
                    _(
                        'Unable to find the password reset request. '
                        .'Please check the url we sent you in the email.'
                    ),
                    m::ERROR
                );
                $this->view->assign('userNotValid', true);
            } else {
                $this->view->assign(
                    array(
                        'user' => $user
                    )
                );
            }
        } else {
            $password       = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
            $passwordVerify = $request->request->filter('password-verify', null, FILTER_SANITIZE_STRING);

            if ($password == $passwordVerify && !empty($password)) {
                $user->updateUserPassword($user->id, $password);
                $user->updateUserToken($user->id, null);

                $this->view->assign('updated', true);
            } else {
                m::add(_('Unable to find the password reset request. Please check the url we sent you in the email.'));
            }

        }

        return $this->render('user/regenerate_pass.tpl', array('token' => $token, 'user' => $user));

    }

    /**
     * Generates the HTML for the user menu by ajax
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function getUserMenuAction(Request $request)
    {
        $login = $this->generateUrl('frontend_auth_login');
        $logout = $this->generateUrl('frontend_auth_logout');
        $register = $this->generateUrl('frontend_user_register');
        $myAccount = $this->generateUrl('frontend_user_show');

        if (isset($_SESSION['userid'])) {
            $output =
                '<ul>
                    <li>
                        <a href="'.$logout.'">'._("Logout").'</a>
                    </li>
                    <li>
                        <a href="'.$myAccount.'">'._("My account").'</a>
                    </li>
                </ul>';
        } else {
            $output =
                '<ul>
                    <li>
                        <a href="'.$register.'">'._("Register").'</a>
                    </li>
                    <li>
                        <a href="'.$login.'">'._("Login").'</a>
                    </li>
                </ul>';
        }

        return $output;
    }

    /**
     * Shows the author frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function authorFrontpageAction(Request $request)
    {

        $slug         = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 12;

        $cacheID = $this->view->generateCacheId('author-'.$slug, '', $page);


        if (($this->view->caching == 0)
           || (!$this->view->isCached('user/author_frontpage.tpl', $cacheID))
        ) {
            // Get user by slug
            $ur = $this->get('user_repository');
            $user = $ur->findOneBy("username='{$slug}'", 'ID DESC');
            if (!empty($user)) {
                $user->photo = new \Photo($user->avatar_img_id);
                $user->getMeta();

                $searchCriteria =  "`fk_author`={$user->id}  AND fk_content_type IN (1, 4, 7, 9) "
                    ."AND available=1 AND in_litter=0";

                $er = $this->get('entity_repository');
                $contentsCount  = $er->count($searchCriteria);
                $contents = $er->findBy($searchCriteria, 'starttime DESC', $itemsPerPage, $page);

                foreach ($contents as &$item) {
                    $item = $item->get($item->id);
                    $item->author = $user;
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $image = new \Photo($item->img1);
                        $item->img1_path = $image->path_file.$image->name;
                        $item->img1 = $image;
                    }

                    if ($item->fk_content_type == 7) {
                        $image = new \Photo($item->cover_id);
                        $item->img1_path = $image->path_file.$image->name;
                        $item->img1 = $image;
                        $item->summary = $item->subtitle;
                        $item->subtitle= '';
                    }

                    if ($item->fk_content_type == 9) {
                        $item->obj_video = $item;
                        $item->summary = $item->description;
                    }

                    if (isset($item->fk_video) && ($item->fk_video > 0)) {
                        $item->video = new \Video($item->fk_video2);
                    }
                }
                // Build the pager
                $pagination = \Onm\Pager\Slider::create(
                    $contentsCount,
                    $itemsPerPage,
                    $this->generateUrl(
                        'frontend_author_frontpage',
                        array('slug' => $slug,)
                    )
                );

                $this->view->assign(
                    array(
                        'contents'   => $contents,
                        'author'     => $user,
                        'pagination' => $pagination,
                    )
                );
            }
        }

        $ads = $this->getInnerAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'user/author_frontpage.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );

    }

    /**
     * Shows the author frontpage from external source
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function extAuthorFrontpageAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $slug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);
        $page = $request->query->getDigits('page', 1);

        // Get sync params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        foreach ($syncParams as $siteUrl => $categoriesToSync) {
            foreach ($categoriesToSync as $value) {
                if (preg_match('/'.$categoryName.'/i', $value)) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        if (empty($wsUrl)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        return $this->redirect($wsUrl.'/author/'.$slug);
    }

    /**
     * Shows the author frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAuthorsAction(Request $request)
    {

        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 16;

        $cacheID = $this->view->generateCacheId('frontpage-authors', '', $page);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('user/frontpage_author.tpl', $cacheID))
        ) {
            $sql = "SELECT count(pk_content) as total_contents, users.id FROM contents, users "
                ." WHERE users.activated = 1 AND users.fk_user_group  LIKE '%3%' "
                ." AND contents.fk_author = users.id  AND fk_content_type IN (1, 4, 7, 9) "
                ." AND available = 1 AND in_litter!= 1 GROUP BY users.id ORDER BY total_contents DESC";

            $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
            $rs = $GLOBALS['application']->conn->Execute($sql);


            $authorsContents = $rs->GetArray();

            $totalUsers = count($authorsContents);

            if (empty($page)) {
                $authorsContents = array_slice($authorsContents, ($page)*$itemsPerPage, $itemsPerPage);
            } else {
                $authorsContents = array_slice($authorsContents, ($page-1)*$itemsPerPage, $itemsPerPage);
            }

            // Build the pager
            $pagination = \Onm\Pager\Slider::create(
                $totalUsers,
                $itemsPerPage,
                $this->generateUrl('frontend_frontpage_authors')
            );


            // Get user by slug
            $ur = $this->get('user_repository');
            foreach ($authorsContents as &$element) {
                $user = $ur->find($element['id']);
                $user->total_contents = $element['total_contents'];
                $element = $user;
            }

            $this->view->assign(
                array(
                    'authors_contents' => $authorsContents,
                    'pagination'       => $pagination,
                )
            );
        }

        $ads = $this->getInnerAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'user/frontpage_authors.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );

    }

    /**
     * Fetches advertisements for article inner
     *
     * @param string category the category identifier
     *
     * @return void
     **/
    public static function getInnerAds($category = 'home')
    {
        $positions = array(101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 191, 192, 193);

        return \Advertisement::findForPositionIdsAndCategory($positions, 0);
    }
}
