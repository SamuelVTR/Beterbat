// Scénarios de bout en bout du script, sur un classeur simulé.
// Lancer : node --test outils/dimes-google-sheets/tests/
'use strict';

const test = require('node:test');
const assert = require('node:assert/strict');
const { creerEnvironnement, registre, estDate } = require('./mock-apps-script');

function nouveauClasseur(env) {
  const ss = new env.Classeur('Dîmes et reçus fiscaux');
  const ajout = (nom, id, lignes, cols) => { const f = ss.insertSheet(nom, id); f.maxRows = lignes; f.maxCols = cols; return f; };
  ajout("Mode d'emploi", 0, 100, 4);
  const params = ajout('Paramètres', 1, 20, 4);
  ajout('Adresses', 2, 1000, 6).getRange('A1:F1').setValues([['Nom', 'Prénom', 'Adresse 1', 'Adresse 2', 'Code postal', 'Ville']]);
  const fiche = ajout('Fiche', 3, 30, 8);
  ajout('Fiches en attente', 4, 1000, 69);
  ajout('Fiches reçus', 5, 1000, 72);
  ajout('Récapitulatif', 6, 1000, 18);
  const modele = ajout('Reçu fiscal', 7, 42, 19);
  modele.getRange('A1').setValue('Reçu au titre des dons');
  modele.getRange('A17').setValue('X');
  params.getRange('B3').setValue('ANDU');
  params.getRange('B4').setValue(26);
  env.etat.classeur = ss;
  ss.active = fiche;
  env.ctx.effacerFiche_();
  return ss;
}

function saisir(fiche, identite, mois) {
  fiche.getRange('B4:B9').setValues(identite.map(v => [v]));
  for (const [m, montant, mode, jour] of mois) {
    fiche.getRange(12 + m, 2, 1, 5).setValues([[montant, mode === 'cheque', mode === 'especes', mode === 'virement', jour]]);
  }
}

// Simule une saisie au clavier dans l'onglet (déclenche onEdit).
function taper(env, feuille, a1, valeur) {
  const plage = feuille.getRange(a1);
  const ancienne = plage.getValue();
  plage.setValue(valeur);
  env.ctx.onEdit({
    range: plage, value: typeof valeur === 'boolean' ? String(valeur).toUpperCase() : String(valeur),
    oldValue: ancienne === '' ? undefined : String(ancienne), source: env.etat.classeur,
  });
}

const ligne = (f, r, c, n) => f.getRange(r, c, 1, n).getValues()[0];

test('montant en toutes lettres', () => {
  const env = creerEnvironnement();
  const l = n => env.ctx.enLettres_(n);
  assert.equal(l(0), 'zéro euro');
  assert.equal(l(1), 'un euro');
  assert.equal(l(2), 'deux euros');
  assert.equal(l(21), 'vingt et un euros');
  assert.equal(l(71), 'soixante et onze euros');
  assert.equal(l(77), 'soixante-dix-sept euros');
  assert.equal(l(80), 'quatre-vingts euros');
  assert.equal(l(81), 'quatre-vingt-un euros');
  assert.equal(l(91), 'quatre-vingt-onze euros');
  assert.equal(l(99), 'quatre-vingt-dix-neuf euros');
  assert.equal(l(100), 'cent euros');
  assert.equal(l(200), 'deux cents euros');
  assert.equal(l(201), 'deux cent un euros');
  assert.equal(l(280), 'deux cent quatre-vingts euros');
  assert.equal(l(1000), 'mille euros');
  assert.equal(l(1001), 'mille un euros');
  assert.equal(l(2000), 'deux mille euros');
  assert.equal(l(21000), 'vingt et un mille euros');
  assert.equal(l(80000), 'quatre-vingt mille euros');
  assert.equal(l(200000), 'deux cent mille euros');
  assert.equal(l(350.5), 'trois cent cinquante euros et cinquante centimes');
  assert.equal(l(1250.75), 'mille deux cent cinquante euros et soixante-quinze centimes');
  assert.equal(l(1.01), 'un euro et un centime');
  assert.equal(l(1000000), "un million d'euros");
  assert.equal(l(2300000), 'deux millions trois cent mille euros');
  assert.equal(env.ctx.euros_(1250.5), '1 250,50 €');
});

