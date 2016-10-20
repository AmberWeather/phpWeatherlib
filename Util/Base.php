<?php
/**
 * @date: 2015/12/08
 * @author: Tiger <DropFan@Gmail.com>
 */
namespace Weatherlib\Util;

abstract class Base
{

    public function __construct()
    {
        echo 'Base';
    }

    public function __toString()
    {
        return json_encode($this);
    }

    public function __get($name)
    {
        // if ($name = 'sun') echo '[a]<br>';
        $getter = 'get' . ucfirst($name);
        if (isset($this->$name)) {
            return $this->$name;
        } elseif (method_exists($this, $getter)) {
            return $this->$getter();
        }

        $message = sprintf('Class "%1$s" does not have a property named "%2$s"
                            or a method named "%3$s".', get_class($this), $name, $getter);
        throw new \OutOfRangeException($message);
    }

    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        }

        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            $message = sprintf('Implicit property "%2$s" of class "%1$s" cannot
                               be set because it is read-only.', get_class($this), $name);
        } else {
            $message = sprintf('Class "%1$s" does not have a property named "%2$s"
                                or a method named "%3$s".', get_class($this), $name, $setter);
        }
        throw new \OutOfRangeException($message);
    }
/*
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        $message = sprintf('Class "%1$s" does not have a property named "%2$s"
                            or a method named "%3$s".', get_class($this), $name, $getter);
        throw new \OutOfRangeException($message);
    }

    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        }

        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            $message = sprintf('Implicit property "%2$s" of class "%1$s" cannot
                               be set because it is read-only.', get_class($this), $name);
        } else {
            $message = sprintf('Class "%1$s" does not have a property named "%2$s"
                                or a method named "%3$s".', get_class($this), $name, $setter);
        }
        throw new \OutOfRangeException($message);
    }*/
}
