# 💳 Moyens de Paiement Africains - Guide de Configuration

## 📋 Vue d'ensemble

Cette application supporte **15+ moyens de paiement** populaires en Afrique, permettant à vos clients de payer leurs factures avec leur méthode préférée.

---

## 🌍 Moyens de Paiement Disponibles

### 1️⃣ **Stripe** (International + Afrique)
- **Pays**: Disponible partout
- **Devises**: EUR, USD, GBP, NGN, GHS, KES, ZAR, etc.
- **Configuration**:
  ```env
  STRIPE_ENABLED=true
  STRIPE_KEY=pk_test_votre_cle_publique
  STRIPE_SECRET=sk_test_votre_cle_secrete
  STRIPE_WEBHOOK_SECRET=whsec_votre_webhook_secret
  ```
- **Documentation**: https://stripe.com/docs

---

### 2️⃣ **Paystack** 🇳🇬 (Nigeria, Ghana, Afrique du Sud, Kenya)
- **Pays**: NG, GH, ZA, KE
- **Devises**: NGN, GHS, ZAR, KES, USD
- **Mobile Money**: Oui (MTN, Airtel, Vodafone, etc.)
- **Configuration**:
  ```env
  PAYSTACK_ENABLED=true
  PAYSTACK_PUBLIC_KEY=pk_test_votre_cle_publique
  PAYSTACK_SECRET_KEY=sk_test_votre_cle_secrete
  PAYSTACK_WEBHOOK_SECRET=votre_webhook_secret
  ```
- **Inscription**: https://paystack.com
- **Test Cards**: 
  - Success: 4084084084084081
  - Insufficient funds: 408408408408 4082

---

### 3️⃣ **Flutterwave** 🦋 (Pan-Africain)
- **Pays**: 34+ pays africains
- **Devises**: NGN, GHS, KES, UGX, TZS, ZAR, XOF, XAF, etc.
- **Mobile Money**: MTN, Airtel, Vodafone, Orange, M-Pesa, etc.
- **Configuration**:
  ```env
  FLUTTERWAVE_ENABLED=true
  FLUTTERWAVE_PUBLIC_KEY=FLWPUBK_TEST-votre_cle_publique
  FLUTTERWAVE_SECRET_KEY=FLWSECK_TEST-votre_cle_secrete
  FLUTTERWAVE_ENCRYPTION_KEY=FLWSECK_TEST-votre_cle_encryption
  FLUTTERWAVE_WEBHOOK_SECRET=votre_webhook_secret
  ```
- **Inscription**: https://flutterwave.com
- **Documentation**: https://developer.flutterwave.com

---

### 4️⃣ **Wave** 🌊 (Afrique de l'Ouest)
- **Pays**: Sénégal, Côte d'Ivoire, Mali, Burkina Faso, Bénin, Togo
- **Devise**: XOF (Franc CFA)
- **Type**: Mobile Money
- **Configuration**:
  ```env
  WAVE_ENABLED=true
  WAVE_API_KEY=wave_sn_test_votre_api_key
  WAVE_SECRET_KEY=votre_wave_secret_key
  WAVE_WEBHOOK_SECRET=votre_wave_webhook_secret
  ```
- **Inscription**: https://wave.com/en/business/
- **Avantages**: Sans frais pour les clients, très populaire au Sénégal

---

### 5️⃣ **Orange Money** 🍊 (Afrique Francophone)
- **Pays**: SN, CI, ML, BF, NE, GN, CM, CD, MG, CF
- **Devise**: XOF, XAF
- **Configuration**:
  ```env
  ORANGE_MONEY_ENABLED=true
  ORANGE_MONEY_MERCHANT_KEY=votre_merchant_key
  ORANGE_MONEY_API_USER=votre_api_user
  ORANGE_MONEY_API_PASSWORD=votre_api_password
  ```
- **Inscription**: Contactez Orange Business Services

---

### 6️⃣ **MTN Mobile Money** 📱
- **Pays**: GH, UG, RW, ZM, CI, CM, BJ, CD, CG, SZ
- **Devises**: Selon le pays
- **Configuration**:
  ```env
  MTN_MOMO_ENABLED=true
  MTN_MOMO_API_KEY=votre_mtn_api_key
  MTN_MOMO_USER_ID=votre_user_id
  MTN_MOMO_SUBSCRIPTION_KEY=votre_subscription_key
  ```
- **Documentation**: https://momodeveloper.mtn.com

