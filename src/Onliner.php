<?php

namespace API\Onliner;

use RuntimeException;
use Dionchaika\Http\Uri;
use InvalidArgumentException;
use Dionchaika\Http\Client\Client;
use Dionchaika\Http\Utils\FormData;
use Dionchaika\Http\Factory\RequestFactory;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * The API class for www.onliner.by.
 */
class Onliner
{
    /**
     * The device ID.
     */
    const DEVICE_ID = '66d2a10174fba8def5a773f914908b4d';

    /**
     * The HTTP client.
     *
     * @var \Dionchaika\Http\Client\Client
     */
    protected $client;

    /**
     * The HTTP request factory.
     *
     * @var \Dionchaika\Http\Factory\RequestFactory
     */
    protected $factory;

    /**
     * Is the client logged in.
     *
     * @var bool
     */
    protected $loggedIn = false;

    /**
     * The array of session parameters.
     *
     * @var mixed[]
     */
    protected $sessionParams = [];

    /**
     * The API constructor.
     *
     * @param  bool  $debug
     * @param  string|null  $debugFile
     */
    public function __construct(bool $debug = false, ?string $debugFile = null)
    {
        $config = [

            'headers' => [

                'Accept'          => 'text/html, application/xhtml+xml, application/xml; q=0.9, image/webp, image/apng, */*; q=0.8, application/signed-exchange; v=b3',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'ru-RU, ru; q=0.9, en-US; q=0.8, en; q=0.7',

                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36'

            ],

            'redirects' => true,

            'debug'      => $debug,
            'debug_file' => $debugFile

        ];

        $this->client = new Client($config);
        $this->factory = new RequestFactory;
    }

