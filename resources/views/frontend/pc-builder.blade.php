@extends('layouts.app')

@section('title', 'PC Builder — Složi svoj računar | Axon')

@push('styles')
<style>
:root {
    --builder-bg:        #0a0a0f;
    --builder-surface:   #111118;
    --builder-surface2:  #16161f;
    --builder-border:    rgba(255,255,255,0.07);
    --builder-border2:   rgba(255,255,255,0.13);
    --builder-muted:     rgba(255,255,255,0.45);
    --builder-text:      #ffffff;
    --builder-text-soft: rgba(255,255,255,0.75);
}
body.light-theme {
    --builder-bg:        #f0f2f5;
    --builder-surface:   #ffffff;
    --builder-surface2:  #f4f5f8;
    --builder-border:    rgba(0,0,0,0.08);
    --builder-border2:   rgba(0,0,0,0.14);
    --builder-muted:     rgba(0,0,0,0.50);
    --builder-text:      #111111;
    --builder-text-soft: rgba(0,0,0,0.72);
}

body { background: var(--builder-bg) !important; }
.builder-wrap { min-height: calc(100vh - 60px); padding: 0; }

/* ── LEFT sidebar ── */
.builder-steps-col {
    background: var(--builder-surface);
    border-right: 1px solid var(--builder-border);
    min-height: calc(100vh - 60px);
    position: sticky; top: 60px;
    height: calc(100vh - 60px);
    overflow-y: auto;
    display: flex; flex-direction: column;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.1) transparent;
}
.steps-header { padding: 20px 20px 12px; border-bottom: 1px solid var(--builder-border); flex-shrink: 0; }
.steps-header-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.2em; color: var(--builder-muted); }
.steps-header-title { font-size: 15px; font-weight: 700; color: var(--builder-text); margin: 4px 0 0; }
.step-list { flex: 1; padding: 10px 12px; }
.step-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 12px; border-radius: 8px; margin-bottom: 2px;
    cursor: pointer; border: 1px solid transparent;
    transition: background 0.15s, border-color 0.15s;
}
.step-item.is-locked { cursor: not-allowed; opacity: 0.4; pointer-events: none; }
.step-item.is-active { background: rgba(var(--bs-primary-rgb),0.12); border-color: rgba(var(--bs-primary-rgb),0.3); }
.step-item.is-done:hover,
.step-item:not(.is-locked):not(.is-active):hover { background: var(--builder-surface2); border-color: var(--builder-border2); }
.step-num {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; flex-shrink: 0;
    background: var(--builder-surface2); color: var(--builder-muted);
    border: 1px solid var(--builder-border2);
    transition: background 0.2s, color 0.2s, border-color 0.2s;
}
.step-item.is-active .step-num { background: var(--bs-primary); color: #fff; border-color: var(--bs-primary); }
.step-item.is-done .step-num { background: rgba(25,135,84,0.2); color: #198754; border-color: rgba(25,135,84,0.4); font-size: 0; }
.step-item.is-done .step-num::after { content: '✓'; font-size: 12px; }
.step-info { flex: 1; min-width: 0; }
.step-label { font-size: 11px; color: var(--builder-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1px; }
.step-item.is-active .step-label { color: rgba(var(--bs-primary-rgb),0.8); }
.step-name { font-size: 13px; font-weight: 600; color: var(--builder-text-soft); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.step-item.is-active .step-name { color: var(--builder-text); }
.step-selected { font-size: 11px; color: #198754; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 1px; }

/* ── RIGHT col ── */
.builder-products-col { background: var(--builder-bg); min-height: calc(100vh - 60px); padding: 0; display: flex; flex-direction: column; }

/* ── Stats bar ── */
.builder-stats-bar {
    background: var(--builder-surface); border-bottom: 1px solid var(--builder-border);
    padding: 12px 24px; display: flex; align-items: center; flex-wrap: wrap; gap: 16px;
    position: relative;
}
.stat-block { display: flex; flex-direction: column; gap: 2px; }
.stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: var(--builder-muted); }
.stat-value { font-size: 18px; font-weight: 800; letter-spacing: -0.03em; color: var(--builder-text); line-height: 1; }
.stat-value.primary { color: var(--bs-primary); }
.stat-divider { width: 1px; height: 36px; background: var(--builder-border2); flex-shrink: 0; }

/* Compatibility indicator */
.compat-indicator {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600;
    padding: 5px 12px; border-radius: 6px;
    border: 1px solid transparent;
    transition: all 0.3s;
    white-space: nowrap;
}
.compat-ok    { background: rgba(25,135,84,0.1);  color: #198754; border-color: rgba(25,135,84,0.2); }
.compat-warn  { background: rgba(255,193,7,0.1);   color: #d4a017; border-color: rgba(255,193,7,0.2); }
.compat-error { background: rgba(220,53,69,0.1);   color: #dc3545; border-color: rgba(220,53,69,0.2); }
.compat-idle  { background: var(--builder-surface2); color: var(--builder-muted); border-color: var(--builder-border); }

.res-tabs { display: flex; gap: 2px; background: var(--builder-surface2); border-radius: 6px; padding: 3px; border: 1px solid var(--builder-border); }
.res-tab { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 4px; cursor: pointer; color: var(--builder-muted); border: none; background: none; transition: background 0.15s, color 0.15s; }
.res-tab.active { background: var(--bs-primary); color: #fff; }
.stats-controls { display: flex; align-items: center; gap: 8px; margin-left: auto; }
.theme-toggle { display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 6px; border: 1px solid var(--builder-border); background: var(--builder-surface2); color: var(--builder-text); cursor: pointer; transition: background 0.15s; flex-shrink: 0; }

.fps-grid { display: flex; gap: 12px; align-items: center; }
.fps-game { display: flex; flex-direction: column; gap: 1px; text-align: center; }
.fps-game-name { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--builder-muted); }
.fps-game-val { font-size: 16px; font-weight: 800; color: var(--builder-text); line-height: 1; }
.fps-game-unit { font-size: 9px; color: var(--builder-muted); font-weight: 600; }

/* ── Products header ── */
.products-header {
    padding: 16px 24px 12px; border-bottom: 1px solid var(--builder-border);
    background: var(--builder-surface);  top: 60px; z-index: 10;
    display: flex; align-items: center; gap: 16px;
}
.products-header-step { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: var(--bs-primary); }
.products-header-title { font-size: 17px; font-weight: 800; color: var(--builder-text); margin: 2px 0 0; }
.products-search {
    margin-left: auto; background: var(--builder-surface2);
    border: 1px solid var(--builder-border2); border-radius: 8px;
    padding: 7px 14px; color: var(--builder-text); font-size: 13px;
    outline: none; width: 220px; transition: border-color 0.2s, width 0.3s;
}
.products-search:focus { border-color: var(--bs-primary); width: 280px; }
.products-search::placeholder { color: var(--builder-muted); }

.products-body { padding: 16px 20px; flex: 1; }

/* ── Product card ── */
.builder-product-item {
    display: flex; align-items: center;
    background: var(--builder-surface); border: 1px solid var(--builder-border);
    border-radius: 8px; margin-bottom: 8px; overflow: hidden;
    transition: border-color 0.15s, box-shadow 0.15s; cursor: pointer;
}
.builder-product-item:hover { border-color: var(--builder-border2); box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
.builder-product-item.is-selected { border-color: var(--bs-primary); background: rgba(var(--bs-primary-rgb),0.06); box-shadow: 0 0 0 1px rgba(var(--bs-primary-rgb),0.2); }
.builder-product-item.is-incompatible { opacity: 0.45; cursor: not-allowed; filter: grayscale(0.5); }
.builder-product-item.is-incompatible::before {
    content: attr(data-incompat-reason);
    position: absolute; background: rgba(220,53,69,0.9); color: #fff;
    font-size: 10px; font-weight: 700; padding: 2px 8px;
    border-radius: 0 0 4px 0; left: 72px; top: 0;
}
.builder-product-item { position: relative; }

.product-thumb { width: 72px; height: 72px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--builder-surface2); border-right: 1px solid var(--builder-border); padding: 8px; }
.product-thumb img { max-width: 100%; max-height: 100%; object-fit: contain; }
.product-thumb-placeholder { width: 36px; height: 36px; opacity: 0.2; }
.product-info { flex: 1; min-width: 0; padding: 10px 14px; }
.product-mobile-link { margin-top: 5px; }
.product-name { font-size: 13px; font-weight: 600; color: var(--builder-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 3px; }
.product-desc { font-size: 11px; color: var(--builder-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.product-links { display: flex; align-items: center; gap: 6px; padding: 0 10px; flex-shrink: 0; }
.product-link-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-decoration: none; text-transform: uppercase; transition: opacity 0.15s; }
.product-link-badge:hover { opacity: 0.8; }
.badge-axon   { background: rgba(var(--bs-primary-rgb),0.15); color: var(--bs-primary); border: 1px solid rgba(var(--bs-primary-rgb),0.25); }
.badge-amazon { background: rgb(124, 58, 237); color: #ffffff;  }
.product-price-col { display: flex; flex-direction: column; align-items: flex-end; padding: 10px 14px; flex-shrink: 0; min-width: 110px; border-left: 1px solid var(--builder-border); }
.product-price { font-size: 15px; font-weight: 800; color: var(--builder-text); letter-spacing: -0.02em; margin-bottom: 6px; }
.product-price-orig { font-size: 11px; color: var(--builder-muted); text-decoration: line-through; margin-bottom: 2px; }
.product-price.is-sale { color: #dc3545; }
.btn-select-component { font-size: 11px; font-weight: 700; padding: 5px 14px; border-radius: 6px; text-transform: uppercase; white-space: nowrap; }
.builder-product-item.is-selected .btn-select-component { background: #198754 !important; border-color: #198754 !important; }
.products-empty { text-align: center; padding: 60px 20px; color: var(--builder-muted); }

/* ── Nav ── */
.products-nav { padding: 16px 20px 24px; display: flex; gap: 8px; justify-content: flex-end; background: var(--builder-surface); border-top: 1px solid var(--builder-border); }
.products-nav .btn { min-width: 130px; font-size: 13px; font-weight: 600; border-radius: 6px; }

/* ══════════════════════════════════════════════════
   SUMMARY / FINALE SCREEN
   ══════════════════════════════════════════════════ */
#summaryScreen { display: none; }
#summaryScreen.is-visible { display: block; }

.summary-wrap { padding: 24px; }
.summary-title { font-size: 22px; font-weight: 800; color: var(--builder-text); letter-spacing: -0.02em; margin-bottom: 4px; }
.summary-subtitle { font-size: 13px; color: var(--builder-muted); margin-bottom: 24px; }

/* Summary component list */
.summary-component-row {
    display: flex; align-items: center; gap: 0;
    background: var(--builder-surface); border: 1px solid var(--builder-border);
    border-radius: 8px; margin-bottom: 8px; overflow: hidden;
    transition: border-color 0.15s;
}
.summary-component-row:hover { border-color: var(--builder-border2); }
.summary-thumb { width: 60px; height: 60px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--builder-surface2); border-right: 1px solid var(--builder-border); padding: 6px; }
.summary-thumb img { max-width: 100%; max-height: 100%; object-fit: contain; }
.summary-thumb-ph { width: 28px; height: 28px; opacity: 0.15; }
.summary-info { flex: 1; min-width: 0; padding: 8px 14px; }
.summary-type { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--builder-muted); margin-bottom: 2px; }
.summary-name { font-size: 13px; font-weight: 600; color: var(--builder-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.summary-price-col { padding: 8px 14px; flex-shrink: 0; border-left: 1px solid var(--builder-border); text-align: right; min-width: 90px; }
.summary-price { font-size: 14px; font-weight: 800; color: var(--builder-text); }
.summary-edit-col { padding: 8px 12px; flex-shrink: 0; }
.btn-summary-edit { font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 6px; text-transform: uppercase; }
.summary-edit-col { display: flex; align-items: center; gap: 4px; }
.summary-icon-btn {
    display: flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 6px;
    border: none; background: none; padding: 0;
    color: var(--builder-text); cursor: pointer;
    transition: background 0.15s, opacity 0.15s;
    text-decoration: none;
}
.summary-icon-btn:hover { background: var(--builder-surface2); opacity: 0.8; }

/* Summary totals */
.summary-total-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-radius: 8px;
    background: rgba(var(--bs-primary-rgb),0.08); border: 1px solid rgba(var(--bs-primary-rgb),0.2);
    margin-bottom: 28px; margin-top: 4px;
}
.summary-total-label { font-size: 13px; color: var(--builder-muted); font-weight: 600; }
.summary-total-val { font-size: 26px; font-weight: 900; color: var(--bs-primary); letter-spacing: -0.03em; }

/* Final benchmark panel */
.benchmark-panel {
    background: var(--builder-surface); border: 1px solid var(--builder-border);
    border-radius: 12px; overflow: hidden; margin-bottom: 24px;
}
.benchmark-header {
    padding: 16px 20px; border-bottom: 1px solid var(--builder-border);
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.benchmark-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--builder-muted); }
.benchmark-score-wrap { display: flex; align-items: baseline; gap: 6px; }
.benchmark-score { font-size: 36px; font-weight: 900; color: var(--builder-text); letter-spacing: -0.04em; }
.benchmark-score-label { font-size: 12px; color: var(--builder-muted); font-weight: 600; }

/* FPS list — 3DMark stil */
.fps-summary-grid {
    padding: 8px 0;
}
.fps-row-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 11px 20px;
    border-bottom: 1px solid var(--builder-border);
    transition: background 0.1s;
}
.fps-row-item:last-child { border-bottom: none; }
.fps-row-item:hover { background: var(--builder-surface2); }
.fps-row-game {
    font-size: 14px; font-weight: 500; color: var(--builder-text);
}
.fps-row-val {
    font-size: 18px; font-weight: 800; letter-spacing: -0.02em;
}
.fps-row-unit {
    font-size: 12px; font-weight: 600; opacity: 0.7;
}

/* Summary res tabs */
.summary-res-tabs { display: flex; gap: 4px; }
.summary-res-tab {
    font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 6px;
    cursor: pointer; border: 1px solid var(--builder-border2);
    background: var(--builder-surface2); color: var(--builder-muted); transition: all 0.15s;
}
.summary-res-tab.active { background: var(--bs-primary); color: #fff; border-color: var(--bs-primary); }

/* Action buttons na kraju */
.summary-actions { display: flex; gap: 12px; flex-wrap: wrap; padding-bottom: 24px; }
.summary-actions .btn { font-size: 14px; font-weight: 700; padding: 10px 28px; border-radius: 8px; }

/* ── Summary: sakrij sidebar, proširi desnu kolonu ── */
.summary-mode #stepsCol {
    display: none !important;
}
.summary-mode #productsCol {
    flex: 0 0 100%;
    max-width: 100%;
}
.summary-mode .builder-stats-bar {
    position: relative;
}
.summary-wrap { max-width: 860px; margin: 0 auto; }
.btn-add-build {
    display: flex; align-items: center; justify-content: center;
    width: 100%;
    background: var(--bs-primary); color: #fff;
    border: none; border-radius: 8px;
    font-size: 14px; font-weight: 700;
    padding: 13px 28px;
    cursor: pointer;
    transition: opacity 0.15s, transform 0.1s;
}
.btn-add-build:hover { opacity: 0.9; }
.btn-add-build:active { transform: scale(0.99); }

.build-warnings-panel { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
.build-warning-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 14px; border-radius: 8px;
    font-size: 13px; line-height: 1.4;
}
.build-warning-item.level-warn  { background: rgba(255,193,7,0.08);  border: 1px solid rgba(255,193,7,0.25);  color: #d4a017; }
.build-warning-item.level-error { background: rgba(220,53,69,0.08);  border: 1px solid rgba(220,53,69,0.25);  color: #dc3545; }
.build-warning-item.level-info  { background: rgba(13,110,253,0.08); border: 1px solid rgba(13,110,253,0.25); color: #6ea8fe; }
.build-warning-icon { flex-shrink: 0; margin-top: 1px; }
.build-warning-text strong { color: var(--builder-text); font-weight: 700; }

/* Responsive */
@media (max-width: 991px) {
    .builder-steps-col {
        position: relative; top: 0; height: auto;
        border-right: none; border-bottom: 1px solid var(--builder-border);
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .builder-steps-col.steps-collapsed {
        border-bottom: none;
    }
    .builder-steps-col.steps-collapsed #stepList {
        display: none !important;
    }
    /* Kad je collapsed, col zauzima samo visinu headera */
    .builder-steps-col.steps-collapsed {
    min-height: 0 !important;
    flex: 0 0 auto !important;
    max-height: none;
    }
    .builder-steps-col.steps-collapsed #stepList {
    display: none !important;
    }
    .builder-stats-bar { padding: 10px 16px; gap: 10px; }
    .products-search { width: 140px; }
    .products-search:focus { width: 180px; }
    .product-thumb { width: 56px; height: 56px; }
    .product-desc { display: none; }
    .steps-collapse-icon { font-size: 10px; transition: transform 0.2s; display: inline-block; }
    .steps-collapsed .steps-collapse-icon { transform: rotate(180deg); }
    .stats-controls { gap: 4px; }
    .stats-controls #resTabs { display: none;}
    .product-links { display: none; }
    .product-mobile-link { display: block !important; }
}
@media (max-width: 576px) {
    .builder-stats-bar .fps-grid { display: none; }
    .stats-controls #resTabs { display: none; }
    .fps-summary-grid { grid-template-columns: repeat(2, 1fr); }
}
.build-hero {
    display: flex; flex-direction: column; align-items: center;
    gap: 20px; margin-bottom: 24px;
}
.build-case-img-wrap {
    width: 220px; height: 220px;
    display: flex; align-items: center; justify-content: center;
    background: var(--builder-surface); border: 1px solid var(--builder-border);
    border-radius: 16px; overflow: hidden; padding: 16px;
}
.build-case-img-wrap img { max-width: 100%; max-height: 100%; object-fit: contain; }
.build-case-img-ph { width: 80px; height: 80px; opacity: 0.12; }

.build-meta-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 10px; width: 100%;
}
.build-meta-card {
    background: var(--builder-surface); border: 1px solid var(--builder-border);
    border-radius: 10px; padding: 12px 14px;
    display: flex; flex-direction: column; gap: 4px;
}
.build-meta-card.is-warn  { border-color: rgba(255,193,7,0.4);  background: rgba(255,193,7,0.06); }
.build-meta-card.is-error { border-color: rgba(220,53,69,0.4);  background: rgba(220,53,69,0.06); }
.build-meta-card.is-ok    { border-color: rgba(25,135,84,0.4);  background: rgba(25,135,84,0.06); }
.build-meta-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--builder-muted); }
.build-meta-val   { font-size: 18px; font-weight: 800; color: var(--builder-text); letter-spacing: -0.02em; line-height: 1.1; }
.build-meta-sub   { font-size: 11px; color: var(--builder-muted); }

@media (max-width: 600px) {
    .build-meta-grid { grid-template-columns: repeat(2, 1fr); }
    .build-case-img-wrap { width: 160px; height: 160px; }
}
</style>
@endpush

@section('content')

<script id="builder-data" type="application/json">
    {!! json_encode($builderData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>

<div class="builder-wrap">
<div class="row g-0">

    {{-- ══ LEFT: Koraci (sakriven na summary ekranu) ══ --}}
    <div class="col-lg-3 builder-steps-col" id="stepsCol">
        <div class="steps-header" id="stepsHeaderToggle" role="button" aria-expanded="true" aria-controls="stepList" style="cursor:pointer;">
            <p class="steps-header-label">PC Builder <span class="steps-collapse-icon ms-1">▲</span></p>
            <h1 class="steps-header-title">Složi svoj računar</h1>
        </div>
        <div class="step-list" id="stepList">
            @foreach($steps as $key => $label)
            <div class="step-item {{ $loop->first ? 'is-active' : 'is-locked' }}"
                 data-step="{{ $key }}"
                 data-index="{{ $loop->index }}">
                <div class="step-num">{{ $loop->iteration }}</div>
                <div class="step-info">
                    <div class="step-label">Korak {{ $loop->iteration }}</div>
                    <div class="step-name">{{ $label }}</div>
                    <div class="step-selected" id="stepSelected_{{ $key }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══ RIGHT: Sadržaj ══ --}}
    <div class="col-lg-9 builder-products-col" id="productsCol">

        {{-- Stats bar --}}
        <div class="builder-stats-bar">
            <div class="stat-block">
                <span class="stat-label">Ukupna cena</span>
                <span class="stat-value primary" id="statTotalPrice">€0</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-block">
                <span class="stat-label">3DMark Time Spy</span>
                <span class="stat-value" id="statTimeSpy">—</span>
            </div>
            <div class="stat-divider d-none d-md-block"></div>
            {{-- Compatibility indicator --}}
            <div class="compat-indicator compat-idle" id="compatIndicator">
                Izaberi CPU i GPU
            </div>
            <div class="stats-controls">
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="Promeni temu">
                    <i class="bi bi-sun-fill theme-icon-light"></i>
                    <i class="bi bi-moon-fill theme-icon-dark d-none"></i>
                </button>
            </div>
        </div>

        {{-- Products panel --}}
        <div id="productsPanel">
            <div class="products-header">
                <div>
                    <p class="products-header-step" id="prodHeaderStep">Korak 1</p>
                    <h2 class="products-header-title" id="prodHeaderTitle">Procesor (CPU)</h2>
                </div>
                <input type="text" class="products-search" id="productSearch" placeholder="Pretraži…" autocomplete="off">
            </div>
            <div class="products-body">
                <div id="productList">
                    <div class="products-empty">
                        <p>Učitavam komponente…</p>
                    </div>
                </div>
            </div>
            <div class="products-nav">
                <button class="btn btn-outline-secondary" id="btnPrev" disabled>← Nazad</button>
                <button class="btn btn-primary" id="btnNext" disabled>Sledeći →</button>
            </div>
        </div>

        {{-- ══ SUMMARY SCREEN ══ --}}
        <div id="summaryScreen">
            <div class="summary-wrap">
                <h2 class="summary-title">Build je kompletan</h2>
                <p class="summary-subtitle">Pregled svih izabranih komponenti. Klikni "Izmeni" da se vratiš na taj korak.</p>

                <div class="build-hero">
                    <div class="build-case-img-wrap" id="buildCaseImgWrap">
                        {{-- JS popunjava --}}
                    </div>
                    <div class="build-meta-grid" id="buildMetaGrid">
                        {{-- JS popunjava --}}
                    </div>
                </div>

                <div id="buildWarningsPanel" class="build-warnings-panel"></div>

                {{-- Lista komponenti --}}
                <div id="summaryComponentList"></div>

                {{-- Ukupna cena --}}
                <div class="summary-total-bar">
                    <span class="summary-total-label">Ukupna cena konfiguracije</span>
                    <span class="summary-total-val" id="summaryTotalPrice">€0</span>
                </div>

                <form action="{{ route('builder.add-to-cart') }}" method="POST" id="addBuildForm">
               @csrf
               <div id="buildComponentIdsContainer"></div>
               <button type="submit" class="btn-add-build mb-4">
                  <i class="bi bi-bag me-2"></i>Dodaj ceo PC u korpu
                </button>
               </form>
                {{-- Benchmark panel --}}
                <div class="benchmark-panel">
                    <div class="benchmark-header">
                        <div>
                            <p class="benchmark-title">Procena performansi</p>
                            <div class="benchmark-score-wrap">
                                <span class="benchmark-score" id="summaryTimeSpy">—</span>
                                <span class="benchmark-score-label">3DMark Time Spy</span>
                            </div>
                        </div>
                        <div class="summary-res-tabs" id="summaryResTabs">
                            <button class="summary-res-tab active" data-res="1080">1080p</button>
                            <button class="summary-res-tab" data-res="1440">1440p</button>
                            <button class="summary-res-tab" data-res="2160">4K</button>
                        </div>
                    </div>
                    <div class="fps-summary-grid" id="fpsSummaryGrid">
                        {{-- Renderuje JS --}}
                    </div>
                </div>

                {{-- Akcije --}}
                <div class="summary-actions">
                    <button class="btn btn-outline-secondary" onclick="axonBuilderBackToEdit()">
                        ←Nazad
                    </button>
                    <button class="btn btn-outline-danger btn-sm ms-auto" onclick="axonBuilderReset()">
                        Novi build
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
@push('scripts')
    @vite('resources/js/pc-builder.js')
@endpush
@endsection

