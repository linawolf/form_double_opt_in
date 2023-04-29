<?php

namespace LinaWolf\FormDoubleOptIn\Utility;

use Symfony\Component\Mime\Address;

class AddressUtility
{
    /**
     * @param array $adresses
     * @return array
     */
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
            $value = new Address($value['email'], $value['name']);
        });
        return $adresses;
    }
}
