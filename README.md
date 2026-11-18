# JobYaari Blog Management System

## QA results on master

QA target for this run: `http://127.0.0.1:8000`.

| Check | Command | Literal output |
|---|---|---|
| HTTP route matrix | `for path in / /blogs/filter '/blogs/filter?category=Unknown' /blogs/1 /blogs/999999 /admin/login /admin/dashboard; do printf '%s ' "$path"; curl -s -o /dev/null -w '%{http_code}' "http://127.0.0.1:8000$path"; printf '\n'; done` | `/ 200`<br>`/blogs/filter 200`<br>`/blogs/filter?category=Unknown 200`<br>`/blogs/1 200`<br>`/blogs/999999 404`<br>`/admin/login 200`<br>`/admin/dashboard 302` |
| Newman collection | `npx --yes newman run postman/blog-management.postman_collection.json --env-var baseUrl=http://127.0.0.1:8000 --env-var "adminEmail=$QA_ADMIN_EMAIL" --env-var "adminPassword=$QA_ADMIN_PASSWORD" --env-var blogId=$QA_EXISTING_BLOG_ID -r cli` | `│              requests │                14 │                0 │`<br>`│            assertions │                31 │                0 │` (exit status `0`) |
| Laravel tests | `php artisan test` | `Tests:    11 passed (76 assertions)` |
| `qa.md` test-case rows by Status | `awk -F'|' '/^\| [A-Z]+-[0-9]+ \|/ { status=$8; gsub(/^[ \t]+|[ \t]+$/, "", status); count[status]++; total++ } END { printf "test-case rows: %d\n", total; for (status in count) printf "%s: %d\n", status, count[status] }' qa.md` | `test-case rows: 48`<br>`Pass: 48` |
| SQLite blog rows before one additional blog creation | `sqlite3 /tmp/blog-management-reconcile.sqlite 'SELECT count(*) FROM blogs;'` | `1` |
| SQLite blog rows after one additional blog creation | `sqlite3 /tmp/blog-management-reconcile.sqlite 'SELECT count(*) FROM blogs;'` | `2` |

Detailed test outputs and the diagnosis of the earlier seven Newman failures are in [test-artifacts/results.md](./test-artifacts/results.md). Related reports: [QA inventory](./qa.md) · [confirmed defects](./test-artifacts/defects.md).

## About

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

## Re-run checks

Run the Laravel unit and feature tests from the project root:

```bash
php artisan test
```

The test suite uses an in-memory SQLite database. Run the Newman collection
against a disposable database because it creates, updates, and deletes a blog:

```bash
npx --yes newman run postman/blog-management.postman_collection.json \
  --env-var "baseUrl=http://127.0.0.1:8000" \
  --env-var "adminEmail=$QA_ADMIN_EMAIL" \
  --env-var "adminPassword=$QA_ADMIN_PASSWORD" \
  --env-var "blogId=$QA_EXISTING_BLOG_ID" \
  -r cli
```

Set the `QA_*` variables in your local shell. `QA_EXISTING_BLOG_ID` must refer
to a disposable blog for the collection's initial detail request. Do not
commit credentials or use a production database.
