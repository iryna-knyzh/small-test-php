<?php

namespace TestProject\Src;

require 'vendor/autoload.php';

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class CommissionsHelper
{
    public const EU_COUNTRIES = [
        'EU', 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO',
        'SE', 'SI', 'SK',
    ];

    public const EU_COMMISSION = 0.01;
    public const NON_EU_COMMISSION = 0.02;
    public const LOOKUP_BIN_URL = 'https://lookup.binlist.net/';
    public const EXCHANGE_RATE_API_URL = 'https://api.apilayer.com/exchangerates_data/latest';
    public const EXCHANGE_RATE_API_KEY = 's5J5YIgPehM9ca0uDwTrguS4MGooFK70';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $fileName
     *
     * @return array
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getCommissions(string $fileName): array
    {
        $commissions = [];

        foreach (explode("\n", file_get_contents($fileName)) as $row) {
            if (empty($row)) {
                break;
            }
            $rowArr = json_decode($row, true, 512, JSON_THROW_ON_ERROR);

            $commissions[] = $this->getCommission($rowArr["bin"], $rowArr["amount"], $rowArr["currency"]);
        }

        return $commissions;
    }

    /**
     * @param $bin
     * @param $amount
     * @param $currency
     *
     * @return float|int
     * @throws Exception|GuzzleException
     */
    public function getCommission($bin, $amount, $currency)
    {
        $isEu = $this->isEu($this->getCountryShortName($bin));

        $amntFixed = $this->getAmntFixed($amount, $currency);

        return $this->ceilDecimals($amntFixed * ($isEu ? self::EU_COMMISSION : self::NON_EU_COMMISSION));
    }

    /**
     * @param float  $amount
     * @param string $currency
     *
     * @return float
     * @throws Exception|GuzzleException
     */
    public function getAmntFixed(float $amount, string $currency): float
    {
        $response = $this->client->get(self::EXCHANGE_RATE_API_URL, [
            RequestOptions::HEADERS => [
                'apikey' => self::EXCHANGE_RATE_API_KEY,
            ],
        ]);
        $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (empty($body) || $body['success'] === false) {
            throw new Exception('Exchange api error');
        }

        $rate = $body['rates'][$currency];

        return ($currency !== 'EUR' || $rate > 0) ? $amount / $rate : $amount;
    }

    /**
     * @param $bin
     *
     * @return mixed
     * @throws Exception|GuzzleException
     */
    public function getCountryShortName(string $bin): string
    {
        $response = $this->client->get(self::LOOKUP_BIN_URL . $bin);
        $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (empty($body)) {
            throw new Exception('Lookup bin api error');
        }

        return $body['country']['alpha2'];
    }

    /**
     * @param $country
     *
     * @return bool
     */
    public function isEu(string $country): bool
    {
        return in_array($country, self::EU_COUNTRIES, true);
    }


    /**
     * @param float $number
     *
     * @return float|int
     */
    public function ceilDecimals(float $number)
    {
        return ceil($number * 100) / 100;
    }
}
