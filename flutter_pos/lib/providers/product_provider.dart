import 'package:flutter/material.dart';
import '../models/product.dart';
import '../models/category.dart';
import '../services/product_service.dart';

/// ─────────────────────────────────────────────────────────
/// Product Provider — Mengelola state daftar produk
/// ─────────────────────────────────────────────────────────
class ProductProvider extends ChangeNotifier {
  final ProductService _productService = ProductService();

  List<Product> _products = [];
  List<Category> _categories = [];
  bool _isLoading = false;
  String? _errorMessage;
  String _searchQuery = '';
  int? _selectedCategoryId;

  List<Product> get products => _products;
  List<Category> get categories => _categories;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  String get searchQuery => _searchQuery;
  int? get selectedCategoryId => _selectedCategoryId;

  /// Fetch produk dari API
  Future<void> fetchProducts() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _products = await _productService.getProducts(
        search: _searchQuery.isNotEmpty ? _searchQuery : null,
        categoryId: _selectedCategoryId,
      );
      _errorMessage = null;
    } catch (e) {
      _errorMessage = 'Gagal memuat produk';
    }

    _isLoading = false;
    notifyListeners();
  }

  /// Fetch kategori dari API
  Future<void> fetchCategories() async {
    try {
      _categories = await _productService.getCategories();
      notifyListeners();
    } catch (_) {
      // Silent fail untuk kategori
    }
  }

  /// Set pencarian
  void setSearchQuery(String query) {
    _searchQuery = query;
    fetchProducts();
  }

  /// Set filter kategori
  void setCategory(int? categoryId) {
    _selectedCategoryId = categoryId;
    fetchProducts();
  }

  /// Refresh semua data
  Future<void> refreshAll() async {
    await Future.wait([
      fetchProducts(),
      fetchCategories(),
    ]);
  }
}
