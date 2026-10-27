/**
 * Tableau des dîmes et reçus fiscaux, version Google Sheets.
 *
 * Reprise du classeur Excel « Tableau dîmes et RF » (macros VBA) :
 * saisie des dîmes par donateur et par mois, fiches en attente,
 * numérotation des reçus fiscaux, génération des reçus en PDF
 * (original + duplicata), annulation, points de retour.
 *
 * Installation : dans le Google Sheet, menu Extensions > Apps Script,
 * remplacer le contenu de Code.gs par ce fichier, enregistrer, puis
 * recharger le classeur. Le menu « Dîmes » apparaît dans la barre de menus.
 */

const FEUILLES = {
  params: { nom: 'Paramètres', id: 1 },
  adresses: { nom: 'Adresses', id: 2 },
  fiche: { nom: 'Fiche', id: 3 },
  attente: { nom: 'Fiches en attente', id: 4 },
  recus: { nom: 'Fiches reçus', id: 5 },
  recap: { nom: 'Récapitulatif', id: 6 },
  modele: { nom: 'Reçu fiscal', id: 7 },
};

const MOIS = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet',
  'août', 'septembre', 'octobre', 'novembre', 'décembre'];

// Une fiche = 68 champs, dans l'ordre des colonnes B:BQ de « Fiches en attente » :
// Nom, Prénom, Adresse 1, Adresse 2, Code postal, Ville, Particulier, Société,
// puis pour chaque mois : Montant, Jour, Chèque, Espèces, Virement.
const NB_CHAMPS = 68;
const iMontant = m => 8 + 5 * m;
const iJour = m => 9 + 5 * m;
const iCheque = m => 10 + 5 * m;
const iEspeces = m => 11 + 5 * m;
const iVirement = m => 12 + 5 * m;

// Fiches en attente : données à partir de B3 (la colonne A contient le total calculé).
const ATTENTE = { ligne1: 3, col1: 2 };
// Fiches reçus : A n° du reçu, B montant, C date d'impression, D annulé, E:BT la fiche.
const RECUS = { ligne1: 3, nbCols: 4 + NB_CHAMPS };
const ADRESSES = { ligne1: 2, nbCols: 6 };

const FICHE = {
  identite: 'B4:B9', type: 'G4:G5', colType: 7, origine: 'B10', mois: 'B12:F23',
  nom: 'B4', prenom: 'B5', adresse: 'B6:B9', premiereLigneMois: 12,
};
const PARAMS = { eglise: 'B3', annee: 'B4', retour: 'B7' };

// Cases du modèle « Reçu fiscal » (ligne, colonne) dans la moitié gauche ;
// la moitié droite (duplicata) est décalée de 10 colonnes.
const MODELE = {
  lignes: 42, cols: 19, decalage: 10,
  champs: {
    numero: [2, 7], libelle: [4, 1], nom: [22, 1], prenom: [22, 5],
    adr1: [24, 2], adr2: [25, 2], cp: [26, 3], ville: [26, 7],
    somme: [29, 3], lettres: [30, 3], dateDon: [31, 3], anneeDon: [31, 7],
    art200: [33, 1], art238: [33, 3],
    especes: [39, 1], cheque: [39, 3], virement: [39, 5], dateRecu: [41, 3],
  },
};

const RETOUR = {
  attente: 'Retour - Fiches en attente',
  recus: 'Retour - Fiches reçus',
  adresses: 'Retour - Adresses',
};

const MAX_PDF = 80;
const CLE_SOURCE = 'dimes_source';
const TITRE = 'Dîmes';

/* ------------------------------------------------------------------ */
/* Menu, panneau et déclencheurs                                       */
/* ------------------------------------------------------------------ */

function onOpen() {
  const ui = SpreadsheetApp.getUi();
  ui.createMenu(TITRE)
    .addItem('Ouvrir le panneau de saisie', 'ouvrirPanneau')
    .addSeparator()
    .addSubMenu(ui.createMenu('Fiche')
      .addItem('Mémoriser (fiche en attente)', 'memoriserFiche')
      .addItem('Enregistrer le reçu', 'enregistrerRecuFiche')
      .addItem('Aperçu du reçu', 'apercuRecuFiche')
      .addItem('Effacer la fiche', 'effacerFiche'))
    .addSubMenu(ui.createMenu('Fiches en attente')
      .addItem('Enregistrer les lignes sélectionnées en reçus', 'enregistrerLignesSelectionnees')
      .addItem('Compléter les adresses', 'completerAdressesAttente')
      .addItem('Trier par nom', 'trierAttente'))
    .addSubMenu(ui.createMenu('Fiches reçus')
      .addItem('Générer le PDF des reçus sélectionnés', 'genererRecusPdf')
      .addItem('Annuler ou rétablir le reçu sélectionné', 'annulerRecuSelectionne')
      .addItem('Compléter les adresses des reçus non imprimés', 'completerAdressesRecus'))
    .addSubMenu(ui.createMenu('Adresses')
      .addItem('Trier par nom', 'trierAdresses'))
    .addSubMenu(ui.createMenu('Sauvegardes')
      .addItem('Créer un point de retour', 'creerPointDeRetour')
      .addItem('Revenir au point de retour', 'revenirAuPointDeRetour')
      .addItem('Faire une copie de sauvegarde dans Drive', 'copieDeSauvegarde')
      .addItem("Préparer le classeur de l'année suivante", 'preparerAnneeSuivante'))
    .addToUi();
}

function ouvrirPanneau() {
  const html = HtmlService.createHtmlOutput(PANNEAU_HTML).setTitle('Dîmes : saisie');
  SpreadsheetApp.getUi().showSidebar(html);
}

function onEdit(e) {
  if (!e || !e.range) return;
  try {
    const sh = e.range.getSheet();
    const nom = sh.getName();
    if (nom === FEUILLES.fiche.nom) surModifFiche_(e, sh);
    else if (nom === FEUILLES.params.nom) surModifParams_(e, sh);
  } catch (err) {
    console.error(err);
  }
}

function surModifFiche_(e, sh) {
  if (e.range.getNumRows() !== 1 || e.range.getNumColumns() !== 1) return;
  const r = e.range.getRow();
  const c = e.range.getColumn();
  const coche = e.value === 'TRUE';
  const l1 = FICHE.premiereLigneMois;
  // Une seule case cochée par mois : chèque (C), espèces (D) ou virement (E).
  if (r >= l1 && r < l1 + 12 && c >= 3 && c <= 5) {
    if (!coche) return;
    sh.getRange(r, 3, 1, 3).setValues([[c === 3, c === 4, c === 5]]);
    if (estVide_(sh.getRange(r, 2).getValue())) {
      e.source.toast('Pensez à saisir le montant de ' + MOIS[r - l1] + '.', TITRE, 4);
    }
    return;
  }
  // Particulier (G4) ou Société (G5).
  if (c === FICHE.colType && (r === 4 || r === 5)) {
    if (coche) sh.getRange(r === 4 ? 5 : 4, FICHE.colType).setValue(false);
    return;
  }
  if (c === 2 && (r === 4 || r === 5)) rechercheAuto_(sh, e.source);
}

// Après saisie du nom et du prénom : reprend la fiche en attente ou l'adresse
// existante, comme le faisait le classeur Excel.
function rechercheAuto_(sh, ss) {
  const nom = texte_(sh.getRange(FICHE.nom).getValue());
  const prenom = texte_(sh.getRange(FICHE.prenom).getValue());
  if (!nom || !prenom) return;
  const src = lireSource_();
  if (src.type === 'attente' || src.type === 'recu') return;
  const k = cle_(nom, prenom);

  const att = lireAttente_().find(a => cleFiche_(a.rec) === k);
  if (att) {
    const mois = sh.getRange(FICHE.mois).getValues();
    const vide = mois.every(l => estVide_(l[0]) && estVide_(l[4]) &&
      l[1] !== true && l[2] !== true && l[3] !== true);
    if (vide) {
      afficherFiche_(att.rec, 'Fiche en attente', { type: 'attente', ligne: att.ligne, cle: k });
      ss.toast('Fiche en attente de ' + nom + ' ' + prenom + ' chargée.', TITRE, 5);
    } else {
      ss.toast('Une fiche en attente existe déjà pour cette personne. « Mémoriser » proposera ' +
        "d'y ajouter les mois saisis ici.", TITRE, 8);
    }
    return;
  }

  const recu = lireRecus_().find(r => cleFiche_(r.rec) === k && estVide_(r.date) && !r.annule);
  if (recu) {
    ss.toast('Un reçu non imprimé (n° ' + recu.num + ') existe pour cette personne. ' +
      'Pour le modifier, chargez-le depuis le panneau de saisie.', TITRE, 8);
    return;
  }

  const adr = lireAdresses_().find(a => cle_(a.v[0], a.v[1]) === k);
  if (adr) {
    const actuelle = sh.getRange(FICHE.adresse).getValues();
    if (actuelle.every(l => texte_(l[0]) === '')) {
      sh.getRange(FICHE.adresse).setValues(adr.v.slice(2, 6).map(x => [x]));
      ss.toast("Adresse reprise de l'onglet « Adresses ».", TITRE, 4);
    }
  }
}

