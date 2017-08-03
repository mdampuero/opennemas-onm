<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Translator;

/**
 *  Handles the actions for the translations
 */
class Translate
{

    /**
     * Initializes the Translator.
     */
    public function __construct()
    {
        $this->translatorFactory   = new TranslatorFactory();
    }

    /**
     * Returns a list of available translators.
     *
     * @return array A list of available translators.
     */
    public function getAvailableTranslators()
    {
        return $this->translatorFactory->getAvailableTranslators();
    }

    /**
     * Returns a list with all translator data.
     *
     * @return array A list of translator data
     */
    public function getTranslatorsData()
    {
        $translationDataArr = array_map(function ($translator) {
            return [
                'translator' => $translator,
                'required_parameters' => $this->translatorFactory->
                    get($translator)->getRequiredParameters()];
        }, $this->getAvailableTranslators());
        return $translationDataArr;
    }
}
