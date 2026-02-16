{{-- Statistiken --}}
<div class="series-stats">
    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $record->episodes->count() }}</div>
            <div class="stat-label">Episoden gesamt</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon owned">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $record->getEpisodesOwnedCount() }}</div>
            <div class="stat-label">Eigene Episoden</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon progress">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ number_format($record->getEpisodeOwnedPercentage(), 1) }}%</div>
            <div class="stat-label">Fortschritt</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon seasons">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $record->episodes->pluck('seasonNumber')->unique()->count() }}</div>
            <div class="stat-label">Staffeln</div>
        </div>
    </div>
</div>

