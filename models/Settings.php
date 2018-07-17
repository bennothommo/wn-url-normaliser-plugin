<?php
namespace BennoThommo\UrlNormaliser\Models;

use Model;

class Settings extends Model
{
    public $implement = [
        'System.Behaviors.SettingsModel'
    ];

    public $settingsCode = 'bennothommo_urlnormalise_settings';
    public $settingsFields = 'fields.yaml';
}
