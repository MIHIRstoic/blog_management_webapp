# JobYaari Blog Hub - QA Inventory and Results

## Application screens and available actions

### Public blog homepage (`GET /`)
- View all saved blog cards, including title, category, summary, image, publication date, and a Read More link.
- Open the Admin Login screen.
- Select a category, including All.
- Select a publication date.
- Apply category and/or date filters without a full-page reload.
- Reset filters and restore the unfiltered blog list.

### Blog detail (`GET /blogs/{blog}`)
- Read the selected blog's title, category, publication date, image, summary, and full content.
- Return to the public blog homepage.

### Admin login (`GET /admin/login`, `POST /admin/login`)
- Enter an email and password and submit the login form.
- See a validation error for invalid credentials.
- Return to the public blog homepage.

### Admin dashboard (`GET /admin/dashboard`, admin session required)
- View all saved blogs.
- Open the Add Blog form.
- Open a blog's Edit form.
- Delete a blog after confirming the browser prompt.
- Log out.
- See a success message after a create, update, or delete action.

### Create blog (`GET /admin/blogs/create`, `POST /admin/blogs`)
- Enter a title, category, optional image URL, optional publication date, short description, and content.
- Submit to create the blog.
- Return to the dashboard or see validation errors and correct invalid input.

### Edit blog (`GET /admin/blogs/{blog}/edit`, `PUT /admin/blogs/{blog}`)
- Change a blog's title, category, image URL, publication date, summary, or content.
- Submit to save changes.
- Return to the dashboard or see validation errors and correct invalid input.
- `GET /admin/blogs/{blog}` is also available and redirects to the edit form.

### Other route actions
- `GET /blogs/filter` returns matching blog records as JSON for the homepage filters.
- `POST /admin/logout` ends the admin session.
- Admin dashboard and blog-management routes redirect guests to the login screen.
- This project has no public registration, comments, payments, checkout, or booking screens.

## Selected features and expected behavior

1. **Public listing and filtering:** The homepage should show saved blogs and update its results to match the selected category and publication date, while Reset restores all blogs.
2. **Blog details:** Selecting Read More should open the selected blog's full content, and the back link should return to the homepage.
3. **Admin authentication:** Valid admin credentials should grant access to the dashboard, invalid credentials should show an error, and logout should end access.
4. **Blog management (CRUD):** An authenticated admin should be able to create, view, edit, and delete blogs, with successful changes reflected in the dashboard and public listing and invalid input explained.

## Test cases

The 48 cases below cover the four selected features. Expected results are defined before execution. Use a disposable test database and create any indicated fixture blogs through the admin form; do not delete the seeded production records.

