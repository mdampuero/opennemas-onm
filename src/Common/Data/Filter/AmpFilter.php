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

class AmpFilter extends Filter
{
    /**
     * Converts an old image url into onm format with translation.
     *
     * @param string $str The string to filter.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        if (!is_string($str)) {
            return;
        }

        return $this->filterValue($str);
    }

    /**
     * Returns the provided HTML string filtered with valid AMP markup
     *
     * @param string $str The string to clean
     *
     * @return string
     */
    private function filterValue($str)
    {
        $patterns = [
            '@(align|border|style|nowrap|onclick)=(\'|").*?(\'|")@',
            '@<\/?font.*?>@',
            '@<img\s+[^>]*src\s*=\s*"([^"]+)"[^>]*>@',
            '@<video([^>]+>)(?s)(.*?)<\/video>@',
            '@<iframe.*src="[http:|https:]*(.*?)".*><\/iframe>@',
            '@<div.*?class="fb-(post|video)".*?data-href="([^"]+)".*?>(?s).*?<\/div>@',
            '@<blockquote.*?class="instagram-media"(?s).*?href=".*?'
                . '(\.com|\.am)\/p\/(.*?)\/"[^>]+>(?s).*?<\/blockquote>@',
            '@<blockquote.*?class="twitter-(video|tweet)"(?s).*?\/status\/(\d+)(?s).+?<\/blockquote>@',
            '@<(script|embed|object|frameset|frame|iframe|style|form)[^>]*>(?s).*?<\/\1>@',
            '@<(link|meta|input)[^>]+>@',
            '@<a\s+[^>]*href\s*=\s*"([^"]+)"[^>]*>@',
            '@<(table|tbody|blockquote|th|tr|td|ul|li|ol|dl|p|strong|br|span'
                . '|div|b|pre|hr|col|h1|h2|h3|h4|h5|h6)[^>]*?(\/?)>@',
            '@target="(.*?)"@',
        ];

        $replacements = [
            '',
            '',
            '<amp-img layout="responsive" width="518" height="291" src="${1}"></amp-img>',
            '<amp-video layout="responsive" width="518" height="291" controls>
                ${2}
                <div fallback>
                    <p>This browser does not support the video element.</p>
                </div>
            </amp-video>',
            '<amp-iframe width=518 height=291
                sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-forms"
                layout="responsive"
                frameborder="0"
                src="https:${1}">
            </amp-iframe>',
            '<amp-facebook width=486 height=657
                layout="responsive"
                data-embed-as="${1}"
                data-href="${2}">
            </amp-facebook>',
            '<amp-instagram
                data-shortcode="${2}"
                width="400"
                height="400"
                layout="responsive">
            </amp-instagram>',
            '<amp-twitter width=486 height=657
                layout="responsive"
                data-tweetid="${2}">
            </amp-twitter>',
            '',
            '',
            '<a href="${1}">',
            '<${1}${2}>',
            'target="_blank"',
        ];

        return preg_replace($patterns, $replacements, $str);
    }
}
