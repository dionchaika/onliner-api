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
     * The access token.
     *
     * @var string
     */
    protected $accessToken;

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

        $uri = new Uri('https://www.onliner.by/sdapi/user.api/login');
        $request = $this->factory->createJsonRequest('POST', $uri, $data)
            ->withHeader('X-Api-Version', '2')
            ->withHeader('X-Onliner-Device', self::DEVICE_ID);

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Login error!');
        }

        $this->loggedIn = true;
        $this->accessToken = json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY)['access_token'];
    }

    /**
     * Log out.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->loggedIn = false;
        $this->client->getCookieStorage()->clearSessionCookies();
    }

    /**
     * Upload an image.
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

        $formData = (new FormData)
            ->append('file', '@'.$filename)
            ->append('meta[type]', 'apartment-for-sale-photo');

        $uri = (new Uri('https://upload.api.onliner.by/upload'))->withQuery('token='.$this->accessToken);
        try {
            $response = $this->client->sendRequest($this->factory->createFormDataRequest('POST', $uri, $formData));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Error uploading image!');
        }

        return json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);
    }
}
