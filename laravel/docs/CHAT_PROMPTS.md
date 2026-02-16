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
