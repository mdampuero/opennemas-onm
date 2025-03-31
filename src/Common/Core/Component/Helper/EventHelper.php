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
 * Performs operations in Database related with one or more events.
 */
class EventHelper
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * The content service
     *
     * @var ContentService
     */
    protected $service;

    /**
     * Constructor.
     *
     * @param Container $container The container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->service   = $this->container->get('api.service.content');
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
     * Retrieves events grouped by their parent type.
     *
     * This function fetches all event types, organizes them into categories,
     * and returns a flattened list where each event is associated with its group.
     *
     * @return array An array of events with their respective categories.
     */
    public function getEventsGroupedByType()
    {
        $events        = $this->getEventTypes();
        $groupedEvents = [];
        $eventById     = [];

        // Index events by their ID for faster lookup
        foreach ($events as $event) {
            $eventById[$event['id']] = $event;
        }

        foreach ($events as $event) {
            // If the event is a category, create a group but don't add it as an event
            if ($event['category']) {
                $groupedEvents[$event['slug']] = [
                    'name'     => $event['name'],
                    'slug'     => $event['slug'],
                    'group'    => $event['name'],
                    'children' => [],
                ];
                continue;
            }

            // Assign event to its parent category if it exists
            $parentId = $event['parent'];

            if (isset($eventById[$parentId])) {
                $parent = $eventById[$parentId];

                $groupedEvents[$parent['slug']]['children'][] = [
                    'name'  => $event['name'],
                    'slug'  => $event['slug'],
                    'group' => $parent['name'],
                ];
            }
        }

        // Flatten grouped events into a final result array
        $result = [];

        foreach ($groupedEvents as $group) {
            foreach ($group['children'] as $child) {
                $result[] = $child;
            }
        }

        return $result;
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
     * Matches the given event type with the event types list.
     *
     * This method checks if the provided event type exists in the list of available event types.
     * It first looks for a matching slug in the pre-defined list, and if not found, it queries an external service.
     *
     * @param string $type The event type slug to match.
     *
     * @return bool True if the event type exists, false otherwise.
     */
    public function matchType(string $type): bool
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
