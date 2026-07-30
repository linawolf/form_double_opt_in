<?php

namespace LinaWolf\FormDoubleOptIn\Utility;

use Symfony\Component\Mime\Address;

class AddressUtility
{
    public static function toArray(array $adresses): array
    {
        array_walk($adresses, function (&$value): void {
            $value = [
                'email' => $value->getAddress(),
                'name' => $value->getName(),
            ];
        });
        return $adresses;
    }

    public static function toAdresses(array $adresses): array
    {
        array_walk($adresses, function (array &$value): void {
            $value = new Address($value['email'], $value['name']);
        });
        return $adresses;
    }
}
