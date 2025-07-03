<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>アンケートフォーム</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  />
  <style>
    #map {
      height: 600px;
      width: 100%;
    }
  </style>
</head>
<body>
  <h2>どの場所から自転車で舞鶴に来ますか。地図上をタップしてください。</h2>
  <form action="submit.php" method="POST" onsubmit="return prepareSubmission();">
    <div id="map"></div>
    <input type="hidden" name="selectedGrid" id="selectedGrid">
    <input type="hidden" name="lat" id="lat">
    <input type="hidden" name="lng" id="lng">
    <button type="submit">送信</button>
  </form>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // 舞鶴付近に地図を設定
    const map = L.map('map').setView([33.24, 131.62], 11);

    // 地図タイル
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // 舞鶴マーカー
    L.marker([33.244583,131.623249]).addTo(map).bindPopup("舞鶴");

    // メッシュ生成範囲（舞鶴周辺）
    const latMin = 33.0, latMax = 33.4;
    const lngMin = 131.36, lngMax = 131.9;
    const delta = 0.009; // ≒1km

    let meshId = 0;
    let selectedRect = null;
    let selectedId = null;
    let selectedLat = null;
    let selectedLng = null;

    // メッシュ描画
    for (let lat = latMin; lat <= latMax; lat += delta) {
      for (let lng = lngMin; lng <= lngMax; lng += delta) {
        const bounds = [
          [lat, lng],
          [lat + delta, lng + delta]
        ];

        const rect = L.rectangle(bounds, {
          color: "#888",
          weight: 1,
          fill: true,
          fillColor: "#ddd",
          fillOpacity: 0.1
        }).addTo(map);

        const id = `mesh_${meshId++}`;
        rect._meshId = id;

        rect.on('click', () => {
          if (selectedRect) {
            selectedRect.setStyle({
              color: "#888",
              fillColor: "#ddd",
              fillOpacity: 0.1,
              weight: 1
            });
          }

          selectedRect = rect;
          selectedId = id;

          const center = rect.getBounds().getCenter();
          selectedLat = center.lat.toFixed(6);
          selectedLng = center.lng.toFixed(6);

          rect.setStyle({
            color: "red",
            fillColor: "red",
            fillOpacity: 0.4,
            weight: 2
          });
        });
      }
    }

    function prepareSubmission() {
      document.getElementById("selectedGrid").value = selectedId || "";
      document.getElementById("lat").value = selectedLat || "";
      document.getElementById("lng").value = selectedLng || "";
      return true;
    }
  </script>
</body>
</html>
