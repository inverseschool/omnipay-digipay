<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay\Message;

use Exception;
use RuntimeException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Digipay\Cache;
use GuzzleHttp\Psr7\MultipartStream;
/**
 * Class AbstractRequest
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * Live Endpoint URL
     *
     * @var string URL
     */
    protected $liveEndpoint = 'https://api.mydigipay.com/digipay/api';

    /**
     * @return string
     */
    abstract protected function getHttpMethod();

    /**
     * @param string $endpoint
     * @return string
     */
    abstract protected function createUri(string $endpoint);

    /**
     * @param array $data
     * @return AbstractResponse
     */
    abstract protected function createResponse(array $data);


    public function getUsername():string
    {
        return $this->getParameter('username');
    }

    /**
     * @param string $value
     * @return self
     */
    public function setUsername(string $value): self
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword():string
    {
        return $this->getParameter('password');
    }
    /**
     * @param string $value
     * @return self
     */
    public function setPassword(string $value): self
    {
        return $this->setParameter('password', $value);
    }

    public function getClientId():string
    {
        return $this->getParameter('clientId');
    }
    /**
     * @param string $value
     * @return self
     */
    public function setClientId(string $value): self
    {
        return $this->setParameter('clientId', $value);
    }

    public function getClientSecret():string
    {
        return $this->getParameter('clientSecret');
    }
    /**
     * @param string $value
     * @return self
     */
    public function setClientSecret(string $value): self
    {
        return $this->setParameter('clientSecret', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setPhoneNumber(string $value): self
    {
        return $this->setParameter('phoneNumber', $value);
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): ?string
    {
        return $this->getParameter('phoneNumber');
    }



    //-----------------------------------------------------------
    // PROTECTED FUNCTIONS

    protected function getAuthorizationKey(): string
    {
        return base64_encode($this->getClientId() . ':' . $this->getClientSecret());
    }
    //-----------------------------------------------------------
    // PROTECTED FUNCTIONS
    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->getParameter('cache');
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->getParameter('refreshToken');
    }

    /**
     * @return string
     * @throws InvalidRequestException
     */
    public function getAmount()
    {
        $value = $this->getParameter('amount');
        $value = $value ?: $this->httpRequest->query->get('Amount');
        return (int)$value;
    }


    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->getParameter('userId');
    }

    /**
     * @param Cache $cache
     * @return static
     */
    public function setCache(Cache $cache)
    {
        return $this->setParameter('cache', $cache);
    }

    /**
     * @param string $value
     * @return static
     */
    public function setApiKey(string $value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @param string $value
     * @return static
     */
    public function setSecretKey(string $value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * @param string $accessToken
     * @return static
     */
    public function setAccessToken(string $accessToken)
    {
        return $this->setParameter('accessToken', $accessToken);
    }

    /**
     * @param string $refreshToken
     * @return static
     */
    public function setRefreshToken(string $refreshToken)
    {
        return $this->setParameter('refreshToken', $refreshToken);
    }

    /**
     * @param mixed $userId
     * @return static
     */
    public function setUserId($userId)
    {
        return $this->setParameter('userId', $userId);
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        if ($this->getTestMode()) {
            throw new \InvalidArgumentException('Digipay payment gateway does not support test mode.');
        }
        return $this->liveEndpoint;
    }

    /**
     * @param bool $isForce
     * @return string
     * @throws Exception
     */
    private function generateToken($isForce = false)
    {
        $cache = $this->getCache();

        $cache->eraseExpired();

        if ($isForce === false && $cache->isCached('accessToken')) {
            $accessToken = $cache->retrieve('accessToken');
            if ($accessToken !== null) {
                $this->setAccessToken($accessToken);
                return $accessToken;
            }
        }

        if ($cache->isCached('refreshToken')) {
            if (!$this->refreshTokens()) {
                return $this->generateNewToken();
            }
        }

        return $this->generateNewToken();
    }

    /**
     * Refresh access token
     *
     * @return bool
     * @throws RuntimeException
     * @throws InvalidResponseException
     */
    private function refreshTokens()
    {
        $cache = $this->getCache();

        try {
            $accessToken = $cache->retrieve('accessToken');

            if ($accessToken === null) {
                throw new RuntimeException('Err in access token.');
            }

            $body = json_encode([
                'accessToken' => str_replace('Bearer ', '', $accessToken),
                'refreshToken' => $cache->retrieve('refreshToken'),
            ]);

            if ($body === false) {
                throw new RuntimeException('Err in access/refresh token.');
            }

            $httpResponse = $this->httpClient->request(
                'POST',
                $this->getEndpoint() . '/oauth/token',
                [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                ],
                $body
            );
            $json = $httpResponse->getBody()->getContents();
            $result = !empty($json) ? json_decode($json, true) : [];

            if (empty($result['accessToken'])) {
                throw new RuntimeException('Err in refresh token.');
            }

            $cache->store('accessToken', 'Bearer ' . $result['access_token'], $result['expires_in']);
            $cache->store('refreshToken', $result['refresh_token'], $result['expires_in']);
            $this->setAccessToken('Bearer ' . $result['access_token']);
            $this->setRefreshToken($result['refresh_token']);
            return true;
        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * Generate new access token
     *
     * @return string
     */
    private function generateNewToken()
    {

        $body = [
            [
                'name'     => 'username',
                'contents' => $this->getUsername(),
            ],
            [
                'name'     => 'password',
                'contents' => $this->getPassword(),
            ],
            [
                'name'     => 'grant_type',
                'contents' => 'password',
            ]
        ];

        $multipartStream = new MultipartStream($body);

        try {
            $httpResponse = $this->httpClient->request(
                'POST',
                $this->getEndpoint() . '/oauth/token',
                [
                    'Accept' => 'application/json',
                    'Content-type' => 'multipart/form-data; boundary='.$multipartStream->getBoundary(),
                    'Authorization' => 'Basic ' . $this->getAuthorizationKey()
                ],
                $multipartStream
            );

            $json = $httpResponse->getBody()->getContents();
            $result = !empty($json) ? json_decode($json, true) : [];

            if (empty($result['access_token'])) {
                throw new \RuntimeException('Err in generate new token.');
            }

            $this->setAccessToken('Bearer ' . $result['access_token']);
            $this->setRefreshToken($result['refresh_token']);
            return 'ok';
        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * Send the request with specified data
     *
     * @param mixed $data The data to send.
     * @return ResponseInterface
     * @throws InvalidResponseException
     */
    public function sendData($data)
    {
        $this->generateToken(true);
        $accessToken = $this->getAccessToken();

        try {
            $body = json_encode($data);

            if ($body === false) {
                throw new RuntimeException('Err while json encoding data.');
            }


            $httpResponse = $this->httpClient->request(
                $this->getHttpMethod(),
                $this->createUri($this->getEndpoint()),
                [
                    'Accept' => 'application/json',
                    'Agent' => 'WEB',
                    'Digipay-Version' =>  '2022-02-02',
                    'Content-Type' => 'application/json',
                    'Authorization' => $accessToken,
                ],
                $body
            );
            $json = $httpResponse->getBody()->getContents();

            $result = !empty($json) ? json_decode($json, true) : [];
            $result['httpStatus'] = $httpResponse->getStatusCode();
            if (isset($result['errors']) && $result['errors'][0]['code'] === 'security.auth_required') {
                $this->generateToken(true);
                $retries = !isset($data['retries']) ? 0 : (int)$data['retries'];
                if ($retries <= 0) {
                    $data['retries'] = 1;
                    return $this->sendData($data);
                }
                $result['httpStatus'] = 401;
            }
            return $this->response = $this->createResponse($result);
        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }
}
