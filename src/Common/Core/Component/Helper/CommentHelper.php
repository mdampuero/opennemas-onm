<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class CommentHelper
{
    /**
     * The dataset for settings.
     *
     * @var DataSet
     */
    protected $ds;

    /**
     * Initializes the AdvertisementHelper.
     *
     * @param EntityManager $em             The entity manager.
     * @param array         $defaultConfigs The list of configurations by
     *                                      default.
     */
    public function __construct($em, $defaultConfigs)
    {
        $this->ds             = $em->getDataSet('Settings', 'instance');
        $this->configs        = $this->ds->get('comments_config', []);
        $this->defaultConfigs = $defaultConfigs;
    }

    /**
     * Whether if comments must be automatically rejected according to configs
     *
     * @return boolean true if comments must be autorejected
     */
    public function autoReject()
    {
        return $this->getConfigs()['moderation_autoreject'] == 1;
    }

    /**
     * Whether if comments must be automatically accepted according to configs
     *
     * @return boolean true if comments must be autoaccepted
     */
    public function autoAccept()
    {
        return $this->getConfigs()['moderation_autoaccept'] == 1;
    }

    /**
     * Returns the complete configs merged with the default
     *
     * @return array the list of configs
     **/
    public function getConfigs()
    {
        if (!is_array($this->configs)) {
            $this->configs = [];
        }

        return array_merge($this->getDefaultConfigs(), $this->configs);
    }

    /**
     * Returns a list of configurations for the comments module
     *
     * @return array the list of default configurations
     **/
    public function getDefaultConfigs()
    {
        return $this->defaultConfigs;
    }

    /**
     * Whether comments must be enabled by default in comments or not
     *
     * @return boolean true if comments are enabled by default on contents
     **/
    public function enableCommentsByDefault()
    {
        return $this->getConfigs()['with_comments'] == 1;
    }

    /**
     * Whether if comments must be moderated manually or not according to configs
     *
     * @return boolean true if the comments are moderated manually
     */
    public function moderateManually()
    {
        return $this->getConfigs()['moderation_manual'] == 1;
    }
}
