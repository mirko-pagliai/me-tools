<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
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
        if (isUrl($id)) {
            $id = self::getId($id);
        }

        return sprintf('http://img.youtube.com/vi/%s/0.jpg', $id);
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
