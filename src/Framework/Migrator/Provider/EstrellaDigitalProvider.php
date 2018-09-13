<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Migrator\Provider;

class EstrellaDigitalProvider extends JsonProvider
{

    /**
     * Constructs a new Migration provider.
     *
     * @param Logger $logger
     * @param array  $settings
     * @param array  $translations Array of translations.
     * @param array  $stats
     * @param array  $debug
     */
    public function __construct(
        $logger,
        $settings,
        &$translations,
        &$stats,
        $output,
        $debug = false
    ) {
        parent::__construct(
            $logger,
            $settings,
            $translations,
            $stats,
            $output,
            $debug
        );

        $this->prepareDatabase();
    }

    /**
     * Prepares the database before starting migration.
     */
    private function prepareDatabase()
    {
        $sql = "ALTER TABLE  `translation_ids` CHANGE  `pk_content_old` "
            . " `pk_content_old` VARCHAR( 255 ) NOT NULL";
        $this->targetConnection->executeQuery($sql);
    }
}