---

### 7️⃣ **M-Pesa** 💚 (Afrique de l'Est)
- **Pays**: Kenya, Tanzania, Mozambique, Lesotho
- **Leader**: Kenya (80% de part de marché)
- **Configuration**:
  ```env
  MPESA_ENABLED=true
  MPESA_CONSUMER_KEY=votre_consumer_key
  MPESA_CONSUMER_SECRET=votre_consumer_secret
  MPESA_SHORTCODE=votre_shortcode
  MPESA_PASSKEY=votre_passkey
  ```
- **Documentation**: https://developer.safaricom.co.ke

---

### 8️⃣ **FedaPay** 💰 (Afrique de l'Ouest)
- **Pays**: Bénin, Togo, Côte d'Ivoire, Sénégal
- **Devises**: XOF, EUR
- **Mobile Money**: MTN, Moov, Orange
- **Configuration**:
  ```env
  FEDAPAY_ENABLED=true
  FEDAPAY_PUBLIC_KEY=pk_sandbox_votre_cle_publique
  FEDAPAY_SECRET_KEY=sk_sandbox_votre_cle_secrete
  FEDAPAY_WEBHOOK_SECRET=votre_fedapay_webhook_secret
  ```
- **Inscription**: https://fedapay.com
- **Avantages**: Facile à intégrer, support francophone

---

### 9️⃣ **Kkiapay** 🔐 (Afrique de l'Ouest)
- **Pays**: Bénin, Togo, Côte d'Ivoire, Sénégal, Burkina Faso
- **Devise**: XOF
- **Mobile Money**: MTN, Moov, Orange, Flooz
- **Configuration**:
  ```env
  KKIAPAY_ENABLED=true
  KKIAPAY_PUBLIC_KEY=votre_public_key
  KKIAPAY_PRIVATE_KEY=votre_private_key
  KKIAPAY_SECRET=votre_kkiapay_secret
  ```
- **Inscription**: https://kkiapay.me
- **Test**: Utilisez le numéro `97000000` en sandbox

---

### 🔟 **CinetPay** 🎬 (Côte d'Ivoire + Afrique de l'Ouest)
- **Pays**: CI, SN, BJ, BF, TG, ML, NE, GN, CM
- **Devises**: XOF, XAF
- **Mobile Money**: Orange, MTN, Moov, Wave
- **Configuration**:
  ```env
  CINETPAY_ENABLED=true
  CINETPAY_API_KEY=votre_cinetpay_api_key
  CINETPAY_SITE_ID=votre_site_id
  ```
- **Inscription**: https://cinetpay.com
- **Avantages**: Multi-pays, interface en français

---

## 🚀 Installation et Configuration

### Étape 1: Installer les dépendances
Les packages sont déjà installés. Si vous ajoutez un nouveau gateway, installez son SDK si nécessaire.

