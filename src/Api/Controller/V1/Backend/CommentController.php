<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Core\Component\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends ApiController
{
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING  = 'pending';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.comment';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($comments = [])
    {
        $extra = [ 'statuses' => [
            [ 'title' => _('All'), 'value' => null ],
            [ 'title' => _('Accepted'), 'value' => self::STATUS_ACCEPTED ],
            [ 'title' => _('Rejected'), 'value' => self::STATUS_REJECTED ],
            [ 'title' => _('Pending'), 'value' => self::STATUS_PENDING ],
        ]
        ];
        $ids   = [];

        if (is_object($comments)) {
            $comments = [ $comments ];
        }

        foreach ($comments as $comment) {
            if ($comment->content_type_referenced && $comment->content_id) {
                $ids[] = [ $comment->content_type_referenced, $comment->content_id ];
            }
        }

        $items = $this->get('api.service.content')->responsify(
            $this->get('entity_repository')->findMulti($ids)
        );

        $extra['contents'] = [];

        foreach ($items as $content) {
            $extra['contents'][$content['pk_content']] = $content;
        }

        $extra['dateTimezone'] = $this->container->get('core.locale')->getTimeZone();

        $this->get('core.locale')->setContext('frontend');

        return $extra;
    }

    /**
     * Returns comments configuration
     *
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        $defaultConfigs  = $this->get('core.helper.comment')->getDefaultConfigs();
        $config          = $ds->get('comments_config', []);

        foreach ($config as $configName => $value) {
            if ($configName == 'number_elements') {
                $config[$configName] = (int) $config[$configName];
            }
            if ($value == '1') {
                $config[$configName] = true;
            }
        }

        $config = array_merge($defaultConfigs, $config);

        return new JsonResponse([
            'config' => $config,
            'extra'   => [
                'handler' => $ds->get('comment_system'),
                'blacklist_comment' => $this->get('core.validator')
                    ->getConfig(Validator::BLACKLIST_RULESET_COMMENTS),
                'disqus_shortname' => $ds->get('disqus_shortname', []),
                'disqus_secret_key' => $ds->get('disqus_secret_key', []),
                'facebook' => $ds->get('facebook', [])
                ]
        ]);
    }
        /**
     * Returns comments configuration
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $msg = $this->get('core.messenger');
        $ds  = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        $config = $request->request->get('config', []);
        $extra  = $request->request->get('extra', []);

        $tobool = [
            'disable_comments', 'with_comments', 'moderation_manual',
            'moderation_autoreject', 'moderation_autoaccept', 'required_email'
        ];

        foreach ($tobool as $key) {
            if (!empty($config[$key])) {
                if ($config[$key] == 'true') {
                    $config[$key] = true;
                } else {
                    $config[$key] = false;
                }
            }
        }

        try {
            $ds->set('disqus_shortname', $extra['disqus_shortname']);
            $ds->set('disqus_secret_key', $extra['disqus_secret_key']);
            $ds->set('comments_config', $config);
            $ds->set('comment_system', $extra['handler']);
            $ds->set('facebook', $extra['facebook']);

            $this->get('core.validator')
                ->setConfig(Validator::BLACKLIST_RULESET_COMMENTS, $extra['blacklist_comment']);

            $msg->add(_('Settings saved.'), 'success', 200);
        } catch (\Exception $e) {
            $msg->add(_('There was an error while saving the settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
