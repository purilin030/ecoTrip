// 1. 显示用户详情 (保持不变)
function showUserDetails(name, email, uid) {
    Swal.fire({
        title: 'User Details',
        html: `
            <div class="text-left">
                <p><strong>Name:</strong> ${name}</p>
                <p><strong>User ID:</strong> ${uid}</p>
                <p><strong>Email:</strong> <a href="mailto:${email}" class="text-blue-600 underline">${email}</a></p>
            </div>
        `,
        icon: 'info'
    });
}

// 2. 新的高级发货逻辑
async function fulfillOrder(recordId, rewardName) {
    
    // 第一步：问管理员要做什么？
    const { value: actionType } = await Swal.fire({
        title: 'Fulfill Order',
        text: `How do you want to fulfill "${rewardName}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#3b82f6',
        confirmButtonText: '📸 Upload Photo Proof', 
        cancelButtonText: '🎟️ Send Voucher Code',   
        showCloseButton: true
    });

    if (!actionType && !Swal.getDismissReason()) return; 

    // 为了简单，我们改用 Input Select 方式
    const { value: selectedMode } = await Swal.fire({
        title: 'Select Fulfillment Method',
        input: 'radio',
        inputOptions: {
            'photo': '📸 Physical Proof (Photo)',
            'code': '🎟️ Virtual Voucher (Code)',
            'simple': '✅ Just Mark as Sent'
        },
        inputValue: 'photo',
        confirmButtonText: 'Next >'
    });

    if (!selectedMode) return;

    // 第二步：准备数据
    let formData = new FormData();
    formData.append('record_id', recordId);
    formData.append('update_status', '1'); 

    if (selectedMode === 'photo') {
        const { value: file } = await Swal.fire({
            title: 'Upload Proof Photo',
            text: 'Upload a photo of the tree/trash bag',
            input: 'file',
            inputAttributes: { 'accept': 'image/*' }
        });
        if (!file) return;
        formData.append('proof_photo', file);

    } else if (selectedMode === 'code') {
        const { value: code } = await Swal.fire({
            title: 'Enter Voucher Code',
            input: 'text',
            inputPlaceholder: 'e.g. ECO-2025-XMAS'
        });
        if (!code) return;
        formData.append('admin_note', code); 
    
    }

    // 第三步：发送给后端 PHP
    Swal.fire({ title: 'Processing...', didOpen: () => Swal.showLoading() });

    fetch('Redemption_List.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if(response.ok) {
            Swal.fire('Success!', 'Order has been fulfilled.', 'success')
            .then(() => location.reload()); 
        } else {
            Swal.fire('Error', 'Something went wrong.', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Network error.', 'error');
    });
}
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelector("tbody").rows;

    for (let i = 0; i < rows.length; i++) {
        // 获取第二列 (User Name) 的文本
        let nameCol = rows[i].cells[1].textContent; 
        if (nameCol.toUpperCase().indexOf(filter) > -1) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
});