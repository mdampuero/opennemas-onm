<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * Saves a new article.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     && hasPermission('ARTICLE_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $article = new \Article();

        $postReq = $request->request;
        $params  = $postReq->get('params', []);

        if (empty($params)) {
            $params = [];
        }

        $contentStatus = $postReq->filter('content_status', '', FILTER_SANITIZE_STRING);
        $frontpage     = $postReq->filter('frontpage', '', FILTER_SANITIZE_STRING);
        $withComment   = $postReq->filter('with_comment', '', FILTER_SANITIZE_STRING);

        $data = [
            'agency'         => $postReq->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'body'           => $postReq->get('body'),
            'category'       => $postReq->getDigits('pk_fk_content_category'),
            'content_status' => (empty($contentStatus)) ? 0 : 1,
            'description'    => $postReq->get('description'),
            'endtime'        => $postReq->filter('endtime', '', FILTER_SANITIZE_STRING),
            'fk_video'       => $postReq->getDigits('fk_video'),
            'fk_video2'      => $postReq->getDigits('fk_video2'),
            'footer_video1'  => $postReq->get('footer_video1'),
            'footer_video2'  => $postReq->get('footer_video2'),
            'frontpage'      => (empty($frontpage)) ? 0 : 1,
            'img1'           => $postReq->getDigits('img1'),
            'img1_footer'    => $postReq->get('img1_footer'),
            'img2'           => $postReq->getDigits('img2'),
            'img2_footer'    => $postReq->get('img2_footer'),
            'metadata'       => $this->get('data.manager.filter')
                ->set($postReq->filter('metadata', '', FILTER_SANITIZE_STRING))
                ->filter('tags', [ 'exclude' => [ '.', '-', '#' ] ])
                ->get(),
            'slug'           => $postReq->get('slug'),
            'starttime'      => $postReq->filter('starttime', '', FILTER_SANITIZE_STRING),
            'subtitle'       => $postReq->get('subtitle'),
            'summary'        => $postReq->get('summary'),
            'title'          => $postReq->get('title'),
            'title_int'      => $postReq->get('title_int'),
            'with_comment'   => (empty($withComment)) ? 0 : 1,
            'relatedFront'   => $postReq->get('relatedFront', []),
            'relatedHome'    => $postReq->get('relatedHome', []),
            'relatedInner'   => $postReq->get('relatedInner', []),
            'fk_author'      => $postReq->getDigits('fk_author', 0),
            'params' => [
                'agencyBulletin'    => array_key_exists('agencyBulletin', $params) ? $params['agencyBulletin'] : '',
                'bodyLink'          => array_key_exists('bodyLink', $params) ? $params['bodyLink'] : '',
                'imageHome'         => array_key_exists('imageHome', $params) ? $params['imageHome'] : null,
                'imageHomeFooter'   => array_key_exists('imageHomeFooter', $params) ? $params['imageHomeFooter'] : null,
                'imageHomePosition' => array_key_exists('imageHomePosition', $params) ?
                    $params['imageHomePosition'] : '',
                'imagePosition'     => array_key_exists('imagePosition', $params) ? $params['imagePosition'] : '',
                'only_registered'   => array_key_exists('only_registered', $params) ? $params['only_registered'] : '',
                'only_subscribers'  => array_key_exists('only_subscribers', $params) ? $params['only_subscribers'] : '',
                'subtitleHome'      => array_key_exists('subtitleHome', $params) ? $params['subtitleHome'] : '',
                'summaryHome'       => array_key_exists('summaryHome', $params) ? $params['summaryHome'] : '',
                'titleHome'         => array_key_exists('titleHome', $params) ? $params['titleHome'] : '',
                'titleHomeSize'     => array_key_exists('titleHomeSize', $params) ? $params['titleHomeSize'] : '',
                'titleSize'         => array_key_exists('titleSize', $params) ? $params['titleSize'] : '',
                'withGallery'       => array_key_exists('withGallery', $params) ? $params['withGallery'] : '',
                'withGalleryHome'   => array_key_exists('withGalleryHome', $params) ? $params['withGalleryHome'] : '',
                'withGalleryInt'    => array_key_exists('withGalleryInt', $params) ? $params['withGalleryInt'] : '',
            ],
        ];

        $msg  = $this->get('core.messenger');
        $data = $this->loadMetaDataFields($data, $postReq);

        if (!$article->create($data)) {
            $msg->add(_('Unable to create the new article.'), 'error', 400);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Article successfully created.')
        );

        $url = $this->generateUrl('admin_articles');

        // Return user to list if has no update acl
        if (!empty($article->pk_content)
            && $this->get('core.security')->hasPermission('ARTICLE_UPDATE')
        ) {
            $params = [ 'id' => $article->id ];

            if ($this->get('core.instance')->hasMultilanguage()
                && !empty($request->get('locale'))
                && $request->get('locale') !==
                    $this->get('core.locale')->getLocale('frontend')
            ) {
                $params['locale'] = $this->request->get('locale');
            }

            $url = $this->generateUrl('admin_article_show', $params);
        }

        $response = new JsonResponse('', 201);
        $response->headers->set('Location', $url);

        return $response;
    }

    /**
     * Updates the article information sent by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $msg      = $this->get('core.messenger');
        $security = $this->get('core.security');
        $id       = $request->query->getDigits('id');
        $article  = new \Article($id);

        if ($article->id == null) {
            $msg->add(_('Unable to update the article.'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if (!$security->hasPermission('CONTENT_OTHER_UPDATE')
            && !$article->isOwner($this->getUser()->id)
        ) {
            $msg->add(_('You can\'t modify this article because you don\'t have enought privileges.'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if (count($request->request) < 1) {
            $msg->add(_('Article data sent not valid.'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $article = new \Article();
        $postReq = $request->request;
        $params  = $postReq->get('params', []);

        if (empty($params)) {
            $params = [];
        }

        $contentStatus = $postReq->filter('content_status', '', FILTER_SANITIZE_STRING);
        $frontpage     = $postReq->filter('frontpage', '', FILTER_SANITIZE_STRING);
        $withComment   = $postReq->filter('with_comment', '', FILTER_SANITIZE_STRING);

        $data = [
            'agency'         => $postReq->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'body'           => $postReq->get('body', ''),
            'category'       => $postReq->getDigits('pk_fk_content_category'),
            'content_status' => (empty($contentStatus)) ? 0 : 1,
            'description'    => $postReq->get('description'),
            'endtime'        => $postReq->filter('endtime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'fk_author'      => $postReq->filter('fk_author', 0, FILTER_VALIDATE_INT),
            'fk_video'       => $postReq->getDigits('fk_video'),
            'fk_video2'      => $postReq->getDigits('fk_video2'),
            'footer_video'   => $postReq->get('footer_video'),
            'footer_video2'  => $postReq->get('footer_video2'),
            'frontpage'      => (empty($frontpage)) ? 0 : 1,
            'id'             => $id,
            'img1'           => $postReq->getDigits('img1'),
            'img1_footer'    => $postReq->get('img1_footer'),
            'img2'           => $postReq->getDigits('img2'),
            'img2_footer'    => $postReq->get('img2_footer'),
            'metadata'       => $this->get('data.manager.filter')
                ->set($postReq->filter('metadata', '', FILTER_SANITIZE_STRING))
                ->filter('tags', [ 'exclude' => [ '.', '-', '#' ] ])
                ->get(),
            'relatedFront'   => $postReq->get('relatedFront', []),
            'relatedHome'    => $postReq->get('relatedHome', []),
            'relatedInner'   => $postReq->get('relatedInner', []),
            'slug'           => $postReq->get('slug'),
            'starttime'      => $postReq->get('starttime'),
            'subtitle'       => $postReq->get('subtitle'),
            'summary'        => $postReq->get('summary'),
            'title'          => $postReq->get('title'),
            'title_int'      => $postReq->get('title_int'),
            'with_comment'   => (empty($withComment)) ? 0 : 1,
            'params'         => [
                'agencyBulletin'    => array_key_exists('agencyBulletin', $params) ? $params['agencyBulletin'] : '',
                'bodyLink'          => array_key_exists('bodyLink', $params) ? $params['bodyLink'] : '',
                'imageHome'         => array_key_exists('imageHome', $params) ? $params['imageHome'] : null,
                'imageHomeFooter'   => array_key_exists('imageHomeFooter', $params) ? $params['imageHomeFooter'] : null,
                'imageHomePosition' => array_key_exists('imageHomePosition', $params) ?
                    $params['imageHomePosition'] : '',
                'imagePosition'     => array_key_exists('imagePosition', $params) ? $params['imagePosition'] : '',
                'only_registered'   => array_key_exists('only_registered', $params) ? $params['only_registered'] : '',
                'only_subscribers'  => array_key_exists('only_subscribers', $params) ? $params['only_subscribers'] : '',
                'subtitleHome'      => array_key_exists('subtitleHome', $params) ? $params['subtitleHome'] : '',
                'summaryHome'       => array_key_exists('summaryHome', $params) ? $params['summaryHome'] : '',
                'titleHome'         => array_key_exists('titleHome', $params) ? $params['titleHome'] : '',
                'titleHomeSize'     => array_key_exists('titleHomeSize', $params) ? $params['titleHomeSize'] : '',
                'titleSize'         => array_key_exists('titleSize', $params) ? $params['titleSize'] : '',
                'withGallery'       => array_key_exists('withGallery', $params) ? $params['withGallery'] : '',
                'withGalleryHome'   => array_key_exists('withGalleryHome', $params) ? $params['withGalleryHome'] : '',
                'withGalleryInt'    => array_key_exists('withGalleryInt', $params) ? $params['withGalleryInt'] : '',
            ],
        ];

        $data = $this->loadMetaDataFields($data, $postReq);

        if (!$article->update($data)) {
            $msg->add(_('Unable to update the article.'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if ($data['content_status'] == 0) {
            $article->dropFromAllHomePages();
        }

        $msg->add(_('Article successfully updated.'), 'success');
        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * This method load from the request the metadata fields,
     *
     * @param mixed   $data Data where load the metadata fields.
     * @param Request $postReq Request where the metadata are.
     */
    private function loadMetaDataFields($data, $postReq)
    {
        if (!$this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            return $data;
        }

        // If I don't have the extension, I don't check the settings
        $groups = $this->get('setting_repository')
            ->get('extraInfoContents.ARTICLE_MANAGER');
        if (!is_array($groups)) {
            return $data;
        }

        foreach ($groups as $group) {
            foreach ($group['fields'] as $field) {
                if (empty($postReq->get($field['key']))) {
                    continue;
                }

                $data[$field['key']] = $postReq->get($field['key']);
            }
        }
        return $data;
    }
}