test('parcours complet : saisie, fiches en attente, reçus, PDF, annulation, retour', () => {
  const env = creerEnvironnement();
  const ss = nouveauClasseur(env);
  const fiche = ss.getSheetByName('Fiche');
  const attente = ss.getSheetByName('Fiches en attente');
  const recus = ss.getSheetByName('Fiches reçus');
  const adresses = ss.getSheetByName('Adresses');

  // 1. Première saisie : janvier, chèque.
  saisir(fiche, ['Dupont', 'Jean', '1 rue des Lilas', '', '06000', 'Nice'], [[0, 50, 'cheque', 5]]);
  env.ctx.memoriserFiche();
  const r3 = ligne(attente, 3, 2, 68);
  assert.deepEqual(r3.slice(0, 8), ['Dupont', 'Jean', '1 rue des Lilas', '', '06000', 'Nice', 'x', '']);
  assert.deepEqual(r3.slice(8, 13), [50, 5, 'x', '', '']);
  assert.deepEqual(ligne(adresses, 2, 1, 6), ['Dupont', 'Jean', '1 rue des Lilas', '', '06000', 'Nice']);
  assert.equal(fiche.getRange('B4').getValue(), '', 'la fiche est effacée après mémorisation');

  // 2. Le mois suivant : en tapant nom et prénom, la fiche en attente se recharge.
  taper(env, fiche, 'B4', 'DUPONT');
  taper(env, fiche, 'B5', 'jean');
  assert.equal(fiche.getRange('B6').getValue(), '1 rue des Lilas');
  assert.equal(fiche.getRange('B12').getValue(), 50);
  fiche.getRange('B13').setValue(60);
  taper(env, fiche, 'E13', true);           // virement
  taper(env, fiche, 'C13', true);           // puis chèque : le virement se décoche
  assert.deepEqual(ligne(fiche, 13, 3, 3), [true, false, false]);
  taper(env, fiche, 'E13', true);
  fiche.getRange('F13').setValue(3);
  env.ctx.memoriserFiche();
  assert.equal(attente.getLastRow(), 3, 'pas de doublon');
  assert.deepEqual(ligne(attente, 3, 2, 68).slice(13, 18), [60, 3, '', '', 'x']);

  // 3. Saisie sans rechargement (montants tapés avant le nom) : fusion proposée.
  saisir(fiche, ['Dupont', 'Jean', '', '', '', ''], [[2, 70, 'especes', 8]]);
  env.etat.reponses.push(true);
  env.ctx.memoriserFiche();
  assert.equal(attente.getLastRow(), 3);
  const fusion = ligne(attente, 3, 2, 68);
  assert.deepEqual([fusion[8], fusion[13], fusion[18]], [50, 60, 70]);
  assert.equal(fusion[2], '1 rue des Lilas', "l'adresse existante est conservée");

  // 4. Autres donateurs.
  saisir(fiche, ['Martin', 'Marie', '', '', '', ''], [[3, 100, 'virement', 2]]);
  env.ctx.memoriserFiche();
  saisir(fiche, ['Anonyme', '1', '', '', '', ''], [[3, 20, 'especes', '']]);
  env.ctx.memoriserFiche();
  saisir(fiche, ['Zola', 'Emile', '', '', '', ''], [[4, 0, '', '']]);
  env.ctx.memoriserFiche();
  assert.equal(attente.getLastRow(), 6);

  // 5. Erreur de saisie : 31 février.
  saisir(fiche, ['Petit', 'Paul', '', '', '', ''], [[1, 10, 'cheque', 31]]);
  env.etat.alertes.length = 0;
  env.ctx.memoriserFiche();
  assert.match(env.etat.alertes[0], /février : jour « 31 » invalide/);
  env.ctx.effacerFiche_();

  // 6. Enregistrement des lignes sélectionnées (Zola, total nul, reste en attente).
  ss.selectionner(attente, 'A3:BQ6');
  env.etat.reponses.push(true);
  env.ctx.enregistrerLignesSelectionnees();
  assert.deepEqual(ligne(recus, 3, 1, 6), ['ANDU-26-001', 180, '', '', 'Dupont', 'Jean']);
  assert.deepEqual(ligne(recus, 4, 1, 6), ['ANDU-26-002', 100, '', '', 'Martin', 'Marie']);
  assert.deepEqual(ligne(recus, 5, 1, 6), ['ANDU-26-000', 20, '', '', 'Anonyme', '1']);
  assert.equal(attente.getLastRow(), 3);
  assert.equal(attente.getRange('B3').getValue(), 'Zola');
  assert.ok(ss.getSheetByName('Retour - Fiches reçus'), 'point de retour créé');

  // 7. Modification d'un reçu non imprimé depuis la fiche.
  env.ctx.chargerElement('recu', 'ANDU-26-001', 'dupont|jean');
  assert.match(fiche.getRange('B10').getValue(), /non imprimé/);
  fiche.getRange('B12').setValue(55);
  env.etat.reponses.push(true);
  env.ctx.enregistrerRecuFiche();
  assert.equal(recus.getRange('B3').getValue(), 185);

  // 8. Un reçu saisi directement depuis la fiche.
  saisir(fiche, ['Durand', 'Paul', '3 av. Foch', '', '13001', 'Marseille'], [[11, 40, 'cheque', 20]]);
  env.etat.reponses.push(true);
  env.ctx.enregistrerRecuFiche();
  assert.equal(recus.getRange('A6').getValue(), 'ANDU-26-003');

  // 9. PDF : l'anonyme et le don daté dans le futur sont écartés.
  env.ctx.aujourdhui_ = () => new (env.eval('Date'))(2026, 9, 27, 12);
  const modele = ss.getSheetByName('Reçu fiscal');
  ss.selectionner(recus, 'A3:A6');
  env.etat.reponses.push(true);       // inclure les adresses incomplètes (Martin)
  env.etat.reponses.push(true);       // confirmer la génération
  env.ctx.genererRecusPdf();
  assert.equal(env.etat.pdfs.length, 1);
  const tmp = env.etat.exports[0];
  assert.deepEqual(tmp.getSheets().map(f => f.getName()), ['ANDU-26-001', 'ANDU-26-002']);
  const p1 = tmp.getSheetByName('ANDU-26-001');
  assert.equal(p1.getRange('G2').getValue(), 'ANDU-26-001');
  assert.equal(p1.getRange('Q2').getValue(), 'ANDU-26-001');
  assert.equal(p1.getRange('A4').getValue(), '', "l'original n'a pas de mention");
  assert.equal(p1.getRange('K4').getValue(), 'DUPLICATA');
  assert.equal(p1.getRange('A22').getValue(), 'Dupont');
  assert.equal(p1.getRange('C26').getValue(), '06000');
  assert.equal(p1.getRange('C29').getValue(), '*** 185,00 € ***');
  assert.equal(p1.getRange('C30').getValue(), 'Cent quatre-vingt-cinq euros');
  assert.equal(p1.getRange('C31').getValue(), '', 'plusieurs mois : pas de date unique');
  assert.equal(p1.getRange('G31').getValue(), '2026');
  assert.deepEqual([p1.getRange('A39').getValue(), p1.getRange('C39').getValue(), p1.getRange('E39').getValue()], ['X', 'X', 'X']);
  assert.equal(p1.getRange('A33').getValue(), 'X');
  assert.equal(p1.getRange('A17').getValue(), 'X', 'les cases fixes du modèle sont gardées');
  const p2 = tmp.getSheetByName('ANDU-26-002');
  assert.equal(p2.getRange('C31').getValue(), '02/04/2026', 'un seul mois : date exacte');
  assert.equal(p2.getRange('E39').getValue(), 'X');
  assert.ok(tmp.trashed, 'le classeur temporaire est supprimé');
  assert.ok(estDate(recus.getRange('C3').getValue()), 'date d\'impression posée');
  assert.ok(estDate(recus.getRange('C4').getValue()));
  assert.equal(recus.getRange('C5').getValue(), '', 'anonyme jamais imprimé');
  assert.equal(recus.getRange('C6').getValue(), '', 'don futur non imprimé');
  assert.equal(modele.getRange('G2').getValue(), '', 'le modèle est remis à vide');

  // 10. Duplicata.
  ss.selectionner(recus, 'B3');
  env.etat.reponses.push(true, true);
  env.ctx.genererRecusPdf();
  const dup = env.etat.exports[1].getSheetByName('ANDU-26-001');
  assert.equal(dup.getRange('A4').getValue(), 'DUPLICATA');

  // 11. Un reçu imprimé ne se modifie plus : la fiche propose de l'annuler.
  env.ctx.chargerElement('recu', 'ANDU-26-001', 'dupont|jean');
  env.etat.reponses.push(true);
  env.ctx.enregistrerRecuFiche();
  assert.equal(recus.getRange('D3').getValue(), 'x');
  ss.selectionner(recus, 'A3');
  env.etat.reponses.push(true);
  env.ctx.annulerRecuSelectionne();
  assert.equal(recus.getRange('D3').getValue(), '');

  // 12. Les paramètres ne changent plus une fois des reçus numérotés.
  const params = ss.getSheetByName('Paramètres');
  taper(env, params, 'B4', 27);
  assert.equal(params.getRange('B4').getValue(), '26');

  // 13. Retour au point de retour (avant l'impression).
  env.etat.reponses.push(true, true);
  env.ctx.revenirAuPointDeRetour();
  assert.equal(recus.getRange('C3').getValue(), '', 'la date d\'impression a disparu');
  assert.equal(recus.getRange('B3').getValue(), 185);
});