// Le code église et l'année ne changent plus une fois des reçus numérotés.
function surModifParams_(e, sh) {
  const a1 = e.range.getA1Notation();
  if (a1 !== PARAMS.eglise && a1 !== PARAMS.annee) return;
  if (a1 === PARAMS.eglise && typeof e.value === 'string' && e.value !== e.value.toUpperCase()) {
    e.range.setValue(e.value.toUpperCase());
  }
  const premier = lireRecus_().find(r => /^[A-Z]{4}-\d{2}-\d{3}$/.test(r.num));
  if (!premier) return;
  const eglise = texte_(sh.getRange(PARAMS.eglise).getValue()).toUpperCase();
  const aa = nombre_(sh.getRange(PARAMS.annee).getValue());
  if (premier.num.slice(0, 7) !== eglise + '-' + pad_(aa, 2)) {
    e.range.setValue(e.oldValue === undefined ? '' : e.oldValue);
    e.source.toast('Des reçus sont déjà numérotés (' + premier.num + ') : le code église ' +
      "et l'année ne peuvent plus changer.", TITRE, 8);
  }
}

/* ------------------------------------------------------------------ */
/* Panneau de saisie                                                   */
/* ------------------------------------------------------------------ */

function listePersonnes() {
  const liste = [];
  lireAttente_().forEach(a => {
    if (!aUnNom_(a.rec)) return;
    liste.push({ type: 'attente', id: a.ligne, cle: cleFiche_(a.rec), nom: texte_(a.rec[0]),
      prenom: texte_(a.rec[1]), info: 'Fiche en attente · ' + euros_(totalFiche_(a.rec)) });
  });
  lireRecus_().forEach(r => {
    if (!r.num) return;
    liste.push({ type: 'recu', id: r.num, cle: cleFiche_(r.rec), nom: texte_(r.rec[0]),
      prenom: texte_(r.rec[1]), info: 'Reçu ' + r.num + ' · ' + etatRecu_(r) });
  });
  lireAdresses_().forEach(a => {
    if (!texte_(a.v[0]) && !texte_(a.v[1])) return;
    liste.push({ type: 'adresse', id: a.ligne, cle: cle_(a.v[0], a.v[1]), nom: texte_(a.v[0]),
      prenom: texte_(a.v[1]), info: 'Adresse' + (texte_(a.v[5]) ? ' · ' + texte_(a.v[5]) : '') });
  });
  liste.sort((a, b) => comparer_(a.nom, b.nom) || comparer_(a.prenom, b.prenom));
  return liste;
}

function chargerElement(type, id, cle) {
  return executer_(() => {
    let nom;
    if (type === 'attente') {
      const att = lireAttente_();
      const a = att.find(x => x.ligne === id && cleFiche_(x.rec) === cle) ||
        att.find(x => cleFiche_(x.rec) === cle);
      if (!a) erreur_('Cette fiche en attente est introuvable. Actualisez la liste.');
      afficherFiche_(a.rec, 'Fiche en attente', { type: 'attente', ligne: a.ligne, cle: cleFiche_(a.rec) });
      nom = nomComplet_(a.rec);
    } else if (type === 'recu') {
      const r = lireRecus_().find(x => x.num === id);
      if (!r) erreur_('Le reçu ' + id + ' est introuvable. Actualisez la liste.');
      afficherFiche_(r.rec, origineRecu_(r), { type: 'recu', num: r.num, cle: cleFiche_(r.rec) });
      nom = nomComplet_(r.rec) + ' (reçu ' + r.num + ')';
    } else if (type === 'adresse') {
      const adr = lireAdresses_();
      const a = adr.find(x => x.ligne === id && cle_(x.v[0], x.v[1]) === cle) ||
        adr.find(x => cle_(x.v[0], x.v[1]) === cle);
      if (!a) erreur_('Cette adresse est introuvable. Actualisez la liste.');
      const src = lireSource_();
      // Sur une nouvelle fiche, les montants déjà saisis sont conservés.
      const rec = (src.type === 'attente' || src.type === 'recu') ? ficheVide_() : lireFiche_();
      for (let i = 0; i < 6; i++) rec[i] = a.v[i];
      afficherFiche_(rec, 'Nouvelle fiche (adresse reprise du carnet)', { type: 'adresse', ligne: a.ligne, cle: cle });
      nom = nomComplet_(rec);
    } else {
      erreur_('Élément inconnu.');
    }
    feuille_('fiche').activate();
    return 'Fiche chargée : ' + nom + '.';
  });
}

/* ------------------------------------------------------------------ */
/* Actions sur la fiche                                                */
/* ------------------------------------------------------------------ */

function memoriserFiche() {
  return executer_(() => {
    const p = parametres_();
    let rec = lireFiche_();
    if (!texte_(rec[0]) || !texte_(rec[1])) erreur_('Il manque le nom ou le prénom.');
    verifierFiche_(rec, p);
    const src = lireSource_();
    if (src.type === 'recu') {
      erreur_("Cette fiche vient d'un reçu déjà enregistré (" + src.num + ').\n' +
        'Pour le modifier, utilisez « Enregistrer le reçu ».');
    }
    const k = cleFiche_(rec);
    const att = lireAttente_();
    let cible = null;

    if (src.type === 'attente') {
      cible = att.find(a => a.ligne === src.ligne && cleFiche_(a.rec) === src.cle) ||
        att.find(a => cleFiche_(a.rec) === src.cle) || null;
      if (cible && src.cle !== k && !confirmer_('Cette fiche a été chargée pour « ' +
          nomComplet_(cible.rec) + ' » mais le nom affiché est maintenant « ' + nomComplet_(rec) +
          ' ».\n\nOui : corriger le nom sur la fiche existante.\nNon : ne rien enregistrer.')) {
        return 'Rien n\'a été enregistré.';
      }
    }
    if (!cible) {
      const doublon = att.find(a => cleFiche_(a.rec) === k);
      if (doublon) {
        if (!confirmer_('Une fiche en attente existe déjà pour ' + nomComplet_(rec) + ' (ligne ' +
            doublon.ligne + ').\n\nOui : y ajouter les mois saisis ici (les autres mois de la fiche ' +
            'existante sont conservés).\nNon : ne rien enregistrer.')) {
          return 'Rien n\'a été enregistré.';
        }
        rec = fusionnerFiches_(doublon.rec, rec);
        cible = doublon;
      } else {
        const recu = lireRecus_().find(r => cleFiche_(r.rec) === k && !r.annule);
        if (recu && !confirmer_('Un reçu existe déjà pour ' + nomComplet_(rec) + ' (n° ' + recu.num +
            ').\nMémoriser quand même une nouvelle fiche en attente ?')) {
          return 'Rien n\'a été enregistré.';
        }
      }
    }

    const ligne = cible ? cible.ligne : ATTENTE.ligne1 + att.length;
    ecrireLigne_(feuille_('attente'), ligne, ATTENTE.col1, rec);
    majAdresse_(rec);
    effacerFiche_();
    return notifier_('Fiche de ' + nomComplet_(rec) + ' mémorisée dans « Fiches en attente » (ligne ' +
      ligne + ').');
  });
}

