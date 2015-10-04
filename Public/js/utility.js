function enableReviewForm(id) {
    var display = document.getElementById(id).style.display;

    if (display === 'none') {
        document.getElementById(id).style.display = 'block';
    } else {
        document.getElementById(id).style.display = 'none';
    }
}

function sendCart(url, path) {
    $.ajax({
        method: "GET",
        url: url,
        data: {}
    }).done(
        function () {
            window.location.replace(path + 'cart')
        }
    );
}

function sendAjax(name, url) {
    $.ajax({
        method: "GET",
        url: url,
        data: {}
    }).done(
        function (msg) {
            document.getElementById("#").style.display = 'block';
            document.getElementById("#").innerHTML = '"' + name + '" added to cart!';
            document.getElementById("cart-products-count").innerHTML = parseInt(document.getElementById("cart-products-count").innerHTML) + 1;
        }
    );
}