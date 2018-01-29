<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Exception\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Exception thrown when the current instance is not activated.
 */
class InvalidRecaptchaException extends AuthenticationException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Invalid reCAPTCHA response';
    }
}
