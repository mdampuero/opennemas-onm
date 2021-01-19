<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

class ActOnContactSubscriber implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The Act-on factory.
     *
     * @var ActOnFactory
     */
    protected $actOnFactory;

    /**
     * The logger service.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Initializes the ActOnSubscriber
     *
     * @param Container       $container    The service container.
     * @param ActOnFactory    $actOnFactory The Act-on factory.
     * @param LoggerInterface $logger       The logger service.
     */
    public function __construct($container, $actOnFactory, $logger)
    {
        $this->actOnFactory = $actOnFactory;
        $this->container    = $container;
        $this->logger       = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            'comment.create' => [ [ 'addContact', 5 ] ],
            'comment.update' => [ [ 'addContact', 5 ] ]
        ];
    }

    /**
     * Adds a contact to an Act-on list.
     *
     * @param Event $event The event.
     */
    public function addContact(Event $event)
    {
        if (!$event->hasArgument('item')
            || !in_array(
                'es.openhost.module.acton',
                $this->container->get('core.instance')->activated_modules
            )
        ) {
            return;
        }

        $commentsConfig = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comments_config');

        $listId  = $commentsConfig['acton_list'] ?? null;
        $comment = $event->getArgument('item');

        if (empty($listId) || $comment->status !== 'accepted') {
            return;
        }

        try {
            $endpoint = $this->actOnFactory->getEndpoint('contact');

            if ($endpoint->existContact($listId, $comment->author_email)) {
                return;
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Error checking for Act-on contact: ' . $e->getMessage()
            );

            return;
        }

        $sendingParams = [
            'Correo electrónico' => $comment->author_email,
            'Nombre Yomi'        => $comment->author,
            'Boletin ECD'        => 'Y',
            'Tema'               => 'Realizado Comentario ECD'
        ];

        try {
            $endpoint->addContact(
                $listId,
                [ 'contact' => json_encode($sendingParams) ]
            );
        } catch (\Exception $e) {
            $this->logger->error(
                'Error creating Act-on contact: ' . $e->getMessage()
            );
        }
    }
}
