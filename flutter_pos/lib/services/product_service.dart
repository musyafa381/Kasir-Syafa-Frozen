import '../models/product.dart';
import '../models/category.dart';
import 'api_service.dart';

/// ─────────────────────────────────────────────────────────
/// Product Service — Fetch produk & kategori dari API
/// ─────────────────────────────────────────────────────────
class ProductService {
  final ApiService _api = ApiService.instance;

  /// Ambil semua produk aktif (dengan filter opsional)
  Future<List<Product>> getProducts({String? search, int? categoryId}) async {
    final params = <String, dynamic>{};
    if (search != null && search.isNotEmpty) {
      params['search'] = search;
    }
    if (categoryId != null) {
      params['category_id'] = categoryId;
    }

    final response = await _api.get('/products', queryParameters: params);
    final data = response.data['data'] as List<dynamic>;

    return data
        .map((json) => Product.fromJson(json as Map<String, dynamic>))
        .toList();
  }

  /// Ambil semua kategori
  Future<List<Category>> getCategories() async {
    final response = await _api.get('/categories');
    final data = response.data['data'] as List<dynamic>;

    return data
        .map((json) => Category.fromJson(json as Map<String, dynamic>))
        .toList();
  }
}
