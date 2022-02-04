window.unlockTimes = JSON.parse(document.getElementById('unlockTimes').dataset.unlocktimes);
console.log(unlockTimes);
window.faucets = JSON.parse(document.getElementById('faucets').dataset.faucets);
console.log(faucets)

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function faucetClaim(faucetId) {
    let faucetClaimBtn = document.getElementById('faucetClaimBtn' + faucetId);
    faucetClaimBtn.innerText = 'Calculating...';
    faucetClaimBtn.disabled = true;
    await sleep(2000);
    $.ajax({
        type: 'POST',
        url: '/faucet/claim',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'faucet': faucetId,
        },
        success: function (response) {
            let transaction_id = response['transfer']['transaction_id'];
            let linkTransactionId = document.getElementById('transactionId');
            let labelTransactionId = document.getElementById('labelTransactionId');
            labelTransactionId.classList.remove('d-none');
            linkTransactionId.innerText = transaction_id;
            linkTransactionId.href = 'https://wax.bloks.io/transaction/' + transaction_id;
            let nowDateTime = new Date();
            let timerMs = faucets[faucetId]['timer'] * 1000;
            unlockTimes[faucetId] = new Date(nowDateTime.getTime() + timerMs);
        }
    });
}

function formatTime(h,m,s){
    if (h < 10){
        h = '0' + h.toString();
    }
    if (m < 10){
        m = '0' + m.toString();
    }
    if (s < 10){
        s = '0' + s.toString();
    }
    return h + ':' + m + ':' + s;
}

setInterval(function () {
    Object.keys(faucets).forEach(faucet_id => {
        let faucetClaimBtn = document.getElementById('faucetClaimBtn' + faucet_id);

        let unlockDateTime = new Date(unlockTimes[faucet_id]);
        let nowDateTime = new Date();

        if (nowDateTime > unlockDateTime || !unlockTimes[faucet_id]) {
            faucetClaimBtn.innerText = 'Claim';
            faucetClaimBtn.disabled = false;
        } else {
            let differenceDate = new Date(unlockDateTime - nowDateTime);
            let timer = formatTime(differenceDate.getUTCHours(), differenceDate.getMinutes(), differenceDate.getSeconds());
            faucetClaimBtn.disabled = true;
            faucetClaimBtn.innerText = timer;
        }
    });
}, 990);