function enregistrerRecuFiche() {
  return executer_(() => {
    const p = parametres_();
    let rec = lireFiche_();
    if (!texte_(rec[0]) || !texte_(rec[1])) erreur_('Il manque le nom ou le prénom.');
    const src = lireSource_();
    const recus = lireRecus_();
    verifierPrefixe_(recus, p);

    if (src.type === 'recu') {
      const r = recus.find(x => x.num === src.num);
      if (!r) erreur_('Le reçu ' + src.num + " n'existe plus dans « Fiches reçus ».");
      if (!estVide_(r.date)) return basculerAnnulation_(r, true);
      verifierFiche_(rec, p);
      if (totalFiche_(rec) <= 0) erreur_('Le total des dons est nul.');
      if (!confirmer_('Vous allez modifier le reçu n° ' + r.num + ' (' + nomComplet_(rec) + ', ' +
          euros_(totalFiche_(rec)) + '). Continuer ?')) {
        return 'Modification abandonnée.';
      }
      ecrireLigne_(feuille_('recus'), r.ligne, 1, ligneRecu_(r.num, rec, '', r.annule));
      majAdresse_(rec);
      effacerFiche_();
      return notifier_('Reçu n° ' + r.num + ' modifié.');
    }

    verifierFiche_(rec, p);
    const k = cleFiche_(rec);
    const att = lireAttente_();
    let retire = null;
    if (src.type === 'attente') {
      retire = att.find(a => a.ligne === src.ligne && cleFiche_(a.rec) === src.cle) ||
        att.find(a => cleFiche_(a.rec) === src.cle) || null;
    }
    if (!retire) {
      const doublon = att.find(a => cleFiche_(a.rec) === k);
      if (doublon) {
        if (!confirmer_('Une fiche en attente existe pour ' + nomComplet_(rec) + ' (ligne ' + doublon.ligne +
            ').\n\nOui : reprendre ses mois dans le reçu et la retirer des fiches en attente.\n' +
            'Non : ne rien enregistrer.')) {
          return 'Enregistrement abandonné.';
        }
        rec = fusionnerFiches_(doublon.rec, rec);
        retire = doublon;
      }
    }
    const total = totalFiche_(rec);
    if (total <= 0) erreur_('Le total des dons est nul : il n\'y a rien à enregistrer.');
    const anonyme = estAnonyme_(rec);
    const existant = anonyme ? null : recus.find(r => cleFiche_(r.rec) === k && !r.annule);
    if (existant && !confirmer_('Un reçu existe déjà pour ' + nomComplet_(rec) + ' (n° ' + existant.num +
        ').\nEn créer un second ?')) {
      return 'Enregistrement abandonné.';
    }
    const num = numerosSuivants_(recus, p, [anonyme])[0];
    if (!confirmer_('Enregistrer le reçu n° ' + num + ' pour ' + nomComplet_(rec) + ' (' + euros_(total) +
        ') ?\n\nLe numéro est définitif.' + (retire ? ' La fiche en attente sera retirée.' : ''))) {
      return 'Enregistrement abandonné.';
    }
    ecrireLigne_(feuille_('recus'), RECUS.ligne1 + recus.length, 1, ligneRecu_(num, rec, '', false));
    if (retire) {
      const restantes = att.filter(a => a !== retire && !ligneVide_(a.rec)).map(a => a.rec);
      ecrireBloc_(feuille_('attente'), ATTENTE.ligne1, ATTENTE.col1, NB_CHAMPS, restantes, att.length);
    }
    majAdresse_(rec);
    effacerFiche_();
    return notifier_('Reçu n° ' + num + ' enregistré pour ' + nomComplet_(rec) + ' (' + euros_(total) + ').');
  });
}

function apercuRecuFiche() {
  return executer_(() => {
    const p = parametres_();
    const rec = lireFiche_();
    if (!texte_(rec[0]) && !texte_(rec[1])) erreur_('La fiche est vide.');
    const src = lireSource_();
    const r = src.type === 'recu' ? lireRecus_().find(x => x.num === src.num) : null;
    const v = valeursRecu_(r ? r.num : '(à attribuer)', normaliserType_(rec),
      r && !estVide_(r.date) ? r.date : '', p);
    v.libelleGauche = 'APERÇU';
    v.libelleDroite = 'APERÇU';
    const modele = feuille_('modele');
    const grille = modele.getRange(1, 1, MODELE.lignes, MODELE.cols).getValues();
    viderChampsModele_(grille);
    placerValeursRecu_(grille, v);
    modele.getRange(1, 1, MODELE.lignes, MODELE.cols).setValues(grille);
    modele.activate();
    return notifier_("Aperçu affiché dans l'onglet « Reçu fiscal ». Rien n'est enregistré.");
  });
}

function effacerFiche() {
  return executer_(() => {
    effacerFiche_();
    feuille_('fiche').activate();
    return notifier_('Fiche effacée.');
  });
}

/* ------------------------------------------------------------------ */
/* Fiches en attente                                                   */
/* ------------------------------------------------------------------ */

function enregistrerLignesSelectionnees() {
  return executer_(() => {
    const p = parametres_();
    const lignes = lignesSelectionnees_('attente');
    const att = lireAttente_();
    const choisies = att.filter(a => lignes.has(a.ligne) && !ligneVide_(a.rec));
    const aEnregistrer = choisies.filter(a => totalFiche_(a.rec) > 0);
    const nulles = choisies.length - aEnregistrer.length;
    if (!aEnregistrer.length) {
      erreur_('Aucune fiche à enregistrer : sélectionnez dans « Fiches en attente » des lignes ' +
        "dont le total n'est pas nul.");
    }
    const problemes = [];
    aEnregistrer.forEach(a => {
      const e = erreursFiche_(a.rec, p.annee);
      if (!texte_(a.rec[0]) || !texte_(a.rec[1])) e.unshift('nom ou prénom manquant');
      if (e.length) problemes.push('Ligne ' + a.ligne + ' (' + nomComplet_(a.rec) + ') : ' + e.join(', '));
    });
    if (problemes.length) erreur_('Corrigez ces lignes avant de recommencer :\n' + problemes.join('\n'));

    const recus = lireRecus_();
    verifierPrefixe_(recus, p);
    const nums = numerosSuivants_(recus, p, aEnregistrer.map(a => estAnonyme_(a.rec)));
    const numerotes = nums.filter(n => !/-000$/.test(n));
    let q = aEnregistrer.length + ' fiche(s) vont devenir des reçus';
    if (numerotes.length) {
      q += ' (n° ' + numerotes[0] + (numerotes.length > 1 ? ' à ' + numerotes[numerotes.length - 1] : '') + ')';
    }
    q += '.\nLes numéros sont définitifs et ces fiches quitteront « Fiches en attente ».';
    if (nulles) q += '\n\n' + nulles + ' ligne(s) sélectionnée(s) ont un total nul et restent en attente.';
    q += '\n\nUn point de retour sera créé avant. Continuer ?';
    if (!confirmer_(q)) return 'Enregistrement abandonné.';

    creerPointDeRetour_();
    const nouvelles = aEnregistrer.map((a, i) => ligneRecu_(nums[i], a.rec, '', false));
    const sh = feuille_('recus');
    const debut = RECUS.ligne1 + recus.length;
    assurerLignes_(sh, debut + nouvelles.length - 1);
    sh.getRange(debut, 1, nouvelles.length, RECUS.nbCols).setValues(nouvelles);
    const restantes = att.filter(a => aEnregistrer.indexOf(a) < 0 && !ligneVide_(a.rec)).map(a => a.rec);
    ecrireBloc_(feuille_('attente'), ATTENTE.ligne1, ATTENTE.col1, NB_CHAMPS, restantes, att.length);
    sh.activate();
    return notifier_(nouvelles.length + ' reçu(s) enregistré(s) dans « Fiches reçus ».');
  });
}

function completerAdressesAttente() {
  return executer_(() => {
    const carnet = carnetAdresses_();
    const att = lireAttente_();
    let n = 0;
    att.forEach(a => {
      const v = carnet.get(cleFiche_(a.rec));
      if (!v) return;
      const avant = a.rec.slice(2, 6).join('|');
      for (let i = 2; i < 6; i++) a.rec[i] = v[i];
      if (a.rec.slice(2, 6).join('|') !== avant) n++;
    });
    if (n) {
      feuille_('attente').getRange(ATTENTE.ligne1, ATTENTE.col1 + 2, att.length, 4)
        .setValues(att.map(a => a.rec.slice(2, 6)));
    }
    return notifier_(n ? n + ' adresse(s) complétée(s) dans « Fiches en attente ».' : 'Aucune adresse à compléter.');
  });
}

