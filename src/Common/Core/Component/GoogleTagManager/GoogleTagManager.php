<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\GoogleTagManager;

/**
 * Generates Google tags manager code
 * See more: https://www.google.com/analytics/tag-manager/
 */
class GoogleTagManager
{
    /**
     * Generates Google Tags Manager head code.
     *
     * @param String  $id   The Google Tags Manager id.
     *
     * @return String $code The generated code.
     */
    public function getGoogleTagManagerHeadCode($id)
    {
        $code = "<!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','" . $id . "');</script>
    <!-- End Google Tag Manager -->";

        return $code;
    }

    /**
     * Generates Google Tags Manager body code.
     *
     * @param String  $id   The Google Tags Manager id.
     *
     * @return String $code The generated code.
     */
    public function getGoogleTagManagerBodyCode($id)
    {
        $code = '<!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $id . '"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->';

        return $code;
    }

    /**
     * Generates Google Tags Manager body code for AMP.
     *
     * @param String  $id   The Google Tags Manager id.
     *
     * @return String $code The generated code.
     */
    public function getGoogleTagManagerBodyCodeAMP($id)
    {
        $code = '<!-- Google Tag Manager AMP -->
    <amp-analytics config="https://www.googletagmanager.com/amp.json?id=' . $id
        . '&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>
    <!-- End Google Tag Manager AMP -->';

        return $code;
    }
}
