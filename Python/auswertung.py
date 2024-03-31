import paho.mqtt.client as mqtt
import pysftp

# MQTT-Broker-Konfiguration
broker_address = "192.168.178.21"

# SFTP-Konfiguration
sftp_host = "xxxx"
sftp_username = "xxxx"
sftp_password = "xxxx"
sftp_remote_path = "xxxx"
cnopts = pysftp.CnOpts()
cnopts.hostkeys = None  # Deaktiviere die Host-Schlüsselüberprüfung

# Dictionary zum Speichern der letzten Nachrichten pro Topic
last_messages = {}

def on_message(client, userdata, message):
    global last_messages
    
    # Nachricht verarbeiten und in Datei schreiben (wird jedes Mal überschrieben)
    with open("data.txt", "w") as f:
        topic = message.topic
        payload = message.payload.decode()
        
        # Speichern des letzten Wertes für jedes Topic
        last_messages[topic] = payload
        
        # Schreibe alle letzten Werte in die Datei
        for topic, value in last_messages.items():
            f.write(f"{topic}: {value}\n")
    
    # SFTP-Verbindung herstellen und Datei übertragen
    with pysftp.Connection(sftp_host, username=sftp_username, password=sftp_password, cnopts=cnopts) as sftp:
        sftp.put("data.txt", sftp_remote_path)

# MQTT-Client initialisieren und Callback-Funktion zuweisen
client = mqtt.Client()
client.on_message = on_message

# Mit dem MQTT-Broker verbinden und Themen abonnieren
client.connect(broker_address)

# Alle Topics abonnieren
client.subscribe("/73bkTV/xxxxxx/properties/report")
client.subscribe("solarflow-hub/xxxxxx/telemetry/solarInputPower")
client.subscribe("solarflow-hub/xxxxxx/telemetry/electricLevel")
client.subscribe("solarflow-hub/xxxxx/telemetry/outputPackPower")
client.subscribe("solarflow-hub/xxxxx/telemetry/packInputPower")
client.subscribe("solarflow-hub/xxxxxx/telemetry/outputHomePower")
client.subscribe("solarflow-hub/xxxxxx/telemetry/outputLimit")
client.subscribe("solarflow-hub/xxxxxx/telemetry/masterSoftVersion")
client.subscribe("solarflow-hub/xxxxxx/telemetry/pass")
client.subscribe("solarflow-hub/xxxxxx/telemetry/batteries/+/socLevel")
client.subscribe("solarflow-hub/xxxxxx/telemetry/batteries/+/totalVol")
client.subscribe("solarflow-hub/xxxxxx/control/#")
client.subscribe("solar/xxxxxxxxx/0/powerdc")
client.subscribe("solar/xxxxxxxxx/+/power")
client.subscribe("solar/xxxxxxxxx/status/producing")
client.subscribe("solar/xxxxxxxxx/status/reachable")
client.subscribe("solar/xxxxxxxxx/status/limit_absolute")
client.subscribe("solar/xxxxxxxxx/status/limit_relative")
client.subscribe("solarflow-hub/+/control/dryRun")
client.subscribe("shellypro3em/status/em:0")

# Auf eingehende Nachrichten warten
client.loop_forever()