function trierAttente() {
  return executer_(() => {
    const att = lireAttente_();
    const lignes = att.map(a => a.rec).filter(r => !ligneVide_(r));
    lignes.sort(comparerFiches_);
    ecrireBloc_(feuille_('attente'), ATTENTE.ligne1, ATTENTE.col1, NB_CHAMPS, lignes, att.length);
    return notifier_('Fiches en attente triées par nom et prénom.');
  });
}

/* ------------------------------------------------------------------ */
/* Fiches reçus                                                        */
/* ------------------------------------------------------------------ */

function genererRecusPdf() {
  return executer_(() => {
    const p = parametres_();
    const lignes = lignesSelectionnees_('recus');
    const choisis = lireRecus_().filter(r => lignes.has(r.ligne) && r.num);
    if (!choisis.length) erreur_('Sélectionnez dans « Fiches reçus » les lignes des reçus à générer.');
    const aujourdhui = aujourdhui_();
    const ecartes = [];
    let liste = [];
    choisis.forEach(r => {
      if (/-000$/.test(r.num)) ecartes.push(r.num + ' : don anonyme, pas de reçu fiscal');
      else if (r.annule) ecartes.push(r.num + ' : reçu annulé');
      else if (donFutur_(r.rec, p.annee, aujourdhui)) ecartes.push(r.num + " : un don est daté après aujourd'hui");
      else liste.push(r);
    });
    const dejaImprimes = liste.filter(r => !estVide_(r.date));
    if (dejaImprimes.length && !confirmer_(dejaImprimes.length + ' reçu(s) sélectionné(s) sont déjà ' +
        'imprimés. Générer leurs duplicatas ?')) {
      liste = liste.filter(r => estVide_(r.date));
    }
    const sansVille = liste.filter(r => !texte_(r.rec[5]));
    if (sansVille.length && !confirmer_(sansVille.length + ' reçu(s) ont une adresse incomplète :\n' +
        sansVille.map(r => r.num + ' ' + nomComplet_(r.rec)).join('\n') + '\n\nLes inclure quand même ?')) {
      liste = liste.filter(r => texte_(r.rec[5]));
    }
    if (!liste.length) erreur_('Aucun reçu à générer.' + (ecartes.length ? '\n\n' + ecartes.join('\n') : ''));
    let reportes = 0;
    if (liste.length > MAX_PDF) {
      reportes = liste.length - MAX_PDF;
      liste = liste.slice(0, MAX_PDF);
    }
    const nouveaux = liste.filter(r => estVide_(r.date)).length;
    let q = liste.length + ' reçu(s) vont être mis dans un PDF.';
    if (nouveaux) {
      q += '\n' + nouveaux + " reçu(s) sont imprimés pour la première fois : ils prendront la date du jour " +
        'et ne seront plus modifiables. Un point de retour sera créé avant.';
    }
    if (ecartes.length) q += '\n\nNon générés :\n' + ecartes.join('\n');
    if (!confirmer_(q + '\n\nContinuer ?')) return 'Génération abandonnée.';

    if (nouveaux) creerPointDeRetour_();
    const fichier = creerPdfRecus_(liste, aujourdhui, p);
    const sh = feuille_('recus');
    liste.forEach(r => {
      if (estVide_(r.date)) sh.getRange(r.ligne, 3).setValue(aujourdhui);
    });
    let texte = liste.length + ' reçu(s) générés dans le PDF « ' + fichier.getName() + ' ».\n' +
      'Ouvrez-le, imprimez-le, puis signez chaque reçu (original à gauche, duplicata à droite).';
    if (reportes) texte += '\n\n' + reportes + ' reçu(s) restent à générer : relancez la commande.';
    afficherLien_('Reçus fiscaux', texte, fichier.getUrl());
    return texte;
  });
}

function annulerRecuSelectionne() {
  return executer_(() => {
    const lignes = lignesSelectionnees_('recus');
    const choisis = lireRecus_().filter(r => lignes.has(r.ligne) && r.num);
    if (choisis.length !== 1) erreur_('Sélectionnez une seule ligne dans « Fiches reçus ».');
    const r = choisis[0];
    if (estVide_(r.date)) {
      erreur_("Le reçu n° " + r.num + " n'est pas encore imprimé : il n'a pas besoin d'être annulé. " +
        'Vous pouvez le modifier depuis la fiche (panneau de saisie).');
    }
    return basculerAnnulation_(r, false);
  });
}

function completerAdressesRecus() {
  return executer_(() => {
    const carnet = carnetAdresses_();
    const sh = feuille_('recus');
    let n = 0;
    lireRecus_().forEach(r => {
      if (!estVide_(r.date)) return;
      const v = carnet.get(cleFiche_(r.rec));
      if (!v || r.rec.slice(2, 6).join('|') === v.slice(2, 6).join('|')) return;
      sh.getRange(r.ligne, 5 + 2, 1, 4).setValues([v.slice(2, 6)]);
      n++;
    });
    return notifier_(n ? n + ' adresse(s) complétée(s) dans « Fiches reçus ».' : 'Aucune adresse à compléter.');
  });
}

/* ------------------------------------------------------------------ */
/* Adresses                                                            */
/* ------------------------------------------------------------------ */

function trierAdresses() {
  return executer_(() => {
    trierAdresses_();
    return notifier_('Adresses triées par nom et prénom.');
  });
}

function trierAdresses_() {
  const adr = lireAdresses_();
  const lignes = adr.map(a => a.v).filter(v => !ligneVide_(v));
  lignes.sort(comparerFiches_);
  ecrireBloc_(feuille_('adresses'), ADRESSES.ligne1, 1, ADRESSES.nbCols, lignes, adr.length);
}

function majAdresse_(rec) {
  if (![2, 3, 4, 5].some(i => texte_(rec[i]) !== '')) return;
  const sh = feuille_('adresses');
  const adr = lireAdresses_();
  const k = cleFiche_(rec);
  const ligne = rec.slice(0, 6);
  const trouve = adr.find(a => cle_(a.v[0], a.v[1]) === k);
  if (trouve) {
    if (trouve.v.join('|') !== ligne.join('|')) ecrireLigne_(sh, trouve.ligne, 1, ligne);
    return;
  }
  ecrireLigne_(sh, ADRESSES.ligne1 + adr.length, 1, ligne);
  trierAdresses_();
}

function carnetAdresses_() {
  const carnet = new Map();
  lireAdresses_().forEach(a => {
    if ([2, 3, 4, 5].some(i => texte_(a.v[i]) !== '')) carnet.set(cle_(a.v[0], a.v[1]), a.v);
  });
  return carnet;
}

/* ------------------------------------------------------------------ */
/* Sauvegardes                                                         */
/* ------------------------------------------------------------------ */

function creerPointDeRetour() {
  return executer_(() => {
    if (!confirmer_('Créer un nouveau point de retour ? Il remplace le précédent.')) return 'Aucun changement.';
    creerPointDeRetour_();
    return notifier_('Point de retour créé.');
  });
}

function creerPointDeRetour_() {
  const ss = classeur_();
  const actif = ss.getActiveSheet();
  Object.keys(RETOUR).forEach(cle => {
    const ancien = ss.getSheetByName(RETOUR[cle]);
    if (ancien) ss.deleteSheet(ancien);
    const copie = feuille_(cle).copyTo(ss).setName(RETOUR[cle]);
    copie.hideSheet();
  });
  feuille_('params').getRange(PARAMS.retour).setValue(maintenant_());
  if (actif) actif.activate();
}

