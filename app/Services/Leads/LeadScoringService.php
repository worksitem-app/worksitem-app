<?php

namespace App\Services\Leads;

class LeadScoringService
{
    public function score(array $context): array
    {
        $score = 0;

        $budgetConfirmed = filter_var(
            $context['budget_confirmed'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        $timeline = strtolower(
            trim((string) ($context['timeline'] ?? ''))
        );

        $phone = preg_replace(
            '/\D+/',
            '',
            (string) ($context['phone'] ?? '')
        );

        $city = mb_strtolower(
            trim((string) ($context['city'] ?? '')),
            'UTF-8'
        );

        $projectType = mb_strtolower(
            trim((string) ($context['project_type'] ?? '')),
            'UTF-8'
        );

        $area = (float) ($context['area'] ?? 0);

        $explicitRequest = filter_var(
            $context['explicit_request'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        $hasFile = filter_var(
            $context['has_file'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        $isDecisionMaker = filter_var(
            $context['is_decision_maker'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        if ($explicitRequest) {
            $score += 15;
        }

        if (strlen($phone) >= 10) {
            $score += 10;
        }

        if ($budgetConfirmed) {
            $score += 25;
        }

        $score += match ($timeline) {
            'now', 'hemen', '0-1', '0_1' => 25,
            'soon', 'yakinda', 'yakında', '1-3', '1_3' => 15,
            'later', '3-6', '3_6' => 0,
            'research', 'arastirma', 'araştırma' => -15,
            default => 0,
        };

        if ($city !== '') {
            $score += $city === 'istanbul' ? 8 : 3;
        }

        if ($area > 0) {
            $score += 5;
        }

        if ($projectType !== '') {
            $score += 5;
        }

        if (
            preg_match(
                '/fabrika|sanayi|endüstri|endustri|ofis|ticari|fit.?out|ges|epoksi|villa/',
                $projectType
            )
        ) {
            $score += 10;
        }

        if ($hasFile) {
            $score += 10;
        }

        if ($isDecisionMaker) {
            $score += 10;
        }

        $score = max(0, min(100, $score));

        $quality = $this->quality(
            $budgetConfirmed,
            $timeline,
            $explicitRequest,
            $score
        );

        return [
            'score' => $score,
            'quality' => $quality,
            'sales_ready' => in_array(
                $quality,
                ['hot', 'warm'],
                true
            ),
        ];
    }

    private function quality(
        bool $budgetConfirmed,
        string $timeline,
        bool $explicitRequest,
        int $score
    ): string {
        if (
            $budgetConfirmed &&
            in_array($timeline, ['now', 'hemen', '0-1', '0_1'], true)
        ) {
            return 'hot';
        }

        if (
            $budgetConfirmed &&
            in_array(
                $timeline,
                ['soon', 'yakinda', 'yakında', '1-3', '1_3'],
                true
            )
        ) {
            return 'warm';
        }

        if (
            $timeline === 'research' ||
            $timeline === 'arastirma' ||
            $timeline === 'araştırma'
        ) {
            return 'research';
        }

        if ($budgetConfirmed) {
            return 'nurture';
        }

        if ($explicitRequest && $score >= 30) {
            return 'warm';
        }

        return $explicitRequest ? 'nurture' : 'unqualified';
    }
}
