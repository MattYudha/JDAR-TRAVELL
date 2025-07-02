<?php
session_start();
include_once("../app/_dbConnection.php");

// Validasi user_id dan package_id
$user_id = filter_var($_GET['user'] ?? '', FILTER_VALIDATE_INT);
$package_id = filter_var($_GET['package'] ?? '', FILTER_VALIDATE_INT);
$discounted_price = filter_var($_GET['discounted_price'] ?? '', FILTER_VALIDATE_FLOAT);
if (!$user_id || !$package_id || !isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !$discounted_price) {
    header("Location: ../registration.php");
    exit;
}

// Ambil data paket
$packageInstance = new Packages();
$res = $packageInstance->getPackage($package_id);
$package = mysqli_fetch_assoc($res);
if (!$package) {
    die("Paket tidak ditemukan.");
}

$price_per_night = $discounted_price;
$package_start = $package['package_start'];
$package_end = $package['package_end'];

// Format tanggal untuk ditampilkan
$package_start_formatted = date('d M Y', strtotime($package_start));
$package_end_formatted = date('d M Y', strtotime($package_end));

// Hitung rentang tanggal untuk validasi
$start_date = new DateTime($package_start);
$end_date = new DateTime($package_end);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - JDAR Travel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            margin-bottom: 8px;
        }
        .calendar-day {
            text-align: center;
            padding: 10px 5px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            background-color: #f8fafc;
        }
        .calendar-date {
            text-align: center;
            padding: 10px 0;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            font-weight: 500;
        }
        .calendar-date:hover:not(.disabled) {
            background-color: #f0f9ff;
            color: #0284c7;
        }
        .calendar-date.selected {
            background-color: #0284c7;
            color: white;
        }
        .calendar-date.selected::after {
            content: '';
            position: absolute;
            bottom: 3px;
            left: 50%;
            transform: translateX(-50%);
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background-color: white;
        }
        .calendar-date.disabled {
            color: #cbd5e1;
            cursor: not-allowed;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9, #0369a1);
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0284c7, #075985);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        }
        .quantity-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }
        .quantity-btn:hover {
            background-color: #e0f2fe;
            color: #0284c7;
        }
        .quantity-input {
            width: 48px;
            text-align: center;
            border: none;
            font-weight: 600;
            background: transparent;
        }
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
        }
        .progress-bar {
            height: 3px;
            background-color: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            margin: 15px 0;
        }
        .progress-step {
            height: 100%;
            background-color: #0284c7;
            width: 25%;
        }
        .nav-steps {
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            margin-bottom: 20px;
        }
        .nav-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 12px;
            color: #64748b;
            flex: 1;
            position: relative;
        }
        .nav-step.active {
            color: #0284c7;
            font-weight: 600;
        }
        .nav-step-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f1f5f9;
            border-radius: 50%;
            margin-bottom: 6px;
        }
        .nav-step.active .nav-step-icon {
            background-color: #0284c7;
            color: white;
        }
        @media (max-width: 640px) {
            .nav-step-text {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header with Progress Bar -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="../index.php" class="text-gray-700 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-800">Detail Pesanan</h1>
                </div>
                <div class="text-sm text-gray-500">Langkah 1 dari 3</div>
            </div>
            
            <div class="progress-bar">
                <div class="progress-step"></div>
            </div>
            
            <div class="nav-steps">
                <div class="nav-step active">
                    <div class="nav-step-icon">1</div>
                    <span class="nav-step-text">Detail</span>
                </div>
                <div class="nav-step">
                    <div class="nav-step-icon">2</div>
                    <span class="nav-step-text">Pemesanan</span>
                </div>
                <div class="nav-step">
                    <div class="nav-step-icon">3</div>
                    <span class="nav-step-text">Konfirmasi</span>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6 md:py-8">
        <div class="md:flex md:space-x-6 mb-8">
            <!-- Form Section -->
            <div class="md:w-2/3">
                <form action="./_order_form.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
                    <input type="hidden" name="price_per_night" value="<?php echo $price_per_night; ?>">
                    <input type="hidden" name="visit_date" id="visit_date" required>

                    <!-- Paket Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 card">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Paket Terpilih</h3>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">
                                Best Deal
                            </span>
                        </div>
                        
                        <div class="mt-4">
                            <h4 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($package['package_name']); ?></h4>
                            <div class="flex items-center mt-2 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Periode: <?php echo $package_start_formatted; ?> - <?php echo $package_end_formatted; ?>
                            </div>
                            
                            <div class="flex flex-wrap gap-3 mt-4">
                                <div class="flex items-center text-gray-600 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Konfirmasi Instan
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Tidak Bisa Refund
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Promo Berlaku
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Kunjungan Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 card">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tanggal Kunjungan</h3>
                        
                        <div class="calendar-header">
                            <button type="button" id="prev-month" class="flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Bulan Sebelumnya
                            </button>
                            <span id="month-year" class="font-semibold text-gray-800"></span>
                            <button type="button" id="next-month" class="flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                Bulan Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="calendar mt-4" id="calendar"></div>
                        
                        <div class="mt-4 flex items-center">
                            <div class="flex items-center mr-4">
                                <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Terpilih</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-gray-200 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Tidak Tersedia</span>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah Kamar/Malam Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 card">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Jumlah Kamar/Malam</h3>
                        
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg mb-4">
                            <div>
                                <p class="font-semibold text-gray-800">Harga per malam</p>
                                <p class="text-blue-600 font-bold text-lg">IDR <?php echo number_format($price_per_night, 0, ',', '.'); ?></p>
                            </div>
                            
                            <div class="flex items-center border border-gray-300 rounded-lg bg-white p-1">
                                <button type="button" id="decrease" class="quantity-btn text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" class="quantity-input" readonly>
                                <button type="button" id="increase" class="quantity-btn text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Harga per malam</span>
                                <span class="text-gray-800">IDR <?php echo number_format($price_per_night, 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Jumlah malam</span>
                                <span class="text-gray-800" id="quantity_display">1</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Pajak & Biaya</span>
                                <span class="text-gray-800">Termasuk</span>
                            </div>
                            <div class="border-t border-gray-300 my-2"></div>
                            <div class="flex justify-between items-center font-semibold">
                                <span class="text-gray-800">Total pembayaran</span>
                                <span class="text-blue-600 text-lg">IDR <span id="total_price"><?php echo number_format($price_per_night, 0, ',', '.'); ?></span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="w-full btn-primary text-white py-4 rounded-xl font-semibold shadow-md hover:shadow-lg">
                            Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Summary Section for Desktop -->
            <div class="hidden md:block md:w-1/3">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6 card">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h3>
                    
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <h4 class="font-semibold"><?php echo htmlspecialchars($package['package_name']); ?></h4>
                        <p class="text-sm text-gray-600 mt-1">
                            <span id="selected_date_display">Tanggal terpilih</span> • <span id="nights_display">1</span> malam
                        </p>
                    </div>
                    
                    <div class="space-y-3 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Harga per malam</span>
                            <span class="text-gray-800">IDR <?php echo number_format($price_per_night, 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Jumlah malam</span>
                            <span class="text-gray-800" id="summary_quantity_display">1</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pajak & Biaya</span>
                            <span class="text-gray-800">Termasuk</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center font-semibold mb-6">
                        <span class="text-gray-800">Total pembayaran</span>
                        <span class="text-blue-600 text-xl">IDR <span id="summary_total_price"><?php echo number_format($price_per_night, 0, ',', '.'); ?></span></span>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm">
                            <p class="font-medium text-gray-800">Dapatkan <span class="text-blue-600">480</span> poin</p>
                            <p class="text-gray-600">dari transaksi ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const pricePerNight = <?php echo $price_per_night; ?>;
        const quantityInput = document.getElementById('quantity');
        const quantityDisplay = document.getElementById('quantity_display');
        const totalPriceDisplay = document.getElementById('total_price');
        const visitDateInput = document.getElementById('visit_date');
        const calendar = document.getElementById('calendar');
        const monthYearDisplay = document.getElementById('month-year');
        const prevMonthBtn = document.getElementById('prev-month');
        const nextMonthBtn = document.getElementById('next-month');
        
        // Summary elements
        const summaryQuantityDisplay = document.getElementById('summary_quantity_display');
        const summaryTotalPrice = document.getElementById('summary_total_price');
        const selectedDateDisplay = document.getElementById('selected_date_display');
        const nightsDisplay = document.getElementById('nights_display');

        const packageStart = new Date('<?php echo $package_start; ?>');
        const packageEnd = new Date('<?php echo $package_end; ?>');
        let currentDate = new Date(packageStart);
        let selectedDate = null;

        // Format date for display
        function formatDateForDisplay(date) {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        // Update total price
        function updateTotalPrice() {
            const quantity = parseInt(quantityInput.value);
            const total = pricePerNight * quantity;
            
            // Update main displays
            quantityDisplay.textContent = quantity;
            totalPriceDisplay.textContent = total.toLocaleString('id-ID');
            
            // Update summary displays
            if (summaryQuantityDisplay) summaryQuantityDisplay.textContent = quantity;
            if (summaryTotalPrice) summaryTotalPrice.textContent = total.toLocaleString('id-ID');
            if (nightsDisplay) nightsDisplay.textContent = quantity;
        }

        // Increase/Decrease quantity
        document.getElementById('increase').addEventListener('click', () => {
            let quantity = parseInt(quantityInput.value);
            if (quantity < 10) {
                quantityInput.value = quantity + 1;
                updateTotalPrice();
            }
        });

        document.getElementById('decrease').addEventListener('click', () => {
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantityInput.value = quantity - 1;
                updateTotalPrice();
            }
        });

        // Generate calendar
        function generateCalendar() {
            calendar.innerHTML = `
                <div class="calendar-day">Min</div>
                <div class="calendar-day">Sen</div>
                <div class="calendar-day">Sel</div>
                <div class="calendar-day">Rab</div>
                <div class="calendar-day">Kam</div>
                <div class="calendar-day">Jum</div>
                <div class="calendar-day">Sab</div>
            `;

            const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const firstDayIndex = firstDayOfMonth.getDay();
            const daysInMonth = lastDayOfMonth.getDate();

            monthYearDisplay.textContent = firstDayOfMonth.toLocaleString('id-ID', { month: 'long', year: 'numeric' });

            // Add empty slots before the first day
            for (let i = 0; i < firstDayIndex; i++) {
                calendar.innerHTML += '<div></div>';
            }

            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
                const isDisabled = date < packageStart || date > packageEnd;
                const formattedDate = date.toISOString().split('T')[0];
                
                let dateClass = isDisabled ? 'calendar-date disabled' : 'calendar-date';
                if (selectedDate && formattedDate === selectedDate) {
                    dateClass += ' selected';
                }
                
                calendar.innerHTML += `<div class="${dateClass}" data-date="${formattedDate}">${day}</div>`;
            }

            // Add click events to dates
            document.querySelectorAll('.calendar-date:not(.disabled)').forEach(date => {
                date.addEventListener('click', () => {
                    document.querySelectorAll('.calendar-date').forEach(d => d.classList.remove('selected'));
                    date.classList.add('selected');
                    selectedDate = date.dataset.date;
                    visitDateInput.value = selectedDate;
                    
                    // Update summary display
                    if (selectedDateDisplay) {
                        const dateObj = new Date(selectedDate);
                        selectedDateDisplay.textContent = formatDateForDisplay(dateObj);
                    }
                });
            });

            // Update navigation buttons
            prevMonthBtn.disabled = currentDate.getFullYear() === packageStart.getFullYear() && currentDate.getMonth() === packageStart.getMonth();
            nextMonthBtn.disabled = currentDate.getFullYear() === packageEnd.getFullYear() && currentDate.getMonth() === packageEnd.getMonth();
        }

        // Navigation for previous and next month
        prevMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendar();
        });

        nextMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendar();
        });

        // Initialize calendar
        generateCalendar();

        // Set default date to package_start
        selectedDate = packageStart.toISOString().split('T')[0];
        visitDateInput.value = selectedDate;
        
        // Update selected date display in summary
        if (selectedDateDisplay) {
            selectedDateDisplay.textContent = formatDateForDisplay(packageStart);
        }
        
        // Select the default date in the calendar
        const defaultElement = document.querySelector(`.calendar-date[data-date="${selectedDate}"]`);
        if (defaultElement) {
            defaultElement.classList.add('selected');
        }
    </script>
</body>
</html>