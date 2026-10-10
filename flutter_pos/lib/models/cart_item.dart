import 'product.dart';

/// Model Item Keranjang — untuk state management lokal di Flutter
class CartItem {
  final Product product;
  int quantity;

  CartItem({
    required this.product,
    this.quantity = 1,
  });

  double get subtotal => product.sellingPrice * quantity;

  /// Untuk dikirim ke API checkout
  Map<String, dynamic> toCheckoutJson() {
    return {
      'product_id': product.id,
      'quantity': quantity,
    };
  }
}
