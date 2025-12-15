<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send an email from a template
     */
    public function sendFromTemplate(
        string $templateSlug,
        string $toEmail,
        array $variables = [],
        ?User $user = null,
        ?string $toName = null
    ): bool {
        // Check if this notification type is enabled
        if (!EmailSetting::isEnabled($templateSlug)) {
            Log::info("Email notification disabled for: {$templateSlug}");
            return false;
        }

        // Get the template
        $template = EmailTemplate::where('slug', $templateSlug)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            Log::error("Email template not found or inactive: {$templateSlug}");
            return false;
        }

        // Process the content
        $subject = $template->getProcessedSubject($variables);
        $htmlContent = $template->getProcessedHtmlContent($variables);
        $textContent = $template->getProcessedTextContent($variables);

        // Create log entry
        $log = EmailLog::create([
            'email_template_id' => $template->id,
            'user_id' => $user?->id,
            'to_email' => $toEmail,
            'to_name' => $toName ?? $user?->name ?? $toEmail,
            'subject' => $subject,
            'html_content' => $htmlContent,
            'text_content' => $textContent,
            'status' => 'pending',
            'variables' => $variables,
        ]);

        try {
            // Send the email
            Mail::send([], [], function ($message) use ($toEmail, $toName, $subject, $htmlContent, $textContent) {
                $message->to($toEmail, $toName)
                    ->subject($subject)
                    ->html($htmlContent)
                    ->text($textContent);
            });

            // Update log as sent
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            // Update log as failed
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Failed to send email: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send temporary password email to new user
     */
    public function sendTemporaryPasswordEmail(User $user, string $temporaryPassword): bool
    {
        return $this->sendFromTemplate(
            'new-user-temp-password',
            $user->email,
            [
                'name' => $user->name,
                'email' => $user->email,
                'temporary_password' => $temporaryPassword,
                'login_url' => route('login'),
                'site_name' => config('app.name', 'KNSA Vereniging'),
            ],
            $user,
            $user->name
        );
    }

    /**
     * Send new match notification
     */
    public function sendNewMatchEmail(User $user, $match): bool
    {
        return $this->sendFromTemplate(
            'new-match',
            $user->email,
            [
                'name' => $user->name,
                'match_name' => $match->naam ?? 'Nieuwe wedstrijd',
                'match_date' => $match->start_datum ? \Carbon\Carbon::parse($match->start_datum)->format('d-m-Y') : 'Nog niet bekend',
                'match_time' => $match->start_datum ? \Carbon\Carbon::parse($match->start_datum)->format('H:i') : 'Nog niet bekend',
                'match_location' => 'Nog niet bekend', // Matches model heeft geen location veld
                'match_url' => url('/admin/matches/' . $match->id . '/edit'),
                'site_name' => config('app.name', 'KNSA Vereniging'),
            ],
            $user,
            $user->name
        );
    }

    /**
     * Send new activity notification
     */
    public function sendNewActivityEmail(User $user, $activity): bool
    {
        return $this->sendFromTemplate(
            'new-activity',
            $user->email,
            [
                'name' => $user->name,
                'activity_name' => $activity->title ?? 'Nieuwe activiteit',
                'activity_date' => $activity->start_date ? \Carbon\Carbon::parse($activity->start_date)->format('d-m-Y') : 'Nog niet bekend',
                'activity_time' => $activity->start_time ?? 'Nog niet bekend',
                'activity_location' => $activity->location ?? 'Nog niet bekend',
                'activity_description' => strip_tags($activity->description ?? ''),
                'activity_url' => url('/activiteiten/' . $activity->id),
                'site_name' => config('app.name', 'KNSA Vereniging'),
            ],
            $user,
            $user->name
        );
    }

    /**
     * Send feedback response notification
     */
    public function sendFeedbackResponseEmail(User $user, $feedback, $response): bool
    {
        return $this->sendFromTemplate(
            'feedback-response',
            $user->email,
            [
                'name' => $user->name,
                'feedback_title' => $feedback->title ?? 'Jouw feedback',
                'feedback_content' => strip_tags($feedback->description ?? ''),
                'response_content' => $response,
                'feedback_url' => url('/feedback/' . $feedback->id),
                'site_name' => config('app.name', 'KNSA Vereniging'),
            ],
            $user,
            $user->name
        );
    }

    /**
     * Send privacy policy update notification
     */
    public function sendPrivacyPolicyUpdateEmail(User $user, $legalDocument): bool
    {
        return $this->sendFromTemplate(
            'privacy-policy-update',
            $user->email,
            [
                'name' => $user->name,
                'document_title' => $legalDocument->title ?? 'Privacy Verklaring',
                'document_version' => $legalDocument->version ?? '1.0',
                'document_date' => $legalDocument->effective_date ? \Carbon\Carbon::parse($legalDocument->effective_date)->format('d-m-Y') : now()->format('d-m-Y'),
                'document_url' => route('privacy-policy'),
                'changes_summary' => $legalDocument->changes_summary ?? 'Bekijk de volledige wijzigingen op onze website.',
                'site_name' => config('app.name', 'KNSA Vereniging'),
            ],
            $user,
            $user->name
        );
    }

    /**
     * Send bulk emails to multiple users
     */
    public function sendBulk(string $templateSlug, array $recipients, array $variables = []): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $success = $this->sendFromTemplate(
                    $templateSlug,
                    $recipient->email,
                    array_merge($variables, ['name' => $recipient->name]),
                    $recipient,
                    $recipient->name
                );
            } else {
                $success = $this->sendFromTemplate(
                    $templateSlug,
                    $recipient['email'],
                    array_merge($variables, ['name' => $recipient['name'] ?? '']),
                    null,
                    $recipient['name'] ?? null
                );
            }

            if ($success) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }
}
