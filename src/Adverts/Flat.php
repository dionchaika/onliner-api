<?php

namespace API\Onliner\Adverts;

use API\Onliner\Finder;
use Dionchaika\Http\Uri;
use API\Onliner\AdvertInterface;
use Psr\Http\Message\RequestInterface;
use Dionchaika\Http\Factory\RequestFactory;

/**
 * The flat advert class.
 */
class Flat implements AdvertInterface
{
    /**
     * The walling type select.
     */
    const WALLING_TYPE = [

        'panel'    => 'Панель',
        'brick'    => 'Кирпич',
        'monolith' => 'Монолит',
        'block'    => 'Блок'

    ];

    /**
     * The parking type select.
     */
    CONST PARKING_TYPE = [

        'street' => 'На улице',
        'garage' => 'В гараже'

    ];

    /**
     * The currency type select.
     */
    const CURRENCY_TYPE = [

        'USD' => 'Доллары США',
        'BYN' => 'Белорусские рубли'

    ];

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
     * @param  string[]  $images
     * @param  bool  $resale
     * @param  string  $walling
     * @param  bool  $repaired
     * @param  int  $rooms
     * @param  bool  $balcony
     * @param  int  $floor
     * @param  int  $numberOfFloors
     * @param  bool  $combinedKitchen
     * @param  int  $priceAmount
     * @param  string  $priceCurrency
     * @param  string  $sellerName
     * @param  string  $sellerUnp
     * @param  string  $contactName
     * @param  string|null  $parking
     * @param  float|null  $totalArea
     * @param  float|null  $livingArea
     * @param  float|null  $kitchenArea
     * @param  float|null  $wallHeight
     * @param  int|null  $buildingYear
     * @param  string|null  $description
     * @param  string[]  $contactPhones
     * @param  int  $callTimeFrom
     * @param  int  $callTimeTo
     */
    public function __construct(
        array $images,
        bool $resale,
        string $walling,
        bool $repaired,
        int $rooms,
        bool $balcony,
        int $floor,
        int $numberOfFloors,
        bool $combinedKitchen,
        int $priceAmount,
        string $priceCurrency,
        string $sellerName,
        string $sellerUnp,
        string $contactName,
        ?string $parking = null,
        ?float $totalArea = null,
        ?float $livingArea = null,
        ?float $kitchenArea = null,
        ?float $wallHeight = null,
        ?int $buildingYear = null,
        ?string $description = null,
        array $contactPhones = [],
        int $callTimeFrom = 9,
        int $callTimeTo = 18
    ) {
        $this->data['photos'] = $images;
        $this->data['resale'] = $resale ? 1 : null;
        $this->data['walling'] = $walling;

        if ($repaired) {
            $this->data['facilities'][] = 'repaired';
        }

        if (1 > $rooms) {
            $rooms = 1;
        } else if (6 < $rooms) {
            $rooms = 6;
        }

        $this->data['number_of_rooms'] = $rooms;

        if ($balcony) {
            $this->data['facilities'][] = 'balcony';
        }

        $this->data['parking'] = $parking;

        if (1 > $floor) {
            $floor = 1;
        } else if (100 < $floor) {
            $floor = 100;
        }

        $this->data['floor'] = $floor;

        if (1 > $numberOfFloors) {
            $numberOfFloors = 1;
        } else if (100 < $numberOfFloors) {
            $numberOfFloors = 100;
        }

        $this->data['number_of_floors'] = $numberOfFloors;

        $this->data['price']['amount'] = round((float)$priceAmount, 2);
        $this->data['price']['currency'] = $priceCurrency;
        $this->data['seller']['name'] = $sellerName;
        $this->data['seller']['legal_info']['unp'] = $sellerUnp;
        $this->data['contact']['name'] = $contactName;
        $this->data['area']['total'] = $totalArea;
        $this->data['area']['living'] = $livingArea;
        $this->data['area']['kitchen'] = $combinedKitchen ? null : $kitchenArea;
        $this->data['wall_height'] = $wallHeight;

        if (1900 > $buildingYear) {
            $buildingYear = 1900;
        } else if (2029 < $buildingYear) {
            $buildingYear = 2029;
        }

        $this->data['building_year'] = $buildingYear;

        $this->data['description'] = trim($description);
        $this->data['contact']['phones'] = array_slice($contactPhones, 0, 3);

        if (0 > $callTimeFrom) {
            $callTimeFrom = 0;
        } else if (23 < $callTimeFrom) {
            $callTimeFrom = 23;
        }

        $this->data['contact']['call_time'][] = $callTimeFrom;

        if (0 > $callTimeTo) {
            $callTimeTo = 0;
        } else if (23 < $callTimeTo) {
            $callTimeTo = 23;
        }

        $this->data['contact']['call_time'][] = $callTimeTo;
    }

    /**
     * Find the walling type by name.
     *
     * @param  string  $wallingTypeName
     *
     * @return string
     */
    public static function findWallingTypeByName(string $wallingTypeName): string
    {
        return Finder::suggestKey($wallingTypeName, static::WALLING_TYPE);
    }

    /**
     * Find the parking type by name.
     *
     * @param  string  $parkingTypeName
     *
     * @return string
     */
    public static function findParkingTypeByName(string $parkingTypeName): string
    {
        return Finder::suggestKey($parkingTypeName, static::PARKING_TYPE);
    }

    /**
     * Find the currency type by name.
     *
     * @param  string  $currencyTypeName
     *
     * @return string
     */
    public static function findCurrencyTypeByName(string $currencyTypeName): string
    {
        return Finder::suggestKey($currencyTypeName, static::CURRENCY_TYPE);
    }

    /**
     * Set an address info.
     *
     * @param  mixed[]  $addressInfo
     *
     * @return self
     */
    public function setAddressInfo(array $addressInfo): self
    {
        $this->data['location']['address'] = $addressInfo['address'];
        $this->data['location']['user_address'] = $addressInfo['user_address'];
        $this->data['location']['latitude'] = $addressInfo['latitude'];
        $this->data['location']['longitude'] = $addressInfo['longitude'];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): RequestInterface
    {
        $uri = new Uri('https://pk.api.onliner.by/apartments');
        return (new RequestFactory)->createJsonRequest('POST', $uri, $this->data, [\JSON_PRETTY_PRINT, \JSON_NUMERIC_CHECK, \JSON_UNESCAPED_SLASHES, \JSON_UNESCAPED_UNICODE]);
    }
}
