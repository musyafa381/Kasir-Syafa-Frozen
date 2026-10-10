import 'dart:ui';
import 'package:flutter/material.dart';
import '../config/theme.dart';

/// ─────────────────────────────────────────────────────────
/// Glass Container — Widget glassmorphism khas iOS
/// Efek frosted glass dengan blur + transparan
/// ─────────────────────────────────────────────────────────
class GlassContainer extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final double? borderRadius;
  final Color? color;
  final double blur;

  const GlassContainer({
    super.key,
    required this.child,
    this.padding,
    this.borderRadius,
    this.color,
    this.blur = AppTheme.glassBlurSigma,
  });

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(borderRadius ?? AppTheme.radiusLarge),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: blur, sigmaY: blur),
        child: Container(
          padding: padding ?? const EdgeInsets.all(AppTheme.spacingM),
          decoration: BoxDecoration(
            color: color ?? AppTheme.glassColor,
            borderRadius: BorderRadius.circular(borderRadius ?? AppTheme.radiusLarge),
            border: Border.all(
              color: AppTheme.glassBorder,
              width: 0.5,
            ),
          ),
          child: child,
        ),
      ),
    );
  }
}
