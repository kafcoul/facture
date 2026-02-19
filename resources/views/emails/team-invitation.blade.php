<x-mail::message>
# Vous êtes invité(e) à rejoindre {{ $tenantName }}

Bonjour{{ $invitation->name ? ' ' . $invitation->name : '' }},

**{{ $inviterName }}** vous invite à rejoindre l'équipe **{{ $tenantName }}** sur Invoice SaaS.

**Rôle proposé :** {{ $role }}

<x-mail::button :url="$acceptUrl" color="primary">
Accepter l'invitation
</x-mail::button>

Cette invitation expire le **{{ $expiresAt }}**.

---

**Qu'est-ce qu'Invoice SaaS ?**

Invoice SaaS est une plateforme de facturation en ligne qui permet aux entreprises de créer, gérer et envoyer des factures professionnelles facilement.

Si vous n'attendiez pas cette invitation ou si vous ne connaissez pas {{ $inviterName }}, vous pouvez ignorer cet email.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
