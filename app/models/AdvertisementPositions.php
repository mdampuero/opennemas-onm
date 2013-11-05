<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Advertisement Position class
 *
 * Handles all the CRUD operations with advertisement positions.
 *
 * @package Onm
 * @subpackage Model
 **/
class AdvertisementPositions
{
    /**
     * Array with all ads positions
     *
     * @var string
     **/
    private $positions = array();

    /**
     * Initializes this class
     **/
    public function __construct()
    {
        $this->positions = array(

            // Frontpage banners
            1 => array(
                'name'  => 'Top Left LeaderBoard',
                'group' => 'frontpage'
            ),
            2 => array(
                'name'  => 'Top Right LeaderBoard',
                'group' => 'frontpage'
            ),
            3 => array(
                'name'  => 'Center Left LeaderBoard',
                'group' => 'frontpage'
            ),
            4 => array(
                'name'  => 'Center Right LeaderBoard',
                'group' => 'frontpage'
            ),
            5 => array(
                'name'  => 'Bottom Left LeaderBoard',
                'group' => 'frontpage'
            ),
            6 => array(
                'name'  => 'Bottom Right LeaderBoard',
                'group' => 'frontpage'
            ),
            7 => array(
                'name'  => 'Banner Right Logo',
                'group' => 'frontpage'
            ),
            9  => array(
                'name'  => 'Top Mega-LeaderBoard',
                'group' => 'frontpage'
            ),

            // Frontpage column 1 buttons
            11 => array(
                'name'  => 'Button Column 1 Position 1',
                'group' => 'frontpage'
            ),
            12 => array(
                'name'  => 'Button Column 1 Position 2',
                'group' => 'frontpage'
            ),
            13 => array(
                'name'  => 'Button Column 1 Position 3',
                'group' => 'frontpage'
            ),
            14 => array(
                'name'  => 'Button Column 1 Position 4',
                'group' => 'frontpage'
            ),
            15 => array(
                'name'  => 'Button Column 1 Position 5',
                'group' => 'frontpage'
            ),
            16 => array(
                'name'  => 'Button Column 1 Position 6',
                'group' => 'frontpage'
            ),

            // Frontpage column 2 buttons
            21 => array(
                'name'  => 'Button Colunm 2 Position 1',
                'group' => 'frontpage'
            ),
            22 => array(
                'name'  => 'Button Colunm 2 Position 2',
                'group' => 'frontpage'
            ),
            24 => array(
                'name'  => 'Button Colunm 2 Position 4',
                'group' => 'frontpage'
            ),
            25 => array(
                'name'  => 'Button Colunm 2 Position 5',
                'group' => 'frontpage'
            ),

            // Frontpage column 3 buttons
            31 => array(
                'name'  => 'Button Colunm 3 Position 1',
                'group' => 'frontpage'
            ),
            32 => array(
                'name'  => 'Button Colunm 3 Position 2',
                'group' => 'frontpage'
            ),
            33 => array(
                'name'  => 'Button Colunm 3 Position 3',
                'group' => 'frontpage'
            ),
            34 => array(
                'name'  => 'Button Colunm 3 Position 4',
                'group' => 'frontpage'
            ),
            35 => array(
                'name'  => 'Button Colunm 3 Position 5',
                'group' => 'frontpage'
            ),
            36 => array(
                'name'  => 'Button Colunm 3 Position 6',
                'group' => 'frontpage'
            ),
            37 => array(
                'name'  => 'Floating banner',
                'group' => 'frontpage'
            ),

            // Frontpage intersticial
            50 => array(
                'name'  => 'Banner Interticial en portada',
                'group' => 'frontpage'
            ),

            // Frontpage skycrapers
            91 => array(
                'name'  => 'Left Skyscraper',
                'group' => 'frontpage'
            ),
            92 => array(
                'name'  => 'Right Skyscraper',
                'group' => 'frontpage'
            ),

            // Article inner banners
            101 => array(
                'name'  => '[I] Big banner superior',
                'group' => 'article_inner'
            ),
            102 => array(
                'name'  => '[I] Banner superior Derecho',
                'group' => 'article_inner'
            ),
            103 => array(
                'name'  => '[I] Banner Columna Derecha 1',
                'group' => 'article_inner'
            ),
            104 => array(
                'name'  => '[I] Robapágina',
                'group' => 'article_inner'
            ),
            105 => array(
                'name'  => '[I] Banner Columna Derecha 2',
                'group' => 'article_inner'
            ),
            106 => array(
                'name'  => '[I] Banner Columna Derecha 3',
                'group' => 'article_inner'
            ),
            107 => array(
                'name'  => '[I] Banner Columna Derecha 4',
                'group' => 'article_inner'
            ),
            108 => array(
                'name'  => '[I] Banner Columna Derecha 5',
                'group' => 'article_inner'
            ),
            109 => array(
                'name'  => '[I] Big Banner Inferior',
                'group' => 'article_inner'
            ),
            110 => array(
                'name'  => '[I] Banner Inferior Derecho',
                'group' => 'article_inner'
            ),

            // Intersticial banner article inner
            150 => array(
                'name'  => '[I] Banner Interticial noticia interior',
                'group' => 'article_inner'
            ),

            // Skycraper banners article inner
            191 => array(
                'name'  => '[I] Left Skyscraper',
                'group' => 'article_inner'
            ),
            192 => array(
                'name'  => '[I] Right Skyscraper',
                'group' => 'article_inner'
            ),
            193 => array(
                'name'  => '[I] InBody Skyscraper',
                'group' => 'article_inner'
            ),

            // Videos frontpage banners
            201 => array(
                'name'  => '[V] Big banner superior',
                'group' => 'video_frontpage'
            ),
            202 => array(
                'name'  => '[V] Banner superior derecho',
                'group' => 'video_frontpage'
            ),
            203 => array(
                'name'  => '[V] Banner Video Button',
                'group' => 'video_frontpage'
            ),
            209 => array(
                'name'  => '[V] Big Banner Inferior',
                'group' => 'video_frontpage'
            ),
            210 => array(
                'name'  => '[V] Banner Inferior Derecho',
                'group' => 'video_frontpage'
            ),

            // Intersticial banner video front
            250 => array(
                'name'  => '[V] Banner Interticial',
                'group' => 'video_frontpage'
            ),

            // Intersticial banner video front
            291 => array(
                'name'  => '[V] Left Skyscraper',
                'group' => 'video_frontpage'
            ),
            292 => array(
                'name'  => '[V] Right Skyscraper',
                'group' => 'video_frontpage'
            ),

            // Video inner banners
            301 => array(
                'name'  => '[VI] Big banner superior',
                'group' => 'video_inner'
            ),
            302 => array(
                'name'  => '[VI] Banner superior Derecho',
                'group' => 'video_inner'
            ),
            303 => array(
                'name'  => '[VI] Banner Video Button',
                'group' => 'video_inner'
            ),
            309 => array(
                'name'  => '[VI] Big Banner Inferior',
                'group' => 'video_inner'
            ),
            310 => array(
                'name'  => '[VI] Banner Inferior Derecho',
                'group' => 'video_inner'
            ),

            // Intersticial banner video inner
            350 => array(
                'name'  => '[VI] Banner Interticial',
                'group' => 'video_inner'
            ),

            // Intersticial banner video inner
            391 => array(
                'name'  => '[VI] Left Skyscraper',
                'group' => 'video_inner'
            ),
            392 => array(
                'name'  => '[VI] Right Skyscraper',
                'group' => 'video_inner'
            ),

            // Albums frontpage banners
            401 => array(
                'name'  => '[A] Big banner superior',
                'group' => 'album_frontpage'
            ),
            402 => array(
                'name'  => '[A] Banner superior Derecho',
                'group' => 'album_frontpage'
            ),
            403 => array(
                'name'  => '[A] Banner 1 Column Right',
                'group' => 'album_frontpage'
            ),
            405 => array(
                'name'  => '[A] Banner 2 Column Right',
                'group' => 'album_frontpage'
            ),
            409 => array(
                'name'  => '[A] Big Banner Inferior',
                'group' => 'album_frontpage'
            ),
            410 => array(
                'name'  => '[A] Banner Inferior Derecho',
                'group' => 'album_frontpage'
            ),

            // Intersticial banner album front
            450 => array(
                'name'  => '[A] Banner Interticial',
                'group' => 'album_frontpage'
            ),

            // Intersticial banner album front
            491 => array(
                'name'  => '[A] Left Skyscraper',
                'group' => 'album_frontpage'
            ),
            492 => array(
                'name'  => '[A] Right Skyscraper',
                'group' => 'album_frontpage'
            ),

            // Albums inner banners
            501 => array(
                'name'  => '[AI] Big banner superior',
                'group' => 'album_inner'
            ),
            502 => array(
                'name'  => '[AI] Banner superior Derecho',
                'group' => 'album_inner'
            ),
            503 => array(
                'name'  => '[AI] Banner Columna Derecha',
                'group' => 'album_inner'
            ),
            509 => array(
                'name'  => '[AI] Big Banner Inferior',
                'group' => 'album_inner'
            ),
            510 => array(
                'name'  => '[AI] Banner Inferior Derecho',
                'group' => 'album_inner'
            ),

            // Intersticial banner album inner
            550 => array(
                'name'  => '[AI] Banner Interticial',
                'group' => 'album_inner'
            ),

            // Intersticial banner album inner
            591 => array(
                'name'  => '[AI] Left Skyscraper',
                'group' => 'album_inner'
            ),
            592 => array(
                'name'  => '[AI] Right Skyscraper',
                'group' => 'album_inner'
            ),

            // Opinion frontpage banners
            601 => array(
                'name'  => '[O] Big banner superior',
                'group' => 'opinion_frontpage'
            ),
            602 => array(
                'name'  => '[O] Banner superior Derecho',
                'group' => 'opinion_frontpage'
            ),
            603 => array(
                'name'  => '[O] Banner 1 Column Right',
                'group' => 'opinion_frontpage'
            ),
            605 => array(
                'name'  => '[O] Banner 2 Column Right',
                'group' => 'opinion_frontpage'
            ),
            609 => array(
                'name'  => '[O] Big Banner Inferior',
                'group' => 'opinion_frontpage'
            ),
            610 => array(
                'name'  => '[O] Banner Inferior Derecho',
                'group' => 'opinion_frontpage'
            ),

            // Intersticial banner opinion front
            650 => array(
                'name'  => '[O] Banner Interticial',
                'group' => 'opinion_frontpage'
            ),

            // Intersticial banner opinion front
            691 => array(
                'name'  => '[O] Left Skyscraper',
                'group' => 'opinion_frontpage'
            ),
            692 => array(
                'name'  => '[O] Right Skyscraper',
                'group' => 'opinion_frontpage'
            ),

            // Opinion inner banners
            701 => array(
                'name'  => '[OI] Big Banner Top(I) (728X90)',
                'group' => 'opinion_inner'
            ),
            702 => array(
                'name'  => '[OI] Banner Top Right(I) (234X90)',
                'group' => 'opinion_inner'
            ),
            703 => array(
                'name'  => '[OI] Banner1 Column Right (I) (300X*)',
                'group' => 'opinion_inner'
            ),
            704 => array(
                'name'  => '[OI] Robapágina (650X*)',
                'group' => 'opinion_inner'
            ),
            705 => array(
                'name'  => '[OI] Banner2 Column Right(I) (300X*)',
                'group' => 'opinion_inner'
            ),
            706 => array(
                'name'  => '[OI] Banner3 Column Right(I) (300X*)',
                'group' => 'opinion_inner'
            ),
            707 => array(
                'name'  => '[OI] Banner4 Column Right(I) (300X*)',
                'group' => 'opinion_inner'
            ),
            708 => array(
                'name'  => '[OI] Banner5 Column Right(I) (300X*)',
                'group' => 'opinion_inner'
            ),
            709 => array(
                'name'  => '[OI] Big Banner Bottom(I) (728X90)',
                'group' => 'opinion_inner'
            ),
            710 => array(
                'name'  => '[OI] Banner Bottom Right(I) (234X90)',
                'group' => 'opinion_inner'
            ),

            // Intersticial banner opinion inner
            750 => array(
                'name'  => '[OI] Banner Intersticial - Inner (800X600)',
                'group' => 'opinion_inner'
            ),

            // Intersticial banner opinion inner
            791 => array(
                'name'  => '[OI] Left Skyscraper',
                'group' => 'opinion_inner'
            ),
            792 => array(
                'name'  => '[OI] Right Skyscraper',
                'group' => 'opinion_inner'
            ),
            793 => array(
                'name'  => '[OI] InBody Skyscraper',
                'group' => 'opinion_inner'
            ),


            // Polls frontpage banners
            801 => array(
                'name'  => '[E] Big banner superior',
                'group' => 'polls_frontpage'
            ),
            802 => array(
                'name'  => '[E] Banner superior Derecho',
                'group' => 'polls_frontpage'
            ),
            803 => array(
                'name'  => '[E] Banner 1 Column Right',
                'group' => 'polls_frontpage'
            ),
            805 => array(
                'name'  => '[E] Banner 2 Column Right',
                'group' => 'polls_frontpage'
            ),
            809 => array(
                'name'  => '[E] Big Banner Inferior',
                'group' => 'polls_frontpage'
            ),
            810 => array(
                'name'  => '[E] Banner Inferior Derecho',
                'group' => 'polls_frontpage'
            ),

            // Intersticial banner polls front
            850 => array(
                'name'  => '[E] Banner Interticial',
                'group' => 'polls_frontpage'
            ),

            // Intersticial banner polls front
            891 => array(
                'name'  => '[E] Left Skyscraper',
                'group' => 'polls_frontpage'
            ),
            892 => array(
                'name'  => '[E] Right Skyscraper',
                'group' => 'polls_frontpage'
            ),


            // Polls inner banners
            901 => array(
                'name'  => '[EI] Big banner superior',
                'group' => 'polls_inner'
            ),
            902 => array(
                'name'  => '[EI] Banner superior Derecho',
                'group' => 'polls_inner'
            ),
            903 => array(
                'name'  => '[EI] Banner Columna Derecha',
                'group' => 'polls_inner'
            ),
            909 => array(
                'name'  => '[EI] Big Banner Inferior',
                'group' => 'polls_inner'
            ),
            910 => array(
                'name'  => '[EI] Banner Inferior Derecho',
                'group' => 'polls_inner'
            ),

            // Intersticial banner polls inner
            950 => array(
                'name'  => '[EI] Banner Interticial',
                'group' => 'polls_inner'
            ),

            // Intersticial banner polls inner
            991 => array(
                'name'  => '[EI] Left Skyscraper',
                'group' => 'polls_inner'
            ),
            992 => array(
                'name'  => '[EI] Right Skyscraper',
                'group' => 'polls_inner'
            ),

            // Newsletter banners
            1001 => array(
                'name'  => '[B] Big banner superior',
                'group' => 'newsletter'
            ),
            1009 => array(
                'name'  => '[B] Big Banner Inferior',
                'group' => 'newsletter'
            ),
        );
    }

