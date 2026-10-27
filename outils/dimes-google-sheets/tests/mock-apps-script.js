// Simulation minimale des services Apps Script utilisés par Code.gs,
// pour tester la logique du script avec Node (node --test tests/).
'use strict';

const fs = require('fs');
const path = require('path');
const vm = require('vm');

function colIndex(lettres) {
  let n = 0;
  for (const ch of lettres) n = n * 26 + (ch.charCodeAt(0) - 64);
  return n;
}

function parseA1(a1) {
  const m = /^([A-Z]+)(\d+)(?::([A-Z]+)(\d+))?$/.exec(a1);
  if (!m) throw new Error('A1 non géré : ' + a1);
  const r1 = Number(m[2]), c1 = colIndex(m[1]);
  const r2 = m[4] ? Number(m[4]) : r1, c2 = m[3] ? colIndex(m[3]) : c1;
  return [r1, c1, r2 - r1 + 1, c2 - c1 + 1];
}

function lettre(c) {
  let s = '';
  while (c > 0) { const r = (c - 1) % 26; s = String.fromCharCode(65 + r) + s; c = Math.floor((c - 1) / 26); }
  return s;
}

// Les dates sont recréées dans le contexte du script, comme dans Apps Script
// où script et classeur partagent le même constructeur Date.
let DateScript = Date;
const estDate = v => Object.prototype.toString.call(v) === '[object Date]';
const copie = v => (estDate(v) ? new DateScript(v.getTime()) : v);

class Plage {
  constructor(feuille, r, c, nr, nc) {
    if (r < 1 || c < 1 || nr < 1 || nc < 1) throw new Error('Plage invalide ' + [r, c, nr, nc]);
    if (r + nr - 1 > feuille.maxRows || c + nc - 1 > feuille.maxCols) {
      throw new Error('Plage hors de la grille ' + feuille.nom + ' ' + [r, c, nr, nc] + ' > ' + [feuille.maxRows, feuille.maxCols]);
    }
    Object.assign(this, { feuille, r, c, nr, nc });
  }
  getValues() {
    const out = [];
    for (let i = 0; i < this.nr; i++) {
      const l = [];
      for (let j = 0; j < this.nc; j++) l.push(copie(this.feuille.get(this.r + i, this.c + j)));
      out.push(l);
    }
    return out;
  }
  getValue() { return copie(this.feuille.get(this.r, this.c)); }
  getDisplayValue() { const v = this.getValue(); return estDate(v) ? v.toISOString() : String(v); }
  setValues(v) {
    if (v.length !== this.nr || v.some(l => l.length !== this.nc)) {
      throw new Error('Dimensions incorrectes : ' + v.length + 'x' + (v[0] || []).length + ' pour ' + this.nr + 'x' + this.nc);
    }
    v.forEach((l, i) => l.forEach((x, j) => this.feuille.set(this.r + i, this.c + j, copie(x))));
    return this;
  }
  setValue(v) { this.feuille.set(this.r, this.c, copie(v)); return this; }
  clearContent() {
    for (let i = 0; i < this.nr; i++) for (let j = 0; j < this.nc; j++) this.feuille.set(this.r + i, this.c + j, '');
    return this;
  }
  getRow() { return this.r; }
  getColumn() { return this.c; }
  getNumRows() { return this.nr; }
  getNumColumns() { return this.nc; }
  getSheet() { return this.feuille; }
  getA1Notation() {
    const a = lettre(this.c) + this.r;
    return this.nr === 1 && this.nc === 1 ? a : a + ':' + lettre(this.c + this.nc - 1) + (this.r + this.nr - 1);
  }
}

class Metadonnee {
  constructor(feuille, cle, valeur) { Object.assign(this, { feuille, cle, valeur }); }
  getKey() { return this.cle; }
  getValue() { return this.valeur; }
  setValue(v) { this.valeur = v; return this; }
  remove() { this.feuille.meta = this.feuille.meta.filter(m => m !== this); }
}

class Feuille {
  constructor(classeur, nom, id, maxRows = 1000, maxCols = 26) {
    Object.assign(this, { classeur, nom, id, maxRows, maxCols, cells: new Map(), cachee: false, meta: [] });
  }
  get(r, c) { const v = this.cells.get(r + ',' + c); return v === undefined ? '' : v; }
  set(r, c, v) {
    if (v === '' || v === null || v === undefined) this.cells.delete(r + ',' + c);
    else this.cells.set(r + ',' + c, v);
  }
  getName() { return this.nom; }
  setName(n) { this.nom = n; return this; }
  getSheetId() { return this.id; }
  getRange(a, b, c, d) {
    if (typeof a === 'string') return new Plage(this, ...parseA1(a));
    return new Plage(this, a, b, c === undefined ? 1 : c, d === undefined ? 1 : d);
  }
  getLastRow() {
    let max = 0;
    for (const [k, v] of this.cells) if (v !== '') max = Math.max(max, Number(k.split(',')[0]));
    return max;
  }
  getMaxRows() { return this.maxRows; }
  insertRowsAfter(apres, n) { this.maxRows += n; return this; }
  hideSheet() { this.cachee = true; return this; }
  activate() { this.classeur.active = this; return this; }
  copyTo(cible) {
    const f = cible.insertSheet('Copie de ' + this.nom);
    f.maxRows = this.maxRows; f.maxCols = this.maxCols;
    for (const [k, v] of this.cells) f.cells.set(k, copie(v));
    return f;
  }
  getDeveloperMetadata() { return this.meta.slice(); }
  addDeveloperMetadata(cle, valeur) { const m = new Metadonnee(this, cle, valeur); this.meta.push(m); return this; }
  getActiveRange() { return this.classeur.selection[0] || null; }
}

