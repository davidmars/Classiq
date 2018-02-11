# Classiq
## Work in progress!
## Si vous voulez un CMS stable ne l'utilisez pas ;)

---
## Si vous aimez coder dangeureusement et que vous comprennez le français continuez à lire...

Classiq est un framework WYSIWYG qui fonctionne avec Pov, Webpack, Composer et Redbean.

### à quoi ressemble un projet réalisé avec Classiq?
à un site internet full Ajax éditable en Wysiwyg.

### Quels languages sont utilisés?
- PHP
- Javascript
- HTML
- Less ou Sass
- MySql, SqlLite, ou tout autre système de BDD pris en charge par l'ORM Redbean.

### Quels langages mon serveur doit-il parler?
- Apache ou tout autre moyen d'interpréter un fichier .htaccess pour faire de la réécriture d'URL.
- PHP (version >=7 svp) 
- MySql, SqlLite, ou tout autre système de BDD pris en charge par l'ORM Redbean.

### Quels langages mon poste local doit-il parler?

Pour que ça tourne la même chose que votre serveur, autrement dit:
- Apache ou tout autre moyen d'interpréter un fichier .htaccess pour faire de la réécriture d'URL.
- PHP (version >=7 svp) 
- MySql, SqlLite, ou tout autre système de BDD pris en charge par l'ORM Redbean.

Pour développer:
- Node JS (Afin d'utiliser webpack et tout ce qui s'en suit)
- Composer (Afin d'installer les librairies PHP dont vous aurez besoin)

*Est-ce que je dois savoir coder en Node.js pour utiliser ce framework?*

**NON**

*Est-ce que je dois savoir utiliser webpack pour utiliser ce framework?*

**NON** mais vous l'utiliserez quand même en permanence, donc il est fortement conseillé de savoir un peu comment ça marche.

### Quel patern est utilisé?

**MVC** dans la mesure où vous avez une séparation claire entre Modèles, Vues et Controlleurs. Cependant on peut construire un projet sans écrire le moindre Controlleur car ceux qui existent dans Classiq sont souvent suffisants.

L'objectif est de vous permettre de travailler 99% du temps sous forme de **composants**. Ainsi vous aurez des répertoires dans lesquels se trouveront des fichiers PHP pour les vues, des fichiers css (less/sass) et des fichiers Javascript (et bien entendu des fichiers HTML, mustache, SVG, etc... si vous en avez besoin). Tout ça ressemblera de loin dans la manière dont c'est organisé à un projet Polymer, Vue ou React sauf qu'en dessous du capot tout sera beaucoup plus orienté serveur que client.


