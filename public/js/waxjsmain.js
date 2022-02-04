const userAccount = getMeta('userAccount');
const pubKey = getMeta('pubKey');
const secureKey = getMeta('secureKey');
const wax = new waxjs.WaxJS('https://chain.wax.io', userAccount, [pubKey, secureKey], false);

function getMeta(metaName) {
    const metas = document.getElementsByTagName('meta');

    for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') === metaName) {
            return metas[i].getAttribute('content');
        }
    }

    return null;
}

function queryGenerator(params) {
    let query = "";
    for (const [key, value] of Object.entries(params)) {
        query += `${key}=${value}&`;
    }
    return query;
}

//automatically check for credentials
autoLogin();

//checks if autologin is available
async function autoLogin() {
    let isAutoLoginAvailable = await wax.isAutoLoginAvailable();
    if (isAutoLoginAvailable) {
        let userAccount = wax.userAccount;
        let pubKeys = wax.pubKeys;
    } else {
        //document.getElementById('response').insertAdjacentHTML('beforeend', 'To view this page, please log in');
    }
}

//normal login. Triggers a popup for non-whitelisted dapps
async function login() {
    try {
        //if autologged in, this simply returns the userAccount w/no popup
        let userAccount = await wax.login();
        let pubKeys = wax.pubKeys;
        console.log(userAccount);
        let str = 'Account: ' + userAccount + '<br/>Active: ' + pubKeys[0] + '<br/>Owner: ' + pubKeys[1]
        sendLoginRequest(userAccount, pubKeys[0], pubKeys[1], 'active');
    } catch (e) {
        console.log(e)
    }
}

function claim() {
    $.ajax({
        type: 'GET',
        url: '/staking/claim',
        success: function (response) {
            location.reload();
        }
    });
}


function sendLoginRequest(userAccount, pubKey, secureKey, permission) {
    $.ajax({
        type: 'POST',
        url: '/auth',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'userAccount': userAccount,
            'pubKey': pubKey,
            'secureKey': secureKey,
            'permission': permission,
        },
        success: function (response) {
            console.log(response);
            location.reload();
        }
    });
}

async function openPack(asset_id, template_id, pack_id) {
    if (!wax.api) {
        await login();
        console.log('login first')
    }
    if (!asset_id || !template_id || !pack_id) {
        console.log('asset_id || template_id || pack_id is empty')
    }
    var loadOpenPackModal = new bootstrap.Modal(document.getElementById('loadOpenPackModal'), {
        keyboard: false,
    })
    loadOpenPackModal.show();
    try {
        const result = await wax.api.transact({
            actions: [{
                account: 'atomicassets',
                name: 'transfer',
                authorization: [{
                    actor: wax.userAccount,
                    permission: 'active',
                }],
                data: {
                    asset_ids: [asset_id],
                    from: wax.userAccount,
                    to: 'anionereward',
                    memo: 'open pack'
                },
            }]
        }, {
            blocksBehind: 3,
            expireSeconds: 30
        });
        console.log(result)

        $.ajax({
            type: 'POST',
            url: '/unpacking/unpack',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'pack_asset_id': asset_id,
                'template_id': template_id,
                'pack_id': pack_id,
                'transaction_id': result['transaction_id'],
            },
            success: function (response) {
                let packRewards = document.getElementById('packRewards');
                let htmlPackRewards = '';
                response['reward'].forEach((card) => {
                    let htmlPackReward =
                        '<div class="col-3 position-relative p-3 d-flex justify-content-center align-items-center">' +
                        '<img class="w-100" src="/storage/app/public/cards/' + card['preview'] + '" alt="' + card['name'] + '">' +
                        '</div>'
                    htmlPackRewards += htmlPackReward
                })
                packRewards.innerHTML = htmlPackRewards;
                loadOpenPackModal.hide();
                console.log(response)
            }
        });
    } catch (e) {
        console.log(e);
    }
}

async function burn(asset_id, template_id) {
    var loadOpenPackModal = new bootstrap.Modal(document.getElementById('loadOpenPackModal'), {
        keyboard: false,
    })
    loadOpenPackModal.show();
    try {
        const result = await wax.api.transact({
            actions: [{
                account: 'atomicassets',
                name: 'burnasset',
                authorization: [{
                    actor: wax.userAccount,
                    permission: 'active',
                }],
                data: {
                    asset_id: asset_id,
                    asset_owner: wax.userAccount,
                },
            }]
        }, {
            blocksBehind: 3,
            expireSeconds: 30
        });
        console.log(result);
        $.ajax({
            type: 'POST',
            url: '/burn',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'asset_id': asset_id,
                'template_id': template_id,
                'transaction_id': result['transaction_id']
            },
            success: function (response) {
                loadOpenPackModal.hide();
                location.reload();
            },
            fail: function () {
                loadOpenPackModal.hide();
                location.reload();
            }
        });

    } catch (e) {
        console.log(result)
    }
}

async function waxTransferAni(amount, memo = '') {
    let transaction_id = '';
    let priceANI = amount + ' ANI';
    let result = await wax.api.transact({
        actions: [{
            account: 'anionereward',
            name: 'transfer', authorization: [{
                actor: userAccount,
                permission: 'active',
            }],
            data: {
                from: userAccount,
                to: 'anionereward',
                quantity: priceANI,
                memo: memo,
            },
        }]
    }, {
        blocksBehind: 3,
        expireSeconds: 30
    });
    transaction_id = result['transaction_id']
    return transaction_id;
}




