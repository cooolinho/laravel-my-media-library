In diesem Projekt verwalte ich meine Serien und die dazugehörigen Episoden. So habe ich einen Überblick darüber welche
Episoden ich schon besitze und welche ich nicht herunterladen muss.
Ich nutze Laravel 12 und Filament 5.
Falls Befehle ausgeführt werden sollen, müssen diese in den Docker-Container ausgeführt werden, da die Anwendung in
einem Container läuft. Das heißt, dass die Befehle mit `docker-compose exec -it -u sail laravel bash` ausgeführt werden
müssen, damit sie im Container ausgeführt werden.

Aktuell werden meine Serien mit einer Tabelle in der list angezeigt. Diese möchte ich ersetzt haben durch eine
Kachelansicht. In dieser Kachelansicht soll das Seriencover angezeigt werden, sowie der Titel der Serie.
Die Kachelansicht soll weiterhin die Möglichkeit haben haben die Serien zu filtern, zu sortieren und zu suchen.
Passe mir dazu meine ListSeriePage an, damit ich die Serien in einer Kachelansicht angezeigt bekomme. erstelle mir dazu
die notwendigen Komponenten, damit ich die Kachelansicht mit den Seriencovern und Titeln angezeigt bekomme.
--
Ich lade zusätzliche Daten für Serien und Episoden von einer externen API. Diese Daten werden in der Datenbank
gespeichert, damit sie schnell verfügbar sind.
Ich brauche einen Zeitstempel an jeder Serie und Epsiode, damit ich weiß, wann die Daten zuletzt aktualisiert wurden. So
kann ich entscheiden, ob ich die Daten erneut von der API laden muss oder ob die vorhandenen Daten noch aktuell sind.
Passe mir dazu meine Serien und Episoden Tabellen an, damit sie einen Zeitstempel für dien letzten Aktualisierung der
Daten haben. Erstelle mir dazu die notwendigen Migrationen, damit ich die Zeitstempel in der Datenbank gespeichert
werden können. Passe mir auch die entsprechenden Modelle an, damit die Zeitstempel in den Modellen verfügbar sind.
Erstelle mir auch die notwendigen Funktionen, damit ich die Zeitstempel aktualisieren kann, wenn ich die Daten von der
API lade.
