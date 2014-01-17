<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
class WelcomeController extends Controller
{
    /**
     * Handles the default action
     *
     * @param Request $request the request object
     *
     * @return void
     **/
    public function defaultAction()
    {
        // $modulesActivated = s::get('activated_modules');

        $availableModules = \Onm\Module\ModuleManager::getAvailableModules();
        $availableModules = array_values($availableModules);
        shuffle($availableModules);
        $availableModules = array_splice($availableModules, 0, 5);

        $youtubeVideoIds = $this->getYoutubeVideoIds();

        $user = new \User($_SESSION['userid']);
        $tourDone = $user->getMeta('initial_tour_done');

        $terms = s::get('terms_accepted');

        return $this->render(
            'welcome/index.tpl',
            array(
                'terms_accepted'    => $terms,
                'modules'           => $availableModules,
                'youtube_videos'    => $youtubeVideoIds,
                'initial_tour_done' => $tourDone,
            )
        );
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getYoutubeVideoIds()
    {
        $cm = new \ContentManager();
        $youtubeRss = $cm->getUrlContent(
            'http://gdata.youtube.com/feeds/base/users/OpennemasPublishing/'
            .'uploads?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile'
        );

        $xml = simplexml_load_string($youtubeRss);

        $videosYoutube = array();
        foreach ($xml->channel->item as $item) {
            preg_match('@v=(.*)&@', $item->link, $matches);

            $videosYoutubeIds []= $matches[1];

        }
        shuffle($videosYoutubeIds);
        $videosYoutubeIds = array_splice($videosYoutubeIds, 0, 5);
        return $videosYoutubeIds;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function acceptTermsAction(Request $request)
    {
        s::set('terms_accepted', 1);

        return new Response('ok');
    }
}
