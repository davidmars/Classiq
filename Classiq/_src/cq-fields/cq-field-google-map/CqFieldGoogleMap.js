import DisplayObject from "../../DisplayObject";
require("./cq-field-google-map.less");

/**
 *
 */
export default class CqFieldGoogleMap extends DisplayObject{
    /**
     *
     * @param {JQuery} $main
     */
    constructor($main){
        super($main,"CqFieldGoogleMap");
        let me=this;

        this.$lat=$main.find("[latlng='lat']");
        this.$lng=$main.find("[latlng='lng']");
        this.$map=$main.find(".map");
        this.$search=$main.find(".search");


        $main.find("[latlng]").on("input change",function(){
            marker.setPosition({lat: Number(me.$lat.val()), lng: Number(me.$lng.val())});
        });

        let lat=Number(me.$lat.val());
        let lng=Number(me.$lng.val());
        let center = {lat: lat, lng: lng};
        let map=this.map = new google.maps.Map(me.$map[0], {
            center: center,
            zoom:5
        });
        let marker = new google.maps.Marker({
            position: center,
            map: map,
            title: 'Déplacez moi',
            draggable:true
        });
        marker.addListener("dragend",function(e){
            me.$lat.val(marker.getPosition().lat());
            me.$lng.val(marker.getPosition().lng());
            me.$lat.trigger("change");
        });

        //------------------uniquement search maintenant----------------------------


        // Create the search box and link it to the UI element.
        var input = me.$search[0];
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();


            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach(function(marker) {
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                let n=new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name+" / cliquez pour déplacer ici",
                    position: place.geometry.location
                });
                n.addListener("click",function(){
                    marker.setPosition(n.getPosition());
                    me.$lat.val(marker.getPosition().lat());
                    me.$lng.val(marker.getPosition().lng());
                    me.$lat.trigger("change");
                })
                markers.push(n);

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });



    }

    destroy(){
        //this.mediumEditor.destroy();
    }
}