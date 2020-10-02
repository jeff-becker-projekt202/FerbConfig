<?php
declare(strict_types=1);
namespace Ferb\Config\Providers;

class PhpIniProvider extends ConfigProviderBase
{
    protected function get_values():array
    {
        return parent::flatten(ini_get_all());
    }
}
