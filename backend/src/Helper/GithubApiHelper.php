<?php

use Symfony\Component\HttpClient\HttpClient;

class GithubApiHelper
{
    /**
     * Simple github helper.
     * For test task it's look's like we not needed something more difficult
     * @param string $username
     * @return array
     */
    public static function showUser(string $username): array
    {
        $client = HttpClient::create();
        try {
            $response = $client->request('GET', 'http://api.github.com/users/' . $username);
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                $content = $response->getContent();
                return $content->toArray();
            } else {
                error_log('github connection error with status code:#' . $statusCode);
            }
        } catch (Throwable $throwable) {
            error_log($throwable->getMessage());
        }
        return [];
    }
}