<?php
/**
 * Defines the NewsletterManager class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Core
 */
namespace Repository;

use Onm\Settings as s;
use Onm\Message  as m;

/**
 * Handles the operations of Newsletters
 *
 * @package  Core
 */
class NewsletterManager
{
    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function __construct($mailer, $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * Performs searches in newsletters
     *
     * @param string $whereClause  the where clause to insert into the search
     * @param string $order        the order clause for the search
     * @param int    $page         the page where start the paginated results
     * @param int    $itemsPerPage the number of items per page in paginated results
     *
     * @return Array the newsletters that matches the search criterias
     **/
    public function find(
        $whereClause = '1 = 1',
        $order = 'created DESC',
        $page = null,
        $itemsPerPage = 20
    ) {
        if (!is_null($page)) {
            if ($page == 1) {
                $limit = ' LIMIT '. $itemsPerPage;
            } else {
                $limit = ' LIMIT '.($page-1) * $itemsPerPage.', '.$itemsPerPage;
            }
        } else {
            $limit = '';
        }

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);

        $sql = 'SELECT * FROM `newsletter_archive` WHERE '.$whereClause. ' ORDER BY '.$order.' '.$limit;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $sql2 = 'SELECT COUNT(`pk_newsletter`)  FROM `newsletter_archive` WHERE '.$whereClause. ' ORDER BY '.$order;
        $countNm = $GLOBALS['application']->conn->GetOne($sql2);

        if (!$rs) {
            return;
        }

        $newsletters = array();
        while (!$rs->EOF) {
            $obj = new \Newsletter();
            $obj->loadData($rs->fields);

            $newsletters[] = $obj;

            $rs->MoveNext();
        }
        return array($countNm, $newsletters);
    }

    /**
     * Send mail to all users
     *
     * @param array  $mailboxes   the list of the email addresses to send the mail to
     * @param string $htmlContent the html content for the mail
     * @param array  $params      an array of configurations
     *
     * @return void
     */
    public function send($mailboxes, $htmlContent, $params)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $this->saveNewsletter($htmlContent);

        foreach ($mailboxes as $mailbox) {
            $this->sendToUser($mailbox, $htmlContent, $params);
        }
    }

    /**
     * Send mail to a given mailbox
     *
     * @param string $mailbox     the email addresses to send the mail to
     * @param string $htmlcontent the html content for the mail
     * @param array  $params      an array of configurations
     *
     * @return void
     */
    public function sendToUser($mailbox, $htmlcontent, $params)
    {
        $this->HTML = $htmlcontent;

        $subject = (!isset($params['subject']))? '[Boletin]': $params['subject'];

        //  Build the message
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($subject)
            ->setBody($this->HTML, 'text/html')
            ->setBody(strip_tags($this->HTML), 'text/plain')
            ->setTo(array($mailbox->email => $mailbox->name))
            ->setFrom(array($params['mail_from'] => $params['mail_from_name']))
            ->setSender(array('no-reply@postman.opennemas.com' => s::get('site_name')));

        try {
            $this->mailer->send($message);

        } catch (\Swift_SwiftException $e) {
            $this->logger->notice(_("Unable to send newsletter: ").$e->getMessage());
            $this->errors[] = _("Unable to send newsletter: ").$e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Renders the newsletter from a list of contents
     *
     * @param array $contents the list of the contents
     *
     * @return string the generated html for the newsletter
     **/
    public function render($contents)
    {
        $tpl = new \Template(TEMPLATE_USER);
        $cm  = new \ContentManager();

        $newsletterContent = $contents;

        if (empty($newsletterContent)) {
            $newsletterContent = array();
        }

        foreach ($newsletterContent as $container) {
            foreach ($container->items as &$item) {
                if (!empty($item->id) && $item->content_type !='label') {
                    $content = new $item->content_type($item->id);

                    //if is a real content include it in the contents array
                    if (!empty($content) && is_object($content)) {
                        $content = $content->get($item->id);
                        $item->content_type = $content->content_type;
                        $item->title        = $content->title;
                        $item->slug         = $content->slug;
                        $item->uri          = $content->uri;
                        $item->subtitle     = $content->subtitle;
                        $item->date         = date(
                            'Y-m-d',
                            strtotime(str_replace('/', '-', substr($content->created, 6)))
                        );
                        $item->cat          = $content->category_name;
                        $item->agency       = '';
                        if (is_array($content->params)
                            && array_key_exists('agencyBulletin', $content->params)
                        ) {
                            $item->agency   = $content->params['agencyBulletin'];
                        }
                        $item->name         = (isset($content->name))?$content->name:'';
                        $item->image        = (isset($content->cover))?$content->cover:'';

                        // Fetch images of articles if exists
                        if (isset($content->img1)) {
                            $item->photo = $cm->find('Photo', 'pk_content ='.$content->img1);
                        }
                        if (isset($content->summary)) {
                            $item->summary  = $content->summary;
                        } else {
                            $item->summary = substr($content->body, 0, 250).'...';
                        }
                        //Fetch opinion author photos
                        if ($content->content_type == '4') {
                            $item->author = new \User($content->fk_author);
                        }

                    }

                }
            }
        }

        $tpl->assign('newsletterContent', $newsletterContent);

        //render menu
        $menuManager = new \Menu();
        $menuFrontpage= $menuManager->getMenu('frontpage');
        $tpl->assign('menuFrontpage', $menuFrontpage->items);

        //render ads
        $advertisement = \Advertisement::getInstance();
        $banners       = $advertisement->getAdvertisements(array(1001, 1009), 0);
        $banners       = $cm->getInTime($banners);

        $advertisement->renderMultiple($banners, $advertisement);

         // VIERNES 4 DE SEPTIEMBRE 2009
        $days = array(
            'Domingo', 'Lunes', 'Martes', 'Miércoles',
            'Jueves', 'Viernes', 'Sábado'
        );
        $months = array(
            '', 'Enero', 'Febrero', 'Marzo',
            'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre',
            'Octubre', 'Noviembre', 'Diciembre'
        );
        $tpl->assign(
            'current_date',
            $days[(int) date('w')].' '.date('j').' de '.$months[(int) date('n')].' '.date('Y')
        );

        $publicUrl = preg_replace('@^http[s]?://(.*?)/$@i', 'http://$1', SITE_URL);
        $tpl->assign('URL_PUBLIC', $publicUrl);

        $configurations = s::get(
            array(
                'newsletter_maillist',
                'newsletter_subscriptionType',
            )
        );

        $tpl->assign('conf', $configurations);
        $htmlContent = $tpl->fetch('newsletter/newNewsletter.tpl');

        return $htmlContent;
    }
}
