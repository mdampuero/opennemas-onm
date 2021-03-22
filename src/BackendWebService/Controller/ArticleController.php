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

        $data             = $request->request->all();
        $data['category'] = $data['category_id'];
        $msg              = $this->get('core.messenger');

        if (!$article->create($data)) {
            $msg->add(_('Unable to create the new article.'), 'error', 400);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Article successfully created.')
        );

        $url = $this->generateUrl('backend_articles_list');

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
                $params['locale'] = $request->get('locale');
            }

            $url = $this->generateUrl('backend_article_show', $params);
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

        if (empty($request->request)) {
            $msg->add(_('Article data sent not valid.'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $data = $request->request->all();

        $data['category'] = $data['category_id'];

        if (!$article->update($data)) {
            $msg->add(_('Unable to update the article.'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if ($data['content_status'] == 0) {
            $article->dropFromAllHomePages();
        }

        $ts = $this->get('api.service.tag');

        $msg->add(_('Article successfully updated.'), 'success');
        $response['message'] = $msg->getMessages()[0];
        $response['tags']    =
            $ts->responsify($ts->getListByIds($article->tags)['items']);

        return new JsonResponse($response, $msg->getCode());
    }
}
