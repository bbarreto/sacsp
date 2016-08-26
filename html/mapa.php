<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <title>KML Layers</title>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script>

function initMap() {
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 11,
    center: {lng: -46.417402, lat: -23.523283}
  });

  var ctaLayer = new google.maps.KmlLayer({
    url: 'http://upload.mamulti.com.br/original/AMIfv96IEdBx5DM5wW5V3TbJ-RACKfNMUY42GFB3Fb5NxPeV9J9TeYsi8FSqfnQL4RsmVUTnzGhKUG3gm0KKznE_GHIJmRViQOMZFf7ss_bFL-pBoANGTQyNSEa0Fm3DTEGyTL0Emk-xkL2TYivvCmOQQFPy2lHe_A',
    map: map
  });
}

    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?callback=initMap">
    </script>
  </body>
</html>