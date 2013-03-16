<?php

/**
 * An array for objects of a specific class.
 *
 * @package Framework
 * @subpackage Business.Entity
 * @see ArrayObject
 * @author Craig Avondo
 */
abstract class ObjectList extends ArrayObject
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param mixed $index <todo:description>
     */
    public function append($value)
    {

        if (!$this->validateItem($newval))
        {
            return;
        }

        parent::append($value);
    }

    /**
     * @param array $array <todo:description>
     */
    public function exchangeArray($array)
    {

        foreach ($array as $item)
        {
            if (!$this->validateItem($item))
            {
                return;
            }
        }

        parent::exchangeArray($array);
    }

    /**
     * @param mixed $index
     * @param object $newval
     */
    public function offsetSet($index, $newval)
    {

        if (!$this->validateItem($newval))
        {
            return;
        }

        parent::offsetSet($index, $newval);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '<pre>' . print_r($this, true) . '</pre>';
    }

    /**
     * Overrides default shallow copy of clone()
     */
    public function __clone()
    {
        foreach ($this as $key => $item)
        {
            $this[$key] = clone $item;
        }
    }

    /**
     * @return bool <todo:description>
     */
    abstract protected function validateItem($item)

    ;
}
