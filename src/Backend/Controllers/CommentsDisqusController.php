<?php
/**
 * Handles the actions for the Disqus comments
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
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the Disqus comments
 *
 * @package Backend_Controllers
 **/
class CommentsDisqusController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Check MODULE
        \Onm\Module\ModuleManager::checkActivatedOrForward('COMMENT_DISQUS_MANAGER');
        // Check ACL
        $this->checkAclOrForward('COMMENT_ADMIN');
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $disqusShortName = s::get('disqus_shortname');
        $disqusSecretKey = s::get('disqus_secret_key');

        // Check if module is configured, if not redirect to configuration form
        if (!$disqusShortName || !$disqusSecretKey) {
            m::add(_('Please provide your Disqus configuration to start to use your Disqus Comments module'));

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
        }

        return $this->render(
            'disqus/list.tpl',
            array(
                'disqus_shortname'  => $disqusShortName,
                'disqus_secret_key' => $disqusSecretKey,
            )
        );
    }

    /**
     * Shows the disqus configuration form and stores its values
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        if ($this->request->getMethod() != 'POST') {
            $disqusShortName = s::get('disqus_shortname');
            $disqusSecretKey = s::get('disqus_secret_key');

            return $this->render(
                'disqus/config.tpl',
                array(
                    'shortname' => $disqusShortName,
                    'secretKey' => $disqusSecretKey,
                )
            );
        } else {
            $shortname = $this->request->request->filter('shortname', null, FILTER_SANITIZE_STRING);
            $secretKey = $this->request->request->filter('secret_key', null, FILTER_SANITIZE_STRING);

            if (s::set('disqus_shortname', $shortname) && s::set('disqus_secret_key', $secretKey)) {
                m::add(_('Disqus configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving the Disqus module configuration'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
        }
    }

    /**
     * Synchronize disqus comments to local database
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function syncAction(Request $request)
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


        // Save last sync time in cache
        $this->container->get('cache')->save(CACHE_PREFIX.'disqus_last_sync', time());

        return $this->redirect($this->generateUrl('admin_comments_disqus'));
    }
}
