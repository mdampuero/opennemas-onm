<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Diéguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 namespace Onm/Import/DataSource;
 /**
  * Handles all the operations for NITF data
  *
  * @package Onm
  * @author 
  **/
 class NITF
 {
    /**
     * Instantiates the NITF DOM data from an SimpleXML object
     *
     * @return void
     * @author 
     **/
    public function __construct($data)
    {
        $this->data = $data;
    }


 } // END class NITF