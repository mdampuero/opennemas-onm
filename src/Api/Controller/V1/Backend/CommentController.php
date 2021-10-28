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
    protected $extension = 'COMMENT_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'delete' => 'COMMENT_DELETE',
        'list'   => 'COMMENT_ADMIN',
        'patch'  => 'COMMENT_UPDATE',
        'show'   => 'COMMENT_UPDATE',
        'update' => 'COMMENT_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.comment';

    /**
     * The list of keys to convert to boolean
     *
     * @var array
     */
    private $tobool = [
        'disable_comments', 'with_comments', 'moderation_manual',
        'moderation_autoreject', 'moderation_autoaccept', 'required_email'
    ];

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

        $extra['keys']         = $this->get('api.service.content')->getL10nKeys();
        $extra['locale']       = $this->get('core.helper.locale')->getConfiguration();
        $extra['dateTimezone'] = $this->container->get('core.locale')->getTimeZone();

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
        $this->checkSecurity($this->extension, 'COMMENT_SETTINGS');

        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');
        $sh = $this->get('core.helper.setting');

        $defaultConfigs = $this->get('core.helper.comment')->getDefaultConfigs();
        $config         = $ds->get('comment_settings', []);

        $config = $sh->toInt($config, ['number_elements']);
        $config = $sh->toBoolean($config, $this->tobool);

        $config = array_merge($defaultConfigs, $config);

        return new JsonResponse([
            'config' => $config,
            'extra'   => [
                'blacklist_comment' => $this->get('core.validator')
                    ->getConfig(Validator::BLACKLIST_RULESET_COMMENTS),
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
        $this->checkSecurity($this->extension, 'COMMENT_SETTINGS');

        $msg = $this->get('core.messenger');
        $ds  = $this->get('orm.manager')->getDataSet('Settings', 'instance');
        $sh  = $this->get('core.helper.setting');

        $config = $request->request->get('config', []);
        $extra  = $request->request->get('extra', []);

        $config = $sh->toInt($config, ['number_elements']);
        $config = $sh->toBoolean($config, $this->tobool);

        try {
            $ds->set('comment_settings', $config);

            $this->get('core.validator')
                ->setConfig(Validator::BLACKLIST_RULESET_COMMENTS, $extra['blacklist_comment']);

            $this->get('core.dispatcher')
                ->dispatch('comments.config');

            $msg->add(_('Settings saved.'), 'success', 200);
        } catch (\Exception $e) {
            $msg->add(_('There was an error while saving the settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
