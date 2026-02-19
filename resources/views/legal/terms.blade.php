@extends('legal.layout')

@section('title', "Conditions Générales d'Utilisation")
@section('meta_description', "Conditions Générales d'Utilisation de la plateforme InvoiceSaaS. Règles d'utilisation du
    service de facturation en ligne.")
@section('breadcrumb', "Conditions Générales d'Utilisation")
@section('heading', "Conditions Générales d'Utilisation")
@section('updated_date', '19/02/2026')

@section('hero_icon')
    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
    </svg>
@endsection

@section('content')

    <div class="callout-info mb-8">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <div>
                <p class="text-sm text-brand-900 font-semibold mb-1" style="margin-top:0">Résumé</p>
                <p class="text-sm text-brand-700/80" style="margin-bottom:0">Les présentes CGU régissent votre utilisation
                    de la plateforme InvoiceSaaS. En utilisant notre service, vous acceptez ces conditions. Lisez-les
                    attentivement.</p>
            </div>
        </div>
    </div>

    <p>Les présentes Conditions Générales d'Utilisation (ci-après « <strong>CGU</strong> ») régissent l'accès et
        l'utilisation de la plateforme <strong>InvoiceSaaS</strong>, éditée par la société InvoiceSaaS SAS (ci-après «
        <strong>la Société</strong> »), accessible à l'adresse <a href="/">invoicesaas.com</a>.</p>

    <p>En accédant ou en utilisant le Service, vous acceptez d'être lié par les présentes CGU. Si vous n'acceptez pas ces
        conditions, veuillez ne pas utiliser le Service.</p>

    <h2>📋 1. Définitions</h2>

    <ul>
        <li><strong>Service</strong> : la plateforme de facturation en ligne InvoiceSaaS, incluant toutes ses
            fonctionnalités (création de factures, paiements, analytics, export, etc.).</li>
        <li><strong>Utilisateur</strong> : toute personne physique ou morale qui crée un compte et utilise le Service.</li>
        <li><strong>Compte</strong> : l'espace personnel de l'Utilisateur, accessible via ses identifiants de connexion.
        </li>
        <li><strong>Contenu</strong> : toute donnée, texte, image, logo ou information saisie par l'Utilisateur sur la
            plateforme.</li>
        <li><strong>Abonnement</strong> : le plan tarifaire choisi par l'Utilisateur (Starter, Pro ou Enterprise).</li>
    </ul>

    <h2>🎯 2. Objet du Service</h2>

    <p>InvoiceSaaS est une plateforme SaaS (Software as a Service) de facturation en ligne qui permet aux entrepreneurs,
        freelances et PME africains de :</p>

    <ul>
        <li>Créer et envoyer des factures professionnelles</li>
        <li>Accepter des paiements via Mobile Money (Orange Money, Wave, MTN MoMo, Moov Money, Free Money, M-Pesa) et carte
            bancaire (Visa, Mastercard)</li>
        <li>Gérer la facturation récurrente</li>
        <li>Suivre les paiements et le chiffre d'affaires via un tableau de bord</li>
        <li>Exporter les données en PDF et CSV</li>
        <li>Gérer les clients et les produits/services</li>
    </ul>

    <h2>👤 3. Inscription et Compte</h2>

    <h3>3.1 Création de compte</h3>
    <p>Pour utiliser le Service, l'Utilisateur doit créer un compte en fournissant des informations exactes et à jour : nom
        complet, adresse email professionnelle et mot de passe. L'Utilisateur s'engage à maintenir la confidentialité de ses
        identifiants.</p>

    <h3>3.2 Conditions d'éligibilité</h3>
    <p>L'Utilisateur doit être âgé d'au moins 18 ans et avoir la capacité juridique de conclure un contrat. Pour les
        personnes morales, l'Utilisateur doit avoir l'autorité nécessaire pour engager l'entité.</p>

    <h3>3.3 Sécurité du compte</h3>
    <p>L'Utilisateur est responsable de toute activité effectuée sous son compte. Il s'engage à :</p>
    <ul>
        <li>Choisir un mot de passe robuste (minimum 8 caractères)</li>
        <li>Activer l'authentification à deux facteurs (2FA) lorsque disponible</li>
        <li>Notifier immédiatement InvoiceSaaS de toute utilisation non autorisée de son compte</li>
        <li>Ne pas partager ses identifiants avec des tiers non autorisés</li>
    </ul>

    <h2>💰 4. Plans et Tarification</h2>

    <h3>4.1 Plans disponibles</h3>
    <p>InvoiceSaaS propose trois plans tarifaires :</p>

    <table>
        <thead>
            <tr>
                <th>Plan</th>
                <th>Prix</th>
                <th>Inclus</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Starter</strong></td>
                <td>Gratuit</td>
                <td>5 factures/mois, 1 utilisateur, export PDF, support email</td>
            </tr>
            <tr>
                <td><strong>Pro</strong></td>
                <td>15 200 FCFA/mois</td>
                <td>Factures illimitées, 3 utilisateurs, Mobile Money & Carte, facturation récurrente, export PDF & CSV</td>
            </tr>
            <tr>
                <td><strong>Enterprise</strong></td>
                <td>52 000 FCFA/mois</td>
                <td>Tout de Pro, utilisateurs illimités, multi-devises, API & Webhooks, 2FA, account manager dédié</td>
            </tr>
        </tbody>
    </table>

    <h3>4.2 Facturation et paiement</h3>
    <p>Les abonnements sont facturés mensuellement ou annuellement (avec une réduction de 20% pour l'engagement annuel). Le
        paiement est dû à l'avance et est non remboursable, sauf disposition contraire prévue par la loi applicable.</p>

    <h3>4.3 Modification des tarifs</h3>
    <p>InvoiceSaaS se réserve le droit de modifier ses tarifs. Toute modification sera notifiée à l'Utilisateur au moins
        <strong>30 jours</strong> avant son entrée en vigueur. L'Utilisateur pourra résilier son abonnement s'il n'accepte
        pas les nouveaux tarifs.</p>

    <h2>✅ 5. Utilisation du Service</h2>

    <h3>5.1 Usage autorisé</h3>
    <p>L'Utilisateur s'engage à utiliser le Service conformément à sa destination, aux lois applicables et aux présentes
        CGU. Le Service est destiné à un usage professionnel de facturation.</p>

    <h3>5.2 Usages interdits</h3>

    <div class="callout-warning mb-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <p class="text-sm text-amber-800" style="margin:0">Les usages suivants sont <strong>strictement
                    interdits</strong> et peuvent entraîner la suspension immédiate de votre compte.</p>
        </div>
    </div>

    <ul>
        <li>Utiliser le Service à des fins illégales ou frauduleuses</li>
        <li>Émettre de fausses factures ou des documents trompeurs</li>
        <li>Tenter de contourner les limitations du plan choisi</li>
        <li>Accéder de manière non autorisée aux systèmes d'InvoiceSaaS</li>
        <li>Revendre ou sous-licencier l'accès au Service sans autorisation</li>
        <li>Transmettre des virus, malwares ou tout code malveillant</li>
        <li>Surcharger volontairement l'infrastructure du Service</li>
    </ul>

    <h2>©️ 6. Propriété intellectuelle</h2>

    <h3>6.1 Droits d'InvoiceSaaS</h3>
    <p>La plateforme InvoiceSaaS, son code source, son design, ses logos, textes et tout contenu original sont la propriété
        exclusive d'InvoiceSaaS SAS. Toute reproduction, modification ou distribution non autorisée est interdite.</p>

    <h3>6.2 Contenu de l'Utilisateur</h3>
    <p>L'Utilisateur conserve la propriété de tout le contenu qu'il saisit sur la plateforme (données clients, factures,
        logos, etc.). L'Utilisateur accorde à InvoiceSaaS une licence limitée pour traiter ce contenu dans le seul but de
        fournir le Service.</p>

    <h2>🔒 7. Protection des données</h2>

    <div class="callout-success mb-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
            <p class="text-sm text-emerald-800" style="margin:0">Vos données sont protégées. Consultez notre <a
                    href="/politique-confidentialite" class="text-emerald-700 underline font-semibold">Politique de
                    Confidentialité</a> pour tous les détails.</p>
        </div>
    </div>

    <p>Le traitement des données personnelles est régi par notre <a href="/politique-confidentialite">Politique de
            Confidentialité</a>. InvoiceSaaS s'engage à protéger les données de ses Utilisateurs conformément à la
        réglementation applicable en matière de protection des données personnelles.</p>

    <h2>⚡ 8. Disponibilité du Service</h2>

    <h3>8.1 Engagement de disponibilité</h3>
    <p>InvoiceSaaS s'efforce de maintenir le Service accessible 24h/24, 7j/7. Nous visons un taux de disponibilité de
        <strong>99,5%</strong> par mois (hors maintenance programmée).</p>

    <h3>8.2 Maintenance</h3>
    <p>Des interruptions temporaires peuvent survenir pour maintenance, mise à jour ou amélioration du Service. Les
        maintenances programmées seront annoncées avec un préavis raisonnable.</p>

    <h3>8.3 Limitation de responsabilité</h3>
    <p>InvoiceSaaS ne saurait être tenu responsable des dommages résultant de l'indisponibilité temporaire du Service,
        notamment en cas de force majeure, défaillance des réseaux de télécommunication ou maintenance.</p>

    <h2>🚪 9. Résiliation</h2>

    <h3>9.1 Par l'Utilisateur</h3>
    <p>L'Utilisateur peut résilier son abonnement à tout moment depuis les paramètres de son compte. La résiliation prend
        effet à la fin de la période de facturation en cours. Le plan Starter gratuit peut être fermé à tout moment.</p>

    <h3>9.2 Par InvoiceSaaS</h3>
    <p>InvoiceSaaS se réserve le droit de suspendre ou résilier un compte en cas de :</p>
    <ul>
        <li>Violation des présentes CGU</li>
        <li>Activité frauduleuse ou illégale</li>
        <li>Non-paiement après relance</li>
        <li>Inactivité prolongée (plus de 12 mois)</li>
    </ul>

    <h3>9.3 Conséquences de la résiliation</h3>

    <div class="callout-info mb-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-brand-800" style="margin:0">Vous disposez de <strong>30 jours</strong> après résiliation
                pour exporter toutes vos données.</p>
        </div>
    </div>

    <p>En cas de résiliation, l'Utilisateur dispose de <strong>30 jours</strong> pour exporter ses données (factures,
        clients, rapports). Passé ce délai, les données seront supprimées conformément à notre politique de confidentialité.
    </p>

    <h2>⚖️ 10. Responsabilité</h2>

    <p>InvoiceSaaS fournit le Service « en l'état ». Dans les limites permises par la loi :</p>
    <ul>
        <li>InvoiceSaaS ne garantit pas que le Service sera exempt d'erreurs ou d'interruptions</li>
        <li>L'Utilisateur est seul responsable de la conformité fiscale et légale de ses factures</li>
        <li>InvoiceSaaS ne pourra être tenu responsable des dommages indirects, pertes de revenus ou de données</li>
        <li>La responsabilité totale d'InvoiceSaaS est limitée au montant payé par l'Utilisateur au cours des 12 derniers
            mois</li>
    </ul>

    <h2>🏛️ 11. Droit applicable et litiges</h2>

    <p>Les présentes CGU sont régies par le droit sénégalais. En cas de litige, les parties s'engagent à rechercher une
        solution amiable. À défaut, les tribunaux compétents de <strong>Dakar, Sénégal</strong> seront seuls compétents.</p>

    <h2>🔄 12. Modification des CGU</h2>

    <p>InvoiceSaaS se réserve le droit de modifier les présentes CGU. Les modifications seront notifiées par email et/ou par
        notification sur la plateforme. L'utilisation continue du Service après notification vaut acceptation des nouvelles
        CGU.</p>

    <h2>📧 13. Contact</h2>

    <p>Pour toute question relative aux présentes CGU, vous pouvez nous contacter :</p>
    <ul>
        <li><strong>Email</strong> : <a href="mailto:contact@invoicesaas.com">contact@invoicesaas.com</a></li>
        <li><strong>Adresse</strong> : InvoiceSaaS SAS, Dakar, Sénégal</li>
    </ul>

@endsection
