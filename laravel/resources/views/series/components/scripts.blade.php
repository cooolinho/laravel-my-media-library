<script>
    function toggleSeason(button) {
        const seasonBlock = button.closest('.season-block');
        const content = seasonBlock.querySelector('.season-content');

        seasonBlock.classList.toggle('open');

        if (seasonBlock.classList.contains('open')) {
            content.style.maxHeight = content.scrollHeight + 'px';
        } else {
            content.style.maxHeight = '0';
        }
    }

    function toggleArtworks(button) {
        const artworksBlock = button.closest('.artworks-accordion-block');
        const content = artworksBlock.querySelector('.artworks-accordion-content');

        artworksBlock.classList.toggle('open');

        if (artworksBlock.classList.contains('open')) {
            // Setze initial auf scrollHeight
            content.style.maxHeight = content.scrollHeight + 'px';

            // Nach der Animation entferne max-height für dynamisches Wachstum
            setTimeout(() => {
                if (artworksBlock.classList.contains('open')) {
                    content.style.maxHeight = 'none';
                }
            }, 400); // 400ms = Transition-Dauer
        } else {
            // Vor dem Schließen setze max-height explizit
            content.style.maxHeight = content.scrollHeight + 'px';
            // Force reflow
            content.offsetHeight;
            // Dann auf 0 setzen für Animation
            content.style.maxHeight = '0';
        }
    }

    // Öffne die erste Staffel standardmäßig
    document.addEventListener('DOMContentLoaded', function () {
        const firstSeason = document.querySelector('.season-block');
        if (firstSeason) {
            const firstButton = firstSeason.querySelector('.season-toggle');
            if (firstButton) {
                toggleSeason(firstButton);
            }
        }
    });
</script>

