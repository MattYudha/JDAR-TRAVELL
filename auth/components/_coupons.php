<?php
// Tidak perlu session_start() karena sudah dipanggil di index.php

// Include file _dbConnection.php dengan path yang benar
include_once("C:/xampp/htdocs/triptrip-master/app/_dbConnection.php");

// Inisialisasi kelas Coupons dan ambil kupon aktif
$couponInstance = new Coupons();
$coupons = $couponInstance->getActiveCoupons();
?>

<div class="section-header">
    <h2>Diskon Spesial untuk Anda</h2>
    <p>Gunakan kode promo berikut untuk mendapatkan harga terbaik pada reservasi Anda</p>
</div>
<div class="coupon-container">
    <?php if (empty($coupons)): ?>
        <p class="text-center">Tidak ada kupon aktif saat ini.</p>
    <?php else: ?>
        <?php foreach ($coupons as $coupon): ?>
            <div class="coupon-card <?php echo $coupon['is_popular'] ? 'featured' : ($coupon['is_saving'] ? 'flight' : 'activity'); ?>">
                <div class="coupon-tag <?php echo $coupon['is_popular'] ? 'popular' : ($coupon['is_saving'] ? '' : 'ending-soon'); ?>">
                    <?php echo $coupon['is_popular'] ? 'POPULAR' : ($coupon['is_saving'] ? 'HEMAT' : 'SEGERA BERAKHIR'); ?>
                </div>
                <div class="coupon-top">
                    <div class="coupon-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?php if ($coupon['is_popular']): ?>
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            <?php elseif ($coupon['is_saving']): ?>
                                <path d="M22 2L11 13"></path>
                                <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
                            <?php else: ?>
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            <?php endif; ?>
                        </svg>
                    </div>
                    <div class="coupon-info">
                        <h3><?php echo htmlspecialchars($coupon['coupon_desc']); ?></h3>
                        <p>Nikmati diskon spesial untuk reservasi Anda</p>
                    </div>
                    <div class="coupon-brand">
                        <img src="auth/assets/garuda.png" alt="Coupon Icon">
                    </div>
                </div>
                <div class="coupon-details">
                    <div class="coupon-meta">
                        <div class="coupon-discount"><?php echo htmlspecialchars($coupon['discount_percentage']); ?>%</div>
                        <div class="coupon-validity">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Valid hingga <?php echo date('d M Y', strtotime($coupon['valid_until'])); ?>
                        </div>
                    </div>
                </div>
                <div class="coupon-divider">
                    <div class="right-circle"></div>
                </div>
                <div class="coupon-action">
                    <div class="coupon-code"><?php echo htmlspecialchars($coupon['coupon_code']); ?></div>
                    <button onclick="copyToClipboard('<?php echo htmlspecialchars($coupon['coupon_code']); ?>')" class="copy-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        Salin Kode
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Kode kupon ' + text + ' telah disalin!');
    }).catch(err => {
        console.error('Gagal menyalin kode kupon: ', err);
    });
}
</script>