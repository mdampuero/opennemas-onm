<?php

class Videos
{
    public $restler;

    /*
    * @url GET /videos/id/:id
    */
    public function id($id)
    {
        $this->validateInt(func_get_args());

        $videoInt = new Video($id);

        return $videoInt;
    }

    /*
    * @url GET /videos/category/:id
    */
    public function category($id)
    {
        $this->validateInt(func_get_args());

        $ccm = new ContentCategoryManager();
        $categoryName = $ccm->get_name($id);

        $cm = new ContentManager();
        $video =  $cm->find_by_category_name(
            'Video',
            $categoryName,
            'contents.content_status=1',
            'ORDER BY created LIMIT 1'
        );

        return $video;
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
