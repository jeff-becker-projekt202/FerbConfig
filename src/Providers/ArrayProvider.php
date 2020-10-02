<?php
declare(strict_types=1);
namespace Ferb\Config\Providers;


class IncludeProvider extends ConfigProviderBase
{
    private $underlying;
    public function __construct($data){
        parent::construct('','');
        $this->underlying = $data ?? [];
    }
    protected function get_values():array{
        return parent::flatten($this->underlying);

    }
}