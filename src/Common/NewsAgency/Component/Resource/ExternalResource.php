<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Resource;

/**
 * Represents a content ready to be imported.
 */
class ExternalResource
{
    /**
     * The agency name.
     *
     * @var string
     */
    public $agency_name = '';

    /**
     * The author name.
     *
     * @var string
     */
    public $author = null;

    /**
     * The resource body.
     *
     * @var string
     */
    public $body = '';

    /**
     * The canonical url
     *
     * @var string
     */
    public $canonicalurl = '';

    /**
     * The category name.
     *
     * @var string
     */
    public $category = '';

    /**
     * The created time.
     *
     * @var \Datetime
     */
    public $created_time = null;

    /**
     * The resource id.
     *
     * @var string
     */
    public $id = '';

    /**
     * If the resource has parent.
     *
     * @var boolean
     */
    public $isChild = false;

    /**
     * The file name for media resources.
     *
     * @var string
     */
    public $file_name = null;

    /**
     * The resource pretitle.
     *
     * @var string
     */
    public $pretitle = '';

    /**
     * The resource priority.
     *
     * @var integer
     */
    public $priority = 1;

    /**
     * List of related contents ids.
     *
     * @var array
     */
    public $related = [];

    /**
     * The resource signature.
     */
    public $signature = null;

    /**
     * The source id.
     */
    public $source = null;

    /**
     * The resource summary.
     *
     * @var string
     */
    public $summary = '';

    /**
     * The resource external href.
     *
     * @var string
     */
    public $href = '';

    /**
     * The resource tags.
     *
     * @var string
     */
    public $tags = '';

    /**
     * The resource title.
     *
     * @var string
     */
    public $title = '';

    /**
     * The resource type.
     *
     * @var string
     */
    public $type = 'text';

    /**
     * The resource uid.
     *
     * @var string
     */
    public $uid = '';

    /**
     * The resource URN.
     *
     * @var string
     */
    public $urn = 'urn:nitf:::';

    /**
     * The event resource start date.
     *
     * @var string
     */
    public $event_start_date;

    /**
     * The event resource start hour.
     *
     * @var string
     */
    public $event_start_hour;

    /**
     * The event resource website.
     *
     * @var string
     */
    public $event_website;

    /**
     * The event resource information.
     *
     * @var string
     */
    public $event_info;

    /**
     * The event resource place.
     *
     * @var string
     */
    public $event_place;

    /**
     * The event resource address.
     *
     * @var string
     */
    public $event_address;

    /**
     * The event resource latitude.
     *
     * @var string
     */
    public $event_map_latitude;

    /**
     * The event resource longitude.
     *
     * @var string
     */
    public $event_map_longitude;

    /**
     * The event resource ticket price.
     *
     * @var string
     */
    public $event_tickets_price;

    /**
     * The event resource ticket link.
     *
     * @var string
     */
    public $event_tickets_link;

    /**
     * The event resource type.
     *
     * @var string
     */
    public $event_type;

    /**
     * The event resource organizer name.
     *
     * @var string
     */
    public $event_organizer_name;

    /**
     * The event resource organizer url.
     *
     * @var string
     */
    public $event_organizer_url;

    /**
     * Initializes a Resource.
     *
     * @param array $data The resource data.
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Updates values from data if they are empty.
     *
     * @param array $data The data.
     */
    public function merge($data)
    {
        foreach ($this as $key => $value) {
            if (empty($this->{$key}) && array_key_exists($key, $data)) {
                $this->{$key} = $data[$key];
            }
        }
    }
}
