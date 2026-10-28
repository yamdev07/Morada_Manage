# Photos du site Morada Lodge

Déposez vos photos **dans ce dossier**, avec exactement les noms ci-dessous.
Aucune autre manipulation n'est nécessaire : dès qu'un fichier est présent, il
remplace automatiquement la plaque « Photographie à venir » sur le site.

Si une photo manque, le site reste propre — il n'affiche jamais d'image cassée.

---

## Les indispensables (par ordre d'importance)

| Fichier | Où elle apparaît | Cadrage conseillé |
|---|---|---|
| `hero.jpg` | Grande image plein écran de l'accueil | **Paysage**, très large, 2400 × 1400 px |
| `lodge-01.jpg` | Accueil, grande image de la section « L'art de recevoir » | **Portrait**, 1000 × 1500 px |
| `lodge-02.jpg` | Accueil, petite image (piscine) | **Carré**, 900 × 900 px |
| `lodge-03.jpg` | Accueil, petite image (terrasse) | **Carré**, 900 × 900 px |
| `restaurant.jpg` | Accueil, section restaurant | Paysage, 1600 × 1200 px |
| `entree.jpg` | Accueil, bas de page | Paysage, 1600 × 1200 px |

## Les expériences (accueil + page Expériences)

| Fichier | Sujet | Cadrage |
|---|---|---|
| `exp-restaurant.jpg` | Un plat, la salle ou la terrasse | **Portrait**, 900 × 1200 px |
| `exp-piscine.jpg` | La piscine | **Portrait**, 900 × 1200 px |
| `exp-nature.jpg` | Les extérieurs, le jardin, la vallée | **Portrait**, 900 × 1200 px |
| `exp-evenements.jpg` | Une table dressée, un événement | **Portrait**, 900 × 1200 px |

## La galerie de l'accueil

`galerie-01.jpg` → `galerie-06.jpg`

- `galerie-01.jpg` : **carré** (900 × 900 px), c'est la plus grande de la mosaïque
- `galerie-02` à `galerie-06` : paysage (1200 × 900 px)

## Les en-têtes des pages intérieures (facultatif)

Si absents, le site utilise automatiquement une autre photo de la liste.

| Fichier | Page |
|---|---|
| `head-chambres.jpg` | Chambres & Suites |
| `head-restaurant.jpg` | Restaurant |
| `head-services.jpg` | Expériences |
| `head-contact.jpg` | Contact |

## Les catégories de chambres

Une photo peut être rattachée à une catégorie en la nommant d'après son nom,
en minuscules et avec des tirets :

- « Bungalows Majestueux » → `bungalows-majestueux.jpg`
- « Suite Présidentielle » → `suite-presidentielle.jpg`

> Mieux encore : ajoutez les photos **directement dans le back-office**, chambre
> par chambre. Elles ont la priorité sur les fichiers de ce dossier.

---

## Conseils pour de belles photos

1. **Lumière naturelle**, de préférence en début de matinée ou en fin
   d'après-midi (lumière chaude, ombres douces).
2. **Horizon droit** et lignes verticales droites : évitez de photographier une
   chambre en biais.
3. **Chambres rangées** : lit fait, rideaux ouverts, aucun objet personnel.
4. **Poids des fichiers** : visez moins de 400 Ko par image. Un site rapide
   paraît plus luxueux qu'un site lent. Outil gratuit : <https://squoosh.app>.
5. **Format** : `.jpg` pour les photos. Gardez le nom exact du tableau.

Une fois les fichiers déposés, videz le cache des vues :

```bash
php artisan view:clear
```
