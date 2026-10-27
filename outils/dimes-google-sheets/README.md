# Tableau des dîmes et reçus fiscaux (Google Sheets)

Reprise en Google Sheets du classeur Excel « V_5. Tableau dîmes et RF » (macros VBA)
utilisé par les trésoriers d'église de la Fédération France Sud : saisie des dîmes
par donateur et par mois, fiches en attente, numérotation des reçus fiscaux,
reçus en PDF (original + duplicata), annulation, récapitulatif pour les
commissaires aux comptes.

## Contenu

- `Code.gs` : le script Apps Script à coller dans le Google Sheet
  (menu « Dîmes », panneau de saisie, onEdit, génération des PDF).
- `tests/` : simulation des services Apps Script et scénarios de bout en bout
  (`node --test outils/dimes-google-sheets/tests/simulation.test.js`).

## Installation dans le Google Sheet

1. Ouvrir le classeur « Dîmes et reçus fiscaux » dans Google Drive.
2. Menu Extensions > Apps Script.
3. Remplacer tout le contenu de `Code.gs` par celui de ce dossier, puis Enregistrer.
4. Recharger le classeur : le menu « Dîmes » apparaît.
5. Au premier clic, autoriser le script (Paramètres avancés > Accéder au projet).

Le script demande l'accès au classeur, à Drive (dossier des PDF, copies de
sauvegarde) et aux requêtes externes (export PDF du classeur).

## Correspondance avec le classeur Excel

| Excel (VBA) | Google Sheets |
|---|---|
| Boutons de la feuille « Fiche » | Menu Dîmes > Fiche, et panneau de saisie |
| Formulaires « Fiches en attente », « Fiches reçus », « Fichier adresses » | Recherche unique dans le panneau de saisie |
| Clic dans chèque / espèces / virement | Cases à cocher (une seule par mois) |
| Bouton « Enregistrer » (fiches en attente) | Dîmes > Fiches en attente > Enregistrer les lignes sélectionnées |
| Bouton « Imprimer » (fiches reçus) | Dîmes > Fiches reçus > Générer le PDF (original + duplicata) |
| Double-clic annulation (récapitulatif) | Dîmes > Fiches reçus > Annuler ou rétablir le reçu sélectionné |
| MEMO / RETOUR | Dîmes > Sauvegardes > point de retour |
| SAUVE / RESTAURE / Import | Copie de sauvegarde Drive, historique des versions, classeur de l'année suivante |
| Feuille « Recapitulatif » (macro) | Onglet « Récapitulatif » en formules |

Corrections par rapport au classeur Excel : le bouton « Adresses » traite toutes
les lignes (l'Excel s'arrêtait vers la 100e), le tri se fait toujours par nom et
prénom, une saisie pour une personne qui a déjà une fiche en attente est fusionnée
au lieu de créer un doublon, et un point de retour est créé avant chaque
opération définitive (numérotation, première impression).
