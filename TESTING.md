# Test cases — Construction Tracking System (Proposal Table 3.1)

| # | Test | Input | Expected result | How to check |
|---|---|---|---|---|
| 1 | Login | pm@site.local / wrong password | Access denied, stays on login | login.php |
| 2 | Login | pm@site.local / Pass@123 | Dashboard loads, role = Project Manager | dashboard.php |
| 3 | Material entry | Add "Cement", 100 reorder, 0 stock | Material saved, appears in list | materials.php |
| 4 | Stock — receive | Receive 50 Cement | Stock +50 | materials.php |
| 5 | Stock — issue | Issue 10 Cement | Stock −10, never below 0 | materials.php |
| 6 | Equipment registration | Add Mixer MX-010, available | Equipment saved | equipment.php |
| 7 | Assignment | Assign Mixer to Site Team A, Block A | Status = assigned, location updated | equipment.php |
| 8 | Return | Return Mixer to Main Store | Status = available | equipment.php |
| 9 | Maintenance | Repair on Excavator, KES 45,000, in_progress | Record saved, equipment = under_maintenance | maintenance.php |
| 10 | Report | Reports, filter by date | Correct stock / movements shown | reports.php |
| 11 | CSV export | Export stock CSV | CSV downloads with stock values | reports.php?export=csv |
| 12 | Access control | Log in as Storekeeper, open users.php | 403 Access denied | users.php |
| 13 | Usability | Navigate all pages on mobile width | Sidebar stacks, forms usable | All pages |

Record actual result + Pass/Fail for each in your Chapter 5 testing table.
