<?php

/** German localization file for KCFinder
  * author: Tim Wahrendorff <wahrendorff@users.sourceforge.net>
  */

$lang = array(

    '_locale' => "de_DE.UTF-8",  // UNIX localization code
    '_charset' => "utf-8",       // Browser charset

    // Date time formats. See http://www.php.net/manual/en/function.strftime.php
    '_dateTimeFull' => "%A, %e.%B.%Y %k:%M",
    '_dateTimeMid' => "%a %e %b %Y %k:%M",
    '_dateTimeSmall' => "%d.%m.%Y %k:%M",

    "You don't have permissions to upload files." => "Du hast keine Berechtigung Dateien hoch zu laden.",
    "You don't have permissions to browse server." => "Fehlende Berechtigung.",
    "Cannot move uploaded file to target folder." => "Kann hochgeladene Datei nicht in den Zielordner verschieben.",
    "Unknown error." => "Unbekannter Fehler.",
    "The uploaded file exceeds {size} bytes." => "Die hochgeladene Datei Гјberschreitet die erlaubte DateigrГ¶Гџe von {size} bytes.",
    "The uploaded file was only partially uploaded." => "Die Datei wurde nur teilweise hochgeladen.",
    "No file was uploaded." => "Keine Datei hochgeladen.",
    "Missing a temporary folder." => "TemporГ¤rer Ordner fehlt.",
    "Failed to write file." => "Fehler beim schreiben der Datei.",
    "Denied file extension." => "Die Dateiendung ist nicht erlaubt.",
    "Unknown image format/encoding." => "Unbekanntes Bildformat/encoding.",
    "The image is too big and/or cannot be resized." => "Das Bild ist zu groГџ und/oder kann nicht verkleinert werden.",
    "Cannot create {dir} folder." => "Ordner {dir} kann nicht angelegt werden.",
    "Cannot rename the folder." => "Der Ordner kann nicht umbenannt werden.",
    "Cannot write to upload folder." => "Kann nicht in den upload Ordner schreiben.",
    "Cannot read .htaccess" => "Kann .htaccess Datei nicht lesen",
    "Incorrect .htaccess file. Cannot rewrite it!" => "Falsche .htaccess Datei. Die Datei kann nicht geschrieben werden",
    "Cannot read upload folder." => "Ziel Ordner kann nicht gelesen werden.",
    "Cannot access or create thumbnails folder." => "Kann thumbnails Ordner nicht erstellen oder darauf zugreifen.",
    "Cannot access or write to upload folder." => "Kann nicht auf den upload Ordner zugreifen oder darin schreiben.",
    "Please enter new folder name." => "Bitte einen neuen Ordnernamen angeben.",
    "Unallowable characters in folder name." => "Der Ordnername enthГ¤lt unerlaubte Zeichen.",
    "Folder name shouldn't begins with '.'" => "Ordnernamen sollten nicht mit '.' beginnen.",
    "Please enter new file name." => "Bitte gib einen neuen Dateinamen an.",
    "Unallowable characters in file name." => "Der Dateiname enthГ¤lt unerlaubte Zeichen",
    "File name shouldn't begins with '.'" => "Dateinamen sollten nicht mit '.' beginnen.",
    "Are you sure you want to delete this file?" => "Willst Du die Datei wirklich lГ¶schen?",
    "Are you sure you want to delete this folder and all its content?" => "Willst Du wirklich diesen Ordner und seinen gesamten Inhalt lГ¶schen?",
    "Non-existing directory type." => "Der Ordner Typ existiert nicht.",
    "Undefined MIME types." => "Unbekannte MIME Typen.",
    "Fileinfo PECL extension is missing." => "PECL extension fГјr Dateiinformationen fehlt",
    "Opening fileinfo database failed." => "в€љГ±ffnen der Dateiinfo Datenbank fehlgeschlagen.",
    "You can't upload such files." => "Du kannst solche Dateien nicht hochladen.",
    "The file '{file}' does not exist." => "Die Datei '{file}' existiert nicht.",
    "Cannot read '{file}'." => "Kann Datei '{file}' nicht lesen.",
    "Cannot copy '{file}'." => "Kann Datei '{file}' nicht kopieren.",
    "Cannot move '{file}'." => "Kann Datei '{file}' nicht verschieben.",
    "Cannot delete '{file}'." => "Kann Datei '{file}' nicht lГ¶schen.",
    "Cannot delete the folder." => "Der Ordner kann nicht gelГ¶scht werden.",
    "Click to remove from the Clipboard" => "Zum entfernen aus der Zwischenablage, hier klicken.",
    "This file is already added to the Clipboard." => "Diese Datei wurde bereits der Zwischenablage hinzugefГјgt.",
    "The files in the Clipboard are not readable." => "Die Dateien in der Zwischenablage kГ¶nnen nicht gelesen werden.",
    "{count} files in the Clipboard are not readable. Do you want to copy the rest?" => "{count} Dateien in der Zwischenablage sind nicht lesbar. MГ¶chtest Du die Гњbrigen trotzdem kopieren?",
    "The files in the Clipboard are not movable." => "Die Dateien in der Zwischenablage kГ¶nnen nicht verschoben werden.",
    "{count} files in the Clipboard are not movable. Do you want to move the rest?" => "{count} Dateien in der Zwischenablage sind nicht verschiebbar. MГ¶chtest Du die Гњbrigen trotzdem verschieben?",
    "The files in the Clipboard are not removable." => "Die Dateien in der Zwischenablage kГ¶nnen nicht gelГ¶scht werden.",
    "{count} files in the Clipboard are not removable. Do you want to delete the rest?" => "{count} Dateien in der Zwischenablage kГ¶nnen nicht gelГ¶scht werden. MГ¶chtest Du die Гњbrigen trotzdem lГ¶schen?",
    "The selected files are not removable." => "Die ausgewГ¤hlten Dateien kГ¶nnen nicht gelГ¶scht werden.",
    "{count} selected files are not removable. Do you want to delete the rest?" => "{count} der ausgewГ¤hlten Dateien kГ¶nnen nicht gelГ¶scht werden. MГ¶chtest Du die Гњbrigen trotzdem lГ¶schen?",
    "Are you sure you want to delete all selected files?" => "MГ¶chtest Du wirklich alle ausgewГ¤hlten Dateien lГ¶schen?",
    "Failed to delete {count} files/folders." => "Konnte {count} Dateien/Ordner nicht lГ¶schen.",
    "A file or folder with that name already exists." => "Eine Datei oder ein Ordner mit dem Namen existiert bereits.",
    "Copy files here" => "Kopiere Dateien hier hin.",
    "Move files here" => "Verschiebe Dateien hier hin.",
    "Delete files" => "LГ¶sche Dateien.",
    "Clear the Clipboard" => "Zwischenablage leeren",
    "Are you sure you want to delete all files in the Clipboard?" => "Willst Du wirklich alle Dateien in der Zwischenablage lГ¶schen?",
    "Copy {count} files" => "Kopiere {count} Dateien",
    "Move {count} files" => "Verschiebe {count} Dateien",
    "Add to Clipboard" => "Der Zwischenablage hinzufГјgen",
    "Inexistant or inaccessible folder." => "Ordnertyp existiert nicht.",
    "New folder name:" => "Neuer Ordnername:",
    "New file name:" => "Neuer Dateiname:",
    "Upload" => "Hochladen",
    "Refresh" => "Aktualisieren",
    "Settings" => "Einstellungen",
    "Maximize" => "Maximieren",
    "About" => "Гњber",
    "files" => "Dateien",
    "selected files" => "ausgewГ¤hlte Dateien",
    "View:" => "Ansicht:",
    "Show:" => "Zeige:",
    "Order by:" => "Ordnen nach:",
    "Thumbnails" => "Miniaturansicht",
    "List" => "Liste",
    "Name" => "Name",
    "Type" => "Typ",
    "Size" => "GrГ¶Гџe",
    "Date" => "Datum",
    "Descending" => "Absteigend",
    "Uploading file..." => "Lade Datei hoch...",
    "Loading image..." => "Lade Bild...",
    "Loading folders..." => "Lade Ordner...",
    "Loading files..." => "Lade Dateien...",
    "New Subfolder..." => "Neuer Unterordner...",
    "Rename..." => "Umbenennen...",
    "Delete" => "LГ¶schen",
    "OK" => "OK",
    "Cancel" => "Abbruch",
    "Select" => "AuswГ¤hlen",
    "Select Thumbnail" => "WГ¤hle Miniaturansicht",
    "Select Thumbnails" => "WГ¤hle Miniaturansicht",
    "View" => "Ansicht",
    "Download" => "Download",
    "Download files" => "Dateien herunterladen",
    "Clipboard" => "Zwischenablage",
    "Checking for new version..." => "Nach neuer Version suchen",
    "Unable to connect!" => "Kann keine Verbindung herstellen!",
    "Download version {version} now!" => "Version {version} herunterladen!",
    "KCFinder is up to date!" => "KCFinder ist aktuell!",
    "Licenses:" => "Lizenz",
    "Attention" => "Achtung",
    "Question" => "Frage",
    "Yes" => "Ja",
    "No" => "Nein",
    "You cannot rename the extension of files!" => "Die Umbenennung von Datei-Erweiterungen ist nicht mГ¶glich!",
    "Uploading file {number} of {count}... {progress}" => "Lade Datei {number} von {count} hoch ... {progress}",
    "Failed to upload {filename}!" => "Hochladen von {filename} fehlgeschlagen!",
    "Close" => "SchlieГџen",
    "Previous" => "Vorherige",
    "Next" => "NГ¤chste",
    "Confirmation" => "BestГ¤tigung",
    "Warning" => "Warnung"
);

?>