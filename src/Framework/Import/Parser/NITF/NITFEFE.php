<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser\NITF;

/**
 * Parses XML files in custom NITF format for EFE.
 */
class NITFEFE extends NITF
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (parent::checkFormat($data)
            && strpos($this->getAgencyName($data), 'EFE')  !== false
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns the created time from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return \DateTime The created time.
     */
    public function getCreatedTime($data)
    {
        $date = $data->xpath('//body/body.head/dateline/story.date');

        if (empty($date)) {
            return new \DateTime();
        }

        $date = str_replace('+0000', '', $date[0]->attributes()->norm[0]);
        $date = \DateTime::createFromFormat('Ymd\THis', $date);

        $date->setTimezone(new \DateTimeZone('Europe/Madrid'));

        return $date;
    }

    /**
     * Returns the priority from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return integer The priority level.
     */
    public function getPriority($data)
    {
        $priority = $data->xpath('//head/meta[@name="prioridad"]');

        if (empty($priority)) {
            return 1;
        }

        $priority = (string) $priority[0]->attributes()->content;

        if (array_key_exists($priority, $this->priorities)) {
            return $this->priorities[$priority];
        }

        return $priority;
    }
}
