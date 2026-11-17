# JobYaari Blog Management System

JobYaari is a Laravel blog app for publishing and browsing articles. Visitors can filter the public listing by category and date, open full article pages, and administrators can sign in to create, edit, and delete posts.

## Features
- Public blog listing page with data fetched from the database
- AJAX + jQuery category/date filtering without page reload
- Full blog detail page
- Simple admin login with session-based protection
- Admin CRUD for adding, editing, and deleting blogs

## Admin Access
- URL: http://127.0.0.1:8000/admin/login
- Email: admin@jobyaari.dev
- Password: admin12345

## Tech Stack
- PHP / Laravel
- SQLite for local development
- Blade templates and responsive CSS
- jQuery + AJAX for live filtering

## Setup
1. composer install
2. php artisan key:generate
3. php artisan migrate --seed
4. php artisan serve

## Live Preview
- Public site: http://127.0.0.1:8000/
- Admin panel: http://127.0.0.1:8000/admin/login

## Verification

### Automated tests

Run the Laravel unit and feature tests from the project root:

```bash
php artisan test
```

The test suite uses an in-memory SQLite database. It does not modify the local application database.

To run the API/web-route collection against a local app, first start the app and use a **disposable database** because the collection creates, updates, and deletes a blog:

```bash
npx --yes newman run postman/blog-management.postman_collection.json \
  --env-var "baseUrl=http://127.0.0.1:8000" \
  --env-var "adminEmail=$QA_ADMIN_EMAIL" \
  --env-var "adminPassword=$QA_ADMIN_PASSWORD" \
  --env-var "blogId=$QA_EXISTING_BLOG_ID"
```

Set the three `QA_*` variables in your local shell. `QA_EXISTING_BLOG_ID` must identify a disposable test blog for the collection's initial detail request. The collection captures its CSRF token and the created blog ID during the run. Do not commit credentials or use a production database.

### Recorded QA results

Results from 2026-11-17:

- Laravel tests: **11 passed, 76 assertions**.
- Browser matrix: **48 cases run in Chrome, Firefox, and Edge; 144/144 executions passed**. The case-by-case results are in [`qa.md`](./qa.md) and [`test-cases/qa-test-log.xlsx`](./test-cases/qa-test-log.xlsx).
- Newman collection: **14 requests, 31 assertions, 0 failures**.
- Empty and malformed input recheck: no server errors or invalid writes. The report notes that array-shaped filters are accepted and invalid login uses the normal redirect response.
- No application defects were found. Mobile/responsive device testing was not performed.
- Controlled mutation demonstrations temporarily introduced and reverted one failing regression per feature category; details and observed failures are clearly labeled as simulated in [`qa.md`](./qa.md).

The browser matrix used a temporary Playwright harness that is not included in the repository; the browser cases can be followed from the QA log, but there is currently no checked-in command to replay that matrix automatically.
