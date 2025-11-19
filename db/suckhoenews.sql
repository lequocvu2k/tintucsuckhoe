-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 19, 2025 lúc 02:46 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `suckhoenews`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baiviet`
--

CREATE TABLE `baiviet` (
  `ma_bai_viet` int(11) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `duong_dan` varchar(255) DEFAULT NULL,
  `noi_dung` text NOT NULL,
  `anh_bv` varchar(255) DEFAULT NULL,
  `ma_tac_gia` int(11) DEFAULT NULL,
  `ma_chuyen_muc` int(11) DEFAULT NULL,
  `ngay_dang` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT NULL,
  `trang_thai` varchar(20) NOT NULL DEFAULT 'draft',
  `luot_xem` int(11) NOT NULL DEFAULT 0,
  `danh_muc` varchar(50) DEFAULT NULL,
  `id_kh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `baiviet`
--

INSERT INTO `baiviet` (`ma_bai_viet`, `tieu_de`, `duong_dan`, `noi_dung`, `anh_bv`, `ma_tac_gia`, `ma_chuyen_muc`, `ngay_dang`, `ngay_cap_nhat`, `trang_thai`, `luot_xem`, `danh_muc`, `id_kh`) VALUES
(10, '5 món ăn uống giúp gan khỏe', '5-mon-an-uong-giup-gan-khoe', '<p>Ăn tỏi, củ dền kết hợp uống tr&agrave; xanh cung cấp c&aacute;c chất chống oxy h&oacute;a, giảm vi&ecirc;m v&agrave; bớt tổn thương gan, g&oacute;p phần phục hồi khi gan suy yếu.</p>\r\n<p>Gan suy yếu thường c&oacute; triệu chứng như mệt mỏi, đầy hơi, t&aacute;o b&oacute;n, nổi mụn trứng c&aacute;, v&agrave;ng da. D&ugrave; tổn thương gan nặng thường kh&ocirc;ng thể phục hồi song nếu nhẹ c&oacute; thể được kiểm so&aacute;t bằng c&aacute;ch thay đổi lối sống v&agrave; chế độ ăn gi&agrave;u chất chống oxy h&oacute;a, chất xơ v&agrave; c&aacute;c chất dinh dưỡng thiết yếu. Dưới đ&acirc;y l&agrave; 5 loại thực phẩm gi&uacute;p tăng chức năng gan v&agrave; giải độc. Tr&agrave; xanh với h&agrave;m lượng catechin cao, g&oacute;p phần nồng độ enzyme về mức c&acirc;n bằng v&agrave; l&agrave;m chậm qu&aacute; tr&igrave;nh t&iacute;ch tụ mỡ gan. Duy tr&igrave; th&oacute;i quen uống 2-3 t&aacute;ch tr&agrave; xanh mỗi ng&agrave;y hỗ trợ cơ quan n&agrave;y hoạt động trơn tru, cung cấp nước v&agrave; c&aacute;c lợi &iacute;ch sức khỏe kh&aacute;c.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/10/cu-den-1-1762763795.png?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=Y1WKdFKBhUvb8HEwNflGNw\" alt=\"\" width=\"646\" height=\"473\" /></p>\r\n<p>Củ dền chứa nhiều betalain, sắc tố kh&ocirc;ng chỉ tạo n&ecirc;n m&agrave;u nổi bật cho loại củ n&agrave;y m&agrave; c&ograve;n tham gia giải độc gan v&agrave; giảm stress oxy h&oacute;a. Ch&uacute;ng cũng th&uacute;c đẩy c&aacute;c enzyme ph&acirc;n hủy độc tố, bảo vệ tế b&agrave;o gan khỏi bị tổn thương. Uống nước &eacute;p củ dền cũng cải thiện chức năng gan, giảm t&iacute;ch tụ mỡ trong gan. T&aacute;c dụng chống oxy h&oacute;a của củ dền cũng g&oacute;p phần ngăn tiến triển của bệnh gan.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/10/sua-nghe-1762763848.png?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=7sYgjujWaKYK3ONFx_Cc0A\" alt=\"\" width=\"646\" height=\"473\" /></p>\r\n<p>Nghệ cung cấp curcumin vừa chống vi&ecirc;m vừa chống oxy h&oacute;a, gi&uacute;p l&agrave;m dịu t&igrave;nh trạng vi&ecirc;m gan v&agrave; bảo vệ cơ quan n&agrave;y khỏi tổn thương. Bổ sung curcumin từ củ nghệ c&oacute; thể cải thiện hoạt động của men gan, l&agrave;m giảm xơ h&oacute;a, loại m&ocirc; h&igrave;nh th&agrave;nh trong bệnh gan.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/10/toi-1762764025.jpg?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=alhjUscj5oaBKaLKooJlNw\" alt=\"\" width=\"646\" height=\"473\" /></p>\r\n<p>Tỏi gi&agrave;u c&aacute;c hợp chất lưu huỳnh, gi&uacute;p tăng cường hoạt động của enzyme gan, hỗ trợ cơ thể đ&agrave;o thải độc tố. Allicin v&agrave; selen trong củ tỏi hoạt động như \"bộ đ&ocirc;i\" bảo vệ gan khỏi bị tổn thương, tăng cường qu&aacute; tr&igrave;nh thanh lọc tự nhi&ecirc;n của gan.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/10/rau-bina-1762763819.png?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=RSWKCwGLRYC5kDZK6_C2DQ\" alt=\"\" width=\"646\" height=\"473\" /></p>\r\n<p>Rau xanh như cải b&oacute; x&ocirc;i, cải xoăn, rau arugula&hellip; chứa nhiều diệp lục c&oacute; khả năng li&ecirc;n kết v&agrave; gi&uacute;p cơ thể loại bỏ một số kim loại nặng, h&oacute;a chất c&ocirc;ng nghiệp, dư lượng thuốc trừ s&acirc;u khỏi m&aacute;u. Ch&uacute;ng cũng cải thiện sản xuất mật - yếu tố quan trọng cho qu&aacute; tr&igrave;nh ti&ecirc;u h&oacute;a v&agrave; b&agrave;i tiết độc tố của cơ thể. C&aacute;c loại rau như b&ocirc;ng cải xanh v&agrave; cải brussels chứa một loạt c&aacute;c hợp chất th&uacute;c đẩy c&aacute;c enzyme giải độc gan hoạt động, tăng khả năng thanh lọc của cơ quan n&agrave;y.</p>', 'uploads/baiviet/1762954891_tra-xanh-1762763206[1].png', 1, 2, '2025-11-12 20:41:31', '2025-11-12 20:43:18', 'published', 20, 'EDITOR\'S PICKS', 9),
(11, 'Bệnh Kawasaki', 'benh-kawasaki', '<p><span style=\"color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">Bệnh Kawasaki l&agrave; một t&igrave;nh trạng g&acirc;y vi&ecirc;m mạch m&aacute;u v&agrave; chủ yếu ảnh hưởng đến trẻ em dưới 5 tuổi.</span></p>\r\n<h2 class=\"title-block-content-detail bg-grey\" style=\"margin: 0px 0px 12px; padding: 8.5px 24px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 32px; font-size: 20px; color: #222222; font-variant-numeric: lining-nums proportional-nums; font-family: Merriweather; background: #e5e5e5;\">Triệu chứng</h2>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">&nbsp;</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Giai đoạn một:</em></p>\r\n<ul class=\"ul-temp\" style=\"margin: 0px; padding: 0px 0px 0px 18px; box-sizing: border-box; text-rendering: optimizelegibility; list-style-type: none; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Sốt cao (thường tr&ecirc;n 38 độ C) k&eacute;o d&agrave;i &iacute;t nhất 5 ng&agrave;y</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Đau mắt đỏ (vi&ecirc;m kết mạc) kh&ocirc;ng g&acirc;y chảy dịch đặc</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Ph&aacute;t ban, nhất l&agrave; ở th&acirc;n m&igrave;nh hoặc v&ugrave;ng sinh dục</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">M&ocirc;i đỏ, kh&ocirc;, nứt nẻ</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Lưỡi sưng đỏ (c&ograve;n gọi l&agrave; lưỡi d&acirc;u t&acirc;y)</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">L&ograve;ng b&agrave;n tay v&agrave; l&ograve;ng b&agrave;n ch&acirc;n sưng đỏ</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Hạch bạch huyết ở cổ</span></li>\r\n</ul>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Giai đoạn hai:</em></p>\r\n<ul class=\"ul-temp\" style=\"margin: 0px; padding: 0px 0px 0px 18px; box-sizing: border-box; text-rendering: optimizelegibility; list-style-type: none; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Tr&oacute;c da ở tay v&agrave; ch&acirc;n, nhất l&agrave; ở đầu ng&oacute;n tay, ng&oacute;n ch&acirc;n</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Đau khớp</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Ti&ecirc;u chảy</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">N&ocirc;n mửa</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Đau dạ d&agrave;y</span></li>\r\n</ul>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Giai đoạn ba:</em></p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Ở giai đoạn ba, c&aacute;c triệu chứng dần biến mất nếu kh&ocirc;ng c&oacute; biến chứng. Sốt thường hết, nhưng trẻ em c&oacute; thể mất đến một th&aacute;ng để ho&agrave;n to&agrave;n cảm thấy b&igrave;nh thường trở lại.</span></p>\r\n<h2 class=\"title-block-content-detail bg-grey\" style=\"margin: 0px 0px 12px; padding: 8.5px 24px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 32px; font-size: 20px; color: #222222; font-variant-numeric: lining-nums proportional-nums; font-family: Merriweather; background: #e5e5e5;\">Nguy&ecirc;n nh&acirc;n</h2>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">&nbsp;</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Bệnh Kawasaki kh&ocirc;ng l&acirc;y nhiễm. Virus hoặc nhiễm tr&ugrave;ng g&acirc;y ra phản ứng bất thường của hệ miễn dịch ở trẻ em c&oacute; yếu tố di truyền dễ mắc bệnh. Một số biến thể gene nhất định c&oacute; li&ecirc;n quan đến tăng nguy cơ mắc bệnh Kawasaki.</span></p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">&nbsp;</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Trẻ em c&oacute; cha mẹ mắc bệnh n&agrave;y c&oacute; nguy cơ cao gấp đ&ocirc;i. Nếu trẻ c&oacute; anh chị em ruột mắc bệnh th&igrave; nguy cơ cao gấp 10 lần so với trẻ kh&ocirc;ng c&oacute; yếu tố gia đ&igrave;nh.</span></p>\r\n<h2 class=\"title-block-content-detail bg-grey\" style=\"margin: 0px 0px 12px; padding: 8.5px 24px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 32px; font-size: 20px; color: #222222; font-variant-numeric: lining-nums proportional-nums; font-family: Merriweather; background: #e5e5e5;\">Nguy cơ</h2>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">&nbsp;</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">C&aacute;c yếu tố nguy cơ:</span></p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">&nbsp;</p>\r\n<ul class=\"ul-temp\" style=\"margin: 0px; padding: 0px 0px 0px 18px; box-sizing: border-box; text-rendering: optimizelegibility; list-style-type: none; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Giới t&iacute;nh nam</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Từ 6 th&aacute;ng đến 5 tuổi</span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">C&oacute; tiền sử gia đ&igrave;nh mắc bệnh.</span>\r\n<h2 class=\"title-block-content-detail bg-grey\" style=\"margin: 0px 0px 12px; padding: 8.5px 24px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 32px; font-size: 20px; font-variant-numeric: lining-nums proportional-nums; font-family: Merriweather; background: #e5e5e5;\">Chẩn đo&aacute;n</h2>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px;\">&nbsp;</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Kh&ocirc;ng c&oacute; x&eacute;t nghiệm cụ thể n&agrave;o để chẩn đo&aacute;n bệnh Kawasaki. Bệnh c&oacute; nhiều đặc điểm tương tự như c&aacute;c bệnh nhi khoa kh&aacute;c, khiến kh&oacute; chẩn đo&aacute;n. Nếu con bị sốt li&ecirc;n tục trong 4 ng&agrave;y hoặc l&acirc;u hơn, k&egrave;m theo sưng tay, mắt đỏ, m&ocirc;i đỏ, nứt nẻ v&agrave; ph&aacute;t ban, đặc biệt nếu trẻ đ&atilde; d&ugrave;ng kh&aacute;ng sinh m&agrave; cơn sốt vẫn kh&ocirc;ng giảm, cha mẹ cần đưa con đi kh&aacute;m.</span></span></span></span></span></span></span></p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Trẻ mắc bệnh Kawasaki thường c&oacute; xu hướng sốt từ 5 ng&agrave;y trở l&ecirc;n v&agrave; c&oacute; &iacute;t nhất 4 trong 5 triệu chứng sau:</span></span></span></span></span></span></span></p>\r\n<ul class=\"ul-temp\" style=\"margin: 0px; padding: 0px 0px 0px 18px; box-sizing: border-box; text-rendering: optimizelegibility; list-style-type: none;\">\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Mắt đỏ kh&ocirc;ng chảy dịch</span></span></span></span></span></span></span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">M&ocirc;i đỏ, kh&ocirc;, nứt nẻ v&agrave; lưỡi chuyển từ m&agrave;u hồng sang đỏ</span></span></span></span></span></span></span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">B&agrave;n tay, b&agrave;n ch&acirc;n sưng đỏ, bong tr&oacute;c</span></span></span></span></span></span></span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Ph&aacute;t ban đỏ, từng mảng tr&ecirc;n th&acirc;n m&igrave;nh</span></span></span></span></span></span></span></li>\r\n<li style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; list-style: outside disc;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Hạch bạch huyết ở cổ sưng, đau</span></span></span></span></span></span></span></li>\r\n</ul>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">X&eacute;t nghiệm m&aacute;u, x&eacute;t nghiệm nước tiểu v&agrave; nu&ocirc;i cấy dịch họng cũng c&oacute; thể được thực hiện để loại trừ c&aacute;c t&igrave;nh trạng kh&aacute;c g&acirc;y ra c&aacute;c triệu chứng tương tự. Sau khi chẩn đo&aacute;n bệnh Kawasaki, b&aacute;c sĩ c&oacute; thể y&ecirc;u cầu chụp điện t&acirc;m đồ (ECG) v&agrave; si&ecirc;u &acirc;m tim để chẩn đo&aacute;n c&aacute;c vấn đề về tim như ph&igrave;nh động mạch v&agrave;nh.</span></span></span></span></span></span></span></p>\r\n<h2 class=\"title-block-content-detail bg-grey\" style=\"margin: 0px 0px 12px; padding: 8.5px 24px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 32px; font-size: 20px; font-variant-numeric: lining-nums proportional-nums; font-family: Merriweather; background: #e5e5e5;\">Điều trị</h2>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px;\">&nbsp;</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\"><span style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Trẻ c&oacute; thể d&ugrave;ng một số loại thuốc theo chỉ định của b&aacute;c sĩ. C&aacute;c phương ph&aacute;p điều trị n&agrave;y c&oacute; thể l&agrave;m giảm đ&aacute;ng kể nguy cơ biến chứng tim nghi&ecirc;m trọng v&agrave; n&ecirc;n d&ugrave;ng c&agrave;ng sớm c&agrave;ng tốt.</span></span></span></span></span></span></span></p>\r\n</li>\r\n</ul>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #fcfaf6;\">&nbsp;</p>', 'uploads/baiviet/1762955674_anhembe-1762832207-1762832217-2565-1762833683[1].png', 1, 6, '2025-11-12 20:54:34', '2025-11-12 20:59:01', 'published', 35, 'MAIN HIGHLIGHTS', 9),
(12, 'Phát hiện sản phẩm giảm cân giả mạo bản công bố', 'phat-hien-san-pham-giam-can-gia-mao-ban-cong-bo', '<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Bộ Y tế ph&aacute;t hiện sản phẩm Giảm c&acirc;n 12kg MINAMI đang b&aacute;n tr&ecirc;n Shopee l&agrave;m giả giấy ph&eacute;p, y&ecirc;u cầu s&agrave;n thương mại điện tử n&agrave;y gỡ bỏ ngay lập tức. Sản phẩm được quảng c&aacute;o l&agrave; \"Giảm c&acirc;n 12kg MINAMI giảm mỡ bụng\" của C&ocirc;ng ty TNHH Thương mại HKK (trụ sở tại Đống Đa, H&agrave; Nội). </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Tuy nhi&ecirc;n, qua tra cứu, Cục An to&agrave;n thực phẩm khẳng định kh&ocirc;ng cấp Giấy tiếp nhận đăng k&yacute; bản c&ocirc;ng bố sản phẩm số 5438/2024/ĐKSP ng&agrave;y 12/7/2024 cho sản phẩm n&agrave;y. \"Đ&acirc;y l&agrave; bản c&ocirc;ng bố sản phẩm giả mạo\", đại diện Cục An to&agrave;n thực phẩm cho biết, h&ocirc;m 12/11. C&ugrave;ng ng&agrave;y, Cục c&oacute; văn bản y&ecirc;u cầu s&agrave;n thương mại điện tử Shopee ngừng kinh doanh, gỡ bỏ ho&agrave;n to&agrave;n th&ocirc;ng tin về sản phẩm vi phạm. Đơn vị n&agrave;y cũng đề nghị Cục Thương mại điện tử v&agrave; Kinh tế số (Bộ C&ocirc;ng Thương) v&agrave;o cuộc, r&agrave; so&aacute;t to&agrave;n bộ c&aacute;c s&agrave;n, ứng dụng v&agrave; website đang kinh doanh thực phẩm chức năng. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Cơ quan chức năng y&ecirc;u cầu c&aacute;c nền tảng n&agrave;y chỉ cho ph&eacute;p b&aacute;n những sản phẩm đ&atilde; được cấp Giấy tiếp nhận đăng k&yacute; bản c&ocirc;ng bố hoặc đ&atilde; c&oacute; th&ocirc;ng tin tr&ecirc;n Hệ thống dữ liệu về an to&agrave;n thực phẩm. Theo quy định hiện h&agrave;nh, trước khi đưa ra thị trường, tất cả loại thực phẩm chức năng (gồm thực phẩm bổ sung, thực phẩm bảo vệ sức khỏe, thực phẩm dinh dưỡng y học v&agrave; thực phẩm cho chế độ ăn đặc biệt) đều phải đăng k&yacute; hoặc tự c&ocirc;ng bố với cơ quan nh&agrave; nước c&oacute; thẩm quyền. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Trong đ&oacute;, doanh nghiệp phải nộp hồ sơ đăng k&yacute; bản c&ocirc;ng bố đến Cục An to&agrave;n thực phẩm đối với nh&oacute;m thực phẩm bảo vệ sức khỏe. C&aacute;c nh&oacute;m c&ograve;n lại như thực phẩm dinh dưỡng y học hay thực phẩm cho chế độ ăn đặc biệt cần nộp hồ sơ tới cơ quan do UBND cấp tỉnh chỉ định. Ri&ecirc;ng thực phẩm bổ sung, doanh nghiệp được ph&eacute;p tự c&ocirc;ng bố v&agrave; nộp hồ sơ tại địa phương. Việc kh&ocirc;ng tu&acirc;n thủ quy tr&igrave;nh n&agrave;y đồng nghĩa sản phẩm kh&ocirc;ng được ph&eacute;p lưu th&ocirc;ng.</span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Thời gian qua, Bộ Y tế li&ecirc;n tục ph&aacute;t hiện nhiều loại sản phẩm giảm c&acirc;n sai phạm. Mới đ&acirc;y, Sở An to&agrave;n thực phẩm TP HCM cũng khuyến c&aacute;o người d&acirc;n kh&ocirc;ng mua, kh&ocirc;ng sử dụng c&aacute;c sản phẩm thực phẩm giảm c&acirc;n do Ng&acirc;n Collagen quảng c&aacute;o, trong thời gian chờ kết quả kiểm tra của cơ quan chức năng. DJ Ng&acirc;n 98 cũng bị bắt do b&aacute;n thuốc giảm c&acirc;n l&agrave; h&agrave;ng giả, \"vi&ecirc;n Collagen\" c&oacute; chất cấm ảnh hưởng nghi&ecirc;m trọng sức khỏe người ti&ecirc;u d&ugrave;ng.</span></span></p>', 'uploads/baiviet/1762959250_1-1762953332-1762953375-176295-2504-3246-1762953496[1].png', 2, 1, '2025-11-12 21:54:10', '2025-11-12 22:01:20', 'published', 31, 'POPULAR POSTS', 10),
(13, '6 loại rau củ giúp giảm cân', '6-loai-rau-cu-giup-giam-can', '<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\">Dưa leo, rau l&aacute; xanh, họ cải, ớt chu&ocirc;ng, măng t&acirc;y, x&agrave; l&aacute;ch gi&agrave;u chất xơ v&agrave; nước, l&agrave;m tăng cảm gi&aacute;c no l&acirc;u, hỗ trợ giảm c&acirc;n. Chuy&ecirc;n vi&ecirc;n dinh dưỡng Trịnh H&agrave; Nhật Quy&ecirc;n, Đơn vị Nội tiết - Đ&aacute;i th&aacute;o đường, Ph&ograve;ng kh&aacute;m Đa khoa T&acirc;m Anh Quận 7, cho biết nguy&ecirc;n tắc chung của giảm c&acirc;n l&agrave; lượng calo nạp v&agrave;o phải thấp hơn lượng calo cơ thể ti&ecirc;u thụ.</p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\">Người giảm c&acirc;n c&oacute; thể ăn nhiều tr&aacute;i c&acirc;y, rau củ &iacute;t calo thay v&igrave; giảm tổng lượng thức ăn ti&ecirc;u thụ. Hầu hết tr&aacute;i c&acirc;y v&agrave; rau củ đều c&oacute; h&agrave;m lượng chất b&eacute;o v&agrave; calo thấp nhưng nhiều nước v&agrave; chất xơ, gi&uacute;p l&agrave;m tăng thể t&iacute;ch cho m&oacute;n ăn, tạo cảm gi&aacute;c no m&agrave; vẫn kiểm so&aacute;t được lượng calo nạp v&agrave;o.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\">Tr&aacute;i c&acirc;y v&agrave; rau cung cấp c&aacute;c vitamin, kho&aacute;ng chất, chất xơ thiết yếu cho sức khỏe. Ăn tr&aacute;i c&acirc;y, rau củ như một phần của chế độ ăn uống l&agrave;nh mạnh, l&agrave;m giảm nguy cơ mắc một số loại ung thư v&agrave; c&aacute;c bệnh mạn t&iacute;nh.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\">Theo b&aacute;c sĩ Quy&ecirc;n, người trưởng th&agrave;nh n&ecirc;n ăn lượng tr&aacute;i c&acirc;y v&agrave; rau kh&ocirc;ng chứa tinh bột tối thiểu l&agrave; 400 g mỗi ng&agrave;y để hỗ trợ giảm c&acirc;n, ưu ti&ecirc;n c&aacute;c loại rau củ như sau:</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Rau l&aacute; xanh v&agrave; họ cải&nbsp;</em>như cải b&oacute; x&ocirc;i, cải xoăn, s&uacute;p lơ, bắp cải, cải brussel... gi&agrave;u chất xơ c&ugrave;ng chất chống oxy h&oacute;a, hỗ trợ đốt ch&aacute;y mỡ thừa, giảm vi&ecirc;m. Một ch&eacute;n s&uacute;p lơ nấu ch&iacute;n c&oacute; khoảng 2 g protein, 2,9 g chất xơ, 61% đơn vị vitamin C v&agrave; 29 calo. Loại rau n&agrave;y c&oacute; thể nấu ch&iacute;n hoặc ăn sống, chứa nhiều sterol/stanol - hợp chất thực vật c&oacute; t&aacute;c dụng giảm mức cholesterol xấu v&agrave; cải thiện chức năng nội m&ocirc;, quan trọng với sức khỏe tim mạch.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Ớt chu&ocirc;ng</em>&nbsp;được sử dụng linh hoạt cho chế độ ăn uống l&agrave;nh mạnh. Loại rau n&agrave;y c&oacute; lượng calo thấp, nhiều chất dinh dưỡng, th&uacute;c đẩy tăng tốc qu&aacute; tr&igrave;nh trao đổi chất. Khi ăn sống hoặc trộn salad, ớt chu&ocirc;ng đỏ hỗ trợ nhu động ruột v&agrave; cung cấp nhiều vitamin, chất chống oxy h&oacute;a hơn.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Dưa leo</em>&nbsp;chứa hơn 95 g nước, 0,5 g chất xơ, 15 calo trong 100 g dưa leo (nguy&ecirc;n vỏ). Dưa leo cung cấp chủ yếu l&agrave; nước, gi&uacute;p duy tr&igrave; cảm gi&aacute;c no. Bạn c&oacute; thể d&ugrave;ng dưa leo để ăn tươi hoặc chế biến đa dạng c&aacute;c m&oacute;n ăn.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Măng t&acirc;y&nbsp;</em>gi&agrave;u chất xơ, nhiều vitamin v&agrave; kho&aacute;ng chất thiết yếu cho cơ thể như vitamin A, C, E, phốt pho, kali, sắt, canxi... Ăn măng t&acirc;y thường xuy&ecirc;n hỗ trợ đốt calo, giảm mỡ bụng.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">C&agrave; chua</em>&nbsp;c&oacute; 95% khối lượng l&agrave; nước, chứa c&aacute;c chất dinh dưỡng như vitamin C, K, folate, kali, &iacute;t calo. C&agrave; chua rất gi&agrave;u lycopene, chất chống oxy h&oacute;a mạnh gi&uacute;p giảm nguy cơ mắc nhiều bệnh nguy hiểm như ung thư. C&agrave; chua c&oacute; thể được chế biến th&agrave;nh rất nhiều m&oacute;n ăn như salad, canh, s&uacute;p, nước sốt... hỗ trợ giảm c&acirc;n l&agrave;nh mạnh.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Ớt chu&ocirc;ng</em>&nbsp;được sử dụng linh hoạt cho chế độ ăn uống l&agrave;nh mạnh. Loại rau n&agrave;y c&oacute; lượng calo thấp, nhiều chất dinh dưỡng, th&uacute;c đẩy tăng tốc qu&aacute; tr&igrave;nh trao đổi chất. Khi ăn sống hoặc trộn salad, ớt chu&ocirc;ng đỏ hỗ trợ nhu động ruột v&agrave; cung cấp nhiều vitamin, chất chống oxy h&oacute;a hơn.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Dưa leo</em>&nbsp;chứa hơn 95 g nước, 0,5 g chất xơ, 15 calo trong 100 g dưa leo (nguy&ecirc;n vỏ). Dưa leo cung cấp chủ yếu l&agrave; nước, gi&uacute;p duy tr&igrave; cảm gi&aacute;c no. Bạn c&oacute; thể d&ugrave;ng dưa leo để ăn tươi hoặc chế biến đa dạng c&aacute;c m&oacute;n ăn.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Măng t&acirc;y&nbsp;</em>gi&agrave;u chất xơ, nhiều vitamin v&agrave; kho&aacute;ng chất thiết yếu cho cơ thể như vitamin A, C, E, phốt pho, kali, sắt, canxi... Ăn măng t&acirc;y thường xuy&ecirc;n hỗ trợ đốt calo, giảm mỡ bụng.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">C&agrave; chua</em>&nbsp;c&oacute; 95% khối lượng l&agrave; nước, chứa c&aacute;c chất dinh dưỡng như vitamin C, K, folate, kali, &iacute;t calo. C&agrave; chua rất gi&agrave;u lycopene, chất chống oxy h&oacute;a mạnh gi&uacute;p giảm nguy cơ mắc nhiều bệnh nguy hiểm như ung thư. C&agrave; chua c&oacute; thể được chế biến th&agrave;nh rất nhiều m&oacute;n ăn như salad, canh, s&uacute;p, nước sốt... hỗ trợ giảm c&acirc;n l&agrave;nh mạnh.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Ớt chu&ocirc;ng</em>&nbsp;được sử dụng linh hoạt cho chế độ ăn uống l&agrave;nh mạnh. Loại rau n&agrave;y c&oacute; lượng calo thấp, nhiều chất dinh dưỡng, th&uacute;c đẩy tăng tốc qu&aacute; tr&igrave;nh trao đổi chất. Khi ăn sống hoặc trộn salad, ớt chu&ocirc;ng đỏ hỗ trợ nhu động ruột v&agrave; cung cấp nhiều vitamin, chất chống oxy h&oacute;a hơn.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Dưa leo</em>&nbsp;chứa hơn 95 g nước, 0,5 g chất xơ, 15 calo trong 100 g dưa leo (nguy&ecirc;n vỏ). Dưa leo cung cấp chủ yếu l&agrave; nước, gi&uacute;p duy tr&igrave; cảm gi&aacute;c no. Bạn c&oacute; thể d&ugrave;ng dưa leo để ăn tươi hoặc chế biến đa dạng c&aacute;c m&oacute;n ăn.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Măng t&acirc;y&nbsp;</em>gi&agrave;u chất xơ, nhiều vitamin v&agrave; kho&aacute;ng chất thiết yếu cho cơ thể như vitamin A, C, E, phốt pho, kali, sắt, canxi... Ăn măng t&acirc;y thường xuy&ecirc;n hỗ trợ đốt calo, giảm mỡ bụng.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">C&agrave; chua</em>&nbsp;c&oacute; 95% khối lượng l&agrave; nước, chứa c&aacute;c chất dinh dưỡng như vitamin C, K, folate, kali, &iacute;t calo. C&agrave; chua rất gi&agrave;u lycopene, chất chống oxy h&oacute;a mạnh gi&uacute;p giảm nguy cơ mắc nhiều bệnh nguy hiểm như ung thư. C&agrave; chua c&oacute; thể được chế biến th&agrave;nh rất nhiều m&oacute;n ăn như salad, canh, s&uacute;p, nước sốt... hỗ trợ giảm c&acirc;n l&agrave;nh mạnh.</p>\r\n<p class=\"Normal\" style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; text-rendering: optimizespeed; line-height: 28.8px; color: #222222; font-family: arial; font-size: 18px; background-color: #ffffff;\"><em style=\"margin: 0px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility;\">Rau diếp (x&agrave; l&aacute;ch)&nbsp;</em>rất &iacute;t năng lượng, 100 g cung cấp khoảng 17 calo nhưng gi&agrave;u vitamin A, lutein, folate gi&uacute;p tăng sức đề kh&aacute;ng, chống oxy h&oacute;a c&ugrave;ng nhiều kho&aacute;ng chất thiết yếu. X&agrave; l&aacute;ch thường được chế biến th&agrave;nh salad hoặc ăn sống k&egrave;m với c&aacute;c m&oacute;n ăn kh&aacute;c, ph&ugrave; hợp cho thực đơn của người giảm c&acirc;n.</p>', 'uploads/baiviet/1762967521_gemini-generated-image-mqwrdym-8142-7187-1762905706[1].png', 1, 2, '2025-11-13 00:12:01', '2025-11-13 00:12:01', 'published', 5, 'EDITOR\'S PICKS', 9),
(14, 'Uống nước sôi để nguội lâu ngày có gây ung thư?', 'uong-nuoc-soi-de-nguoi-lau-ngay-co-gay-ung-thu', '<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Gia đ&igrave;nh t&ocirc;i c&oacute; th&oacute;i quen d&ugrave;ng nước đun s&ocirc;i để nguội nhiều ng&agrave;y, thậm ch&iacute; đun đi đun lại, điều n&agrave;y c&oacute; g&acirc;y ung thư? (Hồng, 35 tuổi, H&agrave; Nội) </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\"><strong>Trả lời:</strong> </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Kh&ocirc;ng c&oacute; cơ sở khoa học n&agrave;o chứng minh rằng nước đun s&ocirc;i để nguội l&acirc;u ng&agrave;y hay nước đun đi đun lại g&acirc;y ung thư. Tuy nhi&ecirc;n, nếu nước đun s&ocirc;i để nguội qu&aacute; l&acirc;u, trong nhiều ng&agrave;y, vi sinh vật c&oacute; thể sinh s&ocirc;i trở lại, g&acirc;y ảnh hưởng xấu đến sức khỏe. Một số &yacute; kiến lo ngại kh&aacute;c về t&aacute;c hại của c&aacute;c mảng b&aacute;m tr&ecirc;n th&agrave;nh ấm đun hay v&aacute;ng nổi tr&ecirc;n mặt nước do đun đi đun lại. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Tuy nhi&ecirc;n, những lo ngại n&agrave;y kh&ocirc;ng ch&iacute;nh x&aacute;c. Nếu nguồn nước sử dụng l&agrave; nước sạch, kh&ocirc;ng chứa tạp chất, được lưu trữ an to&agrave;n v&agrave; đậy k&iacute;n để tr&aacute;nh nhiễm bẩn hoặc nhiễm khuẩn, việc đun lại nhiều lần ho&agrave;n to&agrave;n kh&ocirc;ng g&acirc;y ảnh hưởng xấu tới sức khỏe. Những cặn b&aacute;m xuất hiện trong ấm nước thường chỉ l&agrave; canxi hoặc magie c&oacute; trong nước, v&agrave; ch&uacute;ng kh&ocirc;ng g&acirc;y nguy hại đ&aacute;ng kể cho cơ thể. Khi nước bị đun nhiều lần, h&agrave;m lượng oxy h&ograve;a tan c&oacute; thể bị giảm đi, khiến nước mất đi sự tươi m&aacute;t v&agrave; c&oacute; khả năng g&acirc;y kh&oacute; ti&ecirc;u nếu d&ugrave;ng thường xuy&ecirc;n. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #fcfaf6;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Tuy nhi&ecirc;n, việc n&agrave;y kh&ocirc;ng nghi&ecirc;m trọng, cũng kh&ocirc;ng g&acirc;y thay đổi th&agrave;nh phần h&oacute;a học của nước nếu đ&oacute; l&agrave; nước sạch. Do đ&oacute;, tốt nhất bạn chỉ n&ecirc;n sử dụng nước đun s&ocirc;i để nguội trong ng&agrave;y v&agrave; tr&aacute;nh để qua đ&ecirc;m. Nếu như c&oacute; mối quan hệ giữa bệnh ung thư v&agrave; nước uống th&igrave; chỉ l&agrave; li&ecirc;n quan đến nguồn nước đầu v&agrave;o cũng như quy tr&igrave;nh xử l&yacute; nước uống chứ kh&ocirc;ng phải do việc đun s&ocirc;i nước để nguội. Nh&igrave;n chung, bạn n&ecirc;n sử dụng nguồn nước sạch đ&atilde; qua xử l&yacute; hoặc lọc để đảm bảo kh&ocirc;ng chứa tạp chất hay chất độc hại, ph&ugrave; hợp để uống v&agrave; nấu ăn h&agrave;ng ng&agrave;y. Ngo&agrave;i ra, n&ecirc;n đun lượng nước đủ d&ugrave;ng cho mỗi lần v&agrave; uống hết trong ng&agrave;y, tr&aacute;nh để nước qu&aacute; l&acirc;u nhằm hạn chế nguy cơ nhiễm vi khuẩn hoặc vi sinh vật ph&aacute;t triển.</span></span></p>', 'uploads/baiviet/1762967905_nuoc-dun-soi-de-nguoi-drbinh-j-3629-3508-1762848117[1].png', 1, 4, '2025-11-13 00:18:25', '2025-11-13 00:19:10', 'published', 6, 'MAIN HIGHLIGHTS', 9);
INSERT INTO `baiviet` (`ma_bai_viet`, `tieu_de`, `duong_dan`, `noi_dung`, `anh_bv`, `ma_tac_gia`, `ma_chuyen_muc`, `ngay_dang`, `ngay_cap_nhat`, `trang_thai`, `luot_xem`, `danh_muc`, `id_kh`) VALUES
(15, 'Thoát chết sau ba lần tim ngừng đập', 'thoat-chet-sau-ba-lan-tim-ngung-dap', '<p>Gia Lai-Đang khỏe mạnh, người đ&agrave;n &ocirc;ng 64 tuổi đau ngực tr&aacute;i dữ dội, sau đ&oacute; ngừng tuần ho&agrave;n ba lần, may mắn được c&aacute;c b&aacute;c sĩ cứu sống.</p>\r\n<p>Ng&agrave;y 12/11, đại diện Bệnh viện Đa khoa H&ugrave;ng Vương Gia Lai, cho biết sức khỏe bệnh nh&acirc;n đ&atilde; ổn định, tỉnh t&aacute;o, kh&ocirc;ng c&ograve;n đau ngực hay kh&oacute; thở, tiếp tục được theo d&otilde;i. Trước đ&oacute;, người bệnh nhập viện trong t&igrave;nh trạng đau ngực tr&aacute;i dữ dội lan l&ecirc;n vai v&agrave; c&aacute;nh tay. Ngay khi v&agrave;o viện, bệnh nh&acirc;n đột ngột ngừng tuần ho&agrave;n. &Ecirc;k&iacute;p cấp cứu 115 phối hợp c&ugrave;ng b&aacute;c sĩ hồi sức lập tức sốc điện, &eacute;p tim ngo&agrave;i lồng ngực v&agrave; hỗ trợ h&ocirc; hấp, gi&uacute;p tim &ocirc;ng đập trở lại.</p>\r\n<p>Kết quả chẩn đo&aacute;n h&igrave;nh ảnh x&aacute;c định bệnh nh&acirc;n bị nhồi m&aacute;u cơ tim cấp c&oacute; biến chứng rung thất, tăng huyết &aacute;p, cần can thiệp mạch v&agrave;nh khẩn cấp. Tuy nhi&ecirc;n, kịch t&iacute;nh xảy ra khi bệnh nh&acirc;n ngưng tim lần thứ hai tr&ecirc;n đường tới ph&ograve;ng can thiệp. Đội ngũ y tế lập tức quay lại ph&ograve;ng cấp cứu, tiếp tục hồi sức tim phổi. V&agrave;i ph&uacute;t sau, bệnh nh&acirc;n c&oacute; nhịp tim trở lại. C&aacute;c b&aacute;c sĩ vừa duy tr&igrave; hồi sức, vừa vận chuyển người bệnh đến ph&ograve;ng can thiệp mạch để chạy đua với thời gian.</p>\r\n<p>Kết quả chụp mạch v&agrave;nh ph&aacute;t hiện một huyết khối lớn tắc ho&agrave;n to&agrave;n động mạch, th&agrave;nh mạch bị xơ vữa g&acirc;y hẹp nặng. Trong l&uacute;c c&aacute;c b&aacute;c sĩ thực hiện thủ thuật, tr&aacute;i tim bệnh nh&acirc;n ngừng đập lần thứ ba. Một cuộc hội chẩn khẩn cấp tại chỗ giữa c&aacute;c chuy&ecirc;n gia tim mạch, hồi sức v&agrave; g&acirc;y m&ecirc; được tiến h&agrave;nh. Họ phối hợp đồng thời nhiều kỹ thuật như &eacute;p tim, sốc điện khẩn trương mở th&ocirc;ng mạch v&agrave;nh bị tắc. Sau 30 ph&uacute;t nghẹt thở, mạch v&agrave;nh được t&aacute;i th&ocirc;ng, tim người bệnh đập mạnh trở lại, b&aacute;c sĩ đ&aacute;nh gi&aacute; đ&acirc;y l&agrave; \"cuộc hồi sinh ngoạn mục\".</p>\r\n<p>Theo c&aacute;c chuy&ecirc;n gia, nhồi m&aacute;u cơ tim xảy ra khi mảng xơ vữa trong động mạch v&agrave;nh nứt vỡ, tạo cục m&aacute;u đ&ocirc;ng l&agrave;m tắc nghẽn d&ograve;ng m&aacute;u nu&ocirc;i tim. T&igrave;nh trạng n&agrave;y dẫn đến hoại tử một phần cơ tim, g&acirc;y rối loạn nhịp hoặc ngừng tim đột ngột, c&oacute; thể tử vong nếu kh&ocirc;ng can thiệp kịp thời. B&aacute;c sĩ khuyến c&aacute;o những người c&oacute; bệnh nền như tăng huyết &aacute;p, đ&aacute;i th&aacute;o đường, rối loạn mỡ m&aacute;u hoặc th&oacute;i quen h&uacute;t thuốc l&aacute; thuộc nh&oacute;m nguy cơ cao. Khi xuất hiện c&aacute;c dấu hiệu cảnh b&aacute;o như đau thắt ngực tr&aacute;i, kh&oacute; thở, v&atilde; mồ h&ocirc;i, buồn n&ocirc;n, người d&acirc;n cần đến ngay cơ sở y tế gần nhất, tuyệt đối kh&ocirc;ng tự điều trị tại nh&agrave;. Kh&aacute;m tim mạch định kỳ l&agrave; phương ph&aacute;p hữu hiệu để ph&aacute;t hiện sớm v&agrave; ph&ograve;ng ngừa c&aacute;c biến chứng nguy hiểm.</p>', 'uploads/baiviet/1762968278_heartandlungsillustration26904-4082-7096-1762931365[1].png', 1, 1, '2025-11-13 00:24:38', '2025-11-13 00:24:38', 'published', 2, 'LATEST POSTS', 9),
(16, 'Mẹo ăn chất xơ không gây đầy hơi', 'meo-an-chat-xo-khong-gay-day-hoi', '<p>Bổ sung chất xơ c&ugrave;ng với protein v&agrave; chất b&eacute;o l&agrave;nh mạnh, kh&ocirc;ng ăn qu&aacute; nhiều c&ugrave;ng l&uacute;c, tăng lượng từ từ gi&uacute;p giảm nguy cơ sinh kh&iacute;. Chất xơ l&agrave; chất dinh dưỡng thiết yếu hỗ trợ ti&ecirc;u h&oacute;a, th&uacute;c đẩy nhu động ruột ổn định, giảm nguy cơ mắc c&aacute;c bệnh li&ecirc;n quan đến đường ruột. C&oacute; hai loại l&agrave; chất xơ h&ograve;a tan v&agrave; chất xơ kh&ocirc;ng h&ograve;a tan. Chất xơ h&ograve;a tan li&ecirc;n kết với nước trong hệ ti&ecirc;u h&oacute;a (GI) v&agrave; trở th&agrave;nh một dạng gel, l&agrave;m chậm qu&aacute; tr&igrave;nh ti&ecirc;u h&oacute;a.</p>\r\n<p>Chất xơ kh&ocirc;ng h&ograve;a tan gi&uacute;p tăng tốc độ ti&ecirc;u h&oacute;a. Ăn nhiều chất xơ gi&uacute;p no l&acirc;u, giảm cảm gi&aacute;c đ&oacute;i, từ đ&oacute; kiểm so&aacute;t c&acirc;n nặng. Bổ sung đầy đủ chất xơ c&ograve;n kiểm so&aacute;t lượng đường trong m&aacute;u bằng c&aacute;ch l&agrave;m chậm qu&aacute; tr&igrave;nh hấp thụ đường, giảm nguy cơ mắc c&aacute;c bệnh như tiểu đường, đột quỵ, huyết &aacute;p cao, ung thư ruột gi&agrave; v&agrave; bệnh tim. Chất xơ h&ograve;a tan cũng c&oacute; t&aacute;c dụng giảm cholesterol, ph&ograve;ng ngừa bệnh tim.</p>\r\n<p>Chất dinh dưỡng n&agrave;y c&oacute; nhiều trong rau củ, tr&aacute;i c&acirc;y, yến mạch cũng như một số loại đậu, ngũ cốc nguy&ecirc;n hạt, c&aacute; loại hạt... Tuy nhi&ecirc;n, nếu ăn nhiều chất xơ c&ugrave;ng l&uacute;c v&agrave; qu&aacute; nhanh c&oacute; thể g&acirc;y đầy hơi, kh&oacute; chịu, nặng hơn l&agrave; đau bụng. Bởi cơ thể kh&ocirc;ng thể ti&ecirc;u h&oacute;a ho&agrave;n to&agrave;n chất xơ. Thay v&agrave;o đ&oacute;, ruột sản sinh ra vi khuẩn ph&acirc;n hủy chất xơ, giải ph&oacute;ng kh&iacute; như một sản phẩm phụ. Kh&iacute; n&agrave;y c&oacute; thể t&iacute;ch tụ trong dạ d&agrave;y v&agrave; ruột, tạo cảm gi&aacute;c căng tức, kh&oacute; chịu ở bụng.</p>\r\n<p>Viện Y tế Quốc gia Mỹ (NIH) khuyến c&aacute;o nữ giới n&ecirc;n ti&ecirc;u thụ 25 g chất xơ mỗi ng&agrave;y, trong khi nam giới n&ecirc;n ti&ecirc;u thụ 38 g. Dưới đ&acirc;y l&agrave; gợi &yacute; bổ sung đủ lượng chất xơ khuyến c&aacute;o mỗi ng&agrave;y m&agrave; kh&ocirc;ng bị đầy hơi.</p>\r\n<p>Tăng lượng chất xơ từ từ thay v&igrave; ăn nhanh v&agrave; qu&aacute; nhiều trong một bữa. Th&oacute;i quen ăn nhanh v&agrave; nhiều chất xơ c&ugrave;ng l&uacute;c cũng c&oacute; thể g&acirc;y t&iacute;ch tụ kh&iacute;, t&aacute;o b&oacute;n hoặc ti&ecirc;u chảy, đau bụng. N&ecirc;n để cơ thể l&agrave;m quen với chất xơ từng phần nhỏ v&agrave; tăng dần lượng chất xơ trong v&agrave;i tuần. Kết hợp nhiều loại thực phẩm: Thực phẩm c&oacute; chất xơ h&ograve;a tan thường sinh ra nhiều kh&iacute; hơn thực phẩm c&oacute; chất xơ kh&ocirc;ng h&ograve;a tan.</p>\r\n<p>Tuy nhi&ecirc;n, cơ thể cần cả hai loại. Do đ&oacute;, bạn n&ecirc;n thay đổi nhiều nguồn chất xơ kh&aacute;c nhau, t&igrave;m loại ph&ugrave; hợp với cơ thể nhất. Khi bổ sung chất xơ n&ecirc;n ăn k&egrave;m với thực phẩm gi&agrave;u protein, chất b&eacute;o l&agrave;nh mạnh v&agrave; tinh bột để ti&ecirc;u h&oacute;a ổn định, c&acirc;n bằng dinh dưỡng. Uống đủ nước: Uống 8 cốc nước mỗi ng&agrave;y tương đường 2-2,25 l&iacute;t nước để giảm đầy hơi, th&uacute;c đẩy ti&ecirc;u h&oacute;a ổn định.</p>\r\n<p>Ăn nhiều carbohydrate chứa chất xơ: Chế độ ăn gi&agrave;u carbohydrate chứa chất xơ thay v&igrave; protein c&oacute; thể l&agrave;m giảm đầy hơi đồng thời tăng cường chất xơ cho cơ thể. Ch&uacute;ng bao gồm ngũ cốc nguy&ecirc;n hạt, tr&aacute;i c&acirc;y, rau củ v&agrave; c&aacute;c loại đậu, g&oacute;p phần thay đổi vi khuẩn đường ruột, tốt cho ti&ecirc;u h&oacute;a, tạo cảm gi&aacute;c no l&acirc;u. Bỏ vỏ tr&aacute;i c&acirc;y: Vỏ tr&aacute;i c&acirc;y v&agrave; rau củ thường chứa h&agrave;m lượng chất xơ g&acirc;y đầy hơi cao, khi ăn n&ecirc;n gọt bỏ vỏ để giảm nguy cơ sinh kh&iacute;.</p>', 'uploads/baiviet/1762989560_Generated-Image-July-14-2025-8-4258-3709-1762917312[1].jpg', 1, 2, '2025-11-13 06:19:20', '2025-11-13 06:19:20', 'published', 3, 'EDITOR\'S PICKS', 9),
(17, 'Những loại thực phẩm có nguy cơ nhiễm khuẩn salmonella', 'nhung-loai-thuc-pham-co-nguy-co-nhiem-khuan-salmonella', '<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Thịt chế biến hoặc bảo quản kh&ocirc;ng đ&uacute;ng c&aacute;ch, trứng sống, sữa chưa tiệt tr&ugrave;ng c&oacute; nguy cơ nhiễm khuẩn salmonella. ThS.BS Nguyễn Thị Phương, khoa Dinh dưỡng, Bệnh viện Đa khoa T&acirc;m Anh H&agrave; Nội, cho biết salmonella l&agrave; vi khuẩn độc lực cao, g&acirc;y vi&ecirc;m đường ruột, nhiễm tr&ugrave;ng huyết, c&oacute; thể dẫn đến tử vong.</span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\"> Triệu chứng nhiễm khuẩn salmonella thường khởi ph&aacute;t sau 6-72 giờ, thường gặp như ti&ecirc;u chảy, đau quặn bụng, sốt, buồn n&ocirc;n, n&ocirc;n mửa... Ở người khỏe mạnh, bệnh thường tự khỏi sau v&agrave;i ng&agrave;y. Song với người gi&agrave;, trẻ nhỏ, phụ nữ c&oacute; thai hoặc người c&oacute; hệ miễn dịch yếu, nhiễm khuẩn salmonella c&oacute; thể dẫn đến nhiễm khuẩn huyết. B&aacute;c sĩ Phương lưu &yacute; những thực phẩm dễ nhiễm vi khuẩn n&agrave;y khi kh&ocirc;ng đảm bảo vệ sinh chế biến, ăn uống.</span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\"><strong>Thịt gia cầm, trứng l&ograve;ng đ&agrave;o, sốt mayonnaise</strong></span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\"> Ăn thịt gia cầm nấu chưa ch&iacute;n, trứng sống hoặc c&aacute;c m&oacute;n chế biến chưa ch&iacute;n kỹ như trứng l&ograve;ng đ&agrave;o hoặc c&aacute;c sản phẩm của trứng chưa ch&iacute;n, sốt mayonnaise tự l&agrave;m... tiềm ẩn nhiều rủi ro. Bởi ch&uacute;ng c&oacute; nguy cơ nhiễm khuẩn salmonella cao.</span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\"><strong> Thịt b&ograve;, lợn sấy kh&ocirc;, hun kh&oacute;i </strong></span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Thịt b&ograve;, lợn (x&uacute;c x&iacute;ch, pate, thịt nguội...) chế biến, bảo quản ở nhiệt độ kh&ocirc;ng ph&ugrave; hợp c&oacute; thể bị vi khuẩn tấn c&ocirc;ng. Salmonella c&oacute; thể tồn tại ngay cả trong sản phẩm đ&atilde; được sấy kh&ocirc; hoặc hun kh&oacute;i nếu quy tr&igrave;nh xử l&yacute; kh&ocirc;ng đạt chuẩn.</span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\"><strong>Rau sống, tr&aacute;i c&acirc;y tươi </strong></span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Rau củ v&agrave; tr&aacute;i c&acirc;y tươi cũng c&oacute; thể bị nhiễm salmonella từ ph&acirc;n b&oacute;n hữu cơ hoặc nước tưới bị &ocirc; nhiễm. Rau x&agrave; l&aacute;ch, rau thơm, gi&aacute; đỗ, c&agrave; chua... kh&ocirc;ng được rửa kỹ bằng nước sạch hoặc tiếp x&uacute;c với thớt, dao cắt thịt sống sẽ dễ bị l&acirc;y nhiễm ch&eacute;o. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\"><strong>Sữa v&agrave; c&aacute;c sản phẩm từ sữa chưa tiệt tr&ugrave;ng</strong> </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">Sữa tươi chưa qua tiệt tr&ugrave;ng c&oacute; thể chứa vi khuẩn salmonella, listeria hoặc e. coli. Uống sữa, sản phẩm từ sữa chưa tiệt tr&ugrave;ng c&oacute; nguy cơ mắc bệnh, nhất l&agrave; trẻ em, phụ nữ mang thai, người cao tuổi. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\"><strong>Thực phẩm chế biến sẵn</strong> </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; font-size: 18px; line-height: 28.8px; color: #222222; font-family: arial; background-color: #ffffff;\"><span style=\"color: #222222; font-family: arial;\"><span style=\"font-size: 18px;\">C&aacute;c m&oacute;n ăn được nấu sẵn, bảo quản ở nhiệt độ ph&ograve;ng nhiều giờ như cơm hộp, g&agrave; r&aacute;n, nem chua, b&aacute;nh m&igrave; kẹp thịt... l&agrave; m&ocirc;i trường l&yacute; tưởng cho vi khuẩn ph&aacute;t triển. Sử dụng nguồn nước nhiễm khuẩn, nhiễm ch&eacute;o từ dao, thớt, dụng cụ, tay người b&aacute;n cắt thịt sống l&agrave; con đường phổ biến, theo b&aacute;c sĩ Phương. Người ti&ecirc;u d&ugrave;ng n&ecirc;n ưu ti&ecirc;n mua thực phẩm c&oacute; nguồn gốc r&otilde; r&agrave;ng, đảm bảo an to&agrave;n vệ sinh. Ch&uacute; &yacute; rửa sạch tay bằng x&agrave; ph&ograve;ng trước khi chế biến, t&aacute;ch biệt thực phẩm sống - ch&iacute;n, vệ sinh dao thớt v&agrave; dụng cụ chế biến thường xuy&ecirc;n.</span></span></p>', 'uploads/baiviet/1762989894_image001-1762920819-9994-1762921151[1].jpg', 1, 2, '2025-11-13 06:20:59', '2025-11-13 06:24:54', 'published', 3, 'MAIN HIGHLIGHTS', 9),
(18, '5 món ăn uống bổ mắt', '5-mon-an-uong-bo-mat', '<p>Ăn hạnh nh&acirc;n, hạt hướng dương kết hợp dầu &ocirc; liu, dầu mầm l&uacute;a m&igrave; cung cấp vitamin E, hỗ trợ sức khỏe mắt v&agrave; tăng cường thị lực.</p>\r\n<p>Hạnh nh&acirc;n rất gi&agrave;u vitamin E. Ăn một nắm nhỏ hạnh nh&acirc;n c&oacute; thể đ&aacute;p ứng gần một nửa nhu cầu vitamin E h&agrave;ng ng&agrave;y của một người. Ch&uacute;ng chứa alpha-tocopherol, dạng hoạt động mạnh nhất của vitamin E, c&oacute; thể ngăn ngừa stress oxy h&oacute;a ở v&otilde;ng mạc. Ng&acirc;m hạnh nh&acirc;n qua đ&ecirc;m để dễ ti&ecirc;u h&oacute;a v&agrave; hấp thụ hơn.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/12/hat-huong-duong-1731901517-1762914108.png?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=MtHxsTUFYcLlxBlvqcGH9g\" alt=\"\" width=\"655\" height=\"433\" /></p>\r\n<p>Hạt hướng dương chứa khoảng 7 mg vitamin E trong khẩu phần 30 g. C&aacute;c chất chống oxy h&oacute;a trong loại hạt n&agrave;y gi&uacute;p duy tr&igrave; lưu th&ocirc;ng m&aacute;u tốt cho mắt, hỗ trợ cung cấp oxy v&agrave; chất dinh dưỡng cho c&aacute;c tế b&agrave;o v&otilde;ng mạc. Rang nhẹ hạt hạnh nh&acirc;n thay v&igrave; rang kỹ để giữ nguy&ecirc;n h&agrave;m lượng dinh dưỡng.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/12/dau-oliu-1-1762914130.png?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=45JtUSYFfLicrbYqkbfvfw\" alt=\"\" width=\"655\" height=\"461\" /></p>\r\n<p>Dầu &ocirc; liu kh&ocirc;ng chỉ tốt cho tim mạch m&agrave; c&ograve;n c&oacute; t&aacute;c dụng bảo vệ mắt. Loại dầu n&agrave;y chứa một dạng vitamin E gi&uacute;p chống lại stress oxy h&oacute;a trong m&ocirc; mắt. Th&ecirc;m dầu &ocirc; liu với salad hoặc rau củ nấu ch&iacute;n để giữ nguy&ecirc;n chất lượng chất chống oxy h&oacute;a.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/12/f3485bb7c3944fca1685-1762914157.jpg?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=_4syrSsGbFx1-6G0qRjjxw\" alt=\"\" width=\"655\" height=\"534\" /></p>\r\n<p>Dầu mầm l&uacute;a m&igrave; cũng l&agrave; nguồn cung cấp vitamin E dồi d&agrave;o. Ăn dầu mầm l&uacute;a m&igrave; gi&uacute;p cải thiện tuần ho&agrave;n mắt, hỗ trợ chức năng v&otilde;ng mạc khỏe mạnh. Rưới dầu n&agrave;y l&ecirc;n ngũ cốc hoặc rau củ đ&atilde; nấu ch&iacute;n, tr&aacute;nh đun n&oacute;ng v&igrave; dầu mầm l&uacute;a m&igrave; giảm t&aacute;c dụng ở nhiệt độ cao.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/12/hat-phi-1762914307.png?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=v56-2iWlahKlXSFFd6aSQA\" alt=\"\" width=\"655\" height=\"655\" /></p>\r\n<p>Hạt phỉ c&oacute; h&agrave;m lượng vitamin E cao, hơn 4 mg mỗi khẩu phần. C&aacute;c hợp chất trong hạt phỉ c&oacute; thể l&agrave;m chậm c&aacute;c rối loạn về mắt li&ecirc;n quan đến tuổi t&aacute;c bằng c&aacute;ch tăng cường m&agrave;ng tế b&agrave;o v&agrave; giảm vi&ecirc;m quanh d&acirc;y thần kinh thị gi&aacute;c. N&ecirc;n ăn hạt n&agrave;y vừa phải (khoảng một nắm mỗi ng&agrave;y) v&agrave; kh&ocirc;ng th&ecirc;m muối hoặc bơ để tr&aacute;nh nạp qu&aacute; nhiều calo v&agrave; chất b&eacute;o kh&ocirc;ng tốt.</p>', 'uploads/baiviet/1762990188_hanh-nhan-1750823133-1762913926[1].webp', 1, 2, '2025-11-13 06:29:48', '2025-11-13 06:30:35', 'published', 4, 'MAIN HIGHLIGHTS', 9),
(19, 'Tại sao đau khớp nặng hơn khi dùng điều hòa?', 'tai-sao-dau-khop-nang-hon-khi-dung-dieu-hoa', '<p>Thay đổi nhiệt độ, độ ẩm khi nằm điều h&ograve;a c&ugrave;ng tần suất vận động c&oacute; thể ảnh hưởng đến dịch khớp, sụn, lưu th&ocirc;ng m&aacute;u khiến đau nhức xương khớp nặng hơn.</p>\r\n<p>&Ecirc; ẩm, nhức mỏi to&agrave;n th&acirc;n khi ngủ điều h&ograve;a l&agrave; t&igrave;nh trạng c&oacute; thể gặp phải ở bất kỳ ai, nhất l&agrave; người đang gặp vấn đề về sức khỏe xương khớp. BS.CKI Nguyễn Văn Ơn, Đơn vị Chấn thương Chỉnh h&igrave;nh, Ph&ograve;ng kh&aacute;m Đa khoa T&acirc;m Anh Quận 7, cho biết nguy&ecirc;n nh&acirc;n thường gặp l&agrave; do sự ch&ecirc;nh lệch nhiệt độ v&agrave; độ ẩm.</p>\r\n<p>Dịch khớp đặc: Nhiệt độ thấp khiến g&acirc;n cơ co r&uacute;t, dịch khớp đặc hơn b&igrave;nh thường, giảm khả năng b&ocirc;i trơn giữa c&aacute;c khớp, l&agrave;m tăng ma s&aacute;t khi cử động, khiến khớp hoạt động k&eacute;m linh hoạt v&agrave; dễ bị đau.</p>\r\n<p>Giảm lưu th&ocirc;ng m&aacute;u: Khi nhiệt độ m&ocirc;i trường xuống thấp, c&aacute;c mạch m&aacute;u c&oacute; xu hướng co lại để ưu ti&ecirc;n lưu lượng m&aacute;u đến c&aacute;c cơ quan trung ương. Điều n&agrave;y l&agrave;m giảm lượng m&aacute;u lưu th&ocirc;ng đến c&aacute;c khớp v&agrave; m&ocirc; xung quanh, dẫn đến thiếu hụt oxy v&agrave; dưỡng chất nu&ocirc;i sụn khớp, c&aacute;c tổ chức g&acirc;n, cơ, d&acirc;y chằng quanh khớp, g&acirc;y ra c&aacute;c cơn đau nhức.</p>\r\n<p>Ngồi, nằm l&acirc;u trong ph&ograve;ng: Người mắc bệnh xương khớp cần d&agrave;nh nhiều thời gian để nghỉ ngơi, hạn chế vận động gi&uacute;p tr&aacute;nh tổn thương th&ecirc;m. Tuy nhi&ecirc;n, điều n&agrave;y kh&ocirc;ng đồng nghĩa với việc kh&ocirc;ng vận động. Nếu nằm hoặc ngồi một chỗ qu&aacute; l&acirc;u khi sử dụng điều h&ograve;a c&oacute; thể l&agrave;m t&igrave;nh trạng đau nhức xương khớp trở nặng do ảnh hưởng k&eacute;p từ nhiệt độ thấp v&agrave; cơ khớp co cứng, k&eacute;m linh hoạt.</p>\r\n<p>B&aacute;c sĩ Ơn hướng dẫn người bệnh một số điều sau gi&uacute;p giảm đau nhức xương khớp khi ngủ điều h&ograve;a.</p>\r\n<p>Đảm bảo nhiệt độ trong ph&ograve;ng ph&ugrave; hợp, kh&ocirc;ng qu&aacute; lạnh, v&agrave;o khoảng 25-28 độ C, tốt nhất kh&ocirc;ng n&ecirc;n ch&ecirc;nh lệch qu&aacute; 5 độ C so với nhiệt độ b&ecirc;n ngo&agrave;i. Người bệnh cần tr&aacute;nh nằm ở những vị tr&iacute; bị luồng gi&oacute; lạnh thổi trực tiếp v&agrave;o cơ thể, chỉnh điều h&ograve;a ở chế độ đảo gi&oacute; để kh&ocirc;ng kh&iacute; lưu th&ocirc;ng khắp ph&ograve;ng. Duy tr&igrave; độ ẩm th&iacute;ch hợp cho ph&ograve;ng, tr&aacute;nh độ ẩm qu&aacute; cao l&agrave;m ảnh hưởng sức khỏe xương khớp.</p>\r\n<p>Xoa b&oacute;p, massage nhẹ v&ugrave;ng bị đau gi&uacute;p thư gi&atilde;n v&agrave; cải thiện sự linh hoạt xương khớp. Người bệnh n&ecirc;n vận động cơ thể bằng những b&agrave;i tập gi&atilde;n cơ nhẹ nh&agrave;ng, ph&ugrave; hợp để giảm cứng khớp như đi bộ, đạp xe, dưỡng sinh, yoga... Ăn uống c&acirc;n bằng theo hướng dẫn của b&aacute;c sĩ hoặc chuy&ecirc;n gia dinh dưỡng. Tăng cường c&aacute;c loại thực phẩm c&oacute; lợi cho sức khỏe xương khớp, chứa nhiều canxi v&agrave; vitamin D...</p>\r\n<p>Uống đủ nước, ngay cả khi kh&ocirc;ng kh&aacute;t để hỗ trợ b&ocirc;i trơn khớp, th&uacute;c đẩy qu&aacute; tr&igrave;nh vận chuyển chất dinh dưỡng v&agrave; duy tr&igrave; sự linh hoạt của sụn. Kh&aacute;m sức khỏe định kỳ hoặc ngay khi ph&aacute;t hiện c&aacute;c dấu hiệu bất thường để kịp thời điều trị, nhất l&agrave; ở người mắc c&aacute;c bệnh xương khớp mạn t&iacute;nh. Thay đổi tư thế ngủ. Đ&ocirc;i khi đau nhức xương khớp c&oacute; thể l&agrave; do tư thế ngủ kh&ocirc;ng đ&uacute;ng như nằm sấp, nằm cuộn tr&ograve;n...</p>\r\n<p>L&uacute;c n&agrave;y, việc thay đổi tư thế c&oacute; thể cải thiện đau nhức hiệu quả. Ngo&agrave;i ra, việc nằm đệm qu&aacute; cứng hoặc qu&aacute; mềm, gối đầu qu&aacute; cao hoặc qu&aacute; thấp đều c&oacute; thể g&acirc;y đau nhức xương khớp khi ngủ dậy. Nếu c&aacute;c phương ph&aacute;p chăm s&oacute;c tại nh&agrave; kh&ocirc;ng l&agrave;m giảm đau nhức xương khớp, người bệnh n&ecirc;n đi kh&aacute;m v&igrave; đ&oacute; c&oacute; thể l&agrave; dấu hiệu cảnh b&aacute;o bệnh l&yacute;.</p>', 'uploads/baiviet/1762990376_fdsf-1762839089-1762839298-802-2335-8103-1762908684[1].jpg', 1, 4, '2025-11-13 06:32:56', '2025-11-13 06:32:56', 'published', 3, 'LATEST POSTS', 9),
(20, 'Hơn 160 học sinh mắc cúm A, cả trường nghỉ học dài ngày', 'hon-160-hoc-sinh-mac-cum-a-ca-truong-nghi-hoc-dai-ngay', '<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 28.8px; background-color: #fcfaf6;\"><span style=\"color: #757575; font-family: arial;\"><span style=\"font-size: 16px; letter-spacing: -0.5px; text-transform: uppercase;\">Nghệ An - Trường Phổ th&ocirc;ng D&acirc;n tộc Nội tr&uacute; THCS Con Cu&ocirc;ng ph&aacute;t hiện hơn 160 học sinh mắc c&uacute;m A, để tr&aacute;nh l&acirc;y lan dịch gần 400 em được cho nghỉ học chưa r&otilde; thời hạn đến lớp. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 28.8px; background-color: #fcfaf6;\"><span style=\"color: #757575; font-family: arial;\"><span style=\"font-size: 16px; letter-spacing: -0.5px; text-transform: uppercase;\">Ng&agrave;y 11/11, &ocirc;ng L&ocirc; Văn Thiệp, Hiệu trưởng Trường Phổ th&ocirc;ng D&acirc;n tộc Nội tr&uacute; THCS Con Cu&ocirc;ng, đ&oacute;ng ở x&atilde; Con Cu&ocirc;ng, cho biết một tuần nay nhiều học sinh xuất hiện triệu chứng đau đầu, sốt cao. Ban đầu c&aacute;c em nghĩ l&agrave; cảm c&uacute;m th&ocirc;ng thường n&ecirc;n chỉ uống thuốc, v&agrave;i ng&agrave;y sau số ca bệnh tăng nhanh. Cơ quan y tế kiểm tra kết luận hơn 160 em mắc c&uacute;m A. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 28.8px; background-color: #fcfaf6;\"><span style=\"color: #757575; font-family: arial;\"><span style=\"font-size: 16px; letter-spacing: -0.5px; text-transform: uppercase;\">Theo &ocirc;ng Thiệp, đa số học sinh c&oacute; triệu chứng nhẹ, về nh&agrave; tự điều trị. Một số em nặng hơn được gia đ&igrave;nh đưa v&agrave;o bệnh viện theo d&otilde;i.</span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 28.8px; background-color: #fcfaf6;\"><span style=\"color: #757575; font-family: arial;\"><span style=\"font-size: 16px; letter-spacing: -0.5px; text-transform: uppercase;\">Dịch b&ugrave;ng ph&aacute;t nhanh, gần 400 học sinh trong trường được cho nghỉ học, ở nh&agrave; theo d&otilde;i sức khỏe từ ng&agrave;y 9/11. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 28.8px; background-color: #fcfaf6;\"><span style=\"color: #757575; font-family: arial;\"><span style=\"font-size: 16px; letter-spacing: -0.5px; text-transform: uppercase;\">Nh&agrave; trường phối hợp Trung t&acirc;m Y tế Con Cu&ocirc;ng phun khử khuẩn, ti&ecirc;u độc to&agrave;n bộ khu&ocirc;n vi&ecirc;n, khu nội tr&uacute;, nh&agrave; ăn v&agrave; khu sinh hoạt tập thể. \"Nhiều phụ huynh cho biết học sinh sau khi về nh&agrave; đ&atilde; ph&aacute;t bệnh n&ecirc;n nh&agrave; trường đang theo d&otilde;i th&ecirc;m để sớm đưa ra lịch dạy học trở lại\", &ocirc;ng Thiệp n&oacute;i. </span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 28.8px; background-color: #fcfaf6;\"><span style=\"color: #757575; font-family: arial;\"><span style=\"font-size: 16px; letter-spacing: -0.5px; text-transform: uppercase;\">T&ugrave;y từng chủng virus, c&uacute;m g&acirc;y ra c&aacute;c triệu chứng v&agrave; mức độ nặng nhẹ kh&aacute;c nhau. Ba chủng c&uacute;m ảnh hưởng tới người gồm A, B v&agrave; C; trong đ&oacute;, c&uacute;m A phổ biến nhất, dễ biến chứng v&agrave; từng g&acirc;y nhiều đại dịch. Chủng n&agrave;y thường tổ hợp giữa c&aacute;c kh&aacute;ng nguy&ecirc;n H v&agrave; N, h&igrave;nh th&agrave;nh c&aacute;c t&aacute;c nh&acirc;n g&acirc;y bệnh như H5N1, H3N2, H1N1...</span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 28.8px; background-color: #fcfaf6;\"><span style=\"color: #757575; font-family: arial;\"><span style=\"font-size: 16px; letter-spacing: -0.5px; text-transform: uppercase;\"> C&uacute;m A c&oacute; khả năng l&acirc;y lan mạnh, biến đổi hằng năm, khiến việc kiểm so&aacute;t kh&oacute; khăn hơn. Người bệnh c&uacute;m thường sốt cao đột ngột, đau đầu, nhức mỏi, ớn lạnh, ho khan, chảy nước mũi, ch&aacute;n ăn. Hầu hết tự khỏi sau 7-10 ng&agrave;y, song trường hợp nặng c&oacute; thể kh&oacute; thở, sốt k&eacute;o d&agrave;i, thậm ch&iacute; tử vong nếu kh&ocirc;ng được điều trị kịp. Người bệnh cần nghỉ ngơi, uống đủ nước, d&ugrave;ng thuốc theo chỉ định, ti&ecirc;m ngừa hằng năm v&agrave; giữ vệ sinh c&aacute; nh&acirc;n, đeo khẩu trang, rửa tay thường xuy&ecirc;n để ph&ograve;ng l&acirc;y nhiễm.</span></span></p>\r\n<p class=\"description\" style=\"margin: 0px 0px 15px; padding: 0px; box-sizing: border-box; text-rendering: optimizelegibility; line-height: 28.8px; background-color: #fcfaf6;\">&nbsp;</p>', 'uploads/baiviet/1762990530_truong-con-cuong-1762853904-8710-1762854168[1].jpg', 1, 6, '2025-11-13 06:35:30', '2025-11-13 06:35:30', 'published', 2, 'LATEST POSTS', 9),
(21, 'Nồm ẩm trái mùa gây hại sức khỏe thế nào', 'nom-am-trai-mua-gay-hai-suc-khoe-the-nao', '<p>Bệnh h&ocirc; hấp, tim mạch hay xương khớp, bệnh về da, sức khỏe t&acirc;m thần... dễ b&ugrave;ng ph&aacute;t khi trời nồm ẩm, người d&acirc;n cần lưu &yacute; c&aacute;ch bảo vệ sức khỏe bản th&acirc;n.</p>\r\n<p>Miền Bắc lập đ&ocirc;ng ng&agrave;y 7/11, nhưng đ&atilde; nồm ẩm, độ ẩm kh&ocirc;ng kh&iacute; tr&ecirc;n 80% khiến s&agrave;n v&agrave; tường nh&agrave; \"đổ mồ h&ocirc;i\", quần &aacute;o phơi kh&ocirc;ng kh&ocirc;. Th&ocirc;ng thường cuối thu, đầu đ&ocirc;ng thời tiết chủ đạo của miền Bắc l&agrave; nắng hanh, nhiệt độ ban ng&agrave;y 27-32 độ C, độ ẩm kh&ocirc;ng kh&iacute; dưới 50%. Tuy nhi&ecirc;n, năm nay lạnh đến sớm, trời mưa nhiều, tiết thu duy tr&igrave; chỉ khoảng hai tuần. Ba h&ocirc;m nay, trời sương m&ugrave;, mưa nhỏ cả ng&agrave;y, nhiệt độ cao nhất khoảng 28 độ C, độ ẩm kh&ocirc;ng kh&iacute; tr&ecirc;n 80%.</p>\r\n<p>Tường, s&agrave;n nh&agrave; nhớp nh&aacute;p, g&acirc;y cảm gi&aacute;c kh&oacute; chịu, nguy cơ hư hại đồ điện tử. Chuy&ecirc;n gia kh&iacute; tượng đ&aacute;nh gi&aacute; nồm ẩm xuất hiện v&agrave;o th&aacute;ng 11 l&agrave; bất thường, c&ograve;n theo c&aacute;c chuy&ecirc;n gia sức khỏe, thời tiết n&agrave;y l&agrave;m bệnh h&ocirc; hấp b&ugrave;ng l&ecirc;n, xương khớp đau nhức, da nguy cơ dị ứng tăng, đồng thời g&acirc;y bất lợi cho tim mạch, ti&ecirc;u h&oacute;a v&agrave; sức khỏe tinh thần. B&aacute;c sĩ Nguyễn Huy Ho&agrave;ng, Hội Y học dưới nước v&agrave; Oxy cao &aacute;p Việt Nam, cho biết một số nh&oacute;m bệnh dễ gặp, như sau:</p>\r\n<p>Bệnh h&ocirc; hấp Độ ẩm cao tạo m&ocirc;i trường l&yacute; tưởng cho t&aacute;c nh&acirc;n g&acirc;y bệnh đường h&ocirc; hấp như c&uacute;m, vi&ecirc;m mũi họng, vi&ecirc;m phế quản, vi&ecirc;m phổi v&agrave; hen phế quản gia tăng, nguy cơ ph&aacute;t ban như thủy đậu, sởi, rubella cũng cao hơn, đặc biệt ở trẻ nhỏ. Dấu hiệu trẻ cần đi kh&aacute;m sớm gồm sốt cao kh&ocirc;ng hạ, thở nhanh hoặc r&uacute;t l&otilde;m lồng ngực, b&uacute; k&eacute;m/biếng ăn, t&iacute;m t&aacute;i; người lớn ch&uacute; &yacute; dấu hiệu kh&oacute; thở, đau tức ngực, ho k&eacute;o d&agrave;i qu&aacute; 10 ng&agrave;y.</p>\r\n<p>Đau xương khớp Xương khớp đau tăng khi ẩm, lạnh do thay đổi &aacute;p suất v&agrave; nhiệt độ l&agrave;m g&acirc;n cơ, m&ocirc; sẹo co gi&atilde;n bất thường, dịch khớp \"đặc\" hơn khiến khớp cứng, đau tăng ở người vi&ecirc;m khớp, tho&aacute;i h&oacute;a khớp, lo&atilde;ng xương. Kh&ocirc;ng kh&iacute; ẩm c&ograve;n khiến người d&acirc;n ngại vận động, l&agrave;m khớp k&eacute;m linh hoạt. C&aacute;ch xử tr&iacute; hiệu quả l&agrave; giữ ấm v&ugrave;ng cổ - vai - gối - b&agrave;n ch&acirc;n, vận động nhẹ hằng ng&agrave;y (k&eacute;o gi&atilde;n, đi bộ 20-30 ph&uacute;t trong nh&agrave; kh&ocirc; tho&aacute;ng), tắm nước ấm, chườm ấm khớp đau. Tr&aacute;nh tự &yacute; d&ugrave;ng thuốc giảm đau k&eacute;o d&agrave;i, nếu sưng n&oacute;ng đỏ, hạn chế vận động, cần kh&aacute;m chuy&ecirc;n khoa.</p>\r\n<p>Dị ứng - bệnh da Kh&ocirc;ng kh&iacute; ẩm cao khiến virus, vi khuẩn v&agrave; nấm mốc sinh s&ocirc;i, đồng thời hơi ẩm b&aacute;m tr&ecirc;n bề mặt đồ d&ugrave;ng, chăn ga, quần &aacute;o khiến vi nấm, dị nguy&ecirc;n hiện diện d&agrave;y đặc trong nh&agrave; g&acirc;y vi&ecirc;m da, hăm kẽ, nấm da, vi&ecirc;m nhiễm v&ugrave;ng k&iacute;n... Nắng - mưa - lạnh thay đổi nhanh trong ng&agrave;y l&agrave;m cơ thể kh&oacute; th&iacute;ch nghi, hệ miễn dịch hoạt động k&eacute;m hiệu quả, tạo điều kiện cho bệnh b&ugrave;ng ph&aacute;t, nhất l&agrave; ở trẻ nhỏ, người cao tuổi v&agrave; người c&oacute; bệnh nền.</p>\r\n<p>Tim mạch &ndash; đột quỵ Kh&ocirc;ng kh&iacute; ẩm l&agrave;m h&ocirc; hấp nặng nề, giảm trao đổi oxy, đồng thời biến thi&ecirc;n thời tiết s&aacute;ng, trưa, tối khiến cơ thể kh&oacute; th&iacute;ch nghi, c&oacute; thể khởi ph&aacute;t đợt cấp phổi, cơn tăng huyết &aacute;p, suy tim, đột quỵ ở người lớn tuổi, COPD (phổi tắc nghẽn mạn t&iacute;nh), bệnh tim mạch. Dấu hiệu khẩn cấp l&agrave; hụt hơi tăng nhanh, ph&ugrave; ch&acirc;n, đau ngực; dấu hiệu đột quỵ (m&eacute;o miệng &ndash; yếu liệt tay ch&acirc;n &ndash; n&oacute;i kh&oacute;).</p>\r\n<p>Sức khỏe t&acirc;m thần \"đi xuống\" Trời &acirc;m u k&eacute;o d&agrave;i, thiếu nắng v&agrave; b&iacute; b&aacute;ch v&igrave; ẩm khiến cơ thể mệt mỏi, lờ đờ, thiếu động lực. Người c&oacute; rối loạn lo &acirc;u, trầm cảm dễ b&ugrave;ng ph&aacute;t triệu chứng (c&aacute;u gắt, mất ngủ, giảm tập trung, bi quan). Duy tr&igrave; nhịp sinh hoạt, ngủ đủ - đ&uacute;ng giờ, vận động nhẹ trong nh&agrave; th&ocirc;ng tho&aacute;ng, tiếp x&uacute;c &aacute;nh s&aacute;ng ban ng&agrave;y khi c&oacute; thể sẽ gi&uacute;p cải thiện. Ti&ecirc;u h&oacute;a - an to&agrave;n thực phẩm Độ ẩm cao khiến thực phẩm dễ nhiễm khuẩn, g&acirc;y đau bụng, ti&ecirc;u chảy, n&ocirc;n &oacute;i, vi&ecirc;m dạ d&agrave;y - ruột. Cần bảo quản lạnh, đậy k&iacute;n, ăn ch&iacute;n uống s&ocirc;i, kh&ocirc;ng d&ugrave;ng thực phẩm để l&acirc;u ở nhiệt độ ph&ograve;ng, vệ sinh bếp n&uacute;c kh&ocirc; tho&aacute;ng.</p>\r\n<p>Ti&ecirc;u h&oacute;a - an to&agrave;n thực phẩm Độ ẩm cao khiến thực phẩm dễ nhiễm khuẩn, g&acirc;y đau bụng, ti&ecirc;u chảy, n&ocirc;n &oacute;i, vi&ecirc;m dạ d&agrave;y - ruột. Cần bảo quản lạnh, đậy k&iacute;n, ăn ch&iacute;n uống s&ocirc;i, kh&ocirc;ng d&ugrave;ng thực phẩm để l&acirc;u ở nhiệt độ ph&ograve;ng, vệ sinh bếp n&uacute;c kh&ocirc; tho&aacute;ng.</p>\r\n<p>C&aacute;c biện ph&aacute;p bảo vệ gia đ&igrave;nh trong những ng&agrave;y nồm ẩm, như sau:</p>\r\n<p>- Giữ nh&agrave; kh&ocirc; tho&aacute;ng, d&ugrave;ng m&aacute;y h&uacute;t ẩm/điều h&ograve;a chế độ dry, đ&oacute;ng cửa khi độ ẩm ngo&agrave;i trời cao, mở th&ocirc;ng gi&oacute; khi trời hanh.</p>\r\n<p>- Kiểm so&aacute;t nấm mốc bằng c&aacute;ch lau bề mặt bằng dung dịch vệ sinh ph&ugrave; hợp, giặt - sấy kh&ocirc; ho&agrave;n to&agrave;n chăn ga, r&egrave;m, quần &aacute;o, tr&aacute;nh phơi trong nh&agrave; ẩm k&iacute;n.</p>\r\n<p>- Vệ sinh thiết bị như m&aacute;y lạnh, m&aacute;y lọc kh&ocirc;ng kh&iacute;, m&aacute;y giặt, khay nước ngưng. - Giảm c&aacute;c dị nguy&ecirc;n: Hạn chế thảm d&agrave;y, th&uacute; b&ocirc;ng trong ph&ograve;ng ngủ, bọc nệm - gối chống bụi mạt.</p>\r\n<p>- Giữ ấm cơ thể, đặc biệt v&ugrave;ng cổ, ngực, b&agrave;n ch&acirc;n; thay quần &aacute;o ướt mồ h&ocirc;i kịp thời. - Uống đủ nước v&agrave; dinh dưỡng c&acirc;n bằng c&aacute;ch tăng rau xanh, tr&aacute;i c&acirc;y; hạn chế đồ ngọt, rượu bia, bổ sung bữa n&oacute;ng, canh s&uacute;p.</p>\r\n<p>- Vận động hằng ng&agrave;y như đi bộ trong nh&agrave;, b&agrave;i tập k&eacute;o gi&atilde;n, thăng bằng 20-30 ph&uacute;t.</p>\r\n<p>- Tu&acirc;n thủ thuốc nền: với người tăng huyết &aacute;p, tim mạch, COPD, hen, ti&ecirc;m ph&ograve;ng theo khuyến c&aacute;o.</p>\r\n<p>- Theo d&otilde;i dấu hiệu nặng như kh&oacute; thở, t&iacute;m t&aacute;i, co k&eacute;o cơ h&ocirc; hấp; đau ngực; sốt cao k&eacute;o d&agrave;i; rối loạn &yacute; thức, yếu liệt, ti&ecirc;u chảy mất nước ở trẻ/gi&agrave;, cần đi kh&aacute;m ngay.</p>', 'uploads/baiviet/1762990726_8-1741148957-1741150266-jpg-17-7757-6471-1762762006[1].png', 1, 6, '2025-11-13 06:38:46', '2025-11-13 06:38:46', 'published', 9, 'LATEST POSTS', 9),
(22, '5 động tác yoga giúp phụ nữ giảm mỡ bụng', '5-dong-tac-yoga-giup-phu-nu-giam-mo-bung', '<p>Ph&aacute;i đẹp tập tư thế c&aacute;nh cung, lạc đ&agrave;, tấm v&aacute;n gi&uacute;p c&aacute;c khớp dẻo dai, th&uacute;c đẩy đốt mỡ bụng hiệu quả hơn.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/11/2e846cc3-28cb-4afe-b628-f745ca68f334-1762844966.png?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=A-E_w2IS0hSn5gbfd7mD6A\" alt=\"\" width=\"700\" height=\"467\" /></p>\r\n<p><strong>Tư thế tấm v&aacute;n</strong></p>\r\n<p>Động t&aacute;c yoga n&agrave;y gi&uacute;p tăng cường sức mạnh cho to&agrave;n th&acirc;n, t&aacute;c động đến to&agrave;n bộ cơ thể, giảm mỡ bụng.</p>\r\n<p><em>C&aacute;ch thực hiện:</em></p>\r\n<p>Người tập bắt đầu ở tư thế chống đẩy, giữ cơ thể tạo th&agrave;nh một đường thẳng.</p>\r\n<p>Giữ khuỷu tay dưới vai v&agrave; giữ chặt phần th&acirc;n.</p>\r\n<p>Người tập duy tr&igrave; tư thế n&agrave;y c&agrave;ng l&acirc;u c&agrave;ng tốt.</p>\r\n<p>Thực hiện theo nhu cầu tập luyện v&agrave; t&igrave;nh trạng sức khỏe.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/11/001d7924-0db2-4344-add9-06151088c757-1762845292.jpg?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=0HITRwGflUv3A5VEEgGpLA\" alt=\"\" width=\"700\" height=\"467\" /></p>\r\n<p><strong>Tư thế c&aacute;nh cung </strong></p>\r\n<p>Tư thế n&agrave;y gi&uacute;p săn chắc v&ugrave;ng bụng, cải thiện chức năng ti&ecirc;u h&oacute;a.</p>\r\n<p><em> C&aacute;ch thực hiện</em>:</p>\r\n<p>Người tập nằm sấp v&agrave; gập đầu gối.</p>\r\n<p>Đưa tay ra sau nắm lấy mắt c&aacute; ch&acirc;n, n&acirc;ng ngực l&ecirc;n khỏi s&agrave;n.</p>\r\n<p>Giữ nguy&ecirc;n tư thế trong 20-30 gi&acirc;y, h&iacute;t thở đều đặn.</p>\r\n<p>Nhẹ nh&agrave;ng hạ người xuống v&agrave; lặp lại.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/11/919a0709-292c-432e-bd5d-802845b7df17-1762845427.png?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=DHGWRkEzgceNd4wl7xP9Pw\" alt=\"\" width=\"700\" height=\"467\" /></p>\r\n<p><strong>Tư thế lạc đ&agrave; </strong></p>\r\n<p>Động t&aacute;c n&agrave;y gi&uacute;p ph&aacute;i đẹp k&eacute;o căng cơ bụng v&agrave; tăng cường sức mạnh cho lưng.</p>\r\n<p><em>C&aacute;ch thực hiện: </em></p>\r\n<p>Q&ugrave;y tr&ecirc;n s&agrave;n, hai đầu gối mở rộng bằng h&ocirc;ng.</p>\r\n<p>Đặt tay l&ecirc;n lưng dưới, nhẹ nh&agrave;ng ngả người về ph&iacute;a sau.</p>\r\n<p>Nếu thấy thoải m&aacute;i, h&atilde;y chạm v&agrave;o g&oacute;t ch&acirc;n v&agrave; giữ trong 20 gi&acirc;y.</p>\r\n<p>Trở về vị tr&iacute; ban đầu chậm r&atilde;i.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/11/4853c0c6-c507-434c-a440-6b6fe999db44-1762845916.jpg?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=eUcfdqxZ4L7L3xjS_3FBkg\" alt=\"\" width=\"700\" height=\"467\" /></p>\r\n<p><strong>Tư thế rắn hổ mang </strong></p>\r\n<p>Thực hiện động t&aacute;c n&agrave;y c&oacute; t&aacute;c dụng k&eacute;o căng cơ bụng đồng thời đốt ch&aacute;y mỡ.</p>\r\n<p><em>C&aacute;ch thực hiện: </em></p>\r\n<p>Nằm sấp, đặt l&ograve;ng b&agrave;n tay dưới vai.</p>\r\n<p>H&iacute;t v&agrave;o v&agrave; n&acirc;ng ngực l&ecirc;n khỏi mặt đất, giữ khuỷu tay hơi cong.</p>\r\n<p>Nh&igrave;n l&ecirc;n v&agrave; giữ nguy&ecirc;n tư thế n&agrave;y trong 15-20 gi&acirc;y, sau đ&oacute; thả ra.</p>\r\n<p>Lặp lại 3-4 lần để c&oacute; kết quả tối ưu.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/11/f8e001a0-d401-4f37-8f3d-94259e5908f2-1762847552.jpg?w=1200&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=FZ6uxh-h3oBYmPkAoPR-Sg\" alt=\"\" width=\"700\" height=\"467\" /></p>\r\n<p><strong>Gập người về ph&iacute;a trước</strong></p>\r\n<p>Thực h&agrave;nh tư thế hỗ trợ cải thện sự linh hoạt của c&aacute;c khớp, th&uacute;c đẩy đốt ch&aacute;y mỡ bụng.</p>\r\n<p><em>C&aacute;ch thực hiện:</em></p>\r\n<p>Ngồi duỗi thẳng ch&acirc;n ra ph&iacute;a trước.</p>\r\n<p>Thở ra v&agrave; gập về ph&iacute;a trước, chạm v&agrave;o b&agrave;n ch&acirc;n hoặc ống ch&acirc;n.</p>\r\n<p>Giữ nguy&ecirc;n tư thế trong 20-30 gi&acirc;y trong khi h&iacute;t thở s&acirc;u.</p>', 'uploads/baiviet/1762991084_919a0709-292c-432e-bd5d-802845b7df17-1762845427[1].png', 1, 3, '2025-11-13 06:44:44', '2025-11-13 06:44:44', 'published', 2, 'POPULAR POSTS', 9),
(23, 'AI - cánh tay đắc lực của bác sĩ', 'ai-canh-tay-dac-luc-cua-bac-si', '<p>AI kh&ocirc;ng thay thế b&aacute;c sĩ m&agrave; l&agrave; c&aacute;nh tay đắc lực gi&uacute;p giải ph&oacute;ng những đầu việc lặp lại v&agrave; khuếch đại năng lực của con người.</p>\r\n<p>PGS.TS Trần Ngọc Đăng, Ph&oacute; ph&ograve;ng Khoa học C&ocirc;ng nghệ, Đại học Y Dược TP HCM, chia sẻ tại hội thảo Tư duy linh hoạt v&agrave; ph&aacute;t triển chuy&ecirc;n ng&agrave;nh trong kỷ nguy&ecirc;n AI &amp; VUCA chiều 10/11, rằng từ dự b&aacute;o dịch bệnh, hỗ trợ chẩn đo&aacute;n đến c&aacute; thể h&oacute;a điều trị, c&ocirc;ng nghệ tr&iacute; tuệ nh&acirc;n tạo (AI) đang từng bước định h&igrave;nh lại c&aacute;ch vận h&agrave;nh của ng&agrave;nh y tế.</p>\r\n<p>Với c&aacute;c kỹ thuật h&igrave;nh ảnh, từ X-quang, CT đến MRI, AI c&oacute; thể ph&oacute;ng đại từng điểm bất thường nhỏ đến mức mắt người dễ lướt qua. Trong giải phẫu bệnh, AI như một \"trợ l&yacute; kh&ocirc;ng biết mệt\", gi&uacute;p khuếch đại năng lực của con người, s&agrave;ng lọc h&agrave;ng trăm ti&ecirc;u bản chỉ trong v&agrave;i ph&uacute;t, đẩy những mẫu đ&aacute;ng ngờ l&ecirc;n trước để b&aacute;c sĩ tập trung xử l&yacute;, kh&acirc;u chẩn đo&aacute;n v&igrave; thế sẽ nhanh hơn.</p>\r\n<p>Ở kh&acirc;u h&agrave;nh ch&iacute;nh, AI c&oacute; thể đảm nhận kh&acirc;u nhập liệu, xử l&yacute; hồ sơ, tổng hợp b&aacute;o c&aacute;o..., những c&ocirc;ng việc chiếm h&agrave;ng giờ mỗi ng&agrave;y nhưng &iacute;t tạo ra gi&aacute; trị chuy&ecirc;n m&ocirc;n. &Ocirc;ng Đăng dẫn khảo s&aacute;t tại TP HCM cho thấy 41% nh&acirc;n vi&ecirc;n y tế đang c&oacute; dấu hiệu kiệt sức, ri&ecirc;ng nh&oacute;m g&aacute;nh khối lượng việc lớn, nguy cơ n&agrave;y tăng tới gấp ba lần. Nếu AI \"giải ph&oacute;ng\" bớt những đầu việc lặp lại, b&aacute;c sĩ c&oacute; thể d&agrave;nh thời gian cho bệnh nh&acirc;n, cho chuy&ecirc;n m&ocirc;n.</p>\r\n<p>GS.TS Jonathan Van Tam, nguy&ecirc;n Cố vấn Y tế Ch&iacute;nh phủ Anh, nhận định rằng ở một số chuy&ecirc;n ng&agrave;nh, AI đang mở rộng năng lực của y học theo c&aacute;ch m&agrave; con người kh&oacute; đạt được nếu chỉ dựa v&agrave;o kinh nghiệm c&aacute; nh&acirc;n. &Ocirc;ng dẫn chứng trong lĩnh vực da liễu, một b&aacute;c sĩ d&agrave;y dạn kinh nghiệm c&oacute; thể dựa tr&ecirc;n v&agrave;i ngh&igrave;n ca bệnh từng gặp để đ&aacute;nh gi&aacute; một nốt nghi ngờ.</p>\r\n<p>Trong khi đ&oacute;, AI c&oacute; thể so s&aacute;nh h&igrave;nh ảnh đ&oacute; với h&agrave;ng triệu ca đ&atilde; được x&aacute;c nhận bằng giải phẫu bệnh, từ đ&oacute; hỗ trợ nhận diện sớm v&agrave; ch&iacute;nh x&aacute;c hơn những dấu hiệu bất thường. \"AI sẽ l&agrave; chất x&uacute;c t&aacute;c đưa y học bước v&agrave;o giai đoạn c&aacute; thể h&oacute;a s&acirc;u sắc\", GS Van Tam n&oacute;i. Vaccine, vốn được xem l&agrave; \"l&aacute; chắn\" chống bệnh truyền nhiễm, đang bước sang một chương mới nhờ c&ocirc;ng nghệ mRNA.</p>\r\n<p>&Ocirc;ng dự b&aacute;o trong 5-10 năm tới với sự trợ gi&uacute;p của AI, mRNA c&oacute; thể để lại dấu ấn đậm n&eacute;t hơn ở lĩnh vực điều trị ung thư. Ở một số loại ung thư, sau phẫu thuật v&agrave; c&aacute;c phương ph&aacute;p như h&oacute;a trị, xạ trị hay liệu ph&aacute;p miễn dịch, bệnh nh&acirc;n được kỳ vọng c&oacute; thể được ti&ecirc;m một loại vaccine \"may đo ri&ecirc;ng\" dựa tr&ecirc;n m&ocirc; bệnh học v&agrave; dấu ấn khối u của ch&iacute;nh họ.</p>\r\n<p>Ở g&oacute;c độ y tế dự ph&ograve;ng, PGS Đăng cho biết một số m&ocirc; h&igrave;nh ph&acirc;n t&iacute;ch dữ liệu bằng AI c&oacute; thể hỗ trợ x&acirc;y dựng c&aacute;c kịch bản về nguy cơ b&ugrave;ng ph&aacute;t dịch trong tương lai. \"Từ c&aacute;c dữ liệu nhập v&agrave;o, AI đ&atilde; dự b&aacute;o ch&iacute;nh x&aacute;c c&aacute;c đại dịch xảy ra v&agrave;o năm 2009, 2020\", PGS Đăng n&oacute;i, th&ecirc;m rằng c&oacute; m&ocirc; h&igrave;nh đưa ra giả thuyết về khả năng xuất hiện một đợt dịch v&agrave;o khoảng năm 2027, trong giai đoạn từ th&aacute;ng 3 đến th&aacute;ng 5. TS.BS Trần C&ocirc;ng Thắng, Ph&oacute; Hiệu trưởng Trường Y, Trường Đại học Y Dược TP HCM cho rằng tr&ecirc;n l&acirc;m s&agrave;ng, AI gi&uacute;p tổng kết t&agrave;i liệu để đưa ra c&aacute;c mức độ bằng chứng ph&ugrave; hợp, hỗ trợ cho việc ra quyết định dựa tr&ecirc;n y học bằng chứng.</p>\r\n<p>C&ograve;n trong giảng dạy, đặc biệt sau đại học, AI c&oacute; thể x&acirc;y dựng chương tr&igrave;nh học c&aacute; nh&acirc;n h&oacute;a cho học vi&ecirc;n, dựa tr&ecirc;n khung chương tr&igrave;nh chung. B&aacute;c sĩ Phạm Thị Mỹ Li&ecirc;n, Gi&aacute;m đốc c&ocirc;ng ty GSK Việt Nam chia sẻ bản th&acirc;n sử dụng AI trong khoảng 50% c&ocirc;ng việc, bao gồm soạn b&agrave;i thuyết tr&igrave;nh c&ocirc; đọng, hỗ trợ ng&ocirc;n ngữ, sử dụng trợ l&yacute; ảo để ghi ch&eacute;p bi&ecirc;n bản cuộc họp, v&agrave; ứng dụng trong hoạt động của nh&acirc;n vi&ecirc;n c&ocirc;ng ty (kể cả tr&igrave;nh dược vi&ecirc;n). B&agrave; nhận định việc ứng dụng c&ocirc;ng nghệ AI mang đến nhiều cơ hội cho ng&agrave;nh y tế Việt Nam. Trong đ&oacute;, AI tạo cơ hội học tập v&agrave; hội nhập, l&agrave; trợ thủ đắc lực gi&uacute;p c&ocirc;ng việc th&ocirc;ng minh, tiết kiệm thời gian hơn.</p>\r\n<p>Điều n&agrave;y tạo ra những nghề nghiệp mới, h&igrave;nh th&agrave;nh hệ sinh th&aacute;i gồm: b&aacute;c sĩ, điều dưỡng, dược sĩ, kỹ sư c&ocirc;ng nghệ, nh&agrave; quản l&yacute; dữ liệu, v&agrave; chuy&ecirc;n gia đạo đức. Th&ocirc;ng qua AI, mỗi sinh vi&ecirc;n, giảng vi&ecirc;n v&agrave; nh&acirc;n vi&ecirc;n y tế c&oacute; cơ hội tiếp cận cơ sở dữ liệu lớn, chất lượng cao to&agrave;n quốc v&agrave; quốc tế, rất quan trọng cho y học bằng chứng v&agrave; nghi&ecirc;n cứu l&acirc;m s&agrave;ng. Từ đ&oacute; thu h&uacute;t đầu tư v&agrave; c&aacute;c chương tr&igrave;nh quốc tế, mở ra cơ hội trao đổi t&agrave;i năng v&agrave; vượt ra tầm quốc tế.</p>\r\n<p><img src=\"https://i1-suckhoe.vnecdn.net/2025/11/11/Screenshot-2025-11-11-at-15-07-8775-8584-1762848746.png?w=680&amp;h=0&amp;q=100&amp;dpr=1&amp;fit=crop&amp;s=R-Y3OyQsOvjamfAXmJKZfA\" alt=\"\" width=\"700\" height=\"345\" /></p>\r\n<p>Song, c&aacute;c chuy&ecirc;n gia nh&igrave;n nhận rằng c&ugrave;ng với cơ hội, sự b&ugrave;ng nổ của AI cũng đặt ra nhiều th&aacute;ch thức mới cho đ&agrave;o tạo v&agrave; thực h&agrave;nh y khoa. Một trong những lo ngại được n&ecirc;u ra l&agrave; nếu kh&ocirc;ng ứng xử đ&uacute;ng c&aacute;ch, AI c&oacute; thể khiến người học lệ thuộc v&agrave;o c&ocirc;ng cụ, trong khi nền tảng kiến thức cốt l&otilde;i lại kh&ocirc;ng được củng cố. Một v&iacute; dụ được n&ecirc;u ra l&agrave; t&igrave;nh trạng sinh vi&ecirc;n sử dụng AI để x&acirc;y dựng b&agrave;i thuyết tr&igrave;nh với h&igrave;nh thức hấp dẫn, nội dung mạch lạc.</p>\r\n<p>Tuy nhi&ecirc;n, khi được hỏi s&acirc;u về bản chất vấn đề, chẳng hạn cơ chế dẫn truyền thần kinh, một số sinh vi&ecirc;n lại chưa thật sự nắm r&otilde;. C&aacute;c chuy&ecirc;n gia nhấn mạnh, việc chấp nhận sản phẩm được AI hỗ trợ nhưng kh&ocirc;ng hiểu bản chất sẽ tạo ra khoảng trống nguy hiểm trong đ&agrave;o tạo b&aacute;c sĩ. C&ocirc;ng nghệ c&oacute; thể hỗ trợ, nhưng kh&ocirc;ng thể thay thế tư duy l&acirc;m s&agrave;ng. Th&aacute;ch thức của thế hệ trẻ l&agrave; sử dụng AI đ&uacute;ng c&aacute;ch để gia tăng hiệu quả, kh&ocirc;ng đ&aacute;nh đổi sự chắc chắn trong kiến thức, chẩn đo&aacute;n v&agrave; điều trị. Từ g&oacute;c nh&igrave;n quốc tế, GS Van Tam nhắc lại một băn khoăn phổ biến của giới y khoa khi AI bước v&agrave;o bệnh viện: Bệnh nh&acirc;n liệu c&oacute; sẵn s&agrave;ng \"tr&uacute;t bầu t&acirc;m sự\" với một cỗ m&aacute;y, thay v&igrave; nh&igrave;n v&agrave;o mắt một b&aacute;c sĩ thật?.</p>\r\n<p>&Ocirc;ng cho rằng cảm gi&aacute;c e d&egrave; ấy l&agrave; dễ hiểu, song AI kh&ocirc;ng sinh ra để thay thế b&aacute;c sĩ, m&agrave; để tăng sức mạnh cho b&aacute;c sĩ v&agrave; chuẩn h&oacute;a chất lượng chăm s&oacute;c. Để đảm bảo đạo đức v&agrave; chất lượng khi sử dụng AI trong y tế, b&aacute;c sĩ Li&ecirc;n khuyến c&aacute;o cần c&oacute; sự tu&acirc;n thủ, x&acirc;y dựng ch&iacute;nh s&aacute;ch để bảo vệ quyền ri&ecirc;ng tư v&agrave; dữ liệu của bệnh nh&acirc;n. Ngo&agrave;i ra, cần c&oacute; đơn vị độc lập để bảo chứng, đảm bảo c&ocirc;ng cụ hoặc ứng dụng đ&aacute;p ứng ti&ecirc;u chuẩn nhất định (ti&ecirc;u chuẩn cơ sở, quốc gia). PGS.TS Ph&ugrave;ng Nguyễn Thế Nguy&ecirc;n, Hiệu trưởng Trường Y, Đại học Y Dược TP HCM, cho biết nh&agrave; trường đ&atilde; ban h&agrave;nh nguy&ecirc;n tắc sử dụng AI, đổi mới chương tr&igrave;nh, đ&agrave;o tạo li&ecirc;n ng&agrave;nh v&agrave; khuyến kh&iacute;ch l&agrave;m việc nh&oacute;m.</p>\r\n<p>Mục ti&ecirc;u chiến lược 5 năm tới: Nghi&ecirc;n cứu ứng dụng AI về mặt học thuật v&agrave; ứng dụng số l&agrave; một trong năm mục ti&ecirc;u lớn. Khi viết b&agrave;i b&aacute;o c&aacute;o, sinh vi&ecirc;n được ph&eacute;p sử dụng AI nhưng phải tuy&ecirc;n bố r&otilde; r&agrave;ng mục đ&iacute;ch sử dụng. PGS Đăng cho rằng c&aacute;c trường đại học kh&ocirc;ng chỉ đ&agrave;o tạo người sử dụng cuối (end user) m&agrave; c&ograve;n cần đ&agrave;o tạo về thẩm định, s&aacute;ng tạo v&agrave; quản trị AI, để c&oacute; thể sử dụng c&ocirc;ng nghệ n&agrave;y c&oacute; đạo đức v&agrave; hiệu quả. \"In a world of AI, be a human - H&atilde;y l&agrave; một con người trong thế giới của AI\", PGS Đăng nhấn mạnh.</p>\r\n<p>&nbsp;</p>', 'uploads/baiviet/1762997510_Screenshot-2025-11-11-at-15-02-7237-4908-1762848328[1].png', 1, 5, '2025-11-13 08:31:50', '2025-11-13 08:34:37', 'published', 4, 'LATEST POSTS', 9);
INSERT INTO `baiviet` (`ma_bai_viet`, `tieu_de`, `duong_dan`, `noi_dung`, `anh_bv`, `ma_tac_gia`, `ma_chuyen_muc`, `ngay_dang`, `ngay_cap_nhat`, `trang_thai`, `luot_xem`, `danh_muc`, `id_kh`) VALUES
(24, 'Nguyên liệu \'trôi nổi\' trong tiệm bánh mì gây ngộ độc hơn 300 người', 'nguyen-lieu-troi-noi-trong-tiem-banh-mi-gay-ngo-doc-cho-hon-300-nguoi', '<p>Sở An to&agrave;n thực phẩm TP HCM ghi nhận tiệm B&aacute;nh m&igrave; c&oacute;c c&ocirc; B&iacute;ch d&ugrave;ng nhiều nguy&ecirc;n liệu mua lẻ, kh&ocirc;ng h&oacute;a đơn, chế biến tại nh&agrave;, một chi nh&aacute;nh hoạt động chưa đăng k&yacute; kinh doanh.</p>\r\n<p>T&iacute;nh đến trưa 12/11, 316 người đ&atilde; phải nhập viện do rối loạn ti&ecirc;u h&oacute;a sau khi ăn b&aacute;nh m&igrave; của tiệm \"B&aacute;nh m&igrave; c&oacute;c c&ocirc; B&iacute;ch\", trong đ&oacute; một bệnh nh&acirc;n diễn tiến nặng. B&aacute;o c&aacute;o điều tra sơ bộ của Sở An to&agrave;n thực phẩm TP HCM cho thấy tiệm \"B&aacute;nh m&igrave; c&oacute;c c&ocirc; B&iacute;ch\" c&oacute; giấy ph&eacute;p kinh doanh tại số 112A Nguyễn Th&aacute;i Sơn, phường Hạnh Th&ocirc;ng, từ năm 2020. Tuy nhi&ecirc;n, một chi nh&aacute;nh kh&aacute;c tại số 363 L&ecirc; Quang Định, phường B&igrave;nh Lợi Trung, lại hoạt động nhưng chưa đăng k&yacute; kinh doanh.</p>\r\n<p>Cơ sở thu&ecirc; mặt bằng khoảng 20 m&sup2;, trang bị một xe b&aacute;nh m&igrave; inox, 3 tủ m&aacute;t v&agrave; một tủ đ&ocirc;ng. Chủ cơ sở cho biết tự mua nguy&ecirc;n liệu, kh&ocirc;ng c&oacute; h&oacute;a đơn v&agrave; tự chế biến g&agrave;, bơ v&agrave; dưa chua. Trong đ&oacute;, g&agrave; mua tại cơ sở ở đường L&ecirc; Đức Thọ, mỗi ng&agrave;y khoảng 50-60 kg ức g&agrave;, chế biến tại nh&agrave; v&agrave; sử dụng hết trong ng&agrave;y. Bơ được l&agrave;m từ trứng g&agrave;, dầu ăn v&agrave; chanh, nguồn trứng mua ở đường Điện Bi&ecirc;n Phủ, mỗi ng&agrave;y khoảng 120 trứng. Dưa chua mua ở cơ sở nhỏ lẻ, về rửa, ng&acirc;m muối v&agrave; b&aacute;n hết trong ng&agrave;y.</p>\r\n<p>Pate nhập từ cơ sở ở đường Nguyễn Thiện Thuật. Chả lụa c&oacute; phiếu mua h&agrave;ng 35 kg ng&agrave;y 5/11. Theo Sở Y tế TP HCM, trong 316 ca nhập viện, 252 người đ&atilde; xuất viện, 64 bệnh nh&acirc;n đang được điều trị. Hầu hết bệnh nh&acirc;n đều tạm ổn định, ngoại trừ một trường hợp nặng phải hồi sức t&iacute;ch cực tại Bệnh viện Nh&acirc;n d&acirc;n Gia Định.</p>\r\n<p>Bệnh nh&acirc;n n&agrave;y c&oacute; nhiều bệnh nền phức tạp như vi&ecirc;m phổi, tăng huyết &aacute;p, rung nhĩ, hiện đ&atilde; cai m&aacute;y thở v&agrave; đang thở oxy qua mũi. Vụ việc xảy ra từ ng&agrave;y 4 đến 6/11, với c&aacute;c triệu chứng phổ biến l&agrave; ti&ecirc;u chảy, sốt, đau bụng v&agrave; n&ocirc;n &oacute;i. Độ tuổi trung b&igrave;nh của c&aacute;c bệnh nh&acirc;n l&agrave; 30. Cơ quan chức năng nghi ngờ vi khuẩn Salmonella l&agrave; t&aacute;c nh&acirc;n g&acirc;y ra vụ ngộ độc h&agrave;ng loạt n&agrave;y. Cơ quan chức năng đang tiếp tục l&agrave;m r&otilde; vụ việc v&agrave; xử l&yacute; c&aacute;c vi phạm của cơ sở.</p>\r\n<p>Ng&agrave;nh y tế ghi nhận t&aacute;c nh&acirc;n Salmonella enteritidis v&agrave; Salmonella spp từ mẫu cấy m&aacute;u v&agrave; ph&acirc;n. Sở Y tế TP HCM đ&atilde; phối hợp với Đơn vị Nghi&ecirc;n cứu l&acirc;m s&agrave;ng Đại học Oxford (OUCRU) thu thập mẫu bệnh phẩm, thực hiện x&eacute;t nghiệm nu&ocirc;i cấy vi sinh v&agrave; giải tr&igrave;nh tự gene c&aacute;c chủng ph&acirc;n lập được để l&agrave;m r&otilde; nguy&ecirc;n nh&acirc;n v&agrave; c&aacute;c yếu tố li&ecirc;n quan. Ngộ độc thực phẩm thường xảy ra khi ăn phải thực phẩm nhiễm vi sinh vật hoặc độc tố, như Staphylococcus aureus, Salmonella, Shigella, E.coli, Clostridium perfringens hay Bacillus cereus. Việc x&aacute;c định nguy&ecirc;n nh&acirc;n cần dựa tr&ecirc;n kết quả kiểm nghiệm mẫu thực phẩm, mẫu ph&acirc;n hoặc chất n&ocirc;n, thời gian xuất hiện triệu chứng, diễn biến bệnh v&agrave; ph&acirc;n t&iacute;ch dịch tễ. Chỉ khi tổng hợp đầy đủ dữ liệu, cơ quan chức năng mới c&oacute; thể kết luận ch&iacute;nh thức về t&aacute;c nh&acirc;n g&acirc;y ngộ độc. Sở Y tế TP HCM khuyến c&aacute;o người d&acirc;n kh&ocirc;ng n&ecirc;n hoang mang hay suy diễn từ một kết quả x&eacute;t nghiệm đơn lẻ. Tuy nhi&ecirc;n, mỗi người cần chủ động bảo vệ sức khỏe bằng c&aacute;ch lựa chọn thực phẩm r&otilde; nguồn gốc, ăn ch&iacute;n uống s&ocirc;i, bảo quản thức ăn đ&uacute;ng c&aacute;ch v&agrave; tu&acirc;n thủ c&aacute;c nguy&ecirc;n tắc an to&agrave;n vệ sinh thực phẩm để ph&ograve;ng tr&aacute;nh ngộ độc.</p>', 'uploads/baiviet/1762998053_ba-nh-mi-co-c-1762936128-17629-6372-3723-1762936303[1].jpg', 1, 1, '2025-11-13 08:40:53', '2025-11-13 08:40:53', 'published', 2, 'LATEST POSTS', 9),
(25, '7 thực phẩm ăn sáng lành mạnh', '7-thuc-pham-an-sang-lanh-manh', '<p>C&agrave; ph&ecirc;, tr&agrave; xanh, trứng v&agrave; c&aacute;c loại hạt cung cấp nhiều chất dinh dưỡng thiết yếu để khởi đầu ng&agrave;y mới nhiều năng lượng. Bữa s&aacute;ng bổ dưỡng c&oacute; thể cung cấp năng lượng l&acirc;u d&agrave;i v&agrave; gi&uacute;p no l&acirc;u trong nhiều giờ. Để đ&aacute;p ứng điều n&agrave;y, c&aacute;c thực phẩm ti&ecirc;u thụ v&agrave;o đầu ng&agrave;y cần gi&agrave;u chất xơ, protein, chất b&eacute;o l&agrave;nh mạnh c&ugrave;ng c&aacute;c chất dinh dưỡng. Dưới đ&acirc;y l&agrave; những lựa chọn dễ d&agrave;ng, gi&agrave;u dinh dưỡng cho bữa s&aacute;ng l&agrave;nh mạnh.</p>\r\n<p><strong>Trứng </strong></p>\r\n<p>Trứng cung cấp protein, rất cần thiết cho sự ph&aacute;t triển v&agrave; duy tr&igrave; cơ bắp, gi&uacute;p no l&acirc;u. D&ugrave; trứng c&oacute; h&agrave;m lượng cholesterol cao nhưng ăn thực phẩm n&agrave;y kh&ocirc;ng l&agrave;m tăng mức cholesterol ở hầu hết mọi người. Ăn trứng c&ugrave;ng c&aacute;c thực phẩm bổ dưỡng kh&aacute;c như b&aacute;nh m&igrave; nướng nguy&ecirc;n c&aacute;m, tr&aacute;i c&acirc;y nguy&ecirc;n quả hoặc rau x&agrave;o l&agrave; những c&aacute;ch l&agrave;nh mạnh để tăng cường chất dinh dưỡng.</p>\r\n<p><strong>Sữa chua Hy Lạp </strong></p>\r\n<p>Sữa chua Hy Lạp được l&agrave;m bằng c&aacute;ch lọc v&aacute;ng sữa v&agrave; c&aacute;c chất lỏng kh&aacute;c từ sữa đ&ocirc;ng, tạo ra một sản phẩm s&aacute;nh mịn với h&agrave;m lượng protein cao hơn sữa chua th&ocirc;ng thường. Sữa chua Hy Lạp cũng &iacute;t calo hơn nhiều nguồn protein kh&aacute;c. Một hộp 150 g cung cấp 15 g protein nhưng chỉ c&oacute; 92 calo. Ăn sữa chua Hy Lạp nguy&ecirc;n chất gi&agrave;u protein với c&aacute;c loại quả mọng v&agrave; quả kh&aacute;c c&oacute; thể bổ sung th&ecirc;m prebiotic, probiotic tốt cho ti&ecirc;u h&oacute;a. Th&ecirc;m tr&aacute;i c&acirc;y kh&ocirc;, yến mạch hoặc c&aacute;c loại hạt c&oacute; thể tăng th&ecirc;m kết cấu, chất xơ c&ugrave;ng c&aacute;c chất dinh dưỡng kh&aacute;c.</p>\r\n<p><strong> C&agrave; ph&ecirc; </strong></p>\r\n<p>C&agrave; ph&ecirc; chứa caffeine, gi&uacute;p tăng cường sự tỉnh t&aacute;o v&agrave; hiệu suất thể chất lẫn tinh thần. N&oacute; cũng c&oacute; thể chứa hợp chất polyphenol c&oacute; đặc t&iacute;nh chống oxy h&oacute;a, chống vi&ecirc;m. Người trưởng th&agrave;nh khỏe mạnh c&oacute; thể uống tối đa 4 t&aacute;ch c&agrave; ph&ecirc; mỗi ng&agrave;y (tương đương 946 ml hoặc 400 mg caffeine). Trong thời kỳ mang thai, phụ nữ kh&ocirc;ng n&ecirc;n ti&ecirc;u thụ qu&aacute; 200 mg caffeine mỗi ng&agrave;y, v&igrave; caffeine c&oacute; thể l&agrave;m tăng nguy cơ biến chứng. N&ecirc;n uống c&agrave; ph&ecirc; đen nguy&ecirc;n chất hoặc với sữa thực vật. Bạn n&ecirc;n tr&aacute;nh đường v&agrave; siro c&oacute; hương vị v&igrave; qu&aacute; nhiều đường l&agrave;m tăng nhiều nguy cơ sức khỏe.</p>\r\n<p><strong> Yến mạch </strong></p>\r\n<p>Yến mạch c&aacute;n mỏng hoặc yến mạch cắt nhỏ chứa một chất xơ h&ograve;a tan gọi l&agrave; beta-glucan, c&oacute; thể giảm cholesterol v&agrave; glucose, đồng thời c&oacute; đặc t&iacute;nh chống oxy h&oacute;a v&agrave; tốt cho đường ruột. Ăn thực phẩm n&agrave;y v&agrave;o bữa s&aacute;ng cũng gi&uacute;p bạn cảm thấy no l&acirc;u hơn, giảm cảm gi&aacute;c th&egrave;m ăn vặt v&agrave;o giữa buổi s&aacute;ng. Mỗi cốc (81 g) yến mạch kh&ocirc; chứa khoảng 10 g protein. Để tăng h&agrave;m lượng protein, h&atilde;y nấu yến mạch với sữa thay v&igrave; nước, trộn với một &iacute;t bột protein hoặc ăn k&egrave;m với trứng. Yến mạch ph&ugrave; hợp với người kh&ocirc;ng thể ăn gluten do bệnh celiac hoặc nhạy cảm với gluten.</p>\r\n<p><strong>C&aacute;c loại hạt </strong></p>\r\n<p>C&aacute;c loại hạt đều cung cấp magi&ecirc;, kali, chất b&eacute;o kh&ocirc;ng b&atilde;o h&ograve;a đơn tốt cho tim mạch, chất chống oxy h&oacute;a. H&agrave;m lượng protein, chất b&eacute;o v&agrave; chất xơ của ch&uacute;ng cũng tạo cảm gi&aacute;c no l&acirc;u. C&aacute;c loại hạt thường chứa nhiều calo n&ecirc;n ăn qu&aacute; nhiều c&oacute; thể dẫn đến tăng c&acirc;n qu&aacute; mức. Bạn n&ecirc;n kiểm so&aacute;t khẩu phần v&agrave; ưu ti&ecirc;n c&aacute;c loại hạt nguy&ecirc;n chất kh&ocirc;ng th&ecirc;m muối, đường hoặc dầu.</p>\r\n<p><strong> Tr&agrave; xanh </strong></p>\r\n<p>Tr&agrave; xanh l&agrave; thức uống gi&uacute;p tăng tỉnh t&aacute;o v&agrave;o buổi s&aacute;ng. Tr&agrave; xanh chứa caffeine nhưng chỉ bằng một nửa so với c&agrave; ph&ecirc;. Tr&agrave; xanh cũng chứa L-theanine, c&oacute; t&aacute;c dụng l&agrave;m dịu, giảm cảm gi&aacute;c bồn chồn li&ecirc;n quan đến ti&ecirc;u thụ caffeine. N&oacute; cũng c&oacute; thể cải thiện t&acirc;m trạng, đẩy l&ugrave;i t&igrave;nh trạng lo &acirc;u. Tr&agrave; xanh cũng cung cấp epigallocatechin gallate (EGCG), một chất chống oxy h&oacute;a c&oacute; thể bảo vệ khỏi c&aacute;c rối loạn thần kinh như chứng mất tr&iacute; nhớ, chết tế b&agrave;o.</p>\r\n<p><strong>Tr&aacute;i c&acirc;y</strong></p>\r\n<p>Tr&aacute;i c&acirc;y l&agrave; lựa chọn bữa s&aacute;ng nhẹ nh&agrave;ng. Ch&uacute;ng tương đối &iacute;t calo, nhiều chất xơ, vitamin, kho&aacute;ng chất v&agrave; đường đơn. Chất xơ trong tr&aacute;i c&acirc;y gi&uacute;p l&agrave;m chậm qu&aacute; tr&igrave;nh hấp thụ đường của cơ thể, cung cấp nguồn năng lượng ổn định. H&agrave;m lượng kho&aacute;ng chất trong tr&aacute;i c&acirc;y với h&agrave;m lượng lớn c&oacute; thể kh&aacute;c nhau t&ugrave;y theo loại tr&aacute;i c&acirc;y. C&aacute;c loại tr&aacute;i c&acirc;y gi&agrave;u kali bao gồm chuối, cam, dưa lưới, đu đủ, xo&agrave;i. Nhiều loại cung cấp vitamin C, hoạt động như chất chống oxy h&oacute;a v&agrave; rất quan trọng cho sức khỏe l&agrave;n da. Ch&uacute;ng bao gồm cam, ổi, kiwi, đu đủ, sơ ri, cherry, vải thiều. Tr&aacute;i c&acirc;y mọng &iacute;t calo, gi&agrave;u chất xơ v&agrave; chất chống oxy h&oacute;a. C&aacute;c lựa chọn phổ biến bao gồm việt quất, m&acirc;m x&ocirc;i, d&acirc;u t&acirc;y, m&acirc;m x&ocirc;i đen.</p>\r\n<p>Những loại n&agrave;y cung cấp chất chống oxy h&oacute;a gọi l&agrave; anthocyanin, tạo n&ecirc;n m&agrave;u xanh lam, t&iacute;m v&agrave; đỏ đặc trưng của ch&uacute;ng. Chế độ ăn gi&agrave;u anthocyanin c&oacute; thể bảo vệ cơ thể khỏi vi&ecirc;m, bệnh tim, ung thư, tiểu đường loại 2, c&aacute;c bệnh mạn t&iacute;nh kh&aacute;c. Tr&aacute;i c&acirc;y nguy&ecirc;n quả chứa nhiều chất xơ hơn nước &eacute;p tr&aacute;i c&acirc;y. Một số loại nước &eacute;p đ&oacute;ng chai c&ograve;n c&oacute; th&ecirc;m đường, v&igrave; vậy, tốt nhất n&ecirc;n ăn tr&aacute;i c&acirc;y nguy&ecirc;n quả.</p>', 'uploads/baiviet/1762998382_caphe3-1762931844-1762931860-8440-1762931962[1].jpg', 1, 6, '2025-11-13 08:46:22', '2025-11-13 08:46:22', 'published', 1, 'LATEST POSTS', 9),
(26, 'Thời điểm uống nước trong ngày tốt cho thận', 'thoi-diem-uong-nuoc-trong-ngay-tot-cho-than', '<p>Ph&acirc;n bổ c&aacute;c ngụm nước trong suốt cả ng&agrave;y, uống hầu hết chất lỏng trước buổi tối v&agrave; tu&acirc;n theo quy tắc 2 giờ trước khi ngủ gi&uacute;p thận khỏe.</p>\r\n<p>Nếu bạn thường xuy&ecirc;n uống \"ực ực\" nước v&agrave;o buổi tối hoặc trong bữa ăn, thận c&oacute; thể sẽ kh&ocirc;ng cảm ơn bạn. Ch&uacute;ng ưa th&iacute;ch sự hydrat h&oacute;a ổn định, với những ngụm nhỏ được ph&acirc;n bổ đều trong suốt cả ng&agrave;y. Kiểu uống n&agrave;y gi&uacute;p đ&agrave;o thải chất thải, bảo vệ chống lại sỏi thận v&agrave; duy tr&igrave; chất lượng giấc ngủ. Ngay cả một \"quy tắc 2 giờ\" đơn giản trước khi ngủ cũng c&oacute; thể tạo ra sự kh&aacute;c biệt lớn.</p>\r\n<p><strong>Tại sao thời điểm uống nước lại quan trọng? </strong></p>\r\n<p>Thận của bạn lọc m&aacute;u li&ecirc;n tục. Ch&uacute;ng hoạt động tốt nhất khi chất lỏng được đưa v&agrave;o ổn định, kh&ocirc;ng phải theo những \"cơn lũ\" đột ngột. Việc hydrat h&oacute;a nhất qu&aacute;n gi&uacute;p duy tr&igrave; thể t&iacute;ch m&aacute;u v&agrave; lưu lượng nước tiểu, hỗ trợ loại bỏ chất thải v&agrave; ngăn ngừa sỏi thận. C&aacute;c cơ quan y tế nhấn mạnh tầm quan trọng của việc uống chất lỏng đều đặn suốt cả ng&agrave;y, được điều chỉnh theo hoạt động, nhiệt độ v&agrave; nhu cầu sức khỏe c&aacute; nh&acirc;n, thay v&igrave; chỉ cố định theo một quy tắc duy nhất l&agrave; \"8 cốc nước\".</p>\r\n<p><strong>Hydrat h&oacute;a v&agrave; sức khỏe thận</strong></p>\r\n<p><em> Đủ chất lỏng h&agrave;ng ng&agrave;y</em></p>\r\n<p>C&aacute;c hướng dẫn khuyến nghị n&ecirc;n uống đủ nước để giữ cho nước tiểu c&oacute; m&agrave;u v&agrave;ng nhạt. Nhu cầu tăng l&ecirc;n khi nhiệt độ cao, tập thể dục v&agrave; chế độ ăn uống.</p>\r\n<p><em> Ngăn ngừa sỏi thận</em></p>\r\n<p>Hiệp hội Tiết niệu Mỹ (AUA) khuyến nghị sản xuất &iacute;t nhất 2,5 l&iacute;t nước tiểu mỗi ng&agrave;y để giảm nguy cơ sỏi - điều n&agrave;y chỉ c&oacute; thể đạt được th&ocirc;ng qua việc hydrat h&oacute;a ổn định từ s&aacute;ng đến tối.</p>\r\n<p><em> Giấc ngủ v&agrave; tiểu đ&ecirc;m</em></p>\r\n<p>Uống nhiều chất lỏng gần giờ đi ngủ sẽ l&agrave;m tăng tần suất đi tiểu v&agrave;o ban đ&ecirc;m (tiểu đ&ecirc;m), l&agrave;m gi&aacute;n đoạn giấc ngủ s&acirc;u. C&aacute;c nghi&ecirc;n cứu cho thấy việc ph&acirc;n bổ lượng nước uống đồng đều v&agrave; hạn chế chất lỏng hai giờ trước khi ngủ gi&uacute;p cải thiện chất lượng giấc ngủ v&agrave; giảm số lần đi vệ sinh v&agrave;o ban đ&ecirc;m.</p>\r\n<p><em>Tr&aacute;nh thừa nước </em></p>\r\n<p>Uống qu&aacute; nhiều nước một c&aacute;ch nhanh ch&oacute;ng c&oacute; thể l&agrave;m lo&atilde;ng natri trong m&aacute;u, dẫn đến hạ natri m&aacute;u. Uống từ từ gi&uacute;p tr&aacute;nh t&igrave;nh trạng n&agrave;y.</p>\r\n<p><strong>C&aacute;ch ph&acirc;n bổ lượng nước uống trong ng&agrave;y</strong></p>\r\n<p>Dưới đ&acirc;y l&agrave; c&aacute;ch ph&acirc;n bổ lượng nước uống trong ng&agrave;y để hỗ trợ thận:</p>\r\n<p><em>Buổi s&aacute;ng (trong v&ograve;ng 30-60 ph&uacute;t sau khi thức dậy) </em></p>\r\n<p>Uống 250-300 ml để b&ugrave; nước sau khi mất nước qua đ&ecirc;m v&agrave; hỗ trợ qu&aacute; tr&igrave;nh lọc của thận.</p>\r\n<p><em>Trong mỗi bữa ăn</em></p>\r\n<p>Uống 250 ml để hỗ trợ ti&ecirc;u h&oacute;a v&agrave; giữ cho qu&aacute; tr&igrave;nh hydrat h&oacute;a ổn định m&agrave; kh&ocirc;ng g&acirc;y qu&aacute; tải.</p>\r\n<p><em> Giữa c&aacute;c bữa ăn</em></p>\r\n<p>Uống 150-250 ml cứ sau 60-90 ph&uacute;t nhằm duy tr&igrave; lưu lượng nước tiểu để đ&agrave;o thải chất thải v&agrave; ngăn ngừa sự t&iacute;ch tụ tinh thể.</p>\r\n<p><em>Trong khi vận động hoặc trời n&oacute;ng</em></p>\r\n<p>N&ecirc;n uống 150-350 ml cứ sau 20-30 ph&uacute;t b&ugrave; lại lượng mồ h&ocirc;i mất đi dần dần để bảo vệ sự c&acirc;n bằng điện giải.</p>\r\n<p><em> Hai giờ trước khi ngủ </em></p>\r\n<p>Ngừng uống chất lỏng thường xuy&ecirc;n; chỉ uống từng ngụm nhỏ (30-60 ml) nếu cần để giảm tiểu đ&ecirc;m v&agrave; cải thiện giấc ngủ kh&ocirc;ng bị gi&aacute;n đoạn.</p>\r\n<p><strong>C&aacute;c mẹo uống nước buổi tối để giảm số lần đi vệ sinh </strong></p>\r\n<p><em>Tập trung uống nước sớm </em></p>\r\n<p>Đặt mục ti&ecirc;u uống 60-70% lượng nước h&agrave;ng ng&agrave;y trước 4 giờ chiều.</p>\r\n<p><em>Tr&aacute;nh caffeine hoặc rượu v&agrave;o cuối buổi tối </em></p>\r\n<p>Cả hai đều hoạt động như thuốc lợi tiểu, l&agrave;m tăng lượng nước tiểu.</p>\r\n<p><em> Chọn nước lọc ở nhiệt độ ph&ograve;ng trước khi ngủ</em></p>\r\n<p>Đồ uống qu&aacute; lạnh c&oacute; thể k&iacute;ch hoạt việc uống nhanh v&agrave; ti&ecirc;u thụ qu&aacute; mức.</p>\r\n<p><em>K&ecirc; cao ch&acirc;n sau bữa tối (30-60 ph&uacute;t)</em></p>\r\n<p>Gi&uacute;p ph&acirc;n bổ lại c&aacute;c chất lỏng bị ứ đọng v&agrave; giảm cảm gi&aacute;c muốn đi vệ sinh l&uacute;c 2 giờ s&aacute;ng, hữu &iacute;ch cho người lớn tuổi.</p>\r\n<p><strong> C&aacute;ch nhận biết bạn c&oacute; đang uống đủ lượng nước cần thiết</strong></p>\r\n<p><em> M&agrave;u nước tiểu</em></p>\r\n<p>M&agrave;u v&agrave;ng nhạt l&agrave; hydrat h&oacute;a tốt. M&agrave;u v&agrave;ng đậm c&oacute; nghĩa cần uống th&ecirc;m nước. Nước tiểu trong vắt lặp lại c&oacute; thể l&agrave; dấu hiệu thừa nước.</p>\r\n<p><em>Tần suất</em></p>\r\n<p>Đi vệ sinh 5-7 lần ban ng&agrave;y l&agrave; b&igrave;nh thường. Qu&aacute; &iacute;t c&oacute; nghĩa l&agrave; mất nước; qu&aacute; nhiều v&agrave;o ban đ&ecirc;m c&oacute; nghĩa l&agrave; đ&atilde; uống nhiều v&agrave;o cuối ng&agrave;y.</p>\r\n<p><em> Triệu chứng </em></p>\r\n<p>Cảm gi&aacute;c kh&aacute;t, kh&ocirc; miệng, hoặc ch&oacute;ng mặt chứng tỏ uống qu&aacute; &iacute;t nước; đầy hơi hoặc buồn n&ocirc;n sau khi uống nhiều c&oacute; nghĩa đ&atilde; uống qu&aacute; nhiều nước.</p>\r\n<p><strong> Ai n&ecirc;n c&aacute; nh&acirc;n h&oacute;a thời điểm uống nước? </strong></p>\r\n<p>Những người mắc bệnh thận m&atilde;n t&iacute;nh, suy tim, bệnh gan tiến triển, hoặc những người đang d&ugrave;ng thuốc lợi tiểu cần c&oacute; kế hoạch chất lỏng c&aacute; nh&acirc;n h&oacute;a từ b&aacute;c sĩ. Phụ nữ mang thai v&agrave; người lớn tuổi cũng n&ecirc;n tham khảo &yacute; kiến của nh&agrave; cung cấp dịch vụ chăm s&oacute;c sức khỏe để điều chỉnh lượng v&agrave; thời điểm uống.</p>\r\n<p>H&atilde;y ph&acirc;n bổ c&aacute;c ngụm nước của bạn trong suốt cả ng&agrave;y, uống hầu hết chất lỏng trước buổi tối v&agrave; tu&acirc;n theo quy tắc 2 giờ trước khi ngủ. Nhịp điệu đơn giản n&agrave;y gi&uacute;p thận của bạn lọc hiệu quả, ngăn ngừa sỏi v&agrave; bảo vệ giấc ngủ của bạn, m&agrave; kh&ocirc;ng cần phải vội v&atilde; v&agrave;o nh&agrave; vệ sinh l&uacute;c 3 giờ s&aacute;ng.</p>\r\n<p>H&atilde;y bắt đầu ng&agrave;y mai bằng một ly nước, điều chỉnh tốc độ uống của bạn v&agrave; đặt ra giới hạn nhẹ nh&agrave;ng trước khi ngủ. H&atilde;y để &yacute; nước tiểu của bạn giữ m&agrave;u v&agrave;ng nhạt, giấc ngủ k&eacute;o d&agrave;i hơn v&agrave; số lần đi vệ sinh giảm đi. Giữ một chai nhỏ b&ecirc;n cạnh, đổ đầy suốt cả ng&agrave;y v&agrave; để thời điểm uống ph&aacute;t huy t&aacute;c dụng. Thận sẽ thầm lặng cảm ơn bạn mỗi ng&agrave;y.</p>', 'uploads/baiviet/1762998846_Screenshot-2025-11-12-at-22-15-9825-2037-1762960624[1].png', 1, 3, '2025-11-13 08:54:06', '2025-11-13 08:54:06', 'published', 21, 'MAIN HIGHLIGHTS', 9);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bai_the`
--

CREATE TABLE `bai_the` (
  `ma_bai_viet` int(11) NOT NULL,
  `ma_the` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binhluan`
