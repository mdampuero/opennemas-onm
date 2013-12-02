<?php
/**
 * Defines the Onm\DisqusSync class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm
 **/
namespace Onm;

use Onm\Cache\CacheInterface;
use Onm\Settings as s;

/**
* Class for Disqus sync functions
*
* @package  Onm
*/
class DisqusSync
{
    /**
     * Fetch disqus comments from a forum and stores them in database
     *
     * @return void
     */
    public static function saveDisqusCommentsToDatabase()
    {
        // Get disqus shortname and secretkey
        $disqusShortName = s::get('disqus_shortname');
        $disqusSecretKey = s::get('disqus_secret_key');

        // Create Disqus instance
        $disqus = new \DisqusAPI($disqusSecretKey);

        // Set API call params
        $params = array('forum' => $disqusShortName, 'order' =>  'asc', 'limit' => 100);

        // Fetch last comment date
        $comment = new \Comment();
        $lastDate = $comment->getLastCommentDate();
        if ($lastDate) {
            $params['since'] = date('Y-m-d H:i:s', strtotime($lastDate) + 1);
        }

        // Store all contents id on this array to update num comments
        $contents = array();

        // Fetch the latest comments (http://disqus.com/api/docs/posts/list/)
        do {
            try {
                $posts = $disqus->posts->list($params);

                foreach ($posts as $post) {
                    // Fetch thread details (http://disqus.com/api/docs/threads/details/)
                    $threadDetails = $disqus->threads->details(array('thread' => $post->thread));

                    // Get content id from disqus identifier
                    $contentId = 0;
                    if (!empty($threadDetails) && isset($threadDetails->identifiers[0])) {
                        $disqusIdentifier = @explode('-', $threadDetails->identifiers[0]);
                        if (isset($disqusIdentifier[1])) {
                            $contentId = $disqusIdentifier[1];
                        }
                    }

                    // Add contents id to array
                    $contents[$contentId] = $contentId;

                    // Get parent_id if not null
                    $parentId = 0;
                    if (!is_null($post->parent)) {
                        $parentId = $comment->getCommentIdFromPropertyAndValue('disqus_post_id', $post->parent);
                    }

                    $data = array(
                        'content_id'   => $contentId,
                        'author'       => $post->author->name,
                        'author_email' => @$post->author->email,
                        'author_url'   => @$post->author->url,
                        'author_ip'    => @$post->ipAddress,
                        'date'         => date('Y-m-d H:i:s', strtotime($post->createdAt)),
                        'body'         => $post->raw_message,
                        'status'       => ($post->isApproved) ? 'accepted': 'rejected',
                        'agent'        => 'Disqus v3.0',
                        'type'         => 'comment',
                        'parent_id'    => $parentId,
                        'user_id'      => 0,
                    );

                    // Create comment
                    $comment->create($data);

                    // Set contentmeta for comment
                    $comment->setProperty('disqus_post_id', $post->id);
                    $comment->setProperty('disqus_thread_id', $post->thread);
                    $comment->setProperty('disqus_thread_link', $threadDetails->link);

                }

                if (!empty($posts)) {
                    $params['since'] = $posts[count($posts)-1]->createdAt;
                }

            } catch (\DisqusAPIError $e) {
                $this->get('logger')->notice(
                    "Unable to import disqus comment ".$e->getMessage()
                );
            }

        } while (count($posts) == 100);

        foreach ($contents as $id) {
            $comment->updateContentTotalComments($id);
        }
    }
}
