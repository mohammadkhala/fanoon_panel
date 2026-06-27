<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleTranslateService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://translation.googleapis.com/language/translate/v2';

    public function __construct()
    {
        $this->apiKey = (string) (config('services.google_translate.api_key') ?? '');
    }

    /**
     * Translate text from source language to target language.
     *
     * @param string $text Text to translate (plain or HTML)
     * @param string $targetLang Target language code (e.g. 'en', 'he')
     * @param string|null $sourceLang Source language code (e.g. 'ar'). Null for auto-detect.
     * @param bool $isHtml Whether the text contains HTML (preserves tags)
     * @return string|null Translated text or null on failure
     */
    public function translate(
        string $text,
        string $targetLang,
        ?string $sourceLang = null,
        bool $isHtml = false
    ): ?string {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        if (empty($this->apiKey)) {
            Log::warning('Google Translate API key is not configured');
            return null;
        }

        $url = $this->baseUrl . '?key=' . urlencode($this->apiKey);
        $body = [
            'q' => $text,
            'target' => $targetLang,
            'format' => $isHtml ? 'html' : 'text',
        ];
        if ($sourceLang) {
            $body['source'] = $sourceLang;
        }

        try {
            $response = Http::asForm()->post($url, $body);

            if (!$response->successful()) {
                Log::error('Google Translate API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $translated = $data['data']['translations'][0]['translatedText'] ?? null;

            return $translated !== null ? $this->decodeHtmlEntities($translated) : null;
        } catch (\Throwable $e) {
            Log::error('Google Translate exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Decode HTML entities returned by the API (e.g. &#39; -> ')
     */
    protected function decodeHtmlEntities(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Check if the service is properly configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
