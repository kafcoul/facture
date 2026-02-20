<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Invoice SaaS API",
 *     description="API REST pour la plateforme de facturation multi-tenant Invoice SaaS. Authentification via Laravel Sanctum (Bearer Token).",
 *     @OA\Contact(
 *         email="support@invoice-saas.com",
 *         name="Invoice SaaS Support"
 *     ),
 *     @OA\License(
 *         name="Propriétaire",
 *         url="https://invoice-saas.com/terms"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="Serveur API principal"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum",
 *     description="Utilisez le token obtenu via POST /v1/auth/login. Format: Bearer {token}"
 * )
 *
 * @OA\Tag(
 *     name="Health",
 *     description="Endpoints de monitoring et vérification de santé"
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="Inscription, connexion, gestion de tokens Sanctum"
 * )
 * @OA\Tag(
 *     name="Invoices",
 *     description="Création, consultation et génération PDF de factures"
 * )
 * @OA\Tag(
 *     name="Payments",
 *     description="Initiation et confirmation de paiements (Orange Money, MTN, Stripe…)"
 * )
 */
class SwaggerInfo
{
    // This class exists solely to hold OpenAPI base annotations.
    // It is scanned by l5-swagger to generate the api-docs.json file.
}
