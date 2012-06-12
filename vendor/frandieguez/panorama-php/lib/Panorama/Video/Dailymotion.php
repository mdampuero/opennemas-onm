<?php
/**
 *  Copyright (C) 2011 by OpenHost S.L.
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 **/
/**
 * Wrapper class for Dailymotion videos
 *
 * @author Fran Diéguez <fran@openhost.es>
 * @version \$Id\$
 * @copyright OpenHost S.L., Mér Xuñ 01 15:58:58 2011
 * @package Panorama\Video
 **/
namespace Panorama\Video;

class Dailymotion implements VideoInterface {


    /*
     * Initializes the Dailymotion video wrapper from one url
     *
     * @return null
     */
    public function __construct($url, $options = null)
    {
        $this->url = $url;
    }

    /*
     * Returns the page content for this video
     *
     * @return simplexmlobject The SimpleXML object for the web page XML
     */
    public function getPage()
    {
        if (!isset($this->page)) {
            $videoId = $this->getVideoID();
            $content = file_get_contents("http://www.dailymotion.com/rss/video/{$videoId}");
            $this->page = simplexml_load_string($content);
        }
        return $this->page;
    }

    /*
     * Returns the title for this Dailymotion video
     *
     * @return string the title for this video
     */
    public function getTitle()
    {
        if (!isset($this->title)) {
            $titles = $this->getPage()->xpath('//item/title');
            $this->title = (string) $titles[0];
        }
        return $this->title;
    }

    /*
     * Returns the thumbnail for this Dailymotion video
     *
     * @return string the url to the thumbnail for this video
     */
    public function getThumbnail()
    {

        if (!isset($this->thumbnail)) {
            $thumbnail = $this->getPage()->xpath('//media:thumbnail');
            $this->thumbnail = preg_replace('@preview_large@', 'preview_medium', $thumbnail[0]["url"]);
        }
        return $this->thumbnail;

    }

    /*
     * Returns the duration in secs for this Dailymotion video
     *
     * @return null this service doesn't allow to get duration of the video
     */
    public function getDuration()
    {
        return null;
    }

    /*
     * Returns the embed url for this Dailymotion video
     *
     * @return string the url to the embedurl of this video
     */
    public function getEmbedUrl()
    {
        if (!isset($this->embedUrl)) {
            $embed = $this->getPage()->xpath("//media:content[@type='application/x-shockwave-flash']");
            $this->embedUrl = (string) $embed[0]["url"];
        }
        return $this->embedUrl;
    }

    /*
     * Returns the HTML object to embed for this Dailymotion video
     *
     * @return string the html object to embed for this video
     */
    public function getEmbedHTML($options = array())
    {
        if (!isset($this->embedHTML)) {
            $defaultOptions = array(
                  'width' => 560,
                  'height' => 349
                  );

            $options = array_merge($defaultOptions, $options);
            unset($options['width']);
            unset($options['height']);

            // convert options into and url encoded vars
            $htmlOptions = "";
            if (count($options) > 0) {
                foreach ($options as $key => $value ) {
                    $htmlOptions .= "&" . $key . "=" . $value;
                }
            }

            $this->embedHTML =
                "<object width='{$defaultOptions['width']}' height='{$defaultOptions['height']}'>\n"
                ."<param name='movie' value='{$this->getEmbedUrl()}&related=1'></param>\n"
                ."<param name='allowFullScreen' value='true'></param>\n"
                ."<param name='allowScriptAccess' value='always'></param>\n"
                ."<embed type='application/x-shockwave-flash'\n"
                ."src='{$this->getEmbedUrl()}&related=1'\n"
                ."width='{$defaultOptions['width']}' height='{$defaultOptions['height']}'\n"
                ."allowFullScreen='true' allowScriptAccess='always'>\n"
                ."</embed>\n"
                ."</object>";
        }
        return $this->embedHTML;

    }

    /*
     * Returns the FLV url for this Dailymotion video
     *
     * @return string the url for downloading the video in FLV format
     */
    public function getFLV()
    {
        if (!isset($this->FLV)) {
            $item = $this->getPage()->xpath('//media:content');
            $this->FLV = (string) $item[0]["url"];
        }
        return $this->FLV;
    }

    /*
     * Returns the Download url for this Dailymotion video
     *
     * @return null not implemented for this video service
     */
    public function getDownloadUrl()
    {
        return null;
    }

    /*
     * Returns the name of the Video service
     *
     * @return string the name of this video service
     */
    public function getService()
    {
        return "Dailymotion";
    }

    /*
     * Calculates the Video ID from an Dailymotion URL
     *
     * @return string the video ID for this video
     * @throws Exception if url doesn't seem to be a Dailymotion video url
     */
    public function getVideoID()
    {

        if (!isset($this->videoId)) {
            $urlParts = parse_url($this->url);
            $matches = preg_split("@/video/@", $urlParts['path']);
            if (count($matches) > 0) {
                $this->videoId = $matches[1];
            } else {
                throw new \Exception("This url doesn't seem to be a Dailymotion video url");
            }
        }
        return $this->videoId;

    }
}
