(function($){
    $(document).ready(function(){
        ymaps.ready(init);
        function init() {
            var myMap = new ymaps.Map('map', {
                    center: [57.150612, 65.547308],
                    zoom: 9,
                }),
                zones,
                json = {
                    "empty" : new Array(),
                };

            $('#parse').click(function(){
                for (var uuid in coords){
                    coord = new Array();
                    coord.push(parseFloat(coords[uuid][0]));
                    coord.push(parseFloat(coords[uuid][1]));

                    highlightResult(coord, uuid);
                }
                $.post('/zones/poligons/export',
                    {
                        json : json,
                    },
                    function(){},
                    'json',
                )
            });

            $("#load").click(function(){
                ymaps.geoXml.load('https://valkinaz.ru/media/files/poligons.kml').then(onGeoXmlLoad);
            });

            // Обработчик загрузки XML-файлов.
            function onGeoXmlLoad(res) {
                zones = ymaps.geoQuery(res.geoObjects).addToMap(myMap);
            }

            function highlightResult(coord, uuid) {
                // Находим полигон, в который входят переданные координаты.
                var polygon = zones.searchContaining(coord).get(0);

                if (polygon) {
                    var poligon_name = polygon.properties.get('description');
                    if (json[poligon_name] == undefined) {
                        json[poligon_name] = new Array();
                    } 
                    json[poligon_name].push(uuid);
                } else {
                    json.empty.push(uuid);
                }
            }
        } 
    });
}(jQuery));