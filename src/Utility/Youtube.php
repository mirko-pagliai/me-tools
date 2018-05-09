<?php
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
namespace MeTools\Utility;

/**
 * An utility to get information about YouTube videos
 */
class Youtube
{
    /**
     * Parses a YouTube url and returns the YouTube ID
     * @param string $url Video url
     * @return mixed Youtube ID or `false`
     */
    public static function getId($url)
    {
        if (preg_match('/youtube\.com/', $url)) {
            $url = parse_url($url);

            if (empty($url['query'])) {
                return false;
            }

            parse_str($url['query'], $url);

            return empty($url['v']) ? false : $url['v'];
        } elseif (preg_match('/youtu.be\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }

        return false;
    }

    /**
     * Gets the preview for a video
     * @param string $id YouTube ID or url
     * @return string
     * @uses getId()
     */
    public static function getPreview($id)
    {
        return sprintf('http://img.youtube.com/vi/%s/0.jpg', is_url($id) ? self::getId($id) : $id);
    }

    /**
     * Gets the url for a video
     * @param string $id YouTube ID
     * @return string
     */
    public static function getUrl($id)
    {
        return sprintf('http://youtu.be/%s', $id);
    }
}
