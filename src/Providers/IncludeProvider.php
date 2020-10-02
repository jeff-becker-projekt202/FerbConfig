<?php
declare(strict_types=1);
namespace Ferb\Conf\Providers;


class IncludeProvider extends ConfigProviderBase
{
    private $file;
    public function __construct($file){
        parent::__construct('','');
        $this->file = $file;
    }
    protected function get_values():array{
        $data = (function(){
            return (include $this->file);
        })();
        if($data === false) return [];

        return parent::flatten($data);

    }
}