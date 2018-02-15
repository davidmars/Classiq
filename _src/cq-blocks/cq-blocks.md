#cq-blocks
Les objets cq-blocks sont des listes de *blocks* 



```
<div    cq-blocks 
        
        wysiwyg-var="ma-variable" 
        wysiwyg-type="type-de-record" 
        wysiwyg-id="id-du-record"
        wysiwyg-data-type="list"
        
        wysiwyg-item-templates="mon/template-de-block-1,mon/template-de-block-2,etc..."
        only-images="false"
        
        context-menu-is-list="true"
        context-menu-size="small"
        context-menu-position="tr"

        block-picker-empty-message="Insérez des blocks ici"
        block-picker-message="Insérez des blocks ici"
        
        >
    ...
</div>
```

###Attributs

####`on-key-enter-action`
Type: `String` <br> 
Valeur possible: `addItem` <br> Ajoute un nouveau block à la liste. <br>
Définition via PHP `\Classiq\Wysiwyg\FieldsTyped\FieldListJson::onKeyEnter(action)`

Permet de définir une action à effectuer quand la touche ENTER est préssee au sein de la liste.

