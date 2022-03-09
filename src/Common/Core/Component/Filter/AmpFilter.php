<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Filter\Filter;

class AmpFilter extends Filter
{
    /**
     * Converts a regular HTML string to AMP-valid HTML string.
     *
     * @param string $str The regular HTML string.
     *
     * @return string The AMP-valid HTML string.
     */
    public function filter($str)
    {
        if (!is_string($str)) {
            return;
        }

        $patterns = [
            // Invalid properties
            '@(align|border|style|nowrap|onclick)=(\'|").*?(\'|")@',
            '@<\/?font.*?>@',
            '@target="(.*?)"@',
            '@<a\s+[^>]*href\s*=\s*"([^"]+)"[^>]*>@',

            // Transformed tags
            '@<video([^>]+>)(?s)(.*?)<\/video>@',
            '@<audio([^>]+>)(?s)(.*?)<\/audio>@',
            '@<iframe.*src="[http:|https:]*(.*?)".*?>(?s).*?<\/iframe>@',
            '@<div.*?class="fb-(post|video)".*?data-href="([^"]+)".*?>(?s).*?<\/div>@',
            '@<blockquote.*?class="instagram-media"(?s).*?href=".*?(\.com|\.am)\/p\/(.*?)?\/.*?>(?s).*?<\/blockquote>@',
            '@<blockquote.*?class="twitter-(video|tweet)"(?s).*?\/status\/(\d+)(?s).+?<\/blockquote>@',
            '@<blockquote class="tiktok-embed" cite="(.*?)"((.*)(\n)?)*?</blockquote>@',
            '@<div[^>]*class=\"brid\"[^>]*>.*?/partners/([0-9]+).*</div>[^>]*?'
                . '<script.*"id":"([0-9]+)".*"video":"([0-9]+)".*</script>@',
            '@<(figcaption|table|tbody|blockquote|th|tr|td|ul|li|ol|dl|p|strong|br|span|b|pre|hr|col|h1|h2|h3|h4|h5'
                . '|h6|div).*(?:(?:( id=".*[^"]")|( class=".*[^"]")).*){0,2}(\/?)>@U',

            // Invalid tags
            '@<object[^>]*>(?s).*?<\/object>@',
            '@<(script|embed|object|frameset|iframe|frame|style|form)[^>]*>(?s).*?<\/\1>@i',
            '@<(link|meta|input)[^>]+>@i',

            // Clean attributes
            '@<img\s+[^>]*src\s*=\s*"([^"]+)"[^>]*>@',
        ];

        $replacements = [
            // Invalid properties
            '',
            '',
            'target="_blank"',
            '<a href="${1}">',

            // Transformed tags
            '<amp-video layout="responsive" width="518" height="291" controls>${2}'
                . '<div fallback><p>This browser does not support the video element.</p></div></amp-video>',
            '<amp-audio width="auto" height="50">${2}'
                . '<div fallback><p>This browser does not support the audio element.</p></div></amp-audio>',
            '<amp-iframe width=518 height=291'
                . ' sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-forms"'
                . ' layout="responsive" frameborder="0" src="https:${1}">'
                . ' <amp-img layout="fill" src="/assets/images/lazy-bg.png" placeholder></amp-img></amp-iframe>',
            '<amp-facebook width=486 height=657 layout="responsive" '
                . 'data-embed-as="${1}" data-href="${2}"></amp-facebook>',
            '<amp-instagram data-shortcode="${2}" width="400" height="400" layout="responsive"></amp-instagram>',
            '<amp-twitter width=486 height=657 layout="responsive" data-tweetid="${2}"></amp-twitter>',
            '<amp-tiktok width=486 height=657 layout="responsive" data-src="${1}"></amp-tiktok>',
            '<amp-brid-player autoplay data-partner="${1}" data-player="${2}" data-video="${3}"'
                . ' layout="responsive" width="518" height="291"></amp-brid-player>',
            '<${1}${2}${3}${4}>',

            // Invalid tags
            '',
            '',
            '',

            // Clean attributes
            '<div class="fixed-container"><amp-img class="cover" layout="fill" src="${1}"></amp-img></div>',
        ];

        return preg_replace($patterns, $replacements, $str);
    }
}
