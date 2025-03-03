<x-filament::page>
    @push('styles')
        @vite('resources/css/filament.css')
    @endpush

    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Suche</h1>

        <input
            type="text"
            id="searchInput"
            class="border p-2 rounded w-full"
            placeholder="Geben Sie mindestens 3 Buchstaben ein..."
        />

        <template id="resultCardTemplate">
            <div class="result-card">
                <img class="result-image" src="" alt="">
                <div class="result-info">
                    <h3 class="result-name"></h3>
                    <p class="result-overview"></p>
                    <div class="result-details">
                        <span class="result-year"></span>
                        <span class="result-status"></span>
                        <span class="result-airtime"></span>
                    </div>
                    <div class="result-actions">
                        <x-filament::link href="#" class="action-new">
                            Hinzufügen
                        </x-filament::link>
                    </div>
                </div>
            </div>
        </template>

        <div id="searchResults" class="search-results">
            <!-- Dynamisch generierte Suchergebnisse werden hier eingefügt -->
        </div>
    </div>

    <script>
        let timeout = null; // Variable zum Speichern des Timeout-Handles

        document.getElementById('searchInput').addEventListener('input', function() {
            let query = this.value;

            // Wenn weniger als 3 Buchstaben eingegeben wurden, keine Anfrage senden
            if (query.length < 3) {
                document.getElementById('searchResults').innerHTML = "";
                return;
            }

            // Wenn der Timeout bereits gesetzt ist, löschen wir ihn, bevor wir den neuen setzen
            clearTimeout(timeout);

            // Den Timeout auf 100 ms setzen, um eine Anfrage erst nach der Pause zu senden
            timeout = setTimeout(function() {
                fetch(`{{ route('api.search') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ q: query })
                })
                    .then(response => response.json())
                    .then(data => {
                        let resultsList = document.getElementById('searchResults');
                        resultsList.innerHTML = "";

                        data.forEach(item => {
                            // Hole das Template-Element
                            let template = document.getElementById('resultCardTemplate');

                            // Klone das Template
                            let card = template.content.cloneNode(true);

                            // Füge die Daten in die entsprechenden Elemente ein
                            card.querySelector('.result-image').src = item.image;
                            card.querySelector('.result-image').alt = item.name;
                            card.querySelector('.result-name').textContent = item.name;
                            card.querySelector('.result-overview').textContent = item.overview;
                            card.querySelector('.result-year').textContent = `Jahr: ${item.year}`;
                            card.querySelector('.result-status').textContent = `Status: ${item.status}`;
                            card.querySelector('.result-airtime').textContent = `Erstausstrahlung: ${item.first_air_time}`;
                            card.querySelector('.action-new').href = item.action_new;

                            // Füge die Card in das DOM ein
                            document.getElementById('searchResults').appendChild(card);
                        });

                    });
            }, 1000);
        });
    </script>
</x-filament::page>
