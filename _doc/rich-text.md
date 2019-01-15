# Medium Editor
Le texte enrichi utilise medium editor (https://github.com/yabwe/medium-editor#button-options)


Un block texte formaté avec pas mal d'options
```php
<?=$vv->wysiwyg()
    ->field("texte")
    ->string(\Pov\Utils\StringUtils::FORMAT_HTML)
    ->setPlaceholder("Saisissez votre texte")
    ->setMediumButtons([
            "h1","h2",
            "bold","italic","underline","strikethrough",
            "orderedlist","unorderedlist",
            "anchor","select-record",
            "removeFormat"]
    )
    ->htmlTag("div")
    ->addClass("txt")
?>
```

Un block texte formaté où on remplace le bouton _italic_ 
```php
<?=$vv->wysiwyg()
    ->field("texte_lang")
    ->string(\Pov\Utils\StringUtils::FORMAT_HTML)
    ->setPlaceholder("Saisissez votre texte")
    ->setMediumButtons([
            [
            "name"=>'italic', //nom du bouton qui sera remplacé
            "action"=> 'italic', //action à appliquer
            "tagNames"=> ['i'],
            "contentDefault"=> '<b>Nom</b>', //texte du bouton
            "classList"=>["cq-unstyled"], //css sur le bouton
            ],
        "removeFormat"])
    ->htmlTag("div")
    ->addClass("txt")
?>
```
