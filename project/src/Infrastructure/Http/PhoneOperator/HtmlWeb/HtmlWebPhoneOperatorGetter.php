<?php

namespace App\Infrastructure\Http\PhoneOperator\HtmlWeb;

use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\PhoneOperator\PhoneOperatorGetterInterface;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class HtmlWebPhoneOperatorGetter implements PhoneOperatorGetterInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string              $apiUrl,
        private string              $apiKey,
    ) {}

    public function get(string $phone): ?PhoneOperator
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

        $operator = $data['oper']['brand'] ?? null;

        return $operator ? PhoneOperator::fromPhoneOperator($operator) : null;
    }
}
