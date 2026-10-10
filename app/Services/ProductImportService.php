<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportService
{
    /**
     * Generate dynamic CSV template with sep=; directive for 100% native Microsoft Excel column splitting.
     * No security warnings, opens directly in Columns A to I.
     */
    public function generateTemplate(): string
    {
        $categories = Category::orderBy('name')->pluck('name')->toArray();
        $categoriesList = !empty($categories) 
            ? implode(', ', $categories) 
            : 'Nugget & Sosis, Daging Sapi, Dimsum, Seafood, Kentang Beku, Bumbu & Saus';

        $output = "\xEF\xBB\xBF"; // UTF-8 BOM for Microsoft Excel
        $output .= "sep=;\n"; // Excel directive to force semicolon delimiter in separate columns

        $f = fopen('php://temp', 'r+');

        // Header
        fputcsv($f, [
            'barcode',
            'nama_produk',
            'kategori',
            'satuan',
            'harga_beli',
            'harga_jual',
            'harga_jual_member',
            'isi_per_dus',
            'harga_jual_dus',
            'harga_jual_dus_member',
            'barcode_dus',
            'stok_awal',
            'stok_minimum',
        ], ';');

        // Sample Rows (Realistic Frozen Food Products with Grosir/Dus Options)
        fputcsv($f, [
            '899123456001',
            'Fiesta Chicken Nugget Crispy 500g',
            'Nugget & Sosis',
            'pack',
            '35000',
            '45000',
            '42000',
            '24',
            '1000000',
            '980000',
            '899123456001DUS',
            '120',
            '24',
        ], ';');

        fputcsv($f, [
            '899123456002',
            'Kanzler Singles Keju 65g',
            'Nugget & Sosis',
            'pcs',
            '7500',
            '9500',
            '9000',
            '24',
            '215000',
            '210000',
            '',
            '240',
            '48',
        ], ';');

        fputcsv($f, [
            '899123456003',
            'Daging Sapi Slice US Shortplate 500g',
            'Daging Sapi',
            'pack',
            '55000',
            '68000',
            '64000',
            '10',
            '650000',
            '630000',
            '',
            '50',
            '10',
        ], ';');

        fputcsv($f, [
            '899123456004',
            'Dimsum Ayam Frozen Siap Kukus Isi 10',
            'Dimsum',
            'pack',
            '22000',
            '30000',
            '28000',
            '',
            '',
            '',
            '',
            '40',
            '8',
        ], ';');

        fputcsv($f, [
            '899123456005',
            'Kentang Goreng Shoestring 1kg',
            'Kentang & Sayur',
            'pack',
            '28000',
            '36000',
            '34000',
            '12',
            '400000',
            '390000',
            '',
            '60',
            '12',
        ], ';');

        // Guidance notes below
        fputcsv($f, [], ';');
        fputcsv($f, ['# PANDUAN PENGISIAN TEMPLATE IMPORT:'], ';');
        fputcsv($f, ['# 1. Kolom "nama_produk", "harga_beli" (HPP Modal), dan "harga_jual" (Eceran) WAJIB diisi.'], ';');
        fputcsv($f, ['# 2. Kolom "harga_jual_member" adalah harga khusus member/reseller eceran (opsional).'], ';');
        fputcsv($f, ['# 3. Kolom "isi_per_dus", "harga_jual_dus", "harga_jual_dus_member", "barcode_dus" adalah pengaturan Grosir/Dus (opsional).'], ';');
        fputcsv($f, ['# 4. Kolom "barcode" opsional (jika kosong akan otomatis dibuatkan oleh sistem).'], ';');
        fputcsv($f, ['# 5. Kolom "stok_awal" dihitung dalam satuan terkecil/dasar (Pcs / Pack).'], ';');
        fputcsv($f, ['# 6. Kategori yang saat ini terdaftar di toko: ' . $categoriesList], ';');
        fputcsv($f, ['# 7. Jika Anda menuliskan nama kategori baru, sistem otomatis mendaftarkannya ke sistem kasir.'], ';');

        rewind($f);
        $output .= stream_get_contents($f);
        fclose($f);

        return $output;
    }

    /**
     * Auto-detect CSV delimiter (comma, semicolon, tab).
     */
    public function detectDelimiter(string $content): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (str_starts_with(strtolower($line), 'sep=')) {
                $sep = substr($line, 4, 1);
                if (!empty($sep)) return $sep;
            }
            if (str_starts_with($line, '#')) continue;

            $semicolonCount = substr_count($line, ';');
            $commaCount = substr_count($line, ',');
            $tabCount = substr_count($line, "\t");

            if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
                return ';';
            }
            if ($tabCount > $commaCount && $tabCount > $semicolonCount) {
                return "\t";
            }
            if ($commaCount > 0) {
                return ',';
            }
        }

        return ';';
    }

    /**
     * Clean and normalize string header.
     */
    public function normalizeHeader(string $header): string
    {
        $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header);
        $h = strtolower(trim($h));
        $h = str_replace([' ', '-', '/', '.'], '_', $h);

        return match ($h) {
            'barcode', 'sku', 'kode_produk', 'kode_barang', 'barcode_sku', 'kode' => 'barcode',
            'nama', 'nama_produk', 'nama_barang', 'product_name', 'item_name', 'produk' => 'nama_produk',
            'kategori', 'kategori_produk', 'category', 'category_name' => 'kategori',
            'satuan', 'unit', 'kemasan' => 'satuan',
            'harga_beli', 'hpp', 'modal', 'harga_modal', 'cost_price', 'cost', 'harga_beli_hpp', 'beli' => 'harga_beli',
            'harga_jual', 'harga_umum', 'harga_reguler', 'selling_price', 'price', 'harga_jual_reguler', 'jual' => 'harga_jual',
            'harga_jual_member', 'harga_member', 'harga_loyalty', 'loyalty_price', 'member_price', 'grosir', 'harga_diskon_member', 'member' => 'harga_jual_member',
            'isi_per_dus', 'isi_dus', 'pcs_per_dus', 'pieces_per_box', 'isi_karton', 'isi_box', 'isi_per_karton' => 'isi_per_dus',
            'harga_jual_dus', 'harga_dus', 'box_selling_price', 'harga_karton', 'harga_grosir_dus' => 'harga_jual_dus',
            'harga_jual_dus_member', 'harga_dus_member', 'box_loyalty_price', 'harga_karton_member', 'harga_grosir_member' => 'harga_jual_dus_member',
            'barcode_dus', 'box_barcode', 'barcode_karton', 'kode_dus' => 'barcode_dus',
            'stok', 'stok_awal', 'qty', 'quantity', 'stock', 'initial_stock' => 'stok_awal',
            'stok_minimum', 'min_stok', 'min_stock', 'stok_alert', 'min_stock_alert' => 'stok_minimum',
            'keterangan', 'deskripsi', 'description', 'notes', 'catatan' => 'keterangan',
            default => $h,
        };
    }

    /**
     * Parse and sanitize currency/number value.
     */
    public function parseNumber($val): float
    {
        if (is_numeric($val)) {
            return (float) $val;
        }

        if (empty($val)) {
            return 0.0;
        }

        $str = (string) $val;
        $str = preg_replace('/[^\d,.-]/', '', $str);

        // If formatted like Indonesian standard "45.000,00" or "45.000"
        if (str_contains($str, ',') && str_contains($str, '.')) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif (str_contains($str, '.') && !str_contains($str, ',')) {
            $parts = explode('.', $str);
            if (count($parts) === 2 && strlen($parts[1]) === 3) {
                $str = str_replace('.', '', $str);
            }
        } elseif (str_contains($str, ',') && !str_contains($str, '.')) {
            $parts = explode(',', $str);
            if (count($parts) === 2 && strlen($parts[1]) === 3) {
                $str = str_replace(',', '', $str);
            } else {
                $str = str_replace(',', '.', $str);
            }
        }

        return (float) $str;
    }

    /**
     * Extract files from ZIP data in pure PHP without external dependencies.
     */
    private function extractZipFiles(string $zipData): array
    {
        $entries = [];
        $pos = strrpos($zipData, "\x50\x4b\x05\x06");
        if ($pos === false) {
            return [];
        }

        $cdOffset = unpack('V', substr($zipData, $pos + 16, 4))[1];
        $cdEntries = unpack('v', substr($zipData, $pos + 10, 2))[1];

        $offset = $cdOffset;
        for ($i = 0; $i < $cdEntries; $i++) {
            if (substr($zipData, $offset, 4) !== "\x50\x4b\x01\x02") {
                break;
            }
            $compression = unpack('v', substr($zipData, $offset + 10, 2))[1];
            $compressedSize = unpack('V', substr($zipData, $offset + 20, 4))[1];
            $fileNameLen = unpack('v', substr($zipData, $offset + 28, 2))[1];
            $extraLen = unpack('v', substr($zipData, $offset + 30, 2))[1];
            $commentLen = unpack('v', substr($zipData, $offset + 32, 2))[1];
            $localHeaderOffset = unpack('V', substr($zipData, $offset + 42, 4))[1];
            $fileName = substr($zipData, $offset + 46, $fileNameLen);

            $localExtraLen = unpack('v', substr($zipData, $localHeaderOffset + 28, 2))[1];
            $dataOffset = $localHeaderOffset + 30 + $fileNameLen + $localExtraLen;
            $compressedData = substr($zipData, $dataOffset, $compressedSize);

            if ($compression === 0) {
                $content = $compressedData;
            } elseif ($compression === 8) {
                $content = @gzinflate($compressedData);
            } else {
                $content = '';
            }

            $entries[$fileName] = $content;
            $offset += 46 + $fileNameLen + $extraLen + $commentLen;
        }

        return $entries;
    }

    /**
     * Parse native Excel (.xlsx) file using pure PHP.
     */
    private function parseXlsx(string $content): array
    {
        $files = $this->extractZipFiles($content);
        if (empty($files)) {
            return [];
        }

        // 1. Parse shared strings if present
        $sharedStrings = [];
        if (isset($files['xl/sharedStrings.xml']) && !empty($files['xl/sharedStrings.xml'])) {
            $xml = @simplexml_load_string($files['xl/sharedStrings.xml']);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Locate active sheet (e.g. xl/worksheets/sheet1.xml)
        $sheetFile = null;
        foreach ($files as $name => $data) {
            if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                $sheetFile = $name;
                break;
            }
        }

        if (!$sheetFile || empty($files[$sheetFile])) {
            return [];
        }

        $sheetXml = @simplexml_load_string($files[$sheetFile]);
        if (!$sheetXml || !isset($sheetXml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $rowNode) {
            $rowCols = [];
            foreach ($rowNode->c as $c) {
                $cellRef = (string) $c['r']; // e.g. A1, B1
                preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                $colLetters = $matches[1] ?? 'A';
                
                // Convert column letters (A, B, C...) to index (0, 1, 2...)
                $colIndex = 0;
                for ($l = 0; $l < strlen($colLetters); $l++) {
                    $colIndex = $colIndex * 26 + (ord($colLetters[$l]) - ord('A') + 1);
                }
                $colIndex -= 1;

                $type = (string) $c['t'];
                $val = '';
                if ($type === 's') {
                    $idx = (int) $c->v;
                    $val = $sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $val = (string) $c->is->t;
                } elseif (isset($c->v)) {
                    $val = (string) $c->v;
                }

                $rowCols[$colIndex] = trim($val);
            }

            if (!empty($rowCols)) {
                // Fill any missing indices
                $maxIndex = max(array_keys($rowCols));
                $normalizedRow = [];
                for ($k = 0; $k <= $maxIndex; $k++) {
                    $normalizedRow[$k] = $rowCols[$k] ?? '';
                }
                $rows[] = $normalizedRow;
            }
        }

        return $rows;
    }

    /**
     * Parse and validate file content (XLSX, HTML .xls, CSV, TSV) for live preview.
     */
    public function parseAndValidate(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'File tidak ditemukan.',
                'rows' => [],
                'stats' => ['total' => 0, 'valid' => 0, 'update' => 0, 'invalid' => 0],
            ];
        }

        $content = file_get_contents($filePath);
        $rawRows = [];

        // Check if file is ZIP/XLSX format (starts with PK\x03\x04)
        if (str_starts_with($content, "PK\x03\x04")) {
            $rawRows = $this->parseXlsx($content);
        } elseif (stripos($content, '<table') !== false || stripos($content, '<html') !== false) {
            // HTML Table (.xls)
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            $tables = $dom->getElementsByTagName('table');
            if ($tables->length > 0) {
                $table = $tables->item(0);
                foreach ($table->getElementsByTagName('tr') as $tr) {
                    $cols = [];
                    foreach ($tr->childNodes as $node) {
                        if ($node->nodeName === 'td' || $node->nodeName === 'th') {
                            $cols[] = trim($node->textContent);
                        }
                    }
                    if (!empty($cols) && count(array_filter($cols, fn($c) => $c !== '')) > 0) {
                        $rawRows[] = $cols;
                    }
                }
            }
        } else {
            // CSV / TSV format
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
            $delimiter = $this->detectDelimiter($content);
            $lines = preg_split("/\r\n|\n|\r/", $content);

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with(strtolower($line), 'sep=') || str_starts_with($line, '#')) {
                    continue;
                }

                $cols = str_getcsv($line, $delimiter);
                if (!empty($cols) && count(array_filter($cols, fn($c) => $c !== '')) > 0) {
                    $rawRows[] = array_map('trim', $cols);
                }
            }
        }

        if (empty($rawRows)) {
            return [
                'success' => false,
                'message' => 'File kosong atau tidak dapat diuraikan. Pastikan format file sesuai template.',
                'rows' => [],
                'stats' => ['total' => 0, 'valid' => 0, 'update' => 0, 'invalid' => 0],
            ];
        }

        $rows = [];
        $headers = [];
        $headerFound = false;

        $existingBarcodes = Product::pluck('id', 'barcode')->filter()->toArray();

        $validCount = 0;
        $updateCount = 0;
        $invalidCount = 0;

        foreach ($rawRows as $rowIdx => $cols) {
            // Skip guide notes
            if (!empty($cols[0]) && (str_starts_with($cols[0], '#') || str_starts_with($cols[0], 'PANDUAN') || str_starts_with($cols[0], '1.') || str_starts_with($cols[0], '2.'))) {
                continue;
            }

            // Detect header line
            if (!$headerFound) {
                $normalizedCols = array_map([$this, 'normalizeHeader'], $cols);
                if (in_array('nama_produk', $normalizedCols) || in_array('harga_jual', $normalizedCols)) {
                    $headers = $normalizedCols;
                    $headerFound = true;
                    continue;
                }
            }

            if (!$headerFound) {
                continue;
            }

            // Map columns to standard fields
            $rowRaw = [];
            foreach ($headers as $idx => $headerKey) {
                $rowRaw[$headerKey] = isset($cols[$idx]) ? trim($cols[$idx]) : '';
            }

            $barcode = !empty($rowRaw['barcode']) ? trim($rowRaw['barcode']) : '';
            $name = !empty($rowRaw['nama_produk']) ? trim($rowRaw['nama_produk']) : '';
            $categoryName = !empty($rowRaw['kategori']) ? trim($rowRaw['kategori']) : 'Umum';
            $unit = !empty($rowRaw['satuan']) ? strtolower(trim($rowRaw['satuan'])) : 'pack';
            $costPrice = $this->parseNumber($rowRaw['harga_beli'] ?? 0);
            $sellingPrice = $this->parseNumber($rowRaw['harga_jual'] ?? 0);
            $loyaltyPrice = !empty($rowRaw['harga_jual_member']) ? $this->parseNumber($rowRaw['harga_jual_member']) : $sellingPrice;
            $piecesPerBox = !empty($rowRaw['isi_per_dus']) ? (int) $this->parseNumber($rowRaw['isi_per_dus']) : null;
            $boxSellingPrice = !empty($rowRaw['harga_jual_dus']) ? $this->parseNumber($rowRaw['harga_jual_dus']) : null;
            $boxLoyaltyPrice = !empty($rowRaw['harga_jual_dus_member']) ? $this->parseNumber($rowRaw['harga_jual_dus_member']) : $boxSellingPrice;
            $boxBarcode = !empty($rowRaw['barcode_dus']) ? trim($rowRaw['barcode_dus']) : null;
            $stock = (int) $this->parseNumber($rowRaw['stok_awal'] ?? 0);
            $minStock = !empty($rowRaw['stok_minimum']) ? (int) $this->parseNumber($rowRaw['stok_minimum']) : 5;
            $description = !empty($rowRaw['keterangan']) ? trim($rowRaw['keterangan']) : null;

            // Row Validation
            $errors = [];
            if (empty($name)) {
                $errors[] = 'Nama produk wajib diisi.';
            }
            if ($costPrice < 0) {
                $errors[] = 'Harga beli tidak boleh negatif.';
            }
            if ($sellingPrice <= 0) {
                $errors[] = 'Harga jual reguler harus lebih dari 0.';
            }
            if ($loyaltyPrice < 0) {
                $errors[] = 'Harga member tidak boleh negatif.';
            }
            if ($piecesPerBox !== null && $piecesPerBox < 1) {
                $errors[] = 'Isi per dus minimal 1.';
            }
            if ($boxSellingPrice !== null && $boxSellingPrice < 0) {
                $errors[] = 'Harga jual dus tidak boleh negatif.';
            }
            if ($boxLoyaltyPrice !== null && $boxLoyaltyPrice < 0) {
                $errors[] = 'Harga member dus tidak boleh negatif.';
            }
            if ($stock < 0) {
                $errors[] = 'Stok tidak boleh negatif.';
            }

            $isExisting = !empty($barcode) && isset($existingBarcodes[$barcode]);
            $isValid = empty($errors);

            if ($isValid) {
                if ($isExisting) {
                    $updateCount++;
                } else {
                    $validCount++;
                }
            } else {
                $invalidCount++;
            }

            $rows[] = [
                'row_number' => $rowIdx + 1,
                'barcode' => $barcode,
                'name' => $name,
                'category' => $categoryName,
                'category_name' => $categoryName,
                'unit' => $unit,
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'loyalty_price' => $loyaltyPrice,
                'pieces_per_box' => $piecesPerBox,
                'box_selling_price' => $boxSellingPrice,
                'box_loyalty_price' => $boxLoyaltyPrice,
                'box_barcode' => $boxBarcode,
                'stock' => $stock,
                'min_stock_alert' => $minStock,
                'description' => $description,
                'is_valid' => $isValid,
                'is_update' => $isExisting,
                'existing_product_id' => $isExisting ? $existingBarcodes[$barcode] : null,
                'errors' => $errors,
            ];
        }

        if (!$headerFound) {
            return [
                'success' => false,
                'message' => 'Format file tidak sesuai. Header kolom nama_produk atau harga_jual tidak ditemukan.',
                'rows' => [],
                'stats' => ['total' => 0, 'valid' => 0, 'update' => 0, 'invalid' => 0],
            ];
        }

        return [
            'success' => true,
            'message' => 'File berhasil diuraikan.',
            'rows' => $rows,
            'stats' => [
                'total' => count($rows),
                'valid' => $validCount,
                'update' => $updateCount,
                'invalid' => $invalidCount,
            ],
        ];
    }

    /**
     * Execute importing valid rows into database with transaction.
     */
    public function executeImport(array $rows, bool $updateExisting = true, ?int $userId = null): array
    {
        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        // Cache category IDs
        $categoryCache = [];
        $existingCategories = Category::all();
        foreach ($existingCategories as $cat) {
            $categoryCache[strtolower(trim($cat->name))] = $cat->id;
        }

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                if (!$row['is_valid']) {
                    $failedCount++;
                    continue;
                }

                $barcode = !empty($row['barcode']) ? trim($row['barcode']) : null;
                $name = trim($row['name']);
                $categoryName = !empty($row['category_name']) ? trim($row['category_name']) : (!empty($row['category']) ? trim($row['category']) : 'Umum');
                $catKey = strtolower($categoryName);

                // Find or create category
                if (!isset($categoryCache[$catKey])) {
                    $newCategory = Category::create([
                        'name' => $categoryName,
                        'slug' => Str::slug($categoryName) . '-' . Str::random(4),
                    ]);
                    $categoryCache[$catKey] = $newCategory->id;
                }
                $categoryId = $categoryCache[$catKey];

                $costPrice = (float) $row['cost_price'];
                $sellingPrice = (float) $row['selling_price'];
                $loyaltyPrice = (float) $row['loyalty_price'];
                $piecesPerBox = isset($row['pieces_per_box']) && $row['pieces_per_box'] ? (int) $row['pieces_per_box'] : null;
                $boxSellingPrice = isset($row['box_selling_price']) && $row['box_selling_price'] !== null ? (float) $row['box_selling_price'] : null;
                $boxLoyaltyPrice = isset($row['box_loyalty_price']) && $row['box_loyalty_price'] !== null ? (float) $row['box_loyalty_price'] : $boxSellingPrice;
                $boxBarcode = !empty($row['box_barcode']) ? trim($row['box_barcode']) : null;
                $stock = (int) $row['stock'];
                $minStock = isset($row['min_stock_alert']) ? (int) $row['min_stock_alert'] : 5;
                $unit = !empty($row['unit']) ? $row['unit'] : 'pack';
                $description = $row['description'] ?? null;

                // Check if product exists by barcode
                $existingProduct = null;
                if ($barcode) {
                    $existingProduct = Product::where('barcode', $barcode)->first();
                }

                if ($existingProduct) {
                    if ($updateExisting) {
                        $oldStock = $existingProduct->stock;
                        $existingProduct->update([
                            'name' => $name,
                            'category_id' => $categoryId,
                            'unit' => $unit,
                            'cost_price' => $costPrice,
                            'selling_price' => $sellingPrice,
                            'loyalty_price' => $loyaltyPrice,
                            'pieces_per_box' => $piecesPerBox ?: $existingProduct->pieces_per_box,
                            'box_selling_price' => $boxSellingPrice !== null ? $boxSellingPrice : $existingProduct->box_selling_price,
                            'box_loyalty_price' => $boxLoyaltyPrice !== null ? $boxLoyaltyPrice : $existingProduct->box_loyalty_price,
                            'box_barcode' => $boxBarcode ?: $existingProduct->box_barcode,
                            'stock' => $stock,
                            'min_stock_alert' => $minStock,
                            'description' => $description ?: $existingProduct->description,
                            'is_active' => true,
                        ]);

                        // Record mutation if stock adjusted
                        if ($stock !== $oldStock) {
                            StockMutation::create([
                                'product_id' => $existingProduct->id,
                                'user_id' => $userId,
                                'type' => $stock > $oldStock ? 'in' : 'out',
                                'quantity' => $stock - $oldStock,
                                'stock_before' => $oldStock,
                                'stock_after' => $stock,
                                'reference' => 'EXCEL-IMPORT',
                                'description' => 'Penyesuaian stok via Impor Excel',
                            ]);
                        }

                        $updatedCount++;
                    } else {
                        $skippedCount++;
                    }
                } else {
                    // Create new product
                    $product = Product::create([
                        'barcode' => $barcode,
                        'name' => $name,
                        'category_id' => $categoryId,
                        'unit' => $unit,
                        'cost_price' => $costPrice,
                        'selling_price' => $sellingPrice,
                        'loyalty_price' => $loyaltyPrice,
                        'pieces_per_box' => $piecesPerBox,
                        'box_selling_price' => $boxSellingPrice,
                        'box_loyalty_price' => $boxLoyaltyPrice,
                        'box_barcode' => $boxBarcode,
                        'stock' => $stock,
                        'min_stock_alert' => $minStock,
                        'description' => $description,
                        'is_active' => true,
                    ]);

                    // Record initial stock mutation
                    if ($stock > 0) {
                        StockMutation::create([
                            'product_id' => $product->id,
                            'user_id' => $userId,
                            'type' => 'in',
                            'quantity' => $stock,
                            'stock_before' => 0,
                            'stock_after' => $stock,
                            'reference' => 'INITIAL-STOCK',
                            'description' => 'Stok awal dari Impor Excel',
                        ]);
                    }

                    $importedCount++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Proses impor selesai: {$importedCount} produk baru ditambahkan, {$updatedCount} diperbarui, {$skippedCount} dilewati, {$failedCount} gagal.",
                'created_count' => $importedCount,
                'imported_count' => $importedCount,
                'updated_count' => $updatedCount,
                'skipped_count' => $skippedCount,
                'failed_count' => $failedCount,
                'stats' => [
                    'imported' => $importedCount,
                    'updated' => $updatedCount,
                    'skipped' => $skippedCount,
                    'failed' => $failedCount,
                ],
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal memproses impor: ' . $e->getMessage(),
                'created_count' => 0,
                'imported_count' => 0,
                'updated_count' => 0,
                'skipped_count' => 0,
                'failed_count' => count($rows),
                'stats' => [
                    'imported' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'failed' => count($rows),
                ],
            ];
        }
    }
}
