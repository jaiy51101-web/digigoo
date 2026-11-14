<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | DigiWS</title>
    <!-- Font Awesome for icons (optional but enhances visual) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f4f7fc;
            font-family: 'Segoe UI', Roboto, system-ui, -apple-system, sans-serif;
            padding: 2rem 1rem;
            color: #1e2a3a;
            line-height: 1.6;
        }

        .policy-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px -12px rgba(0, 20, 30, 0.2);
            padding: 2.5rem 2.8rem;
            transition: all 0.2s ease;
        }

        @media (max-width: 640px) {
            .policy-container {
                padding: 1.8rem 1.2rem;
                border-radius: 20px;
            }
        }

        /* header */
        .header {
            border-bottom: 2px solid #e6edf4;
            padding-bottom: 1.6rem;
            margin-bottom: 2.2rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-weight: 700;
            font-size: 2.2rem;
            letter-spacing: -0.3px;
            background: linear-gradient(145deg, #0b2b44, #1b4a6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .badge {
            background: #eaf1fa;
            padding: 0.35rem 1.2rem;
            border-radius: 60px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1b4a6b;
            letter-spacing: 0.3px;
            border: 1px solid #cbdae8;
        }

        /* parts */
        .part {
            margin-bottom: 2.8rem;
        }

        .part-divider {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin: 2.5rem 0 1.8rem 0;
        }

        .part-divider span {
            background: #eef3f9;
            padding: 0.25rem 1.2rem;
            border-radius: 60px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #1d4460;
            border: 1px solid #d3e0ed;
        }

        .part-divider hr {
            flex: 1;
            border: none;
            border-top: 2px dashed #d9e3ef;
        }

        h2 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-top: 1.8rem;
            margin-bottom: 0.6rem;
            color: #0b2b44;
            letter-spacing: -0.2px;
        }

        h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 1.8rem 0 0.6rem 0;
            color: #1a3b55;
        }

        h4 {
            font-size: 1rem;
            font-weight: 600;
            margin: 1.2rem 0 0.2rem 0;
            color: #1f4a6b;
        }

        p {
            margin-bottom: 0.8rem;
            color: #1f2c3b;
        }

        ul, ol {
            margin: 0.6rem 0 1rem 1.8rem;
            color: #1f2c3b;
        }

        li {
            margin-bottom: 0.4rem;
        }

        .highlight-box {
            background: #f0f6fe;
            border-left: 5px solid #2b6a95;
            padding: 1.2rem 1.8rem;
            border-radius: 14px;
            margin: 1.6rem 0;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            margin: 0.6rem 0 1.2rem 0;
        }

        @media (max-width: 540px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .card-mini {
            background: #f9fcff;
            padding: 0.8rem 1.2rem;
            border-radius: 14px;
            border: 1px solid #e2ecf7;
        }

        .badge-icon {
            background: #d9e6f5;
            color: #0f3857;
            font-weight: 600;
            font-size: 0.7rem;
            padding: 0.2rem 0.9rem;
            border-radius: 40px;
            display: inline-block;
            margin-right: 0.4rem;
        }

        .faq-item {
            margin: 1.2rem 0;
            border-bottom: 1px solid #ecf2f9;
            padding-bottom: 0.8rem;
        }

        .faq-item strong {
            color: #0b385b;
            display: block;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }

        .faq-item p {
            margin: 0.2rem 0 0 0;
            padding-left: 0.2rem;
        }

        .footer-note {
            margin-top: 2.8rem;
            border-top: 2px solid #e6edf4;
            padding-top: 2rem;
            text-align: center;
            color: #2c4c6a;
            font-weight: 500;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem 1.8rem;
        }

        .footer-note i {
            margin-right: 6px;
            color: #1f6390;
        }

        .disclaimer {
            background: #f6faff;
            padding: 1.4rem 1.8rem;
            border-radius: 18px;
            border: 1px solid #dbe7f3;
            margin: 2rem 0 0.5rem 0;
        }

        .btn-support {
            display: inline-block;
            background: #0b2b44;
            color: white;
            padding: 0.6rem 1.8rem;
            border-radius: 60px;
            font-weight: 500;
            text-decoration: none;
            transition: 0.2s;
            margin-top: 0.3rem;
        }

        .btn-support:hover {
            background: #1b4a6b;
            transform: scale(1.01);
        }

        .contact-line {
            background: #f1f7fe;
            padding: 1.2rem 1.8rem;
            border-radius: 60px;
            display: inline-block;
            margin-top: 0.5rem;
        }

        hr {
            border: none;
            border-top: 2px dashed #d3e0ed;
            margin: 1.6rem 0;
        }

        .small-meta {
            color: #3a5b78;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="policy-container">

    <!-- HEADER -->
    <div class="header">
        <h1><i class="fas fa-shield-alt" style="margin-right: 12px; color: #1b4a6b; -webkit-text-fill-color: #1b4a6b;"></i>Privacy Policy</h1>
        <span class="badge"><i class="far fa-clock"></i> Last Updated: July 2026</span>
    </div>

    <!-- ==================== PART 1 ==================== -->
    <div class="part">
        <div class="part-divider"><span>PART 1 OF 5</span><hr></div>
        <h2>Cookies and Similar Technologies</h2>
        <p>DigiWS uses cookies and similar technologies to enhance website functionality, improve user experience, maintain account security, remember user preferences, analyze website performance, and protect against fraudulent activity.</p>
        <p>Cookies are small text files stored on your device by your web browser. These files help us recognize your device and provide a more personalized browsing experience.</p>
        <p>By continuing to use DigiWS, you consent to the use of cookies as described in this Privacy Policy unless you disable them through your browser settings.</p>

        <h3>Types of Cookies We Use</h3>
        <h4>Essential Cookies</h4>
        <p>Essential cookies are necessary for the operation of our website. These cookies allow us to:</p>
        <ul>
            <li>Keep you logged into your account</li>
            <li>Maintain secure sessions</li>
            <li>Protect customer accounts</li>
            <li>Process orders</li>
            <li>Maintain shopping carts</li>
            <li>Complete checkout</li>
            <li>Prevent unauthorized access</li>
            <li>Improve website security</li>
        </ul>
        <p>Disabling essential cookies may cause parts of the website to function improperly.</p>

        <h4>Functional Cookies</h4>
        <p>Functional cookies help improve your experience by remembering your preferences. Examples include:</p>
        <ul>
            <li>Preferred Language</li>
            <li>Currency Selection</li>
            <li>Theme Preference</li>
            <li>Login Preferences</li>
            <li>Recently Viewed Products</li>
            <li>Dashboard Preferences</li>
        </ul>

        <h4>Analytics Cookies</h4>
        <p>Analytics cookies help us understand how visitors use DigiWS. These cookies may collect:</p>
        <ul>
            <li>Page Views</li>
            <li>Session Duration</li>
            <li>Device Type</li>
            <li>Browser Type</li>
            <li>Operating System</li>
            <li>Traffic Source</li>
            <li>User Navigation</li>
            <li>Click Behavior</li>
        </ul>
        <p>This information is generally collected in an aggregated form and is used to improve our website.</p>

        <h4>Security Cookies</h4>
        <p>Security cookies help us:</p>
        <ul>
            <li>Detect suspicious logins</li>
            <li>Prevent fraud</li>
            <li>Prevent spam</li>
            <li>Protect accounts</li>
            <li>Monitor unusual download activity</li>
            <li>Identify malicious requests</li>
        </ul>
        <p>These cookies are essential for protecting both DigiWS and our customers.</p>

        <h3>Third-Party Services</h3>
        <p>To provide reliable services, DigiWS works with trusted third-party providers. Depending on how you use our services, your information may be processed by third parties for limited purposes such as payment processing, content delivery, analytics, spam prevention, or infrastructure security.</p>
        <p>Examples include:</p>
        <ul>
            <li>Payment Gateway Providers</li>
            <li>Cloud Infrastructure Providers</li>
            <li>CDN Services</li>
            <li>Email Delivery Providers</li>
            <li>Analytics Providers</li>
            <li>CAPTCHA Services</li>
            <li>Fraud Prevention Services</li>
        </ul>
        <p>Each third-party provider operates under its own privacy policy.</p>

        <h3>Payment Processing</h3>
        <p>Payments on DigiWS are processed by trusted payment service providers. Depending on the payment method selected, payment providers may collect information necessary to complete your transaction.</p>
        <p>Examples include:</p>
        <ul>
            <li>Payment Reference Number</li>
            <li>Transaction ID</li>
            <li>Payment Status</li>
            <li>Billing Information</li>
            <li>Currency</li>
            <li>Payment Timestamp</li>
        </ul>
        <div class="highlight-box">
            <i class="fas fa-lock" style="margin-right: 10px; color: #1b6b9a;"></i> <strong>DigiWS does not store:</strong> Credit Card Numbers, Debit Card Numbers, CVV Codes, ATM PIN, UPI PIN, Internet Banking Passwords. Sensitive payment credentials are processed directly by the payment gateway.
        </div>

        <h3>Google Analytics</h3>
        <p>DigiWS may use Google Analytics or similar analytics platforms to understand website performance. Analytics tools may collect:</p>
        <ul>
            <li>Anonymous Usage Statistics</li>
            <li>Browser Information</li>
            <li>Device Information</li>
            <li>Country</li>
            <li>Session Duration</li>
            <li>Traffic Sources</li>
        </ul>
        <p>This information helps us improve our products and services. Where required by applicable law, consent will be obtained before using non-essential analytics cookies.</p>

        <h3>Google reCAPTCHA</h3>
        <p>To protect forms from spam and automated abuse, DigiWS may use Google reCAPTCHA or similar anti-bot technologies. These services may analyze:</p>
        <ul>
            <li>Mouse Movements</li>
            <li>Browser Information</li>
            <li>Device Information</li>
            <li>IP Address</li>
            <li>User Interaction</li>
        </ul>
        <p>This information helps identify legitimate users while reducing spam and abuse.</p>

        <h3>Cloud Security Services</h3>
        <p>DigiWS may use cloud security and content delivery providers to improve website performance and security. These services may temporarily process technical information such as:</p>
        <ul>
            <li>IP Address</li>
            <li>Request Headers</li>
            <li>Browser Type</li>
            <li>Connection Information</li>
        </ul>
        <p>This information is used solely for security, caching, and performance optimization.</p>

        <h3>Email Communications</h3>
        <p>When you register or purchase from DigiWS, you may receive emails relating to:</p>
        <ul>
            <li>Account Verification</li>
            <li>Password Reset</li>
            <li>Order Confirmation</li>
            <li>Download Information</li>
            <li>Product Updates</li>
            <li>Security Alerts</li>
            <li>Support Responses</li>
            <li>Policy Updates</li>
        </ul>
        <p>These transactional emails are necessary for providing our services.</p>
        <p>If you subscribe to our newsletter, you may also receive promotional communications. You can unsubscribe from marketing emails at any time using the unsubscribe link included in those messages.</p>

        <h3>Data Sharing</h3>
        <p>DigiWS respects your privacy. We do not sell your personal information to third parties for their own marketing purposes.</p>
        <p>We may share information only in limited situations, including:</p>
        <ul>
            <li>Payment Processing</li>
            <li>Fraud Prevention</li>
            <li>Security Services</li>
            <li>Legal Compliance</li>
            <li>Technical Support Providers</li>
            <li>Service Infrastructure</li>
        </ul>
        <p>Any sharing is limited to the extent reasonably necessary to provide or protect our services.</p>

        <h3>Legal Disclosure</h3>
        <p>We may disclose personal information when required to:</p>
        <ul>
            <li>Comply with applicable laws</li>
            <li>Respond to lawful court orders</li>
            <li>Cooperate with law enforcement where legally required</li>
            <li>Protect our legal rights</li>
            <li>Prevent fraud or abuse</li>
            <li>Enforce our Terms & Conditions</li>
            <li>Protect the safety of our customers or the public</li>
        </ul>
        <p>We disclose only the information that is reasonably necessary under the circumstances.</p>

        <h3>Data Retention</h3>
        <p>DigiWS retains information only for as long as reasonably necessary to:</p>
        <ul>
            <li>Provide our services</li>
            <li>Maintain purchase history</li>
            <li>Resolve disputes</li>
            <li>Comply with legal obligations</li>
            <li>Maintain accounting records</li>
            <li>Detect fraud</li>
            <li>Provide customer support</li>
        </ul>
        <p>The exact retention period may vary depending on the type of information and applicable legal requirements. When information is no longer required, we will delete or anonymize it where reasonably practicable, unless retention is required by law.</p>

        <h3>Data Security</h3>
        <p>Protecting customer information is one of our highest priorities. DigiWS implements reasonable technical and organizational safeguards designed to protect personal information against unauthorized access, alteration, disclosure, or destruction.</p>
        <p>These measures may include:</p>
        <ul>
            <li>SSL/TLS Encryption</li>
            <li>Secure Password Hashing</li>
            <li>Access Controls</li>
            <li>Firewall Protection</li>
            <li>Malware Monitoring</li>
            <li>Server Security</li>
            <li>Regular Security Updates</li>
            <li>Activity Monitoring</li>
            <li>Backup Systems</li>
        </ul>
        <p>While we take reasonable steps to protect your information, no method of electronic transmission or storage is completely secure. Therefore, we cannot guarantee absolute security.</p>

        <h3>International Users</h3>
        <p>DigiWS may serve customers from multiple countries. If you access our website from outside the country where our systems are operated, your information may be transferred to, stored in, or processed in jurisdictions with different data protection laws.</p>
        <p>By using DigiWS, you acknowledge that such transfers may occur as necessary to provide our services and comply with applicable laws.</p>

        <h3>Children's Privacy</h3>
        <p>DigiWS is intended for individuals who are legally capable of entering into binding agreements under applicable law. We do not knowingly collect personal information from young children.</p>
        <p>If we become aware that personal information has been collected from a child in violation of applicable law, we will take reasonable steps to delete that information. Parents or guardians who believe that such information has been provided may contact us for assistance.</p>

        <h3>Links to Third-Party Websites</h3>
        <p>Our website may contain links to third-party websites or services for your convenience. DigiWS is not responsible for the privacy practices, content, or policies of those third-party websites. We encourage users to review the privacy policies of any external websites they visit.</p>

        <h3>Business Transfers</h3>
        <p>If DigiWS undergoes a merger, acquisition, investment, asset sale, or similar business transaction, customer information may be transferred as part of that transaction, subject to applicable law and appropriate confidentiality obligations.</p>
        <p>Any successor entity will continue to protect personal information in accordance with this Privacy Policy or provide notice of material changes where required.</p>
    </div>

    <!-- ==================== PART 2 ==================== -->
    <div class="part">
        <div class="part-divider"><span>PART 2 OF 5</span><hr></div>
        <h2>Your Privacy Rights</h2>
        <p>At DigiWS, we believe every customer should have reasonable control over their personal information. Subject to applicable law, you may have the following rights regarding the personal information we maintain about you.</p>
        <p>Depending on your location and applicable regulations, these rights may include the ability to access, update, correct, delete, restrict, or object to certain processing activities.</p>
        <p>We will review and respond to valid requests within a reasonable timeframe.</p>

        <h3>Access to Your Information</h3>
        <p>You may request information regarding the personal data associated with your DigiWS account. Where applicable, you may request details including:</p>
        <ul>
            <li>Your registered account information.</li>
            <li>Purchase history.</li>
            <li>Download history.</li>
            <li>Billing records.</li>
            <li>Support requests.</li>
            <li>Product ownership records.</li>
            <li>Newsletter subscription status.</li>
            <li>Communication preferences.</li>
        </ul>
        <p>For security reasons, DigiWS may verify your identity before providing account-related information.</p>

        <h3>Correction of Information</h3>
        <p>If your personal information is inaccurate or incomplete, you may request corrections. Examples include:</p>
        <ul>
            <li>Name corrections</li>
            <li>Email address updates</li>
            <li>Company information</li>
            <li>Billing information</li>
            <li>Contact details</li>
        </ul>
        <p>Some information may also be updated directly through your DigiWS account dashboard.</p>

        <h3>Account Deletion Request</h3>
        <p>You may request deletion of your DigiWS account. Before deleting an account, please note that DigiWS may retain certain information where necessary for:</p>
        <ul>
            <li>Legal compliance</li>
            <li>Tax requirements</li>
            <li>Accounting obligations</li>
            <li>Fraud prevention</li>
            <li>Security investigations</li>
            <li>Contract enforcement</li>
            <li>Dispute resolution</li>
            <li>Protection of intellectual property</li>
        </ul>
        <p>Deleting your account does not automatically cancel completed orders or remove records that we are legally required or otherwise permitted to retain.</p>

        <h3>Purchase History</h3>
        <p>To provide customer support, software updates, download access (where applicable), and resolve disputes, DigiWS maintains purchase history associated with your account.</p>
        <p>Purchase history may include:</p>
        <ul>
            <li>Order ID</li>
            <li>Product Name</li>
            <li>Purchase Date</li>
            <li>Invoice Number</li>
            <li>Payment Status</li>
            <li>Download Status</li>
            <li>Applied Coupons</li>
            <li>Product Version</li>
            <li>Support Status</li>
        </ul>
        <p>Purchase history is retained for legitimate business, legal, and accounting purposes.</p>

        <h3>Download Records</h3>
        <p>DigiWS may maintain records of digital product downloads for purposes such as:</p>
        <ul>
            <li>Restoring download access (where available)</li>
            <li>Preventing download abuse</li>
            <li>Investigating fraud</li>
            <li>Verifying ownership</li>
            <li>Customer support</li>
            <li>Security monitoring</li>
        </ul>
        <p>Download records may include:</p>
        <ul>
            <li>Download Date</li>
            <li>Download Time</li>
            <li>Product Name</li>
            <li>Product Version</li>
            <li>IP Address</li>
            <li>Browser Information</li>
            <li>Operating System</li>
            <li>Download Attempts</li>
        </ul>
        <p>These records help us maintain the integrity of our digital marketplace.</p>

        <h3>License Information</h3>
        <p>Some DigiWS products include software licenses or activation mechanisms, while others are distributed without license keys. Where applicable, DigiWS may maintain records related to:</p>
        <ul>
            <li>License Activation</li>
            <li>License Status</li>
            <li>Activation Date</li>
            <li>Activation History</li>
            <li>Supported Domain (if applicable)</li>
            <li>Product Version</li>
            <li>License Expiration (if applicable)</li>
        </ul>
        <p>This information is used solely for legitimate business purposes such as support, updates, fraud prevention, and license administration.</p>

        <h4>Products Without License Keys</h4>
        <p>Many DigiWS products are distributed without license keys. For such products, DigiWS may still maintain records necessary to:</p>
        <ul>
            <li>Confirm legitimate purchase.</li>
            <li>Verify product ownership.</li>
            <li>Provide customer support.</li>
            <li>Investigate refund requests.</li>
            <li>Prevent fraudulent claims.</li>
            <li>Resolve download issues.</li>
        </ul>
        <p>The absence of a license key does not reduce the level of privacy protection applied to customer information.</p>

        <h3>Marketing Preferences</h3>
        <p>Customers who subscribe to DigiWS newsletters or promotional communications may receive information regarding:</p>
        <ul>
            <li>Product Updates</li>
            <li>Security Notices</li>
            <li>Discount Offers</li>
            <li>Promotional Campaigns</li>
            <li>New Product Launches</li>
            <li>Educational Content</li>
            <li>Company Announcements</li>
        </ul>
        <p>You may opt out of marketing communications at any time using the unsubscribe link included in promotional emails or by contacting DigiWS Support.</p>
        <p>Please note that service-related emails, including purchase confirmations, invoices, account security notifications, and policy updates, may still be sent as necessary to operate your account.</p>

        <h3>Security Notifications</h3>
        <p>DigiWS may send important notifications without requiring marketing consent where such notifications relate to:</p>
        <ul>
            <li>Password Changes</li>
            <li>Login Alerts</li>
            <li>Security Incidents</li>
            <li>Payment Issues</li>
            <li>Order Confirmation</li>
            <li>Download Availability</li>
            <li>Account Verification</li>
            <li>Critical Software Updates</li>
            <li>Policy Changes</li>
        </ul>
        <p>These communications are intended to protect your account and ensure reliable delivery of our services.</p>

        <h3>Data Accuracy</h3>
        <p>Customers are responsible for providing accurate, complete, and up-to-date information. Providing false or misleading information may:</p>
        <ul>
            <li>Delay order processing</li>
            <li>Prevent product delivery</li>
            <li>Affect customer support</li>
            <li>Delay refund investigations</li>
            <li>Increase fraud verification requirements</li>
        </ul>
        <p>If your information changes, we encourage you to update it promptly.</p>

        <h3>Data Protection Measures</h3>
        <p>DigiWS implements multiple security practices to help safeguard customer information, including:</p>
        <ul>
            <li>Secure HTTPS connections</li>
            <li>Encrypted password storage</li>
            <li>Access controls for authorized personnel</li>
            <li>Regular software updates</li>
            <li>Security monitoring</li>
            <li>Firewall protection</li>
            <li>Malware detection</li>
            <li>Backup procedures</li>
            <li>Administrative safeguards</li>
        </ul>
        <p>Access to customer information is limited to individuals who require it for legitimate operational purposes.</p>

        <h3>Data Breach Response</h3>
        <p>Although DigiWS employs reasonable security measures, no system can guarantee absolute protection against every potential threat. If DigiWS becomes aware of a confirmed data security incident affecting personal information, we will take reasonable steps consistent with applicable law, which may include:</p>
        <ul>
            <li>Investigating the incident.</li>
            <li>Containing the issue.</li>
            <li>Restoring affected systems.</li>
            <li>Notifying affected users where required.</li>
            <li>Cooperating with relevant authorities where legally necessary.</li>
        </ul>

        <h3>Changes to This Privacy Policy</h3>
        <p>DigiWS reserves the right to update or modify this Privacy Policy from time to time. Changes may be made to reflect:</p>
        <ul>
            <li>New legal requirements.</li>
            <li>Product improvements.</li>
            <li>Security enhancements.</li>
            <li>Operational changes.</li>
            <li>New services.</li>
            <li>Industry best practices.</li>
        </ul>
        <p>The "Last Updated" date at the beginning of this Privacy Policy indicates when the most recent revisions were made. Your continued use of DigiWS after changes become effective constitutes acceptance of the updated Privacy Policy.</p>

        <h3>Contact Us</h3>
        <p>If you have any questions, concerns, or requests regarding this Privacy Policy or your personal information, please contact DigiWS Support. Please include sufficient information to help us identify your account, such as:</p>
        <ul>
            <li>Registered Email Address</li>
            <li>Order ID (if applicable)</li>
            <li>Product Name (if applicable)</li>
            <li>Description of Your Request</li>
        </ul>
        <p>For security reasons, DigiWS may request identity verification before processing certain requests.</p>

        <h3>Privacy Commitment</h3>
        <p>Protecting your privacy is an important part of our commitment to providing secure and reliable digital products. We continually review our practices to improve data security, maintain transparency, and ensure that personal information is handled responsibly and in accordance with applicable laws.</p>
        <p>We appreciate the trust you place in DigiWS and remain committed to safeguarding your information while delivering high-quality digital products and services.</p>
    </div>

    <!-- ==================== PART 3 (FINAL) ==================== -->
    <div class="part">
        <div class="part-divider"><span>PART 3 OF 5 — FAQ & FINAL</span><hr></div>
        <h2>Frequently Asked Questions (FAQ)</h2>

        <div class="faq-item"><strong>1. Does DigiWS sell my personal information?</strong><p>No. DigiWS does not sell, rent, or trade your personal information to third parties for their own marketing purposes. Information is only shared where necessary to process payments, provide services, maintain security, comply with legal obligations, or with your consent.</p></div>
        <div class="faq-item"><strong>2. What personal information does DigiWS collect?</strong><p>Depending on how you use our services, we may collect: Name, Email Address, Username, Phone Number (if provided), Billing Information, Country, Purchase History, Download History, Device Information, Browser Information, IP Address, Support Requests. We collect only the information reasonably necessary to provide our services.</p></div>
        <div class="faq-item"><strong>3. Does DigiWS store my payment card details?</strong><p>No. Sensitive payment information such as Credit Card Number, Debit Card Number, CVV, ATM PIN, UPI PIN, Internet Banking Password is processed directly by trusted payment gateways. DigiWS does not store these credentials on its own servers.</p></div>
        <div class="faq-item"><strong>4. Why does DigiWS record my IP address?</strong><p>IP addresses may be logged for purposes such as: Fraud Prevention, Security Monitoring, Login Protection, Abuse Detection, Download Verification, Technical Troubleshooting. This helps protect both our customers and our platform.</p></div>
        <div class="faq-item"><strong>5. Does DigiWS use cookies?</strong><p>Yes. Cookies help us: Keep you logged in, Remember your preferences, Secure your account, Improve website performance, Maintain shopping carts, Analyze website usage. You may manage cookies through your browser settings, although disabling essential cookies may affect website functionality.</p></div>
        <div class="faq-item"><strong>6. Can I request a copy of my personal information?</strong><p>Subject to applicable law, you may request access to the personal information associated with your DigiWS account. Identity verification may be required before fulfilling such requests.</p></div>
        <div class="faq-item"><strong>7. Can I update my account information?</strong><p>Yes. You may update certain account information through your account dashboard or by contacting DigiWS Support.</p></div>
        <div class="faq-item"><strong>8. Can I delete my DigiWS account?</strong><p>Yes. You may request account deletion. However, DigiWS may retain certain records where required for: Tax Compliance, Accounting, Fraud Prevention, Legal Obligations, Security, Dispute Resolution. Deleting your account does not automatically remove legally required business records.</p></div>
        <div class="faq-item"><strong>9. How long does DigiWS retain my information?</strong><p>Information is retained only for as long as reasonably necessary to: Provide Services, Maintain Purchase Records, Resolve Disputes, Prevent Fraud, Meet Legal Obligations. Retention periods may vary depending on the type of information and applicable laws.</p></div>
        <div class="faq-item"><strong>10. Does DigiWS monitor downloads?</strong><p>Yes. Download activity may be recorded to verify ownership, prevent abuse, restore downloads, investigate fraud, and provide customer support.</p></div>
        <div class="faq-item"><strong>11. Does DigiWS monitor software licenses?</strong><p>Some DigiWS products include license verification while others do not. Where applicable, license information may be processed to verify ownership, enable updates, provide support, and prevent unauthorized use. Products distributed without license keys may still require proof of purchase for support or dispute resolution.</p></div>
        <div class="faq-item"><strong>12. Will DigiWS send promotional emails?</strong><p>Only where you have subscribed or otherwise provided consent, where required by law. You may unsubscribe from marketing emails at any time. Important service-related emails such as purchase confirmations, security alerts, invoices, and policy updates may still be sent.</p></div>
        <div class="faq-item"><strong>13. Does DigiWS share information with third parties?</strong><p>We may share limited information with trusted service providers necessary to: Process Payments, Deliver Emails, Prevent Fraud, Improve Security, Analyze Website Performance. We do not authorize those providers to use your information for unrelated purposes.</p></div>
        <div class="faq-item"><strong>14. How does DigiWS protect my information?</strong><p>DigiWS uses reasonable technical and organizational safeguards, which may include: SSL/TLS Encryption, Secure Password Hashing, Firewalls, Malware Protection, Access Controls, Security Monitoring, Regular Software Updates, Backup Systems. While we strive to protect your information, no online system can guarantee absolute security.</p></div>
        <div class="faq-item"><strong>15. Does DigiWS knowingly collect information from children?</strong><p>No. DigiWS is intended for individuals who are legally capable of entering into binding agreements under applicable law. If we become aware that personal information has been collected from a child in violation of applicable law, we will take reasonable steps to remove that information.</p></div>
        <div class="faq-item"><strong>16. What happens if DigiWS changes its Privacy Policy?</strong><p>We may update this Privacy Policy periodically. Material changes will be reflected by updating the "Last Updated" date, and where required by law, we will provide additional notice.</p></div>
        <div class="faq-item"><strong>17. Can DigiWS transfer my information internationally?</strong><p>Depending on how our services are hosted or supported, your information may be processed in different jurisdictions. Where applicable, DigiWS takes reasonable steps to protect personal information during such transfers.</p></div>
        <div class="faq-item"><strong>18. What if I disagree with this Privacy Policy?</strong><p>If you do not agree with this Privacy Policy, you should discontinue use of DigiWS and its services. Continued use of our website after changes become effective constitutes acceptance of the updated Privacy Policy.</p></div>
        <div class="faq-item"><strong>19. How can I contact DigiWS about privacy concerns?</strong><p>You may contact DigiWS Support regarding Privacy Questions, Data Requests, Correction Requests, Account Deletion Requests, Security Concerns. Please include your registered email address and sufficient information to help us identify your account.</p></div>
        <div class="faq-item"><strong>20. Is DigiWS committed to protecting my privacy?</strong><p>Yes. Protecting customer privacy is one of DigiWS's core commitments. We continuously improve our security practices, operational processes, and privacy safeguards to provide a secure environment for our customers.</p></div>

        <hr>

        <h2>Legal Disclaimer</h2>
        <p>This Privacy Policy describes DigiWS's general practices regarding the collection, use, storage, and protection of personal information. Nothing in this Privacy Policy limits any rights that customers may have under applicable data protection or consumer protection laws. If any provision of this Privacy Policy is determined to be unenforceable under applicable law, the remaining provisions shall remain in full force and effect.</p>

        <h2>Acceptance of this Privacy Policy</h2>
        <p>By accessing, browsing, registering an account, purchasing products, downloading digital content, contacting support, subscribing to newsletters, or otherwise using DigiWS, you acknowledge that you have read, understood, and agreed to this Privacy Policy.</p>
        <p>If you do not agree with this Privacy Policy, you should discontinue use of the DigiWS website and services.</p>

        <div class="disclaimer">
            <h3 style="margin-top: 0;">Our Commitment to Privacy</h3>
            <p>At DigiWS, we understand that trust is essential when purchasing digital products online. We are committed to:</p>
            <ul>
                <li>Protecting your personal information.</li>
                <li>Processing data responsibly.</li>
                <li>Maintaining transparency.</li>
                <li>Using information only for legitimate business purposes.</li>
                <li>Continuously improving our security practices.</li>
                <li>Respecting your privacy rights.</li>
            </ul>
            <p>We appreciate your confidence in DigiWS and remain dedicated to providing secure, reliable, and privacy-conscious digital products and services.</p>
        </div>

        <h2>Contact DigiWS</h2>
        <p>If you have any questions about this Privacy Policy or your personal information, please contact us through our official support channels.</p>
        <p>When contacting us, please include:</p>
        <ul>
            <li>Registered Email Address</li>
            <li>Order ID (if applicable)</li>
            <li>Product Name (if applicable)</li>
            <li>Description of your request</li>
        </ul>
        <p>Our team will review your request and respond within a reasonable timeframe.</p>
        <div style="margin: 1.2rem 0;">
            <span class="contact-line"><i class="fas fa-envelope" style="margin-right: 10px;"></i> support@digiws.com</span>
        </div>
        <a href="#" class="btn-support"><i class="fas fa-headset"></i> Contact Support</a>
    </div>

    <!-- FOOTER -->
    <div class="footer-note">
        <span><i class="fas fa-shield-alt"></i> Privacy First</span>
        <span><i class="fas fa-lock"></i> Secure &amp; Encrypted</span>
        <span><i class="fas fa-globe"></i> International Compliance</span>
        <span><i class="fas fa-calendar-check"></i> Policy v3.0</span>
    </div>
    <p style="text-align: center; margin-top: 0.8rem; font-size: 0.85rem; color: #4f6e8a;"><i class="far fa-copyright"></i> 2026 DigiWS — Complete Privacy Policy (Parts 1–5)</p>

</div>
<!-- end container -->
</body>
</html>