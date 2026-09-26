/**
 * Editor validity probe for iawp front-page template.
 * Fail if canvas/chrome shows unexpected/invalid block recovery UI.
 *
 * Login (C-37 / C-39): WP_USER=admin WP_PASS=password — mesma receita dos
 * probes do tema. Drift Studio↔WP →:
 *   studio wp user update admin --user_pass=password --path <site>
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
const out = process.env.OUT || path.join(process.env.USERPROFILE || '', '.cursor/tmp-fidelity/editor-probe');
const user = process.env.WP_USER || 'admin';
const pass = process.env.WP_PASS || 'password';

const browser = await chromium.launch({ channel: 'chrome', headless: true });
const page = await browser.newPage({ viewport: { width: 1440, height: 1100 } });

await page.goto(base + '/wp-login.php', { waitUntil: 'domcontentloaded', timeout: 60000 });
if (await page.locator('#user_login').count()) {
  await page.locator('#user_login').fill(user);
  await page.locator('#user_pass').fill(pass);
  await page.locator('#wp-submit').click({ noWaitAfter: true });
  await page.waitForURL(/wp-admin/, { timeout: 90000 }).catch(() => {});
  await page.waitForTimeout(1500);
}
if (!/wp-admin/.test(page.url())) {
  console.log(
    JSON.stringify(
      {
        error: 'login_failed',
        url: page.url(),
        hint: 'C-37 sync: studio wp user update admin --user_pass=password',
      },
      null,
      2
    )
  );
  await browser.close();
  process.exit(2);
}

const editorUrl =
  base + '/wp-admin/site-editor.php?p=%2Fwp_template%2Fiawp%2F%2Ffront-page&canvas=edit';
await page.goto(editorUrl, { waitUntil: 'domcontentloaded', timeout: 120000 });
await page.waitForTimeout(12000);

const frame = page
  .frameLocator('iframe[name="editor-canvas"], iframe.edit-site-visual-editor__editor-canvas')
  .first();

const bodyText = await page.locator('body').innerText().catch(() => '');
let canvasText = '';
try {
  canvasText = await frame.locator('body').innerText({ timeout: 15000 });
} catch {
  canvasText = '(no canvas text)';
}

const patterns = [
  /conteúdo inesperado/i,
  /unexpected or invalid/i,
  /Tentar restaurar/i,
  /Attempt Recovery/i,
  /Converter em HTML/i,
];

const hay = bodyText + '\n' + canvasText;
const hits = patterns.filter((p) => p.test(hay)).map((p) => String(p));

await page.screenshot({ path: out + '-chrome.png', fullPage: false });
try {
  await frame.locator('body').screenshot({ path: out + '-canvas.png' });
} catch {
  /* canvas may not be ready */
}

console.log(
  JSON.stringify(
    {
      hits,
      mainOk: /main-content|Últimos|IA\+WP/i.test(hay),
      canvasPreview: canvasText.slice(0, 500),
      editorUrl,
    },
    null,
    2
  )
);
await browser.close();
process.exit(hits.length ? 2 : 0);
