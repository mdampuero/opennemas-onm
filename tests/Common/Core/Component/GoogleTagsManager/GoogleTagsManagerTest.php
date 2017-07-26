<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\GoogleTagsManager;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Common\Core\Component\GoogleTagsManager\GoogleTagsManager;

/**
 * Defines test cases for GoogleTagsManager class.
 */
class GoogleTagsManagerTest extends KernelTestCase
{
    public function setUp()
    {
        $this->id = "GMT-0000000";

        $this->object =  new GoogleTagsManager();
    }
    /**
     * Generates Google Tags Manager head code
     *
     * @return String the generated code
     */
    public function testGetGoogleTagsManagerHeadCode()
    {
        $code = "<!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','" . $this->id . "');</script>
    <!-- End Google Tag Manager -->";

        $this->assertEquals($code, $this->object->getGoogleTagsManagerHeadCode($this->id));
    }

    /**
     * Generates Google Tags Manager body code
     *
     * @return String the generated code
     */
    public function testGetGoogleTagsManagerBodyCode()
    {
        $code = '<!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $this->id . '"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->';

        $this->assertEquals($code, $this->object->getGoogleTagsManagerBodyCode($this->id));
    }
}
