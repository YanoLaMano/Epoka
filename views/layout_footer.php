    </main>
</div>

<script>
// Fonction de recherche de ville AJAX
function initVilleSearch(inputId, hiddenId, resultsId) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);
    const results = document.getElementById(resultsId);
    
    if (!input || !hidden || !results) return;

    let timeout = null;

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        // On vide l'ID caché : l'utilisateur DOIT recliquer sur une suggestion
        hidden.value = '';
        
        const terme = this.value.trim();
        
        if (terme.length < 2) {
            results.classList.remove('active');
            return;
        }

        timeout = setTimeout(() => {
            fetch('index.php?api=search_villes&terme=' + encodeURIComponent(terme))
                .then(r => r.json())
                .then(data => {
                    results.innerHTML = '';
                    if (data.length === 0) {
                        results.innerHTML = '<div class="search-result-item">Aucun résultat</div>';
                    } else {
                        data.forEach(ville => {
                            const item = document.createElement('div');
                            item.className = 'search-result-item';
                            item.innerHTML = ville.nom + ' <span class="ville-cp">(' + ville.code_postal + ')</span>';
                            item.addEventListener('click', () => {
                                input.value = ville.nom + ' (' + ville.code_postal + ')';
                                hidden.value = ville.id;
                                results.classList.remove('active');
                                hidden.dispatchEvent(new Event('ville-selected', { bubbles: true }));
                            });
                            results.appendChild(item);
                        });
                    }
                    results.classList.add('active');
                });
        }, 300);
    });

    input.addEventListener('blur', function() {
        setTimeout(() => results.classList.remove('active'), 200);
    });

    input.addEventListener('focus', function() {
        if (results.children.length > 0 && this.value.length >= 2) {
            results.classList.add('active');
        }
    });
}

// Initialiser les champs de recherche ville et les icônes
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    initVilleSearch('ville_depart_search', 'id_ville_depart', 'ville_depart_results');
    initVilleSearch('ville_arrivee_search', 'id_ville_arrivee', 'ville_arrivee_results');
    initVilleSearch('ville_search', 'id_ville', 'ville_results');
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggleBtn = document.getElementById('themeToggleBtn');

        function updateThemeUI(theme) {
            if (!themeToggleBtn) return;
            const iconWrapper = document.getElementById('themeToggleIconWrapper');
            if (iconWrapper) {
                iconWrapper.innerHTML = theme === 'liquid-light'
                    ? '<i data-lucide="moon"></i>'
                    : '<i data-lucide="sun"></i>';
            }
            lucide.createIcons();
        }

        const currentTheme = document.documentElement.getAttribute('data-theme') || 'liquid';
        updateThemeUI(currentTheme);

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const current = document.documentElement.getAttribute('data-theme');
                const newTheme = current === 'liquid' ? 'liquid-light' : 'liquid';

                const applyTheme = () => {
                    document.documentElement.setAttribute('data-theme', newTheme);
                    document.documentElement.setAttribute('data-bs-theme', newTheme === 'liquid-light' ? 'light' : 'dark');
                    localStorage.setItem('epoka-theme', newTheme);
                    updateThemeUI(newTheme);
                };

                // View Transitions API pour une transition fluide (Chrome 111+, Safari 18+)
                if (document.startViewTransition) {
                    document.startViewTransition(applyTheme);
                } else {
                    applyTheme();
                }
            });
        }
    });
</script>
<script>
// ===== Upload Avatar =====
(function () {
    const btn   = document.getElementById('userAvatarBtn');
    const input = document.getElementById('avatarInput');
    if (!btn || !input) return;

    btn.addEventListener('click', () => input.click());

    input.addEventListener('change', async () => {
        const file = input.files[0];
        if (!file) return;

        const fd = new FormData();
        fd.append('avatar', file);

        try {
            const res  = await fetch('index.php?api=upload_avatar', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                const img  = document.getElementById('userAvatarImg');
                const init = document.getElementById('userAvatarInitial');
                img.src = data.url;
                img.style.display = '';
                if (init) init.style.display = 'none';
                // Rafraîchir l'icône Lucide dans l'overlay
                lucide.createIcons();
            } else {
                alert(data.error || 'Erreur lors de l\'upload');
            }
        } catch (e) {
            console.error('Erreur upload avatar', e);
        }

        input.value = '';
    });
})();

// ===== Confetti Vert — explosion depuis tous les bords =====
(function () {
    if (!window._fireConfetti) return;

    const canvas = document.createElement('canvas');
    canvas.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9998;';
    document.body.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    const W = canvas.width  = window.innerWidth;
    const H = canvas.height = window.innerHeight;

    const COLORS = ['#28a745','#34d058','#2ea44f','#85e89d','#22863a','#6fcf97','#52c41a','#b7eb8f'];
    const COUNT  = 120;

    // Génère un point de départ sur l'un des 4 bords + vecteur dirigé vers l'intérieur
    function spawnOnEdge() {
        const edge = Math.floor(Math.random() * 4); // 0=haut 1=bas 2=gauche 3=droite
        let x, y, vx, vy;
        const speed = Math.random() * 6 + 3;
        const spread = (Math.random() - 0.5) * 2; // déviation latérale

        if (edge === 0) {          // haut
            x = Math.random() * W;
            y = -8;
            vx = spread * speed * 0.5;
            vy = Math.random() * speed * 0.6 + speed * 0.4;
        } else if (edge === 1) {   // bas
            x = Math.random() * W;
            y = H + 8;
            vx = spread * speed * 0.5;
            vy = -(Math.random() * speed * 0.6 + speed * 0.4);
        } else if (edge === 2) {   // gauche
            x = -8;
            y = Math.random() * H;
            vx = Math.random() * speed * 0.6 + speed * 0.4;
            vy = spread * speed * 0.5;
        } else {                   // droite
            x = W + 8;
            y = Math.random() * H;
            vx = -(Math.random() * speed * 0.6 + speed * 0.4);
            vy = spread * speed * 0.5;
        }
        return { x, y, vx, vy };
    }

    const particles = Array.from({ length: COUNT }, () => {
        const { x, y, vx, vy } = spawnOnEdge();
        return {
            x, y, vx, vy,
            w  : Math.random() * 10 + 4,
            h  : Math.random() * 5 + 2,
            rot: Math.random() * Math.PI * 2,
            vr : (Math.random() - 0.5) * 0.18,
            col: COLORS[Math.floor(Math.random() * COLORS.length)],
            op : 1,
            delay: Math.floor(Math.random() * 20), // explosion quasi-simultanée
        };
    });

    let frame = 0;
    function draw() {
        ctx.clearRect(0, 0, W, H);
        frame++;
        let alive = false;
        for (const p of particles) {
            if (frame < p.delay) { alive = true; continue; }
            p.x  += p.vx;
            p.y  += p.vy;
            p.vy += 0.07;  // gravité
            p.vx *= 0.995; // friction légère
            p.rot += p.vr;
            p.op  = Math.max(0, p.op - 0.006);
            if (p.op <= 0) continue;
            alive = true;
            ctx.save();
            ctx.globalAlpha = p.op;
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rot);
            ctx.fillStyle = p.col;
            ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
            ctx.restore();
        }
        if (alive) requestAnimationFrame(draw);
        else canvas.remove();
    }
    requestAnimationFrame(draw);
})();
</script>
</body>
</html>
