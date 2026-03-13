<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark" data-theme="liquid">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Epoka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        const savedTheme = localStorage.getItem('epoka-theme') || 'liquid';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.setAttribute('data-bs-theme', savedTheme === 'liquid-light' ? 'light' : 'dark');
    </script>
</head>
<body>

<button id="themeToggleBtn" class="theme-toggle-fab" aria-label="Changer de thème">
    <span id="themeToggleIconWrapper"><i data-lucide="sun"></i></span>
</button>

<div class="login-page">

    <div class="ambient-background">
        <div class="ambient-orb orb-1"></div>
        <div class="ambient-orb orb-2"></div>
        <div class="ambient-orb orb-3"></div>
    </div>

    <div class="login-card-outer">
        <div class="login-card">

            <!-- Mascot + Logo -->
            <div class="login-card-top">
                <div class="mascot-container" id="mascotContainer">
                    <svg class="mascot-svg" viewBox="0 0 200 250" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="capGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#1a237e"/>
                                <stop offset="100%" stop-color="#283593"/>
                            </linearGradient>
                            <linearGradient id="skinGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#FDDFB0"/>
                                <stop offset="100%" stop-color="#F5C999"/>
                            </linearGradient>
                            <linearGradient id="suitGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#283593"/>
                                <stop offset="100%" stop-color="#1a237e"/>
                            </linearGradient>
                            <linearGradient id="sleeveGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#3949ab"/>
                                <stop offset="100%" stop-color="#283593"/>
                            </linearGradient>
                        </defs>

                        <g class="mascot-torso">
                            <path d="M 40,165 Q 20,180 10,250 L 190,250 Q 180,180 160,165 Q 130,195 100,195 Q 70,195 40,165 Z" fill="url(#suitGrad)" stroke="#1a237e" stroke-width="2"/>
                            <path d="M 70,175 L 100,210 L 130,175 Z" fill="#ffffff" stroke="#e0e0e0" stroke-width="1"/>
                            <path d="M 94,190 L 106,190 L 103,235 L 100,240 L 97,235 Z" fill="#d32f2f"/>
                            <path d="M 70,175 L 94,190 L 80,205 Z" fill="#ffffff"/>
                            <path d="M 130,175 L 106,190 L 120,205 Z" fill="#ffffff"/>
                            <g transform="translate(135, 200) rotate(8)">
                                <rect x="0" y="0" width="22" height="30" rx="3" fill="#ffffff"/>
                                <rect x="3" y="3" width="16" height="12" rx="2" fill="#4dabf7"/>
                                <rect x="4" y="18" width="14" height="2" fill="#e0e0e0"/>
                                <rect x="4" y="22" width="10" height="2" fill="#e0e0e0"/>
                                <circle cx="11" cy="2" r="1.5" fill="#333333"/>
                            </g>
                        </g>

                        <ellipse cx="40" cy="100" rx="16" ry="20" fill="#F5C999" stroke="#E0AA78" stroke-width="1.5"/>
                        <ellipse cx="160" cy="100" rx="16" ry="20" fill="#F5C999" stroke="#E0AA78" stroke-width="1.5"/>
                        <ellipse cx="40" cy="100" rx="10" ry="13" fill="#F2B88A" opacity="0.6"/>
                        <ellipse cx="160" cy="100" rx="10" ry="13" fill="#F2B88A" opacity="0.6"/>

                        <circle cx="100" cy="115" r="65" fill="url(#skinGrad)" stroke="#E0AA78" stroke-width="2" class="mascot-face"/>

                        <path d="M 32,68 C 30,5 170,5 168,68 C 130,80 70,80 32,68 Z" fill="url(#capGrad)" stroke="#0a1538" stroke-width="1.5" class="mascot-cap-dome"/>
                        <path d="M 100,13 Q 100,40 100,74" fill="none" stroke="#1a237e" stroke-width="2" opacity="0.5"/>
                        <path d="M 100,13 Q 60,30 40,70" fill="none" stroke="#1a237e" stroke-width="2" opacity="0.5"/>
                        <path d="M 100,13 Q 140,30 160,70" fill="none" stroke="#1a237e" stroke-width="2" opacity="0.5"/>
                        <ellipse cx="100" cy="12" rx="6" ry="3.5" fill="#0a1538"/>
                        <path d="M 15,70 C 40,105 160,105 185,70 C 150,55 50,55 15,70 Z" fill="#0a1538" class="mascot-cap-brim"/>
                        <path d="M 15,70 C 40,105 160,105 185,70 C 155,95 45,95 15,70 Z" fill="#050A1F"/>
                        <circle cx="100" cy="42" r="16" fill="rgba(255,255,255,0.1)"/>
                        <text x="100" y="48" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-weight="900" font-size="20" fill="#ffffff">E</text>

                        <ellipse cx="75" cy="108" rx="16" ry="17" fill="#ffffff" stroke="#ddd" stroke-width="1.5" class="mascot-eye-bg"/>
                        <ellipse cx="125" cy="108" rx="16" ry="17" fill="#ffffff" stroke="#ddd" stroke-width="1.5" class="mascot-eye-bg"/>

                        <circle cx="75" cy="108" r="8" fill="#2c2c2c" class="mascot-pupil" id="pupilL"/>
                        <circle cx="125" cy="108" r="8" fill="#2c2c2c" class="mascot-pupil" id="pupilR"/>

                        <circle cx="79" cy="103" r="3" fill="#ffffff" opacity="0.9" class="mascot-eye-shine"/>
                        <circle cx="129" cy="103" r="3" fill="#ffffff" opacity="0.9" class="mascot-eye-shine"/>

                        <path d="M 61,92 Q 75,86 89,92" fill="none" stroke="#A0785A" stroke-width="2.5" stroke-linecap="round" class="mascot-brow mascot-brow-l"/>
                        <path d="M 111,92 Q 125,86 139,92" fill="none" stroke="#A0785A" stroke-width="2.5" stroke-linecap="round" class="mascot-brow mascot-brow-r"/>

                        <ellipse cx="100" cy="125" rx="4.5" ry="3.5" fill="#D4956B" opacity="0.7"/>
                        <path d="M 88,135 Q 100,145 112,135" fill="none" stroke="#C0835B" stroke-width="2.5" stroke-linecap="round" class="mascot-mouth" id="mascotMouth"/>

                        <ellipse cx="60" cy="128" rx="11" ry="6" fill="#FF9E9E" opacity="0" class="mascot-blush mascot-blush-l"/>
                        <ellipse cx="140" cy="128" rx="11" ry="6" fill="#FF9E9E" opacity="0" class="mascot-blush mascot-blush-r"/>

                        <g class="mascot-arm mascot-arm-l">
                            <path d="M 26,170 Q 30,130 50,115 L 75,125 Q 60,160 54,170 A 14 14 0 0 1 26 170 Z" fill="url(#sleeveGrad)" stroke="#1a237e" stroke-width="1.5"/>
                            <ellipse cx="68" cy="108" rx="22" ry="18" fill="#F5C999" stroke="#E0AA78" stroke-width="1.5"/>
                            <circle cx="52" cy="98"  r="6.5" fill="#F5C999" stroke="#E0AA78" stroke-width="1"/>
                            <circle cx="48" cy="108" r="6.5" fill="#F5C999" stroke="#E0AA78" stroke-width="1"/>
                            <circle cx="52" cy="118" r="6.5" fill="#F5C999" stroke="#E0AA78" stroke-width="1"/>
                        </g>

                        <g class="mascot-arm mascot-arm-r">
                            <path d="M 174,170 Q 170,130 150,115 L 125,125 Q 140,160 146,170 A 14 14 0 0 0 174 170 Z" fill="url(#sleeveGrad)" stroke="#1a237e" stroke-width="1.5"/>
                            <ellipse cx="132" cy="108" rx="22" ry="18" fill="#F5C999" stroke="#E0AA78" stroke-width="1.5"/>
                            <circle cx="148" cy="98"  r="6.5" fill="#F5C999" stroke="#E0AA78" stroke-width="1"/>
                            <circle cx="152" cy="108" r="6.5" fill="#F5C999" stroke="#E0AA78" stroke-width="1"/>
                            <circle cx="148" cy="118" r="6.5" fill="#F5C999" stroke="#E0AA78" stroke-width="1"/>
                        </g>
                    </svg>
                </div>
                <img src="assets/img/logo.svg" class="login-card-logo" alt="Epoka">
            </div>

            <!-- Heading -->
            <div class="login-card-heading">
                <h1 class="login-form-title">Bon retour</h1>
                <p class="login-form-subtitle">Connectez-vous à votre espace Epoka</p>
            </div>

            <?php
            $flash = getFlash();
            if ($flash):
            ?>
            <div class="flash-message flash-<?= $flash['type'] ?>">
                <?= $flash['type'] === 'success' ? '<i data-lucide="check-circle" class="icon-sm"></i>' : '<i data-lucide="x-circle" class="icon-sm"></i>' ?>
                <?= escape($flash['message']) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login" class="login-form">
                <div class="login-input-group">
                    <label class="form-label" for="id">Identifiant</label>
                    <div class="login-input-wrapper">
                        <span class="login-input-icon"><i data-lucide="user"></i></span>
                        <input type="number" id="id" name="id" class="form-control login-input" placeholder="Votre identifiant" min="1" required autofocus>
                    </div>
                </div>

                <div class="login-input-group">
                    <label class="form-label" for="mot_de_passe">Mot de passe</label>
                    <div class="login-input-wrapper">
                        <span class="login-input-icon"><i data-lucide="lock"></i></span>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control login-input" placeholder="Votre mot de passe" required>
                        <button type="button" class="login-toggle-pw" id="togglePwBtn" tabindex="-1" aria-label="Afficher le mot de passe">
                            <i data-lucide="eye" id="pwIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="login-options">
                    <label class="login-remember">
                        <input type="checkbox" name="remember" value="1">
                        <span>Se souvenir de moi</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary login-submit-btn">
                    <i data-lucide="log-in" class="icon-sm"></i> Se connecter
                </button>
            </form>

            <div class="login-hint">
                <i data-lucide="info" class="icon-sm"></i>
                Compte démo&nbsp;: ID <strong>1</strong> / Mot de passe <strong>admin</strong>
            </div>

            <div class="login-form-footer">
                &copy; <?= date('Y') ?> Epoka &mdash; Tous droits réservés
            </div>

        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    // ── Mascot State Machine ──
    const mascotContainer = document.getElementById('mascotContainer');
    const pupilL          = document.getElementById('pupilL');
    const pupilR          = document.getElementById('pupilR');
    const idInput         = document.getElementById('id');
    const pwInputEl       = document.getElementById('mot_de_passe');

    let mascotState = 'idle';
    let stateTimeout  = null;

    function setMascotState(state, duration = 0) {
        if (mascotState === state) return;
        clearTimeout(stateTimeout);
        mascotState = state;
        mascotContainer.setAttribute('data-state', state);
        if (duration > 0) {
            stateTimeout = setTimeout(() => setMascotState('idle'), duration);
        }
    }

    // ── Smooth Eye Follow (requestAnimationFrame lerp) ──
    const EYE_L_CX = 75, EYE_R_CX = 125, EYE_CY = 108, MAX_OFFSET = 6;
    let targetEyeX = 0, targetEyeY = 0;
    let currentEyeX = 0, currentEyeY = 0;

    (function animEyes() {
        currentEyeX += (targetEyeX - currentEyeX) * 0.12;
        currentEyeY += (targetEyeY - currentEyeY) * 0.12;
        if (mascotState !== 'hiding') {
            pupilL.setAttribute('cx', EYE_L_CX + currentEyeX);
            pupilR.setAttribute('cx', EYE_R_CX + currentEyeX);
            pupilL.setAttribute('cy', EYE_CY + currentEyeY);
            pupilR.setAttribute('cy', EYE_CY + currentEyeY);
        } else {
            targetEyeX = 0; targetEyeY = 0;
        }
        requestAnimationFrame(animEyes);
    })();

    // ── Mouse move: yeux suivent + détection proximité ──
    document.addEventListener('mousemove', (e) => {
        if (mascotState === 'hiding') return;
        const svg = mascotContainer.querySelector('.mascot-svg');
        if (!svg) return;
        const rect = svg.getBoundingClientRect();
        const svgCX = rect.left + rect.width / 2;
        const svgCY = rect.top  + rect.height * 0.47;
        const dx = e.clientX - svgCX;
        const dy = e.clientY - svgCY;
        const dist = Math.sqrt(dx * dx + dy * dy);
        const norm  = dist || 1;
        const clamp = Math.min(dist, 120) / 120;
        targetEyeX = (dx / norm) * MAX_OFFSET * clamp;
        targetEyeY = (dy / norm) * MAX_OFFSET * clamp;

        // Surprise si souris trop proche
        if (dist < 55 && mascotState === 'idle') {
            setMascotState('surprised', 1100);
        }
    });

    // ── Clic sur le personnage ──
    mascotContainer.addEventListener('click', () => {
        if (mascotState === 'idle' || mascotState === 'watching') {
            setMascotState('surprised', 900);
        }
    });

    // ── Clic ailleurs → idle ──
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.login-form') &&
            !e.target.closest('.login-toggle-pw') &&
            !e.target.closest('.mascot-container')) {
            if (['watching','happy','surprised'].includes(mascotState)) {
                setMascotState('idle');
            }
            targetEyeX = 0; targetEyeY = 0;
        }
    });

    // ── Champ identifiant ──
    if (idInput) {
        idInput.addEventListener('focus', () => setMascotState('watching'));
        idInput.addEventListener('blur',  (e) => {
            if (e.relatedTarget !== pwInputEl) setMascotState('idle');
        });
        idInput.addEventListener('input', () => {
            targetEyeX = Math.min(idInput.value.length * 0.8, 5);
            targetEyeY = 3;
        });
    }

    // ── Champ mot de passe ──
    if (pwInputEl) {
        pwInputEl.addEventListener('focus', () => {
            setMascotState('hiding');
            targetEyeX = 0; targetEyeY = 0;
        });
        pwInputEl.addEventListener('blur', (e) => {
            setMascotState(e.relatedTarget === idInput ? 'watching' : 'idle');
        });
    }

    // ── Bouton afficher/cacher mot de passe ──
    const togglePwBtn = document.getElementById('togglePwBtn');
    if (togglePwBtn && pwInputEl) {
        togglePwBtn.addEventListener('click', () => {
            const isPassword = pwInputEl.type === 'password';
            pwInputEl.type = isPassword ? 'text' : 'password';
            setMascotState(isPassword ? 'peeking' : 'hiding');
            const iconEl = togglePwBtn.querySelector('svg');
            if (iconEl) iconEl.remove();
            const newIcon = document.createElement('i');
            newIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
            togglePwBtn.appendChild(newIcon);
            lucide.createIcons();
            pwInputEl.focus();
        });
    }

    // ── Bouton connexion hover → happy ──
    const submitBtn = document.querySelector('.login-submit-btn');
    if (submitBtn) {
        submitBtn.addEventListener('mouseenter', () => {
            if (!['hiding','peeking'].includes(mascotState)) setMascotState('happy');
        });
        submitBtn.addEventListener('mouseleave', () => {
            if (mascotState === 'happy') setMascotState('idle');
        });
    }

    // ── Erreur de connexion → scared ──
    if (document.querySelector('.flash-error')) {
        setTimeout(() => setMascotState('scared', 2200), 300);
    }

    // ── Clignement aléatoire ──
    (function scheduleBlink() {
        setTimeout(() => {
            if (['idle','watching'].includes(mascotState)) {
                mascotContainer.classList.add('blinking');
                setTimeout(() => {
                    mascotContainer.classList.remove('blinking');
                    scheduleBlink();
                }, 160);
            } else {
                scheduleBlink();
            }
        }, 2200 + Math.random() * 4000);
    })();

    // ── Theme Switcher ──
    const themeToggleBtn = document.getElementById('themeToggleBtn');

    function updateThemeUI(theme) {
        const iconWrapper = document.getElementById('themeToggleIconWrapper');
        if (!iconWrapper) return;
        iconWrapper.innerHTML = theme === 'liquid-light' ? '<i data-lucide="moon"></i>' : '<i data-lucide="sun"></i>';
        lucide.createIcons();
    }

    updateThemeUI(document.documentElement.getAttribute('data-theme') || 'liquid');

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const current  = document.documentElement.getAttribute('data-theme');
            const newTheme = current === 'liquid' ? 'liquid-light' : 'liquid';
            const apply = () => {
                document.documentElement.setAttribute('data-theme', newTheme);
                document.documentElement.setAttribute('data-bs-theme', newTheme === 'liquid-light' ? 'light' : 'dark');
                localStorage.setItem('epoka-theme', newTheme);
                updateThemeUI(newTheme);
            };
            document.startViewTransition ? document.startViewTransition(apply) : apply();
        });
    }
</script>
</body>
</html>
