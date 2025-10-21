<?php

namespace App\Infrastructure\Http\PhoneOperator\HtmlWeb;

use App\Application\PhoneOperator\Exception\PhoneOperatorException;
use App\Application\PhoneOperator\PhoneOperatorGetterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class HtmlWebPhoneOperatorGetter implements PhoneOperatorGetterInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string              $apiUrl,
        private string              $apiKey,
    ) {}

    public function get(string $phone): ?string
    {
        $query = [];

        if ($this->apiKey) {
            $query['api_key'] = $this->apiKey;
        }

        try {
            $response = $this->httpClient->request(
                'POST',
                sprintf('%s/%s', $this->apiUrl, $phone),
                [
                    'query' => $query,
                ]);

            $data = $response->toArray();
        } catch (\Throwable $e) {
            throw new PhoneOperatorException($e->getMessage(), 0, $e);
        }

        return $data['oper']['brand'] ?? null;
    }
}
