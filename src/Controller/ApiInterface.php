<?php

namespace App\Controller;

use App\Controller\FileTrait;
use App\Service\Menu;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/* #[Route('/apis')] */

class ApiInterface extends AbstractController
{
    use FileTrait;

    protected const UPLOAD_PATH = 'media_entreprise';
    protected $security;
    protected $userInterface;
    protected  $hasher;
    //protected  $utils;
    protected $em;

    protected $client;

    protected $serializer;

    public function __construct(EntityManagerInterface $em, HttpClientInterface $client, SerializerInterface $serializer)
    {


        //$this->utils = $utils;
        $this->client = $client;
        $this->em = $em;
        $this->serializer = $serializer;
    }


    /**
     * @var integer HTTP status code - 200 (OK) by default
     */
    protected $statusCode = 200;
    protected $message = "Operation effectuée avec succes";

    /**
     * Gets the value of statusCode.
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the value of statusCode.
     *
     * @param integer $statusCode the status code
     *
     * @return self
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }
    protected function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /*  public function serializer(){
        $context = [AbstractNormalizer::GROUPS => 'group1'];
        $json = $this->serializer->serialize($batis, 'json', $context);
    } */

    public function response($data, $headers = [])
    {
        // On spécifie qu'on utilise l'encodeur JSON
        $encoders = [new JsonEncoder()];

        // On instancie le "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // On instancie le convertisseur
        $serializer = new Serializer($normalizers, $encoders);


        if ($data == null) {
            $arrayData = [
                'data' => '[]',
                'message' => $this->getMessage(),
                'status' => $this->getStatusCode()
            ];
            $response = $this->json([
                'data' => $data,
                'message' => $this->getMessage(),
                'status' => $this->getStatusCode()

            ], 200);
            $response->headers->set('Access-Control-Allow-Origin', '*');
        } else {
            $arrayData = [
                'data' => $data,
                'message' => $this->getMessage(),
                'status' => $this->getStatusCode()
            ];
            $jsonContent = $serializer->serialize($arrayData, 'json', [
                'circular_reference_handler' => function ($object) {
                    return  $object->getId();
                },

            ]);
            // On instancie la réponse
            $response = new Response($jsonContent);
            //$response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }
        // dd($this->json($data));
        // On convertit en json
        // On ajoute l'entête HTTP

        return $response;
        //return new JsonResponse($response, $this->getStatusCode(), $headers);
    }
    public function responseTrue($data, $headers = [])
    {
        // On spécifie qu'on utilise l'encodeur JSON
        $encoders = [new JsonEncoder()];

        // On instancie le "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // On instancie le convertisseur
        $serializer = new Serializer($normalizers, $encoders);


        if ($data == null) {
            $arrayData = [
                'data' => '[]',
                'message' => $this->getMessage(),
                'status' => $this->getStatusCode()
            ];
            $response = $this->json([
                'data' => $data,
                'message' => $this->getMessage(),
                'status' => $this->getStatusCode()

            ], 200);
            $response->headers->set('Access-Control-Allow-Origin', '*');
        } else {
            $arrayData = [
                'data' => $data,
                'message' => $this->getMessage(),
                'status' => $this->getStatusCode()
            ];
            $jsonContent = $serializer->serialize($arrayData, 'json', [
                'circular_reference_handler' => function ($object) {
                    return  $object->getId();
                },

            ]);
            // On instancie la réponse
            $response = new Response($jsonContent);
            //$response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }
        // dd($this->json($data));
        // On convertit en json
        // On ajoute l'entête HTTP

        return $response;
        //return new JsonResponse($response, $this->getStatusCode(), $headers);
    }



    public function responseAdd($data, $headers = [])
    {
        return $this->json([
            'data' => $data,
            'message' => $this->getMessage(),
            'status' => $this->getStatusCode()

        ], 200);
    }


    public function responseNew($data = [], $group, $headers = [])
    {
        try {
            if ($data) {
                $context = [AbstractNormalizer::GROUPS => $group];
                $json = $this->serializer->serialize($data, 'json', $context);
                $response = new JsonResponse(['code' => 200, 'message' => $this->getMessage(), 'data' => json_decode($json)], 200, $headers);
            } else {
                $response = new JsonResponse(['code' => 200, 'message' => $this->getMessage(), 'data' => []], 200, $headers);
            }
        } catch (\Exception $e) {
            $response = new JsonResponse(['code' => 500, 'message' => $e->getMessage(), 'data' => []], 500, $headers);
        }

        return $response;
    }



    public function sendNotification($data = [])
    {
        $body = [
            "title" => $data['title'],
            "body" => $data['body'],
            "user_id" => $data['userId']
        ];

        $this->client->request(
            'POST',
            'https://api.freewan.store/auth-center/api/notifications/create',
            $body
        );
    }
}
