<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Paginator;

class Paginator
{
    /**
     * The configuration options.
     *
     * @var array
     */
    protected $options = [
        'boundary'    => false,
        'directional' => false,
        'epp'         => 10,
        'join'        => '?',
        'maxLinks'    => 5,
        'page'        => 1,
        'total'       => 0,
        'url'         => null
    ];

    /**
     * Returns the HTML for pagination basing on the configuration.
     *
     * @param array $options The configuration options.
     *
     * @return string The HTML for the pagination.
     */
    public function get($options = [])
    {
        $this->options = array_merge($this->options, $options);

        if (!array_key_exists('total', $this->options)
            || $this->options['total'] === 0
            || $this->options['total'] <= $this->options['epp']
        ) {
            return '';
        }

        $this->options['pages'] = ceil(
            $this->options['total'] / $this->options['epp']
        );

        if (strpos($this->options['url'], '?')) {
            $this->options['join'] = '&';
        }

        return '<ul class="pagination">'
            . $this->getFirstLink()
            . $this->getPreviousLink()
            . $this->getLinks()
            . $this->getNextLink()
            . $this->getLastLink()
            . '</ul>';
    }

    /**
     * Returns the HTML for the first link.
     *
     * @return string The first link as HTML.
     */
    protected function getFirstLink()
    {
        if ($this->options['boundary'] === false) {
            return '';
        }

        $disabled = $this->options['page'] == 1 ? ' class="disabled"' : '';
        $href     = '#';

        if (!empty($this->options['url'])) {
            $href = $this->options['url'] . $this->options['join'] . 'page=1';
        }

        return '<li' . $disabled . '><a href="' . $href .'">' . _('First')
            . '</a></li>';
    }

    /**
     * Returns the HTML for the last link.
     *
     * @return string The last link as HTML.
     */
    protected function getLastLink()
    {
        if ($this->options['boundary'] === false) {
            return '';
        }

        $disabled = $this->options['page'] == $this->options['pages'] ?
            ' class="disabled"' : '';
        $href     = '#';

        if (!empty($this->options['url'])) {
            $href = $this->options['url'] . $this->options['join']
                . 'page=' . $this->options['pages'];
        }

        return '<li' . $disabled . '><a href="' . $href .'">' . _('Last')
            . '</a></li>';
    }

    /**
     * Returns the HTML for the numeric links.
     *
     * @return string The numeric links as HTML.
     */
    protected function getLinks()
    {
        $page  = $this->options['page'];
        $total = $this->options['pages'];
        $pages = min($this->options['maxLinks'], $total);
        $delta = ceil(($this->options['maxLinks'] - 1) / 2);

        $min = max($page - $delta, 1);
        $max = min($page + $delta, $total);

        $linksLeft = max($pages - ($max - $min + 1), 0);
        $max       = min($max + $linksLeft, $total);

        $linksLeft = max($pages - ($max - $min + 1), 0);
        $min       = max($min - $linksLeft, 1);

        $href  = '#';
        $links = '';
        for ($i = $min; $i <= $max; $i++) {
            if (!empty($this->options['url'])) {
                $href = $this->options['url'] . $this->options['join']
                    . 'page=' . $i;
            }

            $links .= '<li' . ($page == $i ? ' class="active"' : '') . '>'
                . '<a href="' . $href . '">'
                    . $i
                . '</a>'
            .'</li>';
        }

        return $links;
    }

    /**
     * Returns the HTML for the next link.
     *
     * @return string The next link as HTML.
     */
    protected function getNextLink()
    {
        if ($this->options['directional'] === false) {
            return '';
        }

        $disabled = $this->options['page'] == $this->options['pages'] ?
            ' class="disabled"' : '';
        $href     = '#';

        if (!empty($this->options['url'])) {
            $href = $this->options['url'] . $this->options['join'] . 'page='
                . min($this->options['page'] + 1, $this->options['pages']);
        }

        return '<li' . $disabled . '><a href="' . $href .'">' . _('Next')
            . '</a></li>';
    }

    /**
     * Returns the HTML for the previous link.
     *
     * @return string The previous link as HTML.
     */
    protected function getPreviousLink()
    {
        if ($this->options['directional'] === false) {
            return '';
        }

        $disabled = $this->options['page'] == 1 ? ' class="disabled"' : '';
        $href     = '#';

        if (!empty($this->options['url'])) {
            $href = $this->options['url'] . $this->options['join'] . 'page='
                . max($this->options['page'] - 1, 1);
        }

        return '<li' . $disabled . '>'
            . '<a href="' . $href .'">' . _('Previous') . '</a></li>';
    }
}