### Étape 2: Configurer les clés API
1. Inscrivez-vous sur la plateforme de votre choix
2. Obtenez vos clés API (test/sandbox d'abord)
3. Ajoutez les clés dans votre fichier `.env`
4. Activez le gateway: `GATEWAY_ENABLED=true`

### Étape 3: Définir le gateway par défaut
```env
PAYMENT_GATEWAY=paystack  # ou stripe, flutterwave, wave, etc.
```

### Étape 4: Configurer les webhooks
Chaque gateway nécessite une URL de webhook pour les notifications de paiement:

```
Stripe:       https://votre-domaine.com/stripe/webhook
Paystack:     https://votre-domaine.com/webhooks/paystack
Flutterwave:  https://votre-domaine.com/webhooks/flutterwave
Wave:         https://votre-domaine.com/webhooks/wave
M-Pesa:       https://votre-domaine.com/webhooks/mpesa
FedaPay:      https://votre-domaine.com/webhooks/fedapay
Kkiapay:      https://votre-domaine.com/webhooks/kkiapay
CinetPay:     https://votre-domaine.com/webhooks/cinetpay
```

---

## 🧪 Tests en Mode Sandbox

### Paystack Test
```bash
# Cartes de test
Success: 4084084084084081
Decline: 408408408408 4082
```

### Flutterwave Test
```bash
# Numéros de test
Card: 5531886652142950
CVV: 564
Expiry: 09/32
OTP: 12345
```

### Wave Test
```bash
# Utilisez les credentials de test fournis par Wave
```

### Kkiapay Test
```bash
# Numéro de téléphone test: 97000000
```

---

## 📊 Tableau de Comparaison

| Gateway | Pays | Frais (env.) | Mobile Money | Cartes | Setup |
|---------|------|--------------|--------------|--------|-------|
| **Stripe** | Global | 2.9% + 0.30 | Non | Oui | Facile |
| **Paystack** | NG, GH, ZA, KE | 1.5% + 100 | Oui | Oui | Facile |
| **Flutterwave** | 34+ pays | 1.4% - 3.8% | Oui | Oui | Facile |
| **Wave** | Afr. Ouest | 1% | Oui | Non | Moyen |
| **Orange Money** | Afr. Franco | ~2% | Oui | Non | Difficile |
| **MTN MoMo** | 10+ pays | 1-3% | Oui | Non | Moyen |
| **M-Pesa** | KE, TZ, MZ | 1-2% | Oui | Non | Difficile |
| **FedaPay** | Afr. Ouest | 2.5% + 50 | Oui | Oui | Facile |
| **Kkiapay** | Afr. Ouest | 2% | Oui | Non | Facile |
| **CinetPay** | Afr. Ouest | 2.5% | Oui | Oui | Facile |

---

## 💡 Recommandations par Région

### 🇸🇳 **Sénégal**
1. **Wave** (le plus populaire, sans frais client)
2. **Orange Money** (couverture nationale)
3. **FedaPay** ou **Kkiapay** (agrégateurs)

### 🇨🇮 **Côte d'Ivoire**
1. **CinetPay** (local, multi-opérateurs)
2. **Orange Money** (leader)
3. **FedaPay** (facile)

### 🇳🇬 **Nigeria**
1. **Paystack** (leader local)
2. **Flutterwave** (plus d'options)
3. **Stripe** (international)

### 🇰🇪 **Kenya**
1. **M-Pesa** (incontournable - 80% du marché)
2. **Flutterwave** (backup)
3. **Paystack** (cartes)

### 🇬🇭 **Ghana**
1. **Paystack** (leader)
2. **Flutterwave** (alternative)
3. **MTN MoMo** (mobile)

### 🇧🇯 **Bénin / Togo**
1. **Kkiapay** (très populaire)
2. **FedaPay** (bonne alternative)
3. **MTN/Moov Money** (directs)

---

## 🔒 Sécurité des Webhooks

Pour chaque gateway, les webhooks sont protégés:

```php
// Exemple: Vérification signature Paystack
protected function verifyPaystackSignature($body, $signature)
{
    $hash = hash_hmac('sha512', $body, config('payments.gateways.paystack.webhook_secret'));
    return hash_equals($hash, $signature);
}
```

**Important**: Ajoutez les URLs webhook dans votre middleware `VerifyCsrfToken`:

```php
protected $except = [
    'stripe/webhook',
    'webhooks/*',
];
```

---

## 🎯 Usage dans le Code

### Initialiser un paiement
```php
use App\Services\PaymentGatewayService;

$gateway = new PaymentGatewayService('paystack'); // ou 'wave', 'mpesa', etc.

$paymentData = $gateway->createPayment($invoice, [
    'email' => $customer->email,
    'name' => $customer->name,
    'phone' => $customer->phone,
]);

// $paymentData contient: reference, payment_url, etc.
```

### Vérifier un paiement
```php
$result = $gateway->verifyPayment($reference);

if ($result['status'] === 'success') {
    // Marquer la facture comme payée
}
```

### Obtenir les gateways disponibles pour un pays
```php
$gateways = PaymentGatewayService::getAvailableGateways('SN'); // Sénégal
// Retourne: ['wave', 'orange_money', 'fedapay', 'kkiapay', 'stripe']
```

---

## 📞 Support

Pour chaque gateway, référez-vous à leur documentation officielle:

- **Paystack**: support@paystack.com
- **Flutterwave**: developers@flutterwavego.com
- **Wave**: https://www.wave.com/en/contact
- **FedaPay**: support@fedapay.com
- **Kkiapay**: contact@kkiapay.me
- **CinetPay**: contact@cinetpay.com

---

## 🎉 Prochaines Étapes

1. ✅ Choisir vos gateways selon votre zone géographique
2. ✅ S'inscrire et obtenir les clés API
3. ✅ Tester en mode sandbox
4. ✅ Configurer les webhooks
5. ✅ Passer en production
6. ✅ Monitorer les transactions

**Bon courage avec vos paiements africains ! 🚀🌍**
