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

        if (empty($params['q']) || !empty($params['search'])) {
            unset($params['q']);
        }
        if (!empty($params['q']) && !empty($params['search']) && $params['q'] != $params['search']) {
            $params['search'] = '';
        }
        if (!empty($params['search']) && empty($params['place'])) {
            $params['place']  = $params['search'];
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
        $ch          = $this->container->get('core.helper.company');
        $placesArray = $ch->getLocalitiesAndProvices();

        $date   = date('Y-m-d H:i:s');
        $place  = '';
        $search = '';
        //This variable indicates when $place is a true place (locality or province) in order to build the sql
        $placeFound = false;

        //if first parameter is not empty
        if (!empty($params['place'])) {
            //match current param with places json
            $place = $this->matchPlace($params['place']);
            //if second parameter is not empty, find it on custom fields settings
            if (!empty($params['search'])) {
                $search = $this->matchCustomfields($params['search']);
            }
            //set $place as a true place
            $placeFound = true;
            //if no match at $place, match with custom fields settings and set $placeFound to false
            if (empty($place)) {
                $place      = $this->matchCustomfields($params['place']);
                $placeFound = false;
            }
        }
        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }
        //Divide SQL in order to create several inner joins when necessary
        $select    = 'SELECT * FROM contents ';
        $innerJoin = '';
        $where     = 'WHERE ';

        if (!empty($place)) {
            $innerJoin = 'INNER JOIN contentmeta as t1 on pk_content = t1.fk_content ';
            //if $place is a real place, $place structure must be like ['province' => ['nm' => 'Viana do Bolo', ...]],
            //with only 1 root element
            if ($placeFound) {
                $where .= sprintf(
                    't1.meta_name = "%s" AND t1.meta_value = "%s" ',
                    //Used key and reset because array key is unknown
                    key($place[0]),
                    reset($place[0])['nm']
                );
            } else {
                //if not placefound $place structure must be like ['field' => ['name' => 'aasd' ...]],
                //this array can contain as root element as duplicated words were defined in custom fields
                foreach ($place as $key => $element) {
                    if ($key !== array_key_first($place)) {
                        $where .= 'OR ';
                    }
                    $where .= 't1.meta_name = "' .
                        key($element) .
                        '" AND t1.meta_value LIKE "%\"' .
                        reset($element)['name'] .
                        '\"%" ';
                }
            }
            //if $search, add inner join and look for province or locality match
            $where .= 'AND ';
            if (!empty($search)) {
                $innerJoin .= 'INNER JOIN contentmeta as t2 on pk_content = t2.fk_content ';
                foreach ($search as $key => $element) {
                    if ($key !== array_key_first($search)) {
                        $where .= 'OR ';
                    }
                    $where .= 't2.meta_name = "' .
                        key($element) .
                        '" AND t2.meta_value LIKE "%\"' .
                        reset($element)['name'] .
                        '\"%" ';
                }
                $where .= 'AND ';
            }
        }
        //build default filter
        $where .= sprintf('content_type_name = "company" and in_litter = 0 and content_status = 1 ' .
        'and (starttime is null or starttime < "%s") ' .
        'and (endtime is null or endtime >= "%s") ', $date, $date);
        //filter by title if $q was provided
        if (!empty($params['q'])) {
            $where .= 'and title like "%' . $params['q'] . '%" ';
        }

        $orderby = sprintf(
            'order by title asc limit %d offset %d',
            $params['epp'],
            $params['epp'] * ($params['page'] - 1)
        );

        $sql      = $select . $innerJoin . $where . $orderby;
        $response = $this->get('api.service.content')->getListBySql($sql);

        // No first page and no contents
        if ($params['page'] > 1) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        $params['places'] = array_merge(
            json_decode($placesArray['localities']),
            json_decode($placesArray['provinces'])
        );
        //Set place and search params for tpl
        $defaultPlace  = $placeFound ? reset($place[0]) : '';
        $defautSeaarch = $placeFound ?
            $this->matchCustomfields($params['search']) :
            $this->matchCustomfields($params['place']);

        $params['search_params'] = [
            'place' => $defaultPlace,
            'search' => $defautSeaarch
        ];

        $params['x-tags']    .= ',company-frontpage';
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

    protected function matchCustomfields($search)
    {
        $ch = $this->container->get('core.helper.company');

        $suggestedWords = $ch->getSuggestedFields();
        $matchedValues  = [];
        //find $search in suggested fields by its value
        foreach ($suggestedWords as $element) {
            if (is_array($element) && array_key_exists('values', $element)) {
                $match = array_filter($element['values'], function ($element) use ($search) {
                    return $element['value'] == $search;
                });
            }
            //if found, parse te value to array where the key is the custom field key property
            if ($match) {
                array_push($matchedValues, [
                    $element['key']['value'] => current($match)
                ]);
            }
        }
        return $matchedValues;
    }

    protected function matchPlace($search)
    {
        $result = [];
        $ch     = $this->container->get('core.helper.company');
        $places = $ch->getLocalitiesAndProvices();
        //find $search in provinces and localities JSON and parse result to an array
        //the array key must match meta_name on db and value must match on meta_value
        $provinceMatch = array_filter(json_decode($places['provinces'], true), function ($element) use ($search) {
            return $element['slug'] == $search;
        });
        if ($provinceMatch) {
            $value = array_shift($provinceMatch);
            array_push($result, [
                'province' => $value
            ]);
        } else {
            $localityMatch = array_filter(json_decode($places['localities'], true), function ($element) use ($search) {
                return $element['slug'] == $search;
            });
            if ($localityMatch) {
                $value = array_shift($localityMatch);
                array_push($result, [
                    'locality' => $value
                ]);
            }
        }
        return $result;
    }

    public function getSuggestionsAction()
    {
        $ch        = $this->container->get('core.helper.company');
        $suggested = $ch->getSuggestedFields();

        $parsedSuggested = [];

        foreach ($suggested as $element) {
            if (is_array($element) && array_key_exists('values', $element)) {
                $parsedSuggested = array_merge($parsedSuggested, $element['values']);
            }
        }
        //Filter suggestions with same value
        $tempArray           = array_unique(array_column($parsedSuggested, 'value'));
        $filteredSuggestions = array_values(array_intersect_key($parsedSuggested, $tempArray));

        return new JsonResponse($filteredSuggestions);
    }

    public function getPlacesAction()
    {
        $ch           = $this->container->get('core.helper.company');
        $places       = $ch->getLocalitiesAndProvices();
        $parsedPlaces = [];

        foreach ($places as $place) {
            $parsedPlaces = array_merge($parsedPlaces, json_decode($place, true));
        }

        return new JsonResponse($parsedPlaces);
    }
}
