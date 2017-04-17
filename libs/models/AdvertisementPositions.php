<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class AdvertisementPositions
{
    /**
     * Array with all ads positions.
     *
     * @var array
     */
    private $positions = [];

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function __construct()
    {
        $this->positions = [
            1 => [
                'name'  => _('Top Left LeaderBoard'),
                'group' => 'frontpage'
            ],
            2 => [
                'name'  => _('Top Right LeaderBoard'),
                'group' => 'frontpage'
            ],
            3 => [
                'name'  => 'Center Left LeaderBoard',
                'group' => 'frontpage'
            ],
            4 => [
                'name'  => 'Center Right LeaderBoard',
                'group' => 'frontpage'
            ],
            5 => [
                'name'  => 'Bottom Left LeaderBoard',
                'group' => 'frontpage'
            ],
            6 => [
                'name'  => 'Bottom Right LeaderBoard',
                'group' => 'frontpage'
            ],
            7 => [
                'name'  => 'Banner Right Logo',
                'group' => 'frontpage'
            ],
            9  => [
                'name'  => 'Top Mega-LeaderBoard',
                'group' => 'frontpage'
            ],

            // Frontpage column 1 buttons
            11 => [
                'name'  => 'Button Column 1 Position 1',
                'group' => 'frontpage'
            ],
            12 => [
                'name'  => 'Button Column 1 Position 2',
                'group' => 'frontpage'
            ],
            13 => [
                'name'  => 'Button Column 1 Position 3',
                'group' => 'frontpage'
            ],
            14 => [
                'name'  => 'Button Column 1 Position 4',
                'group' => 'frontpage'
            ],
            15 => [
                'name'  => 'Button Column 1 Position 5',
                'group' => 'frontpage'
            ],
            16 => [
                'name'  => 'Button Column 1 Position 6',
                'group' => 'frontpage'
            ],

            // Frontpage column 2 buttons
            21 => [
                'name'  => 'Button Colunm 2 Position 1',
                'group' => 'frontpage'
            ],
            22 => [
                'name'  => 'Button Colunm 2 Position 2',
                'group' => 'frontpage'
            ],
            23 => [
                'name'  => 'Button Colunm 2 Position 3',
                'group' => 'frontpage'
            ],
            24 => [
                'name'  => 'Button Colunm 2 Position 4',
                'group' => 'frontpage'
            ],
            25 => [
                'name'  => 'Button Colunm 2 Position 5',
                'group' => 'frontpage'
            ],
            26 => [
                'name'  => 'Button Colunm 2 Position 6',
                'group' => 'frontpage'
            ],

            // Frontpage column 3 buttons
            31 => [
                'name'  => 'Button Colunm 3 Position 1',
                'group' => 'frontpage'
            ],
            32 => [
                'name'  => 'Button Colunm 3 Position 2',
                'group' => 'frontpage'
            ],
            33 => [
                'name'  => 'Button Colunm 3 Position 3',
                'group' => 'frontpage'
            ],
            34 => [
                'name'  => 'Button Colunm 3 Position 4',
                'group' => 'frontpage'
            ],
            35 => [
                'name'  => 'Button Colunm 3 Position 5',
                'group' => 'frontpage'
            ],
            36 => [
                'name'  => 'Button Colunm 3 Position 6',
                'group' => 'frontpage'
            ],
            37 => [
                'name'  => 'Floating banner',
                'group' => 'frontpage'
            ],

            // Frontpage intersticial
            50 => [
                'name'  => 'Banner Interticial en portada',
                'group' => 'frontpage'
            ],

            // Frontpage skycrapers
            91 => [
                'name'  => 'Left Skyscraper',
                'group' => 'frontpage'
            ],
            92 => [
                'name'  => 'Right Skyscraper',
                'group' => 'frontpage'
            ],

            // Article inner banners
            101 => [
                'name'  => '[I] Big banner superior',
                'group' => 'article_inner'
            ],
            102 => [
                'name'  => '[I] Banner superior Derecho',
                'group' => 'article_inner'
            ],
            103 => [
                'name'  => '[I] Banner Columna Derecha 1',
                'group' => 'article_inner'
            ],
            104 => [
                'name'  => '[I] Robapágina',
                'group' => 'article_inner'
            ],
            105 => [
                'name'  => '[I] Banner Columna Derecha 2',
                'group' => 'article_inner'
            ],
            106 => [
                'name'  => '[I] Banner Columna Derecha 3',
                'group' => 'article_inner'
            ],
            107 => [
                'name'  => '[I] Banner Columna Derecha 4',
                'group' => 'article_inner'
            ],
            108 => [
                'name'  => '[I] Banner Columna Derecha 5',
                'group' => 'article_inner'
            ],
            109 => [
                'name'  => '[I] Big Banner Inferior',
                'group' => 'article_inner'
            ],
            110 => [
                'name'  => '[I] Banner Inferior Derecho',
                'group' => 'article_inner'
            ],

            // Intersticial banner article inner
            150 => [
                'name'  => '[I] Banner Interticial noticia interior',
                'group' => 'article_inner'
            ],

            // Skycraper banners article inner
            191 => [
                'name'  => '[I] Left Skyscraper',
                'group' => 'article_inner'
            ],
            192 => [
                'name'  => '[I] Right Skyscraper',
                'group' => 'article_inner'
            ],
            193 => [
                'name'  => '[I] InBody Skyscraper',
                'group' => 'article_inner'
            ],

            // Videos frontpage banners
            201 => [
                'name'  => '[V] Big banner superior',
                'group' => 'video_frontpage'
            ],
            202 => [
                'name'  => '[V] Banner superior derecho',
                'group' => 'video_frontpage'
            ],
            203 => [
                'name'  => '[V] Banner Video Button',
                'group' => 'video_frontpage'
            ],
            209 => [
                'name'  => '[V] Big Banner Inferior',
                'group' => 'video_frontpage'
            ],
            210 => [
                'name'  => '[V] Banner Inferior Derecho',
                'group' => 'video_frontpage'
            ],

            // Intersticial banner video front
            250 => [
                'name'  => '[V] Banner Interticial',
                'group' => 'video_frontpage'
            ],

            // Intersticial banner video front
            291 => [
                'name'  => '[V] Left Skyscraper',
                'group' => 'video_frontpage'
            ],
            292 => [
                'name'  => '[V] Right Skyscraper',
                'group' => 'video_frontpage'
            ],

            // Video inner banners
            301 => [
                'name'  => '[VI] Big banner superior',
                'group' => 'video_inner'
            ],
            302 => [
                'name'  => '[VI] Banner superior Derecho',
                'group' => 'video_inner'
            ],
            303 => [
                'name'  => '[VI] Banner Video Button',
                'group' => 'video_inner'
            ],
            309 => [
                'name'  => '[VI] Big Banner Inferior',
                'group' => 'video_inner'
            ],
            310 => [
                'name'  => '[VI] Banner Inferior Derecho',
                'group' => 'video_inner'
            ],

            // Intersticial banner video inner
            350 => [
                'name'  => '[VI] Banner Interticial',
                'group' => 'video_inner'
            ],

            // Intersticial banner video inner
            391 => [
                'name'  => '[VI] Left Skyscraper',
                'group' => 'video_inner'
            ],
            392 => [
                'name'  => '[VI] Right Skyscraper',
                'group' => 'video_inner'
            ],

            // Albums frontpage banners
            401 => [
                'name'  => '[A] Big banner superior',
                'group' => 'album_frontpage'
            ],
            402 => [
                'name'  => '[A] Banner superior Derecho',
                'group' => 'album_frontpage'
            ],
            403 => [
                'name'  => '[A] Banner 1 Column Right',
                'group' => 'album_frontpage'
            ],
            405 => [
                'name'  => '[A] Banner 2 Column Right',
                'group' => 'album_frontpage'
            ],
            409 => [
                'name'  => '[A] Big Banner Inferior',
                'group' => 'album_frontpage'
            ],
            410 => [
                'name'  => '[A] Banner Inferior Derecho',
                'group' => 'album_frontpage'
            ],

            // Intersticial banner album front
            450 => [
                'name'  => '[A] Banner Interticial',
                'group' => 'album_frontpage'
            ],

            // Intersticial banner album front
            491 => [
                'name'  => '[A] Left Skyscraper',
                'group' => 'album_frontpage'
            ],
            492 => [
                'name'  => '[A] Right Skyscraper',
                'group' => 'album_frontpage'
            ],

            // Albums inner banners
            501 => [
                'name'  => '[AI] Big banner superior',
                'group' => 'album_inner'
            ],
            502 => [
                'name'  => '[AI] Banner superior Derecho',
                'group' => 'album_inner'
            ],
            503 => [
                'name'  => '[AI] Banner Columna Derecha',
                'group' => 'album_inner'
            ],
            509 => [
                'name'  => '[AI] Big Banner Inferior',
                'group' => 'album_inner'
            ],
            510 => [
                'name'  => '[AI] Banner Inferior Derecho',
                'group' => 'album_inner'
            ],

            // Intersticial banner album inner
            550 => [
                'name'  => '[AI] Banner Interticial',
                'group' => 'album_inner'
            ],

            591 => [
                'name'  => '[AI] Left Skyscraper',
                'group' => 'album_inner'
            ],
            592 => [
                'name'  => '[AI] Right Skyscraper',
                'group' => 'album_inner'
            ],

            // Opinion frontpage banners
            601 => [
                'name'  => '[O] Big banner superior',
                'group' => 'opinion_frontpage'
            ],
            602 => [
                'name'  => '[O] Banner superior Derecho',
                'group' => 'opinion_frontpage'
            ],
            603 => [
                'name'  => '[O] Banner 1 Column Right',
                'group' => 'opinion_frontpage'
            ],
            605 => [
                'name'  => '[O] Banner 2 Column Right',
                'group' => 'opinion_frontpage'
            ],
            609 => [
                'name'  => '[O] Big Banner Inferior',
                'group' => 'opinion_frontpage'
            ],
            610 => [
                'name'  => '[O] Banner Inferior Derecho',
                'group' => 'opinion_frontpage'
            ],

            // Intersticial banner opinion front
            650 => [
                'name'  => '[O] Banner Interticial',
                'group' => 'opinion_frontpage'
            ],

            691 => [
                'name'  => '[O] Left Skyscraper',
                'group' => 'opinion_frontpage'
            ],
            692 => [
                'name'  => '[O] Right Skyscraper',
                'group' => 'opinion_frontpage'
            ],

            // Opinion inner banners
            701 => [
                'name'  => '[OI] Big Banner Top(I) (728X90)',
                'group' => 'opinion_inner'
            ],
            702 => [
                'name'  => '[OI] Banner Top Right(I) (234X90)',
                'group' => 'opinion_inner'
            ],
            703 => [
                'name'  => '[OI] Banner1 Column Right (I) (300X*)',
                'group' => 'opinion_inner'
            ],
            704 => [
                'name'  => '[OI] Robapágina (650X*)',
                'group' => 'opinion_inner'
            ],
            705 => [
                'name'  => '[OI] Banner2 Column Right(I) (300X*)',
                'group' => 'opinion_inner'
            ],
            706 => [
                'name'  => '[OI] Banner3 Column Right(I) (300X*)',
                'group' => 'opinion_inner'
            ],
            707 => [
                'name'  => '[OI] Banner4 Column Right(I) (300X*)',
                'group' => 'opinion_inner'
            ],
            708 => [
                'name'  => '[OI] Banner5 Column Right(I) (300X*)',
                'group' => 'opinion_inner'
            ],
            709 => [
                'name'  => '[OI] Big Banner Bottom(I) (728X90)',
                'group' => 'opinion_inner'
            ],
            710 => [
                'name'  => '[OI] Banner Bottom Right(I) (234X90)',
                'group' => 'opinion_inner'
            ],

            // Intersticial banner opinion inner
            750 => [
                'name'  => '[OI] Banner Intersticial - Inner (800X600)',
                'group' => 'opinion_inner'
            ],

            791 => [
                'name'  => '[OI] Left Skyscraper',
                'group' => 'opinion_inner'
            ],
            792 => [
                'name'  => _('[OI] Right Skyscraper'),
                'group' => 'opinion_inner'
            ],
            793 => [
                'name'  => '[OI] InBody Skyscraper',
                'group' => 'opinion_inner'
            ],

            // Polls frontpage banners
            801 => [
                'name'  => '[E] Big banner superior',
                'group' => 'polls_frontpage'
            ],
            802 => [
                'name'  => '[E] Banner superior Derecho',
                'group' => 'polls_frontpage'
            ],
            803 => [
                'name'  => '[E] Banner 1 Column Right',
                'group' => 'polls_frontpage'
            ],
            805 => [
                'name'  => '[E] Banner 2 Column Right',
                'group' => 'polls_frontpage'
            ],
            809 => [
                'name'  => '[E] Big Banner Inferior',
                'group' => 'polls_frontpage'
            ],
            810 => [
                'name'  => '[E] Banner Inferior Derecho',
                'group' => 'polls_frontpage'
            ],

            // Intersticial banner polls front
            850 => [
                'name'  => '[E] Banner Interticial',
                'group' => 'polls_frontpage'
            ],

            // Intersticial banner polls front
            891 => [
                'name'  => '[E] Left Skyscraper',
                'group' => 'polls_frontpage'
            ],
            892 => [
                'name'  => '[E] Right Skyscraper',
                'group' => 'polls_frontpage'
            ],

            // Polls inner banners
            901 => [
                'name'  => '[EI] Big banner superior',
                'group' => 'polls_inner'
            ],
            902 => [
                'name'  => '[EI] Banner superior Derecho',
                'group' => 'polls_inner'
            ],
            903 => [
                'name'  => '[EI] Banner Columna Derecha',
                'group' => 'polls_inner'
            ],
            909 => [
                'name'  => '[EI] Big Banner Inferior',
                'group' => 'polls_inner'
            ],
            910 => [
                'name'  => '[EI] Banner Inferior Derecho',
                'group' => 'polls_inner'
            ],

            // Intersticial banner polls inner
            950 => [
                'name'  => '[EI] Banner Interticial',
                'group' => 'polls_inner'
            ],

            991 => [
                'group_identifier' => '[EI] ',
                'name'  => 'Left Skyscraper',
                'group' => 'polls_inner'
            ],
            992 => [
                'name'  => '[EI] Right Skyscraper',
                'group' => 'polls_inner'
            ],

            // Newsletter banners
            1001 => [
                'name'  => '[B] Big banner superior',
                'group' => 'newsletter'
            ],
            1009 => [
                'name'  => '[B] Big Banner Inferior',
                'group' => 'newsletter'
            ],

            // AMP Positions
            1051 => [
                'name'  => 'AMP inner article - Button 1',
                'group' => 'amp_inner'
            ],
            1052 => [
                'name'  => 'AMP inner article - Button 2',
                'group' => 'amp_inner'
            ],
            1053 => [
                'name'  => 'AMP inner article - Button 3',
                'group' => 'amp_inner'
            ],

            // FIA Positions
            1075 => [
                'name'  => 'Instant Articles inner article - Button 1',
                'group' => 'fia_inner'
            ],
            1076 => [
                'name'  => 'Instant Articles inner article - Button 2',
                'group' => 'fia_inner'
            ],
            1077 => [
                'name'  => 'Instant Articles inner article - Button 3',
                'group' => 'fia_inner'
            ],
        ];
    }

    /**
     * Add new advertisement position.
     *
     * @param array $positions Positions to add.
     */
    public function addPositions($positions)
    {
        if (!is_array($positions)) {
            return $this;
        }

        foreach ($positions as $data) {
            $data['custom'] = true;
            $this->positions[$data['position']] = $data;
        }

        return $this;
    }

    /**
     * Returns the list of positions.
     *
     * @return array The list of positions.
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * Returns the list of positions for the current theme.
     *
     * @return array The list of positions for the current theme.
     */
    public function getPositionsForTheme()
    {
        $ads = [];
        foreach ($this->positions as $key => $value) {
            if (array_key_exists('custom', $value)) {
                $ads[$key] = $value;
            }
        }

        return $ads;
    }

    /**
     * Returns the list of positions for a group.
     *
     * @param string $groupName The name of a group.
     *
     * @return array The list of positions for a group.
     */
    public function getPositionsForGroup($groupName = null, $positions = [])
    {
        $groupPositions = [];
        if (!is_null($groupName)) {
            // Get group positions
            foreach ($this->positions as $key => $value) {
                if ($value['group'] == $groupName) {
                    $groupPositions[] = $key;
                }
            }
        }

        // Add more positions if exists
        if (!empty($positions)) {
            foreach ($positions as $key => $value) {
                $groupPositions[] = $value;
            }
        }

        return $groupPositions;
    }

    /**
     * Returns the list of names for advertisement positions.
     *
     * @return array The list of names.
     */
    public function getPositionNames()
    {
        $adsNames = [];
        foreach ($this->positions as $key => $value) {
            $adsNames[$key] = $value['name'];
        }

        return $adsNames;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getAdvertisementName($id)
    {
        return $this->positions[$id]['name'];
    }
}
