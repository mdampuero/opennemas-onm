<?php

namespace Common\Core\Component\Helper;

/**
* Perform searches in Database related with one content
*/
class WidgetHelper extends ContentHelper
{
    /**
     * Returns true if the widget exists in the database, false otherwise.
     *
     * @param String $name The name of the widget to perform the search.
     *
     * @return boolean True if the widget exists, false otherwise.
     */
    public function widgetExists($name)
    {
        $oql = sprintf(
            'content_type_name = "widget" ' .
            'and in_litter = 0 and content_status = 1 ' .
            'and class = "%s"',
            $name
        );

        try {
            return $this->container->get('api.service.content')
                ->getList($oql)['total'] > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
