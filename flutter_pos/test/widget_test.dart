import 'package:flutter_test/flutter_test.dart';
import 'package:syafa_pos/main.dart';

void main() {
  testWidgets('App launches without errors', (WidgetTester tester) async {
    await tester.pumpWidget(const SyafaPosApp());
    // Verify splash screen shows
    expect(find.text('Syafa Frozen'), findsOneWidget);
    expect(find.text('Point of Sale'), findsOneWidget);
  });
}
