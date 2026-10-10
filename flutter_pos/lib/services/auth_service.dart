import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';
import 'api_service.dart';

/// ─────────────────────────────────────────────────────────
/// Auth Service — Login, Logout, Token Management
/// ─────────────────────────────────────────────────────────
class AuthService {
  final ApiService _api = ApiService.instance;

  /// Login → simpan token ke SharedPreferences
  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await _api.post('/login', data: {
      'email': email,
      'password': password,
    });

    final data = response.data as Map<String, dynamic>;
    final prefs = await SharedPreferences.getInstance();

    // Simpan token
    await prefs.setString(ApiConfig.tokenKey, data['token'] as String);
    // Simpan user data
    await prefs.setString(ApiConfig.userKey, jsonEncode(data['user']));

    return data;
  }

  /// Logout → hapus token
  Future<void> logout() async {
    try {
      await _api.post('/logout');
    } catch (_) {
      // Tetap clear token meskipun API gagal
    }

    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(ApiConfig.tokenKey);
    await prefs.remove(ApiConfig.userKey);
  }

  /// Cek apakah sudah login (ada token tersimpan)
  Future<bool> isLoggedIn() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.containsKey(ApiConfig.tokenKey);
  }

  /// Ambil data user tersimpan
  Future<Map<String, dynamic>?> getSavedUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(ApiConfig.userKey);
    if (userJson != null) {
      return jsonDecode(userJson) as Map<String, dynamic>;
    }
    return null;
  }

  /// Ambil token tersimpan
  Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(ApiConfig.tokenKey);
  }
}
