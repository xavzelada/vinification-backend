<?php

namespace App\Serializer;

class CircularReferenceHandler
{
    public static function handle(object $object): mixed
    {
        if (method_exists($object, 'getId')) {
            return $object->getId();
        }
        return null;
    }
}
