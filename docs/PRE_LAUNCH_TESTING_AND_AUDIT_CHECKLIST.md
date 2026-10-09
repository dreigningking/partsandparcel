# Parts & Parcel — Pre-Launch Testing & Auditing Master Checklist

**Generated:** October 9, 2026  
**Target:** Production Launch Readiness  
**Scope:** Security, Performance, Real-World Gateway Sandbox, Disaster Recovery, Transactional Deliverability, Legal/Compliance, and Device UX.

---

## 1. Security & Vulnerability Audits (Penetration & Hardening)

| Audit Domain | Scope & Description | Specific Relevance to Parts & Parcel | Verification Tool / Command | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Dependency CVE Scan** | Scan PHP and JavaScript dependencies for known Common Vulnerabilities and Exposures. | Outdated packages in `composer.json` or `package.json` with known exploits. | `composer audit`<br>`npm audit` | [ ] |
| **Secret & Key Leak Scan** | Scan repo and commit history for plain text credentials or keys. | Ensuring Paystack secret keys, Flutterwave hashes, AWS credentials, or `.env` entries were never committed. | [TruffleHog](https://github.com/trufflesecurity/trufflehog) or `git-secrets` | [ ] |
| **Stored & Reflected XSS Audit** | Input sanitization across user-generated content. | Sellers or buyers embedding malicious `<script>` tags in listing descriptions, dispute notes, or chat messages. | OWASP ZAP / Burp Suite scan | [ ] |
| **CSRF & Webhook Signature Spoofing** | Verification that state-changing requests come from trusted sources. | Verifying Paystack/Flutterwave webhook HMAC SHA-512 signatures and rejecting forged webhook payloads. | Automated replay test with invalid signatures | [ ] |
| **Cookie & Session Security Audit** | Cookie flags and session lifetime policies. | Ensuring `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, and `SameSite=Lax` on production HTTPS. | DevTools Application/Cookies inspection | [ ] |

---

## 2. Performance, Stress & Load Testing

| Audit Domain | Scope & Description | Specific Relevance to Parts & Parcel | Verification Tool / Command | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Concurrent Load & Stress Testing** | System behavior under peak concurrent buyer traffic. | Simulating 200–1,000 concurrent users searching listings, making offers, and checking out simultaneously. | [k6](https://k6.io/) or Locust script | [ ] |
| **Database Query Profiling (N+1 Detection)** | Query count and memory consumption per page request. | Heavy pages like Marketplace Home, Request View, and User Profile with many eager-loaded relations. | [Laravel Pulse](https://laravel.com/docs/pulse) / Telescope | [ ] |
| **Missing Index & Slow Query Audit** | Database queries taking longer than 100ms. | Ensuring foreign keys (`invoice_id`, `listing_id`, `seller_id`, `created_at`) have indexes as tables grow past 50k rows. | MySQL Slow Query Log (`long_query_time = 0.1`) + `EXPLAIN` | [ ] |
| **Queue Backlog & Worker Saturation Test** | Worker behavior under heavy queue bursts. | Enqueuing 5,000 mock notifications/fulfillment checks to confirm Redis queue workers do not run out of memory. | `php artisan queue:work` benchmarking | [ ] |

---

## 3. Payment Gateway Sandbox & End-to-End Smoke Tests

| Audit Domain | Scope & Description | Specific Relevance to Parts & Parcel | Verification Tool / Command | Status |
| :--- | :--- | :--- | :--- | :---: |
| **3DS OTP / Card Challenge Flow** | Real-world card verification steps on mobile & desktop. | Paystack test card challenges (OTP prompt, redirect, cancellation modal mid-flight). | Paystack Sandbox Test Cards | [ ] |
| **Network Dropped Mid-Payment** | Buyer closes browser tab after payment is charged, before redirect. | Proves that the webhook handles settlement and inventory without depending on the front-end redirect. | Simulate browser close before callback URL fires | [ ] |
| **Currency & Precision Rounding Audit** | Sub-unit conversion (Kobo vs. Naira, Cent vs. Dollar). | Ensuring no ₦0.01 rounding discrepancies occur when multiplying unit prices by quantities or calculating escrow fees. | Dedicated unit tests for currency math | [ ] |
| **Bank Transfer / Payout Verification** | Automated seller settlement transfers. | Confirming Paystack Transfer recipient creation and handling of transfer errors (invalid NUBAN account number). | Paystack Transfer API Sandbox | [ ] |

---

## 4. Failure Mode & Disaster Recovery (Resilience Drills)

| Audit Domain | Scope & Description | Specific Relevance to Parts & Parcel | Verification Tool / Command | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Database Backup & Recovery Drill** | Ability to restore from a backup quickly. | Verify you can restore a full database dump to a fresh server in under 15 minutes. | Execute a practice restore on a staging server | [ ] |
| **Redis Restart / Cache Eviction Drill** | Graceful degradation if Redis is rebooted. | Verify that session, queue, and cache recover without corrupting active orders. | Restart Redis while background jobs are running | [ ] |
| **Dead-Letter Queue (DLQ) Monitoring** | Handling permanently failed queue jobs. | Confirming failed jobs are logged in `failed_jobs` and trigger an alert rather than silently disappearing. | Inspect `php artisan queue:failed` | [ ] |
| **Health Check & Uptime Probe** | Load balancer and container liveness probes. | Verifying `/up` (Laravel 11 health route) correctly checks database and cache connectivity. | UptimeRobot / Better Uptime pinging `/up` | [ ] |

---

## 5. Email & Notification Deliverability (Transactional Infrastructure)

| Audit Domain | Scope & Description | Specific Relevance to Parts & Parcel | Verification Tool / Command | Status |
| :--- | :--- | :--- | :--- | :---: |
| **SPF, DKIM & DMARC DNS Records** | Email domain sender reputation. | Preventing critical emails (order receipts, dispute alerts, password resets) from landing in Spam or Gmail Promotions. | [mail-tester.com](https://www.mail-tester.com/) or MXToolbox | [ ] |
| **Email Client Rendering Audit** | Responsive layout across email clients. | Checking transactional order emails on Gmail (mobile/web), Outlook, and Apple Mail in light and dark modes. | Litmus or manual test sends to various email providers | [ ] |
| **Rate Limits on Notification Drivers** | Reaching third-party email/SMS provider caps. | Ensuring notifications fail gracefully or queue up if provider rate limits are hit. | Provider dashboard analytics (Resend/Postmark/Sendgrid) | [ ] |

---

## 6. Legal, Compliance & Audit Trail

| Audit Domain | Scope & Description | Specific Relevance to Parts & Parcel | Verification Tool / Command | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Admin Audit Trail** | Traceability of privileged actions. | Logging which admin approved a refund, released a disputed escrow balance, or suspended a seller. | Custom activity logging or `spatie/laravel-activitylog` | [ ] |
| **Account Deletion & Data Privacy (NDPR)** | Compliance with user data removal requests. | Anonymizing or deleting user personal info while retaining historical tax/financial invoices. | Legal policy review + account deletion controller test | [ ] |
| **Terms of Service & Escrow Disclosure** | Clear user expectations. | Ensuring inspection window timelines and dispute rules are visible before completing checkout. | Product/legal copy review | [ ] |

---

## 7. Cross-Device & Mobile UX Audit

| Audit Domain | Scope & Description | Specific Relevance to Parts & Parcel | Verification Tool / Command | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Mobile Keyboard & Drawer Interactions** | Viewport behavior when mobile keyboards open. | Ensuring drawers (like Make Offer, Quick View Offers, Message Drawer) don't have buttons obscured by virtual keyboards on iOS/Android. | Real device testing (iPhone Safari & Android Chrome) | [ ] |
| **Offline / Spotty Connection Handling** | App behavior when network drops during Livewire actions. | Livewire displays a clean reconnection banner rather than freezing or showing raw error popups. | Chrome DevTools "Slow 3G" & "Offline" throttling | [ ] |

---

## 8. Immediate Recommended Action Plan

Before cutting over to live production traffic, complete these 4 immediate items:

1. **Run Dependency Audits:**
   ```bash
   composer audit
   npm audit
   ```
2. **Execute Payment Gateway Sandbox End-to-End Walkthrough:**
   - Place 3 test orders with real Paystack sandbox cards:
     - Scenario A: Order fulfilled & delivered -> Escrow released.
     - Scenario B: Order damaged -> Dispute raised -> Partial refund issued.
     - Scenario C: Seller payout transfer to mock Nigerian bank account.
3. **Configure DNS Records for Email Deliverability:**
   - Configure SPF (`v=spf1 include:... ~all`), DKIM, and DMARC (`v=DMARC1; p=quarantine; ...`) on your domain DNS.
4. **Deploy Real-Time Error Tracking:**
   - Integrate Sentry, Bugsnag, or Flare into `bootstrap/app.php` to capture uncaught exceptions in production instantly.
