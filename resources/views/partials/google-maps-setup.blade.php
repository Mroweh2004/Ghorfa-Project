<div class="map-setup-notice" role="alert">
    <div class="map-setup-notice__icon" aria-hidden="true">
        <i class="fas fa-map-marked-alt"></i>
    </div>
    <h2 class="map-setup-notice__title">Google Maps is not configured on this machine</h2>
    <p class="map-setup-notice__lead">
        The map needs API keys in your local <code>.env</code> file. This is separate from Laravel — each developer must add keys (or use shared keys with the correct URL restrictions).
    </p>
    <ol class="map-setup-notice__steps">
        <li>Copy <code>.env.example</code> values into your <code>.env</code> if you have not already.</li>
        <li>Create a project in <a href="https://console.cloud.google.com/google/maps-apis" target="_blank" rel="noopener noreferrer">Google Cloud Console</a> and enable billing (Google provides free monthly credit).</li>
        <li>Enable: <strong>Maps JavaScript API</strong>, <strong>Places API</strong>, and <strong>Geocoding API</strong>.</li>
        <li>Create a <strong>Browser key</strong> and set <code>GOOGLE_MAPS_BROWSER_KEY=...</code> in <code>.env</code>.</li>
        <li>Create a <strong>Server key</strong> and set <code>GOOGLE_MAPS_SERVER_KEY=...</code> in <code>.env</code>.</li>
        <li>For the browser key, under <em>Application restrictions → HTTP referrers</em>, add every URL you use locally, for example:
            <ul>
                <li><code>http://localhost:*</code></li>
                <li><code>http://127.0.0.1:*</code></li>
                <li><code>http://192.168.*.*:*</code> (LAN / phone testing)</li>
            </ul>
        </li>
        <li>Run <code>php artisan config:clear</code> and refresh this page.</li>
    </ol>
    <p class="map-setup-notice__hint">
        If you see <strong>“For development purposes only”</strong> on the map, billing is usually not enabled on the Google project, or your browser key does not allow your current site URL (e.g. <code>{{ request()->getSchemeAndHttpHost() }}</code>).
    </p>
</div>