test('tri, adresses et année suivante', () => {
  const env = creerEnvironnement();
  const ss = nouveauClasseur(env);
  const fiche = ss.getSheetByName('Fiche');
  const attente = ss.getSheetByName('Fiches en attente');
  const adresses = ss.getSheetByName('Adresses');
  for (const [nom, prenom] of [['Zola', 'Emile'], ['Élise', 'Anne'], ['Berger', 'Luc']]) {
    saisir(fiche, [nom, prenom, '', '', '', ''], [[0, 10, 'cheque', 1]]);
    env.ctx.memoriserFiche();
  }
  env.ctx.trierAttente();
  assert.deepEqual([2, 3, 4].map(i => attente.getRange(i + 1, 2).getValue()), ['Berger', 'Élise', 'Zola']);

  adresses.getRange('A2:F2').setValues([['Zola', 'Emile', '5 rue Hugo', '', '75005', 'Paris']]);
  env.ctx.completerAdressesAttente();
  assert.equal(attente.getRange('G5').getValue(), 'Paris');

  saisir(fiche, ['Abel', 'Jo', '2 rue A', '', '69001', 'Lyon'], [[0, 5, 'cheque', 1]]);
  env.ctx.memoriserFiche();
  assert.deepEqual([2, 3].map(i => adresses.getRange(i, 1).getValue()), ['Abel', 'Zola'], 'carnet trié');

  env.etat.reponses.push(true);
  env.ctx.preparerAnneeSuivante();
  const suivant = [...registre.values()].find(c => c.getName() === 'Dîmes et reçus fiscaux ANDU 2027');
  assert.ok(suivant, 'classeur 2027 créé');
  assert.equal(suivant.getSheetByName('Paramètres').getRange('B4').getValue(), 27);
  assert.equal(suivant.getSheetByName('Fiches en attente').getLastRow(), 0, 'fiches vidées');
  assert.equal(suivant.getSheetByName('Adresses').getRange('A2').getValue(), 'Abel', 'adresses gardées');
  assert.equal(ss.getSheetByName('Paramètres').getRange('B4').getValue(), 26, 'classeur actuel inchangé');
});

