/// Model Kategori — dari API /api/categories
class Category {
  final int id;
  final String name;
  final String slug;
  final int productCount;

  Category({
    required this.id,
    required this.name,
    required this.slug,
    required this.productCount,
  });

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'] as int,
      name: json['name'] as String,
      slug: json['slug'] as String? ?? '',
      productCount: json['product_count'] as int? ?? 0,
    );
  }
}
