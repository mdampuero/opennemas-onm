<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the keywords.
 */
class KeywordsController extends Controller
{
    /**
     * Lists all the keywords
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('KEYWORD_MANAGER')
     *     and hasPermission('PCLAVE_ADMIN')")
     *
     */
    public function listAction()
    {
        return $this->render('keywords/list.tpl');
    }

    /**
     * Shows the keyword information given its id.
     *
     * @param integer $id The keyword id.
     *
     * @return Response The response object
     *
     * @Security("hasExtension('KEYWORD_MANAGER')
     *     and hasPermission('PCLAVE_UPDATE')")
     */
    public function showAction($id)
    {
        $keyword = new \PClave();
        $keyword->read($id);

        return $this->render(
            'keywords/new.tpl',
            [ 'keyword' => $keyword, 'tipos' => \PClave::getTypes() ]
        );
    }

    /**
     * Shows the form for creating a new keyword and handles its form.
     *
     * @param Request $request The request object
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('KEYWORD_MANAGER')
     *     and hasPermission('PCLAVE_CREATE')")
     *
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $data = array(
                'pclave' => $request->request->filter('pclave', '', FILTER_SANITIZE_STRING),
                'tipo'   => $request->request->filter('tipo', '', FILTER_SANITIZE_STRING),
                'value'  => $request->request->filter('value', '', FILTER_SANITIZE_STRING),
            );

            $keyword = new \PClave();
            $keyword->create($data);

            $this->get('session')->getFlashBag()->add('success', _('Keyword created successfully'));

            return $this->redirect(
                $this->generateUrl(
                    'admin_keyword_show',
                    array('id' => $keyword->id)
                )
            );
        }

        return $this->render(
            'keywords/new.tpl',
            [ 'tipos' => \PClave::getTypes() ]
        );
    }

    /**
     * Updates the Pclave information given its new data.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('KEYWORD_MANAGER')
     *     and hasPermission('PCLAVE_UPDATE')")
     *
     */
    public function updateAction(Request $request)
    {
        $data = array(
            'id'     => $request->query->getDigits('id'),
            'pclave' => $request->request->filter('pclave', '', FILTER_SANITIZE_STRING),
            'tipo'   => $request->request->filter('tipo', '', FILTER_SANITIZE_STRING),
            'value'  => $request->request->filter('value', '', FILTER_SANITIZE_STRING),
        );

        $keyword = new \PClave();
        $keyword->update($data);

        $this->get('session')->getFlashBag()->add('success', _('Keyword updated successfully'));

        return $this->redirect(
            $this->generateUrl(
                'admin_keyword_show',
                array('id' => $data['id'])
            )
        );
    }

    /**
     * Given a text, this action replaces all the registered keywords with the
     * valid keyword link
     *
     * @param Request $request the request object
     *
     * @return Reponse the response object
     *
     * @Security("hasExtension('KEYWORD_MANAGER')")
     */
    public function autolinkAction(Request $request)
    {
        $content = $request->request->get('text', null);

        $newContent = '';
        if (!empty($content)) {
            $keyword = new \PClave();
            $terms = $keyword->find();

            $newContent = $keyword->replaceTerms($content, $terms);
        }

        return new Response($newContent);
    }
}
