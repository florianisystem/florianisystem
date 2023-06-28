import { ready } from './ready';
import L from 'leaflet';

ready(() => {
  const params = new URLSearchParams(window.location.search);
  const map = L.map('map').setView([params.get('y') ?? 47.928, params.get('x') ?? 16.207], params.get('z') ?? 15);

  const baseLayers = {
    'OpenStreetMap': L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }),
    'Basemap': L.tileLayer('https://maps{s}.wien.gv.at/basemap/geolandbasemap/{type}/google3857/{z}/{y}/{x}.{format}', {
      maxZoom: 19,
      attribution: 'Datenquelle: <a href="https://www.basemap.at">basemap.at</a>',
      subdomains: ['', '1', '2', '3', '4'],
      type: 'normal',
      format: 'png',
      bounds: [[46.35877, 8.782379], [49.037872, 17.189532]]
    }),
    'Orthofoto': L.tileLayer('https://maps{s}.wien.gv.at/basemap/bmaporthofoto30cm/{type}/google3857/{z}/{y}/{x}.{format}', {
      maxZoom: 19,
      attribution: 'Datenquelle: <a href="https://www.basemap.at">basemap.at</a>',
      subdomains: ['', '1', '2', '3', '4'],
      type: 'normal',
      format: 'jpeg',
      bounds: [[46.35877, 8.782379], [49.037872, 17.189532]]
    })
  };

  const overlays = {};
  if (params.has('json')) {
    const geojson = JSON.parse(window.atob(params.get('json')));
    const geojsonLayer = L.geoJSON(geojson, {
      pointToLayer: (geoJsonPoint, latlng) => (L.circleMarker(latlng)),
      style: (feature) => (feature.properties.color ? {
        color: feature.properties.color
      } : {}),
      onEachFeature: (feature, layer) => {
        if (layer.feature.properties.name) {
          layer.bindPopup(layer.feature.properties.name);
        }
      }
    });
    overlays['Daten'] = geojsonLayer;
    map.fitBounds(geojsonLayer.getBounds());
  }

  L.control.layers(baseLayers, overlays).addTo(map);
  map.addLayer(baseLayers.OpenStreetMap);
  map.addLayer(overlays.Daten);
});
