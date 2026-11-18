# Confirmed Defects

All reproductions below were run against a local Laravel server at
`127.0.0.1:8765` with an isolated, temporary SQLite database. No remote
service was contacted. The credential value and session cookie contents are
redacted/not recorded.

## D1 — Hardcoded administrator credentials

- **Severity:** High
- **Priority:** P1
- **Steps:**
  1. Set `ADMIN_PASSWORD` to the repository's hardcoded password and run
     `git grep -n "$ADMIN_PASSWORD"`.
  2. Open `/admin/login` and submit the hardcoded administrator credentials.
- **Expected:** Authentication secrets are not embedded in application code or
  exposed in repository files.
- **Actual:** The administrator email and password are compared directly in
  `AdminController::login`. The supplied credentials are accepted and the
  request redirects to the admin dashboard.
- **Evidence:**

  Command:

  ```sh
  git grep -n "$ADMIN_PASSWORD"
  ```

  For the recorded command, `ADMIN_PASSWORD` was set to the hardcoded value
  being searched for. The literal is omitted from this tracked report.

  Output (credential text redacted to avoid copying it into another tracked
  file):

  ```text
  README.md:15:- Password: [REDACTED]
  app/Http/Controllers/AdminController.php:23:        if ($credentials['email'] === 'admin@jobyaari.dev' && $credentials['password'] === '[REDACTED]') {
  resources/views/admin/login.blade.php:21:    <p style="color:#bfdbfe;">Use admin@jobyaari.dev / [REDACTED].</p>
  tests/Feature/BlogSystemTest.php:121:            'password' => '[REDACTED]',
  ```

  Local login response:

  ```text
  GET /admin/login: 200
  POST /admin/login with listed credentials: 302
  Location: /admin/dashboard
  ```

## D2 — Session ID is not regenerated on login

- **Severity:** Medium
- **Priority:** P2
- **Steps:**
  1. `curl -c jar.txt http://127.0.0.1:8765/admin/login` and extract the form
     CSRF token.
  2. POST the valid credentials to `/admin/login` using the same cookie jar.
  3. Compare the `laravel_session` values from the jar before and after login,
     decrypting them to compare the underlying session IDs.
- **Expected:** The session ID changes after successful authentication.
- **Actual:** The raw encrypted cookie value changes, but the decrypted session
  ID is unchanged. The login method sets the admin session flag without
  regenerating the session.
- **Evidence:**

  Commands: GET the login form with `curl -c jar.txt`, then POST to
  `/admin/login` with `curl -b jar.txt -c jar.txt` and the extracted CSRF token.
  The credential value is redacted.

  Literal output:

  ```text
  POST /admin/login: 302
  Raw laravel_session cookie values equal: no
  Decrypted Laravel session IDs equal: yes
  ```

  The cookie values differ because Laravel encrypts cookies with randomized
  data; comparing the decrypted IDs confirms the session itself was not
  regenerated.

## D3 — No throttling on login POST

- **Severity:** High
- **Priority:** P1
- **Steps:**
  1. GET `/admin/login` and retain its CSRF token and cookie.
  2. POST an incorrect password to `/admin/login` 30 times, sequentially, with
     that token and cookie.
- **Expected:** Repeated failed login attempts are rate-limited and eventually
  return HTTP 429.
- **Actual:** All 30 requests return HTTP 302; none returns HTTP 429.
- **Evidence:**

  Command: a 30-iteration sequential `curl` loop posting an incorrect password
  to `http://127.0.0.1:8765/admin/login` with the form CSRF token and cookie jar.

  Literal output:

  ```text
  30 POST /admin/login status codes: 302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302,302
  HTTP 429 count: 0
  HTTP status counts:
       30 302
  ```

## D4 — Future-dated posts are publicly visible

- **Severity:** Medium
- **Priority:** P2
- **Steps:**
  1. Sign in locally and create a blog titled `Future-dated visibility
     reproduction` with `published_at=2027-01-01`.
  2. Request `/` without authentication and inspect the first blog card.
- **Expected:** A post dated in the future is not included in the public blog
  listing until its publication date.
- **Actual:** The public homepage returns the future-dated post first and shows
  its January 1, 2027 publication date.
- **Evidence:**

  Command: POST the blog fields (including `published_at=2027-01-01`) to
  `/admin/blogs`, then `curl http://127.0.0.1:8765/`.

  Literal output:

  ```text
  POST /admin/login: 302
  POST /admin/blogs: 302
  GET /: 200
  First blog title: Future-dated visibility reproduction
  Published date shown: Published Jan 01, 2027
  ```
