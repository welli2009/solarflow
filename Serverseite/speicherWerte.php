<?php

// Pfadangaben
$dateiPfad = 'data.txt';
$zielDateiPfad = 'gespeicherteWerte.json';

// Anzahl der Durchläufe
$anzahlDurchlaeufe = 10;

// Iterationsschleife
for ($i = 0; $i < $anzahlDurchlaeufe; $i++) {
    // Aktuelle Zeit
    $jetzt = new DateTime();

    // Lese die Datei und suche nach den Werten
    $daten = file_get_contents($dateiPfad);
    if (preg_match('/solarflow-hub\/ME419cfa\/telemetry\/solarInputPower: (\d+)/', $daten, $matches1) &&
        preg_match('/solarflow-hub\/ME419cfa\/telemetry\/outputPackPower: (\d+)/', $daten, $matches2) &&
        preg_match('/solarflow-hub\/ME419cfa\/telemetry\/outputHomePower: (\d+)/', $daten, $matches3)) {
        $solarInputPower = $matches1[1];
        $outputPackPower = $matches2[1];
        $outputHomePower = $matches3[1];

        // Daten als Array vorbereiten
        $datenArray = [
            'zeitstempel' => $jetzt->format('Y-m-d H:i:s'),
            'solarInputPower' => $solarInputPower,
            'outputPackPower' => $outputPackPower,
            'outputHomePower' => $outputHomePower
        ];

        // Bestehende Daten aus der Datei lesen und in ein Array konvertieren
        if (file_exists($zielDateiPfad)) {
            $vorhandeneDaten = json_decode(file_get_contents($zielDateiPfad), true) ?: [];
        } else {
            $vorhandeneDaten = [];
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
        file_put_contents($zielDateiPfad, json_encode(array_values($bereinigteDaten)));

        echo "Werte gespeichert: Solar Input Power = $solarInputPower, Output Pack Power = $outputPackPower, Output Home Power = $outputHomePower\n";
    } else {
        echo "Werte nicht gefunden.\n";
    }

    // Warte 5 Sekunden bis zum nächsten Durchlauf
    if ($i < $anzahlDurchlaeufe - 1) {
        sleep(5);
    }
}
