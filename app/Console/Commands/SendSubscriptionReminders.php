<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendSubscriptionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:send-reminders {--days=7 : Days before expiration to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription renewal reminders to users with expiring subscriptions';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        
        $expiringSubscriptions = Subscription::with('user')
            ->expiringSoon($days)
            ->get();

        if ($expiringSubscriptions->isEmpty()) {
            $this->info('No expiring subscriptions found.');
            return;
        }

        $count = 0;
        foreach ($expiringSubscriptions as $subscription) {
            $daysLeft = $subscription->daysUntilExpiration();
            
            $this->notificationService->sendEmailAndPush(
                $subscription->user,
                'Subscription Renewal Reminder - Oja Ewa',
                'subscription_reminder',
                'Subscription Expiring Soon',
                "Your {$subscription->plan_name} subscription expires in {$daysLeft} days.",
                ['subscription' => $subscription],
                [
                    'subscription_id' => $subscription->id,
                    'days_left' => $daysLeft,
                    'deep_link' => '/subscription/manage'
                ]
            );
            
            $count++;
        }

        $this->info("Sent {$count} subscription renewal reminders.");
    }
}
