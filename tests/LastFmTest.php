<?php

namespace Chrismou\LastFm\Tests;

use Chrismou\LastFm\LastFm;
use PHPUnit_Framework_TestCase;
use \Mockery as m;

class LastFmTest extends PHPUnit_Framework_TestCase
{
    /** @var \Mockery\MockInterface */
    protected $mockHttpClient;

    /** @var \Mockery\MockInterface */
    protected $mockResponseObject;

    /** @var string */
    protected $dummyApiKey = '12345';

    /** @var string */
    protected $dummyApiSecret = 'abcde';

    /** @var array */
    protected $dummyParameters;

    /** @var string */
    protected $dummyMethod;

    /** @var string */
    protected $dummyArtist;

    /** @var \Chrismou\LastFm\LastFm */
    protected $lastfm;

    public function setUp()
    {
        $this->mockHttpClient = m::mock(
            'GuzzleHttp\ClientInterface',
            ['post' => null]
        );

        $this->mockResponseObject = m::mock(
            'response',
            ['getBody' => null]
        );

        $this->dummyMethod = 'artist.getInfo';

        $this->dummyArtist = 'Sam Smith';

        $this->dummyParameters = [
            'method' => $this->dummyMethod,
            'format' => 'json',
            'api_key' => $this->dummyApiKey
        ];

        $this->lastfm = new LastFm($this->mockHttpClient, $this->dummyApiKey, $this->dummyApiSecret);
    }

    /**
     * @test
     */
    public function it_makes_a_request()
    {
        $this->mockHttpClient->shouldReceive('post')
            ->once()
            ->with(
                'http://ws.audioscrobbler.com/2.0/',
                [
                    'form_params' => [
                        'method' => $this->dummyMethod,
                        'format' => "json",
                        'api_key' => $this->dummyApiKey,
                        'artist' => $this->dummyArtist,
                    ]
                ]
            )
            ->andReturn($this->mockResponseObject);

        $this->mockResponseObject->shouldReceive('getBody')
            ->andReturn($this->generateLastfmResponse());

        $this->lastfm->get($this->dummyMethod, ['artist' => $this->dummyArtist]);
    }

    /**
     * @test
     */
    public function it_adds_auth_parameters_when_required()
    {
        $params = [
            'method' => $this->dummyMethod,
            'format' => "json",
            'api_key' => $this->dummyApiKey,
            'artist' => $this->dummyArtist,
            'api_sig' => '97ad93796f578cd6e03f90f0819b9a3b',
        ];

        $this->mockHttpClient->shouldReceive('post')
            ->once()
            ->with(
                'http://ws.audioscrobbler.com/2.0/',
                [
                    'form_params' => $params
                ]
            )
            ->andReturn($this->mockResponseObject);

        $this->mockResponseObject->shouldReceive('getBody')
            ->andReturn($this->generateLastfmResponse());

        $this->lastfm->get($this->dummyMethod, ['artist' => $this->dummyArtist], true);
    }

    /**
     * @test
     */
    public function it_adds_session_key_parameter_when_set()
    {
        $params = [
            'method' => $this->dummyMethod,
            'format' => "json",
            'api_key' => $this->dummyApiKey,
            'artist' => $this->dummyArtist,
            'api_sig' => 'dd115d455d62a20fa4d397d6c57721e2',
            'sk' => 'qwerty',
        ];

        $this->mockHttpClient->shouldReceive('post')
            ->once()
            ->with(
                'http://ws.audioscrobbler.com/2.0/',
                [
                    'form_params' => $params
                ]
            )
            ->andReturn($this->mockResponseObject);

        $this->mockResponseObject->shouldReceive('getBody')
            ->andReturn($this->generateLastfmResponse());

        $lastfm = new LastFm($this->mockHttpClient, $this->dummyApiKey, $this->dummyApiSecret, 'qwerty');

        $lastfm->get($this->dummyMethod, ['artist' => $this->dummyArtist], true);
    }

    /**
     * @test
     * @expectedException \Chrismou\LastFm\Exception\ResponseMalformedException
     */
    public function it_throws_an_exception_when_invalid_json()
    {
        $this->mockHttpClient->shouldReceive('post')
            ->once()
            ->andReturn($this->mockResponseObject);

        $this->mockResponseObject->shouldReceive('getBody')
            ->andReturn('notjson');

        $this->lastfm->get($this->dummyMethod, ['artist' => $this->dummyArtist]);
    }

    /**
     * @test
     * @expectedException \Chrismou\LastFm\Exception\ResponseErrorException
     */
    public function it_throws_an_exception_when_api_returns_error()
    {
        $this->mockHttpClient->shouldReceive('post')
            ->once()
            ->andReturn($this->mockResponseObject);

        $this->mockResponseObject->shouldReceive('getBody')
            ->andReturn(json_encode([
                'error' => 'error',
                'message' => 'The error message',
                'links' => []
            ]));

        $this->lastfm->get($this->dummyMethod, ['artist' => $this->dummyArtist]);
    }

    public function generateLastfmResponse()
    {
        $response = [];
        $response['artist'] = [];
        $response['artist']['name'] = $this->dummyArtist;

        return json_encode($response);
    }

    /**
     * Custom teardown to include mockery expectations as assertions
     */
    public function tearDown()
    {
        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        \Mockery::close();

        parent::tearDown();
    }
}
