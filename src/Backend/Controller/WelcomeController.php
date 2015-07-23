<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
        $termsAccepted = $this->getUser()->getMeta('terms_accepted');

        if ((!$termsAccepted
            || $termsAccepted && $termsAccepted < '2015-07-23 15:24:15')
            && !$this->getUser()->isMaster()
        ) {
            return $this->redirect($this->generateUrl('admin_getting_started'));
        }

        $availableModules = \Onm\Module\ModuleManager::getAvailableModules();
        $availableModules = array_values($availableModules);
        shuffle($availableModules);
        $availableModules = array_splice($availableModules, 0, 5);

        $youtubeVideoIds = $this->getYoutubeVideoIds();

        $user = $this->getUser();
        $tourDone = $user->getMeta('initial_tour_done');

        $terms = s::get('terms_accepted');

        return $this->render(
            'welcome/index.tpl',
            [
                'terms_accepted'    => $terms,
                'modules'           => $availableModules,
                'youtube_videos'    => $youtubeVideoIds,
                'initial_tour_done' => $tourDone,
            ]
        );
    }

    /**
     * Fetches the Youtube video ids to print in the welcome page
     *
     * @return void
     **/
    public function getYoutubeVideoIds()
    {
        $cm = new \ContentManager();
        $params = getContainerParameter('panorama');

        if (!array_key_exists('youtube', $params)
            && !array_key_exists('api_key', $params['youtube'])
            && empty($params['youtube']['api_key'])
        ) {
            throw new \Exception("Missing Youtube configuration.");
        }

        $apiKey = $params['youtube']['api_key'];
        $channelId = 'UUQ-DzmEvQXw5zHgN3qV0T-A';

        // Fetch videos for this playlist
        $playlist = $cm->getUrlContent(
            'https://www.googleapis.com/youtube/v3/playlistItems?'.
            'part=snippet&maxResults=50&playlistId='.$channelId.'&key='.$apiKey,
            true
        );

        $videosYoutubeIds = [];
        if (!is_null($playlist) &&
            $playlist->items &&
            !empty($playlist->items)
        ) {
            foreach ($playlist->items as $video) {
                $videosYoutubeIds[] = $video->snippet->resourceId->videoId;
            }
        }

        shuffle($videosYoutubeIds);
        $videosYoutubeIds = array_splice($videosYoutubeIds, 0, 5);

        return $videosYoutubeIds;
    }
}
