# URL Normaliser Plugin

Normalise URLs for your [Winter CMS](https://wintercms.com)-driven website and ensure that search engines only index your canonical paths to prevent duplicate content. This plugin works by either providing a canonical URL `link` tag in your page (with help from the [Meta Plugin](https://github.com/bennothommo/wn-meta-plugin)) or through using HTTP 301 redirects on your public pages.

## Features

- Enforce your domain to begin, or not to begin, with `www.`
- Enforce your pages to end, or to not end, with a trailing slash.
- Ignore certain pages or sections of your site.
- Apply normalisation through a `rel=canonical` link tag or through HTTP redirects.
- Ensure your Static Pages navigation follows the normalisation standard.
- Update your normalisation settings directly through the Winter CMS backend.

## Requirements

This plugin must be installed with the [Meta Plugin](https://github.com/bennothommo/wn-meta-plugin) to provide the canonical URL link tag functionality.
