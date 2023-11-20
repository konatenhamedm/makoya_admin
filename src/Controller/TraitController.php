<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function Symfony\Component\String\u;

/**
 * I am using a PHP trait to isolate each snippet in a file.
 * This code should be called from a Symfony controller extending AbstractController (as of Symfony 4.2)
 * or Symfony\Bundle\FrameworkBundle\Controller\Controller (Symfony <= 4.1).
 * Services are injected in the main controller constructor.
 */
trait Snippet188Trait
{
    public function snippet188(Request $request): void
    {
        $ip = $request->getClientIp();
        if (u($ip)->isEmpty()) {
            echo 'IP not found 😞.';

            return;
        }

        try {
            $httpClient = HttpClient::createForBaseUri('https://ipgeolocation.abstractapi.com');
            $response = $httpClient->request('GET', '/v1', [
                'query' => [
                    'api_key' => $this->getParameter('abstract_api_key'), // your secret API key
                    'ip_address' => $ip, // If this parameter is not set, it uses the one of the current request
                ],
            ]);
            $data = $response->toArray();
        } catch (RedirectionExceptionInterface | ClientExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            echo 'Error when accessing the Abstract API service, sorry 😞, response code: ' . $e->getCode();

            return;
        }

        /** @var array{total_time: ?string} $info */
        $info = $response->getInfo();

        echo 'Your IP is: ' . ($data['ip_address'] ?? 'NA') . PHP_EOL;
        echo 'City: ' . ($data['city'] ?? 'NA') . PHP_EOL;
        echo 'Flag: ' . ($data['flag']['emoji'] ?? 'NA') . PHP_EOL;
        echo 'Total time: ' . ($info['total_time'] ?? 'NA') . ' sec' . PHP_EOL;

        // That's it! 😁
    }
}
