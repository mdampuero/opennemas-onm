<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Parser\NewsML\Efe;

use Common\NewsAgency\Component\Parser\NewsML\NewsML;

/**
 * Parses NewsComponent of text type from NewsML custom format for EFE.
 */
class NewsMLEfe extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!parent::checkFormat($data)) {
            return false;
        }

        if ($this->getAgencyName($data) === 'Agencia EFE') {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAgencyName($data)
    {
        $agency = $data->xpath('//SentFrom/Party/Property');

        if (is_array($agency) && !empty($agency)) {
            return (string) $agency[0]->attributes()->Value;
        }

        return $this->getFromBag('agency_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getTags($data)
    {
        $tags = $data->xpath("//Property[@FormalName=\"Tesauro\"]");

        if (empty($tags)) {
            return $this->getFromBag('tags');
        }

        $tags = (string) $tags[0]->attributes()->Value;

        $groups = explode(";", $tags);
        $tags   = [];
        foreach ($groups as $group) {
            preg_match('@(.*):(.*)@', $group, $matches);

            if (!empty($matches)) {
                $tags = array_merge($tags, explode(',', $matches[2]));
            }
        }

        return implode(',', array_unique($tags));
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['tags'] = $this->getTags($data);

        return parent::parse($data);
    }
}
