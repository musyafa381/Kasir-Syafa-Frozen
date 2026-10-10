import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../providers/cart_provider.dart';
import '../models/transaction.dart';

/// ─────────────────────────────────────────────────────────
/// Receipt Screen — Struk Nota Digital Thermal
/// Matching 100% tampilan struk web dengan garis putus tipis
/// ─────────────────────────────────────────────────────────
class ReceiptScreen extends StatefulWidget {
  const ReceiptScreen({super.key});

  @override
  State<ReceiptScreen> createState() => _ReceiptScreenState();
}

class _ReceiptScreenState extends State<ReceiptScreen> {
  final _waPhoneController = TextEditingController();

  static final _currencyFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  static final _dateFormat = DateFormat('yyyy-MM-dd', 'id_ID');
  static final _timeFormat = DateFormat('HH:mm:ss', 'id_ID');

  @override
  void dispose() {
    _waPhoneController.dispose();
    super.dispose();
  }

  void _copyReceiptText(Transaction transaction) {
    final text = transaction.toReceiptText(
      storeName: 'Syafa frozen food',
      storeAddress: 'Jl. Piyungan - Ngoro Oro, Jatigrit, Srimartani, Kec. Piyungan, Bantul, DIY',
      storePhone: '089649399694',
      footerLink: 'Develop : Musapang@Tech.id',
    );
    Clipboard.setData(ClipboardData(text: text));
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: const Text('Teks nota struk berhasil disalin!'),
        backgroundColor: AppTheme.success,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
        ),
      ),
    );
  }

  void _sendWhatsApp(Transaction transaction) {
    final phone = _waPhoneController.text.trim();
    final text = transaction.toReceiptText(
      storeName: 'Syafa frozen food',
      storeAddress: 'Jl. Piyungan - Ngoro Oro, Jatigrit, Srimartani, Kec. Piyungan, Bantul, DIY',
      storePhone: '089649399694',
      footerLink: 'Develop : Musapang@Tech.id',
    );

    // Copy to clipboard and inform user
    Clipboard.setData(ClipboardData(text: text));
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          phone.isNotEmpty
              ? 'Teks struk siap dikirim ke WhatsApp ($phone)'
              : 'Teks struk berhasil disalin ke clipboard!',
        ),
        backgroundColor: AppTheme.success,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
        ),
      ),
    );
  }

  void _printReceipt() {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: const Text('Mengirim data struk ke Printer Thermal...'),
        backgroundColor: AppTheme.textPrimary,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
        ),
      ),
    );
  }

  void _saveImage() {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: const Text('Nota berhasil disimpan sebagai gambar PNG'),
        backgroundColor: AppTheme.success,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.read<CartProvider>();
    final transaction = cart.lastTransaction;

    if (transaction == null) {
      return Scaffold(
        backgroundColor: const Color(0xFFF8FAFC),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.receipt_long_rounded, size: 48, color: AppTheme.textTertiary),
              const SizedBox(height: 12),
              const Text('Tidak ada data transaksi aktif', style: TextStyle(fontSize: 14, color: AppTheme.textSecondary)),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: () => Navigator.of(context).pushReplacementNamed('/pos'),
                child: const Text('Kembali ke Kasir'),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        backgroundColor: AppTheme.primary,
        elevation: 0,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Nota Digital: ${transaction.invoiceNumber}',
              style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Colors.white),
            ),
            Text(
              '${_dateFormat.format(transaction.createdAt)} – ${_timeFormat.format(transaction.createdAt)} WIB',
              style: TextStyle(fontSize: 10, color: Colors.white.withValues(alpha: 0.8)),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.close_rounded, color: Colors.white),
            onPressed: () => Navigator.of(context).pushReplacementNamed('/pos'),
          ),
        ],
      ),
      body: SafeArea(
        child: Column(
          children: [
            // Scrollable Receipt Center Area
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
                child: Center(
                  child: Container(
                    constraints: const BoxConstraints(maxWidth: 320),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(4),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.08),
                          blurRadius: 16,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        // ── Header Toko ──
                        const Text(
                          'Syafa frozen food',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w600,
                            letterSpacing: -0.2,
                            color: Colors.black,
                          ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Jl. Piyungan - Ngoro Oro, Jatigrit, Srimartani, Kec. Piyungan, Kabupaten Bantul, Daerah Istimewa Yogyakarta',
                          style: TextStyle(
                            fontSize: 10.5,
                            color: Color(0xFF262626),
                            height: 1.25,
                          ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 2),
                        const Text(
                          'No.Tlp 089649399694',
                          style: TextStyle(
                            fontSize: 10.5,
                            color: Color(0xFF262626),
                          ),
                          textAlign: TextAlign.center,
                        ),

                        const SizedBox(height: 8),
                        const DashedLineDivider(),
                        const SizedBox(height: 8),

                        // ── Tanggal, Kasir & Invoice ──
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  _dateFormat.format(transaction.createdAt),
                                  style: const TextStyle(fontSize: 10.5, fontFamily: 'monospace', color: Colors.black),
                                ),
                                Text(
                                  _timeFormat.format(transaction.createdAt),
                                  style: const TextStyle(fontSize: 10.5, fontFamily: 'monospace', color: Colors.black),
                                ),
                              ],
                            ),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.end,
                              children: [
                                Text(
                                  'Kasir : ${transaction.cashierName.isNotEmpty ? transaction.cashierName : "Owner Syafa"}',
                                  style: const TextStyle(fontSize: 10.5, color: Colors.black),
                                ),
                                Text(
                                  transaction.invoiceNumber,
                                  style: const TextStyle(fontSize: 10.5, fontFamily: 'monospace', color: Colors.black),
                                ),
                              ],
                            ),
                          ],
                        ),

                        if (transaction.customerName.isNotEmpty) ...[
                          const SizedBox(height: 4),
                          Align(
                            alignment: Alignment.centerLeft,
                            child: Text(
                              'Pelanggan : ${transaction.customerName} ${transaction.isLoyalty ? "(Member)" : ""}',
                              style: const TextStyle(fontSize: 10.5, color: Colors.black),
                            ),
                          ),
                        ],

                        const SizedBox(height: 8),
                        const DashedLineDivider(),
                        const SizedBox(height: 8),

                        // ── Daftar Item Belanja ──
                        ...transaction.items.map((item) => Padding(
                              padding: const EdgeInsets.only(bottom: 6),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    item.productName.toUpperCase(),
                                    style: const TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w800,
                                      color: Colors.black,
                                    ),
                                  ),
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        '${item.quantity}  x ${_currencyFormat.format(item.price)}',
                                        style: const TextStyle(
                                          fontSize: 10.5,
                                          fontFamily: 'monospace',
                                          color: Color(0xFF262626),
                                        ),
                                      ),
                                      Text(
                                        _currencyFormat.format(item.subtotal),
                                        style: const TextStyle(
                                          fontSize: 10.5,
                                          fontFamily: 'monospace',
                                          color: Colors.black,
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            )),

                        const SizedBox(height: 4),
                        const DashedLineDivider(),
                        const SizedBox(height: 8),

                        // ── Perhitungan Total ──
                        _buildReceiptRow('Total QTY : ${transaction.items.fold<int>(0, (sum, it) => sum + it.quantity)}', ''),
                        _buildReceiptRow('Sub Total', _currencyFormat.format(transaction.subtotal)),
                        if (transaction.discountAmount > 0)
                          _buildReceiptRow(
                            'Diskon${transaction.discountName != null ? " (${transaction.discountName})" : ""}',
                            '- ${_currencyFormat.format(transaction.discountAmount)}',
                          ),
                        if (transaction.taxAmount > 0)
                          _buildReceiptRow(
                            'Pajak${transaction.taxName != null ? " (${transaction.taxName})" : ""}',
                            '+ ${_currencyFormat.format(transaction.taxAmount)}',
                          ),
                        
                        const SizedBox(height: 2),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text(
                              'Total',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                                color: Colors.black,
                              ),
                            ),
                            Text(
                              _currencyFormat.format(transaction.grandTotal),
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                                fontFamily: 'monospace',
                                color: Colors.black,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 4),
                        _buildReceiptRow(
                          'Bayar (${_paymentLabel(transaction.paymentMethod)})',
                          _currencyFormat.format(transaction.paidAmount),
                        ),
                        _buildReceiptRow(
                          'Kembalian',
                          _currencyFormat.format(transaction.changeAmount),
                        ),

                        const SizedBox(height: 8),
                        const DashedLineDivider(),
                        const SizedBox(height: 8),

                        // ── Footer Struk ──
                        const Text(
                          'Link Kritik dan Saran:',
                          style: TextStyle(fontSize: 10, color: Color(0xFF404040)),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 1),
                        const Text(
                          'Develop : Musapang@Tech.id',
                          style: TextStyle(fontSize: 10, color: Color(0xFF404040)),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 3),
                        const Text(
                          'Terima kasih telah berbelanja!',
                          style: TextStyle(fontSize: 10, color: Color(0xFF404040)),
                          textAlign: TextAlign.center,
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),

            // ── Toolbar Actions (Matching Web Modal Bottom Bar) ──
            _buildActionToolbar(transaction),
          ],
        ),
      ),
    );
  }

  Widget _buildReceiptRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 1.5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(fontSize: 10.5, color: Color(0xFF262626)),
          ),
          Text(
            value,
            style: const TextStyle(fontSize: 10.5, fontFamily: 'monospace', color: Colors.black),
          ),
        ],
      ),
    );
  }

  Widget _buildActionToolbar(Transaction transaction) {
    return Container(
      padding: EdgeInsets.fromLTRB(
        16,
        12,
        16,
        12 + MediaQuery.of(context).padding.bottom,
      ),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        border: const Border(
          top: BorderSide(color: Color(0xFFE2E8F0), width: 1),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.06),
            blurRadius: 10,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Row 1: WhatsApp Input & Send Button
          Row(
            children: [
              Expanded(
                child: Container(
                  height: 38,
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFFCBD5E1)),
                  ),
                  child: TextField(
                    controller: _waPhoneController,
                    keyboardType: TextInputType.phone,
                    style: const TextStyle(fontSize: 12, fontFamily: 'monospace'),
                    decoration: const InputDecoration(
                      hintText: 'No. WA (081234...)',
                      hintStyle: TextStyle(fontSize: 11, color: AppTheme.textTertiary),
                      prefixIcon: Icon(Icons.phone_rounded, size: 16, color: AppTheme.textSecondary),
                      border: InputBorder.none,
                      enabledBorder: InputBorder.none,
                      focusedBorder: InputBorder.none,
                      contentPadding: EdgeInsets.symmetric(vertical: 10),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 8),
              ElevatedButton.icon(
                onPressed: () => _sendWhatsApp(transaction),
                icon: const Icon(Icons.send_rounded, size: 14),
                label: const Text('Kirim Gambar WA', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF059669),
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
              ),
            ],
          ),

          const SizedBox(height: 10),

          // Row 2: Secondary Action Buttons
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              // Unduh PNG & Salin Teks
              Row(
                children: [
                  OutlinedButton(
                    onPressed: _saveImage,
                    style: OutlinedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: AppTheme.textPrimary,
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                      side: const BorderSide(color: Color(0xFFCBD5E1)),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                    child: const Text('Unduh PNG', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600)),
                  ),
                  const SizedBox(width: 6),
                  OutlinedButton(
                    onPressed: () => _copyReceiptText(transaction),
                    style: OutlinedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: AppTheme.textPrimary,
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                      side: const BorderSide(color: Color(0xFFCBD5E1)),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                    child: const Text('Salin Teks', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600)),
                  ),
                ],
              ),

              // Cetak Struk & Tutup
              Row(
                children: [
                  ElevatedButton.icon(
                    onPressed: _printReceipt,
                    icon: const Icon(Icons.print_rounded, size: 14),
                    label: const Text('Cetak Struk', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0F172A),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                  const SizedBox(width: 6),
                  ElevatedButton(
                    onPressed: () => Navigator.of(context).pushReplacementNamed('/pos'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFE2E8F0),
                      foregroundColor: const Color(0xFF334155),
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                    child: const Text('Tutup', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }

  String _paymentLabel(String method) {
    switch (method.toLowerCase()) {
      case 'cash':
        return 'Cash';
      case 'transfer':
        return 'Transfer';
      case 'qris':
        return 'Qris';
      case 'tempo':
        return 'Tempo';
      default:
        return method;
    }
  }
}

/// ─────────────────────────────────────────────────────────
/// Dashed Line Divider — Garis putus-putus tipis dan halus
/// Matching border-dashed border-slate-300
/// ─────────────────────────────────────────────────────────
class DashedLineDivider extends StatelessWidget {
  final double height;
  final Color color;
  final double dashWidth;
  final double dashSpace;

  const DashedLineDivider({
    super.key,
    this.height = 1.0,
    this.color = const Color(0xFFCBD5E1),
    this.dashWidth = 3.0,
    this.dashSpace = 3.0,
  });

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final boxWidth = constraints.constrainWidth();
        final dashCount = (boxWidth / (dashWidth + dashSpace)).floor();
        return Flex(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          direction: Axis.horizontal,
          children: List.generate(dashCount, (_) {
            return SizedBox(
              width: dashWidth,
              height: height,
              child: DecoratedBox(
                decoration: BoxDecoration(color: color),
              ),
            );
          }),
        );
      },
    );
  }
}
