-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 12, 2025 lúc 09:56 AM
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
(9, 'The Apothecary Diaries Season 3 & Original New Movie Announced for 2026-2027', 'the-apothecary-diaries-season-3-original-new-movie-announced-for-2026-2027', '<p style=\"box-sizing: border-box; outline: 0px; margin: 0px 0px 17px; padding: 0px; font-size: 17px; line-height: 1.8; color: #313131; font-family: sans-serif; background-color: #ffffff;\"><em style=\"box-sizing: border-box; outline: 0px; margin: 0px; padding: 0px;\">The Apothecary Diaries&nbsp;</em>anime is officially coming back in 2026 and 2027 with Season 3 and an original new movie. The sequel was&nbsp;<a style=\"box-sizing: border-box; outline: 0px; margin: 0px; padding: 0px; text-decoration-line: none; color: #e34c56; transition: color 0.3s; cursor: pointer;\" href=\"https://animecorner.me/the-apothecary-diaries-sequel-anime-officially-announced-after-season-2/\">previously confirmed</a>&nbsp;immediately after Season 2, but it was now specified to be another TV anime season. It will run for two split cours in October 2026 and April 2027.</p>\r\n<p style=\"box-sizing: border-box; outline: 0px; margin: 0px 0px 17px; padding: 0px; font-size: 17px; line-height: 1.8; color: #313131; font-family: sans-serif; background-color: #ffffff;\">The movie will premiere in Japan in December 2026 following the first part of the TV anime, and it will feature an original story by the author Natsu Hyuuga. You can watch the announcement trailer and see the key visual below.</p>\r\n<p style=\"box-sizing: border-box; outline: 0px; margin: 0px 0px 17px; padding: 0px; font-size: 17px; line-height: 1.8; color: #313131; font-family: sans-serif; background-color: #ffffff;\"><img src=\"https://static.animecorner.me/2025/10/1761135380-ac32ccb8a87c105a0573122cc5f526b3.jpg\" alt=\"\" width=\"1080\" height=\"1528\" /></p>', 'uploads/baiviet/1762857422_1761135682-3633e3e08b3ad181294c1a77ad1447f4[1].png', 1, 1, '2025-11-11 17:37:02', '2025-11-11 17:37:02', 'published', 27, 'MAIN HIGHLIGHTS', 9);

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
(1, 'Dinh dưỡng', 'Bài viết về chế độ ăn, thực phẩm tốt cho mắt và sức khỏe'),
(2, 'Tập luyện', 'Bài tập vận động, giãn cơ'),
(3, 'Nghỉ ngơi', 'Giấc ngủ và phục hồi năng lượng'),
(4, 'Sức khỏe tinh thần', 'Quản lý stress, cân bằng tâm lý'),
(5, 'Mẹo mắt - lưng', 'Bảo vệ mắt và cột sống cho người ngồi lâu');

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
(51, 'baka', 'admin', '2025-11-12 08:38:52');

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
(42, 10, 9, 50, 'doc_bai', '2025-11-12 15:12:27'),
(43, 10, NULL, -1, 'doi_xp', '2025-11-12 15:15:58'),
(44, 10, NULL, -1, 'doi_xp', '2025-11-12 15:17:20'),
(45, 10, NULL, -48, 'doi_xp', '2025-11-12 15:17:35'),
(46, 9, 9, 50, 'doc_bai', '2025-11-12 15:19:38'),
(47, 10, 9, 0, 'xem_bai', '2025-11-12 15:28:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doimatkhau`
--

CREATE TABLE `doimatkhau` (
  `id_dmk` int(11) NOT NULL,
  `id_kh` int(11) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
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
(9, 'Hayase Yuuka', 'baka@gmail.com', NULL, NULL, 'Khác', NULL, 'Việt Nam', NULL, 'QuanTri', 50, 0, '../uploads/avatars/1762857789_$value[1].png', 'nahida'),
(10, 'Yuuka Pajama', 'takina412@gmail.com', NULL, NULL, 'Khác', NULL, 'Việt Nam', NULL, 'NhanVien', 1, 96, '../uploads/avatars/1762915411_azusa.jpg', 'game');

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
(4, 'baka', 'admin', 'admin', '2025-11-11 16:23:16', 10);

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
-- Chỉ mục cho bảng `nhanvien_yc`
--
ALTER TABLE `nhanvien_yc`
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
  MODIFY `ma_bai_viet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  MODIFY `id_binhluan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `chuyenmuc`
--
ALTER TABLE `chuyenmuc`
  MODIFY `ma_chuyen_muc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `dangnhap`
--
ALTER TABLE `dangnhap`
  MODIFY `id_dn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT cho bảng `diemdoc`
--
ALTER TABLE `diemdoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT cho bảng `doimatkhau`
--
ALTER TABLE `doimatkhau`
  MODIFY `id_dmk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `id_kh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `nhanvien_yc`
--
ALTER TABLE `nhanvien_yc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `taotaikhoan`
--
ALTER TABLE `taotaikhoan`
  MODIFY `id_tk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
