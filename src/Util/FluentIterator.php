<?php
declare(strict_types=1);
namespace Ferb\Config\Util;

class FluentIterator extends OuterIterator {

    private $inner;
    public function __construct ($inner){

        $this->inner = \NoRewindIterator(is_array($inner)? new ArrayIterator($inner) : $inner);
    }
    public static function from($inner){
        return new self($inner);
    }
    public function getInnerIterator ( ) {return $this->inner;}
    public function __get($name)
    {
        return $name == 'value'? $this->inner : null;
    }
    public function empty(){
        return new class implements Iterator{
            public function current() {return null;}
            public function key() {return null;}
            public function next() {}
            public function rewind() {}
            public function valid() {return false;}
        };
    }
    public function append($other){
        return new self(IteratorHelper::concat($self, $others));
    }
    public function prepend($other){
        return new self(IteratorHelper::concat($other, $self));
    }

     
    public function filter(callable $predicate = null)
    {
        return new self(IteratorHelper::filter($self, $predicate));
    }
     
    public function map(callable $projection)
    {
        return new self(IteratorHelper::map($self, $projection));
    }
     
    public function flat_map(callable $projection)
    {
        return new self(IteratorHelper::flat_map($self, $projection));
    }
     
    public function reduce(callable $reducer, $inital_value = null)
    {
        return new self(IteratorHelper::reduce($self, $reducer, $inital_value));
    }
     
    public function every(callable $condition = null)
    {
        return new self(IteratorHelper::every($self, $condition));
    }
     
    public function some(callable $condition)
    {
        return new self(IteratorHelper::some($self, $condition));
    }
     
    public function includes($value, callable $equality_comparitor = null)
    {
        return new self(IteratorHelper::includes($self, $value, $equality_comparitor));
    }
     
    public function group_by(callable $key_selector, callable $value_selector)
    {
        return new self(IteratorHelper::group_by($self, $key_selector, $value_selector));
    }
     
    public function to_iterable()
    {
        return new self(IteratorHelper::to_iterable($self));
    }
     
    public function zip($others, callable $zipper)
    {
        return new self(IteratorHelper::zip($self, $others, $zipper));
    }
     
    public function skip_while(callable $condition)
    {
        return new self(IteratorHelper::skip_while($self, $condition));
    }
     
    public function skip($count)
    {
        return new self(IteratorHelper::skip($self, $count));
    }
     
    public function take_while(callable $condition)
    {
        return new self(IteratorHelper::take_while($self, $condition));
    }
     
    public function take($count)
    {
        return new self(IteratorHelper::take($self, $count));
    }
     
    public function reverse()
    {
        return new self(IteratorHelper::reverse($self));
    }
     
    public function distinct_default_comparitor($a, $b)
    {
        return new self(IteratorHelper::distinct_default_comparitor($self, $a, $b));
    }
     
    public function distinct(callable $comparitor = null)
    {
        return new self(IteratorHelper::distinct($self, $comparitor));
    }
     
    public function union ($other, callable $comparitor = null)
    {
        return new self(IteratorHelper::union ($self, $other, $comparitor));
    }
     
    public function order_by_asc(callable $comparitor = null)
    {
        return new self(IteratorHelper::order_by_asc($self, $comparitor));
    }
     
    public function order_by_desc(callable $comparitor = null)
    {
        return new self(IteratorHelper::order_by_desc($self, $comparitor));
    }
     
    public function to_array()
    {
        return new self(IteratorHelper::to_array($self));
    }
     
    public function intersect($other)
    {
        return new self(IteratorHelper::intersect($self, $other));
    }
     
    public function diff($other)
    {
        return new self(IteratorHelper::diff($self, $other));
    }
     
    public function average($by = 'mean')
    {
        return new self(IteratorHelper::average($self, $by));
    }
     
    public function element_at($index)
    {
        return new self(IteratorHelper::element_at($self, $index));
    }
     
    public function first()
    {
        return new self(IteratorHelper::first($self));
    }
     
    public function min(callable $comparitor)
    {
        return new self(IteratorHelper::min($self, $comparitor));
    }
     
    public function max(callable $comparitor)
    {
        return new self(IteratorHelper::max($self, $comparitor));
    }
     
    public function count()
    {
        return new self(IteratorHelper::count($self));
    }
     
    public function sum()
    {
        return new self(IteratorHelper::sum($self));
    }
     
    public function to_dictionary(callable $key_selector, callable $value_selector = null)
    {
        return new self(IteratorHelper::to_dictionary($self, $key_selector, $value_selector));
    } 
    
}