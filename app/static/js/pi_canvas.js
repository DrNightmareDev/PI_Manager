/**
 * PI Surface Template canvas renderer
 * Shared by pi_templates.html and pi_template_detail.html
 *
 * JSON format (DalShooth / EVE in-game export):
 *   P  – pins:   [{T: typeId, La: lat_rad, Lo: lon_rad, ...}]
 *   L  – links:  [{S: src_pin_idx, D: dst_pin_idx, Lv: level}]
 *   R  – routes: [{P: [idx0, idx1, …, idxN], T: typeId, Q: qty}]
 *                 R[i].P is the FULL path through all intermediate pins.
 */

// ── Building type IDs ──────────────────────────────────────────────────────────
const PI_COLORS = {
  2542: '#c8a600',  // Command Center       – gold
  2524: '#00b4d8',  // Launch Pad           – cyan
  2562: '#4cc9f0',  // Storage Facility     – light-blue
  3068: '#f4a300',  // ECU                  – amber
  2481: '#57cc99',  // Extractor Head       – green
  2474: '#9b5de5',  // Adv. Industrial Fac. – purple
  2552: '#3a86ff',  // Basic Industrial Fac.– blue
  2256: '#ff006e',  // High-Tech Plant      – pink
};

const PI_NAMES = {
  2542: 'Command Center',
  2524: 'Launch Pad',
  2562: 'Storage Facility',
  3068: 'Extractor Control Unit',
  2481: 'Extractor Head',
  2474: 'Advanced Industrial Facility',
  2552: 'Basic Industrial Facility',
  2256: 'High-Tech Production Plant',
};

// Base radius per type (scaled at render time)
const PI_RADII = {
  2542: 1.3,   // Command Center  – big
  2524: 1.15,  // Launch Pad
  2562: 1.0,   // Storage
  3068: 1.2,   // ECU
  2481: 0.75,  // Extractor Head – small
  2474: 1.0,   // Adv. Industrial
  2552: 0.9,   // Basic Industrial
  2256: 1.0,   // High-Tech Plant
};

// ── Shape helpers (ctx must be translated to pin centre) ───────────────────────

function _polygon(ctx, sides, r, rotation) {
  rotation = rotation || 0;
  ctx.beginPath();
  for (let i = 0; i < sides; i++) {
    const a = (i / sides) * Math.PI * 2 + rotation;
    i === 0 ? ctx.moveTo(Math.cos(a)*r, Math.sin(a)*r)
             : ctx.lineTo(Math.cos(a)*r, Math.sin(a)*r);
  }
  ctx.closePath();
}

function _diamond(ctx, r) {
  ctx.beginPath();
  ctx.moveTo(0, -r); ctx.lineTo(r * 0.65, 0);
  ctx.lineTo(0, r);  ctx.lineTo(-r * 0.65, 0);
  ctx.closePath();
}

function _roundedRect(ctx, hw, hh, rad) {
  ctx.beginPath();
  ctx.roundRect(-hw, -hh, hw*2, hh*2, rad);
}

function _gear(ctx, rOuter, rInner, teeth) {
  // Star-like shape for ECU
  ctx.beginPath();
  for (let i = 0; i < teeth * 2; i++) {
    const a = (i / (teeth * 2)) * Math.PI * 2 - Math.PI / 2;
    const r = i % 2 === 0 ? rOuter : rInner;
    i === 0 ? ctx.moveTo(Math.cos(a)*r, Math.sin(a)*r)
             : ctx.lineTo(Math.cos(a)*r, Math.sin(a)*r);
  }
  ctx.closePath();
}

function _house(ctx, r) {
  // Launch Pad: square with pointed roof
  const h = r * 0.9, w = r * 0.85;
  ctx.beginPath();
  ctx.moveTo(0, -r);          // top
  ctx.lineTo(w, -h * 0.1);    // roof right
  ctx.lineTo(w, h);           // bottom right
  ctx.lineTo(-w, h);          // bottom left
  ctx.lineTo(-w, -h * 0.1);   // roof left
  ctx.closePath();
}

function _factory(ctx, r) {
  // Square with stepped top (factory silhouette)
  const s = r * 0.9;
  ctx.beginPath();
  ctx.moveTo(-s, s);   ctx.lineTo(-s, -s * 0.4);
  ctx.lineTo(-s * 0.3, -s * 0.4); ctx.lineTo(-s * 0.3, -s);
  ctx.lineTo(s * 0.3, -s);  ctx.lineTo(s * 0.3, -s * 0.4);
  ctx.lineTo(s, -s * 0.4);  ctx.lineTo(s, s);
  ctx.closePath();
}

