<?php
declare(strict_types=1);
namespace Ferb\Config\Providers;

class WordpressOptionsProvider extends ConfigProviderBase
{
    protected function get_values():array
    {
        $data = [];
        if(function_exists('wp_load_alloptions')){
           $data = wp_load_alloptions();
        }
       
        return $data;
    }
}
