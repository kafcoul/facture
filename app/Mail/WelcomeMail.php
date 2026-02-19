<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur ' . config('app.name') . ' ! 🎉',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $planLabel = match ($this->user->plan) {
            'starter' => 'Starter (Gratuit)',
            'pro' => 'Pro',
            'enterprise' => 'Enterprise',
            default => 'Starter',
        };

        return new Content(
            markdown: 'emails.welcome',
            with: [
                'user' => $this->user,
                'userName' => $this->user->name,
                'planLabel' => $planLabel,
                'dashboardUrl' => url('/client'),
                'trialEndsAt' => $this->user->trial_ends_at?->format('d/m/Y'),
            ],
        );
    }
}
