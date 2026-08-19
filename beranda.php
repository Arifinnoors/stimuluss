<?php require_once 'includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="<?= get_language() ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="STIMULUS — Sistem Informasi Monitoring Layanan UTTP & Standar Ukuran. Platform digital Kementerian Perdagangan RI untuk pengelolaan verifikasi dan penerbitan SKVI.">
<title>STIMULUS — Sistem Informasi Monitoring Layanan UTTP &amp; Standar Ukuran</title>
<link rel="icon" href="assets/logo pt.png" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap&font-display=swap">
<link rel="stylesheet" href="assets/css/style.css?v=10">
<style>
/* ====== Landing Page — Beranda STIMULUS ====== */

/* --- Skip to Content (Accessibility) --- */
.skip-link {
    position: absolute; top: -100%; left: 16px; z-index: 10000;
    background: var(--navy-900); color: #fff; padding: 12px 24px;
    border-radius: 0 0 10px 10px; font-weight: 600; font-size: 14px;
    transition: top 0.2s ease;
}
.skip-link:focus { top: 0; text-decoration: none; color: #fff; }

/* --- Navbar --- */
.landing-nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    background: rgba(250, 248, 245, 0.92);
    backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid #E8E4DF;
    transition: background 0.3s ease, box-shadow 0.3s ease;
}
.landing-nav.scrolled {
    background: rgba(250, 248, 245, 0.98);
    box-shadow: 0 2px 12px rgba(15, 26, 46, 0.08);
}
.landing-nav-inner {
    max-width: 1140px; margin: 0 auto; padding: 14px 24px;
    display: flex; align-items: center; justify-content: space-between;
}
.landing-nav .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.landing-nav .brand-logo-pt {
    width: 32px; height: 32px; border-radius: 8px; object-fit: cover; flex-shrink: 0;
}
.landing-nav .brand-logo-kemper {
    height: 28px; width: auto; flex-shrink: 0; opacity: 0.9;
}
.landing-nav .brand-text strong { color: var(--navy-900); font-size: 15px; }
.landing-nav-links { display: flex; align-items: center; gap: 6px; }
.landing-nav-links a {
    color: var(--ink-600); text-decoration: none; font-size: 13.5px; font-weight: 500;
    transition: color 0.2s ease, background 0.3s cubic-bezier(0.4,0,0.2,1), transform 0.3s cubic-bezier(0.4,0,0.2,1);
    position: relative; padding: 6px 16px; border-radius: 8px;
    overflow: hidden;
}
.landing-nav-links a::after {
    content: ''; position: absolute; bottom: 1px; left: 50%; width: 0; height: 2px;
    background: var(--navy-900); border-radius: 2px; transition: width 0.3s ease, left 0.3s ease;
}
.landing-nav-links a:hover { color: var(--navy-900); }
.landing-nav-links a:hover::after { width: 60%; left: 20%; }

/* Active / clicked state */
.landing-nav-links a.nav-active {
    color: var(--navy-900); background: var(--navy-100); font-weight: 700;
    box-shadow: 0 1px 4px rgba(15, 26, 46, 0.08);
}
.landing-nav-links a.nav-active::after { width: 0; }

/* Click ripple effect */
.landing-nav-links a .nav-ripple {
    position: absolute; border-radius: 50%;
    background: rgba(15, 26, 46, 0.08); pointer-events: none;
    transform: scale(0); animation: navRipple 0.5s cubic-bezier(0.4,0,0.2,1) forwards;
}
@keyframes navRipple {
    0%   { transform: scale(0); opacity: 1; }
    100% { transform: scale(3.5); opacity: 0; }
}

@keyframes navPop {
    0%   { transform: scale(1); }
    40%  { transform: scale(0.92); }
    70%  { transform: scale(1.08); }
    100% { transform: scale(1); }
}
.landing-nav-links a.nav-pop { animation: navPop 0.35s cubic-bezier(0.4,0,0.2,1); }

/* Language dropdown */
.lang-dropdown { position: relative; flex-shrink: 0; margin-right: 4px; }
.lang-trigger {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 12px; border-radius: 10px;
    border: 1px solid rgba(15,26,46,0.12); background: rgba(255,255,255,0.9);
    cursor: pointer; font-size: 13px; font-weight: 600;
    color: var(--navy-900); font-family: 'Inter', sans-serif;
    transition: all 0.25s cubic-bezier(.4,0,.2,1); white-space: nowrap;
}
.lang-trigger:hover { border-color: var(--teal-500); background: rgba(42,161,152,0.06); }
.lang-trigger .lang-flag { line-height: 1; display: inline-flex; align-items: center; }
.lang-trigger .lang-flag img { border-radius: 2px; object-fit: cover; }
.lang-trigger .lang-chevron {
    width: 14px; height: 14px;
    transition: transform 0.3s cubic-bezier(.4,0,.2,1); flex-shrink: 0;
}
.lang-dropdown.open .lang-chevron { transform: rotate(180deg); }
.lang-menu {
    position: absolute; top: calc(100% + 6px); right: 0;
    min-width: 210px; background: #fff;
    border: 1px solid rgba(15,26,46,0.08); border-radius: 14px;
    box-shadow: 0 12px 40px rgba(15,26,46,0.12), 0 2px 8px rgba(15,26,46,0.06);
    padding: 6px; z-index: 200;
    opacity: 0; visibility: hidden;
    transform: translateY(-8px) scale(0.96);
    transition: all 0.25s cubic-bezier(.4,0,.2,1);
    transform-origin: top right;
}
.lang-dropdown.open .lang-menu {
    opacity: 1; visibility: visible; transform: translateY(0) scale(1);
}
.lang-option {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 10px;
    text-decoration: none; color: var(--navy-900);
    font-size: 13.5px; font-weight: 500;
    transition: all 0.2s ease; cursor: pointer;
}
.lang-option:hover { background: rgba(15,26,46,0.04); }
.lang-option.active { background: rgba(42,161,152,0.08); color: var(--teal-600); font-weight: 700; }
.lang-option .lang-flag { line-height: 1; display: inline-flex; align-items: center; }
.lang-option .lang-flag img { border-radius: 2px; object-fit: cover; }
.lang-option .lang-check { margin-left: auto; color: var(--teal-600); font-weight: 700; font-size: 14px; }

