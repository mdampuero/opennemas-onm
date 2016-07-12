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

    /**
     * Returns a simplified version of the extension data with the valid values
     * basing on the current language.
     *
     * @return array The extension data.
     */
    public function toArray()
    {
        $data = $this->getData();

        $data['name'] = $data['name']['en'];
        if (array_key_exists(CURRENT_LANGUAGE_SHORT, $this->name)
            && !empty($this->name[CURRENT_LANGUAGE_SHORT])
        ) {
            $data['name'] = $this->name[CURRENT_LANGUAGE_SHORT];
        }

        $data['description'] = $data['description']['en'];
        if (array_key_exists(CURRENT_LANGUAGE_SHORT, $this->description)
            && !empty($this->description[CURRENT_LANGUAGE_SHORT])
        ) {
            $data['description'] = $this->description[CURRENT_LANGUAGE_SHORT];
        }

        $data['about'] = $data['about']['en'];
        if (array_key_exists(CURRENT_LANGUAGE_SHORT, $this->about)
            && !empty($this->about[CURRENT_LANGUAGE_SHORT])
        ) {
            $data['about'] = $this->about[CURRENT_LANGUAGE_SHORT];
        }

        return $data;
    }
}
