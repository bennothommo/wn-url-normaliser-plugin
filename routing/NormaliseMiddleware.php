<?php
namespace BennoThommo\UrlNormaliser\Routing;

use App;
use BennoThommo\UrlNormaliser\Models\Settings;
use Config;
use Closure;

class NormaliseMiddleware
{
    public $ignore = [
        'backend/*'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Always allow backend to be accessed
        if (App::runningInBackend()) {
            return $next($request);
        }

        $settings = Settings::instance();
        $urlInfo = parse_url($request->fullUrl());
        $domain = $urlInfo['host'];
        $requestPath = $request->getPathInfo();
        $changed = false;

        // Determine paths to ignore
        if (!empty($settings->ignore)) {
            $ignorePaths = array_merge($this->ignore, preg_split('/[\r\n]+/', $settings->ignore, -1, PREG_SPLIT_NO_EMPTY));
        } else {
            $ignorePaths = $this->ignore;
        }

        if ($request->method() === 'GET') {
            // Check if URL is ignored
            if (count($ignorePaths) && $requestPath !== '/') {
                $ignored = false;

                foreach ($ignorePaths as $ignorePath) {
                    if ($request->is($ignorePath)) {
                        $ignored = true;
                        break;
                    }
                }

                if ($ignored) {
                    return $next($request);
                }
            }

            // Add or remove trailing slash if preferenced
            if ($settings->trailing_slash !== 'none') {
                // Do not apply trailing slash rules if the URL has an extension
                $extension = pathinfo($requestPath, PATHINFO_EXTENSION);
                $hasSlash = (preg_match('/\/$/', $requestPath) === 1);

                if (empty($extension) && $requestPath !== '/') {
                    if ($settings->trailing_slash === 'yes' && $hasSlash === false) {
                        $requestPath = $requestPath . '/';
                        $changed = true;
                    }
                    if ($settings->trailing_slash === 'no' && $hasSlash === true) {
                        $requestPath = preg_replace('/\/+$/', '', $requestPath);
                        $changed = true;
                    }
                }
            }

            // Add or remove www prefix if preferenced
            if ($settings->www_prefix !== 'none') {
                $hasPrefix = (preg_match('/^www./i', $domain) === 1);

                if ($settings->www_prefix === 'www' && $hasPrefix === false) {
                    $domain = 'www.' . $domain;
                    $changed = true;
                }
                if ($settings->www_prefix === 'notWww' && $hasPrefix === true) {
                    $domain = preg_replace('/^www./i', '', $domain);
                    $changed = true;
                }
            }

            // Add HTTPS if it is forced
            if (empty($urlInfo['scheme'])) {
                $urlInfo['scheme'] = 'https';
            }
            if (boolval($settings->force_https) === true && $urlInfo['scheme'] === 'http') {
                $urlInfo['scheme'] = 'https';
                $changed = true;
            }

            if ($changed === true) {
                if ($settings->mode === 'redirect') {
                    return redirect()->away(
                        $urlInfo['scheme'] . '://' . $domain . $requestPath .
                        ((!empty($urlInfo['query'])) ? '?' . $urlInfo['query'] : '') .
                        ((!empty($urlInfo['fragment'])) ? '#' . $urlInfo['fragment'] : ''), 301);
                } else {
                    \BennoThommo\Meta\Link::set('canonical', $urlInfo['scheme'] . '://' . $domain . $requestPath .
                    ((!empty($urlInfo['query'])) ? '?' . $urlInfo['query'] : '') .
                    ((!empty($urlInfo['fragment'])) ? '#' . $urlInfo['fragment'] : ''));
                }
            }
        }

        return $next($request);
    }
}
