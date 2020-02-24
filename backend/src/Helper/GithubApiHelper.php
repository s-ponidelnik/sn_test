<?php

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GithubApiHelper
{
    /**
     * Simple github helper.
     * For test task it's look's like we not needed something more difficult
     * @param string $username
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function showUser(string $username): array
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'http://api.github.com/users/' . $username);
        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            $content = $response->getContent();
            if (is_string($content)) {
                return json_decode($content, TRUE);
            }
            if (is_object($content)) {//it's a strange logic of httpClient detect json
                return $content->toArray();
            }
        } else {
            error_log('github connection error with status code:#' . $statusCode);
        }

        return [];
    }
}