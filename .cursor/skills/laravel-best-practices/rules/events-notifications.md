# Events & Notifications Best Practices

## Rely on Event Discovery

Laravel auto-discovers listeners by reading `handle(EventType $event)` type-hints. No manual registration needed in `AppServiceProvider`.

## Run `event:cache` in Production Deploy

Event discovery scans the filesystem per-request in dev. Cache it in production: `php artisan optimize` or `php artisan event:cache`.

## Use `ShouldDispatchAfterCommit` Inside Transactions

Without it, a queued listener may process before the DB transaction commits, reading data that doesn't exist yet.

```php
class OrderShipped implements ShouldDispatchAfterCommit {}
```

## Always Queue Notifications

Notifications often hit external APIs (email, SMS, Slack). Without `ShouldQueue`, they block the HTTP response.

```php
class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;
}
```

## Use `afterCommit()` on Notifications in Transactions

Same race condition as events — call `afterCommit()` to delay dispatch until the transaction commits.

```php
$user->notify((new InvoicePaid($invoice))->afterCommit());
```

## Route Notification Channels to Dedicated Queues

Mail and database notifications have different priorities. Use `viaQueues()` to route them to separate queues.

## Use On-Demand Notifications for Non-User Recipients

Avoid creating dummy models to send notifications to arbitrary addresses.

```php
Notification::route('mail', 'admin@example.com')->notify(new SystemAlert());
```

## Event-Driven Side Effects

Always use events and listeners for side effects that are not part of the primary transaction (e.g., sending emails, updating search indexes, logging analytics).

- **Action**: Handles the primary business intent (e.g., saving a record).
- **Event**: Dispatched when the primary intent is completed.
- **Listener**: Handles the side effects (e.g., sending a welcome email).

This keeps your Action classes focused and improves performance by offloading non-critical work to queues.

```php
// Inside an Action
public function execute(array $data): User
{
    $user = $this->repository->create($data);

    UserRegistered::dispatch($user);

    return $user;
}
```

## Implement `HasLocalePreference` on Notifiable Models

Laravel automatically uses the user's preferred locale for all notifications and mailables — no per-call `locale()` needed.