<?php
/**
 * Ethereum Mathematics Engine
 * ===========================
 * Core mathematical functions for Ethereum predictive analytics
 * No external dependencies required.
 * 
 * @package PoIPoE\MathEngine
 * @version 1.0.0
 * @author Illuviel Satori
 */

namespace PoIPoE\MathEngine;

class EthereumMathematicsEngine {
    private $db;

    public function __construct($database = null) {
        $this->db = $database;
    }

    /**
     * Calculate Simple Moving Average (SMA)
     */
    public function calculateSMA(array $data, int $period): ?float {
        if (count($data) < $period) return null;

        $sum = array_sum(array_slice($data, -$period));
        return $sum / $period;
    }

    /**
     * Calculate Exponential Moving Average (EMA)
     */
    public function calculateEMA(array $data, int $period): ?float {
        if (count($data) < 2) return null;

        $alpha = 2 / ($period + 1);
        $ema = $data[0];

        for ($i = 1; $i < count($data); $i++) {
            $ema = $alpha * $data[$i] + (1 - $alpha) * $ema;
        }

        return $ema;
    }

    /**
     * Calculate standard deviation (volatility)
     */
    public function calculateVolatility(array $data): float {
        if (count($data) < 2) return 0.0;

        $mean = array_sum($data) / count($data);
        $variance = 0;

        foreach ($data as $value) {
            $variance += pow($value - $mean, 2);
        }

        return sqrt($variance / count($data));
    }

    /**
     * Determine price trend: rising, falling, or stable
     */
    public function calculateTrend(array $data): string {
        if (count($data) < 2) return 'stable';

        $change = ($data[count($data) - 1] - $data[0]) / $data[0];

        if ($change > 0.05) return 'rising';
        if ($change < -0.05) return 'falling';
        return 'stable';
    }

    /**
     * Predict Ethereum gas price using SMA and trend analysis
     */
    public function predictGasPrice(array $historicalData): array {
        if (empty($historicalData)) {
            return [
                'prediction' => 25.0,
                'confidence' => 50,
                'method' => 'fallback',
                'trend' => 'stable'
            ];
        }

        $sma = $this->calculateSMA($historicalData, min(5, count($historicalData)));
        $volatility = $this->calculateVolatility($historicalData);
        $trend = $this->calculateTrend($historicalData);

        $prediction = $sma;
        if ($trend === 'rising') {
            $prediction *= 1.02;
        } elseif ($trend === 'falling') {
            $prediction *= 0.98;
        }

        $confidence = min(85, max(60, 60 + count($historicalData) * 5 - $volatility * 2));

        return [
            'prediction' => round($prediction, 2),
            'confidence' => round($confidence),
            'method' => 'sma_trend_analysis',
            'trend' => $trend,
            'sma' => round($sma, 2),
            'volatility' => round($volatility, 2)
        ];
    }

    /**
     * Calculate risk score (0-100)
     */
    public function calculateRiskScore(array $factors): int {
        if (empty($factors)) return 50;

        $weights = [
            'volatility' => 0.4,
            'trend_strength' => 0.3,
            'market_conditions' => 0.3
        ];

        $score = 0;
        foreach ($factors as $factor => $value) {
            $score += ($weights[$factor] ?? 0) * $value;
        }

        return (int) round(min(100, max(0, $score)));
    }

    /**
     * Basic linear regression for trend analysis
     */
    public function linearRegression(array $data): array {
        $n = count($data);
        if ($n < 2) return ['slope' => 0, 'intercept' => $data[0] ?? 0, 'direction' => 'stable'];

        $sumX = $sumY = $sumXY = $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $data[$i];
            $sumXY += $i * $data[$i];
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'direction' => $slope > 0 ? 'positive' : 'negative'
        ];
    }

    /**
     * Get system status
     */
    public function getSystemStatus(): array {
        return [
            'mathematics_engine' => 'operational',
            'database_connection' => $this->db ? 'connected' : 'disconnected',
            'functions_available' => [
                'SMA', 'EMA', 'volatility', 'trend', 'prediction', 'risk_score', 'regression'
            ],
            'status' => 'ready'
        ];
    }
}