    /**
     * Log in.
     *
     * @param  string  $user
     * @param  string  $password
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function login(string $user, string $password): void
    {
        $uri = new Uri('https://www.onliner.by/');
        try {
            $response = $this->client->sendRequest($this->factory->createRequest('GET', $uri));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error loading page: '.$uri.'!');
        }

        $data = [

            'login'    => $user,
            'password' => $password

        ];

        $this->client->setConfig([

            'headers' => [

                'Accept' => 'application/json, text/plain, */*',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'ru-RU, ru; q=0.9, en-US; q=0.8, en; q=0.7',

                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36'

            ]

        ]);

        $uri = new Uri('https://www.onliner.by/sdapi/user.api/login');
        $request = $this->factory->createJsonRequest('POST', $uri, $data)
            ->withHeader('X-Api-Version', '2')
            ->withHeader('X-Onliner-Device', self::DEVICE_ID);

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        $this->client->setConfig([

            'headers' => [

                'Accept' => 'text/html, application/xhtml+xml, application/xml; q=0.9, image/webp, image/apng, */*; q=0.8, application/signed-exchange; v=b3',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'ru-RU, ru; q=0.9, en-US; q=0.8, en; q=0.7',

                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36'

            ]

        ]);

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Login error!');
        }

        $this->loggedIn = true;
        $this->sessionParams = json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Log out.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->loggedIn = false;
        $this->sessionParams = [];
        $this->client->getCookieStorage()->clearSessionCookies();
    }

    /**
     * Upload an image.
     *
     * Return data example:
     *      <code>
     *          [
     *
     *              'id'         => 'ZMs3P9uwHN5jH8tB',
     *              'hash'       => 'd8c0e9882ca76491ec95d01ce062df05',
     *              'status'     => 'uploaded',
     *              'created_at' => '2019-06-04 14:55:15',
     *              'updated_at' => '2019-06-04 14:55:15',
     *              'errors'     => [],
     *
     *              'images' => [
     *
     *                  '1400x930' => 'https://content.onliner.by/apartment_for_sale/819328/1400x930/1d6d195d6159bc80205adf1d57e1d285.jpeg',
     *                  '600x400'  => 'https://content.onliner.by/apartment_for_sale/819328/600x400/1d6d195d6159bc80205adf1d57e1d285.jpeg'
     *
     *              ],
     *
     *              'sizes' => [
     *
     *                  '1400x930' => [
     *
     *                      'width'  => 800,
     *                      'height' => 600
     *
     *                  ],
     *
     *                  '600x400' => [
     *
     *                      'width'  => 533,
     *                      'height' => 400
     *
     *                  ]
     *
     *              ],
     *
     *              'url' => 'https://upload.api.onliner.by/upload/ZMs3P9uwHN5jH8tB'
     *
     *          ]
     *      </code>
     *
     * @param  string  $filename
     *
     * @return mixed[]
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function uploadImage(string $filename): array
    {
        if (!$this->loggedIn) {
            throw new RuntimeException('Client is not logged in!');
        }

        if (!file_exists($filename)) {
            throw new InvalidArgumentException('File does not exists: '.$filename.'!');
        }

        $uri = (new Uri('https://upload.api.onliner.by/upload'))->withQuery('token='.$this->sessionParams['access_token']);

        $this->client->setConfig([

            'headers' => [

                'Accept' => '*/*',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'ru-RU, ru; q=0.9, en-US; q=0.8, en; q=0.7',

                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36'

            ],

            'cookies' => false

        ]);

        $request = $this->factory->createRequest('OPTIONS', $uri)->withHeader('Access-Control-Request-Method', 'POST');

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error loading page: '.$uri.'!');
        }

        $formData = (new FormData)
            ->append('file', '@'.$filename)
            ->append('meta[type]', 'apartment-for-sale-photo');

        $this->client->setConfig([

            'headers' => [

                'Accept' => 'application/json, text/plain, */*',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'ru-RU, ru; q=0.9, en-US; q=0.8, en; q=0.7',

                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36'

            ],

            'cookies' => true

        ]);

        $request = $this->factory->createFormDataRequest('POST', $uri, $formData)
            ->withAddedHeader('Cookie', 'access_token='.$this->sessionParams['access_token'])
            ->withAddedHeader('Cookie', 'refresh_token='.$this->sessionParams['refresh_token']);

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (202 !== $response->getStatusCode()) {
            throw new RuntimeException('Error uploading image!');
        }

        $body = json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);

        $uri = (new Uri('https://upload.api.onliner.by/upload/'.$body['id']))->withQuery('v=0.3906445161102299');
        $request = $this->factory->createRequest('GET', $uri)
            ->withAddedHeader('Cookie', 'access_token='.$this->sessionParams['access_token'])
            ->withAddedHeader('Cookie', 'refresh_token='.$this->sessionParams['refresh_token']);

        while (true) {
            try {
                $response = $this->client->sendRequest($request);
            } catch (ClientExceptionInterface $e) {
                throw new RuntimeException($e->getMessage());
            }

            if (200 !== $response->getStatusCode()) {
                throw new RuntimeException('Error uploading image!');
            }

            $body = json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);
            if ('processed' === $body['status']) {
                break;
            }
        }

        $this->client->setConfig([

            'headers' => [

                'Accept' => 'text/html, application/xhtml+xml, application/xml; q=0.9, image/webp, image/apng, */*; q=0.8, application/signed-exchange; v=b3',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'ru-RU, ru; q=0.9, en-US; q=0.8, en; q=0.7',

                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36'

            ]

        ]);

        return json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Get the address info.
     *
     * Return data example:
     *      <code>
     *          [
     *
     *              'latitude'     => '52.0710712',
     *              'longitude'    => '23.7087247',
     *              'address'      => 'Минск, улица Советцкая, 33',
     *              'user_address' => 'город Минск улица Советцкая 33'
     *
     *          ]
     *      </code>
     *
     * @param  string  $address
     *
     * @return mixed[]
     *
     * @throws \RuntimeException
     */
    public function getAddressInfo(string $address): array
    {
        if (!$this->loggedIn) {
            throw new RuntimeException('Client is not logged in!');
        }

        $query = 'q='.rawurlencode($address)
            .'&limit=10'
            .'&format=json'
            .'&countrycodes=by'
            .'&addressdetails=1'
            .'&accept-language=ru'
            .'&v=0.7767891281155128';

        $uri = (new Uri('https://nominatim.openstreetmap.org/search'))->withQuery($query);
        try {
            $response = $this->client->sendRequest($this->factory->createRequest('GET', $uri));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error getting address info!');
        }

        $data = json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);

        if (empty($data)) {
            return [

                'latitude'     => '',
                'longitude'    => '',
                'address'      => '',
                'user_address' => ''

            ];
        }

        $chainedAddress = $data[0]['address']['city'].', '.$data[0]['address']['road']
            .(isset($data[0]['address']['house_number']) ? ', '.$data[0]['address']['house_number'] : '');

        return [

            'latitude'     => $data[0]['lat'],
            'longitude'    => $data[0]['lon'],
            'address'      => $chainedAddress,
            'user_address' => $address

        ];
    }
}
