@extends('legal.layout')

@section('title', 'Mentions Légales')
@section('meta_description', "Mentions légales d'InvoiceSaaS. Informations sur l'éditeur, l'hébergeur et les droits de propriété intellectuelle.")
@section('breadcrumb', 'Mentions Légales')
@section('heading', 'Mentions Légales')
@section('updated_date', '19/02/2026')

@section('hero_icon')
<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
@endsection

@section('content')

<div class="callout-info mb-8">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-brand-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        <div>
            <p class="text-sm text-brand-900 font-semibold mb-1" style="margin-top:0">Informations légales obligatoires</p>
            <p class="text-sm text-brand-700/80" style="margin-bottom:0">Conformément à la réglementation, cette page regroupe les informations sur l'éditeur, l'hébergeur et les droits de propriété intellectuelle du site InvoiceSaaS.</p>
        </div>
    </div>
</div>

<p>Conformément aux dispositions légales, nous vous informons des mentions légales relatives au site et à la plateforme <strong>InvoiceSaaS</strong>.</p>

<h2>🏢 1. Éditeur du site</h2>

<table>
    <tbody>
        <tr>
            <td><strong>Raison sociale</strong></td>
            <td>InvoiceSaaS SAS</td>
        </tr>
        <tr>
            <td><strong>Forme juridique</strong></td>
            <td>Société par Actions Simplifiée (SAS)</td>
        </tr>
        <tr>
            <td><strong>Siège social</strong></td>
            <td>Dakar, Sénégal</td>
        </tr>
        <tr>
            <td><strong>NINEA</strong></td>
            <td>[En cours d'immatriculation]</td>
        </tr>
        <tr>
            <td><strong>Capital social</strong></td>
            <td>1 000 000 FCFA</td>
        </tr>
        <tr>
            <td><strong>Directeur de la publication</strong></td>
            <td>[Nom du dirigeant]</td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td><a href="mailto:contact@invoicesaas.com">contact@invoicesaas.com</a></td>
        </tr>
        <tr>
            <td><strong>Téléphone</strong></td>
            <td>+221 XX XXX XX XX</td>
        </tr>
    </tbody>
</table>

<h2>🖥️ 2. Hébergeur</h2>

<table>
    <tbody>
        <tr>
            <td><strong>Raison sociale</strong></td>
            <td>[Nom de l'hébergeur]</td>
        </tr>
        <tr>
            <td><strong>Adresse</strong></td>
            <td>[Adresse de l'hébergeur]</td>
        </tr>
        <tr>
            <td><strong>Téléphone</strong></td>
            <td>[Téléphone de l'hébergeur]</td>
        </tr>
        <tr>
            <td><strong>Site web</strong></td>
            <td>[URL de l'hébergeur]</td>
        </tr>
    </tbody>
</table>

<h2>©️ 3. Propriété intellectuelle</h2>

<h3>3.1 Marques et logos</h3>
<p>Le nom <strong>InvoiceSaaS</strong>, le logo et l'ensemble des éléments graphiques de la plateforme sont la propriété exclusive d'InvoiceSaaS SAS. Toute reproduction, représentation, modification, publication ou adaptation de tout ou partie de ces éléments, quel que soit le moyen ou le procédé utilisé, est interdite sans l'autorisation écrite préalable d'InvoiceSaaS SAS.</p>

<h3>3.2 Contenu du site</h3>
<p>L'ensemble des textes, images, photographies, vidéos, sons et animations présents sur le site sont protégés par le droit d'auteur. Toute utilisation non autorisée constitue une contrefaçon sanctionnée par le Code de la Propriété Intellectuelle.</p>

<h3>3.3 Code source</h3>
<p>Le code source de la plateforme InvoiceSaaS est la propriété exclusive d'InvoiceSaaS SAS. Toute tentative de décompilation, désassemblage ou reverse engineering est strictement interdite.</p>

<h2>🔒 4. Données personnelles</h2>

<p>InvoiceSaaS SAS collecte et traite des données personnelles dans le cadre de la fourniture de ses services. Pour plus d'informations sur le traitement de vos données, veuillez consulter notre <a href="/politique-confidentialite">Politique de Confidentialité</a>.</p>

<div class="callout-success mb-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        <p class="text-sm text-emerald-800" style="margin:0">Conformément à la loi sénégalaise n°2008-12 du 25 janvier 2008, vous disposez de droits d'accès, de rectification, de suppression et d'opposition. Contact : <a href="mailto:dpo@invoicesaas.com" class="text-emerald-700 underline font-semibold">dpo@invoicesaas.com</a></p>
    </div>
</div>

<h2>🍪 5. Cookies</h2>

<p>Le site utilise des cookies pour assurer son bon fonctionnement et améliorer l'expérience utilisateur. Pour plus de détails, consultez la section dédiée dans notre <a href="/politique-confidentialite">Politique de Confidentialité</a>.</p>

<h2>⚠️ 6. Limitation de responsabilité</h2>

<h3>6.1 Contenu du site</h3>
<p>InvoiceSaaS SAS s'efforce de fournir des informations aussi précises que possible sur le site. Toutefois, elle ne pourra être tenue responsable des omissions, des inexactitudes et des carences dans la mise à jour, qu'elles soient de son fait ou du fait des tiers partenaires qui lui fournissent ces informations.</p>

<h3>6.2 Disponibilité</h3>
<p>InvoiceSaaS SAS s'efforce de maintenir le site et la plateforme accessibles en permanence. Cependant, la responsabilité d'InvoiceSaaS SAS ne saurait être engagée en cas d'impossibilité d'accès au site ou à la plateforme du fait de circonstances extérieures (maintenance, panne technique, force majeure, etc.).</p>

<h3>6.3 Liens hypertextes</h3>
<p>Le site peut contenir des liens vers d'autres sites. InvoiceSaaS SAS n'exerce aucun contrôle sur le contenu de ces sites tiers et décline toute responsabilité quant à leur contenu.</p>

<h2>📋 7. Conformité fiscale</h2>

<div class="callout-warning mb-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        <p class="text-sm text-amber-800" style="margin:0"><strong>Important</strong> : InvoiceSaaS est un outil de création de factures. L'Utilisateur reste seul responsable de la conformité fiscale de ses factures.</p>
    </div>
</div>

<p>L'Utilisateur reste seul responsable :</p>
<ul>
    <li>De la conformité de ses factures avec la législation fiscale applicable dans son pays</li>
    <li>De la déclaration et du paiement de ses impôts et taxes (TVA, impôt sur le revenu, etc.)</li>
    <li>De l'exactitude des informations figurant sur ses factures</li>
    <li>Du respect des obligations comptables qui lui incombent</li>
</ul>
<p>InvoiceSaaS SAS ne fournit pas de conseil fiscal ou juridique et ne saurait être tenu responsable d'un manquement de l'Utilisateur à ses obligations fiscales.</p>

<h2>🏛️ 8. Droit applicable</h2>

<p>Les présentes mentions légales sont régies par le droit sénégalais. En cas de litige, et après tentative de résolution amiable, compétence est attribuée aux tribunaux de <strong>Dakar, Sénégal</strong>.</p>

<h2>🎨 9. Crédits</h2>

<table>
    <tbody>
        <tr>
            <td><strong>Conception & développement</strong></td>
            <td>Équipe InvoiceSaaS</td>
        </tr>
        <tr>
            <td><strong>Design</strong></td>
            <td>InvoiceSaaS Design Team</td>
        </tr>
        <tr>
            <td><strong>Icônes</strong></td>
            <td>Heroicons, Lucide Icons</td>
        </tr>
        <tr>
            <td><strong>Typographie</strong></td>
            <td>Inter (Google Fonts)</td>
        </tr>
        <tr>
            <td><strong>Framework CSS</strong></td>
            <td>Tailwind CSS</td>
        </tr>
    </tbody>
</table>

<h2>📧 10. Contact</h2>

<p>Pour toute question relative aux présentes mentions légales :</p>
<ul>
    <li><strong>Email</strong> : <a href="mailto:contact@invoicesaas.com">contact@invoicesaas.com</a></li>
    <li><strong>Adresse</strong> : InvoiceSaaS SAS, Dakar, Sénégal</li>
</ul>

@endsection