// ── Main pin drawer ────────────────────────────────────────────────────────────

function drawPIPin(ctx, x, y, baseR, typeId, lineWidth) {
  const color = PI_COLORS[typeId] || '#888';
  const scale = PI_RADII[typeId] || 1.0;
  const r = baseR * scale;

  ctx.save();
  ctx.translate(x, y);

  switch (typeId) {
    case 2542: _polygon(ctx, 6, r, Math.PI / 6); break;  // Command Center – hexagon
    case 2524: _house(ctx, r); break;                      // Launch Pad – house/rocket
    case 2562: _roundedRect(ctx, r * 0.85, r * 0.65, r * 0.25); break; // Storage – pill
    case 3068: _gear(ctx, r, r * 0.55, 6); break;          // ECU – gear (6 teeth)
    case 2481: _diamond(ctx, r); break;                    // Extractor Head – diamond
    case 2474: _factory(ctx, r); break;                    // Adv. Industrial – factory
    case 2552: _polygon(ctx, 4, r, Math.PI / 4); break;    // Basic Industrial – square
    case 2256: _polygon(ctx, 3, r, -Math.PI / 2); break;   // High-Tech Plant – triangle
    default:   ctx.beginPath(); ctx.arc(0, 0, r, 0, Math.PI * 2); break;
  }

  ctx.fillStyle = color + '30';
  ctx.fill();
  ctx.strokeStyle = color;
  ctx.lineWidth = lineWidth || 1.5;
  ctx.stroke();

  ctx.restore();
}

// ── Link extraction (from L array) ────────────────────────────────────────────

/**
 * Returns a Set of "min_min_max" strings for unique physical links.
 * L[i] = { S: source_pin_idx, D: dest_pin_idx }
 */
function extractLinks(layoutData) {
  const links = new Set();
  for (const lnk of (layoutData.L || [])) {
    if (lnk.S !== undefined && lnk.D !== undefined) {
      links.add(Math.min(lnk.S, lnk.D) + '_' + Math.max(lnk.S, lnk.D));
    }
  }
  return links;
}

// ── Projection helpers ─────────────────────────────────────────────────────────

/**
 * Azimuthal equidistant projection centred on the pin cluster.
 *
 * EVE coordinate system (confirmed from calli-eve/eve-pi source):
 *   La = colatitude in radians  (0 = north pole, π/2 = equator, π = south pole)
 *   Lo = longitude  in radians  (0 … 2π)
 *
 * Three.js equivalent: setFromSphericalCoords(r, La, Lo)
 *   → x = r·sin(La)·sin(Lo),  y = r·cos(La),  z = r·sin(La)·cos(Lo)
 *
 * The azimuthal equidistant projection preserves great-circle distances from
 * the centre point, so the on-screen layout matches the actual surface layout.
 *
 * Returns a toXY(La, Lo) → [canvasX, canvasY] closure.
 */
function buildProjection(pins, W, H, pad, extraTransform) {
  if (!pins.length) return null;
  extraTransform = extraTransform || { x: 0, y: 0, scale: 1 };

  // ── Cluster centroid (mean on the sphere via 3-D average) ──────────────────
  let cx = 0, cy = 0, cz = 0;
  for (const p of pins) {
    cx += Math.sin(p.La) * Math.cos(p.Lo);
    cy += Math.cos(p.La);
    cz += Math.sin(p.La) * Math.sin(p.Lo);
  }
  cx /= pins.length; cy /= pins.length; cz /= pins.length;
  // Back to spherical
  const La0 = Math.atan2(Math.sqrt(cx*cx + cz*cz), cy);  // colatitude
  const Lo0 = Math.atan2(cz, cx);                          // longitude

  // ── Azimuthal equidistant projection ──────────────────────────────────────
  // Angular distance c from centre, then scale by c/sin(c).
  // x points "east" (increasing Lo), y points "south" (increasing La).
  function project(La, Lo) {
    const cosC = Math.cos(La0)*Math.cos(La)
               + Math.sin(La0)*Math.sin(La)*Math.cos(Lo - Lo0);
    const c = Math.acos(Math.max(-1, Math.min(1, cosC)));
    if (c < 1e-10) return [0, 0];
    const k = c / Math.sin(c);
    return [
       k * Math.sin(La) * Math.sin(Lo - Lo0),
      -k * (Math.sin(La0)*Math.cos(La) - Math.cos(La0)*Math.sin(La)*Math.cos(Lo - Lo0)),
    ];
  }

  // ── Fit projected coords to canvas ────────────────────────────────────────
  const pts = pins.map(p => project(p.La, p.Lo));
  const xs = pts.map(p => p[0]), ys = pts.map(p => p[1]);
  const minX = Math.min(...xs), maxX = Math.max(...xs);
  const minY = Math.min(...ys), maxY = Math.max(...ys);
  const dX = maxX - minX || 1e-4;
  const dY = maxY - minY || 1e-4;

  const usableW = W - pad * 2, usableH = H - pad * 2;
  const baseScale = Math.min(usableW / dX, usableH / dY);
  const baseOffX  = pad + (usableW - dX * baseScale) / 2;
  const baseOffY  = pad + (usableH - dY * baseScale) / 2;

  return function(La, Lo) {
    const [px, py] = project(La, Lo);
    return [
      (baseOffX + (px - minX) * baseScale) * extraTransform.scale + extraTransform.x,
      (baseOffY + (py - minY) * baseScale) * extraTransform.scale + extraTransform.y,
    ];
  };
}

