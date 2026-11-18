| Req | Metric | Value | Evidence |
|---|---|---|---|
| R1  | defects found, by severity      | 4 Major | qa.md defects section |
| R2  | functional cases executed       | 48 test-case rows; 44 Pass; 4 Fail | qa.md |
| R3  | UI cases executed               | not executed | qa.md |
| R4  | DB write verified               | yes; delta +1 (1 to 2) | sqlite3 delta |
| R5  | API requests / assertions / fails | 14 / 31 / 15 | newman |
| R7  | suite pass rate                 | 6/11 (54.5%) | php artisan test |
| R10 | browser executions              | not executed | qa.md |
| R17 | test cases authored             | 48 | qa.md |
| R18 | mobile viewport cases           | not executed | step 6 |
