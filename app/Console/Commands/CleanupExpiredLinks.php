<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LinkAmount;
use App\Models\Order;
use Carbon\Carbon;

class CleanupExpiredLinks extends Command
{
    protected $signature = 'links:cleanup {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up expired payment links';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Cleaning up expired payment links...');
        
        // Find links older than 24 hours
        $expiredLinks = LinkAmount::where('created_at', '<', now()->subHours(24))->get();
        
        if ($expiredLinks->isEmpty()) {
            $this->info('No expired links found.');
            return;
        }
        
        $this->info("Found {$expiredLinks->count()} expired links:");
        
        foreach ($expiredLinks as $link) {
            $this->line("- Link ID: {$link->id}, Token: {$link->token}, Created: {$link->created_at}");
            
            if (!$dryRun) {
                // Check if order is still pending before deleting
                if ($link->order && $link->order->status === 'pending') {
                    $this->warn("  Order #{$link->order->id} is still pending - keeping link");
                } else {
                    $link->delete();
                    $this->info("  Deleted expired link");
                }
            }
        }
        
        if ($dryRun) {
            $this->info('Dry run completed. Use without --dry-run to actually delete expired links.');
        } else {
            $this->info('Cleanup completed.');
        }
    }
}
