<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>رسید خدمات پولی شاهین</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    @page {
      size: A6;
      margin: 0;
    }

    body {
      font-family: 'Noto Naskh Arabic', serif;
      background: #fffef8;
      margin: 0;
      padding: 0;
    }

    .a6-container {
      width: 105mm;
      height: 148mm;
      overflow: hidden;
      position: relative;
      background-color: #fffef8;
    }

    .gold-border {
      background: linear-gradient(to right, #996515, #d4af37, #f0e68c, #d4af37, #996515);
    }

    .islamic-pattern {
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23d4af37' fill-opacity='0.05'%3E%3Cpath d='M30 0L15 15H0v15l15 15v15h15l15-15h15V30L45 15V0H30zm0 30a5 5 0 1 1 0-10 5 5 0 0 1 0 10z'/%3E%3C/g%3E%3C/svg%3E");
      background-size: cover;
    }

    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-45deg);
      font-size: 80px;
      color: rgba(212, 175, 55, 0.05);
      white-space: nowrap;
      pointer-events: none;
      z-index: 0;
      font-weight: bold;
    }

    .subtle-divider {
      height: 1px;
      background: linear-gradient(to right, transparent, #d4af37, transparent);
    }

    .decorative-pattern {
      height: 8px;
      background-image: url("data:image/svg+xml,%3Csvg width='40' height='8' viewBox='0 0 40 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h5v1H0v1h10v1H0v1h15v1H0v1h20v1H0v1h40V0H0z' fill='%23d4af37' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    @keyframes glow {
      0% { filter: drop-shadow(0 0 1px rgba(212, 175, 55, 0.3)); }
      50% { filter: drop-shadow(0 0 3px rgba(212, 175, 55, 0.6)); }
      100% { filter: drop-shadow(0 0 1px rgba(212, 175, 55, 0.3)); }
    }

    .logo-glow {
      animation: glow 3s infinite;
    }

    .company-name {
      position: relative;
      padding-right: 10px;
    }

    .company-name::before {
      content: "";
      position: absolute;
      right: 0;
      top: 10%;
      height: 80%;
      width: 3px;
      background: linear-gradient(to bottom, transparent, #d4af37, transparent);
    }

    .footer-decoration::before,
    .footer-decoration::after {
      content: "✦";
      color: #d4af37;
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 12px;
      opacity: 0.7;
    }

    .footer-decoration::before { left: 10px; }
    .footer-decoration::after { right: 10px; }

    /* ✅ PRINT OVERRIDES */
    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }

      html, body {
        margin: 0 !important;
        padding: 0 !important;
        width: 105mm !important;
        height: 148mm !important;
        background: #fffef8 !important;
      }

      .print-hide {
        display: none !important;
      }

      .a6-container {
        margin: 0 !important;
        padding: 0 !important;
        width: 105mm !important;
        height: 148mm !important;
        box-shadow: none !important;
      }

      .watermark {
        display: block !important;
        opacity: 0.05 !important;
      }

      .islamic-pattern {
        background-size: cover !important;
      }

      .gold-border {
        background: linear-gradient(to right, #996515, #d4af37, #f0e68c, #d4af37, #996515) !important;
      }

      .mt-4 {
        margin-top: 0 !important;
      }
    }
  </style>



  <script>
    function printReceipt() {
      window.print();
    }
  </script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">

  <!-- Print button -->
  <div class="mb-4 print-hide">
    <button onclick="printReceipt()" class="bg-gradient-to-r from-[#996515] via-[#d4af37] to-[#996515] text-white font-medium py-2 px-4 rounded-md shadow">
      چاپ رسید
    </button>
  </div>

  <!-- A6 Container -->
  <div class="a6-container bg-[#fffef8] shadow-lg rounded-md overflow-hidden">
    <div class="gold-border p-[1.5px] rounded-md">
      <div class="relative w-full h-full bg-[#fffef8] islamic-pattern rounded-md">
        <div class="watermark">شاهین</div>

        <div class="relative h-full p-4 flex flex-col justify-start z-10">
          <!-- Header -->
          <div class="flex justify-between items-center mb-3">
            <div class="company-name text-sm font-bold text-[#996515] leading-tight">
              <div>شرکت صرافی و</div>
              <div>خدمات پولی شاهین</div>
              <div class="h-[2px] w-16 bg-gradient-to-left from-transparent via-[#d4af37] to-transparent mt-1"></div>
            </div>
            <div class="flex flex-col items-center">
              <svg viewBox="0 0 100 100" class="w-12 h-12 text-[#996515] logo-glow" xmlns="http://www.w3.org/2000/svg">
                <g fill="currentColor">
                  <path d="M50,15 L53.9,26.6 L66.1,26.6 L56.1,33.8 L60,45.4 L50,38.2 L40,45.4 L43.9,33.8 L33.9,26.6 L46.1,26.6 Z" />
                  <path d="M50,35 C45,35 40,40 35,50 C30,60 25,75 20,85 C35,75 45,70 50,70 C55,70 65,75 80,85 C75,75 70,60 65,50 C60,40 55,35 50,35 Z" />
                  <circle cx="50" cy="45" r="5" />
                </g>
              </svg>
              <div class="text-center mt-1 text-xs text-[#996515] font-semibold">SHAHIN</div>
              <div class="text-[9px] text-[#996515]">MONEY SERVICE PROVIDER LTD</div>
            </div>
          </div>

          <!-- Divider -->
          <div class="subtle-divider my-2"></div>

          <!-- Receipt Info -->
          <div class="text-sm space-y-1 font-[600]">
            <div class="flex justify-between"><span class="text-[#996515]">تاریخ:</span><span class="value">{{ \Carbon\Carbon::parse($hawla->date)->format('Y/m/d H:i') }}</span></div>
            <div class="flex justify-between"><span class="text-[#996515]">فرستنده:</span><span>{{ $hawla->sender_name }}</span></div>
            <div class="flex justify-between"><span class="text-[#996515]">گیرنده:</span><span>{{ $hawla->receiver_name }}</span></div>
            <div class="flex justify-between"><span class="text-[#996515]">شماره حواله:</span><span>{{ $hawla->uuid }}</span></div>
            <div class="flex justify-between font-bold"><span class="text-[#996515]">مبلغ حواله:</span><span class="value">{{ number_format($hawla->given_amount, 2) }} {{ $hawla->givenCurrency->code }}</span></div>
            <div class="flex justify-between font-bold"><span class="text-[#996515]">مبلغ دریافتی:</span><span class="value">{{ number_format($hawla->receiving_amount, 2) }} {{ $hawla->receivingCurrency->code }}</span></div>
            <div class="flex justify-between"><span class="text-[#996515]">نرخ تبادله:</span><span> {{ $hawla->exchange_rate ? number_format($hawla->exchange_rate, 4) : '-' }}</span></div>
            <div class="flex justify-between"><span class="text-[#996515]">آدرس:</span><span>{!! nl2br(e($hawla->receiver_address)) !!}</span></div>
            <div class="flex justify-between"><span class="text-[#996515]">تلفن:</span><span>{{ $hawla->receiver_phone_number }}</span></div>
          </div>

          <!-- Divider -->
          <div class="subtle-divider my-2"></div>

          <!-- Note (with address) -->
          <div class="text-xs bg-[#f0e68c] bg-opacity-10 p-2 rounded text-gray-700 leading-relaxed">
            <span class="text-[#996515] font-semibold">نوت:</span>
            <p class="mt-1">
              آدرس نمایندگی: سرای شهزاده، منزل دوم، کوچه تومن، دکان ب14<br>
              سند فقط جهت معلومات مشتری بوده و هیچگاه ارزش پول ندارد.
            </p>
          </div>

          <!-- Divider -->
          <div class="subtle-divider my-2"></div>

          <!-- Footer -->
          <div class="mt-4 text-center text-sm text-[#996515] font-[600] footer-decoration relative">
            <p>کفایت مارکیت منزل سوم دکان 330 - هزارشریفه</p>
            <p>پنج افغانستان</p>

            <div class="mt-2">
              <div class="inline-block bg-[#f0e68c] bg-opacity-20 px-3 py-1 rounded-full">
                <span class="text-[#996515] font-medium">0799483242</span>
                <span class="mx-2 text-[#d4af37]">-</span>
                <span class="text-[#996515] font-medium">0777895429</span>
              </div>
            </div>

            <div class="decorative-pattern mt-2"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
