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

        $data = array(
            'agency'         => $postReq->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'body'           => $postReq->filter('body', ''),
            'category'       => $postReq->getDigits('category'),
            'content_status' => (empty($contentStatus)) ? 0 : 1,
            'description'    => $postReq->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'endtime'        => $postReq->filter('endtime', '', FILTER_SANITIZE_STRING),
            'fk_video'       => $postReq->getDigits('fk_video', ''),
            'fk_video2'      => $postReq->getDigits('fk_video2', ''),
            'footer_video2'  => $postReq->filter('footer_video2', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'frontpage'      => (empty($frontpage)) ? 0 : 1,
            'img1'           => $postReq->getDigits('img1', null),
            'img1_footer'    => $postReq->get('img1_footer', null),
            'img2'           => $postReq->getDigits('img2', null),
            'img2_footer'    => $postReq->get('img2_footer', null),
            'metadata'       => \Onm\StringUtils::normalizeMetadata($postReq->filter('metadata', '', FILTER_SANITIZE_STRING)),
            'slug'           => $postReq->filter('slug', '', FILTER_SANITIZE_STRING),
            'starttime'      => $postReq->filter('starttime', '', FILTER_SANITIZE_STRING),
            'subtitle'       => $postReq->filter('subtitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'summary'        => $postReq->filter('summary', ''),
            'title'          => $postReq->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'title_int'      => $postReq->filter('title_int', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'with_comment'   => (empty($withComment)) ? 0 : 1,
            'relatedFront'   => json_decode($postReq->get('relatedFront', '')),
            'relatedInner'   => json_decode($postReq->get('relatedInner', '')),
            'relatedHome'    => json_decode($postReq->get('relatedHome', '')),
            'fk_author'      => $postReq->getDigits('fk_author', 0),
            'params' =>  array(
                'agencyBulletin'    => array_key_exists('agencyBulletin', $params) ? $params['agencyBulletin'] : '',
                'bodyLink'          => array_key_exists('bodyLink', $params) ? $params['bodyLink'] : '',
                'imageHome'         => array_key_exists('imageHome', $params) ? $params['imageHome'] : null,
                'imageHomeFooter'   => array_key_exists('imageHomeFooter', $params) ? $params['imageHomeFooter'] : null,
                'imageHomePosition' => array_key_exists('imageHomePosition', $params) ? $params['imageHomePosition'] : '',
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
            ),
        );

        $msg = $this->get('core.messenger');

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
            $url = $this->generateUrl('admin_article_show', [ 'id' => $article->id ]);
        }

        $response = new JsonResponse('', 201);
        $response->headers->set('Location', $url);

        return $response;
    }

    /**
     * Displays the article information given the article id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_UPDATE')")
     */
    public function showAction($id)
    {
        $article = new \Article($id);

        if (is_null($article->id)) {
            return new JsonResponse(
                sprintf(_('Unable to find the article with the id "%d"'), $id),
                400
            );
        }

        if (!$article->params) {
            $article->params = [];
        }

        if (is_string($article->params)) {
            $article->params = unserialize($article->params);
        }

        if (!empty($article->img1)) {
            $params['photo1'] = new \Photo($article->img1);
        }

        if (!empty($article->img2)) {
            $params['photo2'] = new \Photo($article->img2);
        }

        if (is_array($article->params)
            && (array_key_exists('imageHome', $article->params))
            && !empty($article->params['imageHome'])
        ) {
            $params['photo3'] = new \Photo($article->params['imageHome']);
        }

        if (!empty($article->fk_video)) {
            $params['video1'] = new \Video($article->fk_video);
        }

        if (!empty($article->fk_video2)) {
            $params['video2'] = new \Video($article->fk_video2);
        }

        if ($article->isInFrontpageOfCategory((int) $article->category)) {
            $article->promoted_to_category_frontpage = true;
        }

        $rm = $this->get('related_contents');

        $relations = $rm->getRelations($id, 'frontpage');
        if (count($relations) > 0) {
            $params['relatedInFrontpage'] = array_map(function ($content) {
                return \Onm\StringUtils::convertToUtf8($content);
            }, $this->get('entity_repository')->findMulti($relations));
        }
        $relations = $rm->getRelations($id, 'inner');
        if (count($relations) > 0) {
            $params['relatedInInner'] = array_map(function ($content) {
                return \Onm\StringUtils::convertToUtf8($content);
            }, $this->get('entity_repository')->findMulti($relations));
        }

        if ($this->get('core.security')->hasExtension('CRONICAS_MODULES')
            && is_array($article->params)
        ) {
            $galleries = [];

            $relations = $rm->getRelations($id, 'home');
            if (count($relations) > 0) {
                $params['relatedInHome'] = array_map(function ($content) {
                    return \Onm\StringUtils::convertToUtf8($content);
                }, $this->get('entity_repository')->findMulti($relations));
            }

            if (array_key_exists('withGalleryHome', $article->params)
                && !empty($article->params['withGalleryHome'])
            ) {
                $params['galleryForHome'] = new \Album($article->params['withGalleryHome']);
            }

            if (array_key_exists('withGallery', $article->params)
                && !empty($article->params['withGallery'])
            ) {
                $params['galleryForFrontpage'] = new \Album($article->params['withGallery']);
            }

            if (array_key_exists('withGalleryInt', $article->params)
                && !empty($article->params['withGalleryInt'])
            ) {
                $params['galleryForInner'] = new \Album($article->params['withGalleryInt']);
            }

            \Onm\StringUtils::convertToUtf8($galleries);
            $this->view->assign('galleries', $galleries);
        }

        // Force URI generation
        $article->uri = $article->uri;

        $params['article'] = \Onm\StringUtils::convertToUtf8($article);

        return new JsonResponse($params);
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

        $data = array(
            'agency'         => $postReq->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'body'           => $postReq->filter('body', ''),
            'category'       => $postReq->getDigits('category'),
            'content_status' => (empty($contentStatus)) ? 0 : 1,
            'description'    => $postReq->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'endtime'        => $postReq->filter('endtime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'fk_author'      => $postReq->filter('fk_author', 0, FILTER_VALIDATE_INT),
            'fk_video'       => $postReq->getDigits('fk_video', ''),
            'fk_video2'      => $postReq->getDigits('fk_video2', ''),
            'footer_video2'  => $postReq->filter('footer_video2', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'frontpage'      => (empty($frontpage)) ? 0 : 1,
            'id'             => $id,
            'img1'           => $postReq->getDigits('img1', null),
            'img1_footer'    => $postReq->get('img1_footer', null),
            'img2'           => $postReq->getDigits('img2', null),
            'img2_footer'    => $postReq->get('img2_footer', null),
            'metadata'       => \Onm\StringUtils::normalizeMetadata($postReq->filter('metadata', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)),
            'relatedFront'   => json_decode($postReq->get('relatedFront', '')),
            'relatedHome'    => json_decode($postReq->get('relatedHome', '')),
            'relatedInner'   => json_decode($postReq->get('relatedInner', '')),
            'slug'           => $postReq->filter('slug', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'starttime'      => $postReq->filter('starttime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'subtitle'       => $postReq->filter('subtitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'summary'        => $postReq->filter('summary', ''),
            'title'          => $postReq->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'title_int'      => $postReq->filter('title_int', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'with_comment'   => (empty($withComment)) ? 0 : 1,
            'params'         => array(
                'agencyBulletin'    => array_key_exists('agencyBulletin', $params) ? $params['agencyBulletin'] : '',
                'bodyLink'          => array_key_exists('bodyLink', $params) ? $params['bodyLink'] : '',
                'imageHome'         => array_key_exists('imageHome', $params) ? $params['imageHome'] : null,
                'imageHomeFooter'   => array_key_exists('imageHomeFooter', $params) ? $params['imageHomeFooter'] : null,
                'imageHomePosition' => array_key_exists('imageHomePosition', $params) ? $params['imageHomePosition'] : '',
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
            ),
        );

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
}
