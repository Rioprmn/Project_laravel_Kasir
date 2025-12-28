/* public/js/kasir.js */
document.addEventListener('DOMContentLoaded', function() {
    const inputBayar = document.getElementById('input_bayar');
    const displayKembalian = document.getElementById('display_kembalian');
    const btnCheckout = document.getElementById('btn-checkout');
    const totalInput = document.getElementById('total_belanja');
    const hiddenBayar = document.getElementById('hidden_bayar');
    const searchInput = document.getElementById('searchProduct'); // Tambahkan ini

    if (inputBayar && totalInput && btnCheckout) {
        inputBayar.addEventListener('input', function() {
            const totalBelanja = parseFloat(totalInput.value) || 0;
            const bayar = parseFloat(this.value) || 0;
            const kembali = bayar - totalBelanja;

            if (hiddenBayar) {
                hiddenBayar.value = bayar;
            }

            if (kembali >= 0 && bayar > 0) {
                displayKembalian.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(kembali);
                displayKembalian.style.color = '#16a34a';
                
                btnCheckout.disabled = false;
                btnCheckout.style.background = '#1abc9c';
                btnCheckout.style.cursor = 'pointer';
                btnCheckout.innerHTML = '💾 KONFIRMASI & CETAK NOTA';
            } else {
                displayKembalian.innerText = 'Kurang: Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(kembali));
                displayKembalian.style.color = '#ef4444';
                
                btnCheckout.disabled = true;
                btnCheckout.style.background = '#94a3b8';
                btnCheckout.style.cursor = 'not-allowed';
                btnCheckout.innerHTML = '⚠️ UANG BELUM CUKUP';
            }
        });
    }

    // Tambahkan Logika Search agar aman & memudahkan kasir
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let products = document.querySelectorAll('.card-product');
            
            products.forEach(product => {
                let name = product.querySelector('h4').innerText.toLowerCase();
                let code = product.querySelector('p').innerText.toLowerCase();
                if (name.includes(filter) || code.includes(filter)) {
                    product.style.display = "";
                } else {
                    product.style.display = "none";
                }
            });
        });
    }
});