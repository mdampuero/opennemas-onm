<?php

namespace Common\Data\Serialize\Serializable;

interface CsvSerializable
{
    /**
     * Returns all content information when converted to CSV.
     *
     * @return array The content information.
     */
    public function csvSerialize();
}
