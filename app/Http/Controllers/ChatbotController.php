<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    /**
     * Danh sách sản phẩm inject vào context.
     * Sau này có thể đọc từ DB động: Product::active()->get()
     */
    private function getProductContext(): string
    {
        try {
            $products = Product::where('is_active', true)
                ->with(['images' => fn($q) => $q->where('is_primary', true)])
                ->latest()
                ->take(20)
                ->get();

            if ($products->isEmpty()) {
                return "Hiện chưa có sản phẩm nào.";
            }

            return $products->map(function ($p) {
                $price = number_format($p->base_price, 0, ',', '.') . '₫';
                if ($p->discount_percent > 0) {
                    $salePrice = number_format($p->effective_price, 0, ',', '.') . '₫';
                    return "- {$p->name}: ~~{$price}~~ → **{$salePrice}** (giảm {$p->discount_percent}% 🔥)";
                }
                return "- {$p->name}: {$price}";
            })->join("\n");
        } catch (\Exception $e) {
            return "Đang tải danh sách sản phẩm...";
        }
    }

    /**
     * Lấy danh sách sản phẩm đang sale.
     */
    private function getSaleProductContext(): ?string
    {
        try {
            $products = Product::where('is_active', true)
                ->where('discount_percent', '>', 0)
                ->latest()
                ->get();

            if ($products->isEmpty()) {
                return null;
            }

            return $products->map(function ($p) {
                $original  = number_format($p->base_price, 0, ',', '.') . '₫';
                $salePrice = number_format($p->effective_price, 0, ',', '.') . '₫';
                return "- **{$p->name}**: ~~{$original}~~ → **{$salePrice}** (-{$p->discount_percent}%)";
            })->join("\n");
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Main chatbot reply endpoint.
     */
    public function reply(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMsg = trim($request->input('message'));
        $reply   = $this->processMessage($userMsg);

        return response()->json([
            'reply'     => $reply,
            'timestamp' => now()->format('H:i'),
        ]);
    }

    /**
     * Rule-based engine — match patterns và trả lời tự nhiên.
     * Cấu trúc dễ thay bằng Gemini API sau: chỉ cần đổi hàm này.
     */
    private function processMessage(string $msg): string
    {
        $msg   = mb_strtolower($msg, 'UTF-8');
        $words = $this->normalizeVietnamese($msg);

        // ── CHÀO HỎI ──────────────────────────────────────────────
        if ($this->matches($words, ['xin chào', 'hello', 'hi', 'chào', 'alo', 'helo', 'hey'])) {
            $greetings = [
                "Xin chào! 👋 Mình là Lumi, trợ lý của Lumiere. Mình có thể giúp gì cho bạn hôm nay?",
                "Chào bạn! ✨ Bạn đang tìm kiếm trang phục gì? Mình sẵn sàng tư vấn!",
                "Hi bạn! 🌿 Chào mừng đến với Lumiere. Bạn cần hỗ trợ gì không?",
            ];
            return $greetings[array_rand($greetings)];
        }

        // ── CẢM ƠN ──────────────────────────────────────────────
        if ($this->matches($words, ['cảm ơn', 'cam on', 'thanks', 'thank you', 'camon'])) {
            return "Không có chi bạn! 😊 Nếu cần thêm gì cứ hỏi mình nhé. Chúc bạn mua sắm vui vẻ tại Lumiere!";
        }

        // ── SALE / GIẢM GIÁ ──────────────────────────────────────
        if ($this->matches($words, ['sale', 'giảm giá', 'giam gia', 'khuyến mãi', 'khuyen mai', 'ưu đãi', 'uu dai', 'hàng sale', 'đang sale', 'giảm', 'giam', 'flash sale'])) {
            $saleList = $this->getSaleProductContext();
            if ($saleList) {
                return "🔥 **Sản phẩm đang SALE tại Lumiere:**\n\n{$saleList}\n\n👉 [Xem tất cả sản phẩm →](/collection)\n\nMình có thể tư vấn thêm về sản phẩm nào bạn nhé!";
            }
            return "Hiện tại Lumiere chưa có sản phẩm nào đang sale bạn ơi 🙏\n\nNhưng đừng bỏ lỡ — theo dõi [bộ sưu tập mới nhất →](/collection) để cập nhật ưu đãi sớm nhất nhé!";
        }

        // ── SẢN PHẨM ──────────────────────────────────────────────
        if ($this->matches($words, ['sản phẩm', 'san pham', 'hàng', 'có gì', 'bán gì', 'mua gì', 'xem', 'shop'])) {
            $productList = $this->getProductContext();
            return "Dưới đây là một số sản phẩm nổi bật của Lumiere: 🛍️\n\n{$productList}\n\nBạn quan tâm đến loại nào? Mình có thể tư vấn thêm!";
        }

        // ── GIÁ CẢ ──────────────────────────────────────────────
        if ($this->matches($words, ['giá', 'bao nhiêu', 'bao nhieu', 'price', 'mắc', 'rẻ', 'đắt'])) {
            return "Sản phẩm tại Lumiere có mức giá từ **2.375.000₫** đến **7.000.000₫** tùy dòng và chất liệu. 💎\n\nBạn muốn xem chi tiết sản phẩm nào? Mình có thể hỗ trợ thêm!";
        }

        // ── SIZE / KÍCH THƯỚC ──────────────────────────────────────
        if ($this->matches($words, ['size', 'kích thước', 'kich thuoc', 'cỡ', 'co', 'số', 'so'])) {
            return "Lumiere hiện có các size: **S, M, L** 📏\n\n**Hướng dẫn chọn size:**\n• Size S: phù hợp dáng nhỏ, 48-50kg\n• Size M: phù hợp 51-58kg\n• Size L: phù hợp 59-65kg\n\nNếu bạn ở ranh giới 2 size, nên chọn size lớn hơn để thoải mái hơn nhé!";
        }

        // ── MÀU SẮC ──────────────────────────────────────────────
        if ($this->matches($words, ['màu', 'mau', 'color', 'đen', 'trắng', 'be', 'xanh', 'đỏ'])) {
            return "Lumiere hiện có các màu: **Đen, Trắng, Be** 🎨\n\nCác màu này dễ phối đồ và phù hợp nhiều phong cách. Bạn thích màu nào?";
        }

        // ── CHẤT LIỆU ──────────────────────────────────────────────
        if ($this->matches($words, ['chất liệu', 'chat lieu', 'vải', 'vai', 'cotton', 'linen', 'lụa', 'lua', 'material'])) {
            return "Lumiere dùng 3 chất liệu cao cấp: 🌿\n\n• **Cotton**: Mềm mại, thấm hút tốt, thích hợp mặc hàng ngày\n• **Linen**: Thoáng mát, nhẹ nhàng, lý tưởng cho thời tiết nóng\n• **Lụa**: Sang trọng, óng mượt, phù hợp dịp đặc biệt\n\nBạn muốn xem sản phẩm theo chất liệu nào?";
        }

        // ── ĐẶT HÀNG ──────────────────────────────────────────────
        if ($this->matches($words, ['đặt hàng', 'dat hang', 'mua', 'order', 'đặt', 'dat', 'cách mua'])) {
            return "Đặt hàng tại Lumiere rất đơn giản! 🛒\n\n1. Chọn sản phẩm → Chọn size, màu\n2. Thêm vào **giỏ hàng**\n3. Thanh toán → Nhập thông tin giao hàng\n4. Chọn phương thức thanh toán\n5. Xác nhận đơn hàng ✅\n\nBạn có cần hỗ trợ bước nào không?";
        }

        // ── THANH TOÁN ──────────────────────────────────────────────
        if ($this->matches($words, ['thanh toán', 'thanh toan', 'payment', 'trả tiền', 'tra tien', 'cod', 'momo', 'visa', 'chuyển khoản'])) {
            return "Lumiere hỗ trợ nhiều hình thức thanh toán: 💳\n\n• **COD** — Thanh toán khi nhận hàng\n• **MoMo** — Ví điện tử\n• **Visa / MasterCard** — Thẻ tín dụng/ghi nợ\n• **ZaloPay** — Ví điện tử\n• **Chuyển khoản** ngân hàng\n\nBạn muốn dùng phương thức nào?";
        }

        // ── MÃ GIẢM GIÁ / KHUYẾN MÃI ──────────────────────────────
        if ($this->matches($words, ['mã giảm giá', 'ma giam gia', 'coupon', 'voucher', 'discount', 'khuyến mãi', 'khuyen mai', 'ưu đãi', 'uu dai'])) {
            return "Để áp dụng mã giảm giá tại Lumiere: 🎁\n\n1. Thêm sản phẩm vào giỏ hàng\n2. Tiến hành thanh toán\n3. Ở bước **\"Thông tin đơn hàng\"**, nhập mã vào ô **\"Mã giảm giá\"**\n4. Nhấn **Áp dụng**\n\nNếu bạn có mã, hãy thử ngay nhé!";
        }

        // ── GIAO HÀNG / VẬN CHUYỂN ──────────────────────────────
        if ($this->matches($words, ['giao hàng', 'giao hang', 'vận chuyển', 'van chuyen', 'ship', 'shipping', 'bao lâu', 'bao lau', 'khi nào', 'khi nao'])) {
            return "Thời gian giao hàng của Lumiere: 🚚\n\n• **Nội thành** (TP.HCM, Hà Nội...): **1–2 ngày** làm việc\n• **Ngoại thành & tỉnh khác**: **3–5 ngày** làm việc\n\nLumiere sẽ gửi thông báo cho bạn khi đơn hàng được gửi đi nhé!";
        }

        // ── ĐỔI TRẢ / HOÀN TIỀN ──────────────────────────────────
        if ($this->matches($words, ['đổi trả', 'doi tra', 'hoàn tiền', 'hoan tien', 'trả hàng', 'tra hang', 'return', 'refund', 'đổi', 'doi'])) {
            return "Chính sách đổi trả của Lumiere: 🔄\n\n✅ Đổi/trả trong **14 ngày** kể từ ngày nhận\n✅ Sản phẩm còn nguyên **tag**, chưa qua sử dụng\n✅ Không bị lỗi do khách hàng gây ra\n\nĐể đổi trả, liên hệ admin qua chat hoặc email để được hỗ trợ nhanh nhất!";
        }

        // ── KIỂM TRA ĐƠN HÀNG ──────────────────────────────────
        if ($this->matches($words, ['đơn hàng', 'don hang', 'kiểm tra', 'kiem tra', 'trạng thái', 'trang thai', 'order', 'đơn của tôi'])) {
            if (Auth::check()) {
                return "Để xem đơn hàng của bạn: 📦\n\n👉 **Tài khoản → Đơn hàng của tôi**\n\nHoặc nhấn vào đây: [Xem đơn hàng](/profile/orders)\n\nCác trạng thái đơn:\n• ⏳ Chờ xử lý\n• 🚚 Đang giao\n• ✅ Đã giao\n• 🎉 Hoàn thành";
            }
            return "Để xem đơn hàng, bạn cần **đăng nhập** vào tài khoản trước nhé! 🔐\n\nSau đó vào **Tài khoản → Đơn hàng của tôi** để theo dõi.";
        }

        // ── LIÊN HỆ / HỖ TRỢ ──────────────────────────────────────
        if ($this->matches($words, ['liên hệ', 'lien he', 'hỗ trợ', 'ho tro', 'contact', 'support', 'nhân viên', 'nhan vien', 'tư vấn', 'tu van', 'admin'])) {
            return "Bạn có thể liên hệ Lumiere qua: 📬\n\n• **Chat trực tiếp**: [Mở chat](/chat) để nói chuyện với nhân viên\n• **Email**: gpham889@gmail.com\n• **Trang liên hệ**: [Xem tại đây](/contact)\n\nMình sẽ phản hồi trong thời gian sớm nhất!";
        }

        // ── THƯƠNG HIỆU / VỀ CHÚNG TÔI ──────────────────────────
        if ($this->matches($words, ['lumiere', 'cửa hàng', 'cua hang', 'về', 've', 'giới thiệu', 'gioi thieu', 'thương hiệu', 'thuong hieu'])) {
            return "**Lumiere** là thương hiệu thời trang nữ cao cấp của Việt Nam. 🌿✨\n\nChúng mình chuyên về trang phục:\n• Thiết kế tinh tế, nữ tính\n• Chất liệu cao cấp: Cotton, Linen, Lụa\n• Phong cách từ văn phòng đến dạo phố\n• Cam kết thời trang bền vững\n\n[Khám phá bộ sưu tập →](/collection)";
        }

        // ── ÁO / VÁY / QUẦN ──────────────────────────────────────
        if ($this->matches($words, ['áo', 'ao', 'blouse', 'sơ mi', 'so mi', 'khoác', 'khoac', 'len', 'thun'])) {
            return "Lumiere có nhiều mẫu áo đẹp: 👗\n\n• **Áo Quấn Cách Điệu** — 4.000.000₫ (Cotton, Trắng, S/M)\n• **Áo Thun Cơ Bản** — 2.375.000₫ (Linen, Be, M/L)\n• **Áo Khoác Zip Rule** — 4.975.000₫ (Cotton, Đen, L)\n• **Áo Len Chui Đầu Boss** — 7.000.000₫ (Cotton, Trắng, M/L)\n\n[Xem tất cả →](/collection)";
        }

        if ($this->matches($words, ['váy', 'vay', 'đầm', 'dam', 'dress', 'skirt'])) {
            return "Váy & đầm tại Lumiere: 👗✨\n\n• **Váy Sơ Mi** — 6.125.000₫ (Lụa, Đen, Size S)\n\nDáng thanh lịch, phù hợp công sở và dạo phố.\n\n[Xem thêm sản phẩm →](/collection)";
        }

        if ($this->matches($words, ['quần', 'quan', 'pants', 'trouser'])) {
            return "Quần tại Lumiere: 👖\n\n• **Quần Vải Linen** — 4.500.000₫ (Linen, Đen, S/M)\n\nChất liệu linen thoáng mát, kiểu dáng thanh lịch.\n\n[Xem toàn bộ →](/collection)";
        }

        // ── FAQ KHÁC ──────────────────────────────────────────────
        if ($this->matches($words, ['faq', 'hỏi đáp', 'hoi dap', 'câu hỏi', 'cau hoi'])) {
            return "Bạn có thể xem tất cả câu hỏi thường gặp tại: [Trang FAQ →](/faq) 📖\n\nHoặc cứ hỏi mình trực tiếp, mình luôn sẵn sàng!";
        }

        // ── KHÔNG HIỂU ──────────────────────────────────────────
        $fallbacks = [
            "Mình chưa hiểu ý bạn lắm 🤔 Bạn có thể hỏi về:\n\n• Sản phẩm & giá\n• Size & màu sắc\n• Đặt hàng & thanh toán\n• Giao hàng & đổi trả\n• Kiểm tra đơn hàng",
            "Hmm, mình không chắc về câu hỏi này 😅 Thử hỏi về **sản phẩm**, **giao hàng**, hoặc **đổi trả** nhé!\n\nHoặc [chat với nhân viên](/chat) để được hỗ trợ trực tiếp!",
            "Câu hỏi thú vị! Nhưng mình cần thêm thông tin 🌿 Bạn đang hỏi về **sản phẩm**, **đơn hàng**, hay **chính sách** của Lumiere?",
        ];
        return $fallbacks[array_rand($fallbacks)];
    }

    /**
     * Chuẩn hóa tiếng Việt và tách từ.
     */
    private function normalizeVietnamese(string $text): string
    {
        // Giữ nguyên tiếng Việt, chỉ lowercase
        return mb_strtolower($text, 'UTF-8');
    }

    /**
     * Kiểm tra xem tin nhắn có chứa bất kỳ từ khóa nào không.
     */
    private function matches(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (mb_strpos($text, mb_strtolower($kw, 'UTF-8'), 0, 'UTF-8') !== false) {
                return true;
            }
        }
        return false;
    }
}