function revenirAuPointDeRetour() {
  return executer_(() => {
    const ss = classeur_();
    const copies = {};
    Object.keys(RETOUR).forEach(cle => {
      copies[cle] = ss.getSheetByName(RETOUR[cle]);
      if (!copies[cle]) erreur_("Aucun point de retour n'a été créé.");
    });
    const quand = texte_(feuille_('params').getRange(PARAMS.retour).getDisplayValue());
    if (!confirmer_('Les onglets « Fiches en attente », « Fiches reçus » et « Adresses » vont être ' +
        'remplacés par leur état du point de retour' + (quand ? ' (' + quand + ')' : '') + '.\n\n' +
        'Tout ce qui a été fait depuis sera perdu. Continuer ?')) {
      return 'Aucun changement.';
    }
    if (!confirmer_('Confirmez-vous le retour en arrière ?')) return 'Aucun changement.';
    const restaurer = (cle, ligne1, col1, nb) => {
      const actuel = lireBloc_(feuille_(cle), ligne1, col1, nb);
      const sauve = lireBloc_(copies[cle], ligne1, col1, nb);
      ecrireBloc_(feuille_(cle), ligne1, col1, nb, sauve, actuel.length);
    };
    restaurer('attente', ATTENTE.ligne1, ATTENTE.col1, NB_CHAMPS);
    restaurer('recus', RECUS.ligne1, 1, RECUS.nbCols);
    restaurer('adresses', ADRESSES.ligne1, 1, ADRESSES.nbCols);
    effacerFiche_();
    return notifier_('Le classeur est revenu au point de retour' + (quand ? ' du ' + quand : '') + '.');
  });
}

function copieDeSauvegarde() {
  return executer_(() => {
    const ss = classeur_();
    const fichier = DriveApp.getFileById(ss.getId());
    const nom = 'Sauvegarde - ' + ss.getName() + ' - ' +
      Utilities.formatDate(new Date(), ss.getSpreadsheetTimeZone(), "yyyy-MM-dd HH'h'mm");
    const parent = dossierParent_(fichier);
    const copie = parent ? fichier.makeCopy(nom, parent) : fichier.makeCopy(nom);
    const texte = 'Copie de sauvegarde créée : « ' + nom + ' ».';
    afficherLien_('Copie de sauvegarde', texte, copie.getUrl());
    return texte;
  });
}

function preparerAnneeSuivante() {
  return executer_(() => {
    const p = parametres_();
    const suivante = p.annee + 1;
    if (!confirmer_("Créer le classeur de l'année " + suivante + ' ?\n\nC\'est une copie de ce classeur ' +
        'qui garde les paramètres et les adresses ; les fiches et les reçus y sont vides. ' +
        "Ce classeur-ci n'est pas modifié.")) {
      return 'Aucun changement.';
    }
    const ss = classeur_();
    const fichier = DriveApp.getFileById(ss.getId());
    const nom = 'Dîmes et reçus fiscaux ' + p.eglise + ' ' + suivante;
    const parent = dossierParent_(fichier);
    const copie = parent ? fichier.makeCopy(nom, parent) : fichier.makeCopy(nom);
    const ss2 = SpreadsheetApp.openById(copie.getId());
    const vider = (cle, ligne1, col1, nb) => {
      const sh = feuille_(cle, ss2);
      const der = sh.getLastRow();
      if (der >= ligne1) sh.getRange(ligne1, col1, der - ligne1 + 1, nb).clearContent();
    };
    vider('attente', ATTENTE.ligne1, ATTENTE.col1, NB_CHAMPS);
    vider('recus', RECUS.ligne1, 1, RECUS.nbCols);
    Object.keys(RETOUR).forEach(cle => {
      const s = ss2.getSheetByName(RETOUR[cle]);
      if (s) ss2.deleteSheet(s);
    });
    const params2 = feuille_('params', ss2);
    params2.getRange(PARAMS.annee).setValue(suivante % 100);
    params2.getRange(PARAMS.retour).setValue('');
    const fiche2 = feuille_('fiche', ss2);
    fiche2.getRange(FICHE.identite).clearContent();
    fiche2.getRange(FICHE.type).setValues([[true], [false]]);
    fiche2.getRange(FICHE.mois).setValues(Array.from({ length: 12 }, () => ['', false, false, false, '']));
    fiche2.getRange(FICHE.origine).setValue('Nouvelle fiche');
    fiche2.getDeveloperMetadata().filter(d => d.getKey() === CLE_SOURCE).forEach(d => d.remove());
    const texte = 'Classeur « ' + nom + ' » créé avec les adresses de cette année.';
    afficherLien_("Classeur de l'année " + suivante, texte, copie.getUrl());
    return texte;
  });
}

/* ------------------------------------------------------------------ */
/* Reçus en PDF                                                        */
/* ------------------------------------------------------------------ */

function creerPdfRecus_(liste, aujourdhui, p) {
  const ss = classeur_();
  const modele = feuille_('modele');
  const plage = modele.getRange(1, 1, MODELE.lignes, MODELE.cols);
  const base = plage.getValues();
  viderChampsModele_(base);
  const tmp = SpreadsheetApp.create('Reçus fiscaux (fichier temporaire)');
  try {
    const feuilleVide = tmp.getSheets()[0];
    liste.forEach(r => {
      const v = valeursRecu_(r.num, r.rec, estVide_(r.date) ? aujourdhui : r.date, p);
      v.libelleGauche = estVide_(r.date) ? '' : 'DUPLICATA';
      const grille = base.map(l => l.slice());
      placerValeursRecu_(grille, v);
      plage.setValues(grille);
      SpreadsheetApp.flush();
      modele.copyTo(tmp).setName(r.num);
    });
    tmp.deleteSheet(feuilleVide);
    SpreadsheetApp.flush();
    const nom = 'Reçus fiscaux ' + p.prefixe + ' (' +
      Utilities.formatDate(new Date(), ss.getSpreadsheetTimeZone(), "yyyy-MM-dd HH'h'mm") + ').pdf';
    const blob = exporterPdf_(tmp.getId()).setName(nom);
    return dossierRecus_(ss, p).createFile(blob);
  } finally {
    plage.setValues(base);
    DriveApp.getFileById(tmp.getId()).setTrashed(true);
  }
}

function exporterPdf_(id) {
  const url = 'https://docs.google.com/spreadsheets/d/' + id + '/export?format=pdf&size=A4' +
    '&portrait=false&scale=4&gridlines=false&printtitle=false&sheetnames=false&pagenum=UNDEFINED' +
    '&fzr=false&horizontal_alignment=CENTER&vertical_alignment=TOP' +
    '&top_margin=0.3&bottom_margin=0.3&left_margin=0.3&right_margin=0.3';
  for (let essai = 1; essai <= 3; essai++) {
    const rep = UrlFetchApp.fetch(url, {
      headers: { Authorization: 'Bearer ' + ScriptApp.getOAuthToken() },
      muteHttpExceptions: true,
    });
    if (rep.getResponseCode() === 200) return rep.getBlob();
    if (essai < 3) Utilities.sleep(3000 * essai);
    else throw new Error('Export PDF impossible (code ' + rep.getResponseCode() + '). Réessayez dans une minute.');
  }
}

function dossierRecus_(ss, p) {
  const nom = 'Reçus fiscaux ' + p.eglise + ' ' + p.annee;
  const parent = dossierParent_(DriveApp.getFileById(ss.getId())) || DriveApp.getRootFolder();
  const existants = parent.getFoldersByName(nom);
  return existants.hasNext() ? existants.next() : parent.createFolder(nom);
}

function dossierParent_(fichier) {
  try {
    const parents = fichier.getParents();
    return parents.hasNext() ? parents.next() : null;
  } catch (e) {
    return null;
  }
}

