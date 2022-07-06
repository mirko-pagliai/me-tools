<?php
declare(strict_types=1);

/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\HtmlHelper;
use Tools\Exceptionist;

/**
 * Provides functionalities for forms
 * @method h1(?string $text = null, array $options = [])
 * @method h2(?string $text = null, array $options = [])
 * @method h3(?string $text = null, array $options = [])
 * @method h4(?string $text = null, array $options = [])
 * @method h5(?string $text = null, array $options = [])
 * @method h6(?string $text = null, array $options = [])
 * @property \MeTools\View\Helper\IconHelper $Icon
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class BootstrapHtmlHelper extends HtmlHelper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = ['MeTools.Icon', 'Url'];

    /**
     * Missing method handler.
     *
     * If you pass no more than two parameters, it tries to generate a html
     *  tag with the name of the method and works as alias of `tag()`.
     * @param string $method Name of the tag
     * @param array $params Params for the method
     * @return string
     * @throws \ErrorException
     */
    public function __call(string $method, array $params = []): string
    {
        Exceptionist::isTrue(count($params) < 3, sprintf('Method `%s::%s()` does not exist', __CLASS__, $method));

        return $this->tag($method, $params[0], $params[1] ?? []);
    }

    /**
     * Creates an HTML link.
     *
     * See the parent method for all available options.
     * @param array|string $title The content to be wrapped by `<a>` tags
     *   Can be an array if $url is null. If $url is null, $title will be used as both the URL and title.
     * @param array|string|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array<string, mixed> $options Array of options and HTML attributes
     * @return string An `<a />` element
     */
    public function link($title, $url = null, array $options = []): string
    {
        if (is_array($title)) {
            if (!$url) {
                $url = $title;
            }
            $title = '';
        }

        $options = optionsParser($options, ['escape' => false]);

        $titleAsOption = $options->get('title') ?? $title;
        if ($titleAsOption) {
            $options->add('title', trim(strip_tags($titleAsOption)));
        }

        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::link($title, $url, $options->toArray());
    }

    /**
     * Returns a formatted block tag, i.e DIV, SPAN, P.
     *
     * See the parent method for all available options.
     * @param string $name Tag name
     * @param string|null $text String content that will appear inside the div element.
     *   If null, only a start tag will be printed
     * @param array<string, mixed> $options Additional HTML attributes of the DIV tag, see above
     * @return string The formatted tag element
     */
    public function tag(string $name, ?string $text = null, array $options = []): string
    {
        $options = optionsParser($options);
        [$text, $options] = $this->Icon->addIconToText($text, $options);

        return parent::tag($name, $text, $options->toArray());
    }
}
