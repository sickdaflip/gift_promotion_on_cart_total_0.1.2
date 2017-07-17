function showOptions(id) {
    var click_ev = document.createEvent("MouseEvents");
    click_ev.initEvent("click", true, true);
    document.getElementById('fancybox_' + id).dispatchEvent(click_ev);
}


function selectGift(productId, productURL, promotionId, reloadUrl) {
    $('loadingmask').style.display = 'block';
    new Ajax.Request(reloadUrl, {
        method: 'post',
        parameters: {
            productId: productId,
            promotionId: promotionId,
            cartPage: 1
        },
        onComplete: function(transport) {
            $('loadingmask').style.display = 'none';
            if (transport.responseText != null) {
                setLocation(productURL);
            } else {
                alert("Gift could not be applied !! Please try again after some time !!");
            }
        }
    });
}

function setOnepageData() {
   
    $('fancybox-overlay').style.display = 'none';
    $('fancybox-wrap').style.display = 'none';    
}

function selectGiftCheckout(productId, productURL, promotionId, reloadUrl, progressUrl)
{
    $('loadingmask').style.display = 'block';
    new Ajax.Request(reloadUrl, {
        method: 'post',
        async: false,
        parameters: {
            productId: productId,
            promotionId: promotionId,
            checkoutPage: 1,
            redirectUrl: productURL
        },
        onComplete: function(transport) {
            $('loadingmask').style.display = 'none';
            if (transport.responseText != null) {
                var result = transport.responseText.evalJSON(true);
                var redirecturl = result.redirectUrl;
                setLocation(redirecturl);
            } else {
                alert("Gift could not be applied !! Please try again after some time !!");
            }
        }
    });
}
