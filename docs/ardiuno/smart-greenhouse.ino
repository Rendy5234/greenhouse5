// ============================================
// SMART GREENHOUSE - FINAL CODE
// Sensor  : DHT22 + Potensiometer
// Aktuator: Relay Kipas + Relay Pompa + 3 LED
// ============================================

#include "DHT.h"

// ---------- PIN ----------
#define DHTPIN       32   // DHT22 → D15
#define DHTTYPE      DHT22
#define SOIL_PIN     35    // Potensiometer → D2

#define RELAY_KIPAS  26    // Relay kipas → D5
#define RELAY_POMPA  27   // Relay pompa (sanyo) → D18

#define LED_HIJAU    33    // LED Hijau → D4
#define LED_BIRU     25   // LED Biru → D16
#define LED_MERAH    13   // LED Merah → D17

// ---------- AMBANG BATAS ----------
const float TEMP_ON       = 30.0;   // Suhu kipas nyala
const float TEMP_OFF      = 28.0;   // Suhu kipas mati (hysteresis 2°C)
const int   SOIL_KERING   = 2500;   // ADC tanah kering (pompa nyala)
const int   SOIL_BASAH    = 1500;   // ADC tanah basah (pompa mati)

DHT dht(DHTPIN, DHTTYPE);

// State
bool kipasON = false;
bool pompaON = false;

void setup() {
  Serial.begin(115200);
  delay(2000);
  Serial.println("\n=== SMART GREENHOUSE ===");

  pinMode(RELAY_KIPAS, OUTPUT);
  pinMode(RELAY_POMPA, OUTPUT);
  pinMode(LED_MERAH, OUTPUT);
  pinMode(LED_BIRU,  OUTPUT);
  pinMode(LED_HIJAU, OUTPUT);

  // Semua OFF dulu (relay active LOW)
  digitalWrite(RELAY_KIPAS, HIGH);
  digitalWrite(RELAY_POMPA, HIGH);
  digitalWrite(LED_MERAH, LOW);
  digitalWrite(LED_BIRU,  LOW);
  digitalWrite(LED_HIJAU, LOW);

  dht.begin();
  delay(2000);

  Serial.println("SISTEM SIAP\n");
}

void loop() {
  // ---------- BACA SENSOR ----------
  float suhu   = dht.readTemperature();
  float lembab = dht.readHumidity();
  int   soil   = analogRead(SOIL_PIN);

  if (isnan(suhu) || isnan(lembab)) {
    Serial.println("[ERROR] DHT22 gagal!");
    delay(2000);
    return;
  }

  Serial.println("─────────────────────");
  Serial.printf("Suhu   : %.1f C\n", suhu);
  Serial.printf("Lembab : %.1f %%\n", lembab);
  Serial.printf("Tanah  : %d\n", soil);

  // ---------- LOGIKA KIPAS (hysteresis) ----------
  if (suhu > TEMP_ON)       kipasON = true;
  else if (suhu < TEMP_OFF) kipasON = false;
  digitalWrite(RELAY_KIPAS, kipasON ? LOW : HIGH);

  // ---------- LOGIKA POMPA (hysteresis) ----------
  if (soil > SOIL_KERING)      pompaON = true;
  else if (soil < SOIL_BASAH)  pompaON = false;
  digitalWrite(RELAY_POMPA, pompaON ? LOW : HIGH);

  // ---------- LOGIKA LED ----------
  if (!kipasON && !pompaON) {
    // Normal
    digitalWrite(LED_HIJAU, HIGH);
    digitalWrite(LED_MERAH, LOW);
    digitalWrite(LED_BIRU,  LOW);
    Serial.println("Status : NORMAL (LED Hijau)");
  } else if (kipasON && !pompaON) {
    // Kipas saja
    digitalWrite(LED_HIJAU, LOW);
    digitalWrite(LED_MERAH, LOW);
    digitalWrite(LED_BIRU,  HIGH);
    Serial.println("Status : KIPAS ON (LED Biru)");
  } else if (!kipasON && pompaON) {
    // Pompa saja
    digitalWrite(LED_HIJAU, LOW);
    digitalWrite(LED_MERAH, HIGH);
    digitalWrite(LED_BIRU,  LOW);
    Serial.println("Status : POMPA ON (LED Merah)");
  } else {
    // Dua-duanya
    digitalWrite(LED_HIJAU, LOW);
    digitalWrite(LED_MERAH, HIGH);
    digitalWrite(LED_BIRU,  HIGH);
    Serial.println("Status : KIPAS + POMPA ON");
  }

  delay(2000);
}