/* --- Hero Section --- */
.hero {
    background: url('assets/banner gedung.png') center center / cover no-repeat;
    color: #fff; padding: 80px 24px 70px;
    position: relative; overflow: hidden;
}
.hero::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(15, 26, 46, 0.88) 0%, rgba(27, 45, 79, 0.82) 50%, rgba(42, 161, 152, 0.72) 100%);
    z-index: 1;
}
.hero::after {
    content: ''; position: absolute; bottom: -2px; left: 0; width: 100%; height: 4px;
    background: var(--bg);
}
.hero-float-1 {
    position: absolute; width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
    top: 10%; left: -5%; animation: heroFloat 12s ease-in-out infinite;
}
.hero-float-2 {
    position: absolute; width: 150px; height: 150px; border-radius: 50%;
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
    bottom: 15%; right: 5%; animation: heroFloat 15s ease-in-out infinite reverse;
}
.hero-float-3 {
    position: absolute; width: 80px; height: 80px; border-radius: 50%;
    background: rgba(232, 197, 71, 0.08);
    top: 30%; right: 15%; animation: heroFloat 10s ease-in-out infinite 2s;
}
@keyframes heroFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(15px, -10px) scale(1.03); }
    66% { transform: translate(-10px, 8px) scale(0.97); }
}
.hero-inner { max-width: 1060px; margin: 0 auto; position: relative; z-index: 2; }

.hero-grid {
    display: grid; grid-template-columns: 320px 1fr; gap: 40px; align-items: center;
}
.hero-logo-wrap {
    display: flex; flex-direction: column; align-items: center;
    animation: fadeSlideDown 0.8s cubic-bezier(0.4,0,0.2,1) 0.05s both;
}
.hero-logo-img {
    width: 220px; height: 220px; object-fit: contain;
    filter: drop-shadow(0 8px 32px rgba(0,0,0,0.2));
    border-radius: 50%; transition: transform 0.4s ease;
}
.hero-logo-img:hover { transform: scale(1.05) rotate(-2deg); }
.hero-logo-caption {
    margin-top: 14px; font-size: 11px; color: rgba(255,255,255,0.7);
    text-transform: uppercase; letter-spacing: 1px; text-align: center; font-weight: 600;
}
.hero-content { text-align: left; }
.hero h1 {
    font-size: 42px; font-weight: 800; margin: 0 0 12px; letter-spacing: -0.5px; line-height: 1.15;
    animation: fadeSlideDown 0.8s cubic-bezier(0.4,0,0.2,1) 0.1s both;
}
.hero h1 .highlight {
    background: linear-gradient(120deg, #E8C547 0%, #FFF3D0 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.hero-sub {
    font-size: 16px; color: rgba(255,255,255,0.9); margin: 0 0 32px; line-height: 1.7; max-width: 560px;
    animation: fadeSlideDown 0.8s cubic-bezier(0.4,0,0.2,1) 0.2s both;
}
.hero-actions {
    display: flex; gap: 14px; flex-wrap: wrap;
    animation: fadeSlideDown 0.8s cubic-bezier(0.4,0,0.2,1) 0.3s both;
}
.hero-actions .btn { padding: 14px 32px; font-size: 15px; border-radius: 12px; font-weight: 700; }
.btn-hero-primary {
    background: #fff; color: var(--navy-900); border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.btn-hero-primary:hover {
    transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    text-decoration: none;
}
.btn-hero-secondary {
    background: rgba(255,255,255,0.1); color: #fff;
    border: 1.5px solid rgba(255,255,255,0.35);
    transition: all 0.25s ease;
}
.btn-hero-secondary:hover {
    background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.6);
    text-decoration: none;
}

/* --- Section --- */
.landing-section {
    max-width: 1060px; margin: 0 auto; padding: 64px 24px;
    scroll-margin-top: 80px;
    position: relative; z-index: 1;
}
.section-header { text-align: center; margin-bottom: 52px; }
.section-label {
    display: inline-block; font-size: 11.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.2px; color: var(--teal-600); background: var(--teal-100);
    padding: 5px 16px; border-radius: 999px; margin-bottom: 14px;
}
.section-header h2 { font-size: 30px; font-weight: 800; color: var(--ink-900); margin: 0 0 12px; }
.section-header p { font-size: 15px; color: var(--ink-600); max-width: 600px; margin: 0 auto; line-height: 1.6; }

/* --- What is STIMULUS --- */
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 44px; align-items: center; }
.about-text h3 { font-size: 24px; font-weight: 700; color: var(--ink-900); margin: 0 0 16px; }
.about-text p { font-size: 14.5px; color: var(--ink-600); line-height: 1.75; margin: 0 0 18px; }
.about-text ul { list-style: none; padding: 0; margin: 12px 0 0; }
.about-text ul li { display: flex; align-items: flex-start; gap: 12px; font-size: 14px; color: var(--ink-600); margin-bottom: 12px; line-height: 1.55; }
.about-text ul li .check-icon {
    flex-shrink: 0; width: 24px; height: 24px;
    background: linear-gradient(135deg, var(--green-100), #BBF7D0);
    color: var(--green-600); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800; margin-top: 1px;
}
.about-visual {
    background: linear-gradient(135deg, var(--teal-50), var(--teal-100));
    border: 1px solid var(--teal-100); border-radius: 20px;
    padding: 40px 32px; text-align: center; position: relative; overflow: hidden;
}
.about-visual::before {
    content: ''; position: absolute; top: -30px; right: -30px;
    width: 100px; height: 100px; border-radius: 50%; background: rgba(42,161,152,0.08);
}
.about-visual .big-icon {
    width: 88px; height: 88px;
    background: linear-gradient(135deg, var(--navy-700), var(--teal-500));
    border-radius: 22px; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px; font-size: 40px; color: #fff;
    box-shadow: 0 12px 36px rgba(27, 45, 79, 0.28); transition: transform 0.3s ease;
}
.about-visual:hover .big-icon { transform: scale(1.05) rotate(-3deg); }
.about-visual h4 { font-size: 17px; font-weight: 700; color: var(--navy-900); margin: 0 0 8px; }
.about-visual p { font-size: 13.5px; color: var(--ink-600); margin: 0; line-height: 1.65; }

/* --- Building Illustration --- */
.building-showcase {
    text-align: center; padding: 20px 24px 20px;
    position: relative; z-index: 1; overflow: visible;
}
.building-showcase img { width: 100%; max-width: 440px; height: auto; display: block; margin: 0 auto; }
.building-showcase.reveal-building {
    opacity: 0; transform: translateY(30px);
    transition: opacity 0.7s cubic-bezier(0.4,0,0.2,1), transform 0.7s cubic-bezier(0.4,0,0.2,1);
}
.building-showcase.reveal-building.visible { opacity: 1; transform: translateY(0); }

/* --- Roles Section (Carousel) --- */
.roles-carousel {
    position: relative;
    padding: 0 48px;
}
.carousel-track {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding: 10px 0 20px;
    scroll-behavior: smooth;
}
.carousel-track::-webkit-scrollbar { display: none; }
.carousel-track .role-card {
    flex: 0 0 calc(33.333% - 14px);
    min-width: 200px;
    scroll-snap-align: start;
}
.role-card {
    background: var(--white); border: 1px solid var(--border); border-radius: 20px;
    padding: 36px 20px 30px; text-align: center;
    transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
    box-shadow: var(--shadow); position: relative; overflow: hidden; cursor: default;
}
.role-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; opacity: 0; transition: opacity 0.4s ease; }
.role-card::after { content: ''; position: absolute; bottom: -60px; left: 50%; transform: translateX(-50%); width: 120px; height: 120px; border-radius: 50%; opacity: 0; transition: opacity 0.4s ease, bottom 0.4s ease; }
.role-card:hover { transform: translateY(-8px); box-shadow: 0 16px 48px rgba(0,0,0,0.1); border-color: transparent; }
.role-card:hover::before { opacity: 1; }
.role-card:hover::after { opacity: 1; bottom: -40px; }

