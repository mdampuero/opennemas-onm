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
    const BLACKLIST_RULESET_TAGS     = 'tag';

    /**
     * The settings dataset
     *
     * @var DataSet
     **/
    private $ds = null;

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
    public function __construct($em, $validator)
    {
        $this->ds        = $em->getDataSet('Settings', 'instance');
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
        $methodName   = 'get' . ucfirst($ruleName) . 'Constraint';

        if (!$validRuleSet || !method_exists($this, $methodName)) {
            throw new \InvalidArgumentException(sprintf(
                "The ruleset '%s' is not valid",
                $ruleName
            ));
        }

        $violations = $this->validator->validate(
            $entity,
            $this->{$methodName}($entity) //getting the constrains to validate
        );

        if (!empty($violations) && count($violations) > 0) {
            $errors = [];

            $type = 'normal';
            foreach ($violations as $el) {
                if ($el->getCode() !== OnmAssert\BlacklistWords::BLACKLIST_WORD_ERROR) {
                    $type = 'fatal';
                }

                $errors[] = $el->getMessage();
            }

            return [
                'type' => $type,
                'errors' => $errors
            ];
        }

        return [];



        foreach ($violations as $el) {
            $errors[] = $el->getMessage();
        }

        return $errors;
    }

    /**
     * Returns the blacklist config for a given ruleSet
     *
     * @return void
     **/
    public function getConfig($ruleSet)
    {
        return $this->ds->get('blacklist.' . $ruleSet);
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
        try {
            $this->ds->set('blacklist.' . $ruleSet, $config);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the constrains for comments
     *
     * @return Collection Assert collection for comments
     **/
    private function getCommentConstraint($data)
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

        return new Assert\Collection($constraintMap);
    }


    /**
     * Get the constrains for tags
     *
     * @return Collection Assert collection for tags
     **/
    private function getTagConstraint($data)
    {
        $config = $this->getConfig(self::BLACKLIST_RULESET_TAGS);

        return new Assert\Collection([
            'name' => [
                new Assert\NotBlank([
                    'message' => _('Please provide a valid tag')
                ]),
                new OnmAssert\BlacklistWords([
                    'words'   => $config,
                    'message' => _('Your tag has invalid words')
                ]),
                new Assert\Length([
                    'min'        => 2,
                    'minMessage' => _('Your tag is too short')
                ])
            ]
        ]);
    }
}