test('panneau, aperçu et cas particuliers de la fiche', () => {
  const env = creerEnvironnement();
  const ss = nouveauClasseur(env);
  const fiche = ss.getSheetByName('Fiche');
  const attente = ss.getSheetByName('Fiches en attente');
  const recus = ss.getSheetByName('Fiches reçus');
  const adresses = ss.getSheetByName('Adresses');
  adresses.getRange('A2:F2').setValues([['Roux', 'Léa', '8 rue Vert', '', '30000', 'Nîmes']]);

  // Choisir une adresse dans le panneau garde les montants déjà tapés.
  fiche.getRange('B14').setValue(25);
  env.ctx.chargerElement('adresse', 2, 'roux|lea');
  assert.equal(fiche.getRange('B9').getValue(), 'Nîmes');
  assert.equal(fiche.getRange('B14').getValue(), 25);
  env.ctx.memoriserFiche();

  // Saisie du nom avec des montants déjà tapés : pas de rechargement, un message.
  fiche.getRange('B15').setValue(30);
  taper(env, fiche, 'B4', 'Roux');
  taper(env, fiche, 'B5', 'Lea');
  assert.equal(fiche.getRange('B14').getValue(), '', 'la fiche existante n\'a pas écrasé la saisie');
  assert.match(ss.toasts.at(-1), /existe déjà/);

  // Enregistrer le reçu depuis cette fiche reprend les mois de la fiche en attente.
  env.etat.reponses.push(true, true);
  env.ctx.enregistrerRecuFiche();
  assert.deepEqual(ligne(recus, 3, 1, 2), ['ANDU-26-001', 55]);
  assert.equal(attente.getLastRow(), 0, 'la fiche en attente est retirée');

  // Une fiche issue d'un reçu ne se mémorise pas en fiche en attente.
  env.ctx.chargerElement('recu', 'ANDU-26-001', 'roux|lea');
  env.etat.alertes.length = 0;
  env.ctx.memoriserFiche();
  assert.match(env.etat.alertes[0], /Enregistrer le reçu/);

  // Aperçu : modèle rempli avec la mention APERÇU.
  env.ctx.apercuRecuFiche();
  const modele = ss.getSheetByName('Reçu fiscal');
  assert.equal(modele.getRange('A4').getValue(), 'APERÇU');
  assert.equal(modele.getRange('G2').getValue(), 'ANDU-26-001');
  assert.equal(modele.getRange('C30').getValue(), 'Cinquante-cinq euros');

  // Correction du nom d'une fiche en attente : question posée, refus = rien ne change.
  env.ctx.effacerFiche_();
  env.ctx.chargerElement('adresse', 2, 'roux|lea');
  saisir(fiche, ['Blanc', 'Max', '', '', '', ''], [[0, 10, 'cheque', 1]]);
  env.ctx.memoriserFiche();
  env.ctx.chargerElement('attente', 3, 'blanc|max');
  fiche.getRange('B4').setValue('Leblanc');
  env.etat.reponses.push(false);
  env.ctx.memoriserFiche();
  assert.equal(attente.getRange('B3').getValue(), 'Blanc');
  env.etat.reponses.push(true);
  env.ctx.memoriserFiche();
  assert.equal(attente.getRange('B3').getValue(), 'Leblanc');
  assert.equal(attente.getLastRow(), 3);

  // Particulier / Société : une seule case cochée.
  taper(env, fiche, 'G5', true);
  assert.deepEqual([fiche.getRange('G4').getValue(), fiche.getRange('G5').getValue()], [false, true]);

  // Liste du panneau.
  const liste = env.ctx.listePersonnes();
  // Leblanc n'a pas d'adresse saisie : il n'entre pas dans le carnet.
  assert.deepEqual(Array.from(liste, p => p.type + ':' + p.nom), ['attente:Leblanc', 'recu:Roux', 'adresse:Roux']);
});
