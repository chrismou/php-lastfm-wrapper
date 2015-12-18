<?php

namespace Chrismou\LastFm;

class LastFm
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var string
     */
    protected $sessionKey;

    /**
     * @var string
     */
    protected $apiUrl = 'http://ws.audioscrobbler.com/2.0/';

    /**
     * @var string
     */
    protected $authUrl = 'http://www.last.fm/api/auth/';

    /**
     * LastFm constructor.
     *
     * @param \GuzzleHttp\ClientInterface $httpClient
     * @param $apiKey
     * @param null $apiSecret
     * @param null $sessionKey
     */
    public function __construct(\GuzzleHttp\ClientInterface $httpClient, $apiKey, $apiSecret = null, $sessionKey = null)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->sessionKey = $sessionKey;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @param bool $doRequestAuth
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($method, array $parameters = [], $authRequired = false)
    {
        $method = str_replace('_', '.', $method);
        return $this->doRequest($method, $parameters, $authRequired);
    }

    /**
     * @param string $method
     * @param array $parameters
     * @param bool $doRequestAuth
     *
     * @return \stdClass
     * @throws \Exception
     */
    protected function doRequest($method, $parameters = [], $doRequestAuth = false)
    {
        // We automatically append a few parameters here.
        $parameters = array_merge(
            [
                'method' => $method,
                'format' => 'json',
                'api_key' => $this->apiKey,
            ],
            $parameters
        );

        // Do we need to authenticate the request ?
        if ($doRequestAuth) {
            $parameters = $this->buildParametersForAuthentictatedMethods($parameters);
        }

        $request = $this->httpClient->post(
            $this->apiUrl,
            [
                'form_params' => $parameters
            ]
        );

        $response = json_decode($request->getBody());

        // The JSON couldn't be decoded
        if ($response === null) {
            throw new \Exception("Error parsing JSON");
        }

        // An error has occurred
        if (!empty($response->error)) {
            throw new \Exception("[{$response->error}|{$response->message}] "
                .implode(', ', $response->links) . "\n" . http_build_query($parameters));
        }

        return $response;
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    protected function buildParametersForAuthentictatedMethods(array $parameters = [])
    {
        if (!empty($this->sessionKey)) {
            $parameters['sk'] = $this->sessionKey;
        }

        // Bug : you have to get rid of format parameter to compute the api_sig parameter.
        // http://www.last.fm/group/Last.fm+Web+Services/forum/21604/_/428269/1#f18907544
        $fixedParameters = $parameters;
        unset($fixedParameters['format']);

        ksort($fixedParameters);
        $signature = '';
        foreach ($fixedParameters as $k => $v) {
            $signature .= "$k$v";
        }
        $parameters['api_sig'] = md5($signature . $this->apiSecret);

        return $parameters;
    }
}
