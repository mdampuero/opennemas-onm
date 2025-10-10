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
        $this->configs        = $this->ds->get('comment_settings', []);
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
     */
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
     */
    public function getDefaultConfigs()
    {
        return $this->defaultConfigs;
    }

    /**
     * Whether comments must be enabled by default in comments or not
     *
     * @return boolean true if comments are enabled by default on contents
     */
    public function enableCommentsByDefault()
    {
        return $this->getConfigs()['with_comments'] == 1;
    }

    /**
     * Whether if comments must have an email
     *
     * @return boolean true if the comments author email is mandatory
     */
    public function isEmailRequired()
    {
        return $this->getConfigs()['required_email'] == 1;
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

    /**
     * Returns the decoded custom code (HTML, CSS, or JavaScript) configured for comments.
     *
     * This method retrieves the Base64-encoded custom code stored in the configuration
     * under the key 'custom_code', decodes it, and returns it as a string.
     *
     * @return string Decoded custom code to be injected in the comments section.
     */
    public function customCodeHeader()
    {
        $custom = $this->getConfigs()['custom_code_header'];

        return base64_decode($custom);
    }

    /**
     * Returns the decoded custom footer code configured for comments.
     *
     * This method retrieves the Base64-encoded footer code stored in the configuration
     * under the key 'custom_code_footer', decodes it, and returns it as a string.
     * Typically used for JavaScript or other footer scripts.
     *
     * @return string Decoded custom footer code to be appended at the bottom of the page.
     */
    public function customCodeFooter()
    {
        $customFooter = $this->getConfigs()['custom_code_footer'];

        return base64_decode($customFooter);
    }
}
