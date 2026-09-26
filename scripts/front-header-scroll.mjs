/**
 * Front: header overlay gets .is-scrolled + opaque bg after scroll.
 */
import { createRequire } from 'module';
import path from 'path';

const require = createRequire(
  path.join(
    process.env.USERPROFILE || '',
    '.cursor/products/leva-segura/pipeline/adapters/package.json'
  )
);
const { chromium } = require('playwright');

const base = process.env.FRONT_URL || 'http://localhost:8886';
const out = process.env.OUT || path.join(process.env.USERPROFILE || '', '.cursor/tmp-fidelity/header-scroll-front');

const browser = await chromium.launch({ channel: 'chrome', headless: true });
const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
await page.goto(base + '/', { waitUntil: 'domcontentloaded', timeout: 60000 });
await page.waitForSelector('.header--overlay', { timeout: 30000 });

const top = await page.evaluate(() => {
  const h = document.querySelector('.header--overlay');
  const cs = h ? getComputedStyle(h) : null;
  return {
    scrolled: h?.classList.contains('is-scrolled'),
    bg: cs?.backgroundColor,
    beforeOpacity: h ? getComputedStyle(h, '::before').opacity : null,
  };
});

await page.evaluate(() => window.scrollTo(0, 800));
await page.waitForTimeout(500);

const scrolled = await page.evaluate(() => {
  const h = document.querySelector('.header--overlay');
  const cs = h ? getComputedStyle(h) : null;
  return {
    scrolled: h?.classList.contains('is-scrolled'),
    bg: cs?.backgroundColor,
    beforeOpacity: h ? getComputedStyle(h, '::before').opacity : null,
    scrollY: window.scrollY,
  };
});

await page.screenshot({ path: out + '.png', fullPage: false });
console.log(JSON.stringify({ top, scrolled }, null, 2));

const ok =
  scrolled.scrolled === true &&
  scrolled.bg &&
  scrolled.bg !== 'rgba(0, 0, 0, 0)' &&
  Number(scrolled.beforeOpacity) > 0.5;

await browser.close();
process.exit(ok ? 0 : 2);
