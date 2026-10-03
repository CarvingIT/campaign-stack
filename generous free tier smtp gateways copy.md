# 🎁 Generous Free-Tier SMTP Gateways: Complete Setup & Walkthrough Guide

This guide provides a comprehensive list and step-by-step setup instructions for outbound SMTP providers offering the most generous **forever-free tiers** (with no expiring trials). By combining multiple free gateways in **CampaignStack**, you can send **15,000+ to 20,000+ emails every month at zero cost**.

---

## 📊 Quick Comparison Matrix: Forever-Free Tiers

| Provider | Monthly Quota | Daily Limit | Credit Card Needed? | SMTP Host | Port & Encryption |
| :--- | :---: | :---: | :---: | :--- | :--- |
| **Scaleway TEM** | **10,000 / mo** | None | Yes (ID check only) | `smtp.tem.scaleway.com` | `587` (TLS) / `465` (SSL) |
| **Brevo (Sendinblue)** | **9,000 / mo** | 300 / day | **No** | `smtp-relay.brevo.com` | `587` (TLS) |
| **Mailjet** | **6,000 / mo** | 200 / day | **No** | `in-v3.mailjet.com` | `587` (TLS) / `465` (SSL) |
| **Resend** | **3,000 / mo** | 100 / day | **No** | `smtp.resend.com` | `465` (SSL) / `587` (TLS) |
| **MailerSend** | **3,000 / mo** | None | **No** | `smtp.mailersend.net` | `587` (TLS) |
| **SendGrid** | **3,000 / mo** | 100 / day | **No** | `smtp.sendgrid.net` | `587` (TLS) |
| **SMTP2GO** | **1,000 / mo** | 250 / day | **No** | `mail.smtp2go.com` | `587` (TLS) / `2525` |
| **Personal Gmail SMTP** | **15,000 / mo** | 500 / day | **No** | `smtp.gmail.com` | `587` (TLS) / `465` (SSL) |

---

## 1. 🚀 Brevo (Sendinblue) — 300 Emails/Day (9,000/Month)
* **Why it's great:** No credit card required, reliable European infrastructure, high daily free limit.