// ── Full render ────────────────────────────────────────────────────────────────

/**
 * Renders a PI template onto a canvas element.
 *
 * @param {HTMLCanvasElement} canvas
 * @param {object|string} layoutData  – parsed object or raw JSON string
 * @param {object} opts
 *   pad          {number}  canvas padding in px (default 12)
 *   pinScale     {number}  base pin radius = pinScale * 5 (default 1)
 *   lineWidth    {number}  link line width (default 1)
 *   linkAlpha    {number}  link opacity 0-1 (default 0.3)
 *   showLabels   {boolean} draw abbreviated labels when zoomed (default false)
 *   transform    {object}  {x, y, scale} for pan/zoom (default identity)
 */
function renderPITemplate(canvas, layoutData, opts) {
  opts = opts || {};
  if (typeof layoutData === 'string') {
    try { layoutData = JSON.parse(layoutData); } catch(e) { return; }
  }
  if (!layoutData) return;

  const pins = layoutData.P || [];
  if (!pins.length) return;

  const W = canvas.width, H = canvas.height;
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, W, H);

  const pad = opts.pad !== undefined ? opts.pad : 12;
  const transform = opts.transform || { x: 0, y: 0, scale: 1 };
  const toXY = buildProjection(pins, W, H, pad, transform);
  if (!toXY) return;

  const baseR = (opts.pinScale || 1) * 5;
  const alpha = opts.linkAlpha !== undefined ? opts.linkAlpha : 0.28;

  // ── Draw physical links (L array, correct S+D fields) ──
  const links = extractLinks(layoutData);
  ctx.strokeStyle = `rgba(100,165,210,${alpha})`;
  ctx.lineWidth = opts.lineWidth || 1;
  for (const pair of links) {
    const [ai, bi] = pair.split('_').map(Number);
    if (ai >= pins.length || bi >= pins.length) continue;
    const [ax, ay] = toXY(pins[ai].La, pins[ai].Lo);
    const [bx, by] = toXY(pins[bi].La, pins[bi].Lo);
    ctx.beginPath(); ctx.moveTo(ax, ay); ctx.lineTo(bx, by); ctx.stroke();
  }

  // ── Draw pins ──────────────────────────────────────────
  const lw = (opts.pinLineWidth || 1.5) * Math.min(transform.scale, 2);
  for (const pin of pins) {
    const [x, y] = toXY(pin.La, pin.Lo);
    drawPIPin(ctx, x, y, baseR * transform.scale, pin.T, lw);
  }

  // ── Optional labels (detail view when zoomed in) ──────
  if (opts.showLabels && transform.scale > 1.4) {
    const fontSize = Math.min(11, 8 * transform.scale);
    ctx.font = `${fontSize}px sans-serif`;
    ctx.textAlign = 'center';
    for (const pin of pins) {
      const [x, y] = toXY(pin.La, pin.Lo);
      const name = (PI_NAMES[pin.T] || '');
      // Abbreviated: first letter of each word
      const abbr = name.split(' ').filter(w => /^[A-Z]/.test(w)).map(w => w[0]).join('');
      if (!abbr) continue;
      const r = baseR * transform.scale * (PI_RADII[pin.T] || 1);
      ctx.fillStyle = (PI_COLORS[pin.T] || '#aaa') + 'cc';
      ctx.fillText(abbr, x, y + r + fontSize + 1);
    }
  }
}
