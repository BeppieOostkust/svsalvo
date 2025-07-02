<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected User $blockedUser,
        protected User $blockedBy
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Gebruiker Geblokkeerd - ' . config('app.name'))
            ->greeting('Account Status Update')
            ->line("De gebruiker **{$this->blockedUser->name}** ({$this->blockedUser->email}) is geblokkeerd door {$this->blockedBy->name}.")
            ->when($this->blockedUser->blocked_reason, function ($mail) {
                return $mail->line("**Reden:** {$this->blockedUser->blocked_reason}");
            })
            ->line("**Geblokkeerd op:** {$this->blockedUser->blocked_at->format('d-m-Y H:i')}")
            ->line('De gebruiker kan niet meer inloggen totdat het account is gedeblokkeerd.')
            ->action('Bekijk Gebruikers', route('filament.admin.resources.users.index'))
            ->line('Deze notificatie is automatisch verstuurd.')
            ->salutation('Met vriendelijke groet,')
            ->salutation(config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'blocked_user_id' => $this->blockedUser->id,
            'blocked_user_name' => $this->blockedUser->name,
            'blocked_user_email' => $this->blockedUser->email,
            'blocked_by_id' => $this->blockedBy->id,
            'blocked_by_name' => $this->blockedBy->name,
            'blocked_reason' => $this->blockedUser->blocked_reason,
            'blocked_at' => $this->blockedUser->blocked_at,
        ];
    }
}
