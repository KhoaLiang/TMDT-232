let categorySelected = '', authorSelected = '', publisherSelected = '';
let displayPanel = null;

$(document).ready(function ()
{
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    categorySelected = urlParams.get('category') || '';
    publisherSelected = urlParams.get('publisher') || '';

    $('#categorySearch,#categorySearchModal').val(categorySelected);
    $('#publisherSearch,#publisherSearchModal').val(publisherSelected);

    fetchCategoryList();
    fetchAuthorList();
    fetchPublisherList();

    checkScreenWidth();

    $(window).resize(function ()
    {
        checkScreenWidth();
    });

    $('#search_form').submit(function (e)
    {
        e.preventDefault();
        fetchBook();
    });
});

function checkScreenWidth()
{
    if (window.innerWidth < 1200)
    {
        displayPanel = false;
    } else
    {
        displayPanel = true;
    }

}

function fetchCategoryList()
{
    const search = displayPanel ? encodeData($('#categorySearch').val()) : encodeData($('#categorySearchModal').val());

    if (displayPanel)
        $('#categorySearchModal').val($('#categorySearch').val());
    else
        $('#categorySearch').val($('#categorySearchModal').val());

    $.ajax({
        url: '/ajax_service/customer/book/get_category.php',
        method: 'GET',
        data: { search: search },
        dataType: 'json',
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            if (data.query_result)
            {
                let temp = ``;
                $('#categoryList,#categoryListModal').empty();
                for (let i = 0; i < data.query_result.length; i++)
                {
                    temp += `<p onclick="chooseCategory(event)" name='category' class='pointer ${ categorySelected === data.query_result[i].name ? 'itemChoose' : '' }'>${ data.query_result[i].name }</p>`
                }
                $('#categoryList,#categoryListModal').append(temp);
            }
        },
        error: function (err)
        {
            console.error(err);
            if (err.status >= 500)
            {
                $('#errorModal').modal('show');
                $('#error_message').text('Server encountered error!');
            } else
            {
                $('#errorModal').modal('show');
                $('#error_message').text(err.responseJSON.error);
            }
        }
    })
}

function chooseCategory(e)
{
    $('p[name="category"]').removeClass('itemChoose');
    $(`p[name="category"]:contains("${ e.target.innerText }")`).addClass('itemChoose');
    categorySelected = e.target.innerText;
}

function fetchAuthorList()
{
    const search = displayPanel ? encodeData($('#authorSearch').val()) : encodeData($('#authorSearchModal').val());

    if (displayPanel)
        $('#authorSearchModal').val($('#authorSearch').val());
    else
        $('#authorSearch').val($('#authorSearchModal').val());

    $.ajax({
        url: '/ajax_service/customer/book/get_author.php',
        method: 'GET',
        data: { search: search },
        dataType: 'json',
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            if (data.query_result)
            {
                let temp = ``;
                $('#authorList,#authorListModal').empty();
                for (let i = 0; i < data.query_result.length; i++)
                {
                    temp += `<p onclick="chooseAuthor(event)" name='author' class='pointer ${ authorSelected === data.query_result[i].name ? 'itemChoose' : '' }'>${ data.query_result[i].name }</p>`
                }
                $('#authorList,#authorListModal').append(temp);
            }
        },
        error: function (err)
        {
            console.error(err);
            if (err.status >= 500)
            {
                $('#errorModal').modal('show');
                $('#error_message').text('Server encountered error!');
            } else
            {
                $('#errorModal').modal('show');
                $('#error_message').text(err.responseJSON.error);
            }
        }
    })
}

function chooseAuthor(e)
{
    $('p[name="author"]').removeClass('itemChoose');
    $(`p[name="author"]:contains("${ e.target.innerText }")`).addClass('itemChoose');
    authorSelected = e.target.innerText;
}

function fetchPublisherList()
{
    const search = displayPanel ? encodeData($('#publisherSearch').val()) : encodeData($('#publisherSearchModal').val());

    if (displayPanel)
        $('#publisherSearchModal').val($('#publisherSearch').val());
    else
        $('#publisherSearch').val($('#publisherSearchModal').val());

    $.ajax({
        url: '/ajax_service/customer/book/get_publisher.php',
        method: 'GET',
        data: { search: search },
        dataType: 'json',
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            if (data.query_result)
            {
                let temp = ``;
                $('#publisherList,#publisherListModal').empty();
                for (let i = 0; i < data.query_result.length; i++)
                {
                    temp += `<p onclick="choosePublisher(event)" name='publisher' class='pointer ${ publisherSelected === data.query_result[i].name ? 'itemChoose' : '' }'>${ data.query_result[i].name }</p>`
                }
                $('#publisherList,#publisherListModal').append(temp);
            }
        },
        error: function (err)
        {
            console.error(err);
            if (err.status >= 500)
            {
                $('#errorModal').modal('show');
                $('#error_message').text('Server encountered error!');
            } else
            {
                $('#errorModal').modal('show');
                $('#error_message').text(err.responseJSON.error);
            }
        }
    })

}

function choosePublisher(e)
{
    $('p[name="publisher"]').removeClass('itemChoose');
    $(`p[name="publisher"]:contains("${ e.target.innerText }")`).addClass('itemChoose');
    publisherSelected = e.target.innerText;
}

function fetchBook()
{
}