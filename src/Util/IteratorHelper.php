<?php
declare(strict_types=1);
namespace Ferb\Config\Util;

final class IteratorHelper  {


    public static function concat(){
        $others = \func_get_args();
        foreach($others as $other){
            foreach($other as $item){
                yield $item;
            }
        }
    }

    public static function filter($self, callable $predicate = null){
        $predicate = $predicate ?? function($item){ return !empty($item);};
        $i = 0;
        foreach($self as $item){
            if($predicate($item, $i)){
                yield $item;
            }
            $i++;
        }
    }
    public static function map($self, callable $projection){
        $i = 0;
        foreach($self as $item){
            yield $projection($item, $i);
            $i++;
        }
    }

    public static function flat_map($self, callable $projection){

        $i = 0;
        foreach($self as $inner){
            $j = 0;
            foreach($inner as $item){
                yield $projection($item, $j, $i);
                $j++;
            }
            $i++;
        }
    }

    public static function reduce($self, callable $reducer, $inital_value = null){
        $accumulator = $inital_value;
        foreach($self as $item){
            $accumulator = $reducer($accumulator, $item);
        }
        return $accumulator;
    }
    public static function every($self, callable $condition = null)
    {
        $condition = $condition ?? function($item){
            return !empty($item);
        };
        foreach($self as $item){
            if(!$condition($item)){
                return false;
            }
        }
        return true;
    }

