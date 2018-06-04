<?php
/**
 * This file is part of the Onm package.
 *
 * (c) OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Common\Core\Component\Validator\Constraints as OnmAssert;

class Validator
{
    // List of ruleset names
    const BLACKLIST_RULESET_COMMENTS = 'comment';

    /**
     * The settings repository
     *
     * @var SettingRepository
     **/
    private $sm = null;

    /**
     * The validator service
     *
     * @var Symfony\Component\Validator
     **/
    private $validator = null;

    /**
     * Initializes the validator object with dependencies
     *
     * @param SettingsRepository $settingsManager the settings repository object
     * @param Validator          $validator       the validator service
     *
     * @return void
     */
    public function __construct($settingsManager, $validator)
    {
        $this->sm        = $settingsManager;
        $this->validator = $validator;
    }

    /**
     * Validates an array or an object
     *
     * @param array|object $entity the data to validate
     * @param string       $paramname description
     *
     * @return mixed the array of violations
     **/
    public function validate($entity, $ruleName)
    {
        $classReflection = new \ReflectionClass(__CLASS__);
        $constants       = $classReflection->getConstants();

        $validRuleSet = in_array($ruleName, $constants);
        $methodName   = 'validate' . ucfirst($ruleName);
        if ($validRuleSet && method_exists($this, $methodName)) {
            return $this->{$methodName}($entity);
        }

        throw new \InvalidArgumentException(sprintf(
            "The ruleset '%s' is not valid",
            $ruleName
        ));
    }

    /**
     * Returns the blacklist config for a given ruleSet
     *
     * @return void
     **/
    public function getConfig($ruleSet)
    {
        return $this->sm->get('blacklist.' . $ruleSet);
    }

    /**
     * Saves the blacklist config for a given ruleSet
     *
     * @param string $ruleSet the name of the rule set
     * @param mixed  $config  the configuration to save
     *
     * @return void
     **/
    public function setConfig($ruleSet, $config)
    {
        return $this->sm->set('blacklist.' . $ruleSet, $config);
    }

    /**
     * Validates comments given an array of data
     *
     * @param array $data the comment data
     *
     * @return array the list of violations
     **/
    private function validateComment($data)
    {
        $config = $this->getConfig(self::BLACKLIST_RULESET_COMMENTS);

        $constraintMap = [
            'author' => [
                new Assert\NotBlank([
                    'message' => _('Please provide a valid author name')
                ]),
                new OnmAssert\BlacklistWords([
                    'words'   => $config,
                    'message' => _('Your name has invalid words')
                ]),
            ],
            'author_ip'    => new Assert\NotBlank([
                'message' => _('Your IP address is not valid.')
            ]),
            'author_email' => new Assert\Blank(),
            'body'         => [
                new Assert\Length([
                    'min'        => 5,
                    'minMessage' => _('Your comment is too short')
                ]),
                new OnmAssert\BlacklistWords([
                    'words'   => $config,
                    'message' => _('Your comment has invalid words')
                ]),
            ],
            'content_id' => new Assert\Range(['min' => 1]),
        ];

        if (array_key_exists('author_email', $data) && !empty($data['author_email'])) {
            $constraintMap['author_email'] = [
                new Assert\Email([
                    'message' => _('Please provide a valid email address')
                ]),
                new OnmAssert\BlacklistWords([
                    'words'   => $config,
                    'message' => _('Your email is not allowed')
                ]),
            ];
        }

        $constraint = new Assert\Collection($constraintMap);

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
}
