/**
 * Scroll editor canvas + check list view for invalid blocks.
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
const out = process.env.OUT || path.join(process.env.USERPROFILE || '', '.cursor/tmp-fidelity/editor-probe2');

const browser = await chromium.launch({ channel: 'chrome', headless: true });
const page = await browser.newPage({ viewport: { width: 1440, height: 1100 } });

await page.goto(base + '/wp-login.php', { waitUntil: 'domcontentloaded', timeout: 60000 });
if (await page.locator('#user_login').count()) {
  await page.locator('#user_login').fill(process.env.WP_USER || 'admin');
  await page.locator('#user_pass').fill(process.env.WP_PASS || 'password');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle', timeout: 60000 }).catch(() => {}),
    page.locator('#wp-submit').click(),
  ]);
}

await page.goto(
  base + '/wp-admin/site-editor.php?p=%2Fwp_template%2Fiawp%2F%2Ffront-page&canvas=edit',
  { waitUntil: 'networkidle', timeout: 120000 }
);
await page.waitForTimeout(6000);

const frame = page
  .frameLocator('iframe[name="editor-canvas"], iframe.edit-site-visual-editor__editor-canvas')
  .first();

// Scroll canvas to latest section
try {
  await frame.locator('#latest-heading, .section--rule, text=Últimos').first().scrollIntoViewIfNeeded({ timeout: 10000 });
} catch {
  await frame.locator('body').evaluate((b) => b.ownerDocument.defaultView.scrollBy(0, 900));
}
await page.waitForTimeout(1500);

const warningCount = await page.locator('.block-editor-warning, .block-editor-warning__message').count();
const canvasWarning = await frame.locator('.block-editor-warning, .block-editor-warning__message').count().catch(() => 0);
const tryRestore = await page.getByRole('button', { name: /Tentar restaurar|Attempt Recovery/i }).count();
const unexpected = await page.getByText(/conteúdo inesperado|unexpected or invalid/i).count();

// Open list view if possible
let listText = '';
try {
  const listBtn = page.locator('button[aria-label="Vista de lista"], button[aria-label="List View"], button[aria-label*="lista" i]').first();
  if (await listBtn.count()) {
    await listBtn.click();
    await page.waitForTimeout(800);
    listText = await page.locator('.block-editor-list-view-tree, .block-editor-list-view-leaf').allInnerTexts().then((a) => a.join('\n')).catch(() => '');
  }
} catch {
  /* ignore */
}

await page.screenshot({ path: out + '-chrome.png', fullPage: false });
try {
  await frame.locator('body').screenshot({ path: out + '-canvas.png' });
} catch {
  /* ignore */
}

const latestVisible = await frame.locator('text=Últimos').count().catch(() => 0);
const featuredVisible = await frame.locator('.post-featured, text=Ler artigo completo').count().catch(() => 0);

console.log(
  JSON.stringify(
    {
      warningCount,
      canvasWarning,
      tryRestore,
      unexpected,
      latestVisible,
      featuredVisible,
      listPreview: listText.slice(0, 800),
    },
    null,
    2
  )
);

await browser.close();
const bad = warningCount + canvasWarning + tryRestore + unexpected;
process.exit(bad ? 2 : 0);
