<?php

namespace App\Payments\Processors\Cielo;

use App\Payments\Contracts\PaymentProcessorInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PixCielo implements PaymentProcessorInterface
{
  protected Client $http;

  public function __construct()
  {
    $this->http = new Client([
      'base_uri' => config('gateway.cielo.api_url'),
      'headers' => [
        'Content-Type' => 'application/json',
        'MerchantId'   => config('gateway.cielo.merchant_id'),
        'MerchantKey'  => config('gateway.cielo.merchant_key'),
      ]
    ]);
  }

  public function createCustomer(array $data): mixed
  {
    return [
      'name' => $data['name'] ?? 'Cliente',
    ];
  }

  public function processPayment(array $data): mixed
  {
    $body = [
      'MerchantOrderId' => $data['order_id'] ?? uniqid(),
      'Customer' => [
        'Name' => $data['name'] ?? 'Cliente',
      ],
      'Payment' => [
        'Type'   => 'Pix',
        'Amount' => (int)($data['amount'] * 100), // em centavos
      ],
    ];

    try {
      $response = $this->http->post('1/sales/', [
        'json' => $body
      ]);

      $json = json_decode($response->getBody()->getContents(), true);

      return [
        'payment_id'    => $json['Payment']['PaymentId'] ?? null,
        'qrcode_payload'=> $json['Payment']['QrCode'] ?? null,
        'status'        => $json['Payment']['Status'] ?? null,
        'raw'           => $json,
      ];
    } catch (\Throwable $e) {
      Log::error('Cielo Pix payment failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      return [
        'error' => 'Erro ao processar pagamento Pix',
        'message' => $e->getMessage()
      ];
    }
  }

  public function processRefund(string $paymentId, float $amount): mixed
  {
    try {
      $response = $this->http->put("1/sales/{$paymentId}/void", [
        'json' => [
          'amount' => (int)($amount * 100),
        ]
      ]);

      return json_decode($response->getBody()->getContents(), true);
    } catch (\Throwable $e) {
      Log::error('Cielo Pix refund failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      return [
        'error' => 'Erro ao cancelar pagamento Pix',
        'message' => $e->getMessage()
      ];
    }
  }
}
