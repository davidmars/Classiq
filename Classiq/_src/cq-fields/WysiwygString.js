export default class WysiwygString{
    /**
     * Formate un texte
     * @param {string} str
     * @param {string} formatIdentifier
     * @returns {*}
     */
    static format(str,formatIdentifier){
        switch (formatIdentifier){
            case "STRING_FORMAT_NO_HTML_SINGLE_LINE":
            case "FORMAT_NO_HTML_MULTI_LINE":
                let div = document.createElement("div");
                div.innerHTML = str;
                str= div.textContent || div.innerText || "";
                return str;
            default:
                return str;
        }
    }
}