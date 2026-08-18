<?php

namespace App\Services;

/**
 * Full prompt-building and Gemini API call logic will be implemented in
 * the "AI Insight" stage, once Dashboard/Monitoring aggregation queries
 * (which this service will reuse) exist. For now this only exposes the
 * API key availability check, per the client's Stage 1 request, so the
 * rest of the app can already guard against a missing key.
 */
class AIInsightService
{
    public function isConfigured(): bool
    {
        return filled(config('services.gemini.api_key'));
    }

    public function unavailableMessage(): string
    {
        return 'Konfigurasi Gemini API belum diisi. Hubungi Administrator untuk mengatur GEMINI_API_KEY pada file .env.';
    }
}
