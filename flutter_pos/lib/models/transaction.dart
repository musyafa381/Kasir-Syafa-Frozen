import 'package:intl/intl.dart';

/// Model Transaksi — response dari API /api/checkout atau lokal
class Transaction {
  final int id;
  final String invoiceNumber;
  final String customerName;
  final bool isLoyalty;
  final double subtotal;
  final double discountAmount;
  final String? discountName;
  final double taxAmount;
  final String? taxName;
  final double taxRate;
  final double grandTotal;
  final double paidAmount;
  final double changeAmount;
  final String paymentMethod;
  final String paymentStatus;
  final String cashierName;
  final String? notes;
  final List<TransactionItem> items;
  final DateTime createdAt;

  Transaction({
    required this.id,
    required this.invoiceNumber,
    required this.customerName,
    this.isLoyalty = false,
    required this.subtotal,
    required this.discountAmount,
    this.discountName,
    this.taxAmount = 0.0,
    this.taxName,
    this.taxRate = 0.0,
    required this.grandTotal,
    required this.paidAmount,
    required this.changeAmount,
    required this.paymentMethod,
    required this.paymentStatus,
    required this.cashierName,
    this.notes,
    required this.items,
    required this.createdAt,
  });

  factory Transaction.fromJson(Map<String, dynamic> json) {
    return Transaction(
      id: json['id'] as int? ?? 0,
      invoiceNumber: json['invoice_number'] as String? ?? 'INV-${DateTime.now().millisecondsSinceEpoch}',
      customerName: json['customer_name'] as String? ?? 'Pelanggan Umum',
      isLoyalty: json['is_loyalty'] as bool? ?? false,
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0.0,
      discountAmount: (json['discount_amount'] as num?)?.toDouble() ?? 0.0,
      discountName: json['discount_name'] as String?,
      taxAmount: (json['tax_amount'] as num?)?.toDouble() ?? 0.0,
      taxName: json['tax_name'] as String?,
      taxRate: (json['tax_rate'] as num?)?.toDouble() ?? 0.0,
      grandTotal: (json['grand_total'] as num?)?.toDouble() ?? 0.0,
      paidAmount: (json['paid_amount'] as num?)?.toDouble() ?? 0.0,
      changeAmount: (json['change_amount'] as num?)?.toDouble() ?? 0.0,
      paymentMethod: json['payment_method'] as String? ?? 'cash',
      paymentStatus: json['payment_status'] as String? ?? 'paid',
      cashierName: json['cashier_name'] as String? ?? 'Owner Syafa Frozen',
      notes: json['notes'] as String?,
      items: (json['items'] as List<dynamic>?)
              ?.map((e) => TransactionItem.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      createdAt: json['created_at'] != null
          ? DateTime.parse(json['created_at'] as String).toLocal()
          : DateTime.now(),
    );
  }

  /// Generate WhatsApp receipt plain-text format matching web version
  String toReceiptText({String? storeName, String? storeAddress, String? storePhone, String? footerLink}) {
    final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    final dateStr = DateFormat('dd/MM/yyyy HH:mm', 'id_ID').format(createdAt);
    final sName = storeName ?? 'SYAFA FROZEN FOOD';
    
    final lines = <String>[];
    lines.add('*NOTA PEMBELIAN - $sName*');
    lines.add('Pusat Frozen Food Berkualitas & Terpercaya');
    lines.add('----------------------------------------');
    lines.add('*No. Invoice:* $invoiceNumber');
    lines.add('*Waktu:* $dateStr WIB');
    lines.add('*Kasir:* ${cashierName.isNotEmpty ? cashierName : "Kasir"}');
    lines.add('*Pelanggan:* $customerName ${isLoyalty ? "(Member)" : ""}');
    lines.add('----------------------------------------');
    lines.add('*Rincian Belanja:*');

    for (final item in items) {
      lines.add('• *${item.productName}*');
      lines.add('  ${item.quantity} x ${currency.format(item.price)} = ${currency.format(item.subtotal)}');
    }

    lines.add('----------------------------------------');
    lines.add('*Total Belanja:* ${currency.format(grandTotal)}');
    lines.add('*Bayar (${paymentMethod.toUpperCase()}):* ${currency.format(paidAmount)}');
    lines.add('*Kembalian:* ${currency.format(changeAmount)}');
    lines.add('----------------------------------------');
    if (footerLink != null && footerLink.isNotEmpty) {
      lines.add('Kritik & Saran: $footerLink');
    }
    lines.add('Terima kasih telah berbelanja di Syafa Frozen Food!');

    return lines.join('\n');
  }
}

class TransactionItem {
  final String productName;
  final int quantity;
  final double price;
  final double subtotal;

  TransactionItem({
    required this.productName,
    required this.quantity,
    required this.price,
    required this.subtotal,
  });

  factory TransactionItem.fromJson(Map<String, dynamic> json) {
    return TransactionItem(
      productName: json['product_name'] as String? ?? '',
      quantity: json['quantity'] as int? ?? 1,
      price: (json['price'] as num?)?.toDouble() ?? (json['unit_price'] as num?)?.toDouble() ?? 0.0,
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0.0,
    );
  }
}
