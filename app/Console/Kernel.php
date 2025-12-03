<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Commands for health monitoring
        \App\Console\Commands\MonitorHealthCheck::class,

        // Commands for load balancer management
        \App\Console\Commands\LoadBalancerManage::class,
    ];

    protected function commands()
    {
        return array_merge(parent::commands(), $this->commands);
    }

    protected function schedule(Schedule $schedule)
    {
        // Health check every 5 minutes
        $schedule->command('monitor:health-check --log-output')
                  ->everyFiveMinutes()
                  ->withoutOverlapping()
                  ->appendOutputTo(storage_path('logs/health-monitor.log'))
                  ->emailOutputTo(['admin@example.com'])
                  ->runInBackground();

        // Load balancer status check every hour
        $schedule->command('lb:manage status')
                  ->hourly()
                  ->withoutOverlapping()
                  ->appendOutputTo(storage_path('logs/load-balancer-status.log'))
                  ->runInBackground();

        // Clear health monitoring logs daily
        $schedule->exec('rm -f ' . storage_path('logs/health-monitor.json'))
                  ->daily()
                  ->runInBackground();

        // Archive old health monitoring logs weekly
        $schedule->exec('find ' . storage_path('logs') . ' -name "health-monitor*.log" -mtime +7 -delete')
                  ->weekly()
                  ->runInBackground();
    }
}