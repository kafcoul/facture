<x-mail::message>
    # {{ $isOverdue ? '⚠️ Facture en retard' : '📋 Rappel de paiement' }}

    Bonjour {{ $clientName }},

    @if ($isOverdue)
        La facture **{{ $invoiceNumber }}** est en retard de **{{ $daysOverdue }} jours**. Nous vous prions de bien
        vouloir procéder au règlement dans les meilleurs délais.
    @else
        Nous souhaitons vous rappeler que la facture **{{ $invoiceNumber }}** arrive à échéance le
        **{{ $dueDate }}**.
    @endif

    **Récapitulatif :**

    | | |
    |---|---|
    | **Facture** | {{ $invoiceNumber }} |
    | **Montant** | {{ $total }} |
    | **Échéance** | {{ $dueDate }} |
    @if ($isOverdue)
        | **Retard** | {{ $daysOverdue }} jours |
    @endif

    <x-mail::button :url="$paymentUrl" color="{{ $isOverdue ? 'error' : 'primary' }}">
        Payer maintenant
    </x-mail::button>

    @if ($isOverdue)
        Si vous avez déjà effectué le paiement, veuillez ignorer ce message. Le traitement peut prendre quelques jours
        ouvrés.
    @else
        Vous pouvez régler cette facture en ligne en cliquant sur le bouton ci-dessus.
    @endif

    Cordialement,<br>
    L'équipe {{ config('app.name') }}
</x-mail::message>
