# VS Code + GitHub bridge — test change (2026-11-18)

This file is the first change pushed by Archie through the GitHub bridge.
If you can see it in VS Code after pulling, the bridge works.

## Clone in VS Code

1. In VS Code: Ctrl+Shift+P → **Git: Clone**
2. Paste: `https://github.com/otienomosesamer-lab/construction-site-.git`
3. Choose `C:\xampp\htdocs` as the folder, then rename the cloned folder to `construction-tracking-system`
4. Open that folder in VS Code: **File → Open Folder**

## Pull Archie's changes

In VS Code, open **Source Control** (left sidebar) → **… → Pull**.
Or in the terminal inside VS Code:

```
git pull origin main
```

After pulling this test change you should see:
- `VSCODE.md` — this file
- `CHANGELOG.md`
- A new "GitHub bridge & VS Code" section in `README.md`

## Push your own changes

1. Edit files in VS Code
2. Source Control → Stage your changes → write a commit message → **Commit**
3. **Sync / Push**
4. Tell Archie in chat — Archie can then read the new version on GitHub

## Run it

Start Apache + MySQL in XAMPP, then open:
`http://localhost/construction-tracking-system/setup.php` (first run only, then delete setup.php)
Log in at `http://localhost/construction-tracking-system/login.php`
Demo logins: pm@site.local / store@site.local / equip@site.local — password `Pass@123` for all.
