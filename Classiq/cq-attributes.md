#Les éléments avec un attribut [cq-quelque-chose]
Ces éléments seraient des webcomponents si cette techno marchait.

##Dans #the-cq-layer
On ne traîte dans ce chapitre que des éléments qui sont dans l'élément #the-cq-layer.
Autrement dit et pour la faire courte; les éléments qui sont dans le backoffice.

###[cq-visible-in-viewport]
#####Optimisation en fonction du scroll
Tous les éléments `cq-visible-in-viewport` sont masqués par défaut. 
Ils apparaissent (`visibility:visible`) quand ils sont visibles dans le scroll.

##N'importe où...

Pour recharger des records

###[cq-on-model-saved='refresh(this)']
Va recharger le template quand le record page-5 sera modifié.
```
<div class="poster" cq-on-model-saved="refresh(this)" data-pov-v-path="components/poster" data-pov-vv-uid="page-5">
    lorem ipsum
</div>
```