/* Arrows */
.carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(calc(-50% - 10px));
    width: 40px; height: 40px;
    border-radius: 50%;
    background: var(--white);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-md);
    font-size: 22px;
    color: var(--ink-900);
    cursor: pointer;
    z-index: 5;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.25s cubic-bezier(.4,0,.2,1);
    line-height: 1;
}
.carousel-arrow:hover {
    background: var(--teal-500);
    color: #fff;
    border-color: var(--teal-500);
    box-shadow: 0 4px 16px rgba(42,161,152,0.3);
    transform: translateY(calc(-50% - 10px)) scale(1.1);
}
.carousel-prev { left: 0; }
.carousel-next { right: 0; }
.carousel-arrow:active { transform: translateY(calc(-50% - 10px)) scale(0.95); }
.carousel-arrow:disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

/* Dots */
.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 8px;
}
.carousel-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--border);
    border: none;
    cursor: pointer;
    padding: 0;
    transition: all 0.3s ease;
}
.carousel-dot.active {
    background: var(--teal-500);
    width: 24px;
    border-radius: 4px;
}

.role-icon {
    width: 64px; height: 64px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px; position: relative; z-index: 1;
    transition: transform 0.4s cubic-bezier(0.4,0,0.2,1), box-shadow 0.4s ease;
}
.role-icon svg { width: 30px; height: 30px; }
.role-card:hover .role-icon { transform: translateY(-4px) scale(1.08); }

.role-card h4 { font-size: 15px; font-weight: 700; color: var(--ink-900); margin: 0 0 8px; }
.role-card p { font-size: 12.5px; color: var(--ink-500); margin: 0; line-height: 1.6; }

