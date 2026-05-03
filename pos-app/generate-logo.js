const { Jimp } = require('jimp')
const path = require('path')

// Brand colours
const BG     = 0x1e3a5fff  // deep navy blue
const GREEN  = 0x22c55eff  // accent green
const WHITE  = 0xffffffff

function circle(img, cx, cy, r, color) {
  for (let y = cy - r; y <= cy + r; y++) {
    for (let x = cx - r; x <= cx + r; x++) {
      if ((x - cx) ** 2 + (y - cy) ** 2 <= r * r) img.setPixelColor(color, x, y)
    }
  }
}

function rect(img, x, y, w, h, color) {
  for (let py = y; py < y + h; py++)
    for (let px = x; px < x + w; px++)
      img.setPixelColor(color, px, py)
}

async function makeIcon(size, outPath) {
  const img = new Jimp({ width: size, height: size, color: BG })

  const s = size / 100  // scale factor

  // ── Green ring (top centre) ──────────────────────
  const ringCX = Math.round(50 * s)
  const ringCY = Math.round(38 * s)
  const ringR  = Math.round(28 * s)
  const holeR  = Math.round(18 * s)
  circle(img, ringCX, ringCY, ringR, GREEN)
  circle(img, ringCX, ringCY, holeR, BG)

  // ── "A" letter inside ring ───────────────────────
  const lh   = Math.round(20 * s)   // letter height
  const lx   = Math.round(42 * s)   // left start
  const ly   = Math.round(28 * s)   // top
  const sw   = Math.max(2, Math.round(2.5 * s))  // stroke width

  // Left leg of A
  for (let i = 0; i < lh; i++) {
    const px = lx + Math.round(i * 0.28)
    for (let k = 0; k < sw; k++) img.setPixelColor(WHITE, px + k, ly + i)
  }
  // Right leg of A
  for (let i = 0; i < lh; i++) {
    const px = lx + Math.round(14 * s) - Math.round(i * 0.28)
    for (let k = 0; k < sw; k++) img.setPixelColor(WHITE, px + k, ly + i)
  }
  // Crossbar
  const bY  = ly + Math.round(lh * 0.55)
  const bX1 = lx + Math.round(3.5 * s)
  const bX2 = lx + Math.round(10.5 * s)
  for (let x = bX1; x <= bX2; x++)
    for (let k = 0; k < sw; k++) img.setPixelColor(WHITE, x, bY + k)

  // ── Bottom card / receipt bar ────────────────────
  const barH = Math.round(10 * s)
  const barY = Math.round(70 * s)
  const barX = Math.round(12 * s)
  const barW = Math.round(76 * s)
  rect(img, barX, barY, barW, barH, GREEN)

  // Three dots on bar (chip/tap style)
  const dotR = Math.round(2.5 * s)
  const dotY = barY + Math.round(barH / 2)
  for (const dotX of [barX + Math.round(barW * 0.2), barX + Math.round(barW * 0.38), barX + Math.round(barW * 0.56)]) {
    circle(img, dotX, dotY, dotR, BG)
  }

  // ── "POS" text below bar (pixel dots) ───────────
  // Simple 3×5 dot font for each letter
  const dotSz  = Math.max(2, Math.round(2 * s))
  const textY  = Math.round(84 * s)
  const textX  = Math.round(22 * s)
  const gap    = Math.round(9 * s)

  // P: col, row bitmaps (3 wide × 5 tall)
  const glyphs = {
    P: [[1,1,0],[1,0,1],[1,1,0],[1,0,0],[1,0,0]],
    O: [[1,1,1],[1,0,1],[1,0,1],[1,0,1],[1,1,1]],
    S: [[1,1,1],[1,0,0],[1,1,1],[0,0,1],[1,1,1]],
  }

  let cx2 = textX
  for (const [ch, rows] of Object.entries(glyphs)) {
    rows.forEach((row, ry) => {
      row.forEach((on, rx) => {
        if (on) rect(img, cx2 + rx * dotSz, textY + ry * dotSz, dotSz, dotSz, WHITE)
      })
    })
    cx2 += gap
  }

  await img.write(outPath)
  console.log('✓', path.basename(outPath), `(${size}×${size})`)
}

;(async () => {
  const assets = path.join(__dirname, 'assets')
  await makeIcon(1024, path.join(assets, 'icon.png'))
  await makeIcon(1024, path.join(assets, 'adaptive-icon.png'))
  await makeIcon(512,  path.join(assets, 'splash-icon.png'))
  await makeIcon(48,   path.join(assets, 'favicon.png'))
  console.log('\nDone — all icons generated.')
})()
