Chat & Real-time setup notes

What I added:

-   ChatMessage model + migration (database/migrations/2025_10_12_000001_create_chat_messages_table.php)
-   Events: CustomerMessageSent, AdminMessageSent (broadcastable on private channel `order.{id}`)
-   ChatController with `/api/orders/{order}/chat` GET and POST (auth:sanctum)

What you still need to do to enable realtime chat:

1. Install Laravel Echo & broadcaster (Pusher or laravel-websockets)

    - Composer: `composer require pusher/pusher-php-server` (for Pusher) or `composer require beyondcode/laravel-websockets` (if using websockets package)
    - NPM: `npm install --save laravel-echo pusher-js` or `npm install --save laravel-echo socket.io-client` for websockets

2. Configure `.env` broadcasting settings

    - For Pusher (example):
      BROADCAST_DRIVER=pusher
      PUSHER_APP_ID=your_id
      PUSHER_APP_KEY=your_key
      PUSHER_APP_SECRET=your_secret
      PUSHER_HOST=
      PUSHER_PORT=443
      PUSHER_SCHEME=https

    - For laravel-websockets, follow the package docs to configure `websockets.php` and start the websocket server.

3. Configure `config/broadcasting.php` and ensure `BroadcastServiceProvider` is enabled if needed.

4. Create a client-side Echo listener

    - On customer side (frontend SPA) subscribe to private channel `order.{orderId}` using Echo and listen for `AdminMessageSent` and `CustomerMessageSent` events.
    - On admin side (blade + Livewire), load Echo and join the same private channel to receive messages in real-time.

5. Run migrations

    - php artisan migrate

6. Security
    - Private channels require authorization endpoints; Laravel provides `/broadcasting/auth` which uses `auth:sanctum` by default for SPA. Ensure your frontend sends Authorization header (Bearer token) when using Echo with auth.

UI Integration suggestions:

-   Customer (SPA): add a small chat widget on `OrderConfirmation` that fetches `/api/orders/{order}/chat` and posts messages to same endpoint. Use Echo to append new messages in real-time.
-   Admin (Blade): create a Livewire component `AdminChat` that mounts messages for a selected order and uses Echo + Alpine for real-time updates; admin can type messages and post to `/api/orders/{order}/chat`.

If you want, I can scaffold the Livewire component and the minimal Blade view next (requires adding composer package `livewire/livewire`).
