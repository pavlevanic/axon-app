/**
 * AXON PC BUILDER
 * ─────────────────────────────────────────────────────────────
 * Upravljanje:
 *   - Koracima i otključavanjem
 *   - Selekcijom komponenti
 *   - Cookie persistencijom
 *   - Cenom u realnom vremenu
 *   - 3DMark procenom
 *   - FPS procenom za na 3 rezolucije
 *   - Pretragom
 *   - Summary ekranom (hero, upozorenja, add-to-cart)
 */

(function () {
    'use strict';

    /* ─────────────────────────────────────────────────────────────
       IGRE I KOEFICIJENTI
    ───────────────────────────────────────────────────────────── */
    const GAMES = [
        { key: 'cs2',       name: 'Counter-Strike 2',  coeff: 1.65 },
        { key: 'fortnite',  name: 'Fortnite',          coeff: 1.0  },
        { key: 'bf2042',    name: 'Battlefield 2042',  coeff: 0.72 },
        { key: 'cyberpunk', name: 'Cyberpunk 2077',    coeff: 0.52 },
        { key: 'gta5',      name: 'GTA V (Enhanced)',  coeff: 0.95 },
        { key: 'witcher3',  name: 'The Witcher 3 RT',  coeff: 0.60 },
    ];

    const RES_COEFFICIENTS = { 1080: 1.00, 1440: 0.72, 2160: 0.43 };

    /* FPS tier oznake (rezervisano za buduću upotrebu u UI-u) */
    function fpsTier(fps) {
        if (fps >= 144) return { cls: 'tier-ultra',  label: '144+ Ultra' };
        if (fps >= 60)  return { cls: 'tier-high',   label: '60+ High'   };
        if (fps >= 30)  return { cls: 'tier-medium', label: '30+ Medium' };
        return                 { cls: 'tier-low',    label: 'Under 30'  };
    }

    /* ─────────────────────────────────────────────────────────────
       STATE & KONSTANTE
    ───────────────────────────────────────────────────────────── */
    const COOKIE_NAME = 'axon_builder_v2';
    const COOKIE_DAYS = 30;
    const STEPS_ORDER = [
        'cpu', 'gpu', 'motherboard', 'ram',
        'case', 'cpu_cooler', 'case_fan', 'storage', 'psu',
    ];
    const LAST_STEP_IDX = STEPS_ORDER.length - 1;

    let state = {
        currentStep:  0,
        unlockedUpTo: 0,
        selected:     {},
        resolution:   1080,
        showSummary:  false,
    };

    /* Podatke iz Laravela čitamo samo na builder stranici */
    const builderRaw = document.getElementById('builder-data');
    if (!builderRaw) return;

    const builderData = JSON.parse(builderRaw.textContent || '{}');
    const { stepNames = {}, products: allProducts = {} } = builderData;

    /* ─────────────────────────────────────────────────────────────
       COOKIE
    ───────────────────────────────────────────────────────────── */
    function setCookie(name, value, days) {
        const exp = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = `${name}=${encodeURIComponent(JSON.stringify(value))}; expires=${exp}; path=/; SameSite=Lax`;
    }

    function getCookie(name) {
        const m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        if (!m) return null;
        try { return JSON.parse(decodeURIComponent(m[1])); } catch { return null; }
    }

    function deleteCookie(name) {
        document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
    }

    function persistState() {
        setCookie(COOKIE_NAME, {
            currentStep:  state.currentStep,
            unlockedUpTo: state.unlockedUpTo,
            selectedIds:  Object.fromEntries(
                Object.entries(state.selected).map(([k, v]) => [k, v.id])
            ),
            resolution:  state.resolution,
            showSummary: state.showSummary,
        }, COOKIE_DAYS);
    }

    function restoreState() {
        const s = getCookie(COOKIE_NAME);
        if (!s) return;

        state.currentStep  = s.currentStep  ?? 0;
        state.unlockedUpTo = s.unlockedUpTo ?? 0;
        state.resolution   = s.resolution   ?? 1080;
        state.showSummary  = s.showSummary  ?? false;

        if (s.selectedIds && typeof s.selectedIds === 'object') {
            for (const [type, id] of Object.entries(s.selectedIds)) {
                const products = allProducts[type] || [];
                const found = products.find(p => p.id === id);
                if (found) state.selected[type] = found;
            }
        }

        // Legacy fallback (stara struktura kolačića bez selectedIds)
        if (s.selected && typeof s.selected === 'object' && !s.selectedIds) {
            for (const [type, comp] of Object.entries(s.selected)) {
                if (comp && comp.id && STEPS_ORDER.includes(type)) {
                    state.selected[type] = comp;
                }
            }
        }
    }

    /* ─────────────────────────────────────────────────────────────
       DOM REFERENCE
    ───────────────────────────────────────────────────────────── */
    const $stepItem      = (k) => document.querySelector(`.step-item[data-step="${k}"]`);
    const $stepSelected   = (k) => document.getElementById(`stepSelected_${k}`);
    const $productList    = document.getElementById('productList');
    const $headerStep     = document.getElementById('prodHeaderStep');
    const $headerTitle    = document.getElementById('prodHeaderTitle');
    const $btnPrev        = document.getElementById('btnPrev');
    const $btnNext        = document.getElementById('btnNext');
    const $search         = document.getElementById('productSearch');
    const $productsPanel  = document.getElementById('productsPanel');
    const $summaryScreen  = document.getElementById('summaryScreen');

    /* Stats bar */
    const $totalPrice = document.getElementById('statTotalPrice');
    const $timeSpy     = document.getElementById('statTimeSpy');
    const $fpsCS2      = document.getElementById('fpsCS2');
    const $fpsFN       = document.getElementById('fpsFN');
    const $fpsBF       = document.getElementById('fpsBF');
    const $compat      = document.getElementById('compatIndicator');

    /* Summary */
    const $summaryList    = document.getElementById('summaryComponentList');
    const $summaryTotal   = document.getElementById('summaryTotalPrice');
    const $summaryTimeSpy = document.getElementById('summaryTimeSpy');
    const $fpsSummaryGrid = document.getElementById('fpsSummaryGrid');

    /* ─────────────────────────────────────────────────────────────
       KOMPATIBILNOST — JS FILTRIRANJE (blokira selekciju)
    ───────────────────────────────────────────────────────────── */

    /**
     * Vrati razlog nekompatibilnosti za datu komponentu,
     * ili null ako je kompatibilna.
     */
    function getIncompatReason(product, type) {
        const cpu   = state.selected.cpu;
        const gpu   = state.selected.gpu;
        const mobo  = state.selected.motherboard;
        const specs = product.specs || {};

        switch (type) {

            case 'motherboard':
                if (cpu && specs.socket && cpu.socket && specs.socket !== cpu.socket) {
                    return `Socket ${specs.socket} ≠ CPU ${cpu.socket}`;
                }
                break;

            case 'ram':
                if (mobo && specs.ram_type && mobo.specs?.ram_type && specs.ram_type !== mobo.specs.ram_type) {
                    return `${specs.ram_type} ≠ Matična ${mobo.specs.ram_type}`;
                }
                break;

            case 'cpu_cooler':
                if (cpu && specs.socket && cpu.specs?.socket) {
                    const sockets = Array.isArray(specs.socket)
                        ? specs.socket
                        : String(specs.socket).split(',').map(s => s.trim());
                    if (!sockets.includes(cpu.specs.socket)) {
                        return `Socket nekompatibilan (${cpu.specs.socket})`;
                    }
                }
                if (cpu && specs.max_tdp && cpu.specs?.tdp) {
                    if (specs.max_tdp < cpu.specs.tdp) {
                        return `Max TDP ${specs.max_tdp}W < CPU ${cpu.specs.tdp}W`;
                    }
                }
                break;

            case 'case': {
                if (gpu && specs.max_gpu_length_mm && gpu.specs?.length_mm) {
                    if (specs.max_gpu_length_mm < gpu.specs.length_mm) {
                        return `GPU ${gpu.specs.length_mm}mm > Max ${specs.max_gpu_length_mm}mm`;
                    }
                }
                if (mobo && specs.supported_motherboards && mobo.specs?.form_factor) {
                    const supported = Array.isArray(specs.supported_motherboards)
                        ? specs.supported_motherboards
                        : [specs.supported_motherboards];
                    if (!supported.includes(mobo.specs.form_factor)) {
                        return `Ne podržava ${mobo.specs.form_factor}`;
                    }
                }
                const cooler = state.selected.cpu_cooler;
                if (cooler && specs.max_cooler_mm && cooler.specs?.height_mm) {
                    if (specs.max_cooler_mm < cooler.specs.height_mm) {
                        return `Cooler ${cooler.specs.height_mm}mm > Max ${specs.max_cooler_mm}mm`;
                    }
                }
                break;
            }
        }
        return null;
    }

    /* ─────────────────────────────────────────────────────────────
       KOMPATIBILNOST — GORNJI BAR STATUS (live, tokom builda)
    ───────────────────────────────────────────────────────────── */
    function updateCompatIndicator() {
        if (!$compat) return;

        const cpu = state.selected.cpu;
        const gpu = state.selected.gpu;
        const psu = state.selected.psu;

        // Čekamo bar CPU + GPU
        if (!cpu || !gpu) {
            $compat.className = 'compat-indicator compat-idle';
            $compat.textContent = cpu ? 'Izaberi GPU za procenu' : 'Izaberi CPU i GPU';
            return;
        }

        const messages = [];
        let worstLevel = 'ok'; // ok | warn | error

        /* 1. Provjera napajanja */
        if (psu && psu.specs?.wattage) {
            const cpuTdp = cpu.specs?.tdp || 0;
            const gpuTdp = gpu.specs?.tdp || 0;
            const needed = cpuTdp + gpuTdp + 100; // +100W za ostalo
            if (needed > psu.specs.wattage) {
                messages.push(`🔴 Nedovoljna snaga (potrebno ≥${needed}W)`);
                worstLevel = 'error';
            }
        }

        /* 2. Bottleneck procena */
        const cpuScore = cpu.perf_score || 0;
        const gpuScore = gpu.perf_score || 0;
        if (cpuScore > 0 && gpuScore > 0) {
            const ratio = cpuScore / gpuScore;
            if (ratio < 0.60) {
                messages.push('⚠️ CPU Bottleneck: Procesor može limitirati GPU');
                if (worstLevel !== 'error') worstLevel = 'warn';
            } else if (ratio > 1.60) {
                messages.push('⚠️ GPU Bottleneck: Grafička limitira CPU potencijal');
                if (worstLevel !== 'error') worstLevel = 'warn';
            }
        }

        if (messages.length === 0) {
            $compat.className = 'compat-indicator compat-ok';
            $compat.textContent = '🟢 Kompatibilno i balansirano';
        } else {
            $compat.className = `compat-indicator compat-${worstLevel}`;
            $compat.textContent = messages[0];   // prikaži prvu/najkritičniju
            $compat.title = messages.join('\n'); // ostale na hover
        }
    }

    /* ─────────────────────────────────────────────────────────────
       RENDER: STEP LIST
    ───────────────────────────────────────────────────────────── */
    function renderSteps() {
        STEPS_ORDER.forEach((key, index) => {
            const el = $stepItem(key);
            if (!el) return;

            el.classList.remove('is-active', 'is-locked', 'is-done');
            if (state.showSummary) {
                el.classList.add('is-done');
            } else if (index === state.currentStep) {
                el.classList.add('is-active');
            } else if (index <= state.unlockedUpTo) {
                el.classList.add('is-done');
            } else {
                el.classList.add('is-locked');
            }

            const sel = $stepSelected(key);
            if (sel) sel.textContent = state.selected[key]?.name || '';
        });

        if ($btnPrev) $btnPrev.disabled = state.currentStep === 0;

        if ($btnNext) {
            const isLastStep  = state.currentStep === LAST_STEP_IDX;
            const hasSelected = !!state.selected[STEPS_ORDER[state.currentStep]];
            $btnNext.disabled = !hasSelected;
            $btnNext.textContent = isLastStep ? 'Završi Build ✓' : 'Sledeći →';
            $btnNext.className = isLastStep ? 'btn btn-success' : 'btn btn-primary';
        }
    }

    /* ─────────────────────────────────────────────────────────────
       RENDER: PRODUCT LIST
    ───────────────────────────────────────────────────────────── */
    function renderProducts(filterText = '') {
        const type     = STEPS_ORDER[state.currentStep];
        const stepName = stepNames[type] || type;
        const items    = allProducts[type] || [];
        const query    = filterText.toLowerCase().trim();

        if ($headerStep)  $headerStep.textContent  = `Korak ${state.currentStep + 1}`;
        if ($headerTitle) $headerTitle.textContent = stepName;

        const filtered = query
            ? items.filter(p =>
                p.name.toLowerCase().includes(query) ||
                (p.short_desc || '').toLowerCase().includes(query))
            : items;

        if (!filtered.length) {
            $productList.innerHTML = `
                <div class="products-empty">
                    <p>Nema rezultata za "<strong>${escHtml(filterText)}</strong>"</p>
                </div>`;
            return;
        }

        $productList.innerHTML = filtered.map(p => buildProductCard(p, type)).join('');

        $productList.querySelectorAll('.builder-product-item:not(.is-incompatible)').forEach(card => {
            card.addEventListener('click', function (e) {
                if (e.target.closest('.product-link-badge')) return;
                const id = parseInt(this.dataset.productId, 10);
                const product = filtered.find(p => p.id === id);
                if (product) selectComponent(type, product);
            });
        });
    }

    function buildProductCard(p, type) {
        const isSelected     = state.selected[type]?.id === p.id;
        const isSale         = p.has_discount;
        const incompatReason = getIncompatReason(p, type);

        const thumbHtml = p.image
            ? `<img src="${escHtml(p.image)}" alt="${escHtml(p.name)}" loading="lazy">`
            : `<svg class="product-thumb-placeholder" viewBox="0 0 36 36" fill="none">
                 <rect x="2" y="2" width="32" height="32" rx="4" stroke="white" stroke-width="1.5"/>
                 <rect x="9" y="9" width="18" height="18" rx="2" stroke="white" stroke-width="1"/>
               </svg>`;

        const linkHtml = p.detail_url
            ? (p.link_type === 'axon'
                ? `<a href="${escHtml(p.detail_url)}" target="_blank" class="product-link-badge badge-axon">
                     <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                       <circle cx="5" cy="5" r="4" stroke="currentColor" stroke-width="1.2"/>
                       <path d="M3 5h4M5 3l2 2-2 2" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                     </svg>Axon
                   </a>`
                : `<a href="${escHtml(p.detail_url)}" target="_blank" class="product-link-badge badge-amazon">
                     Amazon
                   </a>`)
            : '';

        const mobileLinkHtml = p.detail_url
            ? `<div class="product-mobile-link d-lg-none">
                 <a href="${escHtml(p.detail_url)}" target="_blank"
                    class="product-link-badge ${p.link_type === 'axon' ? 'badge-axon' : 'badge-amazon'}"
                    onclick="event.stopPropagation()">
                     ${p.link_type === 'axon' ? 'Axon →' : 'Amazon →'}
                 </a>
               </div>`
            : '';

        const priceHtml = isSale
            ? `<div class="product-price-orig">€${fmt(p.orig_price)}</div>
               <div class="product-price is-sale">€${fmt(p.price)}</div>`
            : `<div class="product-price">€${fmt(p.price)}</div>`;

        const btnText = isSelected ? '✓ Izabrano' : 'Izaberi';

        return `
        <div class="builder-product-item ${isSelected ? 'is-selected' : ''} ${incompatReason ? 'is-incompatible' : ''}"
             data-product-id="${p.id}"
             ${incompatReason ? `data-incompat-reason="${escHtml(incompatReason)}"` : ''}
             role="button" tabindex="${incompatReason ? '-1' : '0'}"
             aria-pressed="${isSelected}"
             aria-label="${escHtml(p.name)}">
            <div class="product-thumb">${thumbHtml}</div>
            <div class="product-info">
                <div class="product-name">${escHtml(p.name)}</div>
                <div class="product-desc">${escHtml(incompatReason || p.short_desc || '')}</div>
                ${mobileLinkHtml}
            </div>
            <div class="product-links">${linkHtml}</div>
            <div class="product-price-col">
                ${priceHtml}
                <button class="btn btn-primary btn-select-component btn-sm" ${incompatReason ? 'disabled' : ''}>
                    ${incompatReason ? '✕ Nekompatibilno' : btnText}
                </button>
            </div>
        </div>`;
    }

    /* ─────────────────────────────────────────────────────────────
       SELEKCIJA
    ───────────────────────────────────────────────────────────── */
    function selectComponent(type, product) {
        const wasSelected = state.selected[type]?.id === product.id;
        const idx         = STEPS_ORDER.indexOf(type);

        if (wasSelected) {
            delete state.selected[type];
            state.unlockedUpTo = idx;
            STEPS_ORDER.slice(idx + 1).forEach(k => delete state.selected[k]);
        } else {
            state.selected[type] = product;
            if (idx + 1 < STEPS_ORDER.length) {
                state.unlockedUpTo = Math.max(state.unlockedUpTo, idx + 1);
            }
        }

        persistState();
        renderProducts($search ? $search.value : '');
        renderSteps();
        updateStats();
        updateCompatIndicator();
    }

    /* ─────────────────────────────────────────────────────────────
       NAVIGACIJA
    ───────────────────────────────────────────────────────────── */
    async function goToStep(index) {
        if (index < 0 || index >= STEPS_ORDER.length) return;
        if (index > state.unlockedUpTo) return;

        if (state.showSummary) {
            state.showSummary = false;
            $productsPanel?.classList.remove('d-none');
            $summaryScreen?.classList.remove('is-visible');
        }

        state.currentStep = index;
        if ($search) $search.value = '';
        persistState();

        await fetchCurrentStepProducts();

        renderSteps();
        renderProducts();

        const productsCol = document.querySelector('.builder-products-col');
        if (productsCol && window.innerWidth < 992) {
            productsCol.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function showSummary() {
        state.showSummary = true;
        persistState();
        document.querySelector('.builder-wrap')?.classList.add('summary-mode');
        document.getElementById('resTabs')?.classList.add('d-none');
        $productsPanel?.classList.add('d-none');
        $summaryScreen?.classList.add('is-visible');
        renderSummary();
        renderSteps();
    }

    window.axonBuilderBackToEdit = async function () {
        state.showSummary = false;
        persistState();
        document.querySelector('.builder-wrap')?.classList.remove('summary-mode');
        document.getElementById('resTabs')?.classList.remove('d-none');
        $productsPanel?.classList.remove('d-none');
        $summaryScreen?.classList.remove('is-visible');

        await fetchCurrentStepProducts();

        renderSteps();
        renderProducts();
    };

    /* ─────────────────────────────────────────────────────────────
       STATS BAR (live tokom builda)
    ───────────────────────────────────────────────────────────── */
    function calcTimeSpy() {
        const gpu = state.selected.gpu;
        const cpu = state.selected.cpu;
        if (!gpu || !gpu.tdmark_base) return 0;
        const cpuMod = cpu ? (cpu.perf_score / 1000) * 0.18 : 0;
        return Math.round(gpu.tdmark_base * (0.82 + cpuMod));
    }

    function calcFps(gameCoeff, resolution) {
        const gpu = state.selected.gpu;
        const cpu = state.selected.cpu;
        if (!gpu || !gpu.fps_base_1080) return null;

        const resCoeff      = RES_COEFFICIENTS[resolution] || 1.0;
        const cpuBottleneck = cpu ? Math.min(1.0, cpu.perf_score / 600) : 0.85;

        // CS2 je CPU-bound, ostali manje
        const cpuFactor = gameCoeff > 1.3
            ? (0.65 + cpuBottleneck * 0.35)
            : 1.0;

        return Math.round(gpu.fps_base_1080 * gameCoeff * resCoeff * cpuFactor);
    }

    function updateStats() {
        const total = Object.values(state.selected).reduce((s, c) => s + (c.price || 0), 0);
        if ($totalPrice) $totalPrice.textContent = `€${fmt(total)}`;

        const ts = calcTimeSpy();
        if ($timeSpy) $timeSpy.textContent = ts > 0 ? ts.toLocaleString() : '—';

        const cs2Fps = calcFps(GAMES.find(g => g.key === 'cs2').coeff,      state.resolution);
        const fnFps  = calcFps(GAMES.find(g => g.key === 'fortnite').coeff, state.resolution);
        const bfFps  = calcFps(GAMES.find(g => g.key === 'bf2042').coeff,   state.resolution);

        if ($fpsCS2) $fpsCS2.textContent = cs2Fps ?? '—';
        if ($fpsFN)  $fpsFN.textContent  = fnFps  ?? '—';
        if ($fpsBF)  $fpsBF.textContent  = bfFps  ?? '—';
    }

    /* ─────────────────────────────────────────────────────────────
       SUMMARY SCREEN — UPOZORENJA (soft warnings, ne blokiraju)
    ───────────────────────────────────────────────────────────── */
    function getBuildWarnings() {
        const warnings = [];
        const cpu     = state.selected.cpu;
        const gpu     = state.selected.gpu;
        const mobo    = state.selected.motherboard;
        const ram     = state.selected.ram;
        const psu     = state.selected.psu;
        const cooler  = state.selected.cpu_cooler;
        const caseC   = state.selected.case;
        const storage = state.selected.storage;
        const caseFan = state.selected.case_fan;

        // 1. CPU Cooler TDP nedovoljan
        if (cpu && cooler && cpu.specs?.tdp && cooler.specs?.max_tdp) {
            if (cooler.specs.max_tdp < cpu.specs.tdp) {
                warnings.push({
                    level: 'error',
                    text: `<strong>${cooler.name}</strong> podržava do ${cooler.specs.max_tdp}W, a <strong>${cpu.name}</strong> ima TDP od ${cpu.specs.tdp}W. Hlađenje neće biti dovoljno — moguć throttling.`,
                });
            } else if (cooler.specs.max_tdp - cpu.specs.tdp < 20) {
                warnings.push({
                    level: 'warn',
                    text: `Hlađenje je na granici: <strong>${cooler.name}</strong> (${cooler.specs.max_tdp}W) je tek nešto iznad TDP-a procesora (${cpu.specs.tdp}W). Pod opterećenjem može biti glasno.`,
                });
            }
        }

        // 2. CPU bottleneck (jak GPU + slab CPU, ili obrnuto) — na osnovu perf_score
        if (cpu && gpu && cpu.perf_score && gpu.perf_score) {
            const ratio = cpu.perf_score / gpu.perf_score;
            if (ratio < 0.55) {
                warnings.push({
                    level: 'warn',
                    text: `<strong>${cpu.name}</strong> može usporavati <strong>${gpu.name}</strong> u zahtevnijim igrama (CPU bottleneck).`,
                });
            } else if (ratio > 1.7) {
                warnings.push({
                    level: 'warn',
                    text: `<strong>${gpu.name}</strong> je znatno slabija od procesora — nećete iskoristiti pun potencijal <strong>${cpu.name}</strong>.`,
                });
            }
        }

        // 3. PSU nedovoljan / na granici
        if (psu && cpu && gpu && psu.specs?.wattage) {
            const needed = (cpu.specs?.tdp || 0) + (gpu.specs?.tdp || 0) + 100;
            if (psu.specs.wattage < needed) {
                warnings.push({
                    level: 'error',
                    text: `Napajanje <strong>${psu.name}</strong> (${psu.specs.wattage}W) je ispod preporučenih ${needed}W za ovu konfiguraciju.`,
                });
            } else if (psu.specs.wattage - needed < 50) {
                warnings.push({
                    level: 'warn',
                    text: `Napajanje je na granici (${psu.specs.wattage}W od preporučenih ${needed}W) — malo prostora za nadogradnje.`,
                });
            }
        }

        // 4. RAM kapacitet nizak za gaming/rad
        if (ram?.specs?.capacity_gb && ram.specs.capacity_gb < 16) {
            warnings.push({
                level: 'warn',
                text: `${ram.specs.capacity_gb}GB RAM-a je ispod preporučenog minimuma (16GB) za moderne igre i multitasking.`,
            });
        }

        // 5. Mala količina skladišta
        if (storage?.specs?.capacity_gb && storage.specs.capacity_gb < 500) {
            warnings.push({
                level: 'info',
                text: `Skladište od ${storage.specs.capacity_gb}GB može brzo postati premalo — razmislite o dodatnom disku.`,
            });
        }

        // 6. Visoka potrošnja bez dodatnih ventilatora
        const totalTdp = (cpu?.specs?.tdp || 0) + (gpu?.specs?.tdp || 0);
        if (totalTdp > 350 && caseFan?.price === 0) {
            warnings.push({
                level: 'info',
                text: `Sistem ima visoku potrošnju (${totalTdp+80}W) — razmislite o dodatnim ventilatorima za bolji protok vazduha.`,
            });
        }

        // 7. Case / motherboard form factor (dodatna provera za svaki slučaj)
        if (caseC && mobo && caseC.specs?.supported_motherboards && mobo.specs?.form_factor) {
            const supported = Array.isArray(caseC.specs.supported_motherboards)
                ? caseC.specs.supported_motherboards
                : [caseC.specs.supported_motherboards];
            if (!supported.includes(mobo.specs.form_factor)) {
                warnings.push({
                    level: 'error',
                    text: `<strong>${caseC.name}</strong> možda ne podržava format matične ploče (${mobo.specs.form_factor}).`,
                });
            }
        }

        return warnings;
    }

    function renderBuildWarnings() {
        const panel = document.getElementById('buildWarningsPanel');
        if (!panel) return;

        const warnings = getBuildWarnings();
        if (!warnings.length) {
            panel.innerHTML = '';
            return;
        }

        const iconFor = (level) => {
            if (level === 'error') return '⛔';
            if (level === 'warn')  return '⚠️';
            return 'ℹ️';
        };

        panel.innerHTML = warnings.map(w => `
            <div class="build-warning-item level-${w.level}">
                <span class="build-warning-icon">${iconFor(w.level)}</span>
                <span class="build-warning-text">${w.text}</span>
            </div>
        `).join('');
    }

    /* ─────────────────────────────────────────────────────────────
       SUMMARY SCREEN — HERO (slika kućišta + meta kartice)
    ───────────────────────────────────────────────────────────── */
    function renderBuildHero() {
        const caseComp = state.selected.case;
        const cpu      = state.selected.cpu;
        const gpu      = state.selected.gpu;
        const psu      = state.selected.psu;
        const ram      = state.selected.ram;
        const storage  = state.selected.storage;
        const cooler   = state.selected.cpu_cooler;

        // ── Slika kućišta ──
        const imgWrap = document.getElementById('buildCaseImgWrap');
        if (imgWrap) {
            imgWrap.innerHTML = caseComp?.image
                ? `<img src="${escHtml(caseComp.image)}" alt="${escHtml(caseComp.name)}">`
                : `<svg class="build-case-img-ph" viewBox="0 0 80 80" fill="none">
                     <rect x="2" y="2" width="76" height="76" rx="8" stroke="white" stroke-width="2"/>
                     <rect x="16" y="16" width="48" height="48" rx="4" stroke="white" stroke-width="1.5"/>
                     <circle cx="40" cy="40" r="8" stroke="white" stroke-width="1.5"/>
                   </svg>`;
        }

        // ── Meta kartice ──
        const cards = [];

        // 1. Ukupna potrošnja vs PSU
        const cpuTdp     = cpu?.specs?.tdp || 0;
        const gpuTdp     = gpu?.specs?.tdp || 0;
        const systemLoad = cpuTdp + gpuTdp + 80; // +80W sistem
        const psuWatt    = psu?.specs?.wattage || 0;
        if (psuWatt > 0 || systemLoad > 0) {
            const headroom = psuWatt - systemLoad;
            const pct      = psuWatt > 0 ? Math.round((systemLoad / psuWatt) * 100) : null;
            const cardCls  = !psuWatt ? '' : headroom < 0 ? 'is-error' : headroom < 80 ? 'is-warn' : 'is-ok';
            cards.push({
                label: 'Potrošnja sistema',
                val:   systemLoad > 0 ? `${systemLoad}W` : '—',
                sub:   psuWatt > 0 ? `${pct}% PSU kapaciteta (${psuWatt}W)` : 'PSU nije izabran',
                cls:   cardCls,
            });
        }

        // 2. RAM
        if (ram) {
            cards.push({
                label: 'RAM memorija',
                val:   ram.specs?.capacity_gb ? `${ram.specs.capacity_gb} GB` : ram.name,
                sub:   [
                    ram.specs?.ram_type,
                    ram.specs?.speed_mhz ? `${ram.specs.speed_mhz} MHz` : null,
                ].filter(Boolean).join(' · ') || '—',
                cls: 'is-ok',
            });
        }

        // 3. Storage
        if (storage) {
            cards.push({
                label: 'Skladištenje',
                val:   storage.specs?.capacity_gb
                    ? storage.specs.capacity_gb >= 1000
                        ? `${(storage.specs.capacity_gb / 1000).toFixed(1)} TB`
                        : `${storage.specs.capacity_gb} GB`
                    : storage.name,
                sub:   [
                    storage.specs?.interface,
                    storage.specs?.read_mbs ? `R: ${storage.specs.read_mbs} MB/s` : null,
                ].filter(Boolean).join(' · ') || '—',
                cls: '',
            });
        }

        // 4. CPU
        if (cpu) {
            cards.push({
                label: 'Procesor',
                val:   cpu.name,
                sub:   [
                    cpu.specs?.cores ? `${cpu.specs.cores}C/${cpu.specs.threads ?? '?'}T` : null,
                    cpu.specs?.boost_ghz ? `${cpu.specs.boost_ghz} GHz Boost` : null,
                    cpu.specs?.tdp ? `TDP ${cpu.specs.tdp}W` : null,
                ].filter(Boolean).join(' · ') || '—',
                cls: '',
            });
        }

        // 5. GPU
        if (gpu) {
            cards.push({
                label: 'Grafička kartica',
                val:   gpu.name,
                sub:   [
                    gpu.specs?.vram_gb ? `${gpu.specs.vram_gb} GB VRAM` : null,
                    gpu.specs?.tdp ? `TDP ${gpu.specs.tdp}W` : null,
                    gpu.specs?.length_mm ? `${gpu.specs.length_mm}mm` : null,
                ].filter(Boolean).join(' · ') || '—',
                cls: '',
            });
        }

        // 6. CPU hlađenje
        if (cooler) {
            const cpuOk = !cpu?.specs?.tdp || !cooler.specs?.max_tdp
                || cooler.specs.max_tdp >= cpu.specs.tdp;
            cards.push({
                label: 'CPU Hlađenje',
                val:   cooler.name,
                sub:   [
                    cooler.specs?.type,
                    cooler.specs?.max_tdp ? `Max TDP ${cooler.specs.max_tdp}W` : null,
                    cooler.specs?.height_mm ? `${cooler.specs.height_mm}mm visina` : null,
                ].filter(Boolean).join(' · ') || '—',
                cls: cpuOk ? '' : 'is-warn',
            });
        }

        // 7. PSU efikasnost
        if (psu) {
            cards.push({
                label: 'Napajanje',
                val:   psuWatt > 0 ? `${psuWatt}W` : psu.name,
                sub:   [
                    psu.specs?.efficiency ? `80+ ${psu.specs.efficiency}` : null,
                    psu.specs?.modular !== undefined
                        ? (psu.specs.modular ? 'Modularno' : 'Non-modular')
                        : null,
                ].filter(Boolean).join(' · ') || '—',
                cls: '',
            });
        }

        const metaGrid = document.getElementById('buildMetaGrid');
        if (metaGrid) {
            metaGrid.innerHTML = cards.map(c => `
                <div class="build-meta-card ${c.cls}">
                    <span class="build-meta-label">${c.label}</span>
                    <span class="build-meta-val">${escHtml(c.val)}</span>
                    <span class="build-meta-sub">${escHtml(c.sub)}</span>
                </div>
            `).join('');
        }
    }

    /* ─────────────────────────────────────────────────────────────
       SUMMARY SCREEN — "Dodaj ceo PC u korpu" hidden inputi
    ───────────────────────────────────────────────────────────── */
    function renderBuildAddToCartInputs() {
        const container = document.getElementById('buildComponentIdsContainer');
        if (!container) return;
        container.innerHTML = Object.values(state.selected)
            .map(c => `<input type="hidden" name="component_ids[]" value="${c.id}">`)
            .join('');
    }

    /* ─────────────────────────────────────────────────────────────
       SUMMARY SCREEN — glavni render
    ───────────────────────────────────────────────────────────── */
    function renderSummary() {
        renderBuildHero();
        renderBuildWarnings();
        renderBuildAddToCartInputs();

        if (!$summaryList) return;

        // Lista komponenti
        $summaryList.innerHTML = STEPS_ORDER.map(key => {
            const comp = state.selected[key];
            if (!comp) return '';

            const thumbHtml = comp.image
                ? `<img src="${escHtml(comp.image)}" alt="${escHtml(comp.name)}">`
                : `<svg class="summary-thumb-ph" viewBox="0 0 28 28" fill="none">
                     <rect x="1" y="1" width="26" height="26" rx="4" stroke="white" stroke-width="1.5"/>
                   </svg>`;

            const viewLinkHtml = comp.detail_url
                ? `<a href="${escHtml(comp.detail_url)}" target="_blank" class="summary-icon-btn" title="Pogledaj proizvod" onclick="event.stopPropagation()">
                     <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                         <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                         <circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.4"/>
                     </svg>
                   </a>`
                : '';

            return `
            <div class="summary-component-row">
                <div class="summary-thumb">${thumbHtml}</div>
                <div class="summary-info">
                    <div class="summary-type">${escHtml(stepNames[key] || key)}</div>
                    <div class="summary-name">${escHtml(comp.name)}</div>
                </div>
                <div class="summary-price-col">
                    <div class="summary-price">€${fmt(comp.price)}</div>
                </div>
                <div class="summary-edit-col">
                    ${viewLinkHtml}
                    <button type="button" class="summary-icon-btn" title="Izmeni"
                            onclick="axonGoToStep(${STEPS_ORDER.indexOf(key)})">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M11 2.5 13.5 5 5 13.5H2.5V11L11 2.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>`;
        }).join('');

        // Ukupna cena
        const total = Object.values(state.selected).reduce((s, c) => s + (c.price || 0), 0);
        if ($summaryTotal) $summaryTotal.textContent = `€${fmt(total)}`;

        // Time Spy
        const ts = calcTimeSpy();
        if ($summaryTimeSpy) $summaryTimeSpy.textContent = ts > 0 ? ts.toLocaleString() : '—';

        // FPS grid
        renderSummaryFpsGrid(state.resolution);

        // Summary res tabs sync
        document.querySelectorAll('.summary-res-tab').forEach(tab => {
            tab.classList.toggle('active', parseInt(tab.dataset.res, 10) === state.resolution);
        });
    }

    function renderSummaryFpsGrid(resolution) {
        if (!$fpsSummaryGrid) return;
        $fpsSummaryGrid.innerHTML = GAMES.map(game => {
            const fps = calcFps(game.coeff, resolution);
            const color = !fps ? '#666'
                : fps >= 144 ? 'var(--bs-primary)'
                : fps >= 60  ? '#198754'
                : fps >= 30  ? '#d4a017'
                : '#dc3545';
            return `
            <div class="fps-row-item">
                <span class="fps-row-game">${game.name}</span>
                <span class="fps-row-val" style="color:${color}">${fps ?? '—'} <span class="fps-row-unit">FPS</span></span>
            </div>`;
        }).join('');
    }

    /* Globalna funkcija za "Izmeni" dugme iz summary */
    window.axonGoToStep = function (index) {
        axonBuilderBackToEdit();
        goToStep(index);
    };

    window.axonBuilderPrint = function () {
        window.print();
    };

    /* ─────────────────────────────────────────────────────────────
       REZOLUCIJA TABS
    ───────────────────────────────────────────────────────────── */
    function bindResTabs(containerSelector) {
        document.querySelector(containerSelector)?.querySelectorAll('[data-res]').forEach(tab => {
            tab.addEventListener('click', function () {
                document.querySelectorAll(`${containerSelector} [data-res]`).forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                state.resolution = parseInt(this.dataset.res, 10);
                persistState();
                updateStats();
                updateCompatIndicator();

                if (state.showSummary) {
                    renderSummaryFpsGrid(state.resolution);
                    document.querySelectorAll('.summary-res-tab').forEach(t =>
                        t.classList.toggle('active', parseInt(t.dataset.res, 10) === state.resolution));
                    document.querySelectorAll('.res-tab').forEach(t =>
                        t.classList.toggle('active', parseInt(t.dataset.res, 10) === state.resolution));
                }
            });
            tab.classList.toggle('active', parseInt(tab.dataset.res, 10) === state.resolution);
        });
    }

    /* ─────────────────────────────────────────────────────────────
       EVENT LISTENERS
    ───────────────────────────────────────────────────────────── */
    $btnPrev?.addEventListener('click', () => goToStep(state.currentStep - 1));

    $btnNext?.addEventListener('click', () => {
        const type = STEPS_ORDER[state.currentStep];
        if (!state.selected[type]) return;
        if (state.currentStep === LAST_STEP_IDX) {
            showSummary();
        } else {
            goToStep(state.currentStep + 1);
        }
    });

    document.getElementById('stepList')?.addEventListener('click', function (e) {
        const item = e.target.closest('.step-item');
        if (!item || item.classList.contains('is-locked')) return;
        goToStep(STEPS_ORDER.indexOf(item.dataset.step));
    });

    document.getElementById('stepList')?.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const item = e.target.closest('.step-item');
        if (!item || item.classList.contains('is-locked')) return;
        e.preventDefault();
        goToStep(STEPS_ORDER.indexOf(item.dataset.step));
    });

    $productList?.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const card = e.target.closest('.builder-product-item');
        if (!card) return;
        e.preventDefault();
        card.click();
    });

    if ($search) {
        let searchTimer;
        $search.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => renderProducts(this.value), 180);
        });
    }

    /* ─────────────────────────────────────────────────────────────
       MOBILNI COLLAPSE ZA KORAKE
    ───────────────────────────────────────────────────────────── */
    const stepsHeader = document.getElementById('stepsHeaderToggle');
    const stepsCol     = document.getElementById('stepsCol');

    stepsHeader?.addEventListener('click', function () {
        if (window.innerWidth >= 992) return;
        stepsCol?.classList.toggle('steps-collapsed');
        this.setAttribute('aria-expanded', !stepsCol?.classList.contains('steps-collapsed'));
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            stepsCol?.classList.remove('steps-collapsed');
        }
    });

    /* ─────────────────────────────────────────────────────────────
       RESET
    ───────────────────────────────────────────────────────────── */
    window.axonBuilderReset = function () {
        if (!confirm('Obrisati ceo build? Ova akcija se ne može poništiti.')) return;

        deleteCookie(COOKIE_NAME);
        state = { currentStep: 0, unlockedUpTo: 0, selected: {}, resolution: 1080, showSummary: false };

        document.querySelector('.builder-wrap')?.classList.remove('summary-mode');
        $productsPanel?.classList.remove('d-none');
        $summaryScreen?.classList.remove('is-visible');

        renderSteps();
        renderProducts();
        updateStats();
        updateCompatIndicator();
    };

    /* ─────────────────────────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────────────────────────── */
    function fmt(num) {
        return Number(num).toLocaleString('sr-Latn', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /* ─────────────────────────────────────────────────────────────
       INIT
    ───────────────────────────────────────────────────────────── */
    async function fetchCurrentStepProducts() {
        const currentStep = state.currentStep || 'cpu';

        if (state.products && state.products[currentStep] && state.products[currentStep].length > 0) {
            return;
        }

        try {
            const response = await fetch(`/pc-builder/components/${currentStep}`);
            if (!response.ok) throw new Error('Mrežna greška pri učitavanju komponenti');

            const data = await response.json();
            if (!state.products) state.products = {};
            state.products[currentStep] = data.products;
        } catch (error) {
            console.error('Greška pri lazy loading-u:', error);
        }
    }

    async function init() {
        restoreState();

        bindResTabs('#resTabs');
        bindResTabs('#summaryResTabs');

        if (state.showSummary) {
            document.querySelector('.builder-wrap')?.classList.add('summary-mode');
            document.getElementById('resTabs')?.classList.add('d-none');
            $productsPanel?.classList.add('d-none');
            $summaryScreen?.classList.add('is-visible');
            renderSummary();
            renderSteps();
        } else {
            await fetchCurrentStepProducts();
            renderSteps();
            renderProducts();
        }

        updateStats();
        updateCompatIndicator();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();

/* ─────────────────────────────────────────────────────────────
   THEME TOGGLE (light/dark, default: light)
───────────────────────────────────────────────────────────── */
(function () {
    const THEME_KEY = 'axon_builder_theme';
    const toggle = document.getElementById('themeToggle');
    if (!toggle) return;

    const iconLight = toggle.querySelector('.theme-icon-light');
    const iconDark  = toggle.querySelector('.theme-icon-dark');

    function applyTheme(isLight) {
        document.body.classList.toggle('light-theme', isLight);
        iconLight?.classList.toggle('d-none', isLight);
        iconDark?.classList.toggle('d-none', !isLight);
    }

    applyTheme(localStorage.getItem(THEME_KEY) !== 'dark');

    toggle.addEventListener('click', function () {
        const isLight = !document.body.classList.contains('light-theme');
        applyTheme(isLight);
        localStorage.setItem(THEME_KEY, isLight ? 'light' : 'dark');
    });
})();