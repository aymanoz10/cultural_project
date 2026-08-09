import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import HTMLtoDOCX from 'html-to-docx';
import puppeteer from 'puppeteer';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const htmlPath = path.join(__dirname, 'DSS-Professional-Datasheet-Arabic.html');
const docxPath = path.join(__dirname, 'DSS-Professional-Datasheet-Arabic.docx');
const pdfPath = path.join(__dirname, 'DSS-Professional-Datasheet-Arabic.pdf');

const html = fs.readFileSync(htmlPath, 'utf8');

console.log('Generating DOCX...');
const docxBuffer = await HTMLtoDOCX(html, null, {
  orientation: 'portrait',
  margins: {
    top: 1200,
    right: 1200,
    bottom: 1200,
    left: 1200,
  },
  lang: 'ar-SA',
  table: { row: { cantSplit: true } },
  footer: true,
  pageNumber: true,
  font: 'Arial',
  decodeUnicode: true,
});
fs.writeFileSync(docxPath, docxBuffer);
console.log('DOCX saved:', docxPath);

console.log('Generating PDF...');
const browser = await puppeteer.launch({
  headless: true,
  args: ['--font-render-hinting=none', '--disable-dev-shm-usage'],
});
const page = await browser.newPage();
await page.goto(`file:///${htmlPath.replace(/\\/g, '/')}`, {
  waitUntil: 'networkidle0',
  timeout: 120000,
});
await page.pdf({
  path: pdfPath,
  format: 'A4',
  printBackground: true,
  preferCSSPageSize: true,
  margin: { top: '12mm', right: '12mm', bottom: '12mm', left: '12mm' },
});
await browser.close();
console.log('PDF saved:', pdfPath);
