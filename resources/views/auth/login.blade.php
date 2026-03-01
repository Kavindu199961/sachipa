<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sachipa Curtain — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold: #d4a853;
            --gold-light: #f0c97a;
            --deep: #1a0a2e;
            --wine: #6b1a3e;
            --cream: #fdf6ec;
            --text-dark: #1a0a2e;
        }

        body {
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            background: var(--deep);
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* === BACKGROUND SCENE === */
        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        /* Room wall */
        .bg-scene::before {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(ellipse at 50% 30%, #2d1060 0%, #1a0a2e 60%),
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Window light glow */
        .window-light {
            position: absolute;
            top: -10%;
            left: 50%;
            transform: translateX(-50%);
            width: 340px;
            height: 600px;
            background: linear-gradient(180deg, rgba(212,168,83,0.18) 0%, rgba(212,168,83,0.04) 60%, transparent 100%);
            clip-path: polygon(15% 0%, 85% 0%, 100% 100%, 0% 100%);
            animation: windowFlicker 6s ease-in-out infinite;
        }

        @keyframes windowFlicker {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.75; }
        }

        /* === CURTAINS === */
        .curtains-wrap {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .curtain {
            position: absolute;
            top: 0;
            height: 100%;
            width: 30%;
            max-width: 280px;
        }

        .curtain-left {
            left: 0;
            background: linear-gradient(to right, #5c1a3a 0%, #8b2252 40%, #6b1a3e 70%, rgba(107,26,62,0.3) 100%);
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0 100%);
            animation: curtainSway 8s ease-in-out infinite;
            transform-origin: top left;
        }

        .curtain-right {
            right: 0;
            background: linear-gradient(to left, #5c1a3a 0%, #8b2252 40%, #6b1a3e 70%, rgba(107,26,62,0.3) 100%);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 15% 100%);
            animation: curtainSway 8s ease-in-out infinite reverse;
            transform-origin: top right;
        }

        @keyframes curtainSway {
            0%, 100% { transform: skewY(0deg) scaleX(1); }
            25% { transform: skewY(0.4deg) scaleX(1.01); }
            75% { transform: skewY(-0.3deg) scaleX(0.99); }
        }

        /* Curtain folds */
        .curtain-left::before, .curtain-right::before {
            content: '';
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                90deg,
                transparent 0px,
                rgba(0,0,0,0.15) 8px,
                transparent 16px,
                rgba(255,255,255,0.04) 24px,
                transparent 32px
            );
        }

        /* Gold trim */
        .curtain-left::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 4px; height: 100%;
            background: linear-gradient(180deg, var(--gold-light), var(--gold), var(--gold-light));
            box-shadow: 0 0 12px rgba(212,168,83,0.6);
        }
        .curtain-right::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: linear-gradient(180deg, var(--gold-light), var(--gold), var(--gold-light));
            box-shadow: 0 0 12px rgba(212,168,83,0.6);
        }

        /* Curtain rod */
        .curtain-rod {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 28px;
            background: linear-gradient(180deg, #b8872a 0%, #d4a853 40%, #f0c97a 55%, #d4a853 70%, #8a6020 100%);
            z-index: 2;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6), 0 2px 0 rgba(255,255,255,0.1) inset;
        }
        .curtain-rod::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 50px; height: 50px;
            background: radial-gradient(circle, #f0c97a 20%, #d4a853 50%, #8a6020 100%);
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(212,168,83,0.8);
        }

        /* Ring hooks on rod */
        .rod-rings {
            position: fixed;
            top: 12px;
            left: 0;
            width: 100%;
            z-index: 3;
            pointer-events: none;
        }
        .ring {
            position: absolute;
            width: 14px; height: 14px;
            border: 3px solid #b8872a;
            border-radius: 50%;
            background: radial-gradient(circle, #f0c97a, #d4a853);
            top: 0;
            transform: translateX(-50%);
        }

        /* Pelmet/valance */
        .pelmet {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 60px;
            background: linear-gradient(180deg, #3d0f25 0%, #6b1a3e 60%, rgba(107,26,62,0.4) 100%);
            z-index: 4;
            clip-path: polygon(0 0, 100% 0, 100% 70%, 97% 100%, 94% 70%, 91% 100%, 88% 70%, 85% 100%, 82% 70%, 79% 100%, 76% 70%, 73% 100%, 70% 70%, 67% 100%, 64% 70%, 61% 100%, 58% 70%, 55% 100%, 52% 70%, 49% 100%, 46% 70%, 43% 100%, 40% 70%, 37% 100%, 34% 70%, 31% 100%, 28% 70%, 25% 100%, 22% 70%, 19% 100%, 16% 70%, 13% 100%, 10% 70%, 7% 100%, 4% 70%, 1% 100%, 0 70%);
        }
        .pelmet::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent 20%, var(--gold) 40%, transparent 60%, var(--gold) 80%, transparent);
        }

        /* Tassel */
        .tassel {
            position: fixed;
            z-index: 5;
            top: 55%;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .tassel-left { left: 22%; }
        .tassel-right { right: 22%; }
        .tassel-rope {
            width: 3px; height: 60px;
            background: linear-gradient(180deg, var(--gold), #8a6020);
        }
        .tassel-ball {
            width: 18px; height: 18px;
            background: radial-gradient(circle at 35% 35%, var(--gold-light), var(--gold), #8a6020);
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            animation: tasselBob 4s ease-in-out infinite;
        }
        .tassel-fringe {
            display: flex; gap: 2px; margin-top: 2px;
        }
        .tassel-fringe span {
            width: 2px; height: 22px;
            background: linear-gradient(180deg, var(--gold), transparent);
            animation: tasselBob 4s ease-in-out infinite;
        }
        .tassel-fringe span:nth-child(even) { animation-delay: 0.2s; }

        @keyframes tasselBob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(6px); }
        }

        /* === FLOATING DUST PARTICLES === */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(212,168,83,0.6);
            animation: floatUp linear infinite;
        }
        @keyframes floatUp {
            0% { transform: translateY(100vh) translateX(0) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-20px) translateX(30px) scale(1); opacity: 0; }
        }

        /* === LOGIN CARD === */
        .card-wrap {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 24px 20px;
            margin-top: 30px;
            animation: cardReveal 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        @keyframes cardReveal {
            0% { opacity: 0; transform: translateY(40px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-card {
            background: rgba(253,246,236,0.97);
            border-radius: 24px;
            padding: 44px 40px 36px;
            box-shadow:
                0 40px 80px rgba(0,0,0,0.5),
                0 0 0 1px rgba(212,168,83,0.3),
                inset 0 1px 0 rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--wine), var(--gold), var(--wine), var(--gold-light), var(--wine));
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Corner ornaments */
        .corner {
            position: absolute;
            width: 40px; height: 40px;
            opacity: 0.25;
        }
        .corner-tl { top: 12px; left: 12px; border-top: 2px solid var(--gold); border-left: 2px solid var(--gold); border-radius: 6px 0 0 0; }
        .corner-tr { top: 12px; right: 12px; border-top: 2px solid var(--gold); border-right: 2px solid var(--gold); border-radius: 0 6px 0 0; }
        .corner-bl { bottom: 12px; left: 12px; border-bottom: 2px solid var(--gold); border-left: 2px solid var(--gold); border-radius: 0 0 0 6px; }
        .corner-br { bottom: 12px; right: 12px; border-bottom: 2px solid var(--gold); border-right: 2px solid var(--gold); border-radius: 0 0 6px 0; }

        .logo-area {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 64px; height: 64px;
            margin: 0 auto 12px;
            position: relative;
        }
        .logo-icon svg {
            width: 100%; height: 100%;
            filter: drop-shadow(0 4px 8px rgba(107,26,62,0.3));
        }

        .shop-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--text-dark);
            letter-spacing: 1px;
            line-height: 1.1;
        }
        .shop-title em {
            color: var(--wine);
        }
        .shop-tagline {
            font-size: 0.78rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-top: 6px;
            font-weight: 600;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 28px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(107,26,62,0.3));
        }
        .divider::after { background: linear-gradient(270deg, transparent, rgba(107,26,62,0.3)); }
        .divider-diamond {
            width: 8px; height: 8px;
            background: var(--gold);
            transform: rotate(45deg);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--wine);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.3s;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 13px 16px 13px 42px;
            border: 1.5px solid rgba(107,26,62,0.15);
            border-radius: 12px;
            background: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 0.95rem;
            color: var(--text-dark);
            transition: all 0.3s;
            outline: none;
        }
        input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212,168,83,0.15);
            background: #fffdf6;
        }
        input:focus + .focus-line { width: 100%; }

        .focus-line {
            position: absolute;
            bottom: 0; left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--wine), var(--gold), var(--wine));
            border-radius: 2px;
            transition: width 0.4s ease;
        }

        .form-check-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .check-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            color: #555;
            letter-spacing: 0;
            text-transform: none;
            font-weight: 600;
        }
        .check-label input[type="checkbox"] {
            display: none;
        }
        .custom-check {
            width: 18px; height: 18px;
            border: 2px solid rgba(107,26,62,0.3);
            border-radius: 5px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.25s;
            flex-shrink: 0;
        }
        .check-label input:checked ~ .custom-check {
            background: var(--wine);
            border-color: var(--wine);
        }
        .check-label input:checked ~ .custom-check::after {
            content: '✓';
            color: #fff;
            font-size: 11px;
            font-weight: 900;
        }
        .forgot-link {
            font-size: 0.82rem;
            color: var(--wine);
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 0;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--gold); }

        .btn-login {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--wine) 0%, #9b2560 50%, #6b1a3e 100%);
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.3s;
            box-shadow: 0 8px 24px rgba(107,26,62,0.4);
        }
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(212,168,83,0.3), transparent);
            transition: left 0.5s ease;
        }
        .btn-login:hover::before { left: 100%; }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(107,26,62,0.5);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login span { position: relative; z-index: 1; }
        .btn-login .btn-icon { margin-right: 8px; }

        .register-row {
            text-align: center;
            margin-top: 24px;
            font-size: 0.88rem;
            color: #777;
        }
        .register-row a {
            color: var(--wine);
            font-weight: 700;
            text-decoration: none;
            position: relative;
        }
        .register-row a::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 1.5px;
            background: var(--gold);
            transition: width 0.3s;
        }
        .register-row a:hover::after { width: 100%; }

        /* Invalid feedback */
        .invalid-feedback {
            display: block;
            color: #c0392b;
            font-size: 0.78rem;
            margin-top: 5px;
            font-weight: 600;
        }
        .is-invalid { border-color: #c0392b !important; }

        /* === WHATSAPP POPUP MODAL === */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            animation: fadeOverlay 0.3s ease;
        }
        .modal-overlay.active { display: flex; }

        @keyframes fadeOverlay {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-box {
            background: var(--cream);
            border-radius: 24px;
            padding: 40px 36px 32px;
            max-width: 360px;
            width: 90%;
            text-align: center;
            position: relative;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(212,168,83,0.3);
            animation: modalPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            overflow: hidden;
        }
        .modal-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #25D366, #128C7E, #25D366);
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
        }

        @keyframes modalPop {
            0% { opacity: 0; transform: scale(0.8) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-close {
            position: absolute;
            top: 12px; right: 16px;
            background: none; border: none;
            font-size: 1.4rem;
            color: #aaa;
            cursor: pointer;
            line-height: 1;
            transition: color 0.2s;
        }
        .modal-close:hover { color: var(--wine); }

        .modal-wa-icon {
            width: 72px; height: 72px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, #25D366, #128C7E);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(37,211,102,0.4);
            animation: pulsWA 2s ease-in-out infinite;
        }
        @keyframes pulsWA {
            0%, 100% { box-shadow: 0 8px 24px rgba(37,211,102,0.4); }
            50% { box-shadow: 0 8px 40px rgba(37,211,102,0.7); }
        }
        .modal-wa-icon svg { width: 40px; height: 40px; }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        .modal-msg {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .modal-msg strong {
            color: var(--wine);
            font-size: 1.05rem;
        }

        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: 'Nunito', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.3s;
            box-shadow: 0 8px 20px rgba(37,211,102,0.35);
            letter-spacing: 0.5px;
        }
        .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(37,211,102,0.5);
        }

        .modal-note {
            font-size: 0.75rem;
            color: #aaa;
            margin-top: 14px;
        }

        /* === RESPONSIVE === */
        @media (max-width: 480px) {
            .login-card { padding: 36px 24px 28px; }
            .shop-title { font-size: 1.6rem; }
            .tassel { display: none; }
            .curtain { width: 22%; }
        }
    </style>
</head>
<body>

<!-- Background -->
<div class="bg-scene">
    <div class="window-light"></div>
</div>

<!-- Particles -->
<div class="particles" id="particles"></div>

<!-- Curtains -->
<div class="curtains-wrap">
    <div class="curtain curtain-left"></div>
    <div class="curtain curtain-right"></div>
</div>

<!-- Rod & Pelmet -->
<div class="curtain-rod"></div>
<div class="pelmet"></div>

<!-- Ring hooks -->
<div class="rod-rings" id="rings"></div>

<!-- Tassels -->
<div class="tassel tassel-left">
    <div class="tassel-rope"></div>
    <div class="tassel-ball"></div>
    <div class="tassel-fringe">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
    </div>
</div>
<div class="tassel tassel-right">
    <div class="tassel-rope"></div>
    <div class="tassel-ball"></div>
    <div class="tassel-fringe">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
    </div>
</div>

<!-- Login Card -->
<div class="card-wrap">
    <div class="login-card">
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <div class="logo-area">
            <div class="logo-icon">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Curtain icon -->
                    <rect x="2" y="4" width="60" height="6" rx="3" fill="#d4a853"/>
                    <rect x="8" y="8" width="4" height="4" rx="2" fill="#8a6020"/>
                    <rect x="18" y="8" width="4" height="4" rx="2" fill="#8a6020"/>
                    <rect x="28" y="8" width="4" height="4" rx="2" fill="#8a6020"/>
                    <rect x="38" y="8" width="4" height="4" rx="2" fill="#8a6020"/>
                    <rect x="48" y="8" width="4" height="4" rx="2" fill="#8a6020"/>
                    <!-- Left drape -->
                    <path d="M6 12 Q10 28 8 44 Q10 56 14 60 L4 60 Q2 56 4 44 Q2 28 6 12Z" fill="#6b1a3e"/>
                    <path d="M6 12 Q14 28 12 44 Q14 56 20 60 L14 60 Q10 56 8 44 Q10 28 6 12Z" fill="#8b2252"/>
                    <!-- Right drape -->
                    <path d="M58 12 Q54 28 56 44 Q54 56 50 60 L60 60 Q62 56 60 44 Q62 28 58 12Z" fill="#6b1a3e"/>
                    <path d="M58 12 Q50 28 52 44 Q50 56 44 60 L50 60 Q54 56 56 44 Q54 28 58 12Z" fill="#8b2252"/>
                    <!-- Center gather -->
                    <path d="M24 12 Q28 30 30 50 Q32 58 32 60 Q32 58 34 50 Q36 30 40 12Z" fill="#6b1a3e" opacity="0.7"/>
                    <!-- Gold trim -->
                    <line x1="16" y1="12" x2="14" y2="60" stroke="#d4a853" stroke-width="1.5"/>
                    <line x1="48" y1="12" x2="50" y2="60" stroke="#d4a853" stroke-width="1.5"/>
                </svg>
            </div>
            <h1 class="shop-title"><em>Sachipa</em> Curtain</h1>
            <p class="shop-tagline">Drape Your World in Luxury</p>
        </div>

        <div class="divider"><div class="divider-diamond"></div></div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrap">
                    <span class="input-icon">✉</span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="@error('email') is-invalid @enderror"
                        placeholder="your@email.com"
                        required
                        autocomplete="email"
                    >
                    <div class="focus-line"></div>
                </div>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="@error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                    <div class="focus-line"></div>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-check-row">
                <label class="check-label">
                    <input type="checkbox" name="remember" id="remember">
                    <span class="custom-check"></span>
                    Remember me
                </label>
                <a href="#" class="forgot-link" onclick="openModal(event)">Forgot password?</a>
            </div>

            <button type="submit" class="btn-login">
                <span><span class="btn-icon">✦</span>Sign in</span>
            </button>
        </form>

    </div>
</div>

<!-- WhatsApp Contact Popup -->
<div class="modal-overlay" id="waModal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal()">✕</button>

        <div class="modal-wa-icon">
            <svg viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </div>

        <h2 class="modal-title">Contact Admin</h2>
        <p class="modal-msg">
            Please contact our admin on WhatsApp for assistance:<br><br>
            <strong>📱 070 7645303</strong>
        </p>

        <a href="https://wa.me/94707645303" target="_blank" class="btn-whatsapp">
            <svg viewBox="0 0 24 24" fill="white" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Chat on WhatsApp
        </a>

        <p class="modal-note">Tap the button to open WhatsApp directly</p>
    </div>
</div>

<script>
    // Create ring hooks on rod
    const ringsEl = document.getElementById('rings');
    const ringCount = Math.floor(window.innerWidth / 60);
    for (let i = 0; i < ringCount; i++) {
        const r = document.createElement('div');
        r.className = 'ring';
        r.style.left = ((i + 0.5) / ringCount * 100) + '%';
        ringsEl.appendChild(r);
    }

    // Create floating particles
    const particlesEl = document.getElementById('particles');
    for (let i = 0; i < 20; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random() * 3 + 1;
        p.style.cssText = `
            width: ${size}px;
            height: ${size}px;
            left: ${Math.random() * 100}%;
            animation-duration: ${Math.random() * 15 + 10}s;
            animation-delay: ${Math.random() * 10}s;
        `;
        particlesEl.appendChild(p);
    }

    // Custom checkbox interaction
    document.querySelectorAll('.custom-check').forEach(el => {
        el.addEventListener('click', () => {
            const cb = el.previousElementSibling;
            cb.checked = !cb.checked;
        });
    });

    // Modal functions
    function openModal(e) {
        if (e) e.preventDefault();
        document.getElementById('waModal').classList.add('active');
    }
    function closeModal() {
        document.getElementById('waModal').classList.remove('active');
    }
    // Close on overlay click
    document.getElementById('waModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>

</body>
</html>