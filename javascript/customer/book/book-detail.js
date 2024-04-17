let mode = null;

$(document).ready(function ()
{
    setInterval(function ()
    {
        //checkInStock();
    }, 10000);

    $('#addToCartForm').submit(function (e)
    {
        e.preventDefault();
    });

    $('#ebook').on('change', function (e)
    {
        if (e.target.checked)
        {
            mode = 1;
            $('#addToCartBtn').prop('disabled', false);
        }
    });

    $('#hardcover').on('change', function (e)
    {
        if (e.target.checked)
        {
            mode = 2;
            $('#addToCartBtn').prop('disabled', false);
        }
    });
});

function adjustAmount(isIncrease)
{
    if (isIncrease)
        $(`#book_ammount`).val(parseInt($(`#book_ammount`).val()) + 1);
    else
        $(`#book_ammount`).val(parseInt($(`#book_ammount`).val()) - 1);

    checkAmmount();
}

function checkAmmount()
{
    const amount = parseInt($(`#book_ammount`).val());
    const inStock = parseInt($(`#in_stock`).text());

    clearCustomValidity($(`#book_ammount`).get(0));

    if (amount < 0)
    {
        reportCustomValidity($(`#book_ammount`).get(0), "Book amount can not be negative!");
        return;
    } else if (amount === 0)
    {
        reportCustomValidity($(`#book_ammount`).get(0), "Book amount can not be zero!");
        return;
    }
    else if (amount > inStock)
    {
        reportCustomValidity($(`#book_ammount`).get(0), "Book amount exceeds in stock amount!");
        return;
    }
}