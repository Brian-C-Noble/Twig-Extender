<?php
/**
 * Twig Extender plugin for Craft CMS 3.x
 *
 * Adds filter and function not native to Twig or Craft CMS
 *
 * @link      https://github.com/Brian-C-Noble/
 * @copyright Copyright (c) 2019 Brian Noble
 */

namespace briancnoble\twigextender\twigextensions;

use briancnoble\twigextender\TwigExtender;

use Craft;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    Brian Noble
 * @package   TwigExtender
 * @since     1.0.0
 */
class TwigExtenderTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'TwigExtender';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('vartype', [$this, 'getVarType']),
            new \Twig_SimpleFilter('truncate', [$this, 'truncate']),
            new \Twig_SimpleFilter('striphttp', [$this, 'stripHttp']),
            new \Twig_SimpleFilter('phone', [$this, 'formatPhone'])
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
    * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('isVarType', [$this, 'checkVariableType']),
            new \Twig_SimpleFunction('relativeTime', [$this, 'relativeTime']),
            new \Twig_SimpleFunction('truncate', [$this, 'truncate']),
            new \Twig_SimpleFunction('formatVideoUrl', [$this, 'formatVideoUrl']),
            new \Twig_SimpleFunction('oembed', [$this, 'oEmbed']),
            new \Twig_SimpleFunction('ordinal', [$this, 'ordinal'])
        ];
    }


    /**
     * Checks if variable type matches parameter $type.
     * @param $var
     * @param null $type
     * @return bool
     */
    public function checkVariableType($var, $type=null)
    {
        switch ($type)
        {
            case 'array':
                return is_array($var);
                break;

            case 'boolean':
                return is_bool($var);
                break;

            case 'double':
                return is_double($var);
                break;

            case 'float':
                return is_float($var);
                break;

            case 'integer':
                return is_int($var);
                break;

            case 'NULL':
                return is_null($var);
                break;

            case 'numeric':
                return is_numeric($var);
                break;

            case 'object':
                return is_object($var);
                break;

            case 'resource':
                return is_resource($var);
                break;

            case 'string':
                return is_string($var);
                break;
        }
    }

    /**
     * Outputs the variable type
     *
     * @param $var
     * @return string
     */

    public function getVarType($var) {
        return gettype($var);
    }

    /**
     * @param $string
     * @return string
     */
    public function formatVideoUrl($string)
    {
        $parsed = parse_url($string);

        switch ($parsed['host'])
        {
            case 'vimeo.com':
                $url = 'https://player.vimeo.com/video' . $parsed['path'];
            break;

            case 'youtube.com':
            default:

                if (stripos($string, 'youtube.com/embed/') !== false)
                {
                    // good to go!
                    return $string;
                }

                $url = 'https://www.youtube.com/embed'. $parsed['path'];

            break;
        }

        return $url;
    }

    /**
     * Converts a string of numbers to a US phone number formate
     *
     * @param $string
     * @param string $separator
     * @param null $parens
     * @return string|string[]|null
     */
    public function formatPhone($string, $separator="-", $parens=null)
    {
        if (empty($string))
        {
            return '';
        }

        $phoneNumber = preg_replace('/[^0-9]/', '', $string);

        if ($parens === 'parens')
        {
            if (strlen($phoneNumber) > 10)
            {
                $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
                $areaCode = substr($phoneNumber, -10, 3);
                $nextThree = substr($phoneNumber, -7, 3);
                $lastFour = substr($phoneNumber, -4, 4);

                $phoneNumber = $countryCode . ' ' . '(' . $areaCode . ')' . ' ' . $nextThree . $separator . $lastFour;
            }

            else if(strlen($phoneNumber) == 10)
            {
                $areaCode = substr($phoneNumber, 0, 3);
                $nextThree = substr($phoneNumber, 3, 3);
                $lastFour = substr($phoneNumber, 6, 4);

                $phoneNumber = '(' . $areaCode . ')' . ' ' . $nextThree . $separator . $lastFour;
            }
            else if(strlen($phoneNumber) == 7)
            {
                $nextThree = substr($phoneNumber, 0, 3);
                $lastFour = substr($phoneNumber, 3, 4);

                $phoneNumber = $nextThree . $separator . $lastFour;
            }
        }
        else
        {
            if (strlen($phoneNumber) > 10)
            {
                $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
                $areaCode = substr($phoneNumber, -10, 3);
                $nextThree = substr($phoneNumber, -7, 3);
                $lastFour = substr($phoneNumber, -4, 4);

                $phoneNumber = $countryCode . $separator . $areaCode . $separator . $nextThree . $separator . $lastFour;
            }
            else if(strlen($phoneNumber) == 10)
            {
                $areaCode = substr($phoneNumber, 0, 3);
                $nextThree = substr($phoneNumber, 3, 3);
                $lastFour = substr($phoneNumber, 6, 4);

                $phoneNumber = $areaCode . $separator . $nextThree . $separator . $lastFour;
            }
            else if(strlen($phoneNumber) == 7)
            {
                $nextThree = substr($phoneNumber, 0, 3);
                $lastFour = substr($phoneNumber, 3, 4);

                $phoneNumber = $nextThree . $separator . $lastFour;
            }
        }

        return $phoneNumber;
    }

    /**
     * Removes the protocol from a URL
     *
     * @param $string
     * @return string
     */
    public function stripHttp($string)
    {
        $parsed = parse_url($string);

        if (empty($parsed['host']))
        {
            return $string;
        }

        if (empty($parsed['path']))
        {
            return $parsed['host'];
        }

        return $parsed['host'] . ($parsed['path'] === '/' ? '' : $parsed['path']);
    }

    /**
     * Shortens a string to the specified limit and append to elepses end of the string
     *
     * @param $value
     * @param $limit
     * @param string $end
     * @return string
     */
    public function truncate($value, $limit, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
    }

    /**
     * @param \DateTime $dateTime
     * @return string|null
     * @throws \Exception
     */
    public function relativeTime(\DateTime $dateTime)
    {
        $now = new \DateTime();
        $interval = $now->diff($dateTime);

        $days = (int)$interval->format('%d');
        $hours = (int)$interval->format('%h');
        $minutes = (int)$interval->format('%i');
        $seconds = (int)$interval->format('%s');

        $suffix = ! empty($suffix) ? ' ' . $suffix : '';

        if ($days > 0)
        {
            return $days . 'd' . $suffix;
        }

        if ($hours > 0)
        {
            return $hours . 'hr' . $suffix;
        }

        if ($minutes > 0)
        {
            return $minutes . 'm' . $suffix;
        }

        if ($seconds > 0)
        {
            return $seconds . 's' . $suffix;
        }

        return NULL;

    }

    /**
     * @param $string
     * @return string
     */
    public function ordinal($string)
    {
        $fmt = new \NumberFormatter( 'en_US', \NumberFormatter::ORDINAL );

        return $fmt->format($string);
    }
}

class Oembed
{
    public $thumb;

    public $iframeUrl;
}