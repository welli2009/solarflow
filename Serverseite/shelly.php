<?php

// Pfadangaben
$dateiPfad = 'data.txt';
$shellyZielDateiPfad = 'shelly.json';

// Anzahl der Durchläufe
$anzahlDurchlaeufe = 3;

// Aktuelle Zeit
$jetzt = new DateTime();

// Iterationsschleife
for ($i = 0; $i < $anzahlDurchlaeufe; $i++) {
    // Lese die Datei
    $daten = file_get_contents($dateiPfad);

    // Suchmuster für den JSON-Teil
    $jsonMuster = '/{(.+?)}/s';

    if (preg_match($jsonMuster, $daten, $matches)) {
        // Den JSON-Teil isolieren und decodieren
        $jsonTeil = '{' . $matches[1] . '}';
        $jsonArray = json_decode($jsonTeil, true);

        // Prüfen, ob der Wert 'total_act_power' vorhanden ist
        if (isset($jsonArray['total_act_power'])) {
            $totalActPower = $jsonArray['total_act_power'];

            // Daten als Array vorbereiten
            $datenArray = [
                'zeitstempel' => $jetzt->format('Y-m-d H:i:s'),
                'total_act_power' => $totalActPower
            ];

            // Bestehende Daten aus der Datei lesen und in ein Array konvertieren
            $vorhandeneDaten = [];
            if (file_exists($shellyZielDateiPfad)) {
                $vorhandeneDaten = json_decode(file_get_contents($shellyZielDateiPfad), true) ?: [];
            }

            // Neuen Wert hinzufügen
            $vorhandeneDaten[] = $datenArray;

            // Bereinige Einträge, die älter als 6 Stunden sind
            $zeitVor6Stunden = $jetzt->sub(new DateInterval('PT6H'))->getTimestamp();
            $bereinigteDaten = array_filter($vorhandeneDaten, function ($eintrag) use ($zeitVor6Stunden) {
                $zeit = strtotime($eintrag['zeitstempel']);
                return $zeit >= $zeitVor6Stunden;
            });

            // Bereinigte Daten zurück in die Datei schreiben
            file_put_contents($shellyZielDateiPfad, json_encode(array_values($bereinigteDaten)));

            echo "Wert total_act_power gespeichert: $totalActPower\n";
        } else {
            echo "Wert total_act_power nicht gefunden.\n";
        }
    } else {
        echo "JSON-Teil nicht gefunden.\n";
    }

    // Warte 5 Sekunden bis zum nächsten Durchlauf
    if ($i < $anzahlDurchlaeufe - 1) {
        sleep(15);
    }
}