/* Per-role navy-gold-teal gradients */
.r-uml { background: linear-gradient(135deg, #15233D, #1B2D4F); box-shadow: 0 6px 20px rgba(21,35,61,0.3); }
.r-uml .role-card::before { background: linear-gradient(90deg, transparent, #15233D, transparent); }
.r-uml .role-card::after { background: radial-gradient(circle, rgba(21,35,61,0.08) 0%, transparent 70%); }
.r-uml .role-card:hover { box-shadow: 0 16px 48px rgba(21,35,61,0.18); }
.r-uml .role-card:hover .role-icon { box-shadow: 0 8px 24px rgba(21,35,61,0.4); }

.r-koord { background: linear-gradient(135deg, #1A8A7D, #2AA198); box-shadow: 0 6px 20px rgba(26,138,125,0.3); }
.r-koord .role-card::before { background: linear-gradient(90deg, transparent, #1A8A7D, transparent); }
.r-koord .role-card::after { background: radial-gradient(circle, rgba(26,138,125,0.08) 0%, transparent 70%); }
.r-koord .role-card:hover { box-shadow: 0 16px 48px rgba(26,138,125,0.18); }
.r-koord .role-card:hover .role-icon { box-shadow: 0 8px 24px rgba(26,138,125,0.4); }

.r-verify { background: linear-gradient(135deg, #9A6E00, #D4A017); box-shadow: 0 6px 20px rgba(154,110,0,0.3); }
.r-verify .role-card::before { background: linear-gradient(90deg, transparent, #9A6E00, transparent); }
.r-verify .role-card::after { background: radial-gradient(circle, rgba(154,110,0,0.08) 0%, transparent 70%); }
.r-verify .role-card:hover { box-shadow: 0 16px 48px rgba(154,110,0,0.18); }
.r-verify .role-card:hover .role-icon { box-shadow: 0 8px 24px rgba(154,110,0,0.4); }

.r-kt { background: linear-gradient(135deg, #0F1A2E, #243B65); box-shadow: 0 6px 20px rgba(15,26,46,0.3); }
.r-kt .role-card::before { background: linear-gradient(90deg, transparent, #0F1A2E, transparent); }
.r-kt .role-card::after { background: radial-gradient(circle, rgba(15,26,46,0.08) 0%, transparent 70%); }
.r-kt .role-card:hover { box-shadow: 0 16px 48px rgba(15,26,46,0.18); }
.r-kt .role-card:hover .role-icon { box-shadow: 0 8px 24px rgba(15,26,46,0.4); }

.r-dir { background: linear-gradient(135deg, #6B4A00, #D4A017); box-shadow: 0 6px 20px rgba(107,74,0,0.3); }
.r-dir .role-card::before { background: linear-gradient(90deg, transparent, #6B4A00, transparent); }
.r-dir .role-card::after { background: radial-gradient(circle, rgba(107,74,0,0.08) 0%, transparent 70%); }
.r-dir .role-card:hover { box-shadow: 0 16px 48px rgba(107,74,0,0.18); }
.r-dir .role-card:hover .role-icon { box-shadow: 0 8px 24px rgba(107,74,0,0.4); }

.role-card { opacity: 1; transform: none; }

/* --- FAQ Section --- */
.faq-list { max-width: 720px; margin: 0 auto; }
.faq-item {
    border: 1px solid var(--border); border-radius: 14px;
    margin-bottom: 12px; overflow: hidden;
    transition: box-shadow 0.4s ease, border-color 0.4s ease, transform 0.3s ease;
    background: var(--white);
}
.faq-item:hover { box-shadow: var(--shadow-md); }
.faq-item[open] { border-color: rgba(42,161,152,0.2); box-shadow: 0 6px 24px rgba(42,161,152,0.08); }
.faq-question {
    width: 100%; background: transparent; border: none; padding: 20px 24px;
    text-align: left; font-size: 15px; font-weight: 600; color: var(--ink-900);
    cursor: pointer; display: flex; align-items: center; justify-content: space-between;
    gap: 16px; font-family: inherit; list-style: none;
    transition: color 0.3s ease, background 0.3s ease;
}
.faq-question::-webkit-details-marker { display: none; }
.faq-question::marker { content: ''; }
.faq-question:hover { background: rgba(42,161,152,0.03); }
.faq-item[open] .faq-question { color: var(--teal-600); }
.faq-question .faq-icon {
    font-size: 20px; color: var(--teal-600); flex-shrink: 0;
    width: 30px; height: 30px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    background: var(--teal-100); font-weight: 700;
    transition: transform 0.4s cubic-bezier(.34,1.56,.64,1), background 0.35s ease, color 0.35s ease, box-shadow 0.35s ease;
}
.faq-item[open] .faq-question .faq-icon {
    transform: rotate(45deg);
    background: var(--teal-500); color: #fff;
    box-shadow: 0 2px 10px rgba(42,161,152,0.3);
}
.faq-answer {
    font-size: 14.5px; color: var(--ink-600); line-height: 1.8;
    overflow: hidden;
}
.faq-answer-inner {
    padding: 0 24px 22px;
}

/* --- Keyframes --- */
@keyframes fadeSlideDown {
    from { opacity: 0; transform: translateY(-22px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* --- Scroll reveal --- */
.reveal {
    opacity: 0; transform: translateY(32px);
    transition: opacity 0.7s cubic-bezier(0.4,0,0.2,1), transform 0.7s cubic-bezier(0.4,0,0.2,1);
}
.reveal.visible { opacity: 1; transform: translateY(0); }

/* --- Responsive --- */
@media (max-width: 768px) {
    .landing-nav-inner { padding: 10px 14px; gap: 8px; }
    .landing-nav .brand { gap: 6px; }
    .landing-nav .brand-text { display: none; }
    .landing-nav .brand-logo-kemper { height: 22px; }
    .landing-nav .brand-logo-pt { width: 26px; height: 26px; }
    .landing-nav-links { gap: 4px; }
    .landing-nav-links a { font-size: 12.5px; padding: 5px 10px; }
    .lang-trigger { padding: 6px 10px; font-size: 12px; }
    .lang-trigger .lang-flag img { width: 15px; height: 10px; }
    .lang-menu { min-width: 180px; }
    .hero { padding: 100px 20px 50px; }
    .hero-grid { grid-template-columns: 1fr; text-align: center; }
    .hero-logo-wrap { order: -1; }
    .hero-logo-img { width: 160px; height: 160px; }
    .hero-content { text-align: center; }
    .hero h1 { font-size: 28px; }
    .hero-sub { font-size: 14.5px; max-width: 100%; margin-left: auto; margin-right: auto; }
    .hero-actions { justify-content: center; }
    .about-grid { grid-template-columns: 1fr; gap: 28px; }
    .roles-carousel { padding: 0 40px; }
    .carousel-track .role-card { flex: 0 0 calc(50% - 10px); min-width: 170px; }
    .carousel-arrow { width: 34px; height: 34px; font-size: 18px; }
    .landing-section { padding: 44px 16px; }
    .section-header h2 { font-size: 24px; }
}
@media (max-width: 480px) {
    .landing-nav-inner { padding: 8px 10px; }
    .landing-nav .brand-logo-kemper { height: 18px; }
    .landing-nav .brand-logo-pt { width: 22px; height: 22px; }
    .landing-nav-links { gap: 2px; }
    .landing-nav-links a { font-size: 11.5px; padding: 4px 7px; border-radius: 6px; }
    .lang-dropdown { margin-right: 2px; }
    .lang-trigger { padding: 5px 8px; font-size: 11px; gap: 5px; }
    .lang-trigger .lang-flag img { width: 13px; height: 9px; }
    .hero { padding: 80px 16px 40px; }
    .hero h1 { font-size: 22px; }
    .hero-sub { font-size: 13.5px; }
    .hero-actions .btn { padding: 12px 24px; font-size: 14px; }
    .hero-logo-img { width: 130px; height: 130px; }
    .hero-logo-caption { font-size: 10px; }
    .roles-carousel { padding: 0 32px; }
    .carousel-track .role-card { flex: 0 0 calc(80% - 10px); min-width: 160px; }
    .carousel-arrow { width: 30px; height: 30px; font-size: 16px; }
    .hero-actions { flex-direction: column; align-items: center; }
    .about-text h3 { font-size: 20px; }
    .section-header h2 { font-size: 20px; }
}
</style>
</head>
<body>

<script>window._t=<?= t_json() ?>;</script>

<!-- Skip to Content (Accessibility) -->
<a href="#main-content" class="skip-link"><?= t('nav.langsung_konten') ?></a>

<!-- Navbar -->
<nav class="landing-nav" id="landingNav" aria-label="Navigasi utama">
    <div class="landing-nav-inner">
        <a href="beranda.php" class="brand" aria-label="STIMULUS — Beranda">
            <img src="assets/Logo Kemper RI.webp" alt="Logo Kementerian Perdagangan Republik Indonesia" class="brand-logo-kemper">
            <img src="assets/logo pt.png" alt="Logo STIMULUS" class="brand-logo-pt">
        </a>
        <div class="landing-nav-links" role="navigation" aria-label="Menu utama">
            <div class="lang-dropdown" id="langDropdownLanding">
                <button class="lang-trigger" id="langTriggerLanding" aria-haspopup="true" aria-expanded="false">
                    <span class="lang-flag"><img src="assets/<?= get_language() === 'id' ? 'Flag_of_Indonesia_(physical_version).svg.webp' : 'Flag_of_the_United_Kingdom_(3-5).svg' ?>" alt="<?= strtoupper(get_language()) ?>" width="18" height="12"></span>
                    <span class="lang-label"><?= get_language() === 'id' ? t('lang.id') : t('lang.en') ?></span>
                    <svg class="lang-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="lang-menu" id="langMenuLanding">
                    <a href="#" data-lang="id" class="lang-option <?= get_language() === 'id' ? 'active' : '' ?>">
                        <span class="lang-flag"><img src="assets/Flag_of_Indonesia_(physical_version).svg.webp" alt="ID" width="20" height="14"></span>
                        <span><?= t('lang.id') ?></span>
                        <?php if (get_language() === 'id'): ?><span class="lang-check">✓</span><?php endif; ?>
                    </a>
                    <a href="#" data-lang="en" class="lang-option <?= get_language() === 'en' ? 'active' : '' ?>">
                        <span class="lang-flag"><img src="assets/Flag_of_the_United_Kingdom_(3-5).svg" alt="EN" width="20" height="14"></span>
                        <span><?= t('lang.en') ?></span>
                        <?php if (get_language() === 'en'): ?><span class="lang-check">✓</span><?php endif; ?>
                    </a>
                </div>
            </div>
            <a href="#tentang" data-t="nav.tentang"><?= t('nav.tentang') ?></a>
            <a href="#alur-kerja" data-t="nav.alur_kerja"><?= t('nav.alur_kerja') ?></a>
            <a href="#faq" data-t="nav.faq"><?= t('nav.faq') ?></a>
            <a href="#" onclick="event.preventDefault();openLoginModal()" data-t="nav.masuk"><?= t('nav.masuk') ?></a>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero" style="padding-top: 120px;" aria-label="Hero">
    <div class="hero-float-1" aria-hidden="true"></div>
    <div class="hero-float-2" aria-hidden="true"></div>
    <div class="hero-float-3" aria-hidden="true"></div>
    <div class="hero-inner">
        <div class="hero-grid">
            <div class="hero-logo-wrap">
                <img src="assets/logo pt.png" alt="Logo PT Bantjana Patakaran Pralaja Kapradanan — mitra pengembang STIMULUS" class="hero-logo-img">
                <div class="hero-logo-caption">Bantjana Patakaran Pralaja Kapradanan</div>
            </div>
            <div class="hero-content">
                <h1 data-t="hero.title"><?= t('hero.title') ?></h1>
                <p class="hero-sub" data-t="hero.subtitle">
                    <?= t('hero.subtitle') ?>
                </p>
                <div class="hero-actions">
                    <a href="#" class="btn btn-hero-primary" onclick="event.preventDefault();openLoginModal()" data-t="hero.btn_masuk"><?= t('hero.btn_masuk') ?></a>
                    <a href="#alur-kerja" class="btn btn-hero-secondary" data-t="hero.btn_alur"><?= t('hero.btn_alur') ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<main id="main-content">

<!-- Tentang -->
<div class="landing-section" id="tentang">
    <div class="about-grid reveal">
        <div class="about-text">
            <div class="section-label" data-t="about.label"><?= t('about.label') ?></div>
            <h3 data-t="about.title"><?= t('about.title') ?></h3>
            <p data-t="about.desc1">
                <?= t('about.desc1') ?>
            </p>
            <p data-t="about.desc2">
                <?= t('about.desc2') ?>
            </p>
            <ul>
                <li><span class="check-icon">✓</span> <span data-t="about.feat1"><?= t('about.feat1') ?></span></li>
                <li><span class="check-icon">✓</span> <span data-t="about.feat2"><?= t('about.feat2') ?></span></li>
                <li><span class="check-icon">✓</span> <span data-t="about.feat3"><?= t('about.feat3') ?></span></li>
                <li><span class="check-icon">✓</span> <span data-t="about.feat4"><?= t('about.feat4') ?></span></li>
                <li><span class="check-icon">✓</span> <span data-t="about.feat5"><?= t('about.feat5') ?></span></li>
            </ul>
        </div>
        <div class="about-visual" aria-hidden="true">
            <div class="big-icon">📋</div>
            <h4 data-t="about.skvi_title"><?= t('about.skvi_title') ?></h4>
            <p data-t="about.skvi_desc"><?= t('about.skvi_desc') ?></p>
        </div>
    </div>
</div>

<!-- Building Illustration -->
<div class="building-showcase" id="buildingShowcase">
    <img src="assets/gedung ilustrasi final.png" alt="Ilustrasi Gedung Kementerian Perdagangan Republik Indonesia">
</div>

<!-- Role Section -->
<div class="landing-section" style="padding-top: 0;" id="alur-kerja">
    <div class="section-header reveal">
        <div class="section-label" data-t="roles.label"><?= t('roles.label') ?></div>
        <h2 data-t="roles.title"><?= t('roles.title') ?></h2>
        <p data-t="roles.subtitle"><?= t('roles.subtitle') ?></p>
    </div>
    <div class="roles-carousel" id="rolesCarousel">
        <button class="carousel-arrow carousel-prev" id="rolesPrev" aria-label="Sebelumnya">&lsaquo;</button>
        <div class="carousel-track" id="rolesTrack">
            <div class="role-card">
                <div class="role-icon r-uml" role="img" aria-label="Ikon Unit Metrology Legal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                </div>
                <h4>UML</h4>
                <p data-t="roles.uml_desc"><?= t('roles.uml_desc') ?></p>
            </div>
            <div class="role-card">
                <div class="role-icon r-koord" role="img" aria-label="Ikon Koordinator">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
                </div>
                <h4>Koordinator</h4>
                <p data-t="roles.koord_desc"><?= t('roles.koord_desc') ?></p>
            </div>
            <div class="role-card">
                <div class="role-icon r-verify" role="img" aria-label="Ikon Verifikator">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M11 8v6"/><path d="M8 11h6"/></svg>
                </div>
                <h4>Verifikator</h4>
                <p data-t="roles.verify_desc"><?= t('roles.verify_desc') ?></p>
            </div>
            <div class="role-card">
                <div class="role-icon r-kt" role="img" aria-label="Ikon Ketua Tim">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"/></svg>
                </div>
                <h4>Ketua Tim</h4>
                <p data-t="roles.kt_desc"><?= t('roles.kt_desc') ?></p>
            </div>
            <div class="role-card">
                <div class="role-icon r-dir" role="img" aria-label="Ikon Direktur">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14,2 14,8 20,8"/><path d="M8 13h2"/><path d="M8 17h2"/><path d="M14 13h2"/><path d="M14 17h2"/></svg>
                </div>
                <h4>Direktur</h4>
                <p data-t="roles.dir_desc"><?= t('roles.dir_desc') ?></p>
            </div>
        </div>
        <button class="carousel-arrow carousel-next" id="rolesNext" aria-label="Selanjutnya">&rsaquo;</button>
        <div class="carousel-dots" id="rolesDots"></div>
    </div>

</div>

<!-- FAQ -->
<div class="landing-section" id="faq">
    <div class="section-header reveal">
        <div class="section-label" data-t="faq.label"><?= t('faq.label') ?></div>
        <h2 data-t="faq.title"><?= t('faq.title') ?></h2>
    </div>
    <div class="faq-list reveal">
        <details class="faq-item">
            <summary class="faq-question"> <span data-t="faq.q1"><?= t('faq.q1') ?></span> <span class="faq-icon">+</span></summary>
            <div class="faq-answer"><div class="faq-answer-inner" data-t="faq.a1">
                <?= t('faq.a1') ?>
            </div></div>
        </details>
        <details class="faq-item">
            <summary class="faq-question"> <span data-t="faq.q2"><?= t('faq.q2') ?></span> <span class="faq-icon">+</span></summary>
            <div class="faq-answer"><div class="faq-answer-inner" data-t="faq.a2">
                <?= t('faq.a2') ?>
            </div></div>
        </details>
        <details class="faq-item">
            <summary class="faq-question"> <span data-t="faq.q3"><?= t('faq.q3') ?></span> <span class="faq-icon">+</span></summary>
            <div class="faq-answer"><div class="faq-answer-inner" data-t="faq.a3">
                <?= t('faq.a3') ?>
            </div></div>
        </details>
        <details class="faq-item">
            <summary class="faq-question"> <span data-t="faq.q4"><?= t('faq.q4') ?></span> <span class="faq-icon">+</span></summary>
            <div class="faq-answer"><div class="faq-answer-inner" data-t="faq.a4">
                <?= t('faq.a4') ?>
            </div></div>
        </details>
        <details class="faq-item">
            <summary class="faq-question"> <span data-t="faq.q5"><?= t('faq.q5') ?></span> <span class="faq-icon">+</span></summary>
            <div class="faq-answer"><div class="faq-answer-inner" data-t="faq.a5">
                <?= t('faq.a5') ?>
            </div></div>
        </details>
    </div>
</div>

</main>

<!-- Footer (shared with dashboard) -->
<?php include __DIR__ . '/includes/footer-partial.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Language dropdown (instant switch) ──
    (function() {
        var dd = document.getElementById('langDropdownLanding');
        var trigger = document.getElementById('langTriggerLanding');
        var menu = document.getElementById('langMenuLanding');
        if (!dd || !trigger || !menu) return;

        var FLAG_ID = 'assets/Flag_of_Indonesia_(physical_version).svg.webp';
        var FLAG_EN = 'assets/Flag_of_the_United_Kingdom_(3-5).svg';

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dd.classList.toggle('open');
            trigger.setAttribute('aria-expanded', dd.classList.contains('open'));
        });
        document.addEventListener('click', function(e) {
            if (!dd.contains(e.target)) { dd.classList.remove('open'); trigger.setAttribute('aria-expanded', 'false'); }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { dd.classList.remove('open'); trigger.setAttribute('aria-expanded', 'false'); }
        });

        function switchLang(lang) {
            fetch('set_lang.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'lang=' + lang
            });
            document.querySelectorAll('[data-t]').forEach(function(el) {
                var key = el.getAttribute('data-t');
                var text = window._t[key] ? window._t[key][lang] : null;
                if (text !== null) el.innerHTML = text;
            });
            var flagImg = trigger.querySelector('.lang-flag img');
            var label = trigger.querySelector('.lang-label');
            if (flagImg) flagImg.src = lang === 'id' ? FLAG_ID : FLAG_EN;
            if (label) label.textContent = lang === 'id' ? (window._t['lang.id'] ? window._t['lang.id'].id : 'Indonesia') : (window._t['lang.en'] ? window._t['lang.en'].en : 'English');
            menu.querySelectorAll('.lang-option').forEach(function(opt) {
                var isActive = opt.getAttribute('data-lang') === lang;
                opt.classList.toggle('active', isActive);
                var check = opt.querySelector('.lang-check');
                if (isActive && !check) { var s = document.createElement('span'); s.className = 'lang-check'; s.textContent = '✓'; opt.appendChild(s); }
                else if (!isActive && check) check.remove();
            });
            document.documentElement.lang = lang;
            dd.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
        }

        menu.querySelectorAll('.lang-option[data-lang]').forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.preventDefault();
                switchLang(this.getAttribute('data-lang'));
            });
        });
    })();

    // Scroll reveal
    var reveals = document.querySelectorAll('.reveal');
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach(function(el) { observer.observe(el); });

    // Building showcase scroll reveal + parallax
    var showcase = document.getElementById('buildingShowcase');
    if (showcase) {
        showcase.classList.add('reveal-building');
        var showcaseObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    showcaseObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });
        showcaseObs.observe(showcase);

        var showcaseImg = showcase.querySelector('img');
        var currentY = 0, targetY = 0;
        function updateParallax() {
            if (!showcaseImg) return;
            var rect = showcase.getBoundingClientRect();
            var viewH = window.innerHeight;
            if (rect.top < viewH && rect.bottom > 0) {
                var progress = (viewH - rect.top) / (viewH + rect.height);
                targetY = (progress - 0.5) * 120;
            }
            currentY += (targetY - currentY) * 0.1;
            showcaseImg.style.transform = 'translateY(' + currentY.toFixed(1) + 'px)';
            requestAnimationFrame(updateParallax);
        }
        requestAnimationFrame(updateParallax);
    }

    // Roles carousel
    var track = document.getElementById('rolesTrack');
    var prevBtn = document.getElementById('rolesPrev');
    var nextBtn = document.getElementById('rolesNext');
    var dotsWrap = document.getElementById('rolesDots');
    if (track && prevBtn && nextBtn && dotsWrap) {
        var cards = track.querySelectorAll('.role-card');
        var currentIndex = 0;
        var cardGap = 20;

        function getVisibleCount() {
            var w = track.offsetWidth;
            if (w < 500) return 1;
            if (w < 768) return 2;
            return 3;
        }
        function getMaxIndex() {
            return Math.max(0, cards.length - getVisibleCount());
        }

        // Create dots
        function buildDots() {
            dotsWrap.innerHTML = '';
            var total = getMaxIndex() + 1;
            for (var i = 0; i < total; i++) {
                var dot = document.createElement('button');
                dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
                dot.setAttribute('aria-label', 'Slide ' + (i + 1));
                dot.dataset.index = i;
                dot.addEventListener('click', function() {
                    scrollToIndex(parseInt(this.dataset.index));
                });
                dotsWrap.appendChild(dot);
            }
        }

        function updateDots() {
            var dots = dotsWrap.querySelectorAll('.carousel-dot');
            dots.forEach(function(d, i) { d.classList.toggle('active', i === currentIndex); });
            // Hide/show arrows at edges
            prevBtn.style.display = currentIndex <= 0 ? 'none' : 'flex';
            nextBtn.style.display = currentIndex >= getMaxIndex() ? 'none' : 'flex';
        }

        function scrollToIndex(idx) {
            currentIndex = Math.max(0, Math.min(idx, getMaxIndex()));
            var card = cards[currentIndex];
            if (card) {
                track.scrollTo({ left: card.offsetLeft - 8, behavior: 'smooth' });
            }
            updateDots();
        }

        prevBtn.addEventListener('click', function() { scrollToIndex(currentIndex - 1); });
        nextBtn.addEventListener('click', function() { scrollToIndex(currentIndex + 1); });

        // Sync on manual scroll
        var scrollTimer;
        track.addEventListener('scroll', function() {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function() {
                var scrollLeft = track.scrollLeft;
                var minDist = Infinity, best = 0;
                cards.forEach(function(c, i) {
                    var dist = Math.abs(c.offsetLeft - 8 - scrollLeft);
                    if (dist < minDist) { minDist = dist; best = i; }
                });
                currentIndex = Math.min(best, getMaxIndex());
                updateDots();
            }, 100);
        });

        // Touch swipe (native scroll-snap handles this, but rebuild dots on resize)
        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                currentIndex = Math.min(currentIndex, getMaxIndex());
                buildDots();
                scrollToIndex(currentIndex);
            }, 200);
        });

        buildDots();
        updateDots();
    }

    // Navbar scroll effect
    var nav = document.getElementById('landingNav');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 40) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });

    // Smooth scroll with offset for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                var navHeight = nav.offsetHeight + 16;
                var targetPos = target.getBoundingClientRect().top + window.pageYOffset - navHeight;
                window.scrollTo({ top: targetPos, behavior: 'smooth' });
            }
        });
    });

    // Nav link click animation
    var navLinks = document.querySelectorAll('.landing-nav-links a');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            var el = this;
            var ripple = document.createElement('span');
            ripple.className = 'nav-ripple';
            var rect = el.getBoundingClientRect();
            var size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            el.appendChild(ripple);
            ripple.addEventListener('animationend', function() { ripple.remove(); });

            el.classList.remove('nav-pop');
            void el.offsetWidth;
            el.classList.add('nav-pop');
            el.addEventListener('animationend', function handler() {
                el.classList.remove('nav-pop');
                el.removeEventListener('animationend', handler);
            });

            if (el.getAttribute('href').charAt(0) === '#') {
                navLinks.forEach(function(l) { l.classList.remove('nav-active'); });
                el.classList.add('nav-active');
            }
        });
    });

    // Highlight active nav link on scroll
    var sections = document.querySelectorAll('[id]');
    window.addEventListener('scroll', function() {
        var scrollPos = window.scrollY + 120;
        sections.forEach(function(sec) {
            var top = sec.offsetTop;
            var height = sec.offsetHeight;
            var id = sec.getAttribute('id');
            if (scrollPos >= top && scrollPos < top + height) {
                navLinks.forEach(function(l) {
                    l.classList.remove('nav-active');
                    if (l.getAttribute('href') === '#' + id) {
                        l.classList.add('nav-active');
                    }
                });
            }
        });
    });

    // FAQ smooth open/close animation
    var faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(function(item) {
        var answer = item.querySelector('.faq-answer');
        var inner = item.querySelector('.faq-answer-inner');
        if (!answer || !inner) return;

        function animateOpen() {
            // Measure natural height from content
            answer.style.height = 'auto';
            var target = answer.scrollHeight;
            answer.style.height = '0px';
            answer.offsetHeight; // force reflow

            // Fade in inner content
            inner.style.opacity = '0';
            inner.style.transition = 'none';

            answer.style.transition = 'height 0.4s cubic-bezier(0.22, 1, 0.36, 1)';
            answer.style.height = target + 'px';

            // Fade in text after a short delay
            setTimeout(function() {
                inner.style.transition = 'opacity 0.3s ease';
                inner.style.opacity = '1';
            }, 100);

            answer.addEventListener('transitionend', function handler() {
                answer.style.height = 'auto';
                answer.style.transition = '';
                answer.removeEventListener('transitionend', handler);
            });
        }

        function animateClose() {
            // Get current height, lock it, then animate to 0
            var cur = answer.scrollHeight;
            answer.style.height = cur + 'px';
            answer.style.transition = 'none';
            answer.offsetHeight; // force reflow

            // Fade out inner content immediately
            inner.style.transition = 'opacity 0.15s ease';
            inner.style.opacity = '0';

            answer.style.transition = 'height 0.3s cubic-bezier(0.22, 1, 0.36, 1)';
            answer.style.height = '0px';

            answer.addEventListener('transitionend', function handler() {
                answer.style.transition = '';
                answer.removeEventListener('transitionend', handler);
            });
        }

        item.addEventListener('toggle', function() {
            if (item.open) {
                animateOpen();
            } else {
                animateClose();
            }
        });
    });
});
</script>

