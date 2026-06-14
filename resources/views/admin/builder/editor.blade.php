<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Bellevie Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #C9A227; --dark: #0D1B2A; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f0f2f5; height: 100vh; overflow: hidden; }

        /* Top bar */
        #builderTopbar {
            height: 56px; background: var(--dark); color: #fff;
            display: flex; align-items: center; padding: 0 16px; gap: 12px;
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            border-bottom: 2px solid var(--gold);
        }
        #builderTopbar .title { flex: 1; font-size: 0.9rem; opacity: 0.85; }
        #builderTopbar .btn-gold { background: var(--gold); color: #fff; border: none; }
        #builderTopbar .btn-gold:hover { background: #b08c20; }

        /* Layout */
        #builderLayout {
            display: flex; height: calc(100vh - 56px);
            margin-top: 56px;
        }

        /* Left sidebar — block palette */
        #blockPalette {
            width: 220px; min-width: 220px; background: #fff;
            border-right: 1px solid #dee2e6; overflow-y: auto;
            display: flex; flex-direction: column;
        }
        #blockPalette h6 {
            padding: 12px 14px 8px; font-size: 0.7rem; text-transform: uppercase;
            letter-spacing: .08em; color: #888; margin: 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .palette-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; cursor: pointer; font-size: 0.82rem;
            border-bottom: 1px solid #f5f5f5; transition: background .15s;
            user-select: none;
        }
        .palette-item:hover { background: #fdf8ea; }
        .palette-item .icon {
            width: 32px; height: 32px; border-radius: 6px;
            background: #f5f0e2; color: var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .palette-item .label { font-weight: 500; }
        .palette-item .desc { font-size: 0.7rem; color: #999; }

        /* Canvas */
        #builderCanvas {
            flex: 1; overflow-y: auto; padding: 24px;
            background: #f0f2f5;
        }
        #sectionList {
            max-width: 860px; margin: 0 auto;
            display: flex; flex-direction: column; gap: 12px;
            min-height: 200px;
        }
        #sectionList.drag-over { outline: 2px dashed var(--gold); border-radius: 8px; }

        .section-card {
            background: #fff; border-radius: 10px;
            border: 2px solid transparent;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            transition: border-color .15s, box-shadow .15s;
            position: relative; overflow: hidden;
        }
        .section-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,.12); }
        .section-card.selected { border-color: var(--gold); }

        .section-card-header {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 12px; background: #fafafa;
            border-bottom: 1px solid #eee; cursor: move;
        }
        .section-card-header .drag-handle { cursor: grab; color: #ccc; font-size: 1rem; }
        .section-card-header .block-label { flex: 1; font-size: 0.78rem; font-weight: 600; color: #555; }
        .section-card-header .actions { display: flex; gap: 4px; }
        .section-card-header .actions button {
            border: none; background: none; padding: 2px 6px;
            font-size: 0.8rem; color: #888; cursor: pointer; border-radius: 4px;
        }
        .section-card-header .actions button:hover { background: #f0f0f0; color: #333; }

        .section-preview { padding: 16px; min-height: 60px; }

        /* Empty canvas */
        #emptyCanvas {
            text-align: center; padding: 60px 20px; color: #bbb;
            border: 2px dashed #ddd; border-radius: 12px; background: #fff;
        }
        #emptyCanvas i { font-size: 3rem; }

        /* Right settings panel */
        #settingsPanel {
            width: 300px; min-width: 300px; background: #fff;
            border-left: 1px solid #dee2e6; overflow-y: auto;
            display: flex; flex-direction: column;
        }
        #settingsPanel h6 {
            padding: 14px 16px 10px; margin: 0; font-size: 0.78rem;
            text-transform: uppercase; letter-spacing: .08em; color: #888;
            border-bottom: 1px solid #f0f0f0; background: #fafafa;
        }
        #settingsContent { padding: 16px; }
        #settingsContent .form-label { font-size: 0.78rem; font-weight: 600; color: #555; margin-bottom: 4px; }
        #settingsContent .form-control,
        #settingsContent .form-select { font-size: 0.82rem; }
        #settingsContent .section-divider {
            font-size: 0.68rem; text-transform: uppercase; letter-spacing: .08em;
            color: #aaa; margin: 14px 0 8px; padding-bottom: 4px;
            border-bottom: 1px solid #f0f0f0;
        }
        #noSelection { padding: 32px 16px; text-align: center; color: #bbb; font-size: 0.82rem; }

        /* Image picker mini */
        .img-pick-wrap { position: relative; }
        .img-pick-preview {
            width: 100%; height: 90px; object-fit: cover;
            border-radius: 6px; border: 2px solid #dee2e6;
            display: block; background: #f5f5f5;
            cursor: pointer;
        }
        .img-pick-preview.has-img { border-color: var(--gold); }
        .img-pick-btn {
            margin-top: 4px; font-size: 0.75rem; width: 100%;
        }

        /* Save indicator */
        #saveStatus { font-size: 0.75rem; opacity: .7; }
        #saveStatus.saved { color: #4caf50; }
        #saveStatus.saving { color: #ff9800; }
        #saveStatus.error { color: #f44336; }
    </style>
</head>
<body>

{{-- Top bar --}}
<div id="builderTopbar">
    <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="title"><i class="bi bi-grid-1x2 me-1"></i>{{ $title }}</span>
    <span id="saveStatus"></span>
    <a href="{{ $previewUrl }}" target="_blank" class="btn btn-sm btn-outline-light">
        <i class="bi bi-eye me-1"></i>Preview
    </a>
    <button class="btn btn-sm btn-gold" id="btnSave" onclick="builderSave()">
        <i class="bi bi-floppy me-1"></i>Save
    </button>
</div>

{{-- Three-column layout --}}
<div id="builderLayout">

    {{-- Left: block palette --}}
    <div id="blockPalette">
        <h6>Blocks</h6>
        <div id="paletteList"></div>
    </div>

    {{-- Center: canvas --}}
    <div id="builderCanvas">
        <div id="sectionList"></div>
    </div>

    {{-- Right: settings --}}
    <div id="settingsPanel">
        <h6><i class="bi bi-sliders me-1"></i>Block Settings</h6>
        <div id="settingsContent">
            <div id="noSelection">
                <i class="bi bi-cursor" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
                Click a block to edit its settings
            </div>
        </div>
    </div>

</div>

