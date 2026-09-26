import { createRequire } from 'module';
import path from 'path';
const require = createRequire(
  path.join(process.env.USERPROFILE, '.cursor/products/leva-segura/pipeline/adapters/package.json')
);
const { chromium } = require('playwright');
const browser = await chromium.launch({ channel: 'chrome', headless: true });
const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
await page.goto('http://localhost:8886/', { waitUntil: 'networkidle', timeout: 60000 });
await page.addStyleTag({ content: '.site-header{display:none!important}' });
const data = await page.evaluate(() => {
  const cat = document.querySelector('.posts-hub .post-featured__cat');
  const img = document.querySelector('.posts-hub .post-featured__img');
  const body = document.querySelector('.posts-hub .post-featured__body');
  const feat = document.querySelector('.posts-hub .post-featured');
  const cs = cat ? getComputedStyle(cat) : null;
  const br = (el) => {
    if (!el) return null;
    const r = el.getBoundingClientRect();
    return { left: Math.round(r.left), top: Math.round(r.top), w: Math.round(r.width), h: Math.round(r.height) };
  };
  return {
    catText: cat?.textContent?.trim(),
    catPos: cs?.position,
    catDisplay: cs?.display,
    cat: br(cat),
    img: br(img),
    body: br(body),
    feat: br(feat),
    imgPct: img && feat ? Math.round((img.getBoundingClientRect().width / feat.getBoundingClientRect().width) * 100) : null,
  };
});
console.log(JSON.stringify(data, null, 2));
await page.locator('.posts-hub .post-featured').screenshot({ path: path.join(process.env.USERPROFILE, '.cursor/tmp-fidelity/featured-only.png') });
await browser.close();