    /**
     * Add new advertisement position
     *
     * @param int $id a position id
     * @param int $positionData a position information
     *
     * @return void
     **/
    public function addPositions($positions)
    {
        foreach ($positions as $id => $data) {
            $data['custom'] = true;
            $this->positions[$id] = $data;
        }

        return $this;
    }

    /**
     * Replace an already defined advertisement position
     *
     * @param int $id a position id
     * @param int $positionData a position information
     *
     * @return void
     **/
    public function replacePosition($id, $positionData)
    {
        $this->positions[$id] = $positionData;

        return $this;
    }

    /**
     * Remove an advertisement position
     *
     * @param int $positionId a position id
     *
     * @return void
     **/
    public function removePosition($positionId)
    {
        unset($this->positions[$positionId]);

        return $this;
    }

    /**
     * Retrieves all the defined advertisement positions
     *
     * @return array list of all positions
     **/
    public function getAllAdsPositions()
    {
        return $this->positions;
    }

    /**
     * Retrieves all the theme advertisement positions
     *
     * @return array list of all positions
     **/
    public function getThemeAdsPositions()
    {
        $themeAds = array();
        foreach ($this->positions as $key => $value) {
            if (array_key_exists('custom', $value)) {
                $themeAds[$key] = $value;
            }
        }

        return $themeAds;
    }

    /**
     * Retrieves all the defined advertisement positions for a group
     *
     * @param string $groupName name of an ads group
     *
     * @return array list of all positions for a group
     **/
    public function getAdsPositionsForGroup($groupName = null, $positions = array())
    {
        $groupPositions = array();
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
     * Retrieves all the names for advertisement positions
     *
     * @return array list of all names
     **/
    public function getAllAdsNames()
    {
        $adsNames = array();
        foreach ($this->positions as $key => $value) {
            $adsNames[$key] = $value['name'];
        }

        return $adsNames;
    }
}
