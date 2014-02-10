<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Diego Blanco Estévez <diego@openhost.es>
 *
 */
namespace Framework\Migrator\Provider;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

use Onm\DatabaseConnection;
use Onm\Settings as s;
use Onm\StringUtils;

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
        $debug = false
    ) {
        parent::__construct($logger, $settings, $translations, $stats, $debug);

        $this->prepareDatabase();
    }

    /**
     * Prepares the database before starting migration.
     */
    private function prepareDatabase()
    {
        $sql = "ALTER TABLE  `translation_ids` CHANGE  `pk_content_old` "
            . " `pk_content_old` VARCHAR( 255 ) NOT NULL";
        $rss = $this->targetConnection->Execute($sql);
    }
}
