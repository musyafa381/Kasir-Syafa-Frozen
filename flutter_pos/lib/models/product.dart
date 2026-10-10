/// Model Produk — merepresentasikan data dari API /api/products
class Product {
  final int id;
  final String name;
  final String slug;
  final String? barcode;
  final String? description;
  final String? image;
  final double costPrice;
  final double sellingPrice;
  final double loyaltyPrice;
  final int stock;
  final String? unit;
  final bool isLowStock;
  final ProductCategory? category;

  Product({
    required this.id,
    required this.name,
    required this.slug,
    this.barcode,
    this.description,
    this.image,
    required this.costPrice,
    required this.sellingPrice,
    required this.loyaltyPrice,
    required this.stock,
    this.unit,
    required this.isLowStock,
    this.category,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'] as int,
      name: json['name'] as String,
      slug: json['slug'] as String? ?? '',
      barcode: json['barcode'] as String?,
      description: json['description'] as String?,
      image: json['image'] as String?,
      costPrice: (json['cost_price'] as num).toDouble(),
      sellingPrice: (json['selling_price'] as num).toDouble(),
      loyaltyPrice: (json['loyalty_price'] as num?)?.toDouble() ?? 0,
      stock: json['stock'] as int,
      unit: json['unit'] as String?,
      isLowStock: json['is_low_stock'] as bool? ?? false,
      category: json['category'] != null
          ? ProductCategory.fromJson(json['category'] as Map<String, dynamic>)
          : null,
    );
  }
}

class ProductCategory {
  final int id;
  final String name;
  final String slug;

  ProductCategory({
    required this.id,
    required this.name,
    required this.slug,
  });

  factory ProductCategory.fromJson(Map<String, dynamic> json) {
    return ProductCategory(
      id: json['id'] as int,
      name: json['name'] as String,
      slug: json['slug'] as String? ?? '',
    );
  }
}
