const transport = new AnchorLinkBrowserTransport()
const link = new AnchorLink({
    transport,
    chains: [
        {
            chainId: '1064487b3cd1a897ce03ae5b6a865651747e2e152090f99c1d19d44e01aea5a4',
            nodeUrl: 'https://wax.greymass.com',
        }
    ],
})

async function anchorLogin(){
    const identity = await link.login('mydapp')
    const {session} = identity
    let login = `${session.auth}`;
    let publicKey = `${session.publicKey}`;
    login = login.split('@')[0];
    let permission = `${session.auth.permission}`;
    sendLoginRequest(login, publicKey, publicKey, permission);
}

/*Для анчора*/
async function alcorTransferAni(amount, memo = ''){

    let transaction_id = '';
    let priceANI = amount + ' ANI';
    const action = {
        account: 'anionereward',
        name: 'transfer',
        authorization: [{
            actor: userAccount,
            permission: $('meta[name="permission"]').attr('content'),
        }],
        data: {
            from: userAccount,
            to: 'anionereward',
            quantity: priceANI,
            memo: memo,
        },
    }
    await link.transact({action}).then(({signer, transaction}) => {
        transaction_id = `${transaction.id}`;
    })
    return transaction_id;
}
