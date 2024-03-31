<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Werte aus shellypro3em</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <link rel="stylesheet" href="darksite.css"/>
    <link rel="stylesheet" href="addfragen.css"/>
</head>
<body>
<?php
// Daten aus der Datei auslesen
$file_content = file_get_contents('data.txt');

// JSON-Daten aus der Datei extrahieren
$pattern = '/shellypro3em\/status\/em:0: (.*)/';
preg_match($pattern, $file_content, $matches);

// Überprüfen, ob JSON-Daten gefunden wurden
if (isset($matches[1])) {
    // JSON-Daten dekodieren und in ein assoziatives Array speichern
    $json_data = json_decode($matches[1], true);

    // Variablen mit den extrahierten Daten erstellen
    $a_act_power = $json_data['a_act_power'];
    $b_act_power = $json_data['b_act_power'];
    $c_act_power = $json_data['c_act_power'];
    $total_act_power = $json_data['total_act_power'];

    // HTML-Tabelle erstellen und Daten einfügen
    echo "<table border='1'>";
    echo "<tr><th>Phase</th><th>Leistung</th></tr>";
    echo "<tr><td>Phase A</td><td>$a_act_power</td></tr>";
    echo "<tr><td>Phase B</td><td>$b_act_power</td></tr>";
    echo "<tr><td>Phase C</td><td>$c_act_power</td></tr>";
    echo "<tr><td>Gesamt</td><td>$total_act_power</td></tr>";
    echo "</table>";

} else {
    echo "Keine JSON-Daten gefunden.\n";
}
?>
<div class="chart-container2">
    <canvas id="verbrauch-chart-container"></canvas>	
	
<div class="chart-container">
    <canvas id="solarInputChart"></canvas>
</div>
<div class="chart-container">
    <canvas id="outputPackChart"></canvas>
</div>
<div class="chart-container">
    <canvas id="outputHomeChart"></canvas>
</div>

<div class="chart-container" style="margin-top: 20px;">
    <canvas id="akku-chart-container"></canvas>
</div>
<div class="chart-container" style="margin-top: 20px;">
    <canvas id="prozent-chart-container"></canvas>
