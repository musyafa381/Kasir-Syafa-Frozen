import 'package:flutter/material.dart';
import '../models/cart_item.dart';
import '../models/product.dart';
import '../models/transaction.dart';
import '../services/api_service.dart';

/// ─────────────────────────────────────────────────────────
/// Cart Provider — Mengelola state keranjang belanja
/// ─────────────────────────────────────────────────────────
class CartProvider extends ChangeNotifier {
  final List<CartItem> _items = [];
  Transaction? _lastTransaction;

  List<CartItem> get items => List.unmodifiable(_items);
  Transaction? get lastTransaction => _lastTransaction;

  int get itemCount => _items.fold(0, (sum, item) => sum + item.quantity);

  double get subtotal => _items.fold(0.0, (sum, item) => sum + item.subtotal);

  bool get isEmpty => _items.isEmpty;

  /// Tambah produk ke keranjang
  void addItem(Product product) {
    final existingIndex = _items.indexWhere((item) => item.product.id == product.id);

    if (existingIndex >= 0) {
      // Produk sudah ada, tambah quantity
      if (_items[existingIndex].quantity < product.stock) {
        _items[existingIndex].quantity++;
        notifyListeners();
      }
    } else {
      // Produk baru
      if (product.stock > 0) {
        _items.add(CartItem(product: product));
        notifyListeners();
      }
    }
  }

  /// Kurangi quantity
  void decrementItem(int productId) {
    final index = _items.indexWhere((item) => item.product.id == productId);
    if (index >= 0) {
      if (_items[index].quantity > 1) {
        _items[index].quantity--;
      } else {
        _items.removeAt(index);
      }
      notifyListeners();
    }
  }

  /// Hapus item dari keranjang
  void removeItem(int productId) {
    _items.removeWhere((item) => item.product.id == productId);
    notifyListeners();
  }

  /// Update quantity langsung
  void updateQuantity(int productId, int newQuantity) {
    final index = _items.indexWhere((item) => item.product.id == productId);
    if (index >= 0) {
      if (newQuantity <= 0) {
        _items.removeAt(index);
      } else if (newQuantity <= _items[index].product.stock) {
        _items[index].quantity = newQuantity;
      }
      notifyListeners();
    }
  }

  /// Kosongkan keranjang
  void clearCart() {
    _items.clear();
    notifyListeners();
  }

  /// Proses checkout via API
  Future<Transaction> checkout({
    required String paymentMethod,
    required double paidAmount,
    int? customerId,
    String? customerName,
    bool isLoyalty = false,
    double discountAmount = 0,
    String? notes,
    String? dueDate,
  }) async {
    final api = ApiService.instance;

    final response = await api.post('/checkout', data: {
      'items': _items.map((item) => item.toCheckoutJson()).toList(),
      'payment_method': paymentMethod,
      'paid_amount': paidAmount,
      'customer_id': customerId,
      'customer_name': customerName ?? 'Walk-in',
      'is_loyalty': isLoyalty,
      'discount_amount': discountAmount,
      'notes': notes,
      'due_date': dueDate,
    });

    final transaction = Transaction.fromJson(
      response.data['data'] as Map<String, dynamic>,
    );

    _lastTransaction = transaction;
    clearCart();
    return transaction;
  }
}