function valeursRecu_(num, rec, dateRecu, p) {
  const total = totalFiche_(rec);
  const moisDon = [];
  for (let m = 0; m < 12; m++) if (!estVide_(rec[iMontant(m)])) moisDon.push(m);
  let dateDon = '';
  let anneeDon = '';
  if (moisDon.length === 1) {
    const m = moisDon[0];
    const j = nombre_(rec[iJour(m)]);
    dateDon = j > 0 ? pad_(j, 2) + '/' + pad_(m + 1, 2) + '/' + p.annee : MOIS[m] + ' ' + p.annee;
  } else {
    anneeDon = String(p.annee);
  }
  const mode = f => [...Array(12).keys()].some(m => coche_(rec[f(m)])) ? 'X' : '';
  const societe = coche_(rec[7]);
  return {
    numero: num,
    libelleGauche: '',
    libelleDroite: 'DUPLICATA',
    nom: texte_(rec[0]), prenom: texte_(rec[1]),
    adr1: texte_(rec[2]), adr2: texte_(rec[3]), cp: texte_(rec[4]), ville: texte_(rec[5]),
    somme: '*** ' + euros_(total) + ' ***',
    lettres: majuscule_(enLettres_(total)),
    dateDon: dateDon, anneeDon: anneeDon,
    art200: societe ? '' : 'X', art238: societe ? 'X' : '',
    especes: mode(iEspeces), cheque: mode(iCheque), virement: mode(iVirement),
    dateRecu: dateRecu instanceof Date ? formaterDate_(dateRecu) : texte_(dateRecu),
  };
}

function placerValeursRecu_(grille, v) {
  [0, MODELE.decalage].forEach(dec => {
    Object.keys(MODELE.champs).forEach(champ => {
      const [l, c] = MODELE.champs[champ];
      let val = v[champ];
      if (champ === 'libelle') val = dec ? v.libelleDroite : v.libelleGauche;
      grille[l - 1][c - 1 + dec] = val === undefined ? '' : val;
    });
  });
}

function viderChampsModele_(grille) {
  [0, MODELE.decalage].forEach(dec => {
    Object.keys(MODELE.champs).forEach(champ => {
      const [l, c] = MODELE.champs[champ];
      grille[l - 1][c - 1 + dec] = '';
    });
  });
}

function donFutur_(rec, annee, aujourdhui) {
  for (let m = 0; m < 12; m++) {
    if (estVide_(rec[iMontant(m)])) continue;
    const j = nombre_(rec[iJour(m)]);
    const d = new Date(annee, m, j > 0 ? j : 1, 12);
    if (d > aujourdhui) return true;
  }
  return false;
}

function basculerAnnulation_(r, depuisFiche) {
  const q = r.annule
    ? 'Le reçu n° ' + r.num + ' (' + nomComplet_(r.rec) + ') est annulé.\n\nVoulez-vous supprimer l\'annulation ?'
    : (depuisFiche ? 'Ce reçu est déjà imprimé : il ne peut plus être modifié.\n\n' : '') +
      'Voulez-vous annuler le reçu n° ' + r.num + ' (' + nomComplet_(r.rec) + ', ' + euros_(r.montant) +
      ') ?\nConservez l\'original du reçu comme justificatif.';
  if (!confirmer_(q)) return 'Aucun changement.';
  feuille_('recus').getRange(r.ligne, 4).setValue(r.annule ? '' : 'x');
  if (depuisFiche) effacerFiche_();
  return notifier_(r.annule ? "L'annulation du reçu n° " + r.num + ' est supprimée.'
    : 'Le reçu n° ' + r.num + ' est marqué annulé.');
}

/* ------------------------------------------------------------------ */
/* Lecture et écriture des données                                     */
/* ------------------------------------------------------------------ */

function classeur_() {
  return SpreadsheetApp.getActive();
}

function feuille_(cle, ss) {
  ss = ss || classeur_();
  const f = FEUILLES[cle];
  const sh = ss.getSheetByName(f.nom) || ss.getSheets().find(s => s.getSheetId() === f.id);
  if (!sh) {
    erreur_('Onglet « ' + f.nom + ' » introuvable. Ne renommez pas et ne supprimez pas les onglets du classeur.');
  }
  return sh;
}

function parametres_() {
  const sh = feuille_('params');
  const eglise = texte_(sh.getRange(PARAMS.eglise).getValue()).toUpperCase();
  const aa = nombre_(sh.getRange(PARAMS.annee).getValue());
  if (!/^[A-Z]{4}$/.test(eglise) || eglise === 'XXXX') {
    erreur_("Indiquez le code de l'église (4 lettres) dans l'onglet « Paramètres ».");
  }
  if (isNaN(aa) || aa < 0 || aa > 99 || aa !== Math.floor(aa)) {
    erreur_("Indiquez l'année des dons (2 chiffres) dans l'onglet « Paramètres ».");
  }
  return { eglise: eglise, aa: aa, annee: 2000 + aa, prefixe: eglise + '-' + pad_(aa, 2) };
}

function lireBloc_(sh, ligne1, col1, nbCols) {
  const der = sh.getLastRow();
  if (der < ligne1) return [];
  const v = sh.getRange(ligne1, col1, der - ligne1 + 1, nbCols).getValues();
  let n = v.length;
  while (n > 0 && ligneVide_(v[n - 1])) n--;
  return v.slice(0, n);
}

function ecrireBloc_(sh, ligne1, col1, nbCols, lignes, ancienNb) {
  if (Math.max(lignes.length, ancienNb) === 0) return;
  assurerLignes_(sh, ligne1 + Math.max(lignes.length, ancienNb) - 1);
  if (ancienNb > 0) sh.getRange(ligne1, col1, ancienNb, nbCols).clearContent();
  if (lignes.length) sh.getRange(ligne1, col1, lignes.length, nbCols).setValues(lignes);
}

function ecrireLigne_(sh, ligne, col1, valeurs) {
  assurerLignes_(sh, ligne);
  sh.getRange(ligne, col1, 1, valeurs.length).setValues([valeurs]);
}

function assurerLignes_(sh, derniere) {
  const max = sh.getMaxRows();
  if (derniere > max) sh.insertRowsAfter(max, derniere - max + 50);
}

function lireAttente_() {
  return lireBloc_(feuille_('attente'), ATTENTE.ligne1, ATTENTE.col1, NB_CHAMPS)
    .map((rec, i) => ({ ligne: ATTENTE.ligne1 + i, rec: rec }));
}

function lireRecus_() {
  return lireBloc_(feuille_('recus'), RECUS.ligne1, 1, RECUS.nbCols).map((v, i) => ({
    ligne: RECUS.ligne1 + i, num: texte_(v[0]), montant: v[1], date: v[2], annule: coche_(v[3]),
    rec: v.slice(4),
  }));
}

function lireAdresses_() {
  return lireBloc_(feuille_('adresses'), ADRESSES.ligne1, 1, ADRESSES.nbCols)
    .map((v, i) => ({ ligne: ADRESSES.ligne1 + i, v: v }));
}

function lignesSelectionnees_(cle) {
  const ss = classeur_();
  const sh = ss.getActiveSheet();
  const attendu = feuille_(cle);
  if (!sh || sh.getSheetId() !== attendu.getSheetId()) {
    erreur_("Sélectionnez d'abord les lignes concernées dans l'onglet « " + attendu.getName() +
      ' », puis relancez la commande.');
  }
  const lignes = new Set();
  const liste = ss.getActiveRangeList();
  const plages = liste ? liste.getRanges() : [sh.getActiveRange()];
  plages.forEach(r => {
    if (!r) return;
    for (let i = r.getRow(); i < r.getRow() + r.getNumRows(); i++) lignes.add(i);
  });
  return lignes;
}

function lireFiche_() {
  const sh = feuille_('fiche');
  const id = sh.getRange(FICHE.identite).getValues();
  const type = sh.getRange(FICHE.type).getValues();
  const mois = sh.getRange(FICHE.mois).getValues();
  const rec = new Array(NB_CHAMPS).fill('');
  for (let i = 0; i < 6; i++) rec[i] = texte_(id[i][0]);
  rec[6] = type[0][0] === true ? 'x' : '';
  rec[7] = type[1][0] === true ? 'x' : '';
  for (let m = 0; m < 12; m++) {
    const [montant, cheque, especes, virement, jour] = mois[m];
    rec[iMontant(m)] = estVide_(montant) ? '' : montant;
    rec[iJour(m)] = estVide_(jour) ? '' : jour;
    rec[iCheque(m)] = cheque === true ? 'x' : '';
    rec[iEspeces(m)] = especes === true ? 'x' : '';
    rec[iVirement(m)] = virement === true ? 'x' : '';
  }
  return rec;
}

