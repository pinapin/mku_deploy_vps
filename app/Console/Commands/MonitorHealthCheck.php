<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MonitorHealthCheck extends Command
{
    protected $signature = 'monitor:health-check {--log-output : Log output to file}';
    protected $description = 'Monitor application health and resource usage';

    protected $thresholds = [
        'cpu_usage_warning' => 70,
        'cpu_usage_critical' => 90,
        'memory_usage_warning' => 80,
        'memory_usage_critical' => 95,
        'disk_usage_warning' => 80,
        'disk_usage_critical' => 90,
        'response_time_warning' => 2000,
        'response_time_critical' => 5000,
        'error_rate_warning' => 5,
        'error_rate_critical' => 10,
    ];

    public function handle()
    {
        $this->info('Starting health monitoring...');
        $logOutput = $this->option('log-output');

        while (true) {
            $timestamp = now();
            $healthData = $this->collectHealthMetrics();

            $this->displayHealthStatus($healthData, $timestamp);

            if ($logOutput) {
                $this->logHealthData($healthData, $timestamp);
            }

            // Check for critical alerts
            $this->checkCriticalAlerts($healthData);

            // Sleep for 60 seconds
            sleep(60);
        }

        return Command::SUCCESS;
    }

    protected function collectHealthMetrics()
    {
        $metrics = [
            'timestamp' => now()->toISOString(),
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'application' => $this->checkApplicationHealth(),
            'system' => $this->checkSystemResources(),
            'nginx' => $this->checkNginxHealth(),
            'performance' => $this->checkPerformanceMetrics(),
        ];

        // Calculate error rate (last 5 minutes)
        $metrics['error_rate'] = $this->calculateErrorRate();

        return $metrics;
    }

    protected function checkDatabaseHealth()
    {
        try {
            $start = microtime(true);

            // Basic connection test
            $connection = DB::connection('mysql');
            $result = $connection->select('SELECT 1 as test');

            $responseTime = (microtime(true) - $start) * 1000;

            // Connection pool status
            $connectionInfo = DB::select('SHOW STATUS LIKE "Threads_connected"');
            $threadsConnected = $connectionInfo[0]->Value ?? 0;

            $maxConnections = DB::select('SHOW VARIABLES LIKE "max_connections"');
            $maxConn = $maxConnections[0]->Value ?? 100;

            // Slow query count (last minute)
            $slowQueries = DB::select('
                SELECT COUNT(*) as count
                FROM information_schema.processlist
                WHERE time > 1 AND command != "Sleep"
            ')[0]->count ?? 0;

            $connectionUtilization = ($threadsConnected / $maxConn) * 100;

            return [
                'status' => 'healthy',
                'response_time_ms' => round($responseTime, 2),
                'connected_threads' => $threadsConnected,
                'max_connections' => $maxConn,
                'connection_utilization_percent' => round($connectionUtilization, 2),
                'slow_queries' => $slowQueries,
                'utilization_status' => $this->getStatusByPercentage($connectionUtilization),
            ];

        } catch (\Exception $e) {
            Log::error('Database health check failed: ' . $e->getMessage());
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'response_time_ms' => 9999,
            ];
        }
    }

    protected function checkCacheHealth()
    {
        try {
            $start = microtime(true);

            // Redis ping test
            $ping = Cache::store('redis')->get('health_check_ping');
            if (!$ping) {
                Cache::store('redis')->put('health_check_ping', 'pong', 60);
            }

            $responseTime = (microtime(true) - $start) * 1000;

            // Memory usage estimation
            $info = Cache::store('redis')->connection()->info('memory');
            $usedMemory = $info['used_memory'] ?? 0;
            $maxMemory = $info['maxmemory'] ?? 0;
            $memoryUsage = $maxMemory > 0 ? ($usedMemory / $maxMemory) * 100 : 0;

            // Connection count
            $clientInfo = Cache::store('redis')->connection()->info('clients');
            $connectedClients = $clientInfo['connected_clients'] ?? 0;

            return [
                'status' => 'healthy',
                'response_time_ms' => round($responseTime, 2),
                'memory_usage_percent' => round($memoryUsage, 2),
                'connected_clients' => $connectedClients,
                'utilization_status' => $this->getStatusByPercentage($memoryUsage),
            ];

        } catch (\Exception $e) {
            Log::error('Cache health check failed: ' . $e->getMessage());
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'response_time_ms' => 9999,
            ];
        }
    }

    protected function checkApplicationHealth()
    {
        try {
            $start = microtime(true);

            // Test main application endpoint
            $response = Http::timeout(10)->get('http://localhost/health');
            $responseTime = (microtime(true) - $start) * 1000;

            $isHealthy = $response->successful() &&
                       json_decode($response->body())->status === 'healthy';

            return [
                'status' => $isHealthy ? 'healthy' : 'unhealthy',
                'response_time_ms' => round($responseTime, 2),
                'http_status' => $response->status(),
                'endpoint_tested' => '/health',
            ];

        } catch (\Exception $e) {
            Log::error('Application health check failed: ' . $e->getMessage());
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'response_time_ms' => 9999,
                'http_status' => 0,
            ];
        }
    }

    protected function checkNginxHealth()
    {
        try {
            $start = microtime(true);

            // Test Nginx health endpoint
            $response = Http::timeout(5)->get('http://localhost/app-health');
            $responseTime = (microtime(true) - $start) * 1000;

            // Test load balancer endpoint
            $lbResponse = Http::timeout(5)->get('http://localhost/health');
            $lbResponseTime = (microtime(true) - $start) * 1000;

            $isHealthy = $response->successful() &&
                       $lbResponse->successful();

            return [
                'status' => $isHealthy ? 'healthy' : 'unhealthy',
                'app_health_response_ms' => round($responseTime, 2),
                'load_balancer_response_ms' => round($lbResponseTime, 2),
                'app_health_status' => $response->status(),
                'load_balancer_status' => $lbResponse->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Nginx health check failed: ' . $e->getMessage());
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'app_health_response_ms' => 9999,
                'load_balancer_response_ms' => 9999,
            ];
        }
    }

    protected function checkPerformanceMetrics()
    {
        try {
            // Get recent performance metrics from logs or monitoring
            $recentErrors = $this->getRecentErrorCount();
            $totalRequests = $this->getRecentRequestCount();

            $errorRate = $totalRequests > 0 ? ($recentErrors / $totalRequests) * 100 : 0;

            // Average response time (approximation)
            $avgResponseTime = $this->getAverageResponseTime();

            return [
                'error_rate_percent' => round($errorRate, 2),
                'recent_errors_5min' => $recentErrors,
                'total_requests_5min' => $totalRequests,
                'avg_response_time_ms' => round($avgResponseTime, 2),
                'error_rate_status' => $this->getStatusByPercentage($errorRate, false),
            ];

        } catch (\Exception $e) {
            return [
                'error_rate_percent' => 0,
                'recent_errors_5min' => 0,
                'total_requests_5min' => 0,
                'avg_response_time_ms' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function checkSystemResources()
    {
        // Basic system resource checks
        $diskUsage = $this->getDiskUsage();
        $loadAverage = $this->getLoadAverage();

        return [
            'disk_usage_percent' => round($diskUsage, 2),
            'disk_status' => $this->getStatusByPercentage($diskUsage),
            'load_average' => $loadAverage,
            'load_status' => $loadAverage > 2 ? 'high' : ($loadAverage > 1 ? 'medium' : 'normal'),
        ];
    }

    protected function getDiskUsage()
    {
        try {
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            return $total > 0 ? (($total - $free) / $total) * 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getLoadAverage()
    {
        try {
            if (function_exists('sys_getloadavg')) {
                $load = sys_getloadavg();
                return $load[0] ?? 0;
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function calculateErrorRate()
    {
        // Calculate error rate from recent logs or monitoring
        // This is a simplified version
        return Cache::remember('error_rate_5min', 60, function () {
            $recentErrors = $this->getRecentErrorCount();
            $totalRequests = $this->getRecentRequestCount();

            return $totalRequests > 0 ? ($recentErrors / $totalRequests) * 100 : 0;
        });
    }

    protected function getRecentErrorCount()
    {
        // Simplified error counting - in production, this would parse logs
        return Cache::get('recent_errors_count', 0);
    }

    protected function getRecentRequestCount()
    {
        // Simplified request counting
        return Cache::get('recent_requests_count', 100);
    }

    protected function getAverageResponseTime()
    {
        return Cache::remember('avg_response_time', 60, function () {
            // In production, this would be calculated from actual metrics
            return rand(800, 1500); // Placeholder
        });
    }

    protected function getStatusByPercentage($percentage, $isUtilization = true)
    {
        if ($isUtilization) {
            if ($percentage >= 90) return 'critical';
            if ($percentage >= 70) return 'warning';
            return 'normal';
        } else {
            if ($percentage >= 10) return 'critical';
            if ($percentage >= 5) return 'warning';
            return 'normal';
        }
    }

    protected function displayHealthStatus($healthData, $timestamp)
    {
        $this->line("");
        $this->info("=== Health Check: {$timestamp} ===");

        // Database
        $db = $healthData['database'];
        $dbStatus = $this->formatStatus($db['status']);
        $this->line("🗄  Database: {$dbStatus} ({$db['response_time_ms']}ms, {$db['connection_utilization_percent']}% connections)");

        // Cache
        $cache = $healthData['cache'];
        $cacheStatus = $this->formatStatus($cache['status']);
        $this->line("🚀 Cache: {$cacheStatus} ({$cache['response_time_ms']}ms, {$cache['memory_usage_percent']}% memory)");

        // Application
        $app = $healthData['application'];
        $appStatus = $this->formatStatus($app['status']);
        $this->line("🌐 Application: {$appStatus} ({$app['response_time_ms']}ms, HTTP {$app['http_status']})");

        // Nginx
        $nginx = $healthData['nginx'];
        $nginxStatus = $this->formatStatus($nginx['status']);
        $this->line("⚖️  Load Balancer: {$nginxStatus} (App: {$nginx['app_health_response_ms']}ms, LB: {$nginx['load_balancer_response_ms']}ms)");

        // Performance
        $perf = $healthData['performance'];
        $perfStatus = $this->formatStatusByErrorRate($perf['error_rate_percent']);
        $this->line("📊 Performance: {$perfStatus} ({$perf['error_rate_percent']}% error rate, {$perf['avg_response_time_ms']}ms avg)");

        // System
        $system = $healthData['system'];
        $diskStatus = $this->formatStatus($system['disk_status']);
        $loadStatus = ucfirst($system['load_status']);
        $this->line("💾 System: {$diskStatus} ({$system['disk_usage_percent']}% disk, {$loadStatus} load: {$system['load_average']})");

        $this->line("");
    }

    protected function formatStatus($status)
    {
        switch ($status) {
            case 'healthy': return '✅ Healthy';
            case 'warning': return '⚠️  Warning';
            case 'critical': return '❌ Critical';
            case 'unhealthy': return '🔴 Unhealthy';
            default: return '❓ Unknown';
        }
    }

    protected function formatStatusByErrorRate($errorRate)
    {
        if ($errorRate >= 10) return '❌ Critical';
        if ($errorRate >= 5) return '⚠️  Warning';
        return '✅ Normal';
    }

    protected function checkCriticalAlerts($healthData)
    {
        $criticalIssues = [];

        // Database critical checks
        if ($healthData['database']['connection_utilization_percent'] > 90) {
            $criticalIssues[] = 'Database connection utilization critical!';
        }

        // Cache critical checks
        if ($healthData['cache']['memory_usage_percent'] > 95) {
            $criticalIssues[] = 'Cache memory usage critical!';
        }

        // Application critical checks
        if ($healthData['application']['status'] === 'unhealthy') {
            $criticalIssues[] = 'Application is unhealthy!';
        }

        // Performance critical checks
        if ($healthData['performance']['error_rate_percent'] > 10) {
            $criticalIssues[] = 'Error rate is critical!';
        }

        // System critical checks
        if ($healthData['system']['disk_usage_percent'] > 90) {
            $criticalIssues[] = 'Disk usage critical!';
        }

        // Send alerts for critical issues
        foreach ($criticalIssues as $issue) {
            $this->error("🚨 CRITICAL: {$issue}");
            Log::critical("Health Monitor: {$issue}");
        }
    }

    protected function logHealthData($healthData, $timestamp)
    {
        $logData = [
            'timestamp' => $timestamp,
            'health_data' => $healthData,
        ];

        $logFile = storage_path('logs/health-monitor.json');
        file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
    }
}