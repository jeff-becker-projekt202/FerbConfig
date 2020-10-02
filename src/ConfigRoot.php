<?php
declare(strict_types=1);
namespace Ferb\Conf;
;
use Ferb\Conf\Util\ConfigPath;
use Ferb\Iterators\FluentIterator;

class ConfigRoot 
{

    private array $providers;
    private array $providers_reverse;
    public function __construct(array $providers){
        $this->providers = array_values($providers ??[]);
        $this->providers_reverse = \array_reverse($this->providers);
    }

    public function value(string $key){
        if(empty($key)){
            return null;
        }
        foreach($this->providers_reverse as $provider){
            list($found, $value) = $provider->get($key);
            if($found){ 
                return $value;}
        }
        return null;
    }

    public function section(string $key = null) : ConfigSection{
        return new ConfigSection($this, $key);
    }
    public function children($path = ''): array{
        return FluentIterator::from($this->providers)
        ->aggregate(function($a,$p) use ($path){
            return $p->get_child_keys($a,$path);
        },FluentIterator::empty());
    }
    public function has_children($path = ''): bool{
        return $this->children($path)
        ->some(function($p){ return true;});
    }



}