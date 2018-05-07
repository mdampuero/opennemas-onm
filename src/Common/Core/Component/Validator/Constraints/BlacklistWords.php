<?php
/*
 * This file is part of the Opennemas package.
 *
 * (c) Opennemas Developers <developers@opennemas.com.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class BlacklistWords extends Constraint
{
    const IS_BLACKLIST_WORD_ERROR = '9f0865f1-8bee-4bc6-b936-15ef0f33ca7c
';

    protected static $errorNames = [
        self::IS_BLACKLIST_WORD_ERROR => 'IS_BLACKLIST_WORD_ERROR',
    ];

    public $message = 'This value contains not allowed words.';

    public $words;

    public function __construct($options = null)
    {
        if (null !== $options && !is_array($options)) {
            $options = [
                'words'   => $options,
                'message' => $options,
            ];
        }

        parent::__construct($options);

        if (null === $this->words && null === $this->max) {
            throw new MissingOptionsException(
                sprintf('The parameter "words" must be given for constraint %s', __CLASS__),
                ['words']
            );
        }
    }
}
