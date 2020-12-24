<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer\Statistics;

use Common\Model\Entity\Newsletter;
use Frontend\Renderer\StatisticsRenderer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PiwikRenderer extends StatisticsRenderer
{
    /**
     * {@inheritdoc}
     */
    public function __construct($container)
    {
        parent::__construct($container);

        $this->config = $this->global->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('piwik');

        $this->piwikConfig          = $this->global->getContainer()->getParameter('opennemas.piwik');
        $this->config['server_url'] = $this->piwikConfig['url'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        $httpsHost = preg_replace("/http:/", "https:", $this->config['server_url']);
        $ampHost   = preg_replace("/^https?:/", "", $this->config['server_url']);

        $params = [
            'content'   => $content,
            'config'    => $this->config,
            'httpsHost' => $httpsHost,
            'ampHost'   => $ampHost
        ];

        if (!empty($content) && $content instanceof Newsletter) {
            $url = $this->container->get('router')->generate(
                'frontend_newsletter_show',
                [ 'id' => $content->id, ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $params['newsurl'] = urlencode($url);
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        if (!is_array($this->config)
            || !array_key_exists('page_id', $this->config)
            || !array_key_exists('server_url', $this->config)
            || empty(trim($this->config['page_id']))
        ) {
            return false;
        }

        return true;
    }
}
