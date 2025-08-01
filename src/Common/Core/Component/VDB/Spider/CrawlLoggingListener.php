<?php

namespace Common\Core\Component\VDB\Spider;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class CrawlLoggingListener
{
    /**
     * @var Spider
     */
    private $spider;

    /**
     * @var OutputInterface
     */
    private $output;


    /**
     * The colors for the different status codes.
     */
    const COLORS = [
        '1' => 'blue',
        '2' => 'green',
        '3' => 'yellow',
        '4' => 'red',
        '5' => 'red'
    ];

    /**
     * @param Spider $spider the spider to print logging from.
     */
    public function __construct($spider, $output)
    {
        $this->spider = $spider;
        $this->output = $output;
    }

    /**
     * Prints the result of the request.
     *
     * @param GenericEvent $event The event that trigger the listener.
     */
    public function onCrawlPostRequest()
    {
        $persistenceHandler = $this->spider->getDownloader()->getPersistenceHandler();
        $current            = $persistenceHandler->current();

        if (empty($current)) {
            return;
        }

        $this->output->writeln(
            str_pad(
                $current->getUri()->getPath(),
                200,
                '.'
            ) .
            $this->printStatusCode($current->getResponse()->getStatusCode())
        );
        $persistenceHandler->next();
    }

    /**
     * Print responses in different colors.
     *
     * @param string $statusCode The status code of the response.
     *
     * @return string The output of the status code in different colors based on the code.
     */
    protected function printStatusCode(string $statusCode)
    {
        $codeType = substr($statusCode, 0, 1);
        $color    = self::COLORS[$codeType];

        return sprintf('<fg=%s;options=bold>%s</>', $color, $statusCode);
    }
}
