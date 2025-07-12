<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestProfileNotification extends Command
{
    protected $signature = 'test:profile-notification {user_id}';
    protected $description = 'Test profile update notification';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User not found");
            return 1;
        }
        
        $service = new NotificationService();
        $notification = $service->notifyProfileUpdated($user, ['voornaam', 'telefoonnummer', 'adres']);
        
        $this->info("Notification created:");
        $this->info("Title: " . $notification->title);
        $this->info("Message: " . $notification->message);
        $this->info("User: " . $user->name);
        
        return 0;
    }
}