    public static function some($self, callable $condition)
    {
        foreach($self as $item){
            if($condition($item)){
                return true;
            }
        }
        return false;
    }
    public static function includes($self, $value, callable $equality_comparitor = null){
        $comparitor = $comparitor ?? function($a, $b){
            return ($a == $b) ;
        };

    }
    public static function group_by($self, callable $key_selector, callable $value_selector)
    {
        $i = 0;
        $result = [];
        foreach($self as $item){
            $key = $key_selector($item, $i);
            $value = $value_selector($item, $i);
            if(!isset($result[$key])){
                $result[$key] = [];
            }
            $result[$key][] = $item;
        }
        $return = [];
        foreach($result as $k=>$v){
            yield (object)[
                'key'=>$k,
                'values'=>$v
            ];
        }

    }
    public static function to_iterable($self){
        if(is_array($self)){
            return new ArrayIterator($self);
        }
        return $self;
    }
    public static function zip($self, $others, callable $zipper)
    {
        $iterables = [self::to_iterable($self)];
        $iterables[0]->rewind();
        foreach($others as $other){
            $i = self::to_iterable($other);
            $iterables[] = $i;
            $i->rewind(); 
        }

        $result = [];
        $all_valid = function($iterables){
            return self::every($iterables, function($i){
                return $i->valid();
            });
        };
        while($all_valid($iterables)){
            $args = \iterator_to_array(self::map($iterables,function($i){
                return $i->current();
            }));
            $result[] = \call_user_func_array($zipper, $args);
            foreach($iterables as $iterable){
                $iterable->next();
            }
        }
        return $result;

    }
    public static function skip_while($self, callable $condition){
        $i = 0;
        $skipping=  true;
        foreach($self as $item){
            $skipping = $skipping? $condition($item, $i) :false;
            if(!$skipping){
                yield $item;
            }
            $i++;
        }
    }
    public static function skip($self, $count){
        return self::skip_while($self, function($item, $index) use ($count){
            return $index < $count;
        });
    }
    public static function take_while($self, callable $condition){
        $self->rewind();
        $i = 0;
        while($self->valid() && $condition($self->current(), $i)){
            yield $self->current();
            $i++;
        }

    }
    public static function take($self, $count){
        return self::take($self, function($item, $index) use ($count){
            return $index < $count;
        });
    }
    public static function reverse($self){
        return new ArrayIterator(array_reverse(self::to_array($self)));
    }
    public static function unique_default_comparitor($a,$b){
        if(is_string($a) && is_string($b)){
            return strcmp($a,$b);
        }
        if(is_numeric($a) && is_numeric($b)){
           return $a - $b;
        }
        return $a == $b? 0 : 1;
    }
    public static function unique($self, callable $comparitor = null)
    {

        $have_one = false;
        $last_value = null;
        foreach(self::order_by_asc($self, $comparitor) as $item){
            if(!$have_one || $comparitor($item, $last_value) !== 0){
                yield $item;
            }
            $have_one = true;
            $last_value = $item;
        }

    }
    public static function union ($self, $other, callable $comparitor = null)
    {
        return self::unique(self::concat([$self, $others]), $comparitor);
    }
    public static function order_by_asc($self, callable $comparitor = null){
        $comparitor = $comparitor ?? self::unique_default_comparitor;
        $arr = self::to_array($self);
        \uasort($arr, $comparitor);
        return new ArrayIterator($arr);
    }
    public static function order_by_desc($self, callable $comparitor = null){
        $comparitor = $comparitor ?? self::unique_default_comparitor;
        $comparitor = function($a,$b) use ($comparitor){
            return -1* $comparitor($a,$b);
        };
        return self::order_by_asc($self, $comparitor);
    }
    public static function to_array($self){
        return is_array($self)? $self : \iterator_to_array($self);
    }
    public static function intersect($self, $other){
        $hash = [];
        foreach($other as $item ){
            $hash[$item] = $item;
        }
        foreach($self as $item){
            if(isset($hash[$item])){
                yield $item;
            }
        }

    }
    public static function diff($self, $other){
        $hash = [];
        foreach($other as $item ){
            $hash[$item] = $item;
        }
        foreach($self as $item){
            if(!isset($hash[$item])){
                yield $item;
            }
        }

    }
    public static function average($self, $by = 'mean'){

        if($by == 'median'){
            $arr= self::to_array($self);
            if(count($arr) ==0) return false;
            sort($arr);
            $idx = floor(count($arr)/ 2);
            return $arr[$idx];
            
        }
        else if($by = 'mode'){
            $hist = [];
            foreach($self as $item){
                $hist[$item] = ($hist[$item] ?? 0)  + 1;
            }
            $last = [0,0];
            foreach($hist as $value=>$count){
                if($last[0] < $count){
                    $last = [$count, $value];
                }
            }
            return $last[1];

        }
        else{
            $total = 0;
            $count = 0;
            foreach ($self as $item) {
                $total += $item;
                ++$count;
            }

            return $total / $count;
        }
    }
    public static function element_at($self, $index){
        $id = 0;
        foreach($self as $item){
            if($id == $index){
                return $item;
            }
        }
        return null;
    }
    public static function first($self){
        return self::element_at($self, 0);
    }
    private static $nil;
    private function nil(){ 
        if(self::$nil == null){
            self::$nil = new stdClass();
        }
        return self::$nil;
    }
    public static function min($self, callable $comparitor){
        $result = self::nil();
        foreach($self as $item){
            if($result === self::nil()){
                $result = $item;
            }
            else{
                if($comparitor($item, $result) < 0){
                    $result = $item;
                }
            }
        }
        if($result === self::nil()){
            return null;
        }
        return $result;
    }
    public static function max($self, callable $comparitor){
        return self::min($self, function($a,$b) use ($comparitor){
            return $comparitor($a, $b) * -1;
        });
    }
    public static function count($self){
        return \iterator_count($self);
    }
    public static function sum($self){
        $total = 0;
        foreach($self as $item){
            $total += $item;
        }
        return $total;
    }
    public static function to_dictionary($self, callable $key_selector, callable $value_selector = null){
        $value_selector = $value_selector ?? function($value, $index){
            return $value;
        };
        $result = [];
        $i = 0;
        foreach($self as $item){
            $key = $key_selector($item);
            $result[$key] = $value_selector($item, $i);
            $i++;
        }
        return $result;
    }

}