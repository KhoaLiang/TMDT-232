<?php
require_once __DIR__ . '/../../../tool/php/role_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else if ($return_status_code === 200) {
      require_once __DIR__ . '/../../../config/db_connection.php';
      require_once __DIR__ . '/../../../tool/php/converter.php';
      require_once __DIR__ . '/../../../tool/php/formatter.php';
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">
            <link rel="stylesheet" href="/css/customer/book/book-list.css">
            <meta name="page creator" content="Anh Khoa, Nghia Duong">
            <meta name="description" content="Browse book list of NQK bookstore">
            <title>Browse Book</title>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
                  <div class="container-xl my-3">
                        <div class='row'>
                              <div class='d-none d-xl-block col-xl-3 border border-2 me-4 bg-white p-3'>
                                    <div>
                                          <h4>Category</h4>
                                          <input onchange='fetchCategoryList()' id='categorySearch' class="form-control" type="search" placeholder="Search" aria-label="Search by categories">
                                          <div class='ps-2 mt-3' id='categoryList'>
                                          </div>
                                    </div>
                                    <div class='mt-4'>
                                          <h4>Author</h4>
                                          <input onchange='fetchAuthorList()' id='authorSearch' class="form-control" type="search" placeholder="Search" aria-label="Search by authors">
                                          <div class='ps-2 mt-3' id='authorList'>
                                          </div>
                                    </div>
                                    <div class='mt-4'>
                                          <h4>Publisher</h4>
                                          <input onchange='fetchPublisherList()' id='publisherSearch' class="form-control" type="search" placeholder="Search" aria-label="Search by publishers">
                                          <div class='ps-2 mt-3' id='publisherList'>
                                          </div>
                                    </div>
                              </div>
                              <div class='col border border-2 bg-white'>
                                    <form class="d-flex align-items-center w-100 search_form mt-3" role="search" id="search_form">
                                          <button title='submit search form' class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                                <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                      <g id="SVGRepo_iconCarrier">
                                                            <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                      </g>
                                                </svg>
                                          </button>

                                          <input id="search_book" class="form-control" type="search" placeholder="Search book by name or ISBN number" aria-label="Search for books">
                                    </form>
                                    <hr>
                                    <div id='bookList'></div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <div>
                                                <div>
                                                      <h4>Category</h4>
                                                      <input onchange='fetchCategoryList()' id='categorySearchModal' class="form-control" type="search" placeholder="Search" aria-label="Search by categories">
                                                      <div class='ps-2 mt-3' id='categoryListModal'>
                                                      </div>
                                                </div>
                                                <div class='mt-4'>
                                                      <h4>Author</h4>
                                                      <input onchange='fetchAuthorList()' id='authorSearchModal' class="form-control" type="search" placeholder="Search" aria-label="Search by authors">
                                                      <div class='ps-2 mt-3' id='authorListModal'>
                                                      </div>
                                                </div>
                                                <div class='mt-4'>
                                                      <h4>Publisher</h4>
                                                      <input onchange='fetchPublisherList()' id='publisherSearchModal' class="form-control" type="search" placeholder="Search" aria-label="Search by publishers">
                                                      <div class='ps-2 mt-3' id='publisherListModal'>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class=" modal fade" id="errorModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Error</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p id="error_message"></p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
            <script src="/tool/js/ratingStars.js"></script>
            <script src="/javascript/customer/book/book-list.js"></script>
            <script src="/tool/js/encoder.js"></script>
      </body>

      </html>

<?php } ?>