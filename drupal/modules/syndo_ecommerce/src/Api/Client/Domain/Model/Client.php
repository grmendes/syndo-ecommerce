<?php

namespace Drupal\syndo_ecommerce\Api\Client\Domain\Model;

use Drupal\syndo_ecommerce\Api\Client\ClientEndpointInterface;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $cep;

    /**
     * @var string
     */
    protected $phone1;

    /**
     * @var string
     */
    protected $phone2;

    /**
     * @var string
     */
    protected $cpf;

    /**
     * @var string
     */
    protected $sex;

    /**
     * @var string
     */
    protected $birthday;

    /**
     * Client constructor.
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $cep
     * @param string $phone1
     * @param string $phone2
     * @param string $cpf
     * @param string $sex
     * @param string $birthday
     */
    public function __construct(string $id, string $name, string $email, string $cep, string $phone1, string $phone2, string $cpf, string $sex, string $birthday)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->cep = $cep;
        $this->phone1 = $phone1;
        $this->phone2 = $phone2;
        $this->cpf = $cpf;
        $this->sex = $sex;
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCep(): string
    {
        return $this->cep;
    }

    /**
     * @return string
     */
    public function getPhone1(): string
    {
        return $this->phone1;
    }

    /**
     * @return string
     */
    public function getPhone2(): string
    {
        return $this->phone2;
    }

    /**
     * @return string
     */
    public function getCpf(): string
    {
        return $this->cpf;
    }

    /**
     * @return string
     */
    public function getSex(): string
    {
        return $this->sex;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @throws GuzzleException
     */
    public function save()
    {
        $httpClient = new HttpClient([
            'base_uri' => ClientEndpointInterface::API_HOST,
        ]);

        try {
            $body = json_encode([
                'name' => $this->name,
                'email' => $this->email,
                'cep' => str_replace('-', '', $this->cep),
                'phone1' => $this->phone1,
                'cpf' => $this->cpf,
                'sex' => $this->sex === 'male',
                'birthday' => stripslashes($this->birthday),
                'password' => 'senhamestra',
            ]);

            $response = $httpClient->request(
                'POST',
                ClientEndpointInterface::CLIENT_ENDPOINT,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => $body,
                ]
            );

            $responseData = json_decode($response->getBody()->getContents(), true);
            $this->id = $responseData['Client ID'];

        } catch (GuzzleException $e) {
            throw $e;
        }
    }

    /**
     * @param string $id
     *
     * @return Client
     *
     * @throws GuzzleException
     */
    public static function getById(string $id)
    {
        $httpClient = new HttpClient([
            'base_uri' => ClientEndpointInterface::API_HOST,
        ]);

        try {

            $response = $httpClient->request(
                'GET',
                ClientEndpointInterface::CLIENT_ENDPOINT,
                [
                    'query' => [
                        'clientid' => $id,
                    ],
                ]
            );

            $responseData = json_decode($response->getBody()->getContents(), true)['data'][0];

        } catch (GuzzleException $e) {
            throw $e;
        }

        return new self(
            $id,
            $responseData['name'],
            $responseData['email'],
            (string) $responseData['cep'],
            $responseData['phone1'],
            $responseData['phone2'],
            $responseData['cpf'],
            $responseData['sex'] ? 'male' : 'female',
            $responseData['birthday']
        );
    }
}