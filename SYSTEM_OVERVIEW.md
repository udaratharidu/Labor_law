# Ludexora — Legal Intelligence Platform: System Overview

> **Generated from source inspection on 2026-06-30.**
> This document is the single source of truth for the Ludexora application architecture and implementation. All sections describe functionality that exists in the codebase today.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [System Architecture](#2-system-architecture)
3. [Project Structure](#3-project-structure)
4. [Core Features](#4-core-features)
5. [Chat System](#5-chat-system)
6. [AI Response Rendering](#6-ai-response-rendering)
7. [Legal Acts Browser](#7-legal-acts-browser)
8. [Authentication](#8-authentication)
9. [API Integration](#9-api-integration)
10. [Database Schema](#10-database-schema)
11. [Routing](#11-routing)
12. [UI Components & Layouts](#12-ui-components--layouts)
13. [Configuration](#13-configuration)
14. [Security](#14-security)
15. [Performance Optimizations](#15-performance-optimizations)
16. [Error Handling](#16-error-handling)
17. [Responsive Design](#17-responsive-design)
18. [Build & Deployment](#18-build--deployment)
19. [Monitoring & Analytics](#19-monitoring--analytics)
20. [Current Features (Inventory)](#20-current-features-inventory)
21. [Current Limitations](#21-current-limitations)
22. [Future Improvements](#22-future-improvements)

---

## 1. Project Overview

**Ludexora** is a consumer protection law research platform built as a full-stack Laravel web application. It combines an AI legal assistant chat interface with a browsable index of legal acts and regulations.

### Purpose

Ludexora allows users to:

- Ask a conversational AI assistant questions about consumer rights, contract terms, refund policies, and related legal topics.
- Browse a curated library of acts and regulations sourced from a Legal API.
- Read hierarchically-structured legal act content.
- Maintain a history of past AI chat consultations.

The application explicitly positions its output as "for information only — not legal advice."

### Target Users

End-users who want to understand consumer protection law in plain language, without requiring legal expertise. The interface is designed for non-lawyers.

### Relationship with External Services

| Service | Role |
|---|---|
| **Legal RAG / AI Backend** | Answers user questions using retrieval-augmented generation. Reached at `AI_BACKEND_URL`. |
| **Legal API** | Serves browsable acts data (list, detail, tree structure). Reached at `LEGAL_API_URL`. |
| **Legal Admin** | Not directly integrated in this codebase; presumably manages the Legal API data. |

### High-Level Architecture

```
┌──────────────────────────────────────────────────────────┐
│                   User's Browser                         │
│         Alpine.js + Blade + Tailwind CSS                 │
└──────────┬────────────────────────────────┬──────────────┘
           │ HTTP / Form POST / JSON        │ HTTP
           ▼                                ▼
┌─────────────────────┐        (not direct; served by Ludexora)
│  Ludexora           │
│  Laravel 12 (PHP)   │
│  ┌───────────────┐  │
│  │ Web Routes    │  │──── POST /sessions/{id}/ask ──▶  AI Backend
│  │ Auth Routes   │  │                                  (RAG API)
│  │ API Routes    │  │──── GET /acts, GET /acts/{id}/tree ▶ Legal API
│  └───────────────┘  │
│  MySQL Database     │
└─────────────────────┘
```

### Supported Browsers / Devices

No explicit browser matrix is defined. The UI uses:
- CSS custom properties (widely supported)
- Alpine.js 3.x (ES2017+)
- `min-h-[100dvh]` (modern viewport units, supported in all current browsers)
- Responsive Tailwind breakpoints for mobile, tablet, and desktop

---

## 2. System Architecture

### Frontend Architecture

Ludexora uses **server-side rendering** via Laravel Blade templates. There is no separate single-page application (SPA). Pages are rendered on the server and returned as full HTML. Client-side reactivity is added selectively using **Alpine.js 3.x**.

Alpine.js is registered globally in `resources/js/app.js` and started immediately:

```js
// resources/js/app.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

This means every Blade view can use `x-data`, `x-show`, `x-model`, `x-bind`, etc. without additional imports.

### Component Architecture

```
layouts/main.blade.php          ← Primary full-screen shell
├── Sidebar (inline)
│   ├── Brand header
│   ├── "New Consultation" button
│   ├── Primary navigation (Home / Legal Assistant / Browse Laws)
│   ├── Searchable chat history list
│   └── User footer (name initial, logout)
└── Main content area
    └── @yield('content')
        ├── dashboard.blade.php
        ├── chat/index.blade.php → chat/chat.blade.php
        ├── laws/index.blade.php
        └── laws/show.blade.php → laws/_node.blade.php (recursive)

components/guest-layout.blade.php  ← Auth pages shell
├── Left: brand panel (logo, tagline, bullet list)
└── Right: form slot
    ├── auth/login.blade.php
    ├── auth/register.blade.php
    ├── auth/forgot-password.blade.php
    ├── auth/reset-password.blade.php
    ├── auth/verify-email.blade.php
    └── auth/confirm-password.blade.php
```

### Application Lifecycle

1. User requests a URL.
2. Laravel routes it to the appropriate controller.
3. The controller queries the database (sessions, chats) and/or calls an external service (AI backend, Legal API).
4. A Blade view is rendered with the data and returned as HTML.
5. Alpine.js initializes reactive components in the browser on `DOMContentLoaded`.

### Request Lifecycle — Chat Message

```
User types message → Alpine submitMessage()
  → POST /chat (with CSRF token + session_id)
  → ChatController::store()
     → Validate (message max 3000 chars)
     → Look up or create ChatSession
     → BackendApiClient::sendChatMessage()
        → POST {AI_BACKEND_URL}/sessions/{session_id}/ask
        → Returns { answer: "..." }
     → Chat::create() persists message + response
     → Return JSON { session_id, user_message, ai_response }
  → Alpine receives response
  → Updates currentSessionId
  → Runs typeText() animation (character-by-character)
  → Calls renderResponse() to parse Markdown via marked.js
```

### State Management

There is no global client-side state store. State is managed by:
- **Server-side session** (Laravel database session driver)
- **Alpine.js component state** scoped to each page component (e.g., `chatInterface`, the sidebar `open`/`query` state)
- **URL query parameters** (`?session=session_id`) to resume a specific chat session

---

## 3. Project Structure

```
Labor_law/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── ChatApiController.php    # Internal API for AI backend
│   │   │   ├── Auth/                        # Breeze-generated auth controllers
│   │   │   ├── ChatController.php           # Web chat (index/create/store)
│   │   │   ├── DashboardController.php      # Home page
│   │   │   ├── LawController.php            # Acts browser
│   │   │   └── ProfileController.php        # Profile CRUD
│   │   ├── Middleware/
│   │   │   └── VerifyAiBackendToken.php     # Protects /api/chat/* routes
│   │   └── Requests/
│   │       ├── Auth/LoginRequest.php        # Validates + rate-limits login
│   │       └── ProfileUpdateRequest.php
│   ├── Models/
│   │   ├── Chat.php                         # Single message exchange
│   │   ├── ChatSession.php                  # Conversation container
│   │   ├── ChatSummary.php                  # AI-generated session summary
│   │   └── User.php
│   ├── Services/
│   │   ├── BackendApiClient.php             # Calls AI RAG backend
│   │   └── LegalApiClient.php              # Calls Legal API for acts data
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── View/Components/
│       └── AppLayout.php                    # Wraps layouts/app.blade.php (legacy)
├── bootstrap/
│   ├── app.php                              # Application bootstrap + routing config
│   └── providers.php
├── config/
│   ├── services.php                         # ai_backend + legal_api service config
│   ├── database.php                         # MySQL connection
│   ├── session.php                          # database session driver
│   └── ...                                  # Standard Laravel config files
├── database/
│   ├── migrations/                          # Full schema history
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── TestUserSeeder.php
├── public/
│   ├── index.php                            # Laravel entry point
│   ├── images/                              # Brand assets (SVG + PNG logo)
│   └── build/                              # Vite-compiled assets
├── resources/
│   ├── css/
│   │   └── app.css                          # Tailwind + custom Ludexora CSS
│   ├── js/
│   │   ├── app.js                           # Alpine.js bootstrap
│   │   └── bootstrap.js                     # Axios global setup
│   └── views/
│       ├── auth/                            # Login, register, password reset views
│       ├── chat/
│       │   ├── index.blade.php              # Chat page (wraps chat.blade.php)
│       │   └── chat.blade.php               # Chat UI + Alpine chatInterface()
│       ├── components/                      # Blade components (inputs, buttons, etc.)
│       ├── dashboard.blade.php              # Home / welcome page
│       ├── laws/
│       │   ├── index.blade.php              # Acts list with status filter + pagination
│       │   ├── show.blade.php               # Single act, tabbed tree view
│       │   └── _node.blade.php              # Recursive legal node partial
│       ├── layouts/
│       │   ├── main.blade.php               # Primary sidebar shell
│       │   ├── app.blade.php                # Legacy Breeze layout (unused in main flows)
│       │   ├── guest.blade.php              # Legacy Breeze guest layout (unused)
│       │   └── navigation.blade.php         # Legacy Breeze nav (unused)
│       ├── profile/                         # Profile edit page + partials
│       └── welcome.blade.php               # Public landing page (unauthenticated)
├── routes/
│   ├── web.php                              # Authenticated web routes
│   ├── api.php                              # Internal API for AI backend
│   ├── auth.php                             # Auth routes (Breeze)
│   └── console.php                          # Artisan commands
├── tests/
│   ├── Pest.php
│   └── TestCase.php
├── vite.config.js                           # Vite build config
├── tailwind.config.js                       # Tailwind config
├── package.json                             # JS dependencies
└── composer.json                            # PHP dependencies
```

### Responsibility Summary

| Directory / File | Responsibility |
|---|---|
| `app/Http/Controllers/` | Handle HTTP requests, validate input, return views or JSON |
| `app/Services/` | Encapsulate HTTP calls to external APIs |
| `app/Models/` | Eloquent ORM models, relationships |
| `app/Http/Middleware/` | Cross-cutting request concerns (token auth) |
| `resources/views/` | Blade templates — HTML structure and Alpine.js integration |
| `resources/css/app.css` | Design system: Tailwind utilities + Ludexora custom CSS |
| `resources/js/app.js` | Alpine.js bootstrap |
| `database/migrations/` | Database schema evolution |
| `config/services.php` | External service URLs and credentials |
| `routes/web.php` | User-facing page routes |
| `routes/api.php` | Machine-to-machine API for AI backend callbacks |
| `public/` | Web root; compiled assets; brand images |

---

## 4. Core Features

### AI Legal Chat

- Users submit questions (max 3,000 characters) to the AI legal assistant.
- The server proxies the question to the AI backend and returns the response.
- The response is animated character-by-character in the browser before being rendered as Markdown.

### Conversation Sessions

- Each distinct chat thread is a `ChatSession`, identified by a unique string `session_id`.
- A session is created automatically on the first message if no valid `session_id` is provided.
- The session ID is stored in the URL as `?session={session_id}` and persists across page reloads.
- Up to 100 messages per session are loaded into the chat view.

### Conversation History (Sidebar)

- The sidebar lists up to 50 recent `ChatSession` records for the authenticated user.
- Each history item links to `?session={session_id}` to restore that conversation.
- Session titles are derived from the first 60 characters of the user's opening message.

### Sidebar History Search

- An Alpine.js-powered inline text filter (`x-model="query"`) hides sidebar items whose titles do not contain the search string.
- Filtering is instant, client-side only — no network request.

### New Consultation

- The "New Consultation" button links to `/chat/new`, which renders the chat page with an empty session.

### Suggested Prompts (Welcome State)

- When a chat is empty, four suggested prompt buttons are displayed.
- Clicking one populates the message composer with that prompt (but does not auto-submit).

### Browse Laws

- The `/laws` page lists all acts returned by the Legal API, with status filter tabs (All / Active / Amended / Repealed) and pagination.
- Each act shows its title, short title, status badge, commencement date, and gazette reference.

### Act Detail View

- `/laws/{act_id}` renders the full hierarchical tree of a legal act.
- Top-level nodes are presented as tabs; children are shown with collapsible `<details>` elements.
- Section headings, numbering, and text content are preserved.

### User Profile Management

- Users can update their name, email address, and password.
- Changing email clears email verification status.
- Users can permanently delete their account (requires current password confirmation).

### Dashboard

- The home page (`/`) features a hero section with a rotating typewriter animation cycling through platform taglines.
- Feature cards link to the Legal Assistant and Browse Laws sections.
- A recent chat history list provides quick access to past sessions.

---

## 5. Chat System

### Chat Lifecycle

```
New page load (no session)
  → ChatController::create()
  → Renders chat/index.blade.php with empty chats, no session

User submits first message
  → ChatController::store() creates ChatSession
  → Session ID: "{user_id}_{YmdHisu}" (e.g., "3_20260630143022654321")
  → Chat record created after AI response received
  → JSON response returned to Alpine

Subsequent messages (session exists)
  → ChatController::store() finds existing ChatSession by session_id
  → Appends new Chat record
```

### Message Model (`Chat`)

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Auto-increment primary key |
| `user_message` | text | The user's question |
| `ai_response` | longtext | The AI's full response (Markdown) |
| `chat_session_id` | varchar(191) | FK to `chat_sessions.session_id` |
| `is_summarized` | boolean | Whether this chat has been included in a session summary |
| `created_at` / `updated_at` | timestamp | |

### User Message Flow

1. User types in the `<textarea>` composer (auto-grows up to 192px via `autoGrow()`).
2. Pressing Enter (without Shift) or clicking the Send button calls `submitMessage()`.
3. Alpine immediately pushes a placeholder entry into the `chats` array (optimistic UI).
4. A `fetch` POST to `/chat` is made with `FormData` containing `message` and `session_id`.
5. The `X-CSRF-TOKEN` header is set from the injected token.

### Assistant Response Flow

1. On success, the JSON response provides `session_id` (may be newly created) and `ai_response`.
2. `currentSessionId` is updated, ensuring subsequent messages use the correct session.
3. `typeText()` animates the response character-by-character over approximately 2 seconds.
4. Once animation completes, `renderResponse()` calls `marked.parse()` for full Markdown rendering.

### Typing Animation

```js
async typeText(idx, fullText) {
    const charsPerFrame = Math.max(1, Math.ceil(fullText.length / 120));
    // targets ~2 s total at 60 fps
    // drives requestAnimationFrame loop
}
```

During animation, the raw text is HTML-escaped and a blinking cursor (`.lx-typing-cursor`) is appended.

### Error Recovery

- On any fetch error or non-2xx response, the optimistically added message is removed (`chats.splice(idx, 1)`).
- An error banner is shown: "Could not get a response. Please try again."
- The submit button is re-enabled; the user can retry immediately.

### Loading State

- While awaiting the response, a "thinking dots" animation is shown (three animated dots).
- The submit button is disabled (`loading || !draftMessage.trim()`).

### Cancellation

There is no cancel-in-flight mechanism. The `fetch` call runs to completion. If the user navigates away the request is abandoned.

### Conversation Persistence

- All chat exchanges are stored in the `chats` table immediately after the AI responds.
- On returning to a session URL (`?session=...`), the server loads up to 100 chat records ordered by `created_at` and passes them to the Blade view, which bootstraps Alpine's `chats` array via `@js()`.

---

## 6. AI Response Rendering

### Rendering Pipeline

```
AI response (raw string, may be Markdown)
  → typeText() animation phase:
      escape HTML entities (&, <, >) in raw text
      append blinking cursor span
  → On animation complete:
      marked.parse(ai_response)        ← Markdown → HTML
      set via x-html on .lx-prose div
```

### Markdown Support (via marked.js v12, CDN)

marked.js is loaded from CDN:

```html
<script src="https://cdn.jsdelivr.net/npm/marked@12/marked.min.js"></script>
```

It is configured with `breaks: true` so single newlines produce `<br>`. Supported elements:

| Element | Rendered As |
|---|---|
| Paragraphs | `<p>` with `.8em` bottom margin |
| Headings h1–h4 | Styled with `.lx-prose h1/h2/h3` classes |
| Bold, italic | `<strong>`, `<em>` |
| Unordered lists | `<ul>` with disc markers |
| Ordered lists | `<ol>` with decimal markers |
| Inline code | `<code>` with blue-tinted monospace style |
| Code blocks | `<pre><code>` with slate background |
| Blockquotes | Left blue border, italic, gray text |
| Tables | Full bordered table with header background |
| Links | Blue underline |
| Horizontal rules | Thin slate border |

### Prose Styling (`.lx-prose`)

All AI response content is wrapped in a `div.lx-prose` which applies the full Markdown visual style defined in `resources/css/app.css`.

### Fallback Rendering

If `marked` is not available (e.g., CDN failure), responses are rendered as HTML-escaped plain text with newlines converted to `<br>` tags.

---

## 7. Legal Acts Browser

### Acts List (`/laws`)

The `LawController::index()` method calls `LegalApiClient::listActs()` passing optional `status` and `page` query parameters.

**Response data used:**

| Field | Display |
|---|---|
| `act_id` | Used in the detail link |
| `title` | Act name |
| `short_title` | Subtitle |
| `status` | Badge (active = green, amended = amber, repealed = red) |
| `commencement_date` | Formatted date |
| `gazette.gazette_no` | Gazette number |
| `gazette.gazette_date` | Gazette date |

**Pagination:** Uses `meta.last_page` and `meta.current_page` from the API response to render page number links.

### Act Detail (`/laws/{act_id}`)

`LawController::show()` calls `LegalApiClient::getActTree()` which returns a nested `nodes` array representing the act's hierarchical structure.

**Rendering:**
- Top-level nodes become **tabs** (Alpine.js `activeTab` state).
- Each tab panel shows the node's heading, text content, and children.
- Children are rendered recursively via the `laws/_node.blade.php` partial.
- Nodes with children use collapsible `<details>/<summary>` elements.
- Leaf nodes (no children) render their label and text directly.
- The first child of each panel is open by default (`level === 1`).

### Error States

Both list and detail views handle API failures gracefully:
- If `LegalApiClient` returns `null` (timeout, non-2xx, or exception), `loadFailed = true`.
- An amber warning card is displayed explaining the service is unreachable.
- No exceptions propagate to the user.

---

## 8. Authentication

Authentication is implemented using **Laravel Breeze** scaffolding with custom Ludexora UI styling.

### Login

- Route: `GET/POST /login`
- Email + password form with "Remember me" checkbox.
- Rate-limited: 5 failed attempts trigger lockout (variable seconds, displayed in error).
- On success: session regenerated, redirected to `route('home')`.

### Registration

- Route: `GET/POST /register`
- Fields: Full name, email, password, password confirmation.
- On success: user created, logged in, redirected to home.

### Password Reset

- Routes: `/forgot-password` (request link), `/reset-password/{token}` (set new password).

### Email Verification

- Route: `/verify-email`, `/verify-email/{id}/{hash}`.
- Throttled at 6 attempts per minute.
- `email_verified_at` is currently not enforced as a gate on the main application routes (the `auth` middleware group does not add `verified`).

### Password Confirmation

- Route: `/confirm-password` — used for sensitive operations.

### Logout

- Route: `POST /logout`
- Logs out of the `web` guard, invalidates session, regenerates CSRF token, redirects to `/login`.

### Session Storage

Sessions are stored in the `sessions` database table (configured via `SESSION_DRIVER=database`, `SESSION_LIFETIME=120` minutes).

### Protected Routes

All main application routes (`/`, `/chat`, `/laws`) are wrapped in `Route::middleware('auth')`. Unauthenticated access redirects to `/login`.

### Profile Management

- `GET/PUT /profile` — update name and email.
- `DELETE /profile` — account deletion (requires current password).
- Password update: `PUT /password`.

---

## 9. API Integration

### BackendApiClient (`app/Services/BackendApiClient.php`)

Handles communication with the AI RAG backend.

**Method: `sendChatMessage(string $message, ?int $userId, string $sessionId): string`**

- `POST {AI_BACKEND_URL}/sessions/{session_id}/ask`
- Body: `{ "question": "{message}" }`
- Authorization: Bearer token from `AI_BACKEND_TOKEN`
- Timeout: 90 seconds
- Response parsing: reads `data['answer'] ?? data['response']`
- On failure (failed response): returns a user-friendly fallback string
- If `AI_BACKEND_URL` is not configured: returns a placeholder string

### LegalApiClient (`app/Services/LegalApiClient.php`)

Handles communication with the Legal API serving acts/regulations data.

**Base URL:** `LEGAL_API_URL` (default: `http://127.0.0.1:8001/api/public/v1`)

**Methods:**

| Method | Endpoint | Description |
|---|---|---|
| `listActs(?string $status, int $page)` | `GET /acts` | Paginated list of acts, optional status filter |
| `showAct(int $actId)` | `GET /acts/{id}` | Single act metadata |
| `getActTree(int $actId)` | `GET /acts/{id}/tree` | Hierarchical node tree of an act |

- Timeout: 10 seconds per request
- On failure (non-2xx or exception): returns `null` and logs a warning via `Log::warning()`

### Internal API (`routes/api.php`)

Ludexora also exposes an API that the AI backend can call back into, e.g., to create sessions, persist chats, and manage summaries. All routes are under `/api/chat/` and protected by `VerifyAiBackendToken` middleware.

**Endpoints:**

| Method | Path | Controller Method | Description |
|---|---|---|---|
| `POST` | `/api/chat/sessions` | `createSession` | Create a new chat session for a user |
| `PATCH` | `/api/chat/sessions/{session_id}/title` | `updateTitle` | Update session title |
| `POST` | `/api/chat/sessions/{session_id}/ask` | `ask` | Send message to AI and return response |
| `POST` | `/api/chat/sessions/{session_id}/chats` | `createChat` | Persist a chat exchange |
| `GET` | `/api/chat/sessions/{session_id}/chats` | `getChats` | Retrieve non-summarized chats |
| `DELETE` | `/api/chat/sessions/{session_id}/chats` | `clearChats` | Delete all chats in a session |
| `GET` | `/api/chat/sessions/{session_id}/count` | `countChats` | Count non-summarized chats |
| `GET` | `/api/chat/sessions/{session_id}/summary` | `getSummary` | Get session summary |
| `PATCH` | `/api/chat/sessions/{session_id}/summary` | `updateSummary` | Update or create session summary |
| `PATCH` | `/api/chat/mark-summarized` | `markAsSummarized` | Mark chat IDs as summarized |
| `GET` | `/api/chat/history/{user_id}` | `history` | All sessions for a user |

**Authentication:** Bearer token or `X-Api-Token` header, compared with `AI_BACKEND_TOKEN` using `hash_equals` (constant-time comparison to prevent timing attacks).

**Idempotency:** `createChat` includes a guard that returns an existing record if the same `user_message` for the same session was persisted within the last 2 minutes, preventing duplicates if both the web controller and the API client try to save the same exchange.

---

## 10. Database Schema

### `users`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `email` | varchar, unique | |
| `email_verified_at` | timestamp, nullable | |
| `name` | string | Added in migration `2026_06_16_000001` |
| `password` | string | Bcrypt hashed |
| `remember_token` | string(100), nullable | |
| `created_at` / `updated_at` | timestamp | |

### `chat_sessions`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `session_id` | varchar(191), unique | Format: `{user_id}_{YmdHisu}` |
| `chat_title` | varchar(255), nullable | First 60 chars of opening message |
| `user_id` | bigint, FK → users | Cascade delete |
| `created_at` / `updated_at` | timestamp | |

### `chats`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `user_message` | text | |
| `ai_response` | longtext | |
| `chat_session_id` | varchar(191), FK → chat_sessions.session_id | Indexed, cascade delete |
| `is_summarized` | boolean, default false | |
| `created_at` / `updated_at` | timestamp | |

### `chat_summaries`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `chat_session_id` | bigint, unique, FK → chat_sessions.id | Cascade delete; one summary per session |
| `chat_summary` | text | AI-generated summary of the session |
| `created_at` / `updated_at` | timestamp | |

### Standard Laravel Tables

- `sessions` — database session storage
- `cache` — database cache backend
- `jobs` — queued job records
- `password_reset_tokens` — password reset links

---

## 11. Routing

### Web Routes (`routes/web.php`) — requires `auth` middleware

| Method | URI | Controller | Route Name |
|---|---|---|---|
| GET | `/` | `DashboardController@index` | `home` |
| GET | `/chat` | `ChatController@index` | `chat.index` |
| GET | `/chat/new` | `ChatController@create` | `chat.new` |
| POST | `/chat` | `ChatController@store` | `chat.store` |
| GET | `/laws` | `LawController@index` | `laws.index` |
| GET | `/laws/{act}` | `LawController@show` | `laws.show` |

### Auth Routes (`routes/auth.php`)

| Middleware | Routes |
|---|---|
| `guest` | `GET/POST /login`, `GET/POST /register`, `GET/POST /forgot-password`, `GET/POST /reset-password/{token}` |
| `auth` | `GET /verify-email`, `GET /verify-email/{id}/{hash}`, `POST /email/verification-notification`, `GET/POST /confirm-password`, `PUT /password`, `POST /logout` |

Profile routes are typically registered by Breeze in `web.php` (not shown in the file but used via `ProfileController`).

### API Routes (`routes/api.php`) — requires `VerifyAiBackendToken`

All prefixed `/api/chat/` — documented in [Section 9](#9-api-integration).

### Navigation Guards

All web routes under `web.php` require the `auth` middleware. Unauthenticated requests are redirected to the `login` named route. There are no additional role or permission checks beyond authentication.

### Active Route Highlighting

The sidebar uses `request()->routeIs('chat.*')` and `request()->routeIs('laws.*')` to apply the `.lx-nav-link.active` CSS class.

---

## 12. UI Components & Layouts

### Layouts

#### `layouts/main.blade.php` — Primary Shell

Used by: `dashboard`, `chat/index`, `laws/index`, `laws/show`.

Structure:
- Dark navy sidebar (fixed on mobile, static on desktop)
- Mobile overlay and hamburger button (Alpine `open` boolean)
- Mobile top bar with logo and menu toggle
- Inline history search (`query` Alpine state, client-side filtering)
- Main content area (`@yield('content')`)
- `@stack('scripts')` at body end for page-specific JavaScript

#### `components/guest-layout.blade.php` — Auth Shell

Used by: all auth views (login, register, etc.) via `<x-guest-layout>`.

Structure:
- Left panel: brand logo (Y-axis flip animation), tagline, feature bullet list — hidden on mobile
- Right panel: form slot
- Mobile: compact logo + form only

#### `layouts/app.blade.php` + `layouts/navigation.blade.php` — Legacy

These are Breeze default scaffolding files. They are used only by `profile/edit.blade.php`. The rest of the application uses `layouts/main.blade.php`.

### Blade Components

| Component | File | Purpose |
|---|---|---|
| `<x-guest-layout>` | `components/guest-layout.blade.php` | Auth page shell |
| `<x-app-layout>` | `View/Components/AppLayout.php` | Legacy layout for profile page |
| `<x-input-error>` | `components/input-error.blade.php` | Displays validation errors |
| `<x-input-label>` | `components/input-label.blade.php` | Accessible form labels |
| `<x-text-input>` | `components/text-input.blade.php` | Styled text input |
| `<x-primary-button>` | `components/primary-button.blade.php` | Primary CTA button |
| `<x-secondary-button>` | `components/secondary-button.blade.php` | Secondary button |
| `<x-danger-button>` | `components/danger-button.blade.php` | Destructive action button |
| `<x-modal>` | `components/modal.blade.php` | Modal dialog |
| `<x-dropdown>` | `components/dropdown.blade.php` | Dropdown menu |
| `<x-nav-link>` | `components/nav-link.blade.php` | Navigation link |
| `<x-auth-session-status>` | `components/auth-session-status.blade.php` | Flash status message |
| `<x-application-logo>` | `components/application-logo.blade.php` | SVG logo (legacy) |

### Alpine.js Components

#### `chatInterface(...)` — defined in `chat/index.blade.php`

State:
- `chats` — array of `{ user_message, ai_response, typing }` objects
- `draftMessage` — bound to textarea
- `loading` — controls typing indicator and button disabled state
- `errorMessage` — shown in error banner
- `currentSessionId` — tracks active session

Methods:
- `submitMessage()` — handles fetch, optimistic update, animation
- `typeText(idx, fullText)` — `requestAnimationFrame` character animation
- `renderResponse(chat)` — returns HTML string (escaped or marked)
- `autoGrow()` — resizes textarea to content
- `scrollToBottom()` — keeps latest message visible

#### Sidebar — inline in `layouts/main.blade.php`

State: `open` (mobile drawer), `query` (history search string)

#### Act Detail Tabs — inline in `laws/show.blade.php`

State: `activeTab` (integer index of active top-level node)

#### Typewriter — inline in `dashboard.blade.php`

Cycles through platform taglines with a blinking cursor animation.

---

## 13. Configuration

### Environment Variables

| Variable | Default | Description |
|---|---|---|
| `APP_NAME` | Laravel | Application name |
| `APP_ENV` | local | `local`, `production`, etc. |
| `APP_KEY` | (required) | 32-byte encryption key |
| `APP_DEBUG` | true | Show detailed errors |
| `APP_URL` | http://localhost | Application URL |
| `APP_LOCALE` | en | Application locale |
| `DB_CONNECTION` | mysql | Database driver |
| `DB_HOST` | 127.0.0.1 | Database host |
| `DB_PORT` | 3306 | Database port |
| `DB_DATABASE` | labor_law | Database name |
| `DB_USERNAME` | root | Database user |
| `DB_PASSWORD` | (empty) | Database password |
| `SESSION_DRIVER` | database | Session storage backend |
| `SESSION_LIFETIME` | 120 | Session expiry in minutes |
| `CACHE_STORE` | database | Cache backend |
| `QUEUE_CONNECTION` | database | Queue driver |
| `AI_BACKEND_URL` | (required) | URL of the AI RAG API |
| `AI_BACKEND_TOKEN` | (required) | Shared secret for AI backend auth |
| `LEGAL_API_URL` | http://127.0.0.1:8001/api/public/v1 | URL of the Legal API |
| `LOG_CHANNEL` | stack | Logging channel |
| `LOG_LEVEL` | debug | Minimum log level |
| `BCRYPT_ROUNDS` | 12 | Password hashing cost |

### External Service Configuration (`config/services.php`)

```php
'ai_backend' => [
    'url'   => env('AI_BACKEND_URL'),
    'token' => env('AI_BACKEND_TOKEN'),
],

'legal_api' => [
    'url' => env('LEGAL_API_URL', 'http://127.0.0.1:8001/api/public/v1'),
],
```

### Build Configuration (`vite.config.js`)

```js
laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    refresh: true,  // hot-reload on Blade changes in dev
})
```

### Tailwind Configuration (`tailwind.config.js`)

```js
content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
],
plugins: [forms],  // @tailwindcss/forms for form element reset
```

---

## 14. Security

### CSRF Protection

All state-changing web forms use Laravel's `@csrf` directive which generates a hidden `_token` field. Alpine.js chat submissions set the `X-CSRF-TOKEN` request header from the meta tag.

### Login Rate Limiting

`LoginRequest` enforces a rate limit of **5 failed attempts** per email+IP combination. After lockout, further attempts return a validation error with the remaining wait time.

### Session Security

- Sessions are stored server-side in the database (not cookies).
- Session is regenerated on login (`session()->regenerate()`) to prevent session fixation.
- Session is invalidated and a new CSRF token is generated on logout.

### Internal API Authentication (`VerifyAiBackendToken`)

Bearer token or `X-Api-Token` header is compared with `AI_BACKEND_TOKEN` using `hash_equals()` — a constant-time comparison function that prevents timing side-channel attacks. Returns 401 on mismatch and 500 if the token is unconfigured.

### Input Validation

- Chat messages: `required`, `string`, `max:3000`.
- Registration: email uniqueness enforced, password confirmation required.
- API endpoints: all inputs validated with Laravel's `validate()` before use.
- `user_id` in API session creation is verified to exist (`exists:users,id`).

### XSS Prevention — User Content

User messages are displayed via Alpine `x-text="chat.user_message"` which sets `textContent` (not `innerHTML`). This completely prevents XSS from user-supplied text.

### XSS Prevention — AI Responses

During the typing animation, AI responses are HTML-escaped (replacing `&`, `<`, `>`) before being set via `x-html`. After animation, `marked.parse()` converts Markdown to HTML which is then set via `x-html` on the `.lx-prose` div.

**Note:** No HTML sanitization library (e.g., DOMPurify) is applied after `marked.parse()`. If the AI backend returns content with embedded `<script>` tags or `javascript:` URIs, these could be executed. This is a known limitation (see [Section 21](#21-current-limitations)).

### Password Storage

Bcrypt with 12 rounds (`BCRYPT_ROUNDS=12`), handled by Laravel's `Hash` facade.

### Legal Act Content

Legal act content is displayed via `{{ }}` (escaped output) in Blade, not `{!! !!}`. No XSS risk from Legal API data.

---

## 15. Performance Optimizations

### Vite Asset Compilation

Production assets are compiled with `vite build`:
- CSS and JS are bundled, minified, and content-hashed (e.g., `app-Do6kyWuf.css`).
- The `manifest.json` maps logical names to fingerprinted filenames for cache busting.

### Blade View Caching

Laravel caches compiled Blade views in `storage/framework/views/`. In production, `php artisan view:cache` pre-compiles all views.

### Lazy Loading / Asset Loading

- The Ludexora main layout loads marked.js from CDN (jsDelivr) with no `defer` or `async` attribute. It is a blocking script.
- Tailwind CSS is purged at build time — only classes used in Blade templates are included in the output bundle.

### Auto-Grow Textarea

The chat composer textarea grows on input using `autoGrow()` which sets `height: auto` then reads `scrollHeight` — causing a single reflow per keystroke, capped at 192px.

### Scroll Management

`scrollToBottom()` uses `this.$nextTick()` to defer scrolling until after Alpine's DOM update cycle, preventing scroll flicker during message rendering.

### Database Queries

- Chat history sidebar: fetches only `id`, `session_id`, `chat_title`, `created_at` (no `ai_response`), limited to 50 records.
- Chat view: loads up to 100 messages per session.
- Chat summaries: indexed by `chat_session_id` (unique index).
- Chats table: `chat_session_id` is indexed for fast session lookups.

---

## 16. Error Handling

### API Timeouts

- `BackendApiClient`: 90-second timeout. On `$response->failed()`, returns a user-visible fallback string.
- `LegalApiClient`: 10-second timeout. On any exception (`RequestException` or `\Throwable`), logs a warning and returns `null`. Views check for `null` and render an error card.

### Chat Fetch Errors

Alpine's `submitMessage()` wraps the fetch in a `try/catch`. On error:
1. The optimistic message is removed from the array.
2. The error banner displays: "Could not get a response. Please try again."
3. `loading` is set to `false`, re-enabling the form.

### AI Backend Not Configured

If `AI_BACKEND_URL` is empty, `BackendApiClient::sendChatMessage()` returns `"AI backend is not configured yet. This is a placeholder response."` This allows the application to run without an AI backend during development.

### Legal API Failure

If `LegalApiClient` returns `null`, both `laws/index` and `laws/show` render an amber warning panel. No stack traces are shown to the user.

### Laravel Exception Handler

Laravel's default exception handler is used. In production (`APP_DEBUG=false`), generic error pages are rendered. The `/up` health endpoint returns a 200 to confirm the application is running.

---

## 17. Responsive Design

### Breakpoints (Tailwind Defaults)

| Prefix | Min Width | Usage |
|---|---|---|
| (default) | 0px | Mobile-first base styles |
| `sm:` | 640px | Small tablets |
| `lg:` | 1024px | Desktop |

### Mobile Layout

- Sidebar is hidden (`-translate-x-full`) and toggled by the hamburger button (`@click="open = true"`).
- A dark overlay (`bg-black/60`) covers the main content when the sidebar is open.
- The top bar shows the logo and hamburger button.
- Chat and laws pages scroll within the main content area.

### Desktop Layout

- Sidebar is always visible (`lg:static lg:translate-x-0`), 240px wide.
- The top bar is hidden (`lg:hidden`).
- Content area fills the remaining width.

### Viewport Safety

The root container uses `h-[100dvh]` (dynamic viewport height) to account for mobile browser chrome. Chat and laws pages use `absolute inset-0` within the content area to fill exactly the available space.

### Touch Targets

Interactive elements (nav links, buttons, history items) use minimum heights of 32–40px, appropriate for touch use.

---

## 18. Build & Deployment

### Prerequisites

- PHP 8.2+
- Composer
- Node.js (for asset compilation)
- MySQL database
- AI backend service (optional, for chat to function)
- Legal API service (optional, for laws to function)

### Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Edit .env with DB credentials, AI_BACKEND_URL, LEGAL_API_URL
php artisan migrate
npm install
npm run build
```

### Development

```bash
php artisan serve          # Laravel dev server on :8000
npm run dev                # Vite dev server with HMR
```

### Production Build

```bash
npm run build              # Outputs to public/build/
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Static Assets

All compiled assets are in `public/build/`. Brand images are in `public/images/`. The web server should point its document root to `public/`.

### Session / Cache / Queue

Session (`SESSION_DRIVER=database`), cache (`CACHE_STORE=database`), and queue (`QUEUE_CONNECTION=database`) all use the database. The queue is not used by the application at present (no queued jobs), but the table exists.

### `.htaccess`

`public/.htaccess` provides URL rewriting for Apache (all requests to `index.php`). For Nginx, equivalent rewrite rules must be configured manually.

---

## 19. Monitoring & Analytics

No monitoring, analytics, or error reporting integrations are implemented in the current codebase.

Laravel's logging is configured for the `stack` channel (wrapping `single`), writing to `storage/logs/laravel.log`. Failed Legal API requests are logged at the `warning` level via `Log::warning()`.

There are no integrations with Sentry, Datadog, Google Analytics, or similar services.

---

## 20. Current Features (Inventory)

### Chat Features

- [x] AI legal question answering
- [x] Character-by-character response animation (~2 second target)
- [x] Markdown rendering of AI responses (via marked.js)
- [x] Session-based conversation grouping
- [x] Session persistence across page reloads (URL `?session=` parameter)
- [x] Optimistic UI (user message appears immediately)
- [x] Auto-growing textarea composer (max 192px)
- [x] Enter to submit, Shift+Enter for new line
- [x] Thinking/loading animation (bouncing dots)
- [x] Error recovery banner on failed request
- [x] Suggested prompt buttons on empty chat
- [x] Up to 100 messages loaded per session

### Legal Research Features

- [x] Browsable acts list from Legal API
- [x] Status filter (All / Active / Amended / Repealed)
- [x] Paginated acts list
- [x] Act status badges (color-coded)
- [x] Act detail with hierarchical section tree
- [x] Tabbed top-level section navigation
- [x] Collapsible subsection display (`<details>`)
- [x] Commencement date and gazette reference display

### User Features

- [x] Email + password registration
- [x] Login with "Remember me"
- [x] Forgot password / email reset flow
- [x] Email verification support (routes exist; not enforced as access gate)
- [x] Password confirmation page
- [x] Profile: update name and email
- [x] Profile: change password
- [x] Profile: delete account (with password confirmation)
- [x] Logout

### Conversation History Features

- [x] Sidebar listing up to 50 recent sessions
- [x] Inline client-side search/filter of history
- [x] Active session highlighted in sidebar
- [x] Session title derived from opening message (first 60 chars)
- [x] "New Consultation" button to start a fresh session

### UI Features

- [x] Dark navy sidebar, white main content (fixed design, no user theme toggle)
- [x] Animated logo (3D Y-axis rotation)
- [x] Responsive sidebar (mobile drawer)
- [x] Mobile top bar with hamburger menu
- [x] Branded guest layout for auth pages (split panel)
- [x] Typewriter animation on dashboard hero
- [x] Feature cards with hover lift on dashboard

### Internal API Features (for AI backend consumption)

- [x] Create / title update for sessions
- [x] Ask (proxy question to AI backend)
- [x] Persist / retrieve / clear chat messages
- [x] Chat message count
- [x] Session summary create / update / retrieve
- [x] Mark chats as summarized
- [x] Retrieve full session history by user

---

## 21. Current Limitations

### No HTML Sanitization on AI Responses

After `marked.parse()`, the resulting HTML is set directly via `x-html`. There is no DOMPurify or equivalent sanitizer. Malicious content from the AI backend could inject executable scripts into the user's browser.

### No True Streaming

AI responses are fetched as a single HTTP response body (not server-sent events or WebSockets). The "typing" animation is a client-side visual effect applied to the already-complete response text.

### No Request Cancellation

Once a chat message is submitted, the in-flight HTTP request cannot be cancelled from the UI. If the AI backend is slow (up to the 90-second timeout), the user must wait.

### marked.js Loaded from CDN

The Markdown renderer is loaded from `cdn.jsdelivr.net` at runtime without Subresource Integrity (SRI) verification. A CDN compromise or outage would affect Markdown rendering.

### Profile Page Uses Legacy Layout

`profile/edit.blade.php` uses `<x-app-layout>` (the Breeze default) which renders with `layouts/app.blade.php` and `layouts/navigation.blade.php`, inconsistent with the rest of the application's `layouts/main.blade.php` design.

### No Pagination in Chat View

The chat view loads at most 100 messages per session. Older messages in a session beyond 100 are not accessible from the UI.

### No Sidebar Pagination

The sidebar lists at most 50 sessions. Sessions beyond 50 are not shown or accessible from the sidebar.

### No File Upload

The chat interface does not support file or document uploads.

### No Citation / Evidence Display

AI responses are rendered as plain Markdown text. There is no structured citation extraction, evidence panel, source card, or legal document link rendering — beyond whatever the AI backend includes in its plain-text response.

### No Copy / Share / Export

There are no buttons to copy, share, or export individual messages or entire conversations.

### No Theme Toggle

The application has a fixed visual theme (dark navy sidebar, white main area). There is no user-facing dark/light mode toggle.

### Email Verification Not Enforced

The `verified` middleware is not applied to any routes. Users can access the full application without verifying their email.

### No Search Across Chat Content

Chat history search in the sidebar filters by session title only, not message content.

---

## 22. Future Improvements

The following are noted only where evidence exists in the codebase (code comments, TODO markers, or structural stubs):

- **AI backend URL is nullable** (`BackendApiClient` checks `if (! $endpoint)`): the placeholder response path implies the AI backend integration is expected to be configured in production but may not always be available in development.

- **`data['answer'] ?? data['response']`** in `BackendApiClient::sendChatMessage()`: the dual key check suggests the AI backend response schema may not yet be finalised.

- **`chat_id` parameter in `ChatApiController::ask()`**: the `chat_id` field is accepted in the request body and returned in the response but is not used for any logic, suggesting a future use case (e.g., linking an AI ask to an existing chat record for retry/edit flows).

- **`ChatSummary` model and summarization API endpoints**: the summarization infrastructure (`is_summarized` flag, `mark-summarized` endpoint, `getSummary`/`updateSummary`) is fully implemented on the storage side but no client-side trigger for summarization is present, implying this is driven by the AI backend autonomously.

No TODO comments, roadmap documents, or issue references were found in the codebase beyond the above inferences.
