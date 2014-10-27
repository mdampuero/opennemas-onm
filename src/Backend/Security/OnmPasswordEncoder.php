<?php

namespace Backend\Security;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class OnmPasswordEncoder implements PasswordEncoderInterface
{
    /**
     * Encodes a password.
     *
     * @param  string $raw  Password to encode.
     * @param  string $salt String used while encoding.
     * @return string       Encoded password.
     */
    public function encodePassword($raw, $salt)
    {
        $salt = 'md5:';
        if (strpos($raw, $salt) === false) {
            return md5($raw);
        } else {
            return substr($raw, strpos($raw, $salt) + 4);
        }
    }

    /**
     * Checks if the raw password is equal to the encoded password.
     *
     * @param  string $encoded Encoded password.
     * @param  string $raw     Password to encode.
     * @param  string $salt    String used while encoding.
     * @return string          True if $encoded is equal to $raw after encoding.
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        if ($encoded === $this->encodePassword($raw, $salt)) {
            return true;
        }

        return false;
    }
}
