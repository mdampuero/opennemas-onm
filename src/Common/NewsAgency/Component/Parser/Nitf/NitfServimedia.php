<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Parser\Nitf;

/**
 * Parses XML files in custom NITF format for Servimedia.
 */
class NitfServimedia extends Nitf
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (parent::checkFormat($data)
            && strpos($this->getAgencyName($data), 'Servimedia') !== false
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory($data)
    {
        $category = $data->xpath('//head/tobject/tobject.subject');

        if (empty($category)) {
            return '';
        }

        return (string) $category[0]->attributes()->{'tobject.subject.type'};
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime($data)
    {
        $date = $data->xpath('//head/pubdata');

        if (empty($date)) {
            return new \DateTime();
        }

        $value = (string) $date[0]->attributes()->{'date.publication'};
        $date  = \DateTime::createFromFormat('Ymd\THisP', $value);

        if (!$date) {
            $date = \DateTime::createFromFormat('Ymd\THis', $value);
        }

        $date->setTimezone(new \DateTimeZone('UTC'));

        return $date;
    }
}