function afficherFiche_(rec, origine, source) {
  const sh = feuille_('fiche');
  sh.getRange(FICHE.identite).setValues(rec.slice(0, 6).map(v => [estVide_(v) ? '' : v]));
  sh.getRange(FICHE.type).setValues([[coche_(rec[6])], [coche_(rec[7])]]);
  const mois = [];
  for (let m = 0; m < 12; m++) {
    mois.push([rec[iMontant(m)], coche_(rec[iCheque(m)]), coche_(rec[iEspeces(m)]),
      coche_(rec[iVirement(m)]), rec[iJour(m)]]);
  }
  sh.getRange(FICHE.mois).setValues(mois);
  sh.getRange(FICHE.origine).setValue(origine);
  ecrireSource_(source);
}

function effacerFiche_() {
  afficherFiche_(ficheVide_(), 'Nouvelle fiche', null);
}

function ficheVide_() {
  const rec = new Array(NB_CHAMPS).fill('');
  rec[6] = 'x';
  return rec;
}

// La fiche affichée se souvient de son origine (fiche en attente, reçu,
// adresse) grâce à une métadonnée invisible de l'onglet « Fiche ».
function lireSource_() {
  const md = feuille_('fiche').getDeveloperMetadata().find(d => d.getKey() === CLE_SOURCE);
  if (!md) return { type: '' };
  try {
    return JSON.parse(md.getValue()) || { type: '' };
  } catch (e) {
    return { type: '' };
  }
}

function ecrireSource_(source) {
  const sh = feuille_('fiche');
  sh.getDeveloperMetadata().filter(d => d.getKey() === CLE_SOURCE).forEach(d => d.remove());
  if (source) sh.addDeveloperMetadata(CLE_SOURCE, JSON.stringify(source));
}

/* ------------------------------------------------------------------ */
/* Règles de gestion                                                   */
/* ------------------------------------------------------------------ */

function verifierFiche_(rec, p) {
  const err = erreursFiche_(rec, p.annee);
  if (err.length) erreur_('Corrigez la fiche :\n' + err.join('\n'));
}

function erreursFiche_(rec, annee) {
  const err = [];
  for (let m = 0; m < 12; m++) {
    const montant = rec[iMontant(m)];
    const jour = rec[iJour(m)];
    if (!estVide_(montant) && isNaN(nombre_(montant))) err.push(MOIS[m] + ' : montant « ' + montant + ' » invalide');
    if (!estVide_(jour)) {
      const j = nombre_(jour);
      const max = new Date(annee, m + 1, 0).getDate();
      if (isNaN(j) || j !== Math.floor(j) || j < 1 || j > max) err.push(MOIS[m] + ' : jour « ' + jour + ' » invalide');
    }
  }
  return err;
}

function totalFiche_(rec) {
  let t = 0;
  for (let m = 0; m < 12; m++) {
    const n = nombre_(rec[iMontant(m)]);
    if (!isNaN(n)) t += n;
  }
  return Math.round(t * 100) / 100;
}

function ligneRecu_(num, rec, date, annule) {
  const r = normaliserType_(rec.slice(0, NB_CHAMPS));
  return [num, totalFiche_(r), date || '', annule ? 'x' : ''].concat(r);
}

// Comme dans le classeur Excel : un donateur qui n'est pas une société est un particulier.
function normaliserType_(rec) {
  const r = rec.slice();
  const societe = coche_(r[7]);
  r[6] = societe ? '' : 'x';
  r[7] = societe ? 'x' : '';
  return r;
}

// Fusion d'une fiche existante et d'une fiche saisie : les mois saisis
// remplacent ceux de la fiche existante, les autres sont conservés.
function fusionnerFiches_(ancienne, saisie) {
  const r = ancienne.slice();
  for (let i = 0; i < 6; i++) if (texte_(saisie[i]) !== '') r[i] = saisie[i];
  r[6] = saisie[6];
  r[7] = saisie[7];
  for (let m = 0; m < 12; m++) {
    if (estVide_(saisie[iMontant(m)])) continue;
    [iMontant, iJour, iCheque, iEspeces, iVirement].forEach(f => { r[f(m)] = saisie[f(m)]; });
  }
  return r;
}

function numerosSuivants_(recus, p, anonymes) {
  let max = 0;
  recus.forEach(r => {
    const m = /-(\d{3})$/.exec(r.num);
    if (m) max = Math.max(max, Number(m[1]));
  });
  return anonymes.map(anonyme => {
    if (anonyme) return p.prefixe + '-000';
    max++;
    if (max > 999) erreur_('Le classeur est limité à 999 reçus par année.');
    return p.prefixe + '-' + pad_(max, 3);
  });
}

function verifierPrefixe_(recus, p) {
  const premier = recus.find(r => /^[A-Z]{4}-\d{2}-\d{3}$/.test(r.num));
  if (premier && premier.num.slice(0, 7) !== p.prefixe) {
    erreur_('Les reçus déjà enregistrés commencent par ' + premier.num.slice(0, 7) + ' alors que les ' +
      "paramètres donnent " + p.prefixe + ". Corrigez l'onglet « Paramètres ».");
  }
}

function origineRecu_(r) {
  return 'Reçu n° ' + r.num + ' : ' + etatRecu_(r) +
    (!r.annule && !estVide_(r.date) ? ' (non modifiable)' : '');
}

function etatRecu_(r) {
  if (r.annule) return 'annulé';
  if (!estVide_(r.date)) return 'imprimé le ' + formaterDate_(r.date);
  return 'non imprimé';
}

function estAnonyme_(rec) {
  return /^anonyme/i.test(texte_(rec[0]));
}

/* ------------------------------------------------------------------ */
/* Montant en toutes lettres                                           */
/* ------------------------------------------------------------------ */

