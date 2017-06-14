<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class UrlGeneratorHelper
{
    /**
     * The current client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * Initializes the PurchaseManager.
     *
     * @param ServiceContainer $contaienr The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns a new purchase or the last incompleted purchase.
     *
     * @param integer $id The purchase id.
     *
     * @return Purchase A new purchase or the last incompleted purchase.
     */
    public function get($content = null)
    {
        if (!empty($content)) {
            return '';
        }

        if (array_key_exists('content', $params)
            && is_object($params['content'])
            && $params['content'] instanceof \Content
        ) {
            $content = $params['content'];

            return $content->uri;
        }

        if (isset($params['slug'])) {
            $slug = $params['slug'];
        } elseif (isset($params['title'])) {
            $slug = \Onm\StringUtils::generateSlug($params['title']);
        }

        $output = Uri::generate(
            $params['content_type'],
            [
                'id'       => sprintf('%06d', $params['id']),
                'date'     => date('YmdHis', strtotime($params['date'])),
                'category' => urlencode($params['category_name']),
                'slug'     => urlencode($slug),
            ]
        );

        return $output;
    }
}
