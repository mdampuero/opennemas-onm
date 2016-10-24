<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser\Nitf;

/**
 * Parses XML files in custom NITF format for EFE.
 */
class NitfEfe extends Nitf
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
            return 5;
        }

        $priority = (string) $priority[0]->attributes()->content;

        if (array_key_exists($priority, $this->priorities)) {
            return $this->priorities[$priority];
        }

        if ($priority > 5) {
            return 5;
        }

        return $priority;
    }
}
