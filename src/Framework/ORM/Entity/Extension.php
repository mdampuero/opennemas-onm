<?php

namespace Framework\ORM\Entity;

class Extension extends Entity
{
    /**
     * Returns the extension price.
     *
     * @param string $type The price type.
     *
     * @return float The extension price.
     */
    public function getPrice($type = 'monthly')
    {
        if (!array_key_exists('price', $this->metas)
            || empty($this->metas['price'])
        ) {
            return 0;
        }

        $prices = array_filter($this->metas['price'], function ($a) use ($type) {
            return $a['type'] === $type;
        });

        if (count($prices)) {
            return $prices[0]['value'];
        }

        return $this->metas['price'][0]['value'];
    }
}
