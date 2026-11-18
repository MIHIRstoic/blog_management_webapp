# QA Results on `master`

The application was served at `127.0.0.1:8000` against a fresh temporary
SQLite database. The listed commands and outputs are from this run only.

| Check | Command | Literal output |
|---|---|---|
| `/` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/` | `200` |
| `/blogs/filter` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/blogs/filter` | `200` |
| `/blogs/filter?category=Unknown` | `curl -s -o /dev/null -w '%{http_code}' 'http://127.0.0.1:8000/blogs/filter?category=Unknown'` | `200` |
| `/blogs/1` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/blogs/1` | `200` |
| `/blogs/999999` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/blogs/999999` | `404` |
| `/admin/login` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/admin/login` | `200` |
| `/admin/dashboard` | `curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8000/admin/dashboard` | `302` |
| Newman collection | `npx --yes newman run postman/blog-management.postman_collection.json --env-var baseUrl=http://127.0.0.1:8000 -r cli` | `│              requests │                14 │                0 │`; `│            assertions │                31 │                7 │` (process exit status `1`) |
| Newman failed assertions | `npx --yes newman run postman/blog-management.postman_collection.json --env-var baseUrl=http://127.0.0.1:8000 -r cli` | `1.  AssertionError  Response status is successful`; `2.  AssertionError  Missing title returns its validation error`; `3.  AssertionError  Created blog appears on the dashboard`; `4.  AssertionError  Response status is successful`; `5.  AssertionError  Response status is successful`; `6.  AssertionError  Response status is successful`; `7.  AssertionError  Response status is successful` |
| Laravel tests | `php artisan test` | `Tests:    11 passed (76 assertions)` |
| `qa.md` rows by Status | `awk -F'|' '/^\| [A-Z]+-[0-9]+ \|/ { status=$8; gsub(/^[ \t]+|[ \t]+$/, "", status); count[status]++; total++ } END { printf "test-case rows: %d\n", total; for (status in count) printf "%s: %d\n", status, count[status] }' qa.md` | `test-case rows: 48`; `Pass: 48` |
| Blog count before creating a blog | `sqlite3 /tmp/blog-management-master-qa.sqlite 'SELECT count(*) FROM blogs;'` | `0` |
| Create one blog | `curl -sS -b "$COOKIE" -c "$COOKIE" -o /dev/null -w '%{http_code}' -X POST http://127.0.0.1:8000/admin/blogs --data-urlencode "_token=$TOKEN" --data-urlencode 'title=Master QA seeded fixture 2026-11-18' --data-urlencode 'category=QA' --data-urlencode 'short_description=Disposable master QA fixture.' --data-urlencode 'content=Created for this QA run.' --data-urlencode 'published_at=2026-11-18'` | `POST /admin/blogs: 302` |
| Blog count after creating a blog | `sqlite3 /tmp/blog-management-master-qa.sqlite 'SELECT count(*) FROM blogs;'` | `1` |

The isolated database was used for the HTTP and Newman runs; the repository's
local SQLite database was not modified.
