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

        if (empty($value)) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        if ($this->match($value, $constraint->words)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(BlacklistWords::BLACKLIST_WORD_ERROR)
                ->addViolation();
        }
    }

    /**
     * Whether the provided value matches against the list of regexp
     *
     * @param string       $value     the value to get matches from
     * @param array|string $blacklist the list of regexp to validate against
     *
     * @return boolean true if the value matches against the list of regextp
     */
    public function match($value, $blacklist)
    {
        if (empty($blacklist)) {
            return false;
        }

        if (!is_array($blacklist)) {
            $blacklist = explode("\n", $blacklist);
        }

        foreach ($blacklist as $regexp) {
            $regexp = trim($regexp);
            if (empty($regexp)) {
                continue;
            } elseif (substr($regexp, 0, 1) !== '/' ||
                substr($regexp, -1) !== '/'
            ) {
                $regexp = '\b' . $regexp . '\b';
            } elseif (substr($regexp, 0, 1) === '/' &&
                substr($regexp, -1) === '/'
            ) {
                $regexp = substr($regexp, 1, -1);
            }

            $cleanRegexp = '@' . trim(str_replace('@', '\@', $regexp)) . '@m';
            $returnValue = preg_match_all($cleanRegexp, $value);

            if ($returnValue !== false && $returnValue > 0) {
                return true;
            }
        }

        return false;
    }
}
