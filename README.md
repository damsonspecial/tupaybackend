# Tupay Remittance Backend

A production-grade, secure, and high-performance backend for cross-border remittance between Nigeria and China.

## Architecture

### Pattern Mastery
- **Repository Pattern**: Data access is decoupled via Interfaces in `app/Interfaces` and implemented in `app/Repositories`.
- **Action Classes**: Core business logic is encapsulated in single-purpose classes (e.g., `SwapCurrencyAction`).
- **Type Safety (PHP Enums)**: All critical states (Transaction types, Currency codes, Wallet status, Gender) are enforced via PHP Enums to eliminate magic strings.
- **High-Precision Math**: Powered by **BCMath** to ensure zero rounding errors during currency conversion.

## Concurrency & Performance
1. **Redis Atomic Locks**: Prevents race conditions during financial operations.
2. **Database Transactions**: Ensures "all-or-nothing" execution for ledger updates.
3. **Redis Caching**: Exchange rates are cached with a short TTL to optimize performance under high load.

## Security Measures
- **2FA (TOTP)**: Sensitive actions are protected by a `2fa.verified` middleware. 
- **Token-Isolated 2FA**: Verification is strictly tied to the specific Sanctum access token, preventing session hijacking.
- **Stateless Auth**: Powered by Laravel Sanctum.
- **Webhook Protection**: Signature verification for incoming settlement notifications.

## Core Features
- **Registration & Wallets**: Automatic provisioning of NGN and CNY wallets upon registration.
- **Currency Swap**: Secure, atomic exchange between supported currencies.
- **Dashboard & Profile**: Consolidated financial overview and secure profile management.
- **Transaction Ledger**: Complete, immutable history for every wallet.

## Webhook Security (Handshake)
Incoming settlement webhooks from third-party payment providers must include an `X-Signature` header.
- **Algorithm**: HMAC-SHA256
- **Secret**: Defined as `SETTLEMENT_WEBHOOK_SECRET` in `.env` (mapped via `config/services.php`).
- **Payload**: The raw JSON body of the request.

**Example (PHP)**:
```php
$secret = 'your-settlement-secret';
$payload = json_encode($data);
$signature = hash_hmac('sha256', $payload, $secret);
```

## API Setup
1. **Initialize Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
2. **Configure Keys**: Open `.env` and set your `SETTLEMENT_WEBHOOK_SECRET`.
3. **Install Dependencies**: `composer install`
4. **Database Setup**: `php artisan migrate --seed`
5. **Documentation**: Refer to the `tupay_api_collection.json` for Postman/Insomnia documentation.

**Pro-tip**: You can also use `composer setup` to automate the basic initialization.

**Test User**: `test@example.com` / `password`
*Note: The 2FA secret is dynamically generated during seeding for maximum security.*
