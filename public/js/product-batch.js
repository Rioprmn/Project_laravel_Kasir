// public/js/product-batch.js
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const batchDeleteBtn = document.getElementById('btn-batch-delete');
    const countSpan = document.getElementById('count-selected');
    const form = document.getElementById('form-batch-delete');

    function toggleBatchButton() {
        const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        if (batchDeleteBtn) {
            batchDeleteBtn.style.display = checkedCount > 0 ? 'block' : 'none';
            countSpan.textContent = checkedCount;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleBatchButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleBatchButton);
    });

    if (batchDeleteBtn) {
        batchDeleteBtn.addEventListener('click', function() {
            if (confirm('Yakin ingin menghapus semua produk yang dipilih?')) {
                form.submit();
            }
        });
    }
});