<!-- Login Modal -->
<div class="login-modal-overlay" id="loginModal" role="dialog" aria-modal="true" aria-label="<?= t('login.title') ?>">
    <div class="login-modal-card">
        <button class="login-modal-close" id="loginModalClose" aria-label="Tutup">&times;</button>
        <div class="login-modal-header">
            <div class="login-modal-logo">
                <img src="assets/Logo%20Kemper%20RI.webp" alt="Logo Kementerian Perdagangan RI" style="height:26px">
                <img src="assets/logo%20pt.png" alt="Logo STIMULUS" style="height:44px;border-radius:50%">
            </div>
            <h2 data-t="login.title"><?= t('login.title') ?></h2>
            <p data-t="login.subtitle"><?= t('login.subtitle') ?></p>
        </div>
        <form class="login-modal-form" novalidate onsubmit="return handleLoginSubmit(event)">
            <div class="login-modal-error" id="loginModalError" role="alert" style="display:none"></div>
            <div class="login-modal-field" style="animation-delay:0.15s">
                <label for="modal-email" data-t="login.email"><?= t('login.email') ?></label>
                <input type="email" id="modal-email" name="email" required placeholder="nama@instansi.go.id" autocomplete="email">
            </div>
            <div class="login-modal-field" style="animation-delay:0.25s">
                <label for="modal-password" data-t="login.password"><?= t('login.password') ?></label>
                <input type="password" id="modal-password" name="password" required placeholder="••••••••" autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block login-modal-submit" id="loginModalSubmit" data-t="login.submit"><?= t('login.submit') ?></button>
        </form>
        <details class="login-modal-demo">
            <summary data-t="login.demo_title"><?= t('login.demo_title') ?></summary>
            <table>
                <tr><td>UML</td><td>uml@demo.com</td></tr>
                <tr><td>Koordinator</td><td>koordinator@demo.com</td></tr>
                <tr><td>Verifikator</td><td>verifikator@demo.com</td></tr>
                <tr><td>Ketua Tim</td><td>ketuatim@demo.com</td></tr>
                <tr><td>Direktur</td><td>direktur@demo.com</td></tr>
            </table>
            <p style="margin:10px 0 0;color:var(--ink-400);font-size:12px;"><?= t('login.demo_hint') ?></p>
        </details>
        <div class="login-modal-footer">&copy; <?= date('Y') ?> <?= t('login.footer') ?></div>
    </div>
