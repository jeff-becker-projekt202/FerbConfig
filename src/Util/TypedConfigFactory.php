<?php
declare(strict_types=1);
namespace Ferb\Config\Util;
use Ferb\Config\ConfigSection;
final class TypedConfigFactory
{
    private \ReflectionClass $class;

    public function __construct($class_name){
        $this->class= new \ReflectionClass($class_name);

    }
    public static function instantiate(string $type, $props){
        if(\class_exists($type)){
            return (new self($type))->create_from($props);
        }
        return null;
    }
    public function create_from($props)
    {
        $constructor = $this->class->getConstructor();
        if($constructor!== null){
            $params = $constructor->getParameters();
        }
        $params = $params ??[];
        if(count($params) === 0){
            return $this->class->newInstance();
        }
        if(count($params) === 1){
            return $this->class->newInstance($props);
        }
        throw new ReflectionException("$type does not have a constructor accepting zero or one args");
    }
    private static function get_property_type($class, $prop_name){
        try {
            $class_prop = $class->getProperty($prop_name);
        }
        catch(ReflectionException $ex){
            $class_prop = null;
        }
        if($class_prop !== null){
            $type = $class_prop->getType();
            if($type !== null){
                return $type->getName();
            }
        }
        return null;
    }
    private static function assign_props_recursive($class, $props){
        $inst = $class->newInstance();
        foreach($props as $key=>$value){
            $type = self::get_property_type($class, $key);
            if($type !== null && \class_exists($type)){
                $inst->{$key} = (new self($type))->create_from($value);
            }
            else{
                $inst->{$key} = $value;
            }

        }
    }

}