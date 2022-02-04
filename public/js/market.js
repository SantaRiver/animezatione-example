async function buy() {
    let asset = collection[previousChosenCard];

    let transaction_id = '';
    let priceANI = asset['price_ani'] + ' ANI';

    if (!pubKey.length) {
        const action = {
            account: 'anionereward',
            name: 'transfer',
            authorization: [{
                actor: userAccount,
                permission: 'active',
            }],
            data: {
                from: userAccount,
                to: 'anionereward',
                quantity: priceANI,
                memo: 'Buy a card',
            },
        }
        await link.transact({action}).then(({signer, transaction}) => {
            transaction_id = `${transaction.id}`;
        })
    } else {
        try {
            let result = await wax.api.transact({
                actions: [{
                    account: 'anionereward',
                    name: 'transfer',
                    authorization: [{
                        actor: userAccount,
                        permission: 'active',
                    }],
                    data: {
                        from: userAccount,
                        to: 'anionereward',
                        quantity: priceANI,
                        memo: 'Buy a card',
                    },
                }]
            }, {
                blocksBehind: 3,
                expireSeconds: 30
            });
            transaction_id = result['transaction_id']
        } catch (e) {
            console.log(e);
        }
    }
    await $.ajax({
        type: 'POST',
        url: '/buy/card',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'template_id': asset['template_id'],
            'transaction_id': transaction_id
        },
        success: function (response) {
            console.log(response)
            alert('Done! Transaction ID: ' + response['transaction_id']);
            location.reload();
        },
    });
}
