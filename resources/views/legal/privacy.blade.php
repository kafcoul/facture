@extends('legal.layout')

@section('title', 'Politique de Confidentialité')
@section('meta_description', "Politique de Confidentialité d'InvoiceSaaS. Découvrez comment nous collectons, utilisons et protégeons vos données personnelles.")
@section('breadcrumb', 'Politique de Confidentialité')
@section('heading', 'Politique de Confidentialité')
@section('updated_date', '19/02/2026')

@section('hero_icon')
<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
@endsection

@section('content')

<div class="callout-success mb-8">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        <div>
            <p class="text-sm text-emerald-900 font-semibold mb-1" style="margin-top:0">Votre vie privée est notre priorité</p>
            <p class="text-sm text-emerald-700/80" style="margin-bottom:0">Nous ne vendons <strong>jamais</strong> vos données. Cette politique explique exactement quelles données nous collectons, pourquoi, et comment nous les protégeons.</p>
        </div>
    </div>
</div>

<p>Chez <strong>InvoiceSaaS</strong>, la protection de vos données personnelles est une priorité. La présente Politique de Confidentialité décrit comment nous collectons, utilisons, stockons et protégeons vos informations lorsque vous utilisez notre plateforme de facturation en ligne.</p>

<p>En utilisant InvoiceSaaS, vous consentez aux pratiques décrites dans cette politique. Nous vous invitons à la lire attentivement.</p>

<h2>🏢 1. Responsable du traitement</h2>

<p>Le responsable du traitement des données personnelles est :</p>
<ul>
    <li><strong>Société</strong> : InvoiceSaaS SAS</li>
    <li><strong>Siège social</strong> : Dakar, Sénégal</li>
    <li><strong>Email DPO</strong> : <a href="mailto:dpo@invoicesaas.com">dpo@invoicesaas.com</a></li>
</ul>

<h2>📊 2. Données collectées</h2>

<h3>2.1 Données fournies par l'Utilisateur</h3>
<p>Lors de l'inscription et de l'utilisation du Service, nous collectons :</p>
<ul>
    <li><strong>Données d'identification</strong> : nom, prénom, adresse email, numéro de téléphone</li>
    <li><strong>Données professionnelles</strong> : nom de l'entreprise, numéro SIRET/NINEA, adresse professionnelle, secteur d'activité</li>
    <li><strong>Données de facturation</strong> : informations de paiement, historique des transactions, coordonnées bancaires ou Mobile Money</li>
    <li><strong>Données clients</strong> : informations sur les clients de l'Utilisateur (noms, adresses, emails) saisies pour la création de factures</li>
</ul>

<h3>2.2 Données collectées automatiquement</h3>
<p>Lors de votre navigation, nous collectons automatiquement :</p>
<ul>
    <li><strong>Données de connexion</strong> : adresse IP, type de navigateur, système d'exploitation, pages consultées, date et heure d'accès</li>
    <li><strong>Données d'utilisation</strong> : actions effectuées sur la plateforme, fréquence d'utilisation, fonctionnalités utilisées</li>
    <li><strong>Données de performance</strong> : temps de chargement, erreurs techniques</li>
</ul>

<h2>🎯 3. Finalités du traitement</h2>

<p>Vos données personnelles sont traitées pour les finalités suivantes :</p>

<table>
    <thead>
        <tr>
            <th>Finalité</th>
            <th>Base légale</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Gestion du compte utilisateur</td>
            <td>Exécution du contrat</td>
        </tr>
        <tr>
            <td>Fourniture du service de facturation</td>
            <td>Exécution du contrat</td>
        </tr>
        <tr>
            <td>Traitement des paiements</td>
            <td>Exécution du contrat</td>
        </tr>
        <tr>
            <td>Support client</td>
            <td>Exécution du contrat</td>
        </tr>
        <tr>
            <td>Amélioration du Service</td>
            <td>Intérêt légitime</td>
        </tr>
        <tr>
            <td>Envoi de communications marketing</td>
            <td>Consentement</td>
        </tr>
        <tr>
            <td>Prévention de la fraude</td>
            <td>Intérêt légitime</td>
        </tr>
        <tr>
            <td>Conformité légale et fiscale</td>
            <td>Obligation légale</td>
        </tr>
        <tr>
            <td>Statistiques et analytics</td>
            <td>Intérêt légitime</td>
        </tr>
    </tbody>
