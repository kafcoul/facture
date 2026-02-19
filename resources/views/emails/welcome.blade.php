<x-mail::message>
    # Bienvenue sur {{ config('app.name') }} ! 🎉

    Bonjour {{ $userName }},

    Votre compte a été créé avec succès. Vous êtes inscrit(e) au plan **{{ $planLabel }}**.

    @if ($trialEndsAt)
        Votre période d'essai se termine le **{{ $trialEndsAt }}**.
    @endif

    ## 🚀 Pour commencer

    Voici les premières étapes pour bien démarrer :

    1. **Créez vos premiers clients** — Ajoutez vos contacts professionnels
    2. **Ajoutez vos produits/services** — Définissez votre catalogue
    3. **Créez votre première facture** — En quelques clics !
    4. **Personnalisez vos paramètres** — Logo, coordonnées, devise

    <x-mail::button :url="$dashboardUrl" color="primary">
        Accéder à mon tableau de bord
    </x-mail::button>

    ## 💡 Fonctionnalités disponibles

    @component('mail::table')
        | Fonctionnalité | Disponible |
        |:---|:---:|
        | Création de factures | ✅ |
        | Export PDF | ✅ |
        | Envoi par email | ✅ |
        | Paiements en ligne | ✅ |
        | Multi-devises | {{ $planLabel !== 'Starter (Gratuit)' ? '✅' : '❌' }} |
        | Gestion d'équipe | {{ str_contains($planLabel, 'Enterprise') ? '✅' : '❌' }} |
        | API REST | {{ str_contains($planLabel, 'Enterprise') ? '✅' : '❌' }} |
    @endcomponent

    Besoin d'aide ? Répondez simplement à cet email.

    Cordialement,<br>
    L'équipe {{ config('app.name') }}
</x-mail::message>
