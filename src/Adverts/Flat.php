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

            'type' => 'agent',
            'name' => null,

            'legal_info' => [

                'unp' => null

            ]

        ],

        'wall_height' => null,
        'walling'     => null

    ];

    /**
     * The flat advert constructor.
     *
     * @param  int  $accountId
     * @param  string[]  $images
     * @param  bool  $resale
     * @param  string  $walling
     * @param  bool  $repaired
     * @param  int  $rooms
     * @param  bool  $balcony
     * @param  string  $parking
     * @param  int  $floor
     * @param  int  $numberOfFloors
     * @param  bool  $combinedKitchen
     * @param  int  $priceAmount
     * @param  string  $priceCurrency
     * @param  string  $sellerName
     * @param  string  $sellerUnp
     * @param  string  $contactName
     * @param  float|null  $totalArea
     * @param  float|null  $livingArea
     * @param  float|null  $kitchenArea
     * @param  float|null  $wallHeight
     * @param  int|null  $buildingYear
     * @param  string|null  $description
     * @param  string[]  $contactPhones
     * @param  int|null  $callTimeFrom
     * @param  int|null  $callTimeTo
     */
    public function __construct(
        int $accountId,
        array $images,
        bool $resale,
        string $walling,
        bool $repaired,
        int $rooms,
        bool $balcony,
        string $parking,
        int $floor,
        int $numberOfFloors,
        bool $combinedKitchen,
        int $priceAmount,
        string $priceCurrency,
        string $sellerName,
        string $sellerUnp,
        string $contactName,
        ?float $totalArea = null,
        ?float $livingArea = null,
        ?float $kitchenArea = null,
        ?float $wallHeight = null,
        ?int $buildingYear = null,
        ?string $description = null,
        array $contactPhones = [],
        ?int $callTimeFrom = null,
        ?int $callTimeTo = null
    ) {
        //
    }
}
