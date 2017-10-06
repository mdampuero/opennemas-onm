<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Framework\ORM\Entity\Client;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class DomainController extends Controller
{
    /**
     * Lists all the available ads.
     *
     * @return Response The response object.
     */
    public function listAction()
    {
        return $this->render('domain/list.tpl', [
            'ssl_enabled' => in_array(
                'es.openhost.module.frontend_ssl',
                $this->get('core.instance')->activated_modules
            )
        ]);
    }

    /**
     * Lists all the available ads.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function addAction(Request $request)
    {
        $client    = [];
        $params    = [];
        $token     = null;
        $extension = null;

        $instance = $this->get('core.instance');
        $em       = $this->get('orm.manager');

        if (!empty($instance->client)) {
            try {
                $client = $em->getRepository('client')
                    ->find($instance->client);

                $params = [ 'customerId' => $client->id ];
                $client = $client->getData();
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }

        $uuid = 'es.openhost.domain.redirect';
        if ($request->query->get('create')) {
            $uuid = 'es.openhost.domain.create';
        }

        $converter = $em->getConverter('Extension');
        $extension = $em->getRepository('Extension')
            ->findOneBy(sprintf('uuid = "%s"', $uuid));
        $extension = $converter->responsify($extension->getData());

        $lang = $this->get('core.locale')->getLocaleShort();

        $extension['name']        = array_key_exists($lang, $extension['name']) ?
            $extension['name'][$lang] : $extension['name']['en'];
        $extension['about']       = array_key_exists($lang, $extension['about']) ?
            $extension['about'][$lang] : $extension['about']['en'];
        $extension['description'] = array_key_exists($lang, $extension['description']) ?
            $extension['description'][$lang] : $extension['description']['en'];

        $countries    = $this->get('core.geo')->getCountries();
        $provinces    = $this->get('core.geo')->getRegions('ES');
        $taxes        = $this->get('vat')->getTaxes();
        $tokenFactory = $this->get('onm.braintree.factory')->get('ClientToken');
        $token        = $tokenFactory::generate($params);

        return $this->render('domain/add.tpl', [
            'client'    => $client,
            'extension' => $extension,
            'countries' => $countries,
            'provinces' => $provinces,
            'taxes'     => $taxes,
            'token'     => $token
        ]);
    }
}
