function csvJSON(csv) {
  var lines = csv.split('\n')
  var result = []
  var headers = lines[0].split(',')
  for (var i = 1; i < lines.length; i++) {
    var obj = {}
    var currentline = lines[i].split(',')
    for (var j = 0; j < headers.length; j++) {
      obj[headers[j]] = currentline[j]
    }
    result.push(obj)
  }
  return JSON.stringify(result) //JSON
}

//Récupération qualité air
fetch(
  'https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson&token='
)
  .then((response) => response.json())
  .then((data) => {
    const qualiteAir = data.features[0].attributes.lib_qual
    // console.log(data.features[0].attributes.lib_qual)
  })

//GESTION IP ET DEPARTEMENT
let departement
const location = await fetch('http://ip-api.com/json/')
  .then((response) => response.json())
  .then((data) => {
    departement = data.zip.substring(0, 2)
    if (data.city !== 'Nancy') {
      return { lat: 48.683187984248235, lon: 6.161876994260524 }
    }
    return { lat: data.lon, lon: data.lat }
  })

//récupération donnée covid
let hosp = []
let dchosp = []
let rea = []
let dateCovid = []
let date = `${new Date().getFullYear()}-01-01`
await fetch('https://www.data.gouv.fr/fr/datasets/r/5c4e1452-3850-4b59-b11c-3dd51d7fb8b5')
  .then((response) => response.text())
  .then((response) => {
    let data = JSON.parse(csvJSON(response))
    data.forEach((element) => {
      if (element.dep == departement && element.date >= date) {
        // console.log(element)
        rea.push(element.rea)
        hosp.push(element.hosp)
        dchosp.push(element.dchosp)
        dateCovid.push(element.date)
      }
    })
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

const hospitalisation = document.getElementById('myChart1')
const reanimation = document.getElementById('myChart2')
const dc_hospitalisation = document.getElementById('myChart3')

new Chart(hospitalisation, {
  type: 'line',
  data: {
    labels: dateCovid,
    datasets: [
      {
        label: 'Hospitalisation',
        data: hosp,
        borderWidth: 1,
        borderColor: 'rgb(255, 0, 0)',
      },
    ],
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
})
new Chart(reanimation, {
  type: 'line',
  data: {
    labels: dateCovid,
    datasets: [
      {
        label: 'Réanimation',
        data: rea,
        borderWidth: 1,
        borderColor: 'rgb(0 ,0, 255)',
      },
    ],
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
})
new Chart(dc_hospitalisation, {
  type: 'line',
  data: {
    labels: dateCovid,
    datasets: [
      {
        label: "Décès à l'hopital",
        data: dchosp,
        borderWidth: 1,
        borderColor: 'rgb(0, 0, 0)',
      },
    ],
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
})
