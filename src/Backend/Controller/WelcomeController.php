<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

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
     */
    public function defaultAction()
    {
        $user = $this->get('core.user');

        if (empty($user->terms_accepted)
            //|| $termsAccepted && $termsAccepted < '2015-07-23 15:24:15')
            && !$this->get('core.security')->hasPermission('MASTER')
        ) {
            return $this->redirect($this->generateUrl('admin_getting_started'));
        }

        $youtubeVideoIds = $this->getYoutubeVideoIds();

        return $this->render('welcome/index.tpl', [
            'youtube_videos' => $youtubeVideoIds
        ]);
    }

    /**
     * Returns a list of videos from YouTube to show in dashboard.
     *
     * @return array The list of videos from YouTube.
     */
    protected function getYoutubeVideoIds()
    {
        $cm     = new \ContentManager();
        $params = $this->getParameter('panorama');

        if (!array_key_exists('youtube', $params)
            && !array_key_exists('api_key', $params['youtube'])
            && empty($params['youtube']['api_key'])
        ) {
            throw new \Exception("Missing Youtube configuration.");
        }

        $apiKey    = $params['youtube']['api_key'];
        $channelId = 'UUQ-DzmEvQXw5zHgN3qV0T-A';

        // Fetch videos for this playlist
        $playlist = $cm->getUrlContent(
            'https://www.googleapis.com/youtube/v3/playlistItems?'
            . 'part=snippet&maxResults=50&playlistId=' . $channelId . '&key='
            . $apiKey,
            true
        );

        $videos = [];
        if (!is_null($playlist) && $playlist->items && !empty($playlist->items)) {
            foreach ($playlist->items as $video) {
                if (property_exists($video->snippet->thumbnails, 'maxres')) {
                    $videos[] = [
                        'id'        => $video->snippet->resourceId->videoId,
                        'thumbnail' => $video->snippet->thumbnails->maxres->url,
                        'title'     => $video->snippet->title,
                    ];
                }
            }
        }

        shuffle($videos);

        return array_splice($videos, 0, 5);
    }
}
