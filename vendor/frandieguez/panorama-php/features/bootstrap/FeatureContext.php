<?php
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{

    public $url =  '';
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^The url (.*)$/
     */
    public function theUrl($url)
    {
        $this->url = $url;
        $this->videoService = new \Panorama\Video($this->url);
    }

    /**
     * @When /^I get the (.*)$/
     */
    public function iGetTheProperty($propertyName)
    {
        switch ($propertyName) {
            case 'service name':
                $this->value = $this->videoService->getService();
                break;

            case 'download url':
                $this->value = $this->videoService->getDownloadUrl();
                break;

            case 'title':
                $this->value = $this->videoService->getTitle();
                break;

            case 'thumbnail':
                $this->value = $this->videoService->getThumbnail();
                break;

            case 'duration':
                $this->value = $this->videoService->getDuration();
                break;

            case 'embed url':
                $this->value = $this->videoService->getEmbedUrl();
                break;

            case 'embed HTML':
            case 'embedHTML':
                $this->value = $this->videoService->getEmbedHTML(array());
                break;

            case 'video id':
                $this->value = $this->videoService->getVideoID();
                break;

            case 'FLV url':
                $this->value = $this->videoService->getFLV();
                break;
        }
    }

    /**
     * @Then /^The result should be "([^"]*)"$/
     */
    public function theResultShouldBe($value)
    {
        assertEquals($value, $this->value);
    }

    /**
     * @Then /^The result should be like "([^"]*)"$/
     */
    public function theResultShouldBeLike($value)
    {
        assertTrue((bool)preg_match($value, $this->value));
    }

    /**
     * @Then /^The result should be:$/
     */
    public function theResultShouldBeAnString(PyStringNode $string)
    {
        assertEquals($string, $this->value);
    }

    /**
     * @Then /^The result should be like:$/
     */
    public function theResultShouldBeLikeAnString(PyStringNode $string)
    {
        assertEquals($string, $this->value);
    }

}