</div>
<script>
    // Laden der JSON-Daten aus der Datei
    fetch('gespeicherteWerte.json')
    .then(response => response.json())
    .then(jsonData => {
        // Solar Input Chart
        var solarInputData = {
            labels: [],
            datasets: [{
                label: 'Solar Input Power',
                backgroundColor: 'rgba(255, 165, 0, 0.4)',
                borderColor: 'rgba(255, 165, 0, 1)',
                borderWidth: 2,
                data: [],
                pointRadius: 0, // Punkte ausblenden
                fill: true // Bereich unter der Linie füllen
            }]
        };

        // Output Pack Chart
        var outputPackData = {
            labels: [],
            datasets: [{
                label: 'Output Pack Power',
       	        backgroundColor: 'rgba(0, 255, 22, 0.2)',
                borderColor: 'rgba(0, 255, 22, 0.2)',
                borderWidth: 2,
                data: [],
                pointRadius: 0, // Punkte ausblenden
                fill: true // Bereich unter der Linie füllen
            }]
        };

        // Output Home Chart
        var outputHomeData = {
            labels: [],
            datasets: [{
                label: 'Output Home Power',
                backgroundColor: 'rgba(192, 192, 224, 0.4)', // Blau
                borderColor: 'rgba(98, 158, 214, 1)',
                borderWidth: 2,
                data: [],
                pointRadius: 0, // Punkte ausblenden
                fill: true // Bereich unter der Linie füllen
            }]
        };

        // Daten aus JSON extrahieren
        jsonData.forEach(function(item) {
            solarInputData.labels.push(item.zeitstempel);
            solarInputData.datasets[0].data.push(item.solarInputPower);

            outputPackData.labels.push(item.zeitstempel);
            outputPackData.datasets[0].data.push(item.outputPackPower);

            outputHomeData.labels.push(item.zeitstempel);
            outputHomeData.datasets[0].data.push(item.outputHomePower);
        });

        // Solar Input Chart
        var solarInputCtx = document.getElementById('solarInputChart').getContext('2d');
        var solarInputChart = new Chart(solarInputCtx, {
            type: 'line',
            data: solarInputData,
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute',
                            displayFormats: {
                                minute: 'HH:mm'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Output Pack Chart
        var outputPackCtx = document.getElementById('outputPackChart').getContext('2d');
        var outputPackChart = new Chart(outputPackCtx, {
            type: 'line',
            data: outputPackData,
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute',
                            displayFormats: {
                                minute: 'HH:mm'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Output Home Chart
        var outputHomeCtx = document.getElementById('outputHomeChart').getContext('2d');
        var outputHomeChart = new Chart(outputHomeCtx, {
            type: 'line',
            data: outputHomeData,
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute',
                            displayFormats: {
                                minute: 'HH:mm'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Setzen des Stils für alle Diagramme
        Chart.defaults.borderColor = 'rgba(75, 192, 192, 1)';
        Chart.defaults.borderWidth = 2;
        Chart.defaults.borderRadius = 7;
        Chart.defaults.borderSkipped = 'bottom';
    })
    .catch(error => console.error('Fehler beim Laden der Daten:', error));

// Akku Chart
var akkuCtx = document.getElementById('akku-chart-container').getContext('2d'); // Korrekte ID für das Canvas-Element
var akkuChart = new Chart(akkuCtx, {
    type: 'bar',
    data: {
        labels: ['Akku-Temperatur'], // Label für die X-Achse
        datasets: [{
            label: 'Akku-Temperatur',
            data: [],
            backgroundColor: 'rgba(0, 255, 22, 0.4)',
            borderColor: 'rgba(0, 255, 22, 0.4)',
            borderRadius: 7,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: true // Ausblenden der Legende
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' °C'; // Zeige den Wert über der Säule als Prozentzahl
                    }
                }
            }
        }
    }
});

// Funktion zum Laden und Aktualisieren der Daten
function updateTEMPLevel() {
    fetch('data.txt')
        .then(response => response.text())
        .then(text => {
            const lines = text.split('\n');
            for (const line of lines) {
                if (line.includes('solarflow-hub/ME419cfa/telemetry/batteries/CO4HLHALKP01325/maxTemp:')) {
                    const tempString = line.split(': ')[1]; // Temperaturzeichenfolge extrahieren
                    const tempValue = parseInt(tempString.substring(0, 2)); // Die ersten beiden Zeichen extrahieren und in eine Zahl umwandeln
                    akkuChart.data.datasets[0].data = [tempValue]; // Korrekter Zugriff auf das Chart-Objekt
                    akkuChart.update();
                    break; // Nur den ersten gefundenen Temperaturwert verwenden
                }
            }
        })
        .catch(error => {
            console.error('Fehler beim Laden der Daten:', error);
        });
}

// Initialisierung
updateTEMPLevel();

// Prozent Chart
var prozentCtx = document.getElementById('prozent-chart-container').getContext('2d');
var prozentChart = new Chart(prozentCtx, {
    type: 'bar',
    data: {
        labels: ['State of Charge'],
        datasets: [{
            label: 'State of Charge',
            data: [],
            backgroundColor: 'rgba(0, 255, 22, 0.4)',
            borderColor: 'rgba(0, 255, 22, 0.4)',
            borderRadius: 7, // Hier wird der borderRadius direkt im Dataset verwendet
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        },
        plugins: {
            legend: {
                display: true
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + '%';
                    }
                }
            }
        }
    }
});

// Funktion zum Laden und Aktualisieren der Daten
function updateSOCLevel() {
    fetch('data.txt')
        .then(response => response.text())
        .then(text => {
            const lines = text.split('\n');
            for (const line of lines) {
                if (line.includes('solarflow-hub/ME419cfa/telemetry/batteries/CO4HLHALKP01325/socLevel:')) {
                    const tempString = line.split(': ')[1]; // Temperaturzeichenfolge extrahieren
                    const tempValue = parseInt(tempString.substring(0, 2)); // Die ersten beiden Zeichen extrahieren und in eine Zahl umwandeln
                    prozentChart.data.datasets[0].data = [tempValue]; // Korrekter Zugriff auf das Chart-Objekt
                    prozentChart.update();
                    break; // Nur den ersten gefundenen Temperaturwert verwenden
                }
            }
        })
        .catch(error => {
            console.error('Fehler beim Laden der Daten:', error);
        });
}

// Initialisierung
updateSOCLevel();

// Stromverbrauchs-Chart (Liniendiagramm)
var verbrauchCtx = document.getElementById('verbrauch-chart-container').getContext('2d');
var verbrauchChart = new Chart(verbrauchCtx, {
    type: 'line',
    data: {
        labels: [], // Zeitstempel als Labels
        datasets: [{
            label: 'Stromverbrauch in Watt',
            data: [],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            tension: 0.1, // Macht die Linie etwas glatter
            fill: true, // Füllt den Bereich unter dem Graphen
            pointRadius: 0 // Entfernt die Punkte vom Graphen
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Verbrauch in Watt'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Zeitstempel'
                },
                type: 'time',
                time: {
                    unit: 'hour',
                    tooltipFormat: 'yyyy-MM-dd HH:mm:ss',
                    displayFormats: {
                        hour: 'MMM d, HH:mm'
                    }
                },
                ticks: {
                    autoSkip: true,
                    maxTicksLimit: 15
                }
            }
        },
        plugins: {
            legend: {
                display: true
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' W';
                    }
                }
            }
        }
    }
});

// Funktion zum Laden und Aktualisieren der Daten
function updateStromverbrauch() {
    fetch('shelly.json')
        .then(response => response.json())
        .then(data => {
            // Aktuelle Zeit und Zeit vor 6 Stunden berechnen
            const jetzt = new Date();
            const sechsStundenZurueck = new Date(jetzt.getTime() - (6 * 60 * 60 * 1000));

            // Daten filtern, um nur die letzten 6 Stunden zu erhalten
            const gefilterteDaten = data.filter(d => {
                const zeit = new Date(d.zeitstempel);
                return zeit >= sechsStundenZurueck;
            });

            const zeitstempel = gefilterteDaten.map(d => d.zeitstempel);
            const verbrauchsdaten = gefilterteDaten.map(d => d.total_act_power);
            
            verbrauchChart.data.labels = zeitstempel;
            verbrauchChart.data.datasets[0].data = verbrauchsdaten;
            
            verbrauchChart.update();
        })
        .catch(error => {
            console.error('Fehler beim Laden der Daten:', error);
        });
}

// Initialisierung
updateStromverbrauch();



</script>
</body>
</html>
