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
 * Parses XML files in custom NITF format for Taquilla.
 */
class NitfTaquilla extends Nitf
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (parent::checkFormat($data)
            && strpos($this->getAgencyName($data), 'Taquilla.com') !== false
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns the unique urn from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The URN.
     */
    public function getUrn($data)
    {
        $classname = get_class($this);
        $classname = substr($classname, strrpos($classname, '\\') + 1);

        $resource = strtolower($classname);
        $agency   = str_replace(
            ' ',
            '_',
            strtolower($this->getAgencyName($data))
        );

        $id = $this->getId($data);

        return "urn:$resource:$agency:text:$id";
    }
}