const UNITES = ['zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix',
  'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
const DIZAINES = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante'];

function moinsDeCent_(n) {
  if (n < 20) return UNITES[n];
  const d = Math.floor(n / 10);
  const u = n % 10;
  if (d === 7) return 'soixante' + (u === 1 ? ' et ' : '-') + UNITES[10 + u];
  if (d === 8) return u === 0 ? 'quatre-vingts' : 'quatre-vingt-' + UNITES[u];
  if (d === 9) return 'quatre-vingt-' + UNITES[10 + u];
  return DIZAINES[d] + (u === 0 ? '' : u === 1 ? ' et un' : '-' + UNITES[u]);
}

// final : rien ne suit (« deux cents », « quatre-vingts ») ; devant « mille »
// ces mots restent invariables (« deux cent mille »).
function moinsDeMille_(n, final) {
  const c = Math.floor(n / 100);
  const r = n % 100;
  let s = '';
  if (c > 0) s = (c > 1 ? UNITES[c] + ' ' : '') + 'cent' + (c > 1 && r === 0 && final ? 's' : '');
  if (r > 0) {
    let t = moinsDeCent_(r);
    if (r === 80 && !final) t = 'quatre-vingt';
    s += (s ? ' ' : '') + t;
  }
  return s;
}

function entierEnLettres_(n) {
  if (n === 0) return 'zéro';
  const millions = Math.floor(n / 1e6);
  const milliers = Math.floor((n % 1e6) / 1000);
  const reste = n % 1000;
  const parts = [];
  if (millions) parts.push(moinsDeMille_(millions, true) + ' million' + (millions > 1 ? 's' : ''));
  if (milliers) parts.push(milliers === 1 ? 'mille' : moinsDeMille_(milliers, false) + ' mille');
  if (reste) parts.push(moinsDeMille_(reste, true));
  return parts.join(' ');
}

function enLettres_(montant) {
  const centimes = Math.round(nombre_(montant) * 100);
  const euros = Math.floor(centimes / 100);
  const cts = centimes % 100;
  let s = entierEnLettres_(euros);
  if (euros >= 1e6 && euros % 1e6 === 0) s += " d'euros";
  else s += euros >= 2 ? ' euros' : ' euro';
  if (cts) s += ' et ' + entierEnLettres_(cts) + ' centime' + (cts > 1 ? 's' : '');
  return s;
}

/* ------------------------------------------------------------------ */
/* Outils                                                              */
/* ------------------------------------------------------------------ */

function executer_(fn) {
  const verrou = LockService.getDocumentLock();
  if (!verrou.tryLock(20000)) {
    alerte_('Une autre opération est en cours sur ce classeur. Réessayez dans un instant.');
    return 'Opération en cours, réessayez.';
  }
  try {
    return fn();
  } catch (e) {
    if (e && e.utilisateur) {
      alerte_(e.message);
      return e.message;
    }
    throw e;
  } finally {
    verrou.releaseLock();
  }
}

function erreur_(message) {
  const e = new Error(message);
  e.utilisateur = true;
  throw e;
}

function alerte_(message) {
  const ui = SpreadsheetApp.getUi();
  ui.alert(TITRE, message, ui.ButtonSet.OK);
}

function confirmer_(question) {
  const ui = SpreadsheetApp.getUi();
  return ui.alert(TITRE, question, ui.ButtonSet.YES_NO) === ui.Button.YES;
}

function notifier_(message) {
  classeur_().toast(message, TITRE, 6);
  return message;
}

function afficherLien_(titre, texte, url) {
  const html = HtmlService.createHtmlOutput(
    '<div style="font-family:Arial,sans-serif;font-size:14px;line-height:1.5">' +
    '<p>' + echapper_(texte).replace(/\n/g, '<br>') + '</p>' +
    '<p><a href="' + echapper_(url) + '" target="_blank">Ouvrir le fichier</a></p></div>')
    .setWidth(460).setHeight(230);
  SpreadsheetApp.getUi().showModalDialog(html, titre);
}

function echapper_(s) {
  return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function texte_(v) {
  if (v === null || v === undefined) return '';
  if (v instanceof Date) return formaterDate_(v);
  return String(v).trim();
}

function estVide_(v) {
  return v === '' || v === null || v === undefined || (typeof v === 'string' && v.trim() === '');
}

function ligneVide_(ligne) {
  return ligne.every(estVide_);
}

function coche_(v) {
  return v === true || texte_(v).toLowerCase() === 'x';
}

function nombre_(v) {
  if (typeof v === 'number') return v;
  if (estVide_(v)) return 0;
  if (v instanceof Date || typeof v === 'boolean') return NaN;
  const n = Number(String(v).replace(/[\s €]/g, '').replace(',', '.'));
  return isNaN(n) ? NaN : n;
}

function aUnNom_(rec) {
  return texte_(rec[0]) !== '' || texte_(rec[1]) !== '';
}

function sansAccents_(s) {
  return s.normalize('NFD').replace(/[̀-ͯ]/g, '');
}

function cle_(nom, prenom) {
  return sansAccents_((texte_(nom) + '|' + texte_(prenom)).toLowerCase()).replace(/\s+/g, ' ');
}

function cleFiche_(rec) {
  return cle_(rec[0], rec[1]);
}

function nomComplet_(rec) {
  return (texte_(rec[0]) + ' ' + texte_(rec[1])).trim();
}

function comparer_(a, b) {
  const x = sansAccents_(texte_(a).toLowerCase()) || '￿';
  const y = sansAccents_(texte_(b).toLowerCase()) || '￿';
  return x < y ? -1 : x > y ? 1 : 0;
}

function comparerFiches_(a, b) {
  return comparer_(a[0], b[0]) || comparer_(a[1], b[1]);
}

function pad_(n, l) {
  return String(n).padStart(l, '0');
}

function majuscule_(s) {
  return s.charAt(0).toUpperCase() + s.slice(1);
}

function euros_(n) {
  n = nombre_(n);
  if (isNaN(n)) n = 0;
  const [e, c] = Math.abs(n).toFixed(2).split('.');
  return (n < 0 ? '-' : '') + e.replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ',' + c + ' €';
}

function fuseau_() {
  try {
    return classeur_().getSpreadsheetTimeZone();
  } catch (e) {
    return Session.getScriptTimeZone();
  }
}

function formaterDate_(d) {
  return Utilities.formatDate(d, fuseau_(), 'dd/MM/yyyy');
}

function maintenant_() {
  return Utilities.formatDate(new Date(), fuseau_(), "dd/MM/yyyy 'à' HH'h'mm");
}

// Date du jour dans le fuseau du classeur, fixée à midi pour éviter tout
// décalage d'un jour entre fuseaux.
function aujourdhui_() {
  const [a, m, j] = Utilities.formatDate(new Date(), fuseau_(), 'yyyy-MM-dd').split('-').map(Number);
  return new Date(a, m - 1, j, 12);
}

/* ------------------------------------------------------------------ */
/* Panneau latéral                                                     */
/* ------------------------------------------------------------------ */

const PANNEAU_HTML = `<!DOCTYPE html>
<html><head><base target="_top"><style>
body{font-family:Arial,sans-serif;font-size:13px;margin:12px;color:#202124}
h2{font-size:14px;margin:14px 0 6px}
h2:first-child{margin-top:0}
input{width:100%;box-sizing:border-box;padding:7px;font-size:13px;border:1px solid #bbb;border-radius:4px}
#res{max-height:280px;overflow-y:auto;margin:6px 0 4px;border:1px solid #e0e0e0;border-radius:4px}
.it{padding:6px 8px;border-bottom:1px solid #eee;cursor:pointer}
.it:last-child{border-bottom:none}
.it:hover{background:#e8f0fe}
.it small{display:block;color:#5f6368;margin-top:2px}
.lien{background:none;border:none;color:#1a73e8;padding:2px 0;width:auto;font-size:12px;cursor:pointer}
button.b{display:block;width:100%;margin:5px 0;padding:8px;font-size:13px;border:1px solid #1a73e8;
background:#fff;color:#1a73e8;border-radius:4px;cursor:pointer}
button.p{background:#1a73e8;color:#fff}
button:disabled{opacity:.5;cursor:default}
#msg{margin-top:12px;padding:8px;background:#f1f3f4;border-radius:4px;white-space:pre-wrap;display:none}
</style></head><body>
<h2>Rechercher une personne</h2>
<input id="q" placeholder="Nom ou prénom" autocomplete="off">
<div id="res"></div>
<button class="lien" onclick="charger()">Actualiser la liste</button>
<h2>Fiche affichée</h2>
<button class="b p" onclick="act('memoriserFiche')">Mémoriser (fiche en attente)</button>
<button class="b" onclick="act('enregistrerRecuFiche')">Enregistrer le reçu</button>
<button class="b" onclick="act('apercuRecuFiche')">Aperçu du reçu</button>
<button class="b" onclick="act('effacerFiche')">Effacer la fiche</button>
<div id="msg"></div>
<script>
var liste = [];
function norm(s){return String(s||'').toLowerCase().normalize('NFD').replace(/[\\u0300-\\u036f]/g,'');}
function charger(){
  google.script.run.withSuccessHandler(function(l){liste=l||[];filtrer();}).withFailureHandler(err).listePersonnes();
}
function filtrer(){
  var q=norm(document.getElementById('q').value).trim();
  var res=document.getElementById('res');
  res.innerHTML='';
  var t=liste.filter(function(p){
    return !q||norm(p.nom+' '+p.prenom).indexOf(q)>=0||norm(p.prenom+' '+p.nom).indexOf(q)>=0;
  }).slice(0,80);
  if(!t.length){res.innerHTML='<div class="it"><small>Aucun résultat</small></div>';return;}
  t.forEach(function(p){
    var d=document.createElement('div');d.className='it';
    var b=document.createElement('b');b.textContent=(p.nom+' '+p.prenom).trim();
    var s=document.createElement('small');s.textContent=p.info;
    d.appendChild(b);d.appendChild(s);
    d.onclick=function(){ouvrir(p);};
    res.appendChild(d);
  });
}
function ouvrir(p){msg('Chargement…');occupe(true);
  google.script.run.withSuccessHandler(fin).withFailureHandler(err).chargerElement(p.type,p.id,p.cle);}
function act(f){msg('En cours…');occupe(true);
  google.script.run.withSuccessHandler(fin).withFailureHandler(err)[f]();}
function fin(m){occupe(false);msg(m||'');charger();}
function err(e){occupe(false);msg('Erreur : '+(e&&e.message?e.message:e));}
function msg(t){var m=document.getElementById('msg');m.textContent=t;m.style.display=t?'block':'none';}
function occupe(b){Array.prototype.forEach.call(document.querySelectorAll('button'),function(x){x.disabled=b;});}
document.getElementById('q').addEventListener('input',filtrer);
charger();
</script></body></html>`;
