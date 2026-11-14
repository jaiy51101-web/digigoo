# DigiWS Software Update Package (v5.6.1)
**Product ID**: `PRD-ISV5JQOF7S`  
**Target Application**: Premium Digital Product Selling Website  
**Package Version**: `5.6.1`  
**Release Date**: `2026-07-30`

---

## 🚀 How to Publish this Update on your License Server (`https://license.digiws.com`)

1. **Log in to your DigiWS License Server Admin Dashboard** at `https://license.digiws.com/admin`.
2. Go to **Products** -> Select **PRD-ISV5JQOF7S** (Premium Digital Product Selling Website).
3. Click on **New Update Release / Upload Update Package**.
4. Set the parameters:
   - **Version**: `5.6.1`
   - **Release Notes**: `v5.6.1 Update: Enhanced remote license security, automated backup engine, performance optimizations & database sync.`
   - **Upload File**: Select `digiws_update_v5.6.1.zip`
5. Click **Publish Release**.

---

## ⚡ What Happens when Clients Update:
When client software clicks **1-Click Install Remote Update** or uploads `digiws_update_v5.6.1.zip`:
1. Creates an automated backup snapshot in `tmp/backups/`.
2. Extracts updated core code files safely.
3. Automatically executes `update_migration.sql` & `update_migration.php`.
4. Automatically updates local `.env` version to `APP_VERSION=5.6.1`.
5. Logs activity in `tmp/update_history.json`.
