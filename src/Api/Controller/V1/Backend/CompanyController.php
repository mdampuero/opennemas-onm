<?php

namespace Api\Controller\V1\Backend;

use Common\Model\Entity\Content;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.companies';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'COMPANY_CREATE',
        'delete' => 'COMPANY_DELETE',
        'patch'  => 'COMPANY_UPDATE',
        'update' => 'COMPANY_UPDATE',
        'list'   => 'COMPANY_ADMIN',
        'save'   => 'COMPANY_CREATE',
        'show'   => 'COMPANY_UPDATE',
    ];

    protected $module = 'company';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_company_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.company';

    /**
     * Loads extra data related to the given contents.
     *
     * @param array $items The items array
     *
     * @return array Array of extra data.
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items),
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ],
            'sectors' => [
                _('Aerospace'),
                _('Agriculture'),
                _('Automobiles'),
                _('Breeding'),
                _('Comerce'),
                _('Construction'),
                _('Creative'),
                _('Culture'),
                _('Education'),
                _('Electronic'),
                _('Energy'),
                _('Entertainment'),
                _('Finance'),
                _('Fishing'),
                _('Food'),
                _('Healthcare'),
                _('Information technology'),
                _('Meat'),
                _('Mining'),
                _('Petroleum'),
                _('Pharmaceutical'),
                _('Real estate'),
                _('Telecommunications'),
                _('Tobacco'),
                _('Textile'),
                _('Transport'),
                _('Wood'),
                _('Other')
            ],
            'timetable' => [
                [ 'name' => _('Monday'), 'enabled' => false, 'schedules' => [] ],
                [ 'name' => _('Tuesday'), 'enabled' => false, 'schedules' => [] ],
                [ 'name' => _('Wednesday'), 'enabled' => false, 'schedules' => [] ],
                [ 'name' => _('Thursday'), 'enabled' => false, 'schedules' => [] ],
                [ 'name' => _('Friday'), 'enabled' => false, 'schedules' => [] ],
                [ 'name' => _('Saturday'), 'enabled' => false, 'schedules' => [] ],
                [ 'name' => _('Sunday'), 'enabled' => false, 'schedules' => [] ],
                [ 'name' => _('Holiday'), 'enabled' => false, 'schedules' => [] ],
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys();
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
     * Previews an article in frontend by sending the article info by POST.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function savePreviewAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $this->get('core.locale')->setContext('frontend')
            ->setRequestLocale($request->get('locale'));

        $company = new Content([ 'pk_content' => 0 ]);

        $data = $request->request->filter('item');
        $data = json_decode($data, true);

        foreach ($data as $key => $value) {
            if (isset($value) && !empty($value)) {
                $company->{$key} = $value;
            }
        }

        $company = $this->get('data.manager.filter')->set($company)
            ->filter('localize', [ 'keys'   => $this->get($this->service)->getL10nKeys() ])
            ->get();

        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        list($positions, $advertisements) = $this->getAdvertisements();

        $params = [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'item'           => $company,
            'content'        => $company,
            'contentId'      => $company->pk_content
        ];

        $this->view->assign($params);

        $this->get('session')->set(
            'last_preview',
            $this->view->fetch('company/company.tpl')
        );

        return new Response('OK');
    }
}
