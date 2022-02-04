function saveNFT(nft){
    $.ajax({
        type: 'POST',
        url: '/dashboard/nft',
        data: nft,
        success: function (response) {
            console.log(response)
        }
    });
    console.log(nft);
    return false;
}
