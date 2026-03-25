<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Services;

class SystemAnalyzerService
{
    public function getBootstrapSummary(): array
    {
        return [
            'status' => 'bootstrap',
            'message' => 'System Analyzer PHP service skeleton is ready for SeAT-native implementation.',
            'next_step' => 'Import static planets and static systems, then build the first analyzer screen.',
        ];
    }
}
