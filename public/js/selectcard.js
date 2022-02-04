function selectCard(id) {
    let card = window.collection[id];
    document.getElementById('cardImg' + window.previousChosenCard).classList.remove("chosen-card");
    document.getElementById('cardImg' + id).classList.add("chosen-card");
    window.previousChosenCard = id

    let cardImage = document.getElementById('cardImage')
    let cardBurn = document.getElementById('cardBurn')
    let cardName = document.getElementById('cardName');
    let cardDescription = document.getElementById('cardDescription');
    let cardCount = document.getElementById('cardCount');
    let cardAsset = document.getElementById('cardAsset');
    let cardRarity = document.getElementById('cardRarity');
    let cardBenefit = document.getElementById('cardBenefit');
    let cardAvailable = document.getElementById('cardAvailable');
    let cardPrice = document.getElementById('cardPrice');
    let cardBurning = document.getElementById('cardBurning');
    let cardAssetID = document.getElementById('cardAssetID');
    let cardMint = document.getElementById('cardMint');

    if (cardName) {
        cardName.innerText = card.name;
    }
    if (cardDescription) {
        cardDescription.innerText = card.description;
    }
    if (cardImage) {
        cardImage.src = '/storage/app/public/cards/' + card.path
    }
    if (cardCount) {
        cardCount.innerText = card.count;
    }
    if (cardAsset) {
        cardAsset.innerText = card.assets;
    }
    if (cardRarity) {
        cardRarity.innerText = card.rarity ?? 'empty';
    }
    if (cardBenefit) {
        cardBenefit.innerText = card.benefit ?? 'empty';
    }
    if (cardBurning) {
        cardBurning.innerText = card.burning ?? 'empty';
    }
    if (cardAssetID) {
        cardAssetID.innerText = card.asset_id;
    }
    if (cardAvailable) {
        let availabilityCards = window.availabilityCards[card.template_id]
        cardAvailable.innerText = availabilityCards.length;
    }
    if (cardPrice) {
        cardPrice.innerText = card.price_ani;
    }
    if (cardMint) {
        cardMint.innerText = card.mint ?? 'empty';
    }

    if (cardBurn){
        if (card.assets !== 'token') {
            cardBurn.classList.remove('d-block');
            cardBurn.classList.add('d-none');
        } else {
            cardBurn.classList.remove('d-none');
            cardBurn.classList.add('d-block');
        }
    }

    /**
     * for unpacking page
     */
    let packRewards = document.getElementById('packRewards');
    let packImage = document.getElementById('packImage');
    if (packRewards) {
        let htmlPackRewards = '';
        let htmlPackImage =
            '<img class="main-card w-100" id="cardImage" src="/storage/app/public/cards/QmcgDwV6yBgq8HZwBaqgcqNwhsdwqe8WJnhBHjMzTWhtZ9.png" alt="Viola Card">\n' +
            '<button class="pack-name h3 mt-0 position-absolute border-2 py-4 px-3 rounded-circle border" id="clickMeBtn" style="border-color: yellow!important;">click me</button>';
        for (let reward of Object.entries(window.collection[id]['pack_reward'])) {
            let chance = Number((window.collection[id]['pack_reward_chance'][reward[1]['id']]).toFixed(2)) + '%';
            if (chance === '0%') {
                chance = '100%'
            }
            let htmlPackReward =
                '<div class="col-3 position-relative p-3 d-flex justify-content-center align-items-center">' +
                '<img class="w-100" src="/storage/app/public/cards/' + reward[1]['preview'] + '" alt="' + reward[1]['name'] + '">' +
                '<p class="pack-name h3 mt-0 position-absolute">' + chance + '</p>' +
                '</div>'
            htmlPackRewards += htmlPackReward
        }
        packImage.innerHTML = htmlPackImage;
        packRewards.innerHTML = htmlPackRewards;
        let clickMeBtn = document.getElementById('clickMeBtn');
        clickMeBtn.addEventListener('click', event => {
            openPack(card.asset_id, card.template_id, card.card_id).then(r => function (){
                document.getElementById('cardImg' + id).remove()
            });
        });
    }

}
