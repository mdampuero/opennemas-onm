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

class NormalizeFilter extends Filter
{
    /**
     * The array of replacements.
     *
     * @var array
     */
    protected $trade = [
        'á'=>'a', 'à'=>'a', 'ã'=>'a', 'ä'=>'a', 'â'=>'a', 'Á'=>'A',
        'À'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Â'=>'A', 'é'=>'e', 'è'=>'e',
        'ë'=>'e', 'ê'=>'e', 'É'=>'E', 'È'=>'E', 'Ë'=>'E', 'Ê'=>'E',
        'í'=>'i', 'ì'=>'i', 'ï'=>'i', 'î'=>'i', 'Í'=>'I', 'Ì'=>'I',
        'Ï'=>'I', 'Î'=>'I', 'ó'=>'o', 'ò'=>'o', 'õ'=>'o', 'ö'=>'o',
        'ô'=>'o', 'Ó'=>'O', 'Ò'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ô'=>'O',
        'ú'=>'u', 'ù'=>'u', 'ü'=>'u', 'û'=>'u', 'Ú'=>'U', 'Ù'=>'U',
        'Ü'=>'U', 'Û'=>'U', '$'=>'',  '@'=>'',  '!'=>'',  '#'=>'',
        '%'=>'',  '^'=>'',  '&'=>'',  '*'=>'',  '('=>'',  ')'=>'',
        '-'=>'-', '+'=>'',  '='=>'',  '\\'=>'-', '|'=>'-', '`'=>'',
        '~'=>'',  '/'=>'-', '\"'=>'', '\''=>'', '<'=>'',  '>'=>'',
        '?'=>'-', ','=>'-', 'ç'=>'c', 'Ç'=>'C',  '·'=>'', 'ª'=>'',
        'º'=>'', ';'=>'-', '['=>'-', ']'=>'-', 'ñ'=>'n', 'Ñ'=>'N',
        ':'=>'',
    ];


    /**
     * Returns the slugified string.
     *
     * @param string $str The string to filter.
     *
     * @return string The slugified string.
     */
    public function filter($str)
    {
        $str = strtr($str, $this->trade);
        $str = mb_strtolower($str, 'UTF-8');
        $str = trim($str);

        $str = preg_replace('/[\- ]+/', '-', $str);

        return $str;
    }
}
