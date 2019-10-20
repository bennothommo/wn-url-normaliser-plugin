<?php
namespace BennoThommo\UrlNormaliser;

use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use BennoThommo\UrlNormaliser\Classes\Normalise;
use Backend;
use Event;

class Plugin extends PluginBase
{
    public $require = [
        'BennoThommo.Meta'
    ];

    public function pluginDetails()
    {
        return [
            'name' => 'bennothommo.urlnormaliser::lang.plugin.name',
            'description' => 'bennothommo.urlnormaliser::lang.plugin.description',
            'author' => 'Ben Thomson',
            'iconSvg' => Backend::url('bennothommo/urlnormaliser/assets/icon.svg')
        ];
    }

    public function registerPermissions()
    {
        return [
            'bennothommo.urlnormaliser.change_prefs' => [
                'label' => 'bennothommo.urlnormaliser::lang.permission.label',
                'tab' => 'system::lang.permissions.name',
                'order' => 600,
                'roles' => ['developer']
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'normalise' => [
                'label' => 'bennothommo.urlnormaliser::lang.nav.normalise.label',
                'description' => 'bennothommo.urlnormaliser::lang.nav.normalise.description',
                'category' => SettingsManager::CATEGORY_SYSTEM,
                'icon' => 'icon-link',
                'class' => 'BennoThommo\UrlNormaliser\Models\Settings',
                'keywords' => 'urls redirect url normalise canonical',
                'order' => 450,
                'permissions' => [
                    'bennothommo.urlnormaliser.change_prefs'
                ]
            ]
        ];
    }

    public function boot()
    {
        // Add normalise middleware
        $this->app['Illuminate\Contracts\Http\Kernel']
            ->prependMiddleware('BennoThommo\UrlNormaliser\Routing\NormaliseMiddleware');

        // Normalise URLs in Static Menus, if enabled
        if (Normalise::normaliseNavigation()) {
            Event::listen('pages.menu.referencesGenerated', function (&$items) {
                $iterator = function ($menuItems) use (&$iterator) {
                    $result = [];

                    foreach ($menuItems as $item) {
                        if ($item->items) {
                            $item->items = $iterator($item->items);
                        }
                        if (isset($item->normalised)) {
                            continue;
                        }

                        // Normalise URL if an internal link
                        $originalUrl = $item->url;
                        $normalisedUrl = Normalise::url($item->url);

                        if ($originalUrl !== $normalisedUrl) {
                            $item->url = $normalisedUrl;
                            $item->normalised = true;
                        } else {
                            $item->normalised = false;
                        }

                        $result[] = $item;
                    }

                    return $result;
                };

                $items = $iterator($items);
            });
        }
    }
}
