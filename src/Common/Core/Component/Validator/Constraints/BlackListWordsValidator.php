<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BlackListWordsValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BlackListWords) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\BlackListWords');
        }

        if (empty($value)) {
            return;
        }

        if (!is_scalar($value)
            && !(is_object($value)
            && method_exists($value, '__toString'))
        ) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        if ($this->match($value, $constraint->words)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(BlackListWords::BLACKLIST_WORD_ERROR)
                ->addViolation();
        }
    }

    /**
     * Whether the provided value matches against the list of regexp.
     *
     * @param string       $value     The value to get matches from.
     * @param array|string $blacklist The list of regexp to validate against.
     *
     * @return boolean True if the value matches against the list of regexp.
     */
    public function match($value, $blacklist)
    {
        if (empty($blacklist)) {
            return false;
        }

        if (!is_array($blacklist)) {
            $blacklist = explode("\n", $blacklist);
        }

        foreach ($blacklist as $word) {
            $word = trim($word);

            if (empty($word)) {
                continue;
            }

            $regexp = $this->parseWord($word);

            if (preg_match_all($regexp, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parses the word and returns the regular expression to apply to the value.
     *
     * @param string $word The word to parse.
     *
     * @return string The regular expression to apply.
     */
    protected function parseWord($word)
    {
        $regexp = '\b' . $word . '\b';

        if (substr($word, 0, 1) === '/' && substr($word, -1) === '/') {
            $regexp = trim($word, '/');
        }

        return '@' . trim(str_replace('@', '\@', $regexp)) . '@mu';
    }
}