</div>

<style>
/* ── Login Modal ── */
.login-modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(15,26,46,0.55);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; visibility: hidden;
    transition: opacity .35s ease, visibility .35s ease;
}
.login-modal-overlay.modal-open { opacity: 1; visibility: visible; }
.login-modal-card {
    position: relative; background: #fff;
    border-radius: 18px; padding: 36px 32px 28px;
    width: 100%; max-width: 420px;
    box-shadow: 0 24px 80px rgba(15,26,46,0.25), 0 4px 20px rgba(0,0,0,0.1);
    transform: translateY(24px) scale(0.96);
    transition: transform .4s cubic-bezier(.34,1.56,.64,1), opacity .35s ease;
    opacity: 0;
}
.login-modal-overlay.modal-open .login-modal-card { transform: translateY(0) scale(1); opacity: 1; }
.login-modal-close {
    position: absolute; top: 14px; right: 16px;
    background: none; border: none; font-size: 26px; line-height: 1;
    color: var(--ink-400); cursor: pointer;
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, color .2s;
}
.login-modal-close:hover { background: var(--navy-100); color: var(--navy-900); }
.login-modal-header { text-align: center; margin-bottom: 24px; }
.login-modal-logo { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 16px; }
.login-modal-header h2 { font-size: 20px; font-weight: 800; color: var(--navy-900); margin: 0 0 4px; }
.login-modal-header p { font-size: 12.5px; color: var(--ink-400); margin: 0; }
.login-modal-form .login-modal-field { margin-bottom: 16px; animation: loginFieldIn .4s ease both; }
.login-modal-form .login-modal-field label { display: block; font-size: 12.5px; font-weight: 600; color: var(--ink-600); margin-bottom: 5px; }
.login-modal-form .login-modal-field input {
    width: 100%; padding: 10px 14px; border: 1.5px solid #ddd;
    border-radius: 10px; font-size: 14px; outline: none;
    transition: border-color .2s, box-shadow .2s; box-sizing: border-box;
}
.login-modal-form .login-modal-field input:focus {
    border-color: var(--teal-500);
    box-shadow: 0 0 0 3px rgba(42,161,152,0.15);
}
.login-modal-submit { animation: loginFieldIn .4s ease 0.35s both; margin-top: 4px; }
@keyframes loginFieldIn {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.login-modal-footer { text-align: center; margin-top: 18px; font-size: 11px; color: var(--ink-400); animation: loginFieldIn .4s ease 0.5s both; }
.login-modal-demo { margin-top: 20px; text-align: center; animation: loginFieldIn .4s ease 0.45s both; }
.login-modal-demo summary { font-size: 12.5px; color: var(--teal-600); cursor: pointer; font-weight: 500; }
.login-modal-demo table { width: 100%; margin-top: 10px; font-size: 12px; border-collapse: collapse; }
.login-modal-demo td { padding: 4px 8px; border-bottom: 1px solid var(--navy-100); }
.login-modal-demo td:first-child { font-weight: 600; color: var(--navy-900); text-align: left; }
.login-modal-demo td:last-child { color: var(--ink-400); text-align: right; font-family: monospace; font-size: 11.5px; }
.login-modal-error {
    background: var(--red-100); color: var(--red-600); border: 1px solid #FCA5A5;
    border-radius: 10px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px;
    animation: loginErrorShake 0.4s ease both;
}
@keyframes loginErrorShake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-5px); }
    40% { transform: translateX(5px); }
    60% { transform: translateX(-3px); }
    80% { transform: translateX(3px); }
}
.login-modal-submit:disabled { opacity: 0.6; cursor: not-allowed; pointer-events: none; }
.login-modal-submit .spinner {
    display: inline-block; width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff;
    border-radius: 50%; animation: loginSpin .6s linear infinite;
    vertical-align: middle; margin-right: 6px;
}
@keyframes loginSpin { to { transform: rotate(360deg); } }
</style>

