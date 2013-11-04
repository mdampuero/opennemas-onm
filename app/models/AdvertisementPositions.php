<?php
class AdvertisementPositions
{

    /**
     * undocumented class variable
     *
     * @var string
     **/
    private $positions = array();

    /**
     * Initializes this class
     **/
    public function __construct()
    {
        $positions = array(
            /* Intersticial banners frontpages */
            50 => "Banner Interticial en portada",
            50 => array(
                'name'            => _('Banner Intersticial en portada'),
                'group'           => 'interstitial',
                'user_registered' => true
            ),
            /* Frontpages banners < 100 */
            1 => "Top Left LeaderBoard",
            2 => "Top Right LeaderBoard",

            3 => "Center Left LeaderBoard",
            4 => "Center Right LeaderBoard",

            5 => "Bottom Left LeaderBoard",
            6 => "Bottom Right LeaderBoard",
            7 => "Banner Right Logo",

            11 => "Button Colunm 1 Position 1",
            12 => "Button Colunm 1 Position 2",
            13 => "Button Colunm 1 Position 3",
            14 => "Button Colunm 1 Position 4",
            15 => "Button Colunm 1 Position 5",
            16 => "Button Colunm 1 Position 6",

            21 => "Button Colunm 2 Position 1",
            22 => "Button Colunm 2 Position 2",
            24 => "Button Colunm 2 Position 4",
            25 => "Button Colunm 2 Position 5",

            31 => "Button Colunm 3 Position 1",
            32 => "Button Colunm 3 Position 2",
            33 => "Button Colunm 3 Position 3",
            34 => "Button Colunm 3 Position 4",
            35 => "Button Colunm 3 Position 5",
            36 => "Button Colunm 3 Position 6",
            37 => "Floating banner",

            9  => "Top Mega-LeaderBoard",
            91 => "Left Skyscraper",
            92 => "Right Skyscraper",

            /* Intersticial banner noticia interior */
            150 => "[I] Banner Interticial noticia interior",

            /* Noticia Interior banners > 100 */
            101 => "[I] Big banner superior",
            102 => "[I] Banner superior Derecho",

            103 => "[I] Banner Columna Derecha 1",
            104 => "[I] Robapágina",
            105 => "[I] Banner Columna Derecha 2",
            106 => "[I] Banner Columna Derecha 3",
            107 => "[I] Banner Columna Derecha 4",
            108 => "[I] Banner Columna Derecha 5",

            109 => "[I] Big Banner Inferior",
            110 => "[I] Banner Inferior Derecho",
            191 => "[I] Left Skyscraper",
            192 => "[I] Right Skyscraper",
            193 => "[I] InBody Skyscraper",

            /* Intersticial banner video front */
            250 => "[V] Banner Interticial",

            /* Videos Front banners > 200 */
            201 => "[V] Big banner superior",
            202 => "[V] Banner superior derecho",
            203 => "[V] Banner Video Button",

            209 => "[V] Big Banner Inferior",
            210 => "[V] Banner Inferior Derecho",
            291 => "[V] Left Skyscraper",
            292 => "[V] Right Skyscraper",
            /* Intersticial banner video inner */
            350 => "[VI] Banner Interticial",

            /* Video Interior banners > 300 */
            301 => "[VI] Big banner superior",
            302 => "[VI] Banner superior Derecho",

            303 => "[VI] Banner Video Button",

            309 => "[VI] Big Banner Inferior",
            310 => "[VI] Banner Inferior Derecho",
            391 => "[VI] Left Skyscraper",
            392 => "[VI] Right Skyscraper",

            /* Intersticial banner album front */
            450 => "[A] Banner Interticial",

            /* Albums Front banners > 400 */
            401 => "[A] Big banner superior",
            402 => "[A] Banner superior derecho",

            403 => "[A] Banner1 Column Right",
            405 => "[A] Banner1 2Column Right",

            409 => "[A] Big Banner Inferior",
            410 => "[A] Banner Inferior Derecho",
            491 => "[A] Left Skyscraper",
            492 => "[A] Right Skyscraper",

            /* Intersticial banner album inner */
            550 => "[AI] Banner Interticial",

            /* Album Interior banners > 500 */
            501 => "[AI] Big banner superior",
            502 => "[AI] Banner superior Derecho",

            503 => "[AI] Banner Columna Derecha",

            509 => "[AI] Big Banner Inferior",
            510 => "[AI] Banner Inferior Derecho",
            591 => "[AI] Left Skyscraper",
            592 => "[AI] Right Skyscraper",

           /* Intersticial banner opinion front */
            650 => "[O] Banner Interticial",

            /* Opinions Front banners > 600 */
            601 => "[O] Big banner superior",
            602 => "[O] Banner superior derecho",
            603 => "[O] Banner1 Column Right",
            605 => "[O] Banner1 2Column Right",
            609 => "[O] Big Banner Inferior",
            610 => "[O] Banner Inferior Derecho",
            691 => "[O] Left Skyscraper",
            692 => "[O] Right Skyscraper",

            /* Intersticial banner opinion inner */
            750 => "[OI] Banner Intersticial - Inner (800X600)",

            /* Opinion Interior banners > 700 */
            701 => "[OI] Big Banner Top(I) (728X90)",
            702 => "[OI] Banner Top Right(I) (234X90)",
            703 => "[OI] Banner1 Column Right (I) (300X*)",
            704 => "[OI] Robapágina (650X*)",
            705 => "[OI] Banner2 Column Right(I) (300X*)",
            706 => "[OI] Banner3 Column Right(I) (300X*)",
            707 => "[OI] Banner4 Column Right(I) (300X*)",
            708 => "[OI] Banner5 Column Right(I) (300X*)",
            709 => "[OI] Big Banner Bottom(I) (728X90)",
            710 => "[OI] Banner Bottom Right(I) (234X90)",
            791 => "[OI] Left Skyscraper",
            792 => "[OI] Right Skyscraper",
            793 => "[OI] InBody Skyscraper",

              /* Intersticial banner polls front */
            850 => "[E] Banner Interticial",

             /* Polls Front banners > 800 */
            801 => "[E] Big banner superior",
            802 => "[E] Banner superior derecho",

            803 => "[E] Banner1 Column Right",
            805 => "[E] Banner1 2Column Right",

            809 => "[E] Big Banner Inferior",
            810 => "[E] Banner Inferior Derecho",
            891 => "[E] Left Skyscraper",
            892 => "[E] Right Skyscraper",

            /* Intersticial banner poll inner */
            950 => "[EI] Banner Interticial",

            /* Polls  Interior banners > 900 */
            901 => "[EI] Big banner superior",
            902 => "[EI] Banner superior Derecho",

            903 => "[EI] Banner Columna Derecha",

            909 => "[EI] Big Banner Inferior",
            910 => "[EI] Banner Inferior Derecho",
            991 => "[EI] Left Skyscraper",
            992 => "[EI] Right Skyscraper",

              /* Newsletter  > 1000 */
            1001 => "[B] Big banner superior",

            1009 => "[B] Big Banner Inferior",

        );
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function addPosition($id, $positionData)
    {
        if (array_key_exists($id, $this->positions)) {
            throw new \Exception('Position id already assign');
        }

        $this->positions[$id] = $positionData;

        return $this;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function replacePosition($id, $positionData)
    {
        $this->positions[$id] = $positionData;

        return $this;
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function removePosition($positionId)
    {
        unset($this->positions[$positionId]);

        return $this;
    }

    /**
     * undocumented function
     *
     * @return array list of all positions
     **/
    public function getAllPositions()
    {
        return $this->positions;
    }
}