</table>

<h2>🍪 4. Cookies et technologies similaires</h2>

<h3>4.1 Types de cookies utilisés</h3>
<ul>
    <li><strong>Cookies essentiels</strong> : nécessaires au fonctionnement du Service (authentification, session, sécurité CSRF). Ils ne peuvent pas être désactivés.</li>
    <li><strong>Cookies de performance</strong> : permettent d'analyser l'utilisation du Service pour en améliorer les performances (temps de chargement, pages les plus visitées).</li>
    <li><strong>Cookies fonctionnels</strong> : mémorisent vos préférences (langue, thème, paramètres d'affichage) pour personnaliser votre expérience.</li>
    <li><strong>Cookies analytics</strong> : nous aident à comprendre comment les Utilisateurs interagissent avec le Service (via des outils tels que Google Analytics, Sentry).</li>
</ul>

<h3>4.2 Gestion des cookies</h3>
<p>Vous pouvez gérer vos préférences de cookies à tout moment via les paramètres de votre navigateur. Notez que la désactivation de certains cookies peut affecter le fonctionnement du Service.</p>

<h2>🤝 5. Partage des données</h2>

<h3>5.1 Sous-traitants</h3>
<p>Nous partageons vos données uniquement avec des sous-traitants de confiance, dans le strict cadre de la fourniture du Service :</p>
<ul>
    <li><strong>Hébergement</strong> : serveurs sécurisés (chiffrement au repos et en transit)</li>
    <li><strong>Paiements</strong> : prestataires de paiement (Stripe, Orange Money, Wave, MTN MoMo) pour le traitement sécurisé des transactions</li>
    <li><strong>Email</strong> : service d'envoi d'emails transactionnels (notifications, factures)</li>
    <li><strong>Monitoring</strong> : outils de surveillance pour garantir la disponibilité et la performance du Service (Sentry)</li>
</ul>

<h3>5.2 Aucune vente de données</h3>

<div class="callout-success mb-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-emerald-800" style="margin:0">InvoiceSaaS <strong>ne vend jamais</strong> vos données personnelles à des tiers. Vos données ne sont pas partagées à des fins publicitaires.</p>
    </div>
</div>

<h3>5.3 Obligations légales</h3>
<p>Nous pouvons être amenés à divulguer vos données si la loi l'exige ou sur demande d'une autorité judiciaire compétente.</p>

<h2>🌍 6. Transferts internationaux</h2>

<p>Vos données peuvent être transférées et traitées dans des pays différents de votre pays de résidence. Dans ce cas, nous mettons en place des garanties appropriées :</p>
<ul>
    <li>Clauses contractuelles types approuvées</li>
    <li>Sélection de sous-traitants offrant un niveau de protection adéquat</li>
    <li>Chiffrement des données en transit et au repos</li>
</ul>

<h2>🛡️ 7. Sécurité des données</h2>

<p>Nous mettons en œuvre des mesures techniques et organisationnelles robustes pour protéger vos données :</p>
<ul>
    <li><strong>Chiffrement</strong> : TLS 1.3 pour les données en transit, chiffrement AES-256 au repos</li>
    <li><strong>Authentification</strong> : hachage sécurisé des mots de passe (bcrypt), authentification à deux facteurs (2FA) disponible</li>
    <li><strong>Accès</strong> : contrôle d'accès basé sur les rôles (RBAC), principe du moindre privilège</li>
    <li><strong>Monitoring</strong> : surveillance 24/7 des systèmes, détection d'intrusion, alertes en temps réel</li>
    <li><strong>Sauvegardes</strong> : sauvegardes chiffrées quotidiennes avec rétention de 30 jours</li>
    <li><strong>Audit</strong> : journalisation de toutes les actions sensibles, audit trail complet</li>
</ul>

<h2>⏱️ 8. Durée de conservation</h2>

<table>
    <thead>
        <tr>
            <th>Type de données</th>
            <th>Durée de conservation</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Données du compte</td>
            <td>Durée de l'abonnement + 30 jours après résiliation</td>
        </tr>
        <tr>
            <td>Factures et données comptables</td>
            <td>10 ans (obligation légale)</td>
        </tr>
        <tr>
            <td>Données de paiement</td>
            <td>Durée nécessaire au traitement + archives légales</td>
        </tr>
        <tr>
            <td>Logs de connexion</td>
            <td>12 mois</td>
        </tr>
        <tr>
            <td>Données analytics</td>
            <td>26 mois</td>
        </tr>
        <tr>
            <td>Données marketing (consentement)</td>
            <td>Jusqu'au retrait du consentement</td>
        </tr>
    </tbody>
</table>

<h2>✋ 9. Vos droits</h2>

<div class="callout-info mb-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-brand-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        <p class="text-sm text-brand-800" style="margin:0">Vous avez le contrôle total sur vos données. Contactez <a href="mailto:dpo@invoicesaas.com" class="text-brand-700 underline font-semibold">dpo@invoicesaas.com</a> pour exercer vos droits. Réponse sous <strong>30 jours</strong>.</p>
    </div>
</div>

<p>Conformément à la réglementation applicable, vous disposez des droits suivants :</p>
<ul>
    <li><strong>Droit d'accès</strong> : obtenir une copie de vos données personnelles</li>
    <li><strong>Droit de rectification</strong> : corriger des données inexactes ou incomplètes</li>
    <li><strong>Droit à l'effacement</strong> : demander la suppression de vos données (sous réserve des obligations légales de conservation)</li>
    <li><strong>Droit à la portabilité</strong> : recevoir vos données dans un format structuré et couramment utilisé (CSV, PDF)</li>
    <li><strong>Droit d'opposition</strong> : vous opposer au traitement de vos données à des fins de marketing</li>
    <li><strong>Droit à la limitation</strong> : demander la limitation du traitement dans certains cas</li>
    <li><strong>Droit de retirer votre consentement</strong> : à tout moment, sans affecter la licéité du traitement antérieur</li>
</ul>

<h2>👶 10. Protection des données des mineurs</h2>

<p>Le Service n'est pas destiné aux personnes de moins de 18 ans. Nous ne collectons pas sciemment de données personnelles auprès de mineurs. Si nous découvrons qu'un mineur a créé un compte, nous procéderons à la suppression de ses données.</p>

<h2>🔄 11. Modifications de la politique</h2>

<p>Nous pouvons mettre à jour cette Politique de Confidentialité pour refléter des changements dans nos pratiques ou pour des raisons légales. Les modifications significatives seront notifiées par email et/ou par bannière sur la plateforme au moins <strong>15 jours</strong> avant leur entrée en vigueur.</p>

<h2>📧 12. Contact</h2>

<p>Pour toute question ou préoccupation relative à la protection de vos données :</p>
<ul>
    <li><strong>DPO (Délégué à la Protection des Données)</strong> : <a href="mailto:dpo@invoicesaas.com">dpo@invoicesaas.com</a></li>
    <li><strong>Support général</strong> : <a href="mailto:contact@invoicesaas.com">contact@invoicesaas.com</a></li>
    <li><strong>Adresse</strong> : InvoiceSaaS SAS, Dakar, Sénégal</li>
</ul>

<p>Vous avez également le droit d'introduire une réclamation auprès de l'autorité de protection des données compétente (CDP – Commission des Données Personnelles du Sénégal).</p>

@endsection
