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
class NitfOpennemas extends Nitf
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (parent::checkFormat($data)
            && strpos($this->getAgencyName($data), 'Opennemas')  !== false
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAgencyName($data)
    {
        $agency = $data->xpath('//distributor');

        if (!empty($agency)) {
            return (string) $agency[0];
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $resource = parent::parse($data);

        $resource->author = $this->getAuthor($data);

        return $resource;
    }

    /**
     * Returns the author data for the current resource.
     *
     * @param SimpleXMLObject $data The data to parse.
     *
     * @return array The author data.
     */
    protected function getAuthor($data)
    {
        $author = $data->xpath('//rights/rights.owner');

        if (!empty($author)) {
            $author = json_decode((string) $author[0]);

            $author->photo = $this->getAuthorPhoto($data);

            return $author;
        }

        return null;
    }

    /**
     * Returns the author photo URL.
     *
     * @param SimpleXMLObject $data The data to parse.
     *
     * @return string The author photo URL.
     */
    protected function getAuthorPhoto($data)
    {
        $photo = $data->xpath('//rights/rights.photo');

        if (!empty($photo)) {
            return (string) $photo[0];
        }

        return null;
    }
}
