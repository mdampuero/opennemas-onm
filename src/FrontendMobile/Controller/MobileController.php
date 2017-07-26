<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FrontendMobile\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package FrontendMobile_Controllers
 */
class MobileController extends Controller
{
    /**
     * Redirects old mobile frontpage to /
     *
     * @return Response the response object
     */
    public function redirectToFrontpageAction(Request $request)
    {
        return $this->redirect($this->generateUrl('frontend_frontpage'));
    }

    /**
     * Redirects old mobile ctegory frontpage to /section/category
     *
     * @return Response the response object
     */
    public function redirectToCategoryFrontpageAction(Request $request)
    {
        $categoryName = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);

        return $this->redirect($this->generateUrl(
            'frontend_frontpage_category',
            [ 'category' => $categoryName ]
        ));
    }

    /**
     * Redirects old mobile inner article to /article/XXX
     *
     * @return Response the response object
     */
    public function articleInnerRedirectAction(Request $request)
    {
        $dirtyID      = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $urlSlug      = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        $article = $this->get('content_url_matcher')
            ->matchContentUrl('article', $dirtyID, $urlSlug, $categoryName);

        if (empty($article)) {
            throw new ResourceNotFoundException();
        }

        return $this->redirect('/'.$article->uri);
    }

    /**
     * Redirects old mobile ctegory frontpage to /opinion
     *
     * @return Response the response object
     */
    public function opinionFrontpageRedirectAction()
    {
        return $this->redirect($this->generateUrl('frontend_opinion_frontpage'));
    }

    /**
     * Redirects old mobile inner article to /opinoin/XXX
     *
     * @return Respone the response object
     */
    public function opinionInnerRedirectAction(Request $request)
    {
        $dirtyID = $request->query->filter('opinion_id', '', FILTER_SANITIZE_STRING);
        $urlSlug = $request->query->filter('opinion_title', '', FILTER_SANITIZE_STRING);

        $opinion = $this->get('content_url_matcher')
            ->matchContentUrl('opinion', $dirtyID, $urlSlug);

        if (empty($opinion)) {
            throw new ResourceNotFoundException();
        }

        return $this->redirect('/'.$opinion->uri);
    }
}
