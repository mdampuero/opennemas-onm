<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Validator\Constraints;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates whether a value is a valid country code.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class BlacklistWordsValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BlacklistWords) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\BlacklistWords');
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;
        $valid = true;
        foreach ($constraint->words as $regexp) {
            $returnValue = preg_match_all('@' . $regexp . '@', $value);

            $value = $value && ($returnValue !== 0);
        }

        if (!$value) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode(BlacklistWords::IS_BLACKLIST_WORD_ERROR)
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode(BlacklistWords::IS_BLACKLIST_WORD_ERROR)
                    ->addViolation();
            }
        }
    }
}
