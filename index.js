const location = await fetch('http://ip-api.com/json/')
  .then((response) => response.json())
  .then((data) => {
    if (data.city !== 'Nancy') {
      return { lat: 48.683187984248235, lon: 6.161876994260524 }
    }
    return { lat: data.lon, lon: data.lat }
  })

//MAP CIRCULATION
var mapOptions = {
  center: [location.lat, location.lon],
  zoom: 12,
}
var map = new L.map('map', mapOptions)
var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
map.addLayer(layer)

//Marker Client
var clientIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
})
L.marker([parseFloat(location.lat), parseFloat(location.lon)], { icon: clientIcon })
  .addTo(map)
  .bindPopup('Vous êtes ici')

//Affichage difficultés routes
fetch('https://carto.g-ny.org/data/cifs/cifs_waze_v2.json')
  .then((response) => response.json())
  .then((data) => {
    data.incidents.forEach((element) => {
      L.marker([parseFloat(element.location.polyline.split(' ')[0]), parseFloat(element.location.polyline.split(' ')[1])])
        .addTo(map)
        .bindPopup(`${element.description}<br>Début: ${new Date(element.starttime).toLocaleDateString()}<br>Fin: ${new Date(element.endtime).toLocaleDateString()}`)
    })
  })
