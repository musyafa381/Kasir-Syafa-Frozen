/// Konfigurasi API untuk koneksi ke Laravel Backend
class ApiConfig {
  ApiConfig._();

  // ── Base URL ───────────────────────────────────────────
  // Untuk Windows Desktop / Web: http://127.0.0.1:8000/api atau localhost
  // Untuk Android Emulator: http://10.0.2.2:8000/api
  // Untuk Device fisik (HP): gunakan IP WiFi komputer (misal http://192.168.1.xxx:8000/api)
  static const String baseUrl = 'http://127.0.0.1:8000/api';

  // Alternatif untuk device fisik (ganti dengan IP komputer Anda):
  // static const String baseUrl = 'http://192.168.1.100:8000/api';

  // ── Timeout ────────────────────────────────────────────
  static const int connectTimeout = 10000; // 10 detik
  static const int receiveTimeout = 15000; // 15 detik

  // ── Storage Keys ───────────────────────────────────────
  static const String tokenKey = 'auth_token';
  static const String userKey = 'auth_user';
}
