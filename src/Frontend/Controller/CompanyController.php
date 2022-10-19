<?php

namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CompanyController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list' => 'company-frontpage',
        'show' => 'company-inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list' => 'company_frontpage',
        'show' => 'company_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'list' => [ 1, 2, 5, 6, 7 ],
        'show' => [ 1, 2, 5, 6, 7 ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $queries = [
        'list' => [ 'page', 'sector', 'title' ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'list'   => 'frontend_companies',
        'show'   => 'frontend_company_show',
        'search' => 'frontend_company_search'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.company';

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list'   => 'company/list.tpl',
        'search' => 'company/list.tpl',
        'show'   => 'company/item.tpl'
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.companies';

    /**
     * Displays a frontpage basing on the parameters in the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function searchAction(Request $request)
    {
        $this->checkSecurity($this->extension);

        $action = 'list';
        $params = $request->query->all();

        if ($params['q'] && empty($params['q'])) {
            unset($params['q']);
        }
        if (!empty($params['q']) && !empty($params['search']) && $params['q'] == $params['search']) {
            unset($params['q']);
        }
        if (!empty($params['q']) && !empty($params['search']) && $params['q'] != $params['search']) {
            $params['search'] = '';
        }
        if (!empty($params['search']) && empty($params['place'])) {
            $params['place'] =  $params['search'];
            $params['search'] = '';
        }

        $expected = $this->getExpectedUri($action, $params);

        return new RedirectResponse($expected, 302);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $ch    = $this->container->get('core.helper.company');

        $placesArray = $ch->getLocalitiesAndProvices();

        $date  = date('Y-m-d H:i:s');
        $place = '';
        $search = '';
        $placeFound = false;
        if (!empty($params['place'])) {
            $place = $this->matchPlace($params['place']);
            if (!empty($params['search'])) {
                $search = $this->matchCustomfields($params['search']);
            }
            $placeFound = true;
            if (empty($place)) {
                $place = $this->matchCustomfields($params['place']);
                $placeFound = false;
            }
        }

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $sql = 'SELECT * FROM contents ';

        if (!empty($place)){
            $sql .= 'INNER JOIN contentmeta on pk_content = fk_content WHERE ';
            if ($placeFound) {
                $sql .= sprintf('meta_name = "%s" AND meta_value = "%s" ', key($place[0]), reset($place[0]));
            } else {
                foreach ($place as $key => $element) {
                    if ($key !== array_key_first($place)){
                        $sql .= 'OR ';
                    }

                    $sql .= 'meta_name = "' . key($element) . '" AND meta_value LIKE "%\"' . reset($element) . '\"%" ';
                }
            }
            $sql .= 'AND ';
            if (!empty($search)) {
                $sql .= sprintf('meta_name = "%s" AND meta_value = "%s" ', key($search[0]), reset($search[0]));
                $sql .= 'AND ';
            }
        } else {
            $sql .= 'WHERE ';
        }

        $sql .= sprintf('content_type_name = "company" and in_litter = 0 and content_status = 1 ' .
            'and (starttime is null or starttime < "%s") ' .
            'and (endtime is null or endtime >= "%s") ', $date, $date );

        if (!empty($params['q'])) {
            $sql .= 'and title like "%' . $params['q'] . '%" ';
        }

        $sql .= sprintf(
            'order by title asc limit %d offset %d',
            $params['epp'],
            $params['epp'] * ($params['page'] - 1)
        );

        $response = $this->get('api.service.content')->getListBySql($sql);
        // dump($sql);
        // dump($response);
        // die();
        // No first page and no contents
        if ($params['page'] > 1) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        $suggested = $ch->getSuggestedFields();

        $parsedSuggested = [];

        foreach ($suggested as $key => $value) {
            if (is_array($value)) {
                $parsed = array_map(function ($element){
                    return array_values($element);
                }, $value);
                $parsedSuggested = array_merge($parsedSuggested, array_values($parsed));
            }
        }

        $parsedSuggested = array_map(function ($element){
            if (!$element[0])
                return $element;
            return $element[0];
        }, $parsedSuggested);

        $params['suggested_fields'] = $parsedSuggested;

        $params['places'] = array_merge(
                json_decode($placesArray['localities']),
                json_decode($placesArray['provinces'])
            );
        $params['sectors'] = [
            [ 'name' => 'aeroespace', 'title' => _('Aerospace') ],
            [ 'name' => 'agriculture', 'title' => _('Agriculture') ],
            [ 'name' => 'automotive', 'title' => _('Automotive') ],
            [ 'name' => 'breeding', 'title' => _('Breeding') ],
            [ 'name' => 'comerce', 'title' => _('Comerce') ],
            [ 'name' => 'construction', 'title' => _('Construction') ],
            [ 'name' => 'advertising_marketing', 'title' => _('Advertising and marketing') ],
            [ 'name' => 'culture', 'title' => _('Culture') ],
            [ 'name' => 'education', 'title' => _('Education') ],
            [ 'name' => 'electronic', 'title' => _('Electronic') ],
            [ 'name' => 'energy', 'title' => _('Energy') ],
            [ 'name' => 'entertainment', 'title' => _('Entertainment') ],
            [ 'name' => 'finance', 'title' => _('Finance') ],
            [ 'name' => 'maritime', 'title' => _('Maritime') ],
            [ 'name' => 'food', 'title' => _('Food') ],
            [ 'name' => 'healthcare', 'title' => _('Healthcare') ],
            [ 'name' => 'information_technology', 'title' => _('Information technology') ],
            [ 'name' => 'mining', 'title' => _('Mining') ],
            [ 'name' => 'fuels', 'title' => _('Fuels') ],
            [ 'name' => 'pharmaceutical', 'title' => _('Pharmaceutical') ],
            [ 'name' => 'real_estate', 'title' => _('Real estate') ],
            [ 'name' => 'telecommunications', 'title' => _('Telecommunications') ],
            [ 'name' => 'tobacco', 'title' => _('Tobacco') ],
            [ 'name' => 'textile', 'title' => _('Textile') ],
            [ 'name' => 'transport', 'title' => _('Transport') ],
            [ 'name' => 'industry', 'title' => _('Industry') ],
            [ 'name' => 'beauty', 'title' => _('Beauty') ],
            [ 'name' => 'animals', 'title' => _('Animals') ],
            [ 'name' => 'cleaning', 'title' => _('Cleaning') ],
            [ 'name' => 'sport', 'title' => _('Sport') ],
            [ 'name' => 'hostelry', 'title' => _('Hostelry') ],
            [ 'name' => 'services', 'title' => _('Services') ],
            [ 'name' => 'metal', 'title' => _('Metal') ],
            [ 'name' => 'architecture', 'title' => _('Architecture') ],
            [ 'name' => 'other', 'title' => _('Other') ]
        ];

        $params['x-tags'] .= ',company-frontpage';

        $params['contents']   = $response['items'];
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $params['epp'],
            'page'        => $params['page'],
            'total'       => $response['total'],
            'route'       => 'frontend_companies'
        ]);

        $params['tags'] = $this->getTags($response['items']);
    }

    protected function matchCustomfields ($search)
    {
        $ch = $this->container->get('core.helper.company');
        $suggestedWords = $ch->getSuggestedFields();

        $matchedValues = [];
        foreach ($suggestedWords as $suggestedField => $sugestedValues) {
            if (is_array($sugestedValues)) {
                $match = array_filter($sugestedValues, function ($element) use ($search) {
                    return $element['name'] == $search;
                });
            }
            if ($match) {
                array_push($matchedValues, [
                    $suggestedField => current($match)['name']
                ]);
            }
        }
        return $matchedValues;
    }

    protected function matchPlace ($search)
    {
        $result = [];
        $ch = $this->container->get('core.helper.company');
        $places = $ch->getLocalitiesAndProvices();
        $provinceMatch = array_filter(json_decode($places['provinces'], true), function($element) use ($search) {
            return $element['nm'] == $search;
        });
        if ($provinceMatch) {
            $value = array_shift($provinceMatch);
            array_push($result, [
                'province' => $value['nm']
            ]);
        } else {
            $localityMatch = array_filter(json_decode($places['localities'], true), function($element) use ($search) {
                return $element['nm'] == $search;
            });
            if ($localityMatch) {
                $value = array_shift($localityMatch);
                array_push($result, [
                    'locality' => $value['nm']
                ]);
            }
        }
        return $result;
    }

    public function getSuggestionsAction() {
        $ch        = $this->container->get('core.helper.company');
        $suggested = $ch->getSuggestedFields();

        $parsedSuggested = [];

        foreach ($suggested as $key => $value) {
            if (is_array($value)) {
                $parsed = array_map(function ($element){
                    return array_values($element);
                }, $value);
                $parsedSuggested = array_merge($parsedSuggested, array_values($parsed));
            }
        }

        $parsedSuggested = array_map(function ($element){
            if (!$element[0])
                return $element;
            return $element[0];
        }, $parsedSuggested);

        return new JsonResponse($parsedSuggested);
    }
}
