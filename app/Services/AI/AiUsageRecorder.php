<?php

namespace App\Services\AI;

use App\Models\AiUsageLedger;

class AiUsageRecorder
{
    public function create(array $data): AiUsageLedger
    {
        return AiUsageLedger::create([
            'user_id' => $data['user_id'] ?? null,
            'ai_generation_id' => $data['ai_generation_id'] ?? null,
            'credit_reservation_id' => $data['credit_reservation_id'] ?? null,

            'tool_key' => $data['tool_key'],
            'provider' => $data['provider'] ?? 'gemini',
            'model' => $data['model'] ?? null,
            'operation' => $data['operation'] ?? null,

            'provider_request_id' => $data['provider_request_id'] ?? null,

            'prompt_tokens' => $data['prompt_tokens'] ?? 0,
            'output_tokens' => $data['output_tokens'] ?? 0,
            'thought_tokens' => $data['thought_tokens'] ?? 0,
            'cached_tokens' => $data['cached_tokens'] ?? 0,
            'total_tokens' => $data['total_tokens'] ?? 0,

            'input_image_count' => $data['input_image_count'] ?? 0,
            'output_image_count' => $data['output_image_count'] ?? 0,

            'provider_cost_usd' => $data['provider_cost_usd'] ?? 0,
            'provider_cost_try' => $data['provider_cost_try'] ?? 0,

            'credits_reserved' => $data['credits_reserved'] ?? 0,
            'credits_charged' => $data['credits_charged'] ?? 0,
            'credits_refunded' => $data['credits_refunded'] ?? 0,

            'status' => $data['status'] ?? 'pending',
            'error_code' => $data['error_code'] ?? null,
            'latency_ms' => $data['latency_ms'] ?? null,

            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    public function complete(
        AiUsageLedger $usage,
        array $response,
        ?int $latencyMs = null
    ): AiUsageLedger {
        $meta = $response['usageMetadata'] ?? [];

        $usage->update([
            'prompt_tokens' =>
                (int) ($meta['promptTokenCount'] ?? 0),

            'output_tokens' =>
                (int) ($meta['candidatesTokenCount'] ?? 0),

            'thought_tokens' =>
                (int) ($meta['thoughtsTokenCount'] ?? 0),

            'cached_tokens' =>
                (int) ($meta['cachedContentTokenCount'] ?? 0),

            'total_tokens' =>
                (int) ($meta['totalTokenCount'] ?? 0),

            'latency_ms' => $latencyMs,

            'status' => 'success',
        ]);

        return $usage->fresh();
    }

    public function fail(
        AiUsageLedger $usage,
        ?string $errorCode = null,
        ?int $latencyMs = null,
        array $metadata = []
    ): AiUsageLedger {
        $usage->update([
            'status' => 'failed',
            'error_code' => $errorCode,
            'latency_ms' => $latencyMs,
            'metadata' => array_merge(
                $usage->metadata ?? [],
                $metadata
            ),
        ]);

        return $usage->fresh();
    }
}
