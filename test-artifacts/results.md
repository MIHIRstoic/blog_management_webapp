# QA Results on `master`

The app ran at `127.0.0.1:8000` against a fresh, isolated temporary SQLite
database. The repository database was not changed.

## Route matrix

| Request | Command | Literal output |
|---|---|---|
| `/` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/` | `200` |
| `/blogs/filter` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/blogs/filter` | `200` |
| `/blogs/filter?category=Unknown` | `curl -s -o /dev/null -w '%{http_code}' 'http://127.0.0.1:8000/blogs/filter?category=Unknown'` | `200` |
| `/blogs/1` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/blogs/1` | `200` |
| `/blogs/999999` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/blogs/999999` | `404` |
| `/admin/login` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/admin/login` | `200` |
| `/admin/dashboard` (guest) | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/admin/dashboard` | `302` |

## Newman diagnosis

The earlier invocation supplied only `baseUrl`. Its collection inputs
`adminEmail`, `adminPassword`, and initial `blogId` were blank. The login form
request successfully captured `csrfToken`; the collection has no variable
named `token`. The blank credentials left the session unauthenticated, and the
empty blog ID generated invalid paths. Newman followed the web redirects, so
some unauthenticated form requests ended with HTTP 200 while still failing
their content assertions.

Reproduction command:

```sh
npx --yes newman run postman/blog-management.postman_collection.json \
  --env-var baseUrl=http://127.0.0.1:8000 \
  --reporters cli,json \
  --reporter-json-export /tmp/reconcile-newman-baseline.json
```

Literal summary output:

```text
│              requests │                14 │                0 │
│            assertions │                31 │                7 │
```

Process exit status: `1`.

All seven failures from that invocation are listed below. The default Newman
assertion expected HTTP 200 for each request; content expectations are also
shown where applicable.

| # | Request URL | Method | Expected status / result | Actual status / result | Cause and route comparison |
|---|---|---|---|---|---|
| 1 | `http://127.0.0.1:8000/blogs/` | GET | `200`; display an existing blog | `404` | **Collection input.** `blogId` was empty. The app defines `GET /blogs/{blog}` in [routes/web.php](../routes/web.php); the request omitted the required ID. |
| 2 | `http://127.0.0.1:8000/admin/blogs` | POST | `200`; response contains `The title field is required.` | `200`; response was the login page, not the validation response | **Collection invocation.** `adminEmail` and `adminPassword` were empty, so the protected `POST /admin/blogs` route (`routes/web.php`) redirected the guest to login. With valid authentication, the route reaches blog validation. |
| 3 | `http://127.0.0.1:8000/admin/blogs` | POST | `200`; new blog appears on the dashboard | `200`; no blog was created and the body was the login page | **Collection invocation.** Blank credentials left the request unauthenticated. The path and method match the protected `POST /admin/blogs` store route in `routes/web.php`. |
| 4 | `http://127.0.0.1:8000/admin/blogs/` | GET | `200`; edit shortcut redirects to the edit form | `405` | **Collection input.** `blogId` was empty. `routes/web.php` defines `GET /admin/blogs/{blog}`; the missing ID leaves `/admin/blogs/`, which is not that GET route. |
| 5 | `http://127.0.0.1:8000/admin/blogs//edit` | GET | `200`; edit form is displayed | `404` | **Collection input.** `blogId` was empty. `routes/web.php` defines `GET /admin/blogs/{blog}/edit`, requiring an ID between `blogs/` and `/edit`. |
| 6 | `http://127.0.0.1:8000/admin/blogs/` | PUT | `200`; blog is updated | `405` | **Collection input.** `blogId` was empty. `routes/web.php` defines `PUT /admin/blogs/{blog}`, requiring a blog ID. |
| 7 | `http://127.0.0.1:8000/admin/blogs/` | DELETE | `200`; blog is deleted | `405` | **Collection input.** `blogId` was empty. `routes/web.php` defines `DELETE /admin/blogs/{blog}`, requiring a blog ID. |

The collection's request methods and paths match the registered routes when
their required variables are provided. No application defect was found from
these seven failures, so no additional defect was added.

### Newman rerun with required inputs

An initial disposable blog (ID `1`) was created for the collection's first
detail request. Local credentials were supplied from the repository's existing
admin configuration without recording them here. The collection then captured
the newly created blog ID and successfully completed its edit and delete
requests.

Command:

```sh
ADMIN_EMAIL=$(sed -n 's/^- Email: //p' README.md)
ADMIN_PASSWORD=$(sed -n 's/^- Password: //p' README.md)
npx --yes newman run postman/blog-management.postman_collection.json \
  --env-var baseUrl=http://127.0.0.1:8000 \
  --env-var "adminEmail=$ADMIN_EMAIL" \
  --env-var "adminPassword=$ADMIN_PASSWORD" \
  --env-var blogId=1 \
  --reporters cli,json \
  --reporter-json-export /tmp/reconcile-newman-fixed.json
```

Literal summary output:

```text
│              requests │                14 │                0 │
│            assertions │                31 │                0 │
total run duration: 722ms
```

Process exit status: `0`.

## Other checks

| Check | Command | Literal output |
|---|---|---|
| Laravel tests | `php artisan test` | `Tests:    11 passed (76 assertions)` |
| `qa.md` rows by Status | `awk -F'|' '/^\| [A-Z]+-[0-9]+ \|/ { status=$8; gsub(/^[ \t]+|[ \t]+$/, "", status); count[status]++; total++ } END { printf "test-case rows: %d\n", total; for (status in count) printf "%s: %d\n", status, count[status] }' qa.md` | `test-case rows: 48`; `Pass: 48` |
| SQLite rows before creating one blog | `sqlite3 /tmp/blog-management-reconcile.sqlite 'SELECT count(*) FROM blogs;'` | `1` |
| Create one blog | `curl -sS -b "$COOKIE" -c "$COOKIE" -o /dev/null -w '%{http_code}' -X POST http://127.0.0.1:8000/admin/blogs --data-urlencode "_token=$TOKEN" --data-urlencode "title=Row-count verification blog" --data-urlencode 'category=Testing' --data-urlencode 'short_description=Disposable row-count fixture.' --data-urlencode 'content=Temporary local test content.' --data-urlencode 'published_at=2026-11-18'` | `302` |
| SQLite rows after creating one blog | `sqlite3 /tmp/blog-management-reconcile.sqlite 'SELECT count(*) FROM blogs;'` | `2` |

The SQLite count test started with the Newman detail fixture in the isolated
database; the additional form submission increased the count from one to two.
