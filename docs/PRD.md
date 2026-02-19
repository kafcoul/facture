# PRD - Invoice SaaS (Laravel + Filament) - MVP
**Projet:** InvoiceSaaS  
**Stack:** Laravel 10+, Filament, Redis (queues), S3 (storage)  
**Objectif:** MVP pour générer factures/devis PDF, envoyer par email, et encaisser paiements.

## Users & Roles
- User (owner) — s'inscrit, gère clients, produits, factures.
- (Optional) Admin Filament (superuser)

## MVP Features
1. Auth (register/login)
2. CRUD Clients, Products
3. Création facture / devis (items dynamiques)
4. Génération PDF (dompdf) + stockage public
5. Envoi par email (job)
6. Paiement via Stripe (checkout + webhook)
7. Dashboard Filament (CA, impayés)
8. Export CSV

## Non-fonctionnel
- Multi-tenant par `user_id` (global scopes)
- Queue pour génération PDF & email (Redis + Horizon)
- Tests unitaires pour calculs & webhooks

## Deliverables
- Code Laravel (migrations, models, controllers, services)
- Filament Resources (Clients, Products, Invoices)
- Scripts d'installation (env example)
