<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a notification for a specific user
     */
    public function createForUser(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create notifications for multiple users
     */
    public function createForUsers(Collection $users, string $type, string $title, string $message, array $data = []): Collection
    {
        $notifications = collect();

        foreach ($users as $user) {
            $notifications->push($this->createForUser($user, $type, $title, $message, $data));
        }

        return $notifications;
    }

    /**
     * Create notifications for all active members
     */
    public function createForAllMembers(string $type, string $title, string $message, array $data = []): Collection
    {
        $users = User::where('is_active_member', true)->get();
        return $this->createForUsers($users, $type, $title, $message, $data);
    }

    /**
     * Create notification when a new activity is created
     */
    public function notifyNewActivity($activity): Collection
    {
        return $this->createForAllMembers(
            'activity',
            'Nieuwe activiteit: ' . $activity->title,
            'Er is een nieuwe activiteit toegevoegd: ' . ($activity->description ?? 'Bekijk de details voor meer informatie.'),
            [
                'activity_id' => $activity->id,
                'url' => '/activiteiten',
            ]
        );
    }

    /**
     * Create notification when a new match is created
     */
    public function notifyNewMatch($match): Collection
    {
        return $this->createForAllMembers(
            'match',
            'Nieuwe wedstrijd: ' . ($match->title ?? 'Nieuwe wedstrijd'),
            'Er is een nieuwe wedstrijd gepland. Schrijf je in!',
            [
                'match_id' => $match->id,
                'url' => '/wedstrijden',
            ]
        );
    }

    /**
     * Create notification when a new news article is published
     */
    public function notifyNewArticle($article): Collection
    {
        return $this->createForAllMembers(
            'nieuws',
            'Nieuw artikel: ' . $article->title,
            'Er is een nieuw artikel gepubliceerd: ' . ($article->excerpt ?? substr(strip_tags($article->content), 0, 100) . '...'),
            [
                'article_id' => $article->id,
                'url' => '/nieuws/' . $article->slug,
            ]
        );
    }

    /**
     * Create notification when user profile is updated by admin
     */
    public function notifyProfileUpdated(User $user, array $updatedFields = []): Notification
    {
        if (empty($updatedFields)) {
            $message = "Je profielinformatie is bijgewerkt door een beheerder.";
        } else {
            $fieldsText = implode(', ', $updatedFields);
            $message = "De volgende gegevens zijn bijgewerkt door een beheerder: {$fieldsText}.";
        }
        
        return $this->createForUser(
            $user,
            'profile_updated',
            'Persoonlijke gegevens bijgewerkt',
            $message,
            [
                'updated_fields' => $updatedFields,
                'url' => '/profile',
            ]
        );
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsReadForUser(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Delete old notifications (older than 30 days)
     */
    public function cleanupOldNotifications(): int
    {
        return Notification::where('created_at', '<', now()->subDays(30))->delete();
    }

    /**
     * Get user notifications with pagination
     */
    public function getUserNotifications(User $user, int $limit = 10)
    {
        return $user->notifications()->limit($limit)->get();
    }
}
