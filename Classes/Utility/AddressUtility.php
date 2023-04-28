<?php

namespace Medienreaktor\FormDoubleOptIn\Utility;

class AddressUtility
{
    public static function toArray(array $adresses): array
    {
        array_walk($adresses, function (&$value) {
            $value = [
                'email' => $value->getAddress(),
                'name' => $value->getName(),
            ];
        });
        return $adresses;
    }

    public static function toAdresses(array $adresses): array
    {
        array_walk($adresses, function (&$value) {
            $value = new \Symfony\Component\Mime\Address($value['email'], $value['name']);
        });
        return $adresses;
    }
}
