import 'package:flutter/material.dart';

/// ─────────────────────────────────────────────────────────
/// SYAFA POS — Design System matching Web POS Terminal
/// Tailwind Blue / Slate / Emerald palette with clean typography
/// ─────────────────────────────────────────────────────────

class AppTheme {
  AppTheme._();

  // ── Colors (Tailwind Match) ──────────────────────────────
  static const Color primary = Color(0xFF1D4ED8);         // blue-700
  static const Color primaryHover = Color(0xFF1E40AF);    // blue-800
  static const Color primaryLight = Color(0xFF3B82F6);    // blue-500
  static const Color primaryBg = Color(0xFFEFF6FF);       // blue-50

  static const Color success = Color(0xFF059669);         // emerald-600
  static const Color successLight = Color(0xFFECFDF5);    // emerald-50
  static const Color warning = Color(0xFFD97706);         // amber-600
  static const Color warningLight = Color(0xFFFFFBEB);    // amber-50
  static const Color danger = Color(0xFFDC2626);          // red-600
  static const Color dangerLight = Color(0xFFFEF2F2);     // red-50

  static const Color background = Color(0xFFF8FAFC);     // slate-50
  static const Color surface = Color(0xFFFFFFFF);         // white
  static const Color surfaceSecondary = Color(0xFFF1F5F9);// slate-100

  static const Color textPrimary = Color(0xFF0F172A);    // slate-900
  static const Color textSecondary = Color(0xFF475569);  // slate-600
  static const Color textTertiary = Color(0xFF94A3B8);   // slate-400
  static const Color textMuted = Color(0xFF64748B);      // slate-500

  static const Color border = Color(0xFFE2E8F0);          // slate-200
  static const Color separator = Color(0xFFE2E8F0);       // alias for divider/separator
  static const Color fill = Color(0xFFF1F5F9);            // input fill
  static const Color borderDashed = Color(0xFFCBD5E1);    // slate-300 fine dashed

  // ── Glassmorphism ──────────────────────────────────────
  static const double glassBlurSigma = 30.0;
  static const Color glassColor = Color(0xCCF8FAFC);
  static const Color glassBorder = Color(0x33FFFFFF);

  // ── Border Radius ──────────────────────────────────────
  static const double radiusSmall = 8.0;
  static const double radiusMedium = 12.0;
  static const double radiusLarge = 16.0;
  static const double radiusXLarge = 24.0;
  static const double radiusRound = 999.0;

  // ── Shadows ────────────────────────────────────────────
  static List<BoxShadow> get shadowSmall => [
    BoxShadow(
      color: const Color(0xFF0F172A).withValues(alpha: 0.04),
      blurRadius: 6,
      offset: const Offset(0, 2),
    ),
  ];

  static List<BoxShadow> get shadowMedium => [
    BoxShadow(
      color: const Color(0xFF0F172A).withValues(alpha: 0.08),
      blurRadius: 16,
      offset: const Offset(0, 4),
    ),
  ];

  static List<BoxShadow> get shadowReceipt => [
    BoxShadow(
      color: const Color(0xFF0F172A).withValues(alpha: 0.12),
      blurRadius: 24,
      offset: const Offset(0, 8),
    ),
  ];

  // ── Typography ─────────────────────────────────────────
  static const TextStyle largeTitle = TextStyle(
    fontSize: 26,
    fontWeight: FontWeight.w800,
    letterSpacing: -0.5,
    color: textPrimary,
  );

  static const TextStyle title1 = TextStyle(
    fontSize: 22,
    fontWeight: FontWeight.w700,
    letterSpacing: -0.3,
    color: textPrimary,
  );

  static const TextStyle title2 = TextStyle(
    fontSize: 18,
    fontWeight: FontWeight.w700,
    letterSpacing: -0.2,
    color: textPrimary,
  );

  static const TextStyle title3 = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: textPrimary,
  );

  static const TextStyle headline = TextStyle(
    fontSize: 15,
    fontWeight: FontWeight.w700,
    color: textPrimary,
  );

  static const TextStyle body = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.w500,
    color: textPrimary,
  );

  static const TextStyle callout = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.w500,
    color: textPrimary,
  );

  static const TextStyle subhead = TextStyle(
    fontSize: 13,
    fontWeight: FontWeight.w500,
    color: textSecondary,
  );

  static const TextStyle footnote = TextStyle(
    fontSize: 12,
    fontWeight: FontWeight.w500,
    color: textSecondary,
  );

  static const TextStyle caption1 = TextStyle(
    fontSize: 11,
    fontWeight: FontWeight.w500,
    color: textMuted,
  );

  static const TextStyle caption2 = TextStyle(
    fontSize: 10,
    fontWeight: FontWeight.w500,
    color: textTertiary,
  );

  // ── Spacing ────────────────────────────────────────────
  static const double spacingXS = 4.0;
  static const double spacingS = 8.0;
  static const double spacingM = 16.0;
  static const double spacingL = 24.0;
  static const double spacingXL = 32.0;
  static const double spacingXXL = 48.0;

  // ── Animation Durations ────────────────────────────────
  static const Duration animFast = Duration(milliseconds: 150);
  static const Duration animMedium = Duration(milliseconds: 250);
  static const Duration animSlow = Duration(milliseconds: 500);

  // ── ThemeData ──────────────────────────────────────────
  static ThemeData get lightTheme => ThemeData(
    useMaterial3: true,
    brightness: Brightness.light,
    scaffoldBackgroundColor: background,
    primaryColor: primary,
    colorScheme: const ColorScheme.light(
      primary: primary,
      secondary: primaryLight,
      surface: surface,
      error: danger,
    ),
    appBarTheme: const AppBarTheme(
      backgroundColor: primary,
      elevation: 0,
      scrolledUnderElevation: 0,
      titleTextStyle: TextStyle(
        fontSize: 16,
        fontWeight: FontWeight.w700,
        color: Colors.white,
      ),
      iconTheme: IconThemeData(color: Colors.white),
    ),
    cardTheme: CardThemeData(
      color: surface,
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(radiusLarge),
        side: const BorderSide(color: border, width: 1),
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: primary,
        foregroundColor: Colors.white,
        elevation: 0,
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(radiusMedium),
        ),
        textStyle: const TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.w700,
        ),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: surfaceSecondary,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMedium),
        borderSide: const BorderSide(color: border),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMedium),
        borderSide: const BorderSide(color: border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMedium),
        borderSide: const BorderSide(color: primary, width: 1.5),
      ),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      hintStyle: subhead.copyWith(color: textTertiary),
    ),
    dividerTheme: const DividerThemeData(
      color: border,
      thickness: 1,
    ),
  );
}
