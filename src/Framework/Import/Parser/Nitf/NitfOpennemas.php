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
            && ($this->getAgencyName($data) === 'Opennemas')
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
        $agency = $data->xpath('//head/docdata/doc.rights');

        if (!empty($agency)) {
            return (string) $agency[0]->attributes()->{'provider'};
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTags($data)
    {
        $tags = $data->xpath('//head/docdata/key-list/keyword');

        if (!empty($tags)) {
            return (string) $tags[0]->attributes()->{'key'};
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $resource = parent::parse($data);

        $author = $this->getAuthor($data);
        if (is_object($author)) {
            $resource->author = $author;
        }

        return $resource;
    }

    /**
     * Returns the body from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The body.
     */
    public function getBody($data)
    {
        $bodies = $data->xpath('//body/body.content');

        if (empty($bodies)) {
            return '';
        }

        $body = '';
        if (!empty((string) $bodies[0])) {
            $body = preg_replace(
                ["/\n/", '/<p>\s*<\/p>/'],
                ['', ''],
                html_entity_decode((string) $bodies[0])
            );
        }

        return iconv(mb_detect_encoding($body), "UTF-8", $body);
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

            if (!$author) {
                return null;
            }

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
        $photo = $data->xpath('//rights/rights.owner.photo');

        if (!empty($photo)) {
            return (string) $photo[0];
        }

        return null;
    }
}
