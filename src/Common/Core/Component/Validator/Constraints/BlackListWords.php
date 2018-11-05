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
class BlackListWords extends Constraint
{
    const BLACKLIST_WORD_ERROR = '9f0865f1-8bee-4bc6-b936-15ef0f33ca7c';

    protected static $errorNames = [
        self::BLACKLIST_WORD_ERROR => 'BLACKLIST_WORD_ERROR',
    ];

    public $message = 'This value contains not allowed words.';

    public $words;

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'words';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return [ 'words' ];
    }
}
