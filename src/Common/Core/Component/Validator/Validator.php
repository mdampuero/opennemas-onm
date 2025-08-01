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

use Symfony\Component\Validator\Constraints as BaseConstraints;
use Common\Core\Component\Validator\Constraints as Constraints;

class Validator
{
    // List of ruleset names
    const BLACKLIST_RULESET_COMMENTS = 'comment';
    const BLACKLIST_RULESET_TAGS     = 'tag';
    const BLACKLIST_RULESET_LETTERS  = 'letter';

    /**
     * The settings dataset
     *
     * @var DataSet
     **/
    private $ds = null;

    /**
     * The validator service
     *
     * @var \Symfony\Component\Validator\ConstraintValidator
     **/
    private $validator = null;

    /**
     * Initializes the validator object with dependencies
     *
     * @param DataSet    $settingsManager the settings repository object
     * @param Validator  $validator       the validator service
     */
    public function __construct($em, $validator, $commentHelper)
    {
        $this->ds            = $em->getDataSet('Settings', 'instance');
        $this->validator     = $validator;
        $this->commentHelper = $commentHelper;
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
                if ($el->getCode() !== Constraints\BlackListWords::BLACKLIST_WORD_ERROR) {
                    $type = 'fatal';
                }

                $errors[] = $el->getMessage();
            }

            return [
                'type'   => $type,
                'errors' => $errors
            ];
        }

        return [];
    }

    /**
     * Returns the blacklist config for a given ruleSet
     *
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
     * Get the constrains for comments.
     *
     * @return BaseConstraints\Collection BaseConstraints collection for comments.
     */
    protected function getCommentConstraint($data)
    {
        $config = $this->getConfig(self::BLACKLIST_RULESET_COMMENTS);

        $blacklist   = [];
        $constraints = [
            'author' => [
                new BaseConstraints\NotBlank([
                    'message' => _('Please provide a valid author name')
                ]),
            ],
            'author_ip' => new BaseConstraints\NotBlank([
                'message' => _('Your IP address is not valid.')
            ]),
            'body' => [
                new BaseConstraints\Length([
                    'min'        => 5,
                    'minMessage' => _('Your comment is too short')
                ]),
            ],
            'content_id' => new BaseConstraints\Range(['min' => 1]),
        ];

        if ($this->commentHelper->isEmailRequired()) {
            $constraints['author_email'][] = new BaseConstraints\NotBlank([
                'message' => _('Please provide a valid email address')
            ]);
        }

        // Check constraints for author email
        if (!empty($data['author_email'])) {
            $constraints['author_email'][] = new BaseConstraints\Email([
                'message' => _('Please provide a valid email address')
            ]);

            $blacklist['author_email'][] = new Constraints\BlackListWords([
                'words'   => $config,
                'message' => _('Your email is not allowed')
            ]);
        }

        // Only with automatic moderation
        if (!$this->commentHelper->moderateManually()) {
            $blacklist['author'][] = new Constraints\BlackListWords([
                'words'   => $config,
                'message' => _('Your name has invalid words')
            ]);

            $blacklist['body'][] = new Constraints\BlackListWords([
                'words'   => $config,
                'message' => _('Your comment has words under discussion.')
            ]);
        }

        $constraints = array_merge_recursive($constraints, $blacklist);

        return new BaseConstraints\Collection($constraints);
    }

    /**
     * Get the constrains for letters.
     *
     * @return BaseConstraints\Collection BaseConstraints collection for comments.
     */
    protected function getLetterConstraint($data)
    {
        $constraints = [
            'email'  => [
                new BaseConstraints\NotBlank([
                    'message' => _('Please provide a valid email address')
                ]),
            ],
            'lettertext' => [
                new BaseConstraints\Length([
                    'min'        => 25,
                    'minMessage' => _('Your letter is too short')
                ]),
            ],
            'name' => [
                new BaseConstraints\NotBlank([
                    'message' => _('Please provide a valid author name')
                ]),
            ],
            'subject' => [
                new BaseConstraints\NotBlank([
                    'message' => _('Please provide a valid subject')
                ]),
            ]
        ];

        // Check constraints for author email
        if (!empty($data['email'])) {
            $constraints['email'][] = new BaseConstraints\Email([
                'message' => _('Please provide a valid email address')
            ]);
        }

        return new BaseConstraints\Collection($constraints);
    }

    /**
     * Get the constrains for tags.
     *
     * @return Collection BaseConstraints collection for tags.
     */
    protected function getTagConstraint()
    {
        $config = $this->getConfig(self::BLACKLIST_RULESET_TAGS);

        return new BaseConstraints\Collection([
            'name' => [
                new BaseConstraints\NotBlank([
                    'message' => _('Please provide a valid tag')
                ]),
                new Constraints\BlackListWords([
                    'words'   => $config,
                    'message' => _('Your tag has invalid words')
                ]),
                new BaseConstraints\Length([
                    'min'        => 2,
                    'minMessage' => _('Your tag is too short')
                ])
            ]
        ]);
    }
}
