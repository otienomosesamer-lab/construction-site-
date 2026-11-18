# Construction Site Materials & Equipment Tracking Information System

**Student:** Moses Ochiengs — Admission No. 227313
**Programme:** Diploma in Business and Information Technology, Strathmore University
**Proposal:** October 2026 — *Development of a Construction Site Materials and Equipment Tracking Information System*

A web-based system that replaces notebooks / separate spreadsheets with one central record for materials received & issued, stock, equipment assignment & location, and maintenance — with reports for the Project Manager.

## GitHub bridge & VS Code

Repo: https://github.com/otienomosesamer-lab/construction-site- — branch `main`.
Clone in VS Code: `git clone https://github.com/otienomosesamer-lab/construction-site-.git`
then rename the folder to `construction-tracking-system` and open it in VS Code.
Pull Archie's changes: Source Control → Pull, or `git pull origin main`.
Full steps: see `VSCODE.md`. Changes: see `CHANGELOG.md`.

## Tech stack (Proposal §3.3.4)

| Component | Tool | Reason |
|---|---|---|
| Front end | HTML, CSS, JavaScript | UI + interactive forms |
| Back end | PHP (PDO, prepared statements) | Request processing & system logic |
| Database | MySQL | Users, materials, transactions, equipment, maintenance |
| Dev environment | Visual Studio Code | Writing & managing the code |
| DB management | MySQL / phpMyAdmin | Create, manage & test the database |
| Browser | Google Chrome | Run & test the web app |

## Project structure

```
construction-tracking-system/
├── index.php            # Redirects to login / dashboard
├── login.php / logout.php
├── dashboard.php        # Stock alerts, recent movements, equipment status
├── materials.php        # Module 2: add material, record received/issued, stock auto-updates
├── equipment.php        # Module 3: add equipment, assign / return, location & status
├── maintenance.php      # Module 4: routine / repair / inspection, cost, next due date
├── reports.php          # Module 5: stock, received/issued, equipment, maintenance + CSV export
├── users.php            # Module 1: user management (Project Manager only)
├── setup.php            # ONE-TIME installer — run once, then delete
├── database.sql         # Schema + sample data (cement, sand, steel, timber, mixers, generators…)
├── config/database.php  # PDO connection (XAMPP defaults: root / no password)
├── includes/auth.php    # Sessions + role-based access control
├── includes/header.php / footer.php
└── assets/css/style.css, assets/js/app.js
```

## Run it in VS Code + XAMPP (5 minutes)

1. Install **XAMPP** (Apache + MySQL) and **VS Code**.
2. Copy this folder to `C:\xampp\htdocs\construction-tracking-system` (Windows) or `/Applications/XAMPP/htdocs/…` (Mac).
3. Start **Apache** and **MySQL** in the XAMPP Control Panel.
4. Open the folder in VS Code: `File → Open Folder`.
5. In Chrome go to: `http://localhost/construction-tracking-system/setup.php`
   - This creates database `construction_tracking`, runs `database.sql`, and creates the demo users.
   - **Delete `setup.php` after this step.**
6. Log in at `http://localhost/construction-tracking-system/login.php`

## Demo logins (password for all: `Pass@123`)

| Role | Email | Can do |
|---|---|---|
| Project Manager | pm@site.local | Everything, reports, user management |
| Storekeeper | store@site.local | Materials: add, receive, issue |
| Equipment Manager | equip@site.local | Equipment + maintenance |

Change these passwords / delete demo users before any real use.

## What each objective maps to

1. Manual-tracking challenges — Ch. 1–2 of the proposal; solved by central records.
2. Materials received / issued / stock — `materials.php` + `material_transactions` table.
3. Equipment details, assignment, location, maintenance — `equipment.php`, `maintenance.php`.
4. Reports — `reports.php` (stock value, received/issued by date, equipment location, maintenance history, CSV export).
5. Accessibility & organisation — role-based login, dashboard, one central database.

## Scope / limits (as in the proposal)

No procurement, accounting/payroll, physical logistics, GPS tracking, or RFID/BIM/GIS in the implementation — those are discussed in the literature review only. Accuracy depends on users recording receipts, issues and equipment changes promptly.

## Testing (Proposal Table 3.1)

Try: login with wrong password (fails) → correct login (dashboard) → add a material → receive 50 → issue 10 → check stock changed by +40 → assign the generator → record a repair → open Reports → export CSV. Each step is a test case: function, input, expected result, actual result, status.
