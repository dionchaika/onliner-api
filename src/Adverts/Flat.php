<?php

namespace API\Onliner\Adverts;

use API\Onliner\Finder;
use Dionchaika\Http\Uri;
use InvalidArgumentException;
use API\Onliner\AdvertInterface;
use Psr\Http\Message\RequestInterface;
use Dionchaika\Http\Factory\RequestFactory;

/**
 * The flat advert class.
 */
class Flat implements AdvertInterface
{
    /**
     * The array
     * of flat advert data.
     *
     * @var mixed[]
     */
    protected $data = [

        'area' => [

            'total'   => null,
            'living'  => null,
            'kitchen' => null

        ],

        'building_year' => null,

        'contact' => [

            'call_time' => [9, 18],
            'name'      => null,
            'phones'    => []

        ],

        'description'      => null,
        'facilities'       => [],
        'floor'            => null,
        'number_of_floors' => null,
        'id'               => null,

        'location' => [

            'address'      => null,
            'user_address' => null,
            'latitude'     => null,
            'longitude'    => null

        ],

        'parking' => null,
        'photos'  => [],

        'price' => [

            'amount'   => null,
            'currency' => null

        ],

        'resale'          => null,
        'number_of_rooms' => null,

        'seller' => [

            'type' => null,
            'name' => null,

            'legal_info' => [

                'unp' => null

            ]

        ],

        'wall_height' => null,
        'walling'     => null

    ];
}
