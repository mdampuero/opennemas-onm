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
     * The Settings DataSet.
     *
     * @var DataSet
     */
    protected $ds;

    /**
     * Constructor.
     *
     * @param Container $container The container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->service   = $this->container->get('api.service.content');
        $this->ds        = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance');
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
                'name'     => _('Corporate and Business'),
                'slug'     => _('corporate-and-business'),
                'category' => true,
                'parent'   => null,
            ],
            2 => [
                'id'       => 2,
                'name'     => _('Conferences'),
                'slug'     => _('conferences'),
                'category' => false,
                'parent'   => 1,
            ],
            3 => [
                'id'       => 3,
                'name'     => _('Seminars'),
                'slug'     => _('seminars'),
                'category' => false,
                'parent'   => 1,
            ],
            4 => [
                'id'       => 4,
                'name'     => _('Webinars'),
                'slug'     => _('webinars'),
                'category' => false,
                'parent'   => 1,
            ],
            5 => [
                'id'       => 5,
                'name'     => _('Trade Shows'),
                'slug'     => _('trade-shows'),
                'category' => false,
                'parent'   => 1,
            ],
            6 => [
                'id'       => 6,
                'name'     => _('Press Conferences'),
                'slug'     => _('press-conferences'),
                'category' => false,
                'parent'   => 1,
            ],
            7 => [
                'id'       => 7,
                'name'     => _('Workshops'),
                'slug'     => _('workshops'),
                'category' => false,
                'parent'   => 1,
            ],
            8 => [
                'id'       => 8,
                'name'     => _('Product Launches'),
                'slug'     => _('product-launches'),
                'category' => false,
                'parent'   => 1,
            ],
            9 => [
                'id'       => 9,
                'name'     => _('Networking'),
                'slug'     => _('networking'),
                'category' => false,
                'parent'   => 1,
            ],
            10 => [
                'id'       => 10,
                'name'     => _('Team Building'),
                'slug'     => _('team-building'),
                'category' => false,
                'parent'   => 1,
            ],
            11 => [
                'id'       => 11,
                'name'     => _('Summits'),
                'slug'     => _('summits'),
                'category' => false,
                'parent'   => 1,
            ],
            12 => [
                'id'       => 12,
                'name'     => _('Social'),
                'slug'     => _('social'),
                'category' => true,
                'parent'   => null,
            ],
            13 => [
                'id'       => 13,
                'name'     => _('Weddings'),
                'slug'     => _('weddings'),
                'category' => false,
                'parent'   => 12,
            ],
            14 => [
                'id'       => 14,
                'name'     => _('Baptisms'),
                'slug'     => _('baptisms'),
                'category' => false,
                'parent'   => 12,
            ],
            15 => [
                'id'       => 15,
                'name'     => _('First Communions'),
                'slug'     => _('first-communions'),
                'category' => false,
                'parent'   => 12,
            ],
            16 => [
                'id'       => 16,
                'name'     => _('Anniversaries'),
                'slug'     => _('anniversaries'),
                'category' => false,
                'parent'   => 12,
            ],
            17 => [
                'id'       => 17,
                'name'     => _('Birthdays'),
                'slug'     => _('birthdays'),
                'category' => false,
                'parent'   => 12,
            ],
            18 => [
                'id'       => 18,
                'name'     => _('Farewells'),
                'slug'     => _('farewells'),
                'category' => false,
                'parent'   => 12,
            ],
            19 => [
                'id'       => 19,
                'name'     => _('Baby Showers'),
                'slug'     => _('baby-showers'),
                'category' => false,
                'parent'   => 12,
            ],
            20 => [
                'id'       => 20,
                'name'     => _('Reunions'),
                'slug'     => _('reunions'),
                'category' => false,
                'parent'   => 12,
            ],
            21 => [
                'id'       => 21,
                'name'     => _('Graduations'),
                'slug'     => _('graduations'),
                'category' => false,
                'parent'   => 12,
            ],
            22 => [
                'id'       => 22,
                'name'     => _('Themed Parties'),
                'slug'     => _('themed-parties'),
                'category' => false,
                'parent'   => 12,
            ],
            23 => [
                'id'       => 23,
                'name'     => _('Cultural and Artistic'),
                'slug'     => _('cultural-and-artistic'),
                'category' => true,
                'parent'   => null,
            ],
            24 => [
                'id'       => 24,
                'name'     => _('Concerts'),
                'slug'     => _('concerts'),
                'category' => false,
                'parent'   => 23,
            ],
            25 => [
                'id'       => 25,
                'name'     => _('Theater'),
                'slug'     => _('theater'),
                'category' => false,
                'parent'   => 23,
            ],
            26 => [
                'id'       => 25,
                'name'     => _('Museum'),
                'slug'     => _('museum'),
                'category' => false,
                'parent'   => 23,
            ],
            27 => [
                'id'       => 26,
                'name'     => _('Exhibitions'),
                'slug'     => _('exhibitions'),
                'category' => false,
                'parent'   => 23,
            ],
            28 => [
                'id'       => 27,
                'name'     => _('Performances'),
                'slug'     => _('performances'),
                'category' => false,
                'parent'   => 23,
            ],
            29 => [
                'id'       => 28,
                'name'     => _('Festivals'),
                'slug'     => _('festivals'),
                'category' => false,
                'parent'   => 23,
            ],
            30 => [
                'id'       => 29,
                'name'     => _('Cinema'),
                'slug'     => _('cinema'),
                'category' => false,
                'parent'   => 23,
            ],
            31 => [
                'id'       => 30,
                'name'     => _('Book Fairs'),
                'slug'     => _('book-fairs'),
                'category' => false,
                'parent'   => 23,
            ],
            32 => [
                'id'       => 31,
                'name'     => _('Urban Art'),
                'slug'     => _('urban-art'),
                'category' => false,
                'parent'   => 23,
            ],
            33 => [
                'id'       => 32,
                'name'     => _('Sports'),
                'slug'     => _('sports'),
                'category' => true,
                'parent'   => null,
            ],
            34 => [
                'id'       => 33,
                'name'     => _('Sports'),
                'slug'     => _('sports'),
                'category' => false,
                'parent'   => 32,
            ],
            35 => [
                'id'       => 34,
                'name'     => _('Races'),
                'slug'     => _('races'),
                'category' => false,
                'parent'   => 32,
            ],
            36 => [
                'id'       => 35,
                'name'     => _('Tournaments'),
                'slug'     => _('tournaments'),
                'category' => false,
                'parent'   => 32,
            ],
            37 => [
                'id'       => 36,
                'name'     => _('Exhibitions'),
                'slug'     => _('sports-exhibitions'),
                'category' => false,
                'parent'   => 32,
            ],
            38 => [
                'id'       => 37,
                'name'     => _('Classes'),
                'slug'     => _('sports-classes'),
                'category' => false,
                'parent'   => 32,
            ],
            39 => [
                'id'       => 38,
                'name'     => _('Esports'),
                'slug'     => _('esports'),
                'category' => false,
                'parent'   => 32,
            ],
            40 => [
                'id'       => 39,
                'name'     => _('Triathlons'),
                'slug'     => _('triathlons'),
                'category' => false,
                'parent'   => 32,
            ],
            41 => [
                'id'       => 40,
                'name'     => _('Extreme Sports'),
                'slug'     => _('extreme-sports'),
                'category' => false,
                'parent'   => 32,
            ],
            42 => [
                'id'       => 41,
                'name'     => _('Educational'),
                'slug'     => _('educational'),
                'category' => true,
                'parent'   => null,
            ],
            43 => [
                'id'       => 42,
                'name'     => _('Courses'),
                'slug'     => _('courses'),
                'category' => false,
                'parent'   => 41,
            ],
            44 => [
                'id'       => 43,
                'name'     => _('Conferences'),
                'slug'     => _('educational-conferences'),
                'category' => false,
                'parent'   => 41,
            ],
            45 => [
                'id'       => 44,
                'name'     => _('Symposiums'),
                'slug'     => _('symposiums'),
                'category' => false,
                'parent'   => 41,
            ],
            46 => [
                'id'       => 45,
                'name'     => _('Debates'),
                'slug'     => _('debates'),
                'category' => false,
                'parent'   => 41,
            ],
            47 => [
                'id'       => 46,
                'name'     => _('Workshops'),
                'slug'     => _('educational-workshops'),
                'category' => false,
                'parent'   => 41,
            ],
            48 => [
                'id'       => 47,
                'name'     => _('Fairs'),
                'slug'     => _('educational-fairs'),
                'category' => false,
                'parent'   => 41,
            ],
            49 => [
                'id'       => 48,
                'name'     => _('Religious'),
                'slug'     => _('religious'),
                'category' => true,
                'parent'   => null,
            ],
            50 => [
                'id'       => 49,
                'name'     => _('Masses'),
                'slug'     => _('masses'),
                'category' => false,
                'parent'   => 48,
            ],
            51 => [
                'id'       => 50,
                'name'     => _('Pilgrimages'),
                'slug'     => _('pilgrimages'),
                'category' => false,
                'parent'   => 48,
            ],
            52 => [
                'id'       => 51,
                'name'     => _('Retreats'),
                'slug'     => _('retreats'),
                'category' => false,
                'parent'   => 48,
            ],
            53 => [
                'id'       => 52,
                'name'     => _('Festivities'),
                'slug'     => _('festivities'),
                'category' => false,
                'parent'   => 48,
            ],
            54 => [
                'id'       => 53,
                'name'     => _('Charitable'),
                'slug'     => _('charitable'),
                'category' => true,
                'parent'   => null,
            ],
            55 => [
                'id'       => 54,
                'name'     => _('Gala Events'),
                'slug'     => _('gala-events'),
                'category' => false,
                'parent'   => 53,
            ],
            56 => [
                'id'       => 55,
                'name'     => _('Auctions'),
                'slug'     => _('auctions'),
                'category' => false,
                'parent'   => 53,
            ],
            57 => [
                'id'       => 56,
                'name'     => _('Donations'),
                'slug'     => _('donations'),
                'category' => false,
                'parent'   => 53,
            ],
            58 => [
                'id'       => 57,
                'name'     => _('Volunteering'),
                'slug'     => _('volunteering'),
                'category' => false,
                'parent'   => 53,
            ],
            59 => [
                'id'       => 58,
                'name'     => _('Benefit Concerts'),
                'slug'     => _('benefit-concerts'),
                'category' => false,
                'parent'   => 53,
            ],
            60 => [
                'id'       => 59,
                'name'     => _('Gastronomic'),
                'slug'     => _('gastronomic'),
                'category' => true,
                'parent'   => null,
            ],
            61 => [
                'id'       => 60,
                'name'     => _('Food Fairs'),
                'slug'     => _('food-fairs'),
                'category' => false,
                'parent'   => 59,
            ],
            62 => [
                'id'       => 61,
                'name'     => _('Tastings'),
                'slug'     => _('tastings'),
                'category' => false,
                'parent'   => 59,
            ],
            63 => [
                'id'       => 62,
                'name'     => _('Gastronomic Festivals'),
                'slug'     => _('gastronomic-festivals'),
                'category' => false,
                'parent'   => 59,
            ],
            64 => [
                'id'       => 63,
                'name'     => _('Technological'),
                'slug'     => _('technological'),
                'category' => true,
                'parent'   => null,
            ],
            65 => [
                'id'       => 64,
                'name'     => _('Tech Fairs'),
                'slug'     => _('tech-fairs'),
                'category' => false,
                'parent'   => 63,
            ],
            66 => [
                'id'       => 65,
                'name'     => _('Demos'),
                'slug'     => _('demos'),
                'category' => false,
                'parent'   => 63,
            ],
            67 => [
                'id'       => 66,
                'name'     => _('Hackathons'),
                'slug'     => _('hackathons'),
                'category' => false,
                'parent'   => 63,
            ],
            68 => [
                'id'       => 67,
                'name'     => _('Governmental'),
                'slug'     => _('governmental'),
                'category' => true,
                'parent'   => null,
            ],
            69 => [
                'id'       => 68,
                'name'     => _('Inaugurations'),
                'slug'     => _('inaugurations'),
                'category' => false,
                'parent'   => 67,
            ],
            70 => [
                'id'       => 69,
                'name'     => _('Summits'),
                'slug'     => _('governmental-summits'),
                'category' => false,
                'parent'   => 67,
            ],
            71 => [
                'id'       => 70,
                'name'     => _('Debates'),
                'slug'     => _('governmental-debates'),
                'category' => false,
                'parent'   => 67,
            ],
            72 => [
                'id'       => 71,
                'name'     => _('Other'),
                'slug'     => _('other'),
                'category' => false,
                'parent'   => 67,
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
            // If the event is parent, create a group but don't add it as an event
            if ($event['category']) {
                $groupedEvents[$event['slug']] = [
                    'name'     => $event['name'],
                    'slug'     => $event['slug'],
                    'group'    => $event['name'],
                    'children' => [],
                ];
                continue;
            }

            // Assign event to its parent if it exists
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

        foreach ($eventTypes as $eventType) {
            if ($eventType['slug'] === $slug) {
                return $eventType['name'];
            }
        }

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

        return false;
    }
}
