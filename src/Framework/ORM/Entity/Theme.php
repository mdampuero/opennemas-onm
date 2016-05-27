<?php

namespace Framework\ORM\Entity;

class Theme extends Entity
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
        if (empty($this->price)) {
            return 0;
        }

        $prices = array_filter($this->price, function ($a) use ($type) {
            return $a['type'] === $type;
        });

        if (count($prices)) {
            return $prices[0]['value'];
        }

        return $this->price[0]['value'];
    }
}
