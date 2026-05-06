# Repository Interface Pattern

Decouple data access from business logic using interfaces. This ensures the application is not tightly coupled to Eloquent and makes testing easier by swapping implementations.

## Use Interfaces for Repositories

Define an interface for every repository. This interface should be the type-hint used in Action classes or Controllers.

```php
namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function find(int $id): ?User;
    public function create(array $data): User;
}
```

## Implementation

The Eloquent implementation should reside in the `Eloquent` sub-namespace.

```php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }
}
```

## Binding in Service Provider

Bind the interface to the implementation in a Service Provider (e.g., `RepositoryServiceProvider`).

```php
public function register(): void
{
    $this->app->bind(
        \App\Repositories\Contracts\UserRepositoryInterface::class,
        \App\Repositories\Eloquent\EloquentUserRepository::class
    );
}
```

## Usage in Action Classes

Always inject the interface, never the concrete implementation.

```php
class RegisterUserAction
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(array $data): User
    {
        return $this->repository->create($data);
    }
}
```
