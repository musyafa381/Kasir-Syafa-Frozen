import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import '../services/auth_service.dart';

/// ─────────────────────────────────────────────────────────
/// Auth Provider — Mengelola state autentikasi
/// ─────────────────────────────────────────────────────────
class AuthProvider extends ChangeNotifier {
  final AuthService _authService = AuthService();

  bool _isLoading = false;
  bool _isAuthenticated = false;
  String? _userName;
  String? _userRole;
  String? _errorMessage;

  bool get isLoading => _isLoading;
  bool get isAuthenticated => _isAuthenticated;
  String? get userName => _userName;
  String? get userRole => _userRole;
  String? get errorMessage => _errorMessage;

  /// Cek status login saat app dimulai
  Future<void> checkAuthStatus() async {
    _isLoading = true;
    notifyListeners();

    try {
      final isLoggedIn = await _authService.isLoggedIn();
      if (isLoggedIn) {
        final user = await _authService.getSavedUser();
        if (user != null) {
          _isAuthenticated = true;
          _userName = user['name'] as String?;
          _userRole = user['role'] as String?;
        }
      }
    } catch (_) {
      _isAuthenticated = false;
    }

    _isLoading = false;
    notifyListeners();
  }

  /// Login
  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final data = await _authService.login(email, password);
      final user = data['user'] as Map<String, dynamic>;

      _isAuthenticated = true;
      _userName = user['name'] as String?;
      _userRole = user['role'] as String?;
      _errorMessage = null;

      _isLoading = false;
      notifyListeners();
      return true;
    } on DioException catch (e) {
      if (e.response?.statusCode == 422) {
        final errors = e.response?.data['errors'] as Map<String, dynamic>?;
        if (errors != null && errors.containsKey('email')) {
          _errorMessage = (errors['email'] as List).first as String;
        } else {
          _errorMessage = e.response?.data['message'] as String? ?? 'Login gagal';
        }
      } else if (e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.connectionError) {
        _errorMessage = 'Tidak dapat terhubung ke server';
      } else {
        _errorMessage = 'Terjadi kesalahan. Coba lagi.';
      }

      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Terjadi kesalahan. Coba lagi.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  /// Logout
  Future<void> logout() async {
    _isLoading = true;
    notifyListeners();

    await _authService.logout();

    _isAuthenticated = false;
    _userName = null;
    _userRole = null;
    _errorMessage = null;
    _isLoading = false;
    notifyListeners();
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