{{-- Image library modal (reuse existing) --}}
<div class="modal fade" id="ipLibraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-images me-2"></i>Media Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="ipLibrarySearch" class="form-control" placeholder="Search…" oninput="ipLibraryLoad(1)">
                </div>
                <div id="ipLibraryGrid" class="row g-2">
                    <div class="col-12 text-center py-4 text-muted">Loading…</div>
                </div>
                <div id="ipLibraryPager" class="d-flex justify-content-center gap-2 mt-3"></div>
            </div>
            <div class="modal-footer">
                <small class="text-muted me-auto" id="ipLibrarySelected">0 selected</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:#C9A227;border:none;" onclick="builderLibraryConfirm()">
                    Use Image
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
// ════════════════════════════════════════════════════════════════════════════
//  BLOCK TYPE DEFINITIONS
// ════════════════════════════════════════════════════════════════════════════
const BLOCK_TYPES = {

  // ── Hero ──────────────────────────────────────────────────────────────────
  hero: {
    label: 'Hero Banner', icon: 'bi-image', desc: 'Full-width hero with image & CTA',
    defaults: {
      title: 'Welcome to Bellevie Hotel',
      subtitle: 'Experience luxury and comfort in the heart of the city.',
      ctaText: 'Book Now', ctaUrl: '/booking',
      ctaText2: '', ctaUrl2: '',
      backgroundImageUrl: '', backgroundImageId: null,
      overlay: '0.5', minHeight: '500', textAlign: 'center',
    },
    fields: [
      { key: 'title',            label: 'Title',                    type: 'text' },
      { key: 'subtitle',         label: 'Subtitle',                 type: 'textarea' },
      { key: 'ctaText',          label: 'Primary Button Text',      type: 'text' },
      { key: 'ctaUrl',           label: 'Primary Button URL',       type: 'text' },
      { key: 'ctaText2',         label: 'Secondary Button Text',    type: 'text' },
      { key: 'ctaUrl2',          label: 'Secondary Button URL',     type: 'text' },
      { key: 'backgroundImageId', label: 'Background Image',        type: 'image' },
      { key: 'overlay',          label: 'Overlay Darkness (0–1)',   type: 'range', min: 0, max: 1, step: 0.05 },
      { key: 'minHeight',        label: 'Min Height (px)',          type: 'number' },
      { key: 'textAlign',        label: 'Text Align',               type: 'select', options: ['left','center','right'] },
    ],
    preview(cfg) {
      const bg = cfg.backgroundImageUrl
        ? `url('${cfg.backgroundImageUrl}') center/cover`
        : `linear-gradient(135deg, #0D1B2A 0%, #1a3a5c 100%)`;
      return `<div style="background:${bg};min-height:${cfg.minHeight||300}px;display:flex;align-items:center;position:relative;border-radius:6px;overflow:hidden;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,${cfg.overlay||0.5});"></div>
        <div style="position:relative;text-align:${cfg.textAlign||'center'};padding:40px;color:#fff;width:100%;">
          <h2 style="font-family:serif;margin-bottom:12px;">${cfg.title||'Hero Title'}</h2>
          <p style="opacity:.85;margin-bottom:20px;">${cfg.subtitle||''}</p>
          <div style="display:flex;gap:10px;justify-content:${cfg.textAlign==='left'?'flex-start':cfg.textAlign==='right'?'flex-end':'center'};flex-wrap:wrap;">
            ${cfg.ctaText?`<span style="background:#C9A227;color:#fff;padding:10px 24px;border-radius:4px;font-size:0.85rem;">${cfg.ctaText}</span>`:''}
            ${cfg.ctaText2?`<span style="border:2px solid #fff;color:#fff;padding:10px 24px;border-radius:4px;font-size:0.85rem;">${cfg.ctaText2}</span>`:''}
          </div>
        </div>
      </div>`;
    }
  },

  // ── About ─────────────────────────────────────────────────────────────────
  about: {
    label: 'About Section', icon: 'bi-building', desc: 'Image + text about section',
    _presets: {
      compact:       { label: 'Compact — just a peek',               css: '320px' },
      standard:      { label: 'Standard — works everywhere',         css: '520px' },
      tall:          { label: 'Tall — bold impression',              css: '680px' },
      extratall:     { label: 'Extra Tall — very dramatic',          css: '820px' },
      fullscreen:    { label: 'Full Screen — fills the whole window', css: '100vh' },
      almostfull:    { label: 'Almost Full Screen',                   css: '90vh'  },
      threequarters: { label: 'Three Quarters of Screen',             css: '75vh'  },
    },
    _breakpoints: [
      { key:'phone',   icon:'bi-phone',   label:'Phone',   note:'Up to 575px wide' },
      { key:'tablet',  icon:'bi-tablet',  label:'Tablet',  note:'576 – 991px wide' },
      { key:'laptop',  icon:'bi-laptop',  label:'Laptop',  note:'992 – 1199px wide' },
      { key:'desktop', icon:'bi-display', label:'Desktop', note:'1200px and wider' },
    ],
    defaults: {
      label: 'ABOUT BELLEVIE',
      title: 'A Legacy of Luxury & Comfort',
      content: '<p>Experience the finest hospitality in a setting that blends timeless elegance with modern comfort. Our dedicated team ensures every stay exceeds expectations.</p>',
      imageId: null, imageUrl: '',
      imagePosition: 'left',
      ctaText: 'Learn More', ctaUrl: '/contact',
      bgColor: '#ffffff',
      stat1Value: '50+', stat1Label: 'Luxury Rooms',
      stat2Value: '15+', stat2Label: 'Years Experience',
      stat3Value: '98%', stat3Label: 'Guest Satisfaction',
      heights: {
        phone:   { preset: 'standard', custom: '520' },
        tablet:  { preset: 'standard', custom: '520' },
        laptop:  { preset: 'standard', custom: '520' },
        desktop: { preset: 'standard', custom: '520' },
      },
    },
    fields: [
      { key: 'label',         label: 'Eyebrow Label',                    type: 'text' },
      { key: 'title',         label: 'Title',                            type: 'text' },
      { key: 'content',       label: 'Content',                          type: 'richtext' },
      { key: 'imageId',       label: 'Image',                            type: 'image' },
      { key: 'imagePosition', label: 'Image Position',                   type: 'select', options: ['left','right'] },
      { key: 'ctaText',       label: 'Button Text',                      type: 'text' },
      { key: 'ctaUrl',        label: 'Button URL',                       type: 'text' },
      { key: 'bgColor',       label: 'Background',                       type: 'color' },
      { key: 'stat1Value',    label: 'Stat 1 Value',                     type: 'text' },
      { key: 'stat1Label',    label: 'Stat 1 Label',                     type: 'text' },
      { key: 'stat2Value',    label: 'Stat 2 Value',                     type: 'text' },
      { key: 'stat2Label',    label: 'Stat 2 Label',                     type: 'text' },
      { key: 'stat3Value',    label: 'Stat 3 Value',                     type: 'text' },
      { key: 'stat3Label',    label: 'Stat 3 Label',                     type: 'text' },
      { key: 'heights',       label: 'Section Height per Screen Size',   type: 'breakpoint-heights' },
    ],
    preview(cfg) {
      const imgEl = cfg.imageUrl
        ? `<img src="${cfg.imageUrl}" style="width:100%;border-radius:8px;border:3px solid #C9A227;">`
        : `<div style="width:100%;height:160px;background:linear-gradient(135deg,#e0d4b0,#c9a227);border-radius:8px;border:3px solid #C9A22740;"></div>`;
      const textEl = `<div style="padding:0 16px;">
        <div style="color:#C9A227;font-size:0.65rem;font-weight:700;letter-spacing:.1em;margin-bottom:8px;">${cfg.label||''}</div>
        <h4 style="font-family:serif;margin-bottom:10px;">${cfg.title||'About'}</h4>
        <div style="font-size:0.8rem;color:#555;margin-bottom:12px;">${cfg.content||''}</div>
        ${cfg.ctaText?`<span style="background:#C9A227;color:#fff;padding:8px 18px;border-radius:4px;font-size:0.78rem;">${cfg.ctaText}</span>`:''}
      </div>`;
      return `<div style="background:${cfg.bgColor||'#fff'};padding:16px;">
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
          <div style="flex:1;min-width:140px;">${cfg.imagePosition==='right'?textEl:imgEl}</div>
          <div style="flex:1;min-width:140px;">${cfg.imagePosition==='right'?imgEl:textEl}</div>
        </div>
      </div>`;
    }
  },

  // ── Why Choose ───────────────────────────────────────────────────────────
  'why-choose': {
    label: 'Why Choose Us', icon: 'bi-star-fill', desc: 'Feature highlights with icons',
    defaults: {
      title: 'Why Choose Bellevie',
      subtitle: 'We go beyond ordinary hospitality',
      columns: '3', bgColor: '#f5f0e8',
      features: [
        { icon: 'bi-award', title: 'Award Winning', desc: 'Recognized for excellence in luxury hospitality.' },
        { icon: 'bi-heart', title: 'Personalized Service', desc: 'Every detail tailored to your preferences.' },
        { icon: 'bi-geo-alt', title: 'Prime Location', desc: 'Situated in the heart of the city centre.' },
        { icon: 'bi-shield-check', title: 'Safe & Secure', desc: '24/7 security and guest privacy guaranteed.' },
        { icon: 'bi-cup-hot', title: 'Fine Dining', desc: 'World-class cuisine from our award-winning chefs.' },
        { icon: 'bi-wifi', title: 'Modern Amenities', desc: 'High-speed WiFi and cutting-edge facilities.' },
      ],
    },
    fields: [
      { key: 'title',    label: 'Section Title', type: 'text' },
      { key: 'subtitle', label: 'Subtitle',      type: 'text' },
      { key: 'columns',  label: 'Columns',       type: 'select', options: ['2','3','4'] },
      { key: 'bgColor',  label: 'Background',    type: 'color' },
      { key: 'features', label: 'Features',      type: 'feature-list' },
    ],
    preview(cfg) {
      const feats = (cfg.features||[]).slice(0,parseInt(cfg.columns)||3);
      const cols = feats.map(f => `
        <div style="flex:1;min-width:120px;text-align:center;padding:14px;">
          <div style="width:44px;height:44px;border-radius:50%;background:#C9A22720;color:#C9A227;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:1.1rem;">
            <i class="bi ${f.icon||'bi-star'}"></i>
          </div>
          <div style="font-size:0.8rem;font-weight:600;margin-bottom:4px;">${f.title||''}</div>
          <div style="font-size:0.72rem;color:#888;">${f.desc||''}</div>
        </div>`).join('');
      return `<div style="background:${cfg.bgColor||'#f5f0e8'};padding:24px;border-radius:8px;">
        <div style="text-align:center;margin-bottom:16px;">
          <h4 style="font-family:serif;">${cfg.title||'Why Choose Us'}</h4>
          <p style="color:#888;font-size:0.82rem;">${cfg.subtitle||''}</p>
        </div>
        <div style="display:flex;flex-wrap:wrap;">${cols}</div>
      </div>`;
    }
  },

  // ── Rooms ─────────────────────────────────────────────────────────────────
  rooms: {
    label: 'Rooms Grid', icon: 'bi-door-open', desc: 'Display room cards',
    defaults: {
      title: 'Our Rooms & Suites', subtitle: 'Find your perfect stay',
      perRow: '3', maxRows: '1', featuredOnly: false,
      showPrice: true, showDescription: true,
      btnText: 'View Room', btnUrl: '',
    },
    fields: [
      { key: 'title',           label: 'Section Title',     type: 'text' },
      { key: 'subtitle',        label: 'Subtitle',          type: 'text' },
      { key: 'perRow',          label: 'Items Per Row',     type: 'select', options: ['1','2','3','4'] },
      { key: 'maxRows',         label: 'Max Rows to Show',  type: 'select', options: ['1','2','3','4','all'] },
      { key: 'featuredOnly',    label: 'Featured Only',     type: 'toggle' },
      { key: 'showPrice',       label: 'Show Price',        type: 'toggle' },
      { key: 'showDescription', label: 'Show Description',  type: 'toggle' },
      { key: 'btnText',         label: 'Button Text',       type: 'text' },
      { key: 'btnUrl',          label: 'Override Button URL (leave blank for room page)', type: 'text' },
    ],
    preview(cfg) {
      const perRow = parseInt(cfg.perRow)||3;
      const maxRows = cfg.maxRows === 'all' ? 4 : parseInt(cfg.maxRows)||1;
      const count = perRow * maxRows;
      const cards = Array(Math.min(count, 8)).fill(0).map(() => `
        <div style="flex:0 0 calc(${100/perRow}% - 12px);min-width:100px;background:#f5f5f5;border-radius:8px;overflow:hidden;">
          <div style="height:80px;background:linear-gradient(135deg,#e0d4b0,#c9a227);"></div>
          <div style="padding:8px;">
            <div style="height:9px;background:#ddd;border-radius:3px;margin-bottom:5px;width:80%;"></div>
            ${cfg.showDescription?`<div style="height:7px;background:#eee;border-radius:3px;width:60%;margin-bottom:5px;"></div>`:''}
            ${cfg.showPrice?`<div style="color:#C9A227;font-size:0.7rem;font-weight:bold;margin-bottom:6px;">From $X/night</div>`:''}
            ${cfg.btnText?`<div style="background:#C9A227;color:#fff;padding:4px 8px;border-radius:3px;font-size:0.68rem;text-align:center;">${cfg.btnText}</div>`:''}
          </div>
        </div>`).join('');
      return `<div>
        <div style="text-align:center;margin-bottom:12px;">
          <h4 style="font-family:serif;">${cfg.title||'Our Rooms'}</h4>
          <p style="color:#888;font-size:0.8rem;">${cfg.subtitle||''}</p>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">${cards}</div>
      </div>`;
    }
  },

  // ── Testimonials ─────────────────────────────────────────────────────────
  testimonials: {
    label: 'Testimonials', icon: 'bi-chat-quote', desc: 'Guest reviews — grid or slider',
    defaults: {
      title: 'What Our Guests Say', count: '6',
      layout: 'grid', columns: '3', bgColor: '#f5f0e8',
      showAvatar: true, showCountry: true,
    },
    fields: [
      { key: 'title',       label: 'Section Title',  type: 'text' },
      { key: 'count',       label: 'Total to Load',  type: 'number' },
      { key: 'layout',      label: 'Layout',         type: 'select', options: ['grid','slider'] },
      { key: 'columns',     label: 'Grid Columns (grid only)', type: 'select', options: ['2','3','4'] },
      { key: 'bgColor',     label: 'Background',     type: 'color' },
      { key: 'showAvatar',  label: 'Show Avatar',    type: 'toggle' },
      { key: 'showCountry', label: 'Show Country',   type: 'toggle' },
    ],
    preview(cfg) {
      const isSlider = cfg.layout === 'slider';
      const displayCount = isSlider ? 1 : Math.min(parseInt(cfg.columns)||3, 3);
      const cards = Array(displayCount).fill(0).map(() => `
        <div style="flex:1;min-width:130px;background:#fff;border-radius:8px;padding:12px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
          <div style="color:#C9A227;font-size:0.75rem;margin-bottom:6px;">★★★★★</div>
          <div style="height:6px;background:#eee;border-radius:3px;margin-bottom:3px;"></div>
          <div style="height:6px;background:#eee;border-radius:3px;width:75%;margin-bottom:10px;"></div>
          <div style="display:flex;align-items:center;gap:6px;">
            <div style="width:24px;height:24px;border-radius:50%;background:#ddd;flex-shrink:0;"></div>
            <div><div style="height:6px;background:#eee;border-radius:3px;width:60px;"></div></div>
          </div>
        </div>`).join('');
      return `<div style="background:${cfg.bgColor||'#f5f0e8'};padding:20px;border-radius:8px;">
        <h4 style="font-family:serif;text-align:center;margin-bottom:14px;">${cfg.title||'Testimonials'}</h4>
        ${isSlider?`<div style="font-size:0.7rem;text-align:center;color:#888;margin-bottom:8px;"><i class="bi bi-arrow-left-right"></i> Slider — shows one at a time</div>`:''}
        <div style="display:flex;gap:10px;flex-wrap:wrap;">${cards}</div>
        ${isSlider?`<div style="text-align:center;margin-top:10px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#C9A227;margin:0 3px;"></span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#ddd;margin:0 3px;"></span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#ddd;margin:0 3px;"></span></div>`:''}
      </div>`;
    }
  },

  // ── Gallery ───────────────────────────────────────────────────────────────
  gallery: {
    label: 'Gallery', icon: 'bi-images', desc: 'Photo gallery grid',
    defaults: { title: 'Gallery', category: '', count: '6', columns: '3' },
    fields: [
      { key: 'title',    label: 'Section Title',                     type: 'text' },
      { key: 'category', label: 'Category Filter (blank = all)',     type: 'text' },
      { key: 'count',    label: 'Images to Show',                    type: 'number' },
      { key: 'columns',  label: 'Columns',                           type: 'select', options: ['2','3','4'] },
    ],
    preview(cfg) {
      const n = Math.min(parseInt(cfg.count)||6, 6);
      const cols = parseInt(cfg.columns)||3;
      const cells = Array(n).fill(0).map((_,i) => `
        <div style="flex:1;min-width:calc(${100/cols}% - 8px);aspect-ratio:1;background:linear-gradient(135deg,#e8e0d0 ${i*15}%,#c9a22740);border-radius:4px;"></div>`).join('');
      return `<div>
        <h4 style="font-family:serif;text-align:center;margin-bottom:12px;">${cfg.title||'Gallery'}</h4>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">${cells}</div>
      </div>`;
    }
  },

  // ── Text Block ────────────────────────────────────────────────────────────
  text: {
    label: 'Text Block', icon: 'bi-file-text', desc: 'Rich text content section',
    defaults: { title: '', content: '<p>Write your content here...</p>', align: 'left', maxWidth: '800', bgColor: '#ffffff', padding: '48' },
    fields: [
      { key: 'title',    label: 'Title (optional)', type: 'text' },
      { key: 'content',  label: 'Content',          type: 'richtext' },
      { key: 'align',    label: 'Alignment',        type: 'select', options: ['left','center','right'] },
      { key: 'maxWidth', label: 'Max Width (px)',   type: 'number' },
      { key: 'bgColor',  label: 'Background',       type: 'color' },
      { key: 'padding',  label: 'Vertical Padding (px)', type: 'number' },
    ],
    preview(cfg) {
      return `<div style="background:${cfg.bgColor||'#fff'};padding:${cfg.padding||32}px 24px;">
        <div style="max-width:${cfg.maxWidth||800}px;margin:0 auto;text-align:${cfg.align||'left'};">
          ${cfg.title?`<h3 style="font-family:serif;margin-bottom:12px;">${cfg.title}</h3>`:''}
          <div style="color:#555;line-height:1.7;font-size:0.88rem;">${cfg.content||''}</div>
        </div>
      </div>`;
    }
  },

  // ── CTA ───────────────────────────────────────────────────────────────────
  cta: {
    label: 'Call to Action', icon: 'bi-megaphone', desc: 'CTA banner — color, image or video background',
    defaults: {
      title: 'Ready to Experience Bellevie?',
      subtitle: 'Book your stay and enjoy our world-class amenities.',
      ctaText: 'Book Now', ctaUrl: '/booking',
      ctaText2: 'Explore Rooms', ctaUrl2: '/rooms',
      bgType: 'color',
      bgColor: '#0D1B2A', textColor: '#ffffff',
      bgImageId: null, bgImageUrl: '',
      bgVideoType: 'youtube', bgVideoUrl: '',
      overlay: '0.6',
      minHeight: '400',
    },
    fields: [
      { key: 'title',      label: 'Title',               type: 'text' },
      { key: 'subtitle',   label: 'Subtitle',            type: 'text' },
      { key: 'ctaText',    label: 'Primary Button Text', type: 'text' },
      { key: 'ctaUrl',     label: 'Primary Button URL',  type: 'text' },
      { key: 'ctaText2',   label: 'Secondary Button Text', type: 'text' },
      { key: 'ctaUrl2',    label: 'Secondary Button URL',  type: 'text' },
      { key: 'textColor',  label: 'Text Color',          type: 'color' },
      { key: 'bgType',     label: 'Background Type',     type: 'select', options: ['color','image','video'] },
      { key: 'bgColor',    label: 'Background Color (if type=color)', type: 'color' },
      { key: 'bgImageId',  label: 'Background Image (if type=image)', type: 'image' },
      { key: 'bgVideoType', label: 'Video Source (if type=video)', type: 'select', options: ['youtube','cloudinary'] },
      { key: 'bgVideoUrl', label: 'Video URL (YouTube or Cloudinary URL)', type: 'text' },
      { key: 'overlay',    label: 'Dark Overlay (0–1)', type: 'range', min: 0, max: 1, step: 0.05 },
      { key: 'minHeight',  label: 'Min Height (px)',    type: 'number' },
    ],
    preview(cfg) {
      let bgStyle = `background:${cfg.bgColor||'#0D1B2A'};`;
      if (cfg.bgType === 'image' && cfg.bgImageUrl) bgStyle = `background:url('${cfg.bgImageUrl}') center/cover;`;
      if (cfg.bgType === 'video') bgStyle = `background:linear-gradient(135deg,#0D1B2A,#1a3a5c);`;
      return `<div style="${bgStyle}min-height:${cfg.minHeight||200}px;padding:40px;text-align:center;border-radius:8px;position:relative;overflow:hidden;">
        ${cfg.bgType==='video'?`<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;opacity:.3;font-size:2rem;color:#fff;"><i class="bi bi-play-circle"></i></div>`:''}
        <div style="position:absolute;inset:0;background:rgba(0,0,0,${cfg.overlay||0.6});"></div>
        <div style="position:relative;color:${cfg.textColor||'#fff'};">
          <h3 style="font-family:serif;margin-bottom:10px;">${cfg.title||'Call to Action'}</h3>
          <p style="opacity:.85;margin-bottom:20px;">${cfg.subtitle||''}</p>
          <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            ${cfg.ctaText?`<span style="background:#C9A227;color:#fff;padding:10px 24px;border-radius:4px;font-size:0.85rem;">${cfg.ctaText}</span>`:''}
            ${cfg.ctaText2?`<span style="border:2px solid rgba(255,255,255,.7);color:#fff;padding:10px 24px;border-radius:4px;font-size:0.85rem;">${cfg.ctaText2}</span>`:''}
          </div>
        </div>
      </div>`;
    }
  },

  // ── Video Block ───────────────────────────────────────────────────────────
  video: {
    label: 'Video Block', icon: 'bi-play-circle', desc: 'YouTube or Cloudinary video with title',
    defaults: {
      title: '', subtitle: '',
      videoType: 'youtube', videoUrl: '',
      autoplay: false, muted: true, loop: false, controls: true,
      aspectRatio: '16/9', maxWidth: '900',
      bgColor: '#000000',
      showOverlayText: false,
    },
    fields: [
      { key: 'title',           label: 'Title (optional)',    type: 'text' },
      { key: 'subtitle',        label: 'Subtitle (optional)', type: 'text' },
      { key: 'videoType',       label: 'Video Source',        type: 'select', options: ['youtube','cloudinary'] },
      { key: 'videoUrl',        label: 'Video URL',           type: 'text' },
      { key: 'aspectRatio',     label: 'Aspect Ratio',        type: 'select', options: ['16/9','4/3','1/1','9/16'] },
      { key: 'maxWidth',        label: 'Max Width (px)',      type: 'number' },
      { key: 'controls',        label: 'Show Controls',       type: 'toggle' },
      { key: 'autoplay',        label: 'Autoplay',            type: 'toggle' },
      { key: 'muted',           label: 'Muted',               type: 'toggle' },
      { key: 'loop',            label: 'Loop',                type: 'toggle' },
      { key: 'bgColor',         label: 'Section Background',  type: 'color' },
    ],
    preview(cfg) {
      return `<div style="background:${cfg.bgColor||'#000'};padding:32px;">
        ${cfg.title?`<div style="text-align:center;margin-bottom:16px;color:#fff;"><h4 style="font-family:serif;">${cfg.title}</h4>${cfg.subtitle?`<p style="opacity:.7;">${cfg.subtitle}</p>`:''}</div>`:''}
        <div style="max-width:${cfg.maxWidth||600}px;margin:0 auto;aspect-ratio:${cfg.aspectRatio||'16/9'};background:#111;border-radius:8px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
          <div style="text-align:center;color:#888;">
            <i class="bi bi-${cfg.videoType==='youtube'?'youtube':'play-circle-fill'}" style="font-size:2.5rem;color:${cfg.videoType==='youtube'?'#ff0000':'#C9A227'};display:block;margin-bottom:8px;"></i>
            <small>${cfg.videoUrl?cfg.videoUrl.slice(0,40)+'…':'No URL set'}</small>
          </div>
        </div>
      </div>`;
    }
  },

  // ── Floating Button ───────────────────────────────────────────────────────
  'floating-btn': {
    label: 'Floating Button', icon: 'bi-cursor-fill', desc: 'Fixed sticky CTA button',
    defaults: {
      label: 'Book Now', url: '/booking', icon: 'bi-calendar-check',
      position: 'bottom-right',
      bgColor: '#C9A227', textColor: '#ffffff',
      desktopShow: true, mobileAlwaysBottom: true,
      size: 'normal',
    },
    fields: [
      { key: 'label',              label: 'Button Label',         type: 'text' },
      { key: 'url',                label: 'Link URL',             type: 'text' },
      { key: 'icon',               label: 'Bootstrap Icon class (e.g. bi-telephone)', type: 'text' },
      { key: 'bgColor',            label: 'Background Color',     type: 'color' },
      { key: 'textColor',          label: 'Text Color',           type: 'color' },
      { key: 'position',           label: 'Desktop Position',     type: 'select', options: ['bottom-right','bottom-left','bottom-center','top-right','top-left'] },
      { key: 'size',               label: 'Size',                 type: 'select', options: ['normal','large'] },
      { key: 'desktopShow',        label: 'Show on Desktop',      type: 'toggle' },
      { key: 'mobileAlwaysBottom', label: 'Mobile: stick to bottom bar', type: 'toggle' },
    ],
    preview(cfg) {
      return `<div style="padding:24px;background:#f5f5f5;border-radius:8px;text-align:center;">
        <p style="color:#888;font-size:0.8rem;margin-bottom:12px;"><i class="bi bi-info-circle"></i> This button floats fixed on the page</p>
        <span style="display:inline-flex;align-items:center;gap:8px;background:${cfg.bgColor||'#C9A227'};color:${cfg.textColor||'#fff'};padding:${cfg.size==='large'?'14px 28px':'10px 20px'};border-radius:50px;font-size:${cfg.size==='large'?'1rem':'0.88rem'};font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);">
          ${cfg.icon?`<i class="bi ${cfg.icon}"></i>`:''}
          ${cfg.label||'Book Now'}
        </span>
        <div style="margin-top:10px;color:#aaa;font-size:0.72rem;">Position: ${cfg.position||'bottom-right'} · Mobile: ${cfg.mobileAlwaysBottom?'bottom bar':'same'}</div>
      </div>`;
    }
  },

  // ── Contact ───────────────────────────────────────────────────────────────
  contact: {
    label: 'Contact Info', icon: 'bi-geo-alt', desc: 'Hotel contact details',
    defaults: { title: 'Get In Touch', showMap: true, showForm: false, bgColor: '#ffffff' },
    fields: [
      { key: 'title',    label: 'Section Title',       type: 'text' },
      { key: 'showMap',  label: 'Show Map Placeholder', type: 'toggle' },
      { key: 'showForm', label: 'Show Contact Form',   type: 'toggle' },
      { key: 'bgColor',  label: 'Background',          type: 'color' },
    ],
    preview(cfg) {
      return `<div style="background:${cfg.bgColor||'#fff'};padding:24px;border-radius:8px;">
        <h4 style="font-family:serif;margin-bottom:16px;">${cfg.title||'Contact'}</h4>
        <div style="display:flex;gap:16px;flex-wrap:wrap;">
          <div style="flex:1;min-width:140px;display:flex;flex-direction:column;gap:10px;">
            ${['Address','Phone','Email'].map(l=>`<div style="display:flex;gap:8px;align-items:center;"><div style="width:20px;height:20px;border-radius:50%;background:#C9A22720;flex-shrink:0;"></div><div style="height:7px;background:#eee;border-radius:3px;flex:1;"></div></div>`).join('')}
          </div>
          ${cfg.showMap?`<div style="flex:1;min-width:140px;height:90px;background:#e0e8f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:0.72rem;"><i class="bi bi-map me-1"></i>Map</div>`:''}
        </div>
      </div>`;
    }
  },

  // ── Parallax Section ─────────────────────────────────────────────────────
  parallax: {
    label: 'Parallax Section', icon: 'bi-layers', desc: 'Full-width section with depth effect — image or video background',
    _presets: {
      compact:       { label: 'Compact — just a peek',               css: '320px' },
      standard:      { label: 'Standard — works everywhere',         css: '520px' },
      tall:          { label: 'Tall — bold impression',              css: '680px' },
      extratall:     { label: 'Extra Tall — very dramatic',          css: '820px' },
      fullscreen:    { label: 'Full Screen — fills the whole window', css: '100vh' },
      almostfull:    { label: 'Almost Full Screen',                   css: '90vh'  },
      threequarters: { label: 'Three Quarters of Screen',             css: '75vh'  },
    },
    _breakpoints: [
      { key:'phone',   icon:'bi-phone',   label:'Phone',   note:'Up to 575px wide' },
      { key:'tablet',  icon:'bi-tablet',  label:'Tablet',  note:'576 – 991px wide' },
      { key:'laptop',  icon:'bi-laptop',  label:'Laptop',  note:'992 – 1199px wide' },
      { key:'desktop', icon:'bi-display', label:'Desktop', note:'1200px and wider' },
    ],
    defaults: {
      bgType: 'image',
      bgImageId: null, bgImageUrl: '',
      bgVideoType: 'youtube',
      bgVideoUrl: '',
      overlay: '0.5',
      parallaxSpeed: '0.4',
      title: 'Experience Bellevie Hotel',
      description: 'Discover a world of luxury, comfort, and unforgettable moments.',
      textPosition: 'center',
      heights: {
        phone:   { preset: 'standard',   custom: '520' },
        tablet:  { preset: 'tall',       custom: '680' },
        laptop:  { preset: 'tall',       custom: '680' },
        desktop: { preset: 'extratall',  custom: '820' },
      },
      buttons: [
        { text: 'Discover More', link: '/about', action: 'link', modalType: 'text', modalTitle: '', modalDescription: '', modalVideoType: 'youtube', modalVideoUrl: '' },
      ],
    },
    fields: [
      { key: 'bgType',        label: 'Background Type',                                  type: 'select', options: ['image','video'] },
      { key: 'bgImageId',     label: 'Background Image (when type = Image)',             type: 'image' },
      { key: 'bgVideoType',   label: 'Video Source (when type = Video)',                 type: 'select', options: ['youtube','cloudinary'] },
      { key: 'bgVideoUrl',    label: 'Video URL — paste a YouTube or Cloudinary link',   type: 'text' },
      { key: 'overlay',       label: 'Darkness over background (0 = clear, 1 = black)',  type: 'range', min: 0, max: 1, step: 0.05 },
      { key: 'parallaxSpeed', label: 'Parallax depth (0 = flat, 0.5 = gentle, 1 = deep)', type: 'range', min: 0, max: 1, step: 0.1 },
      { key: 'heights',       label: 'Section Height per Screen Size',                   type: 'breakpoint-heights' },
      { key: 'title',         label: 'Heading',                                          type: 'text' },
      { key: 'description',   label: 'Description',                                      type: 'textarea' },
      { key: 'textPosition',  label: 'Text Alignment',                                   type: 'select', options: ['left','center','right'] },
      { key: 'buttons',       label: 'Buttons',                                          type: 'button-list' },
    ],
    _resolveHeight(bpCfg) {
      if (!bpCfg) return '520px';
      if (bpCfg.preset === 'custom') return (parseInt(bpCfg.custom) || 520) + 'px';
      return (this._presets[bpCfg.preset] || this._presets.standard).css;
    },
    preview(cfg) {
      const heights  = cfg.heights || {};
      const h        = this._resolveHeight(heights.desktop);
      const align    = cfg.textPosition === 'left' ? 'flex-start' : cfg.textPosition === 'right' ? 'flex-end' : 'center';
      const tAlign   = cfg.textPosition || 'center';
      const isVideo  = cfg.bgType === 'video';
      const bg       = (!isVideo && cfg.bgImageUrl)
        ? `url('${cfg.bgImageUrl}') center/cover no-repeat`
        : `linear-gradient(135deg,#0D1B2A 0%,#1e3d5c 50%,#0a2440 100%)`;
      const buttons  = (cfg.buttons || []).filter(b => b.text);
      const btnHtml  = buttons.map(b => {
        const isModal = b.action === 'modal';
        return `<span style="display:inline-block;margin:4px;padding:11px 26px;border-radius:4px;font-size:0.82rem;font-weight:600;
          background:${isModal?'transparent':'#C9A227'};color:#fff;
          border:${isModal?'2px solid rgba(255,255,255,.7)':'none'};">${b.text}${isModal?' ↗':''}</span>`;
      }).join('');
      const videoOverlay = isVideo ? `<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;opacity:.25;">
        <i class="bi bi-play-circle-fill" style="font-size:3rem;color:#fff;"></i></div>` : '';
      const bpSummary = ['phone','tablet','laptop','desktop'].map(k =>
        `${k[0].toUpperCase()}:${this._resolveHeight(heights[k])}`).join(' · ');
      return `<div style="position:relative;background:${bg};min-height:${h};border-radius:6px;overflow:hidden;display:flex;flex-direction:column;">
        ${videoOverlay}
        <div style="position:absolute;inset:0;background:rgba(0,0,0,${cfg.overlay||0.5});"></div>
        <div style="position:relative;flex:1;display:flex;align-items:center;justify-content:${align};padding:40px;">
          <div style="max-width:600px;text-align:${tAlign};color:#fff;">
            ${cfg.title?`<h2 style="font-family:serif;font-size:1.5rem;margin-bottom:10px;text-shadow:0 2px 8px rgba(0,0,0,.4);">${cfg.title}</h2>`:''}
            ${cfg.description?`<p style="opacity:.88;margin-bottom:18px;font-size:0.88rem;line-height:1.6;">${cfg.description}</p>`:''}
            ${btnHtml}
          </div>
        </div>
        <div style="position:absolute;bottom:8px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.55);color:#aaa;font-size:.58rem;padding:2px 10px;border-radius:10px;white-space:nowrap;">
          📐 ${bpSummary} · ${isVideo?'Video':'Parallax image'}
        </div>
      </div>`;
    }
  },

  // ── FAQ ───────────────────────────────────────────────────────────────────
  faq: {
    label: 'FAQ Section', icon: 'bi-question-circle', desc: 'Frequently asked questions list',
    defaults: {
      title: 'Frequently Asked Questions',
      subtitle: '',
      category: 'all',
      layout: 'expandable',
      bgColor: '#ffffff',
      maxItems: '10',
    },
    fields: [
      { key: 'title',    label: 'Section Title',   type: 'text' },
      { key: 'subtitle', label: 'Subtitle (optional)', type: 'text' },
      { key: 'category', label: 'FAQ Category',    type: 'select', options: ['all','booking','hotel','restaurant','conference','finance','general'] },
      { key: 'layout',   label: 'Display Style — "expandable" = click to reveal answer / "rows" = always show both title & answer', type: 'select', options: ['expandable','rows'] },
      { key: 'maxItems', label: 'Max Items to Show', type: 'number' },
      { key: 'bgColor',  label: 'Background',      type: 'color' },
    ],
    _categoryLabels: { all:'All Categories', booking:'Booking', hotel:'Hotel', restaurant:'Restaurant', conference:'Conference', finance:'Finance', general:'General' },
    preview(cfg) {
      const catLabel = this._categoryLabels[cfg.category] || cfg.category;
      const isExpandable = cfg.layout !== 'rows';
      const sampleQA = [
        { q: 'What time is check-in and check-out?', a: 'Check-in is from 3:00 PM and check-out is until 11:00 AM. Early check-in and late check-out can be arranged subject to availability.' },
        { q: 'Is breakfast included in the room rate?', a: 'Breakfast is included in select room packages. Please check your booking details or contact us for more information.' },
        { q: 'Do you offer airport transfers?', a: 'Yes, we provide complimentary airport transfer for guests staying 2 or more nights. Please inform us of your flight details in advance.' },
      ];
      const rows = sampleQA.map((item, i) => {
        if (isExpandable) {
          return `<div style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;${i>0?'margin-top:8px':''}">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;cursor:pointer;background:${i===0?'#fdf8ea':'#fff'};">
              <span style="font-weight:600;font-size:0.88rem;color:#1a1a2e;">${item.q}</span>
              <span style="color:#C9A227;font-size:1rem;flex-shrink:0;margin-left:12px;">${i===0?'−':'+'}</span>
            </div>
            ${i===0?`<div style="padding:0 16px 14px;font-size:0.82rem;color:#555;line-height:1.6;border-top:1px solid #f0f0f0;">${item.a}</div>`:''}
          </div>`;
        } else {
          return `<div style="padding:16px 0;${i>0?'border-top:1px solid #eee':''}">
            <div style="font-weight:600;font-size:0.88rem;color:#1a1a2e;margin-bottom:6px;">${item.q}</div>
            <div style="font-size:0.82rem;color:#666;line-height:1.6;">${item.a}</div>
          </div>`;
        }
      }).join('');
      return `<div style="background:${cfg.bgColor||'#fff'};padding:24px;border-radius:8px;">
        <div style="text-align:center;margin-bottom:20px;">
          <h4 style="font-family:serif;margin-bottom:6px;">${cfg.title||'FAQ'}</h4>
          ${cfg.subtitle?`<p style="color:#888;font-size:0.82rem;">${cfg.subtitle}</p>`:''}
          <span style="display:inline-block;background:#C9A22715;color:#C9A227;border:1px solid #C9A22730;padding:3px 12px;border-radius:20px;font-size:0.72rem;font-weight:600;">${catLabel}</span>
        </div>
        <div>${rows}</div>
        <div style="text-align:center;margin-top:12px;color:#bbb;font-size:0.72rem;">
          Style: ${isExpandable?'Expandable List (click to reveal answer)':'Simple Rows (title + description visible)'}
        </div>
      </div>`;
    }
  },

  // ── Hero Slider ───────────────────────────────────────────────────────────
  'hero-slider': {
    label: 'Hero Image Slider', icon: 'bi-collection-play', desc: 'Full-width slider with multiple hero slides',

    // Preset heights: value is the actual CSS min-height value
    _presets: {
      compact:    { label: 'Compact — just a peek',        css: '320px' },
      standard:   { label: 'Standard — works everywhere',  css: '520px' },
      tall:       { label: 'Tall — bold impression',        css: '680px' },
      extratall:  { label: 'Extra Tall — very dramatic',   css: '820px' },
      fullscreen: { label: 'Full Screen — fills the whole window', css: '100vh' },
      almostfull: { label: 'Almost Full Screen',           css: '90vh' },
      threequarters: { label: 'Three Quarters of Screen',  css: '75vh' },
      custom:     { label: 'Custom size…',                 css: null },
    },

    // Breakpoints shown in the UI
    _breakpoints: [
      { key: 'phone',   icon: 'bi-phone',   label: 'Phone',   note: 'Up to 575px wide' },
      { key: 'tablet',  icon: 'bi-tablet',  label: 'Tablet',  note: '576 – 991px wide' },
      { key: 'laptop',  icon: 'bi-laptop',  label: 'Laptop',  note: '992 – 1199px wide' },
      { key: 'desktop', icon: 'bi-display', label: 'Desktop', note: '1200px and wider' },
    ],

    defaults: {
      animation: 'fade',
      autoplay: true,
      autoplaySpeed: '5',
      overlay: '0.4',
      heights: {
        phone:   { preset: 'compact',   custom: '320' },
        tablet:  { preset: 'standard',  custom: '520' },
        laptop:  { preset: 'tall',      custom: '680' },
        desktop: { preset: 'extratall', custom: '820' },
      },
      slides: [
        { imageId: null, imageUrl: '', title: 'Welcome to Bellevie', description: 'Experience luxury and comfort in the heart of the city.', buttonText: 'Book Now', buttonLink: '/booking', textPosition: 'center' },
        { imageId: null, imageUrl: '', title: 'Unmatched Elegance', description: 'Where every detail is crafted to perfection.', buttonText: '', buttonLink: '', textPosition: 'left' },
      ],
    },

    fields: [
      { key: 'heights',      label: 'Slider Height per Screen Size',  type: 'breakpoint-heights' },
      { key: 'animation',    label: 'Transition Effect',              type: 'select', options: ['fade','slide','zoom'] },
      { key: 'autoplay',     label: 'Auto-advance slides',            type: 'toggle' },
      { key: 'autoplaySpeed',label: 'Seconds per slide',              type: 'number' },
      { key: 'overlay',      label: 'Dark overlay (0 = none, 1 = full black)', type: 'range', min: 0, max: 1, step: 0.05 },
      { key: 'slides',       label: 'Slides',                         type: 'slide-list' },
    ],

    _resolveHeight(bpCfg) {
      if (!bpCfg) return '520px';
      if (bpCfg.preset === 'custom') return (parseInt(bpCfg.custom) || 520) + 'px';
      return (this._presets[bpCfg.preset] || this._presets.standard).css;
    },

    preview(cfg) {
      const slides = Array.isArray(cfg.slides) ? cfg.slides : [];
      const heights = cfg.heights || {};
      const desktopH = this._resolveHeight(heights.desktop);

      const dots = slides.map((_, i) => `<span style="display:inline-block;width:${i===0?'24px':'8px'};height:8px;border-radius:20px;background:${i===0?'#C9A227':'rgba(255,255,255,.5)'};margin:0 3px;transition:width .3s;"></span>`).join('');
      const slide = slides[0] || {};
      const align     = slide.textPosition === 'left' ? 'flex-start' : slide.textPosition === 'right' ? 'flex-end' : 'center';
      const textAlign = slide.textPosition === 'left' ? 'left' : slide.textPosition === 'right' ? 'right' : 'center';
      const bg = slide.imageUrl
        ? `url('${slide.imageUrl}') center/cover no-repeat`
        : `linear-gradient(135deg,#0D1B2A 0%,#1a3a5c 60%,#C9A22730 100%)`;

      // Compact height summary for the preview badge
      const bpSummary = ['phone','tablet','laptop','desktop'].map(k => {
        const h = this._resolveHeight(heights[k]);
        return `${k[0].toUpperCase()}:${h}`;
      }).join(' · ');

      return `<div style="position:relative;background:${bg};min-height:${desktopH};border-radius:6px;overflow:hidden;display:flex;flex-direction:column;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,${cfg.overlay||0.4});"></div>
        <div style="position:relative;flex:1;display:flex;align-items:center;justify-content:${align};padding:40px;">
          <div style="max-width:600px;text-align:${textAlign};color:#fff;">
            ${slide.title?`<h2 style="font-family:serif;font-size:1.6rem;margin-bottom:12px;text-shadow:0 2px 8px rgba(0,0,0,.4);">${slide.title}</h2>`:''}
            ${slide.description?`<p style="opacity:.88;margin-bottom:20px;font-size:0.92rem;line-height:1.6;">${slide.description}</p>`:''}
            ${slide.buttonText?`<span style="display:inline-block;background:#C9A227;color:#fff;padding:11px 28px;border-radius:4px;font-size:0.85rem;font-weight:600;">${slide.buttonText}</span>`:''}
          </div>
        </div>
        <div style="position:relative;text-align:center;padding:14px 0;">${dots}</div>
        <div style="position:absolute;top:50%;right:12px;transform:translateY(-50%);width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;color:#fff;">›</div>
        <div style="position:absolute;top:50%;left:12px;transform:translateY(-50%);width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;color:#fff;">‹</div>
        <div style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,.6);color:#fff;font-size:.6rem;padding:3px 8px;border-radius:10px;line-height:1.6;">
          ${cfg.animation||'fade'} · ${slides.length} slide${slides.length!==1?'s':''}
        </div>
        <div style="position:absolute;bottom:50px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.55);color:#aaa;font-size:.58rem;padding:2px 10px;border-radius:10px;white-space:nowrap;">
          📐 ${bpSummary}
        </div>
      </div>`;
    }
  },

  // ── Split Content ────────────────────────────────────────────────────────
  'split-content': {
    label: 'Split Content', icon: 'bi-layout-split', desc: 'Text on one side, one or two images on the other',
    defaults: {
      imagePosition: 'right',
      eyebrow: 'BELLEVIE HOTEL',
      title: 'Enjoy a Luxury Experience',
      content: '<p>Hotel Bellevie is not just a hotel; it\'s a story of passion and dedication. Established with a vision to provide a home-like atmosphere, our journey began with a commitment to the highest standards of service and care.</p>',
      subheading: 'Your Comfort is Our Priority',
      subContent: '<p>We take pride in our cozy and well-appointed accommodations, designed to make you feel right at home with plush bedding and modern amenities.</p>',
      contactIcon: 'bi-telephone',
      contactLabel: 'Reservation',
      contactValue: '',
      contactLink: '',
      image1Id: null, image1Url: '',
      image2Id: null, image2Url: '',
      bgColor: '#ffffff',
      verticalPadding: '80',
    },
    fields: [
      { key: 'imagePosition', label: 'Image Side',          type: 'select', options: ['left','right'] },
      { key: 'image1Id',      label: 'Image 1',             type: 'image' },
      { key: 'image2Id',      label: 'Image 2 (optional)',  type: 'image' },
      { key: 'eyebrow',       label: 'Top Label (small uppercase text)', type: 'text' },
      { key: 'title',         label: 'Main Heading',        type: 'text' },
      { key: 'content',       label: 'Body Text',           type: 'richtext' },
      { key: 'subheading',    label: 'Sub-heading (optional)',  type: 'text' },
      { key: 'subContent',    label: 'Secondary Text (optional)', type: 'richtext' },
      { key: 'contactIcon',   label: 'Contact Icon (Bootstrap icon class, e.g. bi-telephone)', type: 'text' },
      { key: 'contactLabel',  label: 'Contact Label (e.g. Reservation)', type: 'text' },
      { key: 'contactValue',  label: 'Contact Value (e.g. phone number)', type: 'text' },
      { key: 'contactLink',   label: 'Contact Link (e.g. tel:+1234567890)', type: 'text' },
      { key: 'bgColor',       label: 'Background',          type: 'color' },
      { key: 'verticalPadding', label: 'Vertical Padding (px)', type: 'number' },
    ],
    preview(cfg) {
      const isLeft = cfg.imagePosition === 'left';
      const img1 = cfg.image1Url
        ? `<img src="${cfg.image1Url}" style="width:100%;height:160px;object-fit:cover;border-radius:6px;">`
        : `<div style="width:100%;height:160px;background:linear-gradient(135deg,#e0d4b0,#C9A22740);border-radius:6px;"></div>`;
      const img2 = cfg.image2Url
        ? `<img src="${cfg.image2Url}" style="width:100%;height:160px;object-fit:cover;border-radius:6px;">`
        : null;
      const imgCol = `<div style="display:flex;gap:8px;flex:1;min-width:140px;">
        <div style="flex:1;">${img1}</div>
        ${img2 ? `<div style="flex:1;">${img2}</div>` : ''}
      </div>`;
      const contact = cfg.contactValue ? `<div style="display:flex;align-items:center;gap:10px;margin-top:10px;">
        <div style="width:36px;height:36px;border-radius:50%;background:#C9A22720;color:#C9A227;display:flex;align-items:center;justify-content:center;">
          <i class="bi ${cfg.contactIcon||'bi-telephone'}" style="font-size:.9rem;"></i>
        </div>
        <div>
          <div style="font-size:.65rem;color:#888;">${cfg.contactLabel||''}</div>
          <div style="font-size:.82rem;font-weight:700;color:#C9A227;">${cfg.contactValue}</div>
        </div>
      </div>` : '';
      const textCol = `<div style="flex:1;min-width:140px;padding:0 10px;">
        ${cfg.eyebrow?`<p style="color:#C9A227;font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;margin-bottom:6px;">${cfg.eyebrow}</p>`:''}
        <h4 style="font-family:serif;margin-bottom:8px;font-size:1.1rem;">${cfg.title||'Title'}</h4>
        <div style="font-size:.78rem;color:#555;line-height:1.6;margin-bottom:8px;">${cfg.content||''}</div>
        ${cfg.subheading?`<h5 style="font-size:.88rem;font-weight:700;margin-bottom:4px;">${cfg.subheading}</h5>`:''}
        ${cfg.subContent?`<div style="font-size:.75rem;color:#666;">${cfg.subContent}</div>`:''}
        ${contact}
      </div>`;
      return `<div style="background:${cfg.bgColor||'#fff'};padding:16px;">
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
          ${isLeft ? imgCol + textCol : textCol + imgCol}
        </div>
      </div>`;
    }
  },

  // ── Embed / Iframe ───────────────────────────────────────────────────────
  embed: {
    label: 'Embed / Map', icon: 'bi-code-square', desc: 'Paste any iframe — map, video, booking widget…',
    _presets: {
      compact:       { label: 'Compact (280px)',                css: '280px' },
      standard:      { label: 'Standard (450px)',               css: '450px' },
      tall:          { label: 'Tall (600px)',                   css: '600px' },
      extratall:     { label: 'Extra Tall (750px)',             css: '750px' },
      fullscreen:    { label: 'Full Screen — fills the window', css: '100vh' },
      almostfull:    { label: 'Almost Full Screen',             css: '90vh'  },
      threequarters: { label: 'Three Quarters of Screen',       css: '75vh'  },
    },
    _breakpoints: [
      { key:'phone',   icon:'bi-phone',   label:'Phone',   note:'Up to 575px wide' },
      { key:'tablet',  icon:'bi-tablet',  label:'Tablet',  note:'576 – 991px wide' },
      { key:'laptop',  icon:'bi-laptop',  label:'Laptop',  note:'992 – 1199px wide' },
      { key:'desktop', icon:'bi-display', label:'Desktop', note:'1200px and wider' },
    ],
    defaults: {
      iframeCode: '',
      title: '',
      description: '',
      widthType: 'full',
      customWidthPct: '100',
      heights: {
        phone:   { preset: 'compact',  custom: '280' },
        tablet:  { preset: 'standard', custom: '450' },
        laptop:  { preset: 'standard', custom: '450' },
        desktop: { preset: 'tall',     custom: '600' },
      },
      bgColor: '#ffffff',
      padding: '40',
    },
    fields: [
      { key: 'iframeCode',     label: 'Embed Code — paste the full iframe HTML here', type: 'richtext' },
      { key: 'title',          label: 'Title (leave blank to hide)',       type: 'text' },
      { key: 'description',    label: 'Description (leave blank to hide)', type: 'textarea' },
      { key: 'widthType',      label: 'Embed Width',                       type: 'select', options: ['full','threequarters','half','custom'] },
      { key: 'customWidthPct', label: 'Custom width (%)',                  type: 'number' },
      { key: 'heights',        label: 'Embed Height per Screen Size',      type: 'breakpoint-heights' },
      { key: 'bgColor',        label: 'Background',                        type: 'color' },
      { key: 'padding',        label: 'Vertical Padding (px)',             type: 'number' },
    ],
    preview(cfg) {
      const widthMap = { full:'100%', threequarters:'75%', half:'50%', custom:(cfg.customWidthPct||'100')+'%' };
      const w = widthMap[cfg.widthType] || '100%';
      const heights = cfg.heights || {};
      const def2 = this;
      const resolveH = (bpCfg) => {
        if (!bpCfg) return '280px';
        if (bpCfg.preset === 'custom') return (parseInt(bpCfg.custom)||280)+'px';
        return (def2._presets[bpCfg.preset]||def2._presets.standard).css;
      };
      const h = resolveH(heights.desktop);
      return `<div style="background:${cfg.bgColor||'#fff'};padding:16px;text-align:center;">
        ${cfg.title?`<h4 style="font-family:serif;margin-bottom:8px;">${cfg.title}</h4>`:''}
        ${cfg.description?`<p style="color:#666;font-size:.82rem;margin-bottom:12px;">${cfg.description}</p>`:''}
        <div style="margin:0 auto;width:${w};height:${h};background:#e0e8f0;border-radius:6px;
                    display:flex;align-items:center;justify-content:center;border:1px dashed #aac;">
          <div style="text-align:center;color:#779;">
            <i class="bi bi-code-square" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
            <div style="font-size:.78rem;">Embed · Width: ${w} · Height: ${h}</div>
            ${cfg.iframeCode?'<div style="font-size:.68rem;color:#aaa;margin-top:4px;">Code attached ✓</div>':'<div style="font-size:.68rem;color:#e09a00;margin-top:4px;">Paste your embed code in settings →</div>'}
          </div>
        </div>
      </div>`;
    }
  },

  // ── Contact Hero ─────────────────────────────────────────────────────────
  'contact-hero': {
    label: 'Contact — Page Header', icon: 'bi-signpost-split', desc: 'Hero banner for the contact page',
    defaults: {
      _locked: true,
      eyebrow: 'GET IN TOUCH',
      title: 'Contact Us',
      subtitle: "We'd love to hear from you. Reach out with any questions, reservations or special requests.",
    },
    fields: [
      { key: 'eyebrow',  label: 'Top Label (small uppercase)',   type: 'text' },
      { key: 'title',    label: 'Page Title',                    type: 'text' },
      { key: 'subtitle', label: 'Subtitle',                      type: 'textarea' },
    ],
    preview(cfg) {
      return `<div style="background:linear-gradient(135deg,#0D1B2A,#1a3a5c);padding:28px;border-radius:6px;text-align:center;color:#fff;">
        <p style="color:#C9A227;font-size:.62rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;margin-bottom:6px;">${cfg.eyebrow||''}</p>
        <h3 style="font-family:serif;font-size:1.4rem;margin-bottom:8px;">${cfg.title||'Contact Us'}</h3>
        <p style="opacity:.7;font-size:.78rem;">${cfg.subtitle||''}</p>
      </div>`;
    }
  },

  // ── Contact Form ──────────────────────────────────────────────────────────
  'contact-form': {
    label: 'Contact — Form', icon: 'bi-envelope-paper', desc: 'Contact form — title & description editable',
    defaults: {
      _locked: true,
      title: 'Send us a Message',
      description: "Fill in the form below and we'll get back to you within 24 hours.",
    },
    fields: [
      { key: 'title',       label: 'Form Title',       type: 'text' },
      { key: 'description', label: 'Form Description', type: 'textarea' },
    ],
    preview(cfg) {
      return `<div style="background:#fff;padding:20px;border-radius:8px;box-shadow:0 1px 6px rgba(0,0,0,.08);">
        <h4 style="font-family:serif;margin-bottom:4px;">${cfg.title||'Send us a Message'}</h4>
        <p style="color:#888;font-size:.75rem;margin-bottom:14px;">${cfg.description||''}</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
          ${['Full Name','Email Address','Phone Number','Subject'].map(l=>`<div style="height:32px;background:#f5f5f5;border-radius:4px;border:1px solid #e0e0e0;display:flex;align-items:center;padding:0 10px;font-size:.68rem;color:#aaa;">${l}</div>`).join('')}
        </div>
        <div style="height:64px;background:#f5f5f5;border-radius:4px;border:1px solid #e0e0e0;margin-bottom:10px;"></div>
        <div style="background:#C9A227;color:#fff;padding:8px;border-radius:4px;text-align:center;font-size:.78rem;font-weight:600;">Send Message</div>
        <div style="text-align:center;margin-top:8px;font-size:.6rem;color:#bbb;">Form submits to the Contact route — not editable here</div>
      </div>`;
    }
  },

  // ── Contact Info ──────────────────────────────────────────────────────────
  'contact-info': {
    label: 'Contact — Hotel Info', icon: 'bi-building-fill-gear', desc: 'Auto-displays address, phone, email from Settings',
    defaults: { _locked: true },
    fields: [],
    preview(cfg) {
      return `<div style="background:#0D1B2A;padding:20px;border-radius:8px;color:#fff;">
        <h4 style="font-family:serif;margin-bottom:14px;font-size:1rem;">Contact Information</h4>
        ${['bi-geo-alt-fill','bi-telephone-fill','bi-envelope-fill','bi-clock-fill'].map((icon,i) => `
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div style="width:34px;height:34px;border-radius:6px;background:rgba(201,162,39,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="bi ${icon}" style="color:#C9A227;font-size:.85rem;"></i>
            </div>
            <div style="height:8px;background:rgba(255,255,255,.2);border-radius:3px;flex:1;"></div>
          </div>`).join('')}
        <div style="text-align:center;margin-top:10px;font-size:.6rem;color:rgba(255,255,255,.35);">
          Pulls from Admin → Settings → General
        </div>
      </div>`;
    }
  },

  // ── Contact Quick Links ───────────────────────────────────────────────────
  'contact-quick-links': {
    label: 'Contact — Quick Links', icon: 'bi-link-45deg', desc: 'Booking, Rooms and Availability links',
    defaults: { _locked: true },
    fields: [],
    preview(cfg) {
      return `<div style="background:#fff;padding:16px;border-radius:8px;box-shadow:0 1px 6px rgba(0,0,0,.08);">
        <h4 style="font-size:.88rem;font-weight:700;margin-bottom:12px;">Quick Links</h4>
        ${['bi-calendar-check','bi-door-open','bi-search'].map((icon,i) => `
          <div style="display:flex;align-items:center;gap:10px;padding:8px;border:1px solid #eee;border-radius:6px;margin-bottom:6px;">
            <i class="bi ${icon}" style="color:#C9A227;"></i>
            <div style="height:8px;background:#eee;border-radius:3px;flex:1;"></div>
            <i class="bi bi-chevron-right" style="color:#ccc;font-size:.65rem;"></i>
          </div>`).join('')}
        <div style="text-align:center;margin-top:8px;font-size:.6rem;color:#bbb;">Links are fixed — Book a Room, View Rooms, Check Availability</div>
      </div>`;
    }
  },

  // ── Divider ───────────────────────────────────────────────────────────────
  divider: {
    label: 'Divider', icon: 'bi-dash-lg', desc: 'Horizontal separator or spacer',
    defaults: { spacing: '40', style: 'line', color: '#dee2e6' },
    fields: [
      { key: 'spacing', label: 'Spacing (px)', type: 'number' },
      { key: 'style',   label: 'Style',        type: 'select', options: ['line','thick','dots','ornament','wave','blank'] },
      { key: 'color',   label: 'Color',        type: 'color' },
    ],
    preview(cfg) {
      const sp = cfg.spacing || 40;
      const inner = cfg.style==='dots'?`<div style="text-align:center;color:${cfg.color};">• • •</div>`
        : cfg.style==='ornament'?`<div style="text-align:center;color:#C9A227;font-size:1.1rem;">— ✦ —</div>`
        : cfg.style==='wave'?`<div style="text-align:center;color:${cfg.color};letter-spacing:.2em;">〜〜〜〜〜</div>`
        : cfg.style==='thick'?`<hr style="border:none;border-top:3px solid ${cfg.color};margin:0;">`
        : cfg.style==='blank'?''
        : `<hr style="border-color:${cfg.color};margin:0;">`;
      return `<div style="padding:${sp}px 0;">${inner}</div>`;
    }
  },

  // ── Columns (Row) ─────────────────────────────────────────────────────────
  'columns': {
    label: 'Columns (Row)', icon: 'bi-layout-three-columns',
    desc: 'Two or more side-by-side columns — place any blocks inside each column',
    defaults: {
      bgColor: 'transparent', paddingTop: 60, paddingBottom: 60,
      gutter: '4', contained: true,
      columns: [
        { id: 'cola', colSm: '12', colMd: '12', colLg: '7', pt:'0', pb:'0', ps:'0', pe:'0', mt:'0', mb:'0', blocks: [] },
        { id: 'colb', colSm: '12', colMd: '12', colLg: '5', pt:'0', pb:'0', ps:'0', pe:'0', mt:'0', mb:'0', blocks: [] },
      ]
    },
    fields: [], // fully custom panel — handled by renderColumnsPanel()
    preview(cfg) {
      const cols = cfg.columns || [];
      const colPreviews = cols.map((c, ci) => {
        const lgW = parseInt(c.colLg) || 6;
        const blocks = (c.blocks || []).map(b => {
          const bDef = BLOCK_TYPES[b.type] || {};
          return `<div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;
                              padding:3px 7px;font-size:.62rem;color:#555;margin-bottom:3px;
                              display:flex;align-items:center;gap:4px;white-space:nowrap;overflow:hidden;">
                    <i class="bi ${bDef.icon||'bi-box'}" style="color:#C9A227;font-size:.6rem;flex-shrink:0;"></i>
                    <span style="overflow:hidden;text-overflow:ellipsis;">${bDef.label||b.type}</span>
                  </div>`;
        }).join('') || `<div style="text-align:center;color:#ccc;font-size:.6rem;padding:8px 0;
                                    border:1px dashed #e0e0e0;border-radius:4px;">empty</div>`;
        return `<div style="flex:${lgW};min-width:0;background:#f5f5f5;border-radius:6px;padding:7px;margin:0 3px;">
                  <div style="font-size:.58rem;font-weight:700;color:#aaa;margin-bottom:5px;
                               text-transform:uppercase;letter-spacing:.05em;">col-lg-${lgW}</div>
                  ${blocks}
                </div>`;
      }).join('');
      return `<div style="display:flex;align-items:stretch;padding:4px 0;min-height:50px;">${colPreviews}</div>`;
    }
  },

};

// ════════════════════════════════════════════════════════════════════════════
//  BUILDER STATE
// ════════════════════════════════════════════════════════════════════════════
let sections         = {!! json_encode($builderData) !!};
let selectedId       = null;
let _imgCallback     = null;   // callback(imageId, imageUrl) for image pickers in settings
let _colBlockEditing = null;   // { sectionId, colIdx, blockIdx } — nested block currently being edited
let showHidden       = false;  // whether hidden blocks are visible in the editor canvas

// Ensure sections is array
if (!Array.isArray(sections)) sections = [];

// ════════════════════════════════════════════════════════════════════════════
//  RENDER HELPERS
// ════════════════════════════════════════════════════════════════════════════

function uid() {
    return 'sec_' + Math.random().toString(36).slice(2, 10);
}

function addSection(type) {
    const def = BLOCK_TYPES[type];
    if (!def) return;
    const section = { id: uid(), type, config: { ...def.defaults } };
    sections.push(section);
    renderCanvas();
    selectSection(section.id);
    setDirty();
}

function removeSection(id) {
    sections = sections.filter(s => s.id !== id);
    if (selectedId === id) { selectedId = null; renderSettings(); }
    renderCanvas();
    setDirty();
}

function duplicateSection(id) {
    const idx = sections.findIndex(s => s.id === id);
    if (idx < 0) return;
    const copy = JSON.parse(JSON.stringify(sections[idx]));
    copy.id = uid();
    sections.splice(idx + 1, 0, copy);
    renderCanvas();
    selectSection(copy.id);
    setDirty();
}

function moveSection(id, dir) {
    const idx = sections.findIndex(s => s.id === id);
    const newIdx = idx + dir;
    if (newIdx < 0 || newIdx >= sections.length) return;
    const tmp = sections[idx];
    sections[idx] = sections[newIdx];
    sections[newIdx] = tmp;
    renderCanvas();
    setDirty();
}

function selectSection(id) {
    selectedId = id;
    document.querySelectorAll('.section-card').forEach(el => {
        el.classList.toggle('selected', el.dataset.id === id);
    });
    renderSettings();
}

function updateConfig(id, key, value) {
    const sec = sections.find(s => s.id === id);
    if (!sec) return;
    sec.config[key] = value;
    // Re-render just the preview for this card
    const card = document.querySelector(`.section-card[data-id="${id}"] .section-preview`);
    if (card) card.innerHTML = BLOCK_TYPES[sec.type].preview(sec.config);
    setDirty();
}

// ════════════════════════════════════════════════════════════════════════════
//  CANVAS RENDER
// ════════════════════════════════════════════════════════════════════════════

function toggleHidden(id) {
    const sec = sections.find(s => s.id === id);
    if (!sec) return;
    sec.config._hidden = !sec.config._hidden;
    // If we just hid it and it was selected, deselect
    if (sec.config._hidden && selectedId === id && !showHidden) {
        selectedId = null;
        renderSettings();
    }
    renderCanvas();
    setDirty();
}

function toggleShowHidden() {
    showHidden = !showHidden;
    renderCanvas();
}

function renderCanvas() {
    const list = document.getElementById('sectionList');

    // ── Hidden-blocks toolbar ─────────────────────────────────────────────
    const hiddenCount = sections.filter(s => s.config?._hidden).length;
    let toolbar = document.getElementById('hiddenToolbar');
    if (!toolbar) {
        toolbar = document.createElement('div');
        toolbar.id = 'hiddenToolbar';
        toolbar.style.cssText = 'margin-bottom:8px;display:none;';
        list.parentNode.insertBefore(toolbar, list);
    }
    if (hiddenCount > 0 || showHidden) {
        toolbar.style.display = '';
        toolbar.innerHTML = `<button type="button" onclick="toggleShowHidden()"
            style="width:100%;border:1px dashed ${showHidden ? '#C9A227' : '#ccc'};
                   background:${showHidden ? '#fdf8ea' : '#fafafa'};
                   color:${showHidden ? '#8a6d00' : '#999'};border-radius:6px;
                   padding:5px 10px;font-size:.75rem;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;gap:6px;">
            <i class="bi bi-eye${showHidden ? '' : '-slash'}"></i>
            ${showHidden
                ? `Showing ${hiddenCount} hidden block${hiddenCount !== 1 ? 's' : ''} — click to hide from editor`
                : `${hiddenCount} hidden block${hiddenCount !== 1 ? 's' : ''} — click to show in editor`}
        </button>`;
    } else {
        toolbar.style.display = 'none';
    }

    // ── Determine which sections to render ────────────────────────────────
    const displaySections = showHidden
        ? sections
        : sections.filter(s => !s.config?._hidden);

    if (displaySections.length === 0 && sections.length === 0) {
        list.innerHTML = `<div id="emptyCanvas">
            <i class="bi bi-layout-wtf"></i>
            <p class="mt-3" style="font-size:0.9rem;">Add blocks from the left panel to start building</p>
        </div>`;
        return;
    }

    if (displaySections.length === 0) {
        list.innerHTML = `<div id="emptyCanvas">
            <i class="bi bi-eye-slash" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="mt-3" style="font-size:0.9rem;color:#aaa;">All blocks are hidden — use the toggle above to reveal them</p>
        </div>`;
        return;
    }

    list.innerHTML = displaySections.map((sec, i) => {
        const def      = BLOCK_TYPES[sec.type] || { label: sec.type, icon: 'bi-question', preview: () => '' };
        const isFirst  = i === 0, isLast = i === displaySections.length - 1;
        const isHidden = !!(sec.config?._hidden);

        const eyeBtn = `<button onclick="event.stopPropagation();toggleHidden('${sec.id}')"
            title="${isHidden ? 'Unhide — show on frontend' : 'Hide from frontend'}"
            style="color:${isHidden ? '#C9A227' : '#aaa'};">
            <i class="bi bi-eye${isHidden ? '-slash' : ''}"></i>
        </button>`;

        const hiddenBadge = isHidden
            ? `<span style="font-size:.6rem;background:#fff3cd;color:#856404;border:1px solid #ffe69c;
                            border-radius:10px;padding:1px 6px;margin-left:4px;">hidden</span>`
            : '';

        const cardStyle = isHidden
            ? 'opacity:0.55;border-style:dashed;'
            : '';

        return `<div class="section-card${selectedId === sec.id ? ' selected' : ''}"
                     data-id="${sec.id}" style="${cardStyle}">
            <div class="section-card-header" onclick="selectSection('${sec.id}')">
                <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
                <i class="bi ${def.icon}" style="color:${isHidden ? '#aaa' : '#C9A227'};"></i>
                <span class="block-label">${def.label}${hiddenBadge}</span>
                <div class="actions">
                    <button onclick="event.stopPropagation();moveSection('${sec.id}',-1)" ${isFirst?'disabled':''} title="Move Up"><i class="bi bi-chevron-up"></i></button>
                    <button onclick="event.stopPropagation();moveSection('${sec.id}',1)" ${isLast?'disabled':''} title="Move Down"><i class="bi bi-chevron-down"></i></button>
                    ${eyeBtn}
                    <button onclick="event.stopPropagation();duplicateSection('${sec.id}')" title="Duplicate"><i class="bi bi-copy"></i></button>
                    <button onclick="event.stopPropagation();removeSection('${sec.id}')" title="Delete" style="color:#e53935;"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            ${isHidden ? '' : `<div class="section-preview">${def.preview(sec.config)}</div>`}
        </div>`;
    }).join('');

    // Init Sortable (only on visible items — hidden sections maintain their position in the data array)
    Sortable.create(list, {
        handle: '.drag-handle',
        animation: 150,
        onEnd(evt) {
            // Map display indices back to sections array indices
            const fromSec = displaySections[evt.oldIndex];
            const toSec   = displaySections[evt.newIndex];
            const fromIdx = sections.findIndex(s => s.id === fromSec.id);
            const toIdx   = sections.findIndex(s => s.id === toSec.id);
            const moved   = sections.splice(fromIdx, 1)[0];
            sections.splice(toIdx, 0, moved);
            setDirty();
        }
    });
}

// ════════════════════════════════════════════════════════════════════════════
//  SETTINGS PANEL RENDER
// ════════════════════════════════════════════════════════════════════════════

function renderSettings() {
    const panel = document.getElementById('settingsContent');
    if (!selectedId) {
        panel.innerHTML = `<div id="noSelection">
            <i class="bi bi-cursor" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
            Click a block to edit its settings</div>`;
        return;
    }

    const sec = sections.find(s => s.id === selectedId);
    if (!sec) return;
    const def = BLOCK_TYPES[sec.type];
    if (!def) return;

    // ── Columns block: fully custom panel ────────────────────────────────────
    if (sec.type === 'columns') {
        // Editing a nested block inside one of the columns?
        if (_colBlockEditing && _colBlockEditing.sectionId === sec.id) {
            const { colIdx, blockIdx } = _colBlockEditing;
            const nestedBlk = sec.config.columns?.[colIdx]?.blocks?.[blockIdx];
            if (nestedBlk) {
                const nDef = BLOCK_TYPES[nestedBlk.type];
                if (nDef) {
                    let nHtml = `<div style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #f0f0f0;">
                        <button type="button" onclick="colBackToRow()"
                          style="border:none;background:none;color:#C9A227;cursor:pointer;font-size:.78rem;font-weight:600;padding:0;">
                          <i class="bi bi-arrow-left me-1"></i>Back to Row
                        </button>
                      </div>
                      <div style="margin-bottom:12px;">
                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;font-weight:600;color:#333;">
                          <i class="bi ${nDef.icon}" style="color:#C9A227;"></i>${nDef.label} — Column ${colIdx+1}
                        </span>
                      </div>`;
                    if (!nDef.fields || nDef.fields.length === 0) {
                        nHtml += `<p style="font-size:.8rem;color:#888;text-align:center;padding:12px 0;">
                          This block has no configurable settings.</p>`;
                    } else {
                        nDef.fields.forEach(field => {
                            const val = nestedBlk.config[field.key] !== undefined ? nestedBlk.config[field.key] : '';
                            nHtml += `<div class="mb-3"><label class="form-label">${field.label}</label>`;
                            if (field.type === 'text' || field.type === 'number') {
                                nHtml += `<input type="${field.type}" class="form-control form-control-sm"
                                    value="${escHtml(String(val))}"
                                    oninput="colUpdateBlockConfig('${sec.id}',${colIdx},${blockIdx},'${field.key}',this.value)">`;
                            } else if (field.type === 'textarea' || field.type === 'richtext') {
                                nHtml += `<textarea class="form-control form-control-sm" rows="4"
                                    oninput="colUpdateBlockConfig('${sec.id}',${colIdx},${blockIdx},'${field.key}',this.value)">${escHtml(String(val))}</textarea>`;
                                if (field.type === 'richtext') nHtml += `<small class="text-muted">HTML supported</small>`;
                            } else if (field.type === 'select') {
                                nHtml += `<select class="form-select form-select-sm"
                                    onchange="colUpdateBlockConfig('${sec.id}',${colIdx},${blockIdx},'${field.key}',this.value)">
                                    ${field.options.map(o=>`<option value="${o}"${o==val?' selected':''}>${o}</option>`).join('')}
                                    </select>`;
                            } else if (field.type === 'toggle') {
                                nHtml += `<div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" ${val?'checked':''}
                                    onchange="colUpdateBlockConfig('${sec.id}',${colIdx},${blockIdx},'${field.key}',this.checked)">
                                    </div>`;
                            } else if (field.type === 'color') {
                                nHtml += `<input type="color" class="form-control form-control-sm form-control-color"
                                    value="${escHtml(String(val||'#000000'))}" style="max-width:60px;"
                                    oninput="colUpdateBlockConfig('${sec.id}',${colIdx},${blockIdx},'${field.key}',this.value)">`;
                            } else if (field.type === 'range') {
                                nHtml += `<input type="range" class="form-range"
                                    min="${field.min||0}" max="${field.max||1}" step="${field.step||0.1}" value="${val}"
                                    oninput="colUpdateBlockConfig('${sec.id}',${colIdx},${blockIdx},'${field.key}',this.value)">
                                    <small class="text-muted">${val}</small>`;
                            }
                            nHtml += `</div>`;
                        });
                    }
                    panel.innerHTML = nHtml;
                    return;
                }
            }
            _colBlockEditing = null; // stale reference — reset
        }
        panel.innerHTML = renderColumnsPanel(sec);
        return;
    }
    // ── End columns block ─────────────────────────────────────────────────────

    let html = `<div style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f0f0f0;">
        <span style="display:inline-flex;align-items:center;gap:6px;font-size:0.82rem;font-weight:600;color:#333;">
            <i class="bi ${def.icon}" style="color:#C9A227;"></i>${def.label}
        </span>
    </div>`;

    def.fields.forEach(field => {
        const val = sec.config[field.key] !== undefined ? sec.config[field.key] : '';
        html += `<div class="mb-3">`;
        html += `<label class="form-label">${field.label}</label>`;

        if (field.type === 'text' || field.type === 'number') {
            html += `<input type="${field.type}" class="form-control form-control-sm"
                data-id="${sec.id}" data-key="${field.key}"
                value="${escHtml(String(val))}"
                oninput="updateConfig('${sec.id}','${field.key}',this.value)">`;

        } else if (field.type === 'textarea') {
            html += `<textarea class="form-control form-control-sm" rows="3"
                data-id="${sec.id}" data-key="${field.key}"
                oninput="updateConfig('${sec.id}','${field.key}',this.value)">${escHtml(String(val))}</textarea>`;

        } else if (field.type === 'richtext') {
            html += `<textarea class="form-control form-control-sm" rows="5"
                data-id="${sec.id}" data-key="${field.key}"
                oninput="updateConfig('${sec.id}','${field.key}',this.value)">${escHtml(String(val))}</textarea>
                <small class="text-muted">HTML supported</small>`;

        } else if (field.type === 'select') {
            const opts = field.options.map(o => `<option value="${o}"${o==val?' selected':''}>${o}</option>`).join('');
            html += `<select class="form-select form-select-sm"
                data-id="${sec.id}" data-key="${field.key}"
                onchange="updateConfig('${sec.id}','${field.key}',this.value)">${opts}</select>`;

        } else if (field.type === 'toggle') {
            html += `<div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                    ${val?'checked':''} data-id="${sec.id}" data-key="${field.key}"
                    onchange="updateConfig('${sec.id}','${field.key}',this.checked)">
            </div>`;

        } else if (field.type === 'color') {
            html += `<input type="color" class="form-control form-control-sm form-control-color"
                value="${escHtml(String(val||'#000000'))}" style="max-width:60px;"
                data-id="${sec.id}" data-key="${field.key}"
                oninput="updateConfig('${sec.id}','${field.key}',this.value)">`;

        } else if (field.type === 'range') {
            html += `<input type="range" class="form-range"
                min="${field.min||0}" max="${field.max||1}" step="${field.step||0.1}"
                value="${val}" data-id="${sec.id}" data-key="${field.key}"
                oninput="updateConfig('${sec.id}','${field.key}',this.value);document.getElementById('range_${sec.id}_${field.key}').textContent=this.value">
                <small id="range_${sec.id}_${field.key}" class="text-muted">${val}</small>`;

        } else if (field.type === 'image') {
            const imgUrl = sec.config[field.key.replace(/Id$/,'Url')] || sec.config[field.key + 'Url'] || '';
            html += `<div class="img-pick-wrap">
                ${imgUrl
                    ? `<img src="${escHtml(imgUrl)}" class="img-pick-preview has-img" onclick="builderPickImage('${sec.id}','${field.key}')">`
                    : `<div class="img-pick-preview d-flex align-items-center justify-content-center text-muted" style="cursor:pointer;border:2px dashed #dee2e6;border-radius:6px;" onclick="builderPickImage('${sec.id}','${field.key}')">
                        <i class="bi bi-image" style="font-size:1.5rem;"></i>
                       </div>`}
                <button type="button" class="btn btn-sm btn-outline-secondary img-pick-btn"
                    onclick="builderPickImage('${sec.id}','${field.key}')">
                    <i class="bi bi-images me-1"></i>Choose from Library
                </button>
            </div>`;

        } else if (field.type === 'button-list') {
            const buttons = Array.isArray(val) ? val : [];
            html += '<div id="bl_' + sec.id + '" style="display:flex;flex-direction:column;gap:10px;">';
            buttons.forEach(function(btn, i) {
                html += buildButtonCard(sec.id, btn, i);
            });
            html += '</div>'
                + '<button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100"'
                + ' onclick="blAdd(\'' + sec.id + '\')">'
                + '<i class="bi bi-plus-circle me-1"></i>Add Button</button>'
                + '<small class="text-muted d-block mt-1">Up to 3 buttons. If no link and no pop-up is chosen, the button will not appear on the page.</small>';

        } else if (field.type === 'breakpoint-heights') {
            // val = { phone:{preset,custom}, tablet:{...}, laptop:{...}, desktop:{...} }
            const heights    = (typeof val === 'object' && val !== null) ? val : {};
            const def2       = BLOCK_TYPES[sec.type];
            const presets    = def2._presets   || {};
            const breakpoints= def2._breakpoints || [];
            // Active breakpoint state stored per section
            window._bpActive = window._bpActive || {};
            if (!window._bpActive[sec.id]) window._bpActive[sec.id] = 'desktop';
            const activeBp = window._bpActive[sec.id];
            const activeCfg = heights[activeBp] || { preset: 'standard', custom: '520' };

            // Breakpoint toggle buttons
            const bpButtons = breakpoints.map(bp => {
                const isActive = bp.key === activeBp;
                const bpCfg   = heights[bp.key] || { preset: 'standard' };
                const presetLabel = (presets[bpCfg.preset] || presets.standard).label.split(' — ')[0].split(' (')[0];
                return `<button type="button" title="${bp.label} — ${bp.note}"
                    style="flex:1;border:2px solid ${isActive?'#C9A227':'#ddd'};border-radius:8px;padding:8px 4px;
                           background:${isActive?'#fdf8ea':'#fff'};cursor:pointer;text-align:center;
                           transition:border-color .15s,background .15s;"
                    onclick="bpSetActive('${sec.id}','${bp.key}');renderSettings();">
                    <i class="bi ${bp.icon}" style="font-size:1.3rem;display:block;margin-bottom:3px;color:${isActive?'#C9A227':'#888'};"></i>
                    <div style="font-size:.65rem;font-weight:${isActive?700:500};color:${isActive?'#C9A227':'#555'};">${bp.label}</div>
                    <div style="font-size:.58rem;color:#aaa;margin-top:1px;">${presetLabel}</div>
                </button>`;
            }).join('');

            // Preset dropdown for active breakpoint
            const presetOpts = Object.entries(presets).map(([k,p]) =>
                `<option value="${k}" ${activeCfg.preset===k?'selected':''}>${p.label}</option>`
            ).join('');

            const showCustom = activeCfg.preset === 'custom';
            const activeBpInfo = breakpoints.find(b => b.key === activeBp) || {};

            html += `<div>
                <div style="display:flex;gap:6px;margin-bottom:12px;">${bpButtons}</div>
                <div style="background:#f8f8f8;border:1px solid #e8e8e8;border-radius:8px;padding:12px;">
                    <div style="font-size:0.75rem;font-weight:700;color:#555;margin-bottom:6px;">
                        <i class="bi ${activeBpInfo.icon||'bi-display'}" style="color:#C9A227;"></i>
                        ${activeBpInfo.label||'Screen'} height — <span style="font-weight:400;color:#888;">${activeBpInfo.note||''}</span>
                    </div>
                    <select class="form-select form-select-sm mb-2"
                        onchange="bpSetPreset('${sec.id}','${activeBp}',this.value);renderSettings();">
                        ${presetOpts}
                    </select>
                    ${showCustom ? `
                    <div style="display:flex;align-items:center;gap:6px;">
                        <input type="number" class="form-control form-control-sm" style="width:90px;"
                            value="${activeCfg.custom||520}" min="100" max="2000" step="10"
                            oninput="bpSetCustom('${sec.id}','${activeBp}',this.value)">
                        <span style="font-size:0.78rem;color:#888;">px</span>
                    </div>` : ''}
                </div>
            </div>`;

        } else if (field.type === 'slide-list') {
            const slides = Array.isArray(val) ? val : [];
            html += `<div id="sl_${sec.id}" style="display:flex;flex-direction:column;gap:10px;">`;
            slides.forEach((slide, i) => {
                const thumbUrl = slide.imageUrl || '';
                html += `<div style="background:#f5f5f5;border-radius:8px;border:1px solid #e0e0e0;overflow:hidden;">
                    <div style="background:#e8e8e8;padding:7px 10px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:0.75rem;font-weight:700;color:#444;">Slide ${i+1}</span>
                        <button type="button" style="border:none;background:none;color:#e53935;cursor:pointer;font-size:0.82rem;padding:0;"
                            onclick="slRemove('${sec.id}',${i})">✕ Remove</button>
                    </div>
                    <div style="padding:10px;display:flex;flex-direction:column;gap:8px;">
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:#555;display:block;margin-bottom:3px;">Background Image</label>
                            ${thumbUrl
                                ? `<img src="${escHtml(thumbUrl)}" style="width:100%;height:70px;object-fit:cover;border-radius:5px;border:2px solid #C9A227;cursor:pointer;display:block;"
                                       onclick="slPickImage('${sec.id}',${i})">`
                                : `<div style="width:100%;height:70px;border:2px dashed #ccc;border-radius:5px;display:flex;align-items:center;justify-content:center;cursor:pointer;background:#fff;color:#aaa;"
                                       onclick="slPickImage('${sec.id}',${i})">
                                       <i class="bi bi-image" style="font-size:1.3rem;"></i>
                                   </div>`}
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1 w-100" style="font-size:0.72rem;"
                                onclick="slPickImage('${sec.id}',${i})">
                                <i class="bi bi-images me-1"></i>Choose Image
                            </button>
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:#555;display:block;margin-bottom:3px;">Title</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Slide heading"
                                value="${escHtml(slide.title||'')}" oninput="slUpdate('${sec.id}',${i},'title',this.value)">
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:#555;display:block;margin-bottom:3px;">Description</label>
                            <textarea class="form-control form-control-sm" rows="2" placeholder="Short subtitle text"
                                oninput="slUpdate('${sec.id}',${i},'description',this.value)">${escHtml(slide.description||'')}</textarea>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                            <div>
                                <label style="font-size:0.72rem;font-weight:600;color:#555;display:block;margin-bottom:3px;">Button Text</label>
                                <input type="text" class="form-control form-control-sm" placeholder="e.g. Book Now"
                                    value="${escHtml(slide.buttonText||'')}" oninput="slUpdate('${sec.id}',${i},'buttonText',this.value)">
                            </div>
                            <div>
                                <label style="font-size:0.72rem;font-weight:600;color:#555;display:block;margin-bottom:3px;">Button Link</label>
                                <input type="text" class="form-control form-control-sm" placeholder="/booking"
                                    value="${escHtml(slide.buttonLink||'')}" oninput="slUpdate('${sec.id}',${i},'buttonLink',this.value)">
                            </div>
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:#555;display:block;margin-bottom:3px;">Text Position</label>
                            <div style="display:flex;gap:4px;">
                                ${['left','center','right'].map(pos => `
                                <button type="button"
                                    style="flex:1;padding:4px;border-radius:4px;font-size:0.72rem;font-weight:600;cursor:pointer;
                                           border:2px solid ${(slide.textPosition||'center')===pos?'#C9A227':'#ddd'};
                                           background:${(slide.textPosition||'center')===pos?'#fdf8ea':'#fff'};
                                           color:${(slide.textPosition||'center')===pos?'#C9A227':'#666'};"
                                    onclick="slUpdate('${sec.id}',${i},'textPosition','${pos}');renderSettings();">
                                    <i class="bi bi-text-${pos==='center'?'center':pos==='left'?'left':'right'}"></i> ${pos.charAt(0).toUpperCase()+pos.slice(1)}
                                </button>`).join('')}
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            html += `</div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100"
                    onclick="slAdd('${sec.id}')">
                    <i class="bi bi-plus-circle me-1"></i>Add Slide
                </button>
                <small class="text-muted d-block mt-1">If button text is empty the button will not be shown on the page.</small>`;

        } else if (field.type === 'feature-list') {
            const feats = Array.isArray(val) ? val : [];
            html += `<div id="fl_${sec.id}" style="display:flex;flex-direction:column;gap:8px;">`;
            feats.forEach((f, i) => {
                html += `<div style="background:#f8f8f8;border-radius:6px;padding:8px;border:1px solid #eee;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                        <small style="font-weight:600;color:#555;">Feature ${i+1}</small>
                        <button type="button" style="border:none;background:none;color:#e53935;cursor:pointer;font-size:0.8rem;"
                            onclick="flRemove('${sec.id}',${i})">✕</button>
                    </div>
                    <input type="text" class="form-control form-control-sm mb-1" placeholder="Icon (e.g. bi-star)"
                        value="${escHtml(f.icon||'')}" oninput="flUpdate('${sec.id}',${i},'icon',this.value)">
                    <input type="text" class="form-control form-control-sm mb-1" placeholder="Title"
                        value="${escHtml(f.title||'')}" oninput="flUpdate('${sec.id}',${i},'title',this.value)">
                    <textarea class="form-control form-control-sm" rows="2" placeholder="Description"
                        oninput="flUpdate('${sec.id}',${i},'desc',this.value)">${escHtml(f.desc||'')}</textarea>
                </div>`;
            });
            html += `</div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100"
                    onclick="flAdd('${sec.id}')">
                    <i class="bi bi-plus-circle me-1"></i>Add Feature
                </button>
                <small class="text-muted">Bootstrap icon classes: bi-star, bi-heart, bi-shield-check, etc.</small>`;
        }

        html += `</div>`;
    });

    panel.innerHTML = html;
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Feature-list helpers ──────────────────────────────────────────────────
function flAdd(sectionId) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec) return;
    if (!Array.isArray(sec.config.features)) sec.config.features = [];
    sec.config.features.push({ icon: 'bi-star', title: 'New Feature', desc: 'Description here.' });
    renderSettings();
    const card = document.querySelector(`.section-card[data-id="${sectionId}"] .section-preview`);
    if (card && BLOCK_TYPES[sec.type]) card.innerHTML = BLOCK_TYPES[sec.type].preview(sec.config);
    setDirty();
}

function flRemove(sectionId, index) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !Array.isArray(sec.config.features)) return;
    sec.config.features.splice(index, 1);
    renderSettings();
    const card = document.querySelector(`.section-card[data-id="${sectionId}"] .section-preview`);
    if (card && BLOCK_TYPES[sec.type]) card.innerHTML = BLOCK_TYPES[sec.type].preview(sec.config);
    setDirty();
}

function flUpdate(sectionId, index, key, value) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !Array.isArray(sec.config.features) || !sec.config.features[index]) return;
    sec.config.features[index][key] = value;
    const card = document.querySelector(`.section-card[data-id="${sectionId}"] .section-preview`);
    if (card && BLOCK_TYPES[sec.type]) card.innerHTML = BLOCK_TYPES[sec.type].preview(sec.config);
    setDirty();
}

// ── Button card builder (avoids nested template-literal syntax errors) ────
function buildButtonCard(sectionId, btn, i) {
    var isModal = btn.action === 'modal';
    var isVideo = btn.modalType === 'video';
    var vidType = btn.modalVideoType || 'youtube';
    var selLink  = (!btn.action || btn.action === 'link') ? ' selected' : '';
    var selModal = btn.action === 'modal' ? ' selected' : '';

    var out = '<div style="background:#f5f5f5;border-radius:8px;border:1px solid #e0e0e0;overflow:hidden;">';

    // Header
    out += '<div style="background:#e8e8e8;padding:7px 12px;display:flex;justify-content:space-between;align-items:center;">';
    out += '<span style="font-size:0.75rem;font-weight:700;color:#444;">Button ' + (i+1) + '</span>';
    out += '<button type="button" style="border:none;background:none;color:#e53935;cursor:pointer;font-size:0.78rem;"'
        + ' onclick="blRemove(\'' + sectionId + '\',' + i + ')">&#x2715; Remove</button>';
    out += '</div>';

    // Body
    out += '<div style="padding:10px;display:flex;flex-direction:column;gap:7px;">';

    // Label input
    out += '<div><label style="font-size:.7rem;font-weight:600;color:#555;display:block;margin-bottom:2px;">Button Label</label>'
        + '<input type="text" class="form-control form-control-sm" placeholder="e.g. Discover More"'
        + ' value="' + escHtml(btn.text||'') + '"'
        + ' oninput="blUpdate(\'' + sectionId + '\',' + i + ',\'text\',this.value)"></div>';

    // Link input
    out += '<div><label style="font-size:.7rem;font-weight:600;color:#555;display:block;margin-bottom:2px;">Link / URL</label>'
        + '<input type="text" class="form-control form-control-sm" placeholder="/about or https://..."'
        + ' value="' + escHtml(btn.link||'') + '"'
        + ' oninput="blUpdate(\'' + sectionId + '\',' + i + ',\'link\',this.value)"></div>';

    // Action select
    out += '<div><label style="font-size:.7rem;font-weight:600;color:#555;display:block;margin-bottom:2px;">What happens when clicked?</label>'
        + '<select class="form-select form-select-sm"'
        + ' onchange="blUpdate(\'' + sectionId + '\',' + i + ',\'action\',this.value);renderSettings();">'
        + '<option value="link"' + selLink + '>Go to the link above</option>'
        + '<option value="modal"' + selModal + '>Open a pop-up window</option>'
        + '</select></div>';

    // Modal options (only shown when action=modal)
    if (isModal) {
        var selText  = !isVideo ? ' selected' : '';
        var selVid   = isVideo  ? ' selected' : '';

        out += '<div style="background:#fff;border:1px solid #e0e0e0;border-radius:6px;padding:8px;">';
        out += '<label style="font-size:.7rem;font-weight:700;color:#C9A227;display:block;margin-bottom:6px;">'
            + '<i class="bi bi-window-stack me-1"></i>Pop-up Content</label>';

        // Content type select
        out += '<div style="margin-bottom:6px;">'
            + '<label style="font-size:.68rem;color:#666;display:block;margin-bottom:2px;">Show in pop-up:</label>'
            + '<select class="form-select form-select-sm"'
            + ' onchange="blUpdate(\'' + sectionId + '\',' + i + ',\'modalType\',this.value);renderSettings();">'
            + '<option value="text"' + selText + '>Text — heading &amp; description</option>'
            + '<option value="video"' + selVid + '>Video — play a video</option>'
            + '</select></div>';

        if (isVideo) {
            // Video source + URL
            var selYT  = vidType === 'youtube'    ? ' selected' : '';
            var selCld = vidType === 'cloudinary' ? ' selected' : '';
            var phUrl  = vidType === 'youtube' ? 'https://www.youtube.com/watch?v=...' : 'Cloudinary video URL';

            out += '<div>';
            out += '<label style="font-size:.68rem;color:#666;display:block;margin-bottom:2px;">Video source:</label>'
                + '<select class="form-select form-select-sm mb-1"'
                + ' onchange="blUpdate(\'' + sectionId + '\',' + i + ',\'modalVideoType\',this.value);renderSettings();">'
                + '<option value="youtube"' + selYT + '>YouTube</option>'
                + '<option value="cloudinary"' + selCld + '>Cloudinary (uploaded video)</option>'
                + '</select>';
            out += '<input type="text" class="form-control form-control-sm" style="margin-top:4px;"'
                + ' placeholder="' + phUrl + '"'
                + ' value="' + escHtml(btn.modalVideoUrl||'') + '"'
                + ' oninput="blUpdate(\'' + sectionId + '\',' + i + ',\'modalVideoUrl\',this.value)">';
            if (vidType === 'cloudinary') {
                out += '<button type="button" class="btn btn-sm btn-outline-secondary mt-1 w-100" style="font-size:.7rem;"'
                    + ' onclick="blUploadVideo(\'' + sectionId + '\',' + i + ')">'
                    + '<i class="bi bi-cloud-upload me-1"></i>Upload Video to Cloudinary</button>';
            }
            out += '</div>';
        } else {
            // Text content
            out += '<div>';
            out += '<label style="font-size:.68rem;color:#666;display:block;margin-bottom:2px;">Pop-up heading (optional):</label>'
                + '<input type="text" class="form-control form-control-sm mb-1" placeholder="Leave blank to use the button label"'
                + ' value="' + escHtml(btn.modalTitle||'') + '"'
                + ' oninput="blUpdate(\'' + sectionId + '\',' + i + ',\'modalTitle\',this.value)">';
            out += '<label style="font-size:.68rem;color:#666;display:block;margin-bottom:2px;">Pop-up text:</label>'
                + '<textarea class="form-control form-control-sm" rows="3" placeholder="Write the pop-up body text here…"'
                + ' oninput="blUpdate(\'' + sectionId + '\',' + i + ',\'modalDescription\',this.value)">'
                + escHtml(btn.modalDescription||'') + '</textarea>';
            out += '</div>';
        }

        out += '</div>'; // end modal options box
    }

    out += '</div>'; // end body
    out += '</div>'; // end card
    return out;
}

// ── Button-list helpers ───────────────────────────────────────────────────
function blAdd(sectionId) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec) return;
    if (!Array.isArray(sec.config.buttons)) sec.config.buttons = [];
    if (sec.config.buttons.length >= 3) { alert('Maximum 3 buttons per section.'); return; }
    sec.config.buttons.push({ text: 'Learn More', link: '', action: 'link', modalType: 'text', modalTitle: '', modalDescription: '', modalVideoType: 'youtube', modalVideoUrl: '' });
    renderSettings();
    _refreshBlockPreview(sectionId);
    setDirty();
}

function blRemove(sectionId, index) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !Array.isArray(sec.config.buttons)) return;
    sec.config.buttons.splice(index, 1);
    renderSettings();
    _refreshBlockPreview(sectionId);
    setDirty();
}

function blUpdate(sectionId, index, key, value) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !Array.isArray(sec.config.buttons) || !sec.config.buttons[index]) return;
    sec.config.buttons[index][key] = value;
    _refreshBlockPreview(sectionId);
    setDirty();
}

function blUploadVideo(sectionId, buttonIndex) {
    const cloudName = @json(config('cloudinary.cloud_name'));
    const preset    = @json(config('cloudinary.upload_preset', ''));
    if (!cloudName || !preset) { alert('Cloudinary upload preset not configured.'); return; }
    if (typeof window.cloudinary === 'undefined') {
        const s = document.createElement('script');
        s.src = 'https://widget.cloudinary.com/v2.0/global/all.js';
        s.onload = () => _openVideoWidget(sectionId, buttonIndex, cloudName, preset);
        document.head.appendChild(s);
    } else {
        _openVideoWidget(sectionId, buttonIndex, cloudName, preset);
    }
}

function _openVideoWidget(sectionId, buttonIndex, cloudName, preset) {
    window.cloudinary.openUploadWidget({
        cloud_name: cloudName, upload_preset: preset,
        resource_type: 'video', multiple: false,
        sources: ['local', 'url'],
        styles: { palette: { action: '#C9A227', link: '#C9A227' } }
    }, function(error, result) {
        if (error || result.event !== 'success') return;
        const url = result.info.secure_url;
        blUpdate(sectionId, buttonIndex, 'modalVideoUrl', url);
        blUpdate(sectionId, buttonIndex, 'modalVideoType', 'cloudinary');
        renderSettings();
    });
}

// ── Generic block preview refresh ─────────────────────────────────────────
function _refreshBlockPreview(sectionId) {
    const sec  = sections.find(s => s.id === sectionId);
    const card = document.querySelector(`.section-card[data-id="${sectionId}"] .section-preview`);
    if (card && sec && BLOCK_TYPES[sec.type]) card.innerHTML = BLOCK_TYPES[sec.type].preview(sec.config);
}

// ── Breakpoint-heights helpers ────────────────────────────────────────────
window._bpActive = window._bpActive || {};

function bpSetActive(sectionId, bp) {
    window._bpActive[sectionId] = bp;
    // renderSettings() is called by the onclick after this
}

function bpSetPreset(sectionId, bp, preset) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec) return;
    if (!sec.config.heights) sec.config.heights = {};
    if (!sec.config.heights[bp]) sec.config.heights[bp] = {};
    sec.config.heights[bp].preset = preset;
    _refreshBlockPreview(sectionId);
    setDirty();
}

function bpSetCustom(sectionId, bp, value) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec) return;
    if (!sec.config.heights) sec.config.heights = {};
    if (!sec.config.heights[bp]) sec.config.heights[bp] = {};
    sec.config.heights[bp].custom = value;
    _refreshBlockPreview(sectionId);
    setDirty();
}

// ── Slide-list helpers ────────────────────────────────────────────────────
function slAdd(sectionId) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec) return;
    if (!Array.isArray(sec.config.slides)) sec.config.slides = [];
    sec.config.slides.push({ imageId: null, imageUrl: '', title: 'New Slide', description: '', buttonText: '', buttonLink: '', textPosition: 'center' });
    renderSettings();
    _refreshSliderPreview(sectionId);
    setDirty();
}

function slRemove(sectionId, index) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !Array.isArray(sec.config.slides)) return;
    sec.config.slides.splice(index, 1);
    renderSettings();
    _refreshSliderPreview(sectionId);
    setDirty();
}

function slUpdate(sectionId, index, key, value) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !Array.isArray(sec.config.slides) || !sec.config.slides[index]) return;
    sec.config.slides[index][key] = value;
    _refreshSliderPreview(sectionId);
    setDirty();
}

function slPickImage(sectionId, slideIndex) {
    _builderImgSectionId = sectionId;
    _builderImgKey       = '__slide__';
    _builderImgSlideIdx  = slideIndex;
    _builderImgSelection = null;
    ipLibraryLoad(1);
    new bootstrap.Modal(document.getElementById('ipLibraryModal')).show();
}

// Kept for backward compatibility — delegates to the generic version
function _refreshSliderPreview(sectionId) { _refreshBlockPreview(sectionId); }

// ════════════════════════════════════════════════════════════════════════════
//  COLUMNS BLOCK HELPERS
// ════════════════════════════════════════════════════════════════════════════

function colRefreshPreview(sectionId) {
    const sec  = sections.find(s => s.id === sectionId);
    const card = document.querySelector(`.section-card[data-id="${sectionId}"] .section-preview`);
    if (card && sec) card.innerHTML = BLOCK_TYPES.columns.preview(sec.config);
}

function colUpdateRow(sectionId, key, value) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec) return;
    sec.config[key] = value;
    colRefreshPreview(sectionId);
    setDirty();
}

function colUpdateProp(sectionId, colIdx, key, value) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !sec.config.columns[colIdx]) return;
    sec.config.columns[colIdx][key] = value;
    colRefreshPreview(sectionId);
    setDirty();
}

function colAddColumn(sectionId) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !Array.isArray(sec.config.columns)) return;
    if (sec.config.columns.length >= 4) { alert('Maximum 4 columns per row.'); return; }
    sec.config.columns.push({
        id: 'col_' + Math.random().toString(36).slice(2, 8),
        colSm: '12', colMd: '12', colLg: '4',
        pt: '0', pb: '0', ps: '0', pe: '0', mt: '0', mb: '0', blocks: []
    });
    renderSettings();
    colRefreshPreview(sectionId);
    setDirty();
}

function colRemoveColumn(sectionId, colIdx) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !Array.isArray(sec.config.columns)) return;
    if (sec.config.columns.length <= 1) { alert('A row must have at least one column.'); return; }
    sec.config.columns.splice(colIdx, 1);
    if (_colBlockEditing && _colBlockEditing.sectionId === sectionId && _colBlockEditing.colIdx === colIdx) {
        _colBlockEditing = null;
    }
    renderSettings();
    colRefreshPreview(sectionId);
    setDirty();
}

function colAddBlock(sectionId, colIdx, blockType) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !sec.config.columns[colIdx]) return;
    const def = BLOCK_TYPES[blockType];
    if (!def) return;
    if (!Array.isArray(sec.config.columns[colIdx].blocks)) sec.config.columns[colIdx].blocks = [];
    sec.config.columns[colIdx].blocks.push({
        id: 'cblk_' + Math.random().toString(36).slice(2, 10),
        type: blockType,
        config: { ...(def.defaults || {}) }
    });
    renderSettings();
    colRefreshPreview(sectionId);
    setDirty();
}

function colRemoveBlock(sectionId, colIdx, blockIdx) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !sec.config.columns[colIdx]) return;
    sec.config.columns[colIdx].blocks.splice(blockIdx, 1);
    if (_colBlockEditing && _colBlockEditing.sectionId === sectionId &&
        _colBlockEditing.colIdx === colIdx && _colBlockEditing.blockIdx === blockIdx) {
        _colBlockEditing = null;
    }
    renderSettings();
    colRefreshPreview(sectionId);
    setDirty();
}

function colMoveBlock(sectionId, colIdx, blockIdx, dir) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !sec.config.columns[colIdx]) return;
    const blocks  = sec.config.columns[colIdx].blocks;
    const newIdx  = blockIdx + dir;
    if (newIdx < 0 || newIdx >= blocks.length) return;
    const tmp = blocks[blockIdx]; blocks[blockIdx] = blocks[newIdx]; blocks[newIdx] = tmp;
    renderSettings();
    colRefreshPreview(sectionId);
    setDirty();
}

function colEditBlock(sectionId, colIdx, blockIdx) {
    _colBlockEditing = { sectionId, colIdx, blockIdx };
    renderSettings();
}

function colBackToRow() {
    _colBlockEditing = null;
    renderSettings();
}

function colUpdateBlockConfig(sectionId, colIdx, blockIdx, key, value) {
    const sec = sections.find(s => s.id === sectionId);
    if (!sec || !sec.config.columns[colIdx] || !sec.config.columns[colIdx].blocks[blockIdx]) return;
    sec.config.columns[colIdx].blocks[blockIdx].config[key] = value;
    colRefreshPreview(sectionId);
    setDirty();
}

function renderColumnsPanel(sec) {
    const cfg  = sec.config;
    const cols = cfg.columns || [];

    const colWidthOpts = ['1','2','3','4','5','6','7','8','9','10','11','12','auto'];
    const spacingOpts  = ['0','1','2','3','4','5'];
    const gutterOpts   = ['0','1','2','3','4','5'];

    const mkSel = (val, opts, onChange) =>
        `<select class="form-select form-select-sm" style="font-size:.72rem;" onchange="${onChange}">
           ${opts.map(v => `<option value="${v}"${v === String(val) ? ' selected' : ''}>${v}</option>`).join('')}
         </select>`;

    // ── Row settings ──────────────────────────────────────────────────────────
    let html = `
    <div style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #f0f0f0;">
      <span style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;font-weight:600;color:#333;">
        <i class="bi bi-layout-three-columns" style="color:#C9A227;"></i>Columns (Row)
      </span>
    </div>

    <div style="background:#fafafa;border:1px solid #eee;border-radius:6px;padding:10px;margin-bottom:14px;">
      <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
                  color:#999;margin-bottom:8px;">Row Settings</div>

      <div class="row g-2 mb-2">
        <div class="col-6">
          <label style="font-size:.75rem;color:#555;display:block;margin-bottom:2px;">Background</label>
          <input type="color" class="form-control form-control-sm form-control-color" style="max-width:50px;"
            value="${escHtml(cfg.bgColor || '#ffffff')}"
            oninput="colUpdateRow('${sec.id}','bgColor',this.value)">
        </div>
        <div class="col-6">
          <label style="font-size:.75rem;color:#555;display:block;margin-bottom:2px;">Gutter (g-N)</label>
          ${mkSel(cfg.gutter || '4', gutterOpts, `colUpdateRow('${sec.id}','gutter',this.value)`)}
        </div>
      </div>

      <div class="row g-2 mb-2">
        <div class="col-6">
          <label style="font-size:.75rem;color:#555;display:block;margin-bottom:2px;">Padding Top (px)</label>
          <input type="number" class="form-control form-control-sm" value="${cfg.paddingTop ?? 60}"
            style="font-size:.78rem;" oninput="colUpdateRow('${sec.id}','paddingTop',this.value)">
        </div>
        <div class="col-6">
          <label style="font-size:.75rem;color:#555;display:block;margin-bottom:2px;">Padding Bottom (px)</label>
          <input type="number" class="form-control form-control-sm" value="${cfg.paddingBottom ?? 60}"
            style="font-size:.78rem;" oninput="colUpdateRow('${sec.id}','paddingBottom',this.value)">
        </div>
      </div>

      <div class="row g-2">
        <div class="col-12">
          <label style="font-size:.75rem;color:#555;display:block;margin-bottom:2px;">Container</label>
          <select class="form-select form-select-sm" style="font-size:.78rem;"
            onchange="colUpdateRow('${sec.id}','contained',this.value==='true')">
            <option value="true"${cfg.contained !== false ? ' selected' : ''}>Contained (max-width)</option>
            <option value="false"${cfg.contained === false ? ' selected' : ''}>Full Width</option>
          </select>
        </div>
      </div>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
      <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#999;">
        Columns (${cols.length})
      </span>
      <button type="button" onclick="colAddColumn('${sec.id}')"
        style="border:none;background:#C9A227;color:#fff;border-radius:4px;padding:2px 10px;
               font-size:.73rem;cursor:pointer;font-weight:600;">
        <i class="bi bi-plus"></i> Add Column
      </button>
    </div>`;

    // ── Per-column panels ─────────────────────────────────────────────────────
    cols.forEach((col, ci) => {
        const colBlocks = col.blocks || [];

        html += `
        <div style="background:#f9f9f9;border:1px solid #e5e5e5;border-radius:8px;margin-bottom:10px;overflow:hidden;">
          <div style="background:#e8e8e8;padding:6px 10px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:.75rem;font-weight:700;color:#444;">Column ${ci + 1}</span>
            <button type="button" onclick="colRemoveColumn('${sec.id}',${ci})"
              style="border:none;background:none;color:#e53935;font-size:.72rem;cursor:pointer;">
              ✕ Remove
            </button>
          </div>
          <div style="padding:10px;">

            <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;color:#aaa;margin-bottom:5px;">
              Bootstrap Width
            </div>
            <div class="row g-1 mb-3">
              <div class="col-4">
                <label style="font-size:.65rem;color:#888;display:block;margin-bottom:2px;">sm <span style="color:#bbb;">(≤575px)</span></label>
                ${mkSel(col.colSm || '12', colWidthOpts, `colUpdateProp('${sec.id}',${ci},'colSm',this.value)`)}
              </div>
              <div class="col-4">
                <label style="font-size:.65rem;color:#888;display:block;margin-bottom:2px;">md <span style="color:#bbb;">(576-991)</span></label>
                ${mkSel(col.colMd || '12', colWidthOpts, `colUpdateProp('${sec.id}',${ci},'colMd',this.value)`)}
              </div>
              <div class="col-4">
                <label style="font-size:.65rem;color:#888;display:block;margin-bottom:2px;">lg <span style="color:#bbb;">(≥992px)</span></label>
                ${mkSel(col.colLg || '6', colWidthOpts, `colUpdateProp('${sec.id}',${ci},'colLg',this.value)`)}
              </div>
            </div>

            <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;color:#aaa;margin-bottom:5px;">
              Padding <span style="font-weight:400;color:#bbb;">(Bootstrap p-N classes)</span>
            </div>
            <div class="row g-1 mb-3">
              ${[['pt','Top'],['pb','Bot'],['ps','Left'],['pe','Right']].map(([k, l]) => `
                <div class="col-3">
                  <label style="font-size:.63rem;color:#999;display:block;margin-bottom:2px;">${l}</label>
                  ${mkSel(col[k] || '0', spacingOpts, `colUpdateProp('${sec.id}',${ci},'${k}',this.value)`)}
                </div>`).join('')}
            </div>

            <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;color:#aaa;margin-bottom:5px;">
              Margin <span style="font-weight:400;color:#bbb;">(Bootstrap m-N classes)</span>
            </div>
            <div class="row g-1 mb-3">
              ${[['mt','Top'],['mb','Bottom']].map(([k, l]) => `
                <div class="col-3">
                  <label style="font-size:.63rem;color:#999;display:block;margin-bottom:2px;">${l}</label>
                  ${mkSel(col[k] || '0', spacingOpts, `colUpdateProp('${sec.id}',${ci},'${k}',this.value)`)}
                </div>`).join('')}
            </div>

            <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;color:#aaa;margin-bottom:6px;">
              Blocks in this column
            </div>`;

        // Blocks list
        if (colBlocks.length === 0) {
            html += `<div style="text-align:center;padding:10px 0;color:#ccc;font-size:.75rem;
                                  border:1px dashed #ddd;border-radius:5px;margin-bottom:8px;">
                       No blocks yet
                     </div>`;
        } else {
            colBlocks.forEach((blk, bi) => {
                const bDef = BLOCK_TYPES[blk.type] || { label: blk.type, icon: 'bi-box' };
                const isFirst = bi === 0, isLast = bi === colBlocks.length - 1;
                html += `
                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:6px;padding:5px 8px;
                             margin-bottom:5px;display:flex;align-items:center;gap:5px;">
                  <i class="bi ${bDef.icon}" style="color:#C9A227;font-size:.78rem;flex-shrink:0;"></i>
                  <span style="font-size:.73rem;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#333;">
                    ${bDef.label}
                  </span>
                  <button type="button" title="Edit settings"
                    onclick="colEditBlock('${sec.id}',${ci},${bi})"
                    style="border:none;background:none;color:#C9A227;cursor:pointer;font-size:.78rem;padding:0 3px;">
                    <i class="bi bi-pencil-fill"></i></button>
                  <button type="button" title="Move up" ${isFirst ? 'disabled' : ''}
                    onclick="colMoveBlock('${sec.id}',${ci},${bi},-1)"
                    style="border:none;background:none;color:${isFirst ? '#ddd' : '#888'};cursor:pointer;font-size:.72rem;padding:0 2px;">
                    <i class="bi bi-chevron-up"></i></button>
                  <button type="button" title="Move down" ${isLast ? 'disabled' : ''}
                    onclick="colMoveBlock('${sec.id}',${ci},${bi},1)"
                    style="border:none;background:none;color:${isLast ? '#ddd' : '#888'};cursor:pointer;font-size:.72rem;padding:0 2px;">
                    <i class="bi bi-chevron-down"></i></button>
                  <button type="button" title="Remove"
                    onclick="colRemoveBlock('${sec.id}',${ci},${bi})"
                    style="border:none;background:none;color:#e53935;cursor:pointer;font-size:.78rem;padding:0 2px;">
                    <i class="bi bi-trash"></i></button>
                </div>`;
            });
        }

        // Add block selector
        html += `
            <select class="form-select form-select-sm" style="font-size:.75rem;margin-top:4px;"
              onchange="if(this.value){colAddBlock('${sec.id}',${ci},this.value);this.value='';}">
              <option value="">+ Add block to this column…</option>
              ${Object.entries(BLOCK_TYPES)
                  .filter(([t]) => t !== 'columns' && t !== 'floating-btn')
                  .map(([t, d]) => `<option value="${t}">${d.label}</option>`)
                  .join('')}
            </select>
          </div>
        </div>`;
    });

    return html;
}

// ════════════════════════════════════════════════════════════════════════════
//  IMAGE PICKER (reuses ipLibraryModal)
// ════════════════════════════════════════════════════════════════════════════

let _builderImgSectionId = null;
let _builderImgKey       = null;
let _builderImgSlideIdx  = null;   // set when picking image for a specific slide
let _builderImgSelection = null;

function builderPickImage(sectionId, key) {
    _builderImgSectionId = sectionId;
    _builderImgKey       = key;
    _builderImgSlideIdx  = null;
    _builderImgSelection = null;
    ipLibraryLoad(1);
    new bootstrap.Modal(document.getElementById('ipLibraryModal')).show();
}

function builderLibraryConfirm() {
    if (!_builderImgSelection) return;
    const sec = sections.find(s => s.id === _builderImgSectionId);
    if (sec) {
        if (_builderImgKey === '__slide__' && _builderImgSlideIdx !== null) {
            // Slide-specific image pick
            if (!Array.isArray(sec.config.slides)) sec.config.slides = [];
            if (!sec.config.slides[_builderImgSlideIdx]) sec.config.slides[_builderImgSlideIdx] = {};
            sec.config.slides[_builderImgSlideIdx].imageId  = _builderImgSelection.id;
            sec.config.slides[_builderImgSlideIdx].imageUrl = _builderImgSelection.url;
        } else {
            sec.config[_builderImgKey] = _builderImgSelection.id;
            const urlKey = _builderImgKey.replace(/Id$/, 'Url');
            sec.config[urlKey] = _builderImgSelection.url;
        }
        const card = document.querySelector(`.section-card[data-id="${_builderImgSectionId}"] .section-preview`);
        const def  = BLOCK_TYPES[sec.type];
        if (card && def) card.innerHTML = def.preview(sec.config);
        renderSettings();
        setDirty();
    }
    bootstrap.Modal.getInstance(document.getElementById('ipLibraryModal'))?.hide();
}

// Shared library loader (same as image-picker component)
function ipLibraryLoad(page) {
    const search = (document.getElementById('ipLibrarySearch') || {}).value || '';
    const grid   = document.getElementById('ipLibraryGrid');
    grid.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border spinner-border-sm"></div></div>';

    fetch('/admin/images?page=' + page + '&search=' + encodeURIComponent(search), {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.ok ? r.json() : Promise.reject('HTTP ' + r.status))
    .then(data => {
        if (!data.data || !data.data.length) {
            grid.innerHTML = '<div class="col-12 text-center py-4 text-muted">No images found.</div>';
            document.getElementById('ipLibraryPager').innerHTML = '';
            return;
        }

        grid.innerHTML = data.data.map(img => `
            <div class="col-6 col-md-3 col-lg-2">
                <div class="ip-lib-item" data-id="${img.id}" data-url="${img.url}" data-filename="${img.original_filename||''}"
                     onclick="builderLibrarySelect(this)"
                     style="cursor:pointer;border:2px solid transparent;border-radius:6px;overflow:hidden;position:relative;">
                    <img src="${img.url_thumb||img.url}" style="width:100%;aspect-ratio:1;object-fit:cover;display:block;">
                    <div class="ip-sel-overlay" style="position:absolute;inset:0;background:rgba(201,162,39,.35);display:none;">
                        <i class="bi bi-check-circle-fill" style="position:absolute;top:6px;right:6px;color:#fff;font-size:1.25rem;"></i>
                    </div>
                    <small style="display:block;padding:3px 4px;font-size:.7rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${img.original_filename||''}</small>
                </div>
            </div>`).join('');

        const pager = document.getElementById('ipLibraryPager');
        if (data.last_page <= 1) { pager.innerHTML = ''; return; }
        let btns = '';
        for (let p = 1; p <= data.last_page; p++) {
            btns += `<button type="button" class="btn btn-sm ${p===data.current_page?'btn-primary':'btn-outline-secondary'}"
                      onclick="ipLibraryLoad(${p})">${p}</button>`;
        }
        pager.innerHTML = btns;
    })
    .catch(e => { grid.innerHTML = `<div class="col-12 text-center py-4 text-danger">Failed: ${e}</div>`; });
}

function builderLibrarySelect(el) {
    document.querySelectorAll('.ip-lib-item').forEach(e => {
        e.style.borderColor = 'transparent';
        e.querySelector('.ip-sel-overlay').style.display = 'none';
    });
    el.style.borderColor = '#C9A227';
    el.querySelector('.ip-sel-overlay').style.display = 'block';
    _builderImgSelection = { id: parseInt(el.dataset.id), url: el.dataset.url, filename: el.dataset.filename };
    document.getElementById('ipLibrarySelected').textContent = '1 selected';
}

// ════════════════════════════════════════════════════════════════════════════
//  SAVE
// ════════════════════════════════════════════════════════════════════════════

function setDirty() {
    const el = document.getElementById('saveStatus');
    el.textContent = '● Unsaved changes';
    el.className = 'saving';
}

function builderSave() {
    const status = document.getElementById('saveStatus');
    status.textContent = 'Saving…';
    status.className = 'saving';

    const token = document.querySelector('meta[name="csrf-token"]');
    fetch('{{ $saveUrl }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token ? token.content : '',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ sections })
    })
    .then(async r => {
        const data = await r.json();
        if (!r.ok || data.success === false) {
            status.textContent = '✗ ' + (data.message || 'Save failed');
            status.className = 'error';
            // Show persistent alert for validation errors (422)
            if (r.status === 422 || data.success === false) {
                let alertEl = document.getElementById('saveErrorAlert');
                if (!alertEl) {
                    alertEl = document.createElement('div');
                    alertEl.id = 'saveErrorAlert';
                    alertEl.style.cssText = 'position:fixed;top:60px;left:50%;transform:translateX(-50%);' +
                        'z-index:9999;background:#fff3cd;border:1px solid #ffe69c;color:#664d03;' +
                        'border-radius:8px;padding:12px 18px;max-width:520px;width:90%;' +
                        'box-shadow:0 4px 20px rgba(0,0,0,.15);font-size:.85rem;line-height:1.5;' +
                        'display:flex;align-items:flex-start;gap:10px;';
                    document.body.appendChild(alertEl);
                }
                alertEl.innerHTML = `<i class="bi bi-exclamation-triangle-fill" style="color:#c9a227;font-size:1.1rem;flex-shrink:0;margin-top:1px;"></i>
                    <div style="flex:1;">${data.message || 'Save failed.'}</div>
                    <button onclick="document.getElementById('saveErrorAlert').remove()"
                        style="border:none;background:none;color:#664d03;cursor:pointer;font-size:1rem;padding:0;flex-shrink:0;">✕</button>`;
            }
            return;
        }
        // Remove any previous error alert
        document.getElementById('saveErrorAlert')?.remove();
        status.textContent = '✓ Saved';
        status.className = 'saved';
        setTimeout(() => { status.textContent = ''; status.className = ''; }, 3000);
    })
    .catch(e => {
        status.textContent = '✗ Save failed';
        status.className = 'error';
        console.error(e);
    });
}

// Ctrl+S to save
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); builderSave(); }
});

// ════════════════════════════════════════════════════════════════════════════
//  PALETTE RENDER + INIT
// ════════════════════════════════════════════════════════════════════════════

function renderPalette() {
    const list = document.getElementById('paletteList');
    list.innerHTML = Object.entries(BLOCK_TYPES).map(([type, def]) => `
        <div class="palette-item" onclick="addSection('${type}')">
            <div class="icon"><i class="bi ${def.icon}"></i></div>
            <div>
                <div class="label">${def.label}</div>
                <div class="desc">${def.desc}</div>
            </div>
        </div>`).join('');
}

// Bootstrap
renderPalette();
renderCanvas();
</script>
</body>
</html>
