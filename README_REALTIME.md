Realtime chat setup (developer notes)

Options (pick one):

A) Quick local: laravel-echo-server (Node) - recommended for local dev

1. Install laravel-echo-server globally:
    # PowerShell (Windows)
    npm install -g laravel-echo-server
2. Ensure you have Redis running locally (echo uses redis by default) or change `laravel-echo-server.json` to use "database": "redis" and configure host/port.
3. Start the server from project root:
    # From project root (PowerShell):
    npx laravel-echo-server start
4. Ensure your backend `.env` has:
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=local
   PUSHER_APP_KEY=local
   PUSHER_APP_SECRET=local
   PUSHER_HOST=127.0.0.1
   PUSHER_PORT=6001
   PUSHER_SCHEME=http
5. Start frontend dev server and it will attempt to initialize Echo if VITE_PUSHER_APP_KEY is set in your `.env`/Vite env.

Vite/.env notes (example .env for frontend):

VITE_PUSHER_APP_KEY=local
VITE_PUSHER_HOST=127.0.0.1
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http

B) Hosted Pusher

1. Create a Pusher app and put credentials in `.env`:
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=...
   PUSHER_APP_KEY=...
   PUSHER_APP_SECRET=...
   PUSHER_APP_CLUSTER=...
2. Configure frontend env VITE_PUSHER_APP_KEY to the same key.

Notes:

-   The application already emits broadcastable events `AdminMessageSent` and `CustomerMessageSent` on private channel `order.{id}`.
-   The SPA uses token-based auth (Bearer) and admin uses session cookie. For private channel auth the backend endpoint `/broadcasting/auth` will use the current guard to authorize.
-   If you want, I can (a) fully install and configure laravel-websockets (composer) to avoid external tools, or (b) wire laravel-echo-server with docker-compose to run Redis and the echo server.