--

CREATE TABLE `binhluan` (
  `id_binhluan` int(11) NOT NULL,
  `id_kh` int(11) DEFAULT NULL,
  `ma_bai_viet` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_binhluan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `binhluan`
--

INSERT INTO `binhluan` (`id_binhluan`, `id_kh`, `ma_bai_viet`, `noi_dung`, `ngay_binhluan`) VALUES
(27, 9, 9, 'a', '2025-11-11 21:08:15'),
(28, 9, 9, 'c', '2025-11-11 21:22:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuyenmuc`
--

CREATE TABLE `chuyenmuc` (
  `ma_chuyen_muc` int(11) NOT NULL,
  `ten_chuyen_muc` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chuyenmuc`
--

INSERT INTO `chuyenmuc` (`ma_chuyen_muc`, `ten_chuyen_muc`, `mo_ta`) VALUES
(1, 'Tin tức', 'Cập nhật tin tức y tế, dịch bệnh và sức khỏe đời sống.'),
(2, 'Dinh dưỡng', 'Chia sẻ chế độ ăn, thực phẩm tốt và cách ăn uống khoa học.'),
(3, 'Khỏe đẹp', 'Bí quyết giữ dáng, làm đẹp và tập luyện hiệu quả.'),
(4, 'Tư vấn', 'Các bài viết hỏi đáp, chia sẻ từ chuyên gia sức khỏe.'),
(5, 'Dịch vụ y tế', 'Cung cấp thông tin bệnh viện, công nghệ và dịch vụ chăm sóc sức khỏe.'),
(6, 'Các bệnh', 'Kiến thức về bệnh lý, dấu hiệu và cách phòng ngừa.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dangnhap`
--

CREATE TABLE `dangnhap` (
  `id_dn` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ngay_dn` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dangnhap`
--

INSERT INTO `dangnhap` (`id_dn`, `username`, `password`, `ngay_dn`) VALUES
(13, 'admin', 'baka', '2025-11-11 10:29:35'),
(14, 'admin', 'baka', '2025-11-11 11:06:15'),
(15, 'admin', 'baka', '2025-11-11 11:07:08'),
(16, 'admin', 'baka', '2025-11-11 12:06:06'),
(17, 'admin', 'baka', '2025-11-11 13:44:29'),
(18, 'admin', 'baka', '2025-11-11 14:07:49'),
(19, 'admin', 'baka', '2025-11-11 16:42:32'),
(20, 'baka', 'admin', '2025-11-11 16:42:56'),
(21, 'admin', 'baka', '2025-11-11 16:51:04'),
(22, 'baka', 'admin', '2025-11-11 16:51:12'),
(23, 'admin', 'baka', '2025-11-12 00:18:32'),
(24, 'admin', 'baka', '2025-11-12 01:40:46'),
(25, 'baka', 'admin', '2025-11-12 02:39:30'),
(26, 'admin', 'baka', '2025-11-12 02:39:36'),
(27, 'baka', 'admin', '2025-11-12 02:43:15'),
(28, 'admin', 'baka', '2025-11-12 02:43:38'),
(29, 'baka', 'admin', '2025-11-12 03:03:58'),
(30, 'admin', 'baka', '2025-11-12 03:04:09'),
(31, 'baka', 'admin', '2025-11-12 03:10:03'),
(32, 'admin', 'baka', '2025-11-12 03:10:24'),
(33, 'baka', 'admin', '2025-11-12 03:15:04'),
(34, 'admin', 'baka', '2025-11-12 03:15:25'),
(35, 'baka', 'admin', '2025-11-12 03:18:49'),
(36, 'admin', 'baka', '2025-11-12 03:19:03'),
(37, 'baka', 'admin', '2025-11-12 03:55:34'),
(38, 'admin', 'baka', '2025-11-12 04:06:05'),
(39, 'baka', 'admin', '2025-11-12 04:06:20'),
(40, 'admin', 'baka', '2025-11-12 04:09:12'),
(41, 'baka', 'admin', '2025-11-12 04:09:19'),
(42, 'admin', 'baka', '2025-11-12 04:09:50'),
(43, 'baka', 'admin', '2025-11-12 04:10:06'),
(44, 'admin', 'baka', '2025-11-12 04:12:20'),
(45, 'baka', 'admin', '2025-11-12 04:13:27'),
(46, 'admin', 'baka', '2025-11-12 07:01:44'),
(47, 'baka', 'admin', '2025-11-12 07:30:55'),
(48, 'baka', 'admin', '2025-11-12 08:09:25'),
(49, 'admin', 'baka', '2025-11-12 08:19:30'),
(50, 'baka', 'admin', '2025-11-12 08:23:32'),
(51, 'baka', 'admin', '2025-11-12 08:38:52'),
(52, 'admin', 'baka', '2025-11-12 11:03:14'),
(53, 'baka', 'admin', '2025-11-12 14:30:49'),
(54, 'baka', 'admin', '2025-11-12 14:31:32'),
(55, 'admin', 'baka', '2025-11-12 16:27:52'),
(56, 'admin', 'baka', '2025-11-12 23:10:10'),
(57, 'admin', 'baka', '2025-11-12 23:13:57'),
(58, 'admin', 'baka', '2025-11-13 01:01:13'),
(59, 'test', 'ac', '2025-11-13 03:29:11'),
(60, 'test', 'ac', '2025-11-13 03:29:20'),
(61, 'admin', 'baka', '2025-11-13 03:33:16'),
(62, 'baka', 'admin', '2025-11-13 03:33:25'),
(63, 'admin', 'baka', '2025-11-13 03:33:39'),
(64, 'admin', 'baka', '2025-11-19 00:56:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `diemdoc`
--

CREATE TABLE `diemdoc` (
  `id` int(11) NOT NULL,
  `id_kh` int(11) NOT NULL,
  `ma_bai_viet` int(11) DEFAULT NULL,
  `diem_cong` int(11) DEFAULT NULL,
  `loai_giao_dich` varchar(50) DEFAULT 'doc_bai',
  `ngay_them` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `diemdoc`
--

INSERT INTO `diemdoc` (`id`, `id_kh`, `ma_bai_viet`, `diem_cong`, `loai_giao_dich`, `ngay_them`) VALUES
(42, 10, NULL, 50, 'doc_bai', '2025-11-12 15:12:27'),
(43, 10, NULL, -1, 'doi_xp', '2025-11-12 15:15:58'),
(44, 10, NULL, -1, 'doi_xp', '2025-11-12 15:17:20'),
(45, 10, NULL, -48, 'doi_xp', '2025-11-12 15:17:35'),
(46, 9, NULL, 50, 'doc_bai', '2025-11-12 15:19:38'),
(47, 10, NULL, 0, 'xem_bai', '2025-11-12 15:28:25'),
(48, 9, NULL, 0, 'xem_bai', '2025-11-12 18:03:16'),
(49, 9, 10, 0, 'xem_bai', '2025-11-12 20:41:47'),
(50, 9, 11, 0, 'xem_bai', '2025-11-12 20:54:42'),
(51, 9, 10, 0, 'doc_bai', '2025-11-12 21:26:36'),
(52, 10, 10, 50, 'xem_bai', '2025-11-12 21:30:52'),
(53, 10, 11, 50, 'xem_bai', '2025-11-12 21:31:07'),
(54, 10, NULL, -4, 'doi_xp', '2025-11-12 21:35:19'),
(55, 10, 12, 50, 'xem_bai', '2025-11-12 21:57:01'),
(56, 9, 12, 50, 'xem_bai', '2025-11-12 23:39:10'),
(57, 9, 13, 50, 'xem_bai', '2025-11-13 00:12:10'),
(58, 9, 14, 50, 'xem_bai', '2025-11-13 00:18:29'),
(59, 9, 15, 50, 'xem_bai', '2025-11-13 00:24:50'),
(60, 9, 16, 50, 'xem_bai', '2025-11-13 06:19:29'),
(61, 9, 17, 50, 'xem_bai', '2025-11-13 06:21:03'),
(62, 9, 18, 50, 'xem_bai', '2025-11-13 06:29:51'),
(63, 9, 19, 50, 'xem_bai', '2025-11-13 06:32:59'),
(64, 9, 20, 50, 'xem_bai', '2025-11-13 06:35:35'),
(65, 9, 21, 50, 'xem_bai', '2025-11-13 06:38:53'),
(66, 9, 22, 50, 'xem_bai', '2025-11-13 06:44:57'),
(67, 9, 23, 50, 'xem_bai', '2025-11-13 08:31:56'),
(68, 9, 24, 50, 'xem_bai', '2025-11-13 08:40:57'),
(69, 9, 25, 50, 'xem_bai', '2025-11-13 08:46:30'),
(70, 9, 26, 50, 'xem_bai', '2025-11-13 08:56:57'),
(71, 11, 24, 50, 'xem_bai', '2025-11-13 10:22:41'),
(72, 12, 10, 50, 'xem_bai', '2025-11-13 10:35:33'),
(73, 12, 12, 50, 'xem_bai', '2025-11-13 10:35:36'),
(74, 9, 26, 50, 'xem_bai', '2025-11-19 08:10:30'),
(75, 9, 10, 50, 'xem_bai', '2025-11-19 08:43:19'),
(76, 9, 23, 50, 'xem_bai', '2025-11-19 08:43:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doimatkhau`
--

CREATE TABLE `doimatkhau` (
  `id_dmk` int(11) NOT NULL,
  `id_kh` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `id_kh` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `gioi_tinh` enum('Nam','Nữ','Khác') DEFAULT 'Khác',
  `ngay_sinh` date DEFAULT NULL,
  `quoc_gia` varchar(50) DEFAULT 'Việt Nam',
  `tinh_thanh` varchar(50) DEFAULT NULL,
  `vai_tro` enum('Khach','NhanVien','QuanTri') DEFAULT 'Khach',
  `so_diem` int(11) NOT NULL DEFAULT 0,
  `xp` int(11) DEFAULT 0,
  `avatar_url` varchar(255) DEFAULT NULL,
  `avatar_frame` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`id_kh`, `ho_ten`, `email`, `sdt`, `dia_chi`, `gioi_tinh`, `ngay_sinh`, `quoc_gia`, `tinh_thanh`, `vai_tro`, `so_diem`, `xp`, `avatar_url`, `avatar_frame`) VALUES
(9, 'Hayase Yuuka', 'baka@gmail.com', '8686868686', 'Trái Đất', 'Nữ', '2005-12-04', 'Việt Nam', 'Hà Nội', 'QuanTri', 950, 0, '../uploads/avatars/1762857789_$value[1].png', 'chiu'),
(10, 'Yuuka Pajama', 'takina412@gmail.com', NULL, NULL, 'Khác', NULL, 'Việt Nam', NULL, 'NhanVien', 147, 100, '../uploads/avatars/1762915411_azusa.jpg', 'gi'),
(12, 'test', 'a@gmail.com', NULL, NULL, 'Khác', NULL, 'Việt Nam', NULL, 'Khach', 100, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lienhe`
--

CREATE TABLE `lienhe` (
  `id` int(11) NOT NULL,
  `id_kh` int(11) DEFAULT NULL,
  `noidung` text NOT NULL,
  `ngaygui` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien_yc`
--

CREATE TABLE `nhanvien_yc` (
  `id` int(11) NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `sdt` varchar(20) NOT NULL,
  `the_loai` varchar(255) NOT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `id_kh` int(11) NOT NULL,
  `trang_thai` enum('chờ duyệt','đã duyệt','bị từ chối') DEFAULT 'chờ duyệt',
  `vai_tro` varchar(255) DEFAULT 'Khach'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien_yc`
--

INSERT INTO `nhanvien_yc` (`id`, `ho_ten`, `sdt`, `the_loai`, `ngay_tao`, `id_kh`, `trang_thai`, `vai_tro`) VALUES
(5, 'Yuuka Pajama', '0987654321', 'Đăng bài viết', '2025-11-12 10:18:57', 10, 'đã duyệt', 'NhanVien');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `saved_posts`
--

CREATE TABLE `saved_posts` (
  `id` int(11) NOT NULL,
  `id_kh` int(11) NOT NULL,
  `ma_bai_viet` int(11) NOT NULL,
  `saved_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `saved_posts`
--

INSERT INTO `saved_posts` (`id`, `id_kh`, `ma_bai_viet`, `saved_at`) VALUES
(3, 9, 26, '2025-11-19 08:28:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taotaikhoan`
--

CREATE TABLE `taotaikhoan` (
  `id_tk` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirm_password` varchar(255) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_kh` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taotaikhoan`
--

INSERT INTO `taotaikhoan` (`id_tk`, `username`, `password`, `confirm_password`, `ngay_tao`, `id_kh`) VALUES
(3, 'admin', 'baka', 'baka', '2025-11-11 10:29:00', 9),
(4, 'baka', 'admin', 'admin', '2025-11-11 16:23:16', 10),
(6, 'test', 'ab', 'ab', '2025-11-13 03:34:29', 12);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teptin`
--

CREATE TABLE `teptin` (
  `ma_tep` int(11) NOT NULL,
  `ma_bai_viet` int(11) DEFAULT NULL,
  `loai` enum('anh','video','infographic') DEFAULT 'anh',
  `duong_dan` varchar(255) DEFAULT NULL,
  `chu_thich` varchar(255) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `teptin`
--

INSERT INTO `teptin` (`ma_tep`, `ma_bai_viet`, `loai`, `duong_dan`, `chu_thich`, `ngay_tao`) VALUES
(1, NULL, 'video', '/media/videos/gian-co-1.mp4', 'Video bài tập giãn cơ', '2025-10-30 10:35:25'),
(2, NULL, 'infographic', '/media/info/10p-chart.png', 'Infographic 10 phút', '2025-10-30 10:35:25'),
(3, NULL, 'anh', '/media/images/5p-break.jpg', 'Ảnh minh họa nghỉ 5 phút', '2025-10-30 10:35:25'),
(4, NULL, 'anh', '/media/images/chon-ghe.jpg', 'Ảnh hướng dẫn ghế', '2025-10-30 10:35:25'),
(5, NULL, 'anh', '/media/images/mat-foods.jpg', 'Ảnh thực phẩm tốt cho mắt', '2025-10-30 10:35:25'),
(6, NULL, 'anh', '/media/images/snack-healthy.jpg', 'Ảnh snack lành mạnh', '2025-10-30 10:35:25'),
(7, NULL, 'video', '/media/videos/sleep-tips.mp4', 'Video mẹo ngủ ngon', '2025-10-30 10:35:25'),
(8, NULL, 'anh', '/media/images/blue-light.jpg', 'Ảnh kính lọc ánh sáng xanh', '2025-10-30 10:35:25'),
(9, NULL, 'infographic', '/media/info/avoid-foods.png', 'Infographic thực phẩm nên tránh', '2025-10-30 10:35:25'),
(10, NULL, 'anh', '/media/images/breathing.jpg', 'Ảnh hướng dẫn thở', '2025-10-30 10:35:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `the`
--

CREATE TABLE `the` (
  `ma_the` int(11) NOT NULL,
  `ten_the` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `the`
--

INSERT INTO `the` (`ma_the`, `ten_the`) VALUES
(4, 'dinh dưỡng'),
(3, 'giấc ngủ'),
(2, 'giãn cơ'),
(10, 'infographic'),
(6, 'kính lọc'),
(1, 'mỏi mắt'),
(8, 'thở'),
(7, 'thực phẩm'),
(5, 'tư thế'),
(9, 'video');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thongbao`
--

CREATE TABLE `thongbao` (
  `id` int(11) NOT NULL,
  `id_kh` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `da_doc` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `baiviet`
--
ALTER TABLE `baiviet`
  ADD PRIMARY KEY (`ma_bai_viet`),
  ADD UNIQUE KEY `duong_dan` (`duong_dan`),
  ADD KEY `ma_tac_gia` (`ma_tac_gia`),
  ADD KEY `ma_chuyen_muc` (`ma_chuyen_muc`);

--
-- Chỉ mục cho bảng `bai_the`
--
ALTER TABLE `bai_the`
  ADD PRIMARY KEY (`ma_bai_viet`,`ma_the`),
  ADD KEY `ma_the` (`ma_the`);

--
-- Chỉ mục cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  ADD PRIMARY KEY (`id_binhluan`),
  ADD KEY `id_kh` (`id_kh`);

--
-- Chỉ mục cho bảng `chuyenmuc`
--
ALTER TABLE `chuyenmuc`
  ADD PRIMARY KEY (`ma_chuyen_muc`),
  ADD UNIQUE KEY `ten_chuyen_muc` (`ten_chuyen_muc`);

--
-- Chỉ mục cho bảng `dangnhap`
--
ALTER TABLE `dangnhap`
  ADD PRIMARY KEY (`id_dn`);

--
-- Chỉ mục cho bảng `diemdoc`
--
ALTER TABLE `diemdoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ma_bai_viet` (`ma_bai_viet`);

--
-- Chỉ mục cho bảng `doimatkhau`
--
ALTER TABLE `doimatkhau`
  ADD PRIMARY KEY (`id_dmk`),
  ADD KEY `id_kh` (`id_kh`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`id_kh`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `lienhe`
--
ALTER TABLE `lienhe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kh` (`id_kh`);

--
-- Chỉ mục cho bảng `nhanvien_yc`
--
ALTER TABLE `nhanvien_yc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `taotaikhoan`
--
ALTER TABLE `taotaikhoan`
  ADD PRIMARY KEY (`id_tk`),
  ADD KEY `id_kh` (`id_kh`);

--
-- Chỉ mục cho bảng `teptin`
--
ALTER TABLE `teptin`
  ADD PRIMARY KEY (`ma_tep`),
  ADD KEY `ma_bai_viet` (`ma_bai_viet`);

--
-- Chỉ mục cho bảng `the`
--
ALTER TABLE `the`
  ADD PRIMARY KEY (`ma_the`),
  ADD UNIQUE KEY `ten_the` (`ten_the`);

--
-- Chỉ mục cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kh` (`id_kh`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `baiviet`
--
ALTER TABLE `baiviet`
  MODIFY `ma_bai_viet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  MODIFY `id_binhluan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `chuyenmuc`
--
ALTER TABLE `chuyenmuc`
  MODIFY `ma_chuyen_muc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `dangnhap`
--
ALTER TABLE `dangnhap`
  MODIFY `id_dn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT cho bảng `diemdoc`
--
ALTER TABLE `diemdoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT cho bảng `doimatkhau`
--
ALTER TABLE `doimatkhau`
  MODIFY `id_dmk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `id_kh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `lienhe`
--
ALTER TABLE `lienhe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nhanvien_yc`
--
ALTER TABLE `nhanvien_yc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `saved_posts`
--
ALTER TABLE `saved_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `taotaikhoan`
--
ALTER TABLE `taotaikhoan`
  MODIFY `id_tk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `teptin`
--
ALTER TABLE `teptin`
  MODIFY `ma_tep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `the`
--
ALTER TABLE `the`
  MODIFY `ma_the` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bai_the`
--
ALTER TABLE `bai_the`
  ADD CONSTRAINT `bai_the_ibfk_1` FOREIGN KEY (`ma_bai_viet`) REFERENCES `baiviet` (`ma_bai_viet`) ON DELETE CASCADE,
  ADD CONSTRAINT `bai_the_ibfk_2` FOREIGN KEY (`ma_the`) REFERENCES `the` (`ma_the`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  ADD CONSTRAINT `binhluan_ibfk_1` FOREIGN KEY (`id_kh`) REFERENCES `khachhang` (`id_kh`);

--
-- Các ràng buộc cho bảng `diemdoc`
--
ALTER TABLE `diemdoc`
  ADD CONSTRAINT `diemdoc_ibfk_2` FOREIGN KEY (`ma_bai_viet`) REFERENCES `baiviet` (`ma_bai_viet`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `doimatkhau`
--
ALTER TABLE `doimatkhau`
  ADD CONSTRAINT `doimatkhau_ibfk_1` FOREIGN KEY (`id_kh`) REFERENCES `khachhang` (`id_kh`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lienhe`
--
ALTER TABLE `lienhe`
  ADD CONSTRAINT `lienhe_ibfk_1` FOREIGN KEY (`id_kh`) REFERENCES `khachhang` (`id_kh`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `taotaikhoan`
--
ALTER TABLE `taotaikhoan`
  ADD CONSTRAINT `taotaikhoan_ibfk_1` FOREIGN KEY (`id_kh`) REFERENCES `khachhang` (`id_kh`);

--
-- Các ràng buộc cho bảng `teptin`
--
ALTER TABLE `teptin`
  ADD CONSTRAINT `teptin_ibfk_1` FOREIGN KEY (`ma_bai_viet`) REFERENCES `baiviet` (`ma_bai_viet`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`id_kh`) REFERENCES `khachhang` (`id_kh`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
