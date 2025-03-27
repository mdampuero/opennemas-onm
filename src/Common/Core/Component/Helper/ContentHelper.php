<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Helper;

use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Symfony\Component\DependencyInjection\Container;

/**
* Perform searches in Database related with one content
*/
class ContentHelper
{
    /**
     * The services container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The content service.
     *
     * @var ContentService
     */
    protected $service;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $template;

    /**
     * The entity repository.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The tags service.
     *
     * @var TagService
     */
    protected $tagService;

    /**
     * The subscriptions helper.
     *
     * @var SubscriptionHelper
     */
    protected $subscriptionHelper;

    /**
     * The locale core.
     *
     * @var Locale
     */
    protected $locale;

    /**
     * Initializes the ContentHelper.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container          = $container;
        $this->service            = $this->container->get('api.service.content');
        $this->template           = $this->container->get('core.template.frontend');
        $this->entityManager      = $this->container->get('entity_repository');
        $this->cache              = $this->container->get('cache.connection.instance');
        $this->tagService         = $this->container->get('api.service.tag');
        $this->subscriptionHelper = $this->container->get('core.helper.subscription');
        $this->locale             = $this->container->get('core.locale');
    }

    /**
     * Returns the body for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content body.
     */
    public function getBody($item = null) : ?string
    {
        $map = [
            'album' => 'description',
            'poll'  => 'description',
            'video' => 'description'
        ];

        $value = array_key_exists($this->getType($item), $map)
            ? $this->getProperty($item, $map[$this->getType($item)])
            : $this->getProperty($item, 'body');

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the body with live updates for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content body with live updates.
     */
    public function getBodyWithLiveUpdates($item = null) : ?string
    {
        $contentBody = $this->getBody($item) ?? '';

        if ($this->isLiveBlog($item)) {
            foreach ($item->live_blog_updates as $update) {
                $contentBody .= ' ' . $update['body'];
            }
        }

        return !empty($contentBody) ? $contentBody : null;
    }
    /**
     * Get the proper cache expire date for scheduled contents.
     *
     * @return mixed The expire cache datetime in "Y-m-d H:i:s" format or null.
     */
    public function getCacheExpireDate()
    {
        $oqlStart = sprintf(
            'content_status = 1 and in_litter != 1 and'
            . ' content_type_name != "advertisement" and'
            . ' (starttime !is null and starttime > "%s")'
            . ' order by starttime asc limit 1',
            date('Y-m-d H:i:s')
        );

        $oqlEnd = sprintf(
            'content_status = 1 and in_litter != 1 and'
            . ' content_type_name != "advertisement" and'
            . ' (endtime !is null and endtime > "%s")'
            . ' order by endtime desc limit 1',
            date('Y-m-d H:i:s')
        );

        try {
            $start = $this->service->getItemBy($oqlStart);
        } catch (\Exception $e) {
            $start = null;
        }

        try {
            $end = $this->service->getItemBy($oqlEnd);
        } catch (\Exception $e) {
            $end = null;
        }

        if (empty($start) && empty($end)) {
            return null;
        }

        // Get valid date formated or null
        $starttime = !empty($start) && $start->starttime
            ? $start->starttime->format('Y-m-d H:i:s') : null;
        $endtime   = !empty($end) && $end->endtime
            ? $end->endtime->format('Y-m-d H:i:s') : null;

        return min(array_filter([ $starttime, $endtime ]));
    }

    /**
     * Retrieves a list of event types slugs.
     *
     * @return string[] An array of event type slugs.
     */
    public function getEventTypes()
    {
        return [
            1 => [
                'id'       => 1,
                'name'     => _('Eventos Corporativos y Empresariales'),
                'slug'     => 'corporate',
                'category' => true,
                'parent'   => null,
            ],
            2 => [
                'id'       => 2,
                'name'     => _('Conferencias'),
                'slug'     => 'conferencias',
                'category' => false,
                'parent'   => 1,
            ],
            3 => [
                'id'       => 3,
                'name'     => _('Seminarios'),
                'slug'     => 'seminarios',
                'category' => false,
                'parent'   => 1,
            ],
            4 => [
                'id'       => 4,
                'name'     => _('Webinars'),
                'slug'     => 'webinars',
                'category' => false,
                'parent'   => 1,
            ],
            5 => [
                'id'       => 5,
                'name'     => _('Ferias y exposiciones'),
                'slug'     => 'ferias-exposiciones',
                'category' => false,
                'parent'   => 1,
            ],
            6 => [
                'id'       => 6,
                'name'     => _('Ruedas de prensa'),
                'slug'     => 'ruedas-de-prensa',
                'category' => false,
                'parent'   => 1,
            ],
            7 => [
                'id'       => 7,
                'name'     => _('Talleres y capacitaciones'),
                'slug'     => 'talleres-capacitaciones',
                'category' => false,
                'parent'   => 1,
            ],
            8 => [
                'id'       => 8,
                'name'     => _('Lanzamientos de productos'),
                'slug'     => 'lanzamientos-productos',
                'category' => false,
                'parent'   => 1,
            ],
            9 => [
                'id'       => 9,
                'name'     => _('Reuniones de networking'),
                'slug'     => 'reuniones-networking',
                'category' => false,
                'parent'   => 1,
            ],
            10 => [
                'id'       => 10,
                'name'     => _('Eventos de team building'),
                'slug'     => 'team-building',
                'category' => false,
                'parent'   => 1,
            ],
            11 => [
                'id'       => 11,
                'name'     => _('Cumbres y foros'),
                'slug'     => 'cumbres-foros',
                'category' => false,
                'parent'   => 1,
            ],
            12 => [
                'id'       => 12,
                'name'     => _('Eventos Sociales'),
                'slug'     => 'social',
                'category' => true,
                'parent'   => null,
            ],
            13 => [
                'id'       => 13,
                'name'     => _('Bodas'),
                'slug'     => 'bodas',
                'category' => false,
                'parent'   => 12,
            ],
            14 => [
                'id'       => 14,
                'name'     => _('Bautizos'),
                'slug'     => 'bautizos',
                'category' => false,
                'parent'   => 12,
            ],
            15 => [
                'id'       => 15,
                'name'     => _('Comuniones'),
                'slug'     => 'comuniones',
                'category' => false,
                'parent'   => 12,
            ],
            16 => [
                'id'       => 16,
                'name'     => _('Aniversarios'),
                'slug'     => 'aniversarios',
                'category' => false,
                'parent'   => 12,
            ],
            17 => [
                'id'       => 17,
                'name'     => _('Fiestas de cumpleaños'),
                'slug'     => 'fiestas-cumpleanos',
                'category' => false,
                'parent'   => 12,
            ],
            18 => [
                'id'       => 18,
                'name'     => _('Despedidas de soltero/a'),
                'slug'     => 'despedidas-soltero',
                'category' => false,
                'parent'   => 12,
            ],
            19 => [
                'id'       => 19,
                'name'     => _('Baby showers'),
                'slug'     => 'baby-showers',
                'category' => false,
                'parent'   => 12,
            ],
            20 => [
                'id'       => 20,
                'name'     => _('Reuniones familiares'),
                'slug'     => 'reuniones-familiares',
                'category' => false,
                'parent'   => 12,
            ],
            21 => [
                'id'       => 21,
                'name'     => _('Eventos Culturales y Artísticos'),
                'slug'     => 'cultural',
                'category' => true,
                'parent'   => null,
            ],
            22 => [
                'id'       => 22,
                'name'     => _('Conciertos'),
                'slug'     => 'conciertos',
                'category' => false,
                'parent'   => 21,
            ],
            23 => [
                'id'       => 23,
                'name'     => _('Obras de teatro'),
                'slug'     => 'obras-teatro',
                'category' => false,
                'parent'   => 21,
            ],
            24 => [
                'id'       => 24,
                'name'     => _('Exposiciones de arte'),
                'slug'     => 'exposiciones-arte',
                'category' => false,
                'parent'   => 21,
            ],
            25 => [
                'id'       => 25,
                'name'     => _('Presentaciones de libros'),
                'slug'     => 'presentaciones-libros',
                'category' => false,
                'parent'   => 21,
            ],
            26 => [
                'id'       => 26,
                'name'     => _('Festivales culturales'),
                'slug'     => 'festivales-culturales',
                'category' => false,
                'parent'   => 21,
            ],
            27 => [
                'id'       => 27,
                'name'     => _('Proyecciones de cine'),
                'slug'     => 'proyecciones-cine',
                'category' => false,
                'parent'   => 21,
            ],
            28 => [
                'id'       => 28,
                'name'     => _('Eventos Deportivos'),
                'slug'     => 'sports',
                'category' => true,
                'parent'   => null,
            ],
            29 => [
                'id'       => 29,
                'name'     => _('Maratones y carreras'),
                'slug'     => 'maratones-carreras',
                'category' => false,
                'parent'   => 28,
            ],
            30 => [
                'id'       => 30,
                'name'     => _('Torneos y campeonatos'),
                'slug'     => 'torneos-campeonatos',
                'category' => false,
                'parent'   => 28,
            ],
            31 => [
                'id'       => 31,
                'name'     => _('Exhibiciones deportivas'),
                'slug'     => 'exhibiciones-deportivas',
                'category' => false,
                'parent'   => 28,
            ],
            32 => [
                'id'       => 32,
                'name'     => _('Clases y entrenamientos abiertos'),
                'slug'     => 'clases-entrenamientos',
                'category' => false,
                'parent'   => 28,
            ],
            33 => [
                'id'       => 33,
                'name'     => _('Esports (competencias de videojuegos)'),
                'slug'     => 'esports',
                'category' => false,
                'parent'   => 28,
            ],
            34 => [
                'id'       => 34,
                'name'     => _('Eventos Educativos'),
                'slug'     => 'educational',
                'category' => true,
                'parent'   => null,
            ],
            35 => [
                'id'       => 35,
                'name'     => _('Clases y cursos'),
                'slug'     => 'clases-cursos',
                'category' => false,
                'parent'   => 34,
            ],
            36 => [
                'id'       => 36,
                'name'     => _('Conferencias académicas'),
                'slug'     => 'conferencias-academicas',
                'category' => false,
                'parent'   => 34,
            ],
            37 => [
                'id'       => 37,
                'name'     => _('Simposios'),
                'slug'     => 'simposios',
                'category' => false,
                'parent'   => 34,
            ],
            38 => [
                'id'       => 38,
                'name'     => _('Debates y mesas redondas'),
                'slug'     => 'debates-mesas-redondas',
                'category' => false,
                'parent'   => 34,
            ],
            39 => [
                'id'       => 39,
                'name'     => _('Talleres prácticos'),
                'slug'     => 'talleres-practicos',
                'category' => false,
                'parent'   => 34,
            ],
            40 => [
                'id'       => 40,
                'name'     => _('Eventos Religiosos'),
                'slug'     => 'religious',
                'category' => true,
                'parent'   => null,
            ],
            41 => [
                'id'       => 41,
                'name'     => _('Misas y cultos'),
                'slug'     => 'misas-cultos',
                'category' => false,
                'parent'   => 40,
            ],
            42 => [
                'id'       => 42,
                'name'     => _('Peregrinaciones'),
                'slug'     => 'peregrinaciones',
                'category' => false,
                'parent'   => 40,
            ],
            43 => [
                'id'       => 43,
                'name'     => _('Retiros espirituales'),
                'slug'     => 'retiros-espirituales',
                'category' => false,
                'parent'   => 40,
            ],
            44 => [
                'id'       => 44,
                'name'     => _('Festividades religiosas'),
                'slug'     => 'festividades-religiosas',
                'category' => false,
                'parent'   => 40,
            ],
            45 => [
                'id'       => 45,
                'name'     => _('Eventos Benéficos y Comunitarios'),
                'slug'     => 'charity',
                'category' => true,
                'parent'   => null,
            ],
            46 => [
                'id'       => 46,
                'name'     => _('Galas benéficas'),
                'slug'     => 'galas-beneficas',
                'category' => false,
                'parent'   => 45,
            ],
            47 => [
                'id'       => 47,
                'name'     => _('Subastas solidarias'),
                'slug'     => 'subastas-solidarias',
                'category' => false,
                'parent'   => 45,
            ],
            48 => [
                'id'       => 48,
                'name'     => _('Campañas de recolección de donaciones'),
                'slug'     => 'campanas-donaciones',
                'category' => false,
                'parent'   => 45,
            ],
            49 => [
                'id'       => 49,
                'name'     => _('Voluntariados'),
                'slug'     => 'voluntariados',
                'category' => false,
                'parent'   => 45,
            ],
            50 => [
                'id'       => 50,
                'name'     => _('Eventos de concienciación'),
                'slug'     => 'eventos-concienciacion',
                'category' => false,
                'parent'   => 45,
            ],
        ];
    }

    /**
     * Retrieves the name of an event type based on its slug.
     *
     * @param string $slug The slug of the event type.
     * @return string|null The name of the event type or null if the slug doesn't exist.
     */
    public function getEventTypeNameBySlug($slug)
    {
        $eventTypes = $this->getEventTypes();

        // Recorremos el array de eventTypes para encontrar el slug
        foreach ($eventTypes as $eventType) {
            if ($eventType['slug'] === $slug) {
                return $eventType['name']; // Devolvemos el nombre si encontramos el slug
            }
        }

        // Si no se encuentra el slug, devolvemos null
        return null;
    }

    /**
     * Retrieves the creation date of the first item of a given content type.
     *
     * This method queries the service to get the first item of the specified content type that was created after
     * January 1, 2000, and that is not in litter (i.e., `in_litter` is 0). It returns the creation date of the item.
     *
     * @param string $contentTypeName The content type name to search for (default is 'article').
     *
     * @return string|null The creation date of the first item found, or null if no item matches the criteria.
     */
    public function getFirstItemDate($contentTypeName = 'article')
    {
        $oql = sprintf(
            'content_type_name = "%s" and created !is null and created >="2000-01-01" and in_litter = 0'
            . ' order by created asc limit 1',
            $contentTypeName
        );

        try {
            $item = $this->service->getItemBy($oql);
            return $item->created;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the caption for an item.
     *
     * @param mixed $item The item to get caption from.
     *
     * @return string The item caption when the photo is provided as an array (with
     *                the object, the position in the list of related contents of
     *                the same type and the caption).
     */
    public function getCaption($item = null) : ?string
    {
        if (!is_array($item)) {
            return null;
        }

        return array_key_exists('caption', $item)
            ? strip_tags($item['caption'], '<a>')
            : null;
    }

    /**
     * Returns the content of specified type for the provided item.
     *
     * @param mixed  $item        The item to return or the id of the item to return. If
     *                            not provided, the function will try to search the item in
     *                            the template.
     * @param string $type        Content type used to find the content when an id
     *                            provided as first parameter.
     * @param bool   $unpublished Flag to indicate if the content to get the property from can be unpublished.
     *
     * @return Content The content.
     */
    public function getContent($item = null, $type = null, bool $unpublished = false)
    {
        $item = $item ?? $this->template->getValue('item');

        // Item as a related content (array with item + caption + position)
        if (is_array($item) && array_key_exists('item', $item)) {
            $item = $item['item'];
        }

        if (!is_object($item) && is_numeric($item) && !empty($type)) {
            try {
                $item = $this->entityManager->find($type, $item);
            } catch (GetItemException $e) {
                return null;
            }
        }

        if (!$item instanceof \Common\Model\Entity\Content
            && !$item instanceof \Content
        ) {
            return null;
        }

        if ($unpublished) {
            return $item;
        }

        return $this->isReadyForPublish($item) ? $item : null;
    }

    /**
     * Returns the creation date for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content creation date.
     */
    public function getCreationDate($item = null) : ?\Datetime
    {
        $value = $this->getProperty($item, 'created');

        return is_object($value) ? $value : new \Datetime($value);
    }

    /**
     * Returns the description for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content description.
     */
    public function getDescription($item = null) : ?string
    {
        $value = $this->getProperty($item, 'description');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns the id of an item.
     *
     * @param Content $content The content to get id from.
     *
     * @return int The content id.
     */
    public function getId($item) : ?int
    {
        $item = $this->getContent($item);

        return empty($item) ? null : $item->pk_content;
    }

    /**
     * Returns the pretitle for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content pretitle.
     */
    public function getPretitle($item = null) : ?string
    {
        $value = $this->getProperty($item, 'pretitle');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns a property for the provided item.
     *
     * @param Content $item        The item to get property from.
     * @param string  $name        The property name.
     *
     * @param bool    $unpublished Flag to indicate if the content to get the property from can be unpublished.
     * @return mixed The property value.
     */
    public function getProperty($item, string $name, bool $unpublished = false)
    {
        $item = $this->getContent($item, null, $unpublished);

        if (empty($item)) {
            return null;
        }

        return !empty($item->{$name}) ? $item->{$name} : null;
    }

    /**
     * Returns the publication date for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content publication date.
     */
    public function getPublicationDate($item = null) : ?\Datetime
    {
        $value = $this->getProperty($item, 'starttime') ?? $this->getProperty($item, 'created');

        return is_object($value) ? $value : new \Datetime($value);
    }

    /**
     * Returns the scheduling state.
     *
     * @return string The scheduling state.
     */
    public function getSchedulingState($item)
    {
        if ($this->isScheduled($item)) {
            if ($this->isInTime($item)) {
                return \Content::IN_TIME;
            } elseif ($this->isDued($item)) {
                return \Content::DUED;
            } elseif ($this->isPostponed($item)) {
                return \Content::POSTPONED;
            }
        }

        return \Content::NOT_SCHEDULED;
    }

    /**
     * Returns a list of contents related with a content type and category.
     *
     * @param string $contentTypeName  Content types required.
     * @param string $filter           Advanced SQL filter for contents.
     * @param int    $numberOfElements Number of results.
     *
     * @return array Array with the content properties of each content.
     */
    public function getSuggested($contentTypeName, $categoryId = null, $contentId = null)
    {
        $epp = $this->container->get('core.theme')->getSuggestedEpp();

        if (empty($epp)) {
            return [];
        }

        $cacheId = sprintf('suggested_contents_%s_%d', $contentTypeName, $categoryId);
        $items   = $this->cache->get($cacheId);

        if (!empty($items)) {
            if ($contentId) {
                $items = array_values(array_filter($items, function ($item) use ($contentId) {
                    return $item->pk_content != $contentId;
                }));
            }

            return array_slice($items, 0, $epp);
        }

        $criteria = [
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
            'content_type_name' => [ [ 'value' => $contentTypeName ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        if (!empty($categoryId)) {
            $criteria['category_id'] = [ [ 'value' => $categoryId ] ];
        }

        try {
            $items = $this->entityManager->findBy($criteria, [
                'starttime' => 'desc'
            ], $epp + 1, 1);

            $this->cache->set($cacheId, $items, 900);

            if ($contentId) {
                $items = array_values(array_filter($items, function ($item) use ($contentId) {
                    return $item->pk_content != $contentId;
                }));
            }
        } catch (\Exception $e) {
            return [];
        }

        return array_slice($items, 0, $epp);
    }

    /**
     * Returns the summary for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content summary.
     */
    public function getSummary($item = null) : ?string
    {
        if (in_array(
            $item->content_type_name,
            [ 'article', 'company', 'obituary', 'opinion', 'video', 'poll', 'event' ]
        )) {
            return $this->getProperty($item, 'description');
        }

        $value = $this->getProperty($item, 'summary');

        //TODO: Recover use of htmlentities when possible
        return !empty($value) ? $value : null;
    }

    /**
     * Returns the title for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content title.
     */
    public function getTitle($item = null) : ?string
    {
        $value = $this->getProperty($item, 'title');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns the list of tags for the provided item.
     *
     * @param Content $item The item to get tags from.
     *
     * @return array The list of tags.
     */
    public function getTags($item = null) : array
    {
        $value = $this->getProperty($item, 'tags');

        if (empty($value)) {
            return [];
        }

        try {
            return $this->tagService->getListByIds($value)['items'];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Returns the internal type or human-readable type for the provided item.
     *
     * @param Content $item        The item to get content type for.
     * @param bool    $readable    True if the instance and item have comments enabled. False
     *                             otherwise.
     * @param bool    $unpublished Flag to indicate if the content to get the property from can be unpublished.
     * @param string The internal or human-readable type.
     */
    public function getType($item = null, bool $readable = false, bool $unpublished = false) : ?string
    {
        $value = $this->getProperty($item, 'content_type_name', $unpublished);

        return !empty($value)
            ? (!$readable ? $value : _(ucfirst(implode(' ', explode('_', $value)))))
            : null;
    }

    /**
     * Check if the content has a body.
     *
     * @param Content $item The item to check body for.
     *
     * @return bool True if the content has a body. False otherwise.
     */
    public function hasBody($item = null) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getBody($item))
            && !$this->subscriptionHelper->isHidden($token, 'body');
    }

    /**
     * Checks if the item has a caption.
     *
     * @param mixed $item The item to get caption from.
     *
     * @return bool True if the item is provided as an array (with the object, the
     *              position in the list of related contents of the same type and
     *              the caption) and the caption is not empty.
     */
    public function hasCaption($item = null) : bool
    {
        return !empty($this->getCaption($item));
    }

    /**
     * Checks if the content has comments enabled or not.
     *
     * @param Content $item The item to get if comments are enabled.
     *
     * @return bool True if enabled, false otherwise.
     */
    public function hasCommentsEnabled($item = null) : bool
    {
        $disableComments = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comment_settings')['disable_comments'];

        return !empty($this->getProperty($item, 'with_comment')) && !(is_null($disableComments) ?
            true :
            $disableComments
        );
    }

    /**
     * Check if the content has a description.
     *
     * @param Content $item The item to check description for.
     *
     * @return bool True if the content has a description. False otherwise.
     */
    public function hasDescription($item) : bool
    {
        return !empty($this->getDescription($item));
    }

    /**
     * Check if the content has a pretitle.
     *
     * @param Content $item The item to check pretitle for.
     *
     * @return bool True if the content has a pretitle. False otherwise.
     */
    public function hasPretitle($item) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getPretitle($item))
            && !$this->subscriptionHelper->isHidden($token, 'pretitle');
    }

    /**
     * Checks if the content has a summary.
     *
     * @param Content $item The item to check summary for.
     *
     * @return bool True if the content has a summary. False otherwise.
     */
    public function hasSummary($item) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getSummary($item))
            && !$this->subscriptionHelper->isHidden($token, 'summary');
    }

    /**
     * Checks if the content has tags.
     *
     * @param Content $item The item to check tags for.
     *
     * @return bool True if the content has tags. False otherwise.
     */
    public function hasTags($item = null) : bool
    {
        return !empty($this->getTags($item));
    }

    /**
     * Checks if the content has a title.
     *
     * @param Content $item The item to check title for.
     *
     * @return bool True if the content has a title. False otherwise.
     */
    public function hasTitle($item) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getTitle($item))
            && !$this->subscriptionHelper->isHidden($token, 'title');
    }

    /**
     * Check if a content is in time for publishing
     *
     * @return boolean
     */
    public function isInTime($item)
    {
        $timezone  = $this->locale->getTimeZone();
        $now       = new \DateTime(null, $timezone);
        $starttime = !$item->starttime instanceof \DateTime ?
            new \DateTime($item->starttime, $timezone) :
            $item->starttime;
        $endtime   = !$item->endtime instanceof \DateTime ?
            new \DateTime($item->endtime, $timezone) :
            $item->endtime;

        $dued = (
            !empty($item->endtime)
            && $now->getTimestamp() > $endtime->getTimestamp()
        );

        $postponed = (
            !empty($item->starttime)
            && $now->getTimestamp() < $starttime->getTimestamp()
        );

        return (!$dued && !$postponed);
    }

    /**
     * Returns true if a match time constraints, is available and is not in trash
     *
     * @return boolean true if is ready
     */
    public function isReadyForPublish($item)
    {
        return ($this->isInTime($item)
            && $item->content_status == 1
            && $item->in_litter == 0);
    }

    /**
     * Returns true if the content is suggested
     *
     * @return boolean true if the content is suggested
     */
    public function isSuggested($item)
    {
        return ($item->frontpage == 1);
    }

    /**
     * Check if this content is dued
     *       End      Now
     * -------]--------|-----------
     *
     * @return bool
     */
    protected function isDued($item)
    {
        if (empty($item->endtime)) {
            return false;
        }

        $timezone = $this->locale->getTimeZone();

        $end = !$item->endtime instanceof \DateTime ?
            new \DateTime($item->endtime, $timezone) :
            $item->endtime;
        $now = new \DateTime(null, $timezone);

        return $now->getTimeStamp() > $end->getTimeStamp();
    }

    /**
     * Check if this content is postponed
     *
     *       Now     Start
     * -------|--------[-----------
     *
     * @return bool
     */
    protected function isPostponed($item)
    {
        if (empty($item->starttime)) {
            return false;
        }

        $timezone = $this->locale->getTimeZone();

        $start = !$item->starttime instanceof \DateTime ?
            new \DateTime($item->starttime, $timezone) :
            $item->starttime;
        $now   = new \DateTime(null, $timezone);

        return $now->getTimeStamp() < $start->getTimeStamp();
    }

    /**
     * Check if this content is scheduled or, in others words, if this
     * content has a starttime and/or endtime defined.
     *
     * @return bool
     */
    protected function isScheduled($item)
    {
        if (empty($item->starttime)) {
            return false;
        }

        $start = !$item->starttime instanceof \Datetime ?
            new \DateTime($item->starttime) :
            $item->starttime;
        $end   = !$item->endtime instanceof \DateTime ?
            new \DateTime($item->endtime) :
            $item->endtime;

        if ($start->getTimeStamp() - $end->getTimeStamp() == 0) {
            return false;
        }

        return true;
    }

     /**
     * Check if this content have live blog flag enabled
     *
     * @return bool
     */
    public function isLiveBlog($item)
    {
        return !empty($item->live_blog_posting) &&
            !empty($item->coverage_start_time) &&
            !empty($item->coverage_end_time) &&
            !empty($item->live_blog_updates);
    }

     /**
     * Check if this content have live blog updates
     *
     * @return bool
     */
    public function hasLiveUpdates($item)
    {
        return !empty($item->live_blog_updates);
    }

     /**
     * Return last live update date if live blog article
     *
     * @return string
     */
    public function getLastLiveUpdate($item)
    {
        if (!$this->isLiveBlog($item) || empty($item->live_blog_updates)) {
            return null;
        }

        return $item->live_blog_updates[0]['modified'];
    }

    /**
     * Check if this content is live or, in others words, if this
     * content is between coverage start time and end time
     *
     * @return bool
     */
    public function isLive($item)
    {
        if (empty($item->coverage_start_time || $item->coverage_end_time)) {
            return false;
        }

        $timezone  = $this->locale->getTimeZone();
        $startTime = (gettype($item->coverage_start_time) == 'object') ?
            $item->coverage_start_time :
            new \DateTime($item->coverage_start_time);

        $endTime = (gettype($item->coverage_end_time) == 'object') ?
            $item->coverage_end_time :
            new \DateTime($item->coverage_end_time);

        $now = new \DateTime(null, $timezone);

        return $now->getTimeStamp() >= $startTime->getTimeStamp() && $now->getTimeStamp() <= $endTime->getTimeStamp();
    }

    /**
     * Matches the given event type with the event types list.
     *
     * This method checks if the provided event type exists in the list of available event types.
     * It first looks for a matching slug in the pre-defined list, and if not found, it queries an external service.
     *
     * @param string $type The event type slug to match.
     *
     * @return bool True if the event type exists, false otherwise.
     */
    public function matchEventType(string $type): bool
    {
        if ($this->getEventTypeNameBySlug($type)) {
            return true;
        }

        $oql = sprintf('event_type = "%s"', $type);

        try {
            $event = $this->service->getList($oql);
            return !empty($event['items']);
        } catch (GetListException $e) {
            return false;
        }
    }
}
