<?php

namespace BennoThommo\UrlNormaliser\Classes;

use BennoThommo\UrlNormaliser\Models\Settings;

class Normalise
{
    use \October\Rain\Support\Traits\Singleton;

    /**
     * Normalisation settings.
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Paths to ignore
     *
     * @var array
     */
    protected $pathsToIgnore = [];

    /**
     * Paths to always ignore
     *
     * @var array
     */
    protected $alwaysIgnore = [
        'backend/*'
    ];

    /**
     * Initialize this singleton.
     *
     * @return void
     */
    protected function init()
    {
        $this->settings = Settings::instance();

        // Determine paths to ignore
        if (!empty($this->settings->ignore)) {
            $this->pathsToIgnore = array_merge(
                $this->alwaysIgnore,
                preg_split(
                    '/[\r\n]+/',
                    $this->settings->ignore,
                    -1,
                    PREG_SPLIT_NO_EMPTY
                )
            );
        } else {
            $this->pathsToIgnore = $this->alwaysIgnore;
        }
    }

    /**
     * Normalise a given URL using your URL normalisation settings.
     *
     * This will ignore any URLs that appear to be external. You may force the normalisation if you wish.
     *
     * @param string $url
     * @param bool $force
     * @return string The normalised URL
     */
    public static function url(string $url, bool $force = false)
    {
        $instance = self::instance();

        if (!$force && $instance->isExternal($url)) {
            return $url;
        }

        $originalUrl = $url;
        $url = parse_url($url);

        // Set default URL parts, if not provided
        if (empty($url['host'])) {
            $url['host'] = $instance->getHostname();

            // If we cannot determine the hostname, return the URL as is
            if (empty($url['host'])) {
                return $url;
            }
        }
        if (empty($url['scheme'])) {
            $url['scheme'] = 'http';
        }

        // Check if path is ignored
        if (empty($url['path'])) {
            $url['path'] = '/';
        }

        if (count($instance->pathsToIgnore) && $url['path'] !== '/') {
            foreach ($instance->pathsToIgnore as $ignorePath) {
                $ignorePath = (substr($ignorePath, 0, 1) !== '/')
                    ? '/' . $ignorePath
                    : $ignorePath;
                $targetPath = (substr($url['path'], 0, 1) !== '/')
                    ? '/' . $url['path']
                    : $url['path'];
                $wildcardPos = strpos($ignorePath, '*');

                if ($wildcardPos !== false) {
                    $ignorePath = substr($ignorePath, 0, $wildcardPos);
                    $targetPath = substr($targetPath, 0, $wildcardPos);
                }

                if ($ignorePath === $targetPath) {
                    return $originalUrl;
                }
            }
        }

        // Add or remove trailing slash if preferenced
        if ($instance->settings->trailing_slash !== 'none') {
            // Do not apply trailing slash rules if the URL has an extension
            $extension = pathinfo($url['path'], PATHINFO_EXTENSION);
            $hasSlash = (preg_match('/\/$/', $url['path']) === 1);

            if (empty($extension) && $url['path'] !== '/') {
                if ($instance->settings->trailing_slash === 'yes' && $hasSlash === false) {
                    $url['path'] = $url['path'] . '/';
                }
                if ($instance->settings->trailing_slash === 'no' && $hasSlash === true) {
                    $url['path'] = preg_replace('/\/+$/', '', $url['path']);
                }
            }
        }

        // Add or remove www prefix if preferenced
        if ($instance->settings->www_prefix !== 'none') {
            $hasPrefix = (preg_match('/^www./i', $url['host']) === 1);

            if ($instance->settings->www_prefix === 'www' && $hasPrefix === false) {
                $url['host'] = 'www.' . $url['host'];
            }
            if ($instance->settings->www_prefix === 'notWww' && $hasPrefix === true) {
                $url['host'] = preg_replace('/^www./i', '', $url['host']);
            }
        }

        // Add HTTPS if it is forced
        if (empty($url['scheme'])) {
            $url['scheme'] = 'https';
        }
        if (boolval($instance->settings->force_https) === true && $url['scheme'] === 'http') {
            $url['scheme'] = 'https';
        }

        return \http_build_url($url);
    }

    /**
     * Determines whether we will 301 redirect incorrect URLs.
     *
     * @return bool
     */
    public static function doRedirect()
    {
        $instance = self::instance();

        return $instance->settings->mode === 'redirect';
    }

    /**
     * Determines whether we will normalise navigation URLs.
     *
     * @return bool
     */
    public static function normaliseNavigation()
    {
        $instance = self::instance();

        if (!isset($instance->settings->normalise_nav)) {
            return false;
        }

        return (bool) $instance->settings->normalise_nav;
    }

    /**
     * Determines if a URL is external, based on the server name.
     *
     * @param string $url
     * @return bool
     */
    protected function isExternal(string $url)
    {
        $urlHostname = parse_url($url, PHP_URL_HOST) ?? null;

        // A URL without a hostname is definitely an internal URL.
        if (empty($urlHostname)) {
            return false;
        }

        $serverName = $this->getHostname();

        // If we cannot determine the server name, assume external
        if (empty($serverName)) {
            return true;
        }

        // Strip any prefixed "www" from the server names
        $urlHostname = preg_replace('/www\./i', '', $urlHostname);
        $serverName = preg_replace('/www\./i', '', $serverName);

        return (strtolower($serverName) !== strtolower($urlHostname));
    }

    /**
     * Get the hostname, either from the server variables or from the config.
     *
     * @return string|null
     */
    protected function getHostname()
    {
        return $_SERVER['SERVER_NAME']
            ?? parse_url(url(), PHP_URL_HOST)
            ?? null;
    }
}
