<?php
/**
 * Handles the generic actions for contents
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Common\Core\Annotation\BotDetector;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the generic actions for contents
 *
 * @package Frontend_Controllers
 */
class ContentsController extends Controller
{
    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function printAction(Request $request)
    {
        $dirtyID = $request->query->filter('id', '', FILTER_SANITIZE_STRING);

        // Resolve content ID, we dont know which type the content is so we have to
        // perform some calculations
        preg_match("@(?P<date>\d{1,14})(?P<id>\d+)@", $dirtyID, $matches);

        if (empty($matches)) {
            throw new ResourceNotFoundException();
        }

        try {
            $content = $this->container->get('api.service.content_old')->getItem($matches['id']);
        } catch (GetItemException $e) {
            throw new ResourceNotFoundException();
        }

        return $this->redirect($this->container->get('core.helper.url_generator')->generate($content), 301);
    }

    /**
     * Increments the num views for a content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @BotDetector
     */
    public function statsAction(Request $request)
    {
        $contentId = $request->query->getDigits('content_id', 0);

        // Raise exception if content id is not provided
        if ($contentId <= 0) {
            throw new ResourceNotFoundException();
        }

        $saved = $this->get('content_views_repository')->setViews($contentId);

        $httpCode = 400;
        $content  = "false";

        if ($saved) {
            $httpCode = 200;
            $content  = "Ok";
        }

        return new Response($content, $httpCode);
    }

    /**
     * Redirects from a content permalink to a content url
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function permalinkAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id');

        if ($id <= 0) {
            throw new ResourceNotFoundException();
        }

        // The only way to know which type of content is to query for the entire
        // content and then do the translation to the entity repository service
        // Not very proud of this.
        $content = new \Content($id);
        $content = $this->get('entity_repository')->find(classify($content->content_type_name), $id);

        if (!is_object($content)) {
            throw new ResourceNotFoundException();
        }

        $url = $this->get('core.helper.url_generator')->generate($content);
        $url = $this->get('core.decorator.url')->prefixUrl($url);

        return new \Symfony\Component\HttpFoundation\RedirectResponse($url);
    }
}
