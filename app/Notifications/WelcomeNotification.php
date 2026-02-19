<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification de bienvenue envoyée après inscription
 */
class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [30, 120, 600];

    public function __construct(
        public string $plan = 'starter'
    ) {}

    /**
     * Canaux de notification
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Notification email
     */
    public function toMail(object $notifiable): MailMessage
    {
        $planLabels = [
            'starter' => 'Starter (Gratuit)',
            'pro' => 'Pro',
            'enterprise' => 'Enterprise',
        ];
        $planLabel = $planLabels[$this->plan] ?? 'Starter';
        $trialDays = 30;

        $message = (new MailMessage)
            ->subject("🎉 Bienvenue sur InvoiceSaaS, {$notifiable->name} !")
            ->greeting("Bienvenue {$notifiable->name} !")
            ->line("Votre compte InvoiceSaaS est prêt ! Vous avez choisi le plan **{$planLabel}**.")
            ->line("Vous bénéficiez de **{$trialDays} jours d'essai gratuit** pour découvrir toutes les fonctionnalités.");

        // Steps to get started
        $message->line('---')
            ->line('**🚀 Pour bien démarrer :**')
            ->line('1. **Ajoutez vos clients** — Commencez par créer votre base clients')
            ->line('2. **Créez votre première facture** — En quelques clics')
            ->line('3. **Personnalisez vos templates** — Choisissez le style qui vous correspond')
            ->line('4. **Configurez vos paiements** — Activez les passerelles de paiement');

        $message->action('Accéder à mon espace', url('/client'))
            ->line('---')
            ->line("**Besoin d'aide ?** Notre équipe support est disponible pour vous accompagner.")
            ->salutation('À bientôt sur InvoiceSaaS ! 🇸🇳🇨🇮🇧🇫🇲🇱');

        return $message;
    }

    /**
     * Notification base de données
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'welcome',
            'plan' => $this->plan,
            'message' => "Bienvenue sur InvoiceSaaS ! Votre compte {$this->plan} est activé avec 30 jours d'essai gratuit.",
        ];
    }
}
