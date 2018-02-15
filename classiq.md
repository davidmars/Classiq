#Classic
Classic est un module complet qui permet de créer des sites internets Wysiwyg assez rapidement.

#Base de données Redbean


#SEO

##Site maps xml

**Exemple**  
http://127.0.0.1/edsa-ee/fk-lab/sitemap.xml

Pour rajouter des types de pages à indexer dans les sitemaps:
```php
<?php
\Classiq\C_sitemap_xml::$modelTypesToIndex[]="montypedepage";
```

##robots.txt
**Exemple**  
http://127.0.0.1/edsa-ee/fk-lab/robots.txt

Pour rajouter des urls à ne pas indexer:

```php
<?php
\Classiq\C_robots.txt::$disallow[]="/mon-repertoire/";
```

#WYSIWYG

####wysiwyg-data-type="list"
Liste d'éléments réorganisables

##### list-horizontal="true"
La liste affichera des flèches horizontales

##### only-records="true"
La liste n'est composée que de records. 

En terme d'ergonomie, cela veut dire qu'au click sur le bouton + c'est la popin de choix de records qui va s'ouvrir.
L'utilisateur pourra choisir plusieurs records qui seront insérés dans la liste.

En terme de structure de données, la liste reste formatée en json avec des entrées key_etc... qui représenteront chaque entrée.
le record selectionné sera lui représenté par une variable uid=montype-monid (c'est une convention).
Ainsi, il sera possible d'attribuer d'autres variables à l'entrée, ce qui ne serait pas possibles avec des liste de type id1,id2,id3 etc...


##### wysiwyg-sortable="true"
L'élément est réorganisable (et utilise https://github.com/RubaXa/Sortable)

##### wysiwyg-item-templates="mon/template1,mon/template2"
La liste des templates qu'il est possible d'insérer dans la liste

##### popin-x-pos="0.25"
Les popins générées par cet élément seront positionnées à 25% de la largeur de l'écran;

##### popin-y-pos="0.75"
Les popins générées par cet élément seront positionnées à 75% de la hauteur de l'écran;

##### zzzzzzzzzzzzzzzzzzzzzzz
zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz

##### zzzzzzzzzzzzzzzzzzzzzzz
zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz

##### zzzzzzzzzzzzzzzzzzzzzzz
zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz

###### zzzzzzzzzzzzzzzzzzzzzzz
zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz








| Header 1 | Header 2 |
| -------- | -------- |
| Data 1   | Data 2   |