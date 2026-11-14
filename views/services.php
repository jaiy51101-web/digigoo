<!-- Services Page -->
<style>
.services-hero {
    background: linear-gradient(135deg, #0b1120 0%, #1e293b 100%);
    padding: 90px 0 100px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.services-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at center, rgba(37,99,235,0.16) 0%, transparent 60%);
    pointer-events: none;
}
.services-hero-grid {
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 30px 30px;
    background-position: center;
    opacity: 0.8;
    pointer-events: none;
}
.services-hero h1 {
    font-family: var(--font-title);
    font-size: 3.2rem;
    font-weight: 900;
    color: #ffffff;
    margin-bottom: 16px;
    letter-spacing: -1.5px;
    position: relative;
}
.services-hero p {
    font-size: 1.15rem;
    color: #94a3b8;
    max-width: 650px;
    margin: 0 auto;
    line-height: 1.6;
    position: relative;
}
.services-section {
    padding: 80px 0;
    background: var(--bg-primary);
}
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}
.service-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 40px 30px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    height: 100%;
}
.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08), 0 2px 8px rgba(15, 23, 42, 0.04);
    border-color: rgba(37, 99, 235, 0.4);
}
.service-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: linear-gradient(90deg, #2563EB, #8B5CF6);
    opacity: 0;
    transition: var(--transition);
}
.service-card:hover::after {
    opacity: 1;
}
.service-icon-wrapper {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(139, 92, 246, 0.1));
    color: var(--primary);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 24px;
    transition: var(--transition);
    border: 1px solid rgba(37, 99, 235, 0.15);
}
.service-card:hover .service-icon-wrapper {
    background: linear-gradient(135deg, #2563EB, #8B5CF6);
    color: #ffffff;
    transform: scale(1.08) rotate(3deg);
    border-color: transparent;
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
}
.service-card h3 {
    font-family: var(--font-title);
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 12px;
    letter-spacing: -0.5px;
}
.service-card p {
    font-size: 0.95rem;
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 24px;
    flex-grow: 1;
}
.service-features-list {
    list-style: none;
    padding: 0;
    margin: 0;
    border-top: 1px solid var(--border-color);
    padding-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.service-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.88rem;
    color: var(--text-secondary);
    font-weight: 600;
}
.service-feature-item i {
    color: #10B981;
    font-size: 1.05rem;
}
.services-cta-section {
    padding: 60px 0 80px;
    background: var(--bg-primary);
}
.services-cta-box {
    background: linear-gradient(135deg, #0b1120 0%, #1e293b 100%);
    border-radius: 32px;
    padding: 60px 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}
.services-cta-box::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at center, rgba(139,92,246,0.1) 0%, transparent 60%);
    pointer-events: none;
}
.services-cta-box h2 {
    font-family: var(--font-title);
    font-size: 2.2rem;
    font-weight: 900;
    color: #ffffff;
    margin-bottom: 12px;
    letter-spacing: -1px;
    position: relative;
}
.services-cta-box p {
    font-size: 1.05rem;
    color: #cbd5e1;
    max-width: 550px;
    margin: 0 auto 28px;
    line-height: 1.6;
    position: relative;
}
.btn-cta-services {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 15px 36px;
    background: linear-gradient(135deg, #2563EB, #8B5CF6);
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
    border-radius: var(--radius-full);
    text-decoration: none;
    transition: var(--transition);
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
    position: relative;
    border: none;
    cursor: pointer;
}
.btn-cta-services:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 35px rgba(37, 99, 235, 0.45);
    color: #ffffff;
}

@media (max-width: 992px) {
    .services-hero h1 {
        font-size: 2.6rem;
    }
    .services-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}
@media (max-width: 576px) {
    .services-hero {
        padding: 60px 0 70px;
    }
    .services-hero h1 {
        font-size: 2rem;
        letter-spacing: -0.8px;
    }
    .services-hero p {
        font-size: 1rem;
    }
    .services-cta-box {
        padding: 40px 20px;
        border-radius: 24px;
    }
    .services-cta-box h2 {
        font-size: 1.6rem;
    }
}
</style>

<div class="services-hero">
    <div class="services-hero-grid"></div>
    <div class="container">
        <div class="dc-reveal">
            <h1>Our Premium Web Services</h1>
            <p>High-performance web development solutions designed to scale your business. We handle the code, configuration, and launch so you can focus on growth.</p>
        </div>
    </div>
</div>

<section class="services-section">
    <div class="container">
        <div class="services-grid">
            <!-- Website Design -->
            <div class="service-card dc-reveal">
                <div class="service-icon-wrapper">
                    <i class="bi bi-palette"></i>
                </div>
                <h3>Website Design</h3>
                <p>Tailor-made, conversion-focused UI/UX layouts that reflect your brand identity and captivate your audience across all device screens.</p>
                <ul class="service-features-list">
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Modern UI/UX Wireframes</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Mobile-First Responsiveness</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> SEO-Friendly Layout Structure</li>
                </ul>
            </div>

            <!-- Website Update -->
            <div class="service-card dc-reveal" style="animation-delay: 0.1s;">
                <div class="service-icon-wrapper">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <h3>Website Update</h3>
                <p>Refresh your existing platform with state-of-the-art visual modules, structural components, copywriting updates, and modern tech stack migrations.</p>
                <ul class="service-features-list">
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Styling & Asset Refresh</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Feature Additions & Integrations</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Codebase Performance Optimization</li>
                </ul>
            </div>

            <!-- Website Installation -->
            <div class="service-card dc-reveal" style="animation-delay: 0.2s;">
                <div class="service-icon-wrapper">
                    <i class="bi bi-download"></i>
                </div>
                <h3>Website Installation</h3>
                <p>Flawless setup, config tuning, and live deployment of premium scripts, themes, and complex templates directly onto your server host.</p>
                <ul class="service-features-list">
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Hosting & Domain Connection</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Script Config & DB Binding</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> HTTPS & Security Setup</li>
                </ul>
            </div>

            <!-- Custom Code Design -->
            <div class="service-card dc-reveal" style="animation-delay: 0.3s;">
                <div class="service-icon-wrapper">
                    <i class="bi bi-code-slash"></i>
                </div>
                <h3>Custom Code Design</h3>
                <p>Fully bespoke web applications built from scratch with clean, semantic markup, matching your exact specifications and feature requirements.</p>
                <ul class="service-features-list">
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Tailored App Architecture</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> API Integrations & Database Logic</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Clean, Documented Source Files</li>
                </ul>
            </div>

            <!-- Bug & Error Fixing -->
            <div class="service-card dc-reveal" style="animation-delay: 0.4s;">
                <div class="service-icon-wrapper">
                    <i class="bi bi-bug"></i>
                </div>
                <h3>Website Error & Bug Fixing</h3>
                <p>Rapid diagnostics and code repair to patch critical errors, database connection failures, script glitches, and server-side breaks.</p>
                <ul class="service-features-list">
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Rapid Diagnostic Auditing</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> PHP & JS Bug Resolution</li>
                    <li class="service-feature-item"><i class="bi bi-check-circle-fill"></i> Security Patches & Lockdowns</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="services-cta-section">
    <div class="container">
        <div class="services-cta-box dc-reveal">
            <h2>Ready to Start Your Project?</h2>
            <p>Connect with our expert developers today. Share your requirements and get a professional timeline and customized quotation.</p>
            <a href="<?= url('contact') ?>" class="btn-cta-services">
                <i class="bi bi-chat-dots-fill"></i> Discuss Your Project
            </a>
        </div>
    </div>
</section>
