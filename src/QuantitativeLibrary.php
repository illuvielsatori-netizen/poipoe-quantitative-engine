<?php
/**
 * PoIPoE Protocol Vol. 1 - QUANTITATIVE LIBRARY
 * =====================================
 * Precision Statistical & Predictive Analytics Engine
 * 
 * This library provides the mathematical foundation for the Meta-Miner's
 * predictive intelligence, transforming raw blockchain data into strategic forecasts.
 * 
 * @package Proof of Intelligence × Proof of Evolution
 * @version 2.0.0
 * @author Illuviel Satori
 */

namespace src\Quantitative;

class QuantitativeLibrary {
    
    // =================================================================
    // SECTION 1: DESCRIPTIVE STATISTICS
    // =================================================================
    
    /**
     * Calculate arithmetic mean (average)
     * 
     * @param array $data Numeric array
     * @return float|null Mean value or null if empty
     */
    public static function mean(array $data): ?float {
        if (empty($data)) return null;
        return array_sum($data) / count($data);
    }
    
    /**
     * Calculate median (middle value)
     * 
     * @param array $data Numeric array
     * @return float|null Median value or null if empty
     */
    public static function median(array $data): ?float {
        if (empty($data)) return null;
        
        $values = array_values($data);
        sort($values);
        $count = count($values);
        $middle = floor($count / 2);
        
        if ($count % 2 == 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }
        return $values[$middle];
    }
    
    /**
     * Calculate standard deviation (volatility measure)
     * 
     * @param array $data Numeric array
     * @param bool $sample True for sample std dev, false for population
     * @return float|null Standard deviation or null if insufficient data
     */
    public static function standardDeviation(array $data, bool $sample = true): ?float {
        $count = count($data);
        if ($count < 2) return null;
        
        $mean = self::mean($data);
        $variance = 0;
        
        foreach ($data as $value) {
            $variance += pow($value - $mean, 2);
        }
        
        $divisor = $sample ? ($count - 1) : $count;
        return sqrt($variance / $divisor);
    }
    
    /**
     * Calculate variance
     * 
     * @param array $data Numeric array
     * @param bool $sample True for sample variance, false for population
     * @return float|null Variance or null if insufficient data
     */
    public static function variance(array $data, bool $sample = true): ?float {
        $stdDev = self::standardDeviation($data, $sample);
        return $stdDev !== null ? pow($stdDev, 2) : null;
    }
    
    /**
     * Calculate percentile
     * 
     * @param array $data Numeric array
     * @param float $percentile Percentile to calculate (0-100)
     * @return float|null Percentile value or null if empty
     */
    public static function percentile(array $data, float $percentile): ?float {
        if (empty($data) || $percentile < 0 || $percentile > 100) return null;
        
        $values = array_values($data);
        sort($values);
        $count = count($values);
        
        $index = ($percentile / 100) * ($count - 1);
        $lower = floor($index);
        $upper = ceil($index);
        $weight = $index - $lower;
        
        return $values[$lower] * (1 - $weight) + $values[$upper] * $weight;
    }
    
    /**
     * Calculate z-score (standard score)
     * 
     * @param float $value Value to calculate z-score for
     * @param array $data Population data
     * @return float|null Z-score or null if calculation not possible
     */
    public static function zScore(float $value, array $data): ?float {
        $mean = self::mean($data);
        $stdDev = self::standardDeviation($data, false);
        
        if ($stdDev === null || $stdDev == 0) return null;
        
        return ($value - $mean) / $stdDev;
    }
    
    // =================================================================
    // SECTION 2: CORRELATION & COVARIANCE
    // =================================================================
    
    /**
     * Calculate Pearson correlation coefficient
     * Measures linear relationship between two variables (-1 to +1)
     * 
     * @param array $x First dataset
     * @param array $y Second dataset
     * @return float|null Correlation coefficient or null if invalid
     */
    public static function correlation(array $x, array $y): ?float {
        if (count($x) !== count($y) || count($x) < 2) return null;
        
        $n = count($x);
        $meanX = self::mean($x);
        $meanY = self::mean($y);
        
        $numerator = 0;
        $sumXSquared = 0;
        $sumYSquared = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $diffX = $x[$i] - $meanX;
            $diffY = $y[$i] - $meanY;
            
            $numerator += $diffX * $diffY;
            $sumXSquared += $diffX * $diffX;
            $sumYSquared += $diffY * $diffY;
        }
        
        $denominator = sqrt($sumXSquared * $sumYSquared);
        
        if ($denominator == 0) return null;
        
