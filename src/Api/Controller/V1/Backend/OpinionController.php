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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OpinionController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_opinion_show';

    /**
     * Previews an opinion in frontend by sending the opinion info by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function previewAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $this->get('core.locale')->setContext('frontend')
            ->setRequestLocale($request->get('locale'));

        $opinion     = new \Opinion();
        $cm          = new \ContentManager();
        $opinion->id = 0;

        $data = $request->request->filter('item');
        $data = json_decode($data, true);

        foreach ($data as $key => $value) {
            if (isset($value) && !empty($value)) {
                $opinion->{$key} = $value;
            }
        }

        $opinion->tag_ids = [];

        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        list($positions, $advertisements) = $this->getAdvertisements();

        try {
            if (!empty($opinion->fk_author)) {
                $opinion->author = $this->get('api.service.author')
                    ->getItem($opinion->fk_author);
            }
        } catch (\Exception $e) {
        }

        $machineSuggestedContents = $this->get('automatic_contents')
            ->searchSuggestedContents('opinion', "pk_content <> $opinion->id", 4);

        // Get author slug for suggested opinions
        foreach ($machineSuggestedContents as &$suggest) {
            $element = new \Opinion($suggest['pk_content']);

            $suggest['author_name_slug'] = "author";
            $suggest['uri']              = $element->uri;

            if (!empty($element->author)) {
                $suggest['author_name']      = $element->author;
                $suggest['author_name_slug'] =
                    \Onm\StringUtils::getTitle($element->author);
            }
        }

        // Associated media code --------------------------------------
        $photo = '';
        if (isset($opinion->img2) && ($opinion->img2 > 0)) {
            $photo = new \Photo($opinion->img2);
        }

        $otherOpinions = $cm->find(
            'Opinion',
            ' opinions.fk_author=' . (int) $opinion->fk_author
            . ' AND `pk_opinion` <>' . $opinion->id . ' AND content_status=1',
            ' ORDER BY created DESC LIMIT 0,9'
        );

        foreach ($otherOpinions as &$otOpinion) {
            $otOpinion->author           = $opinion->author;
            $otOpinion->author_name_slug = $opinion->author_name_slug;
            $otOpinion->uri              = $otOpinion->uri;
        }

        $params = [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'opinion'        => $opinion,
            'content'        => $opinion,
            'other_opinions' => $otherOpinions,
            'author'         => $opinion->author,
            'contentId'      => $opinion->id,
            'photo'          => $photo,
            'suggested'      => $machineSuggestedContents,
            'tags'           => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($opinion->tag_ids)['items']
        ];

        $this->view->assign($params);

        $this->get('session')->set(
            'last_preview',
            $this->view->fetch('opinion/opinion.tpl')
        );

        return new Response('OK');
    }

    /**
     * Description of this action.
     *
     * @return Response The response object.
     */
    public function getPreviewAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $session = $this->get('session');
        $content = $session->get('last_preview');

        $session->remove('last_preview');

        return new Response($content);
    }

    /**
     * Returns a list of extra data
     *
     * @return array
     **/
    protected function getExtraData($items = null)
    {
        $extra = parent::getExtraData($items);

        $extraFields = null;

        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extraFields = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get(\Opinion::EXTRA_INFO_TYPE);
        }

        return array_merge([
            'extra_fields' => $extraFields,
        ], $extra);
    }

    /**
     * Returns the list of l10n keys
     * @param Type $var Description
     *
     * @return array
     **/
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('opinion');
    }

    /**
     * Returns the list of contents related with items.
     *
     * @param Content $content The content.
     *
     * @return array The list of photos linked to the content.
     */
    protected function getRelatedContents($content)
    {
        $em    = $this->get('entity_repository');
        $extra = [];

        if (empty($content)) {
            return $extra;
        }

        if (is_object($content)) {
            $content = [ $content ];
        }

        foreach ($content as $element) {
            foreach (['img1', 'img2'] as $relation) {
                if (!empty($element->{$relation})) {
                    $photo = $em->find('Photo', $element->{$relation});

                    $extra[] = \Onm\StringUtils::convertToUtf8($photo);
                }
            }
        }

        return $extra;
    }
}
