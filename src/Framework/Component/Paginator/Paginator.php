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
     * The HTML string for pagination.
     *
     * @var string
     */
    public $links = '';

    /**
     * The configuration options.
     *
     * @var array
     */
    protected $options = [
        'boundary'    => false,
        'class'       => 'pagination',
        'directional' => false,
        'epp'         => 10,
        'maxLinks'    => 5,
        'page'        => 1,
        'route'       => null,
        'total'       => 0,
    ];

    /**
     * Initializes the Paginator.
     *
     * @param Router $router The router service.
     */
    public function __construct($router)
    {
        $this->router = $router;

        $this->templates = [
            'first'    => _('First'),
            'last'     => _('Last'),
            'next'     => _('Next'),
            'previous' => _('Previous'),
        ];
    }

    /**
     * Returns the HTML code for pagination.
     *
     * @return string The HTML code for pagination.
     */
    public function __toString()
    {
        return $this->links;
    }

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

        // Do not generator the paginator if epp is less than 0. Division by 0.
        if (!$this->options['epp'] <= 0) {
            return '';
        }

        if (array_key_exists('templates', $options)) {
            $this->templates = array_merge(
                $this->templates,
                $options['templates']
            );
        }

        $this->options['pages'] = ceil(
            $this->options['total'] / $this->options['epp']
        );

        $this->links = '<ul class="' . $this->options['class'] . '">'
            . $this->getFirstLink()
            . $this->getPreviousLink()
            . $this->getLinks()
            . $this->getNextLink()
            . $this->getLastLink()
            . '</ul>';

        return $this;
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

        $disabled = $this->options['page'] == 1 ? ' disabled' : '';

        return '<li class="first' . $disabled . '"><a href="'
            . $this->getUrl(1) . '">' . $this->templates['first'] . '</a></li>';
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
            ' disabled' : '';

        return '<li class="last' . $disabled . '"><a href="'
            . $this->getUrl($this->options['pages']) . '">'
            . $this->templates['last'] . '</a></li>';
    }

    /**
     * Returns the HTML for the numeric links.
     *
     * @return string The numeric links as HTML.
     */
    protected function getLinks()
    {
        if ($this->options['maxLinks'] === 0) {
            return '';
        }

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

        $links = '';
        for ($i = $min; $i <= $max; $i++) {
            $links .= '<li' . ($page == $i ? ' class="active"' : '') . '>'
                . '<a href="' . $this->getUrl($i) . '">'
                    . $i
                . '</a>'
            . '</li>';
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

        $page     = min($this->options['page'] + 1, $this->options['pages']);
        $disabled = $this->options['page'] == $this->options['pages'] ?
            ' disabled' : '';

        return '<li class="next' . $disabled . '"><a href="'
            . $this->getUrl($page) . '">' . $this->templates['next']
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

        $page     = max($this->options['page'] - 1, 1);
        $disabled = $this->options['page'] == 1 ? ' disabled' : '';

        return '<li class="previous' . $disabled . '">'
            . '<a href="' . $this->getUrl($page) . '">'
            . $this->templates['previous'] . '</a></li>';
    }

    /**
     * Returns the URL for a page.
     *
     * @param integer $page The page value.
     *
     * @return string The URL for the page.
     */
    protected function getUrl($page)
    {
        $route  = $this->options['route'];
        $params = [];
        if (is_array($route)) {
            if (array_key_exists('params', $route)) {
                $params = $route['params'];
            }

            $route = $route['name'];
        }

        $params = array_merge($params, [ 'page' => $page ]);

        return $this->router->generate($route, $params);
    }
}
