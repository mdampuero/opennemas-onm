<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class AuthenticationSuccessListener
{
    /**
     * Add user's information to the request on authentication success.
     *
     * @param AuthenticationSuccessEvent $event The event object.
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof AdvancedUserInterface) {
            return;
        }

        // $data['token'] contains the JWT

        $data['user'] = array(
            'id'            => $user->id,
            'avatar_img_id' => $user->avatar_img_id,
            'email'         => $user->email,
            'name'          => $user->name,
            'username'      => $user->username,
        );

        $event->setData($data);
    }
}
