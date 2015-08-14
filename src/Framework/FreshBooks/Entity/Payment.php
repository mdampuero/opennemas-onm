<?php

namespace Framework\FreshBooks\Entity;

class Payment extends Entity
{
    /**
     * Array of invalid fields to send in requests
     *
     * @var array
     */
    protected $_invalid = [ 'from_credit', 'updated' ];

    /**
     * Cleans invalid fields from the invoice.
     *
     * @return array The cleaned RAW data array.
     */
    public function clean()
    {
        $cleaned = array_diff_key($this->_data, array_flip($this->_invalid));

        if (array_key_exists('lines', $cleaned)
            && array_key_exists('line', $cleaned['lines'])
        ) {
            foreach ($cleaned['lines']['line'] as &$line) {
                unset($line['order']);
            }
        }

        return $cleaned;
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        $id = $this->payment_id;

        return !empty($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->clean();
    }
}
