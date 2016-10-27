<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * The PasswordEncoder class provides methods to encode users passwords.
 */
class PasswordEncoder implements PasswordEncoderInterface
{
    /**
     * Encodes a password.
     *
     * @param  string $raw  Password to encode.
     * @param  string $salt String used while encoding.
     *
     * @return string Encoded password.
     */
    public function encodePassword($raw, $salt)
    {
        $salt = 'md5:';

        if (strpos($raw, $salt) === false) {
            return md5($raw);
        }

        return substr($raw, strpos($raw, $salt) + 4);
    }

    /**
     * Checks if the raw password is equal to the encoded password.
     *
     * @param  string $encoded Encoded password.
     * @param  string $raw     Password to encode.
     * @param  string $salt    String used while encoding.
     *
     * @return string True if $encoded is equal to $raw after encoding.
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        if ($encoded === $this->encodePassword($raw, $salt)) {
            return true;
        }

        return false;
    }
}
