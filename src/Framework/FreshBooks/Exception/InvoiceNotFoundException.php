<?php

namespace Framework\FreshBooks\Exception;

class InvoiceNotFoundException extends EntityNotFoundException
{
    /**
     * Initializes the exception with a custom message.
     *
     * @param string $id The invoice id.
     */
    public function __construct($id)
    {
        $message = "The invoice with id \"$id\" not found.";

        parent::__construct($message);
    }
}
