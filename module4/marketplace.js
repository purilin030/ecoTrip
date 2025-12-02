function redeemItem(rewardID, rewardName){
    // 替换 confirm
    Swal.fire({
        title: 'Redeem this reward?',
        text: `Are you sure you want to spend points on ${rewardName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981', // Emerald-500
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, redeem it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // 用户点了 Yes，发送请求
            const formData = new FormData();
            formData.append('reward_id', rewardID);

            fetch('Process-redeem.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    // 替换成功 alert
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        location.reload(); 
                    });
                } else {
                    // 替换失败 alert
                    Swal.fire('Oops...', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
            });
        }
    });
}