| ID | Feature | Title | Steps | Expected Result | Actual Result | Status | Defect ID |
|---|---|---|---|---|---|---|---|
| LIST-01 | Public listing and filtering | Show saved blogs on homepage | 1. Open `/`. 2. Find a known saved blog by its title. | HTTP 200; the blog card and its category, summary, and Read More link are present; Admin Login is same-origin. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-02 | Public listing and filtering | Filter by a valid category | 1. Open `/blogs/filter?category=Admit%20Card`. 2. Inspect the JSON response. | HTTP 200; only blogs in Admit Card are returned. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-03 | Public listing and filtering | Combine category and date filters | 1. Create Result fixtures dated 2026-11-17 and 2026-11-16 and an Admit Card fixture dated 2026-11-17. 2. Request `/blogs/filter?category=Result&date=2026-11-17`. | Only the Result fixture dated 2026-11-17 is returned. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-04 | Public listing and filtering | Handle category with no matches | 1. Request `/blogs/filter?category=Unknown`. | HTTP 200 with an empty JSON array. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-05 | Public listing and filtering | Select All categories | 1. Open `/`. 2. Select All. 3. Click Apply Filter. | The list includes blogs from every category. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-06 | Public listing and filtering | Filter by date only | 1. Create two blogs on different dates. 2. Choose the first blog's date and click Apply Filter. | Only blogs published on the chosen date appear. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-07 | Public listing and filtering | Combined filters with no matches | 1. Select a category/date combination with no saved blog. 2. Click Apply Filter. | The list displays “No blogs match the current filter.” | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-08 | Public listing and filtering | Reset active filters | 1. Select a non-All category and a date. 2. Click Apply Filter. 3. Click Reset. | Category returns to All, date is cleared, and the full blog list returns. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-09 | Public listing and filtering | Request list without filter values | 1. Request `/blogs/filter` without query parameters. | HTTP 200; all saved blogs are returned. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-10 | Public listing and filtering | Submit malformed date filter | 1. Request `/blogs/filter?date=not-a-date`. | Request does not cause a server error; no blog matches the malformed date. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-11 | Public listing and filtering | Verify newest-first ordering | 1. Create two blogs with different publication dates. 2. Open `/`. | The more recently published blog appears before the older one. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| LIST-12 | Public listing and filtering | Display blog with no publication date | 1. Create a blog and leave Published Date blank. 2. Open `/`. | Blog appears without a publication date and the page loads without an error. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-01 | Blog details | Open an existing blog | 1. Open `/blogs/{id}` using an existing blog ID. | HTTP 200; title, full content, and link back to blogs are shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-02 | Blog details | Request an unknown blog ID | 1. Open `/blogs/999999`. | HTTP 404; no unrelated blog is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-03 | Blog details | Verify back-to-home link | 1. Open an existing blog detail page. 2. Inspect the Back to blogs link's `href`. | Link target is `/`, so it stays on the current origin and points to the homepage. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-04 | Blog details | Show the selected blog summary | 1. Create a blog with a unique summary. 2. Open its detail page. | The exact summary is displayed for that blog. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-05 | Blog details | Show category and publication date | 1. Create a blog with a known category and date. 2. Open its detail page. | Correct category and formatted publication date are displayed. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-06 | Blog details | Use fallback when image is empty | 1. Create a blog with no image URL. 2. Open its detail page. | A fallback image is rendered; page returns HTTP 200. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-07 | Blog details | Escape markup in content | 1. Create a fixture whose content contains `<script>alert(1)</script>`. 2. Open its detail page. | Markup is rendered as escaped text and does not execute as script. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-08 | Blog details | Display long blog content | 1. Create a blog with several paragraphs of content. 2. Open its detail page. | The full content is displayed without truncation. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-09 | Blog details | Open a blog with no publication date | 1. Create a blog without a publication date. 2. Open its detail page. | Page returns HTTP 200 and displays the rest of the blog without error. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-10 | Blog details | Verify image alt text | 1. Create a blog with title “Accessibility Fixture” and a valid image URL. 2. Open its detail page. | The image's alt text is “Accessibility Fixture”. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-11 | Blog details | Request blog ID zero | 1. Open `/blogs/0`. | HTTP 404. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| DET-12 | Blog details | Request nonnumeric blog ID | 1. Open `/blogs/not-a-number`. | HTTP 404; no server error occurs. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-01 | Admin authentication | Log in with valid credentials | 1. Open `/admin/login`. 2. Enter the configured admin email and password shown on the login screen. 3. Submit. | Redirect to `/admin/dashboard`; dashboard returns HTTP 200. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-02 | Admin authentication | Reject incorrect password | 1. Enter the configured admin email. 2. Enter an incorrect nonempty password. 3. Submit. | Login is rejected and “Invalid admin credentials.” is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-03 | Admin authentication | Reject unknown email | 1. Enter `unknown@example.com` and any nonempty password. 2. Submit. | Login is rejected and “Invalid admin credentials.” is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-04 | Admin authentication | Reject malformed email | 1. Enter `not-an-email` and a nonempty password. 2. Submit. | Login is rejected with an email-format validation message. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-05 | Admin authentication | Submit empty email | 1. Leave Email empty. 2. Enter a password. 3. Submit the form. | Browser required-field validation prevents submission and identifies Email as required. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-06 | Admin authentication | Submit empty password | 1. Enter the configured admin email. 2. Leave Password empty. 3. Submit the form. | Browser required-field validation prevents submission and identifies Password as required. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-07 | Admin authentication | Check password length boundary | 1. Submit the configured email with a 9-character password formed by removing the final character from the configured 10-character password. 2. Repeat with the exact configured password. | 9-character value is rejected; exact configured password is accepted and opens the dashboard. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-08 | Admin authentication | Verify email case sensitivity | 1. Enter the configured password. 2. Change the email's letter case and submit. | Case-changed email is rejected with “Invalid admin credentials.” | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-09 | Admin authentication | Verify password case sensitivity | 1. Enter the configured email. 2. Change the case of one password letter and submit. | Case-changed password is rejected with “Invalid admin credentials.” | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-10 | Admin authentication | Block guest dashboard access | 1. In a logged-out browser session, open `/admin/dashboard`. | Browser is redirected to `/admin/login` on the same origin. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-11 | Admin authentication | Block guest blog-management access | 1. In a logged-out session, open `/admin/blogs/create`. 2. Repeat for an existing blog's edit URL. | Both requests redirect to `/admin/login`; no admin form is exposed. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| AUTH-12 | Admin authentication | Verify logout ends access | 1. Log in. 2. Click Logout. 3. Open `/admin/dashboard`. | Logout redirects to login; dashboard is inaccessible until logging in again. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-01 | Blog management | Create a valid blog | 1. Log in. 2. Open Add Blog. 3. Enter title, category, valid image URL, date, summary, and content. 4. Submit. | Blog is saved and appears in dashboard and public homepage. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-02 | Blog management | Verify dashboard edit and delete controls | 1. Log in. 2. Create a test blog. 3. Inspect its Edit link and Delete form. 4. Open `/admin/blogs/{id}` for that blog. | Edit and DELETE actions target that blog ID; resource URL redirects to its edit form. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-03 | Blog management | Reject empty title | 1. Open Add Blog. 2. Fill all fields except Title. 3. Submit. | Blog is not saved; “The title field is required.” is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-04 | Blog management | Reject empty category | 1. Open Add Blog. 2. Fill all fields except Category. 3. Submit. | Blog is not saved; “The category field is required.” is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-05 | Blog management | Reject empty summary | 1. Open Add Blog. 2. Fill all fields except Short Description. 3. Submit. | Blog is not saved; “The short description field is required.” is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-06 | Blog management | Reject empty content | 1. Open Add Blog. 2. Fill all fields except Content. 3. Submit. | Blog is not saved; “The content field is required.” is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-07 | Blog management | Check title length boundary | 1. Submit a title of exactly 255 characters with other valid fields. 2. Repeat with a 256-character title. | 255-character title is accepted; 256-character title is rejected with a max-length error. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-08 | Blog management | Check category length boundary | 1. Submit a category of exactly 100 characters. 2. Repeat with a 101-character category. | 100-character category is accepted; 101-character category is rejected with a max-length error. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-09 | Blog management | Reject malformed image URL | 1. Fill all required fields. 2. Enter `not-a-url` in Image URL. 3. Submit. | Blog is not saved; “The image field must be a valid URL.” is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-10 | Blog management | Reject malformed publication date | 1. Fill all required fields and a valid image URL. 2. Submit a malformed date through the form request. | Blog is not saved; a publication-date validation error is shown. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-11 | Blog management | Edit and update a blog | 1. Log in. 2. Open Edit for a saved blog. 3. Change its title and content. 4. Submit. | Changes persist and the updated values appear in dashboard and public detail/listing. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |
| CRUD-12 | Blog management | Delete a blog | 1. Log in. 2. Click Delete for a test blog. 3. Accept the confirmation prompt. | Blog is removed from dashboard and public homepage. | Passed: expected behavior verified in Chrome, Firefox, and Edge (2026-11-17). | Pass | |

