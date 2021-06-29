<?php

/** Finnish localization file for KCFinder
  * author: Heikki Tenhunen (heikki@sivudesign.fi)
  */

$lang = array(

    '_locale' => "fi_FI.UTF-8",  // UNIX localization code
    '_charset' => "utf-8",       // Browser charset

    // Date time formats. See http://www.php.net/manual/en/function.strftime.php
    '_dateTimeFull' => "%A, %e %B, %Y %H:%M",
    '_dateTimeMid' => "%a %e %b %Y %H:%M",
    '_dateTimeSmall' => "%d.%m.%Y %H:%M",

    "You don't have permissions to upload files." => "Sinulla ei ole oikeuksia ladata tiedostoja.",
    "You don't have permissions to browse server." => "Sinulla ei ole riittГ¤viГ¤ oikeuksia tiedostoselaimeen.",
    "Cannot move uploaded file to target folder." => "Ladattua tiedostoa ei voi siirtГ¤Г¤ kohdekansioon. ",
    "Unknown error." => "Tuntematon virhe.",
    "The uploaded file exceeds {size} bytes." => "Tiedoston koko ylittГ¤Г¤ {size} tavua.",
    "The uploaded file was only partially uploaded." => "Valitsemasi tiedosto latautui vain osittain.",
    "No file was uploaded." => "YhtГ¤Г¤n tiedostoja ei ladattu.",
    "Missing a temporary folder." => "Puuttuu vГ¤liaikainen kansio.",
    "Failed to write file." => "Tiedostoon kirjoitus epГ¤onnistui.",
    "Denied file extension." => "Kielletty tiedostopГ¤Г¤te.",
    "Unknown image format/encoding." => "Tuntematon kuvatiedosto/koodaus.",
    "The image is too big and/or cannot be resized." => "Kuva on liian iso, koon muuttaminen ei onnistu.",
    "Cannot create {dir} folder." => "Kansiota {dir} ei voi luoda.",
    "Cannot rename the folder." => "Kansiota ei voi nimetГ¤ uudelleen.",
    "Cannot write to upload folder." => "Latauskansioon ei voi kirjoittaa.",
    "Cannot read .htaccess" => ".htaccess tiedostoa ei voi lukea.",
    "Incorrect .htaccess file. Cannot rewrite it!" => "Virheellinen .htaccess tiedosto. Tiedostoon ei voi kirjoittaa.",
    "Cannot read upload folder." => "Latauskansiota ei voi lukea.",
    "Cannot access or create thumbnails folder." => "Esikatselukuvien kansiota ei voi lukea tai kirjoittaa.",
    "Cannot access or write to upload folder." => "Latauskansiota ei voi lukea tai kirjoittaa.",
    "Please enter new folder name." => "Kirjoita uuden kansion nimi.",
    "Unallowable characters in folder name." => "KiellettyjГ¤ merkkejГ¤ kansion nimessГ¤.",
    "Folder name shouldn't begins with '.'" => "Kansion nimi ei voi alkaa '.'",
    "Please enter new file name." => "Kirjoita uusi tiedostonimi.",
    "Unallowable characters in file name." => "KiellettyjГ¤ merkkejГ¤ tiedoston nimessГ¤.",
    "File name shouldn't begins with '.'" => "Tiedoston nimi ei voi alkaa '.'",
    "Are you sure you want to delete this file?" => "Haluatko varmasti poistaa tiedoston?",
    "Are you sure you want to delete this folder and all its content?" => "Haluatko varmasti poistaa tiedoston sekГ¤ kaiken sen sisГ¤llГ¶n?",
    "Non-existing directory type." => "Hakemisto tyyppi ei ole olemassa.",
    "Undefined MIME types." => "MГ¤Г¤rittГ¤mГ¤ttГ¶mГ¤t MIME tyypit.",
    "Fileinfo PECL extension is missing." => "Fileinfo PECL pГ¤Г¤te puuttuu.",
    "Opening fileinfo database failed." => "Opening fileinfo database failed.",
    "You can't upload such files." => "Tiedostoja ei voi ladata.",
    "The file '{file}' does not exist." => "Tiedostoa '{file}' ei ole luotu.",
    "Cannot read '{file}'." => "Tiedostoa '{file}' ei voi lukea.",
    "Cannot copy '{file}'." => "Tiedostoa '{file}' ei voi kopioda.",
    "Cannot move '{file}'." => "Tiedostoa '{file}' ei voi siirtГ¤Г¤.",
    "Cannot delete '{file}'." => "Tiedostoa '{file}' ei voi poistaa.",
    "Cannot delete the folder." => "Kansiota ei voi poistaa.",
    "Click to remove from the Clipboard" => "Klikkaa poistaaksesi LeikepГ¶ydГ¤ltГ¤.",
    "This file is already added to the Clipboard." => "Tiedosto on jo lisГ¤tty LeikepГ¶ydГ¤lle.",
    "The files in the Clipboard are not readable." => "LeikepГ¶ydГ¤n tiedostot eivГ¤t ole luettavissa.",
    "{count} files in the Clipboard are not readable. Do you want to copy the rest?" => "LeikepГ¶ydГ¤llГ¤ on {count} tiedostoa joita ei voi lukea. Haluatko kopioida loput?",
    "The files in the Clipboard are not movable." => "LeikepГ¶ydГ¤n tiedostoja ei voi  siirtГ¤Г¤.",
    "{count} files in the Clipboard are not movable. Do you want to move the rest?" => "LeikepГ¶ydГ¤llГ¤ on {count} tiedostoa joita ei voi siirtГ¤Г¤. Haluatko siirtГ¤Г¤ loput?",
    "The files in the Clipboard are not removable." => "LeikepГ¶ydГ¤n tiedostoja ei voi poistaa.",
    "{count} files in the Clipboard are not removable. Do you want to delete the rest?" => "LeikepГ¶ydГ¤llГ¤ on {count} tiedostoa joita ei voi poistaa. Haluatko siirtГ¤Г¤ loput?",
    "The selected files are not removable." => "Valittuja tiedostoja ei voi poistaa.",
    "{count} selected files are not removable. Do you want to delete the rest?" => "LeikepГ¶ydГ¤llГ¤ on {count} tiedostoa joita ei voi poistaa. Haluatko poistaa loput?",
    "Are you sure you want to delete all selected files?" => "Haluatko varmasti poistaa kaikki valitut tiedostot?",
    "Failed to delete {count} files/folders." => "{count} tiedoston/kansion poistaminen epГ¤onnistui.",
    "A file or folder with that name already exists." => "Tiedosto tai kansio nimellГ¤ on jo luoto.",
    "Copy files here" => "Kopio tГ¤hГ¤n",
    "Move files here" => "SiirrГ¤ tГ¤hГ¤n",
    "Delete files" => "Poista tiedostot",
    "Clear the Clipboard" => "Pyyhi leikepГ¶ytГ¤.",
    "Are you sure you want to delete all files in the Clipboard?" => "Haluatko varmasti poistaa kaikki tiedostot LeikepГ¶ydГ¤ltГ¤?",
    "Copy {count} files" => "Kopio {count} tiedostoa",
    "Move {count} files" => "SiirrГ¤ {count} tiedostoa",
    "Add to Clipboard" => "LisГ¤Г¤ LeikepГ¶ydГ¤lle",
    "Inexistant or inaccessible folder." => "Kansiota ei ole olemassa tai sitГ¤ ei voi avata.",
    "New folder name:" => "Uusi kansion nimi:",
    "New file name:" => "Uusi tiedostonimi:",
    "Upload" => "Lataa",
    "Refresh" => "PГ¤ivitГ¤",
    "Settings" => "Asetukset",
    "Maximize" => "Koko ruutu",
    "About" => "LisГ¤tietoja",
    "files" => "tiedostot",
    "selected files" => "valitut tiedostot",
    "View:" => "NГ¤kymГ¤:",
    "Show:" => "NГ¤ytГ¤:",
    "Order by:" => "JГ¤rjestГ¤:",
    "Thumbnails" => "Esikatselukuvat",
    "List" => "Lista",
    "Name" => "Nimi",
    "Type" => "Tyyppi",
    "Size" => "Koko",
    "Date" => "PГ¤ivГ¤ys",
    "Descending" => "Laskeva",
    "Uploading file..." => "SiirretГ¤Г¤n tiedostoa...",
    "Loading image..." => "Ladataan tiedostoa...",
    "Loading folders..." => "Ladataan kansioita...",
    "Loading files..." => "Ladataan tiedostoja...",
    "New Subfolder..." => "Uusi alikansio...",
    "Rename..." => "NimeГ¤ uudelleen...",
    "Delete" => "Poista",
    "OK" => "OK",
    "Cancel" => "Peru",
    "Select" => "Valitse",
    "Select Thumbnail" => "Valitse esikatselukuva",
    "Select Thumbnails" => "Valitse esikatselukuvat",
    "View" => "NГ¤ytГ¤",
    "Download" => "Lataa",
    "Download files" => "Lataa tiedostot",
    "Clipboard" => "LeikepГ¶ytГ¤",
    "Checking for new version..." => "Tarkastetaan uusin versio...",
    "Unable to connect!" => "Yhteys epГ¤onnistui!",
    "Download version {version} now!" => "Lataa versio {version} nyt!",
    "KCFinder is up to date!" => "KCFinder uusin versio on kГ¤ytГ¶ssГ¤!",
    "Licenses:" => "Lisenssit:",
    "Attention" => "Huomio",
    "Question" => "Kysymys",
    "Yes" => "KyllГ¤",
    "No" => "Ei",
    "You cannot rename the extension of files!" => "Et voi nimetГ¤ uudelleen tiedostopГ¤Г¤tettГ¤!",
    "Uploading file {number} of {count}... {progress}" => "SiirretГ¤Г¤n tiedostoa {number}/{count} ... {progress}",
    "Failed to upload {filename}!" => "Siirto epГ¤onnistui {filename}!",
);

?>