        return $numerator / $denominator;
    }
    
    /**
     * Calculate covariance between two datasets
     * 
     * @param array $x First dataset
     * @param array $y Second dataset
     * @param bool $sample True for sample covariance
     * @return float|null Covariance or null if invalid
     */
    public static function covariance(array $x, array $y, bool $sample = true): ?float {
        if (count($x) !== count($y) || count($x) < 2) return null;
        
        $n = count($x);
        $meanX = self::mean($x);
        $meanY = self::mean($y);
        
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += ($x[$i] - $meanX) * ($y[$i] - $meanY);
        }
        
        $divisor = $sample ? ($n - 1) : $n;
        return $sum / $divisor;
    }
    
    // =================================================================
    // SECTION 3: TIME SERIES ANALYSIS
    // =================================================================
    
    /**
     * Calculate Simple Moving Average (SMA)
     * 
     * @param array $data Time series data
     * @param int $period Window size
     * @return array Array of SMA values
     */
    public static function simpleMovingAverage(array $data, int $period): array {
        if ($period < 1 || $period > count($data)) return [];
        
        $sma = [];
        $values = array_values($data);
        
        for ($i = $period - 1; $i < count($values); $i++) {
            $window = array_slice($values, $i - $period + 1, $period);
            $sma[] = array_sum($window) / $period;
        }
        
        return $sma;
    }
    
    /**
     * Calculate Exponential Moving Average (EMA)
     * More responsive to recent price changes than SMA
     * 
     * @param array $data Time series data
     * @param int $period Window size
     * @return array Array of EMA values
     */
    public static function exponentialMovingAverage(array $data, int $period): array {
        if ($period < 1 || empty($data)) return [];
        
        $values = array_values($data);
        $ema = [];
        $multiplier = 2 / ($period + 1);
        
        // First EMA is SMA
        $firstWindow = array_slice($values, 0, $period);
        $ema[] = array_sum($firstWindow) / $period;
        
        // Subsequent EMAs
        for ($i = $period; $i < count($values); $i++) {
            $emaValue = ($values[$i] - $ema[count($ema) - 1]) * $multiplier + $ema[count($ema) - 1];
            $ema[] = $emaValue;
        }
        
        return $ema;
    }
    
    /**
     * Calculate Weighted Moving Average (WMA)
     * Recent values have more weight
     * 
     * @param array $data Time series data
     * @param int $period Window size
     * @return array Array of WMA values
     */
    public static function weightedMovingAverage(array $data, int $period): array {
        if ($period < 1 || $period > count($data)) return [];
        
        $wma = [];
        $values = array_values($data);
        $weightSum = ($period * ($period + 1)) / 2; // Sum of weights: 1+2+3+...+period
        
        for ($i = $period - 1; $i < count($values); $i++) {
            $weightedSum = 0;
            for ($j = 0; $j < $period; $j++) {
                $weight = $j + 1;
                $weightedSum += $values[$i - $period + 1 + $j] * $weight;
            }
            $wma[] = $weightedSum / $weightSum;
        }
        
        return $wma;
    }
    
    /**
     * Calculate Moving Standard Deviation
     * Measures volatility over rolling window
     * 
     * @param array $data Time series data
     * @param int $period Window size
     * @return array Array of moving std dev values
     */
    public static function movingStandardDeviation(array $data, int $period): array {
        if ($period < 2 || $period > count($data)) return [];
        
        $movingStdDev = [];
        $values = array_values($data);
        
        for ($i = $period - 1; $i < count($values); $i++) {
            $window = array_slice($values, $i - $period + 1, $period);
            $movingStdDev[] = self::standardDeviation($window, true);
        }
        
        return $movingStdDev;
    }
    
    /**
     * Calculate Bollinger Bands
     * Volatility indicator with upper and lower bands
     * 
     * @param array $data Time series data
     * @param int $period Window size (typically 20)
     * @param float $stdDevMultiplier Standard deviations for bands (typically 2)
     * @return array ['middle' => SMA, 'upper' => upper band, 'lower' => lower band]
     */
    public static function bollingerBands(array $data, int $period = 20, float $stdDevMultiplier = 2): array {
        $sma = self::simpleMovingAverage($data, $period);
        $stdDev = self::movingStandardDeviation($data, $period);
        
        $upper = [];
        $lower = [];
        
        for ($i = 0; $i < count($sma); $i++) {
            $upper[] = $sma[$i] + ($stdDev[$i] * $stdDevMultiplier);
            $lower[] = $sma[$i] - ($stdDev[$i] * $stdDevMultiplier);
        }
        
        return [
            'middle' => $sma,
            'upper' => $upper,
            'lower' => $lower
        ];
    }
    
    /**
     * Calculate Rate of Change (ROC)
     * Momentum indicator showing percentage change
     * 
     * @param array $data Time series data
     * @param int $period Lookback period
     * @return array Array of ROC values (percentage)
     */
    public static function rateOfChange(array $data, int $period): array {
        if ($period < 1 || $period >= count($data)) return [];
        
        $roc = [];
        $values = array_values($data);
        
        for ($i = $period; $i < count($values); $i++) {
            $oldValue = $values[$i - $period];
            if ($oldValue != 0) {
                $roc[] = (($values[$i] - $oldValue) / $oldValue) * 100;
            } else {
                $roc[] = 0;
            }
        }
        
        return $roc;
    }
    
    // =================================================================
    // SECTION 4: REGRESSION & TREND ANALYSIS
    // =================================================================
    
    /**
     * Calculate Linear Regression
     * Returns slope, intercept, and R-squared
     * 
     * @param array $x Independent variable (e.g., time)
     * @param array $y Dependent variable (e.g., price)
     * @return array|null ['slope', 'intercept', 'r_squared', 'predict'] or null
     */
    public static function linearRegression(array $x, array $y): ?array {
        if (count($x) !== count($y) || count($x) < 2) return null;
        
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumXSquared = 0;
        $sumYSquared = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumXSquared += $x[$i] * $x[$i];
            $sumYSquared += $y[$i] * $y[$i];
        }
        
        // Calculate slope (m) and intercept (b) for y = mx + b
        $denominator = ($n * $sumXSquared) - ($sumX * $sumX);
        
        if ($denominator == 0) return null;
        
        $slope = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
        $intercept = ($sumY - ($slope * $sumX)) / $n;
        
        // Calculate R-squared (coefficient of determination)
        $meanY = $sumY / $n;
        $ssTotal = 0; // Total sum of squares
        $ssResidual = 0; // Residual sum of squares
        
        for ($i = 0; $i < $n; $i++) {
            $predicted = $slope * $x[$i] + $intercept;
            $ssTotal += pow($y[$i] - $meanY, 2);
            $ssResidual += pow($y[$i] - $predicted, 2);
        }
        
        $rSquared = ($ssTotal != 0) ? 1 - ($ssResidual / $ssTotal) : 0;
        
        // Prediction function
        $predict = function($xValue) use ($slope, $intercept) {
            return $slope * $xValue + $intercept;
        };
        
        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'r_squared' => $rSquared,
            'predict' => $predict,
            'equation' => "y = {$slope}x + {$intercept}"
        ];
    }
    
    /**
     * Detect trend direction
     * 
     * @param array $data Time series data
     * @param int $period Window for trend analysis
     * @return string 'uptrend', 'downtrend', or 'sideways'
     */
    public static function detectTrend(array $data, int $period = 20): string {
        if (count($data) < $period) return 'insufficient_data';
        
        $recent = array_slice($data, -$period);
        $x = range(0, count($recent) - 1);
        $regression = self::linearRegression($x, $recent);
        
        if ($regression === null) return 'unknown';
        
        $slope = $regression['slope'];
        $avgValue = self::mean($recent);
        $slopePercent = ($avgValue != 0) ? abs($slope / $avgValue) * 100 : 0;
        
        // Threshold: 0.1% per period
        if ($slopePercent < 0.1) {
            return 'sideways';
        } elseif ($slope > 0) {
            return 'uptrend';
        } else {
            return 'downtrend';
        }
    }
    
    /**
     * Forecast future values using linear regression
     * 
     * @param array $data Historical time series data
     * @param int $periods Number of periods to forecast
     * @return array Forecasted values
     */
    public static function forecastLinear(array $data, int $periods): array {
        if (count($data) < 2 || $periods < 1) return [];
        
        $x = range(0, count($data) - 1);
        $regression = self::linearRegression($x, $data);
        
        if ($regression === null) return [];
        
        $forecast = [];
        $startIndex = count($data);
        
        for ($i = 0; $i < $periods; $i++) {
            $forecast[] = $regression['predict']($startIndex + $i);
        }
        
        return $forecast;
    }
    
    // =================================================================
    // SECTION 5: VOLATILITY & RISK METRICS
    // =================================================================
    
    /**
     * Calculate Historical Volatility (annualized)
     * 
     * @param array $prices Price series
     * @param int $period Window size
     * @param int $periodsPerYear 365 for daily, 24 for hourly, etc.
     * @return float|null Annualized volatility percentage
     */
    public static function historicalVolatility(array $prices, int $period, int $periodsPerYear = 365): ?float {
        if (count($prices) < $period + 1) return null;
        
        // Calculate log returns
        $returns = [];
        for ($i = 1; $i < count($prices); $i++) {
            if ($prices[$i - 1] != 0) {
                $returns[] = log($prices[$i] / $prices[$i - 1]);
            }
        }
        
        if (count($returns) < $period) return null;
        
        // Take most recent returns
        $recentReturns = array_slice($returns, -$period);
        
        // Calculate standard deviation of returns
        $stdDev = self::standardDeviation($recentReturns, true);
        
        if ($stdDev === null) return null;
        
        // Annualize
        return $stdDev * sqrt($periodsPerYear) * 100;
    }
    
    /**
     * Calculate Sharpe Ratio
     * Risk-adjusted return metric
     * 
     * @param array $returns Array of period returns
     * @param float $riskFreeRate Risk-free rate (same period as returns)
     * @return float|null Sharpe ratio
     */
    public static function sharpeRatio(array $returns, float $riskFreeRate = 0): ?float {
        if (count($returns) < 2) return null;
        
        $avgReturn = self::mean($returns);
        $stdDev = self::standardDeviation($returns, true);
        
        if ($stdDev === null || $stdDev == 0) return null;
        
        return ($avgReturn - $riskFreeRate) / $stdDev;
    }
    
    /**
     * Calculate Maximum Drawdown
     * Largest peak-to-trough decline
     * 
     * @param array $prices Price series
     * @return array ['max_drawdown' => %, 'peak_index' => int, 'trough_index' => int]
     */
    public static function maximumDrawdown(array $prices): array {
        if (empty($prices)) return ['max_drawdown' => 0, 'peak_index' => 0, 'trough_index' => 0];
        
        $maxDrawdown = 0;
        $peak = $prices[0];
        $peakIndex = 0;
        $troughIndex = 0;
        $maxPeakIndex = 0;
        $maxTroughIndex = 0;
        
        foreach ($prices as $i => $price) {
            if ($price > $peak) {
                $peak = $price;
                $peakIndex = $i;
            }
            
            $drawdown = (($peak - $price) / $peak) * 100;
            
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
                $maxPeakIndex = $peakIndex;
                $maxTroughIndex = $i;
            }
        }
        
        return [
            'max_drawdown' => $maxDrawdown,
            'peak_index' => $maxPeakIndex,
            'trough_index' => $maxTroughIndex
        ];
    }
    
    /**
     * Calculate Value at Risk (VaR)
     * Maximum expected loss at given confidence level
     * 
     * @param array $returns Historical returns
     * @param float $confidenceLevel Confidence level (e.g., 0.95 for 95%)
     * @return float|null VaR value (positive number represents potential loss)
     */
    public static function valueAtRisk(array $returns, float $confidenceLevel = 0.95): ?float {
        if (empty($returns) || $confidenceLevel <= 0 || $confidenceLevel >= 1) return null;
        
        $sorted = $returns;
        sort($sorted);
        
        $percentile = (1 - $confidenceLevel) * 100;
        $var = self::percentile($sorted, $percentile);
        
        return -$var; // Return as positive for loss
    }
    
    // =================================================================
    // SECTION 6: ANOMALY DETECTION
    // =================================================================
    
    /**
     * Detect anomalies using Z-score method
     * 
     * @param array $data Time series data
     * @param float $threshold Z-score threshold (typically 2 or 3)
     * @return array Indices of anomalous data points
     */
    public static function detectAnomaliesZScore(array $data, float $threshold = 3): array {
        if (count($data) < 3) return [];
        
        $mean = self::mean($data);
        $stdDev = self::standardDeviation($data, true);
        
        if ($stdDev === null || $stdDev == 0) return [];
        
        $anomalies = [];
        
        foreach ($data as $index => $value) {
            $zScore = abs(($value - $mean) / $stdDev);
            if ($zScore > $threshold) {
                $anomalies[] = [
                    'index' => $index,
                    'value' => $value,
                    'z_score' => $zScore,
                    'deviation_percent' => (($value - $mean) / $mean) * 100
                ];
            }
        }
        
        return $anomalies;
    }
    
    /**
     * Detect anomalies using IQR (Interquartile Range) method
     * More robust to outliers than Z-score
     * 
     * @param array $data Time series data
     * @param float $multiplier IQR multiplier (typically 1.5)
     * @return array Indices of anomalous data points
     */
    public static function detectAnomaliesIQR(array $data, float $multiplier = 1.5): array {
        if (count($data) < 4) return [];
        
        $q1 = self::percentile($data, 25);
        $q3 = self::percentile($data, 75);
        $iqr = $q3 - $q1;
        
        $lowerBound = $q1 - ($multiplier * $iqr);
        $upperBound = $q3 + ($multiplier * $iqr);
        
        $anomalies = [];
        
        foreach ($data as $index => $value) {
            if ($value < $lowerBound || $value > $upperBound) {
                $anomalies[] = [
                    'index' => $index,
                    'value' => $value,
                    'lower_bound' => $lowerBound,
                    'upper_bound' => $upperBound,
                    'type' => $value < $lowerBound ? 'low' : 'high'
                ];
            }
        }
        
        return $anomalies;
    }
    
    // =================================================================
    // SECTION 7: PROBABILITY & CONFIDENCE
    // =================================================================
    
    /**
     * Calculate confidence interval for mean
     * 
     * @param array $data Sample data
     * @param float $confidenceLevel Confidence level (e.g., 0.95)
     * @return array|null ['lower' => float, 'upper' => float, 'margin' => float]
     */
    public static function confidenceInterval(array $data, float $confidenceLevel = 0.95): ?array {
        if (count($data) < 2) return null;
        
        $mean = self::mean($data);
        $stdDev = self::standardDeviation($data, true);
        $n = count($data);
        
        if ($stdDev === null) return null;
        
        // For large samples (n > 30), use z-score approximation
        // For small samples, this is an approximation (proper t-distribution would be better)
        $zScore = 1.96; // 95% confidence
        
        if ($confidenceLevel == 0.99) $zScore = 2.576;
        elseif ($confidenceLevel == 0.90) $zScore = 1.645;
        
        $standardError = $stdDev / sqrt($n);
        $margin = $zScore * $standardError;
        
        return [
            'lower' => $mean - $margin,
            'upper' => $mean + $margin,
            'margin' => $margin,
            'mean' => $mean
        ];
    }
    
    /**
     * Calculate probability of value being within range (normal distribution assumption)
     * 
     * @param float $value Value to test
     * @param array $data Population data
     * @return float Probability (0 to 1)
     */
    public static function probabilityInRange(float $value, array $data): float {
        $zScore = self::zScore($value, $data);
        
        if ($zScore === null) return 0.5;
        
        // Approximate cumulative distribution function for standard normal
        return self::normalCDF($zScore);
    }
    
    /**
     * Standard normal cumulative distribution function (approximation)
     * 
     * @param float $z Z-score
     * @return float Cumulative probability
     */
    private static function normalCDF(float $z): float {
        // Abramowitz and Stegun approximation
        $t = 1 / (1 + 0.2316419 * abs($z));
        $d = 0.3989423 * exp(-$z * $z / 2);
        $p = $d * $t * (0.3193815 + $t * (-0.3565638 + $t * (1.781478 + $t * (-1.821256 + $t * 1.330274))));
        
        return $z > 0 ? 1 - $p : $p;
    }
    
    // =================================================================
    // SECTION 8: UTILITY FUNCTIONS
    // =================================================================
    
    /**
     * Calculate percent change
     * 
     * @param float $oldValue Starting value
     * @param float $newValue Ending value
     * @return float|null Percent change
     */
    public static function percentChange(float $oldValue, float $newValue): ?float {
        if ($oldValue == 0) return null;
        return (($newValue - $oldValue) / $oldValue) * 100;
    }
    
    /**
     * Normalize data to 0-1 range (min-max normalization)
     * 
     * @param array $data Data to normalize
     * @return array Normalized data
     */
    public static function normalize(array $data): array {
        if (empty($data)) return [];
        
        $min = min($data);
        $max = max($data);
        $range = $max - $min;
        
        if ($range == 0) return array_fill(0, count($data), 0);
        
        return array_map(function($value) use ($min, $range) {
            return ($value - $min) / $range;
        }, $data);
    }
    
    /**
     * Calculate compound annual growth rate (CAGR)
     * 
     * @param float $startValue Beginning value
     * @param float $endValue Ending value
     * @param float $years Number of years
     * @return float|null CAGR percentage
     */
    public static function CAGR(float $startValue, float $endValue, float $years): ?float {
        if ($startValue <= 0 || $years <= 0) return null;
        
        return (pow($endValue / $startValue, 1 / $years) - 1) * 100;
    }
}
