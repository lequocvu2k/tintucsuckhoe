<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <title>Gửi thư qua Gmail</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/fw.css">
  <link rel="stylesheet" href="../css/mail.css">
  <link rel="stylesheet" href="../css/menu.css">
  <script src="../resources/js/anime.min.js"></script>
  <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
  <script src="../js/fireworks.js" async defer></script>
  <script src="../js/menu.js" defer></script>
</head>

<body>
  <video autoplay muted loop>
    <source src="../video/background2.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="form-container">
    <div class="form-header">
      <h2> Gửi thư cho chúng tôi</h2>
      <a href="../php/index.php" class="backhome">❌</a>
    </div>
    <hr>
    <form action="mail.php" method="POST" autocomplete="off">
      <label for="to">Email:</label>
      <input type="email" id="to" name="to" required placeholder="Nhập email của bạn" />

      <label for="subject">Tiêu đề:</label>
      <input type="text" id="subject" name="subject" required placeholder="Tiêu đề" />

      <label for="message">Nội dung:</label>
      <textarea id="message" name="message" rows="5" required
        placeholder="Người tiện tay vẽ hoa vẽ lá, Tôi đa tình tưởng đó là mùa xuân..."></textarea>

      <button type="submit">Gửi</button>
    </form>
  </div>

</body>

</html>