let compteurId = 0;
const registre = new Map();

class Classeur {
  constructor(nom) {
    this.id = 'ss' + (++compteurId);
    this.nom = nom;
    this.feuilles = [];
    this.toasts = [];
    this.selection = [];
    this.active = null;
    this.trashed = false;
    registre.set(this.id, this);
  }
  insertSheet(nom, id) {
    const f = new Feuille(this, nom, id === undefined ? 1000 + this.feuilles.length + compteurId * 100 : id);
    this.feuilles.push(f);
    return f;
  }
  getSheetByName(n) { return this.feuilles.find(f => f.nom === n) || null; }
  getSheets() { return this.feuilles.slice(); }
  deleteSheet(f) {
    if (this.feuilles.length === 1) throw new Error('Impossible de supprimer la dernière feuille');
    this.feuilles = this.feuilles.filter(x => x !== f);
  }
  getActiveSheet() { return this.active; }
  getActiveRangeList() { return this.selection.length ? { getRanges: () => this.selection.slice() } : null; }
  selectionner(feuille, ...a1s) { this.active = feuille; this.selection = a1s.map(a => feuille.getRange(a)); }
  toast(m) { this.toasts.push(m); }
  getId() { return this.id; }
  getName() { return this.nom; }
  getSpreadsheetTimeZone() { return 'Europe/Paris'; }
  cloner(nom) {
    const c = new Classeur(nom);
    this.feuilles.forEach(f => {
      const g = c.insertSheet(f.nom, f.id);
      Object.assign(g, { maxRows: f.maxRows, maxCols: f.maxCols, cachee: f.cachee });
      for (const [k, v] of f.cells) g.cells.set(k, copie(v));
      f.meta.forEach(m => g.addDeveloperMetadata(m.cle, m.valeur));
    });
    return c;
  }
}

function creerEnvironnement() {
  const etat = {
    reponses: [],      // réponses à donner aux ui.alert OUI/NON (true = Oui)
    alertes: [],       // messages affichés
    dialogues: [],
    pdfs: [],          // fichiers PDF créés
    exports: [],       // classeurs temporaires au moment de l'export
    classeur: null,
  };
  const Button = { YES: 'YES', NO: 'NO', OK: 'OK' };
  const ButtonSet = { OK: 'OK', YES_NO: 'YES_NO' };
  const menu = () => { const m = { addItem: () => m, addSeparator: () => m, addSubMenu: () => m, addToUi: () => m }; return m; };
  const ui = {
    Button, ButtonSet,
    alert(titre, message, boutons) {
      etat.alertes.push(message);
      if (boutons === ButtonSet.YES_NO) {
        const r = etat.reponses.length ? etat.reponses.shift() : true;
        return r ? Button.YES : Button.NO;
      }
      return Button.OK;
    },
    createMenu: menu,
    showSidebar() {},
    showModalDialog(html, titre) { etat.dialogues.push(titre); },
  };
  const fichier = (ss) => ({
    getId: () => ss.id,
    getName: () => ss.nom,
    getUrl: () => 'https://docs.google.com/spreadsheets/d/' + ss.id,
    getParents: () => ({ hasNext: () => false }),
    makeCopy: (nom) => fichier(ss.cloner(nom)),
    setTrashed: (t) => { ss.trashed = t; },
  });
  const dossier = {
    getFoldersByName: () => ({ hasNext: () => false }),
    createFolder: () => dossier,
    createFile: (blob) => { etat.pdfs.push(blob.nom); return { getName: () => blob.nom, getUrl: () => 'https://drive/pdf' }; },
  };
  const pad = n => String(n).padStart(2, '0');
  const contexte = {
    console,
    SpreadsheetApp: {
      getActive: () => etat.classeur,
      getUi: () => ui,
      flush: () => {},
      create: (nom) => { const c = new Classeur(nom); c.insertSheet('Feuille 1', 0); return c; },
      openById: (id) => registre.get(id),
    },
    LockService: { getDocumentLock: () => ({ tryLock: () => true, releaseLock: () => {} }) },
    Utilities: {
      sleep: () => {},
      formatDate: (d, tz, fmt) => fmt
        .replace('yyyy', d.getFullYear()).replace('MM', pad(d.getMonth() + 1)).replace('dd', pad(d.getDate()))
        .replace('HH', pad(d.getHours())).replace('mm', pad(d.getMinutes())).replace(/'/g, ''),
    },
    Session: { getScriptTimeZone: () => 'Europe/Paris' },
    DriveApp: {
      getFileById: (id) => fichier(registre.get(id)),
      getRootFolder: () => dossier,
    },
    UrlFetchApp: {
      fetch: (url) => {
        const id = /\/d\/([^/]+)\//.exec(url)[1];
        etat.exports.push(registre.get(id));
        return { getResponseCode: () => 200, getBlob: () => ({ nom: '', setName(n) { this.nom = n; return this; } }) };
      },
    },
    ScriptApp: { getOAuthToken: () => 'jeton' },
    HtmlService: {
      createHtmlOutput: () => { const h = { setTitle: () => h, setWidth: () => h, setHeight: () => h }; return h; },
    },
  };
  vm.createContext(contexte);
  const code = fs.readFileSync(path.join(__dirname, '..', 'Code.gs'), 'utf8');
  vm.runInContext(code, contexte, { filename: 'Code.gs' });
  DateScript = vm.runInContext('Date', contexte);
  return { ctx: contexte, etat, ui, Classeur, eval: (expr) => vm.runInContext(expr, contexte) };
}

module.exports = { creerEnvironnement, Classeur, registre, estDate };
