/**
 * Site Editor: scroll canvas and assert overlay header gets .is-scrolled + non-transparent bg.
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
const out = process.env.OUT || path.join(process.env.USERPROFILE || '', '.cursor/tmp-fidelity/header-scroll');

const browser = await chromium.launch({ channel: 'chrome', headless: true });
const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });

await page.goto(base + '/wp-login.php', { waitUntil: 'domcontentloaded', timeout: 60000 });
if (await page.locator('#user_login').count()) {
  await page.locator('#user_login').fill(process.env.WP_USER || 'admin');
  await page.locator('#user_pass').fill(process.env.WP_PASS || 'password');
  await page.locator('#wp-submit').click({ noWaitAfter: true });
  await page.waitForURL(/wp-admin/, { timeout: 90000 }).catch(() => {});
  await page.waitForTimeout(1500);
}

await page.goto(
  base + '/wp-admin/site-editor.php?p=%2Fwp_template%2Fiawp%2F%2Ffront-page&canvas=edit',
  { waitUntil: 'networkidle', timeout: 120000 }
);
await page.waitForTimeout(7000);

const frame = page
  .frameLocator('iframe[name="editor-canvas"], iframe.edit-site-visual-editor__editor-canvas')
  .first();

// Wait for header
await frame.locator('.header--overlay, header.site-header').first().waitFor({ timeout: 30000 });

const before = await frame.locator('body').evaluate(() => {
  const h = document.querySelector('.header--overlay');
  if (!h) return { err: 'no header' };
  const cs = getComputedStyle(h);
  return {
    scrolled: h.classList.contains('is-scrolled'),
    bg: cs.backgroundColor,
    classes: h.className,
  };
});

// Scroll canvas iframe
await frame.locator('body').evaluate(() => {
  window.scrollTo(0, 900);
  document.documentElement.scrollTop = 900;
  document.body.scrollTop = 900;
});
await page.waitForTimeout(800);

const after = await frame.locator('body').evaluate(() => {
  const h = document.querySelector('.header--overlay');
  if (!h) return { err: 'no header' };
  const cs = getComputedStyle(h);
  const beforeEl = getComputedStyle(h, '::before');
  return {
    scrolled: h.classList.contains('is-scrolled'),
    bg: cs.backgroundColor,
    beforeOpacity: beforeEl.opacity,
    beforeBg: beforeEl.backgroundColor,
    scrollY: window.scrollY,
    scriptPresent: !!document.querySelector('script[src*="block-header-behavior"]'),
  };
});

await page.screenshot({ path: out + '-chrome.png', fullPage: false });
try {
  await frame.locator('body').screenshot({ path: out + '-canvas.png' });
} catch {
  /* ignore */
}

const invalid = await page.getByText(/conteúdo inesperado|Tentar restaurar/i).count();

console.log(JSON.stringify({ before, after, invalid }, null, 2));

const bgOk =
  after.scrolled === true &&
  after.bg &&
  after.bg !== 'rgba(0, 0, 0, 0)' &&
  after.bg !== 'transparent';

await browser.close();
process.exit(bgOk && invalid === 0 ? 0 : 2);
