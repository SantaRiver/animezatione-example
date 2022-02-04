let input = document.getElementById('amountStackInput');
input.addEventListener("focusout", function() {
    if (parseInt(input.value) < parseInt(input.min)){
        input.value = input.min;
    }
    if (parseInt(input.value) > parseInt(input.max)){
        input.value = input.max;
    }
});

async function stack() {
    let amountStack = document.getElementById('amountStackInput').value;
    let transaction_id = '';
    if (!pubKey.length || pubKey.indexOf('PUB_K1_') === 0) {
        transaction_id = await alcorTransferAni(amountStack, 'Staking');
    } else {
        transaction_id = await waxTransferAni(amountStack, 'Staking');
    }
    await $.ajax({
        type: 'POST',
        url: '/token_staking/stack',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'transaction_id': transaction_id
        },
        success: function (response) {
            location.reload();
        },
    });
}

function cancelStaking(active_stacking_id){
    let cancelBtn = document.getElementById('cancelBtn' + active_stacking_id);
    cancelBtn.disabled = true;
    $.ajax({
        type: 'POST',
        url: '/token_staking/cancel',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'active_stacking_id': active_stacking_id
        },
        success: function (response) {
            if (response['status'] === 'success'){
                location.reload();
            } else {
                alert(response['message']);
                location.reload();
            }
        },
    });
}

function claimStaking(active_stacking_id){
    let claimBtn = document.getElementById('claimBtn' + active_stacking_id);
    claimBtn.disabled = true;
    $.ajax({
        type: 'POST',
        url: '/token_staking/claim',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'active_stacking_id': active_stacking_id
        },
        success: function (response) {
            if (response['status'] === 'success'){
                location.reload();
            } else {
                alert(response['message']);
                location.reload();
            }
        },
    });
}
