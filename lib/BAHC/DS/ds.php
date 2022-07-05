<?php
namespace BAHC\DS;

class DS {
    
    const SPLIT = ' ⇢ ';
    const H_SPLIT = '->';

    private static $DATA;
    private static $index = '';
    
    static function getAll() {
        return self::$DATA;
    }

    static function get($key='', $default=null) {
        $key = self::norm($key);
        return isset(self::$DATA[$key])
                    ? self::$DATA[$key]
                    : $default; 
    }

    static function getMany($keys=[]) {
        $values = null;
        foreach ($keys as $key) {
            $values[$key] = self::get($key);
        }
        return $values;
    }

    static function put($key='', $value=null, $sort=true) {
        if (!empty($key) && !is_null($key)) {
            $key = self::norm( $key );
            self::$DATA[$key] = $value;
            if (true === $sort) { 
                self::sort(); 
            }
            self::reset();
        }
    }

    static function putMany($values=null, $key=null, $sort=true) {
        
        if (\is_null($values)) { 
            return; 
        }
   
        if (\is_array($values) && \count($values)) {
            foreach ($values as $k => $v) {
                if (!\is_null($key)) {
                    $k = $key . self::SPLIT . $k;
                }
                self::putMany($v, $k, $sort);
            }
        }

        if (!is_array($values)) {
            self::put($key, $values, $sort);
        }
    }
    
    static function flush() {
        self::$DATA = [];
    }

    static function forget($key) {
        if (is_array($key)) {
            foreach($key as $k) {
                self::forget($k);
            }
        } else {
            $key = self::norm($key);
            if (self::get($key)) {
                unset(self::$DATA[$key]);
            }
        }
    }

    static function increment($key, $increment=1) {
        $increment = (int) $increment;
        if ($increment < 1) { $increment = 1; }
        $value = (int) self::get($key);
        if(!is_null($value)){
            self::put($key, $value+$increment);
        } else {
            self::put($key, 1); 
        }
    }

    static function decrement($key, $decrement=1) {
        $decrement = (int) $decrement;
        if ($decrement < 1) { $decrement = 1; }
        $value = self::get($key);
        if (!is_null($value)){
            self::put($key, $value - $decrement);
        } else {
            self::put($key, 0-$decrement); 
        }
    }
    
    static function sort($direction='asc') {
        if ('desc' == $direction) {
            \krsort(self::$DATA);
        } else {
            \ksort(self::$DATA);
        }
    }

    static function norm($key) {
        if (empty($key)) return;
        $key = \strtolower($key);
        return \str_replace (self::H_SPLIT, self::SPLIT, $key);
    }

    static function tags($tags='') {
        if (empty($tags)) { return; }
        else if (!is_array($tags)) { $tags = [$tags]; }

        $keys = [];
        if (count($tags)) {
            $_haystack = self::getKeys();
            foreach ($tags as $_tag) {
                $_tag = self::norm($_tag);
                foreach ($_haystack as $_key) {
                    if (strstr($_key, $_tag)) {
                        $keys[] = $_key;
                    }
                }
            }
        }

        $result = [];
        if (count($keys)) {
            foreach ($keys as $key) {
                $result[$key] = self::get($key);
            }
        }

        return $result;
    }

    static function getKeys() {
        return array_keys(self::$DATA);
    }

    static function getIndex() {
        return self::$index;
    }

    static function setIndex($index='') {
        self::$index = self::norm($index);
    }

    static function reset() {
        $keys = self::getKeys();
        self::setIndex($keys[0]);
    }

    static function next() {
        //$current = '';
        $keys = self::getKeys();

        if (count($keys)) {
            $_index = self::getIndex();
            $next = '';

            if (!empty($_index)) {
                $next_idx = array_search($_index, $keys, true);
                if (false !== $next_idx) {
                    $next_idx++;
                    if (isset($keys[$next_idx])) {
                        $next = $keys[$next_idx];
                    }
                }
                
            }
            $currentIndex =  !empty($next)? $next : $keys[0];
            self::setIndex($currentIndex);
        }

        return self::current();
    }

    static function prev() {
        $current = '';
        $keys = self::getKeys();

        if (count($keys)) {
            $_index = self::getIndex();
            $prev = '';

            if (!empty($_index)) {
                $_idx = array_search($_index, $keys, true);
                if (false !== $_idx && 0 < $_idx) {
                    $_idx--;
                    if (isset($keys[$_idx])) {
                        $prev = $keys[$_idx];
                    }
                }
                
            }
            $current =  !empty($prev)? $prev : $keys[count($keys)-1];
            self::setIndex($current);
        }
        return self::current();
    }

    static function current() {
        $idx = self::getIndex();
        return self::get($idx);
    }


    static function toArray($ds='') {
        
        if (!\is_array($ds)) {
            $ds = self::getAll();
        }

        $result = [];
        
        foreach ($ds as $k => $v) {
            $k = \explode(self::SPLIT, $k);
            $_end = \str_repeat('}', \count($k));
            
            $_json = '';
            
            $k = \implode ('":{"', $k);
            
            if ( !self::isDigit($v) ) {

                if (\is_bool($v)) {
                    $v = false === $v ? 'false': 'true';
                }

                $v = '"'. $v .'"';
            }

            $_json = '{"'. $k . '":' . $v . $_end;
        
            $_r = \json_decode($_json, true);

            $result = @\array_merge_recursive($result, $_r);
            
        }
        
        return $result;
    }

    static function toJson($ds='') {
        if (!\is_array($ds)) {
            $ds = self::getAll();
        }
        $_res = \json_encode($ds, true);
        $_res = str_ireplace('"true"', 'true', $_res);
        $_res = str_ireplace('"false"', 'false', $_res);
        return $_res;
    }

    static function isDigit($v) {
        return (\is_int($v) || \is_float($v) || \is_double($v));
    }

    static function sum($tags=[]) {
        $_res = 0;
        if (!empty($tags)) {
            $nums = self::tags($tags);
            if (!empty($nums)) {
                foreach ($nums as $n) {
                    $_res += $n;
                }
            }
        }
        return $_res;
    }

    static function sumAll() {
        $tags = \array_keys(self::getAll());
        return self::sum($tags);
    }

}