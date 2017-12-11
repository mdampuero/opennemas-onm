<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Filter;

class TagsFilter extends Filter
{
    /**
     * Initializes the TagFilter.
     *
     * @param ServiceContainer $container The service container.
     * @param array            $params    The filter parameters.
     */
    public function __construct($container, $params = [])
    {
        $this->utils = new \Onm\StringUtils;

        parent::__construct($container, $params);
    }

    /**
     * Returns an array of tags extracted from the string.
     *
     * @param string $str The string to filter.
     *
     * @return string The UTF-8 encoded string.
     */
    public function filter($str)
    {
        // Convert to UTF-8
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');

        // ensure that we have utf-8 used
        $beforeEncoding = mb_internal_encoding();
        mb_internal_encoding("utf-8");

        if ($this->getParameter('lowercase', false)) {
            $str = mb_strtolower($str, 'UTF-8');
        }

        // Remove invalid words
        $str = $this->utils->removeShorts($str);

        // Remove duplicates
        $str = preg_replace('/[\,]+/', ',', $str);
        $str = preg_replace('/[\.]+/', '', $str);

        $str = trim($str);
        $str = preg_replace('/[ ]+/', ',', $str);

        $str = array_unique(explode(',', $str));
        $str = implode($this->getParameter('separator', ','), $str);

        // Reset encoding
        mb_internal_encoding($beforeEncoding);

        return $str;
    }

    /**
     * Returns a list of words tokenized for Hiragana and Katagana.
     *
     * @return array The list of words.
     */
    protected function tokenizeJapannese($string)
    {
        $hiraganaTokens = [
            'あ', 'い', 'う', 'え', 'お', 'か', 'き', 'く', 'け', 'こ', 'さ',
            'し', 'す', 'せ', 'そ', 'た', 'ち', 'つ', 'て', 'と', 'な', 'に',
            'ぬ', 'ね', 'の', 'は', 'ひ', 'ふ', 'へ', 'ほ', 'ま', 'み', 'む',
            'め', 'も', 'や', 'ゆ', 'よ', 'ら', 'り', 'る', 'れ', 'ろ', 'わ',
            'ゐ', 'ゑ', 'を', 'ん', 'が', 'ぎ', 'ぐ', 'げ', 'ご', 'ざ', 'じ',
            'ず', 'ぜ', 'ぞ', 'だ', 'ぢ', 'づ', 'で', 'ど', 'ば', 'び', 'ぶ',
            'べ', 'ぼ', 'ぱ', 'ぴ', 'ぷ', 'ぺ', 'ぽ', 'ぁ', 'ぃ', 'ぅ', 'ぇ',
            'ぉ',
        ];

        $katakanaTokens = [
            'ア', 'イ', 'ウ', 'エ', 'オ', 'カ', 'キ', 'ク', 'ケ', 'コ', 'サ',
            'シ', 'ス', 'セ', 'ソ', 'タ', 'チ', 'ツ', 'テ', 'ト', 'ナ', 'ニ',
            'ヌ', 'ネ', 'ノ', 'ハ', 'ヒ', 'フ', 'ヘ', 'ホ', 'マ', 'ミ', 'ム',
            'メ', 'モ', 'ヤ', 'ユ', 'ヨ', 'ラ', 'リ', 'ル', 'レ', 'ロ', 'ワ',
            'ヰ', 'ヱ', 'ヲ', 'ン', 'ガ', 'ギ', 'グ', 'ゲ', 'ゴ', 'ザ', 'ジ',
            'ズ', 'ゼ', 'ゾ', 'ダ', 'ヂ', 'ヅ', 'デ', 'ド', 'バ', 'ビ', 'ブ',
            'ベ', 'ボ', 'パ', 'ピ', 'プ', 'ペ', 'ポ', 'ァ', 'ィ', 'ゥ', 'ェ',
            'ォ', 'ー',
        ];

        $hiraganaStart = 0x1;
        $katakanaStart = 0x2;
        $kanjiStart    = 0x4;

        $tokens = [];

        $currentSystem = null;
        $currentToken  = '';

        for ($i = 0; $i <= mb_strlen($string); $i++) {
            $character = mb_substr($string, $i, 1);

            if (in_array($character, $hiraganaTokens)) {
                $system = $hiraganaStart;
            } elseif (in_array($character, $katakanaTokens)) {
                $system = $katakanaStart;
            } else {
                $system = $kanjiStart;
            }

            // First string did not have a starting system
            if ($currentSystem == null) {
                $currentSystem = $system;
            }

            // if the system still is the same, no boundary has been reached
            if ($currentSystem == $system) {
                $currentToken .= $character;
            } else {
                // Write ended token to tokens and start a new one
                $tokens[]      = $currentToken;
                $currentToken  = $character;
                $currentSystem = $system;
            }
        }

        return $tokens;
    }
}
