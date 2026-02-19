<x-mail::message>
    # Paiement reçu ✅

    Bonjour {{ $clientName }},

    Nous avons bien reçu votre paiement pour la facture **{{ $invoiceNumber }}**.

    **Détails du paiement :**

    | | |
    |---|---|
    | **Montant payé** | {{ $amount }} |
    | **Facture** | {{ $invoiceNumber }} |
    | **Moyen de paiement** | {{ $gateway }} |
    | **Date** | {{ $paidAt }} |

    <x-mail::button :url="$invoiceUrl" color="success">
        Voir la facture
    </x-mail::button>

    Merci pour votre confiance !

    Cordialement,<br>
    L'équipe {{ config('app.name') }}
</x-mail::message>
