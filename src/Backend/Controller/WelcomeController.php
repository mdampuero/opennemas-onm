<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 */
class WelcomeController extends Controller
{
    /**
     * Handles the default action
     *
     * @param Request $request the request object
     *
     * @return void
     */
    public function defaultAction()
    {
        $user = $this->get('core.user');

        if (empty($user->terms_accepted)
            //|| $termsAccepted && $termsAccepted < '2015-07-23 15:24:15')
            && !$this->getUser()->isMaster()
        ) {
            return $this->redirect($this->generateUrl('admin_getting_started'));
        }

        $youtubeVideoIds = $this->getYoutubeVideoIds();

        return $this->render(
            'welcome/index.tpl',
            [ 'youtube_videos' => $youtubeVideoIds ]
        );
    }

    /**
     * Fetches the Youtube video ids to print in the welcome page
     *
     * @return void
     */
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

        $videosYoutube = [];
        if (!is_null($playlist) &&
            $playlist->items &&
            !empty($playlist->items)
        ) {
            foreach ($playlist->items as $video) {
                if (!property_exists($video->snippet->thumbnails, 'maxres')) {
                    continue;
                }

                $videosYoutube[] = [
                    'id'        => $video->snippet->resourceId->videoId,
                    'thumbnail' => $video->snippet->thumbnails->maxres->url,
                    'title'     => $video->snippet->title,
                ];
            }
        }

        shuffle($videosYoutube);
        $videosYoutube = array_splice($videosYoutube, 0, 5);

        return $videosYoutube;
    }
}