## Run and summary

- **QA target:** Local app at `127.0.0.1:8000`.
- Expected results were recorded in this plan before running the new expanded cases.
- **Web cases:** 48 unique cases run in Chrome 159.0.8154.6, Firefox 159.0, and Edge 157.0.4341.60: **144/144 passed, 0 failed**. Actual results and statuses are recorded in the table above.
- **API collection:** 14 requests and 31 assertions run with Newman against the isolated app: **14/14 requests passed, 31/31 assertions passed**. Responses were checked for successful status and non-empty body; the missing-title request verified the validation error. The initial run had seven failures because the invocation left `adminEmail`, `adminPassword`, and the initial `blogId` blank. The collection passed after supplying the required local credentials and a disposable existing blog ID; the request methods and paths match the routes.
- **Laravel tests:** `php artisan test` — **11 passed, 0 failed (76 assertions)**.
- **Database:** Create, update, and delete behavior was checked against an isolated SQLite database; created and updated values matched the submitted values. The repository's `database/database.sqlite` was not modified.
- **Acceptance:** Public listing/filtering, blog details, admin authentication, and blog CRUD are all **Met** for the tested desktop browser environments.
- **Defects:** **0 Critical, 2 High, 2 Medium, 0 Low; 4 total.** All four confirmed defects remain open. Reproduction steps and command/output evidence for each are documented in [test-artifacts/defects.md](./test-artifacts/defects.md).
- **Coverage gap:** the four defects were found by exploratory testing outside the 48 scripted
  cases. The suite does not cover authentication security, session handling, or scheduled publishing.
