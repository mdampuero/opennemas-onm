<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Renderer;

use Repository\EntityManager;

/**
 * The AdvertisementRenderer service provides methods to generate the HTML code
 * for advertisements basing on the advertisements information.
 */
class NewsletterRenderer
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the entity manager.
     *
     * @param DbalWrapper    $dbConn      The database connection.
     * @param  Template       $template    The template service.
     */
    public function __construct(
        $tpl,
        EntityManager $entityManager,
        $settingManager
    ) {
        $this->tpl = $tpl;
        $this->er  = $entityManager;
        $this->sr  = $settingManager;
    }

    /**
     * Renders the newsletter from a list of contents.
     *
     * @param array $contents The list of the contents.
     *
     * @return string The generated html for the newsletter.
     */
    public function render($contents)
    {
        $newsletterContent = $contents;

        if (empty($newsletterContent)) {
            $newsletterContent = [];
        }

        foreach ($newsletterContent as $container) {
            foreach ($container->items as &$item) {
                // if current item do not fullfill the required format
                // then skip it
                if (empty($item->id) || $item->content_type == 'label') {
                    continue;
                }

                $content = $this->er->find($item->content_type, $item->id);

                // if is not a real content, skip this element
                if (!is_object($content) || is_null($content->id)) {
                    continue;
                }

                $item = $this->hydrateContent($content);
            }
        }

        $this->tpl->assign('newsletterContent', $newsletterContent);

        // Fetch and assign the frontpage menu
        $menu = new \Menu();
        $menu = $menu->getMenu('frontpage');
        $this->tpl->assign('menuFrontpage', $menu->items);

        // Fetch and assign newsletter ads
        $positions = getService('core.helper.advertisement')
            ->getPositionsForGroup('newsletter', [ 1001, 1009 ]);
        $ads       = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, 0);
        $this->tpl->assign('advertisements', $ads);

        // Format and assign the current date.
        // CRAP!
        $days   = [
            'Domingo', 'Lunes', 'Martes', 'Miércoles',
            'Jueves', 'Viernes', 'Sábado'
        ];
        $months = [
            '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $time = new \DateTime();
        $this->tpl->assign(
            'current_date',
            $days[$time->format('w')] . ' ' . $time->format('j') . ' de ' .
            $months[(int) $time->format('n')] . ' ' . $time->format('Y')
        );

        // Process and assign public URL for images and links
        $publicUrl = preg_replace(
            '@^http[s]?://(.*?)/$@i',
            'http://$1',
            getService('core.instance')->getMainDomain()
        );
        $this->tpl->assign('URL_PUBLIC', 'http://' . $publicUrl);

        // Fetch and assign settings
        $configurations = $this->sr->get([
            'newsletter_maillist',
            'newsletter_subscriptionType',
        ]);
        $this->tpl->assign('conf', $configurations);
        $this->tpl->assign('render_params', ['ads-format' => 'inline']);

        return $this->tpl->fetch('newsletter/newNewsletter.tpl');
    }

    /**
     * Completes the content information from an object from repository
     *
     * @param Content $item the item to complete
     * @param Content $content the content from the repository
     *
     * @return Content the item completed
     **/
    public function hydrateContent($content)
    {
        $content->cat    = $content->category_name;
        $content->name   = (isset($content->name)) ? $content->name : '';
        $content->image  = (isset($content->cover)) ? $content->cover : '';
        $content->agency = is_array($content->params) && array_key_exists('agencyBulletin', $content->params)
            ? $content->params['agencyBulletin'] : '';
        $content->date   = date(
            'Y-m-d',
            strtotime(str_replace('/', '-', substr($content->created, 6)))
        );

        // Fetch images of articles if exists
        $content->photo = [];
        if (!empty($content->img1)) {
            $content->photo[] = $this->er->find('Photo', $content->img1);
        } elseif (!empty($content->fk_video)) {
            $content->video = $this->er->find('Video', $content->fk_video);
        } elseif (!empty($content->img2)) {
            $content->photo[] = $this->er->find('Photo', $content->img2);
        }

        if (!isset($content->summary)) {
            $content->summary = substr(strip_tags($content->body), 0, 250) . '...';
        }

        // Fetch opinion author photos
        if ($content->content_type == '4') {
            $content->author = $this->er->find('User', $content->fk_author);
        }

        // Fetch video thumbnails
        if ($content->content_type == '9') {
            $content->thumb = $content->getThumb();
        }

        return $content;
    }
}