<script>
(function() {
    var overlay = document.getElementById('loginModal');
    var closeBtn = document.getElementById('loginModalClose');
    var errorDiv = document.getElementById('loginModalError');
    var submitBtn = document.getElementById('loginModalSubmit');

    window.openLoginModal = function() {
        overlay.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        errorDiv.style.display = 'none';
        setTimeout(function() {
            var emailInput = document.getElementById('modal-email');
            if (emailInput) emailInput.focus();
        }, 400);
    };

    function closeLoginModal() {
        overlay.classList.remove('modal-open');
        document.body.style.overflow = '';
        errorDiv.style.display = 'none';
    }

    closeBtn.addEventListener('click', closeLoginModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeLoginModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('modal-open')) {
            closeLoginModal();
        }
    });

    overlay.addEventListener('keydown', function(e) {
        if (e.key !== 'Tab' || !overlay.classList.contains('modal-open')) return;
        var focusable = overlay.querySelectorAll('button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (!focusable.length) return;
        var first = focusable[0];
        var last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault(); last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault(); first.focus();
        }
    });

    window.handleLoginSubmit = function(e) {
        e.preventDefault();
        errorDiv.style.display = 'none';
        var email = document.getElementById('modal-email').value.trim();
        var pass  = document.getElementById('modal-password').value;
        if (!email || !pass) {
            errorDiv.textContent = 'Email dan password wajib diisi.';
            errorDiv.style.display = 'block';
            return false;
        }
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Masuk...';
        var fd = new FormData();
        fd.append('email', email);
        fd.append('password', pass);
        fetch('api_login.php', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.ok) {
                    window.location.href = data.redirect || 'dashboard.php';
                } else {
                    errorDiv.textContent = data.error || 'Terjadi kesalahan.';
                    errorDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Masuk';
                }
            })
            .catch(function() {
                errorDiv.textContent = 'Gagal terhubung ke server. Coba lagi.';
                errorDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Masuk';
            });
        return false;
    };
})();
</script>
</body>
</html>
