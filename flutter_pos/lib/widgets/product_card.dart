import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../config/theme.dart';
import '../models/product.dart';

/// ─────────────────────────────────────────────────────────
/// Product Card — Kartu produk POS matching tampilan Web
/// ─────────────────────────────────────────────────────────
class ProductCard extends StatefulWidget {
  final Product product;
  final VoidCallback onTap;

  const ProductCard({
    super.key,
    required this.product,
    required this.onTap,
  });

  @override
  State<ProductCard> createState() => _ProductCardState();
}

class _ProductCardState extends State<ProductCard>
    with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late Animation<double> _scaleAnimation;

  static final _currencyFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      duration: AppTheme.animFast,
      vsync: this,
    );
    _scaleAnimation = Tween<double>(begin: 1.0, end: 0.96).animate(
      CurvedAnimation(parent: _animController, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  void _onTapDown(TapDownDetails details) {
    _animController.forward();
  }

  void _onTapUp(TapUpDetails details) {
    _animController.reverse();
    widget.onTap();
  }

  void _onTapCancel() {
    _animController.reverse();
  }

  @override
  Widget build(BuildContext context) {
    final product = widget.product;
    final isOutOfStock = product.stock <= 0;

    return AnimatedBuilder(
      animation: _scaleAnimation,
      builder: (context, child) => Transform.scale(
        scale: _scaleAnimation.value,
        child: child,
      ),
      child: GestureDetector(
        onTapDown: isOutOfStock ? null : _onTapDown,
        onTapUp: isOutOfStock ? null : _onTapUp,
        onTapCancel: isOutOfStock ? null : _onTapCancel,
        child: Container(
          decoration: BoxDecoration(
            color: AppTheme.surface,
            borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
            border: Border.all(
              color: isOutOfStock ? AppTheme.border : const Color(0xFFE2E8F0),
              width: 1,
            ),
            boxShadow: AppTheme.shadowSmall,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // ── Gambar & Badges ──
              Expanded(
                flex: 3,
                child: Stack(
                  children: [
                    Container(
                      width: double.infinity,
                      decoration: const BoxDecoration(
                        color: Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.vertical(
                          top: Radius.circular(AppTheme.radiusLarge - 1),
                        ),
                      ),
                      child: product.image != null && product.image!.isNotEmpty
                          ? ClipRRect(
                              borderRadius: const BorderRadius.vertical(
                                top: Radius.circular(AppTheme.radiusLarge - 1),
                              ),
                              child: Image.network(
                                product.image!,
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) => _buildPlaceholder(),
                              ),
                            )
                          : _buildPlaceholder(),
                    ),

                    // Category Pill
                    if (product.category != null)
                      Positioned(
                        top: 8,
                        left: 8,
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 7,
                            vertical: 2.5,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.white.withValues(alpha: 0.92),
                            borderRadius: BorderRadius.circular(AppTheme.radiusRound),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withValues(alpha: 0.06),
                                blurRadius: 4,
                              ),
                            ],
                          ),
                          child: Text(
                            product.category!.name,
                            style: const TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.textSecondary,
                            ),
                          ),
                        ),
                      ),

                    // Stock Pill
                    Positioned(
                      top: 8,
                      right: 8,
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 7,
                          vertical: 2.5,
                        ),
                        decoration: BoxDecoration(
                          color: isOutOfStock
                              ? AppTheme.dangerLight
                              : (product.isLowStock
                                  ? AppTheme.warningLight
                                  : const Color(0xFFF1F5F9)),
                          borderRadius: BorderRadius.circular(AppTheme.radiusRound),
                          border: Border.all(
                            color: isOutOfStock
                                ? AppTheme.danger.withValues(alpha: 0.2)
                                : (product.isLowStock
                                    ? AppTheme.warning.withValues(alpha: 0.2)
                                    : AppTheme.border),
                            width: 0.5,
                          ),
                        ),
                        child: Text(
                          isOutOfStock
                              ? 'Habis'
                              : 'Stok: ${product.stock}',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.w700,
                            fontFamily: 'monospace',
                            color: isOutOfStock
                                ? AppTheme.danger
                                : (product.isLowStock
                                    ? AppTheme.warning
                                    : AppTheme.textMuted),
                          ),
                        ),
                      ),
                    ),

                    // Out of stock overlay
                    if (isOutOfStock)
                      Positioned.fill(
                        child: Container(
                          decoration: BoxDecoration(
                            color: Colors.white.withValues(alpha: 0.6),
                            borderRadius: const BorderRadius.vertical(
                              top: Radius.circular(AppTheme.radiusLarge - 1),
                            ),
                          ),
                        ),
                      ),
                  ],
                ),
              ),

              // ── Info Produk ──
              Expanded(
                flex: 2,
                child: Padding(
                  padding: const EdgeInsets.all(10),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        product.name,
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: isOutOfStock
                              ? AppTheme.textTertiary
                              : AppTheme.textPrimary,
                          height: 1.25,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Flexible(
                            child: Text(
                              _currencyFormat.format(product.sellingPrice),
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                                fontFamily: 'monospace',
                                color: isOutOfStock
                                    ? AppTheme.textTertiary
                                    : AppTheme.primary,
                              ),
                            ),
                          ),
                          Container(
                            width: 26,
                            height: 26,
                            decoration: BoxDecoration(
                              color: isOutOfStock
                                  ? const Color(0xFFF1F5F9)
                                  : AppTheme.primaryBg,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Icon(
                              Icons.add_rounded,
                              size: 16,
                              color: isOutOfStock
                                  ? AppTheme.textTertiary
                                  : AppTheme.primary,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPlaceholder() {
    return const Center(
      child: Icon(
        Icons.ac_unit_rounded,
        size: 32,
        color: Color(0xFFCBD5E1),
      ),
    );
  }
}
