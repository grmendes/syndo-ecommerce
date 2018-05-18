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
     * @var string
     */
    protected $address;

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
     * @param string $address
     */
    public function __construct($id, $name, $email, $cep, $phone1, $phone2, $cpf, $sex, $birthday, $address)
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
        $this->address = $address;
    }

    public function save()
    {
        $httpClient = new HttpClient([
            'base_uri' => ClientEndpointInterface::API_HOST,
        ]);

        try {
            $body = json_encode([
                'name' => $this->name,
                'email' => $this->email,
                'cep' => $this->cep,
                'telephone' => $this->phone1,
                'cpf' => $this->cpf,
                'gender' => $this->sex,
                'birthdate' => $this->birthday,
                'password' => 'senhamestra',
                'samePass' => 'senhamestra',
                'address' => $this->address,
            ]);

            error_log($body);
            $response = $httpClient->request(
                'POST',
                ClientEndpointInterface::CLIENT_ENDPOINT,
                [
                    'headers' => [
                        'api_key' => 'abc',
                        'Content-Type' => 'application/json',
                    ],
                    'body' => $body,
                ]
            );

            $this->id = $response->getBody()->getContents();

            error_log($this->id);
        } catch (GuzzleException $e) {
            drupal_set_message('An error has occurred while connecting to webservice. Please try again');
            error_log($e->getMessage());
        }
    }
}