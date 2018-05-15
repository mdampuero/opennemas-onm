<?php

namespace Backend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Intl;
use Common\Core\Annotation\Security;

class StoreController extends Controller
{
    /**
     * Displays the wizard form for checkout.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('ADMIN')")
     */
    public function checkoutAction()
    {
        $id     = $this->get('core.instance')->getClient();
        $client = [];
        $params = [];

        if (!empty($id)) {
            try {
                $client = $this->get('orm.manager')
                    ->getRepository('Client', 'manager')
                    ->find($id);

                $params = [ 'customerId' => $client->id ];
                $client = $this->get('orm.manager')->getConverter('Client')
                    ->responsify($client);
            } catch (\Exception $e) {
                getService('error.log')->error($e->getMessage());
            }
        }

        $countries    = Intl::getRegionBundle()->getCountryNames();
        $taxes        = $this->get('vat')->getTaxes();
        $tokenFactory = $this->get('onm.braintree.factory')->get('ClientToken');
        $token        = $tokenFactory::generate($params);
        $provinces    = [
            'Álava', 'Albacete', 'Alicante/Alacant', 'Almería', 'Asturias',
            'Ávila', 'Badajoz', 'Barcelona', 'Burgos', 'Cáceres', 'Cádiz',
            'Cantabria', 'Castellón/Castelló', 'Ceuta', 'Ciudad Real',
            'Córdoba', 'Cuenca', 'Girona', 'Las Palmas', 'Granada',
            'Guadalajara', 'Guipúzcoa', 'Huelva', 'Huesca', 'Illes Balears',
            'Jaén', 'A Coruña', 'La Rioja', 'León', 'Lleida', 'Lugo', 'Madrid',
            'Málaga', 'Melilla', 'Murcia', 'Navarra', 'Ourense', 'Palencia',
            'Pontevedra', 'Salamanca', 'Segovia', 'Sevilla', 'Soria',
            'Tarragona', 'Santa Cruz de Tenerife', 'Teruel', 'Toledo',
            'Valencia/València', 'Valladolid', 'Vizcaya', 'Zamora', 'Zaragoza'
        ];

        return $this->render(
            'store/checkout.tpl',
            [
                'client'    => $client,
                'countries' => $countries,
                'provinces' => $provinces,
                'taxes'     => $taxes,
                'token'     => $token
            ]
        );
    }

    /**
     * Displays the list of available modules.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('ADMIN')")
     */
    public function listAction()
    {
        return $this->render('store/list.tpl');
    }
}
