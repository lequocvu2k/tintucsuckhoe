<?php
// health_faq.php
header('Content-Type: application/json; charset=utf-8');

// Không cần login, chỉ trả lời chung
$question = strtolower(trim($_POST['question'] ?? ''));

if ($question === '') {
    echo json_encode(['answer' => 'Bạn vui lòng nhập câu hỏi nhé. 😊']);
    exit;
}

// Bộ FAQ đơn giản (rule-based)
$faqs = [
    'bmi' => "BMI là chỉ số khối cơ thể, được tính bằng cân nặng (kg) / [chiều cao (m)]². BMI giúp ước lượng gầy, bình thường, thừa cân hoặc béo phì, nhưng không thay thế chẩn đoán của bác sĩ.",
    'ngủ' => "Người trưởng thành thường nên ngủ khoảng 7–9 tiếng mỗi ngày. Thiếu ngủ lâu dài có thể gây mệt mỏi, giảm tập trung, tăng nguy cơ bệnh tim mạch, béo phì,…",
    'nước' => "Một cách ước lượng là khoảng 30–35 ml nước / kg cân nặng mỗi ngày. Ví dụ 50kg → khoảng 1.5–1.8 lít/ngày (tùy thời tiết và mức vận động).",
    'tập luyện' => "Khuyến nghị chung: ít nhất 150 phút hoạt động thể lực mức vừa mỗi tuần (hoặc 75 phút mức mạnh), có thể chia nhỏ 20–30 phút mỗi ngày, kèm bài tập cơ bắp 2 ngày/tuần.",
    'ăn uống' => "Cố gắng ăn đa dạng: nhiều rau xanh, trái cây, hạn chế đồ chiên rán, nước ngọt có gas, thực phẩm siêu chế biến. Cân bằng giữa tinh bột, đạm, chất béo tốt và chất xơ.",
    'giảm cân' => "Giảm cân an toàn thường ở mức 0.5–1kg/tuần. Kết hợp giảm nhẹ calo ăn vào, tăng vận động, uống đủ nước, ngủ đủ. Không nên nhịn ăn quá mức hoặc dùng thuốc không rõ nguồn gốc.",
    'tăng cân' => "Muốn tăng cân lành mạnh: tăng dần lượng calo, ăn thêm bữa phụ giàu đạm (sữa, sữa chua, hạt, trứng), tập luyện sức mạnh để tăng cơ, ngủ đủ giấc.",
    'stress' => "Khi bị stress, có thể thử: hít thở sâu, tập thể dục nhẹ, nghe nhạc thư giãn, chia sẻ với người thân/bạn bè. Nếu kéo dài, nên gặp chuyên gia tâm lý hoặc bác sĩ.",
];

$answer = null;

foreach ($faqs as $keyword => $text) {
    if (strpos($question, $keyword) !== false) {
        $answer = $text;
        break;
    }
}

if (!$answer) {
    $answer = "Hiện mình chỉ trả lời các câu hỏi chung về BMI, ngủ, uống nước, ăn uống, giảm cân, tăng cân, tập luyện, stress... Bạn thử hỏi lại cụ thể về một trong các chủ đề đó nhé. 💬";
}

echo json_encode(['answer' => $answer]);
