<?php

namespace Backend\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Intl;

class StoreController extends Controller
{
    /**
     * Displays the wizard form for checkout.
     *
     * @return Response The response object.
     */
    public function checkoutAction()
    {
        $id     = $this->get('core.instance')->getClient();
        $client = [];

        if (!empty($id)) {
            $client = $this->get('orm.manager')
                ->getRepository('Client', 'manager')
                ->find($id);

            $client = $client->getData();
        }

        $countries = Intl::getRegionBundle()->getCountryNames();
        $taxes     = $this->get('vat')->getTaxes();
        $provinces = [
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
                'taxes'     => $taxes
            ]
        );
    }

    /**
     * Displays the list of available modules.
     *
     * @return Response The response object.
     */
    public function listAction()
    {
        $plans = \Onm\Module\ModuleManager::getAvailablePacks();
        $plans[] = [
            'id'   => 'OTHER',
            'name' => _('Others')
        ];

        return $this->render('store/list.tpl', [ 'plans' => $plans ]);
    }
}
