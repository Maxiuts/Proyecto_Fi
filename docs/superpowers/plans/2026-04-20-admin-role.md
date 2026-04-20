# Admin Role Assignment — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Asignar automáticamente el rol `admin` a un correo específico en cada inicio de sesión (login normal y OAuth), y `user` a todos los demás.

**Architecture:** El correo admin se define en `.env` como `ADMIN_EMAIL` y se expone vía `config('app.admin_email')`. En ambos controladores de login se compara el email del usuario autenticado y se actualiza su `role` antes de redirigir. La vista `shop.blade.php` ya muestra el enlace "Administrar" condicionalmente con `isAdmin()`.

**Tech Stack:** Laravel 11, Pest PHP, Laravel Socialite

---

## Archivos a modificar

| Archivo | Acción |
|---|---|
| `.env` | Agregar `ADMIN_EMAIL` |
| `config/app.php` | Exponer `admin_email` desde env |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Asignar rol post-login |
| `app/Http/Controllers/Auth/SocialiteController.php` | Asignar rol post-OAuth |
| `tests/Feature/Auth/AuthenticationTest.php` | Corregir redirect y agregar test de rol |

---

### Task 1: Agregar `ADMIN_EMAIL` a configuración

**Files:**
- Modify: `.env`
- Modify: `config/app.php`

- [ ] **Step 1: Agregar variable al `.env`**

Abrir `.env` y agregar al final:
```
ADMIN_EMAIL=tu_correo@ejemplo.com
```
(Reemplaza con el correo real del admin.)

- [ ] **Step 2: Exponer en `config/app.php`**

En `config/app.php`, agregar después de la línea `'name' => env('APP_NAME', 'Laravel'),`:
```php
'admin_email' => env('ADMIN_EMAIL', ''),
```

- [ ] **Step 3: Commit**

```bash
git add .env config/app.php
git commit -m "config: add ADMIN_EMAIL env variable"
```

---

### Task 2: Asignar rol en login normal

**Files:**
- Modify: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- Test: `tests/Feature/Auth/AuthenticationTest.php`

- [ ] **Step 1: Escribir el test que falla**

En `tests/Feature/Auth/AuthenticationTest.php`, reemplazar el test `'users can authenticate using the login screen'` con:

```php
test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('shop', absolute: false));
});

test('admin email gets admin role on login', function () {
    config(['app.admin_email' => 'admin@test.com']);

    $user = User::factory()->create(['email' => 'admin@test.com', 'role' => 'user']);

    $this->post('/login', [
        'email' => 'admin@test.com',
        'password' => 'password',
    ]);

    expect($user->fresh()->role)->toBe('admin');
});

test('non-admin email gets user role on login', function () {
    config(['app.admin_email' => 'admin@test.com']);

    $user = User::factory()->create(['email' => 'otro@test.com', 'role' => 'admin']);

    $this->post('/login', [
        'email' => 'otro@test.com',
        'password' => 'password',
    ]);

    expect($user->fresh()->role)->toBe('user');
});
```

- [ ] **Step 2: Correr los tests para verificar que fallan**

```bash
php artisan test tests/Feature/Auth/AuthenticationTest.php
```

Esperado: FAIL en los 2 tests nuevos (rol no se asigna aún) y en el test de redirect (apunta a dashboard).

- [ ] **Step 3: Implementar la asignación de rol en `AuthenticatedSessionController`**

Reemplazar el método `store` en `app/Http/Controllers/Auth/AuthenticatedSessionController.php`:

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = Auth::user();
    $user->update([
        'role' => $user->email === config('app.admin_email') ? 'admin' : 'user',
    ]);

    return redirect()->intended(route('shop', absolute: false));
}
```

- [ ] **Step 4: Correr los tests para verificar que pasan**

```bash
php artisan test tests/Feature/Auth/AuthenticationTest.php
```

Esperado: PASS en todos los tests.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Auth/AuthenticatedSessionController.php tests/Feature/Auth/AuthenticationTest.php
git commit -m "feat: assign role on normal login based on ADMIN_EMAIL"
```

---

### Task 3: Asignar rol en login OAuth (Socialite)

**Files:**
- Modify: `app/Http/Controllers/Auth/SocialiteController.php`
- Test: `tests/Feature/Auth/AuthenticationTest.php`

- [ ] **Step 1: Escribir el test que falla**

Agregar al final de `tests/Feature/Auth/AuthenticationTest.php`:

```php
test('admin email gets admin role on oauth callback', function () {
    config(['app.admin_email' => 'admin@test.com']);

    $user = User::factory()->create([
        'email' => 'admin@test.com',
        'role' => 'user',
        'email_verified_at' => now(),
    ]);

    // Simular que el SocialiteController ya procesó el callback
    // actuando directamente sobre el controlador
    $socialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);
    $socialUser->shouldReceive('getEmail')->andReturn('admin@test.com');
    $socialUser->shouldReceive('getName')->andReturn('Admin');
    $socialUser->shouldReceive('getId')->andReturn('123');
    $socialUser->shouldReceive('getAvatar')->andReturn(null);

    \Laravel\Socialite\Facades\Socialite::shouldReceive('driver->user')
        ->andReturn($socialUser);

    $this->get('/auth/google/callback');

    expect($user->fresh()->role)->toBe('admin');
});
```

- [ ] **Step 2: Correr el test para verificar que falla**

```bash
php artisan test tests/Feature/Auth/AuthenticationTest.php --filter="admin email gets admin role on oauth callback"
```

Esperado: FAIL — rol no se asigna en callback OAuth.

- [ ] **Step 3: Implementar la asignación de rol en `SocialiteController`**

Reemplazar el método `callback` en `app/Http/Controllers/Auth/SocialiteController.php`:

```php
public function callback(string $provider)
{
    try {
        $socialUser = Socialite::driver($provider)->user();
    } catch (\Exception $e) {
        return redirect()->route('login')
            ->with('error', 'No se pudo autenticar con ' . $provider . '. Intenta de nuevo.');
    }

    $user = User::updateOrCreate(
        ['email' => $socialUser->getEmail()],
        [
            'name'              => $socialUser->getName(),
            'provider'          => $provider,
            'provider_id'       => $socialUser->getId(),
            'avatar'            => $socialUser->getAvatar(),
            'email_verified_at' => now(),
            'password'          => null,
        ]
    );

    $user->update([
        'role' => $user->email === config('app.admin_email') ? 'admin' : 'user',
    ]);

    Auth::login($user, remember: true);

    return redirect()->intended(route('shop'));
}
```

- [ ] **Step 4: Correr todos los tests para verificar que pasan**

```bash
php artisan test tests/Feature/Auth/AuthenticationTest.php
```

Esperado: PASS en todos los tests.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Auth/SocialiteController.php tests/Feature/Auth/AuthenticationTest.php
git commit -m "feat: assign role on OAuth callback based on ADMIN_EMAIL"
```

---

### Task 4: Verificación final

- [ ] **Step 1: Correr toda la suite de tests**

```bash
php artisan test
```

Esperado: todos los tests pasan, sin regresiones.

- [ ] **Step 2: Verificar la migración está aplicada**

```bash
php artisan migrate:status
```

Verificar que `2026_04_15_201646_add_role_to_users_table` aparece como `Ran`.

- [ ] **Step 3: Commit final**

```bash
git add .
git commit -m "chore: verify admin role implementation complete"
```
