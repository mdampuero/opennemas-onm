<?php

class Ads
{
    public $restler;

    /*
    * @url GET /ads/frontpage/:categoryId
    */
    public function frontpage($category)
    {
        $this->validateInt(func_get_args());

        $category = (!isset($category) || ($category=='home'))? 0: $category;
        $advertisement = Advertisement::getInstance();

        $banners = $advertisement->getAdvertisements(
            array(
                1,2, 3,4, 5,6, 11,12,13,14,15,16, 21,22,24,25, 31,32,33,34,35,36,103,105
            ),
            $category
        );

        $cm = new ContentManager();
        $banners = $cm->getInTime($banners);

        // Get intersticial banner
        $intersticial = $advertisement->getIntersticial(50, $category);

        return array($intersticial,$banners);
    }

    /*
    * @url GET /ads/article/:categoryId
    */
    public function article($category)
    {
        $this->validateInt(func_get_args());

        $category = (!isset($category) || ($category=='home'))? 0: $category;
        $advertisement = Advertisement::getInstance();

        $banners = $advertisement->getAdvertisements(
            array(
                101, 102, 103, 104, 105, 106, 107, 108, 109, 110
            ),
            $category
        );

        $cm = new ContentManager();
        $banners = $cm->getInTime($banners);

        // Get intersticial banner
        $intersticial = $advertisement->getIntersticial(150, $category);

        return array($intersticial,$banners);
    }

    /*
    * @url GET /ads/opinion/:categoryId
    */
    public function opinion($category)
    {
        $this->validateInt(func_get_args());

        $category = (!isset($category) || ($category=='home'))? 0: $category;
        $advertisement = Advertisement::getInstance();

        $banners = $advertisement->getAdvertisements(
            array(
                701, 702, 703, 704, 705, 706, 707, 708, 709, 710
            ),
            $category
        );

        $cm = new ContentManager();
        $banners = $cm->getInTime($banners);

        // Get intersticial banner
        $intersticial = $advertisement->getIntersticial(750, $category);

        return array($intersticial,$banners);
    }

    private function validateInt($number)
    {
        foreach ($number as $value) {
            if (!is_numeric($value)) {
                throw new RestException(400, 'parameter is not a number');
            }
            if (is_infinite($value)) {
                throw new RestException(400, 'parameter is not finite');
            }
        }
    }
}

