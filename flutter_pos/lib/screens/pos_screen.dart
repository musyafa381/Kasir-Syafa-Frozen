import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:shimmer/shimmer.dart';
import '../config/theme.dart';
import '../providers/auth_provider.dart';
import '../providers/cart_provider.dart';
import '../providers/product_provider.dart';
import '../widgets/product_card.dart';
import '../widgets/category_chip.dart';
import '../widgets/cart_item_tile.dart';

/// ─────────────────────────────────────────────────────────
/// POS Screen — Terminal Kasir matching Web POS
/// Adaptive layout: mobile & tablet split view
/// ─────────────────────────────────────────────────────────
class PosScreen extends StatefulWidget {
  const PosScreen({super.key});

  @override
  State<PosScreen> createState() => _PosScreenState();
}

class _PosScreenState extends State<PosScreen> {
  final _searchController = TextEditingController();
  final _searchFocusNode = FocusNode();

  static final _currencyFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ProductProvider>().refreshAll();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    _searchFocusNode.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final screenWidth = MediaQuery.of(context).size.width;
    final isTablet = screenWidth >= 800;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: _buildTopAppBar(isTablet),
      body: SafeArea(
        child: isTablet ? _buildTabletLayout() : _buildMobileLayout(),
      ),
    );
  }

  PreferredSizeWidget _buildTopAppBar(bool isTablet) {
    return AppBar(
      backgroundColor: AppTheme.primary,
      elevation: 0,
      titleSpacing: 16,
      title: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(
              Icons.ac_unit_rounded,
              color: Colors.white,
              size: 20,
            ),
          ),
          const SizedBox(width: 10),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Syafa Frozen Food',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                  letterSpacing: -0.2,
                ),
              ),
              Consumer<AuthProvider>(
                builder: (context, auth, _) => Text(
                  'Kasir : ${auth.userName ?? "Kasir"}',
                  style: TextStyle(
                    fontSize: 11,
                    color: Colors.white.withValues(alpha: 0.85),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh_rounded, color: Colors.white),
          tooltip: 'Refresh Produk',
          onPressed: () => context.read<ProductProvider>().refreshAll(),
        ),
        IconButton(
          icon: const Icon(Icons.logout_rounded, color: Colors.white),
          tooltip: 'Keluar',
          onPressed: () => _showLogoutDialog(),
        ),
        const SizedBox(width: 8),
      ],
    );
  }

  // ══════════════════════════════════════════════════════════
  // MOBILE LAYOUT
  // ══════════════════════════════════════════════════════════
  Widget _buildMobileLayout() {
    return Stack(
      children: [
        Column(
          children: [
            _buildSearchBar(),
            _buildCategoryBar(),
            Expanded(child: _buildProductGrid(crossAxisCount: 2)),
            const SizedBox(height: 84), // Space for bottom bar
          ],
        ),
        Positioned(
          left: 0,
          right: 0,
          bottom: 0,
          child: _buildMobileBottomBar(),
        ),
      ],
    );
  }

  // ══════════════════════════════════════════════════════════
  // TABLET / DESKTOP LAYOUT (Split Screen)
  // ══════════════════════════════════════════════════════════
  Widget _buildTabletLayout() {
    return Row(
      children: [
        // Left Column: Catalog
        Expanded(
          flex: 6,
          child: Column(
            children: [
              _buildSearchBar(),
              _buildCategoryBar(),
              Expanded(child: _buildProductGrid(crossAxisCount: 3)),
            ],
          ),
        ),

        // Right Column: Cart Panel
        Container(
          width: 380,
          decoration: const BoxDecoration(
            color: Colors.white,
            border: Border(
              left: BorderSide(color: Color(0xFFE2E8F0), width: 1),
            ),
          ),
          child: _buildCartPanel(),
        ),
      ],
    );
  }

  // ══════════════════════════════════════════════════════════
  // SEARCH BAR
  // ══════════════════════════════════════════════════════════
  Widget _buildSearchBar() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 6),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
          border: Border.all(color: const Color(0xFFE2E8F0), width: 1),
          boxShadow: AppTheme.shadowSmall,
        ),
        child: TextField(
          controller: _searchController,
          focusNode: _searchFocusNode,
          onChanged: (val) {
            context.read<ProductProvider>().setSearchQuery(val);
          },
          decoration: InputDecoration(
            hintText: 'Cari produk frozen atau scan barcode...',
            hintStyle: const TextStyle(
              fontSize: 13,
              color: AppTheme.textTertiary,
            ),
            prefixIcon: const Icon(
              Icons.search_rounded,
              color: AppTheme.textSecondary,
              size: 20,
            ),
            suffixIcon: _searchController.text.isNotEmpty
                ? IconButton(
                    icon: const Icon(Icons.close_rounded, size: 18),
                    onPressed: () {
                      _searchController.clear();
                      context.read<ProductProvider>().setSearchQuery('');
                    },
                  )
                : const Icon(
                    Icons.qr_code_scanner_rounded,
                    color: AppTheme.primary,
                    size: 20,
                  ),
            border: InputBorder.none,
            enabledBorder: InputBorder.none,
            focusedBorder: InputBorder.none,
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 14,
              vertical: 12,
            ),
          ),
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════════════
  // CATEGORY BAR
  // ══════════════════════════════════════════════════════════
  Widget _buildCategoryBar() {
    return Consumer<ProductProvider>(
      builder: (context, provider, _) {
        return SizedBox(
          height: 48,
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            children: [
              Padding(
                padding: const EdgeInsets.only(right: 8),
                child: CategoryChip(
                  label: 'Semua Produk',
                  isSelected: provider.selectedCategoryId == null,
                  onTap: () => provider.setCategory(null),
                ),
              ),
              ...provider.categories.map((cat) => Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: CategoryChip(
                      label: cat.name,
                      isSelected: provider.selectedCategoryId == cat.id,
                      onTap: () => provider.setCategory(cat.id),
                    ),
                  )),
            ],
          ),
        );
      },
    );
  }

  // ══════════════════════════════════════════════════════════
  // PRODUCT GRID
  // ══════════════════════════════════════════════════════════
  Widget _buildProductGrid({required int crossAxisCount}) {
    return Consumer<ProductProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) {
          return _buildShimmerGrid(crossAxisCount);
        }

        if (provider.errorMessage != null) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(
                  Icons.cloud_off_rounded,
                  size: 48,
                  color: AppTheme.textTertiary,
                ),
                const SizedBox(height: 12),
                Text(
                  provider.errorMessage!,
                  style: const TextStyle(
                    fontSize: 13,
                    color: AppTheme.textSecondary,
                  ),
                ),
                const SizedBox(height: 12),
                ElevatedButton(
                  onPressed: () => provider.refreshAll(),
                  child: const Text('Coba Lagi'),
                ),
              ],
            ),
          );
        }

        if (provider.products.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: const [
                Icon(
                  Icons.inventory_2_outlined,
                  size: 48,
                  color: AppTheme.textTertiary,
                ),
                SizedBox(height: 12),
                Text(
                  'Tidak ada produk yang sesuai',
                  style: TextStyle(
                    fontSize: 13,
                    color: AppTheme.textSecondary,
                  ),
                ),
              ],
            ),
          );
        }

        return RefreshIndicator(
          onRefresh: () => provider.refreshAll(),
          color: AppTheme.primary,
          child: GridView.builder(
            padding: const EdgeInsets.all(16),
            gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: crossAxisCount,
              crossAxisSpacing: 12,
              mainAxisSpacing: 12,
              childAspectRatio: 0.72,
            ),
            itemCount: provider.products.length,
            itemBuilder: (context, index) {
              final product = provider.products[index];
              return ProductCard(
                product: product,
                onTap: () {
                  context.read<CartProvider>().addItem(product);
                },
              );
            },
          ),
        );
      },
    );
  }

  // ══════════════════════════════════════════════════════════
  // MOBILE BOTTOM BAR
  // ══════════════════════════════════════════════════════════
  Widget _buildMobileBottomBar() {
    return Consumer<CartProvider>(
      builder: (context, cart, _) {
        if (cart.isEmpty) return const SizedBox.shrink();

        return Container(
          padding: EdgeInsets.fromLTRB(
            16,
            12,
            16,
            12 + MediaQuery.of(context).padding.bottom,
          ),
          decoration: BoxDecoration(
            color: Colors.white,
            border: const Border(
              top: BorderSide(color: Color(0xFFE2E8F0), width: 1),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.08),
                blurRadius: 16,
                offset: const Offset(0, -4),
              ),
            ],
          ),
          child: Row(
            children: [
              // Cart icon
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: AppTheme.primaryBg,
                  borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                ),
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    const Icon(
                      Icons.shopping_bag_outlined,
                      color: AppTheme.primary,
                      size: 22,
                    ),
                    Positioned(
                      top: 4,
                      right: 4,
                      child: Container(
                        padding: const EdgeInsets.all(3),
                        decoration: const BoxDecoration(
                          color: AppTheme.danger,
                          shape: BoxShape.circle,
                        ),
                        child: Text(
                          '${cart.itemCount}',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 9,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(width: 12),

              // Total
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      '${cart.itemCount} Item terpilih',
                      style: const TextStyle(
                        fontSize: 11,
                        color: AppTheme.textTertiary,
                      ),
                    ),
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
              ),

              // Checkout Button
              ElevatedButton.icon(
                onPressed: () {
                  Navigator.of(context).pushNamed('/checkout');
                },
                icon: const Icon(Icons.arrow_forward_rounded, size: 18),
                label: const Text('Bayar'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primary,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 20,
                    vertical: 12,
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  // ══════════════════════════════════════════════════════════
  // CART PANEL (Tablet / Desktop Side Panel)
  // ══════════════════════════════════════════════════════════
  Widget _buildCartPanel() {
    return Consumer<CartProvider>(
      builder: (context, cart, _) {
        return Column(
          children: [
            // Header
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 12),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const Text(
                        'Keranjang',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w800,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 2,
                        ),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryBg,
                          borderRadius: BorderRadius.circular(AppTheme.radiusRound),
                        ),
                        child: Text(
                          '${cart.itemCount} item',
                          style: const TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.primary,
                          ),
                        ),
                      ),
                    ],
                  ),
                  if (!cart.isEmpty)
                    GestureDetector(
                      onTap: () => cart.clearCart(),
                      child: const Text(
                        'Hapus Semua',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.danger,
                        ),
                      ),
                    ),
                ],
              ),
            ),

            const Divider(height: 1, color: Color(0xFFE2E8F0)),

            // Cart Items List
            Expanded(
              child: cart.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: const [
                          Icon(
                            Icons.shopping_cart_outlined,
                            size: 48,
                            color: Color(0xFFCBD5E1),
                          ),
                          SizedBox(height: 12),
                          Text(
                            'Keranjang masih kosong',
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.textSecondary,
                            ),
                          ),
                          SizedBox(height: 4),
                          Text(
                            'Pilih item produk untuk memulai',
                            style: TextStyle(
                              fontSize: 11,
                              color: AppTheme.textTertiary,
                            ),
                          ),
                        ],
                      ),
                    )
                  : ListView.separated(
                      padding: const EdgeInsets.all(12),
                      itemCount: cart.items.length,
                      separatorBuilder: (context, index) => const SizedBox(height: 8),
                      itemBuilder: (context, index) {
                        final item = cart.items[index];
                        return CartItemTile(
                          item: item,
                          onIncrement: () => cart.addItem(item.product),
                          onDecrement: () => cart.decrementItem(item.product.id),
                          onRemove: () => cart.removeItem(item.product.id),
                        );
                      },
                    ),
            ),

            // Summary & Checkout
            if (!cart.isEmpty) ...[
              const Divider(height: 1, color: Color(0xFFE2E8F0)),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: const BoxDecoration(
                  color: Color(0xFFF8FAFC),
                ),
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Subtotal:',
                          style: TextStyle(
                            fontSize: 13,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                        Text(
                          _currencyFormat.format(cart.subtotal),
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w700,
                            fontFamily: 'monospace',
                            color: AppTheme.textPrimary,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Total Belanja:',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                        Text(
                          _currencyFormat.format(cart.subtotal),
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                            fontFamily: 'monospace',
                            color: AppTheme.primary,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    SizedBox(
                      width: double.infinity,
                      height: 48,
                      child: ElevatedButton.icon(
                        onPressed: () {
                          Navigator.of(context).pushNamed('/checkout');
                        },
                        icon: const Icon(Icons.payment_rounded, size: 20),
                        label: const Text('Proses Pembayaran'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppTheme.primary,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ],
        );
      },
    );
  }

  // ══════════════════════════════════════════════════════════
  // SHIMMER LOADING
  // ══════════════════════════════════════════════════════════
  Widget _buildShimmerGrid(int crossAxisCount) {
    return Shimmer.fromColors(
      baseColor: const Color(0xFFE2E8F0),
      highlightColor: const Color(0xFFF8FAFC),
      child: GridView.builder(
        padding: const EdgeInsets.all(16),
        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: crossAxisCount,
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
          childAspectRatio: 0.72,
        ),
        itemCount: 6,
        itemBuilder: (context, idx) => Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
          ),
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════════════
  // LOGOUT DIALOG
  // ══════════════════════════════════════════════════════════
  void _showLogoutDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppTheme.radiusLarge),
        ),
        title: const Text(
          'Keluar Akun',
          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
        ),
        content: const Text(
          'Yakin ingin keluar dari terminal kasir?',
          style: TextStyle(fontSize: 13, color: AppTheme.textSecondary),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              context.read<AuthProvider>().logout();
              Navigator.of(context).pushReplacementNamed('/login');
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.danger,
            ),
            child: const Text('Keluar'),
          ),
        ],
      ),
    );
  }
}