### ⚙️ Step-by-Step Setup:
1. **Sign Up:** Go to [brevo.com](https://www.brevo.com) and create a free account.
2. **Authenticate Domain:**
   * Go to **Senders, Domains & Dedicated IPs** $\rightarrow$ **Domains** $\rightarrow$ click **Add a Domain**.
   * Add the 3 DNS TXT records displayed by Brevo to your DNS host:
     * `brevo-code` (TXT)
     * DKIM (TXT)
     * DMARC (TXT)
   * Click **Check DNS records** to verify.
3. **Generate SMTP Key:**
   * Click your profile menu (top right) $\rightarrow$ **SMTP & API**.
   * Click the **SMTP** tab $\rightarrow$ click **Generate a new SMTP key**.
   * Copy the generated master key.

### 🔌 CampaignStack Settings:
* **Name:** `Brevo Free Relay (300/day)`
* **Host:** `smtp-relay.brevo.com`
* **Port:** `587`
* **Encryption:** `tls`
* **Username:** `[Your Brevo account email]`
* **Password:** `[Your Generated SMTP Key]`
* **Sender Email:** `newsletter@yourdomain.com`
* **Sender Name:** `Your Brand`

---

## 2. ⚡ Scaleway Transactional Email — 10,000 Emails/Month (No Daily Cap)
* **Why it's great:** 10,000 free emails every month with **zero daily limit** (send all 10,000 in one day if needed).
* *Note:* Requires card entry for fraud prevention, but charges $0 up to 10k emails.

### ⚙️ Step-by-Step Setup:
1. **Sign Up:** Go to [scaleway.com](https://www.scaleway.com) and create an account.
2. **Enable Transactional Email:**
   * In the console side menu, select **Transactional Email (TEM)**.
   * Click **Add a Domain**, enter your sending domain (e.g. `mail.yourdomain.com`).
   * Scaleway generates SPF, DKIM, and MX DNS records. Add them to your DNS provider and click **Verify**.
3. **Generate API / SMTP Credentials:**
   * In the Scaleway console, go to **IAM** $\rightarrow$ **API Keys** $\rightarrow$ **Generate API Key**.
   * You will receive an **Access Key** and a **Secret Key**.
   * Your SMTP Username is your **Access Key**, and your SMTP Password is your **Secret Key**.

### 🔌 CampaignStack Settings:
* **Name:** `Scaleway TEM (10k/month)`
* **Host:** `smtp.tem.scaleway.com`
* **Port:** `587`
* **Encryption:** `tls`
* **Username:** `[Your Scaleway Access Key]`
* **Password:** `[Your Scaleway Secret Key]`
* **Sender Email:** `hello@mail.yourdomain.com`
* **Sender Name:** `Your Brand`

---

## 3. 📬 MailerSend — 3,000 Emails/Month (No Daily Cap)
* **Why it's great:** Built by the MailerLite team; modern dashboard, fast approval, and no daily throttling.

### ⚙️ Step-by-Step Setup:
1. **Sign Up:** Register at [mailersend.com](https://www.mailersend.com).
2. **Add Domain:**
   * Navigate to **Domains** $\rightarrow$ click **Add Domain**.
   * Add the SPF, DKIM, and Custom Return-Path records to your DNS provider.
   * Click **Verify Domain**.
3. **Retrieve SMTP Credentials:**
   * In your domain page, click the **SMTP** tab.
   * Click **Generate SMTP User** (or copy existing credentials).
   * Save the generated password.

### 🔌 CampaignStack Settings:
* **Name:** `MailerSend Free (3k/mo)`
* **Host:** `smtp.mailersend.net`
* **Port:** `587`
* **Encryption:** `tls`
* **Username:** `[Generated User, e.g. ms_user@domain.com]`
* **Password:** `[Generated Password]`
* **Sender Email:** `news@yourdomain.com`
* **Sender Name:** `Your Brand`

---

## 4. 💎 Resend — 3,000 Emails/Month (100/Day)
* **Why it's great:** Exceptional inbox placement on Gmail and Outlook; clean API keys.

### ⚙️ Step-by-Step Setup:
1. **Sign Up:** Register at [resend.com](https://resend.com).
2. **Add Domain:** Go to **Domains** $\rightarrow$ **Add Domain** $\rightarrow$ Add the 3 DNS records (MX, SPF, DKIM) $\rightarrow$ Click **Verify**.
3. **Create API Key:** Go to **API Keys** $\rightarrow$ **Create API Key** $\rightarrow$ copy `re_xxxxxxxx...`.

### 🔌 CampaignStack Settings:
* **Name:** `Resend Free (100/day)`
* **Host:** `smtp.resend.com`
* **Port:** `465` (SSL) or `587` (TLS)
* **Encryption:** `ssl` (for 465) or `tls` (for 587)
* **Username:** `resend` *(literal word: `resend`)*
* **Password:** `re_your_api_key_here`
* **Sender Email:** `contact@yourdomain.com`
* **Sender Name:** `Your Name`

---

## 5. ✈️ Mailjet — 6,000 Emails/Month (200/Day)
* **Why it's great:** 200 emails every day forever with zero credit card required.

### ⚙️ Step-by-Step Setup:
1. **Sign Up:** Register at [mailjet.com](https://www.mailjet.com).
2. **Verify Domain:**
   * Go to **Account Settings** $\rightarrow$ **Add a Domain or Email Address**.
   * Validate your domain with SPF and DKIM.
3. **Get API Key / Secret:**
   * Go to **Account Settings** $\rightarrow$ **REST API** $\rightarrow$ **Master API Key & Sub-account Key Management**.
   * Copy your **API Key** (Username) and **Secret Key** (Password).

### 🔌 CampaignStack Settings:
* **Name:** `Mailjet Free (200/day)`
* **Host:** `in-v3.mailjet.com`
* **Port:** `587`
* **Encryption:** `tls`
* **Username:** `[Your Mailjet API Key]`
* **Password:** `[Your Mailjet Secret Key]`
* **Sender Email:** `updates@yourdomain.com`
* **Sender Name:** `Your Brand`

---

## 6. 🌐 Personal Gmail SMTP — 500 Emails/Day (15,000/Month)
* **Why it's great:** No custom domain required. Works directly with any personal `@gmail.com` address.

### ⚙️ Step-by-Step Setup:
1. **Enable 2-Step Verification:** Go to [Google Security](https://myaccount.google.com/security) and ensure 2FA is ON.
2. **Create App Password:**
   * Search for **"App passwords"** in the Google Security search bar.
   * App name: `CampaignStack`. Click **Create**.
   * Copy the 16-character password without spaces (e.g. `abcd efgh ijkl mnop`).

### 🔌 CampaignStack Settings:
* **Name:** `Personal Gmail SMTP`
* **Host:** `smtp.gmail.com`
* **Port:** `587` (TLS) or `465` (SSL)
* **Encryption:** `tls`
* **Username:** `yourname@gmail.com`
* **Password:** `abcdefghijklmnop` *(16-character App Password)*
* **Sender Email:** `yourname@gmail.com`
* **Sender Name:** `Your Name`

---

## 💡 The "Free Stacking" Architecture in CampaignStack

Instead of relying on a single free tier, you can configure **all of these gateways** inside CampaignStack (`/outbound_mail_accounts`):

```text
[Broadcast Campaign: Q3 Outreach Sequence]
   ├── Batch 1 (300 emails)  ──> Dispatched via Brevo Free (Quota reset daily)
   ├── Batch 2 (200 emails)  ──> Dispatched via Mailjet Free (Quota reset daily)
   ├── Batch 3 (100 emails)  ──> Dispatched via Resend Free (High deliverability)
   └── Overflow (1,000 emails)──> Dispatched via Scaleway Free (10,000 pool)
```

### Benefits of Stacking:
1. **Total Monthly Capacity:** **20,000+ completely free emails per month**.
2. **Reputation Protection:** If one provider temporarily throttles a batch, your broadcasts switch to the next account without interruption.
3. **Zero Subscriptions:** Everything stays within the permanent free quotas.
