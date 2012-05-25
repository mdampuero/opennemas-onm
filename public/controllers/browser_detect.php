<?php
/**
 * browser_detect.php
 *
 * Check browser and route the request to /mobile/index.php if
 * isMobileDevice is detected
 *
 * @link http://code.google.com/p/phpbrowscap/wiki/QuickStart Documentation phpbrowscap
 * @deprecated 0.6-RC1 Use Application::mobileRouter()
 */

// Creates a new Browscap object (loads or creates the cache)
$bc = new Browscap( dirname(__FILE__) . '/cache');
$browser = $bc->getBrowser();

if (!empty($browser->isMobileDevice) && ($browser->isMobileDevice == 1) && !(isset($_COOKIE['confirm_mobile']))) {
    Application::forward('/mobile' . $_SERVER['REQUEST_URI'] );
}

if ($browser->isBanned) {
    Application::forward('robots.html');
}

unset($broser);
unset($bc);
