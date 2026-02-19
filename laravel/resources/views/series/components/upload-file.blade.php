<div class="p-4 bg-gray-100 rounded-lg">
    <h3 class="text-lg font-semibold text-gray-900">Hinweise zum Upload</h3>
    <p class="text-sm text-gray-700 mt-2">
        Bitte laden Sie eine <strong>Textdatei (.txt)</strong> hoch.
    </p>
    <p class="text-sm text-gray-700 mt-2 mb-2">
        Zum Erstellen der .txt Datei kann folgender Befehl verwendet werden:
    </p>
    <code>
        find . -mindepth 1 -maxdepth 2 -type f \( -name "*.mkv" -o -name "*.avi" -o -name "*.mp4" \) -printf '%f\n' >>
        list.txt
    </code>
    <p class="text-sm text-gray-700 mt-2 mb-2">
        Episoden sollten mit dieser Syntax benannt sein: <b>S01E01</b>, <b>S01E02</b> usw.
    </p>
</div>
