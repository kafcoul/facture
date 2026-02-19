<x-mail::message>
# Nouvelle facture 📄

Bonjour {{ $clientName }},

Vous avez reçu une nouvelle facture.

**Détails de la facture :**

| | |
|---|---|
| **Numéro** | {{ $invoiceNumber }} |
| **Date d'émission** | {{ $issuedAt }} |
| **Montant** | {{ $total }} |
| **Échéance** | {{ $dueDate }} |

<x-mail::button :url="$paymentUrl" color="primary">
Voir et payer la facture
</x-mail::button>

Vous pouvez également télécharger la facture au format PDF :

<x-mail::button :url="$downloadUrl" color="success">
Télécharger le PDF
</x-mail::button>

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
