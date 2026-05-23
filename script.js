/**
 * Advanced Billing Management System - Main JavaScript
 * AJAX and Interactive Features
 */

// Global Variables
let billItems = [];
let srNo = 1;
const GST_RATE = 5;

/**
 * Document Ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

/**
 * Search functionality
 */
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

/**
 * Delete confirmation with SweetAlert
 */
function confirmDelete(url, message = 'Do you want to delete this item?') {
    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

/**
 * Show loading spinner
 */
function showLoading() {
    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Hide loading spinner
 */
function hideLoading() {
    Swal.close();
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return '₹' + parseFloat(amount).toFixed(2);
}

/**
 * Calculate GST
 */
function calculateGST(amount) {
    return (amount * GST_RATE) / 100;
}

/**
 * Add item to bill (AJAX)
 */
function addItemToBill(productId) {
    const qtyInput = document.getElementById('qty_' + productId);
    const qty = qtyInput ? parseInt(qtyInput.value) : 1;
    
    if (qty < 1) {
        Swal.fire('Error', 'Quantity must be at least 1', 'error');
        return;
    }
    
    // Get product details from data attributes
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    const productName = productCard.dataset.productName;
    const productPrice = parseFloat(productCard.dataset.productPrice);
    
    // Check if product already exists in bill
    const existingItem = billItems.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += qty;
        existingItem.gst = calculateGST(existingItem.price * existingItem.quantity);
        existingItem.total = (existingItem.price * existingItem.quantity) + existingItem.gst;
    } else {
        const gst = calculateGST(productPrice * qty);
        const total = (productPrice * qty) + gst;
        
        billItems.push({
            sr: srNo++,
            id: productId,
            name: productName,
            price: productPrice,
            quantity: qty,
            gst: gst,
            total: total
        });
    }
    
    renderBillTable();
    
    // Reset quantity input
    if (qtyInput) qtyInput.value = 1;
    
    // Show success message
    Swal.fire({
        icon: 'success',
        title: 'Item Added',
        text: productName + ' added to bill',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    });
}

/**
 * Update quantity in bill
 */
function updateQuantity(index, change) {
    billItems[index].quantity += change;
    
    if (billItems[index].quantity < 1) {
        billItems[index].quantity = 1;
    }
    
    const price = billItems[index].price;
    const qty = billItems[index].quantity;
    billItems[index].gst = calculateGST(price * qty);
    billItems[index].total = (price * qty) + billItems[index].gst;
    
    renderBillTable();
}

/**
 * Remove item from bill
 */
function removeItem(index) {
    Swal.fire({
        title: 'Remove Item?',
        text: 'Do you want to remove this item from the bill?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            billItems.splice(index, 1);
            renderBillTable();
            Swal.fire('Removed!', 'Item has been removed.', 'success');
        }
    });
}

/**
 * Render bill table
 */
function renderBillTable() {
    const tableBody = document.getElementById('billTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    let totalAmt = 0, totalGst = 0, finalAmt = 0;
    
    billItems.forEach((item, index) => {
        totalAmt += item.price * item.quantity;
        totalGst += item.gst;
        finalAmt += item.total;
        
        const row = `
            <tr>
                <td>${item.sr}</td>
                <td>${item.name}</td>
                <td>
                    <button type="button" class="qty-btn" onclick="updateQuantity(${index}, -1)">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="mx-2 fw-bold">${item.quantity}</span>
                    <button type="button" class="qty-btn" onclick="updateQuantity(${index}, 1)">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>
                <td>${formatCurrency(item.price)}</td>
                <td>${formatCurrency(item.gst)}</td>
                <td>${formatCurrency(item.total)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
    
    // Update totals
    document.getElementById('subtotal').innerText = formatCurrency(totalAmt);
    document.getElementById('total_gst').innerText = formatCurrency(totalGst);
    
    // Apply discount if any
    const discountInput = document.getElementById('discount_amount');
    const discount = discountInput ? parseFloat(discountInput.value) || 0 : 0;
    const finalAmount = finalAmt - discount;
    
    document.getElementById('final_amount').innerText = formatCurrency(finalAmount);
    
    // Update hidden input
    document.getElementById('bill_items').value = JSON.stringify(billItems);
    document.getElementById('subtotal_hidden').value = totalAmt.toFixed(2);
    document.getElementById('gst_hidden').value = totalGst.toFixed(2);
    document.getElementById('discount_hidden').value = discount.toFixed(2);
    document.getElementById('final_hidden').value = finalAmount.toFixed(2);
}

/**
 * Apply discount
 */
function applyDiscount() {
    renderBillTable();
}

/**
 * Save bill with AJAX
 */
function saveBill() {
    const customerName = document.getElementById('customer_name').value;
    const customerPhone = document.getElementById('customer_phone').value;
    
    if (!customerName || customerName.trim() === '') {
        Swal.fire('Error', 'Please enter customer name', 'error');
        return;
    }
    
    if (billItems.length === 0) {
        Swal.fire('Error', 'Please add at least one item to the bill', 'error');
        return;
    }
    
    // Show loading
    showLoading();
    
    // Submit form
    document.getElementById('billForm').submit();
}

/**
 * Print bill
 */
function printBill() {
    window.print();
}

/**
 * Export to PDF (simplified - you can use jsPDF for advanced features)
 */
function exportPDF() {
    Swal.fire('Info', 'PDF export feature coming soon!', 'info');
}

/**
 * Barcode scanner simulation
 */
function scanBarcode() {
    Swal.fire({
        title: 'Enter Barcode',
        input: 'text',
        inputPlaceholder: 'Scan or enter barcode',
        showCancelButton: true,
        confirmButtonText: 'Search',
        showLoaderOnConfirm: true,
        preConfirm: (barcode) => {
            // AJAX call to search product by barcode
            return fetch(`search_product.php?barcode=${barcode}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message);
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Error: ${error}`);
                });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            addItemToBill(result.value.product_id);
        }
    });
}

/**
 * Image preview before upload
 */
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Filter table by date range
 */
function filterByDate() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (!startDate || !endDate) {
        Swal.fire('Error', 'Please select both start and end dates', 'error');
        return;
    }
    
    // Reload page with date parameters
    window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
}

/**
 * Clear filters
 */
function clearFilters() {
    window.location.href = window.location.pathname;
}