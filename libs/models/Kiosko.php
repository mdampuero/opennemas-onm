<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Kiosko extends Content
{
    /**
      * Initializes the Kiosko.
      *
      * @param integer $id The kiosko id.
      */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Kiosko');
        $this->content_type           = 14;
        $this->content_type_name      = 'kiosko';

        parent::__construct($id);
    }
}
