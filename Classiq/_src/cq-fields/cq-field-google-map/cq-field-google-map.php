<?php
/** @var array $vv [lattitude,longitude]*/
?>
<div cq-field-google-map>
    <table>
        <tr>
            <td><label>lat</label></td>
            <td><input type="number" name="lat" latlng="lat" class="fld" value="<?=$vv[0]?>"></td>
            <td><label>lng</label></td>
            <td><input type="number" name="lng" latlng="lng"  class="fld" value="<?=$vv[1]?>"></td>
        </tr>
    </table>

    <input class="search controls" type="text" placeholder="Rechercher un lieu">

    <div class="map">
        map here
    </div>
</div>