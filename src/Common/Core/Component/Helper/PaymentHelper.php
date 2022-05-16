<?php

namespace Common\Core\Component\Helper;

use Symfony\Component\DependencyInjection\Container;

/**
* Perform auxiliary actions for Payments Controller.
*/
class PaymentHelper
{
    /**
     * Initializes the payment helper.
     *
     * @param Container The service dependency injector.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the referer url with the response message as a query string.
     *
     * @param string $url     The referer url.
     * @param int    $code    The code of the response.
     *
     * @return string The referer url with the response message added as a query string.
     */
    public function getRefererUrlWithMessage(string $url, string $code)
    {
        $messages = [
            '00'      => _('The transaction was successful'),
            '101'     => _('The transaction was declined by the bank'),
            '501'     => _('This transaction was already processed'),
            '110'     => _('There are some errors in the form'),
            'default' => _('There was an issue with the transaction')
        ];

        $message = $messages[$code] ?? $messages['default'];
        $code    = $code ?? 'error';

        $urlHelper = $this->container->get('core.helper.url');

        $parts = $urlHelper->parse($url);

        $parts['query'] = sprintf('message=%s&code=%s', $message, $code);

        $url = $urlHelper->unparse($parts);

        return $url;
    }
}
