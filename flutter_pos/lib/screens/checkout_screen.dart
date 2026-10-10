import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../providers/cart_provider.dart';

/// ─────────────────────────────────────────────────────────
/// Checkout Screen — Modal Pembayaran matching Web POS
/// ─────────────────────────────────────────────────────────
class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen({super.key});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final _paidController = TextEditingController();
  final _customerNameController = TextEditingController(text: 'Pelanggan Umum');
  final _customerPhoneController = TextEditingController();
  
  String _paymentMethod = 'cash';
  bool _isProcessing = false;

  static final _currencyFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  double get _paidAmount {
    final text = _paidController.text.replaceAll(RegExp(r'[^0-9]'), '');
    return text.isEmpty ? 0 : double.parse(text);
  }

  @override
  void initState() {
    super.initState();
    // Default nominal bayar diisi uang pas
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final cart = context.read<CartProvider>();
      _paidController.text = cart.subtotal.toInt().toString();
      setState(() {});
    });
  }

  @override
  void dispose() {
    _paidController.dispose();
    _customerNameController.dispose();
    _customerPhoneController.dispose();
    super.dispose();
  }

  Future<void> _handleCheckout() async {
    final cart = context.read<CartProvider>();

    if (_paymentMethod != 'tempo' && _paidAmount < cart.subtotal) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text('Nominal uang bayar kurang dari total belanja!'),
          backgroundColor: AppTheme.danger,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
          ),
        ),
      );
      return;
    }

    setState(() => _isProcessing = true);

    try {
      await cart.checkout(
        paymentMethod: _paymentMethod,
        paidAmount: _paymentMethod == 'tempo' ? _paidAmount : (_paidAmount > 0 ? _paidAmount : cart.subtotal),
      );

      if (mounted) {
        Navigator.of(context).pushReplacementNamed('/receipt');
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isProcessing = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Checkout gagal: $e'),
            backgroundColor: AppTheme.danger,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
            ),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: AppTheme.primary,
        title: const Text(
          'Pembayaran Transaksi',
          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white),
        ),
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 18, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Consumer<CartProvider>(
        builder: (context, cart, _) {
          return Column(
            children: [
              Expanded(
                child: ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    // ── Card 1: Data Pelanggan ──
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Informasi Pelanggan',
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                          const SizedBox(height: 12),
                          TextField(
                            controller: _customerNameController,
                            decoration: InputDecoration(
                              labelText: 'Nama Pelanggan',
                              prefixIcon: const Icon(Icons.person_outline_rounded, size: 20),
                              hintText: 'Pelanggan Umum',
                            ),
                          ),
                          const SizedBox(height: 10),
                          TextField(
                            controller: _customerPhoneController,
                            keyboardType: TextInputType.phone,
                            decoration: InputDecoration(
                              labelText: 'No. WhatsApp (Opsional)',
                              prefixIcon: const Icon(Icons.phone_iphone_rounded, size: 20),
                              hintText: '08123456789',
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 16),

                    // ── Card 2: Metode Pembayaran ──
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Metode Pembayaran',
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                          const SizedBox(height: 12),
                          _buildPaymentMethods(),
                        ],
                      ),
                    ),

                    const SizedBox(height: 16),

                    // ── Card 3: Nominal Bayar & Shortcut ──
                    if (_paymentMethod != 'tempo') ...[
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Nominal Uang Diterima',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _buildPaidAmountInput(cart),
                            const SizedBox(height: 12),
                            const Text(
                              'Pecahan Cepat:',
                              style: TextStyle(fontSize: 11, color: AppTheme.textSecondary),
                            ),
                            const SizedBox(height: 8),
                            _buildQuickAmountButtons(cart),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                    ],

                    // ── Card 4: Rincian Belanja ──
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'Rincian Item',
                                style: TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.textPrimary,
                                ),
                              ),
                              Text(
                                '${cart.itemCount} Item',
                                style: const TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.primary,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 10),
                          ...cart.items.map((item) => Padding(
                                padding: const EdgeInsets.only(bottom: 6),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Expanded(
                                      child: Text(
                                        '${item.quantity}x ${item.product.name}',
                                        style: const TextStyle(
                                          fontSize: 12,
                                          color: AppTheme.textPrimary,
                                        ),
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                    Text(
                                      _currencyFormat.format(item.subtotal),
                                      style: const TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.w700,
                                        fontFamily: 'monospace',
                                        color: AppTheme.textPrimary,
                                      ),
                                    ),
                                  ],
                                ),
                              )),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              // ── Sticky Summary & Confirm Button ──
              _buildSummarySection(cart),
            ],
          );
        },
      ),
    );
  }

  Widget _buildPaymentMethods() {
    final methods = [
      {'key': 'cash', 'label': 'Tunai (Cash)', 'icon': Icons.payments_rounded},
      {'key': 'qris', 'label': 'QRIS', 'icon': Icons.qr_code_2_rounded},
      {'key': 'transfer', 'label': 'Transfer', 'icon': Icons.account_balance_rounded},
      {'key': 'tempo', 'label': 'Tempo', 'icon': Icons.schedule_rounded},
    ];

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 10,
        mainAxisSpacing: 10,
        childAspectRatio: 2.4,
      ),
      itemCount: methods.length,
      itemBuilder: (context, idx) {
        final method = methods[idx];
        final isSelected = _paymentMethod == method['key'];

        return GestureDetector(
          onTap: () {
            setState(() {
              _paymentMethod = method['key'] as String;
              if (_paymentMethod == 'qris' || _paymentMethod == 'transfer') {
                final cart = context.read<CartProvider>();
                _paidController.text = cart.subtotal.toInt().toString();
              }
            });
          },
          child: AnimatedContainer(
            duration: AppTheme.animFast,
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(
              color: isSelected ? AppTheme.primaryBg : Colors.white,
              borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
              border: Border.all(
                color: isSelected ? AppTheme.primary : const Color(0xFFE2E8F0),
                width: isSelected ? 2 : 1,
              ),
            ),
            child: Row(
              children: [
                Icon(
                  method['icon'] as IconData,
                  color: isSelected ? AppTheme.primary : AppTheme.textSecondary,
                  size: 22,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    method['label'] as String,
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: isSelected ? FontWeight.w700 : FontWeight.w600,
                      color: isSelected ? AppTheme.primary : AppTheme.textPrimary,
                    ),
                  ),
                ),
                if (isSelected)
                  const Icon(
                    Icons.check_circle_rounded,
                    color: AppTheme.primary,
                    size: 16,
                  ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildPaidAmountInput(CartProvider cart) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFFF1F5F9),
        borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
        border: Border.all(color: const Color(0xFFCBD5E1), width: 1),
      ),
      child: TextField(
        controller: _paidController,
        keyboardType: TextInputType.number,
        textAlign: TextAlign.center,
        style: const TextStyle(
          fontSize: 22,
          fontWeight: FontWeight.w800,
          fontFamily: 'monospace',
          color: AppTheme.primary,
        ),
        decoration: InputDecoration(
          hintText: _currencyFormat.format(cart.subtotal),
          border: InputBorder.none,
          enabledBorder: InputBorder.none,
          focusedBorder: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(vertical: 14),
          prefixText: 'Rp ',
          prefixStyle: const TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.w700,
            color: AppTheme.textSecondary,
          ),
        ),
        onChanged: (_) => setState(() {}),
      ),
    );
  }

  Widget _buildQuickAmountButtons(CartProvider cart) {
    final total = cart.subtotal;
    final amounts = <double>{
      total,
      _roundUp(total, 10000),
      _roundUp(total, 20000),
      _roundUp(total, 50000),
      _roundUp(total, 100000),
    }.toList();

    return Wrap(
      spacing: 8,
      runSpacing: 8,
      children: amounts.map((amount) {
        final isExact = amount == total;
        return GestureDetector(
          onTap: () {
            _paidController.text = amount.toInt().toString();
            setState(() {});
          },
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
            decoration: BoxDecoration(
              color: isExact ? AppTheme.primaryBg : const Color(0xFFF1F5F9),
              borderRadius: BorderRadius.circular(AppTheme.radiusRound),
              border: Border.all(
                color: isExact ? AppTheme.primary : const Color(0xFFE2E8F0),
              ),
            ),
            child: Text(
              isExact ? 'Uang Pas (${_currencyFormat.format(amount)})' : _currencyFormat.format(amount),
              style: TextStyle(
                fontSize: 11,
                fontFamily: 'monospace',
                fontWeight: isExact ? FontWeight.w800 : FontWeight.w600,
                color: isExact ? AppTheme.primary : AppTheme.textPrimary,
              ),
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buildSummarySection(CartProvider cart) {
    final change = _paidAmount - cart.subtotal;

    return Container(
      padding: EdgeInsets.fromLTRB(
        16,
        14,
        16,
        14 + MediaQuery.of(context).padding.bottom,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
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
        children: [
          // Subtotal Row
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Total Belanja:', style: TextStyle(fontSize: 13, color: AppTheme.textSecondary)),
              Text(
                _currencyFormat.format(cart.subtotal),
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                  fontFamily: 'monospace',
                  color: AppTheme.textPrimary,
                ),
              ),
            ],
          ),

          // Kembalian Row
          if (_paymentMethod != 'tempo' && _paidAmount > 0) ...[
            const SizedBox(height: 4),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('Kembalian:', style: TextStyle(fontSize: 13, color: AppTheme.textSecondary)),
                Text(
                  _currencyFormat.format(change >= 0 ? change : 0),
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w800,
                    fontFamily: 'monospace',
                    color: change >= 0 ? AppTheme.success : AppTheme.danger,
                  ),
                ),
              ],
            ),
          ],

          const SizedBox(height: 12),

          // Selesaikan Transaksi Button
          SizedBox(
            width: double.infinity,
            height: 48,
            child: ElevatedButton(
              onPressed: _isProcessing ? null : _handleCheckout,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primary,
              ),
              child: _isProcessing
                  ? const SizedBox(
                      width: 22,
                      height: 22,
                      child: CircularProgressIndicator(
                        color: Colors.white,
                        strokeWidth: 2.5,
                      ),
                    )
                  : const Text(
                      'Selesaikan Transaksi & Cetak Nota',
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
                    ),
            ),
          ),
        ],
      ),
    );
  }

  double _roundUp(double value, int nearest) {
    return (value / nearest).ceil() * nearest.toDouble();
  }
}
