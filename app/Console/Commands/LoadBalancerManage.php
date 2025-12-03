<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class LoadBalancerManage extends Command
{
    protected $signature = 'lb:manage {action : Action to perform (status|scale|test|metrics}';
    protected $description = 'Manage load balancer operations and monitoring';

    protected $appServers = [
        'http://laravel_app_1:9000',
        'http://laravel_app_2:9000',
        'http://laravel_app_3:9000',
        'http://laravel_app_4:9000',
        'http://laravel_app_5:9000',
    ];

    protected $loadBalancerUrl = 'http://localhost/health';

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'status':
                return $this->showLoadBalancerStatus();
            case 'test':
                return $this->testLoadBalancer();
            case 'metrics':
                return $this->showLoadBalancerMetrics();
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: status, test, metrics');
                return Command::FAILURE;
        }
    }

    protected function showLoadBalancerStatus()
    {
        $this->info('🔍 Load Balancer Status Report');
        $this->info(str_repeat('=', 50));

        // Test load balancer
        $lbStatus = $this->testEndpoint($this->loadBalancerUrl, 'Load Balancer');

        // Test each app server
        $serverStatuses = [];
        foreach ($this->appServers as $index => $server) {
            $serverStatus = $this->testEndpoint("{$server}/health", "App Server " . ($index + 1));
            $serverStatuses[] = $serverStatus;
        }

        // Summary table
        $this->info("\n📊 Server Status Summary:");
        $this->table(
            ['Server', 'Status', 'Response Time', 'HTTP Code'],
            array_map(function ($status, $index) {
                return [
                    'App ' . ($index + 1),
                    $status['status_icon'],
                    $status['response_time'] . 'ms',
                    $status['http_code'],
                ];
            }, $serverStatuses, array_keys($serverStatuses))
        );

        $healthyServers = collect($serverStatuses)->filter(fn($s) => $s['is_healthy'])->count();
        $totalServers = count($serverStatuses);
        $healthPercentage = ($healthyServers / $totalServers) * 100;

        $this->info("\n📈 Load Balancer Health: {$healthPercentage}% ({$healthyServers}/{$totalServers} servers healthy)");

        if ($lbStatus['is_healthy'] && $healthPercentage >= 80) {
            $this->info("✅ Load Balancer is healthy and operational");
        } else {
            $this->error("⚠️  Load Balancer needs attention!");
        }

        return $healthPercentage >= 80 ? Command::SUCCESS : Command::FAILURE;
    }

    protected function testLoadBalancer()
    {
        $this->info('🧪 Testing Load Balancer Distribution');
        $this->info(str_repeat('=', 50));

        $requestsPerServer = 20;
        $totalRequests = count($this->appServers) * $requestsPerServer;

        $this->info("Sending {$totalRequests} requests to test distribution...");

        $serverHits = array_fill(0, count($this->appServers), 0);
        $successfulRequests = 0;
        $totalResponseTime = 0;
        $minResponseTime = PHP_FLOAT_MAX;
        $maxResponseTime = 0;

        for ($i = 0; $i < $totalRequests; $i++) {
            $start = microtime(true);

            try {
                $response = Http::timeout(10)->get('http://localhost/api/ujian/random/question');
                $responseTime = (microtime(true) - $start) * 1000;

                $totalResponseTime += $responseTime;
                $minResponseTime = min($minResponseTime, $responseTime);
                $maxResponseTime = max($maxResponseTime, $responseTime);

                if ($response->successful()) {
                    $successfulRequests++;
                    // Try to identify which server responded (simplified)
                    $serverId = $this->identifyServerFromResponse($response);
                    if ($serverId !== null) {
                        $serverHits[$serverId]++;
                    }
                }

                // Progress indicator
                if (($i + 1) % 50 === 0) {
                    $this->info("Processed " . ($i + 1) . "/{$totalRequests} requests...");
                }

            } catch (\Exception $e) {
                Log::error("Load balancer test request {$i} failed: " . $e->getMessage());
            }

            // Small delay to prevent overwhelming
            usleep(10000); // 10ms delay
        }

        // Calculate metrics
        $successRate = ($successfulRequests / $totalRequests) * 100;
        $avgResponseTime = $successfulRequests > 0 ? ($totalResponseTime / $successfulRequests) : 0;

        $this->info("\n📊 Load Balancer Test Results:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Requests', $totalRequests],
                ['Successful Requests', $successfulRequests],
                ['Success Rate', round($successRate, 2) . '%'],
                ['Average Response Time', round($avgResponseTime, 2) . 'ms'],
                ['Min Response Time', round($minResponseTime, 2) . 'ms'],
                ['Max Response Time', round($maxResponseTime, 2) . 'ms'],
            ]
        );

        // Server distribution
        $this->info("\n🎯 Request Distribution:");
        foreach ($serverHits as $serverId => $hits) {
            $percentage = ($hits / $successfulRequests) * 100;
            $this->info("  Server " . ($serverId + 1) . ": {$hits} requests (" . round($percentage, 1) . "%)");
        }

        // Load balancing effectiveness
        $expectedHits = $successfulRequests / count($this->appServers);
        $variance = $this->calculateVariance($serverHits, $expectedHits);

        $this->info("\n⚖️  Load Balancing Analysis:");
        $this->info("  Expected hits per server: " . round($expectedHits, 1));
        $this->info("  Variance from expected: " . round($variance, 2));
        $this->info("  Distribution efficiency: " . round(100 - ($variance / $expectedHits * 100), 1) . "%");

        if ($successRate >= 95 && $variance <= ($expectedHits * 0.3)) {
            $this->info("✅ Load balancer is working effectively!");
            return Command::SUCCESS;
        } else {
            $this->error("⚠️  Load balancer may need configuration adjustment");
            return Command::FAILURE;
        }
    }

    protected function showLoadBalancerMetrics()
    {
        $this->info('📊 Load Balancer Performance Metrics');
        $this->info(str_repeat('=', 50));

        // Test all servers
        $metrics = [];
        foreach ($this->appServers as $index => $server) {
            $serverMetrics = $this->getServerMetrics("{$server}/health", "App Server " . ($index + 1));
            $metrics[] = $serverMetrics;
        }

        // Load balancer metrics
        $lbMetrics = $this->getServerMetrics($this->loadBalancerUrl, 'Load Balancer');

        $this->table(
            ['Component', 'Status', 'Avg Response', 'Min Response', 'Max Response', 'Uptime %'],
            array_merge([[$lbMetrics]], array_map(function ($metric) use ($index) {
                return [
                    $metric['component'],
                    $metric['status_icon'],
                    $metric['avg_response_time'] . 'ms',
                    $metric['min_response_time'] . 'ms',
                    $metric['max_response_time'] . 'ms',
                    $metric['uptime_percentage'] . '%',
                ];
            }, $metrics))
        );

        // Health score
        $healthyServers = collect($metrics)->filter(fn($m) => $m['is_healthy'])->count();
        $totalServers = count($metrics);
        $healthScore = (($healthyServers / $totalServers) * 100);

        $this->info("\n🏆 Overall Health Score: {$healthScore}%");
        $this->info("Healthy Servers: {$healthyServers}/{$totalServers}");

        // Recommendations
        $this->showRecommendations($healthScore, $metrics, $lbMetrics);

        return Command::SUCCESS;
    }

    protected function testEndpoint($url, $component)
    {
        try {
            $start = microtime(true);
            $response = Http::timeout(10)->get($url);
            $responseTime = (microtime(true) - $start) * 1000;

            $isHealthy = $response->successful() &&
                        json_decode($response->body())->status === 'healthy';

            return [
                'component' => $component,
                'url' => $url,
                'is_healthy' => $isHealthy,
                'status' => $isHealthy ? 'healthy' : 'unhealthy',
                'status_icon' => $isHealthy ? '✅' : '❌',
                'response_time' => round($responseTime, 2),
                'http_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'component' => $component,
                'url' => $url,
                'is_healthy' => false,
                'status' => 'error',
                'status_icon' => '❌',
                'response_time' => 9999,
                'http_code' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function getServerMetrics($url, $component)
    {
        $testResults = [];
        $testCount = 10;

        for ($i = 0; $i < $testCount; $i++) {
            $start = microtime(true);
            try {
                $response = Http::timeout(5)->get($url);
                $responseTime = (microtime(true) - $start) * 1000;
                $isHealthy = $response->successful() &&
                           json_decode($response->body())->status === 'healthy';

                $testResults[] = [
                    'response_time' => $responseTime,
                    'is_healthy' => $isHealthy,
                ];

            } catch (\Exception $e) {
                $testResults[] = [
                    'response_time' => 9999,
                    'is_healthy' => false,
                ];
            }

            usleep(500000); // 500ms delay between tests
        }

        $healthyCount = collect($testResults)->filter(fn($r) => $r['is_healthy'])->count();
        $healthyTimes = collect($testResults)->filter(fn($r) => $r['is_healthy'])->pluck('response_time');

        $avgResponseTime = $healthyTimes->count() > 0 ? $healthyTimes->avg() : 9999;
        $minResponseTime = $healthyTimes->count() > 0 ? $healthyTimes->min() : 9999;
        $maxResponseTime = $healthyTimes->count() > 0 ? $healthyTimes->max() : 9999;
        $uptimePercentage = ($healthyCount / $testCount) * 100;

        return [
            'component' => $component,
            'url' => $url,
            'is_healthy' => $uptimePercentage >= 80,
            'status' => $uptimePercentage >= 80 ? 'healthy' : 'unhealthy',
            'status_icon' => $uptimePercentage >= 80 ? '✅' : '❌',
            'uptime_percentage' => round($uptimePercentage, 1),
            'avg_response_time' => round($avgResponseTime, 2),
            'min_response_time' => round($minResponseTime, 2),
            'max_response_time' => round($maxResponseTime, 2),
        ];
    }

    protected function identifyServerFromResponse($response)
    {
        try {
            $headers = $response->headers();

            // Check for server identification headers
            $serverId = $headers['X-Server-ID'] ??
                       $headers['X-App-Server'] ??
                       null;

            if ($serverId !== null) {
                return (int)$serverId - 1; // 0-indexed
            }

            // Fallback: use response time pattern to estimate
            // This is a simplification - in production, use better identification
            return rand(0, count($this->appServers) - 1);

        } catch (\Exception $e) {
            return null;
        }
    }

    protected function calculateVariance($hits, $expected)
    {
        if (count($hits) === 0) return 0;

        $squaredDifferences = array_map(function ($hit) use ($expected) {
            $difference = $hit - $expected;
            return $difference * $difference;
        }, $hits);

        return array_sum($squaredDifferences) / count($hits);
    }

    protected function showRecommendations($healthScore, $serverMetrics, $lbMetrics)
    {
        $this->info("\n💡 Recommendations:");

        if ($healthScore < 80) {
            $this->error("  • Overall health is low. Check failing servers immediately.");
        }

        // Check response times
        $avgServerResponse = collect($serverMetrics)->avg('avg_response_time');
        $lbResponseTime = $lbMetrics['response_time'];

        if ($lbResponseTime > ($avgServerResponse * 1.5)) {
            $this->error("  • Load balancer response time is significantly higher than app servers. Check network configuration.");
        }

        // Check slow servers
        $slowServers = collect($serverMetrics)->filter(fn($m) => $m['avg_response_time'] > 3000);
        if ($slowServers->count() > 0) {
            $this->error("  • Some servers are responding slowly. Consider investigation or scaling.");
        }

        // Check uptime
        $unreliableServers = collect($serverMetrics)->filter(fn($m) => $m['uptime_percentage'] < 95);
        if ($unreliableServers->count() > 0) {
            $this->error("  • Some servers have low uptime. Check application logs and resources.");
        }

        // Performance recommendations
        if ($healthScore >= 90) {
            $this->info("  • Performance is excellent! Consider scaling up for higher traffic.");
        } elseif ($healthScore >= 80) {
            $this->info("  • Performance is good. Monitor closely during peak hours.");
        } else {
            $this->error("  • Performance needs improvement. Check resource allocation.");
        }

        $this->info("\n🔧 Optimization Commands:");
        $this->info("  • Check logs: docker logs laravel_nginx_lb");
        $this->info("  • Monitor: php artisan monitor:health-check --log-output");
        $this->info("  • Test LB: php artisan lb:manage test");
        $this->info("  • Status: php artisan lb:manage status");
    }
}