<div>
    @if($getState())
        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4 overflow-x-auto">
            <pre class="text-sm text-gray-900 dark:text-gray-100"><code>{{ json_encode($getState(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400">Keine Daten vorhanden</p>
    @endif
</div>

