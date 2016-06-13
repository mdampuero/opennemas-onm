<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser\NewsML\NewsMLComponent;

use Framework\Import\Parser\NewsML\NewsML;
use Framework\Import\Resource\Resource;

/**
 * Parses NewsComponent of text type from NewsML files.
 */
class NewsMLComponentText extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        $node = $data->xpath('/NewsComponent');

        if (!is_array($node) || count($node) == 0) {
            return false;
        }

        $node = $data->xpath('/NewsComponent/ContentItem/MediaType[@FormalName="Text"]');

        if (empty($node)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody($data)
    {
        $bodies = $data->xpath('//ContentItem');

        $body = '';
        if (is_array($bodies)
            && !empty($bodies)
            && !empty($bodies[0]->DataContent)
            && !empty($bodies[0]->DataContent->p)
        ) {
            foreach ($bodies[0]->DataContent->p as $p) {
                $body .= "<p>$p</p>";
            }
        }

        return $body;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $resource = new Resource();

        $resource->body = $this->getBody($data);
        $resource->urn  = $this->getUrn($data);
        $resource->type = 'text';

        return $resource;
    }
}
