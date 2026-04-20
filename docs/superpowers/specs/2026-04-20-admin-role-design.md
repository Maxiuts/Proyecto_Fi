# Admin Role Assignment — Design Spec

**Date:** 2026-04-20

## Objetivo

Que un correo electrónico específico reciba automáticamente el rol `admin` en cada inicio de sesión (login normal y OAuth), y que en la vista `/shop` aparezca un apartado visible solo para admins con acceso al dashboard.

---

## Infraestructura existente (no tocar)

- Migración `add_role_to_users_table`: columna `role` con default `'user'` ✓
- `User::isAdmin()`: retorna `$this->role === 'admin'` ✓
- Middleware `EnsureAdmin`: aborta con 403 si no es admin ✓
- Rutas admin (`/dashboard`, CRUD productos) protegidas con `EnsureAdmin` ✓

---

## Cambios requeridos

### 1. Configuración — `.env` y `config/app.php`

Agregar en `.env`:
```
ADMIN_EMAIL=correo_del_admin@ejemplo.com
```

Agregar en `config/app.php`:
```php
'admin_email' => env('ADMIN_EMAIL', ''),
```

### 2. Lógica de asignación de rol en login

En ambos controladores, después de autenticar al usuario:

```php
$role = $user->email === config('app.admin_email') ? 'admin' : 'user';
$user->update(['role' => $role]);
```

**`AuthenticatedSessionController::store`** — después de `$request->authenticate()`:
```php
$user = Auth::user();
$user->update(['role' => $user->email === config('app.admin_email') ? 'admin' : 'user']);
return redirect()->intended(route('shop', absolute: false));
```

**`SocialiteController::callback`** — después de `updateOrCreate`:
```php
$user->update(['role' => $user->email === config('app.admin_email') ? 'admin' : 'user']);
Auth::login($user, remember: true);
return redirect()->intended(route('shop'));
```

### 3. Vista `shop.blade.php`

Agregar sección visible solo para admins:

```blade
@if(auth()->user()->isAdmin())
    {{-- Botón/enlace al dashboard --}}
    <a href="{{ route('dashboard') }}">Ir al Dashboard</a>
@endif
```

El redirect después del login es `route('shop')` para todos los usuarios (ya es el comportamiento actual).

---

## Flujo completo

1. Usuario inicia sesión (normal o OAuth)
2. Sistema compara `$user->email` con `config('app.admin_email')`
3. Actualiza `role` a `'admin'` o `'user'` según corresponda
4. Redirige a `/shop`
5. En `/shop`, el bloque `@if(auth()->user()->isAdmin())` muestra el acceso al dashboard solo si es admin

---

## Lo que NO cambia

- El middleware `EnsureAdmin` sigue protegiendo las rutas de admin
- Usuarios regulares no pueden acceder a `/dashboard` ni a los endpoints de productos
- El flujo de registro/login no cambia visualmente para el usuario final