- **Next:** Run responsive/mobile viewport checks and repeat the write checks against the intended deployed environment. No Android device or mobile visual inspection was part of this run.

## Empty and invalid API input recheck

- Re-ran the Laravel suite in one run with an isolated, in-memory database: **12 tests passed, 77 assertions, 0 failures** (11 existing tests plus the temporary edge-input probe).
- Filter endpoint: no parameters, empty category/date, and `category=All` returned HTTP 200 with the seeded record; unknown category, malformed date, and impossible date returned HTTP 200 with an empty array.
- Array-shaped `category[]` and `date[]` filters returned HTTP 200 and matched the seeded record. They did not produce server errors; the controller/query builder treats these values as multi-value filters rather than rejecting their shape.
- Login: empty credentials, malformed email, and array-shaped email returned HTTP 422. Unknown credentials and array-shaped password returned HTTP 302 to the login form with the invalid-credentials behavior; neither granted access.
- Blog create/update: empty bodies, array-shaped fields, malformed image/date, impossible date, title over 255 characters, and category over 100 characters returned HTTP 422. Invalid requests did not change the database record count. Unknown update/delete IDs and nonnumeric blog IDs returned HTTP 404; guest writes redirected to login.
- **Edge-case failures:** none found (no 5xx responses and no invalid writes). Note that array filter inputs are accepted, and invalid-login responses use the normal HTML redirect flow rather than HTTP 422; these are not failures under the current web-route contract. If a strict JSON API contract is intended, those response shapes should be specified and validated separately.

## Controlled mutation demonstrations

These are intentional, temporary regressions used to show that the feature tests detect failures. Each mutation was removed immediately after its targeted test failed; none remains in the application. These are **simulated failures, not product defects**:

| Feature | Temporary regression | Test that detected it | Observed failure |
|---|---|---|---|
| Public listing and filtering | Ignore the category filter | `ajax_filter_returns_matching_blogs` | Unexpected Result blog appeared in the Admit Card response. |
| Blog details | Return 404 for every detail page | `blog_detail_shows_post_content_and_unknown_blog_returns_404` | Expected HTTP 200, received 404. |
| Admin authentication | Reject valid credentials | `admin_can_log_in_access_dashboard_and_log_out` | Expected redirect to dashboard; received login redirect with invalid-credentials message. |
| Blog management (CRUD) | Skip persistence on create | `admin_can_create_update_and_delete_a_blog` | Expected blog was missing (`ModelNotFoundException`). |

After restoring all four mutations, `php artisan test` passed: **11 tests, 76 assertions, 0 failures**. No real defect IDs or Trello cards were created for these demonstrations.

## Postman API collection

- Import [`postman/blog-management.postman_collection.json`](./postman/blog-management.postman_collection.json) into Postman. It covers all 13 application routes in `routes/web.php`, plus a missing-title validation request.
- The collection was executed with Newman 6.2.3 (Postman's CLI collection runner); Postman Desktop is not installed in this workspace. To repeat against a local app, use `npx --yes newman run postman/blog-management.postman_collection.json --env-var baseUrl=http://127.0.0.1:8000` and provide `adminEmail` and `adminPassword` in a local environment.
- In the collection variables, set `baseUrl` to the app origin with no trailing slash. The initial value is the current Codespaces forwarded URL; use `http://localhost:8000` only when the app is running on the same computer as Postman.
- Set `adminEmail` and `adminPassword` in a local Postman environment; keep credentials out of exported collections.
- Run **Get login form and capture CSRF token** before **Submit admin login**. Postman stores the session cookies; the login-form request script captures the CSRF token automatically.
- Log in before running the Admin blog management requests. The **Create blog** request makes a unique title and captures the created blog ID. The following open/edit/update/delete requests use that ID. A blank `blogId` avoids accidentally modifying a seeded blog.
- Set `category` and `date` collection variables before running **Filter blogs (category and date)**. The collection contains requests for HTML pages and web forms as well as the JSON filter endpoint.

## Database record checks

- The app uses SQLite: `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`.
- To verify a write in the application, log in and open the admin dashboard. Its **All Blogs** table displays persisted records.
- To inspect SQLite directly, open `database/database.sqlite` in DB Browser for SQLite from the project workspace and run:

  ```sql
  SELECT id, title, category, published_at, created_at, updated_at
  FROM blogs
  ORDER BY id DESC;
  ```

- PHPUnit used in-memory SQLite. Browser and Newman checks used `/tmp/jobyaari-qa.sqlite`; neither wrote test records to `database/database.sqlite`.
