<div class="cq-cols">
    <div>
        <h3>Champs simples</h3>
        <label>Label</label>
        <input class="fld" type="text" placeholder="text">
        <dfn>Un élément dfn permet d'annoter un champ</dfn>

        <input class="fld" type="text" placeholder="Avec valeur" value="Value">
        <input class="fld" type="email" placeholder="email">
        <input class="fld" type="number" placeholder="number">
        <input class="fld" type="password" placeholder="password">
        <input class="fld" type="date">
        <input class="fld" type="datetime-local">
        <input class="fld" type="time">
        <input class="fld" type="tel" name="usrtel" placeholder="06 44 88 55 33">
        <input class="fld" type="url" name="homepage" placeholder="http://www.google.com">


        <textarea class="fld" placeholder="Placeholder"></textarea>
        <textarea class="fld"><?php echo pov()->utils->string->loremIspum(200,200)?></textarea>
    </div>

    <div>

        <h3>Radio</h3>
        <div class="fld-chk">
            <label for="skyrock">Skyrock</label>
            <input id="skyrock" type="radio" name="gender" value="skyrock">
        </div>
        <div class="fld-chk">
            <label for="fc">France Culture</label>
            <input id="fc"type="radio" name="gender" value="france culture">
        </div>
        <div class="fld-chk">
            <label for="nrj">Nrj</label>
            <input id="nrj" type="radio" name="gender" value="nrj">
        </div>

        <h3>Checkboxed</h3>
        <div class="fld-chk">
            <label for="skyrock2">Skyrock</label>
            <input id="skyrock2" type="checkbox" name="ckb" value="skyrock">
        </div>
        <div class="fld-chk">
            <label for="fc2">France Culture</label>
            <input id="fc2"type="checkbox" name="ckb" value="france culture">
        </div>
        <div class="fld-chk">
            <label for="nrj2">Nrj</label>
            <input id="nrj2" type="checkbox" name="ckb" value="nrj">
        </div>

        <h3>Select</h3>
        <select class="fld">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
        </select>

        <label>Color</label>
        <input class="fld" type="color">



        <label>Range</label>
        <input class="fld" type="range" name="points" min="0" max="10">



    </div>

    <div>
        <h3>Images <small>(ça se corse)</small></h3>
    </div>

</div>



