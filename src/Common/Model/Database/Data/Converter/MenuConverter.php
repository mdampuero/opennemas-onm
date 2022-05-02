<?php

namespace Common\Model\Database\Data\Converter;

use Opennemas\Orm\Database\Data\Converter\BaseConverter;

class MenuConverter extends BaseConverter
{
    /**
     * Databasifies related contents before saving it to database.
     *
     * @param array $related The list of related contents
     *
     * @return array The list of related contents ready to be saved.
     */
    public function databasifyMenuItems($menuItems)
    {
        if (empty($menuItems)) {
            return [];
        }

        $properties = [
            'title'     => [ 'from' => 'l10n_string', 'to' => 'string' ],
            'link_name'  => [ 'from' => 'l10n_string', 'to' => 'string' ]
        ];

        foreach ($menuItems as &$r) {
            foreach ($r as $key => $value) {
                if (!array_key_exists($key, $properties)) {
                    continue;
                }

                $from = $properties[$key]['from'];
                $to   = $properties[$key]['to'];

                $r[$key] = $this->convertTo($from, $to, $value);
            }
        }

        return $menuItems;
    }

    /**
     * {@inheritdoc}
     */
    protected function sObjectifyStrict($source)
    {
        $data = parent::sObjectifyStrict($source);
        if (!array_key_exists('menu_items', $data)) {
            return $data;
        }

        $properties = [
            'title'     => [ 'from' => 'string', 'to' => 'l10n_string' ],
            'link_name'  => [ 'from' => 'string', 'to' => 'l10n_string' ]
        ];

        foreach ($data['menu_items'] as &$menuItem) {
            foreach ($menuItem as $key => $value) {
                if (!array_key_exists($key, $properties)) {
                    continue;
                }

                $from = $properties[$key]['from'];
                $to   = $properties[$key]['to'];

                $menuItem[$key] = $this->convertFrom($to, $from, $value);
            }
        }
        return $data;
    }
}
