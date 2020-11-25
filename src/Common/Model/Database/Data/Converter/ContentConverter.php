<?php

namespace Common\Model\Database\Data\Converter;

use Opennemas\Orm\Database\Data\Converter\BaseConverter;

class ContentConverter extends BaseConverter
{
    /**
     * Databasifies related contents before saving it to database.
     *
     * @param array $related The list of related contents
     *
     * @return array The list of related contents ready to be saved.
     */
    public function databasifyRelated($related)
    {
        if (empty($related)) {
            return [];
        }

        $properties = [
            'type'     => [ 'from' => 'string', 'to' => 'string' ],
            'caption'  => [ 'from' => 'l10n_string', 'to' => 'string' ],
            'position' => [ 'from' => 'integer', 'to' => 'integer' ]
        ];

        foreach ($related as &$r) {
            foreach ($r as $key => $value) {
                if (!array_key_exists($key, $properties)) {
                    continue;
                }

                $from = $properties[$key]['from'];
                $to   = $properties[$key]['to'];

                $r[$key] = $this->convertTo($from, $to, $value);
            }
        }

        return $related;
    }

    /**
     * {@inheritdoc}
     */
    protected function sObjectifyStrict($source)
    {
        $data = parent::sObjectifyStrict($source);

        if (!array_key_exists('related_contents', $data)
            || empty($data['related_contents'])
        ) {
            return $data;
        }

        $properties = [
            'type'     => [ 'from' => 'string', 'to' => 'string' ],
            'caption'  => [ 'from' => 'string', 'to' => 'l10n_string' ],
            'position' => [ 'from' => 'integer', 'to' => 'integer' ]
        ];

        foreach ($data['related_contents'] as &$related) {
            foreach ($related as $key => $value) {
                if (!array_key_exists($key, $properties)) {
                    continue;
                }

                $from = $properties[$key]['from'];
                $to   = $properties[$key]['to'];

                $related[$key] = $this->convertFrom($to, $from, $value);
            }
        }

        return $data;
    }
}
