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

use Symfony\Component\Validator\Constraints as Assert;
use Common\Core\Component\Validator\Constraints as OnmAssert;

class CommentHelper
{
    /**
     * The settings manager.
     *
     * @var SettingsManager
     */
    protected $sm;

    /**
     * Initializes the AdvertisementHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($sm, $validator, $defaultConfigs)
    {
        $this->sm             = $sm;
        $this->configs        = $sm->get('comments_config');
        $this->validator      = $validator;
        $this->defaultConfigs = $defaultConfigs;
    }

    public function moderateManually()
    {
        $configs = $this->getConfigs();

        return $configs['moderation_manual'] == 1;
    }

    /**
     * Checks if the content of a comment has bad words.
     *
     * @param array $data The data of the comment.
     *
     * @return integer Higher values means more bad words.
     */
    public static function hasBadWordsComment($string)
    {
        $weight = \Onm\StringUtils::getWeightBadWords($string);

        return $weight > 100;
    }

    /**
     * Validates the comment information against a set of rules
     *
     * @return array the list of errors
     **/
    public function validate($data)
    {
        $constraint = new Assert\Collection([
            'author'       => new Assert\NotBlank([
                'message' => _('Please provide a valid author name.')
            ]),
            'author_email' => new Assert\Email([
                'message' => _('Please provide a valid email address')
            ]),
            'author_ip'    => new Assert\NotBlank([
                'message' => _('Your IP address is not valid.')
            ]),
            'body'         => [
                new Assert\Length([
                    'min'     => 10,
                    'minMessage' => _('Your comment has no enought contents.')
                ]),
                new OnmAssert\BlacklistWords([
                    'words'      => $this->getConfigs()['moderation_blacklist'],
                    'message' => _('Your comment uses words that are not allowed.')
                ]),
            ],
            'content_id'   => new Assert\Range(['min' => 1]),
        ]);

        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $el) {
                $errors[] = $el->getMessage();
            }

            return $errors;
        }

        return [];
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
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getConfigs()
    {
        $configs = array_merge($this->getDefaultConfigs(), $this->configs);

        $configs['moderation_blacklist'] = explode("\n", $configs['moderation_blacklist']);

        return $configs;
    }
}
