<?php

namespace Common\Core\Component\Helper;

use Common\Core\Component\Template\Template;
use Common\Model\Entity\Content;
use Symfony\Component\Mime\MimeTypes;
use Opennemas\Data\Filter\FilterManager;
use Symfony\Component\Process\Process;
use Common\Core\Component\Loader\InstanceLoader;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Helper class to retrieve video data.
 */

class VideoHelper extends FileHelper
{
    /**
     * The Filesystem component.
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * The InstanceLoader service.
     *
     * @var InstanceLoader
     */
    protected $loader;

    /**
     * The server public directory.
     *
     * @var string
     */
    protected $publicDir;

    /**
     * The content helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * The related helper.
     *
     * @var RelatedHelper
     */
    protected $relatedHelper;

    /**
     * The data filter.
     *
     * @var FilterManager
     */
    protected $filter;

    /**
     * The admin template.
     *
     * @var Template
     */
    protected $template;

    /**
     * Initializes the video helper
     *
     * @param ContentHelper $contentHelper The content helper.
     * @param RelatedHelper $relatedHelper The related helper.
     * @param Template      $template      The admin template.
     * @param FilterManager $filter        The data filter.
     */
    public function __construct(
        ContentHelper $contentHelper,
        RelatedHelper $relatedHelper,
        Template $template,
        FilterManager $filter,
        InstanceLoader $loader,
        string $publicDir
    ) {
        $this->fs            = new Filesystem();
        $this->contentHelper = $contentHelper;
        $this->relatedHelper = $relatedHelper;
        $this->filter        = $filter;
        $this->template      = $template;
        $this->loader        = $loader;
        $this->publicDir     = $publicDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPathForFile()
    {
        return $this->loader->getInstance()->getVideosShortPath();
    }

    /**
     * Returns the path where the file should be moved.
     *
     * @param \SplFileInfo     $file The file to generate path to.
     * @param DateTime $date The date to generate the path from.
     *
     * @return string The path where the file should be moved.
     */
    public function generatePath(\SplFileInfo $file, \DateTime $date): string
    {
        return preg_replace('/\/+/', '/', sprintf(
            '%s/%s/%s/%s%s.%s',
            $this->publicDir,
            $this->getPathForFile(),
            $date->format('Y/m/d'),
            $date->format('YmdHis'),
            str_pad(substr(gettimeofday()['usec'], 0, 5), 5, '0'),
            $this->getExtension($file)
        ));
    }

    /**
     * @codeCoverageIgnore
     *
     * Returns the extension for a file.
     *
     * @param \SplFileInfo $file The file to return extension for.
     *
     * @return string The file extension.
     */
    public function getExtension(\SplFileInfo $file): string
    {
        $mimeType   = (new MimeTypes())->guessMimeType($file->getRealPath());
        $extensions = (new MimeTypes())->getExtensions($mimeType);
        return $extensions[0] ?? '';
    }

    /**
     * Returns the video embed html.
     *
     * @param Content $item The item to get embed html for.
     *
     * @return string The embed html.
     */
    public function getVideoEmbedHtml($item)
    {
        $information = $this->contentHelper->getProperty($item, 'information');

        return !empty($information['embedHTML']) ? $information['embedHTML'] : null;
    }

    /**
     * Returns the video embed url.
     *
     * @param Content $item The item to get embed url for.
     *
     * @return string The embed url.
     */
    public function getVideoEmbedUrl($item)
    {
        $information = $this->contentHelper->getProperty($item, 'information');

        return !empty($information['embedUrl']) ? $information['embedUrl'] : null;
    }

    /**
     * Returns the video html.
     *
     * @param Content $item The item to get html for.
     *
     * @return string The html code.
     */
    public function getVideoHtml($item, $width = null, $height = null, $amp = false)
    {
        $width  = $width ?? '560';
        $height = $height ?? '320';
        $output = sprintf('<div>%s</div>', $item->body);

        if ($item->type !== 'script') {
            $tpl  = 'video/render/web-source.tpl';
            $info = $item->information;
            if (!empty($item->information['source'])) {
                $tpl            = 'video/render/external.tpl';
                $info['source'] = array_filter($item->information['source']);
            }

            $output = $this->template->fetch($tpl, [
                'isAmp'  => $amp,
                'title'  => $item->title,
                'info'   => $info,
                'height' => $height,
                'width'  => $width,
                'type'   => in_array(strtolower($item->type), ['youtube', 'vimeo']) ? strtolower($item->type) : ''
            ]);
        }

        if ($amp) {
            $output = $this->filter->set($output)->filter('amp')->get();
        }

        return $output;
    }

    /**
     * Returns the type for the provided item.
     *
     * @param Content $item The item to get type for.
     *
     * @return string The content type.
     */
    public function getVideoType($item)
    {
        $value = $this->contentHelper->getProperty($item, 'type');

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the path for the provided item.
     *
     * @param Content $item The item to get path for.
     *
     * @return string The content path.
     */
    public function getVideoPath($item)
    {
        $value = $this->contentHelper->getProperty($item, 'path');

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the thumbnail for the provided item.
     *
     * @param Content $item The item to get thumbnail from.
     *
     * @return mixed The thumbnail id or the thumbnail string.
     */
    public function getVideoThumbnail($item)
    {
        $related = $this->relatedHelper->getRelated($item, 'featured_frontpage');

        if (!empty($related)) {
            $thumbnail = array_shift($related);
            return $this->contentHelper->getContent($thumbnail);
        }

        if (empty($item->information)
            || !array_key_exists('thumbnail', $item->information)
        ) {
            return null;
        }

        return new Content([
            'content_status'    => 1,
            'content_type_name' => 'photo',
            'description'       => $item->title,
            'external_uri'      => $item->information['thumbnail']
        ]);
    }

    /**
     * Returns if the video has embed html or not.
     *
     * @param Content $item The item to check if has embed html or not.
     *
     * @return boolean true if has embed html.
     */
    public function hasVideoEmbedHtml($item)
    {
        $value = $this->getVideoEmbedHtml($item);

        return !empty($value);
    }

    /**
     * Returns if the video has embed url or not.
     *
     * @param Content $item The item to check if has embed url or not.
     *
     * @return boolean true if has embed url.
     */
    public function hasVideoEmbedUrl($item)
    {
        $value = $this->getVideoEmbedUrl($item);

        return !empty($value);
    }

    /**
     * Returns if the video has path or not.
     *
     * @param Content $item The item to check if has path or not.
     *
     * @return boolean true if has path.
     */
    public function hasVideoPath($item)
    {
        $value = $this->getVideoPath($item);

        return !empty($value);
    }

    /**
     * Moves the file to the target path.
     *
     * @param \SplFileInfo   $file   The file to move.
     * @param string $target The path where file will be moved.
     * @param bool   $copy   Whether to copy the file.
     *
     * @return string The target path.
     */
    public function move(\SplFileInfo $file, string $target, bool $copy = false): void
    {
        $name      = basename($target);
        $directory = str_replace($name, '', $target);

        if ($copy) {
            $this->fs->copy($file->getRealPath(), $target);
            return;
        }

        $file->move($directory, $name);
    }

    /**
     * @codeCoverageIgnore
     *
     * Removes a file basing on the path.
     *
     * @param string $path The path to the file to remove.
     */
    public function remove(string $path): void
    {
        $path = preg_replace('/\/+/', '/', sprintf(
            '%s/%s/%s',
            $this->publicDir,
            $this->getPathForFile(),
            $path
        ));

        if ($this->fs->exists($path) && is_file($path)) {
            $this->fs->remove($path);
        }

        /**
         * Calls the console command to upload the file to the storage.
         */
        $process = new Process(
            sprintf(
                '/home/opennemas/current/bin/console app:core:storage --operation=delete --path=%s',
                str_replace($this->publicDir, '', $path)
            )
        );
        $process->start();
    }
}
