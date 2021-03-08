<?php

namespace Api\Controller\V1\Backend;

use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'ARTICLE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_article_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.article';

    /**
     * Returns the list of paramters needed to create a new article.
     *
     * @return JsonResponse The response object.
     */
    public function createAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Loads extra data related to the given contents.
     *
     * @param array $items The items array
     *
     * @return array Array of extra data.
     */
    protected function getExtraData($items = null)
    {
        $extra = parent::getExtraData($items);

        $extraFields = null;

        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extraFields = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('extraInfoContents.ARTICLE_MANAGER');
        }

        $categories = $this->get('api.service.category')->responsify(
            $this->get('api.service.category')->getList()['items']
        );
        $subscriptions = $this->get('api.service.subscription')->responsify(
            $this->get('api.service.subscription')->getList('enabled = 1 order by name asc')['items']
        );

        return array_merge([
            'categories'    => $categories,
            'extra_fields'  => $extraFields,
            'subscriptions' => $subscriptions,
            'tags'          => $this->getTags($items)
        ], $extra);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelatedContents($content)
    {
        $service = $this->get('api.service.photo');
        $extra   = [];

        if (empty($content)) {
            return $extra;
        }

        if (is_object($content)) {
            $content = [ $content ];
        }

        foreach ($content as $element) {
            if (!is_array($element->related_contents)) {
                continue;
            }

            foreach ($element->related_contents as $relation) {
                if (!preg_match('/featured_.*/', $relation['type'])) {
                    continue;
                }
                try {
                    $photo                         = $service->getItem($relation['target_id']);
                    $extra[$relation['target_id']] = $service->responsify($photo);
                } catch (GetItemException $e) {
                }
            }
        }

        return $extra;
    }
}
