<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowPaymentsService
{
    protected $apiKey;
    protected $baseUrl;
    protected $sandbox;

    public function __construct()
    {
        $this->apiKey = config('services.nowpayments.api_key');
        $this->baseUrl = config('services.nowpayments.base_url');
        $this->sandbox = config('services.nowpayments.sandbox');
    }

    /**
     * Get available cryptocurrencies
     */
    public function getCurrencies()
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/currencies');

            if ($response->successful()) {
                return $response->json();
            }

            // Handle error responses
            $errorData = $response->json();
            Log::error('NOWPayments API Error: ' . $response->body());
            
            return [
                'error' => $errorData['message'] ?? 'Failed to get currencies',
                'code' => $errorData['code'] ?? $response->status(),
                'status' => false
            ];
        } catch (\Exception $e) {
            Log::error('NOWPayments Service Error: ' . $e->getMessage());
            return [
                'error' => 'Network error: ' . $e->getMessage(),
                'code' => 500,
                'status' => false
            ];
        }
    }

    /**
     * Get minimum payment amount
     */
    public function getMinPaymentAmount($currencyFrom, $currencyTo)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/min-amount', [
                'currency_from' => $currencyFrom,
                'currency_to' => $currencyTo,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Handle error responses
            $errorData = $response->json();
            Log::error('NOWPayments Min Amount Error: ' . $response->body());
            
            return [
                'error' => $errorData['message'] ?? 'Failed to get minimum amount',
                'code' => $errorData['code'] ?? $response->status(),
                'status' => false
            ];
        } catch (\Exception $e) {
            Log::error('NOWPayments Min Amount Error: ' . $e->getMessage());
            return [
                'error' => 'Network error: ' . $e->getMessage(),
                'code' => 500,
                'status' => false
            ];
        }
    }

    /**
     * Create a payment
     */
    public function createPayment($data)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment', $data);
            if ($response->successful()) {
                return $response->json();
            }

            // Handle error responses
            $errorData = $response->json();
            Log::error('NOWPayments Payment Creation Error: ' . $response->body());
            
            // Return error information for better handling
            return [
                'error' => $errorData['message'] ?? 'Payment creation failed',
                'code' => $errorData['code'] ?? $response->status(),
                'status' => false
            ];
        } catch (\Exception $e) {
            Log::error('NOWPayments Payment Creation Error: ' . $e->getMessage());
            return [
                'error' => 'Network error: ' . $e->getMessage(),
                'code' => 500,
                'status' => false
            ];
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($paymentId)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/payment/' . $paymentId);

            if ($response->successful()) {
                return $response->json();
            }

            // Handle error responses
            $errorData = $response->json();
            Log::error('NOWPayments Payment Status Error: ' . $response->body());
            
            return [
                'error' => $errorData['message'] ?? 'Failed to get payment status',
                'code' => $errorData['code'] ?? $response->status(),
                'status' => false
            ];
        } catch (\Exception $e) {
            Log::error('NOWPayments Payment Status Error: ' . $e->getMessage());
            return [
                'error' => 'Network error: ' . $e->getMessage(),
                'code' => 500,
                'status' => false
            ];
        }
    }

    /**
     * Get estimated amount for crypto payment
     */
    public function getEstimatedAmount($amount, $currencyFrom, $currencyTo)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/estimate', [
                'amount' => $amount,
                'currency_from' => $currencyFrom,
                'currency_to' => $currencyTo,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            // Handle error responses
            $errorData = $response->json();
            Log::error('NOWPayments Estimate Error: ' . $response->body());
            
            return [
                'error' => $errorData['message'] ?? 'Failed to get estimated amount',
                'code' => $errorData['code'] ?? $response->status(),
                'status' => false,
                'currency_from' => $currencyFrom,
                'currency_to' => $currencyTo
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Network error: ' . $e->getMessage(),
                'code' => 500,
                'status' => false
            ];
        }
    }

    /**
     * Verify IPN signature
     */
    public function verifyIpnSignature($payload, $signature)
    {
        $ipnSecret = config('services.nowpayments.ipn_secret');
        $expectedSignature = hash_hmac('sha512', $payload, $ipnSecret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Create a fiat payment (Visa/Mastercard/Apple Pay)
     */
    public function createFiatPayment($data)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment', $data);
            
            if ($response->successful()) {
                return $response->json();
            }

            // Handle error responses
            $errorData = $response->json();
            Log::error('NOWPayments Fiat Payment Creation Error: ' . $response->body());
            
            // Return error information for better handling
            return [
                'error' => $errorData['message'] ?? 'Fiat payment creation failed',
                'code' => $errorData['code'] ?? $response->status(),
                'status' => false
            ];
        } catch (\Exception $e) {
            Log::error('NOWPayments Fiat Payment Creation Error: ' . $e->getMessage());
            return [
                'error' => 'Network error: ' . $e->getMessage(),
                'code' => 500,
                'status' => false
            ];
        }
    }

    /**
     * Get supported cryptocurrencies for display
     */
    public function getSupportedCrypto()
    {
        // Check cache first
        $cacheKey = 'nowpayments_supported_crypto';
        $cachedCrypto = \Cache::get($cacheKey);
        
        // if ($cachedCrypto) {
        //     return $cachedCrypto;
        // }
        
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/currencies');
            if ($response->successful()) {
                $data = $response->json();
                $currencies = $data['currencies'] ?? [];
                // Map NOWPayments currencies to our format
                $supportedCrypto = [];
                $cryptoMapping = [
                    'btc' => ['name' => 'Bitcoin', 'symbol' => 'BTC', 'icon' => 'btc.svg'],
                    'eth' => ['name' => 'Ethereum', 'symbol' => 'ETH', 'icon' => 'eth.svg'],
                    'usddtrc20' => ['name' => 'Tether', 'symbol' => 'USDTtrc20', 'icon' => 'usdt.svg'],
                    'usdterc20' => ['name' => 'Tether', 'symbol' => 'USDTErc20', 'icon' => 'usdt.svg'],
                    'usd' => ['name' => 'Tether', 'symbol' => 'USDT', 'icon' => 'usdt.svg'],
                    'usdc' => ['name' => 'USD Coin', 'symbol' => 'USDC', 'icon' => 'usdc.svg'],
                    'bnbbsc' => ['name' => 'Binance Coin', 'symbol' => 'BNB', 'icon' => 'bnb.svg'],
                    'xrp' => ['name' => 'XRP', 'symbol' => 'XRP', 'icon' => 'xrp.svg'],
                    'ada' => ['name' => 'Cardano', 'symbol' => 'ADA', 'icon' => 'ada.svg'],
                    'sol' => ['name' => 'Solana', 'symbol' => 'SOL', 'icon' => 'sol.svg'],
                    'doge' => ['name' => 'Dogecoin', 'symbol' => 'DOGE', 'icon' => 'doge.svg'],
                    'ltc' => ['name' => 'Litecoin', 'symbol' => 'LTC', 'icon' => 'ltc.svg'],
                ];
                
                foreach ($cryptoMapping as $nowpaymentsCode => $cryptoInfo) {
                    if (in_array($nowpaymentsCode, $currencies)) {
                        $key = $this->getCryptoKey($nowpaymentsCode);
                        $supportedCrypto[$key] = array_merge($cryptoInfo, ['nowpayments_code' => $nowpaymentsCode]);
                    }
                }
                
                // Cache for 1 hour
                \Cache::put($cacheKey, $supportedCrypto, 3600);
                
                return $supportedCrypto;
            }

            // Fallback to hardcoded list if API fails
            $fallbackCrypto = $this->getFallbackCrypto();
            \Cache::put($cacheKey, $fallbackCrypto, 300); // Cache fallback for 5 minutes
            return $fallbackCrypto;
            
        } catch (\Exception $e) {
            Log::error('NOWPayments Get Supported Crypto Error: ' . $e->getMessage());
            $fallbackCrypto = $this->getFallbackCrypto();
            \Cache::put($cacheKey, $fallbackCrypto, 300); // Cache fallback for 5 minutes
            return $fallbackCrypto;
        }
    }

    private function getCryptoKey($nowpaymentsCode)
    {
        $mapping = [
            'btc' => 'btc',
            'eth' => 'eth',
            'usdterc20' => 'usdt',
            'usdc' => 'usdc',
            'bnbbsc' => 'bnb',
            'xrp' => 'xrp',
            'ada' => 'ada',
            'sol' => 'sol',
            'doge' => 'doge',
            'ltc' => 'ltc',
        ];
        
        return $mapping[$nowpaymentsCode] ?? $nowpaymentsCode;
    }

    private function getFallbackCrypto()
    {
        return [
            'btc' => ['name' => 'Bitcoin', 'symbol' => 'BTC', 'icon' => 'btc.svg', 'nowpayments_code' => 'btc'],
            'eth' => ['name' => 'Ethereum', 'symbol' => 'ETH', 'icon' => 'eth.svg', 'nowpayments_code' => 'eth'],
            'usdt' => ['name' => 'Tether', 'symbol' => 'USDT', 'icon' => 'usdt.svg', 'nowpayments_code' => 'usdterc20'],
            'usdc' => ['name' => 'USD Coin', 'symbol' => 'USDC', 'icon' => 'usdc.svg', 'nowpayments_code' => 'usdc'],
            'bnb' => ['name' => 'Binance Coin', 'symbol' => 'BNB', 'icon' => 'bnb.svg', 'nowpayments_code' => 'bnbbsc'],
            'xrp' => ['name' => 'XRP', 'symbol' => 'XRP', 'icon' => 'xrp.svg', 'nowpayments_code' => 'xrp'],
            'ada' => ['name' => 'Cardano', 'symbol' => 'ADA', 'icon' => 'ada.svg', 'nowpayments_code' => 'ada'],
            'sol' => ['name' => 'Solana', 'symbol' => 'SOL', 'icon' => 'sol.svg', 'nowpayments_code' => 'sol'],
            'doge' => ['name' => 'Dogecoin', 'symbol' => 'DOGE', 'icon' => 'doge.svg', 'nowpayments_code' => 'doge'],
            'ltc' => ['name' => 'Litecoin', 'symbol' => 'LTC', 'icon' => 'ltc.svg', 'nowpayments_code' => 'ltc'],
        ];
    }

    /**
     * Get supported fiat payment methods
     */
    public function getSupportedFiatMethods()
    {
        return [
            'visa' => ['name' => 'Visa', 'icon' => 'visa.png'],
            'mastercard' => ['name' => 'Mastercard', 'icon' => 'mastercard.png'],
            'apple_pay' => ['name' => 'Apple Pay', 'icon' => 'apple-pay.png'],
        ];
    }
}
